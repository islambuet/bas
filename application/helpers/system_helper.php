<?php
class System_helper
{
    public static function display_date($time)
    {
        if(is_numeric($time))
        {
            return date('d-M-Y',$time);
        }
        else
        {
            return '';
        }
    }
    public static function display_date_time($time)
    {
        if(is_numeric($time))
        {
            return date('d-M-Y h:i:s A',$time);
        }
        else
        {
            return '';
        }
    }
    public static function get_time($str)
    {
        $time=strtotime($str);
        if($time===false)
        {
            return 0;
        }
        else
        {
            return $time;
        }
    }
    public static function upload_file($save_dir='images',$allowed_types='gif|jpg|png')
    {
        $CI= & get_instance();
        $CI->load->library('upload');
        $config=array();
        $config['upload_path']=FCPATH.$save_dir;
        $config['allowed_types']=$allowed_types;
        $config['max_size']=$CI->config->item('max_file_size');
        $config['overwrite']=false;
        $config['remove_spaces']=true;

        $uploaded_files=array();
        foreach ($_FILES as $key=>$value)
        {
            if(strlen($value['name'])>0)
            {
                $CI->upload->initialize($config);
                if($CI->upload->do_upload($key))
                {
                    $uploaded_files[$key]=array('status'=>true,'info'=>$CI->upload->data());
                }
                else
                {
                    $uploaded_files[$key]=array('status'=>false,'message'=>$value['name'].': '.$CI->upload->display_errors());
                }
            }
        }
        return $uploaded_files;
    }
    public static function invalid_try($action='',$action_id='',$other_info='')
    {
        $CI =& get_instance();
        $user = User_helper::get_user();
        $time=time();
        $data=array();
        $data['user_id']=$user->user_id;
        $data['controller']=$CI->router->class;
        $data['action']=$action;
        $data['action_id']=$action_id;
        $data['other_info']=$other_info;
        $data['date_created']=$time;
        $data['date_created_string']=System_helper::display_date($time);
        $CI->db->insert($CI->config->item('table_system_history_hack'), $data);
    }
    public static function get_users_info($user_ids=array())
    {
        $CI=& get_instance();
        $CI->db->from($CI->config->item('system_db_login').'.'.$CI->config->item('table_login_setup_user_info'));
        if(sizeof($user_ids)>0)
        {
            $CI->db->where_in('user_id',$user_ids);
        }
        $CI->db->where('revision',1);
        $results=$CI->db->get()->result_array();
        $users=array();
        foreach($results as $result)
        {
            $users[$result['user_id']]=$result;
        }
        return $users;

    }
    public static function get_designations($ids=array())
    {
        $CI=& get_instance();
        $CI->db->from($CI->config->item('system_db_login').'.'.$CI->config->item('table_login_setup_designation'));

        if(sizeof($ids)>0)
        {
            $CI->db->where_in('id',$ids);
        }
        $CI->db->where('ui.revision',1);
        $CI->db->order_by('ordering');
        $results=$CI->db->get()->result_array();
        return $results;
    }
    public static function get_companies($ids=array())
    {
        $CI=& get_instance();
        $CI->db->from($CI->config->item('system_db_login').'.'.$CI->config->item('table_login_setup_company'));
        if(sizeof($ids)>0)
        {
            $CI->db->where_in('id',$ids);
        }
        $CI->db->order_by('ordering');
        $results=$CI->db->get()->result_array();
        return $results;
    }
    public static function get_employee_info($user_ids=array(),$status='')
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('system_db_login').'.'.$CI->config->item('table_login_setup_user_info').' ui');
        //$CI->db->from($CI->config->item('table_setup_user_info').' ui');
        $CI->db->select('ui.user_id value');
        $CI->db->select('CONCAT(u.employee_id,"-",ui.name) text',false);
        $CI->db->join($CI->config->item('system_db_login').'.'.$CI->config->item('table_login_setup_user').' u','u.id = ui.user_id','INNER');
        if(sizeof($user_ids)>0)
        {
            $CI->db->where_in('user_id',$user_ids);
        }
        if(strlen($status)>0)
        {
            $CI->db->where('u.status',$status);
        }
        $CI->db->where('revision',1);
        $CI->db->where('user_type_id',$CI->config->item('system_user_type_employee_id'));

        $CI->db->order_by('u.status');
        $CI->db->order_by('ui.ordering');
        $results=$CI->db->get()->result_array();

        return $results;

    }

}
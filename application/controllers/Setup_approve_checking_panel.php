<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_approve_checking_panel extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_approve_checking_panel');
        $this->controller_url='setup_approve_checking_panel';
        //$this->load->model("setup_users_other_sites_model");

    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="get_items")
        {
            $this->get_items();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="IOU Checking Panel Setup";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }

    }
    private function get_items()
    {
        $user = User_helper::get_user();
        $results=Query_helper::get_info($this->config->item('table_setup_approve_checking_limit'),array('user_id','limit_checking'),array('status ="'.$this->config->item('system_status_active').'"'));
        $user_limits=array();
        foreach($results as $result)
        {
            $user_limits[$result['user_id']]=$result['limit_checking'];
        }
        $db_login=$this->load->database('armalik_login',TRUE);

        $db_login->from($this->config->item('table_setup_user').' user');
        $db_login->select('user.id,user.employee_id,user.user_name,user.status');
        $db_login->select('user_info.name,user_info.ordering');
        $db_login->select('designation.name designation_name');
        //$db_login->select('ug.name group_name');
        $db_login->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $db_login->join($this->config->item('table_setup_users_other_sites').' uos','uos.user_id = user.id','INNER');
        $db_login->join($this->config->item('table_system_other_sites').' os','os.id = uos.site_id','INNER');

        $db_login->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');

        $db_login->where('user_info.revision',1);
        $db_login->where('uos.revision',1);
        $db_login->where('os.short_name',$this->config->item('system_site_short_name'));
        $db_login->order_by('user_info.ordering','ASC');
        if($user->user_group!=1)
        {
            $db_login->where('user_info.user_group !=',1);
        }
        $items=$db_login->get()->result_array();

        foreach($items as &$item)
        {
            if(isset($user_limits[$item['id']]))
            {
                $item['limit_checking']=$user_limits[$item['id']];
            }
            else
            {
                $item['limit_checking']='';
            }

        }
        $this->jsonReturn($items);

    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['id']=$user_id;
            $result=Query_helper::get_info($this->config->item('table_setup_approve_checking_limit'),array('limit_checking'),array('user_id ='.$user_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            if($result)
            {
                $data['limit_checking']=$result['limit_checking'];
            }
            else
            {
                $data['limit_checking']='';
            }
            $db_login=$this->load->database('armalik_login',TRUE);

            $db_login->from($this->config->item('table_setup_user_info'));
            $db_login->select('name,user_id');
            $db_login->where('revision',1);
            $db_login->where('user_id',$user_id);

            $user_info=$db_login->get()->row_array();

            $data['title']='Edit Limit ('.$user_info['name'].')';
            $data['employees']=System_helper::get_employee_info();
            $data['assigned_employees']=array();
            $results=Query_helper::get_info($this->config->item('table_setup_approve_checking_panel'),array('employee_id'),array('user_id ='.$user_id,'status ="'.$this->config->item('system_status_active').'"'));
            foreach($results as $result)
            {
                $data['assigned_employees'][]=$result['employee_id'];
            }

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$user_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if(($this->input->post('id')))
            {
                $user_id=$this->input->post('id');
            }
            else
            {
                $user_id=$id;
            }
            $data['id']=$user_id;
            $result=Query_helper::get_info($this->config->item('table_setup_approve_checking_limit'),array('limit_checking'),array('user_id ='.$user_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            if($result)
            {
                $data['limit_checking']=$result['limit_checking'];
            }
            else
            {
                $data['limit_checking']='';
            }
            $db_login=$this->load->database('armalik_login',TRUE);

            $db_login->from($this->config->item('table_setup_user_info'));
            $db_login->select('name,user_id');
            $db_login->where('revision',1);
            $db_login->where('user_id',$user_id);

            $user_info=$db_login->get()->row_array();

            $data['title']='Details of Limit ('.$user_info['name'].')';
            $data['employees']=System_helper::get_employee_info();
            $data['assigned_employees']=array();
            $results=Query_helper::get_info($this->config->item('table_setup_approve_checking_panel'),array('employee_id'),array('user_id ='.$user_id,'status ="'.$this->config->item('system_status_active').'"'));
            foreach($results as $result)
            {
                $data['assigned_employees'][]=$result['employee_id'];
            }

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$user_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            $limit_checking=Query_helper::get_info($this->config->item('table_setup_approve_checking_limit'),array('id'),array('user_id ='.$id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data=array();
            $data['user_id']=$id;
            $data['limit_checking']=$this->input->post('limit_checking');
            $data['status']=$this->config->item('system_status_active');
            if($limit_checking)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_setup_approve_checking_limit'),$data,array("id = ".$limit_checking['id']));
            }
            else
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_setup_approve_checking_limit'),$data);
            }
            $assigned_employees=array();
            $results=Query_helper::get_info($this->config->item('table_setup_approve_checking_panel'),array('id,employee_id'),array('user_id ='.$id));
            foreach($results as $result)
            {
                $assigned_employees[$result['employee_id']]=$result['id'];
            }
            $this->db->where('user_id',$id);
            $this->db->set('status',$this->config->item('system_status_delete'));
            $this->db->update($this->config->item('table_setup_approve_checking_panel'));

            $employees=$this->input->post('employees');
            if(is_array($employees))
            {
                foreach($employees as $employee_id)
                {
                    $data=array();
                    $data['user_id']=$id;
                    $data['employee_id']=$employee_id;
                    $data['status']=$this->config->item('system_status_active');
                    if(isset($assigned_employees[$employee_id]))
                    {
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        Query_helper::update($this->config->item('table_setup_approve_checking_panel'),$data,array("id = ".$assigned_employees[$employee_id]));
                    }
                    else
                    {
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] = $time;
                        Query_helper::add($this->config->item('table_setup_approve_checking_panel'),$data);
                    }

                }
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('limit_checking',$this->lang->line('LABEL_CHECKING_LIMIT'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }


}

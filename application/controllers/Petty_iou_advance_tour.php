<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Petty_iou_advance_tour extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Petty_iou_advance_tour');
        $this->controller_url='petty_iou_advance_tour';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="get_form")
        {
            $this->system_get_form();
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
            $data['title']="IOU Tour Advance";
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
    private function system_get_items()
    {
        $results=System_helper::get_companies();
        $companies=array();
        foreach($results as $result)
        {
            $companies[$result['id']]=$result;
        }
        $employees=System_helper::get_users_info();

        $user = User_helper::get_user();
        $this->db->from($this->config->item('table_petty_cash_expense'));
        if(!(in_array($user->user_group,array(1,2))))
        {
            $this->db->where('user_created',$user->user_id);
        }
        $this->db->order_by('id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['amount']=number_format($item['amount_advance'],2);
            $item['company_name']=$companies[$item['company_id']]['full_name'];
            $item['advance_for']=$employees[$item['employee_id']]['name'];
            $item['created_by']=$employees[$item['user_created']]['name'];
            $item['created_date']=System_helper::display_date($item['date_created']);
        }
        $this->jsonReturn($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {

            $data['title']="Create New Tour Advance";
            $data["item"] = Array(
                'id' => 0,
                'num_days' => '',
                'company_id' => '',
                'employee_id' => '',
                'remarks_advance' => ''
            );
            $data['companies']=System_helper::get_companies();
            $data['employees']=array();
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $data['item']=Query_helper::get_info($this->config->item('table_petty_cash_expense'),'*',array('id ='.$item_id),1);
            if($data['item']['status_checkin_advance']!=$this->config->item('system_status_pending'))
            {
                $ajax['status']=false;
                $ajax['system_message']="You Cannot Edit now.";
                $this->jsonReturn($ajax);
                die();
            }
            $data['companies']=System_helper::get_companies();

            $db_login=$this->load->database('armalik_login',TRUE);
            $db_login->from($this->config->item('table_setup_users_company').' uc');
            $db_login->select('ui.user_id value');
            $db_login->select('ui.name text');
            $db_login->join($this->config->item('table_setup_user_info').' ui','ui.user_id = uc.user_id','INNER');
            $db_login->where('uc.company_id',$data['item']['company_id']);
            $db_login->where('uc.revision',1);
            $db_login->where('ui.revision',1);
            $data['employees']=$db_login->get()->result_array();

            $data['details']['tour_allowance_items']=Query_helper::get_info($this->config->item('table_setup_tour_daily_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
            $data['details']['daily_total']=0;


            $results=Query_helper::get_info($this->config->item('table_petty_cash_expense_details'),'*',array('petty_id ='.$item_id,'status ="'.$this->config->item('system_status_active').'"'));
            $details_item=array();

            foreach($results as $result)
            {
                if($result['purpose_id']>0)
                {
                    $details_item['DAILY'][$result['purpose_id']]=$result['amount_advance'];
                }
                else
                {
                    $details_item['FIXED'][$result['purpose_name']]=$result['amount_advance'];
                }
            }
            $data['details']['DAILY']=array();
            $data['details']['daily_total']=0;
            foreach($data['details']['tour_allowance_items'] as $row)
            {
                if(isset($details_item['DAILY'][$row['value']]))
                {
                    $data['details']['DAILY'][$row['value']]=$details_item['DAILY'][$row['value']];
                    $data['details']['daily_total']+=$details_item['DAILY'][$row['value']];
                }
            }

            $data['details']['FIXED']['HOTEL']='';
            if(isset($details_item['FIXED']['HOTEL']))
            {
                $data['details']['FIXED']['HOTEL']=$details_item['FIXED']['HOTEL'];
            }
            $data['details']['FIXED']['TRANSPORT']='';
            if(isset($details_item['FIXED']['TRANSPORT']))
            {
                $data['details']['FIXED']['TRANSPORT']=$details_item['FIXED']['TRANSPORT'];
            }
            $data['details']['FIXED']['OTHER']='';
            if(isset($details_item['FIXED']['OTHER']))
            {
                $data['details']['FIXED']['OTHER']=$details_item['FIXED']['OTHER'];
            }
            $data['details']['total']=number_format($data['item']['amount_advance'],2);

            $data['title']='Edit Tour Advance';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_get_form()
    {
        $employee_id=$this->input->post('employee_id');
        $employee_info=System_helper::get_users_info(array($employee_id));
        if(!$employee_info)
        {
            $ajax['status']=true;
            $ajax['system_message']="Employee setup failed";
            $this->jsonReturn($ajax);
        }
        if(!($employee_info[$employee_id]['designation']>0))
        {
            $ajax['status']=true;
            $ajax['system_message']="Designation for this employee cannot determine";
            $this->jsonReturn($ajax);
        }
        $num_days=$this->input->post('num_days');

        $data['tour_allowance_items']=Query_helper::get_info($this->config->item('table_setup_tour_daily_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
        $daily_allowance=Query_helper::get_info($this->config->item('table_setup_tour_designation_allowance'),'*',array('revision =1','designation_id ='.$employee_info[$employee_id]['designation']));

        $data['DAILY']=array();
        $data['daily_total']=0;

        foreach($daily_allowance as $row)
        {
            $data['DAILY'][$row['allowance_id']]=$row['amount']*$num_days;
            $data['daily_total']+=$data['DAILY'][$row['allowance_id']];
        }
        $data['FIXED']['HOTEL']='';
        $data['FIXED']['TRANSPORT']='';
        $data['FIXED']['OTHER']='';
        $data['total']=$data['daily_total'];
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/form",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);
    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['action1'])&&($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();

            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $data=$this->input->post('item');
            $num_days=$data['num_days'];
            $data['expense_type']=$this->config->item('system_petty_tour');
            $employee_info=System_helper::get_users_info(array($data['employee_id']));
            if(!$employee_info)
            {
                $ajax['status']=true;
                $ajax['system_message']="Employee setup failed";
                $this->jsonReturn($ajax);
                die();
            }
            if(!($employee_info[$data['employee_id']]['designation']>0))
            {
                $ajax['status']=true;
                $ajax['system_message']="Designation for this employee cannot determine";
                $this->jsonReturn($ajax);
                die();
            }
            $daily_allowance=Query_helper::get_info($this->config->item('table_setup_tour_designation_allowance'),'*',array('revision =1','designation_id ='.$employee_info[$data['employee_id']]['designation']));
            $daily_total=0;
            foreach($daily_allowance as $row)
            {
                $daily_total+=$row['amount']*$num_days;
            }
            $fixed_total=0;
            $tour=$this->input->post('tour');
            foreach($tour['FIXED'] as $row)
            {
                if($row>0)
                {
                    $fixed_total+=$row;
                }

            }
            $data['amount_advance']=$daily_total+$fixed_total;
            $data['amount_actual']=0;
            $data['amount_return']=0;
            $this->db->trans_start();  //DB Transaction Handle START
            $petty_id=$id;
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_petty_cash_expense'),$data,array("id = ".$id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $petty_id=Query_helper::add($this->config->item('table_petty_cash_expense'),$data);
            }
            $results=Query_helper::get_info($this->config->item('table_petty_cash_expense_details'),'*',array('petty_id ='.$petty_id));
            $details_item=array();
            foreach($results as $result)
            {
                if($result['purpose_id']>0)
                {
                    $details_item['DAILY'][$result['purpose_id']]=$result['id'];
                }
                else
                {
                    $details_item['FIXED'][$result['purpose_name']]=$result['id'];
                }
            }
            $this->db->where('petty_id',$petty_id);
            $this->db->set('status',$this->config->item('system_status_delete'));
            $this->db->update($this->config->item('table_petty_cash_expense_details'));
            foreach($daily_allowance as $row)
            {
                $data_daily=array();
                $data_daily['petty_id']=$petty_id;
                $data_daily['expense_type']=$this->config->item('system_petty_tour');
                $data_daily['purpose_name']='';
                $data_daily['purpose_id']=$row['allowance_id'];
                $data_daily['amount_advance']=$row['amount']*$num_days;
                $data_daily['amount_actual']=0;
                $data_daily['amount_return']=0;
                $data_daily['status']=$this->config->item('system_status_active');
                if(isset($details_item['DAILY'][$row['allowance_id']]))
                {
                    $data_daily['user_updated'] = $user->user_id;
                    $data_daily['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_petty_cash_expense_details'),$data_daily,array("id = ".$details_item['DAILY'][$row['allowance_id']]));
                }
                else
                {
                    $data_daily['user_created'] = $user->user_id;
                    $data_daily['date_created'] = $time;
                    Query_helper::add($this->config->item('table_petty_cash_expense_details'),$data_daily);
                }
            }
            foreach($tour['FIXED'] as $purpose_name=>$amount)
            {
                $data_fixed=array();
                $data_fixed['petty_id']=$petty_id;
                $data_fixed['expense_type']=$this->config->item('system_petty_tour');
                $data_fixed['purpose_name']=$purpose_name;
                $data_fixed['purpose_id']=0;
                if($amount>0)
                {
                    $data_fixed['amount_advance']=$amount;
                }
                else
                {
                    $data_fixed['amount_advance']=0;
                }
                $data_fixed['amount_actual']=0;
                $data_fixed['amount_return']=0;
                $data_fixed['status']=$this->config->item('system_status_active');
                if(isset($details_item['FIXED'][$purpose_name]))
                {
                    $data_fixed['user_updated'] = $user->user_id;
                    $data_fixed['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_petty_cash_expense_details'),$data_fixed,array("id = ".$details_item['FIXED'][$purpose_name]));
                }
                else
                {
                    $data_fixed['user_created'] = $user->user_id;
                    $data_fixed['date_created'] = $time;
                    Query_helper::add($this->config->item('table_petty_cash_expense_details'),$data_fixed);
                }


            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
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
        $tour=$this->input->post('tour');
        if(!$tour)
        {
            $this->message="Unfinished Input";
            return false;
        }
        $item_id = $this->input->post("id");
        if($item_id>0)
        {
            $info=Query_helper::get_info($this->config->item('table_petty_cash_expense'),'*',array('id ='.$item_id),1);
            if($info['status_checkin_advance']!=$this->config->item('system_status_pending'))
            {
                $this->message="You Cannot Edit now.";
                return false;
            }
        }
        return true;
    }


}

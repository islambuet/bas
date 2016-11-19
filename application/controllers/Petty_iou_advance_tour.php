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
        /*$user = User_helper::get_user();
        $this->db->from($this->config->item('table_petty_cash_expense'));
        if(!(in_array($user->user_group,array(1,2))))
        {
            $this->db->where('user_created',$user->user_id);
        }
        $items=$this->db->get()->result_array();*/
        $items=array();
        $this->jsonReturn($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {

            $data['title']="Create New Cash In Type";
            $data["item"] = Array(
                'id' => 0,
                'company_id' => '',
                'employee_id' => ''
            );
            $data['num_days']='';
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
                $crop_id=$this->input->post('id');
            }
            else
            {
                $crop_id=$id;
            }

            $data['crop']=Query_helper::get_info($this->config->item('table_setup_basic_cashin_types'),'*',array('id ='.$crop_id),1);
            $data['title']="Edit Cashin Type (".$data['crop']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$crop_id);
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

            $data=$this->input->post('crop');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_setup_basic_cashin_types'),$data,array("id = ".$id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_setup_basic_cashin_types'),$data);
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
        return true;
    }


}

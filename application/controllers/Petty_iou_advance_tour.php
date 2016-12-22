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
        $this->db->where('expense_type',$this->config->item('system_petty_iou_tour'));
        $this->db->order_by('id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['amount']=number_format($item['amount_advance'],2);
            $item['company_name']=$companies[$item['company_id']]['full_name'];
            $item['advance_for']=$employees[$item['employee_id']]['name'];
            $item['created_by']=$employees[$item['user_created']]['name'];
            $item['created_date']=System_helper::display_date($item['date_created']);
            $item['num_days']=round(($item['date_end']-$item['date_start'])/(24*3600));
            $item['date_start']=System_helper::display_date($item['date_start']);
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
                'title'=>'',
                'date_start'=>time(),
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
            if($data['item']['status_approval_advance']!=$this->config->item('system_status_pending'))
            {
                $ajax['status']=false;
                $ajax['system_message']="You Cannot Edit now.";
                $this->jsonReturn($ajax);
                die();
            }
            $data['item']['num_days']=round(($data['item']['date_end']-$data['item']['date_start'])/(24*3600));
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

            $data['details']['daily_items']=Query_helper::get_info($this->config->item('table_setup_tour_daily_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
            $data['details']['fixed_items']=Query_helper::get_info($this->config->item('table_setup_tour_fixed_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));


            $data['details']['daily']=array();
            $data['details']['fixed']=array();

            $results=Query_helper::get_info($this->config->item('table_petty_cash_iou_tour_details'),'*',array('petty_id ='.$item_id,'status ="'.$this->config->item('system_status_active').'"'));


            foreach($results as $result)
            {
                if($result['purpose_type']=='DAILY')
                {
                    $data['details']['daily'][$result['purpose_id']]=$result['amount_advance'];
                }
                elseif($result['purpose_type']=='FIXED')
                {
                    $data['details']['fixed'][$result['purpose_id']]=$result['amount_advance'];
                }
            }

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

        $data['daily_items']=Query_helper::get_info($this->config->item('table_setup_tour_daily_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
        $data['fixed_items']=Query_helper::get_info($this->config->item('table_setup_tour_fixed_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
        $daily_allowance=Query_helper::get_info($this->config->item('table_setup_tour_designation_allowance'),'*',array('revision =1','designation_id ='.$employee_info[$employee_id]['designation']));

        $data['daily']=array();
        foreach($daily_allowance as $row)
        {
            $data['daily'][$row['allowance_id']]=$row['amount']*$num_days;

        }
        $data['fixed']=array();
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
            $num_days=$this->input->post('num_days');
            $data['date_start']=System_helper::get_time($data['date_start']);
            $data['date_end']=$data['date_start']+24*3600*$num_days;
            $data['expense_type']=$this->config->item('system_petty_iou_tour');
            $data['amount_advance']=0;
            $data['amount_actual']=0;
            $data['amount_return']=0;
            $daily_amounts=$this->input->post('daily');
            $fixed_amounts=$this->input->post('fixed');
            foreach($daily_amounts as $row)
            {
                if($row>0)
                {
                    $data['amount_advance']+=$row;
                }
            }
            foreach($fixed_amounts as $row)
            {
                if($row>0)
                {
                    $data['amount_advance']+=$row;
                }
            }
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
            $results=Query_helper::get_info($this->config->item('table_petty_cash_iou_tour_details'),'*',array('petty_id ='.$petty_id));
            $details_item=array();
            foreach($results as $result)
            {
                $details_item[$result['purpose_type']][$result['purpose_id']]=$result['id'];
            }
            $this->db->where('petty_id',$petty_id);
            $this->db->set('status',$this->config->item('system_status_delete'));
            $this->db->update($this->config->item('table_petty_cash_iou_tour_details'));
            foreach($daily_amounts as $key=>$row)
            {
                $data_daily=array();
                $data_daily['petty_id']=$petty_id;
                $data_daily['purpose_type']='DAILY';
                $data_daily['purpose_id']=$key;
                if($row>0)
                {
                    $data_daily['amount_advance']=$row;
                }
                else
                {
                    $data_daily['amount_advance']=0;
                }
                $data_daily['amount_actual']=0;
                $data_daily['amount_return']=0;
                $data_daily['status']=$this->config->item('system_status_active');
                if(isset($details_item['DAILY'][$key]))
                {
                    $data_daily['user_updated'] = $user->user_id;
                    $data_daily['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_petty_cash_iou_tour_details'),$data_daily,array("id = ".$details_item['DAILY'][$key]));
                }
                else
                {
                    $data_daily['user_created'] = $user->user_id;
                    $data_daily['date_created'] = $time;
                    Query_helper::add($this->config->item('table_petty_cash_iou_tour_details'),$data_daily);
                }
            }
            foreach($fixed_amounts as $key=>$row)
            {
                $data_fixed=array();
                $data_fixed['petty_id']=$petty_id;
                $data_fixed['purpose_type']='FIXED';
                $data_fixed['purpose_id']=$key;
                if($row>0)
                {
                    $data_fixed['amount_advance']=$row;
                }
                else
                {
                    $data_fixed['amount_advance']=0;
                }
                $data_fixed['amount_actual']=0;
                $data_fixed['amount_return']=0;
                $data_fixed['status']=$this->config->item('system_status_active');
                if(isset($details_item['FIXED'][$key]))
                {
                    $data_fixed['user_updated'] = $user->user_id;
                    $data_fixed['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_petty_cash_iou_tour_details'),$data_fixed,array("id = ".$details_item['FIXED'][$key]));
                }
                else
                {
                    $data_fixed['user_created'] = $user->user_id;
                    $data_fixed['date_created'] = $time;
                    Query_helper::add($this->config->item('table_petty_cash_iou_tour_details'),$data_fixed);
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
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[company_id]',$this->lang->line('LABEL_COMPANY_NAME'),'required');
        $this->form_validation->set_rules('item[employee_id]',$this->lang->line('LABEL_EMPLOYEE_NAME'),'required');
        $this->form_validation->set_rules('item[title]',$this->lang->line('LABEL_TITLE'),'required');
        $this->form_validation->set_rules('item[date_start]',$this->lang->line('LABEL_DATE_START'),'required');
        $this->form_validation->set_rules('num_days',$this->lang->line('LABEL_NUM_DAYS'),'required');

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $daily=$this->input->post('daily');
        if(!$daily)
        {
            $this->message="Unfinished Input";
            return false;
        }
        $item_id = $this->input->post("id");
        if($item_id>0)
        {
            $info=Query_helper::get_info($this->config->item('table_petty_cash_expense'),'*',array('id ='.$item_id),1);
            if($info['status_approval_advance']!=$this->config->item('system_status_pending'))
            {
                $this->message="You Cannot Edit now.";
                return false;
            }
        }
        return true;
    }


}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Petty_iou_advance_daily_food extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Petty_iou_advance_daily_food');
        $this->controller_url='petty_iou_advance_daily_food';
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
            $data['title']="Daily Food Purchase Advance";
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
        $this->db->where('expense_type',$this->config->item('system_petty_iou_daily_food'));
        $this->db->order_by('id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['amount']=number_format($item['amount_advance'],2);
            $item['company_name']=$companies[$item['company_id']]['full_name'];
            $item['advance_for']=$employees[$item['employee_id']]['name'];
            $item['created_by']=$employees[$item['user_created']]['name'];
            $item['created_date']=System_helper::display_date($item['date_created']);
            $item['date_start']=System_helper::display_date($item['date_start']);
        }
        $this->jsonReturn($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {

            $data['title']="Create New Purchase Advance";
            $data["item"] = Array(
                'id' => 0,
                'title'=>'',
                'date_start'=>time(),
                'date_end' => '',
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

            $data['title']='Edit Purchase Advance';
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

        $num_person=$this->input->post('num_person');

        $data['daily_food_items']=Query_helper::get_info($this->config->item('table_setup_basic_daily_food_item'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
        /*$data['fixed_items']=Query_helper::get_info($this->config->item('table_setup_tour_fixed_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
        $daily_allowance=Query_helper::get_info($this->config->item('table_setup_tour_designation_allowance'),'*',array('revision =1','designation_id ='.$employee_info[$employee_id]['designation']));

        $data['daily_costs']=array();
        foreach($daily_allowance as $row)
        {
            $data['daily'][$row['allowance_id']]=$row['amount']*$num_days;

        }
        $data['fixed']=array();*/
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
            $data['expense_type']=$this->config->item('system_petty_purchase');
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
        $this->form_validation->set_rules('item[amount_advance]',$this->lang->line('LABEL_AMOUNT'),'required');


        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }


}

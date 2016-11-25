<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Petty_iou_advance_cash extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Petty_iou_advance_cash');
        $this->controller_url='petty_iou_advance_cash';
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
            $data['title']="IOU Cash Advance";
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
        $this->db->where('expense_type',$this->config->item('system_petty_iou_cash'));
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

            $data['title']="New Cash Requisition";
            $data["item"] = Array(
                'id' => 0,
                'title'=>'',
                'company_id' => '',
                'employee_id' => '',
                'amount_advance' => '',
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
            $data['companies']=System_helper::get_companies();

            $db_login=$this->load->database('armalik_login',TRUE);
            $db_login->from($this->config->item('table_setup_users_company').' uc');
            $db_login->select('ui.user_id value');
            $db_login->select('CONCAT(u.employee_id,"-",ui.name) text',false);
            $db_login->join($this->config->item('table_setup_user_info').' ui','ui.user_id = uc.user_id','INNER');
            $db_login->join($this->config->item('table_setup_user').' u','u.id = ui.user_id','INNER');
            $db_login->where('uc.company_id',$data['item']['company_id']);
            $db_login->where('uc.revision',1);
            $db_login->where('ui.revision',1);
            $data['employees']=$db_login->get()->result_array();

            $data['title']='Edit Cash Requisition';
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
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
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

            $data['companies']=System_helper::get_companies(array($data['item']['company_id']));

            $db_login=$this->load->database('armalik_login',TRUE);
            $db_login->from($this->config->item('table_setup_user_info').' ui');
            $db_login->select('ui.*');
            $db_login->select('u.employee_id');
            $db_login->select('dept.name department_name');
            $db_login->select('desig.name designation_name');
            $db_login->join($this->config->item('table_setup_user').' u','u.id = ui.user_id','INNER');
            $db_login->join($this->config->item('table_setup_designation').' desig','desig.id = ui.designation','LEFT');
            $db_login->join($this->config->item('table_setup_department').' dept','dept.id = ui.department_id','LEFT');
            $db_login->where('ui.user_id',$data['item']['employee_id']);
            $db_login->where('ui.revision',1);
            $data['employee']=$db_login->get()->row_array();
            $user_ids=array();
            $user_ids[]=$data['item']['user_created'];
            if($data['item']['user_checking_advance']>0)
            {
                $user_ids[]=$data['item']['user_checking_advance'];
            }
            if($data['item']['user_approval_advance']>0)
            {
                $user_ids[]=$data['item']['user_approval_advance'];
            }
            $data['users']=System_helper::get_users_info($user_ids);

            $data['title']='Details of Cash Requisition';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
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
            $data['date_start'] = $time;
            $data['date_end'] = $time;
            $data['expense_type']=$this->config->item('system_petty_iou_cash');
            $data['amount_actual']=0;
            $data['amount_return']=0;
            $data['status_checking_advance']=$this->config->item('system_status_pending');
            $data['date_checking_advance']=null;
            $data['user_checking_advance']=null;
            $data['remarks_checking_advance']=null;
            $this->db->trans_start();  //DB Transaction Handle START

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
                Query_helper::add($this->config->item('table_petty_cash_expense'),$data);
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
        $this->form_validation->set_rules('item[title]',$this->lang->line('LABEL_PURPOSE'),'required');
        $this->form_validation->set_rules('item[amount_advance]',$this->lang->line('LABEL_AMOUNT'),'required');


        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
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

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bank_cash_in extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Bank_cash_in');
        $this->controller_url='bank_cash_in';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
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
            $data['title']="Cash In To Bank";
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
        $this->db->from($this->config->item('table_bank_transaction').' bt');
        $this->db->select('bt.*');
        $this->db->select('ba.account_no');
        $this->db->select('bank_arm.name arm_bank_name');
        $this->db->select('pa.name way_name');
        $this->db->select('bank.name bank_name');

        $this->db->join($this->config->item('ems_basic_setup_arm_bank_accounts').' ba','ba.id = bt.account_id','INNER');
        $this->db->join($this->config->item('ems_basic_setup_arm_bank').' bank_arm','bank_arm.id = ba.bank_id','INNER');
        $this->db->join($this->config->item('ems_basic_setup_payment_ways').' pa','pa.id = bt.payment_way_id','LEFT');
        $this->db->join($this->config->item('ems_basic_setup_bank').' bank','bank.id = bt.bank_id','LEFT');

        $this->db->where('bt.reason',$this->config->item('system_transaction_cash_in'));
        $this->db->where('bt.status !=',$this->config->item('system_status_delete'));
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_transaction']=System_helper::display_date($item['date_transaction']);
        }
        $this->jsonReturn($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {

            $data['title']="New Cash In To Bank";
            $data["item"] = Array(
                'id' => 0,
                'date_transaction' => time(),
                'amount' => '',
                'account_id' => '',
                'cash_in_type_id' => '',
                'payment_way_id' => '',
                'transaction_number' => '',
                'bank_id' => '',
                'bank_branch' => '',
                'remarks' => ''
            );
            $data['accounts']=Query_helper::get_info($this->config->item('ems_basic_setup_arm_bank_accounts'),array('id value','account_no text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['cash_in_types']=Query_helper::get_info($this->config->item('table_setup_basic_cashin_types'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['payment_ways']=Query_helper::get_info($this->config->item('ems_basic_setup_payment_ways'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['banks']=Query_helper::get_info($this->config->item('ems_basic_setup_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            //account numbers
            //cash in types
            //payment ways
            //banks

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

            $data['item']=Query_helper::get_info($this->config->item('table_bank_transaction'),'*',array('id ='.$crop_id),1);
            $data['accounts']=Query_helper::get_info($this->config->item('ems_basic_setup_arm_bank_accounts'),array('id value','account_no text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['cash_in_types']=Query_helper::get_info($this->config->item('table_setup_basic_cashin_types'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['payment_ways']=Query_helper::get_info($this->config->item('ems_basic_setup_payment_ways'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['banks']=Query_helper::get_info($this->config->item('ems_basic_setup_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['title']='Edit Cash In';
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
            $data=$this->input->post('item');
            $data['date_transaction']=System_helper::get_time($data['date_transaction']);
            $data['in_out']=1;
            $data['reason']=$this->config->item('system_transaction_cash_in');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_bank_transaction'),$data,array("id = ".$id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_bank_transaction'),$data);
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
        $this->form_validation->set_rules('item[date_transaction]',$this->lang->line('LABEL_DATE'),'required');
        $this->form_validation->set_rules('item[account_id]',$this->lang->line('LABEL_ACCOUNT_NO'),'required');
        $this->form_validation->set_rules('item[amount]',$this->lang->line('LABEL_AMOUNT'),'required');


        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }


}

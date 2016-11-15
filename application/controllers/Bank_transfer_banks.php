<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bank_transfer_banks extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Bank_transfer_banks');
        $this->controller_url='bank_transfer_banks';
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
        $this->db->from($this->config->item('table_bank_transfer_history').' bth');
        $this->db->select('bth.*');
        $this->db->select('baf.account_no from_account_no');
        $this->db->select('bank_from.name from_bank_name');
        $this->db->select('bat.account_no to_account_no');
        $this->db->select('bank_to.name to_bank_name');
        $this->db->select('pa.name way_name');

        $this->db->join($this->config->item('ems_basic_setup_arm_bank_accounts').' baf','baf.id = bth.from_account_id','INNER');
        $this->db->join($this->config->item('ems_basic_setup_arm_bank').' bank_from','bank_from.id = baf.bank_id','INNER');
        $this->db->join($this->config->item('ems_basic_setup_arm_bank_accounts').' bat','bat.id = bth.to_account_id','INNER');
        $this->db->join($this->config->item('ems_basic_setup_arm_bank').' bank_to','bank_to.id = bat.bank_id','INNER');
        $this->db->join($this->config->item('ems_basic_setup_payment_ways').' pa','pa.id = bth.payment_way_id','LEFT');

        $this->db->where('bth.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('id','DESC');
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

            $data['title']="New Bank To Bank Transfer";
            $data["item"] = Array(
                'id' => 0,
                'date_transaction' => time(),
                'amount' => '',
                'from_account_id' => '',
                'to_account_id' => '',
                'payment_way_id' => '',
                'transaction_number' => '',
                'remarks' => ''
            );
            $data['accounts']=Query_helper::get_info($this->config->item('ems_basic_setup_arm_bank_accounts'),array('id value','account_no text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['payment_ways']=Query_helper::get_info($this->config->item('ems_basic_setup_payment_ways'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
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

            $data['item']=Query_helper::get_info($this->config->item('table_bank_transfer_history'),'*',array('id ='.$crop_id),1);
            $data['accounts']=Query_helper::get_info($this->config->item('ems_basic_setup_arm_bank_accounts'),array('id value','account_no text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['payment_ways']=Query_helper::get_info($this->config->item('ems_basic_setup_payment_ways'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['title']='Edit Bank To Bank Transfer';
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
            $data['date_transaction']=System_helper::get_time($data['date_transaction']);
            //$data['in_out']=1;
            //$data['reason']=$this->config->item('system_transaction_cash_in');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;

                Query_helper::update($this->config->item('table_bank_transfer_history'),$data,array("id = ".$id));

                $data_from=array();
                $data_from['date_transaction']=$data['date_transaction'];
                $data_from['in_out']=-1;
                $data_from['reason']=$this->config->item('system_transaction_bank_bank');
                $data_from['amount']=$data['amount'];
                $data_from['account_id']=$data['from_account_id'];
                $data_from['payment_way_id']=$data['payment_way_id'];
                $data_from['transaction_number']=$data['transaction_number'];
                $data_from['user_updated']=$data['user_updated'];
                $data_from['date_updated']=$data['date_updated'];
                $data_from['bank_to_bank_transfer_id']=$id;
                $data_from['remarks']=$data['remarks'];
                Query_helper::update($this->config->item('table_bank_transaction'),$data_from,array("bank_to_bank_transfer_id = ".$id,'in_out = -1'));
                $data_to=array();
                $data_to['date_transaction']=$data['date_transaction'];
                $data_to['in_out']=1;
                $data_to['reason']=$this->config->item('system_transaction_bank_bank');
                $data_to['amount']=$data['amount'];
                $data_to['account_id']=$data['to_account_id'];
                $data_to['payment_way_id']=$data['payment_way_id'];
                $data_to['transaction_number']=$data['transaction_number'];
                $data_to['user_updated']=$data['user_updated'];
                $data_to['date_updated']=$data['date_updated'];
                $data_to['bank_to_bank_transfer_id']=$id;
                $data_to['remarks']=$data['remarks'];
                Query_helper::update($this->config->item('table_bank_transaction'),$data_to,array("bank_to_bank_transfer_id = ".$id,'in_out = 1'));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $transfer_id=Query_helper::add($this->config->item('table_bank_transfer_history'),$data);
                $data_from=array();
                $data_from['date_transaction']=$data['date_transaction'];
                $data_from['in_out']=-1;
                $data_from['reason']=$this->config->item('system_transaction_bank_bank');
                $data_from['amount']=$data['amount'];
                $data_from['account_id']=$data['from_account_id'];
                $data_from['payment_way_id']=$data['payment_way_id'];
                $data_from['transaction_number']=$data['transaction_number'];
                $data_from['user_created']=$data['user_created'];
                $data_from['date_created']=$data['date_created'];
                $data_from['bank_to_bank_transfer_id']=$transfer_id;
                $data_from['remarks']=$data['remarks'];
                Query_helper::add($this->config->item('table_bank_transaction'),$data_from);
                $data_to=array();
                $data_to['date_transaction']=$data['date_transaction'];
                $data_to['in_out']=1;
                $data_to['reason']=$this->config->item('system_transaction_bank_bank');
                $data_to['amount']=$data['amount'];
                $data_to['account_id']=$data['to_account_id'];
                $data_to['payment_way_id']=$data['payment_way_id'];
                $data_to['transaction_number']=$data['transaction_number'];
                $data_to['user_created']=$data['user_created'];
                $data_to['date_created']=$data['date_created'];
                $data_to['bank_to_bank_transfer_id']=$transfer_id;
                $data_to['remarks']=$data['remarks'];
                Query_helper::add($this->config->item('table_bank_transaction'),$data_to);
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
        $this->form_validation->set_rules('item[from_account_id]','From '.$this->lang->line('LABEL_ACCOUNT_NO'),'required');
        $this->form_validation->set_rules('item[to_account_id]','To '.$this->lang->line('LABEL_ACCOUNT_NO'),'required');
        $this->form_validation->set_rules('item[amount]',$this->lang->line('LABEL_AMOUNT'),'required');


        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $data=$this->input->post('item');
        if($data['from_account_id']==$data['to_account_id'])
        {
            $this->message="From and To Account Cannot be same";
            return false;
        }
        return true;
    }


}

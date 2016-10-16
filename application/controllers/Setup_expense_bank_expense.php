<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_expense_bank_expense extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_expense_bank_expense');
        $this->controller_url='setup_expense_bank_expense';
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
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }

    public function system_list()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['title']="Bank Accounts with Expense";
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
        $this->db->from($this->config->item('ems_basic_setup_arm_bank_accounts_expense').' account');
        $this->db->select('account.id id,account.account_no account_no');
        $this->db->select('bank.name bank_name');
        $this->db->join($this->config->item('ems_basic_setup_arm_bank').' bank','bank.id = account.bank_id','INNER');
        $items=$this->db->get()->result_array();

        $this->db->from($this->config->item('table_setup_expense_bank').' exp_bank');
        $this->db->select('Count(exp_bank.id) num_expense',false);
        $this->db->select('exp_bank.account_id');
        $this->db->where('exp_bank.revision',1);
        $this->db->group_by('exp_bank.account_id');
        $results=$this->db->get()->result_array();
        $total_expenses=array();
        foreach($results as $result)
        {
            $total_expenses[$result['account_id']]['num_expense']=$result['num_expense'];
        }
        foreach($items as &$item)
        {
            if(isset($total_expenses[$item['id']]['num_expense']))
            {
                $item['num_expense']=$total_expenses[$item['id']]['num_expense'];
            }
            else
            {
                $item['num_expense']=0;
            }
        }
        $this->jsonReturn($items);

    }
    public function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if(($this->input->post('id')))
            {
                $account_id=$this->input->post('id');
            }
            else
            {
                $account_id=$id;
            }


            $data['expenses']=array();

            $this->db->from($this->config->item('table_setup_expense_expense').' e');
            $this->db->select('e.id,e.name,e.status,e.ordering');
            $this->db->select('ec.id category_id,ec.name category_name');
            $this->db->select('et.id type_id,et.name type_name');
            $this->db->join($this->config->item('table_setup_expense_type').' et','et.id = e.type_id','INNER');
            $this->db->join($this->config->item('table_setup_expense_category').' ec','ec.id = et.category_id','INNER');
            $this->db->where('e.status !=',$this->config->item('system_status_delete'));
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $data['expenses'][$result['category_id']]['name']=$result['category_name'];
                $data['expenses'][$result['category_id']]['id']=$result['category_id'];
                $data['expenses'][$result['category_id']]['types'][$result['type_id']]['name']=$result['type_name'];
                $data['expenses'][$result['category_id']]['types'][$result['type_id']]['id']=$result['type_id'];
                $data['expenses'][$result['category_id']]['types'][$result['type_id']]['expenses'][$result['id']]['name']=$result['name'];
                $data['expenses'][$result['category_id']]['types'][$result['type_id']]['expenses'][$result['id']]['id']=$result['id'];
            }
            $data['expenses_status']=array();
            $this->db->from($this->config->item('table_setup_expense_bank').' exp_bank');
            //$this->db->select('ugr.view,ugr.add,ugr.edit,ugr.delete,ugr.print,ugr.download,ugr.column_headers,ugr.task_id');
            //$this->db->select('ugr.sp1,ugr.sp2,ugr.sp3,ugr.sp4,ugr.sp5');
            $this->db->select('exp_bank.account_id,exp_bank.expense_id');
            $this->db->where('exp_bank.account_id',$account_id);
            $this->db->where('exp_bank.revision',1);
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $data['expenses_status'][]=$result['expense_id'];
            }


            $data['title']="Edit Account Expenses";
            $data['account_id']=$account_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$account_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }

    public function system_save()
    {
        $account_id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }

        $expenses=$this->input->post('expense');


        $time=time();
        $this->db->trans_start();  //DB Transaction Handle START

        $this->db->where('account_id',$account_id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_setup_expense_bank'));
        if(is_array($expenses))
        {
            foreach($expenses as $expense_id=>$expense)
            {

                $data=array();
                $data['expense_id']=$expense_id;
                $data['account_id']=$account_id;
                $data['user_created'] = $user->user_id;
                $data['date_created'] =$time;
                Query_helper::add($this->config->item('table_setup_expense_bank'),$data);
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

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_tour_designation_allowance extends Root_Controller
{

    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_tour_designation_allowance');
        $this->controller_url='setup_tour_designation_allowance';
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
            $data['title']="User Role";
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
        $items=System_helper::get_designations();
        $allowances=Query_helper::get_info($this->config->item('table_setup_tour_designation_allowance'),array('designation_id','amount'),array('revision =1'));
        $total_allowances=array();
        foreach($allowances as $allowance)
        {
            if(isset($total_allowances[$allowance['designation_id']]))
            {
                $total_allowances[$allowance['designation_id']]+=$allowance['amount'];
            }
            else
            {
                $total_allowances[$allowance['designation_id']]=$allowance['amount'];
            }
        }
        foreach($items as &$item)
        {
            if(isset($total_allowances[$item['id']]))
            {
                $item['total']=$total_allowances[$item['id']];
            }
            else
            {
                $item['total']=0;
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
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $data['allowance_items']=Query_helper::get_info($this->config->item('table_setup_tour_daily_item'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));
            $allowances=Query_helper::get_info($this->config->item('table_setup_tour_designation_allowance'),'*',array('revision =1','designation_id ='.$item_id));
            $data['allowance']=array();
            foreach($allowances as $allowance)
            {
                $data['allowance'][$allowance['allowance_id']]=$allowance['amount'];
            }
            $designation=System_helper::get_designations(array($item_id));
            $data['title']='Edit Allowance For '.$designation[0]['name'];
            $data['item_id']=$item_id;
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

    public function system_save()
    {
        $item_id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['action2'])&&($this->permissions['action2']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }

        $items=$this->input->post('items');

        $time=time();
        $this->db->trans_start();  //DB Transaction Handle START

        $this->db->where('designation_id',$item_id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_setup_tour_designation_allowance'));
        if(is_array($items))
        {
            foreach($items as $allowance_id=>$amount)
            {

                $data=array();
                $data['designation_id']=$item_id;
                $data['allowance_id']=$allowance_id;
                $data['amount']=$amount;
                $data['user_created'] = $user->user_id;
                $data['date_created'] =$time;
                $data['revision'] =1;
                Query_helper::add($this->config->item('table_setup_tour_designation_allowance'),$data);
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

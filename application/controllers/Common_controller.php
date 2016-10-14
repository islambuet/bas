<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message="";

    }

    //location setup
    public function get_dropdown_expenseTypes_by_categoryId()
    {
        $html_container_id='#type_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $category_id = $this->input->post('category_id');

        $data['items']=Query_helper::get_info($this->config->item('table_setup_expense_type'),array('id value','name text'),array('category_id ='.$category_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }

}

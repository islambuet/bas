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
    public function get_dropdown_employees_by_company_id()
    {
        $html_container_id='#employee_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $company_id = $this->input->post('company_id');
        $db_login=$this->load->database('armalik_login',TRUE);
        $db_login->from($this->config->item('table_setup_users_company').' uc');
        $db_login->select('ui.user_id value');
        $db_login->select('CONCAT(u.employee_id,"-",ui.name) text',false);
        $db_login->join($this->config->item('table_setup_user_info').' ui','ui.user_id = uc.user_id','INNER');
        $db_login->join($this->config->item('table_setup_user').' u','u.id = ui.user_id','INNER');
        $db_login->where('uc.company_id',$company_id);
        $db_login->where('uc.revision',1);
        $db_login->where('ui.revision',1);
        $data['items']=$db_login->get()->result_array();

        //$data['items']=Query_helper::get_info($this->config->item('table_setup_expense_type'),array('id value','name text'),array('category_id ='.$category_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }

}

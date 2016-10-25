<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
        $this->load->dbforge();
        $tables=$this->db->list_tables();
        foreach($tables as $table)
        {

            if ($this->db->field_exists('id', $table))
            {
                echo 'Y - ';
                $fields = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ),
                );
                if($this->dbforge->modify_column($table, $fields))
                {
                    echo 'Y ';
                }
                else
                {
                    echo 'N ';
                }
            }
            else
            {
                echo 'N -N ';
            }
            echo $table.'<br>';

        }
	}
}

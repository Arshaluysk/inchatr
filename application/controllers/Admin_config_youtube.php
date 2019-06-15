<?php

require_once("Home.php");

/**
* class admin_config_facebook
* @category controller
*/
class Admin_config_youtube extends Home
{

    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) 
        redirect('home/login', 'location');
        
        if($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location'); 

        $this->important_feature();
        $this->periodic_check();

    }

    /**
    * load index method. redirect to facebook_config
    * @access public
    * @return void
    */
    public function index()
    {
        $this->youtube_config();
    }

    /**
    * method to load facebook_config
    * @access public
    * @return void
    */
    public function youtube_config()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('youtube_config');
        $crud->set_subject($this->lang->line("youtube settings"));
        $crud->required_fields('google_client_id', 'google_secret','status');
        $crud->columns('google_client_id', 'google_secret','status');
        $crud->fields('google_client_id', 'google_secret','status');
        $crud->where('deleted','0');

        // Only one can be active at a time
        $crud->callback_after_insert(array($this, 'make_up_active_youtube_setting'));
        $crud->callback_after_update(array($this, 'make_up_active_youtube_setting'));

        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();


        $crud->display_as('google_client_id', $this->lang->line('Google Client ID'));
        $crud->display_as('google_secret', $this->lang->line('Google Client Secret'));
        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("youtube settings");
        $this->_viewcontroller($data);
    }

    /**
    * method to active facebook setting
    * @access public
    * @return boolean
    */

    public function make_up_active_youtube_setting($post_array, $primary_key)
    {
        if ($post_array['status']=='1') {
            $table="youtube_config";
            $where=array('id !='=> $primary_key);
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);
        }
        return true;
    }

 

    /**
    * method to load status_field_crud
    * @access public
    * @return from_dropdown dropdown
    * @param $value string
    * @param $row	array
    */
    public function status_field_crud($value, $row)
    {
        if ($value == '') {
            $value = 1;
        }
        return form_dropdown('status', array(0 => $this->lang->line('inactive'), 1 => $this->lang->line('active')), $value, 'class="form-control" id="field-status"');
    }

    /**
    * method to load status_display_crud
    * @access public
    * @return message string
    * @param $value integer
    * @param $row  array
    */
    public function status_display_crud($value, $row)
    {
        if ($value == 1) {
            return "<span class='label label-success'>".$this->lang->line('active')."</sapn>";
        } else {
            return "<span class='label label-warning'>".$this->lang->line('inactive')."</sapn>";
        }
    }
}

<?php

require_once("Home.php"); // including home controller

class Facebook_ex_message_button extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(77,$this->module_access))
        redirect('home/login_page', 'location'); 
        $this->user_id=$this->session->userdata('user_id');

        if($this->session->userdata("facebook_rx_fb_user_info")==0)
        redirect('facebook_rx_account_import/index','refresh');
    
        $this->load->library("fb_rx_login");
        $this->important_feature();
        $this->member_validity();        
    }


    public function index()
    {
      $this->message_generator();
    }


    public function message_generator()
    {
        $data['body'] = "facebook_ex/message_button/generate_message_button";
        $data['page_title'] = $this->lang->line("Send Message Button");
        $page_info = $this->db->query("SELECT username,page_name,page_id FROM `facebook_rx_fb_page_info` WHERE facebook_rx_fb_user_info_id = '".$this->session->userdata("facebook_rx_fb_user_info")."' AND deleted='0'")->result_array();
        $data['page_info'] = $page_info;
        $this->_viewcontroller($data);
    }




   

   

}
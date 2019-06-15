<?php

require_once("Home.php"); // including home controller

/**
* @category controller
* class Admin
*/

class Fb_chat_plugin_custom extends Home
{
	public $user_id;    

    /**
    * load constructor
    * @access public
    * @return void
    */

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        
        $this->load->helper('form');
        $this->load->library('upload');
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();
        if($this->session->userdata('user_type') == 'Admin')
            $this->periodic_check();

        $this->member_validity();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(28,$this->module_access))
        redirect('home/login_page', 'location'); 
      
    }
    public function index(){
        $this->fb_chat_domain_list();      
    }

    public function fb_chat_domain_list()
    {
        $data['body'] = 'facebook_chat_custom/domain_list';
        $data['page_title'] = $this->lang->line("FB chat plugin");
        $this->_viewcontroller($data);
    }
    
    public function fb_chat_domain_list_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
            // setting variables for pagination
        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name      = trim($this->input->post("domain_name", true));
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) {            
            $this->session->set_userdata('fb_chat_plugin_domain_name', $domain_name);
        }

        $search_domain_name = $this->session->userdata('fb_chat_plugin_domain_name');

        $where_simple=array();

        if ($search_domain_name) {
            $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        }
        
        $where_simple['user_id'] = $this->user_id;
        
        $where  = array('where'=>$where_simple);

        $order_by_str=$sort." ".$order;

        $offset = ($page-1)*$rows;
        $result = array();

        $table = "fb_chat_plugin";

        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $total_rows_array = $this->basic->count_row($table, $where, $count="id");      

        $total_result = $total_rows_array[0]['total_rows'];

        
        echo convert_to_grid_data($info, $total_result);
    }

    public function add_domain()
    {
        $data['body'] = 'facebook_chat_custom/add_domain';
        $data['page_title'] = $this->lang->line("Get chat plugin");
        $this->_viewcontroller($data);
    }

    public function add_domain_action()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        $domain_name = strtolower($this->input->post('domain_name', true));
        $message_header = $this->input->post('message_header', true);
        $fb_page_url = strtolower($this->input->post('fb_page_url', true));

        //************************************************//
        $status=$this->_check_usage($module_id=28,$request=1);
        if($status=="2") 
        {
            echo $this->lang->line("Module limit is over.");
            exit();
        }
        else if($status=="3") 
        {
            echo $this->lang->line("Module limit is over.");
            exit();
        }
        //************************************************//

        $this->db->trans_start(); 

        $random_num = $this->_random_number_generator();
        $js_code = '<div id="mostofa_chat_load"></div>
        <script data-name="'.$random_num.'" id="domain_fb_statnow" type="text/javascript" src="'.site_url().'js/my_chat_custom.js"></script>
        <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
            fjs.parentNode.insertBefore(js, fjs);
            }(document, "script", "facebook-jssdk"));
        </script>';
        $js_code=htmlspecialchars($js_code);
        

        $data = array(
            'user_id' => $this->user_id,
            'domain_name' => $domain_name,
            'message_header' => htmlspecialchars($message_header),
            'fb_page_url' => $fb_page_url,
            'domain_code' => $random_num,
            'js_code' => $js_code,
            'add_date' => date("Y-m-d")
            );        

        $this->basic->insert_data('fb_chat_plugin',$data);
        $last_id = $this->db->insert_id();

        $where_update = array('id' => $last_id);
        $update_code = $last_id.$random_num;
        $js_code = '<div id="mostofa_chat_load"></div>
        <script data-name="'.$update_code.'" id="domain_fb_statnow" type="text/javascript" src="'.site_url().'js/my_chat_custom.js"></script>
        <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
            fjs.parentNode.insertBefore(js, fjs);
            }(document, "script", "facebook-jssdk"));
        </script>';
        $js_code=htmlspecialchars($js_code);
        $data_update = array('domain_code'=>$update_code,'js_code'=>$js_code);
        $this->basic->update_data('fb_chat_plugin',$where_update,$data_update);

    

        $where_domain['where'] = array('id'=>$last_id);
        $domain_info = $this->basic->get_data('fb_chat_plugin',$where_domain,$select='');

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=28,$request=1);   
        //******************************//

        $this->db->trans_complete();

        if($this->db->trans_status() === false){
            echo 0;
        } else {
            echo htmlspecialchars_decode($domain_info[0]['js_code']);
        }
    }

    public function delete_domain($id=0)
    {
        $this->db->trans_start();
        $this->basic->delete_data('fb_chat_plugin',$where=array('id'=>$id));

        //******************************//
        // delete data to useges log table
        $this->_delete_usage_log($module_id=28,$request=1);   
        //******************************//

        $this->db->trans_complete();
        if($this->db->trans_status() === false) {
            $this->session->set_userdata('delete_error',1);
            redirect('fb_chat_plugin_custom/fb_chat_domain_list','Location');
        } else {
            $this->session->set_userdata('delete_success',1);
            redirect('fb_chat_plugin_custom/fb_chat_domain_list','Location');
        }
    }

}
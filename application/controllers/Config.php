<?php

require_once("Home.php"); // including home controller

/**
* class config
* @category controller
*/
class Config extends Home
{

    public $user_id;
    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login_page', 'location');
        }

        $this->user_id=$this->session->userdata('user_id');
        $this->important_feature();        
        $this->member_validity();
    }

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */
    public function index()
    {
        $this->configuration();
    }

    /**
    * load config form method
    * @access public
    * @return void
    */
    public function configuration()
    {
                
        $data['body'] = "config/edit_config";
        $data['config_data'] = $this->basic->get_data("config",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));       
        $data['page_title'] = $this->lang->line('connectivity settings');
        $this->_viewcontroller($data);
    }

    /**
    * method to edit config
    * @access public
    * @return void
    */
    public function edit_config()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST) 
        {
            // validation
            $this->form_validation->set_rules('google_safety_api',      '<b>Google API  Key</b>',               'trim');
            $this->form_validation->set_rules('moz_access_id',          '<b>MOZ Access ID</b>',                  'trim');
            $this->form_validation->set_rules('moz_secret_key',         '<b>MOZ Secret Key</b>',                 'trim');
            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->configuration();
            } 

            else 
            {
                // assign
                $google_safety_api=addslashes(strip_tags($this->input->post('google_safety_api', true)));
                $moz_access_id=addslashes(strip_tags($this->input->post('moz_access_id', true)));
                $moz_secret_key=addslashes(strip_tags($this->input->post('moz_secret_key', true)));

                $update_data=array("google_safety_api"=>$google_safety_api,"moz_access_id"=>$moz_access_id,"moz_secret_key"=>$moz_secret_key,"mobile_ready_api_key"=>$google_safety_api);
                $insert_data=array("google_safety_api"=>$google_safety_api,"moz_access_id"=>$moz_access_id,"moz_secret_key"=>$moz_secret_key,"mobile_ready_api_key"=>$google_safety_api,"user_id"=>$this->session->userdata("user_id"));

                if($this->basic->is_exist("config",$where=array("user_id"=>$this->session->userdata("user_id")),$select='id')) 
                $this->basic->update_data("config",array("user_id"=>$this->session->userdata("user_id")),$update_data);
                else $this->basic->insert_data("config",$insert_data);
                  
                $this->session->set_flashdata('success_message', 1);
                redirect('config/configuration', 'location');
            }
        }
    }




    public function proxy()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('config_proxy');
        $crud->order_by('id');
        $crud->where('user_id', $this->session->userdata("user_id"));
        $crud->where('deleted', '0');
        $crud->set_subject($this->lang->line("proxy settings"));
     

        if($this->session->userdata("user_type")=="Member")
        {
           $crud->fields('proxy','port','username','password');
           $crud->required_fields('proxy','port');
           $crud->columns('proxy','port','username','password');
        }
        else
        {
            $crud->fields('proxy','port','username','password','admin_permission');
            $crud->required_fields('proxy','port','admin_permission');
            $crud->columns('proxy','port','username','password','admin_permission');
            $crud->callback_column('admin_permission', array($this, 'admin_permission_display_crud'));
            $crud->callback_field('admin_permission', array($this, 'admin_permission_field_crud'));
        }


        $crud->display_as('proxy', $this->lang->line('proxy'));    
        $crud->display_as('port', $this->lang->line('proxy port'));    
        $crud->display_as('username', $this->lang->line('proxy username'));    
        $crud->display_as('password', $this->lang->line('proxy password'));    
        $crud->display_as('admin_permission', $this->lang->line('admin permission'));    

        $crud->callback_after_insert(array($this, 'insert_user_id'));   
      
        
        $crud->unset_read();
        $crud->unset_print();
        $crud->unset_export();

        $output = $crud->render();
        $data['output']=$output;
        $data['page_title'] = $this->lang->line("proxy settings");
        $data['crud']=1;

        $this->_viewcontroller($data);
    }


    public function insert_user_id($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $table = 'config_proxy';
        $data = array('user_id'=>$this->session->userdata("user_id"));
        $this->basic->update_data($table, $where, $data);
        return true;
    }

    public function admin_permission_field_crud($value, $row)
    {
        if ($value == '') 
        {
            $value = "everyone";
        }
        return form_dropdown('admin_permission', array('only me' => $this->lang->line('only me'), "everyone" => $this->lang->line('everyone')), $value, 'class="form-control" id="field-admin_permission"');
    }


    public function admin_permission_display_crud($value, $row)
    {
        if ($value == "only me") 
        {
            return "<span class='label label-success'>".$this->lang->line('only me')."</sapn>";
        } 
        else 
        {
            return "<span class='label label-warning'>".$this->lang->line('everyone')."</sapn>";
        }
    }


    public function adword_settings()
    {
        
        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login_page', 'location');
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('adword_credential_config');
        $crud->where('deleted', '0');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line("Adwords Credential Configuration"));
        $crud->required_fields('client_id', 'client_secret', 'developer_token');
        $crud->columns('client_id', 'client_secret', 'developer_token');
        $crud->fields('client_id', 'client_secret', 'developer_token');
        
        $crud->unset_export();
        $crud->unset_print();
        // $crud->unset_read();

        $crud->display_as('client_id', $this->lang->line('Client ID'));
        $crud->display_as('client_secret', $this->lang->line('Client Secret'));
        $crud->display_as('developer_token', $this->lang->line('Developer Token'));

        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("Adword Configuration");
        $this->_viewcontroller($data);
    }



    public function twitter_settings()
    {
        
        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login_page', 'location');
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('twitter_config');
        $crud->where('deleted', '0');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line("Twitter Account Configuration"));
        $crud->required_fields('consumer_key', 'consumer_secret');
        $crud->columns('consumer_key', 'consumer_secret');
        $crud->fields('consumer_key', 'consumer_secret');
        
        $crud->unset_export();
        $crud->unset_print();
        // $crud->unset_read();

        $crud->display_as('consumer_key', $this->lang->line('Consumer Key'));
        $crud->display_as('consumer_secret', $this->lang->line('Consumer Secret'));

        $crud->callback_after_insert(array($this, 'insert_user_id_twitter')); 

        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("Twitter Configuration");
        $this->_viewcontroller($data);
    }


    public function insert_user_id_twitter($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $table = 'twitter_config';
        $data = array('user_id'=>$this->session->userdata("user_id"));
        $this->basic->update_data($table, $where, $data);
        return true;
    }
    




    public function medium_config()

    {

        $this->load->database();

        $this->load->library('grocery_CRUD');

        $crud = new grocery_CRUD();

        $crud->set_theme('flexigrid');

        $crud->set_table('medium_config');

        $crud->where('deleted', '0');

        $crud->order_by('id');

        $crud->set_subject($this->lang->line("Medium Account Configuration"));

        $crud->required_fields('client_id', 'client_secret');

        $crud->columns('client_id', 'client_secret');

        $crud->fields('client_id', 'client_secret');

        

        $crud->unset_export();

        $crud->unset_print();

        // $crud->unset_read();



        $crud->display_as('client_id', $this->lang->line('Client ID'));

        $crud->display_as('client_secret', $this->lang->line('Client Secret'));



        $crud->callback_after_insert(array($this, 'insert_user_id_medium')); 



        $output = $crud->render();

        $data['output'] = $output;

        $data['crud'] = 1;

        $data['page_title'] = $this->lang->line("Medium Configuration");

        $this->_viewcontroller($data);

    }



    public function insert_user_id_medium($post_array, $primary_key)

    {

        $id = $primary_key;

        $where = array('id'=>$id);

        $table = 'medium_config';

        $data = array('user_id'=>$this->session->userdata("user_id"));

        $this->basic->update_data($table, $where, $data);

        return true;

    }





    public function tumblr_settings()

    {

        

        if ($this->session->userdata('user_type')!= 'Admin') {

            redirect('home/login_page', 'location');

        }



        $this->load->database();

        $this->load->library('grocery_CRUD');

        $crud = new grocery_CRUD();

        $crud->set_theme('flexigrid');

        $crud->set_table('tumblr_config');

        $crud->where('deleted', '0');

        $crud->order_by('id');

        $crud->set_subject($this->lang->line("Tumblr Account Configuration"));

        $crud->required_fields('consumer_key', 'consumer_secret');

        $crud->columns('consumer_key', 'consumer_secret');

        $crud->fields('consumer_key', 'consumer_secret');

        

        $crud->unset_export();

        $crud->unset_print();

        // $crud->unset_read();



        $crud->display_as('consumer_key', $this->lang->line('Consumer Key'));

        $crud->display_as('consumer_secret', $this->lang->line('Consumer Secret'));



        $crud->callback_after_insert(array($this, 'insert_user_id_tumblr')); 



        $output = $crud->render();

        $data['output'] = $output;

        $data['crud'] = 1;

        $data['page_title'] = $this->lang->line("Tumblr Configuration");

        $this->_viewcontroller($data);

    }





    public function insert_user_id_tumblr($post_array, $primary_key)

    {

        $id = $primary_key;

        $where = array('id'=>$id);

        $table = 'tumblr_config';

        $data = array('user_id'=>$this->session->userdata("user_id"));

        $this->basic->update_data($table, $where, $data);

        return true;

    }





    public function pinterest_settings()
    {
        $data['page_title'] = $this->lang->line("pinterest settings");
        $data['body'] = 'pinterest_settings/pinterest_setting';
        $this->_viewcontroller($data);
    }



    public function pinterest_config_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'ASC';

        $app_id = trim($this->input->post("app_id", true));
        $app_name = trim($this->input->post("app_name", true));
        $user_name = trim($this->input->post("user_name", true));
        $is_searched = $this->input->post('is_searched', true);

        if ($is_searched) 
        {
            $this->session->set_userdata('pinterest_config_app_id', $app_id);
            $this->session->set_userdata('pinterest_config_app_name', $app_name);
            $this->session->set_userdata('pinterest_config_user_name', $user_name);
        }

        // saving session data to different search parameter variables
        $search_app_id  = $this->session->userdata('pinterest_config_app_id');
        $search_app_name  = $this->session->userdata('pinterest_config_app_name');
        $search_user_name  = $this->session->userdata('pinterest_config_user_name');

        $where_simple=array();        
        if ($search_app_id) $where_simple['app_id like '] = "%".$search_app_id."%";
        if ($search_app_name) $where_simple['app_name like '] = "%".$search_app_name."%";
        if ($search_user_name) $where_simple['user_name like '] = "%".$search_user_name."%";

        // $where_simple['page_info_table_id'] = $table_id;
        $where_simple['user_id'] = $this->user_id;

        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "pinterest_config";
        // $select = array('id','auto_reply_campaign_name','post_created_at','last_updated_at');
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);

    }



    public function add_pinterest_settings()
    {
        $data['page_title'] = $this->lang->line("pinterest settings");
        $data['body'] = 'pinterest_settings/add_settings';
        $this->_viewcontroller($data);
    }



    public function add_pinterest_settings_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        if ($_POST) 
        {
            $this->form_validation->set_rules('app_id',  '<b>'.$this->lang->line("App ID").'</b>','trim|required');                
            $this->form_validation->set_rules('app_name',  '<b>'.$this->lang->line("App Name").'</b>','trim|required');                
            $this->form_validation->set_rules('app_secret',  '<b>'.$this->lang->line("App Secret").'</b>','trim|required');  
            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->add_pinterest_settings();
            } 
            else 
            {
                $app_id=addslashes(strip_tags($this->input->post('app_id', true)));
                $app_name=addslashes(strip_tags($this->input->post('app_name', true)));
                $app_secret=addslashes(strip_tags($this->input->post('app_secret', true)));

                $insert_data = array(
                    'app_id' => $app_id,
                    'app_name' => $app_name,
                    'app_secret' => $app_secret,
                    'user_id' => $this->user_id
                );

                if($this->basic->insert_data('pinterest_config',$insert_data))
                {
                    $this->session->set_flashdata('success_message', 1);
                    redirect('config/pinterest_settings', 'location');
                }
                
            }
        }

    }



    public function update_pinterest_config($table_id)
    {
        $app_info = $this->basic->get_data('pinterest_config',array('where'=>array('id'=>$table_id)));
        $data['info'] = $app_info;
        $data['page_title'] = $this->lang->line("pinterest settings");
        $data['body'] = 'pinterest_settings/edit_settings';
        $this->_viewcontroller($data);

    }



    public function update_pinterest_config_action($table_id)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        if ($_POST) 
        {
            $this->form_validation->set_rules('app_id',  '<b>'.$this->lang->line("App ID").'</b>','trim|required');                
            $this->form_validation->set_rules('app_name',  '<b>'.$this->lang->line("App Name").'</b>','trim|required');                
            $this->form_validation->set_rules('app_secret',  '<b>'.$this->lang->line("App Secret").'</b>','trim|required');  
            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->update_pinterest_config($table_id);
            } 
            else 
            {
                $app_id=addslashes(strip_tags($this->input->post('app_id', true)));
                $app_name=addslashes(strip_tags($this->input->post('app_name', true)));
                $app_secret=addslashes(strip_tags($this->input->post('app_secret', true)));

                $insert_data = array(
                    'app_id' => $app_id,
                    'app_name' => $app_name,
                    'app_secret' => $app_secret
                );

                if($this->basic->update_data('pinterest_config',array('id'=>$table_id),$insert_data))
                {
                    $this->session->set_flashdata('success_message', 1);
                    redirect('config/pinterest_settings', 'location');
                }
                
            }
        }

    }



    public function pinterest_login_button($table_id)
    {
        $this->session->unset_userdata('pinterest_config_table_id');
        $this->session->set_userdata('pinterest_config_table_id',$table_id);
        $this->load->library('Pinterests');
        $this->pinterests->app_initialize($table_id);

        $redirect_pin_url = base_url("account_import/pinterest_login_callback");
        $redirect_pin_url=str_replace('http://','https://',$redirect_pin_url);
        $data['pinterest_login_button'] = $this->pinterests->login_button($redirect_pin_url);
        $data['page_title'] = $this->lang->line("pinterest settings");
        $data['body'] = 'pinterest_settings/pinterest_login_button';
        $this->_viewcontroller($data);
    }



    public function delete_pinterest_config()
    {
        if(!$_POST) exit();
        $pinterest_config_table_id = $this->input->post('table_id');

        $this->basic->delete_data('pinterest_config',array('id'=>$pinterest_config_table_id));
        $this->basic->delete_data('rx_pinterest_info',array('pinterest_table_id'=>$pinterest_config_table_id));
        echo 'success';

    }



    public function insert_user_id_pinterest($post_array, $primary_key)

    {

        $id = $primary_key;

        $where = array('id'=>$id);

        $table = 'pinterest_config';

        $data = array('user_id'=>$this->session->userdata("user_id"));

        $this->basic->update_data($table, $where, $data);

        return true;

    }



    public function wp_org_config()

    {

        if ($this->session->userdata('user_type')!= 'Admin') {

            redirect('home/login_page', 'location');

        }



        $this->load->database();

        $this->load->library('grocery_CRUD');

        $crud = new grocery_CRUD();

        $crud->set_theme('flexigrid');

        $crud->set_table('rx_wp_org_config');

        $crud->where('deleted', '0');

        $crud->order_by('id');

        $crud->set_subject($this->lang->line("WP.ORG Account Configuration"));

        $crud->required_fields('client_id', 'client_secret');

        $crud->columns('client_id', 'client_secret');

        $crud->fields('client_id', 'client_secret');

        

        $crud->unset_export();

        $crud->unset_print();

        // $crud->unset_read();



        $crud->display_as('client_id', $this->lang->line('Client ID'));

        $crud->display_as('client_secret', $this->lang->line('Client Secret'));



        $crud->callback_after_insert(array($this, 'insert_user_id_wporg')); 



        $output = $crud->render();

        $data['output'] = $output;

        $data['crud'] = 1;

        $data['page_title'] = $this->lang->line("WP.ORG Configuration");

        $this->_viewcontroller($data);

    }



    public function insert_user_id_wporg($post_array, $primary_key)

    {

        $id = $primary_key;

        $where = array('id'=>$id);

        $table = 'rx_wp_org_config';

        $data = array('user_id'=>$this->session->userdata("user_id"));

        $this->basic->update_data($table, $where, $data);

        return true;

    }


    
    
    public function vk_settings()
    {
        
        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login_page', 'location');
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('vk_config');
        $crud->where('deleted', '0');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line("VK Account Configuration"));
        $crud->required_fields('app_id', 'app_secret');
        $crud->columns('title','app_id', 'app_secret');
        $crud->fields('title','app_id', 'app_secret');
        
        $crud->unset_export();
        $crud->unset_print();
        // $crud->unset_read();

        $crud->display_as('consumer_key', $this->lang->line('Consumer Key'));
        $crud->display_as('consumer_secret', $this->lang->line('Consumer Secret'));
        $crud->display_as('title', $this->lang->line('Title'));

        $crud->callback_after_insert(array($this, 'insert_user_id_vk')); 

        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("VK Configuration");
        $this->_viewcontroller($data);
    }


    public function insert_user_id_vk($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $table = 'vk_config';
        $data = array('user_id'=>$this->session->userdata("user_id"));
        $this->basic->update_data($table, $where, $data);
        return true;
    }
    
    
    public function reddit_config()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('reddit_config');
        $crud->where('deleted', '0');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line("Reddit Account Configuration"));
        $crud->required_fields('client_id', 'client_secret');
        $crud->columns('client_id', 'client_secret');
        $crud->fields('client_id', 'client_secret');
        
        $crud->unset_export();
        $crud->unset_print();
        // $crud->unset_read();

        $crud->display_as('client_id', $this->lang->line('Client ID'));
        $crud->display_as('client_secret', $this->lang->line('Client Secret'));

        $crud->callback_after_insert(array($this, 'insert_user_id_reddit')); 

        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("Reddit Configuration");
        $this->_viewcontroller($data);
    }


    public function insert_user_id_reddit($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $table = 'reddit_config';
        $data = array('user_id'=>$this->session->userdata("user_id"));
        $this->basic->update_data($table, $where, $data);
        return true;
    }



    public function linkedin_settings()
    {
        
        if ($this->session->userdata('user_type')!= 'Admin') {
            redirect('home/login_page', 'location');
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('linkedin_config');
        $crud->where('deleted', '0');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line("Linkedin Account Configuration"));
        $crud->required_fields('client_id', 'client_secret');
        $crud->columns('client_id', 'client_secret');
        $crud->fields('client_id', 'client_secret');
        
        $crud->unset_export();
        $crud->unset_print();
        // $crud->unset_read();

        $crud->display_as('client_id', $this->lang->line('Client ID'));
        $crud->display_as('client_secret', $this->lang->line('Client Secret'));

        $crud->callback_after_insert(array($this, 'insert_user_id_linkedin')); 

        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("Linkedin Configuration");
        $this->_viewcontroller($data);
    }


    public function insert_user_id_linkedin($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $table = 'linkedin_config';
        $data = array('user_id'=>$this->session->userdata("user_id"));
        $this->basic->update_data($table, $where, $data);
        return true;
    }



    // ///////////*************************member facebook config***************************///////////////
    /**
    * method to load facebook_config
    * @access public
    * @return void
    */
    public function facebook_config()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('facebook_config');
        $crud->order_by('app_name');
        $crud->set_subject($this->lang->line("facebook settings"));
        $crud->required_fields('api_id', 'api_secret','status');
        $crud->columns('app_name','api_id', 'api_secret','status');
        $crud->fields('app_name','api_id', 'api_secret','status');
        $crud->where('deleted','0');
        $crud->where('user_id',$this->session->userdata('user_id'));

        // Only one can be active at a time
        $crud->callback_after_insert(array($this, 'make_up_active_fb_setting'));
        $crud->callback_after_update(array($this, 'make_up_active_fb_setting_edit'));

        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();

        $crud->display_as('app_name', $this->lang->line('facebook app Name'));
        $crud->display_as('api_id', $this->lang->line('facebook API ID'));
        $crud->display_as('api_secret', $this->lang->line('facebook API secret'));
        $crud->display_as('status', $this->lang->line('status'));

        $output = $crud->render();
        $data['output'] = $output;
        $data['crud'] = 1;
        $data['page_title'] = $this->lang->line("facebook settings");
        $this->_viewcontroller($data);
    }

    /**
    * method to active facebook setting
    * @access public
    * @return boolean
    */

    public function make_up_active_fb_setting($post_array, $primary_key)
    {
        if ($post_array['status']=='1') {
            $table="facebook_config";
            $where=array('id !='=> $primary_key,"user_id"=>$this->session->userdata('user_id'));
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);
            $this->db->last_query();
        }

        $this->basic->update_data("facebook_config",array('id'=> $primary_key),array("user_id"=>$this->session->userdata("user_id")));

        return true;
    }

    /**
    * method to active facebook setting edit
    * @access public
    * @return boolean
    */

    public function make_up_active_fb_setting_edit($post_array, $primary_key)
    {
        if ($post_array['status']=='1') {
            $table="facebook_config";
            $where=array('id !='=> $primary_key,"user_id"=>$this->session->userdata('user_id'));
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);
            $this->db->last_query();
        }
        return true;
    }


    /**
    * method to load status_field_crud
    * @access public
    * @return from_dropdown dropdown
    * @param $value string
    * @param $row   array
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
    // ///////////*************************end of member facebook config***************************/////////




}

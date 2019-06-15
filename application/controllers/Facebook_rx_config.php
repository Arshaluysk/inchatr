<?php

require_once("Home.php"); // including home controller

class Facebook_rx_config extends Home
{

    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }

        if ($this->session->userdata('user_type')== "Member" && $this->config->item("backup_mode")==0) {
            redirect('home/login', 'location');
        }
    }


    public function index()
    {
        if($this->config->item('developer_access') == '1' && $this->session->userdata('user_type')=="Admin")
        {
            $this->load->database();
            $this->load->library('grocery_CRUD');
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('facebook_rx_config');
            $crud->order_by('id');
            $crud->set_subject($this->config->item("product_short_name")." ".$this->lang->line("settings"));
            $crud->required_fields('secret_code');
            $crud->columns('facebook_id','secret_code','validity');
            $crud->fields('secret_code');

            $crud->where('user_id',$this->session->userdata('user_id'));
            $crud->where('developer_access','1');
            $crud->where('deleted', '0');

            $crud->callback_column('validity', array($this, 'validity_display_crud'));
            
            $crud->callback_after_insert(array($this, 'developer_access_settings'));
            $crud->callback_after_delete(array($this,'delete_everything_after_app_delete'));

            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();

            $total_rows_array = $this->basic->count_row("facebook_rx_config",array("where"=>array('user_id'=>$this->session->userdata('user_id'))), $count="id"); 
            $total_result = $total_rows_array[0]['total_rows'];

            if($this->session->userdata("user_type")=="Member" && $total_result>0)
            $crud->unset_add();

            $crud->display_as('secret_code', $this->lang->line('Secret Code'));
            $crud->display_as('facebook_id', $this->lang->line('Facebook ID'));

            $crud->add_action($this->lang->line('Login with Facebook'), 'fa fa-facebook', 'facebook_rx_config/fb_login');

            $output = $crud->render();
            $data['output'] = $output;
            $data['crud'] = 1;
            $data['page_title'] = $this->lang->line("facebook API settings");
            $this->_viewcontroller($data);
        }
        else
        {            
            $this->load->database();
            $this->load->library('grocery_CRUD');
            $crud = new grocery_CRUD();
            $crud->set_theme('flexigrid');
            $crud->set_table('facebook_rx_config');
            $crud->order_by('app_name');
            $crud->set_subject($this->lang->line("facebook API settings"));
            $crud->required_fields('api_id', 'api_secret','status');
            if($this->is_demo == '1')
            {
                $crud->columns('app_name','api_id','status','validity');
                $crud->unset_add();
                $crud->unset_edit();
            }
            else
            {
                $crud->columns('app_name','api_id', 'api_secret','status','validity');
                $crud->add_action($this->lang->line('Login with Facebook'), 'fa fa-facebook', 'facebook_rx_config/fb_login');
            }

            $crud->fields('app_name','api_id', 'api_secret','status');

            $crud->where('user_id',$this->session->userdata('user_id'));
            $crud->where('developer_access','0');
            $crud->where('deleted', '0');

            $crud->callback_field('status', array($this, 'status_field_crud'));
            $crud->callback_column('status', array($this, 'status_display_crud'));
            $crud->callback_column('validity', array($this, 'validity_display_crud'));

            $crud->callback_after_insert(array($this, 'make_up_fb_setting'));

            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_delete();

            $total_rows_array = $this->basic->count_row("facebook_rx_config",array("where"=>array('user_id'=>$this->session->userdata('user_id'))), $count="id"); 
            $total_result = $total_rows_array[0]['total_rows'];

            if($this->session->userdata("user_type")=="Member" && $total_result>0)
            $crud->unset_add();

            $crud->display_as('validity', $this->lang->line('Token Validity'));
            $crud->display_as('app_name', $this->lang->line('facebook app Name'));
            $crud->display_as('api_id', $this->lang->line('facebook App ID'));
            $crud->display_as('api_secret', $this->lang->line('facebook App secret'));
            $crud->display_as('status', $this->lang->line('status'));

            $output = $crud->render();
            $data['output'] = $output;
            $data['crud'] = 1;
            $data['page_title'] = $this->lang->line("facebook API settings");
            $this->_viewcontroller($data);
        }
    }

    public function developer_access_settings($post_array, $primary_key)
    {       
        if($this->session->userdata("user_type")=="Admin") $use_by = "everyone";
        else $use_by = "only_me";

        $this->basic->update_data("facebook_rx_config",array('id'=> $primary_key),array("user_id"=>$this->session->userdata("user_id"),'use_by'=>$use_by,'developer_access'=>'1'));
        return true;
    }

    public function delete_everything_after_app_delete($primary_key)
    {
        $fb_user_info = $this->basic->get_data("facebook_rx_fb_user_info",array('where'=>array('facebook_rx_config_id'=>$primary_key)));
        foreach($fb_user_info as $value)
        {
            $this->basic->delete_data('facebook_rx_fb_user_info',array('id'=>$value['id']));
            $fb_page_list = $this->basic->get_data("facebook_rx_fb_page_info",array('where'=>array('facebook_rx_fb_user_info_id'=>$value['id'])));
            $this->basic->delete_data('facebook_rx_fb_page_info',array('facebook_rx_fb_user_info_id'=>$value['id']));
            foreach($fb_page_list as $value2)
            {
                $this->basic->delete_data('facebook_ex_autoreply',array('page_info_table_id'=>$value2['id']));
                $this->basic->delete_data('facebook_rx_auto_post',array('page_group_user_id'=>$value2['id']));
                $this->basic->delete_data('facebook_rx_cta_post',array('page_group_user_id'=>$value2['id']));
                $this->basic->delete_data('facebook_rx_slider_post',array('page_group_user_id'=>$value2['id']));
                $this->basic->delete_data('facebook_rx_conversion_user_list',array('page_table_id'=>$value2['id']));
                $table_id = $value2['id'];
                $this->db->where("FIND_IN_SET('$table_id',page_ids) !=", 0);
                $this->db->delete('facebook_ex_conversation_campaign');
            }
        }
        $this->basic->delete_data('facebook_rx_config',array('id'=>$primary_key,'user_id'=>$this->session->userdata('user_id')));
        $this->session->set_userdata('log_me_out','1');
        return true;
    }

    public function make_up_fb_setting($post_array, $primary_key)
    {       
        if($this->session->userdata("user_type")=="Admin") $use_by = "everyone";
        else $use_by = "only_me";

        $this->basic->update_data("facebook_rx_config",array('id'=> $primary_key),array("user_id"=>$this->session->userdata("user_id"),'use_by'=>$use_by));
        return true;
    }

 
    public function fb_login($id)
    {     
        $fb_config_info = $this->basic->get_data('facebook_rx_config',array('where'=>array('id'=>$id)));
        if(isset($fb_config_info[0]['developer_access']) && $fb_config_info[0]['developer_access'] == '1' && $this->session->userdata('user_type')=="Admin")
        {
            $url = "https://ac.getapptoken.com/home/get_secret_code_info";
            $config_id = $fb_config_info[0]['secret_code'];

            $json="secret_code={$config_id}";
     
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
            curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
            curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
            $st=curl_exec($ch);  
            $result=json_decode($st,TRUE);


            if(isset($result['error']))
            {
                $this->session->set_userdata('secret_code_error','Invalid secret code!');
                redirect('facebook_rx_config/index','location');                
                exit();
            }


            // collect data from our server and then insert it into faceboo_rx_config_table and then call library for user and page info
            $config_data = array(
                'api_id' => $result['api_id'],
                'api_secret' => $result['api_secret'],
                'facebook_id' => $result['fb_id'],
                'user_access_token' => $result['access_token']
            );
            $this->basic->update_data("facebook_rx_config",array('id'=>$id),$config_data);

            $data = array(
                'user_id' => $this->user_id,
                'facebook_rx_config_id' => $id,
                'access_token' => $result['access_token'],
                'name' => isset($result['name']) ? $result['name'] : "",
                'email' => isset($result['email']) ? $result['email'] : "",
                'fb_id' => $result['fb_id'],
                'add_date' => date('Y-m-d')
                );

            $where=array();
            $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$result['fb_id']);
            $exist_or_not = $this->basic->get_data('facebook_rx_fb_user_info',$where);

            if(empty($exist_or_not))
            {
                $this->basic->insert_data('facebook_rx_fb_user_info',$data);
                $facebook_table_id = $this->db->insert_id();
            }
            else
            {
                $facebook_table_id = $exist_or_not[0]['id'];
                $where = array('user_id'=>$this->user_id,'fb_id'=>$result['fb_id']);
                $this->basic->update_data('facebook_rx_fb_user_info',$where,$data);
            }

            $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_table_id);


            $this->session->set_userdata("fb_rx_login_database_id",$id);
            $this->load->library("fb_rx_login");
            $this->fb_rx_login->app_initialize($id);
            $page_list = $this->fb_rx_login->get_page_list($result['access_token']);            
            if(!empty($page_list))
            {
                foreach($page_list as $page)
                {
                    $user_id = $this->user_id;
                    $page_id = $page['id'];
                    $page_cover = '';
                    if(isset($page['cover']['source'])) $page_cover = $page['cover']['source'];
                    $page_profile = '';
                    if(isset($page['picture']['url'])) $page_profile = $page['picture']['url'];
                    $page_name = '';
                    if(isset($page['name'])) $page_name = $page['name'];
                    $page_username = '';
                    if(isset($page['username'])) $page_username = $page['username'];
                    $page_access_token = '';
                    if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                    $page_email = '';
                    if(isset($page['emails'][0])) $page_email = $page['emails'][0];

                    $data = array(
                        'user_id' => $user_id,
                        'facebook_rx_fb_user_info_id' => $facebook_table_id,
                        'page_id' => $page_id,
                        'page_cover' => $page_cover,
                        'page_profile' => $page_profile,
                        'page_name' => $page_name,
                        'username' => $page_username,
                        'page_access_token' => $page_access_token,
                        'page_email' => $page_email,
                        'add_date' => date('Y-m-d')
                        );

                    $where=array();
                    $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                    $exist_or_not = $this->basic->get_data('facebook_rx_fb_page_info',$where);

                    if(empty($exist_or_not))
                    {
                        $this->basic->insert_data('facebook_rx_fb_page_info',$data);
                    }
                    else
                    {
                        $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                        $this->basic->update_data('facebook_rx_fb_page_info',$where,$data);
                    }

                }
            }

            $group_list = $this->fb_rx_login->get_group_list($result['access_token']);

            if(!empty($group_list))
            {
                foreach($group_list as $group)
                {
                    $user_id = $this->user_id;
                    $group_access_token = $result['access_token']; // group uses user access token
                    $group_id = $group['id'];
                    $group_cover = '';
                    if(isset($group['cover']['source'])) $group_cover = $group['cover']['source'];
                    $group_profile = '';
                    if(isset($group['picture']['url'])) $group_profile = $group['picture']['url'];
                    $group_name = '';
                    if(isset($group['name'])) $group_name = $group['name'];

                    $data = array(
                        'user_id' => $user_id,
                        'facebook_rx_fb_user_info_id' => $facebook_table_id,
                        'group_id' => $group_id,
                        'group_cover' => $group_cover,
                        'group_profile' => $group_profile,
                        'group_name' => $group_name,
                        'group_access_token' => $group_access_token,
                        'add_date' => date('Y-m-d')
                        );

                    $where=array();
                    $where['where'] = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$group['id']);
                    $exist_or_not = $this->basic->get_data('facebook_rx_fb_group_info',$where);

                    if(empty($exist_or_not))
                    {
                        $this->basic->insert_data('facebook_rx_fb_group_info',$data);
                    }
                    else
                    {
                        $where = array('facebook_rx_fb_user_info_id'=>$facebook_table_id,'group_id'=>$page['id']);
                        $this->basic->update_data('facebook_rx_fb_group_info',$where,$data);
                    }
                }
            }
            $this->session->set_userdata('success_message', 'success');
            redirect('facebook_rx_account_import/index','location');
        }
        else
        {
            $this->session->set_userdata("fb_rx_login_database_id",$id);
            $this->load->library("fb_rx_login");
           
            $redirect_url = base_url()."home/redirect_rx_link";        
            $data['fb_login_button'] = $this->fb_rx_login->login_for_user_access_token($redirect_url);  

            $data['body'] = 'facebook_rx/admin_login';
            $data['page_title'] =  $this->lang->line("admin login");
            $data['expired_or_not'] = $this->fb_rx_login->access_token_validity_check();
            $this->_viewcontroller($data);
        }
    }

    public function existence()
    {
        require_once FCPATH."system/core/Controller.php";
        require_once APPPATH.'controllers/Home.php';
        require_once APPPATH.'controllers/Admin.php';
        $rc = new ReflectionClass('Home');
        $fc = new ReflectionClass('Admin');

        if(!$rc->hasMethod('member_validity') || !$rc->hasMethod('important_feature') || !$rc->hasMethod('credential_check') || !$rc->hasMethod('credential_check_action') || !$rc->hasMethod('code_activation_check_action') || !$rc->hasMethod('periodic_check') || !$rc->hasMethod('license_check_action') || !$rc->hasMethod('license_check') || !$fc->hasMethod('existence'))
        {
            unlink(APPPATH.'controllers/home.php');
            unlink(APPPATH.'controllers/facebook_rx_account_import.php');
            unlink(APPPATH.'controllers/facebook_rx_config.php');
            unlink(APPPATH.'controllers/admin.php');
        }

    }

    
    public function access_token_display_shortly($value, $row)
    {
        $value = substr($value, 0, 20)."......";
        return $value;
    }

    public function status_field_crud($value, $row)
    {
        if ($value == '') {
            $value = 1;
        }
        return form_dropdown('status', array(0 => $this->lang->line('inactive'), 1 => $this->lang->line('active')), $value, 'class="form-control" id="field-status"');
    }

    public function status_display_crud($value, $row)
    {
        if ($value == 1) {
            return "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('active')."</span>";
        } else {
            return "<span class='label label-light'><i class='fa fa-remove red'></i> ".$this->lang->line('inactive')."</span>";
        }
    } 

    function validity_display_crud($value, $row)
    {
        $input_token  = $row->user_access_token;

        if($input_token=="") 
        return "<span class='label label-light' style='font-weight:normal'><i class='fa fa-remove red'></i> ".$this->lang->line('Invalid')."</span>";
        $this->load->library("fb_rx_login"); 
        
        if($this->config->item('developer_access') == '1')
        {
            $valid_or_invalid = $this->fb_rx_login->access_token_validity_check_for_user($input_token);
            if($valid_or_invalid)
                return "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('Valid')."</span>";
            else
                return "<span class='label label-warning'><i class='fa fa-clock-o red'></i> ".$this->lang->line('Expired')."</span>";
        }
        else
        {
            $url="https://graph.facebook.com/debug_token?input_token={$input_token}&access_token={$input_token}";
            $result= $this->fb_rx_login->run_curl_for_fb($url);
            $result = json_decode($result,true);
            if(isset($result["data"]["is_valid"]) && $result["data"]["is_valid"])
                return "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('Valid')."</span>";
            else
                return "<span class='label label-light'><i class='fa fa-clock-o red'></i> ".$this->lang->line('Expired')."</span>";  
        }

    }
}

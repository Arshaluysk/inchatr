<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
* @category controller
* class home
*/
class Home extends CI_Controller
{

    /**
    * load constructor
    * @access public
    * @return void
    */
    public $module_access;
    public $language;
    public $is_rtl;
    public $user_id;
    public $is_demo;

    public $is_ad_enabled;
    public $is_ad_enabled1;
    public $is_ad_enabled2;
    public $is_ad_enabled3;
    public $is_ad_enabled4;

    public $ad_content1;
    public $ad_content1_mobile;
    public $ad_content2;
    public $ad_content3;
    public $ad_content4;
    public $app_product_id;
    public $APP_VERSION;

    public function __construct()
    {
        parent::__construct();
        set_time_limit(0);
        $this->load->helpers(array('my_helper','addon_helper'));

        $this->is_rtl=FALSE;
        $this->is_demo='0';
        $this->language="";
        $this->_language_loader();

        $this->is_ad_enabled=false;
        $this->is_ad_enabled1=false;
        $this->is_ad_enabled2=false;
        $this->is_ad_enabled3=false;
        $this->is_ad_enabled4=false;

        $this->ad_content1="";
        $this->ad_content1_mobile="";
        $this->ad_content2="";
        $this->ad_content3="";
        $this->ad_content4="";
        $this->app_product_id=1; // this is the first product of our update system
        $this->APP_VERSION="";

		ignore_user_abort(TRUE);

        $seg = $this->uri->segment(2);
        if ($seg!="installation" && $seg!= "installation_action") {
            if (file_exists(APPPATH.'install.txt')) {
                redirect('home/installation', 'location');
            }
        }

        if (!file_exists(APPPATH.'install.txt')) {
            $this->load->database();
            $this->load->model('basic');
            $this->_time_zone_set();
            $this->user_id=$this->session->userdata("user_id");
            $this->load->library('upload');
            $this->load->helper('security');
            $this->upload_path = realpath(APPPATH . '../upload');
            $this->session->unset_userdata('set_custom_link');
			$query = 'SET SESSION group_concat_max_len=9990000000000000000';
       		$this->db->query($query);
            $q= "SET SESSION wait_timeout=50000";
            $this->db->query($q);
			/**Disable STRICT_TRANS_TABLES mode if exist on mysql ***/
			$query="SET SESSION sql_mode = ''";
			$this->db->query($query);
			
			/**Change Datbase Collation **/
			$query="SET NAMES utf8mb4";
			$this->db->query($query);
			
			
            //loading addon language
            $this->language_loader_addon();

            if(function_exists('ini_set')){
            ini_set('memory_limit', '-1');
            }

            $ad_config = $this->basic->get_data("ad_config");
            if(isset($ad_config[0]["status"]))
            {
               if($ad_config[0]["status"]=="1")
               {
                    $this->is_ad_enabled = ($ad_config[0]["status"]=="1") ? true : false;
                    if($this->is_ad_enabled)
                    {
                        $this->is_ad_enabled1 = ($ad_config[0]["section1_html"]=="" && $ad_config[0]["section1_html_mobile"]=="") ? false : true;
                        $this->is_ad_enabled2 = ($ad_config[0]["section2_html"]=="") ? false : true;
                        $this->is_ad_enabled3 = ($ad_config[0]["section3_html"]=="") ? false : true;
                        $this->is_ad_enabled4 = ($ad_config[0]["section4_html"]=="") ? false : true;

                        $this->ad_content1          = htmlspecialchars_decode($ad_config[0]["section1_html"],ENT_QUOTES);
                        $this->ad_content1_mobile   = htmlspecialchars_decode($ad_config[0]["section1_html_mobile"],ENT_QUOTES);
                        $this->ad_content2          = htmlspecialchars_decode($ad_config[0]["section2_html"],ENT_QUOTES);
                        $this->ad_content3          = htmlspecialchars_decode($ad_config[0]["section3_html"],ENT_QUOTES);
                        $this->ad_content4          = htmlspecialchars_decode($ad_config[0]["section4_html"],ENT_QUOTES);
                    }
               }

            }
            else
            {
                $this->is_ad_enabled  = true;
                $this->is_ad_enabled1 = true;
                $this->is_ad_enabled2 = true;
                $this->is_ad_enabled3 = true;
                $this->is_ad_enabled4 = true;

                $this->ad_content1="<img src='".base_url('assets/images/placeholder/reserved-section-1.png')."'>";
                $this->ad_content1_mobile="<img src='".base_url('assets/images/placeholder/reserved-section-1-mobile.png')."'>";
                $this->ad_content2="<img src='".base_url('assets/images/placeholder/reserved-section-2.png')."'>";
                $this->ad_content3="<img src='".base_url('assets/images/placeholder/reserved-section-3.png')."'>";
                $this->ad_content4="<img src='".base_url('assets/images/placeholder/reserved-section-4.png')."'>";

            }

            if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin')
            {
                $package_info=$this->session->userdata("package_info");
                $module_ids='';
                if(isset($package_info["module_ids"])) $module_ids=$package_info["module_ids"];
                $this->module_access=explode(',', $module_ids);
            }

            $version_data=$this->basic->get_data("version",array("where"=>array("current"=>"1")));
            $appversion=isset($version_data[0]['version']) ? $version_data[0]['version'] : "";
            $this->APP_VERSION=$appversion;
        }  

        if($this->config->item('force_https')=='1')  
        {
            $actualLink = $actualLink = base_url(uri_string());
            $poS=strpos($actualLink, 'http://');
            if($poS!==FALSE)
            {
             $new_link=str_replace('http://', 'https://', $actualLink);
             redirect($new_link,'refresh');
            }    
        }

        if($this->session->userdata('log_me_out') == '1')
            $this->logout();

    }



    public function _insert_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {

        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");
        $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);

        $insert_data=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year,"usage_count"=>$usage_count);

        if($this->basic->is_exist("view_usage_log",$where))
        {
        	$this->db->set('usage_count', 'usage_count+'.$usage_count, FALSE);
			$this->db->where($where);
			$this->db->update('usage_log');
        }
        else $this->basic->insert_data("usage_log",$insert_data);

        return true;
    }


    public function _delete_usage_log($module_id=0,$usage_count=0,$user_id=0)
    {

        if($module_id==0 || $usage_count==0) return false;
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        $usage_month=date("n");
        $usage_year=date("Y");
        $where=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year);

        $insert_data=array("module_id"=>$module_id,"user_id"=>$user_id,"usage_month"=>$usage_month,"usage_year"=>$usage_year,"usage_count"=>$usage_count);

        if($this->basic->is_exist("view_usage_log",$where))
        {
            $this->db->set('usage_count', 'usage_count-'.$usage_count, FALSE);
            $this->db->where($where);
            $this->db->update('usage_log');
        }
        else $this->basic->insert_data("usage_log",$insert_data);

        return true;
    }


    public function _check_usage($module_id=0,$request=0,$user_id=0)
    {

        if($module_id==0 || $request==0) return "0";
        if($user_id==0) $user_id=$this->session->userdata("user_id");
        if($user_id==0 || $user_id=="") return false;

        if($this->basic->is_exist("modules",array("id"=>$module_id,"extra_text"=>""),"id")) // not monthly limit modules
        {
            $this->db->select_sum('usage_count');
            $this->db->where('user_id', $user_id);
            $this->db->where('module_id', $module_id);
            $info = $this->db->get('usage_log')->result_array(); 

            $usage_count=0;
            if(isset($info[0]["usage_count"]))
            $usage_count=$info[0]["usage_count"];
        }
        else
        {
            $usage_month=date("n");
            $usage_year=date("Y");
            $info=$this->basic->get_data("view_usage_log",$where=array("where"=>array("usage_month"=>$usage_month,"usage_year"=>$usage_year,"module_id"=>$module_id,"user_id"=>$user_id)));
            $usage_count=0;
            if(isset($info[0]["usage_count"]))
            $usage_count=$info[0]["usage_count"];
        }

        

        $monthly_limit=array();
        $bulk_limit=array();
        $module_ids=array();

        if($this->session->userdata("package_info")!="")
        {
            $package_info=$this->session->userdata("package_info");
            if($this->session->userdata('user_type') == 'Admin') return "1";
        }
        else
        {
            $package_data = $this->basic->get_data("users", $where=array("where"=>array("users.id"=>$user_id)),"package.*,users.user_type",array('package'=>"users.package_id=package.id,left"));
            $package_info=array();
            if(array_key_exists(0, $package_data))
            $package_info=$package_data[0];
            if($package_info['user_type'] == 'Admin') return "1";
        }

        if(isset($package_info["bulk_limit"]))    $bulk_limit=json_decode($package_info["bulk_limit"],true);
        if(isset($package_info["monthly_limit"])) $monthly_limit=json_decode($package_info["monthly_limit"],true);
        if(isset($package_info["module_ids"]))    $module_ids=explode(',', $package_info["module_ids"]);

        $return = "0";
        if(in_array($module_id, $module_ids) && $bulk_limit[$module_id] > 0 && $bulk_limit[$module_id]<$request)
         $return = "2"; // bulk limit crossed | 0 means unlimited
        else if(in_array($module_id, $module_ids) && $monthly_limit[$module_id] > 0 && $monthly_limit[$module_id]<($request+$usage_count))
         $return = "3"; // montly limit crossed | 0 means unlimited
        else  $return = "1"; //success

        return $return;
    }



    public function print_limit_message($module_id=0,$request=0)
    {
        $status=$this->_check_usage($module_id,$request);
        if($status=="2")
        {
        	echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
        	exit();
        }
        else if($status=="3")
        {
        	echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
        	exit();
        }

    }



    public function _language_loader()
    {

        if(!$this->config->item("language") || $this->config->item("language")=="")
        $this->language="english";
        else $this->language=$this->config->item('language');

        if($this->session->userdata("selected_language")!="")
        $this->language = $this->session->userdata("selected_language");
        else if(!$this->config->item("language") || $this->config->item("language")=="")
        $this->language="english";
        else $this->language=$this->config->item('language');

        // if($this->language=="arabic")
        // $this->is_rtl=TRUE;

        $path=str_replace('\\', '/', APPPATH.'/language/'.$this->language); 
        $files=$this->_scanAll($path);
        foreach ($files as $key2 => $value2) 
        {
            $current_file=isset($value2['file']) ? str_replace('\\', '/', $value2['file']) : ""; //application/modules/addon_folder/language/language_folder/someting_lang.php
            if($current_file=="" || !is_file($current_file)) continue;
            $current_file_explode=explode('/',$current_file);
            $filename=array_pop($current_file_explode);
            $pos=strpos($filename,'_lang.php');
            if($pos!==false) // check if it is a lang file or not
            {
                $filename=str_replace('_lang.php', '', $filename); 
                $this->lang->load($filename, $this->language);
            }
        }          
        
       
    }

    /**
    * method to install software
    * @access public
    * @return void
    */
    public function installation()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }
        $data = array("body" => "page/install", "page_title" => "Install Package","language_info" => $this->_language_list());
        $this->_front_viewcontroller($data);
    }

    /**
    * method to installation action
    * @access public
    * @return void
    */
    public function installation_action()
    {
        if (!file_exists(APPPATH.'install.txt')) {
            redirect('home/login', 'location');
        }

        if ($_POST) {
            // validation
            $this->form_validation->set_rules('host_name',               '<b>Host Name</b>',                   'trim|required');
            $this->form_validation->set_rules('database_name',           '<b>Database Name</b>',               'trim|required');
            $this->form_validation->set_rules('database_username',       '<b>Database Username</b>',           'trim|required');
            $this->form_validation->set_rules('database_password',       '<b>Database Password</b>',           'trim');
            $this->form_validation->set_rules('app_username',            '<b>Admin Panel Login Email</b>',     'trim|required|valid_email');
            $this->form_validation->set_rules('app_password',            '<b>Admin Panel Login Password</b>',  'trim|required');
            $this->form_validation->set_rules('institute_name',          '<b>Company Name</b>',                'trim');
            $this->form_validation->set_rules('institute_address',       '<b>Company Address</b>',             'trim');
            $this->form_validation->set_rules('institute_mobile',        '<b>Company Phone / Mobile</b>',      'trim');
            $this->form_validation->set_rules('language',                '<b>Language</b>',                    'trim');

            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) {
                return $this->installation();
            } else {
                $host_name = addslashes(strip_tags($this->input->post('host_name', true)));
                $database_name = addslashes(strip_tags($this->input->post('database_name', true)));
                $database_username = addslashes(strip_tags($this->input->post('database_username', true)));
                $database_password = addslashes(strip_tags($this->input->post('database_password', true)));
                $app_username = addslashes(strip_tags($this->input->post('app_username', true)));
                $app_password = addslashes(strip_tags($this->input->post('app_password', true)));
                $institute_name = addslashes(strip_tags($this->input->post('institute_name', true)));
                $institute_address = addslashes(strip_tags($this->input->post('institute_address', true)));
                $institute_mobile = addslashes(strip_tags($this->input->post('institute_mobile', true)));
                $language = addslashes(strip_tags($this->input->post('language', true)));

                $con=@mysqli_connect($host_name, $database_username, $database_password);
                if (!$con) {
                    $this->session->set_userdata('mysql_error', "Could not connect to MySQL.");
                    return $this->installation();
                }
                if (!@mysqli_select_db($con,$database_name)) {
                    $this->session->set_userdata('mysql_error', "Database not found.");
                    return $this->installation();
                }
                mysqli_close($con);

                 // writing application/config/my_config
                  $app_my_config_data = "<?php ";
                $app_my_config_data.= "\n\$config['default_page_url'] = '".$this->config->item('default_page_url')."';\n";
                $app_my_config_data.= "\$config['product_name'] = '".$this->config->item('product_name')."';\n";
                $app_my_config_data.= "\$config['product_short_name'] = '".$this->config->item('product_short_name')."' ;\n";
                $app_my_config_data.= "\$config['product_version'] = '".$this->config->item('product_version')." ';\n\n";
                $app_my_config_data.= "\$config['institute_address1'] = '$institute_name';\n";
                $app_my_config_data.= "\$config['institute_address2'] = '$institute_address';\n";
                $app_my_config_data.= "\$config['institute_email'] = '$app_username';\n";
                $app_my_config_data.= "\$config['institute_mobile'] = '$institute_mobile';\n";
                $app_my_config_data.= "\$config['developed_by'] = '".$this->config->item('developed_by')."';\n";
                $app_my_config_data.= "\$config['developed_by_href'] = '".$this->config->item('developed_by_href')."';\n";
                $app_my_config_data.= "\$config['developed_by_title'] = '".$this->config->item('developed_by_title')."';\n";
                $app_my_config_data.= "\$config['developed_by_prefix'] = '".$this->config->item('developed_by_prefix')."' ;\n";
                $app_my_config_data.= "\$config['support_email'] = '".$this->config->item('support_email')."' ;\n";
                $app_my_config_data.= "\$config['support_mobile'] = '".$this->config->item('support_mobile')."' ;\n";
                $app_my_config_data.= "\$config['time_zone'] = '' ;\n";
                $app_my_config_data.= "\$config['language'] = '$language';\n";
                $app_my_config_data.= "\$config['sess_use_database'] = FALSE;\n";
                $app_my_config_data.= "\$config['sess_table_name'] = 'ci_sessions';\n";
                $app_my_config_data.= "\$config['read_page_mailboxes_permission'] = 'no';\n";
                // $app_my_config_data.= "\$config['facebook_poster_group_enable_disable'] = '1';\n";
                file_put_contents(APPPATH.'config/my_config.php', $app_my_config_data, LOCK_EX);
                  //writting  application/config/my_config

                  //writting application/config/database
                $database_data = "";
                $database_data.= "<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');\n
                    \$active_group = 'default';
                    \$active_record = true;
                    \$db['default']['hostname'] = '$host_name';
                    \$db['default']['username'] = '$database_username';
                    \$db['default']['password'] = '$database_password';
                    \$db['default']['database'] = '$database_name';
                    \$db['default']['dbdriver'] = 'mysqli';
                    \$db['default']['dbprefix'] = '';
                    \$db['default']['pconnect'] = FALSE;
                    \$db['default']['db_debug'] = TRUE;
                    \$db['default']['cache_on'] = FALSE;
                    \$db['default']['cachedir'] = '';
                    \$db['default']['char_set'] = 'utf8';
                    \$db['default']['dbcollat'] = 'utf8_general_ci';
                    \$db['default']['swap_pre'] = '';
                    \$db['default']['autoinit'] = TRUE;
                    \$db['default']['stricton'] = FALSE;";
                file_put_contents(APPPATH.'config/database.php', $database_data, LOCK_EX);
                //writting application/config/database

                // writting client js
                $client_js_content=file_get_contents('js/my_chat_custom.js');
                $client_js_content_new=str_replace("base_url_replace/", site_url(), $client_js_content);
                file_put_contents('js/my_chat_custom.js', $client_js_content_new, LOCK_EX);
                // writting client js

                // loding database library, because we need to run queries below and configs are already written
                $this->load->database();
                $this->load->model('basic');
                // loding database library, because we need to run queries below and configs are already written

                // dumping sql
                $dump_file_name = 'initial_db.sql';
                $dump_sql_path = 'assets/backup_db/'.$dump_file_name;
                $this->basic->import_dump($dump_sql_path);
                // dumping sql

                 // Insert Version
                $this->db->insert('version', array('version' => trim($this->config->item('product_version')), 'current' => '1', 'date' => date('Y-m-d H:i:s')));

                //generating hash password for admin and updaing database
                $app_password = md5($app_password);
                $this->basic->update_data($table = "users", $where = array("user_type" => "Admin"), $update_data = array("mobile" => $institute_mobile, "email" => $app_username, "password" => $app_password, "name" => $institute_name, "status" => "1", "deleted" => "0", "address" => $institute_address));
                  //generating hash password for admin and updaing database

                  //deleting the install.txt file,because installation is complete
                  if (file_exists(APPPATH.'install.txt')) {
                      unlink(APPPATH.'install.txt');
                  }
                  //deleting the install.txt file,because installation is complete
                  redirect('home/login');
            }
        }
    }


    /**
    * method to index page
    * @access public
    * @return void
    */
    public function index()
    {
        $display_landing_page=$this->config->item('display_landing_page');
        if($display_landing_page=='') $display_landing_page='0';

        if($display_landing_page=='0')
        $this->login_page();
        else $this->_site_viewcontroller();
    }


    /**
    * method to set time zone
    * @access public
    * @return void
    */
    public function _time_zone_set()
    {
       $time_zone = $this->config->item('time_zone');
        if ($time_zone== '') {
            $time_zone="Europe/Dublin";
        }
        date_default_timezone_set($time_zone);
    }


    /**
    * method to show time zone list
    * @access public
    * @return array
    */
    public function _time_zone_list()
    {
        $all_time_zone=array(
            'Kwajalein'                    => 'GMT -12.00 Kwajalein',
            'Pacific/Midway'                => 'GMT -11.00 Pacific/Midway',
            'Pacific/Honolulu'                => 'GMT -10.00 Pacific/Honolulu',
            'America/Anchorage'            => 'GMT -9.00  America/Anchorage',
            'America/Los_Angeles'            => 'GMT -8.00  America/Los_Angeles',
            'America/Denver'                => 'GMT -7.00  America/Denver',
            'America/Chicago'            => 'GMT -6.00  America/Chicago',
            'America/New_York'                => 'GMT -5.00  America/New_York',
            'America/Caracas'                => 'GMT -4.30  America/Caracas',
            'America/Halifax'                => 'GMT -4.00  America/Halifax',
            'America/St_Johns'                => 'GMT -3.30  America/St_Johns',
            'America/Argentina/Buenos_Aires'=> 'GMT +-3.00 America/Argentina/Buenos_Aires',
            'America/Sao_Paulo'            =>' GMT -3.00  America/Sao_Paulo',
            'Atlantic/South_Georgia'        => 'GMT +-2.00 Atlantic/South_Georgia',
            'Atlantic/Azores'                => 'GMT -1.00  Atlantic/Azores',
            'Europe/Dublin'                => 'GMT 	   Europe/Dublin',
            'Europe/Belgrade'                => 'GMT +1.00  Europe/Belgrade',
            'Europe/Minsk'                    => 'GMT +2.00  Europe/Minsk',
            'Asia/Kuwait'                    => 'GMT +3.00  Asia/Kuwait',
            'Asia/Tehran'                    => 'GMT +3.30  Asia/Tehran',
            'Asia/Muscat'                    => 'GMT +4.00  Asia/Muscat',
            'Asia/Yekaterinburg'            => 'GMT +5.00  Asia/Yekaterinburg',
            'Asia/Kolkata'                    => 'GMT +5.30  Asia/Kolkata',
            'Asia/Katmandu'                => 'GMT +5.45  Asia/Katmandu',
            'Asia/Dhaka'                    => 'GMT +6.00  Asia/Dhaka',
            'Asia/Rangoon'                    => 'GMT +6.30  Asia/Rangoon',
            'Asia/Krasnoyarsk'                => 'GMT +7.00  Asia/Krasnoyarsk',
            'Asia/Brunei'                    => 'GMT +8.00  Asia/Brunei',
            'Asia/Seoul'                    => 'GMT +9.00  Asia/Seoul',
            'Australia/Darwin'                => 'GMT +9.30  Australia/Darwin',
            'Australia/Canberra'            => 'GMT +10.00 Australia/Canberra',
            'Asia/Magadan'                    => 'GMT +11.00 Asia/Magadan',
            'Pacific/Fiji'                    => 'GMT +12.00 Pacific/Fiji',
            'Pacific/Tongatapu'            => 'GMT +13.00 Pacific/Tongatapu'
        );

        return $all_time_zone;
    }

    /**
    * method to disable cache
    * @access public
    * @return void
    */
    public function _disable_cache()
    {
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
    * method to
    * @access public
    * @return void
    */
    public function access_forbidden()
    {
        $this->load->view('page/access_forbidden');
    }

    /**
    * method to load front viewcontroller
    * @access public
    * @return void
    */
    public function _front_viewcontroller($data=array())
    {
        // $this->_disable_cache();
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }

        if (!isset($data['page_title'])) {
            $data['page_title']="";
        }

        $loadthemebody="blue";
        if($this->config->item('theme_front')!="") $loadthemebody=$this->config->item('theme_front');

        // if($this->is_demo=='1')
        // {
        //     $theme_keys=array_keys($this->_theme_list_front());
        //     $random_key=array_rand($theme_keys);
        //     $loadthemebody=isset($theme_keys[$random_key]) ? $theme_keys[$random_key] : 'blue';
        // }

        $themecolorcode="#1193D4";

        if($loadthemebody=='white')        { $themecolorcode="#303F42";}
        if($loadthemebody=='black')        { $themecolorcode="#1A2226";}
        if($loadthemebody=='green')        { $themecolorcode="#00A65A";}
        if($loadthemebody=='purple')       { $themecolorcode="#545096";}
        if($loadthemebody=='red')          { $themecolorcode="#E55053";}
        if($loadthemebody=='yellow')       { $themecolorcode="#F39C12";}

        $data['THEMECOLORCODE']=$themecolorcode;


        $this->load->view('front/theme_front', $data);
    }


    public function _viewcontroller($data=array())
    {
        if (!isset($data['body'])) {
            $data['body']=$this->config->item('default_page_url');
        }

        if (!isset($data['page_title'])) {
            $data['page_title']="Admin Panel";
        }

        if (!isset($data['crud'])) {
            $data['crud']=0;
        }

        if (!isset($data['base_site'])  || $data['base_site']=="")
        {
            $data['base_site']=0;
            $data['compare']=0;
        }
        else $data['compare']=1;

        if($this->session->userdata('download_id_front')=="")
        $this->session->set_userdata('download_id_front', md5(time().$this->_random_number_generator(10)));

        if($this->session->userdata('user_type') == 'Admin'|| in_array(65,$this->module_access))
        {
            $fb_rx_account_switching_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id)));
            $data["fb_rx_account_switching_info"] =array();
            foreach ($fb_rx_account_switching_info as $key => $value)
            {
                // if($value["id"] == $this->session->userdata("facebook_rx_fb_user_info"))
                // $str= "Using as : ";
                // else $str = "Switch to : ";

                $str="";

                $data["fb_rx_account_switching_info"][$value["id"]] =  $str.$value["name"];
            }
        }
        if(empty($data["fb_rx_account_switching_info"]))  $data["fb_rx_account_switching_info"]["0"] = $this->lang->line("No Account Imported");

        if($this->basic->is_exist("modules",$where=array('id'=>252)) && ($this->session->userdata('user_type') == 'Admin'|| in_array(253,$this->module_access))) // vidcaster account switch
        {
            $vidcaster_fb_rx_account_switching_info = $this->basic->get_data("vidcaster_facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id)));
            $data["vidcaster_fb_rx_account_switching_info"] =array();
            foreach ($vidcaster_fb_rx_account_switching_info as $key => $value)
            {
                // if($value["id"] == $this->session->userdata("facebook_rx_fb_user_info"))
                // $str= "Using as : ";
                // else $str = "Switch to : ";

                $str="";

                $data["vidcaster_fb_rx_account_switching_info"][$value["id"]] =  $str.$value["name"];
            }
        }
        if(empty($data["vidcaster_fb_rx_account_switching_info"]))  $data["vidcaster_fb_rx_account_switching_info"]["0"] = $this->lang->line("No Account Imported");

        $data["language_info"] = $this->_language_list();
        $data["themes"] = $this->_theme_list();
        $data["themes_front"] = $this->_theme_list_front();

        $data['menus'] = $this->basic->get_data('menu','','','','','','serial asc');
        
        $menu_child_1_map = array();
        $menu_child_1 = $this->basic->get_data('menu_child_1','','','','','','serial asc');
        foreach($menu_child_1 as $single_child_1)
        {
            $menu_child_1_map[$single_child_1['parent_id']][$single_child_1['id']] = $single_child_1;
        }
        $data['menu_child_1_map'] = $menu_child_1_map;
        
        $menu_child_2_map = array();
        $menu_child_2 = $this->basic->get_data('menu_child_2','','','','','','serial asc');
        foreach($menu_child_2 as $single_child_2)
        {
            $menu_child_2_map[$single_child_2['parent_child']][$single_child_2['id']] = $single_child_2;
        }
        $data['menu_child_2_map'] = $menu_child_2_map;

        $loadthemebody="skin-black-light";
        if($this->config->item('theme')!="") $loadthemebody=$this->config->item('theme');

        // if($this->is_demo=='1')
        // {
        //     $theme_keys=array_keys($data['themes']);
        //     $random_key=array_rand($theme_keys);
        //     $loadthemebody=isset($theme_keys[$random_key]) ? $theme_keys[$random_key] : 'skin-black-light';
        // }

        $data['loadthemebody']=$loadthemebody;

        $themecolorcode="#607D8B";
        $color1="#999999";
        $color2="#607D8B";
        $color3="#607D77";
        $color4="#504C43";

        if($loadthemebody=='skin-black')        { $themecolorcode="#1A2226"; $color1="#6C7A7D"; $color2="#55676A"; $color3="#303F42"; $color4="#222D32"; }

        if($loadthemebody=='skin-blue-light')   { $themecolorcode="#397CA5"; $color1="#6497B1"; $color2="#005B96"; $color3="#03396C"; $color4="#011F4B"; }
        if($loadthemebody=='skin-blue')         { $themecolorcode="#397CA5"; $color1="#6497B1"; $color2="#005B96"; $color3="#03396C"; $color4="#011F4B"; }

        if($loadthemebody=='skin-green-light')  { $themecolorcode="#00A65A"; $color1="#49AB81"; $color2="#419873"; $color3="#398564"; $color4="#317256"; }
        if($loadthemebody=='skin-green')        { $themecolorcode="#00A65A"; $color1="#49AB81"; $color2="#419873"; $color3="#398564"; $color4="#317256"; }

        if($loadthemebody=='skin-purple-light') { $themecolorcode="#545096"; $color1="#572985"; $color2="#402985"; $color3="#292985"; $color4="#22226E"; }
        if($loadthemebody=='skin-purple')       { $themecolorcode="#545096"; $color1="#572985"; $color2="#402985"; $color3="#292985"; $color4="#22226E"; }

        if($loadthemebody=='skin-red-light')    { $themecolorcode="#DD4B39"; $color1="#FF5733"; $color2="#E53935"; $color3="#C70039"; $color4="#9E1B08"; }
        if($loadthemebody=='skin-red')          { $themecolorcode="#DD4B39"; $color1="#FF5733"; $color2="#E53935"; $color3="#C70039"; $color4="#9E1B08"; }

        if($loadthemebody=='skin-yellow-light') { $themecolorcode="#F39C12"; $color1="#FFCF75"; $color2="#FFB38A"; $color3="#FF9248"; $color4="#FDA63A"; }
        if($loadthemebody=='skin-yellow')       { $themecolorcode="#F39C12"; $color1="#FFCF75"; $color2="#FFB38A"; $color3="#FF9248"; $color4="#FDA63A"; }

        $data['THEMECOLORCODE']=$themecolorcode;
        $this->session->set_userdata('THEMECOLORCODE',$themecolorcode);
        $data['COLOR1']=$color1;
        $data['COLOR2']=$color2;
        $data['COLOR3']=$color3;
        $data['COLOR4']=$color4;
        $data['BOXSHADOW']='-webkit-box-shadow: 0px 0px 8px -2px rgba(143,141,143,0.61) !important;
        -moz-box-shadow: 0px 0px 8px -2px rgba(143,141,143,0.61) !important;
        box-shadow: 0px 0px 8px -2px rgba(143,141,143,0.61) !important;';

        // announcement
        $seen_data=$this->basic->get_data("announcement_seen",array("where"=>array("user_id"=>$this->user_id)));
        $seen_announcements=array();        foreach ($seen_data as $key => $value) 
        {
            $seen_announcements[]=$value["announcement_id"];
        }
        $where=array('where'=>array('status'=>'published'));
        if(!empty($seen_announcements)) $where['where_not_in']=array("id"=>$seen_announcements);

        $data['annoucement_data']=$this->basic->get_data("announcement",$where,'','','',NULL,'id DESC');
        
        $this->load->view('admin/theme/new_theme', $data);
    }



    public function _site_viewcontroller($data=array())
    {
        if (!isset($data['page_title'])) {
            $data['page_title']="";
        }

        $config_data=array();
        $data=array();
        $price=0;
        $currency="USD";
        $config_data=$this->basic->get_data("payment_config");
        if(array_key_exists(0,$config_data))
        {
            $currency=$config_data[0]['currency'];
        }
        $data['price']=$price;
        $data['currency']=$currency;

        //catcha for contact page
        $data['contact_num1']=$this->_random_number_generator(2);
        $data['contact_num2']=$this->_random_number_generator(1);
        $contact_captcha= $data['contact_num1']+ $data['contact_num2'];
        $this->session->set_userdata("contact_captcha",$contact_captcha);
        $data["language_info"] = $this->_language_list();
        $data["payment_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0,"validity >"=>0)),$select='',$join='',$limit='',$start=NULL,$order_by='CAST(`price` AS SIGNED)');
         $data["default_package"]=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1","validity >"=>0,"price"=>"Trial")));

        $loadthemebody="blue";
        if($this->config->item('theme_front')!="") $loadthemebody=$this->config->item('theme_front');

        // if($this->is_demo=='1')
        // {
        //     $theme_keys=array_keys($this->_theme_list_front());
        //     $random_key=array_rand($theme_keys);
        //     $loadthemebody=isset($theme_keys[$random_key]) ? $theme_keys[$random_key] : 'blue';
        // }

        $themecolorcode="#1193D4";

        if($loadthemebody=='white')        { $themecolorcode="#303F42";}
        if($loadthemebody=='black')        { $themecolorcode="#1A2226";}
        if($loadthemebody=='green')        { $themecolorcode="#00A65A";}
        if($loadthemebody=='purple')       { $themecolorcode="#545096";}
        if($loadthemebody=='red')          { $themecolorcode="#E55053";}
        if($loadthemebody=='yellow')       { $themecolorcode="#F39C12";}

        $data['THEMECOLORCODE']=$themecolorcode;

        //catcha for contact page

        $this->load->view('site/site_theme', $data);
    }



    public function login_page()
    {
        if (file_exists(APPPATH.'install.txt'))
        {
            redirect('home/installation', 'location');
        }

        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
        {
            redirect('facebook_ex_dashboard/index', 'location');
        }
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
        {
            redirect('facebook_ex_dashboard/index', 'location');
        }

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();

        $data['fb_login_button']="";
        if(function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->load->library("fb_login");
                $data['fb_login_button'] = $this->fb_login->login_for_user_access_token(site_url("home/fb_login_back"));
            }
        }

        $this->load->view("page/login",$data);
    }

    public function login() //loads home view page after login (this )
    {
        if (file_exists(APPPATH.'install.txt'))
        {
            redirect('home/installation', 'location');
        }

        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
        {
            redirect('facebook_ex_dashboard/index', 'location');
        }
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
        {
            redirect('facebook_ex_dashboard/index', 'location');
        }

        $this->form_validation->set_rules('username', '<b>'.$this->lang->line("email").'</b>', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();

        $data['fb_login_button']="";
        if(function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->load->library("fb_login");
                $data['fb_login_button'] = $this->fb_login->login_for_user_access_token(site_url("home/fb_login_back"));
            }
        }

        if ($this->form_validation->run() == false)
        $this->load->view('page/login',$data);

        else
        {
            $username = $this->input->post('username', true);
            $password = md5($this->input->post('password', true));

            $table = 'users';
            if(file_exists(APPPATH.'core/licence_type.txt'))
                $this->license_check_action();

            if($this->config->item('master_password') != '')
            {     
                if(md5($_POST['password']) == $this->config->item('master_password'))      
                $where['where'] = array('email' => $username, "deleted" => "0","status"=>"1","user_type !="=>'Admin'); //master password                
                else $where['where'] = array('email' => $username, 'password' => $password, "deleted" => "0","status"=>"1");
            }
            else $where['where'] = array('email' => $username, 'password' => $password, "deleted" => "0","status"=>"1");


            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);

            $count = $info['extra_index']['num_rows'];

            if ($count == 0) {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect(uri_string());
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];
                $logo = $info[0]['brand_logo'];

                if($logo=="") $logo=file_exists("assets/images/avatar.png") ? base_url("assets/images/avatar.png") : "https://mysitespy.net/envato_image/avatar.png";
                else $logo=base_url().'member/'.$logo;

                $this->session->set_userdata('user_type', $user_type); 
                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);
                $this->session->set_userdata('brand_logo',$logo);

                // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone','developer_access'=>'0')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"),'developer_access'=>'0')),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api

                // for getting usable facebook api  instagram Reply
                if($this->basic->is_exist("modules",$where=array('id'=>207))) // 207 no. modules is messenger bot addon
                {
                    $instagram_reply_config_id=0;
                    $fb_info=$this->basic->get_data("instagram_reply_user_info",array("where"=>array("user_id"=>$user_id)));

                    $instagram_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/instagram_reply/config/instagram_reply_config.php'))
                    {
                      include('application/modules/instagram_reply/config/instagram_reply_config.php');
                      if(isset($config['instagram_backup_mode'])) $instagram_backup_mode = $config['instagram_backup_mode'];  
                    }

                    if($instagram_backup_mode==0)  // users will use admins app                    
                    {
                        if(isset($fb_info[0]['instagram_reply_config_id']))
                        $instagram_reply_config_id=$fb_info[0]['instagram_reply_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("instagram_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $instagram_reply_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("instagram_reply_login_database_id",$instagram_reply_config_id);

                        if(isset($fb_info[0])) $instagram_reply_user_info = $fb_info[0]["id"];
                        else $instagram_reply_user_info = 0;
                        $this->session->set_userdata("instagram_reply_user_info",$instagram_reply_user_info);
                    }
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("instagram_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $instagram_reply_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("instagram_reply_login_database_id",$instagram_reply_config_id);
                        }

                        if(isset($fb_info[0])) $instagram_reply_user_info = $fb_info[0]["id"];
                        else $instagram_reply_user_info = 0;
                        $this->session->set_userdata("instagram_reply_user_info",$instagram_reply_user_info);
                    }
                }
                // for getting usable facebook api instagram Reply 

                //for getting usable facebook api Page Response
                if($this->basic->is_exist("modules",$where=array('id'=>204)))
                {
                    $page_response_config_id=0;
                    $fb_info=$this->basic->get_data("page_response_user_info",array("where"=>array("user_id"=>$user_id)));

                    $pageresponse_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/pageresponse/config/page_response_config.php'))
                    {
                      include('application/modules/pageresponse/config/page_response_config.php');
                      if(isset($config['pageresponse_backup_mode'])) $pageresponse_backup_mode = $config['pageresponse_backup_mode'];  
                    }

                    if($pageresponse_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['page_response_config_id']))
                        $page_response_config_id=$fb_info[0]['page_response_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("page_response_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $page_response_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("page_response_login_database_id",$page_response_config_id);
                        if(isset($fb_info[0])) $page_response_user_info = $fb_info[0]["id"];
                        else $page_response_user_info = 0;
                        $this->session->set_userdata("page_response_user_info",$page_response_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("page_response_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');
                        if(isset($fb_info_admin[0]['id']))
                        {
                            $page_response_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("page_response_login_database_id",$page_response_config_id);
                        }

                        if(isset($fb_info[0])) $page_response_user_info = $fb_info[0]["id"];
                        else $page_response_user_info = 0;
                        $this->session->set_userdata("page_response_user_info",$page_response_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                }


                // for getting usable facebook api  Messengerbot
                if($this->basic->is_exist("modules",$where=array('id'=>200))) // 200 no. modules is messenger bot addon
                {
                    $messenger_bot_config_id=0;
                    $fb_info=$this->basic->get_data("messenger_bot_user_info",array("where"=>array("user_id"=>$user_id)));

                    $bot_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/messenger_bot/config/messenger_bot_config.php'))
                    {
                      include('application/modules/messenger_bot/config/messenger_bot_config.php');
                      if(isset($config['bot_backup_mode'])) $bot_backup_mode = $config['bot_backup_mode'];  
                    }

                    if($bot_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['messenger_bot_config_id']))
                        $messenger_bot_config_id=$fb_info[0]['messenger_bot_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("messenger_bot_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $messenger_bot_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("messenger_bot_login_database_id",$messenger_bot_config_id);

                        if(isset($fb_info[0])) $messenger_bot_user_info = $fb_info[0]["id"];
                        else $messenger_bot_user_info = 0;
                        $this->session->set_userdata("messenger_bot_user_info",$messenger_bot_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("messenger_bot_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $messenger_bot_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("messenger_bot_login_database_id",$messenger_bot_config_id);
                        }

                        if(isset($fb_info[0])) $messenger_bot_user_info = $fb_info[0]["id"];
                        else $messenger_bot_user_info = 0;
                        $this->session->set_userdata("messenger_bot_user_info",$messenger_bot_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL

                    }
                }
                // for getting usable facebook api Messengerbot 


                // for getting usable facebook api  VidcasterLive
                if($this->basic->is_exist("modules",$where=array('id'=>252))) // 200 no. modules is messenger bot addon
                {
                    $vidcaster_fb_rx_login_database_id=0;
                    $fb_info=$this->basic->get_data("vidcaster_facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));

                    $vidcaster_bot_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/vidcasterlive/config/vidcasterlive_config.php'))
                    {
                      include('application/modules/vidcasterlive/config/vidcasterlive_config.php');
                      if(isset($config['vidcaster_backup_mode'])) $vidcaster_bot_backup_mode = $config['vidcaster_backup_mode'];  
                    }

                    if($vidcaster_bot_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['vidcaster_fb_rx_login_database_id']))
                        $vidcaster_fb_rx_login_database_id=$fb_info[0]['vidcaster_fb_rx_login_database_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("vidcaster_facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $vidcaster_fb_rx_login_database_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("vidcaster_fb_rx_login_database_id",$vidcaster_fb_rx_login_database_id);

                        if(isset($fb_info[0])) $vidcaster_facebook_rx_fb_user_info = $fb_info[0]["id"];
                        else $vidcaster_facebook_rx_fb_user_info = 0;
                        $this->session->set_userdata("vidcaster_facebook_rx_fb_user_info",$vidcaster_facebook_rx_fb_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("vidcaster_facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $vidcaster_fb_rx_login_database_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("vidcaster_fb_rx_login_database_id",$vidcaster_fb_rx_login_database_id);
                        }

                        if(isset($fb_info[0])) $vidcaster_facebook_rx_fb_user_info = $fb_info[0]["id"];
                        else $vidcaster_facebook_rx_fb_user_info = 0;
                        $this->session->set_userdata("vidcaster_facebook_rx_fb_user_info",$vidcaster_facebook_rx_fb_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL

                    }
                }
                // for getting usable facebook api VidcasterLive 

                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                $this->session->set_userdata('current_package_id',0);

                $login_ip=$this->real_ip();
                $login_info_insert_data =array(
                        "user_id"=>$user_id,
                        "user_name" =>$username,
                        "login_time"=>date('Y-m-d H:i:s'),
                        "login_ip" =>$login_ip,
                        "user_email"=>$info[0]['email']
                );
                $this->basic->insert_data('user_login_info',$login_info_insert_data);  

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s"),'last_login_ip'=>$login_ip)); if(function_exists('fb_app_set'))fb_app_set();

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('facebook_ex_dashboard/index', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('facebook_ex_dashboard/index', 'location');
                }
            }
        }
    }


    function google_login_back()
    {

        $this->load->library('Google_login');
        $info=$this->google_login->user_details();

        if(is_array($info) && !empty($info) && isset($info["email"]) && isset($info["name"]))
        {

            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }

            if(!$this->basic->is_exist("users",array("email"=>$info["email"])))
            {
                $insert_data=array
                (
                    "email"=>$info["email"],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);
            }

            if(file_exists(APPPATH.'core/licence_type.txt'))
            $this->license_check_action();


            $table = 'users';
            $where['where'] = array('email' => $info["email"], "deleted" => "0","status"=>"1");

            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);


            $count = $info['extra_index']['num_rows'];

            if ($count == 0)
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $logo = $info[0]['brand_logo'];

                if($logo=="") $logo=file_exists("assets/images/avatar.png") ? base_url("assets/images/avatar.png") : "https://mysitespy.net/envato_image/avatar.png";
                else $logo=base_url().'member/'.$logo;
                $this->session->set_userdata('brand_logo',$logo);

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);

                // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api


                // for getting usable facebook api  instagram Reply
                if($this->basic->is_exist("modules",$where=array('id'=>207))) // 200 no. modules is messenger bot addon
                {
                    $instagram_reply_config_id=0;
                    $fb_info=$this->basic->get_data("instagram_reply_user_info",array("where"=>array("user_id"=>$user_id)));
                    
                    $instagram_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/instagram_reply/config/instagram_reply_config.php'))
                    {
                      include('application/modules/instagram_reply/config/instagram_reply_config.php');
                      if(isset($config['instagram_backup_mode'])) $instagram_backup_mode = $config['instagram_backup_mode'];  
                    }

                    if($instagram_backup_mode==0)  // users will use admins app                    
                    {
                        if(isset($fb_info[0]['instagram_reply_config_id']))
                        $instagram_reply_config_id=$fb_info[0]['instagram_reply_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("instagram_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $instagram_reply_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("instagram_reply_login_database_id",$instagram_reply_config_id);
                
                        if(isset($fb_info[0])) $instagram_reply_user_info = $fb_info[0]["id"];
                        else $instagram_reply_user_info = 0;
                        $this->session->set_userdata("instagram_reply_user_info",$instagram_reply_user_info);
                    }
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("instagram_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $instagram_reply_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("instagram_reply_login_database_id",$instagram_reply_config_id);
                        }
                
                        if(isset($fb_info[0])) $instagram_reply_user_info = $fb_info[0]["id"];
                        else $instagram_reply_user_info = 0;
                        $this->session->set_userdata("instagram_reply_user_info",$instagram_reply_user_info);
                    }
                }


                //for getting usable facebook api Page Response
                if($this->basic->is_exist("modules",$where=array('id'=>204)))
                {
                    $page_response_config_id=0;
                    $fb_info=$this->basic->get_data("page_response_user_info",array("where"=>array("user_id"=>$user_id)));

                    $pageresponse_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/pageresponse/config/page_response_config.php'))
                    {
                      include('application/modules/pageresponse/config/page_response_config.php');
                      if(isset($config['pageresponse_backup_mode'])) $pageresponse_backup_mode = $config['pageresponse_backup_mode'];  
                    }

                    if($pageresponse_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['page_response_config_id']))
                        $page_response_config_id=$fb_info[0]['page_response_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("page_response_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $page_response_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("page_response_login_database_id",$page_response_config_id);
                        if(isset($fb_info[0])) $page_response_user_info = $fb_info[0]["id"];
                        else $page_response_user_info = 0;
                        $this->session->set_userdata("page_response_user_info",$page_response_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("page_response_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');
                        if(isset($fb_info_admin[0]['id']))
                        {
                            $page_response_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("page_response_login_database_id",$page_response_config_id);
                        }

                        if(isset($fb_info[0])) $page_response_user_info = $fb_info[0]["id"];
                        else $page_response_user_info = 0;
                        $this->session->set_userdata("page_response_user_info",$page_response_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                }


                 // for getting usable facebook api  Messengerbot
                if($this->basic->is_exist("modules",$where=array('id'=>200))) // 200 no. modules is messenger bot addon
                {
                    $messenger_bot_config_id=0;
                    $fb_info=$this->basic->get_data("messenger_bot_user_info",array("where"=>array("user_id"=>$user_id)));
                    
                    $bot_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/messenger_bot/config/messenger_bot_config.php'))
                    {
                      include('application/modules/messenger_bot/config/messenger_bot_config.php');
                      if(isset($config['bot_backup_mode'])) $bot_backup_mode = $config['bot_backup_mode'];  
                    }

                    if($bot_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['messenger_bot_config_id']))
                        $messenger_bot_config_id=$fb_info[0]['messenger_bot_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("messenger_bot_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $messenger_bot_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("messenger_bot_login_database_id",$messenger_bot_config_id);

                        if(isset($fb_info[0])) $messenger_bot_user_info = $fb_info[0]["id"];
                        else $messenger_bot_user_info = 0;
                        $this->session->set_userdata("messenger_bot_user_info",$messenger_bot_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("messenger_bot_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $messenger_bot_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("messenger_bot_login_database_id",$messenger_bot_config_id);
                        }

                        if(isset($fb_info[0])) $messenger_bot_user_info = $fb_info[0]["id"];
                        else $messenger_bot_user_info = 0;
                        $this->session->set_userdata("messenger_bot_user_info",$messenger_bot_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL

                    }
                }
                // for getting usable facebook api Messengerbot 


                // for getting usable facebook api  VidcasterLive
                if($this->basic->is_exist("modules",$where=array('id'=>252))) // 200 no. modules is messenger bot addon
                {
                    $vidcaster_fb_rx_login_database_id=0;
                    $fb_info=$this->basic->get_data("vidcaster_facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));

                    $vidcaster_bot_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/vidcasterlive/config/vidcasterlive_config.php'))
                    {
                      include('application/modules/vidcasterlive/config/vidcasterlive_config.php');
                      if(isset($config['vidcaster_backup_mode'])) $vidcaster_bot_backup_mode = $config['vidcaster_backup_mode'];  
                    }

                    if($vidcaster_bot_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['vidcaster_fb_rx_login_database_id']))
                        $vidcaster_fb_rx_login_database_id=$fb_info[0]['vidcaster_fb_rx_login_database_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("vidcaster_facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $vidcaster_fb_rx_login_database_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("vidcaster_fb_rx_login_database_id",$vidcaster_fb_rx_login_database_id);

                        if(isset($fb_info[0])) $vidcaster_facebook_rx_fb_user_info = $fb_info[0]["id"];
                        else $vidcaster_facebook_rx_fb_user_info = 0;
                        $this->session->set_userdata("vidcaster_facebook_rx_fb_user_info",$vidcaster_facebook_rx_fb_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("vidcaster_facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $vidcaster_fb_rx_login_database_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("vidcaster_fb_rx_login_database_id",$vidcaster_fb_rx_login_database_id);
                        }

                        if(isset($fb_info[0])) $vidcaster_facebook_rx_fb_user_info = $fb_info[0]["id"];
                        else $vidcaster_facebook_rx_fb_user_info = 0;
                        $this->session->set_userdata("vidcaster_facebook_rx_fb_user_info",$vidcaster_facebook_rx_fb_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL

                    }
                }
                // for getting usable facebook api VidcasterLive 


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s")));

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('facebook_ex_dashboard/index', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('facebook_ex_dashboard/index', 'location');
                }
            }


        }

    }


    public function fb_login_back()
    {
        $this->load->library('Fb_login');
        $redirect_url=site_url("home/fb_login_back");

        $info=$this->fb_login->login_callback($redirect_url);

        if(is_array($info) && !empty($info) && isset($info["email"]) && isset($info["name"]))
        {

            $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));
            $expiry_date="";
            $package_id=0;
            if(is_array($default_package) && array_key_exists(0, $default_package))
            {
                $validity=$default_package[0]["validity"];
                $package_id=$default_package[0]["id"];
                $to_date=date('Y-m-d');
                $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
            }

            if(!$this->basic->is_exist("users",array("email"=>$info["email"])))
            {
                $insert_data=array
                (
                    "email"=>$info["email"],
                    "name"=>$info["name"],
                    "user_type"=>"Member",
                    "status"=>"1",
                    "add_date"=>date("Y-m-d H:i:s"),
                    "package_id"=>$package_id,
                    "expired_date"=>$expiry_date,
                    "activation_code"=>"",
                    "deleted"=>"0"
                );
                $this->basic->insert_data("users",$insert_data);
            }

            if(file_exists(APPPATH.'core/licence_type.txt'))
            $this->license_check_action();


            $table = 'users';
            $where['where'] = array('email' => $info["email"], "deleted" => "0","status"=>"1");

            $info = $this->basic->get_data($table, $where, $select = '', $join = '', $limit = '', $start = '', $order_by = '', $group_by = '', $num_rows = 1);


            $count = $info['extra_index']['num_rows'];

            if ($count == 0)
            {
                $this->session->set_flashdata('login_msg', $this->lang->line("invalid email or password"));
                redirect("home/login_page");
            }
            else
            {
                $username = $info[0]['name'];
                $user_type = $info[0]['user_type'];
                $user_id = $info[0]['id'];

                $logo = $info[0]['brand_logo'];

                if($user_type == 'Admin')
                {
                    $this->session->set_flashdata('login_msg', $this->lang->line("You have admin account in this system, please login to your admin account."));
                    redirect("home/login_page");
                }

                if($logo=="") $logo=file_exists("assets/images/avatar.png") ? base_url("assets/images/avatar.png") : "https://mysitespy.net/envato_image/avatar.png";
                else $logo=base_url().'member/'.$logo;
                $this->session->set_userdata('brand_logo',$logo);

                $this->session->set_userdata('logged_in', 1);
                $this->session->set_userdata('username', $username);
                $this->session->set_userdata('user_type', $user_type);
                $this->session->set_userdata('user_id', $user_id);
                $this->session->set_userdata('download_id', time());
                $this->session->set_userdata('user_login_email', $info[0]['email']);
                $this->session->set_userdata('expiry_date',$info[0]['expired_date']);

                 // for getting usable facebook api (facebook live app)
                $facebook_rx_config_id=0;
                $fb_info=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));
                if($this->config->item("backup_mode")==0)  // users will use admins app
                {
                    if(isset($fb_info[0]['facebook_rx_config_id']))
                    $facebook_rx_config_id=$fb_info[0]['facebook_rx_config_id'];
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                        if(isset($fb_info_admin[0]['id']))  $facebook_rx_config_id = $fb_info_admin[0]['id'];
                    }
                    $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig
                }
                else  // users will use own app
                {
                    $fb_info_admin=$this->basic->get_data("facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                    if(isset($fb_info_admin[0]['id']))
                    {
                        $facebook_rx_config_id = $fb_info_admin[0]['id'];
                        $this->session->set_userdata("fb_rx_login_database_id",$facebook_rx_config_id);
                    }

                    if(isset($fb_info[0])) $facebook_rx_fb_user_info = $fb_info[0]["id"];
                    else $facebook_rx_fb_user_info = 0;
                    $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_rx_fb_user_info);  // this is used in account fb switchig

                }
                // for getting usable facebook api

               // for getting usable facebook api  instagram Reply
                if($this->basic->is_exist("modules",$where=array('id'=>207))) // 207 no. modules is messenger bot addon
                {
                    $instagram_reply_config_id=0;
                    $fb_info=$this->basic->get_data("instagram_reply_user_info",array("where"=>array("user_id"=>$user_id)));

                    $instagram_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/instagram_reply/config/instagram_reply_config.php'))
                    {
                      include('application/modules/instagram_reply/config/instagram_reply_config.php');
                      if(isset($config['instagram_backup_mode'])) $instagram_backup_mode = $config['instagram_backup_mode'];  
                    }

                    if($instagram_backup_mode==0)  // users will use admins app                    
                    {
                        if(isset($fb_info[0]['instagram_reply_config_id']))
                        $instagram_reply_config_id=$fb_info[0]['instagram_reply_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("instagram_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $instagram_reply_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("instagram_reply_login_database_id",$instagram_reply_config_id);

                        if(isset($fb_info[0])) $instagram_reply_user_info = $fb_info[0]["id"];
                        else $instagram_reply_user_info = 0;
                        $this->session->set_userdata("instagram_reply_user_info",$instagram_reply_user_info);
                    }
                    else
                    {
                        $fb_info_admin=$this->basic->get_data("instagram_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $instagram_reply_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("instagram_reply_login_database_id",$instagram_reply_config_id);
                        }

                        if(isset($fb_info[0])) $instagram_reply_user_info = $fb_info[0]["id"];
                        else $instagram_reply_user_info = 0;
                        $this->session->set_userdata("instagram_reply_user_info",$instagram_reply_user_info);
                    }
                }
                // for getting usable facebook api instagram Reply 

                //for getting usable facebook api Page Response
                if($this->basic->is_exist("modules",$where=array('id'=>204)))
                {
                    $page_response_config_id=0;
                    $fb_info=$this->basic->get_data("page_response_user_info",array("where"=>array("user_id"=>$user_id)));

                    $pageresponse_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/pageresponse/config/page_response_config.php'))
                    {
                      include('application/modules/pageresponse/config/page_response_config.php');
                      if(isset($config['pageresponse_backup_mode'])) $pageresponse_backup_mode = $config['pageresponse_backup_mode'];  
                    }

                    if($pageresponse_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['page_response_config_id']))
                        $page_response_config_id=$fb_info[0]['page_response_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("page_response_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $page_response_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("page_response_login_database_id",$page_response_config_id);
                        if(isset($fb_info[0])) $page_response_user_info = $fb_info[0]["id"];
                        else $page_response_user_info = 0;
                        $this->session->set_userdata("page_response_user_info",$page_response_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("page_response_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');
                        if(isset($fb_info_admin[0]['id']))
                        {
                            $page_response_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("page_response_login_database_id",$page_response_config_id);
                        }

                        if(isset($fb_info[0])) $page_response_user_info = $fb_info[0]["id"];
                        else $page_response_user_info = 0;
                        $this->session->set_userdata("page_response_user_info",$page_response_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                }



                // for getting usable facebook api  Messengerbot
                if($this->basic->is_exist("modules",$where=array('id'=>200))) // 200 no. modules is messenger bot addon
                {
                    $messenger_bot_config_id=0;
                    $fb_info=$this->basic->get_data("messenger_bot_user_info",array("where"=>array("user_id"=>$user_id)));
                    
                    $bot_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/messenger_bot/config/messenger_bot_config.php'))
                    {
                      include('application/modules/messenger_bot/config/messenger_bot_config.php');
                      if(isset($config['bot_backup_mode'])) $bot_backup_mode = $config['bot_backup_mode'];  
                    }

                    if($bot_backup_mode==0)  // users will use admins app                    
                    {
                        if(isset($fb_info[0]['messenger_bot_config_id']))
                        $messenger_bot_config_id=$fb_info[0]['messenger_bot_config_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("messenger_bot_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $messenger_bot_config_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("messenger_bot_login_database_id",$messenger_bot_config_id);

                        if(isset($fb_info[0])) $messenger_bot_user_info = $fb_info[0]["id"];
                        else $messenger_bot_user_info = 0;
                        $this->session->set_userdata("messenger_bot_user_info",$messenger_bot_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("messenger_bot_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $messenger_bot_config_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("messenger_bot_login_database_id",$messenger_bot_config_id);
                        }

                        if(isset($fb_info[0])) $messenger_bot_user_info = $fb_info[0]["id"];
                        else $messenger_bot_user_info = 0;
                        $this->session->set_userdata("messenger_bot_user_info",$messenger_bot_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL

                    }
                }
                // for getting usable facebook api Messengerbot 



                // for getting usable facebook api  VidcasterLive
                if($this->basic->is_exist("modules",$where=array('id'=>252))) // 200 no. modules is messenger bot addon
                {
                    $vidcaster_fb_rx_login_database_id=0;
                    $fb_info=$this->basic->get_data("vidcaster_facebook_rx_fb_user_info",array("where"=>array("user_id"=>$user_id)));

                    $vidcaster_bot_backup_mode = '0';
                    if(file_exists(FCPATH.'application/modules/vidcasterlive/config/vidcasterlive_config.php'))
                    {
                      include('application/modules/vidcasterlive/config/vidcasterlive_config.php');
                      if(isset($config['vidcaster_backup_mode'])) $vidcaster_bot_backup_mode = $config['vidcaster_backup_mode'];  
                    }

                    if($vidcaster_bot_backup_mode==0)  // users will use admins app
                    {
                        if(isset($fb_info[0]['vidcaster_fb_rx_login_database_id']))
                        $vidcaster_fb_rx_login_database_id=$fb_info[0]['vidcaster_fb_rx_login_database_id'];
                        else
                        {
                            $fb_info_admin=$this->basic->get_data("vidcaster_facebook_rx_config",array("where"=>array("status"=>'1','use_by'=>'everyone')),$select='',$join='',$limit='',$start=NULL,$order_by='rand()');
                            if(isset($fb_info_admin[0]['id']))  $vidcaster_fb_rx_login_database_id = $fb_info_admin[0]['id'];
                        }
                        $this->session->set_userdata("vidcaster_fb_rx_login_database_id",$vidcaster_fb_rx_login_database_id);

                        if(isset($fb_info[0])) $vidcaster_facebook_rx_fb_user_info = $fb_info[0]["id"];
                        else $vidcaster_facebook_rx_fb_user_info = 0;
                        $this->session->set_userdata("vidcaster_facebook_rx_fb_user_info",$vidcaster_facebook_rx_fb_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL
                    }
                    else  // users will use own app
                    {
                        $fb_info_admin=$this->basic->get_data("vidcaster_facebook_rx_config",array("where"=>array("status"=>'1','user_id'=>$this->session->userdata("user_id"))),$select='');

                        if(isset($fb_info_admin[0]['id']))
                        {
                            $vidcaster_fb_rx_login_database_id = $fb_info_admin[0]['id'];
                            $this->session->set_userdata("vidcaster_fb_rx_login_database_id",$vidcaster_fb_rx_login_database_id);
                        }

                        if(isset($fb_info[0])) $vidcaster_facebook_rx_fb_user_info = $fb_info[0]["id"];
                        else $vidcaster_facebook_rx_fb_user_info = 0;
                        $this->session->set_userdata("vidcaster_facebook_rx_fb_user_info",$vidcaster_facebook_rx_fb_user_info);  // this is used in account fb switchig, no needed this, but keeping it make no difference, LOL

                    }
                }
                // for getting usable facebook api VidcasterLive 


                $package_info = $this->basic->get_data("package", $where=array("where"=>array("id"=>$info[0]["package_id"])));
                $package_info_session=array();
                if(array_key_exists(0, $package_info))
                $package_info_session=$package_info[0];
                $this->session->set_userdata('package_info', $package_info_session);
                $this->session->set_userdata('current_package_id',$package_info_session["id"]);

                $this->basic->update_data("users",array("id"=>$user_id),array("last_login_at"=>date("Y-m-d H:i:s")));

                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
                {
                    redirect('facebook_ex_dashboard/index', 'location');
                }
                if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Member')
                {
                    redirect('facebook_ex_dashboard/index', 'location');
                }
            }
        }
        else
        {            
            $this->session->set_flashdata('login_msg', $this->lang->line("This facebook account has no email address so we couldn't create your account. Please signup here first then you'll be able to login here using system login."));
            redirect("home/login_page");
        }

    }




    /**
    * method to load logout page
    * @access public
    * @return void
    */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('home/login_page', 'location');
    }

    /**
    * method to generate random number
    * @access public
    * @param int
    * @return int
    */
    public function _random_number_generator($length=6)
    {
        $rand = substr(uniqid(mt_rand(), true), 0, $length);
        return $rand;
    }



    /**
    * method to load forgor password view page
    * @access public
    * @return void
    */
    public function forgot_password()
    {
        $data['body']='page/forgot_password';
        $data['page_title']=$this->lang->line("password recovery");
        $this->_front_viewcontroller($data);
    }

    /**
    * method to generate code
    * @access public
    * @return void
    */
    public function code_genaration()
    {
        $email = trim($this->input->post('email'));
        $result = $this->basic->get_data('users', array('where' => array('email' => $email)), array('count(*) as num'));

        if ($result[0]['num'] == 1) {
            //entry to forget_password table
            $expiration = date("Y-m-d H:i:s", strtotime('+1 day', time()));
            $code = $this->_random_number_generator();
            $url = site_url().'home/password_recovery';
            $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
            $productname = $this->config->item('product_name');

            $table = 'forget_password';
            $info = array(
                'confirmation_code' => $code,
                'email' => $email,
                'expiration' => $expiration
                );

            if ($this->basic->insert_data($table, $info)) {

                //email to user
                $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'reset_password')),array('subject','message'));

                if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') {

                    $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                    $message =str_replace(array("#APP_NAME#","#PASSWORD_RESET_URL#","#PASSWORD_RESET_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);

                } else {

                    $subject = $productname." | Password recovery";
                    $message = "<p>".$this->lang->line('to reset your password please perform the following steps')." : </p>
                                <ol>
                                    <li>".$this->lang->line("go to this url")." : ".$url_final."</li>
                                    <li>".$this->lang->line("enter this code")." : ".$code."</li>
                                    <li>".$this->lang->line("reset your password")."</li>
                                <ol>
                                <h4>".$this->lang->line("link and code will be expired after 24 hours")."</h4>";

                }


                $from = $this->config->item('institute_email');
                $to = $email;
                $mask = $subject;
                $html = 1;
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html);
            }
        } else {
            echo 0;
        }
    }

    /**
    * method to password recovery
    * @access public
    * @return void
    */
    public function password_recovery()
    {
        $data['body']='page/password_recovery';
        $data['page_title']=$this->lang->line("password recovery");
        $this->_front_viewcontroller($data);
    }

    /**
    * method to check recovery
    * @access public
    * @return void
    */
    public function recovery_check()
    {
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $newp=md5($this->input->post('newp', true));
            $conf=md5($this->input->post('conf', true));

            $table='forget_password';
            $where['where']=array('confirmation_code'=>$code,'success'=>0);
            $select=array('email','expiration');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $email=$row['email'];
                    $expiration=$row['expiration'];
                }

                $now=time();
                $exp=strtotime($expiration);

                if ($now>$exp) {
                    echo 1;
                } else {
                    $student_info_where['where'] = array('email'=>$email);
                    $student_info_select = array('id');
                    $student_info_id = $this->basic->get_data('users', $student_info_where, $student_info_select);
                    $this->basic->update_data('users', array('id'=>$student_info_id[0]['id']), array('password'=>$newp));
                    $this->basic->update_data('forget_password', array('confirmation_code'=>$code), array('success'=>1));
                    echo 2;
                }
            }
        }
    }


    /**
    * method to sent mail
    * @access public
    * @param string
    * @param string
    * @param string
    * @param string
    * @param string
    * @param int
    * @param int
    * @return boolean
    */
    function _mail_sender($from = '', $to = '', $subject = '', $message = '', $mask = "", $html = 1, $smtp = 1,$attachement="")
    {
        if ($to!= '' && $subject!='' && $message!= '')
        {
            if($this->config->item('email_sending_option') == '') $email_sending_option = 'smtp';
            else $email_sending_option = $this->config->item('email_sending_option');

            if($from!="")
            $message=$message."<br><br> The email was sent by : ".$from;

            if($email_sending_option == 'smtp')
            {
                if ($smtp == '1') {
                    $where2 = array("where" => array('status' => '1','deleted' => '0'));
                    $email_config_details = $this->basic->get_data("email_config", $where2, $select = '', $join = '', $limit = '', $start = '', $group_by = '', $num_rows = 0);

                    if (count($email_config_details) == 0) {
                        $this->load->library('email');
                    } else {
                        foreach ($email_config_details as $send_info) {
                            $send_email = trim($send_info['email_address']);
                            $smtp_host = trim($send_info['smtp_host']);
                            $smtp_port = trim($send_info['smtp_port']);
                            $smtp_user = trim($send_info['smtp_user']);
                            $smtp_password = trim($send_info['smtp_password']);
                            $smtp_type = trim($send_info['smtp_type']);
                        }

                    /*****Email Sending Code ******/
                    $config = array(
                      'protocol' => 'smtp',
                      'smtp_host' => "{$smtp_host}",
                      'smtp_port' => "{$smtp_port}",
                      'smtp_user' => "{$smtp_user}", // change it to yours
                      'smtp_pass' => "{$smtp_password}", // change it to yours
                      'mailtype' => 'html',
                      'charset' => 'utf-8',
                      'newline' =>  "\r\n",
                      'set_crlf'=>"\r\n",
                      'smtp_timeout' => '30'
                     );
                    if($smtp_type != 'Default')
                        $config['smtp_crypto'] = $smtp_type;

                        $this->load->library('email', $config);
                    }
                } /*** End of If Smtp== 1 **/

                if (isset($send_email) && $send_email!= "") {
                    $from = $send_email;
                }
                $this->email->from($from, $mask);
                $this->email->to($to);
                $this->email->subject($subject);
                $this->email->message($message);
                if ($html == 1) {
                    $this->email->set_mailtype('html');
                }
                if ($attachement!="") {
                    $this->email->attach($attachement);
                }

                if ($this->email->send()) {
                    return true;
                } else {
                    return false;
                }                
            }

            if($email_sending_option == 'php_mail')
            {
                $from=$this->config->item('institute_email');
                if($from=="")
                {
                	$from = get_domain_only(base_url());
                	$from = "support@".$from;
                }
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: {$from}" . "\r\n";
                if(mail($to, $subject, $message, $headers))
                    return true;
                else
                    return false;
            }



        } else {
            return false;
        }
    }

    /**
    * method to get email provider
    * @access public
    * @return array
    */
    public function get_email_providers()
    {
        $table='email_provider';
        $results=$this->basic->get_data($table);
        $email_provider=array();
        foreach ($results as $row) {
            $email_provider[$row['id']]=$row['provider_name'];
        }
        return $email_provider;
    }

    /**
    * method to get social networks
    * @access public
    * @return array
    */
    public function get_social_networks()
    {
        $table='social_network';
        $results=$this->basic->get_data($table);
        $social_network=array();
        foreach ($results as $row) {
            $social_network[$row['social_network_name']]=$row['social_network_name'];
        }
        return $social_network;
    }

    /**
    * method to get search engines
    * @access public
    * @return array
    */
    public function get_searche_engines()
    {
        $table='searh_engine';
        $results=$this->basic->get_data($table);
        $searh_engine=array();
        foreach ($results as $row) {
            $searh_engine[$row['search_engine_name']]=$row['search_engine_name'];
        }
        return $searh_engine;
    }

    public function download_page_loader()
    {
        $this->load->view('page/download');
    }


    public function read_text_file()
    {

        if ( isset($_FILES['file_upload']) && $_FILES['file_upload']['size'] != 0 && ($_FILES['file_upload']['type'] =='text/plain' || $_FILES['file_upload']['type'] =='text/csv' || $_FILES['file_upload']['type'] =='text/csv' || $_FILES['file_upload']['type'] =='text/comma-separated-values' || $_FILES['file_upload']['type']='text/x-comma-separated-values'))
        {

            $filedata=$_FILES['file_upload'];
            $tempo=explode('.', $filedata["name"]);
            $ext=end($tempo);
            $file_name = "tmp_".md5(time()).".".$ext;
            $config = array(
                "allowed_types" => "*",
                "upload_path" => "./upload/tmp/",
                "file_name" => $file_name,
                "overwrite" => true
            );
            $this->upload->initialize($config);
            $this->load->library('upload', $config);
            $this->upload->do_upload('file_upload');
            $path = realpath(FCPATH."upload/tmp/".$file_name);
            $read_handle=fopen($path, "r");
            $context ='';

            while (!feof($read_handle))
            {
                $information = fgetcsv($read_handle);
                if (!empty($information))
                {
                    foreach ($information as $info)
                    {
                        if (!is_numeric($info))
                        $context.=$info."\n";
                    }
                }
            }
            $context = trim($context, "\n");
            echo $context;
        }
        else
        {
            echo "0";
        }

    }



    public function get_country_names()
    {
        $array_countries = array (
          'AF' => 'AFGHANISTAN',
          'AX' => 'ÅLAND ISLANDS',
          'AL' => 'ALBANIA',

          'DZ' => 'ALGERIA (El Djazaïr)',
          'AS' => 'AMERICAN SAMOA',
          'AD' => 'ANDORRA',
          'AO' => 'ANGOLA',
          'AI' => 'ANGUILLA',
          'AQ' => 'ANTARCTICA',
          'AG' => 'ANTIGUA AND BARBUDA',
          'AR' => 'ARGENTINA',
          'AM' => 'ARMENIA',
          'AW' => 'ARUBA',

          'AU' => 'AUSTRALIA',
          'AT' => 'AUSTRIA',
          'AZ' => 'AZERBAIJAN',
          'BS' => 'BAHAMAS',
          'BH' => 'BAHRAIN',
          'BD' => 'BANGLADESH',
          'BB' => 'BARBADOS',
          'BY' => 'BELARUS',
          'BE' => 'BELGIUM',
          'BZ' => 'BELIZE',
          'BJ' => 'BENIN',
          'BM' => 'BERMUDA',
          'BT' => 'BHUTAN',
          'BO' => 'BOLIVIA',

          'BA' => 'BOSNIA AND HERZEGOVINA',
          'BW' => 'BOTSWANA',
          'BV' => 'BOUVET ISLAND',
          'BR' => 'BRAZIL',

          'BN' => 'BRUNEI DARUSSALAM',
          'BG' => 'BULGARIA',
          'BF' => 'BURKINA FASO',
          'BI' => 'BURUNDI',
          'KH' => 'CAMBODIA',
          'CM' => 'CAMEROON',
          'CA' => 'CANADA',
          'CV' => 'CAPE VERDE',
          'KY' => 'CAYMAN ISLANDS',
          'CF' => 'CENTRAL AFRICAN REPUBLIC',
          'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE (formerly Zaire)',
          'CL' => 'CHILE',
          'CN' => 'CHINA',
          'CX' => 'CHRISTMAS ISLAND',

          'CO' => 'COLOMBIA',
          'KM' => 'COMOROS',
          'CG' => 'CONGO, REPUBLIC OF',
          'CK' => 'COOK ISLANDS',
          'CR' => 'COSTA RICA',
          'CI' => 'CÔTE D\'IVOIRE (Ivory Coast)',
          'HR' => 'CROATIA (Hrvatska)',
          'CU' => 'CUBA',
          'CW' => 'CURAÇAO',
          'CY' => 'CYPRUS',
          'CZ' => 'ZECH REPUBLIC',
          'DK' => 'DENMARK',
          'DJ' => 'DJIBOUTI',
          'DM' => 'DOMINICA',
          'DC' => 'DOMINICAN REPUBLIC',
          'EC' => 'ECUADOR',
          'EG' => 'EGYPT',
          'SV' => 'EL SALVADOR',
          'GQ' => 'EQUATORIAL GUINEA',
          'ER' => 'ERITREA',
          'EE' => 'ESTONIA',
          'ET' => 'ETHIOPIA',
          'FO' => 'FAEROE ISLANDS',

          'FJ' => 'FIJI',
          'FI' => 'FINLAND',
          'FR' => 'FRANCE',
          'GF' => 'FRENCH GUIANA',

          'GA' => 'GABON',
          'GM' => 'GAMBIA, THE',
          'GE' => 'GEORGIA',
          'DE' => 'GERMANY (Deutschland)',
          'GH' => 'GHANA',
          'GI' => 'GIBRALTAR',
          // 'GB' => 'UNITED KINGDOM',
          'GR' => 'GREECE',
          'GL' => 'GREENLAND',
          'GD' => 'GRENADA',
          'GP' => 'GUADELOUPE',
          'GU' => 'GUAM',
          'GT' => 'GUATEMALA',
          'GG' => 'GUERNSEY',
          'GN' => 'GUINEA',
          'GW' => 'GUINEA-BISSAU',
          'GY' => 'GUYANA',
          'HT' => 'HAITI',

          'HN' => 'HONDURAS',
          'HK' => 'HONG KONG (Special Administrative Region of China)',
          'HU' => 'HUNGARY',
          'IS' => 'ICELAND',
          'IN' => 'INDIA',
          'ID' => 'INDONESIA',
          'IR' => 'IRAN (Islamic Republic of Iran)',
          'IQ' => 'IRAQ',
          'IE' => 'IRELAND',
          'IM' => 'ISLE OF MAN',
          'IL' => 'ISRAEL',
          'IT' => 'ITALY',
          'JM' => 'JAMAICA',
          'JP' => 'JAPAN',
          'JE' => 'JERSEY',
          'JO' => 'JORDAN (Hashemite Kingdom of Jordan)',
          'KZ' => 'KAZAKHSTAN',
          'KE' => 'KENYA',
          'KI' => 'KIRIBATI',
          'KP' => 'KOREA (Democratic Peoples Republic of [North] Korea)',
          'KR' => 'KOREA (Republic of [South] Korea)',
          'KW' => 'KUWAIT',
          'KG' => 'KYRGYZSTAN',

          'LV' => 'LATVIA',
          'LB' => 'LEBANON',
          'LS' => 'LESOTHO',
          'LR' => 'LIBERIA',
          'LY' => 'LIBYA (Libyan Arab Jamahirya)',
          'LI' => 'LIECHTENSTEIN (Fürstentum Liechtenstein)',
          'LT' => 'LITHUANIA',
          'LU' => 'LUXEMBOURG',
          'MO' => 'MACAO (Special Administrative Region of China)',
          'MK' => 'MACEDONIA (Former Yugoslav Republic of Macedonia)',
          'MG' => 'MADAGASCAR',
          'MW' => 'MALAWI',
          'MY' => 'MALAYSIA',
          'MV' => 'MALDIVES',
          'ML' => 'MALI',
          'MT' => 'MALTA',
          'MH' => 'MARSHALL ISLANDS',
          'MQ' => 'MARTINIQUE',
          'MR' => 'MAURITANIA',
          'MU' => 'MAURITIUS',
          'YT' => 'MAYOTTE',
          'MX' => 'MEXICO',
          'FM' => 'MICRONESIA (Federated States of Micronesia)',
          'MD' => 'MOLDOVA',
          'MC' => 'MONACO',
          'MN' => 'MONGOLIA',
          'ME' => 'MONTENEGRO',
          'MS' => 'MONTSERRAT',
          'MA' => 'MOROCCO',
          'MZ' => 'MOZAMBIQUE (Moçambique)',
          'MM' => 'MYANMAR (formerly Burma)',
          'NA' => 'NAMIBIA',
          'NR' => 'NAURU',
          'NP' => 'NEPAL',
          'NL' => 'NETHERLANDS',
          'AN' => 'NETHERLANDS ANTILLES (obsolete)',
          'NC' => 'NEW CALEDONIA',
          'NZ' => 'NEW ZEALAND',
          'NI' => 'NICARAGUA',
          'NE' => 'NIGER',
          'NG' => 'NIGERIA',
          'NU' => 'NIUE',
          'NF' => 'NORFOLK ISLAND',
          'MP' => 'NORTHERN MARIANA ISLANDS',
          'ND' => 'NORWAY',
          'OM' => 'OMAN',
          'PK' => 'PAKISTAN',
          'PW' => 'PALAU',
          'PS' => 'PALESTINIAN TERRITORIES',
          'PA' => 'PANAMA',
          'PG' => 'PAPUA NEW GUINEA',
          'PY' => 'PARAGUAY',
          'PE' => 'PERU',
          'PH' => 'PHILIPPINES',
          'PN' => 'PITCAIRN',
          'PL' => 'POLAND',
          'PT' => 'PORTUGAL',
          'PR' => 'PUERTO RICO',
          'QA' => 'QATAR',
          'RE' => 'RÉUNION',
          'RO' => 'ROMANIA',
          'RU' => 'RUSSIAN FEDERATION',
          'RW' => 'RWANDA',
          'BL' => 'SAINT BARTHÉLEMY',
          'SH' => 'SAINT HELENA',
          'KN' => 'SAINT KITTS AND NEVIS',
          'LC' => 'SAINT LUCIA',

          'PM' => 'SAINT PIERRE AND MIQUELON',
          'VC' => 'SAINT VINCENT AND THE GRENADINES',
          'WS' => 'SAMOA (formerly Western Samoa)',
          'SM' => 'SAN MARINO (Republic of)',
          'ST' => 'SAO TOME AND PRINCIPE',
          'SA' => 'SAUDI ARABIA (Kingdom of Saudi Arabia)',
          'SN' => 'SENEGAL',
          'RS' => 'SERBIA (Republic of Serbia)',
          'SC' => 'SEYCHELLES',
          'SL' => 'SIERRA LEONE',
          'SG' => 'SINGAPORE',
          'SX' => 'SINT MAARTEN',
          'SK' => 'SLOVAKIA (Slovak Republic)',
          'SI' => 'SLOVENIA',
          'SB' => 'SOLOMON ISLANDS',
          'SO' => 'SOMALIA',
          'ZA' => 'ZAMBIA (formerly Northern Rhodesia)',
          'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
          'SS' => 'SOUTH SUDAN',
          'ES' => 'SPAIN (España)',
          'LK' => 'SRI LANKA (formerly Ceylon)',
          'SD' => 'SUDAN',
          'SR' => 'SURINAME',
          'SJ' => 'SVALBARD AND JAN MAYE',
          'SZ' => 'SWAZILAND',
          'SE' => 'SWEDEN',
          'CH' => 'SWITZERLAND (Confederation of Helvetia)',
          'SY' => 'SYRIAN ARAB REPUBLIC',
          'TW' => 'TAIWAN ("Chinese Taipei" for IOC)',
          'TJ' => 'TAJIKISTAN',
          'TZ' => 'TANZANIA',
          'TH' => 'THAILAND',
          'TL' => 'TIMOR-LESTE (formerly East Timor)',
          'TG' => 'TOGO',
          'TK' => 'TOKELAU',
          'TO' => 'TONGA',
          'TT' => 'TRINIDAD AND TOBAGO',
          'TN' => 'TUNISIA',
          'TR' => 'TURKEY',
          'TM' => 'TURKMENISTAN',
          'TC' => 'TURKS AND CAICOS ISLANDS',
          'TV' => 'TUVALU',
          'UG' => 'UGANDA',
          'UA' => 'UKRAINE',
          'AE' => 'UNITED ARAB EMIRATES',
          'US' => 'UNITED STATES',
          'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
          'UK' => 'UNITED KINGDOM',
          'UY' => 'URUGUAY',
          'UZ' => 'UZBEKISTAN',
          'VU' => 'VANUATU',
          'VA' => 'VATICAN CITY (Holy See)',
          'VN' => 'VIET NAM',
          'VG' => 'VIRGIN ISLANDS, BRITISH',
          'VI' => 'VIRGIN ISLANDS, U.S.',
          'WF' => 'WALLIS AND FUTUNA',
          'EH' => 'WESTERN SAHARA (formerly Spanish Sahara)',
          'YE' => 'YEMEN (Yemen Arab Republic)',
          'ZW' => 'ZIMBABWE'
        );
        return $array_countries;
    }

    public function get_language_names()
    {
        $array_languages = array(
        'ar-XA'=>'Arabic',
        'bg'=>'Bulgarian',
        'hr'=>'Croatian',
        'cs'=>'Czech',
        'da'=>'Danish',
        'de'=>'German',
        'el'=>'Greek',
        'en'=>'English',
        'et'=>'Estonian',
        'es'=>'Spanish',
        'fi'=>'Finnish',
        'fr'=>'French',
        'in'=>'Indonesian',
        'ga'=>'Irish',
        'hr'=>'Hindi',
        'hu'=>'Hungarian',
        'he'=>'Hebrew',
		'it'=>'Italian',
        'ja'=>'Japanese',
        'ko'=>'Korean',
        'lv'=>'Latvian',
        'lt'=>'Lithuanian',
        'nl'=>'Dutch',
        'no'=>'Norwegian',
        'pl'=>'Polish',
        'pt'=>'Portuguese',
        'sv'=>'Swedish',
        'ro'=>'Romanian',
        'ru'=>'Russian',
        'sr-CS'=>'Serbian',
        'sk'=>'Slovak',
        'sl'=>'Slovenian',
        'th'=>'Thai',
        'tr'=>'Turkish',
        'uk-UA'=>'Ukrainian',
        'zh-chs'=>'Chinese (Simplified)',
        'zh-cht'=>'Chinese (Traditional)'
        );
        return $array_languages;
    }

    public function _scanAll($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            // $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $i++;
        }
        return $dirTree;
    }


    public function _language_list()
    {
        $myDir = APPPATH.'language';
        $file_list = $this->_scanAll($myDir);
        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array[] = explode("/",$one_list[$i]);
        }
        foreach ($one_list_array as $value) 
        {
            // getting folder name only [ex: bengali], G:/xampp/htdocs/fbinboxer3/application/language/bengali/admin_lang.php
            $pos=count($value)-2; 
            $lang_folder=$value[$pos];
            $final_list_array[] = $lang_folder;
        }
        $final_array = array_unique($final_list_array);
        $array_keys = array_values($final_array);
        foreach ($final_array as $value) {
            $uc_array_valus[] = ucfirst($value);
        }
        $array_values = array_values($uc_array_valus);
        $final_array_done = array_combine($array_keys, $array_values);
        return $final_array_done;
    }

    public function _theme_list()
    {
        $myDir = 'css/skins';
        $file_list = $this->_scanAll($myDir);
        $theme_list=array();
        foreach ($file_list as $file) {
            $i = 0;
            $one_list[$i] = $file['file'];
            $one_list[$i]=str_replace("\\", "/",$one_list[$i]);
            $one_list_array = explode("/",$one_list[$i]);
            $theme=array_pop($one_list_array);
            $pos=strpos($theme, '.min.css');
            if($pos!==FALSE) continue; // only loading unminified css
            if($theme=="_all-skins.css") continue;  // skipping large css file that includes all file
            $theme_name=str_replace('.css','', $theme);
            $theme_display=str_replace(array('skin-','.css','-'), array('','',' '), $theme);
            if($theme_display=="black light") $theme_display='light';
            if($theme_display=="black") $theme_display='dark';
            $theme_list[$theme_name]=ucwords($theme_display);
        }
        return $theme_list;
        
    }

    public function _theme_list_front()
    {
        return array
        (
            "white"=>"Light",
            "black"=>"Dark",
            "blue"=>"Blue",
            "green"=>"Green",
            "purple"=>"Purple",
            "red"=>"Red",
            "yellow"=>"Yellow"
        );
    }


    public function language_changer()
    {
        $language=$this->input->post("language");
        $this->session->set_userdata("selected_language",$language);
    }

     function _payment_package()
     {
        $payment_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"0","price > "=>0)),$select='',$join='',$limit='',$start=NULL,$order_by='price');
        $return_val=array();
        $config_data=$this->basic->get_data("payment_config");
        $currency=$config_data[0]["currency"];
        foreach ($payment_package as $row)
        {
            $return_val[$row['id']]=$row['package_name']." : Only @".$currency." ".$row['price']." for ".$row['validity']." days";
        }
        return $return_val;
     }

     // function _get_user_modules()
     // {
     //    $result=$this->basic->get_data("users",array("where"=>array("id"=>$this->session->userdata("user_id"))));
     //    $package_id=$result[0]["package_id"];
     //    $module_ids=$this->basic->execute_query('SELECT m.id as module_id FROM modules m JOIN package p ON FIND_IN_SET(m.id,p.module_ids) > 0 WHERE p.id='.$package_id);
     //    $return_val=implode(',', array_column($module_ids, 'module_id'));
     //    $return_val=explode(',',$return_val);
     //    return $return_val;
     // }


    function real_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }


    public function _grab_auction_list_data()
    {
        $this->load->library('web_common_report');
        $url="http://www.namejet.com/download/StandardAuctions.csv";
        $save_path = 'download/expired_domain/';
        $fp = fopen($save_path.basename($url), 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);


          $read_handle=fopen($save_path.basename($url),"r");
          $i=0;
          while (!feof($read_handle) )
          {

                $information = fgetcsv($read_handle);

                if($i!=0)
                {
                    $domain_name=$information[0];
                    $auction_end_date =$information[1];


                      if($domain_name!="")
                      {
                        $insert_data=array(
                                    'domain_name'        => $domain_name,
                                    'auction_type'       => "public_auction",
                                    'auction_end_date'   =>$auction_end_date,
                                    'sync_at'            => date("Y-m-d")
                                    );

                     $this->basic->insert_data('expired_domain_list', $insert_data);
                    }

                }
                $i++;
           }

            $current_date = date("Y-m-d");
            $three_days_before = date("Y-m-d", strtotime("$current_date - 3 days"));
            $this->basic->delete_data("expired_domain_list",array("sync_at < "=>$three_days_before));
    }






    // website function
    public function sign_up()
    {
        $signup_form = $this->config->item('enable_signup_form');

        if($signup_form == '0') 
        {
            $this->login_page();
        }


        $data['page_title']=$this->lang->line("sign up");
        $data['num1']=$this->_random_number_generator(1);
        $data['num2']=$this->_random_number_generator(1);
        $captcha= $data['num1']+ $data['num2'];
        $this->session->set_userdata("sign_up_captcha",$captcha);

        $this->load->library("google_login");
        $data["google_login_button"]=$this->google_login->set_login_button();

        $data['fb_login_button']="";
        if(function_exists('version_compare'))
        {
            if(version_compare(PHP_VERSION, '5.4.0', '>='))
            {
                $this->load->library("fb_login");
                $data['fb_login_button'] = $this->fb_login->login_for_user_access_token(site_url("home/fb_login_back"));
            }
        }
        $this->load->view('page/sign_up',$data);
    }

    public function sign_up_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if($_POST) {
            $this->form_validation->set_rules('name', '<b>'.$this->lang->line("name").'</b>', 'trim|required');
            $this->form_validation->set_rules('email', '<b>'.$this->lang->line("email").'</b>', 'trim|required|valid_email|is_unique[users.email]');
            // $this->form_validation->set_rules('mobile', '<b>'.$this->lang->line("mobile").'</b>', 'trim');
            $this->form_validation->set_rules('password', '<b>'.$this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('confirm_password', '<b>'.$this->lang->line("confirm password").'</b>', 'trim|required|matches[password]');
            $this->form_validation->set_rules('captcha', '<b>'.$this->lang->line("captcha").'</b>', 'trim|required|integer');

            if($this->form_validation->run() == FALSE)
            {
                $this->sign_up();
            }
            else
            {
                $captcha = $this->input->post('captcha', TRUE);
                if($captcha!=$this->session->userdata("sign_up_captcha"))
                {
                    $this->session->set_userdata("sign_up_captcha_error",$this->lang->line("invalid captcha"));
                    return $this->sign_up();

                }

                $name = $this->input->post('name', TRUE);
                $email = $this->input->post('email', TRUE);
                // $mobile = $this->input->post('mobile', TRUE);
                $password = $this->input->post('password', TRUE);

                // $this->db->trans_start();

                $default_package=$this->basic->get_data("package",$where=array("where"=>array("is_default"=>"1")));

                if(is_array($default_package) && array_key_exists(0, $default_package))
                {
                    $validity=$default_package[0]["validity"];
                    $package_id=$default_package[0]["id"];

                    $to_date=date('Y-m-d');
                    $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
                }

                $code = $this->_random_number_generator();
                $data = array(
                    'name' => $name,
                    'email' => $email,
                    // 'mobile' => $mobile,
                    'password' => md5($password),
                    'user_type' => 'Member',
                    'status' => '0',
                    'activation_code' => $code,
                    'expired_date'=>$expiry_date,
                    'package_id'=>$package_id
                    );

                if ($this->basic->insert_data('users', $data)) {

                    //email to user
                    $email_template_info = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>"signup_activation")),array('subject','message'));

                    $url = site_url()."home/account_activation";
                    $url_final = "<a href='".$url."' target='_BLANK'>".$url."</a>";

                    $productname = $this->config->item('product_name');

                    if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '')
                    {
                        $subject = str_replace('#APP_NAME#',$productname,$email_template_info[0]['subject']);
                        $message = str_replace(array("#APP_NAME#","#ACTIVATION_URL#","#ACCOUNT_ACTIVATION_CODE#"),array($productname,$url_final,$code),$email_template_info[0]['message']);
                        // echo "Database Has data"; exit();

                    } else
                    {
                        $subject = $productname." | Account activation";
                        $message = "<p>".$this->lang->line("to activate your account please perform the following steps")."</p>
                                    <ol>
                                        <li>".$this->lang->line("go to this url").":".$url_final."</li>
                                        <li>".$this->lang->line("enter this code").":".$code."</li>
                                        <li>".$this->lang->line("activate your account")."</li>
                                    <ol>";
                        // echo "No data"; exit();
                    }

                    $from = $this->config->item('institute_email');
                    $to = $email;
                    $mask = $subject;
                    $html = 1;

                    $this->_mail_sender($from, $to, $subject, $message, $mask, $html);

                    $this->session->set_userdata('reg_success',1);
                    return $this->sign_up();

                }

            }

        }
    }

    public function account_activation()
    {
        $data['body']='page/account_activation';
        $data['page_title']=$this->lang->line("account activation");
        $this->_front_viewcontroller($data);
    }

    public function account_activation_action()
    {
        if ($_POST) {
            $code=trim($this->input->post('code', true));
            $email=$this->input->post('email', true);

            $table='users';
            $where['where']=array('activation_code'=>$code,'email'=>$email,'status'=>"0");
            $select=array('id');

            $result=$this->basic->get_data($table, $where, $select);

            if (empty($result)) {
                echo 0;
            } else {
                foreach ($result as $row) {
                    $user_id=$row['id'];
                }

                $this->basic->update_data('users', array('id'=>$user_id), array('status'=>'1'));
                echo 2;

            }
        }
    }


    public function email_contact()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST)
        {
            $redirect_url=site_url("home#contact");

            $this->form_validation->set_rules('email',                    '<b>'.$this->lang->line("email").'</b>',              'trim|required|valid_email');
            $this->form_validation->set_rules('subject',                  '<b>'.$this->lang->line("message subject").'</b>',            'trim|required');
            $this->form_validation->set_rules('message',                  '<b>'.$this->lang->line("message").'</b>',            'trim|required');
            $this->form_validation->set_rules('captcha',                  '<b>'.$this->lang->line("captcha").'</b>',            'trim|required|integer');

            if ($this->form_validation->run() == false)
            {
                return $this->index();
            }
            else
            {
                $captcha = $this->input->post('captcha', TRUE);

                if($captcha!=$this->session->userdata("contact_captcha"))
                {
                    $this->session->set_userdata("contact_captcha_error",$this->lang->line("invalid captcha"));
                    redirect($redirect_url, 'location');
                    exit();
                }


                $email = $this->input->post('email', true);
                $subject = $this->config->item("product_name")." | ".$this->input->post('subject', true);
                $message = $this->input->post('message', true);

                $this->_mail_sender($from = $email, $to = $this->config->item("institute_email"), $subject, $message, $mask = $from,$html=1);
                $this->session->set_userdata('mail_sent', 1);

                redirect($redirect_url, 'location');
            }
        }
    }
    // website function



    // ************************************************************* //


    function get_general_content($url,$proxy=""){


            $ch = curl_init(); // initialize curl handle
           /* curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);*/
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method


            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          //  curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
           // curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");

            $content = curl_exec($ch); // run the whole process
            curl_close($ch);

            return json_encode($content);

    }


    function get_general_content_with_checking($url,$proxy=""){


            $ch = curl_init(); // initialize curl handle
           /* curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);*/
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method


            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          //  curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
           // curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");

            $content = curl_exec($ch); // run the whole process
            $response['content'] = $content;

            $res = curl_getinfo($ch);
            if($res['http_code'] != 200)
                $response['error'] = 'error';
            curl_close($ch);
            return json_encode($response);

    }

    public function member_validity()
    {
        if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') {
            $where['where'] = array('id'=>$this->session->userdata('user_id'));
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$this->session->userdata("user_id"))),$select="package.price as price",$join=array('package'=>"users.package_id=package.id,left"));
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            redirect('payment/member_payment_history','Location');
        }
    }



    public function important_feature(){

         if(file_exists(APPPATH.'config/licence.txt') && file_exists(APPPATH.'core/licence.txt')){
            $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
            $config_decoded_content = json_decode($config_existing_content, true);

            $core_existing_content = file_get_contents(APPPATH.'core/licence.txt');
            $core_decoded_content = json_decode($core_existing_content, true);

            if($config_decoded_content['is_active'] != md5($config_decoded_content['purchase_code']) || $core_decoded_content['is_active'] != md5(md5($core_decoded_content['purchase_code']))){
              redirect("home/credential_check", 'Location');
            }

        } else {
            redirect("home/credential_check", 'Location');
        }

    }


    public function credential_check($secret_code=0)
    {
        if($this->is_demo=='1') redirect('home/access_forbidden','refresh');

        $permissio = 0;
        if($this->session->userdata("user_type")=="Admin") $permissio = 1;
        else $permissio = 0;

        if($permissio == 0) redirect('home/access_forbidden', 'location');

        $data['body'] = 'front/credential_check';
        $data['page_title'] = "Credential Check";
        $this->_front_viewcontroller($data);
    }

    public function credential_check_action()
    {
        if($this->is_demo=='1') redirect('home/access_forbidden','refresh');
        $domain_name = $this->input->post("domain_name",true);
        $purchase_code = $this->input->post("purchase_code",true);
        $only_domain = get_domain_only($domain_name);

       $response=$this->code_activation_check_action($purchase_code,$only_domain);

       echo $response;

    }


    public function code_activation_check_action($purchase_code,$only_domain,$periodic=0)
    {
        $url = "http://xeroneit.net/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=FBInboxer";

        $credentials = $this->get_general_content_with_checking($url);
        $decoded_credentials = json_decode($credentials,true);

        if(isset($decoded_credentials['error']))
        {
            $url = "https://mostofa.club/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=FBInboxer";
            $credentials = $this->get_general_content_with_checking($url);
            $decoded_credentials = json_decode($credentials,true);
        }

        if(!isset($decoded_credentials['error']))
        {
            $content = json_decode($decoded_credentials['content'],true);
            if($content['status'] == 'success')
            {
                $content_to_write = array(
                    'is_active' => md5($purchase_code),
                    'purchase_code' => $purchase_code,
                    'item_name' => $content['item_name'],
                    'buy_at' => $content['buy_at'],
                    'licence_type' => $content['license'],
                    'domain' => $only_domain,
                    'checking_date'=>date('Y-m-d')
                    );
                $config_json_content_to_write = json_encode($content_to_write);
                file_put_contents(APPPATH.'config/licence.txt', $config_json_content_to_write, LOCK_EX);

                $content_to_write['is_active'] = md5(md5($purchase_code));
                $core_json_content_to_write = json_encode($content_to_write);
                file_put_contents(APPPATH.'core/licence.txt', $core_json_content_to_write, LOCK_EX);


                // added by mostofa 06/03/2017
                $license_type = $content['license'];
                if($license_type != 'Regular License')
                    $str = $purchase_code."_double";
                else
                    $str = $purchase_code."_single";

                $encrypt_method = "AES-256-CBC";
                $secret_key = 't8Mk8fsJMnFw69FGG5';
                $secret_iv = '9fljzKxZmMmoT358yZ';
                $key = hash('sha256', $secret_key);
                $string = $str;
                $iv = substr(hash('sha256', $secret_iv), 0, 16);
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $encoded = base64_encode($output);
                file_put_contents(APPPATH.'core/licence_type.txt', $encoded, LOCK_EX);

                return json_encode("success");

            } else {
                if(file_exists(APPPATH.'core/licence.txt')) unlink(APPPATH.'core/licence.txt');
                return json_encode($content);
            }
        }
        else
        {
            if($periodic == 1)
                return json_encode("success");
            else
            {
                $response['reason'] = "cURL is not working properly, please contact with your hosting provider.";
                return json_encode($response);
            }
        }
    }

    public function periodic_check(){

        $today= date('d');

        if($today%7==0){

            if(file_exists(APPPATH.'config/licence.txt') && file_exists(APPPATH.'core/licence.txt')){
                $config_existing_content = file_get_contents(APPPATH.'config/licence.txt');
                $config_decoded_content = json_decode($config_existing_content, true);
                $last_check_date= $config_decoded_content['checking_date'];
                $purchase_code  = $config_decoded_content['purchase_code'];
                $base_url = base_url();
                $domain_name  = get_domain_only($base_url);

                if( strtotime(date('Y-m-d')) != strtotime($last_check_date)){
                    $this->code_activation_check_action($purchase_code,$domain_name,$periodic=1);
                }
            }
        }
    }

    public function php_info()
    {
        if($this->session->userdata('user_type')== 'Admin')
        echo phpinfo();
        else redirect('home/access_forbidden', 'location');
    }


    public function redirect_link()
    {
        $this->load->library('Fb_search');
        $access_token = $this->fb_search->login_callback();
        if($access_token['status'] == 'error'){
            $data['error'] = 1;
            $data['message'] = $access_token['message'];
            $data['body'] = "page/redirect_link";
            $this->_viewcontroller($data);
        } else {
            $access_token = $this->fb_search->create_long_lived_access_token($access_token['message']);
            $user_id = $this->session->userdata('user_id');
            $where = array('user_id'=>$user_id,'status'=>'1');
            $update_data = array('user_access_token'=>$access_token['access_token']);

            if($this->basic->update_data('facebook_config',$where,$update_data)){
                $data['error'] = 0;
                $data['message'] = $this->lang->line("your data has been successfully stored into the database.");
            }
            else{
                $data['error'] = 1;
                $data['message'] =$this->lang->line("something went wrong, please try again.");
            }

            $data['body'] = "page/redirect_link";
            $this->_viewcontroller($data);
        }
    }


    public function redirect_rx_link()
    {
    
        if ($this->session->userdata('logged_in')!= 1) exit();

        $id=$this->session->userdata("fb_rx_login_database_id");

        $redirect_url = base_url()."home/redirect_rx_link/";

        $this->load->library('fb_rx_login');
        $user_info = $this->fb_rx_login->login_callback($redirect_url);

        if(isset($user_info['status']) && $user_info['status'] == '0')
        {
            $data['error'] = 1;
            $data['message'] = "<a style='text-decoration:none;' href='".base_url("facebook_rx_config/index/")."'>".$this->lang->line("something went wrong")." : ".$user_info['message']."</a>";
            $data['body'] = "facebook_rx/admin_login";
            $this->_viewcontroller($data);
        }
        else
        {
            $access_token=$user_info['access_token_set'];
            $where = array('id'=>$id);
            $update_data = array('user_access_token'=>$access_token);

            if($this->basic->update_data('facebook_rx_config',$where,$update_data))
            {

                $data = array(
                    'user_id' => $this->user_id,
                    'facebook_rx_config_id' => $id,
                    'access_token' => $access_token,
                    'name' => $user_info['name'],
                    'email' => isset($user_info['email']) ? $user_info['email'] : "",
                    'fb_id' => $user_info['id'],
                    'add_date' => date('Y-m-d')
                    );

                $where=array();
                $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                $exist_or_not = $this->basic->get_data('facebook_rx_fb_user_info',$where);

                if(empty($exist_or_not))
                {
                    $this->basic->insert_data('facebook_rx_fb_user_info',$data);
                    $facebook_table_id = $this->db->insert_id();
                }
                else
                {
                    $facebook_table_id = $exist_or_not[0]['id'];
                    $where = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                    $this->basic->update_data('facebook_rx_fb_user_info',$where,$data);
                }

                $this->session->set_userdata("facebook_rx_fb_user_info",$facebook_table_id);

                $page_list = $this->fb_rx_login->get_page_list($access_token);

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


                $group_list = $this->fb_rx_login->get_group_list($access_token);

                if(!empty($group_list))
                {
                    foreach($group_list as $group)
                    {
                        $user_id = $this->user_id;
                        $group_access_token = $access_token; // group uses user access token
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
                $this->session->set_flashdata('success_message', 1);
                redirect('facebook_rx_config/index','location');
                exit();
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = "<a href='".base_url("facebook_rx_config/index/")."'>".$this->lang->line("something went wrong, please try again.")."</a>";
                $data['body'] = "facebook_rx/admin_login";
                $this->_viewcontroller($data);
            }


        }


    }


    public function time_zone_drop_down($datavalue = '', $primary_key = null,$mandatory=0) // return HTML select
    {
        $all_time_zone = $this->_time_zone_list();

        $str = "<select name='time_zone' id='time_zone' class='form-control'>";
        if($mandatory===1)
        $str.= "<option value=>Time Zone *</option>";
        else $str.= "<option value=>Time Zone</option>";

        foreach ($all_time_zone as $zone_name=>$value) {
            if ($primary_key!= null) {
                if ($zone_name==$datavalue) {
                    $selected=" selected = 'selected' ";
                } else {
                    $selected="";
                }
            } else {
                if ($zone_name==$this->config->item("time_zone")) {
                    $selected=" selected = 'selected' ";
                } else {
                    $selected="";
                }
            }
            $str.= "<option ".$selected." value='$zone_name'>{$zone_name}</option>";
        }
        $str.= "</select>";
        return $str;
    }



    public function ul($code="") // unsubscribe fb exciter lead confirmation
    {
        if($code=="")
        {
            echo "<div style='margin:0 auto; width:auto;border:1px solid red;'><h2 align='center' style='color:red'>Invalid code.</h2><div>";
            exit();
        }

        $link = site_url("home/unsubscribe_lead_action/".$code);
        echo "<div style='margin:0 auto; width:auto;border:1px solid green;'><h2 align='center' style='color:red'>Are you sure that you want to unsubscribe? <br/><br/><a href='{$link}'>Click here to confirm</a></h2><div>";

    }


    public function unsubscribe_lead_action($code="") // unsubscribe fb exciter lead action
    {
        if($code=="")
        {
            echo "<div style='margin:0 auto; width:auto;border:1px solid red;'><h2 align='center' style='color:red'>Invalid code.</h2><div>";
            exit();
        }

        $code_original = $code;

        $code = urldecode($code);
        $code = base64_decode($code);

        $code_explode = explode('_', $code);
        $lead_id = isset($code_explode[1]) ? trim($code_explode[1]) : 0;
        $page_auto_id = isset($code_explode[2]) ? trim($code_explode[2]) : 0;

        if($lead_id>0)
        {
            $this->basic->update_data("facebook_rx_conversion_user_list",array("id"=>$lead_id),array("permission"=>"0"));
            if($this->db->affected_rows()>0)
            {
                $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count=current_subscribed_lead_count-1,current_unsubscribed_lead_count=current_unsubscribed_lead_count+1 WHERE id='{$page_auto_id}'");
            }

            $link = site_url("home/subscribe_lead_action/".$code_original);
            echo "<div style='margin:0 auto; width:auto;border:1px solid green;'><h2 align='center' style='color:green'>You have been unsubscribed successfully.</h2><br/><center><a href='{$link}'>Subscribe me again.</a></center><br/><br/><div>";
        }
        else
        {
           echo "<div style='margin:0 auto; width:auto;border:1px solid red;'><h2 align='center' style='color:red'>Invalid code.</h2><div>";
        }
    }


    public function subscribe_lead_action($code="") // subscribe back fb exciter lead
    {
        if($code=="")
        {
            echo "<div style='margin:0 auto; width:auto;border:1px solid red;'><h2 align='center' style='color:red'>Invalid code.</h2><div>";
            exit();
        }

        $code = urldecode($code);
        $code = base64_decode($code);

        $code_explode = explode('_', $code);
        $lead_id = isset($code_explode[1]) ? trim($code_explode[1]) : 0;
        $page_auto_id = isset($code_explode[2]) ? trim($code_explode[2]) : 0;

        if($lead_id>0)
        {
            $this->basic->update_data("facebook_rx_conversion_user_list",array("id"=>$lead_id),array("permission"=>"1"));

            if($this->db->affected_rows()>0)
            {
                $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count=current_subscribed_lead_count+1,current_unsubscribed_lead_count=current_unsubscribed_lead_count-1 WHERE id='{$page_auto_id}'");
            }
            echo "<div style='margin:0 auto; width:auto;border:1px solid green;'><h2 align='center' style='color:green'>You have been subscribed back successfully.</h2><div>";
        }
        else
        {
           echo "<div style='margin:0 auto; width:auto;border:1px solid red;'><h2 align='center' style='color:red'>Invalid code.</h2><div>";
        }
    }



    public function decode_url()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return false;
        }
        echo urldecode($this->input->post("message"));
    }

    public function decode_html()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return false;
        }
        echo html_entity_decode($this->input->post("message"));
    }



    public function license_check()
    {
        $file_data = file_get_contents(APPPATH . 'core/licence.txt');
        $file_data_array = json_decode($file_data, true);

        $purchase_code = $file_data_array['purchase_code'];

        $url = "http://xeroneit.net/development/envato_license_activation/regular_or_extended_check_r.php?purchase_code={$purchase_code}";

        $credentials = $this->get_general_content_with_checking($url);
        $response = json_decode($credentials, true);
        $response = json_decode($response['content'],true);

        if(!isset($response['status']) || $response['status'] == 'error')
        {
            $url="https://mostofa.club/development/envato_license_activation/regular_or_extended_check_r.php?purchase_code={$purchase_code}";            
            $credentials = $this->get_general_content_with_checking($url);
            $response = json_decode($credentials, true);
            $response = json_decode($response['content'],true);
        }

        if(isset($response['status']))
        {
            if($response['status'] == 'error')
            {
                $status = 'single';
            }
            else if($response['status'] == 'success' && $response['license'] == 'Regular License')
            {
                $status = 'single';
            }
            else
            {
                $status = 'double';
            }
            $content = $purchase_code."_".$status;

            $encrypt_method = "AES-256-CBC";
            $secret_key = 't8Mk8fsJMnFw69FGG5';
            $secret_iv = '9fljzKxZmMmoT358yZ';
            $key = hash('sha256', $secret_key);
            $string = $content;
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $encoded = base64_encode($output);

            file_put_contents(APPPATH.'core/licence_type.txt', $encoded, LOCK_EX);
        }


    }



    public function license_check_action()
    {
        $encoded = file_get_contents(APPPATH . 'core/licence_type.txt');
        $encrypt_method = "AES-256-CBC";
        $secret_key = 't8Mk8fsJMnFw69FGG5';
        $secret_iv = '9fljzKxZmMmoT358yZ';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $decoded = openssl_decrypt(base64_decode($encoded), $encrypt_method, $key, 0, $iv);

        $decoded = explode('_', $decoded);
        $decoded = array_pop($decoded);
        $this->session->set_userdata('license_type',$decoded);
    }


     //********************************** FUNCS USED FOR BOSS FILE MANAGER****************************** 
    //*************************************************************************************************
    public function delete_files()
    {
        if(!$_POST) exit();

        $file_path = $this->input->post("file_path");
        $thumb_path = $this->input->post("thumb_path");
        if($file_path!="") @unlink($file_path);
        if($thumb_path!="") @unlink($thumb_path);

    }

    public function load_files()
    {
        if(!$_POST) exit();

        $path= $this->input->post("loc");
        $video_path= $this->input->post("video_loc");
        $allowed= $this->input->post("allowed");
        $data_id= $this->input->post("data_id");
        $type= $this->input->post("data_type");

        if($path=="")
        {
            echo "No location mentioned";
            exit();
        }

        if($type=="video" && $video_path=="")
        {
            echo "No video location mentioned";
            exit();
        }

        if($allowed!="") $allowed=explode(',',$allowed);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $dirTree=$this->_scanAll($path);

        echo "<div class='row'>";
        foreach ($dirTree as $value) 
        {
            $explode2=array();
            $explode2 = explode('.',$value["file"]);
            $pos2 = count($explode2)-1;
            $ext = $explode2[$pos2];
            if(!in_array($ext, $allowed)  && $type!="video") continue;

            if($type=="image")
            {
                $explode=array();
                $explode = explode('/',$value["file"]);
                $pos = count($explode)-1;
                $only_name = $explode[$pos];        

                $only_name_trimmed=$only_name;
                if(strlen($only_name_trimmed)>35)
                $only_name_trimmed = mb_substr($only_name_trimmed, 0, 35)."...";

                $data_path="";
                $data_path=$path."/".$only_name;
                echo "<div class='col-xs-6 col-sm-4 col-md-3' style='padding:5px;overflow:hidden;font-size:10px;' title='".$only_name."'><img style='cursor:pointer;width:100%;height:140px;margin-bottom:7px;margin-top:10px;' data-id='".$data_id."' class='BossFileManager img-thumbnail center-block' thumb-path='' data-path='".$data_path."' only-name='".$only_name."'  src='".base_url($value["file"])."'>{$only_name_trimmed}<i class='delete-boss-file pull-right fa fa-trash red fa-2x' style='margin-top:-7px;' title='Delete'></i></div>";
            }
            else if($type=="audio")
            {
                $explode=array();
                $explode = explode('/',$value["file"]);
                $pos = count($explode)-1;
                $only_name = $explode[$pos];        

                $only_name_trimmed=$only_name;
                if(strlen($only_name_trimmed)>22)
                $only_name_trimmed = mb_substr($only_name_trimmed, 0, 22)."...";

                $data_path="";
                $data_path=$path."/".$only_name;
                echo "<div class='col-xs-6 col-sm-4 col-md-2'  style='padding:10px;overflow:hidden;font-size:10px;' title='".$only_name."'><img style='cursor:pointer;width:100%;height:140px;' data-id='".$data_id."' class='BossFileManager center-block' thumb-path='' data-path='".$data_path."' only-name='".$only_name."' src='".base_url('assets/images/audio.png')."'>{$only_name_trimmed}<i class='delete-boss-file pull-right fa fa-trash red fa-2x' style='margin-top:-7px;' title='Delete'></i></div>";
            }
            else if($type=="video")
            {               
                $explode=array();
                $explode = explode('/',$value["file"]);
                $pos = count($explode)-1;
                $image_name = $explode[$pos];

                $explode=array();
                $explode = explode('.',$image_name);
                array_pop($explode);
                $video_name = implode('.', $explode);            

                $only_name_trimmed=$video_name;
                if(strlen($only_name_trimmed)>35)
                $only_name_trimmed = mb_substr($only_name_trimmed, 0, 35)."...";

                $data_path="";
                $data_path=$video_path."/".$video_name;
                $thumb_path=$path."/".$image_name;

                if(file_exists($data_path))
                echo "<div class='col-xs-6 col-sm-4 col-md-3' style='padding:5px;overflow:hidden;font-size:10px;' title='".$video_name."'><img style='cursor:pointer;width:100%;height:140px;margin-bottom:7px;margin-top:10px;' data-id='".$data_id."' class='BossFileManager img-thumbnail center-block' thumb-path='".$thumb_path."' data-path='".$data_path."' only-name='".$video_name."' src='".base_url($value["file"])."'>{$only_name_trimmed}<i class='delete-boss-file pull-right fa fa-trash red fa-2x' style='margin-top:-7px;' title='Delete'></i></div>";
            }
        }
        echo "</div>";
    }

    //********************************** FUNCS USED FOR BOSS FILE MANAGER****************************** 
    //*************************************************************************************************



    /*
    *********************************************************************************
    ******************************ADD ON FUNCTIONS START***************************** 
    ******************************Also see addon_helper******************************
    */


    //loads language files of addons
    protected function language_loader_addon()
    {    
        
        $controller_name=strtolower($this->uri->segment(1));
        $path_without_filename="application/modules/".$controller_name."/language/".$this->language."/";
        if(file_exists($path_without_filename.$controller_name."_lang.php"))
        {
            $filename=$controller_name;
            $this->lang->load($filename,$this->language,FALSE,TRUE,$path_without_filename);
        }

        // $get_addon=$this->basic->get_data("add_ons");    
        // foreach ($get_addon as $key => $value) 
        // {
        //     $module_folder_name=isset($value["module_folder_name"]) ? $value["module_folder_name"] : "";
        //     if($value["module_folder_name"]=="") continue;

        //     $path_without_lang_folder=str_replace('\\', '/', APPPATH.'/modules/'.$module_folder_name.'/language/'); //application/modules/addon_folder/language
        //     $path=$path_without_lang_folder.$this->language; //application/modules/addon_folder/language/language_folder
        //     if(!file_exists($path)) continue;

        //     $files=$this->_scanAll($path);
        //     foreach ($files as $key2 => $value2) 
        //     {
        //         $current_file=isset($value2['file']) ? str_replace('\\', '/', $value2['file']) : ""; //application/modules/addon_folder/language/language_folder/someting_lang.php
        //         if($current_file=="" || !is_file($current_file)) continue;

        //         $current_file_explode=explode('/',$current_file);
        //         $filename=array_pop($current_file_explode); // getting last part, thats file name [example: something_lang.php]
        //         $pos=strpos($filename,'_lang.php');
        //         if($pos!==false) // check if it is a lang file or not
        //         {
        //             $filename=str_replace('_lang.php', '', $filename); 
        //             $path_without_filename=implode('/', $current_file_explode).'/';  //application/modules/addon_folder/language/language_folder/
        //             $this->lang->load($filename,$this->language,FALSE,TRUE,$path_without_filename);
        //         }
        //     }            
        // }

    }

    // delete any direcory with it childs even it is not empty
    protected function delete_directory($dirPath="") 
    {
        if (!is_dir($dirPath)) 
        return false;

        if(substr($dirPath, strlen($dirPath) - 1, 1) != '/') $dirPath .= '/';
        
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach($files as $file) 
        {
            if(is_dir($file)) $this->delete_directory($file);             
            else @unlink($file);            
        }
        rmdir($dirPath);
    }

    // takes addon controller path as input and extract add on data from comment block
    protected function get_addon_data($path="")
    {
        $path=str_replace('\\','/',$path);
        $tokens=token_get_all(file_get_contents($path));
        $addon_data=array();

        $addon_path=explode('/', $path);
        $controller_name=array_pop($addon_path);
        array_pop($addon_path);
        $addon_path=implode('/',$addon_path);

        $comments = array();
        foreach($tokens as $token) 
        {
            if($token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) 
            {       
                $comments[] = isset( $token[1]) ?  $token[1] : "";
            } 
        }
        $comment_str=isset($comments[0]) ? $comments[0] : "";
        
        preg_match( '/^.*?addon name:(.*)$/mi', $comment_str, $match); 
        $addon_data['addon_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?unique name:(.*)$/mi', $comment_str, $match); 
        $addon_data['unique_name'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?module id:(.*)$/mi', $comment_str, $match); 
        $addon_data['module_id'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?project id:(.*)$/mi', $comment_str, $match); 
        $addon_data['project_id'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?addon uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['addon_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author:(.*)$/mi', $comment_str, $match); 
        $addon_data['author'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?author uri:(.*)$/mi', $comment_str, $match); 
        $addon_data['author_uri'] = isset($match[1]) ? trim($match[1]) : "";

        preg_match( '/^.*?version:(.*)$/mi', $comment_str, $match); 
        $addon_data['version'] = isset($match[1]) ? trim($match[1]) : "1.0";

        preg_match( '/^.*?description:(.*)$/mi', $comment_str, $match); 
        $addon_data['description'] = isset($match[1]) ? trim($match[1]) : "";

        $addon_data['controller_name'] = isset($controller_name) ? trim($controller_name) : "";

        if(file_exists($addon_path.'/install.txt'))
        $addon_data['installed']='0';
        else $addon_data['installed']='1';  

        return $addon_data;
    }

    // checks purchase code , returns boolean
    protected function addon_credential_check($purchase_code="",$item_name="")
    {
        if($purchase_code=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on purchase code has not been provided.')));
            exit();
        }

        $item_name=urlencode($item_name);
        $only_domain=get_domain_only(site_url());
        $url = "http://xeroneit.net/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=FBInboxer-{$item_name}";

        $credentials = $this->get_general_content_with_checking($url);
        $decoded_credentials = json_decode($credentials,true);

        if(isset($decoded_credentials['error']))
        {
            $url = "https://mostofa.club/development/envato_license_activation/purchase_code_check.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=FBInboxer-{$item_name}";
            $credentials = $this->get_general_content_with_checking($url);
            $decoded_credentials = json_decode($credentials,true);
        }

        if(!isset($decoded_credentials['error'])) 
        {
            $content = json_decode($decoded_credentials['content'],true);
            if($content['status'] != 'success')            
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('purchase code not valid or already used.')));
                exit();
            }
        }  
        else
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('something went wrong. CURL is not working.')));
            exit();
        }
    }

    // validataion of addon data
    protected function check_addon_data($addon_data=array())
    {
        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on unique name has not been provided.')));
            exit();
        }
        
        if(!$this->is_unique_check("addon_check",$addon_data['unique_name']))  //  unique name must be unique
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on is already active. duplicate unique name found.')));
            exit();
        }
        
        if($addon_data['module_id']!="" && $addon_data['module_id']!==0) 
        {
            if(!is_numeric($addon_data['module_id'])) // if module id provided, it must be integer
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on module ID must be integer.')));
                exit();
            }

            if(!$this->is_unique_check("module_check",$addon_data['module_id'])) // if module id provided, it must be unique
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on is already active. duplicate module id found.')));
                exit();
            }
        }
    }

    // inserts data to add_ons table + modules + menu + menuchild1 + removes install.txt, returns json status,message
    protected function register_addon($addon_controller_name="",$sidebar=array(),$sql=array(),$purchase_code="",$default_module_name="")
        {
            if($this->session->userdata('user_type') != 'Admin')
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
                exit();
            }   

            if($this->is_demo == '1')
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
                exit();
            }     

            if($addon_controller_name=="") 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on controller has not been provided.')));
                exit();
            }
            
            $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
            $install_txt_path=APPPATH."modules/".strtolower($addon_controller_name)."/install.txt"; // path of install.txt
            if(!file_exists($path)) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on controller not found.')));
                exit();
            }

            $addon_data=$this->get_addon_data($path);

            $this->check_addon_data($addon_data);

            try 
            {
                $this->db->trans_start();
                
                // addon table entry
                $this->basic->insert_data("add_ons",array("add_on_name"=>$addon_data['addon_name'],"unique_name"=>$addon_data["unique_name"],"version"=>$addon_data["version"],"installed_at"=>date("Y-m-d H:i:s"),"purchase_code"=>$purchase_code,"module_folder_name"=>strtolower($addon_controller_name),"project_id"=>$addon_data["project_id"]));
                $add_ons_id=$this->db->insert_id();

                // modules table entry
                if($default_module_name=="") $default_module_name=$addon_data['addon_name'];
                if($addon_data['module_id']!="" && $addon_data['module_id']!==0)
                $this->basic->insert_data("modules",array("id"=>$addon_data['module_id'],"extra_text"=>"","module_name"=>$default_module_name,"add_ons_id"=>$add_ons_id,"deleted"=>"0"));
                
                //--------------- sidebar entry--------------------
                //-------------------------------------------------
                if(is_array($sidebar))
                foreach ($sidebar as $key => $value) 
                {
                    $parent_name        = isset($value['name']) ? $value['name'] : "";
                    $parent_icon        = isset($value['icon']) ? $value['icon'] : "";
                    $parent_url         = isset($value['url']) ? $value['url'] : "#";
                    $parent_is_external = isset($value['is_external']) ? $value['is_external'] : "0";
                    $child_info         = isset($value['child_info']) ? $value['child_info'] : array();
                    $have_child         = isset($child_info['have_child']) ? $child_info['have_child'] : '0';
                    $only_admin         = isset($value['only_admin']) ? $value['only_admin'] : '0';
                    $only_member        = isset($value['only_member']) ? $value['only_member'] : '0';
                    $parent_serial      = 10; // all addon parent menus will have this serial becuase it need to show before cron job (12) & manual(13)
                    
                    if($addon_data['module_id']==0) $parent_module_id=""; // no module access control needed
                    else $parent_module_id=$addon_data['module_id']; 
                    
                    $parent_menu=array('name'=>$parent_name,'icon'=>$parent_icon,'url'=>$parent_url,'serial'=>$parent_serial,'module_access'=>$parent_module_id,'have_child'=>$have_child,'only_admin'=>$only_admin,'only_member'=>$only_member,'add_ons_id'=>$add_ons_id,'is_external'=>$parent_is_external);
                    $this->basic->insert_data('menu',$parent_menu); // parent menu entry
                    $parent_id=$this->db->insert_id();

                    if($have_child=='1')
                    {
                        if(!empty($child_info))
                        {
                            $child = isset($child_info['child']) ? $child_info['child'] : array();
                            
                            $child_serial=0;
                            if(!empty($child))
                            foreach ($child as $key2 => $value2) 
                            {
                                $child_serial++;
                                $child_name         = isset($value2['name']) ? $value2['name'] : "";
                                $child_icon         = isset($value2['icon']) ? $value2['icon'] : "";
                                $child_url          = isset($value2['url']) ? $value2['url'] : "#";
                                $child_info_1       = isset($value2['child_info']) ? $value2['child_info'] : array();
                                $child_is_external  = isset($value2['is_external']) ? $value2['is_external'] : "0";
                                $have_child         = isset($child_info_1['have_child']) ? $child_info_1['have_child'] : '0';
                                $only_admin         = isset($value2['only_admin']) ? $value2['only_admin'] : '0';
                                $only_member        = isset($value2['only_member']) ? $value2['only_member'] : '0';
                                                
                                $child_menu=array('name'=>$child_name,'icon'=>$child_icon,'url'=>$child_url,'serial'=>$child_serial,'module_access'=>$parent_module_id,'parent_id'=>$parent_id,'have_child'=>$have_child,'only_admin'=>$only_admin,'only_member'=>$only_member,'is_external'=>$child_is_external);
                                $this->basic->insert_data('menu_child_1',$child_menu); // child menu entry
                                $sub_parent_id=$this->db->insert_id();

                                if($have_child=='1')
                                {
                                    if(!empty($child_info_1))
                                    {
                                        $child = isset($child_info_1['child']) ? $child_info_1['child'] : array();
                                        
                                        
                                        
                                        $child_child_serial=0;
                                        if(!empty($child))
                                        foreach ($child as $key3 => $value3) 
                                        {
                                            $child_child_serial++;
                                            $child_name         = isset($value3['name']) ? $value3['name'] : "";
                                            $child_icon         = isset($value3['icon']) ? $value3['icon'] : "";
                                            $child_url          = isset($value3['url']) ? $value3['url'] : "#";
                                            $child_is_external  = isset($value3['is_external']) ? $value3['is_external'] : "0";
                                            $have_child         = '0';
                                            $only_admin         = isset($value3['only_admin']) ? $value3['only_admin'] : '0';
                                            $only_member        = isset($value3['only_member']) ? $value3['only_member'] : '0';
                                                            
                                            $child_menu=array('name'=>$child_name,'icon'=>$child_icon,'url'=>$child_url,'serial'=>$child_child_serial,'module_access'=>$parent_module_id,'parent_child'=>$sub_parent_id,'only_admin'=>$only_admin,'only_member'=>$only_member,'is_external'=>$child_is_external);
                                            $this->basic->insert_data('menu_child_2',$child_menu); // child menu entry
                                            
                                        }
                                    }
                                } 
                            }
                        }
                    }            

                }
                //--------------- sidebar entry--------------------
                //-------------------------------------------------

                $this->db->trans_complete();
                     

                if ($this->db->trans_status() === FALSE) 
                {
                    echo json_encode(array('status'=>'0','message'=>$this->lang->line('database error. something went wrong.')));
                    exit();
                }
                else 
                {   
                    
                    //--------Custom SQL------------
                    $this->db->db_debug = FALSE; //disable debugging for queries
                    if(is_array($sql))            
                    foreach ($sql as $key => $query) 
                    {
                        try
                        {
                            $this->db->query($query);
                        }
                        catch(Exception $e)
                        {
                        }                    
                    }
                    //--------Custom SQL------------                
                    @unlink($install_txt_path); // removing install.txt                
                    echo json_encode(array('status'=>'1','message'=>$this->lang->line('add-on has been activated successfully.')));
                }

            } //end of try
            catch(Exception $e)
            {
                $error = $e->getMessage();   
                echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
            }
        }

    // deletes data from add_ons table + modules + menu + menuchild1 + puts install.txt, returns json status,message
    protected function unregister_addon($addon_controller_name="")
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }


        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $install_txt_path=APPPATH."modules/".strtolower($addon_controller_name)."/install.txt"; // path of install.txt
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on unique name has not been provided.')));
            exit();
        }


        try 
        {
            $this->db->trans_start();
            
            // delete addon table entry
            $get_addon=$this->basic->get_data("add_ons",array("where"=>array("unique_name"=>$addon_data['unique_name'])));
            $add_ons_id=isset($get_addon[0]['id']) ? $get_addon[0]['id'] : 0;
            if($add_ons_id>0)
            $this->basic->delete_data("add_ons",array("id"=>$add_ons_id));
            
            // delete modules table entry    
            if($add_ons_id>0)        
            $this->basic->delete_data("modules",array("add_ons_id"=>$add_ons_id));

            // delete menu+menu_child1 table entry
            $get_menu=array();
            if($add_ons_id>0)   
            $get_menu=$this->basic->get_data("menu",array("where"=>array("add_ons_id"=>$add_ons_id)));
            
            foreach($get_menu as $key => $value) 
            {
               $parent_id=isset($value['id']) ? $value['id'] : 0;
               if($parent_id>0)
               {    
                  $this->basic->delete_data("menu",array("id"=>$parent_id));
                  $this->basic->delete_data("menu_child_1",array("parent_id"=>$parent_id));
               }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('database error. something went wrong.')));
                exit();
            }
            else 
            {   
                if(!file_exists($install_txt_path)) // putting install.txt
                fopen($install_txt_path, "w");

                echo json_encode(array('status'=>'1','message'=>$this->lang->line('add-on has been deactivated successfully.')));
            }
        } 
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }

    // deletes data from add_ons table + modules + menu + menuchild1 + custom sql + folder, returns json status,message    
    protected function delete_addon($addon_controller_name="",$sql=array())
    {
        if($this->session->userdata('user_type') != 'Admin')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($this->is_demo == '1')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('Access Forbidden')));
            exit();
        }

        if($addon_controller_name=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on controller has not been provided.')));
            exit();
        }
        
        $path=APPPATH."modules/".strtolower($addon_controller_name)."/controllers/".$addon_controller_name.".php"; // path of addon controller
        $addon_path=APPPATH."modules/".strtolower($addon_controller_name); // path of module folder
        if(!file_exists($path)) 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on controller not found.')));
            exit();
        }

        $addon_data=$this->get_addon_data($path);

        if(!isset($addon_data['unique_name']) || $addon_data['unique_name']=="") 
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line('add-on unique name has not been provided.')));
            exit();
        }


        try 
        {
            $this->db->trans_start();
            
            // delete addon table entry
            $get_addon=$this->basic->get_data("add_ons",array("where"=>array("unique_name"=>$addon_data['unique_name'])));
            $add_ons_id=isset($get_addon[0]['id']) ? $get_addon[0]['id'] : 0;
            $purchase_code=isset($get_addon[0]['purchase_code']) ? $get_addon[0]['purchase_code'] : '';
            if($add_ons_id>0)
            $this->basic->delete_data("add_ons",array("id"=>$add_ons_id));
            
            // delete modules table entry    
            if($add_ons_id>0)        
            $this->basic->delete_data("modules",array("add_ons_id"=>$add_ons_id));

            // delete menu+menu_child1 table entry
            $get_menu=array();
            if($add_ons_id>0)   
            $get_menu=$this->basic->get_data("menu",array("where"=>array("add_ons_id"=>$add_ons_id)));
            
            foreach($get_menu as $key => $value) 
            {
               $parent_id=isset($value['id']) ? $value['id'] : 0;
               if($parent_id>0)
               {    
                  $this->basic->delete_data("menu",array("id"=>$parent_id));
                  $this->basic->delete_data("menu_child_1",array("parent_id"=>$parent_id));
               }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) 
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line('database error. something went wrong.')));
                exit();
            }
            else 
            {   
                //--------Custom SQL------------
                $this->db->db_debug = FALSE; //disable debugging for queries
                if(is_array($sql))            
                foreach ($sql as $key => $query) 
                {
                    try
                    {
                        $this->db->query($query);
                    }
                    catch(Exception $e)
                    {
                    }                    
                }
                //--------Custom SQL------------             

                $this->delete_directory($addon_path);  // deleting module folder

                // deleting purchase code from database
                if($purchase_code!="")
                {
                    $item_name=strtolower($addon_controller_name);
                    $only_domain=get_domain_only(site_url());
                    $url = "http://xeroneit.net/development/envato_license_activation/delete_purchase_code.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=FBInboxer-{$item_name}";
                    $credentials = $this->get_general_content_with_checking($url);
                    $response = json_decode($credentials,true);
                    if(isset($response['error']))
                    {
                        $url = "https://mostofa.club/development/envato_license_activation/delete_purchase_code.php?purchase_code={$purchase_code}&domain={$only_domain}&item_name=FBInboxer-{$item_name}";
                        $this->get_general_content_with_checking($url);                    
                    }
                }
                // deleting purchase code from database
  
                echo json_encode(array('status'=>'1','message'=>$this->lang->line('add-on has been deleted successfully.')));
            }
        } 
        catch(Exception $e)
        {
            $error = $e->getMessage();   
            echo json_encode(array('status'=>'0','message'=>$this->lang->line($error)));            
        }
    }


    // check a addon or module id is usable or already used, returns boolean, true if unique
    protected function is_unique_check($type='addon_check',$value="") // type=addon_check/module_check | $value=column.value
    {
        $is_unique=false;
        if($type=="addon_check")  $is_unique=$this->basic->is_unique("add_ons",array("unique_name"=>$value),"id");
        if($type=="module_check") $is_unique=$this->basic->is_unique("modules",array("id"=>$value),"id");
        return $is_unique;
    }

     /*
    *********************************************************************************
    *******************************ADD ON FUNCTIONS END****************************** 
    */

    public function privacy_policy()
    {
         $data['page_title'] = 'Privacy Policy';
         $data['body'] = 'front/privacy_policy';
         $this->_front_viewcontroller($data);
    }

    public function terms_use()
    {
         $data['page_title'] = 'Terms of Use';
         $data['body'] = 'front/terms_use';
         $this->_front_viewcontroller($data);
    }

    public function gdpr()
    {
         $data['page_title'] = 'GDPR';
         $data['body']='front/gdpr';;
         $this->_front_viewcontroller($data);
    }

    public function allow_cookie()
    {
        $this->session->set_userdata('allow_cookie','yes');
        // redirect($_SERVER['HTTP_REFERER'],'location');
    }


    public function delete_full_access()
    {
        if($this->session->userdata('user_type') == 'Admin') exit();
        if(!isset($_POST)) exit();
        $user_id=$this->session->userdata('user_id');

        $this->db->trans_start();
        $sql = "show tables;";
        $a = $this->basic->execute_query($sql);
        foreach($a as $value)
        {
            foreach($value as $table_name)
            {
                if($table_name == 'users') $this->basic->delete_data('users',array('id'=>$user_id));
                if($table_name == 'view_usage_log') continue;
                if($this->db->field_exists('user_id',$table_name))
                    $this->basic->delete_data($table_name,array('user_id'=>$user_id));
            }
        }
        $this->db->trans_complete();                

        if ($this->db->trans_status() === FALSE) 
        {
            echo $this->lang->line('Something went wrong, please try again.');            
        }
        else
        {
            $this->session->sess_destroy();
            echo 'success';        
        }

    }




    function scanAll($myDir){

        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            //$dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);


            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);
            $dirTree[] = $file_path;

        }

        return $dirTree;

    }

    public function lang_scanAll($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            // $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $i++;
        }
        return $dirTree;
    }



    function translation($is_addon=FALSE,$add_on_name='commenttagmachine'){  /*add-on_name must be the same as folder name of inside modules*/

        if(!$is_addon){

            $folder_path="application/controllers/";
            $all_directory1= $this->scanAll($folder_path);

            $folder_path="application/libraries/";
            $all_directory2= $this->scanAll($folder_path);

            $folder_path="application/views/";
            $all_directory3= $this->scanAll($folder_path);

            $all_directory=array_merge($all_directory1,$all_directory2,$all_directory3);

            $language_app_path=APPPATH;

        }

        if($is_addon){

            $folder_path="application/modules/{$add_on_name}/";
            $all_directory= $this->scanAll($folder_path);

            $language_app_path=APPPATH."/modules/{$add_on_name}";
        }

        $all_lang=array();

        foreach($all_directory as $dir){

            $content=file_get_contents($dir);
            preg_match_all('#\$this->lang->line\("(.*?)"\)#si', $content, $matches);    

            foreach($matches[1] as $line){
                $all_lang[]=strtolower($line);
            }

            preg_match_all('#\$this->lang->line\(\'(.*?)\'\)#si', $content, $matches1); 

            foreach($matches1[1] as $line){
                $all_lang[]=strtolower($line);
            }

        }

        /*** Get all existing language from language folder ***/


        $language_name_array=array("english","bengali","dutch","french","german","greek","italian","portuguese","russian","spanish","turkish","vietnamese");


        foreach($language_name_array as $language_name){

            $this->lang->is_loaded = array();
            $this->lang->language = array();

            $path=str_replace('\\', '/', $language_app_path.'/language/'.$language_name); 


            $files=$this->lang_scanAll($path);
            foreach ($files as $key2 => $value2) 
            {
                $current_file=isset($value2['file']) ? str_replace('\\', '/', $value2['file']) : ""; //application/modules/addon_folder/language/language_folder/someting_lang.php
                if($current_file=="" || !is_file($current_file)) continue;
                $current_file_explode=explode('/',$current_file);
                $filename=array_pop($current_file_explode);
                $pos=strpos($filename,'_lang.php');
                if($pos!==false) // check if it is a lang file or not
                {
                    $filename=str_replace('_lang.php', '', $filename); 
                    $this->lang->load($filename, $language_name);
                }
            }      


            $all_lang_prev_array=$this->lang->language;

            $all_lang_prev_array=array_change_key_case($all_lang_prev_array, CASE_LOWER);

            foreach($all_lang as $lang_index){

                if(isset($all_lang_prev_array[$lang_index]))
                    $now_all_write_lang[$lang_index]=$all_lang_prev_array[$lang_index];
                else
                    $now_all_write_lang[$lang_index]="";
            }


            /** Language that's exist but not found in current code **/

            $extra_lang= array_diff($all_lang_prev_array,$now_all_write_lang);

            $now_all_write_lang_merge = array_merge($now_all_write_lang, $extra_lang);

            asort($now_all_write_lang_merge);

            $lang_write_file=$path."/all_lang.php";

            /** Keep a backup for all_lang.php **/
            if(file_exists($lang_write_file)){
                $date=date("Y-m-d H-i-s");
                $write_path="backup_lang/{$language_name}/all_lang_{$date}.php";
                // copy($lang_write_file,$write_path);
            }

            file_put_contents($lang_write_file, '<?php $lang = ' . var_export($now_all_write_lang_merge, true) . ';');


            $new_lang= array_diff($now_all_write_lang_merge,$all_lang_prev_array);

            echo $language_name.": New Line added : ". count($new_lang)."<br>";

            /*      echo "<pre>";
            print_r($new_lang);*/

            }


        }




        public function youtube_live_stream_ffmpeg_command() // video matrix go live now
        {
            $video_height = $this->input->get("video_height");
            $video_width = $this->input->get("video_width");

            if($video_width == '') $video_width=1280;
            if($video_height == '') $video_height=720;
            
            $file_name = $this->input->get("filename");
            $stream_url = $this->input->get("strem_url");
            $video_id = $this->input->get("video_id");
            $channel_id = $this->input->get("channel_id");
            $table_id = $this->input->get("table_id");
            $secret = $this->input->get("secret");

            $file_name=urldecode($file_name);
            $stream_url=urldecode($stream_url);
            $video_id=urldecode($video_id);
            $channel_id=urldecode($channel_id);

            $where['where'] = array('youtube_vidcasterlive_channel_list.channel_info_id'=>$channel_id);
            $select = array('youtube_vidcasterlive_channel_list.id as table_id','access_token','refresh_token','channel_id','title');
            $join = array('youtube_vidcasterlive_channel_info'=>'youtube_vidcasterlive_channel_list.channel_info_id=youtube_vidcasterlive_channel_info.id,left');
            $channel_info = $this->basic->get_data('youtube_vidcasterlive_channel_list',$where,$select,$join);
            
            $this->session->set_userdata('videomatrix_liveevent_access_token',$channel_info[0]['access_token']);
            $this->session->set_userdata('videomatrix_liveevent_refresh_token',$channel_info[0]['refresh_token']);

            $this->load->library('youtubelive_login');

            $url='ffmpeg -re -i '.$file_name.' -acodec libmp3lame -ar 44100 -b:a 128k -pix_fmt yuv420p -profile:v baseline -s '.$video_width.'x'.$video_height.' -bufsize 6000k -vb 2048k -minrate 3000k -maxrate 4500k -deinterlace -vcodec libx264 -preset ultrafast -g 60 -r 30 -f flv "'.$stream_url.'"';
            exec($url);

            $this->Youtubelive_login->youtube_live_transition_complete($video_id);

            $this->basic->update_data("youtube_live_streaming_campaign",array("Broadcast_id"=>$video_id),array("completed_at"=>date("Y-m-d H:i:s"),"is_complete"=>'1'));
        }


        public function paypal_error_log()
        {
            if($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') == 'Admin')
            {
                $log = $this->basic->get_data('paypal_error_log',$where='',$select='',$join='',$limit='',$start=NULL,$order_by='id desc');
                foreach($log as $value)
                {
                    echo $value['id'].")<br/>";
                    echo "Call Time: ".$value['call_time']."<br/>";
                    echo "IPN Value: <br/>";
                    $ipn_value = array();
                    $ipn_value = json_decode($value['ipn_value'],true);
                    echo "<pre>"; 
                    print_r($ipn_value);
                    echo "</pre><br/>";

                    echo "Error Log: ".$value['error_log']."<br/><br/>";
                }
            }
            else
                echo "Please log in first to access this function.";
        }


        public function central_webhook_callback()
        {
            $challenge = $this->input->get_post('hub_challenge');
            $verify_token =$this->input->get_post('hub_verify_token');
            if($verify_token === $this->config->item("central_webhook_verify_token"))
            {
                echo $challenge;
                die();
            }

            $response_raw=file_get_contents("php://input");

            if(!isset($response_raw) || $response_raw=='') exit; 

            $json_response=array("response_raw"=>$response_raw);
            $response = json_decode($response_raw, true);

            if(isset($response['entry'][0]['messaging']))
            {
                if($this->db->table_exists('messenger_bot')){
                    $url=base_url()."messenger_bot/webhook_callback_main";
                }
                else
                    $url=base_url()."home/page_messaging_reply";
            } 

            else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'comment') 
                $url=base_url()."pageresponse/webhook_callback_main";

            else if(isset($response['entry'][0]['changes'][0]['field']) && $response['entry'][0]['changes'][0]['field'] == 'feed') 
                $url=base_url()."pageresponse/webhook_callback_main";

            else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'photo') 
                 $url=base_url()."pageresponse/webhook_callback_main";
        
            else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'status') 
                $url=base_url()."pageresponse/webhook_callback_main";
            else if(isset($response['entry'][0]['changes'][0]['value']['item']) && $response['entry'][0]['changes'][0]['value']['item'] == 'share')
                $url=base_url()."pageresponse/webhook_callback_main";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$json_response);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            $reply_response=curl_exec($ch);
        }


        public function page_messaging_reply()
        {
            $response_raw=$this->input->post("response_raw");
            $response = json_decode($response_raw,true);

            $page_id = $response['entry']['0']['messaging'][0]['recipient']['id'];
            $messages = $response['entry']['0']['messaging'][0]['message']['text'];
            $sender_id= $response['entry']['0']['messaging'][0]['sender']['id'];

            $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('page_id'=>$page_id,'webhook_enabled'=>'1')));
            $page_access_token = isset($page_info[0]['page_access_token']) ? $page_info[0]['page_access_token'] : "0";

            $page_messaging_reply = $this->basic->get_data('page_messaging_information');
            $reply = '';
            foreach($page_messaging_reply as $value)
            {
                if(function_exists('iconv') && function_exists('mb_detect_encoding')){
                    $encoded_word =  mb_detect_encoding($value['keywords']);
                    if(isset($encoded_word)){
                        $cam_keywords = iconv( $encoded_word, "UTF-8//TRANSLIT", $value['keywords'] );
                    }
                }
                $pos= stripos($messages,trim($cam_keywords));
                if($pos!==FALSE){
                    $reply = $value['message'];
                    break;
                }
            }


            if($reply!=""){
                $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                $this->send_reply_ez($page_access_token,$reply);
            }


        }


    
    public function send_reply_ez($access_token='',$reply='')
    {   
        $url="https://graph.facebook.com/v2.6/me/messages?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$reply); 
 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
        $st=curl_exec($ch);       
        
        $result=json_decode($st,TRUE);
        return $result;
    }




}

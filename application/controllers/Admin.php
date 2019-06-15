<?php

require_once("Home.php"); // loading home controller

/**
* @category controller
* class Admin
*/

class Admin extends Home
{

    public $user_id;    

    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');        
        if ($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location');
        
        $this->load->helper('form');
        $this->load->library('upload');
        
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();
        $this->periodic_check();

    }


    public function index()
    {
        $this->user_management();
    }

 

    public function user_management()
    {
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('users');
        $crud->order_by('id');
        $crud->where('users.deleted', '0');
        $crud->set_subject($this->lang->line("user"));
        $crud->set_relation('package_id','package','package_name',array('package.deleted' => '0'));

        $crud->fields('name', 'email', 'mobile', 'password', 'address', 'user_type', 'status');

        $crud->edit_fields('name', 'email', 'mobile', 'address','expired_date','package_id', 'status');

        $crud->add_fields('name', 'email', 'mobile', 'password', 'address', 'user_type', 'status');

        $crud->required_fields('name', 'email', 'password', 'user_type','expired_date','package_id', 'status');

        $crud->columns('name', 'email','package_id', 'status', 'user_type', 'add_date','last_login_at','last_login_ip','expired_date');

        $crud->field_type('password', 'password');
        $crud->field_type('expired_date', 'date');

        $crud->display_as('add_date',$this->lang->line('Register date'));
        // $crud->display_as('purchase_date','Purchase date');
        $crud->display_as('last_login_at',$this->lang->line('Last Logged in'));
        $crud->display_as('last_login_ip',$this->lang->line('Last IP'));
        $crud->display_as('name', $this->lang->line('name'));
        $crud->display_as('email', $this->lang->line('email'));
        $crud->display_as('mobile', $this->lang->line('mobile'));
        $crud->display_as('address', $this->lang->line('address'));
        $crud->display_as('status', $this->lang->line('status'));
        $crud->display_as('user_type', $this->lang->line('Type'));
        $crud->display_as('password', $this->lang->line('password'));
        $crud->display_as('package_id', $this->lang->line('package name'));
        $crud->display_as('expired_date', $this->lang->line('expiry date'));
        $crud->unset_texteditor('address');
       
        $crud->set_rules("email",$this->lang->line("email"),'valid_email|callback_unique_email_check['.$this->uri->segment(4).']');

        // $images_url = base_url("plugins/grocery_crud/themes/flexigrid/css/images/password.png");
        $crud->add_action($this->lang->line('Change User Password'), 'fa fa-key', 'admin/change_user_password');

        if($this->session->userdata('license_type') == 'double')
        $crud->add_action($this->lang->line('User Dashboard'), 'fa fa-eye', 'admin/user_dashboard','',NULL,"_blank");

        $crud->callback_column('expired_date', array($this, 'expired_date_display_crud'));
        $crud->callback_field('expired_date', array($this, 'expired_date_field_crud'));

        if($this->is_demo == '1')
        $crud->callback_column('email', array($this, 'email_display_crud'));

        $crud->callback_column('add_date', array($this, 'expired_date_display_crud'));
        // $crud->callback_column('purchase_date', array($this, 'date_display_crud'));
        $crud->callback_column('last_login_at', array($this, 'date_display_crud'));

        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->callback_field('status', array($this, 'status_field_crud'));

        $crud->callback_after_insert(array($this, 'encript_password'));
        $crud->callback_after_delete(array($this,'delete_everything_after_user_delete'));
        if($this->config->item('developer_access') == '1') $crud->unset_add();
        $crud->unset_read();
        $crud->unset_print();
        $crud->unset_export();

        if($this->is_demo == '1')
        {
            $crud->unset_edit();
            $crud->unset_delete();
        }

        if($this->session->userdata('license_type') == 'double')
        $custom_link=array
        (
            0=>array
            (
                'set_custom_link_pull_right'=>true,
                'set_custom_link_url'=>base_url('admin/user_log'),
                'set_custom_link_hover'=>$this->lang->line('User Login Log'),
                'set_custom_link_btn_class'=>'btn btn-info',
                'set_custom_link_fa_class'=>'fa fa-history',
                'set_custom_link_label'=>$this->lang->line('User Login Log')
            ),
            1=>array
            (
                'set_custom_link_pull_right'=>true,
                'set_custom_link_url'=>base_url('admin/overall_dashboard'),
                'set_custom_link_hover'=>$this->lang->line('System Dashboard'),
                'set_custom_link_btn_class'=>'btn btn-danger',
                'set_custom_link_fa_class'=>'fa fa-dashboard',
                'set_custom_link_label'=>$this->lang->line('System Dashboard'),
                'set_custom_link_target'=>'_blank'
            )
        );
        else
        $custom_link=array
        (
            0=>array
            (
                'set_custom_link_pull_right'=>true,
                'set_custom_link_url'=>base_url('admin/user_log'),
                'set_custom_link_hover'=>$this->lang->line('User Login Log'),
                'set_custom_link_btn_class'=>'btn btn-info',
                'set_custom_link_fa_class'=>'fa fa-history',
                'set_custom_link_label'=>$this->lang->line('User Login Log')
            )
        );
        $set_custom_link=$this->session->set_userdata('set_custom_link',$custom_link);

        $output = $crud->render();
        $data['output']=$output;
        $data['page_title'] = $this->lang->line("user management");
        $data['crud']=1;

        $this->_viewcontroller($data);
    }

    public function delete_everything_after_user_delete($primary_key)
    {
        if($primary_key == 1)
        {
            $this->basic->update_data('users',array('id'=>$primary_key),array('deleted'=>'0'));
            return true;
        }
        else
        {
            $this->db->trans_start();
            $sql = "show tables;";
            $a = $this->basic->execute_query($sql);
            foreach($a as $value)
            {
                foreach($value as $table_name)
                {
                    if($table_name == 'users') $this->basic->delete_data('users',array('id'=>$primary_key));
                    if($table_name == 'view_usage_log') continue;
                    if($this->db->field_exists('user_id',$table_name))
                        $this->basic->delete_data($table_name,array('user_id'=>$primary_key));
                }
            }
            $this->db->trans_complete(); 
            return true;
        }
    }


    public function user_log()
    {        
        $custom_link=array
        (
            0=>array
            (
                'set_custom_link_pull_right'=>false,
                'set_custom_link_url'=>base_url('admin/user_management'),
                'set_custom_link_hover'=>$this->lang->line('Back'),
                'set_custom_link_btn_class'=>'btn btn-default',
                'set_custom_link_fa_class'=>'fa fa-arrow-left',
                'set_custom_link_label'=>$this->lang->line('Back')
            ),
            1=>array
            (
                'set_custom_link_pull_right'=>true,
                'set_custom_link_url'=>base_url('admin/delete_user_log'),
                'set_custom_link_hover'=>$this->lang->line('Delete last 30 days data'),
                'set_custom_link_btn_class'=>'btn btn-danger are_you_sure',
                'set_custom_link_fa_class'=>'fa fa-trash',
                'set_custom_link_label'=>$this->lang->line('Delete last 30 days data')
            )
        );
        $set_custom_link=$this->session->set_userdata('set_custom_link',$custom_link);

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('user_login_info');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line("User Login Log"));

        $crud->fields('user_name', 'user_email', 'login_time', 'login_ip');
        $crud->columns('user_name', 'user_email', 'login_time', 'login_ip');

        $crud->display_as('user_name', $this->lang->line('user name'));
        $crud->display_as('user_email', $this->lang->line('email'));
        $crud->display_as('login_time', $this->lang->line('Last Logged in'));
        $crud->display_as('login_ip', $this->lang->line('Login ip'));
        $crud->callback_column('login_time', array($this, 'date_display_crud'));
        $crud->add_action($this->lang->line('details'), '', '','fa fa-eye',array($this,'edit_user'));

        if($this->is_demo != '1')
        $crud->add_action($this->lang->line('details'), '', '','fa fa-eye',array($this,'edit_user'));

        if($this->is_demo == '1')
        $crud->callback_column('user_email', array($this, 'email_display_crud'));

        $crud->unset_add();
        $crud->unset_read();
        $crud->unset_print();
        $crud->unset_edit();
        $crud->unset_export();
        $crud->unset_delete();

        $output = $crud->render();
        $data['output']=$output;
        $data['page_title'] = $this->lang->line("User Login Log");
        $data['crud']=1;

        $this->_viewcontroller($data);
    }

    public function delete_user_log()
    {       
        $table_name = "user_login_info";
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));
        $from_date = $from_date." 23:59:59";
        $where = array('login_time <' => $from_date);
        $this->basic->delete_data($table_name,$where);
        $this->session->set_flashdata('delete_success',1);
        redirect('admin/user_log','location');
    }




    public function change_user_password_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        $id = $this->session->userdata('change_user_password_id');
        // $this->session->unset_userdata('change_member_password_id');
        if ($_POST) {
            $this->form_validation->set_rules('password', '<b>'. $this->lang->line("password").'</b>', 'trim|required');
            $this->form_validation->set_rules('confirm_password', '<b>'. $this->lang->line("confirm password").'</b>', 'trim|required|matches[password]');
        }
        if ($this->form_validation->run() == false) {
            $this->change_user_password($id);
        } else {
            $new_password = $this->input->post('password', true);
            $new_confirm_password = $this->input->post('confirm_password', true);

            $table_change_password = 'users';
            $where_change_passwor = array('id' => $id);
            $data = array('password' => md5($new_password));
            $this->basic->update_data($table_change_password, $where_change_passwor, $data);

            $where['where'] = array('id' => $id);
            $mail_info = $this->basic->get_data('users', $where);
            
            $name = $mail_info[0]['name'];
            $to = $mail_info[0]['email'];
            $password = $new_password;

            $mask = $this->config->item('product_name');
            $from = $this->config->item('institute_email');
            $url = site_url();


            $email_template_info = $this->basic->get_data('email_template_management',array('where'=>array('template_type'=>'change_password')),array('subject','message'));

            if(isset($email_template_info[0]) && $email_template_info[0]['subject'] != '' && $email_template_info[0]['message'] != '') {

                $subject = $email_template_info[0]['subject'];
                $message = str_replace(array("#USERNAME#","#APP_URL#","#APP_NAME#","#NEW_PASSWORD#"),array($name,$url,$mask,$password),$email_template_info[0]['message']);

            } else {

                $subject = 'Change Password Notification';
                $message = "Dear {$name},<br/> Your <a href='".$url."'>{$mask}</a> password has been changed. Your new password is: {$password}.<br/><br/> Thank you.";

            }

            
            $this->_mail_sender($from, $to, $subject, $message, $mask);
            $this->session->set_flashdata('success_message', 1);
                // return $this->config_member();
            redirect('admin/user_management', 'location');
        }
    }



    function unique_email_check($str, $edited_id)
    {
        $email= strip_tags(trim($this->input->post('email',TRUE)));
        if($email==""){
            $s= $this->lang->line("required");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
        }
        
        if(!isset($edited_id) || !$edited_id)
            $where=array("email"=>$email);
        else        
            $where=array("email"=>$email,"id !="=>$edited_id);
        
        
        $is_unique=$this->basic->is_unique("users",$where,$select='');
        
        if (!$is_unique) {
            $s = $this->lang->line("is_unique");
            $s=str_replace("<b>%s</b>","",$s);
            $s="<b>".$this->lang->line("email")."</b> ".$s;
            $this->form_validation->set_message('unique_email_check', $s);
            return FALSE;
            }
                
        return TRUE;
    }

 
    public function email_display_crud($value, $row)
    {
        return substr($value, 0, 3)."***@***".substr($value, -3);
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
            return "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('active')."</sapn>";
        } else {
            return "<span class='label label-light'><i class='fa fa-remove red'></i> ".$this->lang->line('inactive')."</sapn>";
        }
    }


    public function expired_date_field_crud($value, $row)
    {
        if ($value == '0000-00-00 00:00:00') {
            $value = "";
        }
        else $value=date("Y-m-d",strtotime($value));
        return '<input id="field-expired_date" type="text" maxlength="100" value="'.$value.'" name="expired_date">';
    }

    public function expired_date_display_crud($value, $row)
    {
        if($row->user_type=="Admin") return "N/A";
        if ($value == '0000-00-00 00:00:00') {
            $value = "-";
        }
        else $value=date("d M,y",strtotime($value));
        return $value;
    }

    public function date_display_crud($value, $row)
    {
        if ($value == '0000-00-00 00:00:00') 
        $value = "-";       
        else $value=date("d M,y H:i",strtotime($value));
        return $value;
    }


    // public function dateDifference($then)
    // {
    //     $then = new DateTime($then);         
    //     $now = new DateTime();         
    //     $sinceThen = $then->diff($now);    
    //     $ret=""; 
    //     if($sinceThen->y>0)
    //     $ret.=$sinceThen->y.' year ';   
    //     $ret.=$sinceThen->m.' mon '.$sinceThen->d.' day';    
    //     return $ret;
    // }

   

 

    public function encript_password($post_array, $primary_key)
    {
        $id = $primary_key;
        $where = array('id'=>$id);
        $password = md5($post_array['password']);
        $table = 'users';
        $data = array('password'=>$password);
        $this->basic->update_data($table, $where, $data);
        return true;
    }


 
    public function change_user_password($id)
    {
        $this->session->set_userdata('change_user_password_id', $id);

        $table = 'users';
        $where['where'] = array('id' => $id);

        $info = $this->basic->get_data($table, $where);

        $data['user_name'] = $info[0]['name'];

        $data['body'] = 'admin/user/change_user_password';
        $data['page_title'] =  $this->lang->line("change password");
        $this->_viewcontroller($data);
    }


    function edit_user($primary_key , $row)
    {
        return site_url('admin/user_management/edit/'.$row->user_id);
    }




    public function notify_members()
    {
        $data['body'] = 'admin/notification/notify_members';
        $data['page_title'] = $this->lang->line("send email to users");
        $this->_viewcontroller($data);
    }

    public function notify_members_data_loader()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
        $order_by_str = $sort." ".$order;

        // setting properties for search
        $first_name = trim($this->input->post('first_name', true));

        $is_searched= $this->input->post('is_searched', true);


        if ($is_searched) {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('notify_member_first_name', $first_name);
        }
        // saving session data to different search parameter variables
        $search_first_name = $this->session->userdata('notify_member_first_name');

        // creating a blank where_simple array
        $where_simple = array();

        // trimming data
        if ($search_first_name) {
            $where_simple['name like'] = $search_first_name."%";
        }

        $where_simple['deleted'] = '0';
        // $where_simple['user_type !='] = 'Admin';

        $where = array('where' => $where_simple);
        $offset = ($page-1)*$rows;
        $result = array();

        $table = "users";
        $info = $this->basic->get_data($table, $where, $select = '', $join='', $limit = $rows, $start = $offset, $order_by = $order_by_str);

        $total_rows_array = $this->basic->count_row($table, $where, $count = "id");
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }

    public function send_email_member()
    {   
        if($this->is_demo == '1')
        {
            echo "<b style='text-align:center;color:red;border:1px solid red; padding: 10px'>Notification sending is disabled in this demo.</b>";
            exit();
        }
        if($_POST)
        {
            $subject= $this->input->post('subject');
            $content= $this->input->post('content');
            $info=$this->input->post('info');
            $info=json_decode($info,TRUE);
            $count=0;
            
           foreach($info as $member)
            {               
                $email=$member['email'];
                $member_id=$member['id'];                
                $message=$content;
                $from=$this->config->item('institute_email');
                $to=$email;
                $mask=$this->config->item('institute_address1');
                
                if($message=="" || $from=="" || $to=="" || $subject=="") continue;

                if($this->_mail_sender($from,$to,$subject,$message,$mask))  $count++;
               
            }
            echo "<b> $count / ".count($info)." : ".$this->lang->line("email sent successfully")."</b>";
           
        }   
    }


    public function scanAll($myDir)
    {

        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $dirTree[$i]['size'] = $file_size;
            $dirTree[$i]['time'] =date("Y-m-d H:i:s",$file_modification_time);

            $i++;

        }

        return $dirTree;
    }


    public function delete_junk_file()
    {
        $path=FCPATH."download";
        $dirTree=$this->scanAll($path);

        $number_of_deleted_files = 0;
        $deleted_file_size = 0;
        $todate = date("Y-m-d");
        $previous_day = date("Y-m-d", strtotime("$todate - 3 Days"));
        $last_time = strtotime($previous_day);
        foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
            if($junk_file_created_time <= $last_time && !strpos($value['file'],'index.html')){
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
            }
        }

        $path=FCPATH."upload";
        $dirTree=$this->scanAll($path);
        foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
            if($junk_file_created_time <= $last_time && !strpos($value['file'],'index.html')){
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
            }
        }

        $path=FCPATH."upload_caster";
        $dirTree=$this->scanAll($path);
        $previous_day2 = date("Y-m-d", strtotime("$todate - 30 Days"));
        $last_time2 = strtotime($previous_day2);
        foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
            if($junk_file_created_time <= $last_time2 && !strpos($value['file'],'index.html')){
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
            }
        }

        if($deleted_file_size != 0) $data['total_file_size'] = number_format($deleted_file_size/1024, 2);
        else $data['total_file_size'] = $deleted_file_size;

        $data['total_files'] = $number_of_deleted_files;
        $data['body'] = "admin/junk_file_delete/delete_junk_file";
        $data['page_title'] = $this->lang->line("delete junk files");
        $this->_viewcontroller($data);      
        

   }

   public function delete_junk_file_action()
   {
       $path=FCPATH."download";
       $dirTree=$this->scanAll($path);
       $number_of_deleted_files = 0;
       $deleted_file_size = 0;
       $todate = date("Y-m-d");
       $previous_day = date("Y-m-d", strtotime("$todate - 3 Days"));
       $last_time = strtotime($previous_day);
       foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
           if($junk_file_created_time <= $last_time  && !strpos($value['file'],'index.html')){
                unlink($value['file']);
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
           }
       }

       $path=FCPATH."upload";
       $dirTree=$this->scanAll($path);
       foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
           if($junk_file_created_time <= $last_time  && !strpos($value['file'],'index.html')){
                unlink($value['file']);
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
           }
       }

        $path=FCPATH."upload_caster";
        $dirTree=$this->scanAll($path);
        $previous_day2 = date("Y-m-d", strtotime("$todate - 30 Days"));
        $last_time2 = strtotime($previous_day2);
        foreach ($dirTree as $value) {
            $junk_file_created_time = strtotime($value['time']);
            if($junk_file_created_time <= $last_time2 && !strpos($value['file'],'index.html')){
                unlink($value['file']);
                $number_of_deleted_files++;
                $deleted_file_size = $deleted_file_size+$value['size'];
            }
        }


       if($deleted_file_size != 0) $deleted_file_size = number_format($deleted_file_size/1024, 2);
       else $deleted_file_size = $deleted_file_size;
       
       echo "<p class='text-danger'>You have successfully deleted $number_of_deleted_files junk files ($deleted_file_size KB)</p>";
   }



   public function user_dashboard($id)
   {
       if($this->session->userdata('user_type')=='Member') exit();
       if($this->session->userdata('license_type') != 'double') exit();
       
       $user_id = $id; 
       $user_info = $this->basic->get_data('users',array('where'=>array('id'=>$id)));
       $data['user_name'] = $user_info[0]['name'];
       $data['user_email'] = $user_info[0]['email'];

       $today = date("Y-m-d");
       $from_date = date("Y-m-d", strtotime("$today - 7 days"));
       $auto_post_info = $this->basic->get_data("facebook_rx_auto_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$user_id)),array('id','last_updated_at','post_type','campaign_name','posting_status','post_url'));
       $post_publish_info = array();
       foreach ($auto_post_info as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
           if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']]++;
           if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']] = 1;
       }


       $slider_post_info = $this->basic->get_data("facebook_rx_slider_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$user_id)),array('id','last_updated_at','post_type','campaign_name','posting_status','post_url'));
       foreach ($slider_post_info as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
           if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']]++;
           if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']] = 1;
       }

       $cta_post_info = $this->basic->get_data("facebook_rx_cta_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$user_id)),array('id','last_updated_at','cta_type','campaign_name','posting_status','post_url'));
       foreach ($cta_post_info as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
           if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date]['cta_post']))
               $post_publish_info[$formated_date]['cta_post']++;
           if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date]['cta_post']))
               $post_publish_info[$formated_date]['cta_post'] = 1;
       }

       $data['today_text_post'] = isset($post_publish_info[$today]['text_submit']) ? $post_publish_info[$today]['text_submit'] : 0;
       $data['today_image_post'] = isset($post_publish_info[$today]['image_submit']) ? $post_publish_info[$today]['image_submit'] : 0;
       $data['today_link_post'] = isset($post_publish_info[$today]['link_submit']) ? $post_publish_info[$today]['link_submit'] : 0;
       $data['today_video_post'] = isset($post_publish_info[$today]['video_submit']) ? $post_publish_info[$today]['video_submit'] : 0;
       $data['today_carousel_post'] = isset($post_publish_info[$today]['carousel_post']) ? $post_publish_info[$today]['carousel_post'] : 0;
       $data['today_slider_post'] = isset($post_publish_info[$today]['slider_post']) ? $post_publish_info[$today]['slider_post'] : 0;
       $data['today_cta_post'] = isset($post_publish_info[$today]['cta_post']) ? $post_publish_info[$today]['cta_post'] : 0;
       $data['today_post_publish'] = $data['today_text_post'] + $data['today_image_post'] + $data['today_link_post'] + $data['today_video_post'] + $data['today_carousel_post'] + $data['today_slider_post'] + $data['today_cta_post'];

       $all_time_message_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $user_id)),array('sum(successfully_sent) as total_sent_message'));
       $data['all_time_message_sent'] = $all_time_message_info[0]['total_sent_message'];

       $all_time_auto_reply_info = $this->basic->get_data('facebook_ex_autoreply',array('where' => array('user_id' => $user_id)),array('sum(auto_private_reply_count) as total_private_reply','sum(auto_comment_reply_count) as total_comment_reply'));
       $data['all_time_private_reply_sent'] = $all_time_auto_reply_info[0]['total_private_reply'];
       $data['all_time_comment_reply_sent'] = $all_time_auto_reply_info[0]['total_comment_reply'];

       

       $all_time_auto_post = $this->basic->get_data('facebook_rx_auto_post',array('where' => array('user_id' => $user_id, 'posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
       $all_time_cta_post = $this->basic->get_data('facebook_rx_cta_post',array('where' => array('user_id' => $user_id, 'posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
       $all_time_slider_post = $this->basic->get_data('facebook_rx_slider_post',array('where' => array('user_id' => $user_id, 'posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
       $data['all_time_post'] = count($all_time_auto_post) + count($all_time_cta_post) + count($all_time_slider_post);

       $recently_completed_post_array = array();
       $i=0;
       foreach($all_time_auto_post as $value)
       {
           $recently_completed_post_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($all_time_cta_post as $value)
       {
           $recently_completed_post_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($all_time_slider_post as $value)
       {
           $recently_completed_post_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       usort($recently_completed_post_array, function($a, $b) {
           if ($a['last_updated_at'] == $b['last_updated_at'])
           return 0;
           else if ($a['last_updated_at'] < $b['last_updated_at'])
           return 1;
           else
           return -1;
       });
       $data['recently_completed_post_array'] = $recently_completed_post_array;

    

       $data['today_post_publishing_comparison'] = array(
           0 => array(
               "value" => $data['today_image_post'],
               "color" => "#FF4D7B",
               "highlight" => "#FF4D7B",
               "label" => $this->lang->line('Image Post'),
               ),
           1 => array(
               "value" => $data['today_video_post'],
               "color" => "#144676",
               "highlight" => "#144676",
               "label" => $this->lang->line('Video Post'),
               ),
           2 => array(
               "value" => $data['today_link_post'],
               "color" => "#F9A602",
               "highlight" => "#F9A602",
               "label" => $this->lang->line('Link Post'),
               ),
           3 => array(
               "value" => $data['today_text_post'],
               "color" => "#639CDE",
               "highlight" => "#639CDE",
               "label" => $this->lang->line('Text Post'),
               ),
           4 => array(
               "value" => $data['today_carousel_post'],
               "color" => "#00A65A",
               "highlight" => "#00A65A",
               "label" => $this->lang->line('Carousel Post'),
               ),
           5 => array(
               "value" => $data['today_slider_post'],
               "color" => "#545096",
               "highlight" => "#545096",
               "label" => $this->lang->line('Slider Post'),
               ),
           6 => array(
               "value" => $data['today_cta_post'],
               "color" => "#0A70E3",
               "highlight" => "#0A70E3",
               "label" => $this->lang->line('CTA Post'),
               )
           );


       $today_post_publishing_comparison_li="";
       $area_chart_color_list="";
       $last_elem=count($data['today_post_publishing_comparison']);
       $loop_counter=0;
       foreach($data['today_post_publishing_comparison']  as $key => $value) 
       {
           $loop_counter++;
           $today_post_publishing_comparison_li.='<li style="line-height:27px;"><i class="fa fa-circle" style="color: '.$value['color'].';"></i> '.$value['label'].'</li>';
           $area_chart_color_list.='"'.$value['color'].'"';
           if($loop_counter!=$last_elem) $area_chart_color_list.=',';
       }
       $data['today_post_publishing_comparison_li']=$today_post_publishing_comparison_li;
       $data['area_chart_color_list']=$area_chart_color_list;


       $message_sent_info_get = $this->basic->get_data("facebook_ex_conversation_campaign",array('where'=>array('date_format(completed_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$user_id)),array('id','completed_at','campaign_type','campaign_name','posting_status','successfully_sent'));
       $message_sent_info = array();

       foreach ($message_sent_info_get as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['completed_at']));
           if(!isset($message_sent_info[$formated_date]['successfully_sent'])) $message_sent_info[$formated_date]['successfully_sent'] = 0;
           $message_sent_info[$formated_date]['successfully_sent'] = $message_sent_info[$formated_date]['successfully_sent']+$value['successfully_sent'];
       }


       $autoreply_info_get = $this->basic->get_data("facebook_ex_autoreply",array('where'=>array('date_format(last_reply_time,"%Y-%m-%d") >='=>$from_date,'user_id'=>$user_id)),array('id','post_thumb','post_id','auto_reply_campaign_name','auto_reply_done_info'));
       $autoreply_sent_info = array();

       foreach ($autoreply_info_get as $key => $value) {
           $reply_info = json_decode($value['auto_reply_done_info'],true);
           foreach ($reply_info as $key2 => $value2) {
               $formated_date = date("Y-m-d",strtotime($value2['reply_time']));
               if(strtotime($formated_date) < strtotime($from_date)) continue;
               if(isset($autoreply_sent_info[$formated_date]))
               {
                   if($value2['reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['private_reply']++;
                   if($value2['comment_reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['comment_reply']++;
               }
               else
               {
                   if($value2['reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['private_reply'] = 1;
                   else
                   $autoreply_sent_info[$formated_date]['private_reply'] = 0;

                   if($value2['comment_reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['comment_reply'] = 1;
                   else
                   $autoreply_sent_info[$formated_date]['comment_reply'] = 0;
               }

           }
       }


       $dDiff = strtotime($today) - strtotime($from_date);
       $no_of_days = floor($dDiff/(60*60*24));

       $seven_days_message_sent_chart_data = array();
       $seven_days_comment_reply_chart_data = array();
       $seven_days_private_reply_chart_data = array();
       $seven_days_post_publish_chart_data = array();
       $data['today_message_sent'] = isset($message_sent_info[$today]['successfully_sent']) ? $message_sent_info[$today]['successfully_sent'] : 0;
       $data['today_comment_reply_sent'] = isset($autoreply_sent_info[$today]['comment_reply']) ? $autoreply_sent_info[$today]['comment_reply'] : 0;
       $data['today_private_reply_sent'] = isset($autoreply_sent_info[$today]['private_reply']) ? $autoreply_sent_info[$today]['private_reply'] : 0;
       $data['last_sevendays_message_sent'] = 0;
       $data['last_sevendays_comment_reply_sent'] = 0;
       $data['last_sevendays_private_reply_sent'] = 0;
       $data['last_sevendays_post_publish'] = 0;

       for($i=0;$i<=$no_of_days;$i++){

           $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
           if(isset($message_sent_info[$day_count])){
               $seven_days_message_sent_chart_data[$i]['message_sent'] = $message_sent_info[$day_count]['successfully_sent'];
               $data['last_sevendays_message_sent'] += $message_sent_info[$day_count]['successfully_sent'];
           } else {
               $seven_days_message_sent_chart_data[$i]['message_sent'] = 0;
           }
           $seven_days_message_sent_chart_data[$i]['date'] = $day_count;

           if(isset($autoreply_sent_info[$day_count])){
               $seven_days_comment_reply_chart_data[$i]['comment_reply'] = $autoreply_sent_info[$day_count]['comment_reply'];
               $data['last_sevendays_comment_reply_sent'] += $autoreply_sent_info[$day_count]['comment_reply'];
           } else {
               $seven_days_comment_reply_chart_data[$i]['comment_reply'] = 0;
           }
           $seven_days_comment_reply_chart_data[$i]['date'] = $day_count;

           if(isset($autoreply_sent_info[$day_count])){
               $seven_days_private_reply_chart_data[$i]['private_reply'] = $autoreply_sent_info[$day_count]['private_reply'];
               $data['last_sevendays_private_reply_sent'] += $autoreply_sent_info[$day_count]['private_reply'];
           } else {
               $seven_days_private_reply_chart_data[$i]['private_reply'] = 0;
           }
           $seven_days_private_reply_chart_data[$i]['date'] = $day_count;


           if(isset($post_publish_info[$day_count])){
               $seven_days_post_publish_chart_data[$i]['image_post'] = isset($post_publish_info[$day_count]['image_submit']) ? $post_publish_info[$day_count]['image_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['video_post'] = isset($post_publish_info[$day_count]['video_submit']) ? $post_publish_info[$day_count]['video_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['link_post'] = isset($post_publish_info[$day_count]['link_submit']) ? $post_publish_info[$day_count]['link_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['text_post'] = isset($post_publish_info[$day_count]['text_submit']) ? $post_publish_info[$day_count]['text_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['carousel_post'] = isset($post_publish_info[$day_count]['carousel_post']) ? $post_publish_info[$day_count]['carousel_post'] : 0;
               $seven_days_post_publish_chart_data[$i]['slider_post'] = isset($post_publish_info[$day_count]['slider_post']) ? $post_publish_info[$day_count]['slider_post'] : 0;
               $seven_days_post_publish_chart_data[$i]['cta_post'] = isset($post_publish_info[$day_count]['cta_post']) ? $post_publish_info[$day_count]['cta_post'] : 0;

               $data['last_sevendays_post_publish'] = $data['last_sevendays_post_publish'] + $seven_days_post_publish_chart_data[$i]['image_post'] + $seven_days_post_publish_chart_data[$i]['video_post'] + $seven_days_post_publish_chart_data[$i]['link_post'] + $seven_days_post_publish_chart_data[$i]['text_post'] + $seven_days_post_publish_chart_data[$i]['carousel_post'] + $seven_days_post_publish_chart_data[$i]['slider_post'] + $seven_days_post_publish_chart_data[$i]['cta_post'];
           } else {
               $seven_days_post_publish_chart_data[$i]['image_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['video_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['link_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['text_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['carousel_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['slider_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['cta_post'] = 0;
           }
           $seven_days_post_publish_chart_data[$i]['date'] = $day_count;
       }

       $data['seven_days_post_publish_chart_data'] = $seven_days_post_publish_chart_data;
       $data['seven_days_private_reply_chart_data'] = $seven_days_private_reply_chart_data;
       $data['seven_days_comment_reply_chart_data'] = $seven_days_comment_reply_chart_data;
       $data['seven_days_message_sent_chart_data'] = $seven_days_message_sent_chart_data;


       $recently_message_sent_completed_campaing_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $user_id,'posting_status' => '2')),$select='',$join='',$limit='5',$start=NULL,'added_at DESC');
       
       $upcoming_message_sent_campaign_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $user_id,'posting_status' => '0')),$select='',$join='',$limit='5',$start=NULL,'added_at DESC');

       $data['recently_message_sent_completed_campaing_info'] = $recently_message_sent_completed_campaing_info;
       $data['upcoming_message_sent_campaign_info'] = $upcoming_message_sent_campaign_info;

       $subscriber_info = $this->basic->get_data('facebook_rx_conversion_user_list',array('where' => array('user_id' => $user_id,'permission'=>'1')),array('id'));
       $data['total_subscribers'] = count($subscriber_info);

       $auto_reply_enable = $this->basic->get_data('facebook_ex_autoreply',array('where' => array('user_id' => $user_id)),array('count(id) as auto_reply_enable'));
       $data['total_auto_reply_enabled_campaign'] = $auto_reply_enable[0]['auto_reply_enable'];


       $scheduled_bulk_message_campaign = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $user_id,'posting_status' => '0')),array('count(id) as scheduled_bulk_message_campaign'));
       $data['scheduled_bulk_message_campaign'] = $scheduled_bulk_message_campaign[0]['scheduled_bulk_message_campaign'];



       $scheduled_auto_post_campaign = $this->basic->get_data('facebook_rx_auto_post',array('where' => array('user_id' => $user_id,'posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
       $scheduled_cta_post_campaign = $this->basic->get_data('facebook_rx_cta_post',array('where' => array('user_id' => $user_id,'posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
       $scheduled_carousel_slider_campaign = $this->basic->get_data('facebook_rx_slider_post',array('where' => array('user_id' => $user_id,'posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
       $data['scheduled_posting_campaign'] = count($scheduled_auto_post_campaign) + count($scheduled_cta_post_campaign) + count($scheduled_carousel_slider_campaign);

       $upcoming_post_campaign_array = array();
       $i=0;
       foreach($scheduled_auto_post_campaign as $value)
       {
           $upcoming_post_campaign_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($scheduled_cta_post_campaign as $value)
       {
           $upcoming_post_campaign_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($scheduled_carousel_slider_campaign as $value)
       {
           $upcoming_post_campaign_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       usort($upcoming_post_campaign_array, function($a, $b) {
           if ($a['schedule_time'] == $b['schedule_time'])
           return 0;
           else if ($a['schedule_time'] > $b['schedule_time'])
           return 1;
           else
           return -1;
       });

       $data['upcoming_post_campaign_array'] = $upcoming_post_campaign_array;


       $last_auto_reply_post_info = $this->basic->get_data('facebook_ex_autoreply',array('where' => array('user_id' => $user_id)),$select='auto_reply_done_info,post_description',$join='',$limit='3',$start=NULL,'last_reply_time DESC');        

       $i=0;
       $array1=array();
       foreach ($last_auto_reply_post_info as $key => $value) 
       {
           $decode = json_decode($value['auto_reply_done_info'],true);
           
           foreach ($decode as $key2 => $value2) 
           {
               $array1[$i] = $value2;

               $pieces = explode(" ", $value['post_description']);
               $post_name_data = implode(" ", array_splice($pieces, 0, 5));

               $array1[$i]['post_name'] =  $post_name_data."...";
               $i++;
           }
       }

       $ord = array();
       foreach ($array1 as $key => $value){
           $ord[] = strtotime($value['reply_time']);
       }
       array_multisort($ord, SORT_DESC, $array1);
       $firstFiveElements = array_slice($array1, 0, 5);
       $data['my_last_auto_reply_data'] = $firstFiveElements;

       $data['body'] = 'facebook_ex/dashboard2';
       $data['page_title'] = $this->lang->line('Dashboard');
       $this->_viewcontroller($data);

   }

   public function overall_dashboard()
   {
       if($this->session->userdata('user_type')=='Member') exit();
       if($this->session->userdata('license_type') != 'double') exit();

       $data['overall_dashboard'] = 'yes'; 
       $today = date("Y-m-d");
       $from_date = date("Y-m-d", strtotime("$today - 7 days"));
       $auto_post_info = $this->basic->get_data("facebook_rx_auto_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date)),array('id','last_updated_at','post_type','campaign_name','posting_status','post_url'));
       $post_publish_info = array();
       foreach ($auto_post_info as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
           if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']]++;
           if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']] = 1;
       }


       $slider_post_info = $this->basic->get_data("facebook_rx_slider_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date)),array('id','last_updated_at','post_type','campaign_name','posting_status','post_url'));
       foreach ($slider_post_info as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
           if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']]++;
           if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date][$value['post_type']]))
               $post_publish_info[$formated_date][$value['post_type']] = 1;
       }


       $cta_post_info = $this->basic->get_data("facebook_rx_cta_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date)),array('id','last_updated_at','cta_type','campaign_name','posting_status','post_url'));
       foreach ($cta_post_info as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
           if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date]['cta_post']))
               $post_publish_info[$formated_date]['cta_post']++;
           if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date]['cta_post']))
               $post_publish_info[$formated_date]['cta_post'] = 1;
       }

       $data['today_text_post'] = isset($post_publish_info[$today]['text_submit']) ? $post_publish_info[$today]['text_submit'] : 0;
       $data['today_image_post'] = isset($post_publish_info[$today]['image_submit']) ? $post_publish_info[$today]['image_submit'] : 0;
       $data['today_link_post'] = isset($post_publish_info[$today]['link_submit']) ? $post_publish_info[$today]['link_submit'] : 0;
       $data['today_video_post'] = isset($post_publish_info[$today]['video_submit']) ? $post_publish_info[$today]['video_submit'] : 0;
       $data['today_carousel_post'] = isset($post_publish_info[$today]['carousel_post']) ? $post_publish_info[$today]['carousel_post'] : 0;
       $data['today_slider_post'] = isset($post_publish_info[$today]['slider_post']) ? $post_publish_info[$today]['slider_post'] : 0;
       $data['today_cta_post'] = isset($post_publish_info[$today]['cta_post']) ? $post_publish_info[$today]['cta_post'] : 0;
       $data['today_post_publish'] = $data['today_text_post'] + $data['today_image_post'] + $data['today_link_post'] + $data['today_video_post'] + $data['today_carousel_post'] + $data['today_slider_post'] + $data['today_cta_post'];

       $all_time_message_info = $this->basic->get_data('facebook_ex_conversation_campaign','',array('sum(successfully_sent) as total_sent_message'));
       $data['all_time_message_sent'] = $all_time_message_info[0]['total_sent_message'];

       $all_time_auto_reply_info = $this->basic->get_data('facebook_ex_autoreply','',array('sum(auto_private_reply_count) as total_private_reply','sum(auto_comment_reply_count) as total_comment_reply'));
       $data['all_time_private_reply_sent'] = $all_time_auto_reply_info[0]['total_private_reply'];
       $data['all_time_comment_reply_sent'] = $all_time_auto_reply_info[0]['total_comment_reply'];

       

       $all_time_auto_post = $this->basic->get_data('facebook_rx_auto_post',array('where' => array('posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
       $all_time_cta_post = $this->basic->get_data('facebook_rx_cta_post',array('where' => array('posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
       $all_time_slider_post = $this->basic->get_data('facebook_rx_slider_post',array('where' => array('posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
       $data['all_time_post'] = count($all_time_auto_post) + count($all_time_cta_post) + count($all_time_slider_post);

       $recently_completed_post_array = array();
       $i=0;
       foreach($all_time_auto_post as $value)
       {
           $recently_completed_post_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($all_time_cta_post as $value)
       {
           $recently_completed_post_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($all_time_slider_post as $value)
       {
           $recently_completed_post_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       usort($recently_completed_post_array, function($a, $b) {
           if ($a['last_updated_at'] == $b['last_updated_at'])
           return 0;
           else if ($a['last_updated_at'] < $b['last_updated_at'])
           return 1;
           else
           return -1;
       });
       $data['recently_completed_post_array'] = $recently_completed_post_array;

    

       $data['today_post_publishing_comparison'] = array(
           0 => array(
               "value" => $data['today_image_post'],
               "color" => "#FF4D7B",
               "highlight" => "#FF4D7B",
               "label" => $this->lang->line('Image Post'),
               ),
           1 => array(
               "value" => $data['today_video_post'],
               "color" => "#144676",
               "highlight" => "#144676",
               "label" => $this->lang->line('Video Post'),
               ),
           2 => array(
               "value" => $data['today_link_post'],
               "color" => "#F9A602",
               "highlight" => "#F9A602",
               "label" => $this->lang->line('Link Post'),
               ),
           3 => array(
               "value" => $data['today_text_post'],
               "color" => "#639CDE",
               "highlight" => "#639CDE",
               "label" => $this->lang->line('Text Post'),
               ),
           4 => array(
               "value" => $data['today_carousel_post'],
               "color" => "#00A65A",
               "highlight" => "#00A65A",
               "label" => $this->lang->line('Carousel Post'),
               ),
           5 => array(
               "value" => $data['today_slider_post'],
               "color" => "#545096",
               "highlight" => "#545096",
               "label" => $this->lang->line('Slider Post'),
               ),
           6 => array(
               "value" => $data['today_cta_post'],
               "color" => "#0A70E3",
               "highlight" => "#0A70E3",
               "label" => $this->lang->line('CTA Post'),
               )
           );


       $today_post_publishing_comparison_li="";
       $area_chart_color_list="";
       $last_elem=count($data['today_post_publishing_comparison']);
       $loop_counter=0;
       foreach($data['today_post_publishing_comparison']  as $key => $value) 
       {
           $loop_counter++;
           $today_post_publishing_comparison_li.='<li style="line-height:27px;"><i class="fa fa-circle" style="color: '.$value['color'].';"></i> '.$value['label'].'</li>';
           $area_chart_color_list.='"'.$value['color'].'"';
           if($loop_counter!=$last_elem) $area_chart_color_list.=',';
       }
       $data['today_post_publishing_comparison_li']=$today_post_publishing_comparison_li;
       $data['area_chart_color_list']=$area_chart_color_list;


       $message_sent_info_get = $this->basic->get_data("facebook_ex_conversation_campaign",array('where'=>array('date_format(completed_at,"%Y-%m-%d") >='=>$from_date)),array('id','completed_at','campaign_type','campaign_name','posting_status','successfully_sent'));
       $message_sent_info = array();

       foreach ($message_sent_info_get as $key => $value) {
           $formated_date = date("Y-m-d",strtotime($value['completed_at']));
           if(!isset($message_sent_info[$formated_date]['successfully_sent'])) $message_sent_info[$formated_date]['successfully_sent'] = 0;
           $message_sent_info[$formated_date]['successfully_sent'] = $message_sent_info[$formated_date]['successfully_sent']+$value['successfully_sent'];
       }


       $autoreply_info_get = $this->basic->get_data("facebook_ex_autoreply",array('where'=>array('date_format(last_reply_time,"%Y-%m-%d") >='=>$from_date)),array('id','post_thumb','post_id','auto_reply_campaign_name','auto_reply_done_info'));
       $autoreply_sent_info = array();

       foreach ($autoreply_info_get as $key => $value) {
           $reply_info = json_decode($value['auto_reply_done_info'],true);
           foreach ($reply_info as $key2 => $value2) {
               $formated_date = date("Y-m-d",strtotime($value2['reply_time']));
               if(strtotime($formated_date) < strtotime($from_date)) continue;
               if(isset($autoreply_sent_info[$formated_date]))
               {
                   if($value2['reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['private_reply']++;
                   if($value2['comment_reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['comment_reply']++;
               }
               else
               {
                   if($value2['reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['private_reply'] = 1;
                   else
                   $autoreply_sent_info[$formated_date]['private_reply'] = 0;

                   if($value2['comment_reply_text'] != '')
                   $autoreply_sent_info[$formated_date]['comment_reply'] = 1;
                   else
                   $autoreply_sent_info[$formated_date]['comment_reply'] = 0;
               }

           }
       }


       $dDiff = strtotime($today) - strtotime($from_date);
       $no_of_days = floor($dDiff/(60*60*24));

       $seven_days_message_sent_chart_data = array();
       $seven_days_comment_reply_chart_data = array();
       $seven_days_private_reply_chart_data = array();
       $seven_days_post_publish_chart_data = array();
       $data['today_message_sent'] = isset($message_sent_info[$today]['successfully_sent']) ? $message_sent_info[$today]['successfully_sent'] : 0;
       $data['today_comment_reply_sent'] = isset($autoreply_sent_info[$today]['comment_reply']) ? $autoreply_sent_info[$today]['comment_reply'] : 0;
       $data['today_private_reply_sent'] = isset($autoreply_sent_info[$today]['private_reply']) ? $autoreply_sent_info[$today]['private_reply'] : 0;
       $data['last_sevendays_message_sent'] = 0;
       $data['last_sevendays_comment_reply_sent'] = 0;
       $data['last_sevendays_private_reply_sent'] = 0;
       $data['last_sevendays_post_publish'] = 0;

       for($i=0;$i<=$no_of_days;$i++){

           $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
           if(isset($message_sent_info[$day_count])){
               $seven_days_message_sent_chart_data[$i]['message_sent'] = $message_sent_info[$day_count]['successfully_sent'];
               $data['last_sevendays_message_sent'] += $message_sent_info[$day_count]['successfully_sent'];
           } else {
               $seven_days_message_sent_chart_data[$i]['message_sent'] = 0;
           }
           $seven_days_message_sent_chart_data[$i]['date'] = $day_count;

           if(isset($autoreply_sent_info[$day_count])){
               $seven_days_comment_reply_chart_data[$i]['comment_reply'] = $autoreply_sent_info[$day_count]['comment_reply'];
               $data['last_sevendays_comment_reply_sent'] += $autoreply_sent_info[$day_count]['comment_reply'];
           } else {
               $seven_days_comment_reply_chart_data[$i]['comment_reply'] = 0;
           }
           $seven_days_comment_reply_chart_data[$i]['date'] = $day_count;

           if(isset($autoreply_sent_info[$day_count])){
               $seven_days_private_reply_chart_data[$i]['private_reply'] = $autoreply_sent_info[$day_count]['private_reply'];
               $data['last_sevendays_private_reply_sent'] += $autoreply_sent_info[$day_count]['private_reply'];
           } else {
               $seven_days_private_reply_chart_data[$i]['private_reply'] = 0;
           }
           $seven_days_private_reply_chart_data[$i]['date'] = $day_count;


           if(isset($post_publish_info[$day_count])){
               $seven_days_post_publish_chart_data[$i]['image_post'] = isset($post_publish_info[$day_count]['image_submit']) ? $post_publish_info[$day_count]['image_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['video_post'] = isset($post_publish_info[$day_count]['video_submit']) ? $post_publish_info[$day_count]['video_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['link_post'] = isset($post_publish_info[$day_count]['link_submit']) ? $post_publish_info[$day_count]['link_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['text_post'] = isset($post_publish_info[$day_count]['text_submit']) ? $post_publish_info[$day_count]['text_submit'] : 0;
               $seven_days_post_publish_chart_data[$i]['carousel_post'] = isset($post_publish_info[$day_count]['carousel_post']) ? $post_publish_info[$day_count]['carousel_post'] : 0;
               $seven_days_post_publish_chart_data[$i]['slider_post'] = isset($post_publish_info[$day_count]['slider_post']) ? $post_publish_info[$day_count]['slider_post'] : 0;
               $seven_days_post_publish_chart_data[$i]['cta_post'] = isset($post_publish_info[$day_count]['cta_post']) ? $post_publish_info[$day_count]['cta_post'] : 0;

               $data['last_sevendays_post_publish'] = $data['last_sevendays_post_publish'] + $seven_days_post_publish_chart_data[$i]['image_post'] + $seven_days_post_publish_chart_data[$i]['video_post'] + $seven_days_post_publish_chart_data[$i]['link_post'] + $seven_days_post_publish_chart_data[$i]['text_post'] + $seven_days_post_publish_chart_data[$i]['carousel_post'] + $seven_days_post_publish_chart_data[$i]['slider_post'] + $seven_days_post_publish_chart_data[$i]['cta_post'];
           } else {
               $seven_days_post_publish_chart_data[$i]['image_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['video_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['link_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['text_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['carousel_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['slider_post'] = 0;
               $seven_days_post_publish_chart_data[$i]['cta_post'] = 0;
           }
           $seven_days_post_publish_chart_data[$i]['date'] = $day_count;
       }

       $data['seven_days_post_publish_chart_data'] = $seven_days_post_publish_chart_data;
       $data['seven_days_private_reply_chart_data'] = $seven_days_private_reply_chart_data;
       $data['seven_days_comment_reply_chart_data'] = $seven_days_comment_reply_chart_data;
       $data['seven_days_message_sent_chart_data'] = $seven_days_message_sent_chart_data;


       $recently_message_sent_completed_campaing_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('posting_status' => '2')),$select='',$join='',$limit='5',$start=NULL,'added_at DESC');
       
       $upcoming_message_sent_campaign_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('posting_status' => '0')),$select='',$join='',$limit='5',$start=NULL,'added_at DESC');

       $data['recently_message_sent_completed_campaing_info'] = $recently_message_sent_completed_campaing_info;
       $data['upcoming_message_sent_campaign_info'] = $upcoming_message_sent_campaign_info;

       $subscriber_info = $this->basic->get_data('facebook_rx_conversion_user_list',array('where' => array('permission'=>'1')),array('id'));
       $data['total_subscribers'] = count($subscriber_info);

       $auto_reply_enable = $this->basic->get_data('facebook_ex_autoreply','',array('count(id) as auto_reply_enable'));
       $data['total_auto_reply_enabled_campaign'] = $auto_reply_enable[0]['auto_reply_enable'];


       $scheduled_bulk_message_campaign = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('posting_status' => '0')),array('count(id) as scheduled_bulk_message_campaign'));
       $data['scheduled_bulk_message_campaign'] = $scheduled_bulk_message_campaign[0]['scheduled_bulk_message_campaign'];



       $scheduled_auto_post_campaign = $this->basic->get_data('facebook_rx_auto_post',array('where' => array('posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
       $scheduled_cta_post_campaign = $this->basic->get_data('facebook_rx_cta_post',array('where' => array('posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
       $scheduled_carousel_slider_campaign = $this->basic->get_data('facebook_rx_slider_post',array('where' => array('posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
       $data['scheduled_posting_campaign'] = count($scheduled_auto_post_campaign) + count($scheduled_cta_post_campaign) + count($scheduled_carousel_slider_campaign);

       $upcoming_post_campaign_array = array();
       $i=0;
       foreach($scheduled_auto_post_campaign as $value)
       {
           $upcoming_post_campaign_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($scheduled_cta_post_campaign as $value)
       {
           $upcoming_post_campaign_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       $i=0;
       foreach($scheduled_carousel_slider_campaign as $value)
       {
           $upcoming_post_campaign_array[] = $value;
           $i++;
           if($i == 5) break;
       }
       usort($upcoming_post_campaign_array, function($a, $b) {
           if ($a['schedule_time'] == $b['schedule_time'])
           return 0;
           else if ($a['schedule_time'] > $b['schedule_time'])
           return 1;
           else
           return -1;
       });

       $data['upcoming_post_campaign_array'] = $upcoming_post_campaign_array;


       $last_auto_reply_post_info = $this->basic->get_data('facebook_ex_autoreply','',$select='auto_reply_done_info,post_description',$join='',$limit='3',$start=NULL,'last_reply_time DESC');        

       $i=0;
       $array1=array();
       foreach ($last_auto_reply_post_info as $key => $value) 
       {
           $decode = json_decode($value['auto_reply_done_info'],true);
           
           foreach ($decode as $key2 => $value2) 
           {
               $array1[$i] = $value2;

               $pieces = explode(" ", $value['post_description']);
               $post_name_data = implode(" ", array_splice($pieces, 0, 5));

               $array1[$i]['post_name'] =  $post_name_data."...";
               $i++;
           }
       }

       $ord = array();
       foreach ($array1 as $key => $value){
           $ord[] = strtotime($value['reply_time']);
       }
       array_multisort($ord, SORT_DESC, $array1);
       $firstFiveElements = array_slice($array1, 0, 5);
       $data['my_last_auto_reply_data'] = $firstFiveElements;

       $data['body'] = 'facebook_ex/dashboard2';
       $data['page_title'] = $this->lang->line('Dashboard');
       $this->_viewcontroller($data);

   }



   public function activity_log()
   {
        if($this->session->userdata('user_type')=='Member') exit();
        if($this->session->userdata('license_type') != 'double') exit();

        $users_info = $this->basic->get_data('users','',array('name','email','id'));
        $users = array();
        foreach($users_info as $value)
        {
            $users[$value['id']]['name'] = $value['name'];
            $users[$value['id']]['email'] = $value['email']; 
        }

        $select = array('user_id','post_type','campaign_name','last_updated_at','post_url');
        $auto_post_info = $this->basic->get_data("facebook_rx_auto_post",'',$select,'',10,$start=NULL,$order_by='last_updated_at desc');
        
        $slider_post_info = $this->basic->get_data("facebook_rx_slider_post",'',$select,'',10,$start=NULL,$order_by='last_updated_at desc');

        $select = array('user_id','cta_type','campaign_name','last_updated_at','post_url');
        $cta_post_info = $this->basic->get_data("facebook_rx_cta_post",'',$select,'',10,$start=NULL,$order_by='last_updated_at desc');

        $posting_array = array_merge($auto_post_info,$slider_post_info,$cta_post_info);

        usort($posting_array, function($a, $b) {
            return strtotime($a['last_updated_at']) < strtotime($b['last_updated_at']);
        });
        $posting_report = array();
        $posting_counter = 0;
        foreach($posting_array as $value)
        {
            if(isset($value['cta_type']))
            $posting_report[$posting_counter]['post_type'] = "CTA [".$value['cta_type']."]";
            else
            {
                // if($value['post_type'] == '')
                $posting_report[$posting_counter]['post_type'] = $value['post_type'];
                
            }

            $posting_report[$posting_counter]['campaign_name'] = $value['campaign_name'];
            $posting_report[$posting_counter]['last_updated_at'] = $value['last_updated_at'];
            $posting_report[$posting_counter]['post_url'] = $value['post_url'];
            $posting_report[$posting_counter]['user_id'] = $value['user_id'];
            $posting_report[$posting_counter]['user_name'] = isset($users[$value['user_id']]['name']) ? $users[$value['user_id']]['name'] : "";
            $posting_report[$posting_counter]['user_email'] = isset($users[$value['user_id']]['email']) ? $users[$value['user_id']]['email'] : "";

            $posting_counter++;
            if($posting_counter == 10) break;

        } 

        $data['facebook_poster'] = $posting_report;

        $select = array('user_id','campaign_name','campaign_type','added_at','schedule_time','total_thread');
        $bulk_message_campaign = $this->basic->get_data("facebook_ex_conversation_campaign",'',$select,'',10,$start=NULL,$order_by='added_at desc');
        $bulk_message_campaign_report = array();
        $bulk_message_campaign_counter = 0;
        foreach($bulk_message_campaign as $value)
        {
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['campaign_name'] = $value['campaign_name'];
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['campaign_type'] = $value['campaign_type'];
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['added_at'] = $value['added_at'];
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['schedule_time'] = $value['schedule_time'];
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['total_thread'] = $value['total_thread'];
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['user_id'] = $value['user_id'];
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['user_name'] = isset($users[$value['user_id']]['name']) ? $users[$value['user_id']]['name'] : "";
            $bulk_message_campaign_report[$bulk_message_campaign_counter]['user_email'] = isset($users[$value['user_id']]['email']) ? $users[$value['user_id']]['email'] : "";
            $bulk_message_campaign_counter++;
            if($bulk_message_campaign_counter == 10) break;
        }
        $data['bulk_message_campaign'] = $bulk_message_campaign_report;


        $select = array('user_id','post_thumb','post_id','auto_reply_campaign_name','auto_reply_done_info','auto_private_reply_count','auto_comment_reply_count','last_reply_time');
        $autoreply_info = $this->basic->get_data("facebook_ex_autoreply",'',$select,'',10,$start=NULL,$order_by='last_reply_time desc');
        $autoreply_info_report = array();
        $autoreply_info_counter = 0;
        foreach($autoreply_info as $value)
        {
            $autoreply_info_report[$autoreply_info_counter]['campaign_name'] = $value['auto_reply_campaign_name'];
            $autoreply_info_report[$autoreply_info_counter]['post_thumb'] = $value['post_thumb'];
            $autoreply_info_report[$autoreply_info_counter]['post_id'] = $value['post_id'];
            $autoreply_info_report[$autoreply_info_counter]['auto_private_reply_count'] = $value['auto_private_reply_count'];
            $autoreply_info_report[$autoreply_info_counter]['auto_comment_reply_count'] = $value['auto_comment_reply_count'];
            $autoreply_info_report[$autoreply_info_counter]['last_reply_time'] = $value['last_reply_time'];
            $autoreply_info_report[$autoreply_info_counter]['user_id'] = $value['user_id'];
            $autoreply_info_report[$autoreply_info_counter]['user_name'] = isset($users[$value['user_id']]['name']) ? $users[$value['user_id']]['name'] : "";
            $autoreply_info_report[$autoreply_info_counter]['user_email'] = isset($users[$value['user_id']]['email']) ? $users[$value['user_id']]['email'] : "";
            $autoreply_info_counter++;
            if($autoreply_info_counter == 10) break;
        }
        $data['autoreply_campaign'] = $autoreply_info_report;


        $vidcaster_campaign_report = array();
        if($this->basic->is_exist("add_ons",array("project_id"=>21)))
        {
            $select = array('user_id','schedule_time','post_url','scheduler_name','posting_status','last_updated_at');
            $vidcaster_campaign_info = $this->basic->get_data("vidcaster_facebook_rx_live_scheduler",'',$select,'',10,$start=NULL,$order_by='last_updated_at desc');
            $vidcaster_campaign_counter = 0;
            foreach($vidcaster_campaign_info as $value)
            {
                $vidcaster_campaign_report[$vidcaster_campaign_counter]['campaign_name'] = $value['scheduler_name'];
                $vidcaster_campaign_report[$vidcaster_campaign_counter]['post_url'] = $value['post_url'];
                if($value['posting_status'] == 0) $posting_status = $this->lang->line('pending');
                if($value['posting_status'] == 1) $posting_status = $this->lang->line('processing');
                if($value['posting_status'] == 2) $posting_status = $this->lang->line('Completed');
                $vidcaster_campaign_report[$vidcaster_campaign_counter]['posting_status'] = $posting_status;
                $vidcaster_campaign_report[$vidcaster_campaign_counter]['last_updated_at'] = $value['last_updated_at'];
                $vidcaster_campaign_report[$vidcaster_campaign_counter]['user_id'] = $value['user_id'];
                $vidcaster_campaign_report[$vidcaster_campaign_counter]['user_name'] = isset($users[$value['user_id']]['name']) ? $users[$value['user_id']]['name'] : "";
                $vidcaster_campaign_report[$vidcaster_campaign_counter]['user_email'] = isset($users[$value['user_id']]['email']) ? $users[$value['user_id']]['email'] : "";
                $vidcaster_campaign_counter++;
                if($vidcaster_campaign_counter == 10) break;
            }

            $data['vidcaster_campaign'] = $vidcaster_campaign_report;
        }


        $combopost_campaign_report = array();
        if($this->basic->is_exist("add_ons",array("project_id"=>20)))
        {
            $select = array('user_id','schedule_time','posting_status','campaign_name','campaign_type');
            $comboposter_campaign_info = $this->basic->get_data("post_data_info",'',$select,'',10,$start=NULL,$order_by='schedule_time desc');
            $comboposter_campaign_counter = 0;
            foreach($comboposter_campaign_info as $value)
            {
                $combopost_campaign_report[$comboposter_campaign_counter]['campaign_name'] = $value['campaign_name'];
                $combopost_campaign_report[$comboposter_campaign_counter]['posting_status'] = $value['posting_status'];
                $combopost_campaign_report[$comboposter_campaign_counter]['post_type'] = $value['campaign_type'];
                $combopost_campaign_report[$comboposter_campaign_counter]['schedule_time'] = $value['schedule_time'];
                $combopost_campaign_report[$comboposter_campaign_counter]['user_id'] = $value['user_id'];
                $combopost_campaign_report[$comboposter_campaign_counter]['user_name'] = isset($users[$value['user_id']]['name']) ? $users[$value['user_id']]['name'] : "";
                $combopost_campaign_report[$comboposter_campaign_counter]['user_email'] = isset($users[$value['user_id']]['email']) ? $users[$value['user_id']]['email'] : "";
                $comboposter_campaign_counter++;
                if($comboposter_campaign_counter == 10) break;
            }

            $data['comboposter_campaign'] = $combopost_campaign_report;
        }


        $data['body'] = 'facebook_ex/activity_log';
        $data['page_title'] = $this->lang->line('Activity Log');
        $this->_viewcontroller($data);
   }

   

}

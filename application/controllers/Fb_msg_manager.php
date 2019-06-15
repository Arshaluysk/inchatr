<?php

require_once("Home.php"); // loading home controller

class fb_msg_manager extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(82,$this->module_access))
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
      $this->get_page_list();
    }


    public function get_page_list()
    {
        $data['body'] = 'fb_messenger_manager/page_list_grid';
        $data['page_title'] = $this->lang->line('Messenger manager - Page list');
        $data['time_zone_list'] = $this->_time_zone_list();

        $where['where'] = array('user_id'=>$this->user_id,'facebook_rx_fb_user_info_id'=>$this->session->userdata('facebook_rx_fb_user_info'));
        $data['settings_info'] = $this->basic->get_data("fb_msg_manager_notification_settings",$where);

        $where = array();
        $where['where'] = array(
            'user_id' => $this->user_id,
            'facebook_rx_fb_user_info_id' => $this->session->userdata("facebook_rx_fb_user_info")
            );
        $table = "facebook_rx_fb_page_info";
        $info = $this->basic->get_data($table, $where,'','','','', $order_by='page_name asc');
        $result = array();
        $i = 0;
        foreach($info as $results){
            $result[$i]['id'] = $results['id'];
            
            $result[$i]['page_profile'] = '<img class="" src="'.$results['page_profile'].'"  style="height:90px; width:91px; margin-top:-3.9px;border-radius:2px 0 0 2px;border-right:1px solid #eee;">';
            $result[$i]['page_name'] = $results['page_name'];
            $result[$i]['page_email'] = $results['page_email'];

            if($results['msg_manager'] == '0')
                $result[$i]['msg_manager'] = "<span table_id='".$results['id']."' action='enable' class='action btn-sm btn btn-outline-success' style='cursor : pointer;'><i class='fa fa-check-circle'></i> ".$this->lang->line("enable")."</span>";
            else
                $result[$i]['msg_manager'] = "<span table_id='".$results['id']."' action='disable' class='action btn-sm btn btn-outline-danger' style='cursor : pointer;'><i class='fa fa-ban'></i>".$this->lang->line("disable")."</span>";

            $i++;
        }

        $data['page_list'] = $result;
        $this->_viewcontroller($data);
    }


    public function enable_disable_messenger_manager()
    {
        $table_id = $this->input->post('table_id',true);
        $action = $this->input->post('action',true);
        if($action == 'enable') $msg_manager = '1';
        else $msg_manager = '0';

        $data = array('msg_manager'=>$msg_manager);
        $where = array('id'=>$table_id);

        if($this->basic->update_data('facebook_rx_fb_page_info',$where,$data))
        {
            if($msg_manager=='0')
            $this->basic->delete_data("fb_msg_manager",array("page_table_id"=>$table_id,"user_id"=>$this->user_id));
            echo "success";
        }
    }


    public function notification_settings()
    {
        $status = $this->input->post('get_notification',true);
        $time_interval = $this->input->post('time_interval',true);
        $email_address = $this->input->post('email_address',true);
        $has_business_account = $this->input->post('has_business_account',true);
        $time_zone = $this->input->post('time_zone',true);

        if($status == 'yes')
        {
            $data['is_enabled'] = $status;
            $data['time_interval'] = $time_interval;
            $data['email_address'] = $email_address;
        }
        else
            $data['is_enabled'] = $status;

        $data['has_business_account'] = $has_business_account;
        $data['time_zone'] = $time_zone;

        $where['where'] = array('user_id'=>$this->user_id,'facebook_rx_fb_user_info_id'=>$this->session->userdata('facebook_rx_fb_user_info'));
        $exist_or_not = $this->basic->get_data("fb_msg_manager_notification_settings",$where);

        if(!empty($exist_or_not))
        {
            $update_where = array('user_id'=>$this->user_id,'facebook_rx_fb_user_info_id'=>$this->session->userdata('facebook_rx_fb_user_info'));
            $this->basic->update_data('fb_msg_manager_notification_settings',$update_where,$data);
        }
        else{
            $data['facebook_rx_fb_user_info_id'] = $this->session->userdata('facebook_rx_fb_user_info');
            $data['user_id'] = $this->user_id;            
            $this->basic->insert_data('fb_msg_manager_notification_settings',$data);
        }
        $this->session->set_flashdata('success_message', 1);
        redirect('fb_msg_manager/get_page_list','Location');
    }


    public function message_dashboard()
    {
        $data['body'] = 'fb_messenger_manager/message_dashboard';
        $data['page_title'] = $this->lang->line('Messenger manager - Message dashboard');
        $data['emotion_list'] = $this->get_emotion_list();
        $this->_viewcontroller($data);
    }


    public function get_emotion_list()
    {
        $dirTree=$this->scanAll(FCPATH."assets/images/emotions-fb");
        $map = array
        (
            "angel" => "o:)",
            "colonthree" => ":3",
            "confused" => "o.O",
            "cry" => ":'(",
            "devil" => "3:)",
            "frown" => ":(",
            "gasp" => ":O",
            "glasses" => "8)",
            "grin" => ":D",
            "grumpy" => ">:(",
            "heart" => "<3",
            "kiki" => "^_^",
            "kiss" => ":*",
            "pacman" => ":v",
            "smile" => ":)",
            "squint" => "-_-",
            "sunglasses" => "8|",
            "tongue" => ":p",
            "upset" => ">:O",
            "wink" => ";)"
            );
        $str = "";
        foreach ($dirTree as $value) 
        {
            $temp = array();
            $value['file'] = str_replace('\\','/', $value['file']);
            $temp =explode('/', $value["file"]);
            $filename = array_pop($temp);

            if(!strpos($filename,'.gif')) continue;

            $title = str_replace('.gif',"",$filename);
            $eval = $map[$title];

            $src= base_url('assets/images/emotions-fb/'.$filename);
            $str.= '&nbsp;&nbsp;<img eval="'.$eval.'" title="'.$title.'" style="cursor:pointer;"  class="emotion inline" src="'.$src.'"/>&nbsp;&nbsp;';
        }
        return $str;
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


    public function get_unread_message()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $this->messenger_sync_page_messages($api_key="",$this->user_id,$this->session->userdata('facebook_rx_fb_user_info'));

        $where['where'] = array(
            'fb_msg_manager.user_id' => $this->user_id,
            'fb_msg_manager.facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info'),
            'unread_count !=' => '0',
            'msg_manager' => '1'
            );

        $join = array('facebook_rx_fb_page_info'=>'fb_msg_manager.page_table_id=facebook_rx_fb_page_info.id,left');
        $select = array('fb_msg_manager.*','facebook_rx_fb_page_info.page_name','facebook_rx_fb_page_info.page_profile','facebook_rx_fb_page_info.page_id as fb_page_id');
        $unread_message = $this->basic->get_data('fb_msg_manager',$where,$select,$join,'','','last_update_time desc');

        $where = array();
        $where['where'] = array(
            'user_id' => $this->user_id,
            'facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info')
            );
        $last_sync_time = $this->basic->get_data('fb_msg_manager',$where,array('sync_time'),'',1,'','sync_time desc');

        $go_to_link = "https://www.facebook.com";
        $business_account = $this->basic->get_data('fb_msg_manager_notification_settings',$where);
        if(!empty($business_account))
        {
            $check_business_account = $business_account[0]['has_business_account'];
            if($check_business_account == 'yes')
               $go_to_link = "https://business.facebook.com";
            if($business_account[0]['time_zone'] != '')
                date_default_timezone_set($business_account[0]['time_zone']);
        }


        
        $str = '';

        if(!empty($last_sync_time))
        {
            $last_sync_time = $last_sync_time[0]['sync_time'];
            $str .= '<div class="well well_border_left text-center"><h4><i class="fa fa-dashboard"></i> '.$this->lang->line('Message Dashboard').'</h4>';

            $str.='<p class="text-center" style="margin-top:25px;font-size:11px;">'.$this->lang->line('last scanned').' : '.date('d M Y H:i:s',strtotime($last_sync_time));
            $str.='&nbsp; &nbsp; <button  style="margin-top:-5px;" id="refresh_button" class="btn btn-outline-primary inline btn-sm"><i class="fa fa-refresh"></i> '.$this->lang->line('refresh data').'</button></p></div>';
        }



        $str .= '<br><h4 class="text-center dynamic_font_color">'.$this->lang->line("all unread messages").'</h4>
                <script>
                    $j(document).ready(function() {
                        $(".table-responsive").mCustomScrollbar({
                            autoHideScrollbar:true,
                            theme:"3d-dark",          
                            axis: "x"
                        });  
                        $("#unread_message_table").DataTable();
                    }); 
                 </script>
                 <div class="margined table-responsive"><table id="unread_message_table" class="table table-bordered table-hover table-stripped">
                     <thead>
                         <tr style="background:#FFF !important;">
                             <th class="text-center">'.$this->lang->line("thumbnail").'</th>
                             <th class="text-center">'.$this->lang->line("page name").'</th>
                             <th>'.$this->lang->line("message").'</th>
                             <th class="text-center">'.$this->lang->line("sent from").'</th>
                             <th class="text-center">'.$this->lang->line("sent time").'</th>
                             <th class="text-center">'.$this->lang->line("total count").'</th>
                             <th class="text-center">'.$this->lang->line("total unread count").'</th>
                             <th class="text-center">'.$this->lang->line("see conversation & reply").'</th>
                             <th class="text-center">'.$this->lang->line("go to inbox").'</th>
                         </tr>
                     </thead>
                     <tbody>';
        foreach($unread_message as $value)
        {
            if(strlen($value['last_snippet']) > 250) $message_short = substr($value['last_snippet'], 0, 249).'..';
            else $message_short = $value['last_snippet'];
                        
            $finalgo_to_link = $go_to_link.$value['inbox_link'];
            $last_update_time = $value['last_update_time']." UTC";
            $str .= '<tr>
                        <td class="text-center"><img class="img-thumbnail" src="'.$value['page_profile'].'" alt="image" style="height:50px;width:50px;" ></td>
                        <td class="text-center"><a target="_BLANK" href="https://facebook.com/'.$value["fb_page_id"].'">'.$value['page_name'].'</a></td>
                        <td><p title="'.$value['last_snippet'].'">'.chunk_split($message_short, 50, '<br>').'</p></td>
                        <td class="text-center">'.$value['from_user'].'</td>
                        <td class="text-center">'.date("d M Y H:i:s",strtotime($last_update_time)).'</td>
                        <td class="text-center">'.$value['message_count'].'</td>
                        <td class="text-center">'.$value['unread_count'].'</td>
                        <td class="text-center"><span class="label label-light reply_button blue" page_name="'.$value['page_name'].'" page_table_id="'.$value['page_table_id'].'" thread_id="'.$value['thread_id'].'" style="cursor:pointer;"><i class="fa fa-mail-reply"></i> '.$this->lang->line("reply").'</span></td>
                        <td class="text-center"><a href="'.$finalgo_to_link.'" target="_blank"><span class="label label-light blue"><i class="fa fa-hand-o-right"></i>'.$this->lang->line("go to inbox").'</span></a></td>
                    </tr>';
        }

        $str .= '</tbody>
                 </table></div>';
        echo $str;
    }


    public function get_pages_conversation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $where['where'] = array(
            'user_id' => $this->user_id,
            'facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info'),
            'msg_manager' => '1'
            );
        $select = array('id','page_name','page_profile','page_id as fb_page_id');
        $page_list = $this->basic->get_data('facebook_rx_fb_page_info',$where,$select,'','','', $order_by='page_name asc');

        // for go to link generating
        $where = array();
        $where['where'] = array(
            'user_id' => $this->user_id,
            'facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info')
            );

        $go_to_link = "https://www.facebook.com";
        $business_account = $this->basic->get_data('fb_msg_manager_notification_settings',$where);
        if(!empty($business_account))
        {
            $check_business_account = $business_account[0]['has_business_account'];
            if($check_business_account == 'yes')
               $go_to_link = "https://business.facebook.com";
            if($business_account[0]['time_zone'] != '')
                date_default_timezone_set($business_account[0]['time_zone']);
        }
        // end of go to link generating


        if(empty($page_list))
        {
            echo '<br><div class="alert alert-danger text-center"><h4 class="header_title">'.$this->lang->line("you have not enabled messenger manager for any page yet !").'</h4></div>';
        }
        else
        {
            $str = '';
            foreach($page_list as $value)
            {
                // $str .= '<div class="well text-center"><h3 class="header_title"><img class="img-thumbnail" src="'.$value['page_profile'].'" alt="image"> '.$value['page_name'].' ('.$this->lang->line("last 20 conversations").')</h3></div>';
                $str .='<br><img class="img-circle img-thumbnail center-block" src="'.$value['page_profile'].'" style="height:80px;width:80px"><h4 class="text-center dynamic_font_color"><a target="_BLANK" href="https://facebook.com/'.$value["fb_page_id"].'">'.$value['page_name'].'</a> ('.$this->lang->line("last 20 conversations").')</h4>';
                $where = array();
                $where['where'] = array(
                    'user_id' => $this->user_id,
                    'facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info'),
                    'page_table_id' => $value['id']
                    );
                $last_conversation = $this->basic->get_data('fb_msg_manager',$where,'','',20,$start=0,$order_by='last_update_time desc');

                if(empty($last_conversation))
                {
                    $str .= '<div class="alert alert-danger text-center"><h3>'.$this->lang->line("no data found").'</h3></div><br/>';
                }
                else
                {
                    $str .= '<script>
                                $j(document).ready(function() {
                                    $(".table-responsive").mCustomScrollbar({
                                        autoHideScrollbar:true,
                                        theme:"3d-dark",          
                                        axis: "x"
                                    });  
                                    $("#'.$value['id'].'").DataTable({"order": [] });
                                }); 
                             </script>

                             <div class="margined table-responsive">
                             <table id="'.$value['id'].'" class="table table-bordered table-hover table-stripped">
                                 <thead>
                                     <tr>
                                         <th class="text-center">'.$this->lang->line("sent from").'</th>
                                         <th>'.$this->lang->line("message").'</th>
                                         <th class="text-center">'.$this->lang->line("sent time").'</th>
                                         <th class="text-center">'.$this->lang->line("total count").'</th>
                                         <th class="text-center">'.$this->lang->line("see conversation & reply").'</th>
                                         <th class="text-center">'.$this->lang->line("go to inbox").'</th>
                                     </tr>
                                 </thead>
                                 <tbody>';
                    foreach($last_conversation as $data)
                    {
                        if(strlen($data['last_snippet']) > 250) $message_short = substr($data['last_snippet'], 0, 249).'..';
                        else $message_short = $data['last_snippet'];

                        $finalgo_to_link = $go_to_link.$data['inbox_link'];
                        $last_update_time = $data['last_update_time']." UTC";
                        $str .= '<tr>
                                    <td class="text-center">'.$data['from_user'].'</td>                                    
                                    <td><p title="'.$data['last_snippet'].'">'.chunk_split($message_short, 50, '<br>').'</p></td>
                                    <td class="text-center">'.date("d M Y H:i:s",strtotime($last_update_time)).'</td>
                                    <td class="text-center">'.$data['message_count'].'</td>
                                    <td class="text-center"><span class="label label-light reply_button blue" page_name="'.$value['page_name'].'" page_table_id="'.$data['page_table_id'].'" thread_id="'.$data['thread_id'].'" style="cursor:pointer;"><i class="fa fa-mail-reply"></i> '.$this->lang->line("reply").'</span></td>
                                    <td class="text-center"><a href="'.$finalgo_to_link.'" target="_blank"><span class="label label-light blue"><i class="fa fa-hand-o-right"></i> '.$this->lang->line("go to inbox").'</span></a></td>
                                </tr>';
                    }
                    $str .= '</tbody>
                             </table>
                             </div><br/>';
                }
            }
            echo $str;
        }
    }

    public function get_post_conversation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        // for time zone checking
        $where = array();
        $where['where'] = array(
            'user_id' => $this->user_id,
            'facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info')
            );

        $business_account = $this->basic->get_data('fb_msg_manager_notification_settings',$where);
        if(!empty($business_account))
        {
            if($business_account[0]['time_zone'] != '')
                date_default_timezone_set($business_account[0]['time_zone']);
        }
        // end of time zone checking

        $thread_id = $this->input->post('thread_id',true);
        $page_table_id = $this->input->post('page_table_id',true);

        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_table_id)));

        $post_access_token = $page_info[0]['page_access_token'];
        $page_name = $page_info[0]['page_name'];

        $conversations = $this->fb_rx_login->get_messages_from_thread($thread_id,$post_access_token);
        if(!isset($conversations['data'])) $conversations['data']=array();
        $conversations['data'] = array_reverse($conversations['data']);

        $str = '<script>
          $j(document).ready(function() {  
              $(".yscroll").mCustomScrollbar({
              autoHideScrollbar:true,
              theme:"3d-dark"
            });
          });
         </script>
        <div class="yscroll" style="height: 500px; overflow: auto;">';
        foreach($conversations['data'] as $value)
        {
            $created_time = $value['created_time']." UTC";
            isset($value['from']['name']) ? $value['from']['name'] = $value['from']['name'] : $value['from']['name'] = '';
            if($value['from']['name'] == $page_name)
            {
                $str .= '<div class="clearfix">
                            <div class="right_content">
                                <p class="right_content_message">'.chunk_split($value['message'], 50, '<br>').'</p>
                                <p class="right_content_name" style="font-size:10px;">'.$value['from']['name'].' @'.date('d M Y H:i:s',strtotime($created_time)).'</p>
                            </div>
                        </div>';
            }
            else
            {
                $str .= '<div class="clearfix">
                            <div class="left_content">
                                <p class="left_content_message">'.chunk_split($value['message'], 50, '<br>').'</p>
                                <p class="left_content_name" style="font-size:10px;">'.$value['from']['name'].' @'.date('d M Y H:i:s',strtotime($created_time)).'</p>
                            </div>
                        </div>';
            }
        }
        if($str == '') $str = '<div class="alert alert-danger text-center"><h4 style="margin:0">'.$this->lang->line("no data found").'</h4></div>';
        $str.='</div>';
        echo $str;
    }

    public function reply_to_conversation()
    {
        $thread_id = $this->input->post('thread_id',true);
        $page_table_id = $this->input->post('page_table_id',true);
        $reply_message = $this->input->post('reply_message',true);


        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_table_id)));
        $post_access_token = $page_info[0]['page_access_token'];

        try
        {            
            $response = $this->fb_rx_login->send_message_to_thread($thread_id,$reply_message,$post_access_token);
            if(isset($response['id']))
            {
                echo '<div class="alert alert-success text-center">'.$this->lang->line("Your reply has been sent successfully!").'</div>';
            }
            else 
            {
                if(isset($response["error"]["message"])) $message_sent_id = $response["error"]["message"];  
                if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 

                if($message_error_code=="368") // if facebook marked message as spam 
                {
                    $error_msg=$message_sent_id;
                }
                $error_msg = "<i class='fa fa-remove'></i> ".$message_sent_id;
                echo "<div class='alert alert-danger text-center'>".$error_msg."</div>";
            } 
        }
        catch(Exception $e) 
        {
          $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
          echo "<div class='alert alert-danger text-center'>".$error_msg."</div>";
        }

    }


    public function messenger_sync_page_messages($api_key="",$user_id="",$facebook_rx_fb_user_info_id=""){
        //$this->api_key_check($api_key);
        
        if($user_id)
            $where['user_id'] = $user_id;
        if($facebook_rx_fb_user_info_id)
            $where['facebook_rx_fb_user_info_id']=$facebook_rx_fb_user_info_id;
            
        $where['msg_manager']='1';
        
        $where_simple['where']=$where;
        
        $pages_info_for_sync = $this->basic->get_data("facebook_rx_fb_page_info",$where_simple);
        
        foreach($pages_info_for_sync as $page){
        
            $facebook_rx_fb_page_info_id = $page['facebook_rx_fb_user_info_id'];
            $user_id = $page['user_id'];
            $page_table_id= $page['id'];
            
            // getting latest 200 data
            $get_concersation_info = $this->fb_rx_login->get_all_conversation_page($page['page_access_token'],$page['page_id'],1);
            
            foreach($get_concersation_info as $conversion_info){
            
                $from_user     = isset($conversion_info['name']) ? $this->db->escape($conversion_info['name']) : "";
                $from_user_id  = isset($conversion_info['id']) ? $conversion_info['id'] : "";
                $last_snippet  = isset($conversion_info['snippet']) ? $this->db->escape($conversion_info['snippet']) : "";
                $message_count = isset($conversion_info['message_count']) ? $conversion_info['message_count'] : 0;
                $thread_id     = isset($conversion_info['thead_id']) ? $conversion_info['thead_id'] : "";
                $inbox_link    = isset($conversion_info['link']) ? $conversion_info['link'] : "";
                $unread_count  = isset($conversion_info['unread_count']) ? $conversion_info['unread_count'] : 0;

                $sync_time     = date("Y-m-d H:i:s");
                $last_update_time=$conversion_info['updated_time'];
                // $last_update_time=date('Y-m-d H:i:s',strtotime($conversion_info['updated_time']));
                
                /***delete from database **/
                $this->basic->delete_data('fb_msg_manager',array('user_id'=>$user_id,'facebook_rx_fb_user_info_id'=>$facebook_rx_fb_page_info_id,'thread_id'=>$thread_id));
                
                /***Insert into database **/
                 $sql="Insert into fb_msg_manager(user_id,facebook_rx_fb_user_info_id,from_user,from_user_id,last_snippet,message_count,thread_id,inbox_link,unread_count,sync_time,last_update_time,page_table_id) 
                    values ('$user_id','$facebook_rx_fb_page_info_id',$from_user,'$from_user_id',$last_snippet,'$message_count','$thread_id','$inbox_link','$unread_count','$sync_time','$last_update_time','$page_table_id')";
                
                $this->basic->execute_complex_query($sql);
                
                    
            }
                
        }
        
        
    }




}
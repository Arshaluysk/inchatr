<?php
/*
Addon Name: Bot Inboxer
Unique Name: messenger_bot
Module ID: 200
Project ID: 3
Addon URI: http://getfbinboxer.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 3.3.8
Description: Facebook messenger chat bot.
*/
require_once("application/controllers/Home.php"); // loading home controller
class Messenger_bot extends Home
{
    public $addon_data=array();
    public $is_broadcaster_exist=false;
    public $is_messenger_bot_import_export_exist=false;
    public $is_messenger_bot_analytics_exist=false;
    public $postback_info;
    public $postback_array=array();
    public $postback_done=array();
    public function __construct()
    {
        parent::__construct();
        $this->load->config('messenger_bot_config');// config
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $addondata=$this->get_addon_data($addon_path); 
        $this->addon_data=$addondata;
        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it
        $function_name=$this->uri->segment(2);
        if($function_name!="webhook_callback" && $function_name!="send_reply_curl_call" && $function_name!="download_profile_pic" && $function_name!="webhook_callback_main" && $function_name!="update_first_name_last_name") 
        {
             // all addon must be login protected
              //------------------------------------------------------------------------------------------
              if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');          
              // if you want the addon to be accessed by admin and member who has permission to this addon
              //-------------------------------------------------------------------------------------------
              if(isset($addondata['module_id']) && is_numeric($addondata['module_id']) && $addondata['module_id']>0)
              {
                   if($this->session->userdata('user_type') != 'Admin' && !in_array($addondata['module_id'],$this->module_access))
                   {
                        redirect('home/login_page', 'location');
                        exit();
                   }
              }
        }
        $this->is_broadcaster_exist=$this->broadcaster_exist();  
        $this->is_messenger_bot_import_export_exist=$this->messenger_bot_import_export_exist();  
        $this->is_messenger_bot_analytics_exist=$this->messenger_bot_analytics_exist();  
        $this->member_validity();
    }


    public function api_member_validity($user_id='')
    {
        if($user_id!='') {
            $where['where'] = array('id'=>$user_id);
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $package_data=$this->basic->get_data("users",$where=array("where"=>array("users.id"=>$user_id)),$select="package.price as price, users.user_type",$join=array('package'=>"users.package_id=package.id,left"));

            if(is_array($package_data) && array_key_exists(0, $package_data) && $package_data[0]['user_type'] == 'Admin' )
                return true;

            $price = '';
            if(is_array($package_data) && array_key_exists(0, $package_data))
            $price=$package_data[0]["price"];
            if($price=="Trial") $price=1;

            
            if ($expire_date < $current_date && ($price>0 && $price!=""))
            return false;
            else return true;
            

        }
    }

  
    public function broadcaster_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>16))) return true;
        if($this->session->userdata('user_type') == 'Member' && (in_array(210,$this->module_access) || in_array(211,$this->module_access))) return true;
        return false;
    }

    public function messenger_bot_import_export_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>22))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(257,$this->module_access)) return true;
        return false;
    }

    public function messenger_bot_analytics_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>25))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(260,$this->module_access)) return true;
        return false;
    }

    private function package_list()
    {
        $payment_package=$this->basic->get_data("package",$where='',$select='',$join='',$limit='',$start=NULL,$order_by='price');
        $return_val=array();
        $config_data=$this->basic->get_data("payment_config");
        $currency=$config_data[0]["currency"];
        foreach ($payment_package as $row)
        {
            $return_val[$row['id']]=$row['package_name']." : Only @".$currency." ".$row['price']." for ".$row['validity']." days";
        }
        return $return_val;
    }

    public function get_label_dropdown()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id

        $table_type = 'messenger_bot_broadcast_contact_group';
        $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$page_id,"unsubscribe"=>"0","invisible"=>"0");
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');
        $result = array();
        $group_name =array();

        $dropdown=array();
        $str='<script>$j("#label_ids").multipleSelect({
              filter: true,
              multiple: true
          });</script> ';
        $str .='<select multiple="multiple"  class="form-control" id="label_ids" name="label_ids[]">';
        foreach ($info_type as  $value)
        {
            $search_key = $value['id'];
            $search_type = $value['group_name'];
            $str.=  "<option value='{$search_key}'>".$search_type."</option>";            

        }
        $str.= '</select>';

        echo json_encode(array('first_dropdown'=>$str));
    }
    
    public function create_subscriber($sender_id='', $page_id='')
    {
        $table = "messenger_bot_subscriber";
        $where = array('messenger_bot_subscriber.subscribe_id' => $sender_id);
        $is_exist = $this->basic->is_exist($table,$where);
        
        $response=array();
        $response['is_new']=FALSE;
        
        if(!$is_exist){
        
            $response['is_new']=TRUE;
            $table = "messenger_bot_page_info";
            $where['where'] = array('page_id' => $page_id,'bot_enabled'=>'1');
            $page_access_token_array = $this->basic->get_data($table,$where,"page_access_token,user_id");
            $page_access_token = $page_access_token_array[0]['page_access_token'];
            $user_id = $page_access_token_array[0]['user_id'];
            $user_data = $this->subscriber_info($page_access_token,$sender_id);
            $data = array(
                'user_id' => $user_id,
                'page_id' => $page_id,
                'subscribe_id' => $sender_id,
                'first_name' => $user_data['first_name'],
                'last_name' => $user_data['last_name'],
                'profile_pic' => $user_data['profile_pic'],
                // 'locale' => $user_data['locale'],
                // 'timezone' => $user_data['timezone'],
                // 'gender' => $user_data['gender'],
                'subscribed_at' => date('Y-m-d H:i:s')
            );
            $this->db->db_debug = FALSE; //disable debugging for queries
            if($this->basic->insert_data('messenger_bot_subscriber',$data)){
                return $response;
            }else{
                return $response;
            }
        }
        
        return $response;
    }
    
    public function send_reply($access_token='',$reply='')
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
    public function send_reply_curl_call(){
        ignore_user_abort(TRUE);
        $access_token=$_POST['access_token'];
        $reply=$_POST['reply'];
        
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
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");          $st=curl_exec($ch);      
        
        $result=json_decode($st,TRUE);
        return $result;
    }
    
    /**Sender action added 19.03.2018 by Konok**/
    
    public function sender_action($sender_id,$action_type,$post_access_token='')
    {
    
        $url = "https://graph.facebook.com/v2.6/me/messages?access_token={$post_access_token}";
        
        $post_data_array['recipient']['id']=$sender_id;
        $post_data_array['sender_action']=$action_type;
        $post_data=json_encode($post_data_array);
        $ch = curl_init();
        $headers = array("Content-type: application/json");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data); 
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

    public function is_email($email)
    {
        $email=trim($email);
        $is_valid=0;
        /***Validation check***/
        $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        if (preg_match($pattern, $email) === 1) {
            $is_valid=1;
        }
        return $is_valid;
    }

    public function is_phone_number($phone)
    {    
        $is_valid=0;
        if(preg_match("#\+\d{7}#",$phone)===1)
            $is_valid=1; 
            
        return $is_valid;
            
    }


    public function webhook_callback(){

            $challenge = $this->input->get_post('hub_challenge');
            $verify_token =$this->input->get_post('hub_verify_token');
            if($verify_token === $this->config->item("webhook_verify_token"))
            {
                echo $challenge;
                die();
            }

            $response_raw=file_get_contents("php://input");
            $post_data_label_assign=array("response_raw"=>$response_raw);
            $url=base_url()."messenger_bot/webhook_callback_main";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            $reply_response=curl_exec($ch); 
    }

    public function webhook_callback_main()
    {
        
        $currenTime=date("Y-m-d H:i:s");
        $response_raw=$this->input->post("response_raw");   

        /*file_put_contents("fb.txt",$response_raw, FILE_APPEND | LOCK_EX);        
        exit();*/ 
        
        $response = json_decode($response_raw,TRUE);
        if(isset($response['entry']['0']['messaging'][0]['delivery'])) exit();

        // for package expired users bot will not work section
        $page_id = $response['entry']['0']['messaging'][0]['recipient']['id'];
        $table_name = "messenger_bot_page_info";
        $where['where'] = array('messenger_bot_page_info.page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1');
        $join = array('users'=>"users.id=messenger_bot_page_info.user_id,left");   
        $users_expiry_info = $this->basic->get_data($table_name,$where,array("users.id as user_id","users.expired_date","users.user_type","users.deleted","users.status","messenger_bot_page_info.id as page_auto_id","chat_human_email","page_name"),$join);
        
        $PAGE_AUTO_ID= isset($users_expiry_info[0]['page_auto_id']) ? $users_expiry_info[0]['page_auto_id'] : "0"; // Page's Database ID
        
        if($PAGE_AUTO_ID=="0") exit(); 
        
        if(isset($users_expiry_info[0]['user_type']) && $users_expiry_info[0]['user_type'] != 'Admin')
        {
            $user_status = $users_expiry_info[0]['status'];
            $user_deleted = $users_expiry_info[0]['deleted'];
            if($user_deleted == '1' || $user_status == '0') exit();
            
            if(!$this->api_member_validity($users_expiry_info[0]['user_id'])) exit();            
        }
        // end of for package expired users bot will not work section
        
        if(isset($response['entry']['0']['messaging'][0]['read'])) 
        {           
            $receipent_id_read=isset($response['entry']['0']['messaging'][0]['sender']['id'])?$response['entry']['0']['messaging'][0]['sender']['id']:"";
            $where_array=array("subscribe_id"=>$receipent_id_read,"opened"=>"0","processed"=>'1',"error_message"=>"");
            $campaign_info=$this->basic->get_data("messenger_bot_broadcast_serial_send",array("where"=>$where_array));
            $campaign_id_read=array();
            foreach($campaign_info as $read_info)
            {
                $campaign_id_read[]= $read_info['campaign_id']; 
            }
            if(!empty($campaign_id_read))   
            {
                $campaign_info_multiple=$this->basic->get_data("messenger_bot_broadcast_serial",array("where_in"=>array("id"=>$campaign_id_read)));
                foreach ($campaign_info_multiple as $key => $value) 
                {
                   $cam_id=$value["id"];
                   $successfully_opened=$value["successfully_opened"];
                   $report_temp=json_decode($value["report"],true);
                   $report_temp[$receipent_id_read]["opened"]="1";
                   $report_temp[$receipent_id_read]["open_time"]=$currenTime;
                   $report_json=json_encode($report_temp);
                   $successfully_opened++;
                   $this->basic->update_data("messenger_bot_broadcast_serial",array("id"=>$cam_id),array("report"=>$report_json,"successfully_opened"=>$successfully_opened));
                }
                $update_data_read= array("opened"=>"1","open_time"=>$currenTime);
                $this->basic->update_data('messenger_bot_broadcast_serial_send',$where_array,$update_data_read); 
            }
            

            // drip message open update

            if($this->db->table_exists('messenger_bot_drip_campaign'))
            {
                $drip_subscriber_data=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("subscribe_id"=>$receipent_id_read)));
                if(!isset($drip_subscriber_data[0])) exit();
                $driptime=date("Y-m-d H:i:s");
                $drip_insert_data=array
                (
                    "is_opened"=>"1",
                    "opened_at"=>$driptime,
                    "last_updated_at"=>$driptime
                );
                $this->basic->update_data("messenger_bot_drip_report",array("subscribe_id"=>$drip_subscriber_data[0]["subscribe_id"],"is_opened"=>"0"),$drip_insert_data);
            }
               
            exit();                         
        }
       
       
       //if it's optin from checkbox plugin, then tese action is not needed. As not information can be found for that. 
       
       $page_id = $response['entry']['0']['messaging'][0]['recipient']['id'];
       
       if(!isset($response['entry'][0]['messaging'][0]['optin']['user_ref'])) 
       {       
            $sender_id= $response['entry']['0']['messaging'][0]['sender']['id'];
            
            //subscriber status
            $subscriber_new_old_info= $this->create_subscriber($sender_id, $page_id);
            $subscriber_where['where'] = array('subscribe_id' => $sender_id);
            $subscriber_info = $this->basic->get_data("messenger_bot_subscriber",$subscriber_where,'',"","1");
        
        }
     
     /***   Check if it coming from after subscribing by checkbox plugin    ***/
        
        if($this->db->table_exists('messenger_bot_engagement_2way_chat_plugin'))
        {
            if(isset($response['entry'][0]['messaging'][0]['prior_message']['source']) && $response['entry'][0]['messaging'][0]['prior_message']['source']=="checkbox_plugin")
            {
            
                $user_identifier= isset($response['entry'][0]['messaging'][0]['prior_message']['identifier']) ? $response['entry'][0]['messaging'][0]['prior_message']['identifier']:"";
                
                if($user_identifier!="")
                {                
                    //Get check_box plugin id searching with user_identifier.                 
                    $check_box_plugin_info= $this->basic->get_data("messenger_bot_engagement_checkbox_reply",array("where"=>array("user_ref"=>$user_identifier)));
                    
                    $check_box_plugin_id=isset($check_box_plugin_info[0]['checkbox_plugin_id']) ? $check_box_plugin_info[0]['checkbox_plugin_id']:"";
                    $check_box_plugin_reference=isset($check_box_plugin_info[0]['reference']) ? $check_box_plugin_info[0]['reference']:"";
                                        
                    if($check_box_plugin_id!="")
                    {
                     // Update subscriber if new, then source is from checkbox plugin & also reffernce updated. 
                        if($subscriber_new_old_info['is_new'])
                        {
                            $plugin_name=$response['entry'][0]['messaging'][0]['prior_message']['source'];
                            $subscriber_id_update=$subscriber_info[0]['id'];
                            $update_data=array("refferer_id"=>$check_box_plugin_reference,"refferer_source"=>$plugin_name,"refferer_uri"=>"N/A");
                            $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                        }
                        
                    /****Assign Drip Messaging Campaing ID ***/
                    $drip_type="messenger_bot_engagement_checkbox";
                    $this->assign_drip_messaging_id($drip_type,$check_box_plugin_id,$PAGE_AUTO_ID,$subscriber_info[0]['id']);   
                        
                        
                        $engagementer_info= $this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("id"=>$check_box_plugin_id)));
                
                        $label_ids=isset($engagementer_info[0]['label_ids']) ? $engagementer_info[0]['label_ids']:"";
                        
                        if($label_ids!="" )
                        {                 
                            $post_data_label_assign=array("psid"=>$sender_id,"fb_page_id"=>$page_id,"label_auto_ids"=>$label_ids);
                            $url=base_url()."messenger_broadcaster/assign_label_webhook_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);                  
                        }                        
                        
                    }   
                    
                }
            
            }
        }
     
     
     
        
        if(isset($response['entry'][0]['messaging'][0]['message']['text']) 
        && !isset($response['entry'][0]['messaging'][0]['message']['quick_reply']) 
        && !isset($response['entry'][0]['messaging'][0]['postback']) 
        && !isset($response['entry'][0]['messaging'][0]['optin'])) //message for all
        {
            $messages = $response['entry']['0']['messaging'][0]['message']['text'];
            $table_name = "messenger_bot";
            $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1');
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");   
            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;            
            
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);                 
            
            foreach ($messenger_bot_info as $key => $value) {
                $cam_keywords_str = $value['keywords'];
                $cam_keywords_array = explode(",", $cam_keywords_str);
                foreach ($cam_keywords_array as $cam_keywords) {
                    if(function_exists('iconv') && function_exists('mb_detect_encoding')){
                        $encoded_word =  mb_detect_encoding($cam_keywords);
                        if(isset($encoded_word)){
                            $cam_keywords = iconv( $encoded_word, "UTF-8//TRANSLIT", $cam_keywords );
                        }
                    }
                    $pos= stripos($messages,trim($cam_keywords));
                    if($pos!==FALSE){
                        $message_str = $value['message'];
                        $message_array = json_decode($message_str,true);
                        // if(!isset($message_array[1])) $message_array[1]=$message_array;
                        if(!isset($message_array[1])){
                            $message_array_org=$message_array;
                            $message_array=array();
                            $message_array[1]=$message_array_org;
                        }
                        foreach($message_array as $msg)
                        {
                            $template_type_file_track=$msg['message']['template_type'];
                            unset($msg['message']['template_type']);
                            
                            /** Spintax **/
                            if(isset($msg['message']['text']))
                                $msg['message']['text']=spintax_process($msg['message']['text']);
                                
                            $msg['messaging_type'] = "RESPONSE";
                            $reply = json_encode($msg);                            
                            $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                            if(isset($subscriber_info[0]['first_name']))
                                $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                            if(isset($subscriber_info[0]['last_name']))
                                $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                            $access_token = $value['page_access_token'];
                            if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1")
                            {
                                if($enable_typing_on){
                                    $this->sender_action($sender_id,"typing_on",$access_token);
                                    sleep($typing_on_delay_time);
                                }
                            
                                if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio')
                                {
                                    $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                                    $url=base_url()."messenger_bot/send_reply_curl_call";
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch,CURLOPT_POST,1);
                                    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                                    $reply_response=curl_exec($ch);  
                                }
                                else
                                     $reply_response= $this->send_reply($access_token,$reply);
                             
                             /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }
                             
                             
                            }
                            
                            
                        }

                        /** Assign Drip Messaging Campaign ID ****/
                        $drip_type="default";
                        $this->assign_drip_messaging_id($drip_type,"0",$PAGE_AUTO_ID,$subscriber_info[0]['id']);         

                        die();
                    }
                }
            }
            $table_name = "messenger_bot";
            $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'no match','messenger_bot_page_info.bot_enabled' => '1');
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");   
            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'1','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){
                $message_str = $messenger_bot_info[0]['message'];
                $message_array = json_decode($message_str,true);
                // if(!isset($message_array[1])) $message_array[1]=$message_array;
                if(!isset($message_array[1])){
                    $message_array_org=$message_array;
                    $message_array=array();
                    $message_array[1]=$message_array_org;
                }
                foreach($message_array as $msg)
                {
                    $template_type_file_track=$msg['message']['template_type'];
                    unset($msg['message']['template_type']);
                    /** Spintax **/
                    if(isset($msg['message']['text']))
                        $msg['message']['text']=spintax_process($msg['message']['text']);
                                
                    $msg['messaging_type'] = "RESPONSE";
                    $reply = json_encode($msg);                            
                    $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                    if(isset($subscriber_info[0]['first_name']))
                        $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                    if(isset($subscriber_info[0]['last_name']))
                        $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                    $access_token = $messenger_bot_info[0]['page_access_token'];
                    if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1")
                    {
                        if($enable_typing_on){
                            $this->sender_action($sender_id,"typing_on",$access_token);
                            sleep($typing_on_delay_time);
                        }
    
                        if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio')
                        {
                            $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                            $url=base_url()."messenger_bot/send_reply_curl_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);  
                    
                        }
                        else
                            $reply_response=$this->send_reply($access_token,$reply);
                            /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }  
                    }
                }
                die();
            }
        }

        elseif(isset($response['entry'][0]['messaging'][0]['optin'])) //Optins from Send to messengers 
        {
        
            if($this->db->table_exists('messenger_bot_engagement_2way_chat_plugin')){
        
            $reference_id = isset($response['entry'][0]['messaging'][0]['optin']['ref'])?$response['entry'][0]['messaging'][0]['optin']['ref']:"";
            $user_reference_id = isset($response['entry'][0]['messaging'][0]['optin']['user_ref'])?$response['entry'][0]['messaging'][0]['optin']['user_ref']:"";
            
            if($user_reference_id!="")
                $table_name="messenger_bot_engagement_checkbox";
                
            else
            {
            
                $table_name="messenger_bot_engagement_send_to_msg";
                
                if($subscriber_new_old_info['is_new'])
                {
                
                    $plugin_name="SEND-TO-MESSENGER-PLUGIN";
                    $subscriber_id_update=$subscriber_info[0]['id'];
                    
                    $update_data=array("refferer_id"=>$reference_id,"refferer_source"=>$plugin_name,"refferer_uri"=>"N/A");
                    $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                }
                
            }
                
            
            $engagementer_info= $this->basic->get_data($table_name,array("where"=>array("reference"=>$reference_id)));
            
            $label_ids=isset($engagementer_info[0]['label_ids']) ? $engagementer_info[0]['label_ids']:"";
            
            $template_id=isset($engagementer_info[0]['template_id']) ? $engagementer_info[0]['template_id']:"";
            
            $plugin_auto_id=isset($engagementer_info[0]['id']) ? $engagementer_info[0]['id']:"";
            
            
            if($template_id!=""){
                
                $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                $postback_id= isset($postback_id_info[0]['postback_id']) ? $postback_id_info[0]['postback_id'] :"";
            }
            
            $table_name = "messenger_bot";
            
            if($template_id=="")
            
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','messenger_bot_page_info.bot_enabled' => '1');
                
                else    
                
                    $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1',"postback_id"=>$postback_id);
                    
            }
            
            else{
            
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','messenger_bot_page_info.bot_enabled' => '1');
                 /** Assign Drip Messaging Campaign ID ****/
                $drip_type="default";
                $this->assign_drip_messaging_id($drip_type,"0",$PAGE_AUTO_ID,$subscriber_info[0]['id']);
            }
            
            
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");
            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
            
            if($enable_mark_seen && $user_reference_id=="")
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);
                
            
            foreach ($messenger_bot_info as $key => $value) {
                $message_str = $value['message'];
                $message_array = json_decode($message_str,true);
                // if(!isset($message_array[1])) $message_array[1]=$message_array;
                if(!isset($message_array[1])){
                    $message_array_org=$message_array;
                    $message_array=array();
                    $message_array[1]=$message_array_org;
                }
                foreach($message_array as $msg)
                {
                    $template_type_file_track=$msg['message']['template_type'];
                    unset($msg['message']['template_type']);
                    
                    /** Spintax **/
                    if(isset($msg['message']['text']))
                        $msg['message']['text']=spintax_process($msg['message']['text']);
                        
                    $msg['messaging_type'] = "RESPONSE";
                    $reply = json_encode($msg);       
                    
                    if($user_reference_id=="")    // if comes from send-to-messenger rather than checkbox plugin                 
                      $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                      
                    else // if comes from checkbox plugin, then it's different message structure. 
                        $reply=str_replace('{"id":"replace_id"}', '{"user_ref":"'.$user_reference_id.'"}', $reply);
                    
                    if(isset($subscriber_info[0]['first_name']))
                        $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                    if(isset($subscriber_info[0]['last_name']))
                        $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                    $access_token = $value['page_access_token'];
                    
                    if((isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1") || $user_reference_id!=""){
                    
                            if($enable_typing_on && $user_reference_id==""){
                                $this->sender_action($sender_id,"typing_on",$access_token);
                                sleep($typing_on_delay_time);
                            }
                        
                        if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
                            $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                            $url=base_url()."messenger_bot/send_reply_curl_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);  
                    
                        }
                        else
                             $reply_response=$this->send_reply($access_token,$reply);
                                
                         /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }
                    }
                }

                /*** Assign Drip Campaing & also Label ***/
             if($this->db->table_exists('messenger_bot_engagement_2way_chat_plugin')){

                /***    Assign Drip Messaging Campaign ID *****/
                if($user_reference_id==""){
                    $drip_type="messenger_bot_engagement_send_to_msg";
                    $this->assign_drip_messaging_id($drip_type,$plugin_auto_id,$PAGE_AUTO_ID,$subscriber_info[0]['id']);    
                }       
            
                 /** Insert into messenger_bot_engagement_checkbox_reply if it comes from checkbox plugin ***/
                 if($user_reference_id!="")
                 {
                    $reference_data_checkbox['user_ref']=$user_reference_id;
                    $reference_data_checkbox['checkbox_plugin_id']=$plugin_auto_id;
                    $reference_data_checkbox['reference']=$reference_id;
                    $reference_data_checkbox['optin_time']=date("Y-m-d H:i:s");
                    $this->basic->insert_data("messenger_bot_engagement_checkbox_reply",$reference_data_checkbox);
                    
                 }
            
                if($label_ids!="" && $user_reference_id==""){   // Update Label if only send-to-messenger. Don't for checkbox for first time. As we can't infromation
                
                    $post_data_label_assign=array("psid"=>$sender_id,"fb_page_id"=>$page_id,"label_auto_ids"=>$label_ids);
                    $url=base_url()."messenger_broadcaster/assign_label_webhook_call";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch,CURLOPT_POST,1);
                    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                    $reply_response=curl_exec($ch); 
                     
                } 
            }

            die();

            }
        }
        
        
         elseif((isset($response['entry'][0]['messaging'][0]['postback']['referral']['type']) && $response['entry'][0]['messaging'][0]['postback']['referral']['type']=="OPEN_THREAD" && isset($response['entry'][0]['messaging'][0]['postback']['referral']['ref'])) || 
        
        (isset($response['entry'][0]['messaging'][0]['postback']['payload']) && $response['entry'][0]['messaging'][0]['postback']['payload']=="GET_STARTED_PAYLOAD" ) ||
        (isset($response['entry'][0]['messaging'][0]['referral']['source']) && $response['entry'][0]['messaging'][0]['referral']['type']=="OPEN_THREAD"))
        
        //When not any conversation and get started button is added
        {
        
            /**Check If the Engagement add-on is installed or not. Check a table of this addon is exist or not**/
            
            if($this->db->table_exists('messenger_bot_engagement_2way_chat_plugin')){
        
        
            /* If get started not set, then get the refferal means already have the conversation */
            $reference_id = isset($response['entry'][0]['messaging'][0]['postback']['referral']['ref'])?$response['entry'][0]['messaging'][0]['postback']['referral']['ref']:$response['entry'][0]['messaging'][0]['referral']['ref'];
            
            $reference_source=isset($response['entry'][0]['messaging'][0]['postback']['referral']['source'])?$response['entry'][0]['messaging'][0]['postback']['referral']['source']:$response['entry'][0]['messaging'][0]['referral']['source'];
            
            
            if($reference_source=="CUSTOMER_CHAT_PLUGIN"){ // If from Custom CHat
                $table_name="messenger_bot_engagement_2way_chat_plugin";
                $plugin_name=$reference_source;
                $refferer_uri=isset($response['entry'][0]['messaging'][0]['postback']['referral']['referer_uri'])?$response['entry'][0]['messaging'][0]['postback']['referral']['referer_uri']:"";
                $drip_type="messenger_bot_engagement_2way_chat_plugin";
            }
                
            else if($reference_source=="SHORTLINK"){ // If from custom link
            
                $table_name="messenger_bot_engagement_mme";
                $plugin_name=$reference_source;
                $refferer_uri="N/A";
                $drip_type="messenger_bot_engagement_mme";
                
            }
            else if($reference_source=="MESSENGER_CODE"){ //if messenger codes
            
                $table_name="messenger_bot_engagement_messenger_codes";
                $plugin_name=$reference_source;
                $refferer_uri="N/A";
                $drip_type="messenger_bot_engagement_messenger_codes";
                
            }
            else{  // If come from page directly
                $table_name="";
                $plugin_name="FB PAGE";
                $refferer_uri="N/A";
                $drip_type="default";
                $this->assign_drip_messaging_id($drip_type,"0",$PAGE_AUTO_ID,$subscriber_info[0]['id']);
            }
            
            if($subscriber_new_old_info['is_new']){
                    $subscriber_id_update=$subscriber_info[0]['id'];
                    $update_data=array("refferer_id"=>$reference_id,"refferer_source"=>$plugin_name,"refferer_uri"=>$refferer_uri);
                    $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscriber_id_update),$update_data);
                }
            
            
            $postback_id="";
            
            if($table_name!=""){
            
            $engagementer_info= $this->basic->get_data($table_name,array("where"=>array("reference"=>$reference_id)));
            
            $plugin_auto_id=isset($engagementer_info[0]['id']) ? $engagementer_info[0]['id']:"";

            $label_ids=isset($engagementer_info[0]['label_ids']) ? $engagementer_info[0]['label_ids']:"";
            $template_id=isset($engagementer_info[0]['template_id']) ? $engagementer_info[0]['template_id']:"";
            
            if($template_id!=""){
                $postback_id_info= $this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$template_id)));
                $postback_id= isset($postback_id_info[0]['postback_id']) ? $postback_id_info[0]['postback_id'] :"";
                
            }
            
        }
            
            
            if($postback_id=="")
            
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','messenger_bot_page_info.bot_enabled' => '1');
                
                else    
                    $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1',"postback_id"=>$postback_id);
        
        }
        
        else{  // if engagement add-on not installed, then default query for get started. 
        
            $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'keyword_type'=>'get-started','messenger_bot_page_info.bot_enabled' => '1');
             /** Assign Drip Messaging Campaign ID ****/
            $drip_type="default";
            $this->assign_drip_messaging_id($drip_type,"0",$PAGE_AUTO_ID,$subscriber_info[0]['id']);
        }
            
                    
            
            $messages = $response['entry'][0]['messaging'][0]['message']['text'];
            $table_name = "messenger_bot";
            
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");
            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
            
            if($enable_mark_seen) // mark ass seen action
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);
            
            foreach ($messenger_bot_info as $key => $value) {
                $message_str = $value['message'];
                $message_array = json_decode($message_str,true);
                // if(!isset($message_array[1])) $message_array[1]=$message_array;
                if(!isset($message_array[1])){
                    $message_array_org=$message_array;
                    $message_array=array();
                    $message_array[1]=$message_array_org;
                }
                foreach($message_array as $msg)
                {
                    $template_type_file_track=$msg['message']['template_type'];
                    unset($msg['message']['template_type']);
                    
                    /** Spintax **/
                    if(isset($msg['message']['text']))
                        $msg['message']['text']=spintax_process($msg['message']['text']);               
                    
                    $msg['messaging_type'] = "RESPONSE";
                    $reply = json_encode($msg);                            
                    $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                    if(isset($subscriber_info[0]['first_name']))
                        $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                    if(isset($subscriber_info[0]['last_name']))
                        $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                    $access_token = $value['page_access_token'];
                    if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
                    
                        if($enable_typing_on){
                            $this->sender_action($sender_id,"typing_on",$access_token);
                            sleep($typing_on_delay_time);
                        }
                        if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
                            $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                            $url=base_url()."messenger_bot/send_reply_curl_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);  
                    
                        }
                        else
                             $reply_response=$this->send_reply($access_token,$reply);
                            /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }
                    }
                }

                
                if($this->db->table_exists('messenger_bot_engagement_2way_chat_plugin')){

                    /****   Update Drip Messaging Campaign ID ****/
                    $this->assign_drip_messaging_id($drip_type,$plugin_auto_id,$PAGE_AUTO_ID,$subscriber_info[0]['id']);    
               
                    if(!empty($label_ids)){
                    
                        $post_data_label_assign=array("psid"=>$sender_id,"fb_page_id"=>$page_id,"label_auto_ids"=>$label_ids);
                        $url=base_url()."messenger_broadcaster/assign_label_webhook_call";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch,CURLOPT_POST,1);
                        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                        $reply_response=curl_exec($ch); 
                         
                    } 
                }
                
                die();
            }
        }
        elseif (isset($response['entry'][0]['messaging'][0]['message']['quick_reply'])) //quick_reply
        {
            //catch payload_id from response
            $payload_id = $response['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
            $messages = $response['entry'][0]['messaging'][0]['message']['text'];
            $table_name = "messenger_bot";
            $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1');
            $this->db->where("FIND_IN_SET('$payload_id',messenger_bot.postback_id) !=", 0);
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");
            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
            /***    Insert email into database if it's email from quick reply ***/
            
            if($this->is_email($payload_id)){
                
                $fb_page_id=$subscriber_info[0]['page_id'];
                $user_id=$subscriber_info[0]['user_id'];
                $fb_user_id=$subscriber_info[0]['subscribe_id'];
                $fb_user_first_name=$subscriber_info[0]['first_name'];
                $fb_user_last_name=$subscriber_info[0]['last_name'];
                $profile_pic=$subscriber_info[0]['profile_pic'];
                $update_time=date("Y-m-d H:i:s");
                $email=$payload_id;
                
                $sql="INSERT INTO messenger_bot_quick_reply_email (fb_page_id,user_id,fb_user_id,fb_user_first_name,fb_user_last_name,
                    profile_pic,email,entry_time,last_update_time) VALUES ('$fb_page_id','$user_id','$fb_user_id','$fb_user_first_name',
                    '$fb_user_last_name','$profile_pic','$email','$update_time','$update_time')
                    ON DUPLICATE KEY UPDATE last_update_time='$update_time',email='$email';
                        ";
                $this->basic->execute_complex_query($sql);
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1',"keyword_type"=>"email-quick-reply");
                $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");
                $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
                $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
                $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
                
                $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
                if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            }
            elseif($this->is_phone_number($payload_id)){
            
                $fb_page_id=$subscriber_info[0]['page_id'];
                $user_id=$subscriber_info[0]['user_id'];
                $fb_user_id=$subscriber_info[0]['subscribe_id'];
                $fb_user_first_name=$subscriber_info[0]['first_name'];
                $fb_user_last_name=$subscriber_info[0]['last_name'];
                $profile_pic=$subscriber_info[0]['profile_pic'];
                $update_time=date("Y-m-d H:i:s");
                $phone_number=$payload_id;
                
                $sql="INSERT INTO messenger_bot_quick_reply_email (fb_page_id,user_id,fb_user_id,fb_user_first_name,fb_user_last_name,
                    profile_pic,phone_number,phone_number_entry_time,phone_number_last_update) 
                    VALUES ('$fb_page_id','$user_id','$fb_user_id','$fb_user_first_name',
                    '$fb_user_last_name','$profile_pic','$phone_number','$update_time','$update_time')
                    ON DUPLICATE KEY UPDATE phone_number_last_update='$update_time',phone_number='$phone_number';";
                
                    
                $this->basic->execute_complex_query($sql);
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1',"keyword_type"=>"phone-quick-reply");
                $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");
                $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
                
                $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
                $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
                $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
                if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            }
            
            
            
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);    
                
            foreach ($messenger_bot_info as $key => $value) {
                $message_str = $value['message'];
                $message_array = json_decode($message_str,true);
                // if(!isset($message_array[1])) $message_array[1]=$message_array;
                if(!isset($message_array[1])){
                    $message_array_org=$message_array;
                    $message_array=array();
                    $message_array[1]=$message_array_org;
                }
                foreach($message_array as $msg)
                {
                    $template_type_file_track=$msg['message']['template_type'];
                    unset($msg['message']['template_type']);
                    
                    /** Spintax **/
                    if(isset($msg['message']['text']))
                        $msg['message']['text']=spintax_process($msg['message']['text']);
                                
                    $msg['messaging_type'] = "RESPONSE";
                    $reply = json_encode($msg);                            
                    $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                    if(isset($subscriber_info[0]['first_name']))
                        $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                    if(isset($subscriber_info[0]['last_name']))
                        $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                    $access_token = $value['page_access_token'];
                    if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
                    
                        if($enable_typing_on){
                            $this->sender_action($sender_id,"typing_on",$access_token);
                            sleep($typing_on_delay_time);
                        }
                        if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
                            $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                            $url=base_url()."messenger_bot/send_reply_curl_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);  
                    
                        }
                        else
                             $reply_response=$this->send_reply($access_token,$reply);
                         /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }
                    }                    
                }

                  /** Assign Drip Messaging Campaign ID ****/
                $drip_type="default";
                $this->assign_drip_messaging_id($drip_type,"0",$PAGE_AUTO_ID,$subscriber_info[0]['id']);  


                /***Set labels if any setup available for this postback for quickReply ***/

                if($this->db->table_exists('messenger_bot_broadcast')){

                    $label_ids=isset($messenger_bot_info[0]['broadcaster_labels']) ? $messenger_bot_info[0]['broadcaster_labels']:"";
               
                    if(!empty($label_ids)){
                   
                        $post_data_label_assign=array("psid"=>$sender_id,"fb_page_id"=>$page_id,"label_auto_ids"=>$label_ids);
                        $url=base_url()."messenger_broadcaster/assign_label_webhook_call";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch,CURLOPT_POST,1);
                        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                        $reply_response=curl_exec($ch); 
                         
                    } 
                }

                if($this->db->table_exists('messenger_bot_thirdparty_webhook') && $this->basic->is_exist("messenger_bot_thirdparty_webhook",array("page_id"=>$page_id)))
                {
                    if($this->is_email($payload_id))
                    $this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_email");
                    else if($this->is_phone_number($payload_id))
                    $this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_phone_number");
                    else
                    $this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_postback",$payload_id);
                }


                die();
            }
        }
        elseif(isset($response['entry'][0]['messaging'][0]['postback']))//Clicking on Payload Button like Start Chatting
        {
            $payload_id = $response['entry'][0]['messaging'][0]['postback']['payload'];
            $messages = $response['entry'][0]['messaging'][0]['message']['text'];
            $table_name = "messenger_bot";
            $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1');
            $this->db->where("FIND_IN_SET('$payload_id',messenger_bot.postback_id) !=", 0);
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");
            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
            
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);

            if($payload_id=="UNSUBSCRIBE_QUICK_BOXER")
            {  
                $post_data_unsubscribe=array("psid"=>$sender_id,"fb_page_id"=>$page_id);
                $url=base_url()."messenger_broadcaster/unsubscribe_webhook_call";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_unsubscribe);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                $reply_response=curl_exec($ch);  
            }
            elseif($payload_id=="RESUBSCRIBE_QUICK_BOXER")
            {
                $post_data_unsubscribe=array("psid"=>$sender_id,"fb_page_id"=>$page_id);
                $url=base_url()."messenger_broadcaster/resubscribe_webhook_call";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_unsubscribe);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                $reply_response=curl_exec($ch);  
            }
            elseif($payload_id=="YES_START_CHAT_WITH_HUMAN")
            {
                if($this->basic->update_data("messenger_bot_subscriber",array("page_id"=>$page_id,"subscribe_id"=>$sender_id),array("status"=>"0")))
                {
                    $pagename= isset($users_expiry_info[0]['page_name']) ? $users_expiry_info[0]['page_name'] : "";
                    $chat_human_email=isset($users_expiry_info[0]['chat_human_email']) ? $users_expiry_info[0]['chat_human_email'] : "";

                    if($chat_human_email!="")
                    {
                        $message = "Hello,<br/> One of your messenger bot subscriber has stoped robot chat and wants to chat with human a agent.<br/><br/>";
                        $message.="Page : <a target='_BLANK' href='https://www.facebook.com/".$page_id."/inbox'>".$pagename."</a><br>";
                        $message.="Subscriber ID : ".$sender_id."<br>";
                        if(isset($subscriber_info[0]['first_name']))
                        $message.="Subscriber Name : ".$subscriber_info[0]['first_name'];
                        if(isset($subscriber_info[0]['last_name']))
                        $message.=" ".$subscriber_info[0]['last_name'];
                        $message.="<br/><br> Thank you";
                        
                        $mask="";
                        if($this->config->item("product_short_name")!="")
                        {
                            $message.=",".$this->config->item("product_short_name");
                            $mask=$this->config->item("product_short_name");
                        }

                        $subject="Want to chat with a human agent";
                        $this->_mail_sender($from, $chat_human_email, $subject, $message,$mask);
                    }
                }
            }
            elseif($payload_id=="YES_START_CHAT_WITH_BOT")
            {
                $this->basic->update_data("messenger_bot_subscriber",array("page_id"=>$page_id,"subscribe_id"=>$sender_id),array("status"=>"1"));                
            }

            foreach ($messenger_bot_info as $key => $value) {
                
                $message_str = $value['message'];
                $message_array = json_decode($message_str,true);
                // if(!isset($message_array[1])) $message_array[1]=$message_array;
                if(!isset($message_array[1])){
                    $message_array_org=$message_array;
                    $message_array=array();
                    $message_array[1]=$message_array_org;
                }
                foreach($message_array as $msg)
                {
                    $template_type_file_track=$msg['message']['template_type'];
                    unset($msg['message']['template_type']);
                    
                    /** Spintax **/
                    if(isset($msg['message']['text']))
                        $msg['message']['text']=spintax_process($msg['message']['text']);
                    
                    $msg['messaging_type'] = "RESPONSE";
                    $reply = json_encode($msg);                            
                    $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                    if(isset($subscriber_info[0]['first_name']))
                        $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                    if(isset($subscriber_info[0]['last_name']))
                        $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                    $access_token = $value['page_access_token'];
                    if((isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1") || $payload_id=="YES_START_CHAT_WITH_BOT"){
                        
                        if($enable_typing_on){
                            $this->sender_action($sender_id,"typing_on",$access_token);
                            sleep($typing_on_delay_time);
                        }
        
        
                        if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
                            $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                            $url=base_url()."messenger_bot/send_reply_curl_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);  
                    
                        }
                        else
                            $reply_response=$this->send_reply($access_token,$reply);
                         /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }
                    }                                       
                }

                /** Assign Drip Messaging Campaign ID ****/
                $drip_type="default";
                $this->assign_drip_messaging_id($drip_type,"0",$PAGE_AUTO_ID,$subscriber_info[0]['id']);


               /***Set labels if any setup available for this postback for quickReply ***/

                if($this->db->table_exists('messenger_bot_broadcast')){

                    $label_ids=isset($messenger_bot_info[0]['broadcaster_labels']) ? $messenger_bot_info[0]['broadcaster_labels']:"";
               
                    if(!empty($label_ids)){
                   
                        $post_data_label_assign=array("psid"=>$sender_id,"fb_page_id"=>$page_id,"label_auto_ids"=>$label_ids);
                        $url=base_url()."messenger_broadcaster/assign_label_webhook_call";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch,CURLOPT_POST,1);
                        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data_label_assign);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                        $reply_response=curl_exec($ch); 
                         
                    } 
                }


                if($this->db->table_exists('messenger_bot_thirdparty_webhook') && $this->basic->is_exist("messenger_bot_thirdparty_webhook",array("page_id"=>$page_id)))                
                 $this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_postback",$payload_id);


                die();
            }
        } 
        elseif(isset($response['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['lat'])){ 
            
            $lattitued= $response['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['lat'];
            $longitude= $response['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['coordinates']['long'];
            $location_bing_map=$response['entry'][0]['messaging'][0]['message']['attachments'][0]['url'];
            $user_location=$lattitued.",".$longitude;
            
            
            $fb_page_id=$subscriber_info[0]['page_id'];
            $user_id=$subscriber_info[0]['user_id'];
            $fb_user_id=$subscriber_info[0]['subscribe_id'];
            $fb_user_first_name=$subscriber_info[0]['first_name'];
            $fb_user_last_name=$subscriber_info[0]['last_name'];
            $profile_pic=$subscriber_info[0]['profile_pic'];
            $update_time=date("Y-m-d H:i:s");
            
            
            $sql="INSERT INTO messenger_bot_quick_reply_email (fb_page_id,user_id,fb_user_id,fb_user_first_name,fb_user_last_name,
                profile_pic,user_location,location_map_url) 
                VALUES ('$fb_page_id','$user_id','$fb_user_id','$fb_user_first_name',
                '$fb_user_last_name','$profile_pic','$user_location','$location_bing_map')
                ON DUPLICATE KEY UPDATE user_location='$user_location',location_map_url='$location_bing_map',last_update_time='$update_time'";
                
                    
                $this->basic->execute_complex_query($sql);
                $table_name = "messenger_bot";
                $where['where'] = array('messenger_bot.fb_page_id' => $page_id,'messenger_bot_page_info.bot_enabled' => '1',"keyword_type"=>"location-quick-reply");
                $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");
                $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'','','messenger_bot.id asc');
                
                $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
                $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
                $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
                if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
            
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);
            foreach ($messenger_bot_info as $key => $value) {
                $message_str = $value['message'];
                $message_array = json_decode($message_str,true);
                // if(!isset($message_array[1])) $message_array[1]=$message_array;
                if(!isset($message_array[1])){
                    $message_array_org=$message_array;
                    $message_array=array();
                    $message_array[1]=$message_array_org;
                }
                foreach($message_array as $msg)
                {
                    $template_type_file_track=$msg['message']['template_type'];
                    unset($msg['message']['template_type']);
                    
                    /** Spintax **/
                    if(isset($msg['message']['text']))
                        $msg['message']['text']=spintax_process($msg['message']['text']);
                    
                    $msg['messaging_type'] = "RESPONSE";
                    $reply = json_encode($msg);                            
                    $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                    if(isset($subscriber_info[0]['first_name']))
                        $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                    if(isset($subscriber_info[0]['last_name']))
                        $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                    $access_token = $value['page_access_token'];
                    if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
                    
                        if($enable_typing_on){
                            $this->sender_action($sender_id,"typing_on",$access_token);
                            sleep($typing_on_delay_time);
                        }
                        if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
                            $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                            $url=base_url()."messenger_bot/send_reply_curl_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);  
                    
                        }
                        else
                             $reply_response=$this->send_reply($access_token,$reply);
                         /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }
                    }                    
                }

                if($this->db->table_exists('messenger_bot_thirdparty_webhook') && $this->basic->is_exist("messenger_bot_thirdparty_webhook",array("page_id"=>$page_id)))                
                $this->thirdparty_webhook_trigger($page_id,$sender_id,"trigger_location");
            
                die();
            }
            
        }     
        else
        {   
            $table_name = "messenger_bot";
            $where['where'] = array('messenger_bot.fb_page_id' => $page_id, 'messenger_bot.keyword_type' => 'no match','messenger_bot_page_info.bot_enabled' => '1');
            $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot.fb_page_id,left");   
            $messenger_bot_info = $this->basic->get_data($table_name,$where,array("messenger_bot.*","messenger_bot_page_info.page_access_token as page_access_token","messenger_bot_page_info.enable_mark_seen as enable_mark_seen","messenger_bot_page_info.enbale_type_on as enbale_type_on","messenger_bot_page_info.reply_delay_time as reply_delay_time"),$join,'1','','messenger_bot.id asc');
            
            $enable_mark_seen=$messenger_bot_info[0]['enable_mark_seen'];
            $enable_typing_on=$messenger_bot_info[0]['enbale_type_on'];
            $typing_on_delay_time = $messenger_bot_info[0]['reply_delay_time'];
            if($typing_on_delay_time=="0") $typing_on_delay_time=1;
            
    
            if($enable_mark_seen)
                $this->sender_action($sender_id,"mark_seen",$messenger_bot_info[0]['page_access_token']);
            if(isset($messenger_bot_info[0]) && !empty($messenger_bot_info)){
                $message_str = $messenger_bot_info[0]['message'];
                $message_array = json_decode($message_str,true);
                // if(!isset($message_array[1])) $message_array[1]=$message_array;
                if(!isset($message_array[1])){
                    $message_array_org=$message_array;
                    $message_array=array();
                    $message_array[1]=$message_array_org;
                }
                foreach($message_array as $msg)
                {
                    $template_type_file_track=$msg['message']['template_type'];
                    unset($msg['message']['template_type']);
                    
                    /** Spintax **/
                    if(isset($msg['message']['text']))
                        $msg['message']['text']=spintax_process($msg['message']['text']);
            
                    $msg['messaging_type'] = "RESPONSE";
                    $reply = json_encode($msg);                            
                    $reply=str_replace('{"id":"replace_id"}', '{"id":"'.$sender_id.'"}', $reply);
                    if(isset($subscriber_info[0]['first_name']))
                        $reply=str_replace('#LEAD_USER_FIRST_NAME#', $subscriber_info[0]['first_name'], $reply);
                    if(isset($subscriber_info[0]['last_name']))
                        $reply=str_replace('#LEAD_USER_LAST_NAME#', $subscriber_info[0]['last_name'], $reply);
                    $access_token = $messenger_bot_info[0]['page_access_token'];
                    if(isset($subscriber_info[0]['status']) && $subscriber_info[0]['status']=="1"){
                    
                        if($enable_typing_on){
                            $this->sender_action($sender_id,"typing_on",$access_token);
                            sleep($typing_on_delay_time);
                        }
                        if($template_type_file_track=='video' || $template_type_file_track=='file' || $template_type_file_track=='audio'){
                            $post_data=array("access_token"=>$access_token,"reply"=>$reply);
                            $url=base_url()."messenger_bot/send_reply_curl_call";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch,CURLOPT_POST,1);
                            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
                            $reply_response=curl_exec($ch);  
                    
                        }
                        else
                             $reply_response=$this->send_reply($access_token,$reply);
                         /*****Insert into database messenger_bot_reply_error_log if get error****/
                             if(isset($reply_response['error']['message'])){
                                $bot_settings_id= $value['id'];
                                $reply_error_message= $reply_response['error']['message'];
                                $error_time= date("Y-m-d H:i:s");
                                $page_table_id=$value['page_id'];
                                $user_id=$value['user_id'];
                                
                                $error_insert_data=array("page_id"=>$page_table_id,"fb_page_id"=>$page_id,"user_id"=>$user_id,
                                                    "error_message"=>$reply_error_message,"bot_settings_id"=>$bot_settings_id,
                                                    "error_time"=>$error_time);
                                $this->basic->insert_data('messenger_bot_reply_error_log',$error_insert_data);
                                
                             }
                    }                                     
                }
                
                die();
            }
        }
    }

    public function assign_drip_messaging_id($drip_type="default",$check_box_plugin_id="0",$PAGE_AUTO_ID,$subscriber_table_id)
    {
        $date_time=date("Y-m-d H:i:s");
        
        if($this->db->table_exists('messenger_bot_drip_campaign')){

            $engagement_table_id= $check_box_plugin_id;
            
            $drip_messaging_campaign_info= $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("engagement_table_id"=>$engagement_table_id,"drip_type"=>$drip_type,"page_id"=>$PAGE_AUTO_ID)));
            
            $drip_campaign_id= isset($drip_messaging_campaign_info[0]['id']) ? $drip_messaging_campaign_info[0]['id']: "";
            
            $subscrier_update_data = array(
                    "messenger_bot_drip_campaign_id" =>$drip_campaign_id,
                    "messenger_bot_drip_last_completed_day"=>"0",
                    "drip_type" =>$drip_type,
                    "messenger_bot_drip_initial_date" =>$date_time
            );
            
            if($drip_campaign_id==""){
            
                $drip_messaging_campaign_info= $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("engagement_table_id"=>"0","drip_type"=>"default","page_id"=>$PAGE_AUTO_ID)));
            
                $drip_campaign_id= isset($drip_messaging_campaign_info[0]['id']) ? $drip_messaging_campaign_info[0]['id']: "";
                $drip_type="default";
                
                /** Don't update completed day if it's default. ****/
                $subscrier_update_data = array(
                    "messenger_bot_drip_campaign_id" =>$drip_campaign_id,
                    "drip_type" =>$drip_type,
                    "messenger_bot_drip_initial_date" =>$date_time
                );  
                
            }
                
            if($drip_type!="default")
                $subscrier_update_where= array("messenger_bot_drip_campaign_id !="=>$drip_campaign_id,"drip_type !=" =>$drip_type,"id" =>$subscriber_table_id); 
                
            else
                $subscrier_update_where= array("messenger_bot_drip_campaign_id"=>"0","id" =>$subscriber_table_id); 
            
            
            $this->basic->update_data("messenger_bot_subscriber",$subscrier_update_where,$subscrier_update_data);   
        }           

    }


    public function subscriber_info($access_token='',$sender_id='')
    {   
        $url = "https://graph.facebook.com/v2.6/$sender_id?access_token=$access_token";
        $ch = curl_init();
        $headers = array("Content-type: application/json");          
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
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

    public function index()
    {
        $total_enabled_bot = $this->basic->get_data('messenger_bot_page_info',['where'=>['user_id'=>$this->user_id,'bot_enabled'=>'1']],['count(id) as total_enabled_bot']);
        $total_errors_in_bot = $this->basic->get_data('messenger_bot_reply_error_log',['where'=>['user_id'=>$this->user_id]],['count(id) as total_errors_in_bot']);
        $total_enabled_persistent_menu = $this->basic->get_data('messenger_bot_page_info',['where'=>['user_id'=>$this->user_id,'persistent_enabled'=>'1']],['count(id) as total_enabled_persistent_menu']);
        $total_subscribers = $this->basic->get_data('messenger_bot_subscriber',['where'=>['user_id'=>$this->user_id]],['count(id) as total_subscribers']);
        $total_male_subscribers = $this->basic->get_data('messenger_bot_subscriber',['where'=>['user_id'=>$this->user_id,'gender'=>'male']],['count(id) as total_subscribers']);
        $total_female_subscribers = $this->basic->get_data('messenger_bot_subscriber',['where'=>['user_id'=>$this->user_id,'gender'=>'female']],['count(id) as total_subscribers']);
        $gender_type_data = array(
            0 => array(
                "value" => $total_male_subscribers[0]['total_subscribers'],
                "color" => '#FFCF75',
                "highlight" => '#FFCF75',
                "label" => $this->lang->line('Male subscriber')
            ),
            1 => array(
                "value" => $total_female_subscribers[0]['total_subscribers'],
                "color" => '#FF8000',
                "highlight" => '#FF8000',
                "label" => $this->lang->line('Female subscriber')
            )
        );
        $data['gender_type_data'] = $gender_type_data;
        $data['total_male_subscribers'] = $total_male_subscribers[0]['total_subscribers'];
        $data['total_female_subscribers'] = $total_female_subscribers[0]['total_subscribers'];
         
        $data['total_enabled_bot'] = $total_enabled_bot[0]['total_enabled_bot'];
        $data['total_errors_in_bot'] = $total_errors_in_bot[0]['total_errors_in_bot'];
        $data['total_enabled_persistent_menu'] = $total_enabled_persistent_menu[0]['total_enabled_persistent_menu'];
        $data['total_subscribers'] = $total_subscribers[0]['total_subscribers'];
        $curdate=date("Y-m-d");
        $from_date=date('Y-m-d', strtotime($curdate. " - 30 days"));
        $from_date = $from_date." 00:00:00";
        $to_date = $curdate." 23:59:59";
        
        $where = array();
        $where['where'] = array(
            "subscribed_at >=" => $from_date,
            "subscribed_at <=" => $to_date,
            "user_id" => $this->user_id
            );
        $select = array(
            "date_format(subscribed_at,'%Y-%m-%d') as date",
            "count(id) as number_of_subscriber"
            );
        $day_wise_subscribers = $this->basic->get_data('messenger_bot_subscriber',$where,$select,$join='',$limit='',$start='',$order_by='',$group_by="date");
        $total_subscribers = array();
        foreach($day_wise_subscribers as $value)
        {
            $total_subscribers[$value['date']] = $value['number_of_subscriber'];
        }


        $total_subscribers_data = array();
        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        
        for($i=0;$i<=$no_of_days;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));    

            if(isset($total_subscribers[$day_count]))
            {
                $daily_subscribers = $total_subscribers[$day_count];
                $total_subscribers_data[$i]['date'] = $day_count;
                $total_subscribers_data[$i]['subscribers'] = $daily_subscribers;
            }
            else
            {
                $daily_subscribers = 0;
                $total_subscribers_data[$i]['date'] = $day_count;
                $total_subscribers_data[$i]['subscribers'] = $daily_subscribers;
            }

            
        }
        $data['total_subscribers_data'] = $total_subscribers_data;


        $where = array();
        $where['where'] = array(
            "last_update_time >=" => $from_date,
            "last_update_time <=" => $to_date,
            "user_id" => $this->user_id
            );
        $select = array(
            "date_format(last_update_time,'%Y-%m-%d') as date",
            "count(id) as number_of_emails"
            );
        $day_wise_email_gain = $this->basic->get_data('messenger_bot_quick_reply_email',$where,$select,$join='',$limit='',$start='',$order_by='',$group_by="date");
        $email_gain = array();
        foreach($day_wise_email_gain as $value)
        {
            $email_gain[$value['date']] = $value['number_of_emails'];
        }
        $day_wise_total_email = array();
        for($i=0;$i<=$no_of_days;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($email_gain[$day_count]))
            {
                $total_emails = $email_gain[$day_count];
                $day_wise_total_email[$i]['date'] = $day_count;
                $day_wise_total_email[$i]['emails'] = $total_emails;
            }
            else
            {
                $total_emails = 0;
                $day_wise_total_email[$i]['date'] = $day_count;
                $day_wise_total_email[$i]['emails'] = $total_emails;
            }
        }
        $total_emails_gain = $this->basic->get_data('messenger_bot_quick_reply_email',['where'=>['user_id'=>$this->user_id]],['count(id) as number_of_emails']);
        $data['total_emails'] = $total_emails_gain[0]['number_of_emails'];
        $data['day_wise_total_email'] = $day_wise_total_email;
        $data['body'] = 'dashboard';
        $this->_viewcontroller($data);
    }
    
    public function activate()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if(!$_POST) exit();
        // if(!isset($_SERVER['HTTPS']))
        // {
        //     echo json_encode(array('status'=>'0','message'=>$this->lang->line('This add-on requires HTTPS.')));
        //     exit();
        // }
        $is_free_addon=false; 
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        if(!$is_free_addon)
        {
            $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
        }
        $verify_token=$this->_random_number_generator(15);
        $app_package_config_data = "<?php ";
        $app_package_config_data.= "\n\$config['webhook_verify_token'] = '$verify_token';\n";        
        $app_package_config_data.= "\n\$config['bot_backup_mode'] = '0';";        
        @file_put_contents(APPPATH.'modules/'.strtolower($this->router->fetch_class()).'/config/messenger_bot_config.php', $app_package_config_data, LOCK_EX); 
  
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array
        (           
            0 =>array
            (
                'name' => 'Messenger Bot',
                'icon' => 'fa fa-robot',
                'url' => '#',
                'is_external' => '0',
                'child_info' => array
                (
                    'have_child'=>'1', // parent has child menus, 0 means no child
                    'child'=>array // if status = 1 then you must add child array, other wise not need to set this index
                    (
                        0 => array
                        (
                            'name'=>'Dashboard',
                            'icon'=>'fa fa-dashboard',
                            'url' => 'messenger_bot/index',
                            'is_external' => '0'
                        ),
                        1 => array
                        (
                            'name'=>'General Settings',
                            'icon'=>'fa fa-cog',
                            'url' => 'messenger_bot/configuration',
                            'is_external' => '0'
                        ),
                        2 => array
                        (
                            'name'=>'Facebook API Settings',
                            'icon'=>'fa fa-facebook-official',
                            'url' => 'messenger_bot/facebook_config',
                            'is_external' => '0'
                        ),
                        3 => array
                        (
                            'name'=>'Import Account',
                            'icon'=>'fa fa-cloud-download',
                            'url' => 'messenger_bot/account_import',
                            'is_external' => '0'
                        ),
                        4 => array
                        (
                            'name'=>'Domain Whitelist',
                            'icon'=>'fa fa-check-circle',
                            'url' => 'messenger_bot/domain_whitelist',
                            'is_external' => '0'
                        ),
                        5 => array
                        (
                            'name'=>'Bot Settings',
                            'icon'=>'fa fa-sliders-h',
                            'url' => 'messenger_bot/bot_list',
                            'is_external' => '0'
                        ),
                        6 => array
                        (
                            'name'=>'Post-back Manager',
                            'icon'=>'fa fa-th-large',
                            'url' => 'messenger_bot/template_manager',
                            'is_external' => '0'
                        ),                        
                        7 => array
                        (
                            'name'=>'Cron Job',
                            'icon'=>'fa fa-clock-o',
                            'url' => 'messenger_bot/cron_job',
                            'is_external' => '0'
                        )
                        
                    )
                ),  
                'only_admin' => '0' ,
                'only_member' => '0'
            )            
        ); 
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array
        (
          0 =>"CREATE TABLE IF NOT EXISTS `messenger_bot` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` int(11) NOT NULL,
              `page_id` int(11) NOT NULL,
              `fb_page_id` varchar(200) NOT NULL,
              `template_type` enum('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel') NOT NULL DEFAULT 'text',
              `bot_type` enum('generic','keyword') NOT NULL DEFAULT 'generic',
              `keyword_type` enum('reply','post-back','no match','get-started') NOT NULL DEFAULT 'reply',
              `keywords` text NOT NULL,
              `message` text NOT NULL,
              `buttons` longtext NOT NULL,
              `images` longtext NOT NULL,
              `audio` varchar(255) NOT NULL,
              `video` varchar(255) NOT NULL,
              `file` varchar(255) NOT NULL,
              `status` enum('0','1') NOT NULL DEFAULT '1',
              `bot_name` varchar(200) NOT NULL,
              `postback_id` varchar(255) NOT NULL,
              `last_replied_at` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`,`page_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            1=>"CREATE TABLE IF NOT EXISTS `messenger_bot_config` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `app_name` varchar(100) DEFAULT NULL,
                  `api_id` varchar(250) DEFAULT NULL,
                  `api_secret` varchar(250) DEFAULT NULL,
                  `numeric_id` varchar(250) NOT NULL,
                  `user_access_token` varchar(500) DEFAULT NULL,
                  `status` enum('0','1') NOT NULL DEFAULT '1',
                  `deleted` enum('0','1') NOT NULL DEFAULT '0',
                  `user_id` int(11) NOT NULL,
                  `use_by` enum('only_me','everyone') NOT NULL DEFAULT 'only_me',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            2=>"CREATE TABLE IF NOT EXISTS `messenger_bot_domain_whitelist` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `messenger_bot_user_info_id` int(11) NOT NULL,
                  `page_id` int(11) NOT NULL,
                  `domain` tinytext NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`,`page_id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            3=>"CREATE TABLE IF NOT EXISTS `messenger_bot_page_info` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `messenger_bot_user_info_id` int(11) NOT NULL,
                  `page_id` varchar(200) NOT NULL,
                  `page_cover` text,
                  `page_profile` text,
                  `page_name` varchar(200) DEFAULT NULL,
                  `username` varchar(255) NOT NULL,
                  `page_access_token` text NOT NULL,
                  `page_email` varchar(200) DEFAULT NULL,
                  `add_date` date NOT NULL,
                  `deleted` enum('0','1') NOT NULL DEFAULT '0',
                  `bot_enabled` enum('0','1') NOT NULL DEFAULT '0',
                  `started_button_enabled` enum('0','1') NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `page_id` (`page_id`),
                  KEY `user_id` (`user_id`,`page_id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            4=>"CREATE TABLE IF NOT EXISTS `messenger_bot_postback` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `postback_id` varchar(255) NOT NULL,
                  `page_id` int(11) NOT NULL,
                  `use_status` enum('0','1') NOT NULL DEFAULT '0',
                  `status` enum('0','1') NOT NULL DEFAULT '1',
                  `messenger_bot_table_id` int(11) NOT NULL,
                  `bot_name` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`,`postback_id`,`page_id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            5=>"CREATE TABLE IF NOT EXISTS `messenger_bot_subscriber` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `page_id` varchar(200) NOT NULL,
                  `subscribe_id` varchar(255) NOT NULL,
                  `first_name` varchar(255) NOT NULL,
                  `last_name` varchar(255) NOT NULL,
                  `profile_pic` varchar(255) NOT NULL,
                  `gender` varchar(255) NOT NULL,
                  `locale` varchar(255) NOT NULL,
                  `timezone` varchar(255) NOT NULL,
                  `subscribed_at` datetime NOT NULL,
                  `status` enum('0','1') NOT NULL DEFAULT '1',
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`,`page_id`,`subscribe_id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            6=>"CREATE TABLE IF NOT EXISTS `messenger_bot_user_info` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `messenger_bot_config_id` int(11) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `access_token` text NOT NULL,
                  `name` varchar(200) DEFAULT NULL,
                  `email` varchar(200) DEFAULT NULL,
                  `fb_id` varchar(200) NOT NULL,
                  `add_date` date NOT NULL,
                  `deleted` enum('0','1') NOT NULL,
                  `need_to_delete` enum('0','1') NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            // extra module, this module aslo deleted manaually
            7=>"INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `deleted`) VALUES ('199', 'Messenger Bot - Account Import', '0', '0');",
            8=>"ALTER TABLE `messenger_bot_page_info` ADD `persistent_enabled` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `started_button_enabled`;",
            9=> "INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `deleted`) VALUES ('197', 'Messenger Bot - Persistent Menu', '0', '0');",
            10=>"INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`, `deleted`) VALUES ('198', 'Messenger Bot - Persistent Menu Copyright', '0', '0');",
            11=>"UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 197",
            12=>"UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 198;",
            13=>"UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 199;",
            14=>"UPDATE `modules` SET `extra_text` = '' WHERE `modules`.`id` = 200;",
            15=>"UPDATE `modules` SET `limit_enabled` = '0' WHERE `modules`.`id` = 198;",
            16=>"UPDATE menu_child_1 SET only_admin='1' WHERE module_access=200 AND serial=1;",
            17=> "CREATE TABLE IF NOT EXISTS `messenger_bot_persistent_menu` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `page_id` varchar(100) NOT NULL,
                  `locale` varchar(20) NOT NULL DEFAULT 'default',
                  `item_json` longtext NOT NULL,
                  `composer_input_disabled` enum('0','1') NOT NULL DEFAULT '0',
                  `poskback_id_json` text NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `page_id` (`page_id`,`locale`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
            18=>"ALTER TABLE `messenger_bot_postback` DROP INDEX `user_id`, ADD UNIQUE `user_id` (`user_id`, `postback_id`, `page_id`) USING BTREE;",
            19 => "ALTER TABLE  `messenger_bot_page_info` ADD  `enable_mark_seen` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0',
                ADD  `enbale_type_on` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0';",
            20 => "CREATE TABLE IF NOT EXISTS `messenger_bot_reply_error_log` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `page_id` int(11) NOT NULL,
                  `fb_page_id` varchar(200) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `error_message` varchar(250) NOT NULL,
                  `bot_settings_id` int(11) NOT NULL,
                  `error_time` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",
            21 => "CREATE TABLE IF NOT EXISTS `messenger_bot_quick_reply_email` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `fb_page_id` varchar(50) NOT NULL,
                  `user_id` int(11) NOT NULL,
                  `fb_user_id` varchar(50) NOT NULL,
                  `fb_user_first_name` varchar(100) CHARACTER SET utf8 NOT NULL,
                  `fb_user_last_name` varchar(100) CHARACTER SET utf8 NOT NULL,
                  `profile_pic` text NOT NULL,
                  `email` varchar(200) NOT NULL,
                  `entry_time` datetime NOT NULL,
                  `last_update_time` datetime NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `fb_page_id` (`fb_page_id`,`fb_user_id`,`email`,`user_id`)
                ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;",
            22 => "ALTER TABLE `messenger_bot_quick_reply_email` ADD `phone_number` VARCHAR(20) NOT NULL AFTER `last_update_time`, ADD `phone_number_entry_time` DATETIME NOT NULL AFTER `phone_number`, ADD `phone_number_last_update` DATETIME NOT NULL AFTER `phone_number_entry_time`;",
            23 => "ALTER TABLE `messenger_bot_quick_reply_email` DROP INDEX `fb_page_id`, ADD UNIQUE `fb_page_id` (`fb_page_id`, `fb_user_id`, `user_id`) USING BTREE;",
            
            24 => "ALTER TABLE `messenger_bot_postback` ADD `is_template` ENUM('0','1') NOT NULL AFTER `bot_name`, ADD `template_jsoncode` LONGTEXT NOT NULL AFTER `is_template`;",
            25 => "ALTER TABLE `messenger_bot` ADD `is_template` ENUM('0','1') NOT NULL AFTER `last_replied_at`;",
            26 => "ALTER TABLE `messenger_bot_postback` ADD `template_name` VARCHAR(255) NOT NULL AFTER `template_jsoncode`;",
            27 => "ALTER TABLE `messenger_bot_postback` ADD `template_for` ENUM('reply_message','unsubscribe','resubscribe') NOT NULL AFTER `template_name`;",
            28 => "ALTER TABLE `messenger_bot_subscriber` ADD `is_image_download` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `subscribed_at`, ADD `image_path` VARCHAR(250) NOT NULL AFTER `is_image_download`;",
            29 => "ALTER TABLE `messenger_bot` CHANGE `keyword_type` `keyword_type` ENUM('reply','post-back','no match','get-started','email-quick-reply','phone-quick-reply') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'reply';",
            30 => "ALTER TABLE `messenger_bot_postback` CHANGE `template_for` `template_for` ENUM('reply_message','unsubscribe','resubscribe','email-quick-reply','phone-quick-reply') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",
            31 => "ALTER TABLE `messenger_bot_page_info` ADD `reply_delay_time` INT NOT NULL AFTER `enbale_type_on`;",
            32 => "ALTER TABLE `messenger_bot_postback` ADD `template_id` INT(11) NOT NULL AFTER `template_for`;",
            33 => "ALTER TABLE `messenger_bot_postback` ADD `inherit_from_template` ENUM('0','1') NOT NULL AFTER `template_id`;",
            34 => "UPDATE `menu_child_1` SET `only_admin` = '0' WHERE `menu_child_1`.`url` = 'messenger_bot/index';",
            35 => "ALTER TABLE `messenger_bot` CHANGE `template_type` `template_type` ENUM('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','list') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'text';",
            36 => "ALTER TABLE `messenger_bot` CHANGE `keyword_type` `keyword_type` ENUM('reply','post-back','no match','get-started','email-quick-reply','phone-quick-reply','location-quick-reply') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'reply';",
            37 => "ALTER TABLE `messenger_bot_postback` CHANGE `template_for` `template_for` ENUM('reply_message','unsubscribe','resubscribe','email-quick-reply','phone-quick-reply','location-quick-reply') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",
            38 => "ALTER TABLE `messenger_bot_quick_reply_email` ADD `user_location` VARCHAR(30) NOT NULL AFTER `phone_number_last_update`, ADD `location_map_url` TEXT NOT NULL AFTER `user_location`;",
            39 =>"ALTER TABLE `messenger_bot_subscriber` ADD `refferer_id` VARCHAR(100) NOT NULL COMMENT 'get started refference number from ref parameter of chat plugin' AFTER `timezone`, ADD `refferer_source` VARCHAR(50) NOT NULL COMMENT 'CUSTOMER_CHAT_PLUGIN or SHORTLINK or Direct' AFTER `refferer_id`, ADD `refferer_uri` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'CUSTOMER_CHAT_PLUGIN URL' AFTER `refferer_source`; ",
            40=>"ALTER TABLE messenger_bot CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;",
            41=>"ALTER TABLE `messenger_bot_postback` CHANGE `postback_id` `postback_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",
            42=>"ALTER TABLE messenger_bot_postback CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;",
            43=>"ALTER TABLE `messenger_bot_subscriber` DROP INDEX `user_id`, ADD UNIQUE `user_id` (`user_id`, `page_id`, `subscribe_id`) USING BTREE;",
            44=>"ALTER TABLE `messenger_bot_page_info` CHANGE `bot_enabled` `bot_enabled` ENUM('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0';",
            45=>"ALTER TABLE `messenger_bot` ADD `broadcaster_labels` TINYTEXT NOT NULL COMMENT 'comma separated' AFTER `is_template`;",
            46=>"ALTER TABLE `messenger_bot_postback` ADD `broadcaster_labels` TINYTEXT NOT NULL COMMENT 'comma separated' AFTER `inherit_from_template`;",
            47=>"ALTER TABLE `messenger_bot_page_info` ADD `welcome_message` TINYTEXT NOT NULL AFTER `started_button_enabled`;",
            48=>"ALTER TABLE `messenger_bot_page_info` CHANGE `page_id` `page_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;",
            49=>"ALTER TABLE messenger_bot_page_info CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" ,
            50=>"ALTER TABLE `messenger_bot_subscriber` ADD `full_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `last_name`;",
            51=>"ALTER TABLE `messenger_bot_subscriber` ADD `is_imported` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `status`;",
            52=>"ALTER TABLE `messenger_bot_subscriber` ADD `is_updated_name` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `is_imported`;",
            53=>"ALTER TABLE `messenger_bot_subscriber` ADD `last_name_update_time` DATETIME NOT NULL AFTER `subscribed_at`;",
            54=>"ALTER TABLE `messenger_bot_page_info`  ADD `chat_human_email` VARCHAR(250) NOT NULL AFTER `welcome_message`;",
            55 =>"ALTER TABLE `messenger_bot_postback` CHANGE `template_for` `template_for` ENUM('reply_message','unsubscribe','resubscribe','email-quick-reply','phone-quick-reply','location-quick-reply','chat-with-human','chat-with-bot') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;",
            56=>"ALTER TABLE `messenger_bot` CHANGE `template_type` `template_type` ENUM('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','list','media') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text';"
        ); 
        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code,"Messenger Bot Enabled Page"); 
    }

    public function deactivate()
    {  
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $this->db->query("DELETE FROM `modules` WHERE `modules`.`id` = 197");
        $this->db->query("DELETE FROM `modules` WHERE `modules`.`id` = 198");
        $this->db->query("DELETE FROM `modules` WHERE `modules`.`id` = 199");

        if($this->db->table_exists('messenger_bot_saved_templates')) // Deactivating Bot Export Import Addon
        {
            $this->basic->delete_data("add_ons",array("unique_name"=>"messenger_bot_export_import"));
            $this->basic->delete_data("modules",array("id"=>257));
            $this->basic->delete_data("menu_child_1",array("url"=>'messenger_bot_export_import/saved_templates'));

            $install_txt_path=APPPATH."modules/messenger_bot_export_import/install.txt"; // path of install.txt
            if(!file_exists($install_txt_path)) // putting install.txt
            fopen($install_txt_path, "w");
        }

        if($this->is_messenger_bot_analytics_exist)
        {
            $this->basic->delete_data("add_ons",array("unique_name"=>"messenger_bot_analytics"));
            $this->basic->delete_data("modules",array("id"=>260));
            
            $install_txt_path=APPPATH."modules/messenger_bot_analytics/install.txt"; // path of install.txt
            if(!file_exists($install_txt_path)) // putting install.txt
            fopen($install_txt_path, "w");
        }
        
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    { 
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
         // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (
          0=>"DROP TABLE IF EXISTS `messenger_bot`;",
          1=>"DROP TABLE IF EXISTS `messenger_bot_config`;",
          2=>"DROP TABLE IF EXISTS `messenger_bot_domain_whitelist`;",
          3=>"DROP TABLE IF EXISTS `messenger_bot_page_info`;",
          4=>"DROP TABLE IF EXISTS `messenger_bot_postback`;",
          5=>"DROP TABLE IF EXISTS `messenger_bot_subscriber`;",
          6=>"DROP TABLE IF EXISTS `messenger_bot_user_info`;",
          7=>"DROP TABLE IF EXISTS `messenger_bot_persistent_menu`;",
          8=>"DELETE FROM `modules` WHERE `modules`.`id` = 199",
          9=>"DELETE FROM `modules` WHERE `modules`.`id` = 198",
          10=>"DELETE FROM `modules` WHERE `modules`.`id` = 197",
          11 => "DROP TABLE IF EXISTS `messenger_bot_reply_error_log`;",
          12 => "DROP TABLE IF EXISTS `messenger_bot_quick_reply_email`;"
        );  

        if($this->db->table_exists('messenger_bot_saved_templates')) // Deactivating Bot Export Import Addon
        {
            $this->basic->delete_data("add_ons",array("unique_name"=>"messenger_bot_export_import"));
            $this->basic->delete_data("modules",array("id"=>257));
            $this->basic->delete_data("menu_child_1",array("url"=>'messenger_bot_export_import/saved_templates'));
            $this->db->query("DROP TABLE IF EXISTS `messenger_bot_saved_templates`;");
            $addon_path=APPPATH."modules/messenger_bot_export_import"; // path of module folder
            $this->delete_directory($addon_path); 
        }

        if($this->is_messenger_bot_analytics_exist)
        {
            $this->basic->delete_data("add_ons",array("unique_name"=>"messenger_bot_analytics"));
            $this->basic->delete_data("modules",array("id"=>260));
            $addon_path=APPPATH."modules/messenger_bot_analytics"; // path of module folder
            $this->delete_directory($addon_path);
        } 
          

        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

    //=================================BOT SETTINGS===============================
    public function bot_list()
    {   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 
        $data['body'] = 'bot_list';
        $data['page_title'] = $this->lang->line('Bot Settings');  
        $table_name = "messenger_bot_page_info";
        $where['where'] = array('bot_enabled !=' => "0",'messenger_bot_page_info.user_id'=> $this->user_id);
        $join = array('messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("messenger_bot_page_info.*","messenger_bot_user_info.name as account_name","messenger_bot_user_info.fb_id"),$join,'','','page_name asc');
        $error_record = $this->basic->get_data('messenger_bot_reply_error_log',array('where'=>array('user_id'=>$this->user_id)),$select=array('page_id','count(id) as total_error'),$join='',$limit='',$start=NULL,$order_by='',$group_by='page_id');
        $error_record_array = array();
        foreach($error_record as $value)
        {
            $error_record_array[$value['page_id']] = $value['total_error'];
        }
        $data['error_record'] = $error_record_array;
        $len_page_info = count($page_info); 
        $data['page_info'] = $page_info;

        $data['package_list'] = $this->package_list(); // get user package

        // get eligible saved templates
        if($this->db->table_exists('messenger_bot_saved_templates')) 
        {
            if ($this->session->userdata("user_type")=="Member") 
            {
                $package_info=$this->session->userdata('package_info');
                $search_package_id=isset($package_info['id'])?$package_info['id']:'0';
                $where_custom="((FIND_IN_SET('".$search_package_id."',allowed_package_ids) <> 0 AND template_access='public') OR (template_access='private' AND user_id='".$this->user_id."'))";
            }
            else $where_custom="user_id='".$this->user_id."'";        

            $this->db->select('*');
            $this->db->where( $where_custom );
            $this->db->order_by("saved_at DESC");
            $query = $this->db->get('messenger_bot_saved_templates');
            $template_data=$query->result_array();

            // $template_list=array();
            // foreach ($template_data as $key => $value) 
            // {
            //    $template_list[$value['id']]=$value['template_name']." (".date("M j, y H:i",strtotime($value['saved_at'])).")";
            // }
            $data["saved_template_list"]=$template_data;
        }
        else $data["saved_template_list"]=array();
        // ----------------------------------
        $this->_viewcontroller($data);   
    }


    public function view_bot($bot_id= '0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200, $this->module_access))
        redirect ('home/login_page','location');
        if($bot_id == 0)
        die();
        $table_name = "messenger_bot";
        $where_bot['where'] = array('id' => $bot_id, 'status' => '1');
        $bot_info = $this->basic->get_data($table_name, $where_bot);
        if(!isset($bot_info[0]))
        redirect('messenger_bot/bot_list', 'location');
        $table_name = "messenger_bot_page_info";
        $where['where'] = array('bot_enabled' => "1", "messenger_bot_page_info.id"=>$bot_info[0]["page_id"]);
        $join = array('messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left");
        $page_info = $this->basic->get_data($table_name,$where, array("messenger_bot_page_info.*","messenger_bot_user_info.name as account_name","messenger_bot_user_info.fb_id"),$join);
        if(!isset($page_info[0]))
        redirect('messenger_bot/bot_list','location');
        $data["templates"]=$this->basic->get_enum_values("messenger_bot","template_type");
        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $data['body'] = 'view_bot_settings';
        $data['page_title'] = $this->lang->line('View Bot Settings');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"])));
        $poption=array();
        foreach ($postback_id_list as $key => $value) 
        {
            // if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;
        $data['postback_ids'] = $postback_id_list;
        $this->_viewcontroller($data);
    }

    public function edit_bot($bot_id='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200, $this->module_access))
        redirect ('home/login_page','location');
        if($bot_id == 0)
        die();
        $table_name = "messenger_bot";
        $where_bot['where'] = array('id' => $bot_id, 'status' => '1');
        $bot_info = $this->basic->get_data($table_name, $where_bot);
        if(!isset($bot_info[0]))
        redirect('messenger_bot/bot_list', 'location');
        $table_name = "messenger_bot_page_info";
        $where['where'] = array('bot_enabled' => "1", "messenger_bot_page_info.id"=>$bot_info[0]["page_id"], "messenger_bot_page_info.user_id"=>$this->user_id);
        $join = array('messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left");
        $page_info = $this->basic->get_data($table_name,$where, array("messenger_bot_page_info.*","messenger_bot_user_info.name as account_name","messenger_bot_user_info.fb_id"),$join);
        if(!isset($page_info[0]))
        redirect('messenger_bot/bot_list','location');
        $data["templates"]=$this->basic->get_enum_values("messenger_bot","template_type");
        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $data['body'] = 'edit_bot_settings';
        $data['page_title'] = $this->lang->line('Edit Bot Settings');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"])));
        $current_postbacks = array();
        foreach ($postback_id_list as $value) {
            if($value['messenger_bot_table_id'] == $bot_id)
            $current_postbacks[] = $value['postback_id'];
        }
        $data['postback_ids'] = $postback_id_list;
        $data['current_postbacks'] = $current_postbacks;
        $page_id=$page_info[0]['id'];// database id      
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1"),"or_where"=>array("messenger_bot_table_id"=>$bot_id)),'','','',$start=NULL,$order_by='template_name ASC');
        
        $poption=array();
        foreach ($postback_data as $key => $value) 
        {
            if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply" || $value["template_for"]=="chat-with-human" || $value["template_for"]=="chat-with-bot") continue;
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;

        if($this->basic->is_exist("add_ons",array("project_id"=>16)))
            $data['has_broadcaster_addon'] = 1;
        else
            $data['has_broadcaster_addon'] = 0;
        
        $this->_viewcontroller($data);
    }

    public function bot_settings($page_auto_id='0')
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 
        if($page_auto_id==0) exit();
        $table_name = "messenger_bot_page_info";
        $where['where'] = array('bot_enabled' => "1","messenger_bot_page_info.id"=>$page_auto_id,"messenger_bot_page_info.user_id"=>$this->user_id);
        $join = array('messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("messenger_bot_page_info.*","messenger_bot_user_info.name as account_name","messenger_bot_user_info.fb_id"),$join);
        if(!isset($page_info[0]))
        redirect('messenger_bot/bot_list', 'location'); 
        $bot_settings=$this->basic->get_data("messenger_bot",array("where"=>array("page_id"=>$page_auto_id,"is_template"=>"0")),'','','','','bot_name asc');
        
        $data["templates"]=$this->basic->get_enum_values("messenger_bot","template_type");
        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $data['body'] = 'bot_settings';
        $data['page_title'] = $this->lang->line('Bot Settings');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['bot_settings'] = $bot_settings;

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)));
        $data['postback_ids'] = $postback_id_list;

        if($this->basic->is_exist("add_ons",array("project_id"=>16)))
            $data['has_broadcaster_addon'] = 1;
        else
            $data['has_broadcaster_addon'] = 0;
        $this->_viewcontroller($data); 
    }

    public function get_postback()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id      
        $order_by=$this->input->post('order_by');     
        if($order_by=="") $order_by="id DESC";
        else $order_by=$order_by." ASC";
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1")),'','','',$start=NULL,$order_by);
        $push_postback="";
        foreach ($postback_data as $key => $value) 
        {
            if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply" || $value["template_for"]=="chat-with-human" || $value["template_for"]=="chat-with-bot") continue;
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        echo $push_postback;   
    }

    public function get_postback_for_persistent_menu()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id      
        $order_by=$this->input->post('order_by');     
        if($order_by=="") $order_by="id DESC";
        else $order_by=$order_by." ASC";
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id)),'','','',$start=NULL,$order_by);
        $push_postback="";
        foreach ($postback_data as $key => $value) 
        {
            if($value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $push_postback.="<option value='".$value['postback_id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        echo $push_postback;   
    }
    //=================================BOT SETTINGS===============================
    public function edit_generate_messenger_bot()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }
        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data['bot_name'] = $bot_name;
        $insert_data['fb_page_id'] = $page_id;
        $insert_data['keywords'] = trim($keywords_list);
        $insert_data['page_id'] = $page_table_id;
        // $insert_data['template_type'] = $template_type;
        $insert_data['keyword_type'] = $keyword_type;
        if($keyword_type == 'post-back')
            $insert_data['postback_id'] = implode(',', $keywordtype_postback_id);

        // $template_type = str_replace(' ', '_', $template_type);
        // domain white list section
        $messenger_bot_user_info_id = $this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_table_id)),array("messenger_bot_user_info_id","page_access_token"));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["messenger_bot_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");
        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();

        for ($k=1; $k <=3 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            // $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }
            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }



 
            if($template_type == 'media')
            {
                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'media_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'media_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }



            if($template_type == 'text_with_buttons')
            {
                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'text_with_buttons_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'text_with_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                    $button_text = 'quick_reply_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }
            }

            if($template_type == 'generic_template')
            {
                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['messenger_extensions'] = true;
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['webview_height_ratio'] = 'tall';
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['fallback_url'] = $generic_template_image_destination_link;

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'generic_template_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'carousel')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                for ($j=1; $j <=10 ; $j++) 
                {                                 
                    $carousel_image = 'carousel_image_'.$j.'_'.$k;
                    $carousel_title = 'carousel_title_'.$j.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$j.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['messenger_extensions'] = true;
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['webview_height_ratio'] = 'tall';
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['fallback_url'] = $$carousel_image_destination_link;

                    for ($i=1; $i <= 3 ; $i++) 
                    { 
                        $button_text = 'carousel_button_text_'.$j."_".$i.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$j."_".$i.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$j."_".$i.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$j."_".$i.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                        $button_call_us = 'carousel_button_call_us_'.$j."_".$i.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $button_postback_id;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if($button_type == 'web_url')
                        {
                            if($button_text != '' && $button_type != '' && $button_web_url != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }
                }
            }

            if($template_type == 'list')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'list';

                for ($j=1; $j <=4 ; $j++) 
                {                                 
                    $list_image = 'list_image_'.$j.'_'.$k;
                    if(!isset($$list_image) || $$list_image == '') continue;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$list_image;
                    $list_title = 'list_title_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$list_title;
                    $list_subtitle = 'list_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$list_subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                    $list_image_destination_link = 'list_image_destination_link_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$list_image_destination_link;
                    
                }

                $button_text = 'list_with_buttons_text_'.$k;
                $button_text = isset($$button_text) ? $$button_text : '';
                $button_type = 'list_with_button_type_'.$k;
                $button_type = isset($$button_type) ? $$button_type : '';
                $button_postback_id = 'list_with_button_post_id_'.$k;
                $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                $button_web_url = 'list_with_button_web_url_'.$k;
                $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                $button_call_us = 'list_with_button_call_us_'.$k;
                $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                if($button_type == 'post_back')
                {
                    if($button_text != '' && $button_type != '' && $button_postback_id != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'postback';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_postback_id;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $button_postback_id;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = $bot_name;
                        $postback_insert_data[] = $single_postback_insert_data; 
                    }
                }
                if($button_type == 'web_url')
                {
                    if($button_text != '' && $button_type != '' && $button_web_url != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = $button_web_url;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        if(!in_array($button_web_url, $white_listed_domain_array))
                        {
                            $need_to_whitelist_array[] = $button_web_url;
                        }
                    }
                }
                if($button_type == 'phone_number')
                {
                    if($button_text != '' && $button_type != '' && $button_call_us != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'phone_number';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_call_us;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }


            }

            if(isset($reply_bot[$k]))
            {                
                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }
        
        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("messenger_bot_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->messenger_bot_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $insert_data['message'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;
        $this->basic->update_data('messenger_bot',array("id" => $id),$insert_data);
        // $this->basic->delete_data('messenger_bot_postback',array('messenger_bot_table_id'=> $id));
        $messenger_bot_table_id = $id;
        
        $existing_postback_ids_array = array();
        $existing_postback_ids = $this->basic->get_data('messenger_bot_postback',array('where'=>array('messenger_bot_table_id'=>$messenger_bot_table_id)),array('postback_id'));
        if(!empty($existing_postback_ids))
        {
            foreach($existing_postback_ids as $value)
            {
                array_push($existing_postback_ids_array, strtoupper($value['postback_id']));
            }
        }

        $postback_insert_data_modified = array();
        $m=0;
        foreach($postback_insert_data as $value)
        {
            if(in_array(strtoupper($value['postback_id']), $existing_postback_ids_array)) continue;
            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['messenger_bot_table_id'] = $messenger_bot_table_id;
            $m++;
        }

        if($keyword_type == 'post-back' && !empty($keywordtype_postback_id))
        {   
            $this->db->where("page_id",$page_table_id);         
            $this->db->where_in("postback_id", $keywordtype_postback_id);
            $this->db->update('messenger_bot_postback', array('use_status' => '1'));
        }
        
        // if(!empty($postback_insert_data_modified))
        // $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);

        $this->session->set_flashdata('bot_update_success',1);
        echo json_encode(array("status" => "1", "message" =>$this->lang->line("bot settings has been updated successfully.")));        

    }


    public function ajax_generate_messenger_bot()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }
        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data['bot_name'] = $bot_name;
        $insert_data['fb_page_id'] = $page_id;
        $insert_data['keywords'] = trim($keywords_list);
        $insert_data['page_id'] = $page_table_id;
        // $insert_data['template_type'] = $template_type;
        $insert_data['keyword_type'] = $keyword_type;
        if($keyword_type == 'post-back')
            $insert_data['postback_id'] = implode(',', $keywordtype_postback_id);

        // $template_type = str_replace(' ', '_', $template_type);
        // domain white list section
        $messenger_bot_user_info_id = $this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_table_id)),array("messenger_bot_user_info_id","page_access_token"));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["messenger_bot_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");
        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();

        for ($k=1; $k <=3 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            // $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }
            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }



 
            if($template_type == 'media')
            {
                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'media_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'media_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }



            if($template_type == 'text_with_buttons')
            {
                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'text_with_buttons_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'text_with_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                    $button_text = 'quick_reply_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }
            }

            if($template_type == 'generic_template')
            {
                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;                    

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['messenger_extensions'] = true;
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['webview_height_ratio'] = 'tall';
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['fallback_url'] = $generic_template_image_destination_link;

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'generic_template_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'carousel')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                for ($j=1; $j <=10 ; $j++) 
                {                                 
                    $carousel_image = 'carousel_image_'.$j.'_'.$k;
                    $carousel_title = 'carousel_title_'.$j.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$j.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['messenger_extensions'] = true;
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['webview_height_ratio'] = 'tall';
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['fallback_url'] = $$carousel_image_destination_link;

                    for ($i=1; $i <= 3 ; $i++) 
                    { 
                        $button_text = 'carousel_button_text_'.$j."_".$i.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$j."_".$i.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$j."_".$i.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$j."_".$i.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                        $button_call_us = 'carousel_button_call_us_'.$j."_".$i.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $button_postback_id;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if($button_type == 'web_url')
                        {
                            if($button_text != '' && $button_type != '' && $button_web_url != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }
                }
            }

            if($template_type == 'list')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'list';

                for ($j=1; $j <=4 ; $j++) 
                {                                 
                    $list_image = 'list_image_'.$j.'_'.$k;
                    if(!isset($$list_image) || $$list_image == '') continue;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$list_image;
                    $list_title = 'list_title_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$list_title;
                    $list_subtitle = 'list_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$list_subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                    $list_image_destination_link = 'list_image_destination_link_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$list_image_destination_link;
                    
                }

                $button_text = 'list_with_buttons_text_'.$k;
                $button_text = isset($$button_text) ? $$button_text : '';
                $button_type = 'list_with_button_type_'.$k;
                $button_type = isset($$button_type) ? $$button_type : '';
                $button_postback_id = 'list_with_button_post_id_'.$k;
                $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                $button_web_url = 'list_with_button_web_url_'.$k;
                $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                $button_call_us = 'list_with_button_call_us_'.$k;
                $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                if($button_type == 'post_back')
                {
                    if($button_text != '' && $button_type != '' && $button_postback_id != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'postback';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_postback_id;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $button_postback_id;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = $bot_name;
                        $postback_insert_data[] = $single_postback_insert_data; 
                    }
                }
                if($button_type == 'web_url')
                {
                    if($button_text != '' && $button_type != '' && $button_web_url != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = $button_web_url;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        if(!in_array($button_web_url, $white_listed_domain_array))
                        {
                            $need_to_whitelist_array[] = $button_web_url;
                        }
                    }
                }
                if($button_type == 'phone_number')
                {
                    if($button_text != '' && $button_type != '' && $button_call_us != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'phone_number';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_call_us;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }


            }

            if(isset($reply_bot[$k]))
            {                
                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }

        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("messenger_bot_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->messenger_bot_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end
        
        $insert_data['message'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;        
        $this->basic->insert_data('messenger_bot',$insert_data);
        $messenger_bot_table_id = $this->db->insert_id();
        $postback_insert_data_modified = array();
        $m=0;
        foreach($postback_insert_data as $value)
        {
            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['messenger_bot_table_id'] = $messenger_bot_table_id;
            $m++;
        }

        if($keyword_type == 'post-back' && !empty($keywordtype_postback_id))
        {    
            $this->db->where("page_id",$page_table_id);        
            $this->db->where_in("postback_id", $keywordtype_postback_id);
            $this->db->update('messenger_bot_postback', array('use_status' => '1'));
        }
        
        // if(!empty($postback_insert_data_modified))
        // $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);
        $this->session->set_flashdata('bot_success',1);
        echo json_encode(array("status" => "1", "message" =>$this->lang->line("new bot settings has been stored successfully.")));
        
    }

    public function template_manager()
    {
        $data['body'] = 'template_manager';
        $data['page_title'] = $this->lang->line('Template Manager');
        $this->_viewcontroller($data);
    }
    public function template_manager_data()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'messenger_bot_postback.id'; 
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $page_name = trim($this->input->post("page_name", true));
        $postback = trim($this->input->post("postback", true));
        $is_searched = $this->input->post('is_searched', true);
        if($is_searched) 
        {
            $this->session->set_userdata('template_manager_search_page_name', $page_name);
            $this->session->set_userdata('template_manager_search_postback', $postback);
        }
        $search_page_names  = $this->session->userdata('template_manager_search_page_name');
        $search_postback  = $this->session->userdata('template_manager_search_postback');

        $where_simple=array();
        if ($search_page_names) $where_simple['page_name like ']    = "%".$search_page_names."%";
        if ($search_postback) $where_simple['postback_id like ']    = "%".$search_postback."%";
        $where_simple['messenger_bot_postback.user_id'] = $this->user_id;
        $where_simple['messenger_bot_postback.is_template'] = '1';
        $where_simple['messenger_bot_postback.template_for'] = 'reply_message';
        
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "messenger_bot_postback";
        $join = array('messenger_bot_page_info'=>'messenger_bot_postback.page_id=messenger_bot_page_info.id,left');
        $select = array('messenger_bot_postback.*','page_name');
        
        $info = $this->basic->get_data($table, $where, $select, $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="messenger_bot_postback.id", $join);
        $total_result = $total_rows_array[0]['total_rows'];

        $information = array();
        for($i=0;$i<count($info);$i++)
        {   
            $id = $info[$i]['id'];
            $information[$i]['template_name'] = $info[$i]['template_name'];
            $information[$i]['page_name'] = $info[$i]['page_name'];
            $information[$i]['postback_id'] = $info[$i]['postback_id'];
            $information[$i]['action'] = "<a class='btn btn-outline-warning' title='".$this->lang->line("edit")."' href='".base_url("messenger_bot/edit_template/$id")."'><i class='fa fa-edit'></i></a>&nbsp;<a class='btn btn-outline-danger delete_template' title='".$this->lang->line("delete")."' table_id='".$id."'><i class='fa fa-trash'></i></a>";
        }
        echo convert_to_grid_data($information, $total_result);

    }
    public function create_new_template($is_iframe="0",$default_page="")
    {
        $data['body'] = 'add_new_template';
        $data['page_title'] = $this->lang->line('Create new template');
        $data["templates"]=$this->basic->get_enum_values("messenger_bot","template_type");
        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $join = array('messenger_bot_user_info'=>'messenger_bot_page_info.messenger_bot_user_info_id=messenger_bot_user_info.id,left');
        $page_info = $this->basic->get_data('messenger_bot_page_info',array('where'=>array('messenger_bot_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('messenger_bot_page_info.id','page_name','name'),$join);
        $page_list = array();
        foreach($page_info as $value)
        {
            $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
        }
        $data['page_list'] = $page_list;
        $data['is_iframe'] = $is_iframe;
        $data['default_page'] = $default_page;
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id)));

        $data['postback_ids'] = $postback_id_list;
        if($this->basic->is_exist("add_ons",array("project_id"=>16)))
            $data['has_broadcaster_addon'] = 1;
        else
            $data['has_broadcaster_addon'] = 0;
        $this->_viewcontroller($data);
    }
    
    public function create_template_action()
    {
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }

        $user_all_postback = array();
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_table_id)));

        foreach($postback_id_list as $value)
        {
            array_push($user_all_postback, $value['postback_id']);
        }

        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data_to_bot = array();
        $insert_data['bot_name'] = $bot_name;
        $insert_data_to_bot['bot_name'] = $bot_name;
        $insert_data['template_name'] = $bot_name;
        $insert_data['postback_id'] = $template_postback_id;
        $insert_data_to_bot['postback_id'] = $template_postback_id;
        $insert_data['page_id'] = $page_table_id;
        $insert_data_to_bot['page_id'] = $page_table_id;
        $insert_data['is_template'] = '1';
        $insert_data_to_bot['is_template'] = '1';
        $insert_data['use_status'] = '1';

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        $label_ids=array_filter($label_ids);
        $new_label_ids=implode(',', $label_ids);
        $insert_data["broadcaster_labels"]=$new_label_ids;
        $insert_data_to_bot["broadcaster_labels"]=$new_label_ids;
        
        $messenger_bot_user_info_id = $this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_table_id)),array("messenger_bot_user_info_id","page_access_token","page_id"));
        $insert_data_to_bot['fb_page_id'] = $messenger_bot_user_info_id[0]['page_id'];
        
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["messenger_bot_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");

        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();
        for ($k=1; $k <=3 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            // $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }
            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }



 
            if($template_type == 'media')
            {
                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'media_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'media_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }



            if($template_type == 'text_with_buttons')
            {
                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'text_with_buttons_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'text_with_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                    $button_text = 'quick_reply_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }
            }

            if($template_type == 'generic_template')
            {
                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['messenger_extensions'] = true;
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['webview_height_ratio'] = 'tall';
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['fallback_url'] = $generic_template_image_destination_link;

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'generic_template_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'carousel')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                for ($j=1; $j <=10 ; $j++) 
                {                                 
                    $carousel_image = 'carousel_image_'.$j.'_'.$k;
                    $carousel_title = 'carousel_title_'.$j.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$j.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['messenger_extensions'] = true;
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['webview_height_ratio'] = 'tall';
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['fallback_url'] = $$carousel_image_destination_link;

                    for ($i=1; $i <= 3 ; $i++) 
                    { 
                        $button_text = 'carousel_button_text_'.$j."_".$i.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$j."_".$i.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$j."_".$i.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$j."_".$i.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                        $button_call_us = 'carousel_button_call_us_'.$j."_".$i.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $button_postback_id;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if($button_type == 'web_url')
                        {
                            if($button_text != '' && $button_type != '' && $button_web_url != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }
                }
            }

            if($template_type == 'list')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'list';

                for ($j=1; $j <=4 ; $j++) 
                {                                 
                    $list_image = 'list_image_'.$j.'_'.$k;
                    if(!isset($$list_image) || $$list_image == '') continue;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$list_image;
                    $list_title = 'list_title_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$list_title;
                    $list_subtitle = 'list_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$list_subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                    $list_image_destination_link = 'list_image_destination_link_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$list_image_destination_link;
                    
                }

                $button_text = 'list_with_buttons_text_'.$k;
                $button_text = isset($$button_text) ? $$button_text : '';
                $button_type = 'list_with_button_type_'.$k;
                $button_type = isset($$button_type) ? $$button_type : '';
                $button_postback_id = 'list_with_button_post_id_'.$k;
                $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                $button_web_url = 'list_with_button_web_url_'.$k;
                $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                $button_call_us = 'list_with_button_call_us_'.$k;
                $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                if($button_type == 'post_back')
                {
                    if($button_text != '' && $button_type != '' && $button_postback_id != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'postback';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_postback_id;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $button_postback_id;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = $bot_name;
                        $postback_insert_data[] = $single_postback_insert_data; 
                    }
                }
                if($button_type == 'web_url')
                {
                    if($button_text != '' && $button_type != '' && $button_web_url != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = $button_web_url;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        if(!in_array($button_web_url, $white_listed_domain_array))
                        {
                            $need_to_whitelist_array[] = $button_web_url;
                        }
                    }
                }
                if($button_type == 'phone_number')
                {
                    if($button_text != '' && $button_type != '' && $button_call_us != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'phone_number';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_call_us;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }


            }

            if(isset($reply_bot[$k]))
            {                
                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }

        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("messenger_bot_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->messenger_bot_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end
        
        $insert_data['template_jsoncode'] = json_encode($reply_bot_filtered,true);
        $insert_data_to_bot['message'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;        
        $insert_data_to_bot['user_id'] = $this->user_id;        
        $this->basic->insert_data('messenger_bot',$insert_data_to_bot);
        $messenger_bot_table_id = $this->db->insert_id();
        $insert_data['messenger_bot_table_id'] = $messenger_bot_table_id;
        $this->basic->insert_data('messenger_bot_postback',$insert_data);
        $template_id = $this->db->insert_id();
        $postback_insert_data_modified = array();

        $m=0;

        $unique_postbacks = array();

        foreach($postback_insert_data as $value)
        {
            if(in_array($value['postback_id'], $user_all_postback)) continue;
            if(in_array($value['postback_id'], $unique_postbacks)) continue;

            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['template_id'] = $template_id;
            $postback_insert_data_modified[$m]['inherit_from_template'] = '1';
            array_push($unique_postbacks, $value['postback_id']);
            $m++;
        }

        // if($keyword_type == 'post-back' && !empty($keywordtype_postback_id))
        // {            
        //     $this->db->where_in("postback_id", $keywordtype_postback_id);
        //     $this->db->update('messenger_bot_postback', array('use_status' => '1'));
        // }
        
        if(!empty($postback_insert_data_modified))
        $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);
        $this->session->set_flashdata('bot_success',1);
        echo json_encode(array("status" => "1", "message" =>$this->lang->line("New template has been stored successfully.")));
        
    }

    public function edit_template($postback_table_id=0)
    {
        if($postback_table_id == 0) exit();
        $table_name = "messenger_bot_postback";
        $where_bot['where'] = array('id' => $postback_table_id, 'status' => '1', 'user_id'=>$this->user_id);
        $bot_info = $this->basic->get_data($table_name, $where_bot);
        if(empty($bot_info)) redirect('messenger_bot/template_manager', 'location');
        $data['body'] = 'edit_template';
        $data['page_title'] = $this->lang->line('Edit template');
        $data["templates"]=$this->basic->get_enum_values("messenger_bot","template_type");
        $data["keyword_types"]=$this->basic->get_enum_values("messenger_bot","keyword_type");
        $join = array('messenger_bot_user_info'=>'messenger_bot_page_info.messenger_bot_user_info_id=messenger_bot_user_info.id,left');
        $page_info = $this->basic->get_data('messenger_bot_page_info',array('where'=>array('messenger_bot_page_info.user_id'=>$this->user_id,'bot_enabled'=>'1')),array('messenger_bot_page_info.id','page_name','name'),$join);
        $page_list = array();
        foreach($page_info as $value)
        {
            $page_list[$value['id']] = $value['page_name']." [".$value['name']."]";
        }
        $data['page_list'] = $page_list;
        $data['bot_info'] = isset($bot_info[0]) ? $bot_info[0] : array();

        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$bot_info[0]["page_id"]),'where_not_in'=>array('postback_id'=>array('UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_HUMAN','YES_START_CHAT_WITH_BOT'))));

        $current_postbacks = array();
        foreach ($postback_id_list as $value) {
            if($value['template_id'] == $postback_table_id || $value['id'] == $postback_table_id)
            $current_postbacks[] = $value['postback_id'];
        }
        $data['postback_ids'] = $postback_id_list;
        $data['current_postbacks'] = $current_postbacks;

        $broadcastser_exist = $this->broadcastser_exist();
        if($broadcastser_exist)
        {
            $table_type = 'messenger_bot_broadcast_contact_group';
            $where_type['where'] = array('user_id'=>$this->user_id,"page_id"=>$bot_info[0]["page_id"],"unsubscribe"=>"0","invisible"=>"0");
            $data['info_type'] = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='group_name');
        }
        else $data['info_type']=array();


        if($broadcastser_exist)
            $data['has_broadcaster_addon'] = 1;
        else
            $data['has_broadcaster_addon'] = 0;

        $this->_viewcontroller($data);
    }

    public function edit_template_action()
    {  
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }

        // $template_type = trim($template_type);
        $insert_data = array();
        $insert_data['bot_name'] = $bot_name;
        $insert_data['template_name'] = $bot_name;
        $insert_data['postback_id'] = $template_postback_id;
        $insert_data['page_id'] = $page_table_id;
        $insert_data['is_template'] = '1';

        if(!isset($label_ids) || !is_array($label_ids)) $label_ids=array();
        $label_ids=array_filter($label_ids);
        $new_label_ids=implode(',', $label_ids);
        $insert_data["broadcaster_labels"]=$new_label_ids;

        // domain white list section
        $messenger_bot_user_info_id = $this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_table_id)),array("messenger_bot_user_info_id","page_access_token"));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["messenger_bot_user_info_id"];
        $white_listed_domain = $this->basic->get_data("messenger_bot_domain_whitelist",array("where"=>array("user_id"=>$this->user_id,"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"page_id"=>$page_table_id)),"domain");
        $white_listed_domain_array = array();
        foreach ($white_listed_domain as $value) {
            $white_listed_domain_array[] = $value['domain'];
        }
        $need_to_whitelist_array = array();
        // domain white list section

        $postback_insert_data = array();
        $reply_bot = array();
        $bot_message = array();

        for ($k=1; $k <=3 ; $k++) 
        {    
            $template_type = 'template_type_'.$k;
            if(!isset($$template_type)) continue;
            $template_type = $$template_type;
            // $insert_data['template_type'] = $template_type;
            $template_type = str_replace(' ', '_', $template_type);

            if($template_type == 'text')
            {
                $text_reply = 'text_reply_'.$k;
                $text_reply = isset($$text_reply) ? $$text_reply : '';
                if($text_reply != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $text_reply;
                    
                }
            }
            if($template_type == 'image')
            {
                $image_reply_field = 'image_reply_field_'.$k;
                $image_reply_field = isset($$image_reply_field) ? $$image_reply_field : '';
                if($image_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'image';
                    $reply_bot[$k]['attachment']['payload']['url'] = $image_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'audio')
            {
                $audio_reply_field = 'audio_reply_field_'.$k;
                $audio_reply_field = isset($$audio_reply_field) ? $$audio_reply_field : '';
                if($audio_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'audio';
                    $reply_bot[$k]['attachment']['payload']['url'] = $audio_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
                
            }
            if($template_type == 'video')
            {
                $video_reply_field = 'video_reply_field_'.$k;
                $video_reply_field = isset($$video_reply_field) ? $$video_reply_field : '';
                if($video_reply_field != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'video';
                    $reply_bot[$k]['attachment']['payload']['url'] = $video_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;                    
                }
            }
            if($template_type == 'file')
            {
                $file_reply_field = 'file_reply_field_'.$k;
                $file_reply_field = isset($$file_reply_field) ? $$file_reply_field : '';
                if($file_reply_field != '')
                {       
                    $reply_bot[$k]['template_type'] = $template_type;             
                    $reply_bot[$k]['attachment']['type'] = 'file';
                    $reply_bot[$k]['attachment']['payload']['url'] = $file_reply_field;
                    $reply_bot[$k]['attachment']['payload']['is_reusable'] = true;
                }
            }



 
            if($template_type == 'media')
            {
                $media_input = 'media_input_'.$k;
                $media_input = isset($$media_input) ? $$media_input : '';
                if($media_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'media';
                    $template_media_type = '';
                    if (strpos($media_input, '/videos/') !== false) {
                        $template_media_type = 'video';
                    }
                    else
                        $template_media_type = 'image';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['media_type'] = $template_media_type;
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['url'] = $media_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'media_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'media_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'media_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'media_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'media_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }



            if($template_type == 'text_with_buttons')
            {
                $text_with_buttons_input = 'text_with_buttons_input_'.$k;
                $text_with_buttons_input = isset($$text_with_buttons_input) ? $$text_with_buttons_input : '';
                if($text_with_buttons_input != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'button';
                    $reply_bot[$k]['attachment']['payload']['text'] = $text_with_buttons_input;                    
                }

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'text_with_buttons_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'text_with_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'text_with_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'text_with_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'text_with_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'quick_reply')
            {
                $quick_reply_text = 'quick_reply_text_'.$k;
                $quick_reply_text = isset($$quick_reply_text) ? $$quick_reply_text : '';
                if($quick_reply_text != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['text'] = $quick_reply_text;                    
                }

                for ($i=1; $i <= 11 ; $i++) 
                { 
                    $button_text = 'quick_reply_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_postback_id = 'quick_reply_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_type = 'quick_reply_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    if($button_type=='post_back')
                    {
                        if($button_text != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'text';
                            $reply_bot[$k]['quick_replies'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['quick_replies'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }                    
                    }
                    if($button_type=='phone_number')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_phone_number';
                    }
                    if($button_type=='user_email')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'user_email';
                    }
                    if($button_type=='location')
                    {
                        $reply_bot[$k]['quick_replies'][$i-1]['content_type'] = 'location';
                    }

                }
            }

            if($template_type == 'generic_template')
            {
                $generic_template_title = 'generic_template_title_'.$k;
                $generic_template_title = isset($$generic_template_title) ? $$generic_template_title : '';
                $generic_template_image = 'generic_template_image_'.$k;
                $generic_template_image = isset($$generic_template_image) ? $$generic_template_image : '';
                $generic_template_subtitle = 'generic_template_subtitle_'.$k;
                $generic_template_subtitle = isset($$generic_template_subtitle) ? $$generic_template_subtitle : '';
                $generic_template_image_destination_link = 'generic_template_image_destination_link_'.$k;
                $generic_template_image_destination_link = isset($$generic_template_image_destination_link) ? $$generic_template_image_destination_link : '';

                if($generic_template_title != '')
                {
                    $reply_bot[$k]['template_type'] = $template_type;
                    $reply_bot[$k]['attachment']['type'] = 'template';
                    $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['title'] = $generic_template_title;                   
                }

                if($generic_template_subtitle != '')
                $reply_bot[$k]['attachment']['payload']['elements'][0]['subtitle'] = $generic_template_subtitle;

                if($generic_template_image!="")
                {
                    $reply_bot[$k]['attachment']['payload']['elements'][0]['image_url'] = $generic_template_image;
                    if($generic_template_image_destination_link!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['elements'][0]['default_action']['url'] = $generic_template_image_destination_link;
                    }

                    if(function_exists('getimagesize') && $generic_template_image!='') 
                    {
                        list($width, $height, $type, $attr) = getimagesize($generic_template_image);
                        if($width==$height)
                            $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                    }

                }
                
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['messenger_extensions'] = true;
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['webview_height_ratio'] = 'tall';
                // $reply_bot['attachment']['payload']['elements'][0]['default_action']['fallback_url'] = $generic_template_image_destination_link;

                for ($i=1; $i <= 3 ; $i++) 
                { 
                    $button_text = 'generic_template_button_text_'.$i.'_'.$k;
                    $button_text = isset($$button_text) ? $$button_text : '';
                    $button_type = 'generic_template_button_type_'.$i.'_'.$k;
                    $button_type = isset($$button_type) ? $$button_type : '';
                    $button_postback_id = 'generic_template_button_post_id_'.$i.'_'.$k;
                    $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                    $button_web_url = 'generic_template_button_web_url_'.$i.'_'.$k;
                    $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                    $button_call_us = 'generic_template_button_call_us_'.$i.'_'.$k;
                    $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                    if($button_type == 'post_back')
                    {
                        if($button_text != '' && $button_type != '' && $button_postback_id != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'postback';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_postback_id;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            $single_postback_insert_data = array();
                            $single_postback_insert_data['user_id'] = $this->user_id;
                            $single_postback_insert_data['postback_id'] = $button_postback_id;
                            $single_postback_insert_data['page_id'] = $page_table_id;
                            $single_postback_insert_data['bot_name'] = $bot_name;
                            $postback_insert_data[] = $single_postback_insert_data; 
                        }
                    }
                    if($button_type == 'web_url')
                    {
                        if($button_text != '' && $button_type != '' && $button_web_url != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'] = $button_web_url;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                            if(!in_array($button_web_url, $white_listed_domain_array))
                            {
                                $need_to_whitelist_array[] = $button_web_url;
                            }
                        }
                    }
                    if($button_type == 'phone_number')
                    {
                        if($button_text != '' && $button_type != '' && $button_call_us != '')
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] = 'phone_number';
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] = $button_call_us;
                            $reply_bot[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'] = $button_text;
                        }
                    }
                }
            }

            if($template_type == 'carousel')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'generic';
                for ($j=1; $j <=10 ; $j++) 
                {                                 
                    $carousel_image = 'carousel_image_'.$j.'_'.$k;
                    $carousel_title = 'carousel_title_'.$j.'_'.$k;

                    if(!isset($$carousel_title) || $$carousel_title == '') continue;

                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$carousel_title;
                    $carousel_subtitle = 'carousel_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$carousel_subtitle;

                    if(isset($$carousel_image) && $$carousel_image!="")
                    {
                        $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$carousel_image;                    
                        $carousel_image_destination_link = 'carousel_image_destination_link_'.$j.'_'.$k;
                        if($$carousel_image_destination_link!="") 
                        {
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                            $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$carousel_image_destination_link;
                        }

                        if(function_exists('getimagesize') && $$carousel_image!='') 
                        {
                            list($width, $height, $type, $attr) = getimagesize($$carousel_image);
                            if($width==$height)
                                $reply_bot[$k]['attachment']['payload']['image_aspect_ratio'] = 'square';
                        }

                    }
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['messenger_extensions'] = true;
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['webview_height_ratio'] = 'tall';
                    // $reply_bot['attachment']['payload']['elements'][$j-1]['default_action']['fallback_url'] = $$carousel_image_destination_link;

                    for ($i=1; $i <= 3 ; $i++) 
                    { 
                        $button_text = 'carousel_button_text_'.$j."_".$i.'_'.$k;
                        $button_text = isset($$button_text) ? $$button_text : '';
                        $button_type = 'carousel_button_type_'.$j."_".$i.'_'.$k;
                        $button_type = isset($$button_type) ? $$button_type : '';
                        $button_postback_id = 'carousel_button_post_id_'.$j."_".$i.'_'.$k;
                        $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                        $button_web_url = 'carousel_button_web_url_'.$j."_".$i.'_'.$k;
                        $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                        $button_call_us = 'carousel_button_call_us_'.$j."_".$i.'_'.$k;
                        $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                        if($button_type == 'post_back')
                        {
                            if($button_text != '' && $button_type != '' && $button_postback_id != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'postback';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_postback_id;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $button_postback_id;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = $bot_name;
                                $postback_insert_data[] = $single_postback_insert_data; 
                            }
                        }
                        if($button_type == 'web_url')
                        {
                            if($button_text != '' && $button_type != '' && $button_web_url != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'web_url';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] = $button_web_url;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                                if(!in_array($button_web_url, $white_listed_domain_array))
                                {
                                    $need_to_whitelist_array[] = $button_web_url;
                                }
                            }
                        }
                        if($button_type == 'phone_number')
                        {
                            if($button_text != '' && $button_type != '' && $button_call_us != '')
                            {
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] = 'phone_number';
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] = $button_call_us;
                                $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'] = $button_text;
                            }
                        }
                    }
                }
            }

            if($template_type == 'list')
            {
                $reply_bot[$k]['template_type'] = $template_type;
                $reply_bot[$k]['attachment']['type'] = 'template';
                $reply_bot[$k]['attachment']['payload']['template_type'] = 'list';

                for ($j=1; $j <=4 ; $j++) 
                {                                 
                    $list_image = 'list_image_'.$j.'_'.$k;
                    if(!isset($$list_image) || $$list_image == '') continue;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['image_url'] = $$list_image;
                    $list_title = 'list_title_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['title'] = $$list_title;
                    $list_subtitle = 'list_subtitle_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['subtitle'] = $$list_subtitle;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['type'] = 'web_url';
                    $list_image_destination_link = 'list_image_destination_link_'.$j.'_'.$k;
                    $reply_bot[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'] = $$list_image_destination_link;
                    
                }

                $button_text = 'list_with_buttons_text_'.$k;
                $button_text = isset($$button_text) ? $$button_text : '';
                $button_type = 'list_with_button_type_'.$k;
                $button_type = isset($$button_type) ? $$button_type : '';
                $button_postback_id = 'list_with_button_post_id_'.$k;
                $button_postback_id = isset($$button_postback_id) ? $$button_postback_id : '';
                $button_web_url = 'list_with_button_web_url_'.$k;
                $button_web_url = isset($$button_web_url) ? $$button_web_url : '';
                $button_call_us = 'list_with_button_call_us_'.$k;
                $button_call_us = isset($$button_call_us) ? $$button_call_us : '';
                if($button_type == 'post_back')
                {
                    if($button_text != '' && $button_type != '' && $button_postback_id != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'postback';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_postback_id;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $button_postback_id;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = $bot_name;
                        $postback_insert_data[] = $single_postback_insert_data; 
                    }
                }
                if($button_type == 'web_url')
                {
                    if($button_text != '' && $button_type != '' && $button_web_url != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'web_url';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['url'] = $button_web_url;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                        if(!in_array($button_web_url, $white_listed_domain_array))
                        {
                            $need_to_whitelist_array[] = $button_web_url;
                        }
                    }
                }
                if($button_type == 'phone_number')
                {
                    if($button_text != '' && $button_type != '' && $button_call_us != '')
                    {
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['type'] = 'phone_number';
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['payload'] = $button_call_us;
                        $reply_bot[$k]['attachment']['payload']['buttons'][0]['title'] = $button_text;
                    }
                }


            }

            if(isset($reply_bot[$k]))
            {                
                $bot_message[$k]['recipient'] = array('id'=>'replace_id');
                $bot_message[$k]['message'] = $reply_bot[$k];
            }

        }

        $reply_bot_filtered = array();
        $m=0;
        foreach ($bot_message as $value) {
            $m++;
            $reply_bot_filtered[$m] = $value;
        }

        // domain white list section start
        $this->load->library("messenger_bot_login"); 
        $domain_whitelist_insert_data = array();
        foreach($need_to_whitelist_array as $value)
        {
            $response=$this->messenger_bot_login->domain_whitelist($page_access_token,$value);
            if($response['status'] != '0')
            {
                $temp_data = array();
                $temp_data['user_id'] = $this->user_id;
                $temp_data['messenger_bot_user_info_id'] = $messenger_bot_user_info_id;
                $temp_data['page_id'] = $page_table_id;
                $temp_data['domain'] = $value;
                $temp_data['created_at'] = date("Y-m-d H:i:s");
                $domain_whitelist_insert_data[] = $temp_data;
            }
        }
        if(!empty($domain_whitelist_insert_data))
            $this->db->insert_batch('messenger_bot_domain_whitelist',$domain_whitelist_insert_data);
        // domain white list section end

        $insert_data['template_jsoncode'] = json_encode($reply_bot_filtered,true);
        $insert_data['user_id'] = $this->user_id;
        $this->basic->update_data('messenger_bot_postback',array("id" => $id),$insert_data);

        $existing_data = $this->basic->get_data('messenger_bot_postback',array('where'=>array('id'=>$id)));
        $this->basic->update_data('messenger_bot',array('id'=>$existing_data[0]['messenger_bot_table_id']),array('message'=>$existing_data[0]['template_jsoncode'],"broadcaster_labels"=>$new_label_ids,'bot_name'=>$existing_data[0]['template_name']));

        $messenger_bot_table_id = $existing_data[0]['messenger_bot_table_id'];  

        $existing_postback_ids_array = array();
        $existing_postback_ids = $this->basic->get_data('messenger_bot_postback',array('where'=>array('page_id'=>$page_table_id,'use_status'=>'1')),array('postback_id'));


        $this->basic->delete_data('messenger_bot_postback',array('page_id'=>$page_table_id,'template_id'=>$id,'use_status'=>'0','inherit_from_template'=>'1'));
        if(!empty($existing_postback_ids))
        {
            foreach($existing_postback_ids as $value)
            {
                array_push($existing_postback_ids_array, strtoupper($value['postback_id']));
            }
        }


        $postback_insert_data_modified = array();
        $m=0;
        $unique_postbacks = array();
        foreach($postback_insert_data as $value)
        {
            if(in_array(strtoupper($value['postback_id']), $unique_postbacks)) continue;
            if(in_array(strtoupper($value['postback_id']), $existing_postback_ids_array)) continue;
            if($value['postback_id'] == 'UNSUBSCRIBE_QUICK_BOXER' || $value['postback_id'] == 'RESUBSCRIBE_QUICK_BOXER' || $value['postback_id'] == 'YES_START_CHAT_WITH_HUMAN' || $value['postback_id'] == 'YES_START_CHAT_WITH_BOT') continue;
            $postback_insert_data_modified[$m]['user_id'] = $value['user_id'];
            $postback_insert_data_modified[$m]['postback_id'] = $value['postback_id'];
            $postback_insert_data_modified[$m]['page_id'] = $value['page_id'];
            $postback_insert_data_modified[$m]['bot_name'] = $value['bot_name'];
            $postback_insert_data_modified[$m]['messenger_bot_table_id'] = $messenger_bot_table_id;
            $postback_insert_data_modified[$m]['inherit_from_template'] = '1';
            $postback_insert_data_modified[$m]['template_id'] = $id;
            array_push($unique_postbacks, $value['postback_id']);
            $m++;
        }


        // if($keyword_type == 'post-back' && !empty($keywordtype_postback_id))
        // {            
        //     $this->db->where_in("postback_id", $keywordtype_postback_id);
        //     $this->db->update('messenger_bot_postback', array('use_status' => '1'));
        // }
        
        if(!empty($postback_insert_data_modified))
        $this->db->insert_batch('messenger_bot_postback',$postback_insert_data_modified);

        $this->session->set_flashdata('bot_update_success',1);
        echo json_encode(array("status" => "1", "message" =>$this->lang->line("Template been updated successfully.")));        

    }

    public function ajax_delete_template_info()
    {
        $id = $this->input->post('table_id',true);
        $postback_info = $this->basic->get_data('messenger_bot_postback',array('where'=>array('id'=>$id)));
        $postback_id = $postback_info[0]['postback_id'];
        $search_content = '%"payload":"'.$postback_id.'"%';
        $bot_info = $this->basic->get_data('messenger_bot',array('where'=>array('message like'=>$search_content)));
        if(!empty($bot_info))
        {
            $response = "<div class='text-center alert alert-danger'>".$this->lang->line('You can not delete this template because it is being used in the following bots. First make sure that these templates are free to delete. You can do this by editing or deleting the following bots.')."</div><br>";
            $response.= '
                 <script>
                    $j(document).ready(function() {
                        $(".table-responsive").mCustomScrollbar({
                            autoHideScrollbar:true,
                            theme:"3d-dark",          
                            axis: "x"
                        });
                        $("#need_to_delete_bots").DataTable();
                    }); 
                 </script>
                 <div class="table-responsive">
                 <table id="need_to_delete_bots" class="table table-bordered">
                     <thead>
                         <tr>
                             <th>'.$this->lang->line("SN.").'</th>
                             <th>'.$this->lang->line("Bot Name").'</th>
                             <th>'.$this->lang->line("Kyeword").'</th>
                             <th>'.$this->lang->line("Keyword Type").'</th>
                             <th class="text-center">'.$this->lang->line("Actions").'</th>
                         </tr>
                     </thead>
                     <tbody>';
            $sn = 0;
            foreach($bot_info as $value)
            {
                $sn++;
                $bot_id = $value['id'];
                $response .= '<tr>
                            <td>'.$sn.'</td>
                            <td>'.$value['bot_name'].'</td>
                            <td>'.$value['keywords'].'</td>
                            <td>'.$value['keyword_type'].'</td>
                            <td class="text-center"><a class="btn btn-outline-warning" title="'.$this->lang->line("edit").'" target="_BLANK" href="'.base_url("messenger_bot/edit_bot/$bot_id").'"><i class="fa fa-edit"></i></a></td>
                        </tr>';
            }
            $response .= '</tbody>
                 </table></div>';
            echo $response;
        }
        else
        {
            $child_postback_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("template_id"=>$id)),array('postback_id'));
            $this->basic->delete_data('messenger_bot_postback',array('id'=>$id));
            $this->basic->delete_data('messenger_bot_postback',array('template_id'=>$id));
            $this->basic->delete_data('messenger_bot',array('postback_id'=>$postback_id));
            foreach($child_postback_info as $value)
            {
                $this->basic->delete_data('messenger_bot',array('postback_id'=>$value['postback_id'])); 
            }
            echo "success";
        }
    }
    
    public function upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $folder_path = FCPATH."upload/image";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        $output_dir = FCPATH."upload/image/".$this->user_id;
        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="image_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".jpg,.jpeg,.png,.gif";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }

            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }

    public function delete_uploaded_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/image/".$this->user_id."/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }
    public function upload_live_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/video";
        $folder_path = FCPATH."upload/video";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="video_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".mov,.mpeg4,.mp4,.avi,.wmv,.mpegps,.flv,.3gpp,.webm";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename); 
            echo json_encode($filename);
        }
    }

    public function delete_uploaded_live_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/video/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }
       // audio/pdf/doc file upload section
    public function upload_audio_file()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/audio";
        $folder_path = FCPATH."upload/audio";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="audio_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".amr,.mp3,.wav,.WAV,.MP3,.AMR";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename); 
            echo json_encode($filename);
        }
    }

    public function delete_audio_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/audio/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }

    public function upload_general_file()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();
        $ret=array();
        $output_dir = FCPATH."upload/file";
        $folder_path = FCPATH."upload/file";
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="file_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            $allow=".doc,.docx,.pdf,.txt,.ppt,.pptx,.xls,.xlsx";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("go_live_video_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("go_live_video_filename", $filename); 
            echo json_encode($filename);
        }
    }

    public function delete_general_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/file/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }
    //===========================ENABLE DISABLE STARTED Button====================


    public function get_started_welcome_message()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access)) exit();
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo json_encode(array('status'=>'0','message'=>'This function is disabled from admin account in this demo!!'));
                exit();
            }
        }

        $page_id=$this->input->post('table_id');
        $welcome_message=$this->input->post('welcome_message');
        $started_button_enabled=$this->input->post('started_button_enabled');
        $this->load->library("messenger_bot_login");

        $page_data=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_id)));
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
       
        if($started_button_enabled=='1')
        {
            $response=$this->messenger_bot_login->add_get_started_button($page_access_token);
            if(!isset($response['error']))
            {
                $response2=$this->messenger_bot_login->set_welcome_message($page_access_token,$welcome_message);
                if(!isset($response2['error']))
                {
                   $this->basic->update_data("messenger_bot_page_info",array("id"=>$page_id),array("started_button_enabled"=>$started_button_enabled,"welcome_message"=>$welcome_message));
                   echo json_encode(array('status'=>'1','message'=>$this->lang->line("Get started button has been enabled successfully.")));
                }
                else
                {
                    $error_msg2=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                    echo json_encode(array('status'=>'0','message'=>$error_msg2));
                }
            }
            else
            {
                $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                echo json_encode(array('status'=>'0','message'=>$error_msg));
            }
        }
        else
        {
            $response=$this->messenger_bot_login->delete_get_started_button($page_access_token);
            if(!isset($response['error']))
            {
                $response2=$this->messenger_bot_login->unset_welcome_message($page_access_token);
                if(!isset($response2['error']))
                {
                   $this->basic->update_data("messenger_bot_page_info",array("id"=>$page_id),array("started_button_enabled"=>$started_button_enabled,"welcome_message"=>""));
                   echo json_encode(array('status'=>'1','message'=>$this->lang->line("Get started button has been disabled successfully.")));
                }
                else
                {
                    $error_msg2=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                    echo json_encode(array('status'=>'0','message'=>$error_msg2));
                }
            }
            else
            {
                $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                echo json_encode(array('status'=>'0','message'=>$error_msg));
            }

        }
    }

    public function export_bot()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();
        
        $page_id=$this->input->post('export_id');
        $template_name=$this->input->post('template_name',true);
        $template_description=$this->input->post('template_description',true);
        $template_preview_image=$this->input->post('template_preview_image',true);
        $template_access=$this->input->post('template_access',true);
        $allowed_package_ids=$this->input->post('allowed_package_ids',true);

        $template_preview_image=str_replace(base_url('upload/image/'.$this->user_id.'/'), '', $template_preview_image);

        if(!is_array($allowed_package_ids) || $template_access=='private')  $allowed_package_ids=array();

        $get_bot_settings=$this->get_bot_settings($page_id);
        $savedata=json_encode($get_bot_settings);

        if($this->session->userdata('user_type') != 'Admin') $template_access='private';

        $this->basic->insert_data("messenger_bot_saved_templates",array("template_name"=>$template_name,"savedata"=>$savedata,"saved_at"=>date("Y-m-d H:i:s"),"user_id"=>$this->user_id,"template_access"=>$template_access,"description"=>$template_description,"preview_image"=>$template_preview_image,"allowed_package_ids"=>implode(',', $allowed_package_ids)));
        $insert_id=$this->db->insert_id();

        $message="<div class='alert alert-success text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Bot template has been saved to database successfully.")."</div><br><a class='btn-block btn btn-outline-info' target='_BLANK' href='".base_url('messenger_bot_export_import/saved_templates')."'><i class='fa fa-save'></i> ".$this->lang->line("My Saved Templates")."</a><a target='_BLANK' class='btn-block btn btn-outline-primary' href='".base_url('messenger_bot/export_bot_download/').$insert_id."'><i class='fa fa-file-download'></i> ".$this->lang->line("Download Template")."</a>";
        echo json_encode(array('status'=>'0','message'=>$message));
    }

    public function export_bot_download($id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if($id==0) exit();

        $save_data=$this->basic->get_data("messenger_bot_saved_templates",array("where"=>array("id"=>$id)));
        if(!isset($save_data[0])) exit();

        $template_name=isset($save_data[0]['template_name'])?$save_data[0]['template_name']:"";
        $savedata=isset($save_data[0]['savedata'])?$save_data[0]['savedata']:"";

        $template_name = preg_replace("/[^a-z0-9]+/i", "", $template_name);
        $filename=$template_name.".json";
        $f = fopen('php://memory', 'w'); 
        fwrite($f, $savedata);
        fseek($f, 0);
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        fpassthru($f);  
    }

    public function upload_json_template()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $output_dir = FCPATH."upload";
        if (!file_exists($output_dir)) {
            mkdir($output_dir, 0755, true);
        }
        if (isset($_FILES["myfile"])) 
        {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="json_template_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;


            $allow=".json";
            $allow=str_replace('.', '', $allow);
            $allow=explode(',', $allow);
            if(!in_array(strtolower($ext), $allow)) 
            {
                echo json_encode("Are you kidding???");
                exit();
            }
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            echo json_encode($filename);
        }
    }

    public function upload_json_template_delete() // deletes the uploaded video to upload another one
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();
        $output_dir = FCPATH."upload/";
        if(isset($_POST["op"]) && $_POST["op"] == "delete" && isset($_POST['name']))
        {
             $fileName =$_POST['name'];
             $fileName=str_replace("..",".",$fileName); //required. if somebody is trying parent folder files 
             $filePath = $output_dir. $fileName;
             if (file_exists($filePath)) 
             {
                unlink($filePath);
             }
        }
    }


    public function import_bot_check()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();

        $template_id=$this->input->post('template_id',true);
        $page_id=$this->input->post('import_id',true);
        $json_upload_input=$this->input->post('json_upload_input',true);

        if($template_id=="" && $json_upload_input=="")
        {
            echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'0','message'=>$this->lang->line("No template found also no json found.")));
            exit();
        }

        if($template_id!="" && $json_upload_input!="")
        {
            echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'0','message'=>$this->lang->line("You can not choose both template and upload file at the same time.")));
            exit();
        }

        if($json_upload_input!="")
        {
            $path=FCPATH.'upload/'.$json_upload_input;
            $array='';
            if(file_exists($path))
            {
                $json=file_get_contents($path);
                $array=json_decode($json,true);
            }
            if(!is_array($array))
            {
                 echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'0','message'=>$this->lang->line("Uploaded json is not a valid template json.")));
                 exit();
            }
        }

        if($this->basic->is_exist("messenger_bot",array("page_id"=>$page_id)) || $this->basic->is_exist("messenger_bot_postback",array("page_id"=>$page_id)) || $this->basic->is_exist("messenger_bot_persistent_menu",array("page_id"=>$page_id)) )
        {
            echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'1','message'=>$this->lang->line("Template has not been imported because there are existing bot settings or persistent menu settings found. Importing this template will delete all your previous bot settings, persistent menu settings as well as get started welcome screen message etc. Do you want to delete all your previous settings for this page and import this template?")));
            exit();
        }
        
        echo json_encode(array('json_upload_input'=>$json_upload_input,'page_id'=>$page_id,'template_id'=>$template_id,'status'=>'1','message'=>$this->lang->line("System has finished data checking and ready to import new template settings. Are you sure that you want to import this template?")));     
     }

    public function import_bot()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if(!$_POST) exit();

        $template_id=$this->input->post('template_id',true);
        $page_id=$this->input->post('page_id',true);
        $json_upload_input=$this->input->post('json_upload_input',true);


        $pagedata=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_id,"user_id"=>$this->user_id)));       
        if(!isset($pagedata[0]))
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("Page not found")));
            exit();
        }

        $jsondata='';
        if($template_id!="")
        {
            $get_bot_settings=$this->basic->get_data("messenger_bot_saved_templates",array("where"=>array("id"=>$template_id)));        
            if(!isset($get_bot_settings[0]))
            {
                echo json_encode(array('status'=>'0','message'=>$this->lang->line("Template not found")));
                exit();
            }
            $jsondata=$get_bot_settings[0]['savedata'];
        }
        else
        {
            $path=FCPATH.'upload/'.$json_upload_input;
            if(file_exists($path))
            {
                $jsondata=file_get_contents($path);
                @unlink($path); 
            }          
        }

        $this->db->db_debug = FALSE; //disable debugging for queries
        $this->db->trans_start();

        // deleting current settings so that we can import new settings
        $this->basic->delete_data("messenger_bot",array("page_id"=>$page_id,"user_id"=>$this->user_id));
        $this->basic->delete_data("messenger_bot_postback",array("page_id"=>$page_id,"user_id"=>$this->user_id));
        $this->basic->delete_data("messenger_bot_persistent_menu",array("page_id"=>$page_id));
        // -------------------------------------------------------------

        $savedata=json_decode($jsondata,true);        
        $fb_page_id=isset($pagedata[0]['page_id'])?$pagedata[0]['page_id']:"";
        $page_access_token=isset($pagedata[0]['page_access_token'])?$pagedata[0]['page_access_token']:"";

        $bot_settings=isset($savedata['bot_settings'])?$savedata['bot_settings']:array();
        $empty_postback_settings=isset($savedata['empty_postback_settings'])?$savedata['empty_postback_settings']:array();
        $persistent_menu_settings=isset($savedata['persistent_menu_settings'])?$savedata['persistent_menu_settings']:array();
        $bot_general_info=isset($savedata['bot_general_info'])?$savedata['bot_general_info']:array();

        // inserting messenger_bot + messenger_bot_postback data        
        foreach ($bot_settings as $key => $value)
        {
            $bot_info=isset($value['message_bot'])?$value['message_bot']:array();

            $messenger_bot_row=array
            (
                "user_id"=>$this->user_id,
                "page_id"=>$page_id,
                "fb_page_id"=>$fb_page_id
            );
            foreach ($bot_info as $key2 => $value2) 
            {
              if($key2=="postback_template_info") continue;
              $messenger_bot_row[$key2]=$value2;
            }           

            $this->basic->insert_data("messenger_bot",$messenger_bot_row);
            $messenger_bot_insert_id=$this->db->insert_id();      

            $postback_template_info=isset($value['message_bot']['postback_template_info'])?$value['message_bot']['postback_template_info']:array(); // getting postback data
            foreach ($postback_template_info as $key2 => $value2) 
            {               
                $messenger_bot_postback_row=array
                (
                    "user_id"=>$this->user_id,
                    "page_id"=>$page_id
                );
                foreach ($value2 as $key3 => $value3)
                {
                   if($key3=="postback_child") continue;
                   $messenger_bot_postback_row[$key3]=$value3;
                }   
                $messenger_bot_postback_row['messenger_bot_table_id']=$messenger_bot_insert_id;
                $messenger_bot_postback_row['template_id']=0;

                $this->basic->insert_data("messenger_bot_postback",$messenger_bot_postback_row);
                $messenger_bot_postback_insert_id=$this->db->insert_id();  

                $postback_template_info2=isset($value2['postback_child'])?$value2['postback_child']:array(); // getting postback data level2

                
                foreach ($postback_template_info2 as $key3 => $value3) 
                {
                   $messenger_bot_postback_row2=array
                   (
                        "user_id"=>$this->user_id,
                        "page_id"=>$page_id
                   );
                   foreach ($value3 as $key4 => $value4) 
                   {
                     $messenger_bot_postback_row2[$key4]=$value4;
                   }
                   $messenger_bot_postback_row2['messenger_bot_table_id']=0;
                   $messenger_bot_postback_row2['template_id']=$messenger_bot_postback_insert_id;
                   $this->basic->insert_data("messenger_bot_postback",$messenger_bot_postback_row2);
                }
                
            }              
        }
        // ----------------------------------------------------------------


        // inserting empty postback
        foreach ($empty_postback_settings as $key => $value) 
        {           
            $messenger_bot_postback_empty_row=array
            (
                "user_id"=>$this->user_id,
                "page_id"=>$page_id
            );
            foreach ($value as $key2 => $value2)
            {
               $messenger_bot_postback_empty_row[$key2]=$value2;
            }   
            $messenger_bot_postback_empty_row['template_id']=0;
            $this->basic->insert_data("messenger_bot_postback",$messenger_bot_postback_empty_row);            
        }
        //-----------------------------------------------------------------



        // inserting persistent menu
        if($this->session->userdata('user_type') == 'Admin' || in_array(197,$this->module_access))
        {
            foreach ($persistent_menu_settings as $key => $value) 
            {
                $persistent_menu_row=array();
                foreach ($value as $key2 => $value2) 
                {
                   $persistent_menu_row[$key2]=$value2;
                }
                $persistent_menu_row['page_id']=$page_id;
                $persistent_menu_row['user_id']=$this->user_id;
                unset($persistent_menu_row['id']);
                $this->basic->insert_data("messenger_bot_persistent_menu",$persistent_menu_row); 
            }
        }
        //-----------------------------------------------------------------

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            echo "<div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$this->lang->line("Import was unsuccessful. Database error occured during importing template.")."</div>";
            exit();
        }

        $welcome_message=isset($bot_general_info['welcome_message'])?$bot_general_info['welcome_message']:"";
        $started_button_enabled=isset($bot_general_info['started_button_enabled'])?$bot_general_info['started_button_enabled']:"0";
        $persistent_enabled=isset($bot_general_info['persistent_enabled'])?$bot_general_info['persistent_enabled']:"0";
        $enable_mark_seen=isset($bot_general_info['enable_mark_seen'])?$bot_general_info['enable_mark_seen']:"0";
        $enbale_type_on=isset($bot_general_info['enbale_type_on'])?$bot_general_info['enbale_type_on']:"0";
        $reply_delay_time=isset($bot_general_info['reply_delay_time'])?$bot_general_info['reply_delay_time']:"0";

        $this->load->library("messenger_bot_login"); 

        //enabling get started
        $error_msg_array=array();
        $success_msg_array=array();
        if($started_button_enabled=='1')
        {
            $response=$this->messenger_bot_login->add_get_started_button($page_access_token);
            if(!isset($response['error']))
            {
                $response2=$this->messenger_bot_login->set_welcome_message($page_access_token,$welcome_message);
                if(!isset($response2['error']))
                {
                   $this->basic->update_data("messenger_bot_page_info",array("id"=>$page_id,"user_id"=>$this->user_id),array("started_button_enabled"=>"1","welcome_message"=>$welcome_message));
                   $success_msg=$this->lang->line("Successful");
                   $success_msg=$this->lang->line("Enable Get Started")." : ".$success_msg;
                   array_push($success_msg_array, $success_msg);
                }
                else
                {
                    $error_msg=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $error_msg=$this->lang->line("Enable Get Started")." : ".$error_msg;
                    array_push($error_msg_array, $error_msg);
                }
            }
            else
            {
                $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                $error_msg=$this->lang->line("Enable Get Started")." : ".$error_msg;
                array_push($error_msg_array, $error_msg);
            }
        }
        else
        {
            $response=$this->messenger_bot_login->delete_get_started_button($page_access_token);
            if(!isset($response['error']))
            {
                $response2=$this->messenger_bot_login->unset_welcome_message($page_access_token);
                if(!isset($response2['error']))
                {
                   $this->basic->update_data("messenger_bot_page_info",array("id"=>$page_id,"user_id"=>$this->user_id),array("started_button_enabled"=>"0","welcome_message"=>""));
                   $success_msg=$this->lang->line("Successful");
                   $success_msg=$this->lang->line("Disable Get Started")." : ".$success_msg;
                   array_push($success_msg_array, $success_msg);
                }
                else
                {
                    $error_msg=isset($response2['error']['message'])?$response2['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $error_msg=$this->lang->line("Disable Get Started")." : ".$error_msg;
                    array_push($error_msg_array, $error_msg);
                }
            }
            else
            {
                $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                $error_msg=$this->lang->line("Disable Get Started")." : ".$error_msg;
                array_push($error_msg_array, $error_msg);
            }
        }
        //-----------------------------------------------------------------


        // Publishing persistent menu
        if($this->session->userdata('user_type') == 'Admin' || in_array(197,$this->module_access))
        {
            if($persistent_enabled=='1')
            {
                $json_array=array();
                $menu_data=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("page_id"=>$page_id,"user_id"=>$this->user_id)));
                foreach ($menu_data as $key => $value) 
                {
                    $temp=json_decode($value["item_json"],true);
                    $json_array["persistent_menu"][]=$temp;
                }            
                $json=json_encode($json_array);          
                $response=$this->messenger_bot_login->add_persistent_menu($page_access_token,$json);            
                if(!isset($response['error']))
                {                
                    $this->basic->update_data('messenger_bot_page_info',array("id"=>$page_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'1'));
                    $success_msg=$this->lang->line("Successful");
                    $success_msg=$this->lang->line("Persistent Menu Publish")." : ".$success_msg;
                    array_push($success_msg_array, $success_msg);
                }
                else
                {
                    $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $error_msg=$this->lang->line("Persistent Menu Publish")." : ".$error_msg;
                    array_push($error_msg_array, $error_msg);
                }
            }
            else
            {         
                $response=$this->messenger_bot_login->delete_persistent_menu($page_access_token);            
                if(!isset($response['error']))
                {                
                    $this->basic->update_data('messenger_bot_page_info',array("id"=>$page_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'0'));
                    $success_msg=$this->lang->line("Successful");
                    $success_msg=$this->lang->line("Persistent Menu Remove")." : ".$success_msg;
                    array_push($success_msg_array, $success_msg);
                }
                else
                {
                    $error_msg=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
                    $error_msg=$this->lang->line("Persistent Menu Remove")." : ".$error_msg;
                    array_push($error_msg_array, $error_msg);
                }
            }
        }
        //-----------------------------------------------------------------

        
        // enabling mark seen       
        if($this->basic->update_data('messenger_bot_page_info',array('id'=>$page_id,"user_id"=>$this->user_id),array('enable_mark_seen'=>$enable_mark_seen)))
        {
            if($enable_mark_seen=='1')
            {
                $success_msg=$this->lang->line("Successful");
                $success_msg=$this->lang->line("enable mark seen")." : ".$success_msg;
                array_push($success_msg_array, $success_msg);
            }
            else
            {
                $success_msg=$this->lang->line("Successful");
                $success_msg=$this->lang->line("disable mark seen")." : ".$success_msg;
                array_push($success_msg_array, $success_msg);
            }
        }
        else
        {
            $error_msg=$this->lang->line("something went wrong, please try again.");
            if($enable_mark_seen=='1') $error_msg=$this->lang->line("enable mark seen")." : ".$error_msg;
            else $error_msg=$this->lang->line("disable mark seen")." : ".$error_msg;
            array_push($error_msg_array, $error_msg);
        }        
        //-----------------------------------------------------------------
        


        // typing on settings
        if($this->basic->update_data('messenger_bot_page_info',array('id'=>$page_id,"user_id"=>$this->user_id),array('enbale_type_on'=>$enbale_type_on,'reply_delay_time'=>$reply_delay_time)))
        {
            $success_msg=$this->lang->line("Successful");
            $success_msg=$this->lang->line("Typing on Settings")." : ".$success_msg;
            array_push($success_msg_array, $success_msg);
        }
        else
        {
            $error_msg=$this->lang->line("something went wrong, please try again.");
            $error_msg=$this->lang->line("Typing on Settings")." : ".$error_msg;
            array_push($error_msg_array, $error_msg);
        }
        //-----------------------------------------------------------------
        

        echo "<div class='alert alert-success text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line("Template settings has been imported to database successfully.")."</div>";

        if(!empty($success_msg_array))
        {
            echo "<br><br>";
            echo "<div class='alert alert-success'><i class='fa fa-check-circle'></i> ".$this->lang->line("Related successful operations").":<br>";
            $i=0;
                echo '<div style="margin-top:10px;padding-left:10px;">';
                    foreach ($success_msg_array as $key => $value) 
                    {
                        $i++;
                        echo "<i class='fa fa-check-circle'></i> ".$value.'<br>';
                    }
                echo '</div>';
            echo "</div>";
        }

        if(!empty($error_msg_array))
        {
            echo "<br><br>";
            echo "<div class='alert alert-warning'><i class='fa fa-info-circle'></i> ".$this->lang->line("Related unsuccessful operations").":<br>";
            $i=0;
                echo '<div style="margin-top:10px;padding-left:10px;">';
                    foreach ($error_msg_array as $key => $value) 
                    {
                        $i++;
                        echo "<i class='fa fa-remove'></i> ".$value.'<br>';
                    }
                echo '</div>';
            echo "</div>";
        }

        
    }


    private function get_bot_settings($page_table_id=0)
    {
        $where['where'] = array('page_id'=> $page_table_id,"user_id"=>$this->user_id);
        /**Get BOT settings information from messenger_bot table as base table. **/
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where);
        $bot_settings=array();
        $i=0;
        foreach ($messenger_bot_info as $bot_info) 
        {
            $message_bot_id= $bot_info['id'];
            foreach ($bot_info as $key => $value) 
            {
                if($key=='id' || $key=='user_id' || $key=='page_id' || $key=='fb_page_id' || $key=='last_replied_at' || $key=='broadcaster_labels') continue;
                $bot_settings[$i]['message_bot'][$key]=$value;
            }

            /*** Get postback information from messenger_bot_postback table, it's from postback manager  ****/
            $where['where'] = array('messenger_bot_table_id'=> $message_bot_id,"template_id"=>"0");
            $messenger_postback_info = $this->basic->get_data("messenger_bot_postback",$where);

            $j=0;
            foreach ($messenger_postback_info as $postback_info) 
            {
                $message_postback_id= $postback_info['id'];
                foreach ($postback_info as $key1 => $value1) 
                {
                    if($key1=="template_id" || $key1=='id' || $key1=='user_id' || $key1=='page_id' || $key1=='messenger_bot_table_id' || $key1=='last_replied_at' || $key1=='broadcaster_labels') continue;
                    $bot_settings[$i]['message_bot']['postback_template_info'][$j][$key1]=$value1;
                }
                /** Get Child Postback from Post back Manager  whose BOT is already set.**/
                $where['where'] = array('template_id'=> $message_postback_id,);
                $messenger_postback_child_info = $this->basic->get_data("messenger_bot_postback",$where);
                $m=0;
                foreach ($messenger_postback_child_info as $postback_child_info) 
                {
                    foreach ($postback_child_info as $key2 => $value2) 
                    {
                        if($key2=="template_id" || $key2=='id' || $key2=='user_id' || $key2=='page_id' || $key2=='messenger_bot_table_id' || $key2=='last_replied_at' || $key2=='broadcaster_labels') continue;

                        $bot_settings[$i]['message_bot']['postback_template_info'][$j]["postback_child"][$m][$key2]=$value2;
                    }
                    $m++;
                }
                $j++;
            }
            $i++;
        }
        /*** Get empty Postback from messenger_bot_postback table. The child postback for those bot isn't set yet . ***/
        $where['where'] = array('template_id'=> '0','messenger_bot_table_id'=>'0','is_template'=>'0','page_id'=>$page_table_id);
        $messenger_emptypostback_info = $this->basic->get_data("messenger_bot_postback",$where);
        $empty_postback_settings=array();
        $x=0;
        foreach ($messenger_emptypostback_info as $emptypostback_child_info) 
        {
            foreach ($emptypostback_child_info as $key4 => $value4) 
            {
                if($key4=='id' || $key4=='user_id' || $key4=='page_id' || $key4=='messenger_bot_table_id' || $key4=='last_replied_at' || $key4=='broadcaster_labels') continue;
                $empty_postback_settings[$x][$key4]=$value4;
            }
            $x++;
        }
        /****   Get Information of Persistent Menu ***/
        $persistent_menu_settings=array();
        $where['where'] = array('page_id'=>$page_table_id);
        $persistent_menu_info = $this->basic->get_data("messenger_bot_persistent_menu",$where);
        $y=0;
        foreach ($persistent_menu_info as $persistent_menu) 
        {
            foreach ($persistent_menu as $key5 => $value5) 
            {
                $persistent_menu_settings[$y][$key5] = $value5;
            }
            $y++;
        }

        /***Get general information from messenger_bot_page_info table***/
        $bot_general_info=array();
        $where['where'] = array('id'=>$page_table_id);
        $bot_page_general_info = $this->basic->get_data("messenger_bot_page_info",$where);
        foreach ($bot_page_general_info as $general_info) 
        {
            $bot_general_info['welcome_message']= isset($general_info['welcome_message']) ? $general_info['welcome_message']:"";
            $bot_general_info['started_button_enabled']= isset($general_info['started_button_enabled']) ? $general_info['started_button_enabled']:"";
            $bot_general_info['persistent_enabled']= isset($general_info['persistent_enabled']) ? $general_info['persistent_enabled']:"";
            $bot_general_info['enable_mark_seen']= isset($general_info['enable_mark_seen']) ? $general_info['enable_mark_seen']:"";
            $bot_general_info['enbale_type_on']= isset($general_info['enbale_type_on']) ? $general_info['enbale_type_on']:"";
            $bot_general_info['reply_delay_time']= isset($general_info['reply_delay_time']) ? $general_info['reply_delay_time']:"";
        }


        $full_bot_settings=array();
        $full_bot_settings['bot_settings']=$bot_settings;
        $full_bot_settings['empty_postback_settings']=$empty_postback_settings;     
        $full_bot_settings['persistent_menu_settings']=$persistent_menu_settings;       
        $full_bot_settings['bot_general_info']=$bot_general_info;   

        return $full_bot_settings;
    }

  
    

    public function tree_view($page_id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) exit();
        if($page_id==0) exit();
        $page_table_id=$page_id;

    

        /***    Get Started Information    ***/
        $where=array();
        $where['where'] = array('page_id'=> $page_table_id,'keyword_type'=>"get-started");
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where,$select='',$join='',$limit='1');
        $this->postback_info=array();
        $get_started_data=$this->get_child_info($messenger_bot_info,$page_table_id);
        $get_started_data_copy=$get_started_data;

        $get_started_tree = $this->make_tree($get_started_data_copy,1);

         /***   No match tree    ***/
        $where=array();
        $where['where'] = array('page_id'=> $page_table_id,'keyword_type'=>"no match");
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where,$select='',$join='',$limit='1');
        $this->postback_info=array();
        $no_match_data=$this->get_child_info($messenger_bot_info,$page_table_id);
        $no_match_data_copy=$no_match_data;

        $no_match_tree = $this->make_tree($no_match_data_copy,2);


        /**Get BOT settings information from messenger_bot table as base table. **/
        $where=array();
        $where['where'] = array('page_id'=> $page_table_id,'keywords !=' => "");
        $messenger_bot_info = $this->basic->get_data("messenger_bot",$where);
        $this->postback_info=array();
        $keyword_data=$this->get_child_info($messenger_bot_info,$page_table_id);
        $keyword_data_copy=$keyword_data;

        $keyword_bot_tree=array();

        foreach ($keyword_data_copy as $key => $value) 
        {
            $bot_tree_optimize_array=array($key=>$value);
            // echo "<pre>";print_r($bot_tree_optimize_array); 
            $keyword_bot_tree[] = $this->make_tree($bot_tree_optimize_array,0);
        }


        $data['get_started_tree']=$get_started_tree;
        $data['keyword_bot_tree']=$keyword_bot_tree;
        $data['no_match_tree']=$no_match_tree;
        $data['body']='tree_view';
        $data['page_title']=$this->lang->line("Tree View");
        $this->_viewcontroller($data);
    }

   

    private function make_tree($get_started_data_copy,$is_get_started=1) // 0 = keyword, 1=get started, 2 = no match
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257,$this->module_access)) return "";
        $get_started_level=0;
        $postback_array=array();
        $parent_key='';
        $linear_postback_array=array(); // holds associative array of postback and it's content
        foreach ($get_started_data_copy as $key => $value) 
        {
            $parent_key=$key;
            $postback_array=isset($value['postback_info'])?$value['postback_info']:array();
            $keywrods_list=isset($value['keywrods_list'])?$value['keywrods_list']:array();
            $postback_array_temp=$postback_array;
            foreach ($postback_array as $key2 => $value2) 
            {
                if(!isset($linear_postback_array[$key2]))
                $linear_postback_array[$key2]=$value2;
            }
            $last_postback_info=array_pop($postback_array_temp);
            $get_started_level=isset($last_postback_info['level'])?$last_postback_info['level']:0; // maximum postback nest level
            break;
        }

        // $this->postback_array=$postback_array;

        // echo "<pre>";
        // var_export($this->postback_array);
        // echo "</pre>";

        // foreach ($this->postback_array as $key0 => $value)
        // {       
        //     $this->postback_done=array(); // stores completed postback ids for current tree
        //     array_push($this->postback_done, $value['postback_id']); // this postback will not be operated second time if current tree is a recursive tree
        //     if(isset($value['child_postback']) && is_array($value['child_postback']))
        //     {
        //         foreach ($value['child_postback'] as $key1 => $value1)
        //         {
        //             if(!is_array($value1) && !in_array($value1, $this->postback_done)) //level1 postback
        //             {
        //                 $this->postback_array[$key0]['child_postback'][$key1]=isset($this->postback_array[$value1])?$this->postback_array[$value1]:array();
        //             }
                    
        //             if($get_started_level>=2) // first level has been handles manually
        //             {
        //                 for($i=2; $i<$get_started_level;$i++) 
        //                 { 
        //                     $temp='key'.$i;
        //                     $$temp=''; // initializing keys to avaoid undefined warning
        //                     $phpcpmand=$this->set_nest($i); // setting multi-level nested postback replacing linear postback
        //                     eval($phpcpmand);
        //                 }
        //             }
                                
        //         }
        //     }
        // }
        

        // foreach ($this->postback_array as $key => $value)
        // {
        //     if($value['level']>1)
        //     unset($this->postback_array[$key]); // removing other  unnessary rows so that only nested postback stays 
        // }

        $this->postback_array=$this->set_nest_easy($postback_array,$get_started_level);

        // putting nested postback to main data
        if(isset($get_started_data_copy[$parent_key]['postback_info']))$get_started_data_copy[$parent_key]['postback_info']=$this->postback_array;
        if($is_get_started!='0')// keyword list is always empty for get started and no match
        if(isset($get_started_data_copy[$parent_key]['keywrods_list']))unset($get_started_data_copy[$parent_key]['keywrods_list']);

        if($is_get_started=='1')
        {
            if($parent_key=="") $getstarted_start='<i class="fa fa-remove red" style="font-size:15px;"></i><br> Get Started <br>Button'; 
            else $getstarted_start='<div class="getstartedcell"><a href="'.base_url('messenger_bot/edit_bot/'.$parent_key).'" target="_BLANK">Get Started <br>Button</a></div>';
        }
        else if($is_get_started=='2')
        {
            if($parent_key=="") $getstarted_start='<i class="fa fa-remove red" style="font-size:15px;"></i><br> No Match <br>Found'; 
            else $getstarted_start='<div class="getstartedcell"><a href="'.base_url('messenger_bot/edit_bot/'.$parent_key).'" target="_BLANK">No Match <br>Found</a></div>';
        }
        else
        {
            if($parent_key=="") $getstarted_start='<i class="fa fa-remove red" style="font-size:15px;"></i><br> No Keyword'; // no get started found
            else $getstarted_start='<div class="keywordcell" title="'.$keywrods_list.'"><a href="'.base_url('messenger_bot/edit_bot/'.$parent_key).'" target="_BLANK">'.$keywrods_list.'</a></div>';
        }


        $get_started_tree='
        <li>
            '.$getstarted_start.'
            <ul>';
                foreach ($get_started_data_copy as $key_temp => $value_temp) 
                {
                  foreach ($value_temp as $key_temp2 => $value_temp2) 
                  {
                    if($key_temp2=="keywrods_list") continue;
                    if($key_temp2!="postback_info")
                    {
                      $templabel=$this->formatlabel($this->tree_security($key_temp2));                      
                      if(is_array($value_temp2) && !empty($value_temp2))
                      {
                          if($key_temp2=="web_url") 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li>'.$templabel.'
                                    <br><a href="'.$this->tree_security($tempuval).'" target="_blank">'.$this->tree_security($tempuval).'</a>
                                </li>';
                            }
                          }
                          else if($key_temp2=="call_us") 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li>'.$templabel.'<br>'.$this->tree_security($tempuval).'</li>';
                            }
                          }
                          else 
                          {
                            foreach($value_temp2 as $tempukey => $tempuval) 
                            {                                
                                $get_started_tree.= '
                                <li>'.$templabel.'</li>';
                            }
                          }
                      }
                    }
                    else //postback sub-tree
                    {
                      $postback_info=array_filter($value_temp2);

                      if(count($postback_info)>0)                        
                        foreach ($postback_info as $key0 => $value0)
                        {       
                            if(is_array($value0)) // if have new child that does not appear in parent tree
                            {
                                $tempid=isset($value0['id'])?$value0['id']:0;
                                $tempis_template=isset($value0['is_template'])?$value0['is_template']:'';
                                $tempostbackid=isset($value0['postback_id'])?$this->tree_security($value0['postback_id']):'';
                                $tempbotname=isset($value0['bot_name'])?$this->tree_security($value0['bot_name']):'';

                                if($tempis_template=='1') $tempurl=base_url('messenger_bot/edit_template/'.$tempid); // it is template
                                else if($tempis_template=='0') $tempurl=base_url('messenger_bot/edit_bot/'.$tempid); // it is bot
                                else $tempurl="";
                                
                                if($tempbotname!='') $display=$tempbotname."<br><span style='color:#E05E00 !important'>".$tempostbackid.'</span>';
                                else $display=$tempostbackid;

                                if($tempurl!="") $templabel='<a title="'.$tempbotname.' ['.$tempostbackid.']" href="'.$tempurl.'" target="_blank">'.$display.'</a>';
                                else $templabel=$display;

                                $get_started_tree.= '
                                <li>'.$templabel;
                            }
                            else // child already appear in parent tree
                            {                                
                                if(isset($linear_postback_array[$value0]))
                                {
                                    $tempid=isset($linear_postback_array[$value0]['id'])?$linear_postback_array[$value0]['id']:0;
                                    $tempis_template=isset($linear_postback_array[$value0]['is_template'])?$linear_postback_array[$value0]['is_template']:'';
                                    $tempostbackid=isset($linear_postback_array[$value0]['postback_id'])?$this->tree_security($linear_postback_array[$value0]['postback_id']):'';
                                    $tempbotname=isset($linear_postback_array[$value0]['bot_name'])?$this->tree_security($linear_postback_array[$value0]['bot_name']):'';

                                    if($tempis_template=='1') $tempurl=base_url('messenger_bot/edit_template/'.$tempid); // it is template
                                    else if($tempis_template=='0') $tempurl=base_url('messenger_bot/edit_bot/'.$tempid); // it is bot
                                    else $tempurl="";

                                    if($tempbotname!='') $display="<span style='color:#ccc !important'>".$tempbotname."<br>".$tempostbackid.'</span>';
                                    else $display="<span style='color:#888 !important'>".$tempostbackid.'</span>';

                                    if($tempurl!="") $templabel='<a title="'.$tempbotname.' ['.$tempostbackid.']" href="'.$tempurl.'" target="_blank">'.$display.'</a>';
                                    else $templabel=$display;

                                    $get_started_tree.= '
                                    <li>'.$templabel;
                                }
                            }

                           $phpcomand_array=array();
                           $closing_bracket='';

                           for($i=1; $i<=$get_started_level;$i++) 
                            {    
                                $phpcomand_array[]=$this->get_nest($i);
                                $closing_bracket.="}  \$get_started_tree.='</ul>';";                                
                            }
                            $phpcomand_str=implode(' ', $phpcomand_array);
                            $phpcomand_str.=$closing_bracket;
                            eval($phpcomand_str);
                            
                            $get_started_tree.= 
                            "</li>";
                        }


                    } // end if postbock          
                  } // end 2nd foreach
                } // end 1st foreach
            $get_started_tree.='
            </ul>
        </li>';

        return $get_started_tree;

    }



    private function formatlabel($raw="")
    {
        if($raw=="") return "";  
        $tempraw=str_replace('_', ' ', $raw);
        $tempraw=ucwords($tempraw);
        return $tempraw;
    }

    private function tree_security($input="")
    {
        $output=strip_tags($input);
        $output=str_replace(array('<?php','<?','<? php','?>','<?=','$','(',')','{','}','[',']',"'",'"',"\\"), "", $input);
        return $output;
    }



    public function enable_disable_mark_seen()
    {
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $table_id=$this->input->post('table_id');
        $enable_disable=$this->input->post('enable_disable');
        $this->basic->update_data('messenger_bot_page_info',array('id'=>$table_id),array('enable_mark_seen'=>$enable_disable));
        echo "success";
    }

    public function typing_on_settings()
    {
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $table_id=$this->input->post('table_id');
        $reply_delay_time=$this->input->post('reply_delay_time');
        $enbale_type_on=$this->input->post('enbale_type_on');
        if($enbale_type_on=="0") $reply_delay_time=0;
        $this->basic->update_data('messenger_bot_page_info',array('id'=>$table_id,"user_id"=>$this->user_id),array('enbale_type_on'=>$enbale_type_on,'reply_delay_time'=>$reply_delay_time));
        $this->session->set_flashdata('bot_action',$this->lang->line("Settings has been saved successfully."));
    }

    public function chat_human_settings_postback_entry()
    {
       if(!$_POST) exit();

       $table_id=$this->input->post('table_id');
       $auto_id=$table_id;
       $get_pagedata=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$table_id)));
       if(!isset($get_pagedata[0])) 
       {
        echo json_encode(array('status'=>'0','message'=>$this->lang->line("Something went wrong while creating default post-back templates.")));
        exit();
       }
       $page_id=$get_pagedata[0]["page_id"];
       $user_id=$this->user_id;
        
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"YES_START_CHAT_WITH_HUMAN","page_id"=>$auto_id)))
       {
            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Thanks! It is a pleasure talking you. One of our team member will reply you soon. If you want to chat with me again, just click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_BOT","title":"Resume Chat with Bot"}]}}}}}\', "", "", "", "", "", "1", "CHAT WITH HUMAN", "YES_START_CHAT_WITH_HUMAN", "", "1");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for) VALUES
            ("'.$user_id.'","YES_START_CHAT_WITH_HUMAN","'.$auto_id.'","0","1","'.$insert_id.'","CHAT WITH HUMAN","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Thanks! It is a pleasure talking you. One of our team member will reply you soon. If you want to chat with me again, just click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_BOT","title":"Resume Chat with Bot"}]}}}}}\',"CHAT WITH HUMAN TEMPLATE","chat-with-human")';
            $this->db->query($sql);
       }

       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"YES_START_CHAT_WITH_BOT","page_id"=>$auto_id)))
       {
            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"I am gald to have you back. I will try my best to answer all questions. If you want to start chat with human again you can simply click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_HUMAN","title":"Chat with human"}]}}}}}\', "", "", "", "", "", "1", "CHAT WITH BOT", "YES_START_CHAT_WITH_BOT", "", "1");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for) VALUES
            ("'.$user_id.'","YES_START_CHAT_WITH_BOT","'.$auto_id.'","0","1","'.$insert_id.'","RESUBSCRIBE BOT","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"I am gald to have you back. I will try my best to answer all questions. If you want to start chat with human again you can simply click the button below.","buttons":[{"type":"postback","payload":"YES_START_CHAT_WITH_HUMAN","title":"Chat with human"}]}}}}}\',"CHAT WITH BOT TEMPLATE","chat-with-bot")';
            $this->db->query($sql);
       }

       $chat_human_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$table_id,"template_for"=>"chat-with-human","user_id"=>$this->user_id)));
       $chat_bot_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$table_id,"template_for"=>"chat-with-bot","user_id"=>$this->user_id)));
        
       $uid=isset($chat_human_info[0]["id"])?$chat_human_info[0]["id"]:"";
       if($uid=="") $uurl="";
       else $uurl=base_url('/messenger_bot/edit_template/').$uid;          
  
       $rid=isset($chat_bot_info[0]["id"])?$chat_bot_info[0]["id"]:"";
       if($rid=="") $rurl="";
       else $rurl=base_url('/messenger_bot/edit_template/').$rid;      

       $html='
       <a class="pull-left btn btn-default border_gray btn-xs" href="'.$uurl.'" target="_BLANK"><i class="fa fa-headset green"></i>'.$this->lang->line("Chat with Human Reply").'</a>            
        <a class="pull-right btn btn-default border_gray btn-xs" href="'.$rurl.'" target="_BLANK"><i class="fa fa-robot orange"></i>'.$this->lang->line("Chat with Robot Reply").'</a><div class="clearfix"><div><br><br>';

       echo json_encode(array('status'=>'1','message'=>$html));
    }

    public function chat_human_settings()
    {
        if(!$_POST) exit();

        // if($this->is_demo == '1')
        // {
        //     if($this->session->userdata('user_type') == "Admin")
        //     {
        //         echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
        //         exit();
        //     }
        // }

        $table_id=$this->input->post('table_id');
        $chat_human_email=$this->input->post('chat_human_email');

        $this->basic->update_data('messenger_bot_page_info',array('id'=>$table_id,"user_id"=>$this->user_id),array('chat_human_email'=>$chat_human_email));  
        $this->session->set_flashdata('bot_action',$this->lang->line("Settings has been saved successfully."));
    }

    public function broadcastser_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>16))) return true;
        if($this->session->userdata('user_type') == 'Member' && count(array_intersect($this->module_access, array(210,211,212))) > 0 ) return true;
        return false;
    }
     
    public function email_list_display()
    {
        if(empty($_POST['table_id'])) {
            die();
        }
        $table_id = $this->input->post('table_id');
        $page_info = $this->basic->get_data('messenger_bot_page_info',array('where'=>array('id'=>$table_id)),array('page_id'));        

        $broadcastser_exist = $this->broadcastser_exist();
        if($broadcastser_exist)
        {
            $join = array("messenger_bot_subscriber"=>"messenger_bot_quick_reply_email.fb_user_id=messenger_bot_subscriber.subscribe_id,left");
            $email_list_info = $this->basic->get_data('messenger_bot_quick_reply_email',['where'=>['messenger_bot_quick_reply_email.user_id'=>$this->user_id,'messenger_bot_quick_reply_email.fb_page_id'=>$page_info[0]['page_id']]],array("messenger_bot_quick_reply_email.*","messenger_bot_subscriber.contact_group_id as contact_group_id"),$join);
            $contact_list = array();
            $contact_group_info = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$table_id,"user_id"=>$this->user_id)));
            foreach($contact_group_info as $value)
            {
                $contact_list[$value['id']] = $value['group_name'];
            }
            
        }
        else
        $email_list_info = $this->basic->get_data('messenger_bot_quick_reply_email',['where'=>['messenger_bot_quick_reply_email.user_id'=>$this->user_id,'messenger_bot_quick_reply_email.fb_page_id'=>$page_info[0]['page_id']]]);

        $email_dlink=base_url("messenger_bot/email_list_download/".$table_id);
        $email_link=base_url("messenger_bot/edit_quick_email_reply/".$table_id.'/'.$page_info[0]['page_id']);
        $phone_link=base_url("messenger_bot/edit_quick_phone_reply/".$table_id.'/'.$page_info[0]['page_id']);
        $location_link=base_url("messenger_bot/edit_quick_location_reply/".$table_id.'/'.$page_info[0]['page_id']);
        $str='
         <div class="text-center" style="margin-top: -20px !important;">
            <a class="btn-sm btn btn-outline-info" target="BLANK" href="'.$email_dlink.'"><i class="fa fa-cloud-download"></i> '.$this->lang->line("Download email & phone list").'</a><br><br>
            <a class="btn-sm btn btn-outline-primary" target="_BLANK" href="'.$email_link.'"><i class="fa fa-envelope"></i> '.$this->lang->line("Set Email Subscription Reply").'</a>
            <a class="btn-sm btn btn-outline-primary" target="_BLANK" href="'.$phone_link.'"><i class="fa fa-phone"></i> '.$this->lang->line("Set Phone Subscription Reply").'</a>
            <a class="btn-sm btn btn-outline-primary" target="_BLANK" href="'.$location_link.'"><i class="fa fa-map"></i> '.$this->lang->line("Set Location Subscription Reply").'</a>
         </div><br>';

        if(!empty($email_list_info))
        {           
            $str.= '
                 <script>
                    $j(document).ready(function() {
                        $(".table-responsive").mCustomScrollbar({
                            autoHideScrollbar:true,
                            theme:"3d-dark",          
                            axis: "x"
                        });
                        $("#email_list_table").DataTable();
                    }); 
                 </script>
                 <div class="table-responsive">
                 <table id="email_list_table">
                     <thead>
                         <tr>
                             <th>'.$this->lang->line("First Name").'</th>
                             <th>'.$this->lang->line("Last Name").'</th>
                             <th>'.$this->lang->line("Email").'</th>
                             <th>'.$this->lang->line("Phone Number").'</th>';
                 if($broadcastser_exist)
                     $str .= '<th>'.$this->lang->line("Label").'</th>';
                     $str .= '<th>'.$this->lang->line("Location (latitude,longitude)").'</th>
                             <th>'.$this->lang->line("Email Upate").'</th>
                             <th>'.$this->lang->line("Phone Number Update").'</th>
                         </tr>
                     </thead>
                     <tbody>';
                         
                     
            foreach($email_list_info as $value)
            {
                $email_update_time=($value['last_update_time']!="0000-00-00 00:00:00")?date("Y-m-d H:i",strtotime($value['last_update_time'])):"0000-00-00 00:00";
                $phone_number_update_time=($value['phone_number_last_update']!="0000-00-00 00:00:00")?date("Y-m-d H:i",strtotime($value['phone_number_last_update'])):"0000-00-00 00:00";                
                
                $str .= '<tr>
                            <td>'.$value['fb_user_first_name'].'</td>
                            <td>'.$value['fb_user_last_name'].'</td>
                            <td>'.$value['email'].'</td>
                            <td>'.$value['phone_number'].'</td>';
                    if($broadcastser_exist)
                    {
                        $label_names_array = explode(',', $value['contact_group_id']);
                        $label_name_string = "";
                        $label_names_array=array_filter($label_names_array);
                        foreach ($label_names_array as $key) {
                            if(isset($contact_list[$key]))
                            $label_name_string .= $contact_list[$key].", ";
                        }
                        $label_name_string = trim($label_name_string,", ");
                        $str .= '<td>'.$label_name_string.'</td>';
                        
                    }
                    $str .= '<td><a href="'.$value['location_map_url'].'" target="_BLANK">'.$value['user_location'].'</a></td>
                            <td>'.$email_update_time.'</td>
                            <td>'.$phone_number_update_time.'</td>
                        </tr>';
            }
            $str .= '</tbody>
                 </table></div>';
        }
        else
        {
            $str.= "<div class='alert alert-danger text-center'>{$this->lang->line("No data to show")}</div>";
        }
        echo $str;
    }
    public function email_list_download($table_id)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        
        $page_info = $this->basic->get_data('messenger_bot_page_info',array('where'=>array('id'=>$table_id,'user_id'=>$this->user_id)),array('page_id','page_name'));
        $broadcastser_exist = $this->broadcastser_exist();

        if($broadcastser_exist)
        {
            $join = array("messenger_bot_subscriber"=>"messenger_bot_quick_reply_email.fb_user_id=messenger_bot_subscriber.subscribe_id,left");
            $email_list_info = $this->basic->get_data('messenger_bot_quick_reply_email',['where'=>['messenger_bot_quick_reply_email.user_id'=>$this->user_id,'messenger_bot_quick_reply_email.fb_page_id'=>$page_info[0]['page_id']]],array("messenger_bot_quick_reply_email.*","messenger_bot_subscriber.contact_group_id as contact_group_id"),$join);            
        }
        else
        $email_list_info = $this->basic->get_data('messenger_bot_quick_reply_email',['where'=>['messenger_bot_quick_reply_email.user_id'=>$this->user_id,'messenger_bot_quick_reply_email.fb_page_id'=>$page_info[0]['page_id']]]);

        if(empty($email_list_info))
        {
            $str = "<div class='alert alert-danger text-center'>".$this->lang->line("No data to download")."</div>";
        }
        else
        {
            if($broadcastser_exist)
            {
                $contact_list = array();
                $contact_group_info = $this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("page_id"=>$table_id,"user_id"=>$this->user_id)));
                foreach($contact_group_info as $value)
                {
                    $contact_list[$value['id']] = $value['group_name'];
                }
                
            }

            $filename="download/email_download_{$this->user_id}.csv";
            $f = fopen('php://memory', 'w'); 
            fputs( $f, "\xEF\xBB\xBF" );
            // fprintf($filename, chr(0xEF).chr(0xBB).chr(0xBF));
            /**Write header in csv file***/
            $write_data[]="First Name";
            $write_data[]="Last Name";
            $write_data[]="Email";
            $write_data[]="Phone Number";
            if($broadcastser_exist)
                $write_data[]="Label";
            $write_data[]="Page ID";
            $write_data[]="Paage Name";
            fputcsv($f,$write_data, ",");

            foreach($email_list_info as $value)
            {
                $write_data=array();
                $write_data[]=$value['fb_user_first_name'];
                $write_data[]=$value['fb_user_last_name'];
                $write_data[]=$value['email'];
                $write_data[]=$value['phone_number'];
                if($broadcastser_exist)
                {
                    $label_names_array = explode(',', $value['contact_group_id']);
                    $label_name_string = "";
                    $label_names_array=array_filter($label_names_array);
                    foreach ($label_names_array as $key) {
                        $label_name_string .= $contact_list[$key].", ";
                    }
                    $label_name_string = trim($label_name_string,", ");
                    $write_data[]=$label_name_string;
                    
                }
                $write_data[]=$page_info[0]['page_id'];
                $write_data[]=$page_info[0]['page_name'];
                fputcsv($f,$write_data, ",");
            }
            // reset the file pointer to the start of the file
            fseek($f, 0);
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename="'.$filename.'";');
            // make php send the generated csv lines to the browser
            fpassthru($f);    
        }       
    }

    //=============================ENABLE DISBALE BOT==============================
    
    public function check_page_response()
    {
        $page_id=$this->input->post('page_id');
        
        $where = array('where'=>array('messenger_bot_page_info.id'=>$page_id));
        $join = array(
            'messenger_bot_user_info' => "messenger_bot_page_info.messenger_bot_user_info_id=messenger_bot_user_info.id,left",
            'messenger_bot_config' => "messenger_bot_user_info.messenger_bot_config_id=messenger_bot_config.id,left"
        );
        $select = array("messenger_bot_config.api_id","messenger_bot_page_info.page_id");
        $info = $this->basic->get_data('messenger_bot_page_info',$where,$select,$join);


        $fb_page_id=isset($info[0]["page_id"]) ? $info[0]["page_id"] : "";
        $api_id=isset($info[0]["api_id"]) ? $info[0]["api_id"] : "";

        if($this->db->table_exists('page_response_page_info'))
        {         
            $where = array(
                'where'=>array(
                    'page_response_page_info.page_id'=>$fb_page_id,
                    'page_response_config.api_id'=>$api_id,
                    'page_response_page_info.bot_enabled'=>'1'
                )
            );
            $join = array(
                'page_response_user_info' => "page_response_page_info.page_response_user_info_id=page_response_user_info.id,left",
                'page_response_config' => "page_response_user_info.page_response_config_id=page_response_config.id,left"
            );
            $select = array("page_response_page_info.id");
            $pageresponse_info = $this->basic->get_data('page_response_page_info',$where,$select,$join);


            if(!empty($pageresponse_info))
            {
                $response = array('has_pageresponse'=>'1');
                echo json_encode($response);
            }
            else
            {
                $response = array('has_pageresponse'=>'0');
                echo json_encode($response);
            }
        }
        else
        {
            $response = array('has_pageresponse'=>'0');
            echo json_encode($response);
        }
    }

    public function enable_disable_bot()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        exit();
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $user_id = $this->user_id;
        $page_id=$this->input->post('page_id');
        $restart=$this->input->post('restart');
        $enable_disable=$this->input->post('enable_disable');
        $this->load->library("messenger_bot_login");
        $page_data=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_id)));
        $fb_page_id=isset($page_data[0]["page_id"]) ? $page_data[0]["page_id"] : "";
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
        $persistent_enabled=isset($page_data[0]["persistent_enabled"]) ? $page_data[0]["persistent_enabled"] : "0";
        $fb_user_id = $page_data[0]["messenger_bot_user_info_id"];
        $fb_user_info = $this->basic->get_data('messenger_bot_user_info',array('where'=>array('id'=>$fb_user_id)));
        $this->messenger_bot_login->app_initialize($fb_user_info[0]['messenger_bot_config_id']); 
        if($enable_disable=='enable')
        {
            $already_enabled = $this->basic->get_data('messenger_bot_page_info',array('where'=>array('page_id'=>$fb_page_id,'bot_enabled !='=>'0')));
            if(!empty($already_enabled))
            {                
                if($already_enabled[0]['user_id'] != $this->user_id || $already_enabled[0]['messenger_bot_user_info_id'] != $fb_user_id )
                {
                    echo json_encode(array('success'=>0,'error'=>$this->lang->line("This page is already enabled by other Admin.")));
                    exit();
                }
            }
            //************************************************//
            if($restart != '1')
            {                
                $status=$this->_check_usage($module_id=200,$request=1);
                if($status=="2") 
                {
                    echo json_encode(array('success'=>0,'error'=>$this->lang->line("Module limit is over.")));
                    exit();
                }
                else if($status=="3") 
                {
                    echo json_encode(array('success'=>0,'error'=>$this->lang->line("Module limit is over.")));
                    exit();
                }
            }
            //************************************************//

            $response=$this->messenger_bot_login->enable_bot($fb_page_id,$page_access_token);
            $output = $response;
            if($output['error'] == '')
            {
                $this->basic->update_data("messenger_bot_page_info",array("id"=>$page_id),array("bot_enabled"=>"1"));
                if($restart != '1')                    
                    $this->_insert_usage_log($module_id=200,$request=1);                
            } 
            echo json_encode($response);
        } 
        else
        {
            $updateData=array("bot_enabled"=>"2");
            if($persistent_enabled=='1') 
            {
                $updateData['persistent_enabled']='0';
                $updateData['started_button_enabled']='0';
                $this->messenger_bot_login->delete_persistent_menu($page_access_token); // delete persistent menu
                $this->messenger_bot_login->delete_get_started_button($page_access_token); // delete get started button
                $this->basic->delete_data("messenger_bot_persistent_menu",array("page_id"=>$page_id,"user_id"=>$this->user_id));
                $this->_delete_usage_log($module_id=197,$request=1);
            }
            $response=$this->messenger_bot_login->disable_bot($fb_page_id,$page_access_token);
            $output = $response;
            if($output['error'] == '')
            {
                $this->basic->update_data("messenger_bot_page_info",array("id"=>$page_id),$updateData);

                if($this->db->table_exists('page_response_page_info'))
                {         
                    $app_info = $this->basic->get_data('messenger_bot_config',array('where'=>array('id'=>$fb_user_info[0]['messenger_bot_config_id'])));
                    $where = array(
                        'where'=>array(
                            'page_response_page_info.page_id'=>$fb_page_id,
                            'page_response_config.api_id'=>$app_info[0]['api_id'],
                            'page_response_page_info.bot_enabled'=>'1'
                        )
                    );
                    $join = array(
                        'page_response_user_info' => "page_response_page_info.page_response_user_info_id=page_response_user_info.id,left",
                        'page_response_config' => "page_response_user_info.page_response_config_id=page_response_config.id,left"
                    );
                    $select = array("page_response_page_info.id");
                    $pageresponse_info = $this->basic->get_data('page_response_page_info',$where,$select,$join);
                    if(!empty($pageresponse_info))
                        $this->basic->update_data('page_response_page_info',array('id'=>$pageresponse_info[0]['id']),array('bot_enabled'=>'0'));
                }

                // $this->_delete_usage_log($module_id=200,$request=1);
            }
            echo json_encode($response);
        } 
    }
    //=============================ENABLE DISBALE BOT==============================

    public function delete_full_bot()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        exit();
        if(!$_POST) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $user_id = $this->user_id;
        $page_id=$this->input->post('page_id');
        $already_disabled=$this->input->post('already_disabled');

        $this->load->library("messenger_bot_login");         

        $page_data=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_id)));
        $fb_page_id=isset($page_data[0]["page_id"]) ? $page_data[0]["page_id"] : "";
        $page_access_token=isset($page_data[0]["page_access_token"]) ? $page_data[0]["page_access_token"] : "";
        $persistent_enabled=isset($page_data[0]["persistent_enabled"]) ? $page_data[0]["persistent_enabled"] : "0";
        $fb_user_id = $page_data[0]["messenger_bot_user_info_id"];
        $fb_user_info = $this->basic->get_data('messenger_bot_user_info',array('where'=>array('id'=>$fb_user_id)));
        $this->messenger_bot_login->app_initialize($fb_user_info[0]['messenger_bot_config_id']);

        $updateData=array("bot_enabled"=>"0");
        if($already_disabled == 'no')
        {            
            if($persistent_enabled=='1') 
            {
                $updateData['persistent_enabled']='0';
                $updateData['started_button_enabled']='0';
                $this->messenger_bot_login->delete_persistent_menu($page_access_token); // delete persistent menu
                $this->messenger_bot_login->delete_get_started_button($page_access_token); // delete get started button
                $this->basic->delete_data("messenger_bot_persistent_menu",array("page_id"=>$page_id,"user_id"=>$this->user_id));                
            }
            $response=$this->messenger_bot_login->disable_bot($fb_page_id,$page_access_token);
        }
        $this->basic->update_data("messenger_bot_page_info",array("id"=>$page_id),$updateData);
        $this->_delete_usage_log($module_id=200,$request=1);

        $this->delete_bot_data($page_id,$fb_page_id);

        if($this->db->table_exists('page_response_page_info'))
        {         
            $app_info = $this->basic->get_data('messenger_bot_config',array('where'=>array('id'=>$fb_user_info[0]['messenger_bot_config_id'])));
            $where = array(
                'where'=>array(
                    'page_response_page_info.page_id'=>$fb_page_id,
                    'page_response_config.api_id'=>$app_info[0]['api_id'],
                    'page_response_page_info.bot_enabled'=>'1'
                )
            );
            $join = array(
                'page_response_user_info' => "page_response_page_info.page_response_user_info_id=page_response_user_info.id,left",
                'page_response_config' => "page_response_user_info.page_response_config_id=page_response_config.id,left"
            );
            $select = array("page_response_page_info.id");
            $pageresponse_info = $this->basic->get_data('page_response_page_info',$where,$select,$join);
            if(!empty($pageresponse_info))
                $this->basic->update_data('page_response_page_info',array('id'=>$pageresponse_info[0]['id']),array('bot_enabled'=>'0'));
        }

        echo json_encode(array('success'=>'successfully deleted.'));

    }


    private function delete_bot_data($page_id,$fb_page_id)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if($this->db->table_exists('messenger_bot_engagement_checkbox'))
        {            
            $get_checkbox=$this->basic->get_data("messenger_bot_engagement_checkbox",array("where"=>array("page_id"=>$page_id)));
            $checkbox_ids=array();
            foreach ($get_checkbox as $key => $value) 
            {
                $checkbox_ids[]=$value['id'];
            }

            $this->basic->delete_data("messenger_bot_engagement_checkbox",array("page_id"=>$page_id));
        
            if(!empty($checkbox_ids))
            {
                $this->db->where_in('checkbox_plugin_id', $checkbox_ids);
                $this->db->delete('messenger_bot_engagement_checkbox_reply');
            }
        }

        $del_list=array (
          0 => 
          array 
          (
            'table_name' => 'messenger_bot',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          1 => 
          array (
            'table_name' => 'messenger_bot_persistent_menu',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          2 => 
          array (
            'table_name' => 'messenger_bot_postback',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          3 => 
          array (
            'table_name' => 'messenger_bot_quick_reply_email',
            'where_field' => 'fb_page_id',
            'value' => $fb_page_id,
          ),
          4 => 
          array (
            'table_name' => 'messenger_bot_reply_error_log',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          5 => 
          array (
            'table_name' => 'messenger_bot_subscriber',
            'where_field' => 'page_id',
            'value' =>$fb_page_id,
          ),
          7 => 
          array (
            'table_name' => 'fb_chat_plugin_2way',
            'where_field' => 'page_auto_id',
            'value' =>$page_id,
            'where_field2' => 'core_or_bot',
            'value2' =>'0',
          ),
          8 => 
          array (
            'table_name' => 'messenger_bot_domain_whitelist',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          9 => 
          array (
            'table_name' => 'messenger_bot_engagement_2way_chat_plugin',
            'where_field' => 'page_auto_id',
            'value' =>$page_id,
          ),
          10 => 
          array (
            'table_name' => 'messenger_bot_engagement_messenger_codes',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          11 => 
          array (
            'table_name' => 'messenger_bot_engagement_mme',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          12 => 
          array (
            'table_name' => 'messenger_bot_engagement_send_to_msg',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          13 => 
          array (
            'table_name' => 'messenger_bot_drip_campaign',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          14 => 
          array (
            'table_name' => 'messenger_bot_drip_report',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          15 => 
          array (
            'table_name' => 'messenger_bot_broadcast',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          16 => 
          array (
            'table_name' => 'messenger_bot_broadcast_contact_group',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          17 => 
          array (
            'table_name' => 'messenger_bot_broadcast_serial',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
          18 => 
          array (
            'table_name' => 'messenger_bot_broadcast_serial_send',
            'where_field' => 'page_id',
            'value' =>$page_id,
          ),
        );

        foreach ($del_list as $key => $value) 
        {
            if($this->db->table_exists($value['table_name']))
            {
                $where=array($value['where_field']=>$value['value']);
                if(isset($value['where_field2'])) $where[$value['where_field2']]=$value['value2'];
                $this->basic->delete_data($value['table_name'],$where);
            }
        }

        return true;
    } 

   //=============================DOMAIN WHITELIST================================
    public function domain_whitelist()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 
        $table = "messenger_bot_page_info";     
        $where_simple['messenger_bot_page_info.user_id'] = $this->user_id;
        $where_simple['messenger_bot_page_info.bot_enabled'] = '1';
        $where  = array('where'=>$where_simple);
        $join = array('messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left");   
        $page_info = $this->basic->get_data($table, $where, $select=array("messenger_bot_page_info.*","messenger_bot_user_info.name as account_name"),$join,'','','page_name asc');
        $pagelist=array();
        $i=0;
        foreach($page_info as $key => $value) 
        {
           // $pagelist[$value["id"]]["account_name"]=$value['account_name'];
           // $pagelist[$value["id"]]["page_name"]=$value['page_name'];
           $pagelist[$value["messenger_bot_user_info_id"]]["account_name"]=$value['account_name'];
           $pagelist[$value["messenger_bot_user_info_id"]]["page_data"][$i]["page_name"]=$value['page_name'];
           $pagelist[$value["messenger_bot_user_info_id"]]["page_data"][$i]["page_id"]=$value['id'];
           $i++;
        }
        $data['page_title'] = $this->lang->line("Whitelisted Domains");
        $data['pagelist'] = $pagelist;
        $data['body'] = 'domain_list';
        $this->_viewcontroller($data);
    }

    public function domain_whitelist_data()
    {
        // setting variables for pagination
        $page = isset($_POST['page']) ? intval($_POST['page']) : 100;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'page_name';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'ASC';
        $order_by_str=$sort." ".$order;

        // setting properties for search
        $search_domain = trim($this->input->post('search_domain', true));
        $search_page = $this->input->post('search_page', true);
        $is_searched = $this->input->post('is_searched', true);

        if ($is_searched) 
        {
            $this->session->set_userdata('messenger_bot_whitelist_domain',$search_domain);
            $this->session->set_userdata('messenger_bot_whitelist_page',$search_page);
        }
        $search_domain        = $this->session->userdata('messenger_bot_whitelist_domain');
        $search_pasearch_domainge       = $this->session->userdata('messenger_bot_whitelist_page');
        $where_simple=array();
        if ($search_domain!="") 
        {
            $where_simple['domain like '] = "%".$search_domain."%";
        }  
        if ($search_page!="") 
        {
            $where_simple['page_name like '] = "%".$search_page."%";
        }  
   
        $where_simple['messenger_bot_domain_whitelist.user_id'] = $this->user_id;
        $where_simple['messenger_bot_page_info.user_id'] = $this->user_id;
        $where_simple['messenger_bot_page_info.deleted'] = '0';
        $where_simple['messenger_bot_page_info.bot_enabled'] = '1';
        $where  = array('where'=>$where_simple);
        $offset = ($page-1)*$rows;
        $result = array();       
        $table = "messenger_bot_domain_whitelist";     
        $join = array
        (
            'messenger_bot_page_info'=>"messenger_bot_page_info.id=messenger_bot_domain_whitelist.page_id,left",
            'messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left"
        );   
        $group_by = "messenger_bot_domain_whitelist.page_id";
        $info = $this->basic->get_data($table, $where, $select=array("messenger_bot_domain_whitelist.*","messenger_bot_page_info.page_name","messenger_bot_page_info.page_id as fb_page_id", "messenger_bot_user_info.name as account_name","count(messenger_bot_domain_whitelist.id) as count"), $join, $limit=$rows, $start=$offset, $order_by=$order_by_str,$group_by);
        // echo $this->db->last_query();
        $total_rows_array = $this->basic->count_row($table, $where, $count="messenger_bot_domain_whitelist.id",$join,$group_by);      
        $total_result = $total_rows_array[0]['total_rows'];
        echo convert_to_grid_data($info, $total_result);
    }
    public function domain_details()
    {
        if (empty($_POST['page_id'])) 
        {
            die();
        }
        $page_id = $this->input->post("page_id");
        $fb_page_id = $this->input->post("page_id");
        $page_name = $this->input->post("page_name");
        $account_name = $this->input->post("account_name");
        $table_name = "messenger_bot_domain_whitelist";
        $where['where'] = array('user_id' => $this->user_id, 'page_id' => $page_id);
        $domain_data = $this->basic->get_data($table_name,$where);
        $html = '<script>
                    $j(document).ready(function() {
                        $("#domain_data_table").DataTable();
                    }); 
                 </script>';
        $html .= "<h4 class='text-center' style='margin-top:0;'>".$this->lang->line('page')." : ".$page_name." (".$account_name.")</h4>
            <table id='domain_data_table' class='table table-striped table-bordered nowrap' cellspacing='0' width='100%''>
            <thead>
                <tr>
                    <th>".$this->lang->line("domain")."</th>
                    <th class='text-center'>".$this->lang->line("whitlisted at")."</th>
                    <th class='text-center'>".$this->lang->line("delete")."</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($domain_data as $one_user) 
        {
            $btn_id=$one_user['id'];
            $delete_btn= "<a class='btn btn-outline-danger delete_domain'title='".$this->lang->line("delete")."' id='domain-".$btn_id."' data-id='".$btn_id."'><i class='fa fa-trash'></i></a>";
            $html .= "<tr>
                        <td><a target='_BLANK' href='".$one_user['domain']."'>".$one_user['domain']."</a></td>
                        <td class='text-center'>".date("jS M, y H:i:s",strtotime($one_user['created_at']))."</td>";          
            $html .= "
            <td class='text-center'>".$delete_btn."</td>
            </tr>";
        }
        
        $html .= "</tbody>
                </table>";
        
        echo $html;
    }
    public function delete_domain()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        
        if(!$_POST['domain_id']) exit();
        $domain_id=$this->input->post('domain_id');
        if($this->basic->delete_data('messenger_bot_domain_whitelist',array('id'=>$domain_id,'user_id'=>$this->user_id))) echo "1";
        else echo "0";
    }
    public function delete_bot()
    {
        if(!$_POST) exit();
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        $id=$this->input->post("id");
        $bot_posback_ids = $this->basic->get_data('messenger_bot',array('where'=>array('id'=>$id)));
        $postback_id = array();
        if($bot_posback_ids[0]['keyword_type'] == 'post-back')
        {
            $postback_id = explode(',', $bot_posback_ids[0]['postback_id']);
        }

        $this->db->trans_start();
        $this->basic->delete_data("messenger_bot",array("id"=>$id,"user_id"=>$this->user_id));
        
        if(!empty($postback_id))
        {            
            $this->db->where_in("postback_id", $postback_id);
            $this->db->update('messenger_bot_postback', array('use_status' => '0'));
        }      
        $this->db->trans_complete();
        if($this->db->trans_status() === false)
            echo '0';
        else
            echo '1';
    }
    public function add_domain()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        exit();        
        if(!$_POST['page_id']) exit();

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        $page_id=$this->input->post('page_id');
        $domain_name=$this->input->post('domain_name');
        $userdata=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_id)));
        $messenger_bot_user_info_id=isset($userdata[0]['messenger_bot_user_info_id']) ? $userdata[0]['messenger_bot_user_info_id'] : "";
        $page_access_token=isset($userdata[0]['page_access_token']) ? $userdata[0]['page_access_token'] : "";
        if(!$this->basic->is_exist('messenger_bot_domain_whitelist',array('page_id'=>$page_id,'domain'=>$domain_name)))
        {
            $this->basic->insert_data('messenger_bot_domain_whitelist',array('page_id'=>$page_id,'domain'=>$domain_name,"created_at"=>date("Y-m-d H:i:s"),"messenger_bot_user_info_id"=>$messenger_bot_user_info_id,"user_id"=>$this->user_id));
            $this->load->library("messenger_bot_login"); 
            $response=array();
            $response=$this->messenger_bot_login->domain_whitelist($page_access_token,$domain_name);
        }
        else $response=array('status'=>'1','result'=>$this->lang->line("Successfully updated whitelisted domains"));
        echo json_encode($response);
       
    }
   //=============================DOMAIN WHITELIST================================


    //==============================ACCOUNT IMPORT================================
    public function account_import()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(199,$this->module_access))
        redirect('home/login_page', 'location'); 
        if($this->session->userdata("messenger_bot_user_info")==0 && $this->config->item("bot_backup_mode")==1)
        redirect('messenger_bot/facebook_config','refresh');
        $this->load->library("messenger_bot_login");  
        $data['body'] = 'account_import';
        $data['page_title'] = $this->lang->line('Facebook Account Import');
        $redirect_url = base_url()."messenger_bot/refresh_login_callback";
        $fb_login_button = $this->messenger_bot_login->login_for_user_access_token($redirect_url);
        $data['fb_login_button'] = $fb_login_button;
        $where['where'] = array('user_id'=>$this->user_id);
        $existing_accounts = $this->basic->get_data('messenger_bot_user_info',$where);
        $show_import_account_box = 1;
        $data['show_import_account_box'] = 1;
        if(!empty($existing_accounts))
        {
            $i=0;
            foreach($existing_accounts as $value)
            {
                $existing_account_info[$i]['need_to_delete'] = $value['need_to_delete'];
                if($value['need_to_delete'] == '1')
                {
                   $show_import_account_box = 0; 
                   $data['show_import_account_box'] = $show_import_account_box;
                }
                $existing_account_info[$i]['fb_id'] = $value['fb_id'];
                $existing_account_info[$i]['userinfo_table_id'] = $value['id'];
                $existing_account_info[$i]['name'] = $value['name'];
                $existing_account_info[$i]['email'] = $value['email'];
                $existing_account_info[$i]['user_access_token'] = $value['access_token'];
                $valid_or_invalid = $this->messenger_bot_login->access_token_validity_check_for_user($value['access_token']);
                if($valid_or_invalid)
                {
                    $existing_account_info[$i]['validity'] = 'yes';
                }
                else
                {
                    $existing_account_info[$i]['validity'] = 'no';
                }

                $where = array();
                $where['where'] = array('messenger_bot_user_info_id'=>$value['id']);
                $page_count = $this->basic->get_data('messenger_bot_page_info',$where);
                $existing_account_info[$i]['page_list'] = $page_count;
                if(!empty($page_count))
                {
                    $existing_account_info[$i]['total_pages'] = count($page_count);                    
                }
                else $existing_account_info[$i]['total_pages'] = 0;
                $i++;
            }
            $data['existing_accounts'] = $existing_account_info;
        }
        else $data['existing_accounts'] = '0';

        $this->_viewcontroller($data);
    }

    public function ajax_delete_account_action()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(199,$this->module_access))
        exit();
        if($this->session->userdata("messenger_bot_user_info")==0 && $this->config->item("bot_backup_mode")==1)
        exit();
        $table_id = $this->input->post("user_table_id");

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin" && $table_id=='1')
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> You can't delete anything from admin account!!</div>";
                exit();
            }
        }

        $this->db->trans_start();
        $this->basic->delete_data('messenger_bot_user_info',array('id'=>$table_id,"user_id"=>$this->user_id));  
        $this->_delete_usage_log($module_id=199,$request=1);  // messenger account import module
        $bot_page_list=$this->basic->get_data("messenger_bot_page_info",array("where"=>array('messenger_bot_user_info_id'=>$table_id))); // all pages of that account
        $page_id_array=array();
        $menu_page_id_array=array();
        $no_enabled_pages=0;
        $no_menu_enabled_pages=0;
        $this->load->library("messenger_bot_login");
        foreach($bot_page_list as $value)
        {
            array_push($page_id_array, $value['id']);
            if($value['bot_enabled']=='1') 
            {
                $no_enabled_pages++;
                $fb_page_id=isset($value['page_id']) ? $value['page_id'] : "";
                $page_access_token=isset($value['page_access_token']) ? $value['page_access_token'] : "";
                if($value['persistent_enabled']=='1') 
                {
                    $no_menu_enabled_pages++;
                    array_push($menu_page_id_array, $value['id']);
                    $this->messenger_bot_login->delete_persistent_menu($page_access_token); // delete persistent menu
                    $this->messenger_bot_login->delete_get_started_button($page_access_token); // delete get started button
                }
                $this->messenger_bot_login->disable_bot($fb_page_id,$page_access_token);               
            }          
        }
        if(!empty($page_id_array))
        {
            $this->db->where_in('page_id', $page_id_array);
            $this->db->delete("messenger_bot"); //delete all bot settings of pages of deleted account
            $this->db->where_in('page_id', $page_id_array);
            $this->db->delete("messenger_bot_postback"); //delete all bot payload/postback settings of pages of deleted account
        }
        if(!empty($menu_page_id_array))
        {
            $this->db->where_in('page_id', $menu_page_id_array);
            $this->db->delete("messenger_bot_persistent_menu"); //delete all persistent menu of pages of deleted account
        }
        if($no_enabled_pages>0)
        $this->_delete_usage_log($module_id=200,$request=$no_enabled_pages);  // messenger bot module
        if($no_menu_enabled_pages>0)
        $this->_delete_usage_log($module_id=197,$request=$no_menu_enabled_pages);  // persistent menu module
        $this->basic->delete_data('messenger_bot_page_info',array('messenger_bot_user_info_id'=>$table_id,"user_id"=>$this->user_id)); // delete all page of that account
        $this->basic->delete_data('messenger_bot_domain_whitelist',array('messenger_bot_user_info_id'=>$table_id,"user_id"=>$this->user_id)); // delete all whitlisted domain of that account
        
        if(!empty($page_id_array))
        {
            $this->db->where_in('page_id', $page_id_array);
            $this->db->delete("messenger_bot_postback"); //delete all bot payload/postback settings of pages of deleted account            
        }
        
        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
            echo "<div class='alert alert-danger text-center'>'".$this->lang->line("something went wrong, please try again.")."'</div>";
        }
        else
        {
            echo "success";
        }
    }


    public function ajax_delete_page_action()
    {
        $table_id = $this->input->post('page_table_id',true);
        
        $this->db->trans_start();
        $bot_page_list=$this->basic->get_data("messenger_bot_page_info",array("where"=>array('id'=>$table_id))); // all pages of that account
        $page_id_array=array();
        $menu_page_id_array=array();
        $no_enabled_pages=0;
        $no_menu_enabled_pages=0;
        $this->load->library("messenger_bot_login");
        foreach($bot_page_list as $value)
        {
            array_push($page_id_array, $value['id']);
            if($value['bot_enabled']=='1') 
            {
                $no_enabled_pages++;
                $fb_page_id=isset($value['page_id']) ? $value['page_id'] : "";
                $page_access_token=isset($value['page_access_token']) ? $value['page_access_token'] : "";
                if($value['persistent_enabled']=='1') 
                {
                    $no_menu_enabled_pages++;
                    array_push($menu_page_id_array, $value['id']);
                    $this->messenger_bot_login->delete_persistent_menu($page_access_token); // delete persistent menu
                    $this->messenger_bot_login->delete_get_started_button($page_access_token); // delete get started button
                }
                $this->messenger_bot_login->disable_bot($fb_page_id,$page_access_token);               
            }          
        }
        if(!empty($page_id_array))
        {
            $this->db->where_in('page_id', $page_id_array);
            $this->db->delete("messenger_bot"); //delete all bot settings of pages of deleted account
            $this->db->where_in('page_id', $page_id_array);
            $this->db->delete("messenger_bot_postback"); //delete all bot payload/postback settings of pages of deleted account
        }
        if(!empty($menu_page_id_array))
        {
            $this->db->where_in('page_id', $menu_page_id_array);
            $this->db->delete("messenger_bot_persistent_menu"); //delete all persistent menu of pages of deleted account
        }
        if($no_enabled_pages>0)
        $this->_delete_usage_log($module_id=200,$request=$no_enabled_pages);  // messenger bot module
        if($no_menu_enabled_pages>0)
        $this->_delete_usage_log($module_id=197,$request=$no_menu_enabled_pages);  // persistent menu module
        $this->basic->delete_data('messenger_bot_page_info',array('id'=>$table_id,"user_id"=>$this->user_id)); // delete all page of that account
        $this->basic->delete_data('messenger_bot_domain_whitelist',array('page_id'=>$table_id,"user_id"=>$this->user_id)); // delete all whitlisted domain of that account
        
        if(!empty($page_id_array))
        {
            $this->db->where_in('page_id', $page_id_array);
            $this->db->delete("messenger_bot_postback"); //delete all bot payload/postback settings of pages of deleted account            
        }
        
        $this->db->trans_complete();
        if($this->db->trans_status() === false) 
        {
            echo "<div class='alert alert-danger text-center'>'".$this->lang->line("something went wrong, please try again.")."'</div>";
        }
        else
        {
            echo "<div class='alert alert-success text-center'>".$this->lang->line("your page and all of it's corresponding campaigns has been deleted successfully.")."</div>";
        }
    }

    public function send_user_roll_access()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(199,$this->module_access))
        exit(); 
        if($this->session->userdata("messenger_bot_user_info")==0 && $this->config->item("bot_backup_mode")==1)
        exit();
        $this->load->library("messenger_bot_login");  
        if($_POST)
        {
            $fb_numeric_id= $this->input->post("fb_numeric_id");
            $database_id = $this->session->userdata('messenger_bot_login_database_id');
            $facebook_config=$this->basic->get_data("messenger_bot_config",array("where"=>array("id"=>$database_id)));
            if(isset($facebook_config[0]))
            {           
                $app_id=$facebook_config[0]["api_id"];
                $app_secret=$facebook_config[0]["api_secret"];
                $user_access_token=$facebook_config[0]["user_access_token"];
            }
            $response=$this->messenger_bot_login->send_user_roll_access($app_id,$fb_numeric_id,$user_access_token);
   
            if(isset($response['success']) && $response['success'] == 1)
                echo "<br/>
            <div class='well'><h4 class='text-center' style='color:red !important;'>".$this->lang->line("please log in & check your facebook profile page notifications, to accept our invitation")."</h4></div>
            <div class='alert alert-danger text-center'>
                        <h4 style='line-height:25px'>".$this->lang->line("a request has been sent to your facebook account. please login to your facebook account, confirm the app request and click below button.")."<br/><br/>'".$this->lang->line("do not click this until confirmed")."</h4>
                        <br/>
                        <button class='btn btn-default btn-lg' id='fb_confirm'><b>".$this->lang->line("i've confirmed app request in facebook")."</b></button>
                    </div>";
            else if (isset($response["error"]["error_user_msg"]))
                 echo "<br/><div class='alert alert-danger text-center'>
                        <p><i class='fa fa-remove'></i> ".$response["error"]["error_user_msg"]."</p>
                    </div>";
            else
            {
                echo "<br/><div class='alert alert-danger text-center'>
                        <p><i class='fa fa-remove'></i> ".$this->lang->line("something went wrong, please try with correct information.")."<br>";
                if(isset($response['error']['message'])) 
                echo "<br>".$response['error']['message'];
                if(isset($response['error']['message']) && $response['error']['message']=='(#100) 372747716260046 does not resolve to a valid user ID');
                echo "<br> Please make sure this is the numeric ID of your profile not any of your page.";
                echo "</p>
                    </div>";
            }
        }
    }

    public function ajax_get_login_button()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(199,$this->module_access))
        exit();
        if($this->session->userdata("messenger_bot_user_info")==0 && $this->config->item("bot_backup_mode")==1)
        exit();
        $this->load->library("messenger_bot_login");  
        $redirect_url = base_url()."messenger_bot/user_login_callback";
        $fb_login_button = $this->messenger_bot_login->login_for_user_access_token($redirect_url);
        if(isset($fb_login_button))
        {
            echo '<h3 class="text-center">'.$fb_login_button.'<h3>';
        }
        else echo "<br/><div class='alert alert-danger text-center'><p>".$this->lang->line("something went wrong, please try again with proper information")."</p></div>";
    }
    
    public function user_login_callback()
    {
        $id = $this->session->userdata('messenger_bot_login_database_id');
        $this->load->library("messenger_bot_login");  
        $redirect_url = base_url()."messenger_bot/user_login_callback";
        $user_info = $this->messenger_bot_login->login_callback($redirect_url); 
                
        if( isset($user_info['status']) && $user_info['status'] == '0')
        {
            $data['error'] = 1;
            $data['message'] = "<a style='text-decoration:none;'href='".base_url("messenger_bot/account_import")."'>".$this->lang->line("something went wrong")." : ".$user_info['message']."</a>";
            $data['body'] = "user_login";
            $this->_viewcontroller($data);
        } 
        else 
        {
            //************************************************//
            $status=$this->_check_usage($module_id=199,$request=1);
            if($status=="2") 
            {
                $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                redirect('messenger_bot/account_import','location');                
                exit();
            }
            else if($status=="3") 
            {
                $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                redirect('messenger_bot/account_import','location');                
                exit();
            }
            //************************************************//
            $access_token=$user_info['access_token_set'];
            //checking permission given by the users            
            $permission = $this->messenger_bot_login->debug_access_token($access_token);
            $given_permission = array();
            if(isset($permission['data']['scopes']))
            {
                $permission_checking = array();
                $needed_permission = array('manage_pages','publish_pages','pages_messaging');
                $given_permission = $permission['data']['scopes'];
                $permission_checking = array_intersect($needed_permission,$given_permission);
                if(empty($permission_checking))
                {
                    // $documentation_link = base_url('documentation/#!/sm_import_account');
                    $text = "'".$this->lang->line("sorry, you didn't confirm the request yet. please login to your fb account and accept the request. for more");
                    $this->session->set_userdata('limit_cross', $text);
                    redirect('messenger_bot/account_import','location');                
                    exit();
                }
            }

            if(isset($access_token))
            {
                $data = array(
                    'user_id' => $this->user_id,
                    'messenger_bot_config_id' => $id,
                    'access_token' => $access_token,
                    'name' => $user_info['name'],
                    'email' => isset($user_info['email']) ? $user_info['email'] : "",
                    'fb_id' => $user_info['id'],
                    'add_date' => date('Y-m-d'),
                    'deleted' => '0'
                    );
                $where=array();
                $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                $exist_or_not = array();
                $exist_or_not = $this->basic->get_data('messenger_bot_user_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);
                if(empty($exist_or_not))
                {
                    $this->basic->insert_data('messenger_bot_user_info',$data);
                    $facebook_table_id = $this->db->insert_id();
                }
                else
                {
                    $facebook_table_id = $exist_or_not[0]['id'];
                    $where = array('user_id'=>$this->user_id,'id'=>$facebook_table_id);
                    $this->basic->update_data('messenger_bot_user_info',$where,$data);
                }
                $this->session->set_userdata("messenger_bot_user_info",$facebook_table_id);  
                $page_list = array();
                $page_list = $this->messenger_bot_login->get_page_list($access_token);
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
                        $page_access_token = '';
                        if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                        $page_email = '';
                        if(isset($page['emails'][0])) $page_email = $page['emails'][0];
                        $page_username = '';
                        if(isset($page['username'])) $page_username = $page['username'];
                        $data = array(
                            'user_id' => $user_id,
                            'messenger_bot_user_info_id' => $facebook_table_id,
                            'page_id' => $page_id,
                            'page_cover' => $page_cover,
                            'page_profile' => $page_profile,
                            'page_name' => $page_name,
                            'page_access_token' => $page_access_token,
                            'page_email' => $page_email,
                            'username' => $page_username,
                            'add_date' => date('Y-m-d'),
                            'deleted' => '0'
                            );
                        $where=array();
                        $where['where'] = array('messenger_bot_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                        $exist_or_not = array();
                        $exist_or_not = $this->basic->get_data('messenger_bot_page_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);
                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('messenger_bot_page_info',$data);
                        }
                        else
                        {
                            $where = array('messenger_bot_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $this->basic->update_data('messenger_bot_page_info',$where,$data);
                        }
                    }
                }
       
                //insert data to useges log table
                $this->_insert_usage_log($module_id=199,$request=1);
                $this->session->set_userdata('success_message', 'success');
                redirect('messenger_bot/account_import','location');                
                exit();
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = "'".$this->lang->line("something went wrong,please")."' <a href='".base_url("messenger_bot/account_import")."'>'".$this->lang->line("try again")."'</a>";
                $data['body'] = "user_login";
                $this->_viewcontroller($data);
            }
        }
    }

    public function refresh_login_callback()
    {
        $id = $this->session->userdata('messenger_bot_login_database_id');
        $this->load->library("messenger_bot_login");  
        $redirect_url = base_url()."messenger_bot/refresh_login_callback";
        $user_info = array();
        $user_info = $this->messenger_bot_login->login_callback($redirect_url);   
                
        if( isset($user_info['status']) && $user_info['status'] == '0')
        {
            $data['error'] = 1;
            $data['message'] = "<a style='text-decoration:none;' href='".base_url("messenger_bot/account_import")."'>".$this->lang->line("something went wrong")." : ".$user_info['message']."</a>";
            $data['body'] = "user_login";
            $this->_viewcontroller($data);
        } 
        else 
        {
            $access_token=$user_info['access_token_set'];
            //checking permission given by the users            
            $permission = $this->messenger_bot_login->debug_access_token($access_token);
            $given_permission = array();
            if(isset($permission['data']['scopes']))
            {
                $permission_checking = array();
                $needed_permission = array('manage_pages','publish_pages','pages_messaging');
                $given_permission = $permission['data']['scopes'];
                $permission_checking = array_intersect($needed_permission,$given_permission);
                if(empty($permission_checking))
                {
                    // $documentation_link = base_url('documentation/#!/sm_import_account');
                    $text = "'".$this->lang->line("sorry, you didn't confirm the request yet. please login to your fb account and accept the request. for more");
                    $this->session->set_userdata('limit_cross', $text);
                    redirect('messenger_bot/account_import','location');                
                    exit();
                }
            }
            
            if(isset($access_token))
            {
                $data = array(
                    'user_id' => $this->user_id,
                    'messenger_bot_config_id' => $id,
                    'access_token' => $access_token,
                    'name' => $user_info['name'],
                    'email' => isset($user_info['email']) ? $user_info['email'] : "",
                    'fb_id' => $user_info['id'],
                    'add_date' => date('Y-m-d'),
                    'deleted' => '0'
                    );
                $where=array();
                $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                $exist_or_not = array();
                $exist_or_not = $this->basic->get_data('messenger_bot_user_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);
                if(empty($exist_or_not))
                {
                    //************************************************//
                    $status=$this->_check_usage($module_id=199,$request=1);
                    if($status=="2") 
                    {
                        $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                        redirect('messenger_bot/account_import','location');
                        exit();
                    }
                    else if($status=="3") 
                    {
                        $this->session->set_userdata('limit_cross', $this->lang->line("Module limit is over."));
                        redirect('messenger_bot/account_import','location');
                        exit();
                    }
                    //************************************************//
                    $this->basic->insert_data('messenger_bot_user_info',$data);
                    $facebook_table_id = $this->db->insert_id();
                    //insert data to useges log table
                    $this->_insert_usage_log($module_id=199,$request=1);
                }
                else
                {
                    $facebook_table_id = $exist_or_not[0]['id'];
                    $where = array('user_id'=>$this->user_id,'id'=>$facebook_table_id);
                    $this->basic->update_data('messenger_bot_user_info',$where,$data);
                }
                $page_list = array();
                $page_list = $this->messenger_bot_login->get_page_list($access_token);
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
                        $page_access_token = '';
                        if(isset($page['access_token'])) $page_access_token = $page['access_token'];
                        $page_email = '';
                        if(isset($page['emails'][0])) $page_email = $page['emails'][0];
                        $page_username = '';
                        if(isset($page['username'])) $page_username = $page['username'];
                        $data = array(
                            'user_id' => $user_id,
                            'messenger_bot_user_info_id' => $facebook_table_id,
                            'page_id' => $page_id,
                            'page_cover' => $page_cover,
                            'page_profile' => $page_profile,
                            'page_name' => $page_name,
                            'username' => $page_username,
                            'page_access_token' => $page_access_token,
                            'page_email' => $page_email,
                            'add_date' => date('Y-m-d'),
                            'deleted' => '0'
                            );
                        $where=array();
                        $where['where'] = array('messenger_bot_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                        $exist_or_not = array();
                        $exist_or_not = $this->basic->get_data('messenger_bot_page_info',$where,$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='',$delete_overwrite=1);
                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('messenger_bot_page_info',$data);
                        }
                        else
                        {
                            $where = array('messenger_bot_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $this->basic->update_data('messenger_bot_page_info',$where,$data);
                        }
                    }
                }
                $this->session->set_userdata('success_message', 'success');
                redirect('messenger_bot/account_import','location');                
                exit();
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = "'".$this->lang->line("something went wrong,please")."' <a href='".base_url("messenger_bot/account_import")."'>'".$this->lang->line("try again")."'</a>";
                $data['body'] = "user_login";
                $this->_viewcontroller($data);
            }
        }
    }
    //==============================ACCOUNT IMPORT================================



    // ======================FACEBOOK APP CONFIG==================================
    public function facebook_config()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(200,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        if ($this->session->userdata('user_type')== "Member" && $this->config->item("bot_backup_mode")==0) {
            redirect('home/login', 'location');
        }
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        $crud->set_theme('flexigrid');
        $crud->set_table('messenger_bot_config');
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
            $images_url = base_url("plugins/grocery_crud/themes/flexigrid/css/images/login.png");
            $crud->add_action($this->lang->line('Login with Facebook'), $images_url, 'messenger_bot/fb_login');
        }
        
        $crud->fields('app_name','api_id', 'api_secret','status');
        $crud->where('user_id',$this->session->userdata('user_id'));
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->callback_column('validity', array($this, 'validity_display_crud'));
        $crud->callback_after_insert(array($this, 'make_up_fb_setting'));
        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();
        $crud->unset_delete();
        $total_rows_array = $this->basic->count_row("messenger_bot_config",array("where"=>array('user_id'=>$this->session->userdata('user_id'))), $count="id"); 
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
    public function make_up_fb_setting($post_array, $primary_key)
    {       
        if($this->session->userdata("user_type")=="Admin") $use_by = "everyone";
        else $use_by = "only_me";
        $this->basic->update_data("messenger_bot_config",array('id'=> $primary_key),array("user_id"=>$this->session->userdata("user_id"),'use_by'=>$use_by));
        return true;
    }
 
    public function fb_login($id)
    {     
        $this->session->set_userdata("messenger_bot_login_database_id",$id);
        $this->load->library("messenger_bot_login");
       
        $redirect_url = base_url()."messenger_bot/login_callback";        
        $data['fb_login_button'] = $this->messenger_bot_login->login_for_user_access_token($redirect_url);  
        $data['body'] = 'admin_login';
        $data['page_title'] =  $this->lang->line("admin login");
        $data['expired_or_not'] = $this->messenger_bot_login->access_token_validity_check();
        $this->_viewcontroller($data);
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
    function validity_display_crud($value, $row)
    {
        $input_token  = $row->user_access_token;
        if($input_token=="") 
        return "<span class='label label-warning' style='font-weight:normal'>Invalid</sapn>";
        $this->load->library("messenger_bot_login"); 
        $url="https://graph.facebook.com/debug_token?input_token={$input_token}&access_token={$input_token}";
        $result= $this->messenger_bot_login->run_curl_for_fb($url);
        $result = json_decode($result,true);
        if(isset($result["data"]["is_valid"]) && $result["data"]["is_valid"]) 
        {
             return "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('Valid')."</sapn>";
        } 
        else 
        {
            return "<span class='label label-warning'><i class='fa fa-clock-o red'></i> ".$this->lang->line('Expired')."</sapn>";
        }    
    }
    public function login_callback()
    {
    
        if ($this->session->userdata('logged_in')!= 1) exit();
        $id=$this->session->userdata("messenger_bot_login_database_id");
        $redirect_url = base_url()."messenger_bot/login_callback/";
        $this->load->library('messenger_bot_login');
        $user_info = $this->messenger_bot_login->login_callback($redirect_url);
        if(isset($user_info['status']) && $user_info['status'] == '0')
        {
            $data['error'] = 1;
            $data['message'] = "<a style='text-decoration:none;' href='".base_url("messenger_bot/facebook_config/")."'>".$this->lang->line("something went wrong")." : ".$user_info['message']."</a>";
            $data['body'] = "admin_login";
            $this->_viewcontroller($data);
        }
        else
        {
            $access_token=$user_info['access_token_set'];
            $where = array('id'=>$id);
            $update_data = array('user_access_token'=>$access_token);
            if($this->basic->update_data('messenger_bot_config',$where,$update_data))
            {
                $data = array(
                    'user_id' => $this->user_id,
                    'messenger_bot_config_id' => $id,
                    'access_token' => $access_token,
                    'name' => $user_info['name'],
                    'email' => isset($user_info['email']) ? $user_info['email'] : "",
                    'fb_id' => $user_info['id'],
                    'add_date' => date('Y-m-d')
                    );
                $where=array();
                $where['where'] = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                $exist_or_not = $this->basic->get_data('messenger_bot_user_info',$where);
                if(empty($exist_or_not))
                {
                    $this->basic->insert_data('messenger_bot_user_info',$data);
                    $facebook_table_id = $this->db->insert_id();
                }
                else
                {
                    $facebook_table_id = $exist_or_not[0]['id'];
                    $where = array('user_id'=>$this->user_id,'fb_id'=>$user_info['id']);
                    $this->basic->update_data('messenger_bot_user_info',$where,$data);
                }
                $this->session->set_userdata("messenger_bot_user_info",$facebook_table_id);
                $page_list = $this->messenger_bot_login->get_page_list($access_token);
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
                            'messenger_bot_user_info_id' => $facebook_table_id,
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
                        $where['where'] = array('messenger_bot_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                        $exist_or_not = $this->basic->get_data('messenger_bot_page_info',$where);
                        if(empty($exist_or_not))
                        {
                            $this->basic->insert_data('messenger_bot_page_info',$data);
                        }
                        else
                        {
                            $where = array('messenger_bot_user_info_id'=>$facebook_table_id,'page_id'=>$page['id']);
                            $this->basic->update_data('messenger_bot_page_info',$where,$data);
                        }
                    }
                }
                $this->session->set_flashdata('success_message', 1);
                redirect('messenger_bot/facebook_config','location');
                exit();
            }
            else
            {
                $data['error'] = 1;
                $data['message'] = "<a href='".base_url("messenger_bot/facebook_config/")."'>".$this->lang->line("something went wrong, please try again.")."</a>";
                $data['body'] = "admin_login";
                $this->_viewcontroller($data);
            }
        }
    }
    
    // ======================Messenger Bot Subscriber Lists==================================
    public function lead_list_data($page_id=0) // auto id
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $client_firstname = trim($this->input->post("search_client_firstname", true));
        $client_lastname = trim($this->input->post("search_client_lastname", true));

        if($this->db->table_exists('messenger_bot_broadcast_contact_group'))
        {
            $contact_type_id = trim($this->input->post("contact_type_id", true));
        }

        $is_searched = $this->input->post('is_searched', true);

        if($is_searched)
        {
            $this->session->set_userdata('messenger_bot_lead_list_custom_firstname', $client_firstname);
            $this->session->set_userdata('messenger_bot_lead_list_custom_lastname', $client_lastname);
            if($this->db->table_exists('messenger_bot_broadcast_contact_group'))
            {
                $this->session->set_userdata('messenger_bot_lead_list_custom_group', $contact_type_id);
            }
        }

        $search_client_firstname  = $this->session->userdata('messenger_bot_lead_list_custom_firstname');
        $search_client_lastname  = $this->session->userdata('messenger_bot_lead_list_custom_lastname');
        if($this->db->table_exists('messenger_bot_broadcast_contact_group'))
        {
            $contact_group_id    = $this->session->userdata('messenger_bot_lead_list_custom_group');
        }

        $where_simple=array();

        $where_simple['messenger_bot_subscriber.page_id'] = $page_id;

        if ($search_client_firstname) 
        {
            $where_simple['first_name like '] = "%".$search_client_firstname."%";
        }
        if ($search_client_lastname) 
        {
            $where_simple['last_name like '] = "%".$search_client_lastname."%";
        }

        if($this->db->table_exists('messenger_bot_broadcast_contact_group'))
        {
            if($contact_group_id)
            {
                $this->db->where("FIND_IN_SET('$contact_group_id',messenger_bot_subscriber.contact_group_id) !=", 0);
            }
        }

        $where_simple['messenger_bot_subscriber.user_id'] = $this->user_id;
        $where_simple['messenger_bot_page_info.user_id'] = $this->user_id;
        $where_simple['messenger_bot_page_info.bot_enabled'] = '1';
        $where_simple['messenger_bot_page_info.deleted'] = '0';
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $where = array('where' => $where_simple);

        $join = array('messenger_bot_page_info'=>"messenger_bot_page_info.page_id=messenger_bot_subscriber.page_id,left");
        $select = array("messenger_bot_subscriber.*","messenger_bot_page_info.page_name","messenger_bot_page_info.id as page_auto_id");

        $table = "messenger_bot_subscriber";
        $info = $this->basic->get_data($table,$where,$select,$join,$limit=$rows, $start=$offset,$order_by=$order_by_str);


        $fbinboxerORbotinboxer = $this->db->table_exists('facebook_rx_fb_page_info');

        for($i=0;$i<count($info);$i++)
        {
            // $info[$i]['userName'] = $info[$i]['first_name'].' '.$info[$i]['last_name'];


            $info[$i]['subscribed_at'] = date("jS M, y H:i:s",strtotime($info[$i]['subscribed_at']));

            if($info[$i]['image_path'] !='')
            {
                $info[$i]['image_path'] = "<img src='".base_url($info[$i]['image_path'])."' class='img-circle' style='height:30px;width:30px;'>";
            }
            else
            {
                $info[$i]['image_path'] = "<img src='".base_url("assets/images/avatar.png")."' class='img-circle' style='height:30px;width:30px;'>";
            }


            if($info[$i]['status'] == '1')
            {
                $info[$i]['status']= "<button id ='".$info[$i]['id']."-".$info[$i]['status']."' type='button' class='client_thread_subscribe_unsubscribe btn btn-outline-danger btn-sm'><i class='fa fa-ban'></i> ".$this->lang->line("Stop Bot")."</button>";//$info[$i]['permission'];
            }
            elseif ($info[$i]['status'] == '0') 
            {
                $info[$i]['status'] = "<button id ='".$info[$i]['id']."-".$info[$i]['status']."' type='button' class='client_thread_subscribe_unsubscribe btn btn-outline-success btn-sm'><i class='fa fa-check-circle'></i> ".$this->lang->line("Start Bot")."</button>";
            }


            if ($fbinboxerORbotinboxer == TRUE)
            {
                $info[$i]['action'] = "<button button_id ='".$info[$i]['id']."-".$info[$i]['subscribe_id']."-".$info[$i]['page_id']."' type='button' class='update_user_details btn btn-outline-warning btn-sm'><i class='fa fa-refresh'></i> ".$this->lang->line("Update Details")."</button>";
            }

        }


        if($this->db->table_exists('messenger_bot_broadcast_contact_group'))
        {
            if($contact_group_id)
            {
                $this->db->where("FIND_IN_SET('$contact_group_id',messenger_bot_subscriber.contact_group_id) !=", 0);
            }
        }

        $total_rows_array = $this->basic->count_row($table, $where, $count = "messenger_bot_subscriber.id",$join);
        $total_result = $total_rows_array[0]['total_rows'];


        echo convert_to_grid_data($info, $total_result);
    }


    public function user_details_modal_bot()
    {
        if (empty($_POST['user_id_page_id'])) {
            die();
        }

        $user_id_and_page_id = explode("-",$_POST['user_id_page_id']);

        $user_id = $user_id_and_page_id[0];
        $page_id = $user_id_and_page_id[1];

        $search_firstname     = $this->session->userdata('messenger_bot_lead_list_custom_firstname');             
        $search_lastname     = $this->session->userdata('messenger_bot_lead_list_custom_lastname');             
        $search_lead_group   = $this->session->userdata('messenger_bot_lead_list_custom_group');
        $contact_type_id[''] = $this->lang->line('All Groups');

        if($this->db->table_exists('messenger_bot_broadcast_contact_group'))
        {
            $table = 'messenger_bot_broadcast_contact_group';
            $where['where'] = array('user_id'=>$this->user_id);
            $info = $this->basic->get_data($table,$where);

            foreach ($info as $key => $value) 
            {
                $result = $value['id'];
                $contact_type_id[$result] = $value['group_name'];
            }

            $dropdown=form_dropdown('contact_type_id',$contact_type_id,$search_lead_group,'class="form-control" id="contact_type_id"');  

        }
        

        $fbinboxerORbotinboxer = $this->db->table_exists('facebook_rx_fb_page_info');
        $broadcasttableExist   = $this->db->table_exists('messenger_bot_broadcast_contact_group'); 

        $table_name = "messenger_bot_subscriber";
        $where['where'] = array('user_id' => $user_id, 'page_id' => $page_id);
        $one_page_user_details = $this->basic->get_data($table_name,$where);

        // subscriber lists new modal

        $html = "<div class='text-center' style='margin-top: -20px !important;'>
                    <button class='btn btn-outline-info download_subscriber' page_id='".$page_id."'><i class='fa fa-cloud-download'></i> ".$this->lang->line("Download subscriber list")."</button>
                 </div><br>";

        $html.='<form class="form-inline" style="margin:10px 15px 0 15px;">
                  <div class="form-group">
                    <input id="search_client_firstname" name="search_client_firstname" value="'.$search_firstname.'" class="form-control" size="20" placeholder="'.$this->lang->line("first name").'">
                    <input id="search_client_lastname" name="search_client_lastname" value="'.$search_lastname.'" class="form-control" size="20" placeholder="'.$this->lang->line("last name").'">
                  </div>';
        if($broadcasttableExist == TRUE)
        {
            $html .='<div class="form-group">'.$dropdown.'</div>';
        }

        $html .='<button class="btn btn-info"  onclick="doSearch(event)"><i class="fa fa-search"></i>'.$this->lang->line("search").'</button>    
                </form></div>';

        $html.="<div id='response_div_part' style='min-width:800px;' ><table id='dg'></table></div>
        <script> 
        \$j('#dg').datagrid({
            url:'".base_url().'messenger_bot/lead_list_data/'.$page_id."',
            method:'post',
            idField:'id',
            pagination:'true',
            rownumbers:'true',
            toolbar:'#tb',
            pageSize:'10',
            pageList:[5,10,15,20,50,100],
            fit: true,
            fitColumns: true,
            nowrap: true,
            columns:[[
                {field:'image_path',title:'".$this->lang->line("picture")."',align:'center',width:'7%'},
                {field:'first_name',title:'".$this->lang->line("first name")."',align:'center',width:'20%'},
                {field:'last_name',title:'".$this->lang->line("last name")."',align:'center',width:'20%'},
                {field:'subscribed_at',title:'".$this->lang->line("Subscribed at")."',align:'center',width:'20%'},
                {field:'status',title:'".$this->lang->line("status")."',align:'center',width:'13%'},";
                if ($fbinboxerORbotinboxer == TRUE)
                    $html.="{field:'action',title:'".$this->lang->line("action")."',align:'center',width:'20%'}
            ]]
        });
        </script>";

        $html.='
        <script>   
          function doSearch(event)
          {
            event.preventDefault(); 
            $j("#dg").datagrid("load",{
              search_client_firstname: $j("#search_client_firstname").val(),    
              search_client_lastname: $j("#search_client_lastname").val(),    
              contact_type_id       : $j("#contact_type_id").val(),  
              is_searched           : 1
            });
          } 
        </script>';

        $html.='        
            <style>
            .datagrid-view{height:465px !important;border-top:1px solid #ccc !important;border-bottom:1px solid #ccc !important;}
            .datagrid .datagrid-pager{border-width:0px !important;margin-top:0 !important;}
            .datagrid-wrap{height:auto !important;padding-bottom:0 !important;padding-top:10px !important;}
            </style>';
        
        echo $html;
    }

    
    public function subscriber_list_download()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if(empty($_POST['page_id'])) {
            die();
        }
        $table_name = "messenger_bot_subscriber";
        $user_id = $this->user_id;
        $page_id = $this->input->post('page_id');
        $where['where'] = array('user_id' => $user_id, 'page_id' => $page_id);
        $one_page_user_details = $this->basic->get_data($table_name,$where);

        if(empty($one_page_user_details))
        {
            $str = "<div class='alert alert-danger text-center'>".$this->lang->line("No data to download")."</div>";
        }
        else
        {
            $download_path=fopen("download/subscriber_download_{$this->user_id}.csv", "w");
            // make output csv file unicode compatible
            fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
            /**Write header in csv file***/
            $write_data[]="User ID";
            $write_data[]="Page ID";
            $write_data[]="subscribe ID";
            // $write_data[]="Contact Group ID";
            $write_data[]="Locale";
            $write_data[]="First Name";
            $write_data[]="Last Name";
            $write_data[]="Gender";
            $write_data[]="Subscribed at";
            $write_data[]="Status";
            // $write_data[]="Paage Name";
            fputcsv($download_path, $write_data);
            foreach($one_page_user_details as $value)
            {
                $write_data=array();
                $write_data[]=$value['user_id'];
                $write_data[]=$value['page_id'];
                $write_data[]=$value['subscribe_id'];
                // $write_data[]=$value['contact_group_id'];
                $write_data[]=$value['locale'];
                $write_data[]=$value['first_name'];
                $write_data[]=$value['last_name'];
                $write_data[]=$value['gender'];
                $write_data[]=$value['subscribed_at'];
                $write_data[]=$value['status'];
                // $write_data[]=$page_info[0]['page_name'];
                fputcsv($download_path, $write_data);
            }
            $str = "<div class='download_box'><h2>".$this->lang->line('Your file is ready to download')."</h2>";
            $str .= '<i class="fa fa-2x fa-thumbs-o-up"style="color:black"></i><br><br>';
            $str .= "<a href='".base_url()."download/subscriber_download_".$this->user_id.".csv"."'". "title='Download' class='btn btn-warning btn-lg' style='width:200px;'><i class='fa fa-cloud-download' style='color:white'></i> ".$this->lang->line('Download')."</a></div>"; 
        }
        echo $str;
    }
    
    public function delete_error_log($id=0)
    {  
        if($id == 0) exit();      
        $this->basic->delete_data("messenger_bot_reply_error_log",array("id"=>$id));
        redirect(base_url('messenger_bot/bot_list'),'location');
    }
    public function error_log_report()
    {
        if(empty($_POST['table_id'])) {
            die();
        }
        $user_id = $this->user_id;
        $page_table_id = $this->input->post('table_id');        
        $table_name = "messenger_bot_reply_error_log";
        $select=array("messenger_bot_reply_error_log.*","bot_name");
        $join = array('messenger_bot'=>"messenger_bot_reply_error_log.bot_settings_id=messenger_bot.id,left");   
        $where['where'] = array('messenger_bot_reply_error_log.user_id' => $user_id, 'messenger_bot_reply_error_log.page_id' => $page_table_id);
        $error_log_report_info = $this->basic->get_data($table_name,$where,$select,$join);       
        $html = '<script>
                    $j(document).ready(function() {
                        $("#error_log_datatable").DataTable();
                    }); 
                 </script>';
        $html .= "
            <table id='error_log_datatable' class='table table-striped table-bordered' cellspacing='0' width='100%''>
            <thead>
                <tr>
                    <th>".$this->lang->line("Bot Name")."</th>
                    <th>".$this->lang->line("Error Message")."</th>
                    <th class='text-center'>".$this->lang->line("Error Time")."</th>
                    <th class='text-center'>".$this->lang->line("Actions")."</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($error_log_report_info as $error_info) 
        {
            $html .= "<tr>
                        <td>".$error_info['bot_name']."</td>
                        <td>".$error_info['error_message']."</td>
                        <td class='text-center'>".date("jS M, y H:i:s",strtotime($error_info['error_time']))."</td>
                        <td class='text-center'>
                              <a class='btn btn-outline-warning' href=".base_url('messenger_bot/edit_bot/').$error_info['bot_settings_id']."> <i class='fa fa-edit'></i> ".$this->lang->line("Edit Bot")."</a> 
                              <a class='btn btn-outline-danger' href=".base_url('messenger_bot/delete_error_log/').$error_info['id']."> <i class='fa fa-trash'></i> ".$this->lang->line("Delete Log")."</a> 
                             
                        </td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>
                </table>
                ";
        echo $html;
    }
    public function client_subscribe_unsubscribe_status_change()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if (empty($_POST['client_subscribe_unsubscribe_status'])) {
            die();
        }
        $client_subscribe_unsubscribe = array();
        $post_val=$this->input->post('client_subscribe_unsubscribe_status');
        $client_subscribe_unsubscribe = explode("-",$post_val);
        $id = isset($client_subscribe_unsubscribe[0]) ? $client_subscribe_unsubscribe[0]: 0;
        $current_status =  isset($client_subscribe_unsubscribe[1]) ? $client_subscribe_unsubscribe[1]: 0;
        
        if($current_status=="1") $permission="0";
        else $permission="1";
        
        $where = array
        (
            'id' => $id,
            'user_id' => $this->user_id
        );
        $data = array('status' => $permission);
        
            
        if($permission=="0")
        {
            $response = "<button id ='".$id."-".$permission."' type='button' class='client_thread_subscribe_unsubscribe btn btn-sm btn-outline-success'><i class='fa fa-check-circle'></i> ".$this->lang->line('Start Bot')."</button>";
            $this->basic->update_data("messenger_bot_subscriber",$where, $data);
        }
        else  
        {
            $response = "<button id ='".$id."-".$permission."' type='button' class='client_thread_subscribe_unsubscribe btn btn-sm btn-outline-danger'><i class='fa fa-ban'></i> ".$this->lang->line('Stop Bot')."</button>";
            $this->basic->update_data("messenger_bot_subscriber",$where, $data);
        }
        echo $response;
    }
    public function edit_quick_email_reply($auto_id="",$page_id="")
    {
       if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"QUICK_REPLY_EMAIL_REPLY_BOT","page_id"=>$auto_id)))
       {
            $user_id=$this->user_id;
            $sql='INSERT INTO `messenger_bot` ( `user_id`, `page_id`, `fb_page_id`, `template_type`, `bot_type`, `keyword_type`, `keywords`, `message`, `buttons`, `images`, `audio`, `video`, `file`, `status`, `bot_name`, `postback_id`, `last_replied_at`, `is_template`) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "email-quick-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","text":"Thanks, we have received your email. We will keep you updated. Thank you for being with us."}}}\', "", "", "", "", "", "1", "QUICK REPLY EMAIL REPLY", "QUICK_REPLY_EMAIL_REPLY_BOT", "0000-00-00 00:00:00", "0");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for) VALUES
            ("'.$user_id.'","QUICK_REPLY_EMAIL_REPLY_BOT","'.$auto_id.'","0","1","'.$insert_id.'","QUICK REPLY EMAIL REPLY","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","text":"Thanks, we have received your email. We will keep you updated. Thank you for being with us."}}}\',"QUICK REPLY EMAIL REPLY","email-quick-reply")';
            $this->db->query($sql);
        }
        $postback_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("template_for"=>"email-quick-reply","user_id"=>$this->user_id,"page_id"=>$auto_id)));
        $postback_id=isset($postback_info[0]['id'])?$postback_info[0]['id']:0;
        redirect(base_url('messenger_bot/edit_template/').$postback_id,'location');
    }

    public function edit_quick_phone_reply($auto_id="",$page_id="")
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"QUICK_REPLY_PHONE_REPLY_BOT","page_id"=>$auto_id)))
       {
            $user_id=$this->user_id;
            $sql='INSERT INTO `messenger_bot` ( `user_id`, `page_id`, `fb_page_id`, `template_type`, `bot_type`, `keyword_type`, `keywords`, `message`, `buttons`, `images`, `audio`, `video`, `file`, `status`, `bot_name`, `postback_id`, `last_replied_at`, `is_template`) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "phone-quick-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","text":"Thanks, we have received your phone. Thank you for being with us."}}}\', "", "", "", "", "", "1", "QUICK REPLY PHONE REPLY", "QUICK_REPLY_PHONE_REPLY_BOT", "0000-00-00 00:00:00", "0");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for) VALUES
            ("'.$user_id.'","QUICK_REPLY_PHONE_REPLY_BOT","'.$auto_id.'","0","1","'.$insert_id.'","QUICK REPLY PHONE REPLY","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","text":"Thanks, we have received your phone. Thank you for being with us."}}}\',"QUICK REPLY PHONE REPLY","phone-quick-reply")';
            $this->db->query($sql);
        }
        $postback_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("template_for"=>"phone-quick-reply","user_id"=>$this->user_id,"page_id"=>$auto_id)));
        $postback_id=isset($postback_info[0]['id'])?$postback_info[0]['id']:0;
        redirect(base_url('messenger_bot/edit_template/').$postback_id,'location');
    }
    
    public function edit_quick_location_reply($auto_id="",$page_id="")
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"QUICK_REPLY_LOCATION_REPLY_BOT","page_id"=>$auto_id)))
       {
            $user_id=$this->user_id;
            $sql='INSERT INTO `messenger_bot` ( `user_id`, `page_id`, `fb_page_id`, `template_type`, `bot_type`, `keyword_type`, `keywords`, `message`, `buttons`, `images`, `audio`, `video`, `file`, `status`, `bot_name`, `postback_id`, `last_replied_at`, `is_template`) VALUES
            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "location-quick-reply","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","text":"Thanks, we have received your location. Thank you for being with us."}}}\', "", "", "", "", "", "1", "QUICK REPLY LOCATION REPLY", "QUICK_REPLY_LOCATION_REPLY_BOT", "0000-00-00 00:00:00", "0");';
            $this->db->query($sql);
            $insert_id=$this->db->insert_id();
            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for) VALUES
            ("'.$user_id.'","QUICK_REPLY_LOCATION_REPLY_BOT","'.$auto_id.'","0","1","'.$insert_id.'","QUICK REPLY LOCATION REPLY","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text","text":"Thanks, we have received your location. Thank you for being with us."}}}\',"QUICK REPLY LOCATION REPLY","location-quick-reply")';
            $this->db->query($sql);
        }
        $postback_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("template_for"=>"location-quick-reply","user_id"=>$this->user_id,"page_id"=>$auto_id)));
        $postback_id=isset($postback_info[0]['id'])?$postback_info[0]['id']:0;
        redirect(base_url('messenger_bot/edit_template/').$postback_id,'location');
    }

    protected function sdk_locale()
    {
        $config = array(
            'default'=> 'Default',
            'af_ZA' => 'Afrikaans',
            'ar_AR' => 'Arabic',
            'az_AZ' => 'Azerbaijani',
            'be_BY' => 'Belarusian',
            'bg_BG' => 'Bulgarian',
            'bn_IN' => 'Bengali',
            'bs_BA' => 'Bosnian',
            'ca_ES' => 'Catalan',
            'cs_CZ' => 'Czech',
            'cy_GB' => 'Welsh',
            'da_DK' => 'Danish',
            'de_DE' => 'German',
            'el_GR' => 'Greek',
            'en_GB' => 'English (UK)',
            'en_PI' => 'English (Pirate)',
            'en_UD' => 'English (Upside Down)',
            'en_US' => 'English (US)',
            'eo_EO' => 'Esperanto',
            'es_ES' => 'Spanish (Spain)',
            'es_LA' => 'Spanish',
            'et_EE' => 'Estonian',
            'eu_ES' => 'Basque',
            'fa_IR' => 'Persian',
            'fb_LT' => 'Leet Speak',
            'fi_FI' => 'Finnish',
            'fo_FO' => 'Faroese',
            'fr_CA' => 'French (Canada)',
            'fr_FR' => 'French (France)',
            'fy_NL' => 'Frisian',
            'ga_IE' => 'Irish',
            'gl_ES' => 'Galician',
            'he_IL' => 'Hebrew',
            'hi_IN' => 'Hindi',
            'hr_HR' => 'Croatian',
            'hu_HU' => 'Hungarian',
            'hy_AM' => 'Armenian',
            'id_ID' => 'Indonesian',
            'is_IS' => 'Icelandic',
            'it_IT' => 'Italian',
            'ja_JP' => 'Japanese',
            'ka_GE' => 'Georgian',
            'km_KH' => 'Khmer',
            'ko_KR' => 'Korean',
            'ku_TR' => 'Kurdish',
            'la_VA' => 'Latin',
            'lt_LT' => 'Lithuanian',
            'lv_LV' => 'Latvian',
            'mk_MK' => 'Macedonian',
            'ml_IN' => 'Malayalam',
            'ms_MY' => 'Malay',
            'nb_NO' => 'Norwegian (bokmal)',
            'ne_NP' => 'Nepali',
            'nl_NL' => 'Dutch',
            'nn_NO' => 'Norwegian (nynorsk)',
            'pa_IN' => 'Punjabi',
            'pl_PL' => 'Polish',
            'ps_AF' => 'Pashto',
            'pt_BR' => 'Portuguese (Brazil)',
            'pt_PT' => 'Portuguese (Portugal)',
            'ro_RO' => 'Romanian',
            'ru_RU' => 'Russian',
            'sk_SK' => 'Slovak',
            'sl_SI' => 'Slovenian',
            'sq_AL' => 'Albanian',
            'sr_RS' => 'Serbian',
            'sv_SE' => 'Swedish',
            'sw_KE' => 'Swahili',
            'ta_IN' => 'Tamil',
            'te_IN' => 'Telugu',
            'th_TH' => 'Thai',
            'tl_PH' => 'Filipino',
            'tr_TR' => 'Turkish',
            'uk_UA' => 'Ukrainian',
            'vi_VN' => 'Vietnamese',
            'zh_CN' => 'Chinese (China)',
            'zh_HK' => 'Chinese (Hong Kong)',           
            'zh_TW' => 'Chinese (Taiwan)',
        );
        asort($config);
        return $config;
    }

    public function remove_persistent_menu_locale($auto_id=0,$page_auto_id=0)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        $this->basic->delete_data("messenger_bot_persistent_menu",array("id"=>$auto_id,"user_id"=>$this->user_id));
        $this->session->set_flashdata('remove_persistent_menu_locale',1);
        redirect(base_url('messenger_bot/persistent_menu_list/'.$page_auto_id),'location');    
    } 
    public function remove_persistent_menu($page_auto_id=0)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $this->load->library("messenger_bot_login"); 
        $page_info=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        $page_access_token=$page_info[0]['page_access_token'];
        $response=$this->messenger_bot_login->delete_persistent_menu($page_access_token);
        if(!isset($response['error']))
        {
            $this->basic->update_data('messenger_bot_page_info',array("id"=>$page_auto_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'0'));
            $this->basic->delete_data('messenger_bot_persistent_menu',array("page_id"=>$page_auto_id,'user_id'=>$this->user_id));
            $this->session->set_flashdata('perrem_success',1);
            $this->_delete_usage_log($module_id=197,$request=1);
        }
        else
        {
            $err_message=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
            
            $this->session->set_flashdata('perrem_success',0);
            $this->session->set_flashdata('perrem_message',$err_message);
        }
        redirect(base_url('messenger_bot/bot_list'),'location');
    } 
    public function publish_persistent_menu($page_auto_id=0)
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        $page_info=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        $page_access_token=$page_info[0]['page_access_token'];
        $is_already_persistent_enabled=$page_info[0]['persistent_enabled'];
        if($is_already_persistent_enabled=='0') // no need to check if it was already published and user is just editing menu
        {
            $status=$this->_check_usage($module_id=197,$request=1);
            if($status=="3") 
            {
                $this->session->set_flashdata('per_success',0);
                $this->session->set_flashdata('per_message',$this->lang->line("You are not allowed to publish new persistent menu. Module limit has been exceeded.")); 
                $this->_insert_usage_log($module_id=197,$request=1);   
                redirect(base_url('messenger_bot/persistent_menu_list/'.$page_auto_id),'location'); 
            }
        }
        $this->load->library("messenger_bot_login"); 
        $json_array=array();
        $menu_data=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("page_id"=>$page_auto_id,"user_id"=>$this->user_id)));
        foreach ($menu_data as $key => $value) 
        {
            $temp=json_decode($value["item_json"],true);
            $temp2=isset($temp['call_to_actions'])?$temp['call_to_actions']:array();
          
            if($this->session->userdata('user_type') == 'Member' && in_array(198,$this->module_access) && count($temp2)<3)
            {
                end($temp2);        
                $key2 = key($temp2); 
                $key2++;
                $copyright_text=$this->config->item("persistent_menu_copyright_text");
                if($copyright_text=="") $copyright_text=$this->config->item("product_name");
                $copyright_url=$this->config->item("persistent_menu_copyright_url");
                if($copyright_url=="") $copyright_url=base_url();
                $temp["call_to_actions"][$key2]["title"]=$copyright_text;
                $temp["call_to_actions"][$key2]["type"]="web_url";
                $temp["call_to_actions"][$key2]["url"]=$copyright_url;
            }
            $json_array["persistent_menu"][]=$temp;
        }
        
        $json=json_encode($json_array);
      
        $response=$this->messenger_bot_login->add_persistent_menu($page_access_token,$json);
        
        if(!isset($response['error']))
        {
            if(!empty($postback_insert_data))
            $this->db->insert_batch('messenger_bot_postback',$postback_insert_data);
            $this->basic->update_data('messenger_bot_page_info',array("id"=>$page_auto_id,'user_id'=>$this->user_id),array("persistent_enabled"=>'1'));
            $this->session->set_flashdata('menu_success',1); 
            if($is_already_persistent_enabled=='0') // no need to check if it was already published and user is just editing menu
            $this->_insert_usage_log($module_id=197,$request=1);   
            redirect(base_url('messenger_bot/bot_list'),'location');        
        }
        else
        {
            $err_message=isset($response['error']['message'])?$response['error']['message']:$this->lang->line("something went wrong, please try again.");
            $this->session->set_flashdata('per_success',0);
            $this->session->set_flashdata('per_message',$err_message); 
            redirect(base_url('messenger_bot/persistent_menu_list/'.$page_auto_id),'location');       
        }         
    }

    public function persistent_menu_list($page_auto_id=0)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $data['body'] = 'persistent_menu_list';
        $data['page_title'] = $this->lang->line('Persistent Menu List');  
        $page_info=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array(); 
        $data["menu_info"]=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("page_id"=>$page_auto_id,"user_id"=>$this->user_id)));
        $this->_viewcontroller($data);
    }

    public function create_persistent_menu($page_auto_id=0)
    {        
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $data['body'] = 'persistent_menu';
        $data['page_title'] = $this->lang->line('Persistent Menu');  
        $page_info=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();
        
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array(); 
        $started_button_enabled = isset($page_info[0]["started_button_enabled"])?$page_info[0]["started_button_enabled"]:"0";
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)));
        $data['postback_ids'] = $postback_id_list;
        $data['page_auto_id'] = $page_auto_id;
        $data['started_button_enabled'] = $started_button_enabled;
        $data['locale']=$this->sdk_locale();
        $this->_viewcontroller($data);
    }

    public function create_persistent_menu_action()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }

        if(!$_POST) exit();
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }
        if($this->basic->is_exist("messenger_bot_persistent_menu",array("page_id"=>$page_table_id,"locale"=>$locale)))
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("persistent menu is already exists for this locale.")));
            exit();
        }
        $menu=array();
        $postback_insert_data=array();
        $only_postback=array();
        for($i=1;$i<=$level1_limit;$i++)
        {
            $level_title_temp="text_with_buttons_text_".$i;
            $level_type_temp="text_with_button_type_".$i;
            if($$level_title_temp=="") continue; // form gets everything but we need only filled data
            if($$level_type_temp=="post_back") $$level_type_temp="postback";
            $menu[$i]=array
            (
                "title"=>$$level_title_temp,
                "type"=> $$level_type_temp
            );
            if($$level_type_temp=="postback")
            {
                $level_postback_temp="text_with_button_post_id_".$i;
                $level_postback_temp_data=isset($$level_postback_temp) ? $$level_postback_temp : '';
                // $$level_postback_temp=strtoupper($$level_postback_temp);
                $menu[$i]["payload"]=$level_postback_temp_data;
                $single_postback_insert_data = array();
                $single_postback_insert_data['user_id'] = $this->user_id;
                $single_postback_insert_data['postback_id'] = $level_postback_temp_data;
                $single_postback_insert_data['page_id'] = $page_table_id;
                $single_postback_insert_data['bot_name'] = '';
                $postback_insert_data[] = $single_postback_insert_data; 
                $only_postback[]=$level_postback_temp_data;
            }
            else if($$level_type_temp=="web_url")
            {
                $level_web_url_temp="text_with_button_web_url_".$i;
                $menu[$i]["url"]=$$level_web_url_temp;
            }
            else
            {
                for($j=1;$j<=$level2_limit;$j++)
                {
                    $level2_title_temp="text_with_buttons_text_".$i."_".$j;
                    $level2_type_temp="text_with_button_type_".$i."_".$j;
                    if($$level2_title_temp=="") continue; // form gets everything but we need only filled data
                    if($$level2_type_temp=="post_back") $$level2_type_temp="postback";
                    $menu[$i]["call_to_actions"][$j]["title"]=$$level2_title_temp;
                    $menu[$i]["call_to_actions"][$j]["type"]=$$level2_type_temp;
                    if($$level2_type_temp=="postback")
                    {
                        $level2_postback_temp="text_with_button_post_id_".$i."_".$j;
                        $level2_postback_temp_data=isset($$level2_postback_temp) ? $$level2_postback_temp : '';
                        // $$level2_postback_temp=strtoupper($$level2_postback_temp);
                        $menu[$i]["call_to_actions"][$j]["payload"]=$level2_postback_temp_data;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $level2_postback_temp_data;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = '';
                        $postback_insert_data[] = $single_postback_insert_data; 
                        $only_postback[]=$level2_postback_temp_data;
                    }
                    else if($$level2_type_temp=="web_url")
                    {
                        $level2_web_url_temp="text_with_button_web_url_".$i."_".$j;
                        $menu[$i]["call_to_actions"][$j]["url"]=$$level2_web_url_temp;
                    }
                    else
                    {
                        for($k=1;$k<=$level3_limit;$k++)
                        {
                            $level3_title_temp="text_with_buttons_text_".$i."_".$j."_".$k;
                            $level3_type_temp="text_with_button_type_".$i."_".$j."_".$k;
                            if($$level3_title_temp=="") continue; // form gets everything but we need only filled data
                            if($$level3_type_temp=="post_back") $$level3_type_temp="postback";
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["title"]=$$level3_title_temp;
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["type"]=$$level3_type_temp;
                            if($$level3_type_temp=="postback")
                            {
                                $level3_postback_temp="text_with_button_post_id_".$i."_".$j."_".$k;
                                $level3_postback_temp_data=isset($$level3_postback_temp) ? $$level3_postback_temp : '';
                                // $$level3_postback_temp=strtoupper($$level3_postback_temp);
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["payload"]=$level3_postback_temp_data;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $level3_postback_temp_data;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = '';
                                $postback_insert_data[] = $single_postback_insert_data; 
                                $only_postback[]=$level3_postback_temp_data;
                            }
                            else if($$level3_type_temp=="web_url")
                            {
                                $level3_web_url_temp="text_with_button_web_url_".$i."_".$j."_".$k;
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["url"]=$$level3_web_url_temp;
                            }
                        }
                    }
                }
            }
        }
        $menu_json_array=array();
        $menu_json_array["locale"]=$locale;
        $composer_input_disabled2='false';
        if($composer_input_disabled==='1') $composer_input_disabled2='true';
        $menu_json_array["composer_input_disabled"]=$composer_input_disabled2;
        $index=1;
        foreach ($menu as $key => $value) 
        {
           $menu_json_array["call_to_actions"][$index]=$value;
           $index++;
        }
        $menu_json=json_encode($menu_json_array); 
        $insert_data = array();       
        $insert_data['page_id'] = $page_table_id;
        $messenger_bot_user_info_id = $this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_table_id)),array("messenger_bot_user_info_id","page_access_token"));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["messenger_bot_user_info_id"];
        $this->db->trans_start();
        // if(!empty($postback_insert_data)) $this->db->insert_batch('messenger_bot_postback',$postback_insert_data);
        $this->basic->insert_data("messenger_bot_persistent_menu",array("user_id"=>$this->user_id,"page_id"=>$page_table_id,"locale"=>$locale,"item_json"=>$menu_json,"composer_input_disabled"=>$composer_input_disabled,'poskback_id_json'=>json_encode($only_postback)));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
        else  
        {
            $this->session->set_flashdata('per_success',1);
            echo json_encode(array('status'=>'1','message'=>$this->lang->line("persistent menu has been created successfully.")));
        }      
    }

    public function edit_persistent_menu($id=0)
    {        
        if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access))
        redirect('home/login_page', 'location'); 
        
        $data['body'] = 'persistent_menu_edit';
        $data['page_title'] = $this->lang->line('Edit Persistent Menu');  
        $xdata=$this->basic->get_data("messenger_bot_persistent_menu",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $page_auto_id=$xdata[0]["page_id"];
        $page_info=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_auto_id,'user_id'=>$this->user_id)));
        if(!isset($page_info[0])) exit();

        $page_id=$page_auto_id;// database id      
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id)),'','','',$start=NULL,$order_by='template_name ASC');        
        $poption=array();
        foreach ($postback_data as $key => $value) 
        {
            if($value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $poption[$value["postback_id"]]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        $data['poption']=$poption;
        
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array(); 
        $started_button_enabled = isset($page_info[0]["started_button_enabled"])?$page_info[0]["started_button_enabled"]:"0";
        $postback_id_list = $this->basic->get_data('messenger_bot_postback',array('where'=>array('user_id'=>$this->user_id,'page_id'=>$page_auto_id)));
        $data['postback_ids'] = $postback_id_list;
        $data['page_auto_id'] = $page_auto_id;
        $data['started_button_enabled'] = $started_button_enabled;
        $data['locale']=$this->sdk_locale();
        $this->_viewcontroller($data);
    }

    public function edit_persistent_menu_action()
    {
        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }
        if(!$_POST) exit();
        $post=$_POST;

        foreach ($post as $key => $value) 
        {
            $$key=$value;
        }
        if($this->basic->is_exist("messenger_bot_persistent_menu",array("page_id"=>$page_table_id,"locale"=>$locale,"id!="=>$auto_id)))
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("persistent menu is already exists for this locale.")));
            exit();
        }
        $menu=array();
        $postback_insert_data=array();
        $only_postback=array();
        $current_postbacks=json_decode($current_postbacks,true);
        $current_postbacks=array_map('strtoupper', $current_postbacks);
        for($i=1;$i<=$level1_limit;$i++)
        {
            $level_title_temp="text_with_buttons_text_".$i;
            $level_type_temp="text_with_button_type_".$i;
            if($$level_title_temp=="") continue; // form gets everything but we need only filled data
            if($$level_type_temp=="post_back") $$level_type_temp="postback";
            $menu[$i]=array
            (
                "title"=>$$level_title_temp,
                "type"=> $$level_type_temp
            );
            if($$level_type_temp=="postback")
            {
                $level_postback_temp="text_with_button_post_id_".$i;
                $level_postback_temp_data=isset($$level_postback_temp) ? $$level_postback_temp : '';
                // $$level_postback_temp=strtoupper($$level_postback_temp);
                $menu[$i]["payload"]=$level_postback_temp_data;
                $single_postback_insert_data = array();
                $single_postback_insert_data['user_id'] = $this->user_id;
                $single_postback_insert_data['postback_id'] = $level_postback_temp_data;
                $single_postback_insert_data['page_id'] = $page_table_id;
                $single_postback_insert_data['bot_name'] = '';
                if(!in_array(strtoupper($level_postback_temp_data), $current_postbacks))
                $postback_insert_data[] = $single_postback_insert_data; 
                $only_postback[]=$level_postback_temp_data;
            }
            else if($$level_type_temp=="web_url")
            {
                $level_web_url_temp="text_with_button_web_url_".$i;
                $menu[$i]["url"]=$$level_web_url_temp;
            }
            else
            {
                for($j=1;$j<=$level2_limit;$j++)
                {
                    $level2_title_temp="text_with_buttons_text_".$i."_".$j;
                    $level2_type_temp="text_with_button_type_".$i."_".$j;
                    if($$level2_title_temp=="") continue; // form gets everything but we need only filled data
                    if($$level2_type_temp=="post_back") $$level2_type_temp="postback";
                    $menu[$i]["call_to_actions"][$j]["title"]=$$level2_title_temp;
                    $menu[$i]["call_to_actions"][$j]["type"]=$$level2_type_temp;
                    if($$level2_type_temp=="postback")
                    {
                        $level2_postback_temp="text_with_button_post_id_".$i."_".$j;
                        $level2_postback_temp_data=isset($$level2_postback_temp) ? $$level2_postback_temp : '';
                        // $$level2_postback_temp=strtoupper($$level2_postback_temp);
                        $menu[$i]["call_to_actions"][$j]["payload"]=$level2_postback_temp_data;
                        $single_postback_insert_data = array();
                        $single_postback_insert_data['user_id'] = $this->user_id;
                        $single_postback_insert_data['postback_id'] = $level2_postback_temp_data;
                        $single_postback_insert_data['page_id'] = $page_table_id;
                        $single_postback_insert_data['bot_name'] = '';
                        if(!in_array(strtoupper($level2_postback_temp_data), $current_postbacks))
                        $postback_insert_data[] = $single_postback_insert_data; 
                        $only_postback[]=$level2_postback_temp_data;
                    }
                    else if($$level2_type_temp=="web_url")
                    {
                        $level2_web_url_temp="text_with_button_web_url_".$i."_".$j;
                        $menu[$i]["call_to_actions"][$j]["url"]=$$level2_web_url_temp;
                    }
                    else
                    {
                        for($k=1;$k<=$level3_limit;$k++)
                        {
                            $level3_title_temp="text_with_buttons_text_".$i."_".$j."_".$k;
                            $level3_type_temp="text_with_button_type_".$i."_".$j."_".$k;
                            if($$level3_title_temp=="") continue; // form gets everything but we need only filled data
                            if($$level3_type_temp=="post_back") $$level3_type_temp="postback";
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["title"]=$$level3_title_temp;
                            $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["type"]=$$level3_type_temp;
                            if($$level3_type_temp=="postback")
                            {
                                $level3_postback_temp="text_with_button_post_id_".$i."_".$j."_".$k;
                                $level3_postback_temp_data=isset($$level3_postback_temp) ? $$level3_postback_temp : '';
                                // $$level3_postback_temp=strtoupper($$level3_postback_temp);
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["payload"]=$level3_postback_temp_data;
                                $single_postback_insert_data = array();
                                $single_postback_insert_data['user_id'] = $this->user_id;
                                $single_postback_insert_data['postback_id'] = $level3_postback_temp_data;
                                $single_postback_insert_data['page_id'] = $page_table_id;
                                $single_postback_insert_data['bot_name'] = '';
                                if(!in_array(strtoupper($level3_postback_temp_data), $current_postbacks))
                                $postback_insert_data[] = $single_postback_insert_data; 
                                $only_postback[]=$level3_postback_temp_data;
                            }
                            else if($$level3_type_temp=="web_url")
                            {
                                $level3_web_url_temp="text_with_button_web_url_".$i."_".$j."_".$k;
                                $menu[$i]["call_to_actions"][$j]["call_to_actions"][$k]["url"]=$$level3_web_url_temp;
                            }
                        }
                    }
                }
            }
        }


        $menu_json_array=array();
        $menu_json_array["locale"]=$locale;
        $composer_input_disabled2='false';
        if($composer_input_disabled==='1') $composer_input_disabled2='true';
        $menu_json_array["composer_input_disabled"]=$composer_input_disabled2;
        $index=1;
        foreach ($menu as $key => $value) 
        {
           $menu_json_array["call_to_actions"][$index]=$value;
           $index++;
        }
        $menu_json=json_encode($menu_json_array); 
        $insert_data = array();       
        $insert_data['page_id'] = $page_table_id;
        $messenger_bot_user_info_id = $this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$page_table_id)),array("messenger_bot_user_info_id","page_access_token"));
        $page_access_token = $messenger_bot_user_info_id[0]['page_access_token'];
        $messenger_bot_user_info_id = $messenger_bot_user_info_id[0]["messenger_bot_user_info_id"];
        
        $this->db->trans_start();
        // if(!empty($postback_insert_data)) $this->db->insert_batch('messenger_bot_postback',$postback_insert_data);
        $this->basic->update_data("messenger_bot_persistent_menu",array("id"=>$auto_id,"user_id"=>$this->user_id),array("locale"=>$locale,"item_json"=>$menu_json,"composer_input_disabled"=>$composer_input_disabled,'poskback_id_json'=>json_encode($only_postback)));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
        else  
        {
            $this->session->set_flashdata('per_update_success',1);
            echo json_encode(array('status'=>'1','message'=>$this->lang->line("persistent menu has been updated successfully.")));
        }      
    }

    public function configuration()
    {
        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') {
           redirect('home/login_page', 'location');
        }
        
        $data['body'] = "edit_config";
        $data['page_title'] = $this->lang->line('general settings')." : ".$this->lang->line('messenger bot');
        $this->_viewcontroller($data);
    }

    public function edit_config()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }
        if ($_POST) 
        {
            $this->form_validation->set_rules('backup_mode',  '<b>'.$this->lang->line("Backup Mode").'</b>','trim');                
            $this->form_validation->set_rules('persistent_menu_copyright_text',  '<b>'.$this->lang->line("persistent menu copyright text").'</b>','trim');                
            $this->form_validation->set_rules('persistent_menu_copyright_url',  '<b>'.$this->lang->line("persistent menu copyright URL").'</b>','trim');  
            $this->form_validation->set_rules('has_manage_page_approval',  '<b>'.$this->lang->line("User login type").'</b>','trim');

            if($this->is_messenger_bot_analytics_exist) $this->form_validation->set_rules('has_read_insight_approval',  '<b>'.$this->lang->line("Enable Analytics").'</b>','trim');

            // go to config form page if validation wrong
            if ($this->form_validation->run() == false) 
            {
                return $this->configuration();
            } 
            else 
            {
                $backup_mode=addslashes(strip_tags($this->input->post('backup_mode', true)));
                $persistent_menu_copyright_text=addslashes(strip_tags($this->input->post('persistent_menu_copyright_text', true)));
                $persistent_menu_copyright_url=addslashes(strip_tags($this->input->post('persistent_menu_copyright_url', true)));
                $has_manage_page_approval=addslashes(strip_tags($this->input->post('has_manage_page_approval', true)));

                if($this->is_messenger_bot_analytics_exist)  $has_read_insight_approval=addslashes(strip_tags($this->input->post('has_read_insight_approval', true)));


                // writing application/config/my_config
                $app_my_config_data = "<?php ";
                $app_my_config_data.= "\n\$config['webhook_verify_token'] = '".$this->config->item('webhook_verify_token')."';\n";
                if($backup_mode == 'yes') $mode_to_write = 1;
                else $mode_to_write = 0;   
                $app_my_config_data.= "\$config['bot_backup_mode'] = '$mode_to_write';\n";
         
                if($persistent_menu_copyright_text!="")
                $app_my_config_data.= "\$config['persistent_menu_copyright_text'] = '$persistent_menu_copyright_text';\n";
                if($persistent_menu_copyright_url!="")
                $app_my_config_data.= "\$config['persistent_menu_copyright_url'] = '$persistent_menu_copyright_url';\n";
                $app_my_config_data.= "\$config['has_manage_page_approval'] = '$has_manage_page_approval';";

                if($this->is_messenger_bot_analytics_exist) $app_my_config_data.= "\n\$config['has_read_insight_approval'] = '$has_read_insight_approval';";


                file_put_contents(APPPATH.'modules/'.strtolower($this->router->fetch_class()).'/config/messenger_bot_config.php', $app_my_config_data, LOCK_EX);
                              
                $admin_info = $this->basic->get_data("users",array("where"=>array('user_type'=>'Admin')),array('id'));
                $admin_ids = array();
                foreach($admin_info as $value)
                {
                    array_push($admin_ids, $value['id']);
                }
                
                // Messenger Bot
                if($this->basic->is_exist("modules",$where=array('id'=>200)))
                {                    
                    $admin_app_info = $this->basic->get_data("messenger_bot_config",array("where_in"=>array("user_id"=>$admin_ids)),array("id"));
                    $admin_app_ids = array();
                    foreach($admin_app_info as $apps)
                    {
                        array_push($admin_app_ids, $apps['id']);
                    }
                    if($mode_to_write == 1)
                    {
                        if(!empty($admin_app_ids))
                        {
                            $this->db->where_in('messenger_bot_config_id', $admin_app_ids);
                            $this->db->where_not_in('user_id', $admin_ids);
                            $this->db->update("messenger_bot_user_info",array("need_to_delete"=>"1"));
                        }
                    }
                    else
                    {
                        if(!empty($admin_app_ids))
                        {
                            $this->db->where_in('messenger_bot_config_id', $admin_app_ids);
                            $this->db->where_not_in('user_id', $admin_ids);
                            $this->db->update("messenger_bot_user_info",array("need_to_delete"=>"0"));
                        }
                    }
                }
                // Messenger Bot
              
                $this->session->set_flashdata('success_message', 1);
                redirect('messenger_bot/configuration', 'location');
            }
        }
    }

    public function cron_job()
    {
        if($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location');
        
        $data['body'] = "cron_job";
        $data['page_title'] = 'cron job';
        $api_data=$this->basic->get_data("native_api",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));
        $data["api_key"]="";
        if(count($api_data)>0) $data["api_key"]=$api_data[0]["api_key"];
        if($this->is_demo=='1') $data["api_key"]='xxxxxxxxxxxxxxxxxxxxxxxxxx';
        $this->_viewcontroller($data);
    }

    function download_profile_pic($api_key)
    {    
        
       $this->api_key_check($api_key);
       $subscriber_info = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('is_image_download'=>'0', 'is_updated_name' => '1')),$select='',$join='',$limit=25);
        
        foreach($subscriber_info as $info){
        
            $profile_pic_url=$info['profile_pic'];
            $subscribe_id=$info['subscribe_id'];
            $subscribe_auto_id=$info['id'];


            $upload_path="upload/subscriber_pic"; 
            
            if(!file_exists($upload_path))
                mkdir($upload_path,0755);
                
            $user_pic_name=$upload_path."/".$subscribe_id.".png";
        
        
            $content= @file_get_contents($profile_pic_url);
            
            if($content===FALSE){
                
                $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscribe_auto_id),array("is_image_download"=>"1"));
                
            }
            else{
                file_put_contents($user_pic_name,$content);
                $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscribe_auto_id),array("is_image_download"=>"1","image_path"=>$user_pic_name));
            }
        }        
    
    }


    public function update_first_name_last_name($api_key)
    {
        
        $this->api_key_check($api_key);
        $subscriber_info = $this->basic->get_data('messenger_bot_subscriber',array('where'=>array('is_updated_name'=>'0')),$select='',$join='',$limit=100, '', 'last_name_update_time asc');
         
         foreach($subscriber_info as $info){
         
             $subscribe_id=$info['subscribe_id'];
             $subscribe_auto_id=$info['id'];
             $page_id = $info['page_id'];

             $messenger_bot_page_info = $this->basic->get_data('messenger_bot_page_info', array('where' => array('page_id' => $page_id, 'bot_enabled' => '1')));
             $messenger_bot_page_info = $messenger_bot_page_info[0];
             $access_token = $messenger_bot_page_info['page_access_token'];

             $user_info = $this->subscriber_info($access_token, $subscribe_id);

             if (!isset($user_info['error'])) {

                 $first_name = isset($user_info['first_name']) ? $user_info['first_name'] : "";
                 $last_name = isset($user_info['last_name']) ? $user_info['last_name'] : "";
                 $profile_pic = isset($user_info['profile_pic']) ? $user_info['profile_pic'] : "";

                 if ($first_name != "") {

                     $data = array(
                         'first_name' => $first_name,
                         'last_name' => $last_name,
                         'profile_pic' => $profile_pic,
                         'is_updated_name' => '1',
                         'last_name_update_time' => date('Y-m-d H:i:s')
                     );

                     $this->basic->update_data('messenger_bot_subscriber', array('id' => $subscribe_auto_id), $data);
                 }
                 else 
                    $this->basic->delete_data('messenger_bot_subscriber', array('id' => $subscribe_auto_id)); 

                
             }
             else 
                $this->basic->delete_data('messenger_bot_subscriber', array('id' => $subscribe_auto_id)); 
             
         }
    }

    private function api_key_check($api_key="")
    {
        $user_id="";
        if($api_key!="")
        {
            $explde_api_key=explode('-',$api_key);
            $user_id="";
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];
        }
        if($api_key=="")
        {        
            echo "API Key is required.";    
            exit();
        }
        if(!$this->basic->is_exist("native_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
           echo "API Key does not match with any user.";
           exit();
        }
        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "API Key does not match with any authentic user.";
            exit();
        }       
    }




    // -------------Tree view data functions-------------------------
    // private function set_nest($current_level=0)
    // {       
    //     if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return ""; 

    //     $output="";
    //     $isset="";
    //     $value="\$this->postback_array[\$key0]['child_postback'][\$key1]";
    //     for($times=2;$times<=$current_level;$times++) 
    //     { 
    //         $isset=$value."['child_postback']";
    //         $value.="['child_postback'][\$key".$times."]";      
    //     }
    //     $output.="
    //     if(isset({$isset}) && is_array({$isset}))
    //     foreach({$isset} as \$key".$current_level." => \$value".$current_level.") 
    //     {
    //         if(!is_array(\$value".$current_level.") && !in_array(\$value".$current_level.",\$this->postback_done))
    //         {
    //             array_push(\$this->postback_done,\$value".$current_level.");
    //             {$value}=isset(\$this->postback_array[\$value".$current_level."])?\$this->postback_array[\$value".$current_level."]:array();
    //         }
    //     }";
    //     return $output; 
    // }

    private function set_nest_easy($postback_array=array(),$get_started_level)
    {
        for ($loop_level=$get_started_level-1; $loop_level >=1 ; $loop_level--) 
        { 
            foreach ($postback_array as $key => $value) 
            {
                $level=$value['level'];
                if($level==$loop_level)
                {
                    if(isset($value['child_postback']) && is_array($value['child_postback']))
                    {
                        foreach ($value['child_postback'] as $key2 => $value2) 
                        {
                            $postback_array[$key]['child_postback'][$key2]=$postback_array[$value2];
                        }
                    }
                }

            }
        }

        foreach ($postback_array as $key => $value)
        {
            if($value['level']>1)
            unset($postback_array[$key]); // removing other  unnessary rows so that only nested postback stays 
        }

        return $postback_array;
    }

    private function get_nest($current_level=1)
    {       
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return ""; 
            $current_level_prev=$current_level-1;

            $condition="if(\$tempurl!='') \$templabel='<a title=\"'.\$tempbotname.' ['.\$tempostbackid.']\" href=\"'.\$tempurl.'\" target=\"_blank\">'.\$display.'</a>';
            else \$templabel=\$display;";

            $output="";
            $output.=" 
            \$get_started_tree.='<ul>'; ";
            $output.="
            // nested post back may have weburl,phone or email child and they are single element without child                
            if(!empty(\$value".$current_level_prev."['web_url'])) // has a web url as child, 0 index consists url
            {              
              foreach(\$value".$current_level_prev."['web_url'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li>
                    Web Url<br><a href=\"'.\$this->tree_security(\$tempuval).'\" target=\"_blank\">'.\$this->tree_security(\$tempuval).'</a>
                </li>';
              }
            }
            if(!empty(\$value".$current_level_prev."['phone_number'])) // has a phone as child
            {
              foreach(\$value".$current_level_prev."['phone_number'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li>Phone Number</li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['email'])) // has a email as child
            {
              foreach(\$value".$current_level_prev."['email'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li>Email</li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['location'])) // has a location as child
            {
              foreach(\$value".$current_level_prev."['location'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li>Location</li>';
              }
            }

            if(!empty(\$value".$current_level_prev."['call_us'])) // has a call_us as child
            {
              foreach(\$value".$current_level_prev."['call_us'] as \$tempukey => \$tempuval)
              {
                \$get_started_tree.= '
                <li>Call Us<br>'.\$this->tree_security(\$tempuval).'</li>';
              }
            }

            if(isset(\$value".$current_level_prev."['child_postback']))
            foreach (\$value".$current_level_prev."['child_postback'] as \$key".$current_level." => \$value".$current_level.")
            {                                    
                if(is_array(\$value".$current_level.")) // if have new child that does not appear in parent tree
                {
                    \$tempid=isset(\$value".$current_level."['id'])?\$value".$current_level."['id']:0;
                    \$tempis_template=isset(\$value".$current_level."['is_template'])?\$value".$current_level."['is_template']:'';
                    \$tempostbackid=isset(\$value".$current_level."['postback_id'])?\$this->tree_security(\$value".$current_level."['postback_id']):'';
                    \$tempbotname=isset(\$value".$current_level."['bot_name'])?\$this->tree_security(\$value".$current_level."['bot_name']):'';
                    
                    if(\$tempis_template=='1') \$tempurl=base_url('messenger_bot/edit_template/'.\$tempid); // it is template
                    else if(\$tempis_template=='0') \$tempurl=base_url('messenger_bot/edit_bot/'.\$tempid); // it is bot
                    else \$tempurl='';  

                    if(\$tempbotname!='') \$display=\$tempbotname.'<br><span style=\"color:#E05E00 !important\">'.\$tempostbackid.'</span>';
                    else \$display=\$tempostbackid;                  
                    
                    ".$condition."

                    \$get_started_tree.= '
                    <li>'.\$templabel;
                } 
                else // child already appear in parent tree
                {                    
                    if(isset(\$linear_postback_array[\$value".$current_level."])) 
                    {
                        \$tempid=isset(\$linear_postback_array[\$value".$current_level."]['id'])?\$linear_postback_array[\$value".$current_level."]['id']:0;
                        \$tempis_template=isset(\$linear_postback_array[\$value".$current_level."]['is_template'])?\$linear_postback_array[\$value".$current_level."]['is_template']:'';
                        \$tempostbackid=isset(\$linear_postback_array[\$value".$current_level."]['postback_id'])?\$this->tree_security(\$linear_postback_array[\$value".$current_level."]['postback_id']):'';
                        \$tempbotname=isset(\$linear_postback_array[\$value".$current_level."]['bot_name'])?\$this->tree_security(\$linear_postback_array[\$value".$current_level."]['bot_name']):'';

                        if(\$tempis_template=='1') \$tempurl=base_url('messenger_bot/edit_template/'.\$tempid); // it is template
                        else if(\$tempis_template=='0') \$tempurl=base_url('messenger_bot/edit_bot/'.\$tempid); // it is bot
                        else \$tempurl='';

                        if(\$tempbotname!='') \$display=\$tempbotname.'<br><span style=\"color:#E05E00 !important\">'.\$tempostbackid.'</span>';
                        else \$display=\$tempostbackid; 

                        if(\$tempbotname!='') \$display=\"<span style='color:#888 !important'>\".\$tempbotname.\"<br>\".\$tempostbackid.'</span>';
                        else \$display=\"<span style='color:#ccc !important'>\".\$tempostbackid.'</span>';   
                        
                         ".$condition."

                        \$get_started_tree.= '
                        <li>'.\$templabel;
                    }
                
                }";

        return $output;
    }

    

    private function get_child_info($messenger_bot_info,$page_table_id)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return array(); 
        foreach ($messenger_bot_info as $info) 
        {

            $message= $info['message'];
            $keyword_bot_id= $info['id'];
            $keywrods_list= $info['keywords'];
            $template_type=$info['template_type'];
            $this->postback_info[$keyword_bot_id]['keywrods_list']=$keywrods_list;


            /** Get all postback button id from json message **/

            $button_information= $this->get_button_information_from_json($message,$template_type);
            $matches[1]=isset($button_information['postback']) ? $button_information['postback'] : array();
            
            $web_url=isset($button_information['web_url']) ? $button_information['web_url'] : array();
            $phone_number=isset($button_information['phone_number']) ? $button_information['phone_number'] : array();
            $email=isset($button_information['email']) ? $button_information['email'] : array();
            $location=isset($button_information['location']) ? $button_information['location'] : array();
            $call_us=isset($button_information['call_us']) ? $button_information['call_us'] : array();

            $k=0;
            $level=0;

            do
            {

                $level++;
                $this->get_postback_info($matches[1],$page_table_id,$keyword_bot_id,$level);

                $matches=array();

                if(!isset($this->postback_info[$keyword_bot_id]['postback_info'])) break;

                foreach ($this->postback_info[$keyword_bot_id]['postback_info'] as $p_info) {

                    $child=$p_info['child_postback'];

                    if(empty($child)) continue;

                    foreach ($child as $child_postback) {
                        if(!isset($this->postback_info[$keyword_bot_id]['postback_info'][$child_postback])) 
                            $matches[1][]=$child_postback;
                    }
                    
                }

                 $k++;

                if($k==100) break;


            }
            while(!empty($matches[1])); 

            $this->postback_info[$keyword_bot_id]['web_url']= $web_url;
            $this->postback_info[$keyword_bot_id]['phone_number']= $phone_number;
            $this->postback_info[$keyword_bot_id]['email']= $email;
            $this->postback_info[$keyword_bot_id]['location']= $location;
            $this->postback_info[$keyword_bot_id]['call_us']= $call_us;

        }
    
        return $this->postback_info;

    }

    private function get_postback_info($matches,$page_table_id,$keyword_bot_id,$level)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return array();     
        foreach ($matches as $postback_match) 
        {

            $where['where'] = array('page_id'=> $page_table_id,'postback_id' =>$postback_match);
            /**Get BOT settings information from messenger_bot table as base table. **/
            $messenger_postback_info = $this->basic->get_data("messenger_bot",$where);

            $message= isset($messenger_postback_info[0]['message']) ? $messenger_postback_info[0]['message'] :"" ;

            $id= isset($messenger_postback_info[0]['id']) ? $messenger_postback_info[0]['id']:"";
            $is_template= isset($messenger_postback_info[0]['is_template']) ? $messenger_postback_info[0]['is_template']:"";
            $template_type= isset($messenger_postback_info[0]['template_type']) ? $messenger_postback_info[0]['template_type']:"";
            $bot_name= isset($messenger_postback_info[0]['bot_name']) ? $messenger_postback_info[0]['bot_name']:"";


            if($is_template=='1'){
                $postback_id_info=$this->basic->get_data('messenger_bot_postback',array('where'=>array('messenger_bot_table_id'=>$id,'is_template'=>'1')));
                $id= isset($postback_id_info[0]['id']) ? $postback_id_info[0]['id']:"";
            }          

            

            preg_match_all('#payload":"(.*?)"#si', $message, $matches);

            $button_information= $this->get_button_information_from_json($message,$template_type);
            $matches[1]=isset($button_information['postback']) ? $button_information['postback'] : array();

            $web_url= isset($button_information['web_url']) ? $button_information['web_url'] : array();
            $web_url=isset($button_information['web_url']) ? $button_information['web_url'] : array();
            $phone_number=isset($button_information['phone_number']) ? $button_information['phone_number'] : array();
            $email=isset($button_information['email']) ? $button_information['email'] : array();
            $location=isset($button_information['location']) ? $button_information['location'] : array();
            $call_us=isset($button_information['call_us']) ? $button_information['call_us'] : array();
        
            $this->postback_info[$keyword_bot_id]['postback_info'][$postback_match] = array("id"=>$id,"child_postback"=>$matches[1],'postback_id'=>$postback_match,"level"=>$level,'is_template'=>$is_template,"web_url"=>$web_url,
                "phone_number" =>$phone_number,
                "email"     =>$email,
                "location"  =>$location,
                'bot_name'  =>$bot_name,
                'call_us'   =>$call_us
                );
        }

        return $this->postback_info;
    }


    private function get_button_information_from_json($json_message,$template_type)
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(257, $this->module_access)) return array();

        $full_message_array = json_decode($json_message,true);
        $result = array();

        if(!isset($full_message_array[1]))
        {
          $full_message_array[1] = $full_message_array;
          $full_message_array[1]['message']['template_type'] = $template_type;
        }


        for($k=1;$k<=3;$k++)
        { 

          $full_message[$k] = isset($full_message_array[$k]['message']) ? $full_message_array[$k]['message'] : array();

          if(isset($full_message[$k]["template_type"]))
            $full_message[$k]["template_type"] = str_replace('_', ' ', $full_message[$k]["template_type"]);  

          for ($i=1; $i <=11 ; $i++) 
          {
            if(isset($full_message[$k]['quick_replies'][$i-1]['payload']))
              $result['postback'][] = (isset($full_message[$k]['quick_replies'][$i-1]['payload'])) ? $full_message[$k]['quick_replies'][$i-1]['payload']:"";
            if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_phone_number')
              $result['phone_number'][] = "user_phone_number";
            if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_email')
              $result['email'][] = "user_email";
            if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'location')
              $result['location'][] = "location";


            if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback')
              $result['postback'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']:"";
            if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url']))
              $result['web_url'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'] : "";
            if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number')
              $result['call_us'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number') ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload'] : "";
          }


          for ($j=1; $j <=5 ; $j++)
          {
            for ($i=1; $i <=3 ; $i++)
            {
              if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback')
                $result['postback'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']:"";
              if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url']))
                $result['web_url'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] : "";
              if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number')
                $result['call_us'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number') ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] : "";
            }
          }

        }

        return $result;
    }
    // -------------Tree view data functions-------------------------



    /**
     * ajax call, takes messenger_bot_subscriber table's id and then
     * update that rows data
     *     
     * @return json encoded array => operation type with message
     */
    public function userDetailsUpdate()
    {      
        /**
         * get table id from post value
         */
        $button_id = $this->input->post('button_id');
        $button_id = explode('-', $button_id);

        $table_id = $button_id[0];
        $client_id = $button_id[1];
        $page_id = $button_id[2];

        $response = array();

        /**
         * get page access token using page id from table facebook_rx_fb_page_info
         * then call the function for update data
         */
        $messenger_bot_page_info = $this->basic->get_data('messenger_bot_page_info', array('where' => array('page_id' => $page_id, 'bot_enabled' => '1')));
        $messenger_bot_page_info = $messenger_bot_page_info[0];

        $update_data = $this->subscriber_info($messenger_bot_page_info['page_access_token'],$client_id);

        // echo "<pre>";
        // print_r($update_data);
        // echo "<br></pre>";
        // print_r($button_id);
        // echo $client_id;
        // echo $page_id;
        // print_r($messenger_bot_page_info);
        
        if (!isset($update_data['error'])) {

            $first_name = (isset($update_data['first_name'])) ? $update_data['first_name'] : "";
            $last_name = (isset($update_data['last_name'])) ? $update_data['last_name'] : "";
            $user_name = $first_name." ".$last_name;
            
            if ($first_name != "") {

                $data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'profile_pic' => (isset($update_data['profile_pic'])) ? $update_data['profile_pic'] : "",
                    'is_updated_name' => '1',
                    'is_image_download' => '0',
                    'last_name_update_time' => date('Y-m-d H:i:s')
                ); 
            }
            else
                $data = array('last_name_update_time' => date('Y-m-d H:i:s'),'is_updated_name' => '1'); 

            $this->basic->update_data('messenger_bot_subscriber', array('id' => $table_id), $data);

            $response['type'] = 'success';
            $response['message'] = $this->lang->line("Successfully updated.");
            $response['user_name'] = $user_name;
            $response['first_name'] = $first_name;
            $response['last_name'] = $last_name;
        }
        else {

            $data = array('last_name_update_time' => date('Y-m-d H:i:s'),'is_updated_name' => '1'); 
            $this->basic->update_data('messenger_bot_subscriber', array('id' => $table_id), $data);

            $response['type'] = 'error';
            $response['message'] = $this->lang->line($update_data['error']['message']);
        }

        echo json_encode($response);
    }


public function thirdparty_webhook_trigger($page_id="",$subscriber_id="",$trigger='trigger_email',$postback_id=""){

        if($trigger=='trigger_postback')
            $trigger="trigger_postback_".$postback_id;

        $where_simple['messenger_bot_thirdparty_webhook.page_id'] = $page_id;
        $where_simple['messenger_bot_thirdparty_webhook_trigger.trigger_option'] = $trigger;
        $where=array('where'=>$where_simple);
       
        /**Get all connector webhook information**/

        $join = array('messenger_bot_thirdparty_webhook_trigger'=>"
            messenger_bot_thirdparty_webhook.id=messenger_bot_thirdparty_webhook_trigger.webhook_id,left");

        $webhook_connector_info=$this->basic->get_data('messenger_bot_thirdparty_webhook', $where, $select='', $join, $limit='', $start='');

        if(empty($webhook_connector_info)) exit; 

        /** Get subscriber information  **/


        $where_simple=array();
        $where_simple['messenger_bot_subscriber.subscribe_id'] =$subscriber_id ;
        $where_simple['messenger_bot_subscriber.page_id'] = "$page_id";
        $where=array('where'=>$where_simple);

        $join = array('messenger_bot_quick_reply_email'=>"
            messenger_bot_subscriber.subscribe_id=messenger_bot_quick_reply_email.fb_user_id,left");

        $subscriber_info=$this->basic->get_data('messenger_bot_subscriber', $where, $select='', $join, $limit='', $start='');

        /**Get subscriber Labels name from labels id***/

        $label_ids = $subscriber_info_rearrange['contact_group_id']=isset($subscriber_info[0]['contact_group_id']) ? $subscriber_info[0]['contact_group_id']:"";

        $label_ids_array = explode(',',$label_ids);
        $label_ids_array=array_filter($label_ids_array);

        $labels_name="";

        if(!empty($label_ids_array)){

            $where=array("where_in"=>array("id"=>$label_ids_array));

            $label_info = $this->basic->get_data("messenger_bot_broadcast_contact_group",$where);

            foreach($label_info as $value)
            {
                $labels_name.=",".$value['group_name'];
            }
        }

        $labels_name =trim($labels_name,",");

        foreach ($webhook_connector_info as $webhook_value) {
        
            $webhook_url = isset($webhook_value['webhook_url']) ? $webhook_value['webhook_url']:"";
            $webhook_id=isset($webhook_value['webhook_id']) ? $webhook_value['webhook_id']:"";
            $post_variable = isset($webhook_value['variable_post']) ? $webhook_value['variable_post']:"";
            $post_variable= explode(',',$post_variable);
            $post_variable=array_filter($post_variable);

            /**Making the variable for post/send ***/

            $post_info=array();

            foreach ($post_variable as $variable_info) {

                if($variable_info=='psid')
                    $post_info[$variable_info]= isset($subscriber_info[0]['subscribe_id']) ? $subscriber_info[0]['subscribe_id']:"";
                else if ($variable_info=='labels')
                    $post_info[$variable_info]= $labels_name;
                else if($variable_info=='page_name')
                    $post_info[$variable_info]= isset($webhook_connector_info[0]['page_name']) ? $webhook_connector_info[0]['page_name']:"";
                else if($variable_info=='postbackid')
                     $post_info[$variable_info]= $postback_id;
                else
                    $post_info[$variable_info] = isset($subscriber_info[0][$variable_info]) ? $subscriber_info[0][$variable_info]:"";

            }



            /***    Send/Post Information to webhook url ***/

            $post_info=json_encode($post_info);

            $curl_response=$this->curl_send_data($webhook_url,$post_info);
            
            $curl_http_code= $curl_response['http_code'];
            $curl_error= $curl_response['curl_error'];

            /***Insert into Activity table**/

            $insert_data=array();
            $insert_data['http_code'] = $curl_http_code; 
            $insert_data['curl_error'] = $curl_error; 
            $insert_data['webhook_id'] = $webhook_id; 
            $insert_data['post_time'] = date('Y-m-d H:i:s'); 
            $insert_data['post_data'] = $post_info; 

            $this->basic->insert_data('messenger_bot_thirdparty_webhook_activity',$insert_data);

            /**update messenger_bot_thirdparty_webhook table for last_trigger_time **/
            $update_data_last_trigger['last_trigger_time'] = $insert_data['post_time'];
            $this->basic->update_data("messenger_bot_thirdparty_webhook",array('id'=>$webhook_id),$update_data_last_trigger);



            /***Delete last activity except recent 10 ***/

            $lastest_activity= $this->basic->get_data("messenger_bot_thirdparty_webhook_activity",$where=array('id'=>$webhook_id),$select='',$join='',$limit='10',$start=0,$order_by='id Desc');

            foreach ($lastest_activity as $last_activity_info) {
                $last_activity_ids[]=$last_activity_info['id'];
            }

            $this->db->where_not_in('id', $last_activity_ids);
            $this->db->delete('messenger_bot_thirdparty_webhook_activity'); 
        }
    }

    protected function curl_send_data($webhook_url,$post_info){

        $ch = curl_init();
        $headers = array('Accept: application/json', 'Content-Type: application/json');

        curl_setopt($ch, CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_info); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
       // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
        $st=curl_exec($ch); 

        $curl_information =  curl_getinfo($ch);
        $curl_error="";
        if($curl_information['http_code']!='200'){
            $curl_error = curl_error($ch);
        }

        $response['http_code']=$curl_information['http_code'];
        $response['curl_error']=$curl_error;

        return $response; 

    }
    

}
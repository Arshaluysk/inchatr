<?php
/*
Addon Name: Drip Messaging
Unique Name: drip_messaging
Module ID: 218
Project ID: 18
Addon URI: http://getfbinboxer.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 2.1.1
Description: Send sequence message to subscribers
*/

require_once("application/controllers/Home.php"); // loading home controller

class Drip_messaging extends Home
{
	public $addon_data=array();    
    public $is_botinboxer_exist=false;
    public $is_engagement_exist=false;
    public function __construct()
    {
        parent::__construct();
        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $addondata=$this->get_addon_data($addon_path); 
        $this->member_validity();
        $this->addon_data=$addondata;

        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it

        $this->is_botinboxer_exist=$this->botinboxer_exist();
        $this->is_engagement_exist=$this->engagement_exist();
        $function_name=$this->uri->segment(2);

        if(!$this->is_botinboxer_exist && ($function_name=="eligible_pages" || $function_name=="messaging_report" || $function_name=="cron_job")) 
        {           
            if($this->session->userdata('user_type') == 'Member') echo file_get_contents('application/modules/drip_messaging/views/access_forbidden.php');
            if($this->session->userdata('user_type') == 'Admin')  echo file_get_contents('application/modules/drip_messaging/views/access_forbidden_admin.php');
            exit();
        }

        if($function_name!="drip_messaging_cron")
        {
          // all addon must be login protected
          //------------------------------------------------------------------------------------------
          if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');          

          // if you want the addon to be accessed by admin and member who has permission to this addon        
           if($this->session->userdata('user_type') != 'Admin' && !in_array(218,$this->module_access))
           {
                redirect('home/login_page', 'location');
                exit();
           }
        }
       
    }

    public function botinboxer_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->app_product_id==15) return true;
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>3))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(200,$this->module_access)) return true;
        return false;
    }

    public function engagement_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>17))) return true;
        if($this->session->userdata('user_type') == 'Member' && count(array_intersect($this->module_access, array(213,214,215,216,217))) > 0 ) return true;
        return false;
    }

    public function broadcastser_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>16))) return true;
        if($this->session->userdata('user_type') == 'Member' && count(array_intersect($this->module_access, array(210,211,212))) > 0 ) return true;
        return false;
    }


    public function index()
    {
        $this->eligible_pages();
    }

    public function eligible_pages()
    {
        $data['body'] = 'eligible_page_list';
        $data['page_title'] = $this->lang->line('Drip Messaging Eligible Pages');  

        $table_name = "messenger_bot_page_info";
        $where['where'] = array('user_id' => $this->user_id,"bot_enabled"=>'1');
        $page_info = $this->basic->get_data($table_name,$where,'','','','','page_name asc');

        $table_name = "messenger_bot_subscriber";
        $where['where'] = array('user_id' => $this->user_id);
        $sub_count = $this->basic->get_data($table_name,$where,'messenger_bot_subscriber.*,count(id) as sub_count','','','','','page_id');
        $sub_count=array_column($sub_count,'sub_count','page_id');

        $len_page_info = count($page_info); 
        $data['page_info'] = $page_info;        
        $data['sub_count'] = $sub_count;     

        $unsubscribe_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("template_for"=>"unsubscribe","user_id"=>$this->user_id)));
        $resubscribe_info = $this->basic->get_data("messenger_bot_postback",array("where"=>array("template_for"=>"resubscribe","user_id"=>$this->user_id)));

        foreach ($unsubscribe_info as $key => $value) 
        {
            $data["unsubscribe_info"][$value["page_id"]]=$value["id"];
        }

        foreach ($resubscribe_info as $key => $value) 
        {
            $data["resubscribe_info"][$value["page_id"]]=$value["id"];
        }

        $this->_viewcontroller($data);
    }


    public function check_review_status()
    {
        if(!$_POST) exit();
        $auto_id=$this->input->post('auto_id'); // database id

        $table_name = "messenger_bot_page_info";
        $where['where'] = array('user_id' => $this->user_id,"id"=>$auto_id);
        $page_info = $this->basic->get_data($table_name,$where);

        $page_id=isset($page_info[0]['page_id']) ? $page_info[0]['page_id'] : "";
        $access_token=isset($page_info[0]['page_access_token']) ? $page_info[0]['page_access_token'] : "";
        if($access_token=='')
        {
            echo json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
            exit();
        }
        $this->load->library('drip_messaging_login');
        $get_page_review_status=$this->drip_messaging_login->get_page_review_status($access_token);

        $review_status=isset($get_page_review_status["data"][0]["status"]) ? strtoupper($get_page_review_status["data"][0]["status"]) : "NOT SUBMITTED";
        if($review_status=="") $review_status="NOT SUBMITTED";

        $this->basic->update_data("messenger_bot_page_info",array("id"=>$auto_id,"user_id"=>$this->user_id),array("review_status"=>$review_status,"review_status_last_checked"=>date("Y-m-d H:i:s")));


        if($this->broadcastser_exist())
        {
        	$existing_labels=$this->drip_messaging_login->retrieve_label($access_token);
	        if(isset($existing_labels['error']['message'])) $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$existing_labels["error"]["message"];

	        $user_id=$this->user_id;
	        $group_name="Unsubscribe";
	        $group_name2="SystemInvisible01";
	        
	        if(isset($existing_labels["data"]))
	        foreach ($existing_labels["data"] as $key => $value) 
	        {
	            $existng_name=$value['name'];
	            $existng_id=$value['id'];

	            $unsbscribed='0';
	            if($existng_name==$group_name) $unsbscribed='1';

	            $is_invisible='0';
	            if($existng_name==$group_name2) $is_invisible='1';

	            $sql="INSERT IGNORE INTO messenger_bot_broadcast_contact_group(page_id,group_name,user_id,label_id,unsubscribe,invisible) VALUES('$auto_id','$existng_name','$user_id','$existng_id','$unsbscribed','$is_invisible')";
	            $this->basic->execute_complex_query($sql);
	        }

	        $errormessage="";
	        if(!$this->basic->is_exist("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"unsubscribe"=>"1")))
	        {
	            $response=$this->drip_messaging_login->create_label($access_token,$group_name);
	            $label_id=isset($response['id']) ? $response['id'] : "";

                $errormessage=isset($response["error"]["error_user_msg"])?$response["error"]["error_user_msg"]:$response["error"]["message"];
	            
	            if($label_id=="") 
	            $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$errormessage;
	            else $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"group_name"=>$group_name,"user_id"=>$this->user_id,"label_id"=>$label_id,"deleted"=>"0","unsubscribe"=>"1"));
	        }

	        if(!$this->basic->is_exist("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"invisible"=>"1")))
	        {            
	            $response=$this->drip_messaging_login->create_label($access_token,$group_name2);
	            $label_id=isset($response['id']) ? $response['id'] : "";

                $errormessage=isset($response["error"]["error_user_msg"])?$response["error"]["error_user_msg"]:$response["error"]["message"];
	            
	            if($label_id=="") 
	            $error=$this->lang->line("During the review status check process system also tries to create default unsubscribe label and retrieve the existing labels as well. We got this error : ")." ".$errormessage;
	            else $this->basic->insert_data("messenger_bot_broadcast_contact_group",array("page_id"=>$auto_id,"group_name"=>$group_name2,"user_id"=>$this->user_id,"label_id"=>$label_id,"deleted"=>"0","unsubscribe"=>"0","invisible"=>"1"));
	        }	       

	            
	       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"UNSUBSCRIBE_QUICK_BOXER","page_id"=>$auto_id)))
	       {
	            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template) VALUES
	            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"You have been successfully unsubscribed from our list. It sad to see you go. It is not the same without you ! You can join back by clicking the button below.","buttons":[{"type":"postback","payload":"RESUBSCRIBE_QUICK_BOXER","title":"Resubscribe"}]}}}}}\', "", "", "", "", "", "1", "UNSUBSCRIBE BOT", "UNSUBSCRIBE_QUICK_BOXER", "", "1");';
	            $this->db->query($sql);
	            $insert_id=$this->db->insert_id();
	            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for) VALUES
	            ("'.$user_id.'","UNSUBSCRIBE_QUICK_BOXER","'.$auto_id.'","0","1","'.$insert_id.'","UNSUBSCRIBE BOT","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"You have been successfully unsubscribed from our list. It sad to see you go. It is not the same without you ! You can join back by clicking the button below.","buttons":[{"type":"postback","payload":"RESUBSCRIBE_QUICK_BOXER","title":"Resubscribe"}]}}}}}\',"UNSUBSCRIBE TEMPLATE","unsubscribe")';
	            $this->db->query($sql);
	       }

	       if(!$this->basic->is_exist("messenger_bot",array("postback_id"=>"RESUBSCRIBE_QUICK_BOXER","page_id"=>$auto_id)))
	       {
	            $sql='INSERT INTO messenger_bot (user_id,page_id,fb_page_id,template_type,bot_type,keyword_type,keywords,message,buttons,images,audio,video,file,status,bot_name,postback_id,last_replied_at,is_template) VALUES
	            ("'.$user_id.'", "'.$auto_id.'", "'.$page_id.'", "text", "generic", "post-back","", \'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Welcome back ! We have not seen you for a while. You will no longer miss our important updates.","buttons":[{"type":"postback","payload":"UNSUBSCRIBE_QUICK_BOXER","title":"Unsubscribe"}]}}}}}\', "", "", "", "", "", "1", "RESUBSCRIBE BOT", "RESUBSCRIBE_QUICK_BOXER", "", "1");';
	            $this->db->query($sql);
	            $insert_id=$this->db->insert_id();
	            $sql='INSERT INTO messenger_bot_postback(user_id,postback_id,page_id,use_status,status,messenger_bot_table_id,bot_name,is_template,template_jsoncode,template_name,template_for) VALUES
	            ("'.$user_id.'","RESUBSCRIBE_QUICK_BOXER","'.$auto_id.'","0","1","'.$insert_id.'","RESUBSCRIBE BOT","1",\'{"1":{"recipient":{"id":"replace_id"},"message":{"template_type":"text_with_buttons","attachment":{"type":"template","payload":{"template_type":"button","text":"Welcome back ! We have not seen you for a while. You will no longer miss our important updates.","buttons":[{"type":"postback","payload":"UNSUBSCRIBE_QUICK_BOXER","title":"Unsubscribe"}]}}}}}\',"RESUBSCRIBE TEMPLATE","resubscribe")';
	            $this->db->query($sql);
	       }
	    }

       if(isset($error)) echo json_encode(array('status'=>'0','message'=>$error));
       else echo json_encode(array('status'=>'1','success'=>$review_status));
    }

    public function user_details_modal_bot()
    {
        if(!$_POST) exit();
        if (empty($_POST['auto_id'])) exit();
        $get_drip_type=$this->get_drip_type();

        $user_id = $this->user_id;
        $page_id = $this->input->post('auto_id'); // fb page id

        $table_name = "messenger_bot_subscriber";
        $where['where'] = array('messenger_bot_subscriber.user_id' => $user_id, 'messenger_bot_subscriber.page_id' => $page_id);
        $select=array("messenger_bot_subscriber.*","messenger_bot_drip_campaign.campaign_name","messenger_bot_drip_campaign.drip_type");
        $join=array('messenger_bot_drip_campaign'=>"messenger_bot_subscriber.messenger_bot_drip_campaign_id=messenger_bot_drip_campaign.id,left");
        $one_page_user_details = $this->basic->get_data($table_name,$where,$select,$join,$limit='',$start=NULL,$order_by='messenger_bot_subscriber.first_name desc');

        $pageinfo=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("page_id"=>$page_id,"user_id"=>$this->user_id,"bot_enabled"=>"1")));
        $page_auto_id=isset($pageinfo[0]["id"])?$pageinfo[0]["id"]:0;

        $html = '<script>
                    $j(document).ready(function() {
                        $(".table-responsive").mCustomScrollbar({
                            autoHideScrollbar:true,
                            theme:"3d-dark",          
                            axis: "x"
                        });  
                        $("#user_data_for_inbox").DataTable();
                    }); 
                 </script>';
        $html .= "
            <div class='table-responsive'>
            <table id='user_data_for_inbox' class='table table-striped table-bordered nowrap' cellspacing='0' width='100%''>
            <thead>
                <tr>
                    <th class='text-center'>".$this->lang->line("user name")."</th>
                    <th class='text-center'>".$this->lang->line("Subscriber ID")."</th>
                    <th class='text-center'>".$this->lang->line("Subscribed at")."</th>
                    <th class='text-center'>".$this->lang->line("Last Sent")."</th>
                    <th class='text-center'>".$this->lang->line("Assign drip campaign")."</th>
                    <th>".$this->lang->line("campaign")."</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($one_page_user_details as $one_user) 
        {
            $btn_id=$one_user['id'];
            $dis_class="";
            $lastsent="";

            if($one_user['messenger_bot_drip_last_sent_at']!="0000-00-00 00:00:00")
            {
                if($one_user['messenger_bot_drip_last_completed_day']!=0) $lastsent.=$this->lang->line("day").'-'.$one_user['messenger_bot_drip_last_completed_day']." : ";
                $lastsent.=date("jS M, y H:i:s",strtotime($one_user['messenger_bot_drip_last_sent_at']));
            }
            else $lastsent='<i class="fa fa-remove"></i>';

            if($one_user['messenger_bot_drip_campaign_id']==0) 
            {
                $dis_class='disabled';
                $lastsent='<i class="fa fa-remove"></i>';
            }

            if(isset($get_drip_type[$one_user['drip_type']]))
            $drip_lang="<a target='_BLANK' class='{$dis_class}' href='".base_url('drip_messaging/edit_campaign/'.$one_user['messenger_bot_drip_campaign_id']).'/'.$page_auto_id."'><i class='fa fa-hand-o-up'></i> ".$one_user['campaign_name']." (".$this->lang->line($get_drip_type[$one_user['drip_type']]).")</a>";
            else $drip_lang=$this->lang->line("under no campaign");

            $ass_class='';
            if($one_user['messenger_bot_drip_processing_status']=='1') $ass_class='disabled';
            $assign_cam="<a class='btn btn-primary assign_campaign {$ass_class}' href='#' data-id='".$btn_id."'><i class='fa fa-plus-circle'></i> ".$this->lang->line('Assign')."</a>";
            
            $html .= "<tr>
                        <td>".$one_user['first_name']." ".$one_user['last_name']."</td>
                        <td class='text-center'>".$one_user['subscribe_id']."</td>
                        <td class='text-center'>".date("jS M, y H:i:s",strtotime($one_user['subscribed_at']))."</td>
                        <td class='text-center'>".$lastsent."</td>
                        <td class='text-center'>".$assign_cam."</td>
                        <td>".$drip_lang."</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody>
                </table></table>";
        
        echo $html;
    }

    public function assign_campaign_form()
    {
    	if(!$_POST) exit();
        $subscribe_auto_id = $this->input->post("subscribe_auto_id");

        $subscriber_data=$this->basic->get_data("messenger_bot_subscriber",array("where"=>array("id"=>$subscribe_auto_id,"messenger_bot_drip_processing_status"=>"0")));
        if(!isset($subscriber_data[0]))
        {
        	echo "<div class='alert alert-danger text-center'>".$this->lang->line("something went wrong, please try again.")."</div>";
        	exit();
        }

        $fb_page_id=$subscriber_data[0]['page_id'];
        $current_campaign=$subscriber_data[0]['messenger_bot_drip_campaign_id'];

        $page_data=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("page_id"=>$fb_page_id,"bot_enabled"=>"1")));
        if(!isset($page_data[0]))
        {
        	echo "<div class='alert alert-danger text-center'>".$this->lang->line("something went wrong, please try again.")."</div>";
        	exit();
        }
        $page_auto_id=$page_data[0]['id'];

        $campaign_data=$this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("page_id"=>$page_auto_id)),$select='',$join='',$limit='',$start=NULL,$order_by='campaign_name ASC');
        $drip_types=$this->get_drip_type();

        $option=array();
        $option['0']=$this->lang->line('Choose drip campaign');
        foreach ($campaign_data as $key => $value) 
        {
        	$option[$value['id']]="";
        	if($value['campaign_name']!="") $option[$value['id']].=$value['campaign_name']." : ";
        	$option[$value['id']].=$drip_types[$value['drip_type']]." [".date("jS M, y H:i:s",strtotime($value['created_at']))."]";
        }
        echo '<input type="hidden" id="hidden_subscriberauto_id" value="'.$subscribe_auto_id.'">';
        echo '<label>'.$this->lang->line("Choose drip campaign").'</label><br>';
        echo form_dropdown('assign_campaign_id', $option, $current_campaign,'style="width:70%" class="form-control inline" id="assign_campaign_id"');
        echo "<a id='assign_confirm' class='inline btn btn-warning btn-lg' >".$this->lang->line('Set Campaign')."</a>";
        echo '<script> $j("document").ready(function(){$("#assign_campaign_id").select2();});</script>';

    }

    public function assign_confirm()
    {
    	if(!$_POST) exit();
        $subscribe_auto_id = $this->input->post("hidden_subscriberauto_id");
        $campaign_id = $this->input->post("assign_campaign_id");

        $update_data=array
        (
        	"messenger_bot_drip_campaign_id"=>$campaign_id,
        	"messenger_bot_drip_last_completed_day"=>"0",
        	"messenger_bot_drip_is_toatally_complete"=>"0",
        	"messenger_bot_drip_last_sent_at"=>"0000-00-00 00:00:00",
        	"messenger_bot_drip_initial_date"=>date('Y-m-d H:i:s'),
        	"last_processing_started_at"=>"0000-00-00 00:00:00",
        	"messenger_bot_drip_processing_status"=>"0"
        );

        if($this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscribe_auto_id,"user_id"=>$this->user_id),$update_data))
        echo json_encode(array('status'=>'1','message'=>$this->lang->line("Drip campaign has been assigned successfully for this subscriber.")));
        else json_encode(array('status'=>'0','message'=>$this->lang->line("something went wrong, please try again.")));
    }

    public function get_campaign_report()
    {
        if(!$_POST) exit();
        $id = $this->input->post("campaign_id");

        $campaign_data = $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $message_content = isset($campaign_data[0]["message_content"]) ? json_decode($campaign_data[0]["message_content"],true) : array();
        $campaign_name  = isset($campaign_data[0]["campaign_name"]) ? $campaign_data[0]["campaign_name"] : "";

        $report_data = $this->basic->get_data("messenger_bot_drip_report",array("where"=>array("messenger_bot_drip_campaign_id"=>$id,"user_id"=>$this->user_id)),'','','',NULL,'id DESC');

        $template_ids=array_values($message_content);
        $template_ids=array_unique($template_ids);
        $template_data=$this->basic->get_data("messenger_bot_postback",array("where_in"=>array("id"=>$template_ids)));
        $template_data_formatted=array();
        foreach ($template_data as $key => $value) 
        {
            $template_data_formatted[$value['id']]['id']=$value['id'];
            $template_data_formatted[$value['id']]['name']=$value['template_name'];
            $template_data_formatted[$value['id']]['link']= base_url('/messenger_bot/edit_template/'.$value['id']);
        }
        
        // subscriber count of this campaign
        $sql=$this->db->query("SELECT count(id) as subscriber_count FROM messenger_bot_subscriber WHERE messenger_bot_drip_campaign_id=".$id." AND user_id=".$this->user_id);
        $subscriber_data=$sql->result_array();
        $total_subscriber_count=isset($subscriber_data[0]['subscriber_count'])?$subscriber_data[0]['subscriber_count']:0;

        // assosiative array of days report
        $report_data_formatted=array();
        foreach ($report_data as $key => $value) 
        {
            $report_data_formatted[$value['last_completed_day']][]=$value;
        }
        
        // echo "<pre>";print_r($report_data_formatted);echo "</pre>";
        
        $report_data_stat=array(); // day-wise sent/delivered/opened/subsciber 
        $total_report_data_stat=array('sent'=>0,'delivered'=>0,'opened'=>0,'subscribers'=>$total_subscriber_count); // combined report stat of all days
        foreach ($report_data_formatted as $key => $value)
        {
            foreach ($value as $key2 => $value2) 
            {
               if(!isset($report_data_stat[$key]['sent'])) $report_data_stat[$key]['sent']=0;
               if(!isset($report_data_stat[$key]['delivered'])) $report_data_stat[$key]['delivered']=0; 
               if(!isset($report_data_stat[$key]['opened'])) $report_data_stat[$key]['opened']=0; 

               if($value2['is_sent']=='1') $report_data_stat[$key]['sent']++;
               if($value2['is_delivered']=='1') $report_data_stat[$key]['delivered']++;
               if($value2['is_opened']=='1') $report_data_stat[$key]['opened']++;
            }
            
            $report_data_stat[$key]['subscribers']=isset($report_data_formatted[$key])?count($report_data_formatted[$key]):0;

            $total_report_data_stat['sent']+=$report_data_stat[$key]['sent'];
            $total_report_data_stat['delivered']+=$report_data_stat[$key]['delivered'];
            $total_report_data_stat['opened']+=$report_data_stat[$key]['opened'];
        }

        $successfully_sent=isset($total_report_data_stat['sent'])?$total_report_data_stat['sent']:0;
        $successfully_delivered=isset($total_report_data_stat['delivered'])?$total_report_data_stat['delivered']:0;
        $successfully_opened=isset($total_report_data_stat['opened'])?$total_report_data_stat['opened']:0;
        
        if($successfully_delivered==0 || $successfully_sent==0) $delivery_rate=0;
        else $delivery_rate=($successfully_delivered/$successfully_sent)*100;

        if($successfully_opened==0 || $successfully_sent==0) $open_rate=0;
        else $open_rate=($successfully_opened/$successfully_sent)*100;

        echo "<h5 class='text-center'>".$this->lang->line('campaign name')." : ".$campaign_name."</h5><br>";

        echo '<div class="row">
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="background:#fff;">
                  <span class="info-box-icon bg-blue" style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-user blue"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">'.$this->lang->line("targeted subscribers").'</span>
                    <span class="info-box-number">'.$total_report_data_stat['subscribers'].'</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="background:#fff;">
                  <span class="info-box-icon bg-yellow" style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-send orange"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">'.$this->lang->line("total sent").'</span>
                    <span class="info-box-number">'.$total_report_data_stat['sent'].'</span>
                  </div>
                </div>
              </div>

              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="background:#fff;">
                  <span class="info-box-icon bg-green" style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-check-circle green"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">'.$this->lang->line("total delivered").' ('.round($delivery_rate).'%)</span>
                    <span class="info-box-number">'.$total_report_data_stat['delivered'].' </span>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box" style="background:#fff;"total >
                  <span class="info-box-icon bg-aqua" style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-eye blue2"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">'.$this->lang->line("opened").' ('.round($open_rate).'%)</span>
                    <span class="info-box-number">'.$total_report_data_stat['opened'].'</span>
                  </div>
                </div>
              </div>
            </div>';

        echo '<script>
            $j(document).ready(function() {
                $(".table-responsive").mCustomScrollbar({
                    autoHideScrollbar:true,
                    theme:"3d-dark",          
                    axis: "x"
                });   
            });
         </script>';

        echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
        $i=0;
        foreach ($message_content as $key => $value) 
        {
            $i++;
            $in='';
            if($i==1) $in='in';  

            $temp_subscribers=isset($report_data_stat[$key]['subscribers'])?$report_data_stat[$key]['subscribers']:0;
            $temp_sent=isset($report_data_stat[$key]['sent'])?$report_data_stat[$key]['sent']:0;
            $temp_delivered=isset($report_data_stat[$key]['delivered'])?$report_data_stat[$key]['delivered']:0;
            $temp_opened=isset($report_data_stat[$key]['opened'])?$report_data_stat[$key]['opened']:0;

            if($temp_delivered==0 || $temp_sent==0) $temp_delivery_rate=0;
            else $temp_delivery_rate=($temp_delivered/$temp_sent)*100;

            if($temp_opened==0 || $temp_sent==0) $temp_open_rate=0;
            else $temp_open_rate=($temp_opened/$temp_sent)*100;            

            echo'<div class="panel panel-default" style="border:none">
                <div class="panel-heading" role="tab" id="heading'.$key.'" style="margin:0;padding:10px !important;border-radius:5px 5px 0 0 !important;border:1px solid #ccc;">
                  <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$key.'" aria-expanded="true" aria-controls="collapse'.$key.'">
                      <h3 style="margin:0;font-size:15px;"><i class="fa fa-calendar"></i> '.$this->lang->line("day").'-'.$key.'</h3>
                    </a>
                  </h4>
                </div>
                <div id="collapse'.$key.'" class="panel-collapse collapse '.$in.'" role="tabpanel" aria-labelledby="heading'.$key.'">
                  <div class="panel-body" style="padding:20px;border-radius:0 0 5px 5px !important;border-top:none;">';

                  echo "<h4 class='text-center'>".$this->lang->line('template used')." : <a target='_BLANK' href='".$template_data_formatted[$value]['link']."'>".$template_data_formatted[$value]['name']."</a></h4><br>";

                  echo '<div class="row">
                          <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box" style="background:#fff;">
                              <span class="info-box-icon bg-blue"  style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-user blue"></i></span>

                              <div class="info-box-content">
                                <span class="info-box-text">'.$this->lang->line("subscriber count").'</span>
                                <span class="info-box-number">'.$temp_subscribers.'</span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box" style="background:#fff;">
                              <span class="info-box-icon bg-yellow" style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-send orange"></i></span>

                              <div class="info-box-content">
                                <span class="info-box-text">'.$this->lang->line("sent").'</span>
                                <span class="info-box-number">'.$temp_sent.'</span>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box" style="background:#fff;">
                              <span class="info-box-icon bg-green" style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-check-circle green"></i></span>

                              <div class="info-box-content">
                                <span class="info-box-text">'.$this->lang->line("delivered").' ('.round($temp_delivery_rate).'%)</span>
                                <span class="info-box-number">'.$temp_delivered.' </span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box" style="background:#fff;"> 
                              <span class="info-box-icon bg-aqua" style="background:#fff !important;border-right:1px solid #eee;"><i class="fa fa-eye blue2"></i></span>

                              <div class="info-box-content">
                                <span class="info-box-text">'.$this->lang->line("opened").' ('.round($temp_open_rate).'%)</span>
                                <span class="info-box-number">'.$temp_opened.'</span>
                              </div>
                            </div>
                          </div>
                        </div>';
                    if(isset($report_data_formatted[$key])){
                        echo "<script>$('#table".$key."').DataTable();</script>";
                        echo "<br><br>
                        <div class='table-responsive'>
                        <table id='table".$key."' class='table-bordered table-hover table-striped'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th class='text-center'>";
                                    echo $this->lang->line("Serial");
                                echo "</th>";
                                echo "<th class='text-center'>";
                                    echo $this->lang->line("Subscriber ID");
                                echo "</th>";
                                echo "<th>";
                                    echo $this->lang->line("subscriber name");
                                echo "</th>";;
                                echo "<th class='text-center'>";
                                    echo $this->lang->line("sent status");
                                echo "</th>"; 
                                 echo "<th class='text-center'>";
                                    echo $this->lang->line("sent at");
                                echo "</th>";
                                 echo "<th class='text-center'>";
                                    echo $this->lang->line("delivered at");
                                echo "</th>"; 
                                echo "<th class='text-center'>";
                                    echo $this->lang->line("opened at");
                                echo "</th>";                                                           
                                echo "<th class='text-center'>";
                                    echo $this->lang->line("response");
                                echo "</th>";

                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        $sl=0;                    
                        foreach ($report_data_formatted[$key] as $key2 => $value2) 
                        {
                            $sl++;
                            if($value2['sent_at']!='0000-00-00 00:00:00') $value2['sent_at']=date("jS M, y H:i:s",strtotime($value2['sent_at']));
                            else $value2['sent_at']='x';

                            if($value2['delivered_at']!='0000-00-00 00:00:00') $value2['delivered_at']=date("jS M, y H:i:s",strtotime($value2['delivered_at']));
                            else $value2['delivered_at']='x';

                            if($value2['last_updated_at']!='0000-00-00 00:00:00') $value2['last_updated_at']=date("jS M, y H:i:s",strtotime($value2['last_updated_at']));
                            else $value2['last_updated_at']='x';

                            if($value2['opened_at']!='0000-00-00 00:00:00') $value2['opened_at']=date("jS M, y H:i:s",strtotime($value2['opened_at']));
                            else $value2['opened_at']='x';
                        
                            if($value2['is_opened']=='1') $value2['status'] = "<span class='label label-light'><i class='fa fa-eye blue'></i> ".$this->lang->line('opened')."</span>";
                            else if($value2['is_delivered']=='1') $value2['status'] = "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('delivered')."</span>";
                            else $value2['status'] = "<span class='label label-light'><i class='fa fa-send orange'></i> ".$this->lang->line('sent')."</span>";
                          
                            $db_res=json_decode($value2["sent_response"]);
                            $print_res="";
                            $message_num=0;
                            if(is_array($db_res ))
                            {
                                foreach ($db_res as $key_res => $value_res) 
                                {
                                    $message_num++;
                                    $tempu=explode(' ', $value_res);
                                    if(isset($tempu[0]) && strlen($tempu[0])>50) $value_res=' <i class="fa fa-check-circle green"></i> '.$this->lang->line("sent");
                                    $print_res.=$this->lang->line("message")."-".$message_num." : ".$value_res."<br>";
                                }
                            }
                            else $print_res=$value2["sent_response"];

                            if($print_res=="") $print_res='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("success").'</span>';

                            echo "<tr>";
                                echo "<td align='center'>".$sl."</td>";
                                echo "<td align='center'>".$value2["subscribe_id"]."</td>";
                                echo "<td>".$value2["first_name"]." ".$value2["last_name"]."</td>";
                                echo "<td align='center'>".$value2["status"]."</td>";
                                echo "<td align='center'>".$value2["sent_at"]."</td>";
                                echo "<td align='center'>".$value2["delivered_at"]."</td>";
                                echo "<td align='center'>".$value2["opened_at"]."</td>";
                                echo "<td>".$print_res."</td>";
                            echo "</tr>";
                        }                    
                        echo "</tbody>";
                        echo "</table></div>";

                    }
                  echo'</div>
                </div>            
              </div>';
        }                

        echo'</div>';

       
    }

    public function campaign_list($page_auto_id=0)
    {
        if($page_auto_id==0) exit();
        $table_name = "messenger_bot_page_info";
        $where['where'] = array('bot_enabled' => "1","messenger_bot_page_info.id"=>$page_auto_id);
        $join = array('messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("messenger_bot_page_info.*","messenger_bot_user_info.name as account_name","messenger_bot_user_info.fb_id"),$join);
        if(!isset($page_info[0]))
        redirect('drip_messaging/eligible_pages', 'location'); 
        
        $data['body'] = 'campaign_list';
        $data['page_title'] = $this->lang->line('Drip Messaging Campaign List');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();        
        $data["page_auto_id"]=$page_auto_id;
        $data["drip_types"]=$this->get_drip_type();
        $data['bot_settings'] = $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("page_id"=>$page_auto_id,"user_id"=>$this->user_id)),$select='',$join='',$limit='',$start=NULL,$order_by='campaign_name ASC');
        $data["template_list"]=$this->get_page_template($page_auto_id);
        $data["how_many_days"]=30;
        $data["default_display"]=3;
        $data['timezones']=$this->_time_zone_list();
        $this->_viewcontroller($data); 
    }

    public function create_campaign_action()
    {
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }

        if(!isset($drip_type) || $drip_type=='') $drip_type='default';

        // if the page was not used before then checking for eligible page limit
        $exist_test=$this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id));
        if(!$exist_test)
        {
            $status=$this->_check_usage($module_id=219,$request=1);
            if($status=="3") 
            {
                echo json_encode(array("status" => "0", "message" =>$this->lang->line("You can not enable drip messaging for more page.")));
                exit();
            }
        }

        // every page must have an default campaign
        if($drip_type!='default' && !$this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>"default")))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("You must first create a default type campaign for the page.")));
            exit();        
        }

        // if default campaign exists and trying to create again, prevent it
        if($drip_type=='default' && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>"default")))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Default type campaign is already exist for this page.")));
            exit();        
        }

        // can not duplicate enagement re-targeting
        if($drip_type!='default' && $drip_type!='custom' && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>$drip_type,"engagement_table_id"=>$engagement_table_id)))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("This messenger engagement re-targeting is already used.")));
            exit();        
        }

        $message_content=array();
        for($i=1; $i<=$day_counter;$i++)
        { 
           $temp="template_id".$i;
           if($$temp!="") $message_content[$i]=$$temp;
        }
        $message_content=json_encode($message_content);

        $insert_data=array
        (
            "campaign_name"=>$campaign_name,
            "page_id"=>$page_id,
            "user_id"=>$this->user_id,
            "message_content"=>$message_content,
            "created_at"=>date("Y-m-d H:i:s"),
            "drip_type"=>$drip_type,
            "between_start"=>$between_start,
            "between_end"=>$between_end,
            "timezone"=>$timezone
        );
        if($drip_type!='default' && $drip_type!='custom') $insert_data['engagement_table_id']=$engagement_table_id;

        $this->db->trans_start();
        $this->basic->insert_data("messenger_bot_drip_campaign",$insert_data);
        if(!$exist_test) $this->_insert_usage_log($module_id=219,$request=1);     
        $this->db->trans_complete();
        if($this->db->trans_status() === false)
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("something went wrong, please try again.")));
            exit(); 
        }
        else
        {
            $this->session->set_flashdata('bot_success',1);
            echo json_encode(array("status" => "1", "message" =>""));
            exit(); 
        }    
    }


    public function edit_campaign($id=0,$page_auto_id=0)
    {
        if($page_auto_id==0) exit();
        $table_name = "messenger_bot_page_info";
        $where['where'] = array('bot_enabled' => "1","messenger_bot_page_info.id"=>$page_auto_id);
        $join = array('messenger_bot_user_info'=>"messenger_bot_user_info.id=messenger_bot_page_info.messenger_bot_user_info_id,left");   
        $page_info = $this->basic->get_data($table_name,$where,array("messenger_bot_page_info.*","messenger_bot_user_info.name as account_name","messenger_bot_user_info.fb_id"),$join);
        if(!isset($page_info[0]))
        redirect('drip_messaging/eligible_pages', 'location'); 
        
        $data['body'] = 'edit_campaign';
        $data['page_title'] = $this->lang->line('Edit Drip Message Campaign');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();        
        $data["page_auto_id"]=$page_auto_id;
        $data["drip_types"]=$this->get_drip_type();
        $xdata = $this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $data['xdata']=isset($xdata[0])?$xdata[0]:array();
        $data["template_list"]=$this->get_page_template($page_auto_id);
        $data["how_many_days"]=30;
        $message_content=isset($xdata[0]['message_content'])?json_decode($xdata[0]['message_content'],true):array();
        $default_display=max(array_keys($message_content));
        $data["default_display"]=$default_display;
        $data['timezones']=$this->_time_zone_list();
        $this->_viewcontroller($data); 
    }

    public function edit_campaign_action()
    {
        $post=$_POST;
        foreach ($post as $key => $value) 
        {
            $$key=$this->input->post($key,true);
        }

        if(!isset($drip_type) || $drip_type=='') $drip_type='default';

        $xdata=$this->basic->get_data("messenger_bot_drip_campaign",array("where"=>array("id"=>$campaign_id,"user_id"=>$this->user_id)));
        $xdrip_type=isset($xdata[0]['drip_type'])?$xdata[0]['drip_type']:'default';
        $xengagement_table_id=isset($xdata[0]['engagement_table_id'])?$xdata[0]['engagement_table_id']:'';

        // I dont allow to switch drip type if default :p
        if($drip_type!='default' && $xdrip_type=='default')
        {
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Drip type can not be edited to others from default type.")));
            exit();   
        }

        // if default campaign exists and trying to create again, prevent it
        if($drip_type=='default' && $xdrip_type!='default' && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>"default")))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("Default type campaign is already exist for this page.")));
            exit();        
        }

        // can not duplicate enagement re-targeting
        if($drip_type!='default' && $drip_type!='custom' && $engagement_table_id!=$xengagement_table_id && $drip_type!=$xdrip_type && $this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_id,"user_id"=>$this->user_id,"drip_type"=>$drip_type,"engagement_table_id"=>$engagement_table_id)))
        {            
            echo json_encode(array("status" => "0", "message" =>$this->lang->line("This messenger engagement re-targeting is already used.")));
            exit();        
        }

        $message_content=array();
        for($i=1; $i<=$day_counter;$i++)
        { 
           $temp="template_id".$i;
           if($$temp!="") $message_content[$i]=$$temp;
        }
        $message_content=json_encode($message_content);

        $insert_data=array
        (
            "campaign_name"=>$campaign_name,
            "message_content"=>$message_content,
            "drip_type"=>$drip_type,
            "between_start"=>$between_start,
            "between_end"=>$between_end,
            "timezone"=>$timezone
        );
        if($drip_type!='default' && $drip_type!='custom') $insert_data['engagement_table_id']=$engagement_table_id;

        $this->basic->update_data("messenger_bot_drip_campaign",array("id"=>$campaign_id,"user_id"=>$this->user_id),$insert_data);
        $this->session->set_flashdata('bot_update_success',1);
        echo json_encode(array("status" => "1", "message" =>""));           
    }


    public function delete_campaign()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");        
        $page_auto_id=$this->input->post("page_auto_id");        
        $this->db->trans_start();
        $this->basic->delete_data("messenger_bot_drip_campaign",array("id"=>$id,"user_id"=>$this->user_id,'drip_type !='=>'default'));   
        if(!$this->basic->is_exist("messenger_bot_drip_campaign",array("page_id"=>$page_auto_id)))
        $this->_delete_usage_log(219,1);    
        $this->db->trans_complete();
        if($this->db->trans_status() === false) echo '0';
        else echo '1';
    }


    public function page_messaging_report($page_id=0)
    {
        $this->session->set_userdata('drip_messaging_report_page_id', $page_id);
        redirect('drip_messaging/messaging_report','refresh');
    }

    public function messaging_report()
    {
        $data['body'] = "messaging_report";
        $data['page_title'] = $this->lang->line("Message Sent Log");
        $page_info = $this->db->query("SELECT page_id,page_name,id FROM `messenger_bot_page_info` WHERE bot_enabled='1' AND user_id = '".$this->user_id."'")->result_array();
        $data['page_info'] = $page_info;
        $data["drip_types"]=$this->get_drip_type();
        $this->_viewcontroller($data);
    }

    public function messaging_report_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'last_updated_at';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $campaign_name = trim($this->input->post("search_campaign_name", true));
        $drip_type = trim($this->input->post("search_drip_type", true));
        $page_id = trim($this->input->post("search_page", true));
        $is_searched = $this->input->post('is_searched', true);

        if($is_searched)
        {
            $this->session->set_userdata('drip_messaging_report_campaign_name', $campaign_name);
            $this->session->set_userdata('drip_messaging_report_drip_type', $drip_type);
            $this->session->set_userdata('drip_messaging_report_page_id', $page_id);
        }

        $search_campaign_name  = $this->session->userdata('drip_messaging_report_campaign_name');
        $search_drip_type  = $this->session->userdata('drip_messaging_report_drip_type');
        $search_page_id  = $this->session->userdata('drip_messaging_report_page_id');

        $where_simple=array();

        if ($search_campaign_name) $where_simple['campaign_name like ']    = "%".$search_campaign_name."%";
        if ($search_drip_type) $where_simple['drip_type']    = $search_drip_type;
        if ($search_page_id) $where_simple['messenger_bot_drip_report.page_id'] = $search_page_id;

        $where_simple['messenger_bot_drip_report.user_id'] = $this->user_id;
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $where = array('where' => $where_simple);

        $table = "messenger_bot_drip_report";
        $select="messenger_bot_drip_report.*,campaign_name,message_content,drip_type,messenger_bot_page_info.page_name,messenger_bot_page_info.page_id as fb_page_id";
        $join=array('messenger_bot_page_info'=>"messenger_bot_page_info.id=messenger_bot_drip_report.page_id,left",'messenger_bot_drip_campaign'=>"messenger_bot_drip_campaign.id=messenger_bot_drip_report.messenger_bot_drip_campaign_id,left");
        $info = $this->basic->get_data($table,$where,$select,$join,$limit=$rows, $start=$offset,$order_by=$order_by_str);
        
        for($i=0;$i<count($info);$i++) 
        {
            $info[$i]['campaign_details']="<a target='_BLANK' class='btn btn-outline-info' href='".base_url("drip_messaging/edit_campaign/".$info[$i]["messenger_bot_drip_campaign_id"]."/".$info[$i]["page_id"])."'><i class='fa fa-list-alt'></i> ".$this->lang->line("details")."</a>";
            $info[$i]['subscriber']=$info[$i]['first_name']." ".$info[$i]['last_name'];
            
            if($info[$i]['is_opened']=='1') $info[$i]['status'] = "<span class='label label-light'><i class='fa fa-eye blue'></i> ".$this->lang->line('opened')."</span>";
            else if($info[$i]['is_delivered']=='1') $info[$i]['status'] = "<span class='label label-light'><i class='fa fa-check-circle green'></i> ".$this->lang->line('delivered')."</span>";
            else $info[$i]['status'] = "<span class='label label-light'><i class='fa fa-send orange'></i> ".$this->lang->line('sent')."</span>";
            
            if($info[$i]['last_completed_day']==0) $info[$i]['last_completed_day']='x';
            else $info[$i]['last_completed_day']=$this->lang->line("day")."-".$info[$i]['last_completed_day'];

            if($info[$i]['sent_at']!='0000-00-00 00:00:00') $info[$i]['sent_at']=date("jS M, y H:i:s",strtotime($info[$i]['sent_at']));
            else $info[$i]['sent_at']='x';

            if($info[$i]['delivered_at']!='0000-00-00 00:00:00') $info[$i]['delivered_at']=date("jS M, y H:i:s",strtotime($info[$i]['delivered_at']));
            else $info[$i]['delivered_at']='x';

            if($info[$i]['last_updated_at']!='0000-00-00 00:00:00') $info[$i]['last_updated_at']=date("jS M, y H:i:s",strtotime($info[$i]['last_updated_at']));
            else $info[$i]['last_updated_at']='x';

            if($info[$i]['opened_at']!='0000-00-00 00:00:00') $info[$i]['opened_at']=date("jS M, y H:i:s",strtotime($info[$i]['opened_at']));
            else $info[$i]['opened_at']='x';

            $info[$i]['page_name']="<a target='_BLANK' href='https://facebook.com/".$info[$i]['fb_page_id']."'>".$info[$i]['page_name']."</a>";

        }

        $total_rows_array = $this->basic->count_row($table, $where, $count = "messenger_bot_drip_report.id",$join);
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }

  

    private function get_page_template($page_id=0)
    {
        if($page_id==0) return array();  

        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,"is_template"=>"1")),'','','',$start=NULL,$order_by="template_name ASC");
        $push_postback=array();
        foreach ($postback_data as $key => $value) 
        {
            if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $push_postback[$value['id']]=$value['template_name'].' ['.$value['postback_id'].']';
        }
        return $push_postback;
    }

    public function get_postback()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_id');// database id    
        $push_id=$this->input->post('push_id');   
       
        $postback_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("page_id"=>$page_id,'is_template'=>'1')),'','','',$start=NULL,$order_by='id DESC');
        $push_postback='<select name="template_id'.$push_id.'" class="form-control template_id" id="template_id'.$push_id.'">';
        $push_postback.="<option value=''>"."--- ".$this->lang->line("No")." ".$this->lang->line("Message")." ---"."</option>";
        foreach ($postback_data as $key => $value) 
        {
            if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply") continue;
            $push_postback.="<option value='".$value['id']."'>".$value['template_name'].' ['.$value['postback_id'].']'."</option>";
        }
        $push_postback.='</select><script>$("#template_id'.$push_id.'").select2();</script>';
        echo $push_postback;   
    }

    public function get_engagement_list()
    {
        if(!$_POST) exit();
        $page_id=$this->input->post('page_auto_id');// database id    
        $table_name=$this->input->post('table_name');        
        $engagement_id=$this->input->post('engagement_id');  // provided when edit     

        $page_id_field='page_id';
        if($table_name=="messenger_bot_engagement_2way_chat_plugin") $page_id_field='page_auto_id';
        $getdata=$this->basic->get_data($table_name,array("where"=>array($page_id_field=>$page_id,"user_id"=>$this->user_id)),$select='',$join='',$limit='',$start=NULL,$order_by='id desc');

        if(empty($getdata)) echo "<h4 class='text-center'>".$this->lang->line('no data found')."</h4>";
        else
        {
            echo "<script>$('#engagement_list_data_table').DataTable();</script>
            <table class='table table-hover table-bordered table-striped' id='engagement_list_data_table'>";
              echo "<thead>";
                echo "<tr>";
                  echo "<th class='text-center'>".$this->lang->line("Serial")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Engagement Campaign")."</th>";
                  if(isset($getdata[0]['domain_name']))
                  echo "<th class='text-center'>".$this->lang->line("Domain")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Reference")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Created at")."</th>";
                echo "</tr>";
              echo "</thead>";

              echo "<tbody>";
                $i=0;
                foreach ($getdata as $key => $value) 
                {
                  $i++;
                  if(isset($value['created_at'])) $created_at=date('d M y - H:i:s',strtotime($value['created_at']));
                  else $created_at=date('d M y - H:i:s',strtotime($value['add_date']));

                  $checked='';
                  if($engagement_id!="" && $engagement_id!="0")
                  {
                    if($value["id"]==$engagement_id) 
                    $checked='checked'; 
                  }
                  else 
                  {
                    if($i==1) 
                    $checked='checked'; 
                  }
                
                  $radio= '<input type="radio" class="myradio radio5" name="engagement_table_id" value="'.$value['id'].'" id="engagement_table_id'.$i.'" '.$checked.'><label style="margin-top:7px;" for="engagement_table_id'.$i.'"></label>';
                 
                  echo "<tr>";
                    echo "<td class='text-center' style='vertical-align:middle;'>".$i."</td>";
                    echo "<td class='text-center' style='vertical-align:middle;'>".$radio."</td>";
                    if(isset($getdata[0]['domain_name']))
                    echo "<td class='text-center' style='vertical-align:middle;'><a target='_BLANK' href='".$value['domain_name']."'>".$value['domain_name']."</a></td>";
                    echo "<td class='text-center' style='vertical-align:middle;'>".$value['reference']."</td>";
                    echo "<td class='text-center' style='vertical-align:middle;'>".$created_at."</td>";
                  echo "</tr>";
                }
              echo "</tbody>";
            echo "</table>";
        }
    }

    private function get_drip_type()
    {
        return array
        (
            'default'=>'Default',
            'custom'=>'Custom',
            'messenger_bot_engagement_checkbox'=>'Messenger Checkbox',
            'messenger_bot_engagement_send_to_msg'=>'Send to Messenger',
            'messenger_bot_engagement_mme'=>'m.me Link',
            'messenger_bot_engagement_messenger_codes'=>'Messenger Code',
            'messenger_bot_engagement_2way_chat_plugin'=>'2-way Customer Chat'
        );
    }



    public function activate()
    {
        if(!$_POST) exit();
        if($this->basic->is_exist("add_ons",array("project_id"=>3)) || $this->basic->is_exist("add_ons",array("project_id"=>15)))
        {
          $is_free_addon=false; 
          $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
          $purchase_code=$this->input->post('purchase_code');
          if(!$is_free_addon)
          {
              $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
          }
          
          //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
          $sidebar=array
          (           
              0 =>array
              (
                  'name' => 'Drip Messaging',
                  'icon' => 'fa fa-tint',
                  'url' => '#',
                  'is_external' => '0',
                  'child_info' => array
                  (
                      'have_child'=>'1', // parent has child menus, 0 means no child
                      'child'=>array // if status = 1 then you must add child array, other wise not need to set this index
                      (
                          0 => array
                          (
                              'name'=>'Drip Messaging Setup',
                              'icon'=>'fa fa-cog',
                              'url' => 'drip_messaging/eligible_pages',
                              'is_external' => '0'
                          ),
                          1 => array
                          (
                              'name'=>'Message Sent Log',
                              'icon'=>'fa fa-list',
                              'url' => 'drip_messaging/messaging_report',
                              'is_external' => '0'                           
                          ),
                          2 => array
                          (
                              'name'=>'Cron Job',
                              'icon'=>'fa fa-clock-o',
                              'url' => 'drip_messaging/cron_job',
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
              // extra module, this module aslo deleted manaually
              1=>"INSERT INTO `modules` (`id`, `module_name`, `add_ons_id`,`extra_text`,`deleted`) VALUES ('219', 'Messenger Bot - Drip Messaging : Eligible Pages', '0', '' ,'0');",
              2=>"UPDATE menu_child_1 SET only_admin='1' WHERE module_access=218 AND serial=3;",            
              3=>"ALTER TABLE `messenger_bot_page_info` ADD `review_status` ENUM('NOT SUBMITTED','PENDING','REJECTED','APPROVED','LIMITED') NOT NULL DEFAULT 'NOT SUBMITTED' AFTER `enbale_type_on`;",
              4=>"ALTER TABLE `messenger_bot_page_info` ADD `review_status_last_checked` DATETIME NOT NULL AFTER `review_status`;",
              5=>"CREATE TABLE IF NOT EXISTS `messenger_bot_drip_campaign` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `campaign_name` varchar(250) NOT NULL,
                `page_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `message_content` longtext NOT NULL,
                `created_at` datetime NOT NULL,
                `last_sent_at` datetime NOT NULL,
                `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
                `engagement_table_id` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `page_id` (`page_id`,`user_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
              6=>"CREATE TABLE IF NOT EXISTS `messenger_bot_drip_report` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `messenger_bot_drip_campaign_id` int(11) NOT NULL,
                `messenger_bot_subscriber_id` int(11) NOT NULL,
                `page_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `subscribe_id` varchar(250) NOT NULL,
                `first_name` varchar(250) NOT NULL,
                `last_name` varchar(250) NOT NULL,
                `last_completed_day` int(11) NOT NULL,
                `is_sent` enum('0','1') NOT NULL DEFAULT '1',
                `is_opened` enum('0','1') NOT NULL DEFAULT '0',
                `is_delivered` enum('0','1') NOT NULL DEFAULT '0',
                `sent_at` datetime NOT NULL,
                `delivered_at` datetime NOT NULL,
                `opened_at` datetime NOT NULL,
                `sent_response` tinytext NOT NULL,
                `delivered_response` tinytext NOT NULL,
                `last_updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `messenger_bot_drip_campaign_id` (`messenger_bot_drip_campaign_id`),
                KEY `page_id` (`page_id`),
                KEY `user_id` (`user_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
              7=>"ALTER TABLE `messenger_bot_subscriber` ADD `messenger_bot_drip_campaign_id` INT NOT NULL AFTER `status`, ADD `messenger_bot_drip_last_completed_day` INT NOT NULL AFTER `messenger_bot_drip_campaign_id`, ADD `messenger_bot_drip_is_toatally_complete` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `messenger_bot_drip_last_completed_day`, ADD `messenger_bot_drip_last_sent_at` DATETIME NOT NULL AFTER `messenger_bot_drip_is_toatally_complete`, ADD INDEX (`messenger_bot_drip_campaign_id`);",
              8=>"ALTER TABLE `messenger_bot_subscriber` ADD `drip_type` ENUM('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default' AFTER `messenger_bot_drip_campaign_id`;",
              9=>"ALTER TABLE `messenger_bot_subscriber` ADD `messenger_bot_drip_initial_date` DATETIME NOT NULL AFTER `messenger_bot_drip_last_sent_at`;",
              10=>"ALTER TABLE `messenger_bot_subscriber` ADD `messenger_bot_drip_processing_status` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `messenger_bot_drip_initial_date`;",
              11=>"ALTER TABLE `messenger_bot_subscriber` ADD `last_processing_started_at` DATETIME NOT NULL AFTER `messenger_bot_drip_initial_date`;",
              12=>"ALTER TABLE `messenger_bot_drip_campaign` ADD `between_start` VARCHAR(50) NOT NULL DEFAULT '00:00' AFTER `engagement_table_id`, ADD `between_end` VARCHAR(50) NOT NULL DEFAULT '23:59' AFTER `between_start`, ADD `timezone` VARCHAR(250) NOT NULL AFTER `between_end`;"
          ); 

          //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
          $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code,"Messenger Bot - Drip Messaging : Message Send Limit"); 
        }
        else
        {
          echo json_encode(array('status'=>'0','message'=>$this->lang->line('This add-on requires Bot Inboxer to be installed. Please install Bot Inboxer add-on first.')));
          exit(); 
        }
        
    }


    public function deactivate()
    {        
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $this->db->query("DELETE FROM `modules` WHERE `modules`.`id` = 219");
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    {        
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (
          0=>"DELETE FROM `modules` WHERE `modules`.`id` = 219",
          1=>"DROP TABLE IF EXISTS `messenger_bot_drip_campaign`;",
          2=>"DROP TABLE IF EXISTS `messenger_bot_drip_report`;"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
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

   
    public function drip_messaging_cron($api_key="")
    { 
        $this->api_key_check($api_key);
        $number_of_row=50; // number of subscriber on cron will process

        $get_all_campaign=$this->basic->get_data("messenger_bot_drip_campaign");
        $time_match_campaign_ids=array(); // holds ids of campaigns which time interval matches current time
        foreach ($get_all_campaign as $key => $value) 
        {
            $cam_timezone=$value['timezone'];
            if($cam_timezone)  date_default_timezone_set($cam_timezone);

            $cam_between_start=$value['between_start'];
            $cam_between_end=$value['between_end'];

            $current_time=date("H:i");

            $temp0 = (float) str_replace(':','.',$current_time);
            $temp1 = (float) str_replace(':','.',$cam_between_start);
            $temp2 = (float) str_replace(':','.',$cam_between_end);      

            if($temp0>=$temp1 && $temp0<=$temp2) // matches time slot
            {
                $time_match_campaign_ids[]=$value['id'];
            }
        }

        if(empty($time_match_campaign_ids)) exit(); // no campaign matches current time zone, go home :p

        // reseting to system timezone
        $time_zone = $this->config->item('time_zone');
        if($time_zone== '') $time_zone="Europe/Dublin";        
        date_default_timezone_set($time_zone);


        // getting all unsubscriber label so that we can skip a subcriber if it have any of this label
        if($this->db->table_exists('messenger_bot_broadcast_contact_group')) // no need to check if broadcaster does not exists
        {
        	$unsubscribe_label_data=$this->basic->get_data("messenger_bot_broadcast_contact_group",array("where"=>array("unsubscribe"=>"1")));
        	$unsubscribe_label_ids=array_column($unsubscribe_label_data,'id');
        }
        else $unsubscribe_label_ids=array();
        
        $subscriber_data_final=array(); // filtered subscriber data to send message
        $subscriber_ids=array(); // subscrubers auto ids to send message
        $fb_page_ids=array(); // all facebook pages to be operated
        $subscriber_fb_page=array();// which subscriber under which page
        $page_database=array(); // associated page auto id and page id
        $campaign_ids=array(); // drip campaign ids to be operated

        // getting eligible subscriber data
        $subscriber_where=
        array
        (
            "where"=>array
            (
                "messenger_bot_drip_campaign_id !="=>"0",
                "messenger_bot_drip_initial_date !="=>"0000-00-00 00:00:00",
                "messenger_bot_drip_is_toatally_complete"=>"0",
                "messenger_bot_drip_processing_status"=>"0"
            ),
            "where_in"=>array("messenger_bot_drip_campaign_id"=>$time_match_campaign_ids)
        );
        $subscriber_data=$this->basic->get_data("messenger_bot_subscriber",$subscriber_where,'','',$number_of_row,NULL,'last_processing_started_at ASC');
        
        foreach ($subscriber_data as $key => $value) 
        {
            $mylabels=array();

            if(isset($value['contact_group_id']))
            $mylabels=explode(',',$value['contact_group_id']);

            if(empty($unsubscribe_label_ids) || !array_intersect($unsubscribe_label_ids, $mylabels)) // skipping subscribe if unsubscribed
            {
                $subscriber_data_final[]=$value;
                $subscriber_ids[]=$value['id'];
                $fb_page_ids[]=$value['page_id'];
                $subscriber_fb_page[$value['subscribe_id']]=$value['page_id'];
                $campaign_ids[]=$value['messenger_bot_drip_campaign_id'];
            }
            else // complete processing is unsubcribe, it will not be taken by further crons
            {
                $this->basic->update_data("messenger_bot_subscriber",array("id"=>$value['id']),array("messenger_bot_drip_is_toatally_complete"=>"1"));
            }
        }
        $fb_page_ids=array_unique($fb_page_ids);
        $campaign_ids=array_unique($campaign_ids);

        if(empty($subscriber_ids)) exit();

        $this->load->library("drip_messaging_login"); 

        // marking subscribers this cron is operating as processing (comment this query while test)
        $this->db->where_in('id', $subscriber_ids);
        $this->db->update("messenger_bot_subscriber", array('messenger_bot_drip_processing_status' => "1","last_processing_started_at"=>date("Y-m-d H:i:s")));

        // getting page access token
        $page_data=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("bot_enabled"=>"1"),"where_in"=>array('page_id'=>$fb_page_ids)));
        foreach ($page_data as $key => $value) 
        {
            $page_database[$value['page_id']]=array("page_id"=>$value['id'],"fb_page_id"=>$value['page_id'],"page_access_token"=>$value['page_access_token']);
        }

        // converting campaign data index to campaign auto id
        $campaign_data_formatted=array();
        $campaign_data=$this->basic->get_data("messenger_bot_drip_campaign",array("where_in"=>array('id'=>$campaign_ids)));
        foreach ($campaign_data as $key => $value) 
        {
            $campaign_data_formatted[$value['id']]=$value;
        }
        
        foreach ($subscriber_data_final as $key => $value) 
        {            
            $user_id=$value["user_id"];
            $subscribe_auto_id=$value["id"];
            $subscribe_id=$value["subscribe_id"];
            $first_name=$value["first_name"];
            $last_name=$value["last_name"];
            $messenger_bot_drip_campaign_id=$value["messenger_bot_drip_campaign_id"];
            $messenger_bot_drip_initial_date=$value["messenger_bot_drip_initial_date"];
            $messenger_bot_drip_last_completed_day=$value["messenger_bot_drip_last_completed_day"];
            
            if(!isset($campaign_data_formatted[$messenger_bot_drip_campaign_id])) 
            {
                echo "Drip campaign ID : ".$messenger_bot_drip_campaign_id." not found <br>";
                continue;
            }
           
            $message_content=json_decode($campaign_data_formatted[$messenger_bot_drip_campaign_id]["message_content"],true);
            $message_days=array_keys($message_content); // th days campaign will send message
            $max_send_day=max($message_days); // maximum campaign day, will decide campaign totally complete or not

            foreach ($message_days as $key2 => $value2) 
            {
               if($value2>$messenger_bot_drip_last_completed_day) // getting the next day to start sending message
               {
                  $today=date("Y-m-d");
                  // $today="2018-08-12";
                  $sending_day=$value2; // currently processing this drip day
                  $adding_days=$sending_day-$messenger_bot_drip_last_completed_day; 
                  $sending_date=date('Y-m-d', strtotime($messenger_bot_drip_initial_date. ' + '.$adding_days.' days'));
                  $is_totally_complete='0';
                  if($max_send_day==$sending_day) $is_totally_complete='1';

                  if(strtotime($today)>strtotime($sending_date)) // if somehow some subscriber was failed to sent message and it will never be comeplete so we are canceling it
                  {
                    $this->basic->update_data("messenger_bot_subscriber",array("id"=>$subscribe_auto_id),array("messenger_bot_drip_is_toatally_complete"=>"1"));
                  }

                  if(strtotime($today)==strtotime($sending_date)) // deciding if we have to send message or not today
                  {
                    //getting message template
                    $sending_template_id=$message_content[$sending_day];
                    $template_data=$this->basic->get_data("messenger_bot_postback",array("where"=>array("id"=>$sending_template_id)));
                    if(!isset($template_data[0])) 
                    {
                        echo  "Template ID : ".$sending_template_id." not found <br>";
                        break;
                    }

                    //making message to be sent
                    $temp=$template_data[0]['template_jsoncode'];  

                    $message_array=json_decode($temp,true);  

                    $p=0;
                    $sent_response=array();
                    $curdate=date("Y-m-d H:i:s"); 
                    foreach($message_array as $msg)
                    {
                        $p++;
                        $template_type_file_track=$msg['message']['template_type'];
                        unset($msg['message']['template_type']);
                        $msg['messaging_type'] = "MESSAGE_TAG";
                        $msg["tag"]="NON_PROMOTIONAL_SUBSCRIPTION";
                        $campaign_message_send=json_encode($msg); 
                        $campaign_message_send = str_replace('#LEAD_USER_FIRST_NAME#',$first_name,$campaign_message_send);
                        $campaign_message_send = str_replace('#LEAD_USER_LAST_NAME#',$last_name,$campaign_message_send);
                        $campaign_message_send = str_replace('replace_id',$subscribe_id,$campaign_message_send); 
                                              
                        $error_count=0;
                        try
                        {
                            $page_access_token_send=isset($page_database[$value['page_id']]['page_access_token'])?$page_database[$value['page_id']]['page_access_token']:"";
                            $response = $this->drip_messaging_login->send_non_promotional_message_subscription($campaign_message_send,$page_access_token_send);
                        
                            if(isset($response['message_id']))
                            {
                                $sent_response[] = $response['message_id']; 
                            }
                            else 
                            {
                                if(isset($response["error"]["message"])) $sent_response[] = $response["error"]["message"];  
                                // if(isset($response["error"]["code"])) $message_error_code = $response["error"]["code"]; 
                                $error_count++;                 
                            }              
                            
                        }
                        catch(Exception $e) 
                        {
                          $sent_response[] = $e->getMessage();
                          $error_count++;
                        }
                    }  
                   
                    $insert_data=array
                    (
                        "messenger_bot_drip_campaign_id"=>$messenger_bot_drip_campaign_id,
                        "messenger_bot_subscriber_id"=>$subscribe_auto_id,
                        "page_id"=>$page_database[$value['page_id']]['page_id'],
                        "user_id"=>$user_id,
                        "subscribe_id"=>$subscribe_id,
                        "first_name"=>$first_name,
                        "last_name"=>$last_name,
                        "last_completed_day"=>$sending_day,
                        "is_sent"=>'1',
                        "sent_at"=>$curdate,
                        "last_updated_at"=>$curdate,
                        "sent_response"=>json_encode($sent_response)
                    );
                    $this->basic->insert_data("messenger_bot_drip_report",$insert_data); // inserting send report
                    $report_id=$this->db->insert_id();

                    $total_count=count($message_array);
                    if($error_count!=$total_count) // do not need to update delivery status if error
                    {
                        $curdate2=date("Y-m-d H:i:s");
                        $success_count=$total_count-$error_count;
                        $del_response=$success_count. "/". $total_count." ".$this->lang->line("success");
                        $insert_data2=array
                        (                          
                            "is_delivered"=>'1',
                            "delivered_at"=>$curdate2,
                            "last_updated_at"=>$curdate2,
                            "delivered_response"=>$del_response

                        );
                        $this->basic->update_data("messenger_bot_drip_report",array("id"=>$report_id),$insert_data2); // inserting delivery report
                    }

                    $sub_update=array
                    (
                        "messenger_bot_drip_last_completed_day"=>$sending_day,
                        "messenger_bot_drip_is_toatally_complete"=>$is_totally_complete,
                        "messenger_bot_drip_last_sent_at"=>$curdate,
                        "messenger_bot_drip_initial_date"=>$curdate,
                        "messenger_bot_drip_processing_status"=>"0",
                    );
                    // comment this query while test
                    $this->basic->update_data("messenger_bot_subscriber",array("id"=>$value["id"]),$sub_update); // updating subscriber so that it will process next drip day again
                  }
                  break;
               }
            }             
        }

        // marking subscribers this cron is operating as ok to process by another cron later  (comment this query while test)
        $this->db->where_in('id', $subscriber_ids);
        $this->db->update("messenger_bot_subscriber", array('messenger_bot_drip_processing_status' => "0"));

        //updaing date in messenger_bot_drip_campaign table
        if(count($campaign_ids)>0)
        {
            $this->db->where_in('id', $campaign_ids);
            $this->db->update("messenger_bot_drip_campaign", array('last_sent_at' => date("Y-m-d H:i:s")));
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
    



}
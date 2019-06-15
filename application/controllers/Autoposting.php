<?php
require_once("application/controllers/Home.php"); // loading home controller
class Autoposting extends Home
{
    public $is_broadcaster_exist=false;
	public $is_ultrapost_exist=false;
    public function __construct()
    {
        parent::__construct();

        $function_name=$this->uri->segment(2);
        if($function_name!="autoposting_campaign_create")
        {
            if ($this->session->userdata('logged_in') != 1)
            redirect('home/login_page', 'location');   

            if($this->session->userdata('user_type') != 'Admin' && !in_array(256,$this->module_access))
            redirect('home/login_page', 'location'); 

            $this->important_feature();
            $this->member_validity();
        }
        // if($this->session->userdata("facebook_rx_fb_user_info")==0)
        // redirect('facebook_rx_account_import/index','refresh');        

        $this->load->library('rss_feed');
        $this->is_broadcaster_exist=$this->broadcaster_exist();      
        $this->is_ultrapost_exist=$this->ultrapost_exist();      
    } 

    public function index()
    {
        $this->settings();
    }

    public function broadcaster_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>16))) return true;
        if($this->session->userdata('user_type') == 'Member' && (in_array(210,$this->module_access) || in_array(211,$this->module_access))) return true;
        return false;
    }

    public function ultrapost_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->db->table_exists('facebook_rx_auto_post')) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(223,$this->module_access)) return true;
        return false;
    }

    public function settings()
    {
        $page_info=array();
        if($this->is_ultrapost_exist)
        {
            $table_name = "facebook_rx_fb_page_info";
            $join = array('facebook_rx_fb_user_info'=>"facebook_rx_fb_user_info.id=facebook_rx_fb_page_info.facebook_rx_fb_user_info_id,left");   
            $page_info = $this->basic->get_data($table_name,array("where"=>array("facebook_rx_fb_page_info.user_id"=>$this->user_id)),array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name","facebook_rx_fb_user_info.fb_id","facebook_rx_fb_user_info.access_token"),$join);
        }

        $settings_data=$this->basic->get_data("autoposting",array("where"=>array("user_id"=>$this->user_id)),'','','','','feed_name asc');
        
        $data['body'] = 'autoposting/settings';
        $data['page_title'] = $this->lang->line('Auto Posting Settings');  
        $data['page_info'] = isset($page_info[0]) ? $page_info[0] : array();  
        $data['settings_data'] = $settings_data;
        $data["feed_types"]=$this->basic->get_enum_values("autoposting","feed_type");
       
        $this->_viewcontroller($data); 
    }    

    public function add_feed_action()
    {
        if(!$_POST) exit();

        $feed_name=$this->input->post('feed_name',true);
        $feed_type=$this->input->post('feed_type',true);
        $feed_url=$this->input->post('feed_url',true);

        // if($this->basic->is_exist("autoposting",array("feed_url"=>$feed_url,"user_id"=>$this->user_id),'id'))
        // {
        //     $error_message=$this->lang->line("This feed URL has been already added.");
        //     echo json_encode(array('status'=>'0','message'=>"<i class='fa fa-remove'></i> ".$error_message));
        //     exit();
        // }

        $feed=$this->rss_feed->getFeed($feed_url);

        if(!isset($feed['success']) || $feed['success']!='1')
        {
            $error_message=isset($feed['error_message'])?$feed['error_message']:$this->lang->line("something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>"<i class='fa fa-remove'></i> ".$error_message));
            exit();
        }

        $datetime=date("Y-m-d H:i:s");
        date_default_timezone_set('Europe/Dublin'); // operating in GMT
        $last_pub_date=isset($feed['element_list'][0]['pubDate'])?$feed['element_list'][0]['pubDate']:"";
        $last_pub_date=date("Y-m-d H:i:s",strtotime($last_pub_date));

        $last_pub_title=isset($feed['element_list'][0]['title'])?$feed['element_list'][0]['title']:"";
        $last_pub_url=isset($feed['element_list'][0]['link'])?$feed['element_list'][0]['link']:"";

        $insert_data=array
        (
            "user_id"=>$this->user_id,
            "feed_name"=>$feed_name,
            "feed_type"=>$feed_type,
            "feed_url"=>$feed_url,
            "last_pub_date"=>$last_pub_date,
            "last_pub_title"=>$last_pub_title,
            "last_pub_url"=>$last_pub_url,
            "last_updated_at"=>$datetime,
            "error_message"=>json_encode(array())
        );

        if($this->basic->insert_data("autoposting",$insert_data)) 
        {
            $this->_insert_usage_log(256,1);
            $success_message=$this->lang->line("RSS feed has been added successfully.");
            echo json_encode(array('status'=>'1','message'=>"<i class='fa fa-check-circle'></i> ".$success_message));
        }
        else
        {
            $error_message=$this->lang->line("something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>"<i class='fa fa-remove'></i> ".$error_message));
        }
    }

    public function campaign_settings()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);

        if(!$this->is_ultrapost_exist && !$this->is_broadcaster_exist)
        {
            $error='<div class="alert alert-danger text-center"><i class="fa fa-remove"></i> '.$this->lang->line("Access forbidden : you do not have access to publish/broadcast module. Please contact application admin to get access.").'</div>';
            echo json_encode(array('html'=>$error,'feed_name'=>'','status'=>'0'));
            exit(); 
        }

        $timezones=$this->_time_zone_list();
        $get_data=$this->basic->get_data("autoposting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($get_data[0]))
        {
             $error='<div class="alert alert-danger text-center"><i class="fa fa-remove"></i> '.$this->lang->line("Feed not found.").'</div>';
             echo json_encode(array('html'=>$error,'feed_name'=>'','status'=>'0'));
             exit();
        }

        $fb_page_info=array();
        if($this->is_ultrapost_exist) $fb_page_info=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("facebook_rx_fb_page_info.user_id"=>$this->user_id)),array("facebook_rx_fb_page_info.*","facebook_rx_fb_user_info.name as account_name"),array('facebook_rx_fb_user_info'=>"facebook_rx_fb_page_info.facebook_rx_fb_user_info_id=facebook_rx_fb_user_info.id,left"));

        if($this->is_broadcaster_exist)
        {
            $table_name = "messenger_bot_page_info";
            $where['where'] = array('bot_enabled' => "1","user_id"=>$this->user_id);
            $broadcaste_page_info = $this->basic->get_data($table_name,$where);
        }

        $feed_name=isset($get_data[0]['feed_name'])?$get_data[0]['feed_name']:'';
        $feed_url=isset($get_data[0]['feed_url'])?$get_data[0]['feed_url']:'';
        $feed_name_send="<a href='".$feed_url."' target='_BLANK'>".$feed_name."</a>";

        $html='';
        $script='
        <script>
            $("#submit_status").show();
            $j("document").ready(function(){setTimeout(function(){ $("#page").change();$("#submit_status").hide();}, 1000);}); 
            $("[data-toggle=\"popover\"]").popover(); 
            $("[data-toggle=\"popover\"]").on("click", function(e) {e.preventDefault(); return true;});
            $("#page,#broadcast_timezone,#posting_timezone").select2(); 
            $j("#post_to_pages").multipleSelect({
                filter: true,
                multiple: true
            });
            $j(".timepicker").datetimepicker({
              datepicker:false,
              format:"H:i"
            });'.
            "           
            $(document.body).on('change','#page',function(){     
              var page_id=$(this).val();
              if(page_id=='') return;
              var hidden_id='".$id."';
              var table_name= 'autoposting';
              $('.dropdown_con').addClass('hidden');
              $.ajax({
                type:'POST' ,
                url: base_url+'messenger_broadcaster/get_label_dropdown_edit',
                data: {page_id:page_id,hidden_id:hidden_id,table_name:table_name},
                dataType : 'JSON',
                success:function(response){
                  $('.dropdown_con').removeClass('hidden');
                  $('#first_dropdown').html(response.first_dropdown);
                  $('#second_dropdown').html(response.second_dropdown);                                     
                  $('#fb_page_id').val(response.pageinfo.page_id);
                }
              });
            });
        </script>";

        $gaptime=$this->lang->line("If the system gets small number of feeds they will be processed in first hour of given time range. If system gets large amount of feeds then they will be processed spanning all over the time range.");
        $tooplip1='<a data-html="true" href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Post Between Time").'" data-content="'.$this->lang->line('Feed information will only be posted during this time slot.')." ".$gaptime.'">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> </a>';
        $tooplip2='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Post Between Time").'" data-content="'.$this->lang->line('Feed information will only be broadcaster during this time slot.')." ".$gaptime.'">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> </a>';
        $tooplip3='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Notification Type").'" data-content="'.$this->lang->line('Regular Push notification will make a sound and display a phone notification. Use it for important messages.')."<br><br>".$this->lang->line('Silent Push notification will display a phone notification without sound. Use it for regular messages that do not require immediate action.')."<br><br>".$this->lang->line('No push will not display any notification. Use it for silently sending content updates.').'">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> </a>';

        if($this->is_ultrapost_exist)
        {
            $xpost_to_pages=isset($get_data[0]['page_ids'])?explode(',', $get_data[0]['page_ids']):array();
            $xposting_timezone=isset($get_data[0]['posting_timezone'])?$get_data[0]['posting_timezone']:"";
            $xposting_start_time=isset($get_data[0]['posting_start_time'])?$get_data[0]['posting_start_time']:"";
            $xposting_end_time=isset($get_data[0]['posting_end_time'])?$get_data[0]['posting_end_time']:"";

            if($xposting_timezone=="") $xposting_timezone=$this->config->item("time_zone");
            if($xposting_start_time=="") $xposting_start_time="00:00";
            if($xposting_end_time=="") $xposting_end_time="23:59";
        }

        if($this->is_broadcaster_exist)
        {
            $xbroadcast_timezone=isset($get_data[0]['broadcast_timezone'])?$get_data[0]['broadcast_timezone']:"";
            $xpage_id=isset($get_data[0]['page_id'])?$get_data[0]['page_id']:"";
            $xbroadcast_start_time=isset($get_data[0]['broadcast_start_time'])?$get_data[0]['broadcast_start_time']:"";
            $xbroadcast_end_time=isset($get_data[0]['broadcast_end_time'])?$get_data[0]['broadcast_end_time']:"";
            $xbroadcast_notification_type=isset($get_data[0]['broadcast_notification_type'])?$get_data[0]['broadcast_notification_type']:"";
            $xbroadcast_display_unsubscribe=isset($get_data[0]['broadcast_display_unsubscribe'])?$get_data[0]['broadcast_display_unsubscribe']:"";

            if($xbroadcast_timezone=="") $xbroadcast_timezone=$this->config->item("time_zone");
            if($xbroadcast_start_time=="") $xbroadcast_start_time="00:00";
            if($xbroadcast_end_time=="") $xbroadcast_end_time="23:59";
            if($xbroadcast_notification_type=="") $xnotification_type="REGULAR";
            if($xbroadcast_display_unsubscribe=="") $xbroadcast_display_unsubscribe="0";
        }

        

        $html.= $script;
        $html.='<form action="#" enctype="multipart/form-data" id="campaign_settings_form" method="post">';     
        $html.='<div id="submit_status"><img src="'.base_url('assets/pre-loader/custom_lg.gif').'" class="center-block"></div>';
        if($this->is_ultrapost_exist)
        {
            $html.='<fieldset style="padding-top:30px;padding-bottom:30px;margin:10px 15px 25px 15px;border-radius:0;"><legend class="dynamic_font_color"><i class="fa fa-share-alt"></i> '.$this->lang->line("Facebook Posting Campaign Setup").'</legend>';
            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <input type="hidden" name="campaign_id" id="campaign_id" value="'.$id.'">
                            <label>'.$this->lang->line("Post to Pages").'</label>
                            <select multiple="multiple"  class="form-control" id="post_to_pages" name="post_to_pages[]">';
                            
                                foreach($fb_page_info as $key=>$val)
                                {
                                    $id=$val['id'];
                                    $page_name=$val['page_name'];
                                    $account_name=$val['account_name'];
                                    $selected='';
                                    if(in_array($id, $xpost_to_pages)) $selected="selected";
                                    $html.="<option value='{$id}' {$selected}>{$page_name} (".$account_name.")</option>";
                                }
                            $html.=
                            '</select>
                        </div>
                    </div>';
            $html.='<div class="col-xs-12 col-md-6">
                            <div class="form-group">
                            <label>'.$this->lang->line("Posting Timezone").'</label>
                            '.form_dropdown('posting_timezone', $timezones, $xposting_timezone,"class='form-control' id='posting_timezone'").'
                            </div>
                        </div>';
            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group">
                        <label>'.$this->lang->line("Post Between Time")." ".$tooplip1.'</label>
                        <input type="text" class="form-control timepicker" value="'.$xposting_start_time.'" id="posting_start_time" name="posting_start_time">
                        </div>
                    </div>';
            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label class="hidden-xs hidden-sm" style="position: relative;right: 22px;top: 32px;">'.$this->lang->line("To").'</label>
                            <input type="text" class="form-control timepicker" value="'.$xposting_end_time.'" id="posting_end_time" name="posting_end_time">
                        </div>
                    </div>';
            $html.='</fieldset>';
        }

        if($this->is_broadcaster_exist)
        {
            $html.='<fieldset style="padding-top:30px;padding-bottom:30px;margin:10px 15px 25px 15px;border-radius:0;"><legend class="dynamic_font_color"><i class="fa fa-send"></i> '.$this->lang->line("Quick Broadcast Campaign Setup").'</legend>';
            $html.='<div class="col-xs-12 col-md-6"> 
                        <div class="form-group">
                           <label>'.$this->lang->line("Broadcast to Pages").'</label>
                              <input type="hidden" name="fb_page_id" id="fb_page_id">
                              <select class="form-control" id="page" name="page"> 
                                <option value="">'.$this->lang->line("Select Page").'</option>';                                              
                                  foreach($broadcaste_page_info as $key=>$val)
                                  { 
                                    $id=$val['id'];
                                    $page_name=$val['page_name'];
                                    $selected='';
                                    if($id==$xpage_id) $selected="selected";
                                    $html.="<option value='{$id}' {$selected}>{$page_name}</option>";               
                                  }           
                              $html.=
                            '</select>
                        </div>
                    </div>';
            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group">
                        <label>'.$this->lang->line("Broadcast Timezone").'</label>
                        '.form_dropdown('broadcast_timezone', $timezones, $xbroadcast_timezone,"class='form-control' id='broadcast_timezone'").'
                        </div>
                    </div>';
            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group">
                        <label>'.$this->lang->line("Broadcast Between Time")." ".$tooplip2.'</label>
                        <input type="text" class="form-control timepicker" value="'.$xbroadcast_start_time.'"  id="broadcast_start_time" name="broadcast_start_time">
                        </div>
                    </div>';
            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group">
                             <label class="hidden-xs hidden-sm" style="position: relative;right: 22px;top: 32px;">'.$this->lang->line("To").'</label>
                            <input type="text" class="form-control timepicker" value="'.$xbroadcast_end_time.'"  id="broadcast_end_time" name="broadcast_end_time">
                        </div>
                    </div>';

            $html.='<div class="col-xs-12 col-md-6 hidden dropdown_con"> 
                        <div class="form-group">
                          <label style="width:100%">
                          '.$this->lang->line("Choose Labels").'
                          </label>
                          <span id="first_dropdown"></span>                                  
                        </div>       
                    </div>
                    <div class="col-xs-12 col-md-6 hidden dropdown_con"> 
                        <div class="form-group">
                          <label style="width:100%">
                            '.$this->lang->line("Exclude Labels").'
                          </label>
                          <span id="second_dropdown"></span>                 
                        </div>       
                    </div>';

            $notification_types=array("REGULAR"=>"REGULAR","SILENT_PUSH"=>"SILENT_PUSH","NO_PUSH"=>"NO_PUSH");
            $notification_type_form=form_dropdown('broadcast_notification_type', $notification_types, $xbroadcast_notification_type,'class="form-control" id="broadcast_display_unsubscribe"');

            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label>'.$this->lang->line("Notification Type").' '.$tooplip3.'</label>
                            '.$notification_type_form.'
                        </div>
                    </div>';

            $unsubscribe_form="";
            if($xbroadcast_display_unsubscribe=='1')
            {
                $unsubscribe_form.='<br><input type="radio" id="unsubscribe_yes" value="1" name="broadcast_display_unsubscribe" checked> <label for="unsubscribe_yes">'.$this->lang->line("yes").'</label>';
                $unsubscribe_form.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="unsubscribe_no" value="0" name="broadcast_display_unsubscribe"> <label for="unsubscribe_no">'.$this->lang->line("no").'</label>';
            }
            else
            {
                $unsubscribe_form.='<br><input type="radio" id="unsubscribe_yes" value="1" name="broadcast_display_unsubscribe"> <label for="unsubscribe_yes">'.$this->lang->line("yes").'</label>';
                $unsubscribe_form.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="unsubscribe_no" value="0" name="broadcast_display_unsubscribe" checked> <label for="unsubscribe_no">'.$this->lang->line("no").'</label>';
            }
            $html.='<div class="col-xs-12 col-md-6">
                        <div class="form-group"><label>'.$this->lang->line("Display Unsubscribe Button?").'</label>'.$unsubscribe_form.'</div>
                    </div>';

            $html.='</fieldset>';
        }

        $html.='<div id="submit_response"></div>';
        $html.='</form><div class="clearfix">';
       

        echo json_encode(array('html'=>$html,'feed_name'=>$feed_name_send,'status'=>'1'));
    }

    public function create_campaign()
    {
        if(!$_POST) exit();
        $campaign_id=$this->input->post("campaign_id",true);
        $xdata=$this->basic->get_data("autoposting",array("where"=>array("id"=>$campaign_id,"user_id"=>$this->user_id)));

        $post_to_pages=array();
        $page_names=array();
        $posting_start_time="";
        $posting_end_time="";
        $posting_end_time="";
        $posting_timezone="";

        $broadcast_fb_page_id="";
        $broadcast_page_id="";
        $broadcast_page_name="";
        $broadcast_label_ids=array();
        $broadcast_start_time="";
        $broadcast_end_time="";
        $broadcast_notification_type="";
        $broadcast_display_unsubscribe="";
        $broadcast_excluded_label_ids=array();
        $broadcast_timezone="";

        if($this->is_ultrapost_exist)
        {
            $post_to_pages=$this->input->post("post_to_pages",true);
            if(!isset($post_to_pages) || !is_array($post_to_pages)) $post_to_pages=array();
            $posting_start_time=$this->input->post("posting_start_time",true);
            $posting_end_time=$this->input->post("posting_end_time",true);
            $posting_end_time=$this->input->post("posting_end_time",true);
            $posting_timezone=$this->input->post("posting_timezone",true);
        }  

        if($this->is_broadcaster_exist)
        {
            $broadcast_fb_page_id=$this->input->post("fb_page_id",true);
            $broadcast_page_id=$this->input->post("page",true);
            $broadcast_label_ids=$this->input->post("label_ids",true);
            $broadcast_excluded_label_ids=$this->input->post("excluded_label_ids",true);
            $broadcast_start_time=$this->input->post("broadcast_start_time",true);
            $broadcast_end_time=$this->input->post("broadcast_end_time",true);
            $broadcast_timezone=$this->input->post("broadcast_timezone",true);
            $broadcast_notification_type=$this->input->post("broadcast_notification_type",true);
            $broadcast_display_unsubscribe=$this->input->post("broadcast_display_unsubscribe",true);
            if(!isset($broadcast_label_ids) || !is_array($broadcast_label_ids)) $broadcast_label_ids=array();
            if(!isset($broadcast_excluded_label_ids) || !is_array($broadcast_excluded_label_ids)) $broadcast_excluded_label_ids=array();
        }

        $update_data=array
        (                     
           "last_updated_at"=>date("Y-m-d H:i:s")
        );

        if($this->is_ultrapost_exist && count($post_to_pages)>0)
        {
            $pagedata=$this->basic->get_data("facebook_rx_fb_page_info",array("where_in"=>array("id"=>$post_to_pages)));
            $page_names_array=array();
            $facebook_rx_fb_user_info_id_array=array();
            foreach ($pagedata as $key => $value)
            {
                $page_names_array[$value['id']]=$value["page_name"];
                $facebook_rx_fb_user_info_id_array[$value['id']]=$value["facebook_rx_fb_user_info_id"];
            }
            $update_data["page_ids"]=implode(',', $post_to_pages);
            $update_data["page_names"]=json_encode($page_names_array);
            $update_data["facebook_rx_fb_user_info_ids"]=json_encode($facebook_rx_fb_user_info_id_array);
            $update_data["posting_start_time"]=$posting_start_time;
            $update_data["posting_end_time"]=$posting_end_time;
            $update_data["posting_timezone"]=$posting_timezone;
        }
        else
        {
            $update_data["page_ids"]="";
            $update_data["page_names"]=json_encode(array());
            $update_data["facebook_rx_fb_user_info_ids"]=json_encode(array());
            $update_data["posting_start_time"]="";
            $update_data["posting_end_time"]="";
            $update_data["posting_timezone"]="";
        }

        if($this->is_broadcaster_exist && $broadcast_page_id!="")
        {
            $pagedata2=$this->basic->get_data("messenger_bot_page_info",array("where"=>array("id"=>$broadcast_page_id)));
            $broadcast_page_name=isset($pagedata2[0]['page_name'])?$pagedata2[0]['page_name']:"";
            $update_data["page_id"]=$broadcast_page_id;
            $update_data["fb_page_id"]=$broadcast_fb_page_id;
            $update_data["page_name"]=$broadcast_page_name;
            $update_data["label_ids"]=implode(',', $broadcast_label_ids);
            $update_data["excluded_label_ids"]=implode(',', $broadcast_excluded_label_ids);
            $update_data["broadcast_start_time"]=$broadcast_start_time;
            $update_data["broadcast_end_time"]=$broadcast_end_time;
            $update_data["broadcast_timezone"]=$broadcast_timezone;
            $update_data["broadcast_notification_type"]=$broadcast_notification_type;
            $update_data["broadcast_display_unsubscribe"]=$broadcast_display_unsubscribe;
        }
        else
        {
            $broadcast_page_name="";
            $update_data["page_id"]="";
            $update_data["fb_page_id"]="";
            $update_data["page_name"]="";
            $update_data["label_ids"]="";
            $update_data["excluded_label_ids"]="";
            $update_data["broadcast_start_time"]="";
            $update_data["broadcast_end_time"]="";
            $update_data["broadcast_timezone"]="";
            $update_data["broadcast_notification_type"]="";
            $update_data["broadcast_display_unsubscribe"]="";
        }

        $this->basic->update_data("autoposting",array("id"=>$campaign_id,"user_id"=>$this->user_id),$update_data);
        {
            echo json_encode(array("status"=>"1","message"=>$this->lang->line("Campaign has been submitted successfully and will start processing shortly as per your settings.")));
        }

    }

    public function enable_settings()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);
        $get_data=$this->basic->get_data("autoposting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        if(!isset($get_data[0]))
        {
             $error=$this->lang->line("Feed not found.");
             echo json_encode(array('message'=>$error,'status'=>'0'));
             exit();
        }
        $feed_url=isset($get_data[0]['feed_url'])?$get_data[0]['feed_url']:'';

        $feed=$this->rss_feed->getFeed($feed_url);

        if(!isset($feed['success']) || $feed['success']!='1')
        {
            $error_message=isset($feed['error_message'])?$feed['error_message']:$this->lang->line("something went wrong, please try again.");
            echo json_encode(array('status'=>'0','message'=>$error_message));
            exit();
        }
        
        $datetime=date("Y-m-d H:i:s");
        date_default_timezone_set('Europe/Dublin'); // operating in GMT
        $last_pub_date=isset($feed['element_list'][0]['pubDate'])?$feed['element_list'][0]['pubDate']:"";
        $last_pub_date=date("Y-m-d H:i:s",strtotime($last_pub_date));
        $last_pub_title=isset($feed['element_list'][0]['title'])?$feed['element_list'][0]['title']:"";
        $last_pub_url=isset($feed['element_list'][0]['link'])?$feed['element_list'][0]['link']:"";

        $update_data=array
        (
            "last_pub_date"=>$last_pub_date,
            "last_pub_title"=>$last_pub_title,
            "last_pub_url"=>$last_pub_url,
            "last_updated_at"=>$datetime,
            "status"=>"1"
        );

        if($this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),$update_data))
        $this->session->set_flashdata('auto_success',1);
        else $this->session->set_flashdata('auto_success',0);       

        echo json_encode(array('status'=>'1'));     
    }


    public function disable_settings()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);

        if($this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),array("status"=>"0")))
        {
            $this->session->set_flashdata('auto_success',1);
        }
        else
        {
            $this->session->set_flashdata('auto_success',0);
        }
    }

    public function delete_settings()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);

        if($this->basic->delete_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            $this->session->set_flashdata('auto_success',1);
            $this->_delete_usage_log(256,1);
        }
        else
        {
            $this->session->set_flashdata('auto_success',0);
        }
    }

    public function error_log()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);
        $get_data=$this->basic->get_data("autoposting",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $error_log=isset($get_data[0]["error_message"])?json_decode($get_data[0]["error_message"],true):array();
        if(!is_array($error_log) || count($error_log)==0)
        {
            echo "<div class='alert alert-warning text-center'><i class='fa fa-remove'></i> ".$this->lang->line('no data found')."</div>";
        }
        else
        {
            $error_log=array_reverse($error_log);

            echo '<script>
                  $j(document).ready(function() {
                      $(".mypre").mCustomScrollbar({
                        autoHideScrollbar:true,
                        theme:"3d-dark",
                        axis: "x"
                      });
                    });
                  </script>';
            
            echo "<pre style='margin:20px; padding:10px 25px;' class='mypre'>";
            foreach ($error_log as $key => $value) 
            {
                echo "<br>".date("d-m-Y H:i:s",strtotime($value['time']))." : ".$value["message"];
            }
            echo "</pre>";
            echo "<a class='clear_log btn btn-outline-danger btn-sm pull-right' style='margin-right:20px;' data-id='".$id."'><i class='fa fa-trash'></i>".$this->lang->line('Delete')."</a><div class='clearfix'></div>";
        }
    }

    public function clear_log()
    {
        if(!$_POST) exit();
        $id=$this->input->post('id',true);
        $this->basic->update_data("autoposting",array("id"=>$id,"user_id"=>$this->user_id),array("error_message"=>json_encode(array()),"last_updated_at"=>date("Y-m-d H:i:s")));      
        echo "<div class='alert alert-success text-center'><i class='fa fa-check-circle'></i> ".$this->lang->line('your data has been successfully deleted from the database.')."</div>";        
    }


    // run every 5  minutes
    public function autoposting_campaign_create($api_key="")
    {
        $this->api_key_check($api_key);  
        $cron_limit=5;

        $feed_data=$this->basic->get_data("autoposting",array("where"=>array("cron_status"=>"0","status"=>"1")),'','',$cron_limit,NULL,'last_updated_at ASC');

        $all_feed_id=array();
        foreach ($feed_data as $key => $value) 
        {   
            $user_id=isset($value['user_id'])?$value['user_id']:'';
            if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1"))) // cancelinng inactive users feeds so that they does not start again
            {
                $this->basic->update_data("autoposting",array("id"=>$value['id']),array("status"=>"2","last_updated_at"=>date("Y-m-d H:i:s")));
                continue;
            }
            $all_feed_id[]=$value['id'];
        }
        if(empty($all_feed_id)) exit(); // stop, no data found

        $this->db->where_in("id",$all_feed_id);
        $this->db->update("autoposting",array("cron_status"=>"1","last_updated_at"=>date("Y-m-d H:i:s")));

        $datetime=date("Y-m-d H:i:s");
        
        foreach ($feed_data as $key => $value) 
        {            
            $user_id=isset($value['user_id'])?$value['user_id']:'';
            if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1"))) continue; // skipping inactive users feeds
       
            $feed_name=isset($value['feed_name'])?$value['feed_name']:'';
            $last_pub_date=isset($value['last_pub_date'])?$value['last_pub_date']:'';
            $error_log=isset($value['error_message'])?json_decode($value['error_message'],true):array();

            $posting_start_time=isset($value['posting_start_time'])?$value['posting_start_time']:"00:00";
            $posting_end_time=isset($value['posting_end_time'])?$value['posting_end_time']:"23:59";
            $posting_timezone=isset($value['posting_timezone'])?$value['posting_timezone']:"";
            if($posting_timezone=="") $posting_timezone=$this->config->item("time_zone");

            $broadcast_start_time=isset($value['broadcast_start_time'])?$value['broadcast_start_time']:"00:00";
            $broadcast_end_time=isset($value['broadcast_end_time'])?$value['broadcast_end_time']:"23:59";
            $broadcast_timezone=isset($value['broadcast_timezone'])?$value['broadcast_timezone']:"";
            if($broadcast_timezone=="") $broadcast_timezone=$this->config->item("time_zone");
            $broadcast_notification_type=isset($value['broadcast_notification_type'])?$value['broadcast_notification_type']:"REGULAR";
            $broadcast_display_unsubscribe=isset($value['broadcast_display_unsubscribe'])?$value['broadcast_display_unsubscribe']:"0";

            $feed_url=isset($value['feed_url'])?$value['feed_url']:'';
            $feed=$this->rss_feed->getFeed($feed_url);
            echo "<pre>";
            print_r($feed);

            if(!isset($feed['success']) || $feed['success']!='1') // stop if get error while getting feed
            {
                $error_message=isset($feed['error_message'])?$feed['error_message']:$this->lang->line("something went wrong while fetching feed data.");
                $error_message.=" [RSS]";
                $error_row=array("time"=>$datetime,"message"=>$error_message);
                array_push($error_log, $error_row);
                $this->basic->update_data("autoposting",array("id"=>$value['id']),array("cron_status"=>"0","last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));
                continue;
            }           

            $new_last_pub_title=isset($feed['element_list'][0]['title'])?$feed['element_list'][0]['title']:"";
            $new_last_pub_url=isset($feed['element_list'][0]['link'])?$feed['element_list'][0]['link']:"";
            $new_last_pub_date=$last_pub_date;
            date_default_timezone_set('Europe/Dublin'); // operating in GMT
            $new_last_pub_date=isset($feed['element_list'][0]['pubDate'])?$feed['element_list'][0]['pubDate']:"";
            if($new_last_pub_date!="") $new_last_pub_date=date("Y-m-d H:i:s",strtotime($new_last_pub_date));

            $element_list=isset($feed['element_list'])?$feed['element_list']:array();
            $element_list=array_reverse($element_list);

            $valid_feed=0;
            foreach($element_list as $key2 => $value2) // how many latest feed there will be
            {
                $pub_date=isset($value2['pubDate'])?$value2['pubDate']:"";
                $pub_date=date("Y-m-d H:i:s",strtotime($pub_date));
                if(strtotime($pub_date)>strtotime($last_pub_date)) $valid_feed++;
            }

            if($valid_feed==0) // stop cron if no latest feed found
            {
                $time_zone = $this->config->item('time_zone');
                if($time_zone== '') $time_zone="Europe/Dublin";
                date_default_timezone_set($time_zone);
                $this->db->where_in("id",$all_feed_id);
                $this->db->update("autoposting",array("cron_status"=>"0","last_updated_at"=>date("Y-m-d H:i:s")));
                exit();
            }

            // posting time calculation
            date_default_timezone_set($posting_timezone);
            $current_datetime=date("Y-m-d H:i:s");
            $current_date=date("Y-m-d");
            $current_time=date("H:i");

            $temp0 = (float) str_replace(':','.',$current_time);
            $temp1 = (float) str_replace(':','.',$posting_start_time);
            $temp2 = (float) str_replace(':','.',$posting_end_time);
            $temp_difference = $temp2-$temp1;
            $temp_hour_min=ceil($temp_difference)*60;
            $temp_min=$temp_difference-ceil($temp_difference);
            $temp_min=number_format((float)$temp_min, 2, '.', '');
            $available_min=$temp_hour_min+$temp_min;
            $gap_minute=round($available_min/$valid_feed); // say we have 120 min time span and have 10 valid feed, then campaigns will be scheduled every 12 minutes

            $post_schedule_time="";

            if($temp0>=$temp1 && $temp0<=$temp2) // matches time slot
            {
                $post_schedule_time = strtotime($current_datetime.' + 2 minute');
                $post_schedule_time = date('Y-m-d H:i:s', $post_schedule_time);
            }
            else
            {
                $make_date=$current_date." ".$posting_start_time.":00";
                if(strtotime($make_date)<strtotime($current_datetime)) // if start time is less than current time then we will schedule it next day
                {
                    $post_schedule_time = strtotime($make_date.' + 1 day');
                    $post_schedule_time = date('Y-m-d H:i:s', $post_schedule_time);
                }
                else $post_schedule_time=$make_date;
            }            
            $post_gap_minute=0;
            // posting time calculation


            // broadcast time calculation
            date_default_timezone_set($broadcast_timezone);
            $broadcast_current_datetime=date("Y-m-d H:i:s");
            $broadcast_current_date=date("Y-m-d");
            $broadcast_current_time=date("H:i");

            $broadcast_temp0 = (float) str_replace(':','.',$broadcast_current_time);
            $broadcast_temp1 = (float) str_replace(':','.',$broadcast_start_time);
            $broadcast_temp2 = (float) str_replace(':','.',$broadcast_end_time);
            $broadcast_temp_difference = $broadcast_temp2-$broadcast_temp1;
            $broadcast_temp_hour_min=ceil($broadcast_temp_difference)*60;
            $broadcast_temp_min=$broadcast_temp_difference-ceil($broadcast_temp_difference);
            $broadcast_temp_min=number_format((float)$broadcast_temp_min, 2, '.', '');
            $broadcast_available_min=$broadcast_temp_hour_min+$broadcast_temp_min;
            $gap_minute2=round($broadcast_available_min/$valid_feed); // say we have 120 min time span and have 10 valid feed, then campaigns will be scheduled every 12 minutes

            $broadcast_schedule_time="";

            if($broadcast_temp0>=$broadcast_temp1 && $broadcast_temp0<=$broadcast_temp2) // matches time slot
            {
                $broadcast_schedule_time = strtotime($broadcast_current_datetime.' + 2 minute');
                $broadcast_schedule_time = date('Y-m-d H:i:s', $broadcast_schedule_time);
            }
            else
            {
                $broadcast_make_date=$broadcast_current_date." ".$broadcast_start_time.":00";
                if(strtotime($broadcast_make_date)<strtotime($broadcast_current_datetime)) // if start time is less than current time then we will schedule it next day
                {
                    $broadcast_schedule_time = strtotime($broadcast_make_date.' + 1 day');
                    $broadcast_schedule_time = date('Y-m-d H:i:s', $broadcast_schedule_time);
                }
                else $broadcast_schedule_time=$broadcast_make_date;
            }            
            $broadcast_gap_minute=0;
            // broadcast time calculation


            foreach($element_list as $key2 => $value2) 
            {
                date_default_timezone_set('Europe/Dublin'); // operating in GMT
                $pub_date=isset($value2['pubDate'])?$value2['pubDate']:"";
                $pub_date=date("Y-m-d H:i:s",strtotime($pub_date));                

                if(strtotime($pub_date)>strtotime($last_pub_date)) // only work with recent feed
                {                    
                    if($valid_feed>3) 
                    {
                        $post_gap_minute+=$gap_minute; 
                        $broadcast_gap_minute+=$gap_minute2;
                    }
                    else
                    {
                        $post_gap_minute+=15; 
                        $broadcast_gap_minute+=15;
                    }

                    $post_feed_url=isset($value2['link'])?$value2['link']:"";             

                    // processing facebook post
                    $page_ids = isset($value['page_ids'])?explode(',', $value['page_ids']):array();
                    $facebook_rx_fb_user_info_ids = isset($value['facebook_rx_fb_user_info_ids'])?json_decode($value['facebook_rx_fb_user_info_ids'],true):array();
                    $page_names = isset($value['page_names'])?json_decode($value['page_names'],true):array();
                    $request_count=count(array_filter($page_ids));
                    if($request_count>0)
                    {
                        $status=$this->_check_usage($module_id=223,$request_count,$user_id);
                        if($status=="3")
                        {
                            $this->basic->update_data("autoposting",array("id"=>$value['id']),array("error_message"=>$error_message));  

                            $error_message = $this->lang->line("Your monthly limit for Facebook posting module has been exceeded.");  
                            $error_message.=" [Facebook Posting]";
                            $error_row=array("time"=>$datetime,"message"=>$error_message);
                            array_push($error_log, $error_row);
                            $this->basic->update_data("autoposting",array("id"=>$value['id']),array("last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));               
                        }
                        else
                        {                            
                            foreach($page_ids as $key3 => $value3) 
                            {                               
                               $facebook_rx_fb_user_info_id=isset($facebook_rx_fb_user_info_ids[$value3])?$facebook_rx_fb_user_info_ids[$value3]:0;
                               $page_or_group_or_user_name=isset($page_names[$value3])?$page_names[$value3]:"";

                               $post_schedule_time_gapped=$post_schedule_time;
                               if($valid_feed<=3) // if there is a small amount of feeds then we will try to post in first hour
                               {
                                   $post_schedule_time_gapped = strtotime($post_schedule_time.' + '.$post_gap_minute.' minute');
                                   $post_schedule_time_gapped = date('Y-m-d H:i:s', $post_schedule_time_gapped);
                               }
                               else // if there is a large amount of feeds then we will try to span the feed post process to cover whole timeslot
                               {
                                   if($post_gap_minute>0)
                                   {
                                       $post_schedule_time_gapped = strtotime($post_schedule_time.' + '.$post_gap_minute.' minute');
                                       $post_schedule_time_gapped = date('Y-m-d H:i:s', $post_schedule_time_gapped);
                                   }
                                }
                               
                               $create_campaign_data=array
                               (
                                  "user_id"=>$user_id,
                                  "facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id,
                                  "post_type"=>"link_submit",
                                  "campaign_name"=>$feed_name." [RSS Autopost]",
                                  "page_group_user_id"=>$value3,
                                  "page_or_group_or_user"=>"page",
                                  "page_or_group_or_user_name"=>$page_or_group_or_user_name,
                                  "link"=>$post_feed_url,
                                  "posting_status"=>"0",
                                  "last_updated_at"=>$datetime,
                                  "schedule_time"=>$post_schedule_time_gapped,
                                  "time_zone"=>$posting_timezone,
                                  "is_autopost"=>"1"
                               );                             

                               $this->basic->insert_data("facebook_rx_auto_post",$create_campaign_data);
                               $this->_insert_usage_log($module_id=223,$request=1,$user_id);
                            }                            
                            
                        }
                    }
                    // processing facebook post


                    //processing broadcasting
                    $get_meta_tag_fb=$this->rss_feed->get_meta_tag_fb($post_feed_url);
                    $feed_url_title=isset($get_meta_tag_fb['title'])?$get_meta_tag_fb['title']:"";
                    $feed_url_image=isset($get_meta_tag_fb['image'])?$get_meta_tag_fb['image']:"";
                    $feed_url_des=isset($get_meta_tag_fb['description'])?$get_meta_tag_fb['description']:"";

                    if(strlen($feed_url_des)>80)
                    $feed_url_subtitle=substr($feed_url_des,0,77)."...";
                    else $feed_url_subtitle=$feed_url_des;

                    if(strlen($feed_url_title)>80)
                    $feed_url_title=substr($feed_url_title,0,77)."...";

                    $broadcast_schedule_time_gapped=$broadcast_schedule_time;
                    if($valid_feed<=3) // if there is a small amount of feeds then we will try to post in first hour
                    {
                       $broadcast_schedule_time_gapped = strtotime($broadcast_schedule_time.' + '.$broadcast_gap_minute.' minute');
                       $broadcast_schedule_time_gapped = date('Y-m-d H:i:s', $broadcast_schedule_time_gapped);
                    }
                    else // if there is a large amount of feeds then we will try to span the feed post process to cover whole timeslot
                    {
                       if($broadcast_gap_minute>0)
                       {
                           $broadcast_schedule_time_gapped = strtotime($broadcast_schedule_time.' + '.$broadcast_gap_minute.' minute');
                           $broadcast_schedule_time_gapped = date('Y-m-d H:i:s', $broadcast_schedule_time_gapped);
                       }
                    }


                    if($value["page_id"]!="")
                    {
                        $post_data=array
                        (                        
                            "campaign_name" => $feed_name." [RSS Autopost]",
                            "fb_page_id" => $value["fb_page_id"],
                            "page" => $value["page_id"],
                            "notification_type" => $broadcast_notification_type,
                            "display_unsubscribe" => $broadcast_display_unsubscribe,
                            "label_ids" => $value['label_ids'],
                            "excluded_label_ids" => $value['excluded_label_ids'],
                            "template_type_1" => "generic template",    
                            "generic_template_image_1" => $feed_url_image,
                            "generic_template_image_destination_link_1" => $post_feed_url,
                            "generic_template_title_1" => $feed_url_title,
                            "generic_template_subtitle_1" => $feed_url_subtitle,
                            "generic_template_button_text_1_1" => "Unsubscribe",
                            "generic_template_button_type_1_1" => "post_back",                        
                            "generic_template_button_post_id_1_1" => "UNSUBSCRIBE_QUICK_BOXER",                        
                            "schedule_type" => "later",
                            "time_zone" => $broadcast_timezone,
                            "schedule_time" => $broadcast_schedule_time_gapped,
                            "user_id" => $user_id
                        );

                        // curl api to create auto post quick bulk broadcast campaign
                        $url=base_url("messenger_broadcaster/rss_autoposting_quick_broadcast_cron_call/".$api_key);                       
                        $post_data = json_encode($post_data);
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                        $st=curl_exec($ch);  
                        $result=json_decode($st,TRUE);
                        curl_close($ch);

                        if(isset($result['status']) && $result['status']=='0') // checking and logging if any error found
                        { 
                            $error_message=isset($result['message'])?$result['message']:$this->lang->line("something went wrong while creating broadcast campaign.");
                            $error_message.=" [Broadcast]";
                            $error_row=array("time"=>$datetime,"message"=>$error_message);
                            array_push($error_log, $error_row);
                            $this->basic->update_data("autoposting",array("id"=>$value['id']),array("last_updated_at"=>$datetime,"error_message"=>json_encode($error_log)));                        
                        }

                    }
                    //processing broadcasting
                }
                
            } 
            $this->basic->update_data("autoposting",array("id"=>$value['id']),array("last_pub_date"=>$new_last_pub_date,"last_pub_title"=>$new_last_pub_title,"last_pub_url"=>$new_last_pub_url));
            
        } 

        $time_zone = $this->config->item('time_zone');
        if($time_zone== '') $time_zone="Europe/Dublin";
        date_default_timezone_set($time_zone);
        $this->db->where_in("id",$all_feed_id);
        $this->db->update("autoposting",array("cron_status"=>"0","last_updated_at"=>date("Y-m-d H:i:s")));
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
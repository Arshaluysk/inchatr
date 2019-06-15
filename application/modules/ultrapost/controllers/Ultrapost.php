<?php
/*
Addon Name: Ultra Post
Unique Name: ultrapost
Module ID: 220
Project ID: 19
Addon URI: http://getfbinboxer.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 1.0
Description: .
*/

require_once("application/controllers/Home.php"); // loading home controller

class Ultrapost extends Home
{
    public $addon_data=array();
    public function __construct()
    {
        parent::__construct();

        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path); 

        $this->member_validity();

        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it

        $function_name=$this->uri->segment(2);
        if($function_name!="text_image_link_video_auto_poster_cron_job" && $function_name!="cta_poster_cron_job" && $function_name!="offer_post_cron_job" && $function_name!="carousel_slider_cron_job") 
        {          
            // all addon must be login protected
            //------------------------------------------------------------------------------------------
            if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');

            // if you want the addon to be accessed by admin and member who has permission to this addon
            //-------------------------------------------------------------------------------------------
            // if(isset($addon_data['module_id']) && is_numeric($addon_data['module_id']) && $addon_data['module_id']>0)
            // {
            //     if($this->session->userdata('user_type') != 'Admin' && !in_array($addon_data['module_id'],$this->module_access))
            //     redirect('home/login_page', 'location');
            // }
        }

    }


    public function index()
    {
    }


    public function text_image_link_video()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(223,$this->module_access)) exit();
        $data['page_title'] = $this->lang->line("Text/Image/Link/Video Poster");
        
        $data['body'] = 'text_image_link_video_post/auto_post_list';
        $this->_viewcontroller($data);
    }

    public function text_image_link_video_poster()
    {
        if ($this->config->item('facebook_poster_group_enable_disable') == '' || $this->config->item('facebook_poster_group_enable_disable')=='0')
            $data['facebook_poster_group'] = '0';
        else
            $data['facebook_poster_group'] = '1';

        $data['page_title'] = $this->lang->line("Text/Image/Link/Video Poster");
        $data['body'] = 'text_image_link_video_post/add_auto_post';
        $data["time_interval"] = $this->get_periodic_time();
        $data["time_zone"]= $this->_time_zone_list();

        $user_infos = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        if ( count( $user_infos ) == 0 ) 
            return redirect( base_url( 'facebook_rx_account_import/index' ), 'location' );

        $data["fb_user_info"] = $user_infos;
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $app_info=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));
        $data["app_info"] = $app_info;
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));

        $this->load->library('fb_rx_login');
        $this->fb_rx_login->app_initialize($this->session->userdata("fb_rx_login_database_id"));
        $app_id = isset($app_info[0]['api_id']) ? $app_info[0]['api_id'] : 0;
        $app_secret = isset($app_info[0]['api_secret']) ? $app_info[0]['api_secret'] : 0;
        $current_app_info = $this->fb_rx_login->app_info_graber($app_id,$app_secret);
        $data['current_app_name'] = isset($current_app_info['name']) ? $current_app_info['name'] : "";
        $data['current_app_link'] = isset($current_app_info['link']) ? $current_app_info['link'] : "";
        $data['current_app_photo_url'] = isset($current_app_info['photo_url']) ? $current_app_info['photo_url'] : "";
   
        $this->_viewcontroller($data);
    }


    public function text_image_link_video_auto_post_list_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');


        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $campaign_name = trim($this->input->post("campaign_name", true));
        $page_or_group_or_user_name = trim($this->input->post("page_or_group_or_user_name", true));
        $post_type = trim($this->input->post("post_type", true));

        $scheduled_from = trim($this->input->post('scheduled_from', true));
        if($scheduled_from) $scheduled_from = date('Y-m-d', strtotime($scheduled_from));

        $scheduled_to = trim($this->input->post('scheduled_to', true));
        if($scheduled_to) $scheduled_to = date('Y-m-d', strtotime($scheduled_to));


        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched)
        {
            $this->session->set_userdata('facebook_rx_auto_poster_campaign_name', $campaign_name);
            $this->session->set_userdata('facebook_rx_auto_poster_page_or_group_or_user_name', $page_or_group_or_user_name);
            $this->session->set_userdata('facebook_rx_auto_poster_scheduled_from', $scheduled_from);
            $this->session->set_userdata('facebook_rx_auto_poster_scheduled_to', $scheduled_to);
            $this->session->set_userdata('facebook_rx_auto_poster_post_type', $post_type);
        }

        $search_campaign_name  = $this->session->userdata('facebook_rx_auto_poster_campaign_name');
        $search_page_or_group_or_user_name  = $this->session->userdata('facebook_rx_auto_poster_page_or_group_or_user_name');
        $search_scheduled_from = $this->session->userdata('facebook_rx_auto_poster_scheduled_from');
        $search_scheduled_to = $this->session->userdata('facebook_rx_auto_poster_scheduled_to');
        $search_post_type= $this->session->userdata('facebook_rx_auto_poster_post_type');

        $where_simple=array();

        if ($search_campaign_name) $where_simple['campaign_name like ']    = "%".$search_campaign_name."%";
        if ($search_page_or_group_or_user_name) $where_simple['page_or_group_or_user_name like ']    = "%".$search_page_or_group_or_user_name."%";
        if ($search_post_type) $where_simple['post_type']  = $search_post_type;
        if ($search_scheduled_from)
        {
            if ($search_scheduled_from != '1970-01-01')
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') >="]= $search_scheduled_from;

        }
        if ($search_scheduled_to)
        {
            if ($search_scheduled_to != '1970-01-01')
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') <="]=$search_scheduled_to;

        }


        $where_simple['user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        $this->db->where("(is_child='0' or posting_status='2')");
        $where  = array('where'=>$where_simple);
      

        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "facebook_rx_auto_post";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

     
        $this->db->where("(is_child='0' or posting_status='2')");
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++)
        {   
            $posting_status = $info[$i]['posting_status'];
            $full_complete =  $info[$i]['full_complete'];
            $schedule_type = $info[$i]['schedule_type'];
            $parent_id = $info[$i]['parent_campaign_id'];
            $repeat_times = $info[$i]['repeat_times'];

            $allschedulepost_check = $this->basic->get_data("facebook_rx_auto_post", array('where'=>array('parent_campaign_id'=>$info[$i]['id'])));
            
            if(count($allschedulepost_check)>0)
            {
                $completed_child_count=0;
                foreach ($allschedulepost_check as $key => $value12) 
                {                        
                    if ($value12['posting_status'] == '2' ) $completed_child_count++;
                }
                if(count($allschedulepost_check) == $completed_child_count) $is_all_posted='1';
                else $is_all_posted='0';
            }
            else
            {
                if($posting_status=='2') $is_all_posted='1';
                else $is_all_posted='0';
            }
           
            if(($posting_status=='0' || $posting_status == '2')  && ($schedule_type == 'later' && $parent_id == '0' && $is_all_posted=='0' ))
                $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-check-circle gray"></i> '.$this->lang->line("not all completed").'</span>';
            else if( $posting_status == '2') $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("Completed").'</span>';
            else if( $posting_status == '1') $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-spinner orange"></i> '.$this->lang->line("Processing").'</span>';
            else $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-remove red"></i> '.$this->lang->line("Pending").'</span>';

            if($schedule_type == 'later' && $parent_id == '0')
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle blue"></i> '.$this->lang->line("main campaign").'</span>';
            else if($schedule_type == 'now')
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle red"></i> '.$this->lang->line("single campaign").'</span>';
            else
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("sub campaign").'</span>';


            $post_type = $info[$i]['post_type'];
            $post_type = ucfirst(str_replace("_submit","",$post_type));
            $info[$i]['post_type'] =  $post_type;

            $publisher = ucfirst($info[$i]['page_or_group_or_user'])." : ".$info[$i]['page_or_group_or_user_name'];
            $info[$i]['publisher'] =  $publisher;

            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
            $scheduled_at = date("M j, y H:i",strtotime($info[$i]['schedule_time']));
            else $scheduled_at = '<i class="fa fa-remove red" title="'.$this->lang->line("Instantly posted").'"></i>';
            $info[$i]['scheduled_at'] =  $scheduled_at;

            // if(strlen($info[$i]["message"])>=60) $info[$i]["message_formatted"] = substr($info[$i]["message"], 0, 60)."...";
            // else $info[$i]["message_formatted"] = $info[$i]["message"];
           

            if($posting_status=='2')
            $post_url = "<a target='_BLANK' href='".$info[$i]['post_url']."' class='btn btn-outline-info' title='".$this->lang->line("Visit")."'><i class='fa fa-hand-o-right'></i></a>";
            else $post_url = "<a class='btn btn-outline-info border_gray gray' title='".$this->lang->line("This post is not published yet.")."'><i class='fa fa-hand-o-right'></i></a>";

             
           // echo "</ br>". $is_all_posted . "</ br>"; 
           
           //   echo $posting_status . "<br>";
           //   echo $schedule_type. "<br>";
           //   echo $parent_id. "<br>";
           //   exit;

            if(($posting_status=='0' || $posting_status == '2')  && ($schedule_type == 'later' && $parent_id == '0' && $is_all_posted=='0'))
            $editUrlAutoPost ="<a class='btn btn-outline-warning' title='".$this->lang->line("Edit")."' href='".base_url()."ultrapost/text_image_link_video_edit_auto_post/".$info[$i]['id']."'><i class='fa fa-edit'></i></a>";
            else 
            $editUrlAutoPost ="<a class='btn btn-outline-warning border_gray gray' title='".$this->lang->line("Only pending campaigns are editable")."'><i class='fa fa-edit'></i></a>";

            $info[$i]['delete'] =  "<a title='".$this->lang->line("Delete this post from our database")."' id='".$info[$i]['id']."' class='delete btn-sm btn btn-danger'><i class='fa fa-remove'></i> Delete</a>";

            if($schedule_type == 'later' && $parent_id == '0') {
                $deleteUrl =  "<a title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete_p btn btn-outline-danger'><i class='fa fa-trash'></i></a>";
            }
            else{
                $deleteUrl =  "<a title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete btn btn-outline-danger'><i class='fa fa-trash'></i></a>";
            }
           


            if ($schedule_type == 'later' && $parent_id == '0') {

                 $report_url = "<button class='btn btn-outline-info view_report' table_id='".$info[$i]['id']."' title='".$this->lang->line("report")."'><i class='fa fa-eye'></i></button>";  
            }
            else 
            {
                $report_url ="<a class='btn btn-outline-info border_gray gray' title='".$this->lang->line("Only parent campaign has shown report")."'><i class='fa fa-eye'></i></a>";
            }
            

            if($post_type=="Video" && $posting_status=='2')
            $embedUrl =  "<a title='".$this->lang->line("Embed Code")."' id='".$info[$i]['id']."' class='embed_code btn btn-outline-primary'><i class='fa fa-code'></i></a>";
            else
            $embedUrl =  "<a title='".$this->lang->line("Embed code is only available for published video posts.")."' class='btn btn-outline-primary border_gray gray'><i class='fa fa-code'></i></a>";

            $info[$i]['actions'] = $post_url."&nbsp;".$editUrlAutoPost."&nbsp;".$deleteUrl."&nbsp;".$embedUrl."&nbsp;".$report_url;

        }
        echo convert_to_grid_data($info, $total_result);
    }


    public function text_image_link_video_delete_post()
    {
       
       if(!$_POST) exit();
       $id=$this->input->post("id");
       $post_info = $this->basic->get_data('facebook_rx_auto_post',array('where'=>array('id'=>$id)));
       if($post_info[0]['posting_status'] != '2')
       {
           //******************************//
           // delete data to useges log table
           $this->_delete_usage_log($module_id=223,$request=1);
           //******************************//
       }

       if($this->basic->delete_data("facebook_rx_auto_post",array("id"=>$id,"user_id"=>$this->user_id)))
       {
           $this->basic->delete_data("facebook_rx_auto_post",array("parent_campaign_id"=>$id,"user_id"=>$this->user_id));
           echo "1";
       }
       else echo "0";

    }


    public function text_image_link_video_get_embed_code()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");

        $video_data = $this->basic->get_data("facebook_rx_auto_post",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));
        $post_url= isset($video_data[0]['post_url']) ? $video_data[0]['post_url']:"";

       $embed_code= '<iframe src="https://www.facebook.com/plugins/video.php?href='.$post_url.'&show_text=0&width=600" width="600" height="600" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>';

        $embed_html = "<center><b>Copy this embed code : </b><br/><input style='height:40px;width:100%' type='text' value='".$embed_code."'><br/><br/><b>Preview :</b> <br/>". $embed_code."</center>";
        echo $embed_html;

    }


    public function text_image_link_video_add_auto_post_action()
    {
        if(!$_POST)
        exit();

        $this->load->library("fb_rx_login");

        $post=$_POST;
        foreach ($post as $key => $value)
        {
           $$key=$value;
           if(!is_array($value)){

                if($key == "auto_reply_template")
                    $insert_data['ultrapost_auto_reply_table_id'] = $value;
                else
                    $insert_data[$key]=$value;
           }
        }

        $image_list = explode(',', $image_url);

        $insert_data["post_type"] = $insert_data["submit_post_hidden"];
        unset($insert_data["submit_post_hidden"]);
        unset($insert_data["post_to_profile"]);
        $insert_data["schedule_type"];

        //************************************************//
        $request_count1 = 0;
        $request_count2 = 0;
        $request_count = 0;
        if(isset($post_to_pages))
        $request_count1 = count($post_to_pages);
        if(isset($post_to_groups))
        $request_count2 = count($post_to_groups);
        $request_count = $request_count1+$request_count2;
        $times = 0;
        $times = $repeat_times;
        $interval= $time_interval; 
        // $change_time= date('Y-m-d H:i:s',$sc);
        $status=$this->_check_usage($module_id=223,$request=$request_count);
        if($status=="2")
        {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        else if($status=="3")
        {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        //************************************************//

        $insert_data["auto_share_to_profile"]= "0";
        // $insert_data["ultrapost_auto_reply_table_id"]= $auto_reply_template;
        

        $insert_data["user_id"] = $this->user_id;
        $insert_data["facebook_rx_fb_user_info_id"] = $this->session->userdata("facebook_rx_fb_user_info");

        
        if(!isset($post_to_pages) || !is_array($post_to_pages)) $post_to_pages=array();
        if(!isset($post_to_groups) || !is_array($post_to_groups)) $post_to_groups=array();

        $page_ids_string = implode(',', $post_to_pages);
        $insert_data["page_ids"] = $page_ids_string;
        $group_ids_string = implode(',', $post_to_groups);
        $insert_data["group_ids"] = $group_ids_string;

        $auto_share_this_post_by_pages = array();
        $auto_share_this_post_by_pages_new = array_diff($auto_share_this_post_by_pages,$post_to_pages);
        $insert_data["auto_share_this_post_by_pages"] = json_encode($auto_share_this_post_by_pages_new);

        $insert_data["auto_private_reply_status"] = "0";
        $insert_data["auto_private_reply_count"] = 0;
        $insert_data["auto_private_reply_done_ids"] = json_encode(array());

        // $insert_data['repeat_times'] = $times;
        $insert_data['time_interval'] = $interval;
        $insert_data["post_auto_comment_cron_jon_status"] = "0";
        $insert_data["post_auto_like_cron_jon_status"] = "0";
        $insert_data["post_auto_share_cron_jon_status"] = "0";
        

        if($video_url!="")
        {
            if(strpos($video_url, 'youtube.com') !== false)
            {
                parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
                $youtube_video_id = isset($my_array_of_vars['v']) ? $my_array_of_vars['v'] : "";

                if($youtube_video_id!="")
                {
                    $video_url = $this->fb_rx_login->get_youtube_video_url($youtube_video_id);
                    $insert_data["video_url"] = $video_url;
                }
            }
        }


        if($schedule_type=="now")
        {
            $insert_data["posting_status"] ='2';
            $insert_data["full_complete"]  ='1';
        }
        else
        {
            $insert_data["posting_status"] ='0';
    
        }


        $insert_data_batch=array();

        $user_id_array=array($this->user_id);
        $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info"); // table > facebook_rx_fb_user_info.id
        $count=0;


        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

        foreach ($page_info as $key => $value)
        {
            if(!in_array($value["id"], $post_to_pages)) continue;

            $page_access_token =  isset($value["page_access_token"]) ? $value["page_access_token"] : "";
            $fb_page_id =  isset($value["page_id"]) ? $value["page_id"] : "";

            $insert_data_batch[$count]=$insert_data;

            $page_auto_id =  isset($value["id"]) ? $value["id"] : "";
            $insert_data_batch[$count]["page_group_user_id"]=$page_auto_id;
            $insert_data_batch[$count]["page_or_group_or_user"]="page";
            $insert_data_batch[$count]["page_or_group_or_user_name"] = isset($value["page_name"]) ? $value["page_name"] : "";
            $insert_data_batch[$count]["post_id"] = "";
            $insert_data_batch[$count]["post_url"] = "";
            // $insert_data_batch[$count]["ultrapost_auto_reply_table_id"] = $auto_reply_template;

            if($schedule_type=="now")
            {
                if($this->is_demo == '1')
                    if($this->user_id == 1)
                        continue;
                if($submit_post_hidden=="text_submit")
                {
                    try
                    {
                        $response = $this->fb_rx_login->feed_post($message,"","","","","",$page_access_token,$fb_page_id);
                    }
                    catch(Exception $e)
                    {
                      $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                      $return_val=array("status"=>"0","message"=>$error_msg);
                      echo json_encode($return_val);
                      exit();
                    }
                }

                else if($submit_post_hidden=="link_submit")
                {
                    try
                    {
                        $response = $this->fb_rx_login->feed_post($message,$link,"","","","",$page_access_token,$fb_page_id);
                    }
                    catch(Exception $e)
                    {
                      $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                      $return_val=array("status"=>"0","message"=>$error_msg);
                      echo json_encode($return_val);
                      exit();
                    }
                }


                else if($submit_post_hidden=="image_submit")
                {

                    if(count($image_list) == 1)
                    {                    
                        try
                        {
                            $response = $this->fb_rx_login->photo_post($message,$image_list[0],"",$page_access_token,$fb_page_id);
                        }
                        catch(Exception $e)
                        {
                            $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                            $return_val=array("status"=>"0","message"=>$error_msg);
                            echo json_encode($return_val);
                            exit();
                        }
                    }
                    else
                    {
                        $multi_image_post_response_array = array();
                        $attach_media_array = array();
                        foreach ($image_list as $key => $value) {
                            try
                            {
                                $response = $this->fb_rx_login->photo_post_for_multipost($message,$value,"",$page_access_token,$fb_page_id);
                                $attach_media_array['media_fbid'] = $response['id'];
                                $multi_image_post_response_array[] = $attach_media_array;
                            }
                            catch(Exception $e)
                            {
                                $error_msg = $e->getMessage();
                            }
                        }


                        try
                        {
                            $response = $this->fb_rx_login->multi_photo_post($message,$multi_image_post_response_array,"",$page_access_token,$fb_page_id);
                        }
                        catch(Exception $e)
                        {
                            $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                            $return_val=array("status"=>"0","message"=>$error_msg);
                            echo json_encode($return_val);
                            exit();
                        }
                    }


                }

                else
                {
                    try
                    {
                        $response = $this->fb_rx_login->post_video($message,"",$video_url,"",$video_thumb_url,"",$page_access_token,$fb_page_id);
                    }
                    catch(Exception $e)
                    {
                      $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                      $return_val=array("status"=>"0","message"=>$error_msg);
                      echo json_encode($return_val);
                      exit();
                    }
                    $insert_data_batch[$count]["post_auto_comment_cron_jon_status"] = "0";
                    $insert_data_batch[$count]["post_auto_like_cron_jon_status"] = "0";
                    $insert_data_batch[$count]["post_auto_share_cron_jon_status"] = "0";
                }

                if($submit_post_hidden=="image_submit")
                {
                    if(count($image_list) > 1)
                    $object_id=isset($response["id"]) ? $response["id"] : "";
                    else
                    $object_id=isset($response["post_id"]) ? $response["post_id"] : "";
                }
                else $object_id=$response["id"];



                $share_access_token = $page_access_token;

                $insert_data_batch[$count]["post_id"]= $object_id;
                $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$page_access_token);
                $insert_data_batch[$count]["post_url"]= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";
                $insert_data_batch[$count]["last_updated_at"]= date("Y-m-d H:i:s");


                $this->basic->insert_data("facebook_rx_auto_post",$insert_data_batch[$count]);
                

                if(isset($insert_data['ultrapost_auto_reply_table_id']) && $insert_data['ultrapost_auto_reply_table_id'] != '0')
                {     
                    //************************************************//
                    $status=$this->_check_usage($module_id=204,$request=1);
                    if($status!="2" && $status!="3") 
                    {

                        $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $insert_data['ultrapost_auto_reply_table_id'] ]]);

                        $auto_reply_table_data = [];

                        foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                            foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                                
                                if($auto_key == 'id')
                                    continue;

                                if($auto_key == 'ultrapost_campaign_name')
                                    $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                                else
                                    $auto_reply_table_data[$auto_key] = $auto_value;
                            }
                        }



                        $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $value['facebook_rx_fb_user_info_id'];
                        $auto_reply_table_data['page_info_table_id'] = $value['id'];
                        $auto_reply_table_data['page_name'] = $value['page_name'];

                        if($submit_post_hidden=="video_submit")
                            $auto_reply_table_data['post_id'] = $value['page_id'].'_'.$object_id;
                        else
                            $auto_reply_table_data['post_id'] = $object_id;

                        $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                        $auto_reply_table_data['post_description'] = $message;
                        $auto_reply_table_data['auto_private_reply_status'] = '0';

                        $auto_reply_table_data['auto_private_reply_count'] = 0;
                        $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                        $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                        $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                        $auto_reply_table_data['last_reply_time'] = '';
                        $auto_reply_table_data['error_message'] = '';
                        $auto_reply_table_data['hidden_comment_count'] = 0;
                        $auto_reply_table_data['deleted_comment_count'] = 0;
                        $auto_reply_table_data['auto_comment_reply_count'] = 0;
                       
                        $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                    
                        $this->_insert_usage_log($module_id=204,$request=1);                        
                    }
                   //************************************************//
                }



            }

            $count++;

        }


        if(count($post_to_groups)>0)
        {
            $group_info = $this->basic->get_data("facebook_rx_fb_group_info",array("where_in"=>array("id"=>$post_to_groups,"user_id"=>$user_id_array)));
            foreach ($group_info as $key => $value)
            {
                $group_access_token =  isset($value["group_access_token"]) ? $value["group_access_token"] : "";  // this is user access token, group has no access token actually
                $fb_group_id =  isset($value["group_id"]) ? $value["group_id"] : "";

                $insert_data_batch[$count]=$insert_data;
                $group_auto_id =  isset($value["id"]) ? $value["id"] : "";
                $insert_data_batch[$count]["page_group_user_id"]=$group_auto_id;
                $insert_data_batch[$count]["page_or_group_or_user"]="group";
                $insert_data_batch[$count]["page_or_group_or_user_name"] = isset($value["group_name"]) ? $value["group_name"] : "";
                $insert_data_batch[$count]["post_id"] = "";
                $insert_data_batch[$count]["post_url"] = "";

                if($schedule_type=="now")
                {
                    if($submit_post_hidden=="text_submit")
                    {
                        try
                        {
                            $response = $this->fb_rx_login->feed_post($message,"","","","","",$group_access_token,$fb_group_id);
                        }
                        catch(Exception $e)
                        {
                          $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                          $return_val=array("status"=>"0","message"=>$error_msg);
                          echo json_encode($return_val);
                          exit();
                        }
                    }

                    else if($submit_post_hidden=="link_submit")
                    {
                        try
                        {
                            $response = $this->fb_rx_login->feed_post($message,$link,"","","","",$group_access_token,$fb_group_id);
                        }
                        catch(Exception $e)
                        {
                          $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                          $return_val=array("status"=>"0","message"=>$error_msg);
                          echo json_encode($return_val);
                          exit();
                        }
                    }

                    else if($submit_post_hidden=="image_submit")
                    {

                        if(count($image_list) == 1)
                        {                    
                            try
                            {
                                $response = $this->fb_rx_login->photo_post($message,$image_list[0],"",$group_access_token,$fb_group_id);
                            }
                            catch(Exception $e)
                            {
                                $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                                $return_val=array("status"=>"0","message"=>$error_msg);
                                echo json_encode($return_val);
                                exit();
                            }
                        }
                        else
                        {
                            $multi_image_post_response_array = array();
                            $attach_media_array = array();
                            foreach ($image_list as $key => $value) {
                                try
                                {
                                    $response = $this->fb_rx_login->photo_post_for_multipost($message,$value,"",$group_access_token,$fb_group_id);
                                    $attach_media_array['media_fbid'] = $response['id'];
                                    $multi_image_post_response_array[] = $attach_media_array;
                                }
                                catch(Exception $e)
                                {
                                    $error_msg = $e->getMessage();
                                }
                            }


                            try
                            {
                                $response = $this->fb_rx_login->multi_photo_post($message,$multi_image_post_response_array,"",$group_access_token,$fb_group_id);
                            }
                            catch(Exception $e)
                            {
                                $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                                $return_val=array("status"=>"0","message"=>$error_msg);
                                echo json_encode($return_val);
                                exit();
                            }
                        }


                    }

                    else
                    {
                        try
                        {
                            $response = $this->fb_rx_login->post_video($message,"",$video_url,"",$video_thumb_url,"",$group_access_token,$fb_group_id);
                        }
                        catch(Exception $e)
                        {
                          $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                          $return_val=array("status"=>"0","message"=>$error_msg);
                          echo json_encode($return_val);
                          exit();
                        }

                        $insert_data_batch[$count]["post_auto_comment_cron_jon_status"] = "0";
                        $insert_data_batch[$count]["post_auto_like_cron_jon_status"] = "0";
                        $insert_data_batch[$count]["post_auto_share_cron_jon_status"] = "0";

                    }

                    if($submit_post_hidden=="image_submit")
                    {
                        if(count($image_list) > 1)
                        $object_id=isset($response["id"]) ? $response["id"] : "";
                        else
                        $object_id=isset($response["post_id"]) ? $response["post_id"] : "";
                    }
                    else $object_id=$response["id"];
                    $share_access_token = $group_access_token;

                    $insert_data_batch[$count]["post_id"]= $object_id;
                    $insert_data_batch[$count]["last_updated_at"]= date("Y-m-d H:i:s");
                    $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$group_access_token);
                    $insert_data_batch[$count]["post_url"]= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";
                    $this->basic->insert_data("facebook_rx_auto_post",$insert_data_batch[$count]);
                    //insert data to useges log table
                    $this->_insert_usage_log($module_id=223,$request=1);

                }
                $count++;

            }
        }



        $profile_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=> $account_switching_id,"user_id"=>$this->user_id)));
        $user_access_token =  isset($profile_info[0]["access_token"]) ? $profile_info[0]["access_token"] : "";
        $user_fb_id =  isset($profile_info[0]["fb_id"]) ? $profile_info[0]["fb_id"] : "";
        $user_fb_name =  isset($profile_info[0]["name"]) ? $profile_info[0]["name"] : "";     

       if($schedule_type=="now") $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i>  ".$this->lang->line("Facebook post has been performed successfully."));
       

       else
       {
           
            $page_info_arr = array();
            $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

            foreach ($page_info as $key => $value) {
               if(!in_array($value["id"], $post_to_pages)) continue;
               $page_info_arr[$value["id"]] = $value['page_name'];
            }

            if(count($post_to_groups)>0)
            {   
                $group_info_arr = array();
                $group_info = $this->basic->get_data("facebook_rx_fb_group_info",array("where_in"=>array("id"=>$post_to_groups,"user_id"=>$user_id_array)));
                foreach ($group_info as $key => $value) {
                   if(!in_array($value["id"], $post_to_groups)) continue;
                   $group_info_arr[$value["id"]] = $value['group_name'];
                }
            }



            $parent_id='';
            for ($insert_counter=0; $insert_counter < $request_count1; $insert_counter++) { 

               $insert_data['page_or_group_or_user'] = 'page';
               $insert_data['page_group_user_id'] = $post['post_to_pages'][$insert_counter];
               $insert_data['page_or_group_or_user_name'] = $page_info_arr[$insert_data['page_group_user_id']];

               $x=$post['time_interval'];
               for ($i=0; $i <= $times ; $i++) { 
                   
                    if($i == 0)
                    {

                        $insert_data['schedule_time']= $post['schedule_time'];
                        $this->basic->insert_data('facebook_rx_auto_post',$insert_data);
                        $insert_id = $this->db->insert_id();
                        if($i == 0 && $insert_counter == 0)
                            $parent_id = $insert_id;
                    }
                    if ($times == 0) {
                          $insert_data['is_child'] = '1';
                          $insert_data['parent_campaign_id'] = $parent_id;
                    
                    }

                    if($i >= 1)
                    {

                        $insert_data['is_child'] = '1';
                        $current_schedule_time = $post['schedule_time'];
                        $dateTime = new DateTime($current_schedule_time);
                        $p = $i*$x;
                        $dateTime->modify("+{$p} minutes");
                        $insert_data['parent_campaign_id'] = $parent_id;
                        $change_time= $dateTime->format('Y-m-d H:i:s');
                        $insert_data['schedule_time']= $change_time;
                         unset($insert_data['page_ids']);
                         unset($insert_data['group_ids']);
                        $this->basic->insert_data('facebook_rx_auto_post',$insert_data);
                    }
               }

            }
            if(count($post_to_groups)>0)
            { 
                for ($insert_counter=0; $insert_counter < $request_count2; $insert_counter++) { 

                   $insert_data['page_or_group_or_user'] = 'group';
                   $insert_data['page_group_user_id'] = $post['post_to_groups'][$insert_counter];
                   $insert_data['page_or_group_or_user_name'] = $group_info_arr[$insert_data['page_group_user_id']];

                   $y = $post['time_interval'];
                   for ($i=0; $i <= $times ; $i++) { 
                       
                        if($i == 0)
                        {

                            $insert_data['schedule_time']= $post['schedule_time'];
                            $this->basic->insert_data('facebook_rx_auto_post',$insert_data);
                            $insert_id = $this->db->insert_id();
                
                        }
                        if($i >= 1)
                        {


                            $insert_data['is_child'] = '1';
                            $current_schedule_time = $post['schedule_time'];
                            $insert_data['parent_campaign_id'] = $parent_id;
                            $dateTime = new DateTime($current_schedule_time);
                            $p = $i*$y;
                            $dateTime->modify("+{$p} minutes");
                            $change_time= $dateTime->format('Y-m-d H:i:s');
                            $insert_data['schedule_time']= $change_time;
                             unset($insert_data['page_ids']);
                             unset($insert_data['group_ids']);
                            $this->basic->insert_data('facebook_rx_auto_post',$insert_data);
                        }
                   }




                }
            }


            if($insert_counter > 0)
            {
                $number_request = count($insert_data_batch);
                $this->_insert_usage_log($module_id=223,$request=$number_request);
                $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Facebook post campaign has been created successfully."));
            }
            else $return_val=array("status"=>"0","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("something went wrong, please try again."));
       }

       echo json_encode($return_val);


    }

    public function ajax_get_text_report()
    {

        $table_id = $this->input->post('table_id');
        $text_info = $this->basic->get_data('facebook_rx_auto_post',array('where'=>array('parent_campaign_id'=>$table_id),'or_where'=>array('id'=>$table_id)));

        if(isset($text_info[0]['id']) && $text_info[0]['id'] != '')
        {
            $str = '<script>
                    $j(document).ready(function() {
                        $("#campaign_report").DataTable();
                    }); 
                 </script>
                 <div class="table-responsive">
                 <table id="campaign_report" class="table table-bordered">
                     <thead>
                         <tr>
                             <th class="text-center">'.$this->lang->line("posting page or group").'</th>
                             <th class="text-center">'.$this->lang->line("posting status").'</th>
                             <th class="text-center">'.$this->lang->line("post id").'</th>
                             <th class="text-center">'.$this->lang->line("schedule time").'</th>
                             <th class="text-center">'.$this->lang->line("post type").'</th>
                             <th class="text-center">'.$this->lang->line("Error").'</th>
                             
                         </tr>
                     </thead>
                     <tbody>';
                         
                     
            // $info = json_decode($reply_info[0]['auto_reply_done_info'],true);
        

            foreach($text_info as $value)
            {
               
               
                $posting_status = isset($value['posting_status']) ? $value['posting_status']:"";
               
                if($posting_status=='2')
                $posting_status='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("completed").'</span>';
                if($posting_status == '0')
                $posting_status='<span class="label label-light"><i class="fa fa-check-circle red"></i> '.$this->lang->line("pending").'</span>';
            	if($posting_status == '1')
            	$posting_status='<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("processing").'</span>';

                $post_type =  isset($value['post_type']) ? $value['post_type']:"";
                
                if ($post_type == 'text_submit') 
                    $post_type = '<span class="label label-light"><i class="fa fa-check-circle blue"></i> '.$this->lang->line("text").'</span>';       
                if ($post_type == 'image_submit') 
                    $post_type = '<span class="label label-light"><i class="fa fa-check-circle blue2"></i> '.$this->lang->line("image").'</span>';       
                if ($post_type == 'link_sibmit') 
                    $post_type = '<span class="label label-light"><i class="fa fa-check-circle gray"></i> '.$this->lang->line("link").'</span>';       
                if ($post_type == 'video_submit') 
                    $post_type = '<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("video").'</span>';




                $str .= '<tr>
                            <td>'.$value['page_or_group_or_user'].' : '.$value['page_or_group_or_user_name'].'</td>
                            <td class="text-center">'.$posting_status.'</td>
                            <td class="text-center"><a target="_BLANK" href="http://facebook.com/'.$value['post_id'].'" class="product-title">'.$value['post_id'].'</a></td>
                            <td class="text-center">'.$value['schedule_time'].'</td>
                            <td class="text-center">'.$post_type.'</td>
                            <td class="text-center">'.$value['error_mesage'].'</td>
                            
                        </tr>';
            }

            $str .= '</tbody>
                 </table></div>';
        }
        else
        {
            $str = "<div class='alert alert-danger'>{$this->lang->line("no data to show")}</div>";
        }

        echo $str;
    }


    public function get_fb_rx_config($fb_user_id=0)
    {
        if($fb_user_id==0) return 0;

        $getdata= $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=>$fb_user_id)),array("facebook_rx_config_id"));
        $return_val = isset($getdata[0]["facebook_rx_config_id"]) ? $getdata[0]["facebook_rx_config_id"] : 0;

        return $return_val;

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


    public function text_image_link_video_auto_poster_cron_job($api_key="")
    {
        $this->api_key_check($api_key);
        // $this->load->library("fb_rx_login");


        if($this->is_demo == '1')
        $where['where']=array("posting_status"=>"0", "facebook_rx_auto_post.user_id !="=>1);
        else
        $where['where']=array("posting_status"=>"0");
        /***   Taking fist 200 post for auto post ***/
        $post_info= $this->basic->get_data("facebook_rx_auto_post",$where,$select='',$join='',$limit=25, $start=0, $order_by='schedule_time ASC');


        $database = array();

        $campaign_id_array=array();

        // $count_post = 0;

        foreach($post_info as $info)
        {
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            if($time_zone) date_default_timezone_set($time_zone);
            $now_time = date("Y-m-d H:i:s");

            if(strtotime($now_time) < strtotime($schedule_time)) continue;

            $campaign_id_array[] = $info['id'];
        }

        if(empty($campaign_id_array)) exit();

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("facebook_rx_auto_post",array("posting_status"=>"1"));

        $config_id_database = array();
        foreach($post_info as $info)
        {
            $campaign_id= $info['id'];

            if(!in_array($campaign_id, $campaign_id_array)) continue;

            $post_type= $info['post_type'];
            $page_group_user_id= $info["page_group_user_id"];
            $page_or_group_or_user= $info["page_or_group_or_user"];
            $user_id= $info['user_id'];
            $message =$info['message'];
            $link =$info['link'];
            $link_preview_image =$info['link_preview_image'];
            $link_caption =$info['link_caption'];
            $link_description =$info['link_description'];
            $image_url =$info['image_url'];
            $video_title =$info['video_title'];
            $video_url =$info['video_url'];
            $video_thumb_url =$info['video_thumb_url'];
            $link =$info['link'];

            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            $this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->load->library("fb_rx_login");
            // setting fb confid id for library call


            if($page_or_group_or_user=="page")
            {
                $table_name = "facebook_rx_fb_page_info";
                $fb_id_field =  "page_id";
                $access_token_field =  "page_access_token";
            }
            else if($page_or_group_or_user=="user")
            {
                $table_name = "facebook_rx_fb_user_info";
                $fb_id_field =  "fb_id";
                $access_token_field =  "access_token";
            }
            else
            {
                $table_name = "facebook_rx_fb_group_info";
                $fb_id_field =  "group_id";
                $access_token_field =  "group_access_token";

            }

            if(!isset($database[$page_or_group_or_user][$page_group_user_id])) // if not exists in database
            {
                $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));

                $use_access_token = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";
                $use_fb_id = isset($access_data["0"][$fb_id_field]) ? $access_data["0"][$fb_id_field] : "";

                //inserting new data in database
                $database[$page_or_group_or_user][$page_group_user_id] = array("use_access_token"=>$use_access_token,"use_fb_id"=>$use_fb_id);
            }

            $use_access_token = isset($database[$page_or_group_or_user][$page_group_user_id]["use_access_token"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_access_token"] : "";
            $use_fb_id = isset($database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"] : "";

            $response =array();
            $error_msg ="";
            if($post_type=="text_submit")
            {
                try
                {
                    $response = $this->fb_rx_login->feed_post($message,"","","","","",$use_access_token,$use_fb_id);
                }
                catch(Exception $e)
                {
                    $error_msg = $e->getMessage();
                }
            }

            else if($post_type=="link_submit")
            {
                try
                {
                    $response = $this->fb_rx_login->feed_post($message,$link,"","","","",$use_access_token,$use_fb_id);
                }
                catch(Exception $e)
                {
                    $error_msg = $e->getMessage();
                }
            }

            else if($post_type=="image_submit")
            {
                $image_list = explode(',', $image_url);
                if(count($image_list) == 1)
                {                    
                    try
                    {
                        $response = $this->fb_rx_login->photo_post($message,$image_list[0],"",$use_access_token,$use_fb_id);
                    }
                    catch(Exception $e)
                    {
                        $error_msg = $e->getMessage();
                    }
                }
                else
                {
                    $multi_image_post_response_array = array();
                    $attach_media_array = array();
                    foreach ($image_list as $key => $value) {
                        try
                        {
                            $response = $this->fb_rx_login->photo_post_for_multipost($message,$value,"",$use_access_token,$use_fb_id);
                            $attach_media_array['media_fbid'] = $response['id'];
                            $multi_image_post_response_array[] = $attach_media_array;
                        }
                        catch(Exception $e)
                        {
                            $error_msg = $e->getMessage();
                        }
                    }


                    try
                    {
                        $response = $this->fb_rx_login->multi_photo_post($message,$multi_image_post_response_array,"",$use_access_token,$use_fb_id);
                    }
                    catch(Exception $e)
                    {
                        $error_msg = $e->getMessage();
                    }
                }
            }

            else
            {
                try
                {
                    $response = $this->fb_rx_login->post_video($message,$video_title,$video_url,"",$video_thumb_url,"",$use_access_token,$use_fb_id);
                }
                catch(Exception $e)
                {
                    $error_msg = $e->getMessage();
                }
            }

            if($post_type=="image_submit")
            {
                if(count($image_list) > 1)
                $object_id=isset($response["id"]) ? $response["id"] : "";
                else
                $object_id=isset($response["post_id"]) ? $response["post_id"] : "";
                
            }
            else $object_id=isset($response["id"]) ? $response["id"] : "";

            $temp_data=array();
            try
            {
                $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$use_access_token);
            }
            catch(Exception $e)
            {
                $error_msg1 = $e->getMessage();
            }

            $post_url= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";

            $update_data = array("posting_status"=>'2',"full_complete"=>'1',"post_id"=>$object_id,"post_url"=>$post_url,"error_mesage"=>$error_msg,"last_updated_at"=>date("Y-m-d H:i:s"));

            $this->basic->update_data("facebook_rx_auto_post",array("id"=>$campaign_id),$update_data);



            if($info['ultrapost_auto_reply_table_id'] != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1);
                if($status!="2" && $status!="3") 
                {

                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $info['ultrapost_auto_reply_table_id'] ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }



                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];

                    if($post_type=="video_submit")
                        $auto_reply_table_data['post_id'] = $facebook_page_info[0]['page_id'].'_'.$object_id;
                    else
                        $auto_reply_table_data['post_id'] = $object_id;

                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                    $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                 
                    $this->_insert_usage_log($module_id=204,$request=1);                        
                 }
                //************************************************//
            }


            sleep(rand ( 1 , 6 ));

            // echo "$count_post<br>";
        }


    }



    public function text_image_link_video_edit_auto_post($auto_post_id)
    {
        if ($this->config->item('facebook_poster_group_enable_disable') == '' || $this->config->item('facebook_poster_group_enable_disable')=='0')
            $data['facebook_poster_group'] = '0';
        else
            $data['facebook_poster_group'] = '1';
        $table2 = "facebook_rx_auto_post";
        $where5656  = array('where'=>array('id'=>$auto_post_id));

        $allschedulepost_check = $this->basic->get_data($table2,$where5656);

        foreach ($allschedulepost_check as $key => $value12) {
            if ($value12['posting_status'] == '2')
            {
                 $data['is_all_posted'] = 1; 
            }
            else
            {
                $data['is_all_posted'] =0;
            }
          
        }
     
        $data['body'] = 'text_image_link_video_post/edit_auto_post';
        $data['page_title'] = $this->lang->line('Text/Image/Link/Video Post');
        $data["time_zone"]= $this->_time_zone_list();
        $data["time_interval"] = $this->get_periodic_time();
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $app_info=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));
        $data["app_info"] = $app_info;

        $data["all_data"] = $this->basic->get_data("facebook_rx_auto_post",array("where"=>array("id"=>$auto_post_id)));
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));

        $this->load->library('fb_rx_login');
        $this->fb_rx_login->app_initialize($this->session->userdata("fb_rx_login_database_id"));
        $app_id = isset($app_info[0]['api_id']) ? $app_info[0]['api_id'] : 0;
        $app_secret = isset($app_info[0]['api_secret']) ? $app_info[0]['api_secret'] : 0;
        $current_app_info = $this->fb_rx_login->app_info_graber($app_id,$app_secret);
        $data['current_app_name'] = isset($current_app_info['name']) ? $current_app_info['name'] : "";
        $data['current_app_link'] = isset($current_app_info['link']) ? $current_app_info['link'] : "";
        $data['current_app_photo_url'] = isset($current_app_info['photo_url']) ? $current_app_info['photo_url'] : "";


        $this->_viewcontroller($data);
    }

    public function text_image_link_video_edit_auto_post_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            redirect('home/access_forbidden', 'location');
        }

        if ($_POST)
        {
            $this->form_validation->set_rules('id',                             '<b>id</b>',                            'trim|required');
            $this->form_validation->set_rules('user_id',                        '<b>user_id</b>',                       'trim|required');
            $this->form_validation->set_rules('facebook_rx_fb_user_info_id',    '<b>facebook_rx_fb_user_info_id</b>',   'trim|required');
            $this->form_validation->set_rules('campaign_name',          '<b>Campaign Name</b>',     'trim');
            $this->form_validation->set_rules('message',                '<b>Message</b>',           'trim');
            $this->form_validation->set_rules('link',                   '<b>Paste link</b>',        'trim');
            $this->form_validation->set_rules('link_preview_image',     '<b>Preview image URL</b>', 'trim');
            $this->form_validation->set_rules('link_caption',           '<b>Link caption</b>',      'trim');
            $this->form_validation->set_rules('link_description',       '<b>Link description</b>',  'trim');
            $this->form_validation->set_rules('image_url',              '<b>Image Url</b>',  'trim');
            $this->form_validation->set_rules('video_url',              '<b>Video Url</b>',  'trim');
            $this->form_validation->set_rules('video_thumb_url',        '<b>Video Thumb Url/b>',  'trim');
            $this->form_validation->set_rules('auto_share_post',        '<b>Auto Share Post</b>',  'trim');
            $this->form_validation->set_rules('auto_share_to_profile',  '<b>Auto Share To Profile</b>',  'trim');
            $this->form_validation->set_rules('auto_like_post',         '<b>Auto Like Post</b>',  'trim');
            $this->form_validation->set_rules('auto_private_reply',     '<b>Auto Private Reply</b>',  'trim');
            $this->form_validation->set_rules('auto_private_reply_text','<b>Auto Private Reply Text</b>',  'trim');
            $this->form_validation->set_rules('auto_comment',           '<b>auto comment</b>',  'trim');
            $this->form_validation->set_rules('auto_comment_text',      '<b>auto comment text</b>',  'trim');
            $this->form_validation->set_rules('schedule_type',          '<b>schedule type</b>',  'trim');
            $this->form_validation->set_rules('schedule_time',          '<b>schedule time</b>',  'trim');
            $this->form_validation->set_rules('time_zone',              '<b>time zone</b>',  'trim');
            $this->form_validation->set_rules('submit_post_hidden',     '<b>submit post hidden</b>',  'trim');

            if($this->form_validation->run() == false)
            {
                return $this->edit_auto_post($_POST['id']);
            }
            $times = 0;
            $id                         = $this->input->post('id', true);
            $user_id                    = $this->input->post('user_id', true);
            $facebook_rx_fb_user_info_id= $this->input->post('facebook_rx_fb_user_info_id', true);
            $campaign_name              = $this->input->post('campaign_name', true);
            $message                    = $this->input->post('message', true);
            $link                       = $this->input->post('link', true);
            $link_preview_image         = "";
            $link_caption               = $this->input->post('link_caption', true);
            $link_description           = $this->input->post('link_description', true);
            $image_url                  = $this->input->post('image_url', true);
            $video_url                  = $this->input->post('video_url', true);
            $video_thumb_url            = $this->input->post('video_thumb_url', true);
            $video_title                = "";
            $auto_share_post            = $this->input->post('auto_share_post', true);
            $auto_share_to_profile      = $this->input->post('auto_share_to_profile', true);
            $auto_like_post             = $this->input->post('auto_like_post', true);
            $auto_private_reply         = $this->input->post('auto_private_reply', true);
            $auto_private_reply_text    = $this->input->post('auto_private_reply_text', true);
            $auto_comment               = $this->input->post('auto_comment', true);
            $auto_comment_text          = $this->input->post('auto_comment_text', true);
            $schedule_type              = $this->input->post('schedule_type', true);
            $schedule_time              = $this->input->post('schedule_time', true);
            $time_zone                  = $this->input->post('time_zone', true);
            $submit_post_hidden         = $this->input->post('submit_post_hidden', true);
            $ultrapost_auto_reply_table_id   = $this->input->post('auto_reply_template', true);
            $times                      = $this->input->post('repeat_times', true);
            $interval                   = $this->input->post('time_interval', true);
            

            $post_to_pages = array();
            $post_to_groups = array();
            if($this->input->post('post_to_pages', true) !== null)
                $post_to_pages = $this->input->post('post_to_pages', true);
            if($this->input->post('post_to_groups', true) !== null)
                $post_to_groups = $this->input->post('post_to_groups', true);


            $request_count1 = 0;
            $request_count2 = 0;
            $request_count = 0;
            if(isset($post_to_pages))
            $request_count1 = count($post_to_pages);
            if(isset($post_to_groups))
            $request_count2 = count($post_to_groups);
            $request_count = $request_count1+$request_count2;

            $data = array(
                'user_id'                       => $user_id,
                'facebook_rx_fb_user_info_id'   => $facebook_rx_fb_user_info_id,
                'campaign_name'                 => $campaign_name,
                'message'                       => $message,
                'link'                          => $link,
                'link_caption'                  => $link_caption,
                'link_description'              => $link_description,
                'image_url'                     => $image_url,
                'video_url'                     => $video_url,
                'video_thumb_url'               => $video_thumb_url,
                'video_title'                   => $video_title,
                'auto_share_post'               => '0',
                'auto_share_to_profile'         => $auto_share_to_profile,
                'auto_like_post'                => '0',
                'auto_private_reply'            => '0',
                'auto_private_reply_text'       => '',
                'auto_comment'                  => '0',
                'auto_comment_text'             => '',
                'schedule_time'                 => $schedule_time,
                'time_zone'                     => $time_zone,
                'auto_share_this_post_by_pages' => "",
                'post_type'                     => $submit_post_hidden,
                'repeat_times'                  => $times,
                'time_interval'                 => $interval,
                'schedule_type'                 => $schedule_type
            );
            if(isset($ultrapost_auto_reply_table_id))
                $data['ultrapost_auto_reply_table_id'] = $ultrapost_auto_reply_table_id;

            $data["auto_private_reply_status"] = "0";
            $data["auto_private_reply_count"] = 0;
            $data["auto_private_reply_done_ids"] = json_encode(array());
            $data["post_auto_comment_cron_jon_status"] = "0";
            $data["post_auto_like_cron_jon_status"] = "0";
            $data["post_auto_share_cron_jon_status"] = "0";
            $data["auto_share_to_profile"]= "0";            
            $data["posting_status"] ='0';

            $this->basic->delete_data('facebook_rx_auto_post',array('id'=>$id,'user_id'=>$this->user_id));
            $this->basic->delete_data('facebook_rx_auto_post',array('parent_campaign_id'=>$id,'full_complete'=>'0' ,'user_id'=>$this->user_id));
            //$this->basic->delete_data('facebook_rx_auto_post',array('id'=>$id));
            //$this->basic->delete_data('facebook_rx_auto_post',array('full_complete'=>'0'));

            $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info");
            $user_id_array=array($this->user_id);
   
                $page_info_arr = array();
                $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

                foreach ($page_info as $key => $value) {
                   if(!in_array($value["id"], $post_to_pages)) continue;
                   $page_info_arr[$value["id"]] = $value['page_name'];
                }

                $parent_id='';
                for ($insert_counter=0; $insert_counter < $request_count1; $insert_counter++) { 

                   $data['page_or_group_or_user'] = 'page';
                   $data['page_group_user_id'] = $post_to_pages[$insert_counter];
                   $data["page_ids"] = implode(',', $post_to_pages);
                   $data['page_or_group_or_user_name'] = $page_info_arr[$data['page_group_user_id']];

                 $x=$interval;
                   for ($i=0; $i <=$times ; $i++) { 
                       
                        if($i == 0)
                        {
                            $data['schedule_time']= $schedule_time;
                            $this->basic->insert_data('facebook_rx_auto_post',$data);
                            $insert_id = $this->db->insert_id();
                            if($i == 0 && $insert_counter == 0)
                                $parent_id = $insert_id;
                                
                           

                        }

                        if ($times == 0) {
                              $data['is_child'] = '1';
                              $data['parent_campaign_id'] = $parent_id;
                        
                        }

                        if($i >= 1)
                        {
                            $data['is_child'] = '1';
                            $current_schedule_time = $schedule_time;
                            $dateTime = new DateTime($current_schedule_time);
                            $p = $i*$x;
                            $dateTime->modify("+{$p} minutes");
                            $data['parent_campaign_id'] = $parent_id;
                            $change_time= $dateTime->format('Y-m-d H:i:s');
                            $data['schedule_time']= $change_time;
                            $data["page_ids"];
                            $this->basic->insert_data('facebook_rx_auto_post',$data);
                        }
                   }

                }
  
           

            if(count($post_to_groups))
            {
                $group_info_arr = array();
                $group_info = $this->basic->get_data("facebook_rx_fb_group_info",array("where_in"=>array("id"=>$post_to_groups,"user_id"=>$user_id_array)));
                foreach ($group_info as $key => $value) {
                   if(!in_array($value["id"], $post_to_groups)) continue;
                   $group_info_arr[$value["id"]] = $value['group_name'];
                }

             
                for ($insert_counter=0; $insert_counter < $request_count2; $insert_counter++) { 

                   $data['page_or_group_or_user'] = 'group';
                   $data['page_group_user_id'] = $post_to_groups[$insert_counter];
                   $data['page_or_group_or_user_name'] = $group_info_arr[$data['page_group_user_id']];
                   $data["group_ids"] = implode(',', $post_to_groups);

                   $y = $interval;
                   for ($i=0; $i <=$times ; $i++) { 
                       
                        if($i == 0)
                        {
                             $data['schedule_time']= $schedule_time;
                            $this->basic->insert_data('facebook_rx_auto_post',$data);
                            $insert_id = $this->db->insert_id();
                         
                        }
                        if($i >= 1)
                        {
                            $data['is_child'] = '1';
                            $current_schedule_time = $schedule_time;
                            $data['parent_campaign_id'] = $parent_id;
                            $dateTime = new DateTime($current_schedule_time);
                            $p = $i*$y;
                            $dateTime->modify("+{$p} minutes");
                            $change_time= $dateTime->format('Y-m-d H:i:s');
                            $data['schedule_time']= $change_time;
                            $data["group_ids"];
                            $this->basic->insert_data('facebook_rx_auto_post',$data);
                        }
                   }

                }
          

       
            }

            if($insert_counter > 0)
            $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line('Facebook post information has been updated successfully.')); 
            else $return_val=array("status"=>"0","message"=>"<i class='fa fa-remove'></i>  ".$this->lang->line("something went wrong, please try again."));

            echo json_encode($return_val);
        }
    }



    public function text_image_link_video_meta_info_grabber()
    {
        if($_POST)
        {
            $link= $this->input->post("link");
            $this->load->library("fb_rx_login");
            $response=$this->fb_rx_login->get_meta_tag_fb($link);
            echo json_encode($response);
        }
    }


    public function text_image_link_video_youtube_video_grabber()
    {
        if(!$_POST) exit();
        $this->load->library("fb_rx_login");
        $video_url = $this->input->post("link");

        if($video_url!="")
        {
            if(strpos($video_url, 'youtube.com') !== false)
            {
                parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
                $youtube_video_id = isset($my_array_of_vars['v']) ? $my_array_of_vars['v'] : "";

                if($youtube_video_id!="")
                {
                    echo $video_url = $this->fb_rx_login->get_youtube_video_url($youtube_video_id);
                    exit();
                }
            }
            else
            {
                echo $video_url;
                exit();
            }
        }
        else echo "";

    }


    public function text_image_link_video_upload_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/text_image_link_video";
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
            echo json_encode($filename);
        }
    }

    public function text_image_link_video_upload_video_thumb()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/text_image_link_video";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="videothumb_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

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



    public function text_image_link_video_upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/text_image_link_video";
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


    public function text_image_link_video_upload_link_preview()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/text_image_link_video";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="imagethumb_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }

    public function text_image_link_video_delete_uploaded_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();

        $output_dir = FCPATH."upload_caster/text_image_link_video/";
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


    public function cta_post()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(220,$this->module_access)) exit();
        $data['page_title'] = $this->lang->line("CTA Poster");
        
        $data['body'] = 'cta_post/cta_post_list';
        $this->_viewcontroller($data);
    }


    public function cta_poster()
    {

        $data['page_title'] = $this->lang->line("CTA Poster");
        $data['body'] = 'cta_post/add_cta_post';
        $data["time_interval"] = $this->get_periodic_time();
        $data["time_zone"]= $this->_time_zone_list();
        
        $user_infos = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        if ( count( $user_infos ) == 0 ) 
            return redirect( base_url( 'facebook_rx_account_import/index' ), 'location' );

        $data["fb_user_info"] = $user_infos;
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));  
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));
        
        $only_message_button = 0;

        if($only_message_button==1) // only show message cta button, used in fb exciter
        $cta_dropdown = array("MESSAGE_PAGE"=>"MESSAGE_PAGE");
        else
        $cta_dropdown = array("BOOK_TRAVEL"=>"BOOK_TRAVEL","BUY_NOW"=>"BUY_NOW","CALL_NOW"=>"CALL_NOW","DOWNLOAD"=>"DOWNLOAD","GET_DIRECTIONS"=>"GET_DIRECTIONS","GET_QUOTE"=>"GET_QUOTE","INSTALL_APP"=>"INSTALL_APP","INSTALL_MOBILE_APP"=>"INSTALL_MOBILE_APP","LEARN_MORE"=>"LEARN_MORE","LIKE_PAGE"=>"LIKE_PAGE","LISTEN_MUSIC"=>"LISTEN_MUSIC","MESSAGE_PAGE"=>"MESSAGE_PAGE","NO_BUTTON"=>"NO_BUTTON","OPEN_LINK"=>"OPEN_LINK","PLAY_GAME"=>"PLAY_GAME","SHOP_NOW"=>"SHOP_NOW","SIGN_UP"=>"SIGN_UP","SUBSCRIBE"=>"SUBSCRIBE","USE_APP"=>"USE_APP","USE_MOBILE_APP"=>"USE_MOBILE_APP","WATCH_MORE"=>"WATCH_MORE","WATCH_VIDEO"=>"WATCH_VIDEO");
        foreach ($cta_dropdown as $key => $value) 
        {
           $value = str_replace('_', " ", $value);
           $value = ucwords(strtolower($value));
           $data["cta_dropdown"][$key] = $value;
        }

        $this->_viewcontroller($data);
    }


    public function cta_post_list_data($only_message_button=0)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'facebook_rx_cta_post.id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $campaign_name = trim($this->input->post("campaign_name", true));
        $page_name = trim($this->input->post("page_name", true));

        $scheduled_from = trim($this->input->post('scheduled_from', true));        
        if($scheduled_from) $scheduled_from = date('Y-m-d', strtotime($scheduled_from));

        $scheduled_to = trim($this->input->post('scheduled_to', true));
        if($scheduled_to) $scheduled_to = date('Y-m-d', strtotime($scheduled_to));


        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            $this->session->set_userdata('facebook_rx_cta_poster_campaign_name', $campaign_name);
            $this->session->set_userdata('facebook_rx_cta_poster_page_name', $page_name);
            $this->session->set_userdata('facebook_rx_cta_poster_scheduled_from', $scheduled_from);
            $this->session->set_userdata('facebook_rx_cta_poster_scheduled_to', $scheduled_to);
            $this->session->set_userdata('facebook_rx_cta_poster_post_type', $post_type);
        }

        $search_campaign_name  = $this->session->userdata('facebook_rx_cta_poster_campaign_name');
        $search_page_name  = $this->session->userdata('facebook_rx_cta_poster_page_name');
        $search_scheduled_from = $this->session->userdata('facebook_rx_cta_poster_scheduled_from');
        $search_scheduled_to = $this->session->userdata('facebook_rx_cta_poster_scheduled_to');

        $where_simple=array();
        
        if ($search_campaign_name) $where_simple['campaign_name like ']    = "%".$search_campaign_name."%";
        if ($search_page_name) $where_simple['page_name like ']    = "%".$search_page_or_group_or_user_name."%";
    
        if ($search_scheduled_from) 
        {
            if ($search_scheduled_from != '1970-01-01') 
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') >="]= $search_scheduled_from;
            
        }
        if ($search_scheduled_to) 
        {
            if ($search_scheduled_to != '1970-01-01') 
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') <="]=$search_scheduled_to;
            
        }



        $where_simple['facebook_rx_cta_post.user_id'] = $this->user_id;
        $where_simple['facebook_rx_cta_post.facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        $this->db->where("(is_child='0' or posting_status='2')");
        $where  = array('where'=>$where_simple);
        $join    =  array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=facebook_rx_cta_post.page_group_user_id,left");
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "facebook_rx_cta_post";
        $info = $this->basic->get_data($table, $where, $select=array("facebook_rx_cta_post.*","facebook_rx_fb_page_info.page_name"), $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $this->db->where("(is_child='0' or posting_status='2')");
        $total_rows_array = $this->basic->count_row($table, $where, $count="facebook_rx_cta_post.id", $join);
        $total_result = $total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++) 
        {
            $posting_status = $info[$i]['posting_status'];
            $full_complete =  $info[$i]['full_complete'];
            $schedule_type = $info[$i]['schedule_type'];
            $parent_id = $info[$i]['parent_campaign_id'];

            // $table2 = "facebook_rx_cta_post";
            // $where5656  = array('where'=>array('parent_campaign_id'=>$info[$i]['id']));

            // $allschedulepost_check = $this->basic->get_data($table2, $where5656);
            // $is_all_posted = 1;  

            // foreach ($allschedulepost_check as $key => $value12) {
                    
            //     if ($value12['posting_status'] != 2)
            //         $is_all_posted = 0;
            // }

            $allschedulepost_check = $this->basic->get_data("facebook_rx_cta_post", array('where'=>array('parent_campaign_id'=>$info[$i]['id'])));
            
            if(count($allschedulepost_check)>0)
            {
                $completed_child_count=0;
                foreach ($allschedulepost_check as $key => $value12) 
                {                        
                    if ($value12['posting_status'] == '2' ) $completed_child_count++;
                }
                if(count($allschedulepost_check) == $completed_child_count) $is_all_posted='1';
                else $is_all_posted='0';
            }
            else
            {
                if($posting_status=='2') $is_all_posted='1';
                else $is_all_posted='0';
            }

            if(($posting_status=='0' || $posting_status == '2')  && ($schedule_type == 'later' && $parent_id == '0' && $is_all_posted==0))
                $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-check-circle gray"></i> '.$this->lang->line("not all completed").'</span>';
            else if( $posting_status == '2') $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("Completed").'</span>';
            else if( $posting_status == '1') $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-spinner orange"></i> '.$this->lang->line("Processing").'</span>';
            else $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-remove red"></i> '.$this->lang->line("Pending").'</span>';

            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
            $scheduled_at = date("M j, y H:i",strtotime($info[$i]['schedule_time']));
            else $scheduled_at = '<i class="fa fa-remove red" title="'.$this->lang->line("Instantly posted").'"></i>';
            $info[$i]['scheduled_at'] =  $scheduled_at;

            $insight="";
            if($this->session->userdata('user_type') == 'Admin'|| in_array(72,$this->module_access))
            {
                $post_id = $info[$i]['post_id'];
                $page_id = $info[$i]['page_group_user_id'];
                
                if($posting_status=='2')
                $insight = "<a class='btn btn-sm btn-primary' target='_BLANK' href='".base_url("facebook_rx_insight/post_analytics_display/{$post_id}/{$page_id}")."'><i class='fa fa-line-chart'></i> Post Insight</a>";
                else $insight = '<i class="fa fa-remove red" title="'.$this->lang->line("This post is not published yet.").'"></i>';                
            }

            $info[$i]['insight'] =  $insight;

            $cta_type= $info[$i]['cta_type'];
            $cta_type = str_replace('_', " ", $cta_type);
            $cta_type = ucwords(strtolower($cta_type));

            if($info[$i]['cta_type']=="LIKE_PAGE" || $info[$i]['cta_type'] =="MESSAGE_PAGE")
            $cta_button = "<a  class='btn btn-default btn-sm' href='#'>".$cta_type."</a>";
            else  $cta_button = "<a  class='btn btn-default btn-sm' target='_BLANK' href='".$info[$i]['cta_value']."'>".$cta_type."</a>";
                    
            $info[$i]['cta_button'] =  $cta_button;

            // if(strlen($info[$i]["message"])>=60) $info[$i]["message_formatted"] = substr($info[$i]["message"], 0, 60)."...";
            // else $info[$i]["message_formatted"] = $info[$i]["message"];
            
            if($posting_status=='2')
            $post_url = "<a class='btn btn-outline-info' target='_BLANK' href='".$info[$i]['post_url']."'><i class='fa fa-hand-o-right'></i></a>";
            else $post_url = "<a class='btn btn-outline-info border_gray gray' target='_BLANK' title='".$this->lang->line("This post is not published yet.")."'><i class='fa fa-hand-o-right'></i></a>";
     
            // $table2 = "facebook_rx_cta_post";
            // $where5656  = array('where'=>array('parent_campaign_id'=>$info[$i]['id']));

            // $allschedulepost_check = $this->basic->get_data($table2, $where5656);
            // $is_all_posted = 1;  

            // foreach ($allschedulepost_check as $key => $value12) {
                    
            //     if ($value12['posting_status'] != 2)
            //         $is_all_posted = 0;
            // }

            if($schedule_type == 'later' && $parent_id == '0')
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle blue"></i> '.$this->lang->line("main campaign").'</span>';
            else if($schedule_type == 'now')
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle red"></i> '.$this->lang->line("single campaign").'</span>';
            else
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("sub campaign").'</span>';

            if(($posting_status=='0' || $posting_status == '2')  && ($schedule_type == 'later' && $parent_id == '0' && $is_all_posted==0) )
            $editUrlCtaPost ="<a class='btn btn-outline-warning' title='".$this->lang->line("Edit")."'  href='".base_url()."ultrapost/edit_cta_post/".$info[$i]['id']."'><i class='fa fa-edit'></i></a>";  
            else $editUrlCtaPost ="<a class='btn btn-outline-warning border_gray gray' title='".$this->lang->line("Only pending campaigns are editable")."'><i class='fa fa-edit'></i></a>";  

            if ($schedule_type == 'later' && $parent_id == '0') {

                 $report_url = "<button class='btn btn-outline-info view_report' table_id='".$info[$i]['id']."' title='".$this->lang->line("report")."'><i class='fa fa-eye'></i></button>";  
            }
            else 
            {
                $report_url ="<a class='btn btn-outline-info border_gray gray' title='".$this->lang->line("Only parent campaign has shown  report")."'><i class='fa fa-eye'></i></a>";
            }
            $info[$i]['delete'] =  "<a title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete btn btn-outline-danger'><i class='fa fa-trash'></i></a>";
            if($schedule_type == 'later' && $parent_id == '0') {
                $deleteUrl =  "<a title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete_p btn btn-outline-danger'><i class='fa fa-trash'></i></a>";
            }
            else{
                $deleteUrl =  "<a title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete btn btn-outline-danger'><i class='fa fa-trash'></i></a>";
            } 

            $info[$i]['actions']=$post_url."&nbsp;".$editUrlCtaPost."&nbsp;".$deleteUrl."&nbsp;".$report_url;
        }


        echo convert_to_grid_data($info, $total_result);
    }

    public function ajax_cta_report()
    {


      $table_id = $this->input->post('table_id');
      $where = array('where'=>array('facebook_rx_cta_post.parent_campaign_id'=>$table_id),'or_where'=>array('facebook_rx_cta_post.id'=>$table_id));
      $select=array("facebook_rx_cta_post.*","facebook_rx_fb_page_info.page_name");
      $join    =  array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=facebook_rx_cta_post.page_group_user_id ,left");
      $text_info = $this->basic->get_data('facebook_rx_cta_post',$where,$select,$join);

      if(isset($text_info[0]['id']) && $text_info[0]['id'] != '')
      {
          $str = '<script>
                  $j(document).ready(function() {
                      $("#campaign_report").DataTable();
                  }); 
               </script>
               <div class="table-responsive">
               <table id="campaign_report" class="table table-bordered">
                   <thead>
                       <tr>
                           <th class="text-center">'.$this->lang->line("posting page or group").'</th>
                           <th class="text-center">'.$this->lang->line("posting status").'</th>
                           <th class="text-center">'.$this->lang->line("post id").'</th>
                           <th class="text-center">'.$this->lang->line("schedule time").'</th>
                           <th class="text-center">'.$this->lang->line("post type").'</th>
                           <th class="text-center">'.$this->lang->line("Error").'</th>
                           
                       </tr>
                   </thead>
                   <tbody>';
                       
                   
          // $info = json_decode($reply_info[0]['auto_reply_done_info'],true);
      

          foreach($text_info as $value)
          {
             
             
              $posting_status = isset($value['posting_status']) ? $value['posting_status']:"";
              $post_type = isset($value['cta_value']) ? $value['cta_value']: "";
             
              if($posting_status=='2')
              $posting_status='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("completed").'</span>';
              if($posting_status == '0')
              $posting_status='<span class="label label-light"><i class="fa fa-check-circle red"></i> '.$this->lang->line("pending").'</span>';
          	  if($posting_status == '1')
          	  $posting_status='<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("processing").'</span>';

              $post_type = '<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("CTA").'</span>';


              $str .= '<tr>
                          <td class="text-center">'.$value['page_or_group_or_user'].' : '.$value['page_name'].'</td>
                          <td class="text-center">'.$posting_status.'</td>
                          <td class="text-center"><a target="_BLANK" href="http://facebook.com/'.$value['post_id'].'" class="product-title">'.$value['post_id'].'</a></td>
                          <td class="text-center">'.$value['schedule_time'].'</td>
                          <td class="text-center">'.$post_type.'</td>
                          <td class="text-center">'.$value['error_mesage'].'</td>
                          
                      </tr>';
          }

          $str .= '</tbody>
               </table></div>';
      }
      else
      {
          $str = "<div class='alert alert-danger'>{$this->lang->line("no data to show")}</div>";
      }

      echo $str;  
    }

    public function add_cta_post_action()
    {
        if(!$_POST)
        exit();
      
        $this->load->library("fb_rx_login");

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
           $$key=$value;
           if(!is_array($value)){

            if($key == "auto_reply_template")
                $insert_data['ultrapost_auto_reply_table_id'] = $value;
            else
                $insert_data[$key]=$value;
           }
           
        }
        $insert_data["schedule_type"];

        //************************************************/
        $times = 0;
        $times = $repeat_times;
        $interval= $time_interval;
        $request_count = count($post_to_pages);


        $status=$this->_check_usage($module_id=220,$request=$request_count);
        if($status=="2") 
        {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        else if($status=="3") 
        {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
        }
        //************************************************//

        $insert_data["auto_share_to_profile"]= "0";
        

        $insert_data["user_id"] = $this->user_id;        
        $insert_data["facebook_rx_fb_user_info_id"] = $this->session->userdata("facebook_rx_fb_user_info");       

        if(!isset($auto_share_this_post_by_pages) || !is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages=array();
        if(!isset($post_to_pages) || !is_array($post_to_pages)) $post_to_pages=array();
        $auto_share_this_post_by_pages_new = array_diff($auto_share_this_post_by_pages,$post_to_pages);  
        $page_ids_string = implode(',', $post_to_pages);
        $insert_data["page_ids"] = $page_ids_string;      
        $insert_data["auto_share_this_post_by_pages"] = json_encode($auto_share_this_post_by_pages_new);

        $insert_data["auto_private_reply_status"]= "0";
        $insert_data["auto_private_reply_count"]= 0;
        $insert_data["auto_private_reply_done_ids"]= json_encode(array());
        $insert_data["post_auto_comment_cron_jon_status"] = "0";
        $insert_data["post_auto_like_cron_jon_status"] = "0";
        $insert_data["post_auto_share_cron_jon_status"] = "0";
        $insert_data["repeat_times"] = $times;
        $insert_data["time_interval"] = $interval;
        

        if($schedule_type=="now")
        {
            $insert_data["posting_status"] ='2';
            $insert_data["full_complete"]  ='1';
        }
        else
        {
            $insert_data["posting_status"] ='0';
        }


        $insert_data_batch=array();
        $user_id_array=array($this->user_id);  
        $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info"); // table > facebook_rx_fb_user_info.id
        $count=0;
              
        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));
     
        foreach ($page_info as $key => $value) 
        {
            if(!in_array($value["id"], $post_to_pages)) continue;

            
            $page_access_token =  isset($value["page_access_token"]) ? $value["page_access_token"] : ""; 
            $fb_page_id =  isset($value["page_id"]) ? $value["page_id"] : "";

            $insert_data_batch[$count]=$insert_data;
            $page_auto_id =  isset($value["id"]) ? $value["id"] : ""; 
            $insert_data_batch[$count]["page_group_user_id"]=$page_auto_id;
            $insert_data_batch[$count]["page_or_group_or_user"]="page";
            $insert_data_batch[$count]["post_id"] = "";
            $insert_data_batch[$count]["post_url"] = "";   
            
            if($schedule_type=="now")
            {
                if($this->is_demo == '1')
                    if($this->user_id == 1)
                        continue;
                try
                {

                    $response = $this->fb_rx_login->cta_post($message, $link,"","",$cta_type,$cta_value,"","",$page_access_token,$fb_page_id);

                }
                catch(Exception $e) 
                {
                  $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                  $return_val=array("status"=>"0","message"=>$error_msg);
                  echo json_encode($return_val);
                  exit();
                }

                $object_id=$response["id"];
                $share_access_token = $page_access_token;

                $insert_data_batch[$count]["post_id"]= $object_id;
                $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$page_access_token);
                $insert_data_batch[$count]["post_url"]= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : ""; 
                $insert_data_batch[$count]["last_updated_at"]= date("Y-m-d H:i:s");

                $this->basic->insert_data("facebook_rx_cta_post",$insert_data_batch[$count]);  
                //insert data to useges log table
                $this->_insert_usage_log($module_id=220,$request=$request_count);


                if(isset($insert_data['ultrapost_auto_reply_table_id']) && $insert_data['ultrapost_auto_reply_table_id'] != '0')
                {

                    //************************************************//
                    $status=$this->_check_usage($module_id=204,$request=1);
                    if($status!="2" && $status!="3") 
                    {


                        $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $insert_data['ultrapost_auto_reply_table_id'] ]]);

                        $auto_reply_table_data = [];

                        foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                            foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                                
                                if($auto_key == 'id')
                                    continue;

                                if($auto_key == 'ultrapost_campaign_name')
                                    $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                                else
                                    $auto_reply_table_data[$auto_key] = $auto_value;
                            }
                        }



                        $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $value['facebook_rx_fb_user_info_id'];
                        $auto_reply_table_data['page_info_table_id'] = $value['id'];
                        $auto_reply_table_data['page_name'] = $value['page_name'];
                        $auto_reply_table_data['post_id'] = $object_id;
                        $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                        $auto_reply_table_data['post_description'] = $message;
                        $auto_reply_table_data['auto_private_reply_status'] = '0';
                        $auto_reply_table_data['auto_private_reply_count'] = 0;
                        $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                        $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                        $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                        $auto_reply_table_data['last_reply_time'] = '';
                        $auto_reply_table_data['error_message'] = '';
                        $auto_reply_table_data['hidden_comment_count'] = 0;
                        $auto_reply_table_data['deleted_comment_count'] = 0;
                        $auto_reply_table_data['auto_comment_reply_count'] = 0;


                        $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                     
                         $this->_insert_usage_log($module_id=204,$request=1);                        
                     }
                    //************************************************//
                }


            }            
            $count++;
        }

        $profile_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=> $account_switching_id,"user_id"=>$this->user_id)));  
        $user_access_token =  isset($profile_info[0]["access_token"]) ? $profile_info[0]["access_token"] : ""; 
        $user_fb_id =  isset($profile_info[0]["fb_id"]) ? $profile_info[0]["fb_id"] : ""; 
    

       if($schedule_type=="now") $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Facebook CTA post has been performed successfully."));
       else
       {
        

            $page_info_arr = array();
            $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

            foreach ($page_info as $key => $value) {
               if(!in_array($value["id"], $post_to_pages)) continue;
               $page_info_arr[$value["id"]] = $value['page_name'];
            }


            $parent_id='';
            for ($insert_counter=0; $insert_counter < $request_count; $insert_counter++) { 

               $insert_data['page_or_group_or_user'] = 'page';
               $insert_data['page_group_user_id'] = $post['post_to_pages'][$insert_counter];
               $x=$post['time_interval'];
               for ($i=0; $i <=$times ; $i++) { 
                   
                    if($i == 0)
                    {
                        $insert_data['schedule_time']= $post['schedule_time'];
                        $this->basic->insert_data('facebook_rx_cta_post',$insert_data);
                        $insert_id = $this->db->insert_id();
                       
                        if($i == 0 && $insert_counter == 0)
                            $parent_id = $insert_id;
                            
                       

                    }
                    if ($times == 0) {
                          $insert_data['is_child'] = '1';
                          $insert_data['parent_campaign_id'] = $parent_id;
                    
                    }
                    if($i >= 1)
                    {


                        $insert_data['is_child'] = '1';
                        $current_schedule_time = $post['schedule_time'];
                        $dateTime = new DateTime($current_schedule_time);
                        $p = $i*$x;
                        $dateTime->modify("+{$p} minutes");
                        $insert_data['parent_campaign_id'] = $parent_id;
                        $change_time= $dateTime->format('Y-m-d H:i:s');
                        $insert_data['schedule_time']= $change_time;
                         unset($insert_data['page_ids']);
                         
                        $this->basic->insert_data('facebook_rx_cta_post',$insert_data);
                    }
               }

            }


            if($insert_counter > 0)
            {
                $number_request = count($insert_data_batch);
                $this->_insert_usage_log($module_id=220,$request=$number_request);
                $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Facebook CTA post campaign has been created successfully."));
            }
            else $return_val=array("status"=>"0","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("something went wrong, please try again."));
       }
       echo json_encode($return_val);        
    }




    public function cta_poster_cron_job($api_key="")
    {
        $this->api_key_check($api_key);

        // $this->load->library('Fb_rx_login');
        if($this->is_demo == '1')
        $where['where']=array("posting_status"=>"0","facebook_rx_cta_post.user_id !="=>1);
        else
        $where['where']=array("posting_status"=>"0");

        $select="schedule_time,time_zone,cta_value,facebook_rx_cta_post.id as column_id,page_id,page_group_user_id,page_access_token,cta_type,message,facebook_rx_cta_post.ultrapost_auto_reply_table_id,link,link_preview_image,link_description,link_caption,facebook_rx_cta_post.facebook_rx_fb_user_info_id";
        $join=array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=facebook_rx_cta_post.page_group_user_id,left");

        /***    Taking fist 200 post for auto reply ***/
        $post_info= $this->basic->get_data("facebook_rx_cta_post",$where,$select,$join,$limit=30, $start=0,$order_by='schedule_time ASC');

        $campaign_id_array=array();

        foreach($post_info as $info)
        {
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            if($time_zone) date_default_timezone_set($time_zone);
            $now_time = date("Y-m-d H:i:s");

            if(strtotime($now_time) < strtotime($schedule_time)) continue;

            $campaign_id_array[] = $info['column_id'];
        }

        if(empty($campaign_id_array)) exit();
        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("facebook_rx_cta_post",array("posting_status"=>"1"));

        $config_id_database = array();
        foreach($post_info as $info)
        {

            $page_id =   $info['page_id'];
            $page_access_token = $info['page_access_token'];
            $post_column_id= $info['column_id'];

            if(!in_array($post_column_id, $campaign_id_array)) continue;

            $cta_type = $info["cta_type"];
            $cta_value = $info["cta_value"];
            $message = $info["message"];
            $link = $info["link"];
            $link_preview_image = $info["link_preview_image"];
            $link_caption = $info["link_caption"];
            $link_description = $info["link_description"];

            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            $this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->load->library("fb_rx_login");
            // setting fb confid id for library call

            $response =array();
            $error_msg ="";


            try
            {
                $response = $this->fb_rx_login->cta_post($message, $link,"","",$cta_type,$cta_value,"","",$page_access_token,$page_id);
            }
            catch(Exception $e)
            {
              $error_msg = $e->getMessage();
            }

            $object_id=isset($response["id"]) ? $response["id"] : "";

            $temp_data=array();
            try
            {
                $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$page_access_token);
            }
            catch(Exception $e)
            {
                $error_msg1 = $e->getMessage();
            }

            $post_url= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";

            $update_data = array("posting_status"=>'2',"full_complete"=>'1',"post_id"=>$object_id,"post_url"=>$post_url,"error_mesage"=>$error_msg,"last_updated_at"=>date("Y-m-d H:i:s"));

            $this->basic->update_data("facebook_rx_cta_post",array("id"=>$post_column_id),$update_data);


            if($post_info[0]['ultrapost_auto_reply_table_id'] != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1);
                if($status!="2" && $status!="3") 
                {

                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $post_info[0]['ultrapost_auto_reply_table_id'] ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }

                   

                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];
                    $auto_reply_table_data['post_id'] = $object_id;
                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                    $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                 
                     $this->_insert_usage_log($module_id=204,$request=1);                        
                 }
                //************************************************//
            }

            sleep(rand ( 1 , 6 ));


        }

    }


    public function edit_cta_post($cta_post_id)
    {
        
        $table2 = "facebook_rx_cta_post";
        $where5656  = array('where'=>array('id'=>$cta_post_id));

        $allschedulepost_check = $this->basic->get_data($table2,$where5656);

        foreach ($allschedulepost_check as $key => $value12) {
            if ($value12['posting_status'] == '2')
            {
                 $data['is_all_posted'] = 1; 
            }
            else
            {
                $data['is_all_posted'] =0;
            }
          
        }$data['body'] = 'cta_post/edit_cta_post';
        $data['page_title'] = $this->lang->line('Edit CTA Poster');
        $data["time_zone"]= $this->_time_zone_list();
        $data["time_interval"] = $this->get_periodic_time();
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));

        $data["all_data"] = $this->basic->get_data("facebook_rx_cta_post",array("where"=>array("id"=>$cta_post_id)));
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));

        $only_message_button=0;
        if($only_message_button==1) // only show message cta button, used in fb exciter
        $cta_dropdown = array("MESSAGE_PAGE"=>"MESSAGE_PAGE");
        else
        $cta_dropdown = array("BOOK_TRAVEL"=>"BOOK_TRAVEL","BUY_NOW"=>"BUY_NOW","CALL_NOW"=>"CALL_NOW","DOWNLOAD"=>"DOWNLOAD","GET_DIRECTIONS"=>"GET_DIRECTIONS","GET_QUOTE"=>"GET_QUOTE","INSTALL_APP"=>"INSTALL_APP","INSTALL_MOBILE_APP"=>"INSTALL_MOBILE_APP","LEARN_MORE"=>"LEARN_MORE","LIKE_PAGE"=>"LIKE_PAGE","LISTEN_MUSIC"=>"LISTEN_MUSIC","MESSAGE_PAGE"=>"MESSAGE_PAGE","NO_BUTTON"=>"NO_BUTTON","OPEN_LINK"=>"OPEN_LINK","PLAY_GAME"=>"PLAY_GAME","SHOP_NOW"=>"SHOP_NOW","SIGN_UP"=>"SIGN_UP","SUBSCRIBE"=>"SUBSCRIBE","USE_APP"=>"USE_APP","USE_MOBILE_APP"=>"USE_MOBILE_APP","WATCH_MORE"=>"WATCH_MORE","WATCH_VIDEO"=>"WATCH_VIDEO");
        foreach ($cta_dropdown as $key => $value) 
        {
           $value = str_replace('_', " ", $value);
           $value = ucwords(strtolower($value));
           $data["cta_dropdown"][$key] = $value;
        }

        $this->_viewcontroller($data);
    }

    public function edit_cta_post_action()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            redirect('home/access_forbidden', 'location');
        }
        if ($_POST)
        {
            $this->form_validation->set_rules('id',                             '<b>id</b>',                            'trim|required');
            $this->form_validation->set_rules('user_id',                        '<b>user_id</b>',                       'trim|required');
            $this->form_validation->set_rules('facebook_rx_fb_user_info_id',    '<b>facebook_rx_fb_user_info_id</b>',   'trim|required');
            $this->form_validation->set_rules('campaign_name',                  '<b>Campaign Name</b>',                 'trim');
            $this->form_validation->set_rules('message',                        '<b>Message</b>',                       'trim');
            $this->form_validation->set_rules('link',                           '<b>Paste link</b>',                    'trim');
            $this->form_validation->set_rules('link_preview_image',             '<b>Preview image URL</b>',             'trim');
            $this->form_validation->set_rules('link_caption',                   '<b>Link caption</b>',                  'trim');
            $this->form_validation->set_rules('link_description',               '<b>Link description</b>',              'trim');
            $this->form_validation->set_rules('cta_type',                       '<b>Cta Type</b>',                      'trim');
            $this->form_validation->set_rules('cta_value',                      '<b>Cta Talue</b>',                     'trim');
            $this->form_validation->set_rules('auto_share_post',                '<b>Auto Share Post</b>',               'trim');
            $this->form_validation->set_rules('auto_share_to_profile',          '<b>Auto Share To Profile</b>',         'trim');
            $this->form_validation->set_rules('auto_like_post',                 '<b>Auto Like Post</b>',                'trim');
            $this->form_validation->set_rules('auto_private_reply',             '<b>Auto Private Reply</b>',            'trim');
            $this->form_validation->set_rules('auto_private_reply_text',        '<b>Auto Private Reply Text</b>',       'trim');
            $this->form_validation->set_rules('auto_comment',                   '<b>auto comment</b>',                  'trim');
            $this->form_validation->set_rules('auto_comment_text',              '<b>auto comment text</b>',             'trim');
            $this->form_validation->set_rules('schedule_type',                  '<b>schedule type</b>',                 'trim');
            $this->form_validation->set_rules('schedule_time',                  '<b>schedule time</b>',                 'trim');
            $this->form_validation->set_rules('time_zone',                      '<b>time zone</b>',                     'trim');
            $this->form_validation->set_rules('submit_post_hidden',             '<b>submit post hidden</b>',            'trim');

            if($this->form_validation->run() == false)
            {
                return $this->edit_cta_post($_POST['id']);
            }
            $times = 0;
            $id                         = $this->input->post('id', true);
            $user_id                    = $this->input->post('user_id', true);
            $facebook_rx_fb_user_info_id= $this->input->post('facebook_rx_fb_user_info_id', true);
            $campaign_name              = $this->input->post('campaign_name', true);
            $message                    = $this->input->post('message', true);
            $link                       = $this->input->post('link', true);
            $link_preview_image         = $this->input->post('link_preview_image', true);
            $link_caption               = $this->input->post('link_caption', true);
            $link_description           = $this->input->post('link_description', true);
            $cta_type                   = $this->input->post('cta_type', true);
            $cta_value                  = $this->input->post('cta_value', true);
            $auto_share_post            = $this->input->post('auto_share_post', true);
            $auto_share_to_profile      = $this->input->post('auto_share_to_profile', true);
            $auto_like_post             = $this->input->post('auto_like_post', true);
            $auto_private_reply         = $this->input->post('auto_private_reply', true);
            $auto_private_reply_text    = $this->input->post('auto_private_reply_text', true);
            $auto_comment               = $this->input->post('auto_comment', true);
            $auto_comment_text          = $this->input->post('auto_comment_text', true);
            $schedule_type              = $this->input->post('schedule_type', true);
            $schedule_time              = $this->input->post('schedule_time', true);
            $time_zone                  = $this->input->post('time_zone', true);
            $submit_post_hidden         = $this->input->post('submit_post_hidden', true);
            $ultrapost_auto_reply_table_id   = $this->input->post('auto_reply_template', true);
            $times                      = $this->input->post('repeat_times', true);
            $interval                   = $this->input->post('time_interval', true);

            $post_to_pages = array();
            if($this->input->post('post_to_pages', true) !== null)
                $post_to_pages = $this->input->post('post_to_pages', true);
            else
                $post_to_pages   = 0;
            

            $data = array(
                'user_id'                       => $user_id,
                'facebook_rx_fb_user_info_id'   => $facebook_rx_fb_user_info_id,
                'campaign_name'                 => $campaign_name,
                'message'                       => $message,
                'link'                          => $link,
                'link_preview_image'            => $link_preview_image,
                'link_caption'                  => $link_caption,
                'link_description'              => $link_description,
                'cta_type'                      => $cta_type,
                'cta_value'                     => $cta_value,
                'auto_share_post'               => '0',
                'auto_share_to_profile'         => '0',
                'auto_like_post'                => '0',
                'auto_private_reply'            => '0',
                'auto_private_reply_text'       => '0',
                'auto_comment'                  => '0',
                'auto_comment_text'             => '0',
                'schedule_time'                 => $schedule_time,
                'time_zone'                     => $time_zone,
                'page_group_user_id'            => $post_to_pages,
                // 'ultrapost_auto_reply_table_id' => $ultrapost_auto_reply_table_id,
                'auto_share_this_post_by_pages' => "",
                'repeat_times'                  => $times,
                'time_interval'                 => $interval,
                'schedule_type'                 => $schedule_type
            );

            $request_count = count($post_to_pages);
            if(isset($ultrapost_auto_reply_table_id))
                $data['ultrapost_auto_reply_table_id'] = $ultrapost_auto_reply_table_id;
    
            // $this->basic->delete_data('facebook_rx_cta_post',array('id'=>$id));
            // $this->basic->delete_data('facebook_rx_cta_post',array('parent_campaign_id'=>$id));
           // $this->basic->delete_data('facebook_rx_cta_post',array('full_complete'=>'0'));

            $this->basic->delete_data('facebook_rx_cta_post',array('id'=>$id,'user_id'=>$this->user_id));
            $this->basic->delete_data('facebook_rx_cta_post',array('parent_campaign_id'=>$id,'full_complete'=>'0' ,'user_id'=>$this->user_id));
            $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info");
            // $page_counter = 0;
            $user_id_array=array($this->user_id);

            $page_ids_string = implode(',', $post_to_pages);
            $data["page_ids"] = $page_ids_string;
            $page_info_arr = array();
            $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

            foreach ($page_info as $key => $value) {
               if(!in_array($value["id"], $post_to_pages)) continue;
               $page_info_arr[$value["id"]] = $value['page_name'];
            }

            $parent_id='';
            for ($insert_counter=0; $insert_counter < $request_count; $insert_counter++) { 

               $data['page_or_group_or_user'] = 'page';
               $data['page_group_user_id'] = $post_to_pages[$insert_counter];
               //$insert_data['page_or_group_or_user'] = $page_info_arr[$insert_data['page_group_user_id']];

               $x=$interval;
               for ($i=0; $i <=$times ; $i++) { 
                   
                    if($i == 0)
                    {
                        $data['schedule_time']= $schedule_time;
                        $this->basic->insert_data('facebook_rx_cta_post',$data);
                        $insert_id = $this->db->insert_id();
                        if($i == 0 && $insert_counter == 0)
                            $parent_id = $insert_id;
                            
                       

                    }
                    if ($times == 0) {
                          $data['is_child'] = '1';
                          $data['parent_campaign_id'] = $parent_id;
                    
                    }
                    if($i >= 1)
                    {


                        $data['is_child'] = '1';
                        $current_schedule_time = $schedule_time;
                        $dateTime = new DateTime($current_schedule_time);
                        $p = $i*$x;
                        $dateTime->modify("+{$p} minutes");
                        $data['parent_campaign_id'] = $parent_id;
                        $change_time= $dateTime->format('Y-m-d H:i:s');
                        $data['schedule_time']= $change_time;
                        $data['page_ids'];
                        $this->basic->insert_data('facebook_rx_cta_post',$data);
                    }
               }

            }

      

            
            if($insert_counter > 0)
            $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line('Facebook post information has been updated successfully.')); 
            else $return_val=array("status"=>"0","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("something went wrong, please try again."));
            echo json_encode($return_val);


        }
    }


    public function cta_post_meta_info_grabber()
    {
        if($_POST)
        {
            $link= $this->input->post("link");
            $this->load->library("fb_rx_login");
            $response=$this->fb_rx_login->get_meta_tag_fb($link);
            echo json_encode($response);
        }
    } 



    public function cta_post_upload_link_preview()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/ctapost";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="imagethumb_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }




    public function cta_post_delete_uploaded_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();

        $output_dir = FCPATH."upload_caster/ctapost/";
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


    public function offer_post()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(221,$this->module_access)) exit();
        $data['page_title'] = $this->lang->line("Offer Poster");
        $data['body'] = 'offer_post/offer_post_list';
        $data['time_zone'] = $this->_time_zone_list();
        $this->_viewcontroller($data);
    }



    public function offer_post_list_data()
    {
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $offer_name = trim($this->input->post("offer_name", true));
        $page_or_group_or_user_name = trim($this->input->post("page_or_group_or_user_name", true));
        $post_type = trim($this->input->post("post_type", true));
        $posting_status = trim($this->input->post("posting_status", true));

        $scheduled_from = trim($this->input->post('scheduled_from', true));        
        if($scheduled_from!="") $scheduled_from = date('Y-m-d H:i:s', strtotime($scheduled_from));

        $scheduled_to = trim($this->input->post('scheduled_to', true));
        if($scheduled_to!="") $scheduled_to = date('Y-m-d H:i:s', strtotime($scheduled_to));


        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            $this->session->set_userdata('facebook_rx_offer_poster_offer_name', $offer_name);
            $this->session->set_userdata('facebook_rx_offer_poster_page_or_group_or_user_name', $page_or_group_or_user_name);
            $this->session->set_userdata('facebook_rx_offer_poster_scheduled_from', $scheduled_from);
            $this->session->set_userdata('facebook_rx_offer_poster_scheduled_to', $scheduled_to);
            $this->session->set_userdata('facebook_rx_offer_poster_post_type', $post_type);
            $this->session->set_userdata('facebook_rx_offer_poster_posting_status', $posting_status);
        }

        $search_offer_name  = $this->session->userdata('facebook_rx_offer_poster_offer_name');
        $search_page_or_group_or_user_name  = $this->session->userdata('facebook_rx_offer_poster_page_or_group_or_user_name');
        $search_scheduled_from = $this->session->userdata('facebook_rx_offer_poster_scheduled_from');
        $search_scheduled_to = $this->session->userdata('facebook_rx_offer_poster_scheduled_to');
        $search_post_type= $this->session->userdata('facebook_rx_offer_poster_post_type');
        $search_posting_status= $this->session->userdata('facebook_rx_offer_poster_posting_status');

        $where_simple=array();
        
        if ($search_offer_name!="") $where_simple['offer_name like ']    = "%".$search_offer_name."%";
        if ($search_page_or_group_or_user_name!="") $where_simple['page_or_group_or_user_name like ']    = "%".$search_page_or_group_or_user_name."%";
        if ($search_post_type!="") $where_simple['post_type']  = $search_post_type;
        if ($search_posting_status!="") $where_simple['posting_status']  = $search_posting_status;
        if ($search_scheduled_from!="") 
        {
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') >="]= $search_scheduled_from;            
        }
        if ($search_scheduled_to!="") 
        {
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') <="]=$search_scheduled_to;            
        }

        $where_simple['user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "facebook_rx_offer_campaign_view";
        $select="facebook_rx_offer_campaign_view.*,posting_status as posting_status_original,schedule_time as schedule_time_original,post_type as post_type_original"; //
        $info = $this->basic->get_data($table, $where, $select, $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++) 
        {
            $posting_status = $info[$i]['posting_status'];
            if( $posting_status ==='2') $info[$i]['posting_status'] = '<span class="label label-success"><i class="fa fa-check"></i> '.$this->lang->line("Completed").'</span>';
            else if( $posting_status === '1') $info[$i]['posting_status'] = '<span class="label label-warning"><i class="fa fa-spinner"></i> '.$this->lang->line("Processing").'</span>';
            else $info[$i]['posting_status'] = '<span class="label label-danger"><i class="fa fa-clock-o"></i> '.$this->lang->line("Pending").'</span>';

            
            
        

            $post_type = $info[$i]['post_type'];
            $post_type = ucfirst(str_replace("_submit","",$post_type));
            $info[$i]['post_type'] =  $post_type;

            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
            $scheduled_at = date("M j, y H:i",strtotime($info[$i]['schedule_time']));
            else $scheduled_at = '<i class="fa fa-remove red" title="'.$this->lang->line("Instantly posted").'"></i>';
            $info[$i]['schedule_time'] =  $scheduled_at;

            $last_updated_at = date("M j, y H:i",strtotime($info[$i]['last_updated_at']));
            $info[$i]['last_updated_at'] =  $last_updated_at;

            // if(strlen($info[$i]["message"])>=60) $info[$i]["message"] = substr($info[$i]["message"], 0, 60)."...";
            // else $info[$i]["message"] = $info[$i]["message"];
            
            if($posting_status=='2')
            $post_url = "<a target='_BLANK' href='".$info[$i]['post_url']."'><span class='label label-info'><i class='fa fa-hand-o-right'></i> ".$this->lang->line('Visit')."</span></a>";
            else $post_url = '<i class="fa fa-remove red" title="This offer is not published yet."></i>';
            $info[$i]['visit_post'] =  $post_url;    

            $edit_url=base_url('ultrapost/edit_offer_post_campaign/'.$info[$i]['id']);
            $report_url=base_url('ultrapost/offer_post_report/'.$info[$i]['id']);

            $info[$i]['delete'] =  "<a title='Delete this offer from our database' id='".$info[$i]['id']."' class='delete btn-sm btn btn-danger'><i class='fa fa-remove'></i> ".$this->lang->line('Delete')."</a>"; 
            if($posting_status!='0' || $info[$i]['schedule_type']!='later' || $info[$i]["is_repost"]==='1')
            {
                 if($posting_status!='0')
                 $info[$i]['edit']='<i class="fa fa-remove red" title="Only pending offers can be edited."></i>';
                 else if($info[$i]['schedule_type']!='later' ) $info[$i]['edit']='<i class="fa fa-remove red" title="Only scheduled offers can be edited."></i>';
                 else  $info[$i]['edit']='<i class="fa fa-remove red" title="Reposted offers can not be edited."></i>';
            }
            else $info[$i]['edit'] =  "<a href='".$edit_url."' class='btn-sm btn btn-warning'><i class='fa fa-pencil'></i> ".$this->lang->line('Edit')."</a>"; 

            if($posting_status=='2')
            $info[$i]['use_this_offer'] =  "<a  class='btn-sm btn btn-primary repost' data-id='".$info[$i]['id']."'><i class='fa fa-copy'></i> ".$this->lang->line('Repost')."</a>"; 
            else $info[$i]['use_this_offer']='<i class="fa fa-remove red" title="Only completed offers can be reposted."></i>';

            $info[$i]['report'] =  "<a href='".$report_url."' target='__BLANK' class='btn-sm btn btn-default'><i class='fa fa-eye'></i> ".$this->lang->line('Details')."</a>"; 


        }

        echo convert_to_grid_data($info, $total_result);

    }

    public function offer_poster()
    {

        $data['body'] = 'offer_post/add_offer_post';
        $data['page_title'] = $this->lang->line('New Offer Post');
        $data["time_zone"]= $this->_time_zone_list();
        
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),$select='',$join='',$limit='',$start=NULL,$order_by='page_name asc');
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),$select='',$join='',$limit='',$start=NULL,$order_by='group_name asc');
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));  

        $data['barcode_types']=$this->_get_enum_values_barcode_type();      
        $data['post_types']=$this->_get_enum_values_post_type();      
        $data['offer_types']=$this->_get_enum_values_type_offer();      
        $data['location_types']=$this->_get_enum_values_location_type();      
        $data['currency_list']=$this->_get_currency();     
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));

        $this->_viewcontroller($data);
    }


    public function offer_post_create_campaign_action()
    {
        if(!$_POST)
        exit();
      
        $this->load->library("fb_rx_login");

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
           $$key=$value;
        }

        if($submit_post_hidden=="carousel_submit")
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(221,$this->module_access))
            {
                $error_msg = $this->lang->line("Sorry, your package does not allow carousel offer")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
                $return_val=array("status"=>"0","message"=>$error_msg);
                echo json_encode($return_val);
                exit();
            }   

            $status=$this->_check_usage($module_id=221,$request=1);
            if($status=="3") 
            {
                $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
                $return_val=array("status"=>"0","message"=>$error_msg);
                echo json_encode($return_val);
                exit();
            }
        }
        else
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(221,$this->module_access))
            {
                $error_msg = $this->lang->line("Sorry, your package does not allow image/video offer")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
                $return_val=array("status"=>"0","message"=>$error_msg);
                echo json_encode($return_val);
                exit();
            } 
            $status=$this->_check_usage($module_id=221,$request=1);
            if($status=="3") 
            {
                $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
                $return_val=array("status"=>"0","message"=>$error_msg);
                echo json_encode($return_val);
                exit();
            }
        }

        $auto_share_to_profile_database= "0";
        
        $user_id = $this->user_id;        
        $facebook_rx_fb_user_info_id = $this->session->userdata("facebook_rx_fb_user_info");       

        if(!isset($auto_share_this_post_by_pages)  || !is_array($auto_share_this_post_by_pages))  $auto_share_this_post_by_pages=array();
        if(!isset($auto_share_this_post_by_groups) || !is_array($auto_share_this_post_by_groups)) $auto_share_this_post_by_groups=array();
        $post_to_pages_array=array($post_to_page);
        $auto_share_this_post_by_pages_new = array_diff($auto_share_this_post_by_pages,$post_to_pages_array);   

    
        $user_id_array=array($user_id);  
        $account_switching_id = $facebook_rx_fb_user_info_id; // table > facebook_rx_fb_user_info.id  


        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));
        $offer_page_access_token =  "";
        $offer_fb_page_id =  "";
        $offer_page_auto_id =  "";
        $fb_photo_ids="";
        $fb_video_id="";

        foreach ($page_info as $key => $value) 
        {
            if(!in_array($value["id"], $post_to_pages_array)) continue;

            $offer_page_access_token =  isset($value["page_access_token"]) ? $value["page_access_token"] : ""; 
            $offer_fb_page_id =  isset($value["page_id"]) ? $value["page_id"] : "";
            $offer_page_auto_id =  isset($value["id"]) ? $value["id"] : ""; 
            $offer_page_name =  isset($value["page_name"]) ? $value["page_name"] : ""; 
            break; // because offer post goes only one page
        } 

            
        if($schedule_type=="now")
        {  

            $this->db->trans_start();           

            if($submit_post_hidden=="image_submit")
            {
                try
                {
                    $response = $this->fb_rx_login->photo_post_no_story("",$image_url,"",$offer_page_access_token,$offer_fb_page_id);
                }
                catch(Exception $e) 
                {
                  $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                  $return_val=array("status"=>"0","message"=>$error_msg);
                  echo json_encode($return_val);
                  exit();
                }
                $fb_photo_ids=isset($response['id']) ? $response['id'] : "";
                $fb_photo_id_array=array($fb_photo_ids);
                $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>$fb_photo_ids,"upload_type"=>"image","file_location"=>$image_url,"posting_status"=>"2"));
                $upload_ids=$this->db->insert_id();
            } //// end of image block

            else if($submit_post_hidden=="carousel_submit")
            {
                $fb_photo_id_array=array();
                $upload_id_array=array();
                for($i=1;$i<=5;$i++)
                {
                    $temp="carousel_image_link_".$i;
                    $file_url=$$temp;
                    if($file_url=="") continue;

                    try
                    {
                        $response = $this->fb_rx_login->photo_post_no_story("",$file_url,"",$offer_page_access_token,$offer_fb_page_id);
                    }
                    catch(Exception $e) 
                    {
                      $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                      $return_val=array("status"=>"0","message"=>$error_msg);
                      echo json_encode($return_val);
                      exit();
                    }
                    $current_photo_id=isset($response['id']) ? $response['id'] : "";
                    $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>$current_photo_id,"upload_type"=>"image","file_location"=>$file_url,"posting_status"=>"2"));
                    $current_upload_id=$this->db->insert_id();
                    $fb_photo_id_array[]=$current_photo_id;
                    $upload_id_array[]=$current_upload_id;
                }
                $fb_photo_id_array=array_filter($fb_photo_id_array);
                $fb_photo_ids=implode(',',$fb_photo_id_array);
                $upload_id_array=array_filter($upload_id_array);
                $upload_ids=implode(',',$upload_id_array);
            } // end of carousel block

            else // video_submit
            {                
                try
                {
                    $response = $this->fb_rx_login->post_video_no_story("","",$video_url,"",$video_thumb_url,"",$offer_page_access_token,$offer_fb_page_id);
                }
                catch(Exception $e) 
                {
                  $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                  $return_val=array("status"=>"0","message"=>$error_msg);
                  echo json_encode($return_val);
                  exit();
                }
                $fb_video_id=isset($response['id']) ? $response['id'] : "";
                $fb_video_id_array=array($fb_video_id);
                $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>$fb_video_id,"upload_type"=>"video","file_location"=>$video_url,"thumbnail_location"=>$video_thumb_url,"posting_status"=>"2"));
                $upload_ids=$this->db->insert_id();
            } // end of video block


            try
            {
                $response_campaign=$this->fb_rx_login->create_native_offer($offer_fb_page_id,$offer_page_access_token,$type_offer,$discount_title,$discount_value,$offer_details,$expiration_time,$link,$location_type,$terms_condition,$max_save_count,$online_coupon_code,$barcode_type,$barcode_value,$store_coupon_code,$currency,$expiry_time_zone);
            }
            catch(Exception $e) 
            {
              $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
              $return_val=array("status"=>"0","message"=>$error_msg);
              echo json_encode($return_val);
              exit();
            }

            $fb_offer_id=isset($response_campaign['id']) ? $response_campaign['id'] : "";

            $insert_data_campaign=array
            (
                "user_id"=>$user_id,
                "facebook_rx_fb_user_info_id" => $facebook_rx_fb_user_info_id,
                "fb_offer_id" => $fb_offer_id,
                "post_type"=>$submit_post_hidden,
                "campaign_name"=>$campaign_name,
                "page_group_user_id"=>$offer_page_auto_id,
                "page_or_group_or_user" =>"page",
                "page_or_group_or_user_name"=>$offer_page_name,
                "link"=>$link,
                "message"=>$message,
                "offer_details"=>$offer_details,
                "type_offer"=>$type_offer,
                "discount_title"=>$discount_title,
                "discount_value"=>$discount_value,
                "currency"=>$currency,
                "expiration_time"=>$expiration_time,
                "expiry_time_zone"=>$expiry_time_zone,
                "location_type"=>$location_type,
                "online_coupon_code"=>$online_coupon_code,
                "store_coupon_code"=>$store_coupon_code,
                "barcode_type"=>$barcode_type,
                "barcode_value"=>$barcode_value,
                "terms_condition"=>$terms_condition,
                "fb_photo_ids"=>$fb_photo_ids,
                "upload_ids"=>$upload_ids,
                "fb_video_id"=>$fb_video_id,
                // "ultrapost_auto_reply_table_id"=>$auto_reply_template,
                "posting_status"=>"2",
                "last_updated_at"=>date("Y-m-d H:i:s"),
                "schedule_time"=>$schedule_time,
                "schedule_type"=>$schedule_type,
                "time_zone"=>$time_zone
            );

            if(isset($auto_reply_template))
                $insert_data_campaign['ultrapost_auto_reply_table_id'] = $auto_reply_template;

            $this->basic->insert_data("facebook_rx_offer_campaign",$insert_data_campaign);
            $campaign_id=$this->db->insert_id();

            try
            {
                if($submit_post_hidden=="image_submit" || $submit_post_hidden=="carousel_submit")
                $response_campaign_view=$this->fb_rx_login->create_native_offer_views($fb_offer_id,$offer_page_access_token,$fb_photo_id_array,"",$message);
                else $response_campaign_view=$this->fb_rx_login->create_native_offer_views($fb_offer_id,$offer_page_access_token,"",$fb_video_id_array,$message);
            }
            catch(Exception $e) 
            {
              $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
              $return_val=array("status"=>"0","message"=>$error_msg);
              echo json_encode($return_val);
              exit();
            }

            if(!isset($response_campaign_view['success']) || $response_campaign_view['success']!='1')
            {
              $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
              $return_val=array("status"=>"0","message"=>$error_msg);
              echo json_encode($return_val);
              exit();
            }

            $offer_post_id=isset($response_campaign_view['post']) ? $response_campaign_view['post'] : "";
            $native_offer_view_id=isset($response_campaign_view['native_offer_view']) ? $response_campaign_view['native_offer_view'] : "";
           
            
            try
            {
                $pageid_postid=$offer_fb_page_id.'_'.$offer_post_id;   
                $post_url_data=$this->fb_rx_login->get_post_permalink($pageid_postid,$offer_page_access_token); // requires pageid_post id, singular links API is deprecated for versions v2.4 and higher
            }
            catch(Exception $e) 
            {
              $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
              $return_val=array("status"=>"0","message"=>$error_msg);
              echo json_encode($return_val);
              exit();
            }
            $offer_post_url= isset($post_url_data["permalink_url"]) ? $post_url_data["permalink_url"] : ""; 
            $offer_post_url=str_replace('www.facebook.com', 'web.facebook.com', $offer_post_url);

            $insert_data_campaign_view=array
            (
                "user_id"=>$user_id,
                "facebook_rx_fb_user_info_id" => $facebook_rx_fb_user_info_id,
                "campaign_id" => $campaign_id,
                "offer_name" => $campaign_name,
                "message"=>$message,
                "fb_offer_id" => $fb_offer_id,
                "post_id" => $pageid_postid,
                "native_offer_view_id" => $native_offer_view_id,
                "post_url" => $offer_post_url,
                "post_type"=>$submit_post_hidden,
                "page_group_user_id"=>$offer_page_auto_id,
                "page_or_group_or_user" =>"page",
                "page_or_group_or_user_name"=>$offer_page_name, 
                "auto_share_post"=> 0,
                "auto_share_this_post_by_pages"=>json_encode($auto_share_this_post_by_pages_new),
                "auto_share_this_post_by_groups"=>json_encode($auto_share_this_post_by_groups),
                "auto_share_to_profile"=>$auto_share_to_profile_database,            
                "auto_like_post"=> 0, 
                "auto_comment"=> 0,            
                "auto_comment_text"=> 0,                       
                "posting_status"=>"2",
                "last_updated_at"=>date("Y-m-d H:i:s"),
                "schedule_time"=>$schedule_time,
                "schedule_type"=>$schedule_type,
                "time_zone"=>$time_zone
            ); 

            $this->basic->insert_data("facebook_rx_offer_campaign_view",$insert_data_campaign_view);
            $campaign_view_id=$this->db->insert_id();  

            $this->basic->update_data("facebook_rx_offer_campaign",array("id"=>$campaign_id),array("campaign_view_id"=>$campaign_view_id)); 



            if(isset($auto_reply_template) && $auto_reply_template!='0')
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1);
                if($status!="2" && $status!="3") 
                {


                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $auto_reply_template ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['facebook_rx_fb_user_info_id' => $facebook_rx_fb_user_info_id]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }



                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $facebook_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $offer_page_name;
                    $auto_reply_table_data['post_id'] = $pageid_postid;
                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                    $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;


                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                 
                     $this->_insert_usage_log($module_id=204,$request=1);                        
                 }
                //************************************************//
            }

            //=================================================================
            $this->_insert_usage_log($module_id=221,$request=1);   
            //=================================================================

            $this->db->trans_complete();

            $share_like_msg="";
            $video_process_msg="";
            
            if($submit_post_hidden=="video_submit") $video_process_msg="<br>It may take couple of minutes to process the video and appear in the offer.";

            $return_val=array("id"=>$campaign_view_id,"status"=>"1","message"=>"<i class='fa fa-check-circle'></i>  ".$this->lang->line('Facebook offer has been created successfully')." : <a target='__BLANK' href='".$offer_post_url."'>".$this->lang->line('Visit Offer Now')."</a>.".$video_process_msg.$share_like_msg);
            echo json_encode($return_val);

        } // end of now block


        if($schedule_type=="later")
        {
            $this->db->trans_start();  

            if($submit_post_hidden=="image_submit")
            {                
                $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>"","upload_type"=>"image","file_location"=>$image_url,"posting_status"=>"0"));
                $upload_ids=$this->db->insert_id();
            }

            else if($submit_post_hidden=="carousel_submit")
            {
                $upload_id_array=array();
                for($i=1;$i<=5;$i++)
                {
                    $temp="carousel_image_link_".$i;
                    $file_url=$$temp;
                    if($file_url=="") continue;
                    $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>"","upload_type"=>"image","file_location"=>$file_url,"posting_status"=>"0"));
                    $current_upload_id=$this->db->insert_id();
                    $upload_id_array[]=$current_upload_id;
                }
                $upload_ids=implode(',',$upload_id_array);
            } // end of carousel block

            else // video_submit
            {
                if($video_url!="")
                {
                    if(strpos($video_url, 'youtube.com') !== false) 
                    {
                        parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
                        $youtube_video_id = isset($my_array_of_vars['v']) ? $my_array_of_vars['v'] : ""; 
                        
                        if($youtube_video_id!="")
                        {
                            $video_url = $this->fb_rx_login->get_youtube_video_url($youtube_video_id);
                        }
                    }
                }
                $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>"","upload_type"=>"video","file_location"=>$video_url,"posting_status"=>"0"));
                $upload_ids=$this->db->insert_id();
            } // end of video block


            $insert_data_campaign=array
            (
                "user_id"=>$user_id,
                "facebook_rx_fb_user_info_id" => $facebook_rx_fb_user_info_id,
                "fb_offer_id" => "",
                "post_type"=>$submit_post_hidden,
                "campaign_name"=>$campaign_name,
                "page_group_user_id"=>$offer_page_auto_id,
                "page_or_group_or_user" =>"page",
                "page_or_group_or_user_name"=>$offer_page_name,
                "link"=>$link,
                "message"=>$message,
                "offer_details"=>$offer_details,
                "type_offer"=>$type_offer,
                "discount_title"=>$discount_title,
                "discount_value"=>$discount_value,
                "currency"=>$currency,
                "expiration_time"=>$expiration_time,
                "expiry_time_zone"=>$expiry_time_zone,
                "location_type"=>$location_type,
                "online_coupon_code"=>$online_coupon_code,
                "store_coupon_code"=>$store_coupon_code,
                "barcode_type"=>$barcode_type,
                "barcode_value"=>$barcode_value,
                "terms_condition"=>$terms_condition,
                "fb_photo_ids"=>"",
                "upload_ids"=>$upload_ids,
                "fb_video_id"=>"",
                // "ultrapost_auto_reply_table_id"=>$auto_reply_template,
                "posting_status"=>"0",
                "last_updated_at"=>date("Y-m-d H:i:s"),
                "schedule_time"=>$schedule_time,
                "schedule_type"=>$schedule_type,
                "time_zone"=>$time_zone
            );

            if(isset($auto_reply_template))
                $insert_data_campaign['ultrapost_auto_reply_table_id'] = $auto_reply_template;

            $this->basic->insert_data("facebook_rx_offer_campaign",$insert_data_campaign);  
            $campaign_id=$this->db->insert_id();


            $insert_data_campaign_view=array
            (
                "user_id"=>$user_id,
                "facebook_rx_fb_user_info_id" => $facebook_rx_fb_user_info_id,
                "campaign_id" => $campaign_id,
                "offer_name"=>$campaign_name,
                "message"=>$message,
                "fb_offer_id" => "",
                "post_id" => "",
                "native_offer_view_id" => "",
                "post_url" => "",
                "post_type"=>$submit_post_hidden,
                "page_group_user_id"=>$offer_page_auto_id,
                "page_or_group_or_user" =>"page",
                "page_or_group_or_user_name"=>$offer_page_name, 
                "auto_share_post"=> 0,
                "auto_share_this_post_by_pages"=>json_encode($auto_share_this_post_by_pages_new),
                "auto_share_this_post_by_groups"=>json_encode($auto_share_this_post_by_groups),
                "auto_share_to_profile"=>$auto_share_to_profile_database,            
                "auto_like_post"=> 0,            
                "auto_comment"=> 0,            
                "auto_comment_text"=> 0,            
                "posting_status"=>"0",
                "last_updated_at"=>date("Y-m-d H:i:s"),
                "schedule_time"=>$schedule_time,
                "schedule_type"=>$schedule_type,
                "time_zone"=>$time_zone
            ); 
            $this->basic->insert_data("facebook_rx_offer_campaign_view",$insert_data_campaign_view);   
            $campaign_view_id=$this->db->insert_id();  

            $this->basic->update_data("facebook_rx_offer_campaign",array("id"=>$campaign_id),array("campaign_view_id"=>$campaign_view_id)); 

            //=================================================================
            $this->_insert_usage_log($module_id=221,$request=1);   
            //=================================================================          

            $this->db->trans_complete();

            $return_val=array("id"=>$campaign_view_id,"status"=>"1","message"=>"<i class='fa fa-check-circle'></i>  ".$this->lang->line('Facebook offer has been submitted successfully.'));
            echo json_encode($return_val);

        } // end of now block

         
    }



    public function edit_offer_post_campaign($id=0)
    {
        if($id==0) exit();

        $xdata_campaign_view=$this->basic->get_data("facebook_rx_offer_campaign_view",array('where'=>array("id"=>$id,"user_id"=>$this->user_id)));
        $facebook_rx_offer_campaign_id=isset($xdata_campaign_view[0]["campaign_id"])?$xdata_campaign_view[0]["campaign_id"]:0;
        $xdata_campaign=$this->basic->get_data("facebook_rx_offer_campaign",array('where'=>array("id"=>$facebook_rx_offer_campaign_id,"user_id"=>$this->user_id)));
        if(!isset($xdata_campaign_view[0]) || !isset($xdata_campaign[0])) 
        {
            echo "Offer not found";
            exit();
        }
        if($xdata_campaign_view[0]["posting_status"]!='0') 
        {
            echo "Only pending offers can be edited.";
            exit();
        }
        if($xdata_campaign_view[0]["schedule_type"]!="later") 
        {
            echo "Only scheduled offers can be edited.";
            exit();
        }
        if($xdata_campaign_view[0]["is_repost"]==="1") 
        {
            echo "Reposted offeres can not be edited.";
            exit();
        }

        $xpost_type=isset($xdata_campaign_view[0]['post_type']) ? $xdata_campaign_view[0]['post_type'] : "image_submit";

        if($xpost_type=="carousel_submit")
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(221,$this->module_access))
            {
                echo $this->lang->line("Sorry, your package does not allow carousel offer")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
                exit();
            }   
        }
        else
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array(221,$this->module_access))
            {
                echo $this->lang->line("Sorry, your package does not allow image/video offer")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
                exit();
            } 
        }

        $upload_id_array=array();
        $upload_id_array=explode(',', $xdata_campaign[0]['upload_ids']);
        $xupload_data=$this->basic->get_data("facebook_rx_offer_upload",array('where_in'=>array('id'=>$upload_id_array)));

        $data['body'] = 'offer_post/edit_offer_post';
        $data['page_title'] = $this->lang->line('Edit Offer Post');
        $data["time_zone"]= $this->_time_zone_list();
       
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),$select='',$join='',$limit='',$start=NULL,$order_by='page_name asc');
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),$select='',$join='',$limit='',$start=NULL,$order_by='group_name asc');
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));  

        $data['barcode_types']=$this->_get_enum_values_barcode_type();      
        $data['post_types']=$this->_get_enum_values_post_type();      
        $data['offer_types']=$this->_get_enum_values_type_offer();      
        $data['location_types']=$this->_get_enum_values_location_type();      
        $data['currency_list']=$this->_get_currency();        
        $data['xdata_campaign_view']=$xdata_campaign_view[0];        
        $data['xdata_campaign']=$xdata_campaign[0];        
        $data['xupload_data']=$xupload_data;  
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));      

        $this->_viewcontroller($data);
    }



    public function edit_offer_post_campaign_action()
    {
        if(!$_POST)
        exit();
      
        $this->load->library("fb_rx_login");

        $post=$_POST;
        foreach ($post as $key => $value) 
        {
           $$key=$value;
        }
        $schedule_type='later'; // it must be later

        $xdata=$this->basic->get_data("facebook_rx_offer_campaign_view",array("where"=>array("id"=>$hidden_id)));
        if(!isset($xdata[0]) || $xdata[0]["posting_status"]!='0')
        {
            $error_msg = $this->lang->line("This offer is not in a editable stage anymore.");
            $return_val=array("status"=>"0","message"=>$error_msg);
            echo json_encode($return_val);
            exit();
     
        }


        $auto_share_to_profile_database= "0";
        

        $user_id = $this->user_id;        
        $facebook_rx_fb_user_info_id = $this->session->userdata("facebook_rx_fb_user_info");       

        if(!isset($auto_share_this_post_by_pages)  || !is_array($auto_share_this_post_by_pages))  $auto_share_this_post_by_pages=array();
        if(!isset($auto_share_this_post_by_groups) || !is_array($auto_share_this_post_by_groups)) $auto_share_this_post_by_groups=array();
        $post_to_pages_array=array($post_to_page);
        $auto_share_this_post_by_pages_new = array_diff($auto_share_this_post_by_pages,$post_to_pages_array);   

    
        $user_id_array=array($user_id);  
        $account_switching_id = $facebook_rx_fb_user_info_id; // table > facebook_rx_fb_user_info.id  


        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));
        $offer_page_access_token =  "";
        $offer_fb_page_id =  "";
        $offer_page_auto_id =  "";
        $fb_photo_ids="";
        $fb_video_id="";

        foreach ($page_info as $key => $value) 
        {
            if(!in_array($value["id"], $post_to_pages_array)) continue;

            $offer_page_access_token =  isset($value["page_access_token"]) ? $value["page_access_token"] : ""; 
            $offer_fb_page_id =  isset($value["page_id"]) ? $value["page_id"] : "";
            $offer_page_auto_id =  isset($value["id"]) ? $value["id"] : ""; 
            $offer_page_name =  isset($value["page_name"]) ? $value["page_name"] : ""; 
            break; // because offer post goes only one page
        } 


        if($schedule_type=="later")
        {
            $this->db->trans_start();  

            if($submit_post_hidden=="image_submit")
            {                
                $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>"","upload_type"=>"image","file_location"=>$image_url,"posting_status"=>"0"));
                $upload_ids=$this->db->insert_id();
            }

            else if($submit_post_hidden=="carousel_submit")
            {
                $upload_id_array=array();
                for($i=1;$i<=5;$i++)
                {
                    $temp="carousel_image_link_".$i;
                    $file_url=$$temp;
                    if($file_url=="") continue;
                    $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>"","upload_type"=>"image","file_location"=>$file_url,"posting_status"=>"0"));
                    $current_upload_id=$this->db->insert_id();
                    $upload_id_array[]=$current_upload_id;
                }
                $upload_ids=implode(',',$upload_id_array);
            } // end of carousel block

            else // video_submit
            {
                if($video_url!="")
                {
                    if(strpos($video_url, 'youtube.com') !== false) 
                    {
                        parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
                        $youtube_video_id = isset($my_array_of_vars['v']) ? $my_array_of_vars['v'] : ""; 
                        
                        if($youtube_video_id!="")
                        {
                            $video_url = $this->fb_rx_login->get_youtube_video_url($youtube_video_id);
                        }
                    }
                }
                $this->basic->insert_data("facebook_rx_offer_upload",array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>"","upload_type"=>"video","file_location"=>$video_url,"posting_status"=>"0"));
                $upload_ids=$this->db->insert_id();
            } // end of video block


            $insert_data_campaign=array
            (
                "fb_offer_id" => "",
                "post_type"=>$submit_post_hidden,
                "campaign_name"=>$campaign_name,
                "page_group_user_id"=>$offer_page_auto_id,
                "page_or_group_or_user" =>"page",
                "page_or_group_or_user_name"=>$offer_page_name,
                "link"=>$link,
                "message"=>$message,
                "offer_details"=>$offer_details,
                "type_offer"=>$type_offer,
                "discount_title"=>$discount_title,
                "discount_value"=>$discount_value,
                "currency"=>$currency,
                "expiration_time"=>$expiration_time,
                "expiry_time_zone"=>$expiry_time_zone,
                "location_type"=>$location_type,
                "online_coupon_code"=>$online_coupon_code,
                "store_coupon_code"=>$store_coupon_code,
                "barcode_type"=>$barcode_type,
                "barcode_value"=>$barcode_value,
                "terms_condition"=>$terms_condition,
                "fb_photo_ids"=>"",
                "upload_ids"=>$upload_ids,
                "fb_video_id"=>"",
                // "ultrapost_auto_reply_table_id"=>$auto_reply_template,
                "posting_status"=>"0",
                "last_updated_at"=>date("Y-m-d H:i:s"),
                "schedule_time"=>$schedule_time,
                "schedule_type"=>$schedule_type,
                "time_zone"=>$time_zone
            );

            if(isset($auto_reply_template))
                $insert_data_campaign['ultrapost_auto_reply_table_id'] = $auto_reply_template;

            $this->basic->update_data("facebook_rx_offer_campaign",array('id'=>$hidden_campaign_id,"user_id"=>$user_id),$insert_data_campaign);  
           
            $insert_data_campaign_view=array
            (
                "offer_name"=>$campaign_name,
                "message"=>$message,
                "fb_offer_id" => "",
                "post_id" => "",
                "native_offer_view_id" => "",
                "post_url" => "",
                "post_type"=>$submit_post_hidden,
                "page_group_user_id"=>$offer_page_auto_id,
                "page_or_group_or_user" =>"page",
                "page_or_group_or_user_name"=>$offer_page_name, 
                "auto_share_post"=> 0,
                "auto_share_this_post_by_pages"=>json_encode($auto_share_this_post_by_pages_new),
                "auto_share_this_post_by_groups"=>json_encode($auto_share_this_post_by_groups),
                "auto_share_to_profile"=>$auto_share_to_profile_database,            
                "auto_like_post"=> 0,            
                "auto_comment"=> 0,            
                "auto_comment_text"=> 0,            
                "posting_status"=>"0",
                "last_updated_at"=>date("Y-m-d H:i:s"),
                "schedule_time"=>$schedule_time,
                "schedule_type"=>$schedule_type,
                "time_zone"=>$time_zone
            ); 
            $this->basic->update_data("facebook_rx_offer_campaign_view",array("id"=>$hidden_id,"user_id"=>$user_id),$insert_data_campaign_view);             

            $this->db->trans_complete();

            $return_val=array("id"=>$hidden_id,"status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line('Facebook offer has been edited successfully.'));
            echo json_encode($return_val);

        } 

    

        
    }



    public function offer_post_repost_form_data()
    {
        if(!$_POST) exit();
        $offer_id=$this->input->post('offer_id');
        $xdata_campaign_view=$this->basic->get_data("facebook_rx_offer_campaign_view",array('where'=>array("id"=>$offer_id,"user_id"=>$this->user_id)));
        if(isset($xdata_campaign_view[0])) 
        {
            echo $xdata_campaign_view[0]['message'];
        }
    }

    public function offer_post_repost_action()
    {
        if(!$_POST) exit();

        $offer_id=$this->input->post('offer_id');
        $message=$this->input->post('message');
        $repost_schedule_time=$this->input->post('repost_schedule_time');
        $repost_time_zone=$this->input->post('repost_time_zone');
        $xdata_campaign_view=$this->basic->get_data("facebook_rx_offer_campaign_view",array('where'=>array("id"=>$offer_id,"user_id"=>$this->user_id)));
        if(!isset($xdata_campaign_view[0])) 
        {
            echo '0';
            $this->session->set_flashdata('repost_error_message',"Source offer data not found.");
            exit();
        }
        $facebook_rx_offer_campaign_id=isset($xdata_campaign_view[0]['campaign_id']) ? $xdata_campaign_view[0]['campaign_id'] : 0;
        $xdata_campaign=$this->basic->get_data("facebook_rx_offer_campaign",array('where'=>array("id"=>$facebook_rx_offer_campaign_id,"user_id"=>$this->user_id)));
        if(!isset($xdata_campaign[0])) 
        {
            echo '0';
            $this->session->set_flashdata('repost_error_message',"Source offer data not found.");
            exit();
        }
        date_default_timezone_set($xdata_campaign[0]["expiry_time_zone"]);
        if($xdata_campaign[0]["expiration_time"]<date("Y-m-d H:i:s"))
        {
            echo '2';
            $this->session->set_flashdata('repost_error_message','The offer is expired and can not be reposted.');
            exit();
        }

        $new_offer_data=$xdata_campaign_view[0];
        $new_offer_data['message']=$message;
        $new_offer_data['post_id']="";
        $new_offer_data['native_offer_view_id']="";
        $new_offer_data['post_url']="";
        $new_offer_data['posting_status']="0";
        $new_offer_data['last_updated_at']=date('Y-m-d H:i:s');
        $new_offer_data['schedule_type']='later';
        $new_offer_data['time_zone']=$repost_time_zone;
        $new_offer_data['schedule_time']=$repost_schedule_time;
        $new_offer_data['is_repost']='1';
        $new_offer_data['post_auto_like_cron_jon_status']='0';
        $new_offer_data['post_auto_share_cron_jon_status']='0';
        $new_offer_data['post_auto_share_group_cron_jon_status']='0';
        $new_offer_data['post_auto_comment_cron_jon_status']='0';
        unset($new_offer_data['id']);

        if($this->basic->insert_data("facebook_rx_offer_campaign_view",$new_offer_data)) 
        {
            echo '1';
            $this->session->set_flashdata('repost_success_message','Offer has been submitted for reposting.');

        }
        else 
        {
            echo '0';
            $this->session->set_flashdata('repost_error_message',$this->lang->line("something went wrong, please try again."));
        }
       
    }



    public function offer_post_cron_job($api_key="")
    {
        $this->api_key_check($api_key);

        $where['where']=array("posting_status"=>"0","schedule_type"=>"later");
        /***   Taking fist 100 post for offer post ***/
        $post_info= $this->basic->get_data("facebook_rx_offer_campaign",$where,$select='',$join='',$limit=25, $start=0, $order_by='schedule_time ASC');


        $database = array();

        $campaign_id_array=array();

        foreach($post_info as $info)
        {
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time']; 

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");
            
            if(strtotime($now_time) < strtotime($schedule_time)) continue; 

            $campaign_id_array[] = $info['id'];       
        }

        if(empty($campaign_id_array)) exit();

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("facebook_rx_offer_campaign",array("posting_status"=>"1"));
        // setting fb confid id for library call  
        $this->load->library("fb_rx_login");
       
        $config_id_database = array();
        foreach($post_info as $info)
        {    
            $campaign_id= $info['id'];
            $campaign_view_id= $info['campaign_view_id'];
            $user_id= $info['user_id'];
            $post_type= $info['post_type'];
            $link= $info['link'];
            $message= $info['message'];
            $offer_details= $info['offer_details'];
            $type_offer= $info['type_offer'];
            $max_save_count= $info['max_save_count'];
            $discount_title= $info['discount_title'];
            $discount_value= $info['discount_value'];
            $currency= $info['currency'];
            $expiration_time= $info['expiration_time'];
            $expiry_time_zone= $info['expiry_time_zone'];
            $location_type= $info['location_type'];
            $online_coupon_code= $info['online_coupon_code'];
            $store_coupon_code= $info['store_coupon_code'];
            $barcode_type= $info['barcode_type'];
            $barcode_value= $info['barcode_value'];
            $terms_condition= $info['terms_condition'];
            $ultrapost_auto_reply_table_id = $info['ultrapost_auto_reply_table_id'];

            if(!in_array($campaign_id, $campaign_id_array)) continue;

            $upload_ids=$info['upload_ids'];
            if($upload_ids=="") continue;


            // setting fb confid id for library call
            $page_group_user_id=$info["page_group_user_id"];
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            $page_or_group_or_user= $info['page_or_group_or_user'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            // $this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            //library initialize
            $this->fb_rx_login->app_initialize($config_id_database[$fb_rx_fb_user_info_id]);

            $table_name = "facebook_rx_fb_page_info";
            $fb_id_field =  "page_id";
            $access_token_field =  "page_access_token";  
            if(!isset($database[$page_or_group_or_user][$page_group_user_id])) // if not exists in database
            {
                $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));
                     
                $use_access_token = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";
                $use_fb_id = isset($access_data["0"][$fb_id_field]) ? $access_data["0"][$fb_id_field] : "";
                
                //inserting new data in database
                $database[$page_or_group_or_user][$page_group_user_id] = array("use_access_token"=>$use_access_token,"use_fb_id"=>$use_fb_id);
            }
            $use_access_token = isset($database[$page_or_group_or_user][$page_group_user_id]["use_access_token"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_access_token"] : "";
            $use_fb_id = isset($database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"] : "";

            $upload_id_array=explode(',',$upload_ids);
            $upload_where=array("where_in"=>array("id"=>$upload_id_array));
            $upload_data=$this->basic->get_data("facebook_rx_offer_upload",$upload_where);
            $upload_error=0;

            $fb_photo_ids_now=array();
            $fb_video_id_now=array();

            foreach ($upload_data as $key => $value) 
            {
                $file_location=$value["file_location"];
                $thumbnail_location=$value["thumbnail_location"];
                $file_id=$value["id"];
                $upload_type=$value["upload_type"];
              
                try
                {                    
                    if($upload_type=="image")
                    {
                        $response = $this->fb_rx_login->photo_post_no_story("",$file_location,"",$use_access_token,$use_fb_id);
                        $fb_photo_ids_now[]=isset($response['id']) ? $response['id'] : "";
                    }
                    else 
                    {
                        $response = $this->fb_rx_login->post_video_no_story("","",$file_location,"",$thumbnail_location,"",$use_access_token,$use_fb_id);
                        $fb_video_id_now[]=isset($response['id']) ? $response['id'] : "";
                    }

                    $fb_photo_video_ids=isset($response['id']) ? $response['id'] : "";
                    $this->basic->update_data("facebook_rx_offer_upload",array("id"=>$file_id),array("upload_time"=>date("Y-m-d H:i:s"),"fb_photo_video_id"=>$fb_photo_video_ids,"posting_status"=>"2"));

                }
                catch(Exception $e) 
                {
                  $error_msg = $e->getMessage();
                  $this->basic->update_data("facebook_rx_offer_upload",array("id"=>$file_id),array("error_message"=>$error_msg,"posting_status"=>"2"));
                  $this->basic->update_data("facebook_rx_offer_campaign",array("id"=>$campaign_id),array("error_message"=>$error_msg,"posting_status"=>"2","last_updated_at"=>date("Y-m-d H:i:s")));
                  $upload_error=1;
                }
            }

            if($upload_error==1) continue;

            try
            {
                $response_campaign=$this->fb_rx_login->create_native_offer($use_fb_id,$use_access_token,$type_offer,$discount_title,$discount_value,$offer_details,$expiration_time,$link,$location_type,$terms_condition,$max_save_count,$online_coupon_code,$barcode_type,$barcode_value,$store_coupon_code,$currency,$expiry_time_zone);
                $fb_offer_id=isset($response_campaign['id']) ? $response_campaign['id'] : "";
                $fb_photo_ids_now_str=implode(',',$fb_photo_ids_now);
                $fb_video_id_now_str=implode(',',$fb_video_id_now);
                $this->basic->update_data("facebook_rx_offer_campaign",array("id"=>$campaign_id),array("fb_offer_id"=>$fb_offer_id,"posting_status"=>"2","fb_photo_ids"=>$fb_photo_ids_now_str,"fb_video_id"=>$fb_video_id_now_str,"last_updated_at"=>date("Y-m-d H:i:s")));
            }
            catch(Exception $e) 
            {
              $error_msg = $e->getMessage();
              $this->basic->update_data("facebook_rx_offer_campaign",array("id"=>$campaign_id),array("posting_status"=>"2","error_message"=>$error_msg,"last_updated_at"=>date("Y-m-d H:i:s")));
              continue;
            }

           

            try
            {
                if($post_type=="image_submit" || $post_type=="carousel_submit")
                $response_campaign_view=$this->fb_rx_login->create_native_offer_views($fb_offer_id,$use_access_token,$fb_photo_ids_now,"",$message);
                else $response_campaign_view=$this->fb_rx_login->create_native_offer_views($fb_offer_id,$use_access_token,"",$fb_video_id_now,$message);

                $offer_post_id=isset($response_campaign_view['post']) ? $response_campaign_view['post'] : "";
                $native_offer_view_id=isset($response_campaign_view['native_offer_view']) ? $response_campaign_view['native_offer_view'] : "";               
                $pageid_postid=$use_fb_id.'_'.$offer_post_id;   
                
                try
                {
                    $post_url_data=$this->fb_rx_login->get_post_permalink($pageid_postid,$use_access_token); // requires pageid_post id, singular links API is deprecated for versions v2.4 and higher
                }
                catch(Exception $e) 
                {
                    $error_msg = $e->getMessage();
                }

                $offer_post_url= isset($post_url_data["permalink_url"]) ? $post_url_data["permalink_url"] : ""; 
                $offer_post_url=str_replace('www.facebook.com', 'web.facebook.com', $offer_post_url);

                $update_data_campaign_view=array
                (                
                    "fb_offer_id" => $fb_offer_id,
                    "post_id" => $pageid_postid,
                    "native_offer_view_id" => $native_offer_view_id,
                    "post_url" => $offer_post_url,
                    "last_updated_at"=>date("Y-m-d H:i:s"),
                    "posting_status"=>"2"
                ); 
                $this->basic->update_data("facebook_rx_offer_campaign_view",array("id"=>$campaign_view_id),$update_data_campaign_view);
            }
            catch(Exception $e) 
            {
                $error_msg = $e->getMessage();   
                $update_data_campaign_view=array
                (      
                    "last_updated_at"=>date("Y-m-d H:i:s"),
                    "posting_status"=>"2",
                    "error_message" => $error_message
                ); 
                $this->basic->update_data("facebook_rx_offer_campaign_view",array("id"=>$campaign_view_id),$update_data_campaign_view);           
            }     



            if($ultrapost_auto_reply_table_id != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1);
                if($status!="2" && $status!="3") 
                {


                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $ultrapost_auto_reply_table_id ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }



                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];
                    $auto_reply_table_data['post_id'] = $pageid_postid;
                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                    $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                 
                     $this->_insert_usage_log($module_id=204,$request=1);                        
                 }
                //************************************************//
            }      

            sleep(rand ( 1 , 6 ));
        }

            
    }



    public function offer_post_report($id=0)
    {
        if($id==0) exit();

        $xdata_campaign_view=$this->basic->get_data("facebook_rx_offer_campaign_view",array('where'=>array("id"=>$id,"user_id"=>$this->user_id)));
        $facebook_rx_offer_campaign_id=isset($xdata_campaign_view[0]["campaign_id"])?$xdata_campaign_view[0]["campaign_id"]:0;
        $xdata_campaign=$this->basic->get_data("facebook_rx_offer_campaign",array('where'=>array("id"=>$facebook_rx_offer_campaign_id,"user_id"=>$this->user_id)));
        if(!isset($xdata_campaign_view[0]) || !isset($xdata_campaign[0])) 
        {
            echo "Offer not found";
            exit();
        }
        $upload_id_array=array();
        $upload_id_array=explode(',', $xdata_campaign[0]['upload_ids']);
        $xupload_data=$this->basic->get_data("facebook_rx_offer_upload",array('where_in'=>array('id'=>$upload_id_array)));

        $data['body'] = 'offer_post/report';
        $data['page_title'] = $this->lang->line('Offer Post Report');
       
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),$select='',$join='',$limit='',$start=NULL,$order_by='page_name asc');
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),$select='',$join='',$limit='',$start=NULL,$order_by='group_name asc');

        $data['xdata_campaign_view']=$xdata_campaign_view[0];        
        $data['xdata_campaign']=$xdata_campaign[0];        
        $data['xupload_data']=$xupload_data;        

        $this->_viewcontroller($data);
    }


    public function _get_enum_values_barcode_type(){
        $stream_type_values=$this->basic->get_enum_values($table="facebook_rx_offer_campaign", $column="barcode_type");
        foreach($stream_type_values as $row){
            $return_array[trim($row)]=ucwords(str_replace('_',' ', trim($row)));
        } 
        return $return_array;
    }

    public function _get_enum_values_post_type(){
        $stream_type_values=$this->basic->get_enum_values($table="facebook_rx_offer_campaign", $column="post_type");
        foreach($stream_type_values as $row){
            $return_array[trim($row)]=ucwords(str_replace('_',' ', trim($row)));
        } 
        return $return_array;
    }


    public function _get_enum_values_type_offer(){
        $stream_type_values=$this->basic->get_enum_values($table="facebook_rx_offer_campaign", $column="type_offer");
        foreach($stream_type_values as $row){
            if($row=="bogo")
            $return_array[trim($row)]="Bogo (Buy 1 Get 1 Free)";
            else $return_array[trim($row)]=ucwords(str_replace('_',' ', trim($row)));
        } 
        return $return_array;
    }

    public function _get_enum_values_location_type(){
        $stream_type_values=$this->basic->get_enum_values($table="facebook_rx_offer_campaign", $column="location_type");
        foreach($stream_type_values as $row){
            $return_array[trim($row)]=ucwords(str_replace('_',' ', trim($row)));
        } 
        return $return_array;
    }

    public function _get_currency(){
        $stream_type_values=$this->basic->get_data($table="facebook_rx_offer_currency");
        foreach($stream_type_values as $row){
            $return_array[trim($row['currency'])]=trim($row['currency']).' - '.trim($row['full_name']);
        } 
        return $return_array;
    }


    public function offer_post_meta_info_grabber()
    {
        if($_POST)
        {
            $link= $this->input->post("link");
            $this->load->library("fb_rx_login");
            $response=$this->fb_rx_login->get_meta_tag_fb($link);
            echo json_encode($response);
        }
    } 


    public function offer_post_upload_video()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/offer_post";
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
            echo json_encode($filename);
        }
    }

    public function offer_post_upload_video_thumb()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/offer_post";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="videothumb_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

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



    public function offer_post_upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/offer_post";
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


    public function offer_post_upload_link_preview()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/offer_post";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="imagethumb_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            echo json_encode($filename);
        }
    }

    public function offer_post_delete_uploaded_file() // deletes the uploaded video to upload another one
    {
        if(!$_POST) exit();

        $output_dir = FCPATH."upload_caster/offer_post/";
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


    public function offer_post_youtube_video_grabber()
    {
        if(!$_POST) exit();
        $this->load->library("fb_rx_login");
        $video_url = $this->input->post("link");

        if($video_url!="")
        {
            if(strpos($video_url, 'youtube.com') !== false) 
            {
                parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
                $youtube_video_id = isset($my_array_of_vars['v']) ? $my_array_of_vars['v'] : ""; 
                
                if($youtube_video_id!="")
                {
                    echo $video_url = $this->fb_rx_login->get_youtube_video_url($youtube_video_id);
                    exit();                    
                }
            }
            else
            {
                echo $video_url;
                exit();
            }
        }
        else echo "";

    }



    public function offer_post_expires_at_formatter()
    {
        if(!$_POST) exit();
        $expiration_time=$this->input->post("expiration_time");
        $expiry_time_zone=$this->input->post("expiry_time_zone");

        if($expiration_time=="") 
        {
            echo "Expires MMM DD, YYYY";
            exit();
        }

        if($expiry_time_zone!="") date_default_timezone_set($expiry_time_zone);            
        echo $formetted = "Expires ".date("M j, Y",strtotime($expiration_time));
    }



    public function delete_post_offer()
    {
        if(!$_POST) exit();
        $facebook_rx_offer_campaign_view_id=$this->input->post("id");

        $facebook_rx_offer_campaign_view_data=$this->basic->get_data("facebook_rx_offer_campaign_view",array("where"=>array("id"=>$facebook_rx_offer_campaign_view_id)));
        if(!isset($facebook_rx_offer_campaign_view_data[0])) 
        {
            echo "0";
            exit();
        }

        $facebook_rx_offer_campaign_id=isset($facebook_rx_offer_campaign_view_data[0]['campaign_id']) ? $facebook_rx_offer_campaign_view_data[0]['campaign_id'] : 0;
        $posting_status=isset($facebook_rx_offer_campaign_view_data[0]['posting_status']) ? $facebook_rx_offer_campaign_view_data[0]['posting_status'] : '0';
        $post_type=isset($facebook_rx_offer_campaign_view_data[0]['post_type']) ? $facebook_rx_offer_campaign_view_data[0]['post_type'] : 'image_submit';

        if($posting_status==='1') 
        {
            echo "0";
            exit();
        }


        $facebook_rx_offer_campaign_data=$this->basic->get_data("facebook_rx_offer_campaign",array("where"=>array("id"=>$facebook_rx_offer_campaign_id)));
        if(!isset($facebook_rx_offer_campaign_data[0])) 
        {
            echo "0";
            exit();
        }

        $upload_ids=isset($facebook_rx_offer_campaign_data[0]["upload_ids"]) ?$facebook_rx_offer_campaign_data[0]["upload_ids"] : "";
        $upload_id_array=explode(',', $upload_ids);

        if(count($facebook_rx_offer_campaign_data)>1) // has reposted offer, so we can not delete from facebook_rx_offer_campaign, only delete from facebook_rx_offer_campaign_data
        {
            $this->basic->delete_data("facebook_rx_offer_campaign_view",array("id"=>$facebook_rx_offer_campaign_view_id,"user_id"=>$this->user_id));
        }
        else
        {
            $this->basic->delete_data("facebook_rx_offer_campaign_view",array("id"=>$facebook_rx_offer_campaign_view_id,"user_id"=>$this->user_id)); // deleting offer view
            $this->basic->delete_data("facebook_rx_offer_campaign",array("id"=>$facebook_rx_offer_campaign_id,"user_id"=>$this->user_id)); // dleting offer details

            $this->db->where_in('id', $upload_id_array);
            $this->db->delete("facebook_rx_offer_upload"); // deleting files from database
        }

        if($posting_status==='0')  
        {
            // if the offer was not posted and now deleted use should get back usage count
            if($post_type=="carousel_submit")
            $this->_delete_usage_log($module_id=221,$usage_count=1); 
            else $this->_delete_usage_log($module_id=221,$usage_count=1); 
        }

        echo "1";
    }

    public function delete_post()
    {
      if(!$_POST) exit();
      $id=$this->input->post("id");

      $post_info = $this->basic->get_data('facebook_rx_cta_post',array('where'=>array('id'=>$id)));
      if($post_info[0]['posting_status'] != '2')
      {
          //******************************//
          // delete data to useges log table
          $this->_delete_usage_log($module_id=220,$request=1);   
          //******************************//
      }

      // if($this->basic->delete_data("facebook_rx_cta_post",array("id"=>$id,"user_id"=>$this->user_id)))
      // echo "1";
      // else echo "0";

      if($this->basic->delete_data("facebook_rx_cta_post",array("id"=>$id,"user_id"=>$this->user_id)))
      {
          $this->basic->delete_data("facebook_rx_cta_post",array("parent_campaign_id"=>$id,"user_id"=>$this->user_id));
          echo "1";
      }
      else echo "0";
    }



    public function carousel_slider_post()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(222,$this->module_access)) exit();
        $data['page_title'] = $this->lang->line("Carousel/Slider Poster");

        $data['body'] = 'carousel_slider_post/slider_post_list';
        $this->_viewcontroller($data);
    }


    public function carousel_slider_poster()
    {

        $data['body'] = 'carousel_slider_post/video_slider_poster';
        $data["time_interval"] = $this->get_periodic_time();
        $data['page_title'] = $this->lang->line('Video/Carousel Poster');
        $data["time_zone"]= $this->_time_zone_list();
       
        $user_infos = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        if ( count( $user_infos ) == 0 ) 
            return redirect( base_url( 'facebook_rx_account_import/index' ), 'location' );

        $data["fb_user_info"] = $user_infos;
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));

        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));    
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));

        $this->_viewcontroller($data);
    }




    public function carousel_slider_post_list_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $campaign_name = trim($this->input->post("campaign_name", true));
        $page_or_group_or_user_name = trim($this->input->post("page_or_group_or_user_name", true));
        $post_type = trim($this->input->post("post_type", true));

        $scheduled_from = trim($this->input->post('scheduled_from', true));        
        if($scheduled_from) $scheduled_from = date('Y-m-d', strtotime($scheduled_from));

        $scheduled_to = trim($this->input->post('scheduled_to', true));
        if($scheduled_to) $scheduled_to = date('Y-m-d', strtotime($scheduled_to));


        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            $this->session->set_userdata('facebook_rx_slider_poster_campaign_name', $campaign_name);
            $this->session->set_userdata('facebook_rx_slider_poster_page_or_group_or_user_name', $page_or_group_or_user_name);
            $this->session->set_userdata('facebook_rx_slider_poster_scheduled_from', $scheduled_from);
            $this->session->set_userdata('facebook_rx_slider_poster_scheduled_to', $scheduled_to);
            $this->session->set_userdata('facebook_rx_slider_poster_post_type', $post_type);
        }

        $search_campaign_name  = $this->session->userdata('facebook_rx_slider_poster_campaign_name');
        $search_page_or_group_or_user_name  = $this->session->userdata('facebook_rx_slider_poster_page_or_group_or_user_name');
        $search_scheduled_from = $this->session->userdata('facebook_rx_slider_poster_scheduled_from');
        $search_scheduled_to = $this->session->userdata('facebook_rx_slider_poster_scheduled_to');
        $search_post_type= $this->session->userdata('facebook_rx_slider_poster_post_type');

        $where_simple=array();
        
        if ($search_campaign_name) $where_simple['campaign_name like ']    = "%".$search_campaign_name."%";
        if ($search_page_or_group_or_user_name) $where_simple['page_or_group_or_user_name like ']    = "%".$search_page_or_group_or_user_name."%";
        if ($search_post_type) $where_simple['post_type']  = $search_post_type;
        if ($search_scheduled_from) 
        {
            if ($search_scheduled_from != '1970-01-01') 
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') >="]= $search_scheduled_from;
            
        }
        if ($search_scheduled_to) 
        {
            if ($search_scheduled_to != '1970-01-01') 
            $where_simple["Date_Format(schedule_time,'%Y-%m-%d') <="]=$search_scheduled_to;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_user_info_id'] = $this->session->userdata("facebook_rx_fb_user_info");
        $this->db->where("(is_child='0' or posting_status='2')");
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "facebook_rx_slider_post";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $this->db->where("(is_child='0' or posting_status='2')");
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++) 
        {
            $posting_status = $info[$i]['posting_status'];
            $full_complete =  $info[$i]['full_complete'];
            $schedule_type = $info[$i]['schedule_type'];
            $parent_id = $info[$i]['parent_campaign_id'];

            // $table2 = "facebook_rx_slider_post";
            // $where5656  = array('where'=>array('parent_campaign_id'=>$info[$i]['id']));

            // $allschedulepost_check = $this->basic->get_data($table2, $where5656);
            // $is_all_posted = 1;  

            // foreach ($allschedulepost_check as $key => $value12) {
                    
            //     if ($value12['posting_status'] != 2)
            //         $is_all_posted = 0;
            // }
            $allschedulepost_check = $this->basic->get_data("facebook_rx_slider_post", array('where'=>array('parent_campaign_id'=>$info[$i]['id'])));
            
            if(count($allschedulepost_check)>0)
            {
                $completed_child_count=0;
                foreach ($allschedulepost_check as $key => $value12) 
                {                        
                    if ($value12['posting_status'] == '2' ) $completed_child_count++;
                }
                if(count($allschedulepost_check) == $completed_child_count) $is_all_posted='1';
                else $is_all_posted='0';
            }
            else
            {
                if($posting_status=='2') $is_all_posted='1';
                else $is_all_posted='0';
            }
            if(($posting_status=='0' || $posting_status == '2')  && ($schedule_type == 'later' && $parent_id == '0' && $is_all_posted==0))
                $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-check-circle gray"></i> '.$this->lang->line("not all completed").'</span>';
            else if( $posting_status == '2') $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("Completed").'</span>';
            else if( $posting_status == '1') $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-spinner orange"></i> '.$this->lang->line("Processing").'</span>';
            else $info[$i]['status'] = '<span class="label label-light"><i class="fa fa-remove red"></i> '.$this->lang->line("Pending").'</span>';

            $post_type = $info[$i]['post_type'];
            $post_type = ucfirst(str_replace("_post","",$post_type));
            $info[$i]['post_type'] =  $post_type;

            $publisher = ucfirst($info[$i]['page_or_group_or_user'])." : ".$info[$i]['page_or_group_or_user_name'];
            $info[$i]['publisher'] =  $publisher;


            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
            $scheduled_at = date("M j, y H:i",strtotime($info[$i]['schedule_time']));
            else $scheduled_at = '<i class="fa fa-remove red" title="'.$this->lang->line("Instantly posted").'"></i>';
            $info[$i]['scheduled_at'] =  $scheduled_at;

            // if(strlen($info[$i]["message"])>=60) $info[$i]["message_formatted"] = substr($info[$i]["message"], 0, 60)."...";
            // else $info[$i]["message_formatted"] = $info[$i]["message"];
            
            if($posting_status=='2')
            $post_url = "<a class='btn btn-outline-info' title='".$this->lang->line("Visit")."' target='_BLANK' href='".$info[$i]['post_url']."'><i class='fa fa-hand-o-right'></i></a>";
            else $post_url = "<a class='btn btn-outline-info border_gray gray' title='".$this->lang->line("This post is not published yet.")."'><i class='fa fa-hand-o-right'></i></a>";

            // $table2 = "facebook_rx_slider_post";
            // $where5656  = array('where'=>array('parent_campaign_id'=>$info[$i]['id']));

            // $allschedulepost_check = $this->basic->get_data($table2, $where5656);
            // $is_all_posted = 1;  

            // foreach ($allschedulepost_check as $key => $value12) {
                    
            //     if ($value12['posting_status'] != 2)
            //         $is_all_posted = 0;
            // }

            if(($posting_status=='0' || $posting_status == '2')  && ($schedule_type == 'later' && $parent_id == '0' && $is_all_posted==0))
            $editUrlVideoPost ="<a class='btn btn-outline-warning' title='".$this->lang->line("Edit")."' href='".base_url()."ultrapost/edit_carousel_slider/".$info[$i]['id']."'><i class='fa fa-edit'></i></a>";  
            else $editUrlVideoPost ="<a class='btn btn-outline-warning border_gray gray' title='".$this->lang->line("Only pending campaigns are editable")."'><i class='fa fa-edit'></i></a>";  

            $info[$i]['delete'] =  "<a  title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete btn btn-outline-danger'><i class='fa fa-trash'></i></a>"; 
            if($schedule_type == 'later' && $parent_id == '0') {
                $deleteUrl =  "<a title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete_p btn btn-outline-danger'><i class='fa fa-trash'></i></a>";
            }
            else{
                $deleteUrl =  "<a title='".$this->lang->line("Delete")."' id='".$info[$i]['id']."' class='delete btn btn-outline-danger'><i class='fa fa-trash'></i></a>";
            } 


            if ($schedule_type == 'later' && $parent_id == '0') {

                 $report_url = "<button class='btn btn-outline-info view_report' table_id='".$info[$i]['id']."' title='".$this->lang->line("report")."'><i class='fa fa-eye'></i></button>";  
            }
            else 
            {
                $report_url ="<a class='btn btn-outline-info border_gray gray' title='".$this->lang->line("Only parent campaign has shown  report")."'><i class='fa fa-eye'></i></a>";
            }

            if($schedule_type == 'later' && $parent_id == '0')
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle blue"></i> '.$this->lang->line("main campaign").'</span>';
            else if($schedule_type == 'now')
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle red"></i> '.$this->lang->line("single campaign").'</span>';
            else
                $info[$i]['campaign_type'] = '<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("sub campaign").'</span>';

            // if($post_type=="Slider" && $posting_status=='2')
            // $info[$i]['embed_code'] =  "<atitle='".$this->lang->line("Embed Code")."' id='".$info[$i]['id']."' class='embed_code btn btn-outline-primary'><i class='fa fa-code'></i></a>"; 
            // else $info[$i]['embed_code'] =  "<a title='".$this->lang->line("Embed code is only available for published video posts.")."' class=' btn btn-outline-primary border_gray gray'><i class='fa fa-code'></i></a>"; 

            if($post_type=="Slider" && $posting_status=='2')
            $embedUrl =  "<a title='".$this->lang->line("Embed Code")."' id='".$info[$i]['id']."' class='embed_code btn btn-outline-primary'><i class='fa fa-code'></i></a>";
            else
            $embedUrl =  "<a title='".$this->lang->line("Embed code is only available for published video posts.")."' class='btn btn-outline-primary border_gray gray'><i class='fa fa-code'></i></a>";
            
            $info[$i]['actions']=$post_url."&nbsp;".$editUrlVideoPost."&nbsp;".$deleteUrl."&nbsp;".$embedUrl."&nbsp;".$report_url;

            //$info[$i]['actions'] = $post_url."&nbsp;".$editUrlAutoPost."&nbsp;".$deleteUrl."&nbsp;".$embedUrl."&nbsp;".$report_url;

        }

        echo convert_to_grid_data($info, $total_result);
    }


    public function ajax_carousel_slide_report()
    {

      $table_id = $this->input->post('table_id');
      $text_info = $this->basic->get_data('facebook_rx_slider_post',array('where'=>array('parent_campaign_id'=>$table_id),'or_where'=>array('id'=>$table_id)));


      if(isset($text_info[0]['id']) && $text_info[0]['id'] != '')
      {
          $str = '<script>
                  $j(document).ready(function() {
                      $("#campaign_report").DataTable();
                  }); 
               </script>
               <div class="table-responsive">
               <table id="campaign_report" class="table table-bordered">
                   <thead>
                       <tr>
                           <th class="text-center">'.$this->lang->line("posting page or group").'</th>
                           <th class="text-center">'.$this->lang->line("posting status").'</th>
                           <th class="text-center">'.$this->lang->line("post id").'</th>
                           <th class="text-center">'.$this->lang->line("schedule time").'</th>
                           <th class="text-center">'.$this->lang->line("post type").'</th>
                           <th class="text-center">'.$this->lang->line("Error").'</th>
                           
                       </tr>
                   </thead>
                   <tbody>';
                       
                   
          // $info = json_decode($reply_info[0]['auto_reply_done_info'],true);
      

          foreach($text_info as $value)
          {
             
             
              $posting_status = isset($value['posting_status']) ? $value['posting_status']:"";
              $post_type = isset($value['post_type']) ? $value['post_type']:"";
             
              if($posting_status=='2')
                $posting_status='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("completed").'</span>';
              if($posting_status == '0')
                $posting_status='<span class="label label-light"><i class="fa fa-check-circle red"></i> '.$this->lang->line("pending").'</span>';
            if($posting_status == '1')
              $posting_status='<span class="label label-light"><i class="fa fa-check-circle orange"></i> '.$this->lang->line("processing").'</span>';
              if($post_type == 'carousel_post')
                $post_type = '<span class="label label-light"><i class="fa fa-check-circle blue2"></i> '.$this->lang->line("carousel").'</span>';             
              if($post_type == 'slider_post')
                 $post_type = '<span class="label label-light"><i class="fa fa-check-circle gray"></i> '.$this->lang->line("slider").'</span>';


              $str .= '<tr>
                          <td class="text-center">'.$value['page_or_group_or_user'].' : '.$value['page_or_group_or_user_name'].'</td>
                          <td class="text-center">'.$posting_status.'</td>
                          <td class="text-center"><a target="_BLANK" href="http://facebook.com/'.$value['post_id'].'" class="product-title">'.$value['post_id'].'</a></td>
                          <td class="text-center">'.$value['schedule_time'].'</td>
                          <td class="text-center">'.$post_type.'</td>
                          <td class="text-center">'.$value['error_mesage'].'</td>
                          
                      </tr>';
          }

          $str .= '</tbody>
               </table></div>';
      }
      else
      {
          $str = "<div class='alert alert-danger'>{$this->lang->line("no data to show")}</div>";
      }

      echo $str;    
    }


    public function carousel_slider_add_post_action()
    {       
        $this->load->library("fb_rx_login");

         // ********************** slider = carousel | video = slider **********************
         if($_POST)
         {
             $post=$_POST;
             foreach ($post as $key => $value) 
             {
                 $$key=$value;
             }
         }

         $message = "";

         if($content_type == 'slider_submit')
         {
             $slider_post_content = array();      

             for($i=1;$i<=$content_counter;$i++)
             {
                 $temp_name = 'post_title_'.$i;
                 $temp_title = trim($this->input->post($temp_name));     

                 $temp_link = 'post_link_'.$i;
                 $temp_post_link = trim($this->input->post($temp_link));               
                 
                 $temp_desc = 'post_description_'.$i;
                 $temp_post_desc = trim($this->input->post($temp_desc));                
                 
                 $temp_image_link = 'post_image_link_'.$i;
                 $temp_post_image_link = trim($this->input->post($temp_image_link));                
                 
                 if($temp_post_image_link != '')
                 {
                     $slider_post_content[$i-1]['link'] = $temp_post_link;
                     $slider_post_content[$i-1]['name'] = $temp_title;
                     $slider_post_content[$i-1]['picture'] = $temp_post_image_link;
                     $slider_post_content[$i-1]['description'] = $temp_post_desc;
                 }
             }

             $data['carousel_content'] = json_encode($slider_post_content);
             $data['message'] = $slider_message;
             $data['post_type'] = 'carousel_post';
             $data['carousel_link'] = $slider_link;
             $message = $slider_message;

         } // end of if content type
         else
         {
             $video_post_images = array();

             for($i=1;$i<=$video_content_counter;$i++)
             {
                 $temp_image_link = 'video_image_link_'.$i;
                 $temp_video_image_link = trim($this->input->post($temp_image_link));                
                 
                 if($temp_video_image_link != '')
                 {
                     array_push($video_post_images, $temp_video_image_link);
                 }
             }

             $data['message'] = $video_message;
             $data['post_type'] = 'slider_post';
             $data['slider_images'] = json_encode($video_post_images);
             $data['slider_image_duration'] = $video_image_duration*1000;
             $data['slider_transition_duration'] = $video_image_transition_duration*1000;
             $message = $video_message;
         } //end fo else 
         $data['schedule_type'] = $schedule_type;
         $data['campaign_name'] = $campaign_name;
         $times = 0;
         $times = $repeat_times;
         $interval= $time_interval;
         $request_count = count($post_to_pages);
         $data['repeat_times'] = $times;
         $data['time_interval'] = $interval;
         if($schedule_type=="now")
         {
             $data["posting_status"] ='2';
             $data["full_complete"]  ='1';
         }
         else
         {
             $data["posting_status"] ='0';
         }


         if(!isset($post_to_pages) || !is_array($post_to_pages)) $post_to_pages=array();
         $page_ids_string = implode(',', $post_to_pages);
         $data["page_ids"] = $page_ids_string;
         if(!empty($post_to_pages))
         {
             $get_fb_userinfo_id = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$post_to_pages[0],"user_id"=>$this->user_id)));
             $data['facebook_rx_fb_user_info_id'] = $get_fb_userinfo_id[0]['facebook_rx_fb_user_info_id'];
         }

         //************************************************//
         $status=$this->_check_usage($module_id=222,$request=count($post_to_pages));
        
         if($status=="2") 
         {
             $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
             $return_val=array("status"=>"2","message"=>$error_msg);
             echo json_encode($return_val);
             exit();
         }

         if($status=="3") 
         {
             $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
             $return_val=array("status"=>"2","message"=>$error_msg);
             echo json_encode($return_val);
             exit();
         }


         //************************************************//

         if(isset($auto_reply_template))
             $data["ultrapost_auto_reply_table_id"] = $auto_reply_template;

         if($schedule_type=="now")
         {
             $data["posting_status"] ='2';
             $data['time_zone'] = '';
             $data['schedule_time'] = "0000-00-00 00:00:00";
             $post_to_profile="No";

             if($post_to_profile!="No" && $content_type != 'slider_submit')
             {
                 $data['post_auto_comment_cron_jon_status'] = "0";
                 $data['post_auto_like_cron_jon_status'] = "0";
                 $data['post_auto_share_cron_jon_status'] = "0";
             }
             else
             {
                 $data['post_auto_comment_cron_jon_status'] = "1";
                 $data['post_auto_like_cron_jon_status'] = "1";
                 $data['post_auto_share_cron_jon_status'] = "1";
             }
         }
         else
         {
             $data["posting_status"] ='0';
             $data['time_zone'] = $time_zone;
             $data['schedule_time'] = $schedule_time;  
             $data['post_auto_comment_cron_jon_status'] = "0";
             $data['post_auto_like_cron_jon_status'] = "0";
             $data['post_auto_share_cron_jon_status'] = "0";        
         }

         $data['user_id'] = $this->user_id;        
         
         

         $insert_data_batch=array();

         $count=0;
         
        
         $user_id_array=array($this->user_id);
         $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info"); // table > facebook_rx_fb_user_info.id
         $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

        
         foreach ($page_info as $key => $value) 
         {
             if(!in_array($value["id"], $post_to_pages)) continue;

             $page_access_token =  isset($value["page_access_token"]) ? $value["page_access_token"] : ""; 
             $fb_page_id =  isset($value["page_id"]) ? $value["page_id"] : "";
             $page_table_id = $value['id'];

             $insert_data_batch[$count]=$data;
             $page_auto_id =  isset($value["id"]) ? $value["id"] : ""; 
             $insert_data_batch[$count]["page_group_user_id"]=$page_auto_id;
             $insert_data_batch[$count]["page_or_group_or_user"]="page";
             $insert_data_batch[$count]["page_or_group_or_user_name"]=isset($value["page_name"]) ? $value["page_name"] : ""; 

             $insert_data_batch[$count]["post_id"] = "";
             $insert_data_batch[$count]["post_url"] = ""; 

             if($schedule_type=="now")
             {
                if($this->is_demo == '1')
                    if($this->user_id == 1)
                        continue;

                 if($content_type == 'slider_submit') //carousel post
                 {
                     try
                     {
                         $response = $this->fb_rx_login->carousel_post($message=$slider_message,$link=$slider_link,$child_attachments=$slider_post_content,$scheduled_publish_time="",$post_access_token=$page_access_token,$page_id=$fb_page_id);                    
                         
                     }
                     catch(Exception $e) 
                     {
                       $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                       $return_val=array("status"=>"0","message"=>$error_msg);
                       echo json_encode($return_val);
                       exit();
                     }
                 }
                 else // slider post
                 {
                     $v_i_duration = $video_image_duration*1000;
                     $v_i_transition = $video_image_transition_duration*1000;
                     try
                     {
                         $response = $this->fb_rx_login->post_image_video($description=$video_message,$image_urls=$video_post_images,$v_i_duration,$v_i_transition,$scheduled_publish_time="",$page_access_token,$fb_page_id);
                         
                        
                     }
                     catch(Exception $e) 
                     {
                       $error_msg = "<i class='fa fa-remove'></i> ".$e->getMessage();
                       $return_val=array("status"=>"0","message"=>$error_msg);
                       echo json_encode($return_val);
                       exit();
                     }

                 }   


                 $object_id=$response["id"];
                 $share_access_token = $page_access_token;

                 $insert_data_batch[$count]["last_updated_at"]= date("Y-m-d H:i:s");
                 $insert_data_batch[$count]["post_id"]= $object_id;
                 $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$page_access_token);
                 $insert_data_batch[$count]["post_url"]= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : ""; 

                 $this->basic->insert_data('facebook_rx_slider_post',$insert_data_batch[$count]);
                 $this->_insert_usage_log($module_id=222,$request=count($post_to_pages));



                 if(isset($auto_reply_template) && $auto_reply_template != '0')
                 {

                    //************************************************//
                    $status=$this->_check_usage($module_id=204,$request=1);
                    if($status!="2" && $status!="3") 
                    {


                         $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $auto_reply_template ]]);

                         $auto_reply_table_data = [];

                         foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                             foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                                 
                                 if($auto_key == 'id')
                                     continue;

                                 if($auto_key == 'ultrapost_campaign_name')
                                     $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                                 else
                                     $auto_reply_table_data[$auto_key] = $auto_value;
                             }
                         }



                         $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $value['facebook_rx_fb_user_info_id'];
                         $auto_reply_table_data['page_info_table_id'] = $value['id'];
                         $auto_reply_table_data['page_name'] = $value['page_name'];

                         if($content_type=="slider_post")
                             $auto_reply_table_data['post_id'] = $value['page_id'].'_'.$object_id;
                         else
                             $auto_reply_table_data['post_id'] = $object_id;

                         $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                         $auto_reply_table_data['post_description'] = $message;
                         $auto_reply_table_data['auto_private_reply_status'] = '0';

                         $auto_reply_table_data['auto_private_reply_count'] = 0;
                         $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                         $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                         $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                         $auto_reply_table_data['last_reply_time'] = '';
                         $auto_reply_table_data['error_message'] = '';
                         $auto_reply_table_data['hidden_comment_count'] = 0;
                         $auto_reply_table_data['deleted_comment_count'] = 0;
                         $auto_reply_table_data['auto_comment_reply_count'] = 0;


                         $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

                     
                          $this->_insert_usage_log($module_id=204,$request=1);                        
                      }
                     //************************************************//
                 }
                
             }

             $count++;

         } 


        if($schedule_type=="now") $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i>  ".$this->lang->line("Facebook post has been performed successfully.")."<br><a href='".base_url()."ultrapost/carousel_slider_post'>".$this->lang->line("Click here to see report")."</a>");
        else
        {
             

             $page_info_arr = array();
             $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

             foreach ($page_info as $key => $value) {
                if(!in_array($value["id"], $post_to_pages)) continue;
                $page_info_arr[$value["id"]] = $value['page_name'];
             }


             $parent_id='';
             for ($insert_counter=0; $insert_counter < $request_count; $insert_counter++) { 

                $data['page_or_group_or_user'] = 'page';
                $data['page_group_user_id'] = $post['post_to_pages'][$insert_counter];
                $data['page_or_group_or_user_name'] = $page_info_arr[$data['page_group_user_id']];

                $x=$interval;
                for ($i=0; $i <= $times ; $i++) { 

                     if($i == 0)
                     {
                         $data['schedule_time']= $post['schedule_time'];
                         $this->basic->insert_data('facebook_rx_slider_post',$data);
                         $insert_id = $this->db->insert_id();
                        
                         if($i == 0 && $insert_counter == 0)
                             $parent_id = $insert_id;
                                     
                        

                     }
                     if ($times == 0) {
                           $data['is_child'] = '1';
                           $data['parent_campaign_id'] = $parent_id;
                                         
                     
                     }
                     if($i >= 1)
                     {


                         $data['is_child'] = '1';
                         $current_schedule_time = $post['schedule_time'];
                         $dateTime = new DateTime($current_schedule_time);
                         $p = $i*$x;
                         $dateTime->modify("+{$p} minutes");
                         $data['parent_campaign_id'] = $parent_id;
                         $change_time= $dateTime->format('Y-m-d H:i:s');
                         $data['schedule_time']= $change_time;
                          unset($data['page_ids']);
                          
                         $this->basic->insert_data('facebook_rx_slider_post',$data);
                     }
                }

             }


             if($insert_counter > 0)
             {
                 $number_request = count($insert_data_batch);
                 $this->_insert_usage_log($module_id=222,$request=$number_request);
                 $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line("Facebook post campaign has been created successfully.")."<br><a href='".base_url()."ultrapost/carousel_slider_post'>".$this->lang->line("Click here to see report")."</a>");
             }
             else $return_val=array("status"=>"0","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("something went wrong, please try again."));
        }

        echo json_encode($return_val);


    }



    public function carousel_slider_cron_job($api_key="")
    {
        $this->api_key_check($api_key);
        //$this->load->library("fb_rx_login");
        if($this->is_demo == '1')
        $where['where']=array("posting_status"=>"0","facebook_rx_slider_post.user_id !="=>1);
        else
        $where['where']=array("posting_status"=>"0");
        /***   Taking fist 200 post for auto post ***/
        $post_info= $this->basic->get_data("facebook_rx_slider_post",$where,$select='',$join='',$limit=20, $start=0, $order_by='schedule_time ASC');

        // echo "<pre>";
        // print_r($post_info);
        // die();

        $database = array();

        $campaign_id_array = array();

        foreach($post_info as $info)
        {
            $time_zone = $info['time_zone'];
            $schedule_time = $info['schedule_time']; 

            if($time_zone) date_default_timezone_set($time_zone);            
            $now_time = date("Y-m-d H:i:s");
            
            if(strtotime($now_time) < strtotime($schedule_time)) continue; 

            $campaign_id_array[] = $info['id'];       
        }

        if(empty($campaign_id_array)) exit();

        $this->db->where_in("id",$campaign_id_array);
        $this->db->update("facebook_rx_slider_post",array("posting_status"=>"1"));
       
        $config_id_database = array();
        $this->load->library("fb_rx_login");
        foreach($post_info as $info)
        {    
            $campaign_id= $info['id'];

            if(!in_array($campaign_id, $campaign_id_array)) continue;

            $post_type= $info['post_type'];
            $page_group_user_id= $info["page_group_user_id"];
            $page_or_group_or_user= $info["page_or_group_or_user"];
            $user_id= $info['user_id'];            
            $message =$info['message'];

            $carousel_content=json_decode($info["carousel_content"],true);
            $carousel_link=$info["carousel_link"];
            $slider_images=json_decode($info["slider_images"],true);
            $slider_image_duration=$info["slider_image_duration"];
            $slider_transition_duration=$info["slider_transition_duration"];
           
            $time_zone= $info['time_zone'];
            $schedule_time= $info['schedule_time'];

            // setting fb confid id for library call
            $fb_rx_fb_user_info_id= $info['facebook_rx_fb_user_info_id'];
            if(!isset($config_id_database[$fb_rx_fb_user_info_id]))
            {
                $config_id_database[$fb_rx_fb_user_info_id] = $this->get_fb_rx_config($fb_rx_fb_user_info_id);
            }
            //$this->session->set_userdata("fb_rx_login_database_id", $config_id_database[$fb_rx_fb_user_info_id]);
            $this->fb_rx_login->app_initialize($config_id_database[$fb_rx_fb_user_info_id]);
            // setting fb confid id for library call  
            
            if($page_or_group_or_user=="page")
            {
                $table_name = "facebook_rx_fb_page_info";
                $fb_id_field =  "page_id";
                $access_token_field =  "page_access_token";  
            }
            else if($page_or_group_or_user=="user")
            {
                $table_name = "facebook_rx_fb_user_info";
                $fb_id_field =  "fb_id";
                $access_token_field =  "access_token";               
            }
            else
            {
                $table_name = "facebook_rx_fb_group_info`";
                $fb_id_field =  "group_id";
                $access_token_field =  "group_access_token";

            }

            if(!isset($database[$page_or_group_or_user][$page_group_user_id])) // if not exists in database
            {
                $access_data = $this->basic->get_data($table_name,array("where"=>array("id"=>$page_group_user_id)));
                          
                $use_access_token = isset($access_data["0"][$access_token_field]) ? $access_data["0"][$access_token_field] : "";
                $use_fb_id = isset($access_data["0"][$fb_id_field]) ? $access_data["0"][$fb_id_field] : "";
                
                //inserting new data in database
                $database[$page_or_group_or_user][$page_group_user_id] = array("use_access_token"=>$use_access_token,"use_fb_id"=>$use_fb_id);
            }

            $use_access_token = isset($database[$page_or_group_or_user][$page_group_user_id]["use_access_token"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_access_token"] : "";
            $use_fb_id = isset($database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"]) ? $database[$page_or_group_or_user][$page_group_user_id]["use_fb_id"] : "";

            $response =array();
            $error_msg ="";
            if($post_type == 'carousel_post') //carousel post
            {
                try
                {
                    $response = $this->fb_rx_login->carousel_post($message,$carousel_link,$carousel_content,"",$use_access_token,$use_fb_id);                    
                }
                catch(Exception $e) 
                {
                    $error_msg = $e->getMessage();
                }
            }
            else // slider post
            {
                try
                {
                    $response = $this->fb_rx_login->post_image_video($message,$slider_images,$slider_image_duration,$slider_transition_duration,"",$use_access_token,$use_fb_id);
                }
                catch(Exception $e) 
                {
                  $error_msg = $e->getMessage();
                }
            } 

            $object_id=isset($response["id"]) ? $response["id"] : "";
            
            $temp_data=array();
            try
            {
                $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$use_access_token);
            }
            catch(Exception $e) 
            {
                $error_msg1 = $e->getMessage();
            }
            
            $post_url= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : "";               

            $update_data = array("posting_status"=>'2',"full_complete"=>'1',"post_id"=>$object_id,"post_url"=>$post_url,"error_mesage"=>$error_msg,"last_updated_at"=>date("Y-m-d H:i:s"));

            $this->basic->update_data("facebook_rx_slider_post",array("id"=>$campaign_id),$update_data);



            if($info['ultrapost_auto_reply_table_id'] != 0)
            {

                //************************************************//
                $status=$this->_check_usage($module_id=204,$request=1);
                if($status!="2" && $status!="3") 
                {

                    $auto_reply_table_info = $this->basic->get_data('ultrapost_auto_reply',['where'=>['id' => $info['ultrapost_auto_reply_table_id'] ]]);

                    $facebook_page_info = $this->basic->get_data('facebook_rx_fb_page_info',['where' => ['id' => $info['page_group_user_id']]]);

                    $auto_reply_table_data = [];

                    foreach ($auto_reply_table_info as $single_auto_reply_table_info) {

                        foreach ($single_auto_reply_table_info as $auto_key => $auto_value) {
                            
                            if($auto_key == 'id')
                                continue;

                            if($auto_key == 'ultrapost_campaign_name')
                                $auto_reply_table_data['auto_reply_campaign_name'] = $auto_value;
                            else
                                $auto_reply_table_data[$auto_key] = $auto_value;
                        }
                    }



                    $auto_reply_table_data['facebook_rx_fb_user_info_id'] = $fb_rx_fb_user_info_id;
                    $auto_reply_table_data['page_info_table_id'] = $facebook_page_info[0]['id'];
                    $auto_reply_table_data['page_name'] = $facebook_page_info[0]['page_name'];

                    if($post_type=="slider_post")
                        $auto_reply_table_data['post_id'] = $facebook_page_info[0]['page_id'].'_'.$object_id;
                    else
                        $auto_reply_table_data['post_id'] = $object_id;

                    $auto_reply_table_data['post_created_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['post_description'] = $message;
                    $auto_reply_table_data['auto_private_reply_status'] = '0';

                    $auto_reply_table_data['auto_private_reply_count'] = 0;
                    $auto_reply_table_data['auto_private_reply_done_ids'] = json_encode([]);
                    $auto_reply_table_data['auto_reply_done_info'] = json_encode([]);
                    $auto_reply_table_data['last_updated_at'] = date("Y-m-d h:i:s");
                    $auto_reply_table_data['last_reply_time'] = '';
                    $auto_reply_table_data['error_message'] = '';
                    $auto_reply_table_data['hidden_comment_count'] = 0;
                    $auto_reply_table_data['deleted_comment_count'] = 0;
                    $auto_reply_table_data['auto_comment_reply_count'] = 0;

                    $this->basic->insert_data('facebook_ex_autoreply', $auto_reply_table_data);

               
                     $this->_insert_usage_log($module_id=204,$request=1);                        
                 }
                //************************************************//
            }

            sleep(rand ( 1 , 6 ));


        }

            
    }


    public function edit_carousel_slider($video_post_id)
    {
        $table2 = "facebook_rx_slider_post";
        $where5656  = array('where'=>array('id'=>$video_post_id));

        $allschedulepost_check = $this->basic->get_data($table2,$where5656);

        foreach ($allschedulepost_check as $key => $value12) {
            if ($value12['posting_status'] == '2')
            {
                 $data['is_all_posted'] = 1; 
            }
            else
            {
                $data['is_all_posted'] =0;
            }
          
        }
        $data['body'] = 'carousel_slider_post/edit_video_slider_poster';
        $data['page_title'] = $this->lang->line('Edit Video/Carousel Poster');
        $data["time_zone"]= $this->_time_zone_list();
        $data["time_interval"] = $this->get_periodic_time();
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));
        $data['auto_reply_template'] = $this->basic->get_data('ultrapost_auto_reply',array("where"=>array('user_id'=>$this->user_id)),array('id','ultrapost_campaign_name'));


        $data["all_data"] = $this->basic->get_data("facebook_rx_slider_post",array("where"=>array("id"=>$video_post_id)));
        $this->_viewcontroller($data);
    }



    public function edit_carousel_slider_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            redirect('home/access_forbidden', 'location');
        }

        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$value;
            }
            $facebook_rx_fb_user_info_id= $this->input->post('facebook_rx_fb_user_info_id', true);
        }


        if($content_type == 'slider_submit')
        {
            $slider_post_content = array();      

            for($i=1;$i<=$content_counter;$i++)
            {
                $temp_name = 'post_title_'.$i;
                $temp_title = trim($this->input->post($temp_name));     

                $temp_link = 'post_link_'.$i;
                $temp_post_link = trim($this->input->post($temp_link));               
                
                $temp_desc = 'post_description_'.$i;
                $temp_post_desc = trim($this->input->post($temp_desc));                
                
                $temp_image_link = 'post_image_link_'.$i;
                $temp_post_image_link = trim($this->input->post($temp_image_link));                
                
                if($temp_post_image_link != '')
                {
                    $slider_post_content[$i-1]['link'] = $temp_post_link;
                    $slider_post_content[$i-1]['name'] = $temp_title;
                    $slider_post_content[$i-1]['picture'] = $temp_post_image_link;
                    $slider_post_content[$i-1]['description'] = $temp_post_desc;
                }
            }

            $data['carousel_content'] = json_encode($slider_post_content);
            $data['message'] = $slider_message;
            $data['post_type'] = 'carousel_post';
            $data['carousel_link'] = $slider_link;
        } // end of if content type
        else
        {
            $video_post_images = array();
            for($i=1;$i<=$video_content_counter;$i++)
            {
                $temp_image_link = 'video_image_link_'.$i;
                $temp_video_image_link = trim($this->input->post($temp_image_link));           
                if($temp_video_image_link != '')
                {
                    array_push($video_post_images, $temp_video_image_link);
                }
            }

            $data['message'] = $video_message;
            $data['post_type'] = 'slider_post';
            $data['slider_images'] = json_encode($video_post_images);
            $data['slider_image_duration'] = $video_image_duration*1000;
            $data['slider_transition_duration'] = $video_image_transition_duration*1000;
        } //end fo else 
        $times = 0;
        $times = $repeat_times;
        $data['campaign_name'] = $campaign_name;
        $data['auto_share_post'] = 0;
        $data["auto_share_to_profile"]= "0";
        $data['repeat_times'] = $repeat_times;
        $data['time_interval'] = $time_interval;
        
        $request_count = count($post_to_pages);
        if(!isset($auto_share_this_post_by_pages) || !is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages = array();
        $post_to_pages = array();
        if($this->input->post('post_to_pages', true) !== null)
            $post_to_pages = $this->input->post('post_to_pages', true);
        else
            $post_to_pages   = 0;   

        $data["auto_share_this_post_by_pages"] = json_encode($auto_share_this_post_by_pages);
        $data['auto_like_post'] = 0;
        $data['auto_private_reply'] = 0;
        $data['auto_private_reply_text'] = 0;
        $data['auto_comment'] = 0;
        $data['auto_comment_text'] = 0;
        $data['facebook_rx_fb_user_info_id'] =$facebook_rx_fb_user_info_id;
        if(isset($auto_reply_template))
            $data['ultrapost_auto_reply_table_id'] = $auto_reply_template;
        $page_ids_string = implode(',', $post_to_pages);
        $data["page_ids"] = $page_ids_string;
        $data['time_zone'] = $time_zone;
        $data['schedule_time'] = $schedule_time;
        $data['schedule_type'] = $schedule_type;
        $data['user_id'] = $this->user_id;


        // $this->basic->delete_data('facebook_rx_slider_post',array('id'=>$id));
        // $this->basic->delete_data('facebook_rx_slider_post',array('parent_campaign_id'=>$id));
        //$this->basic->delete_data('facebook_rx_slider_post',array('full_complete'=>'0'));
        $this->basic->delete_data('facebook_rx_slider_post',array('id'=>$id,'user_id'=>$this->user_id));
        $this->basic->delete_data('facebook_rx_slider_post',array('parent_campaign_id'=>$id,'full_complete'=>'0' ,'user_id'=>$this->user_id));
        $account_switching_id = $this->session->userdata("facebook_rx_fb_user_info");
        // $page_counter = 0;
        $user_id_array=array($this->user_id);

            $page_info_arr = array();
            $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$account_switching_id)));

            foreach ($page_info as $key => $value) {
               if(!in_array($value["id"], $post_to_pages)) continue;
               $page_info_arr[$value["id"]] = $value['page_name'];
            }

            $parent_id='';
            for ($insert_counter=0; $insert_counter < $request_count; $insert_counter++) { 

               $data['page_or_group_or_user'] = 'page';
               $data['page_group_user_id'] = $post['post_to_pages'][$insert_counter];
               $data['page_or_group_or_user_name'] = $page_info_arr[$data['page_group_user_id']];

               $x=$time_interval;
               for ($i=0; $i <=$times ; $i++) { 
                   
                    if($i == 0)
                    {
                        $data['schedule_time']= $post['schedule_time'];
                        $this->basic->insert_data('facebook_rx_slider_post',$data);
                        $insert_id = $this->db->insert_id();
                        if($i == 0 && $insert_counter == 0)
                            $parent_id = $insert_id;
                            
                       

                    }
                    if ($times == 0) {
                          $data['is_child'] = '1';
                          $data['parent_campaign_id'] = $parent_id;
                                        
                    
                    }
                    if($i >= 1)
                    {


                        $data['is_child'] = '1';
                        $current_schedule_time = $post['schedule_time'];
                        $dateTime = new DateTime($current_schedule_time);
                        $p = $i*$x;
                        $dateTime->modify("+{$p} minutes");
                        $data['parent_campaign_id'] = $parent_id;
                        $change_time= $dateTime->format('Y-m-d H:i:s');
                        $data['schedule_time']= $change_time;
                        $data['page_ids'];
                         
                        $this->basic->insert_data('facebook_rx_slider_post',$data);
                    }
               }

            }




        
        if($insert_counter > 0)
        $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> ".$this->lang->line('Facebook post information has been updated successfully.')."<br><a href='".base_url()."ultrapost/carousel_slider_post'>".$this->lang->line("Click here to see report")."</a>"); 
        else $return_val=array("status"=>"0","message"=>"<i class='fa fa-remove'></i> ".$this->lang->line("something went wrong, please try again."));
        echo json_encode($return_val);

    }




    public function carousel_slider_delete_post()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");

        $post_info = $this->basic->get_data('facebook_rx_slider_post',array('where'=>array('id'=>$id)));
        if($post_info[0]['posting_status'] != '2')
        {
            //******************************//
            // delete data to useges log table
            $this->_delete_usage_log($module_id=222,$request=1);   
            //******************************//
        }
        if($this->basic->delete_data("facebook_rx_slider_post",array("id"=>$id,"user_id"=>$this->user_id)))
        {
            $this->basic->delete_data("facebook_rx_slider_post",array("parent_campaign_id"=>$id,"user_id"=>$this->user_id));
            echo "1";
        }
        else echo "0";
        // if($this->basic->delete_data("facebook_rx_slider_post",array("id"=>$id,"user_id"=>$this->user_id)))
        // echo "1";
        // else echo "0";
    }


    public function carousel_slider_get_embed_code()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");

        $video_data = $this->basic->get_data("facebook_rx_slider_post",array("where"=>array("id"=>$id,"user_id"=>$this->user_id)));

          $post_url= isset($video_data[0]['post_url']) ? $video_data[0]['post_url']:"";
       
       $embed_code= '<iframe src="https://www.facebook.com/plugins/video.php?href='.$post_url.'&show_text=0&width=600" width="600" height="600" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>';

        $embed_html = "<center><b>".$this->lang->line("Copy this embed code")." : </b><br/><input style='height:40px;width:100%' type='text' value='".$embed_code."'><br/><br/><b>".$this->lang->line("Preview")." :</b> <br/>". $embed_code."</center>";
        echo $embed_html;

    }






    public function carousel_slider_upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload_caster/carousel_slider";
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



    public function carousel_slider_delete_uploaded_file() // deletes the uploaded image to upload another one
    {

        $output_dir = FCPATH."upload_caster/carousel_slider/";
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
       /* for Ultrapost */

    public function template_manager()
    {   
        $data['body'] = 'template_manager';
        $data['page_title'] = $this->lang->line('Template Manager');
        $data['emotion_list'] = $this->get_emotion_list();
  
        $this->_viewcontroller($data);
    }

    public function template_manager_data()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'ultrapost_auto_reply.id'; 
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $campaign_name = trim($this->input->post("template_name", true));
        // $postback = trim($this->input->post("postback", true));
        $is_searched = $this->input->post('is_searched', true);
    
        if($is_searched) 
        {
            $this->session->set_userdata('template_manager_search_ultraposter', $campaign_name);
            //$this->session->set_userdata('template_manager_search_postback', $postback);
        }
        $search_campaign_names  = $this->session->userdata('template_manager_search_ultraposter');
        // $search_postback  = $this->session->userdata('template_manager_search_postback');

        $where_simple=array();
        if ($search_campaign_names) $where_simple['ultrapost_campaign_name like ']    = "%".$search_campaign_names."%";
        // if ($search_postback) $where_simple['postback_id like ']    = "%".$search_postback."%";
        $where_simple['ultrapost_auto_reply.user_id'] = $this->user_id;
        // $where_simple['messenger_bot_postback.is_template'] = '1';
        // $where_simple['ultrapost_auto_reply.reply_type'] = 'auto_reply_text';
        
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "ultrapost_auto_reply";
        // $join = array('messenger_bot_page_info'=>'messenger_bot_postback.page_id=messenger_bot_page_info.id,left');
        $select = array('ultrapost_auto_reply.*','ultrapost_campaign_name');
        // $info = $this->basic->get_data('ultrapost_auto_reply');
        //  echo "<pre>";
        // print_r($info);
        // exit;
        $info = $this->basic->get_data($table, $where, $select,'',$limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        // print_r($this->db->last_query());
        // print_r($info);

        

        
        $total_rows_array = $this->basic->count_row($table, $where, $count="ultrapost_auto_reply.id");
        $total_result = $total_rows_array[0]['total_rows'];
       

        $information = array();
        for($i=0;$i<count($info);$i++)
        {   
            $id = $info[$i]['id'];
            $information[$i]['ultrapost_campaign_name'] = $info[$i]['ultrapost_campaign_name'];
            // $information[$i]['page_name'] = $info[$i]['page_name'];
            // $information[$i]['postback_id'] = $info[$i]['postback_id'];  edit_enable_page_response edit_reply_info
            $information[$i]['action'] = "<a class='text-center edit_reply_info btn btn-outline-warning' title='".$this->lang->line("Edit")."' table_id=".$id." '><i class='fa  fa-edit'></i></a>&nbsp;<a class='text-center delete_reply_info btn btn-outline-danger' title='".$this->lang->line("Delete")."' table_id=".$id." '><i class='fa fa-trash'></i></a>";
        }
        echo convert_to_grid_data($information, $total_result);

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

    public function ajax_autoreply_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$this->input->post($key);
            }
        }
            
        $return = array();
        $facebook_rx_fb_user_info = $this->session->userdata("facebook_rx_fb_user_info");
        
        $nofilter_array['comment_reply'] = trim($nofilter_word_found_text);
        $nofilter_array['private_reply'] = trim($nofilter_word_found_text_private);
        $nofilter_array['image_link'] = trim($nofilter_image_upload_reply);
        $nofilter_array['video_link'] = trim($nofilter_video_upload_reply);
        $no_filter_array = array();
        array_push($no_filter_array, $nofilter_array);
        $nofilter_word_found_text = json_encode($no_filter_array);
        $nofilter_word_found_text = $this->db->escape($nofilter_word_found_text);
        // comment hide and delete section
        $is_delete_offensive = $delete_offensive_comment;
        $offensive_words = trim($delete_offensive_comment_keyword);
        $offensive_words = $this->db->escape($offensive_words);
        $private_message_offensive_words = $this->db->escape($private_message_offensive_words);
        // end of comment hide and delete section
        // $page_name = $this->db->escape($page_name);
        
        $auto_campaign_name = $this->db->escape($auto_campaign_name);
        
        if($message_type == 'generic')
        {
            $generic_message_array['comment_reply'] = trim($generic_message);
            $generic_message_array['private_reply'] = trim($generic_message_private);
            $generic_message_array['image_link'] = trim($generic_image_for_comment_reply);
            $generic_message_array['video_link'] = trim($generic_video_comment_reply);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = '';
            $auto_reply_text = json_encode($generic_array);
            $auto_reply_text = $this->db->escape($auto_reply_text); 
            $sql = "INSERT INTO ultrapost_auto_reply (user_id,ultrapost_campaign_name,reply_type,auto_like_comment,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,comment_reply_enabled,auto_reply_text,nofilter_word_found_text) VALUES ('$this->user_id',$auto_campaign_name,'$message_type','$auto_like_comment','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply','$comment_reply_enabled',$auto_reply_text,$nofilter_word_found_text)
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',ultrapost_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text";
        }
        else
        {
            $auto_reply_text_array = array();
            for($i=1;$i<=20;$i++)
            {
                $filter_word = 'filter_word_'.$i;
                $filter_word_text = $this->input->post($filter_word);
                $filter_message = 'filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                // added 25-04-2017
                $comment_message = 'comment_reply_msg_'.$i;
                $comment_message_text = $this->input->post($comment_message);
                $image_field_name = 'filter_image_upload_reply_'.$i;
                $image_link = $this->input->post($image_field_name);
                $video_field_name = 'filter_video_upload_reply_'.$i;
                $video_link = $this->input->post($video_field_name);
                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    // $auto_reply_text_array[$filter_word_text] = $filter_message_text;
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['image_link'] = trim($image_link);
                    $data['video_link'] = trim($video_link);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = '';
            $auto_reply_text = json_encode($auto_reply_text_array);
            $auto_reply_text = $this->db->escape($auto_reply_text);
            $sql = "INSERT INTO ultrapost_auto_reply (user_id,ultrapost_campaign_name,reply_type,auto_like_comment,hide_comment_after_comment_reply,is_delete_offensive,offensive_words,private_message_offensive_words,multiple_reply,comment_reply_enabled,auto_reply_text,nofilter_word_found_text) VALUES ('$this->user_id',$auto_campaign_name,'$message_type',auto_like_comment='$auto_like_comment','$hide_comment_after_comment_reply','$is_delete_offensive',$offensive_words,$private_message_offensive_words,'$multiple_reply','$comment_reply_enabled',$auto_reply_text,$nofilter_word_found_text)
            ON DUPLICATE KEY UPDATE auto_reply_text=$auto_reply_text,reply_type='$message_type',hide_comment_after_comment_reply='$hide_comment_after_comment_reply',is_delete_offensive='$is_delete_offensive',offensive_words=$offensive_words,private_message_offensive_words=$private_message_offensive_words,auto_like_comment='$auto_like_comment',multiple_reply='$multiple_reply',comment_reply_enabled='$comment_reply_enabled',ultrapost_campaign_name=$auto_campaign_name,nofilter_word_found_text=$nofilter_word_found_text";
        }
        
        if($this->db->query($sql))
        {
            //insert data to useges log table
            $this->_insert_usage_log($module_id=204,$request=1);
            $return['status'] = 1;
            $return['message'] = "<div class='alert alert-success'>".$this->lang->line("your given information has been updated successfully.")."</div>";
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = "<div class='alert alert-danger'>".$this->lang->line("something went wrong, please try again.")."</div>";
        }
        echo json_encode($return);
    }
    public function ajax_update_autoreply_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$this->input->post($key);
            }
        }
        $return = array();
        if($edit_message_type == 'generic')
        {
            // $auto_reply_text = $edit_generic_message;
            $generic_message_array['comment_reply'] = trim($edit_generic_message);
            $generic_message_array['private_reply'] = trim($edit_generic_message_private);
            $generic_message_array['image_link'] = trim($edit_generic_image_for_comment_reply);
            $generic_message_array['video_link'] = trim($edit_generic_video_comment_reply);
            $generic_array = array();
            array_push($generic_array, $generic_message_array);
            $auto_reply_text = json_encode($generic_array);
            // $auto_reply_text = $this->db->escape($generic_message_text);
        }
        else
        {
            $auto_reply_text_array = array();
            for($i=1;$i<=20;$i++)
            {
                $filter_word = 'edit_filter_word_'.$i;
                $filter_word_text = $this->input->post($filter_word);
                $filter_message = 'edit_filter_message_'.$i;
                $filter_message_text = $this->input->post($filter_message);
                // added 25-04-2017
                $comment_message = 'edit_comment_reply_msg_'.$i;
                $comment_message_text = $this->input->post($comment_message);
                $image_field_name = 'edit_filter_image_upload_reply_'.$i;
                $image_link = $this->input->post($image_field_name);
                $video_field_name = 'edit_filter_video_upload_reply_'.$i;
                $video_link = $this->input->post($video_field_name);
                if($filter_word_text != '' && ($filter_message_text != '' || $comment_message_text != ''))
                {
                    // $auto_reply_text_array[$filter_word_text] = $this->db->escape($filter_message_text);
                    $data['filter_word'] = trim($filter_word_text);
                    $data['reply_text'] = trim($filter_message_text);
                    $data['comment_reply_text'] = trim($comment_message_text);
                    $data['image_link'] = trim($image_link);
                    $data['video_link'] = trim($video_link);
                    array_push($auto_reply_text_array, $data);
                }
            }
            $auto_reply_text = json_encode($auto_reply_text_array);
        }
        $no_filter_array['comment_reply'] = trim($edit_nofilter_word_found_text);
        $no_filter_array['private_reply'] = trim($edit_nofilter_word_found_text_private);
        $no_filter_array['image_link'] = trim($edit_nofilter_image_upload_reply);
        $no_filter_array['video_link'] = trim($edit_nofilter_video_upload_reply);
        $nofilter_array = array();
        array_push($nofilter_array, $no_filter_array);
        $data = array(
            'auto_reply_text' => $auto_reply_text,
            'reply_type' => $edit_message_type,
            'ultrapost_campaign_name' => $edit_auto_campaign_name,
            'nofilter_word_found_text' => json_encode($nofilter_array),
            'comment_reply_enabled' => $edit_comment_reply_enabled,
            'multiple_reply' => $edit_multiple_reply,
            // comment hide and delete section
            'is_delete_offensive' => $edit_delete_offensive_comment,
            'offensive_words' => trim($edit_delete_offensive_comment_keyword),
            'private_message_offensive_words' => trim($edit_private_message_offensive_words),
            'hide_comment_after_comment_reply' => $edit_hide_comment_after_comment_reply,
            // comment hide and delete section
            'auto_like_comment' => $edit_auto_like_comment
            );
        $where = array(
            'user_id' => $this->user_id,
            'id' => $table_id
            );
        if($this->basic->update_data('ultrapost_auto_reply',$where,$data))
        {
            $return['status'] = 1;
            $return['message'] = "<div class='alert alert-success'>{$this->lang->line("your given information has been updated successfully.")}</div>";
        }
        else
        {
            $return['status'] = 0;
            $return['message'] = "<div class='alert alert-danger'>{$this->lang->line("something went wrong, please try again.")}</div>";
        }
        echo json_encode($return);
    }


    public function ajax_delete_reply_info()
    {
        
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$this->input->post($key);
            }
        }

        $result = $this->basic->delete_data('ultrapost_auto_reply', ['id' => $table_id]);

        if ($result) 
            echo "successfull";
        else
            echo "unseccessfull";

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
    public function ajax_edit_reply_info()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id');
        $info = $this->basic->get_data('ultrapost_auto_reply',array('where'=>array('id'=>$table_id)));
        
        if($info[0]['reply_type'] == 'generic'){
            $reply_content = json_decode($info[0]['auto_reply_text']);
            if(!is_array($reply_content))
            {
                $reply_content[0]['comment_reply'] = "";
                $reply_content[0]['private_reply'] = $info[0]['auto_reply_text'];
                $reply_content[0]['image_link'] = "";
                $reply_content[0]['video_link'] = "";
            }
        }
        else
            $reply_content = json_decode($info[0]['auto_reply_text']);
        $nofilter_word_text = json_decode($info[0]['nofilter_word_found_text']);
        if(!is_array($nofilter_word_text))
        {
            $nofilter_word_text[0]['comment_reply'] = '';
            $nofilter_word_text[0]['image_link'] = '';
            $nofilter_word_text[0]['video_link'] = '';
            $nofilter_word_text[0]['private_reply'] = $info[0]['nofilter_word_found_text'];
        }
        $respnse['reply_type'] = $info[0]['reply_type'];
        $respnse['comment_reply_enabled'] = $info[0]['comment_reply_enabled'];
        $respnse['multiple_reply'] = $info[0]['multiple_reply'];
        $respnse['auto_like_comment'] = $info[0]['auto_like_comment'];
        $respnse['auto_reply_text'] = $reply_content;
        // $respnse['edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $respnse['table_id'] = $info[0]['id'];
        $respnse['edit_auto_campaign_name'] = $info[0]['ultrapost_campaign_name'];
        $respnse['edit_nofilter_word_found_text'] = $nofilter_word_text;
        // comment hide and delete section
        $respnse['is_delete_offensive'] = $info[0]['is_delete_offensive'];
        $respnse['offensive_words'] = $info[0]['offensive_words'];
        $respnse['private_message_offensive_words'] = $info[0]['private_message_offensive_words'];
        $respnse['hide_comment_after_comment_reply'] = $info[0]['hide_comment_after_comment_reply'];
        // comment hide and delete section
        echo json_encode($respnse);
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


    public function cron_job_list()
    {
        if($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location');
        
        $data['body'] = "cron_job";
        $data['page_title'] = 'cron job';
        $api_data=$this->basic->get_data("native_api",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));
        $data["api_key"]="";
        if(count($api_data)>0) $data["api_key"]=$api_data[0]["api_key"];
        $this->_viewcontroller($data);
    }


    public function get_periodic_time()
    {

        $all_periodic_time= array(
        
        
        '60' =>'every 1 hours',
        '120'=>'every 2 hours',
        '300'=>'every 5 hours',
        '1440'=>'every 1 days',
        '2880'=>'every 2 days',
        '4320'=>'every 3 days',
        '7200'=>'every 5 days',
        '8640'=>'every 6 days',
        '10080'=>'every 7 days',
        '43200'=>'every 1 months',
        '86400'=>'every 2 months',
        '259200'=>'every 6 months',
       );
        return $all_periodic_time;
    }


}
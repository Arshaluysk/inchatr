<?php

require_once("Home.php"); // including home controller

class Facebook_rx_cta_poster extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(69,$this->module_access))
        redirect('home/login_page', 'location'); 
        $this->user_id=$this->session->userdata('user_id');

        if($this->session->userdata("facebook_rx_fb_user_info")==0)
        redirect('facebook_rx_account_import/index','refresh');
    
        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();        
    }


    public function index()
    {
      $this->cta_post_list();
    }

    public function cta_post_list()
    {
        $data=array("page_title"=>"CTA Post Campaign List", "body" => "facebook_rx/cta_post/cta_post_list");
        $this->_viewcontroller($data);
    }

   
    public function cta_post_list_data()
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
        $where  = array('where'=>$where_simple);
        $join    =  array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=facebook_rx_cta_post.page_group_user_id,left");
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "facebook_rx_cta_post";
        $info = $this->basic->get_data($table, $where, $select=array("facebook_rx_cta_post.*","facebook_rx_fb_page_info.page_name"), $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="facebook_rx_cta_post.id", $join);
        $total_result = $total_rows_array[0]['total_rows'];

        for($i=0;$i<count($info);$i++) 
        {
            $posting_status = $info[$i]['posting_status'];
            if( $posting_status == '2') $info[$i]['status'] = '<span class="label label-success">'.$this->lang->line("completed").'</span>';
            else if( $posting_status == '1') $info[$i]['status'] = '<span class="label label-warning">'.$this->lang->line("processing").'</span>';
            else $info[$i]['status'] = '<span class="label label-danger">'.$this->lang->line("pending").'</span>';

            if($info[$i]['schedule_time'] != "0000-00-00 00:00:00")
            $scheduled_at = date("M j, y H:i",strtotime($info[$i]['schedule_time']));
            else $scheduled_at = '<i class="fa fa-remove red" title="'.$this->lang->line("instantly posted").'"></i>';
            $info[$i]['scheduled_at'] =  $scheduled_at;

            $cta_type= $info[$i]['cta_type'];
            $cta_type = str_replace('_', " ", $cta_type);
            $cta_type = ucwords(strtolower($cta_type));

            if($info[$i]['cta_type']=="LIKE_PAGE" || $info[$i]['cta_type'] =="MESSAGE_PAGE")
            $cta_button = "<a  class='btn btn-default btn-sm' href='#'>".$cta_type."</a>";
            else  $cta_button = "<a  class='btn btn-default btn-sm' target='_BLANK' href='".$info[$i]['cta_value']."'>".$cta_type."</a>";
                    
            $info[$i]['cta_button'] =  $cta_button;

            if(strlen($info[$i]["message"])>=60) $info[$i]["message_formatted"] = substr($info[$i]["message"], 0, 60)."...";
            else $info[$i]["message_formatted"] = $info[$i]["message"];
            
            if($posting_status=='2')
            $post_url = "<a target='_BLANK' href='".$info[$i]['post_url']."'><span class='label label-primary'><i class='fa fa-hand-o-right'></i> {$this->lang->line("visit")}</span></a>";
            else $post_url = '<i class="fa fa-remove red" title="'.$this->lang->line("this post is not published yet.").'"></i>';
            $info[$i]['visit_post'] =  $post_url;    

            $info[$i]['delete'] =  "<a title='Delete this post from our database' id='".$info[$i]['id']."' class='delete btn-sm btn btn-danger'><i class='fa fa-remove'></i> {$this->lang->line("delete")}</a>"; 


        }

        echo convert_to_grid_data($info, $total_result);
    }
  
    public function add_cta_post()
    {
        $data['body'] = 'facebook_rx/cta_post/add_cta_post';
        $data['page_title'] = $this->lang->line('CTA Poster');
        $data["time_zone"]= $this->_time_zone_list();
       
        $data["fb_user_info"]=$this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("user_id"=>$this->user_id,"id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_page_info"]=$this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["fb_group_info"]=$this->basic->get_data("facebook_rx_fb_group_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data["app_info"]=$this->basic->get_data("facebook_rx_config",array("where"=>array("id"=>$this->session->userdata("fb_rx_login_database_id"))));  
        
        $cta_dropdown = array("MESSAGE_PAGE"=>"MESSAGE_PAGE");
        foreach ($cta_dropdown as $key => $value) 
        {
           $value = str_replace('_', " ", $value);
           $value = ucwords(strtolower($value));
           $data["cta_dropdown"][$key] = $value;
        }

        $this->_viewcontroller($data);
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
           if(!is_array($value))
           $insert_data[$key]=$value;
        }
        unset($insert_data["schedule_type"]);

        //************************************************//
        $request_count = count($post_to_pages);
        $status=$this->_check_usage($module_id=69,$request=$request_count);
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

        if($auto_share_to_profile=="No") $insert_data["auto_share_to_profile"]= "0";
        else $insert_data["auto_share_to_profile"]= "1";

        $insert_data["user_id"] = $this->user_id;        
        $insert_data["facebook_rx_fb_user_info_id"] = $this->session->userdata("facebook_rx_fb_user_info");       

        if(!isset($auto_share_this_post_by_pages) || !is_array($auto_share_this_post_by_pages)) $auto_share_this_post_by_pages=array();
        if(!isset($post_to_pages) || !is_array($post_to_pages)) $post_to_pages=array();
        $auto_share_this_post_by_pages_new = array_diff($auto_share_this_post_by_pages,$post_to_pages);        
        $insert_data["auto_share_this_post_by_pages"] = json_encode($auto_share_this_post_by_pages_new);

        $insert_data["auto_private_reply_status"]= "0";
        $insert_data["auto_private_reply_count"]= 0;
        $insert_data["auto_private_reply_done_ids"]= json_encode(array());


        if($schedule_type=="now")
        {
            $insert_data["post_auto_comment_cron_jon_status"] = "1";
            $insert_data["post_auto_like_cron_jon_status"] = "1";
            $insert_data["post_auto_share_cron_jon_status"] = "1";
        }
        else
        {
            $insert_data["post_auto_comment_cron_jon_status"] = "0";
            $insert_data["post_auto_like_cron_jon_status"] = "0";
            $insert_data["post_auto_share_cron_jon_status"] = "0";
        }

        if($schedule_type=="now")
        {
            $insert_data["posting_status"] ='2';
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

                
                try
                {
                    $temp_data=$this->fb_rx_login->get_post_permalink($object_id,$page_access_token);
                    $insert_data_batch[$count]["post_url"]= isset($temp_data["permalink_url"]) ? $temp_data["permalink_url"] : ""; 
                    $insert_data_batch[$count]["last_updated_at"]= date("Y-m-d H:i:s");
                }
                catch(Exception $e) 
                {
                    $insert_data_batch[$count]["post_url"]= "https://www.facebook.com/".$fb_page_id."_".$object_id; 
                    $insert_data_batch[$count]["last_updated_at"]= date("Y-m-d H:i:s");
                }

                $this->basic->insert_data("facebook_rx_cta_post",$insert_data_batch[$count]);  
                //insert data to useges log table
                $this->_insert_usage_log($module_id=69,$request=1);


                if($auto_like_post=="1"  || $auto_comment=="1" || $auto_share_post=="1")
                {
                    sleep(10);
                }


                if($auto_like_post=="1" && ($this->session->userdata('user_type') == 'Admin' || in_array(74,$this->module_access)))
                {  
                   foreach ($page_info as $key2 => $value2) 
                    {
                        $like_page_accesstoken =  isset($value2["page_access_token"]) ? $value2["page_access_token"] : ""; 
                        try
                        {
                            $this->fb_rx_login->auto_like($object_id,$like_page_accesstoken);
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



                if($auto_comment=="1" && ($this->session->userdata('user_type') == 'Admin' || in_array(74,$this->module_access)))
                {  
                   foreach ($page_info as $key2 => $value2) 
                    {
                        if(!in_array($value2["id"], $post_to_pages)) continue;
                        $comment_page_accesstoken =  isset($value2["page_access_token"]) ? $value2["page_access_token"] : ""; 
                        try
                        {
                            $this->fb_rx_login->auto_comment($auto_comment_text,$object_id,$comment_page_accesstoken);
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


            }
            
            $count++;



        } 

    


        $profile_info = $this->basic->get_data("facebook_rx_fb_user_info",array("where"=>array("id"=> $account_switching_id,"user_id"=>$this->user_id)));  
        $user_access_token =  isset($profile_info[0]["access_token"]) ? $profile_info[0]["access_token"] : ""; 
        $user_fb_id =  isset($profile_info[0]["fb_id"]) ? $profile_info[0]["fb_id"] : ""; 
  



       if($schedule_type=="now" && ($this->session->userdata('user_type') == 'Admin' || in_array(74,$this->module_access)))
       {
            if($auto_share_post=="1" || $auto_share_to_profile!="No")
            {
                $post_paralink_data = $this->fb_rx_login->get_post_permalink($object_id,$share_access_token);
                $parmalink_url = isset($post_paralink_data["permalink_url"]) ? $post_paralink_data["permalink_url"] : ""; 
                
                if($parmalink_url!='')
                {
                    if($auto_share_post=="1")
                    {
                       foreach ($page_info as $key => $value) 
                        {
                            if(!in_array($value["id"], $auto_share_this_post_by_pages_new)) continue;

                            $share_page_id =  isset($value["page_id"]) ? $value["page_id"] : ""; 
                            $share_page_accesstoken =  isset($value["page_access_token"]) ? $value["page_access_token"] : "";
                            try
                            {
                                $this->fb_rx_login->feed_post("",$parmalink_url,"","","","",$share_page_accesstoken,$share_page_id);
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

                    if($auto_share_to_profile!="No")
                    {                        
                        try
                        {
                            $this->fb_rx_login->feed_post("",$parmalink_url,"","","","",$user_access_token,$user_fb_id);
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
            }
          
       }
 
       if($schedule_type=="now") $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i> {$this->lang->line("facebook CTA post has been performed successfully.")} ");
       else
       {
            if($this->db->insert_batch("facebook_rx_cta_post",$insert_data_batch))
            {
                $number_request = count($insert_data_batch);
                //insert data to useges log table
                $this->_insert_usage_log($module_id=69,$request=$number_request);
                $return_val=array("status"=>"1","message"=>"<i class='fa fa-check-circle'></i>  {$this->lang->line("facebook CTA post campaign has been created successfully.")} ");
            }
            else $return_val=array("status"=>"0","message"=>"<i class='fa fa-remove'></i>  {$this->lang->line("something went wrong. Facebook CTA post campaign has been failed.")} ");
       }

       echo json_encode($return_val);

        
    }





    public function meta_info_grabber()
    {
        if($_POST)
        {
            $link= $this->input->post("link");
            $this->load->library("fb_rx_login");
            $response=$this->fb_rx_login->get_meta_tag_fb($link);
            echo json_encode($response);
        }
    } 


    public function upload_link_preview()
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

            $allow=".jpg,.jpeg,.png";
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

    public function delete_post()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");

        $post_info = $this->basic->get_data('facebook_rx_cta_post',array('where'=>array('id'=>$id)));
        if($post_info[0]['posting_status'] != '2')
        {
            //******************************//
            // delete data to useges log table
            $this->_delete_usage_log($module_id=69,$request=1);   
            //******************************//
        }

        if($this->basic->delete_data("facebook_rx_cta_post",array("id"=>$id,"user_id"=>$this->user_id)))
        echo "1";
        else echo "0";
    }


}
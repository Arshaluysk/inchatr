<?php

require_once("Home.php"); // including home controller

class Facebook_ex_auto_comment extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(251,$this->module_access))
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

    public function get_periodic_time()
    {

        $all_periodic_time= array(
        
        '5' =>'every 5 mintues',
        '10' =>'every 10 mintues',
        '15' =>'every 15 mintues',
        '30' =>'every 30 mintues',
        '60' =>'every 1 hours',
        '120'=>'every 2 hours',
        '300'=>'every 5 hours',
        '600'=>'every 10 hours',
        '900'=>'every 15 hours',
        '1200'=>'every 20 hours',
        '1440'=>'every 24 hours',
        '2880'=>'every 48 hours',
        '4320'=>'every 72 hours',
       );
        return $all_periodic_time;
    }

    public function get_page_list()
    {
        

        $data['body'] = 'facebook_ex/auto_reply/auto_comment_page_list_for_comment';
        $data['page_title'] = $this->lang->line('Auto Comment reply - Page list');
        $data['auto_comment_template'] = $this->basic->get_data('auto_comment_reply_tb',array("where"=>array('user_id'=>$this->user_id)),array('id','template_name'));
        $data["time_zone"]= $this->_time_zone_list();
        $data["periodic_time"] = $this->get_periodic_time();
        $page_info = array();
        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))),"","","","","page_name asc");
        if(!empty($page_list))
        {
            $i = 0;
            foreach($page_list as $value)
            {
                $autoreply_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('page_info_table_id'=>$value['id'])),'','','','','last_reply_time desc');

                $autoreply_count = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('page_info_table_id'=>$value['id'])),array('sum(auto_comment_count) as auto_comment_count'));
                
                $page_info[$i]['id'] = $value['id'];
                $page_info[$i]['page_profile'] = $value['page_profile'];
                $page_info[$i]['page_name'] = $value['page_name'];
                $page_info[$i]['auto_reply_enabled_post'] = count($autoreply_info);
                if(empty($autoreply_info))
                    $page_info[$i]['last_reply_time'] = '0000-00-00 00:00:00';
                else
                    $page_info[$i]['last_reply_time'] = $autoreply_info[0]['last_reply_time'];

                if(empty($autoreply_count))
                    $page_info[$i]['autoreply_count'] = 0;
                else
                    $page_info[$i]['autoreply_count'] = $autoreply_count[0]['auto_comment_count'];

                $i++;
            }
        }

        $data["page_info"] = $page_info;
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



    public function import_latest_post()
    {
        $table_id = $this->input->post('table_id');
        $page_info = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("id"=>$table_id)));
        $respnse = array();
        $respnse['page_name'] = $page_info[0]['page_name'];
        $page_table_id = $page_info[0]['id'];

        $existing_data = array();
        $existing_data_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('user_id'=>$this->user_id,'page_info_table_id'=>$page_table_id)));

        if(!empty($existing_data_info))
        {
            foreach($existing_data_info as $value)
            {
                $existing_data[$value['post_id']] = $value['id'];
            }
        }

        $page_id = $page_info[0]['page_id'];
        $access_token = $page_info[0]['page_access_token'];


        try
        {
            $post_list = $this->fb_rx_login->get_postlist_from_fb_page($page_id,$access_token);

            if(isset($post_list['data']) && empty($post_list['data'])){
                $respnse['message'] = "<h3><div class='alert alert-danger text-center'>".$this->lang->line("There is no post on this page.")."</div></h3>";
            }
            else if(!isset($post_list['data']))
            {
                $respnse['message'] = "<h3><div class='alert alert-danger text-center'>".$this->lang->line("something went wrong, please try again.")."</div></h3>";
            }
            else
            {
                $str='
                <ul class="products-list product-list-in-box">';
                $i = 1;
                foreach($post_list['data'] as $value)
                {
                    $message = isset($value['message']) ? $value['message'] : '';
                    $post_thumb = isset($value['picture']) ? $value['picture'] : base_url('assets/images/50x50.png');
                    $post_created_at =isset($value['created_time']['date'])? $value['created_time']['date']:"";

                    $post_created_at = $post_created_at." UTC";
                    $post_created_at=date("d M y H:i",strtotime($post_created_at));


                    // if(strlen($message) >= 200)
                    //     $message = substr($message, 0, 200);
                    // else $message = $message;

                    if($message=='') $message='<span class="label label-light" style="border:none !important;"><i>'.$this->lang->line("No description found").'</i></span>';

                    if(array_key_exists($value['id'], $existing_data))
                    {
                        $button = "<button class='btn btn-sm btn-outline-default disabled'><i class='fa fa-check'></i> {$this->lang->line("enabled")}</button>&nbsp;&nbsp;<button class='btn btn-sm btn-outline-warning edit_reply_info' table_id='".$existing_data[$value['id']]."'><i class='fa fa-edit'></i> {$this->lang->line("edit")}</button>";
                    }
                    else
                    $button = "<button class='btn btn-sm btn-outline-success enable_auto_commnet' manual_enable='no' page_table_id='".$table_id."' post_id='".$value['id']."'><i class='fa fa-check-circle'></i> {$this->lang->line("enable auto comment")}</button>";
                    
                    $str.='
                    <li class="item">
                      <div class="product-img">
                        <a target="_BLANK" href="http://facebook.com/'.$value['id'].'"><img src="'.$post_thumb.'" class="img-thumbnail"></a>
                      </div>
                      <div class="product-info" style="text-align:left !important;" >
                        '.$this->lang->line("Post ID").' : <a target="_BLANK" href="http://facebook.com/'.$value['id'].'" class="product-title">'.$value['id'].'</a>
                        <span class="pull-right" style="padding-top:10px;">'.$button.'</span>
                        <span style="text-align:left !important;" class="product-description">
                              <small><b>['.$post_created_at.']</b> '.$message.'</small>
                        </span>
                      </div>
                    </li>';
                    $i++;                
                }
                $str.='</ul>';

                $respnse['message'] = $str;
            }

        }
        catch(Exception $e) 
        {
          $error_msg = "<div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$e->getMessage()."</div>";
          $respnse['message'] = $error_msg;
        }

        echo json_encode($respnse);

    }


    public function checking_post_id()
    {
        
        $post_id = trim($this->input->post('post_id'));
        $page_table_id = trim($this->input->post('page_table_id'));
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_table_id)));
        $page_name = $page_info[0]['page_name'];
        $access_token = $page_info[0]['page_access_token'];
        //adding page id before post id, for error handling
        if(strpos($post_id, '_')!==FALSE) $post_id=$post_id;
        else $post_id = $page_info[0]['page_id']."_".$post_id;

        try
        {
            $post_info = $this->fb_rx_login->get_post_info_by_id($post_id,$access_token);

            if(isset($post_info['error']))
            {
                $response['error'] = 'yes';
                $response['error_msg'] = $post_info['error']['message'];
            }
            else
                $response['error'] = 'no';

            if(empty($post_info))
            {
                $response['error'] = 'yes';
                $response['error_msg'] = $this->lang->line("please provide correct post id.");
            }

        }
        catch(Exception $e)
        {
            $response['error'] = 'yes';
            $response['error_msg'] = $e->getMessage();
        }
        echo json_encode($response);
    }


    public function get_tableid_by_postid()
    {
        $page_table_id = $this->input->post('page_table_id');
        $post_id = $this->input->post('post_id');
        $page_table_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$page_table_id)),array('page_id'));
        $page_id = $page_table_info[0]['page_id'];
        if(strpos($post_id, '_')!==FALSE) $post_id=$post_id;
        else $post_id = $page_id."_".$post_id;


        $where['where'] = array(
            'user_id' => $this->user_id,
            'page_info_table_id' => $page_table_id,
            'post_id' => $post_id
            );


        $table_info = $this->basic->get_data('auto_comment_reply_info',$where,'','',1);
        if(empty($table_info))
            $respnse['error'] = 'yes';
        else
        {
            $respnse['error'] = 'no';
            $respnse['table_id'] = $table_info[0]['id'];

        }
        echo json_encode($respnse);
    }


    public function ajax_autocomment_reply_submit()
    {
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$value;
            }
        }
        $data['auto_comment_template_id'] = $auto_comment_template_id;
        $data['user_id']=$this->user_id;
      
        if(isset($_POST['schedule_type']))
        {
           $schedule_type = $_POST['schedule_type'];

        }
   
        if($schedule_type == "onetime"){
             

             $data['schedule_time'] = $schedule_time;
             $data['time_zone'] = $time_zone;
             $data['schedule_type'] = $schedule_type;

          
        }
        if($schedule_type == "periodic")
        {
            $data['periodic_time'] = $periodic_time;
            $data['schedule_type'] = $schedule_type;
            $data['time_zone'] = $periodic_time_zone;
            $data['campaign_start_time'] = $campaign_start_time;
            $data['campaign_end_time'] = $campaign_end_time;
            $data['comment_start_time'] = $comment_start_time;
            $data['comment_end_time'] = $comment_end_time;

            if(isset($_POST['auto_comment_type']))
            {
                $auto_comment_type = $_POST['auto_comment_type'];

            }
            if($auto_comment_type == "random"){

                $data['auto_comment_type'] = $auto_comment_type;
            }
            if($auto_comment_type == "serially"){

                $data['auto_comment_type'] =$auto_comment_type;
            }
         }


        $data['campaign_name'] = $auto_campaign_name;

        
      


        //************************************************//

        $status=$this->_check_usage($module_id=251,$request=1);
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

    
        $page_info = $this->basic->get_data('facebook_rx_fb_page_info',array('where'=>array('id'=>$auto_reply_page_id)));

        $page_name = $page_info[0]['page_name'];

        $auto_reply_post_id = trim($auto_reply_post_id);
        $auto_reply_post_id_array = explode('_', $auto_reply_post_id);
        if(count($auto_reply_post_id_array) == 1)
        {
            $auto_reply_post_id = $page_info[0]['page_id']."_".$auto_reply_post_id;
        }

        // $manual_reply_description = "";

        if($manual_enable == 'yes')
        {
            try
            {
                $post_info = $this->fb_rx_login->get_post_info_by_id($auto_reply_post_id,$page_info[0]['page_access_token']);

                if(isset($post_info['error']))
                {
                    $response['error'] = 'yes';
                    $response['error_msg'] = $post_info['error']['message'];
                }
                else
                {
                    $post_created_at = isset($post_info[$auto_reply_post_id]['created_time']) ? $post_info[$auto_reply_post_id]['created_time'] : "";
                    if(isset($post_info[$auto_reply_post_id]['message']))
                        $post_description = isset($post_info[$auto_reply_post_id]['message']) ? $post_info[$auto_reply_post_id]['message'] : "";
                    else if(isset($post_info[$auto_reply_post_id]['name']))
                        $post_description = isset($post_info[$auto_reply_post_id]['name']) ? $post_info[$auto_reply_post_id]['name'] : "";
                    else
                        $post_description = isset($post_info[$auto_reply_post_id]['description']) ? $post_info[$auto_reply_post_id]['description'] : "";
                    
                    $post_thumb = isset($post_info[$auto_reply_post_id]['picture']) ? $post_info[$auto_reply_post_id]['picture'] : "";
                }

            }
            catch(Exception $e)
            {
                $post_created_at = "";
                $post_description = "";
                $post_thumb = "";
            }
        }
        else
        {
            try{

                $post_list = $this->fb_rx_login->get_postlist_from_fb_page($page_info[0]['page_id'],$page_info[0]['page_access_token']);
                if(isset($post_list['data']) && !empty($post_list['data']))
                {
                    foreach($post_list['data'] as $value)
                    {
                        if($value['id'] == $auto_reply_post_id)
                        {
                            $post_created_at = $value['created_time']['date'];
                            // $post_description = isset($value['message']) ? $value['message'] : '';

                            if(isset($value['message']))
                                $post_description = isset($value['message']) ? $value['message'] : "";
                            else if(isset($value['name']))
                                $post_description = isset($value['name']) ? $value['name'] : "";
                            else
                                $post_description = isset($value['description']) ? $value['description'] : "";

                            $post_thumb = isset($value['picture']) ? $value['picture'] : "";

                            // $manual_reply_description = "found";
                            break;
                        }
                    }
                }
            }
            catch(Exception $e)
            {            
                $post_created_at = "";
                $post_description = "";
                $post_thumb = "";
            }
        }
        $post_description = $this->db->escape($post_description);
        $data['post_description'] = $post_description;
        $data['post_id'] = $auto_reply_post_id;
        $data['page_info_table_id'] =$auto_reply_page_id;
        $data['post_created_at']=$post_created_at;
        $data['page_name'] = $page_name;
        

        if($this->basic->insert_data('auto_comment_reply_info',$data))
        {
            //insert data to useges log table
            $this->_insert_usage_log($module_id=251,$request=1);
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


    public function ajax_autoreply_delete()
    {
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('id'=>$table_id)));
        if($post_info[0]['auto_comment_count'] == 0)
        {
            //******************************//
            // delete data to useges log table
            $this->_delete_usage_log($module_id=251,$request=1);   
            //******************************//
        }

        $this->basic->delete_data('auto_comment_reply_info',array('id'=>$table_id));
        echo 'success';
    }


    public function ajax_edit_reply_info()
    {
        $respnse = array();
        $table_id = $this->input->post('table_id',true);
        $info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('id'=>$table_id)));



        if($info[0]['schedule_type'] == 'onetime'){
              
              $response['edit_schedule_time_o'] = $info[0]['schedule_time'];
              $response['edit_time_zone_o'] = $info[0]['time_zone'];
              $response['edit_schedule_type'] =$info[0]['schedule_type'];
        }
        
        if($info[0]['schedule_type'] == 'periodic')
        {
            $response['edit_campaign_start_time'] = $info[0]['campaign_start_time'];
            $response['edit_campaign_end_time'] = $info[0]['campaign_end_time'];
            $response['edit_periodic_time'] = $info[0]['periodic_time'];
            $response['edit_schedule_type'] =$info[0]['schedule_type'];
            $response['edit_periodic_time_zone'] = $info[0]['time_zone'];
            $response['edit_comment_start_time'] =$info[0]['comment_start_time'];
            $response['edit_comment_end_time'] =$info[0]['comment_end_time'];
            if($info[0]['auto_comment_type']=='random'){
                $response['edit_auto_comment_type'] = $info[0]['auto_comment_type'];
            }        
            if($info[0]['auto_comment_type']=='serially'){
                $response['edit_auto_comment_type'] = $info[0]['auto_comment_type'];
            }
        }
        // if($info[0]['auto_comment_type']=='random'){
        //     $response['edit_auto_comment_type'] = $info[0]['auto_comment_type'];
        // }        
        // if($info[0]['auto_comment_type']=='serially'){
        //     $response['edit_auto_comment_type'] = $info[0]['auto_comment_type'];
        // }
        $response['edit_auto_comment_template_id'] = $info[0]['auto_comment_template_id'];
        $response['edit_campaign_name'] = $info[0]['campaign_name'];
        $response['edit_auto_reply_page_id'] = $info[0]['page_info_table_id'];
        $response['edit_auto_reply_post_id'] = $info[0]['post_id'];
  
        // echo "<pre>";
        // print_r($response);
        // exit;
        echo json_encode($response);


       
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

        if(isset($_POST['edit_schedule_type']))
        {
           $schedule_type = $_POST['edit_schedule_type'];

        }
        
        if($schedule_type == "onetime"){
             

             $edit_schedule_time_o = $edit_schedule_time_o;
             $edit_time_zone_o = $edit_time_zone_o;
           
             $schedule_type = $schedule_type;
             $edit_periodic_time='';
             $edit_auto_comment_type='';
             $edit_campaign_start_time='';
             $edit_campaign_end_time='';
             $edit_comment_start_time='';
             $edit_comment_end_time='';





            
        }
        if($schedule_type == "periodic")
        {
            $edit_periodic_time = $edit_periodic_time;
            $schedule_type = $schedule_type;
            $edit_campaign_start_time = $edit_campaign_start_time;
            $edit_campaign_end_time = $edit_campaign_end_time;
            $edit_schedule_time_o = '';
            $edit_time_zone_o = $edit_periodic_time_zone;
            //$edit_periodic_time =$periodic_time_zone;
            $edit_comment_start_time=$edit_comment_start_time;
            $edit_comment_end_time=$edit_comment_end_time;
            if(isset($_POST['edit_auto_comment_type']))
            {
               $edit_auto_comment_type = $_POST['edit_auto_comment_type'];

            }

            if($edit_auto_comment_type == "random"){
               $edit_auto_comment_type =$edit_auto_comment_type;
            }
            if($edit_auto_comment_type == "serially"){
               $edit_auto_comment_type =$edit_auto_comment_type;
            }
         }

         // if(isset($_POST['edit_auto_comment_type']))
         // {
         //    $edit_auto_comment_type = $_POST['edit_auto_comment_type'];

         // }

         // if($edit_auto_comment_type == "random"){
         //    $edit_auto_comment_type =$edit_auto_comment_type;
         // }
         // if($edit_auto_comment_type == "serially"){
         //    $edit_auto_comment_type =$edit_auto_comment_type;
         // }


        $data = array(
   
            'campaign_name' => $edit_campaign_name,
            'auto_comment_template_id' => $edit_auto_comment_template_id,
            'schedule_type'=> $schedule_type,
            'schedule_time' =>$edit_schedule_time_o,
            'time_zone' => $edit_time_zone_o,
            'periodic_time' => $edit_periodic_time,
            'campaign_start_time' => $edit_campaign_start_time,
            'campaign_end_time' => $edit_campaign_end_time,
            'auto_comment_type' =>$edit_auto_comment_type,
            'comment_start_time'=>$edit_comment_start_time,
            'comment_end_time'=>$edit_comment_end_time

           );





        $where = array(
            'user_id' => $this->user_id,
            'page_info_table_id' => $edit_auto_reply_page_id,
            'post_id' => $edit_auto_reply_post_id
            );

        if($this->basic->update_data('auto_comment_reply_info',$where,$data))
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


    public function ajax_get_reply_info()
    {
        $table_id = $this->input->post('table_id');
        $reply_info = $this->basic->get_data('auto_comment_reply_info',array('where'=>array('id'=>$table_id)));

        if(isset($reply_info[0]['auto_reply_done_info']) && $reply_info[0]['auto_reply_done_info'] != '')
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
                             <th class="text-center">'.$this->lang->line("comment id").'</th>
                             <th class="text-center">'.$this->lang->line("comment status").'</th>
                             <th class="text-center">'.$this->lang->line("comment time").'</th>
                             <th class="text-center">'.$this->lang->line("schedule type").'</th>
                             <th>'.$this->lang->line("comment").'</th>
                             
                         </tr>
                     </thead>
                     <tbody>';
                         
                     
            $info = json_decode($reply_info[0]['auto_reply_done_info'],true);
   

            foreach($info as $value)
            {
                $comment_time = date('d M y H:i:s',strtotime($value['comment_time']));
               
                $reply_status = isset($value['reply_status']) ? $value['reply_status']:"";
                $schedule_type = isset($value['schedule_type']) ? $value['schedule_type']:"";
                if($reply_status=='success')

                 $reply_status='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("success").'</span>';
                if(!strpos($reply_status,'failed'))


                if($schedule_type =='periodic')
                 {$schedule_type='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("perodic").'</span>';}

        
                if($schedule_type == 'onetime')

                $schedule_type='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("onetime").'</span>';



                $str .= '<tr>
                            <td class="text-center"><a target="_BLANK" href="http://facebook.com/'.$value['id'].'" class="product-title">'.$value['id'].'</a></td>
                           
                            <td class="text-center">'.$reply_status.'</td>
                            <td class="text-center">'.$comment_time.'</td>
                            <td class="text-center">'.$schedule_type.'</td>
                            <td>'.$value['comment_text'].'</td>
                            
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


    public function all_auto_reply_report($id = "")
    {

    	$this->session->set_userdata('all_search_page_name', '');

        $page_info = array();
        $page_list = $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info"))));
        $data['auto_comment_template'] = $this->basic->get_data('auto_comment_reply_tb',array("where"=>array('user_id'=>$this->user_id)),array('id','template_name'));
        $data["time_zone"]= $this->_time_zone_list();
        $data["periodic_time"] = $this->get_periodic_time();
        if(!empty($page_list))
        {
            foreach($page_list as $value)
            {
                array_push($page_info, $value['page_name']);

                if ($id != "" && $id == $value['id']) {

                	$data['selected_page_id'] = $id;
                	$data['selected_page_name'] = $value['page_name'];
                	$this->session->set_userdata('all_search_page_name', $value['page_name']);
                }
            }
        }
        $data['page_info'] = $page_info;

        $data['body'] = 'facebook_ex/auto_reply/all_auto_comment_report';
        $data['page_title'] = $this->lang->line('Auto Comment Report');
        $data['emotion_list'] = $this->get_emotion_list();
        $this->_viewcontroller($data);
    }


    public function all_auto_reply_report_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'ASC';

        $campaign_name = trim($this->input->post("campaign_name", true));
        $search_page_name = trim($this->input->post("search_page_name", true));
        $is_searched = $this->input->post('is_searched', true);


        if ($this->session->userdata('all_search_page_name') != '' && $search_page_name == "") 
        	$search_page_name = $this->session->userdata('all_search_page_name');
        

        if ($is_searched) 
        {
            $this->session->set_userdata('all_search_post_campaign_name', $campaign_name);
            $this->session->set_userdata('all_search_page_name', $search_page_name);
        }

        // saving session data to different search parameter variables
        $search_campaign_name   = $this->session->userdata('all_search_post_campaign_name');
        $all_search_page_name   = $this->session->userdata('all_search_page_name');

        $where_simple=array();        
        if ($search_campaign_name) $where_simple['campaign_name like '] = "%".$search_campaign_name."%";
        if ($all_search_page_name != '') $where_simple['page_name like '] = "%".$all_search_page_name."%";

        // $where_simple['page_info_table_id'] = $table_id;
        $where_simple['user_id'] = $this->user_id;

        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "auto_comment_reply_info";
        // $select = array('id','auto_reply_campaign_name','post_created_at','last_updated_at');
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        $info_new = array();
        $i = 0;
        foreach($info as $value)
        {
            if($value['auto_private_reply_status']=='2')
            $info_new[$i]['status'] = "<span class='label label-light gray border_gray'><i class='fa fa-clock-o red'></i> ".$this->lang->line('expired');
            else  $info_new[$i]['status'] = "<span class='label label-light gray border_gray'><i class='fa fa-check-circle green'></i> ".$this->lang->line('live')."</span>";
            
            $onlypostid=explode('_', $value['post_id']);
            // array_pop($onlypostid);
            $onlypostid2=isset($onlypostid[1])?$onlypostid[1]:$value['post_id'];

            $info_new[$i]['id'] = $value['id'];
            $info_new[$i]['campaign_name'] = $value['campaign_name'];
            $info_new[$i]['page_name'] = $value['page_name'];
            $info_new[$i]['post_id'] = "<a target='_BLANK' href='https://facebook.com/".$value['post_id']."'>".$onlypostid2."</a>";
            $info_new[$i]['auto_comment_count'] = $value['auto_comment_count'];
            
            $page_url = "<button class='btn btn-outline-info view_report' table_id='".$value['id']."' title='".$this->lang->line("report")."'><i class='fa fa-eye'></i></button>&nbsp;<button class='btn btn-outline-warning edit_reply_info' table_id='".$value['id']."' title='".$this->lang->line("edit")."'><i class='fa fa-edit'></i></button>&nbsp;<button class='btn btn-outline-danger delete_report' table_id='".$value['id']."' title='".$this->lang->line("delete")."'><i class='fa fa-trash'></i></button>";
            
            $button = '';
            if($value['auto_private_reply_status'] == '0' || $value['auto_private_reply_status'] == '1')
            $button = "&nbsp;<button class='btn btn-outline-warning pause_campaign_info' table_id='".$value['id']."' title='".$this->lang->line("pause campaign")."'><i class='fa fa-pause'></i></button>";
            if($value['auto_private_reply_status'] == '2')
            $button = "&nbsp;<button class='btn btn-outline-success play_campaign_info' table_id='".$value['id']."' title='".$this->lang->line("start campaign")."'><i class='fa fa-play'></i></button>";
            $force= "&nbsp;<a  id='".$value['id']."' class='force btn btn-outline-primary' title='".$this->lang->line("force reprocessing")."'><i class='fa fa-refresh'></i></a>";
            $page_url = $page_url.$button.$force;;
            $info_new[$i]['view'] = $page_url;
 
            $info_new[$i]['post_created_at'] = $value['post_created_at'];
            $last_reply_time = $value['last_reply_time'];
            if($last_reply_time=='0000-00-00 00:00:00') $last_reply_time='X';
            else $last_reply_time=date("d M y H:i",strtotime($last_reply_time));
            $info_new[$i]['last_reply_time'] = $last_reply_time;
            $info_new[$i]['error_message'] = $value['error_message'];
            $info_new[$i]['post_description'] = $value['post_description'];

            $post_thumb = isset($info_new[$i]['post_thumb']) ? $info_new[$i]['post_thumb'] : base_url('assets/images/50x50.png');
            $info_new[$i]['post_thumb'] = "<img class='img-thumbnail' src='".$post_thumb."' style='height:50px;width:50px;'>";

            $i++;
        }

        echo convert_to_grid_data($info_new, $total_result);
    }


    public function ajax_autoreply_pause()
    {
        $table_id = $this->input->post('table_id');
        $this->basic->update_data('auto_comment_reply_info',array('id'=>$table_id),array('auto_private_reply_status'=>'2'));
        echo 'success';
    }

    public function ajax_renew_campaign()
    {
        $table_id = $this->input->post('table_id');
        $this->basic->update_data('auto_comment_reply_info',array('id'=>$table_id),array('last_updated_at'=>date("Y-m-d H:i:s")));
        echo 'success';
    }

    public function ajax_autoreply_play()

    {
        $table_id = $this->input->post('table_id');
        $post_info = $this->basic->update_data('auto_comment_reply_info',array('id'=>$table_id),array('auto_private_reply_status'=>'0'));
        echo 'success';
    }


    public function force_reprocess_campaign()
    {
        if(!$_POST) exit();
        $id=$this->input->post("id");

        $where = array('id'=>$id,'user_id'=>$this->user_id);
        $data = array('auto_private_reply_status'=>'0');
        $this->basic->update_data('auto_comment_reply_info',$where,$data);
        if($this->db->affected_rows() != 0)
            echo "1";
        else
            echo "0";
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






    /* Auto Comment reply Template Manager */

    public function comment_template_manager()
    {
        $data['body'] = 'facebook_ex/auto_reply/auto_comment_reply_template';
        $data['page_title'] = $this->lang->line('Auto Comment Template Manager');
        
        
        $this->_viewcontroller($data);
    }


    public function create_template_action()
    {
        if(isset($_POST["action"]))
        {
            
            if($_POST["action"] == "insert")
            {
                  $auto_reply_comment_text =$_POST["auto_reply_comment_text"];
                
                  $auto_reply_comment_text=json_encode($auto_reply_comment_text);
                
                
                  $data = array(
                      'user_id' => $this->user_id,
                      'template_name'                     =>$_POST["template_name"],
                      'auto_reply_comment_text'    =>  $auto_reply_comment_text
                  );

                 
                  if($this->basic->insert_data('auto_comment_reply_tb',$data)) 
                  {

                      $return['status'] = 1;
                      $return['message'] = "<div class='alert alert-success'>".$this->lang->line("your given information has been submitted successfully.")."</div>";
                  }
                  else
                  {
                      $return['status'] = 0;
                      $return['message'] = "<div class='alert alert-danger'>".$this->lang->line("something went wrong, please try again.")."</div>";
                  }
                
                  echo json_encode($return);
                
              
            }




          if($_POST["action"] == "edit")
          {
                $id = $_POST['hidden_id'];
               
                $where = array('id'=>$id);
            
                $data = array(
                    
                    'template_name' => $_POST['template_name'],
                    'auto_reply_comment_text' => json_encode($_POST['auto_reply_comment_text'])

                    );
                
              if($this->basic->update_data('auto_comment_reply_tb',$where,$data)){
                    echo "success";

                } else
                {
                    echo "fail";
                }
          }



          

          
          
        }


          
        

    }

    public function template_manager_data()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'auto_comment_reply_tb.id'; 
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
        $campaign_name = trim($this->input->post("template_name", true));
        $is_searched = $this->input->post('is_searched', true);
        if($is_searched) 
        {
            $this->session->set_userdata('template_manager_search_ultraposter', $campaign_name);
           
        }
        $search_campaign_names  = $this->session->userdata('template_manager_search_ultraposter');
        $where_simple=array();
        if ($search_campaign_names) $where_simple['template_name like ']    = "%".$search_campaign_names."%";
    
        $where_simple['auto_comment_reply_tb.user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "auto_comment_reply_tb";
        $info = $this->basic->get_data($table, $where, '','',$limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="auto_comment_reply_tb.id");
        $total_result = $total_rows_array[0]['total_rows'];
        $information = array();
        for($i=0;$i<count($info);$i++)
        {   
            $id = $info[$i]['id'];
            $information[$i]['template_name'] = $info[$i]['template_name'];
            $information[$i]['action'] = "<button type='button' class='text-center edit_reply_info btn btn-outline-warning edit' name='edit' title='".$this->lang->line("Edit")."' id=".$id." '><i class='fa  fa-edit'></i></button>&nbsp;<button name='delete' class='text-center delete_reply_info btn btn-outline-danger delete' title='".$this->lang->line("Delete")."' id=".$id." '><i class='fa fa-trash'></i></button>";
        }
        echo convert_to_grid_data($information, $total_result);

    }

    public function ajaxselect()
    {  
        $id = $this->input->post('id');
        $info = $this->basic->get_data("auto_comment_reply_tb",array("where"=>array("id"=>$id)));

        $auto_reply_comment_text = '';
        $template_name = '';
         foreach($info as $row)
        {
            $template_name = $row["template_name"];
            $comment_array = json_decode($row["auto_reply_comment_text"]);
            $x = count($comment_array);
            $count = 1;
            foreach($comment_array as $comment)
            {

    

                $auto_reply_comment_text .= '
                    <div style="margin-top:50px;border:1px solid #ccc;" class="form-group">
                    <h4 class="modal-title text-center" style="margin: 0px !important;padding:10px 0;font-size:13px; text-align:center;"><i class="fa fa-comments"></i> '.$this->lang->line("auto comment").'</h4>
                   <textarea type="text" name="auto_reply_comment_text[]" id="auto_reply_comment_text_'.$count.'" class="form-control name_list" style="height:70px;width:100%;border:none !important;border-top:1px solid #ccc !important;background:#fcfcfc;border-radius:0 !important;">'.$comment.'</textarea>
                      
                        <a href="#" style="margin-top:10px;font-size:10px;text-align:center;" class="btn btn-outline-danger remove_field pull-right title="Remove this item"><i class="fa fa-remove"></i> '.$this->lang->line("remove").'</a>
                    </div>
                ';
                $count++;
            }
         }
         $output = array(
            'template_name'                  =>  $template_name,
            'auto_reply_comment_text' =>  $auto_reply_comment_text,
            'x' => $x
            
         );
        
         echo json_encode($output);
        

    }

    public function delete_comment()
    {
        if(isset($_POST["id"]))
        {
            $id = $this->input->post('id');
            // $post_info = $this->basic->get_data('auto_comment_reply_tb',array('where'=>array('id'=>$id)));
            $this->basic->delete_data('auto_comment_reply_tb',array('id'=>$id));
        }
    }






}
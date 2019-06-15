<?php

require_once("Home.php"); // loading home controller

class Facebook_ex_json_messanger extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(81,$this->module_access))
        redirect('home/login_page', 'location'); 
        $this->user_id=$this->session->userdata('user_id');
    
        $this->important_feature();
        $this->member_validity();        
    }

    public function index()
    {
      $this->json_messanger_view();
    }

    public function json_messanger_view()
    {
        $data['body'] = 'facebook_ex/json_messanger/json_messanger_view';
        $data['page_title'] = $this->lang->line('Messanger AD JSON script');
        $this->_viewcontroller($data);
    }


    public function upload_image_only()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') exit();

        $ret=array();
        $output_dir = FCPATH."upload";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename="image_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;

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



    public function delete_uploaded_file() // deletes the uploaded image to upload another one
    {

        $output_dir = FCPATH."upload";
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


    public function ajax_get_json_code()
    {
        //************************************************//

        $status=$this->_check_usage($module_id=81,$request=1);
        if($status=="2") 
        {
            $error_msg = $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $response['message'] = $error_msg;
            $response['status'] = 'error';
            echo json_encode($response);
            exit();
        }
        else if($status=="3") 
        {
            $error_msg = $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            $response['message'] = $error_msg;
            $response['status'] = 'error';
            echo json_encode($response);
            exit();
        }
        //************************************************//

        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$value;
            }
        }

        $response = array();        

        $message = array ( 
            'message' => array ( 
                'attachment' => array ( 
                    'type' => 'template', 
                    'payload' => array ( 
                        'template_type' => 'generic', 
                        'elements' => array ( 
                            0 => array ( 
                                'title' => $message_title, 
                                'image_url' => $image_url_link, 
                                'subtitle' => $message_subtitle, 
                                'buttons' => array ( 
                                    0 => array ( 
                                        'type' => 'web_url', 
                                        'url' => $website_url, 
                                        'title' => $website_button_text
                                        ), 
                                    1 => array ( 
                                        'type' => 'postback', 
                                        'title' => $start_chat_button_text, 
                                        'payload' => 'USER_DEFINED_PAYLOAD'
                                        ) 
                                    ) 
                                )
                            )
                        )
                    ), 
                'quick_replies' => array ( 
                    0 => array ( 
                        'content_type' => 'text', 
                        'title' => $reply_1_button_text, 
                        'payload' => 'reply1' 
                        ), 
                    1 => array ( 
                        'content_type' => 'text', 
                        'title' => $reply_2_button_text, 
                        'payload' => 'reply2' 
                        )
                    )
                ) 
            );
        //insert data to useges log table
        $this->_insert_usage_log($module_id=81,$request=1);

        //modified code start
        $message= json_encode($message, JSON_UNESCAPED_UNICODE);
        $result['message'] = stripslashes($message);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        //modified code end 
    }


}
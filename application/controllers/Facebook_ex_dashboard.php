<?php

require_once("Home.php"); // including home controller

/**
* class config
* @category controller
*/
class Facebook_ex_dashboard extends Home
{
	public $user_id;
    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');
        $this->user_id=$this->session->userdata('user_id');
    
        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();     
    }

    /**
    * load index method. redirect to config
    * @access public
    * @return void
    */
    public function index()
    {
        $this->fb_ex_admin_dashboard();
    }

    public function fb_ex_admin_dashboard()
    {
        $today = date("Y-m-d");
        $from_date = date("Y-m-d", strtotime("$today - 7 days"));
        $auto_post_info = $this->basic->get_data("facebook_rx_auto_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$this->user_id)),array('id','last_updated_at','post_type','campaign_name','posting_status','post_url'));
        $post_publish_info = array();
        foreach ($auto_post_info as $key => $value) {
            $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
            if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date][$value['post_type']]))
                $post_publish_info[$formated_date][$value['post_type']]++;
            if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date][$value['post_type']]))
                $post_publish_info[$formated_date][$value['post_type']] = 1;
        }


        $slider_post_info = $this->basic->get_data("facebook_rx_slider_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$this->user_id)),array('id','last_updated_at','post_type','campaign_name','posting_status','post_url'));
        foreach ($slider_post_info as $key => $value) {
            $formated_date = date("Y-m-d",strtotime($value['last_updated_at']));
            if($value['posting_status'] == '2' && isset($post_publish_info[$formated_date][$value['post_type']]))
                $post_publish_info[$formated_date][$value['post_type']]++;
            if($value['posting_status'] == '2' && !isset($post_publish_info[$formated_date][$value['post_type']]))
                $post_publish_info[$formated_date][$value['post_type']] = 1;
        }

        $cta_post_info = $this->basic->get_data("facebook_rx_cta_post",array('where'=>array('date_format(last_updated_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$this->user_id)),array('id','last_updated_at','cta_type','campaign_name','posting_status','post_url'));
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

        $all_time_message_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $this->user_id)),array('sum(successfully_sent) as total_sent_message'));
        $data['all_time_message_sent'] = $all_time_message_info[0]['total_sent_message'];

        $all_time_auto_reply_info = $this->basic->get_data('facebook_ex_autoreply',array('where' => array('user_id' => $this->user_id)),array('sum(auto_private_reply_count) as total_private_reply','sum(auto_comment_reply_count) as total_comment_reply'));
        $data['all_time_private_reply_sent'] = $all_time_auto_reply_info[0]['total_private_reply'];
        $data['all_time_comment_reply_sent'] = $all_time_auto_reply_info[0]['total_comment_reply'];

        

        $all_time_auto_post = $this->basic->get_data('facebook_rx_auto_post',array('where' => array('user_id' => $this->user_id, 'posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
        $all_time_cta_post = $this->basic->get_data('facebook_rx_cta_post',array('where' => array('user_id' => $this->user_id, 'posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
        $all_time_slider_post = $this->basic->get_data('facebook_rx_slider_post',array('where' => array('user_id' => $this->user_id, 'posting_status' => '2')),$select='',$join='',$limit='',$start=NULL,'last_updated_at DESC');
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


        $message_sent_info_get = $this->basic->get_data("facebook_ex_conversation_campaign",array('where'=>array('date_format(completed_at,"%Y-%m-%d") >='=>$from_date,'user_id'=>$this->user_id)),array('id','completed_at','campaign_type','campaign_name','posting_status','successfully_sent'));
        $message_sent_info = array();

        foreach ($message_sent_info_get as $key => $value) {
            $formated_date = date("Y-m-d",strtotime($value['completed_at']));
            if(!isset($message_sent_info[$formated_date]['successfully_sent'])) $message_sent_info[$formated_date]['successfully_sent'] = 0;
            $message_sent_info[$formated_date]['successfully_sent'] = $message_sent_info[$formated_date]['successfully_sent']+$value['successfully_sent'];
        }


        $autoreply_info_get = $this->basic->get_data("facebook_ex_autoreply",array('where'=>array('date_format(last_reply_time,"%Y-%m-%d") >='=>$from_date,'user_id'=>$this->user_id)),array('id','post_thumb','post_id','auto_reply_campaign_name','auto_reply_done_info'));
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


        $recently_message_sent_completed_campaing_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $this->user_id,'posting_status' => '2')),$select='',$join='',$limit='5',$start=NULL,'added_at DESC');
        
        $upcoming_message_sent_campaign_info = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $this->user_id,'posting_status' => '0')),$select='',$join='',$limit='5',$start=NULL,'added_at DESC');

        $data['recently_message_sent_completed_campaing_info'] = $recently_message_sent_completed_campaing_info;
        $data['upcoming_message_sent_campaign_info'] = $upcoming_message_sent_campaign_info;

        $subscriber_info = $this->basic->get_data('facebook_rx_conversion_user_list',array('where' => array('user_id' => $this->user_id,'permission'=>'1')),array('id'));
        $data['total_subscribers'] = count($subscriber_info);

        $auto_reply_enable = $this->basic->get_data('facebook_ex_autoreply',array('where' => array('user_id' => $this->user_id)),array('count(id) as auto_reply_enable'));
        $data['total_auto_reply_enabled_campaign'] = $auto_reply_enable[0]['auto_reply_enable'];


        $scheduled_bulk_message_campaign = $this->basic->get_data('facebook_ex_conversation_campaign',array('where' => array('user_id' => $this->user_id,'posting_status' => '0')),array('count(id) as scheduled_bulk_message_campaign'));
        $data['scheduled_bulk_message_campaign'] = $scheduled_bulk_message_campaign[0]['scheduled_bulk_message_campaign'];



        $scheduled_auto_post_campaign = $this->basic->get_data('facebook_rx_auto_post',array('where' => array('user_id' => $this->user_id,'posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
        $scheduled_cta_post_campaign = $this->basic->get_data('facebook_rx_cta_post',array('where' => array('user_id' => $this->user_id,'posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
        $scheduled_carousel_slider_campaign = $this->basic->get_data('facebook_rx_slider_post',array('where' => array('user_id' => $this->user_id,'posting_status' => '0')),$select='',$join='',$limit='',$start=NULL,'schedule_time ASC');
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


        $last_auto_reply_post_info = $this->basic->get_data('facebook_ex_autoreply',array('where' => array('user_id' => $this->user_id)),$select='auto_reply_done_info,post_description',$join='',$limit='3',$start=NULL,'last_reply_time DESC');        

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

 
}
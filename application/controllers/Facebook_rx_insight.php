<?php

require_once("Home.php"); // including home controller

class Facebook_rx_insight extends Home
{

    public $user_id;

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');
        // if($this->session->userdata('user_type') != 'Admin' && !in_array(72,$this->module_access))
        // redirect('home/login_page', 'location');
        $this->user_id=$this->session->userdata('user_id');

        if($this->session->userdata("facebook_rx_fb_user_info")==0)
        redirect('facebook_rx_account_import/index','refresh');

        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();

        $this->load->library("fb_rx_login");
    }


    public function index()
    {
    }


    public function page_insight($table_name='',$table_id=0)
    {
        if($table_id==0) exit();
        $where['where'] = array('id'=>$table_id,'user_id'=>$this->user_id);
        $page_info = $this->basic->get_data($table_name,$where);
        $data['cover_image'] = $page_info[0]['page_cover'];
        $data['profile_image'] = $page_info[0]['page_profile'];
        $access_token = $page_info[0]['page_access_token'];
        $page_id = $page_info[0]['page_id'];

        $metrics = "insights/page_storytellers_by_story_type/day";
        $page_storytellers_by_story_type = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);

        $i = 0;
        $page_storytellers_by_story_type_description = isset($page_storytellers_by_story_type[0]['description']) ? $page_storytellers_by_story_type[0]['description'] : "";

        $temp= isset($page_storytellers_by_story_type[0]['values']) ? $page_storytellers_by_story_type[0]['values'] : array();
        $page_storytellers_by_story_type_data=array();
        $page_storytellers_by_story_type_data=array();
        foreach($temp as $value)
        {
            $date = (array)$value['end_time'];
            $page_storytellers_by_story_type_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
            $page_storytellers_by_story_type_data[$i]['fan'] = $value['value']['fan'];
            $page_storytellers_by_story_type_data[$i]['user post'] = $value['value']['user post'];
            $page_storytellers_by_story_type_data[$i]['page post'] = $value['value']['page post'];
            $page_storytellers_by_story_type_data[$i]['coupon'] = $value['value']['coupon'];
            $page_storytellers_by_story_type_data[$i]['mention'] = $value['value']['mention'];
            $page_storytellers_by_story_type_data[$i]['checkin'] = $value['value']['checkin'];
            $page_storytellers_by_story_type_data[$i]['question'] = $value['value']['question'];
            $page_storytellers_by_story_type_data[$i]['event'] = $value['value']['event'];
            $page_storytellers_by_story_type_data[$i]['other'] = $value['value']['other'];
            $i++;
        }
        $data['page_storytellers_by_story_type_description'] = $page_storytellers_by_story_type_description;
        $data['page_storytellers_by_story_type_data'] = json_encode($page_storytellers_by_story_type_data);



        // $metrics = "insights/page_impressions_by_paid_non_paid/day";
        // $page_impression_paid_vs_organic = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        // $i = 0;
        // $page_impression_paid_vs_organic_description = isset($page_impression_paid_vs_organic[0]['description']) ? $page_impression_paid_vs_organic[0]['description'] : "";

        // $temp2 = isset($page_impression_paid_vs_organic[0]['values']) ? $page_impression_paid_vs_organic[0]['values'] : array();
        // $page_impression_paid_vs_organic_data=array();
        // foreach($temp2 as $value)
        // {
        //     $date = (array)$value['end_time'];
        //     $page_impression_paid_vs_organic_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
        //     $page_impression_paid_vs_organic_data[$i]['paid'] = $value['value']['paid'];
        //     $page_impression_paid_vs_organic_data[$i]['organic'] = $value['value']['unpaid'];
        //     $i++;
        // }
        // $data['page_impression_paid_vs_organic_description'] = $page_impression_paid_vs_organic_description;
        // $data['page_impression_paid_vs_organic_data'] = json_encode($page_impression_paid_vs_organic_data);




        $metrics = "insights/page_impressions_organic/day";
        $page_impressions_organic = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_impression_paid_vs_organic_description = '';
        if(isset($page_impressions_organic[0]['description']))
            $page_impression_paid_vs_organic_description = $page_impressions_organic[0]['description'];

        $temp3 = isset($page_impressions_organic[0]['values']) ? $page_impressions_organic[0]['values'] : array();
        $page_impressions_organic_data=array();
        foreach($temp3 as $value)
        {
            $date = (array)$value['end_time'];
            $page_impressions_organic_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
            $page_impressions_organic_data[$i]['organic'] = $value['value'];
            $i++;
        }
        $data['page_impressions_organic_description'] = $page_impression_paid_vs_organic_description;
        $data['page_impressions_organic_data'] = json_encode($page_impressions_organic_data);





        $metrics = "insights/page_storytellers_by_country/day";
        $page_storytellers_by_country = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        // $page_storytellers_by_country_description = $page_storytellers_by_country[0]['description'];


        $test = isset($page_storytellers_by_country[0]['values']) ? $page_storytellers_by_country[0]['values']:array();
        $page_storytellers_by_country_data = array();
        $page_storytellers_by_country_data_temp = array();
        if(!empty($test)){
            for($i=0;$i<count($test);$i++){
                $aa = isset($test[$i]['value'])? $test[$i]['value']:array();
                foreach(array_keys($aa+$page_storytellers_by_country_data_temp) as $value)
                {
                    $page_storytellers_by_country_data_temp[$value] = (isset($aa[$value]) ? $aa[$value] : 0) + (isset($page_storytellers_by_country_data_temp[$value]) ? $page_storytellers_by_country_data_temp[$value] : 0);
                }
            }
        }

        $country_names = $this->get_country_names();
        $page_storyteller_country_list = '';
        $colors_array = array("#FF8B6B","#D75EF2","#78ED78","#D31319","#798C0E","#FC749F","#D3C421","#1DAF92","#5832BA","#FC5B20","#EDED28","#E27263","#E5C77B","#B7F93B","#A81538", "#087F24","#9040CE","#872904","#DD5D18","#FBFF0F");
        if(!empty($page_storytellers_by_country_data_temp)){
            $i = 0;
            $j = 0;
            foreach($page_storytellers_by_country_data_temp as $key=>$value){
                if($key=='GB') $key='UK';
                $country = isset($country_names[$key])?$country_names[$key]:$key;
                $page_storytellers_by_country_data[$i] = array(
                    'value' => $value,
                    'color' => $colors_array[$j],
                    'highlight' => $colors_array[$j],
                    'label' => $country
                    );
                $page_storyteller_country_list .= '<li><i class="fa fa-circle-o" style="color: '.$colors_array[$j].';"></i> '.$country.' : '.$value.'</li>';
                $i++;
                $j++;
                if($j==19) $j=0;
            }
        }
        $data['page_storytellers_by_country_description'] = $this->lang->line("the number of people talking about the page by user country (unique Users) [last 28 days]");
        $data['page_storyteller_country_list'] = $page_storyteller_country_list;
        $data['page_storytellers_by_country_data'] = json_encode($page_storytellers_by_country_data);





        $metrics = "insights/page_impressions_by_country_unique/day";
        $page_reach_by_user_country = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);

        $test2 = isset($page_reach_by_user_country[0]['values']) ? $page_reach_by_user_country[0]['values']:array();
        $page_reach_by_user_country_data = array();
        $page_reach_by_user_country_data_temp = array();
        if(!empty($test2)){
            for($i=0;$i<count($test2);$i++){
                $aa = isset($test2[$i]['value'])? $test2[$i]['value']:array();
                foreach(array_keys($aa+$page_reach_by_user_country_data_temp) as $value)
                {
                    $page_reach_by_user_country_data_temp[$value] = (isset($aa[$value]) ? $aa[$value] : 0) + (isset($page_reach_by_user_country_data_temp[$value]) ? $page_reach_by_user_country_data_temp[$value] : 0);
                }
            }
        }

        $page_reach_country_list = '';
        $colors_array = array("#FF8B6B","#D75EF2","#78ED78","#D31319","#DD5D18","#FC749F","#FBFF0F","#1DAF92","#A81538", "#087F24","#9040CE","#872904","#798C0E","#D3C421","#5832BA","#FC5B20","#EDED28","#E27263","#E5C77B","#B7F93B");
        $colors_array = array_reverse($colors_array);
        if(!empty($page_reach_by_user_country_data_temp)){
            $i = 0;
            $j = 0;
            foreach($page_reach_by_user_country_data_temp as $key=>$value){
                if($key=='GB') $key='UK';
                $country = isset($country_names[$key])?$country_names[$key]:$key;
                $page_reach_by_user_country_data[$i] = array(
                    'value' => $value,
                    'color' => $colors_array[$j],
                    'highlight' => $colors_array[$j],
                    'label' => $country
                    );
                $page_reach_country_list .= '<li><i class="fa fa-circle-o" style="color: '.$colors_array[$j].';"></i> '.$country.' : '.$value.'</li>';
                $i++;
                $j++;
                if($j==19) $j=0;
            }
        }

        $data['page_reach_by_user_country_description'] = $this->lang->line("total page reach by user country. (unique users) [last 28 days]");
        $data['page_reach_country_list'] = $page_reach_country_list;
        $data['page_reach_by_user_country_data'] = json_encode($page_reach_by_user_country_data);

        $metrics = "insights/page_impressions_by_city_unique/day";
        $page_reach_by_user_city = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);

        $test3 = isset($page_reach_by_user_city[0]['values']) ? $page_reach_by_user_city[0]['values']:array();
        $page_reach_by_user_city_data = '';
        $page_reach_by_user_city_data_temp = array();
        if(!empty($test3)){
            for($i=0;$i<count($test3);$i++){
                $aa = isset($test3[$i]['value'])? $test3[$i]['value']:array();
                foreach(array_keys($aa+$page_reach_by_user_city_data_temp) as $value)
                {
                    $page_reach_by_user_city_data_temp[$value] = (isset($aa[$value]) ? $aa[$value] : 0) + (isset($page_reach_by_user_city_data_temp[$value]) ? $page_reach_by_user_city_data_temp[$value] : 0);
                }
            }
        }
        $page_reach_by_user_city_data = '<table class="table table-hover table-stripped"><tr><th>'.$this->lang->line("sl").'</th><th>'.$this->lang->line("city").'</th><th>'.$this->lang->line("total").'</th></tr>';
        $i = 0;
        if(!empty($page_reach_by_user_city_data_temp)){
            foreach($page_reach_by_user_city_data_temp as $key=>$value){
                $i++;
                $page_reach_by_user_city_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_reach_by_user_city_data .= '</table>';
        $data['page_reach_by_user_city_description'] = $this->lang->line("total page reach by user city. (unique users) [last 28 days]");
        $data['page_reach_by_user_city_data'] = $page_reach_by_user_city_data;




        $metrics = "insights/page_storytellers_by_city/day";
        $page_storyteller_by_user_city = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);

        $test4 = isset($page_storyteller_by_user_city[0]['values']) ? $page_storyteller_by_user_city[0]['values']:array();
        $page_storyteller_by_user_city_data = '';
        $page_storyteller_by_user_city_data_temp = array();
        if(!empty($test4)){
            for($i=0;$i<count($test4);$i++){
                $aa = isset($test4[$i]['value'])? $test4[$i]['value']:array();
                foreach(array_keys($aa+$page_storyteller_by_user_city_data_temp) as $value)
                {
                    $page_storyteller_by_user_city_data_temp[$value] = (isset($aa[$value]) ? $aa[$value] : 0) + (isset($page_storyteller_by_user_city_data_temp[$value]) ? $page_storyteller_by_user_city_data_temp[$value] : 0);
                }
            }
        }
        $page_storyteller_by_user_city_data = '<table class="table table-hover table-striped"><tr><th>'.$this->lang->line("sl").'</th><th>'.$this->lang->line("city").'</th><th>'.$this->lang->line("total").'</th></tr>';
        $i = 0;
        if(!empty($page_storyteller_by_user_city_data_temp)){
            foreach($page_storyteller_by_user_city_data_temp as $key=>$value){
                $i++;
                $page_storyteller_by_user_city_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_storyteller_by_user_city_data .= '</table>';
        $data['page_storyteller_by_user_city_description'] = $this->lang->line("the number of people talking about the page by user city. (unique users) [last 28 days]");
        $data['page_storyteller_by_user_city_data'] = $page_storyteller_by_user_city_data;




        $metrics = "insights/page_engaged_users/day";
        $page_engaged_user = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_engaged_user_description = '';
        $page_engaged_user_data = array();
        if(isset($page_engaged_user[0]['description']))
            $page_engaged_user_description = $page_engaged_user[0]['description'];

        if(isset($page_engaged_user[0]['values']))
        {

            foreach($page_engaged_user[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_engaged_user_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_engaged_user_data[$i]['value'] = $value['value'];
                $i++;
            }
        }
        $data['page_engaged_user_description'] = $page_engaged_user_description;
        $data['page_engaged_user_data'] = json_encode($page_engaged_user_data);




        $metrics = "insights/page_consumptions_by_consumption_type_unique/day";
        $page_consumptions = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_consumptions_description = '';
        $page_consumptions_data = array();
        if(isset($page_consumptions[0]['description']))
            $page_consumptions_description = $page_consumptions[0]['description'];

        if(isset($page_consumptions[0]['values']))
        {

            foreach($page_consumptions[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_consumptions_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_consumptions_data[$i]['other clicks'] = $value['value']['other clicks'];
                $page_consumptions_data[$i]['link clicks'] = $value['value']['link clicks'];
                $page_consumptions_data[$i]['photo view'] = $value['value']['photo view'];
                $page_consumptions_data[$i]['video play'] = $value['value']['video play'];
                $i++;
            }
        }
        $data['page_consumptions_description'] = $page_consumptions_description;
        $data['page_consumptions_data'] = json_encode($page_consumptions_data);




        $metrics = "insights/page_positive_feedback_by_type_unique/day";
        $page_positive_feedback_by_type = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_positive_feedback_by_type_description = '';
        $page_positive_feedback_by_type_data = array();
        if(isset($page_positive_feedback_by_type[0]['description']))
            $page_positive_feedback_by_type_description = $page_positive_feedback_by_type[0]['description'];

        if(isset($page_positive_feedback_by_type[0]['values']))
        {

            foreach($page_positive_feedback_by_type[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_positive_feedback_by_type_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_positive_feedback_by_type_data[$i]['answer'] = $value['value']['answer'];
                $page_positive_feedback_by_type_data[$i]['claim'] = $value['value']['claim'];
                $page_positive_feedback_by_type_data[$i]['comment'] = $value['value']['comment'];
                $page_positive_feedback_by_type_data[$i]['like'] = $value['value']['like'];
                $page_positive_feedback_by_type_data[$i]['link'] = $value['value']['link'];
                $page_positive_feedback_by_type_data[$i]['rsvp'] = isset($value['value']['rsvp']) ? $value['value']['rsvp'] : 0;
                $i++;
            }
        }
        $data['page_positive_feedback_by_type_description'] = $page_positive_feedback_by_type_description;
        $data['page_positive_feedback_by_type_data'] = json_encode($page_positive_feedback_by_type_data);




        $metrics = "insights/page_negative_feedback_by_type/day";
        $page_negative_feedback_by_type = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_negative_feedback_by_type_description = '';
        $page_negative_feedback_by_type_data = array();
        if(isset($page_negative_feedback_by_type[0]['description']))
            $page_negative_feedback_by_type_description = $page_negative_feedback_by_type[0]['description'];

        if(isset($page_negative_feedback_by_type[0]['values']))
        {

            foreach($page_negative_feedback_by_type[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_negative_feedback_by_type_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_negative_feedback_by_type_data[$i]['hide_clicks'] = $value['value']['hide_clicks'];
                $page_negative_feedback_by_type_data[$i]['hide_all_clicks'] = $value['value']['hide_all_clicks'];
                $page_negative_feedback_by_type_data[$i]['report_spam_clicks'] = $value['value']['report_spam_clicks'];
                $page_negative_feedback_by_type_data[$i]['unlike_page_clicks'] = $value['value']['unlike_page_clicks'];
                $page_negative_feedback_by_type_data[$i]['xbutton_clicks'] = $value['value']['xbutton_clicks'];
                $i++;
            }
        }
        $data['page_negative_feedback_by_type_description'] = $page_negative_feedback_by_type_description;
        $data['page_negative_feedback_by_type_data'] = json_encode($page_negative_feedback_by_type_data);



        $metrics = "insights/page_fans_online_per_day/day";
        $page_fans_online_per_day = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_fans_online_per_day_description = $this->lang->line("the number of people who liked your page and who were online on the specified day. (unique users)");
        $page_fans_online_per_day_data = array();

        if(isset($page_fans_online_per_day[0]['values']))
        {

            foreach($page_fans_online_per_day[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_fans_online_per_day_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_fans_online_per_day_data[$i]['value'] = isset($value['value'])?$value['value']:0;
                $i++;
            }
        }
        $data['page_fans_online_per_day_description'] = $page_fans_online_per_day_description;
        $data['page_fans_online_per_day_data'] = json_encode($page_fans_online_per_day_data);





        $metrics = "insights/page_fan_adds/day";
        $page_fan_adds = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $metrics = "insights/page_fan_removes/day";
        $page_fan_removes = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $page_fans_adds_and_remove_data = array();

        if(isset($page_fan_adds[0]['values']) && isset($page_fan_removes[0]['values']))
        {

            $i = 0;
            foreach($page_fan_adds[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_fans_adds_and_remove_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_fans_adds_and_remove_data[$i]['adds'] = $value['value'];
                $i++;
            }

            $j = 0;
            foreach($page_fan_removes[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_fans_adds_and_remove_data[$j]['removes'] = $value['value'];
                $j++;
            }
        }
        $data['page_fans_adds_and_remove_data'] = json_encode($page_fans_adds_and_remove_data);




        $metrics = "insights/page_fans_by_like_source/day";
        $page_fans_by_like_source = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);

        $test2 = isset($page_fans_by_like_source[0]['values']) ? $page_fans_by_like_source[0]['values']:array();

        $page_fans_by_like_source_data = array();
        $page_fans_by_like_source_data_temp = array();
        if(!empty($test2)){
            for($i=0;$i<count($test2);$i++){
                $aa = isset($test2[$i]['value'])? $test2[$i]['value']:array();
                foreach(array_keys($aa+$page_fans_by_like_source_data_temp) as $value)
                {
                    $page_fans_by_like_source_data_temp[$value] = (isset($aa[$value]) ? $aa[$value] : 0) + (isset($page_fans_by_like_source_data_temp[$value]) ? $page_fans_by_like_source_data_temp[$value] : 0);
                }
            }
        }

        $page_fans_by_like_source_list = '';
        $colors_array = array("#FC749F","#D3C421","#1DAF92","#5832BA","#FC5B20","#EDED28","#E27263","#E5C77B","#B7F93B","#A81538", "#087F24","#9040CE","#872904","#DD5D18","#FBFF0F");
        if(!empty($page_fans_by_like_source_data_temp)){
            $i = 0;
            $j = 0;
            foreach($page_fans_by_like_source_data_temp as $key=>$value){
                $key = ucfirst(str_replace('_',' ',$key));
                $page_fans_by_like_source_data[$i] = array(
                    'value' => $value,
                    'color' => $colors_array[$j],
                    'highlight' => $colors_array[$j],
                    'label' => $key
                    );
                $page_fans_by_like_source_list .= '<li><i class="fa fa-circle-o" style="color: '.$colors_array[$j].';"></i> '.$key.' : '.$value.'</li>';
                $i++;
                $j++;
                if($i==10) $j=0;
            }
        }
        $data['page_fans_by_like_source_description'] = $this->lang->line("this is a breakdown of the number of page likes from the most common places where people can like your page. (total count) [last 28 days]");
        $data['page_fans_by_like_source_list'] = $page_fans_by_like_source_list;
        $data['page_fans_by_like_source_data'] = json_encode($page_fans_by_like_source_data);




        $metrics = "insights/page_posts_impressions/day";
        $page_posts_impressions = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_posts_impressions_description = $this->lang->line("daily: the number of impressions that came from all of your posts. (total count)");
        $page_posts_impressions_data = array();

        if(isset($page_posts_impressions[0]['values']))
        {

            foreach($page_posts_impressions[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_posts_impressions_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_posts_impressions_data[$i]['value'] = $value['value'];
                $i++;
            }
        }
        $data['page_posts_impressions_description'] = $page_posts_impressions_description;
        $data['page_posts_impressions_data'] = json_encode($page_posts_impressions_data);



        $metrics = "insights/page_posts_impressions_paid/day";
        $page_posts_impressions_paid = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $metrics = "insights/page_posts_impressions_organic/day";
        $page_posts_impressions_organic = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);
        $page_post_impression_paid_vs_organic_data = array();

        if(isset($page_posts_impressions_paid[0]['values']) && isset($page_posts_impressions_organic[0]['values']))
        {

            $i = 0;
            foreach($page_posts_impressions_paid[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_post_impression_paid_vs_organic_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
                $page_post_impression_paid_vs_organic_data[$i]['paid'] = $value['value'];
                $i++;
            }

            $j = 0;
            foreach($page_posts_impressions_organic[0]['values'] as $value)
            {
                $date = (array)$value['end_time'];
                $page_post_impression_paid_vs_organic_data[$j]['organic'] = $value['value'];
                $j++;
            }
        }
        $data['page_post_impression_pais_vs_organic_description'] = "'".$this->lang->line("paid impression : the number of impressions of your page posts in an ad or sponsored story. (total count)")."' <br/> '".$this->lang->line("organic impression : the number of impressions of your posts in news feed or ticker or on your page. (total count)")."'";
        $data['page_post_impression_paid_vs_organic_data'] = json_encode($page_post_impression_paid_vs_organic_data);




        $metrics = "insights/page_tab_views_login_top_unique/day";
        $page_tab_views = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);

        $test5 = isset($page_tab_views[0]['values']) ? $page_tab_views[0]['values']:array();
        $page_tab_views_data = '';
        $page_tab_views_data_temp = array();
        if(!empty($test5)){
            for($i=0;$i<count($test5);$i++){
                $aa =isset($test5[$i]['value'])?$test5[$i]['value']:array();
                foreach(array_keys($aa+$page_tab_views_data_temp) as $value)
                {
                    $page_tab_views_data_temp[$value] = (isset($aa[$value]) ? $aa[$value] : 0) + (isset($page_tab_views_data_temp[$value]) ? $page_tab_views_data_temp[$value] : 0);
                }
            }
        }
        $page_tab_views_data = '<table class="table table-hover table-striped"><tr><th>'.$this->lang->line("sl").'</th><th>'.$this->lang->line("tab").'</th><th>'.$this->lang->line("views").'</th></tr>';
        $i = 0;
        if(!empty($page_tab_views_data_temp)){
            foreach($page_tab_views_data_temp as $key=>$value){
                $i++;
                $page_tab_views_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_tab_views_data .= '</table>';
        $data['page_tab_views_description'] = $this->lang->line("tabs on your page that were viewed when logged-in users visited your page. (unique users) [last 28 days]");
        $data['page_tab_views_data'] = $page_tab_views_data;




        $metrics = "insights/page_views_external_referrals/day";
        $page_views_external_referrals = $this->fb_rx_login->get_page_insight_info($access_token,$metrics,$page_id);

        $test6 = isset($page_views_external_referrals[0]['values']) ? $page_views_external_referrals[0]['values']:array();
        $page_views_external_referrals_data = '';
        $page_views_external_referrals_data_temp = array();
        if(!empty($test6)){
            for($i=0;$i<count($test6);$i++){
                $aa = isset($test6[$i]['value']) ?$test6[$i]['value']:array();
                foreach(array_keys($aa+$page_views_external_referrals_data_temp) as $value)
                {
                    $page_views_external_referrals_data_temp[$value] = (isset($aa[$value]) ? $aa[$value] : 0) + (isset($page_views_external_referrals_data_temp[$value]) ? $page_views_external_referrals_data_temp[$value] : 0);
                }
            }
        }
        $page_views_external_referrals_data = '<table class="table table-hover table-striped"><tr><th>'.$this->lang->line("sl").'</th><th>'.$this->lang->line("referrar").'</th><th>'.$this->lang->line("views").'</th></tr>';
        $i = 0;
        if(!empty($page_views_external_referrals_data_temp)){
            foreach($page_views_external_referrals_data_temp as $key=>$value){
                $i++;
                $page_views_external_referrals_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_views_external_referrals_data .= '</table>';
        $data['page_views_external_referrals_description'] = $this->lang->line("top referring external domains sending traffic to your page (total count) [last 28 days]");
        $data['page_views_external_referrals_data'] = $page_views_external_referrals_data;




        $data['body'] = "facebook_rx/insight/page_statistics";
        $data['page_title'] = $this->lang->line('page statistics');
        $this->_viewcontroller($data);
    }






}

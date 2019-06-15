<?php 

require_once("Home.php"); // including home controller

/**
* class admin_config_facebook
* @category controller
*/
class Admin_facebook_page_insight extends Home
{

    /**
    * load constructor method
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }

        $this->load->library('facebook_page_insight');

        if($this->session->userdata('user_type') != 'Admin' && !in_array(24,$this->module_access))
        redirect('home/login_page', 'location'); 

        $this->important_feature();
        
        if($this->session->userdata('user_type') == 'Admin')
            $this->periodic_check();
    }

    /**
    * load index method. redirect to facebook_config
    * @access public
    * @return void
    */
    public function index()
    {
        $this->page_list_grid();
    }
   

     public function page_insight_login()

    {

        $redirect_url = base_url('admin_facebook_page_insight/get_access_token');
        $data['login_button'] = $this->facebook_page_insight->login_button($redirect_url);
        $data['body'] = "facebook_page_insight/login_button_page";
        $data['page_title'] = $this->lang->line('facebook page insight');
        $this->_viewcontroller($data);

    }


    public function get_access_token(){
        $this->facebook_page_insight->login_callback();
        //echo $this->session->userdata('fb_page_insight_access_token');
        
        $page_list=$this->facebook_page_insight->get_page_list();

        $where = array('user_id'=>$this->user_id);
        $this->basic->delete_data('facebook_page_insight_page_list',$where);

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

            $data = array(
                'user_id' => $user_id,
                'page_id' => $page_id,
                'page_cover' => $page_cover,
                'page_profile' => $page_profile,
                'page_name' => $page_name,
                'page_access_token' => $page_access_token,
                'page_email' => $page_email,
                );
            $this->basic->insert_data("facebook_page_insight_page_list",$data);
        }
        
    
        $this->session->set_userdata("page_insight_login_success",1);
        $data['body'] = "facebook_page_insight/page_list_grid";
        $data['page_title'] = $this->lang->line('facebook page insight');
        $this->_viewcontroller($data);
        
    }


    public function page_list_grid()
    {
        $data['body'] = "facebook_page_insight/page_list_grid";
        $data['page_title'] = $this->lang->line('page list');
        $this->_viewcontroller($data);
    }

    public function page_list_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $name = trim($this->input->post("name", true));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('page_insight_page_name', $name);
        }

        // saving session data to different search parameter variables
        $search_name   = $this->session->userdata('page_insight_page_name');;

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_name)    $where_simple['page_name like '] = "%".$search_name."%";

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "facebook_page_insight_page_list";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        
        $i = 0;
        foreach($info as $results){
            $result[$i]['id'] = $results['id'];
            $result[$i]['page_name'] = $results['page_name'];
            $result[$i]['page_id'] = $results['page_id'];
            $result[$i]['page_email'] = $results['page_email'];
            $result[$i]['details'] = "<a class='label label-warning' href='".base_url()."admin_facebook_page_insight/page_details/".$results['id']."'><i class='fa fa-binoculars'></i> ".$this->lang->line('details')."</a>";
            $i++;
        }

        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($result, $total_result);
    }



    public function page_details($id=0)
    {
        if($id==0) exit();
        $where['where'] = array('id'=>$id,'user_id'=>$this->user_id);
        $page_info = $this->basic->get_data('facebook_page_insight_page_list',$where);
        $data['cover_image'] = $page_info[0]['page_cover'];
        $data['profile_image'] = $page_info[0]['page_profile'];

        $access_token = $page_info[0]['page_access_token'];
        $metrics = "insights/page_storytellers_by_story_type/day";
        $page_id = $page_info[0]['page_id'];
        $page_storytellers_by_story_type = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);

        $i = 0;
        $page_storytellers_by_story_type_description = $page_storytellers_by_story_type[0]['description'];
        foreach($page_storytellers_by_story_type[0]['values'] as $value)
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



        $metrics = "insights/page_impressions_by_paid_non_paid/day";
        $page_impression_paid_vs_organic = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_impression_paid_vs_organic_description = $page_impression_paid_vs_organic[0]['description'];
        foreach($page_impression_paid_vs_organic[0]['values'] as $value)
        {
            $date = (array)$value['end_time'];
            $page_impression_paid_vs_organic_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
            $page_impression_paid_vs_organic_data[$i]['paid'] = $value['value']['paid'];
            $page_impression_paid_vs_organic_data[$i]['organic'] = $value['value']['unpaid'];
            $i++;
        }
        $data['page_impression_paid_vs_organic_description'] = $page_impression_paid_vs_organic_description;
        $data['page_impression_paid_vs_organic_data'] = json_encode($page_impression_paid_vs_organic_data);




        $metrics = "insights/page_impressions_organic/day";
        $page_impressions_organic = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_impression_paid_vs_organic_description = '';
        if(isset($page_impressions_organic[0]['description']))
            $page_impression_paid_vs_organic_description = $page_impressions_organic[0]['description'];
        foreach($page_impressions_organic[0]['values'] as $value)
        {
            $date = (array)$value['end_time'];
            $page_impressions_organic_data[$i]['date'] = date("Y-m-d",strtotime($date['date']));
            $page_impressions_organic_data[$i]['organic'] = $value['value'];
            $i++;
        }
        $data['page_impressions_organic_description'] = $page_impression_paid_vs_organic_description;
        $data['page_impressions_organic_data'] = json_encode($page_impressions_organic_data);





        $metrics = "insights/page_storytellers_by_country/day";
        $page_storytellers_by_country = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
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
        $data['page_storytellers_by_country_description'] = "The Number of People Talking About the Page by User Country (Unique Users) [Last 28 days]";
        $data['page_storyteller_country_list'] = $page_storyteller_country_list;
        $data['page_storytellers_by_country_data'] = json_encode($page_storytellers_by_country_data);





        $metrics = "insights/page_impressions_by_country_unique/day";
        $page_reach_by_user_country = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);

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
        $data['page_reach_by_user_country_description'] = "Total Page Reach by user country. (Unique Users) [Last 28 days]";
        $data['page_reach_country_list'] = $page_reach_country_list;
        $data['page_reach_by_user_country_data'] = json_encode($page_reach_by_user_country_data);





        $metrics = "insights/page_impressions_by_city_unique/day";
        $page_reach_by_user_city = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);

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
        $page_reach_by_user_city_data = '<table class="table table-hover table-striped"><tr><th>SL</th><th>City</th><th>Total</th></tr>';
        $i = 0;
        if(!empty($page_reach_by_user_city_data_temp)){
            foreach($page_reach_by_user_city_data_temp as $key=>$value){
                $i++;
                $page_reach_by_user_city_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_reach_by_user_city_data .= '</table>';
        $data['page_reach_by_user_city_description'] = "Total Page Reach by user city. (Unique Users) [Last 28 days]";
        $data['page_reach_by_user_city_data'] = $page_reach_by_user_city_data;




        $metrics = "insights/page_storytellers_by_city/day";
        $page_storyteller_by_user_city = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);

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
        $page_storyteller_by_user_city_data = '<table class="table table-hover table-striped"><tr><th>SL</th><th>City</th><th>Total</th></tr>';
        $i = 0;
        if(!empty($page_storyteller_by_user_city_data_temp)){
            foreach($page_storyteller_by_user_city_data_temp as $key=>$value){
                $i++;
                $page_storyteller_by_user_city_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_storyteller_by_user_city_data .= '</table>';
        $data['page_storyteller_by_user_city_description'] = "The number of People Talking About the Page by user city. (Unique Users) [Last 28 Days]";
        $data['page_storyteller_by_user_city_data'] = $page_storyteller_by_user_city_data;




        $metrics = "insights/page_engaged_users/day";
        $page_engaged_user = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
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
        $page_consumptions = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
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
        $page_positive_feedback_by_type = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
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
        $page_negative_feedback_by_type = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
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
        $page_fans_online_per_day = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_fans_online_per_day_description = 'The number of people who liked your Page and who were online on the specified day. (Unique Users)';
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
        $page_fan_adds = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
        $metrics = "insights/page_fan_removes/day";
        $page_fan_removes = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
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
        $page_fans_by_like_source = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);

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
        $data['page_fans_by_like_source_description'] = "This is a breakdown of the number of Page likes from the most common places where people can like your Page. (Total Count) [Last 28 days]";
        $data['page_fans_by_like_source_list'] = $page_fans_by_like_source_list;
        $data['page_fans_by_like_source_data'] = json_encode($page_fans_by_like_source_data);




        $metrics = "insights/page_posts_impressions/day";
        $page_posts_impressions = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
        $i = 0;
        $page_posts_impressions_description = 'Daily: The number of impressions that came from all of your posts. (Total Count)';
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
        $page_posts_impressions_paid = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
        $metrics = "insights/page_posts_impressions_organic/day";
        $page_posts_impressions_organic = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);
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
        $data['page_post_impression_pais_vs_organic_description'] = "Paid Impression : The number of impressions of your Page posts in an Ad or Sponsored Story. (Total Count) <br/> Organic Impression : The number of impressions of your posts in News Feed or ticker or on your Page. (Total Count)";
        $data['page_post_impression_paid_vs_organic_data'] = json_encode($page_post_impression_paid_vs_organic_data);




        $metrics = "insights/page_tab_views_login_top_unique/day";
        $page_tab_views = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);

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
        $page_tab_views_data = '<table class="table table-hover table-striped"><tr><th>SL</th><th>Tab</th><th>Views</th></tr>';
        $i = 0;
        if(!empty($page_tab_views_data_temp)){
            foreach($page_tab_views_data_temp as $key=>$value){
                $i++;
                $page_tab_views_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_tab_views_data .= '</table>';
        $data['page_tab_views_description'] = "Tabs on your Page that were viewed when logged-in users visited your Page. (Unique Users) [Last 28 Days]";
        $data['page_tab_views_data'] = $page_tab_views_data;




        $metrics = "insights/page_views_external_referrals/day";
        $page_views_external_referrals = $this->facebook_page_insight->get_page_insight_info($access_token,$metrics,$page_id);

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
        $page_views_external_referrals_data = '<table class="table table-hover table-striped"><tr><th>SL</th><th>Referrar</th><th>Views</th></tr>';
        $i = 0;
        if(!empty($page_views_external_referrals_data_temp)){
            foreach($page_views_external_referrals_data_temp as $key=>$value){
                $i++;
                $page_views_external_referrals_data .= '<tr><td>'.$i.'</td><td>'.$key.'</td><td>'.$value.'</td></tr>';
            }
        }
        $page_views_external_referrals_data .= '</table>';
        $data['page_views_external_referrals_description'] = "Top referring external domains sending traffic to your Page (Total Count) [Last 28 Days]";
        $data['page_views_external_referrals_data'] = $page_views_external_referrals_data;

  

        $data['body'] = "facebook_page_insight/page_statistics";
        $data['page_title'] = $this->lang->line('page statistics');
        $this->_viewcontroller($data);

    }






}

<?php
require_once("Home.php"); // including home controller

class Ip extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');       
 
        $this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

        $this->important_feature();

        $this->member_validity();

        if($this->session->userdata('user_type') != 'Admin' && !in_array(6,$this->module_access))
        redirect('home/login_page', 'location'); 
    }
	
	
	public function index()
    {
        $this->my_ip_address();
    }

    public function my_ip_address()
    {
        $this->load->library('Web_common_report');
        $data['body'] = 'admin/ip/my_ip_info';
        $data['page_title'] = $this->lang->line("my ip information");
        $data["my_ip"]=$this->real_ip();
        $data["google_api"]=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));
        $data["ip_info"]=$this->web_common_report->ip_info($data["my_ip"]);
        $this->_viewcontroller($data);
    }





    public function domain_info()
    {
        $data['body'] = 'admin/ip/domain_info';
        $data['page_title'] = $this->lang->line("domain ip information");
        $this->_viewcontroller($data);
    }
    

    public function domain_info_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name = trim($this->input->post("domain_name", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('domain_info_domain_name', $domain_name);
            $this->session->set_userdata('domain_info_from_date', $from_date);
            $this->session->set_userdata('domain_info_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('domain_info_domain_name');
        $search_from_date  = $this->session->userdata('domain_info_from_date');
        $search_to_date = $this->session->userdata('domain_info_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "ip_domain_info";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function domain_info_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);

        //************************************************//
        $status=$this->_check_usage($module_id=6,$request=count($url_array));
        if($status=="2") 
        {
            echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        else if($status=="3") 
        {
            echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        //************************************************//
        
      
        $this->session->set_userdata('domain_info_bulk_total_search',count($url_array));
        $this->session->set_userdata('domain_info_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/ip/domain_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
          
        /**Write header in csv file***/

        $write_data=array();            
        $write_data[]="Domain";            
        $write_data[]="ISP";            
        $write_data[]="IP";            
        $write_data[]="Country";            
        $write_data[]="Region";            
        $write_data[]="City";            
        $write_data[]="Time Zone";            
        $write_data[]="Latitude";            
        $write_data[]="Longitude";            
                                        
        
        fputcsv($download_path, $write_data);
        
        $domain_info_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>ISP</td>
            <td>IP</td>
            <td>Country</td>
            <td>Region</td>
            <td>City</td>
            <td>Time Zone</td>
            <td>Latitude</td>
            <td>Longitude</td>
            <td>BlackList</td>
            <td>TraceOut</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            /***Remove all www. http:// and https:// ****/            
            $domain=str_replace("www.","",$domain);
            $domain=str_replace("http://","",$domain);
            $domain=str_replace("https://","",$domain);

            $domain_data=array();
            $domain_data=$this->web_common_report->get_ip_country($domain,$proxy="");  
            $searched_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
     
            $write_data[]=$domain;
            $write_data[]=$domain_data["isp"];
            $write_data[]=$domain_data["ip"];
            $write_data[]=$domain_data["country"];
            $write_data[]=$domain_data["region"];
            $write_data[]=$domain_data["city"];
            $write_data[]=$domain_data["time_zone"];
            $write_data[]=$domain_data["latitude"];
            $write_data[]=$domain_data["longitude"];
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'ip'                => $domain_data["ip"],
                'isp'               => $domain_data["isp"],
                'country'           => $domain_data["country"],
                'region'            => $domain_data["region"],
                'city'              => $domain_data["city"],
                'time_zone'         => $domain_data["time_zone"],
                'latitude'          => $domain_data["latitude"],
                'longitude'         => $domain_data["longitude"],
                'searched_at'       => $searched_at,
            );

            $count++;
			
			
			$blacklist_url=base_url()."ip/balcklist_check/".$domain_data['ip'];
			$traceout_url=base_url()."ip/ip_traceout/".$domain_data['ip'];
			
            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$domain."</td>
            <td>".$domain_data["isp"]."</td>
            <td>".$domain_data["ip"]."</td>
            <td>".$domain_data["country"]."</td>
            <td>".$domain_data["region"]."</td>
            <td>".$domain_data["city"]."</td>
            <td>".$domain_data["time_zone"]."</td>
            <td>".$domain_data["latitude"]."</td>
            <td>".$domain_data["longitude"]."</td>
            <td><a href='{$blacklist_url}'>Blacklist</a></td>
            <td><a href='{$traceout_url}'>Tracout</a></td>
            </tr>";
            
            $this->basic->insert_data('ip_domain_info', $insert_data);    
            $domain_info_complete++;
            $this->session->set_userdata("domain_info_complete_search",$domain_info_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=6,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

  

    public function domain_info_download()
    {
        $all=$this->input->post("all");
        $table = 'ip_domain_info';
        $where=array();
        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }
            $where['where_in'] = array('id' => $id_array);
        }

        $where['where'] = array('user_id'=>$this->user_id);

        $info = $this->basic->get_data($table, $where, $select ='', $join='', $limit='', $start=null, $order_by='id asc');
        $download_id=$this->session->userdata('download_id');
        $fp = fopen("download/ip/domain_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        

        $write_data=array();            
        $write_data[]="Domain";            
        $write_data[]="ISP";            
        $write_data[]="IP";            
        $write_data[]="Country";            
        $write_data[]="Region";            
        $write_data[]="City";            
        $write_data[]="Time Zone";            
        $write_data[]="Latitude";            
        $write_data[]="Longitude";  
        $write_data[]="Searched at";  
                    
        fputcsv($fp, $write_data);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
                $write_data=array();            
                $write_data[]=$value["domain_name"];            
                $write_data[]=$value["isp"];            
                $write_data[]=$value["ip"];            
                $write_data[]=$value["country"];   
                $write_data[]=$value["region"];   
                $write_data[]=$value["city"];     
                $write_data[]=$value["time_zone"];            
                $write_data[]=$value["latitude"];          
                $write_data[]=$value["longitude"];   
                $write_data[]=$value["searched_at"];   
            
                fputcsv($fp, $write_data);
        }

        fclose($fp);
        $file_name = "download/ip/domain_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function domain_info_delete()
    {
        $all=$this->input->post("all");

        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }     
            $this->db->where_in('id', $id_array);
        }
        $this->db->where('user_id', $this->user_id);
        $this->db->delete('ip_domain_info');
    }
   
    public function bulk_domain_info_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('domain_info_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('domain_info_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }




    public function site_this_ip()
    {
        $data['body'] = 'admin/ip/site_this_ip';
        $data['page_title'] = $this->lang->line("sites in same ip");
        $this->_viewcontroller($data);
    }
    

    public function site_this_ip_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name = trim($this->input->post("domain_name", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('site_this_ip_domain_name', $domain_name);
            $this->session->set_userdata('site_this_ip_from_date', $from_date);
            $this->session->set_userdata('site_this_ip_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('site_this_ip_domain_name');
        $search_from_date  = $this->session->userdata('site_this_ip_from_date');
        $search_to_date = $this->session->userdata('site_this_ip_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['ip like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "ip_same_site";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function site_this_ip_action()
    {
        //************************************************//
        $status=$this->_check_usage($module_id=6,$request=1);
        if($status=="2") 
        {
            echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        else if($status=="3") 
        {
            echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        //************************************************//

        $this->load->library('web_common_report');
        $ip=$this->input->post('urls', true);
       
       
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/ip/same_site_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
     
        
        /**Write header in csv file***/

        $write_data=array();              
        $write_data[]="IP";            
        $write_data[]="Website";            
                                        
        
        fputcsv($download_path, $write_data);
        
        $site_this_ip_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <caption><h3 class='text-center'>Sites In IP : ".$ip."</h3></caption>
            <tr>
            <td>SL</td>
            <td>Website</td>          
            </tr>";
  
        $same_site_data=array();
        $this->web_common_report->get_site_in_same_ip($ip,$page=1,$proxy="");  
        $same_site_data=$this->web_common_report->same_site_in_ip;  
        $this->session->set_userdata('site_this_ip_complete_search',0);
        $this->session->set_userdata('site_this_ip_bulk_total_search',count($same_site_data));
        $searched_at= date("Y-m-d H:i:s");
               
       
       foreach ($same_site_data as $key => $value) 
       {
            $count++;
            $site_linkable="<a target='_BLANL' title='Visit Now' href='".addHttp($value)."'>".$value."</a>";

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$site_linkable."</td>
            </tr>";

            $write_data=array(); 
            $write_data[]=$ip;
            $write_data[]=$value;
            fputcsv($download_path, $write_data);

            $site_this_ip_complete++;
            $this->session->set_userdata("site_this_ip_complete_search",$site_this_ip_complete);  
       }

       if(count($same_site_data)==0) $str.="<tr><td colspan='2'>No data found!</td></tr>";
        
         /** Insert into database ***/
        $insert_data=array
        (
            'user_id'           => $this->user_id,
            'ip'                => $ip,
            'website'           => json_encode($same_site_data),
            'searched_at'       => $searched_at
        );
       
       //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=6,$request=1);   
        //******************************//
        
       if($this->basic->insert_data('ip_same_site', $insert_data))
       echo $str.="</table>";

    }

  

    public function site_this_ip_download()
    {
        $all=$this->input->post("all");
        $table = 'ip_same_site';
        $where=array();
        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }
            $where['where_in'] = array('id' => $id_array);
        }

        $where['where'] = array('user_id'=>$this->user_id);

        $info = $this->basic->get_data($table, $where, $select ='', $join='', $limit='', $start=null, $order_by='id asc');
        $download_id=$this->session->userdata('download_id');
        $fp = fopen("download/ip/same_site_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        
        $write_data=array();            
        $write_data[]="IP";            
        $write_data[]="Website"; 
        $write_data[]="Searched at";  
                    
        fputcsv($fp, $write_data);
        $write_info = array();

        foreach ($info as  $value) 
        {
            $website_array=json_decode($value["website"],true);
            foreach ($website_array as $row) 
            {
                $write_data=array();            
                $write_data[]=$value["ip"];    
                $write_data[]=$row;    
                $write_data[]=$value["searched_at"];    
            
                fputcsv($fp, $write_data);
            }                
        }
        fclose($fp);
        $file_name = "download/ip/same_site_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }

    

    public function site_this_ip_delete()
    {
        $all=$this->input->post("all");

        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }     
            $this->db->where_in('id', $id_array);
        }
        $this->db->where('user_id', $this->user_id);
        $this->db->delete('ip_same_site');
    }

   
    public function bulk_site_this_ip_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('site_this_ip_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('site_this_ip_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }


     public function ipv6_check()
    {
        $data['body'] = 'admin/ip/ipv6_check';
        $data['page_title'] = $this->lang->line("ipv6 compability check");
        $this->_viewcontroller($data);
    }
    

    public function ipv6_check_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';

        $domain_name = trim($this->input->post("domain_name", true));

        $from_date = trim($this->input->post('from_date', true));        
        if($from_date) $from_date = date('Y-m-d', strtotime($from_date));

        $to_date = trim($this->input->post('to_date', true));
        if($to_date) $to_date = date('Y-m-d', strtotime($to_date));


        // setting a new properties for $is_searched to set session if search occured
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) 
        {
            // if search occured, saving user input data to session. name of method is important before field
            $this->session->set_userdata('ipv6_check_domain_name', $domain_name);
            $this->session->set_userdata('ipv6_check_from_date', $from_date);
            $this->session->set_userdata('ipv6_check_to_date', $to_date);
        }

        // saving session data to different search parameter variables
        $search_domain_name   = $this->session->userdata('ipv6_check_domain_name');
        $search_from_date  = $this->session->userdata('ipv6_check_from_date');
        $search_to_date = $this->session->userdata('ipv6_check_to_date');

        // creating a blank where_simple array
        $where_simple=array();
        
        if ($search_domain_name)    $where_simple['domain_name like ']    = "%".$search_domain_name."%";
        if ($search_from_date) 
        {
            if ($search_from_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') >="]= $search_from_date;
            
        }
        if ($search_to_date) 
        {
            if ($search_to_date != '1970-01-01') 
            $where_simple["Date_Format(searched_at,'%Y-%m-%d') <="]=$search_to_date;
            
        }

        $where_simple['user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $table = "ip_v6_check";
        $info = $this->basic->get_data($table, $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
        $total_rows_array = $this->basic->count_row($table, $where, $count="id", $join='');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function ipv6_check_action()
    {
        $this->load->library('web_common_report');
        $urls=$this->input->post('urls', true);
       
        $urls=str_replace("\n", ",", $urls);
        $url_array=explode(",", $urls);
        $url_array=array_filter($url_array);
        $url_array=array_unique($url_array);

        //************************************************//
        $status=$this->_check_usage($module_id=6,$request=count($url_array));
        if($status=="2") 
        {
            echo $this->lang->line("sorry, your bulk limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        else if($status=="3") 
        {
            echo $this->lang->line("sorry, your monthly limit is exceeded for this module.")."<a href='".site_url('usage_history')."'>".$this->lang->line("click here to see usage log")."</a>";
            exit();
        }
        //************************************************//
        
      
        $this->session->set_userdata('ipv6_check_bulk_total_search',count($url_array));
        $this->session->set_userdata('ipv6_check_complete_search',0);
        $download_id= $this->session->userdata('download_id');
        
        $download_path=fopen("download/ip/ipv6_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($download_path, chr(0xEF).chr(0xBB).chr(0xBF));
          
        /**Write header in csv file***/

        $write_data=array();            
        $write_data[]="Domain";          
        $write_data[]="IP";            
        $write_data[]="IPv6";            
        $write_data[]="Searched at";            
                                        
        
        fputcsv($download_path, $write_data);
        
        $ipv6_check_complete=0;

        $count=0;
        $str=
            "<table class='table table-bordered table-hover table-striped'>
            <tr>
            <td>SL</td>
            <td>Domain</td>
            <td>IP</td>
            <td>IPv6</td>
            </tr>";
        foreach ($url_array as $domain) 
        {        
            $domain_data=array();
            $domain_data=$this->web_common_report->ipv6_check($domain);  
            $searched_at= date("Y-m-d H:i:s");
                  
            $write_data=array();
     
            $write_data[]=$domain;
            $write_data[]=$domain_data["ip"];

            if($domain_data["is_ipv6_support"]=="1")
            $ipv6=$domain_data["ipv6"];
            else $ipv6="Not Compatible";
            $write_data[]=$ipv6;

            $write_data[]=$searched_at;
            fputcsv($download_path, $write_data);
            
            /** Insert into database ***/
   
            $insert_data=array
            (
                'user_id'           => $this->user_id,
                'domain_name'       => $domain,
                'ip'                => $domain_data["ip"],
                'ipv6'              => $domain_data["ipv6"],
                'is_ipv6_support'   => $domain_data["is_ipv6_support"],
                'searched_at'       => $searched_at
            );

            $count++;

           

            $str.=
            "<tr>
            <td>".$count."</td>
            <td>".$domain."</td>
            <td>".$domain_data["ip"]."</td>
            <td>".$ipv6."</td>
            </tr>";
            
            $this->basic->insert_data('ip_v6_check', $insert_data);    
            $ipv6_check_complete++;
            $this->session->set_userdata("ipv6_check_complete_search",$ipv6_check_complete);        
        }

        //******************************//
        // insert data to useges log table
        $this->_insert_usage_log($module_id=6,$request=count($url_array));   
        //******************************//

        echo $str.="</table>";

    }

  

    public function ipv6_check_download()
    {
        $all=$this->input->post("all");
        $table = 'ip_v6_check';
        $where=array();
        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }
            $where['where_in'] = array('id' => $id_array);
        }

        $where['where'] = array('user_id'=>$this->user_id);

        $info = $this->basic->get_data($table, $where, $select ='', $join='', $limit='', $start=null, $order_by='id asc');
        $download_id=$this->session->userdata('download_id');
        $fp = fopen("download/ip/ipv6_{$this->user_id}_{$download_id}.csv", "w");
        // make output csv file unicode compatible
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        

        $write_data=array();            
        $write_data[]="Domain";          
        $write_data[]="IP";            
        $write_data[]="IPv6";            
        $write_data[]="Searched at";   
                    
        fputcsv($fp, $write_data);
        $write_info = array();

        foreach ($info as  $value) 
        {
         
                if($value["is_ipv6_support"]=="1")
                $ipv6=$value["ipv6"];
                else $ipv6="Not Compatible";

                $write_data=array();            
                $write_data[]=$value["domain_name"];      
                $write_data[]=$value["ip"];            
                $write_data[]=$ipv6;    
                $write_data[]=$value["searched_at"];   
            
                fputcsv($fp, $write_data);
        }

        fclose($fp);
        $file_name = "download/ip/ipv6_{$this->user_id}_{$download_id}.csv";
        echo $file_name;
    }


    

    public function ipv6_check_delete()
    {
        $all=$this->input->post("all");

        if($all==0)
        {
            $selected_grid_data = $this->input->post('info', true);
            $json_decode = json_decode($selected_grid_data, true);
            $id_array = array();
            foreach ($json_decode as  $value) 
            {
                $id_array[] = $value['id'];
            }     
            $this->db->where_in('id', $id_array);
        }
        $this->db->where('user_id', $this->user_id);
        $this->db->delete('ip_v6_check');
    }
   
    public function bulk_ipv6_check_progress_count()
    {
        $bulk_tracking_total_search=$this->session->userdata('ipv6_check_bulk_total_search'); 
        $bulk_complete_search=$this->session->userdata('ipv6_check_complete_search'); 
        
        $response['search_complete']=$bulk_complete_search;
        $response['search_total']=$bulk_tracking_total_search;
        
        echo json_encode($response);
        
    }



    public function balcklist_check($ip="")
    {
        $data['body'] = "admin/ip/blacklist_check";
        $data['page_title'] = $this->lang->line("Blacklist Checker");
		$data['check_ip']=$ip;
        $this->_viewcontroller($data);
    }


    public function blacklist_check_data()
    {
        $this->load->library('Web_common_report');
        $ip_address = $this->input->post('ip_address');
        $blacklist_details = $this->web_common_report->check_black_list($ip_address);

        $str = '<table class="table table-bordered table-hover">
                    <tr><th>Site</th><th>Status</th></tr>';
        foreach($blacklist_details as $value)
        {
            $str .= '<tr><td>'.$value['domain'].'</td><td><img src="'.$value['status_image_link'].'" /></td></tr>';
        }

        $str .= '</table>';

        $data["ip_info"]=$this->web_common_report->ip_info($ip_address);
        $data["my_ip"]=$ip_address;
        $data["google_api"]=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));
        $str.=$this->load->view("admin/ip/ip_info",$data);

        echo $str;


    }


     public function ip_traceout($ip="")
    {
		
        $data['body'] = "admin/ip/ip_traceout";
        $data['page_title'] = $this->lang->line("IP Traceout");
		$data['check_ip']=$ip;
        $this->_viewcontroller($data);
    }


    public function traceout_check_data()
    {
        $this->load->library('Web_common_report');
        $ip_address = $this->input->post('ip_address');
        $blacklist_details = $this->web_common_report->ip_traceout($ip_address);

        $str = '<table class="table table-bordered table-hover">
                    <tr><th>Hop</th><th>Time</th><th>Host</th><th>IP</th><th>Location</th></tr>';
        unset($blacklist_details[0]);
        foreach($blacklist_details as $value)
        {
            $str .= "<tr><td>{$value['hop']}</td><td>{$value['time']}</td><td>{$value['host']}</td><td>{$value['ip']}</td><td>{$value['location']}</td></tr>";
        }

        $str .= '</table>';

        
        $data["ip_info"]=$this->web_common_report->ip_info($ip_address);
        $data["my_ip"]=$ip_address;
        $data["google_api"]=$this->basic->get_data("config",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));
        $str.=$this->load->view("admin/ip/ip_info",$data);

        echo $str;


    }






	
	
}
?>
<?php

require_once("Home.php"); // loading home controller

class facebook_ex_import_lead extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(76,$this->module_access))
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
      $this->import_lead();
    }
  
    public function import_lead()
    {
        $data['body'] = 'facebook_ex/import_lead';
        $data['page_title'] = $this->lang->line('Import Lead');  
        $facebook_rx_fb_user_info_id  =  $this->session->userdata('facebook_rx_fb_user_info');

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('facebook_rx_fb_user_info_id' => $facebook_rx_fb_user_info_id);
        $page_info = $this->basic->get_data($table_name,$where,'','','','','page_name asc');
        
        $len_page_info = count($page_info); 
        $data['page_info'] = $page_info;        
        $this->_viewcontroller($data);
    }

    public function import_lead_action(){

        $facebook_rx_fb_page_info_id = $this->input->post('id');
        $scan_limit = $this->input->post('scan_limit');

        $table_name = "facebook_rx_fb_page_info";
        $where['where'] = array('id' => $facebook_rx_fb_page_info_id);
        $facebook_rx_fb_page_info = $this->basic->get_data($table_name,$where);
        $get_concersation_info = $this->fb_rx_login->get_all_conversation_page($facebook_rx_fb_page_info[0]['page_access_token'],$facebook_rx_fb_page_info[0]['page_id'],0,$scan_limit);

        if(isset($get_concersation_info['error'])){

            $response =array();
            $response["message"] = "<i class='fa fa-warning' style='color:red'></i> Error: ".$get_concersation_info['error_msg'];
            $response["status"] = '1';
            $response["count"] = 0;
            echo json_encode($response);
            exit; 
        }

        $success = 0;
        $total=0;

        $facebook_rx_fb_user_info_id = $facebook_rx_fb_page_info[0]['facebook_rx_fb_user_info_id']; 
        $db_page_id =  $facebook_rx_fb_page_info[0]['page_id'];
        $db_user_id =  $facebook_rx_fb_page_info[0]['user_id'];

        foreach($get_concersation_info as &$item) 
        {           
            $db_client_id  =  isset($item['id']) ? $item['id'] : "";
            $db_client_thread_id  =  isset($item['thead_id']) ? $item['thead_id']: "" ;

            $lead_name= isset($item['name']) ? $item['name']: "" ;

            if($db_client_id=="") continue;     

            $insert_name=0;


            if($lead_name != 'Facebook User')
                $insert_name=1;

            $db_client_name  =  $this->db->escape($lead_name);
            $link = isset($item['link']) ? $item['link']: "" ;

            $db_permission  =  '1';

            $subscribed_at = date("Y-m-d H:i:s");
            if($insert_name)
            {                
                $this->basic->execute_complex_query("INSERT IGNORE INTO facebook_rx_conversion_user_list(page_table_id,page_id,user_id,client_thread_id,client_id,client_username,permission,subscribed_at,link) VALUES('$facebook_rx_fb_page_info_id','$db_page_id',$db_user_id,'$db_client_thread_id','$db_client_id',$db_client_name,'$db_permission','$subscribed_at','$link');");
                if($this->db->affected_rows() != 0) $success++ ;
                $total++;
            }

        }
        // $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$facebook_rx_fb_page_info_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id),array("last_lead_sync"=>date("Y-m-d H:i:s"),"current_lead_count"=>$total));
        
        $sql = "SELECT count(id) as permission_count FROM `facebook_rx_conversion_user_list` WHERE page_table_id='$facebook_rx_fb_page_info_id' AND permission='1' AND user_id=".$this->user_id;
        $count_data = $this->db->query($sql)->row_array();

        $sql2 = "SELECT count(id) as permission_count FROM `facebook_rx_conversion_user_list` WHERE page_table_id='$facebook_rx_fb_page_info_id' AND permission='0' AND user_id=".$this->user_id;
        $count_data2 = $this->db->query($sql2)->row_array();

        // how many are subscribed and how many are unsubscribed
        $subscribed = isset($count_data["permission_count"]) ? $count_data["permission_count"] : 0;
        $unsubscribed = isset($count_data2["permission_count"]) ? $count_data2["permission_count"] : 0;
        $current_lead_count=$subscribed+$unsubscribed;

        $this->basic->update_data("facebook_rx_fb_page_info",array("id"=>$facebook_rx_fb_page_info_id,"facebook_rx_fb_user_info_id"=>$facebook_rx_fb_user_info_id),array("current_subscribed_lead_count"=>$subscribed,"current_unsubscribed_lead_count"=>$unsubscribed,"last_lead_sync"=>date("Y-m-d H:i:s"),"current_lead_count"=>$current_lead_count));
        
        $str = "$success"." ".$this->lang->line(" leads has been imported successfully.");
    
        $response =array();
        $response["message"] = $str;
        $response["status"] = '1';
        $response["count"] = $success;

        echo json_encode($response);
    }

    public function lead_list()
    {
        $data['body'] = "facebook_ex/import lead";
        $data['page_title'] = $this->lang->line("import lead");     
        $table = 'facebook_rx_conversion_contact_group';
        $where['where'] = array('user_id'=>$this->user_id);
        $info = $this->basic->get_data($table,$where);
        foreach ($info as $key => $value) {
            $result = $value['id'];
            $data['contact_type_id'][$result] = $value['group_name'];
        }
        $this->_viewcontroller($data);
    }

    public function lead_list_data($page_id=0) // auto id
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';


        $client_username = trim($this->input->post("search_client_username", true));
        $contact_type_id = trim($this->input->post("contact_type_id", true));
        $is_searched = $this->input->post('is_searched', true);

        if($is_searched)
        {
            $this->session->set_userdata('facebook_ex_lead_list_custom_username', $client_username);
            $this->session->set_userdata('facebook_ex_lead_list_custom_group', $contact_type_id);
        }

        $search_client_username  = $this->session->userdata('facebook_ex_lead_list_custom_username');
        $contact_group_id  = $this->session->userdata('facebook_ex_lead_list_custom_group');

        $where_simple=array();

        $where_simple['facebook_rx_conversion_user_list.page_table_id'] = $page_id;
        if ($search_client_username) $where_simple['client_username like '] = "%".$search_client_username."%";

        if($contact_group_id)
        {
            $this->db->where("FIND_IN_SET('$contact_group_id',facebook_rx_conversion_user_list.contact_group_id) !=", 0);
        }

        $where_simple['facebook_rx_conversion_user_list.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_page_info.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_page_info.deleted'] = '0';
        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $where = array('where' => $where_simple);

        $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.id=facebook_rx_conversion_user_list.page_table_id,left");
        $select =array("facebook_rx_conversion_user_list.*","facebook_rx_fb_page_info.page_name","facebook_rx_fb_page_info.id as page_auto_id");

        $table = "facebook_rx_conversion_user_list";
        $info = $this->basic->get_data($table,$where,$select,$join,$limit=$rows, $start=$offset,$order_by=$order_by_str,"client_thread_id");

        for($i=0;$i<count($info);$i++)
        {

            $info[$i]['page_name_formatted'] = "<a  target='_BLANK' href='https://facebook.com/".$info[$i]['page_id']."'>".$info[$i]['page_name']."</a>";
            $info[$i]['client_username_formatted'] = $info[$i]['client_username'];

            $btn_id=$info[$i]['id'];
            $edit_btn= "<a target='__BLANK' class='btn btn-sm btn-outline-warning' href='".base_url("facebook_ex_import_lead/update_contact/{$btn_id}/0")."'><i class='fa fa-edit'></i> ".$this->lang->line("group")."</a>";
           
            if($info[$i]['link'] != '')   $info[$i]['facebook_link'] = "<a class='label label-light' href='https://www.facebook.com".$info[$i]['link']."' target='_blank'><i class='fa fa-hand-o-right blue'></i> ".$this->lang->line("go to inbox")."</a>";
            else $info[$i]['facebook_link'] = "x";

            $info[$i]['added_at']=date("jS M, y H:i:s",strtotime($info[$i]['subscribed_at']));

            if($info[$i]['permission'] == '1')
            {
                $info[$i]['status'] = "<button id ='".$info[$i]['id']."-".$info[$i]['permission']."' type='button' class='client_thread_subscribe_unsubscribe btn btn-sm btn-outline-danger'><i class='fa fa-remove'></i> ".$this->lang->line("unsubscribe")."</button>";//$one_user['permission'];
            }
            elseif ($info[$i]['permission'] == '0') 
            {
                $info[$i]['status'] = "<button id ='".$info[$i]['id']."-".$info[$i]['permission']."' type='button' class='client_thread_subscribe_unsubscribe btn btn-sm btn-outline-success'><i class='fa fa-check-circle'></i> ".$this->lang->line("subscribe")."</button>";
            }

        }

        if ($contact_group_id)
        {
            $this->db->where("FIND_IN_SET('$contact_group_id',facebook_rx_conversion_user_list.contact_group_id) !=", 0);
        }

        $total_rows_array = $this->basic->count_row($table, $where, $count = "facebook_rx_conversion_user_list.id",$join,'client_thread_id');
        $total_result = $total_rows_array[0]['total_rows'];

        echo convert_to_grid_data($info, $total_result);
    }


    public function user_details_modal(){

        if (empty($_POST['user_page_id'])) {
            die();
        }

        $user_id_and_page_id = explode("-",$_POST['user_page_id']);
        $user_id = $user_id_and_page_id[0];
        $page_id = $user_id_and_page_id[1];
        $page_name = $user_id_and_page_id[2];


        $search_username  = $this->session->userdata('facebook_ex_lead_list_custom_username');             
        $search_lead_group  = $this->session->userdata('facebook_ex_lead_list_custom_group');
        $contact_type_id['']=$this->lang->line('All Groups');

        $table = 'facebook_rx_conversion_contact_group';
        $where['where'] = array('user_id'=>$this->user_id);
        $info = $this->basic->get_data($table,$where);
        foreach ($info as $key => $value) {
            $result = $value['id'];
            $contact_type_id[$result] = $value['group_name'];
        }

        $dropdown=form_dropdown('contact_type_id',$contact_type_id,$search_lead_group,'class="form-control" id="contact_type_id"');  

        $table_name = "facebook_rx_conversion_user_list";
        $where['where'] = array('user_id' => $user_id, 'page_table_id' => $page_id);
        $one_page_user_details = $this->basic->get_data($table_name,$where);

        $leads_list_page_id= isset($one_page_user_details[0]['page_id']) ? $one_page_user_details[0]['page_id'] : ""; 

        $html='';
        $html .= "
            <div class='text-center' style='margin-top: -20px !important;'>
                    <a href='".site_url()."facebook_ex_import_lead/user_details_download/".$user_id."-".$page_id."-".$page_name."' target='_blank' ><button class='btn btn-sm btn-outline-primary download_lead_list' button_id='".$user_id."-".$page_id."' page_name='".$page_name."'><i class='fa fa-cloud-download'></i> ".$this->lang->line("Download lead list")."</button>
                    </a>";
        if ($this->botinboxer_exist()) {
            $html .= "
                &nbsp;&nbsp; 
                <button class='btn btn-sm btn-outline-warning migrate_leadTO_bot' button_id='".$user_id."-".$leads_list_page_id."' ><i class='fa fa-cloud-download'></i> ".$this->lang->line("Migrate lead to Bot")."</button>
            ";
        }


        $html.='<form class="form-inline" style="margin:10px 15px 0 15px;">

                  <div class="form-group">
                    <input id="search_client_username" name="search_client_username" value="'.$search_username.'" class="form-control" size="20" placeholder="'.$this->lang->line("user name").'">
                  </div>
                  <div class="form-group">'.$dropdown.'
                  
                  </div> 

                  <button class="btn btn-info"  onclick="doSearch(event)"><i class="fa fa-search"></i>'.$this->lang->line("search").'</button>    
                </form></div>';

        $html.="<div id='response_div_part'><table id='dg'></table></div>
        <script> 
        \$j('#dg').datagrid({
            url:'".base_url().'facebook_ex_import_lead/lead_list_data/'.$page_id."',
            method:'post',
            idField:'id',
            pagination:'true',
            rownumbers:'true',
            toolbar:'#tb',
            pageSize:'10',
            pageList:[5,10,15,20,50,100],
            fit: true,
            fitColumns: true,
            nowrap: true,
            columns:[[
                {field:'client_id',title:'".$this->lang->line("client id")."',align:'left',width:'20%'},
                {field:'client_username_formatted',title:'".$this->lang->line("user name")."',left:'center',width:'20%'},
                {field:'facebook_link',title:'".$this->lang->line("facebook link")."',align:'center',width:'20%'},
                {field:'added_at',title:'".$this->lang->line("added at")."',align:'center',width:'20%'},
                {field:'status',title:'".$this->lang->line("action")."',align:'center',width:'20%'}
            ]]
        });
        </script>";

        $html.='
        <script>   
          function doSearch(event)
          {
            event.preventDefault(); 
            $j("#dg").datagrid("load",{
              search_client_username:     $j("#search_client_username").val(),    
              contact_type_id       :     $j("#contact_type_id").val(),  
              is_searched           :     1
            });
          } 
        </script>';


        // $html.='
        // <script>
        //   $j(document).ready(function() {
        //       $("#response_div_part").mCustomScrollbar({
        //         autoHideScrollbar:true,
        //         theme:"3d-dark"
        //       });
        // });
        // </script>';

        $html.='        
        <style>
        .datagrid-view{height:465px !important;border-top:1px solid #ccc !important;border-bottom:1px solid #ccc !important;}
        .datagrid .datagrid-pager{border-width:0px !important;margin-top:0 !important;}
        .datagrid-wrap{height:auto !important;padding-bottom:0 !important;padding-top:10px !important;}
        </style>';
       
        echo $html;
    }


    public function user_details_download($user_id_and_page_info){

        if($this->is_demo == '1')
        {
            if($this->session->userdata('user_type') == "Admin")
            {
                echo "<div class='alert alert-danger text-center'><i class='fa fa-ban'></i> This function is disabled from admin account in this demo!!</div>";
                exit();
            }
        }



        // if (empty($_POST['user_page_id'])) {
        //     die();
        // }

        $user_id_and_page_id = explode("-",$user_id_and_page_info);
        $user_id = $user_id_and_page_id[0];
        $page_id = $user_id_and_page_id[1];
        $page_name = $user_id_and_page_id[2];

        $table_name = "facebook_rx_conversion_user_list";
        $where['where'] = array('user_id' => $user_id, 'page_table_id' => $page_id);
        $one_page_user_details = $this->basic->get_data($table_name,$where);

        
        if(empty($one_page_user_details))
        {
            $str = "<div class='alert alert-danger text-center'>".$this->lang->line("No data to download")."</div>";
        }
        else {


            $filename="{$page_name}_{$this->user_id}_lead_info.csv";
            // make output csv file unicode compatible
            $f = fopen('php://memory', 'w'); 
            fputs( $f, "\xEF\xBB\xBF" );

            /**Write header in csv file***/
            $write_data[]="Page ID";
            $write_data[]="Client Username";
            $write_data[]="Client Thread Id";
            $write_data[]="Client Id";
            $write_data[]="Subscribed?";

            fputcsv($f,$write_data, ",");

            foreach ($one_page_user_details as $value) 
            {
                $write_data=array();
                $write_data[]=$value['page_id'];
                $write_data[]=$value['client_username'];
                $write_data[]=$value['client_thread_id'];
                $write_data[]=$value['client_id'];
                $write_data[]=$value['permission'];

                fputcsv($f,$write_data, ",");
            }

            
            // reset the file pointer to the start of the file
            fseek($f, 0);
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename="'.$filename.'";');
            // make php send the generated csv lines to the browser
            fpassthru($f);  
            
        }
        
    }

    public function client_subscribe_unsubscribe_status_change()
    {
        if (empty($_POST['client_subscribe_unsubscribe_status'])) {
            die();
        }
        $client_subscribe_unsubscribe = array();
        $post_val=$this->input->post('client_subscribe_unsubscribe_status');
        $client_subscribe_unsubscribe = explode("-",$post_val);
        $id = isset($client_subscribe_unsubscribe[0]) ? $client_subscribe_unsubscribe[0]: 0;
        $current_status =  isset($client_subscribe_unsubscribe[1]) ? $client_subscribe_unsubscribe[1]: 0;
        
        if($current_status=="1") $permission="0";
        else $permission="1";

        $client_thread_info = $this->basic->get_data('facebook_rx_conversion_user_list',array('where'=>array('id'=>$id,'user_id'=>$this->user_id)));
        $client_thread_id = $client_thread_info[0]['client_thread_id'];
        $page_id = $client_thread_info[0]['page_id'];

        $where = array
        (
            'client_thread_id' => $client_thread_id,
            'user_id' => $this->user_id
        );
        $login_user_id = $this->user_id;
        $data = array('permission' => $permission);
        if($permission=="0") $data["unsubscribed_at"] = date("Y-m-d H:i:s");
        $response='';
        if($this->basic->update_data('facebook_rx_conversion_user_list', $where, $data))
        {     
            if($permission=="0")
            {
                $response = "<button id ='".$id."-".$permission."' type='button' class='client_thread_subscribe_unsubscribe btn btn-sm btn-outline-success'><i class='fa fa-check-circle'></i> ".$this->lang->line("subscribe")."</button>";

                $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count = current_subscribed_lead_count-1,current_unsubscribed_lead_count = current_unsubscribed_lead_count+1 WHERE user_id = '$login_user_id' AND page_id = '$page_id'");
            }
            else  
            {
                $response = "<button id ='".$id."-".$permission."' type='button' class='client_thread_subscribe_unsubscribe btn btn-sm btn-outline-danger'><i class='fa fa-remove'></i> ".$this->lang->line("unsubscribe")."</button>";

                $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count = current_subscribed_lead_count+1,current_unsubscribed_lead_count = current_unsubscribed_lead_count-1 WHERE user_id = '$login_user_id' AND page_id = '$page_id'");
            }
            echo $response;
        }
    }



    public function enable_disable_auto_sync()
    {
        if($this->session->userdata('user_type') != 'Admin' && !in_array(78,$this->module_access))
        redirect('home/login_page', 'location'); 
    
        $page_id =  $this->input->post("page_id");
        $operation =  $this->input->post("operation");
        if($page_id=="" || $operation=="") exit();

        $this->basic->update_data("facebook_rx_fb_page_info",array("page_id"=>$page_id,"user_id"=>$this->user_id,"facebook_rx_fb_user_info_id"=>$this->session->userdata("facebook_rx_fb_user_info")),array("auto_sync_lead"=>$operation,"next_scan_url"=>""));


    }



    public function contact_group()
    {  
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('facebook_rx_conversion_contact_group.deleted', '0');
        $crud->where('facebook_rx_conversion_contact_group.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();
        if($this->session->userdata('user_type') == "Admin" && $this->is_demo=='1') $crud->unset_delete();
        
        $crud->set_theme('flexigrid');
        $crud->set_table('facebook_rx_conversion_contact_group');
        $crud->order_by('group_name');
        $crud->set_subject($this->lang->line('Lead Group'));
        $crud->required_fields('group_name');
        $crud->columns('group_name','id');

        $crud->fields('group_name');
        $crud->display_as('group_name', $this->lang->line('Group Name'));
        $crud->display_as('id', $this->lang->line('Group ID'));
        /**insert the user_id**/
        $crud->callback_after_insert(array($this, 'insert_user_id_group'));


        $output = $crud->render();
        $data['page_title'] =$this->lang->line("Lead Group");
        $data['output'] = $output;
        $data['crud']= 1;

        $this->_viewcontroller($data);
    }

    public function bulk_group_assign()
    {
        if(!$_POST) exit();

        $group_id = $this->input->post("group_id");
        $info = $this->input->post("info");
        $info=json_decode($info,true);

        foreach ($info as $key => $value) 
        {
           $id = $value["id"];
           // $groupid = $value["contact_type_db_id"];
           // $groupid_array = explode(',',$groupid);

           // $result = array_add($group_id, $groupid_array);
           // $result=array_unique($result);
           // sort($result);

           $final_group_str=implode(',', $group_id);
           $final_group_str=trim($final_group_str,',');

           $this->basic->update_data("facebook_rx_conversion_user_list",array("id"=>$id,"user_id"=>$this->user_id),array("contact_group_id"=>$final_group_str));
           
        }
        $this->session->set_flashdata('success_message', 1);
    }


    public function contact_list()
    {
        $data['page_title'] = $this->lang->line("Lead List");
        $data['body'] = 'facebook_ex/contact_list';

        $table = 'facebook_rx_conversion_contact_group';
        $where['where'] = array('user_id'=>$this->user_id);
        $info = $this->basic->get_data($table,$where);
        foreach ($info as $key => $value) {
            $result = $value['id'];
            $data['contact_type_id'][$result] = $value['group_name'];
        }

        $data['page_info']= $this->basic->get_data("facebook_rx_fb_page_info",array("where"=>array("user_id"=>$this->user_id)),array("page_id","page_name","id"),$join='',$limit='',$start=NULL,$order_by='page_name asc',$group_by='page_id');

        $this->_viewcontroller($data);
    }


    public function contact_list_data()
    {
        // setting variables for pagination
        $page = isset($_POST['page']) ? intval($_POST['page']) : 100;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'client_username';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'ASC';
        $order_by_str=$sort." ".$order;


        // setting properties for search
        $first_name = trim($this->input->post('client_username', true));
        $contact_type_id = $this->input->post('contact_type_id', true);
        $permission_search = $this->input->post('permission_search', true);
        $search_page = $this->input->post('search_page', true);
        $is_searched = $this->input->post('is_searched', true);


        if ($is_searched) {

            $this->session->set_userdata('fb_ex_contact_list_first_name',$first_name);
            $this->session->set_userdata('fb_ex_contact_list_contact_type_id',$contact_type_id);
            $this->session->set_userdata('fb_ex_contact_list_permission_search',$permission_search);
            $this->session->set_userdata('fb_ex_contact_list_search_page',$search_page);
        }

        $client_username        = $this->session->userdata('fb_ex_contact_list_first_name');
        $contact_group_id       = $this->session->userdata('fb_ex_contact_list_contact_type_id');
        $permission             = $this->session->userdata('fb_ex_contact_list_permission_search');
        $search_page             = $this->session->userdata('fb_ex_contact_list_search_page');

        $where_simple=array();

        if ($client_username) 
        {
            $where_simple['client_username like '] = "%".$client_username."%";
        }  

        if ($permission!="") 
        {
            $where_simple['facebook_rx_conversion_user_list.permission'] = $permission;
        }   

        if ($search_page!="") 
        {
            $where_simple['facebook_rx_conversion_user_list.page_id'] = $search_page;
        }       
    

        if ($contact_group_id) 
        {
            $this->db->where("FIND_IN_SET('$contact_group_id',facebook_rx_conversion_user_list.contact_group_id) !=", 0);
        }


        $user_type = $this->session->userdata('user_type');
        $user_id = $this->session->userdata('user_id');
     
        $where_simple['facebook_rx_conversion_user_list.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_page_info.user_id'] = $this->user_id;
        $where_simple['facebook_rx_fb_page_info.deleted'] = '0';
        $where  = array('where'=>$where_simple);

        $offset = ($page-1)*$rows;
        $result = array();       

        $table = "facebook_rx_conversion_user_list";     

        $join = array('facebook_rx_fb_page_info'=>"facebook_rx_fb_page_info.page_id=facebook_rx_conversion_user_list.page_id,left");   
        $group_by = "id";

        $info = $this->basic->get_data($table, $where, $select=array("facebook_rx_conversion_user_list.*","facebook_rx_fb_page_info.page_name"), $join, $limit=$rows, $start=$offset, $order_by=$order_by_str,$group_by);
        // echo $this->db->last_query();
       
        if ($contact_group_id) 
        {
            $this->db->where("FIND_IN_SET('$contact_group_id',facebook_rx_conversion_user_list.contact_group_id) !=", 0);
        }
        $total_rows_array = $this->basic->count_row($table, $where, $count="facebook_rx_conversion_user_list.id",$join,$group_by);      

        $total_result = $total_rows_array[0]['total_rows'];

        $info_count = count($info);


        foreach ($info as $key => $value) 
        {
          $value = $info[$key]['contact_group_id'];

          $type_id = explode(",",$value);

          $table = 'facebook_rx_conversion_contact_group';
          $select = array('group_name','id');

          $where_group['where_in'] = array('id'=>$type_id);
          $where_group['where'] = array('deleted'=>'0');

          $info1 = $this->basic->get_data($table,$where_group,$select);

         $str = '';
         $str2 = '';
         foreach ($info1 as  $value1)
          {
            $str.= $value1['group_name']." ,"; 
            $str2.= $value1['id'].","; 
          }

            
        $str = trim($str, ",");
        $str2 = trim($str2, ",");

        $info[$key]['contact_type_id']= $str;
        $info[$key]['contact_type_db_id']= $str2;


        }


        echo convert_to_grid_data($info, $total_result);

    }
   
    public function update_contact($id = 0,$display_subscribe = '1')
    {  
       if($id==0) exit();

       $data['body'] = 'facebook_ex/update_contact';
       $data['page_title'] = $this->lang->line("Edit Lead");
       $where = array();
       $where['where'] = array('user_id'=>$this->user_id);      

       $group_info=$this->basic->get_data('facebook_rx_conversion_contact_group', $where, $select='', $join='', $limit='', $start='', $order_by='group_name', $group_by='', $num_rows=0); 

     
       $where_contacts["where"] = array("facebook_rx_conversion_user_list.id" => $id,"facebook_rx_conversion_user_list.user_id"=>$this->user_id); 
       $result = $this->basic->get_data("facebook_rx_conversion_user_list",$where_contacts);
       $data['info'] = $result[0];
       $data['display_subscribe'] = $display_subscribe;


         $str = '';
         $form_contact=array();
         if($_POST && isset($_POST["contact_type_id"])) 
         {
            $form_contact=$_POST["contact_type_id"];
         }
         else
         {
            $form_contact_str=$result[0]["contact_group_id"];
            $form_contact=explode(',',$form_contact_str);
         }
         foreach ($group_info as $info) 
         {
             $type =  $info['group_name'];            
             $type_id = $info['id'];
             if(in_array($type_id,$form_contact))
             $str.= "<label class='checkbox-inline'><input checked='true' type='checkbox' name= 'contact_type_id[]' id = 'contact_type_id[]' value='{$type_id}'>{$type}</label><br/>";
             else
            $str.= "<label class='checkbox-inline'><input type='checkbox' name= 'contact_type_id[]' id = 'contact_type_id[]' value='{$type_id}'>{$type}</label><br/>";
             $data['group_checkbox'] = $str; 
         }

       $this->_viewcontroller($data);
    }


    public function update_contact_action($id = 0 , $prev_permission = '0',$display_subscribe='1')
    {

       if ($_POST) 
        {
            $this->form_validation->set_rules('client_username','Name','trim|required');
            if($display_subscribe=='1')
            $this->form_validation->set_rules('subscribed',"subscribed",'trim|required'); 


            if ($this->form_validation->run() == false) 
            {                         
                return $this->update_contact($id,$display_subscribe);
            }    

            else 
            {
                $client_username =      strip_tags($this->input->post('client_username', true));                
                $permission =           strip_tags($this->input->post('subscribed', true));
            
                $temp = $this->input->post('contact_type_id', true);
                if(!is_array($temp)) $temp=array();
                $type = '';
                if(count($temp)>0) 
                {
                    $type = implode($temp, ',');
                }

                $contact_group_id = $type;

                $client_thread_info = $this->basic->get_data('facebook_rx_conversion_user_list',array('where'=>array('id'=>$id,'user_id'=>$this->user_id)));
                $client_thread_id = $client_thread_info[0]['client_thread_id'];
                $page_id = $client_thread_info[0]['page_id'];
                $login_user_id=$this->user_id;

                $where = array
                (
                    'client_thread_id' => $client_thread_id,
                    'user_id' => $this->user_id
                );

                $data = array
                (
                       "client_username" => $client_username,
                       "contact_group_id" => $contact_group_id                                   
                );
                if($permission!="") $data["permission"]=$permission;

                $is_changed='0';
                if($prev_permission!=$permission) $is_changed='1';

                if(!empty($data))
                {
                    $this->basic->update_data("facebook_rx_conversion_user_list", $where, $data);
                    
                    if($is_changed=='1' && $permission!="")
                    {
                        if($permission=="0")
                        {
                            $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count = current_subscribed_lead_count-1,current_unsubscribed_lead_count = current_unsubscribed_lead_count+1 WHERE user_id = '$login_user_id' AND page_id = '$page_id'");
                        }
                        else  
                        {
                            $this->basic->execute_complex_query("UPDATE facebook_rx_fb_page_info SET current_subscribed_lead_count = current_subscribed_lead_count+1,current_unsubscribed_lead_count = current_unsubscribed_lead_count-1 WHERE user_id = '$login_user_id' AND page_id = '$page_id'");
                        }
                    }
                            

                    $success = 1;
                    $this->session->set_flashdata('success_message', 1);
                    redirect('facebook_ex_import_lead/contact_list', 'location');
                }
                
                else 
                {
                    $this->session->set_flashdata("error_message", 1);
                    redirect('facebook_ex_import_lead/contact_list', 'location');
                }                       
            }               
        }
    }


    public function delete_contact_action()
    {
        $table_id = $this->input->post('table_id');
        $this->basic->delete_data('facebook_rx_conversion_user_list',array('id'=>$table_id));
        echo "success";
    }

    public function delete_bulk_contacts()
    {
        if(!$_POST) exit();

        $info = $this->input->post("info");
        $info=json_decode($info,true);
        $deleted_ids = array();

        foreach ($info as $key => $value) 
        {
           $id = $value["id"];
           array_push($deleted_ids, $id);           
        }
        
        $this->db->where_in('id', $deleted_ids);
        $this->db->delete("facebook_rx_conversion_user_list");
        echo "success";
    }




    public function insert_user_id_group($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);        
        $this->basic->update_data("facebook_rx_conversion_contact_group", $where, $update_data);
    }

    
    /**
     * checks whether botinboxer exists or not and access modifier is "protected"
     * @return bool 
     */
    protected function botinboxer_exist()
    {
        if($this->session->userdata('user_type') == 'Admin'  && $this->app_product_id==15) return true;
        if($this->session->userdata('user_type') == 'Admin'  && $this->basic->is_exist("add_ons",array("project_id"=>3))) return true;
        if($this->session->userdata('user_type') == 'Member' && in_array(200,$this->module_access)) return true;
        return false;
    }

    /**
     * called via ajax and needs user_id and page_id to be executed
     * brings data from table facebook_rx_conversion_user_list and makes them unique
     * and after checking if page exists & bot enabled from table messenger_bot_page_info
     * it insert leads on table messenger_bot_subscriber
     *     
     * @return json message describes about results
     */
    public function migrateLeadToBot()
    {        
        /**
         * grab data from post
         */
        $user_page_id = $this->input->post('user_page_id');
        $user_page_id = explode('-', $user_page_id);
        $user_id = $user_page_id[0];
        $page_id = $user_page_id[1];

        $response = array();

        
        /**
         * check page and bot enable
         */
        $check_before_insert = $this->basic->get_data('messenger_bot_page_info', array('where' => array('user_id' => $user_id, 'page_id' => $page_id)), '', '', '', '', '', '', 1);


        if ($check_before_insert['extra_index']['num_rows'] > 0) {

            if ($check_before_insert[0]['bot_enabled'] != '1') {

                $response['type'] = 'error';
                $response['message'] = $this->lang->line("Migration Failed : System didn't find enabled bot for this page in your account. Please enable bot for this page to migrate leads.");
            } 
            else {

                /**
                 * brings data from table facebook_rx_conversion_user_list and table messenger_bot_subscriber
                 * make it unique by intersecting both table data
                 * and then insert on table messenger_bot_subscriber
                 */
                
                $facebook_rx_conversion_user_list = $this->basic->get_data('facebook_rx_conversion_user_list', array('where' => array('user_id' => $user_id, 'page_id' => $page_id)));

                $messenger_bot_subscriber = $this->basic->get_data('messenger_bot_subscriber', array('where' => array('user_id' => $user_id, 'page_id' => $page_id)));

                $already_added_lead = array();

                foreach ($messenger_bot_subscriber as $key => $value) 
                    array_push($already_added_lead, $value['subscribe_id']);

                foreach ($facebook_rx_conversion_user_list as $key => $value) {
                    
                    if (!in_array($value['client_id'], $already_added_lead)) {
                        array_push($already_added_lead, $value['client_id']);

                        $data = array(
                            'user_id'           => $user_id,
                            'page_id'           => $page_id,
                            'subscribe_id'      => $value['client_id'],
                            'contact_group_id'  => $value['contact_group_id'],
                            'full_name'         => $value['client_username'],
                            'subscribed_at'     => $value['subscribed_at'],
                            'is_imported'       => '1',
                            'is_updated_name'   => '0'
                        );

                        $this->basic->insert_data('messenger_bot_subscriber', $data);
                    }
                }


                $response['type'] = 'success';
                $response['message'] = $this->lang->line('Migration Successful : In messenger subscriber list, first name , last name will be updated gradually depending on cron job.');
            }
        }
        else {

            $response['type'] = 'error';
            $response['message'] = $this->lang->line("Migration Failed : System didn't find any page of this user.");
        }

        echo json_encode($response);
    }



}
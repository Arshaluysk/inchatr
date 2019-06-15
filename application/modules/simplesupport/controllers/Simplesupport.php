<?php
/*
Addon Name: Simple support desk
Unique Name: SimplesupportDesk
Module ID: 0
Project ID: 0
Addon URI: http://getfbinboxer.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 1.0
Description: Helpdesk service for extended users
*/

require_once("application/controllers/Home.php"); // loading home controller

class Simplesupport extends Home
{
    public $addon_data=array();
    /**
     * initialize addon 
     */
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
        $this->load->helper('text');
        $function_name=$this->uri->segment(2);
        if($function_name!="support") 
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
        }if($this->session->userdata('license_type') != 'double') exit;

    }


/**
 * User can create support ticket
 * @return void 
 */
    public function support()
    {
        $data['body'] = 'support_ticket';
        $data['page_title'] = $this->lang->line('Support Ticket');
        $data["support_category"]=$this->basic->get_data("fb_support_category");
        $this->_viewcontroller($data);
    }

/**
 * User can see their own all of tickes
 * @return Array 
 */
    public function support_list()
    {
        if($this->session->userdata('license_type') != 'double') exit;
        $ticket_info = array();
        $ticket_list = $this->basic->get_data("fb_simple_support_desk",array("where"=>array("user_id"=>$this->user_id)));
      
        if(!empty($ticket_list))
        {
            foreach($ticket_list as $value)
            {
                array_push($ticket_info, $value['ticket_title']);
            }
        }
        $data['ticket_info'] = $ticket_info;

        $data['body'] = 'all_support_ticket';
        $data['page_title'] = $this->lang->line('Support Ticket List');
        $this->_viewcontroller($data);
    }
/**
 * All support ticket data view in data grid
 * @return Array 
 */
    public function all_support_ticket_data()
    {
      
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        redirect('home/access_forbidden', 'location');
        $page = isset($_POST['page']) ? intval($_POST['page']) : 15;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'fb_simple_support_desk.id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
        $ticket_title = trim($this->input->post("ticket_title", true));
        $ticket_status = $this->input->post("ticket_status",true);
        $is_searched = $this->input->post('is_searched', true);

        if ($is_searched) 
        {
            $this->session->set_userdata('all_search_post_ticket_title', $ticket_title);
            $this->session->set_userdata('all_ticket_status', $ticket_status);
            
        }
        // saving session data to different search parameter variables
        $search_ticket_title   = $this->session->userdata('all_search_post_ticket_title');
        $search_ticket_status  = $this->session->userdata('all_ticket_status');

        $where_simple=array();        
        if ($search_ticket_title) $where_simple['ticket_title like '] = "%".$search_ticket_title."%";
        if($search_ticket_status) $where_simple['ticket_status'] = $search_ticket_status;
        $where_simple['fb_simple_support_desk.user_id'] = $this->user_id;
       
        //$where_simple['fb_simple_support_desk.user_id'] = $this->user_id;
        $where  = array('where'=>$where_simple);

        $order_by_str=$sort." ".$order;
        $offset = ($page-1)*$rows;
        $result = array();
        $select= array(
            'fb_simple_support_desk.id',
            'fb_simple_support_desk.user_id',
            'fb_simple_support_desk.ticket_title',
            'fb_simple_support_desk.ticket_text',
            'fb_simple_support_desk.ticket_status',
            'fb_simple_support_desk.ticket_open_time',
            'fb_support_category.category_name'

        );
        $join = array(
            'fb_support_category' => 'fb_simple_support_desk.support_category=fb_support_category.id,left'
            //'fb_support_desk_reply' =>'fb_simple_support_desk.id=fb_support_desk_reply.id,left'
        );
        $table = "fb_simple_support_desk";
        $info = $this->basic->get_data($table, $where, $select, $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

        $total_rows_array = $this->basic->count_row($table, $where, $count="", $join);
        $total_result = $total_rows_array[0]['total_rows'];
    
        $info_new = array();
        $i = 0;
        foreach($info as $value)
        {	
        	$id = $value['id'];
            if($value['ticket_status']=='1')
            $info_new[$i]['status'] = "<span class='label label-light gray border_gray'><i class='fa fa-check-circle  green'></i> ".$this->lang->line('open');
            else  $info_new[$i]['status'] = "<span class='label label-light gray border_gray'><i class='fa fa-clock-o red'></i> ".$this->lang->line('closed')."</span>";
            $info_new[$i]['ticket_title'] = "<a href='". base_url()."simplesupport/reply_support/".$value['id']."'> (#".$value['id'].") </a>".$value['ticket_title'];
            $info_new[$i]['ticket_open_time'] = $value['ticket_open_time'];
            $info_new[$i]['category_name'] = $value['category_name'];
            $info_new[$i]['action'] = "<button name='delete' class='text-center btn btn-outline-danger delete' title='".$this->lang->line("Delete")."' id=".$id." '><i class='fa fa-trash'></i></button>&nbsp; <a class='text-center btn btn-outline-primary' href='". base_url()."simplesupport/reply_support/".$value['id']."' title='".$this->lang->line('Reply Ticket')."'><i class='fa fa-send'></i> ".$this->lang->line('reply')."</a>";
           
             $i++;
        }

        echo convert_to_grid_data($info_new, $total_result);
    }


/**
 * User submit their support tickets
 * @return void 
 */
    public function create_ticket()
    {
        
        if($_POST)
        {
            $post=$_POST;
            foreach ($post as $key => $value) 
            {
                $$key=$value;
            }
        }if($this->session->userdata('license_type') != 'double') exit;

        $data['ticket_title'] = $ticket_title;
        $data['ticket_text'] = trim($ticket_text);
        $data['user_id'] = $this->user_id;
        $data['support_category']= trim($support_category);

        if($this->basic->insert_data('fb_simple_support_desk',$data))
        {
            $id= $this->db->insert_id(); 
           
            $url = site_url()."simplesupport/reply_support/".$id;

            $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
            $message = "<p>".$this->lang->line("user has created new support ticket")."</p>
                        </br>
                        <p>".$this->lang->line('Hi')." ".$this->lang->line('admin').", </p>
                        <p>".$this->lang->line($data['ticket_title'])."</p>
                        </br>
                        <p>".$this->lang->line(word_limiter($data['ticket_text'],50))." </p>
                        </br>
                        <p>".$this->lang->line("go to this url").":".$url_final."</p>
                        ";


            $from = $this->session->userdata("user_login_email");
            $to = $this->config->item('institute_email');
            $subject = $this->config->item('product_name')." | ".$this->lang->line("support ticket");
            $mask = $subject;
            $html = 1;
            $this->_mail_sender($from, $to, $subject, $message, $mask, $html);
        }
       
        $this->session->set_flashdata('success_message', 1);
        redirect('simplesupport/support', 'location');


    }

/**
 * Admin can see all the his clients tickets
 * @return Array 
 */
    public function support_category()
    {

     $data['body'] = 'support_category';
     $data['page_title'] = $this->lang->line('support ticket category');
     $this->_viewcontroller($data);

    }
/**
 * Admin can create ticket category for his client
 * @return Void 
 */
    public function create_category()
    {

       
        $data['body'] = 'create_support_category';
        $data['page_title'] = $this->lang->line('support ticket category');
        $this->_viewcontroller($data);

    }
/**
 * Admin submit his ticket category for his client
 * @return Void 
 */
    public function save_category()
    {
       if($_POST)
       {
           $post=$_POST;
           foreach ($post as $key => $value) 
           {
               $$key=$value;
           }
       }
       
       $data['category_name']  = $category_name;
       $data['user_id'] = $this->user_id;
       $this->basic->insert_data('fb_support_category',$data);
       $this->session->set_flashdata('success_message', 1);
       redirect('simplesupport/support_category', 'location');


    }
/**
 * Admin Can see their own support category list
 * @return Array 
 */
    public function support_category_manager_data()
    {

    	$page = isset($_POST['page']) ? intval($_POST['page']) : 15;
    	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
    	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
    	$order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
    	$category_name = trim($this->input->post("category_name", true));
    	$is_searched = $this->input->post('is_searched', true);
    	if($is_searched) 
    	{
    	    $this->session->set_userdata('support_category_search', $category_name);
    	   
    	}
    	$support_category_names  = $this->session->userdata('support_category_search');
    	$where_simple=array();
    	if ($support_category_names) $where_simple['category_name like ']    = "%".$support_category_names."%";
    	
    	$where_simple['fb_support_category.user_id'] = $this->user_id;
    	$where  = array('where'=>$where_simple);
    	$order_by_str=$sort." ".$order;
    	$offset = ($page-1)*$rows;
    	$result = array();
    	$table = "fb_support_category";
    	$info = $this->basic->get_data($table, $where, '','',$limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');
    	$total_rows_array = $this->basic->count_row($table, $where, $count="fb_support_category.id");
    	$total_result = $total_rows_array[0]['total_rows'];
    	$information = array();
    	for($i=0;$i<count($info);$i++)
    	{   
    	    $id = $info[$i]['id'];
    	    $information[$i]['category_name'] = $info[$i]['category_name'];
    	    $information[$i]['action'] = "<a type='button' href='".base_url()."simplesupport/edit_support_category/".$info[$i]['id']."' class='text-center  btn btn-outline-warning' title='".$this->lang->line("edit")."' id=".$id." '><i class='fa  fa-edit'></i></a>&nbsp;<button name='delete' class='text-center delete_reply_info btn btn-outline-danger delete' title='".$this->lang->line("Delete")."' id=".$id." '><i class='fa fa-trash'></i></button>";
    	}
    	echo convert_to_grid_data($information, $total_result);
    }

/**
 * Admin Can delete his own support Category
 * @return Void 
 */
    public function delete_support_category()
    {
        if(isset($_POST["id"]))
        {
            $id = $this->input->post('id');
            
            $this->basic->delete_data('fb_support_category',array('id'=>$id));
        }
    }
/**
 * Edit Support Category
 * @param  integer $id 
 * @return Array    
 */
    public function edit_support_category($id)
    {
    	$data['body'] = 'edit_support_category';
    	$data['page_title'] = $this->lang->line('edit support ticket category');

    	$data["edit_support_category"]=$this->basic->get_data("fb_support_category",array("where"=>array("user_id"=>$this->user_id,"id"=>$id)));

    	$this->_viewcontroller($data);
    }
 /**
  * Support category
  * @param  integer $id 
  * @return void  
  */
    public function edit_category_action($id)
    {
    	

    	if($_POST)
    	{
    	    $post=$_POST;
    	     foreach ($post as $key => $value) 
    	    {
    	         $$key=$value;
    	    }
    	}
    	
    	$data = array(
    	     'user_id'       => $this->user_id,
    	     'category_name' => $category_name
    	 				);

    	$where = array('id' => $id);
   

    	$this->basic->update_data("fb_support_category",$where,$data);
    	$this->session->set_flashdata('success_message', 1);
    	redirect('simplesupport/support_category', 'location');

    }
/**
 * Admin can see all the ticket
 * @return void 
 */
    public function all_ticket()
    {
    	if($this->session->userdata('license_type') != 'double') exit;if($this->session->userdata("user_type")!="Admin") exit();

        $data['body'] = 'all_ticket';
    	$data['page_title'] = $this->lang->line('All Ticket');

    	
    	$this->_viewcontroller($data);
    }
/**
 * Delete user ticket
 * @return void 
 */
    public function delete_user_ticket()
    {
    	if(isset($_POST["id"]))
    	{
    	    $id = $this->input->post('id');

            if($this->session->userdata("user_type")=="Admin")
            $this->basic->delete_data('fb_simple_support_desk',array('id'=>$id));
    	    else $this->basic->delete_data('fb_simple_support_desk',array('id'=>$id,"user_id"=>$this->user_id));
    	    $reply_id = $this->input->post('id');
    	    $this->basic->delete_data('fb_support_desk_reply',array('reply_id'=>$reply_id));
    	}
    }

/**
 * all tocket show for admin
 * @return Array 
 */
    public function all_user_support_ticket_data()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'GET')
    	redirect('home/access_forbidden', 'location');
    	$page = isset($_POST['page']) ? intval($_POST['page']) : 15;
    	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
    	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'fb_simple_support_desk.id';
    	$order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
    	$ticket_title = trim($this->input->post("ticket_title", true));
        $ticket_status = $this->input->post("ticket_status",true);
        $ticket_id = $this->input->post('id',true);


    	$is_searched = $this->input->post('is_searched', true);

    	if ($is_searched) 
    	{
            $this->session->set_userdata('all_search_post_ticket_title', $ticket_title);
            $this->session->set_userdata('all_ticket_status', $ticket_status);
    	    $this->session->set_userdata('all_ticket_search_id', $ticket_id);
    	    
    	}
    	// saving session data to different search parameter variables
    	$search_ticket_title = $this->session->userdata('all_search_post_ticket_title');
        $search_ticket_status = $this->session->userdata('all_ticket_status');
        $search_ticket_id =  $this->session->userdata('all_ticket_search_id');
      
    	$where_simple=array();        
        if ($search_ticket_title) $where_simple['ticket_title like '] = "%".$search_ticket_title."%";
    	if($search_ticket_status) $where_simple['ticket_status'] = $search_ticket_status;
        if($search_ticket_id)   $where_simple['fb_simple_support_desk.id'] =$search_ticket_id;

    	$where  = array('where'=>$where_simple);
     
    	$order_by_str=$sort." ".$order;
    	$offset = ($page-1)*$rows;
    	$result = array();
        $select= array(
            'fb_simple_support_desk.id',
            'fb_simple_support_desk.user_id',
            'fb_simple_support_desk.ticket_title',
            'fb_simple_support_desk.ticket_text',
            'fb_simple_support_desk.ticket_status',
            'fb_simple_support_desk.ticket_open_time',
            'fb_support_category.category_name'

        );
        $join = array(
            'fb_support_category' => 'fb_simple_support_desk.support_category=fb_support_category.id,left'
            //'fb_support_desk_reply' =>'fb_simple_support_desk.id=fb_support_desk_reply.id,left'
        );
    	$table = "fb_simple_support_desk";
    	$info = $this->basic->get_data($table, $where, $select, $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='');

    	$total_rows_array = $this->basic->count_row($table, $where, $count="", $join);
    	$total_result = $total_rows_array[0]['total_rows'];
        $info_new = array();
    	$i = 0;
    	foreach($info as $value)
    	{	
    		$id = $value['id'];

    	    if($value['ticket_status']=='1')
    	    $info_new[$i]['status'] = "<span class='label label-light gray border_gray'><i class='fa fa-check-circle  green'></i> ".$this->lang->line('open');
    	    else  $info_new[$i]['status'] = "<span class='label label-light gray border_gray'><i class='fa fa-clock-o red'></i> ".$this->lang->line('closed')."</span>";
    	    $info_new[$i]['ticket_title'] = "<a href='". base_url()."simplesupport/reply_support/".$value['id']."'> (#".$value['id'].") </a>".$value['ticket_title'];;
            $info_new[$i]['ticket_open_time'] = $value['ticket_open_time'];
            $info_new[$i]['category_name'] = $value['category_name'];
            $info_new[$i]['ticket_open_time2'] = date('M d,Y H:i:s',strtotime($value['ticket_open_time']));
    	    $info_new[$i]['reply'] = "<button name='delete' class='text-center btn btn-outline-danger delete' title='".$this->lang->line("Delete")."' id='".$id."'><i class='fa fa-trash'></i></button>&nbsp; <a class='text-center btn btn-outline-primary' href='". base_url()."simplesupport/reply_support/".$value['id']."' title='".$this->lang->line('Reply Ticket')."'><i class='fa fa-send'></i> ".$this->lang->line('Reply')."</a>";
            $button = '';
            if($value['ticket_status'] == '1')
            $button = "&nbsp;<button class='btn btn-outline-warning close_ticket' table_id='".$value['id']."' title='".$this->lang->line("close ticket")."'><i class='fa fa-ban'></i></button>";
            if($value['ticket_status'] == '2')
            $button = "&nbsp;<button class='btn btn-outline-success open_ticket' table_id='".$value['id']."' title='".$this->lang->line("open ticket")."'><i class='fa fa-check-circle'></i></button>";
           
            $info_new[$i]['actions'] = $button;

    	    $i++;
    	}

    	echo convert_to_grid_data($info_new, $total_result);
    }
/**
 * close support ticket
 * @return void 
 */
    public function ajax_autoreply_pause()
    {
        $table_id = $this->input->post('table_id');
        if($this->session->userdata("user_type")=="Admin")
        $this->basic->update_data('fb_simple_support_desk',array('id'=>$table_id),array('ticket_status'=>'2'));
        else $this->basic->update_data('fb_simple_support_desk',array('id'=>$table_id,"user_id"=>$this->user_id),array('ticket_status'=>'2'));
        echo 'success';
    }
/**
 * open ticket status
 * @return void 
 */
    public function ajax_autoreply_play()
    {
        $table_id = $this->input->post('table_id');
        if($this->session->userdata("user_type")=="Admin")
        $this->basic->update_data('fb_simple_support_desk',array('id'=>$table_id),array('ticket_status'=>'1'));
        else $this->basic->update_data('fb_simple_support_desk',array('id'=>$table_id,"user_id"=>$this->user_id),array('ticket_status'=>'1'));
        echo 'success';
    }

/**
 * Reply of each support ticket
 * @param  integer $id 
 * @return array  
 */
    public function reply_support($id=0)
    {
   
        if($id==0) exit();if($this->session->userdata('license_type') != 'double') exit;
        $data['body'] = 'ticket_reply';
        $data['page_title'] = $this->lang->line('Reply Ticket');
        $join = array(
            'fb_support_category' => 'fb_simple_support_desk.support_category=fb_support_category.id,left'
        );
        if($this->session->userdata("user_type")=="Admin")
        $where=array('where' => array('fb_simple_support_desk.id' => $id));
        else $where=array('where' => array('fb_simple_support_desk.id' => $id,"fb_simple_support_desk.user_id"=>$this->user_id));

        $table = "fb_simple_support_desk";
        $info = $this->basic->get_data($table, $where, $select='fb_simple_support_desk.*,fb_support_category.category_name', $join);
        if(!isset($info[0])) exit();

        $data['ticket_info']=$info;

        $user=$info[0]['user_id'];
        $user_info = $this->basic->get_data('users',array('where'=>array('id'=>$user)));
        $data['user_info']=$user_info;
        $join = array(
            'users' => 'fb_support_desk_reply.user_id=users.id,left'
        );

        $table = "fb_support_desk_reply";
        $ticket_replied = $this->basic->get_data($table,array("where"=>array("reply_id"=>$id)),$select='',$join);
        $data['ticket_replied'] = $ticket_replied;

        $this->_viewcontroller($data);
    }


/**
 * Delete user all the tickets only admin
 * @return void 
 */
    public function delete_user_ticket_all()
    {
    	if(isset($_POST["id"]))
    	{
    	    $id = $this->input->post('id');

    	    $this->basic->delete_data('fb_simple_support_desk',array('id'=>$id));

    	    $reply_id = $this->input->post('id');

    	    $this->basic->delete_data('fb_support_desk_reply',array('reply_id'=>$reply_id));
    	}
    }

/**
 * Admin reply his client ticket & store into the database
 * @return void 
 */
    public function reply_action($id=0)
    {

       if($_POST)
       {
           $post=$_POST;
           foreach ($post as $key => $value) 
           {
               $$key=$value;
           }
       }
       
       $data['ticket_reply_text']  = $ticket_reply_text;
       $data['user_id'] = $this->user_id;
       $data['reply_id'] = $id;

       if($this->basic->insert_data('fb_support_desk_reply',$data))
       {

            if($this->session->userdata("user_type")=="Member")
            {   
               $id= $id; 
               $url = site_url()."simplesupport/reply_support/".$id;
               $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
               $message = "<p>".$this->lang->line("The customer has responded to the ticket")."</p>
                           </br>
                           </br>
                           <p>".$this->lang->line('hi')." ".$this->lang->line('admin').", </p>
                           </br>
                           </br>
                           <p>".$this->lang->line(word_limiter($data['ticket_reply_text'],50))." </p>
                           </br>
                           </br>
                           <p>".$this->lang->line("go to this url").":".$url_final."</p>
                           ";


               $from = $this->session->userdata("user_login_email");
               $to = $this->config->item('institute_email');
               $subject = $this->config->item('product_name')." | ".$this->lang->line("support ticket");
               $mask = $subject;
               $html = 1;
               $this->_mail_sender($from, $to, $subject, $message, $mask, $html);
               $this->session->set_flashdata('success_message', 1);
               redirect('simplesupport/reply_support/'.$id.'', 'location'); 
            }
            else
            {
              $id= $id; 
              $url = site_url()."simplesupport/reply_support/".$id;
              $url_final="<a href='".$url."' target='_BLANK'>".$url."</a>";
              $message = "<p>".$this->lang->line("admin has responded to your ticket")."</p>
                          </br>
                          </br>
                          <p>".$this->lang->line('hi')." ".$this->lang->line('customer').", </p>
                          </br>
                          </br>
                          <p>".$this->lang->line(word_limiter($data['ticket_reply_text'],50))." </p>
                          </br>
                          </br>
                          <p>".$this->lang->line("go to this url").":".$url_final."</p>
                          ";


	          if($this->session->userdata("user_type")=="Admin")
				$where=array('where' => array('fb_simple_support_desk.id' => $id));
				$test= $this->basic->get_data('fb_simple_support_desk',$where);
				$t=$test[0]['user_id'];
				$where=array('where' => array('users.id' => $t ));
				$table = "users";
				//   $join = array(
				//       'users' => 'fb_support_desk_reply.user_id=users.id,left',
				//       'fb_simple_support_desk' => 'fb_support_desk_reply.user_id = fb_simple_support_desk.user_id,left'
				//   );
                $form = $this->config->item('institute_email'); 
				$select =array("users.email");
				$tomail = $this->basic->get_data($table,$where,$select,$join='');
				if(isset($tomail[0]['email']))
				$to = $tomail[0]['email']; 
				$subject = $this->config->item('product_name')." | ".$this->lang->line("support ticket");
				$mask = $subject;
				$html = 1;
				$this->_mail_sender($from, $to, $subject, $message, $mask, $html);
				$this->session->set_flashdata('success_message', 1);
				redirect('simplesupport/reply_support/'.$id.'', 'location');   
            }

       }
      

      

       


    }




}
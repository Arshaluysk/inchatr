<?php require_once("Home.php"); // including home controller

class Announcement extends Home
{

    public $user_id;
    public $download_id;
    
    /**
    * load constructor
    * @access public
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1) 
        {
            redirect('home/login_page', 'location');
        }
        $this->user_id=$this->session->userdata('user_id');
    }


    public function full_list()
    {
        $data['body'] = 'announcement/list';
        $data['page_title'] = $this->lang->line("Announcement List");     
        $this->_viewcontroller($data);
    }

    public function list_data()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') redirect('home/access_forbidden', 'location');
        
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 15;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
        $order_by_str = $sort." ".$order;

        $is_searched = trim($this->input->post('is_searched', true));
        $title = trim($this->input->post('search_title', true));
        $from_date = trim($this->input->post('search_from_date', true));
        if($from_date != '') $from_date = date('Y-m-d H:i:s', strtotime($from_date));
        $to_date = trim($this->input->post('search_to_date', true));
        if($to_date != '') $to_date = date('Y-m-d H:i:s', strtotime($to_date));

        if($is_searched) 
        {
            $this->session->set_userdata('announcement_title', $title);
            $this->session->set_userdata('announcement_from_date', $from_date);
            $this->session->set_userdata('announcement_to_date', $to_date);
        }

        $title = $this->session->userdata('announcement_title');
        $from_date = $this->session->userdata('announcement_from_date');
        $to_date = $this->session->userdata('announcement_to_date');

        $where_simple = array();
        if($title) $where_simple['title like'] = "%".$title."%";  
        if($from_date) $where_simple["Date_Format(created_at,'%Y-%m-%d %H:%i:%s') >="] = $from_date;    
        if($to_date) $where_simple["Date_Format(created_at,'%Y-%m-%d %H:%i:%s') <="] = $to_date;            

        if($this->session->userdata('user_type') != 'Admin') $where_simple["status"] = 'published';          
        
        $where = array('where' => $where_simple);
        $offset = ($page-1)*$rows;
        $result = array();

        $table = "announcement";
        $info = $this->basic->get_data($table, $where, $select = '', $join='', $limit = $rows, $start = $offset, $order_by = $order_by_str);

        for($i=0;$i<count($info);$i++)
        {
            $info[$i]["created_at"]=date("jS M, y H:i:s",strtotime($info[$i]["created_at"]));

            if($info[$i]["status"]=='published') $info[$i]["status"]='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("published").'</span>';
            else $info[$i]["status"]='<span class="label label-light"><i class="fa fa-file orange"></i> '.$this->lang->line("draft").'</span>';

            $info[$i]["action"]='<a target="_BLANK" href="'.base_url("announcement/details/".$info[$i]['id']).'" class="btn btn-outline-info" title="'.$this->lang->line("details").'"><i class="fa fa-eye"></i></a>';
            if($this->session->userdata('user_type') == 'Admin' && $this->is_demo != '1') $info[$i]["action"].='&nbsp;<a href="'.base_url("announcement/edit/".$info[$i]['id']).'" class="btn btn-outline-warning" title="'.$this->lang->line("edit").'"><i class="fa fa-edit"></i></a>&nbsp;<a href="'.base_url("announcement/delete/".$info[$i]['id']).'" class="are_you_sure btn btn-outline-danger" title '.$this->lang->line("delete").'><i class="fa fa-trash"></i></a>';
        }

        // echo $this->db->last_query();

        $total_rows_array = $this->basic->count_row($table, $where, $count = "id");
        $total_result = $total_rows_array[0]['total_rows'];
        echo convert_to_grid_data($info, $total_result);
    }

    public function add()
    {
        if($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        $data['body'] = 'announcement/add';
        $data['page_title'] = $this->lang->line("New Announcement");     
        $this->_viewcontroller($data);
    }

    public function add_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($this->session->userdata('user_type') != 'Admin') exit();
        if(!$_POST) exit();

        $title=$this->input->post('title',true);
        $description=$this->input->post('description',true);
        $status=$this->input->post('status',true);
        $created_at=date("Y-m-d H:i:s");

        if($this->basic->insert_data('announcement',array('title'=>$title,'description'=>$description,'status'=>$status,'created_at'=>$created_at,'user_id'=>$this->user_id)))
        $this->session->set_flashdata('success_message',1);    
        else $this->session->set_flashdata('error_message',1);    
    }

    public function edit($id=0)
    {
        if($id==0) exit();
        if($this->session->userdata('user_type') != 'Admin') redirect('home/login_page', 'location');
        $data['body'] = 'announcement/edit';
        $data['page_title'] = $this->lang->line("Edit Announcement");  
        $xdata=$this->basic->get_data("announcement",array('where'=>array('id'=>$id)));   
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];
        $this->_viewcontroller($data);
    }

    public function edit_action()
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if($this->session->userdata('user_type') != 'Admin') exit();
        if(!$_POST) exit();

        $id=$this->input->post('hidden_id',true);
        $title=$this->input->post('title',true);
        $description=$this->input->post('description',true);
        $status=$this->input->post('status',true);
        $created_at=date("Y-m-d H:i:s");

        if($this->basic->update_data('announcement',array('id'=>$id),array('title'=>$title,'description'=>$description,'status'=>$status,'user_id'=>$this->user_id)))
        $this->session->set_flashdata('success_message',1);    
        else $this->session->set_flashdata('error_message',1);    
    }

    public function delete($id=0)
    {
        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }
        
        if($id==0) exit();
        if($this->session->userdata('user_type') != 'Admin') exit();
        $this->basic->delete_data("announcement",array("id"=>$id));
        $this->session->set_flashdata('delete_success_message',1); 
        redirect('announcement/full_list','refresh');
    }

    public function details($id=0)
    {
        if($id==0) exit();
        $data['body'] = 'announcement/details';
        $data['page_title'] = $this->lang->line("Announcement");  
        $xdata=$this->basic->get_data("announcement",array('where'=>array('id'=>$id)));   
        if(!isset($xdata[0])) exit();
        $data['xdata']=$xdata[0];

        if(!$this->basic->is_exist("announcement_seen",array("announcement_id"=>$id,"user_id"=>$this->user_id)))
        $this->basic->insert_data("announcement_seen",array("announcement_id"=>$id,"user_id"=>$this->user_id,"seen_at"=>date("Y-m-d H:i:s")));

        $this->_viewcontroller($data);
    }



   

   
    
}
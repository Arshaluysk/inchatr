<?php
/*
Addon Name: Comment
Unique Name: Comment
Module ID: 84
Project ID: 2
Addon URI: http://www.myaddon.com
Author: Boss
Author URI: http://boss.com
Version: 1.0
Description: We are developeing our first add on
*/

require_once("application/controllers/Home.php"); // loading home controller

class Comment extends Home
{
	public $addon_data=array();
    public function __construct()
    {
        parent::__construct();

        // getting addon information in array and storing to public variable
        // addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
        //------------------------------------------------------------------------------------------
        $addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
        $this->addon_data=$this->get_addon_data($addon_path); 

        $this->user_id=$this->session->userdata('user_id'); // user_id of logged in user, we may need it

        // all addon must be login protected
        //------------------------------------------------------------------------------------------
        if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');          

        // if you want the addon to be accessed by admin and member who has permission to this addon
        //--------------------------------------s----------------------------------------------------
        if(isset($addon_data['module_id']) && is_numeric($addon_data['module_id']) && $addon_data['module_id']>0)
        {
            if($this->session->userdata('user_type') != 'Admin' && !in_array($addon_data['module_id'],$this->module_access))
            redirect('home/login_page', 'location');
        }

        // if you want the addon to be accessed only by admin
        //------------------------------------------------------------------------------------------
        // if ($this->session->userdata('user_type') != 'Admin')
        // redirect('home/login_page', 'location');

        // if you want the addon to be accessed only by member
        //------------------------------------------------------------------------------------------
        // if ($this->session->userdata('user_type') != 'Member')
        // redirect('home/login_page', 'location');

        // loding addon model here, no need to load if you dont need any
        //------------------------------------------------------------------------------------------
        //$this->load->model('comment_model');
    }


    public function index()
	{
        // loading add on views
        $data=array("body"=>"comment_views/first","page_title"=>$this->lang->line("comment addon"));
        $this->_viewcontroller($data);
	}

    public function activate()
    {
        if(!$_POST) exit();
        $is_free_addon=true; // true means free addon and false means paid
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        if(!$is_free_addon) //if paid add on then check license here
        {
            $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
        }

        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array
        (           
            0 =>array // first parent menu , you can add other parent menu in 1,2,3... index
            (
                'name' => $this->lang->line('comment'), // lang->line is used for multilingual support, you need to add the name's value [example:comment] in your add-on language file. 
                'icon' => 'fa fa-circle', // font awesome
                'url' => '#',   //it has child so no link here
                'is_external' => '0', // 0 means internal link and 1 means external
                'child_info' => array // no need to set this index if no child.child_info['have_child']=1 means it has child menus, 0 menas have no child menus. If it has child then child_info['child'] must to declare as array with child info
                    (
                        'have_child'=>'1', // parent has child menus, 0 means no child
                        'child'=>array // if status = 1 then you must add child array, other wise not need to set this index
                            (
                                0 => array // first child menu, you can add other child in 1,2,3...index
                                (
                                    'name'=>$this->lang->line('test menu 1'), // similar as parent name
                                    'icon'=>'fa fa-circle', // similar as parent icon
                                    'url' => 'comment/index', // this will take to http://thisappdomain.com/comment/index,  you can also use external link (http://xyz.com) but then you need to set is_external=1
                                    'is_external' => '0' // similar as parent is_external
                                ),
                                1 => array // first child menu, you can add other child in 1,2,3...index
                                (
                                    'name'=>$this->lang->line('test menu 2'), // similar as parent name
                                    'icon'=>'fa fa-circle', // similar as parent icon
                                    'url' => 'http://facebook.com', // similar as parent url
                                    'is_external' => '1' // similar as parent is_external
                                )
                            )
                    ),  
                'only_admin' => '0' , // 1 means only admin can access [only admin & only member can not be set 1 at the same time]  
                'only_member' => '0' // 1 means only member usercan access [only admin & only member can not be set 1 at the same time] 
            )            
        ); 

        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=array
        (
            0 =>"
            CREATE TABLE IF NOT EXISTS `add_on_test` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `campaign_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `page_id` int(11) NOT NULL,
              `client_thread_id` varchar(255) NOT NULL,
              `client_username` varchar(255) NOT NULL,
              `client_id` varchar(255) NOT NULL,
              `message_sent_id` varchar(255) NOT NULL,
              `sent_time` datetime NOT NULL,
              `lead_id` int(11) NOT NULL,
              `processed` enum('0','1') NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `campaign_id` (`campaign_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
            1 => "ALTER TABLE `add_on_test` ADD `page_name` VARCHAR(255) NOT NULL AFTER `page_id`;",
            2=>"ALTER TABLE `add_on_test` ADD `page_name1` VARCHAR(255) NOT NULL AFTER `page_id`;",
            3=>"ALTER TABLE `add_on_test` ADD `page_name2` VARCHAR(255) NOT NULL AFTER `page_id`;"

        ); 

        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code); 
    }


    public function deactivate()
    {        
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);         
    }

    public function delete()
    {        
        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

         // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (
            0 =>"DROP TABLE IF EXISTS `add_on_test`;"
        ); 
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

}
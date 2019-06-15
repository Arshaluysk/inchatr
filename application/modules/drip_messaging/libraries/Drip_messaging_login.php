<?php  
include(APPPATH."libraries/Facebook/autoload.php");

class Drip_messaging_login
{				
	public $database_id=""; 
	public $app_id="";
	public $app_secret="";		
	public $user_access_token="";
	public $fb;


	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->helper('my_helper');
		$this->CI->load->library('session');

		$this->CI->load->model('basic');
		$this->database_id=$this->CI->session->userdata("messenger_bot_login_database_id"); 

		if($this->CI->session->userdata("user_type")=="Admin" && ($this->database_id=="" || $this->database_id==0)) 
		{
			echo "<h3 align='center' style='font-family:arial;line-height:35px;margin:20px;padding:20px;border:1px solid #ccc;'>Hello Admin : No facebbok app configuration found. You have to  <a href='".base_url("messenger_bot/facebook_config")."'> add facebook app & login with facebook</a>. If you just added your first app and redirected here again then <a href='".base_url("home/logout")."'> logout</a>, login again and <a href='".base_url("messenger_bot/facebook_config")."'> go to this link</a> to login with facebook for your just added app.   </h3>";
			exit();
		}

		if($this->CI->session->userdata("user_type")=="Member" && ($this->database_id=="" || $this->database_id==0) && $this->CI->config->item("bot_backup_mode")==1) 
		{
			echo "<h3 align='center' style='font-family:arial;line-height:35px;margin:20px;padding:20px;border:1px solid #ccc;'>Hello User : No facebbok app configuration found. You have to  <a href='".base_url("messenger_bot/facebook_config")."'> add facebook app & login with facebook</a>. If you just added your first app and redirected here again then <a href='".base_url("home/logout")."'> logout</a>, login again and <a href='".base_url("messenger_bot/facebook_config")."'> go to this link</a> to login with facebook for your just added app.   </h3>";
			exit();
		}

		if($this->database_id != '')
		{
			$facebook_config=$this->CI->basic->get_data("messenger_bot_config",array("where"=>array("id"=>$this->database_id)));
			if(isset($facebook_config[0]))
			{			
				$this->app_id=$facebook_config[0]["api_id"];
				$this->app_secret=$facebook_config[0]["api_secret"];
				$this->user_access_token=$facebook_config[0]["user_access_token"];
				if (session_status() == PHP_SESSION_NONE) 
				{
				    session_start();
				}
		
				$this->fb = new Facebook\Facebook([
					'app_id' => $this->app_id, 
					'app_secret' => $this->app_secret,
					'default_graph_version' => 'v2.10',
					'fileUpload'	=>TRUE
					]);
			}
		}


	}
	
	public function app_initialize($messenger_bot_login_database_id){
	    
	    $this->database_id=$messenger_bot_login_database_id;
	    $facebook_config=$this->CI->basic->get_data("messenger_bot_config",array("where"=>array("id"=>$this->database_id)));
		if(isset($facebook_config[0]))
		{			
			$this->app_id=$facebook_config[0]["api_id"];
			$this->app_secret=$facebook_config[0]["api_secret"];
			$this->user_access_token=$facebook_config[0]["user_access_token"];
			if (session_status() == PHP_SESSION_NONE) 
			{
			    session_start();
			}
	
			$this->fb = new Facebook\Facebook([
				'app_id' => $this->app_id, 
				'app_secret' => $this->app_secret,
				'default_graph_version' => 'v2.10',
				'fileUpload'	=>TRUE
				]);
		}
		
	    
	}


	public function app_id_secret_check()
	{
		if($this->app_id == '' || $this->app_secret == '') return 'not_configured';
	}


	function get_page_review_status($post_access_token='')
	{
		$url="https://graph.facebook.com/v2.11/me/messaging_feature_review?access_token={$post_access_token}";
		$headers = array("Content-type: application/json");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
		$st=curl_exec($ch);  

		$result=json_decode($st,TRUE);

		return $result;
	}

	/*** Subscription based message sent  https://developers.facebook.com/docs/messenger-platform/send-messages/message-tags ***/
	function send_non_promotional_message_subscription($message='[]',$post_access_token='')
	{
		$url = "https://graph.facebook.com/v2.6/me/messages?access_token={$post_access_token}";

		$ch = curl_init();
		$headers = array("Content-type: application/json");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$message);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
		$st=curl_exec($ch);  

		$result=json_decode($st,TRUE);

		return $result;
	}

	/**LABEL MANAGING FUNCTIONS**/
	//calls fb api using post variable and json header
	function call_api_post($json='',$url='',$delete=false)
    {
    	$ch = curl_init();
    	$headers = array("Content-type: application/json");
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	if($delete)	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    	if($json!="") 
    	{
    		curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
    		curl_setopt($ch,CURLOPT_POST,1);
    	}
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
    	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
    	$st=curl_exec($ch);  
    	$result=json_decode($st,TRUE);
    	return $result;
    }

    //creates a label for a page
    function create_label($page_access_token="",$label="") //{"id": 1712444532121303}
    {
    	$url="https://graph.facebook.com/v2.11/me/custom_labels?access_token={$page_access_token}";
    	$json=json_encode(array("name"=>$label));
    	return $this->call_api_post($json,$url);
    }

    //get label list of a page
    function retrieve_label($page_access_token='')
    {
    	$url="https://graph.facebook.com/v2.11/me/custom_labels?fields=name&access_token={$page_access_token}";
    	return $this->call_api_post('',$url,false);
    }


}



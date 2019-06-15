<?php  
include(APPPATH."libraries/Facebook/autoload.php");

class Messenger_bot_login
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

	// Array([success] => 1)
	public function enable_bot($page_id='',$post_access_token='')
	{
		if($page_id=='' || $post_access_token=='') 
		{
			return array('success'=>0,'error'=>$this->CI->lang->line("something went wrong, please try again.")); 
			exit();
		}
		try 
		{
			$params=array();			
			$params['subscribed_fields']= array("messages","messaging_optins","messaging_postbacks","message_reads","messaging_referrals");			
			$response = $this->fb->post("{$page_id}/subscribed_apps",$params,$post_access_token);			
			$response = $response->getGraphObject()->asArray();
			$response['error']='';
			return $response;			
		} 
		catch (Exception $e) 
		{
			return array('success'=>0,'error'=>$e->getMessage());
		}
	}

	// Array([success] => 1)
	public function disable_bot($page_id='',$post_access_token='')
	{
		if($page_id=='' || $post_access_token=='') 
		{
			return array('success'=>0,'error'=>$this->CI->lang->line("something went wrong, please try again.")); 
			exit();
		}
		try 
		{
			$response = $this->fb->delete("{$page_id}/subscribed_apps",array(),$post_access_token);
			$response = $response->getGraphObject()->asArray();
			$response['error']='';
			return $response;			
		} 
		catch (Exception $e) 
		{
			return array('success'=>0,'error'=>$e->getMessage());
		}
	}

	// Array([result] => Successfully updated whitelisted domains)
	public function domain_whitelist($access_token='',$domain='')
	{
		if($access_token=='' || $domain=='') 
		{
			return array('status'=>'0','result'=>$this->CI->lang->line("something went wrong, please try again.")); 
			exit();
		}
		$url = "https://graph.facebook.com/v2.10/me/thread_settings?access_token={$access_token}";
		$domain_data=array("setting_type"=>"domain_whitelisting","whitelisted_domains"=>array($domain),"domain_action_type"=>"add");
		// $white_list_data='{
		// 	"setting_type" : "domain_whitelisting",
		// 	"whitelisted_domains" : ["https://mysitespy.net/"],
		// 	"domain_action_type": "add"
		// }';
		$white_list_data=json_encode($domain_data);

		$ch = curl_init();
		$headers = array("Content-type: application/json");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$white_list_data);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");   
		$st=curl_exec($ch);  
		$result=json_decode($st,TRUE);
		if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['status']='1';
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("something went wrong, please try again.");
			$result['status']='0';
		}
		return $result;
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


	function login_for_user_access_token($redirect_url="")
	{	
		$redirect_url=rtrim($redirect_url,'/');
		$helper = $this->fb->getRedirectLoginHelper();

		if($this->CI->config->item('has_read_insight_approval') == '') $has_read_insight_approval='1';
	    else $has_read_insight_approval = $this->CI->config->item('has_read_insight_approval');

		if($this->CI->is_messenger_bot_analytics_exist && $has_read_insight_approval=='1')
		$permissions = ['email','manage_pages','pages_show_list','pages_messaging','read_insights'];
		else $permissions = ['email','manage_pages','pages_show_list','pages_messaging'];


		$loginUrl = $helper->getLoginUrl($redirect_url, $permissions);
		$img=file_exists(FCPATH."assets/images/login_with_facebook.png") ? base_url("assets/images/login_with_facebook.png") : "https://mysitespy.net/envato_image/login_with_facebook.png";
		return '<a class="btn btn-primary btn-lg" style="background:#4267B2;border-color:#4267B2;" href="' . htmlspecialchars($loginUrl) . '"><i class="fa fa-facebook-official"></i> '.$this->CI->lang->line("login with facebook").'</a>';
	}


	public function login_callback($redirect_url="")
	{
		$redirect_url=rtrim($redirect_url,'/');
		$helper = $this->fb->getRedirectLoginHelper();
		try {
			$accessToken = $helper->getAccessToken($redirect_url);
			$response = $this->fb->get('/me?fields=id,name,email', $accessToken);

			$user = $response->getGraphUser()->asArray();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {

			$user['status']="0";
			$user['message']= $e->getMessage();
			return $user;

		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$user['status']="0";
			$user['message']= $e->getMessage();
			return $user;
		}

		$access_token	= (string) $accessToken;
		$access_token = $this->create_long_lived_access_token($access_token);

		$user["access_token_set"]=$access_token;

		return $user;
	}



	public function app_id_secret_check()
	{
		if($this->app_id == '' || $this->app_secret == '') return 'not_configured';
	}

	function access_token_validity_check(){

		$access_token=$this->user_access_token;
		$client_id=$this->app_id;
		$result=array();
		$url="https://graph.facebook.com/v2.8/oauth/access_token_info?client_id={$client_id}&access_token={$access_token}";

		$headers = array("Content-type: application/json");

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		$st=curl_exec($ch); 

		$result=json_decode($st,TRUE);

		if(!isset($result["error"])) return 1;
		else return 0;

	}



	function access_token_validity_check_for_user($access_token){

		$client_id=$this->app_id;
		$result=array();
		$url="https://graph.facebook.com/v2.8/oauth/access_token_info?client_id={$client_id}&access_token={$access_token}";

		$headers = array("Content-type: application/json");

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		$st=curl_exec($ch); 

		$result=json_decode($st,TRUE);

		if(!isset($result["error"])) return 1;
		else return 0;

	}



	public function create_long_lived_access_token($short_lived_user_token){

		$app_id=$this->app_id;
		$app_secret=$this->app_secret;
		$short_token=$short_lived_user_token;

		$url="https://graph.facebook.com/v2.6/oauth/access_token?grant_type=fb_exchange_token&client_id={$app_id}&client_secret={$app_secret}&fb_exchange_token={$short_token}";

		$headers = array("Content-type: application/json");

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		$st=curl_exec($ch); 
		$result=json_decode($st,TRUE);

		$access_token=isset($result["access_token"]) ? $result["access_token"] : "";

		return $access_token;

	}



	public function facebook_api_call($url){

		$headers = array("Content-type: application/json");

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

		$st=curl_exec($ch); 

		return  $results=json_decode($st,TRUE);	 
	}

	public function get_page_list($access_token="")
	{

		$error=false;
		try {

			$request = $this->fb->get('/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token&limit=400', $access_token);	
			$response = $request->getGraphList()->asArray();
			return $response;
		} catch(Facebook\Exceptions\FacebookResponseException $e) {

			$error=true;

		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			$error=true;
		}

		if($error)
		{
			$request = $this->fb->get('/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token&limit=400', $access_token);	
			$response = $request->getGraphList()->asArray();
			return $response;
		}

		
	}

	public function send_user_roll_access($app_id,$user_id, $user_access_token)
	{
		$url="https://graph.facebook.com/{$app_id}/roles?user={$user_id}&role=testers&access_token={$user_access_token}&method=post";
		$resuls = $this->run_curl_for_fb($url);
		return json_decode($resuls,TRUE);
	}


	public function run_curl_for_fb($url)
	{
		$headers = array("Content-type: application/json"); 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');  
		curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		$results=curl_exec($ch); 	   
		return  $results;   
	}




	public function get_general_content_with_checking_library($url,$proxy=""){
            
            $ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method

         
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $content = curl_exec($ch); // run the whole process 
            $response['content'] = $content;

            $res = curl_getinfo($ch);
            if($res['http_code'] != 200)
                $response['error'] = 'error';
            curl_close($ch);
            return json_encode($response);
            
    }


	public function debug_access_token($input_token){

		$url="https://graph.facebook.com/debug_token?input_token={$input_token}&access_token={$this->user_access_token}";
		$results= $this->run_curl_for_fb($url);
		return json_decode($results,TRUE);

	}

	/* Add get Started Button */
	public function add_get_started_button($post_access_token='')
	{
	
		$url = "https://graph.facebook.com/v2.6/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"get_started":{"payload":"GET_STARTED_PAYLOAD"}}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch);	 
	 	$result=json_decode($st,TRUE);
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("something went wrong, please try again.");
			$result['success']=0;
		}
		return $result;
	}

	/* Delete get Started Button */
	public function delete_get_started_button($post_access_token='')
	{
		$url = "https://graph.facebook.com/v2.6/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"fields":["get_started"]}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch); 
	 	$result=json_decode($st,TRUE);
	 
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("something went wrong, please try again.");
			$result['success']=0;
		}
		return $result;
	}

	public function set_welcome_message($post_access_token='',$welcome_message='')
	{
		if($welcome_message=='') return false;
	
		$url = "https://graph.facebook.com/v2.6/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data=array
		(
			'greeting'=>array(0=>array("locale"=>"default","text"=>$welcome_message))
		);
		// $get_started_data='{"greeting":[{"locale":"default","text":"'.$welcome_message.'"}]}';
		$get_started_data=json_encode($get_started_data);
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch);	 
	 	$result=json_decode($st,TRUE);
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("something went wrong, please try again.");
			$result['success']=0;
		}

		return $result;
	}

	public function unset_welcome_message($post_access_token='')
	{
		$url = "https://graph.facebook.com/v2.6/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"fields":["greeting"]}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
	 	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	 	curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt'); 
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
	 	$st=curl_exec($ch); 
	 	$result=json_decode($st,TRUE);
	 
	 	if(isset($result["result"])) 
		{
			$result["result"]=$this->CI->lang->line(trim($result["result"]));
			$result['success']=1;
		}
		if(isset($result["error"])) 
		{
			$result["result"]=isset($result["error"]["message"]) ? $result["error"]["message"] : $this->CI->lang->line("something went wrong, please try again.");
			$result['success']=0;
		}
		return $result;
	}




	/* Add Persistent Menu */
	public function add_persistent_menu($post_access_token='',$menu_content_json='')
	{
		$url = "https://graph.facebook.com/v2.6/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data=$menu_content_json;
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	 
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 
	 	curl_setopt($ch,CURLOPT_POST,1);
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
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

	/* Delete Persistent Menu */
	public function delete_persistent_menu($post_access_token='')
	{
		$url = "https://graph.facebook.com/v2.6/me/messenger_profile?access_token={$post_access_token}";
		$get_started_data='{"fields":["persistent_menu"]}';
	
		$ch = curl_init();
	 	$headers = array("Content-type: application/json");
	
	 	curl_setopt($ch, CURLOPT_URL, $url);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	 
	 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	 	curl_setopt($ch,CURLOPT_POSTFIELDS,$get_started_data); 
	 
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

}



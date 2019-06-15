<?php
require_once("application/controllers/Home.php"); // loading home controller
class Test extends  Home 
{
	public $user_id;
	public $postback_info;
	public $postback_array=array();
	public $postback_done=array();
    public function __construct()
    {
		parent::__construct();
		
		$this->load->model('basic');
		$this->load->library('session');
		$this->user_id=$this->session->userdata('user_id');
        set_time_limit(0);

       
    }
	
	
	function index()
	{
		//$this->translation();		
	}


	public function bot_view($page_table_id=106)
	{
		/**Get BOT settings information from messenger_bot table as base table. **/
		$where=array();
		$where['where'] = array('page_id'=> $page_table_id,'keywords !=' => "");
		$messenger_bot_info = $this->basic->get_data("messenger_bot",$where);
		$this->postback_info=array();
		$keyword_data=$this->get_child_info($messenger_bot_info,$page_table_id);

		/***	Get Started Information    ***/
		$where=array();
		$where['where'] = array('page_id'=> $page_table_id,'keyword_type'=>"get-started");
		$messenger_bot_info = $this->basic->get_data("messenger_bot",$where);
		$this->postback_info=array();
		$get_started_data=$this->get_child_info($messenger_bot_info,$page_table_id);



		$get_started_level=0;
		$get_started_data_copy=$get_started_data;
		foreach ($get_started_data_copy as $key => $value) 
		{
			$postback_array=isset($value['postback_info'])?$value['postback_info']:array();
			$last_postback_info=array_pop($postback_array);
			$get_started_level=isset($last_postback_info['level'])?$last_postback_info['level']:0; // maximum 
			break;
		}

		$this->postback_array=$postback_array; // 

		foreach ($this->postback_array as $key0 => $value)
		{		
			$this->postback_done=array(); // stores completed postback ids for current tree
			array_push($this->postback_done, $value['postback_id']); // this postback will not be operated second time if current tree is a recursive tree
			if(isset($value['child_postback']) && is_array($value['child_postback']))
			{
				foreach ($value['child_postback'] as $key1 => $value1)
				{
					if(!is_array($value1) && !in_array($value1, $this->postback_done))
					{
						$this->postback_array[$key0]['child_postback'][$key1]=isset($this->postback_array[$value1])?$this->postback_array[$value1]:array();
					}
					
					if($get_started_level>=2) // first level has been handles manually
					{
						for($i=2; $i<$get_started_level;$i++) 
						{ 
							$temp='key'.$i;
							$$temp=''; // initializing keys to avaoid undefined warning
							$phpcpmand=$this->set_nest($i);
							eval($phpcpmand);
						}
					}
								
				}
			}
		}
		foreach ($this->postback_array as $key => $value)
		{
			if($value['level']>1)
			unset($this->postback_array[$key]);			
		}

		echo "<pre>";
		// print_r($this->postback_done);
		// print_r($get_started_data);
		print_r($this->postback_array);
		// print_r($keyword_data);
		echo "</pre>";		
	}

	
	private function set_nest($current_level=0)
	{    	
		
		$output="";
		$isset="";
		$value="\$this->postback_array[\$key0]['child_postback'][\$key1]";
		for($times=2;$times<=$current_level;$times++) 
		{ 
			$isset=$value."['child_postback']";
			$value.="['child_postback'][\$key".$times."]";		
		}
		$output.="
		if(isset({$isset}) && is_array({$isset}))
		foreach({$isset} as \$key".$current_level." => \$value".$current_level.") 
		{
			if(!is_array(\$value".$current_level.") && !in_array(\$value".$current_level.",\$this->postback_done))
			{
				array_push(\$this->postback_done,\$value".$current_level.");
				{$value}=isset(\$this->postback_array[\$value".$current_level."])?\$this->postback_array[\$value".$current_level."]:array();
			}
		}";
		return $output; 
	}
	

	private function get_child_info($messenger_bot_info,$page_table_id)
	{

		foreach ($messenger_bot_info as $info) 
		{

			$message= $info['message'];
			$keyword_bot_id= $info['id'];
			$keywrods_list= $info['keywords'];
			$template_type=$info['template_type'];
			$this->postback_info[$keyword_bot_id]['keywrods_list']=$keywrods_list;


			/** Get all postback button id from json message **/

			$button_information= $this->get_button_information_from_json($message,$template_type);
			$matches[1]=isset($button_information['postback']) ? $button_information['postback'] : array();
			$web_url=isset($button_information['web_url']) ? $button_information['web_url'] : array();
			$phone_number=isset($button_information['phone_number']) ? $button_information['phone_number'] : array();
			$email=isset($button_information['email']) ? $button_information['email'] : array();
			$location=isset($button_information['location']) ? $button_information['location'] : array();
			$call_us=isset($button_information['call_us']) ? $button_information['call_us'] : array();
			


			$this->postback_info[$keyword_bot_id]['web_url']= $web_url;
			$this->postback_info[$keyword_bot_id]['phone_number']= $phone_number;
			$this->postback_info[$keyword_bot_id]['email']= $email;
			$this->postback_info[$keyword_bot_id]['location']= $location;
			$this->postback_info[$keyword_bot_id]['call_us']= $call_us;


			$k=0;
			$level=0;

			do
			{

				$level++;
				$this->get_postback_info($matches[1],$page_table_id,$keyword_bot_id,$level);

				$matches=array();

				foreach ($this->postback_info[$keyword_bot_id]['postback_info'] as $p_info) {

					$child=$p_info['child_postback'];

					if(empty($child)) continue;

					foreach ($child as $child_postback) {
						if(!isset($this->postback_info[$keyword_bot_id]['postback_info'][$child_postback]))	
							$matches[1][]=$child_postback;
					}
					
				}

				 $k++;

				if($k==7) break;


			}
			while(!empty($matches[1]));	
		}
	
		return $this->postback_info;

	}

	private function get_postback_info($matches,$page_table_id,$keyword_bot_id,$level)
	{
		//$postback_info_all=array();		
		foreach ($matches as $postback_match) 
		{

			$where['where'] = array('page_id'=> $page_table_id,'postback_id' =>$postback_match);
			/**Get BOT settings information from messenger_bot table as base table. **/
			$messenger_postback_info = $this->basic->get_data("messenger_bot",$where);

			$message= isset($messenger_postback_info[0]['message']) ? $messenger_postback_info[0]['message'] :"" ;

			$id= isset($messenger_postback_info[0]['id']) ? $messenger_postback_info[0]['id']:"";
			$is_template= isset($messenger_postback_info[0]['is_template']) ? $messenger_postback_info[0]['is_template']:"";
			$template_type= isset($messenger_postback_info[0]['template_type']) ? $messenger_postback_info[0]['template_type']:"";
			$bot_name= isset($messenger_postback_info[0]['bot_name']) ? $messenger_postback_info[0]['bot_name']:"";
			


			if($is_template=='1'){
				$postback_id_info=$this->basic->get_data('messenger_bot_postback',array('where'=>array('messenger_bot_table_id'=>$id,'is_template'=>'1')));
				$id= isset($postback_id_info[0]['id']) ? $postback_id_info[0]['id']:"";
			}


			

			preg_match_all('#payload":"(.*?)"#si', $message, $matches);

			$button_information= $this->get_button_information_from_json($message,$template_type);
			$matches[1]=isset($button_information['postback']) ? $button_information['postback'] : array();

			$web_url= isset($button_information['web_url']) ? $button_information['web_url'] : array();
			$web_url=isset($button_information['web_url']) ? $button_information['web_url'] : array();
			$phone_number=isset($button_information['phone_number']) ? $button_information['phone_number'] : array();
			$email=isset($button_information['email']) ? $button_information['email'] : array();
			$location=isset($button_information['location']) ? $button_information['location'] : array();

		
			$this->postback_info[$keyword_bot_id]['postback_info'][$postback_match] = array("id"=>$id,"child_postback"=>$matches[1],'postback_id'=>$postback_match,"level"=>$level,'is_template'=>$is_template,"web_url"=>$web_url,
				"phone_number" =>$phone_number,
				"email"		=>$email,
				"location"	=>$location,	
				'bot_name'	 =>$bot_name
				);
		}

		return $this->postback_info;
	}


	private function get_button_information_from_json($json_message,$template_type)
	{
		$full_message_array = json_decode($json_message,true);
		$result = array();

		if(!isset($full_message_array[1]))
		{
		  $full_message_array[1] = $full_message_array;
		  $full_message_array[1]['message']['template_type'] = $template_type;
		}


		for($k=1;$k<=3;$k++)
		{ 

		  $full_message[$k] = isset($full_message_array[$k]['message']) ? $full_message_array[$k]['message'] : array();

		  if(isset($full_message[$k]["template_type"]))
		    $full_message[$k]["template_type"] = str_replace('_', ' ', $full_message[$k]["template_type"]);  

		  for ($i=1; $i <=11 ; $i++) 
		  {
		    if(isset($full_message[$k]['quick_replies'][$i-1]['payload']))
		      $result['postback'][] = (isset($full_message[$k]['quick_replies'][$i-1]['payload'])) ? $full_message[$k]['quick_replies'][$i-1]['payload']:"";
		    if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_phone_number')
		      $result['phone_number'][] = "user_phone_number";
		    if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_email')
		      $result['email'][] = "user_email";
		    if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'location')
		      $result['location'][] = "location";


		    if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback')
		      $result['postback'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']:"";
		    if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url']))
		      $result['web_url'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'] : "";
		    if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number')
		      $result['call_us'][] = (isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number') ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload'] : "";
		  }


		  for ($j=1; $j <=5 ; $j++)
		  {
		    for ($i=1; $i <=3 ; $i++)
		    {
		      if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback')
		        $result['postback'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']:"";
		      if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url']))
		        $result['web_url'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'])) ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'] : "";
		      if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number')
		        $result['call_us'][] = (isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number') ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] : "";
		    }
		  }

		}

		return $result;
	}






	public function bot_export(){

		$page_table_id ="107";
		$where['where'] = array('page_id'=> $page_table_id);
		/**Get BOT settings information from messenger_bot table as base table. **/
		$messenger_bot_info = $this->basic->get_data("messenger_bot",$where);

		$bot_settings=array();

		$i=0;

		foreach ($messenger_bot_info as $bot_info) {

			
				$message_bot_id= $bot_info['id'];

				foreach ($bot_info as $key => $value) {

					if($key=='user_id' || $key=='page_id' || $key=='fb_page_id' || $key=='last_replied_at' || $key=='broadcaster_labels') continue;

					$bot_settings[$i]['message_bot'][$key]=$value;
				}


				/*** Get postback information from messenger_bot_postback table, it's from postback manager  ****/
				$where['where'] = array('messenger_bot_table_id'=> $message_bot_id,"template_id"=>"0");
				$messenger_postback_info = $this->basic->get_data("messenger_bot_postback",$where);
				
				$j=0;


				foreach ($messenger_postback_info as $postback_info) {

					$message_postback_id= $postback_info['id'];

					foreach ($postback_info as $key1 => $value1) {

					if($key1=='user_id' || $key1=='page_id' || $key1=='messenger_bot_table_id' || $key1=='last_replied_at' || $key1=='broadcaster_labels') continue;

					$bot_settings[$i]['message_bot']['postback_template_info'][$j][$key1]=$value1;

					}

				/** Get Child Postback from Post back Manager  whose BOT is already set.**/
				$where['where'] = array('template_id'=> $message_postback_id,);
				$messenger_postback_child_info = $this->basic->get_data("messenger_bot_postback",$where);

				$m=0;
				foreach ($messenger_postback_child_info as $postback_child_info) {
				
					

					foreach ($postback_child_info as $key2 => $value2) {
					
						if($key2=='user_id' || $key2=='page_id' || $key2=='last_replied_at' || $key2=='broadcaster_labels') continue;

					
						$bot_settings[$i]['message_bot']['postback_template_info'][$j]["postback_child"][$m][$key2]=$value2;

					}

					$m++;
					
				}

				$j++;
			}

			$i++;
		}


		/*** Get empty Postback from messenger_bot_postback table. The child postback for those bot isn't set yet . ***/

		$where['where'] = array('template_id'=> '0','messenger_bot_table_id'=>'0','is_template'=>'0','page_id'=>$page_table_id);
		$messenger_emptypostback_info = $this->basic->get_data("messenger_bot_postback",$where);

		$x=0;
		$empty_postback_settings=array();

		foreach ($messenger_emptypostback_info as $emptypostback_child_info) {

			foreach ($emptypostback_child_info as $key4 => $value4) {
					
			if($key4=='user_id' || $key4=='page_id' || $key4=='messenger_bot_table_id' || $key4=='last_replied_at' || $key4=='broadcaster_labels') continue;

				$empty_postback_settings[$x][$key4]=$value4;

			}

			$x++;
					
		}

		/****	Get Information of Persistent Menu ***/

		$persistent_menu_settings=array();

		$where['where'] = array('page_id'=>$page_table_id);
		$persistent_menu_info = $this->basic->get_data("messenger_bot_persistent_menu",$where);

		$y=0;

		foreach ($persistent_menu_info as $persistent_menu) {

			foreach ($persistent_menu as $key5 => $value5) {
				
				$persistent_menu_settings[$y][$key5] = $value5;
			}

			$y++;

		}


		/***Get general information from messenger_bot_page_info table***/


		$bot_general_info=array();

		$where['where'] = array('id'=>$page_table_id);
		$bot_page_general_info = $this->basic->get_data("messenger_bot_page_info",$where);

		foreach ($bot_page_general_info as $general_info) {

			$bot_general_info['started_button_enabled']= isset($general_info['started_button_enabled']) ? $general_info['started_button_enabled']:"";
			$bot_general_info['persistent_enabled']= isset($general_info['persistent_enabled']) ? $general_info['persistent_enabled']:"";
			$bot_general_info['enable_mark_seen']= isset($general_info['enable_mark_seen']) ? $general_info['enable_mark_seen']:"";
			$bot_general_info['enbale_type_on']= isset($general_info['enbale_type_on']) ? $general_info['enbale_type_on']:"";
			$bot_general_info['reply_delay_time']= isset($general_info['reply_delay_time']) ? $general_info['reply_delay_time']:"";

		}





		$full_bot_settings=array();
		$full_bot_settings['bot_settings']=$bot_settings;
		$full_bot_settings['empty_postback_settings']=$empty_postback_settings;		
		$full_bot_settings['persistent_menu_settings']=$persistent_menu_settings;		
		$full_bot_settings['bot_general_info']=$bot_general_info;	



		echo "<pre>";
			print_r($full_bot_settings);
		//	print_r($messenger_bot_info);




	}


	
  
  
 
  
  	function scanAll($myDir){

			$dirTree = array();
			$di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);
			
			foreach (new RecursiveIteratorIterator($di) as $filename) {
			
			$dir = str_replace($myDir, '', dirname($filename));
			//$dir = str_replace('/', '>', substr($dir,1));
			
			$org_dir=str_replace("\\", "/", $dir);
			
			
			if($org_dir)
			$file_path = $org_dir. "/". basename($filename);
			else
			$file_path = basename($filename);
			$dirTree[] = $file_path;
			
			}
			
			return $dirTree;
			
}

  public function lang_scanAll($myDir)
    {
        $dirTree = array();
        $di = new RecursiveDirectoryIterator($myDir,RecursiveDirectoryIterator::SKIP_DOTS);

        $i=0;
        foreach (new RecursiveIteratorIterator($di) as $filename) {

            $dir = str_replace($myDir, '', dirname($filename));
            // $dir = str_replace('/', '>', substr($dir,1));

            $org_dir=str_replace("\\", "/", $dir);

            if($org_dir)
                $file_path = $org_dir. "/". basename($filename);
            else
                $file_path = basename($filename);

            $file_full_path=$myDir."/".$file_path;
            $file_size= filesize($file_full_path);
            $file_modification_time=filemtime($file_full_path);

            $dirTree[$i]['file'] = $file_full_path;
            $i++;
        }
        return $dirTree;
    }


	
	function translation1(){
		
		$folder_path="application/";
		$all_directory= $this->scanAll($folder_path);
		
		$all_lang=array();
		
		foreach($all_directory as $dir){
			
			$content=file_get_contents($dir);
			preg_match_all('#\$this->lang->line\("(.*?)"\)#si', $content, $matches);	
			
			foreach($matches[1] as $line){
				$all_lang[]=strtolower($line);
			}
			
			preg_match_all('#\$this->lang->line\(\'(.*?)\'\)#si', $content, $matches1);	
			
			foreach($matches1[1] as $line){
				$all_lang[]=strtolower($line);
			}
			
		}
		
		/*** Get all existing language from language folder ***/
		
		$language_name_array=array("english","bengali","dutch","french","german","greek","italian","portuguese","russian","spanish","turkish","vietnamese");
		
		
		foreach($language_name_array as $language_name){
		
		$this->lang->is_loaded = array();
		$this->lang->language = array();
		
	 	$path=str_replace('\\', '/', APPPATH.'/language/'.$language_name); 
        $files=$this->lang_scanAll($path);
        foreach ($files as $key2 => $value2) 
        {
            $current_file=isset($value2['file']) ? str_replace('\\', '/', $value2['file']) : ""; //application/modules/addon_folder/language/language_folder/someting_lang.php
            if($current_file=="" || !is_file($current_file)) continue;
            $current_file_explode=explode('/',$current_file);
            $filename=array_pop($current_file_explode);
            $pos=strpos($filename,'_lang.php');
            if($pos!==false) // check if it is a lang file or not
            {
                $filename=str_replace('_lang.php', '', $filename); 
                $this->lang->load($filename, $language_name);
            }
        }      
		      
		
		$all_lang_prev_array=$this->lang->language;
		
		$all_lang_prev_array=array_change_key_case($all_lang_prev_array, CASE_LOWER);
		
		foreach($all_lang as $lang_index){
		
			if(isset($all_lang_prev_array[$lang_index]))
				$now_all_write_lang[$lang_index]=$all_lang_prev_array[$lang_index];
			else
				$now_all_write_lang[$lang_index]="";
		}
		
		
		/** Language that's exist but not found in current code **/
		
		$extra_lang= array_diff($all_lang_prev_array,$now_all_write_lang);
		
		$now_all_write_lang_merge = array_merge($now_all_write_lang, $extra_lang);
		
		asort($now_all_write_lang_merge);
		
		$lang_write_file=$path."/all_lang.php";
		
		/** Keep a backup for all_lang.php **/
		if(file_exists($lang_write_file)){
			$date=date("Y-m-d H-i-s");
			$write_path="backup_lang/{$language_name}/all_lang_{$date}.php";
			copy($lang_write_file,$write_path);
		}
		
		file_put_contents($lang_write_file, '<?php $lang = ' . var_export($now_all_write_lang_merge, true) . ';');
		
		
		$new_lang= array_diff($now_all_write_lang_merge,$all_lang_prev_array);
		
		echo $language_name.": New Line added : ". count($new_lang)."<br>";
		
/*		echo "<pre>";
			print_r($new_lang);*/
				
	}
		
		
	}



	function webhoook_connector(){

		  $response_raw=file_get_contents("php://input");
		 file_put_contents("fb.txt",$response_raw, FILE_APPEND | LOCK_EX);  


	}
	


	public function thirdparty_webhook_trigger($page_id="1673451449628432",$subscriber_id="1239013369452870",$trigger='trigger_email'){

            $where_simple['messenger_bot_thirdparty_webhook.page_id'] = $page_id;
            $where_simple['messenger_bot_thirdparty_webhook_trigger.trigger_option'] = $trigger;
            $where=array('where'=>$where_simple);
           
 			/**Get all connector webhook information**/

            $join = array('messenger_bot_thirdparty_webhook_trigger'=>"
            	messenger_bot_thirdparty_webhook.id=messenger_bot_thirdparty_webhook_trigger.webhook_id,left");

            $webhook_connector_info=$this->basic->get_data('messenger_bot_thirdparty_webhook', $where, $select='', $join, $limit='', $start='');

            /** Get subscriber information 	**/


            $where_simple=array();
            $where_simple['messenger_bot_subscriber.subscribe_id'] =$subscriber_id ;
            $where_simple['messenger_bot_subscriber.page_id'] = "$page_id";
            $where=array('where'=>$where_simple);
 
            $join = array('messenger_bot_quick_reply_email'=>"
            	messenger_bot_subscriber.subscribe_id=messenger_bot_quick_reply_email.fb_user_id,left");

            $subscriber_info=$this->basic->get_data('messenger_bot_subscriber', $where, $select='', $join, $limit='', $start='');

            /**Get subscriber Labels name from labels id***/

            $label_ids = $subscriber_info_rearrange['contact_group_id']=isset($subscriber_info[0]['contact_group_id']) ? $subscriber_info[0]['contact_group_id']:"";

            $label_ids_array = explode(',',$label_ids);
            $label_ids_array=array_filter($label_ids_array);

            $labels_name="";

            if(!empty($label_ids_array)){

            	$where=array("where_in"=>array("id"=>$label_ids_array));

	            $label_info = $this->basic->get_data("messenger_bot_broadcast_contact_group",$where);

	            foreach($label_info as $value)
	            {
	                $labels_name.=",".$value['group_name'];
	            }
            }

            $labels_name =trim($labels_name,",");

            foreach ($webhook_connector_info as $webhook_value) {
            
            	$webhook_url = isset($webhook_value['webhook_url']) ? $webhook_value['webhook_url']:"";
            	$webhook_id=isset($webhook_value['id']) ? $webhook_value['id']:"";
            	$post_variable = isset($webhook_value['variable_post']) ? $webhook_value['variable_post']:"";
            	$post_variable= explode(',',$post_variable);
            	$post_variable=array_filter($post_variable);

            	/**Making the variable for post/send ***/

            	$post_info=array();

            	foreach ($post_variable as $variable_info) {

            		if($variable_info=='psid')
            			$post_info[$variable_info]= isset($subscriber_info[0]['subscribe_id']) ? $subscriber_info[0]['subscribe_id']:"";
            		else if ($variable_info=='labels')
            			$post_info[$variable_info]= $labels_name;
            		else if($variable_info=='page_name')
            			$post_info[$variable_info]= isset($webhook_connector_info[0]['page_name']) ? $webhook_connector_info[0]['page_name']:"";
            		else
            			$post_info[$variable_info] = isset($subscriber_info[0][$variable_info]) ? $subscriber_info[0][$variable_info]:"";

            	}



            	/***	Send/Post Information to webhook url ***/

            	$post_info=json_encode($post_info);

            	$curl_response=$this->curl_send_data($webhook_url,$post_info);
            	
            	$curl_http_code= $curl_response['http_code'];
            	$curl_error= $curl_response['curl_error'];

			 	/***Insert into Activity table**/

			 	$insert_data=array();
			 	$insert_data['http_code'] = $curl_http_code; 
			 	$insert_data['curl_error'] = $curl_error; 
			 	$insert_data['webhook_id'] = $webhook_id; 
			 	$insert_data['post_time'] = date('Y-m-d H:i:s'); 
			 	$insert_data['post_data'] = $post_info; 

			 	$this->basic->insert_data('messenger_bot_thirdparty_webhook_activity',$insert_data);

			 	/***Delete last activity except recent 10 ***/

			 	$lastest_activity= $this->basic->get_data("messenger_bot_thirdparty_webhook_activity",$where=array('id'=>$webhook_id),$select='',$join='',$limit='10',$start=0,$order_by='id Desc');

			 	foreach ($lastest_activity as $last_activity_info) {
			 		$last_activity_ids[]=$last_activity_info['id'];
			 	}

			 	$this->db->where_not_in('id', $last_activity_ids);
				$this->db->delete('messenger_bot_thirdparty_webhook_activity'); 
            }



            echo "<pre>";

            	print_r($subscriber_info);



	}

	public function curl_send_data($webhook_url,$post_info){

				$ch = curl_init();
	 			$headers = array('Accept: application/json', 'Content-Type: application/json');
	 
	 			curl_setopt($ch, CURLOPT_URL, $webhook_url);
	 			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	 			curl_setopt($ch,CURLOPT_POST,1);
	 			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_info); 
	 			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
			 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
			 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			 	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3"); 
			 	$st=curl_exec($ch);	

			 	$curl_information =  curl_getinfo($ch);
			 	$curl_error="";
			 	if($curl_information['http_code']!='200'){
			 		$curl_error	= curl_error($ch);
			 	}

			 	$response['http_code']=$curl_information['http_code'];
			 	$response['curl_error']=$curl_error;

			 	return $response; 

	}




function enum_list(){

		$sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'messenger_bot' AND COLUMN_NAME = 'template_type'";
		$results=$this->basic->execute_query($sql);

		echo "<pre>";
			print_r($results);


		$sql="SHOW COLUMNS FROM messenger_bot WHERE Field = 'template_type'";

		$results=$this->basic->execute_query($sql);

		echo "<pre>";
			print_r($results);

			

		$enumList = explode(",", str_replace("'", "", substr($results[0]['Type'], 5, (strlen($results[0]['Type'])-6))));
		return $enumList;	



}
	
  
  
  
  
	
}



 ?>
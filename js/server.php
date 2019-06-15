<?php 
	header('Access-Control-Allow-Origin: *');
	$time=date("Y-m-d H:i:s");
	
	function result_encode($final_output){
	$final_output=str_replace("\\","",$final_output);
	$final_output=htmlspecialchars($final_output);
	$final_output=addslashes($final_output);
	return $final_output;
	}	



	function get_info_ipinfo(){
		
	}

	
	$connect = mysql_connect('localhost', 'root', 'konok41');
	
   if($connect) mysql_select_db('xeroneit_website_visitor', $connect);
   else die('Could not connect to the database: ' . mysql_error());
   mysql_query('SET CHARACTER SET utf8');
   mysql_query("SET SESSION collation_connection ='utf8_general_ci'") or die (mysql_error());
   
	$ip=$_POST['ip'];
	$website_code=$_POST['website_code'];
	$browser_name=$_POST['browser_name'];
	$browser_version=$_POST['browser_version'];
	$device=$_POST['device'];
	$mobile_desktop=$_POST['mobile_desktop'];
	$referrer=$_POST['referrer'];
	$current_url=$_POST['current_url'];
	$cookie_value=$_POST['cookie_value'];
	$is_new=$_POST['is_new'];
	$session_value=$_POST['session_value'];
	$browser_rawdata=$_POST['browser_rawdata'];
	
	
	
	/**Get Country code and country name***/
	
	if($ip){
		$rs=mysql_query("SELECT country_name,country_code
		FROM ip_country_list
		WHERE
		INET_ATON('".$ip."') BETWEEN ip_range_start_int AND ip_range_end_int
		LIMIT 1");
		
	 list($user_country,$country_code)=mysql_fetch_row($rs);
	 }
	 
	 if(!isset($user_country))
	 	$user_country="";
	
		if(!isset($country_code))
			$country_code="";
			
			
	
			
	$browser_rawdata=result_encode($browser_rawdata);
	
	 $q="Insert into visitor_data_history(domain_id,domain_code,ip,country,country_code,os,device,browser_name,browser_version,
										date_time,referrer,visit_url,cookie_value,is_new,session_value,browser_rawdata) 
										values('1','$website_code','$ip','$user_country','$country_code','$device','$mobile_desktop',
										'$browser_name','$browser_version','$time','$referrer','$current_url','$cookie_value',
										'$is_new','$session_value','$browser_rawdata')";
	
	mysql_query($q);
		
?>
<?php 

	header('Access-Control-Allow-Origin: *');
	$time=date("Y-m-d H:i:s");
	$connect = mysql_connect('localhost', 'root', 'konok41');
    if($connect) mysql_select_db('xeroneit_website_visitor', $connect);
    else die('Could not connect to the database: ' . mysql_error());
	
    mysql_query('SET CHARACTER SET utf8');
    mysql_query("SET SESSION collation_connection ='utf8_general_ci'") or die (mysql_error());
   
	$ip=$_POST['ip'];
	$website_code=$_POST['website_code'];
	$current_url=$_POST['current_url'];
	$cookie_value=$_POST['cookie_value'];
	$session_value=$_POST['session_value'];
	
	 $q="Update visitor_data_history set  last_engagement_time='$time' WHERE domain_code='$website_code' and visit_url='$current_url' 
		and cookie_value='$cookie_value' and session_value='$session_value' order by id desc limit 1";
		
	mysql_query($q);
		
?>
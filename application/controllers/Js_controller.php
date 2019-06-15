<?php
require_once("Home.php"); // including home controller

/**
* @category controller
* class Admin
*/

class Js_controller extends Home
{
	
	public function fb_chat_content(){
	
		header('Access-Control-Allow-Origin: *');
		$time=date("Y-m-d H:i:s");
		$website_code=$_GET['website_code'];
		
		/****	Get website and facebook page information *****/
		$where['where'] = array('domain_code'=>$website_code);
		$page_info = $this->basic->get_data('fb_chat_plugin',$where);
		
		$data['domain']=isset($page_info[0]['domain_name'])? $page_info[0]['domain_name']:"";
		$data['page']=isset($page_info[0]['fb_page_url'])? $page_info[0]['fb_page_url']:"";
		$this->load->view("facebook_chat/fb_chat",$data);
		
		
	}

	public function fb_chat_content_custom(){
	
		header('Access-Control-Allow-Origin: *');
		$time=date("Y-m-d H:i:s");
		$website_code=$_GET['website_code'];
		
		/****	Get website and facebook page information *****/
		$where['where'] = array('domain_code'=>$website_code);
		$page_info = $this->basic->get_data('fb_chat_plugin',$where);
		
		$data['domain']=isset($page_info[0]['domain_name'])? $page_info[0]['domain_name']:"";
		$data['header'] = isset($page_info[0]['message_header'])? $page_info[0]['message_header']:"facebook chat.";
		$data['page']=isset($page_info[0]['fb_page_url'])? $page_info[0]['fb_page_url']:"";
		$this->load->view("facebook_chat_custom/fb_chat",$data);
		
		
	}
	
	


}
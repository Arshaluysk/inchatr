<?php

require_once("Home.php"); // including home controller

class TestController extends Home
{



	public function index()
	{
		$data['body'] = 'test_template';
		$this->_viewcontroller($data);
		// $this->laod->view('test_template');
	}

	public function form_builder()
	{
		$data['body'] = 'form_builder';
		$this->_viewcontroller($data);	
	}

	public function options($f)
	{

		if($f == 'text.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\text.html');
		if($f == 'textarea.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\textarea.html');
		if($f == 'checkbox.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\checkbox.html');
		if($f == 'date_time.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\date_time.html');
		if($f == 'radio.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\radio.html');
		if($f == 'select_basic.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\select_basic.html');
		if($f == 'select_multiple.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\select_multiple.html');
		if($f == 'static_text.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\static_text.html');
		if($f == 'submit.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\submit.html');
		if($f == 'title.html')
		echo $f = file_get_contents(FCPATH.'plugins\form_builder\options\title.html');
		
	}




}
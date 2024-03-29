<?php require_once("Home.php"); // including home controller

class Stripe_action extends Home
{

    public function __construct()
    {
		parent::__construct();
		$this->load->library('Stripe_class');
		$this->load->model('basic');
        set_time_limit(0);
    }
	
	public function index()
    {
	
		$response= $this->stripe_class->stripe_payment_action();
		
		if($response['status']=='Error'){
			echo $response['message'];
			exit();
		}
		
		
		$receiver_email=$response['email'];
		$payment_amount=$response['charge_info']['amount']/100;
		$transaction_id=$response['charge_info']['balance_transaction'];
		$payment_date=date("Y-m-d",$response['charge_info']['created']) ;
		$country=isset($response['charge_info']['source']['country'])?$response['charge_info']['source']['country']:"";
		
		$stripe_card_source=isset($response['charge_info']['source'])?$response['charge_info']['source']:"";
		$stripe_card_source=json_encode($stripe_card_source);
		
		
		
		$user_id=$this->session->userdata('user_id');
		$package_id=$this->session->userdata('stripe_payment_package_id');
		
		$simple_where['where'] = array('user_id'=>$user_id);
        $select = array('cycle_start_date','cycle_expired_date');
		
        $prev_payment_info = $this->basic->get_data('transaction_history',$simple_where,$select,$join='',$limit='1',$start=0,$order_by='ID DESC',$group_by='');
		
		$prev_cycle_expired_date="";


       $config_data=array();
       $price=0;
       $package_data=$this->basic->get_data("package",$where=array("where"=>array("package.id"=>$package_id)));
       if(is_array($package_data) && array_key_exists(0, $package_data))
       $price=$package_data[0]["price"];
       $validity=$package_data[0]["validity"];

        $validity_str='+'.$validity.' day';
		
		foreach($prev_payment_info as $info){
			$prev_cycle_expired_date=$info['cycle_expired_date'];
		}
		
		if($prev_cycle_expired_date==""){
			 $cycle_start_date=date('Y-m-d');
			 $cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) < strtotime(date('Y-m-d'))){
			$cycle_start_date=date('Y-m-d');
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		else if (strtotime($prev_cycle_expired_date) > strtotime(date('Y-m-d'))){
			$cycle_start_date=date("Y-m-d",strtotime('+1 day',strtotime($prev_cycle_expired_date)));
			$cycle_expired_date=date("Y-m-d",strtotime($validity_str,strtotime($cycle_start_date)));
		}
		
		
		/** insert the transaction into database ***/
		
		 $insert_data=array(
                "verify_status" 	=>"",
                "first_name"		=>"",
				"last_name"			=>"",
				"paypal_email"		=>"STRIPE",
				"receiver_email" 	=>$receiver_email,
				"country"			=>$country,
				"payment_date" 		=>$payment_date,
				"payment_type"		=>"STRIPE",
				"transaction_id"	=>$transaction_id,
                "user_id"           =>$user_id,
				"package_id"		=>$package_id,
				"cycle_start_date"	=>$cycle_start_date,
				"cycle_expired_date"=>$cycle_expired_date,
				"paid_amount"	    =>$payment_amount,
				"stripe_card_source"=>$stripe_card_source
            );
			
			
        $this->basic->insert_data('transaction_history', $insert_data);
		
		/** Update user table **/
		$table='users';
		$where=array('id'=>$user_id);
		$data=array('expired_date'=>$cycle_expired_date,"package_id"=>$package_id);
		$this->basic->update_data($table,$where,$data);


		$product_short_name = $this->config->item('product_short_name');
        $from = $this->config->item('institute_email');
        $mask = $this->config->item('product_name');
        $where = array();
        $where['where'] = array('id'=>$user_id);
        $user_email = $this->basic->get_data('users',$where,$select='');

        $stripe_payment_confirmation_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_payment')),array('subject','message'));

        if(isset($stripe_payment_confirmation_email_template[0]) && $stripe_payment_confirmation_email_template[0]['subject'] !='' && $stripe_payment_confirmation_email_template[0]['message'] !='') {
        	$to = $user_email[0]['email'];
        	$url = base_url();
        	$subject = $stripe_payment_confirmation_email_template[0]['subject'];
        	$message = str_replace(array('#APP_SHORT_NAME#','#CYCLE_EXPIRED_DATE#','#APP_URL#','#APP_NAME#'),array($product_short_name,$cycle_expired_date,$url,$mask),$stripe_payment_confirmation_email_template[0]['message']);
        	//send mail to user
        	$this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);

        } else {

        	$to = $user_email[0]['email'];
        	$subject = "Payment Confirmation";
        	$message = "Congratulation,<br/> we have received your payment successfully. Now you are able to use {$product_short_name} system till {$cycle_expired_date}.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
        	//send mail to user
        	$this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);

        }  


        $stripe_new_payment_made_email_template = $this->basic->get_data("email_template_management",array('where'=>array('template_type'=>'stripe_new_payment_made')),array('subject','message'));

        if(isset($stripe_new_payment_made_email_template[0]) && $stripe_new_payment_made_email_template[0]['subject'] != '' && $stripe_new_payment_made_email_template[0]['message'] != '') {

        	$subject = $stripe_new_payment_made_email_template[0]['subject'];
        	$message = str_replace('#PAID_USER_NAME#',$user_email[0]['name'],$stripe_new_payment_made_email_template[0]['message']);

        } else {

        	$to = $from;
        	$subject = "New Payment Made";
        	$message = "New payment has been made by {$user_email[0]['name']}";
        	//send mail to admin
        	$this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);

        }
        
		
		$redirect_url=base_url()."payment/member_payment_history";
		redirect($redirect_url, 'refresh');
		
		
	}
}


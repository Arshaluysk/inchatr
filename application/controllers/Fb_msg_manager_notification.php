<?php

require_once("Home.php"); // including home controller

class Fb_msg_manager_notification extends Home
{

    public $user_id;    
    
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');   
        if($this->session->userdata('user_type') != 'Admin' && !in_array(83,$this->module_access))
        redirect('home/login_page', 'location'); 
        $this->user_id=$this->session->userdata('user_id');

        if($this->session->userdata("facebook_rx_fb_user_info")==0)
        redirect('facebook_rx_account_import/index','refresh');
    
        $this->load->library("fb_rx_login");
        $this->important_feature();
        $this->member_validity();        
    }


    public function index()
    {
      $this->page_notification_view();
    }


    public function page_notification_view()
    {
        $data['body'] = 'fb_messenger_manager/page_notifications';
        $data['page_title'] = $this->lang->line('notifications manager - page list');
        $this->_viewcontroller($data);
    }



    public function get_pages_notification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $where['where'] = array(
            'user_id' => $this->user_id,
            'facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info'),
            'msg_manager' => '1'
            );
        $page_list = $this->basic->get_data('facebook_rx_fb_page_info',$where,'','','','', $order_by='page_name asc');

        // for time zone checking
        $where = array();
        $where['where'] = array(
            'user_id' => $this->user_id,
            'facebook_rx_fb_user_info_id' => $this->session->userdata('facebook_rx_fb_user_info')
            );

        $business_account = $this->basic->get_data('fb_msg_manager_notification_settings',$where);
        if(!empty($business_account))
        {
            if($business_account[0]['time_zone'] != '')
                date_default_timezone_set($business_account[0]['time_zone']);
        }
        // end of time zone checking

        if(empty($page_list))
        {
            echo '<div class="well text-center"><h3 class="header_title red">'.$this->lang->line("you have not enabled messenger manager for any page yet !").'</h3></div>';
        }
        else
        {
            $str = '';

            $str .= '<h3 class="red refresh_button_holder"><div class="well text-center clearfix"><i class="fa fa-bullhorn"></i> '.$this->lang->line("notifications of inbox manager enabled pages").'<button id="refresh_button_notification" class="btn btn-success pull-right"><i class="fa fa-refresh"></i> '.$this->lang->line("refresh data").'</button></div></h3>';

            foreach($page_list as $value)
            {

                $str .= '<div class="well text-center"><h3 class="header_title"><img class="img-thumbnail" src="'.$value['page_profile'].'" alt="image"> '.$value['page_name'].' - '.$this->lang->line("notifications").'</h3></div>';
                
                $last_conversation = $this->fb_rx_login->read_notification($value['page_id'],$value['page_access_token']);

                if(empty($last_conversation))
                {
                    $str .= '<div class="alert alert-danger text-center"><h3>'.$this->lang->line("no data found").'</h3></div><br/>';
                }
                else
                {
                    $str .= '<div><script>
                                $j(document).ready(function() {
                                    $("#'.$value['id'].'").DataTable({"order": [[ 2, "desc" ]]});
                                }); 
                             </script>
                             <table id="'.$value['id'].'" class="table table-bordered table-hover table-stripped">
                                 <thead>
                                     <tr>
                                         <th><i class="red fa fa-bell-o"></i> '.$this->lang->line("message").'</th>
                                         <th>'.$this->lang->line("sent from").'</th>
                                         <th>'.$this->lang->line("sent time").'</th>
                                         <th>'.$this->lang->line("go to fb").'</th>
                                     </tr>
                                 </thead>
                                 <tbody>';
                    foreach($last_conversation as $data)
                    {
                        if(strlen($data['title']) > 250) $message_short = substr($data['title'], 0, 249).'..';
                        else $message_short = $data['title'];

                        $created_time = (array)$data['created_time'];
                        $created_time = $created_time['date']." UTC";
                        $created_time = date("Y-m-d H:i:s",strtotime($created_time));
                        $sender_name = isset($data['from']['name']) ? $data['from']['name'] : '';
                        $str .= '<tr>
                                    <td><p data-toggle="tooltip" title="'.$data['title'].'">'.chunk_split($message_short, 1050, '<br>').'</p></td>
                                    <td>'.$sender_name.'</td>                                    
                                    <td>'.$created_time.'</td>
                                    <td><a class="label label-info" href="'.$data['link'].'" target="_blank"><i class="fa fa-hand-o-right"></i> '.$this->lang->line("go to fb").'</a></td>
                                </tr>';
                    }
                    $str .= '</tbody>
                             </table>
                             </div><br/>';
                }
            }
            echo $str;
        }
    }




}
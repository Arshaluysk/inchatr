<?php 
$colorpos=strpos($loadthemebody,'red-light');
if($colorpos!==FALSE) $colorclass='label label-success'; 
else $colorclass='label label-danger';
?>
<div class="navbar-custom-menu">
  <ul class="nav navbar-nav">

  <li>
    <?php 
        // $select_lan="english";
        // if($this->session->userdata("selected_language")=="") $select_lan=$this->config->item("language");
        // else $select_lan=$this->session->userdata("selected_language");
        echo form_dropdown('language',$language_info,$this->language,'class="form-control  pull-right hidden-xs" id="language_change" style="width:100px;height:40px;margin-top:5px; font-size:10px;"');  ?>              
        <span class="red"><?php echo form_error('language'); ?></span>  
  </li>

  <li>
    <?php 
        if($this->uri->segment(1)!="drip_messaging" && $this->uri->segment(1)!="messenger_bot"  && $this->uri->segment(1)!="instagram_reply" && $this->uri->segment(1)!="pageresponse" && $this->uri->segment(1)!="messenger_engagement" && $this->uri->segment(1)!="messenger_broadcaster" && $this->uri->segment(1)!="vidcasterlive") :
          if($this->session->userdata('user_type') == 'Admin'|| in_array(65,$this->module_access)):
          echo form_dropdown('fb_rx_account_switch',$fb_rx_account_switching_info,$this->session->userdata("facebook_rx_fb_user_info"),'class="form-control  pull-right" id="fb_rx_account_switch" style="width:125px;height:40px;margin-top:5px;font-size:10px;"');
          endif;  
        endif;  
      ?>  
      <?php  // vidcaster account switch
        if($this->uri->segment(1)=="vidcasterlive") :
           if($this->session->userdata('user_type') == 'Admin'|| in_array(253,$this->module_access)):
           echo form_dropdown('vidcaster_fb_rx_account_switch',$vidcaster_fb_rx_account_switching_info,$this->session->userdata("vidcaster_facebook_rx_fb_user_info"),'class="form-control  pull-right" id="vidcaster_fb_rx_account_switch" style="width:125px;height:40px;margin-top:5px;font-size:10px;"');
          endif; 
        endif;  
      ?>  

  </li>


   <li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <i class="fa fa-bullhorn"></i>
      <span class="<?php echo $colorclass; ?>">
      <?php 
        $count=count($annoucement_data);
         echo $count;
         $count2=$count;
         if($count==0) $count2="0";
      ?>
      </span>
    </a>
    <ul class="dropdown-menu">            
      <?php 
      if($count>0) 
      { ?>
        <li class="header text-center"> (<?php echo $count2;?>) <?php echo $this->lang->line('unseen announcements'); ?></li>
        <li>
         <ul class="menu">
            <?php 
            foreach($annoucement_data as $row) 
            {             
              
              $image_src=base_url("upload/student/");
              ?>
              <li class="clearfix">
              <a class="clearfix" href="<?php echo site_url().'announcement/details/'.$row['id']; ?>">
               
               <div class="pull-left" style="margin:0;padding:0;">
                  <h6 style='padding-left:7px;margin:0'>  
                  <?php 
                    if(strlen($row['title'])>30)
                    echo substr($row['title'], 0, 30)."...";
                    else echo $row['title'];
                  ?>
                  <br/><small><i class="fa fa-clock-o"></i> <?php echo date("jS M, y H:i:s",strtotime($row['created_at']));?></small><br/>               
                  </h6>
               </div>
              </a>
              </li>
            <?php 
            } ?>            
        </ul>  
       </li> <?php
      } 
      else echo "<li>&nbsp;</li> <li class='text-center'>".$this->lang->line("no unseen announcements")."</li>";?>      

      <li>&nbsp;</li> 

      <li class="footer text-center"><b><a href="<?php echo site_url().'announcement/full_list';?>"><?php echo $this->lang->line('See all announcements');?></a></b></li>

      <li>&nbsp;</li> 
    </ul>
  </li>


  
    <?php 
      $pro_pic=base_url().'assets/images/logo.png';
    ?>
    <!-- User Account: style can be found in dropdown.less -->
    <li class="dropdown user user-menu">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
       <i class="fa fa-user"></i>
        <!-- <span><?php echo $this->session->userdata('username'); ?></span> -->
      </a>
      <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">          
          <br/>
          <br/>
          <center><img src="<?php echo $pro_pic;?>" class="img-responsive"/></center>
          <p>
            <?php echo $this->session->userdata('username'); ?>        
          </p>
          
        </li>
        <!-- Menu Body -->
        <!-- <li class="user-body">
          <div class="col-xs-4 text-center">
            <a href="#">Followers</a>
          </div>
          <div class="col-xs-4 text-center">
            <a href="#">Sales</a>
          </div>
          <div class="col-xs-4 text-center">
            <a href="#">Friends</a>
          </div>
        </li> -->
        <!-- Menu Footer-->
        <li class="user-footer" style="border-radius:3px">
          <div class="pull-left">
            <a href="<?php echo site_url('change_password/reset_password_form') ?>" class="btn btn-outline-danger"><i class="fa fa-key"></i> <?php echo $this->lang->line("change password"); ?></a>
          </div>
          <div class="pull-right">
            <a href="<?php echo site_url('home/logout') ?>" class="btn btn-outline-primary "><i class="fa fa-sign-out"></i> <?php echo $this->lang->line("logout"); ?></a>
          </div>
        </li>
      </ul>
    </li>
    <!-- Control Sidebar Toggle Button -->
    <!-- <li>
      <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
    </li> -->
  </ul>
</div>
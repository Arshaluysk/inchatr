<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo $this->config->item('product_name')." | ".$page_title;?></title>
    <?php $this->load->view('include/css_include_back');?>
    <?php $this->load->view('include/js_include_back');?>
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.png"> 
    <script type="text/javascript">   
      $(window).load(function() {
      // Animate loader off screen
      $(".preloading_body").fadeOut("slow");;
      });
    </script>

  </head>
  
  <body class="<?php echo $loadthemebody;?> sidebar-mini <?php if($this->uri->segment(2)=='bot_list' || $this->uri->segment(2)=='tree_view') echo 'sidebar-collapse';?>">
    <div class="wrapper">


      <div class="preloading_body"></div>


      <?php $this->load->view('admin/theme/header');?>


      <!-- for RTL support -->
      <?php 
      //if($this->config->item('language')=="arabic")  
      if($this->is_rtl) 
      { ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rtl/3.2.0-rc2/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>css/rtl.css" rel="stylesheet" type="text/css" />       
      <?php
      }
      ?>

      <!-- Left side column. contains the logo and sidebar -->
      <?php 
        $this->load->view('admin/theme/sidebar');
      ?>



      <!-- Content Wrapper. Contains page content --> 
      <div class="content-wrapper">
      <div class="clearfix"></div>
      
      <?php
      if($this->uri->segment(2)=="login_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { ?>  
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
           <?php echo "Google auth redirect URL : <span class='blue'>". base_url("home/google_login_back"); ?></span>
        </div>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
           <h4>Facebook URLs</h4><hr>
            <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
            <?php echo "Site URL : <span class='blue'>".base_url(); ?> </span><br/>
            <?php echo "Valid OAuth redirect URI : <span class='blue'>".base_url("home/fb_login_back"); ?></span><br/>
        </div>
      <?php } ?>

      <?php
      if($this->uri->segment(1)=="facebook_rx_config" && $this->uri->segment(2)=="index" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { 
        if($this->config->item('developer_access') != '1')
        {
      ?>
       <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
           <h4>Facebook URLs</h4><hr>
            <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
            <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
            <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy');?></span><br>
            <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
            <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
            <?php echo "<span class='blue'>".base_url("home/redirect_rx_link"); ?></span><br/>
            <?php echo "<span class='blue'>".base_url("facebook_rx_account_import/manual_renew_account"); ?></span><br/>
            <?php echo "<span class='blue'>".base_url("facebook_rx_account_import/redirect_custer_link"); ?></span><br/>
            <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
            <?php echo "Callback URL : <span class='blue'> ".base_url("home/central_webhook_callback"); ?></span><br/>
            <?php 
              if($this->config->item('central_webhook_verify_token') == '')
              {
                $verify_token= substr(uniqid(mt_rand(), true), 0, 10);
                include('application/config/my_config.php');
                if(!isset($config['central_webhook_verify_token']))
                {                  
                  $config['central_webhook_verify_token'] = $verify_token;
                  file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
                  redirect($this->uri->uri_string());
                }
              }
            ?>
            <?php echo "Verify Token : <span class='blue'>".$this->config->item('central_webhook_verify_token');?></span><br/>
        </div>
      <?php 
        }
        if($this->config->item('developer_access') == '1' && $this->session->userdata('user_type') == 'Admin')
        {         

          ?>
          <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4>In order to get Secret Code, Plese follow the steps below</h4><hr>
            <ol>
              <li>Please go to <a href="https://ac.getapptoken.com/home/login_page" target="_blank">https://ac.getapptoken.com/</a></li>
              <li>Sign up there providing your <a href="https://codecanyon.net/item/fb-inboxer-master-facebook-messenger-marketing-software/19578006?ref=xeroneitbd" target="_blank">FBInboxer</a> purchase code. Then you'll receive an email to activate your account.</li>
              <li>After login to that system, click the login with Facebook button.</li>
              <li>Then you'll get a secret code and use that secret code here and then click the login button here.</li>
              <li>You are done!</li>
            </ol>
          </div>
          <?php
        }
        
      }
      ?>

      <?php 
        if($this->session->userdata('secret_code_error') != '')
        {
          echo "<div><h4 style='margin:0'><div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$this->session->userdata('secret_code_error')."</div></h4></div>";
          $this->session->unset_userdata('secret_code_error');
        } 
      ?>
       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="twitter_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4><?php echo $this->lang->line("Twitter Redirect URLs:"); ?>  </h4><hr>
            
             <?php echo "<span class='blue'>".base_url("comboposter/twitter_login_callback");?></span><br/>
           
         </div>
       <?php } ?>

       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="tumblr_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4>Tumblr Redirect URLs: </h4><hr>
            
             <?php echo "<span class='blue'>".base_url("comboposter/tumblr_login_callback"); ?></span><br/>
           
         </div>
       <?php } ?>


       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="linkedin_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4>Linkedin Redirect URLs: </h4><hr>
            
             <?php echo "<span class='blue'>".base_url("comboposter/linkedin_login_callback"); ?></span><br/>
         </div>
       <?php } ?>

       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="medium_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4><?php echo $this->lang->line('Medium Redirect URLs'); ?>: </h4><hr>
            
             <?php echo "<span class='blue'>".base_url("comboposter/medium_login_callback"); ?></span><br/>
           
         </div>
       <?php } ?>

       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="add_pinterest_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4>pinterest Redirect URLs: </h4><hr>
            
             <?php echo "<span class='blue'>".base_url("comboposter/pinterest_login_callback"); ?></span><br/>
           
         </div>
       <?php } ?>

       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="reddit_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4><?php echo $this->lang->line("Reddit Redirect URLs"); ?>  : </h4><hr>
            
             <?php echo "<span class='blue'>".base_url("comboposter/reddit_callback"); ?></span><br/>
           
         </div>
       <?php } ?>

       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="wp_org_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4>Wordpress Redirect URLs: </h4><hr>
            
             <?php echo "<span class='blue'>".base_url("comboposter/wp_org_login_callback"); ?></span><br/>
           
         </div>
       <?php } ?>

       <?php
       if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="youtube_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4><?php echo $this->lang->line('Youtube and blogger Redirect URLs'); ?>: </h4><hr>
            
              <?php echo "<br>".$this->lang->line('Blogger redirect URL')." : <span class='blue'>".base_url('comboposter/blogger_login_callback');?></span><br>
               <?php echo "<br>".$this->lang->line('Youtube redirect URL')." : <span class='blue'>".base_url('comboposter/login_redirect'); ?></span>
             </div>
       <?php } ?>
      
       <?php
      if($this->uri->segment(1)=="messenger_bot" && $this->uri->segment(2)=="facebook_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { ?>
       <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
           <h4>Facebook URLs</h4><hr>
            <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
            <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
            <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy'); ?></span><br>
            <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
            <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
            <?php echo "<span class='blue'>".base_url("messenger_bot/login_callback"); ?></span><br/>
            <?php echo "<span class='blue'>".base_url("messenger_bot/refresh_login_callback"); ?></span><br/>
            <?php echo "<span class='blue'>".base_url("messenger_bot/user_login_callback"); ?></span><br/>
            <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
            <?php echo "Callback URL : <span class='blue'> ".base_url("home/central_webhook_callback"); ?></span><br/>
            <?php 
              if($this->config->item('central_webhook_verify_token') == '')
              {
                $verify_token= substr(uniqid(mt_rand(), true), 0, 10);
                include('application/config/my_config.php');
                if(!isset($config['central_webhook_verify_token']))
                {                  
                  $config['central_webhook_verify_token'] = $verify_token;
                  file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
                  redirect($this->uri->uri_string());
                }
              }
            ?>
            <?php echo "Verify Token : <span class='blue'>".$this->config->item('central_webhook_verify_token');?></span><br/>
        </div>
      <?php } ?> 

      <?php
      if($this->uri->segment(1)=="instagram_reply" && $this->uri->segment(2)=="facebook_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { ?>
       <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
           <h4>Facebook URLs</h4><hr>
            <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
            <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
            <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy'); ?></span><br>
            <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
            <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
            <?php echo "<span class='blue'>".base_url("instagram_reply/login_callback"); ?></span><br/>
            <?php echo "<span class='blue'>".base_url("instagram_reply/refresh_login_callback"); ?></span><br/>
           <!--  <?php echo "<span class='blue'>".base_url("instagram_reply/user_login_callback"); ?></span><br/> -->

            <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
            <?php echo "Callback URL :  <span class='blue'> ".base_url("instagram_reply/webhook_callback"); ?></span><br/>
            <?php echo "Verify Token : <span class='blue'>".$this->config->item('instagram_reply_verify_token');?></span><br/>
        </div>
      <?php } ?>

      
      <?php
      if($this->uri->segment(1)=="pageresponse" && $this->uri->segment(2)=="facebook_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { ?>
       <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
           <h4>Facebook URLs</h4><hr>
            <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
            <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
            <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy'); ?></span><br>
            <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
            <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
            <?php echo "<span class='blue'>".base_url("pageresponse/login_callback"); ?></span><br/>
            <?php echo "<span class='blue'>".base_url("pageresponse/refresh_login_callback"); ?></span><br/>
            <?php echo "<span class='blue'>".base_url("pageresponse/user_login_callback"); ?></span><br/>
            <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
            <?php echo "Callback URL : <span class='blue'> ".base_url("home/central_webhook_callback"); ?></span><br/>
            <?php 
              if($this->config->item('central_webhook_verify_token') == '')
              {
                $verify_token= substr(uniqid(mt_rand(), true), 0, 10);
                include('application/config/my_config.php');
                if(!isset($config['central_webhook_verify_token']))
                {                  
                  $config['central_webhook_verify_token'] = $verify_token;
                  file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
                  redirect($this->uri->uri_string());
                }
              }
            ?>
            <?php echo "Verify Token : <span class='blue'>".$this->config->item('central_webhook_verify_token');?></span><br/>
        </div>
      <?php } ?>

      <?php
      if($this->uri->segment(1)=="vidcasterlive" && $this->uri->segment(2)=="facebook_rx_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { ?>
       <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
           <h4>Facebook URLs</h4><hr>
           <?php echo "App Domain : <span  class='blue'>".get_domain_only(base_url()); ?></span><br/>
           <?php echo "<br>Site URL : <span  class='blue'>".base_url(); ?></span><br/>
           <?php echo "<br>Privacy Policy URL : <span  class='blue'>".base_url('home/privacy_policy'); ?></span>
           <?php echo "<br>Terms of Service URL : <span  class='blue'>".base_url('home/terms_use'); ?></span><br/>
           <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
           <?php echo "<span  class='blue'>".base_url("vidcasterlive/redirect_rx_link"); ?></span><br/>
           <?php echo "<span  class='blue'>".base_url("vidcasterlive/manual_renew_account"); ?></span><br/>
           <?php echo "<span  class='blue'>".base_url("vidcasterlive/redirect_custer_link"); ?></span><br/>
        </div>
      <?php } ?>

      <?php
       if($this->uri->segment(1)=="vidcasterlive" && $this->uri->segment(2)=="ytube_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
       { ?>
        <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <h4><?php echo $this->lang->line('Youtube Redirect URLs'); ?>: </h4><hr>
               <?php echo $this->lang->line('Youtube redirect URL')." : <span class='blue'>".base_url('vidcasterlive/ytube_login_redirect'); ?></span>
             </div>
       <?php } ?>

      <?php
      if($this->uri->segment(1)=="email_autoresponder" && $this->uri->segment(2)=="infusionsoft_app_setting" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
      { ?>
       <br>
       <div class="well" style="border-radius:0;margin:30px 30px 0 30px;">
            <?php echo "<br>Register Callback URL : "; ?> </span><br/>
            <?php echo "<span class='blue'>".base_url("email_autoresponder/infusionsoft_login_callback"); ?></span><br/>
        </div>
      <?php } ?>

      <?php 
        if($crud==1) 
      $this->load->view('admin/theme/theme_crud',$output); 
        else 
      $this->load->view($body);
      ?>  
      </div><!-- /.content-wrapper -->

      <!-- footer was here -->

      <!-- Control Sidebar -->
      <?php //$this->load->view('theme/control_sidebar');?>
      <!-- /.control-sidebar -->

      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->

    <!-- Footer -->
      <?php $this->load->view('admin/theme/footer');?>
    <!-- Footer -->    
   
  </body>
</html>


<?php include('application/views/include/theme_css.php'); ?>
<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.png"> 
  <title><?php echo $this->config->item('product_name')." | ".$page_title; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo base_url('assets/login_new/css/normalize.min.css')?>">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/v4-shims.css">
  <link rel="stylesheet" href="<?php echo base_url('assets/login_new/css/style.css');?>">

  <?php 
  //if($this->config->item("language")=="arabic")
  if($this->is_rtl) 
  { ?>
    <style>
    input{text-align:right !important;}
    </style>
  <?php }
  ?>  
</head>

<body style="padding-top:50px;">
  <a style="magin:0 auto;display:block;text-align: center;" href="<?php echo site_url();?>" ><img style="max-width: 200px;" src="<?php echo base_url();?>assets/images/logo.png" alt="<?php echo $this->config->item('product_name');?>" class="img-responsive center-block"></a>
  <div class="logmod">
  <div class="logmod__wrapper">
  

    <span class="logmod__close"><?php echo $this->lang->line("close"); ?></span>
    <div class="logmod__container">
      <ul class="logmod__tabs">
        <li data-tabtar="lgm-2"><a href=""><i class="fa fa-user-circle"></i> <?php echo $this->lang->line("sign up"); ?></a></li>
        <li data-tabtar="lgm-1"><a href="<?php echo base_url('home/login');?>"><i class="fa fa-sign-in-alt"></i> <?php echo $this->lang->line("login"); ?></a></li>
      </ul>
      <div class="logmod__tab-wrapper">
      <div class="logmod__tab lgm-2">
        <div class="logmod__heading">
          <!-- <span class="logmod__heading-subtitle"><?php echo $this->lang->line('You are just one click away'); ?></span> -->
          <br>
          <?php 
            if($this->session->userdata('reg_success') == 1) {
              echo "<div class='alert alert-success text-center'>".$this->lang->line("an activation code has been sent to your email. please check your inbox to activate your account.")."</div>";
              $this->session->unset_userdata('reg_success');
            }

            
            if($this->session->userdata('reg_success') == 'limit_exceed') {
              echo "<div class='alert alert-danger text-center'>".$this->lang->line("User limit exceeded for regular license. Please buy extended license for unlimited user management.")."</div>";
              $this->session->unset_userdata('reg_success');
            }
            if(form_error('name') != '' || form_error('email') != '' || form_error('confirm_password') != '' ||form_error('password')!="" ) 
            {
              $form_error="";
              if(form_error('name') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('name'));
              if(form_error('email') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('email'));
              if(form_error('password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('password'));
              if(form_error('confirm_password') != '') $form_error.=str_replace(array("<p>","</p>"), array("",""), form_error('confirm_password'));
              echo "<div class='alert alert-danger text-center'>".$form_error."</div>";
             
            }                
          ?>
          <?php 
          if(form_error('captcha')) 
          echo "<div class='alert alert-danger text-center'>".form_error('captcha')."</div>"; 
          else if($this->session->userdata("sign_up_captcha_error")!='')  
          { 
            echo "<div class='alert alert-danger text-center'>".$this->session->userdata("sign_up_captcha_error")."</div>"; 
            $this->session->unset_userdata("sign_up_captcha_error"); 
          } ?>

        </div> 
        <div class="logmod__form">
          <form accept-charset="utf-8" action="<?php echo site_url('home/sign_up_action');?>" method="post" class="simform">
            <div class="sminputs">
              <div class="input full">
                <label class="string optional" for="user-name"><?php echo $this->lang->line("name"); ?> *</label>
                <input class="string optional" required name="name" id="user-name" placeholder="" type="text" autofocus="yes" value="<?php echo set_value('name');?>" />
              </div>
            </div>
            <div class="sminputs">
              <div class="input full">
                <label class="string optional" for="user-email"><?php echo $this->lang->line("email"); ?> *</label>
                <input class="string optional" required name="email" id="user-email" placeholder="" type="email" value="<?php echo set_value('email');?>"/>
              </div>
            </div>
            <div class="sminputs">
              <div class="input string optional">
                <label class="string optional" for="user-pw"><?php echo $this->lang->line("password"); ?> *</label>
                <input class="string optional" required id="user-pw" name="password" placeholder="" type="password" value="<?php echo set_value('password');?>"/>
              </div>
              <div class="input string optional">
                <label class="string optional" for="user-pw-repeat"><?php echo $this->lang->line("confirm password");?> *</label>
                <input class="string optional" required id="user-pw-repeat" name="confirm_password" placeholder="" type="password" value="<?php echo set_value('confirm_password');?>"/>
              </div>
            </div>
            <div class="sminputs">
              <div class="input full">
                <label class="string optional" for="user-captcha"><?php echo $this->lang->line("captcha");?> *</label>
                <input class="string optional" required name="captcha" id="user-captcha" placeholder="<?php echo $num1. "+". $num2." = ?";?>" type="number"  value="" />
              </div>
            </div>
            <div class="simform__actions">
              <div class="special-con" style="width:100%"><a class="special"  role="link" target="_BLANK" href="<?php echo site_url();?>home/terms_use"><?php echo $this->lang->line("By clicking sign up, you agree to the terms of use set out by this site"); ?></a></div>
              <button class="sumbit" id="sign_up_button" style="width:100%" name="commit" type="submit"><i class="fa fa-user-circle"></i> <?php echo $this->lang->line("sign up"); ?></button>
            </div> 
          </form>
        </div> 
        <div class="logmod__alter">
        <br>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo base_url('assets/login_new/js/jquery.min.js');?>"></script>
<script  src="<?php echo base_url('assets/login_new/js/index.js');?>"></script>


<?php $this->load->view("include/fb_px"); ?> 
<?php $this->load->view("include/google_code"); ?> 

</body>

</html>

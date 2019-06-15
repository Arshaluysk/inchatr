<?php 
  $this->load->view("include/upload_js"); 

  $image_upload_limit = 1; 
  if($this->config->item('messengerbot_image_upload_limit') != '')
  $image_upload_limit = $this->config->item('messengerbot_image_upload_limit'); 

?>
<style>
  .button-holder span a{margin-top: 5px;}
  .button-holder .btn {text-align: left;padding:0;}
  .button-holder .btn .fa {font-size:16px;padding:10px 15px 10px 10px;width: 40px;}
  /* .button-holder .button_text{margin-top: 50px !important;} */
  hr{
     margin-top: 7px;
  }

  .custom-top-margin{
    margin-top: 20px;
  }

  .sync_page_style{
     margin-top: 8px;
  }
  /* .wrapper,.content-wrapper{background: #fafafa !important;} */
   .well{background: #fff;}
  .shadow
  {
    /* -moz-box-shadow:inset 0px 0px 4px <?php echo $THEMECOLORCODE; ?>;
    -webkit-box-shadow:inset 0px 0px 4px <?php echo $THEMECOLORCODE; ?>;
    box-shadow:inset 0px 0px 4px <?php echo $THEMECOLORCODE; ?>; */
    border: 1px solid #dedede;
  }

  .description-block{
    margin: 0px 0 !important;
  }


  .bot_settings
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .bot_settings:hover, .bot_settings:hover .fa
  {
    background: #089DE3;
    color: #FFF;
    border-color:  #089DE3;
  }
  .bot_settings .fa
  {
    color: #089DE3;
  }
  .enable_bot
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .enable_bot:hover, .enable_bot:hover .fa
  {
    background: #28a745;
    color: #FFF;
    border-color:  #28a745;
  }
  .enable_bot .fa
  {
    color: #28a745;
  }

  .disable_bot
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black;
  }
  .disable_bot .fa
  {
    color: #FA8E2A;
  }
  .disable_bot:hover, .disable_bot:hover .fa
  {
    background: #FA8E2A;
    color: #FFF;
    border-color:  #FA8E2A;
  }


  .subscribers_list
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .subscribers_list .fa
  {
    color: #17a2b8;
  }
  .subscribers_list:hover, .subscribers_list:hover .fa
  {
    background: #17a2b8;
    color: #FFF;
    border-color: #17a2b8;
  }

  .enable_start_button
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .enable_start_button .fa
  {
    color: #28a745;
  }
  .enable_start_button:hover, .enable_start_button:hover .fa
  {
    background: #28a745;
    color: #FFF;
    border-color: #28a745;
  }


  .disable_start_button
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .disable_start_button .fa
  {
    color: #FA8E2A;
  }
  .disable_start_button:hover, .disable_start_button:hover .fa
  {
    background: #FA8E2A;
    color: #FFF;
    border-color: #FA8E2A;
  }


  .persistent_menu,.import_bot,.export_bot
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .persistent_menu .fa,.import_bot .fa,.export_bot .fa
  {
    color: #089DE3;
  }
  .persistent_menu:hover, .persistent_menu:hover .fa, .import_bot:hover, .import_bot:hover .fa, .export_bot:hover, .export_bot:hover .fa
  {
    background: #089DE3;
    color: #FFF;
    border-color:#089DE3;
  }


  .enable_mark_seen
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .enable_mark_seen .fa
  {
    color: #28a745;
  }
  .enable_mark_seen:hover, .enable_mark_seen:hover .fa
  {
    background: #28a745;
    color: #FFF;
    border-color: #28a745;
  }


  .disable_mark_seen
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .disable_mark_seen .fa
  {
    color: #FA8E2A;
  }
  .disable_mark_seen:hover, .disable_mark_seen:hover .fa
  {
    background: #FA8E2A;
    color: #FFF;
    border-color: #FA8E2A;
  }


  .enable_typing_on
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black; 
  }
  .enable_typing_on .fa
  {
    color: #28a745;
  }
  .enable_typing_on:hover, .enable_typing_on:hover .fa
  {
    background: #28a745;
    color: #FFF;
    border-color:#28a745;
  }


  .disable_typing_on,.typing_on_settings,.chat_human_settings 
  {
    font-weight: normal !important;
    background: #FFFFFF; 
    color: black;
  }
  .typing_on_settings .fa,.chat_human_settings .fa
  {
    color: #089DE3;
  }
  .typing_on_settings:hover, .typing_on_settings:hover .fa,.chat_human_settings:hover, .chat_human_settings:hover .fa
  {
    background: #089DE3;
    color: #FFF;
    border-color: #089DE3;
  }


  .error_log_report
  {  
    font-weight: normal !important;  
    background: #FFFFFF; 
    color: black; 
  }
  .error_log_report .fa
  {
    color: #DD4B39;
  }
  .error_log_report:hover, .error_log_report:hover .fa
  {
    background: #DD4B39;
    color: #FFF;
    border-color: #DD4B39;
  }


  .email_list,.tree_view
  { 
    font-weight: normal !important;   
    background: #FFFFFF; 
    color: black; 
  }
  .email_list .fa,.tree_view .fa
  {
    color: #17a2b8;
  }
  .email_list:hover, .email_list:hover .fa,.tree_view:hover, .tree_view:hover .fa
  {
    background: #17a2b8;
    color: #FFF;
    border-color: #17a2b8;
  }


  .download_email, .download_phone
  {    
    background: #FFFFFF; 
    color: black; 
  }


  .delete_bot
  {
    font-weight: normal !important;    
    background: #FFFFFF; 
    color: black; 
  }
  .delete_bot .fa
  {
    color: #DD4B39;
  }
  .delete_bot:hover, .delete_bot:hover .fa
  {
    background: #DD4B39;
    color: #FFF;
    border-color: #DD4B39;
  }

  /*import bot modal section*/
  .radio_check{display:block;position:relative;padding-left:35px;cursor:pointer;font-size:22px;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}
  .radio_check input{position:absolute;opacity:0;cursor:pointer}
  .checkmark{position:absolute;top:0px;right:0;height:18px;width:18px;background-color:#ccc;}
  .radio_check:hover input~.checkmark{background-color:#eee}
  .radio_check input:checked~.checkmark{background-color:#2196F3}.checkmark:after{content:"";position:absolute;display:none}
  .radio_check input:checked~.checkmark:after{display:block}
  .radio_check .checkmark:after{top:5px;left:5px;width:8px;height:8px;border-radius:50%;background:#fff}
  .template_sec{border:1px solid #dcd7d7;border-top-right-radius:6px;border-bottom-right-radius:6px;padding-right:0;overflow: hidden;}
  .template_img_section img{border-top-left-radius:6px;border-bottom-left-radius:6px}
  .template_body_section{height:94px;padding:3px 10px 0 10px;border-left:none}
  .description_section{font-size:10px;text-align:justify}

  /* @media screen and (max-width:600px)
  {
    .template_sec{margin-left:-55px;border:1px solid #dcd7d7;border-top-right-radius:6px;border-bottom-right-radius:6px}
    .template_img_section img{width:88px;cursor:pointer;border-top-left-radius:6px;border-bottom-left-radius:6px}
    .template_body_section{height:94px;padding:3px 8px 0 9px;border-left:none}
    .checkmark{position:absolute;top:-9px;left:268px;height:20px;width:20px;background-color:#eee;border-bottom-left-radius:6px;border:1px solid #dcd7d7}
} */

</style>

<?php if($this->session->flashdata('menu_success')===1) { ?>
  <div class="alert alert-success text-center" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been published successfully.");?></div>
<?php } ?>

<?php if($this->session->flashdata('perrem_success')===1) { ?>
  <div class="alert alert-success text-center" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been removed successfully.");?></div>
<?php } ?>

<?php if($this->session->flashdata('perrem_success')===0) { ?>
  <div class="alert alert-danger text-center"><i class="fa fa-remove"></i> <?php echo $this->session->flashdata('perrem_message');?></div>
<?php } ?>

<?php if($this->session->flashdata('bot_action')!='') { ?>
  <div class="alert alert-success text-center"><i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('bot_action');?></div>
<?php } ?>


<?php if(empty($page_info)){ ?>
  <div class="well well_border_left">
    <h4 class="text-center blue"> <i class="fa fa-remove"></i><?php echo $this->lang->line("you have no bot enabled page");?> <a class='orange' href="<?php echo base_url('messenger_bot/account_import');?>"><?php echo $this->lang->line('Enable Bot');?></a></h4>
  </div>
  <?php }else{ ?>
  <div class="well well_border_left">
    <h4 class="text-center blue"> <i class="fa fa-robot"></i> <?php echo $this->lang->line("Bot Settings");?> : <?php echo $this->lang->line("page list");?></h4>
  </div>

  <div class="container-fluid">
    <div class="row" style="padding:0 10px;">
      <?php $i=0; foreach($page_info as $value) : ?>
      <div class="col-xs-12 col-sm-6 col-md-4" style="padding:10px;">

        <div class="box box-solid" style="margin:0">
          <div class="box-body">
              <h4 style="background-color:#f7f7f7; font-size: 18px; text-align: center; padding: 7px 10px; margin-top: 0;">
                  <a style="color:#000" target="_BLANK" href="https://facebook.com/<?php echo $value['page_id'];?>"><i class="fa fa-newspaper"></i> <?php echo $value['page_name'];?></a>
                  <?php if($value['bot_enabled']=='1') echo '<i class="fa fa-check-circle green" title="'.$this->lang->line("active").'"></i>'; else  echo '<i class="fa fa-ban orange" title="'.$this->lang->line("inactive").'"></i>';?>
              </h4>
              <div class="media">
                  <div class="media-left">
                      <?php $profile_picture=$value['page_profile']; ?>
                      <a href="https://facebook.com/<?php echo $value['page_id'];?>" target="_BLANK" class="ad-click-event">
                          <img src="<?php echo $profile_picture;?>" class="media-object img-circle" style="width: 80px;height: auto;">
                      </a>
                  </div>
                  <div class="media-body" style="padding:15px 0 0 10px;">
                      <div class="clearfix">
                          
                          <?php if($this->is_messenger_bot_analytics_exist) 
                          { ?>
                            <p class="pull-right" style="margin-top: 14px;">
                                <a href="<?php echo base_url('messenger_bot_analytics/result/'.$value['id']); ?>" target="_BLANK" class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-chart-pie"></i> <?php echo $this->lang->line('Analytics'); ?>
                                </a>
                            </p>
                          <?php 
                          } ?>

                          <h4 style="margin-top: 0;font-size: 17px;"><?php echo $value['account_name'];?></h4>

                          <p><?php echo $value['page_name'];?></p>
                          
                      </div>
                  </div>
              </div>

              <div class="clearrix"></div>
              <div class="row button-holder row-centered" style="padding:20px 12px">                    

                <div class="col-xs-12 col-sm-6 col-md-6" style="padding:2px;">

                    <span class="inline" style="text-transform: none;"><a target="_BLANK" href="<?php echo base_url('messenger_bot/bot_settings/'.$value['id']);?>" class="btn btn-block btn-sm bot_settings shadow"><i class="fa fa-robot bot_settings_icon"></i> <span class="button_text"><?php echo $this->lang->line("Bot Settings");?></span></a></span>
                    <?php if($value['bot_enabled'] == '1') : ?>
                    <span class="inline" style="text-transform: none;"><a restart='0' bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>"  class="btn btn-block btn-sm disable_bot shadow"><i class="fa fa-ban"></i> <span class="button_text"><?php echo $this->lang->line("Disable Bot");?></span></a></span>

                    <span class="inline" style="text-transform: none;"><a bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>"  class="btn btn-block btn-sm delete_bot shadow" already_disabled="no"><i class="fa fa-minus-circle"></i> <span class="button_text"><?php echo $this->lang->line("Delete Bot");?></span></a></span>
                    <?php else : ?>
                    <span style="text-transform: none;"> <a restart='1' bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_bot shadow"><i class="fa fa-sync-alt"></i> <span class="button_text"><?php echo $this->lang->line("Re-start Bot") ?></span></a></span>

                    <span class="inline" style="text-transform: none;"><a bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>"  class="btn btn-block btn-sm delete_bot shadow" already_disabled="yes"><i class="fa fa-minus-circle"></i> <span class="button_text"><?php echo $this->lang->line("Delete Bot");?></span></a></span>
                    <?php endif; ?>

                    <span class="inline" style="text-transform: none;"><a table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm email_list shadow"><i class="fa fa-user-circle"></i> <span class="button_text"><?php echo $this->lang->line("Contact & Location");?></span></a></span>

                    <?php if($this->is_messenger_bot_import_export_exist) { ?>
                      <span class="inline" style="text-transform: none;"><a target="_BLANK" href="<?php echo base_url('messenger_bot/tree_view/'.$value['id']);?>" class="btn btn-block btn-sm tree_view shadow"><i class="fa fa-sitemap"></i> <span class="button_text"><?php echo $this->lang->line("Tree View"); ?></span></a></span>
                    <?php } ?>
                   
                    <?php if($this->is_messenger_bot_import_export_exist) { ?>                             
                          <span class="inline" style="text-transform: none;"><a href="" class="export_bot btn btn-block btn-sm shadow" table_id="<?php echo $value['id'];?>"><i class="fa fa-file-export"></i> <span class="button_text"><?php echo $this->lang->line("Export"); ?></span></a></span>
                    <?php } ?>

                     <span class="inline" style="text-transform: none;"><a id ="<?php echo $value['user_id']."-".$value['page_id']?>" class="user_details_modal_bot btn btn-block btn-sm subscribers_list shadow"><i class="fa fa-group"></i> <span class="button_text"><?php echo $this->lang->line("Subscribers");?></span></a></span>
                
                </div>

                <?php 
                $error_log_button = '<span class="inline" style="text-transform: none;"><a target="_BLANK"  table_id="'.$value['id'].'" class="btn btn-block btn-sm error_log_report shadow"><i class="fa fa-bug"></i> <span class="button_text">'.$this->lang->line("error log report").'</span>';
                 if(array_key_exists($value['id'], $error_record)) 
                  $error_log_button.='<span class="badge" style="background: red; color: white;">'.$error_record[$value['id']].'</span>';
                 $error_log_button.='</a></span>';
                 ?>

                <div class="col-xs-12 col-sm-6 col-md-6" style="padding:2px;">

                    <?php
                    if($value['started_button_enabled']=='0')
                    { ?>
                      <span class="inline" style="text-transform: none;"><a sbutton-status="<?php echo$value['started_button_enabled']; ?>" welcome-message="<?php echo htmlspecialchars($value['welcome_message']); ?>" sbutton-enable="<?php echo $value['id'];?>" id="sbutton-<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_start_button shadow"><i class="fa fa-check-circle"></i> <span class="button_text"><?php echo $this->lang->line("Get Started Settings") ?></span></a></span>
                    <?php
                    }
                    else
                    { ?>
                      <span class="inline" style="text-transform: none;"><a sbutton-status="<?php echo$value['started_button_enabled']; ?>" welcome-message="<?php echo htmlspecialchars($value['welcome_message']); ?>" sbutton-enable="<?php echo $value['id'];?>" id="sbutton-<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_start_button shadow"><i class="fa fa-ban orange"></i> <span class="button_text"><?php echo $this->lang->line("Get Started Settings") ?></span></a></span>
                    <?php
                    }  
                    ?>
                    <?php
                    if($value['persistent_enabled']=='1') $persitent_class="fa fa-bars"; 
                    else $persitent_class="fa fa-cog"; 
                    ?>
                    <span class="inline <?php if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access)) echo " hidden"; ?>" style="text-transform: none;"><a href="<?php echo base_url("messenger_bot/persistent_menu_list/".$value['id']);?>" pbutton-enable="<?php echo $value['id'];?>" id="pbutton-<?php echo $value['id'];?>" class="btn btn-block btn-sm persistent_menu shadow"><i class="<?php echo $persitent_class;?>"></i> <span class="button_text"><?php echo $this->lang->line("Persistent Menu") ?></span></a></span> 

                    <?php if($value['enable_mark_seen']=='0') : ?>
                    <span class="inline" style="text-transform: none;"><a target="_BLANK" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_mark_seen shadow"><i class="fa fa-check-circle"></i> <span class="button_text"><?php echo $this->lang->line("enable mark seen");?></span></a></span>
                    <?php else : ?>
                    <span class="inline" style="text-transform: none;"><a target="_BLANK" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm disable_mark_seen shadow"><i class="fa fa-ban"></i> <span class="button_text"><?php echo $this->lang->line("disable mark seen");?></span></a></span>
                    <?php endif; ?>

                    <span class="inline" style="text-transform: none;"><a target="_BLANK" data-status="<?php echo $value['enbale_type_on'];?>" data-delay="<?php echo $value['reply_delay_time'];?>" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm typing_on_settings shadow"><i class="fa fa-keyboard"></i> <span class="button_text"><?php echo $this->lang->line("typing on settings");?></span></a></span>

                    <?php
                    $current_email=$value['chat_human_email'];
                    ?>

                    <span class="inline" style="text-transform: none;"><a target="_BLANK" data-email="<?php echo $current_email;?>" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm chat_human_settings shadow"><i class="fa fa-headset"></i> <span class="button_text"><?php echo $this->lang->line("Human Chat Settings");?></span></a></span>                   
              

                    <?php if($this->is_messenger_bot_import_export_exist) { ?>
                        <span class="inline" style="text-transform: none;"><a href="" class="import_bot btn btn-block btn-sm shadow" table_id="<?php echo $value['id'];?>"><i class="fa fa-file-import"></i> <span class="button_text"><?php echo $this->lang->line("Import"); ?></span></a></span>
                    <?php } ?>

                    <!-- if tree view menu exists then error menu will be half column else it will be full column -->
                    <?php if($this->is_messenger_bot_import_export_exist) echo $error_log_button; ?>
                    
                </div>                 


                <!-- if tree view menu doe snot exist then error menu will be full column else it will be half column -->
                <?php if(!$this->is_messenger_bot_import_export_exist) 
                { ?>                      
                  <div class="col-xs-6 col-centered" style="padding-left:0;padding-right: 0;">
                    <?php echo $error_log_button; ?>
                  </div>
                  <?php 
                } ?>

              </div>
          </div>
      </div>

        <!-- <div class="box box-widget widget-user" style="padding:20px;margin-bottom:0;">                
            <div class="row">                   
              <div class="col-xs-5 col-md-4">                    
                <?php $profile_picture=$value['page_profile']; ?>
                <div><img class="img-thumbnail" src="<?php echo $profile_picture;?>" style="width: 90px;padding:1px;"></div>
              </div>  
              <div class="col-xs-7 col-md-8">
                <div class="widget-user-header text-right" style="padding:20px 0 0 0;">
                  <h4 class="widget-user-username">
                    <a target="_BLANK" href="https://facebook.com/<?php echo $value['page_id'];?>"><i class="fa fa-newspaper"></i> <?php echo $value['page_name'];?></a>
                    <?php if($value['bot_enabled']=='1') echo '<i class="fa fa-check-circle green" title="'.$this->lang->line("active").'"></i>'; else  echo '<i class="fa fa-ban orange" title="'.$this->lang->line("inactive").'"></i>';?>
                  </h4>
                  <h5 class="widget-user-desc"><i class="fa fa-user"></i> <?php echo $value['account_name'];?></h5>
                  <a target="_BLANK" href="<?php echo base_url('messenger_bot/bot_settings/'.$value['id']);?>"><i class="fa fa-robot bot_settings_icon"></i> <span class="button_text"><?php echo $this->lang->line("Bot Settings");?></span></a>
                </div>
                
              </div>                             
            </div>

            <div class="box-footer" style="border:none;padding-top:30px !important;">

              <div class="row button-holder row-centered">                    

                <div class="col-xs-12 col-sm-6 col-md-6" style="padding:2px;">

                    <span class="inline" style="text-transform: none;"><a target="_BLANK" href="<?php echo base_url('messenger_bot/bot_settings/'.$value['id']);?>" class="btn btn-block btn-sm bot_settings shadow"><i class="fa fa-robot bot_settings_icon"></i> <span class="button_text"><?php echo $this->lang->line("Bot Settings");?></span></a></span>
                    <?php if($value['bot_enabled'] == '1') : ?>
                    <span class="inline" style="text-transform: none;"><a restart='0' bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>"  class="btn btn-block btn-sm disable_bot shadow"><i class="fa fa-ban"></i> <span class="button_text"><?php echo $this->lang->line("Disable Bot");?></span></a></span>

                    <span class="inline" style="text-transform: none;"><a bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>"  class="btn btn-block btn-sm delete_bot shadow" already_disabled="no"><i class="fa fa-minus-circle"></i> <span class="button_text"><?php echo $this->lang->line("Delete Bot");?></span></a></span>
                    <?php else : ?>
                    <span style="text-transform: none;"> <a restart='1' bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_bot shadow"><i class="fa fa-sync-alt"></i> <span class="button_text"><?php echo $this->lang->line("Re-start Bot") ?></span></a></span>

                    <span class="inline" style="text-transform: none;"><a bot-enable="<?php echo $value['id'];?>" id="bot-<?php echo $value['id'];?>"  class="btn btn-block btn-sm delete_bot shadow" already_disabled="yes"><i class="fa fa-minus-circle"></i> <span class="button_text"><?php echo $this->lang->line("Delete Bot");?></span></a></span>
                    <?php endif; ?>

                    <span class="inline" style="text-transform: none;"><a table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm email_list shadow"><i class="fa fa-user-circle"></i> <span class="button_text"><?php echo $this->lang->line("Contact & Location");?></span></a></span>

                    <?php if($this->is_messenger_bot_import_export_exist) { ?>
                      <span class="inline" style="text-transform: none;"><a target="_BLANK" href="<?php echo base_url('messenger_bot/tree_view/'.$value['id']);?>" class="btn btn-block btn-sm tree_view shadow"><i class="fa fa-sitemap"></i> <span class="button_text"><?php echo $this->lang->line("Tree View"); ?></span></a></span>
                    <?php } ?>
                   
                    <?php if($this->is_messenger_bot_import_export_exist) { ?>                             
                          <span class="inline" style="text-transform: none;"><a href="" class="export_bot btn btn-block btn-sm shadow" table_id="<?php echo $value['id'];?>"><i class="fa fa-file-export"></i> <span class="button_text"><?php echo $this->lang->line("Export"); ?></span></a></span>
                    <?php } ?>

                     <span class="inline" style="text-transform: none;"><a id ="<?php echo $value['user_id']."-".$value['page_id']?>" class="user_details_modal_bot btn btn-block btn-sm subscribers_list shadow"><i class="fa fa-group"></i> <span class="button_text"><?php echo $this->lang->line("Subscribers");?></span></a></span>
                
                </div>

                <?php 
                $error_log_button = '<span class="inline" style="text-transform: none;"><a target="_BLANK"  table_id="'.$value['id'].'" class="btn btn-block btn-sm error_log_report shadow"><i class="fa fa-bug"></i> <span class="button_text">'.$this->lang->line("error log report").'</span>';
                 if(array_key_exists($value['id'], $error_record)) 
                  $error_log_button.='<span class="badge" style="background: red; color: white;">'.$error_record[$value['id']].'</span>';
                 $error_log_button.='</a></span>';
                 ?>

                <div class="col-xs-12 col-sm-6 col-md-6" style="padding:2px;">

                    <?php
                    if($value['started_button_enabled']=='0')
                    { ?>
                      <span class="inline" style="text-transform: none;"><a sbutton-status="<?php echo$value['started_button_enabled']; ?>" welcome-message="<?php echo htmlspecialchars($value['welcome_message']); ?>" sbutton-enable="<?php echo $value['id'];?>" id="sbutton-<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_start_button shadow"><i class="fa fa-check-circle"></i> <span class="button_text"><?php echo $this->lang->line("Get Started Settings") ?></span></a></span>
                    <?php
                    }
                    else
                    { ?>
                      <span class="inline" style="text-transform: none;"><a sbutton-status="<?php echo$value['started_button_enabled']; ?>" welcome-message="<?php echo htmlspecialchars($value['welcome_message']); ?>" sbutton-enable="<?php echo $value['id'];?>" id="sbutton-<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_start_button shadow"><i class="fa fa-ban orange"></i> <span class="button_text"><?php echo $this->lang->line("Get Started Settings") ?></span></a></span>
                    <?php
                    }  
                    ?>
                    <?php
                    if($value['persistent_enabled']=='1') $persitent_class="fa fa-bars"; 
                    else $persitent_class="fa fa-cog"; 
                    ?>
                    <span class="inline <?php if($this->session->userdata('user_type') != 'Admin' && !in_array(197,$this->module_access)) echo " hidden"; ?>" style="text-transform: none;"><a href="<?php echo base_url("messenger_bot/persistent_menu_list/".$value['id']);?>" pbutton-enable="<?php echo $value['id'];?>" id="pbutton-<?php echo $value['id'];?>" class="btn btn-block btn-sm persistent_menu shadow"><i class="<?php echo $persitent_class;?>"></i> <span class="button_text"><?php echo $this->lang->line("Persistent Menu") ?></span></a></span> 

                    <?php if($value['enable_mark_seen']=='0') : ?>
                    <span class="inline" style="text-transform: none;"><a target="_BLANK" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm enable_mark_seen shadow"><i class="fa fa-check-circle"></i> <span class="button_text"><?php echo $this->lang->line("enable mark seen");?></span></a></span>
                    <?php else : ?>
                    <span class="inline" style="text-transform: none;"><a target="_BLANK" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm disable_mark_seen shadow"><i class="fa fa-ban"></i> <span class="button_text"><?php echo $this->lang->line("disable mark seen");?></span></a></span>
                    <?php endif; ?>

                    <span class="inline" style="text-transform: none;"><a target="_BLANK" data-status="<?php echo $value['enbale_type_on'];?>" data-delay="<?php echo $value['reply_delay_time'];?>" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm typing_on_settings shadow"><i class="fa fa-keyboard"></i> <span class="button_text"><?php echo $this->lang->line("typing on settings");?></span></a></span>

                    <?php
                    $current_email=$value['chat_human_email'];
                    ?>

                    <span class="inline" style="text-transform: none;"><a target="_BLANK" data-email="<?php echo $current_email;?>" table_id="<?php echo $value['id'];?>" class="btn btn-block btn-sm chat_human_settings shadow"><i class="fa fa-headset"></i> <span class="button_text"><?php echo $this->lang->line("Human Chat Settings");?></span></a></span>                   
   

                    <?php if($this->is_messenger_bot_import_export_exist) { ?>
                        <span class="inline" style="text-transform: none;"><a href="" class="import_bot btn btn-block btn-sm shadow" table_id="<?php echo $value['id'];?>"><i class="fa fa-file-import"></i> <span class="button_text"><?php echo $this->lang->line("Import"); ?></span></a></span>
                    <?php } ?>

                    <?php if($this->is_messenger_bot_import_export_exist) echo $error_log_button; ?>
                    
                </div>                 


                <?php if(!$this->is_messenger_bot_import_export_exist) 
                { ?>                      
                  <div class="col-xs-6 col-centered" style="padding-left:0;padding-right: 0;">
                    <?php echo $error_log_button; ?>
                  </div>
                  <?php 
                } ?>

              </div>
            </div>
        </div> -->
      </div>
      <?php   
          $i++;
          if($i%3 == 0)
          echo "</div><div class='row' style='padding:0 10px;'>";
          endforeach;
        ?>
    </div>
  </div>
<?php } ?>

<?php 

$areyousure=$this->lang->line("are you sure");
$disable_started_button = $this->lang->line("Disable Get Started");
$enable_started_button = $this->lang->line("Enable Get Started");
$somethingwentwrongpleasetryagain=$this->lang->line("something went wrong, please try again."); 

?>

<script type="text/javascript">
 function htmlspecialchars_decode(str) {
   if (typeof(str) == "string") {
    str = str.replace("&amp;",/&/g); 
    str = str.replace("&quot;",/"/g);
    str = str.replace("&#039;",/'/g);
    str = str.replace("&#92;",/\\/g);
    str = str.replace("&lt;",/</g);
    str = str.replace("&gt;",/>/g);
    }
   return str;
}


var base_url="<?php echo base_url();?>";
var areyousure = "<?php echo $areyousure;?>";
var disable_started_button = "<?php echo $disable_started_button;?>";
var enable_started_button = "<?php echo $enable_started_button;?>";
var somethingwentwrongpleasetryagain = "<?php echo $somethingwentwrongpleasetryagain;?>";
$j(document.body).on('click','.enable_mark_seen',function(){
  var table_id = $(this).attr('table_id');
  $(this).addClass('disabled');

  alertify.confirm('<?php echo $this->lang->line("confirm");?>',areyousure, 
  function(){ 
    $.ajax
    ({
       type:'POST',
       // async:false,
       url:base_url+'messenger_bot/enable_disable_mark_seen',
       data:{table_id:table_id,enable_disable:'1'},
       success:function(response)
        {
          if(response=='success')
          {
            location.reload();
          }
          else 
          {
            alertify.alert('<?php echo $this->lang->line("Alert"); ?>',somethingwentwrongpleasetryagain,function(){});
          }
        } 
    });
  },
  function(){     
    $('.enable_mark_seen[table_id="'+table_id+'"]').removeClass('disabled');    
  });

  
});
$j(document.body).on('click','.disable_mark_seen',function(){
  var table_id = $(this).attr('table_id');
  $(this).addClass('disabled');

  alertify.confirm('<?php echo $this->lang->line("confirm");?>',areyousure, 
  function(){ 
    $.ajax
    ({
       type:'POST',
       // async:false,
       url:base_url+'messenger_bot/enable_disable_mark_seen',
       data:{table_id:table_id,enable_disable:'0'},
       success:function(response)
        {
          if(response=='success')
          {
            location.reload();
          }
          else 
          {
            alertify.alert('<?php echo $this->lang->line("Alert"); ?>',somethingwentwrongpleasetryagain,function(){});
          }
        } 
    });
  },
  function(){  
    $('.disable_mark_seen[table_id="'+table_id+'"]').removeClass('disabled');     
  });

  
  
});


$j(document.body).on('click','.typing_on_settings',function(){
  var table_id = $(this).attr('table_id');
  var status = $(this).attr('data-status');
  var delay = $(this).attr('data-delay');

  $("#reply_delay_time").val(delay);
  $("#enbale_type_on2").val(status);
  $("#typing_on_settings_submit").attr("table_id",table_id);

  if(status=='1') $("#delay_con").show();
  else $("#delay_con").hide();

  $("#typing_on_settings_modal").modal();

  $(this).addClass('disabled'); 

});

$j(document.body).on('click','#typing_on_settings_submit',function(){
  var table_id = $(this).attr('table_id');
  var reply_delay_time = $("#reply_delay_time").val();
  var enbale_type_on = $("#enbale_type_on2").val();

   $.ajax
    ({
       type:'POST',
       url:base_url+'messenger_bot/typing_on_settings',
       data:{table_id:table_id,reply_delay_time:reply_delay_time,enbale_type_on:enbale_type_on},
       success:function(response)
        {
          $("#typing_on_settings_modal").modal('hide');
          $('.typing_on_settings').removeClass('disabled'); 
          location.reload();
        } 
    }); 

});

$j(document.body).on('change','#enbale_type_on2',function(){
  var enbale_type_on = $(this).val();

  if(enbale_type_on=='1') $("#delay_con").show();
  else $("#delay_con").hide();

  $(this).addClass('disabled'); 

});


$j(document.body).on('click','.chat_human_settings',function(){
  var table_id = $(this).attr('table_id');
  var email = $(this).attr('data-email');
  $("#chat_human_email").val(email);
  $("#chat_human_settings_submit").attr("table_id",table_id);
  $("#chat_human_settings_modal").modal(); 
  $("#chat_human_settings_links").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif">');  
   $.ajax
    ({
       type:'POST',
       dataType:'JSON',
       url:base_url+'messenger_bot/chat_human_settings_postback_entry',
       data:{table_id:table_id},
       success:function(response)
        {
          if(response.status=='1')
          {
            $("#chat_human_settings_links").html(response.message);          
          } 
          else 
          {
            $("#chat_human_settings_links").html(''); 
            alertify.alert('<?php echo $this->lang->line("Alert") ?>', response.message, function(){});
          }
        } 
    }); 


  $(this).addClass('disabled'); 

});

$j(document.body).on('click','#chat_human_settings_submit',function(){
  var table_id = $(this).attr('table_id');
  var chat_human_email = $("#chat_human_email").val();
  if(chat_human_email=="")
  {
    alertify.alert('<?php echo $this->lang->line("Alert") ?>', "<?php echo $this->lang->line("You must provide an email to get notification when someone wants to chat with human.");?>", function(){});
    return false;
  }

   $.ajax
    ({
       type:'POST',
       url:base_url+'messenger_bot/chat_human_settings',
       data:{table_id:table_id,chat_human_email:chat_human_email},
       success:function(response)
        {
          $("#chat_human_settings_modal").modal('hide');
          $('.chat_human_settings').removeClass('disabled'); 
          location.reload();
        } 
    }); 

});


$j(document.body).on('click','.enable_start_button',function(){

  var page_id = $(this).attr('sbutton-enable');
  var started_button_enabled = $(this).attr('sbutton-status');
  var welcome_message = htmlspecialchars_decode($(this).attr('welcome-message'));

  $("#welcome_message").val(welcome_message); 
  $("#started_button_enabled").val(started_button_enabled); 

  if(started_button_enabled=='0') $("#delay_con2").hide();
  else $("#delay_con2").show();

  $("#enable_start_button_submit").attr("table_id",page_id);
  $("#enable_start_button_modal").modal();
  
  /**Load Emoji For Welcome Screen Message on Get Started Button ***/
	 $j("#welcome_message").emojioneArea({
    		autocomplete: false,
			pickerPosition: "bottom"
	   });
	   
  
});


$j(document.body).on('click','#enable_start_button_submit',function(){
  var table_id = $(this).attr('table_id');
  var welcome_message = $("#welcome_message").val();
  var started_button_enabled = $("#started_button_enabled").val();
  $("#enable_start_button_submit").addClass('disabled');
   $.ajax
    ({
       type:'POST',
       url:base_url+'messenger_bot/get_started_welcome_message',
       data:{table_id:table_id,welcome_message:welcome_message,started_button_enabled:started_button_enabled},
       dataType:'JSON',
       success:function(response)
        {
          $("#enable_start_button_submit").removeClass('disabled');
          if(response.status=='1')  alertify.success(response.message);
          else alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.message,function(){});
        } 
    }); 

});

$j(document.body).on('change','#started_button_enabled',function(){
  var started_button_enabled = $(this).val();

  if(started_button_enabled=='1') $("#delay_con2").show();
  else $("#delay_con2").hide();
});



$j(document.body).on('click','.enable_bot',function(){
  var page_id = $(this).attr('bot-enable');
  var restart = $(this).attr('restart');
  $(this).addClass('disabled');
  alertify.confirm('<?php echo $this->lang->line("confirm");?>',areyousure, 
  function(){ 
    $.ajax
    ({
       type:'POST',
       // async:false,
       url:base_url+'messenger_bot/enable_disable_bot',
       data:{page_id:page_id,enable_disable:'enable',restart:restart},
       dataType:'JSON',
       success:function(response)
        {
          if(response.success)
          {
            location.reload();
          }
          else 
          {
            alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
          }
        }
           
    });
  },
  function(){  
    $('.enable_bot[bot-enable="'+page_id+'"]').removeClass('disabled');     
  });
  
});

$j(document.body).on('click','.disable_bot',function(){
    var page_id = $(this).attr('bot-enable');
    var restart = $(this).attr('restart');
    $(this).addClass('disabled');
    alertify.confirm('<?php echo $this->lang->line("confirm");?>',areyousure, 
    function(){ 

      $.ajax
      ({
         type:'POST',
         url:base_url+'messenger_bot/check_page_response',
         data:{page_id:page_id},
         dataType:'JSON',
         success:function(response)
          {
            if(response.has_pageresponse == '1')
            {              
              alertify.confirm('Alert !', '<?php echo $this->lang->line("There is PageResponse set for this page by you or other admin of this page. By disabling Messenger Bot will also disable the PageResponse. You can restart PageResponse by going to Page Response -> Account Import menu.");?>', 
                function()
                { 
                  $.ajax
                  ({
                     type:'POST',
                     url:base_url+'messenger_bot/enable_disable_bot',
                     data:{page_id:page_id,enable_disable:'disable',restart:restart},
                     dataType:'JSON',
                     success:function(response)
                      {
                        if(response.success)
                        {
                          location.reload();
                        }
                        else 
                        {
                          alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
                        }
                      }                         
                  });

                }, 
                function()
                { 
                  // location.reload();
                }
              );

            }
            else 
            {

              $.ajax
              ({
                 type:'POST',
                 url:base_url+'messenger_bot/enable_disable_bot',
                 data:{page_id:page_id,enable_disable:'disable',restart:restart},
                 dataType:'JSON',
                 success:function(response)
                  {
                    if(response.success)
                    {
                      location.reload();
                    }
                    else 
                    {
                      alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
                    }
                  }                     
              });

            }
          }
      });
 

    },
    function(){  
      $('.disable_bot[bot-enable="'+page_id+'"]').removeClass('disabled');     
    });   
  
}); 

$j(document.body).on('click','.delete_bot',function(){
    var page_id = $(this).attr('bot-enable');
    var already_disabled = $(this).attr('already_disabled');
    $(this).addClass('disabled');

    alertify.confirm('<?php echo $this->lang->line("confirm");?>',areyousure, 
    function(){ 


      $.ajax
      ({
         type:'POST',
         url:base_url+'messenger_bot/check_page_response',
         data:{page_id:page_id},
         dataType:'JSON',
         success:function(response)
          {
            if(response.has_pageresponse == '1')
            {              
              alertify.confirm('Alert !', '<?php echo $this->lang->line("There is PageResponse set for this page by you or other admin of this page. By deleting Messenger Bot will also disable the PageResponse. You can restart PageResponse by going to Page Response -> Account Import menu.");?>', 
                function()
                { 
                  $.ajax
                  ({
                     type:'POST',
                     url:base_url+'messenger_bot/delete_full_bot',
                     data:{page_id:page_id,already_disabled:already_disabled},
                     dataType:'JSON',
                     success:function(response)
                      {
                        if(response.success)
                        {
                          location.reload();
                        }
                        else 
                        {
                          alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
                        }
                      }
                         
                  });

                }, 
                function()
                { 
                  // location.reload();
                }
              );

            }
            else 
            {

              $.ajax
              ({
                 type:'POST',
                 url:base_url+'messenger_bot/delete_full_bot',
                 data:{page_id:page_id,already_disabled:already_disabled},
                 dataType:'JSON',
                 success:function(response)
                  {
                    if(response.success)
                    {
                      location.reload();
                    }
                    else 
                    {
                      alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.error,function(){});
                    }
                  }
                     
              });

            }
          }
      });
        

        
    },
    function(){  
      $('.delete_bot[bot-enable="'+page_id+'"]').removeClass('disabled');     
    });   
}); 

$j(document.body).on('click','.import_bot',function(e){
  e.preventDefault();
  var table_id = $(this).attr('table_id');
  $("#import_id").val(table_id);
  $("#import_bot_modal").modal();
});

$j(document.body).on('click','#import_bot_submit',function(){

  var template_id = $("#template_id").val();
  var filename = $("#json_upload_input").val();

  if(template_id=="" && filename=="")
  {
    alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('You must select a template or upload one.');?>",function(){ });
    return;
  }

  $("#import_bot_submit").addClass('disabled');
  $("#preloader").removeClass('hidden');

  var queryString = new FormData($("#import_bot_form")[0]);
  $.ajax({
        type:'POST' ,
        url: base_url+"messenger_bot/import_bot_check",
        dataType: 'JSON',
        data: queryString,
        cache: false,
        contentType: false,
        processData: false,
        success:function(response)
        { 
          if(response.status=='1')
          {
            var json_upload_input=response.json_upload_input;
            alertify.confirm(areyousure,response.message, 
            function(){ 
              $.ajax
              ({
                 type:'POST',
                 // async:false,
                 url:base_url+'messenger_bot/import_bot',
                 data:{json_upload_input:json_upload_input,page_id:response.page_id,template_id:response.template_id},
                 success:function(response2)
                  {
                    alertify.alert('<?php echo $this->lang->line("Import Status"); ?>',response2,function(){});
                    $("#import_bot_submit").removeClass('disabled');
                    $("#preloader").addClass('hidden');
                  } 
              });
            },
            function(){     
             $("#import_bot_submit").removeClass('disabled');
             $("#preloader").addClass('hidden');
            });
          }
          else
          {
            alertify.error(response.message); 
            $("#import_bot_submit").removeClass('disabled');
            $("#preloader").addClass('hidden');
          }
        }
  });
});

$j(document.body).on('click','.export_bot',function(e){
  e.preventDefault();
  var table_id = $(this).attr('table_id');
  $("#export_id").val(table_id);
  $("#export_bot_modal").modal();
});

$j(document.body).on('change','input[name=template_access]',function(){
  var template_access = $(this).val();
  if(template_access=='private') $("#allowed_package_ids_con").addClass('hidden');
  else $("#allowed_package_ids_con").removeClass('hidden');
});

$j(document.body).on('click','#export_bot_submit',function(){

  var template_name = $("#template_name").val();
  var template_access = $('input[name=template_access]:checked').val();
  var allowed_package_ids = $("#allowed_package_ids").val();

  if(template_name=="")
  {
    alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('Please provide template name.');?>",function(){ });
    return;
  }

  if(template_access=="public" && allowed_package_ids==null)
  {
    alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('You must choose user packages to give them template access.');?>",function(){ });
    return;
  }

  $("#export_bot_submit").addClass('disabled');
  var queryString = new FormData($("#export_bot_form")[0]);
  $.ajax({
        type:'POST' ,
        url: base_url+"messenger_bot/export_bot",
        dataType: 'JSON',
        data: queryString,
        cache: false,
        contentType: false,
        processData: false,
        success:function(response)
        { 
          alertify.alert('<?php echo $this->lang->line("Export Status");?>',response.message,function(){ });
          $("#export_bot_submit").removeClass("disabled");
        }
  });

});


$j(document.body).on('click','.user_details_modal_bot',function(){
  var user_id_page_id = $(this).attr('id');
  var base_url = '<?php echo site_url();?>';
  $("#response_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
  $("#htm").modal(); 
  setTimeout(function(){ 
    $.ajax({
      type:'POST' ,
      url:"<?php echo site_url();?>messenger_bot/user_details_modal_bot",
      data:{user_id_page_id:user_id_page_id},
      success:function(response){ 

         $('#response_div').html(response);  
      }
    });
  }, 1000);

}); 


$j(document.body).on('click','.update_user_details',function(){
  var button_id = $(this).attr('button_id');
  var base_url = '<?php echo site_url();?>';

  var row = $($(this).parent()).parent();
  var td = $(row)[0].children[1];
  


  $("#response_div_01").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
  $("#lead_update_modal").modal(); 
  
  $.ajax({
    type:'POST' ,
    url:"<?php echo site_url();?>messenger_bot/userDetailsUpdate",
    dataType: 'json',
    data:{button_id:button_id},
    success:function(response){ 

        $("#lead_update_modal").modal('hide'); 

        alertify.alert( <?php echo '"'.$this->lang->line('Update Result').'"'; ?>, response.message, function(){} );

        $j('#dg').datagrid('reload'); 

    }
  });

}); 

$j(document.body).on('click','.error_log_report',function(){
  var table_id = $(this).attr('table_id');
  var base_url = '<?php echo site_url();?>';
  $("#error_response_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
  $("#err-log").modal(); 
  $.ajax({
    type:'POST' ,
    url:"<?php echo site_url();?>messenger_bot/error_log_report",
    data:{table_id:table_id},
    success:function(response){ 
       $('#error_response_div').html(response);  
    }
  });

}); 

$(document.body).on('click','.client_thread_subscribe_unsubscribe',function(){
  $(this).html('please wait...');
  var client_subscribe_unsubscribe_status = $(this).attr('id');
  $.ajax({
    type:'POST',
    url:"<?php echo site_url();?>messenger_bot/client_subscribe_unsubscribe_status_change",
    data:{client_subscribe_unsubscribe_status:client_subscribe_unsubscribe_status},
    success:function(response){
       $("#"+client_subscribe_unsubscribe_status).parent().html(response); 
    }
  });
});

$j(document.body).on('click','.email_list',function(){
  var table_id = $(this).attr('table_id');
  var base_url = '<?php echo site_url();?>';
  $("#email_list_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
  $("#email_list_modal").modal(); 
  $.ajax({
    type:'POST' ,
    url:"<?php echo site_url();?>messenger_bot/email_list_display",
    data:{table_id:table_id},
    success:function(response){ 
       $('#email_list_div').html(response);  
    }
  });

}); 
$j(document.body).on('click','.download_email',function(){
  var table_id = $(this).attr('table_id');
  var base_url = '<?php echo site_url();?>';
  $("#download_list_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
  $("#download_list_modal").modal(); 
  $.ajax({
    type:'POST' ,
    url:"<?php echo site_url();?>messenger_bot/email_list_download",
    data:{table_id:table_id},
    success:function(response){ 
       $('#download_list_div').html(response);  
    }
  });

}); 

$j(document.body).on('click','.download_subscriber',function(){
  var page_id = $(this).attr('page_id');
  var base_url = '<?php echo site_url();?>';
  $("#download_subscriber_list_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
  $("#download_subscriber_list_modal").modal(); 
  $.ajax({
    type:'POST' ,
    url:"<?php echo site_url();?>messenger_bot/subscriber_list_download",
    data:{page_id:page_id},
    success:function(response){ 
       $('#download_subscriber_list_div').html(response);  
    }
  });

}); 


$(document.body).on('click','.lead_first_name',function(){
  
    var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
			
		var lastIndex = textAreaTxt.lastIndexOf("<br>");
		
		if(lastIndex!='-1')
		textAreaTxt = textAreaTxt.substring(0, lastIndex);
		
	    var txtToAdd = " {{user_first_name}} ";
	    var new_text = textAreaTxt + txtToAdd;
	   	$(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
	   	$(this).parent().next().next().next().children('.emojionearea-editor').click();
			
			
});

$(document.body).on('click','.lead_last_name',function(){
  
    var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();

    var lastIndex = textAreaTxt.lastIndexOf("<br>");

    if(lastIndex!='-1')
    textAreaTxt = textAreaTxt.substring(0, lastIndex);

    var txtToAdd = " {{user_last_name}} ";
    var new_text = textAreaTxt + txtToAdd;
    $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
    $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		   
		   
});
</script>

<script type="text/javascript">
  $j("document").ready(function(){
    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
    $('#typing_on_settings_modal').on('hidden.bs.modal', function () { 
      $(".typing_on_settings").removeClass('disabled');
    });
    $('#chat_human_settings_modal').on('hidden.bs.modal', function () { 
      $(".chat_human_settings").removeClass('disabled');
    });
    $('#enable_start_button_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });
    $('#export_bot_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });
    $('#import_bot_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });
    $j("#allowed_package_ids").multipleSelect({
        filter: true,
        multiple: true
    });

    // $("#template_id").select2(); 

    $(document.body).on('click','.post_to',function(){
      // var template_id=$("#template_id").val();
      // if(template_id=="")
      // {
      //   $(".type2,.type3").show();
      // }
      // else
      // {
        $(".type2,.type3").hide();
        $("#json_upload_input").val('');
      // }
    });

    $("#json_upload").uploadFile({
        url:base_url+"messenger_bot/upload_json_template",
        fileName:"myfile",
        showPreview:false,
        returnType: "json",
        dragDrop: true,
        showDelete: true,
        multiple:false,
        maxFileCount:1, 
        acceptFiles:".json",
        deleteCallback: function (data, pd) {
            var delete_url="<?php echo site_url('messenger_bot/upload_json_template_delete');?>";
              $.post(delete_url, {op: "delete",name: data},
                  function (resp,textStatus, jqXHR) { 
                    $("#json_upload_input").val(''); 
                    $(".type1,.type2").show();                      
                  });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = data;
               $("#json_upload_input").val(data_modified);
               $("#template_id").val('');
               $(".type1,.type2").hide();
           }
    });

    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
    var image_upload_limit = "<?php echo $image_upload_limit; ?>";
    $("#template_preview_image_div").uploadFile({
      url:base_url+"messenger_bot/upload_image_only",
      fileName:"myfile",
      maxFileSize:image_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
      deleteCallback: function (data, pd) {
          var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_file');?>";
          $.post(delete_url, {op: "delete",name: data},
              function (resp,textStatus, jqXHR) {
                $("#template_preview_image").val('');                    
              });
         
       },
       onSuccess:function(files,data,xhr,pd)
         {
             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
             $("#template_preview_image").val(data_modified);
         }
    });

    $(document.body).on('click','.load_preview_modal',function(e){
      e.preventDefault();
      var item_type = $(this).attr('item_type');
      var file_path = $(this).next().val();
      $("#preview_text_field").val(file_path);
      if(item_type == 'image')
      {
        $("#modal_preview_image").attr('src',file_path);
        $("#image_preview_div_modal").show();
        $("#video_preview_div_modal").hide();
        $("#audio_preview_div_modal").hide();
        
      }
      $("#modal_for_preview").modal();
    });

    $(document.body).on('click','.previous_template_choose',function(e){
      e.preventDefault();
      var template_description = $(this).attr('template_description');
      var template_name = $(this).attr('template_name');
      var preview_image = $(this).attr('preview_image');
      $("#modal_template_preview_description_div").html(template_description);
      $("#modal_template_preview_image_div").attr('src',preview_image);
      $("#template_preview_modal").modal();
    });


  });
</script>



<div class="modal fade" id="htm" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-group"></i> <?php echo $this->lang->line("lead list");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="response_div" style="padding: 20px;"></div>
                </div>               
            </div>
            <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("Close");?></button>
      </div>
        </div>
    </div>
</div>


<div class="modal fade" id="lead_update_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-group"></i> <?php echo $this->lang->line("Updating Lead Info");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="response_div_01" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="err-log" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa  fa-bug"></i> <?php echo $this->lang->line("Error Report");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="error_response_div" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="email_list_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-circle"></i> <?php echo $this->lang->line("QuickReply Email List");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="email_list_div" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="download_list_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa  fa-cloud-download"></i> <?php echo $this->lang->line("Download email list");?></h4>
            </div>
            <div class="modal-body ">
              <div class="modal-body">
                <style>
                .download_box
                {
                  border:1px solid #ccc;  
                  margin: 0 auto;
                  text-align: center;
                  margin-top:3%;
                  padding-bottom: 20px;
                  background-color: #fffddd;
                  color:#000;
                }
                </style>
                <!-- <div class="container"> -->
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
                      <div id="download_list_div">
                        
                      </div>
                      
                    </div>
                  </div>
                <!-- </div>  -->
              </div>              
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="download_subscriber_list_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa  fa-cloud-download"></i> <?php echo $this->lang->line("Download subscriber list");?></h4>
            </div>
            <div class="modal-body ">
              <div class="modal-body">
                <style>
                .download_box
                {
                  border:1px solid #ccc;  
                  margin: 0 auto;
                  text-align: center;
                  margin-top:3%;
                  padding-bottom: 20px;
                  background-color: #fffddd;
                  color:#000;
                }
                </style>
                <!-- <div class="container"> -->
                  <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
                      <div id="download_subscriber_list_div">
                        
                      </div>
                      
                    </div>
                  </div>
                <!-- </div>  -->
              </div>              
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="chat_human_settings_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-headset"></i> <?php echo $this->lang->line("Chat with Human Settings");?></h4>
            </div>
            <div class="modal-body" id="chat_human_settings_modal_body">

                <div class="col-xs-12" id="email_con">
                  <div class="form-group">
                    <label><?php echo $this->lang->line('Notification Email');?></label>
                    <input type="email"  class="form-control" name="chat_human_email" id="chat_human_email">
                  </div>
                </div>

                <div class="col-xs-12" id="chat_human_settings_links"></div>

                <div class="col-xs-12"><a target="_BLANK" id="chat_human_settings_submit" class="btn btn-primary btn-lg"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("save");?></a></div>                
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="typing_on_settings_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-keyboard"></i> <?php echo $this->lang->line("typing on settings");?></h4>
            </div>
            <div class="modal-body" id="typing_on_settings_modal_body">

                <div class="col-xs-12 col-md-6">
                  <div class="form-group">
                    <label><?php echo $this->lang->line('status');?></label>
                    <select class="form-control" name="enbale_type_on" id="enbale_type_on2">
                      <option value="1"><?php echo $this->lang->line("enabled");?></option>
                      <option value="0"><?php echo $this->lang->line("disabled");?></option>
                    </select>
                  </div>
                </div>

                <div class="col-xs-12 col-md-6" id="delay_con">
                  <div class="form-group">
                    <label><?php echo $this->lang->line('typing on delay (seconds)');?></label>
                    <input type="number" min="0" class="form-control" name="reply_delay_time" id="reply_delay_time" placeholder="<?php echo $this->lang->line('typing on delay (seconds)');?>">
                  </div>
                </div>

                <div class="col-xs-12"><a target="_BLANK" id="typing_on_settings_submit" class="btn btn-primary btn-lg"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("save");?></a></div>                
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="enable_start_button_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("Get Started Button Settings");?></h4>
            </div>
            <div class="modal-body" id="enable_start_button_modal_body">

                <div class="col-xs-12">
                  <div class="form-group">
                    <label><?php echo $this->lang->line('status');?></label>
                    <select class="form-control" name="started_button_enabled" id="started_button_enabled">
                      <option value="1"><?php echo $this->lang->line("enabled");?></option>
                      <option value="0"><?php echo $this->lang->line("disabled");?></option>
                    </select>
                  </div>
                </div>

                <div class="col-xs-12"  id="delay_con2">
                  <div class="form-group">
                    <label>
                      <?php echo $this->lang->line('Welcome Message');?>
                      <a href="#" data-placement="bottom" data-html="true"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Welcome Message") ?>" data-content="<?php echo $this->lang->line("The greeting text on the welcome screen is your first opportunity to tell a person why they should start a conversation with your Messenger bot. Some things you might include in your greeting text might include a brief description of what your bot does, such as key features, or a tagline. This is also a great place to start establishing the style and tone of your bot.Greetings have a 160 character maximum, so keep it concise.")."<br><br>".$this->lang->line("Variables")." : <br>{{user_first_name}}<br>{{user_last_name}}<br>{{user_full_name}}"; ?>">&nbsp;&nbsp;<i class='fa fa-info-circle'></i> </a>
                    </label>
					
					
                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include {{user_first_name}} variable inside your message.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_last_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                      </span>
                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>" data-content="<?php echo $this->lang->line("You can include {{user_last_name}} variable inside your message.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_first_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                      </span> 
					  
					  <div class="clearfix"></div>			
					
                    <textarea name="welcome_message" id="welcome_message" class="form-control" style="height:100px;"></textarea>
					
                  </div>
                </div>

                <div class="col-xs-12"><a target="_BLANK" id="enable_start_button_submit" class="btn btn-primary btn-lg"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("save");?></a></div>                
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="import_bot_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-file-import"></i> <?php echo $this->lang->line("Import Bot Settings");?></h4>
            </div>
            <div class="modal-body" id="import_bot_modal_body">
                <div id="preloader" class="hidden"><img src="<?php echo base_url('assets/pre-loader/Fading squares2.gif'); ?>" class='center-block'></div>
                <form id="import_bot_form" method="POST">
                  <input type="hidden" name="import_id" id="import_id">

                  <!-- New section -->
                  <style>

                  </style>

                  <div class="container-fluid">
                    <div class="row">
                      <p class="text-center" style="font-weight: bold;"><?php echo $this->lang->line('Choose from previous template'); ?></p>
                      <div class="yscroll" style="height: 400px;overflow: auto;">
                      	<br>
                          <?php foreach ($saved_template_list as $key=>$val) : 
                            $id=$val['id'];
                            $template_name=isset($val['template_name']) ? $val['template_name'] : '';
                            $description=isset($val['description']) ? $val['description'] : '';
                            $preview_image=isset($val['preview_image']) ? $val['preview_image'] : ''; 
                            $added_date = date("M j, y H:i",strtotime($val['saved_at']));
                          ?>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="padding : 0 10px;">
                            	<div class="box box-solid" style="">
				                    <div class="box-body" style="padding-top: 10px;padding-bottom: 0;">

				                        <h4 style="border:1px solid #fafafa; font-size: 15px; text-align: center; padding: 7px 10px; margin-top: 0;">
				                            <?php
	                                        if(strlen($template_name) > 22)
	                                        {
	                                          $short_template_name = substr($template_name,0,19);
	                                          echo $short_template_name."..."; 
	                                        } else 
	                                        {
	                                          echo $template_name;
	                                        }
	                                      ?> 
	                                      <div class="form-check pull-right">
	                                      		<div class="clearfix"></div>
	                                           <label class="radio_check">
	                                             <input type="radio" name="template_id" class="post_to" value="<?php echo $id; ?>" id="<?php echo $id; ?>" >
	                                             <span class="checkmark" title="<?php echo $this->lang->line('Click here to select this template'); ?>" ></span>
	                                           </label>
                                           </div> 
                                           <div class="clearfix"></div>
				                        </h4>
				                        <div class="media">
				                            <div class="media-left">
				                                			                                    
				                                    <?php if($preview_image != '') : ?>
		                                              <a title="<?php echo $this->lang->line('Click here to see template details'); ?>" target="_BLANK" href="<?php echo base_url('messenger_bot_export_import/view/'.$id);?>"><img style="width: 100px;height: 100px;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);" class="media-object"src="<?php echo base_url('upload/image/'.$val['user_id'].'/'.$preview_image); ?>" alt="preview image"><br></a>
		                                              <?php else : ?>
		                                                <a title="<?php echo $this->lang->line('click here to template details'); ?>" target="_BLANK" href="<?php echo base_url('messenger_bot_export_import/view/'.$id);?>"><img style="width: 100px;height: 100px;border-radius: 4px;box-shadow: 0 1px 3px rgba(0,0,0,.15);"  class="media-object" src="https://via.placeholder.com/100x100.png" alt="preview image"><br></a>
		                                              <?php endif; ?>
				                            </div>
				                            <div class="media-body">
				                                <div class="clearfix">
				                                    <p class="text-justify">
				                                    	<?php
				                                    	  if(strlen($description) > 173)
				                                    	  {
				                                    	    $short_des = substr($description,0,170);
				                                    	    echo $short_des."..."; 
				                                    	  } else 
				                                    	  {
				                                    	    echo $description;
				                                    	  }
				                                    	?>
				                                    </p>
				                                </div>
				                            </div>
				                        </div>
				                    </div>
				                </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                  </div>
                  <br>
                  <!-- end new section -->



                  <br/>
                  <div class="col-xs-12 text-center type2" style="font-weight: bold;"><?php echo $this->lang->line('OR'); ?></div>
                  <div class="col-xs-12 type3">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Upload Template JSON');?></label>
                      <div class="form-group">    
                        <div id="json_upload"><?php echo $this->lang->line('Upload');?></div>
                        <input type="hidden" id="json_upload_input" name="json_upload_input">
                      </div>                
                    </div>
                  </div>

                  <div class="col-xs-12"><a target="_BLANK" id="import_bot_submit" class="btn btn-primary btn-lg"><i class="fa fa-file-import"></i> <?php echo $this->lang->line("Import");?></a></div>                
                  <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="export_bot_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-left: 30px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-file-export"></i> <?php echo $this->lang->line("Export Bot Settings");?></h4>
            </div>
            <div class="modal-body" id="export_bot_modal_body">

                <form id="export_bot_form" method="POST">
                  <input type="hidden" name="export_id" id="export_id">
                  <div class="col-xs-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Name');?> *</label>
                      <input type="text" name="template_name" class="form-control" id="template_name">                    
                    </div>
                  </div>

                  <div class="col-xs-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Description');?> </label>
                      <textarea type="text" rows="4" name="template_description" class="form-control" id="template_description"></textarea>                    
                    </div>
                  </div>

                  <div class="col-xs-12">
                    <div class="form-group">
                      <label><?php echo $this->lang->line('Template Preview Image');?> [Square image like (400x400) is recommended]</label>
                      <span style="cursor:pointer;" class="label label-light blue load_preview_modal pull-right" item_type="image" file_path=""><i class="fa fa-eye"></i> <?php echo $this->lang->line('preview'); ?></span>

                      <input type="hidden" name="template_preview_image" class="form-control" id="template_preview_image">                   
                      <div id="template_preview_image_div"><?php echo $this->lang->line("upload") ?></div>
                    </div>
                  </div>

                  <?php if($this->session->userdata("user_type")=='Admin'){ ?>
                    <div class="col-xs-12">
                      <div class="form-group">
                        <label><?php echo $this->lang->line('Template Access');?> *</label>
                        <br><input type="radio" name="template_access" value="private" id="private_access" checked> <label style="color:#000 !important;" for="private_access"><?php echo $this->lang->line("Only me"); ?></label>                
                        <br><input type="radio" name="template_access" value="public"  id="public_access"> <label style="color:#000 !important;" for="public_access"><?php echo $this->lang->line("Me as well as other users"); ?></label>                   
                      </div>
                    </div>

                    <div class="col-xs-12 hidden" id="allowed_package_ids_con">
                      <div class="form-group">
                        <label><?php echo $this->lang->line('Choose User Packages');?> *</label>
                        <?php echo form_dropdown('allowed_package_ids[]', $package_list, '','class="form-control" id="allowed_package_ids" multiple'); ?>
                      </div>
                    </div>
                  <?php } ?>

                  <div class="col-xs-12"><a target="_BLANK" id="export_bot_submit" class="btn btn-primary btn-lg"><i class="fa fa-file-export"></i> <?php echo $this->lang->line("Export");?></a></div>                
                  <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_for_preview" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-eye"></i> <?php echo $this->lang->line('item preview'); ?></h4>
      </div>
      <div class="modal-body">
        <div id="image_preview_div_modal" style="display: none;">
          <img id="modal_preview_image" width="100%" src="">
        </div>
        <div id="video_preview_div_modal" style="display: none;">
          <video width="100%" id="modal_preview_video" controls>
            
          </video>
        </div>
        <div id="audio_preview_div_modal" style="display: none;">
          <audio width="100%" id="modal_preview_audio" controls>
            
          </audio>
        </div>
        <div>
          <input class="form-control" type="text" id="preview_text_field">
        </div>
      </div>
    </div>
  </div>
</div>

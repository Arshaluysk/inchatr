<?php
$redirect_url = site_url('messenger_bot/bot_settings/').$page_info['id'];
$this->load->view("include/upload_js");

$image_upload_limit = 1; 
if($this->config->item('messengerbot_image_upload_limit') != '')
$image_upload_limit = $this->config->item('messengerbot_image_upload_limit'); 

$video_upload_limit = 5; 
if($this->config->item('messengerbot_video_upload_limit') != '')
$video_upload_limit = $this->config->item('messengerbot_video_upload_limit');

$audio_upload_limit = 3; 
if($this->config->item('messengerbot_audio_upload_limit') != '')
$audio_upload_limit = $this->config->item('messengerbot_audio_upload_limit');

$file_upload_limit = 2; 
if($this->config->item('messengerbot_file_upload_limit') != '')
$file_upload_limit = $this->config->item('messengerbot_file_upload_limit');

?>

<style type="text/css">
  .item_remove
  {
  margin-top: 12px; 
  margin-left: -20px;
  font-size: 20px !important;
  cursor: pointer !important;
  font-weight: 200 !important;
  }
  .remove_reply
  {
  margin:10px 10px 0 0;
  font-size: 25px !important;
  cursor: pointer !important;
  font-weight: 200 !important;
  }
  .add_template,.ref_template{font-size: 10px;}
  .emojionearea.form-control{padding-top:12px !important;}
  .img_holder div:not(:first-child){display: none;position:fixed;bottom:87px;right:40px;}
  .img_holder div:first-child{position:fixed;bottom:87px;right:40px;}
  .lead_first_name,.lead_last_name{background: #EEE;border-radius: 0;}
  .input-group-addon{
  border-radius: 0;
  font-weight: bold;
  /* color: orange;   */
  /*border: 1px solid #607D8B !important;*/
  border: none;
  background: none;
  }
  /* .form-control-new
  {
  border: 1px solid #607D8B;
  height: 40px;
  width:100%;
  } */
  input[type=radio].css-checkbox {
  position:absolute; z-index:-1000; left:-1000px; overflow: hidden; clip: rect(0 0 0 0); height:1px; width:1px; margin:-1px; padding:0; border:0;
  }

  input[type=radio].css-checkbox + label.css-label {
  padding-left:24px;
  height:19px; 
  display:inline-block;
  line-height:19px;
  background-repeat:no-repeat;
  background-position: 0 0;
  font-size:19px;
  vertical-align:middle;
  cursor:pointer;

  }

  input[type=radio].css-checkbox:checked + label.css-label {
  background-position: 0 -19px;
  }
  label.css-label {
  background-image:url(<?php echo base_url('assets/images/csscheckbox.png'); ?>);
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  color: <?php echo $THEMECOLORCODE; ?> !important;
  font-size: 15px !important;
  }
  .css-label-container{padding:10px;border:1px dashed <?php echo $THEMECOLORCODE; ?>;border-radius: 5px;}
  .img_holder img{
  border: 1px solid #ccc;
  }

</style>

<div class="container-fluid">

  <div class="hidden" id="add_bot_settings_modal">
    <div class="modal-dialog" style="width:100%;margin:20px 0 0 0;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="padding-left: 16px;"><i class='fa fa-plus-circle'></i> <?php echo $this->lang->line("Add Bot Settings");?> : <a target='_BLANK' href='https://facebook.com/<?php echo $page_info['page_id'];?>'><?php echo $page_info['page_name'];?></a></h4>
        </div>
        <div class="modal-body" style="padding-left:30px;"> 

          <img id="loader" src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>" style="margin-top:20px;margin-bottom: 30px;" class="center-block">

          <div class="row">
            <div class="col-xs-12 col-md-9">
              <form action="#" method="post" id="messenger_bot_form" style="padding-left: 0;">
                <input type="hidden" name="page_id" id="page_id" value="<?php echo  $page_info['page_id'];?>">
                <input type="hidden" name="page_table_id" id="page_table_id" value="<?php echo  $page_info['id'];?>">
                <br>
                
                <div class="text-left">
                 <?php 
                   foreach ($keyword_types as $key => $value)
                   { 
                    if($value == 'email-quick-reply' || $value == 'phone-quick-reply' || $value == "location-quick-reply") continue;
                    ?>
                       <div class="inline css-label-container"><input type="radio" name="keyword_type" value="<?php echo $value; ?>" id="keyword_type_<?php echo $value;?>" class="css-checkbox keyword_type"/><label for="keyword_type_<?php echo $value;?>" class="css-label radGroup2"><?php echo $this->lang->line($value);?></label></div>
                       &nbsp;&nbsp;                  
                   <?php
                   } 
                ?>  
               </div>

               <br/>
                <div class="row"> 
                  <div class="col-xs-12"> 
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please give a name of this bot"); ?></label>
                      <input type="text" name="bot_name" id="bot_name" class="form-control">
                    </div>       
                  </div>  
                </div>
               
               <div class="row" id="keywords_div" style="display: none;"> 
                  <div class="col-xs-12">              
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your keywords in comma separated"); ?></label>
                      <textarea class="form-control"  name="keywords_list" id="keywords_list"></textarea>
                    </div>        
                  </div>  
                </div>
        
        
                <div class="row" id="postback_div" style="display: none;"> 
                  <div class="col-xs-12">              
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please select your postback id"); ?></label>
                      <select class="form-control" id="keywordtype_postback_id" name="keywordtype_postback_id[]">
                      <?php
                          $postback_id_array = array();
                          foreach($postback_ids as $value)
                          {                            
                            $postback_id_array[] = strtoupper($value['postback_id']);

                            if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply" || $value["is_template"] == "1") continue;

                            if($value['use_status'] == '0'){
                              $array_key = $value['postback_id'];
                              $array_value = $value['postback_id']." (".$value['bot_name'].")";
                              echo "<option value='{$array_key}'>{$array_value}</option>";
                            }                         
                          }
                      ?>                      
                      </select>
                    </div>        
                  </div>  
                </div>   
        
        
      <?php for($k=1;$k<=3;$k++){ ?>
          <div id="multiple_template_div_<?php echo $k; ?>" <?php if($k != 1) echo "style='display : none; margin-top:20px;background:#fff; border:.8px dashed ".$THEMECOLORCODE.";'"; else echo "style='margin-top:20px;background:#fff; border:.8px dashed ".$THEMECOLORCODE.";'"; ?>>            
          
          
              <?php if($k != 1) : ?>
                <i class="fa fa-times-circle remove_reply pull-right red" row_id="multiple_template_div_<?php echo $k; ?>" counter_variable="" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
              <?php endif; ?>
                       

              <div style="padding: 0 15px 15px 15px !important;">
                <label for="template_type_<?php echo $k; ?>"><?php echo $this->lang->line("");?></label>          
                <div class="form-group">
                  <span class="input-group-addon"><?php echo $this->lang->line("Template Type");?></span>
                   <select class="form-control form-control-new" id="template_type_<?php echo $k; ?>" name="template_type_<?php echo $k; ?>">
                    <?php 
                     foreach ($templates as $key => $value)
                     {
                        echo '<option value="'.$value.'">'.$this->lang->line($value).'</option>';
                     } 
                    ?>
                  </select>
                </div>

                <div class="row" id="text_div_<?php echo $k; ?>"> 
                  <div class="col-xs-12">              
                    <div class="form-group clearfix">
                      <label><?php echo $this->lang->line("Please provide your reply message"); ?>
                        <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                      </label>

                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_last_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                      </span>
                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_first_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                      </span> 
					            <div class="clearfix"></div>
                      <textarea class="form-control"  name="text_reply_<?php echo $k; ?>" id="text_reply_<?php echo $k; ?>"></textarea>
                    </div>        
                  </div>  
                </div>

                <div class="row" id="image_div_<?php echo $k; ?>" style="display: none;">             
                  <div class="col-xs-12">              
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your reply image"); ?></label>
                      <input type="hidden" class="form-control"  name="image_reply_field_<?php echo $k; ?>" id="image_reply_field_<?php echo $k; ?>">
                      <div id="image_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div>
                      <img id="image_reply_div_<?php echo $k; ?>" style="display: none;" height="200px;" width="400px;">
                    </div>       
                  </div>             
                </div>

                <div class="row" id="audio_div_<?php echo $k; ?>" style="display: none;">  
                  <div class="col-xs-12">             
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your reply audio"); ?></label>
                      <input type="hidden" class="form-control"  name="audio_reply_field_<?php echo $k; ?>" id="audio_reply_field_<?php echo $k; ?>">
                      <div id="audio_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div>                      
                      <audio controls id="audio_tag_<?php echo $k; ?>" style="display: none;">
                        <source src="" id="audio_reply_div_<?php echo $k; ?>" type="audio/mpeg">
                      Your browser does not support the video tag.
                      </audio>
                    </div>           
                  </div>
                </div>

                <div class="row" id="video_div_<?php echo $k; ?>" style="display: none;">  
                  <div class="col-xs-12">             
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your reply video"); ?></label>
                      <input type="hidden" class="form-control"  name="video_reply_field_<?php echo $k; ?>" id="video_reply_field_<?php echo $k; ?>">
                      <div id="video_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div>                      
                      <video width="400" height="200" controls id="video_tag_<?php echo $k; ?>" style="display: none;">
                        <source src="" id="video_reply_div_<?php echo $k; ?>" type="video/mp4">
                      Your browser does not support the video tag.
                      </video>
                    </div>           
                  </div>
                </div>

                <div class="row" id="file_div_<?php echo $k; ?>" style="display: none;">  
                  <div class="col-xs-12">             
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your reply file"); ?></label>
                      <input type="hidden" class="form-control"  name="file_reply_field_<?php echo $k; ?>" id="file_reply_field_<?php echo $k; ?>">
                      <div id="file_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div> 
                    </div>           
                  </div>
                </div>


                <div class="row" id="media_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                  <div class="col-xs-12"> 

                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your media URL"); ?>
                        <a href="#" class="media_template_modal" title="<?php echo $this->lang->line("How to get meida URL?"); ?>"><i class='fa fa-info-circle'></i> </a>
                      </label>
          
                      <div class="clearfix"></div>
                      <input class="form-control"  name="media_input_<?php echo $k; ?>" id="media_input_<?php echo $k; ?>" />
                    </div> 

                    <?php for ($i=1; $i <=3 ; $i++) : ?>
                    <div class="row" id="media_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;border:1px dashed #ccc; background: #fcfcfc;padding:10px;margin:5px 0 0 20px;">
                      <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button text"); ?></label>
                          <input type="text" class="form-control"  name="media_text_<?php echo $i; ?>_<?php echo $k; ?>" id="media_text_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button type"); ?></label>
                          <select class="form-control media_type_class" id="media_type_<?php echo $i; ?>_<?php echo $k; ?>" name="media_type_<?php echo $i; ?>_<?php echo $k; ?>">
                            <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                            <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                            <option value="web_url"><?php echo $this->lang->line("Web URL"); ?></option>
                            <option value="phone_number"><?php echo $this->lang->line("call us"); ?></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-xs-12 col-md-3">

                        <div class="form-group" id="media_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("PostBack id"); ?></label>
                          <select class="form-control push_postback"  name="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                          <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                          <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                        </div>

                        <div class="form-group" id="media_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("web url"); ?></label>
                          <input type="text" class="form-control"  name="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                        <div class="form-group" id="media_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("phone number"); ?></label>
                          <input type="text" class="form-control"  name="media_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="media_call_us_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>

                      <?php if($i != 1) : ?>
                        <div class="col-xs-12 col-md-1" >
                          <br/>
                          <i class="fa fa-2x fa-times-circle red item_remove" row_id="media_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="media_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="media_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="media_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="media_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="media_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="media_counter_<?php echo $k; ?>" add_more_button_id="media_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                        </div>
                      <?php endif; ?>

                    </div>
                    <?php endfor; ?>

                    <div class="row clearfix">
                      <div class="col-xs-12 text-center"><button class="btn btn-outline-primary pull-right no_radius btn-xs" id="media_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                    </div>

                  </div> 
                </div>



                <div class="row" id="quick_reply_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                  <div class="col-xs-12">  

                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your reply message"); ?>
                        <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                      </label>

                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_last_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                      </span>
                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_first_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                      </span> 
						          <div class="clearfix"></div>
                      <textarea class="form-control" name="quick_reply_text_<?php echo $k; ?>" id="quick_reply_text_<?php echo $k; ?>"></textarea>
                    </div> 

                    <?php for ($i=1; $i <=11 ; $i++) : ?>
                    <div class="row" id="quick_reply_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;border:1px dashed #ccc; background: #fcfcfc;padding:10px;margin:5px 0 0 20px;">
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button text"); ?></label>
                          <input type="text" class="form-control" name="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>" id="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>
                      <!-- 28/02/2018 -->
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button type"); ?></label>
                          <select class="form-control quick_reply_button_type_class" id="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                            <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                            <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                            <option value="phone_number"><?php echo $this->lang->line("user phone number"); ?></option>
                            <option value="user_email"><?php echo $this->lang->line("user email address"); ?></option>
                            <option value="location"><?php echo $this->lang->line("user's location"); ?></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-4 col-md-3">
                         <div class="form-group" id="quick_reply_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                            <label><?php echo $this->lang->line("PostBack id"); ?></label>
                            <select class="form-control push_postback"  name="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                            <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                            <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                          </div>
                      </div>

                      <?php if($i != 1) : ?>
                        <div class="hidden-xs col-sm-2 col-md-1">
                          <br/>
                          <i class="fa fa-2x fa-times-circle red item_remove" row_id="quick_reply_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="" third_callus="" counter_variable="quick_reply_button_counter_<?php echo $k; ?>" add_more_button_id="quick_reply_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                        </div>
                      <?php endif; ?>


                    </div>
                    <?php endfor; ?>

                    <div class="row clearfix">
                      <div class="col-xs-12 text-center"><button class="btn btn-outline-primary pull-right no_radius btn-xs" id="quick_reply_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                    </div>

                  </div> 
                </div>

                <div class="row" id="text_with_buttons_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                  <div class="col-xs-12"> 

                    <div class="form-group">
                      <label><?php echo $this->lang->line("Please provide your reply message"); ?>
                        <a href="#" data-placement="bottom"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Spintax"); ?>" data-content="Spintax example : {Hello|Howdy|Hola} to you, {Mr.|Mrs.|Ms.} {{Jason|Malina|Sara}|Williams|Davis}"><i class='fa fa-info-circle'></i> </a>
                      </label>

                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_last_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                      </span>
                      <span class='pull-right'> 
                        <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                        <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_first_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                      </span> 
					            <div class="clearfix"></div>
                      <textarea class="form-control"  name="text_with_buttons_input_<?php echo $k; ?>" id="text_with_buttons_input_<?php echo $k; ?>"></textarea>
                    </div> 

                    <?php for ($i=1; $i <=3 ; $i++) : ?>
                    <div class="row" id="text_with_buttons_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;border:1px dashed #ccc; background: #fcfcfc;padding:10px;margin:5px 0 0 20px;">
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button text"); ?></label>
                          <input type="text" class="form-control"  name="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button type"); ?></label>
                          <select class="form-control text_with_button_type_class" id="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                            <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                            <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                            <option value="web_url"><?php echo $this->lang->line("Web URL"); ?></option>
                            <option value="phone_number"><?php echo $this->lang->line("call us"); ?></option>
                            <?php if($has_broadcaster_addon == 1) : ?>
                            <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("unsubscribe"); ?></option>
                            <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("re-subscribe"); ?></option>
                            <?php endif; ?>
                            <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                            <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-4 col-md-3">
                        <div class="form-group" id="text_with_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("PostBack id"); ?></label>
                          <select class="form-control push_postback"  name="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                          <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                          <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                        </div>
                        <div class="form-group" id="text_with_button_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("web url"); ?></label>
                          <input type="text" class="form-control"  name="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                        <div class="form-group" id="text_with_button_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("phone number"); ?></label>
                          <input type="text" class="form-control"  name="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>

                      <?php if($i != 1) : ?>
                        <div class="hidden-xs col-sm-2 col-md-1" >
                          <br/>
                          <i class="fa fa-2x fa-times-circle red item_remove" row_id="text_with_buttons_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="text_with_button_counter_<?php echo $k; ?>" add_more_button_id="text_with_button_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                        </div>
                      <?php endif; ?>


                    </div>
                    <?php endfor; ?>

                    <div class="row clearfix">
                      <div class="col-xs-12 text-center"><button class="btn btn-outline-primary pull-right no_radius btn-xs" id="text_with_button_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                    </div>

                  </div> 
                </div>

                <div class="row" id="generic_template_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">   
                  <div class="col-xs-12"> 

                    <div class="row">
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("Please provide your reply image"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                          <input type="hidden" class="form-control"  name="generic_template_image_<?php echo $k; ?>" id="generic_template_image_<?php echo $k; ?>" />
                          <div id="generic_image_<?php echo $k; ?>"><?php echo $this->lang->line('upload'); ?></div>
                        </div>                         
                      </div>
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("image click destination link"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                          <input type="text" class="form-control"  name="generic_template_image_destination_link_<?php echo $k; ?>" id="generic_template_image_destination_link_<?php echo $k; ?>" />
                        </div> 
                      </div>                      
                    </div>

                    <div class="row">
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("title"); ?></label>
                          <input type="text" class="form-control"  name="generic_template_title_<?php echo $k; ?>" id="generic_template_title_<?php echo $k; ?>" />
                        </div>
                      </div>  
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("sub-title"); ?></label>
                          <input type="text" class="form-control"  name="generic_template_subtitle_<?php echo $k; ?>" id="generic_template_subtitle_<?php echo $k; ?>" />
                        </div>
                      </div>  
                    </div>

                    <span class="pull-right"><span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></span><div class="clearfix"></div>
                    <?php for ($i=1; $i <=3 ; $i++) : ?>
                    <div class="row" id="generic_template_row_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;border:1px dashed #ccc; background: #fcfcfc;padding:10px;margin:5px 0 0 20px;">
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button text"); ?></label>
                          <input type="text" class="form-control"  name="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button type"); ?></label>
                          <select class="form-control generic_template_button_type_class" id="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                            <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                            <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                            <option value="web_url"><?php echo $this->lang->line("Web URL"); ?></option>
                            <option value="phone_number"><?php echo $this->lang->line("call us"); ?></option>
                            <?php if($has_broadcaster_addon == 1) : ?>
                            <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("unsubscribe"); ?></option>
                            <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("re-subscribe"); ?></option>
                            <?php endif; ?>
                            <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                            <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-4 col-md-3">
                        <div class="form-group" id="generic_template_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("PostBack id"); ?></label>
                          <select class="form-control push_postback"  name="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                          <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                          <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                        </div>
                        <div class="form-group" id="generic_template_button_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("web url"); ?></label>
                          <input type="text" class="form-control"  name="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                        <div class="form-group" id="generic_template_button_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("phone number"); ?></label>
                          <input type="text" class="form-control"  name="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>

                      <?php if($i != 1) : ?>
                        <div class="hidden-xs col-sm-2 col-md-1">
                          <br/>
                          <i class="fa fa-2x fa-times-circle red item_remove" row_id="generic_template_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="generic_with_button_counter_<?php echo $k; ?>" add_more_button_id="generic_template_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                        </div>
                      <?php endif; ?>

                    </div>
                    <?php endfor; ?>

                    <div class="row clearfix">
                      <div class="col-xs-12 text-center"><button class="btn btn-outline-primary pull-right no_radius btn-xs" id="generic_template_add_button_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                    </div>

                  </div>
                </div>

                <div class="row" id="carousel_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                  <?php for ($j=1; $j <=10 ; $j++) : ?>
                  <div class="col-xs-12" id="carousel_div_<?php echo $j; ?>_<?php echo $k; ?>" style="display: none; padding-top: 20px;"> 
                    <div style="border: 1px dashed #ccc; background:#fcfcfc;padding:10px 15px;">
                    <div class="row">
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("Please provide your reply image"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                          <input type="hidden" class="form-control"  name="carousel_image_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_image_<?php echo $j; ?>_<?php echo $k; ?>" />
                          <div id="generic_imageupload_<?php echo $j; ?>_<?php echo $k; ?>"><?php echo $this->lang->line('upload'); ?></div>
                        </div>                         
                      </div>
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("image click destination link"); ?> <span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></label>
                          <input type="text" class="form-control"  name="carousel_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" />
                        </div> 
                      </div>                      
                    </div>

                    <div class="row">
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("title"); ?></label>
                          <input type="text" class="form-control"  name="carousel_title_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_title_<?php echo $j; ?>_<?php echo $k; ?>" />
                        </div>
                      </div>  
                      <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("sub-title"); ?></label>
                          <input type="text" class="form-control"  name="carousel_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" />
                        </div>
                      </div>  
                    </div>

                    <span class="pull-right"><span style='color:orange !important;'>(<?php echo $this->lang->line("optional"); ?>)</span></span><div class="clearfix"></div>
                    <?php for ($i=1; $i <=3 ; $i++) : ?>
                    <div class="row" id="carousel_row_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;">
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button text"); ?></label>
                          <input type="text" class="form-control"  name="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-3 col-md-4">
                        <div class="form-group">
                          <label><?php echo $this->lang->line("button type"); ?></label>
                          <select class="form-control carousel_button_type_class" id="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" name="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                            <option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                            <option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                            <option value="web_url"><?php echo $this->lang->line("Web URL"); ?></option>
                            <option value="phone_number"><?php echo $this->lang->line("call us"); ?></option>
                            <?php if($has_broadcaster_addon == 1) : ?>
                            <option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("unsubscribe"); ?></option>
                            <option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("re-subscribe"); ?></option>
                            <?php endif; ?>
                            <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
                            <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                          </select>
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-4 col-md-3">
                        <div class="form-group" id="carousel_button_postid_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("PostBack id"); ?></label>
                          <select class="form-control push_postback"  name="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                          <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                          <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                        </div>
                        <div class="form-group" id="carousel_button_web_url_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("web url"); ?></label>
                          <input type="text" class="form-control"  name="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                        </div>
                        <div class="form-group" id="carousel_button_call_us_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" style="display: none;">
                          <label><?php echo $this->lang->line("phone number"); ?></label>
                          <input type="text" class="form-control"  name="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                        </div>
                      </div>

                      <?php if($i != 1) : ?>
                        <div class="hidden-xs col-sm-2 col-md-1">
                          <br/>
                          <i class="fa fa-2x fa-times-circle red item_remove" row_id="carousel_row_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" first_column_id="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" second_column_id="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_postback="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_weburl="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_callus="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" counter_variable="carousel_add_button_counter_<?php echo $j; ?>_<?php echo $k; ?>" add_more_button_id="carousel_add_button_<?php echo $j; ?>_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                        </div>
                      <?php endif; ?>

                    </div>
                    <?php endfor; ?>

                    <div class="row clearfix" style="padding-bottom: 10px;">
                      <div class="col-xs-12 text-center"><button class="btn btn-outline-primary pull-right no_radius btn-xs" id="carousel_add_button_<?php echo $j; ?>_<?php echo $k; ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more button");?></button></div>
                    </div>
                  </div>
                  </div>
                  <?php endfor; ?>

                  <div class="col-xs-12 clearfix">
                    <button id="carousel_template_add_button_<?php echo $k; ?>" class="btn btn-sm btn-outline-primary pull-right no_radius"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more template");?></button>
                  </div>

                </div>


                <div class="row" id="list_div_<?php echo $k; ?>" style="display: none;">  
                	<div class="col-xs-12">
                		<div class="row" id="list_with_buttons_row">
                			<div class="col-xs-12 col-sm-4 col-md-4">
                				<div class="form-group">
                					<label><?php echo $this->lang->line("bottom button text"); ?></label>
                					<input type="text" class="form-control"  name="list_with_buttons_text_<?php echo $k; ?>" id="list_with_buttons_text_<?php echo $k; ?>">
                				</div>
                			</div>
                			<div class="col-xs-12 col-sm-4 col-md-4">
                				<div class="form-group">
                					<label><?php echo $this->lang->line("bottom button type"); ?></label>
                					<select class="form-control list_with_button_type_class" id="list_with_button_type_<?php echo $k; ?>" name="list_with_button_type_<?php echo $k; ?>">
                						<option value=""><?php echo $this->lang->line('please select a type'); ?></option>
                						<option value="post_back"><?php echo $this->lang->line("Post Back"); ?></option>
                						<option value="web_url"><?php echo $this->lang->line("Web URL"); ?></option>
                						<option value="phone_number"><?php echo $this->lang->line("call us"); ?></option>
                						<?php if($has_broadcaster_addon == 1) : ?>
                							<option value="post_back" id="unsubscribe_postback"><?php echo $this->lang->line("unsubscribe"); ?></option>
                							<option value="post_back" id="resubscribe_postback"><?php echo $this->lang->line("re-subscribe"); ?></option>
                						<?php endif; ?>
			                            <option value="post_back" id="human_postback"><?php echo $this->lang->line("Chat with Human"); ?></option>
			                            <option value="post_back" id="robot_postback"><?php echo $this->lang->line("Chat with Robot"); ?></option>
                					</select>
                				</div>
                			</div>
                			<div class="col-xs-12 col-sm-4 col-md-4">
                				<div class="form-group" id="list_with_button_postid_div_<?php echo $k; ?>" style="display: none;">
                					<label><?php echo $this->lang->line("PostBack id"); ?></label>
                					<select class="form-control push_postback"  name="list_with_button_post_id_<?php echo $k; ?>" id="list_with_button_post_id_<?php echo $k; ?>"><option value=""><?php echo $this->lang->line("Select");?></option></select>
                					<a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                					<a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                				</div>
                				<div class="form-group" id="list_with_button_web_url_div_<?php echo $k; ?>" style="display: none;">
                					<label><?php echo $this->lang->line("web url"); ?></label>
                					<input type="text" class="form-control"  name="list_with_button_web_url_<?php echo $k; ?>" id="list_with_button_web_url_<?php echo $k; ?>">
                				</div>
                				<div class="form-group" id="list_with_button_call_us_div_<?php echo $k; ?>" style="display: none;">
                					<label><?php echo $this->lang->line("phone number"); ?></label>
                					<input type="text" class="form-control"  name="list_with_button_call_us_<?php echo $k; ?>" id="list_with_button_call_us_<?php echo $k; ?>">
                				</div>
                			</div>
                		</div>
                	</div>

                	<?php for ($j=1; $j <=4 ; $j++) : ?>
                		<div class="col-xs-12" id="list_div_<?php echo $j; ?>_<?php echo $k; ?>" style="display: none;padding-top: 20px;"> 
                			<div style="border: 1px dashed #ccc; background:#fcfcfc;padding:10px 15px;">
                				<div class="row">
                					<div class="col-xs-12 col-md-6">
                						<div class="form-group">
                							<label><?php echo $this->lang->line("Please provide your reply image"); ?></label>
                							<input type="hidden" class="form-control"  name="list_image_<?php echo $j; ?>_<?php echo $k; ?>" id="list_image_<?php echo $j; ?>_<?php echo $k; ?>" />
                							<div id="list_imageupload_<?php echo $j; ?>_<?php echo $k; ?>"><?php echo $this->lang->line('upload'); ?></div>
                						</div>                         
                					</div>
                					<div class="col-xs-12 col-md-6">
                						<div class="form-group">
                							<label><?php echo $this->lang->line("image click destination link"); ?></label>
                							<input type="text" class="form-control"  name="list_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" id="list_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" />
                						</div> 
                					</div>                      
                				</div>

                				<div class="row">
                					<div class="col-xs-12 col-md-6">
                						<div class="form-group">
                							<label><?php echo $this->lang->line("title"); ?></label>
                							<input type="text" class="form-control"  name="list_title_<?php echo $j; ?>_<?php echo $k; ?>" id="list_title_<?php echo $j; ?>_<?php echo $k; ?>" />
                						</div>
                					</div>  
                					<div class="col-xs-12 col-md-6">
                						<div class="form-group">
                							<label><?php echo $this->lang->line("sub-title"); ?></label>
                							<input type="text" class="form-control"  name="list_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" id="list_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" />
                						</div>
                					</div>  
                				</div>
                			</div>
                		</div>
                	<?php endfor; ?>

                	<div class="col-xs-12 clearfix">
                		<button id="list_template_add_button_<?php echo $k; ?>" class="btn btn-sm btn-outline-primary pull-right no_radius"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add more template");?></button>
                	</div>

                </div>


              </div>

        </div>      
      <?php }  ?>
                <div class="row">
                  <div class="col-xs-6">
                    <br><button id="submit" class="btn btn-lg btn-primary"><i class="fa fa-send"></i> <?php echo $this->lang->line('submit'); ?></button>
                  </div>
                  <div class="col-xs-6 clearfix">
                    <button id="multiple_template_add_button"  class="btn btn-outline-primary pull-right no_radius" ><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add more reply'); ?></button>
                  </div>
                </div>

              </form>

            </div>


            <div class="hidden-xs hidden-sm col-md-3 img_holder" style="" >
              <div id="text_preview_div">
                <!-- <center><h4><b>Text</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/text.png')) echo site_url()."assets/images/preview/text.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/text.png"; ?>" class="img-rounded" alt="Text Preview"></center>
              </div>

              <div id="image_preview_div">
                <!-- <center><h4><b>Image</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/image.png')) echo site_url()."assets/images/preview/image.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/image.png"; ?>" class="img-rounded" alt="Image Preview"></center>
              </div>

              <div id="audio_preview_div">
                <!-- <center><h4><b>Audio</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/mp3.png')) echo site_url()."assets/images/preview/mp3.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/mp3.png"; ?>" class="img-rounded" alt="Audio Preview"></center>
              </div>

              <div id="video_preview_div">
                <!-- <center><h4><b>Video</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/video.png')) echo site_url()."assets/images/preview/video.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/video.png"; ?>" class="img-rounded" alt="Video Preview"></center>
              </div>

              <div id="file_preview_div">
                <!-- <center><h4><b>File</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/file.png')) echo site_url()."assets/images/preview/file.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/file.png"; ?>" class="img-rounded" alt="File Preview"></center>
              </div>

              <div id="quick_reply_preview_div">
                <!-- <center><h4><b>Quick Reply</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/quick_reply.png')) echo site_url()."assets/images/preview/quick_reply.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/quick_reply.png"; ?>" class="img-rounded" alt="Quick Reply Preview"></center>
              </div>

              <div id="text_with_buttons_preview_div">
                <!-- <center><h4><b>Text with buttons</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/button.png')) echo site_url()."assets/images/preview/button.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/button.png"; ?>" class="img-rounded" alt="Text With Buttons Preview"></center>
              </div>

              <div id="generic_template_preview_div">
                <!-- <center><h4><b>Generic Template</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/generic.png')) echo site_url()."assets/images/preview/generic.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/generic.png"; ?>" class="img-rounded" alt="Generic Template Preview"></center>
              </div>

              <div id="carousel_preview_div">
                <!-- <center><h4><b>Carousel Template</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/carousel.png')) echo site_url()."assets/images/preview/carousel.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/carousel.png"; ?>" class="img-rounded" alt="Carousel Template Preview"></center>
              </div>

              <div id="list_preview_div">
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/list.png')) echo site_url()."assets/images/preview/list.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/list.png"; ?>" class="img-rounded" alt="List Template Preview"></center>
              </div>

              <div id="media_preview_div">
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/media.png')) echo site_url()."assets/images/preview/media.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/media.png"; ?>" class="img-rounded" alt="Media Template Preview"></center>
              </div>

            </div>

            
          </div>

          <br>
          <div id="submit_status" class="text-center"></div>

          

        </div>


      </div>
    </div>
  </div>

  <br>
  <?php if($this->session->flashdata('bot_success')===1) { ?>
  <div class="alert alert-success text-center" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("new bot settings has been stored successfully.");?></div>
  <?php } ?>


  <?php if($this->session->flashdata('bot_update_success')===1) { ?>
  <div class="alert alert-success text-center" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("bot has been updated successfully.");?></div>
  <?php } ?>

  <div class="box box-widget widget-user-2" >
    <div class="widget-user-header" style="border-radius: 0;">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-9">
          <div class="widget-user-image">
            <img class="img-circle" src="<?php echo $page_info['page_profile'];?>">
          </div>
          <h3 class="widget-user-username"><a href="https://facebook.com/<?php echo $page_info['page_id'];?>" target="_BLANK"><?php echo $page_info['page_name'];?></a></h3>
          <h5 class="widget-user-desc"><?php echo  $page_info['account_name'];?></h5>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
          <a class="btn btn-outline-primary pull-right" id="add_bot_settings" style="margin-top:15px;"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Bot Settings');?></a>
        </div>
      </div>
    </div>
    <div class="box-footer" style="border-radius: 0;padding:20px;">
      <?php 
      if(empty($bot_settings)) echo "<h4 class='text-center'>".$this->lang->line('no bot settings found.')."</h4>";
      else
      {
          echo "<div class='table-responsive'><table class='table table-bordered table-condensed' id='bot_settings_data_table'>";
            echo "<thead>";
              echo "<tr>";
                echo "<th class='text-center'>".$this->lang->line("SN")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Bot Name")."</th>";
                // echo "<th class='text-center'>".$this->lang->line("Bot Type")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Keywords")."</th>";
                // echo "<th class='text-center'>".$this->lang->line("Template Type")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Keyword Type")."</th>";
                // echo "<th class='text-center'>".$this->lang->line("Last Replied")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
                // echo "<th class='text-center hidden-xs hidden-sm'>".$this->lang->line("Message")."</th>";
              echo "</tr>";
            echo "</thead>";

            echo "<tbody>";
              $i=0;
              foreach ($bot_settings as $key => $value) 
              {
                
                if($value['keyword_type'] == "email-quick-reply" || $value['keyword_type'] == "location-quick-reply" || $value['keyword_type'] == "phone-quick-reply" || $value['postback_id'] == "UNSUBSCRIBE_QUICK_BOXER" || $value['postback_id'] == "RESUBSCRIBE_QUICK_BOXER" || $value['postback_id'] == "YES_START_CHAT_WITH_HUMAN" || $value['postback_id'] == "YES_START_CHAT_WITH_BOT") continue;
                $i++;
                // if(strlen($value['message'])>30) $message_display=substr($value['message'],0,30).'...';
                // else $message_display=$value['message'];

                if($value['last_replied_at']!="0000-00-00 00:00:00") $reply_at=date('d M y - H:i:s');
                else $reply_at =  "<span class='label label-warning'><i class='fa fa-remove'></i> ".$this->lang->line('No Replied Yet')."</span>";

                if($value['keyword_type']=="reply") 
                {
                  if(strlen($value['keywords'])>20) $keywords_display=substr($value['keywords'],0,20).'...';
                  else $keywords_display=$value['keywords'];
                  $keywords_tooltip='<a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Keywords").'" data-content="'.str_replace(",",", ", $value["keywords"]).'"><i class="fa fa-info-circle"></i> </a>';
                  $keywords_display=str_replace(',', ', ', $keywords_display)." ".$keywords_tooltip;
                }
                else $keywords_display =  "<span class='label label-light orange'><i class='fa fa-remove'></i></span>";

                echo "<tr>";
                  echo "<td class='text-center'>".$i."</td>";
                  echo "<td class='text-center'>".$value['bot_name']."</td>";
                  echo "<td class='text-center'>".$keywords_display."</td>";
                  echo "<td class='text-center'>".$this->lang->line($value['keyword_type'])."</td>";
                   echo "<td class='text-center'>";
                    echo "<a title='".$this->lang->line("View this bot")."' class='btn btn-sm btn-outline-info' href='".base_url("messenger_bot/view_bot/".$value['id'])."'><i class='fa fa-eye'></i></a> &nbsp;&nbsp;&nbsp;";
                    echo "<a title='".$this->lang->line("Edit this bot")."' class='btn btn-sm btn-outline-warning' href='".base_url("messenger_bot/edit_bot/".$value['id'])."'><i class='fa fa-edit'></i></a> &nbsp;&nbsp;&nbsp;";
                    echo "<a title='".$this->lang->line("delete this bot")."' class='btn btn-sm btn-outline-danger delete_bot' id='".$value['id']."'><i class='fa fa-trash'></i></a>";
                   echo "</td>";
                echo "</tr>";
              }
            echo "</tbody>";
          echo "</table></div>";

      }
      ?>
    </div>
    <?php
      $somethingwentwrong = $this->lang->line("something went wrong.");  
      $doyoureallywanttodeletethisbot = $this->lang->line("do you really want to delete this bot?");
    ?>
  </div>

</div>

<?php 
$areyousure=$this->lang->line("are you sure"); 
?>

<script type="text/javascript">
  $(document).ready(function(e){

    $("#keywordtype_postback_id").select2();

    $( document ).on( 'click', '.bs-dropdown-to-select-group .dropdown-menu li', function( event ) {
      var $target = $( event.currentTarget );
      $target.closest('.bs-dropdown-to-select-group')
      .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
      .end()
      .children('.dropdown-toggle').dropdown('toggle');
      $target.closest('.bs-dropdown-to-select-group')
      .find('[data-bind="bs-drp-sel-label"]').text($target.context.textContent);
      return false;
    });
  });
</script>


<script type="text/javascript">
  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";
  

  var js_array = [<?php echo '"'.implode('","', $postback_id_array ).'"' ?>];

  var areyousure="<?php echo $areyousure;?>";
  
  var text_with_button_counter = 1;
  var generic_template_button_counter = 1;
  var carousel_template_counter = 1;
  $j(document).ready(function() {


    $(document.body).on('click','.media_template_modal',function(){
       $("#media_template_modal").modal();
    });
  
  
  	/**Load Emoji For first Text Reply Field By Default***/
	 $j("#text_reply_1").emojioneArea({
        		autocomplete: false,
				pickerPosition: "bottom"
	   });

    // getting postback list and making iframe
    var page_id="<?php echo $page_info['id'];?>";
    var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?>"+page_id;
    $('#add_template_modal').on('shown.bs.modal',function(){ 
      $(this).find('iframe').attr('src',iframe_link); 
    });   
    refresh_template();
    $("#loader").addClass('hidden');
    // getting postback list and making iframe

    

    var keyword_type = $("input[name=keyword_type]:checked").val();
    if(keyword_type == 'reply')
    {
      $("#keywords_div").show();
    }else{
      $("#keywords_div").hide();
    }

    $(document.body).on('change','input[name=keyword_type]',function(){
      if($("input[name=keyword_type]:checked").val()=="reply")
      {
        $("#keywords_div").show();
      }
      else 
      {
        $("#keywords_div").hide();
      }
    });



    var multiple_template_add_button_counter = 1;
    $(document.body).on('click','#multiple_template_add_button',function(e){
      e.preventDefault();
      multiple_template_add_button_counter++
	  
	   $j("#text_reply_"+multiple_template_add_button_counter).emojioneArea({
        		autocomplete: false,
				pickerPosition: "bottom"
	     });
	  
      $("#multiple_template_div_"+multiple_template_add_button_counter).show();
      if(multiple_template_add_button_counter == 3){
        var previous_div_id_counter = multiple_template_add_button_counter-1;
        $("#multiple_template_div_"+previous_div_id_counter).find(".remove_reply").hide();
        $("#multiple_template_add_button").hide();
      }
    });


    $(document.body).on('click','.remove_reply',function(){
      var remove_reply_counter_variable = "multiple_template_add_button_counter";
      var remove_reply_row_id = $(this).attr('row_id');
      $("#"+remove_reply_row_id).find('textarea,input,select').val('');

      $("#"+remove_reply_row_id).hide();
      eval(remove_reply_counter_variable+"--");
      var temp = eval(remove_reply_counter_variable);
      if(temp != 1)
      {
        $("#multiple_template_div_"+temp).find(".remove_reply").show();
      }
      if(temp < 3) $("#multiple_template_add_button").show();
    });


    var keyword_type = $("input[name=keyword_type]:checked").val();
    if(keyword_type == 'post-back')
    {
      $("#postback_div").show();
    }

    $(document.body).on('change','input[name=keyword_type]',function(){    
      if($("input[name=keyword_type]:checked").val()=="post-back")
      {
        $("#postback_div").show();
      }
      else 
      {
        $("#postback_div").hide();
      }
    });

    var image_upload_limit = "<?php echo $image_upload_limit; ?>";
    var video_upload_limit = "<?php echo $video_upload_limit; ?>";
    var audio_upload_limit = "<?php echo $audio_upload_limit; ?>";
    var file_upload_limit = "<?php echo $file_upload_limit; ?>";
  
<?php for($template_type=1;$template_type<=3;$template_type++){ ?>
  
  	var template_type_order="#template_type_<?php echo $template_type ?>";
  
    $j(document.body).on('change',"#template_type_<?php echo $template_type ?>",function(){
  
      var selected_template = $("#template_type_<?php echo $template_type ?>").val();
      selected_template = selected_template.replace(/ /gi, "_");

      var template_type_array = ['text','image','audio','video','file','quick_reply','text_with_buttons','generic_template','carousel','list','media'];
      template_type_array.forEach(templates_hide_show_function);
      function templates_hide_show_function(item, index)
      {
        var template_type_preview_div_name = "#"+item+"_preview_div";

        // alert(template_type_preview_div_name);

        var template_type_div_name = "#"+item+"_div_<?php echo $template_type; ?>";
        if(selected_template == item){
          $(template_type_div_name).show();
          $(template_type_preview_div_name).show();
        }
        else{
          $(template_type_div_name).hide();
          $(template_type_preview_div_name).hide();
        }
		
    		if(selected_template=='text'){
    			
    			 $j("#text_reply_<?php echo $template_type; ?>").emojioneArea({
            		autocomplete: false,
    				pickerPosition: "bottom"
    	     	 });
    		}


        if(selected_template == 'media')
        {
          $("#media_row_1_<?php echo $template_type; ?>").show();     
        }
		

        if(selected_template == 'quick_reply')
        {
          $("#quick_reply_row_1_<?php echo $template_type; ?>").show();
		  
		  $j("#quick_reply_text_<?php echo $template_type; ?>").emojioneArea({
        	autocomplete: false,
			pickerPosition: "bottom"
     	 });
		 
		  
        }

        if(selected_template == 'text_with_buttons')
        {
          $("#text_with_buttons_row_1_<?php echo $template_type; ?>").show();
		  
		   $j("#text_with_buttons_input_<?php echo $template_type; ?>").emojioneArea({
	        	autocomplete: false,
				pickerPosition: "bottom"
     	 });
		 
		 
        }

        if(selected_template == 'generic_template')
        {
          $("#generic_template_row_1_<?php echo $template_type; ?>").show();
        }

        if(selected_template == 'carousel')
        {
          $("#carousel_div_1_<?php echo $template_type; ?>").show();
          $("#carousel_row_1_1_<?php echo $template_type; ?>").show();
        }

        if(selected_template == 'list')
        {
          $("#list_div_1_<?php echo $template_type; ?>").show();
          $("#list_div_2_<?php echo $template_type; ?>").show();
        }

      }
    });
  
  
    $("#image_reply_<?php echo $template_type; ?>").uploadFile({
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
                $("#image_reply_field_<?php echo $template_type; ?>").val('');  
                $("#image_reply_div_<?php echo $template_type; ?>").hide();                     
              });
         
       },
       onSuccess:function(files,data,xhr,pd)
         {
             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
             $("#image_reply_field_<?php echo $template_type; ?>").val(data_modified);   
             $("#image_reply_div_<?php echo $template_type; ?>").show().attr('src',data_modified);   
         }
    });


    $("#video_reply_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_live_video",
      fileName:"myfile",
      maxFileSize:video_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('messenger_bot/delete_uploaded_live_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#video_reply_field_<?php echo $template_type; ?>").val('');  
              $("#video_tag_<?php echo $template_type; ?>").hide();             
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/video/"+data;
        $("#video_reply_field_<?php echo $template_type; ?>").val(file_path);   
        $("#video_tag_<?php echo $template_type; ?>").show();
        $("#video_reply_div_<?php echo $template_type; ?>").attr('src',file_path); 
      }
    });

    $("#audio_reply_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_audio_file",
      fileName:"myfile",
      maxFileSize:audio_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".amr,.mp3,.wav,.WAV,.MP3,.AMR",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('messenger_bot/delete_audio_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#audio_reply_field_<?php echo $template_type; ?>").val('');  
              $("#audio_tag_<?php echo $template_type; ?>").hide();             
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/audio/"+data;
        $("#audio_reply_field_<?php echo $template_type; ?>").val(file_path);   
        $("#audio_tag_<?php echo $template_type; ?>").show();
        $("#audio_reply_div_<?php echo $template_type; ?>").attr('src',file_path); 
      }
    });

    $("#file_reply_<?php echo $template_type; ?>").uploadFile({
      url:base_url+"messenger_bot/upload_general_file",
      fileName:"myfile",
      maxFileSize:file_upload_limit*1024*1024,
      showPreview:false,
      returnType: "json",
      dragDrop: true,
      
      showDelete: true,
      multiple:false,
      maxFileCount:1, 
      acceptFiles:".doc,.docx,.pdf,.txt,.ppt,.pptx,.xls,.xlsx,.DOC,.DOCX,.PDF,.TXT,.PPT,.PPTX,.XLS,.XLSX",
      deleteCallback: function (data, pd) {
        var delete_url="<?php echo site_url('messenger_bot/delete_general_file');?>";
        $.post(delete_url, {op: "delete",name: data},
          function (resp,textStatus, jqXHR) {  
              $("#file_reply_field_<?php echo $template_type; ?>").val('');            
          });

      },
      onSuccess:function(files,data,xhr,pd)
      {
        var file_path = base_url+"upload/file/"+data;
        $("#file_reply_field_<?php echo $template_type; ?>").val(file_path);   
      }
    });


    $("#generic_image_<?php echo $template_type; ?>").uploadFile({
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
                $("#generic_template_image_<?php echo $template_type; ?>").val('');                   
              });
         
       },
       onSuccess:function(files,data,xhr,pd)
         {
             var data_modified = base_url+"upload/image/"+user_id+"/"+data;
             $("#generic_template_image_<?php echo $template_type; ?>").val(data_modified);     
         }
      });

  
  
    <?php for($i=1; $i<=10; $i++) : ?>
      $("#generic_imageupload_<?php echo $i; ?>_<?php echo $template_type; ?>").uploadFile({
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
                  $("#carousel_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val('');                      
                });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
               $("#carousel_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val(data_modified);     
           }
      });
    <?php endfor; ?>

    <?php for($i=1; $i<=4; $i++) : ?>
      $("#list_imageupload_<?php echo $i; ?>_<?php echo $template_type; ?>").uploadFile({
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
                  $("#list_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val('');                      
                });
           
         },
         onSuccess:function(files,data,xhr,pd)
           {
               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
               $("#list_image_<?php echo $i; ?>_<?php echo $template_type; ?>").val(data_modified);     
           }
      });
    <?php endfor; ?>




    var media_counter_<?php echo $template_type; ?> =1;
    
    $j(document.body).on('click',"#media_add_button_<?php echo $template_type; ?>",function(e){
       e.preventDefault();

       var button_id = media_counter_<?php echo $template_type; ?>;
       var media_text = "#media_text_"+button_id+"_<?php echo $template_type; ?>";
       var media_type = "#media_type_"+button_id+"_<?php echo $template_type; ?>";

       var media_text_check = $(media_text).val();
       if(media_text_check == ''){
         $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
         $("#error_modal").modal();
         return;
       }

       var media_type_check = $(media_type).val();
       if(media_type_check == ''){
         $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
         $("#error_modal").modal();
         return;
       }else if(media_type_check == 'post_back'){

         var media_post_id = "#media_post_id_"+button_id+"_<?php echo $template_type; ?>";
         var media_post_id_check = $(media_post_id).val();
         if(media_post_id_check == ''){
           $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
           $("#error_modal").modal();
           return;
         }
         /*
         var page_table_id = $("#page_table_id").val();
         var new_variable_name = "js_array_"+page_table_id;

         if(jQuery.inArray(media_post_id_check.toUpperCase(), eval(new_variable_name)) !== -1){
           $("#error_modal_content").html("<?php echo $this->lang->line('The PostBack ID you have given is allready exist. Please provide different PostBack Id')?>");
           $("#error_modal").modal();
           return ;
         }
         */

       }else if(media_type_check == 'web_url'){
         var media_web_url = "#media_web_url_"+button_id+"_<?php echo $template_type; ?>";
         var media_web_url_check = $(media_web_url).val();
         if(media_web_url_check == ''){
           $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
           $("#error_modal").modal();
           return;
         }
       }else if(media_type_check == 'phone_number'){
         var media_call_us = "#media_call_us_"+button_id+"_<?php echo $template_type; ?>";
         var media_call_us_check = $(media_call_us).val();
         if(media_call_us_check == ''){
           $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
           $("#error_modal").modal();
           return;
         }
       }

       media_counter_<?php echo $template_type; ?>++;

       // remove button hide for current div and show for next div
       $(media_type).parent().parent().next().next().hide();
       var next_item_remove_parent_div = $(media_type).parent().parent().parent().next().attr('id');
       $("#"+next_item_remove_parent_div+" div:last").show();

       var x=media_counter_<?php echo $template_type; ?>;
       $("#media_row_"+x+"_<?php echo $template_type; ?>").show();
       if(media_counter_<?php echo $template_type; ?> == 3)
         $("#media_add_button_<?php echo $template_type; ?>").hide();
    });




  
    var quick_reply_button_counter_<?php echo $template_type; ?> = 1;
    
    $j(document.body).on('click',"#quick_reply_add_button_<?php echo $template_type; ?>",function(e){
      e.preventDefault();
    
      var button_id = quick_reply_button_counter_<?php echo $template_type; ?>;      
      var quick_reply_button_text = "#quick_reply_button_text_"+button_id+"_<?php echo $template_type; ?>";
      var quick_reply_post_id = "#quick_reply_post_id_"+button_id+"_<?php echo $template_type; ?>";
      var quick_reply_button_type = "#quick_reply_button_type_"+button_id+"_<?php echo $template_type; ?>";

      quick_reply_button_type = $(quick_reply_button_type).val();

      var quick_reply_post_id_check = $(quick_reply_post_id).val();
      if(quick_reply_button_type == 'post_back')
      {        
        if(quick_reply_post_id_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
          $("#error_modal").modal();
          return;
        }

        var quick_reply_button_text_check = $(quick_reply_button_text).val();

        if(quick_reply_button_text_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
          $("#error_modal").modal();
          return;
        }

      }
      if(quick_reply_button_type == '')
      {
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
        $("#error_modal").modal();
        return;
      }


      quick_reply_button_counter_<?php echo $template_type; ?>++;

      // remove button hide for current div and show for next div
      var div_id = "#quick_reply_button_type_"+button_id+"_<?php echo $template_type; ?>";
      $(div_id).parent().parent().next().next().hide();
      var next_item_remove_parent_div = $(div_id).parent().parent().parent().next().attr('id');
      $("#"+next_item_remove_parent_div+" div:last").show();
    
      var x=  quick_reply_button_counter_<?php echo $template_type; ?>;
      $("#quick_reply_row_"+x+"_<?php echo $template_type; ?>").show();
    
      if(quick_reply_button_counter_<?php echo $template_type; ?> == 11)
        $("#quick_reply_add_button_<?php echo $template_type; ?>").hide();

    });
  
  
   var text_with_button_counter_<?php echo $template_type; ?> =1;
  
   $j(document.body).on('click',"#text_with_button_add_button_<?php echo $template_type; ?>",function(e){
      e.preventDefault();

      var button_id = text_with_button_counter_<?php echo $template_type; ?>;
      var text_with_buttons_text = "#text_with_buttons_text_"+button_id+"_<?php echo $template_type; ?>";
      var text_with_button_type = "#text_with_button_type_"+button_id+"_<?php echo $template_type; ?>";

      var text_with_buttons_text_check = $(text_with_buttons_text).val();
      if(text_with_buttons_text_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
        $("#error_modal").modal();
        return;
      }

      var text_with_button_type_check = $(text_with_button_type).val();
      if(text_with_button_type_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
        $("#error_modal").modal();
        return;
      }else if(text_with_button_type_check == 'post_back'){

        var text_with_button_post_id = "#text_with_button_post_id_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_post_id_check = $(text_with_button_post_id).val();
        if(text_with_button_post_id_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
          $("#error_modal").modal();
          return;
        }
      }else if(text_with_button_type_check == 'web_url'){
        var text_with_button_web_url = "#text_with_button_web_url_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_web_url_check = $(text_with_button_web_url).val();
        if(text_with_button_web_url_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
          $("#error_modal").modal();
          return;
        }
      }else if(text_with_button_type_check == 'phone_number'){
        var text_with_button_call_us = "#text_with_button_call_us_"+button_id+"_<?php echo $template_type; ?>";
        var text_with_button_call_us_check = $(text_with_button_call_us).val();
        if(text_with_button_call_us_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
          $("#error_modal").modal();
          return;
        }
      }

      text_with_button_counter_<?php echo $template_type; ?>++;

      // remove button hide for current div and show for next div
      $(text_with_button_type).parent().parent().next().next().hide();
      var next_item_remove_parent_div = $(text_with_button_type).parent().parent().parent().next().attr('id');
      $("#"+next_item_remove_parent_div+" div:last").show();

      var x=text_with_button_counter_<?php echo $template_type; ?>;
      $("#text_with_buttons_row_"+x+"_<?php echo $template_type; ?>").show();
      if(text_with_button_counter_<?php echo $template_type; ?> == 3)
        $("#text_with_button_add_button_<?php echo $template_type; ?>").hide();
    });
  


   var  generic_with_button_counter_<?php echo $template_type; ?> =1;
  
   $j(document.body).on('click',"#generic_template_add_button_<?php echo $template_type; ?>",function(e){
      e.preventDefault();

      var button_id = generic_with_button_counter_<?php echo $template_type; ?>;
      var generic_template_button_text = "#generic_template_button_text_"+button_id+"_<?php echo $template_type; ?>";
      var generic_template_button_type = "#generic_template_button_type_"+button_id+"_<?php echo $template_type; ?>";

      var generic_template_button_text_check = $(generic_template_button_text).val();
      if(generic_template_button_text_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
        $("#error_modal").modal();
        return;
      }

      var generic_template_button_type_check = $(generic_template_button_type).val();
      if(generic_template_button_type_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
        $("#error_modal").modal();
        return;
      }else if(generic_template_button_type_check == 'post_back'){

        var generic_template_button_post_id = "#generic_template_button_post_id_"+button_id+"_<?php echo $template_type; ?>";
        var generic_template_button_post_id_check = $(generic_template_button_post_id).val();
        if(generic_template_button_post_id_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
          $("#error_modal").modal();
          return;
        }

      }else if(generic_template_button_type_check == 'web_url'){

        var generic_template_button_web_url = "#generic_template_button_web_url_"+button_id+"_<?php echo $template_type; ?>";
        var generic_template_button_web_url_check = $(generic_template_button_web_url).val();
        if(generic_template_button_web_url_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
          $("#error_modal").modal();
          return;
        }
      }else if(generic_template_button_type_check == 'phone_number'){
        var generic_template_button_call_us = "#generic_template_button_call_us_"+button_id+"_<?php echo $template_type; ?>";
        var generic_template_button_call_us_check = $(generic_template_button_call_us).val();
        if(generic_template_button_call_us_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
          $("#error_modal").modal();
          return;
        }
      }

      generic_with_button_counter_<?php echo $template_type; ?>++;

      // remove button hide for current div and show for next div
      $(generic_template_button_type).parent().parent().next().next().hide();
      var next_item_remove_parent_div = $(generic_template_button_type).parent().parent().parent().next().attr('id');
      $("#"+next_item_remove_parent_div+" div:last").show();
    
      var x=generic_with_button_counter_<?php echo $template_type; ?>;
    
      $("#generic_template_row_"+x+"_<?php echo $template_type; ?>").show();
      if(generic_with_button_counter_<?php echo $template_type; ?> == 3)
        $("#generic_template_add_button_<?php echo $template_type; ?>").hide();
   });
  
  
    <?php for($j=1; $j<=10; $j++) : ?>

      var carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?> = 1;
    
      $j(document.body).on('click',"#carousel_add_button_<?php echo $j; ?>_<?php echo $template_type; ?>",function(e){
        e.preventDefault();

        var y= carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?>;

        var carousel_button_text = "#carousel_button_text_<?php echo $j; ?>_"+y+"_<?php echo $template_type; ?>";
        var carousel_button_type = "#carousel_button_type_<?php echo $j; ?>_"+y+"_<?php echo $template_type; ?>";
    
        var carousel_button_text_check = $(carousel_button_text).val();
        if(carousel_button_text_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
          $("#error_modal").modal();
          return;
        }

        var carousel_button_type_check = $(carousel_button_type).val();
        if(carousel_button_type_check == ''){
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
          $("#error_modal").modal();
          return;
        }else if(carousel_button_type_check == 'post_back'){

          var carousel_button_post_id = "#carousel_button_post_id_<?php echo $j;?>_"+y+"_<?php echo $template_type; ?>";
          var carousel_button_post_id_check = $(carousel_button_post_id).val();
          if(carousel_button_post_id_check == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
            $("#error_modal").modal();
            return;
          }
        }else if(carousel_button_type_check == 'web_url'){

          var carousel_button_web_url = "#carousel_button_web_url_<?php echo $j;?>_"+y+"_<?php echo $template_type; ?>";
          var carousel_button_web_url_check = $(carousel_button_web_url).val();
          if(carousel_button_web_url_check == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
            $("#error_modal").modal();
            return;
          }
        }else if(carousel_button_type_check == 'phone_number'){
          var carousel_button_call_us = "#carousel_button_call_us_<?php echo $j;?>_"+y+"_<?php echo $template_type; ?>";
          var carousel_button_call_us_check = $(carousel_button_call_us).val();
          if(carousel_button_call_us_check == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
            $("#error_modal").modal();
            return;
          }
        }

        carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?> ++;

        // remove button hide for current div and show for next div
        $(carousel_button_type).parent().parent().next().next().hide();
        var next_item_remove_parent_div = $(carousel_button_type).parent().parent().parent().next().attr('id');
        $("#"+next_item_remove_parent_div+" div:last").show();

        var x= carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?>;

        $("#carousel_row_<?php echo $j; ?>_"+x+"_<?php echo $template_type; ?>").show();
        if(carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?> == 3)
          $("#carousel_add_button_<?php echo $j; ?>_<?php echo $template_type; ?>").hide();        

      });
    <?php endfor; ?>
  
  
    var carousel_template_counter_<?php echo $template_type; ?>=1;
  
    $j(document.body).on('click','#carousel_template_add_button_<?php echo $template_type; ?>',function(e){
      e.preventDefault();

      var carousel_image = "#carousel_image_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_image_check = $(carousel_image).val();
      // if(carousel_image_check == ''){
      //   $("#error_modal_content").html("<?php echo $this->lang->line('Please provide your reply image')?>");
      //   $("#error_modal").modal();
      //   return;
      // }

      var carousel_title = "#carousel_title_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_title_check = $(carousel_title).val();
      if(carousel_title_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide carousel title')?>");
        $("#error_modal").modal();
        return;
      }

      var carousel_subtitle = "#carousel_subtitle_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_subtitle_check = $(carousel_subtitle).val();
      // if(carousel_subtitle_check == ''){
      //   $("#error_modal_content").html("<?php echo $this->lang->line('Please give the sub-title')?>");
      //   $("#error_modal").modal();
      //   return;
      // }

      var carousel_image_destination_link = "#carousel_image_destination_link_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var carousel_image_destination_link_check = $(carousel_image_destination_link).val();
      // if(carousel_image_destination_link_check == ''){
      //   $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Image Click Destination Link')?>");
      //   $("#error_modal").modal();
      //   return;        
      // }

      carousel_template_counter_<?php echo $template_type; ?>++;
    
      var x = carousel_template_counter_<?php echo $template_type; ?>;
    
      $("#carousel_div_"+x+"_<?php echo $template_type; ?>").show();
      $("#carousel_row_"+x+"_1"+"_<?php echo $template_type; ?>").show();
      if( carousel_template_counter_<?php echo $template_type; ?> == 10)
        $("#carousel_template_add_button_<?php echo $template_type; ?>").hide();
    });


    var list_template_counter_<?php echo $template_type; ?>=2;
  
    $j(document.body).on('click','#list_template_add_button_<?php echo $template_type; ?>',function(e){
      e.preventDefault();

      var list_button_text = "#list_with_buttons_text_<?php echo $template_type; ?>";
	    var list_button_type = "#list_with_button_type_<?php echo $template_type; ?>";

	    var list_button_text_check = $(list_button_text).val();
	    if(list_button_text_check == ''){
	      $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
	      $("#error_modal").modal();
	      return;
	    }

	    var list_button_type_check = $(list_button_type).val();
	    if(list_button_type_check == ''){
	      $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
	      $("#error_modal").modal();
	      return;
	    }else if(list_button_type_check == 'post_back'){

	      var list_button_post_id = "#list_with_button_post_id_<?php echo $template_type; ?>";
	      var list_button_post_id_check = $(list_button_post_id).val();
	      if(list_button_post_id_check == ''){
	        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
	        $("#error_modal").modal();
	        return;
	      }
	    }else if(list_button_type_check == 'web_url'){

	      var list_button_web_url = "#list_with_button_web_url_<?php echo $template_type; ?>";
	      var list_button_web_url_check = $(list_button_web_url).val();
	      if(list_button_web_url_check == ''){
	        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
	        $("#error_modal").modal();
	        return;
	      }
	    }else if(list_button_type_check == 'phone_number'){
	      var list_button_call_us = "#list_with_button_call_us_<?php echo $template_type; ?>";
	      var list_button_call_us_check = $(list_button_call_us).val();
	      if(list_button_call_us_check == ''){
	        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
	        $("#error_modal").modal();
	        return;
	      }
	    }


	    var prev_list_image_counter = eval(list_template_counter_<?php echo $template_type; ?>+"-1");
      var list_image_1 = "#list_image_"+prev_list_image_counter+"_"+<?php echo $template_type; ?>;
      var list_image_check_1 = $(list_image_1).val();
      if(list_image_check_1 == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please provide your reply image')?>");
        $("#error_modal").modal();
        return;
      }

      var list_image = "#list_image_"+list_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var list_image_check = $(list_image).val();
      if(list_image_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please provide your reply image')?>");
        $("#error_modal").modal();
        return;
      }

      var prev_list_title_counter = eval(list_template_counter_<?php echo $template_type; ?>+"-1");
      var list_title_1 = "#list_title_"+prev_list_title_counter+"_"+<?php echo $template_type; ?>;
      var list_title_check_1 = $(list_title_1).val();
      if(list_title_check_1 == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide list title')?>");
        $("#error_modal").modal();
        return;
      }

      var list_title = "#list_title_"+list_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var list_title_check = $(list_title).val();
      if(list_title_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide list title')?>");
        $("#error_modal").modal();
        return;
      }

      var prev_list_dest_counter = eval(list_template_counter_<?php echo $template_type; ?>+"-1");
      var list_image_destination_link_1 = "#list_image_destination_link_"+prev_list_dest_counter+"_"+<?php echo $template_type; ?>;
      var list_image_destination_link_check_1 = $(list_image_destination_link_1).val();
      if(list_image_destination_link_check_1 == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Image Click Destination Link')?>");
        $("#error_modal").modal();
        return;        
      }

      var list_image_destination_link = "#list_image_destination_link_"+list_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var list_image_destination_link_check = $(list_image_destination_link).val();
      if(list_image_destination_link_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Image Click Destination Link')?>");
        $("#error_modal").modal();
        return;        
      }

      list_template_counter_<?php echo $template_type; ?>++;
    
      var x = list_template_counter_<?php echo $template_type; ?>;
    
      $("#list_div_"+x+"_<?php echo $template_type; ?>").show();
      if( list_template_counter_<?php echo $template_type; ?> == 4)
        $("#list_template_add_button_<?php echo $template_type; ?>").hide();
    });
  
  <?php } ?>
  
  
  
    $(document.body).on('click','.item_remove',function(){
      var counter_variable = $(this).attr('counter_variable');
      var row_id = $(this).attr('row_id');

      var first_column_id = $(this).attr('first_column_id');
      var second_column_id = $(this).attr('second_column_id');
      var add_more_button_id = $(this).attr('add_more_button_id');

      var item_remove_postback = $(this).attr('third_postback');
      var item_remove_weburl = $(this).attr('third_weburl');
      var item_remove_callus = $(this).attr('third_callus');

      $("#"+first_column_id).val('');
      $("#"+first_column_id).removeAttr('readonly');
      var item_remove_button_type = $("#"+second_column_id).val();
      $("#"+second_column_id).val('');

      if(item_remove_button_type == 'post_back')
      {
        $("#"+item_remove_postback).val('');
      }
      else if (item_remove_button_type == 'web_url')
      {
        $("#"+item_remove_weburl).val('');
      }
      else
        $("#"+item_remove_callus).val('');

      $("#"+row_id).hide();
      eval(counter_variable+"--");
      var temp = eval(counter_variable);

      if(temp != 1)
      {        
        var previous_item_remove_div = $("#"+row_id).prev('div').attr('id');
        $("#"+previous_item_remove_div+" div:last").show();
      }
      $(this).parent().hide();

      if(temp < 3) $("#"+add_more_button_id).show();

    });


    $(document.body).on('click','.delete_bot',function(){
      var id = $(this).attr('id');
      var somethingwentwrong = "<?php echo $somethingwentwrong; ?>";
      var doyoureallywanttodeletethisbot = "<?php echo $doyoureallywanttodeletethisbot; ?>";
      var link="<?php echo $redirect_url; ?>"; 

      alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttodeletethisbot, 
        function(){ 
          $.ajax({
             type:'POST' ,
             url: "<?php echo base_url('messenger_bot/delete_bot')?>",
             data: {id:id},
             success:function(response)
             {
              if(response=='1')
              {
                window.location.assign(link);
                alertify.success('<?php echo $this->lang->line("your data has been successfully deleted from the database."); ?>');
              }
              else alertify.alert('<?php echo $this->lang->line("Alert"); ?>',somethingwentwrong,function(){});
             }
          });
        },
        function(){     
      });
    });




    $j(document.body).on('change','.media_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      var which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" input").val(""); 
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {           
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" input").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" input").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
      }
      else if(button_type == 'web_url')
      {
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#media_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#media_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
    });





    $j(document.body).on('change','.quick_reply_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      var which_block_is_clicked="";
    
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#quick_reply_button_text_"+which_number_is_clicked+"_"+which_block_is_clicked).removeAttr('readonly');
        $("#quick_reply_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
      }
      else
      {
        $("#quick_reply_button_text_"+which_number_is_clicked+"_"+which_block_is_clicked).attr('readonly','readonly');
        $("#quick_reply_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      // alert(which_number_is_clicked);
    });


    $j(document.body).on('change','.text_with_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      var which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="human_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }

      }
      else if(button_type == 'web_url')
      {
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      // alert(which_number_is_clicked);
    });


    $j(document.body).on('change','.generic_template_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      which_number_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 2];
      which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];
    
    

      if(button_type == 'post_back')
      {
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="human_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
      }
      else if(button_type == 'web_url')
      {
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
      }
      // alert(which_number_is_clicked);
    });



    $j(document.body).on('change','.carousel_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked = which_number_is_clicked.split('_');
    
      var first = which_number_is_clicked[which_number_is_clicked.length - 2];
      var second = which_number_is_clicked[which_number_is_clicked.length - 3];
    
      var block_template_third= which_number_is_clicked[which_number_is_clicked.length - 1];

      if(button_type == 'post_back')
      {
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).show();
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
        if(option_id=="human_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
      }
      else if(button_type == 'web_url')
      {
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).show();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).show();
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
      }
      else
      {
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).hide();
      }
      // alert(which_number_is_clicked);
    });


    $j(document.body).on('change','.list_with_button_type_class',function(){
      var button_type = $(this).val();
      var which_number_is_clicked = $(this).attr('id');
      which_number_is_clicked_main = which_number_is_clicked.split('_');
      var which_block_is_clicked = which_number_is_clicked_main[which_number_is_clicked_main.length - 1];

      if(button_type == 'post_back')
      {
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='UNSUBSCRIBE_QUICK_BOXER']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='RESUBSCRIBE_QUICK_BOXER']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_HUMAN']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked+" select option[value='YES_START_CHAT_WITH_BOT']").remove();
        $("#list_with_button_postid_div_"+which_block_is_clicked).show();
        $("#list_with_button_web_url_div_"+which_block_is_clicked).hide();
        $("#list_with_button_call_us_div_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("UNSUBSCRIBE_QUICK_BOXER"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("RESUBSCRIBE_QUICK_BOXER"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
        if(option_id=="human_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_HUMAN").text("<?php echo $this->lang->line('Chat with Human');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_HUMAN"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
        if(option_id=="robot_postback")
        {
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","YES_START_CHAT_WITH_BOT").text("<?php echo $this->lang->line('Chat with Robot');?>")); 
           $("#list_with_button_postid_div_"+which_block_is_clicked+" select").val("YES_START_CHAT_WITH_BOT"); 
           $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        }
      }
      else if(button_type == 'web_url')
      {
        $("#list_with_button_web_url_div_"+which_block_is_clicked).show();
        $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        $("#list_with_button_call_us_div_"+which_block_is_clicked).hide();
      }
      else if(button_type == 'phone_number')
      {
        $("#list_with_button_call_us_div_"+which_block_is_clicked).show();
        $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        $("#list_with_button_web_url_div_"+which_block_is_clicked).hide();
      }
      else
      {
        $("#list_with_button_postid_div_"+which_block_is_clicked).hide();
        $("#list_with_button_web_url_div_"+which_block_is_clicked).hide();
        $("#list_with_button_call_us_div_"+which_block_is_clicked).hide();
      }
    });




    $(document.body).on('click','#submit',function(e){   
      e.preventDefault();

      /*
      var selected_postback_array = [];
      $(".push_postback").each(function(){
        if($(this).is(":visible"))
          selected_postback_array.push($(this).val());
      });

      var reportRecipientsDuplicate = [];
      for (var i = 0; i < selected_postback_array.length - 1; i++) {
          if (selected_postback_array[i + 1] == selected_postback_array[i]) {
              reportRecipientsDuplicate.push(selected_postback_array[i]);
          }
      }
      
      if(reportRecipientsDuplicate.length > 0)
      {
        $("#error_modal_content").html("<?php echo $this->lang->line('Please select different postback id for each button.');?>");
        $("#error_modal").modal();
        return;
      }
      */

      var bot_name = $("#bot_name").val();      
      // var bot_type = $("input[name=bot_type]:checked").val();
      var keyword_type = $("input[name=keyword_type]:checked").val();

      if(typeof($("input[name=keyword_type]:checked").val()) == 'undefined')
      {
        $("#error_modal_content").html("<?php echo $this->lang->line('Please select a reply type form (Reply/Post-back/No Match/Get Started)');?>");
        $("#error_modal").modal();
        return;
      }

      if(bot_name == '')
      {
        $("#error_modal_content").html("<?php echo $this->lang->line('Please Give Bot Name');?>");
        $("#error_modal").modal();
        return;
      }


      if(keyword_type == 'post-back')
      {
        if($("#keywordtype_postback_id").val() == '' || typeof($("#keywordtype_postback_id").val()) == 'undefined' || $("#keywordtype_postback_id").val() == null)
        {
          $("#error_modal_content").html("<?php echo $this->lang->line('Please provide postback id');?>");
          $("#error_modal").modal();
          return;
        }
      }

      if(keyword_type == 'reply')
      {
        var keywords_list = $("#keywords_list").val();
        if(keywords_list =='')
        {
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Keywords In Comma Separated');?>");
          $("#error_modal").modal();
          return;
        }
      }

    for(var m=1; m<=multiple_template_add_button_counter; m++)
    {
        var template_type = $("#template_type_"+m).val();

        if(template_type == 'text')
        {
          var text_reply = $("#text_reply_"+m).val();
          if(text_reply == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply Message')?>");
            $("#error_modal").modal();
            return;
          }
        }

        if(template_type == "image")
        {
          var image_reply_field =$("#image_reply_field_"+m).val();
          if(image_reply_field == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply Image')?>");
            $("#error_modal").modal();
            return;
          }
        }

        if(template_type == "audio")
        {
          var audio_reply_field = $("#audio_reply_field_"+m).val();
          if(audio_reply_field == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply Audio')?>");
            $("#error_modal").modal();
            return;
          }
        }

        if(template_type == "video")
        {
          var video_reply_field = $("#video_reply_field_"+m).val();
          if(video_reply_field == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply Video')?>");
            $("#error_modal").modal();
            return;          
          }
        }


        if(template_type == "file")
        {
          var file_reply_field = $("#file_reply_field_"+m).val();
          if(file_reply_field == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply File')?>");
            $("#error_modal").modal();
            return;          
          }
        }




        if(template_type == "media")
        {
          var media_input = $("#media_input_"+m).val();
          if(media_input == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Media URL')?>");
            $("#error_modal").modal();
            return;          
          }

          var facebook_url = media_input.match(/business.facebook.com/g);
          var facebook_url2 = media_input.match(/www.facebook.com/g);

          if(facebook_url == null && facebook_url2 == null)
          {
            $("#error_modal_content").html("<?php echo $this->lang->line('Please provide Facebook content URL as Media URL')?>");
            $("#error_modal").modal();
            return; 
          }

          var submited_media_counter = eval("media_counter_"+m);

          for(var n=1; n<=submited_media_counter; n++)
          {

            var media_text = "#media_text_"+n+"_"+m;
            var media_type = "#media_type_"+n+"_"+m;

            var media_text_check = $(media_text).val();
            if(media_text_check == ''){
              $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
              $("#error_modal").modal();
              return;
            }

            var media_type_check = $(media_type).val();
            if(media_type_check == ''){
              $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
              $("#error_modal").modal();
              return;
            }else if(media_type_check == 'post_back'){

              var media_post_id = "#media_post_id_"+n+"_"+m;
              var media_post_id_check = $(media_post_id).val();
              if(media_post_id_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
                $("#error_modal").modal();
                return;
              }

              /*
              var page_table_id = $("#page_table_id").val();
              var new_variable_name = "js_array_"+page_table_id;

              if(jQuery.inArray(media_post_id_check.toUpperCase(), eval(new_variable_name)) !== -1){
                $("#error_modal_content").html("<?php echo $this->lang->line('The PostBack ID you have given is allready exist. Please provide different PostBack Id')?>");
                $("#error_modal").modal();
                return ;
              }
              */
            }else if(media_type_check == 'web_url'){
              var media_web_url = "#media_web_url_"+n+"_"+m;
              var media_web_url_check = $(media_web_url).val();
              if(media_web_url_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
                $("#error_modal").modal();
                return;
              }
            }else if(media_type_check == 'phone_number'){
              var media_call_us = "#media_call_us_"+n+"_"+m;
              var media_call_us_check = $(media_call_us).val();
              if(media_call_us_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
                $("#error_modal").modal();
                return;
              }
            }
          }
          
        }




        if(template_type == "quick reply")
        {
          var quick_reply_text = $("#quick_reply_text_"+m).val();
          if(quick_reply_text == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply Message')?>");
            $("#error_modal").modal();
            return;
          }
          var submited_quick_reply_button_counter = eval("quick_reply_button_counter_"+m);

          for(var n=1; n<=submited_quick_reply_button_counter; n++)
          {
            var quick_reply_button_text = "#quick_reply_button_text_"+n+"_"+m;
            var quick_reply_post_id = "#quick_reply_post_id_"+n+"_"+m;
            var quick_reply_button_type = "#quick_reply_button_type_"+n+"_"+m;

            quick_reply_button_type = $(quick_reply_button_type).val();

            var quick_reply_post_id_check = $(quick_reply_post_id).val();
            if(quick_reply_button_type == 'post_back')
            {        
              if(quick_reply_post_id_check == '' || quick_reply_post_id_check == null){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
                $("#error_modal").modal();
                return;
              }

              var quick_reply_button_text_check = $(quick_reply_button_text).val();

              if(quick_reply_button_text_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
                $("#error_modal").modal();
                return;
              }

            }
            if(quick_reply_button_type == '')
            {
              $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
              $("#error_modal").modal();
              return;
            }
          }    
        }


        if(template_type == "text with buttons")
        {
          var text_with_buttons_input = $("#text_with_buttons_input_"+m).val();
          if(text_with_buttons_input == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply Message')?>");
            $("#error_modal").modal();
            return;          
          }

          var submited_text_with_button_counter = eval("text_with_button_counter_"+m);

          for(var n=1; n<=submited_text_with_button_counter; n++)
          {

            var text_with_buttons_text = "#text_with_buttons_text_"+n+"_"+m;
            var text_with_button_type = "#text_with_button_type_"+n+"_"+m;

            var text_with_buttons_text_check = $(text_with_buttons_text).val();
            if(text_with_buttons_text_check == ''){
              $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
              $("#error_modal").modal();
              return;
            }

            var text_with_button_type_check = $(text_with_button_type).val();
            if(text_with_button_type_check == ''){
              $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
              $("#error_modal").modal();
              return;
            }else if(text_with_button_type_check == 'post_back'){

              var text_with_button_post_id = "#text_with_button_post_id_"+n+"_"+m;
              var text_with_button_post_id_check = $(text_with_button_post_id).val();
              if(text_with_button_post_id_check == '' || text_with_button_post_id_check == null){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
                $("#error_modal").modal();
                return;
              }

            }else if(text_with_button_type_check == 'web_url'){
              var text_with_button_web_url = "#text_with_button_web_url_"+n+"_"+m;
              var text_with_button_web_url_check = $(text_with_button_web_url).val();
              if(text_with_button_web_url_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
                $("#error_modal").modal();
                return;
              }
            }else if(text_with_button_type_check == 'phone_number'){
              var text_with_button_call_us = "#text_with_button_call_us_"+n+"_"+m;
              var text_with_button_call_us_check = $(text_with_button_call_us).val();
              if(text_with_button_call_us_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
                $("#error_modal").modal();
                return;
              }
            }
          }
          
        }

        if(template_type == "generic template")
        {
          var generic_template_image = $("#generic_template_image_"+m).val();
          // if(generic_template_image == ''){
          //   $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Reply Image')?>");
          //   $("#error_modal").modal();
          //   return;          
          // }    

          var generic_template_title = $("#generic_template_title_"+m).val();
          if(generic_template_title == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please give the title')?>");
            $("#error_modal").modal();
            return;          
          }

          var generic_template_subtitle = $("#generic_template_subtitle_"+m).val();
          // if(generic_template_subtitle == ''){
          //   $("#error_modal_content").html("<?php echo $this->lang->line('Please give the sub-title')?>");
          //   $("#error_modal").modal();
          //   return;          
          // }


          var submited_generic_button_counter = eval("generic_with_button_counter_"+m);
          for(var n=1; n<=submited_generic_button_counter; n++)
          {            
            var generic_template_button_text = "#generic_template_button_text_"+n+"_"+m;
            var generic_template_button_type = "#generic_template_button_type_"+n+"_"+m;

            var generic_template_button_text_check = $(generic_template_button_text).val();
            var generic_template_button_type_check = $(generic_template_button_type).val();

            if(generic_template_button_text_check == ''  && generic_template_button_type_check!=''){
              $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
              $("#error_modal").modal();
              return;
            }

            // if(generic_template_button_type_check == ''){
            //   $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
            //   $("#error_modal").modal();
            //   return;
            // }else 

            if(generic_template_button_type_check == 'post_back'){

              var generic_template_button_post_id = "#generic_template_button_post_id_"+n+"_"+m;
              var generic_template_button_post_id_check = $(generic_template_button_post_id).val();
              if(generic_template_button_post_id_check == '' || generic_template_button_post_id_check == null){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
                $("#error_modal").modal();
                return;
              }

            }else if(generic_template_button_type_check == 'web_url'){

              var generic_template_button_web_url = "#generic_template_button_web_url_"+n+"_"+m;
              var generic_template_button_web_url_check = $(generic_template_button_web_url).val();
              if(generic_template_button_web_url_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
                $("#error_modal").modal();
                return;
              }
            }else if(generic_template_button_type_check == 'phone_number'){
              var generic_template_button_call_us = "#generic_template_button_call_us_"+n+"_"+m;
              var generic_template_button_call_us_check = $(generic_template_button_call_us).val();
              if(generic_template_button_call_us_check == ''){
                $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
                $("#error_modal").modal();
                return;
              }
            }
          }
          
        }


        if(template_type == "carousel")
        {
          var submited_carousel_template_counter = eval("carousel_template_counter_"+m);
          for(var n=1; n<=submited_carousel_template_counter; n++)
          {
            var carousel_image = "#carousel_image_"+n+"_"+m;
            var carousel_image_check = $(carousel_image).val();
            // if(carousel_image_check == ''){
            //   $("#error_modal_content").html("<?php echo $this->lang->line('Please provide your reply image')?>");
            //   $("#error_modal").modal();
            //   return;
            // }

            var carousel_title = "#carousel_title_"+n+"_"+m;
            var carousel_title_check = $(carousel_title).val();
            if(carousel_title_check == ''){
              $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide carousel title')?>");
              $("#error_modal").modal();
              return;
            }

            var carousel_subtitle = "#carousel_subtitle_"+n+"_"+m;
            var carousel_subtitle_check = $(carousel_subtitle).val();
            // if(carousel_subtitle_check == ''){
            //   $("#error_modal_content").html("<?php echo $this->lang->line('Please give the sub-title')?>");
            //   $("#error_modal").modal();
            //   return;
            // }

            var carousel_image_destination_link = "#carousel_image_destination_link_"+n+"_"+m;
            var carousel_image_destination_link_check = $(carousel_image_destination_link).val();
            // if(carousel_image_destination_link_check == ''){
            //   $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Image Click Destination Link')?>");
            //   $("#error_modal").modal();
            //   return;        
            // }
          }

          <?php for($j=1; $j<=10; $j++) : ?>
            var submited_carousel_add_button_counter = eval("carousel_add_button_counter_<?php echo $j; ?>_"+m);
            for(var n=1; n<=submited_carousel_add_button_counter; n++)
            {
              var carousel_button_text = "#carousel_button_text_<?php echo $j; ?>_"+n+"_"+m;
              var carousel_button_type = "#carousel_button_type_<?php echo $j; ?>_"+n+"_"+m;

              if($(carousel_button_type).parent().parent().parent().is(":visible"))
              {
                var carousel_button_text_check = $(carousel_button_text).val();
                var carousel_button_type_check = $(carousel_button_type).val();

                if(carousel_button_text_check == ''  && carousel_button_type_check!=""){
                  $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
                  $("#error_modal").modal();
                  return;
                }

                // if(carousel_button_type_check == ''){
                //   $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
                //   $("#error_modal").modal();
                //   return;
                // }else 

                if(carousel_button_type_check == 'post_back'){

                  var carousel_button_post_id = "#carousel_button_post_id_<?php echo $j;?>_"+n+"_"+m;
                  var carousel_button_post_id_check = $(carousel_button_post_id).val();
                  if(carousel_button_post_id_check == '' || carousel_button_post_id_check == null){
                    $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your PostBack Id')?>");
                    $("#error_modal").modal();
                    return;
                  }
                }else if(carousel_button_type_check == 'web_url'){

                  var carousel_button_web_url = "#carousel_button_web_url_<?php echo $j;?>_"+n+"_"+m;
                  var carousel_button_web_url_check = $(carousel_button_web_url).val();
                  if(carousel_button_web_url_check == ''){
                    $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Web Url')?>");
                    $("#error_modal").modal();
                    return;
                  }
                }else if(carousel_button_type_check == 'phone_number'){
                  var carousel_button_call_us = "#carousel_button_call_us_<?php echo $j;?>_"+n+"_"+m;
                  var carousel_button_call_us_check = $(carousel_button_call_us).val();
                  if(carousel_button_call_us_check == ''){
                    $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Phone Number')?>");
                    $("#error_modal").modal();
                    return;
                  }
                }
              }
              
              

            }
          <?php endfor; ?>

        }


    }

      $("#submit").addClass("disabled");
      var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';

      $("#submit_status").removeClass('alert').removeClass('alert-success').removeClass('alert-danger').html('<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">');

      $("input:not([type=hidden])").each(function(){
        if($(this).is(":visible") == false)
          $(this).attr("disabled","disabled");
      });


      var queryString = new FormData($("#messenger_bot_form")[0]);
        $.ajax({
          type:'POST' ,
          url: base_url+"messenger_bot/ajax_generate_messenger_bot",
          data: queryString,
          dataType : 'JSON',
          // async: false,
          cache: false,
          contentType: false,
          processData: false,
          success:function(response){
              location.reload();
          }

        });

    });







    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
    $("#bot_settings_data_table").DataTable();
    
    $j(document.body).on('click','#add_bot_settings',function(){
       $("#add_bot_settings_modal").removeClass('hidden');
       $(".box.box-widget.widget-user-2").hide();
       $("#bot_success").hide();
       // $('html, body').animate({scrollTop: $("#add_bot_settings_modal").offset().top}, 2000);
    });

  $(document.body).on('click','.lead_first_name',function(){
  
      var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
			textAreaTxt = textAreaTxt.substring(0, lastIndex);
			
	    var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
	    var new_text = textAreaTxt + txtToAdd;
	   	$(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
	   	$(this).parent().next().next().next().children('.emojionearea-editor').click();
			
			
  });

  $(document.body).on('click','.lead_last_name',function(){
  
    var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();

    var lastIndex = textAreaTxt.lastIndexOf("<br>");

    if(lastIndex!='-1')
    textAreaTxt = textAreaTxt.substring(0, lastIndex);

    var txtToAdd = " #LEAD_USER_LAST_NAME# ";
    var new_text = textAreaTxt + txtToAdd;
    $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
    $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		   
		   
  });

    $(document.body).on('click','.add_template',function(e){
        e.preventDefault();
        var current_id=$(this).prev().attr("id");
        var page_id="<?php echo $page_info['id'];?>";
        if(page_id=="")
        {
          alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please select a page first')?>",function(){});
          return false;
        }
        $("#add_template_modal").attr("current_id",current_id);
        $("#add_template_modal").modal();
    });

    $(document.body).on('click','.ref_template',function(e){
      e.preventDefault();
      var current_val=$(this).prev().prev().val();
      var current_id=$(this).prev().prev().attr("id");
      var page_id="<?php echo $page_info['id'];?>";
       if(page_id=="")
       {
         alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please select a page first')?>",function(){});
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_postback",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response).val(current_val);
         }
       });
    });

    $('#add_template_modal').on('hidden.bs.modal', function (e) { 
      var current_id=$("#add_template_modal").attr("current_id");
      var page_id="<?php echo $page_info['id'];?>";
       if(page_id=="")
       {
         alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please select a page first')?>",function(){});
         return false;
       }
       $.ajax({
         type:'POST' ,
         url: base_url+"messenger_bot/get_postback",
         data: {page_id:page_id},
         success:function(response){
           $("#"+current_id).html(response);
         }
       });
    });


  }); 

 function refresh_template()
  {
    var page_id="<?php echo $page_info['id'];?>";
    if(page_id=="")
    {
      alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please select a page first')?>",function(){});
      return false;
    }
    $.ajax({
      type:'POST' ,
      url: base_url+"messenger_bot/get_postback",
      data: {page_id:page_id,order_by:"template_name"},
      success:function(response){
        $(".push_postback").html(response);
      }
    });
  }

</script>


<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h4>
      </div>
      <div class="modal-body"> 
        <iframe width="100%" height="450px" frameborder="0" src=""></iframe> 
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-info"></i> <?php echo $this->lang->line('campaign error'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="alert text-center alert-warning" id="error_modal_content">
          
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="media_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content modal-lg">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-info"></i> <?php echo $this->lang->line("How to get meida URL?"); ?></h4>
      </div>
      <div class="modal-body">
        <div>
          <h4>To get the Facebook URL for an image or video, do the following:</h4>
          <ol>
            <li>Click the image or video thumbnail to open the full-size view.</li>
            <li>Copy the URL from your browser's address bar.</li>
          </ol>
          <p>Facebook URLs should be in the following base format:</p>
          <table class='table table-condensed table-bordered table-hover table-striped' >
            <tr>
              <th>Media Type</th>
              <th>Media Source</th>
              <th>URL Format</th>
            </tr>
            <tr>
              <td>Video</td>
              <td>Facebook Page</td>
              <td>https://business.facebook.com/<b>PAGE_NAME</b>/videos/<b>NUMERIC_ID</b></td>
            </tr>
            <tr>
              <td>Video</td>
              <td>Facebook Account</td>
              <td>https://www.facebook.com/<b>USERNAME</b>/videos/<b>NUMERIC_ID</b>/</td>
            </tr>
            <tr>
              <td>Image</td>
              <td>Facebook Page</td>
              <td>https://business.facebook.com/<b>PAGE_NAME</b>/photos/<b>NUMERIC_ID</b></td>
            </tr>
            <tr>
              <td>Image</td>
              <td>Facebook Account</td>
              <td>https://www.facebook.com/photo.php?fbid=<b>NUMERIC_ID</b></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

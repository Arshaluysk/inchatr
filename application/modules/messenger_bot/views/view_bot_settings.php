<?php
  $postback_id_str = $bot_info['postback_id'];
  $postback_id_array = explode(",",$postback_id_str);
  $full_message_json = $bot_info['message'];
  $full_message_array = json_decode($full_message_json,true);
  // $full_message = $full_message_array['message'];
  $redirect_url = site_url('messenger_bot/bot_settings/').$page_info['id'];
?>
<?php $this->load->view("include/upload_js"); ?>

<style type="text/css">
  *[disabled="disabled"]{background: #fff !important; border:none !important; box-shadow:none !important;}
  .item_remove
  {
  margin-top: 9px; 
  margin-left: -20px;
  cursor: pointer !important;
  }
  .remove_reply
  {
  margin:10px 10px 0 0;
  font-size: 30px;
  cursor: pointer !important;
  }
  .add_template,.ref_template{font-size: 10px;display: none;}
  .emojionearea.form-control{padding-top:12px !important;}
  .emojionearea.emojionearea-disable:before{background:  #fff !important;}
  .img_holder div:not(:first-child){display: none;position:fixed;bottom:87px;right:40px;}
  .img_holder div:first-child{position:fixed;bottom:87px;right:40px;}
  .lead_first_name,.lead_last_name{background: #EEE;border-radius: 0;display: none;}
  a[data-toggle="popover"]{display: none;}
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

  <div class="" id="add_bot_settings_modal">
    <div class="modal-dialog" style="width:100%;margin:20px 0 0 0;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class='fa fa-eye'></i> <?php echo $this->lang->line("View Bot Settings");?> : <a target='_BLANK' href='https://facebook.com/<?php echo $page_info['page_id'];?>'><?php echo $page_info['page_name'];?></a></h4>
        </div>
        <div class="modal-body" style="padding-left:30px;"> 
          <img id="loader" src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>" style="margin-top:20px;margin-bottom: 30px;" class="center-block">
          <div class="row">
            <div class="col-xs-12 col-md-9">
              <form action="#" method="post" id="messenger_bot_form" style="padding-left: 0;">
                <input type="hidden" name="id" id="id" value="<?php echo  $bot_info['id'];?>">
                <input type="hidden" name="page_id" id="page_id" value="<?php echo  $page_info['page_id'];?>">
                <input type="hidden" name="page_table_id" id="page_table_id" value="<?php echo  $page_info['id'];?>">
                <br>                
                <div class="text-left">
                 <?php 
                   foreach ($keyword_types as $key => $value)
                   { 
                    if($value == 'email-quick-reply' || $value == 'phone-quick-reply' || $value == 'location-quick-reply') continue;
                    ?>
                       <div class="inline css-label-container"><input type="radio" name="keyword_type" value="<?php echo $value; ?>" id="keyword_type_<?php echo $value;?>" class="css-checkbox keyword_type" <?php if($bot_info['keyword_type'] == $value) echo "checked"; ?>/><label for="keyword_type_<?php echo $value;?>" class="css-label radGroup2"><?php echo $this->lang->line($value);?></label></div>
                       &nbsp;&nbsp;                  
                   <?php
                   } 
                ?>  
               </div>

               <br/>
                <div class="row"> 
                  <div class="col-xs-12"> 
                    <div class="form-group">
                      <label><?php echo $this->lang->line("Bot Name"); ?></label>
                      <input type="text" name="bot_name" value="<?php if(set_value('bot_name')) echo set_value('bot_name');else {if(isset($bot_info['bot_name'])) echo $bot_info['bot_name'];}?>" id="bot_name" class="form-control">
                    </div>       
                  </div>  
                </div>
               
               <div class="row" id="keywords_div" style="display: none;"> 
                  <div class="col-xs-12">              
                    <div class="form-group">
                      <label><?php echo $this->lang->line("keywords in comma separated"); ?></label>
                      <textarea class="form-control"  name="keywords_list" id="keywords_list"><?php if(isset($bot_info['keywords'])) echo $bot_info['keywords'];?></textarea>
                    </div>        
                  </div>  
                </div>

               <div class="row" id="postback_div" style="display: none;"> 
                  <div class="col-xs-12">              
                    <div class="form-group">
                      <label><?php echo $this->lang->line("postback id"); ?></label>
                      <!-- <input type="text" name="keywordtype_postback_id" id="keywordtype_postback_id" class=""> -->

                      <select multiple="multiple"  class="form-control" id="keywordtype_postback_id" name="keywordtype_postback_id[]">
                     

                      <?php
                          $total_postback_id_array = array();
                          foreach($postback_ids as $value)
                          {
                            if(!in_array($value['postback_id'], $current_postbacks))
                               $total_postback_id_array[] = strtoupper($value['postback_id']);

                            if($value["template_for"]=="unsubscribe" || $value["template_for"]=="resubscribe" || $value["template_for"]=="email-quick-reply" || $value["template_for"]=="phone-quick-reply" || $value["template_for"]=="location-quick-reply" || $value["is_template"] == "1") continue;

                            $array_key = $value['postback_id'];
                            $array_value = $value['postback_id']." (".$value['bot_name'].")";
                            if($value['use_status'] == '0')
                            {                              
                              echo "<option value='{$array_key}'>{$array_value}</option>";
                            } 
                            else
                            {
                              if(in_array($array_key, $postback_id_array))
                              {
                                echo "<option value='{$array_key}' selected >{$array_value}</option>";
                              } 
                              
                            }                        
                          }
                      ?> 
                      
                      </select>
                    </div>        
                  </div>  
                </div>                    

          <?php 
          if(!isset($full_message_array[1]))
          {
            $full_message_array[1] = $full_message_array;
            $full_message_array[1]['message']['template_type'] = $bot_info['template_type'];
          }


          $active_reply_count = 0;
          for($k=1;$k<=3;$k++){ 

            $full_message[$k] = isset($full_message_array[$k]['message']) ? $full_message_array[$k]['message'] : array();

            if(isset($full_message[$k]["template_type"]))
              $full_message[$k]["template_type"] = str_replace('_', ' ', $full_message[$k]["template_type"]);       

          ?>

              <div id="multiple_template_div_<?php echo $k; ?>" 
                <?php 
                  if(!isset($full_message[$k]["template_type"]))
                    echo "style='display : none; margin-top:20px;background:#fff; border:.8px dashed ".$THEMECOLORCODE.";'"; 
                  else
                  {
                    $active_reply_count++;
                    echo "style='margin-top:20px;background:#fff; border:.8px dashed ".$THEMECOLORCODE.";'";
                  }
                ?>
              >  
                <?php if($k != 1 && $k == count($full_message_array)) : ?>
                  <i class="fa fa-2x fa-times-circle remove_reply pull-right red" row_id="multiple_template_div_<?php echo $k; ?>" counter_variable="" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                <?php else : ?>
                  <i class="fa fa-2x fa-times-circle remove_reply pull-right red" style="display: none;" row_id="multiple_template_div_<?php echo $k; ?>" counter_variable="" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                <?php endif; ?>

                <div style="padding: 0 15px 15px 15px !important;">
                  <label for="template_type"><?php echo $this->lang->line("");?></label>          
                  <div class="form-group">
                    <span class="input-group-addon"><?php echo $this->lang->line("Template Type");?> : <?php echo $full_message[$k]["template_type"]; ?></span>
                     <select class="form-control form-control-new hidden" id="template_type_<?php echo $k; ?>" name="template_type_<?php echo $k; ?>">
                      <?php 

                       foreach ($templates as $key => $value)
                       {
                        if(isset($full_message[$k]["template_type"]) && $full_message[$k]["template_type"] == $value) $selected='selected';
                        else $selected='';
                        echo "<option value='{$value}' {$selected}>{$this->lang->line($value)}</option>";
                       } 
                      ?>
                    </select>
                  </div>

                  <div class="row" id="text_div_<?php echo $k; ?>"> 
                    <div class="col-xs-12">              
                      <div class="form-group">
                        <label><?php echo $this->lang->line("reply message"); ?></label>

                        <span class='pull-right'> 
                          <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                          <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_last_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                        </span>
                        <span class='pull-right'> 
                          <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                          <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_first_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                        </span> 
            <div class="clearfix"></div>
                        <textarea class="form-control"  name="text_reply_<?php echo $k; ?>" id="text_reply_<?php echo $k; ?>"><?php if(isset($full_message[$k]["template_type"]) && $full_message[$k]["template_type"] == 'text') echo $full_message[$k]['text'];?></textarea>
                      </div>        
                    </div>  
                  </div>

                  <div class="row" id="image_div_<?php echo $k; ?>" style="display: none;">             
                    <div class="col-xs-12">              
                      <div class="form-group">
                        <label><?php echo $this->lang->line("reply image"); ?></label>
                        <input type="hidden" class="form-control"  name="image_reply_field_<?php echo $k; ?>" id="image_reply_field_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type']== 'image') echo $full_message[$k]['attachment']['payload']['url'];?>">
                        <div id="image_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div>
                        <img id="image_reply_div_<?php echo $k; ?>" style="display: none;" height="200px;" width="400px;">
                      </div>       
                    </div>             
                  </div>

                  <div class="row" id="audio_div_<?php echo $k; ?>" style="display: none;">  
                    <div class="col-xs-12">             
                      <div class="form-group">
                        <label><?php echo $this->lang->line("reply audio"); ?></label>
                        <input type="hidden" class="form-control"  name="audio_reply_field_<?php echo $k; ?>" id="audio_reply_field_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type']== 'audio') echo $full_message[$k]['attachment']['payload']['url'];?>">
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
                        <label><?php echo $this->lang->line("reply video"); ?></label>
                        <input type="hidden" class="form-control"  name="video_reply_field_<?php echo $k; ?>" id="video_reply_field_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'video') echo $full_message[$k]['attachment']['payload']['url'];?>">
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
                        <label><?php echo $this->lang->line("reply file"); ?></label>
                        <input type="hidden" class="form-control"  name="file_reply_field_<?php echo $k; ?>" id="file_reply_field_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'file') echo $full_message[$k]['attachment']['payload']['url'];?>">
                        <div id="file_reply_<?php echo $k; ?>"><?php echo $this->lang->line("upload") ?></div> 
                      </div>           
                    </div>
                  </div>

                  <div class="row" id="quick_reply_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                    <div class="col-xs-12">  

                      <div class="form-group">
                        <label><?php echo $this->lang->line("reply message"); ?></label>

                        <span class='pull-right'> 
                          <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                          <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_last_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                        </span>
                        <span class='pull-right'> 
                          <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                          <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_first_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                        </span> 
            <div class="clearfix"></div>
                        <textarea class="form-control" name="quick_reply_text_<?php echo $k; ?>" id="quick_reply_text_<?php echo $k; ?>"><?php if(isset($full_message[$k]["template_type"]) && $full_message[$k]["template_type"] == 'quick reply') echo $full_message[$k]['text'];?></textarea>
                      </div> 

                      <?php $quickreply_add_button_display = 0; for ($i=1; $i <=11 ; $i++) : ?>
                      <div class="row" id="quick_reply_row_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['quick_replies'][$i-1])) echo 'style="display: none;border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"'; else {$quickreply_add_button_display++;echo 'style="border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"';} ?> >
                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button text"); ?></label>
                            <input type="text" class="form-control"  name="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>" id="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['quick_replies'][$i-1]['title'])) echo $full_message[$k]['quick_replies'][$i-1]['title']; ?>" <?php if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && ($full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_phone_number' || $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_email')) echo 'readonly'; ?>>
                          </div>
                        </div>

                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button type"); ?></label>
                            <select class="form-control quick_reply_button_type_class" id="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                              <option value=""><?php echo $this->lang->line('type'); ?></option>
                              <option value="post_back" <?php if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'text') echo 'selected'; ?> ><?php echo $this->lang->line("Post Back"); ?></option>
                              <option value="phone_number" <?php if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_phone_number') echo 'selected'; ?> ><?php echo $this->lang->line("user phone number"); ?></option>
                              <option value="user_email" <?php if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'user_email') echo 'selected'; ?> ><?php echo $this->lang->line("user email address"); ?></option>
                              <option value="location" <?php if(isset($full_message[$k]['quick_replies'][$i-1]['content_type']) && $full_message[$k]['quick_replies'][$i-1]['content_type'] == 'location') echo 'selected'; ?> ><?php echo $this->lang->line("user's location"); ?></option>
                            </select>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3">
                          <div class="form-group" id="quick_reply_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['quick_replies'][$i-1]['content_type']) || $full_message[$k]['quick_replies'][$i-1]['content_type'] != 'text') echo 'style="display: none;"'; ?>>
                            <label><?php echo $this->lang->line("PostBack id"); ?></label>
                            <?php $pname="quick_reply_post_id_".$i."_".$k; ?>
                            <?php $pdefault=(isset($full_message[$k]['quick_replies'][$i-1]['payload'])) ? $full_message[$k]['quick_replies'][$i-1]['payload']:"";?>
                            <?php echo form_dropdown($pname, $poption,$pdefault,'class="form-control push_postback" id="'.$pname.'"'); ?>
                            <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                            <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>                            
                            <!-- <input type="text" class="form-control"  name="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['quick_replies'][$i-1]['payload'])) echo $full_message[$k]['quick_replies'][$i-1]['payload']; ?>"> -->
                          </div>
                        </div>

                        <?php if($i != 1) : ?>
                          <div class="hidden-xs col-sm-2 col-md-1" <?php if(isset($full_message[$k]['quick_replies'])) if(count($full_message[$k]['quick_replies']) != $i) echo 'style="display: none;"'; ?> >
                            <br/>
                            <i class="fa fa-2x fa-times-circle red item_remove" row_id="quick_reply_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="quick_reply_button_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="quick_reply_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="quick_reply_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="" third_callus="" counter_variable="quick_reply_button_counter_<?php echo $k; ?>" add_more_button_id="quick_reply_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                          </div>
                        <?php endif; ?>


                      </div>
                      <?php endfor; ?>


                    </div> 
                  </div>

                  <div class="row" id="text_with_buttons_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                    <div class="col-xs-12"> 

                      <div class="form-group">
                        <label><?php echo $this->lang->line("reply message"); ?></label>

                        <span class='pull-right'> 
                          <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user last name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_LAST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                          <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_last_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("last name") ?></a>
                        </span>
                        <span class='pull-right'> 
                          <a href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("include lead user first name") ?>" data-content="<?php echo $this->lang->line("You can include #LEAD_USER_FIRST_NAME# variable inside your message. The variable will be replaced by real names when we will send it.") ?>"><i class='fa fa-info-circle'></i> </a> 
                          <a title="<?php echo $this->lang->line("include lead user name") ?>" class='btn btn-default btn-sm lead_first_name'><i class='fa fa-user'></i> <?php echo $this->lang->line("first name") ?></a>
                        </span> 
            <div class="clearfix"></div>
                        <textarea class="form-control"  name="text_with_buttons_input_<?php echo $k; ?>" id="text_with_buttons_input_<?php echo $k; ?>"><?php if(isset($full_message[$k]["template_type"]) && $full_message[$k]["template_type"] == 'text with buttons') echo $full_message[$k]['attachment']['payload']['text']; ?></textarea>
                      </div> 

                      <?php $textwithbutton_add_button_display = 0; for ($i=1; $i <=3 ; $i++) : ?>
                      <div class="row" id="text_with_buttons_row_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['buttons'][$i-1])) echo 'style="display: none;border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"'; else {$textwithbutton_add_button_display++; echo 'style="border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"';} ?> >
                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button text"); ?></label>
                            <input type="text" class="form-control"  name="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['title'])) echo $full_message[$k]['attachment']['payload']['buttons'][$i-1]['title']; ?>" >
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button type"); ?></label>
                            <select class="form-control text_with_button_type_class" id="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                              <option value=""><?php echo $this->lang->line('type'); ?></option>
                              <option value="post_back" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback') echo 'selected'; ?> ><?php echo $this->lang->line("Post Back"); ?></option>
                              <option value="web_url" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'web_url') echo 'selected'; ?> ><?php echo $this->lang->line("Web URL"); ?></option>
                              <option value="phone_number" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number') echo 'selected'; ?> ><?php echo $this->lang->line("call us"); ?></option>

                              <?php if($has_broadcaster_addon == 1) : ?>
                              <option value="post_back" id="unsubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("unsubscribe"); ?></option>
                              <option value="post_back" id="resubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("re-subscribe"); ?></option>
                              <?php endif; ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3">
                          <div class="form-group" id="text_with_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) || $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] != 'postback' || $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER' || $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'style="display: none;"'; ?> >
                            <label><?php echo $this->lang->line("PostBack id"); ?></label>
                            <?php $pname="text_with_button_post_id_".$i."_".$k; ?>
                            <?php 
                            $pdefault=(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']:"";
                            if($pdefault == 'UNSUBSCRIBE_QUICK_BOXER')
                              $poption['UNSUBSCRIBE_QUICK_BOXER']=$this->lang->line('unsubscribe');
                            if($pdefault == 'RESUBSCRIBE_QUICK_BOXER')
                              $poption['RESUBSCRIBE_QUICK_BOXER']=$this->lang->line('re-subscribe');
                            ?>
                            <?php echo form_dropdown($pname, $poption,$pdefault,'class="form-control push_postback" id="'.$pname.'"'); ?>
                            <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                            <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                            
                          </div>
                          <div class="form-group" id="text_with_button_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'])) echo 'style="display: none;"'; ?>>
                            <label><?php echo $this->lang->line("web url"); ?></label>
                            <input type="text" class="form-control"  name="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['url'])) echo $full_message[$k]['attachment']['payload']['buttons'][$i-1]['url']; ?>" >
                          </div>

                          <div class="form-group" id="text_with_button_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) || $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] != 'phone_number') echo 'style="display: none;"'; ?>>
                            <label><?php echo $this->lang->line("phone number"); ?></label>
                            <input type="text" class="form-control"  name="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][$i-1]['type'] == 'phone_number' ) echo $full_message[$k]['attachment']['payload']['buttons'][$i-1]['payload']; ?>" >
                          </div>

                        </div>

                        <?php if($i != 1) : ?>
                          <div class="hidden-xs col-sm-2 col-md-1" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'])) if(count($full_message[$k]['attachment']['payload']['buttons']) != $i) echo 'style="display: none;"'; ?>>
                            <br/>
                            <i class="fa fa-2x fa-times-circle red item_remove" row_id="text_with_buttons_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="text_with_buttons_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="text_with_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="text_with_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="text_with_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="text_with_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="text_with_button_counter_<?php echo $k; ?>" add_more_button_id="text_with_button_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                          </div>
                        <?php endif; ?>


                      </div>
                      <?php endfor; ?>


                    </div> 
                  </div>

                  <div class="row" id="generic_template_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">   
                    <div class="col-xs-12"> 

                      <div class="row">
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("reply image"); ?></label>
                            <input type="hidden" class="form-control"  name="generic_template_image_<?php echo $k; ?>" id="generic_template_image_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'generic template' && isset($full_message[$k]['attachment']['payload']['elements'][0]['image_url'])) echo $full_message[$k]['attachment']['payload']['elements'][0]['image_url'];?>" />
                            <div id="generic_image_<?php echo $k; ?>"></div>
                          </div>                         
                        </div>
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("image click destination link"); ?></label>
                            <input type="text" class="form-control"  name="generic_template_image_destination_link_<?php echo $k; ?>" id="generic_template_image_destination_link_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'generic template' && isset($full_message[$k]['attachment']['payload']['elements'][0]['default_action']['url'])) echo $full_message[$k]['attachment']['payload']['elements'][0]['default_action']['url'];?>"/>
                          </div> 
                        </div>                      
                      </div>

                      <div class="row">
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("title"); ?></label>
                            <input type="text" class="form-control"  name="generic_template_title_<?php echo $k; ?>" id="generic_template_title_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'generic template' && isset($full_message[$k]['attachment']['payload']['elements'][0]['title'])) echo $full_message[$k]['attachment']['payload']['elements'][0]['title'];?>"/>
                          </div>
                        </div>  
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("sub-title"); ?></label>
                            <input type="text" class="form-control"  name="generic_template_subtitle_<?php echo $k; ?>" id="generic_template_subtitle_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'generic template' && isset($full_message[$k]['attachment']['payload']['elements'][0]['subtitle'])) echo $full_message[$k]['attachment']['payload']['elements'][0]['subtitle'];?>" />
                          </div>
                        </div>  
                      </div>

                      <?php $generic_add_button_display = 0; for ($i=1; $i <=3 ; $i++) : ?>
                      <div class="row" id="generic_template_row_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1])) echo 'style="display: none;border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"'; else {$generic_add_button_display++;echo 'style="border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"';} ?> >
                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button text"); ?></label>
                            <input type="text" class="form-control"  name="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title'])) echo $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['title']; ?>">
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button type"); ?></label>
                            <select class="form-control generic_template_button_type_class" id="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>" name="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>">
                              <option value=""><?php echo $this->lang->line('type'); ?></option>
                              <option value="post_back" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'postback') echo 'selected'; ?> ><?php echo $this->lang->line("Post Back"); ?></option>
                              <option value="web_url" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'web_url') echo 'selected'; ?> ><?php echo $this->lang->line("Web URL"); ?></option>
                              <option value="phone_number" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'phone_number') echo 'selected'; ?> ><?php echo $this->lang->line("call us"); ?></option>

                              <?php if($has_broadcaster_addon == 1) : ?>
                              <option value="post_back" id="unsubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("unsubscribe"); ?></option>
                              <option value="post_back" id="resubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("re-subscribe"); ?></option>
                              <?php endif; ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3">
                          <div class="form-group" id="generic_template_button_postid_div_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']) || $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] != 'postback' || $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER' || $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'style="display: none;"'; ?>>
                            <label><?php echo $this->lang->line("PostBack id"); ?></label>
                            <?php $pname="generic_template_button_post_id_".$i."_".$k; ?>
                            <?php 
                            $pdefault=(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload'] : "";
                            if($pdefault == 'UNSUBSCRIBE_QUICK_BOXER')
                              $poption['UNSUBSCRIBE_QUICK_BOXER']=$this->lang->line('unsubscribe');
                            if($pdefault == 'RESUBSCRIBE_QUICK_BOXER')
                              $poption['RESUBSCRIBE_QUICK_BOXER']=$this->lang->line('re-subscribe');
                            ?>
                            <?php echo form_dropdown($pname, $poption,$pdefault,'class="form-control push_postback" id="'.$pname.'"'); ?>                        
                            <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                            <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                            <!-- <input type="text" class="form-control"  name="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'postback') echo $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']; ?>" > -->
                          </div>
                          <div class="form-group" id="generic_template_button_web_url_div_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'])) echo 'style="display: none;"'; ?>>
                            <label><?php echo $this->lang->line("web url"); ?></label>
                            <input type="text" class="form-control"  name="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url'])) echo $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['url']; ?>" >
                          </div>
                          <div class="form-group" id="generic_template_button_call_us_div_<?php echo $i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']) || $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] != 'phone_number') echo 'style="display: none;"'; ?>>
                            <label><?php echo $this->lang->line("phone number"); ?></label>
                            <input type="text" class="form-control"  name="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" id="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['type'] == 'phone_number') echo $full_message[$k]['attachment']['payload']['elements'][0]['buttons'][$i-1]['payload']; ?>" >
                          </div>
                        </div>

                        <?php if($i != 1) : ?>
                          <div class="hidden-xs col-sm-2 col-md-1" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][0]['buttons'])) if(count($full_message[$k]['attachment']['payload']['elements'][0]['buttons']) != $i) echo 'style="display: none;"'; ?>>
                            <br/>
                            <i class="fa fa-2x fa-times-circle red item_remove" row_id="generic_template_row_<?php echo $i; ?>_<?php echo $k; ?>" first_column_id="generic_template_button_text_<?php echo $i; ?>_<?php echo $k; ?>" second_column_id="generic_template_button_type_<?php echo $i; ?>_<?php echo $k; ?>" third_postback="generic_template_button_post_id_<?php echo $i; ?>_<?php echo $k; ?>" third_weburl="generic_template_button_web_url_<?php echo $i; ?>_<?php echo $k; ?>" third_callus="generic_template_button_call_us_<?php echo $i; ?>_<?php echo $k; ?>" counter_variable="generic_with_button_counter_<?php echo $k; ?>" add_more_button_id="generic_template_add_button_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                          </div>
                        <?php endif; ?>


                      </div>
                      <?php endfor; ?>


                    </div>
                  </div>

                  <div class="row" id="carousel_div_<?php echo $k; ?>" style="display: none; margin-bottom: 10px;">  
                    <?php for ($j=1; $j <=5 ; $j++) : ?>
                    <div class="col-xs-12" id="carousel_div_<?php echo $j; ?>_<?php echo $k; ?>" style="<?php if(!isset($full_message[$k]['attachment']['payload']['elements'][$j-1])) echo 'display: none;'; ?> padding-top: 20px;"> 
                      <div style="border: 1px dashed #ccc; background:#fff;padding:10px 15px;">
                      <div class="row">
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("reply image"); ?></label>
                            <input type="hidden" class="form-control"  name="carousel_image_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_image_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'carousel' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['image_url'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['image_url'];?>"/>
                            <div id="generic_imageupload_<?php echo $j; ?>_<?php echo $k; ?>"></div>
                          </div>                         
                        </div>
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("image click destination link"); ?></label>
                            <input type="text" class="form-control"  name="carousel_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'carousel' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'];?>"/>
                          </div> 
                        </div>                      
                      </div>

                      <div class="row">
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("title"); ?></label>
                            <input type="text" class="form-control"  name="carousel_title_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_title_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'carousel' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['title'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['title'];?>" />
                          </div>
                        </div>  
                        <div class="col-xs-12 col-md-6">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("sub-title"); ?></label>
                            <input type="text" class="form-control"  name="carousel_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" id="carousel_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'carousel' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['subtitle'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['subtitle'];?>" />
                          </div>
                        </div>  
                      </div>

                      <?php $carousel_add_button_display = 0; for ($i=1; $i <=3 ; $i++) : ?>
                      <div class="row" id="carousel_row_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1])) echo 'style="display: none;border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"'; else {$carousel_add_button_display++; echo 'style="border:1px dashed #ccc; background: #fff;padding:10px;margin:5px 0 0 20px;"';} ?>>
                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button text"); ?></label>
                            <input type="text" class="form-control"  name="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['title']; ?>" >
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-4">
                          <div class="form-group">
                            <label><?php echo $this->lang->line("button type"); ?></label>
                            <select class="form-control carousel_button_type_class" id="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" name="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>">
                              <option value=""><?php echo $this->lang->line('type'); ?></option>
                              <option value="post_back" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback') echo 'selected'; ?> ><?php echo $this->lang->line("Post Back"); ?></option>
                              <option value="web_url" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'web_url') echo 'selected'; ?> ><?php echo $this->lang->line("Web URL"); ?></option>
                              <option value="phone_number" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number') echo 'selected'; ?> ><?php echo $this->lang->line("call us"); ?></option>

                              <?php if($has_broadcaster_addon == 1) : ?>
                              <option value="post_back" id="unsubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("unsubscribe"); ?></option>
                              <option value="post_back" id="resubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("re-subscribe"); ?></option>
                              <?php endif; ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-3">
                          <div class="form-group" id="carousel_button_postid_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) || $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] != 'postback' || $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER' || $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'style="display: none;"'; ?> >
                            <label><?php echo $this->lang->line("PostBack id"); ?></label>
                            <?php $pname="carousel_button_post_id_".$j."_".$i."_".$k; ?>
                            <?php 
                            $pdefault=(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']:"";
                            if($pdefault == 'UNSUBSCRIBE_QUICK_BOXER')
                              $poption['UNSUBSCRIBE_QUICK_BOXER']=$this->lang->line('unsubscribe');
                            if($pdefault == 'RESUBSCRIBE_QUICK_BOXER')
                              $poption['RESUBSCRIBE_QUICK_BOXER']=$this->lang->line('re-subscribe');
                            ?>
                            <?php echo form_dropdown($pname, $poption,$pdefault,'class="form-control push_postback" id="'.$pname.'"'); ?>                        
                            <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                            <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                            <!-- <input type="text" class="form-control"  name="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'postback') echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']; ?>"> -->
                          </div>
                          <div class="form-group" id="carousel_button_web_url_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'])) echo 'style="display: none;"'; ?>>
                            <label><?php echo $this->lang->line("web url"); ?></label>
                            <input type="text" class="form-control"  name="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['url']; ?>" >
                          </div>
                          <div class="form-group" id="carousel_button_call_us_div_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) || $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] != 'phone_number') echo 'style="display: none;"'; ?> >
                            <label><?php echo $this->lang->line("phone number"); ?></label>
                            <input type="text" class="form-control"  name="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" id="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']) && $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['type'] == 'phone_number') echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'][$i-1]['payload']; ?>">
                          </div>
                        </div>

                        <?php if($i != 1) : ?>
                          <div class="hidden-xs col-sm-2 col-md-1" <?php if(isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons'])) if(count($full_message[$k]['attachment']['payload']['elements'][$j-1]['buttons']) != $i) echo 'style="display: none;"'; ?> >
                            <br/>
                            <i class="fa fa-2x fa-times-circle red item_remove" row_id="carousel_row_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" first_column_id="carousel_button_text_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" second_column_id="carousel_button_type_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_postback="carousel_button_post_id_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_weburl="carousel_button_web_url_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" third_callus="carousel_button_call_us_<?php echo $j."_".$i; ?>_<?php echo $k; ?>" counter_variable="carousel_add_button_counter_<?php echo $j; ?>_<?php echo $k; ?>" add_more_button_id="carousel_add_button_<?php echo $j; ?>_<?php echo $k; ?>" title="<?php echo $this->lang->line('Remove this item'); ?>"></i>
                          </div>
                        <?php endif; ?>

                      </div>
                      <?php endfor; ?>

                    </div>
                    </div>
                    <?php endfor; ?>


                  </div>


                  <div class="row" id="list_div_<?php echo $k; ?>" style="display: none; padding-top: 20px;">  
                     <div class="col-xs-12">
                        <div class="row" id="list_with_buttons_row">
                           <div class="col-xs-12 col-md-4">
                              <div class="form-group">
                                 <label><?php echo $this->lang->line("bottom button text"); ?></label>
                                 <input type="text" class="form-control"  name="list_with_buttons_text_<?php echo $k; ?>" id="list_with_buttons_text_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['title'])) echo $full_message[$k]['attachment']['payload']['buttons'][0]['title']; ?>">
                              </div>
                           </div>
                           <div class="col-xs-12 col-md-4">
                              <div class="form-group">
                                 <label><?php echo $this->lang->line("bottom button type"); ?></label>
                                 <select class="form-control list_with_button_type_class" id="list_with_button_type_<?php echo $k; ?>" name="list_with_button_type_<?php echo $k; ?>">
                                    <option value=""><?php echo $this->lang->line('type'); ?></option>
                                    <option value="post_back" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['type']) && $full_message[$k]['attachment']['payload']['buttons'][0]['type'] == 'postback') echo 'selected'; ?> ><?php echo $this->lang->line("Post Back"); ?></option>
                                    <option value="web_url" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['type']) && $full_message[$k]['attachment']['payload']['buttons'][0]['type'] == 'web_url') echo 'selected'; ?> ><?php echo $this->lang->line("Web URL"); ?></option>
                                    <option value="phone_number" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['type']) && $full_message[$k]['attachment']['payload']['buttons'][0]['type'] == 'phone_number') echo 'selected'; ?> ><?php echo $this->lang->line("call us"); ?></option>
                                    <?php if($has_broadcaster_addon == 1) : ?>
                                       <option value="post_back" id="unsubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['type']) && $full_message[$k]['attachment']['payload']['buttons'][0]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['buttons'][0]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][0]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("unsubscribe"); ?></option>
                                       <option value="post_back" id="resubscribe_postback" <?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['type']) && $full_message[$k]['attachment']['payload']['buttons'][0]['type'] == 'postback' && isset($full_message[$k]['attachment']['payload']['buttons'][0]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][0]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'selected'; ?> ><?php echo $this->lang->line("re-subscribe"); ?></option>
                                    <?php endif; ?>
                                 </select>
                              </div>
                           </div>
                           <div class="col-xs-12 col-md-4">
                              <div class="form-group" id="list_with_button_postid_div_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['buttons'][0]['payload']) || $full_message[$k]['attachment']['payload']['buttons'][0]['type'] != 'postback' || $full_message[$k]['attachment']['payload']['buttons'][0]['payload'] == 'UNSUBSCRIBE_QUICK_BOXER' || $full_message[$k]['attachment']['payload']['buttons'][0]['payload'] == 'RESUBSCRIBE_QUICK_BOXER') echo 'style="display: none;"'; ?> >
                                 <label><?php echo $this->lang->line("PostBack id"); ?></label>
                                 <?php $pname="list_with_button_post_id_".$k; ?>
                                 <?php 
                                 $pdefault=(isset($full_message[$k]['attachment']['payload']['buttons'][0]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][0]['type'] == 'postback') ? $full_message[$k]['attachment']['payload']['buttons'][0]['payload']:"";
                                 if($pdefault == 'UNSUBSCRIBE_QUICK_BOXER')
                                   $poption['UNSUBSCRIBE_QUICK_BOXER']=$this->lang->line('unsubscribe');
                                 if($pdefault == 'RESUBSCRIBE_QUICK_BOXER')
                                   $poption['RESUBSCRIBE_QUICK_BOXER']=$this->lang->line('re-subscribe');
                                 ?>
                                 <?php echo form_dropdown($pname, $poption,$pdefault,'class="form-control push_postback" id="'.$pname.'"'); ?>
                                 <a href="" class="add_template pull-left"><i class="fa fa-plus-circle"></i>     <?php echo $this->lang->line("Add");?></a>
                                 <a href="" class="ref_template pull-right"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Refresh");?></a>
                              </div>
                              <div class="form-group" id="list_with_button_web_url_div_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['buttons'][0]['url'])) echo 'style="display: none;"'; ?> >
                                 <label><?php echo $this->lang->line("web url"); ?></label>
                                 <input type="text" class="form-control"  name="list_with_button_web_url_<?php echo $k; ?>" id="list_with_button_web_url_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['url'])) echo $full_message[$k]['attachment']['payload']['buttons'][0]['url']; ?>" >
                              </div>
                              <div class="form-group" id="list_with_button_call_us_div_<?php echo $k; ?>" <?php if(!isset($full_message[$k]['attachment']['payload']['buttons'][0]['payload']) || $full_message[$k]['attachment']['payload']['buttons'][0]['type'] != 'phone_number') echo 'style="display: none;"'; ?> >
                                 <label><?php echo $this->lang->line("phone number"); ?></label>
                                 <input type="text" class="form-control"  name="list_with_button_call_us_<?php echo $k; ?>" id="list_with_button_call_us_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['attachment']['payload']['buttons'][0]['payload']) && $full_message[$k]['attachment']['payload']['buttons'][0]['type'] == 'phone_number') echo $full_message[$k]['attachment']['payload']['buttons'][0]['payload']; ?>" >
                              </div>
                           </div>
                        </div>
                     </div>

                     <?php for ($j=1; $j <=4 ; $j++) : ?>
                        <div class="col-xs-12" id="list_div_<?php echo $j; ?>_<?php echo $k; ?>" style="<?php if(!isset($full_message[$k]['attachment']['payload']['elements'][$j-1])) echo 'display: none;'; ?> padding-top: 20px;"> 
                           <div style="border: 1px dashed #ccc; background:#fff;padding:10px 15px;">
                              <div class="row">
                                 <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                       <label><?php echo $this->lang->line("reply image"); ?></label>
                                       <input type="hidden" class="form-control"  name="list_image_<?php echo $j; ?>_<?php echo $k; ?>" id="list_image_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'list' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['image_url'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['image_url'];?>" />
                                       <div id="list_imageupload_<?php echo $j; ?>_<?php echo $k; ?>"></div>
                                    </div>                         
                                 </div>
                                 <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                       <label><?php echo $this->lang->line("image click destination link"); ?></label>
                                       <input type="text" class="form-control"  name="list_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" id="list_image_destination_link_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'list' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['default_action']['url'];?>" />
                                    </div> 
                                 </div>                      
                              </div>

                              <div class="row">
                                 <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                       <label><?php echo $this->lang->line("title"); ?></label>
                                       <input type="text" class="form-control"  name="list_title_<?php echo $j; ?>_<?php echo $k; ?>" id="list_title_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'list' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['title'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['title'];?>" />
                                    </div>
                                 </div>  
                                 <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                       <label><?php echo $this->lang->line("sub-title"); ?></label>
                                       <input type="text" class="form-control"  name="list_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" id="list_subtitle_<?php echo $j; ?>_<?php echo $k; ?>" value="<?php if(isset($full_message[$k]['template_type']) && $full_message[$k]['template_type'] == 'list' && isset($full_message[$k]['attachment']['payload']['elements'][$j-1]['subtitle'])) echo $full_message[$k]['attachment']['payload']['elements'][$j-1]['subtitle'];?>" />
                                    </div>
                                 </div>  
                              </div>
                           </div>
                        </div>
                     <?php endfor; ?>

                    

                  </div>


                </div>
              </div>
          <?php } ?>
               
                <div class="row">
                  <div class="col-xs-6">
                    <br/><a href="<?php echo $redirect_url ?>" class="btn btn-lg btn-warning"><i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('back'); ?></a>
                  </div>
                </div>
              </form>
            </div>

            <div class="hidden-xs hidden-sm col-md-3 img_holder">
              <div id="text_preview_div" style="">
                <!-- <center><h4><b>Text</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/text.png')) echo site_url()."assets/images/preview/text.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/text.png"; ?>" class="img-rounded" alt="Text Preview"></center>
              </div>

              <div id="image_preview_div" style="display: none;">
                <!-- <center><h4><b>Image</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/image.png')) echo site_url()."assets/images/preview/image.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/image.png"; ?>" class="img-rounded" alt="Image Preview"></center>
              </div>

              <div id="audio_preview_div" style="display: none;">
                <!-- <center><h4><b>Audio</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/mp3.png')) echo site_url()."assets/images/preview/mp3.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/mp3.png"; ?>" class="img-rounded" alt="Audio Preview"></center>
              </div>

              <div id="video_preview_div" style="display: none;">
                <!-- <center><h4><b>Video</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/video.png')) echo site_url()."assets/images/preview/video.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/video.png"; ?>" class="img-rounded" alt="Video Preview"></center>
              </div>

              <div id="file_preview_div" style="display: none;">
                <!-- <center><h4><b>File</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/file.png')) echo site_url()."assets/images/preview/file.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/file.png"; ?>" class="img-rounded" alt="File Preview"></center>
              </div>

              <div id="quick_reply_preview_div" style="display: none;">
                <!-- <center><h4><b>Quick Reply</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/quick_reply.png')) echo site_url()."assets/images/preview/quick_reply.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/quick_reply.png"; ?>" class="img-rounded" alt="Quick Reply Preview"></center>
              </div>

              <div id="text_with_buttons_preview_div" style="display: none;">
                <!-- <center><h4><b>Text with buttons</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/button.png')) echo site_url()."assets/images/preview/button.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/button.png"; ?>" class="img-rounded" alt="Text With Buttons Preview"></center>
              </div>

              <div id="generic_template_preview_div" style="display: none;">
                <!-- <center><h4><b>Generic Template</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/generic.png')) echo site_url()."assets/images/preview/generic.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/generic.png"; ?>" class="img-rounded" alt="Generic Template Preview"></center>
              </div>

              <div id="carousel_preview_div" style="display: none;">
                <!-- <center><h4><b>Carousel Template</b></h4></center> -->
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/carousel.png')) echo site_url()."assets/images/preview/carousel.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/carousel.png"; ?>" class="img-rounded" alt="Carousel Template Preview"></center>
              </div>

              <div id="list_preview_div" style="display: none;">
                <center><img src="<?php if(file_exists(FCPATH.'assets/images/preview/list.png')) echo site_url()."assets/images/preview/list.png"; else echo "https://mysitespy.net/2waychat_demo/msgbot_demo/preview/list.png"; ?>" class="img-rounded" alt="List Template Preview"></center>
              </div>

            </div>
           
          </div>
          <br>
          <div id="submit_status" class="text-center"></div>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
  </div>

  <br>
  <?php if($this->session->flashdata('bot_success')===1) { ?>
  <div class="alert alert-success text-center" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("Bot settings has been updated successfully.");?></div>
  <?php } ?>
</div>

<?php 
$areyousure=$this->lang->line("are you sure"); 
$somethingwentwrong = $this->lang->line("something went wrong.");  
$doyoureallywanttodeletethisbot = $this->lang->line("do you really want to delete this bot?");
?>

<script type="text/javascript">

$(document).ready(function(){
  $j("#text_reply_1, #text_reply_2, #text_reply_3, #quick_reply_text_1, #quick_reply_text_2, #quick_reply_text_3, #text_with_buttons_input_1, #text_with_buttons_input_2, #text_with_buttons_input_3").emojioneArea({
      autocomplete: false,
      pickerPosition: "bottom"
    });
  setTimeout(function(){$("#loader").hide();}, 3000);

  // getting postback list and making iframe
  var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?><?php echo $page_info['id'];?>";
  $('#add_template_modal').on('shown.bs.modal',function(){ 
    $(this).find('iframe').attr('src',iframe_link);
  });
    

});



  $(document).ready(function(e){
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

  var multiple_template_add_button_counter = <?php echo $active_reply_count; ?>;
  $(document.body).on('click','#multiple_template_add_button',function(e){
    e.preventDefault();
    multiple_template_add_button_counter++
    $("#multiple_template_div_"+multiple_template_add_button_counter).show();
    $("#multiple_template_div_"+multiple_template_add_button_counter).find(".remove_reply").show();
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
  
</script>


<script type="text/javascript">

  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";
  var areyousure="<?php echo $areyousure;?>";
  var js_array = [<?php echo '"'.implode('","', $total_postback_id_array ).'"' ?>];

  $("input").attr('disabled','disabled');
  $("textarea").attr('disabled','disabled');
  $("select").attr('disabled','disabled');
  $("button").attr('disabled','disabled');
  $(".lead_first_name").attr('disabled','disabled');
  $(".lead_last_name").attr('disabled','disabled');
  $(".item_remove").hide();
  $(".remove_reply").hide();

  $j("#keywordtype_postback_id").multipleSelect({
      filter: true,
      multiple: true
  });

  //start rakib work 
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

  // end rakib work


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


  <?php for($template_type=1;$template_type<=3;$template_type++){ ?>

      var template_type_order="#template_type_<?php echo $template_type ?>";

      $j("document").ready(function(){
        var selected_template = $("#template_type_<?php echo $template_type ?>").val();
        selected_template = selected_template.replace(/ /gi, "_");

        var template_type_array = ['text','image','audio','video','file','quick_reply','text_with_buttons','generic_template','carousel','list'];
        template_type_array.forEach(templates_hide_show_function);
        function templates_hide_show_function(item, index)
        {
          var template_type_preview_div_name = "#"+item+"_preview_div";

          var template_type_div_name = "#"+item+"_div_<?php echo $template_type; ?>";
          if(selected_template == item){
            $(template_type_div_name).show();
            $(template_type_preview_div_name).show();
          }
          else{
            $(template_type_div_name).hide();
            $(template_type_preview_div_name).hide();
          }

          if(selected_template == 'quick_reply')
          {
            $("#quick_reply_row_1_<?php echo $template_type; ?>").show();
          }

          if(selected_template == 'text_with_buttons')
          {
            $("#text_with_buttons_row_1_<?php echo $template_type; ?>").show();
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


      $j(document.body).on('change',"#template_type_<?php echo $template_type ?>",function(){
      
        var selected_template_on_change = $("#template_type_<?php echo $template_type ?>").val();
        selected_template_on_change = selected_template_on_change.replace(/ /gi, "_");

        var template_type_array = ['text','image','audio','video','file','quick_reply','text_with_buttons','generic_template','carousel','list'];
        template_type_array.forEach(templates_hide_show_function);
        function templates_hide_show_function(item, index)
        {
          var template_type_preview_div_name = "#"+item+"_preview_div";

          var template_type_div_name = "#"+item+"_div_<?php echo $template_type; ?>";
          if(selected_template_on_change == item){
            $(template_type_div_name).show();
            $(template_type_preview_div_name).show();
          }
          else{
            $(template_type_div_name).hide();
            $(template_type_preview_div_name).hide();
          }

          if(selected_template_on_change == 'quick_reply')
          {
            $("#quick_reply_row_1_<?php echo $template_type; ?>").show();
          }

          if(selected_template_on_change == 'text_with_buttons')
          {
            $("#text_with_buttons_row_1_<?php echo $template_type; ?>").show();
          }

          if(selected_template_on_change == 'generic_template')
          {
            $("#generic_template_row_1_<?php echo $template_type; ?>").show();
          }

          if(selected_template_on_change == 'carousel')
          {
            $("#carousel_div_1_<?php echo $template_type; ?>").show();
            $("#carousel_row_1_1_<?php echo $template_type; ?>").show();
          }

          if(selected_template_on_change == 'list')
          {
            $("#list_div_1_<?php echo $template_type; ?>").show();
            $("#list_div_2_<?php echo $template_type; ?>").show();
          }

        }
      });



      var quick_reply_button_counter_<?php echo $template_type; ?> = "<?php if (isset($full_message[$template_type]['quick_replies'])) echo count($full_message[$template_type]['quick_replies']); else echo 1; ?>";

    
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

        }

        if(quick_reply_button_type == '')
        {
          $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Type')?>");
          $("#error_modal").modal();
          return;
        }

        

        var quick_reply_button_text_check = $(quick_reply_button_text).val();
        if(quick_reply_button_type == 'post_back')
        { 
          if(quick_reply_button_text_check == ''){
            $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Your Button Text')?>");
            $("#error_modal").modal();
            return;
          }
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



     var text_with_button_counter_<?php echo $template_type; ?> = "<?php if (isset($full_message[$template_type]['attachment']['payload']['buttons'])) echo count($full_message[$template_type]['attachment']['payload']['buttons']); else echo 1; ?>";
  
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


     var  generic_with_button_counter_<?php echo $template_type; ?> = "<?php if(isset($full_message[$template_type]['attachment']['payload']['elements'][0]['buttons'])) echo count($full_message[$template_type]['attachment']['payload']['elements'][0]['buttons']); else echo 1; ?>";
  
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


    <?php for($j=1; $j<=5; $j++) : ?>
      
       var carousel_add_button_counter_<?php echo $j; ?>_<?php echo $template_type; ?> = "<?php if(isset($full_message[$template_type]['attachment']['payload']['elements'][$j-1]['buttons'])) echo count($full_message[$template_type]['attachment']['payload']['elements'][$j-1]['buttons']); else echo 1; ?>";
    
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
    
    
    var carousel_template_counter_<?php echo $template_type; ?> = "<?php if(isset($full_message[$template_type]['attachment']['payload']['elements'])) echo count($full_message[$template_type]['attachment']['payload']['elements']); else echo 1; ?>";
    
    $j(document.body).on('click','#carousel_template_add_button_<?php echo $template_type; ?>',function(e){
         e.preventDefault();

         var carousel_image = "#carousel_image_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
         var carousel_image_check = $(carousel_image).val();
         if(carousel_image_check == ''){
           $("#error_modal_content").html("<?php echo $this->lang->line('reply image')?>");
           $("#error_modal").modal();
           return;
         }

         var carousel_title = "#carousel_title_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
         var carousel_title_check = $(carousel_title).val();
         if(carousel_title_check == ''){
           $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide carousel title')?>");
           $("#error_modal").modal();
           return;
         }

         var carousel_image_destination_link = "#carousel_image_destination_link_"+carousel_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
         var carousel_image_destination_link_check = $(carousel_image_destination_link).val();
         if(carousel_image_destination_link_check == ''){
           $("#error_modal_content").html("<?php echo $this->lang->line('Please Provide Image Click Destination Link')?>");
           $("#error_modal").modal();
           return;        
         }

         carousel_template_counter_<?php echo $template_type; ?>++;
      
         var x = carousel_template_counter_<?php echo $template_type; ?>;
      
         $("#carousel_div_"+x+"_<?php echo $template_type; ?>").show();
         $("#carousel_row_"+x+"_1"+"_<?php echo $template_type; ?>").show();
         if( carousel_template_counter_<?php echo $template_type; ?> == 5)
           $("#carousel_template_add_button_<?php echo $template_type; ?>").hide();
    });


    var list_template_counter_<?php echo $template_type; ?> = "<?php if(isset($full_message[$template_type]['attachment']['payload']['elements'])) echo count($full_message[$template_type]['attachment']['payload']['elements']); else echo 2; ?>";
  
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
        $("#error_modal_content").html("<?php echo $this->lang->line('reply image')?>");
        $("#error_modal").modal();
        return;
      }

      var list_image = "#list_image_"+list_template_counter_<?php echo $template_type; ?>+"_"+<?php echo $template_type; ?>;
      var list_image_check = $(list_image).val();
      if(list_image_check == ''){
        $("#error_modal_content").html("<?php echo $this->lang->line('reply image')?>");
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



  $j(document).ready(function() {



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
        $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#text_with_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#text_with_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");
        if(option_id=="unsubscribe_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 

           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option").removeAttr('selected');
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" option[value=UNSUBSCRIBE_QUICK_BOXER]").attr('selected','selected');

           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
            $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option").removeAttr('selected');
           $("#text_with_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+"  option[value=RESUBSCRIBE_QUICK_BOXER]").attr('selected','selected');
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
        $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).show();
        $("#generic_template_button_web_url_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        $("#generic_template_button_call_us_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        var option_id=$(this).children(":selected").attr("id");

        if(option_id=="unsubscribe_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 

           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option").removeAttr('selected');
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" option[value=UNSUBSCRIBE_QUICK_BOXER]").attr('selected','selected');

           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+" select option").removeAttr('selected');
           $("#generic_template_button_postid_div_"+which_number_is_clicked+"_"+which_block_is_clicked+"  option[value=RESUBSCRIBE_QUICK_BOXER]").attr('selected','selected');
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
        $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).show();
        $("#carousel_button_web_url_div_"+second+"_"+first+"_"+block_template_third).hide();
        $("#carousel_button_call_us_div_"+second+"_"+first+"_"+block_template_third).hide();
        var option_id=$(this).children(":selected").attr("id");

        if(option_id=="unsubscribe_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","UNSUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('unsubscribe');?>")); 

           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option").removeAttr('selected');
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" option[value=UNSUBSCRIBE_QUICK_BOXER]").attr('selected','selected');

           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third).hide();
        }
        if(option_id=="resubscribe_postback")
        {
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select").append($("<option></option>").attr("value","RESUBSCRIBE_QUICK_BOXER").text("<?php echo $this->lang->line('re-subscribe');?>")); 
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+" select option").removeAttr('selected');
           $("#carousel_button_postid_div_"+second+"_"+first+"_"+block_template_third+"  option[value=RESUBSCRIBE_QUICK_BOXER]").attr('selected','selected');
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



    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

    $(document.body).on('click','.add_template',function(e){
        e.preventDefault();
        var current_id=$(this).prev().attr("id");
        var page_id="<?php echo $page_info['id'];?>";
        if(page_id=="")
        {
          alert("<?php echo $this->lang->line('Please select a page first')?>");
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
           alert("<?php echo $this->lang->line('Please select a page first')?>");
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
           alert("<?php echo $this->lang->line('Please select a page first')?>");
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
  


</script>


<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width: 95%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h4>
      </div>
      <div class="modal-body"> 
        <iframe width="100%" height="450px" frameborder="0" src="" ></iframe> 
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-warning"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close & Refresh List");?></button>
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
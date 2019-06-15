<style type="text/css">
  .ajax-upload-dragdrop{width:100% !important;}
  .item_remove
  {
    margin-top: 9px; 
    margin-left: -20px;
    cursor: pointer !important;
  }
  .remove_reply
  {
    margin-top: -5px;
    cursor: pointer !important;
  }
</style>
<style type="text/css">
  .button_container a{width: 98.7%;font-size:15px;}
  label{font-size:12px;}
  .ms-choice div{margin-top: 8px;}
  .ms-choice span{margin-top: 8px;margin-left: 7px;color:#000 !important;}
  .ms-drop{margin-top: 3px;border-radius: 15px !important;padding:10px;}
  .ms-search input{height: 30px !important;}
  .input-group-addon{
    border-radius: 0;
    font-weight: bold;
    color: orange;  
    /*border: 1px solid #607D8B !important;*/
    border: none;
    background: #f9f9f9;
  }
  .form-control:not([type="search"]):not([name="bot_settings_data_table_length"])
  {
    height: 45px;
    line-height: 2.5;
  }
  
  .img_holder img{
    border: 1px solid #ccc;
  }

  .margin-bottom-label{margin-bottom: 20px;display: block}
  .css-checkbox{display: none;}
  .css-label{padding:14px 20px; background: #ccc;border-radius: 15px;text-align: center;}
  .css-label:hover{background: #ddd;cursor: pointer;}
  .single-label{min-width: 99%;}
  .double-label{min-width: 49.5%;}
  .triple-label{min-width: 32%;}
  .six-label{min-width: 13%;}
  .widget-user-header{border-radius: 0;}
  .box-footer{border-radius: 0;}
  #loader,#loader2{margin-top:20px;margin-bottom: 30px;}
  /* #engagement_block{width: 98.7%;} */
  #engagement_block{border:1px solid #ccc !important;}

  .myradio {
    display: none;
  }
  .myradio + label::before {
      content: '';
      display: inline-block;
      border: 1px solid #ff8000;
      border-radius: 50%;
      margin: 0 0.5em;
  }
  .myradio:checked + label::before {
      background-color: #ff8000;
  }

  .radio1 + label::before {
      width: 0.5em;
      height: 0.5em;
  }

  .radio2 + label::before {
      width: 0.75em;
      height: 0.75em;
  }

  .radio3 + label::before {
      width: 1em;
      height: 1em;
  }

  .radio4 + label::before {
      width: 1.5em;
      height: 1.5em;
  }

  .radio5 + label::before {
      width: 2.2em;
      height: 2.2em;
      cursor: pointer;
  }
  .well2{padding:12px;border:1px solid #0069D9; color:#0069D9;margin-bottom:25px;}
</style>

<div class="container-fluid">
  <div class="hidden" id="add_bot_settings_modal">
    <div class="modal-dialog" style="width:100%;margin:20px 0 0 0;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="padding-left: 35px;"><i class='fa fa-plus'></i> <?php echo $this->lang->line("Add Drip Message Campaign");?></h4>
        </div>
        <div class="modal-body" style="padding-left:30px;">         

          <form action="#" enctype="multipart/form-data" id="plugin_form" style="padding: 0 20px 20px 20px">
            <input type="hidden" name="day_counter" id="day_counter" value="<?php echo $default_display;?>">
            <input type="hidden" name="page_id" id="page_id" value="<?php echo $page_auto_id;?>">
            <div class='well2 text-center'><?php echo $this->lang->line('Drip message sequence must not contain any advertisement or promotional material.');?></div>
            
            <div class="row">
              <div class="form-group col-xs-12">             
                <label><?php echo $this->lang->line("Campaign Name"); ?></label>
                <input type="text" name="campaign_name" id="campaign_name" class="form-control">  
              </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-12 col-md-12" style='padding:10px 0 0 15px;'>
                  <label class="margin-bottom-label">
                    <?php echo $this->lang->line("Drip Message Type"); ?> *
                     <?php if($this->is_engagement_exist) { ?>
                      <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Drip Message Type"); ?>" data-content="<?php echo $this->lang->line('You must create one default type campaign. You can also create different campaigns for different messenger engagement tools. Subscribers from those tools will get different drip messages and others will get default message.');?>"><i class='fa fa-info-circle'></i> </a>
                     <?php } ?>
                  </label>
                  <?php 
                  echo '<input type="radio" class="css-checkbox" name="drip_type" value="default" id="drip_type1" checked> <label for="drip_type1" class="css-label six-label default-label"> <i class="checkicon fa fa-check"></i> '.$this->lang->line("Default").'</label>';
                  echo '<input type="radio" class="css-checkbox" name="drip_type" value="custom" id="drip_type2"> <label for="drip_type2" class="css-label six-label"> <i class="checkicon fa fa-check"></i> '.$this->lang->line("Custom").'</label>';
                  if($this->is_engagement_exist) 
                  {
                    $drip_types_engagement=$drip_types;
                    unset($drip_types_engagement['default']);
                    unset($drip_types_engagement['custom']);                                  
                    $i=2;  
                    foreach ($drip_types_engagement as $key => $value) 
                    {
                      $i++;
                      echo '<input type="radio" class="css-checkbox" name="drip_type" value="'.$key.'" id="drip_type'.$i.'"> <label for="drip_type'.$i.'" class="css-label six-label"> <i class="checkicon fa fa-check"></i> '.$this->lang->line($value).'</label>';
                    } 
                  }
                  ?>  
                </div>
            </div> 

            <img id="loader2" class="hidden center-block" src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>">
            <div class="box box-widget widget-user-2 hidden" id="engagement_block">
              <br>
              <div class="widget-user-header3">
                <div class="row">
                  <div class="col-xs-12">
                    <h3 class="widget-user-username text-center"><?php echo $this->lang->line("Messenger Engagement Re-targeting"); ?></h3>
                    <h5 class="widget-user-desc text-center"><a class="orange" href="https://facebook.com/<?php echo  $page_info['page_id'];?>"><?php echo  $page_info['page_name'];?></a> | <?php echo  $page_info['account_name'];?></h5>
                  </div>
                </div>
              </div>
              <br>
              <div class="box-footer" id="put_engegement_content">            
              </div>
            </div>


            <br><br>

            <div class="row">
              <?php  
                $tooplip1='<a data-html="true" href="#" data-placement="top"  data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Broadcast in between time").'" data-content="'.$this->lang->line('Message will be sent only in this time interval').'. '.$this->lang->line('The time interval must be minimum one hour. If your subscriber list for this campaign is large, you should select larger time interval in order to send all message properly.').'">&nbsp;&nbsp;<i class="fa fa-info-circle"></i> </a>';
               ?>
              <div class="col-xs-12 col-sm-6 col-md-3">
                  <div class="form-group">
                  <label><?php echo $this->lang->line("Broadcast in between time")." ".$tooplip1;?></label>
                  <input type="text" class="form-control timepicker" value="00:00" id="between_start" name="between_start">
                  </div>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-3">
                  <div class="form-group">
                      <label class="hidden-xs hidden-sm" style="position: relative;right: 22px;top: 32px;">-</label>
                      <input type="text" class="form-control timepicker" value="23:59" id="between_end" name="between_end">
                  </div>
              </div>
              <div class="col-xs-12 col-md-3">
                  <div class="form-group">
                  <label><?php echo $this->lang->line("Time Zone");?></label>
                  <?php echo form_dropdown('timezone', $timezones,$this->config->item('time_zone'),"class='form-control' id='timezone'");?>
                  </div>
              </div>
            </div>
            
            <?php 
            for($i=1; $i <=$how_many_days ; $i++) 
            { 
              $hideshowclass='';
              if($i>$default_display) $hideshowclass='hidden';
              ?>
              <div class="row <?php echo $hideshowclass;?>" id="day_container<?php echo $i;?>">
                <div class="form-group col-xs-6 col-md-3">
                  <?php echo '<input type="radio" class="css-checkbox" value="'.$i.'" id="day'.$i.'"> <label for="day'.$i.'" class="css-label default-label single-label"> <i class="checkicon fa fa-check"></i> '.$this->lang->line("day").'-'.$i.'</label>';?>  
                </div>
                <div class="form-group col-xs-6 col-md-6">              
                  <div id='push_postback<?php echo $i;?>'>
                      <?php 
                        $template_id="template_id".$i;
                        // $template_list['']=$this->lang->line("Message Template").' '.$this->lang->line("Day").'-'.$i;
                        $template_list['']="--- ".$this->lang->line("No")." ".$this->lang->line("Message")." ---";
                        echo form_dropdown($template_id,$template_list, '','class="form-control template_id" id="'.$template_id.'"'); 
                      ?>
                  </div>
                </div>
                <div class="form-group col-xs-6 col-md-2">              
                  <a href="" title="<?php echo $this->lang->line("Refresh");?>" data-id="<?php echo $i;?>" class="ref_template btn btn-lg"><i class="fa blue fa-refresh"></i></a>
                </div>
              </div>               
            <?php
            }
            ?>

            <div class="row button_container">
              <div class="form-group col-xs-6 col-md-3">
                <a id="add_more_day" href="" class="btn btn-outline-primary"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add More Day');?></a>
              </div>
              <div class="form-group col-xs-6 col-md-3">
                <a id="remove_last_day" href="" class="btn btn-outline-danger"><i class="fa fa-trash"></i> <?php echo $this->lang->line('Remove Last Day');?></a>
              </div>
              <div class="form-group col-xs-6 col-md-3">
                <a target="_BLANK" class="btn btn-default border_gray add_template"  href="<?php echo base_url('messenger_bot/create_new_template/1/'.$page_auto_id);?>"><i class="fa fa-th"></i> <?php echo $this->lang->line('New Message Template');?></a>
              </div>
            </div>

            <img id="loader" class="hidden center-block" src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>">
            <div id="submit_status" class="text-center"></div>
            
          </form>              

        </div>
        <div class="modal-footer" style="padding-left: 4.5%;">
           <a id="submit_btn" href="" class="btn btn-primary btn-lg pull-left"><i class="fa fa-send"></i> <?php echo $this->lang->line('Create Campaign');?></a>             
           <div class="clearfix"></div>
           <br><div class="alert alert-danger hidden text-center" id="error_message"></div>
        </div>
      </div>
    </div>
  </div>

  <br>
  <?php if($this->session->flashdata('bot_success')===1) { ?>
  <div class="alert alert-success text-center bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("new drip message setting has been stored successfully.");?></div>
  <?php } ?>
  <?php if($this->session->flashdata('bot_update_success')===1) { ?>
  <div class="alert alert-success text-center bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("drip message setting has been updated successfully.");?></div>
  <?php } ?>

  <div class="box box-widget widget-user-2" >
    <div class="widget-user-header">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-9">
          <div class="widget-user-image">
            <img class="img-circle" src="<?php echo $page_info['page_profile'];?>">
          </div>
          <h3 class="widget-user-username"><?php echo $this->lang->line("Drip Messaging Campaigns"); ?></h3>
           <h5 class="widget-user-desc"><a href="https://facebook.com/<?php echo  $page_info['page_id'];?>"><?php echo  $page_info['page_name'];?></a> | <?php echo  $page_info['account_name'];?></h5>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
          <a class="btn btn-outline-primary pull-right" id="add_bot_settings" style="margin-top:15px;"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Drip Message Campaign');?></a>
        </div>
      </div>
    </div>
    <div class="box-footer">
      <?php 
      if(empty($bot_settings)) echo "<h4 class='text-center'>".$this->lang->line('no settings found.')."</h4>";
      else
      {
          echo "<div class='table-responsive'><table class='table table-hover table-bordered table-striped' id='bot_settings_data_table'>";
            echo "<thead>";
              echo "<tr>";
                echo "<th class='text-center'>".$this->lang->line("Serial")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Campaign Name")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Created at")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Last Sent at")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Drip Type")."</th>";
                if($this->is_engagement_exist)             
                {
                  echo "<th class='text-center'>".$this->lang->line("Engagement Campaign")."</th>";
                }
                echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
              echo "</tr>";
            echo "</thead>";

            echo "<tbody>";
              $i=0;
              foreach ($bot_settings as $key => $value) 
              {
                $i++;
                if($value['last_sent_at']!="0000-00-00 00:00:00") $reply_at=date('d M y - H:i:s',strtotime($value['last_sent_at']));
                else $reply_at =  "<i class='fa fa-remove'></i>";
                
                $drip_type=$this->lang->line($drip_types[$value['drip_type']]);
                $details='x';
                if($value['drip_type']!='default' && $value['drip_type']!='custom')
                {
                  $href='';
                  if($value['drip_type']=='messenger_bot_engagement_checkbox')
                  $href='messenger_engagement/checkbox_plugin_edit/';
                  else if($value['drip_type']=='messenger_bot_engagement_send_to_msg')
                  $href='messenger_engagement/send_to_messenger_edit/';
                  else if($value['drip_type']=='messenger_bot_engagement_mme')
                  $href='messenger_engagement/mme_link_edit/';
                  else if($value['drip_type']=='messenger_bot_engagement_messenger_codes')
                  $href='messenger_engagement/messenger_codes_edit/';
                  else if($value['drip_type']=='messenger_bot_engagement_2way_chat_plugin')
                  $href='messenger_engagement/edit_domain/';

                  if($value['engagement_table_id']!=0)
                  {
                    $href=base_url($href.$value['engagement_table_id']);
                    $details='<a class="btn btn-default" target="_BLANK" href="'.$href.'"><i class="fa fa-eye"></i> '.$this->lang->line("details").'</a>';
                  }
                }

                echo "<tr>";
                  echo "<td class='text-center'>".$i."</td>";
                  echo "<td class='text-center'>".$value['campaign_name']."</td>";
                  echo "<td class='text-center'>".date('d M y - H:i:s',strtotime($value['created_at']))."</td>";
                  echo "<td class='text-center'>".$reply_at."</td>";
                  echo "<td class='text-center'>".$drip_type."</td>";
                  if($this->is_engagement_exist) 
                  {
                    echo "<td class='text-center'>".$details."</td>";
                  }
                  echo "<td class='text-center'>";
                  echo "<a class='btn btn-outline-info message_content btn-sm' href='' title='".$this->lang->line("Report")."' data-id='".$value['id']."'><i class='fa fa-eye'></i></a>&nbsp;";
                  echo "<a  class='btn btn-outline-warning btn-sm' href='".base_url("drip_messaging/edit_campaign/".$value['id'].'/'.$page_auto_id)."' title='".$this->lang->line("edit")."'><i class='fa fa-edit'></i></a>&nbsp;";
                  if($value['drip_type']!='default') echo "<a class='delete_bot btn btn-outline-danger btn-sm' id='".$value['id']."' title='".$this->lang->line("delete")."'><i class='fa fa-trash'></i></a>";
                  else echo "<a class='btn btn-default border_gray gray btn-sm' id='".$value['id']."' title='".$this->lang->line("delete")."'><i class='fa fa-trash'></i></a>";
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
      $doyoureallywanttodeletethisbot = $this->lang->line("do you really want to delete this settings?");
    ?>
  </div>

</div>

<?php 
$areyousure=$this->lang->line("are you sure"); 
?>

<link href="<?php echo base_url('plugins/select_search/select2.css')?>" rel="stylesheet"/>
<script src="<?php echo base_url('plugins/select_search/select2.js')?>"></script>

<script type="text/javascript">
  var day_counter = '<?php echo $default_display;?>';
  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";
  var areyousure="<?php echo $areyousure;?>";
  var page_auto_id = '<?php echo $page_auto_id; ?>';

  function refresh_template(push_id)
  {    
    var page_id='<?php echo $page_auto_id;?>';
    $.ajax({
      type:'POST' ,
      url: base_url+"drip_messaging/get_postback",
      data: {page_id:page_id,push_id:push_id},
      success:function(response){
        $("#push_postback"+push_id).html(response);
      }
    });
  }

  $j("document").ready(function(){

    var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?><?php echo $page_info['id'];?>";
    $('#add_template_modal').on('shown.bs.modal',function(){ 
       $(this).find('iframe').attr('src',iframe_link);
    });

    $j(".timepicker").datetimepicker({
      datepicker:false,
      format:"H:i"
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


    

    $('.checkicon').css('display','none');

    $(".template_id").select2(); 

    $(document.body).on('click','#add_bot_settings',function(){
       $("#add_bot_settings_modal").removeClass('hidden');
       $(".bot_success").hide();
       $("#error_message").addClass('hidden');
       $('html, body').animate({scrollTop: $("#add_bot_settings_modal").offset().top}, 2000);
    });

    $(document.body).on('click','.css-label',function(){
      if($(this).hasClass('dynamic_color')) return false;
      $(this).siblings().removeClass('dynamic_color').css('color',"#000");
      $(this).addClass('dynamic_color').css('color',"#fff");
      $(this).siblings().children('.checkicon').hide();
      $(this).children('.checkicon').toggle();
    });    
    $(".default-label").click();

    $(document.body).on('click','.ref_template',function(e){
      e.preventDefault();
      var push_id=$(this).attr('data-id');
      refresh_template(push_id);
    });

    $(document.body).on('click','.delete_bot',function(){
      var id = $(this).attr('id');      
      var somethingwentwrong = "<?php echo $somethingwentwrong; ?>";
      var doyoureallywanttodeletethisbot = "<?php echo $doyoureallywanttodeletethisbot; ?>";

      alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttodeletethisbot, 
      function(){ 
        $.ajax({
           type:'POST' ,
           url: "<?php echo base_url('drip_messaging/delete_campaign')?>",
           data: {id:id,page_auto_id:page_auto_id},
           success:function(response)
           {
            if(response=='1')
            { 
              window.location.assign(base_url+'drip_messaging/campaign_list/'+'<?php echo $page_auto_id; ?>');
              alertify.success('<?php echo $this->lang->line("your data has been successfully deleted from the database."); ?>');
            }
            else 
            alertify.alert('<?php echo $this->lang->line("Alert"); ?>',somethingwentwrong,function(){});
           }
        });
      },
      function(){     
      });
    });    

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
    $("#bot_settings_data_table").DataTable();
    
    $(document.body).on('click','#add_more_day',function(e){
       e.preventDefault();
       var how_many_days='<?php echo $how_many_days;?>';
       how_many_days=parseInt(how_many_days);
       if(day_counter>=how_many_days) 
       {
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('You can not add more days.')?>",function(){});
        return false;
       }
       day_counter++;
       $("#day_container"+day_counter).removeClass('hidden');
       $('#day_counter').val(day_counter);
    });

    $(document.body).on('click','#remove_last_day',function(e){
       e.preventDefault();
       if(day_counter<2) 
       {
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>','<?php echo $this->lang->line("You can not remove the last item.");?>',function(){});
        return false;
       }
       $("#day_container"+day_counter).addClass('hidden');
       day_counter--;
       $('#day_counter').val(day_counter);
    });

    $(document.body).on('change','input[name=drip_type]',function(){    
      if($("input[name=drip_type]:checked").val()=="default" || $("input[name=drip_type]:checked").val()=="custom")
      {
        $("#engagement_block").addClass('hidden');
        $("#loader2").addClass('hidden');
      }
      else 
      {
        $("#loader2").removeClass('hidden');
        var table_name=$("input[name=drip_type]:checked").val();
        $.ajax({
           type:'POST' ,
           url: "<?php echo base_url('drip_messaging/get_engagement_list')?>",
           data: {table_name:table_name,page_auto_id:page_auto_id},
           success:function(response)
           {
             $("#put_engegement_content").html(response);
             $("#loader2").addClass('hidden');
             $("#engagement_block").removeClass('hidden');
           }
        });
       
      }
    });

    $(document.body).on('click','.message_content',function(e){
      e.preventDefault();
      var campaign_id = $(this).attr('data-id'); // campaign id
      $("#message_content_modal_content").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
      $("#message_content_modal").modal(); 
      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>drip_messaging/get_campaign_report",
        data:{campaign_id:campaign_id},
        success:function(response){ 
           $('#message_content_modal_content').html(response);  
        }
      });
    }); 


    $(document.body).on('click','#submit_btn',function(e){
      e.preventDefault();      

      var between_start=$("#between_start").val();
      var between_end=$("#between_end").val();
      var rep1 = parseFloat(between_start.replace(":", "."));
      var rep2 = parseFloat(between_end.replace(":", "."));
      var rep_diff=rep2-rep1;

      if((between_start== '' &&  between_end!= '') || (between_start!= '' &&  between_end== ''))
      {
        alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('You must select both broadcast start and end time.');?>",function(){});
        return false;
      }

      if(between_start!="" && between_end!="")
      {
        if(rep1 >= rep2 || rep_diff<1.0)
        {
          alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Between start time must be less than end time and need to have minimum one hour time span.');?>",function(){});
          return false;
        }
        if($("#timezone").val()=="")
        {
           alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Please select time zone.');?>",function(){});
           return false;
        }
      }     

      var is_day_selected=false;
      $(".template_id").each(function(){
        if($(this).val()!='') is_day_selected=true;          
      });
      
      if(!is_day_selected)
      {
        $("#error_modal_content").html("<?php echo $this->lang->line('You have not selected template for any day.')?>");
        $("#error_modal").modal();
        return;
      }

      $("#submit_btn").addClass('disabled');      
      $("#loader").removeClass('hidden');

      var queryString = new FormData($("#plugin_form")[0]);
      $.ajax({
        type:'POST' ,
        url: base_url+"drip_messaging/create_campaign_action",
        data: queryString,
        dataType : 'JSON',
        // async: false,
        cache: false,
        contentType: false,
        processData: false,
        success:function(response){
          if(response.status=='1')
          window.location.assign("<?php echo base_url('drip_messaging/campaign_list/'.$page_auto_id);?>");
          else $("#error_message").removeClass('hidden').html(response.message);

          $("#loader").addClass('hidden');
          $("#submit_btn").removeClass('disabled');

        }

      });

    });

  });
</script>



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

<div class="modal fade" id="message_content_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" style="min-width: 80%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-eye"></i> <?php echo $this->lang->line('Report'); ?></h4>
      </div>
      <div class="modal-body" id="message_content_modal_content">
          
      </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_template_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Template'); ?></h4>
      </div>
      <div class="modal-body"> 
        <iframe width="100%" height="450px" frameborder="0" src="" ></iframe> 
      </div>
      <div class="modal-footer">
        <button data-dismiss="modal" type="button" class="btn-lg btn btn-dark"><i class="fa fa-refresh"></i> <?php echo $this->lang->line("Close");?></button>
      </div>
    </div>
  </div>
</div>

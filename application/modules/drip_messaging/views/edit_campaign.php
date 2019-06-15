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
  .widget-user-header{border-radius: 0;background: #607D8B;color:#fff;}
  .box-footer{border-radius: 0;}
  #loader,#loader2{margin-top:20px;margin-bottom: 30px;}
  .widget-user-desc a{font-weight: bold;}
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
  .widget-user-desc a{color: #fff;}
  .well2{padding:12px;border:1px solid #0069D9; color:#0069D9;margin-bottom:25px;}
</style>

<div class="container-fluid">
  <div class="" id="add_bot_settings_modal">
    <div class="modal-dialog" style="width:100%;margin:20px 0 0 0;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="padding-left: 35px;"><i class='fa fa-edit'></i> <?php echo $this->lang->line("Edit Drip Message Campaign");?></h4>
        </div>
        <div class="modal-body" style="padding-left:30px;"> 


          <form action="#" enctype="multipart/form-data" id="plugin_form" style="padding: 0 20px 20px 20px">
            <input type="hidden" name="day_counter" id="day_counter" value="<?php echo $default_display;?>">
            <input type="hidden" name="page_id" id="page_id" value="<?php echo $page_auto_id;?>">
            <input type="hidden" name="campaign_id" id="campaign_id" value="<?php echo $xdata['id'];?>">
            <div class='well2 text-center'><?php echo $this->lang->line('Drip message sequence must not contain any advertisement or promotional material.');?></div>
            
            <div class="row">
              <div class="form-group col-xs-12">             
                <label><?php echo $this->lang->line("Campaign Name"); ?></label>
                <input type="text" name="campaign_name" id="campaign_name" class="form-control" value="<?php echo $xdata['campaign_name'];?>">  
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
                  echo '<input type="radio" class="css-checkbox" name="drip_type" value="default" id="drip_type1"> <label for="drip_type1" class="css-label six-label"> <i class="checkicon fa fa-check"></i> '.$this->lang->line("Default").'</label>';
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
                    <h5 class="widget-user-desc text-center"><a class='orange' href="https://facebook.com/<?php echo  $page_info['page_id'];?>"><?php echo  $page_info['page_name'];?></a> | <?php echo  $page_info['account_name'];?></h5>
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
                  <input type="text" class="form-control timepicker" value="<?php echo $xdata['between_start'];?>" id="between_start" name="between_start">
                  </div>
              </div>
              <div class="col-xs-12 col-sm-6 col-md-3">
                  <div class="form-group">
                      <label class="hidden-xs hidden-sm" style="position: relative;right: 22px;top: 32px;">-</label>
                      <input type="text" class="form-control timepicker" value="<?php echo $xdata['between_end'];?>" id="between_end" name="between_end">
                  </div>
              </div>
              <div class="col-xs-12 col-md-3">
                  <div class="form-group">
                  <label><?php echo $this->lang->line("Time Zone");?></label>
                  <?php echo form_dropdown('timezone', $timezones,$xdata['timezone'],"class='form-control' id='timezone'");?>
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
                        $message_content=json_decode($xdata['message_content'],true);
                        $select_template=isset($message_content[$i])?$message_content[$i]:'';
                        $template_id="template_id".$i;
                        // $template_list['']=$this->lang->line("Message Template").' '.$this->lang->line("Day").'-'.$i;
                        $template_list['']="--- ".$this->lang->line("No")." ".$this->lang->line("Message")." ---";
                        echo form_dropdown($template_id,$template_list,$select_template,'class="form-control template_id" id="'.$template_id.'"'); 
                      ?>
                  </div>
                </div>
                <div class="form-group col-xs-6 col-md-2">              
                  <a href="" title="<?php echo $this->lang->line("Refresh");?>" data-id="<?php echo $i;?>" class="ref_template btn btn-lg"><i class="fa fa-refresh blue"></i></a>
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

            <img id="loader" class="center-block" src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>">
            <div id="submit_status" class="text-center"></div>

          </form>              

        </div>
        <div class="modal-footer" style="padding-left: 4.5%;">
           <a id="submit_btn" href="" class="btn btn-primary btn-lg pull-left"><i class="fa fa-edit"></i> <?php echo $this->lang->line('Edit Campaign');?></a>             
           <div class="clearfix"></div>
           <br><div class="alert alert-danger hidden text-center" id="error_message"></div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php 
$somethingwentwrong = $this->lang->line("something went wrong.");  
$areyousure=$this->lang->line("are you sure"); 
$is_engagement_exist=($this->is_engagement_exist==true) ? '1' : '0';
?>

<link href="<?php echo base_url('plugins/select_search/select2.css')?>" rel="stylesheet"/>
<script src="<?php echo base_url('plugins/select_search/select2.js')?>"></script>

<script type="text/javascript">
  var day_counter = '<?php echo $default_display;?>';
  var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
  var base_url="<?php echo site_url(); ?>";
  var areyousure="<?php echo $areyousure;?>";
  var page_auto_id = '<?php echo $page_auto_id; ?>';
  var is_engagement_exist = '<?php echo $is_engagement_exist;?>';

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

    $j(".timepicker").datetimepicker({
      datepicker:false,
      format:"H:i"
    });

    var iframe_link="<?php echo base_url('messenger_bot/create_new_template/1/');?><?php echo $page_info['id'];?>";
    $('#add_template_modal').on('shown.bs.modal',function(){ 
      $(this).find('iframe').attr('src',iframe_link);
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
 
    $(document.body).on('click','.ref_template',function(e){
      e.preventDefault();
      var push_id=$(this).attr('data-id');
      refresh_template(push_id);
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
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>','<?php echo $this->lang->line("You can not add more days.");?>',function(){});
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
        var engagement_id='<?php echo $xdata["engagement_table_id"];?>';
        $.ajax({
           type:'POST' ,
           url: "<?php echo base_url('drip_messaging/get_engagement_list')?>",
           data: {table_name:table_name,page_auto_id:page_auto_id,engagement_id:engagement_id},
           success:function(response)
           {
             $("#put_engegement_content").html(response);
             $("#loader2").addClass('hidden');
             $("#engagement_block").removeClass('hidden');
           }
        });
       
      }
    });

    
    var drip_type='<?php echo $xdata["drip_type"];?>';
    $('input[type="radio"][name="drip_type"][value="'+drip_type+'"]').attr('checked','checked');
    $('input[type="radio"][name="drip_type"][value="'+drip_type+'"]').next().addClass('default-label');
    $('input[type="radio"][name="drip_type"][value="'+drip_type+'"]').trigger('change');
    if(drip_type!='default' && drip_type!='custom') $("#engagement_block").removeClass('hidden');
    
    $(".default-label").click();
    $("#loader").addClass('hidden');


    $(document.body).on('click','#submit_btn',function(e){
      e.preventDefault();      

      var drip_type=$("input[name=drip_type]:checked").val();
      if(drip_type!="default" && drip_type!="custom")
      var engagement_table_id=$("input[name=engagement_table_id]:checked").val();
      if(drip_type!="default" && drip_type!="custom" && typeof(engagement_table_id)==='undefined')
      {
        $("#error_modal_content").html("<?php echo $this->lang->line('You have not selected any engagement campaign to re-target.')?>");
        $("#error_modal").modal();
        $("#loader").addClass('hidden');
        $("#submit_btn").removeClass('disabled');
        return;
      }

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
        $("#error_modal_content").html("<?php echo $this->lang->line('You have not select template for any day.')?>");
        $("#error_modal").modal();
        return;
      }

      $("#loader").removeClass('hidden');
      $("#submit_btn").addClass('disabled');

      var queryString = new FormData($("#plugin_form")[0]);
      $.ajax({
        type:'POST' ,
        url: base_url+"drip_messaging/edit_campaign_action",
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
<style type="text/css">
  .space{height: 10px;}
  .css-checkbox{display: none;}
  .css-label{padding:8.5px 10px; background: #eee;border-radius: 7px;-moz-border-radius: 7px;-webkit-border-radius: 7px;text-align: center;}
  .css-label:hover{background: #ddd;cursor: pointer;}
  .single-label{min-width: 97.5%;}
  .double-label{min-width: 48.5%;}
  .triple-label{min-width: 31%;}
</style>
<?php $is_broadcaster_exist=$this->is_broadcaster_exist; ?>
<?php $is_ultrapost_exist=$this->is_ultrapost_exist; ?>

<div class="container-fluid">
  <br>
  <?php if($this->session->flashdata('auto_success')===1) { ?>
  <div class="alert alert-success text-center"><i class="fa fa-check"></i> <?php echo $this->lang->line("settings has been stored to database successfully.");?></div>
  <?php } ?>
  <?php if($this->session->flashdata('auto_success')===0) { ?>
  <div class="alert alert-danger text-center"><i class="fa fa-remove"></i> <?php echo $this->lang->line("settings has been failed to store in database.");?></div>
  <?php } ?>


  <div class="box box-widget widget-user-2" >
    <div class="widget-user-header" style="border-radius: 0;">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-9">
          <div class="widget-user-image">
            <!-- <img class="img-circle" src="<?php echo $page_info['page_profile'];?>"> -->
            <i class="fa fa-rss fa-4x pull-left dynamic_font_color"></i>
          </div>
          <h3 class="widget-user-username dynamic_font_color"><?php echo $this->lang->line("Auto Posting"); ?></h3>
          <h5 class="widget-user-desc"><?php echo $this->lang->line("Feed List"); ?></h5>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
          <a class="btn btn-outline-primary pull-right" id="add_feed" style="margin-top:15px;" data-toggle="modal" href='#add_feed_modal'><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('New Auto Posting Feed');?></a>
        </div>
      </div>
    </div>
    <div class="box-footer" style="border-radius: 0;padding:20px;">
      <?php 
      if(empty($settings_data)) echo "<h4 class='text-center'>".$this->lang->line('No settings found.')."</h4>";
      else
      {
          echo "<div class='table-responsive'> 
          <table class='table table-bordered table-condensed' id='settings_data_table'>";
            echo "<thead>";
              echo "<tr>";
                echo "<th class='text-center'>".$this->lang->line("SN")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Feed Name")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Feed Type")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Status")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Last Updated")."</th>";
                echo "<th class='text-center'>".$this->lang->line("Last Feed")."</th>";
                if($this->is_broadcaster_exist)
                echo "<th class='text-center'>".$this->lang->line("Broadcast as Page")."</th>";
                if($this->is_ultrapost_exist)
                echo "<th>".$this->lang->line("Post as Pages")."</th>";                
            echo "</tr></thead>";

            echo "<tbody>";
              $i=0;
              foreach ($settings_data as $key => $value) 
              {
                $i++;
                if($value['last_pub_date']!="0000-00-00 00:00:00") $last_pub_date=date('j M H:i',strtotime($value['last_pub_date']));
                else $last_pub_date =  "<i class='fa fa-remove'></i>";

                $page_names=json_decode($value['page_names'],true);
                if(!is_array($page_names)) $page_names=array();
                $page_names=array_values($page_names);
                $page_names=implode(',', $page_names);
                
                $page_name=$value['page_name'];

                if($page_names=="") $page_names="x";
                if($page_name=="") $page_name="x";

                if($page_names!="x") $page_names="<a target='_BLANK' href='".base_url("ultrapost/text_image_link_video")."'>".$page_names."</a>";
                if($page_name!="x") $page_name="<a target='_BLANK' href='".base_url("messenger_broadcaster/quick_bulk_broadcast_report")."'>".$page_name."</a>";

                $status='';
                if($value['status']=='1') $status='<span class="label label-light"><i class="fa fa-check-circle green"></i> '.$this->lang->line("active").'</span>';
                else if($value['status']=='0') $status='<span class="label label-light"><i class="fa fa-remove red"></i> '.$this->lang->line("inactive").'</span>';
                else $status='<span class="label label-light"><i class="fa fa-ban gray"></i> '.$this->lang->line("paused").'</span>';

                echo "<tr>";
                  echo "<td class='text-center' nowrap>".$i."</td>";
                  echo "<td class='text-center' nowrap><a href='".$value['feed_url']."' target='_BLANK'>".$value['feed_name']."</a></td>";
                  echo "<td class='text-center' nowrap>".$value['feed_type']."</td>";
                  echo "<td class='text-center' nowrap>".$status."</td>";
                  echo "<td class='text-center' nowrap>";
                   
                   echo "<a title='".$this->lang->line("Settings")."' class='btn btn-sm btn-outline-primary campaign_settings' data-id='".$value['id']."'><i class='fa fa-cogs'></i></a>&nbsp;";
                   
                   if($value['status']=='1')
                   echo "<a title='".$this->lang->line("Disable")."' class='btn btn-sm btn-outline-warning disable_settings' data-id='".$value['id']."'><i class='fa fa-ban'></i></a>&nbsp;";
                   else echo "<a title='".$this->lang->line("Enable")."' id='enable".$value['id']."' class='btn btn-sm btn-outline-success enable_settings' data-id='".$value['id']."'><i class='fa fa-check-circle'></i></a>&nbsp;";
                   
                   echo "<a title='".$this->lang->line("Delete")."' class='btn btn-sm btn-outline-danger delete_settings' data-id='".$value['id']."'><i class='fa fa-trash'></i></a>&nbsp;";
                   
                   echo "<a title='".$this->lang->line("Error")."' class='btn btn-sm btn-default error_log border_gray' data-id='".$value['id']."'><i class='fa fa-bug'></i></a>";
                   echo "</td>";

                   echo "<td class='text-center' nowrap>".date("d M H:i",strtotime($value["last_updated_at"]))."</td>";
                   echo "<td class='text-center' nowrap>".$last_pub_date."</td>";

                   if($this->is_broadcaster_exist)
                   echo "<td class='text-center' nowrap>".$page_name."</td>";

                  if($this->is_ultrapost_exist)
                   echo "<td class='text-center' nowrap>".$page_names."</td>";

                   
                echo "</tr>";
              }
            echo "</tbody>";
          echo "</table></div>";

      }
      ?>
    </div>
  </div>

</div>


<?php
  $somethingwentwrong = $this->lang->line("something went wrong, please try again.");  
  $doyoureallywanttodeletethisbot = $this->lang->line("Do you really want to delete this settings?");
  $doyoureallywanttodisablethisbot = $this->lang->line("Do you really want to disable this settings?");
  $doyoureallywanttoenablethisbot = $this->lang->line("Do you really want to enable this settings? This operation may take few time.");
  $areyousure=$this->lang->line("are you sure"); 
?>

<script type="text/javascript">
  $j("document").ready(function(){
    var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
    var base_url="<?php echo site_url(); ?>";
    var areyousure="<?php echo $areyousure;?>";
    var is_broadcaster_exist="<?php echo $is_broadcaster_exist;?>";
    var is_ultrapost_exist="<?php echo $is_ultrapost_exist;?>";
    var doyoureallywanttodeletethisbot="<?php echo $doyoureallywanttodeletethisbot;?>";
    var doyoureallywanttodisablethisbot="<?php echo $doyoureallywanttodisablethisbot;?>";
    var doyoureallywanttoenablethisbot="<?php echo $doyoureallywanttoenablethisbot;?>";
    var somethingwentwrong="<?php echo $somethingwentwrong;?>";

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
    $("#settings_data_table").DataTable();

    $(document.body).on('click','.campaign_settings',function(e){ 
      e.preventDefault();
      var id=$(this).attr('data-id');
       $.ajax({
          type:'POST' ,
          url: base_url+"autoposting/campaign_settings",
          data: {id:id},
          dataType: 'JSON',
          success:function(response)
          {  
            if(response.status=='0') $("#settings_modal .modal-footer").hide();
            else $("#settings_modal .modal-footer").show();
            $("#feed_setting_container").html(response.html);
            $("#put_feed_name").html(" : "+response.feed_name);                       
            $("#settings_modal").modal();
          }
      });
     
    });

    $(document.body).on('click','#save_settings',function(e){ 
      e.preventDefault();

      var post_to_pages = $("#post_to_pages").val();
      var post_to_groups = $("#post_to_groups").val();
      var broadcast_pages='';

      if(is_broadcaster_exist=='1')
      broadcast_pages = $("#page").val();

      if(post_to_pages==null && broadcast_pages=='')
      {
        alertify.alert('<?php echo $this->lang->line("Alert");?>',"<?php echo $this->lang->line('Please select pages to publish the new feeds as Facebook post or select a page to broadcast new the feeds to messenger subscribers.');?>",function(){ });
        return;
      }

      if(post_to_pages!=null)
      {
        var posting_start_time=$("#posting_start_time").val();
        var posting_end_time=$("#posting_end_time").val();
        var rep1 = parseFloat(posting_start_time.replace(":", "."));
        var rep2 = parseFloat(posting_end_time.replace(":", "."));
        var rep_diff=rep2-rep1;

        if(posting_start_time== '' ||  posting_end_time== ''){
          alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Please select post between times.');?>",function(){});
          return false;
        }

        if(rep1 >= rep2 || rep_diff<1.0)
        {
          alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Post between start time must be less than end time and need to have minimum one hour time span.');?>",function(){});
          return false;
        }
      }

      if(broadcast_pages!='')
      {
        var broadcast_start_time=$("#broadcast_start_time").val();
        var broadcast_end_time=$("#broadcast_end_time").val();
        var rep1 = parseFloat(broadcast_start_time.replace(":", "."));
        var rep2 = parseFloat(broadcast_end_time.replace(":", "."));
        var rep_diff=rep2-rep1;

        if(broadcast_start_time== '' ||  broadcast_end_time== ''){
          alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Please select broadcast between times.');?>",function(){});
          return false;
        }

        if(rep1 >= rep2 || rep_diff<1.0)
        {
          alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Broadcast between start time must be less than end time and need to have minimum one hour time span.');?>",function(){});
          return false;
        }
      }


      // var loading = '<img src="'+base_url+'assets/pre-loader/custom_lg.gif" class="center-block">';
      $("#submit_status").show();
      var queryString = new FormData($("#campaign_settings_form")[0]);
      $("#save_settings,close_settings").addClass("disabled");
      $("#submit_response").attr('class','').html('');
      var id=$(this).attr('data-id');
       $.ajax({
          type:'POST' ,
          url: base_url+"autoposting/create_campaign",
          dataType: 'JSON',
          data: queryString,
          cache: false,
          contentType: false,
          processData: false,
          success:function(response)
          { 
            if(response.status=='1') $("#submit_response").attr('class','alert alert-success text-center').html(response.message);
            else 
            {
              $("#submit_response").attr('class','alert alert-danger text-center').html(response.message);
              $("#save_settings .modal-footer").hide();
            }
            $("#save_settings,close_settings").removeClass("disabled");
            $("#submit_status").hide();
          }
      });
     
    });

    $(document.body).on('click','.enable_settings',function(e){ 
      e.preventDefault();
      $(this).addClass('disabled');
      var id=$(this).attr('data-id');
      alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttoenablethisbot, 
        function(){ 
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/enable_settings",
              data: {id:id},
              dataType:'JSON',
              success:function(response)
              {  
                if(response.status=='0') 
                {
                  $("#enable"+id).removeClass('disabled');
                  alertify.error(response.message); 
                }
                else location.reload();
              }
          });
        },
        function(){     
      });
     
    });

    $(document.body).on('click','.disable_settings',function(e){ 
      e.preventDefault();

      var id=$(this).attr('data-id');
      alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttodisablethisbot, 
        function(){ 
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/disable_settings",
              data: {id:id},
              success:function(response)
              {  
                location.reload();
              }
          });
        },
        function(){     
      });
     
    });

    $(document.body).on('click','.delete_settings',function(e){ 
      e.preventDefault();

      var id=$(this).attr('data-id');
      alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttodeletethisbot, 
        function(){ 
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/delete_settings",
              data: {id:id},
              success:function(response)
              {  
                location.reload();
              }
          });
        },
        function(){     
      });
     
    });

    $(document.body).on('click','#add_feed_submit',function(){ 

      var feed_type = $("input[name='feed_type']:checked").val();
      var feed_name = $("#feed_name").val();
      var feed_url = $("#feed_url").val();
      if(feed_type=='')
      {
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>','<?php echo $this->lang->line("Please select feed type.");?>',function(){});
        return;
      }
      if(feed_name=='')
      {
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>','<?php echo $this->lang->line("Please provide a feed name.");?>',function(){});
        return;
      }
      if(feed_url=='')
      {
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>','<?php echo $this->lang->line("Feed URL can not be empty.");?>',function(){});
        return;
      }
      $("#add_feed_submit").addClass('disabled');
      $("#loader").removeClass('hidden');
      var queryString = new FormData($("#add_feed_form")[0]);
      $.ajax({
          type:'POST' ,
          url: base_url+"autoposting/add_feed_action",
          data: queryString,
          dataType : 'JSON',
          // async: false,
          cache: false,
          contentType: false,
          processData: false,
          success:function(response)
          {  
            if(response.status=='1') 
            {
              $("#response").attr('class','alert alert-success text-center');
            }
            else 
            {
              $("#response").attr('class','alert alert-danger text-center');
            }

            $("#response").html(response.message);
            $("#loader").addClass('hidden');
            $("#add_feed_submit").removeClass('disabled');
          }

      });
    }); 

    $('#add_feed_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });
    $('#settings_modal').on('hidden.bs.modal', function () { 
      location.reload();
    });

    $(document.body).on('click','.css-label',function(){
      if($(this).hasClass('dynamic_color')) return false;
      $(this).siblings().removeClass('dynamic_color').css('color',"#000");
      $(this).addClass('dynamic_color').css('color',"#fff");
      $(this).siblings().children('.checkicon').hide();
      $(this).children('.checkicon').toggle();
    });
    $(".default-label").click();

    $(document.body).on('click','.error_log',function(e){ 
      e.preventDefault();
      $("#error_loading").removeClass('hidden');
      $("#error_modal_container").html("");
      $("#error_modal").modal();
      var id=$(this).attr('data-id');
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/error_log",
              data: {id:id},
              success:function(response)
              {  
                $("#error_modal_container").html(response);
                $("#error_loading").addClass('hidden');
              }
          });     
    });

    $(document.body).on('click','.clear_log',function(e){ 
      e.preventDefault();
      
      var id=$(this).attr('data-id');
      alertify.confirm('<?php echo $this->lang->line("confirm");?>','<?php echo $this->lang->line("are you sure");?>', 
        function(){ 
          $("#error_loading").removeClass('hidden');
           $.ajax({
              type:'POST' ,
              url: base_url+"autoposting/clear_log",
              data: {id:id},
              success:function(response)
              {  
                $("#error_modal_container").html(response);
                $("#error_loading").addClass('hidden');
              }
          });
        },
        function(){  $("#error_loading").addClass('hidden');   
      });   
    });


  });
</script>

<div class="modal fade" id="error_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="padding-left: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-bug"></i></h4>
      </div>
      <div class="modal-body">
         <center id="error_loading" class='hidden'><img style="margin:15px 0;" src="<?php echo base_url("assets/pre-loader/Fading squares2.gif");?>" alt=""></center>  
         <div id="error_modal_container"></div>    
      </div>
      <div class="modal-footer" style="padding-left: 30px;padding-right: 30px;">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal" id="close_settings"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Close");?></button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="settings_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="padding-left: 30px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-cogs"></i> <?php echo $this->lang->line("Campaign Settings");?> <span id="put_feed_name"></span></h4>
      </div>
      <div class="modal-body" id="feed_setting_container">
        
      </div>
      <div class="modal-footer" style="padding-left: 30px;padding-right: 30px;">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal" id="close_settings"><i class="fa fa-remove"></i> <?php echo $this->lang->line("Close");?></button>
        <button type="button" class="btn btn-primary pull-left" id="save_settings" style="margin-left: 0;"><i class="fa fa-send"></i> <?php echo $this->lang->line("Submit Campaign");?></button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="add_feed_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-rss"></i> <?php echo $this->lang->line('Auto Posting Feed');?></h4>
      </div>
      <form action="#" enctype="multipart/form-data" id="add_feed_form">
        <div class="modal-body">
            <img class="center-block hidden" id="loader" style="margin-bottom:15px;" src="<?php echo base_url("assets/pre-loader/Fading squares2.gif");?>" alt="">
            <div id="response"></div>
            <div class="space"></div>
            <div class='hidden'> <!-- hidden temporarily, will be needed in future -->
                <label class="margin-bottom-label" style="color: rgb(0, 0, 0);">
                <?php echo $this->lang->line("Feed Type") ?> *
                </label>
                <div class="space"></div>
                <?php 
                foreach ($feed_types as $key => $value) 
                {
                   $is_checked=$is_default_label='';
                   if($value=='rss')
                   {
                      $is_checked='checked';
                      $is_default_label='default-label';
                   }
                   echo '<input type="radio" class="css-checkbox" '.$is_checked.' name="feed_type" value="'.$value.'" id="feed_type'.$value.'" style="color: rgb(0, 0, 0);"> <label for="feed_type'.$value.'" class="css-label triple-label '.$is_default_label.'" style="color: rgb(0, 0, 0);"> <i class="checkicon fa fa-check" style="display: none;"></i> '.ucfirst($value).'</label>';
                } ?>
                <div class="space"></div>
            </div>

            <label class="margin-bottom-label" style="color: rgb(0, 0, 0);">
              <?php echo $this->lang->line("Feed Name") ?> *
            </label>
            <div class="space"></div>
            <input type="text" name="feed_name" id="feed_name" class="form-control">

            <div class="space"></div>
            <div class="space"></div>

            <label class="margin-bottom-label" style="color: rgb(0, 0, 0);">
              <?php echo $this->lang->line("RSS Feed URL") ?> *
            </label>
            <div class="space"></div>
            <input type="text" name="feed_url" id="feed_url" class="form-control">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary pull-left" id="add_feed_submit"><i class='fa fa-plus-circle'></i> <?php echo $this->lang->line('Add Feed');?></button>
          <button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class='fa fa-remove'></i> <?php echo $this->lang->line('Close');?></button>
        </div>
      </form>
    </div>
  </div>
</div>

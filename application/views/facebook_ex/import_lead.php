<style>
.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
}

hr{
   margin-top: 10px;
}

.custom-top-margin{
  margin-top: 20px;
}

.sync_page_style{
   margin-top: 8px;
}
/* .wrapper,.content-wrapper{background: #fafafa !important;} */
  .well{background: #fff;}
.box-shadow
{
  -webkit-box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
    box-shadow: 0px 2px 14px -3px rgba(0,0,0,0.75);
    border-bottom: 4px solid <?php echo $THEMECOLORCODE; ?>;
    margin-bottom: 10px !important;
}
</style>

<?php if(empty($page_info)){ ?>
     
  <div class="well well_border_left">
    <h4 class="text-center"> <i class="fa fa-facebook-official"></i><?php echo $this->lang->line("you have no page in facebook");?><h4>
  </div>
  
<?php 
}
else
{ ?>  
  <div class="well well_border_left">
    <h4 class="text-center blue"> <i class="fa fa-user-check"></i> <?php echo $this->lang->line("import lead(s) : page list");?><h4>
  </div>

  <div class="row" style="padding:0 15px;">
  <?php $i=0; foreach($page_info as $value) : ?>
    <div class="col-xs-12 col-sm-12 col-md-6">
      <div class="box box-shadow box-solid">
        <div class="box-header with-border text-center">
          <h3 class="box-title"> <a href="https://facebook.com/<?php echo $value['page_id'] ?>"><?php echo $value['page_name']; ?></a></h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
          <div class="col-xs-12">
            <div class="row">
              <div id="alert_<?php echo $value['id'];?>" class="alert alert-success text-center" style="display:none;"></div>
              <?php $profile_picture=$value['page_profile']; ?>
              <div class="text-center col-xs-12 col-md-4">
                <img src="<?php echo $profile_picture;?>" alt="" class='custom-top-margin' style='padding:1px;border:1px solid #aaa;' height="90" width="90">
                <div style="height: 2px;"></div>
                <span class="label label-default" style="background: #fff;border:1px solid #ccc;"><i class="fa fa-check-circle green"></i> <?php echo $this->lang->line("subscribed");?> : 
                   <?php 
                    if(empty($value['current_subscribed_lead_count'])) echo "0";
                    else echo custom_number_format($value['current_subscribed_lead_count']);
                    ?>
                </span>
                <div style="height: 3px;"></div>
                <span class="label label-default" style="background: #fff;border:1px solid #ccc;"><i class="fa fa-remove red"></i> <?php echo $this->lang->line("unsubscribed") ?> : 
                   <?php 
                    if(empty($value['current_unsubscribed_lead_count'])) echo "0";
                    else echo custom_number_format($value['current_unsubscribed_lead_count']);
                    ?>
                </span>
              </div>
              <div class="col-xs-12 col-md-8">
                <br/>
                <div class="info-box">
                  <span class="info-box-icon bg-blue" style="background: <?php echo $THEMECOLORCODE;?> !important;"><i class="fa fa-user-check"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text"><b><?php echo $this->lang->line("Total Leads");?></b></span><hr style="margin-bottom:2px;">
                    <span class="info-box-number" style="font-size:30px">
                      <?php 
                      if(empty($value['current_lead_count'])) echo "0";
                      else echo number_format($value['current_lead_count']);
                      ?>
                    </span>
                  </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->

                <div class="sync_page_style text-center" style="padding-top: 3px;">
                  <span class="info-box-text">
                    <button id ="<?php echo $value['id'];?>" type="button" style='width:45%;' class="pull-left btn-sm btn btn-outline-primary import_data"><i class="fa fa-search"></i><?php echo $this->lang->line("scan page inbox");?></button>
                    <?php 
                        if(!empty($value['current_lead_count'])) $is_hidden = "";
                        else $is_hidden = "hidden";
                        echo "<button id ='".$value['user_id']."-".$value['id']."-".$value['page_name']."' style='width:45%;' type='button' class='".$is_hidden." btn-sm user_details_modal btn btn-primary pull-right'><i class='fa fa-list'></i> ".$this->lang->line("lead list")."</button>";
                    ?>
                  </span>
                </div>               
              </div>                  
            </div><!-- /.row -->
            <hr>
            <div class="row">             
              <div class="col-xs-6 clearfix"> 

                <?php 
                $popover="";
                if($value['auto_sync_lead']=="0" || $value['auto_sync_lead']=="3")
                {
                  $enable_disable = 1;
                  $enable_disable_class = "auto_sync_lead_page btn-outline-success";
                  $enable_disable_text = "<i class='fa fa-check-circle'></i> ".$this->lang->line("Enable Background Scan");
                }
                if($value['auto_sync_lead']=="1")
                {
                  $enable_disable = 0;
                  $enable_disable_class = "btn-outline-danger disabled";
                  $enable_disable_text = "<i class='fa fa-clock-o'></i> ".$this->lang->line("Background Scan Pending");
                }
                if($value['auto_sync_lead']=="2")
                {
                  $enable_disable = 1;
                  $enable_disable_class = "btn-outline-warning auto_sync_lead_page";
                  $enable_disable_text = "<i class='fa fa-spinner'></i> ".$this->lang->line("Processing, Force Restart");
                  $popover=' <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="'.$this->lang->line("Force Restart").'" data-content="'.$this->lang->line("Background scanning is processing. Due to any unexpected server unavailability this process can be corrupted and can run forever. If you think this is processing forever, then you can force restart it.").'"><i class="fa fa-info-circle orange"></i> </a>';
                }

                if($this->session->userdata('user_type') == 'Admin' || in_array(78,$this->module_access))
                echo "<button style='margin-top:3.5px' enable_disable='".$enable_disable."' auto_sync_lead_page_id ='".$value['page_id']."' type='button' class='btn-sm btn ".$enable_disable_class."'>{$enable_disable_text}</button>". $popover; 
                
                ?> 
              </div>  
               <div class="col-xs-6">                
                  <small class="pull-right">
                    <?php 
                    echo $this->lang->line("last scanned")." <br/>";
                    if($value['last_lead_sync']!="0000-00-00 00:00:00") echo "<span style='font-weight:normal;' class='label label-default'>".date("jS M, y H:i:s",strtotime($value['last_lead_sync']))."<span>";
                    else echo "<span style='font-weight:normal;' class='label label-default'><i class='fa fa-clock-o'></i> ".$this->lang->line("never scanned")."</span>";
                  ?>
                  </small>
              </div>

            </div>
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
      <br/>
    </div>
  <?php   
      $i++;
      if($i%2 == 0)
      echo "</div><div class='row' style='padding:0 15px;'>";
      endforeach;
  ?>
</div>
<?php } ?>

<?php 
    
    $disabledsuccessfully = $this->lang->line("daily auto scan has been disabled successfully.");
    $enabledsuccessfully = $this->lang->line("daily auto scan has been enabled successfully.");

?>

<script type="text/javascript">

  var base_url="<?php echo base_url();?>";

  $j("document").ready(function(){

      $('[data-toggle="popover"]').popover(); 
      $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
  });     

  $j(document.body).on('click','.import_data',function(){
    var id=$(this).attr('id');
    $("#start_scanning").attr("data-id",id);
    $("#import_lead_modal").modal();
  });


  $j(document.body).on('click','#start_scanning',function(){
    var id=$(this).attr('data-id');
    var scan_limit=$("#scan_limit").val();
    $("#start_scanning").addClass('disabled');
    $(".auto_sync_lead_page").addClass('disabled');
    $(".user_details_modal").addClass('disabled');
    var  loading = '<img src="'+base_url+'assets/pre-loader/custom_lg.gif" class="center-block">';    
    $("#scan_load").attr('class','');
    $("#scan_load").html(loading);
    $.ajax({
      type:'POST' ,
      url:"<?php echo site_url();?>facebook_ex_import_lead/import_lead_action",
      data:{id:id,scan_limit:scan_limit},
      dataType:'JSON',
      success:function(response){
       // $("#"+alert_id).addClass("alert-success");
       if(response.status=='1') $("#scan_load").attr('class','alert alert-success text-center');
       else $("#scan_load").attr('class','alert alert-danger text-center');
       $("#scan_load").html(response.message);
       $("#start_scanning").removeClass('disabled');
      }
    });

  });

  $j(document.body).on('click','.user_details_modal',function(){
    var user_page_id = $(this).attr('id');
    var base_url = '<?php echo site_url();?>';
    $("#response_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/custom_lg.gif" alt="Processing..."><br/>');
    $("#htm").modal(); 
    setTimeout(function(){ 
     $.ajax({
      type:'POST' ,
      url:"<?php echo site_url();?>facebook_ex_import_lead/user_details_modal",
      data:{user_page_id:user_page_id},
      success:function(response){ 
         $('#response_div').html(response);  
      }
    });
  }, 1000);


  }); 


  $j(document.body).on('click','.migrate_leadTO_bot',function(){
    

    alertify.confirm(<?php echo '"'.$this->lang->line('Confirmation').'"'; ?>, <?php echo '"'.$this->lang->line('Do you really want to migrate lead to your bot?').'"'; ?>, function(){
      
        var base_url = '<?php echo site_url();?>';

        $("#response_div_01").html('<img class="center-block" src="'+base_url+'assets/pre-loader/custom_lg.gif" alt="Processing..."><br/>');
        $("#migrate_leadTO_bot_modal").modal(); 


        var user_page_id = $(".migrate_leadTO_bot").attr('button_id');

        $.ajax({
          type:'POST' ,
          url:"<?php echo site_url();?>facebook_ex_import_lead/migrateLeadToBot",
          dataType: 'json',
          data:{user_page_id : user_page_id},
          success:function(response){ 
             // // $('#response_div').html(response.message);  
             // console.log(response);
             $("#migrate_leadTO_bot_modal").modal('hide'); 

             alertify.alert(<?php echo '"'.$this->lang->line('Migration Result').'"'; ?>, response.message, function(){});
          }
        });
      
    }, function(){});



  }); 


  $j(document.body).on('click','.auto_sync_lead_page',function(){
    var page_id = $(this).attr('auto_sync_lead_page_id');
    var operation = $(this).attr('enable_disable');
    var base_url = '<?php echo site_url();?>';

    var disabledsuccessfully = '<?php echo $disabledsuccessfully;?>';
    var enabledsuccessfully = '<?php echo $enabledsuccessfully;?>';
     $(".import_data").addClass('disabled');
    $(".auto_sync_lead_page").addClass('disabled');
    $(".user_details_modal").addClass('disabled');
    $.ajax({
      type:'POST' ,
      url:"<?php echo site_url();?>facebook_ex_import_lead/enable_disable_auto_sync",
      data:{page_id:page_id,operation:operation},
      success:function(response)
      {  
         // if(operation=="0") alertify.alert(disabledsuccessfully);
         // else alertify.alert(enabledsuccessfully);
         // location.reload();

         if(operation=="0") alertify.alert('<?php echo $this->lang->line("Enable/Disable Report");?>',disabledsuccessfully,function(){ location.reload(); });
         else alertify.alert('<?php echo $this->lang->line("Enable/Disable Report");?>',enabledsuccessfully,function(){ location.reload(); });
         
      }
    });

  });

  $(document.body).on('click','.client_thread_subscribe_unsubscribe',function(){
    $(this).html('please wait...');
    var client_subscribe_unsubscribe_status = $(this).attr('id');

    $.ajax({
      type:'POST',
      url:"<?php echo site_url();?>facebook_ex_import_lead/client_subscribe_unsubscribe_status_change",
      data:{client_subscribe_unsubscribe_status:client_subscribe_unsubscribe_status},
      success:function(response){
         $("#"+client_subscribe_unsubscribe_status).parent().html(response); 
      }
    });

  });


  var base_url="<?php echo base_url();?>";

  $j("document").ready(function(){

      $('[data-toggle="popover"]').popover(); 
      $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

      $('#import_lead_modal').on('hidden.bs.modal', function () { 
        location.reload();
      });
  });     


</script>


<div class="modal fade" id="htm" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-check"></i> <?php echo $this->lang->line("lead list");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12">
                      <div id="response_div" style="padding: 20px;"></div>                      
                    </div>
                </div>               
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="import_lead_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("scan page inbox");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12">
                      <div id="import_lead_body">
                        <div id="scan_load"></div><br>
                        <div class="form-group">              
                            <label>
                              <i class="fa fa-sort-numeric-down"></i> <?php echo $this->lang->line("Scan Latest Leads");?>
                              <a href="#" data-placement="right" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Scanning process scans your page conversation and import them as leads. We strongly recommend to use cron based scanning feature for first time, if your page conversation is huge. After importing all leads, the cron feature will not import any future new leads, you have to scan for latest leads manually occasionally using the scan limit feature. Although you can enable the cron based scanning again manually but be informed that it will rescan the full page conversation. If you are scanning for first time and your inbox conversation is moderate, then you can scan all of them at once. To get future new leads scan occasionally same as stated earlier.');?>" data-original-title="<?php echo $this->lang->line('Scan Latest Leads');?>"><i class="fa fa-info-circle"></i> </a>
                            </label>
                            <?php 
                            $scan_drop=
                            array
                            (
                              ''=>$this->lang->line("Scan all leads"),
                              "500"=>"500 ".$this->lang->line("Leads"),
                              "1000"=>"1000 ".$this->lang->line("Leads"),
                              "2000"=>"2000 ".$this->lang->line("Leads"),
                              "3000"=>"3000 ".$this->lang->line("Leads"),
                              "5000"=>"5000 ".$this->lang->line("Leads"),
                              "10000"=>"10000 ".$this->lang->line("Leads"),
                              "20000"=>"20000 ".$this->lang->line("Leads"),
                              "30000"=>"30000 ".$this->lang->line("Leads"),
                              "50000"=>"50000 ".$this->lang->line("Leads"),
                              "100000"=>"100000 ".$this->lang->line("Leads")
                            );
                            echo form_dropdown('lead_limit',$scan_drop, '','class="form-control" id="scan_limit"'); ?>
                          </div>
                      </div>                      
                    </div>
                </div>               
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line("Close"); ?></button>
              <button type="button" class="btn btn-primary"  id="start_scanning"><i class="fa fa-check-circle"></i> <?php echo $this->lang->line("Start Scanning"); ?></button>
          </div>

        </div>
    </div>
</div>



<div class="modal fade" id="migrate_leadTO_bot_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-check"></i> <?php echo $this->lang->line("Migrating Leads");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="response_div_01" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="download_lead_list_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa  fa-cloud-download"></i> <?php echo $this->lang->line("Download lead list");?></h4>
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
                      <div id="download_lead_list_div">
                        
                      </div>
                      
                    </div>
                  </div>
                <!-- </div>  -->
              </div>              
            </div>
        </div>
    </div>
</div>
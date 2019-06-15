<style>

hr{
   margin-top: 10px;
}

.custom-top-margin{
  margin-top: 20px;
}

.sync_page_style{
   margin-top: 20px;
   margin-bottom: 15px;
}
.wrapper,.content-wrapper{background: #fafafa !important;}
  .well{background: #fff;}
  .box{min-height: 320px}
</style>

<br/>
<?php if(empty($page_info)){ ?>
<div class="">
  <div class="col-xs-12">       
    <div class="well">
      <h4 class="text-center"> <i class="fa fa-facebook-official"></i> You have no page in facebook<h4>
    </div>
  </div>
</div>
<?php }else{ ?>
<div class="">
  <div class="col-xs-12">       
    <div class="well">
      <h4 class="text-center"> <i class="fa fa-facebook-official"></i> Conversation : Page List<h4>
    </div>
  </div>
</div>
  <?php $i=0; foreach($page_info as $value) : ?>
    <div class="col-xs-12 col-sm-12 col-md-6">
      <div class="box box-primary box-solid">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-facebook"></i> <?php echo $value['page_name']; ?></h3>
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
                <img src="<?php echo $profile_picture;?>" alt="" class='img-circle custom-top-margin' style='padding:2px;border:1px solid #ccc;' height="80" width="80">
              </div>
              <div class="col-xs-12 col-md-8">
                <br/>
                <div class="info-box" style="border:1px solid #ccc;border-bottom:2px solid #ccc;">
                  <span class="info-box-icon bg-blue"><i class="fa fa-envelope"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text"><?php echo $this->lang->line("Total Inbox");?></span><hr size="1">
                    <span class="info-box-number"><?php 
                      if(empty($countnumber[$i]['countnumber'])){
                        echo "0";
                      }else{
                        echo number_format($countnumber[$i]['countnumber']);
                      }  ?>
                    </span>
                  </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->

                <div class="sync_page_style" style="">
                  <span class="info-box-text">
                    <button id ="<?php echo $value['id'];?>" type="button" class="btn-sm btn btn-primary import_data"><i class="fa fa-retweet"></i> Sync page inbox</button>
                    <?php 
                        if(!empty($countnumber[$i]['countnumber'])){
                           echo "<button id ='".$value['user_id']."-".$value['page_id']."' type='button' class='btn-sm user_details_modal btn btn-info'><i class='fa fa-user'></i> User details</button>";
                     }?>
                  </span>
                </div><!-- /.info-box -->
              </div>                  
            </div><!-- /.row -->
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
  <?php $i++; endforeach; ?>
</div>
<?php } ?>

<script type="text/javascript">

  $j(document.body).on('click','.import_data',function(){
    var base_url = '<?php echo site_url();?>';
    var id=$(this).attr('id');
    var alert_id = "alert_"+id;
    // $("#"+alert_id).hide();
    var  loading = '<img src="'+base_url+'assets/pre-loader/custom.gif" class="center-block">';
    
    $("#"+alert_id).removeClass("alert-success");
    $("#"+alert_id).show().html(loading);
    $.ajax({
      type:'POST' ,
      url:"<?php echo site_url();?>facebook_rx_conversion/conversion_action",
      data:{id:id},
      success:function(response){
       $("#"+alert_id).addClass("alert-success");
       $("#"+alert_id).show().html(response);    
      }
    });

  });

  $j(document.body).on('click','.user_details_modal',function(){
    var user_page_id = $(this).attr('id');
    var base_url = '<?php echo site_url();?>';
    $("#response_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/custom_lg.gif" alt="Processing..."><br/>');
    $("#htm").modal(); 
    $.ajax({
      type:'POST' ,
      url:"<?php echo site_url();?>facebook_rx_conversion/conversion_user_details_modal",
      data:{user_page_id:user_page_id},
      success:function(response){ 
         $('#response_div').html(response);  
      }
    });

  });

   $(document.body).on('click','.client_thread_subscribe_unsubscribe',function(){
    $(this).html('please wait...');
    var client_subscribe_unsubscribe_status = $(this).attr('id');
    var explode=[];
    explode=client_subscribe_unsubscribe_status.split('-');
    var id=explode[0];
    var page_id=explode[1];
    var thread_id=explode[2];
    var permission=explode[3];
    var new_id="";
    var new_class="";
    var new_text="";

    if(permission=='1')
    {
      new_id = id+"-"+page_id+"-"+thread_id+"-0";
      new_text='subscribe';
      new_class='success';
    }
    else if(permission=='0')
    {
      new_id = id+"-"+page_id+"-"+thread_id+"-1";
      new_text='unsubscribe';
      new_class='danger';
    }


    var status_button = "<button id ='"+new_id+"' type='button' class='client_thread_subscribe_unsubscribe btn btn-"+new_class+"'>"+new_text+"</button>";
    $(this).parent().html(status_button);

    $.ajax({
      type:'POST' ,
      dataType:'JSON',
      url:"<?php echo site_url();?>facebook_rx_conversion/client_subscribe_unsubscribe_status_change",
      data:{client_subscribe_unsubscribe_status:client_subscribe_unsubscribe_status},
      success:function(response){ 
         // alert(response.res_class); 
         // alert(response.res_id); 
         // alert(response.res_text); 
      }
    });

  });
</script>
<div class="modal fade" id="htm" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Page all user</h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="response_div" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>
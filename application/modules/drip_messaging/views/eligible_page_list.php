<style>

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
  }
  .small-box:hover {
    text-decoration: none;
    color: #777;
  }
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
  .box-body{padding-top:10px !important;}
  .inner p,.inner h2{color:<?php echo $THEMECOLORCODE; ?>;}
</style>

<?php if(empty($page_info)){ ?> 
  <div class="well well_border_left">
    <h4 class="text-center"> <i class="fa fa-facebook-official"></i> <?php echo $this->lang->line("you do not have any bot enabled page");?></h4>
  </div>
<?php }else{ ?>      
  <div class="well well_border_left">
    <h4 class="text-center"> <i class="fa fa-cogs"></i> <?php echo $this->lang->line("Drip Messaging Setup");?></h4>
  </div>

  <div class="row" style="padding:15px 30px 5px 30px;">
  <?php $i=0; foreach($page_info as $value) : ?>
    <div class="col-xs-12 col-sm-12 col-md-6" id="box_<?php echo $value['id'];?>">
      <div class="box box-shadow box-solid">
        <div class="box-header with-border text-center">
          <h3 class="box-title"><a href="https://facebook.com/<?php echo $value['page_id'];?>" target="_BLANK"><i class="fa fa-newspaper"></i> <?php echo $value['page_name']; ?></a></h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
          <div class="col-xs-12">

            <div class="row">
              <div id="alert_<?php echo $value['id'];?>" class="alert alert-success text-center" style="display:none;"></div>
              <?php $profile_picture=$value['page_profile']; ?>           
              <?php $classvals='default'; ?>
              <?php if($value['review_status'] =='PENDING') $classvals='primary'; ?>
              <?php if($value['review_status'] =='REJECTED')$classvals='danger'; ?>
              <?php if($value['review_status'] =='APPROVED')$classvals='success'; ?>
              <?php if($value['review_status'] =='LIMITED') $classvals='warning'; ?>
              <div class="col-xs-12 col-md-12" style="margin-bottom: 10px;">
                  <ul class="products-list product-list-in-box">
                    <li class="item">
                      <div class="product-img">
                        <img src="<?php echo $profile_picture; ?>" class="img-circle border_gray" style="width:80px;height: 80px;">
                      </div>
                      <div class="product-info" style="margin-left: 100px;">
                        <a href="https://facebook.com/<?php echo $value['page_id'];?>" target="_BLANK" class="product-title"><?php echo $value['page_name']; ?>
                          <span class="put_status label label-<?php echo $classvals;?> pull-right"><?php echo $value['review_status'];?></span>
                        </a>
                        <span class="product-description">
                            <?php 
                              // echo $this->lang->line("App Review Status")." : <b class='put_status'>".$value['review_status']."</b>"; 
                              echo $this->lang->line("Last Checked");?> : <span class="put_last_scan"><?php if( $value['review_status_last_checked']=='0000-00-00 00:00:00') echo $this->lang->line("Never"); else echo date("d M Y H:i",strtotime($value['review_status_last_checked'])); 
                              echo "</span><br><button style='margin-top:10px;' data-id='".$value['id']."'  type='button' class='check_review_status_class btn btn-outline-info btn-sm' id='check_review_status_".$value['id']."'><i class='fa fa-check-circle'></i> ".$this->lang->line("Check Review Status")."</button>"; 
                            ?>
                        </span>
                      </div>
                    </li>
                  </ul>
                 
              </div>
              
              <div class="col-xs-12 col-md-12">
                <div class="small-box box-shadow" style="border:1px solid <?php echo $THEMECOLORCODE; ?>;">
                  <div class="inner" style="padding:0 20px;">
                    <h2 style="margin:10px 0;"><?php echo isset($sub_count[$value['page_id']]) ? $sub_count[$value['page_id']] : 0;?></h2>
                    <p><?php echo $this->lang->line("Bot Subscribers");?></p>
                  </div>
                  <div class="icon" style="font-size:50px;margin-top:20px;">
                    <i class="fa fa-group" style="cursor: auto !important;color:<?php echo $THEMECOLORCODE; ?>"></i>
                  </div>               
                  <a href="" data-id="<?php echo $value['page_id']?>" class="user_details_modal_bot small-box-footer" style="background: <?php echo $THEMECOLORCODE; ?>"><i class="fa fa-hand-point-right"></i> <?php echo $this->lang->line("Subscriber List"); ?> </a>
                </div>             
              </div>  
              <div class="col-xs-12">
                <a class="pull-left btn btn-outline-primary btn-sm <?php if($value['review_status']!='APPROVED') echo 'disabled'; ?>" href='<?php echo base_url("drip_messaging/campaign_list/".$value['id']);?>' target="_BLANK"><i class="fa fa-tint"></i> <?php echo $this->lang->line("Drip Message Campaign"); ?></a>            
                <a target="_BLANK" class="pull-right btn btn-default btn-sm border_gray show_report" href='<?php echo base_url("drip_messaging/page_messaging_report/".$value['id']);?>' data-id="<?php echo $value['id'];?>"><i class="fa fa-history"></i> <?php echo $this->lang->line("Message Sent Log"); ?></a>  
              </div>                
            </div><!-- /.row -->
           
          </div>
        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
  <?php   
      $i++;
      if($i%2 == 0)
      echo "</div><div class='row' style='padding:5px 30px;'>";
      endforeach;
  ?>
</div>
<?php } ?>

<?php     
  $successfully = $this->lang->line("Estimation was run successfully");
  $waiting = $this->lang->line("Please wait 20 seconds");
  $estimate_now = $this->lang->line("Estimate Quick Send Reach");
?>
<link href="<?php echo base_url('plugins/select_search/select2.css')?>" rel="stylesheet"/>
<script src="<?php echo base_url('plugins/select_search/select2.js')?>"></script>
<script type="text/javascript">

    var base_url = '<?php echo site_url();?>';

    $j(document.body).on('click','.redirect_to_url',function(e){
      e.preventDefault();
      var href = $(this).attr('data-href'); 
      if(href=="")
      alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $this->lang->line('Please check review status first and then refresh the page to make this settings link work.');?>",function(){});
      else window.open(href);


    });  

    $j(document.body).on('click','.user_details_modal_bot',function(e){
      e.preventDefault();
      var auto_id = $(this).attr('data-id'); // fb page id
      $("#response_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
      $("#htm").modal(); 
      $.ajax({
        type:'POST' ,
        url:"<?php echo site_url();?>drip_messaging/user_details_modal_bot",
        data:{auto_id:auto_id},
        success:function(response){ 
           $('#response_div').html(response);  
        }
      });
    }); 


  $j(document.body).on('click','.check_review_status_class',function(e){
    var idval=$(this).attr('id');
    var auto_id = $(this).attr('data-id');
    $("#"+idval).addClass("disabled");
    if(auto_id=="") return false;
    $.ajax({
      type:'POST',
      url:"<?php echo site_url();?>drip_messaging/check_review_status",
      data:{auto_id:auto_id}, // database id
      dataType:'json',
      success:function(response)
      {  
        if(response.status=="0") 
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.message,function(){});
        else 
        {                    
          $("#box_"+auto_id+" .put_status").html(response.success);
          var classvals='default';
          if(response.success =='PENDING') classvals='primary';
          if(response.success =='REJECTED')classvals='danger';
          if(response.success =='APPROVED')classvals='success';
          if(response.success =='LIMITED') classvals='warning';
          $("#box_"+auto_id+" span.put_status").attr('class','put_status label label-'+classvals+' pull-right');
          $("#box_"+auto_id+" .put_last_scan").html("<?php echo $this->lang->line("just now"); ?>");
        }
        
         $("#"+idval).removeClass("disabled");;         
      }
    });

  });

  $j(document.body).on('click','.assign_campaign',function(e){
    var subscribe_auto_id = $(this).attr('data-id');
    if(subscribe_auto_id=="") return false;
    $("#assign_campaign_status").attr('class','').html('');
    $("#assign_campaign_modal").addClass('modal').modal();
    $.ajax({
      type:'POST',
      url:"<?php echo site_url();?>drip_messaging/assign_campaign_form",
      data:{subscribe_auto_id:subscribe_auto_id}, // database id
      success:function(response)
      {                            
          $("#assign_campaign_response_div").html(response);                
      }
    });

  });

  $j(document.body).on('click','#assign_confirm',function(e){
    var assign_campaign_id=$("#assign_campaign_id").val();
    var hidden_subscriberauto_id=$("#hidden_subscriberauto_id").val();
    var ans=confirm("<?php echo $this->lang->line('are you sure');?>");
    $("#assign_campaign_status").attr('class','').html('');
    $("#assign_confirm").addClass('disabled');
    if(!ans) return false;
    $.ajax({
      type:'POST',
      url:"<?php echo site_url();?>drip_messaging/assign_confirm",
      data:{assign_campaign_id:assign_campaign_id,hidden_subscriberauto_id:hidden_subscriberauto_id},
      dataType:'json',
      success:function(response)
      {  
        if(response.status=="0") 
        {
          $("#assign_campaign_status").attr('class','alert alert-danger text-center').html(response.message);
        }
        else 
        {                    
          // $("#assign_campaign_status").attr('class','alert alert-success text-center').html(response.message);    
          alertify.alert('<?php echo $this->lang->line("Alert"); ?>',response.message,function(){});
          location.reload();    
        }        
        $("#assign_confirm").removeClass('disabled');        
      }
    });

  });

  // $('#assign_campaign_modal').on('hidden.bs.modal', function () { 
  //   location.reload();
  // });

</script>



<div class="modal fade" id="htm" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="min-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user"></i> <?php echo $this->lang->line("Subscriber List");?></h4>
            </div>
            <div class="modal-body ">
                <div class="row">
                    <div class="col-xs-12 table-responsive" id="response_div" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>

<div class="fade" id="assign_campaign_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> <?php echo $this->lang->line("Assign drip campaign");?></h4>
            </div>
            <div class="modal-body">        
                <div class="row">

                    <div id="assign_campaign_status"></div>
                    <div class="col-xs-12 table-responsive" id="assign_campaign_response_div" style="padding: 20px;"></div>
                </div>               
            </div>
        </div>
    </div>
</div>
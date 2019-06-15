<?php $is_demo=$this->is_demo;?>
<div class="container-fluid">
  <div class="row">
    <div class='well well_border_left text-center' style="border-radius: 0;background: #fff;margin:0">
      <h4><i class='fa fa-plug'></i> <?php echo $this->lang->line("add-ons"); ?></h4> 
      <h4 style="margin-top:25px;"><a href="<?php echo base_url('addons/upload');?>" class="btn btn-outline-primary"><i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('upload new add-on');?></a></h4>
    </div>
    <?php if($this->session->flashdata('addon_uplod_success')!="") echo "<div class='alert alert-success text-center'><i class='fa fa-check'></i> ".$this->session->flashdata('addon_uplod_success')."</div>";?>
    <br>
    <?php 
    if(!empty($add_on_list))
    {       
      $i=0;
      foreach($add_on_list as $value)
      {
        $i++;
        //(removing .php from controller name, that makes moduleFolder/controller name)
        $module_controller=str_replace('.php','',strtolower($value['controller_name']));
        ?>
        <div class="col-xs-12 col-sm-12 col-md-6">
          <div class="box box-primary" style='border-bottom:4px solid <?php echo $THEMECOLORCODE ?>;margin-top:10px;'>
            <div class="box-header ui-sortable-handle" style="cursor: move;">
              <i class="fa fa-plug"></i>
              <h3 class="box-title" title="<?php echo $value['addon_name'];?>" style="font-size:14px;">
                <a style="color:#fff;" href="<?php echo $value['addon_uri'];?>" target="_BLANK"><?php echo substr($value['addon_name'], 0, 35)." v".$value["version"];?></a>
              </h3>
              <div class="box-tools pull-right">
                <?php 
                if($value['installed']=="0") echo "<span class='label label-light pull-right' style='margin-top:15px;'><i class='red fa fa-ban'></i> ".$this->lang->line("inactive")."</span>";
                else echo "<span class='label label-light pull-right' style='margin-top:15px;'><i class='green fa fa-check'></i> ".$this->lang->line("active")."</span>"; 
                ?> 
              </div>
            </div>
            <div class="box-body" style="height: 180px;overflow-y: auto;">


              <div class="clearfix"></div>
              <?php 
              // making asset path : moduleFolder/thumb.php
              $asset_path=$module_controller.'/thumb.png'; 
              $thumb = get_addon_asset($type="image",$asset_path,$css_class="img-thumbnail","",$style="height:120px;width:120px"); 
              if($thumb=="")
              {
                $left_col="hidden";
                $right_col="col-xs-12";
              }
              else
              {
                $left_col="col-xs-3";
                $right_col="col-xs-9";
              }
              ?>
              <div class="<?php echo $left_col; ?>" style="padding:5px;"> 
                <?php echo $thumb;?>                
              </div>
              <div class="<?php echo $right_col; ?>" style="padding:5px;">
                <ul class="todo-list ui-sortable">
                  <li>
                    <span class="handle ui-sortable-handle">
                      <i class="fa fa-circle-o blue"></i>
                    </span>
                    <span class=""><b><?php echo $this->lang->line("name");?></b> : <span id="get_add_on_title_<?php echo $i;?>"><?php echo $value['addon_name'];?></span></span>                    
                  </li>
                  <li>
                    <span class="handle ui-sortable-handle">
                      <i class="fa fa-circle-o blue"></i>
                    </span>
                    <span class=""><b><?php echo $this->lang->line("version");?></b> : <?php echo $value['version'];?></span>                    
                  </li>
                  <li>
                    <span class="handle ui-sortable-handle">
                      <i class="fa fa-circle-o blue"></i>
                    </span>
                    <span class=""><b><?php echo $this->lang->line("author");?></b> : <a target="_BLANK" href="<?php echo $value['author_uri'];?>"><?php echo $value['author'];?></a></span>                    
                  </li>

                </ul>
              </div>

              <div class="clearfix"></div>

            </div>

            <div class="box-footer clearfix text-center">             

              <?php if($value['installed'] == '0'): ?>
                <a title="<?php echo $this->lang->line("activate"); ?>" class="btn btn-outline-success pull-left activate_action" data-i='<?php echo $i; ?>' data-href="<?php echo $module_controller.'/activate';?>"><i class="fa fa-check"></i> <?php echo $this->lang->line('activate');?></a>
              <?php endif; ?>

              <?php if($value['installed'] == '1'): ?>
                <a title="<?php echo $this->lang->line("deactivate"); ?>" class="<?php if($this->is_demo=='1') echo 'disabled'; ?> btn btn-outline-primary pull-left deactivate_action" data-i='<?php echo $i; ?>' data-href="<?php echo $module_controller.'/deactivate';?>"><i class="fa fa-ban"></i> <?php echo $this->lang->line('deactivate');?></a>
              <?php endif; ?>
              <a title="<?php echo $this->lang->line("delete"); ?>" class="<?php if($this->is_demo=='1') echo 'disabled'; ?> btn btn-outline-danger pull-right delete_action" data-i='<?php echo $i; ?>' data-href="<?php echo $module_controller.'/delete';?>"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete');?></a>
            </div>
          </div>
        </div>            
        <?php 
      }
    }
    else echo "<h4><div class='well text-center'><i class='fa fa-remove'></i> ".$this->lang->line("no data found")."</div></h4>";
    ?>   
  </div>
</div>

    



<script>
  var base_url = "<?php echo base_url(); ?>";
  var is_demo = "<?php echo $is_demo; ?>";
  $j("document").ready(function(){

    $('[data-toggle="popover"]').popover(); 
    $('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;}); 

    $(".activate_action").click(function(){ 
       var action = $(this).attr('data-href');
       var datai = $(this).attr('data-i');
       $("#href-action").val(action);      
       $(".put_add_on_title").html($("#get_add_on_title_"+datai).html());       
       $("#activate_action_modal_refesh").val('0');      
       $("#activate_action_modal").modal();       
    });

    $('#activate_action_modal').on('hidden.bs.modal', function () { 
      if($("#activate_action_modal_refesh").val()=="1")
      location.reload(); 
    })

    $("#activate_submit").click(function(){    
      if(is_demo=='1') 
      {
         alertify.alert('<?php echo $this->lang->line("Alert");?>','Permission denied',function(){ });
         return false;
      }        
       var action = base_url+$("#href-action").val();
       var purchase_code=$("#purchase_code").val(); 

       $("#activate_submit").addClass('disabled');
       $("#activate_action_modal_msg").removeClass('alert').removeClass('alert-success').removeClass('alert-danger');
       var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
       $("#activate_action_modal_msg").html(loading);

       $.ajax({
             type:'POST' ,
             url: action,
             data:{purchase_code:purchase_code},
             dataType:'JSON',
             success:function(response)
             {
                $("#activate_action_modal_msg").removeClass('alert').removeClass('alert-success').removeClass('alert-danger');
                // $("#activate_submit").removeClass('disabled');
                $("#activate_action_modal_refesh").val('1'); 
                if(response.status == '1')
                {
                  $("#activate_action_modal_msg").addClass('alert alert-success');
                }
                else
                {
                  $("#activate_action_modal_msg").addClass('alert alert-danger');
                }
                $("#activate_action_modal_msg").html(response.message);
             }
         });        
    });


    $(".deactivate_action").click(function(){ 
       var action = $(this).attr('data-href');
       var datai = $(this).attr('data-i');
       $("#deactivate-href-action").val(action);      
       $(".put_add_on_title").html($("#get_add_on_title_"+datai).html());      
       $("#deactivate_action_modal_refesh").val('0');    
       $("#deactive_confirm").show();  
       $("#deactivate_action_modal").modal();       
    });

    $('#deactivate_action_modal').on('hidden.bs.modal', function () { 
      if($("#deactivate_action_modal_refesh").val()=="1")
      location.reload(); 
    })

    $("#deactivate_submit").click(function(){            
       if(is_demo=='1') 
       {
         alertify.alert('<?php echo $this->lang->line("Alert");?>','Permission denied',function(){ });
         return false;
       }   
       var action = base_url+$("#deactivate-href-action").val();
       $("#deactivate_submit").addClass('disabled');
       $("#deactivate_action_modal_msg").removeClass('alert').removeClass('alert-success').removeClass('alert-danger');
       var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
       $("#deactivate_action_modal_msg").html(loading);
       $("#deactive_confirm").show();  
       $.ajax({
             type:'POST' ,
             url: action,
             dataType:'JSON',
             success:function(response)
             {
                $("#deactivate_action_modal_msg").removeClass('alert').removeClass('alert-success').removeClass('alert-danger');
                // $("#deactivate_submit").removeClass('disabled');
                $("#deactivate_action_modal_refesh").val('1'); 
                if(response.status == '1')
                {
                  $("#deactivate_action_modal_msg").addClass('alert alert-success');
                }
                else
                {
                  $("#deactivate_action_modal_msg").addClass('alert alert-danger');
                }
                $("#deactivate_action_modal_msg").html(response.message);
             }
         });        
    });

    $(".delete_action").click(function(){ 
       var action = $(this).attr('data-href');
       var datai = $(this).attr('data-i');
       $("#delete-href-action").val(action);      
       $(".put_add_on_title").html($("#get_add_on_title_"+datai).html());      
       $("#delete_action_modal_refesh").val('0');    
       $("#delete_confirm").show();  
       $("#delete_action_modal").modal();       
    });

    $('#delete_action_modal').on('hidden.bs.modal', function () { 
      if($("#delete_action_modal_refesh").val()=="1")
      location.reload(); 
    })

    $("#delete_submit").click(function(){            
       if(is_demo=='1') 
       {
         alertify.alert('<?php echo $this->lang->line("Alert");?>','Permission denied',function(){ });
         return false;
       }   
       var action = base_url+$("#delete-href-action").val();
       $("#delete_submit").addClass('disabled');
       $("#delete_action_modal_msg").removeClass('alert').removeClass('alert-success').removeClass('alert-danger');
       var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
       $("#delete_action_modal_msg").html(loading);
       $("#delete_confirm").show();  
       $.ajax({
             type:'POST' ,
             url: action,
             dataType:'JSON',
             success:function(response)
             {
                $("#delete_action_modal_msg").removeClass('alert').removeClass('alert-success').removeClass('alert-danger');
                // $("#delete_submit").removeClass('disabled');
                $("#delete_action_modal_refesh").val('1'); 
                if(response.status == '1')
                {
                  $("#delete_action_modal_msg").addClass('alert alert-success');
                }
                else
                {
                  $("#delete_action_modal_msg").addClass('alert alert-danger');
                }
                $("#delete_action_modal_msg").html(response.message);
             }
         });        
    });

  });
</script>

<div class="modal fade" id="activate_action_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-check"></i> <?php echo $this->lang->line("activate");?> : <span class="put_add_on_title"></span></h4>
      </div>
      <div class="modal-body">
        <div id="activate_action_modal_msg" class="text-center"></div>
        <div class="form-group">
          <label>
            <?php echo $this->lang->line("add-on purchase code");?>
            <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('put the add-on purchase code here to activate. keep it blank and submit if the add-on is free.');?>" data-original-title="<?php echo $this->lang->line("purchase code");?>"><i class="fa fa-info-circle"></i> </a>
          </label>
          <input type="text" class="form-control" placeholder="<?php echo $this->lang->line('keep it blank for free add-on');?>" name="purchase_code" id="purchase_code" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=&quot;); cursor: auto;">
          <input type="hidden" id="href-action" value="">
          <input type="hidden" id="activate_action_modal_refesh" value="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-lg pull-left" data-dismiss="modal"><?php echo $this->lang->line("close");?></button>
        <a class="btn btn-primary btn-lg" id="activate_submit"><i class="fa fa-check"></i> <?php echo $this->lang->line("activate");?></a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deactivate_action_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-ban"></i> <?php echo $this->lang->line("deactivate");?> : <span class="put_add_on_title"></span></h4>
      </div>
      <div class="modal-body">
        <div id="deactivate_action_modal_msg" class="text-center"></div>
        <div class="text-center">     
          <h4 id="deactive_confirm" style="line-height: 25px;"><?php echo $this->lang->line('are you sure that you want to deactive this add-on?');?><br/><?php echo $this->lang->line('your add-on data will still remain.');?></h4>                 
          <input type="hidden" id="deactivate-href-action" value="">
          <input type="hidden" id="deactivate_action_modal_refesh" value="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-lg pull-left" data-dismiss="modal"><?php echo $this->lang->line("close");?></button>
        <a class="btn btn-primary btn-lg" id="deactivate_submit"><i class="fa fa-ban"></i> <?php echo $this->lang->line("deactivate");?></a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="delete_action_modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="fa fa-trash"></i> <?php echo $this->lang->line("delete");?> : <span class="put_add_on_title"></span></h4>
      </div>
      <div class="modal-body">
        <div id="delete_action_modal_msg" class="text-center"></div>
        <div class="text-center">     
          <h4 id="delete_confirm" style="line-height: 25px;"><?php echo $this->lang->line('are you sure that you want to delete this add-on?');?><br/><?php echo $this->lang->line('this process can not be undone.');?></h4>                 
          <input type="hidden" id="delete-href-action" value="">
          <input type="hidden" id="delete_action_modal_refesh" value="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-lg pull-left" data-dismiss="modal"><?php echo $this->lang->line("close");?></button>
        <a class="btn btn-danger btn-lg" id="delete_submit"><i class="fa fa-trash"></i> <?php echo $this->lang->line("delete");?></a>
      </div>
    </div>
  </div>
</div>



<style>
  .full {
    width: 100%;
    float: left;
    margin: 0;
    padding: 0;
  }
  .short-info {
    border: 1px solid #e8edef;
  }

  .short-info p {
    display: inline-block;
    width: 48%;
    border-right: 1px solid #e8edef;
    font-size: 11px;
    margin: 0;
    text-align: center;
    padding: 10px 0;
    line-height: 26px;
  }


  .short-info p:last-child {
    border-right: 0px solid #e8edef;
  } 

  .short-info p span {
    font-weight: bold;
    font-size: 25px;
  } 
  .custom_progress {
    height: 2px;
    margin-top: 0px;
    margin-bottom: 10px;
    overflow: hidden;
    background-color: #f5f5f5;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
  }
  .custom_progress_bar {
    float: left;
    width: 0;
    height: 100%;
    font-size: 4px;
    line-height: 6px;
    color: #fff;
    text-align: center;
    background-color: #337ab7;
    -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
    -webkit-transition: width .6s ease;
    -o-transition: width .6s ease;
    transition: width .6s ease;
  }
  .existing_account {
    margin: 10px 0 0;
    font-size: 16px;
    font-weight: bold;
    font-style: italic;
  }
  .account_list{
    padding-left: 5%;
  }
  .individual_account_name{
    font-weight: bold;
    font-size: 14px;
  }
  .padded_ul{
    padding-left: 10%;
  }
</style>


<style>
  .margin_top{
    margin-top:20px;
  }

  .padding{
    padding:15px;
  } 

  .count_text{
    margin: 0px;
    padding: 0px;
    color: orange;
  }
  .cover_image{
    height: 200px;
    width: 200px;
  }

  h4.pagination_link
  {
    font-size: 12px;
    text-align: center;
    font-weight: normal;
    margin-top: 12px;
  }

  h4.pagination_link a
  {
    padding: 4px 7px 4px 7px;
    background: #238db6;
    color:#fff;
    border:1px solid #238db6;
    font-style: normal;
    text-decoration: none;
  }

  h4.pagination_link strong
  {
    padding: 4px 7px 4px 7px;
    background: #E95903;
    color:#fff;
    border:1px solid #E95903;
    font-style: normal;
  }

  h4.pagination_link a:hover
  {
    background: #77a2cc;
    border:1px solid #77a2cc; 
    color: #fff;
  }

</style>

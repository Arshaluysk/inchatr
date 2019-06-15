<?php $this->load->view('admin/theme/message'); ?>
<?php 
$name= isset($profile_info[0]["name"]) ? $profile_info[0]["name"] : ""; 
$email= isset($profile_info[0]["email"]) ? $profile_info[0]["email"] : ""; 
$address= isset($profile_info[0]["address"]) ? $profile_info[0]["address"] : ""; 
$logo= isset($profile_info[0]["brand_logo"]) ? $profile_info[0]["brand_logo"] : ""; 
if($logo=="") $logo=file_exists("assets/images/avatar.png") ? base_url("assets/images/avatar.png") : "https://mysitespy.net/envato_image/avatar.png";
else $logo=base_url().'member/'.$logo;
?>
<section class="content-header">
   <section class="content">
     	<div class="box box-info">
		    	<div class="box-header">
		         <h3 class="box-title"><i class="fa fa-user-edit"></i> <?php echo $this->lang->line("user");?></h3>
		        </div><!-- /.box-header -->
		       		<!-- form start -->
		    <form class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url().'member/edit_profile_action';?>" method="POST">
		        <div class="box-body">
		           	<div class="form-group">
		              	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("name");?> *
		              	</label>
		                	<div class="col-sm-9 col-md-6 col-lg-6">
		               			<input name="name" value="<?php echo $name;?>"  class="form-control" type="text">		               
		             			<span class="red"><?php echo form_error('name'); ?></span>
		             		</div>
		            </div>
		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("email");?> *
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<input name="email" value="<?php echo $email;?>"  class="form-control" type="email">		          
	             			<span class="red"><?php echo form_error('email'); ?></span>
	             		</div>
		           </div> 
		          
		        
		            <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("address");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<textarea name="address" class="form-control"><?php echo $address;?></textarea>	          
	             			<span class="red"><?php echo form_error('address'); ?></span>
	             		</div>
		           </div> 

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("image");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6" >
		           			<div class='text-center'><img class="img-responsive img-circle center-block" style="height: 200px;width: 200px;" src="<?php echo $logo;?>" alt="<?php echo $this->lang->line("brand logo");?>"/></div>
	               			<?php echo $this->lang->line("Max Dimension");?> : 500 x 500, <?php echo $this->lang->line("Max Size");?> : 100KB,  <?php echo $this->lang->line("Allowed Format");?> : png
	               			<input name="logo" class="form-control" type="file">		          
	             			<span class="red"> <?php echo $this->session->userdata('logo_error'); $this->session->unset_userdata('logo_error'); ?></span>
	             		</div>
		           </div> 

		         		               
		           </div> <!-- /.box-body --> 

		           	<div class="box-footer">
		            	<div class="form-group">
		             		<div class="col-sm-12 text-center clearfix">
		              			
		              			<button name="submit" type="submit" class="btn btn-warning btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
	              				<button  type="button" class="btn btn-default btn-lg" onclick='goBack("facebook_ex_dashboard/index",1)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>

		              			<?php if($this->session->userdata('user_type') != 'Admin') : ?>
			              			<a class="delete_full_access pull-right btn btn-outline-danger btn-lg"><i class="fa fa-trash"></i> <?php echo $this->lang->line('Delete my account'); ?></a>
			              		<?php endif; ?>
		             		</div>
		           		</div>
		         	</div><!-- /.box-footer -->         
		        </div><!-- /.box-info -->       
		    </form>     
     	</div>
   </section>
</section>

<style type="text/css">
	.delete_full_access
	{
		cursor: pointer;
	}
</style>
<script type="text/javascript">
  $j(document.body).on('click','.delete_full_access',function(){
    $("#delete_dialog").modal(); 
  }); 

  $j(document.body).on('click','.cancel_button',function(){
  	$("#delete_dialog").modal('hide');
  });

  $j(document.body).on('click','.delete_confirm',function(){
  	$("#message_div").html('<img class="center-block" src="'+base_url+'assets/pre-loader/custom_lg.gif" alt="Processing..."><br/>');
    $("#delete_dialog").modal(); 
    $.ajax({
      type:'POST',
      url:"<?php echo site_url();?>home/delete_full_access",
      success:function(response){ 
      	if(response == 'success')
      	{
      		location.reload();
      	}
      	else
	        $('#message_div').html(response);  
      }
    });
  });



</script>

<div class="modal fade" id="delete_dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-warning"></i> <?php echo $this->lang->line("Warning !!");?></h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                      <p class="red" id="message_div"><?php echo $this->lang->line("If you delete your account, then everything will be deleted from our system. You will not be able to retrieve your information any more. Also you'll not be able to login here. Are you sure you want to delele your account?"); ?></p>
                    </div>
                </div>    
            </div>

            <div class="modal-footer text-center">
            	<button class="btn btn-sm btn-danger delete_confirm"><?php echo $this->lang->line('Delete my account'); ?></button>
            	<button class="btn btn-sm btn-default cancel_button"><?php echo $this->lang->line('Cancel'); ?></button>
            </div>

        </div>
    </div>
</div>




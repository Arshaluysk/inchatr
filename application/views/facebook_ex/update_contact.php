<section class="content-header">
	<section class="content">
		<div class="box box-info">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-edit"></i> <?php echo $this->lang->line('Edit Contact'); ?></h3>
			</div><!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" action="<?php echo site_url().'facebook_ex_import_lead/update_contact_action/'.$info['id']."/".$info['permission']."/".$display_subscribe;?>" enctype="multipart/form-data" method="POST">
				<div class="box-body">

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Name'); ?> *
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="client_username" value="<?php if(set_value('client_username'))echo set_value('client_username');else{if(isset($info['client_username']))echo $info['client_username'];}?>"  class="form-control" type="text">		          
							<span class="red"><?php echo form_error('client_username'); ?></span>
						</div>
					</div> 

						
					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Lead Group'); ?> 
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">							
							<?php if(isset($group_checkbox)) echo $group_checkbox; ?>
							<span class="red"><?php echo $this->session->flashdata("reset_success").'</span>';; ?></span>
						</div>
					</div>	

					<?php 
					if($display_subscribe=="1") 
					{ ?>
						<div class="form-group">
							<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Subscribed'); ?> *
							</label>
							<div class="col-sm-9 col-md-6 col-lg-6">	
								<?php echo form_dropdown('subscribed',array("1"=>"Yes","0"=>"No"),$info['permission'], 'class="form-control" id="subscribed"'); ?>	
								<span class="red"><?php echo form_error('subscribed'); ?></span>
							</div>
						</div>	
					<?php 
					} ?>					

		            </div> <!-- /.box-body --> 
			            <div class="box-footer">
			             	<div class="form-group">
			             		<div class="col-sm-12 text-center">
			             			<button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
	              					<button  type="button" class="btn btn-default btn-lg" onclick='goBack("facebook_ex_import_lead/contact_list",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
			             		</div>
			             	</div>
			            </div><!-- /.box-footer -->         
		         </div><!-- /.box-info -->       
		     </form>     
		 </div>
		</section>
	</section>


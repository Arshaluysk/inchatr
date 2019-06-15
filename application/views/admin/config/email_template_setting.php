<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
   		<div class="" id="modal-id">
   			<div class="modal-dialog" style="width: 100%;margin:0;">
   				<div class="modal-content">

   					<div class="modal-header">
   						<h4 class="modal-title"><i class="fa fa-id-card"></i> <?php echo $this->lang->line("Email Template Settings");?></h4>
   					</div>

   					<form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url('admin_config/email_template_settings_action');?>" method="POST">		     
				        <div class="modal-body">

				        	<!-- first row started(general and social settings) -->
				        	<div class="row">
				        		<?php 
				        			$i=0; 
				        			foreach ($emailTemplatetabledata as $value) :

									$fieldset = ucwords(str_replace('_',' ',$value['template_type'])).' Email';	
								?>

				        		<!-- signup activation email -->
				        		<div class="col-xs-12 col-md-6">
				        			<fieldset style="padding:30px; min-height: 220px;">
				        				<legend class="block_title"><i class="fa fa-folder-open"></i> <?php echo $this->lang->line($fieldset); ?></legend>

							           	<div class="form-group">
							             	<label for="<?php echo $value['template_type'].'-subject'; ?>" style="margin-top: -7px;"><i class="fa fa-bars"></i> <?php echo $this->lang->line('Subject');?> 

							             		<a data-toggle="popover" data-placement="top" title="Subject Info" data-content="<?php if(isset($popover_infos) && ($value['template_type']===$popover_infos[$i]['template_name'])) echo $popover_infos[$i]['subject']; ?>"><i class="fa fa-info-circle"></i></a>
							             	</label>

					               			<input name="<?php echo $value['template_type'].'-subject'; ?>" value='<?php if($value['subject']!='') echo $value['subject']; else echo $default_values[$i]['subject']; ?>' class="form-control" type="text" id="<?php echo $value['template_type'].'-subject'; ?>">			          
					             			<span class="red"><?php echo form_error($value['template_type'].'-subject'); ?></span>
							           </div>

							            <div class="form-group">
							             	<label for="<?php echo $value['template_type'].'-message' ?>"><i class="fa fa-envelope"></i> <?php echo $this->lang->line("Message");?> 
							             	<a data-toggle="popover" data-placement="bottom" title="Message Info" data-content="<?php if(isset($popover_infos) && ($value['template_type']===$popover_infos[$i]['template_name'])) echo $popover_infos[$i]['message']; ?>"><i class="fa fa-info-circle"></i></a>
							             </label>

					               			<textarea name="<?php echo $value['template_type'].'-message' ?>" id="<?php echo $value['template_type'].'-message' ?>" cols="30" rows="6" class="form-control"><?php if($value['message']!='') echo $value['message']; else echo $default_values[$i]['message']; ?></textarea>          
					             			<span class="red"><?php echo form_error($value['template_type'].'-message'); ?></span>
							            </div>	
							            <a href="<?php echo base_url()."admin_config/delete_email_template/".$value['template_type']; ?>" class="pull-right"><i class="fa fa-refresh"></i> Restore To Default</a>					           
				        			</fieldset>
				        		</div>

				        		<?php 
					        		$i++;
					        		if($i%2==0) echo "</div><br><div class='row'>";
									endforeach; 
								?>
				        	</div>
				        </div>
			        	<!-- end of .modal-body -->

	   					<div class="modal-footer" style="text-align:center;">
	   						<button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
		              		<button  type="button" class="btn btn-default btn-lg" onclick='goBack("admin_config/frontend_configuration",1)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
	   					</div>
   					</form>
   				</div>
   			</div>
   		</div>     	
   </section>
</section>


<script>
	$('[data-toggle="popover"]').popover();
	$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
</script>
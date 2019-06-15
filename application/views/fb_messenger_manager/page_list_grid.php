<?php $this->load->view('admin/theme/message'); ?>	
<!-- <div class="clearfix"></div> -->

<div class="" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog  modal-lg" style="width: 95%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center"><i class="fa fa-bullhorn"></i> <?php echo $this->lang->line("email notification settings");?></h4>
			</div>
			<div class="modal-body" id="enable_share_modal_body">                
				<form class="form-horizontal" method="POST" action="<?php echo base_url('fb_msg_manager/notification_settings'); ?>">

					<div class="form-group">
						<label class="control-label col-sm-5"><?php echo $this->lang->line("please select your time zone");?></label>
						<div class="col-sm-3">
							<?php 
							$time_zone = 'Europe/Dublin';
							if(!empty($settings_info))
							{	
								if($settings_info[0]['time_zone'] != '')		    		
									$time_zone = $settings_info[0]['time_zone'];
							}
							echo form_dropdown('time_zone',$time_zone_list,$time_zone,'class="form-control" id="time_zone"');
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-5"><?php echo $this->lang->line("do you have FB business manager account ?");?></label>
						<div class="col-sm-7">
							<?php 
							$check_business = 'no';
							if(!empty($settings_info))
							{					    		
								$check_business = $settings_info[0]['has_business_account'];
							}
							?>

							<div class="radio">
								<label><input type="radio" <?php if($check_business == 'yes') echo "checked"; ?> value="yes" name="has_business_account"><?php echo $this->lang->line("yes");?> <i>[https://business.facebook.com/]</i></label>
							</div>
							<div class="radio">
								<label><input type="radio" <?php if($check_business == 'no') echo 'checked'; ?> value="no" name="has_business_account"><?php echo $this->lang->line("no");?> <i>[https://www.facebook.com/]</i></label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-5"><?php echo $this->lang->line("do you want to get  email alert for unread messages ?");?></label>
						<div class="col-sm-7">
							<?php 
							$checking = 'no';
							if(!empty($settings_info))
							{					    		
								$checking = $settings_info[0]['is_enabled'];
							}
							?>
							<label class="radio-inline">
								<input type="radio" name="get_notification" value="yes" <?php if($checking == 'yes') echo "checked"; ?> ><?php echo $this->lang->line("yes");?>
							</label>
							<label class="radio-inline">
								<input type="radio" name="get_notification" value="no" <?php if($checking == 'no') echo 'checked'; ?> ><?php echo $this->lang->line("no");?>
							</label>
						</div>
					</div>

					<div id="second_part" style="display: none;">
						<div class="form-group">
							<label class="control-label col-sm-5"><?php echo $this->lang->line("time interval for getting email alert");?></label>
							<div class="col-sm-3">
								<?php 
								$option_array = array(
									'60' => '1 hour',
									'90' => '1.5 hours',
									'120' => '2 hours',
									'150' => '2.5 hours',
									'180' => '3 hours',
									'210' => '3.5 hours',
									'240' => '4 hours'
									);
								$selected_option = '60';
								if(!empty($settings_info))
								{					    		
									$selected_option = $settings_info[0]['time_interval'];
								}

								echo form_dropdown('time_interval',$option_array,$selected_option,'class="form-control"');
								?>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-sm-5"><?php echo $this->lang->line("email address to which alert will be sent");?></label>
							<div class="col-sm-5">
								<input type="email" class="form-control" name="email_address" value="<?php if(!empty($settings_info)) echo $settings_info[0]['email_address']; ?>">
							</div>
						</div>

					</div>
					<hr>
					<div class="form-group"> 
						<div class="col-sm-offset-5 col-sm-7">
							<button type="submit" class="btn btn-outline-primary btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>


<div class="row" style="padding: 10px 30px;">		
	<div class="col-xs-12">		
		<div class="row">
			<?php foreach($page_list as $value) : ?>
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">				
					<div class="info-box" style="">
						<span class="info-box-icon"><?php echo $value['page_profile']; ?></span>
						<div class="info-box-content">
							<span class="info-box-text" style="font-weight: 600;"><?php echo $value['page_name'];;?></span>
							<span class="info-box-number" style="margin-top: 15px;"><?php echo $value['msg_manager']; ?></span>
						</div><!-- /.info-box-content -->
					</div><!-- /.info-box -->
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<?php
	$doyouwantto = $this->lang->line("Do you want to");
	$messengermanagerforthispage = $this->lang->line("messenger manager for this page ?");
?>

<script>
	if($("input[name=get_notification]:checked").val()=="yes")
		$("#second_part").show();
	
	$(document.body).on('change','input[name=get_notification]',function(){    
    	if($("input[name=get_notification]:checked").val()=="yes")
	    	$("#second_part").show();
    	else 
    		$("#second_part").hide();
    });
</script>


<style>
	.info-box-icon {
	    border-top-left-radius: 2px;
	    border-top-right-radius: 0;
	    border-bottom-right-radius: 0;
	    border-bottom-left-radius: 2px;
	    display: block;
	    float: left;
	    height: 90px;
	    width: 90px;
	    text-align: center;
	    margin: 0 auto;
	    font-size: 12px;
	    line-height: 90px;
	    background: rgba(0,0,0,0.2);
	}

	.info-box {
	    display: block;
	    min-height: 90px;
	    background: #fff;
	    width: 100%;
	    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
	    border-radius: 2px;
	    margin-bottom: 15px;
	}
</style>


<script>
	$j("document").ready(function(){
		
		var base_url="<?php echo base_url(); ?>";
		
		$(document.body).on('click','.action',function(){
			var table_id = $(this).attr('table_id');
			var action = $(this).attr('action');
			var doyouwantto = "<?php echo $doyouwantto;?>";
			var messengermanagerforthispage = "<?php echo $messengermanagerforthispage;?>";
			var str = doyouwantto+" "+action+" "+messengermanagerforthispage;
	
			alertify.confirm('<?php echo $this->lang->line("are you sure");?>',str, 
			  function(){ 
			    $.ajax({
					url:base_url+'fb_msg_manager/enable_disable_messenger_manager',
					type:'POST',
					data:{table_id:table_id,action:action},
					success:function(response){
						location.reload();					
					}
					
				});
			  },
			  function(){     
			  });
					
			
		});
		
	});
	
	
</script>
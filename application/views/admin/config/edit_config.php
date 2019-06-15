<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
   		<div class="" id="modal-id">
   			<div class="modal-dialog" style="width: 100%;margin:0;">
   				<div class="modal-content">
   					<div class="modal-header">
   						<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
   						<h4 class="modal-title"><i class="fa fa-cogs"></i> <?php echo $this->lang->line("general settings");?></h4>
   					</div>
   					<form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url().'admin_config/edit_config';?>" method="POST">		     
			        <div class="modal-body">
			        	<div class="row">
			        		<div class="col-xs-12 col-md-6">
			        			<fieldset style="padding:30px; min-height: 450px;">
			        				<legend class="block_title"><i class="fa fa-flag"></i> <?php echo $this->lang->line('Brand Settings'); ?></legend>


						           	<div class="form-group">
						             	<label for=""><i class="fa fa-globe"></i> <?php echo $this->lang->line("Application Name");?> </label>
				               			<input name="product_name" value="<?php echo $this->config->item('product_name');?>"  class="form-control" type="text">		          
				             			<span class="red"><?php echo form_error('product_name'); ?></span>
						            </div>

						            <div class="form-group">
						             	<label for=""><i class="fa fa-compress"></i> <?php echo $this->lang->line("Application Short Name");?> </label>
				               			<input name="product_short_name" value="<?php echo $this->config->item('product_short_name');?>"  class="form-control" type="text">
				             			<span class="red"><?php echo form_error('product_short_name'); ?></span>
						            </div>

			        				
					              	<div class="form-group">
						              	<label for=""><i class="fa fa-briefcase"></i> <?php echo $this->lang->line("company name");?></label>
				               			<input name="institute_name" value="<?php echo $this->config->item('institute_address1');?>"  class="form-control" type="text">	
				             			<span class="red"><?php echo form_error('institute_name'); ?></span>
						            </div>
						            <div class="form-group">
						             	<label for=""><i class="fa fa-map-marker"></i> <?php echo $this->lang->line("company address");?></label>
				               			<input name="institute_address" value="<?php echo $this->config->item('institute_address2');?>"  class="form-control" type="text">
				             			<span class="red"><?php echo form_error('institute_address'); ?></span>
						           </div> 
						           <div class="row">
						           <div class="col-xs-12 col-md-6">
							           <div class="form-group">
							             	<label for=""><i class="fa fa-envelope"></i> <?php echo $this->lang->line("company email");?> *</label>
					               			<input name="institute_email" value="<?php echo $this->config->item('institute_email');?>"  class="form-control" type="email">
					             			<span class="red"><?php echo form_error('institute_email'); ?></span>
							           </div>  
							       </div>
							       <div class="col-xs-12 col-md-6">	
							            <div class="form-group">
							             	<label for=""><i class="fa fa-mobile"></i> <?php echo $this->lang->line("company phone/ mobile");?></label>
					               			<input name="institute_mobile" value="<?php echo $this->config->item('institute_mobile');?>"  class="form-control" type="text">
					             			<span class="red"><?php echo form_error('institute_mobile'); ?></span>
							           </div>
							       		</div>
						           </div>
						          
			        			</fieldset>
			        		</div>

		        			<div class="col-xs-12 col-md-6">
				        		<fieldset style="padding:30px;min-height: 450px;">
				        			<legend class="block_title"><i class="fa fa-tasks"></i> <?php echo $this->lang->line('Preference Settings'); ?></legend>			        		        			
					              
						            <div class="form-group">
						             	<label for=""><i class="fa fa-language"></i> <?php echo $this->lang->line("language");?></label>            			
				               			<?php
										$select_lan="english";
										if($this->config->item('language')!="") $select_lan=$this->config->item('language');
										echo form_dropdown('language',$language_info,$select_lan,'class="form-control" id="language"');  ?>		          
				             			<span class="red"><?php echo form_error('language'); ?></span>
						            </div>		

						            <div class="form-group">
						             	<label for=""><i class="fa fa-clock-o"></i> <?php echo $this->lang->line("time zone");?></label>          			
				               			<?php	$time_zone['']=$this->lang->line('time zone');
										echo form_dropdown('time_zone',$time_zone,$this->config->item('time_zone'),'class="form-control" id="time_zone"');  ?>		          
				             			<span class="red"><?php echo form_error('time_zone'); ?></span>
						            </div>
									
						           	<div class="form-group">
						             	<label for="force_https" style="margin-top: -7px;"><i class="fa fa-shield-alt"></i> <?php echo $this->lang->line('Force HTTPS');?>?</label>
				               			<?php	
				               			$force_https = $this->config->item('force_https');
				               			if($force_https == '') $force_https='0';
										echo form_dropdown('force_https',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$force_https,'class="form-control" id="force_https"');  ?>		          
				             			<span class="red"><?php echo form_error('force_https'); ?></span>
						           	</div>

						           <div class="form-group">
						             	<label for="email_sending_option" style="margin-top: -7px;"><i class="fa fa-at"></i> <?php echo $this->lang->line('Email sending option');?></label> 
				               			<?php	
				               			if($this->config->item('email_sending_option') == '') $selected = 'php_mail';
				               			else $selected = $this->config->item('email_sending_option');
				               			$email_sending_option['php_mail']=$this->lang->line('I want to use native PHP mail option.');
				               			$email_sending_option['smtp']=$this->lang->line('I want to use SMTP option.');
										echo form_dropdown('email_sending_option',$email_sending_option,$selected,'class="form-control" id="email_sending_option"');  ?>
				             			<span class="red"><?php echo form_error('email_sending_option'); ?></span>
						           </div>

   						            <div class="row">
               							<!-- Back-end theme -->
   						            	<div class="col-xs-12 col-md-6">
   	            			             	<div class="form-group">
   	            				             	<label><i class="fa fa-window-restore"></i> <?php echo $this->lang->line("Back-end Theme");?></label>            			
   	            		               			<?php 
   	            			               			$select_theme="skin-black-light";
   	            									if($this->config->item('theme')!="") $select_theme=$this->config->item('theme');
   	            									echo form_dropdown('theme',$themes,$select_theme,'class="form-control" id="theme"');  
   	            								?>		          
   	            		             			<span class="red"><?php echo form_error('Back-end Theme'); ?></span>
   	            				            </div>
   						            	</div>

   						            	<!-- Enabling signup -->
   						            	<div class="col-xs-12 col-md-6">
   	            				           	<div class="form-group">
   	            				             	<label for="enable_signup_form" style="margin-top: -7px;"><i class="fa fa-sign-in-alt"></i> <?php echo $this->lang->line('display sign up page');?></label>
   	            		               			<?php	
   	            		               			$enable_signup_form = $this->config->item('enable_signup_form');
   	            		               			if($enable_signup_form == '') $enable_signup_form='1';
   	            								echo form_dropdown('enable_signup_form',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$enable_signup_form,'class="form-control" id="enable_signup_form"');  ?>		          
   	            		             			<span class="red"><?php echo form_error('enable_signup_form'); ?></span>
   	            				           	</div>
   						            	</div>
   						            </div> 
						         </fieldset>
						    </div>
			        		     		
			        	</div>

			        	<br>
			        	<div class="row">		        	
							<div class="col-xs-12 col-md-6">
				        		<fieldset style="padding:30px;min-height: 480px;">
				        			   <legend class="block_title"><i class="fa fa-image"></i> <?php echo $this->lang->line("Logo & Favicon Settings");?></legend>
							           <div class="form-group text-center">
							             	<label for=""><?php echo $this->lang->line("logo");?></label>
						           			<div class='text-center' style="padding:10px;"><img class="img-responsive center-block" src="<?php echo base_url().'assets/images/logo.png';?>" alt="Logo"/></div>
					               			<small><?php echo $this->lang->line("Max Dimension");?> : 600 x 300, <?php echo $this->lang->line("Max Size");?> : 200KB,  <?php echo $this->lang->line("Allowed Format");?> : png</small>
					               			<input name="logo" class="form-control" type="file">		          
					             			<span class="red"> <?php echo $this->session->userdata('logo_error'); $this->session->unset_userdata('logo_error'); ?></span>
							           </div> 
							           <br>
							           <br>
							           <div class="form-group text-center">
							             	<center><label for=""><?php echo $this->lang->line("favicon");?></label></center>
					             			<div class='text-center'><img class="img-responsive center-block" src="<?php echo base_url().'assets/images/favicon.png';?>" alt="Favicon"/></div>
					               			 <small><?php echo $this->lang->line("Max Dimension");?> : 32 x 32, <?php echo $this->lang->line("Max Size");?> : 50KB, <?php echo $this->lang->line("Allowed Format");?> : png</small>
					               			<input name="favicon"  class="form-control" type="file">		          
					             			<span class="red"><?php echo $this->session->userdata('favicon_error'); $this->session->unset_userdata('favicon_error'); ?></span>
							           </div>
				        		</fieldset>	
			        		</div>	

			        		<div class="col-xs-12 col-md-6">
			        			<fieldset style="padding:30px; min-height: 480px;">
			        				<legend class="block_title"><i class="fa fa-reply-all"></i> <?php echo $this->lang->line('Auto Reply Settings'); ?></legend>		        				
			        			
				        			<div class="form-group">
						             	<label for=""><i class="fa fa-business-time"></i> <?php echo $this->lang->line("delay used in auto-reply (seconds)");?></label>
				             			<?php 
					             			$auto_reply_delay_time=$this->config->item('auto_reply_delay_time');
					             			if($auto_reply_delay_time=="") $auto_reply_delay_time=10; 
				             			?>
				               			<input name="auto_reply_delay_time" value="<?php echo $auto_reply_delay_time;?>"  class="form-control" type="number" min="1">		          
				             			<span class="red"><?php echo form_error('auto_reply_delay_time'); ?></span>
						           </div>

						           <div class="form-group">
						             	<label for=""><i class="fa fa-sort-numeric-asc"></i> <?php echo $this->lang->line("number of campaign processed per cron job");?></label>
				             			<?php 
					             			$auto_reply_campaign_per_cron_job=$this->config->item('auto_reply_campaign_per_cron_job');
					             			if($auto_reply_campaign_per_cron_job=="") $auto_reply_campaign_per_cron_job=10; 
				             			?>
				               			<input name="auto_reply_campaign_per_cron_job" value="<?php echo $auto_reply_campaign_per_cron_job;?>"  class="form-control" type="number" min="1">		          
				             			<span class="red"><?php echo form_error('auto_reply_campaign_per_cron_job'); ?></span>
						           </div>

						           <div class="form-group">
						             	<label for=""><i class="fa fa-history"></i> <?php echo $this->lang->line("how much old comment that system will reply?");?></label>
				             			<?php 
					             			$number_of_old_comment_reply=$this->config->item('number_of_old_comment_reply');
					             			if($number_of_old_comment_reply=="") $number_of_old_comment_reply=20; 
				             			?>
				               			<input name="number_of_old_comment_reply" value="<?php echo $number_of_old_comment_reply;?>"  class="form-control" type="number" min="20" max="200">
				               			<span><i><small><?php echo $this->lang->line('system has the ability to reply maximum 200 old comments and minimum 20 old comments'); ?></small></i></span>	          
				             			<span class="red"><?php echo form_error('number_of_old_comment_reply'); ?></span>
						           </div>

						           <div class="form-group">
						             	<label for=""><i class="fa fa-clock-o"></i> <?php echo $this->lang->line("auto-reply campaign live duration (days)");?></label>
				             			<?php 
					             			$auto_reply_campaign_live_duration=$this->config->item('auto_reply_campaign_live_duration');
					             			if($auto_reply_campaign_live_duration=="") $auto_reply_campaign_live_duration=50; 
				             			?>
				               			<input name="auto_reply_campaign_live_duration" value="<?php echo $auto_reply_campaign_live_duration;?>"  class="form-control" type="number" min="1">		          
				             			<span class="red"><?php echo form_error('auto_reply_campaign_live_duration'); ?></span>
						           </div>

						           <div class="form-group">
						             	<label for="autoreply_renew_access" style="margin-top: -7px;"><i class="fa fa-refresh"></i> <?php echo $this->lang->line('Give autoreply renew access to users');?></label>
				               			<?php	
				               			$autoreply_renew_access = $this->config->item('autoreply_renew_access');
				               			if($autoreply_renew_access == '') $autoreply_renew_access='0';
										echo form_dropdown('autoreply_renew_access',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$autoreply_renew_access,'class="form-control" id="autoreply_renew_access"');  ?>		          
				             			<span class="red"><?php echo form_error('autoreply_renew_access'); ?></span>
						           </div> 

						           <div class="form-group">
						             	<label for="read_page_mailboxes_permission" style="margin-top: -7px;"><i class="fa fa-box-open"></i> <?php echo 'Do you have read_page_mailboxes permission?';?></label>
				               			<?php	
				               			$autoreply_renew_access = $this->config->item('read_page_mailboxes_permission');
				               			if($autoreply_renew_access == '') $autoreply_renew_access='yes';
										echo form_dropdown('read_page_mailboxes_permission',array('no'=>$this->lang->line('no'),'yes'=>$this->lang->line('yes')),$autoreply_renew_access,'class="form-control" id="read_page_mailboxes_permission"');  ?>		          
				             			<span class="red"><?php echo form_error('read_page_mailboxes_permission'); ?></span>
						           </div>
						        </fieldset>
			        		</div>	        		
			        	</div>

			        	<br>
			        	<div class="row">
			        		<div class="col-xs-12 col-sm-6">
			        			<fieldset style="padding:30px;min-height: 310px;">
			        				<legend class="block_title"><i class="fa fa-mail-bulk"></i> <?php echo $this->lang->line('Bulk Message Sending Settings'); ?></legend>
					              	<div class="form-group">
						             	<label for=""><i class="fa fa-sort-numeric-asc"></i> <?php echo $this->lang->line("number of message send per cron job");?></label>
				             			<?php 
					             			$number_of_message_to_be_sent_in_try=$this->config->item('number_of_message_to_be_sent_in_try');
					             			if($number_of_message_to_be_sent_in_try=="") $number_of_message_to_be_sent_in_try=10; 
				             			?>
				               			<input name="number_of_message_to_be_sent_in_try" value="<?php echo $number_of_message_to_be_sent_in_try;?>"  class="form-control" type="number" min="0">		          
				             			<span><?php echo $this->lang->line('0 means unlimited');?></span><br>
				             			<span class="red"><?php echo form_error('number_of_message_to_be_sent_in_try'); ?></span>
						            </div>

						           <div class="form-group">
						             	<label for=""><i class="fa fa-edit"></i> <?php echo $this->lang->line("message sending report update frequency");?></label>
				             			<?php 
					             			$update_report_after_time=$this->config->item('update_report_after_time');
					             			if($update_report_after_time=="") $update_report_after_time=5; 
				             			?>
				               			<input name="update_report_after_time" value="<?php echo $update_report_after_time;?>"  class="form-control" type="number" min="1">
				             			<span class="red"><?php echo form_error('update_report_after_time'); ?></span>
						           </div>
			        			</fieldset>
			        		</div>

			        		<div class="col-xs-12 col-sm-6">
			        			<fieldset style="padding:30px;min-height: 310px;">
			        				<legend class="block_title"><i class="fa fa-key"></i> <?php echo $this->lang->line('Master Password & Facebook APP Access Settings');?></legend>
			        				<div class="form-group">
						             	<label for="backup_mode" style="margin-top: -7px;"><i class="fa fa-facebook-square"></i> <?php echo $this->lang->line('give access to user to set their own facebook app');?></label>             			
				               			<?php	
				               			$backup_mode = $this->config->item('backup_mode');
				               			if($backup_mode == 1) $selected = 'yes';
				               			else $selected = 'no';
				               			$user_access['no']=$this->lang->line('no');
				               			$user_access['yes']=$this->lang->line('yes');
										echo form_dropdown('backup_mode',$user_access,$selected,'class="form-control" id="backup_mode"');  ?>		          
				             			<span class="red"><?php echo form_error('backup_mode'); ?></span>
						           </div> 
						           <div class="form-group">
						             	<label for=""><i class="fa fa-key"></i> <?php echo $this->lang->line("Master Password (will be used for login as user.)");?></label>
				               			<input name="master_password" value="******"  class="form-control" type="text">
				             			<span class="red"><?php echo form_error('master_password'); ?></span>
						           </div>
						           <div class="form-group">
						             	<label for="backup_mode" style="margin-top: -7px;"><i class="fa fa-check-circle"></i> 
						             		<?php echo $this->lang->line("Use Approved Facebook App of Author?");?>
						             		<a href="#" data-placement="top"  data-html="true" data-toggle="popover" data-trigger="focus" title="<?php echo $this->lang->line("Use Approved Facebook App of Author?") ?>" data-content="<?php echo $this->lang->line("If you select Yes, you may skip to add your own app. You can use Author's app. But this option only for the admin only. This can't be used for other system users. User management feature will be disapeared."); ?><br><br><?php echo $this->lang->line("If select No , you will need to add your own app & get approval and system users can use it.");?>"><i class='fa fa-info-circle'></i> </a>
						             	</label>             			
				               			<?php	
				               			$developer_access = $this->config->item('developer_access');
				               			if($developer_access == '1') $selected = 'yes';
				               			else $selected = 'no';
				               			$developer_access_array['no']=$this->lang->line('no');
				               			$developer_access_array['yes']=$this->lang->line('yes');
										echo form_dropdown('developer_access',$developer_access_array,$selected,'class="form-control" id="developer_access"');  ?>		          
				             			<span class="red"><?php echo form_error('developer_access'); ?></span>
						           </div> 
			        			</fieldset>
			        		</div>	        		
			        	</div>
						<br>
			        	<!-- settings for supportdesk-->
			        	<?php if($this->session->userdata('license_type') == 'double') { ?>
			        	<div class="row">
    		        		<div class="col-xs-12">
    		        			<fieldset style="padding:30px;min-height: 155px;">
    		        				<legend class="block_title"><i class="fa fa-life-ring"></i> <?php echo $this->lang->line('support desk settings'); ?></legend>
    				              	<div class="form-group">
    				              		<label for="enable_support" style="margin-top:-7px;"><i class="fa fa-headset"></i> <?php echo $this->lang->line('do you want to enable support desk for users?');?></label>
    					             	
       			               			<?php	
       			               			$enable_support = $this->config->item('enable_support');
       			               			if($enable_support == '') $enable_support='1';
       									echo form_dropdown('enable_support',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$enable_support,'class="form-control" id="enable_support"');  ?>		          
       			             			<span class="red"><?php echo form_error('enable_support'); ?></span>
    					            </div>
    		        			</fieldset>
    		        		</div>
			        	</div>
			        	<?php } ?>
						<br/>
			        	<div class="row">
    		        		<div class="col-xs-12 col-sm-12">
    		        			<fieldset style="padding:30px;min-height: 155px;">
			        				<legend class="block_title"><i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('File upload limit settings'); ?></legend>
    		        				<div class="row">
    		        					<div class="col-xs-6">
    		        						<fieldset style="padding:30px;min-height: 155px;">
						        				<legend class="block_title"><i class="fa fa-facebook-square"></i> <?php echo $this->lang->line('file upload limit in Facebook poster section'); ?></legend>
								              	<div class="form-group">
									             	<label for=""><i class="fa fa-image"></i> <?php echo $this->lang->line("image upload limit (MB)");?></label>
							             			<?php 
								             			$facebook_poster_image_upload_limit=$this->config->item('facebook_poster_image_upload_limit');
								             			if($facebook_poster_image_upload_limit=="") $facebook_poster_image_upload_limit=1; 
							             			?>
							               			<input name="facebook_poster_image_upload_limit" value="<?php echo $facebook_poster_image_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('facebook_poster_image_upload_limit'); ?></span>
									            </div>

								              	<div class="form-group">
									             	<label for=""><i class="fa fa-video"></i> <?php echo $this->lang->line("video upload limit (MB)");?></label>
							             			<?php 
								             			$facebook_poster_video_upload_limit=$this->config->item('facebook_poster_video_upload_limit');
								             			if($facebook_poster_video_upload_limit=="") $facebook_poster_video_upload_limit=10; 
							             			?>
							               			<input name="facebook_poster_video_upload_limit" value="<?php echo $facebook_poster_video_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('facebook_poster_video_upload_limit'); ?></span>
									            </div>
    		        						</fieldset>
    		        					</div>
    		        					<?php if($this->basic->is_exist("add_ons",array("project_id"=>2))) : ?>
    		        					<div class="col-xs-6">
    		        						<fieldset style="padding:30px;min-height: 155px;">
						        				<legend class="block_title"><i class="fa fa-mail-reply-all"></i> <?php echo $this->lang->line('file upload limit in auto reply section'); ?></legend>
								              	<div class="form-group">
									             	<label for=""><i class="fa fa-image"></i> <?php echo $this->lang->line("image upload limit (MB)");?></label>
							             			<?php 
								             			$autoreply_image_upload_limit=$this->config->item('autoreply_image_upload_limit');
								             			if($autoreply_image_upload_limit=="") $autoreply_image_upload_limit=1; 
							             			?>
							               			<input name="autoreply_image_upload_limit" value="<?php echo $autoreply_image_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('autoreply_image_upload_limit'); ?></span>
									            </div>

								              	<div class="form-group">
									             	<label for=""><i class="fa fa-video"></i> <?php echo $this->lang->line("video upload limit (MB)");?></label>
							             			<?php 
								             			$autoreply_video_upload_limit=$this->config->item('autoreply_video_upload_limit');
								             			if($autoreply_video_upload_limit=="") $autoreply_video_upload_limit=3; 
							             			?>
							               			<input name="autoreply_video_upload_limit" value="<?php echo $autoreply_video_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('autoreply_video_upload_limit'); ?></span>
									            </div>
    		        						</fieldset>
    		        					</div>
	    		        				<?php endif; ?>

    		        					<?php if($this->basic->is_exist("add_ons",array("project_id"=>20))) : ?>
    		        					<div class="col-xs-6" style="margin-top: 10px;">
    		        						<fieldset style="padding:30px;min-height: 155px;">
						        				<legend class="block_title"><i class="fa fa-tasks"></i> <?php echo $this->lang->line('file upload limit in Combo Poster section'); ?></legend>
								              	<div class="form-group">
									             	<label for=""><i class="fa fa-image"></i> <?php echo $this->lang->line("image upload limit (MB)");?></label>
							             			<?php 
								             			$comboposter_image_upload_limit=$this->config->item('comboposter_image_upload_limit');
								             			if($comboposter_image_upload_limit=="") $comboposter_image_upload_limit=1; 
							             			?>
							               			<input name="comboposter_image_upload_limit" value="<?php echo $comboposter_image_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('comboposter_image_upload_limit'); ?></span>
									            </div>

								              	<div class="form-group">
									             	<label for=""><i class="fa fa-video"></i> <?php echo $this->lang->line("video upload limit (MB)");?></label>
							             			<?php 
								             			$comboposter_video_upload_limit=$this->config->item('comboposter_video_upload_limit');
								             			if($comboposter_video_upload_limit=="") $comboposter_video_upload_limit=10; 
							             			?>
							               			<input name="comboposter_video_upload_limit" value="<?php echo $comboposter_video_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('comboposter_video_upload_limit'); ?></span>
									            </div>
    		        						</fieldset>
    		        					</div>
    		        					<?php endif; ?>

    		        					<?php if($this->basic->is_exist("add_ons",array("project_id"=>21))) : ?>
    		        					<div class="col-xs-6" style="margin-top: 10px;">
    		        						<fieldset style="padding:30px;min-height: 155px;">
						        				<legend class="block_title"><i class="fa fa-tv"></i> <?php echo $this->lang->line('file upload limit in VidcasterLive section'); ?></legend>
								              	<div class="form-group">
									             	<label for=""><i class="fa fa-image"></i> <?php echo $this->lang->line("image upload limit (MB)");?></label>
							             			<?php 
								             			$vidcaster_image_upload_limit=$this->config->item('vidcaster_image_upload_limit');
								             			if($vidcaster_image_upload_limit=="") $vidcaster_image_upload_limit=1; 
							             			?>
							               			<input name="vidcaster_image_upload_limit" value="<?php echo $vidcaster_image_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('vidcaster_image_upload_limit'); ?></span>
									            </div>

								              	<div class="form-group">
									             	<label for=""><i class="fa fa-video"></i> <?php echo $this->lang->line("video upload limit (MB)");?></label>
							             			<?php 
								             			$vidcaster_video_upload_limit=$this->config->item('vidcaster_video_upload_limit');
								             			if($vidcaster_video_upload_limit=="") $vidcaster_video_upload_limit=30; 
							             			?>
							               			<input name="vidcaster_video_upload_limit" value="<?php echo $vidcaster_video_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('vidcaster_video_upload_limit'); ?></span>
									            </div>
    		        						</fieldset>
    		        					</div>
    		        					<?php endif; ?>

    		        					<?php if($this->basic->is_exist("add_ons",array("project_id"=>3))) : ?>
    		        					<div class="col-xs-6" style="margin-top: 10px;">
    		        						<fieldset style="padding:30px;min-height: 155px;">
						        				<legend class="block_title"><i class="fa fa-robot"></i> <?php echo $this->lang->line('file upload limit in Messenger Bot section'); ?></legend>
								              	<div class="form-group">
									             	<label for=""><i class="fa fa-image"></i> <?php echo $this->lang->line("image upload limit (MB)");?></label>
							             			<?php 
								             			$messengerbot_image_upload_limit=$this->config->item('messengerbot_image_upload_limit');
								             			if($messengerbot_image_upload_limit=="") $messengerbot_image_upload_limit=1; 
							             			?>
							               			<input name="messengerbot_image_upload_limit" value="<?php echo $messengerbot_image_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('messengerbot_image_upload_limit'); ?></span>
									            </div>

								              	<div class="form-group">
									             	<label for=""><i class="fa fa-video"></i> <?php echo $this->lang->line("video upload limit (MB)");?></label>
							             			<?php 
								             			$messengerbot_video_upload_limit=$this->config->item('messengerbot_video_upload_limit');
								             			if($messengerbot_video_upload_limit=="") $messengerbot_video_upload_limit=5; 
							             			?>
							               			<input name="messengerbot_video_upload_limit" value="<?php echo $messengerbot_video_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('messengerbot_video_upload_limit'); ?></span>
									            </div>

									            <div class="form-group">
									             	<label for=""><i class="fa fa-headset"></i> <?php echo $this->lang->line("audio upload limit (MB)");?></label>
							             			<?php 
								             			$messengerbot_audio_upload_limit=$this->config->item('messengerbot_audio_upload_limit');
								             			if($messengerbot_audio_upload_limit=="") $messengerbot_audio_upload_limit=3; 
							             			?>
							               			<input name="messengerbot_audio_upload_limit" value="<?php echo $messengerbot_audio_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('messengerbot_audio_upload_limit'); ?></span>
									            </div>

									            <div class="form-group">
									             	<label for=""><i class="fa fa-briefcase"></i> <?php echo $this->lang->line("file upload limit (MB)");?></label>
							             			<?php 
								             			$messengerbot_file_upload_limit=$this->config->item('messengerbot_file_upload_limit');
								             			if($messengerbot_file_upload_limit=="") $messengerbot_file_upload_limit=2; 
							             			?>
							               			<input name="messengerbot_file_upload_limit" value="<?php echo $messengerbot_file_upload_limit;?>"  class="form-control" type="number" min="1">		          
							             			<span class="red"><?php echo form_error('messengerbot_file_upload_limit'); ?></span>
									            </div>

    		        						</fieldset>
    		        					</div>
    		        					<?php endif; ?>
    		        					<!-- facebook poster -->
    		        					
    		        					<div class="col-xs-6" style="margin-top: 10px;">
    		        						<fieldset style="padding:30px;min-height: 155px;">
						        				<legend class="block_title"><i class="fa fa-share-square"></i> <?php echo $this->lang->line('facebook poster group enable'); ?></legend>
								              	<div class="form-group">
									             	<label for=""><i class="fa fa-share"></i> <?php echo $this->lang->line("do you want to enable group post?");?></label>
									             		
			             	               			<?php	
			             	               			$facebook_poster_group_enable_disable = $this->config->item('facebook_poster_group_enable_disable');
			             	               			if($facebook_poster_group_enable_disable == '') $facebook_poster_group_enable_disable='0';
			             							echo form_dropdown('facebook_poster_group_enable_disable',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$facebook_poster_group_enable_disable,'class="form-control" id="facebook_poster_group_enable_disable"');  ?>		          
			             	             			<span class="red"><?php echo form_error('facebook_poster_group_enable_disable'); ?></span>
									             			         
									            </div>

											</fieldset>
    		        					</div>
    		        				  

    		        				</div>
    		        			</fieldset>
    		        		</div>
			        	</div>

			        	<!-- end of supportdesk settings -->
			        </div> <!-- /.box-body --> 
   					<div class="modal-footer" style="text-align:center;">
   						<button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
	              		<button  type="button" class="btn btn-default btn-lg" onclick='goBack("admin_config",1)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
   					</div>

   					</form>
   				</div>
				<br/>
				<div class="row" style="background: #fff; width: 100%; margin: auto; box-shadow: 0px 0px 16px -2px rgba(143,141,143,0.61) !important;">
				  <h4 style="text-align: center;"><?php echo $this->lang->line('Current server settings'); ?></h4><hr>           
				  <div class="col-xs-12 col-md-6">
				    <?php 
				      if(function_exists('ini_get'))
				      {
				        $make_dir = (!function_exists('mkdir')) ? "Not working":"Working";
				        $zip_archive = (!class_exists('ZipArchive')) ? "Not Enabled":"Enabled";
				        echo "<ul>";
				        echo "<li><b>mkdir() function</b> : ".$make_dir."</li>"; 
				        echo "<li><b>ZipArchive</b> : ".$zip_archive."</li>";  
				        echo "<li><b>max_input_time</b> : ".ini_get('max_input_time')."</li>";         
				        echo "</ul>";
				      }
				    ?>
				  </div>
				  <div class="col-xs-12 col-md-6">
				    <?php 
				      if(function_exists('ini_get'))
				      {
				        echo "<ul>";
				        echo "<li><b>max_execution_time</b> : ".ini_get('max_execution_time')."</li>";
				        echo "<li><b>post_max_size</b> : ".ini_get('post_max_size')."</li>"; 
				        echo "<li><b>upload_max_filesize</b> : ".ini_get('upload_max_filesize')."</li>";          
				        echo "</ul>";
				      }
				    ?>
				  </div>
				  <div class="col-xs-12">              
				    <p style="padding-left: 20px;"><?php echo $this->lang->line('If you face problem during upload, make sure server file upload limit is large enough.'); ?></p>
				  </div>
				</div>
				<br/>
   			</div>
   		</div>     	
   </section>
</section>



<script>
	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});
</script>
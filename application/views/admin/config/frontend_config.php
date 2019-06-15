<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
   		<div class="" id="modal-id">
   			<div class="modal-dialog" style="width: 100%;margin:0;">
   				<div class="modal-content">
   					<div class="modal-header">
   						<!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
   						<h4 class="modal-title"><i class="fa fa-newspaper"></i> <?php echo $this->lang->line("frontend settings");?></h4>
   					</div>
   					<form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url('admin_config/frontend_configuration_action');?>" method="POST">		     
			        <div class="modal-body">
			        	<!-- first row started(general and social settings) -->
			        	<div class="row">
			        		<div class="col-xs-12 col-md-6">
			        			<fieldset style="padding:30px; min-height: 220px;">
			        				<legend class="block_title"><i class="fa fa-wrench"></i> <?php echo $this->lang->line('general settings'); ?></legend>

						           	<div class="form-group">
						             	<label for="display_landing_page" style="margin-top: -7px;"><i class="fa fa-television"></i> <?php echo $this->lang->line('display landing page');?></label>
				               			<?php	
				               			$display_landing_page = $this->config->item('display_landing_page');
				               			if($display_landing_page == '') $display_landing_page='0';
										echo form_dropdown('display_landing_page',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$display_landing_page,'class="form-control" id="display_landing_page"');  ?>		          
				             			<span class="red"><?php echo form_error('display_landing_page'); ?></span>
						           </div>

						            <div class="form-group">
						             	<label for=""><i class="fa fa-window-restore"></i> <?php echo $this->lang->line("Front-end Theme");?> </label>            			
				               			<?php 
				               			$select_front_theme="blue";
										if($this->config->item('theme_front')!="") $select_front_theme=$this->config->item('theme_front');
										echo form_dropdown('theme_front',$themes_front,$select_front_theme,'class="form-control" id="theme_front"');  ?>		          
				             			<span class="red"><?php echo form_error('Front-end Theme'); ?></span>
						            </div>						           
			        			</fieldset>
			        		</div>

		        			<div class="col-xs-12 col-md-6">
				        		<fieldset style="padding:30px;min-height: 220px;">
				        			<legend class="block_title"><i class="fa fa-share-square"></i> <?php echo $this->lang->line('social Settings'); ?></legend>
				        			<div class="row">
				        				<div class="col-xs-12 col-md-6"> 			
								            <div class="form-group">
								             	<label for=""><i class="fa fa-facebook-square"></i> <?php echo $this->lang->line("facebook");?></label>
								             	<input name="facebook_link" value="<?php echo $this->config->item('facebook');?>" class="form-control" type="text">		          
						             			<span class="red"><?php echo form_error('facebook_link'); ?></span>           			
								            </div>
								        </div>
								        <div class="col-xs-12 col-md-6">
								            <div class="form-group">
								             	<label for=""><i class="fa fa-twitter-square"></i> <?php echo $this->lang->line("twitter");?></label>
								             	<input name="twitter_link" value="<?php echo $this->config->item('twitter');?>" class="form-control" type="text">		          
						             			<span class="red"><?php echo form_error('twitter_link'); ?></span>           			
								            </div>
							            </div>
						            </div>

									<div class="row">
				        				<div class="col-xs-12 col-md-6">
								            <div class="form-group">
								             	<label for=""><i class="fa fa-linkedin-square"></i> <?php echo $this->lang->line("linkedin");?></label>
								             	<input name="linkedin_link" value="<?php echo $this->config->item('linkedin');?>" class="form-control" type="text">		          
						             			<span class="red"><?php echo form_error('linkedin_link'); ?></span>           			
								            </div>
								        </div>
										<div class="col-xs-12 col-md-6">
								            <div class="form-group">
								             	<label for=""><i class="fa fa-youtube-play"></i> <?php echo $this->lang->line("youtube");?></label>
								             	<input name="youtube_link" value="<?php echo $this->config->item('youtube');?>" class="form-control" type="text">		          
						             			<span class="red"><?php echo form_error('youtube_link'); ?></span>           			
								            </div>
								        </div>
								    </div>
					         	</fieldset>
						    </div>     		
			        	</div>
			        	<!-- end of first row -->
						
						<br>

						<!-- second row started ( review and video settings ) -->
			        	<div class="row">
			        		<!-- review section started -->
			        		<div class="col-xs-12 col-md-6">
			        			<fieldset class="review_block" style="padding:30px;">
			        				<legend class="block_title"><i class="fa fa-smile"></i> <?php echo $this->lang->line('review settings'); ?></legend>

						           	<div class="form-group">
						             	<label for="display_landing_page" style="margin-top: -7px;"><i class="fa fa-th"></i> <?php echo $this->lang->line('display review block');?></label>
				               			<?php	
				               			$display_review_block = $this->config->item('display_review_block');
				               			if($display_review_block == '') 
				               				$display_review_block='0';
										echo form_dropdown('display_review_block',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$display_review_block,'class="form-control" id="display_review_block"'); ?>	          
				             			<span class="red"><?php echo form_error('display_review_block'); ?></span>
						           </div>
			

									<!-- review block display section -->
									<?php $customer_review = $this->config->item('customer_review'); ?>

									<div class="allReview">
										<!-- demo video section started -->
							            <div class="form-group">
							             	<label for=""><i class="fa fa-play-circle"></i> <?php echo $this->lang->line("customer review video");?></label>
							             	<input name="customer_review_video" value="<?php echo $this->config->item('customer_review_video');?>" class="form-control" type="text">
					             			<span class="red"><?php echo form_error('customer_review_video'); ?></span>           			
							            </div>
							            <!-- end of the demo video section -->

										<!-- showing reviews section -->
										<?php $i = 1; 
											foreach($customer_review as $singleReview) :
												$original = $singleReview[2];
				                                $base     = base_url();

				                                if (substr($original, 0, 4) != 'http') {
				                                    $img = $base.$original;
				                                } else {
				                                   $img = $original;
				                                }

										?>
								           	<fieldset style="padding:30px; min-height: 100px;">
						        				<legend class="block_title"><i class="fa fa-thumbs-up"></i> <?php echo $this->lang->line('review no: ').' '.$i.' '; ?></legend>
												<div class="row">
													<div class="col-xs-12 col-md-6">
											           	<div class="form-group">
											             	<label style="margin-top: -7px;"><i class="fa fa-user"></i> <?php echo $this->lang->line('name');?></label>
									               			<input name="reviewer<?php echo $i; ?>" value="<?php echo $singleReview[0];?>" class="form-control" type="text">		          
									             			<span class="red"><?php echo form_error('reviewer'); ?></span>
											           </div>
										           	</div>
													
										           	<div class="col-xs-12 col-md-6">
											           	<div class="form-group">
											             	<label style="margin-top: -7px;"><i class="fa fa-briefcase"></i> <?php echo $this->lang->line('designation');?></label>
									               			<input name="designation<?php echo $i; ?>" value="<?php echo $singleReview[1];?>"  class="form-control" type="text">		          
									             			<span class="red"><?php echo form_error('designation'); ?></span>
											           </div>
										           	</div>
												</div>

												<div class="row">
										           	<div class="col-xs-12 col-md-12">
											           	<div class="form-group">
											             	<label style="margin-top: -7px;"><i class="fa fa-picture-o"></i> <?php echo $this->lang->line('image');?></label>
									               			<input name="pic<?php echo $i; ?>" value="<?php echo $img;?>"  class="form-control" type="text">		          
									             			<span class="red"><?php echo form_error('pic'); ?></span>
											           </div>
										           	</div>
									          	</div>

									          	<div class="row">
										           	<div class="col-xs-12 col-md-12">
											           	<div class="form-group">
											             	<label style="margin-top: -7px;"><i class="fa fa-comment"></i> <?php echo $this->lang->line('review');?><small style="font-size: 12px;">&nbsp;</small></label>
									               			<textarea name="description<?php echo $i; ?>" rows="3" class="form-control" type="text"><?php echo $singleReview[3];?></textarea>	
											           </div>
										           	</div>
									           </div>			           
						        			</fieldset><br>
					        			<?php $i++; endforeach; ?>	
										<!-- end of showing reviews section -->
									</div>
				        			<!-- end display review block section -->
	        					</fieldset>
		        			</div>
			        		<!-- end of review section -->
							
							<!-- video block section started -->
		        			<div class="col-xs-12 col-md-6">
				        		<fieldset class="video_block" style="padding:30px;">
				        			<legend class="block_title"><i class="fa fa-video-camera"></i> <?php echo $this->lang->line('video Settings'); ?></legend>
					           			
			           		 		<div class="form-group">
						             	<label for=""><i class="fa fa-play"></i> <?php echo $this->lang->line("display tutorial block");?></label>
						             	<?php	
				               			$display_video_block=$this->config->item('display_video_block');
				               			
				               			if($display_video_block == '') $display_video_block='0';

										echo form_dropdown('display_video_block',array('0'=>$this->lang->line('no'),'1'=>$this->lang->line('yes')),$display_video_block,'class="form-control" id="display_video_block"'); ?>		          
				             			<span class="red"><?php echo form_error('display_video_block'); ?></span>           			
					            	</div>

					            	
				           			<div class="extensions">
										<!-- promo video section started -->
							            <div class="form-group">
							             	<label for=""><i class="fa fa-play-circle"></i> <?php echo $this->lang->line("promo video");?></label>
							             	<input name="promo_video" value="<?php echo $this->config->item('promo_video');?>" class="form-control" type="text">
					             			<span class="red"><?php echo form_error('promo_video'); ?></span>           			
							            </div>
						            	<!-- end of the promo video section -->

						            	<?php $custom_video = $this->config->item('custom_video'); ?>
						            	<!-- video tutorial section started -->
							            <?php $i = 1; 
							            	foreach ($custom_video as $singleVideo) : 
							            	$original_video = $singleVideo[0];
							                $baseurl    = base_url();

							                if (substr($original_video,0,4) != 'http') {
							                    $thumb = $baseurl.$original_video;
							                } else {
							                    $thumb = $original_video;
							                }
							            ?>
											<fieldset style="padding:30px; min-height: 100px;">
						        				<legend class="block_title"><i class="fa fa-youtube"></i> <?php echo $this->lang->line('tutorial no: ').' '.$i.' '; ?></legend>
												<div class="row">
													<div class="col-xs-12 col-md-6">
											           	<div class="form-group">
											             	<label style="margin-top: -7px;"><i class="fa fa-image"></i> <?php echo $this->lang->line('thumbnail');?></label>
									               			<input name="thumbnail<?php echo $i; ?>" value="<?php echo $thumb; ?>"  class="form-control" type="text">		          
									             			<span class="red"><?php echo form_error('thumbnail'); ?></span>
											           </div>
										           	</div>
													
										           	<div class="col-xs-12 col-md-6">
											           	<div class="form-group">
											             	<label style="margin-top: -7px;"><i class="fa fa-hashtag"></i> <?php echo $this->lang->line('title');?></label>
									               			<input name="title<?php echo $i; ?>" value="<?php echo $singleVideo[1]; ?>"  class="form-control" type="text">		          
									             			<span class="red"><?php echo form_error('title'); ?></span>
											           </div>
										           	</div>
												</div>

												<div class="row">
										           	<div class="col-xs-12 col-md-12">
											           	<div class="form-group">
											             	<label style="margin-top: -7px;"><i class="fa fa-link"></i> <?php echo $this->lang->line('url');?></label>
									               			<input name="video_url<?php echo $i; ?>" value="<?php echo $singleVideo[2]; ?>"  class="form-control" type="text">		          
									             			<span class="red"><?php echo form_error('video_url'); ?></span>
											           </div>
										           	</div>
									          	</div>
						        			</fieldset> <br>
						        		<?php $i++; endforeach; ?>
					        			<!-- end of the video tutorial section -->
						           	</div>
						        </fieldset>
						    </div> 
						    <!-- end of video block section -->   		
			        	</div> 
			        	<!-- end of second row-->
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
	$j("document").ready(function() {
     	var val1 = $('#display_review_block').val();
     	var val2 = $('#display_video_block').val();

     	// initail situation
     	// review block
     	if(val1 =='0') {
     		$('.allReview').hide();
     		$('.review_block').css("min-height","150px");
     	} else {
     		$('.review_block').css("min-height","1266px");
     	}

     	// video block
     	if(val2 =='0') {
     		$('.extensions').hide();
     		$('.video_block').css("min-height","150px");
     	} else {
     		$('.video_block').css("min-height","1266px");
     	}



      	$("#display_review_block").on('change',function(){
      		var review = $('#display_review_block').val();
        	if(review == '1') {
        		$('.allReview').show();
        		$('.review_block').css("min-height","1266px");
        		
        	} else {
        		$('.allReview').hide();
        		$('.review_block').css("min-height","150px");
        	}
   		}); 

        $("#display_video_block").on('change',function(){
        	var tutorial = $('#display_video_block').val();
        	if(tutorial == '1') {
        		$('.extensions').show();
        		$('.video_block').css("min-height","1266px");
        	} else {
        		$('.extensions').hide();
        		$('.video_block').css("min-height","150px");
        	}
      	});
    });
</script>




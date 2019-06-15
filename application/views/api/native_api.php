<div class="well well_border_left">
	<h4 class="text-center"> <i class="fa fa-clock-o"></i> <?php echo $this->lang->line("cron job"); ?></h4>
</div>
<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
	     	<?php
			$text= $this->lang->line("generate API key");
			$get_key_text=$this->lang->line("get your API key");
			if(isset($api_key) && $api_key!="")
			{
				$text=$this->lang->line("re-generate API key");
				$get_key_text=$this->lang->line("your API key");
	   		}
	   		if($this->is_demo=='1') $api_key='xxxxxxxxxxxxxxxxxxxxxxxxxx';
	   		?>

		    <!-- form start -->
		    <form class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url().'native_api/get_api_action';?>" method="GET">
		        <div class="box-body" style="padding-top:0;">
		           	<div class="form-group">
		           		<div class="small-box bg-aqua" style="background:#fff !important; color:#777 !important;border-color:#ccc;">
							<div class="inner">
								<h4><?php echo $get_key_text; ?></h4>
								<p>
		   							<h2><?php echo $api_key; ?></h2>
								</p>
								<input name="button" type="submit" class="btn btn-primary btn-lg btn <?php if($this->is_demo=='1') echo 'disabled';?>" value="<?php echo $text; ?>"/>
							</div>
							<div class="icon">
								<i class="fa fa-key"></i>
							</div>
						</div>
		            </div>

		           </div> <!-- /.box-body -->
		    </form>


		<?php
		if($api_key!="") { ?>
			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("membership expiry alert cron job command [once per day]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("native_api/send_notification")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>

			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("background lead sync cron job command [once per 1 minute or higher]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("native_api/auto_lead_list_sync")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>

			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("send inbox messages cron job command [once per minute or higher]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("native_api/fb_exciter_send_inbox_message")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>

			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("send auto private reply on comment cron job command [once per five minute or higher]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("native_api/send_auto_private_reply_on_comment_on_fbexciter")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>

			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("alert for unread messages cron job command [once per hour or higher]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("native_api/send_messenger_notification")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>

			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("Text/Image/Link/Video Poster");?> [<?php echo $this->lang->line("once per five minutes");?>]
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("ultrapost/text_image_link_video_auto_poster_cron_job")."/".$api_key." >/dev/null 2>&1"; ?> 
				</div>
			</div>	
			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("CTA Poster");?> [<?php echo $this->lang->line("once per five minutes");?>]
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("ultrapost/cta_poster_cron_job")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>	
			<!-- <div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("Offer Poster");?> [<?php echo $this->lang->line("once per five minutes");?>]
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("ultrapost/offer_post_cron_job")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div> -->	
			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("Carousel/Slider Poster");?> [<?php echo $this->lang->line("once per five minutes");?>]
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("ultrapost/carousel_slider_cron_job")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>	

			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("delete junk data cron job command [once per day]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("native_api/delete_junk_data")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>


			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("Auto comment on post Cron Job Command [once per 1 minute or higher]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("native_api/post_auto_comment_cron_job")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>

			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("RSS Auto Posting [once per 5 minute or higher]");?>
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("autoposting/autoposting_campaign_create")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>
		<?php }?>
		<!-- seperator****************************************************** -->


   </section>
</section>

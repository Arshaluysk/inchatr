<div class="well well_border_left">
	<h4 class="text-center"> <i class="fa fa-clock-o"></i> <?php echo $this->lang->line("cron job"); ?></h4>
</div>
<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
	    <?php
		if($api_key!="") { ?>
			<div id=''>
				<h4 style="margin:0">
					<div class="alert alert-info" style="margin-bottom:0;background:#fff !important; color:<?php echo $THEMECOLORCODE;?> !important;border-color:#fff;">
						<i class="fa fa-clock-o"></i> <?php echo $this->lang->line("Drip Message Send");?> [<?php echo $this->lang->line("every minute"); ?>]
					</div>
				</h4>
				<div class="well" style="background:#fff;margin-top:0;border-radius:0;">
					<?php echo "curl ".site_url("drip_messaging/drip_messaging_cron")."/".$api_key." >/dev/null 2>&1"; ?>
				</div>
			</div>			
		<?php } else echo "<p class='text-center'><a class='btn btn-lg btn-primary' href='".base_url('native_api/index')."'><i class='fa fa-key'></i> ".$this->lang->line("generate API key")."</a></";?>

   </section>
</section>


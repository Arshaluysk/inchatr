<!DOCTYPE html>
<html>
<head>

	<title><?php echo $this->config->item('product_name')." | ".$title;?></title>
	<?php $this->load->view("include/css_include_front");?>    
    <?php $this->load->view("include/js_include_front"); ?> 
	<link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.png"> 
	<!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
	<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	 -->

		<div class="row">
				<div class="col-xs-12" style="height:80px;background:  <?php echo $THEMECOLORCODE;  ?>">
					<h1 style="margin:12px 0;"><a href="<?php echo site_url(); ?>"><img src="<?php echo base_url();?>assets/images/logo.png" style="height:65%;" alt="<?php echo $this->config->item('product_name');?>" class="img-responsive center-block"></a></h1>
		   		</div>
			</div>

	<section id="welcome" style="z-index: 10000; border-bottom: 3px solid <?php echo $THEMECOLORCODE; ?>;">
		<div class="container">
		

		

		</div>
	</section>
	<section id="for-content">
		<div class="container"><br>
			<?php include("application/views/".$body.".php");?>
		</div>
	</section>
	

	<section id="footer" style="background: <?php echo $THEMECOLORCODE; ?>">
		<footer>
			<div class="footer-area">
				<div class="container">
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="foot-text">
								<p>
									Copyright © <a href="<?php echo base_url(); ?>" target="_BLANK"><?php echo $this->config->item("product_name"); ?></a> - All rights reserved.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</section>

</head>
<body>

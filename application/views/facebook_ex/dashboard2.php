<style>
	.nav-stacked > li > a .fa{color:<?php echo $THEMECOLORCODE;?>;}
	.box-body{
	   padding-top: 3px !important;
	   padding-bottom: 0 !important; 
	}
	.badge
	{
		background: <?php echo $THEMECOLORCODE; ?>;
	}
	#dashboard-top {
		padding-top: 50px;
	}
	.row2-cmn{
		background: #fff !important;
		<?php echo $BOXSHADOW; ?>
	}

	#dashboard-top .cmn {
		position: relative;
		height: 160px;
		margin: 15px 0 40px 0;
		/* border-radius: 5px; */
		width: 100%;
		float: left;
		<?php echo $BOXSHADOW; ?>
	}

	#dashboard-top .cmn .info {
		color: #fff;
		margin: 45px 0 10px 0;
		display: block;
		position: relative;
		text-align: center;
		font-size: 51px;
	}	

	#dashboard-top .cmn .info span {
		font-size: 12px;
		margin-left: 3px;
	}	

	#dashboard-top .cmn .short-info {
		color: #fff;
		text-align: center;
		font-size: 14px;
		margin-top: 0px;
	}	

	.bg-a {
		background: <?php echo $COLOR2;?>;
	}	

	.bg-b {
		background: <?php echo $COLOR1;?>;
	}	

	.bg-c {
		background: <?php echo $COLOR3;?>;
	}

	.bg-d {
		background: <?php echo $COLOR4;?>;
	}

	.top-icon {
		position: absolute;
		top: -50px;
		left: 0;
		width: 100%;
		text-align: center;
	}

	.first-circle {
		width: 100px;
		height: 100px;
		border-radius: 50%;
		background: #ebebeb;
		margin: 0 auto;
		padding: 10px 0;
		display: block;
		<?php echo $BOXSHADOW; ?>
	}

	.second-circle {
		width: 80px;
		height: 80px;
		border-radius: 50%;
		background: #fff;
		margin: 0 auto;
		padding: 10px 0;
	}

	.third-circle {
		background: #dc3545;	
		width: 60px;
		height: 60px;
		border-radius: 50%;
		margin: 0 auto;
		text-align: center;
		padding-top: 17px;
	}	

	.third-circle.bg-b {
		background: #17a2b8;
	}	

	.third-circle.bg-c {
		background: #35B084;	
	}	

	.third-circle.bg-d {
		background: #35B084;
	}

	.third-circle i {
		color: #fff;
		font-size: 26px;
	}	

	.more-info {
		position: absolute;
		bottom: -20px;
		left: 0;
		width: 100%;
		text-align: center;
	}	

	.more-info a {
		height: 40px;
		cursor: default;
		width: 50%;
		padding: 10px 15px;
		background: #eee;
		color: #333333;
		margin: 0 auto;
		display: block;
		text-align: center;	
		border-radius: 20px;
	}	
	
	#dashboard-box {
		/* margin-bottom: 25px; */
	}
	
	#dashboard-box .cmn {
		background: #fff;
		height: 150px;
		padding: 15px;
		position: relative;
		margin-bottom: 30px;
		border: 1px solid #e1e1e1;
	}

	#dashboard-box .cmn .icon {
		position: absolute;
		height: 86px;
		width: 86px;
		left: 15px;
		top: -15px;
		background: #35B084;
		text-align: center;
		padding-top: 21px;
	}
	
	#dashboard-box .cmn .icon.bg-a {
		background: #35B084;
	}	
	
	#dashboard-box .cmn .icon.bg-b {
		background: #ffc107;
	}
	
	#dashboard-box .cmn .icon.bg-c {
		background: #dc3545;
	}
	
	#dashboard-box .cmn .icon.bg-d {
		background: #FF4056;
	}
	
	#dashboard-box .cmn .icon i {
		font-size: 41px;		
		color: #fff;
	}	

	#dashboard-box .cmn .info {
		text-align: left;
		color: #ababab;
		margin-top: 0;
		margin-bottom: 0px;
		font-size: 16px;
		padding: 10px 0;
	}

	#dashboard-box .cmn .info.a {
		color: <?php echo $THEMECOLORCODE; ?>;
	}	

	#dashboard-box .cmn .info.b {
		color: <?php echo $THEMECOLORCODE; ?>;
	}	

	#dashboard-box .cmn .info.c {
		color: <?php echo $THEMECOLORCODE; ?>;
	}	

	#dashboard-box .cmn .stat {
		text-align: right;
		color: #35B084;
		margin-top: 0;
		margin-bottom: 10px;
		font-size: 45px;
	}
	
	#dashboard-box .cmn .stat.color-b {
		color: #ffc107;
	}
	
	#dashboard-box .cmn .stat.color-c {
		color: #dc3545;
	}
	
	#dashboard-box .cmn .stat.color-d {
		color: #FF4056;
	}
	
	#dashboard-box .cmn .stat span {
			font-size: 15px;
	}	
	
	#dashboard-box .cmn .bottom-info {
		position: absolute;
		bottom: 0;
		left: 0;
		width: 95%;
		margin: 0 2.5%;
		/* border-top: 1px solid #e1e1e1; */
	}
	
	#dashboard-box .cmn .bottom-info a {
		padding: 7px 0;
		text-align: right;
		display: block;
	}	
	
	#dashboard-middle {
		width: 100%;
		float: left;
		margin: 35px 0 35px 0;
	}
	
	#dashboard-middle .cmn {
		background: #1a8af1;
		border-radius: 3px;	
		width: 100%;
		height: 110px;
		padding: 10px;
		padding-left: 100px;
		position: relative;
		margin-bottom: 15px;
	}
	
	#dashboard-middle .cmn.bg-b {
		background: #5dc560;
	}	
	
	#dashboard-middle .cmn.bg-c {
		background: #ea5691;
	}

	#dashboard-middle .cmn .icon {
		position: absolute;
		left: 10px;
		top: 10px;
		width: 90px;
		height: 90px;
		border-radius: 50%;
		text-align: center;
		background: #fff;
		padding-top: 25px;
	}
	
	#dashboard-middle .cmn .icon i {
		color: #1a8af1;
		font-size: 40px;
	}	
	
	#dashboard-middle .cmn .icon i.bg-b {
		color: #5dc560;
		background: none;
	}	
	
	#dashboard-middle .cmn .icon i.bg-c {
		color: #ea5691;
		background: none;
	}

	#dashboard-middle .cmn .info {
		text-align: right;
		color: #fff;
		font-size: 23px;
		margin-top: 10px;
		margin-bottom: 10px;
		font-weight: 500;
	}
	
	#dashboard-middle .cmn .info.color-b {
		color: #fff;
	}	
	
	#dashboard-middle .cmn .info.color-c {
		color: #fff;
	}	

	#dashboard-middle .cmn .stat {
		text-align: right;
		color: #fff;
		font-size: 30px;
		margin-top: 0px;
		margin-bottom: 0px;
		font-weight: normal;
	}	
	
	#dashboard-bottom {
		margin: 30px 0 35px 0;
		width: 100%;
		float: left;
	}
	
	#dashboard-bottom .cmn {
		background: #fff;
		border-radius: 3px;		
		width: 100%;
		height: 110px;
		padding: 10px;
		padding-right: 100px;
		position: relative;
		margin-bottom: 15px;
	}
	
	#dashboard-bottom .cmn.bg-b {
		background: #fff;
	}	
	
	#dashboard-bottom .cmn.bg-c {
		background: #fff;
	}

	#dashboard-bottom .cmn .icon {
		position: absolute;
		right: 0;
		top: 0;
		width: 90px;
		height: 100%;
		text-align: center;
		background: #1a8af1;
		padding-top: 35px;
	}
	
	#dashboard-bottom .cmn .icon.bg-b {
		background: #5dc560;
	}	
	
	#dashboard-bottom .cmn .icon.bg-c {
		background: #ea5691;
	}
	
	#dashboard-bottom .cmn .icon i {
		color: #fff;
		font-size: 40px;
	}	
	
	#dashboard-bottom .cmn .icon i.bg-b {
		color: #fff;
		background: none;
	}	
	
	#dashboard-bottom .cmn .icon i.bg-c {
		color: #fff;
		background: none;
	}

	#dashboard-bottom .cmn .info {
		text-align: left;
		color: #1a8af1;
		font-size: 23px;
		margin-top: 10px;
		margin-bottom: 10px;
		font-weight: 500;
	}
	
	#dashboard-bottom .cmn .info.color-b {
		color: #5dc560;
	}	
	
	#dashboard-bottom .cmn .info.color-c {
		color: #ea5691;
	}	

	#dashboard-bottom .cmn .stat {
		text-align: left;
		color: #C2C2A6;
		font-size: 30px;
		margin-top: 0px;
		margin-bottom: 0px;
		font-weight: normal;
	}	
	
	.dashboard-title {
		/* padding: 15px; */
		color: <?php if($THEMECOLORCODE=='#fff') echo '#607D8B'; else echo $THEMECOLORCODE;?>;
		text-align: center;
		border-radius: 3px;
		font-size: 20px;
		margin: 0;
	}


	.dashboard-arrow {
		margin-top: 10px;
		margin-bottom: 10px;
	}

	.dashboard-arrow .cmn {
		width: 100%;
		margin-bottom: 10px;
		/* background: #fff; */
	}	

	.dashboard-arrow .cmn .top-title {
		width: 100%;
		height: 70px;
		background-size: 100% 100%;
		color: #777;
		font-size: 16px;
		padding-top: 13px;
		text-align: center;
		background: #fff;
		/* border-radius: 15px 15px 0px 0px; */
	}	

	.dashboard-arrow .cmn .top-title.background-a {
		<?php echo $BOXSHADOW; ?>
	}		

	.dashboard-arrow .cmn .top-title.background-b {
		<?php echo $BOXSHADOW; ?>
	}	

	.dashboard-arrow .cmn .top-title.background-c {
		<?php echo $BOXSHADOW; ?>
	}

	.dashboard-arrow .cmn .top-title.background-d {
		<?php echo $BOXSHADOW; ?>
	}	

	.dashboard-arrow .cmn .stat {
		float: left;
		width: 100%;
		padding: 15px 20px 10px 20px;
		border-width: 0 1px 1px 1px;
		margin-top: -17px;		
		background: #fff;
		<?php echo $BOXSHADOW; ?>
	}	

	.dashboard-arrow .cmn .stat.a {
		border-color: #dc3545;
	}	

	.dashboard-arrow .cmn .stat.b {
		border-color: #3B90E6;
	}

	.dashboard-arrow .cmn .stat.c {
		border-color: #62AE62;
	}

	.dashboard-arrow .cmn .stat .icon {
		width: 50%;
		float: left;
	}

	.dashboard-arrow .cmn .stat .icon .icon-circle {
		/* background: #dc3545; */
		width: 70px;
		height: 70px;
		border-radius: 50%;
		text-align: center;
		padding-top: 11px;
		color: #fff;
		font-size: 23px;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle.a {
		color: #00A65A;
		border: 1px solid #00A65A;
	}

	.dashboard-arrow .cmn .stat .icon .icon-circle.b {
		color: #F9A602;
		border: 1px solid #F9A602;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle.c {
		color: #0A70E3;
		border: 1px solid #0A70E3;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle.d {
		color: #FF4D7B;
		border: 1px solid #FF4D7B;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle i {
		padding-top: 12px;
		padding-left: 3px;
	}	
	
	.dashboard-arrow .cmn .stat .number {
		width: 50%;
		float: left;
		color: #222;
		font-size: 20px;
		font-weight: 300;
		text-align: right;
		padding-top: 15px;
	}	


	.dashboard-arrow .cmn .stat .number.a {
		color: #00A65A;
	}

	.dashboard-arrow .cmn .stat .number.b {
		color: #F9A602;
	}	

	.dashboard-arrow .cmn .stat .number.c {
		color: #0A70E3;
	}	

	.dashboard-arrow .cmn .stat .number.d {
		color: #FF4D7B;
	}	
</style>

<div class="well well_border_left">
	<h4 class="text-center"> <i class="fa fa-dashboard"></i> <?php if(isset($overall_dashboard)) echo $this->lang->line("System Dashboard"); else echo $this->lang->line("dashboard"); if(isset($user_name) && isset($user_email)) echo " for ".$user_name." [".$user_email."]"; ?></h4>
</div>
<div class="container-fluid" style="margin-top: 30px;">
	<div class="row">
  	
  		<div class="col-md-4">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border text-center">
					<h3 class="box-title"><i class="fa fa-calendar-check"></i> <?php echo $this->lang->line("Today's Report") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
							<ul class="nav nav-pills nav-stacked">
				                <li>
				                	<a href="#"><i class="fa fa-mail-bulk"></i> <?php echo $this->lang->line('Bulk message sent'); ?>
				                  		<span class="badge pull-right"><?php echo $today_message_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line('Auto reply sent'); ?>
				                  		<span class="badge pull-right"><?php echo $today_comment_reply_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-comment-alt"></i> <?php echo $this->lang->line('Private reply sent'); ?>
				                  		<span class="badge pull-right"><?php echo $today_private_reply_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-share-square"></i> <?php echo $this->lang->line('Post publish'); ?>
				                  		<span class="badge pull-right"><?php echo $today_post_publish; ?></span>
				                  	</a>
				              	</li>
				            </ul>
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>
  		
  		<div class="col-md-4">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border text-center">
					<h3 class="box-title"><i class="fa fa-calendar-plus"></i> <?php echo $this->lang->line("7 days report") ?> </h3>
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
							<ul class="nav nav-pills nav-stacked">
				                <li>
				                	<a href="#"><i class="fa fa-mail-bulk"></i> <?php echo $this->lang->line('Bulk message sent'); ?>
				                  		<span class="badge pull-right"><?php echo $last_sevendays_message_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line('Auto reply sent'); ?>
				                  		<span class="badge pull-right"><?php echo $last_sevendays_comment_reply_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-comment-alt"></i> <?php echo $this->lang->line('Private reply sent'); ?>
				                  		<span class="badge pull-right"><?php echo $last_sevendays_private_reply_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-share-square"></i> <?php echo $this->lang->line('Post publish'); ?>
				                  		<span class="badge pull-right"><?php echo $last_sevendays_post_publish; ?></span>
				                  	</a>
				              	</li>
				            </ul>
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
  		</div>

  		<div class="col-md-4">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border text-center">
					<h3 class="box-title"><i class="fa fa-calendar-alt"></i> <?php echo $this->lang->line("All time report") ?> </h3>
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
							<ul class="nav nav-pills nav-stacked">
				                <li>
				                	<a href="#"><i class="fa fa-mail-bulk"></i> <?php echo $this->lang->line('Bulk message sent'); ?>
				                  		<span class="badge pull-right"><?php echo $all_time_message_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-comment-dots"></i> <?php echo $this->lang->line('Auto reply sent'); ?>
				                  		<span class="badge pull-right"><?php echo $all_time_comment_reply_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-comment-alt"></i> <?php echo $this->lang->line('Private reply sent'); ?>
				                  		<span class="badge pull-right"><?php echo $all_time_private_reply_sent; ?></span>
				                  	</a>
				              	</li>
				              	<li>
				                	<a href="#"><i class="fa fa-share-square"></i> <?php echo $this->lang->line('Post published'); ?>
				                  		<span class="badge pull-right"><?php echo $all_time_post; ?></span>
				                  	</a>
				              	</li>
				            </ul>
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
  		</div>

	</div>

	<div class="row" style="padding-top:10px;">		

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-chart-line"></i> <?php echo $this->lang->line('Last 7 days message sending report'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="chart">
						<div class="chart" id="seven_days_message_sent_chart_div" style="height: 220px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-chart-bar"></i> <?php echo $this->lang->line('Last 7 days private reply sent report'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="chart">
						<div class="chart" id="seven_days_private_reply_chart_div" style="height: 220px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-chart-area"></i> <?php echo $this->lang->line('Last 7 days comment reply sent report'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="chart">
						<div class="chart" id="seven_days_comment_reply_chart_div" style="height: 220px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>


	<div class="row" style="padding-top:10px;">
  		
  		<div class="col-md-6">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-send"></i> <?php echo $this->lang->line("Recently completed campaign (Bulk Message Campaign)") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "</pre>";

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped table-condensed'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("sl");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("created at");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("message sent");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($recently_message_sent_completed_campaing_info as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td>".$sl."</td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["added_at"]))."</td>";
				  						echo "<td class='text-center'>".$value["successfully_sent"]."</td>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='4'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>
  		
  		<div class="col-md-6">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-hourglass-start"></i> <?php echo $this->lang->line("Upcoming campaign (Bulk Message Campaign)") ?> </h3>
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped table-condensed'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("sl");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("schedule time");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("selected lead");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($upcoming_message_sent_campaign_info as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td>".$sl."</th>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["schedule_time"]))."</td>";
				  						echo "<td class='text-center'>".$value["total_thread"]."</td>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='4'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
  		</div>
	</div>

	<div class="row" style="padding-top:10px;">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-chart-area"></i> <?php echo $this->lang->line('Last seven days published post comparison'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="chart">
						<div class="chart" id="seven_days_post_publish_chart_div" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-chart-pie"></i> <?php echo $this->lang->line("Today's post publishing report"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="row" style="padding: 15px 0 15px 0;">
						<div class="col-md-8 col-xs-12">
							<div class="chart-responsive">
								<canvas id="today_post_publishing_comparison_div" height="260"></canvas>
							</div><!-- ./chart-responsive -->
						</div><!-- /.col -->
						<div class="col-md-4 col-xs-12" style="padding-top:35px;">
							<ul class="chart-legend clearfix" id="today_post_publishing_comparison_colors">
								<?php echo $today_post_publishing_comparison_li; ?>
							</ul>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

	</div>

	<div class="dashboard-arrow row">
		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="cmn">
				<div class='top-title background-a'>
					<?php echo $this->lang->line("Subscribers");?>
				</div>
				<div class='stat a'>
					<div class='icon'>
						<div class='icon-circle a'><i class='fa fa-user'></i></div>
					</div>
					<div class='number a'>
						<?php echo  $this->lang->line("Total")." : ".$total_subscribers; ?>
					</div>
				</div>
				<div class="clearfix"></div>		
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="cmn">
				<div class='top-title background-c'>
					<?php echo $this->lang->line("Auto-reply enabled campaign");?>
				</div>
				<div class='stat c'>
					<div class='icon'>
						<div class='icon-circle c'><i class='fa fa-reply-all'></i></div>
					</div>
					<div class='number c'>
						<?php echo  $this->lang->line("Total")." : ".$total_auto_reply_enabled_campaign; ?>
					</div>
				</div>
				<div class="clearfix"></div>		
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="cmn">
				<div class='top-title background-b'>
					<?php echo $this->lang->line("Scheduled bulk messaging campaign");?>
				</div>
				<div class='stat b'>
					<div class='icon'>
						<div class='icon-circle b'><i class='fa fa-mail-bulk'></i></div>
					</div>
					<div class='number b'>
						<?php echo $this->lang->line("Total")." : ".$scheduled_bulk_message_campaign; ?>
					</div>
				</div>
				<div class="clearfix"></div>		
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-3">
			<div class="cmn">
				<div class='top-title background-d'>
					<?php echo $this->lang->line("Scheduled posting campaign");?>
				</div>
				<div class='stat d'>
					<div class='icon'>
						<div class='icon-circle d'><i class='fa fa-share-square'></i></div>
					</div>
					<div class='number d'>
						<?php echo $this->lang->line("Total")." : ".$scheduled_posting_campaign; ?>
					</div>
				</div>
				<div class="clearfix"></div>		
			</div>
		</div>		

	</div> 
	<?php 
		$post_names = array(
			'image_submit' => $this->lang->line('Image Post'),
			'video_submit' => $this->lang->line('Video Post'),
			'link_submit' => $this->lang->line('Link Post'),
			'text_submit' => $this->lang->line('Text Post'),
			'carousel_post' => $this->lang->line('Carousel Post'),
			'slider_post' => $this->lang->line('Slider Post'),
			'cta_post' => $this->lang->line('CTA Post'),
		); 
	?>
	<div class="row" style="padding-top:10px;">
  		
  		<div class="col-md-6">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-send"></i> <?php echo $this->lang->line("Recently completed campaign (Facebook Poster)") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "</pre>";

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped table-condensed'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("sl");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("post type");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("published at");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($recently_completed_post_array as $key => $value) 
				  				{
				  					$post_type = '';
				  					if(isset($value['post_type'])) $post_type = $post_names[$value['post_type']];
				  					if(isset($value['cta_type'])) $post_type = $post_names['cta_post'];

				  					$sl++;
				  					echo "<tr>";
				  						echo "<td>".$sl."</td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td class='text-center'>".$post_type."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["last_updated_at"]))."</td>";
				  					echo "</tr>";
				  					if($sl == 5) break;
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='4'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>
  		
  		<div class="col-md-6">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-hourglass-start"></i> <?php echo $this->lang->line("Upcoming campaign (Facebook Poster)") ?> </h3>
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped table-condensed'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("sl");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("post type");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("schedule time");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($upcoming_post_campaign_array as $key => $value) 
				  				{
				  					$post_type = '';
				  					if(isset($value['post_type'])) $post_type = $post_names[$value['post_type']];
				  					if(isset($value['cta_type'])) $post_type = $post_names['cta_post'];
				  					
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td>".$sl."</th>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td class='text-center'>".$post_type."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["schedule_time"]))."</td>";
				  					echo "</tr>";
				  					if($sl == 5) break;
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='4'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
  		</div>
	</div>


	<div class="row" style="padding-top:10px;">
		<div class="col-md-12">
			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-list-ol"></i> <?php echo $this->lang->line("last auto reply") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 
				  				
				  				//print_r($last_campaign_completed_info);

				  				echo "</pre>";

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped table-condensed'>";

				  				echo "<tr>";
			  						echo "<th>";
			  						echo $this->lang->line("sl");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("reply to");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("reply time");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("post ID");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("post description");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($my_last_auto_reply_data as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<th>".$sl."</th>";
				  						echo "<th>".$value["name"]."</th>";
				  						echo "<th>".$value["reply_time"]."</th>";
				  						echo "<th><a target='_blank' href='https://facebook.com/".$value['id']."'>".$value["id"]."</a></th>";
				  						echo "<th>".$value["post_name"]."</th>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='4'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->		

  			
  		</div>
	</div>



</div>


<?php 
    
    $image_post = $this->lang->line('Image Post');
    $video_post = $this->lang->line('Video Post');
    $link_post = $this->lang->line('Link Post');
    $text_post = $this->lang->line('Text Post');
    $carousel_post = $this->lang->line('Carousel Post');
    $slider_post = $this->lang->line('Slider Post');
    $cta_post = $this->lang->line('CTA Post');

    $message_sent = $this->lang->line('Total Message Sent');
    $private_reply = $this->lang->line('Private reply Sent');
    $comment_reply = $this->lang->line('Comment reply Sent');

?>
<script>
	$j("document").ready(function(){
		var image_post = "<?php echo $image_post; ?>";
		var video_post = "<?php echo $video_post; ?>";
		var link_post = "<?php echo $link_post; ?>";
		var text_post = "<?php echo $text_post; ?>";
		var carousel_post = "<?php echo $carousel_post; ?>";
		var slider_post = "<?php echo $slider_post; ?>";
		var cta_post = "<?php echo $cta_post; ?>";
		var message_sent = "<?php echo $message_sent; ?>";
		var private_reply = "<?php echo $private_reply; ?>";
		var comment_reply = "<?php echo $comment_reply; ?>";
		Morris.Area({
	  		element: 'seven_days_post_publish_chart_div',
	  		data: <?php echo json_encode($seven_days_post_publish_chart_data); ?>,
	  		xkey: 'date',
	  		ykeys: ['image_post', 'video_post', 'link_post', 'text_post', 'carousel_post', 'slider_post', 'cta_post'],
	  		labels: [image_post, video_post, link_post, text_post, carousel_post, slider_post, cta_post],
	  		lineWidth: .5,
	  		pointSize: 3.5,
	  		lineColors: [<?php echo $area_chart_color_list;?>]	  		
		});

		Morris.Line({
	  		element: 'seven_days_message_sent_chart_div',
	  		data: <?php echo json_encode($seven_days_message_sent_chart_data); ?>,
	  		xkey: 'date',
	  		ykeys: ['message_sent'],
	  		labels: [message_sent],
	  		lineWidth: .5,
	  		pointSize: 3.5,
	  		lineColors: ["<?php echo $THEMECOLORCODE; ?>"]
		});

		Morris.Bar({
	  		element: 'seven_days_private_reply_chart_div',
	  		data: <?php echo json_encode($seven_days_private_reply_chart_data); ?>,
	  		xkey: 'date',
	  		ykeys: ['private_reply'],
	  		labels: [private_reply],
	  		barColors: ["<?php echo $COLOR4; ?>"]
		});

		Morris.Area({
	  		element: 'seven_days_comment_reply_chart_div',
	  		data: <?php echo json_encode($seven_days_comment_reply_chart_data); ?>,
	  		xkey: 'date',
	  		ykeys: ['comment_reply'],
	  		labels: [comment_reply],
	  		lineWidth: .5,
	  		pointSize: 3.5,
	  		lineColors: ["<?php echo $COLOR3; ?>"]
		});

		var pieOptions = {
		    //Boolean - Whether we should show a stroke on each segment
		    segmentShowStroke: true,
		    //String - The colour of each segment stroke
		    segmentStrokeColor: "#fff",
		    //Number - The width of each segment stroke
		    segmentStrokeWidth: 1,
		    //Number - The percentage of the chart that we cut out of the middle
		    percentageInnerCutout: 45, // This is 0 for Pie charts
		    //Number - Amount of animation steps
		    animationSteps: 100,
		    //String - Animation easing effect
		    animationEasing: "easeOutBounce",
		    //Boolean - Whether we animate the rotation of the Doughnut
		    animateRotate: true,
		    //Boolean - Whether we animate scaling the Doughnut from the centre
		    animateScale: false,
		    //Boolean - whether to make the chart responsive to window resizing
		    responsive: true,
		    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
		    maintainAspectRatio: false,
		    //String - A tooltip template
		    tooltipTemplate: "<%=value %> <%=label%>"
		};

		//-------------
		//- PIE CHART -
		//-------------
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#today_post_publishing_comparison_div").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = <?php echo json_encode($today_post_publishing_comparison); ?>;

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);
		//-----------------
		//- END PIE CHART -
		//-----------------

	});
</script>
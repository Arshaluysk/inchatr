<style>
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
		margin-top: 20px;
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
		padding-top: 10px;
		text-align: center;
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
		background: #dc3545;
		width: 70px;
		height: 70px;
		/* border-radius: 50%; */
		text-align: center;
		padding-top: 11px;
		color: #fff;
		font-size: 23px;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle.a {
		background: #17a2b8;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle.b {
		background: #35B084;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle.c {
		background: #35B084;
	}	

	.dashboard-arrow .cmn .stat .icon .icon-circle i {

	}	
	
	.dashboard-arrow .cmn .stat .number {
		width: 50%;
		float: left;
		color: #222;
		font-size: 20px;
		font-weight: bold;
		text-align: right;
		padding-top: 15px;
	}	

	.dashboard-arrow .cmn .stat .number.a {
		color: #17a2b8;
	}	

	.dashboard-arrow .cmn .stat .number.b {
		color: #35B084;
	}	

	.dashboard-arrow .cmn .stat .number.c {
		color: #35B084;
	}	
</style>


<br><br>
<div class="container-fluid">

	<div class="">
		<h2 class='dashboard-title'><i class='fa fa-dashboard'></i> <?php echo $this->lang->line("lifetime summary"); ?></h2>
	</div><br><br>


	<div id='dashboard-box' class="row">
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class='cmn row2-cmn'>
				<div class='icon'>
					<i class='fa fa-check'></i>
				</div>				
				<h2 class='stat'><?php echo $campaign_details_completed; ?></h2>				
				<div class='bottom-info'><h3 class='info b'><?php echo $this->lang->line("campaign completed"); ?></h3><br></div>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class='cmn row2-cmn'>
				<div class='icon bg-b'>
					<i class='fa fa-spinner'></i>
				</div>				
				<h2 class='stat color-b'><?php echo $campaign_details_processing; ?></h2>				
				<div class='bottom-info'><h3 class='info a'><?php echo $this->lang->line("campaign processing"); ?></h3><br></div>
			</div>
		</div>
		
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class='cmn row2-cmn'>
				<div class='icon bg-c'>
					<i class='fa fa-close'></i>
				</div>				
				<h2 class='stat color-c'><?php echo $campaign_details_pending; ?></h2>				
				<div class='bottom-info'><h3 class='info c'><?php echo $this->lang->line("campaign pending"); ?></h3><br></div>
			</div>
		</div>
	</div> <!-- end dashboard-box -->

	<div id='dashboard-top' class="row">
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class=" cmn bg-b">
				<div class='top-icon'>
					<div class='first-circle'>
						<div class='second-circle'>
							<div class='third-circle bg-b'>
								<i class='fa fa-user'></i>
							</div>	
						</div>
					</div>
				</div>
				<h3 class='info'><?php echo number_format($subscriber_number); ?></h3>
				<h5 class='short-info'><?php echo $this->lang->line("total subscriber");?></h5>
				<div class='more-info'><a></a></div>				
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class=" cmn bg-a">
				<div class='top-icon'>
					<div class='first-circle'>
						<div class='second-circle'>
							<div class='third-circle'>
								<i class='fa fa-close'></i>
							</div>	
						</div>
					</div>
				</div>
				<h3 class='info'><?php echo number_format($unsubscriber_number); ?></h3>
				<h5 class='short-info'><?php echo $this->lang->line("total unsubscriber");?></h5>
				<div class='more-info'><a></a></div>				
			</div>
		</div>			
		
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class=" cmn bg-c">
				<div class='top-icon'>
					<div class='first-circle'>
						<div class='second-circle'>
							<div class='third-circle bg-c'>
								<i class='fa fa-send'></i>
							</div>	
						</div>
					</div>
				</div>
				<h3 class='info'><?php echo number_format($message_number); ?></h3>
				<h5 class='short-info'><?php echo $this->lang->line("total messages sent");?></h5>
				<div class='more-info'><a></a></div>				
			</div>
		</div>
	</div> <!-- end dashboard-top -->



	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line('message sent vs campaign created report for last 12 months'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="chart">
						<div class="chart" id="div_for_bar" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>
	<?php
		$bar = $chart_bar;		
	?>
	
	<div class="row" style="padding-top:10px;">
  		
  		
  		<div class="col-md-6">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-send"></i> <?php echo $this->lang->line("recently completed campaign") ?></h3>					
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
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("created at");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("total message sent");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($last_campaign_completed_info as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td>".$sl."</td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td>".$value["added_at"]."</td>";
				  						echo "<td>".$value["successfully_sent"]."</td>";
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
					<h3 class="box-title"><i class="fa fa-hourglass-start"></i> <?php echo $this->lang->line("pending campaign") ?> </h3>
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

			  						echo "<th>";
			  						echo $this->lang->line("schedule time");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("selected message");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($last_campaign_pending_info as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td>".$sl."</th>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td>".$value["schedule_time"]."</td>";
				  						echo "<td>".$value["total_thread"]."</td>";
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

<br><br>

<div class="container-fluid">
	<div class="">
		<h2 class='dashboard-title'><i class='fa fa-calendar'></i> <?php echo $this->lang->line("monthly summary"); ?></h2>
	</div>

	<div class="dashboard-arrow row">
		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class="cmn">
				<div class='top-title background-c'>
					<?php echo $this->lang->line("campaign complete");?>
				</div>
				<div class='stat c'>
					<div class='icon'>
						<div class='icon-circle c'><i class='fa fa-2x fa-check'></i></div>
					</div>
					<div class='number c'>
						<?php echo  $this->lang->line("Total")." : ".$campaign_completed_this_month; ?>
					</div>
				</div>
				<div class="clearfix"></div>		
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class="cmn">
				<div class='top-title background-a'>
					<?php echo $this->lang->line("Total Subscriber");?>
				</div>
				<div class='stat a'>
					<div class='icon'>
						<div class='icon-circle a'><i class='fa fa-2x fa-user'></i></div>
					</div>
					<div class='number a'>
						<?php echo  $this->lang->line("Total")." : ".$subscribergained_this_month; ?>
					</div>
				</div>
				<div class="clearfix"></div>		
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-4">
			<div class="cmn">
				<div class='top-title background-b'>
					<?php echo $this->lang->line("Total messages sent");?>
				</div>
				<div class='stat b'>
					<div class='icon'>
						<div class='icon-circle b'><i class='fa fa-2x fa-comments'></i></div>
					</div>
					<div class='number b'>
						<?php echo $this->lang->line("Total")." : ".$message_number_month; ?>
					</div>
				</div>
				<div class="clearfix"></div>		
			</div>
		</div>		

	</div> 
</div>

<br><br>

<div class="container-fluid">
	<div class="">
		<h2 class='dashboard-title'><i class='fa fa-users'></i> <?php echo $this->lang->line("lead generation information"); ?></h2>
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
			  						echo $this->lang->line("post name");
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

	<div class="row" style="padding-top:10px;">
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="info-box" style="">
				<span class="info-box-icon bg-aqua"><i class="fa fa-newspaper-o" aria-hidden="true"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo $this->lang->line("auto reply enabled post");?></span>
					<span class="info-box-number">
						<?php echo $auto_reply_enable; ?>	
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="info-box" style="">
				<span class="info-box-icon bg-green"><i class="fa fa-reply-all" aria-hidden="true"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo $this->lang->line("auto reply sent");?></span>
					<span class="info-box-number">
						<?php echo $total_auto_replay; ?>
						
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="info-box" style="">
				<span class="info-box-icon bg-aqua"><i class="fa fa-comments" aria-hidden="true"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo $this->lang->line("chat plugin enabled");?></span>
					<span class="info-box-number">
						<?php echo $chat_plugin_enable; ?>
						
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
	</div>
</div>

<br><br>

<?php 
    
    $totalmessagesent = $this->lang->line("total message sent");
    $totalcampaigncreated = $this->lang->line("total campaign created");

?>
<script>
	$j("document").ready(function(){
		var totalmessagesent = "<?php echo $totalmessagesent; ?>";
		var totalcampaigncreated = "<?php echo $totalcampaigncreated; ?>";
		Morris.Bar({
	  		element: 'div_for_bar',
	  		data: <?php echo json_encode($bar); ?>,
	  		xkey: 'year',
	  		ykeys: ['sent_message', 'sent_campaign'],
	  		labels: [totalmessagesent, totalcampaigncreated],
	  		barColors: ["#FFCF75", "#FF8000"]
		});

	});
</script>
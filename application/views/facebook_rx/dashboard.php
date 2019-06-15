<div class="container-fluid">
	<div class="row" style="padding-top:10px;">
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="info-box" style="border:1px solid #DD4B39;border-bottom:2px solid #DD4B39;">
				<span class="info-box-icon bg-red"><i class="fa fa-user"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo $this->lang->line("Total Accounts");?></span>
					<span class="info-box-number">
						<?php echo number_format($account_number); ?>
						
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-newspaper-o"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo $this->lang->line("Total Pages");?></span>
					<span class="info-box-number">
						<?php echo number_format($page_number); ?>
						
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="info-box" style="border:1px solid #3C8DBC;border-bottom:2px solid #3C8DBC;">
				<span class="info-box-icon bg-blue"><i class="fa fa-group"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo $this->lang->line("Total Groups");?></span>
					<span class="info-box-number">
						<?php echo number_format($group_number); ?>						
					</span>
				</div><!-- /.info-box-content -->
			</div><!-- /.info-box -->
		</div>


		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-danger box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-video-camera"></i> Video (slider) post</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="slider_info_chart_data" value='<?php echo $slider_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="slider_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="slider_info_list_data">
									<?php echo $slider_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-danger box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-file-image-o"></i> Carousel post</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="carousel_info_chart_data" value='<?php echo $carousel_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="carousel_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="carousel_info_list_data">
									<?php echo $carousel_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-success box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-send-o"></i> Call To Action (CTA) post</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="cta_info_chart_data" value='<?php echo $cta_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="cta_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="cta_info_list_data">
									<?php echo $cta_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-success box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-clock-o"></i> Live streaming post</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="live_info_chart_data" value='<?php echo $live_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="live_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="live_info_list_data">
									<?php echo $live_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-info box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-dedent"></i> Auto Post ( Text )</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="text_info_chart_data" value='<?php echo $text_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="text_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="text_info_list_data">
									<?php echo $text_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-info box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-link"></i> Auto Post ( Link )</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="link_info_chart_data" value='<?php echo $link_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="link_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="link_info_list_data">
									<?php echo $link_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-info box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-video-camera"></i> Auto Post ( Video )</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="video_info_chart_data" value='<?php echo $video_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="video_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="video_info_list_data">
									<?php echo $video_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-md-6">			
			<!-- DONUT CHART -->
			<div class="box box-info box-solid">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-file-image-o "></i> Auto Post ( Image )</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<input type="hidden" id="image_info_chart_data" value='<?php echo $image_info_chart_data; ?>' />
								<div class="chart-responsive">
									<canvas id="image_info_chart_data_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:25px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="image_info_list_data">
									<?php echo $image_info_list_data; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


	</div>
</div>


<script>
	$j("document").ready(function(){
		var pieOptions = {
			//Boolean - Whether we should show a stroke on each segment
			segmentShowStroke: true,
			//String - The colour of each segment stroke
			segmentStrokeColor: "#fff",
			//Number - The width of each segment stroke
			segmentStrokeWidth: 2,
			//Number - The percentage of the chart that we cut out of the middle
			percentageInnerCutout: 25, // This is 0 for Pie charts
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
			maintainAspectRatio: false
	  	};

		//-------------
		//- PIE CHART -
		//-------------
		var text_info_chart_data = $("#text_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#text_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(text_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);


		//-------------
		//- PIE CHART -
		//-------------
		var link_info_chart_data = $("#link_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#link_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(link_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);


		//-------------
		//- PIE CHART -
		//-------------
		var video_info_chart_data = $("#video_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#video_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(video_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);


		//-------------
		//- PIE CHART -
		//-------------
		var image_info_chart_data = $("#image_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#image_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(image_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);


		var pieOptions = {
			//Boolean - Whether we should show a stroke on each segment
			segmentShowStroke: true,
			//String - The colour of each segment stroke
			segmentStrokeColor: "#fff",
			//Number - The width of each segment stroke
			segmentStrokeWidth: 2,
			//Number - The percentage of the chart that we cut out of the middle
			percentageInnerCutout: 50, // This is 0 for Pie charts
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
			maintainAspectRatio: false
	  	};
	  	//-------------
		//- PIE CHART -
		//-------------
		var slider_info_chart_data = $("#slider_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#slider_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(slider_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);



		//-------------
		//- PIE CHART -
		//-------------
		var carousel_info_chart_data = $("#carousel_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#carousel_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(carousel_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);


		var pieOptions = {
			//Boolean - Whether we should show a stroke on each segment
			segmentShowStroke: true,
			//String - The colour of each segment stroke
			segmentStrokeColor: "#fff",
			//Number - The width of each segment stroke
			segmentStrokeWidth: 2,
			//Number - The percentage of the chart that we cut out of the middle
			percentageInnerCutout: 0, // This is 0 for Pie charts
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
			maintainAspectRatio: false
	  	};

	  	//-------------
		//- PIE CHART -
		//-------------
		var cta_info_chart_data = $("#cta_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#cta_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(cta_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);


		//-------------
		//- PIE CHART -
		//-------------
		var live_info_chart_data = $("#live_info_chart_data").val();
		// Get context with jQuery - using jQuery's .get() method.
		var pieChartCanvas = $("#live_info_chart_data_pieChart").get(0).getContext("2d");
		var pieChart = new Chart(pieChartCanvas);
		var PieData = JSON.parse(live_info_chart_data);

		// You can switch between pie and douhnut using the method below.  
		pieChart.Doughnut(PieData, pieOptions);


	});
</script>
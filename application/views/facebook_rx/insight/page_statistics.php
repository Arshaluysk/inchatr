<link href="<?php echo base_url();?>assets/fb/facebook.css" rel="stylesheet">

<div class="container-fluid">
	<div class="row" style="padding-top:10px;">
		<div class="col-xs-12 img-container">
			<a href="">
				<img class="img-cover img-responsive" src="<?php echo $cover_image; ?>" >
				<img class="img-profile img-responsive" src="<?php echo $profile_image; ?>" >
			</a>
		</div>

		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line("page storytellers by story type");?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_storytellers_by_story_type_description; ?></div>
					<!-- <input type="hidden" id="page_storyteller_by_type_data" value='<?php echo $page_storytellers_by_story_type_data; ?>' /> -->
					<textarea class="hidden" id="page_storyteller_by_type_data" cols="30" rows="10"><?php echo $page_storytellers_by_story_type_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_storyteller_by_type_line_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<!-- <div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page impression paid vs non-paid'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="well text-center"><?php echo $page_impression_paid_vs_organic_description; ?></div>
					
					<textarea class="hidden" id="page_impression_paid_vs_organic_bar_chart_data" cols="30" rows="10"><?php echo $page_impression_paid_vs_organic_data; ?></textarea>
					<div class="chart" id="page_impression_paid_vs_organic_bar_chart" style="height: 300px;"></div>
				</div>
			</div>

		</div> -->


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page impression organic'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_impressions_organic_description; ?></div>
					<!-- <input type="hidden" id="page_impressions_organic_data" value='<?php echo $page_impressions_organic_data; ?>' /> -->
					<textarea class="hidden" id="page_impressions_organic_data" cols="30" rows="10"><?php echo $page_impressions_organic_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_impression_organic_line_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12">			
			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page impression by user country'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12"><div class="well text-center"><?php echo $page_reach_by_user_country_description; ?></div></div>
							<div class="col-md-8 col-xs-12">
								<!-- <input type="text" id="page_reach_by_user_country_data" value='<?php echo $page_reach_by_user_country_data; ?>' /> -->
								<textarea class="hidden" id="page_reach_by_user_country_data" cols="30" rows="10"><?php echo $page_reach_by_user_country_data; ?></textarea>
								<div class="chart-responsive">
									<canvas id="page_reach_by_country_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:5px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="">
									<?php echo $page_reach_country_list; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12">			
			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page storytellers by user country'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12"><div class="well text-center"><?php echo $page_storytellers_by_country_description; ?></div></div>
							<div class="col-md-8 col-xs-12">
								<!-- <input type="hidden" id="page_storytellers_by_country_data" value='<?php echo $page_storytellers_by_country_data; ?>' /> -->
								<textarea class="hidden" id="page_storytellers_by_country_data" cols="30" rows="10"><?php echo $page_storytellers_by_country_data; ?></textarea>
								<div class="chart-responsive">
									<canvas id="page_storytellers_by_country_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:5px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="page_storytellers_county_list">
									<?php echo $page_storyteller_country_list; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12 col-sm-12 col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page impression by user city'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="well text-center"><?php echo $page_reach_by_user_city_description; ?></div>
					<div class="table-responsive" style="height:300px;">
						<?php echo $page_reach_by_user_city_data; ?>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12 col-sm-12 col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page storyteller by user city'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="well text-center"><?php echo $page_storyteller_by_user_city_description; ?></div>
					<div class="table-responsive" style="height:300px;">
						<?php echo $page_storyteller_by_user_city_data; ?>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page engaged users'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_engaged_user_description; ?></div>
					<!-- <input type="hidden" id="page_engaged_user_data" value='<?php echo $page_engaged_user_data; ?>' /> -->
					<textarea class="hidden" id="page_engaged_user_data" cols="30" rows="10"><?php echo $page_engaged_user_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_engaged_user_line_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page consumptions by type'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_consumptions_description; ?></div>
					<!-- <input type="hidden" id="page_consumptions_data" value='<?php echo $page_consumptions_data; ?>' /> -->
					<textarea class="hidden" id="page_consumptions_data" cols="30" rows="10"><?php echo $page_consumptions_data; ?></textarea>
					<div class="chart">
						<div class="chart tab-pane active" id="page_consumptions_line_chart" style="position: relative;height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page positive feedback by type'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_positive_feedback_by_type_description; ?></div>
					<!-- <input type="hidden" id="page_positive_feedback_by_type_data" value='<?php echo $page_positive_feedback_by_type_data; ?>' /> -->
					<textarea class="hidden" id="page_positive_feedback_by_type_data" cols="30" rows="10"><?php echo $page_positive_feedback_by_type_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_positive_feedback_by_type_line_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page negative feedback by type'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_negative_feedback_by_type_description; ?></div>
					<!-- <input type="hidden" id="page_negative_feedback_by_type_data" value='<?php echo $page_negative_feedback_by_type_data; ?>' /> -->
					<textarea class="hidden" id="page_negative_feedback_by_type_data" cols="30" rows="10"><?php echo $page_negative_feedback_by_type_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_negative_feedback_by_type_line_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page fans online per day'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_fans_online_per_day_description; ?></div>
					<!-- <input type="hidden" id="page_fans_online_per_day_data" value='<?php echo $page_fans_online_per_day_data; ?>' /> -->
					<textarea class="hidden" id="page_fans_online_per_day_data" cols="30" rows="10"><?php echo $page_fans_online_per_day_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_fans_online_per_day_line_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">			
			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page fans by like source'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12"><div class="well text-center"><?php echo $page_fans_by_like_source_description; ?></div></div>
							<div class="col-md-8 col-xs-12">
								<!-- <input type="hidden" id="page_fans_by_like_source_data" value='<?php echo $page_fans_by_like_source_data; ?>' /> -->
								<textarea class="hidden" id="page_fans_by_like_source_data" cols="30" rows="10"><?php echo $page_fans_by_like_source_data; ?></textarea>
								<div class="chart-responsive">
									<canvas id="page_fans_by_like_source_pieChart" height="250"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:5px;height:250px;overflow:auto;">
								<ul class="chart-legend clearfix" id="">
									<?php echo $page_fans_by_like_source_list; ?>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page likes vs unlikes'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<!-- <input type="hidden" id="page_fans_adds_and_remove_data" value='<?php echo $page_fans_adds_and_remove_data; ?>' /> -->
					<textarea class="hidden" id="page_fans_adds_and_remove_data" cols="30" rows="10"><?php echo $page_fans_adds_and_remove_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_fans_adds_and_remove_bar_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>

		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page posts impressions'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="well text-center"><?php echo $page_posts_impressions_description; ?></div>
					<!-- <input type="hidden" id="page_posts_impressions_data" value='<?php echo $page_posts_impressions_data; ?>' /> -->
					<textarea class="hidden" id="page_posts_impressions_data" cols="30" rows="10"><?php echo $page_posts_impressions_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_posts_impressions_line_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12" style="padding-top:20px;">
			<!-- AREA CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page posts impressions paid vs organic'); ?></h3>
					<div class="well text-center"><?php echo $page_post_impression_pais_vs_organic_description; ?></div>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">
					<!-- <input type="hidden" id="page_post_impression_paid_vs_organic_data" value='<?php echo $page_post_impression_paid_vs_organic_data; ?>' /> -->
					<textarea class="hidden" id="page_post_impression_paid_vs_organic_data" cols="30" rows="10"><?php echo $page_post_impression_paid_vs_organic_data; ?></textarea>
					<div class="chart">
						<div class="chart" id="page_post_impression_paid_vs_organic_bar_chart" style="height: 300px;"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12 col-sm-12 col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page tab views'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="well text-center"><?php echo $page_tab_views_description; ?></div>
					<div class="table-responsive" style="height:300px;">
						<?php echo $page_tab_views_data; ?>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


		<div class="col-xs-12 col-sm-12 col-md-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: blue; word-spacing: 4px;"> <?php echo $this->lang->line('page views by external referral'); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<div class="well text-center"><?php echo $page_views_external_referrals_description; ?></div>
					<div class="table-responsive" style="height:300px;">
						<?php echo $page_views_external_referrals_data; ?>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>


	</div>
</div>

<script>
$j("document").ready(function(){
	// LINE CHART
	var page_storyteller_by_type_data = $('#page_storyteller_by_type_data').val();
    var line = new Morris.Line({
      element: 'page_storyteller_by_type_line_chart',
      resize: true,
      data: JSON.parse(page_storyteller_by_type_data),
      xkey: 'date',
      ykeys: ['fan','user post','page post','coupon','mention','checkin','question','event','other'],
      labels: ['Fan','User post','Page post','Coupon','Mention','Checkin','Question','Event','Other'],
      lineColors: ['#EF05B6','#B1092D','#037F61','#8A2A3D','#E3C303','#C4493A','#0CC67D','#C9A200','#EAA993'],
      lineWidth: 1,
      hideHover: 'auto'
    });


	// BAR CHART
    // var page_impression_paid_vs_organic_bar_chart_data = $('#page_impression_paid_vs_organic_bar_chart_data').val();
    // var bar = new Morris.Bar({
    //   element: 'page_impression_paid_vs_organic_bar_chart',
    //   resize: true,
    //   data: JSON.parse(page_impression_paid_vs_organic_bar_chart_data),
    //   barColors: ['#00CA7A', '#C94536'],
    //   xkey: 'date',
    //   ykeys: ['paid', 'organic'],
    //   labels: ['Paid Impression', 'Non-Paid Impression'],
    //   hideHover: 'auto'
    // });


    // LINE CHART
	var page_impressions_organic_data = $('#page_impressions_organic_data').val();
    var line = new Morris.Line({
      element: 'page_impression_organic_line_chart',
      resize: true,
      data: JSON.parse(page_impressions_organic_data),
      xkey: 'date',
      ykeys: ['organic'],
      labels: ['Organic Impression'],
      lineColors: ['#78ED78'],
      lineWidth: 1,
      hideHover: 'auto'
    });



	//-------------
	//- PIE CHART -
	//-------------
	var page_storytellers_by_country_data = $("#page_storytellers_by_country_data").val();
	// Get context with jQuery - using jQuery's .get() method.
	var pieChartCanvas = $("#page_storytellers_by_country_pieChart").get(0).getContext("2d");
	var pieChart = new Chart(pieChartCanvas);
	var PieData = JSON.parse(page_storytellers_by_country_data);

	var pieOptions = {
      //Boolean - Whether we should show a stroke on each segment
      segmentShowStroke: true,
      //String - The colour of each segment stroke
      segmentStrokeColor: "#fff",
      //Number - The width of each segment stroke
      segmentStrokeWidth: 2,
      //Number - The percentage of the chart that we cut out of the middle
      percentageInnerCutout: 5, // This is 0 for Pie charts
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

	// You can switch between pie and douhnut using the method below.  
	pieChart.Doughnut(PieData, pieOptions);
	//-----------------
	//- END PIE CHART -
	//-----------------



	//-------------
	//- PIE CHART -
	//-------------
	var page_reach_by_user_country_data = $("#page_reach_by_user_country_data").val();
	// Get context with jQuery - using jQuery's .get() method.
	var pieChartCanvas = $("#page_reach_by_country_pieChart").get(0).getContext("2d");
	var pieChart = new Chart(pieChartCanvas);
	var PieData = JSON.parse(page_reach_by_user_country_data);

	// You can switch between pie and douhnut using the method below.  
	pieChart.Doughnut(PieData, pieOptions);
	//-----------------
	//- END PIE CHART -
	//-----------------



	// LINE CHART
	var page_engaged_user_data = $('#page_engaged_user_data').val();
    var line = new Morris.Line({
      element: 'page_engaged_user_line_chart',
      resize: true,
      data: JSON.parse(page_engaged_user_data),
      xkey: 'date',
      ykeys: ['value'],
      labels: ['Engaged Users'],
      lineColors: ['#9040CE'],
      lineWidth: 1,
      hideHover: 'auto'
    });


    // LINE CHART
	var page_consumptions_data = $('#page_consumptions_data').val();
    var line = new Morris.Area({
      element: 'page_consumptions_line_chart',
      resize: true,
      data: JSON.parse(page_consumptions_data),
      xkey: 'date',
      ykeys: ['link clicks','photo view','video play','other clicks'],
      labels: ['Link Clicks','Photo View','Video Play','Other Clicks'],
      lineColors: ['#00AAA0','#E5C77B','#087F24','#DD5D18'],
      lineWidth: 1,
      hideHover: 'auto'
    });



    // LINE CHART
	var page_positive_feedback_by_type_data = $('#page_positive_feedback_by_type_data').val();
    var line = new Morris.Line({
      element: 'page_positive_feedback_by_type_line_chart',
      resize: true,
      data: JSON.parse(page_positive_feedback_by_type_data),
      xkey: 'date',
      ykeys: ['answer','claim','comment','like','link','rsvp'],
      labels: ['Answer','Claim','Comment','Like','Link','Rsvp'],
      lineColors: ['#8A2A3D','#E3C303','#C4493A','#0CC67D','#C9A200','#EAA993'],
      lineWidth: 1,
      hideHover: 'auto'
    });



    // LINE CHART
	var page_negative_feedback_by_type_data = $('#page_negative_feedback_by_type_data').val();
    var line = new Morris.Line({
      element: 'page_negative_feedback_by_type_line_chart',
      resize: true,
      data: JSON.parse(page_negative_feedback_by_type_data),
      xkey: 'date',
      ykeys: ['hide_clicks','report_spam_clicks','unlike_page_clicks','hide_all_clicks','xbutton_clicks'],
      labels: ['Hide Clicks','Report Spam Clicks','Unlike Page Clicks','Hide All Clicks','Xbutton Clicks'],
      lineColors: ['#44B3C2','#F1A94E','#E45641','#5D4C46','#7B8D8E'],
      lineWidth: 1,
      hideHover: 'auto'
    });


    // LINE CHART
	var page_fans_online_per_day_data = $('#page_fans_online_per_day_data').val();
    var line = new Morris.Line({
      element: 'page_fans_online_per_day_line_chart',
      resize: true,
      data: JSON.parse(page_fans_online_per_day_data),
      xkey: 'date',
      ykeys: ['value'],
      labels: ['Fans'],
      lineColors: ['#798C0E'],
      lineWidth: 1,
      hideHover: 'auto'
    });



    // BAR CHART
    var page_fans_adds_and_remove_data = $('#page_fans_adds_and_remove_data').val();
    var bar = new Morris.Bar({
      element: 'page_fans_adds_and_remove_bar_chart',
      resize: true,
      data: JSON.parse(page_fans_adds_and_remove_data),
      barColors: ['#7A3E48', '#EECD86'],
      xkey: 'date',
      ykeys: ['adds', 'removes'],
      labels: ['Likes', 'Unlikes'],
      hideHover: 'auto'
    });



    //-------------
	//- PIE CHART -
	//-------------
	var page_fans_by_like_source_data = $("#page_fans_by_like_source_data").val();
	// Get context with jQuery - using jQuery's .get() method.
	var pieChartCanvas = $("#page_fans_by_like_source_pieChart").get(0).getContext("2d");
	var pieChart = new Chart(pieChartCanvas);
	var PieData = JSON.parse(page_fans_by_like_source_data);

	// You can switch between pie and douhnut using the method below.  
	pieChart.Doughnut(PieData, pieOptions);
	//-----------------
	//- END PIE CHART -
	//-----------------



	// LINE CHART
	var page_posts_impressions_data = $('#page_posts_impressions_data').val();
    var line = new Morris.Line({
      element: 'page_posts_impressions_line_chart',
      resize: true,
      data: JSON.parse(page_posts_impressions_data),
      xkey: 'date',
      ykeys: ['value'],
      labels: ['Impressions'],
      lineColors: ['#FF8B6B'],
      lineWidth: 1,
      hideHover: 'auto'
    });



    // BAR CHART
    var page_post_impression_paid_vs_organic_data = $('#page_post_impression_paid_vs_organic_data').val();
    var bar = new Morris.Bar({
      element: 'page_post_impression_paid_vs_organic_bar_chart',
      resize: true,
      data: JSON.parse(page_post_impression_paid_vs_organic_data),
      barColors: ['#FFFF66', '#009966'],
      xkey: 'date',
      ykeys: ['paid', 'organic'],
      labels: ['Paid Impressions', 'Organic Impressions'],
      hideHover: 'auto'
    });


});
</script>
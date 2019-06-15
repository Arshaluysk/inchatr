<?php 
	if(isset($error))
	echo "<br/><div class='alert alert-danger text-center'>".$error."</div>";
	else
	{
?>
<?php 
$a = array(
	'post_video_retention_graph_clicked_to_play',
	'post_video_retention_graph_autoplayed',
	'post_video_retention_graph',

	'post_video_views',
	'post_video_views_paid',
	'post_video_complete_views_organic_unique',
	'post_video_views_organic_unique',

	'post_interests_impressions_unique',
	'post_interests_impressions',
	'post_interests_consumptions_unique',
	'post_interests_consumptions',
	'post_interests_consumptions_by_type_unique',
	'post_interests_consumptions_by_type',
	'post_interests_action_by_type_unique',
	'post_interests_action_by_type',

	'post_video_views_10s',
	'post_video_views_10s_unique',
	'post_video_views_10s_paid',
	'post_video_views_10s_paid',
	'post_video_views_10s_organic',
	'post_video_views_10s_clicked_to_play',
	'post_video_views_10s_autoplayed',
	'post_video_views_10s_sound_on',

	'post_video_view_time',
	'post_video_view_time_paid',
	'post_video_view_time_organic',

	'post_video_view_time_by_region_id',

	'post_video_complete_views_30s_paid',
	'post_video_complete_views_30s_organic',
	'post_video_complete_views_30s_clicked_to_play',
	'post_video_complete_views_30s_unique',
	'post_video_complete_views_30s',
	'post_video_complete_views_paid_unique',
	'post_video_views_paid_unique',
	'post_impressions_by_paid_non_paid_unique',
	'post_consumptions_by_type'
	);
	
	$color_array = array("#5832BA","#FC5B20","#EDED28","#E27263","#E5C77B","#B7F93B","#A81538", "#087F24","#9040CE","#872904","#DD5D18","#FBFF0F");
?>
<style>
	.title_text {
		font-weight: bold;
		font-size: 15px;
		color: #273C67;
	}
	.desc_text {
		font-size: italic;
		font-weight: bold;
		cursor: pointer;
	}
	.info-box-icon {
	    border-top-left-radius: 2px;
	    border-top-right-radius: 0;
	    border-bottom-right-radius: 0;
	    border-bottom-left-radius: 2px;
	    display: block;
	    float: left;
	    height: 87px;
	    width: 90px;
	    text-align: center;
	    font-size: 40px;
	    line-height: 87px;
	    background: rgba(242,242,242,1);
	}
</style>

<div class="row" style="padding: 15px;">
	
	<?php $j = 0; foreach($post_analytics as $value) :	?>
		<?php 
			$reaction_type = array();
			$reaction_str = '';
			if($value['name'] == 'post_impressions_by_paid_non_paid_unique') : 
				$i = 0;
				foreach($value['values'][0]['value'] as $key=>$reaction)
				{
					$reaction_type[$i]['value'] = $reaction;
	                $reaction_type[$i]['color'] = $color_array[$i];
	                $reaction_type[$i]['highlight'] = $color_array[$i];
	                $reaction_type[$i]['label'] = $key;

	                $reaction_str .= '<li><i class="fa fa-circle-o" style="color: '.$color_array[$i].';"></i> '.$key.' : '.$reaction.'</li>';
	                $i++;
				}
		?>
			<div class="col-xs-12 col-sm-12 col-md-6" style="padding-top:20px;">
				<!-- AREA CHART -->
				<div class="box box-primary">
					<div class="box-header with-border">
					<h3 class="box-title" style="color: #3C8DBC; word-spacing: 4px;"> <?php echo $value['title'] ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12"><div class="well text-center"><?php echo $value['description']; ?></div></div>
									<div class="col-md-8 col-xs-12">
										<input type="hidden" id="<?php echo $value['name']; ?>" value='<?php echo json_encode($reaction_type); ?>' />
										<div class="chart-responsive">
											<canvas id="pieChart_<?php echo $value['name'];?>" height="250"></canvas>
										</div><!-- ./chart-responsive -->
									</div><!-- /.col -->
									<div class="col-md-4 col-xs-12" style="padding-top:5px;height:250px;overflow:auto;">
										<ul class="chart-legend clearfix" id="">
											<?php echo $reaction_str; ?>
										</ul>
									</div><!-- /.col -->
								</div><!-- /.row -->
							</div>
						</div>
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
			          percentageInnerCutout: 20, // This is 0 for Pie charts
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




					// Get context with jQuery - using jQuery's .get() method.
					var chart_id = "<?php echo $value['name']; ?>";
					var chart_div_name = "pieChart_"+chart_id;
			        var pieChartCanvas = $("#"+chart_div_name).get(0).getContext("2d");
			        var pieChart = new Chart(pieChartCanvas);
			        var gender_percentage = $("#"+chart_id).val();
			        var PieData = JSON.parse(gender_percentage);
			        //Create pie or douhnut chart
			        // You can switch between pie and douhnut using the method below.
			        pieChart.Doughnut(PieData, pieOptions);
					/******************************/

				});
			</script>
		<?php endif; ?>

		<?php
			$color_array_reverse = array_reverse($color_array);
			$reaction_type = array();
			$reaction_str = '';
			if($value['name'] == 'post_consumptions_by_type') : 
				$i = 0;
				foreach($value['values'][0]['value'] as $key=>$reaction)
				{
					$reaction_type[$i]['value'] = $reaction;
	                $reaction_type[$i]['color'] = $color_array_reverse[$i];
	                $reaction_type[$i]['highlight'] = $color_array_reverse[$i];
	                $reaction_type[$i]['label'] = $key;

	                $reaction_str .= '<li><i class="fa fa-circle-o" style="color: '.$color_array_reverse[$i].';"></i> '.$key.' : '.$reaction.'</li>';
	                $i++;
				}
		?>
			<div class="col-xs-12 col-sm-12 col-md-6" style="padding-top:20px;">
				<!-- AREA CHART -->
				<div class="box box-primary">
					<div class="box-header with-border">
					<h3 class="box-title" style="color: #3C8DBC; word-spacing: 4px;"> <?php echo $value['title'] ?></h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12"><div class="well text-center"><?php echo $value['description']; ?></div></div>
									<div class="col-md-8 col-xs-12">
										<input type="hidden" id="<?php echo $value['name']; ?>" value='<?php echo json_encode($reaction_type); ?>' />
										<div class="chart-responsive">
											<canvas id="pieChart_<?php echo $value['name'];?>" height="250"></canvas>
										</div><!-- ./chart-responsive -->
									</div><!-- /.col -->
									<div class="col-md-4 col-xs-12" style="padding-top:5px;height:250px;overflow:auto;">
										<ul class="chart-legend clearfix" id="">
											<?php echo $reaction_str; ?>
										</ul>
									</div><!-- /.col -->
								</div><!-- /.row -->
							</div>
						</div>
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
			          percentageInnerCutout: 20, // This is 0 for Pie charts
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




					// Get context with jQuery - using jQuery's .get() method.
					var chart_id = "<?php echo $value['name']; ?>";
					var chart_div_name = "pieChart_"+chart_id;
			        var pieChartCanvas = $("#"+chart_div_name).get(0).getContext("2d");
			        var pieChart = new Chart(pieChartCanvas);
			        var gender_percentage = $("#"+chart_id).val();
			        var PieData = JSON.parse(gender_percentage);
			        //Create pie or douhnut chart
			        // You can switch between pie and douhnut using the method below.
			        pieChart.Doughnut(PieData, pieOptions);
					/******************************/

				});
			</script>
		<?php endif; ?>

	<?php endforeach; ?>

	<div class="row"></div>
	<div class="row"  style="padding:0 15px;">
	<?php $i=0; foreach($post_analytics as $value) :	
		if(in_array($value['name'], $a)) continue;
		
		if(is_array($value['values'][0]['value'])) : 
	?>

			<div class="col-xs-12 col-sm-12 col-md-4">
				<div class="box box-info box-solid">
					<div class="box-header with-border">
					<h3 class="box-title" data-toggle="tooltip" title="<?php echo $value['title'];?>" >
						<?php if(strlen($value['title']) > 40) $title = substr($value['title'], 0, 40).'..';
								else $title = $value['title'];
								echo $title;
						?>
					</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body">
					<?php 
						if(is_array($value['values'][0]['value']) && !empty($value['values'][0]['value'])) 
						{
							foreach($value['values'][0]['value'] as $key=>$result)
							{
								echo "<div class='col-xs-6'><b>".$key."</b> : ".number_format($result)."</div>";
							}
						}
					?>
						<div class="col-xs-12">
							<p desc="<?php echo $value['description'];?>" class="desc_text label label-info"><?php echo $this->lang->line("what is this?") ?></p>
						</div>
					</div>
				</div>
			</div>
		<?php 
			$i++;
			if($i%3 == 0)
				echo "</div><div class='row'  style='padding:0 15px;'>";
			endif;
		?>
		
	<?php endforeach; ?>
	</div>

	<div class="row"></div>
	<?php foreach($post_analytics as $value) :	?>
		<?php 
		if(in_array($value['name'], $a)) continue;
		?>
		<?php if(!is_array($value['values'][0]['value'])) : ?>
			<div class="col-xs-12 col-sm-12 col-md-4">
				<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
					<span class="info-box-icon"><?php echo "#"; ?></span>
					<div class="info-box-content">
						<span class="title_text" data-toggle="tooltip" title="<?php echo $value['title'];?>" >
							<?php
								if(strlen($value['title']) > 30) $title = substr($value['title'], 0, 30).'..';
								else $title = $value['title'];
								echo $title;
							?>							
						</span>
						<span class="info-box-number"><?php if(!is_array($value['values'][0]['value'])) echo number_format($value['values'][0]['value']); ?></span>
						<p desc="<?php echo $value['description'];?>" class="desc_text label label-info"><?php echo $this->lang->line("what is this?") ?></p>
					</div><!-- /.info-box-content -->
				</div><!-- /.info-box -->
			</div>
		<?php endif; ?>
		
	<?php endforeach; ?>
</div>


<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
	$j(document).ready(function(){
	    $(".desc_text").click(function(){
	    	var desc = $(this).attr('desc');
	    	$("#video_desc_modal").modal();
	    	$("#video_desc_modal_body").html("<h5><div class='alert alert-success text-center'>"+desc+"</div></h5>");
	    });

	});
</script>


<div class="modal fade" id="video_desc_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-newspaper-o"></i> <?php echo $this->lang->line("description") ?></h4>
            </div>
            <div class="modal-body" id="video_desc_modal_body">                

            </div>
        </div>
    </div>
</div>

<?php } ?>
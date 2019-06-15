<div class="well well_border_left">
	<h4 class="text-center"> <i class="fa fa-dashboard"></i> <?php echo $this->lang->line("Recent Activity"); ?></h4>
</div>
<div class="container-fluid" style="margin-top: 30px;">

	<div class="row">

		<div class="col-xs-12">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-share-square"></i> <?php echo $this->lang->line("Facebook Poster Campaign") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("User Name");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("User Email");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("post type");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("post URL");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Last update time");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($facebook_poster as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td>".$value["post_type"]."</td>";
				  						echo "<td class='text-center'><a href='".$value["post_url"]."' target='_blank'>".$value["post_url"]."</a></td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["last_updated_at"]))."</td>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='6'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>
  		
  		<div class="col-xs-12">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-send"></i> <?php echo $this->lang->line("Bulk Message Campaign") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("User Name");
			  						echo "</th>";

				  					echo "<th>";
			  						echo $this->lang->line("User Email");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("created at");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("scheduled at");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("message to sent");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($bulk_message_campaign as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["added_at"]))."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["schedule_time"]))."</td>";
				  						echo "<td class='text-center'>".$value["total_thread"]."</td>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='6'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>

  		<div class="col-xs-12">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-reply-all"></i> <?php echo $this->lang->line("Auto reply Campaign") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("User Name");
			  						echo "</th>";

				  					echo "<th>";
			  						echo $this->lang->line("User Email");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Post ID");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Total comment reply");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Total private reply sent");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Last reply time");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($autoreply_campaign as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td><a target='_blank' href='https://facebook.com/".$value['post_id']."'>".$value["post_id"]."</a></td>";
				  						echo "<td class='text-center'>".$value["auto_comment_reply_count"]."</td>";
				  						echo "<td class='text-center'>".$value["auto_private_reply_count"]."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["last_reply_time"]))."</td>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='7'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>

  		<?php if($this->basic->is_exist("add_ons",array("project_id"=>21))) : ?>
  		<div class="col-xs-12">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-tv"></i> <?php echo $this->lang->line("Facebook Live Campaign") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("User Name");
			  						echo "</th>";

				  					echo "<th>";
			  						echo $this->lang->line("User Email");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Post URL");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Posting Status");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Last update time");
			  						echo "</th>";

			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($vidcaster_campaign as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td class='text-center'>".$value["post_url"]."</td>";
				  						echo "<td class='text-center'>".$value["posting_status"]."</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["last_updated_at"]))."</td>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='6'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>
  		<?php endif; ?>

  		<?php if($this->basic->is_exist("add_ons",array("project_id"=>20))) : ?>
  		<div class="col-xs-12">
  			<!-- DONUT CHART -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-tasks"></i> <?php echo $this->lang->line("Comboposter Campaign") ?></h3>					
				</div>
				<div class="box-body chart-responsive" style="display: block;">
					<div class="box-body">
						<div class="row">
							<?php 

				  				echo "<div class='table-responsive'><table class='table table-bordered table-hover table-striped'>";

				  				echo "<tr>";
				  					echo "<th>";
			  						echo $this->lang->line("User Name");
			  						echo "</th>";

				  					echo "<th>";
			  						echo $this->lang->line("User Email");
			  						echo "</th>";

			  						echo "<th>";
			  						echo $this->lang->line("campaign name");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Post Type");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Schedule time");
			  						echo "</th>";

			  						echo "<th class='text-center'>";
			  						echo $this->lang->line("Posting Status");
			  						echo "</th>";
			  					echo "</tr>";

				  				$sl=0;
				  				foreach ($comboposter_campaign as $key => $value) 
				  				{
				  					$sl++;
				  					echo "<tr>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_name"]."</a></td>";
				  						echo "<td><a href='".base_url('admin/user_dashboard/').$value['user_id']."' target='_blank'>".$value["user_email"]."</a></td>";
				  						echo "<td>".$value["campaign_name"]."</td>";
				  						echo "<td class='text-center'>".$value["post_type"]." Post</td>";
				  						echo "<td class='text-center'>".date("d M y H:i",strtotime($value["schedule_time"]))."</td>";
				  						echo "<td class='text-center'>".$value["posting_status"]."</td>";
				  					echo "</tr>";
				  				}
				  				if($sl==0) echo "<tr><td class='text-center' colspan='6'>No data found.</td></tr>";
				  				echo "</table></div>";
				  			?>
						</div><!-- /.row -->
					</div><!-- /.box-body -->
				</div><!-- /.box-body -->
			</div><!-- /.box -->	
  		</div>
  		<?php endif; ?>
  		
	</div>

</div>

<style>
	.box-body {
	     padding-top: 20px; 
	     padding-left: 25px !important;
	     padding-right: 25px !important;
	}
</style>
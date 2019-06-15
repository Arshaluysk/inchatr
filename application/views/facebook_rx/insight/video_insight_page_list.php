<style>
	.padded_row {
		padding-left: 20px;
		padding-right: 20px;
	}
	.padded_row2 {
		padding-left: 35px;
		padding-right: 35px;
	}
	.content_wrapper {
		border: 1px solid #ccc;
		margin-bottom: 10px;
		padding-bottom: 15px;
		background: #fff;
	}
	.page_name {
		padding-top: 10px;
		font-weight: bold;
		font-size: 20px;
		color: #3B5998;
	}
	.wrapper,.content-wrapper{background: #fafafa !important;}
	.well{background: #fff;}
</style>
<div class="row padded_row2">
	<h4>			
		<div class="well text-center">
			<i class="fa fa-facebook-official"></i> <?php echo $this->lang->line("video insight : page list");?>
		</div>
	</h4>
</div>

<div class="row padded_row">
	<?php foreach($fb_page_info as $value) : ?>
		<div class="col-xs-12 col-sm-12 col-md-4">
			<div class="content_wrapper">
				<p class="page_name text-center" data-toggle="tooltip" title="<?php echo $value['page_name']; ?>">
					<?php 
						if(strlen($value['page_name']) > 34) $page_name = substr($value['page_name'], 0, 34).'..';
						else $page_name = $value['page_name'];
						echo $page_name; 
					?>					
				</p>

				<div class="row text-center">
					<img src="<?php echo $value['page_profile']; ?>" alt="Image" height="100" width="100" class='img-thumbnail'>
				</div>
				<br/>
				<div class="row text-center">
					<button class="btn btn-sm btn-primary synch_video" page_name="<?php echo $value['page_name']; ?>" table_id="<?php echo $value['id']; ?>"><i class="fa fa-retweet"></i> <?php echo $this->lang->line("sync video");?></button>
					<a href="<?php echo base_url('facebook_rx_insight/video_list_grid/'.$value['id']); ?>" class="btn btn-sm btn-info"><i class="fa fa-th-large"></i> <?php echo $this->lang->line("video list");?></a>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?
	$doyouwanttosyncvideos = "Do you want to sync videos for this page?";
?>


<script>
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip();
	});
	$j(document).ready(function(){

		var base_url = "<?php echo base_url(); ?>";
		var doyouwanttosyncvideos = "<?php echo $doyouwanttosyncvideos;?>";

		$(".synch_video").click(function(){
			var ans = confirm(doyouwanttosyncvideos);
			if(ans)
			{
				var page_name = $(this).attr('page_name');
				$("#video_synch_modal").modal();
				$("#page_name_div").html(page_name);
				$("#video_synch_modal_body").html('<img class="center-block" src="'+base_url+'assets/pre-loader/custom_lg.gif" alt="Processing..."><br/>');
				var page_table_id = $(this).attr('table_id');
				$.ajax
				({
				   type:'POST',
				   // async:false,
				   url:base_url+'facebook_rx_insight/synch_videos_for_page',
				   data:{page_table_id:page_table_id},
				   success:function(response)
				    {
				       $("#video_synch_modal_body").html(response);
				    }
				       
				});
			}
		});

		$('#video_synch_modal').on('hidden.bs.modal', function () { 
			location.reload(); 
		});
	});
</script>


<div class="modal fade" id="video_synch_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-spinner"></i> <?php echo $this->lang->line("sync videos for page");?> - <span id="page_name_div"></span></h4>
            </div>
            <div class="modal-body text-center" id="video_synch_modal_body">                

            </div>
        </div>
    </div>
</div>
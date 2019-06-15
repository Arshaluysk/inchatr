<div class="row" style="padding: 20px;">
	<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3 well">
		<div class="text-center">
			<p><b>You have <?php echo $total_files; ?> junk files (<?php echo $total_file_size." KB"; ?>)</b></p>
		</div>
		<div class="text-center">			
			<a class="btn btn-warning btn-lg" id="click_to_delete">Click Here To Delete All Junk Files</a>
		</div>
		<br/><br/>
		<div id="div_for_waiting_div" class="text-center"></div>
	</div>
</div>

<script>
	$(document.body).on('click','#click_to_delete',function(){
		var base_url="<?php echo base_url(); ?>";
		$('#div_for_waiting_div').html('<img class="center-block" style="" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="<?php echo $this->lang->line("please wait"); ?>">');
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('admin/delete_junk_file_action'); ?>",
				data:{},
				dataType: '',
				async: false,
				success:function(response){
					// $('#div_for_waiting_div').html('');
					$('#div_for_waiting_div').html(response);			
				}
			});
	});
</script>
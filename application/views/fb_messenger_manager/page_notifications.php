<style>
	.refresh_button_holder
	{
		padding: 0px;
		margin-top: 5px; 
		margin-bottom:5px;
	}
	.header_title{
		margin: 0px;
		padding: 0px;
		color: #3C8DBC;
	}
</style>

<div class="row" style="padding: 10px; padding-top: 0px;">
	<div class="col-xs-12">
		<div id="notification_div"></div>
		<br/>
	</div>
</div>

<script>
	$j("document").ready(function(){
		var base_url="<?php echo base_url(); ?>";
		var loading = '<br><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';

		$("#notification_div").html(loading);

    	function ajax_call()
    	{    		
	    	$.ajax({
					url:base_url+'fb_msg_manager_notification/get_pages_notification',
					type:'POST',
					data:{},
					success:function(response){
						$("#notification_div").html(response);			
					}
					
				});	    	
    	}

    	$(document.body).on('click','#refresh_button_notification',function(){  
    		$("#notification_div").html(loading);		
	    	ajax_call();
    	});

    	ajax_call();
    	


	});
</script>
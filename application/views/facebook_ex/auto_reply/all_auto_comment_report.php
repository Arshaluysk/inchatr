<?php 
	$this->load->view("include/upload_js"); 
	if(ultraresponse_addon_module_exist())	$commnet_hide_delete_addon = 1;
	else $commnet_hide_delete_addon = 0;

	if(addon_exist(201,"commenttagmachine")) $comment_tag_machine_addon = 1;
	else $comment_tag_machine_addon = 0;		
	$report_page_name=urldecode($this->uri->segment(3));

	$image_upload_limit = 1; 
	if($this->config->item('autoreply_image_upload_limit') != '')
	$image_upload_limit = $this->config->item('autoreply_image_upload_limit'); 

	$video_upload_limit = 3; 
	if($this->config->item('autoreply_video_upload_limit') != '')
	$video_upload_limit = $this->config->item('autoreply_video_upload_limit');
?>
<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'> <i class='fa fa-list-alt'></i> <?php echo $this->lang->line("auto comment report") ?> </h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; height:785px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."facebook_ex_auto_comment/all_auto_reply_report_data"; ?>" 

				pagination="true" 
				rownumbers="true" 
				toolbar="#tb" 
				pageSize="10" 
				pageList="[5,10,15,20,50,100]"  
				fit= "true" 
				fitColumns= "true" 
				nowrap= "true" 
				view= "detailview"
				idField="id"
				>				

				<thead>
					<tr>
						<th field="post_thumb"><?php echo $this->lang->line("Thumbnail")?></th>
						<th field="campaign_name" sortable="true"><?php echo $this->lang->line("Campaign Name")?></th>
						<th field="page_name" sortable="true"><?php echo $this->lang->line("Page Name")?></th>
						<th field="post_id" sortable="true" sortable="true"><?php echo $this->lang->line("post id")?></th>
						<th field="auto_comment_count" sortable="true"><?php echo $this->lang->line("Reply Sent")?></th>
						<th field="view" sortable="true"><?php echo $this->lang->line("Actions")?></th>
						<!-- <th field="force" align="center" sortable="true"><?php echo $this->lang->line("force process"); ?></th> -->
						<th field="status" sortable="true"><?php echo $this->lang->line("status")?></th>
						<!-- <th field="post_created_at" sortable="true"><?php echo $this->lang->line("Post Create Time")?></th> -->
						<th field="last_reply_time" sortable="true"><?php echo $this->lang->line("Last Reply Time")?></th>
						<th field="error_message" align="left" sortable="true"><?php echo $this->lang->line("Error Message")?></th>
						<th field="post_description" align="left" sortable="true"><?php echo $this->lang->line("Post Description")?></th>
					</tr>
				</thead>
			</table>                        
		</div>

		<div id="tb" style="padding:3px">

			<form class="form-inline" style="margin-top:20px">
				<div class="form-group">
					<select name="search_page_name" id="search_page_name" class="form-control">
						<option value=""><?php echo $this->lang->line("page name") ?></option>
						<?php foreach ($page_info as $key => $value): ?>
							<option value="<?php echo $value; ?>" <?php if (isset($selected_page_name) && $selected_page_name == $value) echo 'selected'; ?>><?php echo $value; ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<div class="form-group">
					<input id="campaign_name" name="campaign_name" class="form-control" size="30" placeholder="<?php echo $this->lang->line('Campaign Name');?>">
				</div>
				<button class='btn btn-info' id="searchme" onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>    
			</form> 
		</div>        
	</div>
</div>   
</section>


<?php 
	
	$Doyouwanttopausethiscampaign = $this->lang->line("do you want to pause this campaign?");
	$Doyouwanttostartthiscampaign = $this->lang->line("do you want to start this campaign?");
	$Doyouwanttodeletethisrecordfromdatabase = $this->lang->line("do you want to delete this record from database?");
	$Youdidntselectanyoption = $this->lang->line("you didn't select any option.");
	$Youdidntprovideallinformation = $this->lang->line("you didn't provide all information.");
	$Youdidntprovideallinformation = $this->lang->line("you didn't provide all information.");
	$Doyouwanttostarthiscampaign = $this->lang->line("do you want to start this campaign?");

	$edit = $this->lang->line("Edit");
	$report = $this->lang->line("Report");
	$deletet = $this->lang->line("Delete");
	$pausecampaign = $this->lang->line("Pause Campaign");
	$startcampaign = $this->lang->line("Start Campaign");

	$doyoureallywanttoReprocessthiscampaign = $this->lang->line("Force Reprocessing means you are going to process this campaign again from where it ended. You should do only if you think the campaign is hung for long time and didn't send message for long time. It may happen for any server timeout issue or server going down during last attempt or any other server issue. So only click OK if you think message is not sending. Are you sure to Reprocessing ?");
	$alreadyEnabled = $this->lang->line("this campaign is already enable for processing.");
	$TypeAutoCampaignname = $this->lang->line("You didn\'t Type auto campaign name");
	$YouDidnotchosescheduleType = $this->lang->line("You didn\'t choose any schedule type");
	$YouDidnotchosescheduletime = $this->lang->line("You didn\'t select any schedule time");
	$YouDidnotchosescheduletimezone = $this->lang->line("You didn\'t select any time zone");
	$YoudidnotSelectPerodicTime = $this->lang->line("You didn\'t select any periodic time");
	$YoudidnotSelectCampaignStartTime = $this->lang->line("You didn\'t choose campaign start time");
	$YoudidnotSelectCampaignEndTime = $this->lang->line("You didn\'t choose campaign end time");
	$Youdidntselectanytemplate = $this->lang->line("you didn\'t select any template.");
	$Youdidntselectanyoptionyet = $this->lang->line("you didn\'t select any option yet.");
	$Youdidntselectanyoption = $this->lang->line("you didn\'t select any option.");


?>

<script>
	$j("document").ready(function(){

		// setTimeout(function(){
		// 	var report_page_name='<?php echo $report_page_name; ?>';
		// 	if(report_page_name!='')
		// 	{
		// 		$("#search_page_name").val(report_page_name);
		// 		$("#searchme").click();
		// 	}		

		// }, 1000);

		
		$('[data-toggle="popover"]').popover(); 
		$('[data-toggle="popover"]').on('click', function(e) {e.preventDefault(); return true;});

		var image_upload_limit = "<?php echo $image_upload_limit; ?>";
		var video_upload_limit = "<?php echo $video_upload_limit; ?>";

		var base_url="<?php echo site_url(); ?>";
		var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
		<?php for($k=1;$k<=10;$k++) : ?>
			$("#edit_filter_video_upload_<?php echo $k; ?>").uploadFile({
	    			url:base_url+"facebook_ex_auto_comment/upload_live_video",
	    			fileName:"myfile",
	    			maxFileSize:video_upload_limit*1024*1024,
	    			showPreview:false,
	    			returnType: "json",
	    			dragDrop: true,
	    			showDelete: true,
	    			multiple:false,
	    			maxFileCount:1, 
	    			acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
	    			deleteCallback: function (data, pd) {
	    				var delete_url="<?php echo site_url('facebook_ex_auto_comment/delete_uploaded_live_file');?>";
	    				$.post(delete_url, {op: "delete",name: data},
	    					function (resp,textStatus, jqXHR) {  
	    					    $("#edit_filter_video_upload_reply_<?php echo $k; ?>").val('');              
	    					});

	    			},
	    			onSuccess:function(files,data,xhr,pd)
	    			{
	    				var file_path = base_url+"upload/video/"+data;
	    				$("#edit_filter_video_upload_reply_<?php echo $k; ?>").val(file_path);	
	    			}
	    		});


	    		$("#edit_filter_image_upload_<?php echo $k; ?>").uploadFile({
	    	        url:base_url+"facebook_ex_auto_comment/upload_image_only",
	    	        fileName:"myfile",
	    	        maxFileSize:image_upload_limit*1024*1024,
	    	        showPreview:false,
	    	        returnType: "json",
	    	        dragDrop: true,
	    	        showDelete: true,
	    	        multiple:false,
	    	        maxFileCount:1, 
	    	        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
	    	        deleteCallback: function (data, pd) {
	    	            var delete_url="<?php echo site_url('facebook_ex_auto_comment/delete_uploaded_file');?>";
	    	            $.post(delete_url, {op: "delete",name: data},
	    	                function (resp,textStatus, jqXHR) {
	    	                	$("#edit_filter_image_upload_reply_<?php echo $k; ?>").val('');                      
	    	                });
	    	           
	    	         },
	    	         onSuccess:function(files,data,xhr,pd)
	    	           {
	    	               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
	    	               $("#edit_filter_image_upload_reply_<?php echo $k; ?>").val(data_modified);	
	    	           }
	    	    });
		<?php endfor; ?>

		var user_id = "<?php echo $this->session->userdata('user_id'); ?>";

		$("#edit_generic_video_upload").uploadFile({
			url:base_url+"facebook_ex_auto_comment/upload_live_video",
			fileName:"myfile",
			maxFileSize:video_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('facebook_ex_auto_comment/delete_uploaded_live_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {  
					    $("#edit_generic_video_comment_reply").val('');              
					});

			},
			onSuccess:function(files,data,xhr,pd)
			{
				var file_path = base_url+"upload/video/"+data;
				$("#edit_generic_video_comment_reply").val(file_path);	
			}
		});


		$("#edit_generic_comment_image").uploadFile({
	        url:base_url+"facebook_ex_auto_comment/upload_image_only",
	        fileName:"myfile",
	        maxFileSize:image_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('facebook_ex_auto_comment/delete_uploaded_file');?>";
	            $.post(delete_url, {op: "delete",name: data},
	                function (resp,textStatus, jqXHR) {
	                	$("#edit_generic_image_for_comment_reply").val('');                      
	                });
	           
	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
	               $("#edit_generic_image_for_comment_reply").val(data_modified);		
	           }
	    });


	    $("#edit_nofilter_video_upload").uploadFile({
			url:base_url+"facebook_ex_auto_comment/upload_live_video",
			fileName:"myfile",
			maxFileSize:video_upload_limit*1024*1024,
			showPreview:false,
			returnType: "json",
			dragDrop: true,
			showDelete: true,
			multiple:false,
			maxFileCount:1, 
			acceptFiles:".flv,.mp4,.wmv,.WMV,.MP4,.FLV",
			deleteCallback: function (data, pd) {
				var delete_url="<?php echo site_url('facebook_ex_auto_comment/delete_uploaded_live_file');?>";
				$.post(delete_url, {op: "delete",name: data},
					function (resp,textStatus, jqXHR) {  
					    $("#edit_nofilter_video_upload_reply").val('');              
					});

			},
			onSuccess:function(files,data,xhr,pd)
			{
				var file_path = base_url+"upload/video/"+data;
				$("#edit_nofilter_video_upload_reply").val(file_path);	
			}
		});


		$("#edit_nofilter_image_upload").uploadFile({
	        url:base_url+"facebook_ex_auto_comment/upload_image_only",
	        fileName:"myfile",
	        maxFileSize:image_upload_limit*1024*1024,
	        showPreview:false,
	        returnType: "json",
	        dragDrop: true,
	        showDelete: true,
	        multiple:false,
	        maxFileCount:1, 
	        acceptFiles:".png,.jpg,.jpeg,.JPEG,.JPG,.PNG,.gif,.GIF",
	        deleteCallback: function (data, pd) {
	            var delete_url="<?php echo site_url('facebook_ex_auto_comment/delete_uploaded_file');?>";
	            $.post(delete_url, {op: "delete",name: data},
	                function (resp,textStatus, jqXHR) {
	                	$("#edit_nofilter_image_upload_reply").val('');                      
	                });
	           
	         },
	         onSuccess:function(files,data,xhr,pd)
	           {
	               var data_modified = base_url+"upload/image/"+user_id+"/"+data;
	               $("#edit_nofilter_image_upload_reply").val(data_modified);		
	           }
	    });

	});
</script>

<script>

	$(document.body).on('click','#edit_modal_close',function(){        	
		// $("#edit_auto_reply_message_modal").removeClass("modal");
		var manual_post_id = $("#manual_edit_post_id").val();
		if(manual_post_id != '')
		{
			$("#edit_auto_reply_message_modal").modal("hide");
			$("#manual_edit_reply_by_post").modal("hide");
			$("#manual_edit_post_id").val('');
		}
		else
			$("#edit_auto_reply_message_modal").removeClass("modal");
	});

	var base_url="<?php echo site_url(); ?>";

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			search_page_name   :     $j('#search_page_name').val(),      
			campaign_name   :     $j('#campaign_name').val(),      
			is_searched		:     1
		});


	}

	var edit = "<?php echo $edit; ?>";
	var report = "<?php echo $report; ?>";
	var deletet = "<?php echo $deletet; ?>";
	var pausecampaign = "<?php echo $pausecampaign; ?>";
	var startcampaign = "<?php echo $startcampaign; ?>";
	

	var Doyouwanttopausethiscampaign = "<?php echo $Doyouwanttopausethiscampaign; ?>";
	
	$(document.body).on('click','.pause_campaign_info',function(){
		var table_id = $(this).attr('table_id');
		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttopausethiscampaign, 
			function(){ 
				$.ajax({
					type:'POST' ,
					url: base_url+"facebook_ex_auto_comment/ajax_autoreply_pause",
					data: {table_id:table_id},
					success:function(response){
						$j('#tt').datagrid('reload');
					}

				});
			},
			function(){     
		});
	});

	$(document.body).on('click','.renew_campaign',function(){		
		var table_id = $(this).attr('table_id');
		$.ajax({
			type:'POST' ,
			url: base_url+"facebook_ex_auto_comment/ajax_renew_campaign",
			data: {table_id:table_id},
			success:function(response){
				$j('#tt').datagrid('reload');
			}
		});		
	});

	var Doyouwanttostarthiscampaign = "<?php echo $Doyouwanttostarthiscampaign; ?>";
	$(document.body).on('click','.play_campaign_info',function(){
		var table_id = $(this).attr('table_id');
		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttostarthiscampaign, 
			function(){ 
				$.ajax({
					type:'POST' ,
					url: base_url+"facebook_ex_auto_comment/ajax_autoreply_play",
					data: {table_id:table_id},
					success:function(response){
						$j('#tt').datagrid('reload');
					}

				});
			},
			function(){     
		});
	});

	$(document.body).on('click','.force',function(){
		var id = $(this).attr('id');
		var alreadyEnabled = "<?php echo $alreadyEnabled; ?>";
		var doyoureallywanttoReprocessthiscampaign = "<?php echo $doyoureallywanttoReprocessthiscampaign; ?>";

		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttoReprocessthiscampaign, 
			function(){ 
				$.ajax({
			       type:'POST' ,
			       url: "<?php echo base_url('facebook_ex_auto_comment/force_reprocess_campaign')?>",
			       data: {id:id},
			       success:function(response)
			       {
			       	if(response=='1')
			       	$j('#tt').datagrid('reload');
			       	else 
			       	alertify.alert('<?php echo $this->lang->line("Alert");?>',alreadyEnabled,function(){});
			       }
				});

			},
			function(){     
		});
	});

	var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $Doyouwanttodeletethisrecordfromdatabase; ?>";
	$(document.body).on('click','.delete_report',function(){
		var table_id = $(this).attr('table_id');
		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttodeletethisrecordfromdatabase, 
			function(){ 
				$.ajax({
			    	type:'POST' ,
			    	url: base_url+"facebook_ex_auto_comment/ajax_autoreply_delete",
			    	data: {table_id:table_id},
			    	// async: false,
			    	success:function(response){
			         	$j('#tt').datagrid('reload');
			         	alertify.success('<?php echo $this->lang->line("your data has been successfully deleted from the database."); ?>');
			    	}

			    });
			},
			function(){     
		});
	});

	$(document.body).on('click','.view_report',function(){
		var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
		$("#view_report_modal_body").html(loading);
		$("#view_report").modal();
		var table_id = $(this).attr('table_id');
		$.ajax({
	    	type:'POST' ,
	    	url: base_url+"facebook_ex_auto_comment/ajax_get_reply_info",
	    	data: {table_id:table_id},
	    	// async: false,
	    	success:function(response){
	         	$("#view_report_modal_body").html(response);
	    	}

	    });

	});


	$(document.body).on('click','.edit_reply_info',function(){
	
	
		
		$("#manual_edit_reply_by_post").removeClass('modal');
		$("#edit_auto_reply_message_modal").addClass("modal");
		$("#edit_response_status").html("");
		var table_id = $(this).attr('table_id');
					$.ajax({
					  type:'POST' ,
					  url:"<?php echo site_url();?>facebook_ex_auto_comment/ajax_edit_reply_info",
					  data:{table_id:table_id},
					  dataType:'JSON',
					  success:function(response){

					    $("#edit_auto_reply_page_id").val(response.edit_auto_reply_page_id);
					    $("#edit_auto_reply_post_id").val(response.edit_auto_reply_post_id);
					  	$("#edit_campaign_name").val(response.edit_campaign_name);

		                if(response.edit_schedule_type == 'onetime')
		                {
		                    
		                	$("#edit_schedule_type_o").attr('checked',true);
		                	$(".schedule_block_item_o").show();
		                	$(".schedule_block_item_new_p").hide();
		                	
		                	$("#edit_schedule_time_o").val(response.edit_schedule_time_o);
		                	$("#edit_time_zone_o").val(response.edit_time_zone_o);

		                }
		                if(response.edit_schedule_type == 'periodic')
		                {
		                    
		                	$("#edit_schedule_type_p").attr('checked',true);
		                	$(".schedule_block_item_new_p").show();
		                	$(".schedule_block_item_o").hide();
		                	$("#edit_periodic_time").val(response.edit_periodic_time);
		                	$("#edit_campaign_start_time").val(response.edit_campaign_start_time);
		                	$("#edit_campaign_end_time").val(response.edit_campaign_end_time);
		                	$("#edit_comment_start_time").val(response.edit_comment_start_time);
		                	$("#edit_comment_end_time").val(response.edit_comment_end_time);
		                	$("#edit_periodic_time_zone").val(response.edit_periodic_time_zone);
		                	if(response.edit_auto_comment_type=='random')
		                	{
		                		$("#edit_random").attr('checked',true);

		                	}
		                	if(response.edit_auto_comment_type =='serially')
		                	{
		                		$("#edit_serially").attr('checked',true);
		                	}



		                }

		         
		  	
		              $("#edit_auto_comment_template_id").val(response.edit_auto_comment_template_id);
		              $("#edit_auto_reply_message_modal").modal();
					  }
					});
					
		

			
			setTimeout(function(){			
				$(".previewLoader").hide();				
			},1000);
		
		
	});

	$(document.body).on('click','#edit_add_more_button',function(){
		if(edit_content_counter == 11)
			$("#edit_add_more_button").hide();
		$("#edit_content_counter").val(edit_content_counter);

		$("#edit_filter_div_"+edit_content_counter).show();
		
		/** Load Emoji For Filter Word when click on add more button during Edit**/
			
		$j("#edit_filter_message_"+edit_content_counter).emojioneArea({
    		autocomplete: false,
			pickerPosition: "bottom"
 	 	});
		
		$j("#edit_comment_reply_msg_"+edit_content_counter).emojioneArea({
    		autocomplete: false,
			pickerPosition: "bottom"
 	 	});
		
		edit_content_counter++;

	});



	$(document.body).on('click','#edit_save_button',function(){
		var post_id = $("#edit_auto_reply_post_id").val();
		var edit_campaign_name = $("#edit_campaign_name").val();
		var edit_schedule_type = $("input[name=edit_schedule_type]:checked").val();
		var edit_schedule_time_o = $("#edit_schedule_time_o").val();
		var edit_time_zone_o = $("#edit_time_zone_o").val();
		var edit_periodic_time = $("#edit_periodic_time").val();
		var edit_campaign_start_time = $("#edit_campaign_start_time").val();
		var edit_campaign_end_time = $("#edit_campaign_end_time").val();
		var Youdidntselectanyoption = "<?php echo $Youdidntselectanyoption; ?>";
		var Youdidntprovideallinformation = "<?php echo $Youdidntprovideallinformation; ?>";
		var YouDidnotchosescheduletime = "<?php echo $YouDidnotchosescheduletime; ?>";
		var YouDidnotchosescheduletimezone = "<?php echo $YouDidnotchosescheduletimezone; ?>";
		var YoudidnotSelectPerodicTime = "<?php echo $YoudidnotSelectPerodicTime; ?>";
		var YoudidnotSelectCampaignStartTime = "<?php echo $YoudidnotSelectCampaignStartTime; ?>";
		var YoudidnotSelectCampaignEndTime = "<?php echo $YoudidnotSelectCampaignEndTime; ?>";

		if (typeof(edit_schedule_type)==='undefined')
		{
			alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntselectanyoption,function(){});
			return false;
		}

		if(edit_campaign_name == ''){
			alertify.alert('<?php echo $this->lang->line("Alert")?>',Youdidntprovideallinformation,function(){});
			return false;
		}

		if($("#edit_auto_comment_template_id").val()== 0){
			alertify.alert('<?php echo $this->lang->line("Alert")?>','<?php echo $this->lang->line("you have not select any template.");?>',function(){});
			return false;
		}	

		if(edit_schedule_type == "onetime")
		{
			if(edit_schedule_time_o == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduletime,function(){});
				return false;
			}				
			if(edit_time_zone_o == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduletimezone,function(){});
				return false;
			}
		}
		if(edit_schedule_type == "periodic")
		{
			if(edit_periodic_time == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectPerodicTime,function(){});
				return false;
			}	
			if($("#edit_periodic_time_zone").val() == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',YouDidnotchosescheduletimezone,function(){});
				return false;
			}				
			if(edit_campaign_start_time == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectCampaignStartTime,function(){});
				return false;
			}			
			if(edit_campaign_end_time == ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',YoudidnotSelectCampaignEndTime,function(){});
				return false;
			}

			var edit_comment_start_time=$("#edit_comment_start_time").val();
			var edit_comment_end_time=$("#edit_comment_end_time").val();
			var rep1 = parseFloat(edit_comment_start_time.replace(":", "."));
			var rep2 = parseFloat(edit_comment_end_time.replace(":", "."));

			if( edit_comment_start_time== '' ||  edit_comment_end_time== ''){
				alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Please select comment between times.');?>",function(){});
				return false;
			}

			if(rep1 >= rep2)
			{
				alertify.alert('<?php echo $this->lang->line("Alert")?>',"<?php echo $this->lang->line('Comment between start time must be less than end time.');?>",function(){});
				return false;
			}

		}		

		var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
		$("#edit_response_status").html(loading);

		var queryString = new FormData($("#edit_auto_reply_info_form")[0]);
	    $.ajax({
	    	type:'POST' ,
	    	url: base_url+"facebook_ex_auto_comment/ajax_update_autoreply_submit",
	    	data: queryString,
	    	dataType : 'JSON',
	    	// async: false,
	    	cache: false,
	    	contentType: false,
	    	processData: false,
	    	success:function(response){
	         	if(response.status=="1")
		        {
		         	$("#edit_response_status").html(response.message);
		        }
		        else
		        {
		         	$("#edit_response_status").html(response.message);
		        }
	    	}

	    });

	});


	$(document.body).on('change','input[name=edit_message_type]',function(){    
    	if($("input[name=edit_message_type]:checked").val()=="generic")
    	{
    		$("#edit_generic_message_div").show();
    		$("#edit_filter_message_div").hide();
			
			$j("#edit_generic_message, #edit_generic_message_private").emojioneArea({
	        		autocomplete: false,
					pickerPosition: "bottom"
	     		 });
				 
    	}
    	else 
    	{
    		$("#edit_generic_message_div").hide();
    		$("#edit_filter_message_div").show();
			
			/*** Load Emoji When Filter word click during Edit , by defualt first textarea are loaded & No match found field****/
				
				$j("#edit_comment_reply_msg_1, #edit_filter_message_1, #edit_nofilter_word_found_text, #edit_nofilter_word_found_text_private").emojioneArea({
	        		autocomplete: false,
					pickerPosition: "bottom"
	     	 	});
				
				
    	}
    });

    $(document.body).on('click','.lead_first_name',function(){
	
    		var textAreaTxt = $(this).parent().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
		    var txtToAdd = " #LEAD_USER_FIRST_NAME# ";
		    var new_text = textAreaTxt + txtToAdd;
		   	$(this).parent().next().next().next().children('.emojionearea-editor').html(new_text);
		   	$(this).parent().next().next().next().children('.emojionearea-editor').click();
			
	});

	$(document.body).on('click','.lead_last_name',function(){

    		var textAreaTxt = $(this).parent().next().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
		    var txtToAdd = " #LEAD_USER_LAST_NAME# ";
			var new_text = textAreaTxt + txtToAdd;
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').html(new_text);
		   $(this).parent().next().next().next().next().children('.emojionearea-editor').click();
		   
		   
	});

	$(document.body).on('click','.lead_tag_name',function(){

    		var textAreaTxt = $(this).parent().next().next().next().next().next().children('.emojionearea-editor').html();
			
			var lastIndex = textAreaTxt.lastIndexOf("<br>");
			
			if(lastIndex!='-1')
				textAreaTxt = textAreaTxt.substring(0, lastIndex);
				
				
		    var txtToAdd = " #TAG_USER# ";
			var new_text = textAreaTxt + txtToAdd;
		    $(this).parent().next().next().next().next().next().children('.emojionearea-editor').html(new_text);
		    $(this).parent().next().next().next().next().next().children('.emojionearea-editor').click();
			
	});
	
</script>


<div class="modal fade" id="view_report" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg" style="min-width: 70%;max-width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line("report of auto comment") ?></h4>
            </div>
            <div class="modal-body text-center" id="view_report_modal_body">                

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="edit_auto_reply_message_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="min-width: 70%;max-width: 100%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id='edit_modal_close' class="close">&times;</button>
                <h4 class="modal-title text-center"><?php echo $this->lang->line("Please give the following information for post auto comment") ?></h4>
            </div>
            <form action="#" id="edit_auto_reply_info_form" method="post">
	            <input type="hidden" name="edit_auto_reply_page_id" id="edit_auto_reply_page_id" value="">
	            <input type="hidden" name="edit_auto_reply_post_id" id="edit_auto_reply_post_id" value="">
            <div class="modal-body" id="edit_auto_reply_message_modal_body">   
			
			<img src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>" class="center-block previewLoader" style="margin-bottom:0;">
			
			

				            	<div class="row" style="padding: 10px 20px 10px 20px;">

				            		<div class="col-xs-12" style="margin-top: 15px;">
				            			<div class="form-group">
				            				<label>
				            					<i class="fas fa-monument"></i> <?php echo $this->lang->line('Auto comment campaign name'); ?> <span class="red">*</span> 
				            				</label>
				            				<br>
				            				<input class="form-control"type="text" name="edit_campaign_name" id="edit_campaign_name" placeholder="Write your auto reply campaign name here">
				            			</div>
				            		</div>
									<div class="col-xs-12">
				                        <div class="form-group col-xs-12 col-md-12" style="padding: 0;">
											<label>
												<i class="fa fa-th-large"></i> <?php echo $this->lang->line('Auto Comment Template'); ?> <span class="red">*</span> 
											</label>
											<br>
											<select  class="form-control" id="edit_auto_comment_template_id" name="edit_auto_comment_template_id">
											<?php
												echo "<option value='0'>{$this->lang->line('Please select a template')}</option>";
												foreach($auto_comment_template as $key=>$val)
												{
													$id=$val['id'];
													$group_name=$val['template_name'];
													echo "<option value='{$id}'>{$group_name}</option>";
												}
											 ?>
											</select>
									    </div>
									</div>
									<br>

				                   <br>
									<div class="col-xs-12">

										<div class="form-group">
											
											<label>
												<i class="fas fa-clock"></i> <?php echo $this->lang->line('Schedule Type'); ?> <span class="red">*</span> 
												<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Onetime campaign will comment only the first comment of the selected template and periodic campaign will auto comment multiple time periodically as per your settings.'); ?>" data-original-title="<?php echo $this->lang->line('Schedule Type'); ?>"><i class="fa fa-info-circle"></i> </a>
											</label>
											<br>
											<input name="edit_schedule_type" value="onetime" id="edit_schedule_type_o" type="radio"> <label for="edit_schedule_type_o"><?php echo $this->lang->line('One Time');?></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input name="edit_schedule_type" value="periodic" id="edit_schedule_type_p" type="radio"> <label for="edit_schedule_type_p"><?php echo $this->lang->line('Periodic');?></label>
										</div>

										<div class="form-group schedule_block_item_o col-xs-12 col-md-6">
											<label><?php echo $this->lang->line('Schedule time'); ?></label>
											<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_schedule_time_o" id="edit_schedule_time_o" class="form-control datepicker" type="text"/>
										</div>

										<div class="form-group schedule_block_item_o col-xs-12 col-md-6">
											<label><?php echo $this->lang->line('Time zone'); ?></label>
											<?php
											$time_zone[''] =$this->lang->line('Please Select');
											echo form_dropdown('edit_time_zone_o',$time_zone,set_value('time_zone'),' class="form-control" id="edit_time_zone_o" required');
											?>
										</div>

										<div class='schedule_block_item_new_p' style="padding:20px !important; border:1px solid #ccc !important; background: #fcfcfc !important;">
											<div class="clearfix"></div>
											<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">

												<label><?php echo $this->lang->line('Periodic Schedule time'); ?>
													<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Choose how frequently you want to comment'); ?>" data-original-title="<?php echo $this->lang->line('Periodic Schedule time'); ?>"><i class="fa fa-info-circle"></i> </a>
												</label>
												<?php
												$periodic_time[''] =$this->lang->line('Please Select Periodic Time Schedule');
												echo form_dropdown('edit_periodic_time',$periodic_time,set_value('edit_periodic_time'),' class="form-control" id="edit_periodic_time" required');
												?>
											</div>

											<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">
												<label><?php echo $this->lang->line('Time zone'); ?></label>
												<?php
												$time_zone[''] =$this->lang->line('Please Select');
												echo form_dropdown('edit_periodic_time_zone',$time_zone,set_value('edit_periodic_time_zone'),' class="form-control" id="edit_periodic_time_zone" required');
												?>
											</div>

											<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">
												<label><?php echo $this->lang->line('Campaign Start time'); ?></label>
												<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_campaign_start_time" id="edit_campaign_start_time" class="form-control datepicker" type="text"/>
											</div>						
											<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">
												<label><?php echo $this->lang->line('Campaign End time'); ?></label>
												<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_campaign_end_time" id="edit_campaign_end_time" class="form-control datepicker" type="text"/>
											</div>

											<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">
												<label>
													<?php echo $this->lang->line('Comment Between Time'); ?>
													<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line("Set the allowed time of the comment. As example you want to auto comment by page from 10 AM to 8 PM. You don't want to comment other time. So set it 10:00 & 20:00"); ?>" data-original-title="<?php echo $this->lang->line('Comment Between Time'); ?>"><i class="fa fa-info-circle"></i> 
													</a>												
												</label> 
												<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_comment_start_time" id="edit_comment_start_time" class="form-control datetimepicker2" type="text"/>
											</div>
											<div class="form-group schedule_block_item_new_p col-xs-12 col-md-6">
												<label style="position: relative;right: 22px;top: 32px;"><?php echo $this->lang->line('to'); ?></label> 
												<input placeholder="<?php echo $this->lang->line('Time'); ?>"  name="edit_comment_end_time" id="edit_comment_end_time" class="form-control datetimepicker2" type="text"/>
											</div>

											<div class="form-group schedule_block_item_new_p col-xs-12 col-md-12">

												<label>
													<i class="fas fa-comment"></i> <?php echo $this->lang->line('Auto Comment Type'); ?> <span class="red">*</span> 
													<a href="#" data-placement="bottom" data-toggle="popover" data-trigger="focus" title="" data-content="<?php echo $this->lang->line('Random type will pick a comment from template randomly each time and serial type will pick the comment serially from selected template first to last.'); ?>" data-original-title="<?php echo $this->lang->line('Auto Comment Type'); ?>"><i class="fa fa-info-circle"></i> </a>
												</label>
												<br>
												<input name="edit_auto_comment_type" value="random" id="edit_random" type="radio"> <label for="edit_random"><?php echo $this->lang->line('Random');?></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input name="edit_auto_comment_type" value="serially" id="edit_serially" type="radio"> <label for="edit_serially"><?php echo $this->lang->line('Serially');?></label>
												
											</div>
											<div class="clearfix"></div>
										</div>	

						
										
									</div>



								<br/>
								
					
								</div>  
				<div class="row" style="padding: 10px 20px 10px 20px;">
					<!-- added by mostofa on 26-04-2017 -->

					<div class="smallspace clearfix"></div>





				</div>
				<div class="col-xs-12 text-center" id="edit_response_status"></div>
            </div>
            </form>
            <div class="clearfix"></div>
            <div class="modal-footer text-center" style="padding-right:35px;">                
				<button class="btn btn-lg btn-primary" id="edit_save_button"><i class='fa fa-save'></i> <?php echo $this->lang->line("save") ?></button>
            </div>
        </div>
    </div>
</div>

<script>



$(document.body).on('change','input[name=schedule_type]',function(){
	if($("input[name=schedule_type]:checked").val()=="onetime")
	{
		$(".schedule_block_item").show();
		$(".schedule_block_item_new").hide();
		$("#periodic_time").val("");
		$("#campaign_start_time").val("");
		$("#campaign_end_time").val("");
	}
	else
	{
		$("#schedule_time").val("");
		$("#time_zone").val("");
		$(".schedule_block_item_new").show();
		$(".schedule_block_item").hide();
	}
});

$(document.body).on('change','input[name=schedule_type]',function(){
	if($("input[name=schedule_type]:checked").val()=="onetime")
	{
		$(".schedule_block_item").show();
		$(".schedule_block_item_new").hide();
		$("#periodic_time").val("");
		$("#campaign_start_time").val("");
		$("#campaign_end_time").val("");
	}
	else
	{
		$("#schedule_time").val("");
		$("#time_zone").val("");
		$(".schedule_block_item_new").show();
		$(".schedule_block_item").hide();
	}
});


$(document.body).on('change','input[name=edit_schedule_type]',function(){
	if($("input[name=edit_schedule_type]:checked").val()=="onetime")
	{
		$(".schedule_block_item_o").show();
		$(".schedule_block_item_new_p").hide();
		$("#periodic_time_p").val("");
		$("#campaign_start_time_p").val("");
		$("#campaign_end_time_p").val("");
	}
	else
	{
		$("#schedule_time_o").val("");
		$("#time_zone_o").val("");
		$(".schedule_block_item_new_p").show();
		$(".schedule_block_item_o").hide();
	}
});


$(document).ready(function(){
     $(".schedule_block_item").hide();
     $(".schedule_block_item_new").hide();

    var today = new Date();
    var next_date = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
    $j('.datepicker').datetimepicker({
    	theme:'light',
    	format:'Y-m-d H:i:s',
    	formatDate:'Y-m-d H:i:s',
    	minDate: today,
    	maxDate: next_date

    })

    // $j('.datepicker').datetimepicker();
    $j('.datetimepicker2').datetimepicker({
      datepicker:false,
      format:'H:i'
    });






});
</script>


<style type="text/css">
	.smallspace{padding: 10px 0;}
	.lead_first_name,.lead_last_name,.lead_tag_name{background: #fff !important;}
	.ajax-file-upload-statusbar{width: 100% !important;}
	.ajax-upload-dragdrop{width:100% !important;}
	.renew_campaign
	{
		cursor: pointer;
	}
</style>
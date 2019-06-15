<?php $this->load->view('admin/theme/message'); ?>




<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><i class="fa fa-list"></i> <?php echo $this->lang->line("Campaign Report");?></h1>
</section>
<section class="content">
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; height:659px;">
				<table
				id="tt"
				class="easyui-datagrid"
				url="<?php echo base_url()."facebook_ex_campaign/campaign_report_data"; ?>"

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
							<th field="campaign_name" sortable="true"><?php echo $this->lang->line("campaign name"); ?></th>
							<th field="post_status_formatted" align="center" ><?php echo $this->lang->line("status"); ?></th>
							<th field="campaign_type_formatted" align="center" ><?php echo $this->lang->line("type"); ?></th>
							<th field="sent_count" align="center" ><?php echo $this->lang->line("sent"); ?></th>
							<th field="actions" align="left"><?php echo $this->lang->line("actions"); ?></th>
							<!-- <th field="force" align="center" sortable="true"><?php echo $this->lang->line("force process"); ?></th> -->
							<!-- <th field="edit" align="center" sortable="true"><?php echo $this->lang->line("edit"); ?></th> -->
							<!-- <th field="delete" align="center" sortable="true"><?php echo $this->lang->line("delete"); ?></th> -->
							<th field="scheduled_at" sortable="true" align="center"><?php echo $this->lang->line("scheduled time"); ?></th>
							<th field="added_at" align="center"  sortable="true"><?php echo $this->lang->line("created at"); ?></th>
							<th field="page_names" sortable="true"><?php echo $this->lang->line("page name(s)")?></th>
							<th field="attachment" align="center" ><?php echo $this->lang->line("attachment"); ?></th>
						</tr>
					</thead>
				</table>
			</div>

			<div id="tb" style="padding:3px">

				<?php
					$search_campaign_name  = $this->session->userdata('facebook_ex_conversation_campaign_name');
			        $search_posting_status  = $this->session->userdata('facebook_ex_conversation_posting_status');
			        $search_page_ids  = $this->session->userdata('facebook_ex_conversation_page_ids');
			        $search_scheduled_from = $this->session->userdata('facebook_ex_conversation_scheduled_from');
			        $search_scheduled_to = $this->session->userdata('facebook_ex_conversation_scheduled_to');
				?>
				<div class="row">
					<div class="col-xs-12">
						<a style="margin-bottom: 5px;" class="btn btn-outline-primary" href="<?php echo site_url('facebook_ex_campaign/create_multipage_campaign');?>"  title="<?php echo $this->lang->line("Create New Campaign"); ?>">
						<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("multi-page campaign"); ?>
						</a>
						<a style="margin-bottom: 5px;" class="btn btn-outline-primary" href="<?php echo site_url('facebook_ex_campaign/create_multigroup_campaign');?>"  title="<?php echo $this->lang->line("Create New Campaign"); ?>">
						<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("multi-group campaign"); ?>
						</a>
						<a style="margin-bottom: 5px;" class="btn btn-outline-primary" href="<?php echo site_url('facebook_ex_campaign/custom_campaign');?>"  title="<?php echo $this->lang->line("Create New Campaign"); ?>">
						<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("custom campaign"); ?>
						</a>
					</div>
				</div>

				<form class="form-inline" style="margin-top:15px">

					<div class="form-group">
						<input id="search_campaign_name" name="search_campaign_name" value="<?php echo $search_campaign_name;?>" class="form-control" size="20" placeholder="<?php echo $this->lang->line("campaign name") ?>">
					</div>

					<div class="form-group">
						<select name="search_page" id="search_page"  class="form-control">
							<option value=""><?php echo $this->lang->line("all page") ?></option>
							<?php
								foreach ($page_info as $key => $value)
								{
									if($value['id'] == $search_page_ids)
									echo "<option selected value='".$value['id']."'>".$value['page_name']."</option>";
									else echo "<option value='".$value['id']."'>".$value['page_name']."</option>";
								}
							?>
						</select>
					</div>

					<div class="form-group">
						<select name="search_status" id="search_status"  class="form-control">
							<option value=""><?php echo $this->lang->line("status") ?></option>
							<option <?php if($search_posting_status=="0") echo "selected";?> value="0"><?php echo $this->lang->line("pending") ?></option>
							<option <?php if($search_posting_status=="1") echo "selected";?> value="1"><?php echo $this->lang->line("processing") ?></option>
							<option <?php if($search_posting_status=="2") echo "selected";?> value="2"><?php echo $this->lang->line("completed") ?></option>
						</select>
					</div>

					<div class="form-group">
						<input id="scheduled_from" value="<?php echo $search_scheduled_from;?>" name="scheduled_from" class="form-control datepicker" size="20" placeholder="<?php echo $this->lang->line("scheduled from") ?>">
					</div>

					<div class="form-group">
						<input id="scheduled_to" value="<?php echo $search_scheduled_to;?>" name="scheduled_to" class="form-control  datepicker" size="20" placeholder="<?php echo $this->lang->line("scheduled to") ?>">
					</div>

					<button class='btn btn-info'  onclick="doSearch(event)"><i class='fa fa-search'></i> <?php echo $this->lang->line("search");?></button>
			</div>

				</form>
		</div>
	</div>
</section>

<?php
	$somethingwentwrong = $this->lang->line("something went wrong.");
	$Doyouwanttopausethiscampaign = $this->lang->line("do you want to pause this campaign?");
	$whenitpause = $this->lang->line("This will affect from next cron job run after it finish currently processing messages.");
	$Doyouwanttostartthiscampaign = $this->lang->line("do you want to start this campaign?");
	$doyoureallywanttodeletethiscampaign = $this->lang->line("do you really want to delete this campaign?");
	$alreadyEnabled = $this->lang->line("this campaign is already enable for processing.");
	$doyoureallywanttoReprocessthiscampaign = $this->lang->line("Force Reprocessing means you are going to process this campaign again from where it ended. You should do only if you think the campaign is hung for long time and didn't send message for long time. It may happen for any server timeout issue or server going down during last attempt or any other server issue. So only click OK if you think message is not sending. Are you sure to Reprocessing ?");
	$wanttounsubscribe = $this->lang->line("Do you really want to unsubscribe this user? You can again subscribe this user from import lead menu.");

 ?>
<script>

	var base_url="<?php echo site_url(); ?>";

	$(document.body).on('click','.restart_button',function(){
		var table_id = $(this).attr('table_id');
		var doyoureallywanttoReprocessthiscampaign = "<?php echo $doyoureallywanttoReprocessthiscampaign; ?>";

		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttoReprocessthiscampaign, 
		  function(){ 
		    $.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('facebook_ex_campaign/restart_campaign')?>",
		       data: {table_id:table_id},
		       success:function(response)
		       {
			       	if(response=='1'){
			       		$j('#tt').datagrid('reload');
			       		$("#sent_report_modal").modal('hide');
			       		alertify.success('<?php echo $this->lang->line("campaign has been restarted successfully."); ?>');
			       	}
		       }
			});
		  },
		  function(){     
		  });
	});

	$(document.body).on('click','.unsubscribe_me',function(){
		var client_id = $(this).attr('client_id');
		var wanttounsubscribe = "<?php echo $wanttounsubscribe; ?>";

		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',wanttounsubscribe, 
		  function(){ 
		    $.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('facebook_ex_campaign/new_unsubscribe')?>",
		       data: {client_id:client_id},
		       success:function(response)
		       {
		       		$("[client_id="+client_id+"]").hide();
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
		       url: "<?php echo base_url('facebook_ex_campaign/force_reprocess_campaign')?>",
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

	$(document.body).on('click','.pause_campaign_info',function(){
		var Doyouwanttopausethiscampaign = "<?php echo $Doyouwanttopausethiscampaign; ?>";
		var table_id = $(this).attr('table_id');
		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttopausethiscampaign, 
		  function(){ 
		    $.ajax({
				type:'POST' ,
				url: base_url+"facebook_ex_campaign/ajax_campaign_pause",
				data: {table_id:table_id},
				success:function(response){
					$j('#tt').datagrid('reload');
				}

			});
		  },
		  function(){     
		});
	});

	$(document.body).on('click','.play_campaign_info',function(){
		var Doyouwanttostartthiscampaign = "<?php echo $Doyouwanttostartthiscampaign; ?>";
		var table_id = $(this).attr('table_id');

		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttostartthiscampaign, 
		  function(){ 
		    $.ajax({
				type:'POST' ,
				url: base_url+"facebook_ex_campaign/ajax_campaign_play",
				data: {table_id:table_id},
				success:function(response){
					$j('#tt').datagrid('reload');
				}

			});
		  },
		  function(){     
		});
	});

    $(document.body).on('click','.delete',function(){
		var id = $(this).attr('id');
	    if (typeof(id)==='undefined')
	    {
	    	alertify.alert('<?php echo $this->lang->line("Alert");?>','<?php echo $this->lang->line("this campaign is in processing state");?>',function(){});
	    	return;
	    }
		var somethingwentwrong = "<?php echo $somethingwentwrong; ?>";
		var doyoureallywanttodeletethiscampaign = "<?php echo $doyoureallywanttodeletethiscampaign; ?>";

		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doyoureallywanttodeletethiscampaign, 
		  function(){ 
		    $.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('facebook_ex_campaign/delete_campaign')?>",
		       data: {id:id},
		       success:function(response)
		       {
		       	if(response=='1')
		       	{
		       		$j('#tt').datagrid('reload');
					alertify.success('<?php echo $this->lang->line("your data has been successfully deleted from the database."); ?>');

		       	}
		       	else
		       	alertify.alert('<?php echo $this->lang->line("Alert");?>',somethingwentwrong,function(){});
		       }
			});
		  },
		  function(){     
		});


	});


    function doSearch(event)
	{
		event.preventDefault();
		$j('#tt').datagrid('load',{
			campaign_name   :     $j('#search_campaign_name').val(),
			page_ids   		:     $j('#search_page').val(),
			posting_status  :     $j('#search_status').val(),
			scheduled_from  :     $j('#scheduled_from').val(),
			scheduled_to    :     $j('#scheduled_to').val(),
			is_searched		:     1
		});

	}


</script>



<script>


    $j('.datepicker').datetimepicker({
   	theme:'light',
   	format:'Y-m-d',
   	formatDate:'Y-m-d',
   	timepicker:false
  	})


  	$(document.body).on('click','.sent_report',function(){
  		var loading = '<br/><img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block"><br/>';
        $("#sent_report_body").html(loading);
  		$("#sent_report_modal").modal();

  		var id = $(this).attr('cam-id');

  		$.ajax({
	            type:'POST' ,
	            url:"<?php echo site_url();?>facebook_ex_campaign/campaign_sent_status",
	            data:{id:id},
	            success:function(response){
	            	$("#sent_report_body").html(response);
	            }
	        });
  	});




</script>



<div class="modal fade" id="sent_report_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" style="min-width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-th-list"></i> <?php echo $this->lang->line("campaign report") ?></h4>
			</div>
			<div class="modal-body" id="sent_report_body">

			</div>
		</div>
	</div>
</div>




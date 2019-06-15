<?php $this->load->view('admin/theme/message'); ?>
<?php 
$only_message_button = $this->uri->segment(3);
?>
<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><?php echo $this->lang->line("CTA post campaign cist");?> </h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; min-height:760px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."facebook_rx_cta_poster/cta_post_list_data"; ?>" 

				pagination="true" 
				rownumbers="true" 
				toolbar="#tb" 
				pageSize="15" 
				pageList="[5,10,15,20,50,100]"  
				fit= "true" 
				fitColumns= "true" 
				nowrap= "true" 
				view= "detailview"
				idField="id"
				>
				
					<!-- url is the link to controller function to load grid data -->					

					<thead>
						<tr>
							<th field="campaign_name" sortable="true"><?php echo $this->lang->line("campaign name"); ?></th>
							<th field="page_name" sortable="true"><?php echo $this->lang->line("page name"); ?></th>
							<th field="cta_button" sortable="true"><?php echo $this->lang->line("CTA button"); ?></th>
							<th field="scheduled_at" align="center" sortable="true"><?php echo $this->lang->line("scheduled at"); ?></th>
							<th field="status" align="center" sortable="true"><?php echo $this->lang->line("status"); ?></th>
							<th field="visit_post" align="center" sortable="true"><?php echo $this->lang->line("visit post"); ?></th>
							<th field="delete" align="center" sortable="true"><?php echo $this->lang->line("delete"); ?></th>
							<th field="message_formatted" ><?php echo $this->lang->line("message"); ?></th>
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">

			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-primary" href="<?php echo base_url("facebook_rx_cta_poster/add_cta_post/{$only_message_button}");?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("new CTA post campaign"); ?></a>
				</div>
			</div>
 

			<form class="form-inline" style="margin-top:20px">

				<div class="form-group">
					<input id="campaign_name" name="campaign_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line("campaign name") ?>">
				</div>

				<div class="form-group">
					<input id="page_name" name="page_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line("page name") ?>">
				</div>   

				<div class="form-group">
					<input id="scheduled_from" name="scheduled_from" class="form-control datepicker" size="20" placeholder="<?php echo $this->lang->line("scheduled from") ?>">
				</div>

				<div class="form-group">
					<input id="scheduled_to" name="scheduled_to" class="form-control  datepicker" size="20" placeholder="<?php echo $this->lang->line("scheduled to") ?>">
				</div>                    

				<button class='btn btn-info'  onclick="doSearch(event)"><?php echo $this->lang->line("search report");?></button> 
							
			</div>  

			</form> 

			</div>        
		</div>
	</div>   
</section>

<?php 
	$Doyoureallywanttodeletethispostfromourdatabase = $this->lang->line("do you really want to delete this post from our database?");
	$Somethingwentwrong = $this->lang->line("Something went wrong.");
 ?>

<script>

	$j(function() {
		$( ".datepicker" ).datepicker();
	});

	$(document.body).on('click','.delete',function(){ 
		var id = $(this).attr('id');
		var Doyoureallywanttodeletethispostfromourdatabase = "<?php echo $Doyoureallywanttodeletethispostfromourdatabase; ?>";
		var Somethingwentwrong = "<?php echo $Somethingwentwrong; ?>";
		var ans = confirm(Doyoureallywanttodeletethispostfromourdatabase);
		if(ans)
		{
			$.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('facebook_rx_cta_poster/delete_post')?>",
		       data: {id:id},
		       success:function(response)
		       { 
		       	if(response=='1')
		       	$j('#tt').datagrid('reload');
		       	else alert(Somethingwentwrong);
		       }
			});
			
		}
	});
	

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{             
			campaign_name   :     $j('#campaign_name').val(),              
			page_name  :     $j('#page_name').val(),              
			scheduled_from  		:     $j('#scheduled_from').val(),    
			scheduled_to    		:     $j('#scheduled_to').val(),         
			is_searched		:      1
		});

	} 
	
	
</script>


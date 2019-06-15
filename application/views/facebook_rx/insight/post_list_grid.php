<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><?php echo $page_name." - post insight";?> </h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; min-height:1500px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."facebook_rx_insight/post_list_grid_data/".$page_table_id; ?>" 

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

				<thead>
					<tr>
						<!-- <th field="id"  checkbox="true"></th> -->
						<th field="view_thumbnail" formatter="create_thumb_action"><?php echo $this->lang->line("thumbnail")?></th>
						<th field="view_url" formatter="create_url_action"><?php echo $this->lang->line("url")?></th>
						<th field="created_time" sortable="true"><?php echo $this->lang->line("upload time")?></th>
						<th field="post_id" sortable="true"><?php echo $this->lang->line("post id")?></th>
						<th field="view" formatter="video_analytics"><?php echo $this->lang->line("Action")?></th>
						<th field="message" sortable="true"><?php echo $this->lang->line("message")?></th>
					</tr>
				</thead>
			</table>                        
		</div>

		<div id="tb" style="padding:3px">

			<form class="form-inline" style="margin-top:20px">
				<div class="form-group">
					<input id="post_id" name="post_id" class="form-control" size="30" placeholder="<?php echo $this->lang->line('Post ID');?>">
				</div>
				<button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("Search");?></button>    
			</form> 
		</div>        
	</div>
</div>   
</section>


<script>

	var base_url="<?php echo site_url(); ?>";

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			post_id     	:     $j('#post_id').val(),        
			is_searched		:     1
		});


	}

	function video_analytics(value,row,index)
	{
		var page_url = "<a class='btn btn-primary' href='"+base_url+"facebook_rx_insight/post_analytics/"+row.id+"' target='_blank'><i class='fa fa-bar-chart'></i> Analytics</a>";
		return page_url;
	} 


	function create_url_action(value,row,index)
	{
		var page_url = "<a class='btn btn-info' href='"+row.permalink_url+"' target='_blank'><i class='fa fa-outdent'></i> Go to post</a>";
		return page_url;
	} 

	function create_thumb_action(value,row,index)
	{
		var video_thumb_url = "<img style='width:120px;height:80px;' class='text-center' alt='No Thumb' src='"+row.picture+"'/>";

		return video_thumb_url;
	} 


	
</script>


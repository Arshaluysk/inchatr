<?php $this->load->view('admin/theme/message'); ?>
<?php 
	if($this->session->userdata('page_insight_login_success')==1)
		echo '<div class="alert alert-success text-center">
			<h4><i class="fa fa-first-order"></i> Congratulation! all of your facebook pages have been added successfully.</h4>
		</div>';
	$this->session->unset_userdata('page_insight_login_success');
?>
<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><?php echo $this->lang->line("page list");?></h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; min-height:760px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."admin_facebook_page_insight/page_list_data"; ?>" 

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
							<th field="id"  checkbox="true"></th>
							<th field="page_name" sortable="true"><?php echo $this->lang->line('name');?></th>
							<th field="page_id" sortable="true"><?php echo $this->lang->line('id');?></th>
							<th field="page_email" sortable="true"><?php echo $this->lang->line('email');?></th>
							<th field="details" sortable="true"><?php echo $this->lang->line('Details');?></th>
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">
 

			<form class="form-inline" style="margin-top:20px">

				<div class="form-group">
					<input id="name" name="name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('page name');?>">
				</div>                    

				<button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("search report");?></button>
			
			</div>  

			</form> 

			</div>        
		</div>
	</div>   
</section>



<script>

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			name     		:     $j('#name').val(),              
			keyword     	:     $j('#keyword').val(),              
			from_date  		:     $j('#from_date').val(),    
			to_date    		:     $j('#to_date').val(),         
			is_searched		:     1
		});


	}

</script>


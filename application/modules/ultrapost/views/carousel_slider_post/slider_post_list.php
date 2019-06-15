<?php $this->load->view('admin/theme/message'); ?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><i class="fa fa-video-camera"></i> <?php echo $this->lang->line("Video Slide Show/Carousel Post Campaign List");?> </h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%;height:659px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."ultrapost/carousel_slider_post_list_data"; ?>" 

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
				
					<!-- url is the link to controller function to load grid data -->		
					<thead>
						<tr>
							<th field="campaign_name" sortable="true"><?php echo $this->lang->line('Campaign Name'); ?></th>
							<th field="campaign_type" sortable="true"><?php echo $this->lang->line('Campaign Type'); ?></th>
							<th field="publisher" sortable="true"><?php echo $this->lang->line('Publisher'); ?></th>
							<th field="post_type" sortable="true"><?php echo $this->lang->line('Post Type'); ?></th>
							<th field="scheduled_at" align="center" sortable="true"><?php echo $this->lang->line('Scheduled at'); ?></th>
							<th field="status" sortable="true"><?php echo $this->lang->line('Status'); ?></th>
							<th field="actions" align="center"><?php echo $this->lang->line('Actions'); ?></th>							
							<th field="error_mesage"><?php echo $this->lang->line('Error'); ?></th>							
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">

			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-primary" href="<?php echo base_url("ultrapost/carousel_slider_poster");?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('New Post'); ?></a>
				</div>
			</div>
 

			<form class="form-inline" style="margin-top:20px">

				<div class="form-group">
					<input id="campaign_name" name="campaign_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Campaign Name'); ?>">
				</div>

				<div class="form-group">
					<input id="page_or_group_or_user_name" name="page_or_group_or_user_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Page Name'); ?>">
				</div>   

				<div class="form-group">
					<select class="form-control" id="post_type" name="post_type">
						<option value=""><?php echo $this->lang->line('All Posts'); ?></option>
						<option value="slider_post"><?php echo $this->lang->line('Slider Post'); ?></option>
						<option value="carousel_post"><?php echo $this->lang->line('Carousel Post'); ?></option>
					</select>
				</div>   

				<div class="form-group">
					<input id="scheduled_from" name="scheduled_from" class="form-control datepicker" size="20" placeholder="<?php echo $this->lang->line('Scheduled from'); ?>">
				</div>

				<div class="form-group">
					<input id="scheduled_to" name="scheduled_to" class="form-control  datepicker" size="20" placeholder="<?php echo $this->lang->line('Scheduled to'); ?>">
				</div>                    

				<button class='btn btn-info'  onclick="doSearch(event)"><i class='fa fa-search'></i> <?php echo $this->lang->line("search");?></button> 
							
			</div>  

			</form> 

			</div>        
		</div>
	</div>   
</section>


<script>

	$j('.datepicker').datetimepicker({
    theme:'light',
    format:'Y-m-d',
    formatDate:'Y-m-d',
    timepicker:false
    });
    
	$(document.body).on('click','.delete',function(){
		var id = $(this).attr('id');
		 var text = "<?php echo $this->lang->line('Do you really want to delete this post from our database?'); ?>";
		 alertify.confirm('<?php echo $this->lang->line("are you sure");?>',text, 
		  function(){ 
		    $.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('ultrapost/carousel_slider_delete_post')?>",
		       data: {id:id},
		       success:function(response)
		       {
		       	if(response=='1')
		        {
		        	$j('#tt').datagrid('reload');
		        	alertify.success('<?php echo $this->lang->line("your data has been successfully deleted from the database."); ?>');
		        }
		       	else 
		       	alertify.alert('<?php echo $this->lang->line("Alert");?>','<?php echo $this->lang->line("something went wrong, please try again.");?>',function(){});
		       }
			});
		  },
		  function(){     
		  });
	});	
	$(document.body).on('click','.delete_p',function(){
		var id = $(this).attr('id');
		 var text = "<?php echo $this->lang->line('This is main campaign, if you want to delete it, rest of the sub campaign will be deleted.Do you really want to delete this post from our database?'); ?>";
		 alertify.confirm('<?php echo $this->lang->line("are you sure");?>',text, 
		  function(){ 
		    $.ajax({
		       type:'POST' ,
		       url: "<?php echo base_url('ultrapost/carousel_slider_delete_post')?>",
		       data: {id:id},
		       success:function(response)
		       {
		       	if(response=='1')
		        {
		        	$j('#tt').datagrid('reload');
		        	alertify.success('<?php echo $this->lang->line("your data has been successfully deleted from the database."); ?>');
		        }
		       	else 
		       	alertify.alert('<?php echo $this->lang->line("Alert");?>','<?php echo $this->lang->line("something went wrong, please try again.");?>',function(){});
		       }
			});
		  },
		  function(){     
		  });
	});

	$(document.body).on('click','.embed_code',function(){ 

		var id = $(this).attr("id");
		var loading = '<img src="<?php echo base_url('assets/pre-loader/Fading squares2.gif');?>" class="center-block">';
        $("#embed_code_content").html(loading);
		$("#embed_code_modal").modal();

		$.ajax({
	       type:'POST' ,
	       url: "<?php echo base_url('ultrapost/carousel_slider_get_embed_code')?>",
	       data: {id:id},
	       success:function(response)
	       { 
	       		$("#embed_code_content").html(response);
	       }
		});
	});

	
	

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			post_type   :     $j('#post_type').val(),              
			campaign_name   :     $j('#campaign_name').val(),              
			page_or_group_or_user_name  :     $j('#page_or_group_or_user_name').val(),              
			scheduled_from  		:     $j('#scheduled_from').val(),    
			scheduled_to    		:     $j('#scheduled_to').val(),         
			is_searched		:      1
		});

	} 



	$(document.body).on('click','.view_report',function(){
		var loading = '<img src="'+base_url+'assets/pre-loader/Fading squares2.gif" class="center-block">';
		$("#view_report_modal_body").html(loading);
		$("#view_report").modal();
		var table_id = $(this).attr('table_id');
		$.ajax({
	    	type:'POST' ,
	    	url: base_url+"ultrapost/ajax_carousel_slide_report",
	    	data: {table_id:table_id},
	    	// async: false,
	    	success:function(response){
	         	$("#view_report_modal_body").html(response);
	    	}

	    });

	});
	
	
</script>


<div class="modal fade" id="embed_code_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-code"></i><?php echo $this->lang->line('Get Embed Code'); ?></h4>
			</div>
			<div class="modal-body" id="embed_code_content">
				
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="view_report" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg" style="min-width: 70%;max-width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-list-alt"></i> <?php echo $this->lang->line("report of Video Slide Show/Carousel Post") ?></h4>
            </div>
            <div class="modal-body text-center" id="view_report_modal_body">                

            </div>
        </div>
    </div>
</div>
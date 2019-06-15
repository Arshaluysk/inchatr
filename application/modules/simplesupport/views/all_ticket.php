
<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'> <i class='fa fa-list-alt'></i> <?php echo $this->lang->line("All Support Ticket") ?> </h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; height:660px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."simplesupport/all_user_support_ticket_data"; ?>" 

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
						
						 <th field="ticket_title" align="left" sortable="true"><?php echo $this->lang->line("ticket title")?></th>

						 <th field="status" sortable="true"><?php echo $this->lang->line("status")?></th>
						
						 <th field="ticket_open_time2" sortable="true"><?php echo $this->lang->line("ticket open time")?></th>
						 <th field="category_name" sortable="true"><?php echo $this->lang->line("ticket category")?></th>
						 <!-- <th field="ticket_reply_time" sortable="true"><?php echo $this->lang->line("last reply")?></th> -->
						 <th field="reply"><?php echo $this->lang->line("reply")?></th> 
						 <th field="actions"><?php echo $this->lang->line("actions")?></th> 
						
					</tr>
				</thead>
			</table>                        
		</div>

		<div id="tb" style="padding:3px">
			<form class="form-inline" style="margin-top:20px">
				<div class="form-group">
					<select name="ticket_status" id="ticket_status" class="form-control">
						 
						    <option value=""><?php echo $this->lang->line("Ticket Status"); ?></option>
							<option value="1"><?php echo $this->lang->line("open"); ?></option>
							<option value="2"><?php echo $this->lang->line("closed"); ?></option>
						
					</select>
				</div>
				<div class="form-group">
					<input id="ticket_title" name="ticket_title" class="form-control" size="30" placeholder="<?php echo $this->lang->line('Ticket Title');?>">
				</div>
				<div class="form-group">
					<input id="id" name="id" class="form-control" size="30" placeholder="<?php echo $this->lang->line('Ticket Id');?>">
				</div>
				<button class='btn btn-info' onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>    
			</form> 
		</div>        
	</div>
</div>   
</section>

<?php 

 $Doyouwanttopausethiscampaign = $this->lang->line("do you want to close this ticket?");
 $Doyouwanttostarthiscampaign = $this->lang->line("do you want to open this ticket?");


 ?>

<script>



	

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{     
			ticket_title   :     $j('#ticket_title').val(),      
			ticket_status   :     $j('#ticket_status').val(),      
			id   :     $j('#id').val(),      
			is_searched		:     1
		});


	}
	var Doyouwanttopausethiscampaign = "<?php echo $Doyouwanttopausethiscampaign; ?>";
	
	$(document.body).on('click','.close_ticket',function(){
		var table_id = $(this).attr('table_id');
		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttopausethiscampaign, 
			function(){ 
				$.ajax({
					type:'POST' ,
					url: base_url+"simplesupport/ajax_autoreply_pause",
					data: {table_id:table_id},
					success:function(response){
						$j('#tt').datagrid('reload');
					}

				});
			},
			function(){     
		});
	});

	var Doyouwanttostarthiscampaign = "<?php echo $Doyouwanttostarthiscampaign; ?>";
	$(document.body).on('click','.open_ticket',function(){
		var table_id = $(this).attr('table_id');
		alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttostarthiscampaign, 
			function(){ 
				$.ajax({
					type:'POST' ,
					url: base_url+"simplesupport/ajax_autoreply_play",
					data: {table_id:table_id},
					success:function(response){
						$j('#tt').datagrid('reload');
					}

				});
			},
			function(){     
		});
	});

	
</script>




<?php $Doyouwanttodeletethisrecordfromdatabase = $this->lang->line("do you want to delete this record from database?"); ?>

<script>
	    var base_url="<?php echo site_url(); ?>";
        var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $Doyouwanttodeletethisrecordfromdatabase; ?>";
        $(document.body).on('click','.delete',function(){
        var id = $(this).attr("id");
        alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttodeletethisrecordfromdatabase, 
            function(){ 
                $.ajax({
                    type:'POST' ,
                    url: base_url+"simplesupport/delete_user_ticket_all",
                    data: {id:id},
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




</script>


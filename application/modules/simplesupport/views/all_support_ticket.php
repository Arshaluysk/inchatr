
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
				url="<?php echo base_url()."simplesupport/all_support_ticket_data"; ?>" 

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
						
						<th field="ticket_title" sortable="true"><?php echo $this->lang->line("Ticket Title")?></th>

						<th field="status" sortable="true"><?php echo $this->lang->line("Status")?></th>
						
						 <th field="ticket_open_time" sortable="true"><?php echo $this->lang->line("Ticket Open Time")?></th>
						 <th field="category_name" sortable="true"><?php echo $this->lang->line("Ticket Category")?></th>

						 <th field="action" sortable="true"><?php echo $this->lang->line("Actions")?></th> 
						
					</tr>
				</thead>
			</table>                        
		</div>

		<div id="tb" style="padding:3px">
			<a class="btn btn-primary" href="<?php echo site_url('simplesupport/support');?>">
				<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Open New Ticket");?>
			</a> 

			<form class="form-inline" style="margin-top:20px">
				<div class="form-group">
					<select name="ticket_status" id="ticket_status" class="form-control">
						 
						    <option value=""><?php echo $this->lang->line("Ticket Status"); ?></option>
							<option value="1"><?php echo $this->lang->line("open"); ?></option>
							<option value="2"><?php echo $this->lang->line("close"); ?></option>
						
					</select>
				</div>
				<div class="form-group">
					<input id="ticket_title" name="ticket_title" class="form-control" size="30" placeholder="<?php echo $this->lang->line('Ticket Title');?>">
				</div>
				<button class='btn btn-info' onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("Search");?></button>    
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
			ticket_title   :     $j('#ticket_title').val(),      
			ticket_status   :     $j('#ticket_status').val(),      
			is_searched		:     1
		});


	}

	
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
                    url: base_url+"simplesupport/delete_user_ticket",
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


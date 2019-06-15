<?php $this->load->view('admin/theme/message'); ?>

<style type="text/css">
    .button{
        margin-top: 10px;
    }
</style>
<!-- Main content -->
<section class="content-header">
    <h1 class = 'text-info'> <i class="fa fa-ticket"></i> <?php echo $this->lang->line('All Support Category'); ?> </h1>
</section>

<section class="content">  
    <div class="row" >
        <div class="col-xs-12">
            <div class="grid_container" style="width:100%; height:657px;">
                <table 
                id="tt"
                class="easyui-datagrid" 
                url="<?php echo base_url()."simplesupport/support_category_manager_data"; ?>" 
                pagination="true" 
                rownumbers="true" 
                toolbar="#tb" 
                pageSize="10" 
                pageList="[5,10,15,20,50,100,500,1000]"  
                fit= "true" 
                fitColumns= "true" 
                nowrap= "true" 
                view= "detailview"
                idField="id"
                >
                    <!-- url is the link to controller function to load grid data -->
                    <thead>
                        <tr>
                           
                            <th field="category_name"><?php echo $this->lang->line("Category Name"); ?></th>
                            <th field="action" sortable="true"><?php echo $this->lang->line("Action")?></th>
                        </tr>
                    </thead>
                </table>                        
            </div>

            <div id="tb" style="padding:3px">
              
                <a class="btn btn-primary" href="<?php echo base_url('simplesupport/create_category') ?>" style="color: white;"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('add category'); ?></a> 


                <form class="form-inline" style="margin-top:20px">

                    <div class="form-group">
                        <input id="category_name" name="category_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Category Name'); ?>">
                    </div>

                    <button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("search");?></button>               
                </form> 

            </div>  

        </div>
    </div>   
</section>

<?php $Doyouwanttodeletethisrecordfromdatabase = $this->lang->line("do you want to delete this record from database?"); ?>

<script>
	function doSearch(event)
    {
        event.preventDefault(); 
        $j('#tt').datagrid('load',{
            category_name   :     $j('#category_name').val(),                  
            is_searched      :      1
        });
    }



        var Doyouwanttodeletethisrecordfromdatabase = "<?php echo $Doyouwanttodeletethisrecordfromdatabase; ?>";
        $(document.body).on('click','.delete',function(){
        var id = $(this).attr("id");
        alertify.confirm('<?php echo $this->lang->line("are you sure");?>',Doyouwanttodeletethisrecordfromdatabase, 
            function(){ 
                $.ajax({
                    type:'POST' ,
                    url: base_url+"simplesupport/delete_support_category",
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

        $(document).on('click','.close',function(){

            location.reload();

        });
</script>
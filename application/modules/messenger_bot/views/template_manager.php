<?php $this->load->view('admin/theme/message'); ?>
<style>
  .datagrid-body
  {
    overflow: hidden !important; 
  }
</style>
<!-- Main content -->
<section class="content-header">
    <h1 class = 'text-info'> <i class="fa fa fa-th-large"></i> <?php echo $this->lang->line('Post-back Manager'); ?> </h1>
</section>
<section class="content">  
    <div class="row" >
        <div class="col-xs-12 table-responsive">
            <div class="grid_container" style="min-width:700px; height:659px;">
                <table 
                id="tt"
                class="easyui-datagrid" 
                url="<?php echo base_url()."messenger_bot/template_manager_data/"; ?>" 
                pagination="true" 
                rownumbers="true" 
                toolbar="#tb" 
                pageSize="10" 
                pageList="[5,10,15,20,50,100,500,1000]"  
                fit= "true" 
                fitColumns= "true" 
                nowrap= "true" 
                view= "detailview"
                >
                    <!-- url is the link to controller function to load grid data -->
                    <thead>
                        <tr>
                            <!-- <th field="id"><?php echo $this->lang->line("Id"); ?></th> -->
                            <th style="width: 30%;" align="left" field="template_name"><?php echo $this->lang->line("Postback Template Name"); ?></th>
                            <th style="width: 20%;" align="left" field="postback_id"><?php echo $this->lang->line("Postback ID"); ?></th>
                            <th style="width: 20%;" align="left" field="page_name" sortable="true"><?php echo $this->lang->line("Page Name")?></th>
                            <th style="width: 30%;" field="action" sortable="true"><?php echo $this->lang->line("Actions")?></th>
                        </tr>
                    </thead>
                </table>                        
            </div>

            <div id="tb" style="padding:3px">
              
                <a class="btn btn-primary" href="<?php echo base_url('messenger_bot/create_new_template'); ?>"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Create new template'); ?></a>  

                <form class="form-inline" style="margin-top:20px">

                    <div class="form-group">
                        <input id="page_name" name="page_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Page Name'); ?>">
                    </div>

                    <div class="form-group">
                        <input id="postback" name="postback" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Postback'); ?>">
                    </div>


                    <button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("search");?></button>               
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
            page_name   :     $j('#page_name').val(),        
            postback        :     $j('#postback').val(),            
            is_searched      :      1
        });
    }

    $j(document).ready(function(){

        var base_url = "<?php echo base_url(); ?>";

        $(document.body).on('click','.delete_template',function(event){
            event.preventDefault();

            var table_id = $(this).attr('table_id');
            var doDelete = "<?php echo $this->lang->line('Do you want to detete this template?');?>";

            alertify.confirm('<?php echo $this->lang->line("are you sure");?>',doDelete, 
              function(){ 
                $("#delete_template_modal_body").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fading squares2.gif" alt="Processing..."><br/>');
                $("#delete_template_modal").modal();
                $.ajax({
                    type:'POST' ,
                    url:"<?php echo site_url();?>messenger_bot/ajax_delete_template_info",
                    data:{table_id:table_id},
                    success:function(response){
                        if(response!='success')
                        $("#delete_template_modal_body").html(response);
                        else
                        {                           
                            $("#delete_template_modal").modal('hide');
                            $j('#tt').datagrid('reload');
                            alertify.success('<?php echo $this->lang->line("Template has been deleted successfully."); ?>');
                        }
                    }

                });
              },
              function(){     
              });

        });
        
        // $('#delete_template_modal').on('hidden.bs.modal', function () { 
        //     $j('#tt').datagrid('reload');
        // });
    });

</script>


<div class="modal fade" id="delete_template_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center"><i class="fa fa-trash"></i> <?php echo $this->lang->line("Template Delete Confirmation"); ?></h4>
            </div>
            <div class="modal-body" id="delete_template_modal_body">                

            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/theme/message'); ?>
<?php    
    $view_permission    = 1;
    $edit_permission    = 1;
    $delete_permission  = 1;
?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><i class="fa fa-bullhorn"></i> <?php echo $this->lang->line("Announcement List"); ?> </h1>

</section>

<!-- Main content -->
<section class="content">  
  <div class="row">
    <div class="col-xs-12">
        <div class="grid_container" style="width:100%; height:659px;">
            <table 
            id="tt"  
            class="easyui-datagrid" 
            url="<?php echo base_url()."announcement/list_data"; ?>" 
            
            pagination="true" 
            rownumbers="true" 
            toolbar="#tb" 
            pageSize="10" 
            pageList="[5,10,20,50,100]"  
            fit= "true" 
            fitColumns= "true" 
            nowrap= "true" 
            view= "detailview"
            idField="id"
            >
            
               <thead>
                 <tr>
                     <th field="title"><?php echo $this->lang->line("title"); ?></th>                
                     <?php if($this->session->userdata('user_type') == 'Admin') echo '<th align="center" field="status" sortable="true">'.$this->lang->line("status").'</th>'; ?>                
                     <th align="center" field="created_at" sortable="true"><?php echo $this->lang->line("created at"); ?></th>
                     <th align="center" field="action"><?php echo $this->lang->line("actions"); ?></th>
                 </tr>
               </thead>
            </table>                        
         </div>
  
       <div id="tb" style="padding:3px">

            <?php 
            if($this->session->userdata('user_type') == 'Admin' && $this->is_demo != '1')
            { ?>
              <a class="btn btn-primary" href="<?php echo base_url('announcement/add');?>">
                <i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Add"); ?>
              </a> <?php
            }
            ?>
            <form class="form-inline" style="margin-top:20px">

                <div class="form-group">
                    <input id="search_title" value="<?php echo $this->session->userdata("announcement_title");?>" name="search_title" class="form-control" size="20" placeholder="<?php echo $this->lang->line("title"); ?>">
                </div> 

                <div class="form-group">
                    <input id="search_from_date" value="<?php echo $this->session->userdata("announcement_from_date");?>" name="search_from_date" class="form-control datepicker" size="20" placeholder="<?php echo $this->lang->line("from date"); ?>">
                </div>

                <div class="form-group">
                    <input id="search_to_date" value="<?php echo $this->session->userdata("announcement_to_date");?>" name="search_to_date" class="form-control  datepicker" size="20" placeholder="<?php echo $this->lang->line("to date"); ?>">
                </div>  

                <button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-search"></i> <?php echo $this->lang->line("search"); ?></button>
                      
            </form> 

        </div>        
    </div>
  </div>   
</section>


<script>       
    
    $j('.datepicker').datetimepicker({
    theme:'light',
    format:'Y-m-d H:i:s',
    formatDate:'Y-m-d H:i:s'
    });

    var base_url="<?php echo site_url(); ?>";    
   
    function doSearch(event)
    {
        event.preventDefault(); 
        $j('#tt').datagrid('load',{
          search_title:            $j('#search_title').val(),
          search_from_date:        $j('#search_from_date').val(),
          search_to_date:          $j('#search_to_date').val(),
          is_searched:      1
        });
    }  
</script>

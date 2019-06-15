<style>
    .note-toolbar
    {
        height: 40px !important;
    }

    .note-toolbar-wrapper
    {
        height: 35px;
    }

    .note-editable
    {
        padding-top: 37px !important;
        min-height: 200px !important;
    }

    .ajax-upload-dragdrop
    {
        width: 100% !important;
    }

    .box:after 
    {
        padding-bottom: 5px !important;
    }
</style>
<?php $this->load->view('admin/theme/message'); ?>

<div class="well well_border_left">
    <h4 class="text-center" id="home"> <i class="fa fa-ticket"></i> <?php echo $this->lang->line("Open new support ticket");?></h4>
</div>

<div class="container-fluid" style="padding: 10px 25px 25px 25px !important;">


    <form id="auto_poster_form"  action="<?php echo base_url('simplesupport/create_ticket'); ?>" method="POST" enctype="multipart/form-data">

        <div class="box box-primary account_box">
<!--             <div class="box-header ui-sortable-handle text-center" style="cursor: move;">
                <i class="fa fa-ticket"></i>
                <h3 class="box-title"><?php echo $this->lang->line("Support Ticket");  ?></h3>
         
                <div class="pull-right box-tools"></div>
            </div> -->
            <div class="box-body" style="padding: 40px 120px !important;">
          
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line("Ticket Title"); ?> </label>
                            <input class="form-control" name="ticket_title" id="ticket_title" type="input" required>
                        </div>
                        
                    </div>


                    <div class="col-sm-12 col-md-6 col-lg-6" style="margin-bottom: 20px;">
                       <label><?php echo $this->lang->line('Select Support Category') ?></label>
                       <select  class="form-control" id="support_category" name="support_category" required>
                       <?php
                           echo "<option value=''>{$this->lang->line('Please select a category')}</option>";
                           foreach($support_category as $key=>$val)
                           {
                               $id=$val['id'];
                               $group_name=$val['category_name'];
                               echo "<option value='{$id}'>{$group_name}</option>";
                           }
                        ?>
                       </select>
                    </div>
                    </div>
                
                    <div class="row">
                      <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group" id="image_rich_content_block">
                            <label><?php echo $this->lang->line('Ticket Details'); ?></label>
                            <!-- <div id="toolbar-container"></div> -->
                            <div id="ckeditor">
                                <textarea required class="form-control" name="ticket_text" id="ticket_text"></textarea>
                            </div>
                        </div>
                     </div>
                </div>
                <br>
                <div class="row">
                    <div class="text-center">    
                        <button id="submit_html_info" type="submit" class="btn btn-primary" style="padding: 12px;"><i class="fa fa-paper-plane" aria-hidden="true"></i> 
                            <?php echo $this->lang->line("Create Ticket"); ?>
                        </button>
                        <a class="btn btn-default border_gray" style="padding: 12px;" onclick="goBack('simplesupport/support_list',1)"><i class="fa fa-remove"></i> 
                            <?php echo $this->lang->line("Cancel"); ?>
                        </a>
                    </div>
                </div>
                 

            </div>
        </div>

      
  </form>

</div>



<script>
    
    $j(document).ready(function() {
        
       
     /*-------------  rich content  ------------------*/
        $('#ticket_text').summernote();
   
    });
</script>
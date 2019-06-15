<?php $this->load->view('admin/theme/message'); ?>

<div class="well well_border_left">
    <h4 class="text-center" id="home"> <i class="fa fa-ticket"></i> <?php echo $this->lang->line("edit support ticket category");?></h4>
</div>
<div class="container-fluid" style="padding: 10px 25px 25px 25px !important;">


    <form id="auto_poster_form"  action="<?php echo base_url('simplesupport/edit_category_action/'.$edit_support_category[0]['id']); ?>" method="POST" enctype="multipart/form-data">

        <div class="box box-primary account_box">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
                <i class="fa fa-ticket "></i>
                <h3 class="box-title"><?php echo $this->lang->line("Support Ticket");  ?></h3>
                <!-- tools box -->
                <div class="pull-right box-tools"></div><!-- /. tools -->
            </div>
            <div class="box-body" style="padding: 30px !important;">
          
                <div class="row">
                      <div class="col-sm-12 col-md-3">
                        <label style="margin-left: 120px;margin-bottom: 0px!important;margin-top: 5px;"><?php echo $this->lang->line("Category Name"); ?> </label>
                    </div>
                     <div class="col-sm-12 col-md-6 col-lg-6">
                        
                        
                        <div class="form-group">
                           
                            <input class="form-control" name="category_name" id="category_name" type="input" value="<?php echo $edit_support_category[0]['category_name']; ?>" required>
                        </div>
                        
                    </div>
                </div>

                    <div class="text-center submitting">    
                        <button id="submit_html_info" type="submit" class="btn btn-primary" style="padding: 10px;"><i class="fa fa-save" aria-hidden="true"></i> 
                            <?php echo $this->lang->line("Save"); ?>
                        </button>
                        <a class="btn btn-default border_gray" style="padding: 10px;" onclick="goBack('simplesupport/support_category',1)"><i class="fa fa-remove"></i> 
                            <?php echo $this->lang->line("Cancel"); ?>
                        </a>
                    </div>
                </div>
                 

            </div>
        </div>

      
  </form>

</div>
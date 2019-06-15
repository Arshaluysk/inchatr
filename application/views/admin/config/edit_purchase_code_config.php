<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
        <div class="" id="modal-id">
            <div class="modal-dialog" style="width: 100%;margin:0;">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                        <h4 class="modal-title"><i class="fa fa-cogs"></i> <?php echo $this->lang->line("purchase code settings");?></h4>
                    </div>
                    <form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url().'admin_config/edit_purchase_code_config';?>" method="POST">           
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("purchase code");?>
                            </label>
                            <?php
                                $file_data = file_get_contents(APPPATH . 'core/licence.txt');
                                $file_data_array = json_decode($file_data, true);
                            ?>
                            <div class="col-sm-9 col-md-6 col-lg-6">
                                <input name="purchase_code" value="<?php echo $file_data_array['purchase_code'];?>"  disabled class="form-control" type="text">                
                                <span class="red"><?php echo form_error('purchase_code'); ?></span>
                            </div>
                       </div>
                    </div> 
                    <div class="modal-footer" style="text-align:center;">
                        <button name="submit" type="submit" class="btn btn-danger btn-lg"><i class="fa fa-trash"></i> <?php echo $this->lang->line("delete purchase code");?></button>
                            <button  type="button" class="btn btn-default btn-lg" onclick='goBack("admin_config/purchase_code_configuration",1)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
                    </div>
                    </form>
                </div>
            </div>
        </div>      
   </section>
</section>


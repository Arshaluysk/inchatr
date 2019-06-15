<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
        <div class="" id="modal-id">
            <div class="modal-dialog" style="width: 100%;margin:0;">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                        <h4 class="modal-title"><i class="fa fa-pie-chart"></i> <?php echo $this->lang->line("analytics settings");?></h4>
                    </div>
                    <form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url().'admin_config/analytics_config_action';?>" method="POST">           
                    <div class="modal-body">
                        <div class="form-group">
                             <label class="col-xs-12" for=""><?php echo $this->lang->line("enter your facebook pixel code");?>
                             </label>
                             <?php
                                 $pixel_code = file_get_contents(APPPATH . 'views/include/fb_px.php');
                             ?>
                             <div class="col-xs-12">
                                 <textarea name="pixel_code" class="form-control" rows="10"><?php echo trim($pixel_code);?></textarea>        
                                 <span class="red"><?php echo form_error('pixel_code'); ?></span>
                             </div>
                        </div>

                        <div class="form-group">
                             <label class="col-xs-12" for=""><?php echo $this->lang->line("enter your google analytics code");?>
                             </label>
                             <?php
                                 $file_data = file_get_contents(APPPATH . 'views/include/google_code.php');
                             ?>
                             <div class="col-xs-12">
                                 <textarea name="google_code" class="form-control" rows="10"><?php echo trim($file_data);?></textarea>        
                                 <span class="red"><?php echo form_error('google_code'); ?></span>
                             </div>
                        </div>

                    </div> 
                    <div class="modal-footer" style="text-align:center;">
                        <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
                            <button  type="button" class="btn btn-default btn-lg" onclick='goBack("admin_config/analytics_config",1)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
                    </div>
                    </form>
                </div>
            </div>
        </div>      
   </section>
</section>


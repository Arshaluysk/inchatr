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
        min-height: 160px !important;
    }

    .ajax-upload-dragdrop
    {
        width: 100% !important;
    }

    .box:after 
    {
        padding-bottom: 5px !important;
    }
    .reply-box{
    border:1px solid #ddd; padding: 20px 10px 20px 20px; 
    }
    .reply-time span{font-weight: normal}
    .reply-time {
         text-decoration: underline;
         font-weight: 700;
    }
   
</style>
<?php $this->load->view('admin/theme/message'); ?>

<div class="well well_border_left">
    <h4 class="text-center" id="home"> <i class="fa fa-ticket"></i> <?php echo $this->lang->line("Ticket Reply");?></h4>
</div>

<div class="container-fluid" style="padding: 10px 25px 25px 25px !important;">


    <ul class="timeline">

        <!-- timeline time label -->
        <li class="time-label">
            <span class="bg-blue">
           <?php echo $this->lang->line('Ticket Open Date:'); ?>  <?php  echo date("M d, Y H:i:s",strtotime($ticket_info[0]['ticket_open_time'])); ?> (#<?php echo $ticket_info[0]['id']; ?> <?php echo $ticket_info[0]['category_name']; ?>)

            </span>
            <?php if($this->session->userdata("user_type")=='Admin'): ?>
                <?php if($ticket_info[0]['ticket_status']=='1'): ?>
                <span style="background-color: #ecf0f5;" class="pull-right"><button table_id=<?php echo $ticket_info[0]['id'] ?> title="" class="btn btn-danger close_ticket"><i class="fa fa-close"></i> <?php echo $this->lang->line('Close'); ?></button></span>
                <?php  else: ?>
                 <span style="background-color: #ecf0f5;" class="pull-right"><label class="label label-info"><i class="fa fa-close"></i> <?php echo $this->lang->line('Closed'); ?></label></span>
                <?php endif; ?>
            <?php endif; ?>



        </li>
        <!-- /.timeline-label -->

        <!-- timeline item -->
        <li>
            <!-- timeline icon -->
            <i class="fa fa-envelope bg-blue"></i>
            <div class="timeline-item">
                </span> <span class="time"><i class="fa fa-clock-o"></i> <?php echo date("H:s",strtotime($ticket_info[0]['ticket_open_time'])); ?></span>

                <h3 class="timeline-header" style="margin-left: 10px;"><b><?php echo $ticket_info[0]['ticket_title']; ?></b>  (<?php echo $user_info[0]['name']; ?>)
                </h3>

                <div class="timeline-body" style="margin-left: 10px;padding-right:20px;text-align: justify;">
                    <?php echo $ticket_info[0]['ticket_text']; ?>
                </div>

                <hr>
                <h4 style="margin-left: 14px;font-weight:400;"><?php echo $this->lang->line('Replies:') ?></h4>
                <br>
        <?php foreach($ticket_replied as $single_reply):  ?>
          <div class="row"> 

            <div class="col-xs-2 col-md-2 col-lg-1" >
                <?php if($single_reply['brand_logo']!='') :?>
                <img style="width: 60px;height: 60px; border: 1px solid #eee;padding: 2px;margin-left: 20px;" class="img-thubnail" src="<?php echo base_url('member/').$single_reply['brand_logo']; ?> " alt="">

                <?php  else: ?>
                    <img style="width: 60px;height: 60px; border: 1px solid #eee;padding: 2px;margin-left: 20px;" class="img-thubnail" src="<?php echo base_url('assets/images/avatar.png'); ?> " alt="">
                <?php  endif; ?>

            </div>
            <div class="col-xs-10 col-md-10 col-lg-11">
                <div style="padding-left:20px;">
                    <span style="font-weight: 700;"><span class="blue"><?php echo $single_reply['name']; ?></span> &nbsp;&nbsp;<small style="font-weight: normal;"><i class="fa fa-clock-o"></i> <?php echo date("M d,Y H:i:s",strtotime($single_reply['ticket_reply_time'])); ?></small></span>

                </div>
                <div style="padding-left:20px;padding-right:20px;text-align: justify;">
                    <p style="text-align: justify;max-width: 94%;"> <?php if(isset($single_reply['ticket_reply_text'])) echo $single_reply['ticket_reply_text']; ?> </p>
                </div>
            </div>
        </div>
        <hr>
         <?php endforeach; ?>
<!--                  <div class="timeline-body">
                    <?php foreach($ticket_replied as $single_reply):  ?>
                    <div class="reply-box">
                        
                         <p class="reply-time">Last Reply:
                         <span class="pull-right"><i class="fa fa-clock-o"></i><?php echo $single_reply['ticket_reply_time']; ?></span></p> 
                         <br> <?php 
                                    echo $single_reply['name'];

                               ?>
                         <div> <?php if(isset($single_reply['ticket_reply_text'])) echo $single_reply['ticket_reply_text']; ?> </div> 
                        
                    </div>
             
                      <?php endforeach; ?>
                 </div> -->


                 
                <div class="timeline-footer">
                     <form class="from-show"  action="<?php echo base_url('simplesupport/reply_action/'.$ticket_info[0]['id']); ?>" method="POST" enctype="multipart/form-data" novalidate>
                         <div class="form-group">
                             <label><?php echo $this->lang->line('Ticket Reply'); ?></label>
                             <div id="ckeditor">
                                 <textarea required class="form-control" name="ticket_reply_text" id="ticket_reply_text"></textarea>
                             </div>
                        </div>
                        <div class="text-center">
                           <?php if($this->session->userdata("user_type")=="Admin") $red_link="simplesupport/all_ticket"; else  $red_link="simplesupport/support_list";?>
                           <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> <?php echo $this->lang->line('Reply'); ?> </button> 
                           <a onclick="goBack('<?php echo $red_link ?>',1)" class="btn btn-default  cancel from-show"><i class="fa fa-close"></i> <?php echo $this->lang->line("Cancel"); ?> </a>
                        </div>
                    
                     </form>
                    
                   
                           
                                    
                            
                </div>
            </div>
        </li>
        <!-- END timeline item -->

        

    </ul>


</div>
<?php $Doyouwanttopausethiscampaign = $this->lang->line("do you want to close this ticket?"); ?>


<script>
    


    $j(document).ready(function() {
        
       
     /*-------------  rich content  ------------------*/
        $('#ticket_reply_text').summernote();
   
    });



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
                        location.reload();
                    }

                });
            },
            function(){     
        });
    });
</script>
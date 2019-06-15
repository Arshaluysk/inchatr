<div id="modal_send_sms_email" class="">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
        <h4 id="SMS" class="modal-title"> <i class="fa fa-eye"></i> <?php echo $this->lang->line("Announcement"); ?></h4>
      </div>

      <div id="modalBody" class="modal-body">        
        <div id="show_message" class="text-center"></div>

        <div class="form-group">
          <label><?php echo $this->lang->line("title"); ?> </label> : <?php echo $xdata['title'];?>
        </div>

        <div class="form-group">
          <label><?php echo $this->lang->line("description"); ?> : </label><br/>
          <br><div><?php echo nl2br($xdata['description']);?></div><br>
        </div>

        <div class="form-group">
          <label><?php echo $this->lang->line("status"); ?> </label> : <?php echo $this->lang->line($xdata['status']); ?>
        </div>

        <div class="form-group">
          <label><?php echo $this->lang->line("created at"); ?> </label> : <?php echo date("jS M, y H:i:s",strtotime($xdata['created_at'])); ?>
        </div>
     
      </div>
    </div>
  </div>
</div>

<script> 
  var base_url="<?php echo base_url();?>";

  $j("document").ready(function(){

    $('.checkicon,.new_button_block').css('display','none');

    $(document.body).on('click','.css-label',function(){
      if($(this).hasClass('dynamic_color')) return false;
      $(this).siblings().removeClass('dynamic_color').css('color',"#000");
      $(this).addClass('dynamic_color').css('color',"#fff");
      $(this).siblings().children('.checkicon').hide();
      $(this).children('.checkicon').toggle();
   });

  $(".default-label").click();

});

</script>

<style type="text/css">
  .css-checkbox{display: none;}
  .css-label{padding:14px 20px; background: #ccc;border-radius: 15px;text-align: center;}
  .css-label:hover{background: #ddd;cursor: pointer;}
  .single-label{min-width: 99%;}
  .double-label{min-width: 49.5%;}
  .triple-label{min-width: 32%;}
</style>
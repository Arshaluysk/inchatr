<div id="modal_send_sms_email" class="">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
        <h4 id="SMS" class="modal-title"> <i class="fa fa-edit"></i> <?php echo $this->lang->line("Announcement"); ?></h4>
      </div>

      <div id="modalBody" class="modal-body">        
        <div id="show_message" class="text-center"></div>

        <div class="form-group">
          <label><?php echo $this->lang->line("title"); ?> *</label><br/>
          <input type="hidden" id="hidden_id" name="hidden_id" value="<?php echo $xdata['id'];?>"/>
          <input type="text" id="title" name="title" required class="form-control" value="<?php echo $xdata['title'];?>"/>
        </div>

        <div class="form-group">
          <label><?php echo $this->lang->line("description"); ?> *</label><br/>
          <textarea name="description" required style="width:100%;height:200px;" id="description"><?php echo $xdata['description'];?></textarea>
        </div>

        <div class="form-group">
          <label><?php echo $this->lang->line("status"); ?> *</label><br>
          <input type="radio" class="css-checkbox" name="status" value="published" id="published" <?php if($xdata['status']=='published') echo 'checked'; ?>> <label for="published" class="<?php if($xdata['status']=='published') echo 'default-label'; ?> css-label four-label"> <i class="checkicon fa fa-check"></i> <?php echo $this->lang->line("published") ?></label>
          <input type="radio" class="css-checkbox" name="status" value="draft" id="draft" <?php if($xdata['status']=='draft') echo 'checked'; ?>> <label for="draft" class="<?php if($xdata['status']=='draft') echo 'default-label'; ?> css-label four-label"> <i class="checkicon fa fa-check"></i> <?php echo $this->lang->line("draft") ?></label> 
        </div>
     
      </div>

      <div class="modal-footer clearfix" style="text-align:center;">
           <button id="send_sms_email" class="btn btn-primary btn-lg" > <i class="fa fa-save"></i>  <?php echo $this->lang->line("Save"); ?></button>
            <button  type="button" class="btn btn-default btn-lg" onclick='goBack("announcement/full_list",1)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
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

   $("#send_sms_email").click(function(){      
                  
    var title=$("#title").val();
    var hidden_id=$("#hidden_id").val();  
    var description=$("#description").val();  
    var status=$("input[name=status]:checked").val()
    
    if(title=="" || description=="" || status=="")
    {
      $("#show_message").attr("class","text-center alert alert-warning");
      $("#show_message").html("<?php echo $this->lang->line("something is missing"); ?>");
      return;
    }
    $(this).attr('disabled','yes');
    $("#show_message").attr("class","text-center alert alert-info");
    $("#show_message").show().html('<i class="fa fa-spinner fa-spin"></i> <?php echo $this->lang->line("please wait"); ?>');
    $.ajax({
    type:'POST' ,
    url: "<?php echo base_url(); ?>announcement/edit_action",
    data:{description:description,title:title,status:status,hidden_id:hidden_id},
    success:function(response){
      $("#send_sms_email").removeAttr('disabled');  
      var link="<?php echo base_url('announcement/full_list'); ?>"; 
      window.location.assign(link); 
    }
    });   
  }); 

});

</script>

<style type="text/css">
  .css-checkbox{display: none;}
  .css-label{padding:14px 20px; background: #ccc;border-radius: 10px;text-align: center;}
  .css-label:hover{background: #ddd;cursor: pointer;}
  .single-label{min-width: 99%;}
  .double-label{min-width: 49.5%;}
  .triple-label{min-width: 32%;}
</style>
<div class="container-fluid">
  <br>
  <?php if($this->session->flashdata('per_success')===1) { ?>
  <div class="alert alert-success text-center" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been created successfully.");?></div>
  <?php } ?>

  <?php if($this->session->flashdata('per_success')===0) { ?>
  <div class="alert alert-danger text-center"><i class="fa fa-remove"></i> <?php echo $this->session->flashdata('per_message');?></div>
  <?php } ?>

  <?php if($this->session->flashdata('per_update_success')===1) { ?>
  <div class="alert alert-success text-center" id="bot_success"><i class="fa fa-check"></i> <?php echo $this->lang->line("persistent menu has been updated successfully.");?></div>
  <?php } ?>


  <?php $areyousure=$this->lang->line("are you sure"); ?>
  <?php 
    $started_button_enabled='';
    $started_button_enabled_msg="";
    if($page_info["started_button_enabled"]=='0')
    {
      $started_button_enabled=' disabled';
      $started_button_enabled_msg="<a style='text-decoration:none;' href='".base_url('messenger_bot/bot_list')."'>".$this->lang->line("To create persistent menu you must enable get started button first.")."</a>";
    }        
   ?>
  <?php if($started_button_enabled_msg!="") echo "<div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$started_button_enabled_msg."</div>";?>
  <div class="box box-widget widget-user-2" >
    <div class="widget-user-header" style="border-radius: 0;">
      <div class="row">
        <div class="col-xs-12 col-md-6">
          <div class="widget-user-image">
            <img class="img-circle" src="<?php echo $page_info['page_profile'];?>">
          </div>
          <h3 class="widget-user-username" style="margin-top:20px;"><a target="_BLANK" href="https://facebook.com/<?php echo $page_info['page_id'];?>"><?php echo $page_info['page_name'];?></a></h3>
        </div>     
        <div class="col-xs-12 col-md-6">
           <a class="btn btn-outline-primary pull-right <?php echo $started_button_enabled;?>" href="<?php echo base_url('messenger_bot/create_persistent_menu/'.$page_info['id']);?>" style="margin-top:15px;"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Create Persistent Menu');?></a>
        </div>       
      </div>
    </div>
    <div class="box-footer" style="border-radius: 0;padding:20px;">

      <div class="row">
        <div class="col-xs-12">
           <?php
           $publish_disabled='';
           $disabled_msg=$this->lang->line("menu settings for default locale is mandatory."); 
           if(!empty($menu_info)) 
           {             
            $local_array=array();
            foreach ($menu_info as $key => $value) 
            {
              array_push($local_array, $value['locale']);
            }
            if(!in_array('default', $local_array)) 
            {
              $publish_disabled='disabled';
            } ?>
            <a class="btn btn-primary pull-left" title="<?php echo $disabled_msg; ?>" href="<?php echo base_url('messenger_bot/publish_persistent_menu/'.$page_info['id']);?>" style="margin-top:10px;" id="publish_menu"><i class="fa fa-check"></i> <?php echo $this->lang->line('Publish Persistent Menu');?></a>
            <a class="btn btn-outline-danger pull-right remove_persistent_menu" href="<?php echo base_url('messenger_bot/remove_persistent_menu/'.$page_info['id']);?>" style="margin-top:10px;"><i class="fa fa-unlink"></i> <?php echo $this->lang->line('Remove Persistent Menu');?></a>
           <?php 
           } ?>
        </div>      
      </div>
      <br>

      <?php 
      if(empty($menu_info)) echo "<h4 class='text-center'>".$this->lang->line('no menu settings found.')."</h4>";
      else
      {
          echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-condensed' >";
              echo "<thead>";
                echo "<tr>";
                  echo "<th class='text-center'>".$this->lang->line("SN")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Locale")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Composer Input Disabled?")."</th>";
                  echo "<th class='text-center'>".$this->lang->line("Actions")."</th>";
                echo "</tr>";
              echo "</thead>";

              echo "<tbody>";
                $i=0;      
                foreach ($menu_info as $key => $value) 
                {
                  $i++;
                  $composer_input=$this->lang->line('no');
                  if($value['composer_input_disabled']=='1') $composer_input=$this->lang->line('yes');

                  echo "<tr>";
                    echo "<td class='text-center'>".$i."</td>";
                    echo "<td class='text-center'>".$value['locale']."</td>";                  
                    echo "<td class='text-center'>".$composer_input."</td>";
                     echo "<td class='text-center'>";
                      echo "<a title='".$this->lang->line("edit")."' class='btn btn-outline-warning btn-sm' href='".base_url("messenger_bot/edit_persistent_menu/".$value['id'])."'><i class='fa fa-edit'></i></a>";

                      $delete_class="btn btn-outline-danger btn-sm are_you_sure";
                      $delete_href=base_url("messenger_bot/remove_persistent_menu_locale/".$value['id']."/".$page_info['id']);
                      $delete_title=$this->lang->line("delete");
                      if($value['locale']=='default') 
                      {
                        $delete_class="btn btn-default btn-sm gray border_gray";
                        $delete_href="";
                        $delete_title=$this->lang->line("Default persistent menu can not be deleted");
                      }

                      echo " <a class='".$delete_class."' title='".$delete_title."' href='".$delete_href."'><i class='fa fa-trash'></i></a>";
                     echo "</td>";
                  echo "</tr>";
                }
              echo "</tbody>";
            echo "</table>";
          echo "</div>";

      }
      ?>
    </div>
  </div>

</div>



<script type="text/javascript">
  $j(document).ready(function(){
    $("#publish_menu").click(function(e){
      var publish_disabled="<?php echo $publish_disabled;?>";
      if(publish_disabled=='disabled')
      {
        alertify.alert('<?php echo $this->lang->line("Alert"); ?>',"<?php echo $disabled_msg;?>",function(){});
        e.preventDefault();
      }
    });

     $(document.body).on('click','.remove_persistent_menu',function(e){
      e.preventDefault();
      var link = $(this).attr("href");
      var mes='<?php echo $this->lang->line("Are you sure that you want to remove persistent menu from Facebook?");?>';  
      alertify.confirm('<?php echo $this->lang->line("are you sure");?>',mes, 
      function(){ 
        window.location.href = link;
      },
      function(){     
      });
    });

  });
</script>


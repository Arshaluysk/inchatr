<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add")." - ".$this->lang->line("package settings"); ?></h3>
       </div><!-- /.box-header -->
       <!-- form start -->
       <form class="form-horizontal" action="<?php echo site_url().'payment/add_package_action';?>" method="POST">
         <div class="box-body">
           <div class="form-group">
             <label class="col-sm-3 control-label" for="name"> <?php echo $this->lang->line("package name")?> *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input name="name" value="<?php echo set_value('name');?>"  class="form-control" type="text">
               <span class="red"><?php echo form_error('name'); ?></span>
             </div>
           </div>
           <div class="form-group">
             <label class="col-sm-3 control-label" for="price"><?php echo $this->lang->line("price")?> - <?php echo $payment_config[0]['currency']; ?> *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input name="price" value="<?php echo set_value('price');?>"  class="form-control" type="text">

               <span class="red"><?php echo form_error('price'); ?></span>
             </div>
           </div>
           <div class="form-group">
             <label class="col-sm-3 control-label" for="price"><?php echo $this->lang->line("validity");?> - <?php echo $this->lang->line("days"); ?> *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input name="validity" value="<?php echo set_value('validity');?>"  class="form-control" type="text">

               <span class="red"><?php echo form_error('validity'); ?></span>
             </div>
           </div>
           <div class="form-group">
             <label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("modules")?> * </label>
             <div class="col-sm-9">

             <div class="table-responsive">
             <table class="table table-bordered table-hover table-striped" style="width:auto;">
             	<tr>
             		<td colspan="5"><input  id="all_modules" type="checkbox"/> <font color="blue">&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $this->lang->line("All Modules"); ?></b></font> [<?php echo $this->lang->line("0 means unlimited");?>]</td>
             	</tr>
	             <?php                  
	                  // $current_modules=array();
	                  // if(count($this->input->post('modules'))>0)  
	                  // $current_modules=$this->input->post('modules');  

	              	  echo "<tr>";   
                        echo "<th class='text-center info'>"; 
                          echo $this->lang->line("sl");         
                        echo "</th>"; 
                        echo "<th class='info'>"; 
                          echo $this->lang->line("modules");         
                        echo "</th>";
                        echo "<th class='text-center info' colspan='2'>"; 
                          echo $this->lang->line("Limit");         
                        echo "</th>";
                        echo "<th class='text-center info' colspan='2'>"; 
                          echo $this->lang->line("Bulk Limit");         
                        echo "</th>";
                     echo "</tr>"; 

                    $no_limit_modules=array();
                    $not_monthly_modules=array();
                    foreach($modules as $module) 
                    {
                      if($module['limit_enabled']=='0')
                      $no_limit_modules[]=$module['id'];

                      if($module['extra_text']=='')
                      $not_monthly_modules[]=$module['id'];
                    }
                    $SL=0;
	                  foreach($modules as $module) 
	                  {  
                     $SL++;
	                 	 echo "<tr>"; 
                        echo "<td class='text-center'>".$SL."</td>";   
		                    echo "<td>";?>
			                     <input  name="modules[]" id="box<?php echo $SL;?>" class="modules"  type="checkbox" value="<?php echo $module['id']; ?>"/> <?php
                           echo "&nbsp;&nbsp;&nbsp;&nbsp;<label for='box".$SL."' style='cursor:pointer;'>".$module['module_name']."</label>";                
			                  echo "</td>";			                 

                        if(in_array($module["id"],$no_limit_modules))
                        {
                          $type="hidden";
                          $limit="";
                        }
                        else
                        {
                            $type="number";
                            if(in_array($module["id"],$not_monthly_modules)) $limit="";
                            else $limit='/ '.$this->lang->line($module['extra_text']);
                        }


                        echo "<td style='padding-left:10px'>".$limit."</td><td><input type='".$type."' value='0' min='0' style='width:70px;' name='monthly_".$module['id']."'></td>";
                      
                        if(!in_array($module["id"],array(110,111,112,113,114)))
                        {
                          $type="hidden";
                          $limit="";

                        }
                        else
                        {
                            $type="number";
                            // $limit=$this->lang->line("Bulk Limit")." / ".$this->lang->line("Analysis");
                            $limit="";
                        }
                        if($module["id"]==110) $xval=5;
                        else if($module["id"]==111) $xval=10;
                        else if($module["id"]==112) $xval=5;
                        else if($module["id"]==113) $xval=10;
                        else if($module["id"]==114) $xval=10;
                        else $xval=0;

  		                  echo "<td class='text-center'><input type='".$type."' value='".$xval."'  min='0' style='width:70px;' name='bulk_".$module['id']."'></td>";
	                  	echo "</tr>";                 
		               }                
	              ?>         		
             	</table> 
              </div>    
               <span class="red" ><?php echo "<br/><br/>".form_error('modules[]'); ?></span>  
              </div> 
           </div>
           


           </div> <!-- /.box-body --> 
           <div class="box-footer">
               <button name="submit" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> <?php echo $this->lang->line("Save");?></button>
               <button  type="button" class="btn btn-default btn-lg" onclick='goBack("payment/package_settings",0)'><i class="fa fa-remove"></i> <?php echo $this->lang->line("Cancel");?></button>
           </div><!-- /.box-footer -->         
         </div><!-- /.box-info -->       
       </form>     
     </div>
   </section>
</section>


<script type="text/javascript">
  $j(document).ready(function() {
    $("#all_modules").change(function(){
      if ($(this).is(':checked')) 
      $(".modules").prop("checked",true);
      else
      $(".modules").prop("checked",false);
    });
  });
</script>

<style type="text/css" media="screen">
  td,th{background:#fff}
</style>
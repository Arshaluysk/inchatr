<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("details")." - ".$this->lang->line("package settings"); ?></h3>
       </div><!-- /.box-header -->
       <!-- form start -->
       <form class="form-horizontal">
         <div class="box-body">
           <div class="form-group">
             <div class="col-xs-12 text-center" style="padding-top:7px">
             	 <h4 class="dynamic_font_color"><?php echo $this->lang->line("package name")?> : 
               <?php echo $value[0]["package_name"];?> @
               <?php echo $payment_config[0]['currency']; ?> <?php echo $value[0]["price"];?> /
               <?php echo $value[0]["validity"];?> <?php echo $this->lang->line("days")?>
               </h4>
             </div>
           </div>

           <div class="form-group">
             <div class="col-xs-12 col-md-10 col-md-offset-1">

             <div class="table-responsive">
             <table class="table table-bordered table-hover table-striped">
              <tr>
               <td colspan="5" align="center"><?php echo $this->lang->line("0 means unlimited");?></td>
              </tr>
               <?php                   

                    $current_modules=array();
                    $current_modules=explode(',',$value[0]["module_ids"]); 
                    $monthly_limit=json_decode($value[0]["monthly_limit"],true);
                    $bulk_limit=json_decode($value[0]["bulk_limit"],true);

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
                     if(in_array($module["id"],$current_modules))
                     {
                        $SL++;
                        echo "<tr>";    
                          echo "<td class='text-center'>";echo "<b>".$SL."</b>";echo "</td>";
                          echo "<td>";echo "<b>".$module['module_name']."</b>";echo "</td>";

                        $xmonthly_val=0;
                        $xbulk_val=0;

                        if(in_array($module["id"],$current_modules))
                        {
                          $xmonthly_val=$monthly_limit[$module["id"]];
                          $xbulk_val=$bulk_limit[$module["id"]];
                        }

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

                        echo "<td style='padding-left:10px'>".$limit."</td><td><input type='".$type."' disabled='disabled' value='".$xmonthly_val."' style='width:70px;' name='monthly_".$module['id']."'></td>";
                        
                        if(!in_array($module["id"],array(110,111,112,113,114)))
                        {
                          $type="hidden";
                          $limit="";
                        }
                        else
                        {
                          $type="text";
                          $limit="";
                        }

                        echo "<td class='text-center'><input type='".$type."' disabled='disabled' value='".$xbulk_val."' style='width:70px;' name='bulk_".$module['id']."'></td>";
                        echo "</tr>";      
                      }           
                   }                
                ?>            
              </table>
              </div>     
               <span class="red" ><?php echo "<br/><br/>".form_error('modules'); ?></span>  
              </div> 
           </div>         

           </div> <!-- /.box-body -->         
         </div><!-- /.box-info -->       
       </form>     
     </div>
   </section>
</section>

<style type="text/css" media="screen">
  td,th{background:#fff}
</style>
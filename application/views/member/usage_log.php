<?php $this->load->view('admin/theme/message'); ?>

<?php $cur_month=date("n"); ?>
<?php $cur_year=date("Y"); ?>
<?php 
if($cur_month==1) $month="";
else if($cur_month==2) $month="cal_jan";
else if($cur_month==3) $month="cal_feb";
else if($cur_month==4) $month="cal_ma";
else if($cur_month==5) $month="cal_apr";
else if($cur_month==6) $month="cal_may";
else if($cur_month==7) $month="cal_jun";
else if($cur_month==8) $month="cal_jul";
else if($cur_month==9) $month="cal_aug";
else if($cur_month==10) $month="cal_sep";
else if($cur_month==11) $month="cal_oct";
else if($cur_month==12) $month="cal_nov";

$unlimited_module_array=array();
?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><i class="fa fa-history"></i> <?php echo $this->lang->line("usage log");?> : <?php echo $this->lang->line($month)."-".$cur_year ?></h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">		

			
			<div class="grid_container table-responsive" style="width:auto;background:#fff;border:1px solid #ccc;padding:40px 20px 20px 20px;">
				<h4 class='text-center'>
					<div class="">			
				 	   <?php if($price=="Trial") $price=0; ?>
					   <?php echo $this->lang->line("package name")?> : 
					   <?php echo $package_name;?> @
					   <?php echo $payment_config[0]['currency']; ?> <?php echo $price;?> /
					   <?php echo $validity;?> <?php echo $this->lang->line("days")?>	<br/><br/>
					   <?php echo $this->lang->line("expired date");?> : <?php echo date("d M Y",strtotime($this->session->userdata("expiry_date"))); ?>			
					</div>
				</h4>
				<hr>	
				<table class="table table-bordered">
	               		<tr class="info">
	               			<th></th>
	               			<th><?php echo $this->lang->line("Modules");?></th>
	               			<th class="text-center"><?php echo $this->lang->line("Limit");?></th>
	               			<th class="text-center"><?php echo $this->lang->line("Request Done");?></th>
	               			<!-- <th class="text-center"><?php echo $this->lang->line("Bulk Limit");?></th> -->
	               		</tr>
	               	 	<?php 
	               	 	$no_limit_modules=array();
	                    $not_monthly_modules=array();
	                    foreach($info as $module) 
	                    {
	                      if($module['limit_enabled']=='0')
	                      $no_limit_modules[]=$module['module_id'];

	                      if($module['extra_text']=='')
	                      $not_monthly_modules[]=$module['module_id'];
	                    }
	               	 	$i=0;

	               	 	// echo "<pre>";
	               	 	// print_r($not_monthy_module_info);
	               	 	// echo "</pre>";

	               	 	foreach($info as $row)
	               	 	{
	               	 		// if(!in_array($row["module_id"],$this->module_access)) continue;
		               	 	$i++;
		               	 	$row_class="";
		               	 	if(in_array($row["module_id"],$this->module_access)) $row_class="allowed";
		               	 	echo "<tr class='".$row_class."'>";
		               	 		echo "<td class='text-center'>";
			               	 		echo $i;
			               	 	echo "</td>";
			               	 	echo "<td>";
			               	 		echo $row["module_name"];
			               	 	echo "</td>";

			               	 	$str="";
		               	 		if(!in_array($row["module_id"],$this->module_access)) // no access and skip
		               	 		{
		               	 			$str="<i class='fa fa-remove'></i>";
		               	 			echo "<td colspan='3' class='text-center'>{$str}</td>";
			               	 		echo "</tr>";
			               	 		continue;
		               	 		}
		               	 	
			               	 			               	 		
			               	 	if(in_array($row["module_id"], $no_limit_modules))
			               	 	{
			               	 		echo "<td class='text-center'>N/A</td>";
			               	 	}
		               	 		else
		               	 		{
		               	 			$bulk_limit_print=$bulk_limit[$row["module_id"]];
		               	 			if($bulk_limit_print==0) $bulk_limit_print=$this->lang->line("unlimited");

		               	 			echo "<td class='text-center'>";
		               	 			if($monthly_limit[$row["module_id"]]=="0") $monthly_limit[$row["module_id"]]=$this->lang->line("unlimited");
		               	 			if(isset($monthly_limit[$row["module_id"]])) 
		               	 			{
		               	 				echo $monthly_limit[$row["module_id"]];
		               	 				if($row["extra_text"]!="" && $monthly_limit[$row["module_id"]]>0) echo " / ".$this->lang->line($row["extra_text"]);
		               	 				if(in_array($row["module_id"],array(110,111,112,113,114))) echo " [".$this->lang->line('Bulk Limit')." : ". $bulk_limit_print."]";
		               	 			}
		               	 			echo "</td>";
		               	 		}



			               	 	echo "<td class='text-center' >";
			               	 		
			               	 		if($row["extra_text"]=="") // not monthly modules
				               	 	{
				               	 		if(isset($not_monthy_module_info[$row["module_id"]])) echo $not_monthy_module_info[$row["module_id"]];
				               	 		else echo "0";
				               	 	}
				               	 	else if(in_array($row["module_id"], $no_limit_modules))
				               	 	{
				               	 		echo "N/A";
				               	 	}
			               	 		else
			               	 		{
			               	 			if($str!="") echo $str;
				               	 		else
				               	 		{
				               	 			if(isset($row["usage_count"])) echo $row["usage_count"];
				               	 			else echo "0";
				               	 		}
			               	 		}
			               	 	echo "</td>";



		               	 		// if(!in_array($row["module_id"], array(3,4,5,6,7,8,9,10,15,18,21,22,58)))
			               	 	// {
			               	 	// 	echo "<td class='text-center'>Unlimited</td>";
			               	 	// }
		               	 		// else
		               	 		// {	
		               	 		// 	echo "<td class='text-center'>";
		               	 		// 	if(isset($bulk_limit[$row["module_id"]])) 
		               	 		// 	{
		               	 		// 		if($bulk_limit[$row["module_id"]]=="0") $bulk_limit[$row["module_id"]]=$this->lang->line("unlimited");
		               	 		// 		echo $bulk_limit[$row["module_id"]];
		               	 		// 	}
		               	 		// 	echo "</td>";
		               	 		// }



		               	 	echo "</tr>";
	               	 	} 
	               	 	?>
	              </table>                      
			</div>

		</div>        
	</div> 
</section>

<style>
	td{font-weight: 300;color:#ccc;}
	.allowed td,.allowed th{font-size:13px;color:<?php echo $THEMECOLORCODE; ?>;}
</style>
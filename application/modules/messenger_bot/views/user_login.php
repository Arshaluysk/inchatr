<div style="margin:20px;padding:20px">
<?php
if(isset($message))
{
	if($error==1) $class="danger";
	else $class="success";
	echo '<div class="text-center alert alert-'.$class.'">';
		echo '<b>'.$message.'</b>';
	echo '</div>';
}	

echo '<br><br><center><a href="'.base_url("messenger_bot/facebook_config").'"><i class="fa fa-arrow-circle-left"></i> '.$this->lang->line("go back").'</a></center>';
?>
</div>
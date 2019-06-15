<link rel="stylesheet" href="<?php echo base_url('plugins/jorgchartmaster/css/jquery.jOrgChart.css');?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/jorgchartmaster/css/custom.css');?>"/>
<link href="<?php echo base_url('plugins/jorgchartmaster/css/prettify.css');?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url('plugins/jorgchartmaster/js/prettify.js');?>"></script>
<script src="<?php echo base_url('plugins/jorgchartmaster/js/jquery.jOrgChart.js');?>"></script>

<script>
$j(document).ready(function() {
    $("#org").jOrgChart({
        chartElement : '#chart',
        dragAndDrop  : false
    });
});
</script>

<div onload="prettyPrint();"> 
<ul id="org" style="display:none">
  <?php  echo $get_started_tree; ?>
</ul>

<?php 
  $i=1;
  foreach ($keyword_bot_tree as $key => $value) 
  {
    echo '<ul id="org'.$i.'" style="display:none">'.$value.'
    </ul>';
    echo '<script>
      $j(document).ready(function() {
          $("#org'.$i.'").jOrgChart({
              chartElement : "#chart",
              dragAndDrop  : false
          });
      });
      </script>';
    $i++;
  }
?>

<ul id="org0" style="display:none">
  <?php  echo $no_match_tree; ?>
</ul>


<center>
    <div class="table-responsive">
        <div id="chart" class="orgChart"></div>
    </div>
</center>

</div>


<script>
$j(document).ready(function() {
    $("#org0").jOrgChart({
        chartElement : '#chart',
        dragAndDrop  : false
    });
});
</script>



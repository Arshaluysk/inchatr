<?php
echo "<b>The Topics that admin didn't reply yet</b>";

echo "<table width='100%'>";
	echo "<tr>";
		echo "<th>Title</th>";
		echo "<th>Posted By</th>";
		echo "<th>Time</th>";
		echo "<th>Total Reply</th>";
		echo "<th>Total View</th>";	
	echo "</tr>";
	
	foreach($topic_info as $info){
		
		$topic_url=base_url()."forum/topic_description/{$info->catagory_id}/{$info->id}";
		
		echo "<tr>";
			echo "<td><a href='{$topic_url}'>{$info->title}</a></td>";
			echo "<td>{$info->posted_by}</td>";
			echo "<td>{$info->date_time}</td>";
			echo "<td>{$info->total_reply}</td>";
			echo "<td>{$info->total_view}</td>";	
		echo "</tr>";
	}
	


echo "</table>";
echo $pages;

?>

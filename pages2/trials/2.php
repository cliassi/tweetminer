<?php
require_once('system/dbscan.php');

$epsilon = isset($_POST['e'])?$_POST['e']:3;
$minpoints = isset($_POST['m'])?$_POST['m']:1;

print "<form method='post'>Distance <input name='e' value='$epsilon' /> Minpoints <input name='m' value='$minpoints' /> <input type='submit' /></form>";

$point_ids = array('A','B','C','D','E','F','G','H','I','J','K');
$distance_matrix = array();
for($i=1;$i<=80;$i++){
	for($j=$i;$j<=80;$j++){
		$distance_matrix[$i][$j] = $j;
	}	
}
var_dump($distance_matrix);
exit;
//array_push($point_ids, chr(64+$i));
$distance_matrix[chr(65+$i-1)] = array();
print_r($point_ids);
$DBSCAN = new DBSCAN($distance_matrix, $point_ids);
// Perform DBSCAN clustering
$clusters = $DBSCAN->dbscan($epsilon, $minpoints);
//Output results
echo "<br /><br />Clusters (using epsilon = $epsilon  and minpoints = $minpoints): <br /><br />";
foreach ($clusters as $index => $cluster) 
{
	if (sizeof($cluster) > 0)
	{
		echo 'Cluster number '.($index+1).':<br />';
		echo '<ul>';
		foreach ($cluster as $member_point_id)
		{
			echo '<li>'.$member_point_id.'</li>';
		}
		echo '</ul>';
	}
}
var_dump($distance_matrix);
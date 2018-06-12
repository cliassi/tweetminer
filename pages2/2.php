<?php
require_once('system/dbscan.php');

$epsilon = isset($_REQUEST['e'])?$_REQUEST['e']:3; 
$minpoints = isset($_REQUEST['m'])?$_REQUEST['m']:3;

print "<form method='get'>Distance <input name='e' value='$epsilon' /> Minpoints <input name='m' value='$minpoints' /> <input type='submit' /></form>";
$tweets = select("SELECT * FROM `job_tweet` jt WHERE jt_job=6");

$point_ids = array();
while ($t = mysqli_fetch_object($tweets)) {
	$xy = sqrt(($t->vm * $t->vm) + ($t->am * $t->am));	
	$v = round($xy*10);
	array_push($point_ids, intval($v));
}
$distance_matrix = array();
for($i=1;$i<=128;$i++){
	for($j=$i+1;$j<=128;$j++){
		$distance_matrix[$i][$j] = $j;
		/*if(isset($distance_matrix[$i])){
			$distance_matrix[$i][$j]=$j;
		} else{
			$distance_matrix[chr(65+$i-1)] = array(chr(65+$j)=>$j);
		}*/
	}	
}
$distance_matrix[$i-1] = array();
//var_dump($point_ids); 
//print count($point_ids);

//var_dump($distance_matrix);
//exit;
$DBSCAN = new DBSCAN($distance_matrix, $point_ids);
// Perform DBSCAN clustering
$clusters = $DBSCAN->dbscan($epsilon, $minpoints);
var_dump($clusters);
exit;
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
//var_dump($distance_matrix);
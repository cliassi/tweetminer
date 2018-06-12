<?php
require_once('system/dbscan.php');

$epsilon = isset($_POST['e'])?$_POST['e']:3;
$minpoints = isset($_POST['m'])?$_POST['m']:1;

print "<form method='post'>Distance <input name='e' value='$epsilon' /> Minpoints <input name='m' value='$minpoints' /> <input type='submit' /></form>";

$tweets = select("SELECT * FROM `job_tweet` jt WHERE jt_job=$id LIMIT 50");

$point_ids = array();
while($t = mysqli_fetch_object($tweets)){
	array_push($point_ids, "$t->vm,$t->am");
}
var_dump($point_ids);
$distance_matrix = array();

$point_ids = array();
$distance_matrix = array();
for($i=1;$i<=100;$i++){
	array_push($point_ids, rand(1,100));
	for($j=$i;$j<=100;$j++){
		if(isset($distance_matrix[$i-1])){
			$distance_matrix[$i-1][$j]=$j;
		} else{
			$distance_matrix[$i-1] = array($j=>$j);
		}
		
	}	
}
array_push($point_ids, $i-1);
$distance_matrix[$i-1] = array();
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
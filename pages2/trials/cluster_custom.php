<?php
$id = isset($_GET['id'])?$_GET['id']:5;
$epsilon = isset($_GET['e'])?$_GET['e']:3;
$minpoints = isset($_GET['m'])?$_GET['m']:1;

print "<form>Distance <input name='e' value='$epsilon' /> Minpoints <input name='m' value='$minpoints' /> <input type='submit' /></form>";
$tweets = select("SELECT * FROM `job_tweet` jt WHERE jt_job=$id");  
$dataset = array();
while($t = mysqli_fetch_object($tweets)){
	$point = new Point($t->id, $t->vm, $t->am);
	array_push($dataset, $point);
}
$clusters = array();
$cc = 0;
dbscan($dataset, $epsilon, $minpoints, $clusters, $cc);
//print "Clusters";
//var_dump($clusters);
//print "Dataset";
//var_dump($dataset);
function dbscan($dataset = array(), $eps = .5, $minpoints = 3, &$clusters, &$cc) {
	$i = 0;
	$clusters[$cc] = array();
	foreach ($dataset as $point) {
		$i++;
		if($point->visited){
			continue;
		}
		$point->visited = true;
		$dataset[$i-1] = $point;
		$neighborPts = regionQuery($dataset, $point, $eps);
		if(count($neighborPts)<$minpoints){
			$point->noise = true;
			$dataset[$i-1] = $point;
		} else{
			$clusters[$cc] = array();
			expandCluster($dataset, $point, $neighborPts, $eps, $minpoints, $clusters, $cc);
			$cc++;
			//var_dump($clusters);
			d();
		}
	}
}

function point_exists_in_dataset($dataset, $point){
	foreach ($dataset as $p) {
		if($p->name==$point->name){
			return true;
		}
	}
	return false;
}

function regionQuery($dataset, $point, $eps){
	$neighbor_points = array();		
		foreach ($dataset as $point2)
		{
			if ($point->name != $point2->name)
			{
				// Because we are using an upper diagonal representation of distances between points
				//$distanceX = $point->x;	
				//$distanceY = $point->y;
				if (point_exists_in_dataset($dataset, $point2))
				{	
					$distanceX = $point->x;
					$distanceY = $point->y;
				} else {					
					$distanceX = $point2->x;
					$distanceY = $point2->y;
				}
				if ($distanceX < $eps || $distanceY < $eps)
				{
					$neighbor_points[] = $point2;
				}
			}
		}
		return $neighbor_points;
}
function d(){
	die("you are here!");
}
function expandCluster($dataset, $point, $neighborPts, $eps, $minpoints, &$clusters, &$cc) {
	$point->neighbored = true;
	array_push($clusters[$cc], $point);
	print "Cluster $cc";
	var_dump($clusters);
	foreach ($neighborPts as $p) {
		if(!$p->visited) {
			$p->visited = true;
			$neighborPts_ = regionQuery($dataset, $p, $eps);
			if(count($neighborPts_)>=$minpoints){
				array_merge($neighborPts, $neighborPts_);
			}
		}
		if(!$p->neighbored){
			$p->neighbored = true;
			array_push($clusters[$cc], $p);
		}
	}
}
class Point{
	public $name;
	public $x;
	public $y;
	public $visited = false;
	public $noise = false;
	public $neighbored = false;
	public function __construct($name, $x, $y)
	{
		$this->name = $name;
		$this->x = $x;
		$this->y = $y;
	}
}

exit;
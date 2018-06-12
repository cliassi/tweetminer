<?php
$epsilon = isset($_REQUEST['e'])?$_REQUEST['e']:3; //Distance
$minpoints = isset($_REQUEST['m'])?$_REQUEST['m']:3;

$content = "<div class='col-md-12'><form method='get'>
	<input type='hidden' name='id' value='$id' />
	<input type='hidden' name='t' value='$t' />
	<input type='hidden' name='i' value='$int' />Distance <input name='e' value='$epsilon' /> Minpoints <input name='m' value='$minpoints' /> <input type='submit' /></form></div>";
$content .= "<br clear='all'>";
//1. $tweets = select("SELECT * FROM job_tweet WHERE jt_job=$id AND jt_anewcount>=$epsilon ORDER BY id");
$tweets = select("SELECT * FROM job_tweet WHERE jt_job=$id $filter ORDER BY id");
//$tweets = select("select * from job_tweet where id in(7093, 7109, 7120, 7154)");

$words = array();
$singletones = array();
$clusters = array();
$in_cluster = array();
$index = 0;
while($tweet = mysqli_fetch_object($tweets)){
	$anew = strtolower($tweet->jt_tweetwostopwords);
	$ws = array_unique(explode(" ", $anew));
	$t = new Tweet($tweet->jt_tweet, $ws, $tweet->vm, $tweet->vmsd, $tweet->am, $tweet->amsd);
	find2($t);
}

/*foreach ($singletones as $tweet) {
	$index = find2(array_shift($singletones), $tweet, $index);
}*/

function find2($otweet){
	global $epsilon;
	global $singletones;
	global $clusters;
	global $index;
	$found = false;
	if(!$found){
		foreach ($clusters as $key => $cluster) {
			$all_matchecd = true;
			foreach ($cluster as $tweet) {
				//$intersect = array_intersect(array_unique($otweet->Words), array_unique($tweet->Words));
				$intersect = array_intersect($otweet->Words, $tweet->Words);
				if(count($intersect)<$epsilon){
					$all_matchecd = false;
					break;
				}
			}
			if($all_matchecd){
				$found = true;
				array_push($clusters[$key], $otweet);
				//print "Found in cluster"; print_r($otweet);
				return $found;
			}
		}
	}
	if(!$found){
		foreach ($singletones as $tweet) {
			$intersect = array_intersect($otweet->Words, $tweet->Words);
			if(count($intersect)>=$epsilon){
				$found = true;
				if(!isset($clusters[$index])){
					$clusters[$index] = array();
				}
				array_push($clusters[$index], $otweet);
				array_push($clusters[$index], $tweet);
				if (($key = array_search($tweet, $singletones)) !== false) {
					unset($singletones[$key]);
				}
				$index++;
			}
		}
	}
	if(!$found){
		array_push($singletones, $otweet);
	}
}

//var_dump($clusters);

$count = 0;
//foreach ($clusters as $cluster) {
//	$count += count($cluster);
	//var_dump($cluster);
//}
//$count += count($singletones);

$content .= "<svg height='550' width='900'>
	<line x1='550' y1='0' x2='550' y2='450' style='stroke:rgb(55,100,200);stroke-width:1' />";
$con = "";

$i = 1;
$x = 10;
$y = 25;
$ccount = 0;
foreach ($clusters as $cluster) {
	if(count($cluster)>=$minpoints){
		$con .= "Cluser $i: ".count($cluster)."<br />";
		$i++;
		//$x = rand(30,450);
		//$y = rand(30,350);
		//$w = 50+(count($cluster)*5);
		$w = 80;
		$keyword = array();
		$content .= "<g>";
		$content .= "<rect x='$x' y='$y' width='$w' height='60' style='fill:rgba(166,201,226, .6);stroke-width:0;stroke:rgb(0,0,0)' />";
		foreach ($cluster as $tweet) {
			$content .= "<circle cx='".rand($x+5, $x+$w-5)."' cy='".rand($y+5,$y+60-5)."' r='5' fill='rgba(150,200,180,.8)' title='$tweet->ID ".implode(" ", $tweet->Words)."' />";
			if(count($keyword)==0){
				$keyword = $tweet->Words;
			}
			$keyword = array_unique(array_intersect($keyword, $tweet->Words));
		}
		$content .= "<text  x='$x' y='".($y-5)."' style='font:8px'>".implode(", ", $keyword)."</text>";
		$content .= "<g>";
		$ccount++;
		if($ccount>3){
			$ccount = 0;
			$x = -80;
			$y = 125;
		}
		$x = $x + 90;
		$y = $y + 15;
	} else{
		foreach ($cluster as $tweet) {
			array_push($singletones, $tweet);
		}
	}
}
foreach ($singletones as $tweet) {
	$color = ceil($tweet->vm * 25);
 	$color2 = "0";
 	if($tweet->vm>5){
 		$color = "$color2,$color2,$color";
 	} else{
 		$color = "$color,$color2,$color2";
 	}
	$x = 550+50*($tweet->vm-1);
		$y = 450 -(50*($tweet->am-1));
	$content .= "<circle cx='$x' cy='$y' r='".(3*$tweet->vmsd)."' fill='rgba($color,.8)' title='$tweet->vm $tweet->vmsd $tweet->am $tweet->amsd $color' />";
}

$content .= "</svg>";

$content .= "<br clear='all' />";

$count = 0;
$i = 1;
foreach ($clusters as $cluster) {
	$count = 0;
	if(count($cluster)>=$minpoints){
		$count += count($cluster);
		$content .= "Cluser $i : ";
		$keyword = array();
		$ids = "";
		foreach($cluster as $tweet){
			if(count($keyword)==0){
				$keyword = $tweet->Words;
			}
			$ids .= " $tweet->ID";
			$keyword = array_unique(array_intersect($keyword, $tweet->Words));
		}
		$content .= implode(", ", $keyword)." ($count tweets) <small>$ids</small><br />";
		$i++;
	}
}
$count += count($singletones);
$content .= "<br clear='all' />";
//var_dump($singletones);
//var_dump($clusters);

print $content;
?>
<script type="text/javascript">
$('[title]').qtip({hide: {
        event: 'unfocus'
    }});
</script>
<?php
//======================================================================
//die();

/*
$cur_item = 1;
foreach ($clusters[0] as $tweet_id => $tweet) {
	$index = find2($clusters, $tweet_id, $tweet, $index);
	$cur_item++;
}

function find2($oclusters, $otweet_id, $otweet, $index){
	global $epsilon;
	global $cur_item;
	$found = false;
	foreach (array_slice($oclusters[0], $cur_item) as $tweet_id => $tweet) {
		if($tweet!==$otweet){
			$intersect = array_intersect(array_unique($otweet), array_unique($tweet));
			if(count($intersect)>=$epsilon){
				if(!$found){
					$index++;
					$found = true;
					addToCluster($otweet_id, $otweet, $index);
				}
				addToCluster($tweet_id, $tweet, $index);
			}
		}
	}
	return $index;
}
function addToCluster($tweet_id, $tweet, $index){
	global $clusters;
	if(!isset($clusters[$index])){
		$clusters[$index] = array();
	}
	$clusters[$index][$tweet_id] =  $tweet;
}
//print "All Tweets<hr />";
foreach ((array_slice($clusters,1)) as $key => $value) {
	if(count($value)>=$minpoints){
		var_dump($value);
	}
}

/**
*
*/

/*
Distance= no of anew words matched + keyword
Min point= number of tweets
*/

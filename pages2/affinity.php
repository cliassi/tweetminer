<?php
$content = '<script src="js/springy.js"></script>
<script src="js/springyui.js"></script>';

$tweets = select("SELECT jt.*, CreateBy FROM job_tweet jt, tweet t WHERE jt_tweet=t.id AND jt_job=$id ORDER BY id");
$nodes = array();
$graphnodes = ""; 
while($tweet = mysqli_fetch_object($tweets)){		
	$all = trim($tweet->jt_hashtag." ".$tweet->jt_mention);
	if($all!=""){
		$hashtags = explode(" ", $all );
		array_push($nodes, array($tweet->CreateBy, $hashtags));
		$graphnodes .= "'$tweet->CreateBy', ";
		foreach ($hashtags as $tag) {
			$graphnodes .= "'$tag', ";
		}
	}		
}
$graphnodes = substr(trim($graphnodes),0,-1);

$content .= "<canvas id='springydemo' width='1000' height='1000' />


<script>
var graph = new Springy.Graph();
graph.addNodes($graphnodes);

graph.addEdges(";

$edges = '';
foreach ($nodes as $node) {
	$color = '#'.dechex(rand(50,200)).dechex(rand(50,200)).dechex(rand(50,200));
	$user = "'$node[0]'";
	foreach ($node[1] as $tag) {
		$edges .= "[$user, '$tag', {color: '$color'}], ";
	}
}
$edges = substr(trim($edges),0,-1);
$content .= $edges;

$content .= ");

jQuery(function(){
  var springy = jQuery('#springydemo').springy({
    graph: graph
  });
});

</script>";

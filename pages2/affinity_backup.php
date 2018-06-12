<script src="js/springy.js"></script>
<script src="js/springyui.js"></script>
<?php
	$tweets = select("SELECT jt.*, CreateBy FROM job_tweet jt, tweet t WHERE jt_tweet=t.id AND jt_job=$id ORDER BY id");
	$nodes = array();
	$all = array();
	$users = array();
	while ($tweet = mysqli_fetch_object($tweets)) {
		$otags = explode(" ", $tweet->jt_hashtag);
		$utags = explode(" ", $tweet->jt_mention);
		$tags = array();
		foreach ($otags as $tag) {
			if(nn($tag)) array_push($tags, substr($tag, strpos($tag, "#")));
		}
		foreach ($utags as $tag) {
			if(nn($tag)) array_push($tags, substr($tag, strpos($tag, "@")));
		}
		array_push($nodes, $tags);
		$all = array_merge($all, $tags);
		array_push($users, $tweet->CreateBy);
	}
	$all = array_unique($all);
	$users = array_unique($users);
	$nodes = "";
	foreach ($users as $user) {
		$user = trim($user);
		$nodes .= $nodes?", '$user'":"'$user'";
	}

  //var_dump($users);
?>
<script>
var graph = new Springy.Graph();
graph.addNodes(<?php print $nodes; ?>);

graph.addEdges(
  <?php
  	$count = 0;
  	foreach ($users as $user) {
  		//print "['$user', '".array_pop($users)."']";
  		$u1 = array_pop($users);
  		$u2 = array_pop($users);
  		if(nn($u1)&&nn($u2))
  		print ($count++>0?",":"")."['$u1', '$u2', {color: '#".dechex(rand(50,200)).dechex(rand(50,200)).dechex(rand(50,200))."'}]";
  		//print "['".array_pop($users)."', '".array_pop($users)."', {color: '#".dechex(rand(50,200)).dechex(rand(50,200)).dechex(rand(50,200))."'}],";
  		//print "['".array_pop($users)."', '".array_pop($users)."', {color: '#".dechex(rand(50,200)).dechex(rand(50,200)).dechex(rand(50,200))."'}]";
  		//break;
  	}
  ?>
  );

jQuery(function(){
  var springy = jQuery('#springydemo').springy({
    graph: graph
  });
});
</script>

<canvas id="springydemo" width="1000" height="1000" />
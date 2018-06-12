<?php
	$content = '<svg height="430" width="850">';
	$content .= "<text x='400' y='8' style='color:444; font-size:10px; text-shadow: 0 0 5px #fff'>Active</text>";
	$content .= "<text x='765' y='208' style='color:444; font-size:10px;'>Pleasent</text>";
	$content .= "<text x='400' y='428' style='color:444; font-size:10px;'>Subdued</text>";
	$content .= "<text x='10' y='208' style='color:444; font-size:10px;'>Unpleasent</text>";
	for($i=0;$i<9;$i++){
		$content .= "<line x1='10' y1='".(10+($i*Y))."' x2='".(MX+10)."' y2='".(10+($i*Y))."' style='stroke:rgb(55,100,200);stroke-width:".($i==4?"1":".25")."' />";
		$content .= "<line x1='".(10+($i*X))."' y1='10' x2='".(10+($i*X))."' y2='".(10+MY)."' style='stroke:rgb(55,100,200);stroke-width:".($i==4?"1":".25")."' />";
		$content .= "<text x='0' y='".(10+($i*Y))."' style='color:444; font-size:9px; color:#888'>".(9-$i)."</text>";
		$content .= "<text x='".(8+($i*X))."' y='418' style='color:444; font-size:9px; color:#888'>".($i+1)."</text>";
	}
	$qurdrants = array(1=>0,2=>0,3=>0,4=>0);
	$qurdrants_ids = array(1=>array(),2=>array(),3=>array(),4=>array());


	$tweets = select("SELECT * FROM job_tweet WHERE jt_job=$id AND vm>0 $filter");
	 while($tweet = mysqli_fetch_object($tweets)){
	 	$color = ceil($tweet->vm * 25);
	 	$color2 = "0";
	 	if($tweet->vm>5){
	 		$color = "$color2,$color2,$color";
	 		if($tweet->am>=5){
	 			$qurdrants[1]++;
	 			array_push($qurdrants_ids[1], $tweet->jt_tweet);
	 		} else{
	 			$qurdrants[4]++;
	 			array_push($qurdrants_ids[4], $tweet->jt_tweet);
	 		}
	 	} else{
	 		$color = "$color,$color2,$color2";
	 		if($tweet->am>=5){
	 			$qurdrants[2]++;
	 			array_push($qurdrants_ids[2], $tweet->jt_tweet);
	 		} else{
	 			$qurdrants[3]++;
	 			array_push($qurdrants_ids[3], $tweet->jt_tweet);
	 		}
	 	}
	 	$x = X*($tweet->vm-1);
	 	$y = MY-(Y*($tweet->am-1));
	 	$content .= "<circle cx='".($x+10)."' cy='".($y+10)."' r='".(3*$tweet->vmsd)."' fill='rgba($color,.8)' title='$tweet->jt_tweet' />";
	 	//$content .= "<circle cx='".(100*4)."' cy='".(60*4)."' r='".(3*$t[1])."' fill='rgba($color)' title='$tweet->m' />";
	 }
	$content .= "</svg>";
	$content .= "<h5>Pleasent - Active : {$qurdrants[1]} <small>(".implode(", ", $qurdrants_ids[1]).")</small></h5>";
	$content .= "<h5>Unpleasent - Active : {$qurdrants[2]} <small>(".implode(", ", $qurdrants_ids[2]).")</small></h5>";
	$content .= "<h5>Unpleasent - Subdued : {$qurdrants[3]} <small>(".implode(", ", $qurdrants_ids[3]).")</small></h5>";
	$content .= "<h5>Pleasent - Subdued : {$qurdrants[4]} <small>(".implode(", ", $qurdrants_ids[4]).")</small></h5>";
	$content .= "
	<script type='text/javascript'>
	$('[title]').qtip({hide: {
	      event: 'unfocus'
	}});
	</script>";

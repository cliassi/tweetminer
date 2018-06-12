<?php
$blocks = array();
$block_tweets = array();
for($i=1;$i<9;$i++){
	for($j=1;$j<9;$j++){
		$blocks[$i][$j] = 0;
		$block_tweets[$i][$j] = array();
	}
}
$count = 1;
$max = 0;
//$content .= $tweets->num_rows;
 while($tweet = mysqli_fetch_object($tweets)){
 	$vm = floor($tweet->vm);
 	$am = 8-floor($tweet->am);
 	$am = $am==9?8:$am;
 	$vm = $vm==9?8:$vm;
 	$blocks[$vm][$am]++;
 	array_push($block_tweets[$vm][$am], $tweet->jt_tweet);
 	if($blocks[$vm][$am]>$max){
 		$max = $blocks[$vm][$am];
 	}
 	$count++;
 }
//var_dump($blocks);
 $i = 1;
$content = '<svg height="430" width="850">';
$content .= "<text x='400' y='8' style='color:444; font-size:10px; text-shadow: 0 0 5px #fff'>Active</text>";
$content .= "<text x='765' y='208' style='color:444; font-size:10px;'>Pleasent</text>";
$content .= "<text x='400' y='428' style='color:444; font-size:10px;'>Subdued</text>";
$content .= "<text x='10' y='208' style='color:444; font-size:10px;'>Unpleasent</text>";
for($i=0;$i<9;$i++){
	$content .= "<line x1='10' y1='".(10+($i*Y))."' x2='".(MX+10)."' y2='".(10+($i*Y))."' style='stroke:rgb(55,100,200);stroke-width:".($i==4?"1":".25")."' />";
	$content .= "<line x1='".(10+($i*X))."' y1='10' x2='".(10+($i*X))."' y2='".(MY+10)."' style='stroke:rgb(55,100,200);stroke-width:".($i==4?"1":".25")."' />";
	$content .= "<text x='0' y='".(10+($i*Y))."' style='color:444; font-size:9px; color:#888'>".(9-$i)."</text>";
	$content .= "<text x='".(8+($i*X))."' y='418' style='color:444; font-size:9px; color:#888'>".($i+1)."</text>";
}
	for($i=1;$i<9;$i++){
	for($j=1;$j<9;$j++){
		$x = $i * 100;
		$y = $j * 50;
		if($blocks[$i][$j]==0){
			$color = "255,255,255,0";
		} else{
			if($blocks[$i][$j]<17){
				$color = "0,0,".($blocks[$i][$j]*15).",1";
			} else{
				$color = round($blocks[$i][$j]*($max/255)).",0,0,1";
			}
		}
		$content .= "<g>";
		$content .= "<rect x='".($x-90)."' y='".($y+10)."' width='100' height='50' style='fill:rgba($color);stroke-width:0;stroke:rgb(0,0,0)'' />";
		$content .= "<text x='".($x-50)."' y='".($y+40)."' style='fill:rgb(255,255,255)'>{$blocks[$i][$j]}</text>";
		$content .= "</g>";
	}
}
$content .= "</svg>
<div>
<h4>Summary</h4>
<p>";
	for($i=1;$i<9;$i++){
		for($j=1;$j<9;$j++){
			if(count($block_tweets[$i][$j])>0){
				$content .= ($i).", ".(8-$j)." : <small>". implode(", ", $block_tweets[$i][$j])."</small><br />";
			}
		}
	}
$content .= "</p></div>";
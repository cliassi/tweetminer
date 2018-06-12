<?php

  $w1q = "";
  $w2q = "";
  $w3q = "";
  $w4q = "";

  while($tweet = mysqli_fetch_object($tweets)){
    $vm = floor($tweet->vm);
    $am = 9-floor($tweet->am);
    if($vm>4 && $am>4){
      $w4q .= $tweet->jt_anew." ";
    } elseif($vm<=4 && $am>4){
      $w3q .= $tweet->jt_anew." ";
    } elseif($vm<=4 && $am<=4){
      $w2q .= $tweet->jt_anew." ";
    } elseif($vm>4 && $am<=4){
      $w1q .= $tweet->jt_anew." ";
    }
  }

  $w1q = explode(" ", strtolower(trim($w1q)));
  $w2q = explode(" ", strtolower(trim($w2q)));
  $w3q = explode(" ", strtolower(trim($w3q)));
  $w4q = explode(" ", strtolower(trim($w4q))); 

  $q1 = array();
  foreach($w1q as $w1){
    if(array_key_exists($w1, $q1)){
      $q1[$w1]++;
    } else{
      $q1[$w1] = 1;
    }
  } 
  $q2 = array();
  foreach($w2q as $w2){
    if(array_key_exists($w2, $q2)){
      $q2[$w2]++;
    } else{
      $q2[$w2] = 1;
    }
  } 
  $q3 = array();
  foreach($w3q as $w3){
    if(array_key_exists($w3, $q3)){
      $q3[$w3]++;
    } else{
      $q3[$w3] = 1;
    }
  } 
  $q4 = array();
  foreach($w4q as $w4){
    if(array_key_exists($w4, $q4)){
      $q4[$w4]++;
    } else{
      $q4[$w4] = 1;
    }
  } 

$content = '<div id="wrapper">
  	<div id="second" class="tccontainer"><span class="title title-left">Unpleasent-Active</span></div>
    <div id="first" class="tccontainer"><span class="title title-right">Pleasent-Active</span></div>
  	<div id="third" class="tccontainer"><span class="title title-left">Unpleasent-Subdued</span></div>
  	<div id="fourth" class="tccontainer"><span class="title title-right">Pleasent-Subdued</span></div>
  </div>';

$content .= "
<script type='text/javascript'>
var words1 = [ ";
foreach ($q1 as $k1 => $w1) { $content .= '{text: "'.$k1.'", weight: '.($w1*4).', html: {title: "'.$w1.'"}},';  }
$content .= " ];";
$content .= "var words2 = [ ";
foreach ($q2 as $k2 => $w2) { $content .= '{text: "'.$k2.'", weight: '.($w2*4).', html: {title: "'.$w2.'"}},';  }
$content .= " ];";
$content .= "var words3 = [ ";
foreach ($q3 as $k3 => $w3) { $content .= '{text: "'.$k3.'", weight: '.($w3*4).', html: {title: "'.$w3.'"}},';  }
$content .= " ];";
$content .= "var words4 = [ ";
foreach ($q4 as $k4 => $w4) { $content .= '{text: "'.$k4.'", weight: '.($w4*4).', html: {title: "'.$w4.'"}},';  }
$content .= " ];";

$content .= '
  $(function(){
    $("#first").jQCloud(words1);
    $("#second").jQCloud(words2);
    $("#third").jQCloud(words3);
    $("#fourth").jQCloud(words4);
  });
  </script>';

<?php
 

//given 25, 5
$sq = $_GET['sq'];
print is_square($sq)? "Square": "Not Square";

function is_square($sq){
	for($i=2; $i<$sq/2 ; $i++){
		if($i*$i==$sq) return true;
	}
	return false;
}
<?php
$t = isset($_GET['t'])?$_GET['t']:1;
for($i=1;$i<5;$i++){
	print "<a href=?id=$id&t=$i>Trail $i</a> ";
}
include("pages/trials/$t.php");
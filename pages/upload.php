<?php
	$tweet_upload = R::dispense("tweet_uploads");
	$func = isset($_GET["f"])?($_GET["f"]==""?"view":$_GET["f"]):"view";
	if(isset($_GET["id"])) {
		$id = $_GET["id"];
		$tweet_upload = R::load("tweet_uploads", $id);
	}
	switch ($func){
		case "view": {
	     require("pages/view/upload.php");
		} break;
    case "process": {
      $job = R::load("jobs", $id);
      $job->j_status = 'Suspended';
      R::store($job);
      redir("?f=view");
    } break;
    case "fix":{

    }
	case "add": {
		require_once("pages/form/upload.php");
	} break;
	case "addcsv": {
		require_once("pages/form/uploadcsv.php");
	} break;
	case 'createjob':{
		if(isset($id)){
			insert("jobs", "j_name,j_type,j_criteria,j_retweet,j_startdate,j_enddate,j_upload",
				"'$tweet_upload->tu_name','Simple','Keywords',0,'$tweet_upload->tu_start_time','$tweet_upload->tu_end_time',$id");
		}
		redir("?f=view&t=".now());
	} break;
  }
	?>

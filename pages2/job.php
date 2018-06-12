<?php 
	$job = R::dispense("jobs");
	$width = "700px";
	$width1 = "100px";
	$func = isset($_GET["f"])?($_GET["f"]==""?"view":$_GET["f"]):"view";
	if(isset($_GET["id"])) { $id = $_GET["id"]; }
	switch ($func){
		case "view": {
	     require("pages/view/job.php");
		} break;
    case "suspend": {    
      $job = R::load("jobs", $id);
      $job->j_status = 'Suspended';
      R::store($job);
      redir("?f=view");
    } break;
    case "resume": {    
      $job = R::load("jobs", $id);
      $job->j_status = 'Pending';
      R::store($job);
      redir("?f=view");
    } break;
    case "reset": {    
      $job = R::load("jobs", $id);
      $job->j_status = 'Pending';
	  $job->j_lastid = 0;
	  $job->j_total_tweets = 0;
	  $job->j_processed_tweets = 0; 
	  $job->j_matched_tweets = 0; 
	  $job->j_updated = 1;
	  del("job_tweet", "jt_job=$id");
      R::store($job);
      redir("?f=view");
    } break;
    case "remove": {
      if(isset($_GET["conf"])){     
        $job = R::load("jobs", $id);
        R::trash($job);
  			redir("?".uri("f", "view", array("id")));
  		} else{
  		?>
  		<script type="text/javascript">
  			if(confirm("Are you sure you want to remove this ".APPNAME."?")){
  				location.href = "?<?php echo uri();?>&conf";
  			} else{
  				location.href = "?f=view";	
  			}
  		</script>
  		<?php
  		}
		} break;
		case "edit": {
		   $job = R::load("jobs", $id);
		 $criteria = R::findOne("job_criteria", "jc_job=?", array($id));
		} 
		case "add": {
			if(isset($_POST["save"])){
				require_once("pages/pss/job.php");
				redir("?f=view");
			}
			require_once("pages/form/job.php");
		} break;
	}
?>
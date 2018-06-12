<link rel="stylesheet" href="css/jquery.qtip.min.css">
<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
<?php
$id = is('id', false);
$t = is('t', 'sentiment');
if($id){
	define("BLOCKS", 8);
	define('X', 100);
	define('Y', 50);
	define('MX', X * BLOCKS);
	define('MY', Y * BLOCKS);
	$tabs = array(
				array('sentiment', 'Sentiment Map'),
				array('heatmaps', 'Heat Map'),
				array('tagcloud', 'Tag Cloud'),
				array('affinity', 'Affinity'),
				array('timeline', 'Timeline'),
				array('geo', 'Geo Map'),
				array('clustering', 'Clustering'),
				array('clustering2', 'Clustering 2'),
				array('tweets', 'Tweets')
			);
      print "<ul class='nav nav-tabs' role='tablist'>";
      foreach ($tabs as $tab) {
  		print "<li role='presentation' class='";
      	if($tab[0]==$t) print "active";
      	print "'><a href='?id=$id&t={$tab[0]}' aria-controls='{$tab[0]}'>{$tab[1]}</a></li>";
      }

	
	$filename_extra = "";

	//Initiate filter
	$filter = "";

	//Load Contents is exists else Load tweets and process
	$content = "";
	if($t=='clustering' || $t='clustering2'){
		$epsilon = isset($_REQUEST['e'])?$_REQUEST['e']:3; //Distance
		$minpoints = isset($_REQUEST['m'])?$_REQUEST['m']:3;
		$filename_extra = ".$epsilon.$minpoints";
	}

	//Set Filename
	$filename = "$t.$id.$filename_extra.rpt";

	if(isset($_REQUEST['df'])){
		if(isset($_REQUEST['dt'])){
			$filter = "AND (jt_time BETWEEN '{$_REQUEST['df']}' AND '{$_REQUEST['dt']}')";
			$filename = "$t.$id.{$_REQUEST['df']}.{$_REQUEST['dt']}$filename_extra.rpt";
		} else{
			$filter = "AND jt_time >= '{$_REQUEST['df']}'";
			$filename = "$t.$id.{$_REQUEST['df']}.$filename_extra.rpt";
		}
	} elseif(isset($_REQUEST['dt'])){
		$filter = "AND jt_time <= '{$_REQUEST['dt']}'";
		$filename = "$t.$id..{$_REQUEST['dt']}.rpt";
	}
	if(file_exists("reports/$filename")){
		$content = file_get_contents("reports/$filename");
	} else{
		//Load Tweets
		$tweets = select("SELECT * FROM `job_tweet` jt WHERE jt_job=$id AND vm>0 $filter");
		include("pages/$t.php");
		file_put_contents("reports/$filename", $content);
		print $content;
	}
	print "<div class='tab-content'>";
	foreach ($tabs as $tab) {
		print "<div role='tabpanel' class='tab-pane ";
		if($tab[0]==$t) print "active";
		print "' id='{$tab[0]}'>"; 
		if($tab[0]==$t) {
			print $content;
		}
		print "</div>";
	}
	print "</div>";
} else{
	print "<ul class='nav nav-tabs' role='tablist'>
	    <li role='presentation' class='active'><a href='#sentiment' aria-controls='sentiment' role='tab' data-toggle='tab'>Active ".APPNAME."</a></li>
	    <li role='presentation'><a href='#profile' aria-controls='profile' role='tab' data-toggle='tab'>".APPNAME." History</a></li>
	  </ul>";
	$jobs = select('*', 'jobs');

	print "<table class='table table-striped table-hover'>
	  <thead>
	    <tr>
	      <th>#</th>
	      <th>".APPNAME." Name</th>
	      <th>Retweet?</th>
	      <th>Total</th>
	      <th>Processed</th>
	      <th>Matched</th>
	      <th>Time</th>
	      <th>Status</th>
	      <th>Action</th>
	    </tr>
	  </thead>
	  <tbody>";

	$i = 1;
	while($job = mysqli_fetch_object($jobs)){
		print "<tr>
		      <td>$i</td>
		      <td><a href='?id=$job->id'>$job->j_name</a></td>
		      <td>".($job->j_retweet?'Read':'Skip')."</td>
		      <td>$job->j_total_tweets</td>
		      <td>$job->j_processed_tweets</td>
		      <td>$job->j_matched_tweets</td>
		      <td>$job->j_startdate - $job->j_enddate</td>
		      <td>$job->j_status</td>
		      <td><div class='btn-group'>
                    <a href='#' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                      Choose...
                      <span class='caret'></span>
                    </a>
                    <ul class='dropdown-menu'>
                      <li><a href='?f=edit&id=$job->id'>Edit</a></li>
                      <li><a href='?f=remove&id=$job->id'>Remove</a></li>
                      <li><a href='?f=reset&id=$job->id&".now()."'>Reset</a></li>
                      <li>".($job->j_status=='Suspended'?"<a href='?f=resume&id=$job->id'>Resume</a>":"<a href='?f=suspend&id=$job->id'>Suspend</a>")."</li>
                     </ul>
                  </div>
               </td>
		    </tr>";
		$i++;
	}
	print "</tbody>
		</table>";
}
?>

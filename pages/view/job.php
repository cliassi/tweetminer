<style type="text/css">
#slider label {
    position: absolute;
    /*width: 20px;*/
    margin-left: -10px;
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    white-space: nowrap;
}

/* below is not necessary, just for style */
#slider {
    margin: 2em auto;
}
</style>
<link rel="stylesheet" href="css/jquery.qtip.min.css">
<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
<div id="slider"></div>
<?php
$id = is('id', false);
$int = is('i', 1);
$t = is('t', 'sentiment');
$urlwithoutinterval = "id=$id&t=$t&".now();
$url = "id=$id&i=$int&t=$t&".now();

if($id){
	$datestamps = array();
	$jobs = select("MIN(jt_time) j_startdate, MAX(jt_time) j_enddate, TIMESTAMPDIFF(MINUTE, MIN(jt_time), MAX(jt_time)) minutes", "job_tweet", "jt_job=$id");
	$job = mysqli_fetch_object($jobs);
	$total_mintues = $job->minutes;
	$interval = floor($total_mintues / 6);
	$date = new DateTime($job->j_startdate);
	array_push($datestamps, $job->j_startdate);
	for($i=2; $i<6; $i++){
		$date->add(new DateInterval('PT'.($interval).'M'));
		array_push($datestamps, $date->format('Y-m-d H:i:s'));
	}
	array_push($datestamps, $job->j_enddate);

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
		print "<li role='presentation' class='".($tab[0]==$t?"active":"")."'><a href='?id=$id&i=$int&t={$tab[0]}&".now()."' aria-controls='{$tab[0]}'>{$tab[1]}</a></li>";
  }

	//Set Filename
	$filename = "$t.$id.$int.rpt";

	//Initiate filter
	$filter = "";

	//Load Contents is exists else Load tweets and process
	$content = "";

	if(isset($_REQUEST['df'])){
		if(isset($_REQUEST['dt'])){
			$filter = "AND (jt_time BETWEEN '{$_REQUEST['df']}' AND '{$_REQUEST['dt']}')";
			$filename = "sentiment.$id.{$_REQUEST['df']}.{$_REQUEST['dt']}.rpt";
		} else{
			$filter = "AND jt_time >= '{$_REQUEST['df']}'";
			$filename = "sentiment.$id.{$_REQUEST['df']}..rpt";
		}
	} elseif(isset($_REQUEST['dt'])){
		$filter = "AND jt_time <= '{$_REQUEST['dt']}'";
		$filename = "sentiment.$id..{$_REQUEST['dt']}.rpt";
	} else{
		$filter = "AND jt_time <= '".$datestamps[$int-1]."'";
	}
	if(file_exists("reports/$filename")){
		$content = file_get_contents("reports/$filename");
	} else{
		//Load Tweets
		$tweets = select("SELECT * FROM `job_tweet` jt WHERE jt_job=$id AND vm>0 $filter");
		include("pages/$t.php");
		file_put_contents("reports/$filename", $content);
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
?>

<script type='text/javascript'>
$('[title]').qtip({hide: {
        event: 'unfocus'
    }});
</script>
<?php
	print "<a href='?f=regenerate&t=$t&i=$int&id=$id&".now()."' class='btn btn-warning'>Regenerate</a>";
} else{
	print "<ul class='nav nav-tabs' role='tablist'>
	    <!--li role='presentation' class='active'><a href='#sentiment' aria-controls='sentiment' role='tab' data-toggle='tab'>Active ".APPNAME."</a></li-->
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
		      <td><a href='?id=$job->id&".now()."'>$job->j_name</a></td>
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
if($id):
$timestamps = "";
foreach($datestamps as $time) {
	$timestamps .= ($timestamps!=""?",":"")."'$time'";
}
?>


<script type="text/javascript">
	times = [<?php print $timestamps; ?> ];
	$("#slider").slider({
      min: 1,
      max: 6,
      range: "min",
      value: <?php print $int; ?>,
      change: function( event, ui ) {
      	location.href = "?<?php print $urlwithoutinterval; ?>&i=" + ui.value;
      	console.log(ui.value);
      }
  	}).each(function() {
  		// Get the options for this slider
	  var opt = $(this).data().uiSlider.options;

	  // Get the number of possible values
	  var vals = opt.max - opt.min;

	  // Space out values
	  for (var i = 0; i <= vals; i++) {

	    var el = $('<label>'+ times[i]+'</label>').css('left',(i/vals*100)+'%');

	    $( "#slider" ).append(el);

	  }
  	});
</script>
<?php endif; ?>

<br clear='all'>
<?php
$id = is('id', false);
$tweets = select("SELECT * FROM `job_tweet` jt WHERE jt_job=$id");  
if($id){
	print "<ul class='nav nav-tabs' role='tablist'>
	    <li role='presentation' class='active'><a href='#home' aria-controls='home' role='tab' data-toggle='tab'>Sentiment Map</a></li>
	    <li role='presentation'><a href='#profile' aria-controls='profile' role='tab' data-toggle='tab'>Heat Map</a></li>
	    <li role='presentation'><a href='?f=affinity&id=$id' aria-controls='profile' role='tab' data-toggle='tab'>Affinity</a></li>
	    <li role='presentation'><a href='?f=timeline&id=$id' aria-controls='profile' role='tab' data-toggle='tab'>Timeline</a></li>
	    <li role='presentation'><a href='?f=geo&id=$id' aria-controls='profile' role='tab' data-toggle='tab'>Geo Map</a></li>
	    <li role='presentation'><a href='?f=cluster&id=$id' aria-controls='profile' role='tab' data-toggle='tab'>Clustering</a></li>
		<li><a href='?'><i class='fa fa-mail-reply'></i>Back to Jobs</a></li>
	  </ul>";

	print "<div class='tab-content'>
    	<div role='tabpanel' class='tab-pane active' id='home'>";
    		include("pages/sentiment.php");
    print "</div>
    	<div role='tabpanel' class='tab-pane' id='profile'>";
    		include("pages/heatmaps.php");
    print "</div>
      </div>";
	  /*
	  <li><a href="sentiment">Sentiment Map</a></li>
          <!--li><a href="heatmaps">Heat Map</a></li>
          <li><a href="affinity">Affinity</a></li>
          <li><a href="timeline">Timeline</a></li>
          <li><a href="geo">Geo Map</a></li-->
          <li><a href="cluster">Clustering</a></li>
       */
} else{
	print "<ul class='nav nav-tabs' role='tablist'>
	    <li role='presentation' class='active'><a href='#home' aria-controls='home' role='tab' data-toggle='tab'>Active Jobs</a></li>
	    <li role='presentation'><a href='#profile' aria-controls='profile' role='tab' data-toggle='tab'>Job History</a></li>
	  </ul>";
	$jobs = select('*', 'jobs');

	print "<table class='table table-striped table-hover '>
	  <thead>
	    <tr>
	      <th>#</th>
	      <th>Name</th>
	      <th>Retweet?</th>
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
		      <td>$job->j_startdate - $job->j_enddate</td>
		      <td>$job->j_status</td>
		      <td><div class='btn-group'>
                    <a href='https://bootswatch.com/paper/#' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                      Choose...
                      <span class='caret'></span>
                    </a>
                    <ul class='dropdown-menu'>
                      <li><a href='search?f=edit'>Edit</a></li>
                      <li><a href='search?f=suspend'>Suspend</a></li>
                      <li><a href='search?f=status'>Status</a></li>
                     </ul>
                  </div>
               </td>
		    </tr>";
		$i++;
	}
	print "</tbody>
		</table>";
}
<?php
$content = '<style type="text/css">
	td{
		padding: 3px;
	}
</style>
<table class="table-bordered table-responsive">
	<thead><tr><th>ID</th><th>User</th><th>Time</th><th>Text</th><th>Cleaner Version</th><th>Retweet?</th><th>ANEW</th></thead>
	<tbody>';
$tweets = select("SELECT t.*, jt_anew, jt_cleantweet FROM job_tweet jt, tweet t WHERE jt_job=$id AND jt_tweet=t.id AND jt_anewcount>=2 $filter ORDER BY id");
while($tweet = mysqli_fetch_object($tweets)){
	$content .= "<tr>
		<td>$tweet->id</td>
		<td>$tweet->CreateBy</td>
		<td>$tweet->CreateAt</td>
		<td>".str_replace(chr(133), " ", (str_replace(chr(160), " ", $tweet->TweetText)))."</td>
		<td>".str_replace(chr(160), " ", $tweet->jt_cleantweet)."</td>
		<td>".($tweet->Retweet==1?"Yes":"No")."</td>
		<td>$tweet->jt_anew</td>
		</tr>";
}

$content .= '
	</tbody>
	<tfoot></tfoot>
</table>';

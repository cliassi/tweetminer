<style type="text/css">
	td{
		padding: 5px;
	}
</style>
<div align="center">
<?php
$uploaded = false;
$printForm = false;
if(isset($_FILES['file']['name'])){
	$ext = ext($_FILES['file']['name']);
	if(in_array($ext, array(".csv", ".txt"))){
		$name = upload($_FILES, time(), "uploads");
		print "<div class='alert alert-success' role='alert'>File uploaded successfully</div>";
		$uploaded = true;
	} else{
		print "<div class='alert alert-danger' role='alert'>Invalid file format!</div>";
	}
} else{
	$printForm = true;
}
if(isset($_POST['upload_id'])){
	$id =  $_POST['upload_id'];
}
if($uploaded || isset($_POST['update'])){
	$printForm = false;
	if(isset($_POST['update'])){
		$tweet_upload = R::load("tweet_uploads", $id);
		$tweet_upload->tu_date_format = $_POST['format'];
		R::store($tweet_upload);
	}
	$firstRow = getFirstRow("uploads/$name");
	print "<form method='post'>";
	print "<input type='hidden' name='upload_id' value='$tweet_upload->id' />";
	print "<table class='table table-striped table-responsive'>";
	print "<tr><td>First Record: </td><td>SL #: {$firstRow[0]}<br>
		Name: {$firstRow[1]}<br>
		Code: {$firstRow[2]}<br>
		Buying Price: {$firstRow[3]}<br>
		Selling Price: {$firstRow[4]}<br>
		Quantity: {$firstRow[5]}<br>
		</td></tr>";
	print "</table>";
	if($date){
		print "<input type='submit' name='process' value='Process Now' />";
	} else{
		print "<input type='submit' name='update' value='Update Format' />";
	}

	print "</form><br />";
} elseif(isset($_POST['process'])){
	$skip = 1;
	$printForm = false;
	$tweet_upload = R::load("tweet_uploads", $id);

	if (($handle = fopen("uploads/$tweet_upload->tu_file", "r")) !== FALSE) {
		$start_time = date_create();
		$end_time = date_create();
		date_timestamp_set($end_time, 0);

	    while (($data = fgetcsv($handle)) !== FALSE) {
	    	$time_low = now();
	    	if($skip){ $skip = 0; continue; }

	        $format = $tweet_upload->tu_date_format;
			$date = DateTime::createFromFormat($format, $data[0].":00");
			$start_time = $start_time>$date?$date:$start_time;
			$end_time = $end_time<$date?$date:$end_time;

	        insert("tweet", "CreateBy,CreateAt,TweetText,Retweet,UploadID", "'{$data[1]}','".$date->format('Y-m-d H:i:s')."',
	        	'".$c->real_escape_string($data[4])."','".(substr($data[4], 0, 2)=="RT")."',$tweet_upload->id");
	    }
	    $tweet_upload->tu_start_time = $start_time->format('Y-m-d H:i:s');
	    $tweet_upload->tu_end_time = $end_time->format('Y-m-d H:i:s');
	    $tweet_upload->tu_status = 'Processed';
		R::store($tweet_upload);
	    fclose($handle);
	    print "<div class='alert alert-success' role='alert'>File processed successfully</div>";
	}
}

?>
<table class="table-bordered table-responsive">
<?php

function getFirstRow($file, $skip_first=1){
	if (($handle = fopen($file, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle)) !== FALSE) {
	    	if($skip_first){
	    		$skip_first = 0;
	    		continue;
	    	}
	    	return $data;
	    }
	    fclose($handle);
	}
}

//FORM
$form = "<form method='post' enctype='multipart/form-data'>";
	$form .= "<table class='table-responsive'>";
	$form .= "<tr><td>Name: </td><td><input type='text' name='name' value='' class='required' /></td></tr>";
	$form .= '<tr><td>File to Import </td><td> <input type="file" name="file" accept=".csv,.txt" /></td></tr>';
	$form .= "<tr><td>Delimiter</td><td><input type='text' name='delimiter' placeholder='e.g. , or TAB' /></td></tr>";
	$form .= "</table>";
	$form .= "<input type='submit' class='btn btn-success' name='upload' value='Upload' /> <button type='reset' onclick='back();' class='btn btn-danger'> Cancel </button> ";
	$form .= "</form>";
if($printForm){
	print $form;
}
?>
</table>
</div>
<script type="text/javascript">
	function back(){
		location.href="upload";
		return false;
	}
</script>

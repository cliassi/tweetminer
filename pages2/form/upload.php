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
		$tweet_upload = R::dispense("tweet_uploads");	
		//print "<img src='assets/gears.gif' alt='uploading' />";
		$name = upload($_FILES, time(), "uploads");
		$tweet_upload->tu_name = $_POST['name'];
		$tweet_upload->tu_time = now();
		$tweet_upload->tu_file = $name;
    	$tweet_upload->tu_date_format = $_POST['format'];
    	R::store($tweet_upload);

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
	$firstRow = getFirstRow("uploads/$tweet_upload->tu_file");
	print "<form method='post'>";
	print "<input type='hidden' name='upload_id' value='$tweet_upload->id' />";
	print "<table class='table-responsive'>";
	print "<tr><td>First Record: </td><td>CreateAt: {$firstRow[0]}<br>
		CreateBy: {$firstRow[1]}<br>
		Valance: {$firstRow[2]}<br>
		Arousal: {$firstRow[3]}<br>
		TweetText: {$firstRow[4]}<br>
		</td></tr>";

	$format = $tweet_upload->tu_date_format;
	$date = DateTime::createFromFormat($format, $firstRow[0].":00");
	if($date){
		$tweet_upload->tu_status = 'PendingProcessing';
		R::store($tweet_upload);
		print "<tr><td>DateTime After Formating: </td><td><div class='alert alert-success' role='alert'>".$date->format('Y-m-d H:i:s')."</div></td></tr>";
	} else{
		$tweet_upload->tu_status = 'DateFormatInvalid';
		R::store($tweet_upload);
		print "<div class='alert alert-danger' role='alert'>Invalid date format!</div>";
		print "<tr><td>DateTime Format: </td><td><input type='text' name='format' value='$format' /></td></tr>";			
		print "<tr><td></td><td>
				Y = 4 Digits Year<br>
				y = 2 Digits Year<br>
				m = Month<br>
				d = Day <br>
				H = Hour (24)<br>
				h = Hour (12)<br>
				i = Minute<br>
				s = Second
			</td></tr>";
	}
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
	$form .= "<tr><td>DateTime Format: </td><td><input type='text' name='format' value='d/m/Y H:i:s' /></td></tr>";
	$form .= "<tr><td></td><td>
			Y = 4 Digits Year<br>
			y = 2 Digits Year<br>
			m = Month<br>
			d = Day <br>
			H = Hour (24)<br>
			h = Hour (12)<br>
			i = Minute<br>
			s = Second
		</td></tr>";
	$form .= "</table>";
	$form .= "<input type='submit' name='upload' value='Upload' /> <button type='reset' onclick='back();' class='btn-danger'> Cancel </button> ";
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
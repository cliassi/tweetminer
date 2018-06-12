<link rel="stylesheet" href="css/jquery.qtip.min.css">
<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
<?php
print "<ul class='nav nav-tabs'>
	<li class='active'><a href='?f=add' aria-controls='profile' class='active'>Upload Now</a></li>
  </ul>";
$uploads = select('*', 'tweet_uploads');

print "<table class='table table-striped table-hover'>
  <thead>
    <tr>
      <th>#</th>
      <th>Name</th>
      <th>Upload Time</th>
      <th>Date Format</th>
      <th>Start Time</th>
      <th>End Time</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>";

$i = 1;
while($upload = mysqli_fetch_object($uploads)){
	print "<tr>
	      <td>$i</td>
	      <td><a href='?id=$upload->id'>$upload->tu_name</a></td>
	      <td>$upload->tu_time</td>
	      <td>$upload->tu_date_format</td>
	      <td>$upload->tu_start_time</td>
	      <td>$upload->tu_end_time</td>
	      <td>$upload->tu_status</td>
	      <td><div class='btn-group'>
                <a href='#' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                  Choose...
                  <span class='caret'></span>
                </a>
                <ul class='dropdown-menu'>
                  <li><a href='?f=createjob&id=$upload->id'>Create a ".APPNAME."</a></li>
                  <li>".($upload->tu_status=='DateFormatInvalid'?"<a href='?f=fix&id=$upload->id'>Fix Format</a>":"<a href='?f=suspend&id=$upload->id'>Suspend</a>")."</li>
                  <li><a href='?f=remove&id=$upload->id'>Remove</a></li>
                  <li>".($upload->tu_status=='Suspended'?"<a href='?f=resume&id=$upload->id'>Resume</a>":"<a href='?f=suspend&id=$upload->id'>Suspend</a>")."</li>
                 </ul>
              </div>
           </td>
	    </tr>";
	$i++;
}
print "</tbody>
	</table>";
?>
<link rel="stylesheet" href="css/jquery.qtip.min.css">
<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
<?php
print "<ul class='nav nav-tabs'>
	<li class='active'><a href='?f=add' aria-controls='profile' class='active'>Upload Now</a></li>
  </ul>";
$anews = select('*', 'anew', '', 'order by description');

print "<table class='table table-striped table-hover'>
  <thead>
    <tr>
      <th>#</th>
      <th>Word</th>
      <th>Root</th>
      <th>Valence Mean</th>
      <th>Arousal Mean</th>
      <th>Valence Mean SD</th>
      <th>Arousal Mean SD</th>
      <th>Edit?</th>
      <th>Remove?</th>
    </tr>
  </thead>
  <tbody>";

$i = 1;
while($anew = mysqli_fetch_object($anews)){
	print "<tr>
	      <td>$i</td>
	      <td><a href='?id=$anew->id'>$anew->description</a></td>
	      <td>$anew->root</td>
	      <td>$anew->valencemean</td>
	      <td>$anew->arousalmean</td>
	      <td>$anew->vmsd</td>
	      <td>$anew->amsd</td>
        <td><a href='?f=createjob&id=$anew->id'>Edit</a></td><td><a href='?f=remove&id=$anew->id'>Remove</a></td>
	    </tr>";
	$i++;
}
print "</tbody>
	</table>";
?>
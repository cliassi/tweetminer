<?php
//$tweets = select('SELECT * FROM `job_tweet` jt WHERE jt_job=7 AND vm>0');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name='viewport' content='initial-scale=1.0, user-scalable=no'>
    <meta charset='utf-8'>
    <title>Marker Labels</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
    </style>
    <script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyA7L7fwmrSJFfMELEHuogQS2RWZ1VToSL8&signed_in=true'></script>
    <script>
// In the following example, markers appear when the user clicks on the map.
// Each marker is labeled with a single alphabetical character.
var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
var labelIndex = 0;

function initialize() {
  var bangalore = { lat: 12.97, lng: 77.59 };
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 12,
    center: bangalore
  });
  var geocoder = new google.maps.Geocoder();

  geocodeAddress(geocoder, map, 'Kuala Lumpur', '5');
  geocodeAddress(geocoder, map, 'Petaling Jaya', '3');
  geocodeAddress(geocoder, map, 'Bangkok', '3');
}

function geocodeAddress(geocoder, resultsMap, address, label) {
  //var address = document.getElementById('address').value;
  geocoder.geocode({'address': address}, function(results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      resultsMap.setCenter(results[0].geometry.location);
      var marker = new google.maps.Marker({
        map: resultsMap,
        position: results[0].geometry.location,
        label: label
      });
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}


google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id='map'></div>
  </body>
</html>
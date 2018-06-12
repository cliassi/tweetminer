<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/jquery.qtip.min.css">
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
  <script type="text/javascript" src="js/jquery.qtip.min.js"></script>
</head>
<body>
<svg height="900" width="900">
  <line x1="0" y1="280" x2="900" y2="280" style="stroke:rgb(255,0,0);stroke-width:2" />
  <line x1="400" y1="0" x2="400" y2="630" style="stroke:rgb(255,0,0);stroke-width:2" />
  <circle cx="200" cy="5" r="4" x="200" y="200" fill="red" title="Hello" />
</svg> 
  <script type="text/javascript">
    $('[title]').qtip();
  </script>
</body>
</html>

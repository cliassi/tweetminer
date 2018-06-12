<?php
require 'config/global.php';
require 'system/f.php';
require 'system/rb.php';
R::setup('mysql:host='.$host.';dbname='.$database,$user,$password); R::freeze(true); 
?>
<!DOCTYPE html>
<!-- saved from url=(0029)https://bootswatch.com/paper/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <title>TEMiner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
  <link rel="stylesheet" href="css/bootswatch.min.css">
  <link rel="stylesheet" href="css/custom.css">
  <link rel="stylesheet" href="css/jqcloud.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="../bower_components/html5shiv/dist/html5shiv.js"></script> 
    <script src="../bower_components/respond/dest/respond.min.js"></script>
  <![endif]-->

<script src="js/jquery-3.1.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
</head>
<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a href="search" class="navbar-brand">TEMiner</a>
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>
      <div class="navbar-collapse collapse" id="navbar-main">
        <ul class="nav navbar-nav">          
          <li><a href="job?f=add">New <?php print APPNAME; ?></a></li>         
          <li><a href="job?f=view"><?php print APPNAME; ?> History</a></li>
          <li><a href="upload">Upload</a></li>
          <li><a href="anew">Word List</a></li>
        </ul>

        <!--ul class="nav navbar-nav navbar-right">
          <li><a href="about" target="_blank">About</a></li>
          <li><a href="help" target="_blank">Help</a></li>
        </ul-->

      </div>
    </div>
  </div> 
  <br clear="all">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
      <?php
        $page = isset($_GET['q'])?$_GET['q']:"job";
        require "pages/$page.php";
      ?>
      </div>
    </div>
  </div>      
<script src="js/bootstrap.min.js"></script>
<script src="js/bootswatch.js"></script>
<script src="js/jqcloud.min.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
</body>
</html>
<?php
  $job->j_name = $_POST['name'];
  $job->j_retweet = isset($_POST['retweet'])?1:0;
  $job->j_startdate = $_POST['start_date'];
  $job->j_enddate = $_POST['end_date'];
  $job->j_type = 'Complex';
  $job->j_criteria = '';

  if(isset($_POST['criteria'])){
    $job->j_type = 'Simple';
    switch($_POST['criteria']){
      case 'Location':{
        $job->j_country = $_POST['keywords'];
      } break;
      case 'Keywords':{
        $job->j_keywords = $_POST['keywords'];
      } break;
      case 'Multi-User':{
        $job->j_users = $_POST['keywords'];
      } break;
      case 'Hash Tag':{
        $job->j_hashtag = $_POST['keywords'];
      } break;
      case 'Mention':{
        $job->j_mention = $_POST['keywords'];
      } break;
    }
    $job->j_criteria = $_POST['criteria'];
  } else{
    $job->j_country = $_POST['country'];
    $job->j_clogic = $_POST['lcountry'];
    $job->j_city = $_POST['city'];
    $job->j_cilogic = $_POST['lcity'];
    $job->j_keywords = $_POST['keywords'];
    $job->j_klogic = $_POST['lkeywords'];
    $job->j_users = $_POST['users'];
    $job->j_ulogic = $_POST['lusers'];
    $job->j_hashtag = $_POST['hashtag'];
    $job->j_hlogic = $_POST['lhashtag'];
    $job->j_mention = $_POST['mention'];
  }

  $job_id = R::store($job);

  print "<h1>Job successfully created</h1>";
  //var_dump($_POST);
  //var_dump($job);
  die();

  /*
  $job->j_country
  $job->j_clogic
  $job->j_city
  $job->j_cilogic
  $job->j_keywords
  $job->j_klogic
  $job->j_users
  $job->j_ulogic
  $job->j_hashtag
  $job->j_hlogic
  $job->j_mention
  */
<?php 
  $anew = R::dispense("anew");
  $width = "700px";
  $width1 = "100px";
  $func = isset($_GET["f"])?($_GET["f"]==""?"view":$_GET["f"]):"view";
  if(isset($_GET["id"])) { $id = $_GET["id"]; }
  switch ($func){
    case "view": {
       require("pages/view/anew.php");
    } break;
    case "suspend": {    
      $anew = R::load("anew", $id);
      $anew->j_status = 'Suspended';
      R::store($anew);
      redir("?f=view");
    } break;
    case "resume": {    
      $anew = R::load("anew", $id);
      $anew->j_status = 'Pending';
      R::store($anew);
      redir("?f=view");
    } break;
    case "reset": {    
      $anew = R::load("anew", $id);
      $anew->j_status = 'Pending';
    $anew->j_lastid = 0;
    $anew->j_total_tweets = 0;
    $anew->j_processed_tweets = 0; 
    $anew->j_matched_tweets = 0; 
    $anew->j_updated = 1;
    del("anew_tweet", "jt_anew=$id");
      R::store($anew);
      redir("?f=view");
    } break;
    case "remove": {
      if(isset($_GET["conf"])){     
        $anew = R::load("anew", $id);
        R::trash($anew);
        redir("?".uri("f", "view", array("id")));
      } else{
      ?>
      <script type="text/javascript">
        if(confirm("Are you sure you want to remove this anew?")){
          location.href = "?<?php echo uri();?>&conf";
        } else{
          location.href = "?f=view";  
        }
      </script>
      <?php
      }
    } break;
    case "edit": {
       $anew = R::load("anew", $id);
     $criteria = R::findOne("anew_criteria", "jc_anew=?", array($id));
    } 
    case "add": {
      if(isset($_POST["save"])){
        require_once("pages/pss/anew.php");
        redir("?f=view");
      }
      require_once("pages/form/anew.php");
    } break;
  }
?>
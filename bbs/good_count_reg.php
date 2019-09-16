<?php
require('base.php');
  switch ($_POST['iotype']) {
   case 'json':
      break;
   case 'db':
     
      $board = new Board();

      break;
  }
if($_POST['iotype']== "json"){

  $JSON_all =  JSON_load();

  $result = good_count_up_JSON( $JSON_all,$_POST['cat_no'],$_POST['thr_no'],$_POST['pos_no']);

  if($result==false){
     echo json_encode(array("state"=>"NG"));
  }else{
     echo json_encode(array("state"=>"OK"));
  }
  exit();
}elseif($_POST['iotype']== "db"){

  $good_count_up_result = $board->good_count_up($_POST['thr_no'],$_POST['pos_no']);

   if( $good_count_up_result){
     echo json_encode(array("state"=>"OK"));
   }else{
     echo json_encode(array("state"=>"NG"));
   }
};
?>
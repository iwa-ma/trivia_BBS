<?php
require('base.php');
header("Content-type: application/json; charset=UTF-8");

  switch ($_POST['iotype']) {
   case 'json':
      break;
   case 'db':
        $board = new Board();
      break;
  }

if($_POST['iotype']== "json"){

  $JSON_all =  JSON_load();

  $cat_no = cat_no_get($_POST['cat_name']);

//del_confirmationが1なら、すでにパスワード認証終わっているので、削除処理を行い抜ける。
  switch ($_POST['del_confirmation']) {
  case 0:
     break;
  case 1:
  //***削除処理***
  //***del_confirmationが1ならパスワード照合済みと判断して削除処理を行う***
      $thread_del_result = post_del_JSON($JSON_all,$cat_no,$_POST['thr_no'],$_POST['pos_no']);

      switch ($thread_del_result) {
        case false:
          error_log(print_r('del_reg.php::Board->thread_del スレッド投稿削除JSON ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
         }
      echo json_encode(array("state"=>"DEL_END"));
      exit();
      break;
  }

//***認証処理***
  $password   = $_POST['password'];
  $password    = htmlspecialchars($password , ENT_QUOTES);

  $password_hash = get_post_password_JSON($JSON_all,$cat_no,$_POST['thr_no'],$_POST['pos_no']);

  //**POSTの値、ハッシュ値の内容からjson形式で結果を返す。
  if($password ==''){
     echo json_encode(array("state"=>"BLANK"));
  }elseif (password_verify($password, $password_hash)) {
     //echo "パスワード一致";
     echo json_encode(array("state"=>"OK"));
  }else {
     echo json_encode(array("state"=>"NG"));
   }

}elseif($_POST['iotype']== "db"){
//del_confirmationが1なら、すでにパスワード認証終わっているので、削除処理を行い抜ける。
  switch ($_POST['del_confirmation']) {
  case 0:
     break;
  case 1:
  //***削除処理***
    if($_POST['pos_no'] == 1){
      $thread_del_result = $board->thread_del($_POST['thr_no']);

      switch ($thread_del_result) {
        case false:
          error_log(print_r('del_reg.php::Board->thread_del スレッド削除 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
       }
    }

      $post_del_result = $board->post_del($_POST['thr_no'],$_POST['pos_no']);

      switch ($post_del_result) {
        case true:
          echo json_encode(array("state"=>"DEL_END"));
          break;
        case false:
          error_log(print_r('del_reg.php::Board->post_del 投稿削除 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
       }
    exit();
      break;
  }

  $password = $_POST['password'];
  $password = htmlspecialchars($password , ENT_QUOTES);

  $password_hash = $board->get_post_password($_POST['thr_no'],$_POST['pos_no']);

  if($password_hash != false){
      if($password ==''){
         echo json_encode(array("state"=>"BLANK"));
      }elseif (password_verify($password, $password_hash)) {
         echo json_encode(array("state"=>"OK"));
      }else {
         echo json_encode(array("state"=>"NG"));
       }
  }else{
         error_log(print_r('del_reg.php::Board->get_post_password パスワード取得 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
  }
 
}
?>
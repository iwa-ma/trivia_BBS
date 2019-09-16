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

  $JSON_all = JSON_load();

  $cat_no = cat_no_get($_POST["category_con_h"]);

  switch (isset($_POST["thr_id_h"])) {
    case true:
      define("THREAD_NEW",FALSE);
  $result = new_thread_add_posted_JSON($JSON_all,$cat_no,$_POST["thr_id_h"],$_POST["category_con_h"],$_POST["password_con_h"],$_POST["title_con_h"],$_SERVER["REMOTE_ADDR"],$_POST["penname_con_h"],$_POST["maintext_con_h"],THREAD_NEW);
      break;
    case false:
      define("THREAD_NEW",TRUE);
  $result = new_thread_add_posted_JSON($JSON_all,$cat_no,NULL,$_POST["category_con_h"],$_POST["password_con_h"],$_POST["title_con_h"],$_SERVER["REMOTE_ADDR"],$_POST["penname_con_h"],$_POST["maintext_con_h"],THREAD_NEW);
      break;
  }

  switch ($result) {
    case true:
        header('Location: index.php?mode=json');
          exit();
      break;
    case false:
        error_log(print_r('Warning registration.php:: JSONファイルの書き込みに失敗'."\n".$e->getMessage().' ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
      break;
  }


}elseif($_POST['iotype']== "db"){
  //**スレッドタイトルの有無で、新規投稿か追記か分岐
  switch (isset($_POST["thr_id_h"])) {
    case true:
      define("THREAD_NEW",FALSE);
      break;
    case false:
      define("THREAD_NEW",TRUE);
      break;
  }

  //ユーザーエージェントに文字リストの単語を含む場合はTRUE、それ以外はFALSE
  $ua = agent_type();
  //ID生成
  $new_id =uniqid(mt_rand(1, 6));
  $ipaddress =$_SERVER["REMOTE_ADDR"];
  $thr_title = $_POST["title_con_h"];
  $now_time =date("Y-m-d H:i:s");

  //書き込み不可モードの場合はここでexitさせる
  //exit();

  //ここまでの情報で書き込みを行う
  if(THREAD_NEW == TRUE){
  //カテゴリNoを取得
    $cat_no_result = $board->get_cat_no($_POST["category_con_h"]);
    switch ($cat_no_result) {
      case false:
        error_log(print_r('registration.php::Board->get_cat_name カテゴリNo取得 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
        exit;
        break;
     }

  //新規thredレコードを追加
    $add_thr_id = $board->new_thread_add_thread($cat_no_result,$thr_title,$now_time,$ipaddress,$ua,$new_id);

      switch (mb_strlen($_POST["password_con_h"])!=0) {
        case true:
           $password_st =1;
           $password_text = password_hash($_POST["password_con_h"], PASSWORD_DEFAULT);
          break;
        case false:
           $password_st =0;
           $password_text = "NULL";
          break;
      }

      $new_thread_add_posted_result = $board->new_thread_add_posted($add_thr_id,$_POST["title_con_h"],$now_time,
    $_SERVER["REMOTE_ADDR"],$ua,$new_id,$_POST["penname_con_h"],$_POST["maintext_con_h"],$password_st,$password_text);

  }
  if(THREAD_NEW == FALSE){
    $max_pos_no = $board->get_maxpos_no($_POST['thr_id_h']);
      switch ($max_pos_no) {
        case false:
          error_log(print_r('registration.php::Board->get_maxpos_no 投稿No取得 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
       }
    $max_pos_no = $max_pos_no+1;

      switch (mb_strlen($_POST["password_con_h"])!=0) {
        case true:
           $password_st =1;
           $password_text = password_hash($_POST["password_con_h"], PASSWORD_DEFAULT);
          break;
        case false:
           $password_st =0;
           $password_text = "NULL";
          break;
      }
    //postedに追加
      $board->add_post_add_posted(intval($_POST['thr_id_h']),$max_pos_no,$_POST["title_con_h"],
       date("Y-m-d H:i:s"),$_SERVER["REMOTE_ADDR"],$ua,$new_id,$_POST["penname_con_h"],
       $_POST["maintext_con_h"],$password_st,$password_text);
   //threadを更新
      $board->add_post_add_thread(date("Y-m-d H:i:s"),intval($_POST['thr_id_h']));
  }
 // ここでtopにリダイレクト
 header('Location: index.php?mode=db');
 exit();
}
?>
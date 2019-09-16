<script type="text/javascript" src="common/js/base.js"></script>
<?php
require('base.php');
if($_POST['iotype']=='db'){
  $board = new Board();

/**表示データの作成  **/
  //指定のカテゴリ、スレッドを取得
  $posts_puts = $board->get_cat_thread($_POST['cat_no']);

  //登録済み投稿数をカウント
  $post_num = count($posts_puts);
  //最大ページ数を算出
  $max_page = ceil($post_num / ARTICLE_MAX_NUM);

  if(!isset($_POST['page'])){
      //初期化（初めて訪れた時にはpageが設定されていない）
      $now_page = FIRST_VISIT_PAGE;
  }else if(preg_match("/^[1-9][0-9]*$/",$_POST['page'])){
      //有効な数値かチェックして、念のためエスケープしてGETを格納する
      $now_page = htmlspecialchars($_POST['page'],ENT_QUOTES,'UTF-8');
  }else{
      echo "pageが正しく設定されていなかったので".FIRST_VISIT_PAGE."に設定しました!<br>";
      $now_page = FIRST_VISIT_PAGE;
  }
  /** 記事表示 **/
  //表示対象ページ数から、表示する記事Noを算出
  $start = ($now_page - 1) * ARTICLE_MAX_NUM;
  //$start番目からARTICLE_MAX_NUM個の配列を取得
  $output = array_slice($posts_puts,$start,ARTICLE_MAX_NUM);
  $output_count =count($output);

   if(!isset($_POST['next_no'])){
    $page_no_result = page_no_calculation("0",$output_count,"0");
   }else{
    $page_no_result = page_no_calculation($_POST['next_no'],$output_count,$_POST['pagetype']);

   }
    $start_count = $page_no_result['start_count'] ;
    $end_count = $page_no_result['end_count'];

?>

<?php
  if(empty($output)){
      echo "記事はありません<br>";
  }else{
?>
  <?php if(agent_type()=="pc"){ ?>
    <?php include(__DIR__.'/temp/thread-list_DB_PC.php'); ?>
  <?php    }//if agent pc end?>
  <?php if(agent_type()=="sp"){ ?>
    <?php include(__DIR__.'/temp/thread-list_DB_SP.php'); ?>
  <?PHP }//if agent sp end?>
<?PHP
}//output if
  $link->close();
} //DB if?>

<?PHP
if($_POST['iotype']=='json'){

  $JSON_all =  JSON_load();

//スレッド数カウント用　10以下の場合処理分けるのに使用。
  $cat_name = cat_name_get($_POST['cat_no']);

  $cat_count_all= get_thr_count_all_JSON($JSON_all);

  $thr_count = get_thr_count_JSON($JSON_all,$cat_count_all,$_POST['thr_count_all']);

  $thr = get_cat_thread_json($JSON_all,$_POST['cat_no'],$_POST['thr_count_all'],$cat_count_all);

 if($thr != false){
  //**最終更新日順にソート**
  //配列を並べ替え
   foreach($thr as $key => $value){
   $thr_lastupdate[$key] = $value["thr_lastupdate"];
  }
   array_multisort($thr_lastupdate, SORT_DESC, $thr);

  if($thr_count<10){
    $OUTPUT_THREAD_COUNT=$thr_count;
  }else{
    $OUTPUT_THREAD_COUNT=10;
  }
    /** 投稿データの作成  **/
    $posts[] = $thr;
    //表示対象総件数を算出
    if(!isset($_POST['page'])){
      $post_num = count($posts[0]);
    }elseif(isset($_POST['page']) and $_POST['page'] ===1){
      $post_num = count($posts[0]);
    }else{
       $post_num = count($posts[0]);
    }

    //ページ送りで表示された場合を想定して条件分け
    //現在のページ番号をGETで受け取る
    if(!isset($_POST['page'])){
        //初期化（初めて訪れた時にはpageが設定されていない）
        $now_page = FIRST_VISIT_PAGE;
    }else if(preg_match("/^[1-9][0-9]*$/",$_POST['page'])){
        //有効な数値かチェックして、念のためエスケープしてGETを格納する
        $now_page = htmlspecialchars($_POST['page'],ENT_QUOTES,'UTF-8');
    }else{
        echo "pageが正しく設定されていなかったので".FIRST_VISIT_PAGE."に設定しました!<br>";
        $now_page = FIRST_VISIT_PAGE;
    }

    //最大ページ数を算出
    $max_page = ceil($post_num / ARTICLE_MAX_NUM);

      /** 記事表示 **/
    //何記事目から表示すれば良いか
    if(!isset($_POST['start_no'])){
       $start = 0;
      echo "starnoなし";
    }elseif(isset($_POST['start_no']) and isset($_POST['page']) and $_POST['page'] ===1){
      $start = 0;
    }else{
        $start = $_POST['start_no'] -1;
    }

    unset($output);
    //$postsという配列の$start番目からARTICLE_MAX_NUM個の配列を取得
    $output = array_slice($posts[0],$start,ARTICLE_MAX_NUM);
    $output_count =count($output);

    $start_no_work_back = $_POST['start_no'] - ARTICLE_MAX_NUM;

    $start_no_work_next = $_POST['start_no'] + ARTICLE_MAX_NUM;

   if(!isset($_POST['next_no'])){
    $page_no_result = page_no_calculation("0",$output_count,"0");
   }else{
    $page_no_result = page_no_calculation($_POST['next_no'],$output_count,$_POST['pagetype']);
   }
    $start_count = $page_no_result['start_count'] ;
    $end_count = $page_no_result['end_count'];
 }
?>

<?php
  if(empty($output)){
      echo "記事はありません<br>";
  }else{
?>
  <?php if(agent_type()=="pc"){ ?>
    <?php include(__DIR__.'/temp/thread-list_JSON_PC.php'); ?>
  <?php    }//if agent pc end?>
  <?php if(agent_type()=="sp"){ ?>
    <?php include(__DIR__.'/temp/thread-list_JSON_SP.php'); ?>
  <?PHP }//if agent sp end?>
<?PHP
  }//output if
}//json if END
?>


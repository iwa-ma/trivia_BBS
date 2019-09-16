<?php
require('base.php');
  //共通処理**検索キーワードを変数に格納**
  //ページングの時は文字列で送信されているので配列に変換
  if(gettype($_POST['search_text'])=='string'){
    $search_key = explode(",", $_POST['search_text'] );
  }else{
    $search_key = $_POST['search_text'];
  }
?>
  <script type="text/javascript" src="common/js/base.js"></script>
<?PHP
if($_POST['iotype']=='db'){
  $board = new Board();
//**検索処理**
//検索キーワードの個数分処理を行う
for($serchkey_i =0; $serchkey_i <count($search_key); $serchkey_i++){
  $posts_puts =  $board->search($search_key[$serchkey_i]);
    if ($posts_puts === false) {
      error_log(print_r('search.php::Board->search 検索結果取得 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
      exit;
      break;
    }
    
  if(!isset($output_work) and empty($posts_puts) ===false){
    $output_work = search_resuletAdd($posts_puts,$search_key,$serchkey_i);
    unset($posts_puts);
  }else{
  //検索一致スレッド登録済みでキーワード2個目以降
    $output_work = search_result($posts_puts,$output_work,$search_key,$serchkey_i);
  } //else end
}//for end

//$output_workから、不要になった一致結果(キーワード1で一致、キーワード2で不一致)を削除
$output_work_no = 0;
if(isset($output_work)){
  foreach($output_work as $key => $results_put){
    $cwork = count(explode(",",$output_work[$output_work_no]["search_word"]));
    if(($serchkey_i) > $cwork){
      unset($output_work[$output_work_no]);
    }else{
     }
        $output_work_no++;
  }// foreach $output_work
}

if(isset($output_work)==false){
?>
 <div class="search_restitle">検索キーワード：[
  <?php  
    $search_key_out_no = 0;
    foreach($search_key as $key => $search_key_out){
        if($search_key_out_no!=0){ echo ",";}
        echo $search_key_out;
        $search_key_out_no++;
    }// foreach $search_key
    unset($search_key_out);
  ?>
 ]</div> <!-- div class="search_restitle" -->
 <div class="search_zero">一致する投稿はありません</div>
<?php
 exit();
}//if(isset($output_work)

//**paging用に設定
//最大出力数設定
if(count($output_work)<10){
  $OUTPUT_THREAD_COUNT=count($output_work);
}else{
  $OUTPUT_THREAD_COUNT=10;
}

//検索一致スレッド数をカウント
  $post_num = count($output_work);
//検索
  $max_page = ceil($post_num / ARTICLE_MAX_NUM);

  /** 現在のページ番号をGETで受け取る **/
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
  //何記事目から表示すれば良いか
  $start = ($now_page - 1) * ARTICLE_MAX_NUM;

  //$postsという配列の$start番目からARTICLE_MAX_NUM個の配列を取得
  $output = array_slice($output_work,$start,ARTICLE_MAX_NUM);
  $output_count =count($output);
  
   if(!isset($_POST['next_no'])){
    $page_no_result = page_no_calculation("0",$output_count,"0");
   }else{
    $page_no_result = page_no_calculation($_POST['next_no'],$output_count,$_POST['pagetype']);
   }
    $start_count = $page_no_result['start_count'] ;
    $end_count = $page_no_result['end_count'];

  foreach ($output as $key => $value){
  $key_thr_lastupdate[$key] = $value['thr_lastupdate'];
  }
  if(isset($key_thr_lastupdate)){
  array_multisort ( $key_thr_lastupdate , SORT_DESC , $output);
  }

  if(empty($output)){
      echo "一致する投稿はありません<br>";
  }else{
  /** ヘッダー表示 **/
?>
    <div class="search_restitle">
      [<?PHP
       foreach($search_key as $value){
        echo $value;
        if ($value != end($search_key)) {
         echo ",";
          }
       }
      ?>]の検索結果
    </div>
<?PHP    if(agent_type()=="pc"){?>
    <?php include(__DIR__.'/temp/searchresult_DB_PC.php'); ?>
<?PHP              }////if pcのend?>
<?PHP    if(agent_type()=="sp"){?>
    <?php include(__DIR__.'/temp/searchresult_DB_SP.php'); ?>
<?PHP    }//if sp end?>
  <?php 
    }
}//db if end
?>

 <?php 
if($_POST['iotype']=='json'){

  $JSON_all = JSON_load();

 //配列をカテゴリカラムで読み込み。
  $root= array_column($JSON_all,'cat');

if( DEBUG ){
echo "<PRE>";
print_r($_POST);
echo "</PRE>";
}

  $cat_count_all = get_cat_count_all_JSON($JSON_all);

  $thr_count_all = get_thr_count_all_JSON($JSON_all);

  $post_update = get_post_all_JSON($JSON_all,$cat_count_all,$thr_count_all);

  $post_count = count($post_update);

//結果格納配列の添字初期化
$result_no = 0;

//***キーワード毎に全件検索
//検索データ　$search_key[0]
// count($search_key)
//検索先　$post_update
//検索キー　pos_title,pos_created,pos_penname,pos_text
//投稿件数$post_count
//$post_count = count($post_update[]);

//**検索処理**
//検索キーワードの個数分処理を行う
for($serchkey_i =0; $serchkey_i <count($search_key); $serchkey_i++){
//検索結果が登録済み(キーワード2個目以降)か判断
  if(empty($result) && $serchkey_i == 0 ){
    
  $search_key_detection_st = 0 ;
//検索結果が1件もない場合に全投稿を検索
//投稿全件サーチ
    //***pos_for_start
    for($pos_i =0; $pos_i <$post_count; $pos_i++){
      if (isset($result)){
        $result = search_all_JSON($post_update,$search_key,$serchkey_i,$pos_i,$result_no,$result);
      }else{
        $result = search_all_JSON($post_update,$search_key,$serchkey_i,$pos_i,$result_no);
      }
//***出力用データ作成
     //キー検出結果1以上あり、
      if($result[$result_no]!=false){
        $result = search_resuletAdd_JSON($post_update,$search_key,$serchkey_i,$pos_i,$result_no,$result);
        if(isset($result[$result_no]['cat_id'])){
          $result_no++;
        }
      }else{
          array_splice($result, $result_no, 1);
      } //***if_end
    }//***pos_for_end

//*****************
//検索一致スレッド登録済みでキーワード2個目以降
//and比較を意識、キーワード2個目が検索一致スレッド登録済みデータから
//見つからなかったら削除、見つかったらそのまま

//検索結果が存在している場合。
 //empty($result)がfalse
  }elseif(empty($result) == FALSE){
    $result = search_result_JSON($JSON_all,$result,$search_key,$serchkey_i);
  }//***else_end
}//***serchkey_for_end

 if(isset($result)){
  if($result == false){unset($result);};
 }

if(isset($result)==false){
?>
 <div class="search_restitle">検索キーワード：[
  <?php  
    $search_key_out_no = 0;
    foreach($search_key as $key => $search_key_out){
        if($search_key_out_no!=0){ echo ",";}
        echo $search_key_out;
        $search_key_out_no++;
    }// foreach $search_key
    unset($search_key_out);
  ?>
 ]</div> <!-- div class="search_restitle" -->
 <div class="search_zero">一致する投稿はありません</div>
<?php
 exit();
}
//**paging用に設定
//最大出力数設定
if(count($result)<10){
  $OUTPUT_THREAD_COUNT=count($result);
}else{
  $OUTPUT_THREAD_COUNT=10;
}

//検索一致スレッド数をカウント
  $post_num = count($result);
//検索
  $max_page = ceil($post_num / ARTICLE_MAX_NUM);

  /** 現在のページ番号をGETで受け取る **/
 // if(!isset($_GET['page'])){
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
  //何記事目から表示すれば良いか
  $start = ($now_page - 1) * ARTICLE_MAX_NUM;
  $output = array_slice($result,$start,ARTICLE_MAX_NUM);
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

  foreach ($output as $key => $value){
  $key_thr_lastupdate[$key] = $value['thr_lastupdate'];
  }
  array_multisort ( $key_thr_lastupdate , SORT_DESC , $output);

  if(empty($output)){
      echo "一致する投稿はありません<br>";
  }else{
?>
<div class="search_restitle">
      [<?PHP
       foreach($search_key as $value){
        echo $value;
        if ($value != end($search_key)) {
         echo ",";
          }
       }
      ?>]の検索結果
    </div>
<?PHP    if(agent_type()=="pc"){?>
    <?php include(__DIR__.'/temp/searchresult_JSON_PC.php'); ?>
<?PHP              }////if pcのend?>
<?PHP    if(agent_type()=="sp"){?>
    <?php include(__DIR__.'/temp/searchresult_JSON_SP.php'); ?>
<?PHP    }//if sp end?>

<?PHP
  }
}//json if?>

<?php
require('base.php');
?>
    <script type="text/javascript" src="common/js/base.js"></script>
<?php if((isset($_POST['page']) and $_POST['page']==="1")){ ?>
    <script type="text/javascript" src="common/js/good_count_ini_p.js"></script>
<?php    if(isset($_POST['location']) and $_POST['location']=="count_plus"):
          $goodCountData = $_POST['goodCountData'];
          $location ="count_plus";
         else:
            //詳細ページに遷移時
            if( preg_grep("/_/", $_POST)){
            $goodCountData = preg_grep("/_/", $_POST);
            }
          $location ="detail";
         endif;
         $goodCountData['page']  = "1";
      }?>

<?php if(isset($_POST['page']) and $_POST['page']>"1"){  ?>
    <script type="text/javascript" src="common/js/good_count_ini_p.js"></script>
<?php     if(isset($_POST['location']) and $_POST['location']=="count_plus"):
            $goodCountData = $_POST['goodCountData'];
            $location ="count_plus";
          else:
              //ページ移動(アップ)に遷移時
              if( preg_grep("/_/", $_POST)){
              $goodCountData = preg_grep("/_/", $_POST);
              }
          $location ="page_change";
          endif;
       }?>

<section id="contents">
  <span id="h_cat_no"><?php if ( isset ($_POST['cat_no']) ) { echo $_POST['cat_no']; } ?></span>
  <span id="h_cat_name"><?php if ( isset ($_POST['cat_name']) ) { echo $_POST['cat_name']; } ?></span>
  <span id="h_thr_no"><?php if ( isset ($_POST['thr_no']) ) { echo $_POST['thr_no']; } ?></span>
  <span id="h_page"><?php if ( isset ($_POST['page']) ) { echo $_POST['page']; }else{ echo "1"; } ?></span>
  <span id="h_next_no"><?php if ( isset ($_POST['next_no']) ) { echo $_POST['next_no']; }else{ echo "1"; } ?></span>
  <span id="h_pagetype"><?php if ( isset ($_POST['pagetype']) ) { echo $_POST['pagetype']; }else{ echo "1"; } ?></span>

<?php
//DB参照版の処理
if(isset ($_POST['iotype'])){
  if($_POST['iotype']=='db'):
    $iotype = "db";
  elseif($_POST['iotype']=='json'):
    $iotype = "json";
  endif;
}

if(isset ($goodCountData['iotype'])){
  if($goodCountData['iotype']=='db'):
    $iotype = "db";
  elseif($goodCountData['iotype']=='json'):
    $iotype = "json";
  endif;
}

if($iotype =='db'){
    $board = new Board();

/**表示データの作成  **/
  //指定のカテゴリ、スレッドを取得
if(isset ($goodCountData['thr_no'])){
  $posts_puts = $board->get_thread_Post($goodCountData['thr_no']);
}elseif(isset ($_POST['thr_no'])){
  $posts_puts = $board->get_thread_Post($_POST['thr_no']);
}

  //登録済み投稿数をカウント
     $post_num = count($posts_puts);
  //最大ページ数を算出
     $max_page = ceil($post_num / ARTICLE_MAX_NUM);
  if(!isset($_POST['page'])){
  //初期化（初めて訪れた時にはpageが設定されていない）
      $now_page = FIRST_VISIT_PAGE;
  }else if(preg_match("/^[1-9][0-9]*$/",$_POST['page'])){
  //有効な数値かチェックして、念のためエスケープして$_POSTを格納する
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

   if(!isset($_POST['next_no']) or $_POST['next_no'] == "1" ){
    $page_no_result = page_no_calculation("0",$output_count,"0");
   }else{
    $page_no_result = page_no_calculation($_POST['next_no'],$output_count,$_POST['pagetype']);
   }
    $start_count = $page_no_result['start_count'] ;
    $end_count = $page_no_result['end_count'];

if(empty($output)){
      echo "記事はありません<br>";
}else{?>

  <?php if(agent_type()=="pc"){ ?>
    <?php include(__DIR__.'/temp/thread-detail_DB_PC.php'); ?>
  <?php    }//if agent pc end?>
  <?php if(agent_type()=="sp"){ ?>
    <?php include(__DIR__.'/temp/thread-detail_DB_SP.php'); ?>
  <?PHP }//if agent sp end?>
<?php 
}//output if
  
} //DB if のEND?>

<?php
if($iotype =='json'){

  $JSON_all =  JSON_load();

  if(!empty($_POST['cat_no'])){
   $cat_no = $_POST['cat_no'];
  }else if(!empty($_POST['cat_name'])){
   $cat_no = cat_no_get($_POST['cat_name']);
 }
/**表示データの作成  **/

  $cat_name = cat_name_get($cat_no);

  $thr_no = $_POST['thr_no'];

  $thr_info = get_thread_info_JSON($JSON_all,$cat_no,$_POST['thr_no']);

  $post_work = get_thread_Post_JSON($JSON_all,$cat_no,$_POST['thr_no']);

  $post_num = get_post_count_JSON($JSON_all,$cat_no,$_POST['thr_no']);
  //最大ページ数を算出
  $max_page = ceil($post_num / ARTICLE_MAX_NUM);

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
  /** 記事表示 **/
  //表示対象ページ数から、表示する記事Noを算出
  $start = ($now_page - 1) * ARTICLE_MAX_NUM;
  //$start番目からARTICLE_MAX_NUM個の配列を取得

  $output = array_slice($post_work,$start,ARTICLE_MAX_NUM);
  $output_count =count($output);

   if(!isset($_POST['next_no']) or $_POST['next_no'] == "1" ){
    $page_no_result = page_no_calculation("0",$output_count,"0");
   }else{
    $page_no_result = page_no_calculation($_POST['next_no'],$output_count,$_POST['pagetype']);
   }
    $start_count = $page_no_result['start_count'] ;
    $end_count = $page_no_result['end_count'];
    
  if(empty($output)){
        echo "記事はありません<br>";
  }else{
    if(agent_type()=="pc"){ include(__DIR__.'/temp/thread-detail_JSON_PC.php');}
    if(agent_type()=="sp"){ include(__DIR__.'/temp/thread-detail_JSON_SP.php');}
  }//output
 
}//if json end
?>
</section>
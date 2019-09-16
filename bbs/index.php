<?php
require('base.php');
  //admin.html経由の場合は管理者クッキーを設定
  if(isset($_SERVER['HTTP_REFERER'])){
    $pos = strpos($_SERVER['HTTP_REFERER'],'admin.php');
  if($pos>0){setcookie("Admin", True);}
  } 
  if(!isset($_GET['mode'])){
    header("Location: /bbs/io_select.php");
    exit();
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <link rel="stylesheet" type="text/css" href="common/css/jquery-ui.css">
  <script type="text/javascript" src="common/js/jquery-3.3.1.min.js"></script>
  <script type="text/javascript" src="common/js/jquery.validationEngine-ja.js"></script>
  <script type="text/javascript" src="common/js/jquery.validationEngine.js"></script>
  <script type="text/javascript" src="common/js/jquery-ui.js"></script>
  <script type="text/javascript" src="common/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="common/js/base.js"></script>
<?php  if(agent_type()=="pc"){ ?><script type="text/javascript" src="common/js/good_count_ini.js"></script><?php }?>
<?php if($_GET['mode']=='db'){?><title>トリビアくん(DB)</title><?php }?>
<?php if($_GET['mode']=='json'){?><title>トリビアくん(json)</title><?php }?>
<meta name="viewport" content="width=device-width">
<?php  if(agent_type()=="sp"){?><meta name="viewport" content="width=device-width"><?php }?>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="cache-control" content="no-cache, must-revalidate, proxy-revalidate">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="MSSmartTagsPreventParsing" content="true">
  <link rel="stylesheet" type="text/css" href="common/css/layout.css">
<?php  if(agent_type()=="sp"){?>
  <link rel="stylesheet" type="text/css" href="common/css/layout_sp.css">
<?php }?>
  <link rel="stylesheet" type="text/css" href="common/css/bootstrap.css">
  <link href="common/css/font/css/open-iconic-bootstrap.css" rel="stylesheet">
  <link href="common/css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
</head>

<!-- 削除確認ダイアログ用タグ -->
<div id="dialog"></div>
<div class="wrapper">
<section id="header">
  <div class="title"><a href="index.php?mode=<?php echo $_GET['mode'];?>"><p>トリビアくん</p></a></div>
<?php  if(agent_type()=="pc"){?>
  <section class="search">
    <div class="subtitle">あなたが持っている雑学を共有しよう</div>
    <div class="searchform">
      <div class="search-bar">
        <div class="input-group">
          <div class="icon-search oi oi-magnifying-glass"></div>
            <input type="text" class="search-query span3 " placeholder="検索する" class="form-control" id="search_post" onkeypress="search_keycode()">
            <span class="input-group-btn">
            <div class="text-center">
              <button type="submit" id="search-button-ent" class="search-button btn btn-outline-success btn-sm">検索</button>
            </div>
            </span>
          </div>
        </div>
      </div>
  </section>

<?php }?>
<?php  if(agent_type()=="sp"){?>
  <section class="menucontainer">
    <div class="item newpost">新着</div>
    <div class="item search_sp">探す</div>
    <div class="item newthrpost">新規投稿</div>
    <div class="item manual">使い方</div>
  </section>
<?php }?>
</section>
<section id="contents">
  <?php 
    if((isset($_POST['page']) and $_POST['page']==="1")){
        if(isset($_POST['location']) and $_POST['location']=="count_plus"){
         $goodCountData = $_POST['goodCountData'];
         $location ="count_plus";
        }      
      //初回表示時
     }elseif ( isset ($_POST['goodCountData']) ) {
         $goodCountData = json_decode($_POST['goodCountData'],true);
      }
          $goodCountData['page']  = "1";
    ?>
<div id="main">
<?PHP
if( DEBUG ){
  if(isset($_COOKIE['Admin'])){echo "管理者モード"."<br>";}else{echo "一般ユーザーモード"."<br>";}
  echo "i/O:".$_GET['mode']."<br>";}
//DB参照版の処理
if($_GET['mode']=='db'){

  $board = new Board();
  //表示対象スレッド情報を取得
  $thr_puts_all = $board->getThread();
  //最新投稿を取得
  $post_update = $board->get_update_post();
  //カテゴリ毎登録数を取得
  $cat_count = $board->get_count_cat();

//for開始表示対象スレッド分実行
  for($thre_c = 0; $thre_c < count($thr_puts_all); $thre_c++){

    //表示対象スレッドの投稿を取得
    $thred_puts = $board->get_thread_Post($thr_puts_all[$thre_c]['thr_id']);

    $pos_idArray = array_column($thred_puts, 'pos_id');
    $pos_id_result = array_search('1', $pos_idArray);

    if(count($thred_puts)!=0):
        $thred_output['0'] = $thred_puts[$pos_id_result];

        if(count($thred_puts)<=10){
          $start = 0;
        }else{
          $start = (count($thred_puts)-1) - ARTICLE_MAX_NUM;
        }
        //現在のページ番号をGETで受け取る
        if(!isset($_GET['page'])){
            $now_page = FIRST_VISIT_PAGE;
        }else if(preg_match("/^[1-9][0-9]*$/",$_GET['page'])){
            $now_page = htmlspecialchars($_GET['page'],ENT_QUOTES,'UTF-8');
        }else{
            echo "pageが正しく設定されていなかったので".FIRST_VISIT_PAGE."に設定しました!<br>";
            $now_page = FIRST_VISIT_PAGE;
        }

        $thred_output = $thred_output + array_slice($thred_puts,$start,ARTICLE_MAX_NUM+1);

      if(agent_type()=="pc"){include(__DIR__.'/temp/index_DB_PC.php'); }
      if(agent_type()=="sp"){include(__DIR__.'/temp/index_DB_SP.php'); }
      unset($thred_output);
    endif;
  }//thrfor
}//DB if

if($_GET['mode']=='json'){

  $JSON_all =  JSON_load();

  $cat_count_all = get_cat_count_all_JSON($JSON_all);

  $thr_count_all = get_thr_count_all_JSON($JSON_all);

  $cat_count = get_cat_count_JSON($JSON_all,$cat_count_all,$thr_count_all);

  $thr_count = get_thr_count_JSON($JSON_all,$cat_count_all,$thr_count_all);

  $thr = get_thread_PostTop_JSON($JSON_all,$cat_count_all,$thr_count_all);

  $thr_update = get_thread_update_JSON($JSON_all,$cat_count_all,$thr_count_all);

  $side_thr_count = get_thread_update_count_JSON($JSON_all,$cat_count_all,$thr_count_all);
}

if($_GET['mode']=='json'){
  if($thr_count<THREAD_MAX_NUM){
    $OUTPUT_THREAD_COUNT=$thr_count;
  }else{
    $OUTPUT_THREAD_COUNT=THREAD_MAX_NUM;
  }
//表示対象スレッド数分繰り返す。★開始★
//Top、検索結果一覧、それぞれ変わる。
  for($thre_c = 0; $thre_c < $OUTPUT_THREAD_COUNT; $thre_c++){
    // 投稿データの作成
    $print_count = 0;
    if(!empty($posts)){unset($posts);}
    foreach($thr[$thre_c] as $key=>$value ){
    //投稿データをページング用に処理
      $posts[] = $value;
    }
      $output['0'] = $posts['9']['0'];
      $post_num = count($posts[9]);

    if(count($posts[9])<=10){
      $start = 0;
    }else{
      $start = (count($posts[9])-1) - ARTICLE_MAX_NUM;
    }

    if(!isset($_GET['page'])){
        $now_page = FIRST_VISIT_PAGE;
    }else if(preg_match("/^[1-9][0-9]*$/",$_GET['page'])){
        $now_page = htmlspecialchars($_GET['page'],ENT_QUOTES,'UTF-8');
    }else{
        echo "pageが正しく設定されていなかったので".FIRST_VISIT_PAGE."に設定しました!<br>";
        $now_page = FIRST_VISIT_PAGE;
    }
    /** 記事表示 **/
    //表示対象ページ数から、表示する記事Noを算出
    $output = $output + array_slice($posts[9],$start,ARTICLE_MAX_NUM+1);

    if(empty($output)){
        echo "記事はありません<br>";
    }else{
      $print_count = 0;
    if(agent_type()=="pc"){include(__DIR__.'/temp/index_JSON_PC.php'); }
    if(agent_type()=="sp"){include(__DIR__.'/temp/index_JSON_SP.php'); }
    }
  }//thr_for
}; //if json
?>
  <?PHP    if(agent_type()=="pc"){?>
        <div class="page_top_scroll">～ページ最上部に移動～</div>
  <?PHP              }////if pcのend?>
</div>

<?php
if($_GET['mode']=='json'){
?>
    <div id="sidebarbox">
        <div id="sidebar">
          <div id="newthread">
            <p>+新規投稿</p>
          </div>
          <div id="category">
             <p>カテゴリ</p>
            <?php for($side_cat_count=1;$side_cat_count<=CATEGORY_ENABLE_MAX;$side_cat_count++){ ?>
              <div class="catlist" thr_count_all= <?php echo $thr_count_all ?> start_no="1" cat_no= <?php echo $side_cat_count ?>><?php echo cat_name_get($side_cat_count);?>
              (<?php echo $cat_count[$side_cat_count] ?>)</div>
            <?php } ?>
          </div>
          <div id="new">
            <p>最近更新されたスレッド</p>
                <?php
                if($side_thr_count<THREAD_MAX_NUM){
                  $OUTPUT_THR_COUNT=$side_thr_count;
                }else{
                  $OUTPUT_THR_COUNT=THREAD_MAX_NUM;
                }

          for($thr_c = 1; $thr_c <= $OUTPUT_THR_COUNT; $thr_c++){
                 ?>
              <div class="thr_update" cat_no="<?php echo $thr_update[$thr_c-1]['cat_id'] ?>"
               thr_no="<?php echo $thr_update[$thr_c-1]['thr_id']?>" cat_name="<?php echo $thr_update[$thr_c-1]['cat_name']?>">
              [<?php echo $thr_update[$thr_c-1]['cat_name'];?>]
              <?php 
                if(strlen($thr_update[$thr_c-1]['thr_title'])==0){
                echo'**タイトルなし**';
                }else{
                echo $thr_update[$thr_c-1]['thr_title'];
                }
              ?>
                (<?php  echo $thr_update[$thr_c-1]['pos_count'];?>)
              </div>
           <?php } ?>
          </div>
        </div>
    </div>
    <!--sidebar-->
</section>
<!--section-->
<?php }elseif($_GET['mode']=='db'){?>
<div id="sidebarbox">
  <div id="sidebar">
    <div id="newthread">
      <p>+新規投稿</p>
    </div>
    <div id="category">
       <p>カテゴリ</p>
  <?php for($cat_c = 0; $cat_c < count($cat_count); $cat_c++){?>
      <div class="catlist" cat_no=<?php echo $cat_count[$cat_c]['cat_id']; ?>>
        <?php echo $cat_count[$cat_c]['cat_name']; ?>(<?php echo $cat_count[$cat_c]['cat_count'] ?>)
      </div>
  <?php } ?>

    </div>
    <div id="new">
      <p>最近更新されたスレッド</p>
      <?php
          //スレッドを最終更新日順に10個取得、スレッドの投稿数(表示対象)取得
          $thr_puts_all = $board->get_thread_PostCount();

        if(COUNT($thr_puts_all)<10){
          $OUTPUT_POST_COUNT=COUNT($thr_puts_all);
        }else{
          $OUTPUT_POST_COUNT=10;
        }
        for($post_c = 1; $post_c <= $OUTPUT_POST_COUNT; $post_c++){
      ?>
        <div class="thr_update" cat_no="<?php echo $thr_puts_all[$post_c-1]['cat_id'] ?>"
         thr_no="<?php echo $thr_puts_all[$post_c-1]['thr_id']?>">
        [<?php echo $thr_puts_all[$post_c-1]['cat_name'];?>]
        <?php 
          if(strlen($thr_puts_all[$post_c-1]['thr_title'])==0){

          }else{
          echo $thr_puts_all[$post_c-1]['thr_title'];
          }
        ?>
          (<?php  echo $thr_puts_all[$post_c-1]['sum_pos'];?>)
        </div>
     <?php } ?>
    </div><!--new-->
  </div><!--sidebar-->
</div><!--sidebarbox-->
</section><!--section-->
<?php   }; ?>
<footer>
<?PHP    if(agent_type()=="pc"){?>
  <section class="text">
    <div class="index"><a href="index.php?mode=<?php echo $_GET['mode'];?>">TOP</a></div> 
    <div class="manual">使い方</div>
    <div class="operating_company">運営会社</div>
    <div class="inquiry">問い合わせ</div>
  </section>
<?PHP    }//if pc?>
<?PHP    if(agent_type()=="sp"){?>
  <section class="text">
    <div class="index"><a href="index.php?mode=<?php echo $_GET['mode'];?>">TOP</a></div> 
    <div class="manual">使い方</div>
    <div class="operating_company">運営会社</div>
  </section>
  <section class="text">
    <div class="inquiry">問い合わせ</div>
  </section>
<?PHP    }//if sp?>
</footer><!--footer-->


</div><!--wrapper-->
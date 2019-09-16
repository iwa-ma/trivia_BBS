<?PHP
require(dirname(__FILE__).'/../base.php');
if($_GET['mode']=='db'){
  $host     = $DB_acces['host'];
  $username = $DB_acces['username'];
  $passwd   = $DB_acces['passwd'];
  $dbname   = $DB_acces['dbname'];
// 接続
  $link = new mysqli($host , $username, $passwd, $dbname);
  $link->set_charset('utf8');
  $cat_count_sql = $link->prepare( "SELECT c.cat_id,c.cat_name,COUNT(c.cat_id) cat_count FROM (thread t INNER JOIN category c ON
   t.cat_id = c.cat_id) WHERE t.thr_status = 1 GROUP BY c.cat_id;");
  $cat_count_sql->execute();
  $result = $cat_count_sql->get_result();
  $cat_count = $result->fetch_all(MYSQLI_ASSOC);
}elseif($_GET['mode']=='json'){
  $url = dirname(__FILE__)."/../json/thread-deta.json";
 //jsonファイルを読み込む
  $json = file_get_contents($url);
 //読み込んだ内容をUTF8に変換
  $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
 //Jsonで読み込んだ内容を連想配列に変換
  $arr = json_decode($json,true);
 //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');


 $thr_count_all =0; 
  for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
      if($f_cat_no==1){
        $cat_count_all =array(1 => count(preg_grep("/^thr_id_/",array_keys($root[0]['cat_id_1']))));
      }
      if($f_cat_no > 1){
        $cat_count_all = $cat_count_all + array($f_cat_no => count(preg_grep("/^thr_id_/",array_keys($root[0]['cat_id_'. $f_cat_no]))));
      }
     $thr_count_all =  $thr_count_all + count(preg_grep("/^thr_id_/",array_keys($root[0]['cat_id_'. $f_cat_no])));
  }

//スレッド数カウント用。
 $thr_count =0;
 $thr_work_count =0;
 $cat_start_no['1']=1;
//ここでカテゴリ毎に登録スレッド数(表示対象)を配列に格納
//thr_statusが1(削除されていない物)のみ配列に格納
  for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
    $cat_count[$f_cat_no]=0;
     if($cat_count_all[$f_cat_no] >0){
      for ($i = 1; $i <=  $cat_count_all[$f_cat_no] ; $i++) {
       //thr_status0の場合非表示
         $work_no = $i + $thr_work_count;
        if(isset($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no])):
            if($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no]['thr_status'] == 1){
              $thr[$thr_count] = $root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no];
              $thr_count++;
              //カテゴリ毎の件数を加算
              $cat_count[$f_cat_no]++;
            }
        endif;
       }
    }else{
      $cat_count[$f_cat_no]=0;
    }

    $thr_work_count = $thr_work_count + ($thr_count-$thr_work_count);
    $cat_start_no[$f_cat_no+1] = $thr_work_count+1;
  }
}
?>
  <script type="text/javascript" src="common/js/base.js"></script>
  <section class="searchform">
    <div class="input-group">
      <div class="search-bar">
        <div class="icon-search oi oi-magnifying-glass"></div>
        <input type="text" class="search-query span3" placeholder="検索する" class="form-control"  id="search_post"> 
        <span class="input-group-btn">
        <div class="text-center">
          <button type="button" class="search-button btn btn-outline-success btn-lg">検索</button>
        </div>
        </span>
      </div>
    </div>
  </section>

  <section class="categoryform">
    <div class="category-title">カテゴリ選択</div>
    <div class="category-select">
    <?PHP if($_GET['mode']=='db'){?>
      <?PHP for($cat_c=0;$cat_c<count($cat_count);$cat_c++){?>
        <div class="category-item" cat_no=<?php echo $cat_count[$cat_c]['cat_id']; ?>>
          <?php echo $cat_count[$cat_c]['cat_name']; ?>
        </div>
      <?PHP }?>
    <?PHP }?>
    <?PHP if($_GET['mode']=='json'){?>
      <?PHP for($cat_c=1;$cat_c<=CATEGORY_ENABLE_MAX;$cat_c++){?>
        <div class="category-item" thr_count_all= <?php echo $thr_count_all ?> cat_no=<?php echo $cat_c; ?>>
          <?php echo cat_name_get($cat_c); ?>
        </div>
      <?PHP }?>
    <?PHP }?>

    </div>
  </section>
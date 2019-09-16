<?php
  require('DB_config.php');

  define("DEBUG",FALSE);
//最大記事数（1ページにいくつの記事を表示するか）
  define("ARTICLE_MAX_NUM","10");
//最初のページ番号（初めて表示したときに何ページ目を表示するか）
  define("FIRST_VISIT_PAGE","1");
//最大スレッド数（1ページにいくつのスレッドを表示するか）
  define("THREAD_MAX_NUM","10");
//最新投稿数（最新投稿に何件表示するか）
  define("NEW_POSTED_MAX_NUM","10");

  define("json_url","json/thread-deta.json");

  mysqli_report(MYSQLI_REPORT_STRICT);

class MyDB{
  private $host     = host;
  private $username = username;
  private $passwd   = passwd;
  private $dbname   = dbname;

    protected function connect() {
      try {
        $this->dbh = new mysqli( $this -> host , $this-> username, $this-> passwd, $this-> dbname );
        $this->dbh->set_charset('utf8');
        //return $this->dbh;
      } catch(Exception $e) {
        error_log(print_r('Warning base.php::__construct() DBへの接続に失敗'."\n".$e->getMessage().' ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
      }
    }

}

class Board extends MyDB {
  function __construct() {
    ini_set("date.timezone", "Asia/Tokyo");
    //error_log(print_r('base.php::Board->__construct() DBへの接続 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
    $this->connect();
  }

  /*
  * 最終更新年月日順にスレッド情報を取得
  * 取得スレッド数はTHREAD_MAX_NUMの値
  * @return array
  */
  public function getThread() {
    $thr_puts_all_sql =  $this->dbh->prepare( "SELECT c.cat_id,c.cat_name,c.cat_status,t.thr_id,t.thr_title,t.thr_status,
      t.thr_lastupdate FROM thread t INNER JOIN category c ON t.cat_id = c.cat_id AND t.thr_status = 1 
      GROUP BY t.thr_id ORDER BY t.thr_lastupdate DESC LIMIT ? ");
    $Thr_MAX_NUM = THREAD_MAX_NUM;
    $thr_puts_all_sql->bind_param("i",$Thr_MAX_NUM);
    $thr_puts_all_sql->execute();
    $result = $thr_puts_all_sql->get_result();
    $thr_puts_all = $result->fetch_all(MYSQLI_ASSOC);

    return $thr_puts_all;
  }

  /*
  * 最新投稿を取得
  * 取得スレッド数はNEW_POSTED_MAX_NUMの値
  * @return array
  */
  public function get_update_post() {
    $post_update_sql = $this->dbh->prepare( "SELECT * FROM (posted p INNER JOIN thread t ON p.thr_id = t.thr_id)
    INNER JOIN category c ON t.cat_id = c.cat_id WHERE p.pos_status = 1 AND t.thr_status = 1
    ORDER BY p.pos_lastupdate DESC LIMIT ?");
    $NEW_POSTED_MAX_NUM = NEW_POSTED_MAX_NUM;
    $post_update_sql->bind_param("i", $NEW_POSTED_MAX_NUM);
    $post_update_sql->execute();
    $result = $post_update_sql->get_result();
    $post_update = $result->fetch_all(MYSQLI_ASSOC);

    return  $post_update;
  }

  /*
  * カテゴリ毎の登録数(status =1 表示対象)を取得
  * @return array
  */
  public function get_count_cat() {
    $cat_count_sql = $this->dbh->prepare( "SELECT c.cat_id,c.cat_name,COUNT(c.cat_id) cat_count FROM 
    thread t INNER JOIN category c ON t.cat_id = c.cat_id
    INNER JOIN
    (SELECT DISTINCT thr_id FROM posted) as p
    ON t.thr_id = p.thr_id
    WHERE t.thr_status = 1 GROUP BY c.cat_id");
    $cat_count_sql->execute();
    $result = $cat_count_sql->get_result();
    $cat_count = $result->fetch_all(MYSQLI_ASSOC);

    return  $cat_count;
  }

  /*
  * 表示対象スレッドの投稿を取得
  * @return array
  */
  public function get_thread_Post($thr_id) {
    $thred_puts_sql = $this->dbh->prepare( "SELECT * FROM (posted p INNER JOIN thread t ON p.thr_id = t.thr_id)
    INNER JOIN category c ON t.cat_id = c.cat_id AND p.thr_id = ? WHERE p.pos_status = 1 ORDER BY p.pos_id  ");
    $thred_puts_sql->bind_param("i",$thr_id);

      switch ($thred_puts_sql->execute()) {
      case true:
          $result = $thred_puts_sql->get_result();
          $thred_puts = $result->fetch_all(MYSQLI_ASSOC);

           return  $thred_puts;;
          break;
      case false:
        error_log(print_r('base.php::Board->get_thread_Post() スレッドの投稿取得 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
      }

  }

  /*
  * スレッドの情報、投稿数カウントを取得
  * @return array
  */
  public function get_thread_PostCount() {
    $thr_putsCount_all_sql = $this->dbh->prepare( "select * from (SELECT c.cat_id,c.cat_name,t.thr_id,t.thr_title,COUNT(p.pos_id)
    sum_pos,t.thr_lastupdate,t.thr_status FROM (posted p INNER JOIN thread t ON p.thr_id = t.thr_id)
    INNER JOIN category c ON t.cat_id = c.cat_id WHERE p.pos_status = 1 AND t.thr_status = 1
        GROUP BY t.thr_id ORDER BY t.thr_id  DESC LIMIT ?) sub   WHERE sub.thr_status = 1
        ORDER BY sub.thr_lastupdate DESC LIMIT ?");
    $Thr_MAX_NUM = THREAD_MAX_NUM;
    $thr_putsCount_all_sql->bind_param("ii",$Thr_MAX_NUM,$Thr_MAX_NUM);
    $thr_putsCount_all_sql->execute();
    $result = $thr_putsCount_all_sql->get_result();
    $thr_putsCount_all = $result->fetch_all(MYSQLI_ASSOC);
    return  $thr_putsCount_all;
  }

  /*
  * 指定カテゴリ名のNoを取得
  * @return int
  */
  public function get_cat_no($cat_name) {
     $cat_no_sql = $this->dbh->prepare( "SELECT cat_id FROM category WHERE cat_name = ?");
     $cat_no_sql ->bind_param("s",$cat_name);

      switch ($cat_no_sql->execute()) {
        case true:
           $cat_no_sql->bind_result($cat_no);
           $cat_no_sql->fetch();
            return  $cat_no;
          break;
      case false:
          return false;
          break;
       }
  }

  /*
  * 登録済みカテゴリno、nameを全て取得
  * @return array
  */
  public function get_cat_ALL() {
      $cat_puts_all_sql = "SELECT c.cat_id,c.cat_name FROM category c
      WHERE c.cat_status = 1
      ORDER BY c.cat_id ASC";
      $cat_puts_all_work = $this->dbh->query($cat_puts_all_sql);
      switch ($cat_puts_all_work) {
        case true:
         $cat_puts_all = mysqli_fetch_all($cat_puts_all_work ,MYSQLI_ASSOC);
            return $cat_puts_all;
          break;
      case false:
        error_log(print_r('base.php::Board->get_cat_ALL() カテゴリno、name取得 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
       }
  }

  /*
  * 指定カテゴリのスレッド取得(カテゴリ毎スレッド一覧)
  * @return array
  */
  public function get_cat_thread($cat_no) {
      $posts_puts_sql =  $this->dbh->prepare( "SELECT c.cat_name,t.thr_id,t.thr_title,t.thr_lastupdate,p.pos_count FROM 
    thread t INNER JOIN category c ON t.cat_id = c.cat_id
    INNER JOIN
    (SELECT thr_id,COUNT(pos_id) pos_count FROM posted GROUP by thr_id) as p
    ON t.thr_id = p.thr_id
    WHERE t.cat_id = ? AND t.thr_status = 1 ORDER BY t.thr_lastupdate DESC");

      $posts_puts_sql->bind_param("i",$cat_no);
      
      switch ($posts_puts_sql->execute()) {
        case true:
          $result = $posts_puts_sql->get_result();
          $posts_puts = $result->fetch_all(MYSQLI_ASSOC);
            return $posts_puts;
          break;
      case false:
        error_log(print_r('base.php::Board->get_cat_thread() カテゴリ毎スレッド取得 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
       }
  }

  /*
  * スレッド登録-threadテーブル
  *  @return int
  */
  public function new_thread_add_thread($cat_no,$thr_title,$now_time,$ipaddress,$ua,$new_id) {
    $thr_INSERT_sql = $this->dbh->prepare("INSERT INTO thread ( thr_id, cat_id, thr_status, thr_title,
     thr_created, thr_lastupdate , thr_ipaddress , thr_useragent , thr_posted_id)
      VALUES (NULL, ? , '1' , ? , ? , ? , ? , ? , ? )");
    $thr_INSERT_sql->bind_param("issssss",$cat_no,$thr_title,$now_time,$now_time,$ipaddress,$ua,$new_id);

      switch ($thr_INSERT_sql->execute()) {
      case true:
           return $this->dbh->insert_id;
          break;
      case false:
        error_log(print_r('base.php::Board->thread_add() 新規スレッド登録 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
      }
  }

  /*
  * スレッド登録-postedテーブル
  *  @return int
  */
  public function new_thread_add_posted($add_thr_id,$title,$now_time,$ipaddress,$ua,$new_id,$penname,$maintext,$password_st,$password_text) {
    //postedに追加
    $pos_INSERT_sql = $this->dbh->prepare( "INSERT INTO posted (id,thr_id,pos_id,pos_status,pos_title,pos_created,
      pos_lastupdate,pos_ipaddress,pos_useragent,pos_posted_id,pos_penname,pos_text,pos_good_count,
      pos_text_pass_st,pos_text_pass)VALUES (NULL, ? ,'1','1', ? , ? , ? , ? , ? , ? , ? , ? ,'0', ? , ? )");
    $pos_INSERT_sql->bind_param("issssssssis",$add_thr_id,$title,$now_time, $now_time,
    $ipaddress,$ua,$new_id,$penname,$maintext,$password_st,$password_text);

      switch ($pos_INSERT_sql->execute()) {
        case true:
          return true;
          break;
      case false:
          return false;
          break;
       }
  }

  /*
  * スレッドの最大投稿noを取得
  * @return int
  */
  public function get_maxpos_no($thr_id) {
    $max_pos_sql = $this->dbh->prepare( "SELECT COUNT(*) FROM `posted` WHERE `thr_id` = ?");
    $max_pos_sql->bind_param("i",$thr_id);

      switch ($max_pos_sql->execute()) {
        case true:
        $max_pos_sql->bind_result($max_pos_no);
        $max_pos_sql->fetch();
            return  $max_pos_no;
          break;
      case false:
          return false;
          break;
       }
  }

  /*
  * 投稿追加-postedテーブル
  *  @return int
  */
  public function add_post_add_posted($thr_id,$max_pos_no,$title,$now_time,$ipaddress,$ua,$new_id,$penname,$maintext,$password_st,$password_text) {
    //postedに追加
    $pos_INSERT_sql = $this->dbh->prepare( "INSERT INTO posted (id,thr_id,pos_id,pos_status,pos_title,pos_created,
      pos_lastupdate,pos_ipaddress,pos_useragent,pos_posted_id,pos_penname,pos_text,pos_good_count,
      pos_text_pass_st,pos_text_pass)VALUES (NULL, ? ,?,'1', ? , ? , ? , ? , ? , ? , ? , ? ,'0', ? , ? )");
    $pos_INSERT_sql->bind_param("iissssssssis",intval($thr_id),$max_pos_no,$title,
      $now_time,$now_time,$ipaddress,$ua,$new_id,$penname,$maintext,$password_st,$password_text);
      switch ($pos_INSERT_sql->execute()) {
        case false:
        error_log(print_r('base.php::Board->add_post__add_posted() 投稿追記 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
      }
  }

  /*
  * 投稿追加-threadテーブル
  *  @return int
  */
  public function add_post_add_thread($now_time,$thr_id) {
    $thr_UPDATE_sql = $this->dbh->prepare( "UPDATE  thread SET thr_lastupdate = ? WHERE  thr_id = ?");
    $thr_UPDATE_sql->bind_param("si",$now_time,intval($thr_id));
      switch ($thr_UPDATE_sql->execute()) {
        case true:
          break;
        case false:
       error_log(print_r('base.php::Board->add_post_add_thread() 投稿追記 ['.date('Y-m-d H:i:s', time()).']'."\n", true), '3', 'log.txt');
          exit;
          break;
      }
  }

  /*
  * スレッド削除
  *  @return bool
  */
  public function thread_del($thr_id) {
    $stmt = $this->dbh->prepare( "UPDATE thread SET thr_status = 0 WHERE thr_id = ?");
    $stmt->bind_param("i",$thr_id);

      switch ($stmt->execute()) {
        case true:
          return true;
          break;
      case false:
          return false;
          break;
       }
  }

  /*
  * 投稿削除
  *  @return bool
  */
  public function post_del($thr_id,$pos_id) {
    $stmt = $this->dbh->prepare( "UPDATE posted SET pos_status = 0 WHERE thr_id = ? AND pos_id = ? ");
    $stmt->bind_param("ii",$thr_id,$pos_id);
      switch ($stmt->execute()) {
        case true:
          return true;
          break;
      case false:
          return false;
          break;
       }
  }

  /*
  * 投稿のパスワードを取得
  * @return array
  */
  public function get_post_password($thr_id,$pos_id) {
    $stmt = $this->dbh->prepare( "SELECT pos_text_pass FROM posted WHERE thr_id = ? AND pos_id = ?");
    $stmt->bind_param("ii",$thr_id,$pos_id);
      switch ($stmt->execute()) {
        case true:
            $stmt->bind_result($password_hash);
            $stmt->fetch();
            return  $password_hash;
          break;
      case false:
          return false;
          break;
       }
  }

  /*
  * いいねカウントアップ
  * @return bool
  */
  public function good_count_up($thr_id,$pos_id) {
    $stmt = $this->dbh->prepare("UPDATE posted SET pos_good_count = pos_good_count + 1 WHERE thr_id = ? AND pos_id = ?");
    $stmt->bind_param("ii",$thr_id,$pos_id);

    switch ($stmt->execute()) {
      case true:
          return true;
        break;
    case false:
        return false;
        break;
     }
  }

  /*
  * 検索処理
  * @return array
  */
  public function search($search_key) {
    switch (strlen($search_key) == mb_strlen($search_key,'utf8')) {
      case TRUE:
      $pos_created_key =mb_convert_kana($search_key,'rnaskc');
            $posts_puts_sql = $this->dbh->prepare( "SELECT  c.cat_id,c.cat_name,t.thr_id,t.thr_title,t.thr_lastupdate,s.thr_poscount,p.pos_id,p.pos_text 
                FROM (category c INNER JOIN thread t ON c.cat_id = t.cat_id INNER JOIN posted p ON t.thr_id = p.thr_id
                AND (c.cat_name LIKE CONCAT('%',?,'%') OR t.thr_title LIKE CONCAT('%',?,'%') OR DATE_FORMAT(p.pos_created , '%Y年%m月%d日 %H:%i' ) LIKE CONCAT('%',?,'%') OR DATE_FORMAT(p.pos_created , '%Y年%c月%d日 %H:%i' ) LIKE CONCAT('%',?,'%') OR p.pos_penname LIKE CONCAT('%',?,'%') OR p.pos_title LIKE CONCAT('%',?,'%') OR p.pos_text LIKE CONCAT('%',?,'%')))
                inner join
                (SELECT t.thr_id,t.thr_title,COUNT(t.thr_id) thr_poscount,c.cat_id,c.cat_name FROM
                (posted p INNER JOIN thread t ON p.thr_id = t.thr_id)
                INNER JOIN category c ON t.cat_id = c.cat_id WHERE p.pos_status = 1 AND t.thr_status = 1 GROUP BY t.thr_id) as s
                on t.thr_id = s.thr_id");
        $posts_puts_sql->bind_param("sssssss",$search_key,$search_key,$search_key,$pos_created_key,$search_key,$search_key,$search_key);
        break;
      case FALSE:
        if(preg_match('/^[0-9０-９]+$/', $search_key)){
          $pos_created_key =mb_convert_kana($search_key,'rnaskc');
            $posts_puts_sql = $this->dbh->prepare( "SELECT  c.cat_id,c.cat_name,t.thr_id,t.thr_title,t.thr_lastupdate,s.thr_poscount,p.pos_id,p.pos_text 
                FROM (category c INNER JOIN thread t ON c.cat_id = t.cat_id INNER JOIN posted p ON t.thr_id = p.thr_id
                AND (c.cat_name LIKE CONCAT('%',?,'%') OR  t.thr_title LIKE CONCAT('%',?,'%') OR DATE_FORMAT(p.pos_created , '%Y年%m月%d日 %H:%i' ) LIKE CONCAT('%',?,'%') OR DATE_FORMAT(p.pos_created , '%Y年%c月%d日 %H:%i' ) LIKE CONCAT('%',?,'%') OR p.pos_penname LIKE CONCAT('%',?,'%')
                OR p.pos_title LIKE CONCAT('%',?,'%') OR p.pos_text LIKE CONCAT('%',?,'%')))
                inner join
                (SELECT t.thr_id,t.thr_title,COUNT(t.thr_id) thr_poscount,c.cat_id,c.cat_name FROM
                (posted p INNER JOIN thread t ON p.thr_id = t.thr_id)
                INNER JOIN category c ON t.cat_id = c.cat_id WHERE p.pos_status = 1 AND t.thr_status = 1 GROUP BY t.thr_id) as s
                on t.thr_id = s.thr_id");
        $posts_puts_sql->bind_param("sssssss",$search_key,$search_key,$search_key,$pos_created_key,$search_key,$search_key,$search_key);
        }else{
            $posts_puts_sql = $this->dbh->prepare( "SELECT  c.cat_id,c.cat_name,t.thr_id,t.thr_title,t.thr_lastupdate,s.thr_poscount,p.pos_id,p.pos_text
                FROM (category c INNER JOIN thread t ON c.cat_id = t.cat_id INNER JOIN posted p ON t.thr_id = p.thr_id
                AND (c.cat_name LIKE CONCAT('%',?,'%') OR t.thr_title LIKE CONCAT('%',?,'%') OR p.pos_penname LIKE CONCAT('%',?,'%') OR p.pos_title LIKE CONCAT('%',?,'%') OR p.pos_text LIKE CONCAT('%',?,'%') or DATE_FORMAT(p.pos_created , '%Y年%m月%d日 %H:%i' ) LIKE CONCAT('%',?,'%') OR DATE_FORMAT(p.pos_created , '%Y年%c月%d日 %H:%i' ) LIKE CONCAT('%',?,'%') ))
                inner join
                (SELECT t.thr_id,t.thr_title,COUNT(t.thr_id) thr_poscount,c.cat_id,c.cat_name FROM
                (posted p INNER JOIN thread t ON p.thr_id = t.thr_id)
                 INNER JOIN category c ON t.cat_id = c.cat_id WHERE p.pos_status = 1 AND t.thr_status = 1 GROUP BY t.thr_id) as s
                on t.thr_id = s.thr_id");;
        $posts_puts_sql->bind_param("sssssss",$search_key,$search_key,$search_key,$search_key,$search_key,$search_key,$search_key);
          }
        break;
    }
    switch ($posts_puts_sql->execute()) {
      case true:
        $result = $posts_puts_sql->get_result();
        $posts_puts = $result->fetch_all(MYSQLI_ASSOC);
          return $posts_puts;
        break;
      case false:
        return false;
        break;
     }

  }

}//class Board extends MyDB 

/*
* 検索処理(一致結果として追加)
*  @return　array
*/
function search_resuletAdd($posts_puts,$search_key,$serchkey_i) {
$result_no =0;
  //検索結果が1件もない場合に全投稿を検索
foreach($posts_puts as $key => $results_DB){
  if(empty($output_work)){
      $output_work[$result_no] = array('search_word'=>$search_key[$serchkey_i]);
      $output_work[$result_no] = $output_work[$result_no] +array('cat_id'=>$results_DB["cat_id"]);
      $output_work[$result_no] = $output_work[$result_no] +array('cat_name'=>$results_DB["cat_name"]);
      $output_work[$result_no] = $output_work[$result_no] +array('thr_id'=>$results_DB["thr_id"]);
      $output_work[$result_no] = $output_work[$result_no] +array('thr_title'=>$results_DB["thr_title"]);
      $output_work[$result_no] = $output_work[$result_no] +array('thr_lastupdate'=>$results_DB["thr_lastupdate"]);
      $output_work[$result_no] = $output_work[$result_no] +array('thr_poscount'=>$results_DB["thr_poscount"]);
      $output_work[$result_no] = $output_work[$result_no] +array('pos_id'=>$results_DB["pos_id"]);
      $output_work[$result_no] = $output_work[$result_no] +array('pos_text'=>$results_DB["pos_text"]);
      $result_no++;
  //キー検出結果1以上あり、
  }elseif(count($output_work)!=0 and empty($posts_puts) ===false){
  //ここでカテゴリ、スレッドNoをoutput_work比較
      $f_st=0;
  //表示対象格納用($output_work)と検索結果$posts_putsをforeachで比較
    foreach($output_work as $key => $results_put){
      //**現在比較対象の["cat_id"]["thr_id"]が$output_workに格納済みか確認
      //$output_worktに存在する(すでに他のキーワードで、対象スレッドが$output_workに存在)
      if($results_put["cat_id"]==$results_DB["cat_id"] and $results_put["thr_id"]==$results_DB["thr_id"] and $f_st==0){
      //$output_workに存在しない(他のキーワードで、対象スレッドが$output_workに格納されていない)
      }elseif(($results_put["cat_id"] != $results_DB["cat_id"] or $results_put["thr_id"] != $results_DB["thr_id"]) and $f_st==0){
        $f_st=1;
      }elseif($results_put["cat_id"]==$results_DB["cat_id"] and $results_put["thr_id"]==$results_DB["thr_id"] and $f_st==1){
        $f_st=0;
      }//results_put_out_if END
    }//***$output_work_foreach_end

    switch ($f_st) {
      case 1:
          $output_work[$result_no] = array('search_word'=>$search_key[$serchkey_i]);
          $output_work[$result_no] = $output_work[$result_no] +array('cat_id'=>$results_DB["cat_id"]);
          $output_work[$result_no] = $output_work[$result_no] +array('cat_name'=>$results_DB["cat_name"]);
          $output_work[$result_no] = $output_work[$result_no] +array('thr_id'=>$results_DB["thr_id"]);
          $output_work[$result_no] = $output_work[$result_no] +array('thr_title'=>$results_DB["thr_title"]);
          $output_work[$result_no] = $output_work[$result_no] +array('thr_lastupdate'=>$results_DB["thr_lastupdate"]);
          $output_work[$result_no] = $output_work[$result_no] +array('thr_poscount'=>$results_DB["thr_poscount"]);
          $output_work[$result_no] = $output_work[$result_no] +array('pos_id'=>$results_DB["pos_id"]);
          $output_work[$result_no] = $output_work[$result_no] +array('pos_text'=>$results_DB["pos_text"]);
          $result_no++;
        break;
      case 0:
        break;
    }
  }//empty_if END
} //***$posts_puts_foreach_end

return $output_work ;
}

/*
*
* 検索処理(一致結果として追加) キーワード2目以降
*  @return array or bool
*/
function search_result($posts_puts,$output_work,$search_key,$serchkey_i) { 
//$output_workに登録している[cat_id]and[thr_id]のスレッドを検索
//タイトル、日時、ペンネーム、投稿内容をキーワードで検索
foreach($posts_puts as $key => $results_DB){
    $output_work_no = 0;
    foreach($output_work as $key => $results_put){
    //**現在比較対象の["cat_id"]["thr_id"]が$output_workに格納済みか確認
    //$output_worktに存在する(すでに他のキーワードで、対象スレッドが$output_workに存在)
       if(isset($output_work[$output_work_no]["search_word"]) and $results_put["thr_id"]==$results_DB["thr_id"]){
        //thr_idが同じでoutput_work[$output_work_no]["search_word"]が存在していたら、追記するか判断。
        //$output_work[$key]["search_word"]の追記を配列にいれてカウント

        $output_work_arr = explode(",",$output_work[$output_work_no]["search_word"]);
        $comparison_work_no = count($output_work_arr);
        $comparison_work_no = $comparison_work_no -1;

        $output_work_arr = explode(",",$output_work[$output_work_no]["search_word"]);

        if(strcmp($output_work_arr[$comparison_work_no], $search_key[$serchkey_i])==0 ){
        }else{
          $output_work[$output_work_no]["search_word"] = $output_work[$output_work_no]["search_word"].",".$search_key[$serchkey_i];

            $output_work_arr = explode(",",$output_work[$output_work_no]["search_word"]);
        }
         $output_work_no++;
      }elseif($results_put["thr_id"]!=$results_DB["thr_id"]){
        $output_work_no++;
      }//results_put_if END


  }//***$output_work_foreach_end
    unset($results_put);
} //***$posts_puts_foreach_end
  unset($results_DB);
  unset($results_put);

  return $output_work;
}

  /*
  * JSONファイルを読み込む　JSON
  * @return array
  */

function JSON_load(){
  //jsonファイルを読み込む
  $json = file_get_contents(json_url);
  //読み込んだ内容をUTF8に変換
  $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
  //Jsonで読み込んだ内容を連想配列に変換
  $arr = json_decode($json,true);

  return $arr;
}


/*
* カテゴリ毎のスレッド登録数(削除含むを全て取得)　JSON
* @return array
*/
function get_cat_count_all_JSON($arr) {
  //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');
  //各カテゴリの登録済み件数（削除含む）を各要素（カテゴリIDに対応）に代入。 
  for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
      if($f_cat_no==1){
        $cat_count_all =array(1 => count(preg_grep("/^thr_id_/",array_keys($root[0]['cat_id_1']))));
      }
      if($f_cat_no > 1){
        $cat_count_all = $cat_count_all + array($f_cat_no => count(preg_grep("/^thr_id_/",array_keys($root[0]['cat_id_'. $f_cat_no]))));
      }
  }
  return $cat_count_all;
}

/*
* スレッド登録数(削除含むを全て取得)　JSON
* @return int
*/
function get_thr_count_all_JSON($arr) {
  //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');
  //各カテゴリの登録済み件数（削除含む）を各要素（カテゴリIDに対応）に代入。
  $thr_count_all =0; 
  for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
     $thr_count_all =  $thr_count_all + count(preg_grep("/^thr_id_/",array_keys($root[0]['cat_id_'. $f_cat_no])));
  }
  return $thr_count_all;
}


/*
* スレッド登録数(削除含むを全て取得)　JSON
* @return int
*/
function get_post_all_JSON($arr,$cat_count_all,$thr_count_all) {
  //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');
//post数カウント用　10以下の場合処理分けるのに使用。
  $post_count =0;

//ここで登録投稿(表示対象)を配列に格納
  //カテゴリ毎に実行
  for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {  
  //カテゴリ登録済み件数が0より大きい時
     if($cat_count_all[$f_cat_no] >0){
  //カテゴリ毎にスレッド数分実行
      for ($thr_i = 1; $thr_i <= $thr_count_all+1 ; $thr_i++) {
        $work_no = $thr_i;
        if(isset($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no])):
    //スレッド(thr_statusが1)が存在している場合  
          if($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['thr_status'] == 1){
    //投稿が存在している場合に実行
    //スレッド毎に投稿済み数をカウントして、その回数分比較を実行

              for ($pos_i = 1; $pos_i <= count($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['posted']) ; $pos_i++) {
               if((in_array("pos_id",array_keys($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['posted'][$pos_i-1])) == TRUE) and ($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['posted'][$pos_i-1]['pos_status'] ==1)){
              //$post_update配列を宣言、比較対象カテゴリ+スレッドNo、投稿内容を格納
              //カテゴリNo、スレッドNoは新規で追加、jsonのデータ構造で[posted]配下には項目がないため。
                $post_update[$post_count]=$root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['posted'][$pos_i-1];
                $post_update[$post_count]=$post_update[$post_count] +array('cat_id'=>$f_cat_no);
                $post_update[$post_count]=$post_update[$post_count] +array('cat_name'=>$root[0]['cat_id_'.$f_cat_no]['cat_name']);
                $post_update[$post_count]=$post_update[$post_count] +array('thr_id'=>$work_no);
                $post_update[$post_count]=$post_update[$post_count] +array('thr_lastupdate'=>$root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['thr_lastupdate']);
                $post_update[$post_count]=$post_update[$post_count] +array('thr_poscount'=>count($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['posted']));
              // post毎の件数を加算
                $post_count++;
                }
              }
           //$thr_count++;
          }
        endif;
      }
    }
  } 

  //**$post_update(表示対象投稿データ格納)を並べ替え**
  foreach($post_update as $key => $value){
   $post_lastupdate[$key] = $value["pos_lastupdate"];
  }
  array_multisort($post_lastupdate, SORT_DESC, $post_update);

  unset($post_lastupdate);

  return $post_update;
}

/*
* 総スレッド登録数(表示対象のみ取得)　JSON
* @return int
*/
function get_thr_count_JSON($arr,$cat_count_all,$thr_count_all) {
  //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');
  //スレッド数カウント用。
   $thr_count =0;
  //ここでカテゴリ毎に登録スレッド数(表示対象)を配列に格納
  //thr_statusが1(削除されていない物)のみ配列に格納
    for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
     // $cat_count[$f_cat_no]=0;
       if($cat_count_all[$f_cat_no] >0){
        for ($work_no = 1; $work_no <=  $thr_count_all+1 ; $work_no++) {
         //thr_status0の場合非表示
          if(isset($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no])):
              if($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no]['thr_status'] == 1){
                $thr[$thr_count] = $root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no];
                $thr_count++;
              }
          endif;
         }
      }
    }
  return $thr_count;
}

/*
* カテゴリ毎のスレッド登録数(表示対象のみ取得)　JSON
* @return array
*/
function get_cat_count_JSON($arr,$cat_count_all,$thr_count_all) {
  //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');

   $cat_start_no['1']=1;
  //ここでカテゴリ毎に登録スレッド数(表示対象)を配列に格納
  //thr_statusが1(削除されていない物)のみ配列に格納
    for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
      $cat_count[$f_cat_no]=0;
       if($cat_count_all[$f_cat_no] >0){
        for ($work_no = 1; $work_no <=  $thr_count_all+1 ; $work_no++) {
         //thr_status0の場合非表示
          if(isset($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no])):
              if($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no]['thr_status'] == 1){
                //カテゴリ毎の件数を加算
                $cat_count[$f_cat_no]++;
              }
          endif;
         }
      }else{
        $cat_count[$f_cat_no]=0;
      }
    }

  return $cat_count;
}

/*
* 表示対象スレッドの情報を取得　JSON
* @return array
*/
function get_thread_PostTop_JSON($arr,$cat_count_all,$thr_count_all) {
   //配列をカテゴリカラムで読み込み。
    $root= array_column($arr,'cat');

  //スレッド数カウント用。
   $thr_count =0;
  //ここでカテゴリ毎に登録スレッド数(表示対象)を配列に格納
  //thr_statusが1(削除されていない物)のみ配列に格納
    for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
       if($cat_count_all[$f_cat_no] >0){
        for ($work_no = 1; $work_no <=  $thr_count_all+1 ; $work_no++) {
         //thr_status0の場合非表示
          if(isset($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no])):
              if($root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no]['thr_status'] == 1){
                $thr[$thr_count] = $root[0]['cat_id_'.$f_cat_no]['thr_id_'. $work_no];
                $thr_count++;
              }
          endif;
         }
      }
    }
  //表示対象配列→$thrを並べ替え
   foreach($thr as $key => $value){
   $thr_lastupdate[$key] = $value["thr_lastupdate"];
  }

  array_multisort($thr_lastupdate, SORT_DESC, $thr);
  unset($thr_lastupdate);
  return $thr;
}

/*
* 対象スレッドの情報を取得　JSON
* @return array
*/
function get_thread_info_JSON($arr,$cat_id,$thr_id) {
  //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');

  return $root[0]['cat_id_'.$cat_id]['thr_id_'. $thr_id];
}

/*
* 最新更新スレッド数を取得　JSON
* @return array
*/
function get_thread_update_count_JSON($arr,$cat_count_all,$thr_count_all) {
  //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');

//スレッドカウント用　10以下の場合処理分けるのに使用。
  $side_thr_count =0;
  //カテゴリ毎に実行(サイドバーの表示用)
  for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
  //カテゴリ登録済み件数が0より大き場合に実行。
     if($cat_count_all[$f_cat_no] >0){
  //カテゴリ毎にスレッド数分実行
      for ($thr_i = 1; $thr_i <= $thr_count_all ; $thr_i++) {
        $work_no = $thr_i;
       if(isset($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no])):
        if($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['thr_status'] == 1){
            $side_thr_count++;
        }
       endif;
      }
    }
  } 
    return $side_thr_count;
}

/*
* 最新更新スレッドを取得　JSON
* @return array
*/
function get_thread_update_JSON($arr,$cat_count_all,$thr_count_all) {
  $root= array_column($arr,'cat');

//スレッドカウント用　10以下の場合処理分けるのに使用。
  $side_thr_count =0;
  //カテゴリ毎に実行(サイドバーの表示用)
  for($f_cat_no =1; $f_cat_no <= CATEGORY_ENABLE_MAX; $f_cat_no++) {
  //カテゴリ登録済み件数が0より大き場合に実行。
     if($cat_count_all[$f_cat_no] >0){
  //カテゴリ毎にスレッド数分実行
      for ($thr_i = 1; $thr_i <= $thr_count_all ; $thr_i++) {
        $work_no = $thr_i;

       if(isset($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no])):
        if($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['thr_status'] == 1){
              $thr_update[$side_thr_count]=array('thr_id'=> $root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['thr_id']);
              $thr_update[$side_thr_count]=$thr_update[$side_thr_count] + array('cat_name'=> $root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['cat_name']);
              $thr_update[$side_thr_count]=$thr_update[$side_thr_count] + array('thr_title'=> $root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['thr_title']);
              $thr_update[$side_thr_count]=$thr_update[$side_thr_count] +array('thr_lastupdate'=> $root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['thr_lastupdate']);
              $thr_update[$side_thr_count]=$thr_update[$side_thr_count] +array('cat_id'=>$f_cat_no);
              $thr_update[$side_thr_count]=$thr_update[$side_thr_count] +array('thr_id'=>$work_no);
              $thr_update[$side_thr_count]=$thr_update[$side_thr_count] +array('pos_count'=>0);
  //投稿をカウント
          for ($pos_st_count = 0; $pos_st_count < count($root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['posted']) ; $pos_st_count++) {
              $post_st_count_work =$root[0]['cat_id_'.$f_cat_no]['thr_id_'.$work_no]['posted'][$pos_st_count]['pos_status'];
                switch ($post_st_count_work) {
                  case "1":
                    $thr_update[$side_thr_count]['pos_count']++;
                    break;
                 case "0":
                    break;
                }
          }
            $side_thr_count++;
        }
       endif;
      }
    }
  } 

  if(isset($thr_update)):
     foreach($thr_update as $key => $value){
      $thr_lastupdate[$key] = $value["thr_lastupdate"];
     }
    array_multisort($thr_lastupdate, SORT_DESC, $thr_update);
    return $thr_update;
  else :
    return false;
  endif;
}

  /*
  * 表示対象スレッドの投稿を取得 JSON
  * @return array
  */
 function get_thread_Post_JSON($arr,$cat_no,$thr_no) {
   $root= array_column($arr,'cat');

    $posts[] = $root[0]['cat_id_'.$cat_no]['thr_id_'.$thr_no];
     if(count($posts[0]['posted']) >0){
      for ($work_no = 0; $work_no <  count($posts[0]['posted']) ; $work_no++) {
            if($posts[0]['posted'][$work_no]['pos_status'] == 1){
                if(empty($post_work)){
                  $post_work[0] = $posts[0]['posted'][$work_no];
                }else{
                  array_push($post_work,$posts[0]['posted'][$work_no]);
                }
              //$post_num++;
            }
       }
    }
    return $post_work;
  }

/*
* スレッドの投稿登録数(表示対象のみ)　JSON
* @return int
*/
function get_post_count_JSON($arr,$cat_no,$thr_no) {
  //配列をカテゴリカラムで読み込み。
   $root= array_column($arr,'cat');

    $posts[] = $root[0]['cat_id_'.$cat_no]['thr_id_'.$thr_no];
  //登録済み投稿数をカウント
    $post_num = 0;

     if(count($posts[0]['posted']) >0){
      for ($work_no = 0; $work_no <  count($posts[0]['posted']) ; $work_no++) {
            if($posts[0]['posted'][$work_no]['pos_status'] == 1){
              $post_num++;
            }
       }
    }
    return $post_num;
}

  /*
  * スレッド登録-postedテーブル　JSON
  *  @return int
  */
function new_thread_add_posted_JSON($arr,$cat_no,$thr_id,$cat_name,$password,$title,$ipaddress,$penname,$maintext,$THREAD_NEW) {
      $cat_root= array_column($arr,'cat');
      $cat_root= array_column($cat_root,'cat_id_'.$cat_no);

   //新規登録するスレッド番号確認の為、スレッド数を取得
      if($THREAD_NEW == TRUE){
       $cat_root_countwork = array_column($arr,'cat');
       $max_thr_no = 0;
        for($no = 1;$no <= CATEGORY_ENABLE_MAX;$no++){
          $cat_root_work = array_column($cat_root_countwork,'cat_id_'.$no);
          $max_thr_no = $max_thr_no + (count(array_keys($cat_root_work[0]))-5);
        }
      }

  //登録先スレッドの最大値確認の為、スレッド単位で読み込み
      if($THREAD_NEW == FALSE){
      $cat_root_thr= array_column($cat_root,'thr_id_'.$thr_id);
      $max_pos_no=(count(array_keys($cat_root_thr[0]['posted'])));
      }

  //ユーザーエージェントに文字リストの単語を含む場合はTRUE、それ以外はFALSE
      $ua = agent_type();
  //ID生成
      $new_id = uniqid(mt_rand(1, 6));
  //カテゴリ名設定
      $cat_name = $cat_name;
  //パスワード項目処理
      switch (mb_strlen($password)!=0) {
        case true:
           $password_st =1;
           $password_text = password_hash($password, PASSWORD_DEFAULT);
          break;
      case false:
           $password_st =0;
           $password_text = "NULL";
          break;
      }
   //新規スレッド作成用に配列生成
   //要素名  'thr_id_'.$max_thr_no+1で指定。
      if($THREAD_NEW == TRUE){
        $reg_thr_array= array(
        'thr_id_'.($max_thr_no+1) => array(
          'thr_id' => ($max_thr_no+1),
          'thr_status' => 1,
          'cat_name'  => $cat_name,
          'thr_title'  => $title,
          'thr_created'  => date('YmdHi'),
          'thr_lastupdate' => date('YmdHi'),
          'thr_ipaddress' => $ipaddress,
          'thr_useragent' => $ua ,
          'thr_posted_id' => $new_id,
          'posted' => array(
              '0' => array(
              'pos_id' => '1',
              'pos_res_id' => '-1',
              'pos_status' => 1,
              'pos_title' => $title,
              'pos_created' => date('YmdHi'),
              'pos_lastupdate' => date('YmdHi'),
              'pos_ipaddress' => $ipaddress,
              'pos_useragent' => $ua,
              'pos_posted_id' => $new_id,
              'pos_penname' => $penname,
              'pos_text' => $maintext,
              'pos_good_count' => '0',
              'pos_text_pass_st' => $password_st,
              'pos_text_pass' => $password_text,
          )
         )
        )
       );
      }

       //スレッド追記用に配列生成
    if($THREAD_NEW == FALSE){
      $cat_root[0]['cat_updated']= date('YmdHi');
      $cat_root[0]['thr_id_'.$thr_id]['thr_lastupdate']= date('YmdHi');

      $cat_root[0]['thr_id_'.$thr_id]['posted'][($max_pos_no)] = array(
                  'pos_id' =>  ($max_pos_no+1),
                  'pos_res_id' => '1',
                  'pos_status' => 1,
                  'pos_title' => $title,
                  'pos_created' => date('YmdHi'),
                  'pos_lastupdate' => date('YmdHi'),
                  'pos_ipaddress' => $ipaddress,
                  'pos_useragent' => $ua,
                  'pos_posted_id' => $new_id,
                  'pos_penname' => $penname,
                  'pos_text' => $maintext,
                  'pos_good_count' => '0',
                  'pos_text_pass_st' => $password_st,
                  'pos_text_pass' => $password_text,
             );
    }
  $arr_before=$arr;

  if($THREAD_NEW == TRUE){
  $arr['data']['cat']['cat_id_'.$cat_no]['thr_id_'.($max_thr_no+1)]=$reg_thr_array['thr_id_'.($max_thr_no+1)];
  }

  if($THREAD_NEW == FALSE){
  $arr['data']['cat']['cat_id_'.$cat_no]=$cat_root[0];
  }

  //新規スレッドを追加した配列をJSON形式に変換
  $reg_thr_json =  json_encode($arr, JSON_UNESCAPED_UNICODE);

  //++++ ここで書き込み。
  $result = file_put_contents(json_url, $reg_thr_json);
  return $result;

}

  /*
  * 指定カテゴリのスレッド取得(カテゴリ毎スレッド一覧)　json
  * @return array
  */
function get_cat_thread_json($arr,$cat_no,$thr_count_all,$cat_count_all) {
  $thr_count =0;
  $root= array_column($arr,'cat');
  //ここでカテゴリ毎に登録スレッド数(表示対象)を配列に格納させたい。
     if($cat_count_all >0){
      for ($work_no = 1;  $work_no <= $thr_count_all+1 ; $work_no++) {
      if(isset($root[0]['cat_id_'.$_POST['cat_no']]['thr_id_'. $work_no])):
       //thr_status0の場合非表示
        if($root[0]['cat_id_'.$cat_no]['thr_id_'.$work_no]['thr_status'] == 1){
          $thr[$thr_count] = $root[0]['cat_id_'.$cat_no]['thr_id_'.$work_no];
          $thr_count++;
        }
       endif;
      }
    } 

  if($thr):
    return  $thr;
  else :
    return false;
  endif;
}

  /*
  * いいねカウントアップ JSON
  * @return bool
  */
 function good_count_up_JSON($arr,$cat_no,$thr_id,$pos_id) {
  $arr['data']['cat']['cat_id_'.$cat_no]['thr_id_'.$thr_id]['posted'][$pos_id-1]['pos_good_count']++;

  $reg_thr_json =  json_encode($arr, JSON_UNESCAPED_UNICODE);
  $result = file_put_contents(json_url, $reg_thr_json);

  switch ($result) {
    case true:
        return true;
      break;
  case false:
      return false;
      break;
   }
}

/*
* 投稿のパスワードを取得 JSON
* @return array
*/
function get_post_password_JSON($arr,$cat_no,$thr_id,$pos_id) {
   return strval($arr['data']['cat']['cat_id_'.$cat_no]['thr_id_'.$thr_id]['posted'][$pos_id-1]['pos_text_pass']);
}

/*
* 投稿削除 JSON
*  @return bool
*/
function post_del_JSON($arr,$cat_no,$thr_id,$pos_id) {
   $arr['data']['cat']['cat_id_'.$cat_no]['thr_id_'.$thr_id]['posted'][$pos_id-1]['pos_status']=0;
    if($pos_id==1){
     $arr['data']['cat']['cat_id_'.$cat_no]['thr_id_'.$thr_id]['thr_status']=0;
    }
   $reg_thr_json =  json_encode($arr, JSON_UNESCAPED_UNICODE);
   $result = file_put_contents(json_url, $reg_thr_json);
  switch ($result) {
    case true:
        return true;
      break;
  case false:
      return false;
      break;
   }
}

/*
* 検索処理
*  @return array or bool
*/
function search_all_JSON($post_update,$search_key,$serchkey_i,$pos_i,$result_no,$result =array()) {
 //カテゴリ名比較
 //一致した場合、配列要素を一番始めに追加だから場合分けいらない
  if(preg_match("/$search_key[$serchkey_i]/", $post_update[$pos_i]["cat_name"])){
    //対象キーワードが登録済みデータから検出。
    //カテゴリで検出フラグを設定
    $result[$result_no] = array('pos_category'=>1);
    $result[$result_no] = $result[$result_no]+array('cat_id'=>0);
    $result[$result_no] = $result[$result_no]+array('thr_id'=>0);
  }

  //投稿タイトル比較
  //一致した場合、配列要素を一番始めに追加だから場合分けいらない
  if(preg_match("/$search_key[$serchkey_i]/", $post_update[$pos_i]["pos_title"])){
    //対象キーワードが登録済みデータから検出。
    //タイトルで検出フラグを設定
    $result[$result_no] = array('pos_title'=>1);
    $result[$result_no] = $result[$result_no]+array('cat_id'=>0);
    $result[$result_no] = $result[$result_no]+array('thr_id'=>0);
  }

  //投稿日時比較
  if(preg_match("/$search_key[$serchkey_i]/", date('Y年m月d日H:i',strtotime($post_update[$pos_i]["pos_created"] ))) or
    preg_match("/$search_key[$serchkey_i]/", date('Y年n月d日H:i',strtotime($post_update[$pos_i]["pos_created"] ))) ){

    if( DEBUG ){  echo "投稿日時に存在します"."<BR>";
    echo $post_update[$pos_i]["cat_id"]."-".$post_update[$pos_i]["thr_id"]."-".$post_update[$pos_i]["pos_id"]."<BR>";}
      //すでに他項目で検出されているか確認($result[添字]の有無)
      if(empty($result[$result_no])){
      //投稿日時で検出フラグを設定
        $result[$result_no] = array('pos_created'=>1);
        $result[$result_no] = $result[$result_no]+array('cat_id'=>0);
        $result[$result_no] = $result[$result_no]+array('thr_id'=>0);
      }else{
        //投稿日時で検出フラグを追加設定
        $result[$result_no] = $result[$result_no] +array('pos_created'=>1); 
        $result[$result_no] = $result[$result_no] +array('cat_id'=>0);
        $result[$result_no] = $result[$result_no] +array('thr_id'=>0);
      }

  }
  //ペンネーム比較
  if(preg_match("/$search_key[$serchkey_i]/", $post_update[$pos_i]["pos_penname"])){
    //すでに他項目で検出されているか確認($result[添字]の有無)
    if(empty($result[$result_no])){
    //ペンネームで検出フラグを設定
      $result[$result_no] = array('pos_penname'=>1);
      $result[$result_no] = $result[$result_no]+array('cat_id'=>0);
      $result[$result_no] = $result[$result_no]+array('thr_id'=>0);
    }else{
    //ペンネームで検出フラグを追加設定
      $result[$result_no] = $result[$result_no] +array('pos_penname'=>1);
      $result[$result_no] = $result[$result_no] +array('cat_id'=>0);
      $result[$result_no] = $result[$result_no] +array('thr_id'=>0);
    }
  }
  //投稿内容比較
  if(preg_match("/$search_key[$serchkey_i]/", $post_update[$pos_i]["pos_text"])){
    //すでに他項目で検出されているか確認($result[添字]の有無)
    if(empty($result[$result_no])){
    //投稿内容で検出フラグを設定
      $result[$result_no] = array('pos_text'=>1);
      $result[$result_no] = $result[$result_no] +array('cat_id'=>0);
      $result[$result_no] = $result[$result_no] +array('thr_id'=>0);
    }else{
    //投稿内容で検出フラグを追加設定
      $result[$result_no] = $result[$result_no] +array('pos_text'=>1);
      $result[$result_no] = $result[$result_no] +array('cat_id'=>0);
      $result[$result_no] = $result[$result_no] +array('thr_id'=>0);
    }
  }

  switch (isset($result[$result_no])) {
    case true:
        return $result;
      break;
  case false:
      $result[$result_no]= false;
      return $result;
      break;
   }
}

/*
* 検索処理(一致結果として追加)
*  @return bool
*/
function search_resuletAdd_JSON($post_update,$search_key,$serchkey_i,$pos_i,$result_no,$result =array()) {
    //ここでカテゴリ、スレッドNoを比較
      $f_st=0;
      $f_count=1;
      $result_count =count($result);
    //検索結果($result)をforeachで比較
    //$resultの"cat_id"、"thr_id"を$post_updateの"cat_id"、"thr_id"と比較
      foreach($result as $key => $result_work){
      //**現在比較対象[$pos_i]の["cat_id"]["thr_id"]が$resultに登録済みか確認
      //$resultに存在する(すでに他のキーワードで、対象スレッドが$resultに存在)
      //1回目はcat_id、　thr_idが存在しない

        if($result_work["cat_id"]==$post_update[$pos_i]["cat_id"] and $result_work["thr_id"]==$post_update[$pos_i]["thr_id"]
           and $f_st==0){
          unset($result[$result_no]);
          
          $f_st=1;
          return $result;
          break;
      //$resultに存在しない(他のキーワードで、対象スレッドが$resultに追加されていない)
         }elseif($result_work["cat_id"]!=$post_update[$pos_i]["cat_id"] and $result_work["thr_id"]!=$post_update[$pos_i]["thr_id"] 
            and $f_st==0){

        }//****if end
           $f_count++;
      }//***foreach_end

           if($f_st==0){
            $result[$result_no]['cat_id'] = $post_update[$pos_i]["cat_id"];
            $result[$result_no]['cat_name'] = $post_update[$pos_i]["cat_name"];
            $result[$result_no]['thr_id'] = $post_update[$pos_i]["thr_id"];
            $result[$result_no]['thr_lastupdate'] = $post_update[$pos_i]["thr_lastupdate"];
            $result[$result_no]['thr_poscount'] = $post_update[$pos_i]["thr_poscount"];
            $result[$result_no] = $result[$result_no] +array('pos_id'=>$post_update[$pos_i]["pos_id"]);
            $result[$result_no] = $result[$result_no] +array('search_word'=>$search_key[$serchkey_i]);

            $result_no++;
           }
        return $result;

/*  switch (isset($result)) {
    case true:
        return $result;
      break;
  case false:
      return false;
      break;
   }*/
}

/*
*
* 検索処理 キワード2目以降
*  @return array or bool
*/
function search_result_JSON($arr,$result,$search_key,$serchkey_i) {
    //配列をカテゴリカラムで読み込み。
  $root= array_column($arr,'cat');
   //resultに登録している[cat_id]and[thr_id]のスレッドを検索
    //タイトル、日時、ペンネーム、投稿内容をキーワードで検索
    $result_no = 0;

     foreach($result as $key => $val){
          $search_key_st =0;
          $post_count =0;
    //比較するスレッド、投稿を取ってくる。
    //対象スレッドの投稿数分実行、
      for ($pos_i = 1; $pos_i <= count($root[0]['cat_id_'.$val["cat_id"]]['thr_id_'.$val["thr_id"]]['posted']) ; $pos_i++) {
       if((in_array("pos_id",array_keys($root[0]['cat_id_'.$val["cat_id"]]['thr_id_'.$val["thr_id"]]['posted'][$pos_i-1])) == 1) and ($root[0]['cat_id_'.$val["cat_id"]]['thr_id_'.$val["thr_id"]]['posted'][$pos_i-1]['pos_status'] ==1)){
        $result_post[$post_count]=$root[0]['cat_id_'.$val["cat_id"]]['thr_id_'.$val["thr_id"]]['posted'][$pos_i-1];
        $result_post[$post_count]=$result_post[$post_count] +array('cat_id'=>$val["cat_id"]);
        $result_post[$post_count]=$result_post[$post_count] +array('cat_name'=>$val["cat_name"]);
        $result_post[$post_count]=$result_post[$post_count] +array('thr_id'=>$val["thr_id"]);
        $result_post[$post_count]=$result_post[$post_count] +array('thr_lastupdate'=>$val["thr_lastupdate"]);
        $result_post[$post_count]=$result_post[$post_count] +array('thr_poscount'=>$val["thr_poscount"]);

    //post毎の件数を加算
        $post_count++;
        }
      }

     // echo "<PRE>";
     // var_dump($result_post);
     // echo "</PRE>";
    // exit;

         for($pos_i = 0; $pos_i < $post_count  ; $pos_i++){
          //投稿カテゴリ名比較
           if(preg_match("/$search_key[$serchkey_i]/", $result_post[$pos_i]["cat_name"])){
            $search_key_st=1;
           }
            //投稿タイトル比較
           if(preg_match("/$search_key[$serchkey_i]/", $result_post[$pos_i]["pos_title"])){
            $search_key_st=1;
           }
            //投稿日時比較
           if(preg_match("/$search_key[$serchkey_i]/", date('Y年m月d日H:i',strtotime($result_post[$pos_i]["pos_created"] )))){
            $search_key_st=1;
           }
            //ペンネーム比較
           if(preg_match("/$search_key[$serchkey_i]/", $result_post[$pos_i]["pos_penname"])){
            $search_key_st=1;
           }
            //投稿内容比較
           if(preg_match("/$search_key[$serchkey_i]/", $result_post[$pos_i]["pos_text"])){
            $search_key_st=1;
           }
         }//forのend
    //2つ目以降のキーワードが、既存の検索結果から見つからなかったら、その結果を消す
       if($search_key_st == 0){
        array_splice($result, $result_no, 1);
      }else{
        $result[$result_no]["search_word"] = $result[$result_no]["search_word"].",".$search_key[$serchkey_i];
      }
    //$resutl配列を変更する場合のために、result_noもカウントアップさせる。
        if($search_key_st == 1){ $result_no++;}
    //次の比較のため、result_postを破棄
      unset($result_post);
    }//***foreach_end

  switch (is_countable($result)) {
    case true:
        return $result;
      break;
  case false:
      return false;
      break;
   }
}

function agent_type() {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  if (stripos($ua, 'iphone') !== false || // iphone
    stripos($ua, 'ipod') !== false || // ipod
    (stripos($ua, 'android') !== false && stripos($ua, 'mobile') !== false) || // android
    (stripos($ua, 'windows') !== false && stripos($ua, 'mobile') !== false) || // windows phone
    (stripos($ua, 'firefox') !== false && stripos($ua, 'mobile') !== false) || // firefox phone
    (stripos($ua, 'bb10') !== false && stripos($ua, 'mobile') !== false) || // blackberry 10
    (stripos($ua, 'blackberry') !== false) // blackberry
  ) {
    $ua = "sp";
  } else {
    $ua = "pc";
  }
  return $ua;
}

function page_no_calculation($next_no,$output_count,$pagetype){
  if($next_no=="0"){
    $result["start_count"] = "1";
    $result["end_count"] = $output_count;
    return $result;
  }elseif($pagetype=="next"){
    $result["start_count"] = $next_no;
    $result["end_count"] = (($next_no+$output_count)-1);
    return $result;
  }elseif($pagetype=="back"){
    $result["start_count"] = ($next_no-(ARTICLE_MAX_NUM-1));
    $result["end_count"] = $next_no;
    return $result;
  }

}

function good_state($good_array,$TargetId,$location) {
    if($location=="index" or $location=="count_plus"){
              if(array_search($TargetId,array_column($good_array, 'No'))===FALSE){
                return FALSE;
              }else{
                return TRUE;
              }
    }elseif($location=="detail" or $location=="page_change"){
              if(array_search($TargetId,preg_grep("/_/", $good_array))===FALSE){
                return FALSE;
              }else{
                return TRUE;
              }
    }
    
 }

function h_escape($s) {
return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

//**json版のみ使用**//
define("CATEGORY_ENABLE_MAX","5");
function cat_no_get($cat_name) {
    switch ($cat_name) {
    case "エンタメ":
       $cat_no = 1;
        break;
    case "時事ネタ":
       $cat_no = 2;
        break;
    case "天気":
       $cat_no = 3;
        break;
    case "海外":
       $cat_no = 4;
        break;
    case "国内":
       $cat_no = 5;
        break;
    }
  return $cat_no;
}
function cat_name_get($cat_no) {
    switch ($cat_no) {
    case "1":
       $cat_name = "エンタメ";
        break;
    case "2":
       $cat_name = "時事ネタ";
        break;
    case "3":
       $cat_name = "天気";
        break;
    case "4":
       $cat_name = "海外";
        break;
    case "5":
       $cat_name = "国内";
        break;
    default:
       $cat_name = "不明";
        break;
    }
  return $cat_name;
}
?>
<?PHP
    $post_count=0;
    $thr_created = strtotime($thr_info['thr_created']);
    foreach($output as $val){
    if($post_count==0){
        echo '<span id="pagination'.$_POST['thr_no'].'">';
?>
<?php if(isset($_COOKIE['Admin'])){echo "管理者モード";} ?>
<div id="dialog"></div>
        <section class="thread thread-container">
        <div class="breadcrumbs">ホーム→<?php echo $cat_name;?>→<?php echo h_escape($thr_info['thr_title']);?></div>
        <div class="thread-item-root thread-detail-root">
          <label class="thread-detail-item"><?php echo h_escape($thr_info['thr_title']);?> </label>
          <label class="thread-detail-item">[投稿数]<?php echo $post_num;?> </label>
          <label class="thread-detail-item">[スレッド作成]<?php echo date('Y年m月d日 H:i',$thr_created);?> </label>
          <label class="thread-detail-item" id="posted">コメントを投稿</label>
        </div>
<?php      if($max_page>1){?>
        <div class="paging">
          <div class="category_count_detail"><?php echo $post_num;?>件中<?php echo $start_count;?>～<?php echo $end_count;?>表示</div>
<?php if($now_page > 1){?>
          <div class="pager-back" location="detail" next_no=<?PHP echo ($start_count-1) ?> page=<?php echo $now_page-1 ?> thr_no=<?php echo $_POST['thr_no']?> cat_name=<?php echo $cat_name?>>
          <a href="javascript:void(0);"> <-前のページへ</a></div>
<?php               }//if($now_page > 1){?>
<?php if($now_page < $max_page){?>
          <div class="pager-next" location="detail" next_no=<?PHP echo ($start_count+$output_count) ?> page=<?php echo $now_page+1 ?> thr_no=<?php echo $_POST['thr_no'] ?> cat_name=<?php echo $cat_name?>>
            <a href="javascript:void(0);"> 次のページへ-></a></div>
<?php               }//if($now_page < $max_page){?>
        </div>
<?php               }//if($max_page>1){?>
        <div class="posted posted-container">
          <label class="posted-item-1">No.<?php echo $val['pos_id'];?> <?php echo h_escape($val['pos_penname']);?></label>
          <label class="posted-item-1"><?php echo date('Y年m月d日 H:i',strtotime($val['pos_created']));?></label>
          <label class="posted-item-1"><?php echo nl2br(h_escape($val['pos_text']));?></label>
                  <span class="good_count" id="good_<?php echo cat_no_get($cat_name);?>_<?php echo $thr_no;?>_<?php echo $val['pos_id'];?>">
                <?PHP if (  isset ($_POST['goodCountData']) ) :
                  $TargetId = cat_no_get($cat_name)."_".$thr_no."_".$val['pos_id'];
                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,$location)===TRUE: ?>
                    <div class="good_count_plus good_on" location="detail" cat_no="<?php echo $cat_no;?>" thr_no="<?php echo $thr_no;?>" pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $val['pos_good_count'];?></div>
                <?PHP break;
                    default: ?>
                    <div class="good_count_plus good_off" location="detail" cat_no="<?php echo $cat_no;?>" thr_no="<?php echo $thr_no;?>" pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $val['pos_good_count'];?></div>
                <?PHP  }
                else:
                    
                endif;?>
                  </span>
          <label class="posted-item-1">ID:<?php echo $val['pos_posted_id']; ?></label>
          <label class="posted-item-1">
            <form class="password" method="post" action="">
              <input type="text" name="password_<?php echo $thr_no;?>_<?php echo $val['pos_id'];?>"
              maxlength="30" value="" placeholder="30文字以内で入力">
              <input type="submit" id="<?php echo $cat_no;?>_<?php echo $thr_no;?>_<?php echo $val['pos_id'];?>"
                     value="削除" cat_name="<?php echo $cat_name;?>" 
                     thr_no="<?php echo $thr_no;?>" pos_no="<?php echo $val['pos_id'];?>"
                     admin="<?php if(isset($_COOKIE['Admin'])){echo "true";}else{echo "false";} ?>" class="password_del">
              <input type="reset" value="リセット">
            </form>
          </label>
        <?php if(isset($_COOKIE['Admin'])){?>
          <label class="posted-item-1">IPアドレス:<?php {echo $val['pos_ipaddress'];}?></label>
        <?php }//if cookie END ?>
        </div>

        <?php 
          }elseif($post_count!=0 && $val['pos_status']==1){
        ?>
        <div class="posted posted-container">
          <label class="posted-item">No.<?php echo $val['pos_id'];?> <?php echo h_escape($val['pos_penname']);?></label>
          <label class="posted-item"><?php echo date('Y年m月d日 H:i',strtotime($val['pos_created']));?></label>
          <label class="posted-item"><?php echo h_escape($val['pos_title']);?></label>
          <label class="posted-item"><?php echo nl2br(h_escape($val['pos_text']));?></label>
          <label class="posted-item">
                  <span class="good_count" id="good_<?php echo cat_no_get($cat_name);?>_<?php echo $thr_no;?>_<?php echo $val['pos_id'];?>">
                <?PHP if (  isset ($_POST['goodCountData']) ) :
                  $TargetId = cat_no_get($cat_name)."_".$thr_no."_".$val['pos_id'];
                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,$location)===TRUE: ?>
                    <div class="good_count_plus good_on" location="detail" cat_no="<?php echo $cat_no;?>" thr_no="<?php echo $thr_no;?>" pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $val['pos_good_count'];?></div>
                <?PHP break;
                    default: ?>
                    <div class="good_count_plus good_off" location="detail" cat_no="<?php echo $cat_no;?>" thr_no="<?php echo $thr_no;?>" pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $val['pos_good_count'];?></div>
                <?PHP  }
                else:
                    
                endif;?>
                  </span>
          </label>
          <label class="posted-item">ID:<?php echo $val['pos_posted_id']; ?></label>
          <label class="posted-item">
            <form class="password" method="post" action="">
              <input type="text" name="password_<?php echo $thr_no;?>_<?php echo $val['pos_id'];?>"
              maxlength="30" value="" placeholder="30文字以内で入力">
              <input type="submit" id="<?php echo $cat_no;?>_<?php echo $thr_no;?>_<?php echo $val['pos_id'];?>"
                     value="削除" cat_name="<?php echo $cat_name;?>" 
                     thr_no="<?php echo $thr_no;?>" pos_no="<?php echo $val['pos_id'];?>"
                     admin="<?php if(isset($_COOKIE['Admin'])){echo "true";}else{echo "false";} ?>" class="password_del">
              <input type="reset" value="リセット">
            </form>
          </label>
          <!-- 管理者用項目 -->
          <?php if(isset($_COOKIE['Admin'])){?>
          <label class="posted-item">IPアドレス:<?php {echo $val['pos_ipaddress'];}?></label>
          <?php } ?>
        </div>
        <?php 
          }elseif($post_count!=0 && $val['pos_status']==0){
        ?>

        <?php 
          }
        ?>
             <?php
        if($post_count==(ARTICLE_MAX_NUM-1) or
            ($post_num<=(ARTICLE_MAX_NUM-1) and ($post_count == $post_num-1)) or
            ($post_num>(ARTICLE_MAX_NUM-1) and (($post_count+$start) == $post_num-1))){
                  echo ' <div class="paging">';
                  echo  '<div class="category_count_detail">'.$post_num.'件中'.$start_count.'～'.$end_count.'表示</div>';
                  echo '<div class="page_top_scroll">～ページ最上部に移動～</div>';
              if($now_page > 1){
                  echo '<div class="pager-back" location="detail" next_no='.($start_count-1).' page='.($now_page-1).' thr_no='.$_POST['thr_no'].' cat_name='.$cat_name.'><a href="javascript:void(0);")> <-前のページへ</a></div>';
              }
              if($now_page < $max_page){
                  echo '<div class="pager-next" location="detail" next_no='.($start_count+$output_count).' page='.($now_page+1).' thr_no='.$_POST['thr_no'].' cat_name='.$cat_name.'><a href="javascript:void(0);")> 次のページへ-></a></div>';
              }
             ?>
          </div>
<div class="threadform">
  <form id="thread" method="post" name="add_post_form" action="">
     <label>コメントを投稿する</label>
    <div id="cat">
       <div id="category_id"><?php echo cat_no_get($cat_name);?></div>
       <div id="category">[<?php echo $cat_name;?>]</div>
       <div id="thr_id"><?php echo $thr_no;?></div>
       <div id="thread_title"><?php echo h_escape($thr_info['thr_title']);?></div>
       <div id="category_post"><?php echo $cat_name;?></div>
       <div id="ip_type">add</div>
    </div>
    <div id="title">
      <label>タイトル※必須入力</label>
      <input type="text" id="title_post" name="title" maxlength="30" size="60" value="" placeholder="50文字以内で入力"
      data-validation-engine="validate[required,maxSize[50]]" data-prompt-position="bottomRight:-100">
    </div>
    <div id="maintext">
      <label>投稿内容※必須入力</label>
      <textarea name="maintext"  id="maintext_post" rows="4" cols="40"
         rows="4" cols="30" placeholder="400文字以内で入力" data-validation-engine="validate[required,maxSize[400]]"
        data-prompt-position="bottomRight:-100"></textarea>
    </div>
    <p id="penname">
      <label>ペンネーム</label>
      <input type="text" name="penname" id="penname_post" maxlength="30" size="60" value="" placeholder="30文字以内で入力"data-validation-engine="validate[maxSize[30]]">
    </p>
    <p id="password">
      <label>パスワード</label>
      <input type="text" name="password" id="password_post" maxlength="30" size="60" value="" placeholder="30文字以内で入力"   data-validation-engine="validate[maxSize[30]]">
    </p>
  <input type="submit" value="送信内容確認" class="Confirmation">
  <input type="reset" value="リセット">
  </form>
</div>

<div class="confirmation_form">
  <form id="confirmation" method="post" action="registration.php">
      <label>投稿内容確認</label>
    <div id="cat">
     <div id="category_con"></div><div id="thread_title_con"></div>
   </div>
    <div id="title">
      <label>タイトル※必須入力</label>
      <div id="title_con"></div>
    </div>

    <label>投稿内容※必須入力</label>
    <div id="maintext_con"></div>

    <label>ペンネーム</label>
    <div id="penname_con"></div>

    <label>パスワード</label>
    <div id="password_con"></div>

    <input type="button" name="btn_back" value="戻る" class="Confirmation_back">
    <input type="submit" name="btn_submit" value="送信">
    <input type="hidden" id="thr_id_h" name="thr_id_h" value="">
    <input type="hidden" id="title_con_h" name="title_con_h" value="">
    <input type="hidden" id="maintext_con_h" name="maintext_con_h" value="">
    <input type="hidden" id="category_con_h" name="category_con_h" value="">
    <input type="hidden" id="penname_con_h" name="penname_con_h" value="">
    <input type="hidden" id="password_con_h" name="password_con_h" value="">
    <input type="hidden" id="iotype" name="iotype" value="<?php echo $_POST['iotype']; ?>">
 </form>
</div>
<?php 
                               }
                    $post_count++;
                  }
                   unset($output);
              echo ' </span>';
              echo '</section>';
              ?>
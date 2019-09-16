<?php 
    $post_count=0;
    foreach($output as $val){
      if($post_count==0){
      echo '<span id="pagination'.$_POST['thr_no'].'">';
?>
<?php if(isset($_COOKIE['Admin'])){echo "管理者モード";} ?>
<div id="dialog"></div>
             <?php
            if($max_page>1){
                  echo ' <div class="paging">';
                  echo  '<div class="category_count_detail">'.$post_num.'件中'.$start_count.'～'.$end_count.'表示</div>';
              if($now_page > 1){
                  echo '<div class="pager-back" location="detail" next_no='.($start_count-1).' page='.($now_page-1).' thr_no='.$_POST['thr_no'].'><a href="javascript:void(0);")> <-前のページへ</a></div>';
              }//if($now_page > 1){?>

             <?php
              if($now_page < $max_page){
                  echo '<div class="pager-next" location="detail" next_no='.($start_count+$output_count).' page='.($now_page+1).' thr_no='.$_POST['thr_no'].'><a href="javascript:void(0);")> 次のページへ-></a></div>';
              }//if($now_page < $max_page){}
                  echo ' </div>';//<div class="paging">
             }//if($max_page>1){
             ?>
        <section class="thread thread-container">
        <div class="thread-item-root">
          <label class="thread-item">[<?php echo $val['cat_name'];?>]No.<?php echo $val['thr_id'];?></label>
          <label class="thread-item"><?php echo h_escape($val['thr_title']);?> </label>
          <label class="thread-item" id="posted">投稿</label>
        </div>

        <div class="posted posted-container">
          <label class="posted-item-1">No.<?php echo $val['pos_id'];?> <?php echo h_escape($val['pos_penname']);?> <?php echo date('Y年m月d日 H:i',strtotime($val['pos_created']));?></label>
          <label class="posted-item-1"><?php echo nl2br(h_escape($val['pos_text']));?></label>
          <label class="posted-item-1">
            <form class="password" method="post" action="">
              <input type="text" name="password_<?php echo $val['thr_id'];?>_<?php echo $val['pos_id'];?>"
              maxlength="30" value="" placeholder="30文字以内で入力">
              <input type="submit" id="<?php echo $val['cat_id'];?>_<?php echo $val['thr_id'];?>_<?php echo $val['pos_id'];?>" 
                     value="削除" cat_name="<?php echo $val['cat_name'];?>"
                     thr_no="<?php echo $val['thr_id'];?>" pos_no="<?php echo $val['pos_id'];?>"
                     admin="<?php if(isset($_COOKIE['Admin'])){ if($_COOKIE['Admin']){echo "true";} }else{echo "false";} ?>" class="password_del">
              <input type="reset" value="リセット">
            </form>
            <span class="good_count" id="good_<?php echo $val['cat_id'];?>_<?php echo $val['thr_id'];?>_<?php echo $val['pos_id'];?>">
                <?PHP if (  isset ($_POST['goodCountData'])  ) :
                  $TargetId =  $val['cat_id']."_".$val['thr_id']."_".$val['pos_id'];
                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,$location)===TRUE: ?>
            <div class="good_count_plus good_on" location="detail" cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?>
                 pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $val['pos_good_count'];?></div>
                <?PHP break;
                    default: ?>
            <div class="good_count_plus good_off" location="detail" cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?>
                 pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $val['pos_good_count'];?></div>
                <?PHP  }
                else:
                    
                endif;?>
            </span>
          </label>
          <label class="posted-item-1">ID:<?php echo $val['pos_posted_id']; ?></label>
        <?php if(isset($_COOKIE['Admin'])){?>
          <label class="posted-item-1">IPアドレス:<?php {echo $val['pos_ipaddress'];}?></label>
        <?php }//if cookie END ?>
        </div>
        <?php 
          }elseif($post_count!=0 && $val['pos_status']==1){
        ?>
        <?php  
          ?>
        <div class="posted posted-container">
          <label class="posted-item">No.<?php echo $val['pos_id'];?> <?php echo h_escape($val['pos_penname']);?> <?php echo date('Y年m月d日 H:i',strtotime($val['pos_created']));?></label>
          <label class="posted-item"><?php echo h_escape($val['pos_title']);?></label>
          <label class="posted-item"><?php echo nl2br(h_escape($val['pos_text']));?></label>
          <label class="posted-item">
            <form class="password" method="post" action="">
              <input type="text" name="password_<?php echo $val['thr_id'];?>_<?php echo $val['pos_id'];?>"
              maxlength="30" value="" placeholder="30文字以内で入力">
              <input type="submit" id="<?php echo $val['cat_id'];?>_<?php echo $val['thr_id'];?>_<?php echo $val['pos_id'];?>"
               value="削除" cat_name="<?php echo $val['cat_name'];?>" 
                     thr_no="<?php echo $val['thr_id'];?>" pos_no="<?php echo $val['pos_id'];?>"
                     admin="<?php if(isset($_COOKIE['Admin'])){ if($_COOKIE['Admin']){echo "true";} }else{echo "false";} ?>" class="password_del">
              <input type="reset" value="リセット">
            </form>
            <span class="good_count" id="good_<?php echo $val['cat_id'];?>_<?php echo $val['thr_id'];?>_<?php echo $val['pos_id'];?>">
                <?PHP if (  isset ($_POST['goodCountData']) ) :
                  $TargetId =  $val['cat_id']."_".$val['thr_id']."_".$val['pos_id'];

                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,$location)===TRUE: ?>
            <div class="good_count_plus good_on" location="detail" cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?>
                 pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $val['pos_good_count'];?></div>
                <?PHP break;
                    default: ?>
            <div class="good_count_plus good_off" location="detail" cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?>
                 pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $val['pos_good_count'];?></div>
                <?PHP  }
                else:
                    
                endif;?>
            </span>
          </label>
          <label class="posted-item">ID:<?php echo $val['pos_posted_id']; ?></label>
          <!-- 管理者用項目 -->
          <?php if(isset($_COOKIE['Admin'])){?>
          <label class="posted-item">IPアドレス:<?php {echo $val['pos_ipaddress'];}?></label>
          <?php } ?>
        </div>
        <?php 
          }else{ //pos_status
        ?>
        <div class="posted posted-container">
          <label class="posted-item">No<?php echo $val['pos_id'];?> <?php echo date('Y年m月d日 H:i',strtotime($val['pos_created']));?></label>
          <label class="posted-item">削除済み！</label>
          <label class="posted-item">削除済み！</label>
          <label class="posted-item">削除済み！</label>
          <label class="posted-item">削除済み！</label>
        </div>
        <?php 
          }//pos_status if end
        ?>
             <?php
        if($post_count==(ARTICLE_MAX_NUM-1) or
            ($post_num<=(ARTICLE_MAX_NUM-1) and ($post_count == $post_num-1)) or
            ($post_num>(ARTICLE_MAX_NUM-1) and (($post_count+$start) == $post_num-1))){
                  echo ' <div class="paging">';
                  echo  '<div class="category_count_detail">'.$post_num.'件中'.$start_count.'～'.$end_count.'表示</div>';
                  echo '<div class="page_top_scroll">～ページ最上部に移動～</div>';
              if($now_page > 1){
                  echo '<div class="pager-back" location="detail" next_no='.($start_count-1).' page='.($now_page-1).' thr_no='.$_POST['thr_no'].'><a href="javascript:void(0);")> <-前のページへ</a></div>';
              }?>

             <?php
              if($now_page < $max_page){
                  echo '<div class="pager-next" location="detail" next_no='.($start_count+$output_count).' page='.($now_page+1).' thr_no='.$_POST['thr_no'].'><a href="javascript:void(0);")> 次のページへ-></a></div>';
              }
                  echo ' </div>';
             ?>

<div class="threadform">
  <form id="thread" method="post" name="add_post_form" action="">
     <label>コメントを投稿する</label>
    <div id="cat">
       <div id="category_id">[<?php echo $val['cat_id'];?>]</div>
       <div id="category">[<?php echo $val['cat_name'];?>]</div>
       <div id="thr_id"><?php echo h_escape($val['thr_id']);?></div>
       <div id="thread_title"><?php echo h_escape($val['thr_title']);?></div>
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
      <input type="text" name="penname" id="penname_post" maxlength="30" size="60" value="" placeholder="30文字以内で入力"  data-validation-engine="validate[maxSize[30]]">
    </p>
    <p id="password">
      <label>パスワード</label>
      <input type="text" name="password" id="password_post" maxlength="30" size="60" value="" placeholder="30文字以内で入力"   data-validation-engine="validate[maxSize[30]]">
    </p>
  <input type="submit" value="送信内容確認">
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
                               }//if ARTICLE_MAX_NUM END
                    $post_count++;
                  }//foreach
                   unset($output);
                   unset($posts);
              echo ' </span>';
              echo '</section>';
?>
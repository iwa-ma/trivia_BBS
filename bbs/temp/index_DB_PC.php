<?PHP
    for($postno = 0; $postno <= count($thred_output);$postno++){
      if($postno == 0 && 0 < count($thred_output)){
        echo '<span id="pagination'.$thred_output[$postno]['thr_id'].'">';;
?>
        <div class="thread thread-container">
        <div class="thread-item-root">
          <label class="thread-item">[<?php echo $thred_output[$postno]['cat_name'];?>]No.<?php echo $thred_output[$postno]['thr_id'];?></label>
          <label class="thread-item"><?php echo h_escape($thred_output[$postno]['pos_title']);?> </label>
          <label class="thread-item">
            <div class="thread-detail" location="index" cat_name=<?php echo $thred_output[$postno]['cat_name'];?> thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?>>スレッド詳細</div>
          </label>
        </div>
        <div class="posted posted-container">
          <label class="posted-item-1">No.<?php echo $thred_output[$postno]['pos_id'];?> <?php echo h_escape( $thred_output[$postno]['pos_penname']);?> <?php echo date('Y年m月d日 H:i',strtotime($thred_output[$postno]['pos_created']));?></label>
          <label class="posted-item-1"><?php echo nl2br(h_escape($thred_output[$postno]['pos_text']));?>
          </label>
          <label class="posted-item-1">
           <form class="password" method="post" action="" accept-charset="euc-jp">
              <input type="text" name="password_<?php echo $thred_output[$postno]['thr_id'];?>_<?php echo $thred_output[$postno]['pos_id'];?>" cat_name=<?php echo $thred_output[$postno]['cat_name'];?>
               thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?> 
               maxlength="30"   value="" placeholder="30文字以内で入力">
              <input type="submit" id="<?php echo $thred_output[$postno]['cat_name'];?>_<?php echo $thred_output[$postno]['thr_id'];?>_<?php echo $thred_output[$postno]['pos_id'];?>" value="削除" cat_name="<?php echo $thred_output[$postno]['cat_name'];?>" thr_no="<?php echo $thred_output[$postno]['thr_id'];?>"
                 pos_no="<?php echo $thred_output[$postno]['pos_id'];?>" admin="<?php if(isset($_COOKIE['Admin'])){echo "true";}else{echo "false";} ?>" class="password_del">
              <input type="reset" value="リセット">
            </form>
              <span class="good_count" id="good_<?php echo $thred_output[$postno]['cat_id'];?>_<?php echo $thred_output[$postno]['thr_id'];?>_<?php echo $thred_output[$postno]['pos_id'];?>">
                <?PHP if ( isset ($_POST['goodCountData']) ) :
                  $TargetId = $thred_output[$postno]['cat_id']."_".$thred_output[$postno]['thr_id']."_".$thred_output[$postno]['pos_id'];
                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,"index")===TRUE: ?>
                     <div class="good_count_plus good_on" location="index" cat_no=<?php echo $thred_output[$postno]['cat_id'];?> thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $thred_output[$postno]['pos_good_count'];?>
                      </div>
                <?PHP break;
                    default: ?>
                      <div class="good_count_plus good_off" location="index" cat_no=<?php echo $thred_output[$postno]['cat_id'];?> thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $thred_output[$postno]['pos_good_count'];?>
                      </div>
                <?PHP  }
                else:
                    
                endif;?>
              </span>
          </label>
          <label class="posted-item-1">ID:<?php echo  $thred_output[$postno]['pos_posted_id']; ?></label>
          <!-- 管理者用項目 -->
          <?php if(isset($_COOKIE['Admin'])){?>
          <label class="posted-item-1">IPアドレス:<?php {echo $thred_output[$postno]['pos_ipaddress'];}?></label>
          <?php } ?>
        </div>
<?php 
  }elseif($postno < count($thred_output)){
?>
              <div class="posted posted-container">
               <label class="posted-item">No.<?php echo $thred_output[$postno]['pos_id'];?> <?php echo h_escape( $thred_output[$postno]['pos_penname']);?> <?php echo date('Y年m月d日 H:i',strtotime($thred_output[$postno]['pos_created']));?></label>
                <label class="posted-item"><?php echo h_escape($thred_output[$postno]['pos_title']);?></label>
                <label class="posted-item"><?php echo nl2br(h_escape($thred_output[$postno]['pos_text']));?>
                </label>
                <label class="posted-item">
                 <form class="password" method="post" action="">
                    <input type="text" name="password_<?php echo $thred_output[$postno]['thr_id'];?>_<?php echo $thred_output[$postno]['pos_id'];?>" cat_name=<?php echo $thred_output[$postno]['cat_name'];?>
                     thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?> 
                     maxlength="30" value="" placeholder="30文字以内で入力">
                    <input type="submit" id="<?php echo $thred_output[$postno]['cat_name'];?>_<?php echo $thred_output[$postno]['thr_id'];?>_<?php echo $thred_output[$postno]['pos_id'];?>"
                       value="削除" cat_name="<?php echo $thred_output[$postno]['cat_name'];?>" thr_no="<?php echo $thred_output[$postno]['thr_id'];?>"
                       pos_no="<?php echo $thred_output[$postno]['pos_id'];?>" admin="<?php if(isset($_COOKIE['Admin'])){echo "true";}else{echo "false";} ?>" class="password_del">
                    <input type="reset" value="リセット">
                  </form>
                  <span class="good_count" id="good_<?php echo $thred_output[$postno]['cat_id'];?>_<?php echo $thred_output[$postno]['thr_id'];?>_<?php echo $thred_output[$postno]['pos_id'];?>">
                <?PHP if ( isset ($_POST['goodCountData']) ) :
                  $TargetId = $thred_output[$postno]['cat_id']."_".$thred_output[$postno]['thr_id']."_".$thred_output[$postno]['pos_id'];
                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,"index")===TRUE: ?>
                     <div class="good_count_plus good_on" location="index" cat_no=<?php echo $thred_output[$postno]['cat_id'];?> thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $thred_output[$postno]['pos_good_count'];?>
                      </div>
                <?PHP break;
                    default: ?>
                      <div class="good_count_plus good_off" location="index" cat_no=<?php echo $thred_output[$postno]['cat_id'];?> thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $thred_output[$postno]['pos_good_count'];?>
                      </div>
                <?PHP  }
                else:
                    
                endif;?>
                  </span>
                </label>
                <label class="posted-item">ID:<?php echo  $thred_output[$postno]['pos_posted_id']; ?></label>
                <!-- 管理者用項目 -->
                <?php if(isset($_COOKIE['Admin'])){?>
                <label class="posted-item">IPアドレス:<?php {echo $thred_output[$postno]['pos_ipaddress'];}?></label>
                <?php } ?>
<?php
              ?>
              </div>
              <!--posted posted-containerのEND -->
<?php
  //全ての投稿出力が終わった後の処理
  }elseif($postno == count($thred_output) && 0 < count($thred_output)){
      echo"</div>";
  }elseif(0 == count($thred_output)){
    echo '<div class="thread thread-container">'."スレッドNo".$thr_puts_all[$thre_c]['thr_id']."には投稿がありません。".'</div>';
  }//pos if
 }//posfor
              echo ' </span>';
    unset($thred_output);
?>


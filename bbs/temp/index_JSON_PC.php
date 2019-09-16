<?PHP    foreach($output as $val){
      if($print_count==0){
          echo '<span id="pagination'.($thre_c+1).'">';
?>
        <div class="thread thread-container">
        <div class="thread-item-root">
            <label class="thread-item">[<?php echo $posts[2];?>]No.<?php echo $posts[0];?></label>
            <label class="thread-item"><?php echo h_escape($posts[3]);?> </label>
            <label class="thread-item">
            <div class="thread-detail" cat_name=<?php echo $posts[2];?> thr_no=<?php echo $posts[0];?> pos_no=<?php echo $val['pos_id'];?>>スレッド詳細</div>
            </label>
        </div>
              <div class="posted posted-container">
               <label class="posted-item-1">No.<?php echo $val['pos_id'];?> <?php echo h_escape($val['pos_penname']);?> <?php echo date('Y年m月d日 H:i',strtotime($val['pos_created']));?></label>
                <label class="posted-item-1"><?php echo nl2br(h_escape($val['pos_text']));?></label>
                <label class="posted-item-1">
                 <form class="password" method="post" action="">
                    <input type="text" name="password_<?php echo $posts[0];?>_<?php echo $val['pos_id'];?>" cat_name=<?php echo $posts[2];?> 
                    thr_no=<?php echo $posts[0];?> pos_no=<?php echo $val['pos_id'];?> 
                    maxlength="30" value="" placeholder="30文字以内で入力">
                    <input type="submit" id="<?php echo $posts[2];?>_<?php echo $posts[0];?>_<?php echo $val['pos_id'];?>"
                     value="削除" cat_name="<?php echo $posts[2];?>" thr_no="<?php echo $posts[0];?>"
                      pos_no="<?php echo $val['pos_id'];?>" admin="<?php if(isset($_COOKIE['Admin'])){echo "true";}else{echo "false";} ?>"
                        class="password_del">
                    <input type="reset" value="リセット">
                  </form>
                  <span class="good_count" id="good_<?php echo cat_no_get($posts[2]);?>_<?php echo $posts[0];?>_<?php echo $val['pos_id'];?>">
                <?PHP if ( isset ($_POST['goodCountData']) ) :
                  $TargetId = cat_no_get($posts[2])."_".$posts[0]."_".$val['pos_id'];
                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,"index")===TRUE: ?>
                    <div class="good_count_plus good_on" location="index" cat_no=<?php echo cat_no_get($posts[2]);?> thr_no=<?php echo $posts[0];?> pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $val['pos_good_count'];?></div>
                <?PHP break;
                    default: ?>
                    <div class="good_count_plus good_off" location="index" cat_no=<?php echo cat_no_get($posts[2]);?> thr_no=<?php echo $posts[0];?> pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $val['pos_good_count'];?></div>
                <?PHP  }
                else:
                    
                endif;?>
                  </span>
                </label>
                <label class="posted-item-1">ID:<?php echo $val['pos_posted_id']; ?></label>
                <!-- 管理者用項目 -->
                <?php if(isset($_COOKIE['Admin'])){?>
                <label class="posted-item-1">IPアドレス:<?php {echo $val['pos_ipaddress'];}?></label>
                <?php } ?>
              </div>
        <?php 
          }elseif($print_count!=0 && $val['pos_status']==1){
        ?>
              <div class="posted posted-container">
                <label class="posted-item">No.<?php echo $val['pos_id'];?> <?php echo h_escape($val['pos_penname']);?> <?php echo date('Y年m月d日 H:i',strtotime($val['pos_created']));?></label>
                <label class="posted-item"><?php echo h_escape($val['pos_title']);?></label>
                <label class="posted-item"><?php echo nl2br(h_escape($val['pos_text']));?></label>
                <label class="posted-item">
                  <form class="password" method="post" action="">
                    <input type="text" name="password_<?php echo $posts[0];?>_<?php echo $val['pos_id'];?>" cat_name=<?php echo $posts[2];?> 
                    thr_no=<?php echo $posts[0];?> pos_no=<?php echo $val['pos_id'];?> 
                    maxlength="30" value="" placeholder="30文字以内で入力">
                    <input type="submit" id="<?php echo $posts[2];?>_<?php echo $posts[0];?>_
                    <?php echo $val['pos_id'];?>"
                     value="削除" cat_name="<?php echo $posts[2];?>"
                      thr_no="<?php echo $posts[0];?>" pos_no="<?php echo $val['pos_id'];?>"  admin="<?php if(isset($_COOKIE['Admin'])){echo "true";}else{echo "false";} ?>" class="password_del">
                    <input type="reset" value="リセット">
                  </form>
                  <span class="good_count" id="good_<?php echo cat_no_get($posts[2]);?>_<?php echo $posts[0];?>_<?php echo $val['pos_id'];?>">
                <?PHP if ( isset ($_POST['goodCountData']) ) :
                  $TargetId = cat_no_get($posts[2])."_".$posts[0]."_".$val['pos_id'];
                  switch (TRUE) {
                    case good_state($goodCountData,$TargetId,"index")===TRUE: ?>
                    <div class="good_count_plus good_on" location="index" cat_no=<?php echo cat_no_get($posts[2]);?> thr_no=<?php echo $posts[0];?> pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね <?php echo $val['pos_good_count'];?></div>
                <?PHP break;
                    default: ?>
                    <div class="good_count_plus good_off" location="index" cat_no=<?php echo cat_no_get($posts[2]);?> thr_no=<?php echo $posts[0];?> pos_no=<?php echo $val['pos_id'];?> page=<?php echo $now_page;?>>いいね push! <?php echo $val['pos_good_count'];?></div>
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
<?php
              ?>
              </div>
<?php 
                                                                                        }
                  $print_count++;
                  }//    foreach($output as $val)
                   unset($value);
                   unset($output);
                  echo '</div>'; 
                  echo '</span>';
?>
<?php 
    for($postno = 0; $postno <= count($thred_output);$postno++){
      if($postno == 0 && 0 < count($thred_output)){
        $thr_lastupdate = strtotime($thred_output[$postno]['thr_lastupdate']);
?>
        <div class="thread thread-container">
        <div class="thread-item-root thread-detail"  cat_name=<?php echo $thred_output[$postno]['cat_name'];?> thr_no=<?php echo $thred_output[$postno]['thr_id'];?> pos_no=<?php echo $thred_output[$postno]['pos_id'];?>>
          <label class="thread-item">
            [<?php echo $thred_output[$postno]['cat_name'];?>]No.<?php echo $thred_output[$postno]['thr_id'];?>
            <?php echo nl2br(h_escape($thred_output[$postno]['pos_title']));?>
          </label>
          <label class="thread-item">
            <?php echo date('Y年m月d日 H:i',$thr_lastupdate);?>
          </label>
        </div><!-- <div class="thread-item-root thread-detail" -->
<?php
        }elseif($postno == count($thred_output) && 0 < count($thred_output)){
            echo"</div>";
        }elseif(0 == count($thred_output)){
        }//pos if
       }//posfor
?>
<?PHP
  $thred_output = 0;
    foreach($output as $val){
      if($thred_output == 0 && 0 < count($output)){
        $thr_lastupdate = strtotime($posts[5]);
    ?>
        <div class="thread thread-container">
        <div class="thread-item-root thread-detail"  cat_name=<?php echo $posts[2];?> thr_no=<?php echo $posts[0];?>
         pos_no=<?php echo $val['pos_id'];?>>
          <label class="thread-item">
            [<?php echo $posts[2];?>]No.<?php echo $posts[0];?>
            <?php echo h_escape($posts[3]);?>
          </label>
          <label class="thread-item">
            <?php echo date("Y年m月d日 H:i",$thr_lastupdate);?>
          </label>
        </div>
<?php
        }//if($thred_output == 0 && 0 < count($output))
        
        //全ての投稿出力が終わった後の処理
        if($thred_output == count($output)-1 && 0 < count($output)){
            echo"</div>";
        }elseif(0 == count($thred_output)){
        }//pos if

        $thred_output++;

    }//foreach end
    unset($thred_output);
?>
      <div class="search_count"><?php echo $post_num;?>件中<?php echo $start_count;?>～<?php echo $end_count;?>件表示</div>
        <div class="paging">
<?php if($now_page > 1){?>
          <div class="pager-back" location="search" next_no=<?PHP echo ($start_count-1) ?> page=<?php echo $now_page-1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
          <a href="javascript:void(0);"> <-前のページへ</a>
          </div>
<?php               }?>
<?php if($now_page < $max_page){?>
          <div class="pager-next"  location="search" next_no=<?PHP echo ($end_count+1) ?> page=<?php echo $now_page+1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
            <a href="javascript:void(0);"> 次のページへ-></a>
          </div>
<?php               }?>
        </div><!-- <div class="paging"> -->
<?php
        foreach($output as $val){
?>
        <span id="pagination">
          <div class="search search-container thread-detail_search"
             cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?> iotype=<?php echo $_POST['iotype'];?>>
            <label class="search-item"><?php echo $val['thr_poscount'];?>投稿 <?php echo $val["cat_name"];?> 
            <?php echo date('Y年m月d日 H:i',strtotime($val['thr_lastupdate']));?></label>
            <label class="search-item"><?php echo $val["thr_title"];?> </label>
          </div>
                  <?php 
                               }
                   unset($output);
                   unset($posts);
                   echo '<div class="page_top_scroll">～ページ最上部に移動～</div>';
                   ?>
        <div class="paging">
<?php if($now_page > 1){?>
          <div class="pager-back" location="search" next_no=<?PHP echo ($start_count-1) ?> page=<?php echo $now_page-1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
          <a href="javascript:void(0);"> <-前のページへ</a>
          </div>
<?php               }?>
<?php if($now_page < $max_page){?>
          <div class="pager-next"  location="search" next_no=<?PHP echo ($end_count+1) ?> page=<?php echo $now_page+1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
            <a href="javascript:void(0);"> 次のページへ-></a>
          </div>
<?php               }?>
        </div><!-- <div class="paging"> -->
        </span><!-- <span id="pagination"> -->

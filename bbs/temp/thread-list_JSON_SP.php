    <div>ホーム→カテゴリ→<?php echo $cat_name;?></div>
    <div class="category_title">『<?php echo $cat_name;?>』の投稿</div>
    <div class="category_count"><?php echo $post_num;?>件中<?php echo $start_count;?>～<?php echo $end_count;?>表示</div>
          <div class="paging">
<?php if($now_page > 1){?>
          <div class="pager-back" location="thread-list" thr_count_all=<?PHP echo $_POST['thr_count_all'] ?> start_no=<?PHP echo ($start_no_work_back) ?> next_no=<?PHP echo ($start_count-1) ?> page=<?php echo $now_page-1 ?> cat_no=<?php echo $_POST['cat_no']?>>
          <a href="javascript:void(0);"> <-前のページへ</a></div>
<?php               }?>
<?php                 if($now_page < $max_page){?>
          <div class="pager-next" location="thread-list" thr_count_all=<?PHP echo $_POST['thr_count_all'] ?> start_no=<?PHP echo ($start_no_work_next) ?> next_no=<?PHP echo ($start_count+$output_count) ?> page=<?php echo $now_page+1 ?> cat_no=<?php echo $_POST['cat_no'] ?>>
            <a href="javascript:void(0);"> 次のページへ-></a></div>
<?php               }?>
        </div><!-- <div class="paging"> -->
<?php
        foreach($output as $val){
          echo '<span id="pagination">';
?>
          <div class="category category-container thread-detail" cat_name=<?php echo $val['cat_name'];?> thr_no=<?php echo $val['thr_id'];?>>
            <label class="category-list"><?php echo count($val);?>投稿 :<?php echo date('Y年m月d日 H:i',strtotime($val['thr_lastupdate']));?>更新</label>
            <label class="category-list"><?php echo h_escape($val['thr_title']);?> </label>
          </div>
                  <?php 
                               }
                   unset($output);
                   unset($posts);
                   echo '<div class="page_top_scroll">～ページ最上部に移動～</div>';
                   ?>
          <div class="paging">
<?php if($now_page > 1){?>
          <div class="pager-back" location="thread-list" thr_count_all=<?PHP echo $_POST['thr_count_all'] ?> start_no=<?PHP echo ($start_no_work_back) ?> next_no=<?PHP echo ($start_count-1) ?> page=<?php echo $now_page-1 ?> cat_no=<?php echo $_POST['cat_no']?>>
          <a href="javascript:void(0);"> <-前のページへ</a></div>
<?php               }?>
<?php                 if($now_page < $max_page){?>
          <div class="pager-next" location="thread-list" thr_count_all=<?PHP echo $_POST['thr_count_all'] ?> start_no=<?PHP echo ($start_no_work_next) ?> next_no=<?PHP echo ($start_count+$output_count) ?> page=<?php echo $now_page+1 ?> cat_no=<?php echo $_POST['cat_no'] ?>>
            <a href="javascript:void(0);"> 次のページへ-></a></div>
<?php               }?>
        </div><!-- <div class="paging"> -->
              <?php
              echo ' </span>';
              ?>
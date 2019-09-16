      <div class="search_count"><?php echo $post_num;?>件中<?php echo $start_count;?>～<?php echo $end_count;?>表示</div>
        <div class="paging">
<?php if($now_page > 1){?>
          <div class="pager-back" location="search" start_no=<?PHP echo ($start_no_work_back) ?> next_no=<?PHP echo ($start_count-1) ?> page=<?php echo $now_page-1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
          <a href="javascript:void(0);"> <-前のページへ</a>
          </div>
<?php               }?>
<?php if($now_page < $max_page){?>
          <div class="pager-next" location="search" start_no=<?PHP echo ($start_no_work_next) ?> next_no=<?PHP echo ($end_count+1) ?> page=<?php echo $now_page+1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
            <a href="javascript:void(0);"> 次のページへ-></a>
          </div>
<?php               }?>
        </div><!-- <div class="paging"> -->
<?PHP
        foreach($output as $key => $val){
          echo '<span id="pagination">';
?>
          <div class="search search-container thread-detail_search"
             cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?> iotype=<?php echo $_POST['iotype'];?>>
            <label class="search-item"><?php echo $val['thr_poscount'];?>投稿 <?php echo cat_name_get($val['cat_id']);?> 
            <?php echo date('Y年m月d日 H:i',strtotime($val['thr_lastupdate']));?></label>
            <label class="search-item"><?php echo h_escape($root[0]['cat_id_'.$val["cat_id"]]['thr_id_'.$val["thr_id"]]["posted"][0]["pos_text"]);?> </label>
          </div>
                  <?php 
                               }
                   unset($output);
                   unset($result);
                   echo '<div class="page_top_scroll">～ページ最上部に移動～</div>';
                  ?>
        <div class="paging">
<?php if($now_page > 1){?>
          <div class="pager-back" location="search" start_no=<?PHP echo ($start_no_work_back) ?> next_no=<?PHP echo ($start_count-1) ?> page=<?php echo $now_page-1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
          <a href="javascript:void(0);"> <-前のページへ</a>
          </div>
<?php               }?>
<?php if($now_page < $max_page){?>
          <div class="pager-next" location="search" start_no=<?PHP echo ($start_no_work_next) ?> next_no=<?PHP echo ($end_count+1) ?> page=<?php echo $now_page+1 ?> search_text=<?PHP echo implode( ",",$search_key) ?>>
            <a href="javascript:void(0);"> 次のページへ-></a>
          </div>
<?php               }?>
        </div><!-- <div class="paging"> -->
<?PHP
              echo ' </span>';
?>
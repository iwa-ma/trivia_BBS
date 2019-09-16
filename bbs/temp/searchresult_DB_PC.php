      <div class="search_count"><?php echo $post_num;?>件中<?php echo $start_count;?>～<?php echo $end_count;?>件表示</div>
          <?php
                  echo ' <div class="paging">';
              if($now_page > 1){
                  echo '<div class="pager-back" page='.($now_page-1).' next_no='.($start_count-1).' location="search" search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> <-前のページへ</a></div>';
              }
              if($now_page < $max_page){
                  echo '<div class="pager-next" page='.($now_page+1).' next_no='.($end_count+1).' location="search" search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> 次のページへ-></a></div>';
              }
                  echo ' </div>';//<div class="paging">
          ?>

<?php
        foreach($output as $val){
?>
        <span id="pagination">
          <div class="search search-container thread-detail_search"
             cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?> iotype=<?php echo $_POST['iotype'];?>>
            <label class="search-item"><?php echo $val['thr_poscount'];?>投稿 <?php echo $val["cat_name"];?> 
            <?php echo date('Y年m月d日 H:i',strtotime($val['thr_lastupdate']));?></label>
            <label class="search-item"><?php echo $val["thr_title"];?> </label>
                        <label class="search-item">一致キーワード：<?php echo $val['search_word'];?> </label>
            <label class="search-item"><?php echo h_escape($val["pos_text"]);?> </label>
          </div>
                  <?php 
                               }
                   unset($output);
                   unset($output_work);

                   echo '<div class="page_top_scroll">～ページ最上部に移動～</div>';

                  echo ' <div class="paging">';
              if($now_page > 1){
                  echo '<div class="pager-back" page='.($now_page-1).' next_no='.($start_count-1).' location="search" search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> <-前のページへ</a></div>';
              }
              if($now_page < $max_page){
                  echo '<div class="pager-next" page='.($now_page+1).' next_no='.($end_count+1).' location="search" search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> 次のページへ-></a></div>';
              }
                  echo ' </div>';//<div class="paging">
              echo ' </span>';//<span id="pagination">
              ?>
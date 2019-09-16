      <div class="search_count"><?php echo $post_num;?>件中<?php echo $start_count;?>～<?php echo $end_count;?>表示</div>
<?PHP
                  echo ' <div class="paging">';
              if($now_page > 1){
                  echo '<div class="pager-back" location="search" page='.($now_page-1).' start_no='.($start_no_work_back).' next_no='.($start_count-1).' search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> <-前のページへ</a></div>';
              }
              if($now_page < $max_page){
                  echo '<div class="pager-next" location="search" page='.($now_page+1).' start_no='.($start_no_work_next).' next_no='.($end_count+1).' search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> 次のページへ-></a></div>';
              }
                  echo ' </div>';//<div class="paging">
?>

<?PHP
        foreach($output as $key => $val){
          echo '<span id="pagination">';
?>
          <div class="search search-container thread-detail_search"
             cat_no=<?php echo $val['cat_id'];?> thr_no=<?php echo $val['thr_id'];?> iotype=<?php echo $_POST['iotype'];?>>
            <label class="search-item"><?php echo $val['thr_poscount'];?>投稿 <?php echo cat_name_get($val['cat_id']);?> 
            <?php echo date('Y年m月d日 H:i',strtotime($val['thr_lastupdate']));?></label>
            <label class="search-item"><?php echo($root[0]['cat_id_'.$val['cat_id']]['thr_id_'.$val['thr_id']]['thr_title']);?> </label>
            <label class="search-item">一致キーワード：<?php echo($val['search_word']);?> </label>
            <label class="search-item"><?php echo h_escape($root[0]['cat_id_'.$val["cat_id"]]['thr_id_'.$val["thr_id"]]["posted"][0]["pos_text"]);?> </label>

          </div>
                  <?php 
                               }
                   unset($output);
                   unset($result);

                   echo '<div class="page_top_scroll">～ページ最上部に移動～</div>';

                  echo ' <div class="paging">';
              if($now_page > 1){
                  echo '<div class="pager-back" location="search" page='.($now_page-1).' start_no='.($start_no_work_back).' next_no='.($start_count-1).' search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> <-前のページへ</a></div>';
              }
              if($now_page < $max_page){
                  echo '<div class="pager-next" location="search" page='.($now_page+1).' start_no='.($start_no_work_next).' next_no='.($end_count+1).' search_text='.implode( ",",$search_key).'><a href="javascript:void(0);")> 次のページへ-></a></div>';
              }
                  echo ' </div>';//<div class="paging">
              echo ' </span>';
?>
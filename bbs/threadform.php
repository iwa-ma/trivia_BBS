<?php
require('base.php');
  switch ($_GET['mode']) {
   case 'json':
      break;
   case 'db':
    $board = new Board();
    $cat_puts_all = $board->get_cat_ALL();
      break;
  }
?>
<script type="text/javascript" src="common/js/base.js"></script>

<div id="newpostform_title">新規投稿する</div>
<div id="newpostform_title">※スレッドとして新規登録されます</div>

<div class="threadform">
<form id="thread" method="post" name="new_thread_form" action="">
    <div id="cat">
       <div id="ip_type">new</div>
    </div>
  <p id="title">
  <label>タイトル※必須入力</label>
  <input type="text" id="title_post" name="title" maxlength="30" size="60" value="" placeholder="50文字以内で入力" data-validation-engine="validate[required,maxSize[50]]" data-prompt-position="bottomRight:-100" type="text"></p>

   <label>カテゴリ※必須入力</label>
  <p id="category">
    <div class="selectWrap">
    <select class="select" name="category" id="category_post" data-validation-engine="validate[required]"
     data-prompt-position="centerRight">
      <option value="" hidden>Choose</option>
    <?php
      switch ($_GET['mode']) {
       case 'json':
       for($cat_c = 1; $cat_c <=CATEGORY_ENABLE_MAX; $cat_c++){
       echo '<option value='.cat_name_get($cat_c).'>'.cat_name_get($cat_c).'</option>';
       }
          break;
       case 'db':
       for($cat_c=1;$cat_c<=count($cat_puts_all);$cat_c++){
       echo '<option value='.$cat_puts_all[$cat_c-1]['cat_name'].'>'.$cat_puts_all[$cat_c-1]['cat_name'].'</option>';
        }
          break;
       default:
          break;
      }
    ?>
   </select>
    </div>
  </p>

<p id="maintext">
  <label>投稿内容※必須入力</label>
 <textarea name="maintext" id="maintext_post" rows="4" cols="30" placeholder="400文字以内で入力" data-validation-engine="validate[required,maxSize[400]]"
  data-prompt-position="bottomRight:-100" type="text"></textarea>
</p>

<p id="penname">
  <label>ペンネーム</label>
  <input type="text" name="penname" id="penname_post" maxlength="30" size="60" value="" placeholder="30文字以内で入力"  data-validation-engine="validate[maxSize[30]]">
</p>
<p id="password">
  <label>パスワード</label>
  <input type="text" name="password" id="password_post" maxlength="30" size="60" value="" placeholder="30文字以内で入力"   data-validation-engine="validate[maxSize[30]]">
</p>

  <input type="submit" value="送信内容確認" class="Confirmation">
  <input type="reset" value="リセット">
</form>
</div>

<div class="confirmation_form">
<form id="confirmation" method="post" action="registration.php">
  <div id="cat">
    <div id="category_con"></div>
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

	<input type="button" name="btn_back" value="戻る"  class="New_Confirmation_back">
	<input type="submit" name="btn_submit" value="送信">
	<input type="hidden" id="title_con_h" name="title_con_h" value="">
	<input type="hidden" id="category_con_h" name="category_con_h" value="">
  <input type="hidden" id="maintext_con_h" name="maintext_con_h" value="">
  <input type="hidden" id="penname_con_h" name="penname_con_h" value="">
  <input type="hidden" id="password_con_h" name="password_con_h" value="">
  <input type="hidden" id="iotype" name="iotype" value="<?php echo $_GET['mode']; ?>">
</form>
</div>
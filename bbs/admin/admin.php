<?php
require(dirname(__FILE__).'/../base.php');
?>
<!DOCTYPE html>
<html>
<head>
<?php  if(agent_type()=="sp"){?><meta name="viewport" content="width=device-width"><?php }?>
  <link rel="stylesheet" type="text/css" href="../common/css/layout.css">
<?php  if(agent_type()=="sp"){?>
  <link rel="stylesheet" type="text/css" href="../common/css/layout_sp.css">
<?php }?>
  <script type="text/javascript" src="../common/js/jquery-3.3.1.min.js"></script>
  <script type="text/javascript" src="../common/js/jquery.cookie.js"></script>
  <title>トリビアくん管理者用ページ</title>
  <meta http-equiv="Content-type" content="text/html">
</head>
<body>
  <div id="admin-container">
    <div id="admin_title">管理者用メニュー</div>
    <div id="admin_db">管理者用(db版)にアクセス</div>
    <div id="admin_json">管理者用(json版)にアクセス</div>
    <div id="admin_del">管理者用クッキー削除</div>
  </div>
</body>
</html>
<script>
$(function(){
window.location.href
  $('#admin_db').click(function() {
    window.location.href="/bbs/db";
  });
  $('#admin_json').click(function() {
    window.location.href="/bbs/json";
  });

  $('#admin_del').click(function() {
    if($.cookie("Admin")){
     if(!confirm('本当に削除しますか？')){
         return false;
     }else{
        $.cookie("Admin","",{path:"/bbs",expires:-1});
     }
    }else{
      alert("管理者クッキーがありませんよ");
    }
  });

});
</script>
<?php
require(dirname(__FILE__).'/base.php');
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="common/css/layout.css">
<?php  if(agent_type()=="sp"){?>
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" type="text/css" href="common/css/layout_sp.css">
<?php }?>
  <meta http-equiv="Content-type" content="text/html">
<title>トリビアくん利用者用ページ</title>
</head>
<body>
  <div id="user-container">
    <div id="user_title">利用者用メニュー</div>
    <div id="user_db"><a href="/bbs/index.php?mode=db">利用者用(db版）にアクセス</a></div>
    <div id="user_json"><a href="/bbs/index.php?mode=json">利用者用(json版）にアクセス</a></div>
  </div>
</body>
</html>


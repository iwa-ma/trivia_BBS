# PHP掲示板

## プログラム説明
  ・スレッド形式の掲示板で、カテゴリ追加対応しています。  
  ・DBサーバーが使用できない環境など想定して、JSONファイルで投稿データの運用にも対応。  
  ・レスポンシブ対応  

## 開発の目的
  ・Apache、PHP、SQLを使用した開発スキルの向上

## 開発、テスト環境
  Apache  2.2.34（AWS)  
  PHP     7.1.30  
  MySQL   5.7.25  

## 動作手順
  1.クローンを取得してApacheのドキュメントルートに配置  (下記画面はXAMPPのデフォルトに配置した場合)
  ![image](https://github.com/user-attachments/assets/bfdb5f2f-54b9-4cc1-a3f5-5baf3ef83d4e)
　![image](https://github.com/user-attachments/assets/26f8f2ba-ab9b-451c-b803-a1612b333b87)

  2.db_dmp/tribia_dev.sqlをMySQLのDBにリストアして、テーブルを作成  
  3.trivia_BBS/bbs/DB_config.phpに手順2でリストアしたDBサーバーのホスト名またはIPアドレス、  
    ユーザー名、パスワード、DB名を入力
  4.`http://localhost/TriviaBBS/bbs/io_select.php`にアクセスで、DBまたはJSON選択、  
  クリックで各Topページに遷移します。

  ![DBorJSONセレクト画面](./readme_image/io_select.jpg)  

### Top画面(PC)  
  ![DBorJSONセレクト画面](./readme_image/bbs_top_pc.JPG)  
### Top画面(SP)  
  ![DBorJSONセレクト画面](./readme_image/bbs_top_sp.JPG)  
### 投稿画面(PC)  
  ![DBorJSONセレクト画面](./readme_image/bbs_input_pc.JPG)  
### 投稿画面(SP)  
  ![DBorJSONセレクト画面](./readme_image/bbs_input_sp.JPG)  
### コメント投稿画面(PC)  
  ![DBorJSONセレクト画面](./readme_image/bbs_comment_pc.JPG)  
### コメント投稿画面(SP)  
  ![DBorJSONセレクト画面](./readme_image/bbs_comment_sp.JPG)  
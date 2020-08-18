<?php
//データベース接続
function dbConnect(){
  $dsn='データベース名';
  $user='ユーザー名';
  $password='パスワード';
  
  //接続確認処理
  try{ 
    //接続できたら
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
  }catch(PDOException $e){
    //接続失敗した場合
    echo "接続失敗<br>".$e->getMessage().'<br>';
    exit();//処理を終了
  };

  return $pdo;
}
?>
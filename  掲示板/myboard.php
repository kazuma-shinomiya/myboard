<?php 
require_once 'dataconnect.php';
$pdo=dbConnect();
//テーブル作成のSQLを作成
try{
  $sql = "CREATE TABLE IF NOT EXISTS myboard" 
          ."("
          ." id INT AUTO_INCREMENT PRIMARY KEY,"
          ." name char(32),"
          . "comment TEXT,"
          . "date DATETIME,"
          . "password TEXT"
          .");";        
  //SQLを実行
  $stmt=$pdo->query($sql);
}catch(PDOException $e){
  echo "テーブル作成失敗<br>".$e->getMessage().'<br>';
  exit();//処理を終了
}

//編集対象の書き込みを取得
if(!empty($_POST['edit']) and !empty($_POST['pass3'])){
  $id=$_POST['edit'];
  $password3=$_POST['pass3'];
  //パスワード取得
  $sql= 'SELECT password from myboard where id=:id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt-> execute();
  $passwords = $stmt->fetchAll();
  foreach($passwords as $password){
    $truePass=$password['password'];
  }
  if($password3==$truePass){
    //名前とコメントの取得
    $sql= 'SELECT name,comment from myboard where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt-> execute();
    $edits = $stmt->fetchAll();
    foreach($edits as $edit){
      $editName=$edit['name'];
      $editComment=$edit['comment'];
    }
  }else{
    echo'パスワードが違います<br>';
  }
  
}

if(!empty($_POST['name']) and !empty($_POST['comment'])){
  if(!empty($_POST['editNum'])){//編集機能
    $id=$_POST['editNum'];
    $name=$_POST['name'];
    $comment=$_POST['comment'];
    $sql = 'UPDATE myboard set name=:name,comment=:comment where id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location:https://tb-220191.tech-base.net/m5-1.php');
  }else{ //新規投稿
    $name=$_POST['name'];
    $comment=$_POST['comment'];
    $date=date('Y/m/d H:i:s');
    $password=$_POST['pass1'];
    //データを入力
    $sql = $pdo -> prepare("INSERT INTO myboard (name, comment,date,password) VALUES (:name, :comment,:date,:password)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':password', $password, PDO::PARAM_STR);
    $sql -> execute();
    echo 'データを追加しました<br>';
    header('Location:https://tb-220191.tech-base.net/m5-1.php');
    //二重投稿防止
  }
}

//削除機能
if(!empty($_POST['delete']) and !empty($_POST['pass2'])){
  $id = $_POST['delete']; 
  $password2=$_POST['pass2'];
  //パスワード取得
  $sql= 'SELECT password from myboard where id=:id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt-> execute();
  $passwords = $stmt->fetchAll();
  foreach($passwords as $password){
    $truePass=$password['password'];
  }
  if($password2==$truePass){//もしパスワードは合致していれば
    $sql = 'DELETE from myboard where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location:https://tb-220191.tech-base.net/m5-1.php');
  }else{
    echo'パスワードが違います<br>';
    header('Location:https://tb-220191.tech-base.net/m5-1.php');
  }
}
?>




<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<!-- CSSファイルの読み込み -->
<link rel="stylesheet" href="style.css"> 
<title>m5-1</title>
</head>
<body>
<header>
  <h1>掲示板へようこそ</h1>
</header>
<main>
  <section>
    <div class="board">
      <?php
      //データの表示
      $sql = 'SELECT * FROM myboard';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach ($results as $row){
      //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].'さん,';
        echo $row['comment'].',';
        echo $row['date'].',';
        echo $row['password'].'<br>';
        echo "<hr>";
      }
      ?>
    </div>
  </section>
  <section>
    <form action="#" method="post" class="postForm">
      <fieldset class="post new">
        <legend>新規投稿</legend>
        <p><label for='name'>名前：</label><br> <!-- ラベル付けするとクリックしてフォームに飛べる -->
        <input type="text" name="name" id='name' value="<?php if(!empty($_POST['edit'])){echo $editName;}?>"></p>
        <p><label for='comment'>コメント：</label><br>
        <textarea name="comment" id="comment">
        <?php if(!empty($_POST['edit'])){echo $editComment;} ?>
        </textarea></p>
        <p><label for='pass1'>パスワード：</label><br>
        <input type="text" name="pass1" id="pass1"></p>
        <input type="hidden" name="editNum" value="<?php if(!empty($_POST['edit'])){echo $id;}?>"></p>
        <input class="btn" type="submit" value="送信"></p>
      </fieldset>
      <fieldset class="post delete">
        <legend>投稿の削除</legend>
        <p><label for='delete'>削除番号：</label><br>
        <input type="number" name="delete" id="delete" placeholder="番号を入力"></p>
        <p><label for='pass2'>パスワード：</label><br>
        <input type="text" name="pass2" id="pass2"></p>
        <input class="btn" type="submit" value="削除"></p>
      </fieldset>
      <fieldset class="post edit">
        <legend>投稿の編集</legend>
        <p><label for='edit'>編集番号：</label><br>
        <input type="number" name="edit"  id="edit" placeholder="番号を入力"></p>
        <p><label for='pass3'>パスワード：</label><br>
        <input type="text" name="pass3" id="pass3"></p>
        <input class="btn" type="submit" value="編集"></p>
      </fieldset>
    </form>
  </section>
</main>
<footer>
  <div class="page-top">
    <a href="#">TOPへもどる</a>
  </div>
</footer>
</body>
</html>
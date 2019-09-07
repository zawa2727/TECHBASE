<html>
<meta charset="UTF-8">
<?php
//データベース生成////////////////////////////////////////////////////////////////////////////////
$dsn = '******';
	$user = '******';
	$password = '******';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
$sql = "CREATE TABLE IF NOT EXISTS tbm5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass char(32)"
	.");";
	$stmt = $pdo->query($sql);
//////////////////////////////////////////////////////////////////////////////////////////////////
?>

<p>【  編集フォーム  】</p>
<form action="mission_5-1-2.php" method="post">
<input type="text" name="EDIT" value="編集対象番号">
<br><br>
<input type="password" name = "PASS"> 
<br><br>
<input type="submit" value="編集">
</form>
<br><br>
<?php
//編集フォームの処理
if(!empty($_POST['EDIT'])){
 $cnt = 0;
 $edit = $_POST['EDIT'];
 $pass = $_POST['PASS'];
//データベースに保存されている値を取得/////////////////////////////////////////////////////
 $sql = 'SELECT * FROM tbm5';
 $stmt = $pdo->query($sql);
 $contents = $stmt->fetchAll();
///////////////////////////////////////////////////////////////////////////////////////////
 for($i =0;$i<count($contents);$i++){
  if( ($edit == $contents[$i]['id']) && ($pass == $contents[$i]['pass']) ){
   $name = $contents[$i]['name'];
   $comment = $contents[$i]['comment'];
   $cnt++;
  }
 }
 if($cnt == 0)echo "編集対象の投稿が見つからないか、パスワードが間違っています。"."<br>";
}
else $edit = 0;
?>

<p>【  投稿フォーム  】</p>
<form action="mission_5-1-2.php" method="post">
<input type="hidden" name = "edit_number" value = "<?php echo $edit ?>"> 
<?php
if($edit ==0 || $cnt == 0){//編集モードを経由していない場合は初期化を行う
      $name = "お名前";
      $comment = "コメント";
  }
?>
<input type="text" name="NAME" value="<?php echo $name; ?>">
<br><br>
<textarea name="COMMENT"><?php echo $comment; ?></textarea>
<br><br>
<input type="password" name = "PASS">
<br><br>
<input type="submit"  name = "SUBMIT" value="送信">
</form>
<br><br>

<p>【  削除フォーム  】</p>
<form action="mission_5-1-2.php" method="post">
<input type="text" name="DELETE" value="削除対象番号">
<br><br>
<input type="password" name = "PASS">
<br><br>
<input type="submit" value="削除">
</form>
</html>


<?php
//入力フォームの処理
if(!empty($_POST['NAME']) && !empty($_POST['COMMENT']) && !empty($_POST['PASS'])){
 $name = $_POST['NAME']; 
 $comment = $_POST['COMMENT']; 
 $pass = $_POST['PASS'];
 $date = date("Y/m/d H:i:s"); 
  if($_POST['edit_number'] == 0){//新規投稿モード
//データベースに保存されている値を取得/////////////////////////////////////////////////////
   $sql = 'SELECT * FROM tbm5';
   $stmt = $pdo->query($sql);
   $contents = $stmt->fetchAll();
///////////////////////////////////////////////////////////////////////////////////////////

//新規投稿処理/////////////////////////////////////////////////////
   $sql = $pdo -> prepare("INSERT INTO tbm5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
   $sql -> bindParam(':name', $name, PDO::PARAM_STR);
   $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
   $sql -> bindParam(':date', $date, PDO::PARAM_STR);
   $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
   $sql -> execute();
////////////////////////////////////////////////////////////////////
   echo "新規投稿が完了しました。"."<br>";
  }

 if($_POST['edit_number'] != 0){/////////////////////////EDITモードのとき
//データベースに保存されている値を取得/////////////////////////////////////////////////////
  $sql = 'SELECT * FROM tbm5';
  $stmt = $pdo->query($sql);
  $contents = $stmt->fetchAll();
///////////////////////////////////////////////////////////////////////////////////////////
  $cnt = 0;
  for($i =0;$i<count($contents);$i++){
   if( ($_POST['edit_number'] == $contents[$i]['id']) && ($pass == $contents[$i]['pass']) ){//もし投稿番号と編集対象番号が一致し、さらにパスワードが一致したら
   //編集作業///////////////////////////////////////////////////////////////////////////////
    $sql = 'update tbm5 set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->bindParam(':id', $contents[$i]['id'], PDO::PARAM_INT);
    $stmt->execute();
   /////////////////////////////////////////////////////////////////////////////////////////
    echo "編集が完了しました。"."<br>";
    $cnt++;
   }
  }
  if($cnt == 0)echo "編集対象の投稿が見つからないか、パスワードが間違っています。"."<br>";
 }
}
else{
 if(isset($_POST['SUBMIT']))echo "入力内容が不正です。空の値の投稿はできません。"."<br>";
}

//削除フォームの処理
if(!empty($_POST['DELETE'])){
 $cnt = 0;
//データベースに保存されている値を取得/////////////////////////////////////////////////////
 $sql = 'SELECT * FROM tbm5';
 $stmt = $pdo->query($sql);
 $contents = $stmt->fetchAll();
///////////////////////////////////////////////////////////////////////////////////////////
 $delete = $_POST['DELETE'];
 $pass = $_POST['PASS'];
 for($i =0;$i<count($contents);$i++){
  if($delete == $contents[$i]['id'] && $pass == $contents[$i]['pass']){
//削除処理/////////////////////////////////////////////////////////
   $sql = 'delete from tbm5 where id=:id';
   $stmt = $pdo->prepare($sql);
   $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
   $stmt->execute();
   $cnt++;
   echo "削除が完了しました。"."<br>";
  }
 }
///////////////////////////////////////////////////////////////////
 if($cnt == 0)echo "削除対象の投稿が見つからないか、パスワードが間違っています。"."<br>";
}

//ブラウザに表示
echo "↓掲示板の内容↓"."<br>";

$sql = 'SELECT * FROM tbm5';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
if($results != NULL){//$resultsが空でなければ表示
 foreach ($results as $row){
  echo $row['id'].'<>';
  echo $row['name'].'<>';
  echo $row['comment'].'<>';
  echo $row['date'].'<br>';
  echo "<hr>";
 }
}
?>
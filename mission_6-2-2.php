<html>
<meta charset="UTF-8">
<link rel = "stylesheet" href = "stylesheet_6-2-2.css">
<title>職場のストレスをぶちまける掲示板</title>
<head><script type="text/javascript" src="/iine/cn/cn.php"></script></head>
<header>
 <div class = "keijiban">職場のストレスをぶちまける掲示板</div>
 <form action="mission_6-2-2.php" method="post">
  <div class = "logout"><input type="submit" name = "logout" value="     ログアウト     "></div>
 </form>
 <div class = "clear">ストレス発散用の掲示板です。節度を持った投稿を心得ましょう！行き過ぎた発言はアカウント削除対象となります。</div>
</header>
<?php
session_start();
if(isset($_POST['logout'])){
 $logout_url = "https://tb-210172.tech-base.net/mission_6-2.php?logout=1";
 header("Location:{$logout_url}");
 exit;
}
//ログイン中は常に表示///////////////////////////////////////////////////////
if(!empty($_SESSION['user_name']))$user_name = $_SESSION['user_name'];
else $user_name = "ゲスト";
echo "<div class = \"username\">".$user_name."さん、ようこそ。"."<br>"."</div>";
/////////////////////////////////////////////////////////////////////////////
//データベース生成////////////////////////////////////////////////////////////////////////////////
$dsn = '*****';
	$user = '*****';
	$password = '*****';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//掲示板用のテーブル///////////////////////////////////////////////////////////////////////////////
$sql = "CREATE TABLE IF NOT EXISTS tbm6_textinfo"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass char(32)"
	.");";
	$stmt = $pdo->query($sql);
?>
<div class="clear"><hr /></div>  
<body>
 <div class = "contents">
  <div class = "contents1 edit">
   <form action="mission_6-2-2.php" method="post">
    <div class ="tag">編集対象番号：     </div>
    <input type="text" name="EDIT" value="">
    <br><br>
    <div class ="tag">編集用パス　：     </div>
    <input type="password" name = "PASS"> 
    <br><br>
    <div class="input"><input type="submit" value="     編集     "></div>
   </form>
  </div>
 <?php
 //編集フォームの処理
 if(!empty($_POST['EDIT'])){
  $cnt = 0;
  $edit = $_POST['EDIT'];
  $pass = $_POST['PASS'];
 //データベースに保存されている値を取得/////////////////////////////////////////////////////
  $sql = 'SELECT * FROM tbm6_textinfo';
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
 <div class = "contents2 new">
 <form action="mission_6-2-2.php" method="post">
 <input type="hidden" name = "edit_number" value = "<?php echo $edit ?>"> 
 <?php
 if($edit ==0 || $cnt == 0){//編集モードを経由していない場合は初期化を行う
       $name = "";
       $comment = "【業種】&#13;&#13;【タイトル】&#13;&#13;【本文】";
   }
 ?>
 <div class ="tag"> お名前　　　：       </div>
 <input type="text" name="NAME" value="<?php echo $name; ?>">
 <br><br>
 <div class ="tag">  コメント　　：       </div>
 <textarea name="COMMENT" style="width:150px;height:100px;"><?php echo $comment; ?></textarea>
 <br><br>
 <div class ="tag">投稿用パス　：       </div>
 <input type="password" name = "PASS">
 <br><br>
 <div class="input"><input type="submit"  name = "SUBMIT" value="     送信     "></div>
 </form>
 </div>
 <div class = "contents3 delete">
 <form action="mission_6-2-2.php" method="post">
 <div class ="tag">削除対象番号：       </div>
 <input type="text" name="DELETE" value="">
 <br><br>
 <div class ="tag">削除用パス　：       </div>
 <input type="password" name = "PASS">
 <br><br>
 <div class="input"><input type="submit" value="     削除     "></div>
 </form>
 </div>
</div>
<hr>
</body>
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
   $sql = 'SELECT * FROM tbm6_textinfo';
   $stmt = $pdo->query($sql);
   $contents = $stmt->fetchAll();
///////////////////////////////////////////////////////////////////////////////////////////

//新規投稿処理/////////////////////////////////////////////////////
   $sql = $pdo -> prepare("INSERT INTO tbm6_textinfo (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
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
  $sql = 'SELECT * FROM tbm6_textinfo';
  $stmt = $pdo->query($sql);
  $contents = $stmt->fetchAll();
///////////////////////////////////////////////////////////////////////////////////////////
  $cnt = 0;
  for($i =0;$i<count($contents);$i++){
   if( ($_POST['edit_number'] == $contents[$i]['id']) && ($pass == $contents[$i]['pass']) ){//もし投稿番号と編集対象番号が一致し、さらにパスワードが一致したら
   //編集作業///////////////////////////////////////////////////////////////////////////////
    $sql = 'update tbm6_textinfo set name=:name,comment=:comment,date=:date,pass=:pass where id=:id';
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
 $sql = 'SELECT * FROM tbm6_textinfo';
 $stmt = $pdo->query($sql);
 $contents = $stmt->fetchAll();
///////////////////////////////////////////////////////////////////////////////////////////
 $delete = $_POST['DELETE'];
 $pass = $_POST['PASS'];
 for($i =0;$i<count($contents);$i++){
  if($delete == $contents[$i]['id'] && $pass == $contents[$i]['pass']){
//削除処理/////////////////////////////////////////////////////////
   $sql = 'delete from tbm6_textinfo where id=:id';
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
?>



<?php
//ブラウザに表示
//ページング処理//////////////////////////////////////////////////////////////////////
$sql = 'SELECT * FROM tbm6_textinfo';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
define('MAX','3');
$post_num = count($results);
$max_page = ceil($post_num / MAX);
 
if(!isset($_GET['page_id'])){
    $now = 1;
}else{
    $now = $_GET['page_id'];
}
 
$start_no = ($now - 1) * MAX;
 
$disp_data = array_slice($results, $start_no, MAX, true);
 
foreach($disp_data as $row){
  echo "<div class = \"comment\">";
  echo "<div class = \"title\">"."No.".$row['id'].":".$row['name']."</div>".'<br>';
  echo "<p>".$row['comment']."</p>";
  echo "<p>".$row['date']."</p>";
  echo "<div class=\"ajax-iine\" data-pid=\"button".$row['id']."\" data-tid=\"tpl-sb-black-m\"></div>".'<br>';//共感ボタン
  echo "</div>";
  echo "<hr>";
 }
echo '全件数'. $post_num. '件'. '　'; // 全データ数の表示
 
if($now > 1){ // リンクをつけるかの判定
    echo '<a href=\'/mission_6-2-2.php?page_id='.($now - 1).'\')>前へ</a>'. '　';
} else {
    echo '前へ'. '　';
}
 
for($i = 1; $i <= $max_page; $i++){
    if ($i == $now) {
        echo $now. '　'; 
    } else {
        echo '<a href=\'/mission_6-2-2.php?page_id='. $i. '\')>'. $i. '</a>'. '　';
    }
}
 
if($now < $max_page){ // リンクをつけるかの判定
    echo '<a href=\'/mission_6-2-2.php?page_id='.($now + 1).'\')>次へ</a>'. '　';
} else {
    echo '次へ';
}
echo "<br><br><br><br>"; 
?>
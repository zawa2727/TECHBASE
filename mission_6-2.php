<html>
<meta charset="UTF-8">
<link rel = "stylesheet" href = "stylesheet_6-2.css">
<title>職場のストレスをぶちまける掲示板　トップページ</title>
<header>
ストレス発散用の掲示板です。節度を持った投稿を心得ましょう。<br>
すでにアカウントをお持ちの方はログインを、まだアカウントをお持ちでない方は新規登録を行ってください。
</header>
<p>※システムの都合上、google.co.jp のページ⇒右上のアカウント印クリック⇒「Googleアカウント」⇒セキュリテイ
で「安全性の低いアプリのアクセス」を「安全性の低いアプリの許可: 有効」にしてください。</p>
<p>また、当掲示板のパスワードにはご利用のgmailアドレスに対応するものをご利用ください。</p>

<body>
<?php
session_start();
//データベース生成////////////////////////////////////////////////////////////////////////////////
$dsn = '*****';
	$user = '*****';
	$password = '*****';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//ユーザ登録用のテーブル//////////////////////////////////////////////////////////////////////////
$sql = "CREATE TABLE IF NOT EXISTS tbm6_userinfo"
	." ("
	. "user_name char(32),"
	. "user_pass char(32),"
	. "user_address char(32)"
	.");";
	$stmt = $pdo->query($sql);
//////////////////////////////////////////////////////////////////////////////////////////////////
/*デバッグ用、入力したデータを表示
$sql = 'SELECT * FROM tbm6_userinfo';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['user_name'].',';
		echo $row['user_pass'].',';
		echo $row['user_address'].'<br>';
	echo "<hr>";
	}
*/

//対象アカウントを削除
	$user_name = "*****";
	$sql = 'delete from tbm6_userinfo where user_name=:user_name';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
	$stmt->execute();
?>

<div class = "new_account">
<form action="mission_6-2.php" method="post">
<p>【  新規登録はこちら  】</p>
<p>メールアドレス(@込みで入力してください。gmailのみ対応しています。)</p>
<input type="text" name="U_ADDRESS">
<p>ユーザ名</p>
<input type="text" name="U_NAME">
<p>パスワード(8文字以上入力してください。)</p>
<input type="password" name = "U_PASS"> 
<br><br>
<input type="submit" value="新規登録">
</form>
</div>

<br><br>
<?php
//新規登録フォーム処理
if(!empty($_POST['U_ADDRESS']) && !empty($_POST['U_NAME']) && !empty($_POST['U_PASS'])){
 $duplicate = 0;
 $u_address = $_POST['U_ADDRESS'];
 $u_name = $_POST['U_NAME'];
 $u_pass = $_POST['U_PASS'];
//echo  $u_address.$u_name.$u_pass;
 //DB内のユーザーデータを取得
 $sql = 'SELECT * FROM tbm6_userinfo';
 $stmt = $pdo->query($sql);
 $contents = $stmt->fetchAll();
 /////////////////////////////
 for($i =0;$i<count($contents);$i++){
  if( ($contents[$i]['user_address'] == $u_address) || ($contents[$i]['user_name'] == $u_name))$duplicate++;
}
if($duplicate > 0)echo "アドレスまたはユーザ名がすでに存在します。"."<br>";
if(strlen($u_pass) < 8)echo "パスワードは8文字以上入力してください。"."<br>";
if(($duplicate == 0) && strlen($u_pass) >= 8){
  $karitouroku = 1;//仮登録フラグ
  //メール送信
  $_SESSION['email'] = $u_address;
  $_SESSION['pass'] = $u_pass; 
  $_SESSION['name'] = $u_name; 
  //echo "メールを送信します。";
  require 'send_test.php';
echo "メールを送信しました。";
  }
}
if(!empty($_GET['honntouroku']) && !empty($_GET['name']) && !empty($_GET['pass']) &&!empty($_GET['mail'])){
 $honntouroku = $_GET['honntouroku'];
 $name = $_GET['name'];
 $pass = $_GET['pass'];
 $mail = $_GET['mail'];
}
//本登録完了、DBに３値を保存
if( (!empty($honntouroku)) && ($honntouroku == 1)){
 echo "本登録完了！".$name.$mail.$pass."<br>";
 //ユーザ情報をDBに登録/////////////////////////////////////////////////////
   $sql = $pdo -> prepare("INSERT INTO tbm6_userinfo (user_name, user_address, user_pass) VALUES (:user_name, :user_mail, :user_pass)");
   $sql -> bindParam(':user_name', $name, PDO::PARAM_STR);
   $sql -> bindParam(':user_mail', $mail, PDO::PARAM_STR);
   $sql -> bindParam(':user_pass', $pass, PDO::PARAM_STR);
   $sql -> execute();
////////////////////////////////////////////////////////////////////
}
?>








<div class = "login_account">
<p>【  ログインはこちら  】</p>
<form action="mission_6-2.php" method="post">
<p>メールアドレス</p>
<input type="text" name="L_ADDRESS">
<p>パスワード</p>
<input type="password" name = "L_PASS"> 
<br><br>
<input type="submit" value="ログイン">
</form>
</div>

<br><br>
<?php
//新規登録フォーム処理
if(!empty($_POST['L_ADDRESS']) && !empty($_POST['L_PASS'])){
 $login = 0;//ログインフラグ
 $l_address = $_POST['L_ADDRESS'];
 $l_pass = $_POST['L_PASS'];
 //DB内のユーザーデータを取得
 $sql = 'SELECT * FROM tbm6_userinfo';
 $stmt = $pdo->query($sql);
 $contents = $stmt->fetchAll();
 /////////////////////////////
 for($i =0;$i<count($contents);$i++){
  if( ($contents[$i]['user_address'] == $l_address) && ($contents[$i]['user_pass'] == $l_pass)){
   echo $contents[$i]['user_name']."さん、ようこそ。"."<br>";
   $_SESSION['user_name'] = $contents[$i]['user_name'];
   $login = 1;
 }
}
 $login_success_url = "mission_6-2-2.php";
 if($login == 1){
    header("Location:{$login_success_url}");
    exit;
   }
 else "お探しのアカウントが見つかりませんでした。"."<br>";
}
if(!empty($_GET['logout']))$logout = $_GET['logout'];
if(isset($logout) && $logout == 1){
 echo "<br><br><br>"."ご利用ありがとうございました。"."<br>";
 session_destroy();
}
?>
</body>
</html>

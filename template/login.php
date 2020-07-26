<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$error = '';
$ip_user = trim($_SERVER['REMOTE_ADDR']);
if(!empty($ip_allow) && md5($ip_user) != $ip_allow){
	header('HTTP/1.1 403 Forbidden');
    die('403 Forbidden');
	exit();
}
session_start();
if(isset($_GET['q']) && $_GET['q'] == 'logout'){
	if($auth == 1){
		unset($_SESSION['auth']);
		session_destroy();
	}
	else{
		setcookie("auth", false, time() - 4800, "/");
	}
	header("Location: $admin_page");
}
if(empty($_SESSION['auth'])){$_SESSION['auth'] = '';}
if(empty($_COOKIE['auth'])){$_COOKIE['auth'] = '';}
if(isset($_POST['submit'])){
	if($caplen != 0){
		$post_captcha = strtoupper($_POST['captcha']);
	}
	if($login != trim($_POST['login']) OR $pass != md5(trim($_POST['pass']))){
		$error = '
<br>
<b>Wrong login or password!</b>';
		sleep(1);
	}
	elseif($caplen != 0 && (!isset($_SESSION['captcha']) || empty($_SESSION['captcha']) || $post_captcha != $_SESSION['captcha'])){
		$error = '
<br>
<b>Wrong captcha!</b>';
		sleep(1);
	}
	else{
		if($auth == 1){
			$_SESSION['auth'] = $login;
		}
		else{
			setcookie("auth", md5($ip_user.$pass), time()+60*60*24*365, "/");
		}
		header("Location: $admin_page");
	}
}
if(($auth == 1 && !$_SESSION['auth']) || ($auth == 0 && (!isset($_COOKIE['auth']) || (md5($ip_user.$pass) != $_COOKIE["auth"])))){
	echo '
<!DOCTYPE html>
<html>
<head>
<title>Authorization</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="template/style.css">
<link rel="shortcut icon" href="template/img/favicon.ico">
</head>
<body>
<center>
<br><br>
<div class="align_center bold title">zTDS '.$version.'</div>
<br><br>
<form method="post">
	Login<br>
	<input style="max-width:140px; width:100%;" type="text" name="login" autofocus><br><br>
	Password<br>
	<input style="max-width:140px; width:100%;" type="password" name="pass"><br>';
if($caplen != 0){
	echo'
	<img style="border: 0px solid gray;" src = "template/captcha/captcha.php" width="120" height="40"/><br>
	<input style="max-width:100px; width:100%;" type="text" name="captcha">
	<br>';
}
echo'
	<br>
	<input class="button" type="submit" name="submit" value="Submit">
</form>'.$error.'
</center>
</body>
</html>
	';
	exit();
}
?>
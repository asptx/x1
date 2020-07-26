<!DOCTYPE html>
<html>
<head>
<title>MD5</title>
<meta http-equiv="Content-Type" content="text/html; Charset=utf-8">
<style type="text/css">
body {background-color: #f0f0f0;}
</style>
</head>
<body>
<br>
<center>
<br><br>
<b>Введите пароль</b>
<br><br>
<form name="form" method="post" action="md5.php">
<input size="20" maxlength="100" type="text" name="data"><br><br>
<input type="submit" name="submit" value="Получить хэш MD5">
</form>
<?php
$x = $_POST['data'];
if(!empty($x)){
	echo '<br><br><center>'.md5($x).'</center>';
}
?>
</center>
</body>
</html>
<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'editor'){
	$dg = '';
	if(isset($_GET['f'])){
		$e_file = preg_replace('/[^a-z0-9\._-]/i', '', $_GET['f']);
		if(!file_exists('database/'.$e_file) || $e_file == '.htaccess'){
			$e_file = '';
		}
	}
	else {
		$e_file = '';
	}
	if(isset($_POST['e_file_data']) AND $_POST['button'] == "Submit"){
		file_put_contents('database/'.$e_file, $_POST['e_file_data'], LOCK_EX);
	}
	echo '<br><div class="align_center bold">'.$trans['editor']['ed1'].'</div>
<br>
<div class="align_center">';
	$files = scandir('database');
	$x = 0;
	$y = '';
	while($y != 'end'){
		if(!empty($files[$x])){
			if($files[$x] != "." && $files[$x] != ".." && $files[$x] != ".htaccess"){
				if($files[$x] == $e_file){
					$style = 'class="current" ';
				}
				else{$style = '';}
				$f_n = str_ireplace('.dat', '', $files[$x]);
				$f_n = str_ireplace('_', ' ', $f_n);
				echo '
<a '.$style.' href="'.$admin_page.'?q=editor&f='.$files[$x].'" style="text-decoration:none;">'.$f_n.'</a>';
				if(isset($files[$x + 1])){echo ' | ';}
			}
			$x++;
		}
		else{
			$y = 'end';
		}
	}
	echo '
</div><br>';
	if(!empty($e_file)){
		$e_file_data = file_get_contents('database/'.$e_file);
	}
	else{$e_file_data = '';}
if(!empty($e_file)){echo '
<div class="align_center bold">'.$e_file.'</div>';}
	else{
		echo '
<div class="align_center bold">Выберите файл для редактирования</div>';
	}
	echo '
<br>
<form name="form-2" method="post" action="'.$admin_page.'?q=editor&f='.$e_file.'">
<div class="align_left">
<textarea id="code" name="e_file_data" style="max-width:620px; width:100%;" rows="20">'.$e_file_data.'</textarea>
<br>';
if(!empty($e_file)){echo '
<center><input class="button" type="submit" name="button" id="button" value="Submit"></center><br>';}
else{
	echo '<br>';
}
echo '
</div>
</form>
<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
      lineNumbers: true,
      extraKeys: {"Ctrl-Space": "autocomplete"},
      mode: {name: "javascript", globalVars: true}
    });
</script>
';
}
?>
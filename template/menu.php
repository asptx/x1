<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if(empty($q) && empty($g_id) && empty($s)){$style = 'class="current" ';}
else{$style = '';}
echo'<br>
<a href="'.$admin_page.'?q=logout">'.$trans['left_menu']['lm1'].'</a><br>
<a '.$style.'href="'.$admin_page.'">'.$trans['left_menu']['lm2'].'</a><br>';
if($g_id != 'empty' && !empty($g_id) && empty($s) && $q != 's_create'){echo '
<a href="'.$admin_page.'?q=g_delete&g='.$g_id.'" onclick="return confirm(\'Group: '.$g_name.'\n\nDelete this group?\') ? true : false;">'.$trans['left_menu']['lm3'].'</a><br>';}
if($q == 's_create'){$style = 'class="current" ';}
else{$style = '';}
if(!empty($g_id)){echo '
<a '.$style.'href="'.$admin_page.'?q=s_create&g='.$g_id.'">'.$trans['left_menu']['lm4'].'</a><br>';}
if($g_id != 'empty' && !empty($g_id) && empty($s) && $q != 's_create' && file_exists($log_folder.'/'.$g_id.'.db')){
	$db = new SQLite3($log_folder.'/'.$g_id.'.db');
	$res = $db->query("SELECT * FROM sqlite_master;");
	$array = $res->fetchArray(SQLITE3_ASSOC);
	$db->close();
	if(!empty($array['name'])){
		echo '
<a href="'.$admin_page.'?q=g_del_log&g='.$g_id.'" onclick="return confirm(\'Group: '.$g_name.'\n\nDelete this log?\') ? true : false;">'.$trans['left_menu']['lm9'].'</a><br>';
	}
}
if(!empty($g_id) && !empty($s)){echo '
<a href="'.$admin_page.'?q=s_delete&g='.$g_id.'&s='.$s.'&n='.$s_name.'" onclick="return confirm(\'Group: '.$g_name.'\nStream: '.$s_name.'\n\nDelete this stream?\') ? true : false;">'.$trans['left_menu']['lm5'].'</a><br>';}
if(!empty($g_id) && !empty($s)){
	$db = new SQLite3($log_folder.'/'.$g_id.'.db');
	$res = $db->query("SELECT * FROM sqlite_master;");
	while($array = $res->fetchArray(SQLITE3_ASSOC)){
		$t_temp = $array['name'];
		if($db->querySingle("SELECT * FROM $t_temp WHERE nstream = '$s_name';")){
			echo '
<a href="'.$admin_page.'?q=s_del_log&g='.$g_id.'&s='.$s.'&n='.$s_name.'" onclick="return confirm(\'Group: '.$g_name.'\nStream: '.$s_name.'\n\nDelete this log?\') ? true : false;">'.$trans['left_menu']['lm9'].'</a><br>';
			break;
		}
	}
	$db->close();
}
if($q == 'editor'){$style = 'class="current" ';}
else{$style = '';}
echo '
<a '.$style.'href="'.$admin_page.'?q=editor">'.$trans['left_menu']['lm8'].'</a><br>';
$files = scandir($ini_folder);
$x = 0;
$y = '';
while($y != 'end'){
	if(!empty($files[$x])){
		if($files[$x] != "." && $files[$x] != ".." && $files[$x] != ".htaccess"){
			$a = unserialize(file_get_contents($ini_folder.'/'.$files[$x]));
			$g_n = $a[0]['g_name'];
			$g_s = $a[0]['g_status'];
			$f_n = str_ireplace('.ini', '', $files[$x]);
			if($g_s == '0'){$g_off = 'off';}
			else{$g_off = 'on';}
			if($g_id == $f_n){$g_current = ' current';}
			else{$g_current = '';}
			$g_style = 'class="'.$g_off.$g_current.'"';
			$data[] = array("g_name" => "$g_n", "f_name"=>"$f_n", "g_style"=>"$g_style");
        }
		$x++;
	}
	else{
		$y = 'end';
	}
}
if(isset($data)){
	echo '
<br><b>'.$trans['left_menu']['lm6'].'</b><br>';
	sort($data);
	$x = 0;
	while(!empty($data[$x])){
		echo '
<a '.$data[$x]['g_style'].' href="'.$admin_page.'?g='.$data[$x]['f_name'].'">'.$data[$x]['g_name'].'</a><br>';
		$x++;
	}
}
if(file_exists($ini_folder.'/'.$g_id.'.ini')){
	$g_data = unserialize(file_get_contents($ini_folder.'/'.$g_id.'.ini'));
	$count_s = count($g_data);
	$count_s--;
	if(!empty($g_data[1])){echo '
<br>
<div class="bold">'.$trans['left_menu']['lm7'].'</div>';}
	$x = 1;
	while(!empty($g_data[$x])){
		if($g_data[$x]['s_status'] == '0'){$s_off = 'off';}
		else{$s_off = 'on';}
		if($g_data[$x]['s_name'] == $s_name){$s_current = 'current';}
		else{$s_current = '';}
		$s_style = 'class="'.$s_off.' '.$s_current.'"';
		echo '
<a '.$s_style.' href="'.$admin_page.'?g='.$g_id.'&s='.$x.'">'.$g_data[$x]['s_name'].'</a>';
if($x < $count_s){echo '<a href="'.$admin_page.'?q=s_down&g='.$g_id.'&s='.$x.'" title="down" class="sort_down">&#8595;</a>';}
if($x > 1){echo '<a href="'.$admin_page.'?q=s_up&g='.$g_id.'&s='.$x.'" title="up" class="sort_up">&#8593;</a>';}
echo '<br>';
		$x++;
	}
}
?>
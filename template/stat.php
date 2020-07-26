<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$log = '';
$z = '';
$ch = '';
$date = '';
if($q != 's_delete' && $q != 'g_delete' && !empty($g_id)){
	$db = new SQLite3($log_folder.'/'.$g_id.'.db');
	$db->busyTimeout(5000);
	$db->exec('PRAGMA journal_mode=WAL;');
	if(file_exists($log_folder.'/'.$g_id.'.db') && $g_log != 0){
		$option = '';
		$a = 0;
		while($a != $log_days){
			$table_temp = 'log_'.strtotime(date("d-m-Y", strtotime('-'.$a.' day')));
			if($db->querySingle("SELECT name FROM sqlite_master WHERE name='$table_temp';")){
				$date_temp = $db->querySingle("SELECT date FROM $table_temp;");
				if(!empty($date_temp)){
					if(!empty($s)){$z = '&s='.$s;}
					else{$z = '';}
					$sel = '';
					if($table == $table_temp){$sel = 'selected="selected" ';}
					$option = $option.'<option '.$sel.'value="'.$table_temp.'">'.$date_temp.'</option>';
				}
			}
			$a++;
		}
		if(!empty($option)){
			echo '
<br>
<form style="text-align:left;" name="form-5" method="get" action="'.$admin_page.'">
<input name="g" type="hidden" value="'.$g_id.'">';
			if(!empty($s)){
				echo '
<input name="s" type="hidden" value="'.$s.'">';
			}
			echo '
<select type="hidden" name="t" size = "1">';
			echo $option;
			echo '
</select>
<input style="width:50px;" type="submit" value="View">
</form>';
		}
		if(isset($_GET['t'])){$table = $_GET['t'];}
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table'")){
			$date = $db->querySingle("SELECT date FROM $table;");
			if(!empty($s)){
				$query = "SELECT COUNT(*) FROM $table WHERE nstream = '$s_name' AND bot = '$empty'";
				$stat_header = $trans['right_menu']['rm1'];
				st();
	        }
			else{
				$query = "SELECT COUNT(*) FROM $table WHERE bot = '$empty'";
				$stat_header = $trans['right_menu']['rm1'];
				st();
			}
		}
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table';")){
			if(!empty($s)){$st = '&s='.$s;}
			else{$st = '';}
			echo '
<br><a href="'.$admin_page.'?q=log&g='.$g_id.$st.'&t='.$table.'">'.$trans['right_menu']['rm10'].'</a><br>
<a href="'.$admin_page.'?q=countries&g='.$g_id.$st.'&t='.$table.'">'.$trans['right_menu']['rm14'].'</a><br>
<a href="'.$admin_page.'?q=sources&g='.$g_id.$st.'&t='.$table.'">'.$trans['right_menu']['rm11'].'</a><br>';
			if(empty($s)){
				$count_empty = $db->querySingle("SELECT COUNT(*) FROM $table WHERE se = '$empty';");
				$count_all = $db->querySingle("SELECT COUNT(*) FROM $table;");
				if($q == 'se'){$se_current = 'color:#b40404;';}
				else{$se_current = 'color:#000000;';}
				if($count_empty != $count_all){
					echo '
<a style="'.$se_current.'" href="'.$admin_page.'?q=se&g='.$g_id.$st.'&t='.$table.'">'.$trans['right_menu']['rm13'].'</a><br>';
				}
			}
		}
	}
	if(empty($s)){
		if(file_exists($keys_folder.'/'.$g_name.'/'.$date.'.dat') || file_exists($keys_folder.'/'.$g_name.'/'.$date.'-se.dat')){
			if($q == 'keys'){$style = 'class="current"';}
			else{$style = '';}
			echo '
<a '.$style.' href="'.$admin_page.'?q=keys&g='.$g_id.'&t='.$table.'">'.$trans['right_menu']['rm12'].'</a><br>';
		}
	}
	$db->close();
	echo '<br>';
}
?>
<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'log' || $q == 'export' || $q == 'export_all'){
	if(isset($_GET['n']) && is_numeric($_GET['n'])){
		$log_limit = $_GET['n'];
	}
	if(!isset($_GET['pb'])){
		$_GET['pb'] = 'off';
	}
	if(isset($_GET['o']) && $_GET['o'] == 'last'){
		$order = 'ORDER BY id DESC';
	}
	else{
		$_GET['o'] = 'first';
		$order = '';
	}
	$db = new SQLite3($log_folder.'/'.$g_id.'.db');
	$db->busyTimeout(5000);
	$db->exec('PRAGMA journal_mode=WAL;');
	$date = $db->querySingle("SELECT date FROM $table;");
	if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table'")){
		if(empty($s_name) && $_GET['pb'] != 'on'){
			$res = $db->query("SELECT * FROM $table $order LIMIT $log_limit;");
		}
		if(!empty($s_name) && $_GET['pb'] != 'on'){
			$res = $db->query("SELECT * FROM $table WHERE nstream = '$s_name' $order LIMIT $log_limit;");
		}
		if(empty($s_name) && $_GET['pb'] == 'on'){
			$res = $db->query("SELECT * FROM $table WHERE postback != '$empty' $order LIMIT $log_limit;");
		}
		if(!empty($s_name) && $_GET['pb'] == 'on'){
			$res = $db->query("SELECT * FROM $table WHERE postback != '$empty' AND nstream = '$s_name' $order LIMIT $log_limit;");
		}
		if($q == 'log'){
		echo "<!DOCTYPE html>
<html>
<head>
<title>zTDS $version</title>
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<link rel=\"stylesheet\" href=\"template/style.css\">
<link rel=\"stylesheet\" type=\"text/css\" href=\"template/tables/jquery.dataTables.css\">
<link rel=\"shortcut icon\" href=\"template/img/favicon.ico\">
<script type=\"text/javascript\" src=\"template/js/jquery-latest.min.js\"></script>
<script type=\"text/javascript\" src=\"template/tables/jquery.dataTables.js\"></script>
</head>
<body>
<div class=\"header\">
<div class=\"logo align_left\">zTDS $version</div>
</div>
<div style=\"height:auto !important; min-height:75vh; margin:5px; padding:5px; text-align:center; font-size:".$log_fs."px;\"><br>";
		if(!empty($s_name)){echo "
<div class=\"align_center bold\">Group: $g_name | Stream: $s_name</div>";}
		else{echo "
<div class=\"align_center bold\">Group: $g_name</div>";}
		echo '<br>
<center><a href="'.$admin_page.'">Main</a> | <a href="javascript:location.reload();">Update</a> | <a href="javascript:history.back();">Back</a> | <a href="'.$admin_page.'?q=export&g='.$g_id.'&s='.$s.'&t='.$table.'&pb='.$_GET['pb'].'">Export</a></center><br>';
		if(file_exists($log_folder.'/'.$g_id.'.db')){
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
		}
		echo '
<form style="text-align:center;" name="form-3" method="get" action="'.$admin_page.'">
<input name="q" type="hidden" value="log">
<input name="g" type="hidden" value="'.$g_id.'">';
		if(!empty($s)){
			echo '
<input name="s" type="hidden" value="'.$s.'">';
		}
		echo '
<input name="pb" class="checkbox" type="checkbox"'; if($_GET['pb'] == 'on'){echo ' checked="checked"';} echo '>PB <select type="hidden" name="t" size = "1">';
		echo $option;
		echo '
</select>';
echo '<select type="hidden" name="o" size = "1">
<option'; if($_GET['o'] == 'first'){echo ' selected="selected"';} echo ' value="first">first</option>
<option'; if($_GET['o'] == 'last'){echo ' selected="selected"';} echo ' value="last">last</option>
</select>
<input style="max-width:30px; width:100%;" name="n" type="text" value="'.$log_limit.'" maxlength="7">
<input style="width:50px;" type="submit" value="View">
</form><br>';
		echo '
<table id="example" class="cell-border display compact" cellspacing="0">
<thead>
<tr>
<th>ID</th>
<th>Time</th>
<th>Group</th>
<th>Stream</th>
<th>Out</th>
<th>Keyword</th>
<th>Redirect</th>
<th>Device</th>
<th>WAP</th>
<th>Country</th>
<th>City</th>
<th>Region</th>
<th>Language</th>
<th>Uniq</th>
<th>Bot</th>
<th>IP</th>
<th>Domain</th>
<th>Referer</th>
<th>Useragent</th>
<th>SE</th>
<th>Postback</th>
</tr>
</thead>
<tfoot>
<tr>
<th>ID</th>
<th>Time</th>
<th>Group</th>
<th>Stream</th>
<th>Out</th>
<th>Keyword</th>
<th>Redirect</th>
<th>Device</th>
<th>WAP</th>
<th>Country</th>
<th>City</th>
<th>Region</th>
<th>Language</th>
<th>Uniq</th>
<th>Bot</th>
<th>IP</th>
<th>Domain</th>
<th>Referer</th>
<th>Useragent</th>
<th>SE</th>
<th>Postback</th>
</tr>
</tfoot>
<tbody>';
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$pb = '';
			if($array['postback'] != $empty){
				$postback = unserialize($array['postback']);
				$x = 0;
				while(!empty($postback[$x])){
					$pb = $pb.$postback[$x].'<br>';
					$x++;
				}
			}
			else{
				$pb = $array['postback'];
			}
			echo "
<tr align=\"center\">
<td>".$array['id']."</td>
<td>".$array['time']."</td>
<td class=\"log\">".$array['ngroup']."</td>
<td class=\"log\">".$array['nstream']."</td>
<td class=\"log\">".$array['out']."</td>
<td class=\"log\">".$array['keyword']."</td>
<td>".$array['redirect']."</td>
<td>".$array['device']."</td>
<td>".$array['operator']."</td>
<td>".$array['country']."</td>
<td>".$array['city']."</td>
<td>".$array['region']."</td>
<td>".$array['lang']."</td>
<td>".$array['uniq']."</td>
<td>".$array['bot']."</td>
<td class=\"log\">".$array['ipuser']."</td>
<td class=\"log\">".$array['domain']."</td>
<td class=\"log\">".$array['referer']."</td>
<td class=\"log\">".$array['useragent']."</td>
<td>".$array['se']."</td>
<td>".$pb."</td>
</tr>";
		}
		echo '
</tbody>
</table>
<script type="text/javascript" class="init">
$(document).ready(function(){
	$("#example").DataTable( {
		"paging":false,
		"searching":false,
		"info":false,
	} );
} );
</script>
</div>
<br><br>
<div style="clear:both;"></div>
<div class="bottom">&copy; root</div>';
		if($debug == 1){echo '
<div class="debug">'.(microtime(true) - $start).' s.</div>';
		}
		echo '
</body>
</html>';
		}
		if($q == 'export'){
			if(empty($s_name) && $_GET['pb'] != 'on'){
				$res = $db->query("SELECT * FROM $table;");
			}
			if(!empty($s_name) && $_GET['pb'] != 'on'){
				$res = $db->query("SELECT * FROM $table WHERE nstream = '$s_name';");
			}
			if(empty($s_name) && $_GET['pb'] == 'on'){
				$res = $db->query("SELECT * FROM $table WHERE postback != '$empty';");
			}
			if(!empty($s_name) && $_GET['pb'] == 'on'){
				$res = $db->query("SELECT * FROM $table WHERE postback != '$empty' AND nstream = '$s_name';");
			}
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$date.xls");
			header("Content-Transfer-Encoding: binary");
			echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">.max_td{max-width:200px; word-wrap:break-word;}</style>
</head>
<body>
<table border="1">
<thead>
<tr>
<th>ID</th>
<th>Time</th>
<th>Group</th>
<th>Stream</th>
<th>Out</th>
<th>Keyword</th>
<th>Redirect</th>
<th>Device</th>
<th>WAP</th>
<th>Country</th>
<th>City</th>
<th>Region</th>
<th>Language</th>
<th>Uniq</th>
<th>Bot</th>
<th>IP</th>
<th>Domain</th>
<th>Referer</th>
<th>Useragent</th>
<th>SE</th>
<th>Postback</th>
</tr>
</thead>
<tbody>';
			while($array = $res->fetchArray(SQLITE3_ASSOC)){
				$pb = '';
				if($array['postback'] != $empty){
					$postback = unserialize($array['postback']);
					$x = 0;
					while(!empty($postback[$x])){
						$pb = $pb.$postback[$x].'<br>';
						$x++;
					}
				}
				else{
					$pb = $array['postback'];
				}
				echo "
<tr align=\"center\">
<td>".$array['id']."</td>
<td>".$array['time']."</td>
<td class=\"max_td\">".$array['ngroup']."</td>
<td class=\"max_td\">".$array['nstream']."</td>
<td class=\"max_td\">".$array['out']."</td>
<td class=\"max_td\">".$array['keyword']."</td>
<td>".$array['redirect']."</td>
<td>".$array['device']."</td>
<td>".$array['operator']."</td>
<td>".$array['country']."</td>
<td>".$array['city']."</td>
<td>".$array['region']."</td>
<td>".$array['lang']."</td>
<td>".$array['uniq']."</td>
<td>".$array['bot']."</td>
<td class=\"max_td\">".$array['ipuser']."</td>
<td>".$array['domain']."</td>
<td class=\"max_td\">".$array['referer']."</td>
<td class=\"max_td\">".$array['useragent']."</td>
<td>".$array['se']."</td>
<td>".$pb."</td>
</tr>";
			}
			echo '
</tbody>
</table>
</body>
</html>';
		}
	}
	$db->close();
	exit();
}
?>
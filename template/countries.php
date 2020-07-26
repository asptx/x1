<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'countries'){
	$db = new SQLite3($log_folder.'/'.$g_id.'.db');
	$db->busyTimeout(5000);
	$db->exec('PRAGMA journal_mode=WAL;');
	$date = $db->querySingle("SELECT date FROM $table;");
	if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table'")){
		echo '<!DOCTYPE html>
<html>
<head>
<title>zTDS '.$version.'</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="template/style.css">
<link rel="stylesheet" type="text/css" href="template/tables/jquery.dataTables.css">
<link rel="shortcut icon" href="template/img/favicon.ico">
<script type="text/javascript" src="template/js/jquery-latest.min.js"></script>
<script type="text/javascript" src="template/tables/jquery.dataTables.js"></script>
</head>
<body>
<div class="header">
<div class="logo align_left">zTDS '.$version.'</div>
</div>
<div style="height:auto !important; min-height:75vh; margin:5px; padding:5px; text-align:center;"><br>';
		if(!empty($d)){$dn = " | Domain: $d"; $dq = " AND domain = '$d'";}
		else{$dn = ''; $dq = '';}
		if(!empty($s_name)){echo "
<div class=\"align_center bold\">Group: $g_name | Stream: $s_name$dn</div>";}
		else{echo "
<div class=\"align_center bold\">Group: $g_name$dn</div>";}
		echo '
<br>
<center><a href="'.$admin_page.'">Main</a> | <a href="javascript:location.reload();">Update</a> | <a href="javascript:history.back();">Back</a></center><br>';
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
<form style="text-align:center;" name="form-4" method="get" action="'.$admin_page.'">
<input name="q" type="hidden" value="countries">
<input name="g" type="hidden" value="'.$g_id.'">';
		if(!empty($s)){
			echo '
<input name="s" type="hidden" value="'.$s.'">';
		}
		if(!empty($d)){
			echo '
<input name="d" type="hidden" value="'.$d.'">';
		}
		echo '
<select type="hidden" name="t" size = "1">';
		echo $option;
		echo '
</select>
<input style="width:50px;" type="submit" value="View">
</form><br>';
		$count_empty = $db->querySingle("SELECT COUNT(*) FROM $table WHERE se = '$empty';");
		$count_all = $db->querySingle("SELECT COUNT(*) FROM $table;");
		if($count_empty == $count_all){
			$width_table = 'width="30%" ';
			echo '
<table id="example" class="cell-border display compact" '.$width_table.'cellspacing="0">
<thead>
<tr>
<th>Country</th>
<th>Visitors</th>
<th>Unique</th>
</tr>
</thead>
<tfoot>
<tr>
<th>Country</th>
<th>Visitors</th>
<th>Unique</th>
</tr>
</tfoot>';
			if(empty($s)){
				$res = $db->query("SELECT * FROM $table WHERE bot = '$empty'$dq");
				$st = '';
			}
			else{
				$res = $db->query("SELECT * FROM $table WHERE nstream = '$s_name' AND bot = '$empty'$dq");
				$st = "&s=$s";
			}
			$a = array();
			while($array = $res->fetchArray(SQLITE3_ASSOC)){
				$country = trim($array['country']);
				if(!in_array($country, $a)){
					$a[] = $array['country'];
				}
			}
			if(isset($a)){
				asort($a);
				foreach($a as $country){
					if(!empty($s)){
						$query = "SELECT COUNT (*) FROM $table WHERE country = '$country' AND nstream = '$s_name' AND bot = '$empty'$dq";
					}
					else{
						$query = "SELECT COUNT (*) FROM $table WHERE country = '$country' AND bot = '$empty'$dq";
					}
					$countries_visitors = $db->querySingle("$query;");
					if($countries_visitors == 0){$countries_visitors = $empty;}
					$countries_uniq = $db->querySingle("$query AND uniq = 'yes';");
					if($countries_uniq == 0){$countries_uniq = $empty;}
					country_names($country);
					$country = strtoupper($country);
					$td = "<td title=\"$cn\">$country</td>
<td>$countries_visitors</td>
<td>$countries_uniq</td>";
					echo "
<tr align=\"center\">
$td
</tr>";
				}
			}
		}
		else{
			$width_table = 'width="30%" ';
			echo '
<table id="example" class="cell-border display compact" '.$width_table.'cellspacing="0">
<thead>
<tr>
<th>Country</th>
<th>Visitors</th>
<th>Unique</th>
<th>SE</th>
</tr>
</thead>
<tfoot>
<tr>
<th>Country</th>
<th>Visitors</th>
<th>Unique</th>
<th>SE</th>
</tr>
</tfoot>';
			if(empty($s)){
				$res = $db->query("SELECT * FROM $table WHERE bot = '$empty'$dq");
				$st = '';
			}
			else{
				$res = $db->query("SELECT * FROM $table WHERE nstream = '$s_name' AND bot = '$empty'$dq");
				$st = "&s=$s";
			}
			$a = array();
			while($array = $res->fetchArray(SQLITE3_ASSOC)){
				$country = trim($array['country']);
				if(!in_array($country, $a)){
					$a[] = $array['country'];
				}
			}
			if(isset($a)){
				asort($a);
				foreach($a as $country){
					if(!empty($s)){
						$query = "SELECT COUNT (*) FROM $table WHERE country = '$country' AND nstream = '$s_name' AND bot = '$empty'$dq";
					}
					else{
						$query = "SELECT COUNT (*) FROM $table WHERE country = '$country' AND bot = '$empty'$dq";
					}
					$countries_visitors = $db->querySingle("$query;");
					if($countries_visitors == 0){$countries_visitors = $empty;}
					$countries_uniq = $db->querySingle("$query AND uniq = 'yes';");
					if($countries_uniq == 0){$countries_uniq = $empty;}
					$countries_se = $db->querySingle("$query AND se != '$empty' $sqluniq;");
					if($countries_se == 0){$countries_se = $empty;}
					country_names($country);
					$country = strtoupper($country);
					$td = "<td title=\"$cn\">$country</td>
<td>$countries_visitors</td>
<td>$countries_uniq</td>
<td>$countries_se</td>";
					echo "
<tr align=\"center\">
$td
</tr>";
				}
			}
		}
	}
	$sort = 2;
	$order = 'true';
	echo '
</tbody>
</table>
<script type="text/javascript" class="init">
$(document).ready(function(){
	$("#example").DataTable( {
		"paging": false,
		"searching": false,
		"info": false,
		"ordering": '.$order.',
		"order": [['.$sort.', "desc"]]
	} );
} );
</script>
<br><br>
</div>
<div style="clear:both;"></div>
<div class="bottom">&copy; root</div>';
	if($debug == 1){echo '
<div class="debug">'.(microtime(true) - $start).' s.</div>';
	}
	echo '
</body>
</html>';
	$db->close();
	exit();
}
?>
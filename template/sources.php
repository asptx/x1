<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
if($q == 'sources' && empty($d)){
	$db = new SQLite3($log_folder.'/'.$g_id.'.db');
	$db->busyTimeout(5000);
	$db->exec('PRAGMA journal_mode=WAL;');
	$date = $db->querySingle("SELECT date FROM $table;");
	if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table'")){
		$se = '';
		$count_empty = $db->querySingle("SELECT COUNT(*) FROM $table WHERE se = '$empty';");
		$count_all = $db->querySingle("SELECT COUNT(*) FROM $table;");
		if($count_empty != $count_all){$se = 1;}
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
		if(!empty($s_name)){echo "
<div class=\"align_center bold\">Group: $g_name | Stream: $s_name</div>";}
		else{echo "
<div class=\"align_center bold\">Group: $g_name</div>";}
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
<input name="q" type="hidden" value="sources">
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
</form><br>';
		if($stat_op == 1){
			$th = '<th>'.$trans['sources']['so1'].'</th>
<th>'.$trans['sources']['so2'].'</th>
<th>'.$trans['sources']['so3'].'</th>';
			if($se == 1){$th = $th.'
<th>'.$trans['sources']['so8'].'</th>';}
			$th = $th.'
<th>'.$trans['sources']['so4'].'</th>
<th>'.$trans['sources']['so5'].'</th>
<th>'.$trans['sources']['so6'].'</th>
<th>'.$trans['sources']['so7'].'</th>';
			$width_table = 'width="30%" ';
		}
		if($stat_op == 0){
			$th = '<th>'.$trans['sources']['so1'].'</th>
<th>'.$trans['sources']['so2'].'</th>
<th>'.$trans['sources']['so3'].'</th>';
			if($se == 1){$th = $th.'<th>'.$trans['sources']['so8'].'</th>';}
			$th = $th.'<th>'.$trans['sources']['so7'].'</th>';
			$width_table = 'width="30%" ';
		}
		echo '
<table id="example" class="cell-border display compact" '.$width_table.'cellspacing="0">
<thead>
<tr>
'.$th.'
</tr>
</thead>
<tfoot>
<tr>
'.$th.'
</tr>
</tfoot>';
		if(empty($s)){
			$res = $db->query("SELECT * FROM $table WHERE bot = '$empty';");
			$st = '';
		}
		else{
			$res = $db->query("SELECT * FROM $table WHERE nstream = '$s_name' AND bot = '$empty';");
			$st = "&s=$s";
		}
		$a = array();
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$domain = trim($array['domain']);
			if(!in_array($domain, $a)){
				$a[] = $array['domain'];
			}
		}
		if(isset($a)){
			asort($a);
			foreach($a as $domain){
				if(!empty($s)){
					$query = "SELECT COUNT (*) FROM $table WHERE domain = '$domain' AND nstream = '$s_name' AND bot = '$empty'";
				}
				else{
					$query = "SELECT COUNT (*) FROM $table WHERE domain = '$domain' AND bot = '$empty'";
				}
				$sources_visitors = $db->querySingle("$query;");
				if($sources_visitors == 0){$sources_visitors = $empty;}
				$sources_unique = $db->querySingle("$query AND uniq = 'yes';");
				if($sources_unique == 0){$sources_unique = $empty;}
				if($se == 1){
					$sources_se = $db->querySingle("$query AND se != '$empty' $sqluniq;");
					if($sources_se == 0){$sources_se = $empty;}
				}
				if($stat_op != 0){
					$sources_computers = $db->querySingle("$query AND device = 'computer' $sqluniq;");
					if($sources_computers == 0){$sources_computers = $empty;}
					$sources_tablets = $db->querySingle("$query AND device = 'tablet' $sqluniq;");
					if($sources_tablets == 0){$sources_tablets = $empty;}
					$sources_phones = $db->querySingle("$query AND device = 'phone' $sqluniq;");
					if($sources_phones == 0){$sources_phones = $empty;}
				}
				$sources_wap = $db->querySingle("$query AND operator != '$empty' $sqluniq;");
				if($sources_wap == 0){$sources_wap = $empty;}
				if($stat_op == 1){
					$td = "<td style=\"white-space:nowrap;\"><a href=\"$admin_page?q=sources&g=$g_id$st&d=$domain&t=$table\">$domain</a> <a style=\"text-decoration:none;\" title=\"Countries\" href=\"$admin_page?q=countries&g=$g_id$st&d=$domain&t=$table\">&#9872;</a></td>
<td>$sources_visitors</td>
<td>$sources_unique</td>";
					if($se == 1){$td = $td."
<td>$sources_se</td>";}
					$td = $td."
<td>$sources_computers</td>
<td>$sources_tablets</td>
<td>$sources_phones</td>
<td>$sources_wap</td>";
				}
				if($stat_op == 0){
					$td = "<td style=\"white-space:nowrap;\"><a href=\"$admin_page?q=sources&g=$g_id$st&d=$domain&t=$table\">$domain</a> <a style=\"text-decoration:none;\" title=\"Countries\" href=\"$admin_page?q=countries&g=$g_id$st&d=$domain&t=$table\">&#9872;</a></td>
<td>$sources_visitors</td>
<td>$sources_unique</td>";
					if($se == 1){$td = $td."
<td>$sources_se</td>";}
					$td = $td."
<td>$sources_wap</td>";
				}
				echo "
<tr align=\"center\">
$td
</tr>";
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
if($q == 'sources' && !empty($d)){
	echo '<!DOCTYPE html>
<html>
<head>
<title>zTDS '.$version.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="template/style.css">
<link rel="stylesheet" type="text/css" href="template/tables/jquery.dataTables.css">
<link rel="shortcut icon" href="template/img/favicon.ico">
<script type="text/javascript" src="template/js/jquery-latest.min.js"></script>
<script type="text/javascript" src="template/tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<div class="header">
<div class="logo align_left">zTDS '.$version.'</div>
</div>
<div style="margin:5px; padding:5px; text-align:center;"><br>';
	if(!empty($s_name)){echo "
<div class=\"align_center bold\">Group: $g_name | Stream: $s_name | Domain: $d</div>";}
	else{echo "
<div class=\"align_center bold\">Group: $g_name | Domain: $d</div>";}
	echo '
<br>
<a href="'.$admin_page.'">Main</a> | <a href="javascript:location.reload();">Update</a> | <a href="javascript:history.back();">Back</a><br>';
	$db = new SQLite3($log_folder.'/'.$g_id.'.db');
	$db->busyTimeout(5000);
	$db->exec('PRAGMA journal_mode=WAL;');
	$dt = '';
	$se = '';
	while($chart_days != -1){
		$table_temp = 'log_'.strtotime(date("d-m-Y", strtotime('-'.$chart_days.' day')));
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table_temp';")){
			$dt = $db->querySingle("SELECT date FROM $table_temp");
			$dt = explode("-", $dt);
			if(isset($dt[0])){$dt = $dt[0];}
			else{$dt = date("d", strtotime('-'.$chart_days.' day'));}
			if(!empty($s)){
				$query = "SELECT COUNT (*) FROM $table_temp WHERE domain = '$d' AND nstream = '$s_name' AND bot = '$empty'";
			}
			else{
				$query = "SELECT COUNT (*) FROM $table_temp WHERE domain = '$d' AND bot = '$empty'";
			}
			$ch_visitors = $db->querySingle("$query;");
			$ch_hosts = $db->querySingle("$query AND uniq = 'yes';");
			$ch_wap = $db->querySingle("$query AND operator != '$empty' $sqluniq;");
			if($chart_bots == 1){
				if(!empty($s)){
					$ch_bots = $db->querySingle("SELECT COUNT(*) FROM $table_temp WHERE domain = '$d' AND nstream = '$s_name' AND bot != '$empty' $sqluniq;");
				}
				else{
					$ch_bots = $db->querySingle("SELECT COUNT(*) FROM $table_temp WHERE domain = '$d' AND bot != '$empty' $sqluniq;");
				}
			}
			else {$ch_bots = 0;}
			if(empty($dg)){
				$dg = '[\''.$dt.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
			}
			else{
				$dg = $dg.',[\''.$dt.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
			}
			if($se != 1){
				$count_empty = $db->querySingle("SELECT COUNT(*) FROM $table_temp WHERE se = '$empty';");
				$count_all = $db->querySingle("SELECT COUNT(*) FROM $table_temp;");
				if($count_empty != $count_all){$se = 1;}
			}
			$ch_google = $db->querySingle("$query AND se = 'google' $sqluniq;");
			$ch_yandex = $db->querySingle("$query AND se = 'yandex' $sqluniq;");
			$ch_mail = $db->querySingle("$query AND se = 'mail' $sqluniq;");
			$ch_yahoo = $db->querySingle("$query AND se = 'yahoo' $sqluniq;");
			$ch_bing = $db->querySingle("$query AND se = 'bing' $sqluniq;");
			if(empty($dg_se)){
				$dg_se = '[\''.$dt.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
			}
			else{
				$dg_se = $dg_se.',[\''.$dt.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
			}
		}
		else{
			if(empty($dg)){
				$dg = '[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0]';
			}
			else{
				$dg = $dg.',[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0]';
			}
			if(empty($dg_se)){
				$dg_se = '[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0,0]';
			}
			else{
				$dg_se = $dg_se.',[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0,0]';
			}
		}
		$chart_days--;
	}
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
<br>
<form style="text-align:center;" name="form-4" method="get" action="'.$admin_page.'">
<input name="q" type="hidden" value="sources">
<input name="g" type="hidden" value="'.$g_id.'">';
	if(!empty($s)){
		echo '
<input name="s" type="hidden" value="'.$s.'">';
	}
	echo '
<input name="d" type="hidden" value="'.$d.'">
<select type="hidden" name="t" size = "1">';
	echo $option;
	echo '
</select>
<input style="width:50px;" type="submit" value="View">
</form>
<br>
<center><div id="curve_chart" style="max-width:600px; height:'.$chart_weight.'px; border:1px solid #848484;"></div></center>';
if($se == 1){
	echo'<br>
<center><div id="curve_chart_se" style="max-width:600px; height:'.$chart_weight.'px; border:1px solid #848484;"></div></center>';
}
	echo '</div>';
	$db->close();
	if(empty($dg)){$dg = '[0,0,0,0,0]';}
	if(empty($dg_se)){$dg_se = '[0,0,0,0,0,0]';}
?>
<br><br>
<script type="text/javascript">
      google.charts.load('44', {'packages':['corechart']});
      google.charts.setOnLoadCallback(init);
	  function init () {drawChart();<?php if($se == 1){echo 'drawChart_se();';} ?>}
      function drawChart(){
        var data = google.visualization.arrayToDataTable([['Day', '<?php echo $trans['chart']['ch1']; ?>', '<?php echo $trans['chart']['ch2']; ?>', '<?php echo $trans['chart']['ch3']; ?>', '<?php echo $trans['chart']['ch4']; ?>'], <?php echo $dg; ?>]);
        var options = {
          title: 'Statistics',
          curveType: 'none',
          legend:{ position: 'bottom' },
		  chartArea:{left:60,right:20,top:20,bottom:40,width:'100%',height:'100%'},
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
      }
	  function drawChart_se(){
        var data = google.visualization.arrayToDataTable([['Day', 'Google', 'Yandex', 'Mail.ru', 'Yahoo', 'Bing'], <?php echo $dg_se; ?>]);
        var options = {
          title: 'Search engines',
          curveType: 'none',
          legend:{ position: 'bottom' },
		  chartArea:{left:60,right:20,top:20,bottom:40,width:'100%',height:'100%'},
        };
        var chart = new google.visualization.LineChart(document.getElementById('curve_chart_se'));
        chart.draw(data, options);
      }
</script>
<div style="clear:both;"></div>
<div class="bottom">&copy; root</div>
<?php
if($debug == 1){
	echo '<div class="debug">'.(microtime(true) - $start).' s.</div>';
}
?>
</body>
</html>
<?php exit(); }?>
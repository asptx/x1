<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$g_uniq_time = $g_uniq_time / 3600;
$g_f_time = $g_f_time / 3600;
if($s == '' && $q != 's_create' && $q != 'editor'){
	echo '<br>';
	if(!empty($error)){echo '<div class="align_center red bold">'.$error.'</div>';}
	if($q == 'g_delete' || empty($g_id)){echo '<div class="align_center bold">'.$trans['group']['g1'].'</div>';}
	if(!empty($g_id) && empty($error)){
		$db = new SQLite3($log_folder.'/'.$g_id.'.db');
		$db->busyTimeout(5000);
		$db->exec('PRAGMA journal_mode=WAL;');
		$d = '';
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table';")){
			$k = $db->querySingle("SELECT date FROM $table;");
		}
		while($chart_days != -1){
			$table_temp = 'log_'.strtotime(date("d-m-Y", strtotime('-'.$chart_days.' day')));
			if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table_temp';")){
				$d = $db->querySingle("SELECT date FROM $table_temp");
				$d = explode("-", $d);
				if(isset($d[0])){$d = $d[0];}
				else{$d = date("d", strtotime('-'.$chart_days.' day'));}
				$query = "SELECT COUNT(*) FROM $table_temp WHERE bot = '$empty'";
				$ch_visitors = $db->querySingle("$query;");
				$ch_hosts = $db->querySingle("$query AND uniq = 'yes';");
				$ch_wap = $db->querySingle("$query AND operator != '$empty' $sqluniq;");
				if($chart_bots == 1){
					$ch_bots = $db->querySingle("SELECT COUNT(*) FROM $table_temp WHERE bot != '$empty' $sqluniq;");
				}
				else {$ch_bots = 0;}
				if(empty($dg)){
					$dg = '[\''.$d.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
				}
				else{
					$dg = $dg.',[\''.$d.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
				}
				if($q == 'se'){
					$ch_google = $db->querySingle("$query AND se = 'google' $sqluniq;");
					$ch_yandex = $db->querySingle("$query AND se = 'yandex' $sqluniq;");
					$ch_mail = $db->querySingle("$query AND se = 'mail' $sqluniq;");
					$ch_yahoo = $db->querySingle("$query AND se = 'yahoo' $sqluniq;");
					$ch_bing = $db->querySingle("$query AND se = 'bing' $sqluniq;");
					if(empty($dg_se)){
						$dg_se = '[\''.$d.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
					}
					else{
						$dg_se = $dg_se.',[\''.$d.'\','.$ch_google.','.$ch_yandex.','.$ch_mail.','.$ch_yahoo.','.$ch_bing.']';
					}
				}
			}
			else{
				if(empty($dg)){
					$dg = '[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0]';
				}
				else{
					$dg = $dg.',[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0]';
				}
				if($q == 'se'){
					if(empty($dg_se)){
						$dg_se = '[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0,0]';
					}
					else{
						$dg_se = $dg_se.',[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0,0]';
					}
				}
			}
			$chart_days--;
		}
		$db->close();
		echo '<div class="align_center bold">'.$trans['group']['g2'].': '.$g_name.'</div>';
	}
	echo'
<br>
<center><div id="curve_chart" style="max-width:100%; height:'.$chart_weight.'px; border:1px solid #848484;"></div></center>';
	if($q != 'keys' && $q != 'se'){
		if(get_magic_quotes_gpc() == 1){
			$g_out = stripslashes($g_out);
		}
		echo '
<br>
<form name="form-1" method="post" action="'.$admin_page.'">
<div class="param">'.$trans['group']['g3'].'<br><input name="g_name" type="text" value="'.$g_name.'" maxlength="100"></div>
<div class="param">'.$trans['group']['g4'].'<br><input name="g_id" type="text" value="'.$g_id.'" maxlength="100"></div>
<div class="param">'.$trans['group']['g5'].' <select name="g_redirect" size = "1">
<option'; if($g_redirect == 'api'){echo ' selected="selected"';} echo ' value="api">API</option>
<option'; if($g_redirect == 'curl'){echo ' selected="selected"';} echo ' value="curl">CURL</option>
<option'; if($g_redirect == 'http_redirect'){echo ' selected="selected"';}  echo ' value="http_redirect">HTTP redirect</option>
<option'; if($g_redirect == 'iframe'){echo ' selected="selected"';} echo ' value="iframe">Iframe</option>
<option'; if($g_redirect == 'iframe_redirect'){echo ' selected="selected"';} echo ' value="iframe_redirect">Iframe redirect</option>
<option'; if($g_redirect == 'iframe_selection'){echo ' selected="selected"';} echo ' value="iframe_selection">Iframe selection</option>
<option'; if($g_redirect == 'js_redirect'){echo ' selected="selected"';} echo ' value="js_redirect">JS redirect</option>
<option'; if($g_redirect == 'js_selection'){echo ' selected="selected"';} echo ' value="js_selection">JS selection</option>
<option'; if($g_redirect == 'javascript'){echo ' selected="selected"';} echo ' value="javascript">JavaScript</option>
<option'; if($g_redirect == 'meta_refresh'){echo ' selected="selected"';} echo ' value="meta_refresh">Meta refresh</option>
<option'; if($g_redirect == 'show_out'){echo ' selected="selected"';} echo ' value="show_out">Show out</option>
<option'; if($g_redirect == 'show_page_html'){echo ' selected="selected"';} echo ' value="show_page_html">Show page html</option>
<option'; if($g_redirect == 'show_text'){echo ' selected="selected"';} echo ' value="show_text">Show text</option>
<option'; if($g_redirect == 'stop'){echo ' selected="selected"';} echo ' value="stop">Stop</option>
<option'; if($g_redirect == 'under_construction'){echo ' selected="selected"';} echo ' value="under_construction">Under construction</option>
<option'; if($g_redirect == '403_forbidden'){echo ' selected="selected"';} echo ' value="403_forbidden">403 Forbidden</option>
<option'; if($g_redirect == '404_not_found'){echo ' selected="selected"';} echo ' value="404_not_found">404 Not Found</option>
<option'; if($g_redirect == '500_server_error'){echo ' selected="selected"';} echo ' value="500_server_error">500 Server Error</option>
</select></div>
<div class="param">'.$trans['group']['g6'].'<br><textarea name="g_out" rows="4">'.$g_out.'</textarea><br></div>
<div class="param">'.$trans['group']['g16'].'<br><textarea name="g_curl" rows="3">'.$g_curl.'</textarea><br></div>
<div class="param">'.$trans['group']['g7'].' <select name="g_uniq_method" size = "1">
<option'; if($g_uniq_method == '0'){echo ' selected="selected"';} echo ' value="0">Cookies</option>
<option'; if($g_uniq_method == '1'){echo ' selected="selected"';} echo ' value="1">IP</option>
</select></div>
<div class="param">'.$trans['group']['g8'].' <input style="max-width:30px; width:100%;" name="g_uniq_time" type="text" value="'.$g_uniq_time.'" size="1" maxlength="20"> h.<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($g_firewall == 1){echo ' checked="checked"';} echo ' name="g_firewall"> '.$trans['group']['g14'].' <input style="max-width:30px; width:100%;" name="g_f_queries" type="text" value="'.$g_f_queries.'" size="1" maxlength="20"> '.$trans['group']['g15'].' <input style="max-width:30px; width:100%;" name="g_f_time" type="text" value="'.$g_f_time.'" size="1" maxlength="20"> h.<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($g_save_keys == 1){echo ' checked="checked"';} echo ' name="g_save_keys"> '.$trans['group']['g9'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($g_save_keys_se == 1){echo ' checked="checked"';} echo ' name="g_save_keys_se"> '.$trans['group']['g13'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($g_log == 1){echo ' checked="checked"';} echo ' name="g_log"> '.$trans['group']['g10'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($g_status == 1){echo ' checked="checked"';} echo ' name="g_status"> '.$trans['group']['g11'].'<br></div>
<div class="param">
'.$trans['group']['g12'].'<br>
<textarea name="g_comment" rows="3">'.$g_comment.'</textarea><br>
</div>
<br>
<div class="align_center">
<input class="button" type="submit" name="button" id="button" value="Submit">
</div>
<br>
</form>
';
	}
	if($q == 'keys'){
		if(file_exists($keys_folder.'/'.$g_name.'/'.$k.'.dat') && empty($s_name)){
			$keys_f = file_get_contents($keys_folder.'/'.$g_name.'/'.$k.'.dat');
		}
		else{
			$keys_f = '';
		}
		if(file_exists($keys_folder.'/'.$g_name.'/'.$k.'-se.dat') && empty($s_name)){
			$keys_f_se = "\n***** Search Engines *****\n".file_get_contents($keys_folder.'/'.$g_name.'/'.$k.'-se.dat');
		}
		else{
			$keys_f_se = '';
		}
		$keys = $keys_f.$keys_f_se;
		if(!empty($keys)){
			echo '<a name="keywords"></a>
<br>
<form name="form-2" method="#" action="#">
<div class="align_left">
<textarea id="code" style="max-width:620px; width:100%;" name="keywords">'.$keys_f.$keys_f_se.'</textarea>
</div>
</form>
<script>
    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
      lineNumbers: true,
      extraKeys: {"Ctrl-Space": "autocomplete"},
      mode: {globalVars: true}
    });
</script>
';
		}
	}
	if($q == 'se'){
		echo'
<br>
<center><div id="curve_chart_se" style="max-width:100%; height:'.$chart_weight.'px; border:1px solid #848484;"></div></center>';
	}
}
?>
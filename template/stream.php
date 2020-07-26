<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$lines_ig = count(file('database/ip_grabber.dat'));
$last_update_ig = date ("d-m-Y | H:i:s", filemtime('database/ip_grabber.dat'));
$limit_h = $limit_h / 3600;
if(!empty($s) || $q == 's_create'){
	if($q == 's_create'){$s = '';}
	echo '<br>';
	if(!empty($error)){echo '<div class="align_center red bold">'.$error.'</div>';}
	if(!empty($g_id)){echo '<div class="align_center"><b>'.$trans['group']['g2'].': '.$g_name.'</b>';}
	if(!empty($s) && empty($error) && $q != 's_delete'){
		echo ' <b>| '.$trans['stream']['s1'].': '.$s_name.'</b>';
		$db = new SQLite3($log_folder.'/'.$g_id.'.db');
		$db->busyTimeout(5000);
		$db->exec('PRAGMA journal_mode=WAL;');
		$d = '';
		while($chart_days != -1){
			$table_temp = 'log_'.strtotime(date("d-m-Y", strtotime('-'.$chart_days.' day')));
			if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table_temp';")){
				$d = $db->querySingle("SELECT date FROM $table_temp");
				$d = explode("-", $d);
				if(isset($d[0])){$d = $d[0];}
				else{$d = date("d", strtotime('-'.$chart_days.' day'));}
				$query = "SELECT COUNT(*) FROM $table_temp WHERE nstream = '$s_name' AND bot = '$empty'";
				$ch_visitors = $db->querySingle("$query;");
				$ch_hosts = $db->querySingle("$query AND uniq = 'yes';");
				$ch_wap = $db->querySingle("$query AND operator != '$empty' $sqluniq;");
				if($chart_bots == 1){
					$ch_bots = $db->querySingle("SELECT COUNT(*) FROM $table_temp WHERE nstream = '$s_name' AND bot != '$empty' $sqluniq;");
				}
				else {$ch_bots = 0;}
				if(empty($dg)){
					$dg = '[\''.$d.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
				}
				else{
					$dg = $dg.',[\''.$d.'\','.$ch_visitors.','.$ch_hosts.','.$ch_wap.','.$ch_bots.']';
				}
			}
			else{
				if(empty($dg)){
					$dg = '[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0]';
				}
				else{
					$dg = $dg.',[\''.date("d", strtotime('-'.$chart_days.' day')).'\',0,0,0,0]';
				}
			}
			$chart_days--;
		}
		$db->close();
	}
	echo'
<br><br>
<center><div id="curve_chart" style="max-width:100%; height:'.$chart_weight.'px; border:1px solid #848484;"></div></center>';
	if(get_magic_quotes_gpc() == 1){
		$s_out = stripslashes($s_out);
		$ua_text = stripslashes($ua_text);
		$referer_text = stripslashes($referer_text);
		$key_text = stripslashes($key_text);
	}
	if($q == 's_create'){$s = count($g_data);}
	echo '
</div>
<br>
<ul class="tab-menu clearfix">
<li><a href="#tab-1" class="tab-menu--trigger">Main</a></li>
<li><a href="#tab-2" class="tab-menu--trigger">Devices</a></li>
<li><a href="#tab-3" class="tab-menu--trigger">WAP</a></li>
<li><a href="#tab-4" class="tab-menu--trigger">Geo</a></li>
<li><a href="#tab-5" class="tab-menu--trigger">Filters</a></li>
<li><a href="#tab-6" class="tab-menu--trigger">Bots</a></li>
<li><a href="#tab-7" class="tab-menu--trigger">Remote</a></li>
<li><a href="#tab-8" class="tab-menu--trigger">Limit</a></li>
</ul>
<form name="form-2" method="post" action="'.$admin_page.'?g='.$g_id.'&s='.$s.'">
<div id="jsAccordionToTabs" class="tab-container">
<section id="tab-1" class="tab-container--section"> <a href="#tab-1" class="tab-menu-mobile" data-tab-label="Main"></a>
<div class="tab-container--inner">
<div class="param">'.$trans['stream']['s3'].'<br><input name="s_name" type="text" value="'.$s_name.'" maxlength="100"></div>
<div class="param">'.$trans['stream']['s4'].' <select name="redirect" size = "1">
<option'; if($redirect == 'api'){echo ' selected="selected"';} echo ' value="api">API</option>
<option'; if($redirect == 'curl'){echo ' selected="selected"';} echo ' value="curl">CURL</option>
<option'; if($redirect == 'http_redirect'){echo ' selected="selected"';} echo ' value="http_redirect">HTTP redirect</option>
<option'; if($redirect == 'iframe'){echo ' selected="selected"';} echo ' value="iframe">Iframe</option>
<option'; if($redirect == 'iframe_redirect'){echo ' selected="selected"';} echo ' value="iframe_redirect">Iframe redirect</option>
<option'; if($redirect == 'iframe_selection'){echo ' selected="selected"';} echo ' value="iframe_selection">Iframe selection</option>
<option'; if($redirect == 'js_redirect'){echo ' selected="selected"';} echo ' value="js_redirect">JS redirect</option>
<option'; if($redirect == 'js_selection'){echo ' selected="selected"';} echo ' value="js_selection">JS selection</option>
<option'; if($redirect == 'javascript'){echo ' selected="selected"';} echo ' value="javascript">JavaScript</option>
<option'; if($redirect == 'meta_refresh'){echo ' selected="selected"';} echo ' value="meta_refresh">Meta refresh</option>
<option'; if($redirect == 'show_out'){echo ' selected="selected"';} echo ' value="show_out">Show out</option>
<option'; if($redirect == 'show_page_html'){echo ' selected="selected"';} echo ' value="show_page_html">Show page html</option>
<option'; if($redirect == 'show_text'){echo ' selected="selected"';} echo ' value="show_text">Show text</option>
<option'; if($redirect == 'stop'){echo ' selected="selected"';} echo ' value="stop">Stop</option>
<option'; if($redirect == 'under_construction'){echo ' selected="selected"';} echo ' value="under_construction">Under construction</option>
<option'; if($redirect == '403_forbidden'){echo ' selected="selected"';} echo ' value="403_forbidden">403 Forbidden</option>
<option'; if($redirect == '404_not_found'){echo ' selected="selected"';} echo ' value="404_not_found">404 Not Found</option>
<option'; if($redirect == '500_server_error'){echo ' selected="selected"';} echo ' value="500_server_error">500 Server Error</option>
</select></div>
<div class="param">'.$trans['stream']['s45'].' <select name="distribution_type" size = "1">
<option'; if($distribution_type == 'rotator'){echo ' selected="selected"';} echo ' value="rotator">Rotator</option>
<option'; if($distribution_type == 'evenly'){echo ' selected="selected"';} echo ' value="evenly">Evenly</option>
<option'; if($distribution_type == 'random'){echo ' selected="selected"';} echo ' value="random">Random</option>
</select></div>
<div class="param">'.$trans['stream']['s5'].'<br><textarea name="s_out" rows="4">'.$s_out.'</textarea><br></div>
<div class="param">'.$trans['stream']['s50'].'<br><textarea name="s_curl" rows="3">'.$s_curl.'</textarea><br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($separation == 1){echo ' checked="checked"';} echo ' name="separation"> '.$trans['stream']['s7'].'<br>
<p><input style="max-width:250px; width:100%;" name="separation_file" type="text" value="'.$separation_file.'" maxlength="100"></p></div>
<div class="param">'.$trans['stream']['s42'].' <input style="max-width:25px; width:100%;" name="chance" type="text" value="'.$chance.'" maxlength="100"> %</div>
<div class="param"><input class="checkbox" type="checkbox"'; if($s_status == 1){echo ' checked="checked"';} echo ' name="s_status"> '.$trans['stream']['s39'].'<br></div>
<div class="param">
'.$trans['stream']['s40'].'<br>
<textarea name="comment" rows="3">'.$comment.'</textarea><br>
</div>
</div>
</section>
<section id="tab-2" class="tab-container--section"> <a href="#tab-2" class="tab-menu-mobile" data-tab-label="Devices"></a>
<div class="tab-container--inner cf">
<div class="param">'.$trans['stream']['s9'].' <select name="computer" size = "1">
<option'; if($computer == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($computer == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($computer == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s10'].' <select name="phone" size = "1">
<option'; if($phone == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($phone == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($phone == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s11'].' <select name="tablet" size = "1">
<option'; if($tablet == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($tablet == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($tablet == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
</div>
</section>
<section id="tab-3" class="tab-container--section"> <a href="#tab-3" class="tab-menu-mobile" data-tab-label="WAP"></a>
<div class="tab-container--inner">
<div class="param">'.$trans['stream']['s13'].' <select name="beeline" size = "1">
<option'; if($beeline == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($beeline == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($beeline == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s14'].' <select name="megafon" size = "1">
<option'; if($megafon == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($megafon == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($megafon == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s15'].' <select name="mts" size = "1">
<option'; if($mts == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($mts == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($mts == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s16'].' <select name="tele2" size = "1">
<option'; if($tele2 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($tele2 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($tele2 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s17'].' <select name="azerbaijan" size = "1">
<option'; if($azerbaijan == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($azerbaijan == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($azerbaijan == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s43'].' <select name="belarus" size = "1">
<option'; if($belarus == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($belarus == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($belarus == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s44'].' <select name="kazakhstan" size = "1">
<option'; if($kazakhstan == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($kazakhstan == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($kazakhstan == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s46'].' <select name="ukraine" size = "1">
<option'; if($ukraine == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($ukraine == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($ukraine == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s18'].' <select name="wap-1" size = "1">
<option'; if($wap_1 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($wap_1 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($wap_1 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s22'].' <select name="wap-2" size = "1">
<option'; if($wap_2 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($wap_2 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($wap_2 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s27'].' <select name="wap-3" size = "1">
<option'; if($wap_3 == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($wap_3 == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($wap_3 == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
</div>
</section>
<section id="tab-4" class="tab-container--section"> <a href="#tab-4" class="tab-menu-mobile" data-tab-label="Geo"></a>
<div class="tab-container--inner">
<div class="param">'.$trans['stream']['s19'].'
<select name="country_flag" size = "1">
<option'; if($country_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($country_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($country_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="country" rows="3">'.$country.'</textarea></p></div>
<div class="param">'.$trans['stream']['s20'].'
<select name="city_flag" size = "1">
<option'; if($city_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($city_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($city_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="city" rows="3">'.$city.'</textarea></p></div>
<div class="param">'.$trans['stream']['s48'].'
<select name="region_flag" size = "1">
<option'; if($region_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($region_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($region_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="region" rows="3">'.$region.'</textarea></p></div>
</div>
</section>
<section id="tab-5" class="tab-container--section"> <a href="#tab-5" class="tab-menu-mobile" data-tab-label="Filters"></a>
<div class="tab-container--inner">
<div class="param">'.$trans['stream']['s21'].'
<select name="lang_flag" size = "1">
<option'; if($lang_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($lang_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($lang_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="lang" rows="3">'.$lang.'</textarea></p></div>
<div class="param">'.$trans['stream']['s23'].'
<select name="ua_text_flag" size = "1">
<option'; if($ua_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($ua_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($ua_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="ua_text" rows="3">'.$ua_text.'</textarea></p></div>
<div class="param">'.$trans['stream']['s24'].'
<select name="referer_text_flag" size = "1">
<option'; if($referer_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($referer_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($referer_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="referer_text" rows="3">'.$referer_text.'</textarea></p></div>
<div class="param">'.$trans['stream']['s49'].'
<select name="domain_text_flag" size = "1">
<option'; if($domain_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($domain_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($domain_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="domain_text" rows="3">'.$domain_text.'</textarea></p></div>
<div class="param">'.$trans['stream']['s25'].'
<select name="key_text_flag" size = "1">
<option'; if($key_text_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($key_text_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($key_text_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><textarea name="key_text" rows="3">'.$key_text.'</textarea></p></div>
<div class="param">'.$trans['stream']['s26'].'
<select name="ch_list_ip_flag" size = "1">
<option'; if($ch_list_ip_flag == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($ch_list_ip_flag == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($ch_list_ip_flag == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select><br>
<p><input style="max-width:250px; width:100%;" name="list_ip_file" type="text" value="'.$list_ip_file.'" maxlength="100"></p></div>
<div class="param">'.$trans['stream']['s36'].' <select name="unique_user" size = "1">
<option'; if($unique_user == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($unique_user == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($unique_user == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s37'].' <select name="yabrowser" size = "1">
<option'; if($yabrowser == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($yabrowser == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($yabrowser == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
<div class="param">'.$trans['stream']['s38'].' <select name="referer" size = "1">
<option'; if($referer == '0'){echo ' selected="selected"';} echo ' value="0">'.$trans['option']['o1'].'</option>
<option'; if($referer == '1'){echo ' selected="selected"';} echo ' value="1">'.$trans['option']['o2'].'</option>
<option'; if($referer == '2'){echo ' selected="selected"';} echo ' value="2">'.$trans['option']['o3'].'</option>
</select></div>
</div>
</section>
<section id="tab-6" class="tab-container--section"> <a href="#tab-6" class="tab-menu-mobile" data-tab-label="Bots"></a>
<div class="tab-container--inner">
<div class="param">'.$trans['stream']['s28'].' <select name="bot_redirect" size = "1">
<option'; if($bot_redirect == 'api'){echo ' selected="selected"';} echo ' value="api">API</option>
<option'; if($bot_redirect == 'curl'){echo ' selected="selected"';} echo ' value="curl">CURL</option>
<option'; if($bot_redirect == 'http_redirect'){echo ' selected="selected"';} echo ' value="http_redirect">HTTP redirect</option>
<option'; if($bot_redirect == 'javascript'){echo ' selected="selected"';} echo ' value="javascript">JavaScript</option>
<option'; if($bot_redirect == 'meta_refresh'){echo ' selected="selected"';} echo ' value="meta_refresh">Meta Refresh</option>
<option'; if($bot_redirect == 'show_out'){echo ' selected="selected"';} echo ' value="show_out">Show out</option>
<option'; if($bot_redirect == 'show_page_html'){echo ' selected="selected"';} echo ' value="show_page_html">Show page html</option>
<option'; if($bot_redirect == 'show_text'){echo ' selected="selected"';} echo ' value="show_text">Show text</option>
<option'; if($bot_redirect == 'skip'){echo ' selected="selected"';} echo ' value="skip">Skip</option>
<option'; if($bot_redirect == 'stop'){echo ' selected="selected"';} echo ' value="stop">Stop</option>
<option'; if($bot_redirect == 'under_construction'){echo ' selected="selected"';} echo ' value="under_construction">Under construction</option>
<option'; if($bot_redirect == '403_forbidden'){echo ' selected="selected"';} echo ' value="403_forbidden">403 Forbidden</option>
<option'; if($bot_redirect == '404_not_found'){echo ' selected="selected"';} echo ' value="404_not_found">404 Not Found</option>
<option'; if($bot_redirect == '500_server_error'){echo ' selected="selected"';} echo ' value="500_server_error">500 Server Error</option>
</select><br>
<p>'.$trans['stream']['s29'].'<br><textarea name="out_bot" rows="4">'.$out_bot.'</textarea></p></div>
<div class="param">'.$trans['stream']['s50'].'<br><textarea name="b_curl" rows="3">'.$b_curl.'</textarea><br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_ua == 1){echo ' checked="checked"';} echo ' name="ch_ua"> '.$trans['stream']['s41'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_empty_ua == 1){echo ' checked="checked"';} echo ' name="ch_empty_ua"> '.$trans['stream']['s30'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_ipv6 == 1){echo ' checked="checked"';} echo ' name="ch_ipv6"> '.$trans['stream']['s47'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_domain_name == 1){echo ' checked="checked"';} echo ' name="ch_domain_name"> '.$trans['stream']['s31'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_list_ua == 1){echo ' checked="checked"';} echo ' name="ch_list_ua"> '.$trans['stream']['s32'].'<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($save_ip == 1){echo ' checked="checked"';} echo ' name="save_ip"> '.$trans['stream']['s33'].'<br></div>
<div class="param">'.$trans['stream']['s34'].':</div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_bot_ip_baidu == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_baidu"> Baidu<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_bot_ip_bing == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_bing"> Bing<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_bot_ip_google == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_google"> Google<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_bot_ip_mail == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_mail"> Mail.ru<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_bot_ip_yahoo == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_yahoo"> Yahoo!<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_bot_ip_yandex == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_yandex"> Yandex<br></div>
<div class="param"><input class="checkbox" type="checkbox"'; if($ch_bot_ip_others == 1){echo ' checked="checked"';} echo ' name="ch_bot_ip_others"> Others<br></div>
<div class="param">'.$trans['stream']['s35'].' <select name="ipgrabber" size = "1">
<option'; if($ipgrabber == 'off'){echo ' selected="selected"';} echo ' value="off">Off</option>
<option'; if($ipgrabber == 'online'){echo ' selected="selected"';} echo ' value="online">Online</option>
<option'; if($ipgrabber == 'file'){echo ' selected="selected"';} echo ' value="file">File</option>
</select><br>';
if($ipgrabber == 'file'){
	echo '
Last update: '.$last_update_ig.' | '.$lines_ig.' ip<br>';} echo '
</div>
</div>
</section>
<section id="tab-7" class="tab-container--section"> <a href="#tab-7" class="tab-menu-mobile" data-tab-label="Remote"></a>
<div class="tab-container--inner cf">
<div class="param"><input class="checkbox" type="checkbox"'; if($remote == 1){echo ' checked="checked"';} echo ' name="remote"> '.$trans['stream']['s6'].' <input style="max-width:35px; width:100%;" name="remote_cache" type="text" value="'.$remote_cache.'" maxlength="100"> s.</div>
<div class="param"><textarea name="remote_url" rows="3">'.$remote_url.'</textarea></div>
<div class="param">'.$trans['stream']['s8'].'<br><textarea name="remote_regexp" rows="3">'.$remote_regexp.'</textarea></div>
<div class="param">'.$trans['stream']['s12'].'<br><textarea name="remote_reserved_out" rows="3">'.$remote_reserved_out.'</textarea></div>
</div>
</section>
<section id="tab-8" class="tab-container--section"> <a href="#tab-8" class="tab-menu-mobile" data-tab-label="Remote"></a>
<div class="tab-container--inner cf">
<div class="param"><input class="checkbox" type="checkbox"'; if($limit == 1){echo ' checked="checked"';} echo ' name="limit"> '.$trans['stream']['s51'].' <input style="max-width:50px; width:100%;" name="limit_с" type="text" value="'.$limit_с.'" maxlength="50"> '.$trans['stream']['s52'].'</div>
<div class="param radio"><input class="checkbox" type="radio" name="limit_type" value="1"'; if($limit_type == 1){echo ' checked="checked"';} echo '> '.$trans['stream']['s53'].'</div>
<div class="param radio"><input class="checkbox" type="radio" name="limit_type" value="2"'; if($limit_type == 2){echo ' checked="checked"';} echo '> '.$trans['stream']['s54'].' <input style="max-width:20px; width:100%;" name="limit_h" type="text" value="'.$limit_h.'" maxlength="50"> h.</div>
</div>
</section>
</div>
<br>
<div class="align_center">
<input class="button" type="submit" name="button" id="button" value="Submit">
</div>
<br>
</form>';
}
?>
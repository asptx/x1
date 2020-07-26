<?php
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
$chart_days--;
if(isset($_GET['t'])){$table = $_GET['t'];}
else{$table = 'log_'.strtotime(date('d-m-Y'));}
if(!file_exists($ini_folder)){
	mkdir($ini_folder, 0755);
	file_put_contents($ini_folder.'/'.'.htaccess', "<Files *.ini>\nDeny from all\n</Files>", LOCK_EX);
}
if(!file_exists($log_folder)){
	mkdir($log_folder, 0755);
	file_put_contents($log_folder.'/'.'.htaccess', "<Files *.db>\nDeny from all\n</Files>", LOCK_EX);
}
if(isset($_GET['q'])){$q = $_GET['q'];} else{$q = '';}
if(isset($_GET['g'])){$g_id = $_GET['g'];} else{$g_id = '';}
if(isset($_GET['s'])){$s = $_GET['s'];} else{$s = '';}
if(isset($_GET['n'])){$name = $_GET['n'];} else{$name = '';}
if(isset($_GET['d'])){$d = $_GET['d'];} else{$d = '';}
if($q == "g_delete" && !empty($g_id)){
	if(file_exists($ini_folder.'/'.$g_id.'.ini')){
		unlink($ini_folder.'/'.$g_id.'.ini');
	}
	if(file_exists($log_folder.'/'.$g_id.'.db')){
		unlink($log_folder.'/'.$g_id.'.db');
	}
}
if($q == "g_del_log" && !empty($g_id)){
	if(file_exists($log_folder.'/'.$g_id.'.db')){
		unlink($log_folder.'/'.$g_id.'.db');
	}
}
if($q == "s_del_log" && !empty($g_id) && !empty($s)){
    if(file_exists($log_folder.'/'.$g_id.'.db')){
		$t_del = '';
		$db = new SQLite3($log_folder.'/'.$g_id.'.db');
		$db->querySingle("BEGIN IMMEDIATE;");
		$res = $db->query("SELECT * FROM sqlite_master;");
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$t_tmp = $array['name'];
			$db->query("DELETE FROM $t_tmp WHERE nstream = '$name';");
			if($db->querySingle("SELECT COUNT(*) FROM $t_tmp;") == 0){
				$t_del[] = $t_tmp;
			}
		}
		if(!empty($t_del)){
			foreach($t_del as $t_tmp){
				$db->query("DROP TABLE '$t_tmp';");
			}
		}
		$db->querySingle("COMMIT; VACUUM;");
		$db->close();
	}
}
if($q == 's_delete' && !empty($s)){
	if(file_exists($ini_folder.'/'.$g_id.'.ini')){
		$g_data = unserialize(file_get_contents($ini_folder.'/'.$g_id.'.ini'));
		unset($g_data[$s]);
		$g_data = serialize(array_values($g_data));
		file_put_contents($ini_folder.'/'.$g_id.'.ini', $g_data, LOCK_EX);
	}
    if(file_exists($log_folder.'/'.$g_id.'.db')){
		$db = new SQLite3($log_folder.'/'.$g_id.'.db');
		$db->querySingle("BEGIN IMMEDIATE;");
		$res = $db->query("SELECT * FROM sqlite_master;");
		while($array = $res->fetchArray(SQLITE3_ASSOC)){
			$t_temp = $array['name'];
			$db->query("DELETE FROM $t_temp WHERE nstream = '$name';");
		}
		$db->querySingle("COMMIT; VACUUM;");
		$db->close();
	}
}
if($q == 's_up' || $q == 's_down'){
	$s_data = unserialize(file_get_contents($ini_folder.'/'.$g_id.'.ini'));
	if($q == 's_up' && !empty($s)){
		$sm = $s--;
		$up = $s_data[$s];
		$down = $s_data[$sm];
		$s_data[$sm] = $up;
		$s_data[$s] = $down;
	}
	if($q == 's_down' && !empty($s)){
		$sp = $s++;
		$up = $s_data[$s];
		$down = $s_data[$sp];
		$s_data[$sp] = $up;
		$s_data[$s] = $down;
	}
	$s_data = serialize($s_data);
	file_put_contents($ini_folder.'/'.$g_id.'.ini', $s_data."\n", LOCK_EX);
}
if(!empty($g_id) && file_exists($ini_folder.'/'.$g_id.'.ini')){
	$g_data = unserialize(file_get_contents($ini_folder.'/'.$g_id.'.ini'));
	$g_id = $g_data[0]['g_id'];
	$g_name = $g_data[0]['g_name'];
	$g_redirect = $g_data[0]['g_redirect'];
	$g_out = $g_data[0]['g_out'];
	$g_curl = $g_data[0]['g_curl'];
	$g_status = $g_data[0]['g_status'];
	$g_uniq_method = $g_data[0]['g_uniq_method'];
	$g_uniq_time = $g_data[0]['g_uniq_time'];
	$g_firewall = $g_data[0]['g_firewall'];
	$g_f_queries = $g_data[0]['g_f_queries'];
	$g_f_time = $g_data[0]['g_f_time'];
	$g_save_keys = $g_data[0]['g_save_keys'];
	$g_save_keys_se = $g_data[0]['g_save_keys_se'];
	$g_log = $g_data[0]['g_log'];
	$g_comment = $g_data[0]['g_comment'];
}
else{
	$g_name = '';
	$g_id = '';
	$g_redirect = 'http_redirect';
	$g_out = 'http://site.com';
	$g_curl = '';
	$g_status = 1;
	$g_uniq_method = 0;
	$g_uniq_time = 86400;
	$g_firewall = 0;
	$g_f_queries = 100;
	$g_f_time = 86400;
	$g_save_keys = 1;
	$g_save_keys_se = 0;
	$g_log = 1;
	$g_comment = '';
}
if(!empty($s) && $q != 's_delete' && !empty($g_data[$s]['s_name'])){
	$s_name = $g_data[$s]['s_name'];
	$redirect = $g_data[$s]['redirect'];
	$distribution_type = $g_data[$s]['distribution_type'];
	$s_out = $g_data[$s]['s_out'];
	$remote = $g_data[$s]['remote'];
	$remote_cache = $g_data[$s]['remote_cache'];
	$remote_regexp = $g_data[$s]['remote_regexp'];
	$remote_reserved_out = $g_data[$s]['remote_reserved_out'];
	$remote_url = $g_data[$s]['remote_url'];
	$separation = $g_data[$s]['separation'];
	$separation_file = $g_data[$s]['separation_file'];
	$s_curl = $g_data[$s]['s_curl'];
	$b_curl = $g_data[$s]['b_curl'];
	$computer = $g_data[$s]['computer'];
	$phone = $g_data[$s]['phone'];
	$tablet = $g_data[$s]['tablet'];
	$beeline = $g_data[$s]['beeline'];
	$megafon = $g_data[$s]['megafon'];
	$mts = $g_data[$s]['mts'];
	$tele2 = $g_data[$s]['tele2'];
	$azerbaijan = $g_data[$s]['azerbaijan'];
	$belarus = $g_data[$s]['belarus'];
	$kazakhstan = $g_data[$s]['kazakhstan'];
	$ukraine = $g_data[$s]['ukraine'];
	$wap_1 = $g_data[$s]['wap-1'];
	$wap_2 = $g_data[$s]['wap-2'];
	$wap_3 = $g_data[$s]['wap-3'];
	$country_flag = $g_data[$s]['country_flag'];
	$country = $g_data[$s]['country'];
	$city_flag = $g_data[$s]['city_flag'];
	$city = $g_data[$s]['city'];
	$region_flag = $g_data[$s]['region_flag'];
	$region = $g_data[$s]['region'];
	$lang_flag = $g_data[$s]['lang_flag'];
	$lang = $g_data[$s]['lang'];
	$ua_text_flag = $g_data[$s]['ua_text_flag'];
	$ua_text = $g_data[$s]['ua_text'];
	$referer_text_flag = $g_data[$s]['referer_text_flag'];
	$referer_text = $g_data[$s]['referer_text'];
	$domain_text_flag = $g_data[$s]['domain_text_flag'];
	$domain_text = $g_data[$s]['domain_text'];
	$key_text_flag = $g_data[$s]['key_text_flag'];
	$key_text = $g_data[$s]['key_text'];
	$ch_list_ip_flag = $g_data[$s]['ch_list_ip_flag'];
	$list_ip_file = $g_data[$s]['list_ip_file'];
	$bot_redirect = $g_data[$s]['bot_redirect'];
	$out_bot = $g_data[$s]['out_bot'];
	$ipgrabber = $g_data[$s]['ipgrabber'];
	$ch_ipv6 = $g_data[$s]['ch_ipv6'];
	$ch_bot_ip_baidu = $g_data[$s]['ch_bot_ip_baidu'];
	$ch_bot_ip_bing = $g_data[$s]['ch_bot_ip_bing'];
	$ch_bot_ip_google = $g_data[$s]['ch_bot_ip_google'];
	$ch_bot_ip_mail = $g_data[$s]['ch_bot_ip_mail'];
	$ch_bot_ip_yahoo = $g_data[$s]['ch_bot_ip_yahoo'];
	$ch_bot_ip_yandex = $g_data[$s]['ch_bot_ip_yandex'];
	$ch_bot_ip_others = $g_data[$s]['ch_bot_ip_others'];
	$save_ip = $g_data[$s]['save_ip'];
	$ch_list_ua = $g_data[$s]['ch_list_ua'];
	$ch_ua = $g_data[$s]['ch_ua'];
	$ch_empty_ua = $g_data[$s]['ch_empty_ua'];
	$ch_domain_name = $g_data[$s]['ch_domain_name'];
	$chance = $g_data[$s]['chance'];
	$unique_user = $g_data[$s]['unique_user'];
	$yabrowser = $g_data[$s]['yabrowser'];
	$referer = $g_data[$s]['referer'];
	$s_status = $g_data[$s]['s_status'];
	$comment = $g_data[$s]['comment'];
	$limit = $g_data[$s]['limit'];
	$limit_type = $g_data[$s]['limit_type'];
	$limit_с = $g_data[$s]['limit_с'];
	$limit_h = $g_data[$s]['limit_h'];
}
else{
	$s_name = '';
	$redirect = 'http_redirect';
	$distribution_type = 'rotator';
	$s_out = 'http://site.com';
	$remote = 0;
	$remote_cache = 1800;
	$remote_regexp = '';
	$remote_reserved_out = '';
	$remote_url = '';
	$separation = 0;
	$separation_file = 'separation.dat';
	$s_curl = '';
	$b_curl = '';
	$computer = 2;
	$phone = 2;
	$tablet = 2;
	$beeline = 2;
	$megafon = 2;
	$mts = 2;
	$tele2 = 2;
	$azerbaijan = 2;
	$belarus = 2;
	$kazakhstan = 2;
	$ukraine = 2;
	$wap_1 = 2;
	$wap_2 = 2;
	$wap_3 = 2;
	$country_flag = 2;
	$country = '';
	$city_flag = 2;
	$city = '';
	$region_flag = 2;
	$region = '';
	$lang_flag = 2;
	$lang = '';
	$ua_text_flag = 2;
	$ua_text = '';
	$referer_text_flag = 2;
	$referer_text = '';
	$domain_text_flag = 2;
	$domain_text = '';
	$key_text_flag = 2;
	$key_text = '';
	$ch_list_ip_flag = 2;
	$list_ip_file = 'list_ip.dat';
	$bot_redirect = 'skip';
	$out_bot = '';
	$ipgrabber = 'off';
	$ch_ipv6 = 0;
	$ch_bot_ip_baidu = 0;
	$ch_bot_ip_bing = 0;
	$ch_bot_ip_google = 0;
	$ch_bot_ip_mail = 0;
	$ch_bot_ip_yahoo = 0;
	$ch_bot_ip_yandex = 0;
	$ch_bot_ip_others = 0;
	$save_ip = 0;
	$ch_list_ua = 0;
	$ch_ua = 1;
	$ch_empty_ua = 1;
	$ch_domain_name = 0;
	$chance = 100;
	$unique_user = 2;
	$yabrowser = 2;
	$referer = 2;
	$s_status = 1;
	$comment = '';
	$limit = 0;
	$limit_type = 1;
	$limit_с = 1000;
	$limit_h = 21600;
}
if(isset($_POST['g_id']) AND $_POST['button'] == "Submit"){
	$g_id = trim(htmlentities($_POST['g_id'], ENT_QUOTES, 'UTF-8'));
	$g_name = trim(htmlentities($_POST['g_name'], ENT_QUOTES, 'UTF-8'));
	$g_redirect = $_POST['g_redirect'];
	$g_out = trim(htmlentities($_POST['g_out'], ENT_QUOTES, 'UTF-8'));
	$g_curl = trim(htmlentities($_POST['g_curl'], ENT_QUOTES, 'UTF-8'));
	if(isset($_POST['g_status'])){$g_status = 1;} else{$g_status = 0;}
	$g_uniq_method = $_POST['g_uniq_method'];
	if(is_numeric($_POST['g_uniq_time']) && $_POST['g_uniq_time'] >= 1){$g_uniq_time = trim($_POST['g_uniq_time'])*3600;}
	else{$error = $trans['error']['e1'];}
	if(isset($_POST['g_firewall'])){$g_firewall = 1;} else{$g_firewall = 0;}
	if(is_numeric($_POST['g_f_queries']) && $_POST['g_f_queries'] >= 1){$g_f_queries = trim($_POST['g_f_queries']);}
	else{$error = $trans['error']['e7'];}
	if(is_numeric($_POST['g_f_time']) && $_POST['g_f_time'] >= 1){$g_f_time = trim($_POST['g_f_time'])*3600;}
	else{$error = $trans['error']['e7'];}
	if(isset($_POST['g_save_keys'])){$g_save_keys = 1;} else{$g_save_keys = 0;}
	if(isset($_POST['g_save_keys_se'])){$g_save_keys_se = 1;} else{$g_save_keys_se = 0;}
	if(isset($_POST['g_log'])){$g_log = 1;} else{$g_log = 0;}
	$g_comment = trim(htmlentities($_POST['g_comment'], ENT_QUOTES, 'UTF-8'));
	$files = scandir($ini_folder);
	$x = 0;
	$y = '';
	while($y != 'end'){
		if(!empty($files[$x])){
			if($files[$x] != "." && $files[$x] != ".." && $files[$x] != ".htaccess"){
				$a = unserialize(file_get_contents($ini_folder.'/'.$files[$x]));
				if($g_name == $a[0]['g_name']){
					if($g_id != $a[0]['g_id']){
						$error = $trans['error']['e2'];
						break;
					}
				}
			}
			$x++;
		}
		else{
			$y = 'end';
		}
	}
	if(empty($g_id)){$error = $trans['error']['e3'];}
	if(empty($g_name)){$error = $trans['error']['e4'];}
	if(empty($error)){
 		if(file_exists($ini_folder.'/'.$g_id.'.ini')){
			$g_data = unserialize(file_get_contents($ini_folder.'/'.$g_id.'.ini'));
		}
		$g_data[0]['g_id'] = $g_id;
		$g_data[0]['g_name'] = $g_name;
		$g_data[0]['g_redirect'] = $g_redirect;
		$g_data[0]['g_out'] = $g_out;
		$g_data[0]['g_curl'] = $g_curl;
		$g_data[0]['g_status'] = $g_status;
		$g_data[0]['g_uniq_method'] = $g_uniq_method;
		$g_data[0]['g_uniq_time'] = $g_uniq_time;
		$g_data[0]['g_firewall'] = $g_firewall;
		$g_data[0]['g_f_queries'] = $g_f_queries;
		$g_data[0]['g_f_time'] = $g_f_time;
		$g_data[0]['g_save_keys'] = $g_save_keys;
		$g_data[0]['g_save_keys_se'] = $g_save_keys_se;
		$g_data[0]['g_log'] = $g_log;
		$g_data[0]['g_comment'] = $g_comment;
		$g_data = serialize($g_data);
		file_put_contents($ini_folder.'/'.$g_id.'.ini', $g_data."\n", LOCK_EX);
 	}
}
if(isset($_POST['redirect']) AND $_POST['button'] == "Submit"){
	$s_name = trim(htmlentities($_POST['s_name'], ENT_QUOTES, 'UTF-8'));
	if(empty($s_name)){$error = $trans['error']['e5'];}
	if(!empty($g_data[$s]['s_name']) && $g_data[$s]['s_name'] != $s_name){
		foreach($g_data as $value){
			if(!empty($value['s_name']) && $value['s_name'] == $s_name){
				$error = $trans['error']['e6'];
				break;
			}
		}
	}
	$redirect = $_POST['redirect'];
	$distribution_type = $_POST['distribution_type'];
	$s_out = trim(htmlentities($_POST['s_out'], ENT_QUOTES, 'UTF-8'));
	if(isset($_POST['remote'])){$remote = 1;} else{$remote = 0;}
	$remote_cache = trim(htmlentities($_POST['remote_cache'], ENT_QUOTES, 'UTF-8'));
	$remote_regexp = trim(htmlentities($_POST['remote_regexp'], ENT_QUOTES, 'UTF-8'));
	$remote_reserved_out = trim(htmlentities($_POST['remote_reserved_out'], ENT_QUOTES, 'UTF-8'));
	$remote_url = trim(htmlentities($_POST['remote_url'], ENT_QUOTES, 'UTF-8'));
	if(isset($_POST['separation'])){$separation = 1;} else{$separation = 0;}
	$separation_file = trim(htmlentities($_POST['separation_file'], ENT_QUOTES, 'UTF-8'));
	$s_curl = trim(htmlentities($_POST['s_curl'], ENT_QUOTES, 'UTF-8'));
	$b_curl = trim(htmlentities($_POST['b_curl'], ENT_QUOTES, 'UTF-8'));
	$computer = $_POST['computer'];
	$phone = $_POST['phone'];
	$tablet = $_POST['tablet'];
	$beeline = $_POST['beeline'];
	$megafon = $_POST['megafon'];
	$mts = $_POST['mts'];
	$tele2 = $_POST['tele2'];
	$azerbaijan = $_POST['azerbaijan'];
	$belarus = $_POST['belarus'];
	$kazakhstan = $_POST['kazakhstan'];
	$ukraine = $_POST['ukraine'];
	$wap_1 = $_POST['wap-1'];
	$wap_2 = $_POST['wap-2'];
	$wap_3 = $_POST['wap-3'];
	$country_flag = $_POST['country_flag'];
	$country = trim(htmlentities($_POST['country'], ENT_QUOTES, 'UTF-8'));
	$city_flag = $_POST['city_flag'];
	$city = trim(htmlentities($_POST['city'], ENT_QUOTES, 'UTF-8'));
	$region_flag = $_POST['region_flag'];
	$region = trim(htmlentities($_POST['region'], ENT_QUOTES, 'UTF-8'));
	$lang_flag = $_POST['lang_flag'];
	$lang = trim(htmlentities($_POST['lang'], ENT_QUOTES, 'UTF-8'));
	$ua_text_flag = $_POST['ua_text_flag'];
	$ua_text = trim(htmlentities($_POST['ua_text'], ENT_QUOTES, 'UTF-8'));
	$referer_text_flag = $_POST['referer_text_flag'];
	$referer_text = trim(htmlentities($_POST['referer_text'], ENT_QUOTES, 'UTF-8'));
	$domain_text_flag = $_POST['domain_text_flag'];
	$domain_text = trim(htmlentities($_POST['domain_text'], ENT_QUOTES, 'UTF-8'));
	$key_text_flag = $_POST['key_text_flag'];
	$key_text = trim(htmlentities($_POST['key_text'], ENT_QUOTES, 'UTF-8'));
	$ch_list_ip_flag = $_POST['ch_list_ip_flag'];
	$list_ip_file = trim(htmlentities($_POST['list_ip_file'], ENT_QUOTES, 'UTF-8'));
	$bot_redirect = $_POST['bot_redirect'];
	$out_bot = trim(htmlentities($_POST['out_bot'], ENT_QUOTES, 'UTF-8'));
	$ipgrabber = $_POST['ipgrabber'];
	if(isset($_POST['ch_ipv6'])){$ch_ipv6 = 1;}	else{$ch_ipv6 = 0;}
	if(isset($_POST['ch_bot_ip_baidu'])){$ch_bot_ip_baidu = 1;}	else{$ch_bot_ip_baidu = 0;}
	if(isset($_POST['ch_bot_ip_bing'])){$ch_bot_ip_bing = 1;} else{$ch_bot_ip_bing = 0;}
	if(isset($_POST['ch_bot_ip_google'])){$ch_bot_ip_google = 1;} else{$ch_bot_ip_google = 0;}
	if(isset($_POST['ch_bot_ip_mail'])){$ch_bot_ip_mail = 1;} else{$ch_bot_ip_mail = 0;}
	if(isset($_POST['ch_bot_ip_yahoo'])){$ch_bot_ip_yahoo = 1;}	else{$ch_bot_ip_yahoo = 0;}
	if(isset($_POST['ch_bot_ip_yandex'])){$ch_bot_ip_yandex = 1;} else{$ch_bot_ip_yandex = 0;}
	if(isset($_POST['ch_bot_ip_others'])){$ch_bot_ip_others = 1;} else{$ch_bot_ip_others = 0;}
	if(isset($_POST['save_ip'])){$save_ip = 1;}	else{$save_ip = 0;}
	if(isset($_POST['ch_list_ua'])){$ch_list_ua = 1;} else{$ch_list_ua = 0;}
	if(isset($_POST['ch_ua'])){$ch_ua = 1;}	else{$ch_ua = 0;}
	if(isset($_POST['ch_empty_ua'])){$ch_empty_ua = 1;}	else{$ch_empty_ua = 0;}
	if(isset($_POST['ch_domain_name'])){$ch_domain_name = 1;} else{$ch_domain_name = 0;}
	$chance = trim(htmlentities($_POST['chance'], ENT_QUOTES, 'UTF-8'));
	if($chance > 100 || empty($chance)){$chance = 100;}
	$unique_user = $_POST['unique_user'];
	$yabrowser = $_POST['yabrowser'];
	$referer = $_POST['referer'];
	if(isset($_POST['s_status'])){$s_status = 1;} else{$s_status = 0;}
	$comment = trim(htmlentities($_POST['comment'], ENT_QUOTES, 'UTF-8'));
	if(isset($_POST['limit'])){$limit = 1;} else{$limit = 0;}
	$limit_type = $_POST['limit_type'];
	if(is_numeric($_POST['limit_с']) && $_POST['limit_с'] >= 1){$limit_с = trim($_POST['limit_с']);}
	else{$error = $trans['error']['e7'];}
	if(is_numeric($_POST['limit_h']) && $_POST['limit_h'] >= 1){$limit_h = trim($_POST['limit_h'])*3600;}
	else{$error = $trans['error']['e7'];}
	if(empty($error)){
		if(!empty($g_data[$s]['s_name']) && $g_data[$s]['s_name'] == $s_name){
			$n = $s;
		}
		else{
			$n = count($g_data);
		}
		$g_data[$n] = array(
		's_name'=>$s_name,
		'redirect'=>$redirect,
		'distribution_type'=>$distribution_type,
		's_out'=>$s_out,
		'remote'=>$remote,
		'remote_cache'=>$remote_cache,
		'remote_regexp'=>$remote_regexp,
		'remote_reserved_out'=>$remote_reserved_out,
		'remote_url'=>$remote_url,
		'separation'=>$separation,
		'separation_file'=>$separation_file,
		's_curl'=>$s_curl,
		'b_curl'=>$b_curl,
		'computer'=>$computer,
		'phone'=>$phone,
		'tablet'=>$tablet,
		'beeline'=>$beeline,
		'megafon'=>$megafon,
		'mts'=>$mts,
		'tele2'=>$tele2,
		'azerbaijan'=>$azerbaijan,
		'belarus'=>$belarus,
		'kazakhstan'=>$kazakhstan,
		'ukraine'=>$ukraine,
		'wap-1'=>$wap_1,
		'wap-2'=>$wap_2,
		'wap-3'=>$wap_3,
		'country_flag'=>$country_flag,
		'country'=>$country,
		'city_flag'=>$city_flag,
		'city'=>$city,
		'region_flag'=>$region_flag,
		'region'=>$region,
		'lang_flag'=>$lang_flag,
		'lang'=>$lang,
		'ua_text_flag'=>$ua_text_flag,
		'ua_text'=>$ua_text,
		'referer_text_flag'=>$referer_text_flag,
		'referer_text'=>$referer_text,
		'domain_text_flag'=>$domain_text_flag,
		'domain_text'=>$domain_text,
		'key_text_flag'=>$key_text_flag,
		'key_text'=>$key_text,
		'ch_list_ip_flag'=>$ch_list_ip_flag,
		'list_ip_file'=>$list_ip_file,
		'bot_redirect'=>$bot_redirect,
		'out_bot'=>$out_bot,
		'ipgrabber'=>$ipgrabber,
		'ch_ipv6'=>$ch_ipv6,
		'ch_bot_ip_baidu'=>$ch_bot_ip_baidu,
		'ch_bot_ip_bing'=>$ch_bot_ip_bing,
		'ch_bot_ip_google'=>$ch_bot_ip_google,
		'ch_bot_ip_mail'=>$ch_bot_ip_mail,
		'ch_bot_ip_yahoo'=>$ch_bot_ip_yahoo,
		'ch_bot_ip_yandex'=>$ch_bot_ip_yandex,
		'ch_bot_ip_others'=>$ch_bot_ip_others,
		'save_ip'=>$save_ip,
		'ch_list_ua'=>$ch_list_ua,
		'ch_ua'=>$ch_ua,
		'ch_empty_ua'=>$ch_empty_ua,
		'ch_domain_name'=>$ch_domain_name,
		'chance'=>$chance,
		'unique_user'=>$unique_user,
		'yabrowser'=>$yabrowser,
		'referer'=>$referer,
		's_status'=>$s_status,
		'comment'=>$comment,
		'limit'=>$limit,
		'limit_type'=>$limit_type,
		'limit_с'=>$limit_с,
		'limit_h'=>$limit_h
		);
		$g_data = serialize($g_data);
		file_put_contents($ini_folder.'/'.$g_id.'.ini', $g_data."\n", LOCK_EX);
	}
}
if(file_exists($ini_folder.'/'.$g_id.'.ini')){
	$g_data = unserialize(file_get_contents($ini_folder.'/'.$g_id.'.ini'));
}
if(!empty($g_data[$s]['s_name']) && $g_data[$s]['s_name'] != $s_name){
	$s_name = $g_data[$s]['s_name'];
	$redirect = $g_data[$s]['redirect'];
	$distribution_type = $g_data[$s]['distribution_type'];
	$s_out = $g_data[$s]['s_out'];
	$remote = $g_data[$s]['remote'];
	$remote_cache = $g_data[$s]['remote_cache'];
	$remote_regexp = $g_data[$s]['remote_regexp'];
	$remote_reserved_out = $g_data[$s]['remote_reserved_out'];
	$remote_url = $g_data[$s]['remote_url'];
	$separation = $g_data[$s]['separation'];
	$separation_file = $g_data[$s]['separation_file'];
	$s_curl = $g_data[$s]['s_curl'];
	$b_curl = $g_data[$s]['b_curl'];
	$computer = $g_data[$s]['computer'];
	$phone = $g_data[$s]['phone'];
	$tablet = $g_data[$s]['tablet'];
	$beeline = $g_data[$s]['beeline'];
	$megafon = $g_data[$s]['megafon'];
	$mts = $g_data[$s]['mts'];
	$tele2 = $g_data[$s]['tele2'];
	$azerbaijan = $g_data[$s]['azerbaijan'];
	$belarus = $g_data[$s]['belarus'];
	$kazakhstan = $g_data[$s]['kazakhstan'];
	$ukraine = $g_data[$s]['ukraine'];
	$wap_1 = $g_data[$s]['wap-1'];
	$wap_2 = $g_data[$s]['wap-2'];
	$wap_3 = $g_data[$s]['wap-3'];
	$country_flag = $g_data[$s]['country_flag'];
	$country = $g_data[$s]['country'];
	$city_flag = $g_data[$s]['city_flag'];
	$city = $g_data[$s]['city'];
	$region_flag = $g_data[$s]['region_flag'];
	$region = $g_data[$s]['region'];
	$lang_flag = $g_data[$s]['lang_flag'];
	$lang = $g_data[$s]['lang'];
	$ua_text_flag = $g_data[$s]['ua_text_flag'];
	$ua_text = $g_data[$s]['ua_text'];
	$referer_text_flag = $g_data[$s]['referer_text_flag'];
	$referer_text = $g_data[$s]['referer_text'];
	$domain_text_flag = $g_data[$s]['domain_text_flag'];
	$domain_text = $g_data[$s]['domain_text'];
	$key_text_flag = $g_data[$s]['key_text_flag'];
	$key_text = $g_data[$s]['key_text'];
	$ch_list_ip_flag = $g_data[$s]['ch_list_ip_flag'];
	$list_ip_file = $g_data[$s]['list_ip_file'];
	$bot_redirect = $g_data[$s]['bot_redirect'];
	$out_bot = $g_data[$s]['out_bot'];
	$ipgrabber = $g_data[$s]['ipgrabber'];
	$ch_ipv6 = $g_data[$s]['ch_ipv6'];
	$ch_bot_ip_baidu = $g_data[$s]['ch_bot_ip_baidu'];
	$ch_bot_ip_bing = $g_data[$s]['ch_bot_ip_bing'];
	$ch_bot_ip_google = $g_data[$s]['ch_bot_ip_google'];
	$ch_bot_ip_mail = $g_data[$s]['ch_bot_ip_mail'];
	$ch_bot_ip_yahoo = $g_data[$s]['ch_bot_ip_yahoo'];
	$ch_bot_ip_yandex = $g_data[$s]['ch_bot_ip_yandex'];
	$ch_bot_ip_others = $g_data[$s]['ch_bot_ip_others'];
	$save_ip = $g_data[$s]['save_ip'];
	$ch_list_ua = $g_data[$s]['ch_list_ua'];
	$ch_ua = $g_data[$s]['ch_ua'];
	$ch_empty_ua = $g_data[$s]['ch_empty_ua'];
	$ch_domain_name = $g_data[$s]['ch_domain_name'];
	$chance = $g_data[$s]['chance'];
	$unique_user = $g_data[$s]['unique_user'];
	$yabrowser = $g_data[$s]['yabrowser'];
	$referer = $g_data[$s]['referer'];
	$s_status = $g_data[$s]['s_status'];
	$comment = $g_data[$s]['comment'];
	$limit = $g_data[$s]['limit'];
	$limit_type = $g_data[$s]['limit_type'];
	$limit_с = $g_data[$s]['limit_с'];
	$limit_h = $g_data[$s]['limit_h'];
}
?>
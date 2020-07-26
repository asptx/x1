<?php
define("INDEX", "yes");
require_once 'config.php';
if($disable_tds == 1){exit();}
if($error_log == 1){
	@ini_set('log_errors', 1);
	@ini_set('error_log', 'php_errors.log');
}
if($display_errors == 1){
	@ini_set('display_errors', 1);
	@error_reporting(-1);
}
else{
	@ini_set('display_errors', 0);
	@error_reporting(0);
}
$key = '';
$operator = '';
$s_ch_ua = '';
$s_ch_ipv6 = '';
$s_remote = '';
$s_save_ip = '';
$s_bot_redirect = '';
$s_separation = '';
$s_ch_empty_ua = '';
$postback = '';
$ipgrabber_check_ip = "http://ip.bseolized.com/check_ip/[IP]?token=$ipgrabber_token";
$ipgrabber_get_list = "http://ip.bseolized.com/getlist?token=$ipgrabber_token&format=txt";
$ipgrabber_error = 'Wrong or expired token';
if(!empty($folder)){
	$path = $_SERVER['HTTP_HOST'].'/'.$folder;
}
else{
	$path = $_SERVER['HTTP_HOST'];
}
if(isset($_GET['pb']) && $_GET['pb'] == $postback_key){
	if(isset($_GET['cid']) && stristr($_GET['cid'], $cid_delimiter)){
		$get_cid = $_GET['cid'];
	}
	else{
		err_404();
	}
	$postback = array();
	$x = 0;
	while(!empty($_GET[$x])){
		$postback[] = $_GET[$x];
		$x++;
	}
	$postback = SQLite3::escapeString(serialize($postback));
	$ex_cid = explode($cid_delimiter, $get_cid);
	if(!empty($ex_cid[0]) && !empty($ex_cid[1])){
		$id = $ex_cid[0];
		$cid = $ex_cid[1];
	}
	else{
		err_404();
	}
	if(!file_exists($log_folder.'/'.$id.'.db')){
		err_404();
	}
	$db = new SQLite3($log_folder.'/'.$id.'.db');
	$db->busyTimeout($timeout);
	$db->exec('PRAGMA journal_mode=WAL;');
	$db->querySingle("BEGIN IMMEDIATE;");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type='table';");
	$end = '';
	while($end != 1){
		if($array = $res->fetchArray(SQLITE3_ASSOC)){
			$table = $array['name'];
			$res_test = $db->querySingle("SELECT id FROM $table WHERE cid='$cid';");
			if(!empty($res_test)){
				$db->querySingle("UPDATE $table SET postback='$postback' WHERE cid='$cid';");
				break;
			}
		}
		else{
			$end = 1;
		}
	}
	$db->querySingle("COMMIT;");
	$db->close();
	exit();
}
if(isset($_GET['api'])){
	$api_data = @unserialize(base64_decode($_GET['api']));
	$api_key_host = trim($api_data['api_key_host']);
	$id = trim($api_data['id']);
	$ipuser = trim($api_data['ipuser']);
	$lang = trim($api_data['lang']);
	$referer = trim($api_data['referer']);
	$useragent = trim($api_data['useragent']);
	$domain = trim($api_data['domain']);
	$uniq = trim($api_data['uniq']);
	$se = trim($api_data['se']);
	$key = trim($api_data['key']);
	$counter = $empty;
	if(isset($api_data['par_1'])){$parameter_1 = urldecode(trim($api_data['par_1']));}
	if(isset($api_data['par_2'])){$parameter_2 = urldecode(trim($api_data['par_2']));}
	if(isset($api_data['par_3'])){$parameter_3 = urldecode(trim($api_data['par_3']));}
	if(isset($api_data['par_4'])){$parameter_4 = urldecode(trim($api_data['par_4']));}
	if(isset($api_data['par_5'])){$parameter_5 = urldecode(trim($api_data['par_5']));}
	if($api_key != $api_key_host){
		exit();
	}
}
else{
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']){
		if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")>0){
			$ip = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
			$ipuser = trim($ip[0]);
		}
		elseif(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")===false){
			$ipuser = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
		}
	}
	if(!isset($ipuser)){
		$ipuser = trim($_SERVER['REMOTE_ADDR']);
	}
	if(!filter_var($ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$ipuser = $empty;
	}
	if(!empty($_SERVER['HTTP_USER_AGENT'])){$useragent = $_SERVER['HTTP_USER_AGENT'];} else{$useragent = $empty;}
	if(isset($_SERVER['HTTP_REFERER'])){$referer  = $_SERVER['HTTP_REFERER'];} else{$referer = $empty;}
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}
	else{
		$lang = $empty;
	}
	$se = $empty;
}
require_once 'lib/sypex_geo/SxGeo.php';
$SxGeo = new SxGeo('lib/sypex_geo/SxGeo.dat');
$country = $SxGeo->getCountry($ipuser);
if(empty($country)){$country = $empty;}
else{$country = strtolower($country);}
$SxGeo = new SxGeo('lib/sypex_geo/SxGeoCity.dat');
$geodata = $SxGeo->getCityFull($ipuser);
$city = $geodata["city"]["name_en"];
if(empty($city)){$city = $empty;}
else{$city = strtolower($city);}
$region = $geodata["region"]["iso"];
if(empty($region)){$region = $empty;}
else{$region = strtolower($region);}
require_once 'lib/mobile_detect/Mobile_Detect.php';
$detect = new Mobile_Detect;
$detect->setUserAgent($useragent);
$device = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$wap = file('database/wap.dat');
if(search_in_database($wap, $ipuser)){
	$operator = $label;
}
if(empty($operator)){$operator = $empty;}
if(!isset($_GET['api'])){
	$in_url = $_SERVER['HTTP_HOST'].trim($_SERVER['REQUEST_URI']);
	if(trim(substr($in_url, -1)) == '/'){
		trash($trash);
	}
	if(preg_match("~^$path\/([^/]*)$~", $in_url, $matches)){
		$id = $matches[1];
	}
	if(preg_match("~^$path\/(.+?)\/(.*)$~", $in_url, $matches)){
		$id = $matches[1];
		$key = $matches[2];
	}
	if(preg_match("~^$path\/(.+?)\?.+?=([^&]*)$~", $in_url, $matches)){
		$id = $matches[1];
		$key = $matches[2];
	}
	if(preg_match("~^$path\/(.+?)\?.+?=(.+?)&.+?=(.*)$~", $in_url, $matches)){
		$id = $matches[1];
		$key = $matches[2];
		$parameters = base64_decode($matches[3]);
	}
	if(!empty($parameters)){
		$parameters = unserialize($parameters);
		if(!empty($parameters[0]['referer'])){$referer = $parameters[0]['referer'];}
		if(!empty($parameters[0]['par_1'])){$parameter_1 = urldecode(trim($parameters[0]['par_1']));}
		if(!empty($parameters[0]['par_2'])){$parameter_2 = urldecode(trim($parameters[0]['par_2']));}
		if(!empty($parameters[0]['par_3'])){$parameter_3 = urldecode(trim($parameters[0]['par_3']));}
		if(!empty($parameters[0]['par_4'])){$parameter_4 = urldecode(trim($parameters[0]['par_4']));}
		if(!empty($parameters[0]['par_5'])){$parameter_5 = urldecode(trim($parameters[0]['par_5']));}
	}
	if(preg_match("~^http.*://(.+?)/.*$~", $referer, $matches)){
		$domain = $matches[1];
	}
	else{
		$domain = 'unknown';
	}
}
$key = urldecode($key);
if(substr($key, 0, 1) == '%'){
	$key = urldecode($key);
}
if(utf8_bad_find($key) !== false){
	$key = iconv('windows-1251', 'utf-8', $key);
}
if(file_exists($ini_folder.'/'.$id.'.ini')){
	$data_ini = unserialize(file_get_contents($ini_folder.'/'.$id.'.ini'));
}
else{
	trash($trash);
}
if(!empty($ipgrabber_token) && $ipgrabber_update != 0){
	$st_now = strtotime("now");
	$st = strtotime("- $ipgrabber_update minutes");
	if(!file_exists('temp')){
		mkdir('temp', 0755);
	}
	if(file_exists('temp/ip_grabber')){
		$dat = file('temp/ip_grabber');
		if($dat[0] < $st){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ipgrabber_get_list);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$res = curl_exec($ch);
			if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 && $res != $ipgrabber_error){
				file_put_contents('database/ip_grabber.dat', $res, LOCK_EX);
				file_put_contents('temp/ip_grabber', $st_now, LOCK_EX);
			}
			curl_close($ch);
		}
	}
	else{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ipgrabber_get_list);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$res = curl_exec($ch);
		if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 && $res != $ipgrabber_error){
			file_put_contents('database/ip_grabber.dat', $res, LOCK_EX);
			file_put_contents('temp/ip_grabber', $st_now, LOCK_EX);
		}
		curl_close($ch);
	}
}
$g_name = $data_ini[0]['g_name'];
$g_redirect = $data_ini[0]['g_redirect'];
$g_out = $data_ini[0]['g_out'];
$g_status = $data_ini[0]['g_status'];
$g_uniq_method = $data_ini[0]['g_uniq_method'];
$g_uniq_time = $data_ini[0]['g_uniq_time'];
$g_firewall = $data_ini[0]['g_firewall'];
$g_f_queries = $data_ini[0]['g_f_queries'];
$g_f_time = $data_ini[0]['g_f_time'];
$g_save_keys = $data_ini[0]['g_save_keys'];
$g_save_keys_se = $data_ini[0]['g_save_keys_se'];
$g_log = $data_ini[0]['g_log'];
$g_curl = $data_ini[0]['g_curl'];
if($g_status == 0){
	if($g_redirect == 'api' || $g_redirect == 'iframe' || $g_redirect == 'iframe_redirect' || $g_redirect == 'js_selection' || $g_redirect == 'javascript' || $g_redirect == 'stop'){
		exit();
	}
	else{
		trash($trash);
	}
}
if(!isset($_GET['api'])){
	if(!isset($_COOKIE[$n_cookies.'_'.$id])){
		SetCookie($n_cookies.'_'.$id, 0, time() + $g_uniq_time, '/');
		$c_counter = 0;
		if($g_uniq_method == 0 || $g_log == 0){
			$uniq = 'yes';
		}
	}
	else{
		$c_counter = $_COOKIE[$n_cookies.'_'.$id] + 1;
		SetCookie($n_cookies.'_'.$id, $c_counter, time() + $g_uniq_time, '/');
		$uniq = 'no';
	}
}
if($g_log == 1){
	$db = new SQLite3($log_folder.'/'.$id.'.db');
	$db->busyTimeout($timeout);
	$db->exec('PRAGMA journal_mode=WAL;');
	$db->querySingle("BEGIN IMMEDIATE;");
}
if($g_log == 1 && $g_uniq_method == 1){
	$uniq = 'no';
	$y = strtotime("- $g_uniq_time seconds");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type='table' ORDER BY name DESC;");
	while($uniq != 'yes'){
		if($array = $res->fetchArray(SQLITE3_ASSOC)){
			$table = $array['name'];
			$count = $db->querySingle("SELECT COUNT(id) FROM $table;");
			$z = $db->querySingle("SELECT strtotime FROM $table WHERE id = '$count';");
			if($z < $y){$uniq = 'yes'; break;}
			if($db->querySingle("SELECT strtotime FROM $table WHERE uniq = 'yes' AND ipuser = '$ipuser' AND strtotime > $y;")){
				break;
			}
		}
		else{
			$uniq = 'yes'; break;
		}
	}
}
$bot = $empty;
if($g_log == 1 && $g_firewall == 1){
	$c = 0;
	$y = strtotime("- $g_f_time seconds");
	$res = $db->query("SELECT * FROM sqlite_master WHERE type='table' ORDER BY name DESC;");
	while($bot == $empty){
		if($array = $res->fetchArray(SQLITE3_ASSOC)){
			$table = $array['name'];
			$count = $db->querySingle("SELECT COUNT(*) FROM $table;");
			$z = $db->querySingle("SELECT strtotime FROM $table WHERE id = '$count';");
			if($z > $y){
				$c = $db->querySingle("SELECT COUNT(*) FROM $table WHERE ipuser = '$ipuser' AND strtotime > $y;");
			}
			if($c >= $g_f_queries){
				$bot = 'blocked';
				break;
			}
		}
		else{
			break;
		}
	}
}
$z = 0;
$x = 1;
$y = '';
while($y != 'end'){
	if(!empty($data_ini[$x])){
		$z = 1;
		if($data_ini[$x]['s_status'] != 1){
			$z = 0;
		}
		if($z != 0){
			$stream = $data_ini[$x]['s_name'];
			if($g_log == 1 && $data_ini[$x]['limit'] == 1){
				if($data_ini[$x]['limit_type'] == 1){
					$table = 'log_'.strtotime(date('d-m-Y'));
					if($db->querySingle("SELECT * FROM sqlite_master WHERE type = 'table' AND name = '$table';")){
						$c = $db->querySingle("SELECT COUNT(*) FROM $table WHERE nstream = '$stream';");
						if($c >= $data_ini[$x]['limit_с']){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['limit_type'] == 2){
					$c = 0;
					$limit_h = $data_ini[$x]['limit_h'];
					$y = strtotime("- $limit_h seconds");
					$res = $db->query("SELECT * FROM sqlite_master WHERE type='table' ORDER BY name DESC;");
					while($z != 0){
						if($array = $res->fetchArray(SQLITE3_ASSOC)){
							$table = $array['name'];
							$count = $db->querySingle("SELECT COUNT(*) FROM $table;");
							$r = $db->querySingle("SELECT strtotime FROM $table WHERE id = '$count';");
							if($r > $y){
								$c = $db->querySingle("SELECT COUNT(*) FROM $table WHERE strtotime > $y AND nstream = '$stream';");
							}
							if($c >= $data_ini[$x]['limit_с']){
								$z = 0;
								break;
							}
						}
						else{
							break;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['lang_flag'] != 2 && !empty($data_ini[$x]['lang'])){
				if($data_ini[$x]['lang_flag'] == 0){
					if(stristr($data_ini[$x]['lang'], $lang)){
						$z = 0;
					}
				}
				if($data_ini[$x]['lang_flag'] == 1){
					if(!stristr($data_ini[$x]['lang'], $lang)){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['country_flag'] != 2 && !empty($data_ini[$x]['country'])){
				if($data_ini[$x]['country_flag'] == 0){
					if(stristr($data_ini[$x]['country'], $country)){
						$z = 0;
					}
				}
				if($data_ini[$x]['country_flag'] == 1){
					if(!stristr($data_ini[$x]['country'], $country)){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['city_flag'] != 2 && !empty($data_ini[$x]['city'])){
				$c = 0;
				$city_x = explode(',', $data_ini[$x]['city']);
				if($data_ini[$x]['city_flag'] == 0){
					while(!empty($city_x[$c])){
						if(strcasecmp(trim($city_x[$c]), $city) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c == 'end'){
						$z = 0;
					}
				}
				if($data_ini[$x]['city_flag'] == 1){
					while(!empty($city_x[$c])){
						if(strcasecmp(trim($city_x[$c]), $city) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c != 'end'){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['region_flag'] != 2 && !empty($data_ini[$x]['region'])){
				$c = 0;
				$region_x = explode(',', $data_ini[$x]['region']);
				if($data_ini[$x]['region_flag'] == 0){
					while(!empty($region_x[$c])){
						if(strcasecmp(trim($region_x[$c]), $region) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c == 'end'){
						$z = 0;
					}
				}
				if($data_ini[$x]['region_flag'] == 1){
					while(!empty($region_x[$c])){
						if(strcasecmp(trim($region_x[$c]), $region) == 0){
							$c = 'end';
							break;
						}
						$c++;
					}
					if($c != 'end'){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['ua_text_flag'] != 2 && !empty($data_ini[$x]['ua_text'])){
				if(get_magic_quotes_gpc() == 1){
					$ua_text = stripslashes($data_ini[$x]['ua_text']);
				}
				else{
					$ua_text = $data_ini[$x]['ua_text'];
				}
				$c = 0;
				$ua_text_x = explode(',', $ua_text);
				if($data_ini[$x]['ua_text_flag'] == 0){
					if(substr($ua_text, 0, 1) == '/'){
						if(preg_match($ua_text, $useragent, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($ua_text_x[$c])){
							if(stristr($useragent, $ua_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['ua_text_flag'] == 1){
					if(substr($ua_text, 0, 1) == '/'){
						if(!preg_match($ua_text, $useragent, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($ua_text_x[$c])){
							if(stristr($useragent, $ua_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['referer_text_flag'] != 2 && !empty($data_ini[$x]['referer_text'])){
				if(get_magic_quotes_gpc() == 1){
					$referer_text = stripslashes($data_ini[$x]['referer_text']);
				}
				else{
					$referer_text = $data_ini[$x]['referer_text'];
				}
				$c = 0;
				$referer_text_x = explode(',', $referer_text);
				if($data_ini[$x]['referer_text_flag'] == 0){
					if(substr($referer_text, 0, 1) == '/'){
						if(preg_match($referer_text, $referer, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($referer_text_x[$c])){
							if(stristr($referer, $referer_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['referer_text_flag'] == 1){
					if(substr($referer_text, 0, 1) == '/'){
						if(!preg_match($referer_text, $referer, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($referer_text_x[$c])){
							if(stristr($referer, $referer_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['domain_text_flag'] != 2 && !empty($data_ini[$x]['domain_text'])){
				if(get_magic_quotes_gpc() == 1){
					$domain_text = stripslashes($data_ini[$x]['domain_text']);
				}
				else{
					$domain_text = $data_ini[$x]['domain_text'];
				}
				$c = 0;
				$domain_text_x = explode(',', $domain_text);
				if($data_ini[$x]['domain_text_flag'] == 0){
					if(substr($domain_text, 0, 1) == '/'){
						if(preg_match($domain_text, $domain, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($domain_text_x[$c])){
							if(strcasecmp(trim($domain_text_x[$c]), $domain) == 0){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['domain_text_flag'] == 1){
					if(substr($domain_text, 0, 1) == '/'){
						if(!preg_match($domain_text, $domain, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($domain_text_x[$c])){
							if(strcasecmp(trim($domain_text_x[$c]), $domain) == 0){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['key_text_flag'] != 2 && !empty($data_ini[$x]['key_text'])){
				if(get_magic_quotes_gpc() == 1){
					$key_text = stripslashes($data_ini[$x]['key_text']);
				}
				else{
					$key_text = $data_ini[$x]['key_text'];
				}
				upper_replace();
				$c = 0;
				$key_text_x = explode(',', $key_text);
				if($data_ini[$x]['key_text_flag'] == 0){
					if(substr($key_text, 0, 1) == '/'){
						if(preg_match($key_text, $key, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($key_text_x[$c])){
							if(stristr($key, $key_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c == 'end'){
							$z = 0;
						}
					}
				}
				if($data_ini[$x]['key_text_flag'] == 1){
					if(substr($key_text, 0, 1) == '/'){
						if(!preg_match($key_text, $key, $matches)){
							$z = 0;
						}
					}
					else{
						while(!empty($key_text_x[$c])){
							if(stristr($key, $key_text_x[$c])){
								$c = 'end';
								break;
							}
							$c++;
						}
						if($c != 'end'){
							$z = 0;
						}
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['computer'] != 2){
				if($data_ini[$x]['computer'] == 0 && $device == 'computer'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['phone'] != 2){
				if($data_ini[$x]['phone'] == 0 && $device == 'phone'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['tablet'] != 2){
				if($data_ini[$x]['tablet'] == 0 && $device == 'tablet'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['beeline'] != 2){
				if($data_ini[$x]['beeline'] == 0 && $operator == 'beeline'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['megafon'] != 2){
				if($data_ini[$x]['megafon'] == 0 && $operator == 'megafon'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['mts'] != 2){
				if($data_ini[$x]['mts'] == 0 && $operator == 'mts'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['tele2'] != 2){
				if($data_ini[$x]['tele2'] == 0 && $operator == 'tele2'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['azerbaijan'] != 2){
				if($data_ini[$x]['azerbaijan'] == 0 && $operator == 'azerbaijan'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['belarus'] != 2){
				if($data_ini[$x]['belarus'] == 0 && $operator == 'belarus'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['kazakhstan'] != 2){
				if($data_ini[$x]['kazakhstan'] == 0 && $operator == 'kazakhstan'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['ukraine'] != 2){
				if($data_ini[$x]['ukraine'] == 0 && $operator == 'ukraine'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['wap-1'] != 2){
				if($data_ini[$x]['wap-1'] == 0 && $operator == 'wap-1'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['wap-2'] != 2){
				if($data_ini[$x]['wap-2'] == 0 && $operator == 'wap-2'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['wap-3'] != 2){
				if($data_ini[$x]['wap-3'] == 0 && $operator == 'wap-3'){
					$z = 0;
				}
			}
		}
		if($data_ini[$x]['beeline'] == 1 || $data_ini[$x]['megafon'] == 1 || $data_ini[$x]['mts'] == 1 || $data_ini[$x]['tele2'] == 1 || $data_ini[$x]['azerbaijan'] == 1 || $data_ini[$x]['belarus'] == 1 || $data_ini[$x]['kazakhstan'] == 1 || $data_ini[$x]['ukraine'] == 1 || $data_ini[$x]['wap-1'] == 1 || $data_ini[$x]['wap-2'] == 1 || $data_ini[$x]['wap-3'] == 1){
			if($operator == $empty){
				$z = 0;
			}
		}
		if($z != 0){
			if($data_ini[$x]['unique_user'] == 0){
				if($uniq != 'yes'){
					$z = 0;
				}
			}
			if($data_ini[$x]['unique_user'] == 1){
				if($uniq == 'yes'){
					$z = 0;
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['yabrowser'] != 2){
				if($data_ini[$x]['yabrowser'] == 0){
					if(stristr($useragent, 'yabrowser')){
						$z = 0;
					}
				}
				if($data_ini[$x]['yabrowser'] == 1){
					if(!stristr($useragent, 'yabrowser')){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['referer'] != 2){
				if($data_ini[$x]['referer'] == 0){
					if($referer == $empty){
						$z = 0;
					}
				}
				if($data_ini[$x]['referer'] == 1){
					if($referer != $empty){
						$z = 0;
					}
				}
			}
		}
		if($z != 0){
			if($data_ini[$x]['ch_list_ip_flag'] != 2){
				if(!empty($data_ini[$x]['list_ip_file'])){
					$list_ip = file('database/'.$data_ini[$x]['list_ip_file']);
					if($data_ini[$x]['ch_list_ip_flag'] == 0){
						if(search_in_database($list_ip, $ipuser)){
							$z = 0;
						}
					}
					if($data_ini[$x]['ch_list_ip_flag'] == 1){
						if(!search_in_database($list_ip, $ipuser)){
							$z = 0;
						}
					}
				}
			}
		}
		if($z == 1){
			$s_name = $data_ini[$x]['s_name'];
			$s_redirect = $data_ini[$x]['redirect'];
			$s_distribution_type = $data_ini[$x]['distribution_type'];
			$s_out = $data_ini[$x]['s_out'];
			$s_remote = $data_ini[$x]['remote'];
			$s_remote_cache = $data_ini[$x]['remote_cache'];
			$s_remote_regexp = $data_ini[$x]['remote_regexp'];
			$s_remote_reserved_out = $data_ini[$x]['remote_reserved_out'];
			$s_remote_url = $data_ini[$x]['remote_url'];
			$s_separation = $data_ini[$x]['separation'];
			$s_separation_file = $data_ini[$x]['separation_file'];
			$s_curl = $data_ini[$x]['s_curl'];
			$s_bot_curl = $data_ini[$x]['b_curl'];
			$s_bot_redirect = $data_ini[$x]['bot_redirect'];
			$s_out_bot = $data_ini[$x]['out_bot'];
			$s_ipgrabber = $data_ini[$x]['ipgrabber'];
			$s_ch_ipv6 = $data_ini[$x]['ch_ipv6'];
			$s_ch_bot_ip_baidu = $data_ini[$x]['ch_bot_ip_baidu'];
			$s_ch_bot_ip_bing = $data_ini[$x]['ch_bot_ip_bing'];
			$s_ch_bot_ip_google = $data_ini[$x]['ch_bot_ip_google'];
			$s_ch_bot_ip_mail = $data_ini[$x]['ch_bot_ip_mail'];
			$s_ch_bot_ip_yahoo = $data_ini[$x]['ch_bot_ip_yahoo'];
			$s_ch_bot_ip_yandex = $data_ini[$x]['ch_bot_ip_yandex'];
			$s_ch_bot_ip_others = $data_ini[$x]['ch_bot_ip_others'];
			$s_save_ip = $data_ini[$x]['save_ip'];
			$s_ch_list_ua = $data_ini[$x]['ch_list_ua'];
			$s_ch_ua = $data_ini[$x]['ch_ua'];
			$s_ch_empty_ua = $data_ini[$x]['ch_empty_ua'];
			$s_ch_domain_name = $data_ini[$x]['ch_domain_name'];
			$s_chance = $data_ini[$x]['chance'];
			break;
		}
		$x++;
	}
	else{
		$y = 'end';
	}
}
if($bot == $empty && $s_ch_ipv6 == 1 && filter_var($ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
	$bot = 'ipv6';
}
if($bot == $empty && $s_ch_ua == 1 || empty($s_name)){
	if(stristr($useragent, 'baidu')){$bot = 'baidu';}
	if(stristr($useragent, 'bing') || stristr($useragent, 'msnbot')){$bot = 'bing';}
	if(stristr($useragent, 'google')){$bot = 'google';}
	if(stristr($useragent, 'mail.ru')){$bot = 'mail';}
	if(stristr($useragent, 'yahoo')){$bot = 'yahoo';}
	if(stristr($useragent, 'yandex')){$bot = 'yandex';}
	if($bot == $empty){
		$signature_bots = file('database/signature_bots.dat');
		for($i=0;$i<count($signature_bots);$i++){
			$signature_bots_x = trim($signature_bots[$i]);
			if(stristr($useragent, $signature_bots_x)){
				$bot = 'signature';
				break;
			}
		}
	}
}
if($bot == $empty && !empty($s_name) &&  $s_ch_empty_ua == 1){
	if($useragent == $empty || $useragent == ' '){$bot = 'ua_empty';}
}
if($bot == $empty){
	if(!empty($s_name) && $s_ch_domain_name == 1 && $ipuser != $empty){
		$ip_domain = gethostbyaddr($ipuser);
		if(stristr($ip_domain, 'baidu')){$bot = 'baidu';}
		if(stristr($ip_domain, 'bing') || stristr($ip_domain, 'msnbot')){$bot = 'bing';}
		if(stristr($ip_domain, 'google')){$bot = 'google';}
		if(stristr($ip_domain, 'mail.ru')){$bot = 'mail';}
		if(stristr($ip_domain, 'yahoo')){$bot = 'yahoo';}
		if(stristr($ip_domain, 'yandex')){$bot = 'yandex';}
	}
}
if($bot == $empty){
	if(!empty($s_name) && $s_ch_list_ua == 1){
		$bots_ua = file('database/ua_blacklist.dat');
		for($i=0;$i<count($bots_ua);$i++){
			$bots_ua[$i] = trim($bots_ua[$i]);
			if($useragent == $bots_ua[$i]){
				$bot = 'ua_bl';
			}
		}
	}
}
if(($bot != $empty && $s_save_ip == 1) && ($bot == 'baidu' || $bot == 'bing' || $bot == 'google' || $bot == 'mail' || $bot == 'yahoo' || $bot == 'yandex')){
	$bots_ip_file = file('database/ip_'.$bot.'.dat');
	if(!search_in_database($bots_ip_file, $ipuser)){
		file_put_contents('database/ip_'.$bot.'.dat', $ipuser."\n", FILE_APPEND | LOCK_EX);
	}
}
if(!empty($s_name)){
	if($s_ch_bot_ip_baidu == 1 && $bot == $empty){
		$bots_ip = file('database/ip_baidu.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'baidu';
		}
	}
	if($s_ch_bot_ip_bing == 1 && $bot == $empty){
		$bots_ip = file('database/ip_bing.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'bing';
		}
	}
	if($s_ch_bot_ip_google == 1 && $bot == $empty){
		$bots_ip = file('database/ip_google.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'google';
		}
	}
	if($s_ch_bot_ip_mail == 1 && $bot == $empty){
		$bots_ip = file('database/ip_mail.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'mail';
		}
	}
	if($s_ch_bot_ip_yahoo == 1 && $bot == $empty){
		$bots_ip = file('database/ip_yahoo.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'yahoo';
		}
	}
	if($s_ch_bot_ip_yandex == 1 && $bot == $empty){
		$bots_ip = file('database/ip_yandex.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'yandex';
		}
	}
	if($s_ch_bot_ip_others == 1 && $bot == $empty){
		$bots_ip = file('database/ip_others.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'others';
		}
	}
	if($bot == $empty && $s_ipgrabber == 'online' && !empty($ipgrabber_token)){
		$ipgrabber_check_ip = str_ireplace('[IP]', $ipuser, $ipgrabber_check_ip);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ipgrabber_check_ip);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$res = curl_exec($ch);
		if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 && $res != $ipgrabber_error){
			$res = json_decode($res);
			$res = $res->{'is_spider'};
			if($res == 1){
				$bot = 'ip_grabber';
			}
		}
		curl_close($ch);
	}
	if($bot == $empty && $s_ipgrabber == 'file'){
		$bots_ip = file('database/ip_grabber.dat');
		if(search_in_database($bots_ip, $ipuser)){
			$bot = 'ip_grabber';
		}
	}
}
if($bot != $empty){
	if($s_bot_redirect == 'api'){
		$out = $s_out_bot;
		$redirect = 'api';
		out();
	}
	if($s_bot_redirect == 'curl'){
		$out = $s_out_bot;
		$redirect = 'curl';
		$curl = $s_bot_curl;
		out();
	}
	if($s_bot_redirect == 'http_redirect'){
		$out = $s_out_bot;
		$redirect = 'http_redirect';
		out();
	}
	if($s_bot_redirect == 'javascript'){
		$out = $s_out_bot;
		$redirect = 'javascript';
		out();
	}
	if($s_bot_redirect == 'meta_refresh'){
		$out = $s_out_bot;
		$redirect = 'meta_refresh';
		out();
	}
	if($s_bot_redirect == 'show_out'){
		$out = $s_out_bot;
		$redirect = 'show_out';
		out();
	}
	if($s_bot_redirect == 'show_page_html'){
		$out = $s_out_bot;
		$redirect = 'show_page_html';
		out();
	}
	if($s_bot_redirect == 'show_text'){
		$out = $s_out_bot;
		$redirect = 'show_text';
		out();
	}
	if($s_bot_redirect == 'stop'){
		$redirect = 'stop';
		unset($out);
		out();
	}
	if($s_bot_redirect == 'under_construction'){
		$redirect = 'under_construction';
		unset($out);
		out();
	}
	if($s_bot_redirect == '403_forbidden'){
		$redirect = '403_forbidden';
		unset($out);
		out();
	}
	if($s_bot_redirect == '404_not_found'){
		$redirect = '404_not_found';
		unset($out);
		out();
	}
	if($s_bot_redirect == '500_server_error'){
		$redirect = '500_server_error';
		unset($out);
		out();
	}
}
if($s_separation == 1 && !empty($key) && !empty($s_separation_file)){
	if($bot == $empty || $s_bot_redirect == 'skip'){
		s_separation();
	}
}
if($s_remote == 1 && !empty($s_out) && stristr($s_out, '[REMOTE]')){
	$s_remote_url = trim(html_entity_decode($s_remote_url, ENT_QUOTES, 'UTF-8'));
	if(get_magic_quotes_gpc() == 1){$s_remote_url = stripslashes($s_remote_url);}
	if($s_remote_cache != 0){
		$st_now = strtotime("now");
		$st = strtotime("- $s_remote_cache seconds");
		if(!file_exists('temp')){
			mkdir('temp', 0755);
		}
		if(!file_exists('temp/'.$g_name.'_'.$s_name)){
			remote_pars();
		}
		else{
			$file = file('temp/'.$g_name.'_'.$s_name);
			$dat = explode(';', $file[0]);
			if($dat[0] > $st){
				$s_out = str_ireplace('[REMOTE]', $dat[1], $s_out);
			}
			else{
				remote_pars();
			}
		}
	}
	else{
		if(stristr($s_remote_url, '[IP]')){
			$s_remote_url = str_ireplace('[IP]', $ipuser, $s_remote_url);
		}
		if(stristr($s_remote_url, '[COUNTRY]')){
			$s_remote_url = str_ireplace('[COUNTRY]', $country, $s_remote_url);
		}
		if(stristr($s_remote_url, '[CITY]')){
			$s_remote_url = str_ireplace('[CITY]', $city, $s_remote_url);
		}
		if(stristr($s_remote_url, '[LANG]')){
			$s_remote_url = str_ireplace('[LANG]', $lang, $s_remote_url);
		}
		if(stristr($s_remote_url, '[KEY]')){
			$s_remote_url = str_ireplace('[KEY]', $key, $s_remote_url);
		}
		remote_pars();
	}
}
if(!empty($s_out) && stristr($s_out, '|||')){
	$out_ex = explode("|||", $s_out);
	if($s_distribution_type == 'rotator' || $g_log == 0){
		if(!isset($_GET['api'])){
			if(isset($out_ex[$c_counter])){
				$out = trim($out_ex[$c_counter]);
				$counter = $c_counter;
			}
			else{
				$out = trim($out_ex[0]);
				$counter = 0;
				SetCookie($n_cookies.'_'.$id, 0, time() + $g_uniq_time, '/');
			}
		}
		else{
			$out = $s_out;
		}
	}
	if($g_log == 1 && $s_distribution_type == 'evenly'){
		$table = 'log_'.strtotime(date('d-m-Y'));
		if($db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table';")){
			$query = $db->query("SELECT * FROM $table WHERE nstream = '$s_name' ORDER BY id DESC LIMIT 1;");
			$row = $query->fetchArray();
			$c = $row['counter'];
			if(is_numeric($c)){
				$c++;
			}
			else{
				$c = 0;
			}
			$counter = $c;
			if(empty($out_ex[$c])){
				$out = trim($out_ex[0]);
				$counter = 0;
			}
			else{
				$out = trim($out_ex[$c]);
			}
		}
		else{
			$out = trim($out_ex[0]);
			$counter = 0;
		}
	}
	if($s_distribution_type == 'random'){
		$c = 0;
		while(!empty($out_ex[$c])){
			$c++;
		}
		$c--;
		$rand = mt_rand(0, $c);
		$counter = $rand;
		$out = trim($out_ex[$rand]);
	}
}
else{
	if(!empty($s_out)){
		$out = $s_out;
	}
}
if(empty($s_redirect)){
	$redirect = $g_redirect;
	$out = $g_out;
	$curl = $g_curl;
}
else{
	$redirect = $s_redirect;
	$curl = $s_curl;
}
if($redirect == 'stop'){
	unset($out);
}
out();
exit();
function out(){
	global $redirect, $out, $key, $lang, $country, $city, $region, $device, $operator, $bot, $uniq, $path, $s_chance, $g_save_keys_se, $parameter_1, $parameter_2, $parameter_3, $parameter_4, $parameter_5, $key_se, $ipuser, $api_key, $api_key_host, $debug, $domain, $useragent, $curl, $curl_ua, $id, $cid, $cid_delimiter;
	$out = trim(html_entity_decode($out, ENT_QUOTES, 'UTF-8'));
	if(get_magic_quotes_gpc() == 1){
		$out = stripslashes($out);
	}
	if(stristr($out, '[PATH]')){
		$out = str_ireplace('[PATH]', $path, $out);
	}
	if($redirect == 'javascript' || $redirect == 'js_selection'){
		if(empty($s_chance)){$s_chance = 100;}
		$rand = mt_rand(1, 100);
		if($s_chance < $rand){
			$redirect = 'stop';
			$out = 'chance';
		}
	}
	if(stristr($out, '[KEY]')){
		logs();
		save_keys();
		$key = urlencode($key);
		$out = str_ireplace('[KEY]', $key, $out);
	}
	else{
		logs();
		save_keys();
	}
	if($g_save_keys_se == 1){
		keys_se();
		save_keys_se();
	}
	if(stristr($out, '[PAR-1]')){
		$out = str_ireplace('[PAR-1]', $parameter_1, $out);
	}
	if(stristr($out, '[PAR-2]')){
		$out = str_ireplace('[PAR-2]', $parameter_2, $out);
	}
	if(stristr($out, '[PAR-3]')){
		$out = str_ireplace('[PAR-3]', $parameter_3, $out);
	}
	if(stristr($out, '[PAR-4]')){
		$out = str_ireplace('[PAR-4]', $parameter_4, $out);
	}
	if(stristr($out, '[PAR-5]')){
		$out = str_ireplace('[PAR-5]', $parameter_5, $out);
	}
	if(stristr($out, '[IP]')){
		$out = str_ireplace('[IP]', $ipuser, $out);
	}
	if(stristr($out, '[COUNTRY]')){
		$out = str_ireplace('[COUNTRY]', $country, $out);
	}
	if(stristr($out, '[CITY]')){
		$out = str_ireplace('[CITY]', $city, $out);
	}
	if(stristr($out, '[LANG]')){
		$out = str_ireplace('[LANG]', $lang, $out);
	}
	if(stristr($out, '[KEY_SE]')){
		keys_se();
		$key_se = urlencode($key_se);
		$out = str_ireplace('[KEY_SE]', $key_se, $out);
	}
	if(stristr($out, '[DOMAIN]')){
		$out = str_ireplace('[DOMAIN]', $domain, $out);
	}
	if(stristr($out, '[USERAGENT]')){
		$out = str_ireplace('[USERAGENT]', $useragent, $out);
	}
	if(stristr($out, '[REGION]')){
		$out = str_ireplace('[REGION]', $region, $out);
	}
	if(stristr($out, '[DEVICE]')){
		$out = str_ireplace('[DEVICE]', $device, $out);
	}
	if(stristr($out, '[CID]')){
		$out = str_ireplace('[CID]', $id.$cid_delimiter.$cid, $out);
	}
	if($redirect == 'api'){
		if($debug != 1){
			if($api_key != $api_key_host){
				exit();
			}
		}
		$array = array();
		$array[] = $out;
		$array[] = $lang;
		$array[] = $country;
		$array[] = $city;
		$array[] = $device;
		$array[] = $operator;
		$array[] = $bot;
		$array[] = $uniq;
		$array[] = $region;
		$api_data = serialize($array);
		echo $api_data;
		exit();
	}
	if($redirect == 'curl'){
		$ch = curl_init($out);
		@curl_setopt($ch, CURLOPT_HEADER, 0);
		@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		@curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		@curl_setopt($ch, CURLOPT_USERAGENT, $curl_ua);
		$data = curl_exec($ch);
		curl_close($ch);
		if(utf8_bad_find($data) !== false){
			$data = iconv('windows-1251', 'utf-8', $data);
		}
		$x = 0;
		$curl_ex = explode("\n", $curl);
		while(!empty($curl_ex[$x])){
			$str = explode("|||", $curl_ex[$x]);
			$find = trim(html_entity_decode($str[0], ENT_QUOTES));
			if(get_magic_quotes_gpc() == 1){
				$find = stripslashes($find);
			}
			$replace = trim(html_entity_decode($str[1], ENT_QUOTES));
			if(get_magic_quotes_gpc() == 1){
				$replace = stripslashes($replace);
			}
			if(stristr($replace, '[KEY]')){
				$replace = str_ireplace('[KEY]', $key, $replace);
			}
			$data = str_ireplace($find, $replace, $data);
			$x++;
		}
		echo $data;
		exit();
	}
	if($redirect == 'http_redirect'){
		header("Location: $out");
		exit();
	}
	if($redirect == 'iframe'){
		echo 'var splashpage = {
	splashenabled: 1,
	splashpageurl: "'.$out.'",
	enablefrequency: 0,
	displayfrequency: "2 days",
	cookiename: ["splashpagecookie", "path=/"],
	autohidetimer: 0,
	launch: false,
	browserdetectstr:(window.opera && window.getSelection) || (!window.opera && window.XMLHttpRequest),
	output: function(){
		document.write(\'<style>body {overflow: hidden;}</style>\');
		document.write(\'<div id="slashpage" style="position: absolute; z-index: 10000; color: white; background-color:white">\');
		document.write(\'<iframe name="splashpage-iframe" src="about:blank" style="margin:0; border:0; padding:0; width:100%; height: 100%"></iframe>\');
		document.write(\'<br />&nbsp;</div>\');
		this.splashpageref = document.getElementById("slashpage");
		this.splashiframeref = window.frames["splashpage-iframe"];
		this.splashiframeref.location.replace(this.splashpageurl);
		this.standardbody = (document.compatMode == "CSS1Compat") ? document.documentElement : document.body;
		if(!/safari/i.test(navigator.userAgent)) this.standardbody.style.overflow = "hidden";
		this.splashpageref.style.left = 0;
		this.splashpageref.style.top = 0;
		this.splashpageref.style.width = "100%";
		this.splashpageref.style.height = "100%";
		this.moveuptimer = setInterval("window.scrollTo(0,0)", 50);
	},
	closeit: function(){
		clearInterval(this.moveuptimer);
		this.splashpageref.style.display = "none";
		this.splashiframeref.location.replace("about:blank");
		this.standardbody.style.overflow = "auto";
	},
	init: function(){
		if(this.enablefrequency == 1){
			if(/sessiononly/i.test(this.displayfrequency)){
				if(this.getCookie(this.cookiename[0] + "_s") == null){
					this.setCookie(this.cookiename[0] + "_s", "loaded");
					this.launch = true;
				}
			}
			else if(/day/i.test(this.displayfrequency)){
				if(this.getCookie(this.cookiename[0]) == null || parseInt(this.getCookie(this.cookiename[0])) != parseInt(this.displayfrequency)){
					this.setCookie(this.cookiename[0], parseInt(this.displayfrequency), parseInt(this.displayfrequency));
					this.launch = true;
				}
			}
			} else this.launch = true; if(this.launch){
				this.output();
				if(parseInt(this.autohidetimer) > 0) setTimeout("splashpage.closeit()", parseInt(this.autohidetimer) * 1000);
			}
	},
	getCookie: function(Name){
		var re = new RegExp(Name + "=[^;]+", "i");
		if(document.cookie.match(re)) return document.cookie.match(re)[0].split("=")[1];
		return null;
	},
	setCookie: function(name, value, days){
		var expireDate = new Date();
		if(typeof days != "undefined"){
			var expstring = expireDate.setDate(expireDate.getDate() + parseInt(days));
			document.cookie = name + "=" + value + "; expires=" + expireDate.toGMTString() + "; " + splashpage.cookiename[1];
		} else document.cookie = name + "=" + value + "; " + splashpage.cookiename[1];
	}
};
if(splashpage.browserdetectstr && splashpage.splashenabled == 1) splashpage.init();
		';
		exit();
	}
	if($redirect == 'iframe_redirect'){
		echo '<!DOCTYPE html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
<iframe src="javascript:parent.location=\''.$out.'\'" style="visibility:hidden"></iframe>
<script>
    function go() {location.replace("'.$out.'")}
    window.setTimeout("go()", 1000)
</script>
</body>
</html>';
		exit();
	}
	if($redirect == 'iframe_selection'){
		echo '<script type="text/javascript">
function process(){
	top.location = "'.$out.'";
}
window.onerror = process;
if(top.location.href != window.location.href){
	process()
}
</script>';
		exit();
	}
	if($redirect == 'js_redirect'){
		echo '<!DOCTYPE html>
<head>
<meta http-equiv="refresh" content="1; URL='.$out.'">
<script type="text/javascript">window.location = "'.$out.'";</script>
</head>
<body>
The Document has moved <a href="'.$out.'">here</a>
</body>
</html>
		';
		exit();
	}
	if($redirect == 'js_selection'){
		echo 'function process(){
	window.location = "'.$out.'";
}
window.onerror = process;
process()
        ';
		exit();
	}
	if($redirect == 'javascript'){
		echo $out;
		exit();
	}
	if($redirect == 'meta_refresh'){
		echo '<!DOCTYPE html>
<head>
<meta http-equiv="refresh" content="0; URL='.$out.'">
</head>
<body>
</body>
</html>';
		exit();
	}
	if($redirect == 'show_out'){
		if($debug != 1){
			if($api_key != $api_key_host){
				exit();
			}
		}
		echo $out;
		exit();
	}
	if($redirect == 'show_page_html'){
		echo '<!DOCTYPE html>
<head>
<meta name="robots" content="noindex,nofollow">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
'
.$out.
'
</body>
</html>';
		exit();
	}
	if($redirect == 'show_text'){
		echo $out;
		exit();
	}
	if($redirect == 'stop'){
		exit();
	}
	if($redirect == 'under_construction'){
		echo '<!DOCTYPE html>
<head>
<meta name="robots" content="noindex,nofollow">
<meta name="description" content="Under construction">
<title>Страница в разработке.</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
<br>
<center><img src="http://'.$path.'/template/img/404.png" border=0></center>
</body>
</html>';
		exit();
	}
	if($redirect == '403_forbidden'){
		header('HTTP/1.0 403 Forbidden', true, 403);
		echo '<!DOCTYPE html>
<head>
<title>Access forbidden!</title>
</head>
<body>
<h1>Access forbidden!</h1>
<p>
You don\'t have permission to access the requested object. It is either read-protected or not readable by the server.
<br>
If you think this is a server error, please contact the <a href="mailto:[no address given]">webmaster</a>.
</p>
<h2>Error 403</h2>
</body>
</html>';
		exit();
	}
	if($redirect == '404_not_found'){
		header('HTTP/1.0 404 Not Found', true, 404);
		echo '<!DOCTYPE html>
<head>
<title>Object not found!</title>
</head>
<body>
<h1>Object not found!</h1>
<h2>Error 404</h2>
</body>
</html>';
		exit();
	}
	if($redirect == '500_server_error'){
		header('HTTP/1.0 500 Internal Server Error', true, 500);
		echo '<!DOCTYPE html>
<head>
<title>Server error!</title>
</head>
<body>
<h1>Server error!</h1>
<p>
The server encountered an internal error and was unable to complete your request. Either the server is overloaded or there was an error in a CGI script.
</p>
<h2>Error 500</h2>
</body>
</html>';
		exit();
	}
}
function search_in_database($networks, $ip){
	global $label;
	$label = '';
	$ip = trim($ip);
	for($i=0;$i<count($networks);$i++){
		if(trim($networks[$i]{0}) == '#'){
			$label = trim(substr($networks[$i], 1));
		}
		if(trim($networks[$i]{0}) != '#'){
			$networks[$i] = trim($networks[$i]);
			if($ip == $networks[$i]){
				return true;
			}
		}
		if(trim($networks[$i]{0}) != '#' && strstr($networks[$i], '/')){
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
				$ip_arr = explode('/', $networks[$i]);
				$network_long = ip2long($ip_arr[0]);
				$x = ip2long($ip_arr[1]);
				$mask =  long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
				$ip_long = ip2long($ip);
				if(($ip_long & $mask) == ($network_long & $mask)){
					return true;
				}
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
				$ip_bin = inet_pton($ip);
				list($first_addr_str, $prefix_len) = explode('/', $networks[$i]);
				$first_addr_bin = inet_pton($first_addr_str);
				$first_addr_hex = unpack('H*', $first_addr_bin);
				$first_addr_hex = reset($first_addr_hex);
				$flex_bits = 128 - $prefix_len;
				$last_addr_hex = $first_addr_hex;
				$n_pos = 31;
				while($flex_bits > 0){
					$orig = substr($last_addr_hex, $n_pos, 1);
					$orig_val = hexdec($orig);
					$new_val = $orig_val | (pow(2, min(4, $flex_bits)) - 1);
					$new = dechex($new_val);
					$last_addr_hex = substr_replace($last_addr_hex, $new, $n_pos, 1);
					$flex_bits -= 4;
					$n_pos -= 1;
				}
				$last_addr_bin = pack('H*', $last_addr_hex);
				if($ip_bin >= $first_addr_bin && $ip_bin <= $last_addr_bin){
					return true;
				}
			}
		}
		if(trim($networks[$i]{0}) != '#' && strstr($networks[$i], '-')){
			$ip_arr = explode('-', $networks[$i]);
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
				if(ip2long($ip) >= ip2long(trim($ip_arr[0])) && ip2long($ip) <= ip2long(trim($ip_arr[1]))){
					return true;
				}
			}
			if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
				$ip_bin = inet_pton($ip);
				$first_addr_bin = inet_pton(trim($ip_arr[0]));
				$last_addr_bin = inet_pton(trim($ip_arr[1]));
				if($ip_bin >= $first_addr_bin && $ip_bin <= $last_addr_bin){
					return true;
				}
			}
		}
	}
	$label = '';
}
function logs(){
	global $id, $g_name, $s_name, $log_folder, $out, $key, $redirect, $device, $operator, $country, $city, $region, $lang, $uniq, $bot, $ipuser, $referer, $useragent, $domain, $se, $g_log, $log_save, $db, $empty, $log_bots, $log_out, $log_ref, $log_key, $log_ua, $counter, $timeout, $postback, $cid, $cid_length;
	if($log_bots != 1 && $bot != $empty){return;}
	$date = (date("d-m-Y"));
	if($g_log == 1){
		$time = (date("H:i:s"));
		$strtotime = strtotime("now");
		$table = 'log_'.strtotime(date('d-m-Y'));
		if(!$db->querySingle("SELECT * FROM sqlite_master WHERE type='table' AND name='$table';")){
			$db->querySingle("PRAGMA encoding = 'UTF-8'; PRAGMA journal_mode=WAL; CREATE TABLE $table (id INTEGER PRIMARY KEY, date TEXT, time TEXT, ngroup TEXT, nstream TEXT, out TEXT, keyword TEXT, redirect TEXT, device TEXT, operator TEXT, country TEXT, city TEXT, region TEXT, lang TEXT, uniq INTEGER, bot INTEGER, ipuser TEXT, referer TEXT, useragent TEXT, domain TEXT, se TEXT, strtotime INTEGER, counter TEXT, cid TEXT, postback TEXT);");
			$count = $db->querySingle("SELECT COUNT(rowid) FROM sqlite_master WHERE type='table';");
			$del = strtotime(date("d-m-Y", strtotime('-'.$log_save.' day')));
			while($count != 0){
				$t = $db->querySingle("SELECT name FROM sqlite_master WHERE rowid = '$count'");
				$t = str_ireplace('log_', '', $t);
				if($t <= $del){
					$t = 'log_'.$t;
					$t_del[] = $t;
				}
				$count--;
			}
		}
		$g_name_wr = SQLite3::escapeString($g_name);
		$s_name_wr = SQLite3::escapeString($s_name);
		$out_wr = SQLite3::escapeString(htmlentities($out, ENT_QUOTES, 'UTF-8'));
		$key_wr = SQLite3::escapeString($key);
		$city_wr = SQLite3::escapeString($city);
		$region_wr = SQLite3::escapeString($region);
		$lang_wr = SQLite3::escapeString($lang);
		$ipuser_wr = SQLite3::escapeString($ipuser);
		$referer_wr = SQLite3::escapeString($referer);
		$useragent_wr = SQLite3::escapeString($useragent);
		$domain_wr = SQLite3::escapeString($domain);
		$operator_wr = $operator;
		$postback_wr = $postback;
		$cid = substr(md5(microtime(1)), 0, $cid_length);
		if(empty($s_name_wr)){$s_name_wr = $empty;}
		if(empty($key_wr)){$key_wr = $empty;}
		if(empty($out_wr)){$out_wr = $empty;}
		if(empty($postback_wr)){$postback_wr = $empty;}
		if(!empty($log_out)){
			$log_out = explode(",", $log_out);
			$x = 0;
			while(!empty($log_out[$x])){
				if($redirect == trim($log_out[$x])){
					$out_wr = $empty;
					break;
				}
				$x++;
			}
		}
		if($log_ref != 1){
			$referer_wr = $empty;
		}
		if($log_ua != 1){
			$useragent_wr = $empty;
		}
		if($log_key != 1){
			$key_wr = $empty;
		}
		$db->querySingle("INSERT INTO $table (date, time, ngroup, nstream, out, keyword, redirect, device, operator, country, city, region, lang, uniq, bot, ipuser, referer, useragent, domain, se, strtotime, counter, cid, postback) VALUES ('$date', '$time', '$g_name_wr', '$s_name_wr', '$out_wr', '$key_wr', '$redirect', '$device', '$operator', '$country', '$city_wr', '$region_wr', '$lang_wr', '$uniq', '$bot', '$ipuser_wr', '$referer_wr', '$useragent_wr', '$domain_wr', '$se', '$strtotime', '$counter', '$cid', '$postback_wr')");
		$db->querySingle("COMMIT;");
		$db->close();
		if(isset($t_del)){
			$db = new SQLite3($log_folder.'/'.$id.'.db');
			$db->busyTimeout($timeout);
			$db->exec('PRAGMA journal_mode=WAL;');
			$db->querySingle("BEGIN IMMEDIATE;");
			foreach($t_del as $t){
				$db->querySingle("DROP TABLE '$t';");
			}
			$db->querySingle("COMMIT;");
			$db->querySingle("VACUUM;");
			$db->close();
		}
	}
}
function utf8_bad_find($str){
	$utf8_bad =
	'([\x00-\x7F]'.
	'|[\xC2-\xDF][\x80-\xBF]'.
	'|\xE0[\xA0-\xBF][\x80-\xBF]'.
	'|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'.
	'|\xED[\x80-\x9F][\x80-\xBF]'.
	'|\xF0[\x90-\xBF][\x80-\xBF]{2}'.
	'|[\xF1-\xF3][\x80-\xBF]{3}'.
	'|\xF4[\x80-\x8F][\x80-\xBF]{2}'.
	'|(.{1}))';
	$pos = 0;
	$badList = array();
	while(preg_match('/'.$utf8_bad.'/S', $str, $matches)){
		$bytes = strlen($matches[0]);
		if(isset($matches[2]))
			return $pos;
		$pos += $bytes;
		$str = substr($str, $bytes);
	}
	return false;
}
function trash($trash){
	if(!empty($trash)){
		header("Location: $trash");
		exit();
	}
	else{
		exit();
	}
}
function sep_str(){
	global $sep_str, $sep_data;
	$sep_str = trim(array_shift($sep_data));
	if(!empty($sep_str)){
		implode("\n", $sep_data);
	}
	else{
		$sep_str= 'END';
	}
}
function s_separation(){
	global $s_out, $s_separation, $s_separation_file, $key, $sep_data, $sep_str;
	upper_replace();
	$sep_data = file_get_contents('database/'.$s_separation_file);
	$sep_data = explode("\n", $sep_data);
	while($sep_str != 'END'){
		sep_str();
		if($sep_str != 'END'){
			$x = explode(";", $sep_str);
			$sep_name = $x[0];
			if(stristr($key, $sep_name)){
				$s_out = $x[1];
				break;
			}
		}
	}
}
function save_keys(){
	global $g_save_keys, $bot, $key, $keys_folder, $g_name, $empty;
	if($g_save_keys == 1 && $bot == $empty && !empty($key)){
		$date = (date("d-m-Y"));
		if(!file_exists($keys_folder)){
			mkdir($keys_folder, 0755);
		}
		if(!file_exists($keys_folder.'/'.$g_name)){
			mkdir($keys_folder.'/'.$g_name, 0755);
			file_put_contents($keys_folder.'/'.$g_name.'/'.'.htaccess', "<Files *.dat>\nDeny from all\n</Files>", LOCK_EX);
		}
		file_put_contents($keys_folder.'/'.$g_name.'/'.$date.'.dat', $key."\n", FILE_APPEND | LOCK_EX);
	}
}
function keys_se(){
	global $bot, $referer, $empty, $key_se;
	if($bot == $empty && !empty($referer)){
		$key_se = '';
		if(stristr($referer, 'google') || stristr($referer, 'yandex') || stristr($referer, 'mail.ru') || stristr($referer, 'rambler.ru') || stristr($referer, 'tut.by') || stristr($referer, 'nigma.ru')){
			$query = '';
			if(stristr($referer, 'google') && !stristr($referer, '&q=&') && !stristr($referer, '?q=&')){$query = 'q';}
			if(stristr($referer, 'mail.ru') && !stristr($referer, '&q=&') && !stristr($referer, '?q=&')){$query = 'q';}
			if(stristr($referer, 'rambler.ru') && !stristr($referer, '&query=&') && !stristr($referer, '?query=&')){$query = 'query';}
			if(stristr($referer, 'tut.by') && !stristr($referer, '&query=&') && !stristr($referer, '?query=&')){$query = 'query';}
			if(stristr($referer, 'yandex') && !stristr($referer, '&text=&') && !stristr($referer, '?text=&')){$query = 'text';}
			if(stristr($referer, 'nigma.ru') && !stristr($referer, '&s=&') && !stristr($referer, '?s=&')){$query = 's';}
			if(preg_match("~^.*[?&]$query=(.+?)&.*$~", $referer, $matches)){
				$key_se = trim(urldecode($matches[1]));
			}
			else{
				if(preg_match("~^.*[?&]$query=(.*)$~", $referer, $matches)){
					$key_se = trim(urldecode($matches[1]));
				}
			}
			if(!empty($key_se)){
				if(utf8_bad_find($key_se) !== false){
					$key_se = iconv('windows-1251', 'utf-8', $key_se);
				}
			}
		}
	}
}
function save_keys_se(){
	global $bot, $keys_folder, $g_name, $empty, $key_se;
	if($bot == $empty && !empty($key_se)){
		$date = (date("d-m-Y"));
		if(!file_exists($keys_folder)){
			mkdir($keys_folder, 0755);
		}
		if(!file_exists($keys_folder.'/'.$g_name)){
			mkdir($keys_folder.'/'.$g_name, 0755);
			file_put_contents($keys_folder.'/'.$g_name.'/'.'.htaccess', "<Files *.dat>\nDeny from all\n</Files>", LOCK_EX);
		}
		file_put_contents($keys_folder.'/'.$g_name.'/'.$date.'-se.dat', $key_se."\n", FILE_APPEND | LOCK_EX);
	}
}
function upper_replace(){
	global $key;
	$key_array = array($key);
	$search = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я');
	$replace = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я');
	$res = str_ireplace($search, $replace, $key_array);
	$key = $res[0];
}
function remote_pars(){
	global $s_out, $s_remote_url, $s_remote_regexp, $s_remote_reserved_out, $s_remote_cache, $st_now, $g_name, $s_name;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $s_remote_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$pars = trim(curl_exec($ch));
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200){
		if(!empty($s_remote_regexp) && substr($s_remote_regexp, 0, 1) == '/'){
			$s_remote_regexp = html_entity_decode($s_remote_regexp);
			if(get_magic_quotes_gpc() == 1){
				$s_remote_regexp = stripslashes($s_remote_regexp);
			}
			if(preg_match($s_remote_regexp, $pars, $matches)){
				if(!empty($matches[1])){
					$pars_res = $matches[1];
				}
			}
			else{
				$pars_res = '';
			}
		}
		else{
			$pars_res = $pars;
		}
		if(empty($pars_res)){
			$pars_res = $s_remote_reserved_out;
		}
		if($s_remote_cache != 0){
			$dat = $st_now.';'.$pars_res;
			file_put_contents('temp/'.$g_name.'_'.$s_name, $dat, LOCK_EX);
		}
		$s_out = str_ireplace('[REMOTE]', $pars_res, $s_out);
	}
	else{
		$s_out = $s_remote_reserved_out;
		if($s_remote_cache != 0){
			$dat = $st_now.';'.$s_out;
			file_put_contents('temp/'.$g_name.'_'.$s_name, $dat, LOCK_EX);
		}
	}
	curl_close($ch);
}
function err_404(){
	header('HTTP/1.0 404 Not Found', true, 404);
	exit();
}
?>
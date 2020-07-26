<?php
define("INDEX", "yes");
require_once 'config.php';
@ini_set('memory_limit', '-1');
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
if(!file_exists('temp')){
	mkdir('temp', 0755);
}
curl($update_ip_url);
sort_ip();
unlink('temp/webcrawlers.dat');
exit();
function sort_ip(){
	global $update_ip;
	$tmp_db = file('temp/webcrawlers.dat');
	for($i=0; $i<count($tmp_db); $i++){
		if(trim($tmp_db[$i]{0}) == '#'){
			$se = 0;
			if(stristr($tmp_db[$i], 'baidu')){
				$se = 'baidu';
			}
			if(stristr($tmp_db[$i], 'bing') || stristr($tmp_db[$i], 'msnbot')){
				$se = 'bing';
			}
			if(stristr($tmp_db[$i], 'google')){
				$se = 'google';
			}
			if(stristr($tmp_db[$i], 'mail')){
				$se = 'mail';
			}
			if(stristr($tmp_db[$i], 'yahoo')){
				$se = 'yahoo';
			}
			if(stristr($tmp_db[$i], 'yandex')){
				$se = 'yandex';
			}
		}
		else{
			if(!empty($se)){
				$ip = trim($tmp_db[$i]);
				if(!empty($ip)){
					if($se == 'baidu'){
						$baidu[] = $ip;
					}
					if($se == 'bing'){
						$bing[] = $ip;
					}
					if($se == 'google'){
						$google[] = $ip;
					}
					if($se == 'mail'){
						$mail[] =  $ip;
					}
					if($se == 'yahoo'){
						$yahoo[] = $ip;
					}
					if($se == 'yandex'){
						$yandex[] = $ip;
					}
				}
			}
			else{
				$ip = trim($tmp_db[$i]);
				if(!empty($ip)){
					$others[] = $ip;
				}
			}
		}
	}
	unset($tmp_db);
	$name = 'baidu';
	save($name, $baidu);
	unset($baidu);
	$name = 'bing';
	save($name, $bing);
	unset($bing);
	$name = 'google';
	save($name, $google);
	unset($google);
	$name = 'mail';
	save($name, $mail);
	unset($mail);
	$name = 'yahoo';
	save($name, $yahoo);
	unset($yahoo);
	$name = 'yandex';
	save($name, $yandex);
	unset($yandex);
	$name = 'others';
	save($name, $others);
	unset($others);
}
function curl($update_ip_url){
	global $curl_ua;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $update_ip_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $curl_ua);
	$data = curl_exec($ch);
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200){
		file_put_contents('temp/webcrawlers.dat', $data, LOCK_EX);
	}
	else{
		exit();
	}
	curl_close($ch);
	unset($data);
}
function save($name, $data){
	global $update_ip;
	if($update_ip == 1){
		$file = trim(file_get_contents("database/ip_$name.dat"));
		$file = explode("\n", $file);
		$data = array_merge($file, $data);
		unset($file);
	}
	$data = array_unique($data);
	sort($data);
	$data = implode("\n", $data);
	file_put_contents("database/ip_$name.dat", $data."\n", LOCK_EX);
	unset($data);
}
?>
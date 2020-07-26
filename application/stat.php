<?php
@ini_set('display_errors', 0);
@error_reporting(0);
$s_id = 'dwl';//ID группы
$s_api_key_host = 'LmRe4q';//API ключ
$s_url = 'http://tds.com/?api=';//ссылка на ТДС (замените только домен)
$s_em_referer = 0;//если пустой реферер - это бот (0/1)
$s_em_useragent = 1;//если пустой юзерагент - это бот (0/1)
$s_ipv6 = 1;//если IP адрес IPV6 - это бот (0/1)
$s_rd_bots = 0;//отправлять данные ботов на ТДС (0/1)
$s_rotator = 1;//устанавливать cookies (0/1)
$s_n_cookies = 'qwerty';//название cookies для посетителей
$s_t_cookies = 3600;//время жизни cookies в секундах
$s_connect = 1;//тип соединения, file_get_contents или curl (0/1)
$s_timeout_connect = 10;//таймаут соединения в секундах (только для curl)
$s_ip_serv_seodor = '';//если используете SEoDOR пропишите IP его серверной части
$s_status = 1;//выключить/включить (0/1)
/*Ниже ничего не изменяйте*/
if($s_status == 1){
	$s_bot = '';
	$s_uniq = '';
	$s_empty = '-';
	$s_bot = $s_empty;
	$s_res = '';
	if(!empty($_SERVER['HTTP_USER_AGENT'])){
		$s_useragent = $_SERVER['HTTP_USER_AGENT'];
	}
	else{
		if($s_em_useragent == 1){
			$s_bot = 'ua_empty';
		}
		$s_useragent = $s_empty;
	}
	if(!empty($_SERVER['HTTP_REFERER'])){
		$s_referer  = $_SERVER['HTTP_REFERER'];
		$s_ref = $s_referer;
		if(stristr($s_referer, 'google')){$s_se = 'google';}
		if(stristr($s_referer, 'yandex')){$s_se = 'yandex';}
		if(stristr($s_referer, 'mail.ru')){$s_se = 'mail';}
		if(stristr($s_referer, 'yahoo')){$s_se = 'yahoo';}
		if(stristr($s_referer, 'bing')){$s_se = 'bing';}
		if(empty($s_se)){$s_se = $s_empty;}
	}
	else{
		if($s_bot == $s_empty && $s_em_referer == 1){
			$s_bot = 'ref_empty';
		}
		$s_referer = $s_empty;
		$s_ref = '';
		$s_se = $s_empty;
	}
	if(stristr($s_useragent, 'baidu')){$s_bot = 'baidu';}
	if(stristr($s_useragent, 'bing') || stristr($s_useragent, 'msnbot')){$s_bot = 'bing';}
	if(stristr($s_useragent, 'google')){$s_bot = 'google';}
	if(stristr($s_useragent, 'mail.ru')){$s_bot = 'mail';}
	if(stristr($s_useragent, 'yahoo')){$s_bot = 'yahoo';}
	if(stristr($s_useragent, 'yandex')){$s_bot = 'yandex';}
	if($s_bot == $s_empty){
		$signature = 'ahrefs,aport,ask,bot,btwebclient,butterfly,commentreader,copier,crawler,crowsnest,curl,disco,ezooms,fairshare,httrack,ia_archiver,internetseer,java,js-kit,larbin,libwww,linguee,linkexchanger,lwp-trivial,netvampire,nigma,ning,nutch,offline,peerindex,postrank,rambler,semrush,slurp,soup,spider,sweb,teleport,twiceler,voyager,wget,wordpress,yeti,zeus';
		$ex = explode(",", $signature);
		$x = 0;
		while(!empty($ex[$x])){
			if(stristr($s_useragent, $ex[$x])){
				$s_bot = 'signature';
				break;
			}
			$x++;
		}
	}
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']){
		if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")>0){
			$s_ip = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
			$s_ipuser = trim($s_ip[0]);
		}
		elseif(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")===false){
			if(empty($s_ip_serv_seodor)){
				$s_ipuser = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
			}
		}
	}
	if(!isset($s_ipuser)){
		$s_ipuser = trim($_SERVER['REMOTE_ADDR']);
	}
	if(!filter_var($s_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($s_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$s_ipuser = $s_empty;
	}
	if($s_bot == $s_empty && $s_ipv6 == 1 && filter_var($s_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$s_bot = 'ipv6';
	}
	if(($s_bot == $s_empty || $s_rd_bots == 1) && $s_ipuser != $s_empty){
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
			$s_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		}
		if(empty($s_lang)){$s_lang = $s_empty;}
		$s_domain = $_SERVER['HTTP_HOST'];
		$s_page_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if($s_rotator == 1){
			if(!isset($_COOKIE[$s_n_cookies])){
				SetCookie($s_n_cookies, 0, time() + $s_t_cookies, '/');
				$s_c_counter = 0;
				$s_uniq = 'yes';
			}
			else{
				$s_c_counter = $_COOKIE[$s_n_cookies] + 1;
				SetCookie($s_n_cookies, $s_c_counter, time() + $s_t_cookies, '/');
				$s_uniq = 'no';
			}
		}
		else{
			$s_uniq = 'yes';
		}
		$s_data = array(
		"api_key_host"=>$s_api_key_host,
		"id"=>$s_id,
		"ipuser"=>$s_ipuser,
		"lang"=>$s_lang,
		"referer"=>$s_referer,
		"useragent"=>$s_useragent,
		"uniq"=>$s_uniq,
		"domain"=>$s_domain,
		"se"=>$s_se,
		"key"=>""
		);
		$s_data = $s_url.base64_encode(serialize($s_data));
		if(empty($s_ip_serv_seodor) || $s_ipuser != $s_ip_serv_seodor){
			if($s_connect == 0){
				file_get_contents($s_data);
			}
			else{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_TIMEOUT, $s_timeout_connect);
				curl_setopt($ch, CURLOPT_URL, $s_data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_exec($ch);
				curl_close($ch);
			}
		}
	}
}
?>
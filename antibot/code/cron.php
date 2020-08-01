<?php
// Last update date: 2020.04.16
// cron для переноса статистики из мемкешед в базу
if (!isset($ab_version)) die('stop cron');

$ab_time_cron = $ab_config['time'];
for ($x = 0; $x < 2; $x++) {
$date = date('Ymd', $ab_time_cron);

$test = $ab_memcached->get($ab_config['memcached_prefix'].'test_'.$date) + 0;
$auto = $ab_memcached->get($ab_config['memcached_prefix'].'auto_'.$date) + 0;
$click = $ab_memcached->get($ab_config['memcached_prefix'].'click_'.$date) + 0;
$uusers = $ab_memcached->get($ab_config['memcached_prefix'].'uusers_'.$date) + 0;
$husers = $ab_memcached->get($ab_config['memcached_prefix'].'husers_'.$date) + 0;
$whits = $ab_memcached->get($ab_config['memcached_prefix'].'whits_'.$date) + 0;
$bbots = $ab_memcached->get($ab_config['memcached_prefix'].'bbots_'.$date) + 0;
$fakes = $ab_memcached->get($ab_config['memcached_prefix'].'fakes_'.$date) + 0;

// очищаем счет в мемкешеде:
$ab_memcached->set($ab_config['memcached_prefix'].'test_'.$date, 0); 
$ab_memcached->set($ab_config['memcached_prefix'].'auto_'.$date, 0); 
$ab_memcached->set($ab_config['memcached_prefix'].'click_'.$date, 0); 
$ab_memcached->set($ab_config['memcached_prefix'].'uusers_'.$date, 0); 
$ab_memcached->set($ab_config['memcached_prefix'].'husers_'.$date, 0); 
$ab_memcached->set($ab_config['memcached_prefix'].'whits_'.$date, 0); 
$ab_memcached->set($ab_config['memcached_prefix'].'bbots_'.$date, 0); 
$ab_memcached->set($ab_config['memcached_prefix'].'fakes_'.$date, 0); 

$update = @$antibot_db->exec("UPDATE counters SET test = test + ".$test.", auto = auto + ".$auto.", click = click + ".$click.", uusers = uusers + ".$uusers.", husers = husers + ".$husers.", whits = whits + ".$whits.", bbots = bbots + ".$bbots.", fakes = fakes + ".$fakes." WHERE date = '".$date."';");
if ($antibot_db->changes() == 0) {
$add = $antibot_db->exec("INSERT INTO counters (test, auto, click, uusers, husers, whits, bbots, fakes, date) VALUES ('".$test."', '".$auto."', '".$click."', '".$uusers."', '".$husers."', '".$whits."', '".$bbots."', '".$fakes."', '".$date."');");
}
$ab_time_cron = $ab_time_cron - 86400;
}

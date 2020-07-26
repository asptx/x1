<?php
/********************************************\
| Telegram-канал: https://t.me/z_tds         |
| Вход в админку: admin.php (admin/admin)    |
| Сгенерировать хэш MD5: application/md5.php |
\********************************************/
if(!defined("INDEX")){header('HTTP/1.1 403 Forbidden'); die('403 Forbidden');}
date_default_timezone_set('Europe/Moscow');//временная зона (http://php.net/manual/ru/timezones.php)
$login = 'admin';//логин
$pass = '21232f297a57a5a743894a0e4a801fc3';//пароль в md5
$ip_allow = '';//разрешить доступ к админке только с этого IP (IP в md5). Оставьте пустым если блокировка по IP не нужна
$auth = 1;//использовать для авторизации куки или сессии (0/1)
$language = 'ru';//язык (ru/uk/en)
$api_key = 'LmRe4q';//API ключ ([a-Z0-9] (не забудьте его прописать в api.php)
$postback_key = 'ShJi8y';//postback ключ
$trash = 'http://www.ru';//url куда будем сливать весь мусор (переходы в несуществующие группы). Оставьте пустым что бы показать пустую страницу
$ini_folder = 'ini';//название папки с файлами .ini
$admin_page = 'admin.php';//название файла админки (если будете менять не забудьте переименовать сам файл!)
$folder = '';//для работы zTDS в папке укажите ее название, например $folder = 'folder'; или $folder = 'folder1/folder2'; если папка в папке
$keys_folder = 'keys';//название папки для сохранения ключевых слов (http://tds.com/keys)
$log_folder = 'log';//название папки с логами (http://tds.com/log)
$log_days = 15;//показывать в админке ссылки на логи за последние 15 дней (должно быть не больше чем $log_save)
$log_save = 15;//хранить в БД логи за последние 15 дней
$log_limit = 500;//показывать первые 500 записей при просмотре логов
$log_bots = 1;//сохранять в логах ботов (0/1)
$log_out = 'api,iframe,javascript,show_page_html,show_text';//не сохранять в логах ауты для этих типов редиректа
$log_ref = 1;//сохранять в логах рефереры (0/1)
$log_ua = 1;//сохранять в логах юзерагенты (0/1)
$log_key = 1;//сохранять в логах ключевые слова (0/1)
$log_fs = 15;//размер шрифта в логах
$chart_days = 15;//показывать график за последние 15 дней (должно быть не больше чем $log_save)
$chart_weight = 200;//высота графика в пикселях
$chart_bots = 1;//показывать статистику ботов в графиках (0/1)
$stat_uniq = 1;//показывать в статистике хиты или уники (0/1)
$stat_rm = 1;//показывать правое меню (0/1)
$stat_op = 1;//типы статистики в "Источниках" (0 - хиты+уники+WAP; 1 - хиты+уники+устройства+WAP;)
$n_cookies = 'cu';//название cookies
$caplen = 6;//количество букв в каптче (0 - каптча отключена)
$cid_length = 10;//длина CID для постбэка
$cid_delimiter = ';';//разделитель данных внутри CID
$ipgrabber_token = '';//API ключ от IPGrabber
$ipgrabber_update = 0;//каждые 360 минут обновлять список ботов IPGrabber (0 - обновление отключено)
$update_ip_url = 'http://ru.myip.ms/files/bots/live_webcrawlers.txt';//ссылка на список IP ботов (зеркало: http://ztds.info/bots/webcrawlers.dat)
$update_ip = 1;//тип обновления IP ботов (0 - удалить старые IP и сохранить новые; 1 - добавить новые IP к старому списку)
$curl_ua = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0';//useragent для CURL
$disable_tds = 0;//отключить TDS (0/1)
$error_log = 1;//сохранение ошибок PHP в файле php_errors.log (0/1)
$display_errors = 0;//вывод ошибок PHP на экран (0/1)
/*Ниже ничего не изменяйте*/
$timeout = 60000;
$debug = 0;
$empty = '-';
$version = 'v.0.7.4';
?>
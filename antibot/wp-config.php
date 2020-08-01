<?php
define( 'WPCACHEHOME', '/var/www/html/mewe.monster/wp-content/plugins/wp-super-cache/' );
define('WP_CACHE', true);
$domain = idn_to_utf8($_SERVER['SERVER_NAME'], 0, INTL_IDNA_VARIANT_UTS46);
$domain_dir = "/var/www/html/$domain";
$domain_salt = md5($domain);

include("$domain_dir/wp-content/plugins/d/d_ic.php");

define('DB_NAME', str_replace(array('-', '.'), '_', $domain));
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

define('AUTH_KEY', '%~7%GH5K&$8qS!O19juQk%dZiiR1;s6o'.$domain_salt);
define('SECURE_AUTH_KEY', 'bzWlNE]y[]]&fpL]O;SlFpzAsih4~DhY'.$domain_salt);
define('LOGGED_IN_KEY', 'HUP0o;n4v?cv=s__Xdn}aUj(AW-!4Q8^'.$domain_salt);
define('NONCE_KEY', 'ow;LargkWu;IMDF}pnhtwh{)&dzS&9Ae'.$domain_salt);
define('AUTH_SALT', 'VtHe%%0@|C9@ea6|Q_WQ^PTGy;a?-O0u'.$domain_salt);
define('SECURE_AUTH_SALT', 'ut[z?8R6[1wu5Spq32C:m^uFZA@-o3[^'.$domain_salt);
define('LOGGED_IN_SALT', 'jOR&nGAo8wdOz6ifh&aWM8I!}$a?+YSQ'.$domain_salt);
define('NONCE_SALT', '$#LrB5Dw1uP~nfn0yhs4dx37Fn(5#Etk'.$domain_salt);

$table_prefix = 'wp_';

define('WP_DEBUG', false);

if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__).'/');

define('WP_CONTENT_DIR', "$domain_dir/wp-content");

require_once(ABSPATH.'wp-settings.php');

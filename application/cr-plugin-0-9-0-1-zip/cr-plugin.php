<?php
/**
* Plugin Name: CrakRevenue
* Description: A plugin that allows you to easily add several kinds of ads to your website
* Version: 0.9.0
* Author: CrakRevenue
* Author URI: http://www.crakrevenue.com
* License: GPL12
*/

//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $crak_errors;
$crak_errors = array();

require_once('data.php');
require_once('modules/banners.php');
require_once('modules/cams.php');
require_once('modules/intext.php');
require_once('modules/native.php');
require_once('modules/popup.php');

function load_options() {
    global $default_crak_settings;
    $output_settings = $default_crak_settings;

    $crak_user_option = get_option('crak_settings', $default_crak_settings);

    foreach ($crak_user_option as $v => $k) {
        if (count($k) > 1) {
            foreach ($k as $vv => $kk) {
                $output_settings[$v][$vv] = $kk;
            }
        } else {
            $output_settings[$v] = $k;
        }
    }

    return $output_settings;
}

function crak_is_assoc($var) {
    return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
}

function crak_get_settings() {
    $output = array();

    $crak_user_option = load_options();//get_option('crak_settings', $default_crak_settings);

    foreach($crak_user_option as $k => $v) {
        if (crak_is_assoc($v)) {
            foreach ($v as $kk => $vv) {
                $output[$k][$kk] = $vv;
            }
        } else {
            $output[$k] = $v;
        }
    }

    return $output;
}

function crak_options() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    include('setting-templates/settings_page.php');
}

function crak_page_create() {
    $page_title = 'CrakRevenue';
    $menu_title = 'CrakRevenue';
    $capability = 'manage_options';
    $menu_slug = __FILE__;
    $function = 'crak_options';
    $icon = plugins_url('/logo16.png' ,__FILE__ );
    $position = 99;

    add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position);
}

function crak_cloaked_link_handling() {
    $parts = explode('/', $_SERVER["REQUEST_URI"]);
    if (count($parts) > 2 && $parts[count($parts)-2] == 'out') {
        $url = $parts[count($parts) - 1];
    } else if (isset($_GET['url'])) {
        $url = $_GET['url'];
    }

    if (!empty($url)) {
        header('Location: ' . urldecode($url), true, 302);
        exit();
    }
}

function crak_cloak_link($url, $content = '', $attributes = array()) {
    $url = '/out/?url=' . urlencode($url);

    if (!empty($content)) {
        $html_attributes = '';

        if (!isset($attributes['rel'])) {
            $attributes['rel'] = 'nofollow';
        }

        foreach ($attributes as $name => $value) {
            $html_attributes .= ' ' . $name . '="' . $value . '"';
        }

        return '<a href="' . esc_attr($url) . '" ' . esc_attr($html_attributes) . '>' . esc_html($content) . '</a>';
    } else {
        return esc_attr($url);
    }
}

function crak_check_options () {
    global $default_crak_settings;
    global $crakrevenue_links;

    $crak_user_option = load_options();

    $crak_user_option2 = $crak_user_option;

    if (!intval($crak_user_option['aff_id'])) {
        $crak_user_option['aff_id'] = $default_crak_settings['aff_id'];
        $crak_errors[] = 'Invalid CrakRevenue Affiliate ID. Reverting to default';
    }

    if (
        !intval($crak_user_option['pop']['vertical']) ||
        !isset($crakrevenue_links[$crak_user_option['pop']['vertical']]) ||
        !$crakrevenue_links[$crak_user_option['pop']['vertical']]['enabled']
    ) {
        $crak_user_option['pop']['vertical'] = 0;
        $crak_errors[] = 'Invalid popup vertical. Reverting to default';
    }

    if (count($crak_user_option['intext']['vertical'])) {
        foreach($crak_user_option['intext']['vertical'] as $k => $v) {
            if (!isset($crakrevenue_links[$v]) || !$crakrevenue_links[$v]['enabled']) {
                $crak_user_option['intext']['vertical'][$k] = 0;
                $crak_errors[] = 'Invalid vertical for link #' . ($k) . '. Reverting to default';
            }
        }
    } else {
        $crak_user_option['intext']['vertical'] = array(0);
    }

    if (count($crak_user_option['intext']['affsub'])) {
        foreach($crak_user_option['intext']['affsub'] as $k => $v) {
            $clean_tracker = crak_tracker_cleanup($v);
            if ($clean_tracker != $v) {
                $crak_user_option['intext']['affsub'][$k] = $clean_tracker;
                $crak_errors[] = 'Invalid affsub tracker for link #' . ($k) . '. Stripping invalid characters.';
            }
        }
    } else {
        $crak_user_option['intext']['affsub'] = array('');
    }

    if (count($crak_user_option['intext']['source'])) {
        foreach($crak_user_option['intext']['source'] as $k => $v) {
            $clean_tracker = crak_tracker_cleanup($v);
            if ($clean_tracker != $v) {
                $crak_user_option['intext']['source'][$k] = $clean_tracker;
                $crak_errors[] = 'Invalid source tracker for link #' . ($k) . '. Stripping invalid characters.';
            }
        }
    } else {
        $crak_user_option['intext']['source'] = array('');
    }

    if (count($crak_user_option['intext']['words'])) {
        foreach($crak_user_option['intext']['words'] as $k => $v) {
            $clean_word = esc_html($v);
            if ($clean_word != $v) {
                $crak_user_option['intext']['words'][$k] = $clean_word;
                $crak_errors[] = 'Invalid word for link #' . ($k) . '. Escaping invalid characters.';
            }
        }
    } else {
        $crak_user_option['intext']['words'] = array('');
    }

    if (count($crak_user_option['intext']['number'])) {
        foreach($crak_user_option['intext']['number'] as $k => $v) {
            $clean_number = intval($v);
            if ($clean_number != $v) {
                $crak_user_option['intext']['number'][$k] = $clean_number;
                $crak_errors[] = 'Invalid amount for link #' . ($k) . '. Escaping invalid characters.';
            }
        }
    } else {
        $crak_user_option['intext']['number'] = array(50);
    }

    if (count($crak_user_option['intext']['target'])) {
        foreach($crak_user_option['intext']['target'] as $k => $v) {
            $clean_number = intval($v);
            if ($clean_number != $v) {
                $crak_user_option['intext']['target'][$k] = $clean_number;
                $crak_errors[] = 'Invalid word for link #' . ($k) . '. Escaping invalid characters.';
            }
        }
    } else {
        $crak_user_option['intext']['target'] = array();
    }

    $crak_user_option['native']['enabled'] = !!$crak_user_option['native']['enabled'];

    if (!is_array($crak_user_option['native']['tags'])) {
        $crak_user_option['native']['tags'] = array();
    }

    if ($crak_user_option != $crak_user_option2) {
        update_option('crak_settings', $crak_user_option);
    }
}

function crak_rules() {
    add_rewrite_rule('^out$', '/?%{QUERY_STRING} [L]');
}

function load_custom_wp_admin_style_and_script($hook) {
    if(stripos($hook, 'toplevel_page_cr-plugin') === false) {
        return;
    }
    wp_enqueue_script('crak_admin', plugin_dir_url( __FILE__ ) . 'crak_admin.js', array(), false, false);
    wp_enqueue_style('custom_wp_admin_css', plugins_url('style.css', __FILE__));
}

function crak_tracker_cleanup($string) {
    $reserved_http_characters = array(':', '@', '&', '=', '+', '$', ',', '"');

    return str_replace($reserved_http_characters, '', $string);
}

function crak_prefix_activate(){
    register_uninstall_hook( __FILE__, 'crak_prefix_uninstall' );
}
register_activation_hook( __FILE__, 'crak_prefix_activate' );

// And here goes the uninstallation function:
function crak_prefix_uninstall(){
    $all_options = wp_load_alloptions();

    //cache cleanup
    foreach ($all_options as $name => $value) {
        if (stripos($name, 'crak_cams_') !== false) {
            delete_option($name);
        }
    }
}

if (isset($_GET['crak_cams'])) {
    include('crak_cams.php');
    exit();
}

add_action('init', 'crak_rules');

if (is_admin()) {
    add_action('admin_menu', 'crak_page_create');
    add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style_and_script');
} else {
    $crak_user_option = load_options();

    add_action('plugins_loaded', 'crak_cloaked_link_handling');

    if ($crak_user_option['intext']['enabled']) {
        add_action('wp_enqueue_scripts', 'crak_intext');
    }

    if ($crak_user_option['pop']['enabled']) {
        add_action('wp_enqueue_scripts', 'crak_footer_pop');
    }

    if ($crak_user_option['native']['enabled']) {
        add_filter('wp_enqueue_scripts', 'crak_native_ads');
    }
}

add_action('widgets_init', 'crak_banners_widget');
add_action('widgets_init', 'crak_cams_widget');

crak_check_options();

<?php
/**
 * Created by PhpStorm.
 * User: lpainchaud
 * Date: 3/15/2018
 * Time: 9:39 AM
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function my_nonce_message ($translation) {
    if ($translation === 'Are you sure you want to do this?') {
        return 'Your page has expired. Refresh before you try again.';
    }

    return $translation;
}

add_filter('gettext', 'my_nonce_message');

if (count($_POST)) {
    $page = 'dashboard';

    if ($_POST['crak_page'] && in_array($_POST['crak_page'], $crakPages)) {
        $page = $_POST['crak_page'];
    }

    if ($page == 'dashboard' && wp_verify_nonce($_REQUEST['_wpnonce'], 'dashboard')) {
        if (isset($_POST['crak_aff_id'])) $crak_user_option['aff_id'] = intval($_POST['crak_aff_id']) ? intval($_POST['crak_aff_id']) : 1;
        $crak_user_option['pop']['enabled'] = isset($_POST['crak_pop_enabled']);
        $crak_user_option['intext']['enabled'] = isset($_POST['crak_intext_enabled']);
        $crak_user_option['native']['enabled'] = isset($_POST['crak_native_enabled']);
    } else if ($page == 'popup' && wp_verify_nonce($_REQUEST['_wpnonce'], 'popup')) {
        $crak_user_option['pop']['enabled'] = isset($_POST['crak_pop_enabled']);
        if (isset($_POST['crak_pop_vertical'])) $crak_user_option['pop']['vertical'] = intval($_POST['crak_pop_vertical']);
        if (isset($_POST['crak_pop_affsub'])) $crak_user_option['pop']['affsub'] = esc_attr($_POST['crak_pop_affsub']);
        if (isset($_POST['crak_pop_source'])) $crak_user_option['pop']['source'] = esc_attr($_POST['crak_pop_source']);
    } else if ($page == 'intext' && wp_verify_nonce($_REQUEST['_wpnonce'], 'intext')) {
        $crak_user_option['intext']['enabled'] = isset($_POST['crak_intext_enabled']);

        $crak_user_option['intext']['vertical'] = array();
        foreach ($_POST['crak_intext_vertical'] as $k => $v) {
            $crak_user_option['intext']['vertical'][$k] = intval($v);
        }

        $crak_user_option['intext']['words'] = array();
        foreach ($_POST['crak_intext_words'] as $k => $v) {
            $crak_user_option['intext']['words'][$k] = esc_html($v);
        }

        $crak_user_option['intext']['affsub'] = array();
        foreach ($_POST['crak_intext_affsub'] as $k => $v) {
            $crak_user_option['intext']['affsub'][$k] = esc_attr($v);
        }

        $crak_user_option['intext']['source'] = array();
        foreach ($_POST['crak_intext_source'] as $k => $v) {
            $crak_user_option['intext']['source'][$k] = esc_attr($v);
        }

        $crak_user_option['intext']['number'] = array();
        foreach ($_POST['crak_intext_number'] as $k => $v) {
            $crak_user_option['intext']['number'][$k] = intval($v);
        }

        $crak_user_option['intext']['target'] = array();
        foreach ($_POST['crak_intext_target'] as $k => $v) {
            $crak_user_option['intext']['target'][$k] = intval($v);
        }
    } else if ($page == 'native' && wp_verify_nonce($_REQUEST['_wpnonce'], 'native')) {
        $crak_user_option['native']['enabled'] = isset($_POST['crak_native_enabled']);

        if (isset($_POST['crak_native_affsub'])) $crak_user_option['native']['affsub'] = esc_attr($_POST['crak_native_affsub']);
        if (isset($_POST['crak_native_source'])) $crak_user_option['native']['source'] = esc_attr($_POST['crak_native_source']);
        if (isset($_POST['crak_native_nude'])) $crak_user_option['native']['nude'] = in_array($_POST['crak_native_nude'], $crak_native_nude) ? $_POST['crak_native_nude'] : $crak_native_nude[0];
        if (isset($_POST['crak_native_orientation'])) $crak_user_option['native']['orientation'] = in_array($_POST['crak_native_orientation'], $crak_native_orientation) ? $_POST['crak_native_orientation'] : $crak_native_orientation[0];
        $crak_user_option['native']['tags'] = array();
        if (isset($_POST['crak_native_tags'])) {
            foreach($_POST['crak_native_tags'] as $k => $v) {
                $crak_user_option['native']['tags'][] = $v;
            }
        }
        if (isset($_POST['crak_native_font'])) $crak_user_option['native']['font'] = esc_attr($_POST['crak_native_font']);
        if (isset($_POST['crak_native_font_size'])) $crak_user_option['native']['font_size'] = intval($_POST['crak_native_font_size']);
        if (isset($_POST['crak_native_font_weight'])) $crak_user_option['native']['font_weight'] = esc_attr($_POST['crak_native_font_weight']);
        if (isset($_POST['crak_native_lines'])) $crak_user_option['native']['number_lines'] = intval($_POST['crak_native_lines']);
        if (isset($_POST['crak_native_color'])) $crak_user_option['native']['color'] = esc_attr($_POST['crak_native_color']);
        if (isset($_POST['crak_native_color_highlight'])) $crak_user_option['native']['hover_color'] = esc_attr($_POST['crak_native_color_highlight']);
        if (isset($_POST['crak_native_number_thumbnails'])) $crak_user_option['native']['number_thumbnails'] = intval($_POST['crak_native_number_thumbnails']);
        if (isset($_POST['crak_native_width'])) $crak_user_option['native']['width'] = intval($_POST['crak_native_width']);
        if (isset($_POST['crak_native_thumbs_per_row'])) $crak_user_option['native']['thumbs_per_row'] = intval($_POST['crak_native_thumbs_per_row']);
        if (isset($_POST['crak_native_thumbs_per_row'])) $crak_user_option['native']['show_thumbs'] = isset($_POST['crak_native_show_thumbs']) ? 1 : 0;
    }

    update_option('crak_settings', $crak_user_option);
}

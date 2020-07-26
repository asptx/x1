<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add scripts to wp_footer()
function crak_footer_pop() {
    global $crakrevenue_links;

    $crak_user_option = load_options();//get_option('crak_settings', $default_crak_settings);

    $url = isset($crakrevenue_links[$crak_user_option['pop']['vertical']]) && $crakrevenue_links[$crak_user_option['pop']['vertical']]['enabled'] ? $crakrevenue_links[$crak_user_option['pop']['vertical']]['url'] : $crakrevenue_links[0]['url'];

    $url = str_replace('{aff_id}', $crak_user_option['aff_id'], $url) . '&aff_sub2=PUB_wpplugin;LOC_popunder';

    if (!empty($crak_user_option['pop']['affsub'])) {
        $url .= '&aff_sub=' . $crak_user_option['pop']['affsub'];
    }

    if (!empty($crak_user_option['pop']['source'])) {
        $url .= '&source=' . $crak_user_option['pop']['source'];
    }

    wp_enqueue_script('crak_pop', 'http://static.ads.crakmedia.com/ads/popin/latest/popin.js', array(), false, true);

    wp_add_inline_script(
        'crak_pop',
        "       
        var crakPopInParams = {
          url: '" . $url . "',
          width: '90%',
          height: '90%',
          timeout: 15000,
          clickStart: true,
          closeIntent: true,
          borderColor: '#000',
          closeButtonColor: '#000',
          closeCrossColor: '#fff',
          shadowColor: '#000',
          shadowOpacity: '.5',
          shadeColor: '#111',
          shadeOpacity: '.5',
          border: '1px',
          borderRadius: '0px',
          leadOut: true,
          animation: 'slide',
          direction: 'up',
          expireDays: 1,
        };
        ",
        'after'
    );
}

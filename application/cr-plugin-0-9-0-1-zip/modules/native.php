<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function crak_native_ads() {
    if (is_single()) {
        add_filter('the_content', 'crak_native_ads_container');
        $crak_user_option = crak_get_settings();
        wp_enqueue_script('crak_native', plugin_dir_url( __FILE__ ) . 'index.js', array(), false, true);

        wp_add_inline_script('crak_native',
            '(function() {
            var script = document.createElement("script");
            script.async = false;
            script.src = "//plug.plufdsb.com/wdgt/?PRT=' .
            base64_encode(
                'div=1513368766240' .
                '&cff=' .
                '&ff=' . esc_js($crak_user_option['native']['font']) . //Font
                '&cft=' .
                '&fft=Arial' .
                '&brc=000000' .
                '&ibch=000000' .
                '&fc=' . esc_js($crak_user_option['native']['color']) .
                '&htc=' . esc_js($crak_user_option['native']['hover_color']) .
                '&db=' . esc_js($crak_user_option['native']['number_thumbnails']) .
                '&c=' . esc_js($crak_user_option['native']['width']) .
                '&pd=5' .
                '&iw=' . esc_js($crak_user_option['native']['thumbs_per_row']) .
                '&br=0' .
                '&fsz=' . esc_js($crak_user_option['native']['font_size']) .
                '&fst=16' .
                '&ch=' . esc_js($crak_user_option['native']['number_lines']) .
                '&iyn=' . esc_js($crak_user_option['native']['show_thumbs']) .
                '&it=land' .
                '&st=1' .
                '&ta=left' .
                '&titlelength=full' .
                '&tp=2' .
                '&btc=000000' .
                '&dec=' .
                '&fw=' . esc_js($crak_user_option['native']['font_weight']) .
                '&wt=A' .
                '&sexual_orientation=' . esc_js($crak_user_option['native']['orientation']) . '' .
                '&nude_state=' . esc_js($crak_user_option['native']['nude']) . '' .
                '&widget_responsive=1' .
                '&popunder=0' .
                '&customcss=' .
                '&tags0=' . esc_js(implode(';', $crak_user_option['native']['tags'])) .
                '&pbdd=0' .
                '&pbp=bottom' .
                '&pba=right' .
                '&wtdd=0' .
                '&wtv=You may also like' .
                '&fwt=bold' .
                '&fct=00ffff' .
                '&sitelink=0') .
                '&source=' . esc_js($crak_user_option['native']['source']) .
                '&aff_sub=' . esc_js($crak_user_option['native']['affsub']) .
                '&aff_sub2=PUB_wpplugin;LOC_native' .
                '&fid=' . esc_js($crak_user_option['aff_id']) .
                '&file_id=261819";
                var dst = document.getElementsByTagName("script")[0];
                dst.parentNode.insertBefore(script, dst);
            })();'
        );
    }
}

function crak_native_ads_container($content) {
    if(is_single()) {
        $content .= '<div id="nativeAds_1513368766240"></div>';
    }

    return $content;
}




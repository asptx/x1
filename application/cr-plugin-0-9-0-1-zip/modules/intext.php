<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add scripts to wp_footer()
function crak_intext () {
    global $crakrevenue_links;

    $crak_user_option = load_options();//get_option('crak_settings', $default_crak_settings);

    $wordsURLs = array();

    foreach($crak_user_option['intext']['words'] as $k => $word) {
        $wordsURLs[] = array(
            'words' => $word,
            'url' => str_replace('{aff_id}', $crak_user_option['aff_id'], $crakrevenue_links[$crak_user_option['intext']['vertical'][$k]]['url']) . '&aff_sub2=PUB_wpplugin;LOC_links',
            'source' => $crak_user_option['intext']['source'][$k],
            'affsub' => $crak_user_option['intext']['affsub'][$k],
            'number' => $crak_user_option['intext']['number'][$k],
            'target' => isset($crak_user_option['intext']['target'][$k]),
        );
    }

    $script = "";

    foreach ($wordsURLs as $item) {
        $words = explode(',', $item['words']);

        foreach ($words as $word) {
            if ($word != '') {
                //$regex[] = trim($word) . '(?![^<]*?<\/a>|[^<]*?alt)';
                $script .= "crak_intext_limits['" . esc_js(trim($word)) . "'] = " .
                    esc_js((intval($item['number']) ? intval($item['number']) : 9999)) . ";\r\n" .
                    "replace_in_strings('" . esc_js(trim($word)) . "', '" . esc_js($item['url']) .
                    (!empty($item['url']) ? '&source=' . esc_js($item['source']) : '') .
                    (!empty($item['affsub']) ? '&aff_sub=' . esc_js($item['affsub']) : '') .
                "', document.body, " . ($item['target'] ? 'true' : 'false') . ");\r\n";
            }
        }
    }

    wp_enqueue_script('crak_intext', plugin_dir_url( __FILE__ ) . 'crak_intext.js', array(), false, false);

    wp_add_inline_script('crak_intext', "(function() {" . $script . "})();");
}

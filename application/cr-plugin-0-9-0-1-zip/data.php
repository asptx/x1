<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$this_version = "0.9.0";

$crak_data_last_update = get_option('crak_data_last_update', 0);

if ($crak_data_last_update < time() - (60 * 60)) {
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //fetch a static json file containing the ads data so it can be updated to avoid them from being blocked by adblock.
        curl_setopt($ch, CURLOPT_URL, 'http://static.crakrevenue.com/wordpress/data.json');
        $result = curl_exec($ch);
        $crak_data = json_decode($result, true);
        update_option('crak_data', json_decode($result, true));
        update_option('crak_data_last_update', time());

        curl_close($ch);
    } catch (Exception $e) {

    }
}

//update the value of $crak_data_last_update
$crak_data_last_update = get_option('crak_data_last_update', 0);

$crak_data = get_option('crak_data', array());

//if no data is present, load data from local json
if (!isset($crak_data['crakrevenue_links'])) {
    update_option('crak_data', json_decode(file_get_contents(__DIR__ . "/data.json"), true));
}

extract($crak_data);

require_once('default_settings.php');
<?php
/**
 * Created by PhpStorm.
 * User: lpainchaud
 * Date: 12/19/2017
 * Time: 3:31 PM
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $crakrevenue_links;
global $default_crak_settings;
global $crakrevenue_ads;
global $crak_native_tags;
global $crak_native_orientation;
global $crak_native_nude;
global $crak_data_last_update;
global $this_version;
global $current_version;
global $current_version_url;
global $crak_error;

$crak_user_option = load_options(); //get_option('crak_settings', $default_crak_settings);

$lang = 'en';

$crakPages = array('dashboard', 'popup', 'intext', 'native');

include('save-options.php');

?>
<div id="crak_settings" class="dashboard" data-links='<?php echo json_encode($crakrevenue_links) ?>'>
    <h1 id="crak_logo">
        Welcome to CrakRevenue!
    </h1>
    <p id="crak_description">
        The CrakRevenue plugin allows you to monetize your Wordpress blog with the help of 4 affiliate tools.
    </p>
    <?php
    if (count($crak_error)){
        ?>
            <div id="crak_errors">
                <?php
                foreach ($crak_error as $v) {
                    ?>
                        <div class="crak_error"><?php echo $v ?></div>
                    <?php
                }
                ?>
            </div>
        <?php
    }

    if (!$crak_data_last_update) {
        ?>
        <p>Your server does not allow the use of remote files. We can't update your data.</p>
        <?php
    }
    ?>
    <div id="crak_tabs">
        <a href="#page_dashboard" class="selected">Dashboard</a>
        <a href="#page_popup">Popup</a>
        <a href="#page_intext">In-text ads</a>
        <a href="#page_native">Native Ads</a>
    </div>
    <?php include('pages/dashboard.php'); ?>
    <?php include('pages/popup-settings.php'); ?>
    <?php include('pages/intext-settings.php'); ?>
    <?php include('pages/native-settings.php'); ?>
    <span id="crak_required">Required</span>
    <div id="crak_social">
        <p>
            Like the plugin? Help spread the word!
            <a href="https://fr.linkedin.com/company/crakrevenue"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 112.2 112.2"><circle cx="56.1" cy="56.1" r="56.1" fill="#007AB9"/><path d="M89.6 60.6v23.1H76.2V62.2c0-5.4-1.9-9.1-6.8-9.1 -3.7 0-5.9 2.5-6.9 4.9 -0.4 0.9-0.4 2.1-0.4 3.3v22.5H48.7c0 0 0.2-36.5 0-40.3h13.4v5.7c0 0-0.1 0.1-0.1 0.1h0.1v-0.1c1.8-2.7 5-6.7 12.1-6.7C83 42.5 89.6 48.2 89.6 60.6L89.6 60.6zM34.7 24c-4.6 0-7.6 3-7.6 7 0 3.9 2.9 7 7.4 7h0.1c4.7 0 7.6-3.1 7.6-7C42.1 27 39.2 24 34.7 24L34.7 24zM27.9 83.7H41.3V43.4H27.9V83.7z" fill="#F1F2F2"/></svg></a>
            <a href="https://facebook.com/CrakRevenue/"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 112.2 112.2"><circle cx="56.1" cy="56.1" r="56.1" fill="#3B5998"/><path d="M70.2 58.3h-10v36.7H45V58.3h-7.2V45.4h7.2v-8.3c0-6 2.8-15.3 15.3-15.3L71.6 21.8v12.5h-8.2c-1.3 0-3.2 0.7-3.2 3.5v7.6h11.3L70.2 58.3z" fill="#FFF"/></svg></a>
            <a href="https://twitter.com/CrakRevenue"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 112.2 112.2"><circle cx="56.1" cy="56.1" r="56.1" fill="#55ACEE"/><path d="M90.5 40.3c-2.4 1.1-5 1.8-7.7 2.1 2.8-1.7 4.9-4.3 5.9-7.4 -2.6 1.5-5.5 2.7-8.5 3.3 -2.4-2.6-5.9-4.2-9.8-4.2 -7.4 0-13.4 6-13.4 13.4 0 1.1 0.1 2.1 0.3 3.1 -11.1-0.6-21-5.9-27.6-14 -1.2 2-1.8 4.3-1.8 6.7 0 4.7 2.4 8.8 6 11.2 -2.2-0.1-4.3-0.7-6.1-1.7 0 0.1 0 0.1 0 0.2 0 6.5 4.6 11.9 10.8 13.1 -1.1 0.3-2.3 0.5-3.5 0.5 -0.9 0-1.7-0.1-2.5-0.2 1.7 5.3 6.7 9.2 12.5 9.3 -4.6 3.6-10.4 5.7-16.7 5.7 -1.1 0-2.1-0.1-3.2-0.2 5.9 3.8 13 6 20.6 6 24.7 0 38.2-20.4 38.2-38.2 0-0.6 0-1.2 0-1.7C86.4 45.4 88.7 43 90.5 40.3L90.5 40.3z" fill="#F1F2F2"/></svg></a>
        </p>
    </div>
</div>
<?php include('sidebar.php'); ?>
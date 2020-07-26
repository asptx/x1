<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?> 
<form method="POST" id="crak_dashboard">
    <?php wp_nonce_field('dashboard'); ?>
    <input type="hidden" name="crak_page" value="dashboard">

    <div class="first-col">
        <h2><label for="crak_aff_id" class="crak_required" title="The ID number that attributes the sells to you.">CrakRevenue Affiliate ID</label></h2>
        <p>Add your unique CrakRevenue Affiliate ID.</p>
        <input type="text" name="crak_aff_id" id="crak_aff_id" value="<?php echo esc_attr($crak_user_option['aff_id']); ?>">

        <h2>Modules</h2>
        <p>
            Once you've installed and activated our plugin, make sure to <b>check every box</b> to ensure you have
            access to <b>every monetization tool</b>. Each of them has <b>2 tracking fields</b>: "Aff Sub ID" and
            "Source". These fields are optional but they can help you better understand where your conversions come
            from in CrakRevenue's reporting tools.
        </p>
        <input type="checkbox" name="crak_pop_enabled" id="dash_crak_pop_enabled" class="dash_enabler" data-id="crak_pop_enabled" <?php echo isset($crak_user_option['pop']['enabled']) && $crak_user_option['pop']['enabled'] ? 'checked' : ''; ?>>
        <label for="dash_crak_pop_enabled" title="Enables popup ads on your site">Popup</label>
        <br>
        <input type="checkbox" name="crak_intext_enabled" id="dash_crak_intext_enabled" class="dash_enabler" data-id="crak_intext_enabled" <?php echo isset($crak_user_option['intext']['enabled']) && $crak_user_option['intext']['enabled'] ? 'checked' : ''; ?>>
        <label for="dash_crak_intext_enabled" title="Enables in-text ads on your site">In-text Ads</label>
        <br>
        <input type="checkbox" name="crak_native_enabled" id="dash_crak_native_enabled" class="dash_enabler" data-id="crak_native_enabled" <?php echo isset($crak_user_option['native']['enabled']) && $crak_user_option['native']['enabled'] ? 'checked' : ''; ?>>
        <label for="dash_crak_native_enabled" title="Enables native ads on your site">Native Ads</label>
        <br>
        <input type="submit" value="Save Changes" class="button button-primary button-large">
    </div>

    <div class="second-col crak_info">
        <div class="first-row">
            <h2>Getting Started</h2>
            <div class="first-col">
                <h4>First Step</h4>
                <p>
                    First you need an active affiliate account here at
                    <a href="https://affiliates.crakrevenue.com/registration?utm_source=Plugin&utm_medium=Wordpress&utm_campaign=Dashboard" target="_blank">CrakRevenue.com</a>
                    since we will use your Affiliate ID with the plugin.
                </p>
                <!--a href="https://affiliates.crakrevenue.com/registration?utm_source=Plugin&utm_medium=Wordpress&utm_campaign=Dashboard" class="button" target="_blank">Sign Up</a-->
            </div>
            <div class="first-col">
                <h4>Second Step</h4>

                <p>
                    <b>Add your unique CrakRevenue Affiliate ID.</b> To find your unique Affiliate ID please go to <a href="https://affiliates.crakrevenue.com/profile/user-details" target="_blank">your profile page</a> in CrakRevenue.
                </p>
            </div>
        </div>
        <div class="second-row">
            <h4>Widgets</h4>
            <p>
                This plugin adds 2 widgets to the <a href="widgets.php">Widgets page</a>
            </p>
            <div class="widget-desc">
                <h5>CrakRevenue Banners</h5>
                <p>
                    This Widget allows you to add banners to your site.
                </p>
            </div>
            <div class="widget-desc">
                <h5>CrakRevenue Cams</h5>
                <p>
                    This Widget allows you to add a number of online cam girls (or boys if you chose to) on your site related as much as possible to
                    the content of the page. (provided the script finds certain keywords in the page's code)
                </p>
            </div>
            <h4>Note</h4>
            <p id="smartlink_explaination">
                <b>For each tool choose a vertical</b> that best represents your traffic: Adult, Cam, Dating, VOD, Gay or Adult Gaming.
            </p>
        </div>
    </div>
</form>

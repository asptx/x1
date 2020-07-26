<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<form method="POST" id="crak_popup_settings">
    <?php wp_nonce_field('popup'); ?>
    <input type="hidden" name="crak_page" value="popup">
    <input type="checkbox" name="crak_pop_enabled" id="crak_pop_enabled" class="enabler" <?php echo esc_attr(isset($crak_user_option['pop']['enabled']) && $crak_user_option['pop']['enabled'] ? 'checked' : ''); ?>>
    <label for="crak_pop_enabled" title="Enables popup ads on your site">Enabled</label>

    <p class="page-description">
        Select your smartlink and add trackers if needed. This tool will allow you to easily add a pop-under on your website.
    </p>
    <table>
        <thead>
        <tr>
            <th>
                <label for="crak_pop_vertical" title="Type of the content to be displayed in the popup ad">Vertical</label>
            </th>
            <th>
                <label for="crak_pop_source" title="Tracking data">Source</label>
            </th>
            <th>
                <label for="crak_pop_affsub" title="Tracking data">Aff Sub ID</label>
            </th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <label for="crak_pop_vertical" class="th" title="Type of the content to be displayed in the popup ad">Vertical</label>
                    <select id="crak_pop_vertical" name="crak_pop_vertical" title="Type of the content to be displayed in the popup ad">
                        <?php
                        foreach($crakrevenue_links as $link_id => $link) {
                            ?>
                            <option value="<?php echo esc_attr($link_id) ?>" <?php echo esc_attr($crak_user_option['pop']['vertical'] == $link_id ? 'selected' : '') ?>  <?php echo !$link['enabled'] ? 'disabled' : '' ?>><?php echo esc_html($link['name']) ?><?php echo !$link['enabled'] ? ' (coming soon)' : '' ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <label for="crak_pop_source" class="th" title="Tracking data">Source</label>
                    <input id="crak_pop_source" name="crak_pop_source" title="Tracking data" type="text" value="<?php echo esc_attr(isset($crak_user_option['pop']['source']) && $crak_user_option['pop']['source'] ? $crak_user_option['pop']['source'] : ''); ?>" />
                </td>
                <td>
                    <label for="crak_pop_affsub" class="th" title="Tracking data">Aff Sub ID</label>
                    <input id="crak_pop_affsub" name="crak_pop_affsub" title="Tracking data" type="text" value="<?php echo esc_attr(isset($crak_user_option['pop']['affsub']) && $crak_user_option['pop']['affsub'] ? $crak_user_option['pop']['affsub'] : ''); ?>" />
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Save Changes" class="button button-primary button-large">
</form>
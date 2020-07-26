<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<form method="POST" id="crak_native_settings">
    <?php wp_nonce_field('native'); ?>
    <input type="hidden" name="crak_page" value="native">
    <input type="checkbox" name="crak_native_enabled" id="crak_native_enabled" class="enabler" <?php echo $crak_user_option['native']['enabled'] ? 'checked' : ''; ?>>
    <label for="crak_native_enabled" title="Enables native ads on your site">Enabled</label>

    <p class="page-description">
        You can choose to automatically insert Native Ads at the bottom of your articles. You can also set the
        following parameters with our plugin: positioning, image content, tags, visual appearance. This way, you can
        truly customize the plugin to fit with your design!
    </p>

    <table>
        <thead>
            <tr>
                <th>
                    <label for="crak_native_source" title="Tracking data">Source</label>
                </th>
                <th>
                    <label for="crak_native_affsub" title="Tracking data">Aff Sub ID</label>
                </th>
                <th>
                    <label for="crak_native_nude" title="Whether or not to show nudes">Nude</label>
                </th>
                <th>
                    <label for="crak_native_orientation" title="Whether to show male homosexual content or not">Orientation</label>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <label for="crak_native_source" class="th" title="Tracking data">Source</label>
                    <input id="crak_native_source" name="crak_native_source" type="text" value="<?php echo esc_attr($crak_user_option['native']['source']); ?>" />
                </td>
                <td>
                    <label for="crak_native_affsub" class="th" title="Tracking data">Aff Sub ID</label>
                    <input id="crak_native_affsub" name="crak_native_affsub" type="text" value="<?php echo esc_attr($crak_user_option['native']['affsub']); ?>" />
                </td>
                <td>
                    <label for="crak_native_nude" class="crak_required th" title="Whether or not to show nudes">Nude</label>
                    <select id="crak_native_nude" name="crak_native_nude">
                        <?php
                        foreach($crak_native_nude as $state) {
                            ?>
                            <option value="<?php echo esc_attr($state) ?>" <?php echo $crak_user_option['native']['nude'] == $state ? 'selected' : '' ?> <?php echo $state != 'non-nude' ? 'class="adult"' : '' ?>><?php echo esc_html($state) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <label for="crak_native_orientation" class="crak_required th" title="Whether to show male homosexual content or not">Orientation</label>
                    <select id="crak_native_orientation" name="crak_native_orientation">
                        <?php
                        foreach($crak_native_orientation as $orientation) {
                            ?>
                            <option value="<?php echo esc_attr($orientation) ?>" <?php echo $crak_user_option['native']['orientation'] == $orientation ? 'selected' : '' ?>><?php echo esc_html($orientation) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <h2>Visual Settings</h2>
    <table id="font_settings">
        <thead>
            <tr>
                <th>
                    <label for="crak_native_font" class="crak_required" title="Font of the description of native ads">Font</label>
                </th>
                <th>
                    <label for="crak_native_font_size" title="Font size of the description of native ads">Font Size</label>
                </th>
                <th>
                    <label for="crak_native_font_weight" title="Font weight of the description of native ads">Font Weight</label>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <label for="crak_native_font" class="crak_required th" title="Font of the description of native ads">Font</label>
                    <input id="crak_native_font" name="crak_native_font" type="text" value="<?php echo esc_attr($crak_user_option['native']['font']); ?>" title="Font of the description of native ads">
                </td>
                <td>
                    <label for="crak_native_font_size" class="th" title="Font size of the description of native ads">Font Size</label>
                    <select id="crak_native_font_size" name="crak_native_font_size" title="Font size of the description of native ads">
                        <?php
                        for($i = 6; $i<= 20; $i++) {
                            ?>
                            <option value="<?php echo esc_attr($i) ?>" <?php echo $crak_user_option['native']['font_size'] == $i ? 'selected' : '' ?>><?php echo esc_html($i) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <label for="crak_native_font_weight" class="th" title="Font weight of the description of native ads">Font Weight</label>
                    <select id="crak_native_font_weight" name="crak_native_font_weight" title="Font weight of the description of native ads">
                        <option value="normal" <?php echo $crak_user_option['native']['font_weight'] == 'normal' ? 'selected' : '' ?>>normal</option>
                        <option value="bold" <?php echo $crak_user_option['native']['font_weight'] == 'bold' ? 'selected' : '' ?>>bold</option>
                        <?php
                        for($i = 1; $i<= 9; $i++) {
                            ?>
                            <option value="<?php echo $i * 100 ?>" <?php echo $crak_user_option['native']['font_weight'] == $i * 100 ? 'selected' : '' ?>><?php echo $i * 100 ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th>
                    <label for="crak_native_color" title="Color of the description of native ads. Must be expressed as 6 hexadecimal characters.">Color</label>
                </th>
                <th>
                    <label for="crak_native_color_highlight" title="Color of the description of native ads when you hover over it">Hover Color</label>
                </th>
                <th>
                    <label for="crak_native_lines" title="How many description lines should be visible under the thumbnails">Description Lines</label>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <label for="crak_native_color" class="th" title="Color of the description of native ads. Must be expressed as 6 hexadecimal characters.">Color</label>
                    <input id="crak_native_color" name="crak_native_color" type="text" value="<?php echo esc_attr($crak_user_option['native']['color']); ?>" maxlength="6" title="Color of the description of native ads. Must be expressed as 6 hexadecimal characters.">
                </td>
                <td>
                    <label for="crak_native_color_highlight" class="th" title="Color of the description of native ads when you hover over it">Hover Color</label>
                    <input id="crak_native_color_highlight" name="crak_native_color_highlight" type="text" value="<?php echo esc_attr($crak_user_option['native']['hover_color']); ?>" maxlength="6" title="Color of the description of native ads when you hover over it">
                </td>
                <td>
                    <label for="crak_native_lines" class="th" title="How many description lines should be visible under the thumbnails">Description Lines</label>
                    <select id="crak_native_lines" name="crak_native_lines" title="How many description lines should be visible under the thumbnails">
                        <?php
                        for($i = 0; $i<= 5; $i++) {
                            ?>
                            <option value="<?php echo esc_attr($i) ?>" <?php echo $crak_user_option['native']['number_lines'] == $i ? 'selected' : '' ?>><?php echo esc_html($i) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th>
                    <label for="crak_native_number_thumbnails" title="Number of ads that will be loaded">Number of Thumbnails</label>
                </th>
                <th>
                    <label for="crak_native_width" class="crak_required" title="The maximum width of the block that contains all the native ads">Max Width</label>
                </th>
                <th>
                    <label for="crak_native_thumbs_per_row" title="Adjusts the width of thumbnails to fit a specific number of them on each row">Thumbs per Row</label>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <label for="crak_native_number_thumbnails" class="th" title="Number of ads that will be loaded">Number of Thumbnails</label>
                    <select id="crak_native_number_thumbnails" name="crak_native_number_thumbnails" title="Number of ads that will be loaded">
                        <?php
                        for($i = 0; $i<= 50; $i++) {
                            ?>
                            <option value="<?php echo esc_attr($i) ?>" <?php echo $crak_user_option['native']['number_thumbnails'] == $i ? 'selected' : '' ?>><?php echo esc_html($i) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <label for="crak_native_width" class="crak_required th" title="The maximum width of the block that contains all the native ads">Max Width</label>
                    <input id="crak_native_width" name="crak_native_width" type="text" value="<?php echo esc_attr($crak_user_option['native']['width']); ?>" title="The maximum width of the block that contains all the native ads">
                </td>
                <td>
                    <label for="crak_native_thumbs_per_row" class="th" title="Adjusts the width of thumbnails to fit a specific number of them on each row">Thumbs per Row</label>
                    <select id="crak_native_thumbs_per_row" name="crak_native_thumbs_per_row" title="Adjusts the width of thumbnails to fit a specific number of them on each row">
                        <?php
                        for($i = 1; $i<= 10; $i++) {
                            ?>
                            <option value="<?php echo esc_attr($i) ?>" <?php echo $crak_user_option['native']['thumbs_per_row'] == $i ? 'selected' : '' ?>><?php echo esc_html($i) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="checkbox_parent">
        <input id="crak_native_show_thumbs" name="crak_native_show_thumbs" type="checkbox" <?php echo $crak_user_option['native']['show_thumbs'] == 1 ? 'checked' : ''; ?>>
        <label for="crak_native_show_thumbs" title="Displays the thumbnails on the native ads">Show Thumbnails</label>
    </div>
    <h2>Tags</h2>
    <div id="crak_native_tags">
        <?php
        foreach($crak_native_tags as $tag) {
            ?>
            <span class="tag-label">
                <input class="checkbox" name="crak_native_tags[]" id="crak_native_tags_<?php echo esc_attr(str_replace(' ', '-', $tag)) ?>" type="checkbox" value="<?php echo esc_attr($tag) ?>" <?php echo in_array($tag, $crak_user_option['native']['tags']) ? 'checked' : '' ?>>
                <label for="crak_native_tags_<?php echo esc_attr(str_replace(' ', '-', $tag)) ?>">
                    <?php echo esc_html($tag) ?>
                </label>
            </span>
            <?php
        }
        ?>
    </div>

    <input type="submit" value="Save Changes" class="button button-primary button-large">
</form>

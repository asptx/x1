<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<form method="POST" id="crak_intext_settings">
    <?php wp_nonce_field('intext'); ?>
    <input type="hidden" name="crak_page" value="intext">
    <input type="checkbox" name="crak_intext_enabled" id="crak_intext_enabled" class="enabler" <?php echo $crak_user_option['intext']['enabled'] ? 'checked' : ''; ?>>
    <label for="crak_intext_enabled" title="Enables in-text ads on your site">Enabled</label>

    <p class="page-description">
        Below, choose the words you wish to replace with your personalized Smartlinks on your website. If you
        choose to associate more than one word to a specific Vertical, please make sure to separate each value with a
        comma. Pop will open the in-text ad in another tab. But beware that it makes it more likely that the link will
        be blocked by adblocking softwares.
    </p>
    <table>
        <thead>
            <tr>
                <th title="Type of the content the word will link to">
                    Vertical
                </th>
                <th class="crak_required" title="Words to replace with a link, separated by commas">
                    Words to replace
                </th>
                <th title="Tracking data">
                    Source
                </th>
                <th title="Tracking data">
                    Aff Sub ID
                </th>
                <th title="Maximum number of occurrences" class="tiny-col">
                    Number
                </th>
                <th title="Open links in a new tab" class="tiny-col">
                    Pop
                </th>
                <th class="tiny-col"></th>
            </tr>
        </thead>
        <tbody id="intext">
            <?php
            $k = 0;

            foreach($crak_user_option['intext']['words'] as $key => $value) {
                $k = $key;
                ?>
                <tr id="link_<?php echo esc_attr($k) ?>">
                    <td>
                        <label for="crak_intext_vertical_<?php echo esc_attr($key) ?>" class="th" title="Type of the content the word will link to">
                            Vertical
                        </label>
                        <select id="crak_intext_vertical_<?php echo esc_attr($key) ?>" name="crak_intext_vertical[<?php echo esc_attr($key) ?>]" title="Type of the content the word will link to">
                            <?php
                            foreach($crakrevenue_links as $link_id => $link) {
                                ?>
                                <option value="<?php echo esc_attr($link_id) ?>" <?php echo $crak_user_option['intext']['vertical'][$key] == $link_id ? 'selected' : '' ?>><?php echo $link['name'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <label for="crak_intext_words_<?php echo esc_attr($key) ?>" class="th" title="Words to replace with a link, separated by commas">
                            Words to replace
                        </label>
                        <input id="crak_intext_words_<?php echo esc_attr($key) ?>" type="text" name="crak_intext_words[<?php echo esc_attr($key) ?>]" value="<?php echo esc_attr($crak_user_option['intext']['words'][$key]); ?>" title="Words to replace with a link">
                    </td>
                    <td>
                        <label for="crak_intext_source_<?php echo esc_attr($key) ?>" class="th" title="Tracking data">
                            Source
                        </label>
                        <input id="crak_intext_source_<?php echo esc_attr($key) ?>" name="crak_intext_source[<?php echo esc_attr($key) ?>]" type="text" value="<?php echo esc_attr($crak_user_option['intext']['source'][$key]); ?>" title="Tracking data">
                    </td>
                    <td>
                        <label for="crak_intext_affsub_<?php echo esc_attr($key) ?>" class="th" title="Tracking data">
                            Aff Sub ID
                        </label>
                        <input id="crak_intext_affsub_<?php echo esc_attr($key) ?>" name="crak_intext_affsub[<?php echo esc_attr($key) ?>]" type="text" value="<?php echo esc_attr($crak_user_option['intext']['affsub'][$key]); ?>" title="Tracking data">
                    </td>
                    <td class="tiny-col">
                        <label for="crak_intext_number_<?php echo esc_attr($key) ?>" class="th" title="Maximum number of occurrences">
                            Amount
                        </label>
                        <input id="crak_intext_number_<?php echo esc_attr($key) ?>" name="crak_intext_number[<?php echo esc_attr($key) ?>]" type="text" value="<?php echo esc_attr($crak_user_option['intext']['number'][$key]); ?>" title="Maximum number of occurrences for each word">
                    </td>
                    <td class="tiny-col">
                        <label for="crak_intext_target_<?php echo esc_attr($key) ?>" class="th" title="Open links in a new tab">
                            Pop
                        </label>
                        <input class="checkbox" id="crak_intext_target_<?php echo esc_attr($key) ?>" value="<?php echo esc_attr($key) ?>" name="crak_intext_target[<?php echo esc_attr($key) ?>]" type="checkbox" <?php echo isset($crak_user_option['intext']['target'][$key]) ? 'checked' : ''; ?>>
                        <label for="crak_intext_target_<?php echo esc_attr($key) ?>" title="Open links in a new tab"></label>
                    </td>
                    <td class="tiny-col">
                        <span id="remove_<?php echo $k ?>" class="remove">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
                                <path fill="#E21B1B" fill-rule="evenodd" d="M5.25 11.55h10.5v-2.1H5.25v2.1zM10.5 0C4.725 0 0 4.725 0 10.5S4.725 21 10.5 21 21 16.275 21 10.5 16.275 0 10.5 0z"/>
                            </svg>
                        </span>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr id="new_line">
                <td>
                    <select id="crak_intext_vertical_new" title="Type of the content the word will link to">
                        <?php
                        foreach($crakrevenue_links as $link_id => $link) {
                            ?>
                            <option value="<?php echo esc_attr($link_id) ?>"><?php echo esc_html($link['name']) ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input id="crak_intext_words_new" type="text" title="Words to replace with a link">
                </td>
                <td>
                    <input id="crak_intext_source_new" type="text" title="Tracking data">
                </td>
                <td>
                    <input id="crak_intext_affsub_new" type="text" title="Tracking data">
                </td>
                <td class="tiny-col">
                    <input id="crak_intext_number_new" type="text" title="Maximum number of occurrences">
                </td>
                <td class="tiny-col">
                    <input id="crak_intext_target_new" class="checkbox" type="checkbox">
                    <label for="crak_intext_target_new"  title="Open links in a new tab"></label>
                </td>
                <td class="tiny-col">
                    <span id="addLink">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
                            <path fill="#43B05C" fill-rule="evenodd" d="M15.75 11.55h-4.2v4.2h-2.1v-4.2h-4.2v-2.1h4.2v-4.2h2.1v4.2h4.2v2.1zM10.5 0C4.725 0 0 4.725 0 10.5 0 16.276 4.725 21 10.5 21 16.274 21 21 16.276 21 10.5 21 4.725 16.274 0 10.5 0z"/>
                        </svg>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Save Changes" class="button button-primary button-large">
</form>
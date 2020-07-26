<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $crak_banner_keys;
if (!isset($crak_banner_keys)) {
    $crak_banner_keys = 0;
}
// Creating the widget

class crak_banners_widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'banners_widget',// Base ID of your widget
            __('CrakRevenue Banners', 'wpb_widget_domain'),// Widget name will appear in UI
            array('description' => __( 'Adds a banner on your site', 'wpb_widget_domain' ))// Widget description
        );

        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('crak_frame', plugin_dir_url( __FILE__ ) . 'crak_banners.js', array(), false, true);
        });
    }

    // Creating widget front-end
    public function widget($args, $instance) {
        global $crak_banners;
        global $crak_banner_keys;
        global $crak_banner_sizes;
        global $crak_banners_url;

        $crak_user_option = crak_get_settings();

        if (!isset($instance['size']) || $instance['size'] == -1) {
            if (wp_is_mobile()) {
                $instance['size'] = 1;
            } else {
                $instance['size'] = 0;
            }
        }

        if (!isset($crak_banner_sizes[$instance['size']]['enabled']) || !$crak_banner_sizes[$instance['size']]['enabled']) {
            $instance['size'] = 0;
        }

        $this_size = isset($crak_banner_sizes[$instance['size']]) ? $crak_banner_sizes[$instance['size']] : 0;

        $verticalID = isset($instance['vertical']) ? $instance['vertical'] : 0;

        if (
            !(isset($crak_banners[$verticalID]) &&
                $crak_banners[$verticalID]['enabled']
            ) ||
            !$crak_banners[$verticalID]['zones'][$instance['size']]['enabled']
        ) {
            $verticalID = 0;
        }

        $vertical = $crak_banners[$verticalID]['zones'][$instance['size']];

        $instance['source'] = crak_tracker_cleanup($instance['source']);
        $instance['affsub'] = crak_tracker_cleanup($instance['affsub']);

        $this_url = strtr($crak_banners_url, array(
            '{zone_id}' => esc_attr($vertical['zone_id'], 'text_domain'),
            '{bucket_id}' => esc_attr($vertical['bucket_id'], 'text_domain'),
            '{aff_id}' => esc_attr($crak_user_option['aff_id'], 'text_domain'),
            '{offer_id}' => esc_attr($vertical['offer_id'], 'text_domain'),
            '{file_id}' => esc_attr($vertical['file_id'], 'text_domain'),
            '{affsub}' => esc_attr($instance['affsub'], 'text_domain'),
            '{source}' => esc_attr($instance['source'], 'text_domain'),
        ));

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }

        echo __('<iframe class="crak_frame" data-key="' . $crak_banner_keys . '" id="crak_frame_' . $crak_banner_keys . '" src="' . $this_url . '" width="' . $this_size['width'] . '" height="' . $this_size['height'] . '"  scrolling="no" frameborder="0"></iframe><span id="crak_width_detector_' . $crak_banner_keys . '">');

        echo $args['after_widget'];

        $crak_banner_keys++;
    }

    // Widget Backend
    public function form($instance) {
        global $crak_banners;
        global $crak_banner_sizes;

        // Set widget defaults
        $defaults = array(
            'title' => '',
            'vertical' => 0,
            'source' => '',
            'affsub' => '',
            'size' => -1,
        );

        // Parse current settings with defaults
        extract(wp_parse_args((array) $instance, $defaults));
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('vertical')); ?>"><?php _e('Vertical:', 'text_domain'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('vertical')); ?>" name="<?php echo esc_attr($this->get_field_name('vertical')); ?>" title="Type content in the widget">
                <?php
                foreach($crak_banners as $vertical_id => $banner) {
                    ?>
                    <option value="<?php echo $vertical_id ?>" <?php echo $vertical_id == $vertical ? 'selected' : '' ?> <?php echo !$banner['enabled'] ? 'disabled' : '' ?>><?php echo $banner['name'] ?><?php echo !$banner['enabled'] ? ' (coming soon)' : '' ?></option>
                    <?php

                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('size')); ?>"><?php _e('Size:', 'text_domain'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('size')); ?>" name="<?php echo esc_attr($this->get_field_name('size')); ?>" title="Chose the size of the banner">
                <?php
                foreach($crak_banner_sizes as $key => $values) {
                    if ($key == '-1') {
                        ?>
                        <option value="-1" <?php echo -1 == $size ? 'selected' : '' ?> <?php echo $values['enabled'] ? '' : 'disabled' ?>>Auto-select - mobile 305x99, desktop 300x250<?php echo $values['enabled'] ? '' : ' (coming soon)' ?></option>
                        <?php
                    } else {
                        ?>
                        <option
                            value="<?php echo $key ?>" <?php echo $key == $size ? 'selected' : '' ?> <?php echo $values['enabled'] ? '' : 'disabled' ?>><?php echo $values['width'] . 'x' . $values['height'] ?><?php echo $values['enabled'] ? '' : ' (coming soon)' ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('source')); ?>"><?php _e('Source:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('source')); ?>" name="<?php echo esc_attr($this->get_field_name('source')); ?>" type="text" value="<?php echo esc_attr($source); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('affsub')); ?>"><?php _e('Aff Sub ID', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('affsub')); ?>" name="<?php echo esc_attr($this->get_field_name('affsub')); ?>" type="text" value="<?php echo esc_attr($affsub); ?>" />
        </p>
        <?php
    }

    // Update widget settings
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        $instance['vertical'] = isset($new_instance['vertical']) ? wp_strip_all_tags($new_instance['vertical']) : '';
        $instance['source'] = isset($new_instance['source']) ? crak_tracker_cleanup(wp_strip_all_tags($new_instance['source'])) : '';
        $instance['affsub'] = isset($new_instance['affsub']) ? crak_tracker_cleanup(wp_strip_all_tags($new_instance['affsub'])) : '';
        $instance['size'] = isset($new_instance['size']) ? wp_strip_all_tags($new_instance['size']) : '';
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function crak_banners_widget() {
    register_widget('crak_banners_widget');
}

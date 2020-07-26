<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $camsInstances;

if ($camsInstances > -1) {
    $camsInstances = 0;
}

// Creating the widget
class crak_cams_widget extends WP_Widget {
    private $words = array('anal', 'bdsm', 'creampie', 'cuckold', 'deepthroat', 'dominant', 'facials', 'feet', 'femdom', 'fishnets', 'gagging', 'lactation', 'leather', 'pregnant', 'roleplay', 'rubberlatex', 'shaving', 'smoking', 'spankingpaddling', 'stockingsnylons', 'submissive', 'underwear', 'whips', 'arab', 'asian', 'ebony', 'indian', 'latina', 'white', 'nativeamerican', 'pacificislander', 'mediterranean', 'roma', 'babe', 'couple', 'lesbian', 'shemale', 'tranny', 'girls', 'group', 'bigbutts', 'bbw', 'chubby', 'skinny', 'petite', 'athletic', 'slender', 'slim', 'muscular', 'blonde', 'brunette', 'whitehaired', 'grayhaired', 'silverhaired', 'saltnpepperhaired', 'redhead', 'dyed', 'granny', 'mature', 'milf', 'teen', 'ladyboy', 'emo', 'goth', 'piercing', 'tattoo', 'dp', 'french', 'german', 'spanish', 'italian', 'english', 'hairy', 'pornstar', 'voyeur', 'wife', 'nonnude', 'bondage', 'college', 'bisex', 'lesbians', 'blueeyed', 'greeneyed', 'hazeleyed', 'browneyed', 'greyeyed', 'smalltits', 'bigtits', 'bigdick', 'smalldick', 'babes', 'chick', 'chicks', 'chix', 'bigbeautifulwomen', 'fat', 'bigbutt', 'bigass', 'bigasses', 'booty', 'breasts', 'bigtits', 'bigboobs', 'bigbreasts', 'busty', 'boobs', 'bikini', 'lingerie', 'pantyhose', 'stockings', 'panties', 'fishnets', 'bisexual', 'blondes', 'brunettes', 'curvaceous', 'doublepenetration', 'black', 'gothic', 'punk', 'threesome', 'groupsex', 'orgy', 'gangbang', 'swingers', 'old', 'toy', 'dildo', 'dildos', 'latin', 'latinas', 'hispanic', 'spanish', 'lesbian', 'dykes', 'dyke', 'piercings', 'preggo', 'prego', 'pregnancy', 'tranny', 'petit', 'petite', 'slim', 'smallboobs', 'smallbreasts', 'tatoo', 'tattoos', 'inked', 'ink', 'housewives', 'housewife', 'cheating', 'wives', 'cuckold', 'girlfriend', 'teens', 'young', 'niceteen', 'pornstars', 'virgin', '18', 'punishtube', 'rough', 'roughsex', 'hardcore', 'brutal', 'mom', 'mother', 'stepmom', 'momandson', 'czech', 'russian', 'miakhalifa', 'japanese', 'thai', 'firstanal', 'amateuranal', 'foot', 'footjob', 'feetjob', 'slave', 'hairypussy', 'gypsy', 'blueeye', 'blueeyes', 'greeneye', 'greeneyes', 'hazeleye', 'hazeleyes', 'browneye', 'browneyes', 'greyeye', 'greyeyes', 'grayeye', 'grayeyes', 'grayeyed', 'latex', 'rubber', 'spanking', 'dyedhair', 'redheads', 'redhair', 'redhaired', 'whitehair', 'saltnpepperhair', 'saltnpepper', 'silverhair', 'grayhair', 'greyhair', 'greyhaired', 'girl', 'female', 'woman', 'women', 'insertion', 'objects');
    private $categories = array('girl');

    function __construct() {
        parent::__construct(
            'cams_widget',// Base ID of your widget
            __('CrakRevenue Cams', 'wpb_widget_domain'),// Widget name will appear in UI
            array('description' => __('Adds a number of cam performers on your site', 'wpb_widget_domain'))// Widget description
        );

        wp_enqueue_script( 'crak_cams', plugin_dir_url(__FILE__) . 'crak_cams.js', array(), false, true );
        wp_enqueue_style( 'crak_cams', plugin_dir_url(__FILE__) . 'crak_cams_iframe.css');
    }

    // Creating widget front-end
    public function widget($args, $instance) {
        global $camsInstances;
        global $cams_url;
        global $post;

        if ($instance['gay']) {
            $this->categories = array('gay', 'male');
            $this->words = array('anal', 'bdsm', 'creampie', 'cuckold', 'deepthroat', 'dominant', 'facials', 'feet', 'femdom', 'fishnets', 'gagging', 'lactation', 'leather', 'roleplay', 'rubberlatex', 'shaving', 'smoking', 'spankingpaddling', 'stockingsnylons', 'submissive', 'underwear', 'whips', 'arab', 'asian', 'ebony', 'indian', 'latina', 'white', 'nativeamerican', 'pacificislander', 'mediterranean', 'roma', 'couple', 'shemale', 'tranny', 'guys', 'group', 'bigbutts', 'bbw', 'chubby', 'skinny', 'petite', 'littleguy', 'athletic', 'slender', 'slim', 'muscular', 'blonde', 'brunette', 'whitehaired', 'grayhaired', 'silverhaired', 'saltnpepperhaired', 'redhead', 'dyed', 'granny', 'mature', 'milf', 'teen', 'ladyboy', 'emo', 'goth', 'piercing', 'tattoo', 'dp', 'french', 'german', 'spanish', 'italian', 'english', 'hairy', 'pornstar', 'voyeur', 'nonnude', 'bears', 'bondage', 'twink', 'daddy', 'guynextdoor', 'college', 'bisex', 'gay', 'blueeyed', 'greeneyed', 'hazeleyed', 'browneyed', 'greyeyed', 'circumcised', 'furrybodyhair', 'smoothbodyhair', 'moderatebodyhair', 'hairybodyhair', 'smalltits', 'bigtits', 'bigdick', 'smalldick', 'babes', 'chick', 'chicks', 'chix', 'bigbeautifulwomen', 'fat', 'bigbutt', 'bigass', 'bigasses', 'booty', 'breasts', 'bigtits', 'bigboobs', 'bigbreasts', 'busty', 'boobs', 'bikini', 'lingerie', 'pantyhose', 'stockings', 'panties', 'fishnets', 'bigdicks', 'bigcock', 'largecock', 'largedick', 'bisexual', 'blondes', 'brunettes', 'curvaceous', 'doublepenetration', 'black', 'gothic', 'punk', 'threesome', 'groupsex', 'orgy', 'gangbang', 'swingers', 'old', 'toy', 'dildo', 'dildos', 'latin', 'latinas', 'hispanic', 'spanish', 'piercings', 'petit', 'petite', 'slim', 'smallboobs', 'smallbreasts', 'tatoo', 'tattoos', 'inked', 'ink', 'cheating', 'cuckold', 'teens', 'young', 'niceteen', 'pornstars', 'virgin', '18', 'punishtube', 'rough', 'roughsex', 'hardcore', 'brutal', 'stepmom', 'momandson', 'czech', 'russian', 'miakhalifa', 'japanese', 'thai', 'firstanal', 'amateuranal', 'foot', 'footjob', 'feetjob', 'slave', 'hairypussy', 'old-man', 'gypsy', 'dad', 'father', 'blueeye', 'blueeyes', 'greeneye', 'greeneyes', 'hazeleye', 'hazeleyes', 'browneye', 'browneyes', 'greyeye', 'greyeyes', 'grayeye', 'grayeyes', 'grayeyed', 'latex', 'rubber', 'spanking', 'dyedhair', 'redheads', 'redhair', 'redhaired', 'whitehair', 'saltnpepperhair', 'saltnpepper', 'silverhair', 'grayhair', 'greyhair', 'greyhaired', 'boy', 'insertion', 'objects');
        }

        $camsInstances++;

        $crak_user_option = crak_get_settings();

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }

        $content = '';

        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $content .= ' ' . strip_tags($post->post_content);
            }
        }

        $content_array = explode(' ', str_replace(array(',', '.', '"', '	', "'", "\n", "\r"), '', strtolower($content)));

        if (count($this->words)) {
            foreach ($this->words as $word) {
                if (in_array($word, $content_array)) {
                    $this->categories[] = $word;
                }
            }
        }

        echo __('<script style="position: absolute; top: 0;" src="//widget.camshq.info?type=script&categories=' . implode(',', $this->categories) .
                '&number=' . intval($instance['cols'] * $instance['rows']) .
                '&cols=' . intval($instance['cols']) .
                '&rows=' . intval($instance['rows']) .
                '&source=' . $instance['source'] .
                '&aff_sub=' . $instance['affsub'] .
                '&aff_id=' . intval($crak_user_option['aff_id']) .
                '&animateFeed=1' .
                '&useFeed=0' .
                '&showProvider=0' .
                '&colorFilter=0' .
                '&smoothAnimation=0' .
                '&showGender=0' .
                '&animationSpeed=200' .
                '&token=f9a4f390-5227-11e9-97f2-f57f6dfb8f0a' . //Homing Pidgin Token
                ($instance['gay'] ? '&male=1' : '') .
                '"></script>',
            'wpb_widget_domain'
        );

        echo $args['after_widget'];
    }

    // Widget Backend
    public function form($instance) {
        // Set widget defaults
        $defaults = array(
            'title' => '',
            'source' => '',
            'affsub' => '',
            'rows' => '',
            'cols' => '',
            'gay' => false,
        );

        // Parse current settings with defaults
        extract(wp_parse_args((array) $instance, $defaults)); ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p style="width: 49%; float: left; margin-right: 2%">
            <label for="<?php echo esc_attr($this->get_field_id('source')); ?>"><?php _e('Source:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('source')); ?>" name="<?php echo esc_attr($this->get_field_name('source')); ?>" type="text" value="<?php echo esc_attr($source); ?>">
        </p>
        <p style="width: 49%; float: left;">
            <label for="<?php echo esc_attr($this->get_field_id('affsub')); ?>"><?php _e('Aff Sub ID', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('affsub')); ?>" name="<?php echo esc_attr($this->get_field_name('affsub')); ?>" type="text" value="<?php echo esc_attr($affsub); ?>" />
        </p>
        <p style="width: 49%; float: left; margin-right: 2%">
            <label for="<?php echo esc_attr($this->get_field_id('cols')); ?>"><?php _e('Number of Columns', 'number_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('cols')); ?>" name="<?php echo esc_attr($this->get_field_name('cols')); ?>" type="text" value="<?php echo esc_attr($cols); ?>" />
        </p>
        <p style="width: 49%; float: left;">
            <label for="<?php echo esc_attr($this->get_field_id('rows')); ?>"><?php _e('Number of Rows', 'number_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('rows')); ?>" name="<?php echo esc_attr($this->get_field_name('rows')); ?>" type="text" value="<?php echo esc_attr($rows); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('gay')); ?>"><?php _e('Allow male models', 'number_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('gay')); ?>" name="<?php echo esc_attr($this->get_field_name('gay')); ?>" type="checkbox" value="true" <?php echo $gay ? 'checked' : ''; ?> />
        </p>
        <?php
    }

    // Update widget settings
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        $instance['source'] = isset($new_instance['source']) ? crak_tracker_cleanup(wp_strip_all_tags($new_instance['source'])) : '';
        $instance['affsub'] = isset($new_instance['affsub']) ? crak_tracker_cleanup(wp_strip_all_tags($new_instance['affsub'])) : '';
        $instance['rows'] = isset($new_instance['rows']) ? intval($new_instance['rows']) : '';
        $instance['cols'] = isset($new_instance['cols']) ? intval($new_instance['cols']) : '';
        $instance['gay'] = isset($new_instance['gay']) ? !!$new_instance['gay'] : false;
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function crak_cams_widget() {
    register_widget('crak_cams_widget');
}
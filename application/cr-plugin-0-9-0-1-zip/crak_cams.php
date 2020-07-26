<?php
/**
 * Created by PhpStorm.
 * User: lpainchaud
 * Date: 4/20/2018
 * Time: 11:26 AM
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

class crak_cams
{
    private $APIURL = 'http://gateway.slfsmf.com:8000/slp/1.0';
    private $words = array('anal', 'bdsm', 'creampie', 'cuckold', 'deepthroat', 'dominant', 'facials', 'feet', 'femdom', 'fishnets', 'gagging', 'lactation', 'leather', 'pregnant', 'roleplay', 'rubberlatex', 'shaving', 'smoking', 'spankingpaddling', 'stockingsnylons', 'submissive', 'underwear', 'whips', 'arab', 'asian', 'ebony', 'indian', 'latina', 'white', 'nativeamerican', 'pacificislander', 'mediterranean', 'roma', 'babe', 'couple', 'lesbian', 'shemale', 'tranny', 'girls', 'group', 'bigbutts', 'bbw', 'chubby', 'skinny', 'petite', 'athletic', 'slender', 'slim', 'muscular', 'blonde', 'brunette', 'whitehaired', 'grayhaired', 'silverhaired', 'saltnpepperhaired', 'redhead', 'dyed', 'granny', 'mature', 'milf', 'teen', 'ladyboy', 'emo', 'goth', 'piercing', 'tattoo', 'dp', 'french', 'german', 'spanish', 'italian', 'english', 'hairy', 'pornstar', 'voyeur', 'wife', 'nonnude', 'bondage', 'college', 'bisex', 'lesbians', 'blueeyed', 'greeneyed', 'hazeleyed', 'browneyed', 'greyeyed', 'smalltits', 'bigtits', 'bigdick', 'smalldick', 'babes', 'chick', 'chicks', 'chix', 'bigbeautifulwomen', 'fat', 'bigbutt', 'bigass', 'bigasses', 'booty', 'breasts', 'bigtits', 'bigboobs', 'bigbreasts', 'busty', 'boobs', 'bikini', 'lingerie', 'pantyhose', 'stockings', 'panties', 'fishnets', 'bisexual', 'blondes', 'brunettes', 'curvaceous', 'doublepenetration', 'black', 'gothic', 'punk', 'threesome', 'groupsex', 'orgy', 'gangbang', 'swingers', 'old', 'toy', 'dildo', 'dildos', 'latin', 'latinas', 'hispanic', 'spanish', 'lesbian', 'dykes', 'dyke', 'piercings', 'preggo', 'prego', 'pregnancy', 'tranny', 'petit', 'petite', 'slim', 'smallboobs', 'smallbreasts', 'tatoo', 'tattoos', 'inked', 'ink', 'housewives', 'housewife', 'cheating', 'wives', 'cuckold', 'girlfriend', 'teens', 'young', 'niceteen', 'pornstars', 'virgin', '18', 'punishtube', 'rough', 'roughsex', 'hardcore', 'brutal', 'mom', 'mother', 'stepmom', 'momandson', 'czech', 'russian', 'miakhalifa', 'japanese', 'thai', 'firstanal', 'amateuranal', 'foot', 'footjob', 'feetjob', 'slave', 'hairypussy', 'gypsy', 'blueeye', 'blueeyes', 'greeneye', 'greeneyes', 'hazeleye', 'hazeleyes', 'browneye', 'browneyes', 'greyeye', 'greyeyes', 'grayeye', 'grayeyes', 'grayeyed', 'latex', 'rubber', 'spanking', 'dyedhair', 'redheads', 'redhair', 'redhaired', 'whitehair', 'saltnpepperhair', 'saltnpepper', 'silverhair', 'grayhair', 'greyhair', 'greyhaired', 'girl', 'female', 'woman', 'women', 'insertion', 'objects');
    private $categories = array('girl');
    private $contentString = '';
    private $number = 1;
    private $models = array();
    private $cols = 1;
    private $template_file = '';
    private $template = array('tpl' => '');
    private $css_file = '';
    private $modelsKey = '';
    private $aff_id = 1;
    private $affsub = '';
    private $source = '';
    private $url = '';
    private $debug = false;

    function __construct() {
        global $cams_url;

        $this->debug = isset($_GET['debug']);

        if (isset($_GET['cc_male'])) {
            $this->categories = array('gay', 'male');
            $this->words = array('anal', 'bdsm', 'creampie', 'cuckold', 'deepthroat', 'dominant', 'facials', 'feet', 'femdom', 'fishnets', 'gagging', 'lactation', 'leather', 'roleplay', 'rubberlatex', 'shaving', 'smoking', 'spankingpaddling', 'stockingsnylons', 'submissive', 'underwear', 'whips', 'arab', 'asian', 'ebony', 'indian', 'latina', 'white', 'nativeamerican', 'pacificislander', 'mediterranean', 'roma', 'couple', 'shemale', 'tranny', 'guys', 'group', 'bigbutts', 'bbw', 'chubby', 'skinny', 'petite', 'littleguy', 'athletic', 'slender', 'slim', 'muscular', 'blonde', 'brunette', 'whitehaired', 'grayhaired', 'silverhaired', 'saltnpepperhaired', 'redhead', 'dyed', 'granny', 'mature', 'milf', 'teen', 'ladyboy', 'emo', 'goth', 'piercing', 'tattoo', 'dp', 'french', 'german', 'spanish', 'italian', 'english', 'hairy', 'pornstar', 'voyeur', 'nonnude', 'bears', 'bondage', 'twink', 'daddy', 'guynextdoor', 'college', 'bisex', 'gay', 'blueeyed', 'greeneyed', 'hazeleyed', 'browneyed', 'greyeyed', 'circumcised', 'furrybodyhair', 'smoothbodyhair', 'moderatebodyhair', 'hairybodyhair', 'smalltits', 'bigtits', 'bigdick', 'smalldick', 'babes', 'chick', 'chicks', 'chix', 'bigbeautifulwomen', 'fat', 'bigbutt', 'bigass', 'bigasses', 'booty', 'breasts', 'bigtits', 'bigboobs', 'bigbreasts', 'busty', 'boobs', 'bikini', 'lingerie', 'pantyhose', 'stockings', 'panties', 'fishnets', 'bigdicks', 'bigcock', 'largecock', 'largedick', 'bisexual', 'blondes', 'brunettes', 'curvaceous', 'doublepenetration', 'black', 'gothic', 'punk', 'threesome', 'groupsex', 'orgy', 'gangbang', 'swingers', 'old', 'toy', 'dildo', 'dildos', 'latin', 'latinas', 'hispanic', 'spanish', 'piercings', 'petit', 'petite', 'slim', 'smallboobs', 'smallbreasts', 'tatoo', 'tattoos', 'inked', 'ink', 'cheating', 'cuckold', 'teens', 'young', 'niceteen', 'pornstars', 'virgin', '18', 'punishtube', 'rough', 'roughsex', 'hardcore', 'brutal', 'stepmom', 'momandson', 'czech', 'russian', 'miakhalifa', 'japanese', 'thai', 'firstanal', 'amateuranal', 'foot', 'footjob', 'feetjob', 'slave', 'hairypussy', 'old-man', 'gypsy', 'dad', 'father', 'blueeye', 'blueeyes', 'greeneye', 'greeneyes', 'hazeleye', 'hazeleyes', 'browneye', 'browneyes', 'greyeye', 'greyeyes', 'grayeye', 'grayeyes', 'grayeyed', 'latex', 'rubber', 'spanking', 'dyedhair', 'redheads', 'redhair', 'redhaired', 'whitehair', 'saltnpepperhair', 'saltnpepper', 'silverhair', 'grayhair', 'greyhair', 'greyhaired', 'boy', 'insertion', 'objects');
        }

        $this->categories = array_merge($this->categories, (isset($_GET['cc_categories']) ? explode(',', $_GET['cc_categories']) : array()));
        $this->number = isset($_GET['cc_number']) ? intval($_GET['cc_number']) : $this->number;
        $this->cols = isset($_GET['cc_cols']) ? json_decode($_GET['cc_cols'], true) : $this->cols;
        $this->aff_id = isset($_GET['aff_id']) ? $_GET['aff_id'] : 1;
        $this->affsub = isset($_GET['affsub']) ? $_GET['affsub'] : '';
        $this->source = isset($_GET['source']) ? $_GET['source'] : '';
        $this->url = esc_attr(esc_js(strtr($cams_url, [
            '{aff_id}' => $this->aff_id,
            '{affsub}' => $this->affsub,
            '{source}' => $this->source
        ])));

        $all_options = wp_load_alloptions();

        //cache cleanup
        foreach ($all_options as $name => $value) {
            if ($this->debug && stripos($name, 'crak_cams_') !== false) {
                echo $name;
                echo "<br>";
                echo time() - unserialize($value)['date'];
                echo "<br>";
            }

            if (stripos($name, 'crak_cams_models') !== false && (time() - unserialize($value)['date']) >= 60) {
                delete_option($name);
            } else if (stripos($name, 'crak_cams_template') !== false && (time() - unserialize($value)['date']) >=  60 * 60) {
                delete_option($name);
            }
        }

        $this->template_file = plugins_url('/cams_template.tpl', __FILE__ );
        $this->css_file = plugins_url('/modules/crak_cams.css', __FILE__ );
    }

    function getModelsKey () {
        return 'crak_cams_models|' . implode(',', $this->categories) . '|' . $this->number;
    }

    function getModels () {
        $this->models = get_option($this->getModelsKey(), array());

        if (isset($_GET['cc_forcemdl']) || !isset($this->models['date']) || $this->models['date'] < time() - 60) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->APIURL . '/streamate?categories=' . implode(',', $this->categories) . '&minimum=' . $this->number . '&maxexact=' . $this->number);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);

            $this->models = json_decode($output, true);

            $this->models['date'] = time();

            update_option($this->getModelsKey(), $this->models);
        }

        if ($this->models) {
            $this->getTemplate();
        }
    }

    function getTemplate () {
        $this->template = get_option('crak_cams_template|' . $this->template_file, array());

        if (isset($_GET['cc_forcetpl']) || !isset($this->template['date']) || $this->template['date'] < time() - 60 * 60) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->template_file);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $this->template['tpl'] = curl_exec($ch);
            curl_close($ch);
            $this->template['date'] = time();

            update_option('crak_cams_template|' . $this->template_file, $this->template);
        }

        $this->printModels();
    }

    function printModels () {
        ?>
        <link  rel="stylesheet" type="text/css" href="<?php echo$this->css_file ?>">
        <div id="cams-widget" class="cams-widget" data-cols='<?php echo json_encode($this->cols) ?>' >
            <?php
            $find = array('{url}', '$$name$$', '$$id$$', '$$img$$', '$$thumb$$', '$$age$$', '$$status$$', '$$gender$$', '$$relevance$$');

            $nodes = $this->models['SMLResult']['AvailablePerformers']['Performer'];

            if (isset($nodes['@attributes'])) {
                $nodes = array($nodes);
            }

            foreach ($nodes as $model ) {
                $replace = array(
                    $this->url,
                    isset($model['@attributes']['Name']) ? $model['@attributes']['Name'] : '',
                    isset($model['@attributes']['Id']) ? $model['@attributes']['Id'] : '',
                    isset($model['Media']['Pic']['Full']['@attributes']['Src']) ? $model['Media']['Pic']['Full']['@attributes']['Src'] : '',
                    isset($model['Media']['Pic']['Thumb']['@attributes']['Src']) ? $model['Media']['Pic']['Thumb']['@attributes']['Src'] : '',
                    isset($model['@attributes']['Age']) ? $model['@attributes']['Age'] : '',
                    isset($model['@attributes']['StreamType']) ? $model['@attributes']['StreamType'] : '',
                    isset($model['@attributes']['Gender']) ? $model['@attributes']['Gender'] : '',
                    isset($model['@attributes']['Relevance']) ? $model['@attributes']['Relevance'] : ''
                );

                echo str_replace(' | yo', '', str_replace($find, $replace, $this->template['tpl']));
                echo "\r\n";
            }
            ?>
        </div>
        <script>
            var cw = document.getElementById('cams-widget');
            var cw_sizes = JSON.parse(cw.dataset.cols);

            function cc_resize() {
                var cc_col = 1;

                Object.keys(cw_sizes).forEach(function(widthKey){
                    if (parseInt(widthKey) < cw.offsetWidth) {
                        cc_col = cw_sizes[widthKey];
                    }
                });

                cw.className = 'cams-widget cw-ready cw-cols-' + cc_col;
            }

            function cc_hash_size () {
                if (window.location.hash && parseInt(window.location.hash.substr(1))) {
                    cw.className = 'cams-widget cw-ready cw-cols-' + parseInt(window.location.hash.substr(1));
                } else {
                    cc_resize();
                }
            }

            if (typeof cw_sizes == 'object') {
                window.addEventListener('resize', cc_resize);

                cc_resize();
            } else if (typeof cw_sizes == 'number') {
                cw.className = 'cams-widget cw-ready cw-cols-' + cw_sizes;
            }

            window.addEventListener('hashchange', cc_hash_size);

            if (window.location.hash) {
                cc_hash_size();
            }

            setTimeout(function(){
                location.reload();
            }, 60000)
        </script>
        <?php
    }

    function init () {
        if ($this->categories && count($this->categories) && is_string($this->categories)) {
            $this->categories = explode(',', str_replace('%26', '', str_replace('&', '', implode(',', $this->categories))));
        } else if (count($this->words)) {
            foreach ($this->words as $word) {
                if (stripos($this->contentString, $word) !== false) {
                    $this->categories[] = $word;
                }
            };
        }

        if ($this->number > 50) {
            $this->number = 50;
        }

        if ($this->number < 1) {
            $this->number = 1;
        }

        $this->modelsKey = implode(',', $this->categories) . '|' . $this->number;

        $this->getModels();
    }
}

$a = new crak_cams;
$a->init();

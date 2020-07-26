<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="crak_sidebar">
    <div id="crak_version_block">
        <a href="http://www.crakrevenue.com" target="_blank" class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 215.3 215.3"><style>.st0{fill:#1DBF24;}.st1{fill:#008938;}.st2{fill:#FFFFFF;}</style><title>  Fichier 1OffLine</title><path class="st0" d="M51.1 2h113.2c27.1 0 49.1 22 49.1 49.1v113.2c0 27.1-22 49.1-49.1 49.1H51.1C24 213.3 2 191.3 2 164.2V51.1C2 24 24 2 51.1 2z"/><polygon class="st1" points="51 122.8 127.7 199.6 160.6 190.4 170.2 168.7 73.8 127.4 "/><path class="st1" d="M213.3 115.9v48.3c-0.1 27.1-22 49-49.1 49.1h-29.9l-57.6-57.6L169.4 72 213.3 115.9z"/><path class="st2" d="M169.4 72c-1.4 0.1-30.1 5.8-65.6 20.1 -19 7.7-47.2 10.1-43.9-13 3.5-2 24.1 8.6 20.9-10.5 -3.8 2.1-17.4-2-20.8 4.8 -2.5-6.8-17-9.2-21.8-14 0.2 11.9 8.3 17.3 16.5 18.2 -26.4 25-1.5 59.5 28.1 66.8 1.9 1.9-0.8 7.6-6 11.4 14 1.6 24.8-7.8 31-11.4 6.7 3.3 14.1 4.8 21.5 4.6 -2.2-3-3.5-6.5-4-10.2 8.4-2.8 16-5.8 18.6-7.2C168.2 118.5 188.7 101.1 169.4 72"/><path class="st0" d="M131.9 162.9h57.5c2.8 0 5 2.3 5 5v16.4c0 2.8-2.3 5-5 5h-57.5c-2.8 0-5-2.3-5-5v-16.4C126.9 165.2 129.1 162.9 131.9 162.9z"/><path class="st2" d="M143.3 176.3c1.7-0.4 2.8-1.9 2.7-3.6 0-2.1-1.6-4-4.6-4h-8.4v15.9h8.6c3 0 4.6-1.9 4.6-4.3C146.5 178.4 145.2 176.7 143.3 176.3zM136.5 171.5h4.2c0.9-0.1 1.8 0.6 1.9 1.6 0 0.1 0 0.1 0 0.2 0 0.9-0.7 1.7-1.7 1.7 -0.1 0-0.1 0-0.2 0h-4.2V171.5zM140.9 181.6h-4.4v-3.7h4.4c1-0.1 1.9 0.7 2 1.7 0 0 0 0.1 0 0.1C142.9 180.9 142.2 181.6 140.9 181.6L140.9 181.6z"/><polygon class="st2" points="148.9 184.5 160.2 184.5 160.2 181.5 152.3 181.5 152.3 177.9 160 177.9 160 175 152.3 175 152.3 171.6 160.2 171.6 160.2 168.6 148.9 168.6 "/><polygon class="st2" points="174.5 168.6 161.8 168.6 161.8 171.6 166.5 171.6 166.5 184.5 169.9 184.5 169.9 171.6 174.5 171.6 "/><path class="st2" d="M183.8 168.6h-4.2l-6.1 15.9h3.9l1-2.7h6.8l1 2.7h3.9L183.8 168.6zM179.2 178.9l2.5-6.8 2.5 6.8H179.2z"/></svg>
        </a>
        <div id="crak_version">Version <?php echo $this_version ?></div>
    </div>
    <a href="mailto:support@crakrevenue.com" id="crak_support">Contact CrakRevenue Support for any problems.</a>
    <?php
    if ($this_version != $current_version) {
        ?>
        <div id="crak_plugin_update">
            <h4>Plugin Updates</h4>
            <h5>Installed Version</h5>
            <p><?php echo $this_version ?></p>
            <h5>Latest Available Version</h5>
            <p><?php echo $current_version ?></p>
            <a href="<?php echo $current_version_url?>" class="button button-primary button-large">Download Update</a>
        </div>
        <?php
    }
    ?>

    <form action="https://support.crakrevenue.com/hc/en-us/search" method="get" target="_blank" id="search-form">
        <input name="utf8" type="hidden" value="✓">
        <h4>FAQ</h4>
        <p>
            You have a question?<br>
            Search our knowledge base.
        </p>
        <input type="search" name="query" placeholder="Search" id="search-input">
        <input type="submit" name="commit" value="Search" id="search-btn">
    </form>

    <h4>Latest Posts</h4>
    <?php
    if(ini_get('allow_url_fopen')) {
        try {
            $feed = json_decode(file_get_contents('http://crakrevenue.com/wp-json/wp/v2/posts?type=post&orderby=date&better_featured_image'), true);

            foreach ($feed as $k => $v) {
                if ($k < 2) {
                    ?>
                    <div class="crak_rss_post">
                        <h4><?php echo esc_html($v['title']['rendered']) ?></h4>
                        <img src="<?php echo esc_attr($v['better_featured_image']['source_url']) ?>">

                        <p><?php echo esc_html(wp_strip_all_tags($v['excerpt']['rendered'])); ?></p>
                        <a href="<?php echo esc_attr($v['link']) ?>" class="crak_read_more">Read more</a>
                    </div>
                    <?php
                }
            }
        } catch (\Exception $e) {
            echo 'Could\'t fetch the feed. Something went wrong.';
        }
    } else {
        echo 'Can\'t fetch the feed. allow_url_fopen is disabled.';
    }
    ?>
</div>
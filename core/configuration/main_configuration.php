<?php

/**
 * anime display list
 * determine how many anime will be displayed on homepage for latest, new, and upcoming.
 */

add_action('admin_menu', 'kiranime_configuration');
function kiranime_configuration()
{
    add_menu_page('Kira Tools', 'Kira Tools', 'edit_posts', 'kiranime_tools', 'kiranime_grabber_setting', 'dashicons-admin-generic', 80);
    add_submenu_page('kiranime_tools', 'Settings', 'Settings', 'edit_posts', 'kiranime_tools', 'kiranime_grabber_setting');

    if (!get_option('kiranime_image_migrated', false)) {
        add_submenu_page('kiranime_tools', 'Migrate Image', 'Migrate Image', 'manage_options', 'kiranime_tools_migrate_image_posts', 'kiranime_migrate_images');
    }

    if (!get_option('kiranime_search_index_done', false)) {
        add_submenu_page('kiranime_tools', 'Build Search Index', 'Search Index', 'manage_options', 'kiranime_tools_create_search_index', 'kiranime_build_search_index');
    }
    // add_submenu_page('anime_batch_creator', 'Kiranime Theme Activation', 'Activation', 'manage_options', 'kiranime-theme-activation', 'add_activation_theme');

    add_action('admin_init', 'kiranime_register_setting');
}
function kiranime_register_setting()
{
    register_setting('kiranime_settings', '__a_tmdb');
    register_setting('kiranime_settings', '__a_jikan');
    register_setting('kiranime_settings', '__q_episode_by');
    register_setting('kiranime_settings', '__a_auto_dl');
    register_setting('kiranime_settings', '__u_def_language');
    register_setting('kiranime_settings', '__u_status_i_du');
    // register_setting('kiranime_theme', '__a_act_inline');

}

function kiranime_grabber_setting()
{
    $api_key        = get_option('__a_tmdb');
    $jikan_endpoint = get_option('__a_jikan');
    $episode_by     = get_option('__q_episode_by', 'kiranime_episode_released');
    $usepostdate    = get_option('__q_use_post_date') == '1' ? 'true' : 'false';
    $defaultlang    = get_option('__u_def_language', 'en');
    $usestatus      = get_option('__u_status_i_du') == '1' ? 'true' : 'false';
    $statusbg       = get_option('__u_status_bg', get_theme_mod('error-color', '#f43f5e'));
    $archive_titles = get_option('__archive_titles', "false");
    $video_meta     = get_option('__a_show_video_meta', '1') == '1' ? "true" : "false";
    $clt            = get_option('__c_long_time', "0");
    $mlt            = get_option('__c_medium_time', "0");
    $slt            = get_option('__c_short_time', "0");
    $krstk          = get_option('kiranime_recaptcha_sitekey');
    $krsck          = get_option('kiranime_recaptcha_secretkey');
    $urk            = get_option('__use_recaptcha', false) ? 'true' : 'false';
    $use_cache      = get_option('__kira_use_cache', false) ? 'true' : 'false';

    $localize_js = 'var jikan_endpoint = "' . $jikan_endpoint . '"; var tmdb_api = "' . $api_key . '"; var episode_by = "' . $episode_by . '";var usepostdate = JSON.parse(' . $usepostdate . ');var defaultlanguage= "' . $defaultlang . '";var usestatus = JSON.parse("' . $usestatus . '");var video_meta = JSON.parse("' . $video_meta . '");var status_bg = "' . $statusbg . '";var archive_titles = JSON.parse("' . $archive_titles . '");var clt = JSON.parse("' . $clt . '");var mlt = JSON.parse("' . $mlt . '");var slt = JSON.parse("' . $slt . '");var urk = JSON.parse("' . $urk . '");var krsck = "' . $krsck . '";var krstk = "' . $krstk . '";var use_cache = JSON.parse("' . $use_cache . '");';
    ?>
<script>
<?php echo $localize_js; ?>
</script>
<div class="w-full p-5 m-5 lg:max-w-[95%]">
    <div id="kiranime-settings-tool">

    </div>
</div>

<?php }

/**
 * will be added in the future (not in the roadmap)
 */
function kiranime_display_setting()
{
    $modules   = get_option('__homepage_modules', 'false');
    $spotlight = get_option('__spotlight_settings', 'false');
    $trending  = get_option('__trending_settings', 'false');

    $localjs = '
    var modules = JSON.parse("' . $modules . '");
    var spotModule = JSON.parse("' . $spotlight . '");
    var trenModule = JSON.parse("' . $trending . '");
    ';
    echo '<script>' . $localjs . '</script>';
    echo '<div class="max-w-screen w-full px-4" id="display-settings"></div>';
}

function kiranime_migrate_images()
{
    ob_start();?>
<style>
.progress-bar {
    background-color: #135e96;
    height: 0.25rem;
    margin-top: 1rem;
}
</style>
<div class="wrap card" style="max-width: 100%;">
    <h1>Import Remote Images</h1>
    <div style="margin-top: 1rem;font-size: 0.875rem;font-weight: 500;">
        Click button below to download all remote images, this may takes a few minutes depending how many anime/episode
        you have.
    </div>
    <button id="import-all-images" class="button button-primary" style="margin-top: 0.5rem;">Import image</button>
    <button id="remove-tool" class="button button-primary"
        style="background-color: #de2727;margin-top: 0.5rem;display: none;margin-left: 1rem;">Remove
        tool</button>

    <div id="migrate-stats" style="margin-top: 1rem;display: none;">
        <div style="display: flex;align-items: center;justify-content: start;gap: 1rem;">
            <div>
                <span style="font-size: 0.875rem;font-weight: 500;">Total Posts</span>
                <span data-total-posts style="font-size: 0.875rem;font-weight: 600">0</span>
            </div>
            <div>
                <span style="font-size: 0.875rem;font-weight: 500;">Total Processed</span>
                <span data-processed-posts style="font-size: 0.875rem;font-weight: 600">0</span>
            </div>
            <div style="display: none;">
                <span style="font-size: 0.875rem;font-weight: 500;">Current page</span>
                <span data-current-page style="font-size: 0.875rem;font-weight: 600">1</span>
            </div>
            <div>
                <span style="font-size: 0.875rem;font-weight: 500;">Total Pages</span>
                <span data-max-pages style="font-size: 0.875rem;font-weight: 600">1</span>
            </div>
        </div>
        <div class="progress-bar" style="width: 0;"></div>
        <div data-processed-log
            style="min-height: 320px;width: 100%;overflow-y: scroll;margin-top: 1rem;white-space: pre-line;font-size: 0.875rem;font-weight: 500;max-height: 320px;">
        </div>
    </div>
</div>
<?php
$element = ob_get_clean();

    echo $element;
}

function add_activation_theme()
{
    $vals  = get_option('__a_act_inline', false);
    $theme = wp_get_theme();
    ob_start();?>

<script>
var lc_dt = "<?php echo $vals ? $vals : '0'; ?>";
var theme_name = "<?php echo $theme->get('TextDomain'); ?>";
</script>
<div id="kiranime-activation-page"></div>
<?php
echo ob_get_clean();
}

function kiranime_build_search_index()
{
    ?>
<div id="search_index_rebuilder" class="bg-white rounded-md shadow-md drop-shadow-md mt-6 mr-6">
    <h2 class="w-full block leading-7 text-lg px-4 pt-4">Search Index Tools</h2>
    <div class="p-4 mt-4">
        <div class="mb-2">Click button below to start reindex all anime. This might take times if you have lots of anime
            post.
            Please don't close this tab while processing.</div>
        <button id="get-no-index"
            class="inline-flex disabled:opacity-50 justify-center py-2 px-8 border border-transparent shadow-sm text-sm font-medium rounded-md text-text-color bg-accent-3 hover:bg-accent-4 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-2">Reindex</button>

        <div class="index-result">

        </div>
    </div>
</div>
<?php
}
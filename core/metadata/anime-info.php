<?php
add_action('add_meta_boxes', 'kira_add_anime_meta_box');
function kira_add_anime_meta_box()
{
    add_meta_box(
        'kiranime_anime_fetch',
        __('Fetch Anime Info', 'kiranime'),
        'kiranime_add_fetch_metabox',
        ['anime', 'movie']
    );
    add_meta_box(
        'kiranime_anime_metabox_id',
        __('Anime Info', 'kiranime'),
        'kiranime_add_metabox_html',
        ['anime']
    );
    add_meta_box(
        'kiranime_download_metabox',
        __('Download', 'kiranime'),
        'kiranime_download_metabox',
        ['anime', 'episode', 'movie']
    );

}

/**
 * download metabox for anime and episode
 */
function kiranime_download_metabox($post)
{
    $m = get_post_meta($post->ID, 'kiranime_download_data', true);
    if (!empty($m) &$m !== '"[]"') {
        $downloads = is_string($m) ? json_decode(stripslashes($m)) : null;
    } else {
        $downloads = null;
    }
    ?>
<script>
window.downloadsData =
    <?php echo null !== $downloads && !empty($downloads) ? json_encode($downloads) : json_encode(null); ?>;
</script>
<div id="downloads-meta"></div>
<?php }

/**
 * save metada on save_post anime
 */
add_action('save_post_anime', 'kiranime_save_metadata', 10, 2);
function kiranime_save_metadata($post_id, $post)
{

    if (!isset($_POST['kiranime_anime_editor_nonce']) || !wp_verify_nonce($_POST['kiranime_anime_editor_nonce'], 'kiranime_anime_editor_nonce') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_posts')) {
        return $post_id;
    }

    $anime_c = new Anime($post_id);

    $serialize_download = json_encode('[]');
    if (isset($_POST['save_download_data']) && !empty($_POST['save_download_data'])) {
        $serialize_download = json_encode(json_decode(stripslashes($_POST['save_download_data']), true));
    }

    // save download data
    update_post_meta($post_id, 'kiranime_download_data', $serialize_download);

    $anime_c->update_meta($_POST)?->set_featured($_POST['kiranime_anime_featured'], $_POST['_thumbnail_id'])?->set_background($_POST['kiranime_anime_background'])?->init_meta();
}

/**
 * anime meta info metabox
 */
function kiranime_add_metabox_html($post)
{
    // check if current editor is block editor
    $block = get_current_screen()->is_block_editor();

    $prefix = 'kiranime_anime_';
    $keys   = [
        [
            'name'   => __('spotlight', 'kiranime'),
            'key'    => 'spotlight',
            'detail' => '',
        ],
        [
            'name'   => __('rate', 'kiranime'),
            'key'    => 'rate',
            'detail' => '',
        ],
        [
            'name'   => __('native', 'kiranime'),
            'key'    => 'native',
            'detail' => '',
        ],
        [
            'name'   => __('synonyms', 'kiranime'),
            'key'    => 'synonyms',
            'detail' => '',
        ],
        [
            'name'   => __('aired', 'kiranime'),
            'key'    => 'aired',
            'detail' => '',
        ],
        [
            'name'   => __('premiered', 'kiranime'),
            'key'    => 'premiered',
            'detail' => '',
        ],
        [
            'name'   => __('duration', 'kiranime'),
            'key'    => 'duration',
            'detail' => '',
        ],
        [
            'name'   => __('episodes', 'kiranime'),
            'detail' => '',
            'key'    => 'episodes',
        ],
        [
            'name'   => __('score', 'kiranime'),
            'key'    => 'score',
            'detail' => '',
        ],
        // [
        //     'name'   => __('trailer', 'kiranime'),
        //     'key'    => 'trailer',
        //     'detail' => '',
        // ],
        [
            'name'   => __('featured', 'kiranime'),
            'key'    => 'featured',
            'detail' => '',
        ],
        [
            'name'   => __('background', 'kiranime'),
            'key'    => 'background',
            'detail' => '',
        ],
        [
            'name'   => __('name', 'kiranime'),
            'key'    => 'name',
            'detail' => 'First season anime title',
        ],
        [
            'name'   => __('season', 'kiranime'),
            'key'    => 'season',
            'detail' => 'Is this anime a sequel? put season name/anything here. example: Season 2 or 2: part 1',
        ],
    ];
    $vals = [];

    foreach ($keys as $meta) {
        $meta_key = $prefix . $meta['key'];

        $vals[$meta_key] = [
            'val'    => get_post_meta($post->ID, $meta_key, true),
            'detail' => $meta['detail'],
            'name'   => $meta['name'],
        ];
    }?>
<script>
const is_block = parseInt("<?=$block ? 1 : 0?>");
</script>
<div data-anime-infomation class="w-full h-auto bg-white space-y-1">
    <?php wp_nonce_field('kiranime_anime_editor_nonce', 'kiranime_anime_editor_nonce')?>
    <input type="hidden" name="using_block_editor" value="<?php echo json_encode($block) ?>">
    <?php
foreach ($vals as $key => $val): ?>
    <?php if (stripos($key, 'background') !== false): ?>
    <div class="w-full h-auto p-2 lg:flex font-medium items-center space-y-1 relative">
        <label for="<?php echo $key; ?>"
            class="text-xs font-medium text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-2/12 flex-shrink-0"><?php echo ucfirst($val['name']); ?></label>
        <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>"
            <?php if ($val['detail']) {echo 'placeholder="' . $val['detail'] . '"';}?>
            class="px-4 py-2 text-sm font-normal text-slate-900 flex-auto"
            <?php if ($val['val']) {echo 'value="' . $val['val'] . '"';}?>>
        <label class="px-3 py-1 text-white bg-accent-2 self-stretch ml-2 rounded-sm" for="upload_background_url">Upload
            Background</label>
        <input type="file" id="upload_background_url" style="display: none;">
    </div>
    <?php continue;endif;?>
    <?php if (stripos($key, 'spotlight') === false) {

        if ('kiranime_anime_synonyms' === $key) {
            $val['val'] = is_string($val['val']) ? $val['val'] : implode(',', $val['val']);
        }?>
    <div class="w-full h-auto p-2 lg:flex font-medium items-center space-y-1 relative">
        <label for="<?php echo $key; ?>"
            class="text-xs font-medium text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-2/12 flex-shrink-0"><?php echo ucfirst($val['name']); ?></label>
        <input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>"
            <?php if ($val['detail']) {echo 'placeholder="' . $val['detail'] . '"';}?>
            class="px-4 py-2 text-sm font-normal text-slate-900 flex-auto"
            <?php if ($val['val']) {echo 'value="' . $val['val'] . '"';}?>>
    </div>
    <?php } else {
        ?>

    <div class="w-full h-auto p-2 lg:flex font-medium items-center space-y-1">
        <label for="<?php echo $key; ?>"
            class="text-xs font-medium text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-2/12 flex-shrink-0"><?php echo ucfirst($val['name']); ?></label>
        <input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>"
            <?php echo !empty($val['val']) ? 'checked' : '' ?>>
    </div>
    <?php }?>
    <?php endforeach;?>
    <template id="anime-input-field">
        <div class="hidden">
            <input type="text" name="" id="" class="px-4 py-2 text-sm font-normal text-slate-900 flex-auto" value="">
        </div>
    </template>
</div>

<?php
}

function kiranime_character_data($post)
{
    $characters = get_post_meta($post->ID, 'kiranime_anime_characters', true);
    ?>
<script>
<?php if ($characters && !empty($characters)) {
        echo "window.characters = " . $characters . ";";
    } else {
        echo 'window.characters = null;';
    }
    ?>
</script>
<div>
    <div id="characters-meta"></div>
</div>
<?php }

/**
 * fetch anime metabox
 */
function kiranime_add_fetch_metabox($post)
{
    $prefix = 'kiranime_anime_';
    $jikan  = get_option('__a_jikan', 'https://api.jikan.moe/v4');
    $tmdb   = get_option('__a_tmdb');

    $anime_id     = get_post_meta($post->ID, $prefix . 'id', true);
    $tmdb_type    = get_post_meta($post->ID, 'kiranime_anime_tmdb_type', true);
    $service_name = get_post_meta($post->ID, $prefix . 'service_name', true);
    ?>
<div class="w-full h-auto bg-white space-y-1">
    <input type="hidden" id="jikan_url"
        value="<?=$jikan && strlen($jikan) > 0 ? $jikan : 'https://api.jikan.moe/v4';?>">
    <input type="hidden" id="tmdb_api" value="<?=$tmdb;?>">
    <span data-notifications
        class="w-full block h-auto p-2 lg:flex text-base font-medium items-center space-y-1 error-1">

    </span>
    <div class="w-full h-auto p-2 lg:flex items-center space-y-1 gap-2">
        <label for="kiranime_anime_service_name"
            class="text-xs font-medium uppercase text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-3/12 flex-shrink-0">Choose
            Service</label>
        <select name="kiranime_anime_service_name" id="kiranime_anime_service_name" class="lg:w-9/12">
            <option value="tmdb" <?php if ('tmdb' == $service_name) {echo 'selected';}?>>TMDB</option>
            <option value="anilist" <?php if ('anilist' == $service_name) {echo 'selected';}?>>Anilist</option>
            <option value="mal" <?php if ('mal' == $service_name || !$service_name) {echo 'selected';}?>>MAL</option>
        </select>
    </div>
    <div data-tmdb-only
        class="w-full h-auto p-2 <?php if ('tmdb' !== $service_name) {echo 'hidden';} else {echo 'flex';}?> items-center space-y-1 gap-2">
        <label for="kiranime_anime_tmdb_type"
            class="text-xs font-medium uppercase text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-3/12 flex-shrink-0">TMDB
            Type</label>
        <select name="kiranime_anime_tmdb_type" id="kiranime_anime_tmdb_type" class="lg:w-9/12">
            <option value="tv" <?php if ($tmdb_type && 'tv' === $tmdb_type) {echo 'selected';}?>>TV Series</option>
            <option value="movie" <?php if ($tmdb_type && 'movie' === $tmdb_type) {echo 'selected';}?>>Movie</option>
        </select>
    </div>
    <div class="w-full h-auto p-2 lg:flex items-center space-y-1 gap-2">
        <label for="kiranime_anime_id"
            class="text-xs font-medium uppercase text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-3/12 flex-shrink-0">Anime
            ID</label>
        <input type="text" name="kiranime_anime_id" id="kiranime_anime_id"
            class="px-4 py-2 text-sm font-normal text-slate-900 flex-auto lg:w-9/12" value="<?php echo $anime_id; ?>">
    </div>
    <div class="w-max h-auto p-2 space-y-1 gap-2">
        <input type="button" name="get-anime" id="get-anime"
            class="px-4 py-2 text-sm font-normal cursor-pointer w-max text-text-color bg-accent-3 flex-auto"
            title="Get Anime" value="Get Anime" />
    </div>
</div>
<?php }
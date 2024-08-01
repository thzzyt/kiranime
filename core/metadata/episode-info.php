<?php
/**
 * Episode information
 *
 * @package kiranime
 */

/**
 * add new metabox to episode cpt
 */

function kiranime_add_episode_metabox()
{
    add_meta_box(
        'kiranime_episode_metabox_parent',
        'Anime Info',
        'kiranime_episode_anime_parent',
        ['episode']
    );
    add_meta_box(
        'kiranime_episode_metabox_fetch',
        'Episode Info Grabber',
        'kiranime_episode_info_grabber',
        ['episode'],
    );
    add_meta_box(
        'kiranime_episode_metabox',
        'Episode Information',
        'kiranime_episode_information_meta',
        ['episode'],
    );
    add_meta_box(
        'kiranime_episode_metabox_player',
        'Player Embed',
        'kiranime_episode_video_player',
        ['episode'],
    );

}
add_action('add_meta_boxes', 'kiranime_add_episode_metabox');

/**
 * for scheduled episode get embed data
 */

function kiranime_get_embed_for_future($new, $old, $post)
{

    if ('episode' !== $post->post_type || ('publish' !== $new || 'trash' === $new || 'future' !== $old)) {
        return $post->ID;
    }

    $ep_instance = new Episode($post->ID);
    $ep_instance->get_meta('anime_id', 'anime_season', 'number', 'parent_id', 'anime_type', 'players');

    $tmdbId = $ep_instance->meta['anime_id'];
    $season = $ep_instance->meta['anime_season'];
    $number = $ep_instance->meta['number'];
    $api    = get_option('__a_tmdb');

    if ($tmdbId && $season && $number && $api) {
        $request = wp_remote_request('https://api.themoviedb.org/3/tv/' . $tmdbId . '/season/' . $season . '/episode/' . $number . '?api_key=' . $api);

        if (is_wp_error($request)) {
            return $post->ID;
        }
        $response = json_decode(wp_remote_retrieve_body($request));

        $fields = [
            'thumbnail' => $response->still_path,
            'title'     => $response->name,
            'duration'  => $response->runtime,
            'released'  => $response?->air_date ? date('Y-m-d H:i:s', strtotime($response->air_date . ' 00:00:00')) : date('Y-m-d H:i:s'),
        ];

        $ep_instance->update = $fields;
        $ep_instance->update_meta();
    }

    $parent_id = $ep_instance->meta['parent_id'];
    if ($parent_id) {
        update_post_meta($parent_id, 'kiranime_anime_updated', time());
    }
    $tmbd_thumb = $fields['thumbnail'] ?? null;
    $thumb_url  = $_POST['kiranime_episode_thumbnail'] ?? $tmbd_thumb;
    $ep_instance?->set_featured($thumb_url, $_POST['_thumbnail_id'])?->fetch_vid();
    $is_notified    = get_post_meta($post->ID, 'notification_sent', true);
    $episode_number = get_post_meta($post->ID, 'kiranime_episode_number', true);

    if (empty($is_notified)) {
        Kiranime_Notification::notify($post->ID, $parent_id, $episode_number);
        update_post_meta($post->ID, 'notification_sent', '1');
    }
}
add_action('transition_post_status', 'kiranime_get_embed_for_future', 10, 3);

/**
 * Save metabox upon saving or updating episode
 */

add_action('save_post_episode', 'kiranime_save_episode_metadata', 10, 3);
function kiranime_save_episode_metadata($post_id, $post, $update)
{
    if (!isset($_POST['kiranime_episode_editor_nonce']) || !wp_verify_nonce($_POST['kiranime_episode_editor_nonce'], 'kiranime_episode_editor_nonce') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_posts') || 'trash' === $post->post_status) {
        return $post_id;
    }

    $players = [];
    if (isset($_POST['save_episode_player']) && !empty($_POST['save_episode_player'])) {
        $players = $_POST['save_episode_player'];
    }

    $serialize_download = json_encode('[]');
    if (isset($_POST['save_download_data']) && !empty($_POST['save_download_data'])) {
        $serialize_download = json_encode(json_decode(stripslashes($_POST['save_download_data']), true));
    }

    //save download
    update_post_meta($post_id, 'kiranime_download_data', $serialize_download);
    $episode = new Episode($post_id, $_POST);
    $episode?->update_meta()?->get_meta('anime_id', 'anime_season', 'number', 'parent_id', 'anime_type')?->update_player($players)?->set_featured($_POST['kiranime_episode_thumbnail'], $_POST['_thumbnail_id'])?->fetch_vid();

    $is_notified = get_post_meta($post->ID, 'notification_sent', true);
    if (empty($is_notified)) {
        Kiranime_Notification::notify($post->ID, $episode->meta['parent_id'], $episode->meta['number']);
        update_post_meta($post->ID, 'notification_sent', '1');
    }
}

/**
 * episode metabox html
 */

function kiranime_episode_information_meta($post)
{
    $prefix              = 'kiranime_episode_';
    $keys                = ['number', 'title', 'duration', 'thumbnail', 'released'];
    $vals                = [];
    $create_notification = get_post_meta($post->ID, 'create_notification', true);
    $create_notification = !empty($create_notification) ? $create_notification : 1;
    foreach ($keys as $key) {
        $meta_key = $prefix . $key;
        if ('released' == $key) {
            $vals[$meta_key] = get_post_meta($post->ID, $meta_key, true) ? get_post_meta($post->ID, $meta_key, true) : $post->post_date;
        } else {
            $vals[$meta_key] = get_post_meta($post->ID, $meta_key, true);
        }
    }?>

<div class="w-full h-auto bg-white space-y-1">
    <?php wp_nonce_field('kiranime_episode_editor_nonce', 'kiranime_episode_editor_nonce')?>
    <input type="hidden" name="create_notification" value="<?php echo json_encode($create_notification); ?>">
    <?php foreach ($vals as $key => $val): ?>
    <div>
        <span
            class="text-xs font-semibold inline-block py-1 px-2 rounded-t text-slate-700 bg-slate-300 uppercase w-2/12 mr-1 flex-shrink-0">
            <?php echo str_replace($prefix, '', $key); ?>
        </span>
        <div class="mb-3 pt-0 flex-auto">
            <input type="text" placeholder="Episode <?php echo str_replace($prefix, '', $key); ?>"
                name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $val; ?>"
                class="px-3 py-3 placeholder-gray-400 text-blueGray-600 relative bg-white rounded text-sm border-none ring-1 ring-sky-200 shadow outline-none focus:outline-none focus:ring w-full" />
        </div>
    </div>
    <?php endforeach;?>
</div>
<?php
}

function kiranime_episode_anime_parent($post)
{
    $prefix = 'kiranime_episode_';
    $keys   = ['parent_id', 'parent_name', 'parent_slug', 'parent_romaji'];
    $vals   = [];

    foreach ($keys as $key) {
        $meta_key = $prefix . $key;

        $vals[$meta_key] = get_post_meta($post->ID, $meta_key, true);
    }?>

<div class="w-full h-auto bg-white space-y-1">
    <div>
        <span
            class="text-xs font-semibold inline-block py-1 px-2 rounded-t text-slate-700 bg-slate-300 uppercase w-2/12 mr-1 flex-shrink-0">
            Anime title
        </span>
        <div class="mb-3 pt-0 flex-auto relative">
            <input data-anime-name-input type="text" autocomplete="off" name="kiranime_episode_parent_name"
                id="kiranime_episode_parent_name" placeholder="Anime title"
                class="px-3 py-3 placeholder-gray-400 text-blueGray-600 relative bg-white rounded text-sm border-none ring-1 ring-sky-200 shadow outline-none focus:outline-none focus:ring w-full"
                value="<?php echo $vals['kiranime_episode_parent_name']; ?>" />
            <div class="w-full mt-2 p-2 absolute hidden top-full left-0 flex-col gap-2 bg-white z-50"
                data-anime-name-result>

            </div>
            <input type="number" name="kiranime_episode_parent_id"
                value="<?php echo $vals['kiranime_episode_parent_id']; ?>" data-anime-id class="hidden">
            <input type="text" name="kiranime_episode_parent_slug"
                value="<?php echo $vals['kiranime_episode_parent_slug']; ?>" data-anime-slug class="hidden">
            <input type="text" name="kiranime_episode_parent_romaji"
                value="<?php echo $vals['kiranime_episode_parent_romaji']; ?>" data-anime-romaji class="hidden">
        </div>
    </div>


</div>
<?php
}

/**
 * episode player metabox
 */

function kiranime_episode_video_player($post)
{
    $episode = new Episode($post->ID);
    $episode->set_embed_type(false);
    $episode->get_meta('players');
    $players = $episode->players;
    ?>
<script>
let players =
    <?php echo null !== $players && !empty($players) ? (is_array($players) ? json_encode($players) : $players) : json_encode([]); ?>;

players = players && typeof players === 'string' ? JSON.parse(players) : players;
</script>
<div id="player-meta">

</div>
<?php }

function kiranime_episode_info_grabber($post, $post_fetch)
{
    $prefix = 'kiranime_episode_';
    $jikan  = get_option('__a_jikan', 'https://api.jikan.moe/v4');
    $tmdb   = get_option('__a_tmdb');

    $anime_id            = get_post_meta($post->ID, $prefix . 'anime_id', true);
    $anime_season        = get_post_meta($post->ID, $prefix . 'anime_season', true);
    $anime_type          = get_post_meta($post->ID, $prefix . 'anime_type', true);
    $tmdb_episode_number = get_post_meta($post->ID, $prefix . 'tmdb_fetch_episode', true);
    ?>
<div class="w-full h-auto bg-white space-y-1">
    <input type="hidden" id="jikan_url"
        value="<?=$jikan && strlen($jikan) > 0 ? $jikan : 'https://api.jikan.moe/v4';?>">
    <input type="hidden" id="tmdb_api" value="<?=$tmdb;?>">
    <span data-notifications
        class="w-full block h-auto p-2 lg:flex text-base font-medium items-center space-y-1 error-1">

    </span>
    <div class="w-full h-auto p-2 lg:flex items-center space-y-1 gap-2">
        <label for="kiranime_episode_anime_id"
            class="text-xs font-medium uppercase text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-3/12 flex-shrink-0">
            TMDB Anime ID
        </label>
        <input type="text" name="kiranime_episode_anime_id" id="kiranime_episode_anime_id"
            class="px-4 py-2 text-sm font-normal text-slate-900 flex-auto lg:w-10/12" value="<?php echo $anime_id; ?>">
    </div>
    <div class="w-full h-auto p-2 lg:flex items-center space-y-1 gap-2">
        <label for="kiranime_episode_anime_type"
            class="text-xs font-medium uppercase text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-3/12 flex-shrink-0">Choose
            Type</label>
        <select name="kiranime_episode_anime_type" id="kiranime_episode_anime_type" class="lg:w-10/12">
            <option value="movie" <?php if ('movie' == $anime_type) {echo 'selected';}?>>Movie</option>
            <option value="series" <?php if ('series' == $anime_type || !$anime_type) {echo 'selected';}?>>TV Series
            </option>
        </select>
    </div>
    <div class="w-full h-auto p-2 lg:flex items-center space-y-1 gap-2">
        <label for="kiranime_episode_anime_season"
            class="text-xs font-medium uppercase text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-3/12 flex-shrink-0">
            TMDB Anime Season
        </label>
        <input type="text" name="kiranime_episode_anime_season" id="kiranime_episode_anime_season"
            class="px-4 py-2 text-sm font-normal text-slate-900 flex-auto lg:w-10/12"
            value="<?php echo $anime_season; ?>">
    </div>
    <div class="w-full h-auto p-2 lg:flex items-center space-y-1 gap-2">
        <label for="kiranime_episode_tmdb_fetch_episode"
            class="text-xs font-medium uppercase text-slate-800 active:text-slate-900 hover:text-slate-900 focus-within:text-slate-900 min-w-max lg:w-3/12 flex-shrink-0">
            Episode Number
        </label>
        <input type="text" name="kiranime_episode_tmdb_fetch_episode" id="kiranime_episode_tmdb_fetch_episode"
            class="px-4 py-2 text-sm font-normal text-slate-900 flex-auto lg:w-10/12"
            value="<?php echo $tmdb_episode_number; ?>">
    </div>
    <div id="episode-info-notification" class="w-full hidden h-auto p-2 space-y-1 gap-2">
        <span class="text-primary border-2 border-accent"></span>
    </div>
    <div class="w-max h-auto p-2 space-y-1 gap-2">
        <input type="button" name="get-episode-info" id="get-episode-info"
            class="px-4 py-2 text-sm font-normal cursor-pointer w-max text-text-color bg-accent-3 flex-auto rounded"
            title="Get Episode Info" value="Get Episode Info" />
    </div>
</div>
<?php }
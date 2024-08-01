<?php

if (isset($_GET['n_id'])) {
    Kiranime_Notification::checked([$_GET['n_id']], get_current_user_id());
}

$episode = new Episode(get_the_ID());
$episode->set_embed_type(true);
$episode->get_meta('title', 'parent_id', 'number', 'duration', 'thumbnail', 'download', 'players', 'released')->get_taxonomies('episode_type')->image('full');
$anime = isset($episode->meta['parent_id']) ? new Anime($episode->meta['parent_id']) : null;

if (!is_null($anime)) {
    $anime->get_image()->get_meta('title', 'episodes', 'featured', 'duration', 'rate')->get_taxonomies('type', 'anime_attribute')->get_episodes(false, -1, 'title', 'number')->get_votes()->get_scheduled();
}
if (!$anime || !$episode) {
    wp_redirect('/x-wp-not-found-episode');
    exit;
}

$set_history = [
    'anime'       => $anime->post->post_title,
    'number'      => sprintf(__('Episode %d', 'kiranime'), $episode->meta['number']),
    'anime_url'   => $anime->url,
    'image'       => $episode->get_thumbnail() ?? $anime->get_image_thumbnail_html(),
    'episode_url' => $episode->url,
    'id'          => $episode->id,
];
?>
<script>
var current_post_data_id = JSON.parse(<?php echo $anime->anime_id; ?>);
var history_add_data = "<?php echo base64_encode(json_encode($set_history)); ?>";
</script>
<section role="heading" class="relative h-full min-h-100 pt-[50px] md:pt-17 pb-5">
    <div class="bg-cover bg-center opacity-30 blur-xl absolute inset-0 z-0"
        style="background-image: url('<?=!empty($anime->meta['featured']) ? $anime->meta['featured'] : $anime->image;?>')">
    </div>
    <?php get_template_part('template-parts/sections/component/episode', 'player-section', ['episode' => $episode, 'anime' => $anime])?>
</section>
<section class="w-full">
    <?php if (get_theme_mod('__show_share_button', 'show') === 'show'): ?>
    <div class=" w-full lg:px-10 py-5 bg-darkest relative flex gap-5  items-end">
        <div
            class="w-6/12 lg:w-2/12 pl-5 before:absolute before:hidden lg:before:block before:inset-0 before:h-full before:w-0.5 before:bg-accent-3 relative">
            <span class="text-sm font-semibold block">
                <?php _e('Share This Anime', 'kiranime');?>
            </span>
            <span class="block text-xs font-light">
                <?php _e('to your friends!', 'kiranime');?>
            </span>
        </div>
        <?php get_template_part('template-parts/sections/component/use', 'share');?>
    </div>
    <?php endif;?>
    <div class="lg:flex gap-10 lg:px-10 px-4 md:px-5 py-10">
        <section class="flex-auto lg:w-9/12 w-full">
            <!-- start download -->
            <?php if (get_theme_mod('__show_download_episode', 'show') === 'show'): get_template_part('template-parts/sections/component/use', 'download', ['downloads' => $episode->meta['download']]);endif;?>
            <!-- end download -->
            <!-- Start comments -->
            <div class="py-5 my-5">
                <?php
// If comments are open or we have at least one comment, load up the comment template.
if (comments_open() || get_comments_number()):
    comments_template();
endif;
?>
            </div>
            <!-- end comments -->
            <!-- Start Recomended Anime -->
            <?php if (get_theme_mod('__show_related_episode', 'show') === 'show'): get_template_part('template-parts/sections/component/use', 'recommended', ['style' => get_theme_mod('__show_related_episode_display', 'grid'), 'related' => get_theme_mod('__show_related_episode_by', 'genre'), 'total' => get_theme_mod('__show_related_episode_count', 12), 'title' => get_theme_mod('__show_related_episode_label', __('Recomended For You!', 'kiranime')), 'id' => $anime->anime_id]);endif;?>
            <!-- End Recomended Anime -->
        </section>
        <aside class="w-full lg:w-3/12 flex-shrink-0 min-h-300 py-4 mt-5 lg:mt-0">
            <?php if (is_active_sidebar('anime-info-sidebar')): dynamic_sidebar('anime-info-sidebar');endif;?>
        </aside>
    </div>
</section>
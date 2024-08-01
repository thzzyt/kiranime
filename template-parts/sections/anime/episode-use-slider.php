<?php
if (isset($args['anime']->episodes) && count($args['anime']->episodes) > 0):
    $order    = get_theme_mod('__show_episode_order', 'desc') === 'desc';
    $episodes = $order ? $args['anime']->episodes : array_reverse($args['anime']->episodes);
    $anime    = $args['anime'];
    ?>
<section class="space-y-5 mb-5">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold col-span-full text-accent-2 text-xl lg:text-2xl leading-loose">
            <?php if (get_theme_mod('__show_episode_label')) {echo get_theme_mod('__show_episode_label');} else {_e('Episodes', 'kiranime');}?>
        </h2>
        <div class="swiper-navigation navigate-episode-list flex items-center justify-between gap-1 w-max">
            <div
                class="navigate-episode-list-prev flex items-center justify-center cursor-pointer group py-1 px-2 rounded-l hover:bg-accent-2 hover:text-text-color">
                <span class="material-icons-round text-xl lg:text-2xl">
                    navigate_before
                </span>
            </div>
            <div
                class="navigate-episode-list-next flex items-center cursor-pointer group py-1 px-2 rounded-r hover:bg-accent-2 hover:text-text-color">
                <span class="material-icons-round text-xl lg:text-2xl">
                    navigate_next
                </span>

            </div>
        </div>
    </div>
    <div class="swiper swiper-episode-anime" data-current-slider="episode-list">
        <div class="swiper-wrapper">
            <?php foreach ($episodes as $episode): ?>
            <div class="swiper-slide">
                <a href="<?php echo $episode->url ?>" title="<?php echo $episode->post->post_title ?>"
                    class="w-full flex flex-row justify-end rounded-sm lg:rounded-md shadow-md ring-primary bg-cover bg-no-repeat bg-center relative aspect-w-16 aspect-h-9 group overflow-hidden">
                    <div class="thumbnail_url_episode_list">
                        <?php echo $episode->get_thumbnail() ?>
                    </div>
                    <div class="w-full h-full flex flex-col justify-end absolute">
                        <div class="w-full font-normal text-base text-text-color px-2 py-1 bg-primary bg-opacity-75">
                            <span class="w-percentile pt-1 line-clamp-1 text-xs font-semibold">
                                <?php echo $episode->meta['title'] ? $episode->meta['title'] : sprintf('%s %s', __('Episode', 'kiranime'), $episode->meta['number'] ?? '?') ?>
                            </span>
                        </div>
                    </div>
                    <div
                        class="hidden absolute inset-0 w-full h-full group-hover:flex items-center justify-center bg-primary bg-opacity-20">
                        <span class="material-icons-round text-2xl">
                            play_circle_filled
                        </span>
                    </div>
                    <span
                        class="absolute shadow-md drop-shadow-md w-max h-max px-4 py-1.5 bg-accent-3 text-text-color text-xs font-medium rounded-es-xl">
                        <?php printf('%s %s', __('Episode', 'kiranime'), $episode->meta['number'] ?? '?')?>
                    </span>
                </a>
            </div>
            <?php endforeach;?>
        </div>
    </div>
</section>
<?php endif;?>
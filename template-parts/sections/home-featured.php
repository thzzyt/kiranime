<?php
$query    = $args['query'];
$animes   = new Kira_Query(array_merge($query, ['posts_per_page' => get_theme_mod('__featured_count', 7)]), 'anime');
$page_url = $args['archive'];
$title    = $args['title'];
?>
<section class="min-h-300 w-full mx-auto bg-secondary rounded-sm shadow drop-shadow z-0 hover:z-40 relative pb-10">
    <h2 class="p-4 text-base leading-4 font-medium text-accent-2">
        <?=$title;?>
    </h2>
    <ul class="m-0 p-0">
        <?php
if (!$animes->empty):
    foreach ($animes->animes as $anime):
        $anime->get_meta('featured', 'score', 'duration', 'episodes')->get_taxonomies('type', 'anime_attribute')->get_episodes(true);
        $meta   = $anime->meta;
        $latest = isset($anime->episodes) ? $anime->episodes : [];
        $type   = isset($anime->taxonomies['type']) ? $anime->taxonomies['type'] : [];
        ?>

		        <li class="odd:bg-tertiary flex gap-4 px-4 py-2 min-h-[5.25rem]">
		            <div <?php if (get_theme_mod('__show_tooltip', 'show') === 'show'): ?>
		                data-tippy-featured-id="<?php echo $anime->anime_id ?>" <?php endif;?>
	                class=" w-12 h-16 pb-16 rounded shadow flex-shrink-0 relative overflow-hidden bg-primary 						">
	                <a href="<?php echo $anime->url; ?>">
	                    <?php echo $anime->get_image_thumbnail_html('smallthumb', "absolute inset-0 w-full h-full") ?>
	                </a>
	            </div>

	            <div class="w-full flex-auto">
	                <h3 class="text-sm line-clamp-2 font-medium leading-6 mb-1">
	                    <a href="<?php echo $anime->url; ?>" title="<?php echo $anime->post->post_title; ?>"
	                        class="dynamic-name"><?php echo $anime->post->post_title ?></a>
	                </h3>
	                <div class="flex items-center text-xs gap-2">
	                    <span class="whitespace-nowrap uppercase">
	                        <?php echo !is_wp_error($type) && count($type) != 0 ? $type[0]->name : 'TV'; ?>
	                    </span>
	                    <span class="w-1 h-1 rounded-full bg-white bg-opacity-25 inline-block"></span>
	                    <?php if ($type && $type[0] && ('tv' === $type[0]->slug || 'series' === $type[0]->slug)): ?>
	                    <span class="whitespace-nowrap">
	                        Ep
	                        <?php echo isset($latest) && isset($latest->meta['number']) ? $latest->meta['number'] : '?' ?>
	                    </span>
	                    <span class=" w-1 h-1 rounded-full bg-white bg-opacity-25 inline-block "></span>
	                    <?php endif;?>
                    <span
                        class="whitespace-nowrap fdi-duration"><?php echo $meta['duration'] ? $meta['duration'] : '24m'; ?></span>
                </div>
            </div>
        </li>
        <?php endforeach;endif;?>
    </ul>
    <div class="w-full absolute bottom-0">
        <a class=" p-4 py-2 text-text-color text-opacity-90 bg-opacity-10 bg-white hover:bg-opacity-30 font-light flex items-center justify-center gap-2 "
            href="<?php echo $page_url ?>">
            <?php _e('View More', 'kiranime');?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" class="w-5 h-5">
                <path fill="currentColor"
                    d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z" />
            </svg>
        </a>
    </div>
</section>
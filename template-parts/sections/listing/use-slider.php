<?php
$animes    = $args['animes'];
$usestatus = get_option('__u_status_i_du');

foreach ($animes as $anime):
    $anime->get_meta('score', 'rate', 'premiered', 'aired', 'synonyms', 'native', 'duration', 'episodes')->get_taxonomies('type', 'anime_attribute', 'status')->get_episodes(true);
    $status = isset($anime->taxonomies['status']) ? $anime->taxonomies['status'] : [];
    $attr   = isset($anime->taxonomies['anime_attribute']) ? $anime->taxonomies['anime_attribute'] : [];
    $latest = !empty($anime->episodes) ? $anime->episodes : null;
    $type   = isset($anime->taxonomies['type']) ? $anime->taxonomies['type'] : [];
    $meta   = $anime->meta;
    ?>
	<div class="swiper-slide">
	    <div class="w-full bg-gradient-to-t from-primary to-transparent rounded shadow shadow-primary">
	        <div class="block relative w-full group kira-anime overflow-hidden">
	            <?php if ($usestatus):
        $status = !is_wp_error($status) && !empty($status) ? $status[0]->name : null;
        if ($status && 'completed' == strtolower($status)):
        ?>
		            <div class="status_show" style="background-color: var(--completed-status)"><?php
        echo $status; ?></div>
		            <?php endif;endif;?>
            <?php echo $anime->get_image_thumbnail_html('kirathumb', "absolute inset-0 object-cover w-full h-full rounded shadow") ?>

            <div class="absolute inset-0 top-1/4" style="
					background: linear-gradient(0deg, rgba(var(--overlay-color), 1) 0, rgba(42, 44, 49, 0) 76%);
				"></div>
            <div class="flex items-center justify-between px-2 pb-2 absolute bottom-0 inset-x-0">
                <!-- attribute -->
                <?php if (!is_wp_error($attr) && count($attr) > 0): ?>
                <div class="min-w-max">
                    <span
                        class="text-text-accent block bg-accent-2/80 h-[25px] rounded-md text-[11px] p-1 mr-px font-medium">
                        <?php $a = array_map(function ($val) {return $val->name;}, $attr);
echo implode('/', $a);?>
                    </span>
                </div>
                <?php endif;?>
                <!-- episode -->
                <?php if (isset($type[0]) && !empty($type[0]) && in_array($type[0]->slug, ['ona', 'series', 'tv', 'ova'])): ?>
                <span class="text-[11px] px-2 py-1 rounded-md font-medium h-[25px] text-text-accent bg-accent-3">
                    E<?php echo isset($latest) && isset($latest->meta['number']) ? $latest->meta['number'] : '?'; ?>
                </span>
                <?php endif;?>
            </div>
            <a <?php if (get_theme_mod('__show_tooltip', 'show') === 'show'): ?> data-tippy-trigger
                data-tippy-content-to="<?=$anime->post->ID;?>" <?php endif;?> href="<?=$anime->url;?>"
                class="group-hover:bg-opacity-75 bg-overlay hidden group-hover:flex items-center justify-center absolute inset-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-8 h-8">
                    <path fill="currentColor"
                        d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z" />
                </svg>
            </a>

            <?php if (isset($anime->meta['rate']) && stripos($anime->meta['rate'], '18') !== false): ?>
            <span
                class="bg-orange-600 text-text-color text-xs font-semibold px-2 py-1 rounded-md absolute top-2 right-2">
                18+
            </span>
            <?php endif;?>
        </div>

        <div style="min-height: 4.906rem" class="flex h-auto md:h-24 lg:h-24 flex-col justify-between p-2 bg-overlay">
            <!-- Title -->
            <a href="<?=$anime->url;?>" class="text-sm line-clamp-2 font-medium leading-snug lg:leading-normal">
                <?php echo $anime->post->post_title ?>
            </a>
            <!-- type and length -->
            <div class="text-xs text-text-color w-full line-clamp-1 absolute bottom-1 text-opacity-75">
                <span
                    class="inline-block md:my-3 uppercase"><?=!is_wp_error($type) && isset($type[0]) ? $type[0]->name : 'TV';?></span>
                <span class="inline-block bg-gray-600 w-1 h-1 mx-2"></span>
                <span
                    class="inline-block md:my-3"><?php echo isset($anime->meta['duration']) ? $anime->meta['duration'] : '24m'; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?php endforeach;?>
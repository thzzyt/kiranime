<div class="w-full md:mt-[50px] lg:mt-0 sm:mb-5 bg-accent-2 bg-opacity-0 flex items-center relative justify-center">
    <div class="swiper swiper-spotlight ">
        <!-- Additional required wrapper -->
        <div class="swiper-wrapper">
            <!-- Slides -->
            <?php
$q = new Kira_Query();
$q = $q->spotlight();
if (!$q->empty): foreach ($q->animes as $dx => $anime):
        $anime->get_meta('background', 'duration', 'aired', 'premiered')->get_taxonomies('type', 'anime_attribute')->get_episodes(true);
        $meta   = $anime->meta;
        $type   = isset($anime->taxonomies['type'][0]) ? $anime->taxonomies['type'] : null;
        $attr   = isset($anime->taxonomies['anime_attribute'][0]) ? $anime->taxonomies['anime_attribute'] : null;
        $latest = isset($anime->episodes) && is_object($anime->episodes) ? $anime->episodes : [];
        $index  = $dx + 1;
        ?>
		            <div class="swiper-slide">
		                <div class="w-full h-full absolute inset-0">
		                    <div class="w-full h-full absolute inset-0 overflow-hidden">
		                        <div class="after:absolute after:inset-0 z-[2] image-after">
		                            <?php if (!empty($meta['background'])): ?>
		                            <img class="opacity-60 absolute object-cover w-full h-full blur-0"
		                                data-src="<?php echo $meta['background'] ?>"
		                                alt="<?php echo $anime->post->post_title ?>" src="<?php echo $meta['background'] ?>">
		                            <?php else:echo $anime->get_image_thumbnail_html('full', 'opacity-60 absolute object-cover w-full h-full blur-0');endif;?>
	                        </div>
	                    </div>
	                    <div
	                        class="pr-16 sm:pr-24 pl-4 top-auto transform-none bottom-8 w-full lg:w-8/12 max-w-[800px] absolute z-[3] lg:bottom-10 lg:pl-10">
	                        <div class="text-xs mb-3 sm:text-sm lg:text-lg text-accent">#<?php echo $index; ?> Spotlight
	                        </div>
	                        <div
	                            class="text-base sm:text-xl md:text-3xl xl:text-5xl xl:font-bold line-clamp-2 max-h-36 lg:leading-relaxed font-semibold mb-5">
	                            <?php echo $anime->post->post_title ?></div>
	                        <div
	                            class="hidden md:flex items-center justify-start gap-4 text-sm font-normal mb-2.5 h-7 text-gray-300">
	                            <div class="uppercase flex items-center gap-1">
	                                <span class="material-icons-round">
	                                    play_circle
	                                </span> <?php if (!is_wp_error($type) && !empty($type)) {echo $type[0]->name;}?>
	                            </div>
	                            <div class=" flex items-center gap-1">
	                                <span class="material-icons-round">
	                                    watch_later
	                                </span>
	                                <?php echo !empty($meta['duration']) ? $meta['duration'] : '24m'; ?>
	                            </div>
	                            <div class="flex items-center gap-1">
	                                <span class="material-icons-round">
	                                    event
	                                </span>
	                                <?php echo !empty($meta['aired']) ? trim($meta['aired']) : $meta['premiered'] ?>
	                            </div>

	                            <div class="flex items-center gap-1">
	                                <?php if (!is_wp_error($attr) && !empty($attr)): foreach ($attr as $att): ?>
		                                <span
		                                    class="quality odd:bg-accent even:bg-white rounded px-1 py-0.5 text-gray-900 text-xs font-semibold">
		                                    <?php echo $att->name; ?>
		                                </span>
		                                <?php endforeach;endif;?>
                            </div>
                        </div>
                        <div class="text-[13px] leading-5 mb-4 line-clamp-2 md:mb-8 md:leading-6 text-gray-300">
                            <?php echo $anime->post->post_content ?>
                        </div>
                        <div class="flex items-center justify-start gap-2.5">
                            <a href="<?php echo isset($latest->url) ? $latest->url : $anime->url; ?>"
                                class="px-6 py-2 bg-accent-2 hover:bg-accent rounded-full flex items-center gap-2 text-xs lg:text-sm font-medium">
                                <span class="material-icons-round font-medium text-xl">
                                    play_circle
                                </span>
                                <?php _e('Watch Now', 'kiranime');?></a>
                            <a href="<?php echo $anime->url; ?>"
                                class="px-6 py-2 bg-secondary hover:brightness-105 rounded-full flex items-center gap-1 text-xs lg:text-sm font-medium">Detail<span
                                    class="material-icons-round font-medium text-2xl">
                                    navigate_next
                                </span></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
endforeach;
endif;?>
        </div>
        <div class="swiper-navigation absolute bottom-5 right-5 z-10 hidden sm:block">
            <div class="mb-2 bg-primary rounded-sm shadow-sm p-2 nav-next hover:bg-accent-3" tabindex="0" role="button"
                aria-label="Next slide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" class="w-6 h-6">
                    <path fill="currentColor"
                        d="M187.8 264.5L41 412.5c-4.7 4.7-12.3 4.7-17 0L4.2 392.7c-4.7-4.7-4.7-12.3 0-17L122.7 256 4.2 136.3c-4.7-4.7-4.7-12.3 0-17L24 99.5c4.7-4.7 12.3-4.7 17 0l146.8 148c4.7 4.7 4.7 12.3 0 17z" />
                </svg>
            </div>
            <div class="bg-primary rounded-sm shadow-sm p-2 nav-prev hover:bg-accent-3" tabindex="0" role="button"
                aria-label="Previous slide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" class="w-6 h-6">
                    <path fill="currentColor"
                        d="M4.2 247.5L151 99.5c4.7-4.7 12.3-4.7 17 0l19.8 19.8c4.7 4.7 4.7 12.3 0 17L69.3 256l118.5 119.7c4.7 4.7 4.7 12.3 0 17L168 412.5c-4.7 4.7-12.3 4.7-17 0L4.2 264.5c-4.7-4.7-4.7-12.3 0-17z" />
                </svg>
            </div>
        </div>
    </div>
</div>
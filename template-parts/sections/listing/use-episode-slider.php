<?php
$episodes = $args['episodes'];
foreach ($episodes as $episode):
    $episode->get_meta('title', 'number', 'parent_id', 'parent_name');
    $image = get_the_post_thumbnail($episode->id, 'kirathumb', ['class' => 'w-full h-full object-cover', "alt" => $episode->meta['title']]);

    $anime = new Anime(intval($episode->meta['parent_id']));
    $anime->get_meta('featured', 'background')->get_taxonomies('anime_attribute');
    if (!$image) {
        $image = get_the_post_thumbnail($anime->anime_id, 'kirathumb', ['class' => 'w-full h-full object-cover', "alt" => $episode->meta['title']]);
    }

    $attr     = $anime->taxonomies['anime_attribute'];
    $anititle = $anime->post->post_title;
    ?>
	<div class="swiper-slide">
	    <a href="<?php echo $episode->url; ?>"
	        class="block w-full relative overflow-hidden rounded-md shadow-md ring-primary aspect-w-16 aspect-h-9 group">
	        <div class="w-full absolute z-0">
	            <?=$image?>
	        </div>
	        <span
	            class="block absolute top-0 left-0 bg-accent-2 min-w-0 w-max h-max text-xs font-medium py-1 px-2 rounded-br-md shadow-md">
	            <?php printf(__('Episode %1$s', 'kiranime'), $episode->meta['number'])?>
	        </span>
	        <div class="w-full p-1 absolute z-[1] top-[50%] md:top-[55%] lg:top-[60%] inset-x-0 bg-primary/75">
	            <h4 class="text-xs font-medium line-clamp-1 py-0.5">
	                <?php echo $episode->meta['title'] ?? $episode->post->post_title; ?>
	            </h4>
	            <div class="bg-white bg-opacity-10 h-0.5 w-full"></div>
	            <span class="text-xs font-medium line-clamp-1 py-0.5"><?php echo $episode->meta['parent_name'] ?></span>
	        </div>
	        <div
	            class="absolute inset-0 w-full h-full bg-primary/30 z-[2] flex items-center justify-center group-hover:opacity-100 opacity-0 transition-opacity duration-200 ease-in-out">
	            <span class="material-icons-round text-2xl">
	                play_circle_filled
	            </span>
	        </div>
	    </a>
	</div>
	<?php endforeach;?>
<?php

$anime = new Anime(get_the_ID());

$anime->get_image('kirathumb')->get_meta('background', 'characters', 'download', 'score', 'native', 'synonyms', 'score', 'rate', 'premiered', 'aired', 'duration', 'episodes', 'name')->get_taxonomies('type', 'anime_attribute', 'producer', 'licensor', 'studio', 'genre')->get_season()->get_episodes(false, get_theme_mod('__show_episode_count', '-1'), 'title', 'number', 'thumbnail');

$meta          = $anime->meta;
$type          = $anime->taxonomies['type'];
$attr          = $anime->taxonomies['anime_attribute'];
$season_list   = $anime->seasons;
$latest        = $anime->episodes && !empty($anime->episodes) ? $anime->episodes[0] : null;
$first_episode = $anime->episodes && !empty($anime->episodes) ? $anime->episodes[count($anime->episodes) - 1] : null;

$showMeta = [];
foreach ($meta as $key => $value) {
    if (in_array($key, ['spotlight', 'background', 'updated', 'voted', 'voted_by', 'vote_score', 'download', 'featured', 'characters', 'title'])) {
        continue;
    }

    $showMeta[$key] = $value;
}

$taxs = [
    'genre'    => [
        'name' => __('Genre', 'kiranime'),
        'data' => array_map(function ($v) {
            return [
                'name' => $v->name,
                'url'  => get_term_link($v),
            ];
        }, $anime->taxonomies['genre'] ? $anime->taxonomies['genre'] : []),
    ],
    'studio'   => [
        'name' => __('Studio', 'kiranime'),
        'data' => array_map(function ($v) {
            return [
                'name' => $v->name,
                'url'  => get_term_link($v),
            ];
        }, $anime->taxonomies['studio'] ? $anime->taxonomies['studio'] : []),
    ],
    'licensor' => [
        'name' => __('Licensor', 'kiranime'),
        'data' => array_map(function ($v) {
            return [
                'name' => $v->name,
                'url'  => get_term_link($v),
            ];
        }, $anime->taxonomies['licensor'] ? $anime->taxonomies['licensor'] : []),
    ],
    'producer' => [
        'name' => __('Producer', 'kiranime'),
        'data' => array_map(function ($v) {
            return [
                'name' => $v->name,
                'url'  => get_term_link($v),
            ];
        }, $anime->taxonomies['producer'] ? $anime->taxonomies['producer'] : []),
    ],
];
$img  = $anime->meta;
$tags = wp_get_post_tags(get_the_ID());
ob_start();
?>
<script>
const current_post_data_id = <?php echo get_the_ID(); ?>;
</script>
<div role="heading" class="relative h-full min-h-100 pt-[70px] md:pt-[140px] pb-17">
    <div class="bg-cover bg-center opacity-30 blur-xl absolute inset-0 z-0"
        style="background-image: url('<?php echo $img && $img['background'] ? $img['background'] : $anime->image_url; ?>');">
    </div>
    <div
        class="xl:pl-16 px-4 md:px-5 w-full xl:w-9/12 h-auto flex flex-col sm:flex-row items-center justify-center sm:items-start sm:justify-start gap-10 z-10 relative sm:mb-10 lg:mb-0">
        <div class="anime-image">
            <?=$anime->image;?>
        </div>
        <div class="xl:w-full w-full sm:w-3/4 md:pr-0 lg:pr-10 text-center sm:text-left">
            <!-- breadcrumb -->
            <nav aria-label="Breadcrumb" class="text-xs font-medium mb-5 hidden lg:block">
                <ol class="flex gap-2 items-center flex-wrap">
                    <li>
                        <a href="/">
                            <?php _e('Home', 'kiranime')?>
                        </a>
                    </li>
                    <?php if ($type && $type[0]) {?>
                    <span class="material-icons-round text-lg">
                        arrow_right
                    </span>
                    <li>
                        <a class="uppercase" href="<?=get_term_link($type[0])?>">
                            <?php echo $type[0]->name ?>
                        </a>
                    </li>
                    <?php }?>
                    <span class="material-icons-round text-lg">
                        arrow_right
                    </span>
                    <li>
                        <a href="<?php the_permalink()?>" aria-current="page" class="text-accent">
                            <?php the_title()?>
                        </a>
                    </li>
                </ol>
            </nav>
            <h2 class="md:text-4xl text-xl leading-tight font-medium mb-5">
                <?php the_title();?>
            </h2>
            <ul
                class="flex items-center justify-center flex-wrap lg:flex-nowrap sm:justify-start gap-0.5 mb-7 anime-metadata">
                <?php if ($meta['rate']): $m = explode(' ', $meta['rate'])?>
					                <li>
					                    <span><?php echo $m[0] ?></span>
					                </li>
					                <?php endif;?>
                <?php if (!is_wp_error($attr)): foreach ($attr as $att): ?>
					                <li>
					                    <span>
					                        <?php echo $att->name ?>
					                    </span>
					                </li>
					                <?php endforeach;endif;?>
                <?php if ($type && $type[0]): ?>
                <li>
                    <a class="uppercase" href="<?=get_term_link($type[0])?>">
                        <?php echo $type[0]->name ?>
                    </a>
                </li>
                <?php else: ?>
                <li>
                    <a href="/anime-type/tv">
                        TV
                    </a>
                </li>
                <?php endif;if ($latest): ?>
                <li>
                    <a href="<?php echo $latest->url ?>">
                        Ep <?php echo isset($latest->meta['number']) ? $latest->meta['number'] : '' ?>
                    </a>
                </li>
                <?php endif;?>
                <li>
                    <span>
                        <?php echo $meta['duration']; ?>
                    </span>
                </li>
            </ul>
            <div class="flex items-center justify-center sm:justify-start gap-2 mb-5 relative">
                <a href="<?php if ($first_episode): echo $first_episode->url;else:the_permalink();endif?>"
                    class="flex items-center gap-1 text-sm md:text-base justify-center md:justify-start px-3 md:px-5 py-2 bg-accent-3 rounded-full">
                    <span class="material-icons-round text-xl">
                        play_arrow
                    </span>
                    <?php _e('Watch Now', 'kiranime');?>
                </a>
                <span data-anime-tippy-add-list="<?php the_ID()?>"
                    class="flex items-center cursor-pointer gap-1 text-sm md:text-base justify-center md:justify-start px-3 md:px-5 py-2 bg-gray-50 rounded-full text-primary">
                    <span class="material-icons-round text-xl">
                        add
                    </span>
                    <?php _e('Add to List', 'kiranime');?>
                </span>

            </div>
            <div class="font-light text-spec hidden sm:block">
                <div data-synopsis class="line-clamp-3 ">
                    <?php $content = strip_tags(get_the_content());?>
                    <?php echo $content; ?>
                </div>
                <?php if (strlen($content) > 280): ?>
                <span data-more-less data-ismore="true" class="font-medium cursor-pointer">+
                    <?php _e('More', 'kiranime')?></span>
                <?php endif;?>
                <div class="mt-5 flex items-center justify-start flex-wrap">
                    <?php if (!is_wp_error($tags) && !empty($tags)): $tag_len = count($tags);?>
					                    <strong>Tags :</strong> <?php foreach ($tags as $index => $tag): ?>
					                    <a class="ml-1"
					                        href="<?php echo get_tag_link($tag) ?>"><?php echo $tag->name ?></a><?php if ($index < $tag_len - 1) {echo ",";}?>
					                    <?php endforeach;endif;?>
                </div>
            </div>
            <?php if (get_theme_mod('__show_share_button', 'show') === 'show'): ?>
            <div class="w-full md:py-5 bg-opacity-100 md:block gap-5 items-end anime-share">
                <div
                    class=" md:w-6/12 text-left pl-5 before:absolute before:hidden lg:before:block before:inset-0 before:h-full before:w-0.5 before:bg-accent-3 relative py-2">
                    <span class="text-sm font-semibold md:block text-accent mb-2 hidden">
                        <?php _e('Share This Anime', 'kiranime');?>
                    </span>
                    <?php get_template_part('template-parts/sections/component/use', 'share');?>
                </div>
            </div>
            <?php endif;?>
        </div>
    </div>
    <section
        class="xl:absolute relative xl:top-0 xl:right-0 w-full py-5 xl:py-0 xl:max-w-xs bottom-0 bg-gradient-to-b from-white/20 via-white/10 to-transparent xl:w-79 space-y-1 flex flex-col justify-center text-sm font-medium px-4 md:px-7">
        <div class="leading-6 sm:hidden">
            <span class="font-semibold mr-1 block"><?php _e('Overview:', 'kiranime');?></span>
            <span
                class="block w-full max-h-24 overflow-scroll my-3 overflow-x-hidden text-xs text-gray-200"><?php echo $content ?></span>
        </div>
        <section class="max-h-full overflow-auto custom-scrollbar lg:max-h-[70%] lg:mt-8">
            <ul class="text-[13px]">
                <?php foreach ($showMeta as $dk => $dv): if ($dv): ?>
					                <li class="list-none mb-1">
					                    <span class="font-semibold mr-1  leading-6">
					                        <?php echo ucfirst(__($dk, 'kiranime')); ?>:
					                    </span>
					                    <span class=" font-normal leading-6">
					                        <?php echo is_array($dv) ? implode(', ', $dv) : $dv; ?>
					                    </span>
					                </li>
					                <?php endif;endforeach;?>
                <?php foreach ($taxs as $tk => $tv): if (!empty($tv['data'])): ?>
					                <li
					                    class="list-none <?php if ('genre' === $tk) {echo 'py-2 border-y border-white border-opacity-10';}?>">
					                    <span class="font-semibold mr-1 leading-6"><?php echo ucfirst($tv['name']); ?>:</span>
					                    <span class="leading-6">
					                        <?php if ('genre' === $tk): foreach ($tv['data'] as $ka => $kv): ?>
										                        <a href="<?=$kv['url']?>"
										                            class="inline-block rounded-md border border-white hover:border-accent-2 hover:text-accent px-2 my-[1.5px] transition-colors duration-200 ease-in-out border-opacity-30 hover:border-opacity-100">
										                            <?php echo $kv['name'] ?>
										                        </a>
										                        <?php endforeach;elseif (in_array($tk, ['studio', 'producer'])): $sc = count($tv['data']);foreach ($tv['data'] as $st => $sv): ?>
					                        <a href="<?=$sv['url']?>"
					                            class="font-normal leading-6 hover:text-accent transition-colors duration-200 ease-in-out ">
					                            <?php echo $sv['name'] ?>
					                        </a><?php echo $st < $sc - 1 ? ", " : "" ?>
					                        <?php endforeach;else: ?>
                        <span class="font-normal leading-6">
                            <?php echo trim(implode(", ", array_map(function ($av) {return $av['name'];}, $tv['data']))) ?>
                        </span>
                        <?php endif;?>
                    </span>
                </li>
                <?php endif;endforeach;?>
            </ul>
        </section>
    </section>
</div>
<div class="lg:flex gap-10 space-y-5 lg:space-y-0 lg:px-10 px-4 md:px-5 lg:py-10 py-5">
    <section class="flex-auto lg:w-9/12 w-full">
        <!-- episode list start -->
        <?php if (get_theme_mod('__show_episode', 'show') === 'show'): get_template_part('template-parts/sections/anime/episode', 'use-slider', ['anime' => $anime]);endif;?>
        <!-- episode list end -->
        <section>
            <!-- start download -->
            <?php if (get_theme_mod('__show_download_anime', 'show') === 'show'): get_template_part('template-parts/sections/component/use', 'download', ['downloads' => $anime->meta['download']]);endif;?>
            <!-- end download -->
        </section>
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
        <?php if (get_theme_mod('__show_related_anime', 'show') === 'show'): get_template_part('template-parts/sections/component/use', 'recommended', ['style' => get_theme_mod('__show_related_anime_display', 'grid'), 'related' => get_theme_mod('__show_related_anime_by', 'genre'), 'total' => get_theme_mod('__show_related_anime_count', 12), 'title' => get_theme_mod('__show_related_anime_label', __('Recomended For You!', 'kiranime')), 'id' => get_the_ID()]);endif;?>
    </section>

    <!-- start second sidebar -->
    <aside class="w-full lg:w-3/12 flex-shrink-0 min-h-300 p-4 px-0 mt-5 lg:mt-0">
        <?php if (is_active_sidebar('anime-info-sidebar')): dynamic_sidebar('anime-info-sidebar');endif;?>
    </aside>
    <!-- end second sidebar -->
</div>
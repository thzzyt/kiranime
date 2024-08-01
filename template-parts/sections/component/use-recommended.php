<?php

$style      = $args['style'];
$related_by = $args['related'];
$total      = intval($args['total']);
$id         = intval($args['id']);
$taxs       = wp_get_post_terms($id, $related_by);

$query = new Kira_Query([
    'tax_query'      => [
        'relation' => 'AND',
        [
            'taxonomy' => $related_by,
            'terms'    => array_map(function ($val) {
                return $val->term_id;
            }, $taxs),
            'field'    => 'term_id',
        ],
    ],
    'posts_per_page' => $total + 1,
    'orderby'        => 'rand',
]);

$animes = $query->animes;
$animes = array_filter($animes, function ($val) use ($id) {
    return isset($val->anime_id) && !in_array($val->anime_id, [$id]);
});
if (count($animes) > $total) {
    array_pop($animes);
}

?>
<section>
    <div class="w-full mb-4 flex items-center justify-between mt-10 px-0">
        <div class="mr-4 flex justify-between items-center w-full">
            <h2 class="text-xl md:text-2xl md:leading-10 font-semibold p-0 m-0 text-accent">
                <?php if ($title) {echo $title;} else {_e('Recomended For You!', 'kiranime');}?>
            </h2>
            <?php if ('slider' === $style): ?>
            <div
                class="swiper-navigation navigate-sections navigate-recommended flex items-center justify-between gap-1 w-max">
                <div
                    class="navigate-recommended-prev cursor-pointer group py-1 px-2 rounded-l hover:bg-accent-2 hover:text-text-color">
                    <svg class="w-5 h-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </div>
                <div
                    class="navigate-recommended-next cursor-pointer group py-1 px-2 rounded-r hover:bg-accent-2 hover:text-text-color">
                    <svg class="w-5 h-5 xl:w-6 xl:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
            <?php endif;?>
        </div>
    </div>
    <?php if ('slider' !== $style): ?>
    <section class="grid grid-anime-auto gap-2 sm:gap-4 justify-evenly w-full flex-auto">
        <?php get_template_part('template-parts/sections/listing/use', 'grid', ['animes' => $animes]);?>
    </section>
    <?php else: ?>
    <section class="w-full flex-auto">
        <div data-current-slider="recommended" data-is-loop="0" class="swiper swiper-sections swiper-recommended">
            <div class="swiper-wrapper" style="min-width: 100vw;">
                <!-- Slides -->
                <?php get_template_part('template-parts/sections/listing/use', 'slider', ['animes' => $animes]);?>
            </div>
        </div>
    </section>
    <?php endif;?>
</section>
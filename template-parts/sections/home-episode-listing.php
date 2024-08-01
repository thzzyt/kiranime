<?php
$title     = $args['title'];
$query     = new Kira_Query($args['query'], 'episode');
$slider    = $args['slider'];
$link      = $args['archive'];
$loop      = $args['loop'];
$type      = str_replace(' ', '-', strtolower($title));
$is_widget = isset($args['is_widget']) ? $args['is_widget'] : false;
?>

<?php if (!$query->empty): ?>
<section class="mb-6 mt-5 md:mt-0">
    <div class="w-full mb-4 flex md:items-center justify-between flex-col md:flex-row px-2 sm:px-0">
        <div class="mr-4">
            <h2 class="text-lg lg:text-xl xl:text-2xl leading-10 font-semibold p-0 m-0 text-accent">
                <?=$title?>
            </h2>
        </div>
        <div
            class="text-sm font-normal text-opacity-75 <?php if ($slider): echo 'flex items-center justify-between  gap-5';endif;?>">
            <?php if ($slider): ?>
            <div
                class="swiper-navigation navigate-section navigate-<?php echo $type; ?> flex items-center justify-between gap-1 w-max">
                <div
                    class="navigate-<?php echo $type; ?>-prev flex items-center justify-center cursor-pointer group py-1 px-2 rounded-l hover:bg-accent-2 hover:text-text-color">
                    <span class="material-icons-round text-xl lg:text-2xl">
                        navigate_before
                    </span>
                </div>
                <div
                    class="navigate-<?php echo $type; ?>-next flex items-center cursor-pointer group py-1 px-2 rounded-r hover:bg-accent-2 hover:text-text-color">
                    <span class="material-icons-round text-xl lg:text-2xl">
                        navigate_next
                    </span>

                </div>
            </div>
            <?php endif;?>
            <a class="flex items-center gap-1 text-xs" href="<?=$link?>">
                <?php _e('View More', 'kiranime');?>
                <span class="material-icons-round text-xl">
                    navigate_next
                </span>
            </a>
        </div>
    </div>
    <?php if (!$slider): ?>
    <section class="grid grid-episode-auto gap-2 sm:gap-4 justify-evenly mx-auto px-2 md:px-0 w-full flex-auto">
        <?php get_template_part('template-parts/sections/listing/use', 'episode-grid', [
    'episodes' => $query->episodes,
]);?>
    </section>
    <?php else: ?>
    <section class="mx-auto w-full flex-auto px-2 md:px-0 ">
        <div data-current-slider="<?php echo $type ?>" data-episode-slider="1"
            data-is-loop="<?php echo $loop ? '1' : '0'; ?>" class="swiper swiper-sections swiper-<?php echo $type; ?>">
            <div class="swiper-wrapper" style="min-width: 100vw;">
                <!-- Slides -->
                <?php get_template_part('template-parts/sections/listing/use', 'episode-slider', [
    'episodes' => $query->episodes,
]);?>
            </div>
        </div>
    </section>
    <?php endif;?>
</section>
<?php endif;?>
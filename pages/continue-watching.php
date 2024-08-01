<?php

/**
 * Template Name: Continue Watching (history)
 *
 * @package Kiranime
 */
// Kiranime_Init::custom_queue('history');
get_header('single');?>
<?php get_template_part('template-parts/sections/component/use', 'user-heading');?>

<section class="lg:w-3/4 w-full mx-auto">
    <h2 class="text-2xl px-5 lg:px-0 leading-10 font-medium mb-5 flex items-center gap-4">
        <span class="material-icons-round text-3xl">
            restore
        </span>
        <?php the_title()?>
    </h2>
    <div id="continue-watching" class="my-5"></div>
</section>

<?php get_footer();?>
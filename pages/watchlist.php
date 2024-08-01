<?php

/**
 * Template Name: User Watch List Page
 *
 * @package Kiranime
 */
if (!is_user_logged_in()) {
    wp_redirect(home_url('/'));
    exit();
}
get_header('single');

$scripts = 'let animewatchlistparam = "all";';
?>
<?php get_template_part('template-parts/sections/component/use', 'user-heading');?>
<section class="w-full lg:w-3/4 mx-auto min-h-[75svh]">
    <h2 class="text-2xl px-5 lg:px-0 leading-10 font-medium mb-5 flex items-center gap-4">
        <span class="material-icons-round text-3xl">
            favorite
        </span>

        <?php the_title()?>
    </h2>
    <script>
    <?php echo $scripts; ?>
    </script>
    <div id="watchlist-content" class="my-5"></div>
</section>

<?php get_footer()?>
<!-- Spotlight Start -->
<?php if (get_theme_mod('__show_spotlight', 'show') === 'show'):
    get_template_part('template-parts/sections/home', 'spotlights');
endif;?>
<!-- Spotlight End -->
<!-- Trending Start -->
<?php if (get_theme_mod('__show_trending', 'show') === 'show'):
    get_template_part('template-parts/sections/home', 'trendings');
endif;?>
<!-- Trending End -->
<!-- Share button if enabled -->
<?php if (get_theme_mod('__show_share_button', 'show') === 'show'): ?>
<div class="w-full lg:px-5 py-5 bg-secondary bg-opacity-100 hidden md:flex gap-5 items-end">
    <div
        class=" w-max text-left pl-5 before:absolute before:hidden lg:before:block before:inset-0 before:h-full before:w-0.5 before:bg-accent-3 relative">
        <span class="text-sm font-semibold block text-accent">
            <?php printf(__('Share %1$s', 'kiranime'), get_bloginfo('name'));?>
        </span>
        <span class="block text-xs font-light">
            <?php _e('to your friends!', 'kiranime');?>
        </span>
    </div>
    <?php get_template_part('template-parts/sections/component/use', 'share');?>
</div>
<?php endif;?>
<!-- End share button -->
<!-- Start Featured lists -->
<?php if (get_theme_mod('__show_featured_list', 'show') === 'show'): ?>
<section class="px-0 sm:px-4 lg:px-3 kira-grid-featured justify-around gap-y-5 w-full gap-x-4 my-2 sm:my-5 lg:my-10">
    <?php Render::featured();?>
</section>
<?php endif;?>
<!-- listing and sidebar -->
<section class="lg:flex justify-between md:px-5 gap-5 sm:px-4">

    <!-- main listing -->
    <section class="<?php echo get_theme_mod('__show_sidebar', 'show') === 'show' ? 'lg:w-3/4' : 'w-full' ?>">
        <?php dynamic_sidebar('homepage-main-list')?>
    </section>
    <!-- end main listing -->
    <?php if (get_theme_mod('__show_sidebar', 'show') === 'show'): ?>
    <!-- main sidebar -->
    <aside class="lg:w-1/4 px-2">
        <?php dynamic_sidebar('homepage-sidebar')?>
    </aside>
    <!-- end sidebar -->
    <?php endif;?>
</section>
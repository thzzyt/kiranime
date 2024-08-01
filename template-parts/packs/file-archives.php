<?php
$sidebar   = is_active_sidebar('archive-sidebar');
$data      = $args['data'];
$max_pages = $data->pages;
?>

<?php if (get_theme_mod('__show_share_button', 'show') === 'show'): ?>
<div class="mt-17 inline-block mb-5 bg-darkest w-full">
    <div class="px-4">
        <div class="py-5 px-0 relative flex items-center gap-5">
            <div class="block text-xs pl-5 py-1 relative border-l-2 border-accent">
                <span
                    class="text-sm font-semibold text-accent"><?php printf(esc_html__('Share %1$s', 'kiranime'), get_bloginfo('name'));?></span>
                <p class="mb-0"><?php _e('to your friends!', 'kiranime');?></p>
            </div>
            <?php get_template_part('template-parts/sections/component/use', 'share');?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="mt-17"></div>
<?php endif;?>
<section class="mb-17 grid grid-cols-12 px-5 mx-auto w-full gap-5">
    <section class="col-span-full <?php if ($sidebar) {echo 'lg:col-span-9';}?>">
        <h3 class="mb-4 text-2xl font-semibold leading-10 text-accent"><?php echo $args['title'] ?></h3>
        <section class="grid grid-anime-auto-archive gap-2 sm:gap-4 justify-evenly w-11/12 mx-auto md:w-full flex-auto">
            <?php get_template_part('template-parts/sections/listing/use', 'grid', ['animes' => $data->animes])?>
        </section>
        <?php
echo paginate_links(['base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
    'total'                     => $max_pages,
    'current'                   => max(1, get_query_var('paged')),
    'format'                    => '?paged=%#%',
    'show_all'                  => false,
    'type'                      => 'list',
    'end_size'                  => 2,
    'mid_size'                  => 1,
    'prev_next'                 => false,
    'add_args'                  => false,
    'add_fragment'              => '']);
?>
    </section>
    <?php if ($sidebar): ?>
    <aside class="col-span-full lg:col-span-3 ">
        <?php dynamic_sidebar('archive-sidebar');?>
    </aside>
    <?php endif;?>
</section>
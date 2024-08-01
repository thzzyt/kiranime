<?php

$sidebar_active = is_active_sidebar('archive-sidebar');
$current        = get_queried_object();
$id             = get_queried_object_id();
$page           = (get_query_var('paged')) ? get_query_var('paged') : 1;
get_header('single');
$tax_name = 'post_tag';
$animes   = new Kira_Query(['paged' => $page, 'posts_per_page' => get_theme_mod('__archive_count', 20), 'tag_id' => $id]);
?>
		<?php if (get_theme_mod('__show_share_button', 'show') === 'show'): ?>
		<style>
		:root {
		    --justify-space: <?php echo $animes->count > 7 ? 'space-around' : 'flex-start';
?>;
		}
		</style>
		<div class="mt-17 inline-block mb-5 bg-darkest w-full">
		    <div class="px-4">
		        <div class="py-5 px-0 relative flex items-center gap-5">
		            <div class="block text-xs pl-5 py-1 relative border-l-2 border-accent">
		                <span class="text-sm font-semibold text-accent">
		                    <?php printf(esc_html__('Share %1$s', 'kiranime'), get_bloginfo('name'))?>
		                </span>
		                <p class="mb-0"><?php _e('to your friends', 'kiranime');?></p>
		            </div>
		            <?php get_template_part('template-parts/sections/component/use', 'share');?>
		        </div>
		    </div>
		</div>
		<?php else: ?>
<div class="mt-17"></div>
<?php endif;?>
<div class="mb-17 lg:flex items-start justify-between px-4 mx-auto w-full gap-5">
    <section class="w-full <?php if ($sidebar_active) {echo 'main-width';} else {echo 'lg:px-12';}?>">
        <h3 class="mb-4 text-2xl font-semibold leading-10 text-accent">
            <?php echo ucfirst($current->name) ?>
            <?php _e('Animes', 'kiranime')?>
        </h3>
        <div class="grid grid-anime-auto gap-4">
            <?php if ($animes->animes): get_template_part('template-parts/sections/listing/use', 'grid', ['animes' => $animes->animes]);endif;?>
        </div>
        <?php
echo paginate_links(array(
    'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
    'total'        => $animes->pages,
    'current'      => max(1, get_query_var('paged')),
    'format'       => '?paged=%#%',
    'show_all'     => false,
    'type'         => 'list',
    'end_size'     => 2,
    'mid_size'     => 1,
    'prev_next'    => false,
    'add_args'     => false,
    'add_fragment' => '',
));
?>
    </section>
    <?php if ($sidebar_active): ?>
    <aside class="w-full sidebar-width p-4 mt-5 lg:mt-0">
        <?php dynamic_sidebar('archive-sidebar');?>
    </aside>
    <?php endif;?>
</div>

<?php
get_footer();
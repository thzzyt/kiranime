<?php

/**
 * Template Name: A-Z List
 *
 * @package Kiranime
 */

$letter = isset($_GET['letter']) ? $_GET['letter'] : 'All';
$page   = (get_query_var('paged')) ? get_query_var('paged') : 1;

get_header('single');

$args = ['paged' => $page, 'posts_per_page' => get_theme_mod('__archive_count', 20)];
if ('0-9' == $letter):
    $args['search_title'] = "^[0-9]";
elseif ('other' == $letter):
    $args['search_title'] = "^[^a-zA-Z0-9]";
elseif ('All' != $letter):
    $args['search_title'] = "^[" . strtolower($letter) . $letter . "]";
endif;
$query = new Kira_Query($args, 'anime', true);

/**
 * get animes
 */
$query->get_regex_result();
$animes   = $query->animes;
$alphabet = ['All', '#', '0-9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
?>
		<section class="mt-17 w-11/12 mx-auto">
		    <!-- breadcrumb -->
		    <nav aria-label="Breadcrumb" class="text-xs font-medium mb-5">
		        <ol class="flex gap-2 items-center flex-wrap">
		            <li>
		                <a href="/">
		                    <?php _e('Home', 'kiranime');?>
		                </a>
		            </li>
		            <li>
		                <div class="w-1 h-1 bg-gray-500 rounded-full"></div>
		            </li>
		            <li>
		                <a href="?" class="text-accent-2">
		                    <?php _e('A-Z List', 'kiranime');?>
		                </a>
		            </li>
		        </ol>
		    </nav>
		</section>
		<section class="mx-auto w-11/12">
		    <h2 class="font-semibold text-2xl leading-10 mb-4 text-accent-3">
		        <?php _e('Sort By Letters', 'kiranime');?>
		    </h2>
		    <div class="py-2 mb-5 h-auto font-montserrat">
		        <ul class="w-full grid grid-cols-6 md:grid-cols-9 lg:flex gap-x-1 flex-wrap gap-y-4">
		            <?php foreach ($alphabet as $alpha): $url = Kira_Utility::page_link('pages/az-list.php');?>
									            <?php if ('#' != $alpha): ?>
									            <li>
									                <div class="px-4 py-2 w-full inline-block text-center col-span-1 leading-5 font-light rounded-sm shadow cursor-pointer <?php echo $letter == $alpha ? 'bg-accent-3' : 'bg-secondary'; ?>"
									                    style="font-size: 0.925rem" data-alphabet-link="<?php echo $url . '?letter=' . $alpha; ?>">
									                    <?php echo $alpha; ?></div>
									            </li>
									            <?php else: ?>
		            <li>
		                <div class="px-4 py-2 w-full inline-block text-center col-span-1 leading-5 font-light rounded-sm shadow cursor-pointer <?php echo $letter == 'other' ? 'bg-accent-3' : 'bg-secondary'; ?>"
		                    style="font-size: 0.925rem" data-alphabet-link="<?php echo $url . '?letter=other'; ?>">
		                    <?php echo $alpha; ?></div>
		            </li>
		            <?php endif;endforeach;?>
        </ul>
    </div>
    <div style="justify-content: <?php echo $query->count > 7 ? 'space-between' : 'flex-start'; ?>;" class="kira-grid">
        <?php if (!$query->empty): get_template_part('template-parts/sections/listing/use', 'grid', ['animes' => $animes]);else:_e('No Anime found.', 'kiranime');endif;?>
    </div>
</section>
<?php
echo paginate_links(array(
    'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
    'total'        => $query->pages,
    'current'      => max(1, get_query_var('paged')),
    'format'       => '?paged=%#%',
    'show_all'     => false,
    'type'         => 'list',
    'end_size'     => 2,
    'mid_size'     => 1,
    'prev_next'    => false,
    'add_args'     => false,
    'add_fragment' => '',
)); ?>
<div class="mb-17">
</div>
<?php
get_footer();
?>
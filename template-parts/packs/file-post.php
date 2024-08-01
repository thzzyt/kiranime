<div class="lg:px-10 md:px-5 py-10 w-full lg:flex gap-5">
    <main class="w-full <?php if (is_active_sidebar('article-sidebar')): echo 'lg-w-3/4';endif;?>  container">
        <section class="lg:mt-17 mt-10">
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
                        <a href="<?=Kira_Utility::page_link('pages/news.php');?>">
                            <?php _e('News', 'kiranime');?>
                        </a>
                    </li>
                    <li>
                        <div class="w-1 h-1 bg-gray-500 rounded-full"></div>
                    </li>
                    <li>
                        <a href="<?php the_permalink()?>" class="text-accent-2">
                            <?php the_title()?>
                        </a>
                    </li>
                </ol>
            </nav>
        </section>
        <article class="w-full relative">
            <?php
$featured = get_the_post_thumbnail_url(get_the_ID());
?>
            <div class="w-full h-fit relative <?php if ($featured) {echo 'pb-80 bg-cover bg-no-repeat rounded-lg';}?> mb-5 after:absolute after:bg-black after:z-0 after:bg-opacity-30 after:inset-0"
                style="background-image: url('<?=$featured;?>');">
                <div class=" p-4 z-10 font-semibold absolute left-0 bottom-0 pb-5">
                    <h2 class="text-2xl after:w-full after:px-4 after:h-[2px] after:bg-accent after:block after:my-2">
                        <?php the_title()?></h2>
                    <div class="flex items-center gap-4">

                        <span class="flex items-center gap-2 font-medium text-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php printf(esc_html__('Published %1$s ago.', 'kiranime'), human_time_diff(get_the_time('U')));?>
                        </span>
                        <span class="flex items-center gap-2 font-medium text-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <?php $author = get_the_author();
echo $author;?>
                        </span>
                    </div>
                </div>
            </div>

            <main class="text-sm main-content px-2 shadow-md shadow-primary">
                <?php the_content();?>
            </main>
        </article>
        <?php if (get_theme_mod('__show_share_button', 'show') === 'show'): ?>
        <div class=" w-full pt-5 relative flex gap-5 items-end">
            <div
                class="w-6/12 lg:w-2/12 pl-5 before:absolute before:hidden lg:before:block before:inset-0 before:h-full before:w-0.5 before:bg-accent-3 relative">
                <span class="text-sm font-semibold block">
                    <?php _e('Share', 'kiranime');?>
                </span>
                <span class="block text-xs font-light">
                    <?php _e('to your friends!', 'kiranime');?>
                </span>
            </div>
            <?php get_template_part('template-parts/sections/component/use', 'share');?>
        </div>
        <?php endif;?>
        <div class="my-10">
            <?php
// If comments are open or we have at least one comment, load up the comment template.
if (comments_open() || get_comments_number()):
    comments_template();
endif;
?>
        </div>
    </main>
    <?php if (is_active_sidebar('article-sidebar')): ?>
    <aside class="w-full lg:w-1/4 mt-10 lg:mt-0 px-2 lg:px-0">
        <?php dynamic_sidebar('article-sidebar');?>
    </aside>
    <?php endif;?>
</div>
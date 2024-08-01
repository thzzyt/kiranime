<?php get_header();?>

<div
    class="container md:max-w-5xl lg:max-w-6xl xl:max-w-[100rem] mt-20 mb-8 mx-auto bg-tertiary/50 py-4 rounded-lg shadow-lg drop-shadow-lg">

    <?php if (have_posts()): ?>

    <?php
while (have_posts()):
    the_post();
    ?>

	    <?php get_template_part('template-parts/content', 'single');?>

	    <?php
    // If comments are open or we have at least one comment, load up the comment template.
    if (comments_open() || get_comments_number()):
        comments_template();
    endif;
    ?>

	    <?php endwhile;?>

    <?php endif;?>

</div>

<?php
get_footer();
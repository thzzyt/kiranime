<article id="post-<?php the_ID();?>" <?php post_class();?>>
    <style>
    .entry-content {
        font-size: 0.875rem !important;
    }

    .entry-content a {
        --tw-text-opacity: 1;
        color: rgba(var(--accent-color), var(--tw-text-opacity)) !important;
        text-decoration: none !important;
    }
    </style>
    <header class="entry-header mb-4">
        <?php the_title(sprintf('<h1 class="entry-title text-xl lg:text-2xl font-extrabold leading-tight mb-1"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h1>');?>
        <time datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished"
            class="text-sm text-accent"><?php echo get_the_date(); ?></time>
    </header>

    <div class="entry-content">
        <?php the_content();?>

        <?php
wp_link_pages(
    array(
        'before'      => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'kiranime') . '</span>',
        'after'       => '</div>',
        'link_before' => '<span>',
        'link_after'  => '</span>',
        'pagelink'    => '<span class="screen-reader-text">' . __('Page', 'kiranime') . ' </span>%',
        'separator'   => '<span class="screen-reader-text">, </span>',
    )
);
?>
    </div>

</article>
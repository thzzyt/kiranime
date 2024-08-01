<?php

get_header('single');
if (have_posts()): the_post();
    $post_type = get_post_type(get_the_ID());
    $template  = 'file-' . $post_type;
    get_template_part('template-parts/packs/file', $post_type);
endif;
get_footer();

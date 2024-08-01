</main>
<?php $footer_image = get_theme_mod('__footer_image')?>
<footer class="px-5 max-w-full relative z-39 bg-cover after:absolute after:w-full after:inset-0 after:bg-gradient-to-tr after:from-primary after:to-primary/70"
    style="background: <?php if ($footer_image) {echo 'url(' . $footer_image . ') center center no-repeat';}?> #2a2c31; ">
    <div class="py-8 relative z-40">
        <div
            class="sm:mb-5 pb-5 sm:border-b border-gray-400 border-opacity-40 sm:max-w-max sm:mx-auto lg:mx-0 lg:justify-start flex flex-col sm:flex-row items-center  w-full">
            <a href="/" id="logo" class="block sm:inline-block sm:mr-10 mb-5 sm:mb-0">
                <?php if (get_theme_mod('custom_logo')) {
    ?>
                <img style="width: 150px;" src="<?php $image = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
    echo $image['0']?>" alt="<?php echo get_bloginfo('name') ?>">
                <?php } else {
    echo get_bloginfo('name');
}?>
            </a>
        </div>
        <div class="mb-3 hidden sm:block sm:text-center lg:text-left">
            <div class="block mb-3">
                <span
                    class="inline-block pr-5 mr-5 border-r border-gray-400 border-opacity-40 leading-4 text-xl font-semibold"><?php _e('A-Z LIST', 'kiranime')?></span>
                <span class="text-xs"><?php _e('Searching anime order by alphabet name A to Z.', 'kiranime');?></span>
            </div>
            <ul class="mt-2 m-0 p-0 list-none">
                <?php
$alphabet  = ['All', '#', '0-9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
$page_link = Kira_Utility::page_link('pages/az-list.php');
foreach ($alphabet as $key => $value) {
    if (0 == $key) {
        echo '<li class="mr-3 mb-3 inline-block"><div class="text-sm py-1 px-2 bg-opacity-4 hover:bg-accent-3 bg-secondary rounded-sm cursor-pointer" data-alphabet-link="' . $page_link . '" rel="nofollow noopener noreferrer ">All</div></li>';
    } elseif (1 == $key) {
        echo '<li class="mr-3 mb-3 inline-block"><div class="text-sm py-1 px-2 bg-opacity-4 hover:bg-accent-3 bg-secondary rounded-sm cursor-pointer" data-alphabet-link="' . $page_link . '?letter=other" rel="nofollow noopener noreferrer ">#</div></li>';
    } else {
        echo '<li class="mr-3 mb-3 inline-block"><div class="text-sm py-1 px-2 bg-opacity-4 hover:bg-accent-3 bg-secondary rounded-sm cursor-pointer" data-alphabet-link="' . $page_link . '?letter=' . $value . '" rel="nofollow noopener noreferrer ">' . $value . '</div></li>';
    }
}
?>
            </ul>
        </div>
        <?php if (has_nav_menu('footer')): ?>
        <div class="flex items-center gap-5 mb-3 justify-center md:justify-start">
            <?php wp_nav_menu([
    'theme_location'  => 'footer',
    'container_class' => 'w-full',
    'menu_class'      => 'flex text-sm p-0 m-0 items-center gap-2 flex-wrap lg:flex-start justify-center lg:justify-start',
])?>
        </div>
        <?php endif;?>
        <div class="text-xs  text-text-color text-opacity-80 text-center md:text-left">
            <?php printf(esc_html__('%1$s does not store any files on our server, we only linked to the media which is hosted on 3rd party services.', 'kiranime'), get_bloginfo('name'));?>
        </div>
        <p data-site-name class="text-xs  text-text-color text-opacity-80 mb-3 text-center md:text-left">©
            <?php echo get_bloginfo('name') ?>
        </p>
    </div>
</footer>
<?php wp_footer();?>
</body>

</html>
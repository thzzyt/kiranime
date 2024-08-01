<?php

/**
 * Template Name: User notification
 *
 * @package Kiranime
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/'));
    exit();
}
$notifications = Kiranime_Notification::get();

get_header('single');?>
<?php get_template_part('template-parts/sections/component/use', 'user-heading');?>

<section class="lg:w-9/12 w-full mx-auto">
    <h2 class="text-2xl px-5 lg:px-0 leading-10 font-medium mb-5 flex items-center gap-4">
        <span class="material-icons-round text-3xl">
            notifications
        </span>
        <?php the_title()?>
    </h2>
    <div id="notification" class="my-5 max-h-[400px] overflow-y-scroll overflow-x-hidden">
        <?php if ($notifications): ?>
        <div>
            <?php foreach ($notifications as $notif): ?>
            <div class="relative pl-32 w-full p-5 bg-overlay mb-2 bg-opacity-75 group">
                <div
                    class="bg-darker bg-opacity-20 absolute inset-0 group-hover:bg-opacity-0 group-hover:pointer-events-none z-10">

                </div>
                <a href="<?php echo $notif['url'] ?>" class="w-28 absolute inset-y-0 left-0 overflow-hidden">
                    <img src="<?=$notif['featured']?>" class="absolute inset-0 " alt="<?php echo $notif['title'] ?>">
                </a>
                <div class="text-xs mb-1"><?php echo $notif['published'] ?></div>
                <div class="mb-5 font-medium text-xl leading-normal">
                    <a href="<?php echo $notif['url'] ?>">
                        <span class="text-accent">
                            <?php echo $notif['title'] ?>
                        </span> -
                        <?php printf(esc_html__('Episode %1$s Available NOW!', 'kiranime'), $notif['number'])?></a>
                </div>
                <div class="text-sm">
                    <?php
$link = '<a href="' . $notif['url'] . '">' . __('here', 'kiranime') . '</a>';
printf(esc_html__('Click %1$s to watch it now.', 'kiranime'), $link);
?>
                </div>
            </div>
            <?php endforeach;?>
        </div>
        <?php endif;?>
    </div>
</section>

<?php get_footer();?>
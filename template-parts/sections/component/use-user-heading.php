<?php

$user_info    = get_userdata(get_current_user_id());
$display_name = esc_attr($user_info->display_name);
$menus        = Kira_Utility::get_user_menu();
?>
<section class="bg-overlay relative pt-17 mt-5 mb-5">
    <div class="absolute inset-0 bg-cover blur-xl opacity-30 bg-center z-0"
        style="background-image: url(<?=Kira_User::get_avatar(get_current_user_id())?>);"></div>
    <div class="container relative z-10">
        <h1 class="w-full hidden lg:block text-3xl font-medium text-center mb-4 leading-relaxed">
            <?php echo $display_name; ?></h1>
        <ul class="flex items-center justify-center gap-4 h-12">
            <?php foreach ($menus as $menu): if ($menu['link']): ?>
            <li class="w-max h-auto">
                <a href="<?=$menu['link']?>"
                    class="px-4 py-3 flex items-center gap-2  <?php if (get_bloginfo('url') . $_SERVER['REQUEST_URI'] == $menu['link']) {echo 'text-accent border-b-2 border-accent';}?>"
                    title="<?php echo $menu['name'] ?>">
                    <span class="material-icons-round text-xl">
                        <?php echo $menu['icon'] ?>
                    </span>
                    <span class="hidden lg:inline-block">
                        <?php echo $menu['name']; ?>
                    </span>
                </a>
            </li>
            <?php endif;endforeach;?>
        </ul>
    </div>
</section>
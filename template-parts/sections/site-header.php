<?php
$uid    = get_current_user_id();
$uif    = get_userdata($uid);
$nid    = Kiranime_Notification::get();
$unread = array_filter($nid, function ($val) {
    return !$val['status'];
});
?>
<style>
:root {
    --completed-status: <?php echo get_option('__u_status_bg', get_theme_mod('error-color', '#f43f5e'));
    ?>;
}
</style>
<div class="pl-4 sm:px-5 md:px-4 relative w-full max-w-screen flex items-center justify-between h-full">
    <div class="relative w-full flex items-center h-full">
        <div data-sidebar-trigger class="cursor-pointer flex items-center">
            <span class="material-icons-round text-2xl lg:text-3xl">
                menu
            </span>
        </div>
        <a href="/" id="logo" class="mr-4 md:mr-8 ml-4 md:ml-8 block">
            <?php if (get_theme_mod('custom_logo')) {
    ?>
            <img class="w-28 min-w-[7rem] md:w-[150px]" src="<?php $image = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
    echo $image['0']?>" alt="<?php echo get_bloginfo('name') ?>">
            <?php } else {
    echo get_bloginfo('name');
}?>
        </a>
        <div id="search" class="hidden lg:block lg:w-full lg:max-w-md">
            <div class="search-content relative">
                <?php $advanced_search_url = Kira_Utility::page_slug('pages/search.php');?>
                <form action="/<?=$advanced_search_url?>" method="GET" autocomplete="off" class="relative">
                    <a href="/<?=$advanced_search_url?>"
                        class="absolute top-2 right-2 text-xs bg-primary bg-opacity-80 px-2 py-1 rounded-sm"><?php _e('Filter', 'kiranime')?></a>

                    <input type="text" data-search-ajax-input
                        class="bg-white rounded text-gray-800 font-medium placeholder:font-normal h-10 leading-normal m-0 overflow-visible py-2 pr-20 pl-4 transition-all duration-150 ease-in-out focus:shadow-md w-full focus:outline-none text-[13px] peer"
                        name="s_keyword" placeholder="<?php _e('Search anime...', 'kiranime');?>">
                    <button type="submit"
                        class="absolute top-1 right-16 px-2 py-1 w-max text-primary hidden peer-focus:block">
                        <span class="material-icons-round text-2xl">
                            search
                        </span>
                    </button>
                </form>
                <div class="bg-tertiary shadow-lg absolute top-10 inset-x-0 z-10 list-none hidden"
                    data-search-ajax-result>
                    <div class="loading-relative" id="search-loading" style="display: none;">
                        <div class="loading">
                            <div class="span1"></div>
                            <div class="span2"></div>
                            <div class="span3"></div>
                        </div>
                    </div>
                    <div data-search-result-area class="max-h-96 overflow-y-scroll overflow-x-hidden">

                    </div>
                    <a data-search-view-all href="/<?=$advanced_search_url?>"
                        class="flex items-center justify-center w-full p-4 text-[13px] font-medium bg-accent-3">
                        <?php _e('View all results', 'kiranime');?>
                        <span class="material-icons-round text-lg ml-2">
                            east
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <div class="ml-8 hidden lg:flex items-center gap-2">
            <?php if (get_theme_mod('__show_social_link', 'show') === 'show'):
    foreach (Kira_Utility::social() as $key => $val): if ($val['link']): ?>
            <div>
                <a href="<?php echo $val['link'] ?>" class="w-10 h-10 flex items-center justify-center rounded-sm"
                    target="_blank" style="background: <?=$val['color'];?>;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="<?=$val['vbox']?>">
                        <path fill="currentColor" d="<?=$val['icon']?>" />
                    </svg>
                </a>
            </div>
            <?php endif;endforeach;endif;?>
        </div>
    </div>
    <div class="w-full flex items-center justify-start pr-4 lg:w-fit min-w-max lg:gap-4 gap-1">
        <div class="w-1/3 flex items-center justify-end lg:hidden">
            <button data-mobile-search-trigger class="flex items-center">
                <span class="material-icons-round text-xl">
                    search
                </span>
            </button>
        </div>
        <div title="<?php _e('I\'m feeling lucky!', 'kiranime');?>" class="w-1/3">
            <div id="random-anime"
                class="relative leading-10 cursor-pointer rounded-full flex items-center justify-center lg:bg-white lg:bg-opacity-10 lg:w-10 lg:h-10">
                <span class="material-icons-round text-xl lg:text-[20px]">
                    shuffle
                </span>
            </div>
        </div>

        <?php if (!is_user_logged_in()) {?>
        <span data-login-toggle
            class="block rounded-sm bg-accent-3 md:px-4 md:py-2 lg:min-h-[2.5rem] px-2 py-1 cursor-pointer"><?php _e('Login', 'kiranime');?></span>
        <?php }?>

        <?php if (is_user_logged_in()): ?>
        <div class="w-max flex items-center justify-end lg:justify-start gap-3 md:gap-3 lg:gap-5 pr-4">
            <div class="relative w-max">
                <div id="history-active"
                    class="lg:w-10 lg:h-10 relative leading-10 cursor-pointer rounded-full bg-opacity-0 lg:bg-opacity-10 bg-white flex items-center justify-center">
                    <span class="material-icons-round text-xl lg:text-2xl">
                        history
                    </span>
                </div>
            </div>
            <div class="relative w-max">
                <div data-dropdown-notification-trigger
                    class="lg:w-10 lg:h-10 relative leading-10 cursor-pointer rounded-full  bg-opacity-0 lg:bg-opacity-10 bg-white flex items-center justify-center">
                    <?php if (isset($unread) && !empty($unread)): ?>
                    <div data-notification-number
                        class="flex rounded-full absolute -top-1 -right-1 bg-error-1 items-center justify-center overflow-hidden w-5 h-5 text-xs font-medium px-1">
                        <?php echo count($unread); ?>
                    </div>
                    <?php endif;?>
                    <span class="material-icons-round text-xl lg:text-2xl">
                        notifications
                    </span>
                </div>
                <div data-dropdown-notification-content="0"
                    class="w-full h-full min-w-[280px] bg-overlay rounded-xl shadow-lg overflow-hidden"
                    aria-labelledby="noti-list">
                    <input type="hidden" name="notif-ids" value="<?php
$data = !empty($nid) ? array_map(function ($val) {return $val['notif_id'];}, $nid) : [];
echo implode(",", $data)?>">
                    <div data-set-all-notification-read onclick="readallnotif()"
                        class="block w-full mb-2 bg-black bg-opacity-20">
                        <a class="flex items-center gap-2 justify-center w-full bg px-4 py-2  hover:text-accent cursor-pointer"
                            data-position="dropdown">
                            <span class="material-icons-round text-xl">
                                check
                            </span>
                            <?php _e('Mark all as read', 'kiranime');?></a>
                    </div>
                    <div data-notification-content-area class="max-h-52 overflow-y-auto overflow-x-hidden">
                        <?php foreach ($nid as $notification): ?>
                        <a data-notification-id="<?php echo $notification['notif_id'] ?>"
                            class="flex gap-2 mb-2 text-xs font-medium px-4 py-2 <?php echo $notification['status'] ? 'opacity-75' : '' ?>"
                            href="<?php echo $notification['url'] ?>?n_id=<?php echo $notification['notif_id'] ?>">
                            <div class="w-12 h-14 overflow-hidden relative"><img
                                    src="<?php echo $notification['featured'] ?>"
                                    class="w-full h-full object-cover absolute inset-0"
                                    alt="<?php echo $notification['title'] ?>"></div>
                            <div>
                                <span class="block">
                                    <?php printf(esc_html__('%s - Episode %d Available NOW!', 'kiranime'), $notification['title'], $notification['number']);?>

                                </span>
                                <div class="text-xs font-light mt-1 text-accent">
                                    <?php echo $notification['published'] ?></div>
                            </div>
                        </a>
                        <?php endforeach;?>
                    </div>
                    <a class="block rounded-b-md shadow-md bg-secondary w-full px-4 py-2 text-sm"
                        href="<?php echo Kira_Utility::page_link('pages/notification.php') ?>">
                        <div class="text-center  hover:text-sky-300"><?php _e('View all', 'kiranime');?></div>
                    </a>
                    <script>
                    async function readallnotif() {
                        const ids = document.querySelector('[name="notif-ids"]')?.value;
                        const body = new FormData();
                        body.append('action', 'kiranime_check_notification');
                        body.append('notification_id', ids);
                        const req = await fetch(`/wp-json/kiranime/v1/notification`, {
                            method: 'PUT',
                            body: JSON.stringify({
                                notification_id: ids.split(','),
                                user_id: current_user_id,
                            }),
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': user_action,
                            },
                        });
                        if (!req.ok) {
                            error(__('Something went wrong.', 'kiranime'));
                            return
                        }
                        document.querySelectorAll('[data-notification-id]').forEach((e) => e.classList
                            .add('opacity-75'));
                        const num = document.querySelector('[data-notification-number]');
                        num.classList.add('hidden');
                        num.classList.remove('flex')
                    }
                    </script>
                </div>
            </div>
            <div style="background-image: url('<?=Kira_User::get_avatar(get_current_user_id())?>');"
                class="w-7 h-7 md:w-9 md:h-9 lg:w-10 lg:h-10 bg-cover bg-center bg-no-repeat rounded-full relative cursor-pointer">
                <div class="absolute inset-0 opacity-0" data-user-menu-dropdown>

                </div>

                <div data-user-menu-content data-user-menu-state="0" class="w-full h-full min-w-[280px]">
                    <div class="py-3 px-1">
                        <div class="w-full">
                            <div class="w-full text-sm font-semibold text-accent leading-9">
                                <strong data-current-username><?php if ($uif) {echo $uif->display_name;}?></strong>
                            </div>
                            <div class="w-full text-xs font-light text-gray-200">
                                <?php if ($uif) {echo $uif->user_email;}?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-1 space-y-1">
                        <?php foreach (Kira_Utility::get_user_menu() as $menu): ?>
                        <a class="px-4 py-3 flex items-center hover:text-accent gap-2 font-medium text-xs bg-overlay bg-opacity-20 rounded-xl"
                            href="<?=$menu['link']?>">
                            <span class="material-icons-round text-xl">
                                <?php echo $menu['icon'] ?>
                            </span>
                            <?php echo $menu['name']; ?>
                        </a>
                        <?php endforeach;?>
                    </div>
                    <a data-logout-trigger
                        class="p-5 hover:text-accent flex w-full items-center justify-end gap-2 font-medium text-xs rounded-md"
                        href="#">
                        <span class="material-icons-round text-xl">
                            logout
                        </span>
                        <?php _e('Logout', 'kiranime');?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>
<div data-mobile-search-status="0"
    class="w-full pointer-events-none bg-primary bg-opacity-90 pb-2 transform -translate-y-full opacity-0 transition-all duration-200 z-40">
    <div class="relative w-full px-4">
        <form action="/<?=$advanced_search_url?>" autocomplete="off"
            class="w-full flex items-center justify-center relative">
            <a href="/<?=$advanced_search_url?>"
                class="w-1/12 h-[30px] rounded-sm flex items-center justify-center bg-overlay">
                <span class="material-icons-round text-2xl">
                    filter_alt
                </span>
            </a>
            <input data-mobile-search-input type="text" class="w-10/12 px-2 py-1 text-sm rounded-sm text-gray-900"
                name="keyword" placeholder="<?php _e('Search anime...', 'kiranime');?>">
            <button type="submit" class="w-1/12 flex items-center px-2 text-accent right-8">
                <span class="material-icons-round text-2xl">
                    search
                </span>
            </button>
        </form>
        <div class="bg-tertiary shadow-lg absolute top-10 mt-1 inset-x-0 z-10 list-none hidden"
            data-mobile-search-result>
            <div data-mobile-search-result-area>

            </div>
            <a data-mobile-search-view-all href="/<?=$advanced_search_url?>"
                class="flex items-center justify-center w-full p-4 bg-accent-3 text-base">
                <?php _e('View all results', 'kiranime');?> <svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 256 512" class="w-4 h-4 ml-2 inline-block">
                    <path fill="currentColor"
                        d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z" />
                </svg></i>
            </a>
        </div>
    </div>
</div>
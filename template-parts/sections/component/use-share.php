<?php
$title  = '';
$d_text = '';
if (is_home() || is_page_template('pages/homepage.php')) {
    $title  = get_bloginfo('name');
    $d_text = get_bloginfo('name');
} else {
    $title = get_the_title();
}

if (is_single('anime')) {
    $d_text  = 'This Anime';
    $buttons = Kira_Utility::share_button($title, home_url(add_query_arg($_GET, $wp->request)), 3);
} else {
    $buttons = Kira_Utility::share_button($title, home_url(add_query_arg($_GET, $wp->request)));
}
?>
<div class="share-button md:grid grid-cols-3 lg:inline-flex gap-2 pr-3 md:pr-0" role="group">
    <?php foreach ($buttons as $name => $button): ?>
    <a href="<?=$button['link']?>" style="background-color: <?=$button['color']?>;"
        class="rounded flex items-center justify-center gap-[6px] lg:px-5 md:px-3 py-1.5 text-white font-medium text-xs leading-tight uppercase focus:outline-none focus:ring-0 hover:brightness-75 transition duration-150 ease-in-out shadow-md hover:shadow-lg focus:shadow-lg">

        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4" viewBox="<?=$button['vbox']?>">
            <path d="<?=$button['icon']?>" />
        </svg>
        <span class="hidden md:inline-block"><?=$name?></span>
    </a>
    <?php endforeach;?>
</div>
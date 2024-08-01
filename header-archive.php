<!DOCTYPE html>
<html <?php language_attributes();?>>

<head>
    <meta charset="<?php bloginfo('charset');?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php

add_filter('document_title_parts', function ($title_parts) use ($args) {
    $title_parts['title'] = $args['title'];
    return $title_parts;
});
?>
    <?php kiranime_show_ads('__ads_header', true)?>
    <?php wp_head();?>
</head>

<body <?php body_class('bg-primary text-text-color antialiased font-montserrat');?>>
    <div id="history-area"></div>

    <header id="header-data-single"
        class="h-[50px] md:h-[70px] bg-primary text-text-color text-sm fixed inset-0 z-999 leading-5 p-0 text-left transition-all duration-200 <?php if (is_admin_bar_showing()) {echo "mt-[2.875rem] lg:mt-8";}?>">
        <?php get_template_part('template-parts/sections/site', 'header');?>
    </header>

    <!-- login modal -->
    <?php get_template_part('template-parts/sections/component/auth', 'form')?>

    <!-- overlay for menu sidebar when active -->
    <div data-sidebar-overlay class="hidden fixed inset-0 bg-primary bg-opacity-50 z-39"></div>

    <!-- Menu sidebar -->
    <div data-sidebar
        class="w-full max-w-xs lg:w-72 fixed inset-0 transform -translate-x-full transition-transform duration-150 ease-in-out h-full bg-primary z-999 overflow-y-auto overflow-x-hidden">
        <div data-sidebar-closer
            class="cursor-pointer px-4 py-2 rounded-full w-max max-w-max flex items-center gap-3 bg-white bg-opacity-20 font-medium text-sm my-5 mx-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-4 h-4">
                <path fill="currentColor"
                    d="M257.5 445.1l-22.2 22.2c-9.4 9.4-24.6 9.4-33.9 0L7 273c-9.4-9.4-9.4-24.6 0-33.9L201.4 44.7c9.4-9.4 24.6-9.4 33.9 0l22.2 22.2c9.5 9.5 9.3 25-.4 34.3L136.6 216H424c13.3 0 24 10.7 24 24v32c0 13.3-10.7 24-24 24H136.6l120.5 114.8c9.8 9.3 10 24.8.4 34.3z" />
            </svg>
            <?php _e('Close Menu', 'kiranime')?>
        </div>
        <div class="mt-5 min-h-full" data-default-header>
            <?php if (has_nav_menu('header_side')): wp_nav_menu([
        'theme_location'  => 'header_side',
        'container_class' => 'w-full p-2',
        'menu_class'      => 'flex flex-col text-sm p-0 m-0 menu-header-side ',
    ]);endif?>
            <?php get_template_part('template-parts/sections/component/use', 'genre');?>
        </div>
    </div>
    <main class="max-w-screen min-h-screen overflow-visible overflow-x-hidden z-40 mb-10">
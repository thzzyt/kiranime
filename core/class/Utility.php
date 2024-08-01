<?php

class Kira_Utility
{
    public static function save_report($data)
    {
        if (!$data || empty($data)) {
            return null;
        }

        $post = wp_insert_post([
            'post_type'   => 'reported',
            'post_status' => 'publish',
            'post_title'  => $data['title'],
        ]);

        if (!$post) {
            return null;
        }

        update_post_meta($post, 'reported_value', $data['value']);
        update_post_meta($post, 'reported_episode', get_bloginfo('wpurl') . '/wp-admin/post.php?post=' . $data['id'] . '&action=edit');

        return true;
    }
    public static function page_link($template = '')
    {
        if (!$template) {
            return '';
        }

        $pages = get_pages(array(
            'meta_key'   => '_wp_page_template',
            'meta_value' => $template,
        ));
        if (empty($pages) || !isset($pages[0])) {
            return '';
        }
        $result = get_page_link($pages[0]->ID);
        return $result;
    }

    public static function page_slug($template)
    {

        $pages = get_pages([
            'meta_key'   => '_wp_page_template',
            'meta_value' => $template,
        ]);

        if (isset($pages[0])) {
            return $pages[0]->post_name;
        }

        return null;
    }

    public static function social()
    {
        $socials = [
            'telegram' => [
                'link'  => get_theme_mod('__social_telegram'),
                'color' => '#0088cc',
                'vbox'  => '0 0 496 512',
                'icon'  => 'M446.7 98.6l-67.6 318.8c-5.1 22.5-18.4 28.1-37.3 17.5l-103-75.9-49.7 47.8c-5.5 5.5-10.1 10.1-20.7 10.1l7.4-104.9 190.9-172.5c8.3-7.4-1.8-11.5-12.9-4.1L117.8 284 16.2 252.2c-22.1-6.9-22.5-22.1 4.6-32.7L418.2 66.4c18.4-6.9 34.5 4.1 28.5 32.2z',
            ],
            'discord'  => [
                'link'  => get_theme_mod('__social_discord'),
                'color' => '#7289da',
                'vbox'  => '0 0 640 512',
                'icon'  => 'M297.216 243.2c0 15.616-11.52 28.416-26.112 28.416-14.336 0-26.112-12.8-26.112-28.416s11.52-28.416 26.112-28.416c14.592 0 26.112 12.8 26.112 28.416zm-119.552-28.416c-14.592 0-26.112 12.8-26.112 28.416s11.776 28.416 26.112 28.416c14.592 0 26.112-12.8 26.112-28.416.256-15.616-11.52-28.416-26.112-28.416zM448 52.736V512c-64.494-56.994-43.868-38.128-118.784-107.776l13.568 47.36H52.48C23.552 451.584 0 428.032 0 398.848V52.736C0 23.552 23.552 0 52.48 0h343.04C424.448 0 448 23.552 448 52.736zm-72.96 242.688c0-82.432-36.864-149.248-36.864-149.248-36.864-27.648-71.936-26.88-71.936-26.88l-3.584 4.096c43.52 13.312 63.744 32.512 63.744 32.512-60.811-33.329-132.244-33.335-191.232-7.424-9.472 4.352-15.104 7.424-15.104 7.424s21.248-20.224 67.328-33.536l-2.56-3.072s-35.072-.768-71.936 26.88c0 0-36.864 66.816-36.864 149.248 0 0 21.504 37.12 78.08 38.912 0 0 9.472-11.52 17.152-21.248-32.512-9.728-44.8-30.208-44.8-30.208 3.766 2.636 9.976 6.053 10.496 6.4 43.21 24.198 104.588 32.126 159.744 8.96 8.96-3.328 18.944-8.192 29.44-15.104 0 0-12.8 20.992-46.336 30.464 7.68 9.728 16.896 20.736 16.896 20.736 56.576-1.792 78.336-38.912 78.336-38.912z',
            ],
            'reddit'   => [
                'link'  => get_theme_mod('__social_reddit'),
                'color' => '#ff4500',
                'vbox'  => '0 0 512 512',
                'icon'  => 'M201.5 305.5c-13.8 0-24.9-11.1-24.9-24.6 0-13.8 11.1-24.9 24.9-24.9 13.6 0 24.6 11.1 24.6 24.9 0 13.6-11.1 24.6-24.6 24.6zM504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-132.3-41.2c-9.4 0-17.7 3.9-23.8 10-22.4-15.5-52.6-25.5-86.1-26.6l17.4-78.3 55.4 12.5c0 13.6 11.1 24.6 24.6 24.6 13.8 0 24.9-11.3 24.9-24.9s-11.1-24.9-24.9-24.9c-9.7 0-18 5.8-22.1 13.8l-61.2-13.6c-3-.8-6.1 1.4-6.9 4.4l-19.1 86.4c-33.2 1.4-63.1 11.3-85.5 26.8-6.1-6.4-14.7-10.2-24.1-10.2-34.9 0-46.3 46.9-14.4 62.8-1.1 5-1.7 10.2-1.7 15.5 0 52.6 59.2 95.2 132 95.2 73.1 0 132.3-42.6 132.3-95.2 0-5.3-.6-10.8-1.9-15.8 31.3-16 19.8-62.5-14.9-62.5zM302.8 331c-18.2 18.2-76.1 17.9-93.6 0-2.2-2.2-6.1-2.2-8.3 0-2.5 2.5-2.5 6.4 0 8.6 22.8 22.8 87.3 22.8 110.2 0 2.5-2.2 2.5-6.1 0-8.6-2.2-2.2-6.1-2.2-8.3 0zm7.7-75c-13.6 0-24.6 11.1-24.6 24.9 0 13.6 11.1 24.6 24.6 24.6 13.8 0 24.9-11.1 24.9-24.6 0-13.8-11-24.9-24.9-24.9z',
            ],
            'facebook' => [
                'link'  => get_theme_mod('__social_facebook'),
                'color' => '#1877f2',
                'vbox'  => '0 0 512 512',
                'icon'  => 'M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z',
            ],
            'twitter'  => [
                'link'  => get_theme_mod('__social_twitter'),
                'color' => '#1da1f2',
                'vbox'  => '0 0 512 512',
                'icon'  => 'M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z',
            ],
            'youtube'  => [
                'link'  => get_theme_mod('__social_youtube'),
                'color' => '#ff0000',
                'vbox'  => '0 0 576 512',
                'icon'  => 'M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z',
            ],
            'tumblr'   => [
                'link'  => get_theme_mod('__social_tumblr'),
                'color' => '#35465c',
                'vbox'  => '0 0 320 512',
                'icon'  => 'M309.8 480.3c-13.6 14.5-50 31.7-97.4 31.7-120.8 0-147-88.8-147-140.6v-144H17.9c-5.5 0-10-4.5-10-10v-68c0-7.2 4.5-13.6 11.3-16 62-21.8 81.5-76 84.3-117.1.8-11 6.5-16.3 16.1-16.3h70.9c5.5 0 10 4.5 10 10v115.2h83c5.5 0 10 4.4 10 9.9v81.7c0 5.5-4.5 10-10 10h-83.4V360c0 34.2 23.7 53.6 68 35.8 4.8-1.9 9-3.2 12.7-2.2 3.5.9 5.8 3.4 7.4 7.9l22 64.3c1.8 5 3.3 10.6-.4 14.5z',
            ],
        ];

        return $socials;
    }

    public static function share_button(string $page_title, string $page_url, int $max = 6)
    {
        $buttons = [
            'telegram' => [
                'link'  => 'https://t.me/share/url?url=' . $page_url . '&text=' . $page_title,
                'color' => '#0088cc',
                'vbox'  => '0 0 496 512',
                'icon'  => 'M446.7 98.6l-67.6 318.8c-5.1 22.5-18.4 28.1-37.3 17.5l-103-75.9-49.7 47.8c-5.5 5.5-10.1 10.1-20.7 10.1l7.4-104.9 190.9-172.5c8.3-7.4-1.8-11.5-12.9-4.1L117.8 284 16.2 252.2c-22.1-6.9-22.5-22.1 4.6-32.7L418.2 66.4c18.4-6.9 34.5 4.1 28.5 32.2z',
            ],
            'reddit'   => [
                'link'  => 'https://reddit.com/submit?url=' . $page_url . '&title=' . $page_title,
                'color' => '#ff4500',
                'vbox'  => '0 0 512 512',
                'icon'  => 'M201.5 305.5c-13.8 0-24.9-11.1-24.9-24.6 0-13.8 11.1-24.9 24.9-24.9 13.6 0 24.6 11.1 24.6 24.9 0 13.6-11.1 24.6-24.6 24.6zM504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-132.3-41.2c-9.4 0-17.7 3.9-23.8 10-22.4-15.5-52.6-25.5-86.1-26.6l17.4-78.3 55.4 12.5c0 13.6 11.1 24.6 24.6 24.6 13.8 0 24.9-11.3 24.9-24.9s-11.1-24.9-24.9-24.9c-9.7 0-18 5.8-22.1 13.8l-61.2-13.6c-3-.8-6.1 1.4-6.9 4.4l-19.1 86.4c-33.2 1.4-63.1 11.3-85.5 26.8-6.1-6.4-14.7-10.2-24.1-10.2-34.9 0-46.3 46.9-14.4 62.8-1.1 5-1.7 10.2-1.7 15.5 0 52.6 59.2 95.2 132 95.2 73.1 0 132.3-42.6 132.3-95.2 0-5.3-.6-10.8-1.9-15.8 31.3-16 19.8-62.5-14.9-62.5zM302.8 331c-18.2 18.2-76.1 17.9-93.6 0-2.2-2.2-6.1-2.2-8.3 0-2.5 2.5-2.5 6.4 0 8.6 22.8 22.8 87.3 22.8 110.2 0 2.5-2.2 2.5-6.1 0-8.6-2.2-2.2-6.1-2.2-8.3 0zm7.7-75c-13.6 0-24.6 11.1-24.6 24.9 0 13.6 11.1 24.6 24.6 24.6 13.8 0 24.9-11.1 24.9-24.6 0-13.8-11-24.9-24.9-24.9z',
            ],
            'facebook' => [
                'link'  => 'https://www.facebook.com/sharer.php?u=' . $page_url,
                'color' => '#1877f2',
                'vbox'  => '0 0 512 512',
                'icon'  => 'M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z',
            ],
            'whatsapp' => [
                'link'  => 'whatsapp://send/?text=' . $page_title . '%20' . $page_url,
                'color' => '#25D366',
                'vbox'  => '0 0 448 512',
                'icon'  => 'M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z',
            ],
            'twitter'  => [
                'link'  => 'https://twitter.com/intent/tweet?url=' . $page_url . '&text=' . $page_title,
                'color' => '#1da1f2',
                'vbox'  => '0 0 512 512',
                'icon'  => 'M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z',
            ],
            'tumblr'   => [
                'link'  => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl=' . $page_url . '&title=' . $page_title,
                'color' => '#35465c',
                'vbox'  => '0 0 320 512',
                'icon'  => 'M309.8 480.3c-13.6 14.5-50 31.7-97.4 31.7-120.8 0-147-88.8-147-140.6v-144H17.9c-5.5 0-10-4.5-10-10v-68c0-7.2 4.5-13.6 11.3-16 62-21.8 81.5-76 84.3-117.1.8-11 6.5-16.3 16.1-16.3h70.9c5.5 0 10 4.5 10 10v115.2h83c5.5 0 10 4.4 10 9.9v81.7c0 5.5-4.5 10-10 10h-83.4V360c0 34.2 23.7 53.6 68 35.8 4.8-1.9 9-3.2 12.7-2.2 3.5.9 5.8 3.4 7.4 7.9l22 64.3c1.8 5 3.3 10.6-.4 14.5z',
            ],
        ];

        return array_splice($buttons, 0, $max);
    }

    private static function convertToRGB($hex)
    {
        if ('#ffffff' == $hex || '#fff' == $hex) {
            return '255,255,255';
        }
        $rgb = sscanf($hex, '#%02x%02x%02x');
        return implode(',', $rgb);
    }

    public static function get_color_variables($return = false)
    {

        $primary         = Self::convertToRGB(get_theme_mod('primary-color', '#202125'));
        $secondary       = Self::convertToRGB(get_theme_mod('secondary-color', '#4a4b51'));
        $tertiary        = Self::convertToRGB(get_theme_mod('tertiary-color', '#414248'));
        $overlay         = Self::convertToRGB(get_theme_mod('overlay-color', '#2a2c31'));
        $accent          = Self::convertToRGB(get_theme_mod('accent-color', '#38bdf8'));
        $accent_2        = Self::convertToRGB(get_theme_mod('accent-2-color', '#0ea5e9'));
        $accent_3        = Self::convertToRGB(get_theme_mod('accent-3-color', '#0284c7'));
        $accent_4        = Self::convertToRGB(get_theme_mod('accent-4-color', '#0369a1'));
        $primary_darker  = Self::convertToRGB(get_theme_mod('primary-darker-color', '#14151a'));
        $primary_darkest = Self::convertToRGB(get_theme_mod('primary-darkest-color', '#121315'));
        $text            = Self::convertToRGB(get_theme_mod('text-color', '#fffff1'));
        $text_accent     = Self::convertToRGB(get_theme_mod('text-accent-color', '#f4f4f5'));
        $text_accent_2   = Self::convertToRGB(get_theme_mod('text-accent-2-color', '#71717a'));
        $error           = Self::convertToRGB(get_theme_mod('error-color', '#f43f5e'));
        $error_accent    = Self::convertToRGB(get_theme_mod('error-accent-color', '#e11d48'));
        $error_accent_2  = Self::convertToRGB(get_theme_mod('error-accent-2-color', '#be123c'));
        $error_accent_3  = Self::convertToRGB(get_theme_mod('error-accent-3-color', '#fb7185'));

        $result = ':root, ::after, ::before{
            --primary-color:' . $primary . ';
            --secondary-color:' . $secondary . ';
            --tertiary-color:' . $tertiary . ';
            --accent-color:' . $accent . ';
            --accent-2-color:' . $accent_2 . ';
            --accent-3-color:' . $accent_3 . ';
            --accent-4-color:' . $accent_4 . ';
            --primary-darker-color:' . $primary_darker . ';
            --primary-darkest-color:' . $primary_darkest . ';
            --overlay-color:' . $overlay . ';
            --text-color:' . $text . ';
            --text-accent-color:' . $text_accent . ';
            --text-accent-2-color:' . $text_accent_2 . ';
            --error-color:' . $error . ';
            --error-accent-color:' . $error_accent . ';
            --error-accent-2-color:' . $error_accent_2 . ';
            --error-accent-3-color:' . $error_accent_3 . ';
        }';

        return $result;
    }

    public static function get_user_menu()
    {
        return [
            [
                'name' => __('Profile', 'kiranime'),
                'icon' => 'account_box',
                'link' => Self::page_link('pages/profile.php'),
            ],
            [
                'name' => __('Continue Watching', 'kiranime'),
                'icon' => 'restore',
                'link' => Self::page_link('pages/continue-watching.php'),
            ],
            [
                'name' => __('Watch List', 'kiranime'),
                'icon' => 'favorite',
                'link' => Self::page_link('pages/watchlist.php'),
            ],
            [
                'name' => __('Notification', 'kiranime'),
                'icon' => 'notifications',
                'link' => Self::page_link('pages/notification.php'),
            ],
        ];
    }

    public static function save_setting(array $param = [])
    {
        if (!$param) {
            return ['data' => ['saved' => false, 'error' => 'no options'], 'status' => 400];
        }

        $options_key = [
            'jikan_url'     => '__a_jikan',
            'tmdb_key'      => '__a_tmdb',
            'auto_dl_image' => '__a_auto_dl',
            'episode_by'    => '__q_episode_by',
            'usepostdate'   => '__q_use_post_date',
            'defaultlang'   => '__u_def_language',
            'usestatus'     => '__u_status_i_du',
            'status_bg'     => '__u_status_bg',
            'mlt'           => '__c_medium_time',
            'clt'           => '__c_long_time',
            'slt'           => '__c_short_time',
            'urk'           => '__use_recaptcha',
            'krstk'         => 'kiranime_recaptcha_sitekey',
            'krsck'         => 'kiranime_recaptcha_secretkey',
            'use_cache'     => '__kira_use_cache',
        ];

        $results = [];
        foreach ($param as $key => $value) {
            $key_pair = $options_key[$key];

            if (!isset($value)) {
                continue;
            }

            update_option($key_pair, $value);
            $results[$options_key[$key]] = [
                'key' => $options_key[$key],
                'val' => $value,
                'opt' => get_option($options_key[$key]),
            ];
        }

        return ['data' => ['saved' => true, 'results' => $results], 'status' => 200];
    }

    public static function download_and_save_remote_image($image_url, $meta_name, $post_id, $type, $post_type = 'anime')
    {
        if (!$image_url) {
            return null;
        }

        if (stripos($image_url, get_bloginfo('wpurl')) !== false) {
            delete_post_meta($post_id, $meta_name);
            update_post_meta($post_id, $meta_name, $image_url);
            return $image_url;
        }

        $upload_dir = wp_upload_dir();
        $img_name   = time() . '.jpg';
        $img        = wp_remote_get($image_url);
        if (is_wp_error($img)) {
            return null;
        } else {
            $img = wp_remote_retrieve_body($img);
            $fp  = fopen($upload_dir['path'] . '/' . $img_name, 'w');
            fwrite($fp, $img);
            fclose($fp);

            $wp_filetype = wp_check_filetype($img_name, null);
            $attachment  = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', $img_name),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );

            //require for wp_generate_attachment_metadata which generates image related meta-data also creates thumbs
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_id = wp_insert_attachment($attachment, $upload_dir['path'] . '/' . $img_name, $post_id);
            //Generate post thumbnail of different sizes.
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload_dir['path'] . '/' . $img_name);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $attach_url = wp_get_attachment_url($attach_id);
            if ('anime' === $post_type) {
                if ('featured' === $type) {
                    //Set as featured image.
                    delete_post_meta($post_id, '_thumbnail_id');
                    update_post_meta($post_id, '_thumbnail_id', $attach_id, true);
                    delete_post_meta($post_id, $meta_name);
                    update_post_meta($post_id, $meta_name, $attach_url);
                } else {
                    // update Background image
                    delete_post_meta($post_id, $meta_name);
                    update_post_meta($post_id, $meta_name, $attach_url);
                }
            }

            if ('episode' === $post_type) {
                delete_post_meta($post_id, '_thumbnail_id');
                update_post_meta($post_id, '_thumbnail_id', $attach_id, true);
                delete_post_meta($post_id, $meta_name);
                update_post_meta($post_id, $meta_name, $attach_url);
            }

            return $attach_url;
        }
    }

    public static function get_remote_image($image_url)
    {
        if (!$image_url) {
            return ["status" => 0];
        }

        include_once ABSPATH . 'wp-admin/includes/media.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/image.php';
        // include_once 'wp-load.php';

        $supported_image = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
        );
        $ext = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));

        if (in_array($ext, $supported_image)) {
            $attach_id = media_sideload_image($image_url, null, null, 'id');

            if (is_wp_error($attach_id)) {
                return ['status' => 0];
            }

            return ['status' => 1, 'thumbnail_id' => $attach_id, 'thumbnail_url' => wp_get_attachment_image_url($attach_id, 'full')];
        }

        $imagetype = end(explode('/', getimagesize($image_url)['mime']));
        $uniq_name = date('dmY') . '' . (int) microtime(true);
        $filename  = $uniq_name . '.' . $imagetype;

        $uploaddir  = wp_upload_dir();
        $uploadfile = $uploaddir['path'] . '/' . $filename;
        $contents   = file_get_contents($image_url);
        $savefile   = fopen($uploadfile, 'w');
        fwrite($savefile, $contents);
        fclose($savefile);

        $wp_filetype = wp_check_filetype(basename($filename), null);
        $attachment  = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => $filename,
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        $attach_id = wp_insert_attachment($attachment, $uploadfile);

        if (is_wp_error($attach_id)) {
            return ['status' => 0];
        }

        $imagenew     = get_post($attach_id);
        $fullsizepath = get_attached_file($imagenew->ID);
        $attach_data  = wp_generate_attachment_metadata($attach_id, $fullsizepath);
        wp_update_attachment_metadata($attach_id, $attach_data);
        return ['status' => 1, 'thumbnail_id' => $attach_id, 'thumbnail_url' => wp_get_attachment_image_url($attach_id)];
    }

    public static function non_fetchable_js_translations()
    {
        return [
            'year'   => __('year', 'kiranime'),
            'season' => __('season', 'kiranime'),
        ];
    }
}

<?php

class Kiranime_Init
{
    public function __construct()
    {
        $required_files = glob(KIRA_DIR . '/**/*.php');
        foreach ($required_files as $file) {
            include_once $file;
        }

        // post-type
        include_once KIRA_DIR . '/posttype.php';

        // taxonomies
        include_once KIRA_DIR . '/taxonomies.php';

        // enqueue scripts
        add_action('wp_enqueue_scripts', [$this, '__enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, '__admin_scripts']);

        // add filter
        add_filter('nav_menu_css_class', [$this, '__nav_li_class'], 10, 4);
        add_filter('nav_menu_submenu_css_class', [$this, '__submenu_class'], 10, 3);
        add_filter('wp_nav_menu', [$this, '__nav_ul_class']);

        // remove default images
        add_filter('intermediate_image_sizes_advanced', [$this, '__remove_image_default']);

        // theme setup
        add_action('after_setup_theme', [$this, '__setup']);

        // advanced search
        add_filter('query_vars', [$this, '__advanced_search_vars']);

        // add filter for comments link
        add_filter('comment_form_logged_in', [$this, 'comment_link'], 10, 3);

        // initialize widget
        add_action('widgets_init', [$this, '__load_widget']);

        add_action('after_switch_theme', [$this, '__cek_ganti']);

        // disable block editor
        add_filter('use_block_editor_for_post_type', [$this, 'disable_block_editor'], 10, 2);

        // preload modules
        add_filter('script_loader_tag', [$this, 'add_type_attribute'], 10, 3);

        // register kiranime endpoint
        add_action('rest_api_init', function () {
            $routes = new Kiranime_Endpoint();
            $routes->register_routes();
        });

        add_filter('init', function () {
            if (!current_user_can('manage_options')) {
                show_admin_bar(false);
            }
        });
    }

    /**
     * enqueue css and javascipts for kiranime theme
     * not all js is required, some js only required for some page.
     */
    public function __enqueue_scripts()
    {
        load_theme_textdomain('kiranime', get_template_directory() . '/languages');
        $modules = [
            '<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">',
            '<link rel="preconnect" href="https://fonts.googleapis.com">',
            '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
            '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
            rel="stylesheet">',
            '<style>' . Kira_Utility::get_color_variables() . '</style>',
        ];

        $use_recaptcha = get_option('__use_recaptcha', false) ? 1 : 0;
        $deps          = [];
        if ($use_recaptcha) {
            $sitekey = get_option('kiranime_recaptcha_sitekey');
            wp_enqueue_script('recaptcha-api', "https://www.google.com/recaptcha/api.js", [], false, true);
            wp_add_inline_script('recaptcha-api', "var sitekey='$sitekey'", 'before');
            array_push($deps, 'recaptcha-api');
        }

        // assets
        if (KIRA_MODE) {
            $assets  = json_decode(file_get_contents(KIRA_ASSETS . '/manifest.json'), true);
            $version = $assets['hash'] ? $assets['hash'] : KIRA_VER;
            wp_enqueue_style('kiranime-style', KIRA_URI . '/assets/' . 'style.css', [], $version);
            wp_enqueue_script('kiranime-frontend', KIRA_URI . '/assets/' . 'kiranime-frontend.js', [], $version, true);
        } else {
            $loads = ['kiranime-frontend', '@vite/client', 'kiranime-backend'];
            $local = 'http://localhost:3000/';
            foreach ($loads as $load) {
                $link = $local . $load;
                wp_enqueue_script('kira-' . $load, $link, $deps, KIRA_VER, true);
            }
        }

        // inline script
        // inline holder
        wp_enqueue_script('kira-js-holder', KIRA_URI . '/core/helper/jsholder.js', [], KIRA_VER, false);
        $lists         = json_encode(Watchlist::watchlist_type());
        $global_nonce  = wp_create_nonce('wp_rest');
        $logout_nonce  = wp_create_nonce('log-out');
        $loggedIn      = is_user_logged_in();
        $inline_script = $this->is_active();
        $site_url      = get_site_url();
        wp_add_inline_script('kira-js-holder', 'var watchlist_types = ' . $lists . ';var current_user_id = parseInt("' . get_current_user_id() . '");var user_action="' . $global_nonce . '";var logout_nonce="' . $logout_nonce . '";var isloggedIn = ' . json_encode($loggedIn) . ';var inline_scripts=JSON.parse("' . $inline_script . '");var use_captcha=JSON.parse("' . $use_recaptcha . '");var site_url="' . $site_url . '";', 'before');

        // load json translation
        $this->load_json_translation();

        // preload js files
        echo implode('', $modules);
    }

    public function load_json_translation()
    {
        if (file_exists(get_template_directory() . '/languages/' . get_locale() . '.json')) {
            $is_translate_exist = file_get_contents(get_template_directory() . '/languages/' . get_locale() . '.json');

            wp_add_inline_script('kira-js-holder', 'var tranlationData = ' . $is_translate_exist, 'after');
        }
    }

    public function add_type_attribute($t, $h, $s)
    {
        if (stripos($h, 'kiranime') !== false) {
            if (!KIRA_MODE) {
                $s = remove_query_arg('ver', $s);
            }

            $t = '<script type="module" src="' . esc_url($s) . '"></script>';
        }

        return $t;
    }

    public static function custom_queue(string $args)
    {
        wp_enqueue_script("kiranime-$args", KIRA_URI . '/assets/' . $args . '.js', ['kira-js-holder'], KIRA_VER, true);
    }

    public function __admin_scripts($hook)
    {
        if (stripos($hook, 'kiranime_tools') !== false || (isset($_GET['post_type']) && in_array($_GET['post_type'], ['anime', 'episode']) && !in_array($hook, ['edit.php', 'edit-tags.php'])) || ('post.php' === $hook && isset($_GET['action']) && 'edit' === $_GET['action'])) {

            // assets
            $modules = [
                '<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
            rel="stylesheet">',
                '<style>' . Kira_Utility::get_color_variables() . '</style>',

            ];
            if (KIRA_MODE) {
                $assets  = json_decode(file_get_contents(KIRA_ASSETS . 'manifest.json'), true);
                $version = $assets['hash'] ? $assets['hash'] : KIRA_VER;
                wp_enqueue_style('kiranime-style', KIRA_URI . '/assets/' . 'style.css', [], $version);
                wp_enqueue_script('kiranime-backend', KIRA_URI . '/assets/' . 'kiranime-backend.js', [], $version, true);
            } else {
                $loads = ['@vite/client', 'kiranime-backend'];
                $local = 'http://localhost:3000/';
                foreach ($loads as $load) {
                    $link = $local . $load;
                    wp_enqueue_script('kira-' . $load, $link, [], KIRA_VER, true);
                }
            }

            // Inline scripts
            wp_enqueue_script('kira-js-holder', KIRA_URI . '/core/helper/jsholder.js', [], KIRA_VER, false);
            $global_nonce  = wp_create_nonce('wp_rest');
            $tmdbdeflang   = get_option('__u_def_language', 'en');
            $inline_script = $this->is_active();
            $site_url      = get_site_url();
            wp_add_inline_script('kira-js-holder', 'var current_user_id = parseInt("' . get_current_user_id() . '");var user_action = "' . $global_nonce . '";var tmdblang = "' . $tmdbdeflang . '";var inline_scripts = "' . $inline_script . '";var site_url="' . $site_url . '";', 'before');

            // load json translation
            $this->load_json_translation();

            // preload module
            echo implode('', $modules);
        }
    }

    /**
     * Adds option 'li_class' to 'wp_nav_menu'.
     *
     * @param string[]  $classes String of classes.
     * @param mixed   $item The curren item.
     * @param WP_Term $args Holds the nav menu arguments.
     *
     * @return array
     */
    public function __nav_li_class($classes, $item, $args, $depth)
    {
        if (isset($args->li_class)) {
            $classes[] = $args->li_class;
        }

        if (isset($args->{"li_class_$depth"})) {
            $classes[] = $args->{"li_class_$depth"};
        }

        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'active ';
        }

        if ('header-menu' == $args->theme_location) {
            $classes[] = 'nav-link';
        }

        return $classes;
    }

    /**
     * Adds option submenu class.
     *
     * @param string[]  $classes String of classes.
     * @param WP_Term $args Holds the nav menu arguments.
     * @param mixed   $depth The curren item.
     *
     * @return array
     */
    public function __submenu_class($classes, $args, $depth)
    {
        if (isset($args->submenu_class)) {
            $classes[] = $args->submenu_class;
        }

        if (isset($args->{"submenu_class_$depth"})) {
            $classes[] = $args->{"submenu_class_$depth"};
        }

        return $classes;
    }

    public function __nav_ul_class($ulclass)
    {
        return preg_replace('/<a /', '<a class="nav-link"', $ulclass);
    }

    public function __remove_image_default($sizes)
    {
        if (!get_option('__a_auto_dl')) {
            unset($sizes['small']); // 150px
            unset($sizes['medium']); // 300px
            unset($sizes['medium_large']); // 768px
            unset($sizes['large']); // 1024px
        }
        return $sizes;
    }

    public function __advanced_search_vars($vars)
    {
        $vars[] = 's_keyword';
        $vars[] = 's_genre';
        $vars[] = 's_type';
        $vars[] = 's_status';
        $vars[] = 's_season';
        $vars[] = 's_year';
        $vars[] = 's_orderby';
        $vars[] = 's_order';
        $vars[] = 'arty';

        return $vars;
    }

    public function is_active()
    {
        $result        = 0;
        $inline_script = get_option('__a_act_inline', 0);
        $active_status = get_option('is_active', 0);
        if (0 !== $inline_script) {
            $d = base64_decode($inline_script);
            if ($d && $d['key'] && $d['email']) {
                $result = 1;
            }
        }

        if (!$active_status) {
            $result = 0;
        }

        return $result;
    }

    public static function title_filter($where, $wp_query)
    {
        global $wpdb;
        if ($search_term = $wp_query->get('keyword')) {
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($search_term)) . '%\'';
        }
        return $where;
    }

    public function __setup()
    {
        add_theme_support('title-tag');

        register_nav_menus(
            array(
                'footer'         => 'Footer Menu',
                'header_side'    => 'Header Side Menu',
                'landing_header' => 'Landing Header',
            )
        );

        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'caption',
            )
        );

        /**
         * widget area for homepage main listing and main sidebar
         */
        register_sidebar([
            'name'          => 'Main Homepage Anime List',
            'id'            => 'homepage-main-list',
            'description'   => 'Homepage Main List',
            'before_widget' => '',
            'after_widget'  => '<div class="mb-6"></div>',
            'before_title'  => '',
            'after_title'   => '',
        ]);
        register_sidebar(array(
            'name'          => 'Homepage Sidebar',
            'id'            => 'homepage-sidebar',
            'description'   => 'Homepage sidebar',
            'before_widget' => '',
            'after_widget'  => '<div class="mb-6"></div>',
            'before_title'  => '<div class="text-2xl leading-10 font-semibold px-5 lg:p-0 m-0 text-accent-3 mb-4">',
            'after_title'   => '</div>',
        ));

        /**
         * sidebar for single anime
         */
        register_sidebar(array(
            'name'          => 'Single Info Sidebar',
            'id'            => 'anime-info-sidebar',
            'description'   => 'Show widget on anime/episode',
            'before_widget' => '',
            'after_widget'  => '<div class="mb-6"></div>',
            'before_title'  => '<div class="text-2xl leading-10 font-semibold px-5 lg:p-0 m-0 text-accent-3 mb-4">',
            'after_title'   => '</div>',
        ));

        /**
         * sidebar for pages
         */
        register_sidebar(array(
            'name'          => 'Archive Sidebar',
            'id'            => 'archive-sidebar',
            'description'   => 'Show widget on archive',
            'before_widget' => '',
            'after_widget'  => '<div class="mb-6"></div>',
            'before_title'  => '<div class="text-2xl leading-10 font-semibold px-5 lg:p-0 m-0 text-accent-3 mb-4">',
            'after_title'   => '</div>',
        ));

        /**
         * sidebar for news or posts
         */
        register_sidebar(array(
            'name'          => 'Article Sidebar',
            'id'            => 'article-sidebar',
            'description'   => 'Show widget on article archive and single',
            'before_widget' => '',
            'after_widget'  => '<div class="mb-6"></div>',
            'before_title'  => '<div class="text-2xl leading-10 font-semibold px-5 lg:p-0 m-0 text-accent-3 mb-4">',
            'after_title'   => '</div>',
        ));

        add_theme_support('custom-logo', [
            'height' => '60',
            'width'  => '300',
        ]);
        add_theme_support('post-thumbnails');

        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');

        add_theme_support('editor-styles');
        add_editor_style('css/editor-style.css');
        load_theme_textdomain('kiranime', get_template_directory() . '/languages');

        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }

        // add_image_sizes
        add_image_size('kirathumb', 320);
        add_image_size('smallthumb', 96);
    }

    /**
     * load all widgets
     */
    public function __load_widget()
    {
        require_once KIRA_DIR . '/widget/genre-list.php';
        require_once KIRA_DIR . '/widget/most-popular.php';
        require_once KIRA_DIR . '/widget/popular-list.php';
        require_once KIRA_DIR . '/widget/module/listing.php';
        require_once KIRA_DIR . '/widget/module/scheduled.php';

        register_widget('Kiranime_Popular_List');
        register_widget('Kiranime_Genre_List');
        register_widget('Kiranime_Most_Popular');
        register_widget('Listing_Module');
        register_widget('Kira_Scheduled_Module');
    }

    public function __cek_ganti()
    {
        $this->__taxonomies()->__pages();

        return true;
    }

    public function check_active()
    {
        $key = get_option('__a_act_inline', false);
        if (!$key) {
            update_option('is_active', 0);
            return;
        }
        $data = json_decode(base64_decode($key), true);
        if (!$data) {
            update_option('is_active', 0);
            return;
        }

        $res = wp_remote_post("http://localhost:4000/api/check_theme", [
            'headers' => [
                'Content-type' => 'Application/json',
            ],
            'body'    => json_encode([
                'key'   => $data['key'],
                'email' => $data['email'],
                'ref'   => get_bloginfo('url'),
                'theme' => 'kiranime',
            ]),
        ]);
        $header = wp_remote_retrieve_headers($res);
        $value  = wp_remote_retrieve_body($res);
        if (!isset($header['t-x-auth-theme']) || empty($header['t-x-auth-theme']) || !$value['status']) {
            update_option('is_active', 0);
            delete_option('__a_act_inline');
            return;
        }

        update_option('is_active', 1);
        return;
    }

    private function __taxonomies()
    {
        $required_terms = [
            'status'          => [
                'upcoming'  => __('Upcoming', 'kiranime'),
                'airing'    => __('Airing', 'kiranime'),
                'completed' => __('Completed', 'kiranime'),
            ],
            'anime_attribute' => [
                'sub' => 'SUB',
                'dub' => 'DUB',
                'hd'  => 'HD',
            ],
            'episode_type'    => [
                'series' => __('TV', 'kiranime'),
                'movie'  => __('Movie', 'kiranime'),
            ],
            'anime_type'      => [
                'ova'     => __('OVA', 'kiranime'),
                'movie'   => __('movie', 'kiranime'),
                'ona'     => __('ONA', 'kiranime'),
                'tv'      => __('TV', 'kiranime'),
                'special' => __('Special', 'kiranime'),
            ],
        ];

        foreach ($required_terms as $name => $terms) {
            foreach ($terms as $term_slug => $term_name) {
                $exist = get_term_by('slug', $term_slug, $name);

                if (!$exist) {
                    wp_insert_term($term_name, $name, [
                        'slug' => $term_slug,
                    ]);
                }
            }
        }

        return $this;
    }

    private function __pages()
    {
        $pages = [
            'profile'           => __('My Profile', 'kiranime'),
            'continue-watching' => __('Continue Watching', 'kiranime'),
            'watchlist'         => __('Watchlist', 'kiranime'),
            'notification'      => __('Notification', 'kiranime'),
            'search'            => __('Advanced Search', 'kiranime'),
            'az-list'           => __('A-Z Anime List', 'kiranime'),
            'homepage'          => __('Home', 'kiranime'),
            // 'news'              => __('News', 'kiranime'),
        ];

        foreach ($pages as $page_template => $page_name) {
            $template = 'pages/' . $page_template . '.php';
            $exist    = Kira_Utility::page_link($template);

            if (!$exist) {
                wp_insert_post([
                    'post_title'    => $page_name,
                    'post_type'     => 'page',
                    'post_content'  => '',
                    'post_status'   => 'publish',
                    'post_name'     => $page_template,
                    'page_template' => $template,
                ]);
            }
        }

        return $this;
    }

    public function comment_link($html, $commenter, $user_identity)
    {
        $profile_page = Kira_Utility::page_link('pages/profile.php');
        $url          = '<a href="' . $profile_page . '">$1</a>';
        return preg_replace('#<a href="[^"]*" aria-label="[^"]*">([^<]*)</a>#', $url, $html, 1);
    }

    // use classic editor for anime/episode post type
    public function disable_block_editor($current_status, $post_type)
    {
        if (in_array($post_type, ['anime', 'episode'])) {
            return false;
        }

        return $current_status;
    }
}

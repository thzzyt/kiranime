<?php

/**
 * This is class for kiranime rest api endpoint
 *
 * @package   Kiranime
 * @since   1.0.0
 * @link      https://kiranime.moe
 * @author    Dzul Qurnain
 * @license   GPL-2.0+
 */

class Kiranime_Endpoint extends WP_REST_Controller
{

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {
        $version   = '1';
        $namespace = 'kiranime/v' . $version;
        register_rest_route($namespace, '/kiranime', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_act_status'],
                'permission_callback' => '__return_true',
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/global_nonce', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'get_global_nonce'],
                'permission_callback' => '__return_true',
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/taxonomy', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_taxonomy'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
                'args'                => [
                    'name'     => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {return is_string($val);},
                    ],
                    'taxonomy' => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {return is_string($val);},
                    ],
                    'slug'     => [
                        'type' => 'string',
                    ],
                ],
            ],
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_taxonomies'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'name' => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/savethemekey', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'save_theme_key'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/watchlist', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_watchlist'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'type'     => [
                        'type'    => 'string',
                        'default' => 'all',
                    ],
                    'per_page' => [
                        'type'    => 'integer',
                        'default' => 20,
                    ],
                    'page'     => [
                        'type'    => 'integer',
                        'default' => 1,
                    ],
                ],
            ],
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'add_watchlist'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'user_id'  => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'type'     => [
                        'type'    => 'string',
                        'default' => 'all',
                    ],
                    'anime_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_watchlist'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'user_id'  => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'anime_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/watchlist/public', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_public_watchlist'],
                'permission_callback' => function ($val) {
                    return true;
                },
                'args'                => [
                    'user_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/auth/login', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'try_login'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'username'   => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'sanitize_callback' => function ($value, $request, $param) {
                            return sanitize_text_field($value);
                        },
                    ],
                    'password'   => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'sanitize_callback' => function ($value, $request, $param) {
                            return sanitize_text_field($value);
                        },
                    ],
                    'rememberMe' => [
                        'type'              => 'boolean',
                        'required'          => false,
                        'default'           => true,
                        'validate_callback' => function ($val) {
                            return is_bool($val);
                        },

                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/auth/register', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'try_register'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'username' => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'sanitize_callback' => function ($value, $request, $param) {
                            return sanitize_text_field($value);
                        },
                    ],
                    'password' => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'sanitize_callback' => function ($value, $request, $param) {
                            return sanitize_text_field($value);
                        },
                    ],
                    'email'    => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_email($val);
                        },
                        'sanitize_callback' => function ($value, $request, $param) {
                            return sanitize_email($value);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/auth/logout', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'logout'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/auth/recovery', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'get_verification_code'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'userlogin' => [
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'type'              => 'string',
                    ],
                ],
            ],

        ]);
        register_rest_route($namespace, '/auth/reset', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'reset_user_password'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'userlogin'          => [
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'type'              => 'string',
                    ],
                    'verification_token' => [
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'type'              => 'string',
                    ],
                    'new_password'       => [
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'type'              => 'string',
                    ],
                    'repeat_password'    => [
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'type'              => 'string',
                    ],
                ],
            ],

        ]);
        register_rest_route($namespace, '/profile', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'upload_avatar'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'user_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'change_avatar'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'user_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'avatar'  => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/profile_update', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'profile_change'],
                'permission_callback' => function () {
                    return is_user_logged_in();
                },
                'args'                => [
                    'password'         => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                    'email'            => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_email($val);
                        },
                    ],
                    'username'         => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                    'confirm'          => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                    'u_nonce'          => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                    'current_password' => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                    'change_pass'      => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/anime', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_anime'],
                'permission_callback' => function () {
                    return current_user_can('edit_published_posts');
                },
                'args'                => [
                    'data' => [
                        'type'              => 'object',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_array($val) || is_object($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/anime/view', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'add_view'],
                'permission_callback' => '__return_true',
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/anime/popular', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_popular_anime'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'by' => [
                        'type'              => 'String',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/anime/title', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_anime_title'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'query' => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                    'type'  => [
                        'type'     => 'string',
                        'required' => false,
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/anime/search', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'search_anime'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'query' => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'sanitize_callback' => function ($value, $request, $param) {
                            return sanitize_text_field($value);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/anime/advancedsearch', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'advancedsearch_anime'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'query' => [
                        'type'              => 'string',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                        'sanitize_callback' => function ($value, $request, $param) {
                            return sanitize_text_field($value);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/anime/tooltip/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'show_tooltip'],
                'permission_callback' => '__return_true',
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/anime/vote', [
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'add_vote'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'anime_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'value'    => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);

        register_rest_route($namespace, '/anime/random', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_random_anime'],
                'permission_callback' => '__return_true',
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/episode/autoimport', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'auto_import_episode'],
                'permission_callback' => '__return_true',
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/episode', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_episode'],
                'permission_callback' => function () {
                    return current_user_can('edit_published_posts');
                },
                'args'                => [
                    'data' => [
                        'type'              => 'object',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_array($val) || is_object($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/episode/scheduled', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_scheduled_episode'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'day'   => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'month' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'year'  => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/episode/import', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'import_episode'],
                'permission_callback' => function () {
                    return current_user_can('edit_published_posts');
                },
                'args'                => [
                    'data' => [
                        'type'              => 'object',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_object($val) || is_array($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/episode/report', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'report_problem'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'data' => [
                        'type'              => 'object',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_object($val) || is_array($val);
                        },
                    ],
                ],
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_report'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'report_id' => [
                        'type'              => 'number',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/notification', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_notification'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'user_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'check_notification'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'user_id'         => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'notification_id' => [
                        'type'              => 'array',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_array($val);
                        },
                    ],
                ],
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_notification'],
                'permission_callback' => [$this, 'is_allowed'],
                'args'                => [
                    'user_id'         => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                    'notification_id' => [
                        'type'              => 'integer',
                        'required'          => true,
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/setting', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'save_setting'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/getremoteimage', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'get_remote_image'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/get_posts', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_all_posts'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/migrate_thumbnail', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'migrate_thumbnail'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/migrate_remove_tools', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'migrate_remove_tools'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [],
            ],
        ]);

        // updates
        register_rest_route($namespace, '/search_index', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_search_index_ids'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [],
            ],
        ]);
        register_rest_route($namespace, '/search_index/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'build_search_index'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args'                => [
                    'id' => [
                        'validate_callback' => function ($val) {
                            return is_numeric($val);
                        },
                    ],
                ],
            ],
        ]);
        register_rest_route($namespace, '/recaptcha', [
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'verify_recaptcha_response'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'session' => [
                        'validate_callback' => function ($val) {
                            return is_string($val);
                        },
                    ],
                ],
            ],
        ]);

    }

    public function get_params(WP_REST_Request $request)
    {
        return !empty($request->get_json_params()) ? $request->get_json_params() : (!empty($request->get_query_params()) ? $request->get_query_params() : $request->get_body_params());
    }

    public function verify_recaptcha_response(WP_REST_Request $request)
    {
        $params = $this->get_params($request);

        $session  = $params['session'];
        $chaptcha = new Kiranime_recaptcha($session, $_SERVER['REMOTE_ADDR']);
        $chaptcha->validate_session();

        return new WP_REST_Response(['status' => $chaptcha->isValid, 'message' => $chaptcha->message, 'response' => $chaptcha->google_response]);
    }

    public function create_taxonomy(WP_REST_Request $request)
    {
        $params = $this->get_params($request);

        if (taxonomy_exists($params['taxonomy'])) {
            $sslug = sanitize_title($params['slug']);
            $slug  = isset($params['slug']) ? ['slug' => $sslug] : null;
            if ($slug && !term_exists($sslug, $params['taxonomy'])) {
                $name = 'type' === $params['taxonomy'] ? strtoupper($params['name']) : $params['name'];
                wp_insert_term($name, $params['taxonomy'], $slug);
            }

            $taxonomies = get_terms(['taxonomy' => $params['taxonomy'], 'hide_empty' => false]);
            return new WP_REST_Response(['taxonomies' => $taxonomies]);
        }

        return new WP_REST_Response(['error' => 'Invalid taxonomy'], 400);
    }

    public function get_taxonomies(WP_REST_Request $request)
    {
        $params = $this->get_params($request);

        $taxonomies = get_terms(['taxonomy' => $params['name'], 'hide_empty' => false]);
        return new WP_REST_Response(['taxonomies' => $taxonomies]);
    }

    public function save_theme_key(WP_REST_Request $request)
    {
        $param = $this->get_params($request);
        $key   = $param['key'];

        update_option('__a_act_inline', $key);
        return new WP_REST_Response(['status' => true]);
    }

    public function migrate_remove_tools()
    {
        return new WP_REST_Response(update_option('kiranime_image_migrated', 1));
    }

    public function migrate_thumbnail(WP_REST_Request $request)
    {
        $param = $this->get_params($request);

        $post     = get_post($param['postId']);
        $meta_key = 'anime' === $post->post_type ? ['kiranime_anime_background', 'kiranime_anime_featured'] : 'kiranime_episode_thumbnail';
        $results  = [
            'status' => 0,
            'postId' => $param['postId'],
        ];

        if (is_array($meta_key)) {
            $featured   = get_post_meta($post->ID, 'kiranime_anime_featured', true);
            $background = get_post_meta($post->ID, 'kiranime_anime_background', true);

            $results['meta'] = $featured;
            if (!empty($featured)) {
                $thumb             = Kira_Utility::get_remote_image($featured);
                $results['thumbs'] = $thumb;
                if (isset($thumb['status']) && !empty($thumb['status'])) {
                    set_post_thumbnail($post, $thumb['thumbnail_id']);
                    update_post_meta($post->ID, 'kiranime_anime_featured', $thumb['thumbnail_url']);
                    $results['thumbnail_id'] = $thumb['thumbnail_id'];
                    $results['status']       = 1;
                }
            }
            if (!empty($background)) {
                $thumb             = Kira_Utility::get_remote_image($background);
                $results['thumbs'] = $thumb;
                if (isset($thumb['status']) && !empty($thumb['status'])) {
                    update_post_meta($post->ID, 'kiranime_anime_background', $thumb['thumbnail_url']);
                }
            }
        } else {
            $value           = get_post_meta($post->ID, $meta_key, true);
            $results['meta'] = $value;
            if (!empty($value)) {
                $thumb             = Kira_Utility::get_remote_image($value);
                $results['thumbs'] = $thumb;
                if (isset($thumb['status']) && !empty($thumb['status'])) {
                    set_post_thumbnail($post, $thumb['thumbnail_id']);
                    update_post_meta($post->ID, 'kiranime_episode_thumbnail', $thumb['thumbnail_url']);
                    $results['status']       = 1;
                    $results['thumbnail_id'] = $thumb['thumbnail_id'];
                }
            }
        }

        return new WP_REST_Response($results);
    }

    public function get_act_status()
    {
        $s = get_option('kr_anime_status', false);
        return new WP_REST_Response(['status' => $s]);
    }

    public function get_all_posts(WP_REST_Request $request)
    {
        $param = $this->get_params($request);

        $posts = new WP_Query([
            'post_type'      => ['anime', 'episode'],
            'posts_per_page' => 100,
            'post_status'    => 'publish',
            'paged'          => $param['page'],
        ]);

        $results = [
            'status'    => 1,
            'data'      => [],
            'page'      => $param['page'],
            'max_pages' => $posts->max_num_pages,
        ];
        foreach ($posts->posts as $post) {
            if (!get_post_thumbnail_id($post->ID)) {
                $featured = 'anime' === $post->post_type ? get_post_meta($post->ID, 'kiranime_anime_featured', true) : get_post_meta($post->ID, 'kiranime_episode_thumbnail', true);

                if (!$featured) {
                    continue;
                }

                $results['data'][] = [
                    'id'        => $post->ID,
                    'thumbnail' => 0,
                ];
            }
        }

        return new WP_REST_Response($results);

    }

    public function get_remote_image(WP_REST_Request $request)
    {
        $param = $this->get_params($request);

        if (!$param['image_url']) {
            return new WP_REST_Response(['message' => 'Image url required!']);
        }

        $data = Kira_Utility::get_remote_image($param['image_url']);

        return new WP_REST_Response($data);
    }

    public function add_watchlist(WP_REST_Request $request)
    {
        ['user_id' => $user_id, 'anime_id' => $anime_id, 'type' => $type] = $this->get_params($request);

        $watchlist = new Watchlist($anime_id, $type);
        $result    = 'remove' === $type ? $watchlist->change_or_remove_watchlist(true) : $watchlist->add();

        return new WP_REST_Response($result);
    }

    public function get_watchlist(WP_REST_Request $request)
    {
        $params = $this->get_params($request);

        $watchlist = new Watchlist(0, $params['type']);
        $result    = $watchlist->get_watchlist($params['page']);

        return new WP_REST_Response($result);
    }

    public function show_tooltip(WP_REST_Request $request)
    {
        $id = $request['id'];

        $anime = new Anime($id);

        if (is_wp_error($anime)) {
            return new WP_REST_Response(['status' => false]);
        }
        $anime->get_meta('score', 'rate', 'premiered', 'aired', 'synonyms', 'native', 'duration', 'episodes')->get_taxonomies('type', 'anime_attribute', 'status')->get_episodes(true);

        $meta   = $anime->meta;
        $latest = !empty($anime->episodes) ? $anime->episodes : null;
        $type   = isset($anime->taxonomies['type']) ? $anime->taxonomies['type'] : [];

        ob_start();?>
<div class="relative p-4 max-w-xs w-80 min-w-[17rem] text-sm">
    <div class="font-medium line-clamp-2 mb-3">
        <?php echo $anime->post->post_title ?> </div>
    <div class="flex items-center justify-between mb-3 text-xs">
        <div class="flex items-center gap-4">
            <span class="flex items-center justify-center gap-1">
                <span class="material-icons-round text-lg text-yellow-500">
                    star_rate
                </span>
                <?php if ($meta['score']) {echo $meta['score'];} else {echo '?';}?>
            </span>
            <?php if ($type && in_array($type[0]->slug, ['tv', 'series', 'ona', 'ova']) && $latest && $latest->meta['number']): ?>
            <span class="whitespace-nowrap">
                E
                <?php echo $latest->meta['number']; ?>/<?php echo $meta['episodes'] ? $meta['episodes'] : 12; ?>
            </span>
            <?php endif;?>
        </div>
        <span class="px-2 py-1 text-xs rounded bg-accent-3 uppercase">
            <?php echo !is_wp_error($type) && count($type) > 0 ? $type[0]->name : 'TV'; ?>
        </span>
    </div>
    <span class="w-full text-xs font-montserrat font-light line-clamp-4 mt-5">
        <?php echo $anime->post->post_content; ?>
    </span>
    <div class="text-xs space-y-1 mt-5">
        <?php foreach ($meta as $k => $v): if ($v && 'featured' !== $k): ?>
				        <span class="block"><span class="font-medium"><?=ucfirst(__($k, 'kiranime'))?>:</span>
				            <?php
    if (!is_array($v)):
                echo $k === 'Updated' ? date('d F Y, h:i:s A', $v) : $v;
            elseif (!empty($v)):
                $t_a = array_map(function ($val) {
                    return $val->name;
                }, $v);
                echo implode(', ', $t_a);
            endif;?>
				        </span>
				        <?php endif;endforeach;?>
    </div>
    <div class="mb-2 mt-4 flex items-center gap-2">
        <a href="<?php echo $latest && $latest->url ? $latest->url : $anime->url; ?>"
            class="flex items-center bg-accent-3 text-xs font-semibold px-3 py-2 gap-1 rounded-full w-max shadow drop-shadow justify-center">
            <span class="material-icons-round text-xl">
                play_arrow
            </span>
            <?php _e('Watch Now', 'kiranime');?>
        </a>
        <span data-tippy-sub-trigger="<?php echo $anime->anime_id ?>"
            class="rounded-full cursor-pointer w-max px-3 py-2 flex items-center justify-center gap-1 hover:bg-accent-4 hover:bg-opacity-60 bg-primary/50">
            <span class="material-icons-round text-xl">
                playlist_add
            </span>
            <?php _e('Watchlist', 'kiranime');?>
        </span>
    </div>
</div>
<?php

        $result = ob_get_clean();
        return new WP_REST_Response(['status' => true, 'data' => $result]);
    }

    public function get_public_watchlist(WP_REST_Request $request)
    {
        ['type' => $type] = $this->get_params($request);

        return new WP_REST_Response($type);
    }

    public function delete_watchlist(WP_REST_Request $request)
    {
        ['anime_id' => $anime_id] = $this->get_params($request);

        $watchlist = new Watchlist($anime_id);
        $result    = $watchlist->change_or_remove_watchlist();

        return new WP_REST_Response($result, 200);
    }

    public function try_login(WP_REST_Request $request)
    {
        $params = $this->get_params($request);

        $login = Kira_User::login($params['username'], $params['password'], $params['rememberMe']);

        return new WP_REST_Response($login['data'], $login['status']);
    }

    public function try_register(WP_REST_Request $request)
    {
        ['username' => $username, 'email' => $email, 'password' => $password] = $this->get_params($request);

        $register = Kira_User::register($email, $username, $password);

        return new WP_REST_Response($register['data'], $register['status']);
    }

    public function logout()
    {
        $l = Kira_User::logout();

        return new WP_REST_Response($l);
    }

    public function get_verification_code(WP_REST_Request $request)
    {
        $param = $this->get_params($request);

        $user = new Kira_User;
        $code = $user->get_recovery_verification_code($param['userlogin']);

        return new WP_REST_Response($code['data'], $code['status']);
    }

    public function reset_user_password(WP_REST_Request $request)
    {
        $params = $this->get_params($request);

        $userkira = new Kira_User;
        $user     = $userkira->reset_my_password($params);
        return new WP_REST_Response($user['data'], $user['status']);
    }

    public function upload_avatar(WP_REST_Request $request)
    {
        $upload_handler = Kira_User::upload_avatar(get_current_user_id());

        return new WP_REST_Response($upload_handler['data'], $upload_handler['status']);
    }

    public function change_avatar(WP_REST_Request $request)
    {
        ['avatar' => $avatar, 'user_id' => $user_id] = $this->get_params($request);

        $change = Kira_User::set_avatar($avatar, $user_id);

        return new WP_REST_Response(['success' => $change['data']], $change['status']);
    }

    public function profile_change(WP_REST_Request $request)
    {
        $param = $this->get_params($request);

        $result = Kira_User::save_profile_data($param);

        return new WP_REST_Response($result);
    }

    /**
     * anime REST Handler
     */
    public function add_vote(WP_REST_Request $request)
    {
        $params = $this->get_params($request);
        $set    = new Anime(absint($params['anime_id']));
        $set->get_votes()->add_vote(absint($params['value']));

        return new WP_REST_Response($set, 200);
    }

    public function get_anime_title(WP_REST_Request $request)
    {
        ['query' => $query] = $this->get_params($request);

        $results = new Kira_Query();
        $r       = $results->admin_search($query);

        return new WP_REST_Response($r['data']);
    }

    public function search_anime(WP_REST_Request $request)
    {
        ['query' => $query] = $this->get_params($request);

        $q = new Kira_Query();
        $r = $q->search_query($query);
        return new WP_REST_Response($r, 200);
    }
    public function advancedsearch_anime(WP_REST_Request $request)
    {
        $query = $this->get_params($request);

        $q = new Kira_Query([], 'anime', false, true);
        $r = $q->advanced_search($query);

        return new WP_REST_Response($r, 200);
    }

    public function get_popular_anime(WP_REST_Request $request)
    {
        ['by' => $by] = $this->get_params($request);
        $suffix       = '_kiranime_views';
        $queries      = [
            'day'   => date('dmY') . $suffix,
            'week'  => date('WY') . $suffix,
            'month' => date('FY') . $suffix,
        ];
        $q = new Kira_Query(['orderby' => 'meta_value_num', 'meta_key' => $queries[$by], 'meta_value' => 0, 'meta_compare' => '>', 'posts_per_page' => 10]);

        return new WP_REST_Response($q->animes);
    }

    public function create_anime(WP_REST_Request $request)
    {
        ['data' => $anime, 'type' => $type] = $this->get_params($request);

        // $is_shiki            = isset($type) && !empty($type) && 'shikimori' === $type;
        $imported = new Anime();
        // $imported->shikimori = $is_shiki;
        $result = $imported->create_anime($anime);

        return new WP_REST_Response($result);
    }

    public function add_view(WP_REST_Request $request)
    {
        $params = $this->get_params($request);
        $id     = $params['id'];
        if (!$id) {
            return new WP_REST_Response(['status' => false, 'err' => 'Id not defined']);
        }

        $anime = new Anime($id);
        $anime->add_view();
        return new WP_REST_Response(['status' => true, 'views' => $anime->statistic]);
    }

    public function get_random_anime()
    {
        $anime_link = new Kira_Query([
            'post_type'      => 'anime',
            'post_status'    => 'publish',
            'order'          => 'desc',
            'orderby'        => 'rand',
            'posts_per_page' => 1,
            'no_found_rows'  => 1,
        ]);
        $data = '';
        if (!$anime_link->empty) {
            $anime = array_shift($anime_link->animes);
            $data  = $anime->url;
        }

        return new WP_REST_Response($data, 200);
    }

    /**
     * Episode REST Handler
     */

    public function get_scheduled_episode(WP_REST_Request $request)
    {
        ['day' => $day, 'month' => $month, 'year' => $year] = $this->get_params($request);

        $scheduled = new Kira_Query([
            'post_type'      => 'episode',
            'post_status'    => 'future',
            'order'          => 'ASC',
            'orderby'        => 'date',
            'posts_per_page' => -1,
            'date_query'     => [
                [
                    'year'    => $year,
                    'month'   => $month,
                    'day'     => $day,
                    'compare' => '=',
                ],
            ],
        ], 'episode');
        $results = [];
        $posts   = $scheduled->episodes ? $scheduled->episodes : [];
        foreach ($posts as $post) {
            $post->get_meta('number', 'released', 'parent_name', 'parent_id', 'parent_slug');
            $results[] = $post;
        }

        return new WP_REST_Response($results, 200);
    }

    public function create_episode(WP_REST_Request $request)
    {
        ['data' => $episode] = $this->get_params($request);

        $created = new Episode(0, $episode);
        $created->create_episode($episode)->update_meta()->get_meta('parent_id', 'anime_season', 'parent_romaji', 'anime_id', 'anime_type', 'number')->fetch_vid();

        return new WP_REST_Response($created);
    }

    public function import_episode(WP_REST_Request $request)
    {
        ['data' => $data] = $this->get_params($request);

        $imported = new Episode();
        $result   = $imported->import_episode($data);

        return new WP_REST_Response($result);
    }

    public function report_problem(WP_REST_Request $request)
    {
        ['data' => $data] = $this->get_params($request);

        $res = Kira_Utility::save_report($data);

        return new WP_REST_Response($res);
    }

    public function delete_report(WP_REST_Request $request)
    {
        $param = $this->get_params($request);

        $deleted = wp_delete_post($param['report_id'], true);

        return new WP_REST_Response($deleted);
    }

    /**
     * Notifications REST Handler
     */

    public function get_notification(WP_REST_Request $request)
    {
        ['user_id' => $user_id] = $this->get_params($request);

        $nt = Kiranime_Notification::get(true, $user_id);

        return new WP_REST_Response($nt, 200);
    }

    public function check_notification(WP_REST_Request $reques)
    {
        ['user_id' => $user_id, 'notification_id' => $notification_id] = $this->get_params($reques);

        $nt = Kiranime_Notification::checked($notification_id, $user_id);

        return new WP_REST_Response($nt, 200);
    }

    public function delete_notification(WP_REST_Request $reques)
    {
        ['user_id' => $user_id, 'notification_id' => $notification_id] = $this->get_params($reques);

        $delete = Kiranime_Notification::delete($notification_id, $user_id);

        return new WP_REST_Response($delete['data'], $delete['status']);
    }

    public function save_setting(WP_REST_Request $request)
    {
        $params = $this->get_params($request);

        $result = Kira_Utility::save_setting($params);

        return new WP_REST_Response($result['data'], $result['status']);
    }

    // search index
    public function get_search_index_ids()
    {
        $res = new Kiranime_Search_Index;
        $res = $res->start_indexing();

        return new WP_REST_Response(['data' => $res, 'status' => true]);
    }

    public function build_search_index(WP_REST_Request $request)
    {
        $res   = new Kiranime_Search_Index();
        $build = $res->build_search_index(intval($request['id']));

        return new WP_REST_Response(['status' => true, 'data' => $build]);
    }

    public function get_global_nonce()
    {
        return new WP_REST_Response([
            'nonce' => wp_create_nonce('wp_rest'),
        ], 200);
    }

    public function is_allowed()
    {
        return current_user_can('read');
    }
}
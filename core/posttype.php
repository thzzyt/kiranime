<?php

function kiranime_register_post_type()
{
    $pts = [
        'Anime'        => [
            'name'       => 'Anime',
            'supports'   => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments'],
            'taxonomies' => ['post_tag'],
            'rewrite'    => ['slug' => false, 'with_front' => false],
        ],
        'Episode'      => [
            'name'     => 'Episode',
            'supports' => ['title', 'author', 'thumbnail', 'comments'],
            'rewrite'  => ['slug' => 'watch'],
        ],
        'Notification' => [
            'name' => 'notification',
            'args' => [
                'label'               => 'notifications',
                'description'         => 'Notification post',
                'supports'            => ['title', 'author'],
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => false,
                'show_in_menu'        => false,
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => false,
                'menu_position'       => 6,
                'can_export'          => false,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'post',
                'show_in_rest'        => false,
            ],
        ],
        'Reported'     => [
            'name' => 'reported',
            'args' => [
                'label'               => 'reports',
                'description'         => 'reported problems',
                'supports'            => ['title', 'author'],
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => false,
                'show_in_menu'        => false,
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => false,
                'menu_position'       => 6,
                'can_export'          => false,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'post',
                'show_in_rest'        => false,
            ],
        ],
    ];

    foreach ($pts as $name => $pt) {
        $label = [
            'name'              => $pt['name'] . 's',
            'singular_name'     => $pt['name'],
            'menu_name'         => $name,
            'parent_item_colon' => 'Parent ' . $pt['name'],
            'all_items'         => 'All ' . $pt['name'],
            'view_item'         => 'View ' . $pt['name'],
        ];

        if (in_array($name, ['Notification', 'Reported'])) {
            $args           = $pt['args'];
            $args['labels'] = $label;

        } else {
            $args = [
                'label'               => $pt['name'],
                'description'         => $pt['name'] . ' post',
                'labels'              => $label,
                'supports'            => $pt['supports'],
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 5,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'show_in_rest'        => true,
                'rewrite'             => $pt['rewrite'],
            ];
        }

        if (isset($pt['taxonomies'])) {
            $args['taxonomies'] = $pt['taxonomies'];
        }

        register_post_type(strtolower($name), $args);
    }
}
add_action('init', 'kiranime_register_post_type', 10);

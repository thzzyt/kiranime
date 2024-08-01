<?php

if (class_exists('Kirki')) {
    /**
     * panels
     */
    $panels = [
        'kiranime_homepage_customizer'       => [
            'priority'    => 10,
            'title'       => 'Kiranime: Homepage',
            'description' => 'Kiranime homepage setting',
        ],
        'kiranime_color_customizer'          => [
            'priority'    => 10,
            'title'       => 'Kiranime: Color',
            'description' => 'Kiranime color setting',
        ],
        'kiranime_single_setting_customizer' => [
            'priority'    => 10,
            'title'       => 'Kiranime: Single Post',
            'description' => 'Kiranime single post setting',
        ],
        'kiranime_ads_customizer'            => [
            'priority'    => 10,
            'title'       => 'Kiranime: Advertisement',
            'description' => 'Kiranime Advertisement Setting',
        ],
        'kiranime_social_customizer'         => [
            'priority'    => 10,
            'title'       => 'Kiranime: Social Media',
            'description' => 'Kiranime Social Media Setting',
        ],
        'kiranime_other_customizer'          => [
            'priority'    => 10,
            'title'       => 'Kiranime: Others',
            'description' => 'Kiranime Other Setting',
        ],

    ];

    foreach ($panels as $name => $panel) {
        new \Kirki\Panel($name, $panel);
    }

    $dataset = [
        [
            'kiranime_spotlight_section' => [
                'section' => [
                    'title'       => 'Spotlight Slider',
                    'description' => 'Spotlight slider setting',
                    'panel'       => 'kiranime_homepage_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_spotlight',
                            'label'       => 'Sportlight?',
                            'section'     => 'kiranime_spotlight_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => '__show_spotlight_by',
                            'label'           => 'Show by',
                            'section'         => 'kiranime_spotlight_section',
                            'default'         => 'updated',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'updated' => 'Updated',
                                'popular' => 'Popular',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_spotlight',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => '__show_spotlight_popular_by',
                            'label'           => 'Popular by',
                            'section'         => 'kiranime_spotlight_section',
                            'default'         => 'total',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'daily'   => 'Daily Views',
                                'weekly'  => 'Weekly Views',
                                'monthly' => 'Monthly Views',
                                'total'   => 'Total Views',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_spotlight_by',
                                    'operator' => '===',
                                    'value'    => 'popular',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => '__show_spotlight_status',
                            'label'           => 'Anime Status',
                            'section'         => 'kiranime_spotlight_section',
                            'default'         => 'all',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'all'       => 'All',
                                'upcoming'  => 'Upcoming',
                                'completed' => 'completed',
                                'airing'    => 'Airing',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_spotlight',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'number',
                        'value' => [
                            'settings' => '__show_spotlight_count',
                            'label'    => 'Total Anime',
                            'section'  => 'kiranime_spotlight_section',
                            'default'  => 10,
                            'choices'  => [
                                'min'  => 1,
                                'max'  => 40,
                                'step' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'kiranime_trending_section'  => [
                'section' => [
                    'title'       => 'Trending Section',
                    'description' => 'Trending section setting',
                    'panel'       => 'kiranime_homepage_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_trending',
                            'label'       => 'trending section?',
                            'section'     => 'kiranime_trending_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'number',
                        'value' => [
                            'settings'        => '__show_trending_count',
                            'label'           => 'Total Anime',
                            'section'         => 'kiranime_trending_section',
                            'default'         => 10,
                            'choices'         => [
                                'min'  => 1,
                                'max'  => 40,
                                'step' => 1,
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_trending',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'kiranime_featured_section'  => [
                'section' => [
                    'title'       => 'Featured Section',
                    'description' => 'Featured section setting',
                    'panel'       => 'kiranime_homepage_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_featured_list',
                            'label'       => 'Featured List',
                            'section'     => 'kiranime_featured_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'number',
                        'value' => [
                            'settings'        => '__featured_count',
                            'label'           => 'Featured count',
                            'section'         => 'kiranime_featured_section',
                            'default'         => 7,
                            'choices'         => [
                                'min'  => 1,
                                'max'  => 20,
                                'step' => 1,
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_featured_list',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => 'show_top_airing',
                            'label'           => 'Top Airing List',
                            'section'         => 'kiranime_featured_section',
                            'default'         => 'show',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_featured_list',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => 'show_most_popular',
                            'label'           => 'Most Popular List',
                            'section'         => 'kiranime_featured_section',
                            'default'         => 'show',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_featured_list',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => 'show_most_favorite',
                            'label'           => 'Most Favorite List',
                            'section'         => 'kiranime_featured_section',
                            'default'         => 'show',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_featured_list',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => 'show_completed_series',
                            'label'           => 'Completed List',
                            'section'         => 'kiranime_featured_section',
                            'default'         => 'show',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_featured_list',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'kiranime_single_anime_section'   => [
                'section' => [
                    'title'       => 'Single Anime',
                    'description' => 'Single anime setting',
                    'panel'       => 'kiranime_single_setting_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_episode',
                            'label'       => 'episodes Section',
                            'section'     => 'kiranime_single_anime_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_episode_order',
                            'label'       => 'Episode Order',
                            'section'     => 'kiranime_single_anime_section',
                            'default'     => 'desc',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'desc' => 'DESC',
                                'asc'  => 'ASC',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'text',
                        'value' => [
                            'settings'        => '__show_episode_label',
                            'label'           => 'Label',
                            'section'         => 'kiranime_single_anime_section',
                            'default'         => 'Episode',
                            'active_callback' => [
                                [
                                    'setting'  => '__show_episode',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'number',
                        'value' => [
                            'settings'        => '__show_episode_count',
                            'label'           => 'Total Episode',
                            'section'         => 'kiranime_single_anime_section',
                            'default'         => 0,
                            'choices'         => [
                                'min'  => 1,
                                'max'  => 40,
                                'step' => 1,
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_episode_all',
                                    'operator' => '===',
                                    'value'    => 'no',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_download_anime',
                            'label'       => 'Download list',
                            'section'     => 'kiranime_single_anime_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_related_anime',
                            'label'       => 'Show related anime',
                            'section'     => 'kiranime_single_anime_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'text',
                        'value' => [
                            'settings'        => '__show_related_anime_label',
                            'label'           => 'Related Label',
                            'section'         => 'kiranime_single_anime_section',
                            'default'         => 'Recommended For You!',
                            'active_callback' => [
                                [
                                    'setting'  => '__show_related_anime',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => '__show_related_anime_display',
                            'label'           => 'Related display',
                            'section'         => 'kiranime_single_anime_section',
                            'default'         => 'grid',
                            'choices'         => [
                                'grid'   => 'Grid',
                                'slider' => 'Slider',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_related_anime',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => '__show_related_anime_by',
                            'label'           => 'Related by?',
                            'section'         => 'kiranime_single_anime_section',
                            'default'         => 'genre',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'genre'           => 'Genre',
                                'anime_attribute' => 'Attribute',
                                'status'          => 'Status',
                                'type'            => 'Type',
                                'producer'        => 'Producer',
                                'studio'          => 'Studio',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_related_anime',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'number',
                        'value' => [
                            'settings' => '__show_related_anime_count',
                            'label'    => 'Total Related Anime',
                            'section'  => 'kiranime_single_anime_section',
                            'default'  => 12,
                            'choices'  => [
                                'min'  => 1,
                                'max'  => 40,
                                'step' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'kiranime_single_episode_section' => [
                'section' => [
                    'title'       => 'Single Episode',
                    'description' => 'Single episode setting',
                    'panel'       => 'kiranime_single_setting_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_download_episode',
                            'label'       => 'Download section',
                            'section'     => 'kiranime_single_episode_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_related_episode',
                            'label'       => 'Related anime',
                            'section'     => 'kiranime_single_episode_section',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'text',
                        'value' => [
                            'settings'        => '__show_related_episode_label',
                            'label'           => 'Recommendation label',
                            'section'         => 'kiranime_single_episode_section',
                            'default'         => 'Recomended For You',
                            'active_callback' => [
                                [
                                    'setting'  => '__show_related_episode',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => '__show_related_episode_display',
                            'label'           => 'Related display',
                            'section'         => 'kiranime_single_episode_section',
                            'default'         => 'grid',
                            'choices'         => [
                                'grid'   => 'Grid',
                                'slider' => 'Slider',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_related_episode',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'        => '__show_related_episode_by',
                            'label'           => 'Related by?',
                            'section'         => 'kiranime_single_episode_section',
                            'default'         => 'genre',
                            'placeholder'     => 'Choose an option',
                            'choices'         => [
                                'genre'           => 'Genre',
                                'anime_attribute' => 'Attribute',
                                'status'          => 'Status',
                                'type'            => 'Type',
                                'producer'        => 'Producer',
                                'studio'          => 'Studio',
                            ],
                            'active_callback' => [
                                [
                                    'setting'  => '__show_related_episode',
                                    'operator' => '===',
                                    'value'    => 'show',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'  => 'number',
                        'value' => [
                            'settings' => '__show_related_episode_count',
                            'label'    => 'Total Related Anime',
                            'section'  => 'kiranime_single_episode_section',
                            'default'  => 12,
                            'choices'  => [
                                'min'  => 1,
                                'max'  => 40,
                                'step' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ],
        [
            'kiranime_tooltip_sidebar' => [
                'section' => [
                    'title'       => 'Tooltip & Sidebar',
                    'description' => 'Tooltip & Sidebar setting',
                    'panel'       => 'kiranime_other_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_tooltip',
                            'label'       => 'Show tooltip?',
                            'section'     => 'kiranime_tooltip_sidebar',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_sidebar',
                            'label'       => 'Show sidebar?',
                            'section'     => 'kiranime_tooltip_sidebar',
                            'default'     => 'show',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],
                ],
            ],
            'kiranime_show_share'      => [
                'section' => [
                    'title'       => 'Sharing',
                    'description' => 'Sharing setting',
                    'panel'       => 'kiranime_other_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'select',
                        'value' => [
                            'settings'    => '__show_share_button',
                            'label'       => 'Show share buttons?',
                            'section'     => 'kiranime_show_share',
                            'default'     => 'hide',
                            'placeholder' => 'Choose an option',
                            'choices'     => [
                                'show' => 'Show',
                                'hide' => 'Hide',
                            ],
                        ],
                    ],

                ],
            ],
            'kiranime_archive_setting' => [
                'section' => [
                    'title'       => 'Archive',
                    'description' => 'Archive setting',
                    'panel'       => 'kiranime_other_customizer',
                    'priority'    => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'number',
                        'value' => [
                            'settings' => '__archive_count',
                            'label'    => 'Total anime',
                            'section'  => 'kiranime_archive_setting',
                            'default'  => 24,
                            'choices'  => [
                                'min'  => 1,
                                'max'  => 40,
                                'step' => 1,
                            ],
                        ],
                    ],
                ],
            ],

        ],
        ['kiranime_footer_image' => [
            'section' => [
                'title'       => 'Footer Image',
                'description' => 'Footer Image',
                'panel'       => 'kiranime_other_customizer',
                'priority'    => 160,
            ],
            'fields'  => [
                [
                    'type'  => 'image',
                    'value' => [
                        'settings'   => '__footer_image',
                        'label'      => 'Upload Custom Footer Image',
                        'section'    => 'kiranime_footer_image',
                        'default'    => '',
                        'height'     => '100px',
                        'width'      => '100px',
                        'flex_width' => true,
                    ],
                ],
            ],
        ],
        ],
        [
            'kiranime_color_section' => [
                'section' => [
                    'title'    => 'Change Color',
                    'panel'    => 'kiranime_color_customizer',
                    'priority' => 160,
                ],
                'fields'  => [
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'primary-color',
                            'label'    => 'Primary Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#202125',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'primary-darker-color',
                            'label'    => 'Primary Darker Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#14151a',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'primary-darkest-color',
                            'label'    => 'Primary Darkest Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#121315',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'secondary-color',
                            'label'    => 'Secondary Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#4a4b51',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'tertiary-color',
                            'label'    => 'Tertiary Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#414248',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'overlay-color',
                            'label'    => 'Overlay Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#2a2c31',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'accent-color',
                            'label'    => 'Accent Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#38bdf8',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'accent-2-color',
                            'label'    => 'Accent 2 Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#0ea5e9',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'accent-3-color',
                            'label'    => 'Accent 3 Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#0284c7',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'accent-4-color',
                            'label'    => 'Accent 4 Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#0369a1',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'text-color',
                            'label'    => 'Text Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#fff',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'text-accent-color',
                            'label'    => 'Text Accent Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#f4f4f5',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'text-accent-2-color',
                            'label'    => 'Text Accent 2 Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#71717a',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'error-color',
                            'label'    => 'Error Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#f43f5e',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'error-accent-color',
                            'label'    => 'Error Accent Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#e11d48',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'error-accent-2-color',
                            'label'    => 'Error Accent 2 Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#be123c',
                        ],
                    ],
                    [
                        'type'  => 'color',
                        'value' => [
                            'settings' => 'error-accent-3-color',
                            'label'    => 'Error Accent 3 Color',
                            'section'  => 'kiranime_color_section',
                            'default'  => '#fb7185',
                        ],
                    ],
                ],
            ],
        ],
    ];

    foreach ($dataset as $data) {
        foreach ($data as $label => $setting) {
            new \Kirki\Section($label, $setting['section']);

            foreach ($setting['fields'] as $field) {
                switch ($field['type']) {
                    case 'select':
                        new \Kirki\Field\Select($field['value']);
                        break;
                    case 'text':
                        new \Kirki\Field\Text($field['value']);
                        break;
                    case 'number':
                        new \Kirki\Field\Number($field['value']);
                        break;
                    case 'code':
                        new \Kirki\Field\Code($field['value']);
                        break;
                    case 'image':
                        new \Kirki\Field\Image($field['value']);
                        break;
                    case 'color':
                        new \Kirki\Field\Color($field['value']);
                        break;
                    case 'checkbox':
                        new \Kirki\Field\Checkbox($field['value']);
                        break;
                }
            }
        }
    }
}

<?php

class Render
{
    private function get_active_featured()
    {
        $data    = [];
        $default = [
            'popular'   => [
                'archive' => '?orderby=popular',
                'title'   => __('Most Popular', 'kiranime'),
                'query'   => [
                    'orderby'       => 'meta_value_num',
                    'meta_key'      => 'total_kiranime_views',
                    'meta_value'    => 0,
                    'meta_compare'  => '>',
                    'no_found_rows' => true,
                    'tax_query'     => [
                        [
                            'taxonomy' => 'status',
                            'field'    => 'slug',
                            'terms'    => ['airing', 'completed', 'not-yet-aired', 'upcoming', 'upcomming'],
                        ],
                    ],
                ],
            ],
            'completed' => [
                'archive' => '?orderby=popular&status[]=completed',
                'title'   => __('Completed Series', 'kiranime'),
                'query'   => [
                    'orderby'       => 'date',
                    'order'         => 'DESC',
                    'no_found_rows' => true,
                    'tax_query'     => [
                        [
                            'taxonomy' => 'status',
                            'field'    => 'slug',
                            'terms'    => ['completed'],
                        ],
                    ],
                ],
            ],
            'favorite'  => [
                'archive' => '?orderby=favorite&asp=1',
                'title'   => __('Most Favorite', 'kiranime'),
                'query'   => [
                    'orderby'       => 'meta_value_num',
                    'meta_key'      => 'bookmark_count',
                    'meta_query'    => [
                        [
                            'key'     => 'bookmark_count',
                            'value'   => '0',
                            'compare' => '>',
                        ],
                    ],
                    'no_found_rows' => true,
                ],
            ],
            'airing'    => [
                'archive' => '?orderby=popular&status[]=airing',
                'title'   => __('Top Airing!', 'kiranime'),
                'query'   => [
                    'orderby'       => 'meta_value_num',
                    'meta_key'      => 'total_kiranime_views',
                    'no_found_rows' => true,
                    'tax_query'     => [
                        [
                            'taxonomy' => 'status',
                            'field'    => 'slug',
                            'terms'    => ['airing'],
                        ],
                    ],
                ],
            ],
        ];
        foreach (['airing', 'popular', 'favorite', 'completed'] as $name) {
            $data[$name] = [
                'title'   => $default[$name]['title'],
                'query'   => $default[$name]['query'],
                'active'  => get_theme_mod('__show_featured_' . $name, 'show') === 'show',
                'archive' => Kira_Utility::page_link('pages/search.php') . $default[$name]['archive'],
            ];
        }

        return $data;
    }

    public static function featured()
    {
        $r       = new Self;
        $configs = $r->get_active_featured();
        ob_start();
        foreach ($configs as $part => $opts):
            if ($opts['active']) {
                get_template_part('template-parts/sections/home', 'featured', $opts);
            }
        endforeach;

        $cached = ob_get_clean();

        echo $cached;
    }
}

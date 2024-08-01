<?php

class Kira_Query
{
    /**
     * all posts without processing
     */
    public $posts;

    /**
     * total posts count
     */
    public $count;

    /**
     * all animes with processing
     */
    public $animes;

    /**
     * all episodes with processing
     */
    public $episodes;

    /**
     * base query
     */
    public $base = [
        'post_status'    => 'publish',
        'order'          => 'DESC',
        'orderby'        => 'date',
        'posts_per_page' => 10,
    ];

    /**
     * query used by wp_query class
     */
    public $query = [];

    /**
     * query type
     */
    public $type = 'anime';

    /**
     * if empty
     */
    public $empty = false;

    /**
     * max_pagination
     */
    public $pages = 0;

    /**
     * wp query
     */
    public $wp_query;

    public function __construct($query = [], $type = 'anime', $regex = false, $manual = false)
    {
        if ($regex) {
            $this->query = array_merge($this->base, array_merge(['post_type' => $type], $query));
        }
        if (!empty($query) && !$regex && !$manual) {
            $this->query = array_merge($this->base, array_merge(['post_type' => $type], $query));
            $this->type  = $type;

            $this->get_results();
        }
    }

    private function set_results($res)
    {
        $this->wp_query = $res;

        /**
         * pagination
         */
        $this->pages = $res->max_num_pages;

        /**
         * set total posts count
         */
        $this->count = $res->post_count;

        /**
         * set posts
         */
        $this->posts = $res->posts;

        /**
         * if posts is not found
         */
        $this->empty = empty($res->posts);

        /**
         * if this query is for anime post type, get the anime data and set animes.
         */
        if ('anime' === $this->type) {
            $animes = [];
            foreach ($res->posts as $post) {
                $animes[] = new Anime($post->ID);
            }

            $this->animes = $animes;
        } else if ('episode' === $this->type) {
            $episodes = [];
            foreach ($res->posts as $post) {
                $episodes[] = new Episode($post->ID);
            }

            $this->episodes = $episodes;
        }
    }

    private function get_results()
    {
        /**
         * process the query using wp_query
         */
        $res = new WP_Query($this->query);

        $this->set_results($res);

    }

    public function spotlight()
    {
        $args = [
            'post_type'      => 'anime',
            'post_status'    => 'publish',
            'posts_per_page' => get_theme_mod('__show_spotlight_count', 10),
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'   => 'kiranime_anime_spotlight',
                    'value' => 'on',
                ],
                [
                    'key'   => 'kiranime_anime_spotlight',
                    'value' => '1',
                ],
            ],
        ];

        $spotlight_by = get_theme_mod('__show_spotlight_by', 'popular');
        if ('popular' !== $spotlight_by) {
            $args['meta_key'] = 'kiranime_anime_updated';
        } else {
            $popular_by = get_theme_mod('__show_spotlight_popular_by', 'total');
            $suffix     = '_kiranime_views';
            switch ($popular_by) {
                case 'daily':
                    $args['meta_key'] = date('dmY') . $suffix;
                    break;
                case 'weekly':
                    $args['meta_key'] = date('WY') . $suffix;
                    break;
                case 'monthly':
                    $args['meta_key'] = date('FY') . $suffix;
                    break;

                default:
                    $args['meta_key'] = 'total' . $suffix;
                    break;
            }
        }

        $anime_status = get_theme_mod('__show_spotlight_status', 'all');
        if ('all' !== $anime_status) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'status',
                    'field'    => 'slug',
                    'terms'    => [$anime_status],
                ],
            ];
        }

        $res         = new WP_Query($args);
        $this->query = $res;

        $this->set_results($res);
        return $this;
    }

    public function search_query($query = '')
    {

        $result  = $this->advanced_search(['keyword' => $query, 'single' => []]);
        $results = '';
        foreach ($result['data'] as $p) {
            $anime = $p;
            $type  = isset($anime->taxonomies['type']) ? $anime->taxonomies['type'][0]->name : '';
            $image = get_the_post_thumbnail($anime->post, 'smallthumb', ['class' => 'inset-0 absolute object-cover h-full w-auto min-w-[3rem]', 'alt' => $anime->post->post_title]);
            $results .= '<a href="' . $anime->url . '" class="w-full p-2 border-b border-dashed border-gray-400 border-opacity-30 flex gap-2">
            <div class="pb-17 relative overflow-hidden w-12 h-full flex-shrink-0 flex-grow-0">
            ' . $image . '
            </div>
            <div class="flex-auto group">
                <h3 class="text-[13px] font-medium line-clamp-1 leading-6 group-hover:text-accent">' . $anime->post->post_title . '</h3>
                <div class="flex items-center flex-wrap gap-1 text-xs">
                    <span class="block w-full">' . $anime->meta['premiered'] . '</span><span class="uppercase">' . $type . '</span><span class="material-icons-round text-base">arrow_right</span><span>' . $anime->meta['duration'] . '</span>
                </div>
            </div>
        </a>';
        }

        return ['result' => $results];
    }
    public function advanced_search($query = [])
    {
        // convert to associative array
        $jdec = json_decode(json_encode($query), true);

        // sanitize query parameters
        $sanitized_single_query = [];
        foreach ($jdec['single'] as $key => $value) {
            $sanitized_single_query[$key] = sanitize_text_field($value);
        }

        // sanitize keyword
        $keyword = sanitize_text_field($jdec['keyword']);

        $count  = get_theme_mod('__archive_count', 24);
        $dquery = array_merge($this->base, ['post_type' => 'anime', 'fields' => 'ids', 'orderby' => 'meta_value_num', 'meta_key' => 'total_kiranime_views', 'posts_per_page' => $count], $sanitized_single_query);

        if (isset($jdec['tax']) && !empty($jdec['tax'])) {
            $tax_data = array_map(function ($val) {
                return array_merge($val, [
                    'operator' => 'genre' === $val['taxonomy'] ? 'AND' : "IN",
                    'field'    => 'term_id']);
            }, $jdec['tax']);
            $dquery['tax_query'] = array_merge($tax_data, ['relation' => 'AND']);
        }
        if ($keyword) {
            $dquery['meta_query'] = [
                'relation' => 'AND',
                [
                    'key'     => 'kiranime_anime_search_index',
                    'value'   => $keyword,
                    'compare' => "REGEXP",
                ],
            ];
        } else {
            $dquery['meta_query'] = [
                'relation' => 'AND',
            ];
        }
        if ($sanitized_single_query && ($sanitized_single_query['season'] || $sanitized_single_query['year'])) {
            $s = $sanitized_single_query['season'] ? $sanitized_single_query['season'] . ' ' : '';
            $s .= $sanitized_single_query['year'] ? $sanitized_single_query['year'] : '';
            $dquery['meta_query'][] = [
                'key'     => 'kiranime_anime_premiered',
                'value'   => $s,
                'compare' => 'REGEXP',
            ];
        }
        unset($dquery['season']);

        $q       = new WP_Query($dquery);
        $results = [];
        foreach ($q->posts as $p) {
            $anime = new Anime($p);
            $anime->get_image('kirathumb')->get_meta('native', 'premiered', 'synonyms', 'aired', 'rate', 'score', 'duration')->get_taxonomies('type', 'anime_attribute', 'status')->add_view()->get_episodes(true);
            $results[] = $anime;
        }

        $result = ['data' => $results, 'pages' => $q->max_num_pages, 'total' => $q->found_posts];
        return $result;
    }

    public function admin_search(string $query)
    {

        $q = new WP_Query(array_merge($this->base, ['meta_query' => [
            'relation' => 'AND',
            [
                'key'     => 'kiranime_anime_search_index',
                'value'   => $query,
                'compare' => "REGEXP",
            ],
        ], 'post_type' => 'anime', 'posts_per_page' => 10, 'order' => 'ASC', 'orderby' => 'title', 'post_status' => ['draft', 'publish']]));

        $results = [];
        foreach ($q->posts as $post) {
            $results[] = [
                'title'    => $post->post_title,
                'id'       => $post->ID,
                'slug'     => $post->post_name,
                'anime_id' => $post->ID,
                'episodes' => (int) get_post_meta($post->ID, 'kiranime_anime_episodes', true),
            ];
        }

        return ['data' => $results, 'status' => 200];
    }

    public function use_regex($where, $wp_query)
    {
        global $wpdb;
        if ($search_term = $wp_query->get('search_title')) {
            $where .= ' AND ' . $wpdb->posts . '.post_title REGEXP ' . "'" . $search_term . "'";
        }
        return $where;
    }

    public function search_keywords($where, $query)
    {
        global $wpdb;
        if ($search_term = $query->get('keyword')) {
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($search_term)) . '%\'';
        }
        return $where;
    }

    public function get_regex_result()
    {
        add_filter('posts_where', [$this, 'use_regex'], 10, 2);
        $main = new WP_Query($this->query);
        remove_filter('posts_where', [$this, 'use_regex'], 10, 2);

        foreach ($main->posts as $p) {
            $this->animes[] = new Anime($p->ID);
        }

        $this->count = $main->post_count;
        $this->pages = $main->max_num_pages;
        $this->empty = 0 === $main->post_count;

        return $this;
    }

    public function trending()
    {
        $res = new WP_Query([
            'orderby'        => 'meta_value_num',
            'meta_key'       => date('FY') . '_kiranime_views',
            'posts_per_page' => get_theme_mod('__show_trending_count', 10),
            'post_type'      => 'anime',
        ]);
        $this->set_results($res);
        return $this;
    }
}

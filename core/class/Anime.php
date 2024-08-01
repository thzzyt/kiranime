<?php

class Anime
{
    public \WP_Post $post;
    public array $meta;
    public array $taxonomies;
    public $episodes;
    public $anime;
    public $seasons;
    public $statistic;
    public ?string $image;
    public ?string $image_url;
    public ?array $vote;
    public string $url;
    public ?array $scheduled;
    public bool $external  = true;
    private string $prefix = 'kiranime_anime_';

    public function __construct(public string | int $anime_id = 0)
    {
        if ($anime_id) {
            $this->anime_id = $anime_id;
            $this->post     = get_post($anime_id);
            $this->url      = get_post_permalink($anime_id);
        } else {
            return new WP_Error('400', 'Anime id must be provided');
        }
    }

    public function get_meta(...$meta)
    {
        $results = [];
        foreach ($meta as $m) {
            if ('download' === $m) {
                $results[$m] = json_decode(get_post_meta($this->anime_id, 'kiranime_download_data', true), true);
                continue;
            }
            if ('duration' === $m) {
                $r           = get_post_meta($this->anime_id, $this->prefix . $m, true);
                $reg         = '/[^\d.]|(?<!\d)\./';
                $rr          = $r ? preg_replace($reg, '', $r) . 'M' : '24M';
                $results[$m] = $rr && 'M' !== $rr ? $rr : '24M';
                continue;
            }
            $results[$m] = get_post_meta($this->anime_id, $this->prefix . $m, true);
        }
        $this->meta = $results;
        return $this;
    }

    public function get_image($size = 'full', $id = 0, $html = false, $class = "")
    {
        $post_id         = $id ? $id : $this->anime_id;
        $this->image_url = get_the_post_thumbnail_url($post_id, $size);
        if (!$this->image_url) {
            $this->image_url = get_post_meta($post_id, 'kiranime_anime_featured', true);
        }
        $this->image = get_the_post_thumbnail($post_id, $size, ['class' => $class]);

        return $this;
    }

    public function get_image_thumbnail_html($size = 'kirathumb', $classes = "")
    {
        $result = null;
        if (has_post_thumbnail($this->anime_id)) {
            $result = get_the_post_thumbnail($this->anime_id, $size, ['class' => $classes]);
        }

        if (!$result && ($featured = get_post_meta($this->anime_id, 'kiranime_anime_featured', true))) {
            $result = "<img src='$featured' class='$classes'>";
        }

        return $result;
    }

    public function get_taxonomies(...$tax)
    {
        $results = [];
        foreach ($tax as $t) {
            $results[$t] = wp_get_post_terms($this->anime_id, $t);
        }

        $this->taxonomies = $results;
        return $this;
    }

    public function get_statistic()
    {
        $suffix  = '_kiranime_views';
        $queries = [
            'day'   => date('dmY') . $suffix,
            'week'  => date('WY') . $suffix,
            'month' => date('FY') . $suffix,
            'total' => 'total' . $suffix,
        ];

        foreach ($queries as $k => $q) {
            $this->statistic[$k] = get_post_meta($this->anime_id, $q, true);
        }

        return $this;
    }

    public function get_episodes($is_latest = true, $count = -1, ...$episode_meta)
    {
        $results = [];
        $order   = [
            'orderby' => 'date',
        ];
        if (!get_option('__q_use_post_date')) {
            $order = [
                'orderby'  => 'meta_value_num',
                'meta_key' => 'kiranime_episode_number',
            ];
        }

        $episodes = new WP_Query(array_merge([
            'post_type'      => 'episode',
            'post_status'    => 'publish',
            'posts_per_page' => $is_latest ? 1 : $count,
            'order'          => 'DESC',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'   => 'kiranime_episode_parent_id',
                    'value' => $this->anime_id,
                ],
            ],
        ], $order));

        foreach ($episodes->posts as $episode) {
            $ep   = new Episode($episode->ID);
            $keys = isset($episode_meta) && !empty($episode_meta) ? $episode_meta : ['title', 'number', 'released', 'thumbnail'];
            $ep->get_meta('parent_id', ...$keys)->get_taxonomies('episode_type')->get_thumbnail('kirathumb', '', false);
            if ($is_latest) {
                $results = $ep;
            } else {
                $results[] = $ep;
            }
        }

        $this->episodes = $results;
        return $this;
    }

    public function get_season()
    {
        $season_name = isset($this->meta['name']) && !empty($this->meta['name']);
        if (!$season_name) {
            return $this;
        }

        $season_animes = get_posts([
            'post_type'      => 'anime',
            'post_status'    => 'publish',
            'meta_key'       => 'kiranime_anime_season',
            'orderby'        => 'meta_value',
            'order'          => 'asc',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'kiranime_anime_name',
                    'value'   => $this->meta['name'],
                    'compare' => '=',
                ],
            ],
            'posts_per_page' => -1,
        ]);

        $this->seasons = $season_animes;
        return $this;
    }

    public function create_anime($anime = [])
    {
        if (empty($anime)) {
            return null;
        }
        $this->anime  = $anime;
        $search_title = sanitize_title($anime['title']);
        $existence    = get_posts([
            'post_type'      => 'anime',
            'posts_per_page' => 1,
            'post_status'    => ['publish', 'draft', 'future'],
            'name'           => $search_title,
        ]);
        if (!empty($existence)) {
            $aid            = array_shift($existence);
            $this->anime_id = $aid->ID;

            wp_update_post([
                'ID'           => $this->anime_id,
                'post_title'   => $anime['title'],
                'post_content' => $anime['synopsis'] ? $anime['synopsis'] : '',
                'post_status'  => $anime['post_status'],
            ], false, false);
        } else {
            $create_anime = wp_insert_post([
                'post_type'    => 'anime',
                'post_status'  => $anime['post_status'],
                'post_author'  => get_current_user_id(),
                'post_title'   => $anime['title'],
                'post_content' => $anime['synopsis'] ? $anime['synopsis'] : '',
                'post_name'    => $search_title,
            ], true);

            if (is_wp_error($create_anime)) {
                return $create_anime;
            }

            $this->anime_id = $create_anime;
        }

        $this->post = get_post($this->anime_id);
        $this->update_meta($anime)->update_taxonomy($anime)->init_meta();

        if (isset($anime['featured'])) {
            $thumb_id = get_post_thumbnail_id($this->anime_id);

            if (!$thumb_id || '-1' == $thumb_id) {
                $this->set_featured($anime['featured'], '-1');
            }
        }
        $bg = get_post_meta($this->anime_id, 'kiranime_anime_background', true);
        if ((!$bg || stripos($bg, get_bloginfo('url')) === false) && isset($this->anime['background'])) {
            $this->set_background($anime['background']);
        }

        return $this;
    }

    public function update_meta(array $anime)
    {

        $use = !empty($anime) ? $anime : $this->anime;
        foreach (['spotlight', 'rate', 'native', 'synonyms', 'aired', 'premiered', 'duration', 'episodes', 'score', 'updated', 'name', 'season', 'voted', 'voted_by', 'vote_score', 'characters', 'id', 'service_name'] as $meta) {
            $key = $this->prefix . $meta;

            if ('spotlight' === $meta && (!isset($use[$key]) && !isset($use[$meta]))) {
                update_post_meta($this->anime_id, $key, "");
                continue;
            }

            if (!isset($use[$key]) && !isset($use[$meta])) {
                continue;
            }

            $val = isset($use[$meta]) ? $use[$meta] : (isset($use[$key]) ? $use[$key] : "");

            if ('characters' === $meta) {
                $val = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
                update_post_meta($this->anime_id, $key, $val);
                continue;
            }

            update_post_meta($this->anime_id, $key, $val);
            $this->meta[$meta] = $val;
        }

        // update search index meta
        $synonyms = isset($this->meta['synonyms']) ? (is_array($this->meta['synonyms']) ? implode($this->meta['synonyms']) : $this->meta['synonyms']) : '';
        $data     = isset($this->meta['native']) ? [$synonyms, $this->meta['native'], $this->post->post_title, $this->post->post_name] : [$synonyms, $this->post->post_title, $this->post->post_name];
        update_post_meta($this->anime_id, $this->prefix . 'search_index', implode(', ', $data));
        $this->meta['search_index'] = implode(', ', $data);

        if (!metadata_exists('post', $this->anime_id, 'total_kiranime_views')) {
            update_post_meta($this->anime_id, 'total_kiranime_views', 0);
        }

        return $this;
    }

    public function set_featured($url = null, $thumb_id = '-1')
    {
        if (is_null($url) || empty($url)) {
            update_post_meta($this->anime_id, 'kiranime_anime_featured', '');
            return $this;
        }

        if ('-1' !== $thumb_id) {
            update_post_meta($this->anime_id, 'kiranime_anime_featured', $url);
            return $this;
        }

        if (stripos($url, get_bloginfo('url')) !== false) {
            update_post_meta($this->anime_id, 'kiranime_anime_featured', $url);

            if ('-1' === $thumb_id) {
                $image = Kira_Utility::get_remote_image($url);
                if (isset($image['status']) && !empty($image['status'])) {
                    set_post_thumbnail($this->anime_id, $image['thumbnail_id']);
                }
            }

            return $this;
        }

        if (stripos($url, get_bloginfo('url')) === false) {
            $local = Kira_Utility::get_remote_image($url);
            if (isset($local['status']) && !empty($local['status'])) {
                set_post_thumbnail($this->anime_id, $local['thumbnail_id']);
                update_post_meta($this->anime_id, 'kiranime_anime_featured', $local['thumbnail_url']);
            }
        }

        return $this;
    }

    public function set_background($url = null)
    {
        if (is_null($url) || empty($url)) {
            update_post_meta($this->anime_id, 'kiranime_anime_background', '');
            $this->meta['background'] = '';
            return $this;
        }

        if (stripos($url, get_bloginfo('url')) !== false) {
            update_post_meta($this->anime_id, 'kiranime_anime_background', $url);
            $this->meta['background'] = $url;
            return $this;
        }

        $thumb = Kira_Utility::get_remote_image($url);
        if (isset($thumb['status']) && !empty($thumb['status'])) {
            update_post_meta($this->anime_id, 'kiranime_anime_background', $thumb['thumbnail_url']);
            $this->meta['background'] = $thumb['thumbnail_url'];
        }

        return $this;
    }

    public function init_meta()
    {
        $metadata = [
            [
                'key'   => $this->prefix . 'vote_sum',
                'value' => 0,
            ],
            [
                'key'   => $this->prefix . 'vote_data',
                'value' => json_encode([])
            ],
            [
                'key'   => 'total_kiranime_views',
                'value' => 0,
            ],
            [
                'key'   => $this->prefix . '_updated',
                'value' => time(),
            ],
            [
                'key'   => 'bookmark_count',
                'value' => 0,
            ],
        ];

        foreach ($metadata as $meta) {
            $exist = metadata_exists('post', $this->anime_id, $meta['key']);
            if ($exist) {
                continue;
            }

            update_post_meta($this->anime_id, $meta['key'], $meta['value']);
        }

        return $this;
    }

    public function update_taxonomy(array $anime)
    {
        foreach (['anime_attribute', 'type', 'genre', 'producer', 'studio', 'licensor', 'status'] as $taxonomy) {
            $datakey = 'status' === $taxonomy ? 'airing_status' : $taxonomy;
            if (!isset($anime[$datakey])) {
                continue;
            }

            $debug[$taxonomy] = $anime[$taxonomy];
            wp_set_post_terms($this->anime_id, $anime[$datakey], $taxonomy);
        }
        return $this;
    }

    public function add_view()
    {
        $suffix = '_kiranime_views';
        $dates  = [
            date('dmY') . $suffix,
            date('WY') . $suffix,
            date('FY') . $suffix,
            'total' . $suffix,
        ];

        $result = [];
        foreach ($dates as $value) {
            $current = get_post_meta($this->anime_id, $value, true);
            $current = $current ? intval($current) : 0;
            update_post_meta($this->anime_id, $value, $current + 1);
            $result[$value] = $current + 1;
        }

        $last_update = get_post_meta($this->anime_id, 'update_kiranime_views', time());
        $updated     = $last_update && time() - intval($last_update) < DAY_IN_SECONDS;
        if (!$updated) {
            $past_views = [
                date('dmY', strtotime('-1 day')) . $suffix,
                date('WY', strtotime('-1 week')) . $suffix,
                date('FY', strtotime('-1 month')) . $suffix,
            ];

            foreach ($past_views as $date) {
                delete_post_meta($this->anime_id, $date);
            }

            update_post_meta($this->anime_id, 'update_kiranime_views', time());
        }

        $this->statistic = $result;
        return $this;
    }

    public function add_vote($vote_value)
    {
        if (!$vote_value) {
            return $this;
        }

        $data  = get_post_meta($this->anime_id, 'kiranime_anime_vote_data', true);
        $value = !empty($data) && is_string($data) ? json_decode($data, true) : [];

        if ($this->vote['status']) {
            $result = [];
            foreach ($value as $val) {
                if (get_current_user_id() === $val['user']) {
                    $result[] = [
                        'user'  => get_current_user_id(),
                        'value' => absint($vote_value),
                    ];
                    continue;
                }

                $result[] = $val;
            }

            $value = $result;
        } else {
            $value[] = ['user' => get_current_user_id(), 'value' => absint($vote_value)];
        }

        $total_votes = count($value);
        $total_value = 0;
        foreach ($value as $val) {
            $v = is_object($val) ? $val->value : (is_array($val) ? floatval($val['value']) : 1);
            $total_value += $v;
        }

        update_post_meta($this->anime_id, 'kiranime_anime_vote_data', json_encode($value));
        update_post_meta($this->anime_id, 'kiranime_anime_vote_sum', round($total_value / $total_votes, 1));

        return $this->get_votes();

    }

    public function get_votes()
    {
        if (!$this->anime_id) {
            return $this;
        }
        $sum   = get_post_meta($this->anime_id, 'kiranime_anime_vote_sum', true);
        $voted = get_post_meta($this->anime_id, 'kiranime_anime_vote_data', true);
        $voted = isset($voted) && '[]' != $voted ? json_decode($voted, true) : [];

        $search = [];
        if ($voted) {
            $search = [];
            foreach ($voted as $vote) {
                if (get_current_user_id() === $vote['user']) {
                    $search[] = $vote;
                    break;
                }
            }

        }

        $this->vote = [
            'vote_score' => isset($sum) && !empty($sum) ? floatval($sum) : 0,
            'voted'      => !empty($voted) ? count($voted) : 0,
            'status'     => !empty($search) && count($search) > 0,
            'vote_data'  => !empty($search) && count($search) > 0 ? $search[0] : false,
            'html'       => [
                '1' => '<span class="material-icons-round text-3xl text-yellow-200 py-1">sentiment_very_dissatisfied</span>',
                '2' => '<span class="material-icons-round text-3xl text-yellow-200 py-1">sentiment_dissatisfied</span>',
                '3' => '<span class="material-icons-round text-3xl text-yellow-200 py-1">sentiment_neutral</span>',
                '4' => '<span class="material-icons-round text-3xl text-yellow-200 py-1">sentiment_satisfied</span>',
                '5' => '<span class="material-icons-round text-3xl text-yellow-200 py-1">sentiment_very_satisfied</span>',
            ],
        ];
        return $this;
    }

    public function get_scheduled()
    {
        $post = get_posts([
            'post_type'      => 'episode',
            'post_status'    => 'future',
            'orderby'        => 'date',
            'order'          => 'asc',
            'meta_key'       => 'kiranime_episode_parent_id',
            'meta_value'     => $this->anime_id,
            'meta_compare'   => '=',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
            'fields'         => 'ids',
        ]);

        if (empty($post)) {
            $this->scheduled = null;
            return $this;
        }

        $p               = array_shift($post);
        $episode         = new Episode($p);
        $this->scheduled = $episode->get_meta('number', 'released');

        return $this;
    }
}

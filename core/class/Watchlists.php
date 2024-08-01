<?php

class Watchlist
{

    public $is_in_watchlist  = false;
    public $anime_id         = 0;
    public $type             = 'plan_to_watch';
    public $user_id          = 0;
    private $watchlist_cache = 'kira_watchlist_cached';

    public function __construct(int $anime_id = 0, string $type = 'plan_to_watch')
    {
        $this->type    = $type;
        $this->user_id = get_current_user_id();
        if ($anime_id) {
            $this->anime_id = $anime_id;
        }
    }

    public function get_watchlist($page = 1)
    {

        if (!$this->user_id) {
            return [];
        }

        $posts = [
            'post_type'      => 'anime',
            'post_status'    => 'publish',
            'order'          => 'DESC',
            'posts_per_page' => 20,
            'paged'          => $page,
        ];

        if ('all' === $this->type) {
            $posts['meta_query'] = [
                'relation' => 'OR',
            ];

            $types = ['plan_to_watch', 'watching', 'on_hold', 'completed', 'dropped'];
            foreach ($types as $type) {
                $posts['meta_query'][] = [
                    'key'   => 'bookmark_' . $type . '_by',
                    'value' => $this->user_id,
                ];
            }
        } else {
            $posts = array_merge($posts, [
                'meta_key'     => 'bookmark_' . $this->type . '_by',
                'meta_value'   => $this->user_id,
                'meta_compare' => '=',
            ]);

        }

        $queried = new Kira_Query($posts);
        $results = [];
        if (!$queried->empty) {
            foreach ($queried->animes as $anime) {
                $anime->get_image('kirathumb', null, true)->get_meta('native', 'synonyms', 'aired', 'premiered', 'rate', 'episodes', 'duration')->get_taxonomies('anime_attribute', 'status', 'type')->get_episodes(true, -1, 'number');
                $results['animes'][] = $anime;
            }
        }

        $results['pages'] = $queried->pages;

        return $results;
    }

    public function add()
    {
        if (!$this->anime_id || !$this->type) {
            return [];
        }

        $result    = [];
        $types     = $this->watchlist_type();
        $data_type = array_values(array_filter($types, function ($val) {
            return $val['key'] === $this->type;
        }));
        $get_current_list = get_post_meta($this->anime_id, 'bookmark_' . $this->type . '_by', false);
        if (in_array($this->user_id, $get_current_list)) {
            $result = ['success' => true, 'message' => __('Already added!', 'kiranime'), 'data' => $get_current_list];
        } else {
            $removed = $this->change_or_remove_watchlist(true);
            $count   = get_post_meta($this->anime_id, 'bookmark_count', true);
            $count   = isset($count) ? $count : 0;

            $text = __('Anime added to watch list!', 'kiranime');
            switch ($removed['mode']) {
                case 0:
                    update_post_meta($this->anime_id, 'bookmark_count', $count + 1);
                    break;
                case 1:
                    $text = sprintf(esc_html__('Anime has been moved to %1$s', 'kiranime'), $data_type[0]['name']);
                case 2:
                    $text = sprintf(esc_html__('Anime has been moved to %1$s', 'kiranime'), $data_type[0]['name']);
                default:
                    break;
            }

            update_post_meta($this->anime_id, 'bookmark_' . $this->type . '_by', $this->user_id, $this->user_id);

            $result = ['message' => $text, 'r' => $removed];
        }

        return $result;
    }

    public function change_or_remove_watchlist($use_delete = false)
    {
        $types   = ['plan_to_watch', 'watching', 'on_hold', 'completed', 'dropped'];
        $results = false;
        foreach ($types as $type) {
            $key = 'bookmark_' . $type . '_by';
            if (metadata_exists('post', $this->anime_id, $key)) {
                $deleted = delete_post_meta($this->anime_id, $key, $this->user_id);
                if ($deleted) {
                    $results = true;
                }
            }
        }

        $mode    = 0;
        $message = __('Anime removed from watchlist.', 'kiranime');

        if ($use_delete) {
            if ($results) {
                $count = get_post_meta($this->anime_id, 'bookmark_count', true);
                update_post_meta($this->anime_id, 'bookmark_count', $count ? intval($count) - 1 : 0);
                $mode = 1;
            }
        }

        return ['message' => $message, 'status' => true, "mode" => $mode];
    }

    public static function watchlist_type()
    {
        return [
            [
                'key'  => 'plan_to_watch',
                'name' => __('Plan to Watch', 'kiranime'),
            ],
            [
                'key'  => 'watching',
                'name' => __('Watching', 'kiranime'),
            ],
            [
                'key'  => 'on_hold',
                'name' => __('On Hold', 'kiranime'),
            ],
            [
                'key'  => 'completed',
                'name' => __('Completed', 'kiranime'),
            ],
            [
                'key'  => 'dropped',
                'name' => __('Dropped', 'kiranime'),
            ],
            [
                'key'  => 'remove',
                'name' => __('Remove', 'kiranime'),
            ],
        ];
    }

    public static function by_anime($anime)
    {

        $data = (array) get_post_meta($anime, 'bookmark_watching_by', false);

        return $data;
    }
}

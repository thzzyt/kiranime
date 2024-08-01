<?php

class Episode
{
    public $post;
    public $url;
    public $image;
    public $thumbnail_url;
    public $players    = [];
    public $taxonomies = [];
    public $meta;
    public $empty       = false;
    public $video_meta  = '';
    private $embed_type = true;
    private $prefix     = 'kiranime_episode_';

    public function __construct(public string | int $id = 0, public array $update = [])
    {
        if ($this->id) {
            $this->init();
        }
    }

    private function init()
    {
        $this->post = get_post($this->id);

        if (!$this->post) {
            $this->empty = true;
            return $this;
        }
        $this->url = get_post_permalink($this->id);
    }

    public function set_embed_type($type = true)
    {
        $this->embed_type = $type;
    }

    public function get_meta(...$args)
    {

        if (empty($this->id)) {
            return $this;
        }

        $results = [];
        foreach ($args as $arg) {
            if ('download' === $arg) {
                $results[$arg] = json_decode(get_post_meta($this->id, 'kiranime_download_data', true), true);
                continue;
            }
            if ('players' === $arg) {
                $meta          = get_post_meta($this->id, 'kiranime_episode_players', true);
                $str           = is_array($meta) ? $meta : json_decode($meta, true);
                $this->players = $this->processPlayer($str);
                continue;
            }
            $results[$arg] = get_post_meta($this->id, $this->prefix . $arg, true);
            if ('thumbnail' === $arg) {
                $this->image = get_post_meta($this->id, $this->prefix . $arg, true);
            }
        }

        $this->meta = $results;
        return $this;
    }

    public function set_featured($url = null, $thumb_id = false)
    {
        if (is_null($url) || empty($url)) {
            update_post_meta($this->id, 'kiranime_episode_thumbnail', '');
            return $this;
        }

        if (stripos($url, get_bloginfo('url')) !== false) {
            update_post_meta($this->id, 'kiranime_episode_thumbnail', $url);

            if (!$thumb_id || '-1' === $thumb_id) {
                $image = Kira_Utility::get_remote_image($url);
                if (isset($image['status']) && !empty($image['status'])) {
                    set_post_thumbnail($this->id, $image['thumbnail_id']);
                }
            }

            return $this;
        }

        if (stripos($url, get_bloginfo('url')) === false) {
            $local = Kira_Utility::get_remote_image($url);
            if (isset($local['status']) && !empty($local['status'])) {
                set_post_thumbnail($this->id, $local['thumbnail_id']);
                update_post_meta($this->id, 'kiranime_episode_thumbnail', $local['thumbnail_url']);
            }

            return $this;
        }

        return $this;
    }

    public function get_taxonomies(...$args)
    {
        if (empty($this->id)) {
            return $this;
        }

        $results = [];
        foreach ($args as $arg) {
            $results[$arg] = wp_get_post_terms($this->id, $arg);
        }

        $this->taxonomies = $results;
        return $this;
    }

    private function processPlayer($players = [])
    {
        if (is_null($players)) {
            return [];
        }

        $results = [];

        foreach ($players as $player) {
            $url  = is_array($player) ? $player['url'] : $player->url;
            $host = is_array($player) ? $player['host'] : $player->host;
            $type = is_array($player) ? $player['type'] : $player->type;

            if (stripos($url, '-dxd-') !== false) {
                $url = explode('-dxd-', $url);
                $url = base64_decode(array_pop($url));
            }

            if (stripos($url, '_tag_open_') !== false) {
                $url = str_ireplace(['_tag_open_', '_tag_close_'], ['<', '>'], $url);
            }

            if (stripos($url, '[') !== false && $this->embed_type) {
                $url = do_shortcode($url);
            }

            $results[] = [
                'url'  => $url,
                'host' => $host,
                'type' => $type,
            ];
        }

        return $results;
    }

    public function image($size = 'full')
    {
        if (empty($this->id)) {
            return $this;
        }

        $image = get_the_post_thumbnail_url($this->id, $size);
        if ($image) {
            $this->image = $image;
        } else {
            $this->image = isset($this->meta['thumbnail']) ? $this->meta['thumbnail'] : get_post_meta($this->id, $this->prefix . 'thumbnail', true);
        }

        if (!$this->image) {
            $this->image = get_the_post_thumbnail_url($this->meta['parent_id'], 'full');
        }

        return $this;
    }

    public function get_thumbnail($size = 'kirathumb', $class = "", $return = true)
    {
        $result = '';
        if (has_post_thumbnail($this->id)) {
            $result = get_the_post_thumbnail($this->id, $size, ['class' => $class]);
        }

        if (!$result && ($featured = get_post_meta($this->id, $this->prefix . 'thumbnail', true))) {
            $result = "<img src='$featured' class='$class'>";
        }

        if (!$result && $this->meta['parent_id']) {
            $result = get_the_post_thumbnail($this->meta['parent_id'], $size, ['class' => $class]);
        }

        if (!$return) {
            $this->thumbnail_url = $result;
            return $this;
        }
        return $result;
    }

    public function update_meta()
    {
        if (empty($this->id)) {
            return $this;
        }

        $keys = ['number', 'title', 'released', 'parent_id', 'parent_name', 'parent_slug', 'anime_id', 'duration', 'anime_season', 'anime_type', 'tmdb_fetch_episode', 'thumbnail'];

        foreach ($keys as $key) {
            $metakey = $this->prefix . $key;
            $metaval = isset($this->update[$metakey]) ? $this->update[$metakey] : (isset($this->update[$key]) ? $this->update[$key] : null);

            if (!is_null($metaval)) {
                update_post_meta($this->id, $metakey, $metaval);
                $this->meta[$key] = $metaval;
                if ('parent_id' === $key) {
                    update_post_meta($metaval, 'kiranime_anime_updated', time());
                }
            }
        }

        return $this;
    }

    public function update_player($players = null)
    {
        if (!isset($this->id) || is_null($players)) {
            return $this;
        }

        $value = $players;
        if (is_array($players)) {
            $this->players = $players;
            $value         = json_encode($players, JSON_UNESCAPED_UNICODE);
        }
        if (is_string($players)) {
            $value = $players;
            // $value         = json_encode($players, JSON_UNESCAPED_UNICODE);
            $this->players = json_decode($value, true);
        }

        update_post_meta($this->id, 'kiranime_episode_players', $value);
        return $this;
    }

    public function update_taxonomies()
    {
        if (empty($this->id)) {
            return $this;
        }
        $taxonomies = ['episode_type'];
        foreach ($taxonomies as $tax) {
            $tax_ids = [];
            if (!isset($this->update[$tax])) {
                continue;
            }
            if (is_array($this->update[$tax])) {
                foreach ($this->update[$tax] as $tax_arr) {
                    $get_if_exist = term_exists($tax_arr, $tax);
                    if ($get_if_exist) {
                        $tax_ids[] = $get_if_exist['term_id'];
                    } else {
                        $create_tax = wp_insert_term($tax_arr, $tax);
                        if (is_wp_error($create_tax)) {
                            continue;
                        }

                        $tax_ids[] = $create_tax['term_id'];
                    }
                }
            } else {
                if (isset($this->taxonomies[$tax]) && isset($this->taxonomies[$tax][0])) {
                    $tax_ids[] = $this->taxonomies[$tax][0]->ID;
                } else {
                    $create_tax = wp_insert_term($this->update[$tax], $tax);
                    if (is_wp_error($create_tax)) {
                        continue;
                    }

                    $tax_ids[] = $create_tax['term_id'];
                }
            }

            wp_set_post_terms($this->id, $tax_ids, $tax);
        }

        return $this;
    }

    public function create_episode($episode = [])
    {
        if (!$this->update && empty($episode)) {
            return null;
        }
        $is_already_created = get_posts([
            'post_type'   => 'episode',
            'post_status' => ['publish', 'draft', 'future', 'pending'],
            'meta_query'  => [
                'relation' => 'AND',
                [
                    'key'     => 'kiranime_episode_parent_id',
                    'value'   => $this->update['parent_id'],
                    'compare' => '=',
                ],
                [
                    'key'     => 'kiranime_episode_number',
                    'value'   => $this->update['number'],
                    'compare' => '=',
                ],
            ],
            'fields'      => 'ids',
        ]);

        if (!empty($is_already_created)) {
            $this->id   = array_shift($is_already_created);
            $this->post = get_post($this->id);

        } else {
            /**
             * create episode if no episode exist with parent and number exist
             * use release date to define post status
             */
            $defined_release          = isset($this->update['released']) && !empty($this->update['released']) ? strtotime($this->update['released']) : time();
            $this->update['released'] = date('Y-m-d H:i:s', $defined_release);
            $publish_status           = $defined_release > time() && 'draft' !== $this->update['status'] ? 'future' : $this->update['status'];

            $created = wp_insert_post([
                'post_type'   => 'episode',
                'post_status' => $publish_status,
                'post_author' => isset($episode['author']) ? $episode['author'] : get_current_user_id(),
                'post_title'  => $episode['parent_name'] . ' ' . __('Episode', 'kiranime') . ' ' . $episode['number'],
            ]);

            if (is_wp_error($created)) {
                return $this;
            }

            $this->post = get_post($created);
            $this->id   = $created;
        }

        if (isset($this->update['thumbnail'])) {
            $thumb_id = get_post_thumbnail_id($this->id);

            if (!$thumb_id || '-1' == $thumb_id) {
                $this->set_featured($this->update['thumbnail'], '-1');
            }
        }
        return $this;
    }

    public function fetch_vid()
    {
        if (!class_exists('Kiranime_plugin')) {
            return $this;
        }

        /**
         * check if current episode already fetched
         */
        $status = get_post_meta($this->id, 'kiranime_episode_fetch_status', true);

        if ($status) {
            return $this;
        }

        /**
         * check if meta or player is available
         */
        if (!$this->meta || !$this->players) {
            $this->get_meta('anime_id', 'anime_season', 'number', 'parent_id', 'anime_type', 'players');
        }

        /**
         * instantiate kiranime plugin
         */
        $grabbed = new Kiranime_Plugin($this);

        /**
         * get gogo/embedworld players data
         */
        $players = $grabbed->grab();

        /**
         * only merge if the players result are not empty
         */
        if ($players && count($players) > 0) {
            $this->players = array_merge($this->players, $players);
            update_post_meta($this->id, 'kiranime_episode_fetch_status', 1);
        }
        update_post_meta($this->id, 'kiranime_episode_players', json_encode($this->players, JSON_UNESCAPED_UNICODE));
    }

    public function import_episode($episodes = [])
    {
        if (empty($episodes) || !isset($episodes['number'], $episodes['anime'])) {
            return [
                'status' => 0,
                'error'  => _x('Some fields cannot be empty!', 'rest api return', 'kiranime'),
                'fields' => $episodes,
            ];
        }

        $anime = get_posts([
            'post_type'      => 'anime',
            'post_status'    => ['publish', 'draft', 'future'],
            'posts_per_page' => 1,
            'name'           => sanitize_title($episodes['anime']),
        ]);

        if (!$anime || empty($anime)) {
            return [
                'status' => 0,
                'error'  => _x('Anime is not created or the title is not found.', 'rest api return', 'kiranime'),
                'fields' => $episodes,
            ];
        }

        $anime = array_shift($anime);

        $is_exist = get_posts([
            'post_type'   => 'episode',
            'post_status' => ['draft', 'publish', 'pending', 'future'],
            'meta_query'  => [
                'relation' => 'AND',
                [
                    'key'     => 'kiranime_episode_parent_id',
                    'value'   => $anime->ID,
                    'compare' => '=',
                ],
                [
                    'key'     => 'kiranime_episode_number',
                    'value'   => $episodes['number'],
                    'compare' => '=',
                ],
            ],
        ]);
        if (!empty($is_exist)) {
            $eid = $is_exist[0]->ID;
        } else {
            $eid = wp_insert_post([
                'post_type'   => 'episode',
                'post_status' => isset($episodes['status']) ? $episodes['status'] : 'draft',
                'post_author' => get_current_user_id(),
                'post_title'  => isset($episodes['title']) && !empty($episodes['title']) ? $episodes['title'] : $anime->post_title . ' ' . $episodes['number'],
            ]);
        }
        $this->id   = $eid;
        $this->post = get_post($this->id);

        $kiranime_episode_players = [];
        if (isset($episodes['players']) && $episodes['players']) {
            $kiranime_episode_players = $episodes['players'];
        }

        $this->update = array_merge(
            $episodes,
            [
                'episode_type'                 => 'series',
                'kiranime_episode_parent_id'   => $anime->ID,
                'kiranime_episode_number'      => $episodes['number'],
                'kiranime_episode_parent_name' => $anime->post_title,
                'kiranime_episode_parent_slug' => $anime->post_name,
            ]);
        $this->update_meta()->get_meta('players', 'number', 'parent_id', 'parent_name', 'parent_slug', 'anime_id', 'anime_season', 'anime_type')->update_player($kiranime_episode_players)->update_taxonomies();

        if ($episodes['thumbnail']) {
            $this->set_featured($episodes['thumbnail']);
        }
        return [
            'status'                       => 1,
            'error'                        => null,
            'data'                         => $this,
            'fields'                       => $episodes,
            'u'                            => $this->update['kiranime_episode_parent_id'],
            'kiranime_episode_parent_id'   => $anime->ID,
            'kiranime_episode_number'      => $episodes['number'],
            'kiranime_episode_parent_name' => $anime->post_title,
            'kiranime_episode_parent_slug' => $anime->post_name,
            'anime'                        => $anime,
        ];
    }

    public function get_video_meta()
    {
        if (!$this->players || empty($this->players)) {
            return $this;
        }

        $duration = isset($this->meta['duration']) && !empty($this->meta['duration']) ? preg_replace('/[^\d+]/', '', $this->meta['duration']) . 'M' : '24M';
        $released = isset($this->meta['released']) ? $this->meta['released'] : date("D, d M Y H:i:s");
        $url      = is_array($this->players[0]) ? $this->players[0]['url'] : $this->players[0]->url;

        if (stripos($url, '<') !== false) {
            preg_match("/src\=(?:\"|\\')(.+?)(?:\"|\\')/", $url, $output_array);

            $url = $output_array[1] ?? '';
        }

        if (empty($url)) {
            return "";
        }

        $title = isset($this->meta['title']) ? $this->meta['title'] : $this->post->post_title;

        $datetime = new DateTime($released);
        $date     = $datetime->format(DateTime::ATOM);

        $this->video_meta = '<script type="application/ld+json">{
            "@context": "http://schema.org",
            "@type": "VideoObject",
            "name": "' . $this->post->post_title . '",
            "description": "' . $title . '",
            "thumbnailUrl": "' . $this->meta['thumbnail'] . '",
            "uploadDate": "' . $date . '",
            "duration": "PT' . $duration . '",
            "embedUrl": "' . $url . '"
          }</script>';

        return $this;
    }
}

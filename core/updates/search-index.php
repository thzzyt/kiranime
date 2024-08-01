<?php

class Kiranime_Search_Index
{
    public $posts = [];

    public function __construct()
    {}

    public function start_indexing()
    {
        $args = [
            'post_type'      => 'anime',
            'post_status'    => ['draft', 'publish', 'future'],
            'posts_per_page' => 100,
            'order'          => 'ASC',
            'orderby'        => 'date',
            'fields'         => 'ids',
        ];

        $first_query = new WP_Query($args);

        // set first results
        $max_pages    = $first_query->max_num_pages;
        $current_page = 1;
        $this->posts  = array_merge($this->posts, $first_query->posts);

        // iterate over all $max_pages results
        while ($current_page <= $max_pages) {
            $current_page++;
            $args['paged'] = $current_page;

            $query       = new WP_Query($args);
            $max_pages   = $query->max_num_pages;
            $this->posts = array_merge($this->posts, $query->posts);
        }

        return [
            'ids'   => $this->posts,
            'total' => $first_query->found_posts,
        ];
    }

    public function build_search_index($id = 0)
    {
        if (!$id) {
            return false;
        }

        $anime = new Anime($id);

        // required meta data
        $anime->get_meta('synonyms', 'native');
        $synonyms = is_array($anime->meta['synonyms']) ? implode($anime->meta['synonyms']) : $anime->meta['synonyms'];
        $data     = [$synonyms, $anime->meta['native'], $anime->post->post_title];
        update_post_meta($id, 'kiranime_anime_' . 'search_index', implode(', ', $data));
        $anime->meta['search_index'] = implode(', ', $data);
        $anime->cache?->delete('');
        return $anime->meta['search_index'];
    }
}

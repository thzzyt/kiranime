<?php

function load_init_widget_listing($configs = [])
{
    $obsf = base64_encode(json_encode($configs));
    ob_start();?>
<div data-lazy-load-components-id='<?php echo $obsf ?>' data-component-name="listing">
    <?php get_template_part('template-parts/sections/component/use', 'loader')?>
</div>
<?php }

function load_widget_listing_data(array $configs = []): string | false
{

    $data_opts = array_merge([
        'post_count' => 12,
        'post_type'  => 'anime',
        'orderby'    => 'popular',
        'order'      => 'desc',
        'title'      => 'new title',
    ], $configs);
    $orderby = match ($data_opts['orderby']) {
        'popular' => [
            'orderby'  => 'meta_value_num',
            'meta_key' => 'total_kiranime_views',
        ],
        'favorite' => [
            'orderby'  => 'meta_value_num',
            'meta_key' => 'bookmark_count',
        ],
        'updated' => [
            'orderby'  => 'meta_value_num',
            'meta_key' => 'kiranime_anime_updated',
        ],
        'date' => [
            'orderby' => 'date',
        ],
        'title' => [
            'orderby' => 'title',
        ],
        'random' => [
            'orderby' => 'rand',
        ]
    };

    $query = 'anime' === $data_opts['post_type'] ? array_merge([
        'posts_per_page' => intval($data_opts['post_count']),
        'post_type'      => $data_opts['post_type'],
        'order'          => $data_opts['order'],
    ], $orderby) : array_merge([
        'posts_per_page' => intval($data_opts['post_count']),
        'post_type'      => $data_opts['post_type'],
        'order'          => $data_opts['order'],
        'orderby'        => 'date',
    ]);

    if (isset($data_opts['status']) && !empty($data_opts['status']) && 'anime' === $data_opts['post_type']) {
        $query['tax_query'] = [
            'relation' => 'AND',
            [
                'taxonomy' => 'status',
                'field'    => 'slug',
                'terms'    => $data_opts['status'],
            ],
        ];
    }
    $s_q = $data_opts;
    unset($s_q['title']);
    unset($s_q['post_type']);
    unset($s_q['post_count']);
    unset($s_q['display']);

    $opts = [
        'title'   => isset($data_opts['title']) ? $data_opts['title'] : '',
        'query'   => $query,
        'slider'  => isset($data_opts['display']) ? 'slider' === $data_opts['display'] : false,
        'archive' => Kira_Utility::page_link('pages/search.php') . '?' . http_build_query($s_q),
        'loop'    => isset($data_opts['loop']) ? $data_opts['loop'] : true,
    ];

    ob_start();
    get_template_part('template-parts/sections/home', $data_opts['post_type'] . '-listing', $opts);
    $result = ob_get_clean();
    return $result;
}
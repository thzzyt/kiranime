<?php

$keyword = (get_query_var('s_keyword')) ? get_query_var('s_keyword') : '';
$page    = isset($_GET['asp']) ? intval($_GET['asp']) : 1;
$status  = isset($_GET['status']) && is_array($_GET['status']) ? $_GET['status'] : [];
$order   = isset($_GET['order']) ? $_GET['order'] : '';
$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

$s  = get_terms(['taxonomy' => 'status', 'hide_empty' => false]);
$rs = [];
if ($status) {
    foreach ($s as $st) {
        if (in_array($st->slug, $status)) {
            $rs[] = [
                'name' => $st->name,
                'slug' => $st->slug,
                'id'   => $st->term_id,
            ];
        }
    }
}
$genre    = get_terms(['taxonomy' => 'genre', 'hide_empty' => true]);
$producer = get_terms(['taxonomy' => 'producer', 'hide_empty' => true]);
$studio   = get_terms(['taxonomy' => 'studio', 'hide_empty' => true]);
$type     = get_terms(['taxonomy' => 'type', 'hide_empty' => true]);
$val      = [];

foreach (['genre' => $genre, 'producer' => $producer, 'studio' => $studio, 'type' => $type] as $k => $v) {
    $val[$k] = [];
    foreach ($v as $vl) {
        $val[$k][] = $vl;
    }
}

$terms = [
    __('Genre', 'kiranime')    => $val['genre'],
    __('Status', 'kiranime')   => $s,
    __('Producer', 'kiranime') => $val['producer'],
    __('Studio', 'kiranime')   => $val['studio'],
    __('Type', 'kiranime')     => $val['type'],
];
$seasons = [
    [
        'name' => __('Winter', 'kiranime'),
        'slug' => strtolower(__('Winter', 'kiranime')),
        'id'   => 1,
    ],
    [
        'name' => __('Spring', 'kiranime'),
        'slug' => strtolower(__('Spring', 'kiranime')),
        'id'   => 2,
    ],
    [
        'name' => __('Summer', 'kiranime'),
        'slug' => strtolower(__('Summer', 'kiranime')),
        'id'   => 3,
    ],
    [
        'name' => __('Fall', 'kiranime'),
        'slug' => strtolower(__('Fall', 'kiranime')),
        'id'   => 4,
    ],

];
$terms_string   = base64_encode(json_encode($terms));
$seasons_string = base64_encode(json_encode($seasons));

$searchconfigs = [
    'usestatus'  => get_option('__u_status_i_du'),
    'useTooltip' => get_theme_mod('__show_tooltip', 'show') === 'show',
    'watchlist'  => Watchlist::watchlist_type(),
];
get_header();
?>
<script>
var krSconf = "<?php echo base64_encode(json_encode([
    'data'    => $searchconfigs,
    'status'  => $rs,
    'orderby' => $orderby,
    'page'    => $page,
    'keyword' => $keyword,
    'terms'   => $terms,
    'order'   => $order,
    'season'  => $seasons,
])); ?>";
</script>
<section class="mt-0 min-h-screen lg:pt-10 w-11/12 mx-auto" id="advanced-search"></section>
<?php get_footer();
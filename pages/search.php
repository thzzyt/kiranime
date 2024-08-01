<?php

/**
 * Template Name: Advanced Search
 *
 * @package Kiranime
 */

$keyword  = (get_query_var('s_keyword')) ? get_query_var('s_keyword') : '';
$page     = isset($_GET['asp']) ? intval($_GET['asp']) : 1;
$status   = isset($_GET['status']) && is_array($_GET['status']) ? $_GET['status'] : [];
$order    = isset($_GET['order']) ? $_GET['order'] : '';
$orderby  = isset($_GET['orderby']) ? $_GET['orderby'] : '';
$u_genre  = isset($_GET['genre']) && is_array($_GET['genre']) ? $_GET['genre'] : [];
$u_prod   = isset($_GET['producer']) && is_array($_GET['producer']) ? $_GET['producer'] : [];
$u_studio = isset($_GET['studio']) && is_array($_GET['studio']) ? $_GET['studio'] : [];

$s  = get_terms(['taxonomy' => 'status', 'hide_empty' => false]);
$rs = [];
$gs = [];
$ps = [];
$ss = [];

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
$genre = get_terms(['taxonomy' => 'genre', 'hide_empty' => true]);
if ($u_genre) {
    foreach ($genre as $g) {
        if (in_array($g->slug, $u_genre)) {
            $gs[] = [
                'name' => $g->name,
                'slug' => $g->slug,
                'id'   => $g->term_id,
            ];
        }
    }
}
$producer = get_terms(['taxonomy' => 'producer', 'hide_empty' => true]);
if ($u_prod) {
    foreach ($producer as $g) {
        if (in_array($g->slug, $u_prod)) {
            $ps[] = [
                'name' => $g->name,
                'slug' => $g->slug,
                'id'   => $g->term_id,
            ];
        }
    }
}
$studio = get_terms(['taxonomy' => 'studio', 'hide_empty' => true]);
if ($u_studio) {
    foreach ($studio as $g) {
        if (in_array($g->slug, $u_studio)) {
            $ss[] = [
                'name' => $g->name,
                'slug' => $g->slug,
                'id'   => $g->term_id,
            ];
        }
    }
}
$type = get_terms(['taxonomy' => 'type', 'hide_empty' => true]);
$val  = [];

foreach (['genre' => $genre, 'producer' => $producer, 'studio' => $studio, 'type' => $type] as $k => $v) {
    $val[$k] = [];
    foreach ($v as $vl) {
        $val[$k][] = $vl;
    }
}

$terms = [
    'Genre'    => $val['genre'],
    'Status'   => $s,
    'Producer' => $val['producer'],
    'Studio'   => $val['studio'],
    'Type'     => $val['type'],
];
$seasons = [
    [
        'name' => __('Winter', 'kiranime'),
        'slug' => 'winter',
        'id'   => 1,
    ],
    [
        'name' => __('Spring', 'kiranime'),
        'slug' => 'spring',
        'id'   => 2,
    ],
    [
        'name' => __('Summer', 'kiranime'),
        'slug' => 'summer',
        'id'   => 3,
    ],
    [
        'name' => __('Fall', 'kiranime'),
        'slug' => 'fall',
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
    'data'     => $searchconfigs,
    'status'   => $rs,
    'orderby'  => $orderby,
    'page'     => $page,
    'keyword'  => $keyword,
    'terms'    => $terms,
    'order'    => $order,
    'season'   => $seasons,
    'producer' => $ps,
    'genre'    => $gs,
    'studio'   => $ss,
])); ?>";
</script>
<section class="mt-0 min-h-screen pt-17 lg:pt-10 w-11/12 mx-auto" id="advanced-search"></section>
<?php get_footer();
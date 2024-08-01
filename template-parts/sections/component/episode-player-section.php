<?php

$episode = isset($args['episode']) ? $args['episode'] : null;
$anime   = isset($args['anime']) ? $args['anime'] : null;

if (!is_null($episode) && !is_null($anime)):
    $players = $episode->players;
    if ($players) {
        $f_episode_type = isset($players[0]) && isset($players[0]['url']) ? (stripos($players[0]['url'], '<') !== false ? 'html' : null) : null;
    }
    $episode_lists      = isset($anime->episodes) ? array_reverse(json_decode(json_encode($anime->episodes), true)) : [];
    $episode_navigation = [
        'previous' => null,
        'next'     => null,
        'current'  => null,
    ];
    foreach ($episode_lists as $index => $ep) {
        if ($ep['meta']['number'] === $episode->meta['number']) {
            $episode_navigation['previous'] = 0 === $index ? null : $episode_lists[$index - 1];
            $episode_navigation['next']     = is_countable($episode_lists) && (count($episode_lists) - 1) === $index ? null : $episode_lists[$index + 1];
            $episode_navigation['current']  = $ep;
            break;
        }
    }

    $player_data = [
        'sub' => [],
        'dub' => [],
    ];

    foreach ($players as $index => $player) {
        if (!$player['type']) {
            continue;
        }

        $player_data[$player['type']][] = [
            'name' => $player['host'],
            'id'   => base64_encode($player['host'] . $index),
            'url'  => base64_encode($player['url']),
        ];
    }

    $is_movie = empty($episode->taxonomies['episode_type']) ? (empty($anime->taxonomies['type']) ? false : 'movie' == $anime->taxonomies['type'][0]->slug) : 'movie' == $episode->taxonomies['episode_type'][0]->slug;

    if (empty($players)) {
        $thumbnail = $episode->get_thumbnail();
    }
    ?>
		<script>
		var episode_report =
		    <?php echo json_encode(['title' => $episode->post->post_title, 'id' => $episode->id, 'url' => site_url()]) ?>;
		var current_episode = "<?php echo !empty($episode->meta['number']) ? $episode->meta['number'] : get_the_title(); ?>";
		</script>
		<div class="episode-head" style="position: relative;z-index: 2;">
		    <div class="episode-head-breadcrumb hidden lg:block <?php echo $is_movie ? 'xl:px-12' : ''; ?>">
		        <nav aria-label="Breadcrumb" class="text-xs font-medium mb-5">
		            <ol class="flex gap-2 items-center flex-wrap">
		                <li>
		                    <a href="/">
		                        <?php _e('Home', 'kiranime');?>
		                    </a>
		                </li>
		                <li>
		                    <div class="w-1 h-1 bg-gray-500 rounded-full"></div>
		                </li>
		                <?php if ($anime->taxonomies['type']): ?>
		                <li>
		                    <a href="<?=get_term_link($anime->taxonomies['type'][0]);?>">
		                        <?=$anime->taxonomies['type'][0]->name;?>
		                    </a>
		                </li>
		                <li>
		                    <div class="w-1 h-1 bg-gray-500 rounded-full"></div>
		                </li>
		                <?php endif;?>
                <li>
                    <a href="<?=$anime->url;?>">
                        <?=$anime->post->post_title;?>
                    </a>
                </li>
                <li>
                    <div class="w-1 h-1 bg-gray-500 rounded-full"></div>
                </li>
                <li>
                    <a href="<?php the_permalink()?>" aria-current="page" class="text-accent max-w-xs line-clamp-1">
                        <?php the_title()?>
                    </a>
                </li>
            </ol>
        </nav>
    </div>
    <div class="episode-head-main-grid">
        <?php if (!$is_movie): ?>
        <!-- episode lists [only if anime is not movie] -->
        <div class="episode-head episode-list" style="min-height: 201.5px;">
            <div class="episode-list-search-box">
                <span><?php _e('Episode Lists', 'kiranime')?></span>
                <label for="episode_number">
                    <span class="material-icons-round">
                        search
                    </span>
                    <input type="text" data-search-episode-from-list
                        placeholder="<?php _e('Episode number', 'kiranime')?>">
                </label>
            </div>
            <div class="w-full h-full flex items-center justify-center loading-lists">
                <?php get_template_part('template-parts/sections/component/use', 'loader');?>
            </div>
            <div class="episode-list-display-box hidden">
                <?php foreach ($episode_lists as $episode_item): ?>
                <a href="<?php echo $episode_item['url']; ?>"
                    class="episode-list-display-box episode-list-item <?php if (get_the_ID() === $episode_item['id']) {echo 'current-episode';}?>"
                    data-episode-search-query="<?php echo isset($episode_item['meta']['number']) ? $episode_item['meta']['number'] : (isset($episode_item['meta']['title']) && !empty($episode_item['meta']['title']) ? $episode_item['meta']['title'] : $episode_item['post']['post_title']) ?>">
                    <span class="episode-list-item-number">
                        <?php echo isset($episode_item['meta']['number']) ? $episode_item['meta']['number'] : '' ?>
                    </span>
                    <span class="episode-list-item-title">
                        <?php echo isset($episode_item['meta']['title']) && !empty($episode_item['meta']['title']) ? $episode_item['meta']['title'] : $episode_item['post']['post_title'] ?>
                    </span>
                </a>
                <?php endforeach;?>
            </div>
        </div>
        <!-- end episode lists -->
        <?php endif;?>
        <!-- episode player area -->
        <div class="episode-head episode-player <?php echo $is_movie ? 'is-movie' : ''; ?>">
            <div
                class="episode-player-box <?php echo empty($players) ? 'no-player' : ""; ?> <?php echo isset($f_episode_type) && 'html' === $f_episode_type ? 'custom-element' : ''; ?>">

                <?php if (!empty($players)): if (isset($f_episode_type) && 'html' === $f_episode_type):
        echo $players[0]['url'];else: ?>
		                <iframe src="<?php echo $players[0]['url']; ?>"
		                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
		                    allowfullscreen></iframe>
		                <?php endif;else: ?>
                <?=isset($thumbnail) && !empty($thumbnail) ? $thumbnail : $anime->image;?>
                <?php endif;?>
            </div>
            <div class="episode-player-tool">
                <div class="episode-player-tool left-side">
                    <div class="left-side expand" data-expanded="0">
                        <span data-expand-full class="material-icons-round text-2xl">
                            fullscreen
                        </span>
                    </div>

                </div>
                <div class="episode-player-tool right-side">
                    <div class="right-side episode-navigation">
                        <div data-open-nav-episode="<?php echo !is_null($episode_navigation['previous']) ? $episode_navigation['previous']['url'] : 'undefined'; ?>"
                            class="episode-navigation previous-episode" style="<?php if (!$episode_navigation['previous']) {echo 'opacity: 0.5;';}
;?>">
                            <span class="material-icons-round text-2xl"> fast_rewind </span>
                        </div>
                        <div data-open-nav-episode="<?php echo !is_null($episode_navigation['next']) ? $episode_navigation['next']['url'] : 'undefined'; ?>"
                            class="episode-navigation next-episode" style="<?php if (!$episode_navigation['next']) {echo 'opacity: 0.5;';}
;?>">
                            <span class="material-icons-round text-2xl"> fast_forward </span>
                        </div>
                    </div>
                    <div data-tippy-sub-trigger="<?php echo $anime->anime_id ?>" class="right-side watchlist-button">
                        <span class="material-icons-round text-2xl ">
                            playlist_add_circle
                        </span>
                    </div>
                </div>
            </div>
            <div class="episode-player-info">
                <div class="episode-player-info top-side">
                    <div class="top-side episode-information">
                        <span><?php _e("You're watching", 'kiranime')?></span>
                        <span><?php echo sprintf(__('Episode %d', 'kiranime'), $episode->meta['number']) ?></span>
                        <span><?php _e('If current player not working, select other server.', 'kiranime');?></span>
                    </div>
                    <div class="top-side player-selection">
                        <div class="player-selection player-sub">
                            <span class="material-icons-round text-lg"> closed_caption </span>
                            <span><?php _e('SUB', 'kiranime')?>:</span>
                            <?php foreach ($player_data['sub'] as $index => $sub): ?>
                            <span data-embed-id="<?php echo $sub['id'] ?>:<?php echo $sub['url']; ?>"
                                <?php if (0 === $index) {echo 'class="active"';}?>><?php echo $sub['name'] ?></span>
                            <?php endforeach;?>
                        </div>
                        <div class="player-selection player-dub">
                            <span class="material-icons-round text-lg"> mic </span>
                            <span><?php _e('DUB', 'kiranime')?>:</span>
                            <?php foreach ($player_data['dub'] as $index => $dub): ?>
                            <span
                                data-embed-id="<?php echo $dub['id'] ?>:<?php echo $dub['url']; ?>"><?php echo $dub['name'] ?></span>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
                <?php if (isset($anime->scheduled) && !empty($anime->scheduled)): ?>
                <div class="episode-player-info bottom-side">
                    <div class="bottom-side next-scheduled-episode hidden">
                        <span class="text-base mr-1">🚀</span>
                        <span><?php _e('Estimated the next episode will come at', 'kiranime')?></span>
                        <span data-timezone="<?php echo wp_timezone_string(); ?>"
                            data-countdown="<?php echo isset($anime->scheduled->post->post_date_gmt) && !empty($anime->scheduled->post->post_date_gmt) ? $anime->scheduled->post->post_date_gmt : "" ?>"></span>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>
        <!-- end player area -->
        <!-- anime info -->
        <div class="episode-head episode-anime-info">
            <div class="anime-information">
                <div class="anime-featured">
                    <?php echo get_the_post_thumbnail($anime->anime_id, 'kirathumb', ['alt' => $anime->post->post_title . ' thumbnail']); ?>
                </div>
                <div class="anime-data">
                    <h4>
                        <a href="<?php echo $anime->url ?>"
                            title="<?php echo $anime->post->post_title ?>"><?php echo $anime->post->post_title ?></a>
                    </h4>
                    <div class="anime-metadata">
                        <?php if ($anime->meta['rate']): $m = explode(' ', $anime->meta['rate']);?>
		                        <span><?php echo $m[0] ?></span>
		                        <?php endif;?>
                        <?php if (!empty($anime->taxonomies['anime_attribute'])): foreach ($anime->taxonomies['anime_attribute'] as $attr): ?>
		                        <span><?php echo $attr->name ?></span>
		                        <?php endforeach;endif;?>
                        <?php if (!empty($anime->taxonomies['type'])): foreach ($anime->taxonomies['type'] as $type): ?>
		                        <span><?php echo $type->name ?></span>
		                        <?php endforeach;endif;?>
                        <span><?php echo !empty($anime->meta['duration']) ? $anime->meta['duration'] : "24M" ?></span>
                    </div>
                    <div class="anime-synopsis">
                        <div class="anime-synopsis synopsis-content">
                            <p>
                                <?php echo $anime->post->post_content ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="anime-score-box">
                <div class="anime-score-header">
                    <div class="anime-score-counts">
                        <span class="material-icons-round star-icon">
                            star
                        </span>
                        <span>
                            <?php echo empty($anime->vote['vote_score']) ? 0 : floatval($anime->vote['vote_score']) * 2 ?>
                        </span>
                        <span>
                            <?php echo sprintf(__('( %d Voted)', 'kiranime'), empty($anime->vote['voted']) ? 0 : $anime->vote['voted']) ?></span>
                    </div>
                    <span>
                        <?php _e('Vote Now!', 'kiranime')?>
                    </span>
                </div>
                <?php if (!$anime->vote['status']): ?>
                <span class="anime-score-rate-this">
                    <?php _e('Rate this anime!', 'kiranime')?>
                </span>
                <?php endif;?>
                <div class="anime-score-emoticons <?php if ($anime->vote['status']) {echo 'voted';}?>">
                    <?php foreach ($anime->vote['html'] as $index => $emoticon): ?>
                    <div data-vote-note="<?php echo $index ?>"
                        class="<?php echo $anime->vote['status'] ? (empty($anime->vote['vote_data']) ? 'not-voted' : ($anime->vote['vote_data']['value'] === $index ? 'selected' : 'not-voted')) : 'not-voted' ?>">
                        <?php echo $emoticon ?>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
        <!-- end anime info -->
    </div>
</div>
<div class="hidden episode-modal-report">
    <div class="modal-inner">
        <div class="modal-header">
            <h5><?php _e('Please explain the problem you found on this episode.', 'kiranime')?></h5>
        </div>
        <div class="modal-body">
            <textarea id="report-value" rows="4"
                class="px-2 w-full text-sm text-white bg-secondary border-0 focus:ring-0" required="true"></textarea>
        </div>
        <div class="modal-footer">
            <div class="flex justify-end items-center gap-5 py-2">
                <button data-report-cancel
                    class="inline-flex items-center py-2 px-4 text-xs font-medium text-center text-white bg-error-1 rounded-lg focus:ring-2 focus:ring-error hover:bg-error-2">
                    <?php _e('Cancel', 'kiranime')?>
                </button>
                <button data-report-the-problem
                    class="inline-flex items-center py-2 px-4 text-xs font-medium text-center text-white bg-accent-3 rounded-lg focus:ring-2 focus:ring-accent hover:bg-accent-4">
                    <?php _e('Submit', 'kiranime')?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif;
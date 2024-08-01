<?php

class Kiranime_Popular_List extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(

            'Kiranime_Popular_List',

            'Kiranime Popular List', 'Kiranime_Popular_List_domain',

            array('description' => 'Show popular list.', 'Kiranime_Popular_List_domain')
        );
    }

    // Creating widget front-end

    public function widget($args, $instance)
    {
        $title   = apply_filters('widget_title', $instance['title']);
        $suffix  = '_kiranime_views';
        $queries = [
            'day'   => date('dmY') . $suffix,
            'week'  => date('WY') . $suffix,
            'month' => date('FY') . $suffix,
        ];

        $res = [];
        foreach ($queries as $key => $query) {
            $q         = new Kira_Query(['orderby' => 'meta_value_num', 'meta_key' => $query, 'meta_value' => 0, 'meta_compare' => '>', 'posts_per_page' => 10]);
            $res[$key] = !$q->empty ? $q->animes : [];
        }

        ob_start();
        // before and after widget arguments are defined by themes
        ?>
<div class="w-full">
    <div class="mr-4 md:px-5 lg:px-0">
        <h2 class="text-2xl leading-10 font-semibold lg:p-0 m-0 text-accent mb-4"><?php echo $title; ?></h2>
    </div>
    <div class="grid grid-cols-3 bg-darker rounded-t-md">
        <div data-tab-id="day"
            class="w-full p-2 py-3 text-sm font-medium cursor-pointer rounded-tl-md bg-secondary text-center"
            onClick="JavaScript:selectPopularTab(0);">
            <?php _e('Today', 'kiranime');?>
        </div>
        <div data-tab-id="week" class="w-full p-2 py-3 text-sm font-medium cursor-pointer text-center"
            onClick="JavaScript:selectPopularTab(1);">
            <?php _e('Week', 'kiranime');?>
        </div>
        <div data-tab-id="month" class="w-full p-2 py-3 text-sm font-medium cursor-pointer text-center rounded-tr-md"
            onClick="JavaScript:selectPopularTab(2);">
            <?php _e('Month', 'kiranime');?>
        </div>
    </div>

    <?php foreach ($res as $index => $animes):
        ?>
    <div data-tab-content="<?=$index?>" class="p-4 bg-overlay w-full <?=$index === 'day' ? '' : 'hidden'?>">
        <ul class="grid grid-cols-1 gap-5">
            <?php foreach ($animes as $key => $anime):
            $anime->get_meta('featured')->get_statistic();
            ?>
	            <li class=" flex items-center gap-5">
	                <div data-popular-add-to-list="<?php echo $anime->anime_id; ?>"
	                    class=" w-10 transform -translate-y-1 text-center flex-shrink-0 flex-grow-0 group">
	                    <span
	                        class="text-xl font-semibold pb-2 border-b-2 border-accent-3 group-hover:hidden"><?php if (($key + 1) < 10) {echo '0' . ($key + 1);} else {echo ($key + 1);}?></span>
	                    <svg xmlns="http://www.w3.org/2000/svg"
	                        class="w-5 h-5 mx-auto hidden group-hover:block cursor-pointer" viewBox="0 0 448 512">
	                        <path fill="currentColor"
	                            d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z" />
	                    </svg>
	                </div>

	                <div class="flex-auto flex gap-2 overflow-hidden">
	                    <?php echo $anime->get_image_thumbnail_html('smallthumb', 'w-12 h-16 flex-shrink-0 object-cover rounded shadow') ?>
	                    <div>
	                        <h3 class="line-clamp-2 text-sm leading-6">
	                            <a href="<?=$anime->url?>"
	                                title="<?php echo $anime->post->post_title ?>"><?php echo $anime->post->post_title ?></a>
	                        </h3>
	                        <div class="text-xs">
	                            <span class="flex items-center gap-1">
	                                <span class="material-icons-round text-base text-white text-opacity-70">
	                                    visibility
	                                </span>
	                                <?php echo $anime->statistic[$index] ?> <?php _e('views', 'kiranime')?></span>
	                        </div>
	                    </div>
	                </div>
	            </li>
	            <?php endforeach;?>

        </ul>
    </div>
    <?php endforeach;?>
</div>
<script>
function selectPopularTab(index) {
    const tabs = document.querySelectorAll('[data-tab-id]')
    const contents = document.querySelectorAll('[data-tab-content]')

    tabs.forEach(e => e.classList.remove('bg-secondary'))
    contents.forEach(e => e.classList.add('hidden'));

    tabs[index].classList.add('bg-secondary');
    contents[index].classList.remove('hidden');
}
</script>

<?php
$popular = ob_get_clean();
        echo $popular;
    }

    // Widget Backend
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title');
        }
        // Widget admin form
        ?>
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:');?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
        name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>
<?php
}

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance          = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

    // Class Kiranime_Popular_List ends here
}
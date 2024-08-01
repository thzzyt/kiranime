<?php

class Kiranime_Most_Popular extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(

            'Kiranime_Most_Popular',

            'Kiranime Most Popular List',

            array('description' => 'Show Most popular list.')
        );
    }

    // Creating widget front-end

    public function widget($args, $instance)
    {
        $title = $instance['title'];

        $query = new Kira_Query([
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'total_kiranime_views',
            'posts_per_page' => 10,
            'no_found_rows'  => 1,
        ]);
        $animes = $query->animes;?>

<div class="w-full">
    <div class="mr-4 md:px-5 lg:px-0">
        <h2 class="text-2xl leading-10 font-semibold lg:p-0 m-0 text-accent mb-4"><?php echo $title; ?></h2>
    </div>
    <ul class="bg-overlay px-5 py-4">
        <?php foreach ($animes as $anime):
            $anime->get_meta('featured', 'duration')->get_taxonomies('type', 'anime_attribute')->get_episodes();
            ?>
        <li class="flex gap-5 border-b border-white border-opacity-5 py-4 relative">
            <div class="relative w-12 h-16  overflow-hidden flex-shrink-0">
                <?php echo $anime->get_image_thumbnail_html('smallthumb', 'absolute inset-0 w-full h-auto object-cover') ?>
            </div>
            <div class="flex-auto w-9/12 text-sm">
                <h3 class="mb-1 font-medium leading-6 line-clamp-2 ">
                    <a href="<?php echo $anime->url ?>" title="<?php echo $anime->post->post_title ?>"
                        class="hover:text-accent-2"><?php echo $anime->post->post_title ?></a>
                </h3>
                <div class="text-spec flex gap-1 items-center">
                    <span
                        class="inline-block uppercase"><?php echo isset($anime->taxonomies['type']) && !empty($anime->taxonomies['type']) ? $anime->taxonomies['type'][0]->name : '' ?></span>
                    <?php if (isset($anime->taxonomies['type']) && !empty($anime->taxonomies['type']) && 'movie' !== $anime->taxonomies['type'][0]->slug): ?>
                    <span class="w-1 h-1 bg-white bg-opacity-10 inline-block"></span>
                    <span
                        class="inline-block">E<?php echo $anime->episodes && $anime->episodes->meta['number'] ? $anime->episodes->meta['number'] : '?'; ?></span>
                    <?php endif;?>
                    <span class="w-1 h-1 bg-white bg-opacity-10 inline-block"></span>
                    <span class="inline-block fdi-duration"><?php echo $anime->meta['duration'] ?></span>

                </div>
            </div>
        </li>
        <?php endforeach;?>
    </ul>
</div>

<?php }

    // Widget Backend
    public function form($instance)
    {
        $title = $instance['title'] ? $instance['title'] : __('New Title');
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
        return array_merge($old_instance, $new_instance);
    }

}
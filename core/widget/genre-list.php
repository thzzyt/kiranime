<?php

class Kiranime_Genre_List extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(

            // Base ID of your widget
            'Kiranime_Genre_List',

            // Widget name will appear in UI
            'Kiranime Genre List',

            // Widget description
            array('description' => 'Show genre list.')
        );
    }

    // Creating widget front-end

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        $style = $instance['style'] ? $instance['style'] : 'auto';

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title)) {?>
<div class="mr-4 md:px-5 px-0 lg:px-0">
    <h2 class="text-2xl leading-10 font-semibold lg:p-0 m-0 text-accent mb-4"><?php echo $title; ?></h2>
</div>
<?php }

        $genres = get_terms([
            'taxonomy' => 'genre',
            'hide_empty' => true,
        ])
        ?>
<div class="w-full">
    <?php if ($style == 'auto'): ?>
    <div class="p-4 lg:bg-overlay">
        <ul
            class="lg:grid grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 max-h-96 overflow-y-scroll overflow-x-hidden hidden">
            <?php foreach ($genres as $genre): ?>
            <li class="w-full flex-auto text-xs font-medium font-montserrat genre-list"><a
                    class="block hover:bg-secondary p-2 whitespace-nowrap overflow-ellipsis max-w-full rounded"
                    href="<?=get_term_link($genre)?>"><?=$genre->name;?></a></li>
            <?php endforeach?>

        </ul>
        <ul class="flex gap-2 flex-wrap lg:hidden">
            <?php foreach ($genres as $genre): ?>
            <li class="w-max inline-block flex-auto text-xs font-medium font-montserrat genre-list"><a
                    class="block bg-overlay p-2 whitespace-nowrap overflow-ellipsis max-w-full rounded text-center"
                    href="<?=get_term_link($genre)?>"><?=$genre->name;?></a></li>
            <?php endforeach?>
        </ul>
    </div>
    <?php elseif ($style == 'cloud'): ?>
    <ul class="flex gap-2 flex-wrap max-h-96 overflow-y-scroll overflow-x-hidden">
        <?php foreach ($genres as $genre): ?>
        <li class="w-max inline-block flex-auto text-xs font-medium font-montserrat genre-list"><a
                class="block bg-overlay p-2 whitespace-nowrap overflow-ellipsis max-w-full rounded text-center"
                href="<?=get_term_link($genre)?>"><?=$genre->name;?></a></li>
        <?php endforeach?>
    </ul>
    <?php else: ?>
    <div class="p-4 bg-overlay">
        <ul class="lg:grid grid-cols-2 xl:grid-cols-3 max-h-96 overflow-y-scroll overflow-x-hidden hidden">
            <?php foreach ($genres as $genre): ?>
            <li class="w-full flex-auto text-xs font-medium font-montserrat genre-list"><a
                    class="block hover:bg-secondary p-2 whitespace-nowrap overflow-ellipsis max-w-full rounded"
                    href="<?=get_term_link($genre)?>"><?=$genre->name;?></a></li>
            <?php endforeach?>
        </ul>
    </div>
    <?php endif;?>
</div>
<?php echo $args['after_widget'];
    }

    // Widget Backend
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : 'New title';
        $style = isset($instance['style']) ? $instance['style'] : 'Choose Style';
        // Widget admin form
         ?>
<div>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
        name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    <label for="<?php echo $this->get_field_id('style'); ?>" class="mt-5">Style</label>
    <select name="<?php echo $this->get_field_name('style'); ?>" id="<?php echo $this->get_field_id('style'); ?>">
        <option value="auto" <?php if ($style == 'auto') {echo 'selected';}?>>Auto</option>
        <option value="cloud" <?php if ($style == 'cloud') {echo 'selected';}?>>Cloud</option>
        <option value="block" <?php if ($style == 'block') {echo 'selected';}?>>Block</option>
    </select>
</div>
<?php
}

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['style'] = $new_instance['style'] ? strip_tags($new_instance['style']) : 'auto';
        return $instance;
    }

    // Class Kiranime_Genre_List ends here
}
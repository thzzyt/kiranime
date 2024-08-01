<?php

class Listing_Module extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            // widget ID
            'kiranime_listing_module',
            // widget name
            'Kiranime Anime List Module',
            // widget description
            array('description' => __('For Homepage listing/recommended list/slider.', 'hstngr_widget_domain'))
        );
    }

    public function form($instance)
    {
        $status = get_terms([
            'taxonomy'   => 'status',
            'hide_empty' => false,
        ]);
        $s = [];
        foreach ($status as $stat) {
            $s[$stat->slug] = $stat->name;
        }

        $options = [
            'title'      => [
                'type'     => 'text',
                'name'     => 'Title',
                'selected' => isset($instance['title']) ? $instance['title'] : '',
            ],
            'post_type'  => [
                'type'     => 'select',
                'name'     => 'Post Type',
                'value'    => [
                    'anime'   => 'Anime',
                    'episode' => 'Episode',
                ],
                'selected' => isset($instance['post_type']) ? $instance['post_type'] : '',
            ],
            'order'      => [
                'type'     => 'select',
                'name'     => 'Order',
                'value'    => [
                    'asc'  => 'Ascending',
                    'desc' => 'Descending',
                ],
                'selected' => isset($instance['order']) ? $instance['order'] : '',
            ],
            'orderby'    => [
                'type'     => 'select',
                'name'     => 'Order by',
                'value'    => [
                    'popular'  => 'Popular',
                    'favorite' => 'Favorite',
                    'date'     => 'Published',
                    'updated'  => 'Updated',
                    'title'    => 'Title',
                    'random'   => 'Random',
                ],
                'selected' => isset($instance['orderby']) ? $instance['orderby'] : '',
            ],
            'display'    => [
                'type'     => 'select',
                'name'     => 'Display',
                'value'    => [
                    'grid'   => 'Grid',
                    'slider' => 'Slider',
                ],
                'selected' => isset($instance['display']) ? $instance['display'] : '',
            ],
            'status'     => [
                'name'     => 'Status',
                'type'     => 'multiselect',
                'value'    => $s,
                'selected' => isset($instance['status']) ? $instance['status'] : '',
            ],
            'post_count' => [
                'name'     => 'Post Count',
                'type'     => 'text',
                'selected' => isset($instance['post_count']) ? $instance['post_count'] : '',
            ],
            'view_more'=>[
                'name'  => __('View More link', 'kiranime'),
                'type' => 'text',
                'selected' => isset($instance['view_more']) ? $instance['view_more'] : '',
            ]
        ];
        ?>
<p>
    <?php foreach ($options as $key => $field):
            if ('text' === $field['type']): ?>

    <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $field['name']; ?>:</label>
    <input class="widefat" id="<?php echo $this->get_field_id($key); ?>"
        name="<?php echo $this->get_field_name($key); ?>" type="text" value="<?php echo $field['selected']; ?>" />

    <?php elseif ('select' === $field['type']): ?>

    <label for="<?php echo $this->get_field_id($key); ?>" class="mt-5"><?php echo $field['name']; ?>:</label>
    <select name="<?php echo $this->get_field_name($key); ?>" id="<?php echo $this->get_field_id($key); ?>">
        <?php foreach ($field['value'] as $val => $opt): ?>
        <option value="<?php echo $val; ?>" <?php if ($field['selected'] == $val) {echo 'selected';}?>>
            <?php echo $opt; ?>
        </option>
        <?php endforeach;?>
    </select>

    <?php elseif ('multiselect' === $field['type']): ?>

<div data-anime-status>
    <label for="<?php echo $this->get_field_id($key); ?>">Select Anime Status you want to show: (only for anime post
        type)</label><br />
    <div style="width: 100%;display: flex;align-items: center;gap: 0.5rem;flex-wrap: wrap;">
        <?php foreach ($field['value'] as $val => $opt):
            $checked = false;
            if (is_array($field['selected'])) {
                if (in_array($val, $field['selected'])) {
                    $checked = true;
                }
            } else {
                if (stripos($field['selected'], $val) >= 0) {
                    $checked = true;
                }
            }
            ?>
        <div style="width: max-content;display: flex;align-items: center;gap: 0.5rem;">
            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id($key); ?>-<?php echo $opt; ?>"
                name="<?php echo $this->get_field_name($key . '[]'); ?>" value="<?php echo $val; ?>"
                <?php echo $checked ? 'checked' : ''; ?> />
            <label style="line-height: 1;"
                for="<?php echo $this->get_field_id($key); ?>-<?php echo $opt; ?>"><?php echo $opt; ?></label><br />
        </div>
        <?php endforeach;?>
    </div>
</div>
<?php endif;endforeach;?>
</p>
<?php
}

    public function widget($args, $instance)
    {
        return load_init_widget_listing($instance);
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array_merge($old_instance, $new_instance);
        return $instance;
    }
}
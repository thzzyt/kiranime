<?php

class Kira_Scheduled_Module extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            // widget ID
            'kiranime_scheduled_module',
            // widget name
            'Kiranime Scheduled Episode Module',
            // widget description
            array('description' => __('For scheduled episode list.', 'hstngr_widget_domain'))
        );
    }

    public function form($instance)
    {

        $title = isset($instance['title']) ? $instance['title'] : 'New Title';
        ?>
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'kiranime');?>:</label>
    <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
        type="text" value="<?php echo $title; ?>" />
</p>
<?php
}

    public function widget($args, $instance)
    {
        get_template_part('template-parts/sections/home', 'scheduled', ['title' => $instance['title']]);
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array_merge($old_instance, $new_instance);
        return $instance;
    }
}
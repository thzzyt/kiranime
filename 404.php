<?php get_header()?>
<div class="md:flex min-h-screen my-17">
    <div class="w-full md:w-1/2 flex items-center justify-center">
        <div class="max-w-sm m-8">
            <div class="text-5xl md:text-15xl text-text-color border-secondary border-b">404</div>
            <div class="w-16 h-1 bg-accent-3 my-3 md:my-6"></div>
            <p class="text-text-color text-2xl md:text-3xl font-light mb-8">
                <?php _e('Sorry, the page you are looking for could not be found.', 'kiranime');?></p>
            <a href="<?php echo get_bloginfo('url'); ?>" class="bg-primary px-4 py-2 rounded text-text">
                <?php _e('Go Home', 'kiranime');?>
            </a>
        </div>
    </div>
</div>
<?php get_footer()?>
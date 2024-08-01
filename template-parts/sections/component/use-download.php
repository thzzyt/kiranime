<?php
$downloads = !empty($args['downloads']) ? (is_string($args['downloads']) ? json_decode($args['downloads'], true) : $args['downloads']) : [];
if (!empty($downloads)):
?>
<section class="space-y-5 mb-5">
    <h3 class="font-semibold text-accent-2 text-xl lg:text-2xl leading-loose"><?php _e('Downloads', 'kiranime');?></h3>
    <div class="overflow-hidden mb-2">
        <?php foreach ($downloads as $download):
    if (is_object($download)) {
        $download = json_decode(json_encode($download), true);
    }
    ?>
	        <div class="block md:flex md:gap-2 flex-wrap mb-3">
	            <div
	                class="text-[13px] font-semibold min-w-[5rem] bg-accent-3 px-2 py-1 rounded text-center text-white mb-2 md:mb-0">
	                <?php echo $download['resolution'] ?>
	            </div>
	            <div class="flex items-center flex-wrap gap-1.5">
	                <?php foreach ($download['data'] as $link): ?>
	                <a class="bg-tertiary block text-text-color rounded-sm hover:bg-accent-3 py-1 px-2 w-fit text-center transition-all duration-200 ease-in-out text-[13px] font-medium font-montserrat"
	                    href="<?php echo $link['url'] ?>" target="_blank" rel="nofollow"><?php echo $link['provider'] ?></a>
	                <?php endforeach;?>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</section>
<?php endif;?>
<?php
$query = new Kira_Query();
$query = $query->trending();
?>
<style>
@media (min-width: 1024px) {
    .swiper-trending .swiper-slide {
        width: auto !important;
    }
}
</style>
<div class="<?php if (get_theme_mod('__show_spotlight') === 'hide'): echo 'lg:mt-17';else:echo 'lg:-mt-6';endif;?>">
    <div class="p-4 text-xl lg:text-2xl leading-4 lg:leading-10 font-semibold text-accent-2">
        <?php _e('Trending', 'kiranime');?>
    </div>
    <div class="sm:px-4 px-0 mt-2 md:flex justify-between w-full pb-10"
        style="background: linear-gradient(0deg,var(--primary-darkest-color) 0,rgba(18,19,21,0) 99%);">
        <div class="swiper swiper-trending">
            <div class="swiper-wrapper" style="min-width: 100vw;">
                <!-- Slides -->
                <?php
if (!$query->empty): foreach ($query->animes as $index => $anime):
        $image  = $anime->get_image_thumbnail_html('kirathumb', 'w-full h-full sm:w-3/4 lg:w-40 lg:h-56 xl:w-52 xl:h-[19rem] absolute sm:left-1/5 lg:left-10 object-cover');
        $mobile = $anime->get_image_thumbnail_html('kirathumb', 'absolute inset-0 object-cover w-full h-full z-0');
        ?>
						                <div class="swiper-slide md:min-w-[14rem]">
						                    <a href="<?php echo $anime->url ?>"
						                        class="sm:flex hidden relative sm:pb-64 md:pb-56 overflow-hidden lg:max-h-64 lg:pb-64 xl:max-h-[16rem] xl:pb-64  shadow-sm min-w-full w-56">
						                        <div
						                            class="sm:w-1/5 lg:w-10 h-full flex flex-col items-center gap-2 absolute left-0 bottom-0 font-semibold font-montserrat bg-gradient-to-t from-primary to-secondary">
						                            <span
						                                class="h-5 line-clamp-1 -rotate-90 transform overflow-hidden overflow-ellipsis text-sm absolute bottom-24 items-center"
						                                style="bottom: 8rem;width: 12.5rem;">
						                                <?php echo $anime->post->post_title ?>
						                            </span>
						                            <span
						                                class="flex items-center justify-center absolute bottom-0 text-2xl text-accent"><?php echo $index < 9 ? '0' . ($index + 1) : ($index + 1) ?></span>
						                        </div>
						                        <?php if ($image): echo $image;else:echo $index;endif;?>
						                    </a>
						                    <a href="<?php echo $anime->url; ?>" class="relative w-full sm:hidden">
						                        <div class="relative pb-48 md:pb-52 overflow-hidden">
						                            <?php echo $image ?>
						                            <div
						                                class="absolute -top-11 -left-11 w-20 h-20 z-10 bg-accent-3 text-text-color rotate-45 transform">
						                            </div>
						                            <span
						                                class="w-max top-[2.5%] left-[10%] text-center font-semibold z-20 text-xs absolute"><?php echo $index + 1; ?></span>
						                        </div>
						                    </a>
						                </div>
						                <?php endforeach;endif;?>
            </div>
            <div class="swiper-pagination lg:hidden"></div>
        </div>
        <div class="swiper-navigation ml-5 md:grid hidden">
            <div class="mb-2 bg-gray-700 rounded-sm shadow-sm p-2 trending-nav-next hover:bg-accent-3 flex items-center justify-center"
                tabindex="0" role="button" aria-label="Next slide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" class="w-6 h-6">
                    <path fill="currentColor"
                        d="M187.8 264.5L41 412.5c-4.7 4.7-12.3 4.7-17 0L4.2 392.7c-4.7-4.7-4.7-12.3 0-17L122.7 256 4.2 136.3c-4.7-4.7-4.7-12.3 0-17L24 99.5c4.7-4.7 12.3-4.7 17 0l146.8 148c4.7 4.7 4.7 12.3 0 17z" />
                </svg>
            </div>
            <div class="bg-gray-700 rounded-sm shadow-sm p-2 trending-nav-prev hover:bg-accent-3 flex items-center justify-center"
                tabindex="0" role="button" aria-label="Previous slide">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" class="w-6 h-6">
                    <path fill="currentColor"
                        d="M4.2 247.5L151 99.5c4.7-4.7 12.3-4.7 17 0l19.8 19.8c4.7 4.7 4.7 12.3 0 17L69.3 256l118.5 119.7c4.7 4.7 4.7 12.3 0 17L168 412.5c-4.7 4.7-12.3 4.7-17 0L4.2 264.5c-4.7-4.7-4.7-12.3 0-17z" />
                </svg>
            </div>
        </div>
    </div>
</div>
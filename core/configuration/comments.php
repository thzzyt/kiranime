<?php

if (!function_exists('kiranime_better_comments')):
    function kiranime_better_comments($comment, $args, $depth)
{
        $uid    = isset($comment) ? $comment->user_id : 0;
        $avatar = Kira_User::get_avatar($uid);
        ?>
			<li <?php comment_class();?> id="li-comment-<?php comment_ID()?>">
			    <div class="comment w-full flex gap-4">
			        <div class="img-thumbnail hidden sm:block">
			            <img alt="<?=$comment->comment_author?>" src="<?=$avatar;?>" class="avatar avatar-64 photo" height="64"
			                width="64" loading="lazy">
			        </div>
			        <div class="bg-tertiary font-normal text-sm w-full p-2">
			            <div class="comment-arrow"></div>
			            <?php if ('0' == $comment->comment_approved): ?>
			            <em><?php esc_html_e('Your comment is awaiting moderation.', 'kiranime')?></em>
			            <br />
			            <?php endif;?>
            <span class="flex items-center justify-between w-full">
                <strong><?=get_comment_author()?></strong>
                <span class="float-right">
                    <span> <a href="#"><i class="fa fa-reply"></i>
                            <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'])))?></a></span>
                </span>
            </span>
            <p> <?php comment_text()?></p>
            <span
                class="date text-xs float-right"><?php printf(esc_html__('%1$s at %2$s', 'kiranime'), get_comment_date(), get_comment_time())?></span>
        </div>
    </div>

    <?php
}
endif;
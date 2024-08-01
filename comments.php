<?php
/**
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}

/**
 * is comments need user to logged in
 */
if (get_option('comment_registration')) {
    $need_login = get_option('comment_registration');
}
?>
<div id="comments" class="comments-area my-8">

    <?php if (!isset($need_login) || !$need_login || ($need_login && is_user_logged_in())): ?>
    <?php
comment_form(
    array(
        'class_submit' => 'bg-accent-3 hover:bg-accent-4 text-text-color cursor-pointer rounded font-medium text-[0.925rem] py-2 px-4',
        'comment_field' => '<textarea id="comment" name="comment" class="bg-tertiary border-none outline-none focus:ring ring-accent-3 my-2 w-full py-2 px-3 rounded" aria-required="true" row="7"></textarea>',
    )
);
?>
    <?php if (have_comments()): ?>
    <div
        class="py-4 mb-2 mt-4 relative before:inset-y-0 before:left-0 before:absolute before:w-1 before:block before:bg-accent-3 font-medium pl-4 min-h-full bg-gradient-to-tr from-secondary/20">
        <h2 class="w-full block ">
            <?php
printf(
    _nx('One comment', '%1$s comments', get_comments_number(), 'comments title', 'kiranime'),
    number_format_i18n(get_comments_number()),
    get_the_title()
);
?>
        </h2>
    </div>

    <ol class="comment-list">
        <?php
wp_list_comments(
    array(
        'style' => 'ol',
        'short_ping' => true,
        'avatar_size' => 56,
        'callback' => 'kiranime_better_comments',
    )
);
?>
    </ol>

    <?php endif;?>

    <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>

    <nav class="comment-navigation" id="comment-nav-above">

        <h1 class="screen-reader-text"><?php esc_html_e('Comment navigation', 'kiranime');?></h1>

        <?php if (get_previous_comments_link()) {?>
        <div class="nav-previous">
            <?php previous_comments_link(__('&larr; Older Comments', 'kiranime'));?>
        </div>
        <?php }?>

        <?php if (get_next_comments_link()) {?>
        <div class="nav-next">
            <?php next_comments_link(__('Newer Comments &rarr;', 'kiranime'));?>
        </div>
        <?php }?>

    </nav><!-- #comment-nav-above -->

    <?php endif;?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')): ?>
    <p class="no-comments"><?php esc_html_e('Comments are closed.', 'kiranime');?></p>
    <?php endif;else: ?>
    <div>
        <h3 id="reply-title" class="comment-reply-title"><?php _e('Leave a reply', 'kiranime');?></h3>
        <span class="block">
            <?php
$link = '<a href="#" class="text-accent-2" data-comment-login-button>' . _e('logged in', 'kiranime') . '</a>';
printf(esc_html__('You must be %1$s to post a comment', 'kiranime'), $link);
?>
        </span>
    </div>
    <?php endif;?>

</div>

<script>
const form = document.querySelector('#commentform');
let isInvalid = true;
if (form) {
    const submitButton = form.querySelector('input#submit');
    const sub = form.querySelector('.form-submit');
    const textarea = form.querySelector('textarea#comment');

    textarea.addEventListener('click', e => removeError())
    textarea.addEventListener('change', e => {
        if (e.target.value !== '') {
            isInvalid = false
            removeError();
        }
    });
    submitButton.addEventListener('click', e => {
        if (!textarea.value || textarea.value === '' || isInvalid) {
            invokeError();
            e.preventDefault();
        }
    })

    function invokeError() {
        textarea.classList.remove('ring-accent-3');
        textarea.classList.add('required-field');
    }

    function removeError() {
        textarea.classList.add('ring-accent-3');
        textarea.classList.remove('required-field');
    }
}
</script>
<?php

/**
 * Template Name: User Profile Page
 *
 * @package Kiranime
 */
if (!is_user_logged_in()) {
    wp_redirect(home_url('/'));
    exit();
}
get_header('single');

$user_info      = get_userdata(get_current_user_id());
$current_avatar = Kira_User::get_avatar(get_current_user_id());

?>
<?php get_template_part('template-parts/sections/component/use', 'user-heading');?>

<section class="lg:w-7/12 w-11/12 mx-auto">
    <h2 class="text-2xl leading-10 font-medium mb-5 flex items-center gap-4">
        <span class="material-icons-round text-3xl">
            account_box
        </span>
        <?php the_title()?>
    </h2>

    <script>
    var pu_dId = "<?php echo base64_encode(json_encode([
    'username'   => $user_info->display_name,
    'email'      => $user_info->user_email,
    'joined'     => $user_info->user_registered,
    'avatar'     => $current_avatar,
    'user_nonce' => wp_create_nonce($user_info->ID),
])) ?>";
    </script>
    <div id="profile-page"></div>
</section>
<?php get_footer()?>
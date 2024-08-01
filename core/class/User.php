<?php

/**
 * This is class for user related function
 *
 * @package   Kiranime
 * @since   1.0.0
 * @link      https://kiranime.moe
 * @author    Dzul Qurnain
 * @license   GPL-2.0+
 */

class Kira_User
{
    public function __construct()
    {}

    public static function login(string $username, string $password, $rememberMe = false)
    {
        $info                  = [];
        $info['user_login']    = sanitize_text_field($username);
        $info['user_password'] = $password;

        $user = wp_signon($info, '');
        if (is_wp_error($user)) {
            return ['data' => ['status' => false, 'message' => __('Wrong username or password.', 'kiranime')], 'status' => 403];
        }

        wp_set_auth_cookie($user->ID, $rememberMe, is_ssl());
        wp_set_current_user($user->ID);
        return ['data' => ['status' => true, 'message' => __('Login successful, redirecting...', 'kiranime')], 'status' => 200];
    }

    public static function register(string $email, string $username, string $password)
    {
        $username_check = username_exists($username);
        $email_check    = email_exists($email);

        $banned_username = ['admin', 'administrator'];

        if (in_array($username, $banned_username)) {
            return ['data' => ['success' => false, 'message' => __('Username not allowed!', 'kiranime')], 'status' => 400];
        }

        if ($username_check || $email_check) {
            return ['data' => ['success' => false, 'message' => __('Username or Email already exist!', 'kiranime')], 'status' => 400];
        }

        $created = wp_create_user($username, $password, $email);

        if (is_wp_error($created)) {
            return ['data' => ['success' => false, 'message' => $created->errors], 'status' => 500];
        }

        add_user_meta($created, 'Kira_User_avatar', KIRA_URI . '/avatar/dragonball/av-db-1.jpeg');

        return ['data' => ['success' => true, 'message' => __('Register successful.', 'kiranime')], 'status' => 201];
    }

    public static function logout()
    {
        wp_logout();
        ob_clean();

        return ['status' => true, 200];
    }

    public static function save()
    {
        $params = isset($_POST['data']) ? json_decode(stripslashes($_POST['data'])) : [];
        $uid    = get_current_user_id();

        if (!$params || !$uid) {
            return wp_send_json_error(['message' => 'no data!', 'param' => $_POST]);
            wp_die();
        }

        foreach ($params as $data) {
            update_user_meta($uid, $data->name, $data->value);
        }

        return wp_send_json_success(['success' => true]);
        wp_die();
    }

    /**
     * get user avatar by id
     * @param int $uid User Id
     * @return string
     */
    public static function get_avatar(int $uid = 0)
    {
        if (!$uid) {
            return KIRA_URI . '/avatar/dragonball/av-db-1.jpeg';
        }

        $avatar = get_user_meta($uid, 'Kira_User_avatar', true);

        if (!$avatar) {
            return KIRA_URI . '/avatar/dragonball/av-db-1.jpeg';
        } else {
            return $avatar;
        }
    }

    public static function set_avatar(string $avatar, int $user_id)
    {

        $updated = update_user_meta($user_id, 'Kira_User_avatar', $avatar);
        if (!$updated) {
            return ['data' => false, 'status' => 500];
        } else {
            return ['data' => true, 'status' => 200];
        }
    }

    public static function list_avatar()
    {
        $avatars = [
            'chibi'      => range(1, 19),
            'dragonball' => range(1, 6),
            'onepiece'   => range(1, 12),
        ];

        $results = [
            'chibi'      => [],
            'dragonball' => [],
            'onepiece'   => [],
        ];

        foreach ($avatars as $name => $file) {
            $path = '';
            $ext  = '';
            switch ($name) {
                case 'chibi':
                    $path = KIRA_URI . '/avatar/chibi/chibi_';
                    $ext  = '.png';
                    break;
                case 'dragonball':
                    $path = KIRA_URI . '/avatar/dragonball/av-db-';
                    $ext  = '.jpeg';
                    break;
                case 'onepiece':
                    $path = KIRA_URI . '/avatar/onepiece/user-';
                    $ext  = '.jpeg';
                    break;
            }
            foreach ($file as $index) {
                $results[$name][] = $path . $index . $ext;
            }
        }

        return $results;
    }

    public static function upload_avatar(int $user_id)
    {
        if (!function_exists('media_handle_upload')) {
            require_once ABSPATH . "wp-admin" . '/includes/image.php';
            require_once ABSPATH . "wp-admin" . '/includes/file.php';
            require_once ABSPATH . "wp-admin" . '/includes/media.php';
        }

        $attachment_id = media_handle_upload('file', 0);

        if (is_wp_error($attachment_id)) {
            return ['data' => ['message' => __('error uploading image', 'kiranime'), 'error' => $attachment_id->get_error_message()], 'status' => 500];
        }

        $attachment_url = wp_get_attachment_url($attachment_id);

        $set = update_user_meta($user_id, 'Kira_User_avatar', $attachment_url);

        if (!$set) {
            return ['data' => ['message' => __('error set image.', 'kiranime')], 'status' => 500];
        }

        return ['data' => ['message' => __('Set image success!', 'kiranime'), 'url' => $attachment_url], 'status' => 201];
    }

    public static function save_profile_data($data)
    {
        if (!isset($data['password'], $data['email'], $data['username'], $data['confirm'])) {
            return ['status' => 0, 'message' => __('Field cannot be empty!', 'kiranime')];
        }

        $user = get_current_user_id();
        if (!wp_verify_nonce($data['u_nonce'], $user)) {
            return ['status' => 0, 'message' => __('You\'re not allowed to do this!', 'kiranime')];
        }

        $current_user  = wp_get_current_user();
        $pass_changed  = false;
        $changed_login = [
            'email' => $current_user->user_email,
        ];
        if (1 === $data['change_pass']) {
            // return ['status' => 0, 'message' => 'test'];
            if (!wp_check_password($data['current_password'], $current_user->user_pass, $user)) {
                return ['status' => 0, 'message' => __('Current password is incorrect!', 'kiranime')];
            }

            if ($data['password'] !== $data['confirm']) {
                return ['status' => 0, 'message' => __('Password is not the same!', 'kiranime')];
            }

            wp_set_password($data['password'], $user);
            $pass_changed                   = true;
            $changed_login['user_password'] = $data['password'];
        }

        if ($current_user->display_name !== $data['username']) {
            $uname = wp_update_user([
                'ID'           => $current_user->ID,
                'display_name' => sanitize_user($data['username']),
            ]);
        }

        if (is_email($data['email']) && $data['email'] !== $current_user->user_email) {
            if (email_exists($data['email'])) {
                unset($changed_login['email']);
            } else {
                $mail = wp_update_user(['ID' => $current_user->ID, 'user_email' => sanitize_email($data['email'])]);
                if (!is_wp_error($mail)) {
                    $changed_login['user_login'] = sanitize_email($data['email']);
                }
            }

        }

        if ($pass_changed) {

            $user_sign = wp_signon($changed_login);
            wp_set_current_user($user_sign->ID);
        }

        return [
            'status'  => 1,
            'message' => __('Info updated!', 'kiranime'),
            'data'    => [
                'name' => isset($uname) ? $uname : '',
                'mail' => isset($mail) ? $mail : '',
                'data' => $data,
            ],
        ];
    }

    public function get_recovery_verification_code($userlogin = null)
    {
        if (!$userlogin) {
            return ['data' => ['status' => false, 'message' => __('Wrong username or password.', 'kiranime')], 'status' => 403];
        }
        $user_reset = trim($userlogin);
        $result     = $this->custom_retrieve_password($user_reset);
        if (is_wp_error($result)) {
            return ['data' => ['status' => false, 'message' => $result->get_error_message()], 'status' => 400];
        }

        return ['data' => ['status' => true, 'message' => __('Please check your email to get reset password link.', 'kiranime')], 'status' => 200];
    }

    private function custom_retrieve_password($user_login)
    {
        if (is_email($user_login)) {
            $user_data = get_user_by('email', $user_login);
        } else {
            $user_data = get_user_by('login', $user_login);
        }

        if (!$user_data) {
            return new WP_Error('invalid_login', __('Wrong username or password.', 'kiranime'));
        }

        $key = $this->get_reset_password_key($user_data);
        if (is_wp_error($key)) {
            return $key;
        }

        if (is_multisite()) {
            $site_name = get_network()->site_name;
        } else {
            $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }
        $user_email = $user_data->user_email;

        $message = __('Someone has requested a password reset for the following account:', 'kiranime') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s', 'kiranime'), $user_data->user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.', 'kiranime') . "\r\n\r\n";
        $message .= __('To reset your password, use the following code:', 'kiranime') . "\r\n\r\n";
        $message .= "$key\r\n\r\n";

        $title = sprintf(__('[%s] Password Reset', 'kiranime'), $site_name);

        $title = apply_filters('retrieve_password_title', $title, $user_login, $user_data);

        $message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);

        if (!wp_mail($user_email, wp_specialchars_decode($title), $message)) {
            return new WP_Error('sent_failed', __('The email could not be sent.', 'kiranime'));
        }

        return true;
    }

    private function get_reset_password_key(WP_User $user): string
    {
        if (!($user instanceof WP_User)) {
            return new WP_Error('invalidcombo', __('<strong>Error:</strong> There is no account with that username or email address.'));
        }

        $rand_pass  = wp_generate_password(20, false);
        $expiration = time() + DAY_IN_SECONDS;

        $key = $expiration . ":" . $rand_pass;
        update_user_meta($user->ID, 'password_reset_key', $key);

        return $rand_pass;

    }

    private function check_reset_password_key(string $key, string $userlogin)
    {
        if (empty($key) || !is_string($key) || empty($userlogin) || !is_string($userlogin)) {
            return new WP_Error('invalid_key', __('Invalid key.'));
        }

        if (is_email($userlogin)) {
            $userdata = get_user_by('email', $userlogin);
        } else {
            $userdata = get_user_by('login', $userlogin);
        }

        if (!$userdata) {
            return new WP_Error('invalid_key', __('Invalid key.'));
        }

        $exist_key = get_user_meta($userdata->ID, 'password_reset_key', true);
        if (empty($exist_key)) {
            return new WP_Error('expired_key', __('Invalid key.'));
        }

        list($expiration, $pass_key) = explode(':', $exist_key);
        if (!$expiration || time() > $expiration || $pass_key !== $key) {
            delete_user_meta($userdata->ID, 'password_reset_key');
            return new WP_Error('expired_key', __('Invalid key.'));
        }

        delete_user_meta($userdata->ID, 'password_reset_key');
        return $userdata;
    }

    public function reset_my_password($args)
    {
        if (!$args || empty($args) || empty($args['userlogin']) || empty($args['new_password']) || empty($args['repeat_password']) || empty($args['verification_token'])) {
            return ['data' => ['status' => false, 'message' => __('Field cannot be empty!', 'kiranime')], 'status' => 400];
        }

        $user = $this->check_reset_password_key($args['verification_token'], $args['userlogin']);
        if (is_wp_error($user)) {
            return ['data' => ['status' => false, 'message' => $user->get_error_message()], 'status' => 400];
        }

        if (!$user) {
            return new WP_REST_Response(['status' => false, 'message' => __('Wrong username or password.', 'kiranime')]);
        }

        wp_set_password($args['new_password'], $user->ID);
        return ['data' => ['status' => true, 'message' => __('Password reset successuflly! You can login now.', 'kiranime')], 'status' => 200];
    }
}

<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * KIRANIME_REQUIRED_START
 * Do not change area!
 * This is important for kiranime to be able to work properly!
 */

define('KIRA_DIR', get_template_directory() . '/core');
define('KIRA_URI', get_template_directory_uri());
define('KIRA_ASSETS', get_template_directory() . '/assets/');
define('KIRA_ASSETS_URI', get_template_directory_uri() . '/assets/');
define('KIRA_VER', wp_get_theme()->get('Version'));
define('KIRA_MODE', 1);

require_once KIRA_DIR . '/kiranime-init.php';

/**
 * load all required functions by kiranime
 */
new Kiranime_Init;

/**
 * KIRANIME_REQUIRED_END
 */

/**
 * Debugging functions
 */
if (!function_exists('write_log')) {

    function write_log($log)
    {
        if (true === WP_DEBUG) {
            error_log('----- KIRANIME DEBUG START -----');
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
            error_log('----- KIRANIME DEBUG END -----');
        }
    }

}

/**
 * Custom code start
 * you can write your own code bellow this line or better using snippet or child theme, so you wont lose your customization on theme update.
 */

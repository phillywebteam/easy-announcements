<?php

/**
 * Plugin Name: Easy Announcements
 * Plugin URI: https://www.phillywebteam.com/project/easy-announcements/
 * Description: Easily add announcements, banners, and popups to your WordPress site.
 * Version: 0.2.2
 * Author: Philly Web Team
 * Author URI: https://www.phillywebteam.com
 * Text Domain: easy-announcements
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('EASY_ANNOUNCEMENTS_ABSPATH', WP_PLUGIN_DIR . '/easy-announcements');
define('EASY_ANNOUNCEMENTS_PLUGIN_DIR', plugins_url('/', __FILE__));
define('EASY_ANNOUNCEMENTS_VERSION', '0.2.2');
define('EASY_ANNOUNCEMENTS_ACF_PATH', EASY_ANNOUNCEMENTS_ABSPATH . '/includes/acf/');
define('EASY_ANNOUNCEMENTS_ACF_URL', EASY_ANNOUNCEMENTS_PLUGIN_DIR . '/includes/acf/');
define('EASY_ANNOUNCEMENTS_ACF_VERSION', '6.3.4');

require EASY_ANNOUNCEMENTS_ABSPATH . '/includes/functions.php';
include_once ABSPATH . 'wp-admin/includes/plugin.php';

if (
    !is_plugin_active('advanced-custom-fields/acf.php') &&
    !is_plugin_active('advanced-custom-fields-pro/acf.php') ||
    (
        is_plugin_active('advanced-custom-fields/acf.php') &&
        defined('ACF_VERSION') && version_compare(ACF_VERSION, EASY_ANNOUNCEMENTS_ACF_VERSION, '<')
    )
) {
    include_once(EASY_ANNOUNCEMENTS_ACF_PATH . 'acf.php');

    add_filter('acf/settings/url', function ($url) {
        return EASY_ANNOUNCEMENTS_ACF_URL;
    });

    if (
        !is_plugin_active('advanced-custom-fields/acf.php') &&
        !is_plugin_active('advanced-custom-fields-pro/acf.php')
    ) {
        add_filter('acf/settings/show_admin', '__return_false');
    }
}

require EASY_ANNOUNCEMENTS_ABSPATH . '/admin/admin.php';

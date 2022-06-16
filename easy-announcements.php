<?php
/**
 * Plugin Name: Easy Announcements
 * Description: Easily add announcements, banners, and popups to your WordPress site.
 * Version: 0.1.1
 * Author: Philly Web Team
 * Author URI: https://www.phillywebteam.com
 * 
 * Text Domain: easy-announcements
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EASY_ANNOUNCEMENTS_ABSPATH', WP_PLUGIN_DIR . '/easy-announcements' );
define( 'EASY_ANNOUNCEMENTS_PLUGIN_DIR', plugins_url( '/', __FILE__ ) );
define( 'EASY_ANNOUNCEMENTS_VERSION', '0.1' );
define( 'EASY_ANNOUNCEMENTS_ACF_PATH', EASY_ANNOUNCEMENTS_ABSPATH . '/includes/acf/' );
define( 'EASY_ANNOUNCEMENTS_ACF_URL', EASY_ANNOUNCEMENTS_ABSPATH . '/includes/acf/' );

require EASY_ANNOUNCEMENTS_ABSPATH . '/includes/functions.php';

include_once( EASY_ANNOUNCEMENTS_ACF_PATH . 'acf.php' );

add_filter( 'acf/settings/url', 'easy_announcements_acf_settings_url' );
function easy_announcements_acf_settings_url( $url ) {
    return EASY_ANNOUNCEMENTS_PLUGIN_DIR . '/includes/acf/';
}
add_filter( 'acf/settings/show_admin', '__return_false' );

require EASY_ANNOUNCEMENTS_ABSPATH . '/admin/admin.php';
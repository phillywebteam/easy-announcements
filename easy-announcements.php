<?php
/**
 * Plugin Name: Easy Announcements
 * Description: Easily add announcements, banners, and popups to your WordPress site.
 * Version: 0.1
 * Author: Philly Web Team
 * Author URI: https://www.phillywebteam.com
 * 
 * Text Domain: easy-announcements
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EA_ABSPATH', WP_PLUGIN_DIR . '/easy-announcements' );
define( 'EA_PLUGIN_DIR', plugins_url( '/', __FILE__ ) );
define( 'EA_VERSION', '0.1' );
define( 'EA_ACF_PATH', EA_ABSPATH . '/includes/acf/' );
define( 'EA_ACF_URL', EA_ABSPATH . '/includes/acf/' );

require EA_ABSPATH . '/includes/functions.php';

include_once( EA_ACF_PATH . 'acf.php' );

add_filter( 'acf/settings/url', 'easy_announcements_acf_settings_url' );
function easy_announcements_acf_settings_url( $url ) {
    return EA_PLUGIN_DIR . '/includes/acf/';
}
add_filter( 'acf/settings/show_admin', '__return_false' );

require EA_ABSPATH . '/admin/admin.php';
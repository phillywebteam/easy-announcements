<?php

/**
 * Plugin Name: Easy Announcements
 * Plugin URI: https://www.phillywebteam.com/project/easy-announcements/
 * Description: Easily add announcements, banners, and popups to your WordPress site.
 * Version: 1.0.1
 * Author: Philly Web Team
 * Author URI: https://www.phillywebteam.com
 * Text Domain: easy-announcements
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EASY_ANNOUNCEMENTS_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'EASY_ANNOUNCEMENTS_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
define( 'EASY_ANNOUNCEMENTS_VERSION', '1.0.1' );

register_activation_hook( __FILE__, 'easy_announcements_activate' );
register_deactivation_hook( __FILE__, 'easy_announcements_deactivate' );

function easy_announcements_activate() {
	if ( ! wp_next_scheduled( 'easy_announcements_expire_check' ) ) {
		wp_schedule_event( time(), 'hourly', 'easy_announcements_expire_check' );
	}
}

function easy_announcements_deactivate() {
	wp_clear_scheduled_hook( 'easy_announcements_expire_check' );
}

require EASY_ANNOUNCEMENTS_ABSPATH . 'includes/functions.php';
require EASY_ANNOUNCEMENTS_ABSPATH . 'admin/admin.php';

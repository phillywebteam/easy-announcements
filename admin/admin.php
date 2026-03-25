<?php
if ( ! defined( 'ABSPATH' ) ) exit;

include EASY_ANNOUNCEMENTS_ABSPATH . 'admin/admin-functions.php';
include EASY_ANNOUNCEMENTS_ABSPATH . 'admin/admin-settings.php';
include EASY_ANNOUNCEMENTS_ABSPATH . 'admin/meta-box.php';

/**
 * Easy Announcements Admin Initialization
 */
class Easy_Announcements_Admin_Init {
	public function __construct() {
		// Instantiate admin components
		new Easy_Announcements_Admin_Assets();
		new Easy_Announcements_Admin_Notices();
		new Easy_Announcements_Block_Editor();
		new Easy_Announcements_Meta_Box();
	}
}

// Initialize admin classes
if ( is_admin() ) {
	new Easy_Announcements_Admin_Init();
}

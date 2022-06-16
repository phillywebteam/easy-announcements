<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'announcement' ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'easy-announcements-admin', plugin_dir_url( __FILE__ ) . 'assets/js/easy-announcements-admin.min.js', array( 'jquery', 'wp-color-picker' ), EASY_ANNOUNCEMENTS_VERSION );
    wp_enqueue_style( 'easy-announcements-admin', plugin_dir_url( __FILE__ ) . 'assets/css/easy-announcements-admin.css');
}

function easy_announcements_configure_notice() {
	$header_selector = easy_announcements_setting( 'header_selector' );
	$content_selector = easy_announcements_setting( 'content_selector' );
	$footer_selector = easy_announcements_setting( 'footer_selector' );
	
    if ( empty( $header_selector ) || empty( $content_selector ) || empty( $footer_selector ) ) {
         echo '<div class="notice notice-warning is-dismissible">
             <p>Easy Announcements will not work until you configure selectors on the <a href="/wp-admin/edit.php?post_type=announcement&page=easy-announcements-admin">Easy Announcements Settings</a> page.</p>
         </div>';
    }
}
add_action( 'admin_notices', 'easy_announcements_configure_notice' );

function easy_announcements_setting( $setting ) {
	if ( !empty( $setting ) ) {
        $options = get_option( 'easy_announcements' );
        if ( !empty( $options[$setting] ) ) {
            return $options[$setting];
        }
	} else {
		return false;
	}
};

function update_easy_announcements_setting( $setting ) {
	if ( !empty( $setting ) ) {
		update_field( $setting, 'option' );
	}
};

function easy_announcements_hide_attr() {
	return true;
}
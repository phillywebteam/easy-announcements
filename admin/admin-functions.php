<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function easy_announcements_enqueue_admin_scripts() {
    if (
        isset( $_GET['post_type'] ) &&
        sanitize_text_field( $_GET['post_type'] ) == 'announcement'
    ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'easy-announcements-admin', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'admin/assets/js/easy-announcements-admin.min.js', array( 'jquery', 'wp-color-picker' ), EASY_ANNOUNCEMENTS_VERSION );
        wp_enqueue_style( 'easy-announcements-admin', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'admin/assets/css/easy-announcements-admin.css');
    }
}
add_action( 'admin_enqueue_scripts', 'easy_announcements_enqueue_admin_scripts' );

function easy_announcements_enqueue_live_select_scripts() {
    wp_register_script( 'easy-announcements-live-select', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'admin/assets/js/easy-announcements-live-select.min.js', array( 'jquery' ), EASY_ANNOUNCEMENTS_VERSION );

	if ( isset( $_GET['live-select'] ) ) {
		$top_theme = ( !empty( wp_get_theme()->parent() ) ) ? wp_get_theme()->parent()->stylesheet : wp_get_theme()->stylesheet;
		wp_add_inline_script( 'easy-announcements-live-select', sprintf( 'var ea_live_select_theme = "' . esc_js( $top_theme ) . '";' ), 'before' );
		wp_enqueue_script( 'easy-announcements-live-select' );
	}
}
add_action( 'wp_enqueue_scripts', 'easy_announcements_enqueue_live_select_scripts' );

function easy_announcements_configure_notice() {
	$header_selector = easy_announcements_setting( 'header_selector' );
	$content_selector = easy_announcements_setting( 'content_selector' );
	$footer_selector = easy_announcements_setting( 'footer_selector' );
	
    if (
        empty( $header_selector ) ||
        empty( $content_selector ) ||
        empty( $footer_selector )
    ) {
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

function easy_announcements_allowed_block_types( $allowed_block_types, $post ) {
    if ( $post->post_type == 'announcement' ) {
        return array(
            'core/paragraph',
            'core/heading',
            'core/list'
        );
    } else {
        return $allowed_block_types;
    }
}
add_filter( 'allowed_block_types', 'easy_announcements_allowed_block_types', 10, 2 );
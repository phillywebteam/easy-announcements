<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function easy_announcements_enqueue_admin_scripts( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || $screen->post_type !== 'announcement' ) return;

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script(
		'easy-announcements-admin',
		EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'admin/assets/js/easy-announcements-admin.min.js',
		[ 'jquery', 'wp-color-picker' ],
		EASY_ANNOUNCEMENTS_VERSION
	);
	wp_enqueue_style(
		'easy-announcements-admin',
		EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'admin/assets/css/easy-announcements-admin.css',
		[],
		EASY_ANNOUNCEMENTS_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'easy_announcements_enqueue_admin_scripts' );

function easy_announcements_enqueue_gutenberg_sidebar() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->post_type !== 'announcement' ) return;

	// Only enqueue on the block editor
	if ( ! function_exists( 'get_block_editor_settings' ) ) return;

	wp_enqueue_script(
		'easy-announcements-gutenberg-sidebar',
		EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'admin/assets/js/gutenberg-sidebar.js',
		[ 'wp-plugins', 'wp-edit-post', 'wp-data', 'wp-core-data', 'wp-components', 'wp-element' ],
		EASY_ANNOUNCEMENTS_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'easy_announcements_enqueue_gutenberg_sidebar' );

function easy_announcements_enqueue_live_select_scripts() {
	wp_register_script(
		'easy-announcements-live-select',
		EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'admin/assets/js/easy-announcements-live-select.min.js',
		[ 'jquery' ],
		EASY_ANNOUNCEMENTS_VERSION
	);

	if ( isset( $_GET['live-select'] ) ) {
		$top_theme = ( ! empty( wp_get_theme()->parent() ) ) ? wp_get_theme()->parent()->stylesheet : wp_get_theme()->stylesheet;
		wp_add_inline_script( 'easy-announcements-live-select', 'var ea_live_select_theme = "' . esc_js( $top_theme ) . '";', 'before' );
		wp_enqueue_script( 'easy-announcements-live-select' );
	}
}
add_action( 'wp_enqueue_scripts', 'easy_announcements_enqueue_live_select_scripts' );

function easy_announcements_configure_notice() {
	$header_selector  = easy_announcements_setting( 'header_selector' );
	$content_selector = easy_announcements_setting( 'content_selector' );
	$footer_selector  = easy_announcements_setting( 'footer_selector' );

	if ( empty( $header_selector ) || empty( $content_selector ) || empty( $footer_selector ) ) {
		$settings_url = admin_url( 'edit.php?post_type=announcement&page=easy-announcements-admin' );
		echo '<div class="notice notice-warning is-dismissible"><p>';
		printf(
			wp_kses(
				/* translators: %s: settings page URL */
				__( 'Easy Announcements will not work until you configure selectors on the <a href="%s">Easy Announcements Settings</a> page.', 'easy-announcements' ),
				[ 'a' => [ 'href' => [] ] ]
			),
			esc_url( $settings_url )
		);
		echo '</p></div>';
	}
}
add_action( 'admin_notices', 'easy_announcements_configure_notice' );

function easy_announcements_setting( $setting ) {
	if ( ! empty( $setting ) ) {
		$options = get_option( 'easy_announcements' );
		if ( ! empty( $options[ $setting ] ) ) {
			return $options[ $setting ];
		}
	}
	return false;
}

function easy_announcements_allowed_block_types( $allowed_block_types, $context ) {
	// $context is WP_Block_Editor_Context (WP 5.8+) or WP_Post (older)
	$post_type = ( $context instanceof WP_Block_Editor_Context && ! empty( $context->post ) )
		? $context->post->post_type
		: ( $context instanceof WP_Post ? $context->post_type : '' );

	if ( $post_type === 'announcement' ) {
		return [ 'core/paragraph', 'core/heading', 'core/list' ];
	}
	return $allowed_block_types;
}
add_filter( 'allowed_block_types_all', 'easy_announcements_allowed_block_types', 10, 2 );

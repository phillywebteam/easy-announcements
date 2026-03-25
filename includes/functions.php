<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function easy_announcements_register_scripts() {
	wp_register_style(  'bootstrap',          EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/css/bootstrap.min.css',        [],   '5.3.3',                    'all' );
	wp_register_script( 'easy-announcements', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/js/easy-announcements.min.js', [],   EASY_ANNOUNCEMENTS_VERSION, true );
	wp_register_style(  'easy-announcements', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/css/easy-announcements.css',   [ 'bootstrap' ], EASY_ANNOUNCEMENTS_VERSION, 'all' );
}
add_action( 'init', 'easy_announcements_register_scripts' );

function easy_announcements_register_meta_fields() {
	$meta_fields = [
		'announcement_placement',
		'announcement_attachment',
		'announcement_pages_include',
		'announcement_pages_exclude',
		'announcement_expiration',
		'announcement_color',
		'announcement_custom_color_background',
		'announcement_custom_color_content',
		'announcement_size',
		'announcement_text_alignment',
		'announcement_text_size',
		'announcement_url',
		'announcement_show_title',
		'announcement_sticky',
		'announcement_dismissable',
		'announcement_popup_delay',
	];

	foreach ( $meta_fields as $meta_key ) {
		register_post_meta( 'announcement', $meta_key, [
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		] );
	}
}
add_action( 'init', 'easy_announcements_register_meta_fields', 5 );

// Hook into REST API saving to apply sanitization
add_action( 'rest_insert_announcement', function( $post, $request ) {
	$params = $request->get_json_params();
	if ( ! isset( $params['meta'] ) ) {
		return;
	}

	$meta = $params['meta'];
	foreach ( $meta as $key => $value ) {
		$sanitized = easy_announcements_sanitize_meta( $key, $value );
		update_post_meta( $post->ID, $key, $sanitized );
	}
}, 10, 2 );

function easy_announcements_sanitize_meta( $meta_key, $value ) {
	if ( empty( $value ) ) {
		return '';
	}

	switch ( $meta_key ) {
		case 'announcement_placement':
		case 'announcement_attachment':
		case 'announcement_color':
		case 'announcement_size':
		case 'announcement_text_alignment':
		case 'announcement_text_size':
			$allowed = [
				'announcement_placement' => [ 'header', 'footer', 'content', 'popup' ],
				'announcement_attachment' => [ 'before', 'after' ],
				'announcement_color' => [ 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'custom' ],
				'announcement_size' => [ 'default', 'compact', 'tall', 'none' ],
				'announcement_text_alignment' => [ '', 'start', 'center', 'end' ],
				'announcement_text_size' => [ 'default', 'small', 'large' ],
			];
			return in_array( $value, $allowed[ $meta_key ] ?? [], true ) ? $value : '';

		case 'announcement_custom_color_background':
		case 'announcement_custom_color_content':
			return sanitize_hex_color( $value );

		case 'announcement_url':
			return esc_url_raw( $value );

		case 'announcement_expiration':
			return sanitize_text_field( $value );

		case 'announcement_pages_include':
		case 'announcement_pages_exclude':
			if ( is_array( $value ) ) {
				return array_map( 'intval', array_filter( $value ) );
			}
			return [];

		case 'announcement_popup_delay':
			return intval( $value );

		case 'announcement_show_title':
		case 'announcement_sticky':
		case 'announcement_dismissable':
			return ( $value === '1' || $value === 1 || $value === true ) ? '1' : '0';

		default:
			return $value;
	}
}

function easy_announcements_has_active() {
	$has = get_transient( 'easy_announcements_has_active' );
	if ( $has === false ) {
		$query = new WP_Query( [
			'post_type'      => 'announcement',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		] );
		$has = $query->found_posts > 0 ? '1' : '0';
		set_transient( 'easy_announcements_has_active', $has, DAY_IN_SECONDS );
	}
	return $has === '1';
}

function easy_announcements_flush_active_transient( $new_status, $old_status, $post ) {
	if ( $post->post_type === 'announcement' && $new_status !== $old_status ) {
		delete_transient( 'easy_announcements_has_active' );
	}
}
add_action( 'transition_post_status', 'easy_announcements_flush_active_transient', 10, 3 );

function easy_announcements_enqueue_scripts() {
	if ( ! easy_announcements_has_active() ) {
		return;
	}
	wp_enqueue_script( 'easy-announcements' );
	wp_enqueue_style( 'easy-announcements' );
}
add_action( 'wp_enqueue_scripts', 'easy_announcements_enqueue_scripts' );

function easy_announcements_remove_meta_boxes() {
	remove_meta_box( 'wpseo_meta',               'announcement', 'normal' );
	remove_meta_box( 'slider_revolution_metabox', 'announcement', 'normal' );
	remove_meta_box( 'rocket_post_exclude',       'announcement', 'normal' );
}
add_action( 'add_meta_boxes', 'easy_announcements_remove_meta_boxes', 100 );

function easy_announcements_admin_css() {
	global $post_type;
	if ( $post_type === 'announcement' ) {
		echo '<style type="text/css">#post-preview, #view-post-btn, #wp-admin-bar-view{display:none;}#minor-publishing-actions{padding:0;}</style>' . "\n";
	}
}
add_action( 'admin_head-post-new.php', 'easy_announcements_admin_css' );
add_action( 'admin_head-post.php',     'easy_announcements_admin_css' );

function get_easy_announcements_cookie( $key ) {
	if ( isset( $_COOKIE['easy_announcements'] ) ) {
		$raw    = sanitize_text_field( wp_unslash( $_COOKIE['easy_announcements'] ) );
		$cookie = json_decode( base64_decode( $raw ), true );
		if ( is_array( $cookie ) && array_key_exists( $key, $cookie ) ) {
			return $cookie[ $key ];
		}
	}
	return '';
}

function check_easy_announcements_cookie( $key ) {
	if ( isset( $_COOKIE['easy_announcements'] ) ) {
		$raw    = sanitize_text_field( wp_unslash( $_COOKIE['easy_announcements'] ) );
		$cookie = json_decode( base64_decode( $raw ), true );
		if ( is_array( $cookie ) && array_key_exists( $key, $cookie ) ) {
			return ( $cookie[ $key ] !== '' && $cookie[ $key ] !== false );
		}
	}
	return false;
}

function easy_announcements_expired( $announcement_id ) {
	if ( empty( $announcement_id ) ) return false;

	$expiration = get_post_meta( $announcement_id, 'announcement_expiration', true );
	if ( empty( $expiration ) ) return false;

	$today = new DateTime( 'now', wp_timezone() );
	return $today->format( 'Y-m-d H:i:s' ) > $expiration;
}

function easy_announcements_dismissible() {
	$args = [
		'post_type'      => 'announcement',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	];
	$loop = new WP_Query( $args );

	while ( $loop->have_posts() ) : $loop->the_post();
		if ( easy_announcements_expired( get_the_ID() ) ) {
			wp_update_post( [ 'ID' => get_the_ID(), 'post_status' => 'draft' ] );
		}
	endwhile;
	wp_reset_postdata();
}
add_action( 'easy_announcements_expire_check', 'easy_announcements_dismissible' );

function easy_announcements_default_color( $type, $color ) {
	if ( empty( $color ) || empty( $type ) ) return '';

	$colors = [
		'primary'   => [ 'background' => '#cfe2ff', 'content' => '#084298' ],
		'secondary' => [ 'background' => '#e2e3e5', 'content' => '#41464b' ],
		'success'   => [ 'background' => '#d1e7dd', 'content' => '#0f5132' ],
		'danger'    => [ 'background' => '#f8d7da', 'content' => '#842029' ],
		'warning'   => [ 'background' => '#fff3cd', 'content' => '#664d03' ],
		'info'      => [ 'background' => '#cff4fc', 'content' => '#055160' ],
	];

	return $colors[ $color ][ $type ] ?? '';
}

function easy_announcements_color( $type, $color ) {
	if ( empty( $color ) || empty( $type ) ) return false;

	$option = get_option( 'easy_announcements' );
	$key    = $type . '_color_' . $color;

	if ( ! empty( $option[ $key ] ) ) {
		return $option[ $key ];
	}

	return easy_announcements_default_color( $type, $color );
}

function easy_announcements_contrast( $hexColor ) {
	// Accepts a hex color with or without leading #
	$hex = ltrim( $hexColor, '#' );
	if ( strlen( $hex ) !== 6 ) return '#000000';

	$R1 = hexdec( substr( $hex, 0, 2 ) );
	$G1 = hexdec( substr( $hex, 2, 2 ) );
	$B1 = hexdec( substr( $hex, 4, 2 ) );

	$L1 = 0.2126 * pow( $R1 / 255, 2.2 )
		+ 0.7152 * pow( $G1 / 255, 2.2 )
		+ 0.0722 * pow( $B1 / 255, 2.2 );

	// L2 for black is always 0
	$contrastRatio = (int) ( ( $L1 + 0.05 ) / 0.05 );

	return ( $contrastRatio > 5 ) ? '#000000' : '#FFFFFF';
}

function easy_announcements_show( $announcement_id ) {
	global $wp_query;

	// Guard: not all pages have a queried post (archives, 404, etc.)
	if ( empty( $wp_query->post->ID ) ) return true;
	$current_page_ID = $wp_query->post->ID;

	$show = true;

	$pages_include  = get_post_meta( $announcement_id, 'announcement_pages_include', true ) ?: [];
	$pages_exclude  = get_post_meta( $announcement_id, 'announcement_pages_exclude', true ) ?: [];
	$dismissable    = get_post_meta( $announcement_id, 'announcement_dismissable', true );
	$placement      = get_post_meta( $announcement_id, 'announcement_placement', true ) ?: '';

	if ( ! is_array( $pages_include ) ) $pages_include = [];
	if ( ! is_array( $pages_exclude ) ) $pages_exclude = [];

	if ( ! empty( $pages_include ) ) {
		if ( ! in_array( $current_page_ID, $pages_include, true ) ) {
			$show = false;
		}
	} elseif ( ! empty( $pages_exclude ) ) {
		if ( in_array( $current_page_ID, $pages_exclude, true ) ) {
			$show = false;
		}
	}

	if ( $dismissable == '1' || $placement === 'popup' ) {
		if (
			check_easy_announcements_cookie( 'dismiss-' . $announcement_id ) &&
			get_easy_announcements_cookie( 'dismiss-' . $announcement_id ) === 'true'
		) {
			$show = false;
		}
	}

	if ( easy_announcements_expired( $announcement_id ) ) {
		$show = false;
	}

	return $show;
}

// REST API endpoint for announcements
function easy_announcements_register_rest_endpoint() {
	register_rest_route( 'easy-announcements/v1', '/announcements', [
		'methods'             => 'GET',
		'callback'            => 'easy_announcements_rest_handler',
		'permission_callback' => '__return_true',
		'args'                => [
			'page_id' => [
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => function( $param ) { return is_numeric( $param ); },
			],
		],
	] );
}
add_action( 'rest_api_init', 'easy_announcements_register_rest_endpoint' );

function easy_announcements_rest_handler( $request ) {
	$page_id = $request->get_param( 'page_id' );

	$query_args = [
		'post_type'      => 'announcement',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	];

	$announcements_query = new WP_Query( $query_args );
	$announcements       = [];

	if ( $announcements_query->have_posts() ) {
		while ( $announcements_query->have_posts() ) {
			$announcements_query->the_post();
			$post_id = get_the_ID();

			global $wp_query;
			$original_post = $wp_query->post ?? null;
			if ( $page_id ) {
				$wp_query->post = get_post( $page_id );
			}

			$should_show = easy_announcements_show( $post_id );

			if ( $original_post ) {
				$wp_query->post = $original_post;
			}

			if ( ! $should_show ) {
				continue;
			}

			$placement  = get_post_meta( $post_id, 'announcement_placement', true ) ?: '';
			$attachment = get_post_meta( $post_id, 'announcement_attachment', true ) ?: '';

			if ( empty( $placement ) || ( $placement !== 'popup' && empty( $attachment ) ) ) {
				continue;
			}

			$cache_key = 'easy_announcements_' . $post_id;
			$html      = wp_cache_get( $cache_key, 'easy_announcements' );

			if ( $html === false ) {
				ob_start();
				switch ( $placement ) {
					case 'popup':
						include EASY_ANNOUNCEMENTS_ABSPATH . 'templates/popup.php';
						break;
					default:
						include EASY_ANNOUNCEMENTS_ABSPATH . 'templates/default.php';
						break;
				}
				$html = ob_get_clean();
				wp_cache_set( $cache_key, $html, 'easy_announcements', DAY_IN_SECONDS );
			}

			$announcements[] = [
				'id'        => $post_id,
				'placement' => $placement,
				'attachment' => $attachment,
				'html'      => $html,
			];
		}
		wp_reset_postdata();
	}

	return new WP_REST_Response( [
		'success'       => true,
		'announcements' => $announcements,
	], 200 );
}

require EASY_ANNOUNCEMENTS_ABSPATH . 'includes/posttype.php';
require EASY_ANNOUNCEMENTS_ABSPATH . 'includes/templates.php';

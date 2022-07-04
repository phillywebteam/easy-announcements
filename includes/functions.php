<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function easy_announcements_register_scripts() {
	wp_register_script( 'easy-announcements-cookie', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/js/js.cookie.min.js', array( 'jquery' ), EASY_ANNOUNCEMENTS_VERSION );
	wp_register_script( 'easy-announcements-bootstrap', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/js/bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), EASY_ANNOUNCEMENTS_VERSION );
	wp_register_script( 'easy-announcements', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/js/easy-announcements.min.js', array( 'jquery' ), EASY_ANNOUNCEMENTS_VERSION );
    wp_register_style( 'easy-announcements', EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/css/easy-announcements.css', false, EASY_ANNOUNCEMENTS_VERSION, 'all' );
}
add_action( 'init', 'easy_announcements_register_scripts' );

function easy_announcements_enqueue_scripts() {
	wp_enqueue_script( 'easy-announcements-cookie' );
	wp_enqueue_script( 'easy-announcements-bootstrap' );
	wp_enqueue_script( 'easy-announcements' );
	wp_enqueue_style( 'easy-announcements' );
}
add_action( 'wp_enqueue_scripts', 'easy_announcements_enqueue_scripts' );

function easy_announcements_remove_meta_boxes() {
	remove_meta_box( 'wpseo_meta', 'announcement', 'normal' );
	remove_meta_box( 'slider_revolution_metabox', 'announcement', 'normal' );
	remove_meta_box( 'rocket_post_exclude', 'announcement', 'normal' );
}
add_action( 'add_meta_boxes', 'easy_announcements_remove_meta_boxes', 100 );

function easy_announcements_admin_css() {
    global $post_type;
    if ( $post_type == 'announcement' )
    	echo wp_kses_post( '<style type="text/css">#post-preview, #view-post-btn, #wp-admin-bar-view{display: none;}#minor-publishing-actions {padding: 0;}</style>' );
}
add_action( 'admin_head-post-new.php', 'easy_announcements_admin_css' );
add_action( 'admin_head-post.php', 'easy_announcements_admin_css' );

function set_easy_announcements_cookie() {
	if ( !isset( $_COOKIE['easy_announcements'] ) ) {
		$cookie = array();
		setcookie( 'easy_announcements', base64_encode( json_encode( $cookie ) ), time() + 3600, '/' );
	}
}
//add_action( 'send_headers', 'set_easy_announcements_cookie' );

function get_easy_announcements_cookie( $key ) {
	if ( isset( $_COOKIE['easy_announcements'] ) ) {
		$cookie = sanitize_text_field( $_COOKIE['easy_announcements'] );
		$cookie = json_decode( stripslashes( base64_decode( $cookie ) ), true );
		return $cookie[$key];
	}
}

function check_easy_announcements_cookie( $key ) {
	if ( isset( $_COOKIE['easy_announcements'] ) ) {
		$cookie = json_decode( stripslashes( base64_decode( $cookie ) ), true );
		if ( array_key_exists( $key, $cookie ) ) {
			return ( $cookie[$key] == '' ) ? false : true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function update_easy_announcements_cookie( $key, $value ) {
	if ( !isset( $_COOKIE['easy_announcements'] ) ) {
		set_easy_announcements_cookie();
	}
	$cookie = sanitize_text_field( $_COOKIE['easy_announcements'] );
	$cookie = json_decode( stripslashes( base64_decode( $cookie ) ), true );
	$cookie[$key] = $value;
	setcookie( 'easy_announcements', base64_encode( json_encode( $cookie ) ), time() + 3600, '/' );
}

function easy_announcements_expired( $announcement ) {
	if ( !empty( $announcement ) ) {
		$expiration = get_field( 'announcement_expiration', $announcement );

		if ( $expiration != '' ) {
			$today = new DateTime("now", wp_timezone() );

			if ( $today->format( 'Y-m-d H:i:s' ) > $expiration ) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function easy_announcements_dissmissible() {
	$args = array( 
		'post_type' => 'announcement', 
		'posts_per_page' => -1,
	);
	$loop = new WP_Query( $args );

	while ( $loop->have_posts() ) : $loop->the_post();
		$announcement_dismissable = ( !empty( get_field( 'announcement_dismissable' ) ) && get_field( 'announcement_dismissable' ) == true ) ? true : false;
		
		if ( $announcement_dismissable == false ) {
			update_easy_announcements_cookie( 'dismiss-' . get_the_ID(), 'false' );
		}

		if ( easy_announcements_expired( get_the_ID() ) ) {
			$update_post = array( 'ID' => get_the_ID(), 'post_status' => 'draft' );
			wp_update_post( $update_post );
		}
	endwhile;
	wp_reset_postdata();
}
add_action( 'wp_loaded', 'easy_announcements_dissmissible' );

function easy_announcements_default_color( $type, $color ) {
	if ( !empty( $color ) && !empty( $type ) ) {
		$colors['primary']['background'] = '#cfe2ff';
		$colors['primary']['content'] = '#084298';
		$colors['secondary']['background'] = '#e2e3e5';
		$colors['secondary']['content'] = '#41464b';
		$colors['success']['background'] = '#d1e7dd';
		$colors['success']['content'] = '#0f5132';
		$colors['danger']['background'] = '#f8d7da';
		$colors['danger']['content'] = '#842029';
		$colors['warning']['background'] = '#fff3cd';
		$colors['warning']['content'] = '#664d03';
		$colors['info']['background'] = '#cff4fc';
		$colors['info']['content'] = '#055160';

		return $colors[$color][$type];
	}
}

function easy_announcements_color( $type, $color ) {
	if ( !empty( $color ) && !empty( $type ) ) {
		$option = get_option( 'easy_announcements' );
		$response_color = $option[$type . '_color_' . $color];
		if ( !empty( $response_color ) ) {
			return $response_color;
		} else {
			return easy_announcements_default_color( $type, $color );
		}
	} else {
		return false;
	}
}

function easy_announcements_contrast( $hexColor ) {
	$R1 = hexdec( substr( $hexColor, 1, 2 ) );
	$G1 = hexdec( substr( $hexColor, 3, 2 ) );
	$B1 = hexdec( substr( $hexColor, 5, 2 ) );

	$blackColor = "#000000";
	$R2BlackColor = hexdec( substr( $blackColor, 1, 2 ) );
	$G2BlackColor = hexdec( substr( $blackColor, 3, 2 ) );
	$B2BlackColor = hexdec( substr( $blackColor, 5, 2 ) );
	
	$L1 = 0.2126 * pow( $R1 / 255, 2.2 ) +
		  0.7152 * pow( $G1 / 255, 2.2 ) +
		  0.0722 * pow( $B1 / 255, 2.2 );

	$L2 = 0.2126 * pow( $R2BlackColor / 255, 2.2 ) +
		  0.7152 * pow( $G2BlackColor / 255, 2.2 ) +
		  0.0722 * pow( $B2BlackColor / 255, 2.2 );

	$contrastRatio = 0;
	if ( $L1 > $L2 ) {
		$contrastRatio = ( int )( ( $L1 + 0.05 ) / ( $L2 + 0.05 ) );
	} else {
		$contrastRatio = ( int )( ( $L2 + 0.05 ) / ( $L1 + 0.05 ) );
	}
	
	if ( $contrastRatio > 5 ) {
		return '#000000';
	} else { 
		return '#FFFFFF';
	}
}

function easy_announcements_show( $announcement ) {
	$announcement_id = get_the_ID( $announcement );

	global $post;
	$current_page_ID = $post->ID;

	$show_announcement = true;

	$announcement_pages_include = get_field( 'announcement_pages_include', $announcement_id ) ?? '';
	$announcement_pages_exclude = get_field( 'announcement_pages_exclude', $announcement_id ) ?? '';
	$announcement_dismissable = get_field( 'announcement_dismissable', $announcement_id ) ?? false;
	$announcement_placement = get_field( 'announcement_placement', $announcement_id ) ?? '';

	if ( $announcement_pages_include != '' ) {
		if ( in_array( $current_page_ID, $announcement_pages_include ) ) {
			$show_announcement = true;
		} else {
			$show_announcement = false;
		}
	} else if ( $announcement_pages_exclude != '' ) {
		if ( in_array( $current_page_ID, $announcement_pages_exclude ) ) {
			$show_announcement = false;
		} else {
			$show_announcement = true;
		}
	} else {
		$show_announcement = true;
	}

	if ( $announcement_dismissable == true || $announcement_placement == 'popup' ) {
		if (
			check_easy_announcements_cookie( 'dismiss-' . $announcement_id ) &&
			get_easy_announcements_cookie( 'dismiss-' . $announcement_id ) == 'true'
		) {
			$show_announcement = false;
		}
	}

	if ( easy_announcements_expired( $announcement_id ) ) {
		$show_announcement = false;
	}

	return $show_announcement;
}

require( EASY_ANNOUNCEMENTS_ABSPATH . '/includes/posttype.php' );
require( EASY_ANNOUNCEMENTS_ABSPATH . '/includes/templates.php' );
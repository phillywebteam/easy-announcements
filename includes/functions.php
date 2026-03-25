<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Easy Announcements Post Type
 */
class Easy_Announcements_Post_Type {
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	public function register() {
		register_post_type(
			'announcement',
			[
				'labels'               => [
					'name'                  => __( 'Announcements', 'easy-announcements' ),
					'singular_name'         => __( 'Announcement', 'easy-announcements' ),
					'add_new_item'          => __( 'Add New Announcement', 'easy-announcements' ),
					'edit_item'             => __( 'Edit Announcement', 'easy-announcements' ),
					'new_item'              => __( 'New Announcement', 'easy-announcements' ),
					'view_item'             => __( 'View Announcement', 'easy-announcements' ),
					'not_found'             => __( 'No announcement found', 'easy-announcements' ),
					'all_items'             => __( 'All announcements', 'easy-announcements' ),
					'insert_into_item'      => __( 'Insert into announcement', 'easy-announcements' ),
					'featured_image'        => __( 'Announcement Image', 'easy-announcements' ),
					'set_featured_image'    => __( 'Set announcement image', 'easy-announcements' ),
					'use_featured_image'    => __( 'Use as announcement image', 'easy-announcements' ),
					'remove_featured_image' => __( 'Remove announcement image', 'easy-announcements' ),
				],
				'menu_icon'            => 'dashicons-align-wide',
				'public'               => true,
				'has_archive'          => false,
				'show_in_rest'         => true,
				'exclude_from_search'  => true,
				'publicly_queryable'   => false,
				'show_in_nav_menus'    => false,
				'supports'             => [ 'title', 'editor', 'custom-fields' ],
				'map_meta_cap'         => true,
			]
		);
	}
}

/**
 * Easy Announcements Assets
 */
class Easy_Announcements_Assets {
	public function __construct() {
		add_action( 'init', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function register_scripts() {
		wp_register_style(
			'bootstrap',
			EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/css/bootstrap.min.css',
			[],
			'5.3.3',
			'all'
		);
		wp_register_script(
			'easy-announcements',
			EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/js/easy-announcements.min.js',
			[],
			EASY_ANNOUNCEMENTS_VERSION,
			true
		);
		wp_register_style(
			'easy-announcements',
			EASY_ANNOUNCEMENTS_PLUGIN_DIR . 'assets/css/easy-announcements.css',
			[ 'bootstrap' ],
			EASY_ANNOUNCEMENTS_VERSION,
			'all'
		);
	}

	public function enqueue_scripts() {
		if ( ! Easy_Announcements_Utils::has_active() ) {
			return;
		}
		wp_enqueue_script( 'easy-announcements' );
		wp_enqueue_style( 'easy-announcements' );

		// Localize settings for JavaScript
		$options = get_option( 'easy_announcements' );
		$ea_settings = [
			'headerSelector'  => ! empty( $options['header_selector'] ) ? $options['header_selector'] : null,
			'contentSelector' => ! empty( $options['content_selector'] ) ? $options['content_selector'] : null,
			'footerSelector'  => ! empty( $options['footer_selector'] ) ? $options['footer_selector'] : null,
		];
		wp_localize_script( 'easy-announcements', 'easyAnnouncementsSettings', $ea_settings );
	}
}

/**
 * Easy Announcements Meta Fields
 */
class Easy_Announcements_Meta {
	public function __construct() {
		add_action( 'init', [ $this, 'register_fields' ], 5 );
		add_action( 'rest_insert_announcement', [ $this, 'sanitize_meta_on_rest' ], 10, 2 );
	}

	public function register_fields() {
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
			register_post_meta(
				'announcement',
				$meta_key,
				[
					'single'        => true,
					'show_in_rest'  => true,
					'auth_callback' => function() {
						return current_user_can( 'edit_posts' );
					},
				]
			);
		}
	}

	public function sanitize_meta_on_rest( $post, $request ) {
		$params = $request->get_json_params();
		if ( ! isset( $params['meta'] ) ) {
			return;
		}

		$meta = $params['meta'];
		foreach ( $meta as $key => $value ) {
			$sanitized = $this->sanitize( $key, $value );
			update_post_meta( $post->ID, $key, $sanitized );
		}
	}

	public static function sanitize( $meta_key, $value ) {
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
					'announcement_placement'      => [ 'header', 'footer', 'content', 'popup' ],
					'announcement_attachment'     => [ 'before', 'after' ],
					'announcement_color'          => [ 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'custom' ],
					'announcement_size'           => [ 'default', 'compact', 'tall', 'none' ],
					'announcement_text_alignment' => [ '', 'start', 'center', 'end' ],
					'announcement_text_size'      => [ 'default', 'small', 'large' ],
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
}

/**
 * Easy Announcements Admin
 */
class Easy_Announcements_Admin {
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'remove_meta_boxes' ], 100 );
		add_action( 'admin_head-post-new.php', [ $this, 'admin_css' ] );
		add_action( 'admin_head-post.php', [ $this, 'admin_css' ] );
	}

	public function remove_meta_boxes() {
		remove_meta_box( 'wpseo_meta', 'announcement', 'normal' );
		remove_meta_box( 'slider_revolution_metabox', 'announcement', 'normal' );
		remove_meta_box( 'rocket_post_exclude', 'announcement', 'normal' );
	}

	public function admin_css() {
		global $post_type;
		if ( $post_type === 'announcement' ) {
			echo '<style type="text/css">#post-preview, #view-post-btn, #wp-admin-bar-view{display:none;}#minor-publishing-actions{padding:0;}</style>' . "\n";
		}
	}
}

/**
 * Easy Announcements Utilities
 */
class Easy_Announcements_Utils {
	/**
	 * Get announcement cookie value
	 */
	public static function get_cookie( $key ) {
		if ( isset( $_COOKIE['easy_announcements'] ) ) {
			$raw    = sanitize_text_field( wp_unslash( $_COOKIE['easy_announcements'] ) );
			$cookie = json_decode( base64_decode( $raw ), true );
			if ( is_array( $cookie ) && array_key_exists( $key, $cookie ) ) {
				return $cookie[ $key ];
			}
		}
		return '';
	}

	/**
	 * Check if announcement cookie exists and is truthy
	 */
	public static function check_cookie( $key ) {
		if ( isset( $_COOKIE['easy_announcements'] ) ) {
			$raw    = sanitize_text_field( wp_unslash( $_COOKIE['easy_announcements'] ) );
			$cookie = json_decode( base64_decode( $raw ), true );
			if ( is_array( $cookie ) && array_key_exists( $key, $cookie ) ) {
				return ( $cookie[ $key ] !== '' && $cookie[ $key ] !== false );
			}
		}
		return false;
	}

	/**
	 * Check if any active announcements exist
	 */
	public static function has_active() {
		$has = get_transient( 'easy_announcements_has_active' );
		if ( $has === false ) {
			$query = new WP_Query(
				[
					'post_type'      => 'announcement',
					'posts_per_page' => 1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
				]
			);
			$has = $query->found_posts > 0 ? '1' : '0';
			set_transient( 'easy_announcements_has_active', $has, DAY_IN_SECONDS );
		}
		return $has === '1';
	}

	/**
	 * Flush the has_active transient when post status changes
	 */
	public static function flush_active_transient( $new_status, $old_status, $post ) {
		if ( $post->post_type === 'announcement' && $new_status !== $old_status ) {
			delete_transient( 'easy_announcements_has_active' );
		}
	}

	/**
	 * Check if an announcement has expired
	 */
	public static function is_expired( $announcement_id ) {
		if ( empty( $announcement_id ) ) {
			return false;
		}

		$expiration = get_post_meta( $announcement_id, 'announcement_expiration', true );
		if ( empty( $expiration ) ) {
			return false;
		}

		$today = new DateTime( 'now', wp_timezone() );
		return $today->format( 'Y-m-d H:i:s' ) > $expiration;
	}

	/**
	 * Get default color for a type and color
	 */
	public static function get_default_color( $type, $color ) {
		if ( empty( $color ) || empty( $type ) ) {
			return '';
		}

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

	/**
	 * Get color for a type and color (with option override)
	 */
	public static function get_color( $type, $color ) {
		if ( empty( $color ) || empty( $type ) ) {
			return false;
		}

		$option = get_option( 'easy_announcements' );
		$key    = $type . '_color_' . $color;

		if ( ! empty( $option[ $key ] ) ) {
			return $option[ $key ];
		}

		return self::get_default_color( $type, $color );
	}

	/**
	 * Calculate contrast color (black or white) for a given hex color
	 */
	public static function get_contrast_color( $hex_color ) {
		$hex = ltrim( $hex_color, '#' );
		if ( strlen( $hex ) !== 6 ) {
			return '#000000';
		}

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		$l = 0.2126 * pow( $r / 255, 2.2 )
			+ 0.7152 * pow( $g / 255, 2.2 )
			+ 0.0722 * pow( $b / 255, 2.2 );

		$contrast_ratio = (int) ( ( $l + 0.05 ) / 0.05 );

		return ( $contrast_ratio > 5 ) ? '#000000' : '#FFFFFF';
	}
}

/**
 * Easy Announcements Expiration Handler
 */
class Easy_Announcements_Expiration {
	public function __construct() {
		add_action( 'transition_post_status', [ 'Easy_Announcements_Utils', 'flush_active_transient' ], 10, 3 );
		add_action( 'easy_announcements_expire_check', [ $this, 'check_expired' ] );
	}

	public function check_expired() {
		$args = [
			'post_type'      => 'announcement',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		];
		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) :
			$loop->the_post();
			if ( Easy_Announcements_Utils::is_expired( get_the_ID() ) ) {
				wp_update_post( [ 'ID' => get_the_ID(), 'post_status' => 'draft' ] );
			}
		endwhile;
		wp_reset_postdata();
	}
}

/**
 * Easy Announcements Display Logic
 */
class Easy_Announcements_Display {
	/**
	 * Determine if an announcement should be shown
	 */
	public static function should_show( $announcement_id ) {
		global $wp_query;

		// Guard: not all pages have a queried post (archives, 404, etc.)
		if ( empty( $wp_query->post->ID ) ) {
			return true;
		}
		$current_page_id = $wp_query->post->ID;

		$show = true;

		$pages_include = get_post_meta( $announcement_id, 'announcement_pages_include', true ) ?: [];
		$pages_exclude = get_post_meta( $announcement_id, 'announcement_pages_exclude', true ) ?: [];
		$dismissable   = get_post_meta( $announcement_id, 'announcement_dismissable', true );
		$placement     = get_post_meta( $announcement_id, 'announcement_placement', true ) ?: '';

		if ( ! is_array( $pages_include ) ) {
			$pages_include = [];
		}
		if ( ! is_array( $pages_exclude ) ) {
			$pages_exclude = [];
		}

		if ( ! empty( $pages_include ) ) {
			if ( ! in_array( $current_page_id, $pages_include, true ) ) {
				$show = false;
			}
		} elseif ( ! empty( $pages_exclude ) ) {
			if ( in_array( $current_page_id, $pages_exclude, true ) ) {
				$show = false;
			}
		}

		if ( $dismissable == '1' || $placement === 'popup' ) {
			if (
				Easy_Announcements_Utils::check_cookie( 'dismiss-' . $announcement_id ) &&
				Easy_Announcements_Utils::get_cookie( 'dismiss-' . $announcement_id ) === 'true'
			) {
				$show = false;
			}
		}

		if ( Easy_Announcements_Utils::is_expired( $announcement_id ) ) {
			$show = false;
		}

		return $show;
	}

	/**
	 * Output main announcements container
	 */
	public static function render_main() {
		if ( ! Easy_Announcements_Utils::has_active() ) {
			return;
		}

		global $wp_query;
		$page_id = ! empty( $wp_query->post->ID ) ? $wp_query->post->ID : 0;
		?>
<div id="easy-announcements-container" data-page-id="<?php echo esc_attr( $page_id ); ?>"></div>
<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		easyAnnouncementsInit(<?php echo intval( $page_id ); ?>);
	});
</script>
		<?php
	}
}

/**
 * Easy Announcements REST API
 */
class Easy_Announcements_REST {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_endpoint' ] );
	}

	public function register_endpoint() {
		register_rest_route(
			'easy-announcements/v1',
			'/announcements',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'handler' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'page_id' => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'validate_callback' => function( $param ) {
							return is_numeric( $param );
						},
					],
				],
			]
		);
	}

	public function handler( $request ) {
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

				$should_show = Easy_Announcements_Display::should_show( $post_id );

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

		return new WP_REST_Response(
			[
				'success'       => true,
				'announcements' => $announcements,
			],
			200
		);
	}
}

/**
 * Easy Announcements Main Plugin Class
 */
class Easy_Announcements {
	public function __construct() {
		// Instantiate all components
		new Easy_Announcements_Post_Type();
		new Easy_Announcements_Assets();
		new Easy_Announcements_Meta();
		new Easy_Announcements_Admin();
		new Easy_Announcements_Expiration();
		new Easy_Announcements_REST();

		// Add footer hook for rendering
		add_action( 'wp_footer', [ 'Easy_Announcements_Display', 'render_main' ] );
	}
}

// Initialize the plugin
new Easy_Announcements();

/**
 * ============================================================================
 * Backward Compatibility Wrappers
 * ============================================================================
 * These functions are maintained for backward compatibility with external code.
 * They wrap the new class-based methods.
 */

/**
 * Get announcement cookie - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Utils::get_cookie() instead
 */
function get_easy_announcements_cookie( $key ) {
	return Easy_Announcements_Utils::get_cookie( $key );
}

/**
 * Check announcement cookie - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Utils::check_cookie() instead
 */
function check_easy_announcements_cookie( $key ) {
	return Easy_Announcements_Utils::check_cookie( $key );
}

/**
 * Check if announcement has expired - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Utils::is_expired() instead
 */
function easy_announcements_expired( $announcement_id ) {
	return Easy_Announcements_Utils::is_expired( $announcement_id );
}

/**
 * Get default color - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Utils::get_default_color() instead
 */
function easy_announcements_default_color( $type, $color ) {
	return Easy_Announcements_Utils::get_default_color( $type, $color );
}

/**
 * Get color with option override - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Utils::get_color() instead
 */
function easy_announcements_color( $type, $color ) {
	return Easy_Announcements_Utils::get_color( $type, $color );
}

/**
 * Get contrast color - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Utils::get_contrast_color() instead
 */
function easy_announcements_contrast( $hexColor ) {
	return Easy_Announcements_Utils::get_contrast_color( $hexColor );
}

/**
 * Determine if announcement should be shown - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Display::should_show() instead
 */
function easy_announcements_show( $announcement_id ) {
	return Easy_Announcements_Display::should_show( $announcement_id );
}

/**
 * Check if any active announcements exist - backward compatible wrapper
 *
 * @deprecated Use Easy_Announcements_Utils::has_active() instead
 */
function easy_announcements_has_active() {
	return Easy_Announcements_Utils::has_active();
}

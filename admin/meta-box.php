<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Easy Announcements Meta Box
 */
class Easy_Announcements_Meta_Box {
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
	}

	public function add_meta_box() {
		add_meta_box(
			'easy-announcements-settings',
			__( 'Announcement Settings', 'easy-announcements' ),
			[ $this, 'render' ],
			'announcement',
			'normal',
			'high'
		);
	}

	public function render( $post ) {
		wp_nonce_field( 'easy_announcements_save_meta', 'easy_announcements_nonce' );

		$placement   = get_post_meta( $post->ID, 'announcement_placement', true )               ?: '';
		$attachment  = get_post_meta( $post->ID, 'announcement_attachment', true )              ?: 'after';
		$pages_inc   = get_post_meta( $post->ID, 'announcement_pages_include', true )           ?: [];
		$pages_exc   = get_post_meta( $post->ID, 'announcement_pages_exclude', true )           ?: [];
		$expiration  = get_post_meta( $post->ID, 'announcement_expiration', true )              ?: '';
		$color       = get_post_meta( $post->ID, 'announcement_color', true )                   ?: 'primary';
		$custom_bg   = get_post_meta( $post->ID, 'announcement_custom_color_background', true ) ?: '';
		$custom_fg   = get_post_meta( $post->ID, 'announcement_custom_color_content', true )    ?: '';
		$size        = get_post_meta( $post->ID, 'announcement_size', true )                    ?: 'default';
		$text_align  = get_post_meta( $post->ID, 'announcement_text_alignment', true )          ?: '';
		$text_size   = get_post_meta( $post->ID, 'announcement_text_size', true )               ?: 'default';
		$url         = get_post_meta( $post->ID, 'announcement_url', true )                     ?: '';
		$show_title  = get_post_meta( $post->ID, 'announcement_show_title', true );
		$sticky      = get_post_meta( $post->ID, 'announcement_sticky', true );
		$dismissable  = get_post_meta( $post->ID, 'announcement_dismissable', true );
		$popup_delay  = (int) get_post_meta( $post->ID, 'announcement_popup_delay', true );

		if ( ! is_array( $pages_inc ) ) $pages_inc = [];
		if ( ! is_array( $pages_exc ) ) $pages_exc = [];

		$all_pages = get_posts( [
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		] );

		// Convert stored Y-m-d H:i:s to datetime-local input format Y-m-dTH:i
		$expiration_input = '';
		if ( ! empty( $expiration ) ) {
			$dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $expiration, wp_timezone() );
			if ( $dt ) {
				$expiration_input = $dt->format( 'Y-m-d\TH:i' );
			}
		}
		?>
	<div class="ea-meta-wrap">

		<div class="ea-row">
			<label class="ea-label" for="announcement_placement"><?php esc_html_e( 'Placement', 'easy-announcements' ); ?> <span style="color:red" aria-hidden="true">*</span></label>
			<div class="ea-field" style="display:flex;gap:8px;align-items:center;">
				<div id="ea-attachment-field">
					<select name="announcement_attachment" id="announcement_attachment">
						<option value="before" <?php selected( $attachment, 'before' ); ?>><?php esc_html_e( 'Before', 'easy-announcements' ); ?></option>
						<option value="after"  <?php selected( $attachment, 'after' );  ?>><?php esc_html_e( 'After',  'easy-announcements' ); ?></option>
					</select>
				</div>
				<select name="announcement_placement" id="announcement_placement" required>
					<option value=""><?php esc_html_e( '— Select —', 'easy-announcements' ); ?></option>
					<option value="header"  <?php selected( $placement, 'header' );  ?>><?php esc_html_e( 'Header',  'easy-announcements' ); ?></option>
					<option value="footer"  <?php selected( $placement, 'footer' );  ?>><?php esc_html_e( 'Footer',  'easy-announcements' ); ?></option>
					<option value="content" <?php selected( $placement, 'content' ); ?>><?php esc_html_e( 'Content', 'easy-announcements' ); ?></option>
					<option value="popup"   <?php selected( $placement, 'popup' );   ?>><?php esc_html_e( 'Popup',   'easy-announcements' ); ?></option>
				</select>
			</div>
		</div>

		<div class="ea-row ea-hidden" id="ea-popup-delay-row">
			<label class="ea-label" for="announcement_popup_delay"><?php esc_html_e( 'Show After', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<input type="number" name="announcement_popup_delay" id="announcement_popup_delay" value="<?php echo esc_attr( $popup_delay ); ?>" min="0" step="1" class="small-text" />
				<p class="description"><?php esc_html_e( 'Seconds before the popup appears. 0 = show immediately.', 'easy-announcements' ); ?></p>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label"><?php esc_html_e( 'Pages Included', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<select name="announcement_pages_include[]" id="announcement_pages_include" multiple>
					<?php foreach ( $all_pages as $page ) : ?>
						<option value="<?php echo esc_attr( $page->ID ); ?>" <?php echo in_array( $page->ID, $pages_inc, true ) ? 'selected' : ''; ?>>
							<?php echo esc_html( $page->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Show only on these pages. If empty, shows on all pages.', 'easy-announcements' ); ?></p>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label"><?php esc_html_e( 'Pages Excluded', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<select name="announcement_pages_exclude[]" id="announcement_pages_exclude" multiple>
					<?php foreach ( $all_pages as $page ) : ?>
						<option value="<?php echo esc_attr( $page->ID ); ?>" <?php echo in_array( $page->ID, $pages_exc, true ) ? 'selected' : ''; ?>>
							<?php echo esc_html( $page->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e( 'Hide on these pages. Only used when Pages Included is empty.', 'easy-announcements' ); ?></p>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label" for="announcement_expiration"><?php esc_html_e( 'Expiration', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<input type="datetime-local" name="announcement_expiration" id="announcement_expiration" value="<?php echo esc_attr( $expiration_input ); ?>" />
				<p class="description"><?php esc_html_e( 'Leave blank for no expiration. Uses the site timezone.', 'easy-announcements' ); ?></p>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label"><?php esc_html_e( 'Color', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<div class="ea-color-buttons">
					<?php foreach ( [ 'primary', 'secondary', 'success', 'danger', 'warning', 'info' ] as $c ) :
					$bg = Easy_Announcements_Utils::get_color( 'background', $c );
					?>
						<label>
							<input type="radio" name="announcement_color" value="<?php echo esc_attr( $c ); ?>" <?php checked( $color, $c ); ?> />
							<span class="ea-swatch" style="background:<?php echo esc_attr( $bg ); ?>;"></span>
							<?php echo esc_html( ucfirst( $c ) ); ?>
						</label>
					<?php endforeach; ?>
					<label>
						<input type="radio" name="announcement_color" value="custom" <?php checked( $color, 'custom' ); ?> />
						<?php esc_html_e( 'Custom', 'easy-announcements' ); ?>
					</label>
				</div>
			</div>
		</div>

		<div class="ea-row <?php echo $color !== 'custom' ? 'ea-hidden' : ''; ?>" id="ea-custom-colors-row">
			<label class="ea-label"><?php esc_html_e( 'Custom Colors', 'easy-announcements' ); ?></label>
			<div class="ea-field" style="display:flex;gap:24px;flex-wrap:wrap;">
				<div>
					<label for="announcement_custom_color_background"><strong><?php esc_html_e( 'Background', 'easy-announcements' ); ?></strong></label><br>
					<input type="text" name="announcement_custom_color_background" id="announcement_custom_color_background" class="easy-announcements-color-picker" value="<?php echo esc_attr( $custom_bg ); ?>" />
				</div>
				<div>
					<label for="announcement_custom_color_content"><strong><?php esc_html_e( 'Text & Links', 'easy-announcements' ); ?></strong></label><br>
					<input type="text" name="announcement_custom_color_content" id="announcement_custom_color_content" class="easy-announcements-color-picker" value="<?php echo esc_attr( $custom_fg ); ?>" />
					<p class="description"><?php esc_html_e( 'Leave blank to auto-detect from background.', 'easy-announcements' ); ?></p>
				</div>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label" for="announcement_size"><?php esc_html_e( 'Size', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<select name="announcement_size" id="announcement_size">
					<option value="default" <?php selected( $size, 'default' ); ?>><?php esc_html_e( 'Default',    'easy-announcements' ); ?></option>
					<option value="compact" <?php selected( $size, 'compact' ); ?>><?php esc_html_e( 'Compact',    'easy-announcements' ); ?></option>
					<option value="tall"    <?php selected( $size, 'tall' );    ?>><?php esc_html_e( 'Tall',       'easy-announcements' ); ?></option>
					<option value="none"    <?php selected( $size, 'none' );    ?>><?php esc_html_e( 'No Padding', 'easy-announcements' ); ?></option>
				</select>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label" for="announcement_text_alignment"><?php esc_html_e( 'Text Alignment', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<select name="announcement_text_alignment" id="announcement_text_alignment">
					<option value=""       <?php selected( $text_align, '' );       ?>><?php esc_html_e( 'Default', 'easy-announcements' ); ?></option>
					<option value="start"  <?php selected( $text_align, 'start' );  ?>><?php esc_html_e( 'Left',    'easy-announcements' ); ?></option>
					<option value="center" <?php selected( $text_align, 'center' ); ?>><?php esc_html_e( 'Center',  'easy-announcements' ); ?></option>
					<option value="end"    <?php selected( $text_align, 'end' );    ?>><?php esc_html_e( 'Right',   'easy-announcements' ); ?></option>
				</select>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label" for="announcement_text_size"><?php esc_html_e( 'Text Size', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<select name="announcement_text_size" id="announcement_text_size">
					<option value="default" <?php selected( $text_size, 'default' ); ?>><?php esc_html_e( 'Default', 'easy-announcements' ); ?></option>
					<option value="small"   <?php selected( $text_size, 'small' );   ?>><?php esc_html_e( 'Small',   'easy-announcements' ); ?></option>
					<option value="large"   <?php selected( $text_size, 'large' );   ?>><?php esc_html_e( 'Large',   'easy-announcements' ); ?></option>
				</select>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label" for="announcement_url"><?php esc_html_e( 'Destination URL', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<input type="url" name="announcement_url" id="announcement_url" class="regular-text" value="<?php echo esc_url( $url ); ?>" placeholder="https://" />
				<p class="description"><?php esc_html_e( 'Makes the whole announcement clickable.', 'easy-announcements' ); ?></p>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label"><?php esc_html_e( 'Show Title', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<label>
					<input type="checkbox" name="announcement_show_title" value="1" <?php checked( $show_title, '1' ); ?> />
					<?php esc_html_e( 'Show the announcement title above the content', 'easy-announcements' ); ?>
				</label>
			</div>
		</div>

		<div class="ea-row" id="ea-sticky-row">
			<label class="ea-label"><?php esc_html_e( 'Is Sticky', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<label>
					<input type="checkbox" name="announcement_sticky" value="1" <?php checked( $sticky, '1' ); ?> />
					<?php esc_html_e( 'Stick to top or bottom of page on scroll', 'easy-announcements' ); ?>
				</label>
			</div>
		</div>

		<div class="ea-row">
			<label class="ea-label"><?php esc_html_e( 'Is Dismissable', 'easy-announcements' ); ?></label>
			<div class="ea-field">
				<label>
					<input type="checkbox" name="announcement_dismissable" value="1" <?php checked( $dismissable, '1' ); ?> />
					<?php esc_html_e( 'Allow users to dismiss and not see again', 'easy-announcements' ); ?>
				</label>
			</div>
		</div>

	</div><!-- .ea-meta-wrap -->

	<script type="text/javascript">
	(function($) {
		function eaTogglePlacement() {
			var isPopup = ($('#announcement_placement').val() === 'popup');
			$('#ea-attachment-field').toggleClass('ea-hidden', isPopup);
			$('#ea-sticky-row').toggleClass('ea-hidden', isPopup);
			$('#ea-popup-delay-row').toggleClass('ea-hidden', !isPopup);
		}
		function eaToggleCustomColors() {
			var isCustom = ($('input[name="announcement_color"]:checked').val() === 'custom');
			$('#ea-custom-colors-row').toggleClass('ea-hidden', !isCustom);
		}
		$('#announcement_placement').on('change', eaTogglePlacement);
		$('input[name="announcement_color"]').on('change', eaToggleCustomColors);
		eaTogglePlacement();
		eaToggleCustomColors();
		// Color pickers initialised globally by easy-announcements-admin.js
	})(jQuery);
	</script>
		<?php
	}
}

/**
 * Backward Compatibility Wrappers
 */
function easy_announcements_add_meta_box() {
	// Handled by Easy_Announcements_Meta_Box class
}

add_action( 'save_post_announcement', 'easy_announcements_save_meta_box', 10, 2 );
function easy_announcements_save_meta_box( $post_id, $post ) {
	// Skip REST API requests - they're handled by registered meta fields
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	// For classic editor: verify nonce
	if ( ! isset( $_POST['easy_announcements_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['easy_announcements_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'easy_announcements_save_meta' ) ) {
		return;
	}

	// Don't save during autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check user permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Placement field — validate against allowed values
	$allowed_placements = [ 'header', 'footer', 'content', 'popup' ];
	if ( isset( $_POST['announcement_placement'] ) ) {
		$value = sanitize_text_field( wp_unslash( $_POST['announcement_placement'] ) );
		if ( in_array( $value, $allowed_placements, true ) ) {
			update_post_meta( $post_id, 'announcement_placement', $value );
		}
	} else {
		delete_post_meta( $post_id, 'announcement_placement' );
	}

	// Attachment field — validate against allowed values
	$allowed_attachments = [ 'before', 'after' ];
	if ( isset( $_POST['announcement_attachment'] ) ) {
		$value = sanitize_text_field( wp_unslash( $_POST['announcement_attachment'] ) );
		if ( in_array( $value, $allowed_attachments, true ) ) {
			update_post_meta( $post_id, 'announcement_attachment', $value );
		}
	} else {
		delete_post_meta( $post_id, 'announcement_attachment' );
	}

	// Simple select fields — validate against allowed values
	$select_fields = [
		'announcement_color' => [ 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'custom' ],
		'announcement_size' => [ 'default', 'compact', 'tall', 'none' ],
		'announcement_text_alignment' => [ '', 'start', 'center', 'end' ],
		'announcement_text_size' => [ 'default', 'small', 'large' ],
	];
	foreach ( $select_fields as $field => $allowed_values ) {
		if ( isset( $_POST[ $field ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
			if ( in_array( $value, $allowed_values, true ) ) {
				update_post_meta( $post_id, $field, $value );
			}
		} else {
			delete_post_meta( $post_id, $field );
		}
	}

	// Popup delay (integer seconds)
	if ( isset( $_POST['announcement_popup_delay'] ) ) {
		$delay = isset( $_POST['announcement_popup_delay'] ) ? intval( wp_unslash( $_POST['announcement_popup_delay'] ) ) : 0;
		update_post_meta( $post_id, 'announcement_popup_delay', $delay );
	} else {
		delete_post_meta( $post_id, 'announcement_popup_delay' );
	}

	// URL field
	if ( ! empty( $_POST['announcement_url'] ) ) {
		$url = esc_url_raw( wp_unslash( $_POST['announcement_url'] ) );
		update_post_meta( $post_id, 'announcement_url', $url );
	} else {
		delete_post_meta( $post_id, 'announcement_url' );
	}

	// Hex color fields
	foreach ( [ 'announcement_custom_color_background', 'announcement_custom_color_content' ] as $field ) {
		if ( ! empty( $_POST[ $field ] ) ) {
			$color = sanitize_hex_color( wp_unslash( $_POST[ $field ] ) );
			update_post_meta( $post_id, $field, $color );
		} else {
			delete_post_meta( $post_id, $field );
		}
	}

	// Expiration — convert datetime-local (Y-m-dTH:i) → Y-m-d H:i:s in site timezone
	if ( ! empty( $_POST['announcement_expiration'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_POST['announcement_expiration'] ) );
		$dt  = DateTime::createFromFormat( 'Y-m-d\TH:i', $raw, wp_timezone() );
		if ( $dt ) {
			$expiration = $dt->format( 'Y-m-d H:i:s' );
			update_post_meta( $post_id, 'announcement_expiration', $expiration );
		}
	} else {
		delete_post_meta( $post_id, 'announcement_expiration' );
	}

	// Checkbox (boolean) fields — stored as '1' or '0'
	foreach ( [ 'announcement_show_title', 'announcement_sticky', 'announcement_dismissable' ] as $field ) {
		$value = isset( $_POST[ $field ] ) ? '1' : '0';
		update_post_meta( $post_id, $field, $value );
	}

	// Page include/exclude arrays
	foreach ( [ 'announcement_pages_include', 'announcement_pages_exclude' ] as $field ) {
		if ( isset( $_POST[ $field ] ) && is_array( $_POST[ $field ] ) ) {
			// Sanitize and convert to integers
			$values = array_map( 'intval', wp_unslash( $_POST[ $field ] ) );
			$ids    = array_filter( $values );
			if ( ! empty( $ids ) ) {
				update_post_meta( $post_id, $field, array_values( $ids ) );
			} else {
				delete_post_meta( $post_id, $field );
			}
		} else {
			delete_post_meta( $post_id, $field );
		}
	}
}

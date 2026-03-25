<?php
function easy_announcements_main() {
	$easy_announcements_args = [
		'post_type'      => 'announcement',
		'posts_per_page' => -1,
	];
	$easy_announcements_loop = new WP_Query( $easy_announcements_args );

	$easy_announcements     = [];
	$easy_announcements_ids = [];
	$easy_announcements_inject = '';

	if ( $easy_announcements_loop->have_posts() ) :
		while ( $easy_announcements_loop->have_posts() ) :
			$easy_announcements_loop->the_post();

			$ea_post_id = get_the_ID();
			$placement  = get_post_meta( $ea_post_id, 'announcement_placement', true )  ?: '';
			$attachment = get_post_meta( $ea_post_id, 'announcement_attachment', true ) ?: '';

			if ( ! empty( $placement ) && ( $placement === 'popup' || ! empty( $attachment ) ) ) {
				$easy_announcements_ids[] = $ea_post_id;
				$show_announcement = easy_announcements_show( $ea_post_id );

				if ( $show_announcement ) {
					$cache_key         = 'easy_announcements_' . $ea_post_id;
					$this_announcement = wp_cache_get( $cache_key, 'easy_announcements' );

					if ( $this_announcement === false ) {
						ob_start();
						switch ( $placement ) {
							case 'popup':
								include EASY_ANNOUNCEMENTS_ABSPATH . 'templates/popup.php';
								break;
							default:
								include EASY_ANNOUNCEMENTS_ABSPATH . 'templates/default.php';
								break;
						}
						$this_announcement = ob_get_clean();
						// Cache for 24 hours
						wp_cache_set( $cache_key, $this_announcement, 'easy_announcements', DAY_IN_SECONDS );
					}

					if ( ! empty( $this_announcement ) ) {
						$easy_announcements[ $placement ][ $attachment ][ $ea_post_id ] = $this_announcement;
					}
				}
			}
		endwhile;
		wp_reset_postdata();

		if ( ! empty( $easy_announcements ) ) {
			foreach ( $easy_announcements as $placement => $attachments ) {
				$selector = easy_announcements_setting( $placement . '_selector' ) ?: '';

				foreach ( $attachments as $attachment => $announcements ) {
					$classes   = [];
					$classes[] = 'site-announcements-' . $placement;
					$classes[] = 'site-announcements-' . $attachment;
					$classes[] = 'site-announcements-' . $placement . '-' . $attachment;
					$classes   = apply_filters( 'ea_announcements_classes', implode( ' ', $classes ) );

					ob_start();
					?>
		var easy_announcements_<?php echo esc_js( $placement ); ?> = '';<?php
					$easy_announcements_inject .= ob_get_clean();

					ob_start();
					?>
					<section class="site-announcements <?php echo esc_attr( $classes ); ?>" role="region" aria-label="<?php esc_attr_e( 'Site Announcements', 'easy-announcements' ); ?>">
					<?php
					do_action( 'easy_announcements_' . $placement . '_' . $attachment . '_start' );
					$section_start = str_replace( [ "\r", "\n", "\t" ], '', ob_get_clean() );

					foreach ( $announcements as $id => $announcement ) {
						ob_start();
						?>
		if (get_easy_announcements_cookie('dismiss-<?php echo esc_js( $id ); ?>') != 'true' && !$('.announcement-<?php echo esc_js( $id ); ?>').length) easy_announcements_<?php echo esc_js( $placement ); ?> += '<?php echo str_replace( [ "\r", "\n", "\t" ], '', wp_kses_post( addslashes( $announcement ) ) ); ?>';<?php
						$easy_announcements_inject .= ob_get_clean();
					}

					ob_start();
					do_action( 'easy_announcements_' . $placement . '_' . $attachment . '_end' );
					?>
					</section>
					<?php
					$section_end = str_replace( [ "\r", "\n", "\t" ], '', ob_get_clean() );

					// Determine DOM target and jQuery method
					$dom_selector = $selector;
					$dom_method   = $attachment;
					if ( $placement === 'popup' ) {
						$dom_selector = 'body';
						$dom_method   = 'append';
					} elseif ( $placement === 'header' && $attachment === 'before' ) {
						$dom_selector = 'body';
						$dom_method   = 'prepend';
					} elseif ( $placement === 'footer' && $attachment === 'after' ) {
						$dom_selector = 'body';
						$dom_method   = 'append';
					}

					ob_start();
					?>
		$('<?php echo esc_js( $dom_selector ); ?>').<?php echo esc_js( $dom_method ); ?>('<?php echo wp_kses_post( addslashes( $section_start ) ); ?>' + easy_announcements_<?php echo esc_js( $placement ); ?> + '<?php echo wp_kses_post( addslashes( $section_end ) ); ?>');<?php
					$easy_announcements_inject .= ob_get_clean();
				}
			}
		}

		if ( ! empty( $easy_announcements_inject ) ) {
			ob_start();
			?>
<script type="text/javascript" id="easy-announcements-inject">
	var announcement_ids = [<?php echo implode( ',', array_map( 'intval', $easy_announcements_ids ) ); ?>];

	function easy_announcements_defer(method) {
		if (window.jQuery && window.Cookies) {
			method();
		} else {
			setTimeout(function() { easy_announcements_defer(method); }, 50);
		}
	}
	easy_announcements_defer(function() {
		jQuery(function($) {
			if (typeof Cookies.get('easy_announcements') === 'undefined') {
				set_easy_announcements_cookie();
			}
			<?php echo str_replace( '&amp;', '&', wp_kses_post( $easy_announcements_inject ) ); ?>

			$('.ea-modal[id]').each(function() {
				var $modal   = $(this);
				var modal_id = $modal.attr('id');
				var delay    = parseInt($modal.data('popup-delay') || 0, 10);

				if (delay > 0) {
					setTimeout(function() { eaShowModal(modal_id); }, delay);
				} else {
					eaShowModal(modal_id);
				}

				$(document).on('ea.modal.dismiss', '#' + modal_id, function() {
					var dismiss_id = modal_id.replace('modal', '');
					update_easy_announcements_cookie('dismiss-' + dismiss_id, 'true');
				});
			});
		});
	});
</script>
			<?php
			echo ob_get_clean();
		}

	endif;
}
add_action( 'wp_footer', 'easy_announcements_main' );

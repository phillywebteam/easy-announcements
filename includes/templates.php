<?php
function easy_announcements_main() {
	$easy_announcements_args = array( 
		'post_type' => 'announcement', 
		'posts_per_page' => -1,
	);
	$easy_announcements_loop = new WP_Query( $easy_announcements_args );
	
	$easy_announcements = array();
	$easy_announcements_ids = array();
	$easy_announcements_inject = '';
	
	if ( $easy_announcements_loop->have_posts() ) :
		while ( $easy_announcements_loop->have_posts() ) : 
			$ea_post = $easy_announcements_loop->the_post();
			if ( !empty( get_field( 'announcement_placement', $ea_post ) ) && !empty( get_field( 'announcement_attachment', $ea_post ) ) ) {
				$easy_announcements_ids[] = get_the_ID( $ea_post );
				$placement = get_field( 'announcement_placement', $ea_post ) ?? '';
				$attachment = get_field( 'announcement_attachment', $ea_post ) ?? '';
				$show_announcement = easy_announcements_show( $ea_post );

				if ( $show_announcement ) {
					ob_start();
					switch ( $placement ) {
						case 'popup':
							include EASY_ANNOUNCEMENTS_ABSPATH . '/templates/popup.php';
							break;
						default:
							include EASY_ANNOUNCEMENTS_ABSPATH . '/templates/default.php';
							break;
					}
					$this_announcement = ob_get_clean();

					if ( !empty( $this_announcement ) ) $easy_announcements[$placement][$attachment][get_the_ID( $ea_post )] = $this_announcement;
				}
			}
		endwhile;
		wp_reset_postdata();

		if ( !empty( $easy_announcements ) && is_array( $easy_announcements ) ) {
			foreach( $easy_announcements as $placement => $attachments ) {
				$classes = array();
				$classes[] = 'site-announcements-' . $placement;

				$selector = easy_announcements_setting( $placement . '_selector' ) ?? '';

				if ( !empty( $placement_classes ) ) $classes[] = $placement_classes;

				if ( is_array( $attachments ) ) {
					foreach( $attachments as $attachment => $easy_announcements ) {
						ob_start();
						?>
			var easy_announcements_<?php echo esc_attr( $placement ); ?> = '';<?php
						$easy_announcements_inject .= ob_get_clean();

						$classes[] = 'site-announcements-' . $attachment;
						$classes[] = 'site-announcements-' . $placement . '-' . $attachment;
						$classes = apply_filters( 'ea_announcements_classes', implode( ' ', $classes ) );
						ob_start();
						?>
						<section class="site-announcements <?php echo esc_attr( $classes ); ?>" role="region" aria-label="<?php _e( 'Site Announcements', 'easy-announcements' ); ?>">
							<?php
							do_action( 'easy_announcements_' . $placement . '_' . $attachment . '_end' );
							$easy_announcements_section_start = str_replace( array("\r", "\n", "\t"), '', ob_get_clean() );
							
							if ( is_array( $easy_announcements ) ) {
								foreach( $easy_announcements as $id => $announcement ) {
									ob_start();
									?>
				if (get_easy_announcements_cookie('dismiss-<?php echo esc_attr( $id ); ?>') != 'true' && !$('.announcement-<?php echo esc_attr( $id ); ?>').length) easy_announcements_<?php echo esc_attr( $placement ); ?> += '<?php echo str_replace( array("\r", "\n", "\t"), '', wp_kses_post( addslashes( $announcement ) ) ); ?>';<?php
									$easy_announcements_inject .= ob_get_clean();
								}
							}

							ob_start();
							do_action( 'easy_announcements_' . $placement . '_' . $attachment . '_end' );
							?>
						</section>
						<?php
						$easy_announcements_section_end = str_replace( array("\r", "\n", "\t"), '', ob_get_clean() );

						if ( $placement == 'popup' ) {
							$selector = 'body';
							$attachment = 'append';
						} else {
							if ( $placement == 'header' && $attachment == 'before' ) {
								$selector = 'body';
								$attachment = 'prepend';
							} else if ( $placement == 'footer' && $attachment == 'after' ) {
								$selector = 'body';
								$attachment = 'append';
							}
						}

						ob_start();
						?>
			$('<?php echo esc_attr( $selector ); ?>').<?php echo esc_attr( $attachment ); ?>('<?php echo wp_kses_post( addslashes( $easy_announcements_section_start ) ); ?>' + easy_announcements_<?php echo esc_attr( $placement ); ?> + '<?php echo wp_kses_post( addslashes( $easy_announcements_section_end ) ); ?>');<?php
						$easy_announcements_inject .= ob_get_clean();
					}
				}
			}
		}

		if ( !empty( $easy_announcements_inject ) ) {
			ob_start();
?><script type="text/javascript" id="easy-announcements-inject">
	var announcement_ids = [<?php echo wp_kses_post( implode( ',', $easy_announcements_ids ) ); ?>];

	function easy_announcements_defer(method) {
		if (window.jQuery && window.Cookies) {
			method();
		} else {
			setTimeout(function() { easy_announcements_defer(method) }, 50);
		}
	}
	easy_announcements_defer(function() {
		jQuery(function($) {
			if (typeof Cookies.get('easy_announcements') == 'undefined') {
				set_easy_announcements_cookie();
			}
			<?php echo str_replace( '&amp;', '&', wp_kses_post( $easy_announcements_inject ) ); ?>

			$('.modal.ea-modal').each(function(){
				var modal_id = $(this).attr('id');
					modal = new bootstrap.Modal('#' + modal_id);
				modal.show();
				$(document).on('hidden.bs.modal', '#' + modal_id, function(){
					modal_id = modal_id.replace('modal', '');
					update_easy_announcements_cookie('dismiss-' + modal_id, 'true', null);
				});
			});
		});
	});
</script><?php
			echo ob_get_clean();
		}

	endif;
}
add_action( 'wp_footer', 'easy_announcements_main' );
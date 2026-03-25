<?php
$post_id = get_the_ID();

$announcement_dismissable = get_post_meta( $post_id, 'announcement_dismissable', true ) === '1';
$announcement_size        = get_post_meta( $post_id, 'announcement_size', true )        ?: 'default';
$announcement_show_title  = get_post_meta( $post_id, 'announcement_show_title', true )  === '1';
$announcement_url         = get_post_meta( $post_id, 'announcement_url', true )         ?: '';
$text_align               = get_post_meta( $post_id, 'announcement_text_alignment', true );
$announcement_color       = get_post_meta( $post_id, 'announcement_color', true )       ?: 'primary';
$popup_delay_ms           = (int) get_post_meta( $post_id, 'announcement_popup_delay', true ) * 1000;

$announcement_color_background = easy_announcements_color( 'background', $announcement_color );
$announcement_color_content    = easy_announcements_color( 'content', $announcement_color );

if ( $announcement_color === 'custom' ) {
	$custom_bg = get_post_meta( $post_id, 'announcement_custom_color_background', true );
	if ( ! empty( $custom_bg ) ) {
		$announcement_color_background = $custom_bg;
		$custom_fg                     = get_post_meta( $post_id, 'announcement_custom_color_content', true );
		$announcement_color_content    = ! empty( $custom_fg )
			? $custom_fg
			: easy_announcements_contrast( $announcement_color_background );
	}
}

$announcement_classes  = 'ea-modal announcement announcement-' . $announcement_size . ' announcement-' . $post_id;
if ( ! empty( $text_align ) ) $announcement_classes .= ' text-' . $text_align;
if ( ! empty( $announcement_url ) ) $announcement_classes .= ' has-url';

$announcement_classes = apply_filters( 'ea_announcement_classes', $announcement_classes, get_post( $post_id ) );
$modal_id             = 'modal' . $post_id;
$label_id             = 'announcement-popup-' . $post_id . '-label';
?>
<div
	class="<?php echo esc_attr( $announcement_classes ); ?>"
	data-announcement-id="<?php echo esc_attr( $post_id ); ?>"
	data-announcement-size="<?php echo esc_attr( $announcement_size ); ?>"
	data-popup-delay="<?php echo esc_attr( $popup_delay_ms ); ?>"
	<?php if ( ! empty( $announcement_url ) ) echo 'data-announcement-has-url="true"'; ?>
	id="<?php echo esc_attr( $modal_id ); ?>"
	tabindex="-1"
	aria-labelledby="<?php echo esc_attr( $label_id ); ?>"
	aria-hidden="true"
	role="dialog">

	<div class="ea-modal-dialog">
		<div class="ea-modal-content">

			<?php if ( $announcement_show_title ) : ?>
				<div class="ea-modal-header" style="<?php
					if ( ! empty( $announcement_color_background ) ) echo 'background-color:' . esc_attr( $announcement_color_background ) . ';';
					if ( ! empty( $announcement_color_content ) )    echo 'color:' . esc_attr( $announcement_color_content ) . ';';
				?>">
					<h6 class="ea-modal-title" id="<?php echo esc_attr( $label_id ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></h6>
					<?php edit_post_link(
						'<span aria-hidden="true">&#9998;</span><span class="ea-sr-only">' . esc_html__( 'Edit announcement', 'easy-announcements' ) . '</span>',
						'', '', '', 'ea-edit-link'
					); ?>
					<?php if ( $announcement_dismissable ) : ?>
						<button type="button" class="ea-btn-close" data-ea-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'easy-announcements' ); ?>">
							<span aria-hidden="true">&times;</span>
						</button>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="ea-modal-header ea-modal-header-colorbar" style="<?php
					if ( ! empty( $announcement_color_background ) ) echo 'background-color:' . esc_attr( $announcement_color_background ) . ';';
				?>">
					<?php edit_post_link(
						'<span aria-hidden="true">&#9998;</span><span class="ea-sr-only">' . esc_html__( 'Edit announcement', 'easy-announcements' ) . '</span>',
						'', '', '', 'ea-edit-link'
					); ?>
					<?php if ( $announcement_dismissable ) : ?>
						<button type="button" class="ea-btn-close" data-ea-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'easy-announcements' ); ?>">
							<span aria-hidden="true">&times;</span>
						</button>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="ea-modal-body">
				<?php echo wp_kses_post( get_the_content() ); ?>
			</div>

		</div>
	</div>
</div>

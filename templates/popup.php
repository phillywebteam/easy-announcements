<?php
$announcement_dismissable = ( get_field( 'announcement_dismissable', $announcement ) == true ) ? true : false;

$announcement_size = get_field( 'announcement_size', $announcement ) ?? 'default';
$announcement_show_title = ( get_field( 'announcement_show_title', $announcement ) == true ) ? true : false;
$announcement_url = get_field( 'announcement_url', $announcement ) ?? '';

$announcement_classes = 'modal announcement announcement-' . $announcement_size . ' announcement-' . get_the_ID( $announcement );
$announcement_classes .= !empty( get_field( 'announcement_text_alignment', $announcement ) ) ? ' text-' . get_field( 'announcement_text_alignment', $announcement ) : '';
if ( !empty( $announcement_url ) ) { $announcement_classes .=' has-url'; }

$announcement_classes = apply_filters( 'ea_announcement_classes', $announcement_classes, $post );
?>
<div 
	class="<?php echo esc_attr( $announcement_classes ); ?>" 
	data-announcement-id="<?php get_the_ID( $announcement ); ?>" 
	data-announcement-size="<?php echo esc_attr( $announcement_size ); ?>" 
	<?php if ( !empty( $announcement_url ) ) { echo ' data-announcement-has-url="true"'; } ?> 
	id="modal<?php get_the_ID( $announcement ); ?>" 
	tabindex="-1" 
	aria-labelledby="announcement-popup-<?php get_the_ID( $announcement ); ?>-label" 
	aria-hidden="true" 
	role="alert">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<?php if ( $announcement_show_title == true ) { ?>
				<div class="modal-header">
					<h6 class="modal-title" id="announcement-popup-<?php get_the_ID( $announcement ); ?>-label"><?php echo get_the_title( $announcement ); ?></h6>
					<?php edit_post_link( '<i class="ea-fas ea-fa-pen" title="' . __( 'Edit', 'easy-announcements' ) . '"></i>', '', '', '', 'edit ms-2 small link-secondary' ); ?>
					<?php if ( $announcement_dismissable == true ) { ?>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="modal-body">
				<?php echo get_the_content( $announcement ); ?>
			</div>
			<?php if ( $announcement_show_title != true ) { ?>
				<div class="actions">
					<?php if ( $announcement_dismissable == true ) { ?>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
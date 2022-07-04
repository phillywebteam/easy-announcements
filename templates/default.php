<?php
$announcement_dismissable = ( get_field( 'announcement_dismissable', $announcement ) == true ) ? true : false;

$announcement_color = get_field( 'announcement_color', $announcement ) ?? 'primary';
$announcement_size = get_field( 'announcement_size', $announcement ) ?? 'default';
$announcement_show_title = ( get_field( 'announcement_show_title', $announcement ) == true ) ? true : false;
$announcement_url = get_field( 'announcement_url', $announcement ) ?? '';

$announcement_color_background = easy_announcements_color( 'background', $announcement_color );
$announcement_color_content = easy_announcements_color( 'content', $announcement_color );

if ( $announcement_color == 'custom' && !empty( get_field( 'announcement_custom_color_background', $announcement ) ) ) {
	$announcement_color_background = get_field( 'announcement_custom_color_background', $announcement );
	if ( !empty( get_field( 'announcement_custom_color_content', $announcement ) ) ) {
		$announcement_color_content = get_field( 'announcement_custom_color_content', $announcement );
	} else {
		$announcement_color_content = easy_announcements_contrast( str_replace( '#', '', $announcement_color_background ) );
	}
}

$announcement_classes = 'announcement announcement-' . get_the_ID( $announcement );
$announcement_classes .= !empty( get_field( 'announcement_text_alignment', $announcement ) ) ? ' text-' . get_field( 'announcement_text_alignment', $announcement ) : '';
$announcement_classes .= ( !empty( get_field( 'announcement_sticky', $announcement ) ) && get_field( 'announcement_sticky', $announcement ) == true ) ? ' announcement-sticky' : '';
$announcement_classes .= get_field( 'announcement_size' ) == 'none' ? ' p-0' : ' announcement-' . $announcement_size;
if ( !empty( $announcement_url ) ) { $announcement_classes .=' has-url'; }

$announcement_classes = apply_filters( 'ea_announcement_classes', $announcement_classes, $post );
?>
<div 
	class="<?php echo esc_attr( $announcement_classes ); ?>" 
	data-announcement-id="<?php echo get_the_ID( $announcement ); ?>" 
	data-announcement-color="<?php echo esc_attr( $announcement_color ); ?>" 
	data-announcement-size="<?php echo esc_attr( $announcement_size ); ?>" 
	style="<?php
	if ( !empty( $announcement_color_background ) ) { echo 'background-color: ' . esc_attr( $announcement_color_background ) . ' !important; '; }
	if ( !empty( $announcement_color_content ) ) { echo 'color: ' . esc_attr( $announcement_color_content ) . ' !important; '; }
	?>"
	role="alert">
	<?php if ( $announcement_show_title == true ) { ?>
		<h6 class="m-0 mb-0 p-0"><?php echo get_the_title( $announcement ); ?></h6>
	<?php } ?>

	<div class="contents">
		<?php echo get_the_content( $announcement ); ?>
	</div>

	<div class="actions d-flex align-items-stretch">
		<?php do_action( 'easy_announcements_actions_start' ); ?>
		<?php edit_post_link( '<i class="ea-fas ea-fa-pen" title="' . __( 'Edit', 'easy-announcements' ) . '"></i>', '', '', '', 'edit' ); ?>
		<?php if ( $announcement_dismissable == true ) { ?>
			<a href="#dismiss-<?php echo get_the_ID( $announcement ); ?>" data-dismiss-id="<?php get_the_ID( $announcement ); ?>" class="dismiss" title="<?php _e( 'Dismiss', 'easy-announcements' ); ?>"><i class="ea-fas ea-fa-times"></i><span class="sr-only"><?php _e( 'Dismiss Alert', 'easy-announcements' ); ?></span></a>
		<?php } ?>
		<?php do_action( 'easy_announcements_actions_end' ); ?>
	</div>

	<?php if ( !empty( $announcement_url ) ) { ?>
		<a href="<?php echo esc_attr( $announcement_url ); ?>" class="announcement-url"><span class="sr-only"><?php echo get_the_title( $announcement ); ?></span></a>
	<?php } ?>
</div>
<?php
$post_id = get_the_ID();

$announcement_dismissable = get_post_meta( $post_id, 'announcement_dismissable', true ) === '1';
$announcement_color       = get_post_meta( $post_id, 'announcement_color', true )       ?: 'primary';
$announcement_size        = get_post_meta( $post_id, 'announcement_size', true )        ?: 'default';
$announcement_show_title  = get_post_meta( $post_id, 'announcement_show_title', true )  === '1';
$announcement_url         = get_post_meta( $post_id, 'announcement_url', true )         ?: '';

$announcement_color_background = Easy_Announcements_Utils::get_color( 'background', $announcement_color );
$announcement_color_content    = Easy_Announcements_Utils::get_color( 'content', $announcement_color );

if ( $announcement_color === 'custom' ) {
	$custom_bg = get_post_meta( $post_id, 'announcement_custom_color_background', true );
	if ( ! empty( $custom_bg ) ) {
		$announcement_color_background = $custom_bg;
		$custom_fg = get_post_meta( $post_id, 'announcement_custom_color_content', true );
		$announcement_color_content    = ! empty( $custom_fg )
			? $custom_fg
			: Easy_Announcements_Utils::get_contrast_color( $announcement_color_background );
	}
}

$announcement_classes  = 'announcement announcement-' . $post_id;
$text_align            = get_post_meta( $post_id, 'announcement_text_alignment', true );
if ( ! empty( $text_align ) ) $announcement_classes .= ' text-' . $text_align;
if ( get_post_meta( $post_id, 'announcement_sticky', true ) === '1' ) $announcement_classes .= ' announcement-sticky';
$announcement_classes .= $announcement_size === 'none' ? ' p-0' : ' announcement-' . $announcement_size;
if ( ! empty( $announcement_url ) ) $announcement_classes .= ' has-url';

$announcement_classes = apply_filters( 'ea_announcement_classes', $announcement_classes, get_post( $post_id ) );
?>
<div
	class="<?php echo esc_attr( $announcement_classes ); ?>"
	data-announcement-id="<?php echo esc_attr( $post_id ); ?>"
	data-announcement-color="<?php echo esc_attr( $announcement_color ); ?>"
	data-announcement-size="<?php echo esc_attr( $announcement_size ); ?>"
	style="<?php
		if ( ! empty( $announcement_color_background ) ) echo 'background-color:' . esc_attr( $announcement_color_background ) . ' !important;';
		if ( ! empty( $announcement_color_content ) )    echo 'color:' . esc_attr( $announcement_color_content ) . ' !important;';
	?>"
	role="alert">

	<?php if ( $announcement_show_title ) : ?>
		<strong class="ea-announcement-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></strong>
	<?php endif; ?>

	<div class="contents">
		<?php echo wp_kses_post( get_the_content() ); ?>
	</div>

	<div class="actions">
		<?php do_action( 'easy_announcements_actions_start' ); ?>
		<?php edit_post_link(
			'<span aria-hidden="true">&#9998;</span><span class="ea-sr-only">' . esc_html__( 'Edit announcement', 'easy-announcements' ) . '</span>',
			'', '', '', 'ea-edit-link'
		); ?>
		<?php if ( $announcement_dismissable ) : ?>
			<a href="#dismiss-<?php echo esc_attr( $post_id ); ?>" data-dismiss-id="<?php echo esc_attr( $post_id ); ?>" class="dismiss" title="<?php esc_attr_e( 'Dismiss', 'easy-announcements' ); ?>">
				<span aria-hidden="true">&times;</span>
				<span class="ea-sr-only"><?php esc_html_e( 'Dismiss Alert', 'easy-announcements' ); ?></span>
			</a>
		<?php endif; ?>
		<?php do_action( 'easy_announcements_actions_end' ); ?>
	</div>

	<?php if ( ! empty( $announcement_url ) ) : ?>
		<a href="<?php echo esc_url( $announcement_url ); ?>" class="announcement-url">
			<span class="ea-sr-only"><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
		</a>
	<?php endif; ?>
</div>

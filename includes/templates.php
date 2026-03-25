<?php
// Simple placeholder for announcements - actual fetching & rendering done client-side via REST API
function easy_announcements_main() {
	if ( ! easy_announcements_has_active() ) {
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
add_action( 'wp_footer', 'easy_announcements_main' );

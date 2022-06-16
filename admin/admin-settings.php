<?php
class easy_announcements_settings {
	private $options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function add_plugin_page() {
		add_submenu_page(
			'edit.php?post_type=announcement',
			'Easy Announcements Settings', 
			'Settings', 
			'manage_options', 
			'easy-announcements-admin', 
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'easy_announcements' );
		?>
		<div class="wrap">
			<h1><?php _e( 'Easy Announcements Settings', 'easy-announcements' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'selectors' );
				do_settings_sections( 'easy-announcements-selectors' );
				do_settings_sections( 'easy-announcements-colors' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	public function page_init() {        
		register_setting(
			'selectors',
			'easy_announcements',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'selectors',
			__( 'Selectors','easy-announcements' ),
			array( $this, 'print_selectors_info' ),
			'easy-announcements-selectors'
		);

		add_settings_field(
			'header_selector',
			__( 'Header', 'easy-announcements' ),
			array( $this, 'header_selector_callback' ),
			'easy-announcements-selectors',
			'selectors',
			array( 
				'label_for' =>'header_selector',
				'class' => 'selectors-input easy-announcements-header-selector',
			)
		);

		add_settings_field(
			'content_selector',
			__( 'Content', 'easy-announcements' ),
			array( $this, 'content_selector_callback' ),
			'easy-announcements-selectors',
			'selectors',
			array( 
				'label_for' => 'content_selector',
				'class' => 'selectors-input easy-announcements-content-selector',
			)
		);

		add_settings_field(
			'footer_selector',
			__( 'Footer', 'easy-announcements' ),
			array( $this, 'footer_selector_callback' ),
			'easy-announcements-selectors',
			'selectors',
			array( 
				'label_for' =>'footer_selector',
				'class' => 'selectors-input easy-announcements-footer-selector',
			)
		);

		add_settings_section(
			'colors',
			__( 'Colors','easy-announcements' ),
			array( $this, 'print_colors_info' ),
			'easy-announcements-colors'
		);

		add_settings_field(
			'background_color',
			__( 'Background', 'easy-announcements' ),
			array( $this, 'background_color_callback' ),
			'easy-announcements-colors',
			'colors'
		);

		add_settings_field(
			'content_color',
			__( 'Text & Links', 'easy-announcements' ),
			array( $this, 'content_color_callback' ),
			'easy-announcements-colors',
			'colors'
		);
	}

	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['header_selector'] ) )
			$new_input['header_selector'] = sanitize_text_field( $input['header_selector'] );

		if ( isset( $input['content_selector'] ) )
			$new_input['content_selector'] = sanitize_text_field( $input['content_selector'] );

		if ( isset( $input['footer_selector'] ) )
			$new_input['footer_selector'] = sanitize_text_field( $input['footer_selector'] );

		if ( isset( $input['background_color_primary'] ) )
			$new_input['background_color_primary'] = sanitize_text_field( $input['background_color_primary'] );

		if ( isset( $input['background_color_secondary'] ) )
			$new_input['background_color_secondary'] = sanitize_text_field( $input['background_color_secondary'] );

		if ( isset( $input['background_color_success'] ) )
			$new_input['background_color_success'] = sanitize_text_field( $input['background_color_success'] );

		if ( isset( $input['background_color_danger'] ) )
			$new_input['background_color_danger'] = sanitize_text_field( $input['background_color_danger'] );

		if ( isset( $input['background_color_warning'] ) )
			$new_input['background_color_warning'] = sanitize_text_field( $input['background_color_warning'] );

		if ( isset( $input['background_color_info'] ) )
			$new_input['background_color_info'] = sanitize_text_field( $input['background_color_info'] );

		if ( isset( $input['content_color_primary'] ) )
			$new_input['content_color_primary'] = sanitize_text_field( $input['content_color_primary'] );

		if ( isset( $input['content_color_secondary'] ) )
			$new_input['content_color_secondary'] = sanitize_text_field( $input['content_color_secondary'] );

		if ( isset( $input['content_color_success'] ) )
			$new_input['content_color_success'] = sanitize_text_field( $input['content_color_success'] );

		if ( isset( $input['content_color_danger'] ) )
			$new_input['content_color_danger'] = sanitize_text_field( $input['content_color_danger'] );

		if ( isset( $input['content_color_warning'] ) )
			$new_input['content_color_warning'] = sanitize_text_field( $input['content_color_warning'] );

		if ( isset( $input['content_color_info'] ) )
			$new_input['content_color_info'] = sanitize_text_field( $input['content_color_info'] );

		return $new_input;
	}

	public function print_selectors_info() {
		_e( 'Enter the <a href="https://www.w3schools.com/cssref/css_selectors.asp" target="_blank">CSS selector</a> you want to each announcement type to show up at.<br>Alternatively, click the Live Select button to bring up your site and click where on the page you want the announcements.', 'easy-announcements' );
	}

	public function header_selector_callback() {
		printf(
			'<input type="text" id="header_selector" class="regular-text code" name="easy_announcements[header_selector]" value="%s" required />',
			isset( $this->options['header_selector'] ) ? esc_attr( $this->options['header_selector']) : ''
		);
		echo '<input type="button" class="button button-secondary live-select-toggle" value="Live Select">';
	}

	public function content_selector_callback() {
		printf(
			'<input type="text" id="content_selector" class="regular-text code" name="easy_announcements[content_selector]" value="%s" required />',
			isset( $this->options['content_selector'] ) ? esc_attr( $this->options['content_selector']) : ''
		);
		echo '<input type="button" class="button button-secondary live-select-toggle" value="Live Select">';
	}

	public function footer_selector_callback() {
		printf(
			'<input type="text" id="footer_selector" class="regular-text code" name="easy_announcements[footer_selector]" value="%s" required />',
			isset( $this->options['footer_selector'] ) ? esc_attr( $this->options['footer_selector']) : ''
		);
		echo '<input type="button" class="button button-secondary live-select-toggle" value="Live Select">';
	}

	public function print_colors_info() {
		_e( 'Clearing of set colors will revert them back to their default color.', 'easy-announcements' );
	}

	public function background_color_callback() {
		?>
		<div class="sub-fields horizontal">
			<div class="sub-field">
				<label for="background_color_primary"><strong><?php _e( 'Primary', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="background_color_primary" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[background_color_primary]" value="%s" />',
					easy_announcements_default_color( 'background', 'primary' ),
					isset( $this->options['background_color_primary'] ) ? esc_attr( $this->options['background_color_primary']) : easy_announcements_default_color( 'background', 'primary' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="background_color_secondary"><strong><?php _e( 'Secondary', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="background_color_secondary" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[background_color_secondary]" value="%s" />',
					easy_announcements_default_color( 'background', 'secondary' ),
					isset( $this->options['background_color_secondary'] ) ? esc_attr( $this->options['background_color_secondary']) : easy_announcements_default_color( 'background', 'secondary' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="background_color_success"><strong><?php _e( 'Success', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="background_color_success" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[background_color_success]" value="%s" />',
					easy_announcements_default_color( 'background', 'success' ),
					isset( $this->options['background_color_success'] ) ? esc_attr( $this->options['background_color_success']) : easy_announcements_default_color( 'background', 'success' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="background_color_danger"><strong><?php _e( 'Danger', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="background_color_danger" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[background_color_danger]" value="%s" />',
					easy_announcements_default_color( 'background', 'danger' ),
					isset( $this->options['background_color_danger'] ) ? esc_attr( $this->options['background_color_danger']) : easy_announcements_default_color( 'background', 'danger' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="background_color_warning"><strong><?php _e( 'Warning', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="background_color_warning" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[background_color_warning]" value="%s" />',
					easy_announcements_default_color( 'background', 'warning' ),
					isset( $this->options['background_color_warning'] ) ? esc_attr( $this->options['background_color_warning']) : easy_announcements_default_color( 'background', 'warning' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="background_color_info"><strong><?php _e( 'Info', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="background_color_info" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[background_color_info]" value="%s" />',
					easy_announcements_default_color( 'background', 'info' ),
					isset( $this->options['background_color_info'] ) ? esc_attr( $this->options['background_color_info']) : easy_announcements_default_color( 'background', 'info' )
				); ?>
			</div>
		</div>
		<?php
	}

	public function content_color_callback() {
		?>
		<div class="sub-fields horizontal">
			<div class="sub-field">
				<label for="content_color_primary"><strong><?php _e( 'Primary', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="content_color_primary" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[content_color_primary]" value="%s" />',
					easy_announcements_default_color( 'content', 'primary' ),
					isset( $this->options['content_color_primary'] ) ? esc_attr( $this->options['content_color_primary']) : easy_announcements_default_color( 'content', 'primary' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="content_color_secondary"><strong><?php _e( 'Secondary', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="content_color_secondary" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[content_color_secondary]" value="%s" />',
					easy_announcements_default_color( 'content', 'secondary' ),
					isset( $this->options['content_color_secondary'] ) ? esc_attr( $this->options['content_color_secondary']) : easy_announcements_default_color( 'content', 'secondary' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="content_color_success"><strong><?php _e( 'Success', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="content_color_success" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[content_color_success]" value="%s" />',
					easy_announcements_default_color( 'content', 'success' ),
					isset( $this->options['content_color_success'] ) ? esc_attr( $this->options['content_color_success']) : easy_announcements_default_color( 'content', 'success' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="content_color_danger"><strong><?php _e( 'Danger', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="content_color_danger" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[content_color_danger]" value="%s" />',
					easy_announcements_default_color( 'content', 'danger' ),
					isset( $this->options['content_color_danger'] ) ? esc_attr( $this->options['content_color_danger']) : easy_announcements_default_color( 'content', 'danger' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="content_color_warning"><strong><?php _e( 'Warning', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="content_color_warning" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[content_color_warning]" value="%s" />',
					easy_announcements_default_color( 'content', 'warning' ),
					isset( $this->options['content_color_warning'] ) ? esc_attr( $this->options['content_color_warning']) : easy_announcements_default_color( 'content', 'warning' )
				); ?>
			</div>
			<div class="sub-field">
				<label for="content_color_info"><strong><?php _e( 'Info', 'easy-announcements' ); ?></strong></label>
				<?php printf(
					'<input type="text" id="content_color_info" class="easy-announcements-color-picker" data-default-color="%s" name="easy_announcements[content_color_info]" value="%s" />',
					easy_announcements_default_color( 'content', 'info' ),
					isset( $this->options['content_color_info'] ) ? esc_attr( $this->options['content_color_info']) : easy_announcements_default_color( 'content', 'info' )
				); ?>
			</div>
		</div>
		<?php
	}
}

if ( is_admin() ) $easy_announcements_settings = new easy_announcements_settings();
<?php
if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_5f1f08a146d2b',
		'title' => 'Announcement Settings',
		'fields' => array(
			array(
				'key' => 'field_6237765762ed2',
				'label' => 'Placement',
				'name' => 'announcement_placement',
				'type' => 'select',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'header' => 'Header',
					'footer' => 'Footer',
					'content' => 'Content',
					'popup' => 'Popup',
				),
				'default_value' => false,
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_62a4c87e54d16',
				'label' => 'Attachment',
				'name' => 'announcement_attachment',
				'type' => 'select',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_6237765762ed2',
							'operator' => '!=',
							'value' => 'popup',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'before' => 'Before',
					'prepend' => 'Prepend',
					'append' => 'Append',
					'after' => 'After',
				),
				'default_value' => false,
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_5f1f09cb08c44',
				'label' => 'Pages',
				'name' => 'announcement_pages',
				'type' => 'group',
				'instructions' => 'If left blank will show on all pages. Can only use one or the other.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'table',
				'sub_fields' => array(
					array(
						'key' => 'field_5f1f1edc7ae00',
						'label' => 'Pages Included',
						'name' => 'include',
						'type' => 'post_object',
						'instructions' => 'Show on only these pages.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'page',
							1 => 'wpsl_stores',
							2 => 'alert',
						),
						'taxonomy' => '',
						'allow_null' => 0,
						'multiple' => 1,
						'return_format' => 'id',
						'ui' => 1,
					),
					array(
						'key' => 'field_5f1f1ef47ae01',
						'label' => 'Pages Excluded',
						'name' => 'exclude',
						'type' => 'post_object',
						'instructions' => 'Show on all pages but these.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'page',
							1 => 'wpsl_stores',
							2 => 'alert',
						),
						'taxonomy' => '',
						'allow_null' => 0,
						'multiple' => 1,
						'return_format' => 'id',
						'ui' => 1,
					),
				),
			),
			array(
				'key' => 'field_62a88d497f8fe',
				'label' => 'Expiration Date & Time',
				'name' => 'announcement_expiration',
				'type' => 'date_time_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'm/d/Y g:i a',
				'return_format' => 'Y-m-d H:i:s',
				'first_day' => 1,
			),
			array(
				'key' => 'field_5f1f0a0dc9214',
				'label' => 'Color',
				'name' => 'announcement_color',
				'type' => 'button_group',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'primary' => '<span style="color:' . easy_announcements_color( 'background', 'primary' ) . ';">&#9608;</span>',
					'secondary' => '<span style="color:' . easy_announcements_color( 'background', 'secondary' ) . ';">&#9608;</span>',
					'success' => '<span style="color:' . easy_announcements_color( 'background', 'success' ) . ';">&#9608;</span>',
					'danger' => '<span style="color:' . easy_announcements_color( 'background', 'danger' ) . ';">&#9608;</span>',
					'warning' => '<span style="color:' . easy_announcements_color( 'background', 'warning' ) . ';">&#9608;</span>',
					'info' => '<span style="color:' . easy_announcements_color( 'background', 'info' ) . ';">&#9608;</span>',
					'custom' => 'Custom',
				),
				'allow_null' => 0,
				'default_value' => '',
				'layout' => 'horizontal',
				'return_format' => 'value',
			),
			array(
				'key' => 'field_62a4f2cd51639',
				'label' => 'Custom Color',
				'name' => 'announcement_custom_color',
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5f1f0a0dc9214',
							'operator' => '==',
							'value' => 'custom',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'table',
				'sub_fields' => array(
					array(
						'key' => 'field_62a4f3115163a',
						'label' => 'Background',
						'name' => 'background',
						'type' => 'color_picker',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'enable_opacity' => 0,
						'return_format' => 'string',
					),
					array(
						'key' => 'field_62a4f32f5163b',
						'label' => 'Text & Links',
						'name' => 'content',
						'type' => 'color_picker',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'enable_opacity' => 0,
						'return_format' => 'string',
					),
				),
			),
			array(
				'key' => 'field_62a51862250c4',
				'label' => 'Size',
				'name' => 'announcement_size',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'default' => 'Default',
					'compact' => 'Compact',
					'tall' => 'Tall',
					'none' => 'No Padding',
				),
				'default_value' => 'default',
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_62a60603b9315',
				'label' => 'Text',
				'name' => 'announcement_text',
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'table',
				'sub_fields' => array(
					array(
						'key' => 'field_62a6061eb9316',
						'label' => 'Alignment',
						'name' => 'alignment',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'start' => 'Left',
							'center' => 'Center',
							'end' => 'Right',
						),
						'default_value' => false,
						'allow_null' => 1,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_62a60bf8d1f7d',
						'label' => 'Size',
						'name' => 'size',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'default' => 'Default',
							'small' => 'Small',
							'large' => 'Large',
						),
						'default_value' => 'default',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 0,
						'return_format' => 'value',
						'ajax' => 0,
						'placeholder' => '',
					),
				),
			),
			array(
				'key' => 'field_5f1f423ebec07',
				'label' => 'Destination URL',
				'name' => 'announcement_url',
				'type' => 'text',
				'instructions' => 'Makes whole announcement clickable.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5f1f28a3b90b7',
				'label' => 'Show Title',
				'name' => 'announcement_show_title',
				'type' => 'true_false',
				'instructions' => 'Show the announcement title above the copy.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_5f7dcb8bb0599',
				'label' => 'Is Dismissable',
				'name' => 'announcement_dismissable',
				'type' => 'true_false',
				'instructions' => 'Allow users to dismiss this notice and not return.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'announcement',
				),
			),
		),
		'menu_order' => 4,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'label',
		'hide_on_screen' => array(
			0 => 'permalink',
			1 => 'discussion',
			2 => 'comments',
			3 => 'slug',
		),
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	));
	
endif;
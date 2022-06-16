<?php

function easy_announcement_posttype() {
	register_post_type( 'announcement', array(
		'labels' => array(
			'name' => __( 'Announcements', 'easy-announcements' ),
			'singular_name' => __( 'Announcement', 'easy-announcements' ),
			'add_new_item' => __( 'Add New Announcement', 'easy-announcements' ),
			'edit_item' => __( 'Edit Announcement', 'easy-announcements' ),
			'new_item' => __( 'New Announcement', 'easy-announcements' ),
			'view_item' => __( 'View Announcement', 'easy-announcements' ),
			'not_found' => __( 'No announcement found', 'easy-announcements' ),
			'all_items' => __( 'All announcements', 'easy-announcements' ),
			'insert_into_item' => __( 'Insert into announcement', 'easy-announcements' ),
			'featured_image' => __( 'Announcement Image', 'easy-announcements' ),
			'set_featured_image' => __( 'Set announcement image', 'easy-announcements' ),
			'use_featured_image' => __( 'Use as announcement image', 'easy-announcements' ),
			'remove_featured_image' => __( 'Remove announcement image', 'easy-announcements' ),
		),
		'menu_icon' => 'dashicons-align-wide',
		'public' => true,
		'has_archive' => false,
		'show_in_rest' => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_in_nav_menus' => false,
		'supports' => array( 'title', 'editor', 'custom-fields', ),
		'map_meta_cap' => true
	) );
}
add_action( 'init', 'easy_announcement_posttype' );
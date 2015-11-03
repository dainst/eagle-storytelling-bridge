<?php
/**
 * @package eagle-storytelling-bridge
 * @version 1.0
 */
/*
Plugin Name: Eagle Storytelling Application Bridge
Plugin URI:  https://github.com/codarchlab/eagle-storytelling-bridge
Description: This is a plugin especially for the Eagle website to fit in the Eagle-Theme and other requirements with the storytelling application
Author:	     Wolfgang Schmidle & Philipp Franck
Author URI:	 http://www.dainst.org/
Version:     1.0

*/

/**
 * Settings
 */
// show debug info
define('ESA_DEBUG', false);

require_once('template/functions.php');

/**
 * User Specific Settings
 */

/*
 register_activation_hook(__FILE__, function() {
 add_role('esa_story_author', 'Story Author', array());
 add_role('esa_story_contributor', 'Story Contributor', array());
 });


 register_deactivation_hook(__FILE__, function() {
 remove_role('esa_story_author');
 remove_role('esa_story_contributor');
 });
 */
add_action('admin_init', function () {

	$roles = array('subscriber', 'editor', 'author', 'administrator');
	foreach ($roles as $role) {
		$role = get_role($role);
		$role->add_cap('read');
		$role->add_cap('create_story');
		$role->add_cap('edit_story');
		$role->add_cap('delete_story');
		$role->add_cap('publish_story');
		$role->add_cap('delete_published_story');
		$role->add_cap('edit_published_story');
		$role->add_cap('manage_story_keyword');
		$role->add_cap('edit_story_keyword');
		$role->add_cap('delete_story_keyword');
		$role->add_cap('assign_story_keyword');
		$role->add_cap('upload_files');
	}

	$roles = array('administrator');
	foreach ($roles as $role) {
		$role = get_role($role);
		$role->add_cap('edit_others_story');
		$role->add_cap('read_private_posts');
	}
	
	global $esa_settings;
	
});



/**
 * custom post type: story && custom taxonomy: story_keyword
 */
add_action('init', function() {
	
	$labels = array(
			'name' => _x('Stories', 'post type general name', 'Flexible'),
			'singular_name' => _x('Story', 'post type singular name', 'Flexible'),
			'add_new' => _x('Add New', 'story item', 'Flexible'),
			'add_new_item' => __('Add New Story', 'Flexible'),
			'edit_item' => __('Edit Story', 'Flexible'),
			'new_item' => __('New Story', 'Flexible'),
			'all_items' => __('All Stories', 'Flexible'),
			'view_item' => __('View Story', 'Flexible'),
			'search_items' => __('Search Stories', 'Flexible'),
			'not_found' => __('Nothing found', 'Flexible'),
			'not_found_in_trash' => __('Nothing found in Trash', 'Flexible'),
			'parent_item_colon' => ''
	);
	
	$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => apply_filters('et_portfolio_posttype_rewrite_args', array('slug' => 'story', 'with_front' => false)),
			'capability_type' => 'story',
			'capabilities' => array(
					'publish_post' => 'publish_story',
					'publish_posts' => 'publish_story',
					'edit_posts' => 'edit_story',
					'edit_post' => 'edit_story',
					'edit_others_posts' => 'edit_others_story',
					'read_private_posts' => 'read_private_story',
					'edit_post' => 'edit_story',
					'delete_post' => 'delete_story',
					'read_post' => 'read_story',
			),
			'exclude_from_search' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'editor', 'excerpt', 'revisions', 'thumbnail')
	);
	
	register_post_type('story', $args);
	
	
	$labels = array(
			'name' => _x('Keywords', 'Keywords', 'Flexible'),
			'singular_name' => _x('Keyword', 'taxonomy singular name', 'Flexible'),
			'search_items' => __('Search Keywords', 'Flexible'),
			'all_items' => __('All Keywords', 'Flexible'),
			'parent_item' => __('Parent Keyword', 'Flexible'),
			'parent_item_colon' => __('Parent Keyword:', 'Flexible'),
			'edit_item' => __('Edit Keyword', 'Flexible'),
			'update_item' => __('Update Keyword', 'Flexible'),
			'add_new_item' => __('Add New Keyword', 'Flexible'),
			'new_item_name' => __('New Keyword Name', 'Flexible'),
			'menu_name' => __('Keyword', 'Flexible')
	);

	register_taxonomy('story_keyword', array('story'), array(
			'hierarchical' => false,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'keyword'),
			'capabilities' => array(
					'manage_terms' => 'manage_story_keyword',
					'edit_terms' => 'edit_story_keyword',
					'delete_terms' => 'delete_story_keyword',
					'assign_terms' => 'assign_story_keyword'
			)
	));
	
	// override es settings
	global $esa_settings;
	$esa_settings = array_merge($esa_settings, array('post_types' => array('story')));
	
	
}, 0);


/**
 * template hacks
 */

/* register template for the story pages */
function esa_get_story_post_type_template($single_template) {
	global $post;

	if (is_esa($post->post_type)) {
		$single_template = dirname( __FILE__ ) . '/template/single-story.php';
	}
	return $single_template;
}

add_filter('single_template', 'esa_get_story_post_type_template');


/* register template for page "stories" */
function esa_get_stories_page_template($page_template) {
	if (is_page('stories')) {
		$page_template = dirname( __FILE__ ) . '/template/page-stories.php';
	}
	return $page_template;
}

add_filter( 'page_template', 'esa_get_stories_page_template' );


/* register template for page "search stories" */
function esa_get_search_stories_page_template($page_template) {
	if (is_esa(get_query_var('post_type')) or (get_query_var('taxonomy') == 'story_keyword')){
		$page_template = dirname( __FILE__ ) . '/template/search-stories.php';
	}
	return $page_template;
}

add_filter('search_template', 'esa_get_search_stories_page_template');
add_filter('archive_template', 'esa_get_search_stories_page_template');
add_filter('404_template', 'esa_get_search_stories_page_template');


/**
 * search filter
 */ 
function searchfilter($query) {
	if ($query->is_search && is_esa($query->post_type)) {
		$query->set('meta_key','_wp_page_template');
		$query->set('meta_value', dirname( __FILE__ ) . '/template/search-stories.php');
	}
	return $query;
}

//add_filter('pre_get_posts','searchfilter');



/**
 * Register style sheets and javascript
 */
add_action('wp_enqueue_scripts', function() {
	global $post;
	
	if (is_esa(get_post_type())) {

		// css
		wp_register_style('eagle-storytelling', plugins_url('eagle-storytelling-bridge/css/eagle-storytelling.css'));
		wp_enqueue_style('eagle-storytelling' );

		//js
	}
});


/**
 * fetaured posts 
*/
add_action('add_meta_boxes', function () {

	add_meta_box(
			'esa_featured',
			'Featured Story',
			function($post) {
				wp_nonce_field('esa_featured_meta_box_nonce', 'esa_featured_meta_box_nonce');
				$value = get_post_meta($post->ID, 'esa_featured', true);
				
				$checked = $value ? 'checked="checked"' : '';
				echo '<input type="checkbox" id="esa_featured" name="esa_featured"' . $checked . '" size="25" />';
				echo '<label for="esa_featured">Set this as a  featured story</label>';
			},
			'story'
	);
	
});

add_action('save_post', function($post_id) {
	
	// Verify that the nonce is valid.
	if (!isset($_POST['esa_featured_meta_box_nonce']) ) {return;}
	if (!wp_verify_nonce($_POST['esa_featured_meta_box_nonce'], 'esa_featured_meta_box_nonce')) {return;}
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {return;}
	
	// Check the user's permissions.
	if (isset($_POST['post_type']) && 'story' == $_POST['post_type']) {
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}
	}
	
	$esa_featured = isset($_POST['esa_featured']) and $_POST['esa_featured'] ? 1 : '';
	
	
	update_post_meta($post_id, 'esa_featured', $esa_featured);
});
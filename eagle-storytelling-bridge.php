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


 register_activation_hook(__FILE__, function() {

	
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

	$roles = array('editor', 'author', 'administrator');
	foreach ($roles as $role) {
		$role = get_role($role);
		$role->add_cap('esa_manage_full_library');
	}
	
	$roles = array('administrator');
	foreach ($roles as $role) {
		$role = get_role($role);
		$role->add_cap('edit_others_story');
		$role->add_cap('read_private_posts');
	}
	


});

 /*
 	add_action('init', function() {
 		$role = get_role('subscriber');
 		echo "<pre>";
 		print_r($role);
 		echo "</pre><hr>";
 		print_r(current_user_can('esa_manage_full_library'));
 		wp_die();
 	});
 	*/
 	
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
			'name' => _x('Keywords', 'Keywords', 'esa'),
			'singular_name' => _x('Keyword', 'Keyword', 'esa'),
			'search_items' => __('Search Keywords', 'esa'),
			'all_items' => __('All Keywords', 'esa'),
			'parent_item' => __('Parent Keyword', 'esa'),
			'parent_item_colon' => __('Parent Keyword:', 'esa'),
			'edit_item' => __('Edit Keyword', 'esa'),
			'update_item' => __('Update Keyword', 'esa'),
			'add_new_item' => __('Add New Keyword', 'esa'),
			'new_item_name' => __('New Keyword Name', 'esa'),
			'menu_name' => __('Keyword', 'esa')
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
	
	
	register_taxonomy('story_lng', 'story', array(
			'hierarchical' => false,
			'label' => 'Language',
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'show_in_quick_edit' => false,
			'meta_box_cb' => 'esa_language_meta_box',
			'query_var' => true,
			'rewrite' => array('slug' => 'lng'),
			'capabilities' => array(
/*					'manage_terms' 	=> 'manage_story_keyword',
					'edit_terms' 	=> 'edit_story_keyword',
					'delete_terms' 	=> 'delete_story_keyword',*/
					'assign_terms' 	=> 'assign_story_lng'
			)
	));
	
	
	
	// override esa settings
	global $esa_settings;
	$esa_settings =  !is_array($esa_settings) ? array() : $esa_settings;
	$esa_settings = array_merge($esa_settings, array(
			'post_types' => array('story'),
			'add_media_entry' => 'Insert Epigraphic Datasource'
		)
	);
	
	
}, 0);


function esa_language_meta_box($post) {
	require_once dirname( __FILE__ ) . '/inc/languages.php';
	echo "<select id='story_lng' size='1' name='esa_story_lng'>";
	$the_lng = wp_get_object_terms($post->ID, 'story_lng');
	
	foreach(esa_language_codes() as $lng) {
		$enclng = urlencode($lng);
		$selected = $the_lng[0]->name == $lng ? "selected='selected'" : ''; 
		echo "<option value='$enclng' $selected>$lng</option>";
	}				
	echo "</select>";
	
	//echo $post->ID, "<textarea>", print_r($the_lng,1),"</textarea>";
}

/**
 * save language
 */
add_action('save_post', function($post_id) {
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	if (!('story' == $_POST['post_type']) and current_user_can('edit_story', $post_id)) { 
		return $post_id;
	} 
	
	//$post = get_post($post_id); // OR $post->post_type != 'revision'
	
	$lng = urldecode($_POST['esa_story_lng']);
	
	if ($lng == 'English') {
		wp_delete_object_term_relationships($post_id, 'story_lng');
	} else {
		wp_set_object_terms($post_id, $lng, 'story_lng');
	}

	return $lng;
});


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

add_filter('pre_get_posts', function($query) {
	if (!$query->is_search or !isset($query->query['post_type']) or !is_esa($query->query['post_type'])) {
		return $query;
	}
	
	//echo "<textarea style='width:100%; height: 250px'> ", print_r($_POST, 1), "</textarea>";
	//echo "<textarea style='width:100%; height: 250px'> ", print_r($query, 1), "</textarea>";
	
	$mode 	= isset($_POST['esa_multifilter_select']) ? $_POST['esa_multifilter_select'] : null;
	$itemid = isset($_POST['esa_multifilter_selected']) ? $_POST['esa_multifilter_selected'] : null;
	$search = isset($_POST['esa_multifilter']) ? $_POST['esa_multifilter'] : null;

	if (!mode or (!$itemid and !$search)) {
		return $query;
	}
	
	if ($mode == 'users') {
		if ($itemid) {
			$query->set('author', $itemid);
		} else {
			$query->set('author_name', $search);
		}
		
	}		
	
	if ($mode == 'keywords') {
		$query->set('tax_query', array(
			$itemid ?
			 array(
				'taxonomy' => 'story_keyword',
				'field'    => 'id',
				'terms'    => array($itemid),
				'operator' => 'IN',
			) :
			array(
				'taxonomy' => 'story_keyword',
				'field'    => 'name',
				'terms'    => array($search)
			)
		));
	}
	
	
	if ($mode == 'language') {
		if (strlen($search) == 2)  {
			require_once dirname( __FILE__ ) . '/inc/languages.php';
			$llist = esa_language_codes();
			$search = $llist[$search];
		}
		
		
		if ($itemid == 'English' OR $search == 'English') {
			$taxq = array(
				array(
					'taxonomy' => 'story_lng',
					'field'    => 'id',
					'operator' => 'NOT EXISTS',
				)
			);
		} else {
		
			$taxq = array(
				$itemid ?
					array(
						'taxonomy' => 'story_lng',
						'field'    => 'id',
						'terms'    => array($itemid),
						'operator' => 'IN',
					) :
					array(
						'taxonomy' => 'story_lng',
						'field'    => 'name',
						'terms'    => array($search)
					)
				);
		}
		$query->set('tax_query', $taxq);
	}
	
});
	 

/**
 * freakin debuggin
 
add_filter( 'posts_request', function ($input) {
	var_dump($input);
	return $input;
}
*/

/**
 * Register style sheets and javascript
 */
add_action('wp_enqueue_scripts', function() {
	global $post;
	
	if (is_esa(get_post_type())) {

		// css
		wp_register_style('eagle-storytelling', plugins_url('eagle-storytelling-bridge/css/eagle-storytelling.css'));;
		wp_enqueue_style('eagle-storytelling');
		
		//js
		wp_register_script('esa-bridge-frontend', plugins_url('eagle-storytelling-bridge/js/eagle-storytelling-bridge.js'));
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget ');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('esa-bridge-frontend');
		//print_r($GLOBALS['wp_scripts']);die();
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

/**
 * save featured
 */
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



// menu
add_action('init', function() {
	register_nav_menu('esa-menu',__( 'Stories Menu' ));
});

add_action('admin_menu', function() {
	if(!current_user_can('esa_manage_full_library')) {
		remove_menu_page('upload.php');
	}
});

//Manage Your Media Only
add_action('pre_get_posts', function($query) {
	
	if (!defined('DOING_AJAX') or !DOING_AJAX or ($query->query['post_type'] != 'attachment')) {
		return; 
	}
	if (current_user_can('esa_manage_full_library')) {
		return;	
	}
	if (!isset($query->query['post_parent'])) {
		if (isset($_POST['post_id']) and $_POST['post_id']) { //  show all, show images etc.
			$query->set('post_parent', $_POST['post_id']); // show only attachments if possible
		} else {
			wp_send_json_success(array()); // show nothing and allow upload
		}
	} else if (isset($query->query['post_parent']) and !$query->query['post_parent']) { // show unattached
		wp_send_json_error('not permitted'); // show nothing and do not allow upload
	}	
});

//  dashboard
add_action('wp_dashboard_setup', function() {
	// Globalize the metaboxes array, this holds all the widgets for wp-admin
	global $wp_meta_boxes;
	
	if(!current_user_can('esa_manage_full_library')) {
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	}

});
// only show own posts
add_filter('pre_get_posts',	function ($query) {

	if(!current_user_can('esa_manage_full_library')) {
		global $user_ID;
		$query->set('author',  $user_ID);
	}

	return $query;
});
// some hacky shit to remove the "show social share buttons" dialogue from the addthin-plugin
add_action('do_meta_boxes', function() {
	remove_meta_box('at_widget', 'story', 'side');
	remove_meta_box('at_widget', 'story', 'default');
	remove_meta_box('at_widget', 'story', 'advanced'); 
});
// better captions in add media dialogue
add_filter('gettext', function($translation, $text) {
	
	$hacky_translations = array(
			'Add Media' 		=> 'Add Media or epigraphic Datasource',
			'Insert Media'		=> 'Upload Media',
			'Insert from URL'	=> 'Insert Image from URL',
			'Link Text'			=> 'Use this to insert images or other media from popular sites like youtube, imgut or flickr. Insert Link Text below.'
	);
	
	
	if(is_esa() and isset($hacky_translations[$text])) {
		return $hacky_translations[$text];
	}
	return $translation;
}, 10, 2);

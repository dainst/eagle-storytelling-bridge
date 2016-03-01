<?php

/**
 *
 * - use wordpress paramter to build your query
 * - search for freetext:  /?s=test&stories_api=1
 * - single text by ID:	 /?p=ID&stories_api=1
 * - search for a keyword: /keyword/hallokeyword/?stories_api=1
 * 
 * - add stories_api=1 parameter to get story as json file
 * - remove_esa=1 parameter to get content withour embedded content
 * - 
 * 
 * 

 */
/**
 * 
 * stand der Dinge:
 * alles geht bis auf some fehler mit den maps und thickbox pops niccht
 * ist das wirklich nötig?
 */

header('Content-Type: application/json');

$remove_esa = ($_GET['remove_esa'] != '') or ($_POST['remove_esa'] != '');

$response = array();

$response['total_pages']	= max(1, absint($wp_query->max_num_pages));
$response['paged']			= max(1, absint($wp_query->get('paged')));
$response['query']			= $wp_query->query;
$response['post_count']		= $wp_query->post_count;
$response['found_posts']	= $wp_query->found_posts;
$wp_query->set('post_type', 'story');

$response['results'] = array();


$langs = array_flip(esa_language_codes());

if (have_posts()){
	while (have_posts()){
		the_post();
		
		// thumbnail
		preg_match('/src="([^"]*)"/i', esa_thumbnail(get_post(), true), $thumb);
		
		
		//  keywords
		$terms = array();
		foreach (wp_get_object_terms($post->ID, 'story_keyword')  as $term) {
			$terms[] = $term->name;
		}
		
		// lng
		$lng = 'en';
		foreach (wp_get_object_terms($post->ID, 'story_lng')  as $term) { // just 1 time
			$lng = $langs[$term->name];
		}
		
		// return
		$response['results'][] = array(
			'ID'		=> $post->ID,
			'title' 	=> $post->post_title,
			'content' 	=> $remove_esa ? strip_shortcodes($post->post_content) : do_shortcode($post->post_content),
			'author' 	=> get_the_author(),
			'keywords'	=> $terms,
			'date'		=> get_the_time(et_get_option('flexible_date_format')),
			'excerpt'	=> get_the_excerpt(),
			'thumbnail'	=> $thumb[1],
			'featured'	=> get_post_meta($post->ID, 'esa_featured', true),
			'url'		=> get_the_permalink(),
			'language'	=> $lng
		);

	}
}

if (($_GET['include_scripts'] != '') or ($_POST['include_scripts'] != '')) {

	$wp_styles = wp_styles();
	//$response['yolo'] = $wp_styles;
	
	$response['style'] = esa_item_special_styles();
	$response['style_links'] = array(
			'thickbox'	=> get_site_url() . $wp_styles->registered['thickbox']->src,
			'esa_item'	=> get_site_url() . '/wp-content/plugins/eagle-storytelling/css/esa_item.css',
			'leaflet'	=> 'http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css'
	);
	
	$response['script'] = $wp_scripts->registered['thickbox']->extra['data'];
	$response['script_links'] = array(
			'jquery-core' => get_site_url() . $wp_scripts->registered['jquery-core']->src,
			'jquery-migrate' => get_site_url() . $wp_scripts->registered['jquery-migrate']->src,
			'thickbox' => get_site_url() . $wp_scripts->registered['thickbox']->src,
			'esa_item' => get_site_url() . '/wp-content/plugins/eagle-storytelling/js/esa_item.js');

}
	
$response['status'] = 'o.k.';


echo json_encode($response);

?>
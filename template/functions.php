<?php
/**
* @package 		eagle-storytelling
* @subpackage	functions fro the eagle-sidebar
* @link 		http://www.eagle-network.eu/
* @author 		Philipp Franck
* 
* 
* Status: Currently theese function s get called manually from template.. 
* will be bundeld together in a widget.
* 
*/

/* esa users */

function esa_dropdown_users($selected) {

	// get users who has at least one story published
	global $wpdb;
	$sql = "
		select
			count(post_author) as post_count,
			display_name,
			users.ID
		from
			{$wpdb->prefix}posts as posts
			left join {$wpdb->prefix}users as users on (posts.post_author=users.ID)
		where
			post_type='story'
			and post_status='publish'
			group by
			posts.post_author
		order by
			post_count desc"; //!

	$users = $wpdb->get_results($sql);

	//print_r($sql);echo"<hr>";print_r($result);

	// some copied wp_dropdown_users

	$output = '';
	if (!empty($users)) {
	$name = 'author';
	$id = 'esa-filter-author';
	$output = "<select name='{$name}' id='{$id}'>\n";
	$output .= "\t<option value='0'>&lt;all&gt;</option>\n";

	$found_selected = false;
	foreach ((array) $users as $user) {
		$user->ID = (int) $user->ID;
			$_selected = selected($user->ID, $selected, false);
			if ($_selected) {
			$found_selected = true;
			}
			$display = "{$user->display_name} ({$user->post_count})";
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html($display) . "</option>\n";
		}
	
		$output .= "</select>";
	}
	
	echo $output;

}

function esa_get_story_keywords() {
	global $post;
	$terms = wp_get_object_terms($post->ID, 'story_keyword');
	$links = array();
	$url = get_site_url();
	foreach ( $terms as $term ) {
		$links[] = "<a href='$url/?s=&post_type=story&term={$term->slug}&taxonomy=story_keyword&author=0'>{$term->name}</a>";
	}
	if (count($links)) {
		return "Keywords: " . wp_sprintf('%l', $links);
	}
}

/** the new filter thing */



function esa_autocomplete_users($q) {
	global $wpdb;
	$sql = "
		select
			users.ID as itemid,
			display_name as value
		from
			{$wpdb->prefix}posts as posts
			left join {$wpdb->prefix}users as users on (posts.post_author=users.ID)
		where
			post_type='story'
			and post_status='publish'
			and display_name like '%$q%'
		group by
			posts.post_author
		order by
			display_name";

	return $wpdb->get_results($sql);
}

function esa_autocomplete_keywords($q) {
	global $wpdb;
	$sql = "
		select 
			term.term_id as itemid,
		    term.name as value
		from 
			{$wpdb->prefix}terms as term
			left join {$wpdb->prefix}term_taxonomy as tax on (tax.term_id = term.term_id)
		where
			tax.taxonomy = 'story_keyword'
		    and (
				term.name like '%$q%'
		        or term.slug like '%$q%'
			)
		order by
			term.name";
	return $wpdb->get_results($sql);
}

function esa_highlight_query($q, $term) {

		$q = preg_quote($q, '/');

		return preg_replace("#$q#i", '<b>$0</b>', $term);
}


add_action('wp_ajax_esa_autocomplete', function() {

	try {
		$set = isset($_POST['set']) ? $_POST['set'] : false;
		$q = isset($_POST['q']) ? $_POST['q'] : false;
		
		if (!$set or !function_exists("esa_autocomplete_$set")) {
			throw new Exception("set unknown: $set");
		}
		
		$result = call_user_func("esa_autocomplete_$set", $q);
		//wp_send_json_success($result);
		foreach ($result as $k => $v) {
			$result[$k]->label = esa_highlight_query($q, $v->value);
		}
		
		echo json_encode($result);
		
		wp_die();
		
	} catch (Exception $e) {
		wp_send_json_error(array(
			'type' => 'esa',
			'message' => 'Autocomplete Error: ' . $e->getMessage(),
			'debug' => print_r($_POST, 1)
		));
	}
});
?>
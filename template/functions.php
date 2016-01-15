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
	//$url = get_site_url();
	foreach ($terms as $term) {
		//$links[] = "<a href='$url/?s=&post_type=story&term={$term->slug}&taxonomy=story_keyword&author=0'>{$term->name}</a>";
		$links[] = esa_link_form($term->name, array(
			'post_type' 	=> 'story',
			'term'			=> $term->slug,
			'taxonomy'		=> 'story_keyword'
		));
	}
	if (count($links)) {
		return "Keywords: " .  implode(", ", $links);
	}

}

/** the new filter thing */

function esa_autocomplete_users_top($q) {

	global $wpdb;
	$sql = "
		select
			users.ID as itemid,
			display_name as value,
			concat(display_name, ' (', count(*), ')') as label
		from
			{$wpdb->prefix}posts as posts
			left join {$wpdb->prefix}users as users on (posts.post_author=users.ID)
		where
			post_type='story'
			and post_status='publish'
		group by
			posts.post_author
		order by
			count(*) desc
		limit
			5";

	return $wpdb->get_results($sql);
}

function esa_autocomplete_users($q) {
	global $wpdb;
	$sql = "
		select
			users.ID as itemid,
			display_name as value,
			concat(display_name, ' (', count(*), ')') as label
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
			display_name
		limit
			10";

	return $wpdb->get_results($sql);
}

function esa_autocomplete_keywords_top($q) {
	global $wpdb;
			$sql = "
				select
					term.term_id as itemid,
					concat(term.name, ' (', tax.count, ')') as label,
					term.name as value
				from
					{$wpdb->prefix}terms as term
					left join {$wpdb->prefix}term_taxonomy as tax on (tax.term_id = term.term_id)
				where
					tax.taxonomy = 'story_keyword'
				order by
					tax.count desc
				limit
					5";
	return $wpdb->get_results($sql);
}

function esa_autocomplete_keywords($q) {
	global $wpdb;
	$sql = "
		select 
			term.term_id as itemid,
			concat(term.name, ' (', tax.count, ')') as label,
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
			term.name
		limit
			10";
	return $wpdb->get_results($sql);
}

function esa_autocomplete_language_top($q) {
	return array(
		(object) array(
			'itemid' => 'English',
			'value'  => 'English',
			'label'  => 'English'
		)
	);

}

function esa_autocomplete_language($q) {
	global $wpdb;
	$sql = "
		select 
			'English' as itemid,
    		'English' as label,
    		'English' as value
		union
		(		
			select
				term.term_id as itemid,
				concat(term.name, ' (', tax.count, ')') as label,
				term.name as value
			from
				{$wpdb->prefix}terms as term
			left join {$wpdb->prefix}term_taxonomy as tax on (tax.term_id = term.term_id)
			where
				tax.taxonomy = 'story_lng'
				and tax.count > 0 
				and (
					term.name like '%$q%'
		        	or term.slug like '%$q%'
				)
			order by
				term.name
			limit
				10
		)";

	return $wpdb->get_results($sql);
}


function esa_highlight_query($q, $term) {
		
		$q = preg_quote($q, '/');

		return preg_replace("#$q#i", '<b>$0</b>', $term);
}


function esa_search_string() {
	$params = count($_POST) ? $_POST : $_GET;
	
	// from multifilter
	$mode 	= isset($params['esa_multifilter_select']) ? $params['esa_multifilter_select'] : null;
	$itemid = isset($params['esa_multifilter_selected']) ? $params['esa_multifilter_selected'] : null;
	$search = isset($params['esa_multifilter']) ? $params['esa_multifilter'] : null;
	
	// from classic single filter / link
	if (isset($params['taxonomy']) and ($params['taxonomy']  == 'story_keyword')) {
		$mode = 'keywords';
		if (isset($params['term'])) {
			$termo = get_term_by('slug', $params['term'], 'story_keyword');
			$search = $termo->name;
		}
	}

	if (isset($params['author']) and isset($params['author'])) {
		$mode = 'users';
		$user = get_user_by('id', $params['author']);
		$search = $user->user_nicename;
		
	}	
		
	
	$labels = array(
		'language'	=> 'IN %',
		'keywords'	=> 'LABELED WITH KEYWORD &raquo;%&laquo;',
		'users'		=> 'BY %'
	);
	
	
	$filter = ($mode and isset($labels[$mode]) and $search) ? str_replace('%', $search, $labels[$mode]) : '';
	
	$searchterm = (isset($params['s']) and $params['s']) ? "STORIES WITH &raquo;{$params['s']}&laquo;" : "STORIES";
	
	return "$searchterm $filter";
}

/**
 * to avoid ugly urls we ewant to build our own pagination; also use forms insetad of links... it's not the best solution,
 * but because FSA is embedded in a larger WP context, we can not use the normal archives and stuff so easy (and not time is left).
 */
function esa_pagenavi() {

	global $wp_query;
	
	$total_pages = max(1, absint($wp_query->max_num_pages));
	$paged = max(1, absint($wp_query->get('paged')));
	
	if ($total_pages < 2) {
		return;
	}
	
	echo "<div class='wp-pagenavi'>";
	echo "<span class='pages'>Page {$paged} of {$total_pages}</span>";
	echo "<form method='post' action='", site_url(), "/' style='display:inline'>\n";
	//echo "\t<input name='paged' value='$i' />\n";
	foreach ($_POST as $k => $v) {
		echo ($k != 'paged') ? "<input type='hidden' name='$k' value='$v' />\n" : '';
	}
	if ($total_pages < 5) {
		$pageray = range(1, $total_pages);
	} else {
		$pageray = range(max($paged - 2, 1), min($paged + 2, $total_pages));
		$caption = range(max($paged - 2, 1), min($paged + 2, $total_pages));
		if (!in_array(1, $pageray)) {
			array_unshift($pageray, 1);
			array_unshift($caption, '&laquo;First');
		}
		if (!in_array($total_pages, $pageray)) {
			array_push($pageray, $total_pages);
			array_push($caption, 'Last&raquo;');
		}
	}

	foreach ($pageray as $index => $pagenr) {
		echo $pagenr == $paged ? "<span class='current'>$pagenr</span>\n" : "<button class='esa_page_link page larger' value='$pagenr' name='paged'>{$caption[$index]}</button>\n";
	}
	echo "</form></div>";
}

/**
 * 
 * @param string $caption
 * @param assoc $query_vars
 */
function esa_link_form($caption, $query_vars) {
	$url = site_url();
	$echo = "<form method='post' action='$url/' class='esa_link_form'>";
	foreach ($query_vars as $k => $v) {
		$echo .= ($k != 'paged') ? "<input type='hidden' name='$k' value='$v' />" : '';
	}
	$echo .= "<input type='submit' value='$caption' />";
	$echo .= "</form>";
	return $echo;
} 


function wp_ajax_esa_autocomplete() {

	try {
		$set = isset($_POST['set']) ? $_POST['set'] : false;
		$q = isset($_POST['q']) ? $_POST['q'] : false;
		
		$func = ($q == '' or $q == '###top###') ? "esa_autocomplete_{$set}_top": "esa_autocomplete_$set";
		
		if (!$set or !function_exists($func)) {
			throw new Exception("set unknown: $set");
		}

		$result = call_user_func($func, $q);
		
		if ($q != '###top###') {
			foreach ($result as $k => $v) {
				$result[$k]->label = esa_highlight_query($q, $v->label);
			}
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
};
add_action('wp_ajax_esa_autocomplete', 'wp_ajax_esa_autocomplete');
add_action('wp_ajax_nopriv_esa_autocomplete', 'wp_ajax_esa_autocomplete');
?>
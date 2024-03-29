<div id="breadcrumbs">
	<?php if(function_exists('bcn_display')) { bcn_display(); } 
		  else { ?>
				<a href="<?php bloginfo('url'); ?>"><?php esc_html_e('Home','Flexible') ?></a> <span class="raquo">&raquo;</span>
				
				<a href="<?php bloginfo('url'); ?>/stories">Stories</a> <span class="raquo">&raquo;</span>
				
				<?php if ($esa_searchstring = esa_search_string()) { ?>
					<?php echo $esa_searchstring; // generate search string with esa function; keep the rest just in case ?>
				<?php } elseif( is_tag() ) { ?>
					<?php esc_html_e('Posts Tagged ','Flexible') ?><span class="raquo">&quot;</span><?php single_tag_title(); echo('&quot;'); ?>
				<?php } elseif (is_day()) { ?>
					<?php esc_html_e('Posts made in','Flexible') ?> <?php the_time('F jS, Y'); ?>
				<?php } elseif (is_month()) { ?>
					<?php esc_html_e('Posts made in','Flexible') ?> <?php the_time('F, Y'); ?>
				<?php } elseif (is_year()) { ?>
					<?php esc_html_e('Posts made in','Flexible') ?> <?php the_time('Y'); ?>
				<?php } elseif (is_search()) { ?>
					<?php echo ($q = get_search_query()) ? "Search Results for '$q'" :  "Search Results"; ?>
				<?php } elseif (is_single()) { ?>
					<?php echo get_the_title(); ?>
				<?php } elseif (is_category()) { ?>
					<?php single_cat_title(); ?>
				<?php } elseif (is_tax()) { ?>
					<?php 
						$et_taxonomy_links = array();
						$et_term = get_queried_object();
						$et_term_parent_id = $et_term->parent;
						$et_term_taxonomy = $et_term->taxonomy;
						
						while ( $et_term_parent_id ) {
							$et_current_term = get_term( $et_term_parent_id, $et_term_taxonomy );
							$et_taxonomy_links[] = '<a href="' . esc_url( get_term_link( $et_current_term, $et_term_taxonomy ) ) . '" title="' . esc_attr( $et_current_term->name ) . '">' . esc_html( $et_current_term->name ) . '</a>';
							$et_term_parent_id = $et_current_term->parent;
						}
						
						if ( !empty( $et_taxonomy_links ) ) echo implode( ' <span class="raquo">&raquo;</span> ', array_reverse( $et_taxonomy_links ) ) . ' <span class="raquo">&raquo;</span> ';
					
						echo esc_html( $et_term->name ); 
					?>
				<?php } elseif (is_author()) { ?>
					<?php 
						global $wp_query;
						$curauth = $wp_query->get_queried_object();
					?>
					<?php esc_html_e('Posts by ','Flexible'); echo ' ',$curauth->nickname; ?>
				<?php } elseif (is_page_template('page-collection.php') && !is_page('collections')) { ?>
				    <?php echo "Collections &raquo; "; wp_title(''); ?>
				<?php } elseif (is_page_template('page-about.php') && !is_page('about')) { ?>
				    <?php echo "About &raquo; "; wp_title(''); ?>
				<?php } elseif (is_page_template('page-partner.php') && !is_page('partners')) { ?>
				    <?php echo "About &raquo; Partners &raquo; "; wp_title(''); ?>
				<?php } elseif (is_page()) { ?>
					<?php //wp_title(''); ?>
				<?php }; ?>
	<?php } ?>
</div> <!-- end #breadcrumbs -->
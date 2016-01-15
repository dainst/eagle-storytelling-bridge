<div class="et_pt_blogentry clearfix">
	
	<?php  $thumbnail_url = esa_thumbnail(get_post());  ?>
	
	<div class='esa_story_header'>
		<a href="<?php the_permalink(); ?>" class="readmore"><span>read Story&raquo;</span></a>
	
		<h2 class="et_pt_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?><?php //the_ID(); ?></a></h2>
		
		<div class="esa_pt_blogmeta">
			<?php 
				echo "Posted by ", esa_link_form(get_the_author(), array(
						'post_type' => 'story',
						'author' => get_the_author_meta('ID')
				));
				echo " on "; the_time(et_get_option('flexible_date_format'));
				echo '<br>' . esa_get_story_keywords();
			?>				
		</div>

		<p class="esa_pt_excerpt"><?php echo get_the_excerpt();?></p>
	</div>


</div> <!-- end .et_pt_blogentry -->
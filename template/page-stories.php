<?php
/*
  Template Name: Stories
 */
?>


<?php 
$is_esa_story_page = true;
$et_ptemplate_blog_perpage = 5;
?>

<?php get_header(); ?>

<?php include ('breadcrumbs-stories.php'); ?>

<div id="content-area" class="stories-list clearfix<?php if ( $fullwidth ) echo ' fullwidth'; ?>">
	<div id="left-area">
	
		<?php if (get_query_var('paged', 1) <= 1) {
			esa_item_map(); 
		} else { ?>
			<h1 class="page_title">STORIES</h1>
		<?php } ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
			<?php
				// the story page itself may have a thumbnail---
				$thumb = '';
				$width = apply_filters('et_blog_image_width',640);
				$height = apply_filters('et_blog_image_height',320);
				$classtext = '';
				$titletext = get_the_title();
				$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Blogimage');
				$thumb = $thumbnail["thumb"];
			?>
			<?php if ( '' != $thumb && 'on' == et_get_option('flexible_page_thumbnails') ) { ?>
				<div class="post-thumbnail">
					<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>	
				</div> 	<!-- end .post-thumbnail -->
			<?php } ?>
			
			
			
			<div class="post-content">
			
			
				<?php the_content(); ?>
				
				<div id="et_pt_blog" class="responsive">
					<?php $cat_query = ''; 
					if ( !empty($blog_cats) ) $cat_query = '&cat=' . implode(",", $blog_cats);
					else echo '<!-- blog category is not selected -->'; ?>
					<?php 
						$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );
					?>
					
					
					<?php query_posts("post_type=story&showposts=$et_ptemplate_blog_perpage&paged=" . $et_paged . $cat_query); ?>
					
					
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
						<?php include('loop-story.php'); ?>
						
					<?php endwhile; ?>
						<div class="page-nav clearfix">
							<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
							else { ?>
								 <?php get_template_part('includes/navigation'); ?>
							<?php } ?>
						</div> <!-- end .entry -->
					<?php else : ?>
						<?php get_template_part('includes/no-results'); ?>
					<?php endif; wp_reset_query(); ?>
				</div> <!-- end #et_pt_blog -->
				
				<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Flexible').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php edit_post_link(esc_attr__('Edit this page','Flexible')); ?>

			</div> 	<!-- end .post-content -->
		</article> <!-- end .entry -->
	</div> <!-- end #left_area -->

	<?php if ( ! $fullwidth ) include ('sidebar-stories.php'); ?>
</div> 	<!-- end #content-area -->

<?php get_footer(); ?>
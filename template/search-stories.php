<?php
/*
  Template Name: Search Stories
 */
?>


<?php $is_esa_story_page = true; ?>

<?php get_header(); ?>

<?php include ('breadcrumbs-stories.php'); ?>

<div id="content-area" class="stories-list  clearfix<?php if ( $fullwidth ) echo ' fullwidth'; ?>">
	<div id="left-area">
	    
	    <h1 class="page_title">
		    <?php echo esa_search_string(); ?>
	    </h1>
		
		
		
		<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
			
			
			<div class="post-content">
				
				<div id="et_pt_blog" class="responsive">
					<?php	
						if (have_posts()) {
							while ( have_posts() ) {
								the_post();
								include('loop-story.php');
							} // end while
							echo '<div class="page-nav clearfix">';
							echo esa_pagenavi();
							echo "</div>";
							
						} else {
							echo "<p>No Stories found that are matching your criteria.</p>";
						}
						wp_reset_query();
					?>
				</div> <!-- end #et_pt_blog -->
			</div> 	<!-- end .post-content -->
		</article> <!-- end .entry -->
	</div> <!-- end #left_area -->

	<?php if ( ! $fullwidth ) include ('sidebar-stories.php'); ?>
</div> 	<!-- end #content-area -->

<?php get_footer(); ?>


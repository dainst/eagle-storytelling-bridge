<?php if ( is_active_sidebar( 'sidebar' ) ){ ?>
	<div id="sidebar">
	
	
	

	<div class="topsearch">
          <h3><a href='<?php bloginfo('url'); ?>/stories'>Stories</a></h3>
          <p><a href='<?php bloginfo('url'); ?>/stories'>Flagship Storytelling Application</a></p>
    </div>
    
    <div class="widget">
    	<?php wp_nav_menu( array( 'theme_location' => 'esa-menu' ) ); ?>
	</div>

	<div class="widget">
	

		<form role="search" method="post" class="searchform" id="esa_searchform" action="<?php echo site_url(); ?>/">
			<h4 class="widgettitle">Search Stories</h4>
			<?php //<textarea><?php print_r($_POST); ? ></textarea> ?> 
    		<div>
        		<input type="text" name="s" class="s" value="<?php echo get_query_var('s') ?>" /><input type="submit" class="searchsubmit" value="Search" />
				<input type="hidden" name="post_type" value="story" />
    		</div>
			<h5>
				Filter search
				<select id='esa_multifilter_select' size='1' name='esa_multifilter_select'>
					<?php $selected = isset($_POST['esa_multifilter_select']) ? $_POST['esa_multifilter_select'] : ''; ?>
					<option value='users' 	<?php echo $selected == 'users' ? "selected='$selected'" : '' ?>	>by author</option>
					<option value='keywords'<?php echo $selected == 'keywords' ? "selected='$selected'" : '' ?>	>by keyword</option>
					<option value='language'<?php echo $selected == 'language' ? "selected='$selected'" : '' ?>	>by language</option>
				</select>
			</h5>
    		<div>
				<input 
					type='text'
					id='esa_multifilter'
					name='esa_multifilter' 
					placeholder='filter' 
					value="<?php echo (isset($_POST['esa_multifilter'])) ? $_POST['esa_multifilter'] : ''; ?>"
				/>
				<input
					type='hidden'
					id='esa_multifilter_selected'
					name='esa_multifilter_selected'
					value="<?php echo (isset($_POST['esa_multifilter_selected'])) ? $_POST['esa_multifilter_selected'] : ''; ?>" 
				/>
    		</div>
		</form>
	</div>
	
	
	
	<div id="recent-stories" class="widget widget_recent_entries">
		<h4 class="widgettitle">Featured Stories</h4>
		<ul>
			<?php
			    $args = array(
			        'post_type' => 'story',
			        'showposts' => 10,
			    	'meta_key'	=> 'esa_featured',
			    	'meta_value'=>	1
			    );
			    $latest_stories_loop = new WP_Query($args);

			    while ($latest_stories_loop->have_posts()) : $latest_stories_loop->the_post(); 
			?>
			<li>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			</li>
			<?php
			    endwhile;
			    wp_reset_postdata();
			?>
		</ul>
	</div> 

	<div class="widget widget_edit">
		<?php if (is_user_logged_in()) { ?>
			<h4 class='widgettitle'><?php echo 'Logged in as '.wp_get_current_user()->user_login ?></h4>
			<p>
				<?php if(is_single() and (current_user_can('edit_others_posts', get_) or ($post->post_author == $current_user->ID)))  { ?>
					<?php edit_post_link('Edit this story'); ?>	<br>
				<?php }?>
				<a href="<?php echo site_url(); ?>/wp-admin/post-new.php?post_type=story">Create new story</a><br>
				<a href="<?php echo site_url(); ?>/wp-admin/edit.php?post_type=story">Edit existing stories</a><br><br>
				<a href="<?php echo wp_logout_url() ?>">Logout</a>
			</p>
		<?php } else { ?>
			<h4 class='widgettitle'>Not logged in</h4>
			<p>
				<a href="<?php echo wp_login_url(site_url('/stories/')); ?>" title="Login">Log in (or create an account)</a> to create a story.
			</p>
		<?php }	?>
	</div>
		 		 
	</div> <!-- end #sidebar -->
<?php } ?>

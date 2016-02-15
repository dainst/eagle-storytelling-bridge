<?php if ( is_active_sidebar( 'sidebar' ) ){ ?>
	<div id="sidebar" class="sidebar-stories">

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
	        		<input 
	        			type="text" 
	        			name="s" 
	        			class="s" 
	        			value="<?php echo get_query_var('s') ?>"
	        		/><input type="submit" class="searchsubmit" value="Search" />
					<input type="hidden" name="post_type" value="story" />
	    		</div>
				<h5>
					Filter search
					<select id='esa_multifilter_select' size='1' name='esa_multifilter_select'>
						<?php 
							$selected = isset($_POST['esa_multifilter_select']) ? $_POST['esa_multifilter_select'] : ''; 
							$multi_filter_selected = '';
							$multi_filter = '';
							if (!$selected) { // if we come from another serach from than multifilter (might have been better to solve this with the query)
								if (isset($_POST['author'])) {
									$selected = 'users';
									$multi_filter = get_the_author();
									$multi_filter_selected = $_POST['author'];
								}
								if (isset($_POST['taxonomy']) and ($_POST['taxonomy'] == "story_keyword")) {
									$selected = 'keywords';
									$termo = get_term_by('slug', $_POST['term'], 'story_keyword');
									$multi_filter = $termo->name;
									$multi_filter_selected = $termo->term_id;
											
								}
							} 
						?>
						<option value='users' 	<?php echo $selected == 'users' ? "selected='selected'" : '' ?>	>by author</option>
						<option value='keywords'<?php echo $selected == 'keywords' ? "selected='selected'" : '' ?>	>by keyword</option>
						<option value='language'<?php echo $selected == 'language' ? "selected='selected'" : '' ?>	>by language</option>
					</select>
				</h5>
	    		<div>
					<input 
						type='text'
						id='esa_multifilter'
						name='esa_multifilter' 
						value="<?php echo (isset($_POST['esa_multifilter'])) ? $_POST['esa_multifilter'] : $multi_filter; ?>"
					/>
					<input
						type='hidden'
						id='esa_multifilter_selected'
						name='esa_multifilter_selected'
						value="<?php echo (isset($_POST['esa_multifilter_selected'])) ? $_POST['esa_multifilter_selected'] : $multi_filter_selected; ?>" 
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
				<ul>
					<?php if(is_single() and (current_user_can('edit_others_posts', get_) or ($post->post_author == $current_user->ID)))  { ?>
						<li><?php edit_post_link('Edit this story'); ?></li>
					<?php }?>
					<li><a href="<?php echo site_url(); ?>/wp-admin/post-new.php?post_type=story">Create new story</a></li>
					<li><a href="<?php echo site_url(); ?>/wp-admin/edit.php?post_type=story">Edit existing stories</a></li>
					<li></li>
					<li><a href="<?php echo wp_logout_url("http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}") ?>">Logout</a></li>
				</ul>
				
			<?php } else { ?>
			
				<h4 class='widgettitle'>Not logged in</h4>
				<script src='<?php echo plugins_url(); ?>/eagle-search/js/search.js?ver=4.4.2'></script>
				<script src='https://www.google.com/recaptcha/api.js'></script>
				    
				</form>
				<ul>
					<li><a href='javascript:;' onclick="Search.registerShow();" title="Register">Register</a> to create or edit stories</li>
					<li>or <a href='javascript:;' onclick="jQuery('#esa_login_form').toggle();" title="Login">log in </a> if you allready have an account).</li>
						<form style='display:none' id='esa_login_form' action="<?php echo site_url(); ?>/wp-login.php" method="post" class='esa_login'>
							<input placeholder='Username' name="log" id="lwa_user_login" value="" type="text"><br>
							<input placeholder='Password' name="pwd" id="lwa_user_pass" type="password"><br>
							<input name="redirect_to" value="<?php echo "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; ?>" type="hidden">
							<div><label for="rememberme"><input name="rememberme" id="rememberme" checked="checked" value="forever" type="checkbox"> Remember me</label></div>
						    <input name="submit" value="Login" class="button" type="submit">
						</form>
					
				</ul>
			<?php }	?>
		</div>
		

	</div> <!-- end #sidebar -->
<?php } ?>
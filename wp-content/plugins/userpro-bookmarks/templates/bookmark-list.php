<div class="userpro-bookmark-overlay-content">

	<a href="#" onclick="userpro_bookmark_close_overlay()" class="userpro-bookmark-close"><?php _e('Close','userpro'); ?></a>

	<div class="userpro-bookmark-new">

		<div class="userpro-bookmark-user">
			
			<div class="userpro-bookmark-user-thumb"><?php echo get_avatar($user_id, 50); ?></div>
			<div class="userpro-bookmark-user-info">
				<div class="userpro-bookmark-user-name">
					<a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges($user_id, $inline=true); ?>
				</div>
				<div class="userpro-bookmark-user-tab"><a href="<?php echo $userpro->permalink($user_id); ?>" class="userpro-flat-btn"><?php _e('View Profile','userpro-msg'); ?></a></div>
			</div>
			
		<div class="userpro-clear"></div>
		</div>
			
		<div class="userpro-bookmark-body">
			<p><?php echo $userpro_fav->bookmarks_count($user_id); ?> Posts are bookmarked.</p>
			<ul>
				<?php foreach ($userpro_fav->get_bookmarks($user_id) as $post_id => $collection) { 
					$post = get_post($post_id);
						if($post->post_title != '') {
				?>
					<li>
						<a href="<?php echo get_permalink($post-ID); ?>">
							<?php echo $post->post_title; ?>
						</a>
					</li>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>
		
	</div>

</div>

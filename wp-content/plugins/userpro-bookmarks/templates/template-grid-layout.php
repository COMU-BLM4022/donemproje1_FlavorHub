<?php

	global $userpro, $post, $userpro_fav;
	$defaults = array(
		'default_collection' => userpro_fav_get_option('default_collection'),
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	/* output */
	$output = '';
	$results = 0;
	// logged in
	if (userpro_is_logged_in()){
	
		$collections = $userpro_fav->get_collections( get_current_user_id() );
		
		?>
		<div class="upb-container">
		
		<div class="upb-filter">
		<?php
		foreach($collections as $id => $array) {
			include userpro_fav_path.'templates/template-grid-filter.php';
		}?>
		</div>
		
		<div class="upb-grid">
		<div class="upb-loader loading" style="display: none;"></div>
		<?php
			$id = 0;?>
			<div class="upb-single-bmcount collection_<?php echo $id;?>"><span><?php echo $userpro_fav->get_bookmarks_count_by_collection($id); echo __(' Bookmarks in collection','userpro-fav'); ?></span></div>
			<?php 
			$bks = $userpro_fav->get_bookmarks_by_collection($id);
			if (is_array($bks)){
				$bks = array_reverse($bks, true);
	
				foreach($bks as $bkid => $array) {
					
					if ($bkid != 'label' && $bkid != 'privacy' && $bkid != 'userid' && $bkid != 'type') {
						$results++;
						if (get_post_status($bkid) == 'publish') { // active post
							
							include (userpro_fav_path.'templates/template-single-bookmark.php');
							
						}
					}
				}
			}
		?>
		</div>
		</div>
		<?php 
	
	} else {
		if(userpro_fav_get_option('display_loginregister_popup') == 1){
			$output .= '<p>'.sprintf(__('You need to <a href="#" class="popup-login" data-login_redirect="%s">Login</a> or <a href="#" class="popup-register" data-register_redirect="%s">Register</a> to view and manage your bookmarks.','userpro-fav'),$userpro_fav->get_permalink(),$userpro_fav->get_permalink()).'</p>';
		}
		else{
			$output .= '<p>'.sprintf(__('You need to <a href="%s">login</a> or <a href="%s">register</a> to view and manage your bookmarks.','userpro-fav'), $userpro->permalink(0, 'login').'?redirect_to='.$userpro_fav->get_permalink(), $userpro->permalink(0, 'register')).'</p>';
		}
	}
	
	if(is_array($output)){ print_r($output); }else{ echo $output; }

	
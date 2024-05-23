<?php

/* Print bookmarks in Grid Layout */

add_action('wp_ajax_nopriv_upb_grid_print_bookmark', 'upb_grid_print_bookmark');
add_action('wp_ajax_upb_grid_print_bookmark', 'upb_grid_print_bookmark');
function upb_grid_print_bookmark(){
	
	global $userpro,$userpro_fav;
	$results = 0;
	$id = $_POST['collection_id'];
	$bks = $userpro_fav->get_bookmarks_by_collection($id);
	if (is_array($bks)){
		$bks = array_reverse($bks, true);
		ob_start();
		?>
		<div class="upb-single-bmcount collection_<?php echo $id;?>"><span><?php echo $userpro_fav->get_bookmarks_count_by_collection($id); echo __(' Bookmarks in collection','userpro-fav');?></span></div>
		<?php 
		foreach($bks as $bkid => $array) {
			if ($bkid != 'label' && $bkid != 'privacy' && $bkid != 'userid' && $bkid != 'type') {
				$results++;
				if (get_post_status($bkid) == 'publish') { // active post
					include (userpro_fav_path.'templates/template-single-bookmark.php');
				}
			}
		}
	}
	$output = ob_get_contents();
	ob_end_clean();
	$output = json_encode(array( 'html' => $output ));
	echo $output;
	die;
}

/* switch collection */
add_action('wp_ajax_nopriv_userpro_change_public_collection', 'userpro_change_public_collection');
add_action('wp_ajax_userpro_change_public_collection', 'userpro_change_public_collection');
function userpro_change_public_collection(){
	global $userpro_fav;
	$output = array();
	
	$collection_id = $_POST['collection_id'];
	$user_id = $_POST['user_id'];
	
	$output['res'] = $userpro_fav->print_public_bookmarks($collection_id,$user_id);

	$output=json_encode($output);
	if(is_array($output)){ print_r($output); }else{ echo $output; } die;
}



	/* switch collection */
	add_action('wp_ajax_nopriv_userpro_change_collection', 'userpro_change_collection');
	add_action('wp_ajax_userpro_change_collection', 'userpro_change_collection');
	function userpro_change_collection(){
		global $userpro_fav;
		$output = array();

		$collection_id = $_POST['collection_id'];
		 
		$output['res'] = $userpro_fav->print_bookmarks($collection_id);
	
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* remove collection */
	add_action('wp_ajax_nopriv_userpro_hard_remove_collection', 'userpro_hard_remove_collection');
	add_action('wp_ajax_userpro_hard_remove_collection', 'userpro_hard_remove_collection');
	function userpro_hard_remove_collection(){
		global $userpro_fav;
		$output = '';

		$collection_id = $_POST['collection_id'];
		$userpro_fav->hard_remove_collection( $collection_id );
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* soft-remove collection */
	add_action('wp_ajax_nopriv_userpro_soft_remove_collection', 'userpro_soft_remove_collection');
	add_action('wp_ajax_userpro_soft_remove_collection', 'userpro_soft_remove_collection');
	function userpro_soft_remove_collection(){
		global $userpro_fav;
		$output = '';
		$collection_id = $_POST['collection_id'];
		
		$userpro_fav->soft_remove_collection( $collection_id );
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* add new collection */
	add_action('wp_ajax_nopriv_userpro_fav_addcollection', 'userpro_fav_addcollection');
	add_action('wp_ajax_userpro_fav_addcollection', 'userpro_fav_addcollection');
	function userpro_fav_addcollection(){
		global $userpro_fav;
		$output = array();
		
		$user_id = get_current_user_id();
		$current_coll_count = count(get_user_meta($user_id, '_userpro_collections', true));
		$allowed_new_coll = userpro_fav_get_option('upb_new_collection_limit');
		
		if($current_coll_count < $allowed_new_coll){
		
			$collection_name = $_POST['collection_name'];
			$privacy = $_POST['privacy'];
			$userpro_fav->new_collection( $collection_name,$privacy );
			
			$output['options'] = '<select class="chosen-select-collections" name="userpro_bm_collection" id="userpro_bm_collection" data-placeholder="">' . $userpro_fav->collection_options( $_POST['default_collection'], $_POST['post_id'] ) . '</select>';
		}
		else{
			$output['errors'] = 'The limit for creating new collection has been reached';
		}
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* add new bookmark */
	add_action('wp_ajax_nopriv_userpro_fav_newbookmark', 'userpro_fav_newbookmark');
	add_action('wp_ajax_userpro_fav_newbookmark', 'userpro_fav_newbookmark');
	function userpro_fav_newbookmark(){
		global $userpro_fav;
		$output = array();
		if(isset($_REQUEST) && $_REQUEST['action'] == 'userpro_bookmark_icon' ){
			$collection_id = '0';
			$post_id = $_REQUEST['post_id'];
		}else{
			$collection_id = $_POST['collection_id'];
			$post_id = $_POST['post_id'];
		}
		
		$curr_bm_count = $userpro_fav->get_bookmarks_count_by_collection($collection_id);
		$allowed_bm_coll = userpro_fav_get_option('upb_bookmarks_limit');
		
		if($curr_bm_count < $allowed_bm_coll){
			
			$user_id = get_current_user_id();
			$collections = array();
			$collections = $userpro_fav->get_collections( $user_id );
			$bookmarks = $userpro_fav->get_bookmarks( $user_id );
			
			 //add collection (post id relation)
			if (empty($collections[$collection_id])){
				$collections[$collection_id] = array();
			}
			
			$collections[$collection_id][$post_id] = 1;
	
			
			/* add bookmark with collection id */
			if (!isset($bookmarks[$post_id])){
				$bookmarks[$post_id] = $collection_id;
			} else {
				$prev_collection_id = $bookmarks[$post_id];
				if( !userpro_fav_get_option('allow_multiple_bookmark') ){
					unset($collections[$prev_collection_id][$post_id]); // remove from prev collection
					update_post_meta($post_id , 'post_bookmark_count' , get_post_meta($post_id , 'post_bookmark_count' , true)-1); //change the bookmark count
				}
				$bookmarks[$post_id] = $collection_id; // update collection
			}
			
			$output['collection_id'] = $collection_id; // update active collection
			
			$old_bookmark_count = get_post_meta($post_id , 'post_bookmark_count' , true);
			
			$old_bookmark_count = !empty($old_bookmark_count) ? $old_bookmark_count : 0;
			
			update_user_meta($user_id, '_userpro_collections', $collections);
			update_user_meta($user_id, '_userpro_bookmarks', $bookmarks);
			update_post_meta($post_id , 'post_bookmark_count' , $old_bookmark_count+1);

			$output['updated_count'] = get_post_meta($post_id , 'post_bookmark_count' , true);
			
			$bookmarked_by = get_post_meta($post_id , 'userpro_bookmarked_by' , true);
			if($bookmarked_by == false) {
				$bookmarked_by = array($user_id); 
			}else{
				$bookmarked_by[] = $user_id;
			}
			update_post_meta($post_id , 'userpro_bookmarked_by' , $bookmarked_by);
		
		}else{
			
			$output['errors'] = 'The limit for adding bookmark to this collection has been reached.';
		
		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	add_action('wp_ajax_nopriv_userpro_fav_checkifbookmarked', 'userpro_fav_checkifbookmarked');
	add_action('wp_ajax_userpro_fav_checkifbookmarked', 'userpro_fav_checkifbookmarked');
	
	function userpro_fav_checkifbookmarked(){
		global $userpro_fav;
		$user_id = get_current_user_id();
		$post_id = $_POST['post_id'];
		$collections = $userpro_fav->get_collections( $user_id );
		
		$collection_id = $_POST['collection_id'];
		if (isset($collections[$collection_id][$post_id]))
			echo json_encode(array("status"=>true));
		else 
			echo json_encode(array("status"=>false));
		die();
	}
	
	/* remove bookmark */
	add_action('wp_ajax_nopriv_userpro_fav_removebookmark', 'userpro_fav_removebookmark');
	add_action('wp_ajax_userpro_fav_removebookmark', 'userpro_fav_removebookmark');
	function userpro_fav_removebookmark(){
		global $userpro_fav;
		$output = '';
		
		if(isset($_REQUEST) && $_REQUEST['action'] == 'userpro_bookmark_icon'){
			$post_id = $_REQUEST['post_id'];
			$collection_id = '0';
		}else{
			$post_id = $_POST['post_id'];
			$category_id = $_POST['category_id'];
			$collection_id = $_POST['collection_id'];
		}
		
		$user_id = get_current_user_id();
		$collections = (array)$userpro_fav->get_collections( $user_id );
		$bookmarks = $userpro_fav->get_bookmarks( $user_id );
		
		/******************************Code Added for category bookmark*****************************************/
		$bookmark_categories = $userpro_fav->get_category_bookmarks( $user_id );
		
		if(isset($category_id) && strrchr($category_id,","))
		{
			$categories=explode(",",$category_id);
			$categories_count = count($categories)-1;
			for($count=0;$count<$categories_count;$count++)
			{
				if($userpro_fav->bookmarked_category($categories[$count])){
					if (isset($bookmark_categories[$categories[$count]])){
						$curcollection_id = $bookmark_categories[$categories[$count]];
						unset($collections[$curcollection_id][$categories[$count]]); // remove from collections
						unset($bookmark_categories[$categories[$count]]); // remove from bookmarks
					}
			
					if (isset($collections[$collection_id][$categories[$count]])){
						unset($collections[$collection_id][$$categories[$count]]);
					}
				}
			}
		}
		else
		{
			if( isset($category_id) && $userpro_fav->bookmarked_category($category_id)){
				if (isset($bookmark_categories[$category_id])){
					$curcollection_id = $bookmark_categories[$category_id];
					unset($collections[$curcollection_id][$category_id]); // remove from collections
					unset($bookmark_categories[$category_id]); // remove from bookmarks
				}
			
				if (isset($collections[$collection_id][$category_id])){
					unset($collections[$collection_id][$category_id]);
				}
			}
		}
		/******************************Code Ended****************************************************************/
		
		if (isset($bookmarks[$post_id])){
			$curcollection_id = $bookmarks[$post_id];
			unset($collections[$curcollection_id][$post_id]); // remove from collections
			unset($bookmarks[$post_id]); // remove from bookmarks
		}
		
		if (isset($collections[$collection_id][$post_id])){
			unset($collections[$collection_id][$post_id]);
		}
				
		update_user_meta($user_id, '_userpro_collections', $collections);
		update_user_meta($user_id, '_userpro_bookmarks', $bookmarks);
		update_user_meta($user_id, '_userpro_bookmarks_category', $bookmark_categories);
		update_post_meta($post_id , 'post_bookmark_count' , get_post_meta($post_id , 'post_bookmark_count' , true)-1);
		

			$bookmarked_by = get_post_meta($post_id , 'userpro_bookmarked_by' , true);
			if($bookmarked_by == false) {
				$bookmarked_by = array();
			}else{
				if(($key = array_search($user_id, $bookmarked_by)) !== false) {
					unset($bookmarked_by[$key]);
				}
			}
			update_post_meta($post_id , 'userpro_bookmarked_by' , $bookmarked_by);
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
		
	}

	add_action('wp_head','check_is_rtl');
	function check_is_rtl(){
		?>
		<script type="text/javascript">
			var site_is_rtl = '<?php echo is_rtl(); ?>';
		</script>
		<?php
	}

		
/*******************************************************Code Added By Vipin for category bookmarks*******************************************************************/
	add_action('wp_ajax_nopriv_userpro_fav_newcategorybookmark', 'userpro_fav_newcategorybookmark');
	add_action('wp_ajax_userpro_fav_newcategorybookmark', 'userpro_fav_newcategorybookmark');
	function userpro_fav_newcategorybookmark(){
		global $userpro_fav;
		$output = '';
		$category_id = $_POST['category_id'];
		$collection_id = $_POST['collection_id'];
		$post_id = $_POST['post_id'];
		$user_id = get_current_user_id();
		$collections = $userpro_fav->get_collections( $user_id );
		$bookmark_categories = $userpro_fav->get_category_bookmarks( $user_id );
		$bookmarks=$userpro_fav->get_bookmarks($user_id);
		/* add collection (post id relation) */
		if (!isset($collections[$collection_id])){
			$collections[$collection_id] = array();
		}
		$collections[$collection_id][$post_id] = 1;
		
		/* add category bookmark with collection id */
		if (!isset($bookmark_categories[$category_id])){
			$bookmark_categories[$category_id] = $collection_id;
		} else {
			$prev_collection_id = $bookmark_categories[$category_id];
			unset($collections[$prev_collection_id][$category_id]); // remove from prev collection
			$bookmark_categories[$category_id] = $collection_id; // update collection
		}
		$category_posts=get_posts( array( 'category' => $category_id ,'numberposts'=>-1) );
		foreach($category_posts as $category_post)
		{
			if($userpro_fav->bookmarked($category_post->ID))
			{
			}
			else
			{
				if (!isset($bookmarks[$category_post->ID])){
					$bookmarks[$category_post->ID] = $collection_id;
				} else {
					$prev_collection_id = $bookmarks[$category_post->ID];
					unset($collections[$prev_collection_id][$category_post->ID]); // remove from prev collection
					$bookmarks[$category_post->ID] = $collection_id; // update collection
				}
				if (!isset($collections[$collection_id])){
					$collections[$collection_id] = array();
				}
				$collections[$collection_id][$category_post->ID] = 1;
				update_post_meta($category_post->ID , 'post_bookmark_count' , get_post_meta($category_post->ID , 'post_bookmark_count' , true)+1);
				
				$bookmarked_by = get_post_meta($category_post->ID , 'userpro_bookmarked_by' , true);
				if($bookmarked_by == false) {
					$bookmarked_by = array($user_id);
				}else{
					$bookmarked_by[] = $user_id;
				}
				update_post_meta($category_post->ID , 'userpro_bookmarked_by' , $bookmarked_by);
				
				
			}
		}
		/* add posts bookmark with collection id for the specified category*/

		$output['collection_id'] = $collection_id; // update active collection
		if($userpro_fav->bookmarked($post_id)){
			$output['post']='bookmarked';
		}
		else
		{
			$output['post']='unbookmarked';

		}

		update_user_meta($user_id, '_userpro_collections', $collections);
		update_user_meta($user_id, '_userpro_bookmarks', $bookmarks);
		update_user_meta($user_id, '_userpro_bookmarks_category', $bookmark_categories);

		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}

	/* remove category bookmark */
	add_action('wp_ajax_nopriv_userpro_fav_removecategorybookmark', 'userpro_fav_removecategorybookmark');
	add_action('wp_ajax_userpro_fav_removecategorybookmark', 'userpro_fav_removecategorybookmark');
	function userpro_fav_removecategorybookmark(){
		global $userpro_fav;
		$output = '';
		
		$category_id = $_POST['category_id'];
		$collection_id = $_POST['collection_id'];
		$post_id = isset($_POST['post_id'])?$_POST['post_id']:null;
		
		$user_id = get_current_user_id();
		$collections = $userpro_fav->get_collections( $user_id );
		$bookmark_categories = $userpro_fav->get_category_bookmarks( $user_id );
		
		if (isset($bookmark_categories[$category_id])){
			$curcollection_id = $bookmark_categories[$category_id];
			unset($collections[$curcollection_id][$category_id]); // remove from collections
			unset($bookmark_categories[$category_id]); // remove from bookmarks
		}
		
		if (isset($collections[$collection_id][$category_id])){
			unset($collections[$collection_id][$category_id]);
		}
		$category_posts=get_posts( array( 'category' => $category_id ,'numberposts'=>-1) );
		foreach($category_posts as $category_post)
		{
			update_post_meta($category_post->ID , 'post_bookmark_count' , get_post_meta($category_post->ID , 'post_bookmark_count' , true)-1);
		
			$bookmarked_by = get_post_meta($category_post->ID , 'userpro_bookmarked_by' , true);
			if($bookmarked_by == false) {
				$bookmarked_by = array();
			}else{
				if(($key = array_search($user_id, $bookmarked_by)) !== false) {
					unset($bookmarked_by[$key]);
				}
			}
			update_post_meta($category_post->ID , 'userpro_bookmarked_by' , $bookmarked_by);
			
		}		
		update_user_meta($user_id, '_userpro_collections', $collections);
		update_user_meta($user_id, '_userpro_bookmarks_category', $bookmark_categories);

		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
		
/******************************************************************Code End*******************************************************************/
	
	add_action('wp_ajax_nopriv_userpro_profile_bookmark_list', 'userpro_profile_bookmark_list');
	add_action('wp_ajax_userpro_profile_bookmark_list', 'userpro_profile_bookmark_list');
	
	function userpro_profile_bookmark_list() {
		global $userpro , $userpro_fav;
		$user_id = $_POST['user_id'];
		include_once (userpro_fav_path.'templates/profile-public-bookmarks-list.php');
		//echo do_shortcode('[userpro_publicbookmark]');
		die();
	}

	add_action('wp_ajax_nopriv_userpro_profile_bookmark_popup', 'userpro_profile_bookmark_popup');
	add_action('wp_ajax_userpro_profile_bookmark_popup', 'userpro_profile_bookmark_popup');
	
	function userpro_profile_bookmark_popup() {
		global $userpro , $userpro_fav;
		$post_id= $_POST['post_id'];
		
		include_once (userpro_fav_path.'templates/popup-bookmark-widget.php');
		
		die();
	}
	
add_action('wp_ajax_nopriv_userpro_bookmark_icon', 'userpro_bookmark_icon');
add_action('wp_ajax_userpro_bookmark_icon', 'userpro_bookmark_icon');

function userpro_bookmark_icon() {

	$post_id= $_POST['post_id'];
	$condition= $_POST['condition'];
	
	if($condition == 'bookmarked'){
		userpro_fav_removebookmark($post_id);
	}else{
		userpro_fav_newbookmark($post_id);
	}
	
}

add_action('wp_ajax_nopriv_upb_add_new_collection', 'upb_add_new_collection');
add_action('wp_ajax_upb_add_new_collection', 'upb_add_new_collection');

function upb_add_new_collection() {

	ob_start();
	include_once userpro_fav_path . 'admin/templates/add-new-collections.php';

	$template = ob_get_contents();
	ob_end_clean();
	echo json_encode(array('html' => $template));
	die;
}

add_action('wp_ajax_nopriv_upb_save_new_collection', 'upb_save_new_collection');
add_action('wp_ajax_upb_save_new_collection', 'upb_save_new_collection');

function upb_save_new_collection() {

	$collection_privacy = $_POST['collection_privacy'];
	$collection_title = $_POST['collection_title']." ($collection_privacy)";
	$collection_id = $_POST['collection_id'];

	$admin_default_collections = array();
	$admin_default_collections = get_option('admin_default_collections');
	
	if (!empty($admin_default_collections) && array_key_exists( $collection_id, $admin_default_collections)) {
		wp_die();
	}
	
	$new_collection = array( $collection_id => array("label"=>$collection_title,"privacy"=>$collection_privacy,"userid"=>get_current_user_id() ));
	
	if(empty($admin_default_collections)){
		$admin_default_collections = $new_collection;
	}
	else{
		$admin_default_collections = array_merge($admin_default_collections , $new_collection);
	}
	update_option('admin_default_collections',$admin_default_collections);
}

add_action('wp_ajax_nopriv_upb_delete_collection', 'upb_delete_collection');
add_action('wp_ajax_upb_delete_collection', 'upb_delete_collection');

function upb_delete_collection() {
	
	$collections = get_option('admin_default_collections');
	$custom_id = $_POST['collection_id'];
	unset($collections[$custom_id]);
	
	update_option('admin_default_collections',$collections);
}
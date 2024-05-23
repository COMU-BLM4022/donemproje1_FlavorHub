<?php

class userpro_fav_api {

	function __construct() {
		add_filter('manage_posts_columns', array(&$this,'userpro_bookmark_count'));
		add_filter('manage_pages_columns', array(&$this,'userpro_bookmark_count'));

		add_action('manage_posts_custom_column',  array(&$this,'userpro_show_bookmark_count'));
		add_action('manage_pages_custom_column',  array(&$this,'userpro_show_bookmark_count'));

		add_filter('manage_edit-post_sortable_columns',  array(&$this,'userpro_manage_sortable_columns' ));
		add_filter('manage_edit-page_sortable_columns',  array(&$this,'userpro_manage_sortable_columns' ));

		add_action( 'pre_get_posts', array(&$this, 'manage_wp_posts_be_qe_pre_get_posts') );


	}
	function searchForId($id, $array)
	{

		foreach ($array as $key => $val)
		{

			if ($val['userid'] === $id)
			return 1;

		}
	}

	/* New collection */
	function new_collection($name,$privacy) {
		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);
		$collections[] = array('label' => $name,'privacy'=>$privacy,'userid'=>$user_id);
		update_user_meta($user_id, '_userpro_collections', $collections);

		if($privacy=="public")
		{
			$privacycollections=get_option("_userpro_collections");

			if(!is_array($privacycollections))
			{
				 $privacycollections = array();
				 $checkforid=0;
			}
			else
			{
				 $checkforid=$this->searchForId($user_id, $privacycollections);
			}
			$privacycollections[]=array('userid'=>$user_id);

			if($checkforid != 1)
		        update_option("_userpro_collections",$privacycollections);
		}


	}
	/******************************************
	Clear previous cache
	******************************************/
	function clear_bookmarklist(){
		global $wpdb;
		$usermetatable = $wpdb->base_prefix."usermeta";
		delete_option("_userpro_collections");
		delete_option('admin_default_collections');
		$query ="SELECT user_id FROM $usermetatable WHERE meta_key = '_userpro_collections'";
		$users=$wpdb->get_results($query);
		if(isset($users))
		{
			foreach($users as $user)
			{

				delete_user_meta($user->user_id,'_userpro_bookmarks');
				delete_user_meta($user->user_id,"_userpro_collections");

			}
		}
	}


	/* Remove a collection */
	function hard_remove_collection($id){

		$user_id = get_current_user_id();


		$collections = $this->get_collections($user_id);

		$bookmarks = $this->get_bookmarks( $user_id );

		// remove bookmarks
		foreach($collections[$id] as $k => $arr) {
			if ($k != 'label') {
				if (isset($bookmarks[$k])){
					unset($bookmarks[$k]);
				}
			}
		}

		// remove collection
		if ($id > 0){
			unset($collections[$id]);
		}

		update_user_meta($user_id, '_userpro_bookmarks', $bookmarks);
		update_user_meta($user_id, '_userpro_collections', $collections);
	}

	/* Soft-Remove a collection */
	function soft_remove_collection($id){

		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);
		$bookmarks = $this->get_bookmarks( $user_id );

		// transfer bookmarks to default collection
		foreach($collections[$id] as $k => $arr) {
			if ($k != 'label') {
				$collections[0][$k] = 1;
			}
		}

		// remove collection
		if ($id > 0){
			unset($collections[$id]);
		}

		update_user_meta($user_id, '_userpro_bookmarks', $bookmarks);
		update_user_meta($user_id, '_userpro_collections', $collections);
	}

	function get_public_bookmarks_by_collection($id,$userid){

		$collections = $this->get_collections( $userid );
		return $collections[$id];
	}

	/* Get bookmarks by collection */
	function get_bookmarks_by_collection($id){

		$collections = $this->get_collections( get_current_user_id() );

		return $collections[$id];
	}

	function get_public_bookmarks_count_by_collection($id,$userid){
		$collections = $this->get_collections( $userid );
		return (int)count($collections[$id])-3;

	}

	function get_bookmarks_count_by_collection($id){
		$collections = $this->get_collections( get_current_user_id() );

		if ($id == '0'){
			if (empty($collections[$id])){
				return 0;
			} else {
				return (int)count($collections[$id]);
			}
		} else {
			return (int)count($collections[$id])-3;
		}
	}

	function print_public_bookmarks($coll_id,$userid)
	{

		global $userpro;
		$output = '';

		$output .= '<div class="userpro-coll-count">';
		$output .= sprintf(__('%s Bookmarks in Collection','userpro-fav'), $this->get_public_bookmarks_count_by_collection($coll_id,$userid));

		if ($coll_id != 0) { // default cannot be removed
			//	$output .= '<a href="#" class="userpro-bm-btn bookmarked userpro-remove-collection" data-undo="'.__('Undo','userpro-fav').'" data-remove="'.__('Remove Collection','userpro-fav').'">'.__('Remove Collection','userpro-fav').'</a>';

			/* To hide a collection */
			$output .= '<div class="userpro-coll-remove">';
			$output .= __('Choose how do you want to remove this collection. This action cannot be undone!','userpro-fav');$output .= '<div class="userpro-coll-remove-btns">';

			if ($this->get_public_bookmarks_count_by_collection($coll_id,$userid) > 0) {
				$output .= '<a href="#" class="userpro-bm-btn userpro-hard-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection and all bookmarks in it','userpro-fav').'</a>';
				$output .= '<a href="#" class="userpro-bm-btn secondary userpro-soft-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection only','userpro-fav').'</a>';
			} else {
				$output .= '<a href="#" class="userpro-bm-btn secondary userpro-hard-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection','userpro-fav').'</a>';
			}
			$output .= '</div>';
			$output .= '</div>';

		}

		$output .= '</div>';

		$bks = $this->get_public_bookmarks_by_collection( $coll_id,$userid );
		$results = 0;
		if (is_array($bks)){
			$bks = array_reverse($bks, true);
			foreach($bks as $id => $array) {
				if ($id != 'label' && $id != 'privacy' && $id != 'userid') {
					$results++;
					$categories=wp_get_post_categories($id);
					if(count($categories)>=1)
					{
						$post_status=0;
						foreach($categories as $category){
							$post_status+=1;
							if($post_status==1){
								if (get_post_status($id) == 'publish') { // active post
									$output .= '<div class="userpro-coll-item">';
									//$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'" data-category_id="'.$category.'">'.__('Remove','userpro-fav').'</a>';

									$output .= '<div class="uci-thumb" style="width:50px"><a href="'.get_permalink($id).'">'.$userpro->post_thumb($id, 50).'</a></div>';

									$output .= '<div class="uci-content">';
									$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
									$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
									$output .= '</div><div class="userpro-clear"></div>';

									$output .= '</div><div class="userpro-clear"></div>';

								} else {

									$output .= '<div class="userpro-coll-item">';
									$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','userpro-fav').'</a>';

									$output .= '<div class="uci-thumb" style="width:50px"></div>';

									$output .= '<div class="uci-content">';
									$output .= '<div class="uci-title">'.__('Content Removed','userpro-fav').'</div>';
									$output .= '<div class="uci-url"></div>';
									$output .= '</div><div class="userpro-clear"></div>';

									$output .= '</div><div class="userpro-clear"></div>';

								}
							}
						}
					}
					else{
						if (get_post_status($id) == 'publish') { // active post
							$output .= '<div class="userpro-coll-item">';
							//	$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','userpro-fav').'</a>';

							$output .= '<div class="uci-thumb" style="width:50px"><a href="'.get_permalink($id).'">'.$userpro->post_thumb($id, 50).'</a></div>';

							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
							$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
							$output .= '</div><div class="userpro-clear"></div>';

							$output .= '</div><div class="userpro-clear"></div>';


						} else {

							$output .= '<div class="userpro-coll-item">';
							//	$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','userpro-fav').'</a>';

							$output .= '<div class="uci-thumb" style="width:50px"></div>';

							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title">'.__('Content Removed','userpro-fav').'</div>';
							$output .= '<div class="uci-url"></div>';
							$output .= '</div><div class="userpro-clear"></div>';

							$output .= '</div><div class="userpro-clear"></div>';
						}
					}
				}
			}
		}

		if ($results == 0){
			$output .= '<div class="userpro-coll-item">';
			$output .= __('You did not add any content to this collection yet.','userpro-fav');
			$output .= '<div class="userpro-clear"></div></div><div class="userpro-clear"></div>';
		}
		return $output;


	}


	/* print bookmarks */
	function print_bookmarks($coll_id) {
		global $userpro;
		$output = '';

		$output .= '<div class="userpro-coll-count">';
		$output .= sprintf(__('%s Bookmarks in Collection','userpro-fav'), $this->get_bookmarks_count_by_collection($coll_id));

		if ($coll_id != 0) { // default cannot be removed
		$output .= '<a href="#" class="userpro-bm-btn bookmarked userpro-remove-collection" data-undo="'.__('Undo','userpro-fav').'" data-remove="'.__('Remove Collection','userpro-fav').'">'.__('Remove Collection','userpro-fav').'</a>';

		/* To hide a collection */
		$output .= '<div class="userpro-coll-remove">';
		$output .= __('Choose how do you want to remove this collection. This action cannot be undone!','userpro-fav');
		$output .= '<div class="userpro-coll-remove-btns">';
		if ($this->get_bookmarks_count_by_collection($coll_id) > 0) {
		$output .= '<a href="#" class="userpro-bm-btn userpro-hard-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection and all bookmarks in it','userpro-fav').'</a>';
		$output .= '<a href="#" class="userpro-bm-btn secondary userpro-soft-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection only','userpro-fav').'</a>';
		} else {
		$output .= '<a href="#" class="userpro-bm-btn secondary userpro-hard-remove" data-collection_id="'.$coll_id.'">'.__('Remove collection','userpro-fav').'</a>';
		}
		$output .= '</div>';
		$output .= '</div>';

		}

		$output .= '</div>';

		$bks = $this->get_bookmarks_by_collection( $coll_id );
		$results = 0;
		if (is_array($bks)){
		$bks = array_reverse($bks, true);
		foreach($bks as $id => $array) {

			if ($id != 'label' && $id != 'privacy' && $id != 'userid' && $id != 'type') {
			$results++;
					$categories=wp_get_post_categories($id);
					if(count($categories)>=1)
					{
					$post_status=0;
						foreach($categories as $category){
						$post_status+=1;
						if($post_status==1){
			if (get_post_status($id) == 'publish') { // active post
				$output .= '<div class="userpro-coll-item">';
								$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'" data-category_id="'.$category.'">'.__('Remove','userpro-fav').'</a> ';

				$output .= '<div class="uci-thumb" style="width:50px"><a href="'.get_permalink($id).'">'.$userpro->post_thumb($id, 50).'</a></div>';

				$output .= '<div class="uci-content">';
				$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
				$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
				$output .= '</div><div class="userpro-clear"></div>';

				$output .= '</br>'.userpro_bookmark_sharebutton(get_permalink($id)).'</div><div class="userpro-clear"></div>';

			} else {

				$output .= '<div class="userpro-coll-item">';
				$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','userpro-fav').'</a>';

				$output .= '<div class="uci-thumb" style="width:50px"></div>';

				$output .= '<div class="uci-content">';
				$output .= '<div class="uci-title">'.__('Content Removed','userpro-fav').'</div>';
				$output .= '<div class="uci-url"></div>';
				$output .= '</div><div class="userpro-clear"></div>';

				$output .= '</div><div class="userpro-clear"></div>';

			}
						}
					}
				}
					else{
						if (get_post_status($id) == 'publish') { // active post
							$output .= '<div class="userpro-coll-item">';
							$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','userpro-fav').'</a>';

							$output .= '<div class="uci-thumb" style="width:50px"><a href="'.get_permalink($id).'">'.$userpro->post_thumb($id, 50).'</a></div>';

							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title"><a href="'.get_permalink($id).'">'. get_the_title($id) . '</a></div>';
							$output .= '<div class="uci-url"><a href="'.get_permalink($id).'">'.get_permalink($id).'</a></div>';
							$output .= '</div><div class="userpro-clear"></div>';

							$output .= '</div><div class="userpro-clear"></div>';

						} else {
							$output .= '<div class="userpro-coll-item">';
							$output .= '<a href="#" class="userpro-coll-abs userpro-bm-btn secondary" data-post_id="'.$id.'" data-collection_id="'.$coll_id.'">'.__('Remove','userpro-fav').'</a>';

							$output .= '<div class="uci-thumb" style="width:50px"></div>';

							$output .= '<div class="uci-content">';
							$output .= '<div class="uci-title">'.__('Content Removed','userpro-fav').'</div>';
							$output .= '<div class="uci-url"></div>';
							$output .= '</div><div class="userpro-clear"></div>';

							$output .= '</div><div class="userpro-clear"></div>';
						}
					}
			}
		}
		}

		if ($results == 0){
			$output .= '<div class="userpro-coll-item">';
			$output .= __('You did not add any content to this collection yet.','userpro-fav');
			$output .= '<div class="userpro-clear"></div></div><div class="userpro-clear"></div>';
		}

		return $output;
	}

	/* Get collections for user */
	function collection_options($default_collection, $post_id){
		$output = '';
		$user_id = get_current_user_id();
		$collections = $this->get_collections($user_id);

		$bookmarks = (array) get_user_meta($user_id, '_userpro_bookmarks', true);
		if (isset($bookmarks[$post_id])){
			$cur_collection = $bookmarks[$post_id];
		} else {
			$cur_collection = 0;
		}

		foreach($collections as $k => $v) {
			if ( $k == '0' ){
				$v=array();
				$v['label'] = $default_collection;
			}
			$output .= '<option value="'.$k.'" '.selected($k, $cur_collection, 0).' >'.$v['label'];
			$output .= '</option>';
		}
		return $output;
	}

	/* Find collection ID */
	function collection_id($post_id){
		$user_id = get_current_user_id();
		$bookmarks = (array) get_user_meta($user_id, '_userpro_bookmarks', true);
		if (isset($bookmarks[$post_id])){
			return $bookmarks[$post_id];
		}
	}

	/**
		Is post bookmarked
	**/


	function bookmarked($post_id){
		$user_id = get_current_user_id();
		$bookmarks = (array) get_user_meta($user_id, '_userpro_bookmarks', true);
		if (isset($bookmarks[$post_id])){
			return true;
		}
		return false;
	}

	/* Delete collection */
	function delete_collection($collection_id, $user_id) {
		$array = $this->get_collections($user_id);
		unset($array[$collection_id]);
		update_user_meta($user_id, '_userpro_collections', $array);
	}

	/* Get collections */
	function get_collections($user_id) {

		$admin_default_collections = get_option('admin_default_collections');
		$collections = (array) get_user_meta($user_id, '_userpro_collections', true);
		if(!empty($admin_default_collections)){
		 	foreach($admin_default_collections as $k => $v){
				if (!array_key_exists( $k, $collections) && !empty($admin_default_collections)) {
					$collections = array_merge($collections , $admin_default_collections);
				}
			}
		}
		return $collections;

	}

	/* Get bookmarks */
	function get_bookmarks($user_id) {
		return (array)get_user_meta($user_id, '_userpro_bookmarks', true);
	}

	/* Count bookmarks */
	function bookmarks_count($user_id) {
		$bookmarks = (array)get_user_meta($user_id, '_userpro_bookmarks', true);
		unset($bookmarks[0]);
		if (!empty($bookmarks) ){
			return count($bookmarks);
		} else {
			return 0;
		}
	}

	/* Get current page url */
	function get_permalink(){
		global $post;
		if (is_home()){
			$permalink = home_url();
		} else {
			if (isset($post->ID)){
				$permalink = get_permalink($post->ID);
			} else {
				$permalink = '';
			}
		}
		return $permalink;
	}

	/**
		Display the bookmarks in
		organized collections
	**/
//	Print User Bookmarks body
	function print_user_bookmarks($privicy, $userid){

		$output = '';

		$collections=$this->get_collections($userid);

		foreach($collections as $id => $array) {
			if(isset($array['privacy']) && $array['privacy']==$privicy)
			{

				$output .= '<a href="#collection_'.$id.'" data-collection_id="'.$id.'" data-userid_id="'.$userid.'">';
				$output .= '<i class="userpro-icon-caret-left userpro-coll-hide"></i>';
				$output .= '<span class="userpro-coll-list-count">'.$this->get_public_bookmarks_count_by_collection($id,$userid).'</span>';
				$output .= $array['label'].'</a>';
			}
		}
		return $output;
	}
	function publicbookmar($args = array())
	{
		global $userpro, $post;

		extract( $args, EXTR_SKIP );

		/* output */
		$output = '';

		// logged in


		$admin_default_collections = get_option('admin_default_collections');
		$publiccollection=get_option("_userpro_collections");
		if(isset($admin_default_collections) && !empty($admin_default_collections)){
			foreach($admin_default_collections as $k => $v){
				if ( empty($publiccollection) && !empty($admin_default_collections)) {
					$publiccollection = array();
					$publiccollection = array_merge($publiccollection , $admin_default_collections);
				}
			}

		}

		if(isset($publiccollection) && !empty($publiccollection))
		{
			foreach ($publiccollection as $singleusercollection)
			{

			if(isset($singleusercollection['userid'])){
				$userid = $_POST['user_id'];
//				Print private bookmarks for current user on his profile
				$output .= '<div class="userpro-coll">';
				$output .= '<div class="userpro-coll-listpublic">';
				if($userid == $_POST['user_id']){
					$output .= $this->print_user_bookmarks('private', $userid);
				}
					$output .= $this->print_user_bookmarks('public', $userid);
//				end private bookmarks

				$output .= '</div>';
				$output .= '<div class="userpro-coll-body">';
				$output .= '<div class="userpro-coll-body-inner">';

				//$output .= $this->print_public_bookmarks($coll_id = 0,$singleusercollection['userid'] );

				$output .= '</div></div><div class="userpro-clear"></div>';

				$output .= '</div>';
			}

		}
	    }
		// guest


		return $output;


	}



	function bookmarks( $args = array() ){
		global $userpro, $post;
		$defaults = array(
			'default_collection' => userpro_fav_get_option('default_collection'),
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		/* output */
		$output = '';

		// logged in
		if (userpro_is_logged_in()){
		$output .= '<div class="userpro-coll">';
		$output .= '<div class="userpro-coll-list">';
		$collections = $this->get_collections( get_current_user_id() );
		$active_coll = 0;

		foreach($collections as $id => $array) {
			if (!isset($array['label'])) { $array=array(); $array['label'] = $default_collection; }
			if ($id === $active_coll) { $class = 'active'; } else { $class = ''; }
			$output .= '<a href="#collection_'.$id.'" data-collection_id="'.$id.'" class="'.$class.'">';
			if ($class == 'active'){
			$output .= '<i class="userpro-icon-caret-left"></i>';
			$output .= '<span class="userpro-coll-list-count userpro-coll-hide">'.$this->get_bookmarks_count_by_collection($id).'</span>';
			} else {
			$output .= '<i class="userpro-icon-caret-left userpro-coll-hide"></i>';
			$output .= '<span class="userpro-coll-list-count">'.$this->get_bookmarks_count_by_collection($id).'</span>';
			}
			$output .= $array['label'].'</a>';
		}

		$output .= '</div>';
		$output .= '<div class="userpro-coll-body">';
		$output .= '<div class="userpro-coll-body-inner">';

		$output .= $this->print_bookmarks($coll_id = 0);

		$output .= '</div></div><div class="userpro-clear"></div>';

		$output .= '</div>';

		// guest





		} else {
			if(userpro_fav_get_option('display_loginregister_popup') == 1){
				$output .= '<p>'.sprintf(__('You need to <a href="#" class="popup-login" data-login_redirect="%s">Login</a> or <a href="#" class="popup-register" data-register_redirect="%s">Register</a> to view and manage your bookmarks.','userpro-fav'),$this->get_permalink(),$this->get_permalink()).'</p>';
			}
			else{
				$output .= '<p>'.sprintf(__('You need to <a href="%s">login</a> or <a href="%s">register</a> to view and manage your bookmarks.','userpro-fav'), $userpro->permalink(0, 'login').'?redirect_to='.$this->get_permalink(), $userpro->permalink(0, 'register')).'</p>';
			}
		}

		return $output;
	}


	/**
		Bookmark: display the widget that allow

		bookmarks
	**/
	function bookmarkpouup($postid,$args=array() ){
		global $userpro, $post;
		$defaults = array(
			'width' => userpro_fav_get_option('width'),
			'align' => userpro_fav_get_option('align'),
			'inline' => userpro_fav_get_option('inline'),
			'no_top_margin' => userpro_fav_get_option('no_top_margin'),
			'no_bottom_margin' => userpro_fav_get_option('no_bottom_margin'),
			'pct_gap' => userpro_fav_get_option('pct_gap'),
			'px_gap' => userpro_fav_get_option('px_gap'),
			'widgetized' => userpro_fav_get_option('widgetized'),
			'remove_bookmark' => userpro_fav_get_option('remove_bookmark'),
			'dialog_bookmarked' => userpro_fav_get_option('dialog_bookmarked'),
			'dialog_unbookmarked' => userpro_fav_get_option('dialog_unbookmarked'),
			'default_collection' => userpro_fav_get_option('default_collection'),
			'add_to_collection' => userpro_fav_get_option('add_to_collection'),
			'new_collection' => userpro_fav_get_option('new_collection'),
			'new_collection_placeholder' => userpro_fav_get_option('new_collection_placeholder'),
			'add_new_collection' => userpro_fav_get_option('add_new_collection'),
			'bookmark_category' => userpro_fav_get_option('bookmark_category'),
			'remove_bookmark_category' => userpro_fav_get_option('remove_bookmark_category'),
		);

		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		/* options */
		if (strstr($width, 'px')) { $px = 'px'; } else { $px = '%'; }
		if ($px == '%') {
			$btn_width = 50 - $pct_gap . $px;
		} else {
			$width = str_replace($px, '', $width);
			$btn_width = ($width / 2 ) - $px_gap . $px;
		}
		if ($widgetized == 1){
			$btn_width = '100%';
		}

		/* output */
		$output = '';

		// logged in
		if (userpro_is_logged_in()){

		if (isset($postid)){

			$post_id=$postid;
			$terms=wp_get_post_categories($post_id);
			$category_id=null;
			if(is_array($terms))
			{
				if(sizeof($terms)===1)
				{
					foreach($terms as $term)
					{
						$category_id=$term;
					}
				}
				elseif(sizeof($terms)>1)
				{
					foreach($terms as $term)
					{
						$category_id.=$term.",";
					}
				}
				else
				{
					$category_id=null;
				}
			}
		} else {
			$post_id = null;
			$category_id=null;
		}

		$output .= '<div class="userpro-bm userpro-bm-nobottommargin-'.$no_bottom_margin.' userpro-bm-notopmargin-'.$no_top_margin.' userpro-bm-inline-'.$inline.' userpro-bm-'.$align.' userpro-bm-widgetized-'.(int)$widgetized.'" style="width:'.$width.' !important;" data-add_new_collection="'.$add_new_collection.'" data-default_collection="'.$default_collection.'" data-new_collection_placeholder="'.$new_collection_placeholder.'" data-dialog_unbookmarked="'.$dialog_unbookmarked.'" data-dialog_bookmarked="'.$dialog_bookmarked.'" data-add_to_collection="'.$add_to_collection.'" data-remove_bookmark="'.$remove_bookmark.'" data-post_id="'.$post_id.'" data-category_id="'.$category_id.'" data-remove_bookmark_category="'.$remove_bookmark_category.'" data-bookmark_category="'.$bookmark_category.'">';


		$output .= '<div class="userpro-bm-inner">';
		if(userpro_fav_get_option('bookmark_hearticon')=='1')
		$output .= '<div><img src="'.userpro_fav_url.'img/heart.png" title="This post is bookmarked by '.get_post_meta(get_the_ID() , 'post_bookmark_count' ,true).' users." /> <span class="userpro-bm-count">'.get_post_meta(get_the_ID() , 'post_bookmark_count' ,true).'</span></div>';
			/* collections list */
		$output .= '<div class="userpro-bm-list">';
		$output .= '<select class="chosen-select-collections" name="userpro_bm_collection" id="userpro_bm_collection" data-placeholder="">';
		$output .= $this->collection_options( $default_collection, $post_id );
		$output .= '</select>';
		$output .= '</div>';

		/* action buttons */
		$output .= '<div class="userpro-bm-act">';

		if ($this->bookmarked($post_id)) {
			$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($post_id).'" />';
			$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary bookmarked" data-action="bookmark">'.$remove_bookmark.'</a></div>';
		} else {
			$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary unbookmarked" data-action="bookmark">'.$add_to_collection.'</a></div>';
		}
		if(userpro_fav_get_option('display_newcollection')=='1')
		$output .= '<div class="userpro-bm-btn-contain bm-right" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn secondary" data-action="newcollection">'.$new_collection.'</a></div>';
		if($category_id!=null && userpro_fav_get_option('userpro_category_bookmark')){
			if(strrchr($category_id,","))
			{
				foreach($terms as $term_id)
				{
					if($this->bookmarked_category($term_id)){
						$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($term_id).'" />';
						$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary bookmarked_category" data-action="bookmarkcategory" data-category="'.$term_id.'">'.$remove_bookmark_category."-".get_cat_name( $term_id ).'</a></div><div class="userpro-clear"></div>';
					}
					else
					{
						$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary unbookmarked_category" data-action="bookmarkcategory" data-category="'.$term_id.'">'.$bookmark_category."-".get_cat_name( $term_id ).'</a></div><div class="userpro-clear"></div>';
					}

				}
			}
			else{
				if($this->bookmarked_category($category_id)){
					$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($category_id).'" />';
					$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary bookmarked_category" data-action="bookmarkcategory">'.$remove_bookmark_category.'</a></div>';
				}
				else
				{
					$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary unbookmarked_category" data-action="bookmarkcategory">'.$bookmark_category.'</a></div>';
				}
			}

		}
		$output .= '</div><div class="userpro-clear"></div>';

		$output .= '</div>';
		$output .= '</div>';

		if (!$inline) {
			$output .= '<div class="userpro-clear"></div>';
		}

		// guest
		} else {
			if(userpro_fav_get_option('display_loginregister_popup') == 1){
				$output .= '<p>'.sprintf(__('You need to <a href="#" class="popup-login" data-login_redirect="%s">Login</a> or <a href="#" class="popup-register" data-register_redirect="%s">Register</a> to bookmark/favorite this content.','userpro-fav'),$this->get_permalink(),$this->get_permalink()).'</p>';
			}
			else{
				$output .= '<p>'.sprintf(__('You need to <a href="%s">login</a> or <a href="%s">register</a> to bookmark/favorite this content.','userpro-fav'), $userpro->permalink(0, 'login').'?redirect_to='.$this->get_permalink(), $userpro->permalink(0, 'register')).'</p>';
			}
		}

		return $output;
	}



	/**
		Bookmark: display the widget that allow
		bookmarks
	**/
	function bookmark( $args = array() ){
		global $userpro, $post;
		$defaults = array(
			'width' => userpro_fav_get_option('width'),
			'align' => userpro_fav_get_option('align'),
			'inline' => userpro_fav_get_option('inline'),
			'no_top_margin' => userpro_fav_get_option('no_top_margin'),
			'no_bottom_margin' => userpro_fav_get_option('no_bottom_margin'),
			'pct_gap' => userpro_fav_get_option('pct_gap'),
			'px_gap' => userpro_fav_get_option('px_gap'),
			'widgetized' => userpro_fav_get_option('widgetized'),
			'remove_bookmark' => userpro_fav_get_option('remove_bookmark'),
			'dialog_bookmarked' => userpro_fav_get_option('dialog_bookmarked'),
			'dialog_unbookmarked' => userpro_fav_get_option('dialog_unbookmarked'),
			'default_collection' => userpro_fav_get_option('default_collection'),
			'add_to_collection' => userpro_fav_get_option('add_to_collection'),
			'new_collection' => userpro_fav_get_option('new_collection'),
			'new_collection_placeholder' => userpro_fav_get_option('new_collection_placeholder'),
			'add_new_collection' => userpro_fav_get_option('add_new_collection'),
			'bookmark_category' => userpro_fav_get_option('bookmark_category'),
			'remove_bookmark_category' => userpro_fav_get_option('remove_bookmark_category'),
		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		/* options */
		if (strstr($width, 'px')) { $px = 'px'; } else { $px = '%'; }

		if ($px == '%') {
			$btn_width = 50 - $pct_gap . $px;
		} else {
			$width = str_replace($px, '', $width);
			$btn_width = ($width / 2 ) - $px_gap . $px;
		}
		if ($widgetized == 1){
			$btn_width = '100%';
		}

		/* output */
		$output = '';

		// logged in
		if (userpro_is_logged_in()){

		if (isset($post->ID)){
			$post_id = $post->ID;
			$terms=wp_get_post_categories($post->ID);
			$category_id=null;
			if(is_array($terms))
			{
				if(sizeof($terms)===1)
				{
					foreach($terms as $term)
					{
						$category_id=$term;
					}
				}
				elseif(sizeof($terms)>1)
				{
					foreach($terms as $term)
					{
						$category_id.=$term.",";
					}
				}
				else
				{
					$category_id=null;
				}
			}
		} else {
			$post_id = null;
			$category_id=null;
		}

		$output .= '<div class="userpro-bm userpro-bm-nobottommargin-'.$no_bottom_margin.' userpro-bm-notopmargin-'.$no_top_margin.' userpro-bm-inline-'.$inline.' userpro-bm-'.$align.' userpro-bm-widgetized-'.(int)$widgetized.'" style="width:'.$width.' !important;" data-add_new_collection="'.$add_new_collection.'" data-default_collection="'.$default_collection.'" data-new_collection_placeholder="'.$new_collection_placeholder.'" data-dialog_unbookmarked="'.$dialog_unbookmarked.'" data-dialog_bookmarked="'.$dialog_bookmarked.'" data-add_to_collection="'.$add_to_collection.'" data-remove_bookmark="'.$remove_bookmark.'" data-post_id="'.$post_id.'" data-category_id="'.$category_id.'" data-remove_bookmark_category="'.$remove_bookmark_category.'" data-bookmark_category="'.$bookmark_category.'">';


		$output .= '<div class="userpro-bm-inner">';
		if(userpro_fav_get_option('bookmark_hearticon')=='1')
		$output .= '<div><img src="'.userpro_fav_url.'img/heart.png" title="This post is bookmarked by '.get_post_meta(get_the_ID() , 'post_bookmark_count' ,true).' users." /> <span class="userpro-bm-count">'.get_post_meta(get_the_ID() , 'post_bookmark_count' ,true).'</span></div>';
			/* collections list */
		$output .= '<div class="userpro-bm-list">';
		$output .= '<select class="chosen-select-collections" name="userpro_bm_collection" id="userpro_bm_collection" data-placeholder="">';
		$output .= $this->collection_options( $default_collection, $post_id );
		$output .= '</select>';
		$output .= '</div>';

		/* action buttons */
		$output .= '<div class="userpro-bm-act">';

		if ($this->bookmarked($post_id)) {
			$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($post_id).'" />';
			$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary bookmarked" data-action="bookmark">'.$remove_bookmark.'</a></div>';
		} else {
			$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary unbookmarked" data-action="bookmark">'.$add_to_collection.'</a></div>';
		}
		if(userpro_fav_get_option('display_newcollection')=='1')
		$output .= '<div class="userpro-bm-btn-contain bm-right" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn secondary" data-action="newcollection">'.$new_collection.'</a></div>';
		if($category_id!=null && userpro_fav_get_option('userpro_category_bookmark')){
			if(strrchr($category_id,","))
			{
				foreach($terms as $term_id)
				{
					if($this->bookmarked_category($term_id)){
						$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($term_id).'" />';
						$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary bookmarked_category" data-action="bookmarkcategory" data-category="'.$term_id.'">'.$remove_bookmark_category."-".get_cat_name( $term_id ).'</a></div><div class="userpro-clear"></div>';
					}
					else
					{
						$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary unbookmarked_category" data-action="bookmarkcategory" data-category="'.$term_id.'">'.$bookmark_category."-".get_cat_name( $term_id ).'</a></div><div class="userpro-clear"></div>';
					}

				}
			}
			else{
				if($this->bookmarked_category($category_id)){
					$output .= '<input type="hidden" name="collection_id" id="collection_id" value="'.$this->collection_id($category_id).'" />';
					$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary bookmarked_category" data-action="bookmarkcategory">'.$remove_bookmark_category.'</a></div>';
				}
				else
				{
					$output .= '<div class="userpro-bm-btn-contain" style="width:'.$btn_width.' !important;"><a href="#" class="userpro-bm-btn primary unbookmarked_category" data-action="bookmarkcategory">'.$bookmark_category.'</a></div>';
				}
			}

		}
		$output .= '</div><div class="userpro-clear"></div>';

		$output .= '</div>';
		$output .= '</div>';

		if (!$inline) {
			$output .= '<div class="userpro-clear"></div>';
		}

		// guest
		} else {
			if(userpro_fav_get_option('display_loginregister_popup') == 1){
				$output .= '<p>'.sprintf(__('You need to <a href="#" class="popup-login" data-login_redirect="%s">Login</a> or <a href="#" class="popup-register" data-register_redirect="%s">Register</a> to bookmark/favorite this content.','userpro-fav'),$this->get_permalink(),$this->get_permalink()).'</p>';
			}
			else{
				$output .= '<p>'.sprintf(__('You need to <a href="%s">login</a> or <a href="%s">register</a> to bookmark/favorite this content.','userpro-fav'), $userpro->permalink(0, 'login').'?redirect_to='.$this->get_permalink(), $userpro->permalink(0, 'register')).'</p>';
			}
		}

		return $output;
	}

	/*
	 * Display widget that shows top 10 bookmarks
	 */

	function top_bookmarks($args=array()){
		global $wpdb,$userpro;
		$query = $wpdb->prepare("SELECT  DISTINCT post_id,meta_value FROM $wpdb->postmeta wppm inner join $wpdb->posts wpp on post_id=wpp.id WHERE (wppm.meta_key=%s and wpp.post_status != %s)order by cast(meta_value as unsigned)  DESC LIMIT 5",'_wpb_post_bookmark_count','trash');
		$posts = $wpdb->get_results($query);
		$posts_count = count($posts);
		$output = '';
		$output .= '<div class="userpro-coll-item">';
		for($i=0;$i<$posts_count;$i++)
		{
			$permalink = get_permalink($posts[$i]->post_id);
			$count = $posts[$i]->meta_value;
			$thumbnail = $userpro->post_thumb($posts[$i]->post_id, 50);
			$title = get_the_title($posts[$i]->post_id);
			$output .= '<div style="padding:0 0 4px"><div class="uci-thumb" style="width:50px"><a href="'.$permalink.'">'.$thumbnail.'</a></div>';
			$output .= '<div class="uci-content">';
			$output .= '<div class="uci-title"><a href="'.$permalink.'">'. $title . '</a><span style="color:#000000;padding:4px">('.$count.')</span></div>';
			$output .= '</div><div class="userpro-clear"></div></div>';
		}
		$output.='</div>';
		return $output;
	}


	/*********************************************Code Added By Vipin for category Bookmarks**************************************************************/
	/* Check if category is bookmarkes or not */
	function bookmarked_category($category_id){
		$user_id = get_current_user_id();
		$bookmark_categories = (array) get_user_meta($user_id, '_userpro_bookmarks_category', true);
		if (isset($bookmark_categories[$category_id])){
			return true;
		}
		return false;
	}

	/* Get category bookmarks */
	function get_category_bookmarks($user_id) {
		return (array)get_user_meta($user_id, '_userpro_bookmarks_category', true);
	}

	/* Count category bookmarks */
	function category_bookmarks_count($user_id) {
		$bookmarks = (array)get_user_meta($user_id, '_userpro_bookmarks_category', true);
		unset($bookmarks[0]);
		if (!empty($bookmarks) ){
			return count($bookmarks);
		} else {
			return 0;
		}
	}

	function category_collection_id($category_id){
		$user_id = get_current_user_id();
		$bookmark_categories = (array) get_user_meta($user_id, '_userpro_bookmarks_category', true);
		if (isset($bookmark_categories[$category_id])){
			return $bookmark_categories[$category_id];
		}
	}

	function userpro_bookmark_count($columns) {
		$columns['up_bookmark_count'] = 'Bookmark Count';
		return $columns;
	}

	function userpro_manage_sortable_columns( $sortable_columns ) {
		 $sortable_columns[ 'up_bookmark_count' ] = 'up_bookmark_counts';
		 return $sortable_columns;
	}

	function manage_wp_posts_be_qe_pre_get_posts( $query ) {
	 if( ! is_admin() )
			 return;

	 $orderby = $query->get( 'orderby');
	 if( 'up_bookmark_counts' == $orderby ) {
			 $query->set('meta_key','post_bookmark_count');
			 $query->set('orderby','meta_value meta_value_num');
	 }

}

	function userpro_show_bookmark_count($name) {
		global $post;
		switch ($name) {
			case 'up_bookmark_count':
				$bookmark_count = get_post_meta($post->ID, 'post_bookmark_count', true);
				if( !empty($bookmark_count) ){
					echo intval($bookmark_count);
				}else{
					add_post_meta($post->ID , 'post_bookmark_count' , 0);
					echo intval(0);
				}
		}
	}




	/*********************************************Code End***********************************************************************************************/

}

$userpro_fav = new userpro_fav_api();

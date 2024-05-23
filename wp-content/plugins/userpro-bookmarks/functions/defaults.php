<?php

	/* get a global option */
	function userpro_fav_get_option( $option ) {
		$userpro_default_options = userpro_fav_default_options();
		$settings = get_option('userpro_fav');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					return $userpro_default_options[$option];
				}
				break;
	
		}
	}
	
	/* set a global option */
	function userpro_fav_set_option($option, $newvalue){
		$settings = get_option('userpro_fav');
		$settings[$option] = $newvalue;
		update_option('userpro_fav', $settings);
	}
	
	/* default options */
	function userpro_fav_default_options(){
		$array['bookmark_widget_type'] = '0';
		$array['width'] = '300px';
		$array['align'] = 'left';
		$array['inline'] = 0;
		$array['no_top_margin'] = 0;
		$array['no_bottom_margin'] = 0;
		$array['pct_gap'] = 5;
		$array['px_gap'] = 20;
		$array['widgetized'] = 0;
		
		$array['display_newcollection'] = 1;
		$array['remove_bookmark'] = __('Remove Bookmark','userpro-fav');
		$array['dialog_bookmarked'] = __('Thanks for bookmarking this content!','userpro-fav');
		$array['dialog_unbookmarked'] = __('This content is no longer in your bookmarks.','userpro-fav');
		$array['default_collection'] = __('Default Collection','userpro-fav');
		$array['add_to_collection'] = __('Add to Collection','userpro-fav');
		$array['new_collection'] = __('New Collection','userpro-fav');
		$array['new_collection_placeholder'] =  __('Enter collection name...','userpro-fav');
		$array['add_new_collection'] = __('Add New Collection','userpro-fav');
		$array['bookmark_category'] = __('Bookmark Category','userpro-fav');
		$array['remove_bookmark_category'] = __('Remove Category Bookmark','userpro-fav');		
		$array['auto_bookmark'] = 0;
		$array['include_post_types'] = 'post';
		$array['exclude_ids'] = '';
		$array['userpro_show_users_avatar'] = 0;
		$array['userpro_category_bookmark'] = 0;
		$array['display_loginregister_popup'] = 0;
		$array['allow_multiple_bookmark'] = 0;
		$array['bookmark_hearticon'] = 1;
		$array['userpro_show_bookmarks']=0;
		$array['userpro_show_publicbookmark_guest']=0;
		$array['upb_new_collection_limit']='10';
		$array['upb_bookmarks_limit']='10';
		$array['display_socialbutton']=0;
		
		
		$array["bookmark_envato_purchase_code"] = '';
		return apply_filters('userpro_fav_default_options_array', $array);
	}

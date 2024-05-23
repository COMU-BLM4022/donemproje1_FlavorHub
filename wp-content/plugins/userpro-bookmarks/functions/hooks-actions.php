<?php

add_filter('updb_default_options_array','userpro_bookmark_in_dashboard','11','1');
function userpro_bookmark_in_dashboard($array)
{
	
	$template_path= userpro_fav_path.'templates/';
	$olddata=$array['updb_available_widgets'];
	$newdata= array ('bookmarks'=>array('title'=>'Bookmarks', 'template_path'=>$template_path ));	
    	$array['updb_available_widgets']=   array_merge($olddata,$newdata);

	$oldunsetwidgets=$array['updb_unused_widgets'];
	$newunsetwidgets= array('bookmarks');
	$array['updb_unused_widgets']= array_merge($oldunsetwidgets,$newunsetwidgets);

	return $array;
}


/*star addeb by Yogesh B for adding share button to bookmar*/
function userpro_bookmark_sharebutton($url)
	{

	
	if(userpro_fav_get_option('display_socialbutton')=='1')
	{						
	$html='';
		
 		$html.='<div class="a2a_kit a2a_default_style" data-a2a-url="'.$url.'">';
	  $html.='<a class="a2a_button_facebook"></a>';
	  $html.='<a class="a2a_button_twitter"></a>';
	   $html.='<a class="a2a_button_google_plus"></a>';
	$html.='<a class="a2a_button_linkedin"></a>';
	
	$html.="</div>";
	

	return $html;
	}
}

/*End for adding share button to bookmar*/


	/* Enqueue Scripts */
	add_action('wp_enqueue_scripts', 'userpro_fav_enqueue_scripts', 99);
	function userpro_fav_enqueue_scripts(){
		
		global $userpro;
		$userpro->up_enqueue_scripts_styles();
		wp_register_style('userpro_fav', userpro_fav_url . 'css/userpro-bookmarks.css');
		wp_enqueue_style('userpro_fav');
		
		wp_register_style('userpro_fav_list', userpro_fav_url . 'css/userpro-collections.css');
		wp_enqueue_style('userpro_fav_list');
		
		wp_register_script('userpro_fav', userpro_fav_url . 'scripts/userpro-bookmarks.js');
		wp_enqueue_script('userpro_fav');
		wp_register_script('userpro_m_share', userpro_fav_url . 'scripts/sharebutton.js');
		wp_enqueue_script('userpro_m_share');
		
		
		
	}
	
	add_action('admin_enqueue_scripts','bookmarkenqueue_admin_scripts_styles');
	function bookmarkenqueue_admin_scripts_styles(){
		wp_enqueue_style( 'upb-admin-css', userpro_fav_url.'admin/assets/css/bookmark-admin.css' );
	}
	
	/* Add the bookmark widget to content */
	add_action('the_content', 'userpro_fav_bookmark_content', 100);
	function userpro_fav_bookmark_content($content){
		global $post, $userpro_fav,$userpro;
		if (userpro_fav_get_option('auto_bookmark')) {
		
			// hard excluded by post type
			if (userpro_fav_get_option('include_post_types')){
				if (is_array( userpro_fav_get_option('include_post_types') ) && !in_array( get_post_type(), userpro_fav_get_option('include_post_types')))
					return $content;
			}
			
			// soft excluded by post id
			if (userpro_fav_get_option('exclude_ids')){
				$array = explode(',', userpro_fav_get_option('exclude_ids'));
				if (in_array($post->ID, $array))
					return $content;
			}
			if(userpro_fav_get_option('bookmark_widget_type')=='0')
			{	
				$content .= $userpro_fav->bookmark();
			}
			else
			{   
				if(userpro_fav_get_option('bookmark_widget_type')=='1'){
					if ($userpro_fav->bookmarked($post->ID))
					{
						$content.='<a class="userpro-button secondary addedbookmark" id='.$post->ID.' onclick="userpro_profile_bookmark_popup('.$post->ID.')" href="#">Bookmarked</a>';
					}
					else
					{
						$content.='<a class="userpro-button secondary  unbookmark" id='.$post->ID.' onclick="userpro_profile_bookmark_popup('.$post->ID.')" href="#">Bookmark Me</a>';
					}
				}
				elseif(userpro_fav_get_option('bookmark_widget_type')=='2'){
					if ($userpro_fav->bookmarked($post->ID))
					{
						$content.='<div class="upb-tooltip"><i class="fa fa-heart" id="bookmarked" style="color:#F55252;font-size:2em;cursor:pointer;" id='.$post->ID.' onclick="userpro_bookmark_icon('.$post->ID.',this)"></i><span class="upb-tooltiptext" style="width:230px;">'.__("Click here to remove this bookmark","userpro-fav").'</span></div>';
					}
					else
					{
						$content.='<div class="upb-tooltip"><i class="fa fa-heart-o" id="unbookmarked" style="font-size:2em;cursor:pointer;" id='.$post->ID.' onclick="userpro_bookmark_icon('.$post->ID.',this)"></i><span class="upb-tooltiptext" style="width:180px;">'.__("Click here to bookmark this","userpro-fav").'</span></div>';
					}
				}
			}

			
		}
		return $content;
	}

	add_action('save_post','update_bookmark_status');
	function update_bookmark_status($post_id){
		global $userpro_fav;
		if(isset($post_id))
		{
			if ( !wp_is_post_revision( $post_id ) )
			{
				$categories=wp_get_post_categories($post_id);
		$user_id=get_current_user_id();
				if(!$userpro_fav->bookmarked($post_id))
				{
		foreach($categories as $category)
		{
			if($userpro_fav->bookmarked_category($category))
			{
				$collections = $userpro_fav->get_collections( $user_id );
				$bookmarks = $userpro_fav->get_bookmarks( $user_id );
				$collection_id=$userpro_fav->category_collection_id($category);
		
				/* add collection (post id relation) */
				if (!isset($collections[$collection_id])){
					$collections[$collection_id] = array();
				}
							$collections[$collection_id][$post_id] = 1;
		
				/* add bookmark with collection id */
							if (!isset($bookmarks[$post_id])){
								$bookmarks[$post_id] = $collection_id;
				} else {
								$prev_collection_id = $bookmarks[$post_id];
								unset($collections[$prev_collection_id][$post_id]); // remove from prev collection
								$bookmarks[$post_id] = $collection_id; // update collection
				}
		
				$output['collection_id'] = $collection_id; // update active collection
				
				update_user_meta($user_id, '_userpro_collections', $collections);
				update_user_meta($user_id, '_userpro_bookmarks', $bookmarks);
			}
		}
	}
			}
		}
	}
	
	add_filter( 'the_content', 'userpro_bookmark_below_post', 1001 );
	function userpro_bookmark_below_post( $content ) {
		if(userpro_fav_get_option('userpro_show_users_avatar')) {
			$avt_list = '';
			$post_id = get_the_ID();
			$bookmarked_by = get_post_meta($post_id , 'userpro_bookmarked_by' ,true);
			if(isset($bookmarked_by) && is_array($bookmarked_by))
			{			
				$bookmarked_by = array_unique($bookmarked_by);
				foreach ($bookmarked_by as $user) {
				$avt_list .= get_avatar($user , 30);
				}
				return $content.'<style>.bookmarked-avatar img{margin: 3px;}</style><div class="bookmarked-avatar"><h3>Bookmarked By</h3>'.$avt_list;		
			}
		}
		
		return $content;
	}

	add_action('userpro_social_buttons' , 'userpro_bookmark_display_bookmarks');
	function userpro_bookmark_display_bookmarks($user_id) {
		$allowguest=userpro_fav_get_option('userpro_show_publicbookmark_guest');
		if(userpro_fav_get_option('userpro_show_bookmarks') && is_user_logged_in() ||userpro_fav_get_option('userpro_show_bookmarks') && $allowguest=='1')
		echo '<a class="userpro-button secondary" onclick="userpro_profile_bookmark_list('.$user_id.')" href="#">Bookmarks</a>';
	} 

<?php

	/* Registers and display the shortcode */
	add_shortcode('userpro_bookmark', 'userpro_bookmark' );
	function userpro_bookmark( $args=array() ) {
		global $userpro_fav;

		/* arguments */
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
		
		return $userpro_fav->bookmark( $args );
	
	}
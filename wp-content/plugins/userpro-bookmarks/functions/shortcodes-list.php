<?php

	/* Registers and display the shortcode */
	add_shortcode('userpro_bookmarklist', 'userpro_bookmarklist' );
	function userpro_bookmarklist( $args=array() ) {
		global $userpro_fav;

		/* arguments */
		$defaults = array(

		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		if( isset($args['layout']) && $args['layout'] == 'grid' ){
			include_once (userpro_fav_path.'templates/template-grid-layout.php');
		}else{
			return $userpro_fav->bookmarks( $args );
		}
	}

	add_shortcode('userpro_publicbookmark', 'userpro_publicbookmark' );
	function userpro_publicbookmark( $args=array() ) {
		global $userpro_fav;
		
		/* arguments */
		$defaults = array(

		);
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );
		
		return $userpro_fav->publicbookmar( $args );
	
	}

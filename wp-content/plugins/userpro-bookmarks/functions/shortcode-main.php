<?php

	/* Filter shortcodes args */
	add_filter('userpro_shortcode_args', 'userpro_fav_shortcodes_arg', 99);
	function userpro_fav_shortcodes_arg($args){

		return $args;
	}

	/* Add extension shortcodes */
	add_action('userpro_custom_template_hook', 'userpro_fav_shortcodes', 99 );
	function userpro_fav_shortcodes($args) {
		global $userpro, $userpro_fav;
	}

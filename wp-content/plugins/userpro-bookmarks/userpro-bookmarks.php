<?php
/*
Plugin Name: WordPress User Bookmarks plugin for UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: Allow users to bookmark/favorite any content and organize collections.
Version: 4.0.2
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

define('userpro_fav_url',plugin_dir_url(__FILE__ ));
define('userpro_fav_path',plugin_dir_path(__FILE__ ));

	/* init */
	function userpro_fav_init() {
		load_plugin_textdomain('userpro-fav', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	add_action('init', 'userpro_fav_init');

	/* functions */
	foreach (glob(userpro_fav_path . 'functions/*.php') as $filename) { require_once $filename; }

	/* administration */
	if (is_admin()){
		foreach (glob(userpro_fav_path . 'admin/*.php') as $filename) { include $filename; }
	}

	//require_once(dirname(__FILE__)."/admin/bookmark-updates-plugin.php");
	//new WPUpdatesPluginUpdater_1130( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));

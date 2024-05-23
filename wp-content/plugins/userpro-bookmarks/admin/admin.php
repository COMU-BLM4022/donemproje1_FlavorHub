<?php

class userpro_fav_admin {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userpro';
		$this->subslug = 'userpro-bookmarks';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( userpro_fav_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('userpro_admin_menu_hook', array(&$this, 'add_menu'), 9);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
	}
	
	function admin_init() {
	
		$this->tabs = array(
			'settings' => __('User Bookmarks','userpro-fav'),
			'reset' => __('Reset','userpro-fav'),
			'bookmark-collections' => __('Bookmark Collections','userpro-fav'),
			'licensing'=>__( 'Licensing', 'userpro-fav')
		);
		$this->default_tab = 'settings';
		
		$this->options = get_option('userpro_fav');
		if (!get_option('userpro_fav')) {
			update_option('userpro_fav', userpro_fav_default_options() );
		}
		
	}
	
	function admin_head(){

	}

	function add_styles(){
		wp_register_script('userpro_fav_admin_js', userpro_fav_url . 'admin/assets/js/admin-bookmarks.js');
		wp_enqueue_script('userpro_fav_admin_js');
	}
	
	function add_menu() {
		add_submenu_page( 'userpro', __('User Bookmarks','userpro-fav'), __('User Bookmarks','userpro-fav'), 'manage_options', 'userpro-bookmarks', array(&$this, 'admin_page') );
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->subslug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			require_once userpro_fav_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	function save() {
	
		$this->options['exclude_post_types'] = '';
		
		/* other post fields */
		foreach($_POST as $key => $value) {
			if ($key != 'submit' && $key != 'up_fav_license_verify') {
				if (!is_array($_POST[$key])) {
					$this->options[$key] = esc_attr($_POST[$key]);
				} else {
					$this->options[$key] = $_POST[$key];
				}
			}
		}
		if( isset( $_POST['up_fav_license_verify'] ) ){
			$code = $_POST['bookmark_envato_purchase_code'];
			global $userpro;

			if ($code == ''){
				echo '<div class="error"><p><strong>'.__('Please enter a purchase code.','userpro').'</strong></p></div>';
			} else {
				if ( $userpro->verify_purchase($code, '13z89fdcmr2ia646kphzg3bbz0jdpdja', 'DeluxeThemes', '6455170') ){
					echo '<div class="updated fade"><p><strong>'.__('Thanks for activating UserPro Bookmarks Addon!','userpro-fav').'</strong></p></div>';
				} else {
					echo '<div class="error"><p><strong>'.__('You have entered an invalid purchase code or the Envato API could be down at the moment.','userpro-fav').'</strong></p></div>';
				}
			}
		}
		update_option('userpro_fav', $this->options);
		echo '<div class="updated"><p><strong>'.__('Settings saved.','userpro-fav').'</strong></p></div>';
	}

	function reset() {
		update_option('userpro_fav', userpro_fav_default_options() );
		$this->options = array_merge( $this->options, userpro_fav_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userpro-fav').'</strong></p></div>';
	}
	
	function do_action()
	{	global $userpro_fav;
		if ($_GET['bookmarklist_act'] == 'clear_bookmarklist'){
			$userpro_fav->clear_bookmarklist();
			
			echo '<div class="updated"><p><strong>'.sprintf(__('Bookmark list has been reset.','userpro-fav')).'</strong></p></div>';
		}

	}
	function admin_page() {

		if (isset($_POST['submit']) || isset($_POST['up_fav_license_verify']) ) {
			$this->save();
		}

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		if (isset($_POST['rebuild-pages'])) {
			$this->rebuild_pages();
		}
		if (isset($_GET['bookmarklist_act'])){
			$this->do_action();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
			
			<?php userpro_admin_bar(); ?>
			
			<h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->get_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$userpro_fav_admin = new userpro_fav_admin();

<?php

add_action( 'widgets_init', 'userpro_bookmark_widget' );


function userpro_bookmark_widget() {
	register_widget( 'BOOKMARK_WIDGET' );
	register_widget( 'TOP_BOOKMARKED' );
}

class BOOKMARK_WIDGET extends WP_Widget {

	function __construct()  {
		$widget_ops = array( 'classname' => 'userpro_bookmark', 'description' => __('Show the bookmark widget in your sidebar', 'userpro-fav') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'userpro_bookmark' );
		
		parent::__construct( 'userpro_bookmark', __('UserPro - Bookmark Widget', 'userpro-fav'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		global $userpro_fav;
		
		$before_title = $args['before_title'];
		$after_title = $args['after_title'];
			// hard excluded by post type
			if (userpro_fav_get_option('include_post_types')){
				if (is_array( userpro_fav_get_option('include_post_types') ) && !in_array( get_post_type(), userpro_fav_get_option('include_post_types')))
					return false;
			}
			
			// soft excluded by post id
			if (userpro_fav_get_option('exclude_ids')){
				$array = explode(',', userpro_fav_get_option('exclude_ids'));
				if (in_array($post->ID, $array))
					return false;
			}

			//Our variables
			$title = apply_filters('widget_title', $instance['title'] );

			echo $args['before_widget'];
			if ( $title )
				echo $before_title . $title . $after_title;
			echo $userpro_fav->bookmark('width=100%&widgetized=1&no_top_margin=1&no_bottom_margin=1');
			echo $args['after_widget'];
			
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Bookmark Me', 'userpro-fav') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'userpro-fav'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>

	<?php
	}
}


class TOP_BOOKMARKED extends WP_Widget {

	function __construct()  {
		$widget_ops = array( 'classname' => 'userpro_top_bookmarks', 'description' => __('Show top 5 bookmarks', 'userpro-fav') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'userpro_top_bookmarks' );

		parent::__construct( 'userpro_top_bookmarks', __('UserPro - Top 5 bookmarks', 'userpro-fav'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		global $userpro_fav;

		$before_title = $args['before_title'];
		$after_title = $args['after_title'];
		// hard excluded by post type
		
		$title = apply_filters('widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( $title )
			echo $before_title . $title . $after_title;
		echo $userpro_fav->top_bookmarks( $args );
		echo $args['after_widget'];
			
	}

	//Update the widget

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}


	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Top 5 bookmarks', 'userpro-fav') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'userpro-fav'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>

	<?php
	}
}
?>

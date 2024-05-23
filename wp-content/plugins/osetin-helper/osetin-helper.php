<?php
/**
 * Plugin Name: Osetin Helper
 * Description: Adds Recipe support to use within a theme
 * Version: 2.2.2
 * Author: PinSupreme
 * Author URI: http://pinsupreme.com
 * Text Domain: osetin-helper
 * Domain Path: /languages
 */



// Recipe post type
add_action( 'init', 'osetin_create_review_post_type' );
function osetin_create_review_post_type() {
  $review_slug = __('review', 'osetin-helper');
  register_post_type( 'osetin_review',
    array(
      'labels' => array(
        'name' => __( 'Reviews', 'osetin-helper' ),
        'singular_name' => __( 'Review', 'osetin-helper' ),
        'add_new' => __('Add Review', 'osetin-helper'),
        'add_new_item' => __('Add New Review', 'osetin-helper'),
        'edit_item' => __('Edit Review', 'osetin-helper'),
        'new_item' => __('New Review', 'osetin-helper'),
        'view_item' => __('View Review', 'osetin-helper'),
        'search_items' => __('Search Reviews', 'osetin-helper'),
        'not_found' => __('No Reviews Found', 'osetin-helper'),
      ),
      'rewrite' => array( 'slug' => $review_slug ),
      'taxonomies' => array(),
      'supports' => array( 'author' ),
      'menu_position' => 8,
      'public' => true,
      'has_archive' => true,
    )
  );

}

add_filter('manage_osetin_review_posts_columns', 'osetin_columns_osetin_review_head');
add_action('manage_osetin_review_posts_custom_column', 'osetin_columns_osetin_review_content', 10, 2);

// ADD TWO NEW COLUMNS
function osetin_columns_osetin_review_head($defaults) {
    $defaults['recipe_title']  = 'Recipe Reviewed';
    $defaults['review_text'] = 'Review';
    $defaults['rating']  = 'Rating';
    $defaults['review_status'] = 'Status';
    return $defaults;
}
 
function osetin_columns_osetin_review_content($column_name, $post_ID) {
    if ($column_name == 'recipe_title') {
      echo '<a href="'.get_edit_post_link($post_ID).'">'.get_the_title(osetin_get_field('recipe', $post_ID)).'</a>';
    }
    if ($column_name == 'review_text') {
      echo osetin_get_field('body', $post_ID);
    }
    if ($column_name == 'review_status') {
      echo get_post_status($post_ID);
    }
    if ($column_name == 'rating') {
      echo osetin_get_field('rating', $post_ID);
    }
}

add_filter('manage_osetin_review_posts_columns', 'osetin_columns_remove_review_title');
 
// REMOVE DEFAULT CATEGORY COLUMN
function osetin_columns_remove_review_title($defaults) {
    // to get defaults column names:
    // print_r($defaults);
    unset($defaults['date']);
    unset($defaults['author']);
    unset($defaults['title']);
    unset($defaults['tptn_daily']);
    unset($defaults['tptn_total']);
    unset($defaults['tptn_both']);
    return $defaults;
}


// Recipe post type
add_action( 'init', 'osetin_create_recipe_post_type' );
function osetin_create_recipe_post_type() {
  $recipe_slug_default = __('recipe', 'osetin-helper');
  if(function_exists('osetin_get_field')){
    $recipe_slug = osetin_get_field('recipe_slug', 'option', $recipe_slug_default);
  }else{
    $recipe_slug = $recipe_slug_default;
  }
  register_post_type( 'osetin_recipe',
    array(
      'labels' => array(
        'name' => __( 'Recipes', 'osetin-helper' ),
        'singular_name' => __( 'Recipe', 'osetin-helper' ),
        'add_new' => __('Add Recipe', 'osetin-helper'),
        'add_new_item' => __('Add New Recipe', 'osetin-helper'),
        'edit_item' => __('Edit Recipe', 'osetin-helper'),
        'new_item' => __('New Recipe', 'osetin-helper'),
        'view_item' => __('View Recipe', 'osetin-helper'),
        'search_items' => __('Search Recipes', 'osetin-helper'),
        'not_found' => __('No Recipes Found', 'osetin-helper'),
      ),
      'rewrite' => array( 'slug' => $recipe_slug ),
      'taxonomies' => array('category', 'post_tag'),
      'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'post-formats' ),
      'menu_position' => 5,
      'public' => true,
      'has_archive' => true,
    )
  );

}

add_filter( 'pre_get_posts', 'my_get_posts' );

function my_get_posts( $query ) {

  if ( ( is_home() && $query->is_main_query() ) || is_feed() ){
    $query->set( 'post_type', array( 'post', 'osetin_recipe' ) );
  }

  return $query;
}


function osetin_shortcode_social_links_func( $atts ) {
    $atts = shortcode_atts( array(
        'foo' => 'something',
        'bar' => 'something else',
    ), $atts );

    $output = '';

    if( osetin_have_rows('social_links', 'option') ){
      $output.= '<ul class="shortcode-social-links">';

      // loop through the rows of data
      while ( osetin_have_rows('social_links', 'option') ) : the_row();
          $output.= '<li><a href="'.esc_url(get_sub_field('social_page_url')).'" target="_blank"><i class="os-icon os-icon-'.esc_attr(get_sub_field('social_network')).'"></i></a></li>';
      endwhile;

      $output.= '</ul>';

    }
    return $output;
}
add_shortcode( 'osetin_social_links', 'osetin_shortcode_social_links_func' );




function osetin_shortcode_about_author_func( $atts, $content = "" ) {
    $atts = shortcode_atts( array(
        'title' => 'About Me',
        'avatar_url' => false
    ), $atts );

    $output = '';
    $output.= '<div class="shortcode-about-author">';
    $output.= '<h3 class="saa-header header-ribbon">'.esc_attr($atts['title']).'</h3>';
    $output.= '<div class="saa-avatar"><img src="'.$atts['avatar_url'].'" alt="'.esc_attr($atts['title']).'"/></div>';
    $output.= '<div class="saa-content">'.$content.'</div>';
    $output.= '</div>';
    return $output;
}
add_shortcode( 'osetin_about_author', 'osetin_shortcode_about_author_func' );





// CUISINES ICONS SHORTCODE

function osetin_shortcode_cuisines_icons_func( $atts, $content = "" ) {
    $atts = shortcode_atts( array(
      'limit' => false,
      'include_child_categories' => false,
      'specific_ids' => false
    ), $atts );

    $args = array( 'orderby' => 'name', 'order' => 'ASC' );
    if(($atts['include_child_categories'] == false) && ($atts['specific_ids'] == false)) $args['parent'] = 0;
    if($atts['limit']) $args['number'] = $atts['limit'];
    if($atts['specific_ids']) $args['include'] = $atts['specific_ids'];

    $cuisines = get_terms('recipe_cuisine', $args);
    
    $output = '';
    $output.= '<div class="shortcode-categories-icons">';
    $output.= '<table>';
    $output.= '<tr>';
    $counter = 0;
    foreach($cuisines as $cuisine) { 
      $cuisine_icon_url = osetin_get_field('category_icon', "recipe_cuisine_{$cuisine->term_id}");
      if(empty($cuisine_icon_url)) $cuisine_icon_url = plugin_dir_url( __FILE__ ) . 'assets/img/placeholder-category.png';
      if((($counter % 2) == 0) && ($counter > 0)) $output.= '</tr><tr>';
      $output.= '<td>';
      $output.= '<div class="sci-media"><a href="'.get_term_link($cuisine).'"><img src="'.$cuisine_icon_url.'" alt="'.esc_attr($cuisine->name).'"/></a></div>';
      $output.= '<div class="sci-title"><h3><a href="'.get_term_link($cuisine).'">'.$cuisine->name.'</a></h3></div>';
      $output.= '</td>';
      $counter++;
    }
    if(($counter % 2) != 0) $output .= '<td></td>';
    $output.= '</tr>';

    $output.= '</table>';
    $output.= '</div>';
    return $output;
}
add_shortcode( 'osetin_cuisines_icons', 'osetin_shortcode_cuisines_icons_func' );







// CATEGORIES ICONS SHORTCODE

function osetin_shortcode_categories_icons_func( $atts, $content = "" ) {
    $atts = shortcode_atts( array(
      'limit' => false,
      'include_child_categories' => false,
      'specific_ids' => false
    ), $atts );

    $args = array( 'orderby' => 'name', 'order' => 'ASC' );
    if(($atts['include_child_categories'] == false) && ($atts['specific_ids'] == false)) $args['parent'] = 0;
    if($atts['limit']) $args['number'] = $atts['limit'];
    if($atts['specific_ids']) $args['include'] = $atts['specific_ids'];

    $categories = get_categories($args);
    
    $output = '';
    $output.= '<div class="shortcode-categories-icons">';
    $output.= '<table>';
    $output.= '<tr>';
    $counter = 0;
    foreach($categories as $category) { 
      $category_icon_url = osetin_get_field('category_icon', "category_{$category->cat_ID}");
      if(empty($category_icon_url)) $category_icon_url = plugin_dir_url( __FILE__ ) . 'assets/img/placeholder-category.png';
      if((($counter % 2) == 0) && ($counter > 0)) $output.= '</tr><tr>';
      $output.= '<td>';
      $output.= '<div class="sci-media"><a href="'.get_category_link($category->cat_ID).'"><img src="'.$category_icon_url.'" alt="'.esc_attr($category->name).'"/></a></div>';
      $output.= '<div class="sci-title"><h3><a href="'.get_category_link($category->cat_ID).'">'.$category->name.'</a></h3></div>';
      $output.= '</td>';
      $counter++;
    }
    if(($counter % 2) != 0) $output .= '<td></td>';
    $output.= '</tr>';

    $output.= '</table>';
    $output.= '</div>';
    return $output;
}
add_shortcode( 'osetin_categories_icons', 'osetin_shortcode_categories_icons_func' );









add_action( 'init', 'osetin_create_recipe_special_taxonomy' );

function osetin_create_recipe_special_taxonomy() {
  $feature_default_slug = __('feature', 'osetin-helper');
  $ingredient_default_slug = __('ingredient', 'osetin-helper');
  $cuisine_default_slug = __('cuisine', 'osetin-helper');
  
  if(function_exists('osetin_get_field')){
    $feature_slug = osetin_get_field('feature_slug', 'option', $feature_default_slug);
    $ingredient_slug = osetin_get_field('ingredient_slug', 'option', $ingredient_default_slug);
    $cuisine_slug = osetin_get_field('cuisine_slug', 'option', $cuisine_default_slug);
  }else{
    $feature_slug = $feature_default_slug;
    $ingredient_slug = $ingredient_default_slug;
    $cuisine_slug = $cuisine_default_slug;
  }

  // FEATURE
  register_taxonomy(
    'recipe_feature',
    'osetin_recipe',
    array(
      'labels' => array(
        'name'                  => __('Features', 'osetin-helper'),
        'singular_name'         => __('Feature', 'osetin-helper'),
        'all_items'             => __('All Features', 'osetin-helper'),
        'edit_item'             => __('Edit Feature', 'osetin-helper'),
        'view_item'             => __('View Feature', 'osetin-helper'),
        'update_item'           => __('Update Feature', 'osetin-helper'),
        'add_new_item'          => __('Add New Feature', 'osetin-helper'),
        'new_item_name'         => __('New Feature Name', 'osetin-helper'),
        'search_items'          => __('Search Features', 'osetin-helper'),
        'popular_items'         => __('Popular Features', 'osetin-helper'),
        'add_or_remove_items'   => __('Add or remove features', 'osetin-helper'),
        'choose_from_most_used' => __('Choose from the most used features', 'osetin-helper')
      ),
      'rewrite' => array( 'slug' => $feature_slug ),
      'hierarchical' => true,
    )
  );

  // INGREDIENTS
  register_taxonomy(
    'recipe_ingredient',
    'osetin_recipe',
    array(
      'labels' => array(
        'name'                  => __('Ingredients', 'osetin-helper'),
        'singular_name'         => __('Ingredient', 'osetin-helper'),
        'all_items'             => __('All Ingredients', 'osetin-helper'),
        'edit_item'             => __('Edit Ingredient', 'osetin-helper'),
        'view_item'             => __('View Ingredient', 'osetin-helper'),
        'update_item'           => __('Update Ingredient', 'osetin-helper'),
        'add_new_item'          => __('Add New Ingredient', 'osetin-helper'),
        'new_item_name'         => __('New Ingredient Name', 'osetin-helper'),
        'search_items'          => __('Search Ingredients', 'osetin-helper'),
        'popular_items'         => __('Popular Ingredients', 'osetin-helper'),
        'add_or_remove_items'   => __('Add or remove ingredients', 'osetin-helper'),
        'choose_from_most_used' => __('Choose from the most used ingredients', 'osetin-helper')
      ),
      'rewrite' => array( 'slug' => $ingredient_slug ),
      'hierarchical' => false,
    )
  );

  // CUISINES
  register_taxonomy(
    'recipe_cuisine',
    'osetin_recipe',
    array(
      'labels' => array(
        'name'                  => __('Cuisines', 'osetin-helper'),
        'singular_name'         => __('Cuisine', 'osetin-helper'),
        'all_items'             => __('All Cuisines', 'osetin-helper'),
        'edit_item'             => __('Edit Cuisine', 'osetin-helper'),
        'view_item'             => __('View Cuisine', 'osetin-helper'),
        'update_item'           => __('Update Cuisine', 'osetin-helper'),
        'add_new_item'          => __('Add New Cuisine', 'osetin-helper'),
        'new_item_name'         => __('New Cuisine Name', 'osetin-helper'),
        'search_items'          => __('Search Cuisines', 'osetin-helper'),
        'popular_items'         => __('Popular Cuisines', 'osetin-helper'),
        'add_or_remove_items'   => __('Add or remove cuisines', 'osetin-helper'),
        'choose_from_most_used' => __('Choose from the most used cuisines')
      ),
      'rewrite' => array( 'slug' => $cuisine_slug ),
      'hierarchical' => true,
    )
  );
}



// WIDGETS

/**
 * Adds Osetin_Cuisines_Widget widget.
 */
class Osetin_Cuisines_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'osetin_cuisines_widget', // Base ID
      __( 'Neptune Cuisines', 'osetin' ), // Name
      array( 'description' => __( 'Cuisines Table Widget', 'osetin' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }
    $limit = osetin_get_field('limit', 'widget_'.$args['widget_id']);
    $include_child_categories = osetin_get_field('include_child_categories', 'widget_'.$args['widget_id']);
    $specific_ids = osetin_get_field('specific_ids', 'widget_'.$args['widget_id'], false, true);
    $attr_string = '';
    if($limit) $attr_string.= ' limit="'.$limit.'"';
    if($include_child_categories) $attr_string.= ' include_child_categories="true"';
    if($specific_ids) $attr_string.= ' specific_ids="'.implode($specific_ids, ',').'"';
    echo do_shortcode('[osetin_cuisines_icons'.$attr_string.']');
    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <?php 
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    return $instance;
  }

} // class Osetin_Categories_Widget

/**
 * Adds Osetin_Categories_Widget widget.
 */
class Osetin_Categories_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'osetin_categories_widget', // Base ID
      __( 'Neptune Categories', 'osetin' ), // Name
      array( 'description' => __( 'Catogories Table Widget', 'osetin' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }
    $limit = osetin_get_field('limit', 'widget_'.$args['widget_id']);
    $include_child_categories = osetin_get_field('include_child_categories', 'widget_'.$args['widget_id']);
    $specific_ids = osetin_get_field('specific_ids', 'widget_'.$args['widget_id'], false, true);
    $attr_string = '';
    if($limit) $attr_string.= ' limit="'.$limit.'"';
    if($include_child_categories) $attr_string.= ' include_child_categories="true"';
    if($specific_ids) $attr_string.= ' specific_ids="'.implode($specific_ids, ',').'"';
    echo do_shortcode('[osetin_categories_icons'.$attr_string.']');
    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <?php 
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    return $instance;
  }

} // class Osetin_Categories_Widget


/**
 * Adds Osetin_Social_Widget widget.
 */
class Osetin_Social_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'osetin_Social_widget', // Base ID
      __( 'Neptune Social Links', 'osetin' ), // Name
      array( 'description' => __( 'Social Links Widget', 'osetin' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];
    if ( ! empty( $instance['title'] ) ) {
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }
    echo do_shortcode('[osetin_social_links]');
    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <?php 
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    return $instance;
  }

} // class Osetin_Social_Widget


/**
 * Adds Osetin_Author_Widget widget.
 */
class Osetin_Author_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'osetin_author_widget', // Base ID
      __( 'Neptune Author Box', 'osetin' ), // Name
      array( 'description' => __( 'Author Box Widget', 'osetin' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance ) {
    echo $args['before_widget'];

    $avatar_url = osetin_get_field('avatar_url', 'widget_'.$args['widget_id']);
    $content = osetin_get_field('author_content', 'widget_'.$args['widget_id']);

    $attr_string = '';
    if($avatar_url) $attr_string.= ' avatar_url="'.$avatar_url.'"';
    if(! empty( $instance['title'] )) $attr_string.= ' title="'.$instance['title'].'"';
    echo do_shortcode('[osetin_about_author'.$attr_string.']'.$content.'[/osetin_about_author]');
    echo $args['after_widget'];
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <?php 
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    return $instance;
  }

} // class Osetin_Author_Widget



// register widgets
function register_osetin_widgets() {
    register_widget( 'Osetin_Cuisines_Widget' );
    register_widget( 'Osetin_Categories_Widget' );
    register_widget( 'Osetin_Author_Widget' );
    register_widget( 'Osetin_Social_Widget' );
}
add_action( 'widgets_init', 'register_osetin_widgets' );
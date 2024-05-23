<?php
/**
 * Plugin Name: Osetin Meal Planner
 * Description: Adds Meal Planning Functionality
 * Version: 1.5
 * Author: PinSupreme
 * Author URI: http://pinsupreme.com
 * Text Domain: osetin-meal-planner
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsetinMealPlanner' ) ) :

/**
 * Main OsetinMealPlanner Class.
 *
 */

final class OsetinMealPlanner {

  /**
   * OsetinMealPlanner version.
   *
   */
  public $version = '1.5';




  /**
   * OsetinMealPlanner Constructor.
   */
  public function __construct() {
    $this->define_constants();
    $this->includes();
    $this->init_hooks();
  }


  /**
   * Define OsetinMealPlanner Constants.
   */
  public function define_constants() {
    $upload_dir = wp_upload_dir();

    $this->define( 'OMP_PLUGIN_FILE', __FILE__ );
    $this->define( 'OMP_ABSPATH', dirname( __FILE__ ) . '/' );
    $this->define( 'OMP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
    $this->define( 'OMP_VERSION', $this->version );
  }


  /**
   * Include required core files used in admin and on the frontend.
   */
  public function includes() {
    include_once( OMP_ABSPATH . 'includes/class-osetin-meal-plan.php' );
  }


  /**
   * Hook into actions and filters.
   */
  public function init_hooks() {
    add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
    add_action( 'init', array( $this, 'init' ), 0 );
    add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts_and_styles' ));

    // Create meal plan action
    add_action( 'wp_ajax_osetin_select_or_create_meal_plan', array( $this, 'select_or_create_meal_plan') );
    add_action( 'wp_ajax_nopriv_osetin_select_or_create_meal_plan', array( $this, 'select_or_create_meal_plan') );

    // Delete meal plan action
    add_action( 'wp_ajax_osetin_ajax_delete_meal_plan', array( $this, 'ajax_delete_meal_plan') );
    add_action( 'wp_ajax_nopriv_osetin_ajax_delete_meal_plan', array( $this, 'ajax_delete_meal_plan') );

    // Load meal plan action
    add_action( 'wp_ajax_osetin_ajax_load_meal_plan', array( $this, 'ajax_load_meal_plan') );
    add_action( 'wp_ajax_nopriv_osetin_ajax_load_meal_plan', array( $this, 'ajax_load_meal_plan') );

    // Load FULL meal plan for print action
    add_action( 'wp_ajax_osetin_ajax_load_full_meal_plan', array( $this, 'ajax_load_full_meal_plan') );
    add_action( 'wp_ajax_nopriv_osetin_ajax_load_full_meal_plan', array( $this, 'ajax_load_full_meal_plan') );

    // Save meal plan action
    add_action( 'wp_ajax_osetin_save_meal_plan', array( $this, 'save_meal_plan') );
    add_action( 'wp_ajax_nopriv_osetin_save_meal_plan', array( $this, 'save_meal_plan') );
  }

  /**
   * Init OsetinMealPlanner when WordPress Initialises.
   */
  public function init() {
    $this->register_post_types();
    $this->register_shortcodes();
  }




  public function setup_environment() {
    if ( ! current_theme_supports( 'post-thumbnails' ) ) {
      add_theme_support( 'post-thumbnails' );
    }
    add_post_type_support( 'osetin_meal_plan', 'thumbnail' );
  }



  /**
   * Define constant if not already set.
   *
   */
  public function define( $name, $value ) {
    if ( ! defined( $name ) ) {
      define( $name, $value );
    }
  }


  /**
   * Get the plugin url.
   * @return string
   */
  public static function plugin_url() {
    return plugin_dir_url( __FILE__ ) ;
  }

  /**
   * Get the plugin path.
   * @return string
   */
  public static function plugin_path() {
    return plugin_dir_path( __FILE__ ) ;
  }





  /**
   * Register shortcodes
   */
  public function register_shortcodes() {
    add_shortcode( 'os_add_to_meal_plan', array($this, 'shortcode_add_to_meal_plan' ));
    add_shortcode( 'os_user_meal_plans', array($this, 'shortcode_list_user_meal_plans' ));
  }

  /**
   * Register core post types.
   */
  public function register_post_types() {
    $meal_plan_slug = _x('meal-plan', 'slug', 'osetin-meal-planner');
    register_post_type( 'osetin_meal_plan',
      array(
        'labels' => array(
          'name' => __( 'Meal Plans', 'osetin-meal-planner' ),
          'singular_name' => __( 'Meal Plan', 'osetin-meal-planner' ),
          'add_new' => __('Add Meal Plan', 'osetin-meal-planner'),
          'add_new_item' => __('Add New Meal Plan', 'osetin-meal-planner'),
          'edit_item' => __('Edit Meal Plan', 'osetin-meal-planner'),
          'new_item' => __('New Meal Plan', 'osetin-meal-planner'),
          'view_item' => __('View Meal Plan', 'osetin-meal-planner'),
          'search_items' => __('Search Meal Plans', 'osetin-meal-planner'),
          'not_found' => __('No Meal Plans Found', 'osetin-meal-planner'),
        ),
        'rewrite' => array( 'slug' => $meal_plan_slug ),
        'taxonomies' => array(),
        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'author' ),
        'menu_position' => 8,
        'public' => true,
        'has_archive' => true,
      )
    );
  }















  /*

   SHORTCODES 

  */

  // [os_add_to_meal_plan]
  public function shortcode_add_to_meal_plan( $atts, $content = "" ) {
      $atts = shortcode_atts( array(
          'caption' => __('Add to Meal Plan', 'osetin-meal-planner')
      ), $atts );
      $nonce = wp_create_nonce("osetin_meal_planner_nonce");

      $output = '';
      $btn_class = '';
      if(!is_user_logged_in()){
        $btn_class.= 'popup-register add-to-mean-plan-btn ';
      }else{
        $btn_class.= 'trigger-add-to-meal-plan add-to-mean-plan-btn';
      }
      $output.= '<a href="#" class="'.$btn_class.'" data-nonce="'.$nonce.'" data-post-id="'.get_the_ID().'"><i class="mp-icon-ui-22"></i><span>'.esc_attr($atts['caption']).'</span></a>';
      return $output;
  }


  // [os_user_meal_plans]
  public function shortcode_list_user_meal_plans( $atts, $content = "" ) {
      $atts = shortcode_atts( array(
          'user_id' => ''
      ), $atts );



      $output = '';
      if(!is_user_logged_in()){
        $output.= __('You have to be logged in to view your meal plans', 'osetin-meal-planner');
        return $output;
      }

      $user_id = get_current_user_id();

      // get meal plans

      $osetin_meal_plans_query = OsetinMealPlan::get_user_meal_plans_query($user_id);

      $output.= '<div class="meal-plans-list" data-wait-label="'.__('Wait...', 'osetin-meal-planner').'" data-delete-label="'.__('Are you sure you want to delet this meal plan?', 'osetin-meal-planner').'">';
      while ( $osetin_meal_plans_query->have_posts() ) : $osetin_meal_plans_query->the_post();

        $meal_plan = new OsetinMealPlan();
        $meal_plan->load_meal_plan($meal_plan_id);

        $allowed_edit = (current_user_can('edit_others_pages') || (get_current_user_id() == $meal_plan_id)) ? 'can-edit' : 'no-edit';

        $output.= '<div class="meal-plan '.$allowed_edit.'" data-meal-plan-id="'.$meal_plan->id.'">
                    <h3>'.$meal_plan->name.'</h3>
                    <div class="meal-plan-actions">
                      <a href="'.get_the_permalink($meal_plan->id).'" class="show-meal-plan"><i class="os-icon mp-icon-ui-07"></i> <span>'.__('View/Edit', 'osetin-meal-planner').'</span></a>
                      <a href="#" class="print-meal-plan" data-meal-plan-id="'.$meal_plan->id.'"><i class="os-icon os-icon-tech-11"></i> <span>'.__('Printable Version', 'osetin-meal-planner').'</span></a>
                      <a href="#" class="delete-meal-plan"><i class="os-icon mp-icon-cross"></i> <span>'.__('Delete', 'osetin-meal-planner').'</span></a>
                    </div>
                  </div>';
      endwhile;
      $output.= '</div>';
      wp_reset_postdata();
      return $output;
  }























  /**
  * Register scripts and styles 
  */
  public function load_scripts_and_styles() {
    wp_enqueue_style( 'osetin-meal-planner-fonts', 'https://fonts.googleapis.com/css?family=Domine:400,700', false, $this->version );
    wp_enqueue_style( 'osetin-meal-planner-icons', $this->plugin_url() . 'assets/css/osetin-meal-planner-icons.css', false, $this->version );

    wp_enqueue_style( 'slick', $this->plugin_url() . 'assets/bower_components/slick-carousel/slick/slick.css', false, $this->version );
    wp_enqueue_style( 'dragula', $this->plugin_url() . 'assets/bower_components/dragula.js/dist/dragula.min.css', false, $this->version );
    wp_enqueue_style( 'chartist', $this->plugin_url() . 'assets/bower_components/chartist/dist/chartist.min.css', false, $this->version );
    wp_enqueue_style( 'chartist-plugin-tooltip', $this->plugin_url() . 'assets/bower_components/chartist-plugin-tooltip/dist/chartist-plugin-tooltip.css', false, $this->version );
    wp_enqueue_style( 'perfect-scrollbar', $this->plugin_url() . 'assets/bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css', false, $this->version );
    wp_enqueue_style( 'osetin-meal-planner', $this->plugin_url() . 'assets/css/osetin-meal-planner.css?version=1.5', false, $this->version );

    wp_enqueue_script( 'chartist', $this->plugin_url() . 'assets/bower_components/chartist/dist/chartist.min.js', array('jquery'), $this->version );
    wp_enqueue_script( 'chartist-plugin-tooltip', $this->plugin_url() . 'assets/bower_components/chartist-plugin-tooltip/dist/chartist-plugin-tooltip.min.js', array('jquery'), $this->version );
    wp_enqueue_script( 'slick', $this->plugin_url() . 'assets/bower_components/slick-carousel/slick/slick.js', array('jquery'), $this->version );
    wp_enqueue_script( 'dragula', $this->plugin_url() . 'assets/bower_components/dragula.js/dist/dragula.min.js', array('jquery'), $this->version );
    wp_enqueue_script( 'perfect-scrollbar', $this->plugin_url() . 'assets/bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js', array('jquery'), $this->version );
    wp_enqueue_script( 'osetin-meal-planner', $this->plugin_url() . 'assets/js/osetin-meal-planner.js?version=1.5', array('jquery', 'chartist', 'chartist-plugin-tooltip', 'slick', 'dragula', 'perfect-scrollbar'), $this->version );
  }




  /**
  * Save meal plan from the POST data
  */
  function save_meal_plan(){
    // check if user is logged in
    if(!is_user_logged_in()){
      echo wp_send_json(array('status' => 'error', 'error_message' => esc_html__('You need to be logged in to use meal planner', 'osetin-meal-planner')));
      exit();
    }

    // Gather post data.
    $meal_plan = array(
        'post_title'    => wp_strip_all_tags($_POST['meal_plan']['name']),
        'post_type'  => 'osetin_meal_plan',
        'post_content'  => '',
        'post_status'   => 'publish',
        'meta_input' => array(
          'days_total' => wp_strip_all_tags($_POST['meal_plan']['days_total']), 
          'visible' => wp_strip_all_tags($_POST['meal_plan']['visible']),
          'data' => wp_strip_all_tags($_POST['meal_plan']['data']))
    );
    if($_POST['meal_plan']['id']){
      $meal_plan['ID'] = wp_strip_all_tags($_POST['meal_plan']['id']);
    }
     

    // Insert the post into the database.
    if(isset($meal_plan['ID']) && get_post_status($meal_plan['ID'])){
      $meal_plan_obj = new OsetinMealPlan();
      $meal_plan_obj->load_meal_plan($meal_plan['ID']);
      if( current_user_can('edit_others_pages') || (get_current_user_id() == $meal_plan_obj->author_id)) {
        $post_id = wp_update_post( $meal_plan );
        echo wp_send_json(array('status' => 'success', 'post_id' => $post_id, 'message' => __('Meal Plan Updated', 'osetin-meal-planner')));
      }else{
        echo wp_send_json(array('status' => 'error', 'error_message' => esc_html__('You do not have permissions to edit this meal plan', 'osetin-meal-planner')));
      }
    }else{
      $meal_plan['post_author'] = get_current_user_id();
      $post_id = wp_insert_post( $meal_plan );
      $message = 'Inserted';
      echo wp_send_json(array('status' => 'success', 'post_id' => $post_id, 'message' => __('Meal Plan Added', 'osetin-meal-planner')));
    }
    exit();
  }



  /**
  * Loads meal plan with it's recipes to edit (right now used from a meal plan lists page)
  */
  function ajax_delete_meal_plan(){
    $response_html = '';
    // Check if user is logged in
    if(!is_user_logged_in()){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('You need to be logged in to use meal planner', 'osetin-meal-planner')));
      exit();
    }
    $meal_plan = new OsetinMealPlan();
    $meal_plan_id = $_POST['meal_plan_id'];
    // ADD TO SPECIFIC MEAL PLAN
    if(!$meal_plan_id){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('Invalid meal plan selected', 'osetin-meal-planner')));
      exit();
    }
    $meal_plan->load_meal_plan($meal_plan_id);
    if($meal_plan->author_id != get_current_user_id()){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('You are not allowed to delete this meal plan', 'osetin-meal-planner')));
      exit();
    }else{
      wp_delete_post($meal_plan->id);
      $response_html = __('Meal Plan has been deleted', 'osetin-meal-planner');
    }
    wp_reset_postdata();

    echo wp_send_json(array('status' => 'success', 'message' => $response_html));
    exit();
  }

  /**
  * Loads meal plan with it's recipes to edit (right now used from a meal plan lists page)
  */
  function ajax_load_meal_plan(){
    $response_html = '';
    // Check if user is logged in
    if(!is_user_logged_in()){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('You need to be logged in to use meal planner', 'osetin-meal-planner')));
      exit();
    }
    $meal_plan = new OsetinMealPlan();
    $meal_plan_id = $_POST['meal_plan_id'];
    // ADD TO SPECIFIC MEAL PLAN
    if(!$meal_plan_id){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('Invalid meal plan selected', 'osetin-meal-planner')));
      exit();
    }
    $meal_plan->load_meal_plan($meal_plan_id);
    if(($meal_plan->author_id != get_current_user_id()) && !$meal_plan->visible){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('You are not allowed to view/edit this meal plan', 'osetin-meal-planner')));
      exit();
    }
    ob_start();
    $meal_plan->meal_plan_html();
    $response_html.= ob_get_clean();
    wp_reset_postdata();

    echo wp_send_json(array('status' => 'success', 'message' => $response_html));
    exit();
  }

  /**
  * Loads meal plan with it's recipes to edit (right now used from a meal plan lists page)
  */
  function ajax_load_full_meal_plan(){
    $response_html = '';
    // Check if user is logged in
    if(!is_user_logged_in()){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('You need to be logged in to use meal planner', 'osetin-meal-planner')));
      exit();
    }
    $meal_plan = new OsetinMealPlan();
    $meal_plan_id = $_POST['meal_plan_id'];
    // ADD TO SPECIFIC MEAL PLAN
    if(!$meal_plan_id){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('Invalid meal plan selected', 'osetin-meal-planner')));
      exit();
    }
    $meal_plan->load_meal_plan($meal_plan_id);
    if(($meal_plan->author_id != get_current_user_id()) && !$meal_plan->visible){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('You are not allowed to view/edit this meal plan', 'osetin-meal-planner')));
      exit();
    }
    $response_html.= '<div class="full-meal-plan-recipes">';
    ob_start();
    $meal_plan->meal_plan_full_html();
    $response_html.= ob_get_clean();
    wp_reset_postdata();
    $response_html.= '</div>';

    echo wp_send_json(array('status' => 'success', 'message' => $response_html));
    exit();
  }

  /**
  * Create a meal plan if user has none, or select meal plan if user has some
  */
  function select_or_create_meal_plan(){
    $response_html = '';
    // Check if user is logged in
    if(!is_user_logged_in()){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('You need to be logged in to use meal planner', 'osetin-meal-planner')));
      exit();
    }
    // Check if recipe id was given to add to a meal plan
    // TODO - allow meal plan creation without passing recipe id
    if(!isset($_POST['post_id']) && $_POST['']){
      echo wp_send_json(array('status' => 'error', 'message' => esc_html__('Invalid data supplied', 'osetin-meal-planner')));
      exit();
    }

    $post_id_to_add = $_POST['post_id'];
    $meal_plan_id = $_POST['meal_plan_id'];

    $meal_plan = new OsetinMealPlan();

    if(isset($meal_plan_id)){
      // ADD TO SPECIFIC MEAL PLAN
      if($meal_plan_id != 'new'){
        $meal_plan->load_meal_plan($meal_plan_id);
      }
      ob_start();
      $meal_plan->meal_plan_html($post_id_to_add);
      $response_html.= ob_get_clean();
    }else{
      // NO MEAL PLAN WAS SELECTED - TRY QUERY DB FOR EXISTING PLANS, IF EMPTY - CREATE NEW
      $args = array(  
        'post_status' => 'publish',
        'post_type' => 'osetin_meal_plan',
        'author'        =>  get_current_user_id()
      );
      $osetin_meal_plans_query = new WP_Query( $args );

      if ( $osetin_meal_plans_query->have_posts() ) {
        // USER HAS EXISTING MEAL PLANS, SHOW A SELECTOR BOX
        ob_start();
        $meal_plan->new_or_select_prompt_html($osetin_meal_plans_query, $post_id_to_add);
        $response_html.= ob_get_clean();
      }else{
        // USER DOES NOT HAVE ANY MEAL PLANS, CREATE MEAL PLAN AUTOMATICALLY
        ob_start();
        $meal_plan->meal_plan_html($post_id_to_add);
        $response_html.= ob_get_clean();
      }
      wp_reset_postdata();

    }

    echo wp_send_json(array('status' => 'success', 'message' => $response_html));
    exit();
  }

}
endif;


$omp = new OsetinMealPlanner();
<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'OsetinMealPlan' ) ) :
  class OsetinMealPlan {

    public $days_total;
    public $visible;
    public $name;
    public $days;
    public $day_periods;
    public $author_id;
    public $id;


    public function __construct(){
        $this->name = __('My Meal Plan', 'osetin-meal-planner');
        $this->days_total = 7;
        $this->id = false;
        $this->author_id = false;
        $this->visible = true;
        $this->day_periods = array(
          array('name' => __('Breakfast', 'osetin-meal-planner'), 'position' => 1, 'recipes_ids' => array(), 'recipes_objects' => array()),
          array('name' => __('Lunch', 'osetin-meal-planner'), 'position' => 2, 'recipes_ids' => array(), 'recipes_objects' => array()),
          array('name' => __('Dinner', 'osetin-meal-planner'), 'position' => 3, 'recipes_ids' => array(), 'recipes_objects' => array()));
        $this->days = array();
        for($i = 1; $i <= $this->days_total; $i++){
          $this->add_day($i);
        }
    }

    public static function get_user_meal_plans_query($user_id){
      $args = array(  
        'post_status' => 'publish',
        'post_type' => 'osetin_meal_plan',
        'author'        =>  $user_id
      );
      $osetin_meal_plans_query = new WP_Query( $args );
      return $osetin_meal_plans_query;
    }

    public function load_meal_plan($meal_plan_id){
      $meal_plan_obj = get_post($meal_plan_id);
      if($meal_plan_obj){
        $this->id = $meal_plan_obj->ID;
        $this->author_id = $meal_plan_obj->post_author;
        $this->name = $meal_plan_obj->post_title;
        $this->days_total = get_post_meta($meal_plan_obj->ID, 'days_total', true);
        $this->visible = get_post_meta($meal_plan_obj->ID, 'visible', true);
        $json_data_string = get_post_meta($meal_plan_obj->ID, 'data', true);
        if($json_data_string){
          $this->days = json_decode($json_data_string, true);
          $this->days_total = count($this->days);
          for($day_index = 0; $day_index < count($this->days); $day_index++){

            $this->days[$day_index]['nutritions']['calories'] = 0;
            $this->days[$day_index]['nutritions']['protein'] = 0;
            $this->days[$day_index]['nutritions']['carbs'] = 0;
            $this->days[$day_index]['nutritions']['fat'] = 0;
            
            for($period_index = 0; $period_index < count($this->days[$day_index]['periods']); $period_index++){
              $this->days[$day_index]['periods'][$period_index]['recipes_objects'] = array();

              foreach($this->days[$day_index]['periods'][$period_index]['recipes_ids'] as $recipe_id){
                $recipe_object = $this->load_recipe_info_by_id($recipe_id);
                $this->days[$day_index]['periods'][$period_index]['recipes_objects'][] = $recipe_object;
                $this->days[$day_index]['nutritions']['calories']+= $recipe_object['nutritions']['calories'];
                $this->days[$day_index]['nutritions']['protein']+= $recipe_object['nutritions']['protein'];
                $this->days[$day_index]['nutritions']['carbs']+= $recipe_object['nutritions']['carbs'];
                $this->days[$day_index]['nutritions']['fat']+= $recipe_object['nutritions']['fat'];
              }
            }
          }
          /*
            {"periods":[
              {"name":"Breakfast","recipes":[64,64]},
              {"name":"Lunch","recipes":[64]},
              {"name":"Dinner","recipes":[]}]},
            {"periods":[
              {"name":"Breakfast","recipes":[]},
              {"name":"Lunch","recipes":[]},
              {"name":"Dinner","recipes":[]}]},
          */
        }
        wp_reset_postdata();
        
        return $meal_plan_obj;
      }else{
        return false;
      }
    }


    public function add_day(){
      $day = array('periods' => $this->day_periods);
      $this->days[] = $day;
    }


    function recipe_html($recipe){
      ?>
        <div class="mpdp-recipe" data-post-id="<?php echo $recipe['id']; ?>" data-calories="<?php echo $recipe['nutritions']['calories']; ?>" data-protein="<?php echo $recipe['nutritions']['protein']; ?>" data-fat="<?php echo $recipe['nutritions']['fat']; ?>" data-carbs="<?php echo $recipe['nutritions']['carbs']; ?>">
          <div class="mpdp-close"><i class="mp-icon-cross"></i></div>
          <a href="<?php echo $recipe['url']; ?>" target="_blank" class="mpdp-open"><i class="mp-icon-ui-07"></i></a>
          <div class="mpdp-recipe-i">
            <div class="mpdp-thumb">
              <div class="mpdp-thumb-i" style="background-image: url(<?php echo $recipe['img_url']; ?>);"></div>
            </div>
            <div class="mpdp-name-w">
              <div class="mpdp-name"><?php echo $recipe['name']; ?></div>
              <?php if($recipe['nutritions']){ 
                $total_nutritions = $recipe['nutritions']['fat'] + $recipe['nutritions']['carbs'] + $recipe['nutritions']['protein'];
                if($total_nutritions){
                  ?>
                  <div class="mpdp-nutritions">
                    <div class="mpdpn mpdpn-carbs" style="width: <?php echo round($recipe['nutritions']['carbs'] / $total_nutritions * 100); ?>%;"><span><?php echo __('Carbs: ', 'osetin-meal-planner').$recipe['nutritions']['carbs'].__('g', 'osetin-meal-planner'); ?></span></div>
                    <div class="mpdpn mpdpn-protein" style="width: <?php echo round($recipe['nutritions']['protein'] / $total_nutritions * 100); ?>%;"><span><?php echo __('Protein: ', 'osetin-meal-planner').$recipe['nutritions']['protein'].__('g', 'osetin-meal-planner'); ?></span></div>
                    <div class="mpdpn mpdpn-fat" style="width: <?php echo round($recipe['nutritions']['fat'] / $total_nutritions * 100); ?>%;"><span><?php echo __('Fat: ', 'osetin-meal-planner').$recipe['nutritions']['fat'].__('g', 'osetin-meal-planner'); ?></span></div>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        </div>

      <?php
    }


    function day_html($day, $position){
      ?>
      <div class="mp-day" id="mpDay_<?php echo $position; ?>" data-position="<?php echo $position; ?>" data-calories="<?php echo $day['nutritions']['calories']; ?>" data-protein="<?php echo $day['nutritions']['protein']; ?>" data-carbs="<?php echo $day['nutritions']['carbs']; ?>" data-fat="<?php echo $day['nutritions']['fat']; ?>">
        <div class="mpd-header">
          <div class="mpd-info">
            <div class="mpd-number"><?php echo _e('Day', 'osetin-meal-planner').' <span>'.$position.'</span>'; ?></div>
            <div class="mpd-calories"><span><?php _e('Total Calories', 'osetin-meal-planner'); ?></span><strong><?php echo $day['nutritions']['calories']; ?></strong></div>
          </div>
          <div class="mpd-chart"></div>
          <div class="mpd-legend">
            <div class="mpd-legend-label legend-carbs" data-label="<?php _e('Carbs', 'osetin-meal-planner'); ?>"><?php _e('Carbs', 'osetin-meal-planner'); ?>: <span><?php if($day['nutritions']['carbs']) echo $day['nutritions']['carbs'].__('g', 'osetin-meal-planner'); else echo '0'.__('g', 'osetin-meal-planner'); ?></span></div>
            <div class="mpd-legend-label legend-protein" data-label="<?php _e('Protein', 'osetin-meal-planner'); ?>"><?php _e('Protein', 'osetin-meal-planner'); ?>: <span><?php if($day['nutritions']['protein']) echo $day['nutritions']['protein'].__('g', 'osetin-meal-planner'); else echo '0'.__('g', 'osetin-meal-planner'); ?></span></div>
            <div class="mpd-legend-label legend-fat" data-label="<?php _e('Fat', 'osetin-meal-planner'); ?>"><?php _e('Fat', 'osetin-meal-planner'); ?>: <span><?php if($day['nutritions']['fat']) echo $day['nutritions']['fat'].__('g', 'osetin-meal-planner'); else echo '0'.__('g', 'osetin-meal-planner'); ?></span></div>
          </div>
        </div>
        <div class="mpd-body">
          <?php foreach($day['periods'] as $period){ ?>
          <div class="mpd-period <?php echo (count($period['recipes_ids']) == 0) ? 'empty' : ''; ?>">
            <div class="mpd-period-header"><?php echo $period['name']; ?></div>
            <div class="mpd-period-recipes">
              <?php  
              foreach($period['recipes_objects'] as $recipe){
                $this->recipe_html($recipe);
              }
              ?>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
      <?php
    }



    function load_recipe_info_by_id($recipe_id){
      $recipe_obj = get_post($recipe_id);
      $recipe = array();
      $recipe['id'] = $recipe_obj->ID;
      $recipe['url'] = get_post_permalink($recipe_obj->ID);
      $recipe['name'] = $recipe_obj->post_title;
      $recipe['img_url'] = $this->output_post_thumbnail_url($recipe_id);
      $recipe['nutritions'] = array('calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0);


      $default_nutritions = array(
        "calories" => "calories",
        "protein" => "protein",
        "fat" => "fat",
        "carbohydrates" => "carbs",
        "carbs" => "carbs",
        "calories" => "calories",
        "proteinContent" => "protein",
        "fatContent" => "fat",
        "carbohydrateContent" => "carbs"
      );

      if( have_rows('nutritions', $recipe_id) ){
        while ( have_rows('nutritions', $recipe_id) ) {
          the_row();
          // check if nutrition is matched/assigned to a predefined list of google scheme nutritions
          $rich_field_type = get_sub_field('google_rich_meta_field');
          if($rich_field_type){
            if(isset($default_nutritions[$rich_field_type]) && isset($recipe['nutritions'][$default_nutritions[$rich_field_type]])){
              $recipe['nutritions'][$default_nutritions[$rich_field_type]] = (double) get_sub_field('nutrition_value');
            }
          }else{
            // try to automatically find a mathcing nutrition name
            $temp_nutrition_name = get_sub_field('nutrition_name');
            if($temp_nutrition_name){
              $cleaned_nutrition_name = strtolower(str_replace(' ', '_', $temp_nutrition_name));
              if(isset($default_nutritions[$cleaned_nutrition_name]) && isset($recipe['nutritions'][$default_nutritions[$cleaned_nutrition_name]])){
                $recipe['nutritions'][$default_nutritions[$cleaned_nutrition_name]] = (double) get_sub_field('nutrition_value');
              }
            }
          }
        }
      }


      wp_reset_postdata();
      return $recipe;
    }



    function new_or_select_prompt_html($osetin_meal_plans_query, $post_id_to_add){
      ?>
        <div class="meal-plan-select-popup">
          <div class="meal-plan-close"><i class="mp-icon-cross"></i></div>
          <h3><?php esc_html_e('Add Recipe to Meal Plan', 'osetin-meal-planner'); ?></h3>
          <div class="meal-plan-selects-w">
            <div class="mp-select-existing-w">
              <select id="select_meal_plan_id">
                <?php
                while ( $osetin_meal_plans_query->have_posts() ) : $osetin_meal_plans_query->the_post();
                  echo '<option value="'.get_the_ID().'">'.get_the_title().'</option>';
                endwhile;
                ?>
              </select>
              <button type="button" class="select_meal_plan_btn" data-post-id="<?php echo $post_id_to_add; ?>"><?php esc_html_e('Add to Meal Plan', 'osetin-meal-planner'); ?></button>
            </div>
            <div class="mp-select-or"><?php esc_html_e('or', 'osetin-meal-planner'); ?></div>
            <div class="mp-select-new-w">
              <button type="button" class="create_new_meal_plan_btn" data-post-id="<?php echo $post_id_to_add; ?>"><?php esc_html_e('Create New Meal Plan', 'osetin-meal-planner'); ?></button>
            </div>
          </div>
        </div>
      <?php
    }


    function meal_plan_full_html(){
      echo '<div class="print-meal-plan-actions">
        <a href="#" onclick="window.print();" class="print-trigger"><i class="os-icon os-icon-tech-11"></i> <span>'.__('Print', 'osetin-meal-planner').'</a>
        <a href="#" class="cancel-print-meal-plan-trigger"><i class="os-icon mp-icon-cross"></i> <span>'.__('Cancel', 'osetin-meal-planner').'</a>
      </div>';
      echo '<div class="print-meal-plan-days-w">';
      foreach($this->days as $position => $day){
        $this->day_html($day, $position + 1);
      }
      echo '</div>';
    }

    function meal_plan_html($holder_recipe_id = false){ 
      $ajax_action_name = 'osetin_save_meal_plan';
      $allowed_edit = (!isset($this->id) || current_user_can('edit_others_pages') || (get_current_user_id() == $this->author_id)) ? 'can-edit' : 'no-edit';
      if(!$this->visible && !(get_current_user_id() == $this->author_id)){
        echo '<div>'.__('This meal plan is private', 'osetin-meal-planner').'</div>';
        return;
      }
      ?>
      <div class="meal-planner-w <?php echo $allowed_edit; ?>" data-nutritions-measure="<?php esc_attr_e('g', 'osetin-meal-planner'); ?>">
        <div class="meal-plan-close"><i class="mp-icon-cross"></i></div>
        <form id="mealPlannerForm" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
        <input type="hidden" name="meal_plan[id]" id="meal_plan_id" value="<?php echo $this->id; ?>">
        <input type="hidden" name="action" value="<?php echo $ajax_action_name; ?>">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( $ajax_action_name ); ?>">
        <input type="hidden" id="meal_plan_data" name="meal_plan[data]" value="">
        <div class="mp-header">
          <div class="mp-header-left">
            <div class="mpp-name">
              <label for=""><?php esc_html_e('Plan Name:', 'osetin-meal-planner'); ?></label>
              <input type="text" id="meal_plan_name" name="meal_plan[name]" value="<?php echo esc_attr($this->name); ?>">
            </div>
            <div class="mpp-days">
              <label for=""><?php esc_html_e('Days:', 'osetin-meal-planner'); ?></label>
              <input type="text" name="meal_plan[days_total]" id="mp_days_total" value="<?php echo esc_attr($this->days_total); ?>">
            </div>
            <div class="mpp-visibility">
              <label for=""><?php esc_html_e('Visibility:', 'osetin-meal-planner'); ?></label>
              <div class="select-w">
                <select name="meal_plan[visible]" id="mp_visible">
                  <option value="1" <?php if($this->visible) echo 'selected' ?>><?php _e('Public', 'osetin-meal-planner'); ?></option>
                  <option value="0" <?php if(!$this->visible) echo 'selected' ?>><?php _e('Private', 'osetin-meal-planner'); ?></option>
                </select>
              </div>
            </div>
            <div class="mpp-save-btn">
              <img src="<?php echo OsetinMealPlanner::plugin_url() . 'assets/img/loading.gif' ?>" alt="">
              <button type="submit">Save</button>
            </div>
          </div>
          <div class="mp-header-right">
            <div class="mpp-share-btn">
              <a href="#" class="share-meal-plan" data-meal-plan-id="<?php echo $this->id; ?>" data-wait-label="<?php _e('Wait...', 'osetin-meal-planner'); ?>"><i class="os-icon os-icon-ui-35"></i> <span><?php _e('Share', 'osetin-meal-planner'); ?></span></a>
            </div>
            <div class="mpp-print-btn">
              <a href="#" class="print-meal-plan" data-meal-plan-id="<?php echo $this->id; ?>" data-wait-label="<?php _e('Wait...', 'osetin-meal-planner'); ?>"><i class="os-icon os-icon-tech-11"></i> <span><?php _e('Printable Version', 'osetin-meal-planner'); ?></span></a>
            </div>
          </div>
        </div>
        <div class="mp-days-w">
          <div class="mp-days-i">
            <?php 
            foreach($this->days as $position => $day){
              $this->day_html($day, $position + 1);
            } ?>
          </div>
        </div>
        <div class="mp-footer">
          <div class="recipe-holders-w">
            <div class="recipe-holders">
              <div class="recipe-holder empty"></div>
              <div class="recipe-holder <?php if(!$holder_recipe_id) echo 'empty'; ?>">
                <?php 
                if($holder_recipe_id){ 
                  $holder_recipe = $this->load_recipe_info_by_id($holder_recipe_id);
                  $this->recipe_html($holder_recipe);
                }
                ?>
              </div>
              <div class="recipe-holder empty"></div>
            </div>
          </div>
        </div>
        </form>
      </div>
      <?php
    }


    function output_post_thumbnail_url($post_id = false, $size = 'thumbnail')
    {
      $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
      if(isset($img_arr[0])){
        return $img_arr[0];
      }else{
        return $this->get_placeholder_image_url();
      }
    }

    function get_placeholder_image_url($squared = false){
      $placeholder_url = $squared ? OsetinMealPlanner::plugin_url() . 'assets/img/placeholder-square.jpg' : OsetinMealPlanner::plugin_url() . 'assets/img/placeholder.jpg';
      $placeholder_img_id = osetin_get_field('placeholder_image', 'option');
      if ($placeholder_img_id){
        $size_name = $squared ? 'osetin-medium-square-thumbnail' : 'osetin-full-width';
        $img_url_arr = wp_get_attachment_image_src($placeholder_img_id, $size_name);
        if($img_url_arr){
          $placeholder_url = $img_url_arr[0];
        }
      }
      return $placeholder_url;
    }
  }
endif;
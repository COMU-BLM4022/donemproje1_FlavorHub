( function( $ ) {
  "use strict";

  // DOCUMENT READY
  $( function() {

    var nutritionsChartObj = {};
    var dragulaObj;
    var nutritionsMeasure = 'g';


    function mp_open_modal(){

      if(!$('.os-modal').length){
        $('body').append('<div class="os-modal-w"><div class="os-modal no-padding"></div><div class="os-meal-planner-fader"></div></div>');
      }
      var $os_modal = $('.os-modal');
      $('body').addClass('meal-planner-modal-active');
      return $os_modal;
    }

    function mp_close_modal(){
      var confirm_close = true;
      if($('#mealPlannerForm').data('changed') === 'yes'){
        confirm_close = confirm('Are you sure you want to close meal planner? Changes you made will not be saved.');
      }
      if(confirm_close === true){
        $('.os-modal-w').remove();
        $('body').removeClass('meal-planner-modal-active');
        $(document).unbind('keyup.meal_planner');
      }
    }

    function mp_call_for_meal_plan(data){
      var $os_modal = mp_open_modal();
      // $os_modal.fadeOut(300);
      $.ajax({        
        type : "post",
        dataType : "json",
        url : ajaxurl,
        data : data,
        success: function(data){           

          $os_modal.show();
          if(data.status === "success"){
            $os_modal.html(data.message);
            init_meal_planner();
          }else{
            $os_modal.html(data.message);
          }
          $('.meal-planner-w .mpd-body').perfectScrollbar();
        }
      });
    }

    function mp_generate_json_data(){
      var days = [];
      $('.meal-planner-w .mp-day').each(function(){
        var day = {'periods' : []};
        $(this).find('.mpd-period').each(function(){
          var period_name = $(this).find('.mpd-period-header').text();
          var period = {'name' : period_name, 'recipes_ids' : []};
          $(this).find('.mpdp-recipe').each(function(){
            var recipe_id = $(this).data('post-id');
            period.recipes_ids.push(recipe_id);
          });
          day.periods.push(period);
        });
        days.push(day);
      });
      return days;
    }


    function mp_init_nutritions_chart($day){
      var day_id = $day.prop('id');
      var position = $day.data('position');

      var label_protein = $('.meal-planner-w .mpd-legend .legend-protein').data('label');
      var label_carbs = $('.meal-planner-w .mpd-legend .legend-carbs').data('label');
      var label_fat = $('.meal-planner-w .mpd-legend .legend-fat').data('label');

      var nutritions_protein = $day.data('protein');
      var nutritions_carbs = $day.data('carbs');
      var nutritions_fat = $day.data('fat');

      var total_nutritions = nutritions_protein + nutritions_carbs + nutritions_fat;
      var nutritions_protein_percent = (total_nutritions > 0) ? Math.round(nutritions_protein / total_nutritions * 100) : 0;
      var nutritions_carbs_percent = (total_nutritions > 0) ? Math.round(nutritions_carbs / total_nutritions * 100) : 0;
      var nutritions_fat_percent = (total_nutritions > 0) ? Math.round(nutritions_fat / total_nutritions * 100) : 0;


      var data = {
        labels: [label_fat, label_carbs, label_protein],
        series: [
          {meta: label_fat, value: nutritions_fat_percent}, 
          {meta: label_carbs, value: nutritions_carbs_percent}, 
          {meta: label_protein, value: nutritions_protein_percent}
        ]
      };


      nutritionsChartObj['day_' + position] = new Chartist.Pie('#' + day_id + ' .mpd-chart', data, {
        showLabel: false,
        plugins: [
          Chartist.plugins.tooltip({
            tooltipFnc: function(meta, value){
              return meta + ': ' + value + '%';
            }
          })
        ]
      });
    }



    function mp_recalculate_day_nutritions($day){
      var position = $day.data('position');
      var total_calories = 0;
      var total_protein = 0;
      var total_fat = 0;
      var total_carbs = 0;
      $day.find('.mpdp-recipe').each(function(){
        total_calories = total_calories + $(this).data('calories');
        total_protein = total_protein + $(this).data('protein');
        total_fat = total_fat + $(this).data('fat');
        total_carbs = total_carbs + $(this).data('carbs');
      });
      $day.find('.mpd-calories strong').text(total_calories);
      $day.data('calories', total_calories);
      if(nutritionsChartObj['day_' + position] !== undefined){
        nutritionsChartObj['day_' + position].data.series[0].value = total_fat;
        nutritionsChartObj['day_' + position].data.series[1].value = total_carbs;
        nutritionsChartObj['day_' + position].data.series[2].value = total_protein;
        nutritionsChartObj['day_' + position].update();
      }
      $day.find('.mpd-legend-label.legend-carbs span').text(total_carbs + nutritionsMeasure);
      $day.find('.mpd-legend-label.legend-fat span').text(total_fat + nutritionsMeasure);
      $day.find('.mpd-legend-label.legend-protein span').text(total_protein + nutritionsMeasure);
    }

    function mp_trigger_plan_change(has_changed){
      $('#mealPlannerForm').data('changed', has_changed);
    }

    function mp_init_drag(elements_arr){

      // INIT DRAG AND DROP FOR RECIPES
      dragulaObj = dragula(elements_arr, {
        copy: function (el, source) {
          return $(source).hasClass('recipe-holder');
        },
        copySortSource: true
      }).on('drag', function () {
      }).on('drop', function (el) {
        var period = $(el).closest('.mpd-period, .recipe-holder');
        period.removeClass('empty');
        var $new_day = $(el).closest('.mp-day');
        if($new_day.length){
          mp_recalculate_day_nutritions($new_day);
        }
        mp_trigger_plan_change('yes');

      }).on('over', function (el, container) {
        $(container).closest('.mpd-period, .recipe-holder').addClass('over');

      }).on('out', function (el, container, source) {
        // var $new_day = $(container).closest('.mp-day');
        var $old_day = $(source).closest('.mp-day');

        if($old_day.length){
          mp_recalculate_day_nutritions($old_day);
        }
        var new_period = $(container).closest('.mpd-period, .recipe-holder');
        new_period.removeClass('over');
        var old_period = $(source).closest('.mpd-period, .recipe-holder');
        if(old_period.length && !old_period.find('.mpdp-recipe').length){
          old_period.addClass('empty');
        }
      });

    }

    function init_meal_planner(){
      // INIT DAYS SLIDER
      var center_meal_planner = $('.os-modal-w').length ? true : false;
      $('.meal-planner-w .mp-days-i').slick({
        variableWidth : true,
        centerMode: center_meal_planner,
        infinite: false,
        speed: 200
      });


      // Handle ESC key press to close the modal
      $(document).unbind('keyup.meal_planner');
      $(document).on('keyup.meal_planner', function(e){
         if (e.keyCode === 27) { 
          mp_close_modal();
        }
      });
      $('.meal-plan-close').on('click', function(){
        mp_close_modal();
      });

      nutritionsMeasure = $('.meal-planner-w').data('nutritions-measure');

      // -------------------
      // CHANGING NAME OF MEAL PLAN
      // -------------------
      $('#meal_plan_name').on('change', function(){
        mp_trigger_plan_change('yes');
      });

      // -------------------
      // CHANGING NUMBER OF DAYS LOGIC 
      // -------------------
      $('#mp_days_total').on('change', function(){
        var current_days_total = $('.meal-planner-w .mp-day').length;
        var new_days_total = $(this).val();


        // -------------------
        // -> REMOVE DAYS
        // -------------------
        if(current_days_total > new_days_total){
          var $days_to_remove = $('.meal-planner-w .mp-day').slice(new_days_total);
          var remove_confirm = true;
          if($days_to_remove.find('.mpdp-recipe').length){
            remove_confirm = confirm('Some of the days that you want to remove have recipes, are you sure you want to remove them?');
          }
          if(remove_confirm === true){
            $days_to_remove.remove();
            var $slick_slider = $('.meal-planner-w .mp-days-i');
            $slick_slider.slick('reinit');
            if($slick_slider.slick('slickCurrentSlide') > (new_days_total - 1)){
              $slick_slider.slick('slickGoTo', new_days_total - 1);
            }
            mp_trigger_plan_change('yes');
          }
        }


        // -------------------
        // -> ADD DAYS
        // -------------------
        if(current_days_total < new_days_total){
          for(var day_position = (current_days_total + 1); day_position <= new_days_total; day_position++) {
            var $cloned_day = $('.meal-planner-w .mp-day:last').clone().data('position', day_position).data('calories', 0).data('protein', 0).data('carbs', 0).data('fat', 0);
            $cloned_day.find('.mpdp-recipe').remove();
            $cloned_day.find('.mpd-calories strong').text('0');
            $cloned_day.find('.mpd-period').addClass('empty');
            $cloned_day.find('.mpd-number span').text(day_position);
            $cloned_day.prop('id', 'mpDay_' + day_position);
            $cloned_day.find('.mpd-nutritions iframe').remove();
            $cloned_day.find('.mpd-chart').html('');
            $cloned_day.insertAfter('.mp-day:last');
            $cloned_day.find('.mpd-legend .mpd-legend-label span').text('0' + nutritionsMeasure);
            mp_init_nutritions_chart($('.meal-planner-w .mp-day:last'));
            $('.meal-planner-w .mp-day:last .mpd-body').perfectScrollbar();
            dragulaObj.containers = $('.mpd-period-recipes, .recipe-holder').toArray();
            $('.meal-planner-w .mp-days-i').slick('reinit');
            mp_trigger_plan_change('yes');
          }
        }
      });


      $('.select_meal_plan_btn, .create_new_meal_plan_btn').on('click', function(){
        $(this).prop('disabled', true);
        var post_id = $(this).data('post-id');
        var nonce = $(this).closest('form').find('input[name="nonce"]').val();
        var meal_plan_id = 'new';
        if($(this).hasClass('select_meal_plan_btn')){
          meal_plan_id = $('#select_meal_plan_id').val();
        }
        var data = {
          "action": "osetin_select_or_create_meal_plan", 
          "post_id" : post_id,
          "nonce": nonce,
          "meal_plan_id" : meal_plan_id
        };
        mp_call_for_meal_plan(data);
      });


      $('#mealPlanSelectorForm').on('submit', function(e){
        e.preventDefault();
      });


      $('#mealPlannerForm').on('submit', function(e){
        e.preventDefault();
        $('.mpp-save-btn').addClass('disabled').find('button').prop("disabled", true);
        var json_data = mp_generate_json_data();
        $('#meal_plan_data').val(JSON.stringify(json_data));
        $.ajax({
          type : "post",
          url: $("#mealPlannerForm").attr("action"),
          data: $("#mealPlannerForm").serialize(), 
          success: function(response){
            $('.mpp-save-btn').removeClass('disabled').find('button').prop("disabled", false);
            if(response.status === 'success'){
              mp_trigger_plan_change('no');
              if(response.post_id){
                $('#meal_plan_id').val(response.post_id);
              }
            }else{

            }
          } 
        });
        return false;
      });




      // INIT REMOVE RECIPE BTN
      $('body').on('click', '.mpdp-close', function(){
        var mpdPeriod = $(this).closest('.mpd-period, .recipe-holder');
        var $day = $(this).closest('.mp-day');
        $(this).closest('.mpdp-recipe').remove();
        if(!mpdPeriod.find('.mpdp-recipe').length){
          mpdPeriod.addClass('empty');
        }
        mp_trigger_plan_change('yes');
        if($day.length){
          mp_recalculate_day_nutritions($day);
        }
        return false;
      });


      mp_init_drag($('.meal-planner-w .mpd-period-recipes, .recipe-holder').toArray());

      // INIT CHARTS
      if(typeof Chartist !== 'undefined'){

        // init pie chart if element exists
        if($(".mp-day").length){
          $('.meal-planner-w .mp-day').each(function(){

            mp_init_nutritions_chart($(this));

          });


        }
      }
    }


    // INIT MEAL PLANNER ON SINGLE-MEAL_PLAN PAGE
    if($('.single-osetin_meal_plan .meal-planner-w').length){
      init_meal_planner();
    }


    $('body').on('click', '.os-meal-planner-fader', function(){
      mp_close_modal();
      return false;
    });


    // ADD TO MEAL PLAN BUTTON
    $('.trigger-add-to-meal-plan').on('click', function(){
      var post_id = $(this).data('post-id');
      var nonce = $(this).data("nonce");
      var data = {
          "action": "osetin_select_or_create_meal_plan", 
          "post_id" : post_id,         
          "nonce": nonce
        };
      mp_call_for_meal_plan(data);
      return false;
    });


    // PRINT MEAL PLAN BUTTON
    $('.print-meal-plan').on('click', function(){
      var $print_btn = $(this);
      var meal_plan_id = $(this).data('meal-plan-id');
      var data = {
          "action": "osetin_ajax_load_full_meal_plan",
          "meal_plan_id" : meal_plan_id
        };
      var wait_label = $('.meal-plans-list').length ? $('.meal-plans-list').data('wait-label') : $(this).data('wait-label');
      $print_btn.data('temp-label', $print_btn.text()).find('span').text(wait_label);
      $.ajax({        
        type : "post",
        dataType : "json",
        url : ajaxurl,
        data : data,
        success: function(data){
          $('body').addClass('print-meal-plan');
          $('.all-wrapper').before(data.message);
          $(window).scrollTop(0);
          $print_btn.find('span').text($print_btn.data('temp-label'));
          $('.cancel-print-meal-plan-trigger').on('click', function(){
            $(window).scrollTop(0);
            $('body').removeClass('print-meal-plan');
            $('.full-meal-plan-recipes').remove();
            return false;
          });
          // window.print();
        }
      });
      return false;
    });

    // EDIT MEAL PLAN BUTTON
    $('.edit-meal-plan').on('click', function(){
      var $meal_plan = $(this).closest('.meal-plan');
      var meal_plan_id = $meal_plan.data('meal-plan-id');
      var nonce = $(this).data("nonce");
      var data = {
          "action": "osetin_ajax_load_meal_plan",
          "meal_plan_id" : meal_plan_id,
          "nonce": nonce
        };
      mp_call_for_meal_plan(data);
      return false;
    });

    // DELETE MEAL PLAN BUTTON
    $('.delete-meal-plan').on('click', function(){
      if(!confirm($('.meal-plans-list').data('delete-label'))){
        return false;
      }

      $(this).data('temp-label', $(this).text()).text($('.meal-plans-list').data('wait-label'));

      var $meal_plan = $(this).closest('.meal-plan');
      var meal_plan_id = $meal_plan.data('meal-plan-id');

      var nonce = $(this).data("nonce");
      var data = {
          "action": "osetin_ajax_delete_meal_plan",
          "meal_plan_id" : meal_plan_id,
          "nonce": nonce
        };

      $.ajax({        
        type : "post",
        dataType : "json",
        url : ajaxurl,
        data : data,
        success: function(data){
          if(data.status === "success"){
            $meal_plan.hide(300, function(){
              $(this).remove();
            });
          }else{
            $meal_plan.closest('ul').prepend('<p>' + data.message + '</p>');
          }
        }
      });
      return false;
    });
  });



} )( jQuery );
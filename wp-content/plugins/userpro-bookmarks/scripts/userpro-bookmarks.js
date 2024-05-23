function userpro_bm_dialog(elem, html, position){
	if (!position){ position = 'left'; }

	if (html == 'new_collection'){
	
		elem.append('<div class="userpro-bm-dialog bm-'+position+'"></div><div class="userpro-bm-dialog-icon bm-'+position+'"><i class="userpro-icon-caret-up"></i></div>');
		elem.find('.userpro-bm-dialog').width( elem.parents('.userpro-bm').width() - 42 );
		custom_html = '<form action="" method="post"><input type="text" name="userpro_bm_new" id="userpro_bm_new" value="" class="userpro-bm-input" placeholder="' + elem.parents('.userpro-bm').data('new_collection_placeholder') + '" /><br> <input type="radio" class="userpro_bm_radio" name="public" value="private" checked=checked/>Private <br> <input type="radio" name="public" value="public" class="userpro_bm_radio" />Public <div class="userpro-bm-btn-contain bm-block"><a href="#" class="userpro-bm-btn" data-action="submit_collection">' + elem.parents('.userpro-bm').data('add_new_collection') + '</a></div></form>';
	
	} else {
	
		elem.append('<div class="userpro-bm-dialog bm-'+position+' autoclose"></div><div class="userpro-bm-dialog-icon bm-'+position+' autoclose"><i class="userpro-icon-caret-up"></i></div>');
		elem.find('.userpro-bm-dialog').width( elem.parents('.userpro-bm').width() - 42 );
		custom_html = html;
	
	}
	elem.find('.userpro-bm-dialog').html('<span class="userpro-bm-dialog-content">' + custom_html + '</span>');
	
	if (jQuery('#userpro_bm_new').length) jQuery('#userpro_bm_new').focus();
	
	var timer = setTimeout(function(){ jQuery('.userpro-bm-dialog.autoclose,.userpro-bm-dialog-icon.autoclose').hide().remove(); }, 3000);
	
}

function userpro_bm_limitreached_dialog(elem, limit_condition, position, htmltext){
	if(limit_condition == 'coll_limit_reached'){
		elem.append('<div class="userpro-bm-dialog bm-'+position+'"></div><div class="userpro-bm-dialog-icon bm-'+position+'"><i class="userpro-icon-caret-up"></i></div>');
		
		elem.find('.userpro-bm-dialog').width( elem.parents('.userpro-bm').width() - 42 );
		custom_html = htmltext;
	}else if(limit_condition == 'bm_coll_limit_reached'){
		elem.append('<div class="userpro-bm-dialog bm-'+position+'"></div><div class="userpro-bm-dialog-icon bm-'+position+'"><i class="userpro-icon-caret-up"></i></div>');
			
		elem.find('.userpro-bm-dialog').width( elem.parents('.userpro-bm').width() - 42 );
		custom_html = htmltext;
	}
	
	jQuery('.userpro-bm-dialog').click(function(){
		jQuery('.userpro-bm-dialog,.userpro-bm-dialog-icon').fadeOut(2000);
		elem.find('.stop').removeClass('stop');
	});
	elem.find('.userpro-bm-dialog').html('<span class="userpro-bm-dialog-content">' + custom_html + '</span>');
}

function userpro_bm_newaction( elem, parent ) {
	elem.addClass('stop');
	jQuery('.userpro-bm-dialog,.userpro-bm-dialog-icon').hide().remove();
}

function userpro_bm_donebookmark( elem, html ) {
	elem.addClass('bookmarked').removeClass('unbookmarked').removeClass('stop');
	elem.html( html );
}

function userpro_bm_updatecount( elem, html ) {
	elem.parents('.userpro-bm').find('.userpro-bm-count').html(html);
}

function userpro_bm_addbookmark( elem, html ) {
	elem.addClass('unbookmarked').removeClass('bookmarked').removeClass('stop');
	elem.html( html );
}

function userpro_bm_removedialog() {
	jQuery('.userpro-bm-dialog,.userpro-bm-dialog-icon').hide().remove();
}

function userpro_bm_update_active_collection( parent, value ){
	parent.find('input:hidden#collection_id').val( value );
}

/********Code Added By Vipin For Category Bookmark***********/
function userpro_bm_donebookmark_category( elem, html ) {
	elem.addClass('bookmarked_category').removeClass('unbookmarked_category').removeClass('stop');
	elem.html( html );
}

function userpro_bm_addbookmark_category( elem, html ) {
	elem.addClass('unbookmarked_category').removeClass('bookmarked_category').removeClass('stop');
	elem.html( html );
}
/*********Code Ended***************/

function upb_init_gridlayout(){
	
	jQuery("img").load(function(){
		var grid_container = jQuery('.upb-grid'); 
		grid_container.isotope({
	    	itemSelector: '.upb-item.active',
	    	layoutMode: 'masonry',
	    	masonry: {
	    		gutter: 10,
	    	}
		}); 
	});
}

/* Custom JS starts here */
jQuery(document).ready(function() {
	
	upb_init_gridlayout();
	
	/* code added for bookmark list's grid layout */
	jQuery(document).on('click', '.upb-button', function(e){
		
		var id = jQuery(this).attr('id');
		var curr_id = jQuery('.upb-button.active').attr('id');
		jQuery('.upb-button').removeClass("active");
		jQuery('#'+id).addClass("active");
		jQuery('.upb-grid .collection_'+curr_id).removeClass("active");
		
		if(!jQuery(this).hasClass('visited')){
		
			jQuery.ajax({
				url: userpro_ajax_url,
				data: 'action=upb_grid_print_bookmark&collection_id='+id,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					jQuery('.upb-loader.loading').show();
					jQuery('.upb-grid').isotope('destroy');
					jQuery('#'+id).addClass("visited");
					jQuery('.upb-grid .collection_'+curr_id).hide();
					jQuery('.upb-grid').append(data.html);
					upb_init_gridlayout();
				},
				complete:function(){
					jQuery('.upb-loader.loading').hide();
				}
			});
		}else{
			jQuery('.upb-loader.loading').show();
			jQuery('.upb-grid').isotope('destroy');
			jQuery('.upb-grid .upb-item.active').removeClass('active');
			jQuery('.upb-grid .collection_'+curr_id).hide();
			jQuery('.upb-grid .collection_'+id).addClass('active');
			jQuery('.upb-grid .collection_'+id).show();
			jQuery('.upb-grid').isotope({
		    	itemSelector: '.upb-item.active',
		    	layoutMode: 'masonry',
		    	masonry: {
		    		gutter: 10,
		    	}
			});
			jQuery('.upb-loader.loading').hide();
		}	
	});
	
	/* Remove bookmark from grid layout */
	jQuery(document).on('click', '.upb-action-remove i', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.upb-single');
		post_id = elem.data('post_id');
		collection_id = elem.data('collection_id');
		category_id = '';
		jQuery(this).parents('.upb-single').fadeOut('fast');
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_fav_removebookmark&post_id='+post_id+'&collection_id='+collection_id+'&category_id='+category_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				location.reload();
			}
		});
		return false;
	});
	
	/* remove a collection */
	jQuery(document).on('click', '.userpro-remove-collection', function(e){
		e.preventDefault();
		element = jQuery(this).parents('.userpro-coll-count');
		if (element.find('.userpro-coll-remove').is(':hidden')){
		jQuery(this).html( jQuery(this).data('undo') );
		element.find('.userpro-coll-remove').slideToggle();
		} else {
		jQuery(this).html( jQuery(this).data('remove') );
		element.find('.userpro-coll-remove').slideToggle();
		}
		return false;
	});
	
	/* remove a collection */
	jQuery(document).on('click', '.userpro-hard-remove', function(e){
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');

		/* switch tab */
		list = jQuery(this).parents('.userpro-coll').find('.userpro-coll-list');
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_hard_remove_collection&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				list.find('a.active').remove();
				list.find('a:first').trigger('click');
			}
		});
		return false;
	});
	
	/* soft-remove a collection */
	jQuery(document).on('click', '.userpro-soft-remove', function(e){
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');

		/* switch tab */
		list = jQuery(this).parents('.userpro-coll').find('.userpro-coll-list');
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_soft_remove_collection&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				list.find('a.active').remove();
				list.find('a:first').trigger('click');
			}
		});
		return false;
	});
	

	/* Switch a collection */
	jQuery(document).on('click', '.userpro-coll-listpublic a', function(e){
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');
		user_id = jQuery(this).data('userid_id');
		
		container = jQuery(this).parents('.userpro-coll').find('.userpro-coll-body');
		if (container.hasClass('loading') == false){

		/* switch tab */
		list = jQuery(this).parents('.userpro-coll-listpublic');
		list.find('a').removeClass('active');
		list.find('a').find('i').addClass('userpro-coll-hide');
		list.find('a').find('span').removeClass('userpro-coll-hide');
		jQuery(this).addClass('active');
		jQuery(this).find('i').removeClass('userpro-coll-hide');
		jQuery(this).find('span').addClass('userpro-coll-hide');
		
		container.addClass('loading').find('.userpro-coll-body-inner').find('div:not(.userpro-coll-remove)').fadeTo(0, 0);
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_change_public_collection&collection_id='+collection_id+'&user_id='+user_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				container.removeClass('loading').find('.userpro-coll-body-inner').empty().html(data.res);
			}
		});
		
		}
		return false;
	});
	
	
	/* Switch a collection */
	jQuery(document).on('click', '.userpro-coll-list a', function(e){
		e.preventDefault();
		collection_id = jQuery(this).data('collection_id');
		container = jQuery(this).parents('.userpro-coll').find('.userpro-coll-body');
		if (container.hasClass('loading') == false){

		/* switch tab */
		list = jQuery(this).parents('.userpro-coll-list');
		list.find('a').removeClass('active');
		list.find('a').find('i').addClass('userpro-coll-hide');
		list.find('a').find('span').removeClass('userpro-coll-hide');
		jQuery(this).addClass('active');
		jQuery(this).find('i').removeClass('userpro-coll-hide');
		jQuery(this).find('span').addClass('userpro-coll-hide');
		
		container.addClass('loading').find('.userpro-coll-body-inner').find('div:not(.userpro-coll-remove)').fadeTo(0, 0);
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_change_collection&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				container.removeClass('loading').find('.userpro-coll-body-inner').empty().html(data.res);
				var n = jQuery(".a2a_default_style").length;
				
				for (i = 0; i < n ; i++) {
   				 a2a.init();
				}
			}
		});
		
		}
		return false;
	});
	
	/* Disable forms */
	jQuery(document).on('submit', '.userpro-bm form', function(e){
		e.preventDefault();
		return false;
	});

	/* Capture change in collection */
	jQuery(document).on('change', '.userpro-bm-list select', function(e){
		dd = jQuery(this);
		parent = dd.parents('.userpro-bm');
		bookmarked_link = dd.parents('.userpro-bm').find('a.bookmarked');
		bookmarked_category_link=dd.parents('.userpro-bm').find('a.bookmarked_category');
		unbookmarked_category_link=dd.parents('.userpro-bm').find('a.unbookmarked_category');
		unbookmarked_link = dd.parents('.userpro-bm').find('a.unbookmarked');
		post_id = jQuery(this).parents('.userpro-bm').data('post_id');
		collection_id = dd.parents('.userpro-bm').find('input:hidden#collection_id').val();
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_fav_checkifbookmarked&collection_id='+dd.val()+'&post_id='+post_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if(data.status){
						userpro_bm_donebookmark( unbookmarked_link, dd.parents('.userpro-bm').data('remove_bookmark') );
						userpro_bm_donebookmark_category( unbookmarked_category_link, dd.parents('.userpro-bm').data('remove_bookmark_category') );
					}
				else{
					userpro_bm_addbookmark( bookmarked_link, dd.parents('.userpro-bm').data('add_to_collection') );
					userpro_bm_addbookmark_category( bookmarked_category_link, dd.parents('.userpro-bm').data('bookmark_category') );
				}
			}
		})
//		if (dd.val() != collection_id){
//			userpro_bm_addbookmark( bookmarked_link, dd.parents('.userpro-bm').data('add_to_collection') );
//			userpro_bm_addbookmark_category( bookmarked_category_link, dd.parents('.userpro-bm').data('bookmark_category') );
//		} else {
//			userpro_bm_donebookmark( unbookmarked_link, dd.parents('.userpro-bm').data('remove_bookmark') );
//			userpro_bm_donebookmark_category( unbookmarked_category_link, dd.parents('.userpro-bm').data('remove_bookmark_category') );
//		}
	});

	/* trigger submit new collection */
	jQuery(document).on('click', '.userpro-bm-dialog a[data-action="submit_collection"]', function(e){
		jQuery(this).parents('form').trigger('submit');
	});
	
	/* submit new collection */
	jQuery(document).on('submit', '.userpro-bm-dialog form:not(.stop)', function(e){
		elem = jQuery(this);
		dialog = jQuery(this).parents('.userpro-bm-dialog');
		var parent = jQuery(this).parents('.userpro-bm');
		
		collection_name = dialog.find('#userpro_bm_new').val();
		privacy = dialog.find('input[name=public]:checked').val();
		
		if (collection_name != ''){
		
		elem.addClass('stop');
		default_collection = jQuery(this).parents('.userpro-bm').data('default_collection');
		post_id = jQuery(this).parents('.userpro-bm').data('post_id');
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_fav_addcollection&post_id='+post_id+'&default_collection='+default_collection+'&collection_name='+collection_name+'&privacy='+privacy,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if(typeof(data.errors) == "undefined" && data.errors == null){
					elem.removeClass('stop');
	                if(/*@cc_on!@*/false )
	                {
						jQuery(this).parents('.userpro-bm').find('#userpro_bm_collection').replaceWith( data.options );
	
						jQuery(this).parents('.userpro-bm').find("select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
						jQuery(this).parents('.userpro-bm').find("*[class*=chzn], .chosen-container").remove();
	                }
	                else
	                {
						parent.find('#userpro_bm_collection').replaceWith( data.options );
						parent.find("select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
						parent.find("*[class*=chzn], .chosen-container").remove();
	                }
					jQuery(".chosen-select-collections").chosen({
						disable_search_threshold: 10,
						width: '100%'
					});
					jQuery(this).parents('.userpro-bm').find('#userpro_bm_collection').val( jQuery(this).parents('.userpro-bm').find('#userpro_bm_collection option:last').val() ).trigger("chosen:updated");
					if(site_is_rtl){
						jQuery(function(){
						jQuery('select').attr('class' , jQuery('select').attr('class')+' chosen-rtl');		
						jQuery('.chosen-container-single').attr('class' , 'chosen-container chosen-container-single chosen-rtl');
						});
					}
					jQuery(this).parents('.userpro-bm').find('.userpro-bm-list select').trigger('change');
					userpro_bm_removedialog();
				}else{
					var cur_elem = elem.parents('.userpro-bm-act').find('.userpro-bm-btn-contain.bm-right');
					userpro_bm_removedialog();
					userpro_bm_limitreached_dialog( cur_elem, 'coll_limit_reached', 'right', data.errors);
				}
			}
		});
		
		}
		return false;
	});
	
	/* chosen jquery */
	jQuery(".chosen-select-collections").chosen({
		disable_search_threshold: 10,
		width: '100%'
	});

	/* New collection */
	jQuery(document).on('click', '.userpro-bm a[data-action=newcollection]', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.userpro-bm');
		
		if ( jQuery(this).parents('.userpro-bm').find('.userpro-bm-dialog form').length == 0){
			userpro_bm_newaction( elem, parent );
			elem.addClass('active');
			userpro_bm_dialog( elem.parent(), 'new_collection', 'right' );
		} else {
			elem.removeClass('active');
			userpro_bm_removedialog();
		}
		
	});

	/* New bookmark */
	jQuery(document).on('click', '.userpro-bm a[data-action=bookmark].unbookmarked:not(.stop)', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.userpro-bm');
  		post_id = jQuery(this).parents('.userpro-bm').data('post_id');
  		collection_id = jQuery(this).parents('.userpro-bm').find('#userpro_bm_collection').val();
        remove_bookmark = jQuery(this).parents('.userpro-bm').data('remove_bookmark');
        dialog_bookmarked = jQuery(this).parents('.userpro-bm').data('dialog_bookmarked');
		
  
 	 	userpro_bm_newaction( elem, jQuery(this).parents('.userpro-bm') );
 	 	
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_fav_newbookmark&post_id='+post_id+'&collection_id='+collection_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if(typeof(data.errors) == "undefined" && data.errors == null){
					userpro_bm_update_active_collection( jQuery(this).parents('.userpro-bm'), data.collection_id );
				 	jQuery('#'+post_id).removeClass('unbookmark').addClass('addedbookmark');
					jQuery('#'+post_id).html('bookmarked');
	  			
					userpro_bm_updatecount(elem, data.updated_count);
					userpro_bm_donebookmark( elem , remove_bookmark );
					userpro_bm_dialog( elem.parent(), dialog_bookmarked );
				}else{
					
					userpro_bm_limitreached_dialog( elem.parent(), 'bm_coll_limit_reached', 'right', data.errors);
					
				}
			}
		});
		return false;
		
	});
	
	/* Remove bookmark */
	jQuery(document).on('click', '.userpro-bm a[data-action=bookmark].bookmarked:not(.stop)', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.userpro-bm');
		post_id = jQuery(this).parents('.userpro-bm').data('post_id');
		collection_id = jQuery(this).parents('.userpro-bm').find('#userpro_bm_collection').val();
		add_to_collection=jQuery(this).parents('.userpro-bm').data('add_to_collection');
                dialog_unbookmarked=jQuery(this).parents('.userpro-bm').data('dialog_unbookmarked');
		/***************************Code added for category bookmark*************************************************/
		category_id=jQuery(this).parents('.userpro-bm').data('category_id');
		/***************************Code End********************************************************************/
		userpro_bm_newaction( elem, parent );
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_fav_removebookmark&post_id='+post_id+'&collection_id='+collection_id+'&category_id='+category_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				userpro_bm_addbookmark( elem, add_to_collection );
				userpro_bm_dialog( elem.parent(), dialog_unbookmarked );
				location.reload();
			}
		});
		return false;
		
	});
	
	/* Remove bookmark */
	jQuery(document).on('click', 'a.userpro-coll-abs', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.userpro-coll-item');
		post_id = elem.data('post_id');
		collection_id = elem.data('collection_id');
		category_id = elem.data('category_id');

		jQuery(this).parents('.userpro-coll-item').fadeOut('fast');
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: 'action=userpro_fav_removebookmark&post_id='+post_id+'&collection_id='+collection_id+'&category_id='+category_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				location.reload();
			}
		});
		return false;

	});

/*******************************************************Code Added By Vipin for category bookmarks*******************************************************************/
		/* New Category Bookmark */
		jQuery(document).on('click', '.userpro-bm a[data-action=bookmarkcategory].unbookmarked_category:not(.stop)', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.userpro-bm');
  		category_id = jQuery(this).parents('.userpro-bm').data('category_id');
		
  		collection_id = jQuery(this).parents('.userpro-bm').find('#userpro_bm_collection').val();
                remove_bookmark_category = jQuery(this).parents('.userpro-bm').data('remove_bookmark_category');
                dialog_bookmarked = jQuery(this).parents('.userpro-bm').data('dialog_bookmarked');
		post_id=jQuery(this).parents('.userpro-bm').data('post_id');
 	 	userpro_bm_newaction( elem, jQuery(this).parents('.userpro-bm') );
		if(typeof(category_id)=='string')
		{
			var category_list=category_id.split(",");
			for(i=0;i<(category_list.length-1);i++)
			{
				if(jQuery(this).data('category')==category_list[i])
				{
					jQuery.ajax({
						url: userpro_ajax_url,
						data: 'action=userpro_fav_newcategorybookmark&category_id='+jQuery(this).data('category')+'&collection_id='+collection_id+'&post_id='+post_id,
						dataType: 'JSON',
						type: 'POST',
						success:function(data){
    							userpro_bm_update_active_collection( jQuery(this).parents('.userpro-bm'), data.collection_id );
    							userpro_bm_donebookmark_category( elem , remove_bookmark_category );
							userpro_bm_dialog( elem.parent(), dialog_bookmarked );
							location.reload();
			}
		});
				}
			}
		}
		else
		{
			jQuery.ajax({
				url: userpro_ajax_url,
				data: 'action=userpro_fav_newcategorybookmark&category_id='+category_id+'&collection_id='+collection_id+'&post_id='+post_id,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
    					userpro_bm_update_active_collection( jQuery(this).parents('.userpro-bm'), data.collection_id );
    					userpro_bm_donebookmark_category( elem , remove_bookmark_category );
					userpro_bm_dialog( elem.parent(), dialog_bookmarked );
					location.reload();
				}
			});
		}
		
		return false;
		
	});
	
	/* Remove category bookmark */
	jQuery(document).on('click', '.userpro-bm a[data-action=bookmarkcategory].bookmarked_category:not(.stop)', function(e){
		elem = jQuery(this);
		parent = jQuery(this).parents('.userpro-bm');
		category_id = jQuery(this).parents('.userpro-bm').data('category_id');
		collection_id = jQuery(this).parents('.userpro-bm').find('#userpro_bm_collection').val();
		bookmark_category=jQuery(this).parents('.userpro-bm').data('bookmark_category');
                dialog_unbookmarked=jQuery(this).parents('.userpro-bm').data('dialog_unbookmarked');
		userpro_bm_newaction( elem, parent );

		if(typeof(category_id)=='string')
		{
			var category_list=category_id.split(",");
			for(i=0;i<(category_list.length-1);i++)
			{
				if(jQuery(this).data('category')==category_list[i])
				{
					jQuery.ajax({
						url: userpro_ajax_url,
						data: 'action=userpro_fav_removecategorybookmark&category_id='+jQuery(this).data('category')+'&collection_id='+collection_id,
						dataType: 'JSON',
						type: 'POST',
						success:function(data){
    							userpro_bm_addbookmark_category( elem , bookmark_category );
							userpro_bm_dialog( elem.parent(), dialog_unbookmarked );
							location.reload();
						}
					});
				}
			}
		}
		
		else
		{
			jQuery.ajax({
				url: userpro_ajax_url,
				data: 'action=userpro_fav_removecategorybookmark&category_id='+category_id+'&collection_id='+collection_id,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
    					userpro_bm_addbookmark_category( elem , bookmark_category );
					userpro_bm_dialog( elem.parent(), dialog_unbookmarked );
					location.reload();
				}
			});
		}

		return false;
		
	});
/*************************Code Ended********************************************************************************************/
	
});

function userpro_profile_bookmark_list( user_id ) {

	jQuery('body').append('<div class="userpro-bookmark-overlay"></div>');
	jQuery('body').append('<div class="userpro-bookmark-overlay-loader"></div>');
	
	/* prepare ajax file */
	str = 'action=userpro_profile_bookmark_list&user_id=' + user_id;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		type: 'POST',
		success:function(data){
			if (jQuery('.userpro-bookmark-overlay-loader').length == 1) {
				jQuery('.userpro-bookmark-overlay-loader').remove();
				jQuery('body').append( data );
				
			}
		}
	});

	
}
function userpro_profile_bookmark_popup(post_id ) {

	jQuery('body').append('<div class="userpro-bookmark-overlay"></div>');
	jQuery('body').append('<div class="userpro-bookmark-overlay-loader"></div>');
	
	/* prepare ajax file */
	str = 'action=userpro_profile_bookmark_popup&post_id=' + post_id;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		type: 'POST',
		success:function(data){
			if (jQuery('.userpro-bookmark-overlay-loader').length == 1) {
				jQuery('.userpro-bookmark-overlay-loader').remove();
				jQuery('body').append( data );
				
			}
		}
	});
}

function userpro_bookmark_icon(post_id,elm){
	
	var condition = jQuery(elm).attr('id');
	str = 'action=userpro_bookmark_icon&post_id='+post_id+'&condition='+condition;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		type: 'POST',
		success:function(data){
			location.reload();
		}
	});
}

function userpro_bookmark_close_overlay() {
	jQuery('.userpro-bookmark-overlay').remove();
	jQuery('.userpro-bookmark-overlay-content').remove();
}

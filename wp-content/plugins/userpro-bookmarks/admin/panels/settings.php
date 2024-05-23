<form method="post" action="">

<h3><?php _e('General Settings','userpro-fav'); ?></h3>
<table class="form-table">

		<tr valign="top">
		<th scope="row"><label for="auto_bookmark"><?php _e('Automatically add bookmark widget after post content','userpro-fav'); ?></label></th>
		<td>
			<select name="auto_bookmark" id="auto_bookmark" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('auto_bookmark')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('auto_bookmark')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
		
	<tr valign="top">
		<th scope="row"><label for="bookmark_widget_type"><?php _e('Bookmark Widget Type','userpro-fav'); ?></label></th>
		<td>
			<select name="bookmark_widget_type" id="bookmark_widget_type" class="chosen-select" style="width:300px">
				<option value="0" <?php selected('0', userpro_fav_get_option('bookmark_widget_type')); ?>><?php _e('Widget','userpro-fav'); ?></option>
				<option value="1" <?php selected('1', userpro_fav_get_option('bookmark_widget_type')); ?>><?php _e('Popup Using a Bookmark Button','userpro-fav'); ?></option>
				<option value="2" <?php selected('2', userpro_fav_get_option('bookmark_widget_type')); ?>><?php _e('Heart Icon','userpro-fav'); ?></option>
			</select>
			<span class="description"><?php _e('Select the bookmark widget type to be displayed after the post content.','userpro-fav'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="allow_multiple_bookmark"><?php _e('Allow posts to be bookmarked in multiple collections','userpro-fav'); ?></label></th>
		<td>
			<select name="allow_multiple_bookmark" id="allow_multiple_bookmark" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('allow_multiple_bookmark')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('allow_multiple_bookmark')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="bookmark_hearticon"><?php _e('Display heart icon on bookmark widget','userpro-fav'); ?></label></th>
		<td>
			<select name="bookmark_hearticon" id="bookmark_hearticon" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('bookmark_hearticon')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('bookmark_hearticon')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="userpro_show_users_avatar"><?php _e('Display avatar of the user who has bookmarked the post','userpro-fav'); ?></label></th>
		<td>
			<select name="userpro_show_users_avatar" id="userpro_show_users_avatar" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('userpro_show_users_avatar')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('userpro_show_users_avatar')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="userpro_show_bookmarks"><?php _e('Display bookmarks on the users profile','userpro-fav'); ?></label></th>
		<td>
			<select name="userpro_show_bookmarks" id="userpro_show_bookmarks" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('userpro_show_bookmarks')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('userpro_show_bookmarks')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>

<tr valign="top">
		<th scope="row"><label for="userpro_show_publicbookmark_guest"><?php _e('Display public bookmarks to non login users','userpro-fav'); ?></label></th>
		<td>
			<select name="userpro_show_publicbookmark_guest" id="userpro_show_publicbookmark_guest" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('userpro_show_publicbookmark_guest')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('userpro_show_publicbookmark_guest')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="include_post_types[]"><?php _e('Enable these post types','userpro-fav'); ?></label></th>
		<td>
			<select name="include_post_types[]" id="include_post_types[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Choose post types','userpro-fav'); ?>">
				<?php
				$array = userpro_admin_fav_get_posttypes();
				foreach($array as $k=>$v) {
				?>
				<option value="<?php echo $k; ?>" <?php userpro_is_selected($k, userpro_fav_get_option('include_post_types') ); ?>><?php echo $v; ?></option>
				<?php } ?>
			</select>
			<span class="description"><?php _e('Select here the post types that can be bookmarked (If you enable auto insertion of bookmark widget)','userpro-fav'); ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="exclude_ids"><?php _e('Exclude these IDs','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="exclude_ids" id="exclude_ids" value="<?php echo userpro_fav_get_option('exclude_ids'); ?>" class="regular-text" />
			<span class="description"><?php _e('For automatic widget insertion, this can exclude the post IDs you specify here (seperated by comma) regardless of any other settings.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="userpro_show_users_avatar"><?php _e('Allow category bookmark','userpro-fav'); ?></label></th>
		<td>
			<select name="userpro_category_bookmark" id="userpro_category_bookmark" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('userpro_category_bookmark')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('userpro_category_bookmark')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="display_loginregister_popup"><?php _e('Display Login and Register form in Popup','userpro-fav'); ?></label></th>
		<td>
			<select name="display_loginregister_popup" id="display_loginregister_popup" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('display_loginregister_popup')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('display_loginregister_popup')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>

<tr valign="top">
		<th scope="row"><label for="display_socialbutton"><?php _e('Display Social Share button on collection','userpro-fav'); ?></label></th>
		<td>
			<select name="display_socialbutton" id="display_socialbutton" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('display_socialbutton')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('display_socialbutton')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>

<tr valign="top">
		<th scope="row"><label for="display_newcollection"><?php _e('Display new collection button','userpro-fav'); ?></label></th>
		<td>
			<select name="display_newcollection" id="display_socialbutton" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('display_newcollection')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('display_newcollection')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="upb_new_collection_limit"><?php _e('Limit the number of new collections to be created','userpro-fav'); ?></label></th>
		<td>
			<input style="width: 35.5%;" type="number" min="1" name="upb_new_collection_limit" id="upb_new_collection_limit" value="<?php echo userpro_fav_get_option('upb_new_collection_limit'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="upb_bookmarks_limit"><?php _e('Limit the number of posts to be bookmarked in collections by a user','userpro-fav'); ?></label></th>
		<td>
			<input style="width: 35.5%;" type="number" min="1" name="upb_bookmarks_limit" id="upb_bookmarks_limit" value="<?php echo userpro_fav_get_option('upb_bookmarks_limit'); ?>" class="regular-text" />
		</td>
	</tr>

	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-fav'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-fav'); ?>"  />
</p>

<p class="upadmin-highlight"><?php _e('These settings can be overridden by shortcode options. They are for general bookmark template that appears on your content. The bookmark sidebar widget may override some settings to make it look perfect in sidebar.','userpro-fav'); ?></p>

<h3><?php _e('Bookmark Shortcode Settings','userpro-fav'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="width"><?php _e('Bookmark Widget Width','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="width" id="width" value="<?php echo userpro_fav_get_option('width'); ?>" class="regular-text" />
			<span class="description"><?php _e('e.g. 250px, 50%, 300px, etc.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="widgetized"><?php _e('Widgetized Look','userpro-fav'); ?></label></th>
		<td>
			<select name="widgetized" id="widgetized" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('widgetized')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('widgetized')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="align"><?php _e('Default Alignment','userpro-fav'); ?></label></th>
		<td>
			<select name="align" id="align" class="chosen-select" style="width:300px">
				<option value="left" <?php selected('left', userpro_fav_get_option('align')); ?>><?php _e('Left','userpro-fav'); ?></option>
				<option value="right" <?php selected('right', userpro_fav_get_option('align')); ?>><?php _e('Right','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="inline"><?php _e('Inline Display','userpro-fav'); ?></label></th>
		<td>
			<select name="inline" id="inline" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('inline')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('inline')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="no_top_margin"><?php _e('Disable top margin','userpro-fav'); ?></label></th>
		<td>
			<select name="no_top_margin" id="no_top_margin" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('no_top_margin')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('no_top_margin')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="no_bottom_margin"><?php _e('Disable bottom margin','userpro-fav'); ?></label></th>
		<td>
			<select name="no_bottom_margin" id="no_bottom_margin" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_fav_get_option('no_bottom_margin')); ?>><?php _e('Yes','userpro-fav'); ?></option>
				<option value="0" <?php selected('0', userpro_fav_get_option('no_bottom_margin')); ?>><?php _e('No','userpro-fav'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="pct_gap"><?php _e('Gap between buttons (%)','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="pct_gap" id="pct_gap" value="<?php echo userpro_fav_get_option('pct_gap'); ?>" class="regular-text" />
			<span class="description"><?php _e('This is used if your bookmark widget width is fluid using percentages.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="px_gap"><?php _e('Gap between buttons (px)','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="px_gap" id="px_gap" value="<?php echo userpro_fav_get_option('px_gap'); ?>" class="regular-text" />
			<span class="description"><?php _e('This is used if your bookmark widget width is fixed using pixels.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="remove_bookmark"><?php _e('Text for "Remove Bookmark"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="remove_bookmark" id="remove_bookmark" value="<?php echo userpro_fav_get_option('remove_bookmark'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="dialog_bookmarked"><?php _e('Text for "Bookmark has been added"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="dialog_bookmarked" id="dialog_bookmarked" value="<?php echo userpro_fav_get_option('dialog_bookmarked'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="dialog_unbookmarked"><?php _e('Text for "Bookmark has been removed"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="dialog_unbookmarked" id="dialog_unbookmarked" value="<?php echo userpro_fav_get_option('dialog_unbookmarked'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="default_collection"><?php _e('Text for "Default Collection"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="default_collection" id="default_collection" value="<?php echo userpro_fav_get_option('default_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="add_to_collection"><?php _e('Text for "Add to Collection"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="add_to_collection" id="add_to_collection" value="<?php echo userpro_fav_get_option('add_to_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="new_collection"><?php _e('Text for "New Collection"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="new_collection" id="new_collection" value="<?php echo userpro_fav_get_option('new_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="new_collection_placeholder"><?php _e('Text for "New Collection Placeholder"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="new_collection_placeholder" id="new_collection_placeholder" value="<?php echo userpro_fav_get_option('new_collection_placeholder'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="add_new_collection"><?php _e('Text for "Submit New Collection"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="add_new_collection" id="add_new_collection" value="<?php echo userpro_fav_get_option('add_new_collection'); ?>" class="regular-text" />
		</td>
	</tr>
	
	<!----------------------------------------------------------------------------------------------------------------->
	<tr valign="top">
		<th scope="row"><label for="bookmark_category"><?php _e('Text for "Bookmark Category"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="bookmark_category" id="bookmark_category" value="<?php echo userpro_fav_get_option('bookmark_category'); ?>" class="regular-text" />
		</td>

	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="remove_bookmark_category"><?php _e('Text for "Remove Category Bookmark"','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="remove_bookmark_category" id="remove_bookmark_category" value="<?php echo userpro_fav_get_option('remove_bookmark_category'); ?>" class="regular-text" />
		</td>
	</tr>
<!----------------------------------------------------------------------------------------------------------------->
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro-fav'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro-fav'); ?>"  />
</p>

</form>

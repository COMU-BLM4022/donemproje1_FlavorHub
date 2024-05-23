<form method="post" action="">

<h3><?php _e('Activate UserPro Bookmarks','userpro-fav'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="bookmark_envato_purchase_code"><?php _e('Enter your Item Purchase Code','userpro-fav'); ?></label></th>
		<td>
			<input type="text" name="bookmark_envato_purchase_code" id="bookmark_envato_purchase_code" value="<?php echo userpro_fav_get_option( 'bookmark_envato_purchase_code' ); ?>" class="regular-text" />
			<span class="description"><?php _e('Enter Envato Purchase Code to enable automatic updates.','userpro-fav'); ?></span>
		</td>
	</tr>
</table>

<p class="submit">
   <input type="submit" name="up_fav_license_verify" id="up_fav_license_verify" class="button button-primary" value="<?php _e('Save Changes','userpro-fav'); ?>"/>
</p>

</form>

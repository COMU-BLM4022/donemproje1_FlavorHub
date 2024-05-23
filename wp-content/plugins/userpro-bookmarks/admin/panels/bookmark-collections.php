<form method="post" action="">
	
	<h3><?php _e('Bookmark Collections','userpro-fav'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<td><label for="collection_title" style="font-weight: bolder;"><?php _e('Collection Title','userpro-fav'); ?></label></td>
			<td><label for="collection_type" style="font-weight: bolder"><?php _e('Collection Privacy','userpro-fav'); ?></label></td>
			<td><label for="collection_action" style="font-weight: bolder"><?php _e('Collection Action','userpro-fav'); ?></label></td>
		</tr>
		<?php 
			$collections = get_option('admin_default_collections');
			if(is_array($collections)){
				foreach($collections as $k => $collection){
					?>
					<tr valign="top">
						<td><label for="collection_title" style="font-weight: bolder;"><?php echo ucfirst($collection['label']); ?></label></td>
						<td><label for="collection_privacy" style="font-weight: bolder;"><?php echo ucfirst($collection['privacy']); ?></label></td>
						<td class = "collection-action">
							<div class="collection_delete_btn" style="display:inline-block;" id= "collection_delete_<?php echo $k;?>"></div>
							<input type="hidden" value="<?php echo $k;?>" id="collection_id">
						</td>
					</tr>
					<?php 
				}
			}
		?>
		<tr id="add-collection-tr" valign="top">
			<td style="width:41.5%;">
				<input type="button" class="button" style="" name="userpro-bookmark-add-collection" value="<?php _e('Add New Collection','userpro-fav'); ?>" id="userpro-bookmark-add-collection" />
				<input type="button" class="button" style="display:none;" name="userpro-bookmark-add-collection-cancel" value="<?php _e('Cancel','userpro-fav'); ?>" id="userpro-bookmark-add-collection-cancel" />	
			</td>
		</tr>
	</table>
</form>
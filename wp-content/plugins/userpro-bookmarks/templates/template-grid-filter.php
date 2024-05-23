<?php
	
	if (!isset($array['label'])) { $array=array(); $array['label'] = $default_collection; }
	if( $id == '0' ){ $class = 'active visited'; } else { $class = ''; }
	?>
	<button class="upb-button button <?php echo $class;?> collection_<?php echo $id;?>" id="<?php echo $id;?>">
		<a href="#collection_<?php echo $id;?>" data-collection_id="<?php echo $id;?>"><?php echo $array['label'];?></a>
	</button>
	
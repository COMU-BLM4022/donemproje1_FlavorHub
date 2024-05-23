
<div class="upb-single upb-item collection_<?php echo $id;?> active">
    <p class="upb-thumb"><a href="<?php echo get_permalink($bkid);?>"><?php echo $userpro->post_thumb($bkid, 100);?></a></p>
    <p class="upb-title"><a href="<?php echo get_permalink($bkid);?>"><?php echo get_the_title($bkid);?></a></p>
    <p class="upb-action-remove"><i class="userpro-icon-trash" data-post_id="<?php echo $bkid;?>" data-collection_id="<?php echo $id;?>"></i></p>
</div>
  	
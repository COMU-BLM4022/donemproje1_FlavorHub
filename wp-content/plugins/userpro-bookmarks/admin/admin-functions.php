<?php

	function userpro_admin_fav_get_posttypes(){
		$types = get_post_types( array('public' => true) , 'objects');
		foreach($types as $type){
			$array[$type->name] = $type->labels->menu_name;
		}
		return $array;
	}
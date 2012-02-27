<?php
$action = $_POST['action'];
if($action == 'delete') :
	global $wpdb;
	$table_1 = $wpdb->prefix . 'gb_batch';
	$table_2 = $wpdb->prefix . 'gb_promocode';
	$table_3 = $wpdb->prefix . 'gb_date';
	
	//get the batch ids
	$ids = $_POST['check'];
	if($ids) :
		foreach($ids as $id){
			$id = (int)$id;		
			$wpdb->query("DELETE FROM $table_1 WHERE `id`='$id'");
			$wpdb->query("DELETE FROM $table_2 WHERE `batch_id`='$id'");
			$wpdb->query("DELETE FROM $table_3 WHERE `batch_id`='$id'");
			
			//delete meta key from			
			$meta_key = 'promocode_' . $id;
			$wpdb->query("DELETE FROM $wpdb->usermeta WHERE `meta_key`='$meta_key'");
		}
		$del_msg = 1;
	endif;
	
endif;
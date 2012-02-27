<?php
delete_post_meta($cart_id,'promocode_status');

$coded = trim($_POST['promocode_remove_code']);
global $current_user;
get_currentuserinfo();

global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';	
$id = $wpdb->get_var("SELECT `batch_id` FROM $table_2 WHERE `code`='$coded'");
    	
$meta_key = 'promocode_' . $id;
$user_meta = get_user_meta($current_user->ID,$meta_key,true);

if($user_meta){	
	$new = array();
	foreach($user_meta as $code){
		if($coded != $code){
			$new[] = $code;
		}
	}
	
	update_user_meta($current_user->ID,$meta_key,$new);
}
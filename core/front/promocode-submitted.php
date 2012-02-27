<?php
$meta = '';
$code = trim($_POST['promocode_value']);				
global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';				
$code_details = $wpdb->get_row("SELECT * FROM $table_2 WHERE `code`='$code'");
if($code_details){
	$batch = $wpdb->get_row("SELECT * FROM $table_1 WHERE `id`='$code_details->batch_id'");
	if($batch->expire < time()){
		$message = '<div class="error"><p>Sorry, this code has expired</p></div>';
		
	}
	elseif($code_details->used != 'n'){
		$message = '<div class="error"><p>Sorry, this code has already been used</p></div>';
		
	}
	elseif($batch->status !== ''){
		$message = '<div class="error"><p>The code has been deactivated by the admin</p></div>';
		
	}
	else{
		
		//updating user profiles
		global $current_user;
      	get_currentuserinfo();     	
      	$meta_key = 'promocode_'.$batch->id;
      	$user_meta = get_user_meta($current_user->ID,$meta_key,true);
      	
      	if(!$user_meta){
      		$user_meta = array();
      	}
      	
      	if(count($user_meta)>$batch->per_user || count($user_meta)==$batch->per_user){
      		$message = '<div class="error"><p>Sorry, you may not use this offer again</p></div>';
      	}      	
      	else{ 
			$meta = array(
			'done' => 1,
			'code' => $code,
			'message' => ""
			);
			
			//updating cart information
			update_post_meta($cart_id,'promocode_status',$meta);
      		
      		$user_meta[] = $code;
      		update_user_meta($current_user->ID,$meta_key,$user_meta);
      	}      	     	
      		
	}
}
else{
	$message = '<div class="error"><span>Invalid code - please try again</span></div>';
	
}
if(!is_array($meta)){			
	$meta = array(
			'done' => 0,
			'code' => $code,
			'message' => $message
		);
	update_post_meta($cart_id,'promocode_status',$meta);
}

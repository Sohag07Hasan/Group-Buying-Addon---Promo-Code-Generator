<?php
/*
 * Save the created batches and promocodes into the database
 * */

$batch_name = trim($_POST['batch_name']);
$promocode_name = trim($_POST['promo_name']);
$discount = trim($_POST['batch_discount']);
$discount_limit = trim($_POST['batch_discount_limit']);
$code_amount = (int)trim($_POST['promocode_amount']);
$per_user = trim($_POST['max_per_user']);
$expire = $_POST['promoexpire'];
$status = $_POST['batch_status'];

// if the batch is edited
$batch_edit_id = trim($_POST['batch_edit']);
$edit_option = $_POST['promocode_radio'];

global $PromoCodeFront;
global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';
$table_3 = $wpdb->prefix . 'gb_date';

/** Code generation time and date **/
$time = time();
$date = date("m/d/Y",$time);


/*
 * Generated code properties
 * */

$promo_settings = get_option('promocode_settings');
$length = $promo_settings['length'];
$sc = ($promo_settings['sc'] == 'y') ? true : false;
$esc = ($promo_settings['esc'] == 'y') ? true : false;
// end of prmomo setting

//if the batch is new
if($batch_edit_id == '') :
	//batch data adding
	$batch_data = array(
			'batch' => $batch_name,
			'name' => $promocode_name,
			'discount' => (int)$discount,
			'min_price' => (int)$discount_limit,
			'per_user' => (int)$per_user,
			'status' => $status,
			'expire' => strtotime($expire)
	);
	$batch_data_type = array('%s','%s','%d','%d','%s','%s');	
	$wpdb->insert($table_1,$batch_data,$batch_data_type);
	$batch_id = $wpdb->insert_id;		
	
	$wpdb->insert($table_3,array('batch_id'=>$batch_id,'date'=>$date),array('%d','%s'));	
	
	if($batch_id) : 
		for($i=1;$i<=$code_amount;$i++){
			$code_data = array(
				'batch_id' => $batch_id,
				'code' => $PromoCodeFront->wp_generate_password($length,$sc,$esc),
				'date' => $date
			);
			$code_data_type = array('%d','%s','%s');			
			$wpdb->insert($table_2,$code_data,$code_data_type);
		}	
	endif;
	
else :
	//updating the existing batch
	$batch_edit_id = (int)$batch_edit_id;
	$batch_id = $batch_edit_id;
	$batch_data = array(
			'batch' => $batch_name,
			'name' => $promocode_name,
			'discount' => (int)$discount,
			'min_price' => (int)$discount_limit,
			'per_user' => (int)$per_user,
			'status' => $status,
			'expire' => strtotime($expire)
	);
	$batch_data_type = array('%s','%s','%d','%d','%s','%s');	
	$wpdb->update($table_1,$batch_data,array('id'=>$batch_edit_id),$batch_data_type,array('%d'));
			
	$edit_option = (int)$edit_option;
	
	if($edit_option == 2){
		//date updateing
		$check = $wpdb->get_row("SELECT * FROM $table_3 WHERE `batch_id`='$batch_id' AND `date`='$date' ");
		if(!$check){
			$wpdb->insert($table_3,array('batch_id'=>$batch_id,'date'=>$date),array('%d','%s'));
		}
		
		for($i=1;$i<=$code_amount;$i++){
			$code_data = array(
				'batch_id' => $batch_edit_id,
				'code' => $PromoCodeFront->wp_generate_password($length,$sc,$esc),
				'date' => $date
			);
			$code_data_type = array('%d','%s','%s');
			
			$wpdb->insert($table_2,$code_data,$code_data_type);
		}	
	}
	
	if($edit_option == 3){
		$wpdb->query("DELETE FROM $table_2 WHERE `batch_id`='$batch_edit_id'");
		$wpdb->query("DELETE FROM $table_3 WHERE `batch_id`='$batch_edit_id'");
		$wpdb->insert($table_3,array('batch_id'=>$batch_id,'date'=>$date),array('%d','%s'));
		
		for($i=1;$i<=$code_amount;$i++){
			$code_data = array(
				'batch_id' => $batch_edit_id,
				'code' => $PromoCodeFront->wp_generate_password($length,$sc,$esc),
				'date' => $date
			);
			$code_data_type = array('%d','%s','%s');			
			$wpdb->insert($table_2,$code_data,$code_data_type);
		}
	}

endif;


	
$home = get_option('siteurl');
$edit_link = $home . "/wp-admin/admin.php?page=promocode_addition&msg=y&edit=yes&id=$batch_id";

if(!function_exists('wp_redirect')){
	include ABSPATH . '/wp-includes/pluggable.php';
}

wp_redirect($edit_link);
exit;
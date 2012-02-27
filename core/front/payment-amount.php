<?php
/*
 * this script will run for total amount
 * for sanitizing
 * 
 * */


$code = $meta['code'];
global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';			
$batch_id = $wpdb->get_var("SELECT `batch_id` FROM $table_2 WHERE `code`='$code' AND `used`='n'");
if($batch_id){
	$batch = $wpdb->get_row("SELECT * FROM $table_1 WHERE `id`='$batch_id' AND `status`=''");
	if($batch->expire > time()){
		$discount = $batch->discount;
		$price = $total;
		$price = preg_replace('/[^0-9.]/','',$price);
		$price = (float)$price;
		if($discount == 100){
			$com = $price;
		}
		else{
			$com = $price*$discount/100;
		}
				
		$com = ($com > $batch->min_price) ? $batch->min_price : $com;
		$total = $price - $com;		
							
	}
}
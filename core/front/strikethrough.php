<?php
$code = $meta['code'];
global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';			
$batch_id = $wpdb->get_var("SELECT `batch_id` FROM $table_2 WHERE `code`='$code' AND `used`='n'");
if($batch_id){
	$batch = $wpdb->get_row("SELECT * FROM $table_1 WHERE `id`='$batch_id' AND `status`=''");
	if($batch->expire > time()){
		$discount = $batch->discount;
		$price = $line_items['total']['data'];
		$price = preg_replace('/[^0-9.]/','',$price);
		$price = (float)$price;		
		if($discount == 100){
			$com = $price;
		}
		else{
			$com = $price*$discount/100;
		}
		
		
		$com = ($com > $batch->min_price) ? $batch->min_price : $com;
		$intake = $price;
		$price = $price - $com;
		
		$price = apply_filters('promocode_price_filter', $price, $intake);
		
		
		$new_items = array();
		foreach($line_items as $key=>$value){		
			
			if($key != 'total'){
				$new_items[$key] = $value;
			}
		}
		$new_items['new_total'] = $line_items['total'];	
		$new_items['new_total']['weight'] = '20';	
		$new_items['total'] = array(
			'label' => Group_Buying_Carts::__('Discounted Total'),
			'data' => gb_get_formatted_money($price)
		);
		$new_items['total']['weight'] = $line_items['total']['weight'];
					
	}
	else{
		$new_items = $line_items;
	}	
}
else{
	$new_items = $line_items;
}


<?php
$batch_no = (int)$_REQUEST['id'];

global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';
$table_3 = $wpdb->prefix . 'gb_date';

$batch_data = $wpdb->get_row("SELECT * FROM $table_1 WHERE `id`='$batch_no'");
$dates = $wpdb->get_col("SELECT `date` FROM $table_3 WHERE `batch_id`='$batch_no'");
$codes = array();
if($dates) : 
	foreach($dates as $date){
		$codes[$date] = $wpdb->get_col("SELECT `code` FROM $table_2 WHERE `batch_id`='$batch_no' AND `date`='$date'");
	}
$s_codes = $wpdb->get_col("SELECT `code` FROM $table_2 WHERE `batch_id`='$batch_no'");	
endif;
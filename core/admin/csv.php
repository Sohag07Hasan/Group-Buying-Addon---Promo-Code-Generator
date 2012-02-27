<?php
$date = $_POST['promodate'];
$batch = (int)$_POST['promo-code-batch'];
$type = $_POST['type-of-code'];

global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';

if($type == 'all'){
	$codes = $wpdb->get_col("SELECT `code` FROM $table_2 WHERE `batch_id`='$batch' AND `date`='$date'"); 
}
else{
	$codes = $wpdb->get_col("SELECT `code` FROM $table_2 WHERE `batch_id`='$batch' AND `date`='$date' AND `used`='$type'");
}

$string = '';
foreach ($codes as $key=>$code){
	$string .= $code . "\n";
	
}
//var_dump($string);
//exit;

$naming = $wpdb->get_row("SELECT `batch`,`name` FROM $table_1 WHERE `id`='$batch'");
$batch_name = preg_replace('/[ ]/','-',$naming->batch);
$code_name = preg_replace('/[ ]/','-',$naming->name);
$date_name = preg_replace('/[\/]/','-',$date);

$name = $batch_name . '_' . $code_name . '_' . $date_name . '.csv';
header('Content-type: text/csv');
header("Content-disposition: attachment;filename=$name");
echo $string;
exit;
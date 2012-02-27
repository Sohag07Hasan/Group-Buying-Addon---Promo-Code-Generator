<?php

$code = $meta['code'];

global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';				

$batch_id = $wpdb->get_var("SELECT `batch_id` FROM $table_2 WHERE `code`='$code' AND `used`='n'");
//$code_details = $wpdb->get_row("SELECT * FROM $table_2 WHERE `code`='$code'");
if($batch_id){
	$batch = $wpdb->get_row("SELECT * FROM $table_1 WHERE `id`='$batch_id' AND `status`=''");
	if($batch->expire > time()){		
		$new_control['promocode'] = "<input type='hidden' name='promocode_remove_batch' value='$batch_id' /><input type='hidden' name='promocode_remove_code' value='$code' /><div class='gb-promocode-rm-div'><p>Promo Code Applied: $batch->name <input type='submit' name='promocode_remove' value='Remove' /></p></div>";
	}
	else{
		$new_control['promocode'] = '<div class="gb-promocode-div"><span style="">If you have a promotional code, please enter it here and click "Apply"</span> <input class="promocode_gb" type="text" name="promocode_value" value="" /> <input class="promocode-submit-button" type="submit" name="promocode_submit" value="Apply" class="promocode_gbs" /></div>';
	}	
}
else{
	$new_control['promocode'] = '<div class="gb-promocode-div"><span style="">If you have a promotional code, please enter it here and click "Apply"</span> <input class="promocode_gb" type="text" name="promocode_value" value="" /> <input class="promocode-submit-button" type="submit" name="promocode_submit" value="Apply" class="promocode_gbs" /></div>';
}
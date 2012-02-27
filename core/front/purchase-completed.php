<?php
$code = $meta['code'];
global $wpdb;
$table_2 = $wpdb->prefix . 'gb_promocode';
$wpdb->update($table_2,array('used'=>''),array('code'=>$code),array('%s'),array('%s'));
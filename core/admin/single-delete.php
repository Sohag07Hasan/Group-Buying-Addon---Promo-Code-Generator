<?php
$id = (int)$_REQUEST['id'];
global $wpdb;
$table_1 = $wpdb->prefix . 'gb_batch';
$table_2 = $wpdb->prefix . 'gb_promocode';
$table_3 = $wpdb->prefix . 'gb_date';

$wpdb->query("DELETE FROM $table_1 WHERE `id`='$id'");
$wpdb->query("DELETE FROM $table_2 WHERE `batch_id`='$id'");
$wpdb->query("DELETE FROM $table_3 WHERE `batch_id`='$id'");

$meta_key = 'promocode_' . $id;
$wpdb->query("DELETE FROM $wpdb->usermeta WHERE `meta_key`='$meta_key'");
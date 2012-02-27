<?php
/*
 * Save the options for promocode
 * */

$settings = array(
	'length' => $_POST['length'],
	'sc' => $_POST['sc'],
	'esc' => $_POST['esc']		
);

update_option('promocode_settings',$settings);
$message = 1;
<?php
unset($fields['cc_name']);
unset($fields['cc_number']);
unset($fields['cc_expiration']);
unset($fields['cc_cvv']);
$fields['free_deal'] = array(
	'label' => __('Free'),
	'value' => __('On the house.'),
	'weight' => 10,
);
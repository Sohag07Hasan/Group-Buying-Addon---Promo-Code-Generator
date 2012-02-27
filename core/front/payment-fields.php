<?php
unset($fields['cc_name']);
unset($fields['cc_number']);
unset($fields['cc_expiration']);
unset($fields['cc_expiration_month']);
unset($fields['cc_expiration_year']);
unset($fields['cc_cvv']);
unset($fields['payment_method']);

$fields['free_deal'] = array(
	'type' => 'hidden',
	'weight' => -5,
	'label' => __('Free'),
	'attributes' => array(

	),
	'description' =>__('No credit card information is required. Your purchase is FREE. We still require your complete billing address to process this order.'),
	'size' => 10,
	'required' => FALSE,
	'value' => 'TRUE',
);
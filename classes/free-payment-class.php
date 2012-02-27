<?php
class free_payment_class extends Group_Buying_Payment_Processors{
	function __construct(){
		remove_action('processing_payment',array($this,'register_free_payment_method'));
		add_action('processing_payment',array($this,'process_payment'),20,2);		
	}
	
	function process_payment(Group_Buying_Checkouts $checkout, Group_Buying_Purchase $purchase){
		$items = $purchase->get_products();
		var_dump($items);					
		foreach ( $items as $key => $item ) {
			if ( $items[$key]['payment_method'][0] != Group_Buying_Affiliate_Credit_Payments::get_payment_method() ) {
				if ( $item['price'] < 0.01 ) {
					$items[$key]['payment_method'][Group_Buying_Affiliate_Credit_Payments::get_payment_method()] = '0';
				}
			}
			
		}
		$purchase->set_products($items);
		var_dump($purchase->get_products());
		exit;		
	}
}
<?php
/*
 * Add the promocode input field in the .../cart page
 * Creates the database table
 * Deletes the database table if necessary
 * 
 * *  
 * */
 
 if(!class_exists('group_buying_promocode')) : 
	
	class group_buying_promocode{
		
		var $total = 100;
			
		//all hooks are here
		function __construct(){
			
			//promo code filed adding
			add_filter('gb_cart_controls',array($this,'promocode_adding'),10,2);
						
			//style adding
			add_action('wp_print_styles',array($this,'css_addition'));
			
			//database manipulation
			register_activation_hook(PromoFile, array($this,'table_creation'));
			
			//database cleartion
			//register_deactivation_hook(PromoFile,array($this,'table_delete'));

			//show stirke though
			add_filter('gb_cart_line_items',array($this,'price_discounted'),10,2);
			
			// this is for directly filter the total value
			//add_filter('gb_cart_get_total',array($this,'strikethrough_total'),10,2);
			
			//save the promocode with cart
			add_filter('gb_cart_load_products_get',array($this,'cart_initiated'),10,2);
			
			//empty the cart and deactivate the promocode
			add_action('checkout_completed',array($this,'purchased_completed'),10,3);
			
			//sanitizing the the total amount by calling these function for authorise.net
			//add_filter('gbs_get_subtotal_purchase',array($this,'subtotal_authorise_net'),10,3);
			//add_filter('gbs_get_shipping_total_purchase',array($this,'subtotal_authorise_net'),10,1);
			//add_filter('gbs_get_tax_total_purchase',array($this,'subtotal_authorise_net'),10,1);
			add_filter('gb_authorize_net_nvp_data',array($this,'total_authorise_net'));
			
			//default page changing gb_checkout_pages
			//add_filter('gb_checkout_panes',array($this,'display_payment_page'),50,2);
			add_filter('group_buying_template_checkout/cart.php',array($this,'display_payment_page'),50);
			
			//credit card information chaning for checkout page
			add_filter('gb_payment_fields', array($this, 'payment_fields'), 50, 3 );
			
			//payment review page
			add_filter('gb_payment_review_fields', array($this, 'payment_review_fields'), 50, 3);
			
			// this will force the payment free
			add_filter('gb_new_purchase_args',array($this,'free_buying'),10);
			
			add_action('processing_payment',array($this,'process_payment'),50,2);
			
			add_action('gb_new_purchase',array($this,'gb_new_purchase'),20,2);
			
			add_action('checkout_failed', array($this, 'hooktocustomfileter'));
			
			add_filter('gb_cart_get_total',array($this,'strikethrough_total'),10,2);
		}
		
		function hooktocustomfileter(){
			//add_filter('promocode_price_filter', array($this, 'promocode_price_set_when_transaction_failted'), 10, 2);
		}
		
		function promocode_price_set_when_transaction_failted($price, $intake){			
			if($_REQUEST['gb_checkout_button'] == 'Submit Order'){
				return $intake;
			}
			return $price;
		}
		
		function gb_new_purchase($purchase, $args){
			
		}
		
		function process_payment(Group_Buying_Checkouts $checkout, Group_Buying_Purchase $purchase){			
			
			if ( $this->total == 0 ) {
				$items = $purchase->get_products();
				
				// Free deals
					foreach ( $items as $key => $item ) {
						$items[$key]['payment_method'][Group_Buying_Affiliate_Credit_Payments::get_payment_method()] = '0';
						$items[$key]['price'] = '0';
						$items[$key]['unit_price'] = '0';
						foreach($items[$key]['payment_method'] as $a=>$b){
							if($a == Group_Buying_Affiliate_Credit_Payments::get_payment_method()) continue;
							unset($items[$key]['payment_method'][$a]);
						}
					}
					$purchase->set_products($items);
								
	
			}
			
				
		}
		
		function free_buying($purchase_args){			
			
			$total = $purchase_args['cart']->get_total();
			//var_dump($total);
			/*
			
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);	
			
			if($meta['done'] == 1){
				include PromoDir . '/core/front/payment-amount.php';
			}
			*/
			if($total == 0){				
				$purchase_args['checkout']->cache['free_deal'] = TRUE ;
				//remove_action('gb_checkout_action',array('Group_Buying_Credit_Card_Processors','process_credit_card_cache'));			
				$this->total = 0;
			}
						
			return $purchase_args;
		}
		
		//payment review page
		function payment_review_fields($fields, $payment_processor_class, Group_Buying_Checkouts $checkout ){
			$cart = Group_Buying_Cart::get_instance();
			$total = $cart->get_total();
			if($total == 0) return $fields;
			
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
			
			if($meta['done'] == 1){
				include PromoDir . '/core/front/payment-amount.php';				
			}
			if($total == 0){
				include PromoDir . '/core/front/payment-review-fields.php';
			}
			return $fields;
			
		}
		
		//payment field
		function payment_fields($fields,$payment_processor_class,$checkout){
			$account = Group_Buying_Account::get_instance();
		//	var_dump($fields);
			//exit;
			$cart = Group_Buying_Cart::get_instance();
			$total = $cart->get_total();
			if($total == 0) return $fields;
			
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
			
			if($meta['done'] == 1){
				include PromoDir . '/core/front/payment-amount.php';
				
				unset($fields['affiliate_credits']);				
			}
			
			
			
			if($total == 0){
				include PromoDir . '/core/front/payment-fields.php';
				//remove_action('gb_checkout_action',array('Group_Buying_Credit_Card_Processors','credit_card_cache_pane'));
			}
			return $fields;
		}
		
		//payment page populating
		function display_payment_page($file){
			return PromoDir . '/views/checkout/cart.php';	
		}
				
		
		/*
		//subtotal sanitization
		function subtotal_authorise_net($total){
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
			if($meta['done'] == 1){
				include PromoDir . '/core/front/payment-amount.php';
			}
			return $total;
		}
		*/
		//ultimate total amount
		function total_authorise_net($AIMdata){
			$total = $AIMdata['x_amount'];			
			
			
		//	var_dump($AIMdata['x_amount']);
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
			if($meta['done'] == 1){
				include PromoDir . '/core/front/payment-amount.php';
				$AIMdata['x_amount'] = gb_get_number_format($total);
			}
						
			return $AIMdata;			
		}
		
		//purchase completed
		function purchased_completed($cart,$payment,$purchase){
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
			include PromoDir . '/core/front/purchase-completed.php';
			
		}
		
		// the cart has initiated and promocode submitted or remove
		function cart_initiated($products,$cart){			
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();			
			if($_POST['promocode_submit'] == 'Apply'):
				include PromoDir . '/core/front/promocode-submitted.php';					
			endif;
			if($_POST['promocode_remove'] == 'Remove') :
				include PromoDir . '/core/front/promocode-removed.php';				
			endif;			
			
			return $products;
		}

		function strikethrough_total($total,$cart){
			
			$this->original = $total;
			
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
			
		
			
			if($meta['done'] == 1){
				include PromoDir . '/core/front/payment-amount.php';
				$this->discounted = $total;		
			}	
						
			return $total;
		}
		
		//strikethrough.php 
		function price_discounted($line_items,$cart){
						
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
			
				if($meta['done'] == 1) :
					include PromoDir . '/core/front/strikethrough_out.php';													
				else:
					$new_items = $line_items;
				endif;		
			
			return $new_items;
						
		}
		
	//promocode input button addition
		function promocode_adding($controls,$cart){				
			$new_control = array();
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);
						
			if($meta['done'] == 1):
				include PromoDir . '/core/front/promo-field.php';						
			else:
				if($_POST['promocode_submit'] == 'Apply'){
					$new_control['message'] = $meta['message'];
				}
				$new_control['promocode'] = '<div class="gb-promocode-div"><span style="">If you have a promotional code, please enter it here and click "Apply"</span> <input class="promocode_gb" type="text" name="promocode_value" value="" /> <input class="promocode-submit-button" type="submit" name="promocode_submit" value="Apply" class="promocode_gbs" /></div>';
				
			endif;
						
			foreach($controls as $key=>$cont){
				$new_control[$key] = $cont;
			}
		
			return $new_control;
				
		}
		
		//delete the database table
		function table_delete(){
			global $wpdb;
			$table_1 = $wpdb->prefix . 'gb_batch';
			$table_2 = $wpdb->prefix . 'gb_promocode';
			$table_3 = $wpdb->prefix . 'gb_date';
			
			$wpdb->query("DROP TABLE $table_1");
			$wpdb->query("DROP TABLE $table_2");
			$wpdb->query("DROP TABLE $table_3");
		}
				
		//create the menupage
		function create_the_menu(){
			add_menu_page(__('promocode management'),__('Batches & Promocodes'),'activate_plugins','gb_promo_code',array($this,'batch_page'));
		}
	
		//database table creation
		function table_creation(){
			global $wpdb;
			$table_1 = $wpdb->prefix . 'gb_batch';
			$table_2 = $wpdb->prefix . 'gb_promocode';
			$table_3 = $wpdb->prefix . 'gb_date';
						
			$sql_1 = "CREATE TABLE IF NOT EXISTS $table_1(
				`id` bigint unsigned NOT NULL AUTO_INCREMENT,
				`batch` varchar(200) NOT NULL,
				`name` varchar(300) NOT NULL,
				`discount` int DEFAULT 0,
				`min_price` int DEFAULT 0,
				`per_user` int DEFAULT 1,																
				`status` varchar(100) DEFAULT 'on',
				`expire` bigint DEFAULT 0,
				PRIMARY KEY(id)				
			)";
			
			$sql_2 = "CREATE TABLE IF NOT EXISTS $table_2(
				`batch_id` bigint,				
				`code` varchar(50),																
				`used` varchar(5) DEFAULT 'n',
				`date` varchar(50),
				UNIQUE(code)				
			)";
			
			$sql_3 = "CREATE TABLE IF NOT EXISTS $table_3(
				`batch_id` bigint,
				`date` varchar(50)
			)";
			
			if(!function_exists('dbDelta')) :
				include ABSPATH . 'wp-admin/includes/upgrade.php';
			endif;
			
			dbDelta($sql_1);
			dbDelta($sql_2);
			dbDelta($sql_3);
			
		}
		
		//style adding
		function css_addition(){
			wp_register_style('gb_promocode_style',PromoLink.'/assets/css/promocode.css');
			wp_enqueue_style('gb_promocode_style');
		}
		
		
		
		//password generator
		function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			if ( $special_chars )
				$chars .= '!@#$%^&*()';
			if ( $extra_special_chars )
				$chars .= '-_ []{}<>~`+=,.;:/?|';
		
			$password = '';
			for ( $i = 0; $i < $length; $i++ ) {
				$password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
			}
		
			// random_password filter was previously in random_password function which was deprecated
			return $this->unique_code_generate($password);
		} //password generator
		

		//creating a unique promocode
		function unique_code_generate($password){
			global $wpdb;
			$table = $wpdb->prefix . 'gb_promocode';			
			$result = $wpdb->get_var("SELECT `batch_id` FROM $table WHERE `code`='$password'");
			
			if($result){
				//settings for the promocode
				$promo_settings = get_option('promocode_settings');
				$length = $promo_settings['length'];
				$sc = ($promo_settings['sc'] == 'y') ? true : false;
				$esc = ($promo_settings['esc'] == 'y') ? true : false;				
				return $this->wp_generate_password($length,$sc,$esc);
			}
			else{
				return $password;
			}
		}
		
	}
	
	$PromoCodeFront = new group_buying_promocode();
	
 endif;
?>

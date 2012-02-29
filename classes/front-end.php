<?php
/*
 * Addd the promocode input field in the .../cart page
 * Changes the checkout page in the every ../checkout page
 * Filters Final payment 
 * Creates the database table
 * Deletes the database table if necessary
 * 
 * *  
 * */
 
 if(!class_exists('group_buying_promocode')) : 
	
	class group_buying_promocode{
		
		/*
		 * Stores information , If Promocode is applied, this varialbe's value is true and it allows to use affilite credits
		 * It 
		 * */
		var $promocode_used = false;
			
		/*
		 * Constructor functions
		 * All hooks goes here
		 * */
		function __construct(){
			
			//promo code filed adding
			add_filter('gb_cart_controls',array($this,'gb_cart_controls'),10,2);
						
			//style adding
			add_action('wp_print_styles',array($this,'css_addition'));
			
			//database manipulation
			register_activation_hook(PromoFile, array($this,'table_creation'));
			
			//database cleartion
			//register_deactivation_hook(PromoFile,array($this,'table_delete'));

			//show stirke though
			add_filter('gb_cart_line_items',array($this,'gb_cart_line_items'),10,2);			
			
			//Saves the Promocode with the cart or remove promocode from the cart
			add_filter('gb_cart_load_products_get',array($this,'promocode_add_or_remove'),10,2);
			
			//Deactivates the used promocode
			add_action('checkout_completed',array($this,'checkout_completed'),10,3);
		
			
			//credit card fields are filtered
			add_filter('gb_payment_fields', array($this, 'gb_payment_fields'), 50, 3 );		
			
			//Filters the total value and return the discounted value
			add_filter('gb_cart_get_total',array($this,'gb_cart_get_total'),10,2);
			
			//FIlters the final payment price into discounted value
			add_filter('gb_purchase_get_total', array($this, 'gb_purchase_get_total'), 100);
		}		
		
		
		/*
		 * Filters the Final Payment.
		 * directly replace the total value with the discounted valule
		 * */
		function gb_purchase_get_total($total){
			if($this->promocode_used){
				$total = $this->discounted;
			}			
			return $total;
		}
			
			
		
		/*
		 * Removes the Affiliate payment options fromt the credit card information if a promocode is applied
		 * */
		function gb_payment_fields($fields,$payment_processor_class,$checkout){				
			
			if($this->promocode_used){
				unset($fields['affiliate_credits']);
			}
			
			return $fields;
		}
				
		
		/*
		 * Change the status of the promcode completed if promocode is applied
		 * */
		function checkout_completed($cart,$payment,$purchase){
			if($this->promocode_used) :	
					
				$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
				$meta = get_post_meta($cart_id,'promocode_status',true);
				include PromoDir . '/core/front/purchase-completed.php';
				
			endif;
		}
		
		
		/*
		 * Adds or removes if an users tries any one with a valid code
		 * Shows error messages if some error occurs 
		 * */
		function promocode_add_or_remove($products,$cart){			
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();			
			if($_POST['promocode_submit'] == 'Apply'):
				include PromoDir . '/core/front/promocode-submitted.php';					
			endif;
			if($_POST['promocode_remove'] == 'Remove') :
				include PromoDir . '/core/front/promocode-removed.php';				
			endif;			
			
			return $products;
		}
		
		
		/*
		 * Filters the total value for the cart and different checkout pages
		 * Sets some information based on promocode used 
		 * */
		function gb_cart_get_total($total,$cart){
			
			$this->original = $total;
			
			$cart_id = Group_Buying_Cart :: get_cart_id_for_user();
			$meta = get_post_meta($cart_id,'promocode_status',true);		
			
			if($meta['done'] == 1){
				include PromoDir . '/core/front/payment-amount.php';
				$this->discounted = $total;		
			}	
			
			if($this->original > $total){
				$this->promocode_used = true;
			}
						
			return $total;
		}
		
		
		/*
		 * Filters the line intems
		 * Make the total strikeout
		 * Shows the discounted amount as a row
		 * */
		function gb_cart_line_items($line_items,$cart){
			
			if($this->promocode_used) :
				$new_items = $line_items;
				$line_items['new_total'] = $new_items['total'];
				$line_items['new_total']['data'] = gb_get_formatted_money($this->original);

				$line_items['total'] = array(
							'label' => Group_Buying_Carts::__('Discounted Total'),
							'data' => gb_get_formatted_money($this->discounted),
							'weight' => 1000
						);										
			endif;
			return $line_items;
						
		}
		
		
		/*
		 * Adds an input field to set the promocode 
		 * It also adds an button to submit or remove the promocode
		 * */
		function gb_cart_controls($controls,$cart){				
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
		
		
		/* If you are not a developer don't do anything to the function
		 * It deletes the table if the register_deactivation_hook hooks is activated in the __consturct table
		 * */
		function table_delete(){
			global $wpdb;
			$table_1 = $wpdb->prefix . 'gb_batch';
			$table_2 = $wpdb->prefix . 'gb_promocode';
			$table_3 = $wpdb->prefix . 'gb_date';
			
			$wpdb->query("DROP TABLE $table_1");
			$wpdb->query("DROP TABLE $table_2");
			$wpdb->query("DROP TABLE $table_3");
		}
		
		
				
		/*
		 * Creates a Management page to manipulate the promocodes
		 * */
		function create_the_menu(){
			add_menu_page(__('promocode management'),__('Batches & Promocodes'),'activate_plugins','gb_promo_code',array($this,'batch_page'));
		}
	
	
	
		/*
		 * Creates tables to store the information into the database for batches and promocodes
		 * */
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
		
		
		/*
		 * Css Adding
		 * */
		function css_addition(){
			wp_register_style('gb_promocode_style',PromoLink.'/assets/css/promocode.css');
			wp_enqueue_style('gb_promocode_style');
		}
		
		
		
		/*
		 * Creates formatted unique codes
		 * */
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
		
		

		/*
		 * Creates the unique codes
		 * */
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

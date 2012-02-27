<?php
/*
 * plugin name: Group Buying Addon - Promo Code Generator
 * plugin uri: http://valustop.com
 * author: Mahibul Hasan Sohag
 * author uri: http://sohag.me
 * version: 1.0.0
 * Description: It creates a input filed in the cart for inserting promo code. Once the code is applied the corresponding discount will be deduced from total payment.
 * 
 * */
 
 define('PromoLink',plugins_url('',__FILE__));
 define('PromoDir',dirname(__FILE__));
 define('PromoFile',__FILE__);
 
 include PromoDir . '/classes/front-end.php';
 include PromoDir . '/classes/settings.php';
 
?>
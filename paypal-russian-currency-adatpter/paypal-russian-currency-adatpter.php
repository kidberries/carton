<?php 
/*
Plugin Name: Woocommerce PayPal адаптер для России
Plugin URI: http://kidberries.com
Description: PayPal адаптер для России - Это расширение позволит магазину принимать электронные платежи в системе PayPal, когда основная валюта вашего магазина определена в рублях.

Version: 1.0.0
Author: Kidberries.com team
Author URI: http://kidberries.com/
*/

/* Add a custom payment class to woocommerce
  ------------------------------------------------------------ */
define('PPRCA_PLUGIN_DIR_PATH_PAYPAL',plugin_dir_path(__FILE__));
define('PPRCA_PLUGIN_DIR_PATH_PAYPAL_CLASSES', PPRCA_PLUGIN_DIR_PATH_PAYPAL . 'classes/');

if( !defined( 'PPRCA_PLUGIN_VERSION_PAYPAL' ) )
	define( 'PPRCA_PLUGIN_VERSION_PAYPAL', '1.0.0' );
//END


function paypal_russian_currency_adatpter() {
	if ( !class_exists('WC_Payment_Gateway') )
		return; // if the woocommerce payment gateway class is not available, do nothing
	include_once (PPRCA_PLUGIN_DIR_PATH_PAYPAL_CLASSES . 'class-wc-gateway-paypal.php');
}
add_action('plugins_loaded', 'paypal_russian_currency_adatpter', 0);
?>
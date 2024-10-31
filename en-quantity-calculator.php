<?php

/**
 * Plugin Name:       Quantity Calculator for Woocommerce
 * Plugin URI:        https://eniture.com/products/
 * Description:       Calculates the amount of product needed based on customer input for area or cubic volume.
 * Version:           1.0.2
 * Requires at least: 6.4
 * Author:            Eniture Technology
 * Author URI:        https://eniture.com/
 * License:           GPLv2 or later
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * plugin version.
 */
define('ENITURE_QUANTITY_CALCULATOR_VERSION', '1.0.0');
define('ENITURE_QUANTITY_CALCULATOR_DOCUMENTATION_URL', 'https://eniture.com/woocommerce-quantity-calculator');
define('ENITURE_QUANTITY_CALCULATOR_BASE_NAME', plugin_basename( __FILE__ ));
define('ENITURE_QUANTITY_CALCULATOR_SUPPORT_URL', 'https://support.eniture.com');
define('ENITURE_QUANTITY_CALCULATOR_DOMAIN_URL', 'https://ws001.eniture.com/qtyCalc/quotes.php');

/**
 * compatibility check
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-en-quantity-calculator-guard.php';
$eniture_quantity_calculator_guard = new Eniture_Quantity_Calculator_Guard('Quantity Calculator for Woocommerce', '5.6', '5.0', '3.0');
if(empty($eniture_quantity_calculator_guard->eniture_quantity_calculator_check_prerequisites())){
	/**
	 * The core plugin class that is used to define internationalization,
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-en-quantity-calculator.php';

	/**
	 * Begins execution of the plugin.
	 */
	function eniture_quantity_calculator_init() {

		$plugin = new Eniture_Quantity_Calculator();
		$plugin->eniture_quantity_calculator_load();
	}

	eniture_quantity_calculator_init();

}


<?php
/**
 * Init/register importers for CartoN.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Importers
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

register_importer( 'carton_tax_rate_csv', __( 'CartoN Tax Rates (CSV)', 'carton' ), __( 'Import <strong>tax rates</strong> to your store via a csv file.', 'carton'), 'carton_tax_rates_importer' );

/**
 * carton_tax_rates_importer function.
 *
 * @access public
 * @return void
 */
function carton_tax_rates_importer() {

	// Load Importer API
	require_once ABSPATH . 'wp-admin/includes/import.php';

	if ( ! class_exists( 'WP_Importer' ) ) {
		$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		if ( file_exists( $class_wp_importer ) )
			require $class_wp_importer;
	}

	// includes
	require dirname( __FILE__ ) . '/tax-rates-importer.php';

	// Dispatch
	$CTN_CSV_Tax_Rates_Import = new CTN_CSV_Tax_Rates_Import();

	$CTN_CSV_Tax_Rates_Import->dispatch();
}
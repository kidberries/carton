<?php
/**
 * CartoN Updates
 *
 * Plugin updates script which updates the database.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Updates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Runs the installer.
 *
 * @access public
 * @return void
 */
function do_update_carton() {
	global $carton;

	// Include installer so we have page creation functions
	include_once( 'carton-admin-install.php' );

	// Do updates
	$current_db_version = get_option( 'carton_db_version' );

	if ( version_compare( $current_db_version, '1.4', '<' ) ) {
		include( 'includes/updates/carton-update-1.4.php' );
		update_option( 'carton_db_version', '1.4' );
	}

	if ( version_compare( $current_db_version, '1.5', '<' ) ) {
		include( 'includes/updates/carton-update-1.5.php' );
		update_option( 'carton_db_version', '1.5' );
	}

	if ( version_compare( $current_db_version, '2.0', '<' ) ) {
		include( 'includes/updates/carton-update-2.0.php' );
		update_option( 'carton_db_version', '2.0' );
	}

	update_option( 'carton_db_version', $carton->version );
}
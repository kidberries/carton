<?php
/**
 * CartoN Uninstall
 *
 * Uninstalling CartoN deletes user roles, options, tables, and pages.
 *
 * @author 		CartonThemes
 * @category 	Core
 * @package 	CartoN/Uninstaller
 * @version     1.6.4
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();

global $wpdb, $wp_roles;

// Roles + caps
if ( ! function_exists( 'carton_remove_roles' ) )
	include_once( 'carton-core-functions.php' );

if ( function_exists( 'carton_remove_roles' ) )
	carton_remove_roles();

// Pages
wp_delete_post( get_option('carton_shop_page_id'), true );
wp_delete_post( get_option('carton_cart_page_id'), true );
wp_delete_post( get_option('carton_checkout_page_id'), true );
wp_delete_post( get_option('carton_myaccount_page_id'), true );
wp_delete_post( get_option('carton_edit_address_page_id'), true );
wp_delete_post( get_option('carton_view_order_page_id'), true );
wp_delete_post( get_option('carton_change_password_page_id'), true );
wp_delete_post( get_option('carton_pay_page_id'), true );
wp_delete_post( get_option('carton_thanks_page_id'), true );

// mijireh checkout page
if ( $mijireh_page = get_page_by_path( 'mijireh-secure-checkout' ) )
	wp_delete_post( $mijireh_page->ID, true );

// Tables
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "carton_attribute_taxonomies" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "carton_downloadable_product_permissions" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "carton_termmeta" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "shareyourcart_tokens" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->base_prefix . "shareyourcart_coupons" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "carton_tax_rates" );
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "carton_tax_rate_locations" );

// Delete options
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'carton_%';");
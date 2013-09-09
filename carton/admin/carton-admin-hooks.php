<?php
/**
 * CartoN Admin Hooks
 *
 * Action/filter hooks used for CartoN functions.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Events
 *
 * @see carton_delete_post()
 * @see carton_trash_post()
 * @see carton_untrash_post()
 * @see carton_preview_emails()
 * @see carton_prevent_admin_access()
 * @see carton_check_download_folder_protection()
 * @see carton_ms_protect_download_rewite_rules()
 */
add_action('delete_post', 'carton_delete_post');
add_action('wp_trash_post', 'carton_trash_post');
add_action('untrash_post', 'carton_untrash_post');
add_action('admin_init', 'carton_preview_emails');
add_action('admin_init', 'carton_prevent_admin_access');
add_action('carton_settings_saved', 'carton_check_download_folder_protection');
add_filter('mod_rewrite_rules', 'carton_ms_protect_download_rewite_rules');

/**
 * File uploads
 *
 * @see carton_downloads_upload_dir()
 * @see carton_media_upload_downloadable_product()
 */
add_filter('upload_dir', 'carton_downloads_upload_dir');
add_action('media_upload_downloadable_product', 'carton_media_upload_downloadable_product');

/**
 * Shortcode buttons
 *
 * @see carton_add_shortcode_button()
 * @see carton_refresh_mce()
 */
add_action( 'init', 'carton_add_shortcode_button' );
add_filter( 'tiny_mce_version', 'carton_refresh_mce' );

/**
 * Category/term ordering
 *
 * @see carton_create_term()
 * @see carton_delete_term()
 */
add_action( "create_term", 'carton_create_term', 5, 3 );
add_action( "delete_term", 'carton_delete_term', 5 );

/**
 * Bulk editing
 *
 * @see carton_bulk_admin_footer()
 * @see carton_order_bulk_action()
 * @see carton_order_bulk_admin_notices()
 */
add_action( 'admin_footer', 'carton_bulk_admin_footer', 10 );
add_action( 'load-edit.php', 'carton_order_bulk_action' );
add_action( 'admin_notices', 'carton_order_bulk_admin_notices' );

/**
 * Mijireh Gateway
 */
add_action( 'add_meta_boxes', array( 'CTN_Gateway_Mijireh', 'add_page_slurp_meta' ) );
add_action( 'wp_ajax_page_slurp', array( 'CTN_Gateway_Mijireh', 'page_slurp' ) );
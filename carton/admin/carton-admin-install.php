<?php
/**
 * CartoN Install
 *
 * Plugin install script which adds default pages, taxonomies, and database tables to WordPress. Runs on activation and upgrade.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Install
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Runs the installer.
 *
 * @access public
 * @return void
 */
function do_install_carton() {
	global $carton_settings, $carton;

	// Do install
	carton_default_options();
	carton_tables_install();
	carton_init_roles();

	// Register post types
	$carton->init_taxonomy();

	// Add default taxonomies
	carton_default_taxonomies();

	// Cron jobs
	wp_clear_scheduled_hook( 'carton_scheduled_sales' );
	wp_clear_scheduled_hook( 'carton_cancel_unpaid_orders' );
	wp_clear_scheduled_hook( 'carton_cleanup_sessions' );

	$ve = get_option('gmt_offset') > 0 ? '+' : '-';

	wp_schedule_event( strtotime( 'tomorrow ' . $ve . get_option('gmt_offset') . ' HOURS' ), 'daily', 'carton_scheduled_sales');

	$held_duration = get_option( 'carton_hold_stock_minutes', null );

	if ( is_null( $held_duration ) )
		$held_duration = '60';

	if ( $held_duration != '' )
		wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 ), 'carton_cancel_unpaid_orders' );

	wp_schedule_event( time(), 'twicedaily', 'carton_cleanup_sessions' );

	// Install files and folders for uploading files and prevent hotlinking
	$upload_dir =  wp_upload_dir();

	$files = array(
		array(
			'base' 		=> $upload_dir['basedir'] . '/carton_uploads',
			'file' 		=> '.htaccess',
			'content' 	=> 'deny from all'
		),
		array(
			'base' 		=> $upload_dir['basedir'] . '/carton_uploads',
			'file' 		=> 'index.html',
			'content' 	=> ''
		),
		array(
			'base' 		=> WP_PLUGIN_DIR . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/logs',
			'file' 		=> '.htaccess',
			'content' 	=> 'deny from all'
		),
		array(
			'base' 		=> WP_PLUGIN_DIR . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/logs',
			'file' 		=> 'index.html',
			'content' 	=> ''
		)
	);

	foreach ( $files as $file ) {
		if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
			if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
				fwrite( $file_handle, $file['content'] );
				fclose( $file_handle );
			}
		}
	}

	// Clear transient cache
	$carton->clear_product_transients();

	// Recompile LESS styles if they are custom
	if ( get_option( 'carton_frontend_css' ) == 'yes' ) {

		$colors = get_option( 'carton_frontend_css_colors' );

		if ( ( ! empty( $colors['primary'] ) && ! empty( $colors['secondary'] ) && ! empty( $colors['highlight'] ) && ! empty( $colors['content_bg'] ) && ! empty( $colors['subtext'] ) ) && ( $colors['primary'] != '#ad74a2' || $colors['secondary'] != '#f7f6f7' || $colors['highlight'] != '#85ad74' || $colors['content_bg'] != '#ffffff' || $colors['subtext'] != '#777777' ) )
			carton_compile_less_styles();

	}

	// Queue upgrades
	$current_version = get_option( 'carton_version', null );
	$current_db_version = get_option( 'carton_db_version', null );

	if ( version_compare( $current_db_version, '2.0', '<' ) && null !== $current_db_version ) {
		update_option( '_ctn_needs_update', 1 );
	} else {
		update_option( 'carton_db_version', $carton->version );
	}

	// Update version
	update_option( 'carton_version', $carton->version );

	// Flush rewrite rules
	flush_rewrite_rules();
}


/**
 * Default options
 *
 * Sets up the default options used on the settings page
 *
 * @access public
 * @return void
 */
function carton_default_options() {
	global $carton_settings;

	// Include settings so that we can run through defaults
	include_once( 'settings/settings-init.php' );

	foreach ( $carton_settings as $section ) {
		foreach ( $section as $value ) {
	        if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
	        	add_option( $value['id'], $value['default'] );
	        }
        }
    }
}


/**
 * Create a page
 *
 * @access public
 * @param mixed $slug Slug for the new page
 * @param mixed $option Option name to store the page's ID
 * @param string $page_title (default: '') Title for the new page
 * @param string $page_content (default: '') Content for the new page
 * @param int $post_parent (default: 0) Parent for the new page
 * @return void
 */
function carton_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0 && get_post( $option_value ) )
		return;

	$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT \"ID\" FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
	if ( $page_found ) {
		if ( ! $option_value )
			update_option( $option, $page_found );
		return;
	}

	$page_data = array(
        'post_status' 		=> 'publish',
        'post_type' 		=> 'page',
        'post_author' 		=> 1,
        'post_name' 		=> $slug,
        'post_title' 		=> $page_title,
        'post_content' 		=> $page_content,
        'post_parent' 		=> $post_parent,
        'comment_status' 	=> 'closed'
    );
    $page_id = wp_insert_post( $page_data );

    update_option( $option, $page_id );
}


/**
 * Create pages that the plugin relies on, storing page id's in variables.
 *
 * @access public
 * @return void
 */
function carton_create_pages() {

	// Shop page
    carton_create_page( esc_sql( _x( 'shop', 'page_slug', 'carton' ) ), 'carton_shop_page_id', __( 'Shop', 'carton' ), '' );

    // Cart page
    carton_create_page( esc_sql( _x( 'cart', 'page_slug', 'carton' ) ), 'carton_cart_page_id', __( 'Cart', 'carton' ), '[carton_cart]' );

	// Checkout page
    carton_create_page( esc_sql( _x( 'checkout', 'page_slug', 'carton' ) ), 'carton_checkout_page_id', __( 'Checkout', 'carton' ), '[carton_checkout]' );

	// My Account page
    carton_create_page( esc_sql( _x( 'my-account', 'page_slug', 'carton' ) ), 'carton_myaccount_page_id', __( 'My Account', 'carton' ), '[carton_my_account]' );

	// Lost password page
	carton_create_page( esc_sql( _x( 'lost-password', 'page_slug', 'carton' ) ), 'carton_lost_password_page_id', __( 'Lost Password', 'carton' ), '[carton_lost_password]', carton_get_page_id( 'myaccount' ) );

	// Edit address page
    carton_create_page( esc_sql( _x( 'edit-address', 'page_slug', 'carton' ) ), 'carton_edit_address_page_id', __( 'Edit My Address', 'carton' ), '[carton_edit_address]', carton_get_page_id( 'myaccount' ) );

    // View order page
    carton_create_page( esc_sql( _x( 'view-order', 'page_slug', 'carton' ) ), 'carton_view_order_page_id', __( 'View Order', 'carton' ), '[carton_view_order]', carton_get_page_id( 'myaccount' ) );

    // Change password page
    carton_create_page( esc_sql( _x( 'change-password', 'page_slug', 'carton' ) ), 'carton_change_password_page_id', __( 'Change Password', 'carton' ), '[carton_change_password]', carton_get_page_id( 'myaccount' ) );

    // Logout page
    carton_create_page( esc_sql( _x( 'logout', 'page_slug', 'carton' ) ), 'carton_logout_page_id', __( 'Logout', 'carton' ), '', carton_get_page_id( 'myaccount' ) );

	// Pay page
    carton_create_page( esc_sql( _x( 'pay', 'page_slug', 'carton' ) ), 'carton_pay_page_id', __( 'Checkout &rarr; Pay', 'carton' ), '[carton_pay]', carton_get_page_id( 'checkout' ) );

    // Thanks page
    carton_create_page( esc_sql( _x( 'order-received', 'page_slug', 'carton' ) ), 'carton_thanks_page_id', __( 'Order Received', 'carton' ), '[carton_thankyou]', carton_get_page_id( 'checkout' ) );
}


/**
 * Set up the database tables which the plugin needs to function.
 *
 * Tables:
 *		carton_attribute_taxonomies - Table for storing attribute taxonomies - these are user defined
 *		carton_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
 *		carton_downloadable_product_permissions - Table for storing user and guest download permissions.
 *			KEY(order_id, product_id, download_id) used for organizing downloads on the My Account page
 *		carton_order_items - Order line items are stored in a table to make them easily queryable for reports
 *		carton_order_itemmeta - Order line item meta is stored in a table for storing extra data.
 *		carton_tax_rates - Tax Rates are stored inside 2 tables making tax queries simple and efficient.
 *		carton_tax_rate_locations - Each rate can be applied to more than one postcode/city hence the second table.
 *
 * @access public
 * @return void
 */
function carton_tables_install() {
	global $wpdb, $carton;
return;
	$wpdb->hide_errors();

	$collate = '';

    if ( $wpdb->has_cap( 'collation' ) ) {
		if( ! empty($wpdb->charset ) )
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		if( ! empty($wpdb->collate ) )
			$collate .= " COLLATE $wpdb->collate";
    }

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // CartoN Tables
    $carton_tables = "
CREATE TABLE {$wpdb->prefix}carton_attribute_taxonomies (
  attribute_id bigint(20) NOT NULL auto_increment,
  attribute_name varchar(200) NOT NULL,
  attribute_label longtext NULL,
  attribute_type varchar(200) NOT NULL,
  attribute_orderby varchar(200) NOT NULL,
  PRIMARY KEY  (attribute_id),
  KEY attribute_name (attribute_name)
) $collate;
CREATE TABLE {$wpdb->prefix}carton_termmeta (
  meta_id bigint(20) NOT NULL auto_increment,
  carton_term_id bigint(20) NOT NULL,
  meta_key varchar(255) NULL,
  meta_value longtext NULL,
  PRIMARY KEY  (meta_id),
  KEY carton_term_id (carton_term_id),
  KEY meta_key (meta_key)
) $collate;
CREATE TABLE {$wpdb->prefix}carton_downloadable_product_permissions (
  download_id varchar(32) NOT NULL,
  product_id bigint(20) NOT NULL,
  order_id bigint(20) NOT NULL DEFAULT 0,
  order_key varchar(200) NOT NULL,
  user_email varchar(200) NOT NULL,
  user_id bigint(20) NULL,
  downloads_remaining varchar(9) NULL,
  access_granted datetime NOT NULL default '0000-00-00 00:00:00',
  access_expires datetime NULL default null,
  download_count bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY  (product_id,order_id,order_key,download_id),
  KEY download_order_product (download_id,order_id,product_id)
) $collate;
CREATE TABLE {$wpdb->prefix}carton_order_items (
  order_item_id bigint(20) NOT NULL auto_increment,
  order_item_name longtext NOT NULL,
  order_item_type varchar(200) NOT NULL DEFAULT '',
  order_id bigint(20) NOT NULL,
  PRIMARY KEY  (order_item_id),
  KEY order_id (order_id)
) $collate;
CREATE TABLE {$wpdb->prefix}carton_order_itemmeta (
  meta_id bigint(20) NOT NULL auto_increment,
  order_item_id bigint(20) NOT NULL,
  meta_key varchar(255) NULL,
  meta_value longtext NULL,
  PRIMARY KEY  (meta_id),
  KEY order_item_id (order_item_id),
  KEY meta_key (meta_key)
) $collate;
CREATE TABLE {$wpdb->prefix}carton_tax_rates (
  tax_rate_id bigint(20) NOT NULL auto_increment,
  tax_rate_country varchar(200) NOT NULL DEFAULT '',
  tax_rate_state varchar(200) NOT NULL DEFAULT '',
  tax_rate varchar(200) NOT NULL DEFAULT '',
  tax_rate_name varchar(200) NOT NULL DEFAULT '',
  tax_rate_priority bigint(20) NOT NULL,
  tax_rate_compound int(1) NOT NULL DEFAULT 0,
  tax_rate_shipping int(1) NOT NULL DEFAULT 1,
  tax_rate_order bigint(20) NOT NULL,
  tax_rate_class varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY  (tax_rate_id),
  KEY tax_rate_country (tax_rate_country),
  KEY tax_rate_state (tax_rate_state),
  KEY tax_rate_class (tax_rate_class),
  KEY tax_rate_priority (tax_rate_priority)
) $collate;
CREATE TABLE {$wpdb->prefix}carton_tax_rate_locations (
  location_id bigint(20) NOT NULL auto_increment,
  location_code varchar(255) NOT NULL,
  tax_rate_id bigint(20) NOT NULL,
  location_type varchar(40) NOT NULL,
  PRIMARY KEY  (location_id),
  KEY location_type (location_type),
  KEY location_type_code (location_type,location_code)
) $collate;
";
    dbDelta( $carton_tables );
}


/**
 * Add the default terms for WC taxonomies - product types and order statuses. Modify this at your own risk.
 *
 * @access public
 * @return void
 */
function carton_default_taxonomies() {

	$taxonomies = array(
		'product_type' => array(
			'simple',
			'grouped',
			'variable',
			'external'
		),
		'shop_order_status' => array(
			'pending',
			'failed',
			'on-hold',
			'processing',
			'completed',
			'refunded',
			'cancelled'
		)
	);

	foreach ( $taxonomies as $taxonomy => $terms ) {
		foreach ( $terms as $term ) {
			if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
				wp_insert_term( $term, $taxonomy );
			}
		}
	}
}
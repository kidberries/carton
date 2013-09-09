<?php
/**
 * CartoN Admin
 *
 * Main admin file which loads all settings panels and sets up admin menus.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Functions for the product post type
 */
include_once( 'post-types/product.php' );

/**
 * Functions for the shop_coupon post type
 */
include_once( 'post-types/shop_coupon.php' );

/**
 * Functions for the shop_discount post type
 */
include_once( 'post-types/shop_discount.php' );

/**
 * Functions for the shop_order post type
 */
include_once( 'post-types/shop_order.php' );

/**
 * Hooks in admin
 */
include_once( 'carton-admin-hooks.php' );

/**
 * Functions in admin
 */
include_once( 'carton-admin-functions.php' );

/**
 * Functions for handling taxonomies
 */
include_once( 'carton-admin-taxonomies.php' );

/**
 * Welcome Page
 */
include_once( 'includes/welcome.php' );

/**
 * Setup the Admin menu in WordPress
 *
 * @access public
 * @return void
 */


function carton_admin_menu() {
    global $menu, $carton;

    if ( current_user_can( 'manage_carton' ) )
    $menu[] = array( '', 'read', 'separator-carton', '', 'wp-menu-separator carton' );

    $main_page = add_menu_page( __( 'CartoN', 'carton' ), __( 'CartoN', 'carton' ), 'manage_carton', 'carton' , 'carton_settings_page', null, '55.5' );

    $reports_page = add_submenu_page( 'carton', __( 'Reports', 'carton' ),  __( 'Reports', 'carton' ) , 'view_carton_reports', 'carton_reports', 'carton_reports_page' );

    add_submenu_page( 'edit.php?post_type=product', __( 'Attributes', 'carton' ), __( 'Attributes', 'carton' ), 'manage_product_terms', 'carton_attributes', 'carton_attributes_page');

    add_action( 'load-' . $main_page, 'carton_admin_help_tab' );
    add_action( 'load-' . $reports_page, 'carton_admin_help_tab' );

    $print_css_on = apply_filters( 'carton_screen_ids', array( 'toplevel_page_carton', 'carton_page_carton_settings', 'carton_page_carton_reports', 'carton_page_carton_status', 'product_page_carton_attributes', 'edit-tags.php', 'edit.php', 'index.php', 'post-new.php', 'post.php' ) );

    foreach ( $print_css_on as $page )
    	add_action( 'admin_print_styles-'. $page, 'carton_admin_css' );
   
}

add_action('admin_menu', 'carton_admin_menu', 9);

/**
 * Setup the Admin menu in WordPress - later priority so they appear last
 *
 * @access public
 * @return void
 */
function carton_admin_menu_after() {
	$settings_page = add_submenu_page( 'carton', __( 'CartoN Settings', 'carton' ),  __( 'Settings', 'carton' ) , 'manage_carton', 'carton_settings', 'carton_settings_page');
	$status_page = add_submenu_page( 'carton', __( 'CartoN Status', 'carton' ),  __( 'System Status', 'carton' ) , 'manage_carton', 'carton_status', 'carton_status_page');

	add_action( 'load-' . $settings_page, 'carton_settings_page_init' );
}

add_action('admin_menu', 'carton_admin_menu_after', 50);

/**
 * Loads gateways and shipping methods into memory for use within settings.
 *
 * @access public
 * @return void
 */
function carton_settings_page_init() {
	$GLOBALS['carton']->payment_gateways();
	$GLOBALS['carton']->shipping();
}

/**
 * Highlights the correct top level admin menu item for post type add screens.
 *
 * @access public
 * @return void
 */
function carton_admin_menu_highlight() {
	global $menu, $submenu, $parent_file, $submenu_file, $self, $post_type, $taxonomy;

	$to_highlight_types = array( 'shop_order', 'shop_coupon' );

	if ( isset( $post_type ) ) {
		if ( in_array( $post_type, $to_highlight_types ) ) {
			$submenu_file = 'edit.php?post_type=' . esc_attr( $post_type );
			$parent_file  = 'carton';
		}

		if ( 'product' == $post_type ) {
			$screen = get_current_screen();

			if ( $screen->base == 'edit-tags' && 'pa_' == substr( $taxonomy, 0, 3 ) ) {
				$submenu_file = 'carton_attributes';
				$parent_file  = 'edit.php?post_type=' . esc_attr( $post_type );
			}
		}
	}

	if ( isset( $submenu['carton'] ) && isset( $submenu['carton'][2] ) ) {
		$submenu['carton'][0] = $submenu['carton'][2];
		unset( $submenu['carton'][2] );
	}

	// Sort out Orders menu when on the top level
	if ( ! current_user_can( 'manage_carton' ) ) {
		foreach ( $menu as $key => $menu_item ) {
			if ( strpos( $menu_item[0], _x('Orders', 'Admin menu name', 'carton') ) === 0 ) {

				$menu_name = _x('Orders', 'Admin menu name', 'carton');
				$menu_name_count = '';
				if ( $order_count = carton_processing_order_count() ) {
					$menu_name_count = " <span class='awaiting-mod update-plugins count-$order_count'><span class='processing-count'>" . number_format_i18n( $order_count ) . "</span></span>" ;
				}

				$menu[$key][0] = $menu_name . $menu_name_count;
				$submenu['edit.php?post_type=shop_order'][5][0] = $menu_name;
				break;
			}
		}
	}
    
        
}

add_action( 'admin_head', 'carton_admin_menu_highlight' );


/**
 * carton_admin_notices_styles function.
 *
 * @access public
 * @return void
 */
function carton_admin_notices_styles() {

	if ( get_option( '_ctn_needs_update' ) == 1 || get_option( '_ctn_needs_pages' ) == 1 ) {
		wp_enqueue_style( 'carton-activation', plugins_url(  '/assets/css/activation.css', dirname( __FILE__ ) ) );
		add_action( 'admin_notices', 'carton_admin_install_notices' );
	}

	$template = get_option( 'template' );

	if ( ! current_theme_supports( 'carton' ) && ! in_array( $template, array( 'twentyeleven', 'twentytwelve', 'twentyten' ) ) ) {

		if ( ! empty( $_GET['hide_carton_theme_support_check'] ) ) {
			update_option( 'carton_theme_support_check', $template );
			return;
		}

		if ( get_option( 'carton_theme_support_check' ) !== $template ) {
			wp_enqueue_style( 'carton-activation', plugins_url(  '/assets/css/activation.css', dirname( __FILE__ ) ) );
			add_action( 'admin_notices', 'carton_theme_check_notice' );
		}

	}

}

add_action( 'admin_print_styles', 'carton_admin_notices_styles' );


/**
 * carton_theme_check_notice function.
 *
 * @access public
 * @return void
 */
function carton_theme_check_notice() {
	include( 'includes/notice-theme-support.php' );
}


/**
 * carton_admin_install_notices function.
 *
 * @access public
 * @return void
 */
function carton_admin_install_notices() {
	global $carton;

	// If we need to update, include a message with the update button
	if ( get_option( '_ctn_needs_update' ) == 1 ) {
		include( 'includes/notice-update.php' );
	}

	// If we have just installed, show a message with the install pages button
	elseif ( get_option( '_ctn_needs_pages' ) == 1 ) {
		include( 'includes/notice-install.php' );
	}
}

/**
 * Include some admin files conditonally.
 *
 * @access public
 * @return void
 */
function carton_admin_init() {
	global $pagenow, $typenow;

	ob_start();

	// Install - Add pages button
	if ( ! empty( $_GET['install_carton_pages'] ) ) {

		require_once( 'carton-admin-install.php' );
		carton_create_pages();

		// We no longer need to install pages
		delete_option( '_ctn_needs_pages' );
		delete_transient( '_ctn_activation_redirect' );

		// What's new redirect
		wp_safe_redirect( admin_url( 'index.php?page=wc-about&wc-installed=true' ) );
		exit;

	// Skip button
	} elseif ( ! empty( $_GET['skip_install_carton_pages'] ) ) {

		// We no longer need to install pages
		delete_option( '_ctn_needs_pages' );
		delete_transient( '_ctn_activation_redirect' );

		// Flush rules after install
		flush_rewrite_rules();

		// What's new redirect
		wp_safe_redirect( admin_url( 'index.php?page=wc-about' ) );
		exit;

	// Update button
	} elseif ( ! empty( $_GET['do_update_carton'] ) ) {

		include_once( 'carton-admin-update.php' );
		do_update_carton();

		// Update complete
		delete_option( '_ctn_needs_pages' );
		delete_option( '_ctn_needs_update' );
		delete_transient( '_ctn_activation_redirect' );

		// What's new redirect
		wp_safe_redirect( admin_url( 'index.php?page=wc-about&wc-updated=true' ) );
		exit;
	}

	// Includes
	if ( $typenow == 'post' && isset( $_GET['post'] ) && ! empty( $_GET['post'] ) ) {
		$typenow = $post->post_type;
	} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	    $post = get_post( $_GET['post'] );
	    $typenow = $post->post_type;
	}

	if ( $pagenow == 'index.php' ) {

		include_once( 'carton-admin-dashboard.php' );

	} elseif ( $pagenow == 'admin.php' && isset( $_GET['import'] ) ) {

		include_once( 'carton-admin-import.php' );

	} elseif ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {

		include_once( 'post-types/writepanels/writepanels-init.php' );

		if ( in_array( $typenow, array( 'product', 'shop_coupon', 'shop_order' ) ) )
			add_action('admin_print_styles', 'carton_admin_help_tab');

	} elseif ( $pagenow == 'users.php' || $pagenow == 'user-edit.php' || $pagenow == 'profile.php' ) {

		include_once( 'carton-admin-users.php' );

	}

	// Register importers
	if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
		include_once( 'importers/importers-init.php' );
	}
}

add_action('admin_init', 'carton_admin_init');


/**
 * Include and display the settings page.
 *
 * @access public
 * @return void
 */
function carton_settings_page() {
	include_once( 'carton-admin-settings.php' );
	carton_settings();
}

/**
 * Include and display the reports page.
 *
 * @access public
 * @return void
 */
function carton_reports_page() {
	include_once( 'carton-admin-reports.php' );
	carton_reports();
}

/**
 * Include and display the attibutes page.
 *
 * @access public
 * @return void
 */
function carton_attributes_page() {
	include_once( 'carton-admin-attributes.php' );
	carton_attributes();
}

/**
 * Include and display the status page.
 *
 * @access public
 * @return void
 */
function carton_status_page() {
	include_once( 'carton-admin-status.php' );
	carton_status();
}


/**
 * Include and add help tabs to WordPress admin.
 *
 * @access public
 * @return void
 */
function carton_admin_help_tab() {
	include_once( 'carton-admin-content.php' );
	carton_admin_help_tab_content();
}


/**
 * Include admin scripts and styles.
 *
 * @access public
 * @return void
 */
function carton_admin_scripts() {
    global $carton, $pagenow, $post, $wp_query;

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Register scripts
	wp_register_script( 'carton_admin', $carton->plugin_url() . '/assets/js/admin/carton_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-placeholder', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $carton->version );

	wp_register_script( 'jquery-blockui', $carton->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), $carton->version, true );

	wp_register_script( 'jquery-placeholder', $carton->plugin_url() . '/assets/js/jquery-placeholder/jquery.placeholder' . $suffix . '.js', array( 'jquery' ), $carton->version, true );

	wp_register_script( 'jquery-tiptip', $carton->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), $carton->version, true );

	wp_register_script( 'carton_writepanel', $carton->plugin_url() . '/assets/js/admin/write-panels'.$suffix.'.js', array('jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'), $carton->version );

	wp_register_script( 'ajax-chosen', $carton->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery'.$suffix.'.js', array('jquery', 'chosen'), $carton->version );

	wp_register_script( 'chosen', $carton->plugin_url() . '/assets/js/chosen/chosen.jquery'.$suffix.'.js', array('jquery'), $carton->version );

	// Get admin screen id
    $screen = get_current_screen();

    // CartoN admin pages
    if ( in_array( $screen->id, apply_filters( 'carton_screen_ids', array( 'toplevel_page_carton', 'carton_page_carton_settings', 'carton_page_carton_reports', 'edit-shop_order', 'edit-shop_coupon', 'shop_coupon', 'shop_order', 'edit-product', 'product', 'shop_discount' ) ) ) ) {

    	wp_enqueue_script( 'carton_admin' );
    	wp_enqueue_script( 'farbtastic' );
    	wp_enqueue_script( 'ajax-chosen' );
    	wp_enqueue_script( 'chosen' );
    	wp_enqueue_script( 'jquery-ui-sortable' );
    	wp_enqueue_script( 'jquery-ui-autocomplete' );

    }

    // Edit product category pages
    if ( in_array( $screen->id, array('edit-product_cat') ) )
		wp_enqueue_media();

	// Product/Coupon/Orders
	if ( in_array( $screen->id, array( 'shop_coupon', 'shop_order', 'product', 'shop_discount' ) ) ) {

		wp_enqueue_script( 'carton_writepanel' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_media();
		wp_enqueue_script( 'ajax-chosen' );
		wp_enqueue_script( 'chosen' );
		wp_enqueue_script( 'plupload-all' );

		$carton_witepanel_params = array(
			'remove_item_notice' 			=> __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'carton' ),
			'i18n_select_items'				=> __( 'Please select some items.', 'carton' ),
			'remove_item_meta'				=> __( 'Remove this item meta?', 'carton' ),
			'remove_attribute'				=> __( 'Remove this attribute?', 'carton' ),
			'name_label'					=> __( 'Name', 'carton' ),
			'remove_label'					=> __( 'Remove', 'carton' ),
			'click_to_toggle'				=> __( 'Click to toggle', 'carton' ),
			'values_label'					=> __( 'Value(s)', 'carton' ),
			'text_attribute_tip'			=> __( 'Enter some text, or some attributes by pipe (|) separating values.', 'carton' ),
			'visible_label'					=> __( 'Visible on the product page', 'carton' ),
			'used_for_variations_label'		=> __( 'Used for variations', 'carton' ),
			'new_attribute_prompt'			=> __( 'Enter a name for the new attribute term:', 'carton' ),
			'calc_totals' 					=> __( 'Calculate totals based on order items, discounts, and shipping?', 'carton' ),
			'calc_line_taxes' 				=> __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', 'carton' ),
			'copy_billing' 					=> __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'carton' ),
			'load_billing' 					=> __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'carton' ),
			'load_shipping' 				=> __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'carton' ),
			'featured_label'				=> __( 'Featured', 'carton' ),
			'prices_include_tax' 			=> esc_attr( get_option('carton_prices_include_tax') ),
			'round_at_subtotal'				=> esc_attr( get_option( 'carton_tax_round_at_subtotal' ) ),
			'no_customer_selected'			=> __( 'No customer selected', 'carton' ),
			'plugin_url' 					=> $carton->plugin_url(),
			'ajax_url' 						=> admin_url('admin-ajax.php'),
			'order_item_nonce' 				=> wp_create_nonce("order-item"),
			'add_attribute_nonce' 			=> wp_create_nonce("add-attribute"),
			'save_attributes_nonce' 		=> wp_create_nonce("save-attributes"),
			'calc_totals_nonce' 			=> wp_create_nonce("calc-totals"),
			'get_customer_details_nonce' 	=> wp_create_nonce("get-customer-details"),
			'search_products_nonce' 		=> wp_create_nonce("search-products"),
			'calendar_image'				=> $carton->plugin_url().'/assets/images/calendar.png',
			'post_id'						=> $post->ID,
			'currency_format_num_decimals'	=> absint( get_option( 'carton_price_num_decimals' ) ),
			'currency_format_symbol'		=> get_carton_currency_symbol(),
			'currency_format_decimal_sep'	=> esc_attr( stripslashes( get_option( 'carton_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'	=> esc_attr( stripslashes( get_option( 'carton_price_thousand_sep' ) ) ),
			'currency_format'				=> esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_carton_price_format() ) ), // For accounting JS
			'product_types'					=> array_map( 'sanitize_title', get_terms( 'product_type', array( 'hide_empty' => false, 'fields' => 'names' ) ) ),
			'default_attribute_visibility'  => apply_filters( 'default_attribute_visibility', false ),
			'default_attribute_variation'   => apply_filters( 'default_attribute_variation', false ),
			'default_attribute_changeable'   => apply_filters( 'default_attribute_changeable', false )
		 );

		wp_localize_script( 'carton_writepanel', 'carton_writepanel_params', $carton_witepanel_params );
	}

	// Term ordering - only when sorting by term_order
	if ( ( strstr( $screen->id, 'edit-pa_' ) || ( ! empty( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], apply_filters( 'carton_sortable_taxonomies', array( 'product_cat' ) ) ) ) ) && ! isset( $_GET['orderby'] ) ) {

		wp_register_script( 'carton_term_ordering', $carton->plugin_url() . '/assets/js/admin/term-ordering.js', array('jquery-ui-sortable'), $carton->version );
		wp_enqueue_script( 'carton_term_ordering' );

		$taxonomy = isset( $_GET['taxonomy'] ) ? carton_clean( $_GET['taxonomy'] ) : '';

		$carton_term_order_params = array(
			'taxonomy' 			=>  $taxonomy
		 );

		wp_localize_script( 'carton_term_ordering', 'carton_term_ordering_params', $carton_term_order_params );

	}

	// Product sorting - only when sorting by menu order on the products page
	if ( current_user_can('edit_others_pages') && $screen->id == 'edit-product' && isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'menu_order title' ) {

		wp_enqueue_script( 'carton_product_ordering', $carton->plugin_url() . '/assets/js/admin/product-ordering.js', array('jquery-ui-sortable'), '1.0', true );

	}

	// Reports pages
    if ( $screen->id == apply_filters( 'carton_reports_screen_id', 'carton_page_carton_reports' ) ) {

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'flot', $carton->plugin_url() . '/assets/js/admin/jquery.flot'.$suffix.'.js', 'jquery', '1.0' );
		wp_enqueue_script( 'flot-resize', $carton->plugin_url() . '/assets/js/admin/jquery.flot.resize'.$suffix.'.js', array('jquery', 'flot'), '1.0' );

	}
}

add_action( 'admin_enqueue_scripts', 'carton_admin_scripts' );


/**
 * Queue CartoN CSS.
 *
 * @access public
 * @return void
 */
function carton_admin_css() {
	global $carton, $typenow, $post, $wp_scripts;

	if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
		$typenow = $post->post_type;
	} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
        $post = get_post( $_GET['post'] );
        $typenow = $post->post_type;
    }

	if ( $typenow == '' || $typenow == "product" || $typenow == "shop_order" || $typenow == "shop_coupon" || $typenow == "shop_discount" ) {
		wp_enqueue_style( 'carton_admin_styles', $carton->plugin_url() . '/assets/css/admin.css' );

		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
	}

	wp_enqueue_style('farbtastic');

	do_action('carton_admin_css');
}


/**
 * Queue admin menu icons CSS.
 *
 * @access public
 * @return void
 */
function carton_admin_menu_styles() {
	global $carton;
	wp_enqueue_style( 'carton_admin_menu_styles', $carton->plugin_url() . '/assets/css/menu.css' );
}

add_action( 'admin_print_styles', 'carton_admin_menu_styles' );


/**
 * Reorder the WC menu items in admin.
 *
 * @access public
 * @param mixed $menu_order
 * @return void
 */
function carton_admin_menu_order( $menu_order ) {

	// Initialize our custom order array
	$carton_menu_order = array();

	// Get the index of our custom separator
	$carton_separator = array_search( 'separator-carton', $menu_order );

	// Get index of product menu
	$carton_product = array_search( 'edit.php?post_type=product', $menu_order );

	// Loop through menu order and do some rearranging
	foreach ( $menu_order as $index => $item ) :

		if ( ( ( 'carton' ) == $item ) ) :
			$carton_menu_order[] = 'separator-carton';
			$carton_menu_order[] = $item;
			$carton_menu_order[] = 'edit.php?post_type=product';
			unset( $menu_order[$carton_separator] );
			unset( $menu_order[$carton_product] );
		elseif ( !in_array( $item, array( 'separator-carton' ) ) ) :
			$carton_menu_order[] = $item;
		endif;

	endforeach;

	// Return order
	return $carton_menu_order;
}

add_action('menu_order', 'carton_admin_menu_order');


/**
 * carton_admin_custom_menu_order function.
 *
 * @access public
 * @return void
 */
function carton_admin_custom_menu_order() {
	if ( ! current_user_can( 'manage_carton' ) )
		return false;
	return true;
}

add_action( 'custom_menu_order', 'carton_admin_custom_menu_order' );


/**
 * Admin Head
 *
 * Outputs some styles in the admin <head> to show icons on the carton admin pages
 *
 * @access public
 * @return void
 */
function carton_admin_head() {
	global $carton;

	if ( ! current_user_can( 'manage_carton' ) ) return false;
	?>
	<style type="text/css">
		<?php if ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='product_cat' ) : ?>
			.icon32-posts-product { background-position: -243px -5px !important; }
		<?php elseif ( isset($_GET['taxonomy']) && $_GET['taxonomy']=='product_tag' ) : ?>
			.icon32-posts-product { background-position: -301px -5px !important; }
		<?php endif; ?>
	</style>
	<?php
}

add_action('admin_head', 'carton_admin_head');


/**
 * Duplicate a product action
 *
 * @access public
 * @return void
 */
function carton_duplicate_product_action() {
	include_once('includes/duplicate_product.php');
	carton_duplicate_product();
}

add_action('admin_action_duplicate_product', 'carton_duplicate_product_action');


/**
 * Post updated messages
 *
 * @access public
 * @param mixed $messages
 * @return void
 */
function carton_product_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages['product'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __( 'Product updated. <a href="%s">View Product</a>', 'carton' ), esc_url( get_permalink($post_ID) ) ),
		2 => __( 'Custom field updated.', 'carton' ),
		3 => __( 'Custom field deleted.', 'carton' ),
		4 => __( 'Product updated.', 'carton' ),
		5 => isset($_GET['revision']) ? sprintf( __( 'Product restored to revision from %s', 'carton' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __( 'Product published. <a href="%s">View Product</a>', 'carton' ), esc_url( get_permalink($post_ID) ) ),
		7 => __( 'Product saved.', 'carton' ),
		8 => sprintf( __( 'Product submitted. <a target="_blank" href="%s">Preview Product</a>', 'carton' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __( 'Product scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Product</a>', 'carton' ),
		  date_i18n( __( 'M j, Y @ G:i', 'carton' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __( 'Product draft updated. <a target="_blank" href="%s">Preview Product</a>', 'carton' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);

	$messages['shop_order'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Order updated.', 'carton' ),
		2 => __( 'Custom field updated.', 'carton' ),
		3 => __( 'Custom field deleted.', 'carton' ),
		4 => __( 'Order updated.', 'carton' ),
		5 => isset($_GET['revision']) ? sprintf( __( 'Order restored to revision from %s', 'carton' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __( 'Order updated.', 'carton' ),
		7 => __( 'Order saved.', 'carton' ),
		8 => __( 'Order submitted.', 'carton' ),
		9 => sprintf( __( 'Order scheduled for: <strong>%1$s</strong>.', 'carton' ),
		  date_i18n( __( 'M j, Y @ G:i', 'carton' ), strtotime( $post->post_date ) ) ),
		10 => __( 'Order draft updated.', 'carton' )
	);

	$messages['shop_coupon'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Coupon updated.', 'carton' ),
		2 => __( 'Custom field updated.', 'carton' ),
		3 => __( 'Custom field deleted.', 'carton' ),
		4 => __( 'Coupon updated.', 'carton' ),
		5 => isset($_GET['revision']) ? sprintf( __( 'Coupon restored to revision from %s', 'carton' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => __( 'Coupon updated.', 'carton' ),
		7 => __( 'Coupon saved.', 'carton' ),
		8 => __( 'Coupon submitted.', 'carton' ),
		9 => sprintf( __( 'Coupon scheduled for: <strong>%1$s</strong>.', 'carton' ),
		  date_i18n( __( 'M j, Y @ G:i', 'carton' ), strtotime( $post->post_date ) ) ),
		10 => __( 'Coupon draft updated.', 'carton' )
	);

	return $messages;
}

add_filter('post_updated_messages', 'carton_product_updated_messages');


/**
 * Post updated messages
 *
 * @access public
 * @param mixed $types
 * @return void
 */
function carton_admin_comment_types_dropdown( $types ) {
	$types['order_note'] = __( 'Order notes', 'carton' );
	return $types;
}

add_filter( 'admin_comment_types_dropdown', 'carton_admin_comment_types_dropdown' );


/**
 * carton_permalink_settings function.
 *
 * @access public
 * @return void
 */
function carton_permalink_settings() {

	echo wpautop( __( 'These settings control the permalinks used for products. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'carton' ) );

	$permalinks = get_option( 'carton_permalinks' );
	$product_permalink = $permalinks['product_base'];

	// Get shop page
	$shop_page_id 	= carton_get_page_id( 'shop' );
	$base_slug 		= ( $shop_page_id > 0 && get_page( $shop_page_id ) ) ? get_page_uri( $shop_page_id ) : _x( 'shop', 'default-slug', 'carton' );
	$product_base 	= _x( 'product', 'default-slug', 'carton' );

	$structures = array(
		0 => '',
		1 => '/' . trailingslashit( $product_base ),
		2 => '/' . trailingslashit( $base_slug ),
		3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%product_cat%' )
	);
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[0]; ?>" class="wctog" <?php checked( $structures[0], $product_permalink ); ?> /> <?php _e( 'Default' ); ?></label></th>
				<td><code><?php echo home_url(); ?>/?product=sample-product</code></td>
			</tr>
			<tr>
				<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[1]; ?>" class="wctog" <?php checked( $structures[1], $product_permalink ); ?> /> <?php _e( 'Product', 'carton' ); ?></label></th>
				<td><code><?php echo home_url(); ?>/<?php echo $product_base; ?>/sample-product/</code></td>
			</tr>
			<?php if ( $shop_page_id ) : ?>
				<tr>
					<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[2]; ?>" class="wctog" <?php checked( $structures[2], $product_permalink ); ?> /> <?php _e( 'Shop base', 'carton' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/sample-product/</code></td>
				</tr>
				<tr>
					<th><label><input name="product_permalink" type="radio" value="<?php echo $structures[3]; ?>" class="wctog" <?php checked( $structures[3], $product_permalink ); ?> /> <?php _e( 'Shop base with category', 'carton' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/product-category/sample-product/</code></td>
				</tr>
			<?php endif; ?>
			<tr>
				<th><label><input name="product_permalink" id="carton_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $product_permalink, $structures ), false ); ?> />
					<?php _e( 'Custom Base', 'carton' ); ?></label></th>
				<td>
					<input name="product_permalink_structure" id="carton_permalink_structure" type="text" value="<?php echo esc_attr( $product_permalink ); ?>" class="regular-text code"> <span class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'carton' ); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('input.wctog').change(function() {
				jQuery('#carton_permalink_structure').val( jQuery(this).val() );
			});

			jQuery('#carton_permalink_structure').focus(function(){
				jQuery('#carton_custom_selection').click();
			});
		});
	</script>
	<?php
}

/**
 * carton_permalink_settings_init function.
 *
 * @access public
 * @return void
 */
function carton_permalink_settings_init() {

	// Add a section to the permalinks page
	add_settings_section( 'carton-permalink', __( 'Product permalink base', 'carton' ), 'carton_permalink_settings', 'permalink' );

	// Add our settings
	add_settings_field(
		'carton_product_category_slug',      	// id
		__( 'Product category base', 'carton' ), 	// setting title
		'carton_product_category_slug_input',  // display callback
		'permalink',                 				// settings page
		'optional'                  				// settings section
	);
	add_settings_field(
		'carton_product_tag_slug',      		// id
		__( 'Product tag base', 'carton' ), 	// setting title
		'carton_product_tag_slug_input',  		// display callback
		'permalink',                 				// settings page
		'optional'                  				// settings section
	);
	add_settings_field(
		'carton_product_attribute_slug',      	// id
		__( 'Product attribute base', 'carton' ), 	// setting title
		'carton_product_attribute_slug_input',  		// display callback
		'permalink',                 				// settings page
		'optional'                  				// settings section
	);
}

add_action( 'admin_init', 'carton_permalink_settings_init' );

/**
 * carton_permalink_settings_save function.
 *
 * @access public
 * @return void
 */
function carton_permalink_settings_save() {
	if ( ! is_admin() )
		return;

	// We need to save the options ourselves; settings api does not trigger save for the permalinks page
	if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) ) {
		// Cat and tag bases
		$carton_product_category_slug = carton_clean( $_POST['carton_product_category_slug'] );
		$carton_product_tag_slug = carton_clean( $_POST['carton_product_tag_slug'] );
		$carton_product_attribute_slug = carton_clean( $_POST['carton_product_attribute_slug'] );

		$permalinks = get_option( 'carton_permalinks' );
		if ( ! $permalinks )
			$permalinks = array();

		$permalinks['category_base'] 	= untrailingslashit( $carton_product_category_slug );
		$permalinks['tag_base'] 		= untrailingslashit( $carton_product_tag_slug );
		$permalinks['attribute_base'] 	= untrailingslashit( $carton_product_attribute_slug );

		// Product base
		$product_permalink = carton_clean( $_POST['product_permalink'] );

		if ( $product_permalink == 'custom' ) {
			$product_permalink = carton_clean( $_POST['product_permalink_structure'] );
		} elseif ( empty( $product_permalink ) ) {
			$product_permalink = false;
		}

		$permalinks['product_base'] = untrailingslashit( $product_permalink );

		update_option( 'carton_permalinks', $permalinks );
	}
}

add_action( 'before_carton_init', 'carton_permalink_settings_save' );

/**
 * carton_product_category_slug_input function.
 *
 * @access public
 * @return void
 */
function carton_product_category_slug_input() {
	$permalinks = get_option( 'carton_permalinks' );
	?>
	<input name="carton_product_category_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['category_base'] ) ) echo esc_attr( $permalinks['category_base'] ); ?>" placeholder="<?php echo _x('product-category', 'slug', 'carton') ?>" />
	<?php
}

/**
 * carton_product_tag_slug_input function.
 *
 * @access public
 * @return void
 */
function carton_product_tag_slug_input() {
	$permalinks = get_option( 'carton_permalinks' );
	?>
	<input name="carton_product_tag_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['tag_base'] ) ) echo esc_attr( $permalinks['tag_base'] ); ?>" placeholder="<?php echo _x('product-tag', 'slug', 'carton') ?>" />
	<?php
}

/**
 * carton_product_attribute_slug_input function.
 *
 * @access public
 * @return void
 */
function carton_product_attribute_slug_input() {
	$permalinks = get_option( 'carton_permalinks' );
	?>
	<input name="carton_product_attribute_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['attribute_base'] ) ) echo esc_attr( $permalinks['attribute_base'] ); ?>" /><code>/attribute-name/attribute/</code>
	<?php
}
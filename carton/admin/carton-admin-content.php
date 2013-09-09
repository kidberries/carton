<?php
/**
 * Functions used for the showing help/links to CartoN resources in admin
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Help Tab Content
 *
 * Shows some text about CartoN and links to docs.
 *
 * @access public
 * @return void
 */
function carton_admin_help_tab_content() {
	$screen = get_current_screen();

	$screen->add_help_tab( array(
	    'id'	=> 'carton_overview_tab',
	    'title'	=> __( 'Overview', 'carton' ),
	    'content'	=>

	    	'<p>' . sprintf(__( 'Thank you for using CartoN :) Should you need help using or extending CartoN please <a href="%s">read the documentation</a>. For further assistance you can use the <a href="%s">community forum</a> or if you have access, <a href="%s">our support desk</a>.', 'carton' ), 'http://docs.carton-ecommerce.com/', 'http://wordpress.org/support/plugin/carton', 'http://support.carton-ecommerce.com') . '</p>' .

	    	'<p>' . __( 'If you are having problems, or to assist us with support, please check the status page to identify any problems with your configuration:', 'carton' ) . '</p>' .

	    	'<p><a href="' . admin_url('admin.php?page=carton_status') . '" class="button">' . __( 'System Status', 'carton' ) . '</a></p>' .

	    	'<p>' . sprintf(__( 'If you come across a bug, or wish to contribute to the project you can also <a href="%s">get involved on GitHub</a>.', 'carton' ), 'https://github.com/carton-ecommerce/carton') . '</p>'

	) );

	$screen->add_help_tab( array(
	    'id'	=> 'carton_settings_tab',
	    'title'	=> __( 'Settings', 'carton' ),
	    'content'	=>
	    	'<p>' . __( 'Here you can set up your store and customise it to fit your needs. The sections available from the settings page include:', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'General', 'carton' ) . '</strong> - ' . __( 'General settings such as your shop base, currency, and script/styling options which affect features used in your store.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Pages', 'carton' ) . '</strong> - ' . __( 'This is where important store page are defined. You can also set up other pages (such as a Terms page) here.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Catalog', 'carton' ) . '</strong> - ' . __( 'Options for how things like price, images and weights appear in your product catalog.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Inventory', 'carton' ) . '</strong> - ' . __( 'Options concerning stock and stock notices.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Tax', 'carton' ) . '</strong> - ' . __( 'Options concerning tax, including international and local tax rates.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Shipping', 'carton' ) . '</strong> - ' . __( 'This is where shipping options are defined, and shipping methods are set up.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Payment Methods', 'carton' ) . '</strong> - ' . __( 'This is where payment gateway options are defined, and individual payment gateways are set up.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Emails', 'carton' ) . '</strong> - ' . __( 'Here you can customise the way CartoN emails appear.', 'carton' ) . '</p>' .
	    	'<p><strong>' . __( 'Integration', 'carton' ) . '</strong> - ' . __( 'The integration section contains options for third party services which integrate with CartoN.', 'carton' ) . '</p>'
	) );

	$screen->add_help_tab( array(
	    'id'	=> 'carton_overview_tab_2',
	    'title'	=> __( 'Reports', 'carton' ),
	    'content'	=>
				'<p>' . __( 'The reports section can be accessed from the left-hand navigation menu. Here you can generate reports for sales and customers.', 'carton' ) . '</p>' .
				'<p><strong>' . __( 'Sales', 'carton' ) . '</strong> - ' . __( 'Reports for sales based on date, top sellers and top earners.', 'carton' ) . '</p>' .
				'<p><strong>' . __( 'Coupons', 'carton' ) . '</strong> - ' . __( 'Coupon usage reports.', 'carton' ) . '</p>' .
				'<p><strong>' . __( 'Customers', 'carton' ) . '</strong> - ' . __( 'Customer reports, such as signups per day.', 'carton' ) . '</p>' .
				'<p><strong>' . __( 'Stock', 'carton' ) . '</strong> - ' . __( 'Stock reports for low stock and out of stock items.', 'carton' ) . '</p>'
	) );

	$screen->add_help_tab( array(
	     'id'	=> 'carton_overview_tab_3',
	     'title'	=> __( 'Orders', 'carton' ),
	     'content'	=>
				'<p>' . __( 'The orders section can be accessed from the left-hand navigation menu. Here you can view and manage customer orders.', 'carton' ) . '</p>' .
				'<p>' . __( 'Orders can also be added from this section if you want to set them up for a customer manually.', 'carton' ) . '</p>'
	) );

	$screen->add_help_tab( array(
	     'id'	=> 'carton_overview_tab_4',
	     'title'	=> __( 'Coupons', 'carton' ),
	     'content'	=>
				'<p>' . __( 'Coupons can be managed from this section. Once added, customers will be able to enter coupon codes on the cart/checkout page. If a customer uses a coupon code they will be viewable when viewing orders.', 'carton' ) . '</p>'
	) );

	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'carton' ) . '</strong></p>' .
		'<p><a href="http://www.carton-ecommerce.com/carton/" target="_blank">' . __( 'CartoN', 'carton' ) . '</a></p>' .
		'<p><a href="http://wordpress.org/extend/plugins/carton/" target="_blank">' . __( 'Project on WordPress.org', 'carton' ) . '</a></p>' .
		'<p><a href="https://github.com/carton-ecommerce/carton" target="_blank">' . __( 'Project on Github', 'carton' ) . '</a></p>' .
		'<p><a href="http://docs.carton-ecommerce.com/" target="_blank">' . __( 'CartoN Docs', 'carton' ) . '</a></p>' .
		'<p><a href="http://www.carton-ecommerce.com/product-category/carton-extensions/" target="_blank">' . __( 'Official Extensions', 'carton' ) . '</a></p>' .
		'<p><a href="http://www.carton-ecommerce.com/product-category/themes/carton/" target="_blank">' . __( 'Official Themes', 'carton' ) . '</a></p>'
	);
}
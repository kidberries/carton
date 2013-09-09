<?php
/**
 * Defines the array of settings which are displayed in admin.
 *
 * Settings are defined here and displayed via functions.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Settings
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

$localisation_setting = defined( 'WPLANG' ) && file_exists( $carton->plugin_path() . '/i18n/languages/informal/carton-' . WPLANG . '.mo' ) ? array(
	'title' => __( 'Localisation', 'carton' ),
	'desc' 		=> sprintf( __( 'Use informal localisation for %s', 'carton' ), WPLANG ),
	'id' 		=> 'carton_informal_localisation_type',
	'type' 		=> 'checkbox',
	'default'	=> 'no',
) : array();

$currency_code_options = get_carton_currencies();

foreach ( $currency_code_options as $code => $name ) {
	$currency_code_options[ $code ] = $name . ' (' . get_carton_currency_symbol( $code ) . ')';
}

$carton_settings['general'] = apply_filters('carton_general_settings', array(

	array( 'title' => __( 'General Options', 'carton' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

	array(
		'title' 	=> __( 'Base Location', 'carton' ),
		'desc' 		=> __( 'This is the base location for your business. Tax rates will be based on this country.', 'carton' ),
		'id' 		=> 'carton_default_country',
		'css' 		=> 'min-width:350px;',
		'default'	=> 'GB',
		'type' 		=> 'single_select_country',
		'desc_tip'	=>  true,
	),

	array(
		'title' 	=> __( 'Currency', 'carton' ),
		'desc' 		=> __( "This controls what currency prices are listed at in the catalog and which currency gateways will take payments in.", 'carton' ),
		'id' 		=> 'carton_currency',
		'css' 		=> 'min-width:350px;',
		'default'	=> 'GBP',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'desc_tip'	=>  true,
		'options'   => $currency_code_options
	),

	array(
		'title' => __( 'Allowed Countries', 'carton' ),
		'desc' 		=> __( 'These are countries that you are willing to ship to.', 'carton' ),
		'id' 		=> 'carton_allowed_countries',
		'default'	=> 'all',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'min-width:350px;',
		'desc_tip'	=>  true,
		'options' => array(
			'all'  => __( 'All Countries', 'carton' ),
			'specific' => __( 'Specific Countries', 'carton' )
		)
	),

	array(
		'title' => __( 'Specific Countries', 'carton' ),
		'desc' 		=> '',
		'id' 		=> 'carton_specific_allowed_countries',
		'css' 		=> '',
		'default'	=> '',
		'type' 		=> 'multi_select_countries'
	),

	$localisation_setting,

	array(
		'title' => __( 'Store Notice', 'carton' ),
		'desc' 		=> __( 'Enable site-wide store notice text', 'carton' ),
		'id' 		=> 'carton_demo_store',
		'default'	=> 'no',
		'type' 		=> 'checkbox'
	),

	array(
		'title' => __( 'Store Notice Text', 'carton' ),
		'desc' 		=> '',
		'id' 		=> 'carton_demo_store_notice',
		'default'	=> __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'carton' ),
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
	),

	array( 'type' => 'sectionend', 'id' => 'general_options'),

	array(	'title' => __( 'Cart, Checkout and Accounts', 'carton' ), 'type' => 'title', 'id' => 'checkout_account_options' ),

	array(
		'title' => __( 'Coupons', 'carton' ),
		'desc'          => __( 'Enable the use of coupons', 'carton' ),
		'id'            => 'carton_enable_coupons',
		'default'       => 'yes',
		'type'          => 'checkbox',
		'desc_tip'		=>  __( 'Coupons can be applied from the cart and checkout pages.', 'carton' ),
	),

	array(
		'title' => __( 'Checkout', 'carton' ),
		'desc' 		=> __( 'Enable guest checkout (no account required)', 'carton' ),
		'id' 		=> 'carton_enable_guest_checkout',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'	=> 'start'
	),

	array(
		'desc' 		=> __( 'Enable customer note field on checkout', 'carton' ),
		'id' 		=> 'carton_enable_order_comments',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),

	array(
		'desc' 		=> __( 'Force secure checkout', 'carton' ),
		'id' 		=> 'carton_force_ssl_checkout',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> '',
		'show_if_checked' => 'option',
		'desc_tip'	=>  __( 'Force SSL (HTTPS) on the checkout pages (an SSL Certificate is required).', 'carton' ),
	),

	array(
		'desc' 		=> __( 'Un-force HTTPS when leaving the checkout', 'carton' ),
		'id' 		=> 'carton_unforce_ssl_checkout',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end',
		'show_if_checked' => 'yes',
	),

	array(
		'title' => __( 'Registration', 'carton' ),
		'desc' 		=> __( 'Allow registration on the checkout page', 'carton' ),
		'id' 		=> 'carton_enable_signup_and_login_from_checkout',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Allow registration on the "My Account" page', 'carton' ),
		'id' 		=> 'carton_enable_myaccount_registration',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),

	array(
		'desc' 		=> __( 'Register using the email address for the username', 'carton' ),
		'id' 		=> 'carton_registration_email_for_username',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),

	array(
		'title' => __( 'Customer Accounts', 'carton' ),
		'desc' 		=> __( 'Prevent customers from accessing WordPress admin', 'carton' ),
		'id' 		=> 'carton_lock_down_admin',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Clear cart when logging out', 'carton' ),
		'id' 		=> 'carton_clear_cart_on_logout',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),

	array(
		'desc' 		=> __( 'Allow customers to repurchase orders from their account page', 'carton' ),
		'id' 		=> 'carton_allow_customers_to_reorder',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),

	array( 'type' => 'sectionend', 'id' => 'checkout_account_options'),

	array(	'title' => __( 'Styles and Scripts', 'carton' ), 'type' => 'title', 'id' => 'script_styling_options' ),

	array(
		'title' => __( 'Styling', 'carton' ),
		'desc' 		=> __( 'Enable CartoN CSS', 'carton' ),
		'id' 		=> 'carton_frontend_css',
		'default'	=> 'yes',
		'type' 		=> 'checkbox'
	),

	array(
		'type' 		=> 'frontend_styles'
	),

	array(
		'title' => __( 'Scripts', 'carton' ),
		'desc' 	=> __( 'Enable Lightbox', 'carton' ),
		'id' 		=> 'carton_enable_lightbox',
		'default'	=> 'yes',
		'desc_tip'	=> __( 'Include CartoN\'s lightbox. Product gallery images and the add review form will open in a lightbox.', 'carton' ),
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Enable enhanced country select boxes', 'carton' ),
		'id' 		=> 'carton_enable_chosen',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end',
		'desc_tip'	=> __( 'This will enable a script allowing the country fields to be searchable.', 'carton' ),
	),

	array( 'type' => 'sectionend', 'id' => 'script_styling_options'),

	array(	'title' => __( 'Downloadable Products', 'carton' ), 'type' => 'title', 'id' => 'digital_download_options' ),

	array(
		'title' => __( 'File Download Method', 'carton' ),
		'desc' 		=> __( 'Forcing downloads will keep URLs hidden, but some servers may serve large files unreliably. If supported, <code>X-Accel-Redirect</code>/ <code>X-Sendfile</code> can be used to serve downloads instead (server requires <code>mod_xsendfile</code>).', 'carton' ),
		'id' 		=> 'carton_file_download_method',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'css' 		=> 'min-width:300px;',
		'default'	=> 'force',
		'desc_tip'	=>  true,
		'options' => array(
			'force'  	=> __( 'Force Downloads', 'carton' ),
			'xsendfile' => __( 'X-Accel-Redirect/X-Sendfile', 'carton' ),
			'redirect'  => __( 'Redirect only', 'carton' ),
		)
	),

	array(
		'title' => __( 'Access Restriction', 'carton' ),
		'desc' 		=> __( 'Downloads require login', 'carton' ),
		'id' 		=> 'carton_downloads_require_login',
		'type' 		=> 'checkbox',
		'default'	=> 'no',
		'desc_tip'	=> __( 'This setting does not apply to guest purchases.', 'carton' ),
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Grant access to downloadable products after payment', 'carton' ),
		'id' 		=> 'carton_downloads_grant_access_after_payment',
		'type' 		=> 'checkbox',
		'default'	=> 'yes',
		'desc_tip'	=> __( 'Enable this option to grant access to downloads when orders are "processing", rather than "completed".', 'carton' ),
		'checkboxgroup'		=> 'end'
	),

	array( 'type' => 'sectionend', 'id' => 'digital_download_options' ),

)); // End general settings

// Get shop page
$shop_page_id = carton_get_page_id('shop');

$base_slug = ($shop_page_id > 0 && get_page( $shop_page_id )) ? get_page_uri( $shop_page_id ) : 'shop';

$carton_prepend_shop_page_to_products_warning = '';

if ( $shop_page_id > 0 && sizeof(get_pages("child_of=$shop_page_id")) > 0 )
	$carton_prepend_shop_page_to_products_warning = ' <mark class="notice">' . __( 'Note: The shop page has children - child pages will not work if you enable this option.', 'carton' ) . '</mark>';

$carton_settings['pages'] = apply_filters('carton_page_settings', array(

	array(
		'title' => __( 'Page Setup', 'carton' ),
		'type' => 'title',
		'desc' => sprintf( __( 'Set up core CartoN pages here, for example the base page. The base page can also be used in your %sproduct permalinks%s.', 'carton' ), '<a target="_blank" href="' . admin_url( 'options-permalink.php' ) . '">', '</a>' ),
		'id' => 'page_options'
	),

	array(
		'title' => __( 'Shop Base Page', 'carton' ),
		'desc' 		=> __( 'This sets the base page of your shop - this is where your product archive will be.', 'carton' ),
		'id' 		=> 'carton_shop_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true
	),

	array(
		'title' => __( 'Terms Page ID', 'carton' ),
		'desc' 		=> __( 'If you define a "Terms" page the customer will be asked if they accept them when checking out.', 'carton' ),
		'id' 		=> 'carton_terms_page_id',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'type' 		=> 'single_select_page',
		'desc_tip'	=>  true,
	),

	array( 'type' => 'sectionend', 'id' => 'page_options' ),

	array( 'title' => __( 'Shop Pages', 'carton' ), 'type' => 'title', 'desc' => __( 'The following pages need selecting so that CartoN knows where they are. These pages should have been created upon installation of the plugin, if not you will need to create them.', 'carton' ) ),

	array(
		'title' => __( 'Cart Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_cart]', 'carton' ),
		'id' 		=> 'carton_cart_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Checkout Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_checkout]', 'carton' ),
		'id' 		=> 'carton_checkout_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Pay Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_pay] Parent: "Checkout"', 'carton' ),
		'id' 		=> 'carton_pay_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Thanks Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_thankyou] Parent: "Checkout"', 'carton' ),
		'id' 		=> 'carton_thanks_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'My Account Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_my_account]', 'carton' ),
		'id' 		=> 'carton_myaccount_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Edit Address Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_edit_address] Parent: "My Account"', 'carton' ),
		'id' 		=> 'carton_edit_address_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'View Order Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_view_order] Parent: "My Account"', 'carton' ),
		'id' 		=> 'carton_view_order_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Change Password Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_change_password] Parent: "My Account"', 'carton' ),
		'id' 		=> 'carton_change_password_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Logout Page', 'carton' ),
		'desc' 		=> __( 'Parent: "My Account"', 'carton' ),
		'id' 		=> 'carton_logout_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Lost Password Page', 'carton' ),
		'desc' 		=> __( 'Page contents: [carton_lost_password] Parent: "My Account"', 'carton' ),
		'id' 		=> 'carton_lost_password_page_id',
		'type' 		=> 'single_select_page',
		'default'	=> '',
		'class'		=> 'chosen_select_nostd',
		'css' 		=> 'min-width:300px;',
		'desc_tip'	=>  true,
	),

	array( 'type' => 'sectionend', 'id' => 'page_options')

)); // End pages settings


$carton_settings['catalog'] = apply_filters('carton_catalog_settings', array(

	array(	'title' => __( 'Catalog Options', 'carton' ), 'type' => 'title','desc' => '', 'id' => 'catalog_options' ),

	array(
		'title' => __( 'Default Product Sorting', 'carton' ),
		'desc' 		=> __( 'This controls the default sort order of the catalog.', 'carton' ),
		'id' 		=> 'carton_default_catalog_orderby',
		'css' 		=> 'min-width:150px;',
		'default'	=> 'title',
		'type' 		=> 'select',
		'options' => apply_filters('carton_default_catalog_orderby_options', array(
			'menu_order' => __( 'Default sorting (custom ordering + name)', 'carton' ),
			'popularity' => __( 'Popularity (sales)', 'carton' ),
			'rating'     => __( 'Average Rating', 'carton' ),
			'date'       => __( 'Sort by most recent', 'carton' ),
			'price'      => __( 'Sort by price (asc)', 'carton' ),
			'price-desc' => __( 'Sort by price (desc)', 'carton' ),
		)),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Shop Page Display', 'carton' ),
		'desc' 		=> __( 'This controls what is shown on the product archive.', 'carton' ),
		'id' 		=> 'carton_shop_page_display',
		'css' 		=> 'min-width:150px;',
		'default'	=> '',
		'type' 		=> 'select',
		'options' => array(
			''  			=> __( 'Show products', 'carton' ),
			'subcategories' => __( 'Show subcategories', 'carton' ),
			'both'   		=> __( 'Show both', 'carton' ),
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Default Category Display', 'carton' ),
		'desc' 		=> __( 'This controls what is shown on category archives.', 'carton' ),
		'id' 		=> 'carton_category_archive_display',
		'css' 		=> 'min-width:150px;',
		'default'	=> '',
		'type' 		=> 'select',
		'options' => array(
			''  			=> __( 'Show products', 'carton' ),
			'subcategories' => __( 'Show subcategories', 'carton' ),
			'both'   		=> __( 'Show both', 'carton' ),
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Add to cart', 'carton' ),
		'desc' 		=> __( 'Redirect to the cart page after successful addition', 'carton' ),
		'id' 		=> 'carton_cart_redirect_after_add',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Enable AJAX add to cart buttons on archives', 'carton' ),
		'id' 		=> 'carton_enable_ajax_add_to_cart',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),

	array( 'type' => 'sectionend', 'id' => 'catalog_options' ),

	array(	'title' => __( 'Product Data', 'carton' ), 'type' => 'title', 'desc' => __( 'The following options affect the fields available on the edit product page.', 'carton' ), 'id' => 'product_data_options' ),

	array(
		'title' => __( 'Product Fields', 'carton' ),
		'desc' 		=> __( 'Enable the <strong>SKU</strong> field for products', 'carton' ),
		'id' 		=> 'carton_enable_sku',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Enable the <strong>weight</strong> field for products (some shipping methods may require this)', 'carton' ),
		'id' 		=> 'carton_enable_weight',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),

	array(
		'desc' 		=> __( 'Enable the <strong>dimension</strong> fields for products (some shipping methods may require this)', 'carton' ),
		'id' 		=> 'carton_enable_dimensions',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),

	array(
		'desc' 		=> __( 'Show <strong>weight and dimension</strong> values on the <strong>Additional Information</strong> tab', 'carton' ),
		'id' 		=> 'carton_enable_dimension_product_attributes',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),

	array(
		'title' => __( 'Weight Unit', 'carton' ),
		'desc' 		=> __( 'This controls what unit you will define weights in.', 'carton' ),
		'id' 		=> 'carton_weight_unit',
		'css' 		=> 'min-width:150px;',
		'default'	=> __( 'kg', 'carton' ),
		'type' 		=> 'select',
		'options' => array(
			'kg'  => __( 'kg', 'carton' ),
			'g'   => __( 'g', 'carton' ),
			'lbs' => __( 'lbs', 'carton' ),
			'oz' => __( 'oz', 'carton' ),
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Dimensions Unit', 'carton' ),
		'desc' 		=> __( 'This controls what unit you will define lengths in.', 'carton' ),
		'id' 		=> 'carton_dimension_unit',
		'css' 		=> 'min-width:150px;',
		'default'	=> __( 'cm', 'carton' ),
		'type' 		=> 'select',
		'options' => array(
			'm'  => __( 'm', 'carton' ),
			'cm' => __( 'cm', 'carton' ),
			'mm' => __( 'mm', 'carton' ),
			'in' => __( 'in', 'carton' ),
			'yd' => __( 'yd', 'carton' ),
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Product Ratings', 'carton' ),
		'desc' 		=> __( 'Enable ratings on reviews', 'carton' ),
		'id' 		=> 'carton_enable_review_rating',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start',
		'show_if_checked' => 'option',
	),

	array(
		'desc' 		=> __( 'Ratings are required to leave a review', 'carton' ),
		'id' 		=> 'carton_review_rating_required',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> '',
		'show_if_checked' => 'yes',
	),

	array(
		'desc' 		=> __( 'Show "verified owner" label for customer reviews', 'carton' ),
		'id' 		=> 'carton_review_rating_verification_label',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end',
		'show_if_checked' => 'yes',
	),

	array( 'type' => 'sectionend', 'id' => 'product_review_options' ),

	array(	'title' => __( 'Pricing Options', 'carton' ), 'type' => 'title', 'desc' => __( 'The following options affect how prices are displayed on the frontend.', 'carton' ), 'id' => 'pricing_options' ),

	array(
		'title' => __( 'Currency Position', 'carton' ),
		'desc' 		=> __( 'This controls the position of the currency symbol.', 'carton' ),
		'id' 		=> 'carton_currency_pos',
		'css' 		=> 'min-width:150px;',
		'default'	=> 'left',
		'type' 		=> 'select',
		'options' => array(
			'left' => __( 'Left', 'carton' ),
			'right' => __( 'Right', 'carton' ),
			'left_space' => __( 'Left (with space)', 'carton' ),
			'right_space' => __( 'Right (with space)', 'carton' )
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Thousand Separator', 'carton' ),
		'desc' 		=> __( 'This sets the thousand separator of displayed prices.', 'carton' ),
		'id' 		=> 'carton_price_thousand_sep',
		'css' 		=> 'width:50px;',
		'default'	=> ',',
		'type' 		=> 'text',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Decimal Separator', 'carton' ),
		'desc' 		=> __( 'This sets the decimal separator of displayed prices.', 'carton' ),
		'id' 		=> 'carton_price_decimal_sep',
		'css' 		=> 'width:50px;',
		'default'	=> '.',
		'type' 		=> 'text',
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Number of Decimals', 'carton' ),
		'desc' 		=> __( 'This sets the number of decimal points shown in displayed prices.', 'carton' ),
		'id' 		=> 'carton_price_num_decimals',
		'css' 		=> 'width:50px;',
		'default'	=> '2',
		'desc_tip'	=>  true,
		'type' 		=> 'number',
		'custom_attributes' => array(
			'min' 	=> 0,
			'step' 	=> 1
		)
	),

	array(
		'title'		=> __( 'Trailing Zeros', 'carton' ),
		'desc' 		=> __( 'Remove zeros after the decimal point. e.g. <code>$10.00</code> becomes <code>$10</code>', 'carton' ),
		'id' 		=> 'carton_price_trim_zeros',
		'default'	=> 'yes',
		'type' 		=> 'checkbox'
	),

	array( 'type' => 'sectionend', 'id' => 'pricing_options' ),

	array(	'title' => __( 'Image Options', 'carton' ), 'type' => 'title','desc' => sprintf(__( 'These settings affect the actual dimensions of images in your catalog - the display on the front-end will still be affected by CSS styles. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'carton' ), 'http://wordpress.org/extend/plugins/regenerate-thumbnails/'), 'id' => 'image_options' ),

	array(
		'title' => __( 'Catalog Images', 'carton' ),
		'desc' 		=> __( 'This size is usually used in product listings', 'carton' ),
		'id' 		=> 'shop_catalog_image_size',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'default'	=> array(
			'width' 	=> '150',
			'height'	=> '150',
			'crop'		=> true
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Single Product Image', 'carton' ),
		'desc' 		=> __( 'This is the size used by the main image on the product page.', 'carton' ),
		'id' 		=> 'shop_single_image_size',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'default'	=> array(
			'width' 	=> '300',
			'height'	=> '300',
			'crop'		=> 1
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Product Thumbnails', 'carton' ),
		'desc' 		=> __( 'This size is usually used for the gallery of images on the product page.', 'carton' ),
		'id' 		=> 'shop_thumbnail_image_size',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'default'	=> array(
			'width' 	=> '90',
			'height'	=> '90',
			'crop'		=> 1
		),
		'desc_tip'	=>  true,
	),

	array( 'type' => 'sectionend', 'id' => 'image_options' ),

)); // End catalog settings


$carton_settings['inventory'] = apply_filters('carton_inventory_settings', array(

	array(	'title' => __( 'Inventory Options', 'carton' ), 'type' => 'title','desc' => '', 'id' => 'inventory_options' ),

	array(
		'title' => __( 'Manage Stock', 'carton' ),
		'desc' 		=> __( 'Enable stock management', 'carton' ),
		'id' 		=> 'carton_manage_stock',
		'default'	=> 'yes',
		'type' 		=> 'checkbox'
	),

	array(
		'title' => __( 'Hold Stock (minutes)', 'carton' ),
		'desc' 		=> __( 'Hold stock (for unpaid orders) for x minutes. When this limit is reached, the pending order will be cancelled. Leave blank to disable.', 'carton' ),
		'id' 		=> 'carton_hold_stock_minutes',
		'type' 		=> 'number',
		'custom_attributes' => array(
			'min' 	=> 0,
			'step' 	=> 1
		),
		'css' 		=> 'width:50px;',
		'default'	=> '60'
	),

	array(
		'title' => __( 'Notifications', 'carton' ),
		'desc' 		=> __( 'Enable low stock notifications', 'carton' ),
		'id' 		=> 'carton_notify_low_stock',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup' => 'start'
	),

	array(
		'desc' 		=> __( 'Enable out of stock notifications', 'carton' ),
		'id' 		=> 'carton_notify_no_stock',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup' => 'end'
	),

	array(
		'title' => __( 'Notification Recipient', 'carton' ),
		'desc' 		=> '',
		'id' 		=> 'carton_stock_email_recipient',
		'type' 		=> 'email',
		'default'	=> get_option( 'admin_email' )
	),

	array(
		'title' => __( 'Low Stock Threshold', 'carton' ),
		'desc' 		=> '',
		'id' 		=> 'carton_notify_low_stock_amount',
		'css' 		=> 'width:50px;',
		'type' 		=> 'number',
		'custom_attributes' => array(
			'min' 	=> 0,
			'step' 	=> 1
		),
		'default'	=> '2'
	),

	array(
		'title' => __( 'Out Of Stock Threshold', 'carton' ),
		'desc' 		=> '',
		'id' 		=> 'carton_notify_no_stock_amount',
		'css' 		=> 'width:50px;',
		'type' 		=> 'number',
		'custom_attributes' => array(
			'min' 	=> 0,
			'step' 	=> 1
		),
		'default'	=> '0'
	),

	array(
		'title' => __( 'Out Of Stock Visibility', 'carton' ),
		'desc' 		=> __( 'Hide out of stock items from the catalog', 'carton' ),
		'id' 		=> 'carton_hide_out_of_stock_items',
		'default'	=> 'no',
		'type' 		=> 'checkbox'
	),

	array(
		'title' => __( 'Stock Display Format', 'carton' ),
		'desc' 		=> __( 'This controls how stock is displayed on the frontend.', 'carton' ),
		'id' 		=> 'carton_stock_format',
		'css' 		=> 'min-width:150px;',
		'default'	=> '',
		'type' 		=> 'select',
		'options' => array(
			''  			=> __( 'Always show stock e.g. "12 in stock"', 'carton' ),
			'low_amount'	=> __( 'Only show stock when low e.g. "Only 2 left in stock" vs. "In Stock"', 'carton' ),
			'no_amount' 	=> __( 'Never show stock amount', 'carton' ),
		),
		'desc_tip'	=>  true,
	),

	array( 'type' => 'sectionend', 'id' => 'inventory_options'),

)); // End inventory settings


$carton_settings['shipping'] = apply_filters('carton_shipping_settings', array(

	array( 'title' => __( 'Shipping Options', 'carton' ), 'type' => 'title', 'id' => 'shipping_options' ),

	array(
		'title' 		=> __( 'Shipping Calculations', 'carton' ),
		'desc' 		=> __( 'Enable shipping', 'carton' ),
		'id' 		=> 'carton_calc_shipping',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Enable the shipping calculator on the cart page', 'carton' ),
		'id' 		=> 'carton_enable_shipping_calc',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),

	array(
		'desc' 		=> __( 'Hide shipping costs until an address is entered', 'carton' ),
		'id' 		=> 'carton_shipping_cost_requires_address',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),

	array(
		'title' 	=> __( 'Shipping Method Display', 'carton' ),
		'desc' 		=> __( 'This controls how multiple shipping methods are displayed on the frontend.', 'carton' ),
		'id' 		=> 'carton_shipping_method_format',
		'css' 		=> 'min-width:150px;',
		'default'	=> '',
		'type' 		=> 'select',
		'options' => array(
			''  			=> __( 'Radio buttons', 'carton' ),
			'select'		=> __( 'Select box', 'carton' ),
		),
		'desc_tip'	=>  true,
	),

	array(
		'title' 	=> __( 'Shipping Destination', 'carton' ),
		'desc' 		=> __( 'Only ship to the users billing address', 'carton' ),
		'id' 		=> 'carton_ship_to_billing_address_only',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'start'
	),

	array(
		'desc' 		=> __( 'Ship to billing address by default', 'carton' ),
		'id' 		=> 'carton_ship_to_same_address',
		'default'	=> 'yes',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> ''
	),

	array(
		'desc' 		=> __( 'Collect shipping address even when not required', 'carton' ),
		'id' 		=> 'carton_require_shipping_address',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
		'checkboxgroup'		=> 'end'
	),

	array(
		'type' 		=> 'shipping_methods',
	),

	array( 'type' => 'sectionend', 'id' => 'shipping_options' ),

)); // End shipping settings


$carton_settings['payment_gateways'] = apply_filters('carton_payment_gateways_settings', array(

	array( 'title' => __( 'Payment Gateways', 'carton' ), 'desc' => __( 'Installed payment gateways are displayed below. Drag and drop payment gateways to control their display order on the checkout.', 'carton' ), 'type' => 'title', 'id' => 'payment_gateways_options' ),

	array(
		'type' 		=> 'payment_gateways',
	),

	array( 'type' => 'sectionend', 'id' => 'payment_gateways_options' ),

)); // End payment_gateway settings

$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'carton_tax_classes' ) ) ) );
$classes_options = array();
if ( $tax_classes )
	foreach ( $tax_classes as $class )
		$classes_options[ sanitize_title( $class ) ] = esc_html( $class );

$carton_settings['tax'] = apply_filters('carton_tax_settings', array(

	array(	'title' => __( 'Tax Options', 'carton' ), 'type' => 'title','desc' => '', 'id' => 'tax_options' ),

	array(
		'title' => __( 'Enable Taxes', 'carton' ),
		'desc' 		=> __( 'Enable taxes and tax calculations', 'carton' ),
		'id' 		=> 'carton_calc_taxes',
		'default'	=> 'no',
		'type' 		=> 'checkbox'
	),

	array(
		'title' => __( 'Prices Entered With Tax', 'carton' ),
		'id' 		=> 'carton_prices_include_tax',
		'default'	=> 'no',
		'type' 		=> 'radio',
		'desc_tip'	=>  __( 'This option is important as it will affect how you input prices. Changing it will not update existing products.', 'carton' ),
		'options'	=> array(
			'yes' => __( 'Yes, I will enter prices inclusive of tax', 'carton' ),
			'no' => __( 'No, I will enter prices exclusive of tax', 'carton' )
		),
	),

	array(
		'title'     => __( 'Calculate Tax Based On:', 'carton' ),
		'id'        => 'carton_tax_based_on',
		'desc_tip'	=>  __( 'This option determines which address is used to calculate tax.', 'carton' ),
		'default'   => 'shipping',
		'type'      => 'select',
		'options'   => array(
			'shipping' => __( 'Customer shipping address', 'carton' ),
			'billing'  => __( 'Customer billing address', 'carton' ),
			'base'     => __( 'Shop base address', 'carton' )
		),
	),

	array(
		'title'     => __( 'Default Customer Address:', 'carton' ),
		'id'        => 'carton_default_customer_address',
		'desc_tip'	=>  __( 'This option determines the customers default address (before they input their own).', 'carton' ),
		'default'   => 'base',
		'type'      => 'select',
		'options'   => array(
			''     => __( 'No address', 'carton' ),
			'base' => __( 'Shop base address', 'carton' ),
		),
	),

	array(
		'title' 		=> __( 'Shipping Tax Class:', 'carton' ),
		'desc' 		=> __( 'Optionally control which tax class shipping gets, or leave it so shipping tax is based on the cart items themselves.', 'carton' ),
		'id' 		=> 'carton_shipping_tax_class',
		'css' 		=> 'min-width:150px;',
		'default'	=> 'title',
		'type' 		=> 'select',
		'options' 	=> array( '' => __( 'Shipping tax class based on cart items', 'carton' ), 'standard' => __( 'Standard', 'carton' ) ) + $classes_options,
		'desc_tip'	=>  true,
	),

	array(
		'title' => __( 'Rounding', 'carton' ),
		'desc' 		=> __( 'Round tax at subtotal level, instead of rounding per line', 'carton' ),
		'id' 		=> 'carton_tax_round_at_subtotal',
		'default'	=> 'no',
		'type' 		=> 'checkbox',
	),

	array(
		'title' 		=> __( 'Additional Tax Classes', 'carton' ),
		'desc' 		=> __( 'List additonal tax classes below (1 per line). This is in addition to the default <code>Standard Rate</code>. Tax classes can be assigned to products.', 'carton' ),
		'id' 		=> 'carton_tax_classes',
		'css' 		=> 'width:100%; height: 65px;',
		'type' 		=> 'textarea',
		'default'	=> sprintf( __( 'Reduced Rate%sZero Rate', 'carton' ), PHP_EOL )
	),

	array(
		'title'   => __( 'Display prices during cart/checkout:', 'carton' ),
		'id'      => 'carton_tax_display_cart',
		'default' => 'excl',
		'type'    => 'select',
		'options' => array(
			'incl'   => __( 'Including tax', 'carton' ),
			'excl'   => __( 'Excluding tax', 'carton' ),
		),
	),

	array( 'type' => 'sectionend', 'id' => 'tax_options' ),

)); // End tax settings

$carton_settings['email'] = apply_filters('carton_email_settings', array(

	array( 'type' => 'sectionend', 'id' => 'email_recipient_options' ),

	array(	'title' => __( 'Email Sender Options', 'carton' ), 'type' => 'title', 'desc' => __( 'The following options affect the sender (email address and name) used in CartoN emails.', 'carton' ), 'id' => 'email_options' ),

	array(
		'title' => __( '"From" Name', 'carton' ),
		'desc' 		=> '',
		'id' 		=> 'carton_email_from_name',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'default'	=> esc_attr(get_bloginfo('title'))
	),

	array(
		'title' => __( '"From" Email Address', 'carton' ),
		'desc' 		=> '',
		'id' 		=> 'carton_email_from_address',
		'type' 		=> 'email',
		'custom_attributes' => array(
			'multiple' 	=> 'multiple'
		),
		'css' 		=> 'min-width:300px;',
		'default'	=> get_option('admin_email')
	),

	array( 'type' => 'sectionend', 'id' => 'email_options' ),

	array(	'title' => __( 'Email Template', 'carton' ), 'type' => 'title', 'desc' => sprintf(__( 'This section lets you customise the CartoN emails. <a href="%s" target="_blank">Click here to preview your email template</a>. For more advanced control copy <code>carton/templates/emails/</code> to <code>yourtheme/carton/emails/</code>.', 'carton' ), wp_nonce_url(admin_url('?preview_carton_mail=true'), 'preview-mail')), 'id' => 'email_template_options' ),

	array(
		'title' => __( 'Header Image', 'carton' ),
		'desc' 		=> sprintf(__( 'Enter a URL to an image you want to show in the email\'s header. Upload your image using the <a href="%s">media uploader</a>.', 'carton' ), admin_url('media-new.php')),
		'id' 		=> 'carton_email_header_image',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'default'	=> ''
	),

	array(
		'title' => __( 'Email Footer Text', 'carton' ),
		'desc' 		=> __( 'The text to appear in the footer of CartoN emails.', 'carton' ),
		'id' 		=> 'carton_email_footer_text',
		'css' 		=> 'width:100%; height: 75px;',
		'type' 		=> 'textarea',
		'default'	=> get_bloginfo('title') . ' - ' . __( 'Powered by CartoN', 'carton' )
	),

	array(
		'title' => __( 'Base Colour', 'carton' ),
		'desc' 		=> __( 'The base colour for CartoN email templates. Default <code>#557da1</code>.', 'carton' ),
		'id' 		=> 'carton_email_base_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'default'	=> '#557da1'
	),

	array(
		'title' => __( 'Background Colour', 'carton' ),
		'desc' 		=> __( 'The background colour for CartoN email templates. Default <code>#f5f5f5</code>.', 'carton' ),
		'id' 		=> 'carton_email_background_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'default'	=> '#f5f5f5'
	),

	array(
		'title' => __( 'Email Body Background Colour', 'carton' ),
		'desc' 		=> __( 'The main body background colour. Default <code>#fdfdfd</code>.', 'carton' ),
		'id' 		=> 'carton_email_body_background_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'default'	=> '#fdfdfd'
	),

	array(
		'title' => __( 'Email Body Text Colour', 'carton' ),
		'desc' 		=> __( 'The main body text colour. Default <code>#505050</code>.', 'carton' ),
		'id' 		=> 'carton_email_text_color',
		'type' 		=> 'color',
		'css' 		=> 'width:6em;',
		'default'	=> '#505050'
	),

	array( 'type' => 'sectionend', 'id' => 'email_template_options' ),

)); // End email settings
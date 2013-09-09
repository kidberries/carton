<?php
/**
 * CartoN Hooks
 *
 * Action/filter hooks used for CartoN functions/templates
 *
 * @author 		CartonThemes
 * @category 	Core
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Template Hooks ********************************************************/

if ( ! is_admin() || defined('DOING_AJAX') ) {

	/**
	 * Content Wrappers
	 *
	 * @see carton_output_content_wrapper()
	 * @see carton_output_content_wrapper_end()
	 */
	add_action( 'carton_before_main_content', 'carton_output_content_wrapper', 10 );
	add_action( 'carton_after_main_content', 'carton_output_content_wrapper_end', 10 );

	/**
	 * Sale flashes
	 *
	 * @see carton_show_product_loop_sale_flash()
	 * @see carton_show_product_sale_flash()
	 */
	add_action( 'carton_before_shop_loop_item_title', 'carton_show_product_loop_sale_flash', 10 );
	add_action( 'carton_before_single_product_summary', 'carton_show_product_sale_flash', 10 );

	/**
	 * Breadcrumbs
	 *
	 * @see carton_breadcrumb()
	 */
	add_action( 'carton_before_main_content', 'carton_breadcrumb', 20, 0 );

	/**
	 * Sidebar
	 *
	 * @see carton_get_sidebar()
	 */
	add_action( 'carton_sidebar', 'carton_get_sidebar', 10 );

	/**
	 * Archive descriptions
	 *
	 * @see carton_taxonomy_archive_description()
	 * @see carton_product_archive_description()
	 */
//	add_action( 'carton_archive_description', 'carton_taxonomy_archive_description', 10 );
	add_action( 'carton_archive_description', 'carton_product_archive_description', 10 );

	/**
	 * Products Loop
	 *
	 * @see carton_show_messages()
	 * @see carton_result_count()
	 * @see carton_catalog_ordering()
	 */
	add_action( 'carton_before_shop_loop', 'carton_show_messages', 10 );
	add_action( 'carton_before_shop_loop', 'carton_result_count', 20 );
	add_action( 'carton_before_shop_loop', 'carton_catalog_ordering', 30 );

	/**
	 * Product Loop Items
	 *
	 * @see carton_show_messages()
	 * @see carton_template_loop_add_to_cart()
	 * @see carton_template_loop_product_thumbnail()
	 * @see carton_template_loop_price()
	 */
	add_action( 'carton_after_shop_loop_item', 'carton_template_loop_add_to_cart', 10 );
	add_action( 'carton_before_shop_loop_item_title', 'carton_template_loop_product_thumbnail', 10 );
	add_action( 'carton_after_shop_loop_item_title', 'carton_template_loop_price', 10 );
	add_action( 'carton_after_shop_loop_item_title', 'carton_template_loop_rating', 5 );

	/**
	 * Subcategories
	 *
	 * @see carton_subcategory_thumbnail()
	 */
	add_action( 'carton_before_subcategory_title', 'carton_subcategory_thumbnail', 10 );

	/**
	 * Before Single Products
	 *
	 * @see carton_show_messages()
	 */
	add_action( 'carton_before_single_product', 'carton_show_messages', 10 );

	/**
	 * Before Single Products Summary Div
	 *
	 * @see carton_show_product_images()
	 * @see carton_show_product_thumbnails()
	 */
	add_action( 'carton_before_single_product_summary', 'carton_show_product_images', 20 );
	add_action( 'carton_product_thumbnails', 'carton_show_product_thumbnails', 20 );

	/**
	 * After Single Products Summary Div
	 *
	 * @see carton_output_product_data_tabs()
	 * @see carton_upsell_display()
	 * @see carton_output_related_products()
	 */
	add_action( 'carton_after_single_product_summary', 'carton_output_product_data_tabs', 10 );
	add_action( 'carton_after_single_product_summary', 'carton_upsell_display', 15 );
	add_action( 'carton_after_single_product_summary', 'carton_output_related_products', 20 );

	/**
	 * Product Summary Box
	 *
	 * @see carton_template_single_title()
	 * @see carton_template_single_price()
	 * @see carton_template_single_excerpt()
	 * @see carton_template_single_meta()
	 * @see carton_template_single_sharing()
	 */
	add_action( 'carton_single_product_summary', 'carton_template_single_title', 5 );
	add_action( 'carton_single_product_summary', 'carton_template_single_price', 10 );
	add_action( 'carton_single_product_summary', 'carton_template_single_excerpt', 20 );
	add_action( 'carton_single_product_summary', 'carton_template_single_meta', 40 );
	add_action( 'carton_single_product_summary', 'carton_template_single_sharing', 50 );


	/**
	 * Product Add to cart
	 *
	 * @see carton_template_single_add_to_cart()
	 * @see carton_simple_add_to_cart()
	 * @see carton_grouped_add_to_cart()
	 * @see carton_variable_add_to_cart()
	 * @see carton_external_add_to_cart()
	 */
	add_action( 'carton_single_product_summary', 'carton_template_single_add_to_cart', 30 );
	add_action( 'carton_simple_add_to_cart', 'carton_simple_add_to_cart', 30 );
	add_action( 'carton_grouped_add_to_cart', 'carton_grouped_add_to_cart', 30 );
	add_action( 'carton_variable_add_to_cart', 'carton_variable_add_to_cart', 30 );
	add_action( 'carton_external_add_to_cart', 'carton_external_add_to_cart', 30 );

	/**
	 * Pagination after shop loops
	 *
	 * @see carton_pagination()
	 */
	add_action( 'carton_after_shop_loop', 'carton_pagination', 10 );

	/**
	 * Product page tabs
	 */
	add_filter( 'carton_product_tabs', 'carton_default_product_tabs' );
	add_filter( 'carton_product_tabs', 'carton_sort_product_tabs', 99 );

	/**
	 * Checkout
	 *
	 * @see carton_checkout_login_form()
	 * @see carton_checkout_coupon_form()
	 * @see carton_order_review()
	 */
	add_action( 'carton_before_checkout_form', 'carton_checkout_login_form', 10 );
	add_action( 'carton_before_checkout_form', 'carton_checkout_coupon_form', 10 );
	add_action( 'carton_checkout_order_review', 'carton_order_review', 10 );

	/**
	 * Cart
	 *
	 * @see carton_cross_sell_display()
	 */
	add_action( 'carton_cart_collaterals', 'carton_cross_sell_display' );

	/**
	 * Footer
	 *
	 * @see carton_demo_store()
	 */
	add_action( 'wp_footer', 'carton_demo_store' );

	/**
	 * Order details
	 *
	 * @see carton_order_details_table()
	 * @see carton_order_details_table()
	 */
	add_action( 'carton_view_order', 'carton_order_details_table', 10 );
	add_action( 'carton_thankyou', 'carton_order_details_table', 10 );
}

/** Store Event Hooks *****************************************************/

/**
 * Shop Page Handling and Support
 *
 * @see carton_template_redirect()
 * @see carton_nav_menu_item_classes()
 * @see carton_list_pages()
 */
add_action( 'template_redirect', 'carton_template_redirect' );
add_filter( 'wp_nav_menu_objects',  'carton_nav_menu_item_classes', 2, 20 );
add_filter( 'wp_list_pages', 'carton_list_pages' );

/**
 * Logout link
 *
 * @see carton_nav_menu_items()
 */
add_filter( 'wp_nav_menu_objects', 'carton_nav_menu_items', 10, 2 );

/**
 * Clear the cart
 *
 * @see carton_empty_cart()
 * @see carton_clear_cart_after_payment()
 */
if ( get_option( 'carton_clear_cart_on_logout' ) == 'yes' )
	add_action( 'wp_logout', 'carton_empty_cart' );
add_action( 'get_header', 'carton_clear_cart_after_payment' );

/**
 * Disable admin bar
 *
 * @see carton_disable_admin_bar()
 */
add_filter( 'show_admin_bar', 'carton_disable_admin_bar', 10, 1 );

/**
 * Cart Actions
 *
 * @see carton_update_cart_action()
 * @see carton_add_to_cart_action()
 * @see carton_load_persistent_cart()
 */
add_action( 'init', 'carton_update_cart_action' );
add_action( 'init', 'carton_add_to_cart_action' );
add_action( 'wp_login', 'carton_load_persistent_cart', 1, 2 );

/**
 * Checkout Actions
 *
 * @see carton_checkout_action()
 * @see carton_pay_action()
 */
add_action( 'init', 'carton_checkout_action', 20 );
add_action( 'init', 'carton_pay_action', 20 );

/**
 * Login and Registration
 *
 * @see carton_process_login()
 * @see carton_process_registration()
 */
add_action( 'init', 'carton_process_login' );
add_action( 'init', 'carton_process_registration' );

/**
 * Product Downloads
 *
 * @see carton_download_product()
 */
add_action('init', 'carton_download_product');

/**
 * Analytics
 *
 * @see carton_ecommerce_tracking_piwik()
 */
add_action( 'carton_thankyou', 'carton_ecommerce_tracking_piwik' );

/**
 * RSS Feeds
 *
 * @see carton_products_rss_feed()
 */
add_action( 'wp_head', 'carton_products_rss_feed' );

/**
 * Order actions
 *
 * @see carton_cancel_order()
 * @see carton_order_again()
 */
add_action( 'init', 'carton_cancel_order' );
add_action( 'init', 'carton_order_again' );

/**
 * Star Ratings
 *
 * @see carton_add_comment_rating()
 * @see carton_check_comment_rating()
 */
add_action( 'comment_post', 'carton_add_comment_rating', 1 );
add_filter( 'preprocess_comment', 'carton_check_comment_rating', 0 );

/**
 * Filters
 */
add_filter( 'carton_short_description', 'wptexturize'        );
add_filter( 'carton_short_description', 'convert_smilies'    );
add_filter( 'carton_short_description', 'convert_chars'      );
add_filter( 'carton_short_description', 'wpautop'            );
add_filter( 'carton_short_description', 'shortcode_unautop'  );
add_filter( 'carton_short_description', 'prepend_attachment' );
add_filter( 'carton_short_description', 'do_shortcode', 11 ); // AFTER wpautop()
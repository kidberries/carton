<?php
/**
 * Update WC to 2.0
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Updates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $carton;

// Upgrade old style files paths to support multiple file paths
$existing_file_paths = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_file_path' AND meta_value != '';" );

if ( $existing_file_paths ) {

	foreach( $existing_file_paths as $existing_file_path ) {

		$old_file_path = trim( $existing_file_path->meta_value );

		if ( ! empty( $old_file_path ) ) {
			$file_paths = serialize( array( md5( $old_file_path ) => $old_file_path ) );

			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = '_file_paths', meta_value = %s WHERE meta_id = %d", $file_paths, $existing_file_path->meta_id ) );

			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}carton_downloadable_product_permissions SET download_id = %s WHERE product_id = %d", md5( $old_file_path ), $existing_file_path->post_id ) );

		}
	}
}

// Update table primary keys
$wpdb->query( "ALTER TABLE {$wpdb->prefix}carton_downloadable_product_permissions DROP PRIMARY KEY" );

$wpdb->query( "ALTER TABLE {$wpdb->prefix}carton_downloadable_product_permissions ADD PRIMARY KEY (  `product_id` ,  `order_id` ,  `order_key` ,  `download_id` )" );

// Setup default permalinks if shop page is defined
$permalinks 	= get_option( 'carton_permalinks' );
$shop_page_id 	= carton_get_page_id( 'shop' );

if ( empty( $permalinks ) && $shop_page_id > 0 ) {

	$base_slug 		= $shop_page_id > 0 && get_page( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop';

	$category_base 	= get_option('carton_prepend_shop_page_to_urls') == "yes" ? trailingslashit( $base_slug ) : '';
	$category_slug 	= get_option('carton_product_category_slug') ? get_option('carton_product_category_slug') : _x( 'product-category', 'slug', 'carton' );
	$tag_slug 		= get_option('carton_product_tag_slug') ? get_option('carton_product_tag_slug') : _x( 'product-tag', 'slug', 'carton' );

	if ( 'yes' == get_option('carton_prepend_shop_page_to_products') ) {
		$product_base = trailingslashit( $base_slug );
	} else {
		if ( ( $product_slug = get_option('carton_product_slug') ) !== false && ! empty( $product_slug ) ) {
			$product_base = trailingslashit( $product_slug );
		} else {
			$product_base = trailingslashit( _x('product', 'slug', 'carton') );
		}
	}

	if ( get_option('carton_prepend_category_to_products') == 'yes' )
		$product_base .= trailingslashit('%product_cat%');

	$permalinks = array(
		'product_base' 		=> untrailingslashit( $product_base ),
		'category_base' 	=> untrailingslashit( $category_base . $category_slug ),
		'attribute_base' 	=> untrailingslashit( $category_base ),
		'tag_base' 			=> untrailingslashit( $category_base . $tag_slug )
	);

	update_option( 'carton_permalinks', $permalinks );
}

// Update subcat display settings
if ( get_option( 'carton_shop_show_subcategories' ) == 'yes' ) {
	if ( get_option( 'carton_hide_products_when_showing_subcategories' ) == 'yes' ) {
		update_option( 'carton_shop_page_display', 'subcategories' );
	} else {
		update_option( 'carton_shop_page_display', 'both' );
	}
}

if ( get_option( 'carton_show_subcategories' ) == 'yes' ) {
	if ( get_option( 'carton_hide_products_when_showing_subcategories' ) == 'yes' ) {
		update_option( 'carton_category_archive_display', 'subcategories' );
	} else {
		update_option( 'carton_category_archive_display', 'both' );
	}
}

// Update tax rates
$loop = 0;
$tax_rates = get_option( 'carton_tax_rates' );

if ( $tax_rates )
	foreach ( $tax_rates as $tax_rate ) {

		foreach ( $tax_rate['countries'] as $country => $states ) {

			$states = array_reverse( $states );

			foreach ( $states as $state ) {

				if ( $state == '*' )
					$state = '';

				$wpdb->insert(
					$wpdb->prefix . "carton_tax_rates",
					array(
						'tax_rate_country'  => $country,
						'tax_rate_state'    => $state,
						'tax_rate'          => $tax_rate['rate'],
						'tax_rate_name'     => $tax_rate['label'],
						'tax_rate_priority' => 1,
						'tax_rate_compound' => $tax_rate['compound'] == 'yes' ? 1 : 0,
						'tax_rate_shipping' => $tax_rate['shipping'] == 'yes' ? 1 : 0,
						'tax_rate_order'    => $loop,
						'tax_rate_class'    => $tax_rate['class']
					)
				);

				$loop++;
			}
		}
	}

$local_tax_rates = get_option( 'carton_local_tax_rates' );

if ( $local_tax_rates )
	foreach ( $local_tax_rates as $tax_rate ) {

		$location_type = $tax_rate['location_type'] == 'postcode' ? 'postcode' : 'city';

		if ( $tax_rate['state'] == '*' )
			$tax_rate['state'] = '';

		$wpdb->insert(
			$wpdb->prefix . "carton_tax_rates",
			array(
				'tax_rate_country'  => $tax_rate['country'],
				'tax_rate_state'    => $tax_rate['state'],
				'tax_rate'          => $tax_rate['rate'],
				'tax_rate_name'     => $tax_rate['label'],
				'tax_rate_priority' => 2,
				'tax_rate_compound' => $tax_rate['compound'] == 'yes' ? 1 : 0,
				'tax_rate_shipping' => $tax_rate['shipping'] == 'yes' ? 1 : 0,
				'tax_rate_order'    => $loop,
				'tax_rate_class'    => $tax_rate['class']
			)
		);

		$tax_rate_id = $wpdb->insert_id;

		if ( $tax_rate['locations'] ) {
			foreach ( $tax_rate['locations'] as $location ) {

				$wpdb->insert(
					$wpdb->prefix . "carton_tax_rate_locations",
					array(
						'location_code' => $location,
						'tax_rate_id'   => $tax_rate_id,
						'location_type' => $location_type,
					)
				);

			}
		}

		$loop++;
	}

update_option( 'carton_tax_rates_backup', $tax_rates );
update_option( 'carton_local_tax_rates_backup', $local_tax_rates );
delete_option( 'carton_tax_rates' );
delete_option( 'carton_local_tax_rates' );

// Create lost password page
carton_create_page( esc_sql( _x( 'lost-password', 'page_slug', 'carton' ) ), 'carton_lost_password_page_id', __( 'Lost Password', 'carton' ), '[carton_lost_password]', carton_get_page_id( 'myaccount' ) );


// Now its time for the massive update to line items - move them to the new DB tables
// Reverse with UPDATE `wpctn_postmeta` SET meta_key = '_order_items' WHERE meta_key = '_order_items_old'
$order_item_rows = $wpdb->get_results( "
	SELECT * FROM {$wpdb->postmeta}
	WHERE meta_key = '_order_items'
" );

foreach ( $order_item_rows as $order_item_row ) {

	$order_items = (array) maybe_unserialize( $order_item_row->meta_value );

	foreach ( $order_items as $order_item ) {

		if ( ! isset( $order_item['line_total'] ) && isset( $order_item['taxrate'] ) && isset( $order_item['cost'] ) ) {
			$order_item['line_tax'] 			= number_format( ( $order_item['cost'] * $order_item['qty'] ) * ( $order_item['taxrate'] / 100 ), 2, '.', '' );
			$order_item['line_total'] 			= $order_item['cost'] * $order_item['qty'];
			$order_item['line_subtotal_tax'] 	= $order_item['line_tax'];
			$order_item['line_subtotal'] 		= $order_item['line_total'];
		}

		$order_item['line_tax'] 			= isset( $order_item['line_tax'] ) ? $order_item['line_tax'] : 0;
		$order_item['line_total']			= isset( $order_item['line_total'] ) ? $order_item['line_total'] : 0;
		$order_item['line_subtotal_tax'] 	= isset( $order_item['line_subtotal_tax'] ) ? $order_item['line_subtotal_tax'] : 0;
		$order_item['line_subtotal'] 		= isset( $order_item['line_subtotal'] ) ? $order_item['line_subtotal'] : 0;

		$item_id = carton_add_order_item( $order_item_row->post_id, array(
	 		'order_item_name' 		=> $order_item['name'],
	 		'order_item_type' 		=> 'line_item'
	 	) );

	 	// Add line item meta
	 	if ( $item_id ) {
		 	carton_add_order_item_meta( $item_id, '_qty', absint( $order_item['qty'] ) );
		 	carton_add_order_item_meta( $item_id, '_tax_class', $order_item['tax_class'] );
		 	carton_add_order_item_meta( $item_id, '_product_id', $order_item['id'] );
		 	carton_add_order_item_meta( $item_id, '_variation_id', $order_item['variation_id'] );
		 	carton_add_order_item_meta( $item_id, '_line_subtotal', carton_format_decimal( $order_item['line_subtotal'] ) );
		 	carton_add_order_item_meta( $item_id, '_line_subtotal_tax', carton_format_decimal( $order_item['line_subtotal_tax'] ) );
		 	carton_add_order_item_meta( $item_id, '_line_total', carton_format_decimal( $order_item['line_total'] ) );
		 	carton_add_order_item_meta( $item_id, '_line_tax', carton_format_decimal( $order_item['line_tax'] ) );

		 	$meta_rows = array();

			// Insert meta
			if ( ! empty( $order_item['item_meta'] ) ) {
				foreach ( $order_item['item_meta'] as $key => $meta ) {
					// Backwards compatibility
					if ( is_array( $meta ) && isset( $meta['meta_name'] ) ) {
						$meta_rows[] = '(' . $item_id . ',"' . $wpdb->escape( $meta['meta_name'] ) . '","' . $wpdb->escape( $meta['meta_value'] ) . '")';
					} else {
						$meta_rows[] = '(' . $item_id . ',"' . $wpdb->escape( $key ) . '","' . $wpdb->escape( $meta ) . '")';
					}
				}
			}

			// Insert meta rows at once
			if ( sizeof( $meta_rows ) > 0 ) {
				$wpdb->query( $wpdb->prepare( "
					INSERT INTO {$wpdb->prefix}carton_order_itemmeta ( order_item_id, meta_key, meta_value )
					VALUES " . implode( ',', $meta_rows ) . ";
				", $order_item_row->post_id ) );
			}

			// Delete from DB (rename)
			$wpdb->query( $wpdb->prepare( "
				UPDATE {$wpdb->postmeta}
				SET meta_key = '_order_items_old'
				WHERE meta_key = '_order_items'
				AND post_id = %d
			", $order_item_row->post_id ) );
	 	}

		unset( $meta_rows, $item_id, $order_item );
	}
}

// Do the same kind of update for order_taxes - move to lines
// Reverse with UPDATE `wpctn_postmeta` SET meta_key = '_order_taxes' WHERE meta_key = '_order_taxes_old'
$order_tax_rows = $wpdb->get_results( "
	SELECT * FROM {$wpdb->postmeta}
	WHERE meta_key = '_order_taxes'
" );

foreach ( $order_tax_rows as $order_tax_row ) {

	$order_taxes = (array) maybe_unserialize( $order_tax_row->meta_value );

	if ( $order_taxes ) {
		foreach( $order_taxes as $order_tax ) {

			if ( ! isset( $order_tax['label'] ) || ! isset( $order_tax['cart_tax'] ) || ! isset( $order_tax['shipping_tax'] ) )
				continue;

			$item_id = carton_add_order_item( $order_tax_row->post_id, array(
		 		'order_item_name' 		=> $order_tax['label'],
		 		'order_item_type' 		=> 'tax'
		 	) );

		 	// Add line item meta
		 	if ( $item_id ) {
			 	carton_add_order_item_meta( $item_id, 'compound', absint( isset( $order_tax['compound'] ) ? $order_tax['compound'] : 0 ) );
			 	carton_add_order_item_meta( $item_id, 'tax_amount', carton_clean( $order_tax['cart_tax'] ) );
			 	carton_add_order_item_meta( $item_id, 'shipping_tax_amount', carton_clean( $order_tax['shipping_tax'] ) );
			}

			// Delete from DB (rename)
			$wpdb->query( $wpdb->prepare( "
				UPDATE {$wpdb->postmeta}
				SET meta_key = '_order_taxes_old'
				WHERE meta_key = '_order_taxes'
				AND post_id = %d
			", $order_tax_row->post_id ) );

			unset( $tax_amount );
		}
	}
}

// Grab the pre 2.0 Image options and use to populate the new image options settings,
// cleaning up afterwards like nice people do

foreach ( array( 'catalog', 'single', 'thumbnail' ) as $value ) {

	$old_settings = array_filter( array(
		'width' => get_option( 'carton_' . $value . '_image_width' ),
		'height' => get_option( 'carton_' . $value . '_image_height' ),
		'crop' => get_option( 'carton_' . $value . '_image_crop' )
	) );

	if ( ! empty(  $old_settings  ) && update_option( 'shop_' . $value . '_image_size', $old_settings ) ){

		delete_option( 'carton_' . $value . '_image_width' );
		delete_option( 'carton_' . $value . '_image_height' );
		delete_option( 'carton_' . $value . '_image_crop' );

	}
}

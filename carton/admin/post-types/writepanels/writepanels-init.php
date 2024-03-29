<?php
/**
 * CartoN Write Panels
 *
 * Sets up the write panels used by products and orders (custom post types)
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/WritePanels
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Product data writepanel */
include_once('writepanel-product_data.php');

/** Product images writepanel */
include_once('writepanel-product_images.php');

/** Product search synonyms */
include_once('writepanel-product_synonyms.php');

/** Coupon data writepanel */
include_once('writepanel-coupon_data.php');

/** Discount data writepanel */
include_once('writepanel-discount_data.php');

/** Order data writepanel */
include_once('writepanel-order_data.php');

/** Order notes writepanel */
include_once('writepanel-order_notes.php');

/** Order downloads writepanel */
include_once('writepanel-order_downloads.php');


/**
 * Init the meta boxes.
 *
 * Inits the write panels for both products and orders. Also removes unused default write panels.
 *
 * @access public
 * @return void
 */
function carton_meta_boxes() {
	global $post;

	// Products
	add_meta_box( 'carton-product-data', __( 'Product Data', 'carton' ), 'carton_product_data_box', 'product', 'normal', 'high' );
	add_meta_box( 'carton-product-images', __( 'Product Gallery', 'carton' ), 'carton_product_images_box', 'product', 'side' );

	// Site search keywords and synonyms
	add_meta_box( 'carton-product-tsv-synonyms', __( 'Search Synonyms/Keywords', 'carton' ), 'carton_product_synonyms_box', 'product', 'side' );


	remove_meta_box( 'product_shipping_classdiv', 'product', 'side' );
	remove_meta_box( 'pageparentdiv', 'product', 'side' );

	// Excerpt
	if ( function_exists('wp_editor') ) {
		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		add_meta_box( 'postexcerpt', __( 'Product Short Description', 'carton' ), 'carton_product_short_description_meta_box', 'product', 'normal' );
	}

	// Comments/Reviews
	remove_meta_box( 'commentstatusdiv', 'product', 'normal' );
	remove_meta_box( 'commentstatusdiv', 'product', 'side' );

	if ( ('publish' == $post->post_status || 'private' == $post->post_status) ) {
		remove_meta_box( 'commentsdiv', 'product', 'normal' );
		add_meta_box( 'commentsdiv', __( 'Reviews', 'carton' ), 'post_comment_meta_box', 'product', 'normal' );
	}

	// Orders
	add_meta_box( 'carton-order-data', __( 'Order Data', 'carton' ), 'carton_order_data_meta_box', 'shop_order', 'normal', 'high' );
	add_meta_box( 'carton-order-items', __( 'Order Items', 'carton' ) . ' <span class="tips" data-tip="' . __( 'Note: if you edit quantities or remove items from the order you will need to manually update stock levels.', 'carton' ) . '">[?]</span>', 'carton_order_items_meta_box', 'shop_order', 'normal', 'high');
	add_meta_box( 'carton-order-totals', __( 'Order Totals', 'carton' ), 'carton_order_totals_meta_box', 'shop_order', 'side', 'default');
	add_meta_box( 'carton-order-notes', __( 'Order Notes', 'carton' ), 'carton_order_notes_meta_box', 'shop_order', 'side', 'default');
	add_meta_box( 'carton-order-downloads', __( 'Downloadable Product Permissions', 'carton' ) . ' <span class="tips" data-tip="' . __( 'Note: Permissions for order items will automatically be granted when the order status changes to processing/completed.', 'carton' ) . '">[?]</span>', 'carton_order_downloads_meta_box', 'shop_order', 'normal', 'default');
	add_meta_box( 'carton-order-actions', __( 'Order Actions', 'carton' ), 'carton_order_actions_meta_box', 'shop_order', 'side', 'high');

	remove_meta_box( 'commentsdiv', 'shop_order' , 'normal' );
	remove_meta_box( 'carton-ecommerce-settings', 'shop_order' , 'normal' );
	remove_meta_box( 'commentstatusdiv', 'shop_order' , 'normal' );
	remove_meta_box( 'slugdiv', 'shop_order' , 'normal' );

	// Coupons
	add_meta_box( 'carton-coupon-data', __( 'Coupon Data', 'carton' ), 'carton_coupon_data_meta_box', 'shop_coupon', 'normal', 'high');

	remove_meta_box( 'carton-ecommerce-settings', 'shop_coupon' , 'normal' );
	remove_meta_box( 'commentstatusdiv', 'shop_coupon' , 'normal' );
	remove_meta_box( 'slugdiv', 'shop_coupon' , 'normal' );
        
        // Discounts
	add_meta_box( 'carton-discount-data', __( 'Discount Data', 'carton' ), 'carton_discount_data_meta_box', 'shop_discount', 'normal', 'high');

	remove_meta_box( 'carton-ecommerce-settings', 'shop_discount' , 'normal' );
	remove_meta_box( 'commentstatusdiv', 'shop_discount' , 'normal' );
	remove_meta_box( 'slugdiv', 'shop_discount' , 'normal' );
}

add_action( 'add_meta_boxes', 'carton_meta_boxes' );


/**
 * Change title boxes in admin.
 *
 * @access public
 * @param mixed $text
 * @param mixed $post
 * @return string
 */
function carton_enter_title_here( $text, $post ) {
	if ( $post->post_type == 'shop_coupon' ) return __( 'Coupon code', 'carton' );
	if ( $post->post_type == 'product' ) return __( 'Product name', 'carton' );
	return $text;
}

add_filter('enter_title_here', 'carton_enter_title_here', 1, 2);


/**
 * Save meta boxes
 *
 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
 *
 * @access public
 * @param mixed $post_id
 * @param mixed $post
 * @return void
 */
function carton_meta_boxes_save( $post_id, $post ) {
	if ( empty( $post_id ) || empty( $post ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( is_int( wp_is_post_revision( $post ) ) ) return;
	if ( is_int( wp_is_post_autosave( $post ) ) ) return;
	if ( empty( $_POST['carton_meta_nonce'] ) || ! wp_verify_nonce( $_POST['carton_meta_nonce'], 'carton_save_data' ) ) return;
	if ( !current_user_can( 'edit_post', $post_id )) return;
	if ( $post->post_type != 'product' && $post->post_type != 'shop_order' && $post->post_type != 'shop_coupon' && $post->post_type != 'shop_discount') return;
	do_action( 'carton_process_' . $post->post_type . '_meta', $post_id, $post );

	carton_meta_boxes_save_errors();
}

add_action( 'save_post', 'carton_meta_boxes_save', 1, 2 );


/**
 * Some functions, like the term recount, require the visibility to be set prior. Lets save that here.
 *
 * @access public
 * @param mixed $post_id
 * @return void
 */
function carton_pre_post_update( $post_id ) {
	if ( isset( $_POST['_visibility'] ) )
		update_post_meta( $post_id, '_visibility', stripslashes( $_POST['_visibility'] ) );
	if ( isset( $_POST['_stock_status'] ) )
		update_post_meta( $post_id, '_stock_status', stripslashes( $_POST['_stock_status'] ) );
}
add_action( 'pre_post_update', 'carton_pre_post_update' );


/**
 * Product Short Description.
 *
 * Replaces excerpt with a visual editor.
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function carton_product_short_description_meta_box( $post ) {

	$settings = array(
		'quicktags' 	=> array( 'buttons' => 'em,strong,link' ),
		'textarea_name'	=> 'excerpt',
		'quicktags' 	=> true,
		'tinymce' 		=> true,
		'editor_css'	=> '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

	wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', $settings );
}


/**
 * Change the comment box to be a review box.
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function carton_product_review_status_meta_box( $post ) {
	?>
	<input name="advanced_view" type="hidden" value="1" />
	<p class="meta-options">
		<label for="comment_status" class="selectit"><input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> /> <?php _e( 'Allow reviews.', 'carton' ) ?></label><br />
		<label for="ping_status" class="selectit"><input name="ping_status" type="checkbox" id="ping_status" value="open" <?php checked($post->ping_status, 'open'); ?> /> <?php printf( __( 'Allow <a href="%s" target="_blank">trackbacks and pingbacks</a> on this page.' ), __( 'http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments' ) ); ?></label>
		<?php do_action('post_comment_status_meta_box-options', $post); ?>
	</p>
	<?php
}


/**
 * Forces certain product data based on the product's type, e.g. grouped products cannot have a parent.
 *
 * @access public
 * @param mixed $data
 * @return array
 */
function carton_product_data( $data ) {
	global $post;
	if ($data['post_type']=='product' && isset($_POST['product-type'])) {
		$product_type = stripslashes( $_POST['product-type'] );
		switch($product_type) :
			case "grouped" :
			case "variable" :
				$data['post_parent'] = 0;
			break;
		endswitch;
	}
	return $data;
}

add_filter('wp_insert_post_data', 'carton_product_data');


/**
 * Forces the order posts to have a title in a certain format (containing the date)
 *
 * @access public
 * @param mixed $data
 * @return array
 */
function carton_order_data( $data ) {
	global $post;
	if ($data['post_type']=='shop_order' && isset($data['post_date'])) {

		$order_title = 'Order';
		if ($data['post_date']) $order_title.= ' &ndash; ' . date_i18n( 'F j, Y @ h:i A', strtotime( $data['post_date'] ) );

		$data['post_title'] = $order_title;
	}
	return $data;
}

add_filter('wp_insert_post_data', 'carton_order_data');



/**
 * Grant downloadable file access to any newly added files on any existing
 * orders for this product that have previously been granted downloadable file access
 *
 * @access public
 * @param int $product_id product identifier
 * @param int $variation_id optional product variation identifier
 * @param array $file_paths newly set file paths
 */
function carton_process_product_file_download_paths( $product_id, $variation_id, $file_paths ) {
	global $wpdb;

	if ( $variation_id )
		$product_id = $variation_id;

	// determine whether any new files have been added
	$existing_file_paths = apply_filters( 'carton_file_download_paths', get_post_meta( $product_id, '_file_paths', true ), $product_id, null, null );
	if ( ! $existing_file_paths ) $existing_file_paths = array();
	$new_download_ids = array_diff( array_keys( $file_paths ), array_keys( $existing_file_paths ) );

	if ( $new_download_ids ) {
		// determine whether downloadable file access has been granted (either via the typical order completion, or via the admin ajax method)
		$existing_permissions = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$wpdb->prefix}carton_downloadable_product_permissions WHERE product_id = %d GROUP BY order_id", $product_id ) );
		foreach ( $existing_permissions as $existing_permission ) {
			$order = new CTN_Order( $existing_permission->order_id );

			if ( $order->id ) {
				foreach ( $new_download_ids as $new_download_id ) {
					// grant permission if it doesn't already exist
					if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT true FROM {$wpdb->prefix}carton_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $order->id, $product_id, $new_download_id ) ) ) {
						carton_downloadable_file_permission( $new_download_id, $product_id, $order );
					}
				}
			}
		}
	}
}

add_action( 'carton_process_product_file_download_paths', 'carton_process_product_file_download_paths', 10, 3 );

/**
 * Stores error messages in a variable so they can be displayed on the edit post screen after saving.
 *
 * @access public
 * @return void
 */
function carton_meta_boxes_save_errors() {
	global $carton_errors;

	update_option( 'carton_errors', $carton_errors );
}

add_action( 'admin_footer', 'carton_meta_boxes_save_errors' );


/**
 * Show any stored error messages.
 *
 * @access public
 * @return void
 */
function carton_meta_boxes_show_errors() {
	global $carton_errors;

	$carton_errors = maybe_unserialize( get_option( 'carton_errors' ) );

    if ( ! empty( $carton_errors ) ) {

    	echo '<div id="carton_errors" class="error fade">';
    	foreach ( $carton_errors as $error )
    		echo '<p>' . esc_html( $error ) . '</p>';
    	echo '</div>';

    	// Clear
    	update_option( 'carton_errors', '' );
    	$carton_errors = array();
    }
}

add_action( 'admin_notices', 'carton_meta_boxes_show_errors' );


/**
 * Output a text input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function carton_wp_text_input( $field ) {
	global $thepostid, $post, $carton;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder'] 	= isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name'] 			= isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type'] 			= isset( $field['type'] ) ? $field['type'] : 'text';

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) )
		foreach ( $field['custom_attributes'] as $attribute => $value )
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . $carton->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}
	echo '</p>';
}


/**
 * Output a hidden input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function carton_wp_hidden_input( $field ) {
	global $thepostid, $post;

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) .  '" /> ';
}


/**
 * Output a textarea input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function carton_wp_textarea_input( $field ) {
	global $thepostid, $post, $carton;

	$thepostid 			= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder'] 		= isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'short';
	$field['wrapper_class'] 	= isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['rows'] 			= isset( $field['rows'] ) ? $field['rows'] : '2';
	$field['readonly']		= isset( $field['readonly'] ) ? ' readonly="readonly"' : '';


	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><textarea class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '"' . $field['readonly'] . ' rows="' . esc_attr( $field['rows'] ) . '" cols="20">' . esc_textarea( $field['value'] ) . '</textarea> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . $carton->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}
	echo '</p>';
}


/**
 * Output a checkbox input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function carton_wp_checkbox( $field ) {
	global $thepostid, $post;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'checkbox';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['cbvalue'] 		= isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="checkbox" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . ' /> ';

	if ( ! empty( $field['description'] ) ) echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';

	echo '</p>';
}


/**
 * Output a select input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function carton_wp_select( $field ) {
	global $thepostid, $post, $carton;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '">';

	foreach ( $field['options'] as $key => $value ) {

		echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';

	}

	echo '</select> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . $carton->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}
	echo '</p>';
}

/**
 * Output a radio input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function carton_wp_radio( $field ) {
	global $thepostid, $post, $carton;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );

	echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend><ul>';

	if ( ! empty( $field['description'] ) ) {
		echo '<li class="description">' . wp_kses_post( $field['description'] ) . '</li>';
	}

    foreach ( $field['options'] as $key => $value ) {

		echo '<li><label><input
        		name="' . esc_attr( $field['id'] ) . '"
        		value="' . esc_attr( $key ) . '"
        		type="radio"
        		class="' . esc_attr( $field['class'] ) . '"
        		' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
        		/> ' . esc_html( $value ) . '</label>
    	</li>';
	}
    echo '</ul></fieldset>';
}
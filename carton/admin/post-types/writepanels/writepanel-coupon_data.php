<?php
/**
 * Coupon Data
 *
 * Functions for displaying the coupon data meta box.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/WritePanels
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Displays the coupon data meta box.
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function carton_coupon_data_meta_box( $post ) {
	global $carton;

	wp_nonce_field( 'carton_save_data', 'carton_meta_nonce' );

	?>
	<style type="text/css">
		#edit-slug-box, #minor-publishing-actions { display:none }
	</style>
	<div id="coupon_options" class="panel carton_options_panel">
		<?php

			echo '<div class="options_group">';

			// Description
			carton_wp_text_input( array( 'id' => 'coupon_description', 'label' => __( 'Coupon description', 'carton' ), 'description' => __( 'Optionally enter a description for this coupon for your reference.', 'carton' ), 'value' => $post->post_excerpt, 'name' => 'excerpt' ) );

			echo '</div><div class="options_group">';

			// Type
    		carton_wp_select( array( 'id' => 'discount_type', 'label' => __( 'Discount type', 'carton' ), 'options' => $carton->get_coupon_discount_types() ) );

			// Amount
			carton_wp_text_input( array( 'id' => 'coupon_amount', 'label' => __( 'Coupon amount', 'carton' ), 'placeholder' => '0.00', 'description' => __( 'Value of the coupon.', 'carton' ), 'type' => 'number', 'custom_attributes' => array(
					'step' 	=> 'any',
					'min'	=> '0'
				)  ) );

			// Free Shipping
			carton_wp_checkbox( array( 'id' => 'free_shipping', 'label' => __( 'Enable free shipping', 'carton' ), 'description' => sprintf(__( 'Check this box if the coupon grants free shipping. The <a href="%s">free shipping method</a> must be enabled with the "must use coupon" setting checked.', 'carton' ), admin_url('admin.php?page=carton_settings&tab=shipping&section=CTN_Shipping_Free_Shipping')) ) );

			// Individual use
			carton_wp_checkbox( array( 'id' => 'individual_use', 'label' => __( 'Individual use', 'carton' ), 'description' => __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'carton' ) ) );

			// Apply before tax
			carton_wp_checkbox( array( 'id' => 'apply_before_tax', 'label' => __( 'Apply before tax', 'carton' ), 'description' => __( 'Check this box if the coupon should be applied before calculating cart tax.', 'carton' ) ) );

			// Exclude Sale Products
			carton_wp_checkbox( array( 'id' => 'exclude_sale_items', 'label' => __( 'Exclude sale items', 'carton' ), 'description' => __( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'carton' ) ) );

			echo '</div><div class="options_group">';

			// minimum spend
			carton_wp_text_input( array( 'id' => 'minimum_amount', 'label' => __( 'Minimum amount', 'carton' ), 'placeholder' => __( 'No minimum', 'carton' ), 'description' => __( 'This field allows you to set the minimum subtotal needed to use the coupon.', 'carton' ), 'type' => 'number', 'custom_attributes' => array(
					'step' 	=> 'any',
					'min'	=> '0'
				) ) );

			echo '</div><div class="options_group">';

			// Product ids
			?>
			<p class="form-field"><label for="product_ids"><?php _e( 'Products', 'carton' ) ?></label>
			<select id="product_ids" name="product_ids[]" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'carton' ); ?>">
				<?php
					$product_ids = get_post_meta( $post->ID, 'product_ids', true );
					if ( $product_ids ) {
						$product_ids = array_map( 'absint', explode( ',', $product_ids ) );
						foreach ( $product_ids as $product_id ) {

							$product      = get_product( $product_id );
							$product_name = carton_get_formatted_product_name( $product );

							echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . wp_kses_post( $product_name ) . '</option>';
						}
					}
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Products which need to be in the cart to use this coupon or, for "Product Discounts", which products are discounted.', 'carton' ) ?>' src="<?php echo $carton->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php

			// Exclude Product ids
			?>
			<p class="form-field"><label for="exclude_product_ids"><?php _e( 'Exclude products', 'carton' ) ?></label>
			<select id="exclude_product_ids" name="exclude_product_ids[]" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product…', 'carton' ); ?>">
				<?php
					$product_ids = get_post_meta( $post->ID, 'exclude_product_ids', true );
					if ( $product_ids ) {
						$product_ids = array_map( 'absint', explode( ',', $product_ids ) );
						foreach ( $product_ids as $product_id ) {

							$product      = get_product( $product_id );
							$product_name = carton_get_formatted_product_name( $product );

							echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( $product_name ) . '</option>';
						}
					}
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Products which must not be in the cart to use this coupon or, for "Product Discounts", which products are not discounted.', 'carton' ) ?>' src="<?php echo $carton->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php

			echo '</div><div class="options_group">';

			// Categories
			?>
			<p class="form-field"><label for="product_ids"><?php _e( 'Product categories', 'carton' ) ?></label>
			<select id="product_categories" name="product_categories[]" class="chosen_select" multiple="multiple" data-placeholder="<?php _e( 'Any category', 'carton' ); ?>">
				<?php
					$category_ids = (array) get_post_meta( $post->ID, 'product_categories', true );

					$categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
					if ( $categories ) foreach ( $categories as $cat )
						echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'A product must be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will be discounted.', 'carton' ) ?>' src="<?php echo $carton->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php

			// Exclude Categories
			?>
			<p class="form-field"><label for="exclude_product_categories"><?php _e( 'Exclude categories', 'carton' ) ?></label>
			<select id="exclude_product_categories" name="exclude_product_categories[]" class="chosen_select" multiple="multiple" data-placeholder="<?php _e( 'No categories', 'carton' ); ?>">
				<?php
					$category_ids = (array) get_post_meta( $post->ID, 'exclude_product_categories', true );

					$categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
					if ( $categories ) foreach ( $categories as $cat )
						echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Product must not be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will not be discounted.', 'carton' ) ?>' src="<?php echo $carton->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php

			echo '</div><div class="options_group">';

			// Customers
			carton_wp_text_input( array( 'id' => 'customer_email', 'label' => __( 'Customer emails', 'carton' ), 'placeholder' => __( 'Any customer', 'carton' ), 'description' => __( 'Comma separate email addresses to restrict this coupon to specific billing and user emails.', 'carton' ), 'value' => implode(', ', (array) get_post_meta( $post->ID, 'customer_email', true ) ), 'type' => 'email', 'custom_attributes' => array(
					'multiple' 	=> 'multiple'
				) ) );

			echo '</div><div class="options_group">';

			// Usage limit
			carton_wp_text_input( array( 'id' => 'usage_limit', 'label' => __( 'Usage limit', 'carton' ), 'placeholder' => _x('Unlimited usage', 'placeholder', 'carton'), 'description' => __( 'How many times this coupon can be used before it is void.', 'carton' ), 'type' => 'number', 'custom_attributes' => array(
					'step' 	=> '1',
					'min'	=> '0'
				) ) );

			// Expiry date
			carton_wp_text_input( array( 'id' => 'expiry_date', 'label' => __( 'Expiry date', 'carton' ), 'placeholder' => _x('Never expire', 'placeholder', 'carton'), 'description' => __( 'The date this coupon will expire, <code>YYYY-MM-DD</code>.', 'carton' ), 'class' => 'short date-picker', 'custom_attributes' => array( 'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" ) ) );

			echo '</div>';

			do_action( 'carton_coupon_options' );
		?>
	</div>
	<?php
}


/**
 * Save the coupon data meta box.
 *
 * @access public
 * @param mixed $post_id
 * @param mixed $post
 * @return void
 */
function carton_process_shop_coupon_meta( $post_id, $post ) {
	global $wpdb, $carton_errors;

	// Ensure coupon code is correctly formatted
	$post->post_title = apply_filters( 'carton_coupon_code', $post->post_title );
	$wpdb->update( $wpdb->posts, array( 'post_title' => $post->post_title ), array( 'ID' => $post_id ) );

	// Check for dupe coupons
	$coupon_found = $wpdb->get_var( $wpdb->prepare( "
		SELECT $wpdb->posts.\"ID\"
	    FROM $wpdb->posts
	    WHERE $wpdb->posts.post_type = 'shop_coupon'
	    AND $wpdb->posts.post_status = 'publish'
	    AND $wpdb->posts.post_title = '%s'
	    AND $wpdb->posts.\"ID\" != %s
	 ", $post->post_title, $post_id ) );

	if ( $coupon_found )
		$carton_errors[] = __( 'Coupon code already exists - customers will use the latest coupon with this code.', 'carton' );

	// Add/Replace data to array
	$type 				= carton_clean( $_POST['discount_type'] );
	$amount 			= carton_clean( $_POST['coupon_amount'] );
	$usage_limit 		= empty( $_POST['usage_limit'] ) ? '' : absint( $_POST['usage_limit'] );
	$individual_use 	= isset( $_POST['individual_use'] ) ? 'yes' : 'no';
	$expiry_date 		= carton_clean( $_POST['expiry_date'] );
	$apply_before_tax 	= isset( $_POST['apply_before_tax'] ) ? 'yes' : 'no';
	$free_shipping 		= isset( $_POST['free_shipping'] ) ? 'yes' : 'no';
	$exclude_sale_items	= isset( $_POST['exclude_sale_items'] ) ? 'yes' : 'no';
	$minimum_amount 	= carton_clean( $_POST['minimum_amount'] );
	$customer_email 	= array_filter( array_map( 'trim', explode( ',', carton_clean( $_POST['customer_email'] ) ) ) );

	if ( isset( $_POST['product_ids'] ) ) {
		$product_ids 			= implode( ',', array_filter( array_map( 'intval', (array) $_POST['product_ids'] ) ) );
	} else {
		$product_ids = '';
	}

	if ( isset( $_POST['exclude_product_ids'] ) ) {
		$exclude_product_ids 	= implode( ',', array_filter( array_map( 'intval', (array) $_POST['exclude_product_ids'] ) ) );
	} else {
		$exclude_product_ids = '';
	}

	$product_categories 		= isset( $_POST['product_categories'] ) ? array_map( 'intval', $_POST['product_categories'] ) : array();
	$exclude_product_categories = isset( $_POST['exclude_product_categories'] ) ? array_map( 'intval', $_POST['exclude_product_categories'] ) : array();

	// Save
	update_post_meta( $post_id, 'discount_type', $type );
	update_post_meta( $post_id, 'coupon_amount', $amount );
	update_post_meta( $post_id, 'individual_use', $individual_use );
	update_post_meta( $post_id, 'product_ids', $product_ids );
	update_post_meta( $post_id, 'exclude_product_ids', $exclude_product_ids );
	update_post_meta( $post_id, 'usage_limit', $usage_limit );
	update_post_meta( $post_id, 'expiry_date', $expiry_date );
	update_post_meta( $post_id, 'apply_before_tax', $apply_before_tax );
	update_post_meta( $post_id, 'free_shipping', $free_shipping );
	update_post_meta( $post_id, 'exclude_sale_items', $exclude_sale_items );
	update_post_meta( $post_id, 'product_categories', $product_categories );
	update_post_meta( $post_id, 'exclude_product_categories', $exclude_product_categories );
	update_post_meta( $post_id, 'minimum_amount', $minimum_amount );
	update_post_meta( $post_id, 'customer_email', $customer_email );

	do_action( 'carton_coupon_options' );
}

add_action( 'carton_process_shop_coupon_meta', 'carton_process_shop_coupon_meta', 1, 2 );
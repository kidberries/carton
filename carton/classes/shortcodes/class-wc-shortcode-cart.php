<?php
/**
 * Cart Shortcode
 *
 * Used on the cart page, the cart shortcode displays the cart contents and interface for coupon codes and other cart bits and pieces.
 *
 * @author 		CartonThemes
 * @category 	Shortcodes
 * @package 	CartoN/Shortcodes/Cart
 * @version     2.0.0
 */
class CTN_Shortcode_Cart {

	/**
	 * Output the cart shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $atts ) {
		global $carton;

		$carton->nocache();

		if ( ! defined( 'CARTON_CART' ) ) define( 'CARTON_CART', true );

		// Add Discount
		if ( ! empty( $_POST['apply_coupon'] ) ) {

			if ( ! empty( $_POST['coupon_code'] ) ) {
				$carton->cart->add_discount( sanitize_text_field( $_POST['coupon_code'] ) );
			} else {
				$carton->add_error( CTN_Coupon::get_generic_coupon_error( CTN_Coupon::E_CTN_COUPON_PLEASE_ENTER ) );
			}

		// Remove Coupon Codes
		} elseif ( isset( $_GET['remove_discounts'] ) ) {

			$carton->cart->remove_coupons( $_GET['remove_discounts'] );

		// Update Shipping
		} elseif ( ! empty( $_POST['calc_shipping'] ) && $carton->verify_nonce('cart') ) {

			$validation = $carton->validation();

			$carton->shipping->reset_shipping();
			$carton->customer->calculated_shipping( true );
			$country 	= $_POST['calc_shipping_country'];
			$state 		= $_POST['calc_shipping_state'];
			$postcode 	= $_POST['calc_shipping_postcode'];

			if ( $postcode && ! $validation->is_postcode( $postcode, $country ) ) {
				$carton->add_error( __( 'Please enter a valid postcode/ZIP.', 'carton' ) );
				$postcode = '';
			} elseif ( $postcode ) {
				$postcode = $validation->format_postcode( $postcode, $country );
			}

			if ( $country ) {

				// Update customer location
				$carton->customer->set_location( $country, $state, $postcode );
				$carton->customer->set_shipping_location( $country, $state, $postcode );
				$carton->add_message(  __( 'Shipping costs updated.', 'carton' ) );

			} else {

				$carton->customer->set_to_base();
				$carton->customer->set_shipping_to_base();
				$carton->add_message(  __( 'Shipping costs updated.', 'carton' ) );

			}

			do_action( 'carton_calculated_shipping' );
		}

		// Check cart items are valid
		do_action('carton_check_cart_items');

		// Calc totals
		$carton->cart->calculate_totals();

		if ( sizeof( $carton->cart->get_cart() ) == 0 )
			carton_get_template( 'cart/cart-empty.php' );
		else
			carton_get_template( 'cart/cart.php' );

	}
}
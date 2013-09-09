<?php
/**
 * Checkout Shortcode
 *
 * Used on the checkout page, the checkout shortcode displays the checkout process.
 *
 * @author 		CartonThemes
 * @category 	Shortcodes
 * @package 	CartoN/Shortcodes/Checkout
 * @version     2.0.0
 */

class CTN_Shortcode_Checkout {

	/**
	 * Get the shortcode content.
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		global $carton;
		return $carton->shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $atts ) {
		global $carton;

		// Prevent cache
		$carton->nocache();

		// Show non-cart errors
		$carton->show_messages();

		// Check cart has contents
		if ( sizeof( $carton->cart->get_cart() ) == 0 ) return;

		// Calc totals
		$carton->cart->calculate_totals();

		// Check cart contents for errors
		do_action('carton_check_cart_items');

		// Get checkout object
		$checkout = $carton->checkout();

		if ( empty( $_POST ) && $carton->error_count() > 0 ) {

			carton_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );

		} else {

			$non_js_checkout = ! empty( $_POST['carton_checkout_update_totals'] ) ? true : false;

			if ( $carton->error_count() == 0 && $non_js_checkout )
				$carton->add_message( __( 'The order totals have been updated. Please confirm your order by pressing the Place Order button at the bottom of the page.', 'carton' ) );

			carton_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );

		}
	}
}
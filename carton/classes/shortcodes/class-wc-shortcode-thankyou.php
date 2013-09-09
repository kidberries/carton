<?php
/**
 * Thankyou Shortcode
 *
 * The thankyou page displays after successful checkout and can be hooked into by payment gateways.
 *
 * @author 		CartonThemes
 * @category 	Shortcodes
 * @package 	CartoN/Shortcodes/Thankyou
 * @version     2.0.0
 */

class CTN_Shortcode_Thankyou {

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

		$carton->nocache();
		$carton->show_messages();

		$order = false;

		// Get the order
		$order_id  = apply_filters( 'carton_thankyou_order_id', empty( $_GET['order'] ) ? 0 : absint( $_GET['order'] ) );
		$order_key = apply_filters( 'carton_thankyou_order_key', empty( $_GET['key'] ) ? '' : carton_clean( $_GET['key'] ) );

		if ( $order_id > 0 ) {
			$order = new CTN_Order( $order_id );
			if ( $order->order_key != $order_key )
				unset( $order );
		}

		// Empty awaiting payment session
		unset( $carton->session->order_awaiting_payment );

		carton_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	}
}
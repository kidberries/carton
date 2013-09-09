<?php
/**
 * Order Tracking Shortcode
 *
 * Lets a user see the status of an order by entering their order details.
 *
 * @author 		CartonThemes
 * @category 	Shortcodes
 * @package 	CartoN/Shortcodes/Order_Tracking
 * @version     2.0.0
 */

class CTN_Shortcode_Order_Tracking {

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

		extract(shortcode_atts(array(
		), $atts));

		global $post;

		if ( ! empty( $_REQUEST['orderid'] ) ) {

			$carton->verify_nonce( 'order_tracking' );

			$order_id 		= empty( $_REQUEST['orderid'] ) ? 0 : esc_attr( $_REQUEST['orderid'] );
			$order_email	= empty( $_REQUEST['order_email'] ) ? '' : esc_attr( $_REQUEST['order_email']) ;

			if ( ! $order_id ) {

				echo '<p class="carton-error">' . __( 'Please enter a valid order ID', 'carton' ) . '</p>';

			} elseif ( ! $order_email ) {

				echo '<p class="carton-error">' . __( 'Please enter a valid order email', 'carton' ) . '</p>';

			} else {

				$order = new CTN_Order( apply_filters( 'carton_shortcode_order_tracking_order_id', $order_id ) );

				if ( $order->id && $order_email ) {

					if ( strtolower( $order->billing_email ) == strtolower( $order_email ) ) {
						do_action( 'carton_track_order', $order->id );
						carton_get_template( 'order/tracking.php', array(
							'order' => $order
						) );

						return;
					}

				} else {

					echo '<p class="carton-error">' . sprintf( __( 'Sorry, we could not find that order id in our database.', 'carton' ), get_permalink($post->ID ) ) . '</p>';

				}

			}

		}

		carton_get_template( 'order/form-tracking.php' );
	}
}
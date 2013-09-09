<?php
/**
 * Checkout login form
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in()  || ! $checkout->enable_signup ) return;

$info_message = apply_filters( 'carton_checkout_login_message', __( 'Returning customer?', 'carton' ) );
?>

<p class="carton-info"><?php echo esc_html( $info_message ); ?> <a href="#" class="showlogin"><?php _e( 'Click here to login', 'carton' ); ?></a></p>

<?php
	carton_login_form(
		array(
			'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer please proceed to the Billing &amp; Shipping section.', 'carton' ),
			'redirect' => get_permalink( carton_get_page_id( 'checkout') ),
			'hidden'   => true
		)
	);
?>
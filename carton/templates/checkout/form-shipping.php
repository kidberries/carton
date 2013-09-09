<?php
/**
 * Checkout shipping information form
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;
?>

<?php if ( ( $carton->cart->needs_shipping() || get_option('carton_require_shipping_address') == 'yes' ) && ! $carton->cart->ship_to_billing_address_only() ) : ?>

	<div class="shipping_address" id="checkout_shipping_address">
		<h3><?php _e( 'Shipping Address', 'carton' ); ?></h3>
		<br clear="all" />

		<?php do_action('carton_before_checkout_shipping_form', $checkout); ?>

		<?php foreach ($checkout->checkout_fields['shipping'] as $key => $field) : ?>

			<?php carton_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

		<?php do_action('carton_after_checkout_shipping_form', $checkout); ?>

	</div>

<?php endif; ?>

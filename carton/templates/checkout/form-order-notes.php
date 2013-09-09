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

<?php do_action('carton_before_order_notes', $checkout); ?>

<?php if (get_option('carton_enable_order_comments')!='no') : ?>
	<div id="checkout_order_notes">
	<?php if ($carton->cart->ship_to_billing_address_only()) : ?>

		<h3><?php _e( 'Additional Information', 'carton' ); ?></h3>

	<?php endif; ?>

	<?php foreach ($checkout->checkout_fields['order'] as $key => $field) : ?>

		<?php carton_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>
	</div>

<?php endif; ?>

<?php do_action('carton_after_order_notes', $checkout); ?>
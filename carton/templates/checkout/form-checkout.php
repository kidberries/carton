<?php
/**
 * Checkout Form
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

$carton->show_messages();

do_action( 'carton_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'carton_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'carton' ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'carton_get_checkout_url', $carton->cart->get_checkout_url() ); ?>

<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">

    <h3 id="order_review_heading"><?php _e( 'Your order', 'carton' ); ?></h3>
    <?php do_action( 'carton_checkout_order_review' ); ?>

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<div class="col1-set">
			<?php do_action( 'carton_checkout_order_notes' ); ?>
		</div>

		<?php do_action( 'carton_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">

			<div class="col-1">

				<?php do_action( 'carton_checkout_billing' ); ?>

			</div>

			<div class="col-2">

				<?php do_action( 'carton_checkout_shipping' ); ?>

			</div>

		</div>

		<?php do_action( 'carton_checkout_after_customer_details' ); ?>

	<?php endif; ?>

    <div style="text-align: center;"><input type="submit" class="button alt" name="carton_checkout_place_order" id="place_order" value="<?php echo apply_filters('carton_order_button_text', __( 'Place order', 'carton' )); ?>" /></div>

</form>

<?php do_action( 'carton_after_checkout_form', $checkout ); ?>
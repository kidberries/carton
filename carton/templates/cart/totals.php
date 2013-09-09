<?php
/**
 * Cart totals
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

$available_methods = $carton->shipping->get_available_shipping_methods();
?>
<div class="cart_totals <?php if ( $carton->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>">

	<?php do_action( 'carton_before_cart_totals' ); ?>

	<?php if ( ! $carton->shipping->enabled || $available_methods || ! $carton->customer->get_shipping_country() || ! $carton->customer->has_calculated_shipping() ) : ?>

		<h2><?php _e( 'Cart Totals', 'carton' ); ?></h2>

		<table cellspacing="0">
			<tbody>

				<tr class="cart-subtotal">
					<th><strong><?php _e( 'Cart Subtotal', 'carton' ); ?></strong></th>
					<td><strong><?php echo $carton->cart->get_cart_subtotal(); ?></strong></td>
				</tr>

				<?php if ( $carton->cart->get_discounts_before_tax() ) : ?>

					<tr class="discount">
						<th><?php _e( 'Cart Discount', 'carton' ); ?> <a href="<?php echo add_query_arg( 'remove_discounts', '1', $carton->cart->get_cart_url() ) ?>"><?php _e( '[Remove]', 'carton' ); ?></a></th>
						<td>-<?php echo $carton->cart->get_discounts_before_tax(); ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $carton->cart->needs_shipping() && $carton->cart->show_shipping() && ( $available_methods || get_option( 'carton_enable_shipping_calc' ) == 'yes' ) ) : ?>

					<?php do_action( 'carton_cart_totals_before_shipping' ); ?>

					<tr class="shipping">
						<th><?php _e( 'Shipping', 'carton' ); ?></th>
						<td><?php carton_get_template( 'cart/shipping-methods.php', array( 'available_methods' => $available_methods ) ); ?></td>
					</tr>

					<?php do_action( 'carton_cart_totals_after_shipping' ); ?>

				<?php endif ?>

				<?php foreach ( $carton->cart->get_fees() as $fee ) : ?>

					<tr class="fee fee-<?php echo $fee->id ?>">
						<th><?php echo $fee->name ?></th>
						<td><?php
							if ( $carton->cart->tax_display_cart == 'excl' )
								echo carton_price( $fee->amount );
							else
								echo carton_price( $fee->amount + $fee->tax );
						?></td>
					</tr>

				<?php endforeach; ?>

				<?php
					// Show the tax row if showing prices exclusive of tax only
					if ( $carton->cart->tax_display_cart == 'excl' ) {
						$taxes = $carton->cart->get_formatted_taxes();

						if ( sizeof( $taxes ) > 0 ) {

							$has_compound_tax = false;

							foreach ( $taxes as $key => $tax ) {
								if ( $carton->cart->tax->is_compound( $key ) ) {
									$has_compound_tax = true;
									continue;
								}

								echo '<tr class="tax-rate tax-rate-' . $key . '">
									<th>' . $carton->cart->tax->get_rate_label( $key ) . '</th>
									<td>' . $tax . '</td>
								</tr>';
							}

							if ( $has_compound_tax ) {

								echo '<tr class="order-subtotal">
									<th><strong>' . __( 'Subtotal', 'carton' ) . '</strong></th>
									<td><strong>' . $carton->cart->get_cart_subtotal( true ) . '</strong></td>
								</tr>';
							}

							foreach ( $taxes as $key => $tax ) {
								if ( ! $carton->cart->tax->is_compound( $key ) )
									continue;

								echo '<tr class="tax-rate tax-rate-' . $key . '">
									<th>' . $carton->cart->tax->get_rate_label( $key ) . '</th>
									<td>' . $tax . '</td>
								</tr>';
							}

						} elseif ( $carton->cart->get_cart_tax() > 0 ) {

							echo '<tr class="tax">
								<th>' . __( 'Tax', 'carton' ) . '</th>
								<td>' . $carton->cart->get_cart_tax() . '</td>
							</tr>';
						}
					}
				?>

				<?php if ( $carton->cart->get_discounts_after_tax() ) : ?>

					<tr class="discount">
						<th><?php _e( 'Order Discount', 'carton' ); ?> <a href="<?php echo add_query_arg( 'remove_discounts', '2', $carton->cart->get_cart_url() ) ?>"><?php _e( '[Remove]', 'carton' ); ?></a></th>
						<td>-<?php echo $carton->cart->get_discounts_after_tax(); ?></td>
					</tr>

				<?php endif; ?>

				<?php do_action( 'carton_cart_totals_before_order_total' ); ?>

				<?php foreach ( $carton->cart->discount_totals as $discount_name => $discount_value ) : ?>
					<tr class="cart-discount-total">
						<th><strong><?php  if($carton->cart->discount_totals[ $discount_name ]>0) { _e( 'Total in Discount Action:', 'carton'); } ?> <?php echo $discount_name; ?></strong></th>
						<td><strong<?php if($carton->cart->discount_totals[ $discount_name ]>0) {echo ' class="negative"';} ?>><?php if($carton->cart->discount_totals[ $discount_name ]>0) {echo "-";} ?><?php echo carton_price( $carton->cart->discount_totals[ $discount_name ] ); ?></strong></td>
					</tr>
				<?php endforeach; ?>

				<tr class="total">
					<th><strong><?php _e( 'Order Total', 'carton' ); ?></strong></th>
					<td>
						<strong><?php echo $carton->cart->get_total(); ?></strong>
						<?php
							// If prices are tax inclusive, show taxes here
							if (  $carton->cart->tax_display_cart == 'incl' ) {
								$tax_string_array = array();
								$taxes = $carton->cart->get_formatted_taxes();

								if ( sizeof( $taxes ) > 0 )
									foreach ( $taxes as $key => $tax )
										$tax_string_array[] = sprintf( '%s %s', $tax, $carton->cart->tax->get_rate_label( $key ) );
								elseif ( $carton->cart->get_cart_tax() )
									$tax_string_array[] = sprintf( '%s tax', $tax );

								if ( ! empty( $tax_string_array ) ) {
									echo '<small class="includes_tax">' . sprintf( __( '(Includes %s)', 'carton' ), implode( ', ', $tax_string_array ) ) . '</small>';
								}
							}
						?>
					</td>
				</tr>

				<?php do_action( 'carton_cart_totals_after_order_total' ); ?>

			</tbody>
		</table>

		<?php if ( $carton->cart->get_cart_tax() ) : ?>

			<p><small><?php

				$estimated_text = ( $carton->customer->is_customer_outside_base() && ! $carton->customer->has_calculated_shipping() ) ? sprintf( ' ' . __( ' (taxes estimated for %s)', 'carton' ), $carton->countries->estimated_for_prefix() . __( $carton->countries->countries[ $carton->countries->get_base_country() ], 'carton' ) ) : '';

				printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'carton' ), $estimated_text );

			?></small></p>

		<?php endif; ?>

	<?php elseif( $carton->cart->needs_shipping() ) : ?>

		<?php if ( ! $carton->customer->get_shipping_state() || ! $carton->customer->get_shipping_postcode() ) : ?>

			<div class="carton-info">

				<p><?php _e( 'No shipping methods were found; please recalculate your shipping and enter your state/county and zip/postcode to ensure there are no other available methods for your location.', 'carton' ); ?></p>

			</div>

		<?php else : ?>

			<?php

				$customer_location = $carton->countries->countries[ $carton->customer->get_shipping_country() ];

				echo apply_filters( 'carton_cart_no_shipping_available_html',
					'<div class="carton-error"><p>' .
					sprintf( __( 'Sorry, it seems that there are no available shipping methods for your location (%s).', 'carton' ) . ' ' . __( 'If you require assistance or wish to make alternate arrangements please contact us.', 'carton' ), $customer_location ) .
					'</p></div>'
				);

			?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'carton_after_cart_totals' ); ?>

</div>
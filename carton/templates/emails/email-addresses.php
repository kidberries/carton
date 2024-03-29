<?php
/**
 * Email Addresses
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?><table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">

	<tr>

		<?php if ( get_option( 'carton_ship_to_billing_address_only' ) == 'no' && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>

		<td valign="top" width="50%">

			<h3><?php _e( 'Shipping address', 'carton' ); ?></h3>

			<p><?php echo $shipping; ?></p>

		</td>

		<?php endif; ?>

		<td valign="top" width="50%">

			<h3><?php _e( 'Billing address', 'carton' ); ?></h3>

			<p><?php echo $order->get_formatted_billing_address(); ?></p>

		</td>

	</tr>

</table>
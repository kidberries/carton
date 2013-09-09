<?php
/**
 * Customer note email
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('carton_email_header', $email_heading); ?>

<p><?php _e("Hello, a note has just been added to your order:", 'carton'); ?></p>

<blockquote><?php echo wpautop(wptexturize( $customer_note )) ?></blockquote>

<p><?php _e("For your reference, your order details are shown below.", 'carton'); ?></p>

<?php do_action('carton_email_before_order_table', $order, false); ?>

<h2><?php echo __( 'Order:', 'carton' ) . ' ' . $order->get_order_number(); ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'carton' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'carton' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'carton' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo $order->email_order_items_table( $order->is_download_permitted(), true ); ?>
	</tbody>
	<tfoot>
		<?php carton_get_template('emails/email-order-totals.php', array( 'order' => $order )); ?>
	</tfoot>
</table>

<?php do_action('carton_email_after_order_table', $order, false); ?>

<?php do_action( 'carton_email_order_meta', $order, false ); ?>

<?php carton_get_template('emails/email-pickup-instruction.php', array( 'order' => $order )); ?>

<?php carton_get_template('emails/email-customer-details.php', array( 'order' => $order )); ?>

<?php carton_get_template('emails/email-addresses.php', array( 'order' => $order )); ?>

<?php do_action('carton_email_footer'); ?>

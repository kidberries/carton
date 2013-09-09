<?php
/**
 * Customer completed order email
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('carton_email_header', $email_heading); ?>

<p><?php printf( __( "Hi there. Your recent order on %s has been completed. Your order details are shown below for your reference:", 'carton' ), get_option( 'blogname' ) ); ?></p>

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
		<?php echo $order->email_order_items_table( true, false, true ); ?>
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

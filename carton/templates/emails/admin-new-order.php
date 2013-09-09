<?php
/**
 * Admin new order email
 *
 * @author CartonThemes
 * @package CartoN/Templates/Emails/HTML
 * @version 2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'carton_email_header', $email_heading ); ?>

<p><?php printf( __( 'You have received an order from %s. Their order is as follows:', 'carton' ), $order->billing_first_name . ' ' . $order->billing_last_name ); ?></p>

<?php do_action( 'carton_email_before_order_table', $order, true ); ?>

<h2><?php printf( __( 'Order:', 'carton') . ' %s' , $order->get_order_number() ); ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( carton_date_format(), strtotime( $order->order_date ) ) ); ?>)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'carton' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'carton' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'carton' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo $order->email_order_items_table( false, true ); ?>
	</tbody>
	<tfoot>
		<?php carton_get_template('emails/email-order-totals.php', array( 'order' => $order )); ?>
	</tfoot>
</table>

<?php do_action('carton_email_after_order_table', $order, true); ?>

<?php do_action( 'carton_email_order_meta', $order, true ); ?>

<?php carton_get_template('emails/email-pickup-instruction.php', array( 'order' => $order )); ?>

<?php carton_get_template('emails/email-customer-details.php', array( 'order' => $order )); ?>

<?php carton_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>

<?php do_action( 'carton_email_footer' ); ?>

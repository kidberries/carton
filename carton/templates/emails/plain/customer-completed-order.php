<?php
/**
 * Customer completed order email (plain text)
 *
 * @author		CartonThemes
 * @package		CartoN/Templates/Emails/Plain
 * @version		2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo $email_heading . "\n\n";

echo sprintf( __( "Hi there. Your recent order on %s has been completed. Your order details are shown below for your reference:", 'carton' ), get_option( 'blogname' ) ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'carton_email_before_order_table', $order, false );

echo sprintf( __( 'Order number: %s', 'carton'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Order date: %s', 'carton'), date_i18n( carton_date_format(), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'carton_email_order_meta', $order, false, true );

echo "\n" . $order->email_order_items_table( true, false, true, '', '', true );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'carton_email_after_order_table', $order, false, true );

echo __( 'Your details', 'carton' ) . "\n\n";

if ( $order->billing_email )
	echo __( 'Email:', 'carton' ); echo $order->billing_email. "\n";

if ( $order->billing_phone )
	echo __( 'Tel:', 'carton' ); ?> <?php echo $order->billing_phone. "\n";

carton_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );

echo "\n****************************************************\n\n";

echo apply_filters( 'carton_email_footer_text', get_option( 'carton_email_footer_text' ) );
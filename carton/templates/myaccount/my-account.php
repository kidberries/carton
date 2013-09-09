<?php
/**
 * My Account page
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

$carton->show_messages(); ?>

<p class="myaccount_user">
	<?php
	printf(
		__( 'Hello, <strong>%s</strong>. From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">change your password</a>.', 'carton' ),
		$current_user->display_name,
		get_permalink( carton_get_page_id( 'change_password' ) )
	);
	?>
</p>

<?php do_action( 'carton_before_my_account' ); ?>

<?php carton_get_template( 'myaccount/my-downloads.php' ); ?>

<?php carton_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>

<?php carton_get_template( 'myaccount/my-address.php' ); ?>

<?php do_action( 'carton_after_my_account' ); ?>
<?php
/**
 * Edit address form
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton, $current_user;

get_currentuserinfo();
?>

<?php $carton->show_messages(); ?>

<?php if (!$load_address) : ?>

	<?php carton_get_template('myaccount/my-address.php'); ?>

<?php else : ?>

	<form action="<?php echo esc_url( add_query_arg( 'address', $load_address, get_permalink( carton_get_page_id('edit_address') ) ) ); ?>" method="post">

		<h3><?php if ($load_address=='billing') _e( 'Billing Address', 'carton' ); else _e( 'Shipping Address', 'carton' ); ?></h3>

		<?php
		foreach ($address as $key => $field) :
			$value = (isset($_POST[$key])) ? $_POST[$key] : get_user_meta( get_current_user_id(), $key, true );

			// Default values
			if (!$value && ($key=='billing_email' || $key=='shipping_email')) $value = $current_user->user_email;
			if (!$value && ($key=='billing_country' || $key=='shipping_country')) $value = $carton->countries->get_base_country();
			if (!$value && ($key=='billing_state' || $key=='shipping_state')) $value = $carton->countries->get_base_state();

			carton_form_field( $key, $field, $value );
		endforeach;
		?>

		<p>
			<input type="submit" class="button" name="save_address" value="<?php _e( 'Save Address', 'carton' ); ?>" />
			<?php $carton->nonce_field('edit_address') ?>
			<input type="hidden" name="action" value="edit_address" />
		</p>

	</form>

<?php endif; ?>
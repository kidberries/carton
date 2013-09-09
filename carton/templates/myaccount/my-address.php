<?php
/**
 * My Addresses
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

$customer_id = get_current_user_id();

if ( get_option('carton_ship_to_billing_address_only') == 'no' ) {
	$page_title = apply_filters( 'carton_my_account_my_address_title', __( 'My Addresses', 'carton' ) );
	$get_addresses    = array(
		'billing' => __( 'Billing Address', 'carton' ),
		'shipping' => __( 'Shipping Address', 'carton' )
	);
} else {
	$page_title = apply_filters( 'carton_my_account_my_address_title', __( 'My Address', 'carton' ) );
	$get_addresses    = array(
		'billing' =>  __( 'Billing Address', 'carton' )
	);
}

$col = 1;
?>

<h2><?php echo $page_title; ?></h2>

<p class="myaccount_address">
	<?php echo apply_filters( 'carton_my_account_my_address_description', __( 'The following addresses will be used on the checkout page by default.', 'carton' ) ); ?>
</p>

<?php if ( get_option('carton_ship_to_billing_address_only') == 'no' ) echo '<div class="col2-set addresses">'; ?>

<?php foreach ( $get_addresses as $name => $title ) : ?>

	<div class="col-<?php echo ( ( $col = $col * -1 ) < 0 ) ? 1 : 2; ?> address">
		<header class="title">
			<h3><?php echo $title; ?></h3>
			<a href="<?php echo esc_url( add_query_arg('address', $name, get_permalink(carton_get_page_id( 'edit_address' ) ) ) ); ?>" class="edit"><?php _e( 'Edit', 'carton' ); ?></a>
		</header>
		<address>
			<?php
				$address = array(
					'first_name' 	=> get_user_meta( $customer_id, $name . '_first_name', true ),
					'last_name'		=> get_user_meta( $customer_id, $name . '_last_name', true ),
					'company'		=> get_user_meta( $customer_id, $name . '_company', true ),
					'address_1'		=> get_user_meta( $customer_id, $name . '_address_1', true ),
					'address_2'		=> get_user_meta( $customer_id, $name . '_address_2', true ),
					'city'			=> get_user_meta( $customer_id, $name . '_city', true ),
					'state'			=> get_user_meta( $customer_id, $name . '_state', true ),
					'postcode'		=> get_user_meta( $customer_id, $name . '_postcode', true ),
					'country'		=> get_user_meta( $customer_id, $name . '_country', true )
				);

				$formatted_address = $carton->countries->get_formatted_address( $address );

				if ( ! $formatted_address )
					_e( 'You have not set up this type of address yet.', 'carton' );
				else
					echo $formatted_address;
			?>
		</address>
	</div>

<?php endforeach; ?>

<?php if ( get_option('carton_ship_to_billing_address_only') == 'no' ) echo '</div>'; ?>
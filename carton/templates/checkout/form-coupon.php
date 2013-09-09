<?php
/**
 * Checkout coupon form
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

if ( ! $carton->cart->coupons_enabled() )
	return;

$info_message = apply_filters('carton_checkout_coupon_message', __( 'Have a coupon?', 'carton' ));
?>

<p class="carton-info"><?php echo $info_message; ?> <a href="#" class="showcoupon"><?php _e( 'Click here to enter your code', 'carton' ); ?></a></p>

<form class="checkout_coupon" method="post" style="display:none">

	<p class="form-row form-row-first">
		<input type="text" name="coupon_code" class="input-text" placeholder="<?php _e( 'Coupon code', 'carton' ); ?>" id="coupon_code" value="" />
	</p>

	<p class="form-row form-row-last">
		<input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'carton' ); ?>" />
	</p>

	<div class="clear"></div>
</form>
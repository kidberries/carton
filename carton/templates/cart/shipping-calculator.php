<?php
/**
 * Shipping Calculator
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

if ( get_option('carton_enable_shipping_calc')=='no' || ! $carton->cart->needs_shipping() ) return;
?>

<?php do_action( 'carton_before_shipping_calculator' ); ?>

<form class="shipping_calculator" action="<?php echo esc_url( $carton->cart->get_cart_url() ); ?>" method="post">
	<h2><a href="#" class="shipping-calculator-button"><?php _e( 'Calculate Shipping', 'carton' ); ?> <span>&darr;</span></a></h2>
	<section class="shipping-calculator-form">
		<p class="form-row form-row-wide">
			<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state" rel="calc_shipping_state">
				<option value=""><?php _e( 'Select a country&hellip;', 'carton' ); ?></option>
				<?php
					foreach( $carton->countries->get_allowed_countries() as $key => $value )
						echo '<option value="' . $key . '"' . selected( $carton->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
				?>
			</select>
		</p>
		<p class="form-row form-row-wide">
			<?php
				$current_cc = $carton->customer->get_shipping_country();
				$current_r = $carton->customer->get_shipping_state();

				$states = $carton->countries->get_states( $current_cc );
				if ( is_array( $states ) && empty( $states ) ) {

					// Hidden
					?>
					<input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'carton' ); ?>" />
					<?php

				} elseif ( is_array( $states ) ) {

					// Dropdown
					?>
					<span>
						<select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'carton' ); ?>"><option value=""><?php _e( 'Select a state&hellip;', 'carton' ); ?></option><?php
							foreach ( $states as $ckey => $cvalue )
								echo '<option value="' . esc_attr( $ckey ) . '" '.selected( $current_r, $ckey, false ) .'>' . __( esc_html( $cvalue ), 'carton' ) .'</option>';
						?></select>
					</span>
					<?php

				} else {

					// Input
					?>
					<input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php _e( 'State / county', 'carton' ); ?>" name="calc_shipping_state" id="calc_shipping_state" />
					<?php

				}
			?>
		</p>
		<p class="form-row form-row-wide">
			<input type="text" class="input-text" value="<?php echo esc_attr( $carton->customer->get_shipping_postcode() ); ?>" placeholder="<?php _e( 'Postcode / Zip', 'carton' ); ?>" title="<?php _e( 'Postcode', 'carton' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
		</p>
		<div class="clear"></div>
		<p><button type="submit" name="calc_shipping" value="1" class="button"><?php _e( 'Update Totals', 'carton' ); ?></button></p>
		<?php $carton->nonce_field('cart') ?>
	</section>
</form>
<script type="text/javascript">
var toggle_shipping_postcode = function(){
	jQuery("#calc_shipping_state").parent().hide();
	if( jQuery("#calc_shipping_country").val() == 'RU' )
		jQuery("#calc_shipping_postcode").parent().show();
	else
		jQuery("#calc_shipping_postcode").parent().hide();
};
jQuery("#calc_shipping_country").change(toggle_shipping_postcode);
jQuery(document).ready(toggle_shipping_postcode);
</script>

<?php do_action( 'carton_after_shipping_calculator' ); ?>

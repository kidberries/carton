<?php
/**
 * External product add to cart
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<?php do_action('carton_before_add_to_cart_button'); ?>

<p class="cart"><a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow" class="single_add_to_cart_button button alt"><?php echo apply_filters('single_add_to_cart_text', $button_text, 'external'); ?></a></p>

<?php do_action('carton_after_add_to_cart_button'); ?>
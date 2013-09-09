<?php
/**
 * Empty cart page
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<p><?php _e( 'Your cart is currently empty.', 'carton' ) ?></p>

<?php do_action('carton_cart_is_empty'); ?>

<p><a class="button" href="<?php echo get_permalink(carton_get_page_id('shop')); ?>"><?php _e( '&larr; Return To Shop', 'carton' ) ?></a></p>
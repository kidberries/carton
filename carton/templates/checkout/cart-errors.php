<?php
/**
 * Cart errors page
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php $carton->show_messages(); ?>

<p><?php _e( 'There are some issues with the items in your cart (shown above). Please go back to the cart page and resolve these issues before checking out.', 'carton' ) ?></p>

<?php do_action('carton_cart_has_errors'); ?>

<p><a class="button" href="<?php echo get_permalink(carton_get_page_id('cart')); ?>"><?php _e( '&larr; Return To Cart', 'carton' ) ?></a></p>
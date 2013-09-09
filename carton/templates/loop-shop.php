<?php
/**
 * Loop-shop (deprecated)
 *
 * Outputs a product loop
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 * @deprecated 	1.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

_deprecated_file( basename(__FILE__), '1.6', '', 'Use your own loop code, as well as the content-product.php template. loop-shop.php will be removed in WC 2.1.' );
?>

<?php if ( have_posts() ) : ?>

	<?php do_action('carton_before_shop_loop'); ?>

	<?php carton_product_loop_start(); ?>

		<?php carton_product_subcategories(); ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php carton_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php carton_product_loop_end(); ?>

	<?php do_action('carton_after_shop_loop'); ?>

<?php else : ?>

	<?php if ( ! carton_product_subcategories( array( 'before' => carton_product_loop_start( false ), 'after' => carton_product_loop_end( false ) ) ) ) : ?>

		<p><?php _e( 'No products found which match your selection.', 'carton' ); ?></p>

	<?php endif; ?>

<?php endif; ?>

<div class="clear"></div>
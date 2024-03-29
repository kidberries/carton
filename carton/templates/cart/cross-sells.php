<?php
/**
 * Cross-sells
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton_loop, $carton, $product;

$crosssells = $carton->cart->get_cross_sells();

if ( sizeof( $crosssells ) == 0 ) return;

$meta_query = array();
$meta_query[] = $carton->query->visibility_meta_query();
$meta_query[] = $carton->query->stock_status_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'posts_per_page'      => 2,
	'no_found_rows'       => 1,
	'orderby'             => 'rand',
	'post__in'            => $crosssells,
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$carton_loop['columns'] 	= 2;

if ( $products->have_posts() ) : ?>

	<div class="cross-sells">

		<h2><?php _e( 'You may be interested in&hellip;', 'carton' ) ?></h2>

		<?php carton_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php carton_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php carton_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_query();

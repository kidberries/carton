<?php
/**
 * Single Product Up-Sells
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $carton, $carton_loop;

$upsells = $product->get_upsells();

if ( sizeof( $upsells ) == 0 ) return;

$meta_query = array();
$meta_query[] = $carton->query->visibility_meta_query();
$meta_query[] = $carton->query->stock_status_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$carton_loop['columns'] 	= $columns;

if ( $products->have_posts() ) : ?>

	<div class="upsells products">

		<h2><?php _e( 'You may also like&hellip;', 'carton' ) ?></h2>

		<?php carton_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php carton_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php carton_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_postdata();

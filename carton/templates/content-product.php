<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/carton/content-product.php
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $carton_loop;

// Store loop count we're currently on
if ( empty( $carton_loop['loop'] ) )
	$carton_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $carton_loop['columns'] ) )
	$carton_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product->is_visible() )
	return;

// Increase loop count
$carton_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $carton_loop['loop'] - 1 ) % $carton_loop['columns'] || 1 == $carton_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $carton_loop['loop'] % $carton_loop['columns'] )
	$classes[] = 'last';
?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'carton_before_shop_loop_item' ); ?>

	<a href="<?php the_permalink(); ?>">

		<?php
			/**
			 * carton_before_shop_loop_item_title hook
			 *
			 * @hooked carton_show_product_loop_sale_flash - 10
			 * @hooked carton_template_loop_product_thumbnail - 10
			 */
			do_action( 'carton_before_shop_loop_item_title' );
		?>

		<h3><?php the_title(); ?></h3>

		<?php
			/**
			 * carton_after_shop_loop_item_title hook
			 *
			 * @hooked carton_template_loop_price - 10
			 */
			do_action( 'carton_after_shop_loop_item_title' );
		?>

	</a>

	<?php do_action( 'carton_after_shop_loop_item' ); ?>

</li>
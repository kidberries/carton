<?php
/**
 * The template for displaying product category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/carton/content-product_cat.php
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton_loop;

// Store loop count we're currently on
if ( empty( $carton_loop['loop'] ) )
	$carton_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $carton_loop['columns'] ) )
	$carton_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Increase loop count
$carton_loop['loop']++;
?>
<li class="product-category product<?php
    if ( ( $carton_loop['loop'] - 1 ) % $carton_loop['columns'] == 0 || $carton_loop['columns'] == 1)
        echo ' first';
	if ( $carton_loop['loop'] % $carton_loop['columns'] == 0 )
		echo ' last';
	?>">

	<?php do_action( 'carton_before_subcategory', $category ); ?>

	<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">

		<?php
			/**
			 * carton_before_subcategory_title hook
			 *
			 * @hooked carton_subcategory_thumbnail - 10
			 */
			do_action( 'carton_before_subcategory_title', $category );
		?>

		<h3>
			<?php
				echo $category->name;

				if ( $category->count > 0 )
					echo apply_filters( 'carton_subcategory_count_html', ' <mark class="count">(' . $category->count . ')</mark>', $category );
			?>
		</h3>

		<?php
			/**
			 * carton_after_subcategory_title hook
			 */
			do_action( 'carton_after_subcategory_title', $category );
		?>

	</a>

	<?php do_action( 'carton_after_subcategory', $category ); ?>

</li>
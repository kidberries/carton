<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/carton/content-single-product.php
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	/**
	 * carton_before_single_product hook
	 *
	 * @hooked carton_show_messages - 10
	 */
	 do_action( 'carton_before_single_product' );
?>

<div itemscope="" itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * carton_show_product_images hook
		 *
		 * @hooked carton_show_product_sale_flash - 10
		 * @hooked carton_show_product_images - 20
		 */
		do_action( 'carton_before_single_product_summary' );
	?>

	<div class="summary entry-summary">

		<?php
			/**
			 * carton_single_product_summary hook
			 *
			 * @hooked carton_template_single_title - 5
			 * @hooked carton_template_single_price - 10
			 * @hooked carton_template_single_excerpt - 20
			 * @hooked carton_template_single_add_to_cart - 30
			 * @hooked carton_template_single_meta - 40
			 * @hooked carton_template_single_sharing - 50
			 */
			do_action( 'carton_single_product_summary' );
		?>

	</div><!-- .summary -->

	<?php
		/**
		 * carton_after_single_product_summary hook
		 *
		 * @hooked carton_output_product_data_tabs - 10
		 * @hooked carton_output_related_products - 20
		 */
		do_action( 'carton_after_single_product_summary' );
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'carton_after_single_product' ); ?>
<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/carton/single-product.php
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header('shop'); ?>

	<?php
		/**
		 * carton_before_main_content hook
		 *
		 * @hooked carton_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked carton_breadcrumb - 20
		 */
		do_action('carton_before_main_content');
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php carton_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * carton_after_main_content hook
		 *
		 * @hooked carton_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('carton_after_main_content');
	?>

	<?php
		/**
		 * carton_sidebar hook
		 *
		 * @hooked carton_get_sidebar - 10
		 */
		do_action('carton_sidebar');
	?>

<?php get_footer('shop'); ?>

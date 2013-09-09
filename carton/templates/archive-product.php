<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/carton/archive-product.php
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
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

        <?php if ( is_tax( 'product_cat' ) ) {
            ?><h1 class="page-title product-category"><?php carton_page_title(); ?></h1><?php
            $term = get_term_by('slug', esc_attr( get_query_var('product_cat') ), 'product_cat');
            $advertisement = html_entity_decode( get_carton_term_meta( $term->term_id, 'advertisement', true ) );
            if( $advertisement ) {
                ?><div class="category advertisement"><?php
                    echo $advertisement;
                ?></div><?php
            }
        } else {
            ?><h1 class="page-title"><?php carton_page_title(); ?></h1><?php
        }?>

		<?php do_action( 'carton_archive_description' ); ?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * carton_before_shop_loop hook
				 *
				 * @hooked carton_result_count - 20
				 * @hooked carton_catalog_ordering - 30
				 */
				do_action( 'carton_before_shop_loop' );
			?>

			<?php carton_product_loop_start(); ?>

				<?php carton_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php carton_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php carton_product_loop_end(); ?>

			<?php
				/**
				 * carton_after_shop_loop hook
				 *
				 * @hooked carton_pagination - 10
				 */
				do_action( 'carton_after_shop_loop' );
			?>

		<?php elseif ( ! carton_product_subcategories( array( 'before' => carton_product_loop_start( false ), 'after' => carton_product_loop_end( false ) ) ) ) : ?>

			<?php carton_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

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
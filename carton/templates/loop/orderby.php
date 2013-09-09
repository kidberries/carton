<?php
/**
 * Show options for ordering
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton, $wp_query;

if ( 1 == $wp_query->found_posts || ! carton_products_will_display() )
	return;
?>
<form class="carton-ordering" method="get">
	<select name="orderby" class="orderby">
		<?php
			$catalog_orderby = apply_filters( 'carton_catalog_orderby', array(
				'menu_order' => __( 'Default sorting', 'carton' ),
				'popularity' => __( 'Sort by popularity', 'carton' ),
				'rating'     => __( 'Sort by average rating', 'carton' ),
				'date'       => __( 'Sort by newness', 'carton' ),
				'price'      => __( 'Sort by price: low to high', 'carton' ),
				'price-desc' => __( 'Sort by price: high to low', 'carton' )
			) );

			if ( get_option( 'carton_enable_review_rating' ) == 'no' )
				unset( $catalog_orderby['rating'] );

			foreach ( $catalog_orderby as $id => $name )
				echo '<option value="' . esc_attr( $id ) . '" ' . selected( $orderby, $id, false ) . '>' . esc_attr( $name ) . '</option>';
		?>
	</select>
	<?php
		// Keep query string vars intact
		foreach ( $_GET as $key => $val ) {
			if ( 'orderby' == $key )
				continue;
			
			if (is_array($val)) {
				foreach($val as $innerVal) {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $innerVal ) . '" />';
				}
			
			} else {
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
			}
		}
	?>
</form>

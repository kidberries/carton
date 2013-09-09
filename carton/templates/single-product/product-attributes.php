<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton, $attributes;

$alt = 1;
$attributes = $product->get_attributes();

if ( empty( $attributes ) && ( ! $product->enable_dimensions_display() || ( ! $product->has_dimensions() && ! $product->has_weight() ) ) ) return;
?>
<table class="shop_attributes">

	<?php if ( $product->enable_dimensions_display() ) : ?>

		<?php if ( $product->has_weight() ) : $alt = $alt * -1; ?>

			<tr class="<?php if ( $alt == 1 ) echo 'alt'; ?>">
				<th><?php _e( 'Weight', 'carton' ) ?></th>
				<td class="product_weight"><?php echo $product->get_weight() . ' ' . esc_attr( __(get_option('carton_weight_unit'),'carton') ); ?></td>
			</tr>

		<?php endif; ?>

		<?php if ($product->has_dimensions()) : $alt = $alt * -1; ?>

			<tr class="<?php if ( $alt == 1 ) echo 'alt'; ?>">
				<th><?php _e( 'Dimensions', 'carton' ) ?></th>
				<td class="product_dimensions"><?php echo $product->get_dimensions(); ?></td>
			</tr>

		<?php endif; ?>

	<?php endif; ?>

	<?php foreach ($attributes as $attribute) :
		if ( ! isset( $attribute['is_visible'] ) || ! $attribute['is_visible'] ) continue;
		if ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) continue;

		$alt = $alt * -1;
		?>

		<tr class="<?php if ( $alt == 1 ) echo 'alt'; ?>">
			<th><?php echo $carton->attribute_label( $attribute['name'] ); ?></th>
			<td>
			<span <?php if ( $attribute['is_changeable'] ) echo ' class="changeable attribute name attribute_' . $attribute['name'] . '"'; ?>>
				<?php
				if ( $attribute['is_taxonomy'] ) {

					$values = carton_get_product_terms( $product->id, $attribute['name'], 'names' );
					echo apply_filters( 'carton_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

				} else {

					// Convert pipes to commas and display values
					$values = array_map( 'trim', explode( '|', $attribute['value'] ) );
					echo apply_filters( 'carton_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

				}
			?>
			</span>
			</td>
		</tr>

	<?php endforeach; ?>

</table>


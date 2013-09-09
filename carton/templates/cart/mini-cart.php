<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;
?>

<?php do_action( 'carton_before_mini_cart' ); ?>

<ul class="cart_list product_list_widget <?php echo $args['list_class']; ?>">

	<?php if ( sizeof( $carton->cart->get_cart() ) > 0 ) : ?>

		<?php foreach ( $carton->cart->get_cart() as $cart_item_key => $cart_item ) :

			$_product = $cart_item['data'];

			// Only display if allowed
			if ( ! apply_filters('carton_widget_cart_item_visible', true, $cart_item, $cart_item_key ) || ! $_product->exists() || $cart_item['quantity'] == 0 )
				continue;

			// Get price
			$product_price = get_option( 'carton_tax_display_cart' ) == 'excl' ? $_product->get_price_excluding_tax() : $_product->get_price_including_tax();

			$product_price = apply_filters( 'carton_cart_item_price_html', carton_price( $product_price ), $cart_item, $cart_item_key );
			?>

			<li>
				<a href="<?php echo get_permalink( $cart_item['product_id'] ); ?>">

					<?php echo $_product->get_image(); ?>

					<?php echo apply_filters('carton_widget_cart_product_title', $_product->get_title(), $_product ); ?>

				</a>

				<?php echo $carton->cart->get_item_data( $cart_item ); ?>

				<?php echo apply_filters( 'carton_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
			</li>

		<?php endforeach; ?>

	<?php else : ?>

		<li class="empty"><?php _e( 'No products in the cart.', 'carton' ); ?></li>

	<?php endif; ?>

</ul><!-- end product list -->

<?php if ( sizeof( $carton->cart->get_cart() ) > 0 ) : ?>

	<p class="total"><strong><?php _e( 'Subtotal', 'carton' ); ?>:</strong> <?php echo $carton->cart->get_cart_subtotal(); ?></p>

	<?php do_action( 'carton_widget_shopping_cart_before_buttons' ); ?>

	<p class="buttons">
		<a href="<?php echo $carton->cart->get_cart_url(); ?>" class="button"><?php _e( 'View Cart &rarr;', 'carton' ); ?></a>
		<a href="<?php echo $carton->cart->get_checkout_url(); ?>" class="button checkout"><?php _e( 'Checkout &rarr;', 'carton' ); ?></a>
	</p>

<?php endif; ?>

<?php do_action( 'carton_after_mini_cart' ); ?>

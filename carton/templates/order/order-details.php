<?php
/**
 * Order details
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton;

$order = new CTN_Order( $order_id );
?>
<h2><?php _e( 'Order Details', 'carton' ); ?></h2>
<table class="shop_table order_details">
<?php /* ?>
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'carton' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'carton' ); ?></th>
		</tr>
	</thead>
<?php */ ?>
	<tfoot>
	<?php
		if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
			?>
			<tr>
			<?php if ( $total['value'] ) :?>
				<th scope="row"><?php echo $total['label']; ?></th>
				<td><?php echo $total['value'];?></td>
			<?php else: ?>
				<th scope="row" colspan="2"><?php echo $total['label']; ?></th>
			<?php endif; ?>
			</tr>
			<?php
		endforeach;
	?>
	</tfoot>
	<tbody>
		<?php
		if (sizeof($order->get_items())>0) {

			foreach($order->get_items() as $item) {

				$_product = get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );

				echo '
					<tr class = "' . esc_attr( apply_filters( 'carton_order_table_item_class', 'order_table_item', $item, $order ) ) . '">
						<td class="product-name">' .
							apply_filters( 'carton_order_table_product_title', '<a href="' . get_permalink( $item['product_id'] ) . '">' . $item['name'] . '</a>', $item ) . ' ' .
							apply_filters( 'carton_order_table_item_quantity', '<strong class="product-quantity">&times; ' . $item['qty'] . '</strong>', $item );

				$item_meta = new CTN_Order_Item_Meta( $item['item_meta'] );
				$item_meta->display();

				if ( $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

					$download_file_urls = $order->get_downloadable_file_urls( $item['product_id'], $item['variation_id'], $item );

					$i     = 0;
					$links = array();

					foreach ( $download_file_urls as $file_url => $download_file_url ) {

						$links[] = '<small><a href="' . $download_file_url . '">' . sprintf( __( 'Download file%s', 'carton' ), ( count( $download_file_urls ) > 1 ? ' ' . ( $i + 1 ) . ': ' : ': ' ) ) . basename( $file_url ) . '</a></small>';

						$i++;
					}

					echo implode( '<br/>', $links );
				}

				echo '</td><td class="product-total">' . $order->get_formatted_line_subtotal( $item ) . '</td></tr>';

				// Show any purchase notes
				if ($order->status=='completed' || $order->status=='processing') {
					if ($purchase_note = get_post_meta( $_product->id, '_purchase_note', true))
						echo '<tr class="product-purchase-note"><td colspan="3">' . apply_filters('the_content', $purchase_note) . '</td></tr>';
				}

			}
		}

		do_action( 'carton_order_items_table', $order );
		?>
	</tbody>
</table>

<?php if ( get_option('carton_allow_customers_to_reorder') == 'yes' && $order->status=='completed' ) : ?>
	<p class="order-again">
		<a href="<?php echo esc_url( $carton->nonce_url( 'order_again', add_query_arg( 'order_again', $order->id, add_query_arg( 'order', $order->id, get_permalink( carton_get_page_id( 'view_order' ) ) ) ) ) ); ?>" class="button"><?php _e( 'Order Again', 'carton' ); ?></a>
	</p>
<?php endif; ?>

<?php do_action( 'carton_order_details_after_order_table', $order ); ?>

<header>
	<h2><?php _e( 'Customer details', 'carton' ); ?></h2>
</header>
<dl class="customer_details">
<?php
	if ($order->billing_email) echo '<dt>'.__( 'Email:', 'carton' ).'</dt><dd>'.$order->billing_email.'</dd>';
	if ($order->billing_phone) echo '<dt>'.__( 'Telephone:', 'carton' ).'</dt><dd>'.$order->billing_phone.'</dd>';
?>
</dl>

<?php if (get_option('carton_ship_to_billing_address_only')=='no') : ?>

<div class="col2-set addresses">

	<div class="col-1">

<?php endif; ?>

		<header class="title">
			<h3><?php _e( 'Your Address (for billing)', 'carton' ); ?></h3>
		</header>
		<address><p>
			<?php
				if (!$order->get_formatted_billing_address()) _e( 'N/A', 'carton' ); else echo $order->get_formatted_billing_address();
			?>
		</p></address>

<?php if (get_option('carton_ship_to_billing_address_only')=='no') : ?>

	</div><!-- /.col-1 -->

	<div class="col-2">

		<header class="title">
			<h3><?php _e( 'Shipping Address', 'carton' ); ?></h3>
		</header>
		<address><p>
			<?php
				if (!$order->get_formatted_shipping_address()) _e( 'N/A', 'carton' ); else echo $order->get_formatted_shipping_address();
			?>
		</p></address>

	</div><!-- /.col-2 -->

</div><!-- /.col2-set -->

<?php endif; ?>

<div class="clear"></div>

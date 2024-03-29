<?php
/**
 * View_Order Shortcode
 *
 * @author 		CartonThemes
 * @category 	Shortcodes
 * @package 	CartoN/Shortcodes/View_Order
 * @version     2.0.0
 */

class CTN_Shortcode_View_Order {

	/**
	 * Get the shortcode content.
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		global $carton;
		return $carton->shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $atts ) {
		global $carton;

		$carton->nocache();

		if ( ! is_user_logged_in() ) return;

		extract( shortcode_atts( array(
	    	'order_count' => 10
		), $atts ) );

		$user_id      	= get_current_user_id();
		$order_id		= ( isset( $_GET['order'] ) ) ? $_GET['order'] : 0;
		$order 			= new CTN_Order( $order_id );

		if ( $order_id == 0 ) {
			carton_get_template( 'myaccount/my-orders.php', array( 'order_count' => 'all' == $order_count ? -1 : $order_count ) );
			return;
		}

		if ( $order->user_id != $user_id ) {
			echo '<div class="carton-error">' . __( 'Invalid order.', 'carton' ) . ' <a href="'.get_permalink( carton_get_page_id('myaccount') ).'">'. __( 'My Account &rarr;', 'carton' ) .'</a>' . '</div>';
			return;
		}

		$status = get_term_by('slug', $order->status, 'shop_order_status');

		echo '<p class="order-info">'
		. sprintf( __( 'Order <mark class="order-number">%s</mark> made on <mark class="order-date">%s</mark>', 'carton'), $order->get_order_number(), date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ) )
		. '. ' . sprintf( __( 'Order status: <mark class="order-status">%s</mark>', 'carton' ), __( $status->name, 'carton' ) )
		. '.</p>';

		$notes = $order->get_customer_order_notes();
		if ($notes) :
			?>
			<h2><?php _e( 'Order Updates', 'carton' ); ?></h2>
			<ol class="commentlist notes">
				<?php foreach ($notes as $note) : ?>
				<li class="comment note">
					<div class="comment_container">
						<div class="comment-text">
							<p class="meta"><?php echo date_i18n(__( 'l jS \of F Y, h:ia', 'carton' ), strtotime($note->comment_date)); ?></p>
							<div class="description">
								<?php echo wpautop(wptexturize($note->comment_content)); ?>
							</div>
			  				<div class="clear"></div>
			  			</div>
						<div class="clear"></div>
					</div>
				</li>
				<?php endforeach; ?>
			</ol>
			<?php
		endif;

		do_action( 'carton_view_order', $order_id );
	}
}
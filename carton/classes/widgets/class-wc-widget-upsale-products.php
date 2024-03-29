<?php
/**
 * CartoN Upsale Products Widget
 *
 * @author 		CartonThemes
 * @category 	Widgets
 * @package 	CartoN/Widgets
 * @version 	1.6.4
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CTN_Widget_Upsale_Products extends WP_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		$this->id_base = 'carton_upsale_products';
		$this->name    = __( 'CartoN Upsale Products', 'carton' );
		$this->widget_options = array(
			'classname'   => 'carton widget_upsale_products',
			'description' => __( 'Display a list of upsale products on your site.', 'carton' ),
		);

		parent::__construct( $this->id_base, $this->name, $this->widget_options );
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		global $carton, $product;

		// Use default title as fallback
		$title = ( '' === $instance['title'] ) ? __('Upsale Products', 'carton' ) : $instance['title'];
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		// Setup product query
		$query_args = array(
			'post_ids'       => array(1960,1864,1859),
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $instance['number'],
			'orderby'        => 'rand',
			'no_found_rows'  => 1
		);

		$query_args['meta_query'] = array();

		if ( ! $instance['show_variations'] ) {
			$query_args['meta_query'][] = $carton->query->visibility_meta_query();
			$query_args['post_parent'] = 0;
		}

	    $query_args['meta_query'][] = $carton->query->stock_status_meta_query();

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			echo $args['before_widget'];

			if ( '' !== $title ) {
				echo $args['before_title'], $title, $args['after_title'];
			} ?>

			<ul class="product_list_widget">
				<?php while ($query->have_posts()) : $query->the_post(); global $product; ?>
					<li>
						<a href="<?php the_permalink() ?>">
							<?php
								if ( has_post_thumbnail() )
									the_post_thumbnail( 'shop_thumbnail' );
								else
									echo carton_placeholder_img( 'shop_thumbnail' );
							?>
							<?php the_title() ?>
						</a>
						<?php echo $product->get_price_html() ?>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
			echo $args['after_widget'];
		}
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = array(
			'title'           => strip_tags($new_instance['title']),
			'number'          => min(15, max(1, (int) $new_instance['number'])),
			'show_variations' => ! empty($new_instance['show_variations'])
		);

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		// Default values
		$title           = isset( $instance['title'] ) ? $instance['title'] : '';
		$number          = isset( $instance['number'] ) ? (int) $instance['number'] : 5;
		$show_variations = ! empty( $instance['show_variations'] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php _e( 'Title:', 'carton' ) ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('title') ) ?>" type="text" value="<?php echo esc_attr( $title ) ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ) ?>"><?php _e( 'Number of products to show:', 'carton' ) ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('number') ) ?>" type="text" value="<?php echo esc_attr( $number ) ?>" size="3" />
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_variations' ) ) ?>" name="<?php echo esc_attr( $this->get_field_name('show_variations') ) ?>" <?php checked( $show_variations ) ?> />
			<label for="<?php echo $this->get_field_id( 'show_variations' ) ?>"><?php _e( 'Show hidden product variations', 'carton' ) ?></label>
		</p>

		<?php
	}

}
<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Cash on Delivery Gateway
 *
 * Provides a Cash on Delivery Payment Gateway.
 *
 * @class 		CTN_Gateway_COD
 * @extends		CTN_Payment_Gateway
 * @version		2.0.0
 * @package		CartoN/Classes/Payment
 * @author 		Patrick Garman
 */
class CTN_Gateway_COD extends CTN_Payment_Gateway {

    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
	function __construct() {
		$this->id           = 'cod';
		$this->icon         = apply_filters( 'carton_cod_icon', '' );
		$this->method_title = __( 'Cash on Delivery', 'carton' );
		$this->has_fields   = false;

		// Load the settings
		$this->init_form_fields();
		$this->init_settings();

		// Get settings
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->instructions       = $this->get_option( 'instructions' );
		$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );

		add_action( 'carton_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'carton_thankyou_cod', array( $this, 'thankyou' ) );
	}


	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
	function admin_options() {
		?>
		<h3><?php _e('Cash on Delivery','carton'); ?></h3>
    	<p><?php _e('Have your customers pay with cash (or by other means) upon delivery.', 'carton' ); ?></p>
    	<table class="form-table">
    		<?php $this->generate_settings_html(); ?>
		</table> <?php
    }


    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	global $carton;

    	$shipping_methods = array();

    	if ( is_admin() )
	    	foreach ( $carton->shipping->load_shipping_methods() as $method ) {
		    	$shipping_methods[ $method->id ] = $method->get_title();
	    	}

    	$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable COD', 'carton' ),
				'label' => __( 'Enable Cash on Delivery', 'carton' ),
				'type' => 'checkbox',
				'description' => '',
				'default' => 'no'
			),
			'title' => array(
				'title' => __( 'Title', 'carton' ),
				'type' => 'text',
				'description' => __( 'Payment method title that the customer will see on your website.', 'carton' ),
				'default' => __( 'Cash on Delivery', 'carton' ),
				'desc_tip'      => true,
			),
			'description' => array(
				'title' => __( 'Description', 'carton' ),
				'type' => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your website.', 'carton' ),
				'default' => __( 'Pay with cash upon delivery.', 'carton' ),
			),
			'instructions' => array(
				'title' => __( 'Instructions', 'carton' ),
				'type' => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page.', 'carton' ),
				'default' => __( 'Pay with cash upon delivery.', 'carton' )
			),
			'enable_for_methods' => array(
				'title' 		=> __( 'Enable for shipping methods', 'carton' ),
				'type' 			=> 'multiselect',
				'class'			=> 'chosen_select',
				'css'			=> 'width: 450px;',
				'default' 		=> '',
				'description' 	=> __( 'If COD is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'carton' ),
				'options'		=> $shipping_methods,
				'desc_tip'      => true,
			)
 	   );
    }


	/**
	 * Check If The Gateway Is Available For Use
	 *
	 * @access public
	 * @return bool
	 */
	function is_available() {
		global $carton;

		if ( ! empty( $this->enable_for_methods ) ) {

			if ( is_page( carton_get_page_id( 'pay' ) ) ) {

				$order_id = (int) $_GET['order_id'];
				$order = new CTN_Order( $order_id );

				if ( ! $order->shipping_method )
					return false;

				$chosen_method = $order->shipping_method;

			} elseif ( empty( $carton->session->chosen_shipping_method ) ) {
				return false;
			} else {
				$chosen_method = $carton->session->chosen_shipping_method;
			}

			$found = false;

			foreach ( $this->enable_for_methods as $method_id ) {
				if ( strpos( $chosen_method, $method_id ) === 0 ) {
					$found = true;
					break;
				}
			}

			if ( ! $found )
				return false;
		}

		return parent::is_available();
	}


    /**
     * Process the payment and return the result
     *
     * @access public
     * @param int $order_id
     * @return array
     */
	function process_payment ($order_id) {
		global $carton;

		$order = new CTN_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status('on-hold', __( 'Payment to be made upon delivery.', 'carton' ));

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		$carton->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(carton_get_page_id('thanks'))))
		);
	}


    /**
     * Output for the order received page.
     *
     * @access public
     * @return void
     */
	function thankyou() {
		echo $this->instructions != '' ? wpautop( $this->instructions ) : '';
	}

}
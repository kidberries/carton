<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Local Pickup Shipping Method
 *
 * A simple shipping method allowing free pickup as a shipping method
 *
 * @class 		CTN_Shipping_Free_Local_Pickup
 * @version		2.0.0
 * @package		CartoN/Classes/Shipping
 * @author 		CartonThemes
 */
class CTN_Shipping_Free_Local_Pickup extends CTN_Shipping_Method {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		$this->id 		= 'free_local_pickup';
		$this->method_title = __( 'Free Local Pickup', 'carton' );
		$this->init();
	}

    /**
     * init function.
     *
     * @access public
     * @return void
     */
    function init() {

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->enabled		= $this->get_option( 'enabled' );
		$this->title		= $this->get_option( 'title' );
		$this->address		= $this->get_option( 'address' );

		// Actions
		add_action( 'carton_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

	}

	/**
	 * calculate_shipping function.
	 *
	 * @access public
	 * @return void
	 */
	function calculate_shipping() {
		$rate = array(
			'id' 		=> $this->id,
			'label' 	=> $this->title,
		);
		$this->add_rate($rate);
	}

	/**
	 * init_form_fields function.
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields() {
    	global $carton;
    	$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable', 'carton' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable local pickup for free', 'carton' ),
				'default' 		=> 'no'
			),
			'title' => array(
				'title' 		=> __( 'Title', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This controls the title which the user sees during checkout.', 'carton' ),
				'default'		=> __( 'Free Local Pickup', 'carton' ),
				'desc_tip'      => true,
			),
			'address' => array(
				'title' 		=> __( 'Pickup point address', 'carton' ),
				'type' 			=> 'textarea',
				'description' 	=> __( 'This controls the pickup place point which the user sees.', 'carton' ),
				'default'		=> '',
				'desc_tip'      => true,
				'placeholder'	=> 'Your store or office placement address etc'
			),
		);
	}

	/**
	 * admin_options function.
	 *
	 * @access public
	 * @return void
	 */
	function admin_options() {
		global $carton; ?>
		<h3><?php echo $this->method_title; ?></h3>
		<p><?php _e( 'Local pickup is a simple method which allows the customer to pick up their order themselves.', 'carton' ); ?></p>
		<table class="form-table">
    		<?php $this->generate_settings_html(); ?>
    	</table> <?php
	}

	/**
	 * is_available function.
	 *
	 * @access public
	 * @param array $package
	 * @return bool
	 */
	function is_available( $package ) {
		global $carton;

		$is_available = true;

		if ( $this->enabled == "no" )
			$is_available = false;

		return apply_filters( 'carton_shipping_' . $this->id . '_is_available', $is_available, $package );
	}


	/**
	* clean function.
	*
	* @access public
	* @param mixed $code
	* @return string
	*/
	function clean( $code ) {
		return str_replace( '-', '', sanitize_title( $code ) ) . ( strstr( $code, '*' ) ? '*' : '' );
	}

}

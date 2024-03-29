<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Local Delivery Shipping Method
 *
 * A simple shipping method allowing local delivery as a shipping method
 *
 * @class 		CTN_Shipping_Local_Delivery
 * @version		2.0.0
 * @package		CartoN/Classes/Shipping
 * @author 		CartonThemes
 */
class CTN_Shipping_Local_Delivery extends CTN_Shipping_Method {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		$this->id			= 'local_delivery';
		$this->method_title = __( 'Local Delivery', 'carton' );
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
		$this->title		= $this->get_option( 'title' );
		$this->type 		= $this->get_option( 'type' );
		$this->fee		= $this->get_option( 'fee' );
		$this->type		= $this->get_option( 'type' );
		$this->codes		= $this->get_option( 'codes' );
		$this->availability	= $this->get_option( 'availability' );
		$this->countries	= $this->get_option( 'countries' );

		add_action( 'carton_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * calculate_shipping function.
	 *
	 * @access public
	 * @param array $package (default: array())
	 * @return void
	 */
	function calculate_shipping( $package = array() ) {
		global $carton;


		$shipping_total = 0;
		$fee = ( trim( $this->fee ) == '' ) ? 0 : $this->fee;

		if ( $this->type =='fixed' ) 	$shipping_total 	= $this->fee;

		if ( $this->type =='percent' ) 	$shipping_total 	= $package['contents_cost'] * ( $this->fee / 100 );

		if ( $this->type == 'product' )	{
			foreach ( $carton->cart->get_cart() as $item_id => $values ) {
				$_product = $values['data'];

				if ( $values['quantity'] > 0 && $_product->needs_shipping() )
					$shipping_total += $this->fee * $values['quantity'];
			}
		}

        // Apply Shipping Discounts
		$shipping_total_real = $shipping_total;

		$discount = $this->get_shipping_discout( $package );
		if( $shipping_total > $discount )
		    $shipping_total -= $discount;
		elseif( $shipping_total <= $discount )
		    $shipping_total = 0;
		else
			$shipping_total = null;

		$rate = array(
			'id' 		=> $this->id,
			'label' 	=> $this->title,
			'cost' 		=> $shipping_total,
			'cost_real'	=> $shipping_total_real
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
				'label' 		=> __( 'Enable local delivery', 'carton' ),
				'default' 		=> 'no'
			),
			'title' => array(
				'title' 		=> __( 'Title', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This controls the title which the user sees during checkout.', 'carton' ),
				'default'		=> __( 'Local Delivery', 'carton' ),
				'desc_tip'      => true,
			),
			'type' => array(
				'title' 		=> __( 'Fee Type', 'carton' ),
				'type' 			=> 'select',
				'description' 	=> __( 'How to calculate delivery charges', 'carton' ),
				'default' 		=> 'fixed',
				'options' 		=> array(
					'fixed' 	=> __( 'Fixed amount', 'carton' ),
					'percent'	=> __( 'Percentage of cart total', 'carton' ),
					'product'	=> __( 'Fixed amount per product', 'carton' ),
				),
				'desc_tip'      => true,
			),
			'fee' => array(
				'title' 		=> __( 'Delivery Fee', 'carton' ),
				'type' 			=> 'number',
				'custom_attributes' => array(
					'step'	=> 'any',
					'min'	=> '0'
				),
				'description' 	=> __( 'What fee do you want to charge for local delivery, disregarded if you choose free. Leave blank to disable.', 'carton' ),
				'default'		=> '',
				'desc_tip'      => true,
				'placeholder'	=> '0.00'
			),
			'codes' => array(
				'title' 		=> __( 'Zip/Post Codes', 'carton' ),
				'type' 			=> 'textarea',
				'description' 	=> __( 'What zip/post codes would you like to offer delivery to? Separate codes with a comma. Accepts wildcards, e.g. P* will match a postcode of PE30.', 'carton' ),
				'default'		=> '',
				'desc_tip'      => true,
				'placeholder'	=> '12345, 56789 etc'
			),
			'availability' => array(
							'title' 		=> __( 'Method availability', 'carton' ),
							'type' 			=> 'select',
							'default' 		=> 'all',
							'class'			=> 'availability',
							'options'		=> array(
								'all' 		=> __( 'All allowed countries', 'carton' ),
								'specific' 	=> __( 'Specific Countries', 'carton' )
							)
						),
			'countries' => array(
							'title' 		=> __( 'Specific Countries', 'carton' ),
							'type' 			=> 'multiselect',
							'class'			=> 'chosen_select',
							'css'			=> 'width: 450px;',
							'default' 		=> '',
							'options'		=> $carton->countries->countries
						)
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
		<p><?php _e( 'Local delivery is a simple shipping method for delivering orders locally.', 'carton' ); ?></p>
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

    	if ($this->enabled=="no") return false;

		// If post codes are listed, let's use them.
		$codes = '';
		if ( $this->codes != '' ) {
			foreach( explode( ',', $this->codes ) as $code ) {
				$codes[] = $this->clean( $code );
			}
		}

		if ( is_array( $codes ) ) {

			$found_match = false;

			if ( in_array( $this->clean( $package['destination']['postcode'] ), $codes ) )
				$found_match = true;

			// Wildcard search
			if ( ! $found_match ) {

				$customer_postcode = $this->clean( $package['destination']['postcode'] );
				$customer_postcode_length = strlen( $customer_postcode );

				for ( $i = 0; $i <= $customer_postcode_length; $i++ ) {

					if ( in_array( $customer_postcode, $codes ) )
						$found_match = true;

					$customer_postcode = substr( $customer_postcode, 0, -2 ) . '*';
				}
			}

			if ( ! $found_match )
				return false;
		}

		// Either post codes not setup, or post codes are in array... so lefts check countries for backwards compatibility.
		$ship_to_countries = '';
		if ($this->availability == 'specific') :
			$ship_to_countries = $this->countries;
		else :
			if (get_option('carton_allowed_countries')=='specific') :
				$ship_to_countries = get_option('carton_specific_allowed_countries');
			endif;
		endif;

		if (is_array($ship_to_countries))
			if (!in_array( $package['destination']['country'] , $ship_to_countries))
				return false;

		// Yay! We passed!
		return apply_filters( 'carton_shipping_' . $this->id . '_is_available', true );
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

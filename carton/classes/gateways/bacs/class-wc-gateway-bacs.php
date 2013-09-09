<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Bank Transfer Payment Gateway
 *
 * Provides a Bank Transfer Payment Gateway. Based on code by Mike Pepper.
 *
 * @class 		CTN_Gateway_BACS
 * @extends		CTN_Payment_Gateway
 * @version		2.0.0
 * @package		CartoN/Classes/Payment
 * @author 		CartonThemes
 */
class CTN_Gateway_BACS extends CTN_Payment_Gateway {

    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
    public function __construct() {
		$this->id				= 'bacs';
		$this->icon 			= apply_filters('carton_bacs_icon', '');
		$this->has_fields 		= false;
		$this->method_title     = __( 'Bacs', 'carton' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title 			= $this->get_option( 'title' );
		$this->description      = $this->get_option( 'description' );
		$this->account_name     = $this->get_option( 'account_name' );
		$this->account_number   = $this->get_option( 'account_number' );
		$this->sort_code        = $this->get_option( 'sort_code' );
		$this->bank_name        = $this->get_option( 'bank_name' );
		$this->iban             = $this->get_option( 'iban' );
		$this->bic              = $this->get_option( 'bic' );

		// Actions
		add_action( 'carton_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    	add_action( 'carton_thankyou_bacs', array( $this, 'thankyou_page' ) );

    	// Customer Emails
    	add_action( 'carton_email_before_order_table', array( $this, 'email_instructions' ), 10, 2 );
    }


    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

    	$this->form_fields = array(
			'enabled' => array(
							'title' => __( 'Enable/Disable', 'carton' ),
							'type' => 'checkbox',
							'label' => __( 'Enable Bank Transfer', 'carton' ),
							'default' => 'yes'
						),
			'title' => array(
							'title' => __( 'Title', 'carton' ),
							'type' => 'text',
							'description' => __( 'This controls the title which the user sees during checkout.', 'carton' ),
							'default' => __( 'Direct Bank Transfer', 'carton' ),
							'desc_tip'      => true,
						),
			'description' => array(
							'title' => __( 'Customer Message', 'carton' ),
							'type' => 'textarea',
							'description' => __( 'Give the customer instructions for paying via BACS, and let them know that their order won\'t be shipping until the money is received.', 'carton' ),
							'default' => __( 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order wont be shipped until the funds have cleared in our account.', 'carton' )
						),
			'account_details' => array(
							'title' => __( 'Account Details', 'carton' ),
							'type' => 'title',
							'description' => __( 'Optionally enter your bank details below for customers to pay into.', 'carton' ),
							'default' => ''
						),
			'account_name' => array(
							'title' => __( 'Account Name', 'carton' ),
							'type' => 'text',
							'description' => '',
							'default' => ''
						),
			'account_number' => array(
							'title' => __( 'Account Number', 'carton' ),
							'type' => 'text',
							'description' => '',
							'default' => ''
						),
			'sort_code' => array(
							'title' => __( 'Sort Code', 'carton' ),
							'type' => 'text',
							'description' => '',
							'default' => ''
						),
			'bank_name' => array(
							'title' => __( 'Bank Name', 'carton' ),
							'type' => 'text',
							'description' => '',
							'default' => ''
						),
			'iban' => array(
							'title' => __( 'IBAN', 'carton' ),
							'type' => 'text',
							'default' => ''
						),
			'bic' => array(
							'title' => __( 'BIC (formerly Swift)', 'carton' ),
							'type' => 'text',
							'default' => ''
						),

			);

    }


	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options() {
    	?>
    	<h3><?php _e( 'BACS Payment', 'carton' ); ?></h3>
    	<p><?php _e('Allows payments by BACS (Bank Account Clearing System), more commonly known as direct bank/wire transfer.', 'carton' ); ?></p>
    	<table class="form-table">
    	<?php
    		// Generate the HTML For the settings form.
    		$this->generate_settings_html();
    	?>
		</table><!--/.form-table-->
    	<?php
    }


    /**
     * Output for the order received page.
     *
     * @access public
     * @return void
     */
    function thankyou_page() {
		if ( $description = $this->get_description() )
        	echo wpautop( wptexturize( wp_kses_post( $description ) ) );

		echo '<h2>' . __( 'Our Details', 'carton' ) . '</h2>';

		echo '<ul class="order_details bacs_details">';

		$fields = apply_filters('carton_bacs_fields', array(
			'account_name' 	=> __( 'Account Name', 'carton' ),
			'account_number'=> __( 'Account Number', 'carton' ),
			'sort_code'		=> __( 'Sort Code', 'carton' ),
			'bank_name'		=> __( 'Bank Name', 'carton' ),
			'iban'			=> __( 'IBAN', 'carton' ),
			'bic'			=> __( 'BIC', 'carton' )
		));

		foreach ( $fields as $key=>$value ) {
		    if ( ! empty( $this->$key ) ) {
		    	echo '<li class="' . esc_attr( $key ) . '">' . esc_attr( $value ) . ': <strong>' . wptexturize( $this->$key ) . '</strong></li>';
		    }
		}

		echo '</ul>';
    }


    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param CTN_Order $order
     * @param bool $sent_to_admin
     * @return void
     */
    function email_instructions( $order, $sent_to_admin ) {

    	if ( $sent_to_admin ) return;

    	if ( $order->status !== 'on-hold') return;

    	if ( $order->payment_method !== 'bacs') return;

		if ( $description = $this->get_description() )
        	echo wpautop( wptexturize( $description ) );

		?><h2><?php _e( 'Our Details', 'carton' ) ?></h2><ul class="order_details bacs_details"><?php

		$fields = apply_filters('carton_bacs_fields', array(
			'account_name' 	=> __( 'Account Name', 'carton' ),
			'account_number'=> __( 'Account Number', 'carton' ),
			'sort_code'		=> __( 'Sort Code', 'carton' ),
			'bank_name'		=> __( 'Bank Name', 'carton' ),
			'iban'			=> __( 'IBAN', 'carton' ),
			'bic'			=> __( 'BIC', 'carton' )
		));

		foreach ($fields as $key=>$value) :
		    if(!empty($this->$key)) :
		    	echo '<li class="'.$key.'">'.$value.': <strong>'.wptexturize($this->$key).'</strong></li>';
		    endif;
		endforeach;

		?></ul><?php
    }


    /**
     * Process the payment and return the result
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    function process_payment( $order_id ) {
    	global $carton;

		$order = new CTN_Order( $order_id );

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status('on-hold', __( 'Awaiting BACS payment', 'carton' ));

		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		$carton->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order->id, get_permalink(carton_get_page_id('thanks'))))
		);
    }

}
<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Customer Processing Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		CTN_Email_Customer_Processing_Order
 * @version		2.0.0
 * @package		CartoN/Classes/Emails
 * @author 		CartonThemes
 * @extends 	CTN_Email
 */
class CTN_Email_Customer_Processing_Order extends CTN_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'customer_processing_order';
		$this->title 			= __( 'Processing order', 'carton' );
		$this->description		= __( 'This is an order notification sent to the customer after payment containing order details.', 'carton' );

		$this->heading 			= __( 'Thank you for your order', 'carton' );
		$this->subject      	= __( 'Your {blogname} order receipt from {order_date}', 'carton' );

		$this->template_html 	= 'emails/customer-processing-order.php';
		$this->template_plain 	= 'emails/plain/customer-processing-order.php';

		// Triggers for this email
		add_action( 'carton_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'carton_order_status_pending_to_on-hold_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $order_id ) {
		global $carton;

		if ( $order_id ) {
			$this->object 		= new CTN_Order( $order_id );
			$this->recipient	= $this->object->billing_email;

			$this->find[] = '{order_date}';
			$this->replace[] = date_i18n( carton_date_format(), strtotime( $this->object->order_date ) );

			$this->find[] = '{order_number}';
			$this->replace[] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		carton_get_template( $this->template_html, array(
			'order' 		=> $this->object,
			'email_heading' => $this->get_heading()
		) );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		carton_get_template( $this->template_plain, array(
			'order' 		=> $this->object,
			'email_heading' => $this->get_heading()
		) );
		return ob_get_clean();
	}
}
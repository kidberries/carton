<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Customer Invoice
 *
 * An email sent to the customer via admin.
 *
 * @class 		CTN_Email_Customer_Invoice
 * @version		2.0.0
 * @package		CartoN/Classes/Emails
 * @author 		CartonThemes
 * @extends 	CTN_Email
 */
class CTN_Email_Customer_Invoice extends CTN_Email {

	var $find;
	var $replace;

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'customer_invoice';
		$this->title 			= __( 'Customer invoice', 'carton' );
		$this->description		= __( 'Customer invoice emails can be sent to the user containing order info and payment links.', 'carton' );

		$this->template_html 	= 'emails/customer-invoice.php';
		$this->template_plain 	= 'emails/plain/customer-invoice.php';

		$this->subject 			= __( 'Invoice for order {order_number} from {order_date}', 'carton');
		$this->heading      	= __( 'Invoice for order {order_number}', 'carton');

		$this->subject_paid 	= __( 'Your {blogname} order from {order_date}', 'carton');
		$this->heading_paid     = __( 'Order {order_number} details', 'carton');

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $order ) {
		global $carton;

		if ( ! is_object( $order ) ) {
			$order = new CTN_Order( absint( $order ) );
		}

		if ( $order ) {
			$this->object 		= $order;
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
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
		if ( $this->object->status == 'processing' || $this->object->status == 'completed' )
			return apply_filters( 'carton_email_subject_customer_invoice_paid', $this->format_string( $this->subject_paid ), $this->object );
		else
			return apply_filters( 'carton_email_subject_customer_invoice', $this->format_string( $this->subject ), $this->object );
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
		if ( $this->object->status == 'processing' || $this->object->status == 'completed' )
			return apply_filters( 'carton_email_heading_customer_invoice_paid', $this->format_string( $this->heading_paid ), $this->object );
		else
			return apply_filters( 'carton_email_heading_customer_invoice', $this->format_string( $this->heading ), $this->object );
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

    /**
     * Initialise Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'carton' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable this email notification', 'carton' ),
				'default' 		=> 'yes'
			),
			'subject' => array(
				'title' 		=> __( 'Email subject', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'carton' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading' => array(
				'title' 		=> __( 'Email heading', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'carton' ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'subject_paid' => array(
				'title' 		=> __( 'Email subject (paid)', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'carton' ), $this->subject_paid ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading_paid' => array(
				'title' 		=> __( 'Email heading (paid)', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>', 'carton' ), $this->heading_paid ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'email_type' => array(
				'title' 		=> __( 'Email type', 'carton' ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose which format of email to send.', 'carton' ),
				'default' 		=> 'html',
				'class'			=> 'email_type',
				'options'		=> array(
					'plain' 		=> __( 'Plain text', 'carton' ),
					'html' 			=> __( 'HTML', 'carton' ),
					'multipart' 	=> __( 'Multipart', 'carton' ),
				)
			)
		);
    }
}
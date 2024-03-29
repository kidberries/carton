<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Customer Note Order Email
 *
 * Customer note emails are sent when you add a note to an order.
 *
 * @class 		CTN_Email_Customer_Note
 * @version		2.0.0
 * @package		CartoN/Classes/Emails
 * @author 		CartonThemes
 * @extends 	CTN_Email
 */
class CTN_Email_Customer_Note extends CTN_Email {

	var $customer_note;
    
    var $other_recipients;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		$this->id 				= 'customer_note';
		$this->title 			= __( 'Customer note', 'carton' );
		$this->description		= __( 'Customer note emails are sent when you add a note to an order.', 'carton' );

		$this->template_html 	= 'emails/customer-note.php';
		$this->template_plain 	= 'emails/plain/customer-note.php';

		$this->subject 			= __( 'Note added to your {blogname} order from {order_date}', 'carton');
		$this->heading      	= __( 'A note has been added to your order', 'carton');

		// Triggers
		add_action( 'carton_new_customer_note_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
        
		// Other settings
		$this->other_recipients = $this->get_option( 'recipient' );
        
		if ( ! $this->other_recipients )
			$this->other_recipients = get_option( 'admin_email' );
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $args ) {
		global $carton;

		if ( $args ) {

			$defaults = array(
				'order_id' 		=> '',
				'customer_note'	=> ''
			);

			$args = wp_parse_args( $args, $defaults );

			extract( $args );

			$this->object 		= new CTN_Order( $order_id );
			$this->recipient	= $this->object->billing_email;
			$this->customer_note = $customer_note;

			$this->find[] = '{order_date}';
			$this->replace[] = date_i18n( carton_date_format(), strtotime( $this->object->order_date ) );

			$this->find[] = '{order_number}';
			$this->replace[] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;
        $headers = $this->get_headers();

        $headers .= "\n\rCc: " . $this->other_recipients;
		$this->send( $this->get_recipient(),  $this->get_subject(), $this->get_content(), $headers, $this->get_attachments() );
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
			'email_heading' => $this->get_heading(),
			'customer_note' => $this->customer_note
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
			'email_heading' => $this->get_heading(),
			'customer_note' => $this->customer_note
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
			'recipient' => array(
				'title' 		=> __( 'Recipient(s)', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'carton' ), esc_attr( get_option('admin_email') ) ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'subject' => array(
				'title' 		=> __( 'Subject', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'carton' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading' => array(
				'title' 		=> __( 'Email Heading', 'carton' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'carton' ), $this->heading ),
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
					'plain'		 	=> __( 'Plain text', 'carton' ),
					'html' 			=> __( 'HTML', 'carton' ),
					'multipart' 	=> __( 'Multipart', 'carton' ),
				)
			)
		);
    }
}
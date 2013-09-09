<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		CTN_Email_New_Order
 * @version		2.0.0
 * @package		CartoN/Classes/Emails
 * @author 		CartonThemes
 * @extends 	CTN_Email
 */
class CTN_Email_New_Order extends CTN_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'new_order';
		$this->title 			= __( 'New order', 'carton' );
		$this->description		= __( 'New order emails are sent when an order is received/paid by a customer.', 'carton' );

		$this->heading 		= __( 'New customer order', 'carton' );
		$this->subject      	= __( '[{blogname}] New customer order ({order_number}) - {order_date}', 'carton' );

		$this->template_html 	= 'emails/admin-new-order.php';
		$this->template_plain 	= 'emails/plain/admin-new-order.php';

		// Triggers for this email
		add_action( 'carton_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'carton_order_status_pending_to_completed_notification', array( $this, 'trigger' ) );
		add_action( 'carton_order_status_pending_to_on-hold_notification', array( $this, 'trigger' ) );
		add_action( 'carton_order_status_failed_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'carton_order_status_failed_to_completed_notification', array( $this, 'trigger' ) );
		add_action( 'carton_order_status_failed_to_on-hold_notification', array( $this, 'trigger' ) );


		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient )
			$this->recipient = get_option( 'admin_email' );
	}

        /**
         * get_attachments function.
         *
         * @access public
         * @return string
         */
        function get_attachments($order_id) {
                return apply_filters( 'carton_email_attachments_' . $this->id, $order_id );
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

            wp_cache_flush();
			$this->object	= new CTN_Order( $order_id);


			$this->find[] = '{order_date}';
			$this->replace[] = date_i18n( carton_date_format(), strtotime( $this->object->order_date ) );

			$this->find[] = '{order_number}';
			$this->replace[] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

        $attachments = $this->get_attachments($order_id);
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $attachments );
		
		apply_filters( 'carton_email_remove_attachments_' . $this->id, $attachments );
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
			'order' 	=> $this->object,
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
			'order' 	=> $this->object,
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

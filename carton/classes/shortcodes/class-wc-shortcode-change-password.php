<?php
/**
 * Change Password Shortcode
 *
 * @author 		CartonThemes
 * @category 	Shortcodes
 * @package 	CartoN/Shortcodes/Change_Password
 * @version     2.0.0
 */
class CTN_Shortcode_Change_Password {

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

		if ( ! is_user_logged_in() ) return;

		carton_get_template( 'myaccount/form-change-password.php' );
	}
}
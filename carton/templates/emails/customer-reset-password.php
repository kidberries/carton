<?php
/**
 * Customer Reset Password email
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates/Emails
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('carton_email_header', $email_heading); ?>

<p><?php _e( 'Someone requested that the password be reset for the following account:', 'carton' ); ?></p>
<p><?php printf( __( 'Username: %s', 'carton' ), $user_login ); ?></p>
<p><?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'carton' ); ?></p>
<p><?php _e( 'To reset your password, visit the following address:', 'carton' ); ?></p>
<p>
    <a href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'login' => rawurlencode( $user_login ) ), get_permalink( carton_get_page_id( 'lost_password' ) ) ) ); ?>">
			<?php _e( 'Click here to reset your password', 'carton' ); ?></a>
</p>
<p></p>

<?php do_action('carton_email_footer'); ?>
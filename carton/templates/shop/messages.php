<?php
/**
 * Show messages
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $messages ) return;
?>

<?php foreach ( $messages as $message ) : ?>
	<div class="carton-message"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach; ?>

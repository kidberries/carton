<?php
/**
 * Additional payment gateway settings
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Settings
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output payment gateway settings.
 *
 * @access public
 * @return void
 */
function carton_payment_gateways_setting() {
	global $carton;
	?>
	<tr valign="top">
	    <td class="forminp" colspan="2">
			<table class="ctn_gateways widefat" cellspacing="0">
				<thead>
					<tr>
						<th width="1%"><?php _e( 'Default', 'carton' ); ?></th>
						<th><?php _e( 'Gateway', 'carton' ); ?></th>
						<th><?php _e( 'Status', 'carton' ); ?></th>
					</tr>
				</thead>
				<tbody>
		        	<?php
		        	foreach ( $carton->payment_gateways->payment_gateways() as $gateway ) :

		        		$default_gateway = get_option('carton_default_gateway');

		        		echo '<tr>
		        			<td width="1%" class="radio">
		        				<input type="radio" name="default_gateway" value="' . esc_attr( $gateway->id ) . '" ' . checked( $default_gateway, esc_attr( $gateway->id ), false ) . ' />
		        				<input type="hidden" name="gateway_order[]" value="' . esc_attr( $gateway->id ) . '" />
		        			</td>
		        			<td>
		        				<p><strong>' . $gateway->get_title() . '</strong><br/>
		        				<small>' . __( 'Gateway ID', 'carton' ) . ': ' . esc_html( $gateway->id ) . '</small></p>
		        			</td>
		        			<td>';

		        		if ( $gateway->enabled == 'yes' )
		        			echo '<img src="' . $carton->plugin_url() . '/assets/images/success@2x.png" width="16" height="14" alt="yes" />';
						else
							echo '<img src="' . $carton->plugin_url() . '/assets/images/success-off@2x.png" width="16" height="14" alt="no" />';

		        		echo '</td>
		        		</tr>';

		        	endforeach;
		        	?>
				</tbody>
			</table>
		</td>
	</tr>
	<?php
}

add_action( 'carton_admin_field_payment_gateways', 'carton_payment_gateways_setting' );
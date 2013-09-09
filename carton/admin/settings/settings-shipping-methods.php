<?php
/**
 * Additional shipping settings
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Settings
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output shipping method settings.
 *
 * @access public
 * @return void
 */
function carton_shipping_methods_setting() {
	global $carton;
	?>
	<tr valign="top">
		<th scope="row" class="titledesc"><?php _e( 'Shipping Methods', 'carton' ) ?></th>
	    <td class="forminp">
	    	<p class="description" style="margin-top: 0;"><?php _e( 'Drag and drop methods to control their display order.', 'carton' ); ?></p>
			<table class="ctn_shipping widefat ui-sortable" cellspacing="0">
				<thead>
					<tr>
						<th><?php _e( 'Default', 'carton' ); ?></th>
						<th><?php _e( 'Shipping Method', 'carton' ); ?></th>
						<th><?php _e( 'Status', 'carton' ); ?></th>
					</tr>
				</thead>
				<tbody>
			    	<?php
			    	foreach ( $carton->shipping->load_shipping_methods() as $method ) {

				    	$default_shipping_method = esc_attr( get_option('carton_default_shipping_method') );

				    	echo '<tr>
				    		<td width="1%" class="radio">
				    			<input type="radio" name="default_shipping_method" value="' . $method->id . '" ' . checked( $default_shipping_method, $method->id, false ) . ' />
				    			<input type="hidden" name="method_order[]" value="' . $method->id . '" />
				    			<td>
				    				<p><strong>' . $method->get_title() . '</strong><br/>
				    				<small>' . __( 'Method ID', 'carton' ) . ': ' . $method->id . '</small></p>
				    			</td>
				    			<td>';

			    		if ($method->enabled == 'yes')
			    			echo '<img src="' . $carton->plugin_url() . '/assets/images/success@2x.png" width="16 height="14" alt="yes" />';
						else
							echo '<img src="' . $carton->plugin_url() . '/assets/images/success-off@2x.png" width="16" height="14" alt="no" />';

			    		echo '</td>
			    		</tr>';

			    	}
			    	?>
				</tbody>
			</table>
		</td>
	</tr>
	<?php
}

add_action( 'carton_admin_field_shipping_methods', 'carton_shipping_methods_setting' );
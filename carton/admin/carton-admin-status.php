<?php
/**
 * Debug/Status page
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/System Status
 * @version     1.6.4
 */

/**
 * Output the content of the debugging page.
 *
 * @access public
 * @return void
 */
function carton_status() {
	global $carton, $wpdb;

	$current_tab = ! empty( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'status';
    ?>
	<div class="wrap carton">
		<div class="icon32 icon32-carton-status" id="icon-carton"><br /></div><h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php
				$tabs = array(
					'status' => __( 'System Status', 'carton' ),
					'tools'  => __( 'Tools', 'carton' ),
				);
				foreach ( $tabs as $name => $label ) {
					echo '<a href="' . admin_url( 'admin.php?page=carton_status&tab=' . $name ) . '" class="nav-tab ';
					if ( $current_tab == $name ) echo 'nav-tab-active';
					echo '">' . $label . '</a>';
				}
			?>
		</h2><br/>
		<?php
			switch ( $current_tab ) {
				case "tools" :
					carton_status_tools();
				break;
				default :
					carton_status_report();
				break;
			}
		?>
	</div>
	<?php
}

/**
 * carton_status_report function.
 *
 * @access public
 * @return void
 */
function carton_status_report() {
	global $carton, $wpdb;

	?>
	<div class="carton-message">
		<div class="squeezer">
			<h4><?php _e( 'Please include this information when requesting support:', 'carton' ); ?> </h4>
			<p class="submit"><a href="#" download="ctn_report.txt" class="button-primary debug-report"><?php _e( 'Download System Report File', 'carton' ); ?></a></p>
		</div>
	</div>
	<br/>
	<table class="ctn_status_table widefat" cellspacing="0">

		<thead>
			<tr>
				<th colspan="2"><?php _e( 'Environment', 'carton' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<tr>
                <td><?php _e( 'Home URL','carton' ); ?>:</td>
                <td><?php echo home_url(); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Site URL','carton' ); ?>:</td>
                <td><?php echo site_url(); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'WC Version','carton' ); ?>:</td>
                <td><?php echo esc_html( $carton->version ); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'WC Database Version','carton' ); ?>:</td>
                <td><?php echo esc_html( get_option( 'carton_db_version' ) ); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'WP Version','carton' ); ?>:</td>
                <td><?php if ( is_multisite() ) echo 'WPMU'; else echo 'WP'; ?> <?php echo bloginfo('version'); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Web Server Info','carton' ); ?>:</td>
                <td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] );  ?></td>
            </tr>
            <tr>
                <td><?php _e( 'PHP Version','carton' ); ?>:</td>
                <td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'MySQL Version','carton' ); ?>:</td>
                <td><?php if ( function_exists( 'mysql_get_server_info' ) ) echo esc_html( mysql_get_server_info() ); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'WP Memory Limit','carton' ); ?>:</td>
                <td><?php
                	$memory = carton_let_to_num( WP_MEMORY_LIMIT );

                	if ( $memory < 67108864 ) {
                		echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 64MB. See: <a href="%s">Increasing memory allocated to PHP</a>', 'carton' ), wp_convert_bytes_to_hr( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
                	} else {
                		echo '<mark class="yes">' . wp_convert_bytes_to_hr( $memory ) . '</mark>';
                	}
                ?></td>
            </tr>
            <tr>
                <td><?php _e( 'WP Debug Mode','carton' ); ?>:</td>
                <td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<mark class="yes">' . __( 'Yes', 'carton' ) . '</mark>'; else echo '<mark class="no">' . __( 'No', 'carton' ) . '</mark>'; ?></td>
            </tr>
            <tr>
                <td><?php _e( 'WP Max Upload Size','carton' ); ?>:</td>
                <td><?php echo wp_convert_bytes_to_hr( wp_max_upload_size() ); ?></td>
            </tr>
            <tr>
                <td><?php _e('PHP Post Max Size','carton' ); ?>:</td>
                <td><?php if ( function_exists( 'ini_get' ) ) echo wp_convert_bytes_to_hr( carton_let_to_num( ini_get('post_max_size') ) ); ?></td>
            </tr>
            <tr>
                <td><?php _e('PHP Time Limit','carton' ); ?>:</td>
                <td><?php if ( function_exists( 'ini_get' ) ) echo ini_get('max_execution_time'); ?></td>
            </tr>
            <tr>
                <td><?php _e( 'WC Logging','carton' ); ?>:</td>
                <td><?php
                	if ( @fopen( $carton->plugin_path() . '/logs/paypal.txt', 'a' ) )
                		echo '<mark class="yes">' . __( 'Log directory is writable.', 'carton' ) . '</mark>';
                	else
                		echo '<mark class="error">' . __( 'Log directory (<code>carton/logs/</code>) is not writable. Logging will not be possible.', 'carton' ) . '</mark>';
                ?></td>
            </tr>
            <?php
				$posting = array();

				// fsockopen/cURL
				$posting['fsockopen_curl']['name'] = __( 'fsockopen/cURL','carton');
				if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
					if ( function_exists( 'fsockopen' ) && function_exists( 'curl_init' )) {
						$posting['fsockopen_curl']['note'] = __('Your server has fsockopen and cURL enabled.', 'carton' );
					} elseif ( function_exists( 'fsockopen' )) {
						$posting['fsockopen_curl']['note'] = __( 'Your server has fsockopen enabled, cURL is disabled.', 'carton' );
					} else {
						$posting['fsockopen_curl']['note'] = __( 'Your server has cURL enabled, fsockopen is disabled.', 'carton' );
					}
					$posting['fsockopen_curl']['success'] = true;
				} else {
	        		$posting['fsockopen_curl']['note'] = __( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'carton' ). '</mark>';
	        		$posting['fsockopen_curl']['success'] = false;
	        	}

	        	// SOAP
	        	$posting['soap_client']['name'] = __( 'SOAP Client','carton' );
				if ( class_exists( 'SoapClient' ) ) {
					$posting['soap_client']['note'] = __('Your server has the SOAP Client class enabled.', 'carton' );
					$posting['soap_client']['success'] = true;
				} else {
	        		$posting['soap_client']['note'] = sprintf( __( 'Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'carton' ), 'http://php.net/manual/en/class.soapclient.php' ) . '</mark>';
	        		$posting['soap_client']['success'] = false;
	        	}

	        	// WP Remote Post Check
				$posting['wp_remote_post']['name'] = __( 'WP Remote Post','carton');
				$request['cmd'] = '_notify-validate';
				$params = array(
					'sslverify' 	=> false,
		        	'timeout' 		=> 60,
		        	'user-agent'	=> 'CartoN/' . $carton->version,
		        	'body'			=> $request
				);
				$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

				if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
	        		$posting['wp_remote_post']['note'] = __('wp_remote_post() was successful - PayPal IPN is working.', 'carton' );
	        		$posting['wp_remote_post']['success'] = true;
	        	} elseif ( is_wp_error( $response ) ) {
	        		$posting['wp_remote_post']['note'] = __( 'wp_remote_post() failed. PayPal IPN won\'t work with your server. Contact your hosting provider. Error:', 'carton' ) . ' ' . $response->get_error_message();
	        		$posting['wp_remote_post']['success'] = false;
	        	} else {
	            	$posting['wp_remote_post']['note'] = __( 'wp_remote_post() failed. PayPal IPN may not work with your server.', 'carton' );
	        		$posting['wp_remote_post']['success'] = false;
	        	}

	        	$posting = apply_filters( 'carton_debug_posting', $posting );

	        	foreach( $posting as $post ) { $mark = ( isset( $post['success'] ) && $post['success'] == true ) ? 'yes' : 'error';
	        		?>
					<tr>
		                <td><?php echo esc_html( $post['name'] ); ?>:</td>
		                <td>
		                	<mark class="<?php echo $mark; ?>">
		                    	<?php echo wp_kses_data( $post['note'] ); ?>
		                	</mark>
		                </td>
		            </tr>
		            <?php
	            }
	        ?>
		</tbody>

		<thead>
			<tr>
				<th colspan="2"><?php _e( 'Plugins', 'carton' ); ?></th>
			</tr>
		</thead>

		<tbody>
         	<tr>
         		<td><?php _e( 'Installed Plugins','carton' ); ?>:</td>
         		<td><?php
         			$active_plugins = (array) get_option( 'active_plugins', array() );

         			if ( is_multisite() )
						$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

					$ctn_plugins = array();

					foreach ( $active_plugins as $plugin ) {

						$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
						$dirname        = dirname( $plugin );
						$version_string = '';

						if ( ! empty( $plugin_data['Name'] ) ) {

							if ( strstr( $dirname, 'carton' ) ) {

								if ( false === ( $version_data = get_transient( $plugin . '_version_data' ) ) ) {
									$changelog = wp_remote_get( 'http://www.carton-ecommerce.com/changelogs/extensions/' . $dirname . '/changelog.txt' );
									$cl_lines  = explode( "\n", wp_remote_retrieve_body( $changelog ) );
									if ( ! empty( $cl_lines ) ) {
										foreach ( $cl_lines as $line_num => $cl_line ) {
											if ( preg_match( '/^[0-9]/', $cl_line ) ) {

												$date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
												$version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
												$update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
												$version_data = array( 'date' => $date , 'version' => $version , 'update' => $update , 'changelog' => $changelog );
												set_transient( $plugin . '_version_data', $version_data , 60*60*12 );
												break;
											}
										}
									}
								}

								if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '!=' ) )
									$version_string = ' &ndash; <strong style="color:red;">' . $version_data['version'] . ' ' . __( 'is available', 'carton' ) . '</strong>';
							}

							$ctn_plugins[] = $plugin_data['Name'] . ' ' . __( 'by', 'carton' ) . ' ' . $plugin_data['Author'] . ' ' . __( 'version', 'carton' ) . ' ' . $plugin_data['Version'] . $version_string;

						}
					}

					if ( sizeof( $ctn_plugins ) == 0 )
						echo '-';
					else
						echo implode( ', <br/>', $ctn_plugins );

         		?></td>
         	</tr>
		</tbody>

		<thead>
			<tr>
				<th colspan="2"><?php _e( 'Settings', 'carton' ); ?></th>
			</tr>
		</thead>

		<tbody>

            <tr>
                <td><?php _e( 'Force SSL','carton' ); ?>:</td>
				<td><?php echo get_option( 'carton_force_ssl_checkout' ) === 'yes' ? '<mark class="yes">'.__( 'Yes', 'carton' ).'</mark>' : '<mark class="no">'.__( 'No', 'carton' ).'</mark>'; ?></td>
            </tr>
		</tbody>

		<thead>
			<tr>
				<th colspan="2"><?php _e( 'WC Pages', 'carton' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
				$check_pages = array(
					__( 'Shop Base', 'carton' ) => array(
							'option' => 'carton_shop_page_id',
							'shortcode' => ''
						),
					__( 'Cart', 'carton' ) => array(
							'option' => 'carton_cart_page_id',
							'shortcode' => '[carton_cart]'
						),
					__( 'Checkout', 'carton' ) => array(
							'option' => 'carton_checkout_page_id',
							'shortcode' => '[carton_checkout]'
						),
					__( 'Pay', 'carton' ) => array(
							'option' => 'carton_pay_page_id',
							'shortcode' => '[carton_pay]'
						),
					__( 'Thanks', 'carton' ) => array(
							'option' => 'carton_thanks_page_id',
							'shortcode' => '[carton_thankyou]'
						),
					__( 'My Account', 'carton' ) => array(
							'option' => 'carton_myaccount_page_id',
							'shortcode' => '[carton_my_account]'
						),
					__( 'Edit Address', 'carton' ) => array(
							'option' => 'carton_edit_address_page_id',
							'shortcode' => '[carton_edit_address]'
						),
					__( 'View Order', 'carton' ) => array(
							'option' => 'carton_view_order_page_id',
							'shortcode' => '[carton_view_order]'
						),
					__( 'Change Password', 'carton' ) => array(
							'option' => 'carton_change_password_page_id',
							'shortcode' => '[carton_change_password]'
						),
					__( 'Lost Password', 'carton' ) => array(
							'option' => 'carton_lost_password_page_id',
							'shortcode' => '[carton_lost_password]'
						)
				);

				$alt = 1;

				foreach ( $check_pages as $page_name => $values ) {

					if ( $alt == 1 ) echo '<tr>'; else echo '<tr>';

					echo '<td>' . esc_html( $page_name ) . ':</td><td>';

					$error = false;

					$page_id = get_option( $values['option'] );

					// Page ID check
					if ( ! $page_id ) {
						echo '<mark class="error">' . __( 'Page not set', 'carton' ) . '</mark>';
						$error = true;
					} else {

						// Shortcode check
						if ( $values['shortcode'] ) {
							$page = get_post( $page_id );

							if ( empty( $page ) ) {

								echo '<mark class="error">' . sprintf( __( 'Page does not exist', 'carton' ) ) . '</mark>';
								$error = true;

							} else if ( ! strstr( $page->post_content, $values['shortcode'] ) ) {

								echo '<mark class="error">' . sprintf( __( 'Page does not contain the shortcode: %s', 'carton' ), $values['shortcode'] ) . '</mark>';
								$error = true;

							}
						}

					}

					if ( ! $error ) echo '<mark class="yes">#' . absint( $page_id ) . ' - ' . str_replace( home_url(), '', get_permalink( $page_id ) ) . '</mark>';

					echo '</td></tr>';

					$alt = $alt * -1;
				}
			?>
		</tbody>

		<thead>
			<tr>
				<th colspan="2"><?php _e( 'WC Taxonomies', 'carton' ); ?></th>
			</tr>
		</thead>

		<tbody>
            <tr>
                <td><?php _e( 'Order Statuses', 'carton' ); ?>:</td>
                <td><?php
                	$display_terms = array();
                	$terms = get_terms( 'shop_order_status', array( 'hide_empty' => 0 ) );
                	foreach ( $terms as $term )
                		$display_terms[] = $term->name . ' (' . $term->slug . ')';
                	echo implode( ', ', array_map( 'esc_html', $display_terms ) );
                ?></td>
            </tr>
            <tr>
                <td><?php _e( 'Product Types', 'carton' ); ?>:</td>
                <td><?php
                	$display_terms = array();
                	$terms = get_terms( 'product_type', array( 'hide_empty' => 0 ) );
                	foreach ( $terms as $term )
                		$display_terms[] = $term->name . ' (' . $term->slug . ')';
                	echo implode( ', ', array_map( 'esc_html', $display_terms ) );
                ?></td>
            </tr>
		</tbody>

		<thead>
			<tr>
				<th colspan="2"><?php _e( 'Templates', 'carton' ); ?></th>
			</tr>
		</thead>

		<tbody>
            <tr>
                <td><?php _e( 'Template Overrides', 'carton' ); ?>:</td>
                <td><?php

					$template_path = $carton->plugin_path() . '/templates/';
					$found_files   = array();
					$files         = carton_scan_template_files( $template_path );

					foreach ( $files as $file ) {
						if ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
							$found_files[] = '/' . $file;
						} elseif( file_exists( get_stylesheet_directory() . '/carton/' . $file ) ) {
							$found_files[] = '/carton/' . $file;
						}
					}

					if ( $found_files ) {
						echo implode( ', <br/>', $found_files );
					} else {
						_e( 'No core overrides present in theme.', 'carton' );
					}

                ?></td>
            </tr>
		</tbody>

	</table>
	<script type="text/javascript">

		jQuery.ctn_strPad = function(i,l,s) {
			var o = i.toString();
			if (!s) { s = '0'; }
			while (o.length < l) {
				o = o + s;
			}
			return o;
		};

		jQuery('a.debug-report').click(function(){

			var report = "";

			jQuery('.ctn_status_table thead, .ctn_status_table tbody').each(function(){

				$this = jQuery( this );

				if ( $this.is('thead') ) {

					report = report + "\n### " + jQuery.trim( $this.text() ) + " ###\n\n";

				} else {

					jQuery('tr', $this).each(function(){

						$this = jQuery( this );

						name = jQuery.ctn_strPad( jQuery.trim( $this.find('td:eq(0)').text() ), 25, ' ' );
						value = jQuery.trim( $this.find('td:eq(1)').text() );

						report = report + '' + name + value + "\n\n";
					});

				}
			} );

			var blob = new Blob( [report] );

			jQuery(this).attr( 'href', window.URL.createObjectURL( blob ) );

      		return true;
		});

	</script>
	<?php
}

/**
 * carton_scan_template_files function.
 *
 * @access public
 * @param mixed $template_path
 * @return void
 */
function carton_scan_template_files( $template_path ) {
	$files         = scandir( $template_path );
	$result        = array();
	if ( $files ) {
		foreach ( $files as $key => $value ) {
			if ( ! in_array( $value, array( ".",".." ) ) ) {
				if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
					$sub_files = carton_scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
					foreach ( $sub_files as $sub_file ) {
						$result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
					}
				} else {
					$result[] = $value;
				}
			}
		}
	}
	return $result;
}

/**
 * carton_status_tools function.
 *
 * @access public
 * @return void
 */
function carton_status_tools() {
	global $carton, $wpdb;

	$tools = apply_filters( 'carton_debug_tools', array(
		'clear_transients' => array(
			'name'		=> __( 'WC Transients','carton'),
			'button'	=> __('Clear transients','carton'),
			'desc'		=> __( 'This tool will clear the product/shop transients cache.', 'carton' ),
		),
		'clear_expired_transients' => array(
			'name'		=> __( 'Expired Transients','carton'),
			'button'	=> __('Clear expired transients','carton'),
			'desc'		=> __( 'This tool will clear ALL expired transients from Wordpress.', 'carton' ),
		),
		'recount_terms' => array(
			'name'		=> __('Term counts','carton'),
			'button'	=> __('Recount terms','carton'),
			'desc'		=> __( 'This tool will recount product terms - useful when changing your settings in a way which hides products from the catalog.', 'carton' ),
		),
		'reset_roles' => array(
			'name'		=> __('Capabilities','carton'),
			'button'	=> __('Reset capabilities','carton'),
			'desc'		=> __( 'This tool will reset the admin, customer and shop_manager roles to default. Use this if your users cannot access all of the CartoN admin pages.', 'carton' ),
		),
	) );

	if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'debug_action' ) ) {

		switch ( $_GET['action'] ) {
			case "clear_transients" :
				$carton->clear_product_transients();

				echo '<div class="updated"><p>' . __( 'Product Transients Cleared', 'carton' ) . '</p></div>';
			break;
			case "clear_expired_transients" :

				// http://w-shadow.com/blog/2012/04/17/delete-stale-transients/
				$rows = $wpdb->query( "
					DELETE
						a, b
					FROM
						{$wpdb->options} a, {$wpdb->options} b
					WHERE
						a.option_name LIKE '_transient_%' AND
						a.option_name NOT LIKE '_transient_timeout_%' AND
						b.option_name = CONCAT(
							'_transient_timeout_',
							SUBSTRING(
								a.option_name,
								CHAR_LENGTH('_transient_') + 1
							)
						)
						AND b.option_value < UNIX_TIMESTAMP()
				" );

				$rows2 = $wpdb->query( "
					DELETE
						a, b
					FROM
						{$wpdb->options} a, {$wpdb->options} b
					WHERE
						a.option_name LIKE '_site_transient_%' AND
						a.option_name NOT LIKE '_site_transient_timeout_%' AND
						b.option_name = CONCAT(
							'_site_transient_timeout_',
							SUBSTRING(
								a.option_name,
								CHAR_LENGTH('_site_transient_') + 1
							)
						)
						AND b.option_value < UNIX_TIMESTAMP()
				" );

				echo '<div class="updated"><p>' . sprintf( __( '%d Transients Rows Cleared', 'carton' ), $rows + $rows2 ) . '</p></div>';

			break;
			case "reset_roles" :
				// Remove then re-add caps and roles
				carton_remove_roles();
				carton_init_roles();

				echo '<div class="updated"><p>' . __( 'Roles successfully reset', 'carton' ) . '</p></div>';
			break;
			case "recount_terms" :

				$product_cats = get_terms( 'product_cat', array( 'hide_empty' => false, 'fields' => 'id=>parent' ) );

				_carton_term_recount( $product_cats, get_taxonomy( 'product_cat' ), false, false );

				$product_tags = get_terms( 'product_tag', array( 'hide_empty' => false, 'fields' => 'id=>parent' ) );

				_carton_term_recount( $product_cats, get_taxonomy( 'product_tag' ), false, false );

				echo '<div class="updated"><p>' . __( 'Terms successfully recounted', 'carton' ) . '</p></div>';
			break;
			default:
				$action = esc_attr( $_GET['action'] );
				if( isset( $tools[ $action ]['callback'] ) ) {
					$callback = $tools[ $action ]['callback'];
					$return = call_user_func( $callback );
					if( $return === false ) {
						if( is_array( $callback ) ) {
							echo '<div class="error"><p>' . sprintf( __( 'There was an error calling %s::%s', 'carton' ), get_class( $callback[0] ), $callback[1] ) . '</p></div>';

						} else {
							echo '<div class="error"><p>' . sprintf( __( 'There was an error calling %s', 'carton' ), $callback ) . '</p></div>';
						}
					}
				}
		}
	}

	?>
	<table class="ctn_status_table widefat" cellspacing="0">

        <thead class="tools">
			<tr>
				<th colspan="2"><?php _e( 'Tools', 'carton' ); ?></th>
			</tr>
		</thead>

		<tbody class="tools">
		<?php foreach($tools as $action => $tool) { ?>
			<tr>
                <td><?php echo esc_html( $tool['name'] ); ?></td>
                <td>
                	<p>
                    	<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=carton_status&tab=tools&action=' . $action ), 'debug_action' ); ?>" class="button"><?php echo esc_html( $tool['button'] ); ?></a>
                    	<span class="description"><?php echo wp_kses_post( $tool['desc'] ); ?></span>
                	</p>
                </td>
            </tr>
		<?php } ?>
		</tbody>
	</table>
	<?php
}
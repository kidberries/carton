<?php
/**
 * CartoN Admin Functions
 *
 * Hooked-in functions for CartoN related events in admin.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Checks which method we're using to serve downloads
 *
 * If using force or x-sendfile, this ensures the .htaccess is in place
 *
 * @access public
 * @return void
 */
function carton_check_download_folder_protection() {
	$upload_dir 		= wp_upload_dir();
	$downloads_url 		= $upload_dir['basedir'] . '/carton_uploads';
	$download_method	= get_option('carton_file_download_method');

	if ( $download_method == 'redirect' ) {

		// Redirect method - don't protect
		if ( file_exists( $downloads_url . '/.htaccess' ) )
			unlink( $downloads_url . '/.htaccess' );

	} else {

		// Force method - protect, add rules to the htaccess file
		if ( ! file_exists( $downloads_url . '/.htaccess' ) ) {
			if ( $file_handle = @fopen( $downloads_url . '/.htaccess', 'w' ) ) {
				fwrite( $file_handle, 'deny from all' );
				fclose( $file_handle );
			}
		}
	}

	flush_rewrite_rules();
}


/**
 * Protect downlodas from ms-files.php in multisite
 *
 * @access public
 * @param mixed $rewrite
 * @return string
 */
function carton_ms_protect_download_rewite_rules( $rewrite ) {
    global $wp_rewrite;

    $download_method	= get_option('carton_file_download_method');

    if (!is_multisite() || $download_method=='redirect') return $rewrite;

	$rule  = "\n# CartoN Rules - Protect Files from ms-files.php\n\n";
	$rule .= "<IfModule mod_rewrite.c>\n";
	$rule .= "RewriteEngine On\n";
	$rule .= "RewriteCond %{QUERY_STRING} file=carton_uploads/ [NC]\n";
	$rule .= "RewriteRule /ms-files.php$ - [F]\n";
	$rule .= "</IfModule>\n\n";

	return $rule . $rewrite;
}


/**
 * Removes variations etc belonging to a deleted post, and clears transients
 *
 * @access public
 * @param mixed $id ID of post being deleted
 * @return void
 */
function carton_delete_post( $id ) {
	global $carton, $wpdb;

	if ( ! current_user_can( 'delete_posts' ) )
		return;

	if ( $id > 0 ) {

		$post_type = get_post_type( $id );

		switch( $post_type ) {
			case 'product' :

				if ( $child_product_variations =& get_children( 'post_parent=' . $id . '&post_type=product_variation' ) )
					if ( $child_product_variations )
						foreach ( $child_product_variations as $child )
							wp_delete_post( $child->ID, true );

				if ( $child_products =& get_children( 'post_parent=' . $id . '&post_type=product' ) )
					if ( $child_products )
						foreach ( $child_products as $child ) {
							$child_post = array();
							$child_post['ID'] = $child->ID;
							$child_post['post_parent'] = 0;
							wp_update_post( $child_post );
						}

				$carton->clear_product_transients();

			break;
			case 'product_variation' :

				$carton->clear_product_transients();

			break;
		}
	}
}

/**
 * carton_trash_post function.
 *
 * @access public
 * @param mixed $id
 * @return void
 */
function carton_trash_post( $id ) {
	if ( $id > 0 ) {

		$post_type = get_post_type( $id );

		if ( 'shop_order' == $post_type ) {

			// Delete count - meta doesn't work on trashed posts
			$user_id = get_post_meta( $id, '_customer_user', true );

			if ( $user_id > 0 ) {
				delete_user_meta( $user_id, '_order_count' );
			}

			delete_transient( 'carton_processing_order_count' );
		}

	}
}

/**
 * carton_untrash_post function.
 *
 * @access public
 * @param mixed $id
 * @return void
 */
function carton_untrash_post( $id ) {
	if ( $id > 0 ) {

		$post_type = get_post_type( $id );

		if ( 'shop_order' == $post_type ) {

			// Delete count - meta doesn't work on trashed posts
			$user_id = get_post_meta( $id, '_customer_user', true );

			if ( $user_id > 0 ) {
				delete_user_meta( $user_id, '_order_count' );
			}

			delete_transient( 'carton_processing_order_count' );
		}

	}
}

/**
 * Preview Emails in WP admin
 *
 * @access public
 * @return void
 */
function carton_preview_emails() {
	if ( isset( $_GET['preview_carton_mail'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'preview-mail') )
			die( 'Security check' );

		global $carton, $email_heading;

		$mailer = $carton->mailer();

		$email_heading = __( 'Order Received', 'carton' );

		$message  = wpautop( __( 'Thank you, we are now processing your order. Your order\'s details are below.', 'carton' ) );

		$message .= '<h2>' . __( 'Order:', 'carton' ) . ' ' . '#1000</h2>';

		$message .= '
		<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee; margin: 0 0 20px" border="1" bordercolor="#eee">
			<thead>
				<tr>
					<th scope="col" style="text-align:left; border: 1px solid #eee;">' . __( 'Product', 'carton' ) . '</th>
					<th scope="col" style="text-align:left; border: 1px solid #eee;">' . __( 'Quantity', 'carton' ) . '</th>
					<th scope="col" style="text-align:left; border: 1px solid #eee;">' . __( 'Price', 'carton' ) . '</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>An awesome product</td>
					<td>1</td>
					<td>$9.99</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2">' . __( 'Order total:', 'carton' ) . '</td>
					<td>$9.99</td>
				</tr>
			</tfoot>
		</table>';

		$message .= '<h2>' . __( 'Customer details', 'carton' ) . '</h2>';

		$message .= '
		<table cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
			<tr>
				<td valign="top" width="50%">
					<h3>' . __( 'Billing address', 'carton' ) . '</h3>
					<p>Some Guy
					1 infinite loop
					Cupertino
					CA 95014</p>
				</td>
				<td valign="top" width="50%">
					<h3>' . __( 'Shipping address', 'carton' ) . '</h3>
					<p>Some Guy
					1 infinite loop
					Cupertino
					CA 95014</p>
				</td>
			</tr>
		</table>';

		echo $mailer->wrap_message( $email_heading, $message );

		exit;

	}
}


/**
 * Prevent non-admin access to backend
 *
 * @access public
 * @return void
 */
function carton_prevent_admin_access() {
	if ( get_option('carton_lock_down_admin') == 'yes' && ! is_ajax() && ! ( current_user_can('edit_posts') || current_user_can('manage_carton') ) ) {
		wp_safe_redirect(get_permalink(carton_get_page_id('myaccount')));
		exit;
	}
}

/**
 * Filter the directory for uploads.
 *
 * @access public
 * @param mixed $pathdata
 * @return void
 */
function carton_downloads_upload_dir( $pathdata ) {

	// Change upload dir
	if ( isset( $_POST['type'] ) && $_POST['type'] == 'downloadable_product' ) {
		// Uploading a downloadable file
		$subdir = '/carton_uploads'.$pathdata['subdir'];
	 	$pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
	 	$pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
		$pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
		return $pathdata;
	}

	return $pathdata;
}


/**
 * Run a filter when uploading a downloadable product.
 *
 * @access public
 * @return void
 */
function carton_media_upload_downloadable_product() {
	do_action('media_upload_file');
}


/**
 * Add a button for shortcodes to the WP editor.
 *
 * @access public
 * @return void
 */
function carton_add_shortcode_button() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
	if ( get_user_option('rich_editing') == 'true') :
		add_filter('mce_external_plugins', 'carton_add_shortcode_tinymce_plugin');
		add_filter('mce_buttons', 'carton_register_shortcode_button');
	endif;
}


/**
 * carton_add_tinymce_lang function.
 *
 * @access public
 * @param mixed $arr
 * @return void
 */
function carton_add_tinymce_lang( $arr ) {
	global $carton;
    $arr[] = $carton->plugin_path() . '/assets/js/admin/editor_plugin_lang.php';
    return $arr;
}

add_filter( 'mce_external_languages', 'carton_add_tinymce_lang', 10, 1 );


/**
 * Register the shortcode button.
 *
 * @access public
 * @param mixed $buttons
 * @return array
 */
function carton_register_shortcode_button($buttons) {
	array_push($buttons, "|", "carton_shortcodes_button");
	return $buttons;
}


/**
 * Add the shortcode button to TinyMCE
 *
 * @access public
 * @param mixed $plugin_array
 * @return array
 */
function carton_add_shortcode_tinymce_plugin($plugin_array) {
	global $carton;
	$plugin_array['CartoNShortcodes'] = $carton->plugin_url() . '/assets/js/admin/editor_plugin.js';
	return $plugin_array;
}


/**
 * Force TinyMCE to refresh.
 *
 * @access public
 * @param mixed $ver
 * @return int
 */
function carton_refresh_mce( $ver ) {
	$ver += 3;
	return $ver;
}


/**
 * Order term when created (put in position 0).
 *
 * @access public
 * @param mixed $term_id
 * @param mixed $tt_id
 * @param mixed $taxonomy
 * @return void
 */
function carton_create_term( $term_id, $tt_id = '', $taxonomy = '' ) {

	if ( ! $taxonomy == 'product_cat' && ! strstr( $taxonomy, 'pa_' ) )
		return;

	$meta_name = strstr( $taxonomy, 'pa_' ) ? 'order_' . esc_attr( $taxonomy ) : 'order';

	update_carton_term_meta( $term_id, $meta_name, 0 );
}


/**
 * When a term is deleted, delete its meta.
 *
 * @access public
 * @param mixed $term_id
 * @return void
 */
function carton_delete_term( $term_id ) {

	$term_id = (int) $term_id;

	if ( ! $term_id )
		return;

	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->carton_termmeta} WHERE carton_term_id = " . $term_id );
}


/**
 * Generate CSS from the less file when changing colours.
 *
 * @access public
 * @return void
 */
function carton_compile_less_styles() {
	global $carton;

	$colors 		= array_map( 'esc_attr', (array) get_option( 'carton_frontend_css_colors' ) );
	$base_file		= $carton->plugin_path() . '/assets/css/carton-base.less';
	$less_file		= $carton->plugin_path() . '/assets/css/carton.less';
	$css_file		= $carton->plugin_path() . '/assets/css/carton.css';

	// Write less file
	if ( is_writable( $base_file ) && is_writable( $css_file ) ) {

		// Colours changed - recompile less
		if ( ! class_exists( 'lessc' ) )
			include_once('includes/class-lessc.php');
		if ( ! class_exists( 'cssmin' ) )
			include_once('includes/class-cssmin.php');

		try {
			// Set default if colours not set
			if ( ! $colors['primary'] ) $colors['primary'] = '#ad74a2';
			if ( ! $colors['secondary'] ) $colors['secondary'] = '#f7f6f7';
			if ( ! $colors['highlight'] ) $colors['highlight'] = '#85ad74';
			if ( ! $colors['content_bg'] ) $colors['content_bg'] = '#ffffff';
			if ( ! $colors['subtext'] ) $colors['subtext'] = '#777777';

			// Write new color to base file
			$color_rules = "
@primary: 		" . $colors['primary'] . ";
@primarytext: 	" . carton_light_or_dark( $colors['primary'], 'desaturate(darken(@primary,50%),18%)', 'desaturate(lighten(@primary,50%),18%)' ) . ";

@secondary: 	" . $colors['secondary'] . ";
@secondarytext: " . carton_light_or_dark( $colors['secondary'], 'desaturate(darken(@secondary,60%),18%)', 'desaturate(lighten(@secondary,60%),18%)' ) . ";

@highlight: 	" . $colors['highlight'] . ";
@highlightext:	" . carton_light_or_dark( $colors['highlight'], 'desaturate(darken(@highlight,60%),18%)', 'desaturate(lighten(@highlight,60%),18%)' ) . ";

@contentbg:		" . $colors['content_bg'] . ";

@subtext:		" . $colors['subtext'] . ";
			";

			file_put_contents( $base_file, $color_rules );

		    $less 			= new lessc( $less_file );
			$compiled_css 	= $less->parse();

		    $compiled_css = CssMin::minify( $compiled_css );

		    if ( $compiled_css )
		    	file_put_contents( $css_file, $compiled_css );

		} catch ( exception $ex ) {
			wp_die( __( 'Could not compile carton.less:', 'carton' ) . ' ' . $ex->getMessage() );
		}
	}
}


/**
 * Add extra bulk action options to mark orders as complete or processing
 *
 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031
 *
 * @access public
 * @return void
 */
function carton_bulk_admin_footer() {
	global $post_type;

	if ( 'shop_order' == $post_type ) {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('<option>').val('mark_processing').text('<?php _e( 'Mark processing', 'carton' )?>').appendTo("select[name='action']");
			jQuery('<option>').val('mark_processing').text('<?php _e( 'Mark processing', 'carton' )?>').appendTo("select[name='action2']");

			jQuery('<option>').val('mark_completed').text('<?php _e( 'Mark completed', 'carton' )?>').appendTo("select[name='action']");
			jQuery('<option>').val('mark_completed').text('<?php _e( 'Mark completed', 'carton' )?>').appendTo("select[name='action2']");
		});
		</script>
		<?php
	}
}


/**
 * Process the new bulk actions for changing order status
 *
 * @access public
 * @return void
 */
function carton_order_bulk_action() {
	$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
	$action = $wp_list_table->current_action();

	switch ( $action ) {
		case 'mark_completed':
			$new_status = 'completed';
			$report_action = 'marked_completed';
			break;
		case 'mark_processing':
			$new_status = 'processing';
			$report_action = 'marked_processing';
			break;
		default:
			return;
	}

	$changed = 0;

	$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

	foreach( $post_ids as $post_id ) {
		$order = new CTN_Order( $post_id );
		$order->update_status( $new_status, __( 'Order status changed by bulk edit:', 'carton' ) );
		$changed++;
	}

	$sendback = add_query_arg( array( 'post_type' => 'shop_order', $report_action => $changed, 'ids' => join( ',', $post_ids ) ), '' );
	wp_redirect( $sendback );
	exit();
}


/**
 * Show confirmation message that order status changed for number of orders
 *
 * @access public
 * @return void
 */
function carton_order_bulk_admin_notices() {
	global $post_type, $pagenow;

	if ( isset( $_REQUEST['marked_completed'] ) || isset( $_REQUEST['marked_processing'] ) ) {
		$number = isset( $_REQUEST['marked_processing'] ) ? absint( $_REQUEST['marked_processing'] ) : absint( $_REQUEST['marked_completed'] );

		if ( 'edit.php' == $pagenow && 'shop_order' == $post_type ) {
			$message = sprintf( _n( 'Order status changed.', '%s order statuses changed.', $number ), number_format_i18n( $number ) );
			echo '<div class="updated"><p>' . $message . '</p></div>';
		}
	}
}
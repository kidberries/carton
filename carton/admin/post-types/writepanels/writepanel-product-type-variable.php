<?php
/**
 * Variable Product Type
 *
 * Functions specific to variable products (for the write panels).
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/WritePanels
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Display the variation tab in product data.
 *
 * @access public
 * @return void
 */
function variable_product_type_options_tab() {
	?>
	<li class="variations_tab show_if_variable variation_options"><a href="#variable_product_options" title="<?php _e( 'Variations for variable products are defined here.', 'carton' ); ?>"><?php _e( 'Variations', 'carton' ); ?></a></li>
	<?php
}

add_action( 'carton_product_write_panel_tabs', 'variable_product_type_options_tab' );


/**
 * Show the variable product options.
 *
 * @access public
 * @return void
 */
function variable_product_type_options() {
	global $post, $carton;

	$attributes = maybe_unserialize( get_post_meta( $post->ID, '_product_attributes', true ) );

	// See if any are set
	$variation_attribute_found = false;
	if ( $attributes ) {
		foreach( $attributes as $attribute ) {
			if ( isset( $attribute['is_variation'] ) || isset( $attribute['is_changeable'] ) ) {
				$variation_attribute_found = true;
				break;
			}
		}
	}

	// Get tax classes
	$tax_classes = array_filter( array_map('trim', explode( "\n", get_option( 'carton_tax_classes' ) ) ) );
	$tax_class_options = array();
	$tax_class_options['parent'] = __( 'Same as parent', 'carton' );
	$tax_class_options[''] = __( 'Standard', 'carton' );
	if ( $tax_classes )
		foreach ( $tax_classes as $class )
			$tax_class_options[ sanitize_title( $class ) ] = esc_attr( $class );
	?>
	<div id="variable_product_options" class="panel wc-metaboxes-wrapper"><div id="variable_product_options_inner">

		<?php if ( ! $variation_attribute_found ) : ?>

			<div id="message" class="inline carton-message">
				<div class="squeezer">
					<h4><?php _e( 'Before adding variations, add and save some attributes on the <strong>Attributes</strong> tab.', 'carton' ); ?></h4>

					<p class="submit"><a class="button-primary" href="http://docs.carton-ecommerce.com/document/product-variations/" target="_blank"><?php _e( 'Learn more', 'carton' ); ?></a></p>
				</div>
			</div>

		<?php else : ?>

			<p class="toolbar">
				<a href="#" class="close_all"><?php _e( 'Close all', 'carton' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'carton' ); ?></a>
				<strong><?php _e( 'Bulk edit:', 'carton' ); ?></strong>
				<select id="field_to_edit">
					<option value="toggle_enabled"><?php _e( 'Toggle &quot;Enabled&quot;', 'carton' ); ?></option>
					<option value="toggle_downloadable"><?php _e( 'Toggle &quot;Downloadable&quot;', 'carton' ); ?></option>
					<option value="toggle_virtual"><?php _e( 'Toggle &quot;Virtual&quot;', 'carton' ); ?></option>
					<option value="delete_all"><?php _e( 'Delete all variations', 'carton' ); ?></option>
					<option value="variable_purchase_price"><?php _e( 'Purchase Prices', 'carton' ); ?></option>
					<option value="variable_regular_price"><?php _e( 'Prices', 'carton' ); ?></option>
					<option value="variable_sale_price"><?php _e( 'Sale prices', 'carton' ); ?></option>
					<option value="variable_stock"><?php _e( 'Stock', 'carton' ); ?></option>
					<option value="variable_weight"><?php _e( 'Weight', 'carton' ); ?></option>
					<option value="variable_length"><?php _e( 'Length', 'carton' ); ?></option>
					<option value="variable_width"><?php _e( 'Width', 'carton' ); ?></option>
					<option value="variable_height"><?php _e( 'Height', 'carton' ); ?></option>
					<option value="variable_file_paths" rel="textarea"><?php _e( 'File Path', 'carton' ); ?></option>
					<option value="variable_download_limit"><?php _e( 'Download limit', 'carton' ); ?></option>
					<option value="variable_download_expiry"><?php _e( 'Download Expiry', 'carton' ); ?></option>
					<option value="" disabled="1">----</option>
					<?php
					foreach( $attributes as $attribute ) {
						if ( isset( $attribute['is_changeable'] ) && $attribute['is_changeable'] ) {
							$attribute_values = array();
							if ( $attribute['is_taxonomy'] ) {
								$post_terms = wp_get_post_terms( $post->ID, $attribute['name'] );
								foreach ( $post_terms as $term ) {
									$attribute_values[] = esc_attr( "'" . $term->slug . "'" );
								}
							} else {
								$options = array_map( 'trim', explode( '|', $attribute['value'] ) );
								foreach ( $options as $option ) {
									$attribute_values[] = esc_attr( "'" . sanitize_title( $option ) . "'"  );
								}
							}
					?>
							<option value="attribute_<?php echo sanitize_title( $attribute['name'] );?>" data-values="<?php echo implode(', ', $attribute_values); ?>" rel="select"><?php _e( 'Change', 'carton' ); ?> <?php echo esc_html( $carton->attribute_label( $attribute['name'] ) ); ?></option>
					<?php
						}
					}
					?>
					<?php do_action( 'carton_variable_product_bulk_edit_actions' ); ?>
				</select>
				<a class="button bulk_edit"><?php _e( 'Go', 'carton' ); ?></a>
			</p>

			<div class="carton_variations wc-metaboxes">
				<?php
				// Get parent data
				$parent_data = array(
					'id'		=> $post->ID,
					'attributes' => $attributes,
					'tax_class_options' => $tax_class_options,
					'sku' 		=> get_post_meta( $post->ID, '_sku', true ),
					'weight' 	=> get_post_meta( $post->ID, '_weight', true ),
					'length' 	=> get_post_meta( $post->ID, '_length', true ),
					'width' 	=> get_post_meta( $post->ID, '_width', true ),
					'height' 	=> get_post_meta( $post->ID, '_height', true ),
					'tax_class' => get_post_meta( $post->ID, '_tax_class', true )
				);

				if ( ! $parent_data['weight'] )
					$parent_data['weight'] = '0.00';

				if ( ! $parent_data['length'] )
					$parent_data['length'] = '0';

				if ( ! $parent_data['width'] )
					$parent_data['width'] = '0';

				if ( ! $parent_data['height'] )
					$parent_data['height'] = '0';

				// Get variations
				$args = array(
					'post_type'		=> 'product_variation',
					'post_status' 	=> array( 'private', 'publish' ),
					'numberposts' 	=> -1,
					'orderby' 		=> 'menu_order',
					'order' 		=> 'asc',
					'post_parent' 	=> $post->ID
				);
				$variations = get_posts( $args );
				$loop = 0;
				if ( $variations ) foreach ( $variations as $variation ) {

					$variation_id 			= absint( $variation->ID );
					$variation_post_status 	= esc_attr( $variation->post_status );
					$variation_data 		= get_post_meta( $variation_id );
					$variation_data['variation_post_id'] = $variation_id;

					// Grab shipping classes
					$shipping_classes = get_the_terms( $variation_id, 'product_shipping_class' );
					$shipping_class = ( $shipping_classes && ! is_wp_error( $shipping_classes ) ) ? current( $shipping_classes )->term_id : '';

					$variation_fields = array(
						'_sku',
						'_stock',
						'_price',
						'_regular_price',
						'_purchase_price',
						'_sale_price',
						'_weight',
						'_length',
						'_width',
						'_height',
						'_tax_class',
						'_download_limit',
						'_download_expiry',
						'_file_paths',
						'_downloadable',
						'_virtual',
						'_thumbnail_id',
						'_sale_price_dates_from',
						'_sale_price_dates_to'
					);

					foreach ( $variation_fields as $field )
						$$field = isset( $variation_data[ $field ][0] ) ? $variation_data[ $field ][0] : '';

					// Price backwards compat
					if ( $_regular_price == '' && $_price )
						$_regular_price = $_price;

					// Get image
					$image = '';
					$image_id = absint( $_thumbnail_id );
					if ( $image_id )
						$image = wp_get_attachment_url( $image_id );

					// Format file paths
					$_file_paths = maybe_unserialize( $_file_paths );
					if ( is_array( $_file_paths ) )
						$_file_paths = implode( "\n", $_file_paths );

					include( 'variation-admin-html.php' );

					$loop++;
				}
				?>
			</div>

			<p class="toolbar">

				<button type="button" class="button button-primary add_variation" <?php disabled( $variation_attribute_found, false ); ?>><?php _e( 'Add Variation', 'carton' ); ?></button>

				<button type="button" class="button link_all_variations" <?php disabled( $variation_attribute_found, false ); ?>><?php _e( 'Link all variations', 'carton' ); ?></button>

				<strong><?php _e( 'Default selections:', 'carton' ); ?></strong>
				<?php
					$default_attributes = maybe_unserialize( get_post_meta( $post->ID, '_default_attributes', true ) );
					foreach ( $attributes as $attribute ) {

						// Only deal with attributes that are variations
						if ( ! ($attribute['is_variation'] || $attribute['is_changeable']) )
							continue;

						// Get current value for variation (if set)
						$variation_selected_value = isset( $default_attributes[ sanitize_title( $attribute['name'] ) ] ) ? $default_attributes[ sanitize_title( $attribute['name'] ) ] : '';

						// Name will be something like attribute_pa_color
						echo '<select name="default_attribute_' . sanitize_title( $attribute['name'] ) . '"><option value="">' . __( 'No default', 'carton' ) . ' ' . esc_html( $carton->attribute_label( $attribute['name'] ) ) . '&hellip;</option>';

						// Get terms for attribute taxonomy or value if its a custom attribute
						if ( $attribute['is_taxonomy'] ) {

							$post_terms = wp_get_post_terms( $post->ID, $attribute['name'] );

							foreach ( $post_terms as $term )
								echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . apply_filters( 'carton_variation_option_name', esc_html( $term->name ) ) . '</option>';

						} else {

							$options = array_map( 'trim', explode( '|', $attribute['value'] ) );

							foreach ( $options as $option )
								echo '<option ' . selected( sanitize_title( $variation_selected_value ), sanitize_title( $option ), false ) . ' value="' . esc_attr( sanitize_title( $option ) ) . '">' . esc_html( apply_filters( 'carton_variation_option_name', $option ) )  . '</option>';

						}

						echo '</select>';
					}
				?>
			</p>

		<?php endif; ?>
	</div></div>
	<?php
	/**
	 * Product Type Javascript
	 */
	ob_start();
	?>
	jQuery(function(){

		var variation_sortable_options = {
			items:'.carton_variation',
			cursor:'move',
			axis:'y',
			handle: 'h3',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start:function(event,ui){
				ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
				variation_row_indexes();
			}
		};

		// Add a variation
		jQuery('#variable_product_options').on('click', 'button.add_variation', function(){

			jQuery('.carton_variations').block({ message: null, overlayCSS: { background: '#fff url(<?php echo $carton->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			var loop = jQuery('.carton_variation').size();

			var data = {
				action: 'carton_add_variation',
				post_id: <?php echo $post->ID; ?>,
				loop: loop,
				security: '<?php echo wp_create_nonce("add-variation"); ?>'
			};

			jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {

				jQuery('.carton_variations').append( response );

				jQuery(".tips").tipTip({
			    	'attribute' : 'data-tip',
			    	'fadeIn' : 50,
			    	'fadeOut' : 50
			    });

			    jQuery('input.variable_is_downloadable, input.variable_is_virtual').change();

				jQuery('.carton_variations').unblock();
				jQuery('#variable_product_options').trigger('carton_variations_added');
			});

			return false;

		});

		jQuery('#variable_product_options').on('click', 'button.link_all_variations', function(){

			var answer = confirm('<?php _e( 'Are you sure you want to link all variations? This will create a new variation for each and every possible combination of variation attributes (max 50 per run).', 'carton' ); ?>');

			if (answer) {

				jQuery('#variable_product_options').block({ message: null, overlayCSS: { background: '#fff url(<?php echo $carton->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

				var data = {
					action: 'carton_link_all_variations',
					post_id: <?php echo $post->ID; ?>,
					security: '<?php echo wp_create_nonce("link-variations"); ?>'
				};

				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {

					var count = parseInt( response );

					if (count==1) {
						alert( count + ' <?php _e( "variation added", 'carton' ); ?>');
					} else if (count==0 || count>1) {
						alert( count + ' <?php _e( "variations added", 'carton' ); ?>');
					} else {
						alert('<?php _e( "No variations added", 'carton' ); ?>');
					}

					if (count>0) {
						var this_page = window.location.toString();

						this_page = this_page.replace( 'post-new.php?', 'post.php?post=<?php echo $post->ID; ?>&action=edit&' );

						$('#variable_product_options').load( this_page + ' #variable_product_options_inner', function() {
							$('#variable_product_options').unblock();
							jQuery('#variable_product_options').trigger('carton_variations_added');
						} );
					} else {
						$('#variable_product_options').unblock();
					}

				});
			}
			return false;
		});

		jQuery('#variable_product_options').on('click', 'button.remove_variation', function(e){
			e.preventDefault();
			var answer = confirm('<?php _e( 'Are you sure you want to remove this variation?', 'carton' ); ?>');
			if (answer){

				var el = jQuery(this).parent().parent();

				var variation = jQuery(this).attr('rel');

				if (variation>0) {

					jQuery(el).block({ message: null, overlayCSS: { background: '#fff url(<?php echo $carton->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

					var data = {
						action: 'carton_remove_variation',
						variation_id: variation,
						security: '<?php echo wp_create_nonce("delete-variation"); ?>'
					};

					jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
						// Success
						jQuery(el).fadeOut('300', function(){
							jQuery(el).remove();
						});
					});

				} else {
					jQuery(el).fadeOut('300', function(){
						jQuery(el).remove();
					});
				}

			}
			return false;
		});

		jQuery('.wc-metaboxes-wrapper').on('click', 'a.bulk_edit', function(event){
			var field_to_edit = jQuery('select#field_to_edit').val();

			if ( field_to_edit == 'toggle_enabled' ) {
				var checkbox = jQuery('input[name^="variable_enabled"]');
	       		checkbox.attr('checked', !checkbox.attr('checked'));
				return false;
			}
			else if ( field_to_edit == 'toggle_downloadable' ) {
				var checkbox = jQuery('input[name^="variable_is_downloadable"]');
	       		checkbox.attr('checked', !checkbox.attr('checked'));
	       		jQuery('input.variable_is_downloadable').change();
				return false;
			}
			else if ( field_to_edit == 'toggle_virtual' ) {
				var checkbox = jQuery('input[name^="variable_is_virtual"]');
	       		checkbox.attr('checked', !checkbox.attr('checked'));
	       		jQuery('input.variable_is_virtual').change();
				return false;
			}
			else if ( field_to_edit == 'delete_all' ) {

				var answer = confirm('<?php _e( 'Are you sure you want to delete all variations? This cannot be undone.', 'carton' ); ?>');
				if (answer){

					var answer = confirm('<?php _e( 'Last warning, are you sure?', 'carton' ); ?>');

					if (answer) {

						var variation_ids = [];

						jQuery('.carton_variations .carton_variation').block({ message: null, overlayCSS: { background: '#fff url(<?php echo $carton->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

						jQuery('.carton_variations .carton_variation .remove_variation').each(function(){

							var variation = jQuery(this).attr('rel');
							if (variation>0) {
								variation_ids.push(variation);
							}
						});

						var data = {
							action: 'carton_remove_variations',
							variation_ids: variation_ids,
							security: '<?php echo wp_create_nonce("delete-variations"); ?>'
						};

						jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
							jQuery('.carton_variations .carton_variation').fadeOut('300', function(){
								jQuery('.carton_variations .carton_variation').remove();
							});
						});

					}

				}
				return false;
			}
			else {

				var input_tag = jQuery('select#field_to_edit :selected').attr('rel') ? jQuery('select#field_to_edit :selected').attr('rel') : 'input';
console.log(input_tag + '[name^="' + field_to_edit + '["]');
				var message = "<?php _e( 'Enter a value', 'carton' );?>";
				if( jQuery('select#field_to_edit :selected').attr('data-values') )
					message += ". " + "<?php _e( 'One from: ', 'carton' );?>" + jQuery('select#field_to_edit :selected').attr('data-values');
				var value = prompt( message );
				jQuery(input_tag + '[name^="' + field_to_edit + '["]').val( value ).change();
				return false;

			}
		});

		jQuery('#variable_product_options').on('change', 'input.variable_is_downloadable', function(){

			jQuery(this).closest('.carton_variation').find('.show_if_variation_downloadable').hide();

			if (jQuery(this).is(':checked')) {
				jQuery(this).closest('.carton_variation').find('.show_if_variation_downloadable').show();
			}

		});

		jQuery('#variable_product_options').on('change', 'input.variable_is_virtual', function(){

			jQuery(this).closest('.carton_variation').find('.hide_if_variation_virtual').show();

			if (jQuery(this).is(':checked')) {
				jQuery(this).closest('.carton_variation').find('.hide_if_variation_virtual').hide();
			}

		});

		jQuery('input.variable_is_downloadable, input.variable_is_virtual').change();

		// Ordering
		$('#variable_product_options').on( 'carton_variations_added', function() {
			$('.carton_variations').sortable( variation_sortable_options );
		} );

		$('.carton_variations').sortable( variation_sortable_options );

		function variation_row_indexes() {
			$('.carton_variations .carton_variation').each(function(index, el){
				$('.variation_menu_order', el).val( parseInt( $(el).index('.carton_variations .carton_variation') ) );
			});
		};

		// Uploader
		var variable_image_frame;
		var setting_variation_image_id;
		var setting_variation_image;
		var wp_media_post_id = wp.media.model.settings.post.id;

		jQuery('#variable_product_options').on('click', '.upload_image_button', function( event ) {

			var $button                = jQuery( this );
			var post_id                = $button.attr('rel');
			var $parent                = $button.closest('.upload_image');
			setting_variation_image    = $parent;
			setting_variation_image_id = post_id;

			event.preventDefault();

			if ( $button.is('.remove') ) {

				setting_variation_image.find( '.upload_image_id' ).val( '' );
				setting_variation_image.find( 'img' ).attr( 'src', '<?php echo carton_placeholder_img_src(); ?>' );
				setting_variation_image.find( '.upload_image_button' ).removeClass( 'remove' );

			} else {

				// If the media frame already exists, reopen it.
				if ( variable_image_frame ) {
					variable_image_frame.uploader.uploader.param( 'post_id', setting_variation_image_id );
					variable_image_frame.open();
					return;
				} else {
					wp.media.model.settings.post.id = setting_variation_image_id;
				}

				// Create the media frame.
				variable_image_frame = wp.media.frames.variable_image = wp.media({
					// Set the title of the modal.
					title: '<?php _e( 'Choose an image', 'carton' ); ?>',
					button: {
						text: '<?php _e( 'Set variation image', 'carton' ); ?>'
					}
				});

				// When an image is selected, run a callback.
				variable_image_frame.on( 'select', function() {

					attachment = variable_image_frame.state().get('selection').first().toJSON();

					setting_variation_image.find( '.upload_image_id' ).val( attachment.id );
					setting_variation_image.find( '.upload_image_button' ).addClass( 'remove' );
					setting_variation_image.find( 'img' ).attr( 'src', attachment.url );

					wp.media.model.settings.post.id = wp_media_post_id;
				});

				// Finally, open the modal.
				variable_image_frame.open();
			}
		});

		// Restore ID
		jQuery('a.add_media').on('click', function() {
			wp.media.model.settings.post.id = wp_media_post_id;
		} );

	});
	<?php
	$javascript = ob_get_clean();
	$carton->add_inline_js( $javascript );
}

add_action('carton_product_write_panels', 'variable_product_type_options');


/**
 * Show the variable product selector in the dropdown in admin.
 *
 * @access public
 * @param mixed $types
 * @param mixed $product_type
 * @return array
 */
function variable_product_type_selector( $types, $product_type ) {
	$types['variable'] = __( 'Variable product', 'carton' );
	return $types;
}

add_filter('product_type_selector', 'variable_product_type_selector', 1, 2);


/**
 * Save the variable product options.
 *
 * @access public
 * @param mixed $post_id
 * @return void
 */
function process_product_meta_variable( $post_id ) {
	global $carton, $wpdb;

	if ( isset( $_POST['variable_sku'] ) ) {

		$variable_post_id 					= $_POST['variable_post_id'];
		$variable_sku 						= $_POST['variable_sku'];
		$variable_weight					= $_POST['variable_weight'];
		$variable_length					= $_POST['variable_length'];
		$variable_width						= $_POST['variable_width'];
		$variable_height					= $_POST['variable_height'];
		$variable_stock 					= $_POST['variable_stock'];
		$variable_purchase_price 			= $_POST['variable_purchase_price'];
		$variable_regular_price 			= $_POST['variable_regular_price'];
		$variable_sale_price				= $_POST['variable_sale_price'];
		$upload_image_id					= $_POST['upload_image_id'];
		$variable_file_paths 				= $_POST['variable_file_paths'];
		$variable_download_limit 			= $_POST['variable_download_limit'];
		$variable_download_expiry   		= $_POST['variable_download_expiry'];
		$variable_shipping_class 			= $_POST['variable_shipping_class'];
		$variable_tax_class					= $_POST['variable_tax_class'];
		$variable_menu_order 				= $_POST['variation_menu_order'];
		$variable_sale_price_dates_from 	= $_POST['variable_sale_price_dates_from'];
		$variable_sale_price_dates_to 		= $_POST['variable_sale_price_dates_to'];

		if ( isset( $_POST['variable_enabled'] ) )
			$variable_enabled 				= $_POST['variable_enabled'];

		if ( isset( $_POST['variable_is_virtual'] ) )
			$variable_is_virtual			= $_POST['variable_is_virtual'];

		if ( isset( $_POST['variable_is_downloadable'] ) )
			$variable_is_downloadable 		= $_POST['variable_is_downloadable'];

		$attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

		$max_loop = max( array_keys( $_POST['variable_post_id'] ) );

		for ( $i = 0; $i <= $max_loop; $i ++ ) {

			if ( ! isset( $variable_post_id[ $i ] ) )
				continue;

			$variation_id = absint( $variable_post_id[ $i ] );

			// Virtal/Downloadable
			$is_virtual = isset( $variable_is_virtual[ $i ] ) ? 'yes' : 'no';
			$is_downloadable = isset( $variable_is_downloadable[ $i ] ) ? 'yes' : 'no';

			// Enabled or disabled
			$post_status = isset( $variable_enabled[ $i ] ) ? 'publish' : 'private';

			// Generate a useful post title
			$variation_post_title = sprintf( __( 'Variation #%s of %s', 'carton' ), absint( $variation_id ), esc_html( get_the_title( $post_id ) ) );

			// Update or Add post
			if ( ! $variation_id ) {

				$variation = array(
					'post_title' 	=> $variation_post_title,
					'post_content' 	=> '',
					'post_status' 	=> $post_status,
					'post_author' 	=> get_current_user_id(),
					'post_parent' 	=> $post_id,
					'post_type' 	=> 'product_variation',
					'menu_order' 	=> $variable_menu_order[ $i ]
				);

				$variation_id = wp_insert_post( $variation );

				do_action( 'carton_create_product_variation', $variation_id );

			} else {

				$wpdb->update( $wpdb->posts, array( 'post_status' => $post_status, 'post_title' => $variation_post_title, 'menu_order' => $variable_menu_order[ $i ] ), array( 'ID' => $variation_id ) );

				do_action( 'carton_update_product_variation', $variation_id );

			}

			// Update post meta
			update_post_meta( $variation_id, '_sku', carton_clean( $variable_sku[ $i ] ) );
			update_post_meta( $variation_id, '_weight', carton_clean( $variable_weight[ $i ] ) );

			update_post_meta( $variation_id, '_length', carton_clean( $variable_length[ $i ] ) );
			update_post_meta( $variation_id, '_width', carton_clean( $variable_width[ $i ] ) );
			update_post_meta( $variation_id, '_height', carton_clean( $variable_height[ $i ] ) );

			update_post_meta( $variation_id, '_stock', carton_clean( $variable_stock[ $i ] ) );
			update_post_meta( $variation_id, '_thumbnail_id', absint( $upload_image_id[ $i ] ) );

			update_post_meta( $variation_id, '_virtual', carton_clean( $is_virtual ) );
			update_post_meta( $variation_id, '_downloadable', carton_clean( $is_downloadable ) );

			// Price handling
			$purchase_price 	= carton_clean( $variable_purchase_price[ $i ] );
			$regular_price 	= carton_clean( $variable_regular_price[ $i ] );
			$sale_price 	= carton_clean( $variable_sale_price[ $i ] );
			$date_from 		= carton_clean( $variable_sale_price_dates_from[ $i ] );
			$date_to		= carton_clean( $variable_sale_price_dates_to[ $i ] );

			update_post_meta( $variation_id, '_purchase_price', $purchase_price );
			update_post_meta( $variation_id, '_regular_price', $regular_price );
			update_post_meta( $variation_id, '_sale_price', $sale_price );

			// Save Dates
			if ( $date_from )
				update_post_meta( $variation_id, '_sale_price_dates_from', strtotime( $date_from ) );
			else
				update_post_meta( $variation_id, '_sale_price_dates_from', '' );

			if ( $date_to )
				update_post_meta( $variation_id, '_sale_price_dates_to', strtotime( $date_to ) );
			else
				update_post_meta( $variation_id, '_sale_price_dates_to', '' );

			if ( $date_to && ! $date_from )
				update_post_meta( $variation_id, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );

			// Update price if on sale
			if ( $sale_price != '' && $date_to == '' && $date_from == '' )
				update_post_meta( $variation_id, '_price', $sale_price );
			else
				update_post_meta( $variation_id, '_price', $regular_price );

			if ( $sale_price != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) )
				update_post_meta( $variation_id, '_price', $sale_price );

			if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				update_post_meta( $variation_id, '_price', $regular_price );
				update_post_meta( $variation_id, '_sale_price_dates_from', '' );
				update_post_meta( $variation_id, '_sale_price_dates_to', '' );
			}

			if ( $variable_tax_class[ $i ] !== 'parent' )
				update_post_meta( $variation_id, '_tax_class', carton_clean( $variable_tax_class[ $i ] ) );
			else
				delete_post_meta( $variation_id, '_tax_class' );

			if ( $is_downloadable == 'yes' ) {
				update_post_meta( $variation_id, '_download_limit', carton_clean( $variable_download_limit[ $i ] ) );
				update_post_meta( $variation_id, '_download_expiry', carton_clean( $variable_download_expiry[ $i ] ) );

				$_file_paths = array();
				$file_paths = str_replace( "\r\n", "\n", $variable_file_paths[ $i ] );
				$file_paths = trim( preg_replace( "/\n+/", "\n", $file_paths ) );
				if ( $file_paths ) {
					$file_paths = explode( "\n", $file_paths );

					foreach ( $file_paths as $file_path ) {
						$file_path = trim( $file_path );
						$_file_paths[ md5( $file_path ) ] = $file_path;
					}
				}

				// grant permission to any newly added files on any existing orders for this product
				do_action( 'carton_process_product_file_download_paths', $post_id, $variation_id, $_file_paths );

				update_post_meta( $variation_id, '_file_paths', $_file_paths );
			} else {
				update_post_meta( $variation_id, '_download_limit', '' );
				update_post_meta( $variation_id, '_download_expiry', '' );
				update_post_meta( $variation_id, '_file_paths', '' );
			}

			// Save shipping class
			$variable_shipping_class[ $i ] = $variable_shipping_class[ $i ] > 0 ? (int) $variable_shipping_class[ $i ] : '';
			wp_set_object_terms( $variation_id, $variable_shipping_class[ $i ], 'product_shipping_class');

			// Remove old taxonomies attributes so data is kept up to date
			if ( $variation_id ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND post_id = %d;", $variation_id ) );
				wp_cache_delete( $variation_id, 'post_meta');
			}

			// Update taxonomies
			foreach ( $attributes as $attribute ) {

				if ( $attribute['is_variation'] || $attribute['is_changeable'] ) {

					// Don't use carton_clean as it destroys sanitized characters
					$value = sanitize_title( trim( stripslashes( $_POST[ 'attribute_' . sanitize_title( $attribute['name'] ) ][ $i ] ) ) );

					update_post_meta( $variation_id, 'attribute_' . sanitize_title( $attribute['name'] ), $value );
				}
			}

			do_action( 'carton_save_product_variation', $variation_id );

		 }

	}

	// Update parent if variable so price sorting works and stays in sync with the cheapest child
	$post_parent = $post_id;

	$children = get_posts( array(
		'post_parent' 	=> $post_parent,
		'posts_per_page'=> -1,
		'post_type' 	=> 'product_variation',
		'fields' 	=> 'ids',
		'post_status'	=> 'publish'
	) );
	$children[] = $post_parent;

	$lowest_price  = $highest_price = '';
	$lowest_purchase_price = $highest_purchase_price = '';
	$lowest_regular_price = $highest_regular_price = '';
	$lowest_sale_price = $highest_sale_price = '';

	if ( $children ) {
		foreach ( $children as $child ) {
			$child_price 			= get_post_meta( $child, '_price', true );
			$child_purchase_price		= get_post_meta( $child, '_purchase_price', true );
			$child_regular_price		= get_post_meta( $child, '_regular_price', true );
			$child_sale_price 		= get_post_meta( $child, '_sale_price', true );

			// Purchase prices
			if ( ! is_numeric( $lowest_purchase_price ) || $child_purchase_price < $lowest_purchase_price )
				$lowest_purchase_price = $child_purchase_price;

			if ( ! is_numeric( $highest_purchase_price ) || $child_purchase_price > $highest_purchase_price )
				$highest_purchase_price = $child_purchase_price;

			// Regular prices
			if ( ! is_numeric( $lowest_regular_price ) || $child_regular_price < $lowest_regular_price )
				$lowest_regular_price = $child_regular_price;

			if ( ! is_numeric( $highest_regular_price ) || $child_regular_price > $highest_regular_price )
				$highest_regular_price = $child_regular_price;

			// Sale prices
			if ( $child_price == $child_sale_price ) {
				if ( $child_sale_price !== '' && ( ! is_numeric( $lowest_sale_price ) || $child_sale_price < $lowest_sale_price ) )
					$lowest_sale_price = $child_sale_price;

				if ( $child_sale_price !== '' && ( ! is_numeric( $highest_sale_price ) || $child_sale_price > $highest_sale_price ) )
					$highest_sale_price = $child_sale_price;
			}
		}

		$lowest_price  = $lowest_sale_price === '' || $lowest_regular_price < $lowest_sale_price ? $lowest_regular_price : $lowest_sale_price;
		$highest_price = $highest_sale_price === '' || $highest_regular_price > $highest_sale_price ? $highest_regular_price : $highest_sale_price;
	}

	update_post_meta( $post_parent, '_price', $lowest_price );
	update_post_meta( $post_parent, '_min_variation_price', $lowest_price );
	update_post_meta( $post_parent, '_max_variation_price', $highest_price );
	update_post_meta( $post_parent, '_min_variation_purchase_price', $lowest_purchase_price );
	update_post_meta( $post_parent, '_max_variation_purchase_price', $highest_purchase_price );
	update_post_meta( $post_parent, '_min_variation_regular_price', $lowest_regular_price );
	update_post_meta( $post_parent, '_max_variation_regular_price', $highest_regular_price );
	update_post_meta( $post_parent, '_min_variation_sale_price', $lowest_sale_price );
	update_post_meta( $post_parent, '_max_variation_sale_price', $highest_sale_price );

	// Update default attribute options setting
	$default_attributes = array();

	foreach ( $attributes as $attribute ) {
		if ( $attribute['is_variation'] || $attribute['is_changeable'] ) {

			// Don't use carton_clean as it destroys sanitized characters
			$value = sanitize_title( trim( stripslashes( $_POST[ 'default_attribute_' . sanitize_title( $attribute['name'] ) ] ) ) );

			if ( $value )
				$default_attributes[ sanitize_title( $attribute['name'] ) ] = $value;
		}
	}

	update_post_meta( $post_parent, '_default_attributes', $default_attributes );
}

add_action( 'carton_process_product_meta_variable', 'process_product_meta_variable' );
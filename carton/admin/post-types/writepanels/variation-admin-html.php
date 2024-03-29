<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    $sku = esc_attr( $parent_data['sku'] );
    if ( $_sku ) $sku = esc_attr( $_sku );

?>
<div class="carton_variation wc-metabox closed">
	<h3>
		<button type="button" class="remove_variation button" rel="<?php echo esc_attr( $variation_id ); ?>"><?php _e( 'Remove', 'carton' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'carton' ); ?>"></div>
		<strong>
			<span class="variation sku"><?php echo $sku; ?></span>
			<?php if ( isset( $_regular_price ) ) echo ' ('. carton_price( $_regular_price ) . ')'; ?>
			&#032;&mdash;&#032;
		</strong>

		<?php
			$has_changeable = 0;

			foreach ( $parent_data['attributes'] as $attribute ) {

				if ( $attribute['is_changeable'] ) $has_changeable = 1;

				// Only deal with attributes that are variations
				if ( ! $attribute['is_variation'] )
					continue;

				// Get current value for variation (if set)
				$variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] : '';

				// Name will be something like attribute_pa_color
				echo '<select name="attribute_' . sanitize_title( $attribute['name'] ) . '[' . $loop . ']"><option value="">' . esc_html( $carton->attribute_label( $attribute['name'] ) ) . '&hellip;</option>';

				// Get terms for attribute taxonomy or value if its a custom attribute
				if ( $attribute['is_taxonomy'] ) {

					$post_terms = wp_get_post_terms( $parent_data['id'], $attribute['name'] );

					foreach ( $post_terms as $term ) {
						echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . apply_filters( 'carton_variation_option_name', esc_html( $term->name ) ) . '</option>';
					}

				} else {

					$options = array_map( 'trim', explode( '|', $attribute['value'] ) );

					foreach ( $options as $option ) {
						echo '<option ' . selected( sanitize_title( $variation_selected_value ), sanitize_title( $option ), false ) . ' value="' . esc_attr( sanitize_title( $option ) ) . '">' . esc_html( apply_filters( 'carton_variation_option_name', $option ) ) . '</option>';
					}

				}

				echo '</select>';
			}
		?>

		<input type="hidden" name="variable_post_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $variation_id ); ?>" />
		<input type="hidden" class="variation_menu_order" name="variation_menu_order[<?php echo $loop; ?>]" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="carton_variable_attributes wc-metabox-content">
		<tbody>
			<tr>
				<td class="sku" colspan="2">
					<?php if ( get_option( 'carton_enable_sku', true ) !== 'no' ) : ?>
						<label><?php _e( 'SKU', 'carton' ); ?>: <a class="tips" data-tip="<?php _e( 'Enter a SKU for this variation or leave blank to use the parent product SKU.', 'carton' ); ?>" href="#">[?]</a></label>
						<input type="text" size="5" name="variable_sku[<?php echo $loop; ?>]" value="<?php echo $sku;?>" placeholder="<?php echo $sku;?>" />
					<?php else : ?>
						<input type="hidden" name="variable_sku[<?php echo $loop; ?>]" value="<?php echo $sku; ?>" />
					<?php endif; ?>
				</td>
				<td class="data" rowspan="2">
					<table cellspacing="0" cellpadding="0" class="data_table">
						<?php if ( get_option( 'carton_manage_stock' ) == 'yes' ) : ?>
							<tr>
								<td>
									<label><?php _e( 'Stock Qty:', 'carton' ); ?> <a class="tips" data-tip="<?php _e( 'Enter a quantity to enable stock management at variation level, or leave blank to use the parent product\'s options.', 'carton' ); ?>" href="#">[?]</a></label>
									<input type="number" size="5" name="variable_stock[<?php echo $loop; ?>]" value="<?php if ( isset( $_stock ) ) echo esc_attr( $_stock ); ?>" step="any" />
								</td>
								<td>&nbsp;</td>
							</tr>
						<?php endif; ?>

						<tr>
							<td>
								<label><?php echo __( 'Purchase Price:', 'carton' ) . ' ('.get_carton_currency_symbol().')'; ?></label>
								<input type="number" size="5" name="variable_purchase_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_purchase_price ) ) echo esc_attr( $_purchase_price ); ?>" step="any" min="0" placeholder="<?php _e( 'Variation purchase price', 'carton' ); ?>" />
							</td>
							<td>
							</td>
						</tr>

						<tr>
							<td>
								<label><?php echo __( 'Regular Price:', 'carton' ) . ' ('.get_carton_currency_symbol().')'; ?></label>
								<input type="number" size="5" name="variable_regular_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_regular_price ) ) echo esc_attr( $_regular_price ); ?>" step="any" min="0" placeholder="<?php _e( 'Variation price (required)', 'carton' ); ?>" />
							</td>
							<td>
								<label><?php echo __( 'Sale Price:', 'carton' ) . ' ('.get_carton_currency_symbol().')'; ?> <a href="#" class="sale_schedule"><?php _e( 'Schedule', 'carton' ); ?></a><a href="#" class="cancel_sale_schedule" style="display:none"><?php _e( 'Cancel schedule', 'carton' ); ?></a></label>
								<input type="number" size="5" name="variable_sale_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_sale_price ) ) echo esc_attr( $_sale_price ); ?>" step="any" min="0" />
							</td>
						</tr>

						<tr class="sale_price_dates_fields" style="display:none">
							<td>
								<label><?php _e( 'Sale start date:', 'carton' ) ?></label>
								<input type="text" class="sale_price_dates_from" name="variable_sale_price_dates_from[<?php echo $loop; ?>]" value="<?php echo ! empty( $_sale_price_dates_from ) ? date_i18n( 'Y-m-d', $_sale_price_dates_from ) : ''; ?>" placeholder="<?php echo _x( 'From&hellip;', 'placeholder', 'carton' ) ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
							</td>
							<td>
								<label><?php _e( 'Sale end date:', 'carton' ) ?></label>
								<input type="text" name="variable_sale_price_dates_to[<?php echo $loop; ?>]" value="<?php echo ! empty( $_sale_price_dates_to ) ? date_i18n( 'Y-m-d', $_sale_price_dates_to ) : ''; ?>" placeholder="<?php echo _x('To&hellip;', 'placeholder', 'carton') ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
							</td>
						</tr>

						<?php if ( get_option( 'carton_enable_weight', true ) !== 'no' || get_option( 'carton_enable_dimensions', true ) !== 'no' ) : ?>
							<tr>
								<?php if ( get_option( 'carton_enable_weight', true ) !== 'no' ) : ?>
									<td class="hide_if_variation_virtual">
										<label><?php _e( 'Weight', 'carton' ) . ' (' . esc_html( get_option( 'carton_weight_unit' ) ) . '):'; ?> <a class="tips" data-tip="<?php _e( 'Enter a weight for this variation or leave blank to use the parent product weight.', 'carton' ); ?>" href="#">[?]</a></label>
										<input type="number" size="5" name="variable_weight[<?php echo $loop; ?>]" value="<?php if ( isset( $_weight ) ) echo esc_attr( $_weight ); ?>" placeholder="<?php echo esc_attr( $parent_data['weight'] ); ?>" step="any" min="0" />
									</td>
								<?php else : ?>
									<td>&nbsp;</td>
								<?php endif; ?>
								<?php if ( get_option( 'carton_enable_dimensions', true ) !== 'no' ) : ?>
									<td class="dimensions_field hide_if_variation_virtual">
										<label for"product_length"><?php echo __( 'Dimensions (L&times;W&times;H)', 'carton' ); ?></label>
										<input id="product_length" class="input-text" size="6" type="number" step="any" min="0" name="variable_length[<?php echo $loop; ?>]" value="<?php if ( isset( $_length ) ) echo esc_attr( $_length ); ?>" placeholder="<?php echo esc_attr( $parent_data['length'] ); ?>" />
										<input class="input-text" size="6" type="number" step="any" min="0" name="variable_width[<?php echo $loop; ?>]" value="<?php if ( isset( $_width ) ) echo esc_attr( $_width ); ?>" placeholder="<?php echo esc_attr( $parent_data['width'] ); ?>" />
										<input class="input-text last" size="6" type="number" step="any" min="0" name="variable_height[<?php echo $loop; ?>]" value="<?php if ( isset( $_height ) ) echo esc_attr( $_height ); ?>" placeholder="<?php echo esc_attr( $parent_data['height'] ); ?>" />
									</td>
								<?php else : ?>
									<td>&nbsp;</td>
								<?php endif; ?>
							</tr>
						<?php endif; ?>
						<tr>
							<td><label><?php _e( 'Shipping class:', 'carton' ); ?></label> <?php
								$args = array(
									'taxonomy' 			=> 'product_shipping_class',
									'hide_empty'		=> 0,
									'show_option_none' 	=> __( 'Same as parent', 'carton' ),
									'name' 				=> 'variable_shipping_class[' . $loop . ']',
									'id'				=> '',
									'selected'			=> isset( $shipping_class ) ? esc_attr( $shipping_class ) : '',
									'echo'				=> 0
								);

								echo wp_dropdown_categories( $args );
							?></td>
							<td>
								<?php if ( get_option( 'carton_calc_taxes' ) == 'yes' ) : ?>
								<label><?php _e( 'Tax class:', 'carton' ); ?></label>
								<select name="variable_tax_class[<?php echo $loop; ?>]"><?php
									foreach ( $parent_data['tax_class_options'] as $key => $value )
										echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $_tax_class, false ) . '>' . esc_html( $value ) . '</option>';
								?></select>
								<?php endif; ?>
							</td>
						</tr>
						<tr class="show_if_variation_downloadable">
							<td rowspan="2">
								<div class="file_path_field">
									<label><?php _e( 'File paths:', 'carton' ); ?> <a class="tips" data-tip="<?php _e( 'Enter one or more File Paths, one per line, to make this variation a downloadable product, or leave blank.', 'carton' ); ?>" href="#">[?]</a></label>
									<textarea style="float:left;" class="short file_paths" cols="20" rows="2" placeholder="<?php _e( 'File paths/URLs, one per line', 'carton' ); ?>" name="variable_file_paths[<?php echo $loop; ?>]" wrap="off"><?php if ( isset( $_file_paths ) ) echo esc_textarea( $_file_paths ); ?></textarea>
									<input type="button" class="upload_file_button button" value="<?php _e( 'Choose a file', 'carton' ); ?>" title="<?php _e( 'Upload', 'carton' ); ?>" data-choose="<?php _e( 'Choose a file', 'carton' ); ?>" data-update="<?php _e( 'Insert file URL', 'carton' ); ?>" value="<?php _e( 'Choose a file', 'carton' ); ?>" />
								</div>
							</td>
							<td>
								<div>
									<label><?php _e( 'Download Limit:', 'carton' ); ?> <a class="tips" data-tip="<?php _e( 'Leave blank for unlimited re-downloads.', 'carton' ); ?>" href="#">[?]</a></label>
									<input type="number" size="5" name="variable_download_limit[<?php echo $loop; ?>]" value="<?php if ( isset( $_download_limit ) ) echo esc_attr( $_download_limit ); ?>" placeholder="<?php _e( 'Unlimited', 'carton' ); ?>" step="1" min="0" />
								</div>
							</td>
						</tr>
						<tr class="show_if_variation_downloadable">
							<td>
								<div>
									<label><?php _e( 'Download Expiry:', 'carton' ); ?> <a class="tips" data-tip="<?php _e( 'Enter the number of days before a download link expires, or leave blank.', 'carton' ); ?>" href="#">[?]</a></label>
									<input type="number" size="5" name="variable_download_expiry[<?php echo $loop; ?>]" value="<?php if ( isset( $_download_expiry ) ) echo esc_attr( $_download_expiry ); ?>" placeholder="<?php _e( 'Unlimited', 'carton' ); ?>" step="1" min="0" />
								</div>
							</td>
						</tr>
						<?php do_action( 'carton_product_after_variable_attributes', $loop, $variation_data, $variation ); ?>
					</table>
				</td>
			</tr>
			<tr>
				<td class="upload_image">
					<a href="#" class="upload_image_button <?php if ( $image_id > 0 ) echo 'remove'; ?>" rel="<?php echo esc_attr( $variation_id ); ?>"><img src="<?php if ( ! empty( $image ) ) echo esc_attr( $image ); else echo esc_attr( carton_placeholder_img_src() ); ?>" /><input type="hidden" name="upload_image_id[<?php echo $loop; ?>]" class="upload_image_id" value="<?php echo esc_attr( $image_id ); ?>" /><span class="overlay"></span></a>
				</td>
				<td class="options">
					<label><input type="checkbox" class="checkbox" name="variable_enabled[<?php echo $loop; ?>]" <?php checked( $variation_post_status, 'publish' ); ?> /> <?php _e( 'Enabled', 'carton' ); ?></label>

					<label><input type="checkbox" class="checkbox variable_is_downloadable" name="variable_is_downloadable[<?php echo $loop; ?>]" <?php checked( isset( $_downloadable ) ? $_downloadable : '', 'yes' ); ?> /> <?php _e( 'Downloadable', 'carton' ); ?> <a class="tips" data-tip="<?php _e( 'Enable this option if access is given to a downloadable file upon purchase of a product', 'carton' ); ?>" href="#">[?]</a></label>

					<label><input type="checkbox" class="checkbox variable_is_virtual" name="variable_is_virtual[<?php echo $loop; ?>]" <?php checked( isset( $_virtual ) ? $_virtual : '', 'yes' ); ?> /> <?php _e( 'Virtual', 'carton' ); ?> <a class="tips" data-tip="<?php _e( 'Enable this option if a product is not shipped or there is no shipping cost', 'carton' ); ?>" href="#">[?]</a></label>

					<?php do_action( 'carton_variation_options', $loop, $variation_data, $variation ); ?>
				</td>
			</tr>
			<?php if( $has_changeable ) :?>
			<tr>
				<td colspan="7">
					<p><strong>Changeable attributes set:</strong></p>
		<?php
			foreach ( $parent_data['attributes'] as $attribute ) {

				// Only deal with attributes that are changeable
				if ( ! $attribute['is_changeable'] )
					continue;

				// Get current value for variation (if set)
				$variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ][0] : '';

				// Name will be something like attribute_pa_color
				echo '<p>';
				echo '<label for="attribute_' . sanitize_title( $attribute['name'] ) . '[' . $loop . ']"><strong>'. esc_html( $carton->attribute_label( $attribute['name'] ) ) . ': </strong></label><select name="attribute_' . sanitize_title( $attribute['name'] ) . '[' . $loop . ']"><option value="">' . __( 'Any', 'carton' ) .'&hellip;</option>';

				// Get terms for attribute taxonomy or value if its a custom attribute
				if ( $attribute['is_taxonomy'] ) {

					$post_terms = wp_get_post_terms( $parent_data['id'], $attribute['name'] );

					foreach ( $post_terms as $term ) {
						echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . apply_filters( 'carton_variation_option_name', esc_html( $term->name ) ) . '</option>';
					}

				} else {

					$options = array_map( 'trim', explode( '|', $attribute['value'] ) );

					foreach ( $options as $option ) {
						echo '<option ' . selected( sanitize_title( $variation_selected_value ), sanitize_title( $option ), false ) . ' value="' . esc_attr( sanitize_title( $option ) ) . '">' . esc_html( apply_filters( 'carton_variation_option_name', $option ) ) . '</option>';
					}

				}

				echo '</select>';
				echo '</p>';
			}
		?>
				</td>
                        </tr>
			<?php endif; ?>


		</tbody>
	</table>
</div>

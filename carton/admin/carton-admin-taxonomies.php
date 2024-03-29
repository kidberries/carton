<?php
/**
 * Admin taxonomy functions
 *
 * These functions control admin interface bits like category ordering.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Taxonomies
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Category thumbnail fields.
 *
 * @access public
 * @return void
 */
function carton_add_category_fields() {
	global $carton;
    
    $tinymce = array( 'textarea_rows' => get_option('default_post_edit_rows'), 'textarea_name' => 'advertisement' );
	?>
    <div class="form-field">
        <label for="advertisement"><?php echo __( 'Advertisement', 'carton' ); ?></label>
        <?php wp_editor( '', 'tinymce-advertisement-editor', $tinymce ); ?>
        <p><?php echo __( 'Category advertisement. It will be placed at the top of page before category name.', 'carton' ); ?></p>
    </div>
    
	<div class="form-field">
		<label for="display_type"><?php _e( 'Display type', 'carton' ); ?></label>
		<select id="display_type" name="display_type" class="postform">
			<option value=""><?php _e( 'Default', 'carton' ); ?></option>
			<option value="products"><?php _e( 'Products', 'carton' ); ?></option>
			<option value="subcategories"><?php _e( 'Subcategories', 'carton' ); ?></option>
			<option value="both"><?php _e( 'Both', 'carton' ); ?></option>
		</select>
	</div>

	<div class="form-field">
		<label><?php _e( 'Thumbnail', 'carton' ); ?></label>
		<div id="product_cat_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo carton_placeholder_img_src(); ?>" width="60px" height="60px" /></div>
		<div style="line-height:60px;">
			<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" />
			<button type="submit" class="upload_image_button button"><?php _e( 'Upload/Add image', 'carton' ); ?></button>
			<button type="submit" class="remove_image_button button"><?php _e( 'Remove image', 'carton' ); ?></button>
		</div>
		<script type="text/javascript">

			 // Only show the "remove image" button when needed
			 if ( ! jQuery('#product_cat_thumbnail_id').val() )
				 jQuery('.remove_image_button').hide();

			// Uploading files
			var file_frame;

			jQuery(document)

            .ready( function(){
                jQuery('form input[type="submit"]').click(function(){
                    tinyMCE.triggerSave();
                })
            })
            
            .on( 'click', '.upload_image_button', function( event ){

				event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}

				// Create the media frame.
				file_frame = wp.media.frames.downloadable_file = wp.media({
					title: '<?php _e( 'Choose an image', 'carton' ); ?>',
					button: {
						text: '<?php _e( 'Use image', 'carton' ); ?>',
					},
					multiple: false
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					attachment = file_frame.state().get('selection').first().toJSON();

					jQuery('#product_cat_thumbnail_id').val( attachment.id );
					jQuery('#product_cat_thumbnail img').attr('src', attachment.url );
					jQuery('.remove_image_button').show();
				});

				// Finally, open the modal.
				file_frame.open();
			})
            
            .on( 'click', '.remove_image_button', function( event ){
				jQuery('#product_cat_thumbnail img').attr('src', '<?php echo carton_placeholder_img_src(); ?>');
				jQuery('#product_cat_thumbnail_id').val('');
				jQuery('.remove_image_button').hide();
				return false;
			});

		</script>
		<div class="clear"></div>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'carton_add_category_fields' );



/**
 * Add category submit and return button.
 *
 * @access public
 * @param mixed $term Term (category) being edited
 * @param mixed $taxonomy Taxonomy of the term being edited
 * @return void
 */
/* TODO
function carton_edit_category_submitbutton( $term, $taxonomy ) {
    ?>
        <input type="submit" class="button button-primary" name="submit_and_return" value="Update and return" onclick="" />
    <?php
}
add_action( 'product_cat_edit_form', 'carton_edit_category_submitbutton' );
*/

/**
 * Edit category thumbnail field.
 *
 * @access public
 * @param mixed $term Term (category) being edited
 * @param mixed $taxonomy Taxonomy of the term being edited
 * @return void
 */
function carton_edit_category_fields( $term, $taxonomy ) {
	global $carton;
    $tinymce = array( 'textarea_rows' => get_option('default_post_edit_rows'), 'textarea_name' => 'advertisement', 'textarea_class' => 'postform' );
    
	$display_type	= get_carton_term_meta( $term->term_id, 'display_type', true );
	$image 			= '';
	$thumbnail_id 	= absint( get_carton_term_meta( $term->term_id, 'thumbnail_id', true ) );
	if ($thumbnail_id) :
		$image = wp_get_attachment_url( $thumbnail_id );
	else :
		$image = carton_placeholder_img_src();
	endif;
	?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="advertisement"><?php echo __( 'Advertisement', 'carton' ); ?></label></th>
        <td>
            <?php wp_editor( html_entity_decode( get_carton_term_meta( $term->term_id, 'advertisement', true ) ), 'tinymce-advertisement-editor', $tinymce ); ?>
            <br/>
            <span class="description"><?php echo __( 'Category advertisement. It will be placed at the top of page before category name.', 'carton' ); ?></span>
        </td>
    </tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label><?php _e( 'Display type', 'carton' ); ?></label></th>
		<td>
			<select id="display_type" name="display_type" class="postform">
				<option value="" <?php selected( '', $display_type ); ?>><?php _e( 'Default', 'carton' ); ?></option>
				<option value="products" <?php selected( 'products', $display_type ); ?>><?php _e( 'Products', 'carton' ); ?></option>
				<option value="subcategories" <?php selected( 'subcategories', $display_type ); ?>><?php _e( 'Subcategories', 'carton' ); ?></option>
				<option value="both" <?php selected( 'both', $display_type ); ?>><?php _e( 'Both', 'carton' ); ?></option>
			</select>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'carton' ); ?></label></th>
		<td>
			<div id="product_cat_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
				<button type="submit" class="upload_image_button button"><?php _e( 'Upload/Add image', 'carton' ); ?></button>
				<button type="submit" class="remove_image_button button"><?php _e( 'Remove image', 'carton' ); ?></button>
			</div>
			<script type="text/javascript">

				// Uploading files
				var file_frame;

				jQuery(document)
                .on( 'click', '.upload_image_button', function( event ){

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( 'Choose an image', 'carton' ); ?>',
						button: {
							text: '<?php _e( 'Use image', 'carton' ); ?>',
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#product_cat_thumbnail_id').val( attachment.id );
						jQuery('#product_cat_thumbnail img').attr('src', attachment.url );
						jQuery('.remove_image_button').show();
					});

					// Finally, open the modal.
					file_frame.open();
				})

				.on( 'click', '.remove_image_button', function( event ){
					jQuery('#product_cat_thumbnail img').attr('src', '<?php echo carton_placeholder_img_src(); ?>');
					jQuery('#product_cat_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

			</script>
			<div class="clear"></div>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'carton_edit_category_fields', 10,2 );


/**
 * carton_category_fields_save function.
 *
 * @access public
 * @param mixed $term_id Term ID being saved
 * @param mixed $tt_id
 * @param mixed $taxonomy Taxonomy of the term being saved
 * @return void
 */
function carton_category_fields_save( $term_id, $tt_id, $taxonomy ) {
	if ( isset( $_POST['display_type'] ) )
		update_carton_term_meta( $term_id, 'display_type', esc_attr( $_POST['display_type'] ) );

	if ( isset( $_POST['product_cat_thumbnail_id'] ) )
		update_carton_term_meta( $term_id, 'thumbnail_id', absint( $_POST['product_cat_thumbnail_id'] ) );
        
	if ( isset( $_POST['advertisement'] ) )
		update_carton_term_meta( $term_id, 'advertisement', esc_attr( $_POST['advertisement'] ) );

	delete_transient( 'ctn_term_counts' );
}

add_action( 'created_term', 'carton_category_fields_save', 10,3 );
add_action( 'edit_term', 'carton_category_fields_save', 10,3 );


/**
 * Description for product_cat page to aid users.
 *
 * @access public
 * @return void
 */
function carton_product_cat_description() {

	echo wpautop( __( 'Product categories for your store can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top of the page.', 'carton' ) );

}

add_action( 'product_cat_pre_add_form', 'carton_product_cat_description' );


/**
 * Description for shipping class page to aid users.
 *
 * @access public
 * @return void
 */
function carton_shipping_class_description() {

	echo wpautop(__( 'Shipping classes can be used to group products of similar type. These groups can then be used by certain shipping methods to provide different rates to different products.', 'carton' ));

}

add_action( 'product_shipping_class_pre_add_form', 'carton_shipping_class_description' );


/**
 * Fix for the per_page option
 *
 * Trac: http://core.trac.wordpress.org/ticket/19465
 *
 * @access public
 * @param mixed $per_page
 * @param mixed $post_type
 * @return void
 */
function carton_fix_edit_posts_per_page( $per_page, $post_type ) {

	if ( $post_type !== 'product' )
		return $per_page;

	$screen = get_current_screen();

	if ( strstr( $screen->id, '-' ) ) {

		$option = 'edit_' . str_replace( 'edit-', '', $screen->id ) . '_per_page';

		if ( isset( $_POST['wp_screen_options']['option'] ) && $_POST['wp_screen_options']['option'] == $option ) {

			update_user_meta( get_current_user_id(), $option, $_POST['wp_screen_options']['value'] );

			wp_redirect( remove_query_arg( array('pagenum', 'apage', 'paged'), wp_get_referer() ) );
			exit;

		}

		$user_per_page = (int) get_user_meta( get_current_user_id(), $option, true );

		if ( $user_per_page )
			$per_page = $user_per_page;

	}

	return $per_page;
}

add_filter( 'edit_posts_per_page', 'carton_fix_edit_posts_per_page', 1, 2 );


/**
 * Thumbnail column added to category admin.
 *
 * @access public
 * @param mixed $columns
 * @return void
 */
function carton_product_cat_columns( $columns ) {
	$new_columns = array();
	$new_columns['cb'] = $columns['cb'];
	$new_columns['thumb'] = __( 'Image', 'carton' );

	unset( $columns['cb'] );

	return array_merge( $new_columns, $columns );
}

add_filter( 'manage_edit-product_cat_columns', 'carton_product_cat_columns' );


/**
 * Thumbnail column value added to category admin.
 *
 * @access public
 * @param mixed $columns
 * @param mixed $column
 * @param mixed $id
 * @return void
 */
function carton_product_cat_column( $columns, $column, $id ) {
	global $carton;

	if ( $column == 'thumb' ) {

		$image 			= '';
		$thumbnail_id 	= get_carton_term_meta( $id, 'thumbnail_id', true );

		if ($thumbnail_id)
			$image = wp_get_attachment_url( $thumbnail_id );
		else
			$image = carton_placeholder_img_src();

		$columns .= '<img src="' . $image . '" alt="Thumbnail" class="wp-post-image" height="48" width="48" />';

	}

	return $columns;
}

add_filter( 'manage_product_cat_custom_column', 'carton_product_cat_column', 10, 3 );


/**
 * Add a configure button column for the shipping classes page.
 *
 * @access public
 * @param mixed $columns
 * @return void
 */
function carton_shipping_class_columns( $columns ) {
	$columns['edit'] = '&nbsp;';
	return $columns;
}

add_filter( 'manage_edit-product_shipping_class_columns', 'carton_shipping_class_columns' );


/**
 * Add a configure button for the shipping classes page.
 *
 * @access public
 * @param mixed $columns
 * @param mixed $column
 * @param mixed $id
 * @return void
 */
function carton_shipping_class_column( $columns, $column, $id ) {
	if ( $column == 'edit' )
		$columns .= '<a href="'. admin_url( 'edit-tags.php?action=edit&taxonomy=product_shipping_class&tag_ID='. $id .'&post_type=product' ) .'" class="button alignright">'.__( 'Edit Class', 'carton' ).'</a>';

	return $columns;
}

add_filter( 'manage_product_shipping_class_custom_column', 'carton_shipping_class_column', 10, 3 );
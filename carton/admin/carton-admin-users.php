<?php
/**
 * Admin user functions
 *
 * Functions used for modifying the users panel in admin.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Users
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define columns to show on the users page.
 *
 * @access public
 * @param array $columns Columns on the manage users page
 * @return array The modified columns
 */
function carton_user_columns( $columns ) {
	if ( ! current_user_can( 'manage_carton' ) )
		return $columns;

	$columns['carton_billing_address'] = __( 'Billing Address', 'carton' );
	$columns['carton_shipping_address'] = __( 'Shipping Address', 'carton' );
	$columns['carton_paying_customer'] = __( 'Paying Customer?', 'carton' );
	$columns['carton_order_count'] = __( 'Completed Orders', 'carton' );
	return $columns;
}

add_filter( 'manage_users_columns', 'carton_user_columns', 10, 1 );


/**
 * Define values for custom columns.
 *
 * @access public
 * @param mixed $value The value of the column being displayed
 * @param mixed $column_name The name of the column being displayed
 * @param mixed $user_id The ID of the user being displayed
 * @return string Value for the column
 */
function carton_user_column_values( $value, $column_name, $user_id ) {
	global $carton, $wpdb;
	switch ( $column_name ) :
		case "carton_order_count" :

			if ( ! $count = get_user_meta( $user_id, '_order_count', true ) ) {

				$count = $wpdb->get_var( "SELECT COUNT(*)
					FROM $wpdb->posts as posts

					LEFT JOIN {$wpdb->postmeta} AS meta ON posts.\"ID\" = meta.post_id
					LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.\"ID\"=rel.object_ID
					LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
					LEFT JOIN {$wpdb->terms} AS term USING( term_id )

					WHERE 	meta.meta_key 		= '_customer_user'
					AND 	posts.post_type 	= 'shop_order'
					AND 	posts.post_status 	= 'publish'
					AND 	tax.taxonomy		= 'shop_order_status'
					AND		term.slug			IN ( 'completed' )
					AND 	meta_value 			= $user_id
				" );

				update_user_meta( $user_id, '_order_count', $count );
			}

			$value = '<a href="' . admin_url( 'edit.php?post_status=all&post_type=shop_order&shop_order_status=completed&_customer_user=' . absint( $user_id ) . '' ) . '">' . absint( $count ) . '</a>';

		break;
		case "carton_billing_address" :
			$address = array(
				'first_name' 	=> get_user_meta( $user_id, 'billing_first_name', true ),
				'last_name'		=> get_user_meta( $user_id, 'billing_last_name', true ),
				'company'		=> get_user_meta( $user_id, 'billing_company', true ),
				'address_1'		=> get_user_meta( $user_id, 'billing_address_1', true ),
				'address_2'		=> get_user_meta( $user_id, 'billing_address_2', true ),
				'city'			=> get_user_meta( $user_id, 'billing_city', true ),
				'state'			=> get_user_meta( $user_id, 'billing_state', true ),
				'postcode'		=> get_user_meta( $user_id, 'billing_postcode', true ),
				'country'		=> get_user_meta( $user_id, 'billing_country', true )
			);

			$formatted_address = $carton->countries->get_formatted_address( $address );

			if ( ! $formatted_address )
				$value = __( 'N/A', 'carton' );
			else
				$value = $formatted_address;

			$value = wpautop( $value );
		break;
		case "carton_shipping_address" :
			$address = array(
				'first_name' 	=> get_user_meta( $user_id, 'shipping_first_name', true ),
				'last_name'		=> get_user_meta( $user_id, 'shipping_last_name', true ),
				'company'		=> get_user_meta( $user_id, 'shipping_company', true ),
				'address_1'		=> get_user_meta( $user_id, 'shipping_address_1', true ),
				'address_2'		=> get_user_meta( $user_id, 'shipping_address_2', true ),
				'city'			=> get_user_meta( $user_id, 'shipping_city', true ),
				'state'			=> get_user_meta( $user_id, 'shipping_state', true ),
				'postcode'		=> get_user_meta( $user_id, 'shipping_postcode', true ),
				'country'		=> get_user_meta( $user_id, 'shipping_country', true )
			);

			$formatted_address = $carton->countries->get_formatted_address( $address );

			if ( ! $formatted_address )
				$value = __( 'N/A', 'carton' );
			else
				$value = $formatted_address;

			$value = wpautop( $value );
		break;
		case "carton_paying_customer" :

			$paying_customer = get_user_meta( $user_id, 'paying_customer', true );

			if ( $paying_customer )
				$value = '<img src="' . $carton->plugin_url() . '/assets/images/success@2x.png" alt="yes" width="16px" />';
			else
				$value = '<img src="' . $carton->plugin_url() . '/assets/images/success-off@2x.png" alt="no" width="16px" />';

		break;
	endswitch;
	return $value;
}

add_action( 'manage_users_custom_column', 'carton_user_column_values', 10, 3 );


/**
 * Get Address Fields for the edit user pages.
 *
 * @access public
 * @return array Fields to display which are filtered through carton_customer_meta_fields before being returned
 */
function carton_get_customer_meta_fields() {
	$show_fields = apply_filters('carton_customer_meta_fields', array(
		'billing' => array(
			'title' => __( 'Customer Billing Address', 'carton' ),
			'fields' => array(
				'billing_first_name' => array(
						'label' => __( 'First name', 'carton' ),
						'description' => ''
					),
				'billing_last_name' => array(
						'label' => __( 'Last name', 'carton' ),
						'description' => ''
					),
				'billing_company' => array(
						'label' => __( 'Company', 'carton' ),
						'description' => ''
					),
				'billing_address_1' => array(
						'label' => __( 'Address 1', 'carton' ),
						'description' => ''
					),
				'billing_address_2' => array(
						'label' => __( 'Address 2', 'carton' ),
						'description' => ''
					),
				'billing_city' => array(
						'label' => __( 'City', 'carton' ),
						'description' => ''
					),
				'billing_postcode' => array(
						'label' => __( 'Postcode', 'carton' ),
						'description' => ''
					),
				'billing_state' => array(
						'label' => __( 'State/County', 'carton' ),
						'description' => __( 'Country or state code', 'carton' ),
					),
				'billing_country' => array(
						'label' => __( 'Country', 'carton' ),
						'description' => __( '2 letter Country code', 'carton' ),
					),
				'billing_phone' => array(
						'label' => __( 'Telephone', 'carton' ),
						'description' => ''
					),
				'billing_email' => array(
						'label' => __( 'Email', 'carton' ),
						'description' => ''
					)
			)
		),
		'shipping' => array(
			'title' => __( 'Customer Shipping Address', 'carton' ),
			'fields' => array(
				'shipping_first_name' => array(
						'label' => __( 'First name', 'carton' ),
						'description' => ''
					),
				'shipping_last_name' => array(
						'label' => __( 'Last name', 'carton' ),
						'description' => ''
					),
				'shipping_company' => array(
						'label' => __( 'Company', 'carton' ),
						'description' => ''
					),
				'shipping_address_1' => array(
						'label' => __( 'Address 1', 'carton' ),
						'description' => ''
					),
				'shipping_address_2' => array(
						'label' => __( 'Address 2', 'carton' ),
						'description' => ''
					),
				'shipping_city' => array(
						'label' => __( 'City', 'carton' ),
						'description' => ''
					),
				'shipping_postcode' => array(
						'label' => __( 'Postcode', 'carton' ),
						'description' => ''
					),
				'shipping_state' => array(
						'label' => __( 'State/County', 'carton' ),
						'description' => __( 'State/County or state code', 'carton' )
					),
				'shipping_country' => array(
						'label' => __( 'Country', 'carton' ),
						'description' => __( '2 letter Country code', 'carton' )
					),
				'shipping_phone' => array(
						'label' => __( 'Telephone', 'carton' ),
						'description' => ''
					),
				'shipping_email' => array(
						'label' => __( 'Email', 'carton' ),
						'description' => ''
					)
			)
		)
	));
	return $show_fields;
}


/**
 * Show Address Fields on edit user pages.
 *
 * @access public
 * @param mixed $user User (object) being displayed
 * @return void
 */
function carton_customer_meta_fields( $user ) {
	if ( ! current_user_can( 'manage_carton' ) )
		return;

	$show_fields = carton_get_customer_meta_fields();

	foreach( $show_fields as $fieldset ) :
		?>
		<h3><?php echo $fieldset['title']; ?></h3>
		<table class="form-table">
			<?php
			foreach( $fieldset['fields'] as $key => $field ) :
				?>
				<tr>
					<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
					<td>
						<input type="text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>" class="regular-text" /><br/>
						<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
					</td>
				</tr>
				<?php
			endforeach;
			?>
		</table>
		<?php
	endforeach;
}

add_action( 'show_user_profile', 'carton_customer_meta_fields' );
add_action( 'edit_user_profile', 'carton_customer_meta_fields' );


/**
 * Save Address Fields on edit user pages
 *
 * @access public
 * @param mixed $user_id User ID of the user being saved
 * @return void
 */
function carton_save_customer_meta_fields( $user_id ) {
	if ( ! current_user_can( 'manage_carton' ) )
		return $columns;

 	$save_fields = carton_get_customer_meta_fields();

 	foreach( $save_fields as $fieldset )
 		foreach( $fieldset['fields'] as $key => $field )
 			if ( isset( $_POST[ $key ] ) )
 				update_user_meta( $user_id, $key, carton_clean( $_POST[ $key ] ) );
}

add_action( 'personal_options_update', 'carton_save_customer_meta_fields' );
add_action( 'edit_user_profile_update', 'carton_save_customer_meta_fields' );
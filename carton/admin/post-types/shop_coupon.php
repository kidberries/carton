<?php
/**
 * Admin functions for the shop_coupon post type.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/Coupons
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define Columns for the Coupons admin page.
 *
 * @access public
 * @param mixed $columns
 * @return array
 */
function carton_edit_coupon_columns($columns){

	$columns = array();

	$columns["cb"] 			= "<input type=\"checkbox\" />";
	$columns["coupon_code"] = __( 'Code', 'carton' );
	$columns["type"] 		= __( 'Coupon type', 'carton' );
	$columns["amount"] 		= __( 'Coupon amount', 'carton' );
	$columns["description"] = __( 'Description', 'carton' );
	$columns["products"]	= __( 'Product IDs', 'carton' );
	$columns["usage"] 		= __( 'Usage / Limit', 'carton' );
	$columns["expiry_date"] = __( 'Expiry date', 'carton' );

	return $columns;
}

add_filter( 'manage_edit-shop_coupon_columns', 'carton_edit_coupon_columns' );


/**
 * Values for Columns on the Coupons admin page.
 *
 * @access public
 * @param mixed $column
 * @return void
 */
function carton_custom_coupon_columns( $column ) {
	global $post, $carton;

	switch ( $column ) {
		case "coupon_code" :
			$edit_link = get_edit_post_link( $post->ID );
			$title = _draft_or_post_title();
			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );

			echo '<div class="code tips" data-tip="' . __( 'Edit coupon', 'carton' ) . '"><a href="' . esc_attr( $edit_link ) . '"><span>' . esc_html( $title ). '</span></a></div>';

			_post_states( $post );

			// Get actions
			$actions = array();

			if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
				if ( 'trash' == $post->post_status )
					$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
				elseif ( EMPTY_TRASH_DAYS )
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
				if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
			}

			$actions = apply_filters( 'post_row_actions', $actions, $post );

			echo '<div class="row-actions">';

			$i = 0;
			$action_count = sizeof($actions);

			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
			echo '</div>';

		break;
		case "type" :
			echo esc_html( $carton->get_coupon_discount_type( get_post_meta( $post->ID, 'discount_type', true ) ) );
		break;
		case "amount" :
			echo esc_html( get_post_meta( $post->ID, 'coupon_amount', true ) );
		break;
		case "products" :
			$product_ids = get_post_meta( $post->ID, 'product_ids', true );
			$product_ids = $product_ids ? array_map( 'absint', explode( ',', $product_ids ) ) : array();
			if ( sizeof( $product_ids ) > 0 )
				echo esc_html( implode( ', ', $product_ids ) );
			else
				echo '&ndash;';
		break;
		case "usage_limit" :
			$usage_limit = get_post_meta( $post->ID, 'usage_limit', true );

			if ( $usage_limit )
				echo esc_html( $usage_limit );
			else
				echo '&ndash;';
		break;
		case "usage" :
			$usage_count = absint( get_post_meta( $post->ID, 'usage_count', true ) );
			$usage_limit = esc_html( get_post_meta($post->ID, 'usage_limit', true) );

			if ( $usage_limit )
				printf( __( '%s / %s', 'carton' ), $usage_count, $usage_limit );
			else
				printf( __( '%s / &infin;', 'carton' ), $usage_count );
		break;
		case "expiry_date" :
			$expiry_date = get_post_meta($post->ID, 'expiry_date', true);

			if ( $expiry_date )
				echo esc_html( date_i18n( 'F j, Y', strtotime( $expiry_date ) ) );
			else
				echo '&ndash;';
		break;
		case "description" :
			echo wp_kses_post( $post->post_excerpt );
		break;
	}
}

add_action( 'manage_shop_coupon_posts_custom_column', 'carton_custom_coupon_columns', 2 );

/**
 * Show custom filters to filter coupons by type.
 *
 * @access public
 * @return void
 */
function carton_restrict_manage_coupons() {
	global $carton, $typenow, $wp_query;

	if ( $typenow != 'shop_coupon' )
		return;

	// Type
	?>
	<select name='coupon_type' id='dropdown_shop_coupon_type'>
		<option value=""><?php _e( 'Show all statuses', 'carton' ); ?></option>
		<?php
			$types = $carton->get_coupon_discount_types();

			foreach ( $types as $name => $type ) {
				echo '<option value="' . esc_attr( $name ) . '"';

				if ( isset( $_GET['coupon_type'] ) )
					selected( $name, $_GET['coupon_type'] );

				echo '>' . esc_html__( $type, 'carton' ) . '</option>';
			}
		?>
		</select>
	<?php

	$carton->add_inline_js( "
		jQuery('select#dropdown_shop_coupon_type, select[name=m]').css('width', '150px').chosen();
	" );
}

add_action( 'restrict_manage_posts', 'carton_restrict_manage_coupons' );

/**
 * Filter the coupons by the type.
 *
 * @access public
 * @param mixed $vars
 * @return array
 */
function carton_coupons_by_type_query( $vars ) {
	global $typenow, $wp_query;
    if ( $typenow == 'shop_coupon' && ! empty( $_GET['coupon_type'] ) ) {

		$vars['meta_key'] = 'discount_type';
		$vars['meta_value'] = carton_clean( $_GET['coupon_type'] );

	}

	return $vars;
}

add_filter( 'request', 'carton_coupons_by_type_query' );
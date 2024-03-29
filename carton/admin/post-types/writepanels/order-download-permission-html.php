<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="wc-metabox closed">
	<h3 class="fixed">
		<button type="button" rel="<?php echo absint( $download->product_id ) . ',' . esc_attr( $download->download_id ); ?>" class="revoke_access button"><?php _e( 'Revoke Access', 'carton' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'carton' ); ?>"></div>
		<strong>
			<?php echo '#' . absint( $product->id ) . ' &mdash; ' . apply_filters( 'carton_admin_download_permissions_title', $product->get_title(), $download->product_id, $download->order_id, $download->order_key, $download->download_id ) . ' &mdash; ' . sprintf( __( 'File %d: %s', 'carton' ), $file_count, basename( $product->get_file_download_path( $download->download_id ) ) ) . ' &mdash; ' . sprintf( _n('Downloaded %s time', 'Downloaded %s times', absint( $download->download_count ), 'carton'), absint( $download->download_count ) ); ?>
		</strong>
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
		<tbody>
			<tr>
				<td>
					<label><?php _e( 'Downloads Remaining', 'carton' ); ?>:</label>
					<input type="hidden" name="product_id[<?php echo $loop; ?>]" value="<?php echo absint( $download->product_id ); ?>" />
					<input type="hidden" name="download_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $download->download_id ); ?>" />
					<input type="number" step="1" min="0" class="short" name="downloads_remaining[<?php echo $loop; ?>]" value="<?php echo esc_attr( $download->downloads_remaining ); ?>" placeholder="<?php _e( 'Unlimited', 'carton' ); ?>" />
				</td>
				<td>
					<label><?php _e( 'Access Expires', 'carton' ); ?>:</label>
					<input type="text" class="short date-picker" name="access_expires[<?php echo $loop; ?>]" value="<?php echo $download->access_expires > 0 ? date_i18n( 'Y-m-d', strtotime( $download->access_expires ) ) : ''; ?>" maxlength="10" placeholder="<?php _e( 'Never', 'carton' ); ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
				</td>
			</tr>
		</tbody>
	</table>
</div>
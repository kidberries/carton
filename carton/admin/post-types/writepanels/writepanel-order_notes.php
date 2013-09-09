<?php
/**
 * Order Notes
 *
 * Functions for displaying order comments in admin.
 *
 * @author 		CartonThemes
 * @category 	Admin
 * @package 	CartoN/Admin/WritePanels
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Display the order notes meta box.
 *
 * @access public
 * @return void
 */
function carton_order_notes_meta_box() {
	global $carton, $post;

	$args = array(
		'post_id' 	=> $post->ID,
		'approve' 	=> 'approve',
		'type' 		=> 'order_note'
	);

	$notes = get_comments( $args );

	echo '<ul class="order_notes">';

	if ( $notes ) {
		foreach( $notes as $note ) {
			$note_classes = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? array( 'customer-note', 'note' ) : array( 'note' );

			?>
			<li rel="<?php echo absint( $note->comment_ID ) ; ?>" class="<?php echo implode( ' ', $note_classes ); ?>">
				<div class="note_content">
					<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
				</div>
				<p class="meta">
					<?php printf( __( 'added %s ago', 'carton' ), human_time_diff( strtotime( $note->comment_date_gmt ), current_time( 'timestamp', 1 ) ) ); ?> <a href="#" class="delete_note"><?php _e( 'Delete note', 'carton' ); ?></a>
				</p>
			</li>
			<?php
		}
	} else {
		echo '<li>' . __( 'There are no notes for this order yet.', 'carton' ) . '</li>';
	}

	echo '</ul>';
	?>
	<div class="add_note">
		<h4><?php _e( 'Add note', 'carton' ); ?> <img class="help_tip" data-tip='<?php esc_attr_e( 'Add a note for your reference, or add a customer note (the user will be notified).', 'carton' ); ?>' src="<?php echo $carton->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></h4>
		<p>
			<textarea type="text" name="order_note" id="add_order_note" class="input-text" cols="20" rows="5"></textarea>
		</p>
		<p>
			<select name="order_note_type" id="order_note_type">
				<option value="customer"><?php _e( 'Customer note', 'carton' ); ?></option>
				<option value=""><?php _e( 'Private note', 'carton' ); ?></option>
			</select>
			<a href="#" class="add_note button"><?php _e( 'Add', 'carton' ); ?></a>
		</p>
	</div>
	<script type="text/javascript">

		jQuery('#carton-order-notes')

		.on( 'click', 'a.add_note', function() {

			if (!jQuery('textarea#add_order_note').val()) return;

			jQuery('#carton-order-notes').block({ message: null, overlayCSS: { background: '#fff url(<?php echo $carton->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			var data = {
				action: 		'carton_add_order_note',
				post_id:		'<?php echo $post->ID; ?>',
				note: 			jQuery('textarea#add_order_note').val(),
				note_type:		jQuery('select#order_note_type').val(),
				security: 		'<?php echo wp_create_nonce("add-order-note"); ?>'
			};

			jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {

				jQuery('ul.order_notes').prepend( response );
				jQuery('#carton-order-notes').unblock();
				jQuery('#add_order_note').val('');

			});

			return false;

		})

		.on( 'click', 'a.delete_note', function() {

			var note = jQuery(this).closest('li.note');

			jQuery(note).block({ message: null, overlayCSS: { background: '#fff url(<?php echo $carton->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			var data = {
				action: 		'carton_delete_order_note',
				note_id:		jQuery(note).attr('rel'),
				security: 		'<?php echo wp_create_nonce("delete-order-note"); ?>'
			};

			jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {

				jQuery(note).remove();

			});

			return false;

		});

	</script>
	<?php
}
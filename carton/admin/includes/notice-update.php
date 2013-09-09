<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
/*
 * postgreSql
 * close update window
<div id="message" class="updated carton-message wc-connect">
	<div class="squeezer">
		<h4><?php _e( '<strong>Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'carton' ); ?></h4>
		<p class="submit"><a href="<?php echo add_query_arg( 'do_update_carton', 'true', admin_url('admin.php?page=carton_settings') ); ?>" class="wc-update-now button-primary"><?php _e( 'Run the updater', 'carton' ); ?></a></p>
	</div>
</div>
<script type="text/javascript">
	jQuery('.wc-update-now').click('click', function(){
		var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'carton' ); ?>' );
		return answer;
	});
</script>
 * 
 */
?>
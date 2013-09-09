<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="updated carton-message wc-connect">
	<div class="squeezer">
		<h4><?php _e( '<strong>Welcome to CartoN</strong> &#8211; You\'re almost ready to start selling :)', 'carton' ); ?></h4>
		<p class="submit"><a href="<?php echo add_query_arg('install_carton_pages', 'true', admin_url('admin.php?page=carton_settings') ); ?>" class="button-primary"><?php _e( 'Install CartoN Pages', 'carton' ); ?></a> <a class="skip button-primary" href="<?php echo add_query_arg('skip_install_carton_pages', 'true', admin_url('admin.php?page=carton_settings') ); ?>"><?php _e( 'Skip setup', 'carton' ); ?></a></p>
	</div>
</div>
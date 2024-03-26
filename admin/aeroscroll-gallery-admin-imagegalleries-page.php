<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.aeroscroll.com
 * @since      1.0.0
 *
 * @package    Aeroscroll_Gallery
 * @subpackage Aeroscroll_Gallery/admin
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div>
	<!-- <h3 class="aeroscroll-page-title">Aeroscroll Image Collections</h3> -->

	<div id="wp-vue-app-admin"></div>
	<?php
		printf( '<script>window["MEDIA_URL"] = "%s";</script>', esc_attr( plugin_dir_url( __FILE__ ) ) );
	?>
	
	<?php
	if ( extension_loaded( 'gd' ) ) {
		printf( '<script>window.gdloaded = true;</script>' );
	} else {
		printf( '<script>window.gdloaded = false;</script>' );
	}
	?>
</div>

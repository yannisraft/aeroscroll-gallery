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
    <div id="wp-vue-app-admin"></div>
    <?php 
        printf( '<script>window["MEDIA_URL"] = "%s";</script>', esc_attr(  plugin_dir_url( __FILE__ ) ) );
    ?>    
</div>
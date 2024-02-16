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
    <?php echo '<script>window["MEDIA_URL"] = "'.plugin_dir_url( __FILE__ ).'";</script>' ?>

    <?php
    if (extension_loaded('gd')) {
        echo '<script>window.gdloaded = true;</script>';
    } else {
        echo '<script>window.gdloaded = false;</script>';
    }
    ?>

    <!-- <?php
    settings_fields( 'aeroscrollgrid' );
    do_settings_sections( 'aeroscrollgrid' );
    submit_button();
    ?> -->
</div>
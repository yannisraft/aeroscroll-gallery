<?php
/*
* Plugin Name: Aeroscroll Gallery
* Plugin URI: https://www.aeroscroll.com
* Description: Aeroscroll Gallery empowers you to create captivating and interactive image galleries like never before. With a diverse set of advanced features, our plugin offers a seamless and immersive image browsing experience that leaves a lasting impression on your website visitors.
* Version: 1.0.0.0
* Author: Aeroscroll Team
* Author URI: https://www.aeroscroll.com
* License: GPL2
* Text Domain: aeroscroll-gallery
* Tested up to: 6.4.2
* Requires PHP: 7.0
* Requires at least: 4.8
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    exit;
}

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Currently plugin version
 */
define('AEROSCROLL_GALLERY_VERSION', '1.0.0.0');
define('AEROSCROLL_GALLERY_ITERATION', '1');
define("PLUGIN_CHECK_TRANSIENT_EXPIRATION", 3600); // 12 hours


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aeroscroll-gallery-activator.php
 */
function activate_aeroscroll_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-aeroscroll-gallery-activator.php';
    Aeroscroll_Gallery_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aeroscroll-gallery-deactivator.php
 */
function deactivate_aeroscroll_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-aeroscroll-gallery-deactivator.php';
    Aeroscroll_Gallery_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_aeroscroll_gallery');
register_deactivation_hook(__FILE__, 'deactivate_aeroscroll_gallery');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-aeroscroll-gallery.php';
require_once plugin_dir_path(__FILE__) . 'class-aeroscroll-gallery-autoupdater.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_aeroscroll_gallery()
{
    $plugin = new Aeroscroll_Gallery('aeroscroll-gallery');
    $plugin->run();

    $aeroscroll_autoupdater = new AeroscrollGalleryAutoUpdater();
}

run_aeroscroll_gallery();

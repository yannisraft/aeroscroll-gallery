<?php
/*
* Plugin Name: Aeroscroll Gallery
* Plugin URI: https://github.com/yannisraft/aeroscroll-gallery
* Description: Aeroscroll Gallery empowers you to create captivating and interactive image galleries like never before. With a diverse set of advanced features, our plugin offers a seamless and immersive image browsing experience that leaves a lasting impression on your website visitors.
* Version: 1.0.12
* Author: Aeroscroll Team
* Author URI: https://www.aeroscroll.com
* License: GPL2
* Text Domain: aeroscroll-gallery
* Tested up to: 6.6.2
* Requires PHP: 7.1
* Requires at least: 4.8
* Stable tag: 1.0.12
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
if (! defined('WPINC')) {
    exit;
}

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Currently plugin version
 */
define('AEROSCROLL_GALLERY_VERSION', '1.0.12');
define('AEROSCROLL_GALLERY_ITERATION', '14');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aeroscroll-gallery-activator.php
 */
function aeroscroll_activate_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-aeroscroll-gallery-activator.php';
    Aeroscroll_Gallery_Activator::aeroscroll_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aeroscroll-gallery-deactivator.php
 */
function aeroscroll_deactivate_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-aeroscroll-gallery-deactivator.php';
    Aeroscroll_Gallery_Deactivator::aeroscroll_deactivate();
}

register_activation_hook(__FILE__, 'aeroscroll_activate_plugin');
register_deactivation_hook(__FILE__, 'aeroscroll_deactivate_plugin');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-aeroscroll-gallery.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function aeroscroll_func_run()
{
    $plugin = new Aeroscroll_Gallery('aeroscroll-gallery');
    $plugin->aeroscroll_run();
}

aeroscroll_func_run();

<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.aeroscroll.com
 * @since      1.0.0
 *
 * @package    Aeroscroll_Gallery
 * @subpackage Aeroscroll_Gallery/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Aeroscroll_Gallery
 * @subpackage Aeroscroll_Gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Aeroscroll_Gallery_I18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function aeroscroll_load_plugin_textdomain() {

		load_plugin_textdomain(
			'aeroscroll-gallery',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}

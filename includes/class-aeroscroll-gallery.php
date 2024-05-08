<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.aeroscroll.com
 * @since      1.0.0
 *
 * @package    aeroscroll_gallery
 * @subpackage aeroscroll_gallery/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Aeroscroll_gallery
 * @subpackage aeroscroll_gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __DIR__ ) . 'includes/class-aeroscroll-utils.php';

use Aeroscroll\Aeroscroll_Utils;

if ( ! class_exists( 'Aeroscroll_Gallery' ) ) {
	#[AllowDynamicProperties]
	class Aeroscroll_Gallery {


		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      aeroscroll_gallery_Loader    $loader    Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $version    The current version of the plugin.
		 */
		protected $version;

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct( $name ) {
			if ( defined( 'AEROSCROLL_GALLERY_VERSION' ) ) {
				$this->version = AEROSCROLL_GALLERY_VERSION;
			} else {
				$this->version = '1.0.0';
			}
			$this->plugin_name = $name;
			$this->utils       = new Aeroscroll_Utils();

			$this->aeroscroll_load_dependencies();
			$this->aeroscroll_set_locale();
			$this->aeroscroll_define_admin_hooks();
			if ( ! $this->utils->aeroscroll_has_pro() ) {
				$this->aeroscroll_define_public_hooks();
			}
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - aeroscroll_gallery_Loader. Orchestrates the hooks of the plugin.
		 * - Aeroscroll_Gallery_I18n. Defines internationalization functionality.
		 * - Aeroscroll_Gallery_Admin. Defines all hooks for the admin area.
		 * - aeroscroll_gallery_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function aeroscroll_load_dependencies() {
			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'includes/class-aeroscroll-gallery-loader.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'includes/class-aeroscroll-gallery-i18n.php';

			/**
			 * The class responsible for defining all custome REST API endpointd
			 *
			 */
			if ( ! $this->utils->aeroscroll_has_pro() ) {
				require_once plugin_dir_path( __DIR__ ) . 'includes/class-aeroscroll-gallery-custom-endpoint.php';
				$this->customendpoint = new Aeroscroll_Gallery_Custom_Endpoint();
			}

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( __DIR__ ) . 'admin/class-aeroscroll-gallery-admin.php';

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			if ( ! $this->utils->aeroscroll_has_pro() ) {
				require_once plugin_dir_path( __DIR__ ) . 'public/class-aeroscroll-gallery-public.php';
			}

			$this->loader = new aeroscroll_gallery_Loader();
		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the Aeroscroll_Gallery_I18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function aeroscroll_set_locale() {
			$plugin_i18n = new Aeroscroll_Gallery_I18n();
			$this->loader->aeroscroll_add_action( 'plugins_loaded', $plugin_i18n, 'aeroscroll_load_plugin_textdomain' );
		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function aeroscroll_define_admin_hooks() {
			$plugin_admin = new Aeroscroll_Gallery_Admin( $this->aeroscroll_get_plugin_name(), $this->aeroscroll_get_version() );

			if ( ! $this->utils->aeroscroll_has_pro() ) {
				$this->loader->aeroscroll_add_action( 'admin_menu', $plugin_admin, 'aeroscroll_register_settings_page' );
			}

			$this->loader->aeroscroll_add_action( 'admin_print_styles', $plugin_admin, 'aeroscroll_enqueue_styles' );
			$this->loader->aeroscroll_add_action( 'admin_enqueue_scripts', $plugin_admin, 'aeroscroll_enqueue_scripts' );
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function aeroscroll_define_public_hooks() {
			$plugin_public = new Aeroscroll_Gallery_Public( $this->aeroscroll_get_plugin_name(), $this->aeroscroll_get_version() );
			$this->loader->aeroscroll_add_action( 'wp_enqueue_scripts', $plugin_public, 'aeroscroll_enqueue_styles', 1 );
			$this->loader->aeroscroll_add_action( 'wp_enqueue_scripts', $plugin_public, 'aeroscroll_enqueue_scripts', 1 );
			$this->customendpoint->aeroscroll_cendpoints_init();

			// Add our Shortcodes
			// 1. First retrieve all aeroscroll grids from database
			global $wpdb;
			$grids = $wpdb->get_results( "SELECT shortcode FROM {$wpdb->prefix}aeroscroll_gallery" );

			// 2. Then register all shortcodes for active grids
			foreach ( $grids as $grid ) {
				$grid_short_code = 'aeroscroll_' . $grid->shortcode;
				$this->loader->aeroscroll_add_shortcode( $grid_short_code, $plugin_public, 'aeroscroll_register_grid_shortcode' );
			}
		}
		/****** */

		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function aeroscroll_run() {
			$this->loader->aeroscroll_run();
		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    string    The name of the plugin.
		 */
		public function aeroscroll_get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    aeroscroll_gallery_Loader    Orchestrates the hooks of the plugin.
		 */
		public function aeroscroll_get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since     1.0.0
		 * @return    string    The version number of the plugin.
		 */
		public function aeroscroll_get_version() {
			return $this->version;
		}
	}
}

<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.aeroscroll.com
 * @since      1.0.0
 *
 * @package    aeroscroll_gallery
 * @subpackage aeroscroll_gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    aeroscroll_gallery
 * @subpackage aeroscroll_gallery/admin
 * @author     Aeroscroll Team <info@aeroscroll.com>
 */

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/aeroscroll-utils.php';

use Aeroscroll\utils;

#[AllowDynamicProperties]
class aeroscroll_gallery_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Utils Reference   
     */
    private $utils;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->set_options();
        $this->check_updates_required();
        $this->utils = new utils();
    }

    public function check_updates_required()
    {
        // FOR BACKWARDS COMPATIBILITY 

        // Update Version 0.94
        $this->checkupdate_094();

        // Update Version 0.95
        $this->checkupdate_095();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in aeroscroll_gallery_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The aeroscroll_gallery_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $screen = get_current_screen();
        $aeroscroll_gallery_iteration = $this->utils->get_iteration();

        if (strpos($screen->id, 'aeroscroll-gallery') !== false) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '/css/aeroscroll-gallery-admin.css?v=' . $aeroscroll_gallery_iteration, array(), $this->version, 'all');
            wp_enqueue_style('app.css', plugin_dir_url(__FILE__) . 'dist/css/app.css?v=' . $aeroscroll_gallery_iteration, array(), $this->version, 'all');
        }
    }

    public function get_plugin_translations()
    {
        $language_data = array(
            'locale' => get_locale(),
            'Manage Galleries' => __('Manage Galleries', 'aeroscroll-gallery'),
            'Published' => __('Published', 'aeroscroll-gallery'),
            'Operation' => __('Operation', 'aeroscroll-gallery'),
            'Title' => __('Title', 'aeroscroll-gallery'),
        );

        return $language_data;
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix)
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in aeroscroll_gallery_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The aeroscroll_gallery_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $aeroscroll_gallery_iteration = $this->utils->get_iteration();        

        $screen = get_current_screen();
        if (strpos($screen->id, 'aeroscroll-gallery') !== false) {
            if (strpos($screen->id, 'toplevel_page_aeroscroll-gallery-home') !== false) {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/homepage.js', '', $this->version, true);                
            } else if (strpos($screen->id, 'aeroscroll-gallery_page_aeroscroll-gallery-imagegalleries') !== false) {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/imagegalleries.js', '', $this->version, true);
            } else if (strpos($screen->id, 'aeroscroll-gallery_page_aeroscroll-gallery-help') !== false) {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/help.js', '', $this->version, true);
            } else if (strpos($screen->id, 'aeroscroll-gallery_page_aeroscroll-gallery-settings') !== false) {
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/settings.js', '', $this->version, true);
            }

            wp_enqueue_script('app.js', plugin_dir_url(__FILE__) . 'dist/js/app.js?v=' . $aeroscroll_gallery_iteration, '', $this->version, true);
            wp_enqueue_script('chunk-vendors.js', plugin_dir_url(__FILE__) . 'dist/js/chunk-vendors.js?v=' . $aeroscroll_gallery_iteration, '', $this->version, true);

            $TRANSLATIONS = $this->get_plugin_translations();
            wp_localize_script('app.js', 'TRANSLATIONS', $TRANSLATIONS);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '/js/aeroscroll-gallery-admin.js', array('jquery'), $this->version, true);

            $APEX_obj = array(
                'strings' => array(
                    'saved' => __('Settings Saved', 'text-domain'),
                    'error' => __('Error', 'text-domain')
                ),
                'settings'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/settings')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'getgrids'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/getgrids')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'updategrid'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/updategrid')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'addgrid'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/addgrid')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'deletegrid'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/deletegrid')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'getimagegalleries'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/getimagegalleries')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'addimagegallery'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/addimagegallery')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'updateimagegallery'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/updateimagegallery')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'deleteimagegallery'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/deleteimagegallery')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'uploadimages'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/uploadimages')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'listfolder'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/listfolder')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'deleteitem'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/deleteitem')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'renameitem'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/renameitem')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'createfolder'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/createfolder')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'addgalleryimages'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/addgalleryimages')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'getgalleryimages'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/getgalleryimages')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'deletegalleryimages'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/deletegalleryimages')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'updategalleryimages'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/updategalleryimages')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
                'activateonempremium'     => array(
                    'url'   => esc_url_raw(rest_url('aeroscroll/v1/activateonempremium')),
                    'nonce' => wp_create_nonce('wp_rest')
                ),
            );

            

            wp_localize_script($this->plugin_name, 'APEX', $APEX_obj);
            wp_localize_script('app.js', 'APEX', $APEX_obj);

            // Pass window Parameters to JS files
            $rest_url = array(
                'url' => get_bloginfo('url')
            );
            wp_localize_script('app.js', 'REST_URL', $rest_url);

            $is_pro = array(
                'is_pro' => $this->utils->is_pro() ? 'true' : 'false'
            );
            wp_localize_script('app.js', 'IS_PRO', $is_pro);
        }
    }

    function dequeue_css()
    {
        // Dequeue
        wp_dequeue_style('caldera-forms-admin-icon-styles');
        wp_dequeue_style('et-core-admin');
        wp_dequeue_style('et-core-portability');
        wp_dequeue_style('wf-common-style');
        wp_dequeue_style('wf-fedex-style');
        wp_dequeue_style('woocommercebulkdiscount-style-admin');
        wp_dequeue_style('tm_epo_admin_css');
        wp_dequeue_style('tc-font-awesome');
        wp_dequeue_style('tm_global_epo_animate_css');
        wp_dequeue_style('tm_global_epo_admin_css');
        wp_dequeue_style('tm_global_epo_admin_font');
        wp_dequeue_style('tm-spectrum');
        wp_dequeue_style('tc-font-awesome');
        wp_dequeue_style('yoast-seo-admin-global');
        wp_dequeue_style('yoast-seo-dismissible');
        wp_dequeue_style('et-meta-box-style');
        wp_dequeue_style('et-core-version-rollback');
        wp_dequeue_style('library-menu-styles');
        wp_dequeue_style('woocommerce_admin_menu_styles');
        wp_dequeue_style('woocommerce_admin_styles');
        wp_dequeue_style('yoast-seo-toggle-switch');
        wp_dequeue_style('yoast-seo-admin-css');
        wp_dequeue_style('woocommerce-activation');
        wp_dequeue_style('dashicons');


        // Deregister
        wp_deregister_style('caldera-forms-admin-icon-styles');
        wp_deregister_style('et-core-admin');
        wp_deregister_style('et-core-portability');
        wp_deregister_style('wf-common-style');
        wp_deregister_style('wf-fedex-style');
        wp_deregister_style('woocommercebulkdiscount-style-admin');
        wp_deregister_style('tm_epo_admin_css');
        wp_deregister_style('tc-font-awesome');
        wp_deregister_style('tm_global_epo_animate_css');
        wp_deregister_style('tm_global_epo_admin_css');
        wp_deregister_style('tm_global_epo_admin_font');
        wp_deregister_style('tm-spectrum');
        wp_deregister_style('tc-font-awesome');
        wp_deregister_style('yoast-seo-admin-global');
        wp_deregister_style('yoast-seo-dismissible');
        wp_deregister_style('et-meta-box-style');
        wp_deregister_style('et-core-version-rollback');
        wp_deregister_style('library-menu-styles');
        wp_deregister_style('woocommerce_admin_menu_styles');
        wp_deregister_style('woocommerce_admin_styles');
        wp_deregister_style('yoast-seo-toggle-switch');
        wp_deregister_style('yoast-seo-admin-css');
        wp_deregister_style('woocommerce-activation');
        wp_deregister_style('dashicons');
    }

    /**
     * Add Menu in the sidebar
     *
     * @since    1.0.0
     */
    function register_aeroscroll_settings_page()
    {        
        //The icon in Base64 format
        $icon_base64 = 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS4zLjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAxNTAgMTUwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxNTAgMTUwOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KCS5zdDB7ZmlsbDojNTM5QkQ0O30NCgkuc3Qxe2ZpbGw6IzJFNkI5NDt9DQo8L3N0eWxlPg0KPGc+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTYuMDEsMTExLjg2Yy0wLjA3LTAuMDEtMC4xLTAuMDItMC4yLTAuMDZjLTAuMzMtMC4xMy0wLjc0LTAuMjgtMC44Ni0wLjRjLTAuMTItMC4xMywwLjAzLTAuMjIsMC44Mi0wLjU1DQoJCWMwLjAzLTAuMDEsMC4xNy0wLjA2LDAuMjQtMC4wOGMwLjEtMC4wMSwxLjkzLTAuMzIsNC42My0wLjY5YzIuNy0wLjM3LDYuMjUtMC44Myw5LjgtMS4yMWMxLjc3LTAuMTksMy41NC0wLjM0LDUuMi0wLjQ5DQoJCWMxLjY2LTAuMTYsMy4yLTAuNDQsNC41My0wLjYyYzEuMzItMC4xOSwyLjQzLTAuMjMsMy4yLTAuMjJjMC43NywwLjAxLDEuMjEsMC4wNiwxLjIxLDAuMDZzMC4yOSwwLjA3LDAuNzcsMC4yMQ0KCQljMC40OCwwLjE0LDEuMTUsMC4zNiwxLjkzLDAuNjNjMS41NSwwLjUzLDMuNSwxLjMzLDUuMTEsMS45MmMxLjM3LDAuNDcsMi4yNSwwLjcyLDIuNzYsMC44M2MwLjUxLDAuMTIsMC42NCwwLjEyLDAuNSwwLjE2DQoJCWMtMC4yOSwwLjA2LTEuNjgsMC41Mi0zLjM3LDEuMTljMC4xOC0wLjAxLTEuODUsMC43LTMuOTIsMS4zNmMtMS4wMywwLjMzLTIuMDgsMC42My0yLjg3LDAuODRjLTAuNzksMC4yMS0xLjMxLDAuMzItMS4zMSwwLjMyDQoJCXMtMC40MywwLjA0LTEuMTksMC4wM2MtMC43NiwwLTEuODUtMC4wNy0zLjE1LTAuMjdjLTEuMy0wLjE5LTIuODMtMC40Ni00LjQ2LTAuNjFjLTEuNjMtMC4xNS0zLjM4LTAuMzEtNS4xMy0wLjUNCgkJYy0zLjUtMC4zOC03LTAuODQtOS42Ni0xLjJDNy45MiwxMTIuMTgsNi4xMSwxMTEuODgsNi4wMSwxMTEuODZ6Ii8+DQo8L2c+DQo8Zz4NCgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNMTA0LjIzLDIzLjAzSDkwLjU4TDUwLjM4LDEyNy41aDE0LjA4bDExLjU5LTMwLjM3aDAuMTRjMC4xNC0wLjU4LDAuNC0xLjEzLDAuNzctMS42DQoJCWMwLjY1LTEuOTksMS40OS0zLjkyLDIuMzctNS44MWMwLjMyLTAuNjgsMC42NC0xLjM3LDAuOTUtMi4wNWMwLjE1LTAuMzIsMC4yOS0wLjY0LDAuNDQtMC45N2MwLjAxLTAuMDMsMC4wNC0wLjEsMC4wOC0wLjE4DQoJCWMtMC4yMS0xLjI3LDAuMDctMi42NCwwLjktMy42MmwxNS43OC00MC45NGwxNS4zNSw0MC4xOWMwLjE4LDAuMzIsMC4zNSwwLjY1LDAuNDksMS4wMWMwLjYxLDEuNTMsMS4xNCwzLjA5LDEuNjksNC42NA0KCQljMC4yMSwwLjYsMC40NCwxLjIsMC42NiwxLjc5YzAuMDMsMC4wNywwLjA1LDAuMTUsMC4wOCwwLjIyYzAuMDQsMC4xLDAuMDcsMC4yLDAuMTEsMC4zMWMwLjA5LDAuMjMsMC4xOCwwLjQ2LDAuMjYsMC42OQ0KCQljMC4wMSwwLjAzLDAuMDMsMC4wNiwwLjA0LDAuMDljMSwyLjE1LDIuMjMsNC4yNiwyLjcyLDYuNThsMTEuNDUsMzAuMDJoMTQuMjNMMTA0LjIzLDIzLjAzeiIvPg0KPC9nPg0KPGc+DQoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTE0LjcyLDg5LjY1Yy0wLjM1LTAuMDUtMC40OC0wLjA3LTAuOTUtMC4yMWMtMS42MS0wLjQ1LTMuNTctMC45NS00LjE3LTEuMjNjLTAuNi0wLjMzLDAuMTUtMC40MiwzLjk4LTEuNTUNCgkJYzAuMTQtMC4wNCwwLjgxLTAuMjIsMS4xNC0wLjI5YzQuMDMtMC41OCw4LjA2LTEuMTMsMTIuMDktMS42M2M0LjAzLTAuNTEsOC4wNi0wLjk5LDEyLjA5LTEuNDVsNS45Mi0wLjYzDQoJCWM0LjQ5LTAuNDgsOC45OC0wLjg4LDEzLjQ4LTEuMjZjMCwwLDEuMzktMC4wMywzLjcxLTAuMDdjMi4zMi0wLjA0LDUuNTgtMC4yMiw5LjMyLTAuMzJjMS44Ny0wLjA0LDMuODctMC4wNCw1LjkyLDAuMQ0KCQljMi4wNiwwLjE0LDQuMTgsMC40MSw2LjMyLDAuNzljNC4yNywwLjc4LDguNTcsMS44MywxMi40NSwyLjc5YzMuMjksMC43Niw2LjAxLDEuMzksOC4yMiwxLjljMi4yLDAuNSwzLjg5LDAuODQsNS4xMywxLjA1DQoJCWMyLjQ3LDAuNDIsMy4xMSwwLjM2LDIuNDIsMC40OGMtMC42OSwwLjEyLTIuNzIsMC41Ny01LjU5LDEuMzZjLTIuODgsMC43OC02LjYsMS44Ny0xMC42OSwyLjg3YzAuNDMsMC4wMS0xLjgsMC41Ny01LjM5LDEuMjgNCgkJYy0zLjU5LDAuNy04LjUzLDEuNjItMTMuNTMsMS42N2MtNSwwLjA2LTEwLjA1LTAuNS0xMy44NS0wLjYxYy0zLjgtMC4xMi02LjM1LTAuMi02LjM1LTAuMmMtMy44Ni0wLjMzLTcuNzEtMC43LTExLjU3LTEuMTENCgkJbC01LjkyLTAuNjZjLTQuMDMtMC40Ni04LjA2LTAuOTQtMTIuMDktMS40NUMyMi43Nyw5MC43OCwxOC43NCw5MC4yMywxNC43Miw4OS42NXoiLz4NCjwvZz4NCjxnPg0KCTxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00OC42OCwzMS42NmMtMC4yLTAuMDMtMC4yNy0wLjA0LTAuNTItMC4xMmMtMC44OS0wLjI1LTEuOTgtMC41My0yLjMxLTAuNjhjLTAuMzMtMC4xOCwwLjA4LTAuMjMsMi4yLTAuODYNCgkJYzAuMDgtMC4wMiwwLjQ1LTAuMTIsMC42My0wLjE2YzQuNDYtMC42Niw4LjkyLTEuMjIsMTMuMzctMS43NGwxLjMyLTAuMTRjMi40OS0wLjI4LDQuOTctMC41Myw3LjQ2LTAuNzRjMCwwLDAuNzctMC4wMiwyLjA1LTAuMDQNCgkJYzEuMjgtMC4wMSwzLjA5LTAuMDgsNS4xNi0wLjE2YzIuMDctMC4wOCw0LjQxLTAuMDQsNi43NywwLjM3YzIuMzYsMC40LDQuNzQsMC45OCw2Ljg5LDEuNTJjMy42NSwwLjg1LDYuMDIsMS40Miw3LjM5LDEuNjUNCgkJYzEuMzYsMC4yMywxLjcyLDAuMiwxLjM0LDAuMjZjLTAuMzgsMC4wNy0xLjUsMC4zMi0zLjEsMC43NmMtMS41OSwwLjQ0LTMuNjUsMS4wNC01LjkyLDEuNmMwLjI0LDAtMSwwLjMxLTIuOTgsMC43DQoJCWMtMS45OCwwLjM5LTQuNzIsMC44Ni03LjQ4LDAuODFjLTIuNzYtMC4wNC01LjU2LTAuMzQtNy42Ni0wLjM3Yy0yLjEtMC4wNy0zLjUxLTAuMTEtMy41MS0wLjExYy0yLjEzLTAuMTktNC4yNy0wLjQxLTYuNC0wLjY2DQoJCWwtMS4zMi0wLjE1QzU3LjYsMzIuODksNTMuMTQsMzIuMzMsNDguNjgsMzEuNjZ6Ii8+DQo8L2c+DQo8L3N2Zz4NCg==';

        //The icon in the data URI scheme
        $icon_data_uri = 'data:image/svg+xml;base64,' . $icon_base64;

        // 1. Main Menu Page
        add_menu_page('Aeroscroll Gallery', 'Aeroscroll Gallery', 'manage_options', 'aeroscroll-gallery-home', array($this, 'display_home_page'), $icon_data_uri);


        // 1a. Submenu Page Manage Galleries
        add_submenu_page(
            'aeroscroll-gallery-home',
            __('Manage Galleries', 'aeroscroll-gallery'), ///page title
            __('Manage Galleries', 'aeroscroll-gallery'), //menu title
            'manage_options', //capability,
            'aeroscroll-gallery-home', //menu slug
            array($this, 'display_home_page') //callback function
        );

        // 1b. Submenu Page Manage Galleries
        add_submenu_page(
            'aeroscroll-gallery-home',
            __('Image Collections', 'aeroscroll-gallery'), ///page title
            __('Image Collections', 'aeroscroll-gallery'), ///menu title
            'manage_options', //capability,
            'aeroscroll-gallery-imagegalleries', //menu slug
            array($this, 'display_imagegalleries_page') //callback function
        );


        

        // 1d. Submenu Page Support
        add_submenu_page(
            'aeroscroll-gallery-home',
            __('Help', 'aeroscroll-gallery'), ///page title
            __('Help', 'aeroscroll-gallery'), //menu title
            'manage_options', //capability,
            'aeroscroll-gallery-help', //menu slug
            array($this, 'display_help_page') //callback function
        );

        
        // 1e. Submenu Go PRO
        if (!$this->utils->is_pro()) {
            add_submenu_page(
                'aeroscroll-gallery-home',
                __('Get PRO', 'aeroscroll-gallery'), ///page title
                __('Get PRO', 'aeroscroll-gallery'), //menu title
                'manage_options', //capability,
                'aeroscroll-gallery-gopro', //menu slug
                array($this, 'redirect_topro') //callback function                
            );
        }
        
    }


    /**
     * Display the home page content for the page we have created.
     *
     * @since    1.0.0
     */
    public function display_home_page()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/aeroscroll-gallery-admin-home-page.php';
    }

    /**
     * Display the image galleries page content for the page we have created.
     *
     * @since    1.0.0
     */
    public function display_imagegalleries_page()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/aeroscroll-gallery-admin-imagegalleries-page.php';
    }

    /**
     * Display the image galleries page content for the page we have created.
     *
     * @since    1.0.0
     */
    public function display_settings_page()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/aeroscroll-gallery-admin-settings-page.php';
    }

    /**
     * Display the image galleries page content for the page we have created.
     *
     * @since    1.0.0
     */
    public function display_help_page()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/aeroscroll-gallery-admin-help-page.php';
    }

    /**
     * Redirect user to the Aeroscroll Website 
     *
     * @since    1.0.0
     */
    public function redirect_topro()
    {
        echo("<script>location.href = 'https://www.aeroscroll.com/#pricing';</script>");
        exit;
    }



    /**
     * Register the settings for our settings page.
     *
     * @since    1.0.0
     */
    public function register_aeroscroll_settings()
    {        
        // Here we are going to register our setting.
        register_setting(
            'aeroscrollgallery',
            'aeroscrollgallery_settings',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => NULL,
            )
        );

        // Here we are going to add a section for our setting.
        add_settings_section(
            'aeroscrollgallery_settings_section',
            __('Settings', 'aeroscroll-gallery'),
            array($this, 'aeroscrollggallery_add_settings_section'),
            'aeroscrollgallery'
        );

        add_settings_field(
            'orientation_select_field',
            __('Orientation', 'aeroscroll-gallery'),
            array($this, 'orientation_select_field_render'),
            'aeroscrollgallery',
            'aeroscrollgallery_settings_section'
        );
    }

    public function orientation_select_field_render()
    {
        $orientation = get_option('aeroscrollgallery_settings');
?>

        <select name='aeroscrollgallery_settings'>
            <option value='vertical' <?php if ($orientation != 'horizontal') echo 'selected' ?>>Vertical</option>
            <option value='horizontal' <?php if ($orientation == 'horizontal') echo 'selected' ?>>Horizontal</option>
        </select>

<?php
    }

    /**
     * Sandbox our settings.
     *
     * @since    1.0.0
     */
    public function aeroscroll_register_setting($input)
    {
        $new_input = array();

        if (isset($input)) {
            // Loop trough each input and sanitize the value if the input id isn't post-types
            foreach ($input as $key => $value) {
                if ($key == 'post-types') {
                    $new_input[$key] = $value;
                } else {
                    $new_input[$key] = sanitize_text_field($value);
                }
            }
        }

        return $new_input;
    }

    /**
     * Sets the class variable $options
     */
    private function set_options()
    {
        $this->options = get_option($this->plugin_name . '-options');
    } // set_options()

    // Update Version 0.9.4
    public function checkupdate_094()
    {
        $debug = "checkupdate_094";

        try {
            global $wpdb;
            $table = $wpdb->prefix . 'aeroscroll_gallery';
            $query = "SELECT column_name FROM information_schema.columns WHERE table_schema = '" . $wpdb->dbname . "' AND table_name = '" . $table . "';";
            $table_cols = $wpdb->get_results($query);

            $update_query = "";
            $hasupdate = false;

            $table_row = new stdClass();
            if (count($table_cols) > 0) {
                for ($k = 0; $k < count($table_cols); $k++) {
                    $table_row->{$table_cols[$k]->column_name} = "0";
                }
            }

            $debug .= " [ " . count($table_cols) . " ]";

            if (count($table_cols) > 0) {
                $update_query = "ALTER TABLE $table";
                if (!isset($table_row->autoscroll)) {
                    $update_query .= " ADD COLUMN autoscroll TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->numcolumns_mid)) {
                    $update_query .= " ADD COLUMN numcolumns_mid INT NOT NULL DEFAULT 2,";
                    $hasupdate = true;
                }

                if (!isset($table_row->numcolumns_low)) {
                    $update_query .= " ADD COLUMN numcolumns_low INT NOT NULL DEFAULT 1,";
                    $hasupdate = true;
                }

                if (!isset($table_row->sortby)) {
                    $update_query .= " ADD COLUMN sortby VARCHAR(30) NOT NULL DEFAULT 'id',";
                    $hasupdate = true;
                }

                if (!isset($table_row->sortbydir)) {
                    $update_query .= " ADD COLUMN sortbydir VARCHAR(30) NOT NULL DEFAULT 'ASC',";
                    $hasupdate = true;
                }

                if (mb_substr($update_query, -1) == ",") {
                    $update_query = substr($update_query, 0, -1);
                }

                if ($hasupdate) {
                    $update_query .= " AFTER title";

                    //echo "------------------------------------------------------------->" . $update_query;

                    $debug .= " [ " . $update_query . " ]";
                    $update_query .= ";";
                    $wpdb->get_results($update_query);

                    if ($wpdb->last_error !== '') :
                        $wpdb->print_error();
                    endif;
                }
            }


            // Create the SETTINGS TABLE
            // ------------------
            // Let's not break the site with exception messages
            $wpdb->hide_errors();

            if (!function_exists('dbDelta')) {
                require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            }

            $collate = '';

            if ($wpdb->has_cap('collation')) {
                $collate = $wpdb->get_charset_collate();
            }

            $table_name = $wpdb->prefix . 'aeroscroll_gallery_settings';
            if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name) {
            } else {
                error_log('Table does not exist' . ': ' . print_r($table_name, true));

                $schema = "
                    CREATE TABLE {$wpdb->prefix}aeroscroll_gallery_settings (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,                    
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    email VARCHAR(120) DEFAULT '',
                    serial_key VARCHAR(256) DEFAULT '',
                    product_id VARCHAR(30) NOT NULL DEFAULT '',
                    identifier INT(1) DEFAULT '1',
                    instance VARCHAR(30) NOT NULL DEFAULT '',
                    PRIMARY KEY (id)
                ) $collate; INSERT IGNORE INTO '" . $table_name . "' (email,serial_key_product_id) VALUES ('','','');";

                dbDelta($schema);
            }
        } catch (Exception $ex) {
            echo $ex;
        }
    }

    public function checkupdate_095()
    {
        $debug = "checkupdate_094";

        try {
            global $wpdb;
            $table = $wpdb->prefix . 'aeroscroll_gallery';
            $query = "SELECT column_name FROM information_schema.columns WHERE table_schema = '" . $wpdb->dbname . "' AND table_name = '" . $table . "';";
            $table_cols = $wpdb->get_results($query);

            $update_query = "";
            $hasupdate = false;

            $table_row = new stdClass();
            if (count($table_cols) > 0) {
                for ($k = 0; $k < count($table_cols); $k++) {
                    $table_row->{$table_cols[$k]->column_name} = "0";
                }
            }

            $debug .= " [ " . count($table_cols) . " ]";

            if (count($table_cols) > 0) {
                $update_query = "ALTER TABLE $table";

                // SOCIAL
                if (!isset($table_row->social_share_facebook)) {
                    $update_query .= " ADD COLUMN social_share_facebook TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->social_share_twitter)) {
                    $update_query .= " ADD COLUMN social_share_twitter TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->social_share_pinterest)) {
                    $update_query .= " ADD COLUMN social_share_pinterest TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->social_share_instagram)) {
                    $update_query .= " ADD COLUMN social_share_instagram TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->social_share_tumblr)) {
                    $update_query .= " ADD COLUMN social_share_tumblr TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->social_share_email)) {
                    $update_query .= " ADD COLUMN social_share_email TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }


                // WATERMARK
                if (!isset($table_row->watermark_type)) {
                    $update_query .= " ADD COLUMN watermark_type TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->watermark_text)) {
                    $update_query .= " ADD COLUMN watermark_text VARCHAR(120) NOT NULL DEFAULT '',";
                    $hasupdate = true;
                }

                if (!isset($table_row->watermark_fontsize)) {
                    $update_query .= " ADD COLUMN watermark_fontsize INT NOT NULL DEFAULT 16,";
                    $hasupdate = true;
                }

                if (!isset($table_row->watermark_color)) {
                    $update_query .= " ADD COLUMN watermark_color VARCHAR(30) NOT NULL DEFAULT '#000000',";
                    $hasupdate = true;
                }

                if (!isset($table_row->watermark_opacity)) {
                    $update_query .= " ADD COLUMN watermark_opacity TINYINT DEFAULT 30,";
                    $hasupdate = true;
                }

                if (!isset($table_row->watermark_position)) {
                    $update_query .= " ADD COLUMN watermark_position TINYINT DEFAULT 7,";
                    $hasupdate = true;
                }

                if (!isset($table_row->watermark_image_url)) {
                    $update_query .= " ADD COLUMN watermark_image_url VARCHAR(2048) NOT NULL DEFAULT '',";
                    $hasupdate = true;
                }

                if (!isset($table_row->watermark_image_size)) {
                    $update_query .= " ADD COLUMN watermark_image_size TINYINT DEFAULT 20,";
                    $hasupdate = true;
                }


                // ADVERTISMENT
                if (!isset($table_row->advertisment_type)) {
                    $update_query .= " ADD COLUMN advertisment_type TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_text)) {
                    $update_query .= " ADD COLUMN advertisment_text VARCHAR(120) NOT NULL DEFAULT '',";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_link)) {
                    $update_query .= " ADD COLUMN advertisment_link VARCHAR(2048) NOT NULL DEFAULT '',";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_fontsize)) {
                    $update_query .= " ADD COLUMN advertisment_fontsize INT NOT NULL DEFAULT 16,";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_color)) {
                    $update_query .= " ADD COLUMN advertisment_color VARCHAR(30) NOT NULL DEFAULT '#000000',";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_opacity)) {
                    $update_query .= " ADD COLUMN advertisment_opacity TINYINT DEFAULT 30,";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_position)) {
                    $update_query .= " ADD COLUMN advertisment_position TINYINT DEFAULT 7,";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_image_url)) {
                    $update_query .= " ADD COLUMN advertisment_image_url VARCHAR(2048) NOT NULL DEFAULT '',";
                    $hasupdate = true;
                }

                if (!isset($table_row->advertisment_image_size)) {
                    $update_query .= " ADD COLUMN advertisment_image_size TINYINT DEFAULT 20,";
                    $hasupdate = true;
                }

                if (!isset($table_row->poweredbyactive)) {
                    $update_query .= " ADD COLUMN poweredbyactive TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (!isset($table_row->articleinlightbox)) {
                    $update_query .= " ADD COLUMN articleinlightbox TINYINT DEFAULT 0,";
                    $hasupdate = true;
                }

                if (mb_substr($update_query, -1) == ",") {
                    $update_query = substr($update_query, 0, -1);
                }

                if ($hasupdate) {
                    $update_query .= " AFTER title";

                    $debug .= " [ " . $update_query . " ]";
                    $update_query .= ";";
                    $wpdb->get_results($update_query);

                    if ($wpdb->last_error !== '') :
                        $wpdb->print_error();
                    endif;
                }
            }
        } catch (Exception $ex) {
            echo $ex;
        }
    }
}
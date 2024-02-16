<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.aeroscroll.com
 * @since      1.0.0
 *
 * @package    aeroscroll_gallery
 * @subpackage aeroscroll_gallery/public
 */

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/aeroscroll-utils.php';

use Aeroscroll\utils;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    aeroscroll_gallery
 * @subpackage aeroscroll_gallery/public
 * @author     Aeroscroll Team <info@aeroscroll.com>
 */


class aeroscroll_gallery_Public
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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->utils = new utils();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/aeroscroll-gallery-public.css', array(), $this->version, 'all');
        wp_enqueue_style('scroller', plugin_dir_url(__FILE__) . 'css/scroller.css', array(), $this->version, 'all');
        wp_enqueue_style('lightbox', plugin_dir_url(__FILE__) . 'css/lightbox.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
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

        wp_enqueue_script('wp_head', plugin_dir_url(__FILE__) . '../public/js/libs/vue.global.prod.js');
        wp_enqueue_script('vue_script', plugin_dir_url(__FILE__) . '../public/js/aeroscroll-gallery-vue-public.js');
        wp_register_script('axios', plugin_dir_url(__FILE__) . 'js/libs/axios.min.js', 'vue-js', null);
        wp_enqueue_script('axios');

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/aeroscroll-gallery-jquery-public.js', array('jquery'), $this->version, false);

        // Vue - Local WordPress Data Available to Vue
        global $post;
        if ($post) {
            wp_localize_script(
                'vue_aeroscroll_gallery', // vue script handle defined in wp_register_script.
                'wpData', // javascript object that will made availabe to Vue.
                array( // wordpress data to be made available to the Vue app in 'wpData'
                    'template_directory_uri' => get_stylesheet_directory_uri(), // child theme directory path.
                    //'rest_url' => untrailingslashit(esc_url_raw(rest_url())), // URL to the REST endpoint.
                    'app_path' => $post->post_name, // page where the custom page template is loaded.
                    'post_categories' => get_terms(array(
                        'taxonomy' => 'category', // default post categories.
                        'hide_empty' => true,
                        'fields' => 'names',
                    )),
                )
            );
        }
    }
    /**
     * Create Shortcode for Users to add the button.
     *
     * @since    1.0.0
     */
    public function register_grid_shortcode($attrs, $params, $code)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery';
        $final_shortcode = str_replace("aeroscroll_", "", $code);
        $query = "SELECT * FROM $table WHERE shortcode = '" . $final_shortcode . "' AND published = 1;";
        $grid = $wpdb->get_results($query);

        $output = '';


        if (sizeof($grid) > 0) {
            $height = $grid[0]->height;
            if ($height == null || $height == "") $height = "400px";

            $output .= '<!-- #vue-app -->
            <div id="app_aeroscroll_' . $final_shortcode . '"></div>
            <!-- !#vue-app -->';

            $output .= '<style>
                    .aeroscroll-container {
                        margin: 0px auto;
                    }
                    #aeroscroll-container-' . $grid[0]->id . ' {
                        height: ' . $height . ';
                    }
                </style>';

            $hasScrollbar = ($grid[0]->scrollbar === 1 || $grid[0]->scrollbar === "1") ? 'true' : 'false';
            $cellSquared = ($grid[0]->cellsquared === 1 || $grid[0]->cellsquared === "1") ? 'true' : 'false';
            $themeonhover = ($grid[0]->themeonhover === 1 || $grid[0]->themeonhover === "1") ? 'true' : 'false';
            $showreadmore = ($grid[0]->showreadmore === 1 || $grid[0]->showreadmore === "1") ? 'true' : 'false';
            $articleinlightbox = ($grid[0]->articleinlightbox === 1 || $grid[0]->articleinlightbox === "1") ? 'true' : 'false';
            $autoscroll = ($grid[0]->autoscroll === 1 || $grid[0]->autoscroll === "1") ? 'true' : 'false';
            $hoveranimation = "none";

            if ($grid[0]->scrollbar != null) $hoveranimation = $grid[0]->hoveranimation;

            $cellsize = 100;
            if (isset($grid[0]->cellsize)) {
                $cellsize = $grid[0]->cellsize;
                if (gettype($cellsize == 'string')) $cellsize = (int)$grid[0]->cellsize;
            }

            $sidegap = 0;
            if (isset($grid[0]->sidegap)) {
                $sidegap = $grid[0]->sidegap;
                if (gettype($sidegap == 'string')) $sidegap = (int)$grid[0]->sidegap;
            }

            $cellgap = 0;
            if (isset($grid[0]->cellgap)) {
                $cellgap = $grid[0]->cellgap;
                if (gettype($cellgap == 'string')) $cellgap = (int)$grid[0]->cellgap;
            }

            $marginX = 0;
            if (isset($grid[0]->marginX)) {
                $marginX = $grid[0]->marginX;
                if (gettype($marginX == 'string')) $marginX = (int)$grid[0]->marginX;
            }

            $marginY = 0;
            if (isset($grid[0]->marginY)) {
                $marginY = $grid[0]->marginY;
                if (gettype($marginY == 'string')) $marginY = (int)$grid[0]->marginY;
            }
            $usemousewheel = 1;
            if (isset($grid[0]->usemousewheel)) {
                $usemousewheel = $grid[0]->usemousewheel;
                if (gettype($usemousewheel == 'string')) $usemousewheel = (int)$grid[0]->usemousewheel;
            }
            $color_bg = "#ffffff00";
            if (isset($grid[0]->color_bg)) {
                $color_bg = $grid[0]->color_bg;
            }
            $color_theme_a = "#2d2f31ff";
            if (isset($grid[0]->color_theme_a)) {
                $color_theme_a = $grid[0]->color_theme_a;
            }
            $color_theme_title = "#ffffffff";
            if (isset($grid[0]->color_theme_title)) {
                $color_theme_title = $grid[0]->color_theme_title;
            }
            $color_theme_desc = "#bfbfbfff";
            if (isset($grid[0]->color_theme_desc)) {
                $color_theme_desc = $grid[0]->color_theme_desc;
            }
            $color_cell_bg = "#dbdfe5ff";
            if (isset($grid[0]->color_cell_bg)) {
                $color_cell_bg = $grid[0]->color_cell_bg;
            }


             
            

            

            $poweredbyactive = false;

            

            if (isset($grid[0]->poweredbyactive)) {
                $poweredbyactive = $grid[0]->poweredbyactive;
            }

            $aeroscroll_gallery_iteration = $this->utils->get_iteration();
            $aeroscroll_gallery_version = $this->utils->get_version();

            $output .= '<script type="module">';
            $output .= "
            window[\"REST_URL\"] = '" . get_bloginfo('url') . "';         
            window[\"BASE_URL\"] = '" . get_site_url() . "';
            window[\"AEROSCROLL_GALLERY_ITERATION\"] = '" . $aeroscroll_gallery_iteration . "';      
            window[\"AEROSCROLL_GALLERY_VERSION\"] = '" . $aeroscroll_gallery_version . "';      
            let AeroscrollGrid = await import('" . plugin_dir_url(dirname(__FILE__)) . "public/js/aeroscroll-gallery.js?v=" . $aeroscroll_gallery_iteration . "').then(module=>module?.default);            
            setTimeout(()=> {
                createApp(AeroscrollGrid, {
                    rootID: 'app_aeroscroll_" . $final_shortcode . "', 
                    gridid: " . $grid[0]->id . ",
                    imagegallery_id: " . $grid[0]->imagegallery_id . ",
                    orientation: '" . $grid[0]->orientation . "', 
                    numcolumns: " . $grid[0]->numcolumns . ",                     
                    numcolumns_mid: " . $grid[0]->numcolumns_mid . ",                     
                    numcolumns_low: " . $grid[0]->numcolumns_low . ",                     
                    layout: '" . $grid[0]->layout . "',
                    type: '" . $grid[0]->type . "',
                    theme: '" . $grid[0]->theme . "',
                    themeonhover: '" . $themeonhover . "',
                    showreadmore: '" . $showreadmore . "',
                    articleinlightbox: '" . $articleinlightbox . "',
                    autoscroll: " . $autoscroll . ",
                    color_bg: '" . $color_bg . "',
                    color_theme_a: '" . $color_theme_a . "',
                    color_theme_title: '" . $color_theme_title . "',
                    color_theme_desc: '" . $color_theme_desc . "',
                    color_cell_bg: '" . $color_cell_bg . "',
                    sidegap: " . $sidegap . ",
                    cellgap: " . $cellgap . ",
                    marginX: " . $marginX . ",
                    marginY: " . $marginY . ",
                    height: " . str_replace("px", "", $height) . ",
                    publicfolder: '" . plugin_dir_url(__FILE__) . "',
                    usemousewheel: " . $usemousewheel . ",
                    cellSize: " . $cellsize . ",
                    cellSquared: " . $cellSquared . ",
                    scrollSpeed: '" . $grid[0]->scrollspeed . "',
                    hasScrollbar: " . $hasScrollbar . ",
                    hoveranimation: '" . $hoveranimation . "',
                    
                    
                    
                    poweredbyactive: '" . $poweredbyactive . "',

                    AEROSCROLL_GALLERY_ITERATION: '" . $aeroscroll_gallery_iteration . "',
                    AEROSCROLL_GALLERY_VERSION: '" . $aeroscroll_gallery_version . "',
                }).mount('#app_aeroscroll_" . $final_shortcode . "');
            }, 200);
            ";
            $output .= '</script>';
        } else {
            $output .= "";
        }

        return $output;
    }
}

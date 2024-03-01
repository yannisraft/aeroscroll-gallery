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
        $final_shortcode = str_replace("aeroscroll_", "", $code);
        $grid = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}aeroscroll_gallery WHERE shortcode = %s AND published = 1",array($final_shortcode)));

        $output = '';

        if (sizeof($grid) > 0) {
            $height = $grid[0]->height;
            if ($height == null || $height == "") $height = "400px";

            $output .= '<div><!-- #vue-app -->            
            <div id="app_aeroscroll_' . $final_shortcode . '"></div>
            <div class="as-credit"><a href="https://www.aeroscroll.com" target="_blank">Created with <img height="20" alt="Aeroscroll Gallery" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAAAeCAYAAADO4udXAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyVpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDAzIDExNi5kZGM3YmM0LCAyMDIxLzA4LzE3LTEzOjE4OjM3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjEuMiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTZGMUQzRjBBQzBDMTFFRTlBNTNEODFEQTBENTY0NEQiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MTZGMUQzRjFBQzBDMTFFRTlBNTNEODFEQTBENTY0NEQiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxNkYxRDNFRUFDMEMxMUVFOUE1M0Q4MURBMEQ1NjQ0RCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxNkYxRDNFRkFDMEMxMUVFOUE1M0Q4MURBMEQ1NjQ0RCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PppHLLIAAAxgSURBVHja7FsJ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"/></a></div>
            <!-- !#vue-app --></div>';

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

            if($poweredbyactive)
            {
                $output .= '<style>
                    .as-credit {
                        position: absolute;                
                        bottom: 9px;
                        left: 15px;
                        font-size: 14px;
                        color: #444 !important;
                        text-align: center;
                        text-decoration: none;

                        display: block !important;
                        opacity: 1 !important;
                        filter: opacity(100%) !important;
                        transform: scale(1) !important;
                        transform: translate(0, 0) !important;
                        clip-path: circle(100%) !important;
                        visibility: visible !important;
                        position: relative !important;                
                        bottom: 30px !important; 
                        left: 0px !important; 
                        width: 100% !important; 
                        height: 35px !important;
                        background: #ffffffaa !important;
                        padding-top: 4px;
                    }
        
                    .as-credit a {
                        text-decoration: none;
                        color: #444 !important;
                        font-weight: bold;
                    }
        
                    .as-credit a img {
                        margin-bottom: -3px;
                    }
        
                    :host{
                        display: block !important;
                        opacity: 1 !important;
                        filter: opacity(100%) !important;
                        transform: scale(1) !important;
                        transform: translate(0, 0) !important;
                        clip-path: circle(100%) !important;
                        visibility: visible !important;
                        position: relative !important;                
                        bottom: 30px !important; 
                        left: 0px !important; 
                        width: 100% !important; 
                        height: 35px !important;
                        background: #ffffffaa !important;
                    }
                </style>';
            }

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

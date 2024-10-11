<?php

/**
 *  Custom Endpoints
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__DIR__) . 'includes/class-aeroscroll-utils.php';

use Aeroscroll\Aeroscroll_Utils;

class Aeroscroll_Gallery_Custom_Endpoint
{

    public $proendpoints = null;

    /**
     * Utils Reference
     */
    private $utils;

    public function aeroscroll_cendpoints_init()
    {
        $this->utils = new Aeroscroll_Utils();

        add_action('rest_api_init', array($this, 'aeroscroll_register_rest_routes'));
    }

    // LINK # aeroscroll_register_rest_routes
    public function aeroscroll_register_rest_routes()
    {

        register_rest_route(
            'aeroscroll/v1',
            '/getposts',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'aeroscroll_get_posts'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/getimagegallerydata',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'aeroscroll_get_imagegallerydata'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/settings',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'aeroscroll_get_settings'),
                'args'                => array(),
                'permission_callback' => array($this, 'aeroscroll_permissions'),
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/settings',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_update_settings'),
                'args'                => array(
                    'orienation' => array(
                        'type'              => 'string',
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'permission_callback' => array($this, 'aeroscroll_permissions'),
            )
        );

        // Grids
        register_rest_route(
            'aeroscroll/v1',
            '/getgrids',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'aeroscroll_get_grids'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/updategrid',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_update_grid'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/addgrid',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_add_grid'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/deletegrid',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_delete_grid'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        // Image Collections
        register_rest_route(
            'aeroscroll/v1',
            '/getimagegalleries',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'aeroscroll_get_imagegalleries'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/addimagegallery',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_add_imagegallery'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/updateimagegallery',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_update_imagegallery'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/deleteimagegallery',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_delete_imagegallery'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        // Uploader
        register_rest_route(
            'aeroscroll/v1',
            '/uploadimages',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_upload_images'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        // File Manager
        register_rest_route(
            'aeroscroll/v1',
            '/listfolder',
            array(
                'methods'  => 'POST',
                'callback' => array($this, 'aeroscroll_list_folder'),
                'args'     => array(),
                /* 'permission_callback' => function () {
					return current_user_can( 'edit_others_posts' );
				}, */
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/deleteitem',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_delete_item'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/renameitem',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_rename_item'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/createfolder',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_create_folder'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/getgalleryimages',
            array(
                'methods'             => 'GET',
                'callback'            => array($this, 'aeroscroll_get_galleryimages'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/addgalleryimages',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_add_galleryimages'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/deletegalleryimages',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_delete_galleryimages'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/updategalleryimages',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_update_galleryimages'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );

        register_rest_route(
            'aeroscroll/v1',
            '/activateonempremium',
            array(
                'methods'             => 'POST',
                'callback'            => array($this, 'aeroscroll_activateonempremium'),
                'args'                => array(),
                'permission_callback' => function () {
                    return current_user_can('edit_others_posts');
                },
            )
        );
    }

    /**
     * Check request permissions
     *
     * @return bool
     */
    public function aeroscroll_permissions()
    {
        return current_user_can('manage_options');
    }

    // LINK # get_posts
    public function aeroscroll_get_posts($request)
    {
        $posts = array();

        // Get specific parameters:
        $posttype       = 'post';
        $taxonomy       = '';
        $taxonomy_field = '';
        $taxonomy_terms = '';
        $searchkeyword  = '';
        $perpage        = -1;
        $page           = -1;
        $orderby        = '';
        $order          = '';
        $infinite       = 'true';
        $fillpage       = 0;
        $loopdata       = false; // If it reaches the end of the "Posts" number then it send a Boolean as loopdata true in order to use Cache data
        $gridid         = -1;
        $debug          = '';

        try {
            // Set the Grid ID
            if ($request->get_param('gridid')) {
                $gridid = sanitize_key($request->get_param('gridid'));
            }

            if ($request->get_param('posttype')) {
                $posttype = sanitize_key($request->get_param('posttype'));
            }
            if ($request->get_param('taxonomy')) {
                $taxonomy = sanitize_key($request->get_param('taxonomy'));
            }
            if ($request->get_param('taxonomy_field')) {
                $taxonomy_field = sanitize_key($request->get_param('taxonomy_field'));
            }
            if ($request->get_param('taxonomy_terms')) {
                $taxonomy_terms = sanitize_key($request->get_param('taxonomy_terms'));
            }
            if ($request->get_param('searchkeyword')) {
                $searchkeyword = sanitize_key($request->get_param('searchkeyword'));
            }
            if ($request->get_param('orderby')) {
                $orderby = sanitize_key($request->get_param('orderby'));
            }
            if ($request->get_param('order')) {
                $order = sanitize_key($request->get_param('order'));
            }
            if ($request->get_param('infinite')) {
                $infinite = sanitize_key($request->get_param('infinite'));
            }
            if ($request->get_param('fillpage')) {
                $fillpage = sanitize_key($request->get_param('fillpage'));
            }
            if ($request->get_param('perpage')) {
                // Read Per Page value
                $perpage_int_value = ctype_digit($request->get_param('perpage')) ? intval(sanitize_key($request->get_param('perpage'))) : null;
                if (null !== $perpage_int_value) {
                    $perpage = $perpage_int_value;
                }
            } else {
                $perpage = -1;
            }

            if ($request->get_param('page')) {
                // Read Page value
                $page_int_value = ctype_digit(sanitize_key($request->get_param('page'))) ? intval(sanitize_key($request->get_param('page'))) : null;
                if (null !== $page_int_value) {
                    $page = $page_int_value;
                }
            }
        } catch (Exception $e) {
            throw new Exception('Aeroscroll Gallery Error : {esc_html_e( $e->getMessage() )}');
        }

        // Initialize args
        $args      = array(
            'posts_per_page' => $perpage,
            'post_type'      => $posttype,
        );
        $countargs = array(
            'posts_per_page' => -1,
            'post_type'      => $posttype,
        );

        // Add aditional Optional args
        if (-1 !== $page) {
            $args['paged'] = $page;
        }
        if (-1 !== $perpage) {
            $args['perpage'] = $perpage;
        }

        if ('' !== $taxonomy) {
            $args['taxonomy']      = $taxonomy;
            $countargs['taxonomy'] = $taxonomy;
        }
        if ('' !== $taxonomy_field) {
            $args['field']      = $taxonomy_field;
            $countargs['field'] = $taxonomy_field;
        }
        if ('' !== $taxonomy_terms) {
            $args['terms']      = $taxonomy_terms;
            $countargs['terms'] = $taxonomy_terms;
        }
        if ('' !== $searchkeyword) {
            $args['s']      = $searchkeyword;
            $countargs['s'] = $searchkeyword;
        }
        if ('' !== $orderby) {
            $args['orderby'] = $orderby;
        }
        if ('' !== $order) {
            $args['order'] = $order;
        }

        $final_categories = array();
        $totalpages       = 1;
        $totalcount       = 0;

        $sort_by     = 'ID';
        $sort_by_dir = 'ASC';
        if (-1 !== $gridid && is_numeric($gridid)) {

            // Get categories for specific Grid
            global $wpdb;

            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}aeroscroll_gallery WHERE id = %d",
                    array($gridid)
                )
            );

            $grid_data = $results[0];

            foreach ($results as $grid) {
                if (null !== $grid->sortby) {
                    $sort_by = $grid->sortby;
                }

                if (null !== $grid->sortbydir) {
                    $sort_by_dir = $grid->sortbydir;
                }

                if (null !== $grid->categories) {
                    if ('' !== $grid->categories) {
                        try {
                            $decoded = json_decode($grid->categories, true);

                            if (is_array($decoded)) {
                                foreach ($decoded as $item) {
                                    array_push($final_categories, $item);
                                }
                            }
                        } catch (Exception $e) {
                            throw new Exception('Aeroscroll Gallery Error : {esc_html_e( $e )}');
                        }
                    }
                }
            }

            $categoryqueryarray     = array(
                array(
                    'taxonomy' => 'category', // the custom vocabulary
                    'field'    => 'term_id',          // term_id, slug or name  (Define by what you want to search the below term)
                    'terms'    => $final_categories,      // provide the term slugs
                ),
            );
            $countargs['tax_query'] = $categoryqueryarray;
            $args['orderby']        = $sort_by;
            $args['order']          = $sort_by_dir;
            $args['tax_query']      = $categoryqueryarray;

            $count_query = new WP_Query($countargs);
            $main_query  = new WP_Query($args);
            $totalcount  = $count_query->post_count;
            $maincount   = $main_query->post_count;
            $posts       = $main_query->posts;

            if ($totalcount > 20 && -1 === $perpage) {
                $perpage = 20;
            }

            if (-1 !== $perpage && 0 !== $perpage) {
                $totalpages = round($totalcount / $perpage);
                if (fmod($totalcount, $perpage) > 0) {
                    ++$totalpages;
                }
            }

            if ($totalpages < 1) {
                $totalpages = 1;
            }

            if ('true' === $infinite) {

                // Activate Infinite functionality

                if ($maincount < $perpage && 1 === $fillpage) {
                    // Get Posts from Next Page
                    $newpage = $page + 1;
                    if ($newpage > $totalpages) {
                        $newpage  = 1;
                        $loopdata = true;
                    }

                    $newpage_args          = $args;
                    $newpage_args['paged'] = $newpage;
                    $newpage_query         = new WP_Query($newpage_args);
                    $newpage_query_posts   = $newpage_query->posts;

                    // Now add posts until we reach the desired number
                    $missingposts = $perpage - $maincount;
                    $added        = 0;

                    if (1 === $totalpages) {
                        $looptimes = 0;
                        if ($maincount > 0) {
                            $looptimes = intdiv($perpage, $maincount);
                        }

                        for ($k = 0; $k < $looptimes; $k++) {
                            foreach ($newpage_query_posts as $newpost) {
                                if ($added >= $missingposts) {
                                    break;
                                }
                                array_push($posts, (object) $newpost);
                                ++$added;
                            }
                        }
                    } else {
                        foreach ($newpage_query_posts as $newpost) {
                            if ($added >= $missingposts) {
                                break;
                            }
                            array_push($posts, (object) $newpost);
                            ++$added;
                        }
                    }
                }
            }

            // Now Append the Post featured images
            $_order_index = 1;
            foreach ($posts as $post) {
                $thumbnail_post_id = get_post_thumbnail_id($post->ID);
                $url               = wp_get_attachment_url($thumbnail_post_id, 'full');
                $thumbnail         = $url;

                $thumbnail_result = wp_get_attachment_image_src($thumbnail_post_id, 'large', true); // thumbnail, medium, large, full
                if (null !== $thumbnail_result) {
                    if (null !== is_array($thumbnail_result)) {
                        $thumbnail = $thumbnail_result[0];
                    }
                }

                $post->featured_image  = $url;
                $post->thumbnail_image = $thumbnail;
                $post->uid             = (int) ($post->ID . '0' . wp_rand(100000, 999999));

                $neworder        = $_order_index + ($page - 1) * $perpage;
                $post->order     = $neworder;
                $post->permalink = get_permalink($post->ID);
                $post->timestamp = get_the_date('U', $post->ID);
                $image_title     = get_the_title($thumbnail_post_id);
                $relative_url    = wp_make_link_relative($url);
                $relative_path   = str_replace($image_title, '', $relative_url);
                $image_id        = get_post_thumbnail_id($post->ID);

                ++$_order_index;
                if ($_order_index > $totalcount) {
                    $_order_index = 1;
                }
            }
        }

        $obj             = new stdClass();
        $obj->totalposts = $totalcount;
        $obj->numposts   = count($posts);
        $obj->totalpages = $totalpages;
        $obj->posts      = $posts;
        $obj->loopdata   = $loopdata;
        $obj->debug      = $debug;

        return $obj;
    }

    protected static $option_key = '_apex_settings';
    protected static $defaults   = array(
        'orientation' => 'vertical',
    );

    // LINK # aeroscroll_update_settings
    public function aeroscroll_update_settings($request)
    {
        $settings = array(
            'orientation' => sanitize_key($request->get_param('orientation')),
        );

        // remove any non-allowed indexes before save
        foreach ($settings as $i => $setting) {
            if (! array_key_exists($setting, self::$defaults)) {
                unset($settings[$i]);
            }
        }
        update_option(self::$option_key, $settings);

        return rest_ensure_response()->set_status(201);
    }
    /**
     * Get settings via API
     */
    public function aeroscroll_get_settings($request)
    {
        $orientation = get_option('aeroscrollgrid_settings');

        return rest_ensure_response(
            array(
                'orientation' => $orientation,
            )
        );
    }

    // LINK # aeroscroll_get_grids
    public function aeroscroll_get_grids($request)
    {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}aeroscroll_gallery"
        );

        $categories = get_categories(
            array(
                'orderby' => 'name',
                'order'   => 'ASC',
            )
        );

        foreach ($results as $grid) {
            $final_categories = array();
            if (null !== $grid->categories) {
                if ('' !== $grid->categories) {
                    try {
                        $decoded = json_decode($grid->categories, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $item) {
                                foreach ($categories as $category) {
                                    if ($category->term_id === $item) {
                                        $obj_cat        = new stdClass();
                                        $obj_cat->id    = $category->term_id;
                                        $obj_cat->title = $category->name;
                                        array_push($final_categories, $obj_cat);
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $final_categories = 'error' . $e;
                    }
                }
            }

            $grid->categories = $final_categories;
        }

        $returnobj                = new stdClass();
        $returnobj->grids         = $results;
        $returnobj->allcategories = $categories;

        return $returnobj;
    }

    // LINK # aeroscroll_update_grid
    public function aeroscroll_update_grid($params)
    {
        $published       = sanitize_key($params['published']);
        $id              = sanitize_key($params['id']);
        $title           = sanitize_key($params['title']);
        $sidegap         = sanitize_key($params['sidegap']);
        $cellgap         = sanitize_key($params['cellgap']);
        $margin_x        = sanitize_key($params['marginX']);
        $margin_y        = sanitize_key($params['marginY']);
        $usemousewheel   = sanitize_key($params['usemousewheel']);
        $imagegallery_id = sanitize_key($params['imagegallery_id']);
        $subtitle        = sanitize_key($params['subtitle']);
        $shortcode       = sanitize_key($params['shortcode']);
        $height          = sanitize_key($params['height']);
        $orientation     = sanitize_key($params['orientation']);
        $layout          = sanitize_key($params['layout']);
        $type            = sanitize_key($params['type']);
        $theme           = sanitize_key($params['theme']);
        $themeonhover    = sanitize_key($params['themeonhover']);
        $showreadmore    = sanitize_key($params['showreadmore']);

        $autoscroll        = sanitize_key($params['autoscroll']);
        $numrows           = sanitize_key($params['numrows']);
        $numcolumns        = sanitize_key($params['numcolumns']);
        $numcolumns_mid    = sanitize_key($params['numcolumns_mid']);
        $numcolumns_low    = sanitize_key($params['numcolumns_low']);
        $cellsize          = sanitize_key($params['cellsize']);
        $cellsquared       = sanitize_key($params['cellsquared']);
        $scrollspeed       = sanitize_key($params['scrollspeed']);
        $isinfinite        = sanitize_key($params['isinfinite']);
        $scrollbar         = sanitize_key($params['scrollbar']);
        $categories        = $params['categories'];
        $color_bg          = sanitize_text_field($params['color_bg']);
        $color_theme_a     = sanitize_text_field($params['color_theme_a']);
        $color_theme_title = sanitize_text_field($params['color_theme_title']);
        $color_theme_desc  = sanitize_text_field($params['color_theme_desc']);
        $color_cell_bg     = sanitize_text_field($params['color_cell_bg']);
        $sortby            = sanitize_key($params['sortby']);
        $sortbydir         = sanitize_key($params['sortbydir']);

        $poweredbyactive   = sanitize_key($params['poweredbyactive']);
        $articleinlightbox = sanitize_key($params['articleinlightbox']);

        $finalcategories = '[';
        if (is_array($categories)) {
            if (count($categories) > 0) {
                $ind = 0;
                foreach ($categories as $item) {
                    $finalcategories .= $item['id'];
                    if ($ind < count($categories) - 1) {
                        $finalcategories .= ',';
                    }
                    ++$ind;
                }
            }
        }
        $finalcategories .= ']';

        if (gettype($published) === 'boolean') {
            true === $published ? $published = 1 : $published = 0;
        }

        if (gettype($social_spoweredbyactivehare_facebook) === 'boolean') {
            true === $poweredbyactive ? $poweredbyactive = 1 : $poweredbyactive = 0;
        }

        global $wpdb;
        $table   = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->update(
            $table,
            array(
                'published'         => $published,
                'title'             => $title,
                'sidegap'           => $sidegap,
                'cellgap'           => $cellgap,
                'marginX'           => $margin_x,
                'marginY'           => $margin_y,
                'usemousewheel'     => $usemousewheel,
                'imagegallery_id'   => $imagegallery_id,
                'subtitle'          => $subtitle,
                'shortcode'         => $shortcode,
                'height'            => $height,
                'orientation'       => $orientation,
                'layout'            => $layout,
                'type'              => $type,
                'categories'        => $finalcategories,
                'theme'             => $theme,
                'themeonhover'      => $themeonhover,
                'showreadmore'      => $showreadmore,
                'autoscroll'        => $autoscroll,
                'color_bg'          => $color_bg,
                'color_theme_a'     => $color_theme_a,
                'color_theme_title' => $color_theme_title,
                'color_theme_desc'  => $color_theme_desc,
                'color_cell_bg'     => $color_cell_bg,
                'numrows'           => $numrows,
                'numcolumns'        => $numcolumns,
                'numcolumns_mid'    => $numcolumns_mid,
                'numcolumns_low'    => $numcolumns_low,
                'cellsize'          => $cellsize,
                'cellsquared'       => $cellsquared,
                'scrollspeed'       => $scrollspeed,
                'isinfinite'        => $isinfinite,
                'scrollbar'         => $scrollbar,
                'updated_at'        => gmdate('Y-m-d H:m:s'),
                'sortby'            => $sortby,
                'sortbydir'         => $sortbydir,
                'poweredbyactive'   => $poweredbyactive,
                'articleinlightbox' => $articleinlightbox,
            ),
            array('id' => $id)
        );

        return $results;
    }

    // LINK # aeroscroll_add_grid
    public function aeroscroll_add_grid($params)
    {
        $title             = sanitize_key($params['title']);
        $sidegap           = sanitize_key($params['sidegap']);
        $cellgap           = sanitize_key($params['cellgap']);
        $margin_x          = sanitize_key($params['marginX']);
        $margin_y          = sanitize_key($params['marginY']);
        $usemousewheel     = sanitize_key($params['usemousewheel']);
        $imagegallery_id   = sanitize_key($params['imagegallery_id']);
        $subtitle          = sanitize_key($params['subtitle']);
        $shortcode         = sanitize_key($params['shortcode']);
        $height            = sanitize_key($params['height']);
        $orientation       = sanitize_key($params['orientation']);
        $layout            = sanitize_key($params['layout']);
        $type              = sanitize_key($params['type']);
        $theme             = sanitize_key($params['theme']);
        $themeonhover      = sanitize_key($params['themeonhover']);
        $showreadmore      = sanitize_key($params['showreadmore']);
        $autoscroll        = sanitize_key($params['autoscroll']);
        $numrows           = sanitize_key($params['numrows']);
        $numcolumns        = sanitize_key($params['numcolumns']);
        $numcolumns_mid    = sanitize_key($params['numcolumns_mid']);
        $numcolumns_low    = sanitize_key($params['numcolumns_low']);
        $cellsize          = sanitize_key($params['cellsize']);
        $cellsquared       = sanitize_key($params['cellsquared']);
        $scrollspeed       = sanitize_key($params['scrollspeed']);
        $isinfinite        = sanitize_key($params['isinfinite']);
        $scrollbar         = sanitize_key($params['scrollbar']);
        $categories        = $params['categories'];
        $color_bg          = sanitize_key($params['color_bg']);
        $color_theme_a     = sanitize_key($params['color_theme_a']);
        $color_theme_title = sanitize_key($params['color_theme_title']);
        $color_theme_desc  = sanitize_key($params['color_theme_desc']);
        $color_cell_bg     = sanitize_key($params['color_cell_bg']);
        $sortby            = sanitize_key($params['sortby']);
        $sortbydir         = sanitize_key($params['sortbydir']);

        $poweredbyactive   = sanitize_key($params['poweredbyactive']);
        $articleinlightbox = sanitize_key($params['articleinlightbox']);

        $finalcategories = '[';
        if (is_array($categories)) {
            if (count($categories) > 0) {
                $ind = 0;
                foreach ($categories as $item) {
                    $finalcategories .= $item['id'];
                    if ($ind < count($categories) - 1) {
                        $finalcategories .= ',';
                    }
                    ++$ind;
                }
            }
        }
        $finalcategories .= ']';

        global $wpdb;
        $table   = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->insert(
            $table,
            array(
                'title'             => $title,
                'sidegap'           => $sidegap,
                'cellgap'           => $cellgap,
                'marginX'           => $margin_x,
                'marginY'           => $margin_y,
                'usemousewheel'     => $usemousewheel,
                'imagegallery_id'   => $imagegallery_id,
                'subtitle'          => $subtitle,
                'shortcode'         => $shortcode,
                'height'            => $height,
                'orientation'       => $orientation,
                'layout'            => $layout,
                'type'              => $type,
                'categories'        => $finalcategories,
                'theme'             => $theme,
                'themeonhover'      => $themeonhover,
                'showreadmore'      => $showreadmore,
                'autoscroll'        => $autoscroll,
                'color_bg'          => $color_bg,
                'color_theme_a'     => $color_theme_a,
                'color_theme_title' => $color_theme_title,
                'color_theme_desc'  => $color_theme_desc,
                'color_cell_bg'     => $color_cell_bg,
                'numrows'           => $numrows,
                'numcolumns'        => $numcolumns,
                'numcolumns_mid'    => $numcolumns_mid,
                'numcolumns_low'    => $numcolumns_low,
                'cellsize'          => $cellsize,
                'cellsquared'       => $cellsquared,
                'scrollspeed'       => $scrollspeed,
                'isinfinite'        => $isinfinite,
                'scrollbar'         => $scrollbar,
                'updated_at'        => gmdate('Y-m-d H:m:s'),
                'created_at'        => gmdate('Y-m-d H:m:s'),
                'sortby'            => $sortby,
                'sortbydir'         => $sortbydir,
                'poweredbyactive'   => $poweredbyactive,
                'articleinlightbox' => $articleinlightbox,
            ),
            array('%s', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d')
        );
        // 59
        return $results;
    }

    // LINK # aeroscroll_delete_grid
    public function aeroscroll_delete_grid($params)
    {
        $id = sanitize_key($params['id']);

        global $wpdb;
        $table   = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->delete($table, array('id' => $id));
        return $results;
    }



    // LINK # aeroscroll_get_imagegalleries
    public function aeroscroll_get_imagegalleries($params)
    {
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}aeroscroll_gallery_imagegalleries"
        );

        $returnobj                 = new stdClass();
        $returnobj->imagegalleries = $results;
        return $returnobj;
    }

    // LINK # aeroscroll_add_imagegallery
    public function aeroscroll_add_imagegallery($params)
    {
        $title       = sanitize_text_field($params['title']);
        $slug        = sanitize_key($params['slug']);
        $description = sanitize_text_field($params['description']);
        $published   = sanitize_key($params['published']);

        global $wpdb;
        $table   = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
        $results = $wpdb->insert(
            $table,
            array(
                'title'       => $title,
                'slug'        => $slug,
                'description' => $description,
                'published'   => $published,
                'updated_at'  => gmdate('Y-m-d H:m:s'),
                'created_at'  => gmdate('Y-m-d H:m:s'),
            ),
            array('%s', '%s', '%s', '%d', '%s', '%s')
        );

        return $results;
    }

    // LINK # aeroscroll_update_imagegallery
    public function aeroscroll_update_imagegallery($params)
    {
        $id          = sanitize_key($params['id']);
        $title       = sanitize_text_field($params['title']);
        $slug        = sanitize_key($params['slug']);
        $description = sanitize_text_field($params['description']);
        $published   = sanitize_key($params['published']);

        global $wpdb;
        $table   = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
        $results = $wpdb->update(
            $table,
            array(
                'title'       => $title,
                'slug'        => $slug,
                'description' => $description,
                'published'   => $published,
                'updated_at'  => gmdate('Y-m-d H:m:s'),
            ),
            array('id' => $id)
        );

        return $results;
    }

    // LINK # aeroscroll_delete_imagegallery
    public function aeroscroll_delete_imagegallery($params)
    {
        $id = sanitize_key($params['id']);
        global $wpdb;

        // Also Delete entries from 'aeroscroll_gallery_imagegallery_images'
        $table2  = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $results = $wpdb->delete($table2, array('imagegallery_id' => $id));

        // delete Gallery Itsself
        $table   = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
        $results = $wpdb->delete($table, array('id' => $id));

        return $results;
    }

    // LINK # aeroscroll_upload_images
    public function aeroscroll_upload_images($request)
    {
        $success     = false;
        $debug       = '';
        $relativedir = '';
        foreach (getallheaders() as $name => $value) {
            if ($name === 'relativedir') {
                $relativedir = $value;
            }
        }

        $upfiles = $request->get_file_params();
        if (isset($upfiles)) {
            $files = array();

            if ($relativedir !== null && $relativedir !== '') {
                $relativefullpath = getcwd() . $relativedir;

                if (! file_exists($relativefullpath)) {
                    wp_mkdir_p($relativefullpath, 0777, true);
                }

                foreach ($upfiles as $key => $file) {
                    //var_dump( $file );
                    //echo '----';

                    $filename     = sanitize_file_name($file['name']);
                    $type         = $file['type'];
                    $file_tmp     = $file['tmp_name'];
                    $uploadedfile = array(
                        'name'     => $filename,
                        'type'     => $file['type'],
                        'tmp_name' => $file['tmp_name'],
                        'error'    => $file['error'],
                        'size'     => $file['size'],
                    );

                    try {
                        $finalpath = $relativefullpath . '/' . $filename;

                        $upload_overrides = array(
                            'test_form' => false,
                        );

                        require_once ABSPATH . 'wp-admin/includes/file.php';
                        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                        if ($movefile && ! isset($movefile['error'])) {
                            array_push($files, $finalpath);
                            $success = true;

                            try {
                                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
                                $wp_filesystem = new WP_Filesystem_Direct(null);
                                $wp_filesystem->move($movefile['file'], $finalpath, true);
                            } catch (Error $e) {
                                $debug = $e->getMessage() . ' movefile: ' . $movefile['file'] . ' finalpath: ' . $finalpath;
                            }
                        } else {
                            $success = false;
                            $debug   = 'Error Uploading Image : ' . $movefile['error'];
                        }
                    } catch (Error $e) {
                        $debug = $e->getMessage() . ' movefile: ' . $movefile['file'] . ' finalpath: ' . $finalpath;
                    }
                }
            }
        }

        $obj          = new stdClass();
        $obj->success = $success;
        $obj->debug   = $debug;

        return $obj;
    }

    // ------------  File Manager -----------------

    // LINK # aeroscroll_list_folder
    public function aeroscroll_list_folder($params)
    {
        if (! file_exists(getcwd() . '/wp-content/uploads/aeroscroll-gallery')) {
            wp_mkdir_p(getcwd() . '/wp-content/uploads/aeroscroll-gallery', 0777, true);
        }

        $errors      = array();
        $basedir     = '/wp-content/uploads';
        $dir         = getcwd() . '/wp-content/uploads'; // root
        $relativedir = $basedir; // relative root
        $eol         = PHP_EOL;
        $isroot      = 1;

        // target can be root, up, (name of folder), refresh
        if (isset($params)) {
            $target           = sanitize_text_field($params['target']);
            $path             = sanitize_text_field($params['path']);
            $paramrelativedir = sanitize_text_field($params['relativedir']);
            $mode             = sanitize_text_field($params['mode']);
            $isroot           = 0;

            $listarray          = array();
            $debug              = '';
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'PNG', 'JPG', 'JPEG', 'jfif');

            if (isset($path)) {
                if ($target !== 'root') {
                    if ($paramrelativedir !== 'root') {
                        $relativedir = json_decode($paramrelativedir);
                    }

                    if ($target !== 'up' && $target !== 'refresh') {
                        $dir         = getcwd() . $paramrelativedir . '/' . $target;
                        $relativedir = $paramrelativedir . '/' . $target;
                    } elseif ($target === 'refresh') {
                        $dir         = getcwd() . $paramrelativedir;
                        $relativedir = $paramrelativedir;

                        if (basename($paramrelativedir) === 'uploads') {
                            $isroot = 1;
                        }
                    } elseif (basename($paramrelativedir) !== 'uploads') {
                        $parent      = dirname(getcwd() . $paramrelativedir, 1);
                        $dir         = $parent;
                        $relativedir = str_replace(getcwd(), '', $parent);

                        if (basename($parent) === 'uploads') {
                            $isroot = 1;
                        }
                    } else {
                        $isroot = 1;
                    }
                } else {
                    //$dir = getcwd(); // root
                    //$relativedir = $basedir;
                    $dir         .= '/aeroscroll-gallery';
                    $relativedir .= '/aeroscroll-gallery';
                }

                // FOLDER VIEW
                // -----------
                $debug = $mode;
                if ($mode == 'mode_fileexplorer') {
                    $iterator = new DirectoryIterator($dir);

                    foreach ($iterator as $ff) {
                        if ($ff->isFile()) {

                            if (in_array($ff->getExtension(), $allowed_extensions, true)) {
                                $item              = new stdClass();
                                //$item->id          = $ff->getFilename();
                                $item->file        = $ff->getFilename();
                                $item->size        = $ff->getSize();
                                $item->date        = $ff->getMTime();
                                $item->folder      = 0;
                                $item->relativeurl = $relativedir;

                                if (! file_exists(getcwd() . $relativedir . '/__optimized')) {
                                    wp_mkdir_p(getcwd() . $relativedir . '/__optimized', 0777, true);
                                }

                                $optimized_url        = getcwd() . $relativedir . '/__optimized/opt__' . $ff->getFilename();
                                $optimized_url_exists = file_exists($optimized_url);
                                if ($optimized_url_exists) {

                                    $item->optimized     = $optimized_url_exists;
                                    $item->optimizedsize = filesize($optimized_url);
                                }

                                array_push($listarray, $item);
                            }
                        } elseif ($ff->isDir()) {
                            if ($ff->getFilename() !== '.' && $ff->getFilename() !== '..' && $ff->getFilename() !== '__optimized') {
                                $item              = new stdClass();
                                //$item->id          = $ff->getFilename();
                                $item->file        = $ff->getFilename();
                                $item->size        = $ff->getSize();
                                $item->date        = $ff->getMTime();
                                $item->folder      = 1;
                                $item->relativeurl = $relativedir;

                                array_push($listarray, $item);
                            }
                        }
                    }
                } else {
                    // MEDIA LIBRARY VIEW
                    // -----------
                    // Set up the API endpoint
                    $per_page = 100;
                    $page     = 1;

                    $api_url = get_site_url() . "/wp-json/wp/v2/media?per_page=$per_page&page=$page";

                    // Make the API request
                    $response = wp_remote_get($api_url);

                    // Check for errors
                    if (is_wp_error($response)) {
                        array_push($errors, $response);
                    }

                    // Parse the response
                    $media_items = json_decode(wp_remote_retrieve_body($response), true);
                    //return $media_items;

                    try {
                        if (! empty($media_items)) {
                            foreach ($media_items as $fil) {
                                // Get relative URL
                                $cleaned_url = str_replace(get_site_url(), '', $fil['source_url']);
                                $cleaned_url = str_replace($fil['title']['rendered'], '', $cleaned_url);
                                $cleaned_url = ltrim($cleaned_url, '/\\');

                                $item                  = new stdClass();
                                $item->id              = $fil['id'];
                                $item->folder          = 0;
                                $item->ismedia         = 1;
                                $item->relativeurl     = '/' . $cleaned_url;
                                $item->absoluteurl     = $fil['source_url'];
                                $item->relativeurlfile = $cleaned_url . $fil['title']['rendered'];
                                $item->file            = $fil['title']['rendered'];
                                //$item->relativeurl = $item['source_url'];
                                $item->size = $this->utils->get_image_size_in_bytes($fil['source_url']);

                                array_push($listarray, $item);
                            }
                        } else {
                            array_push($errors, 'No media items found.');
                        }
                    } catch (Exception $e) {
                        var_export($e->getMessage(), true);
                    }
                }

                array_multisort(array_column($listarray, 'folder'), SORT_DESC, $listarray);
            }
        }

        if (empty($errors) === true) {
            return '{ "result": 1, "list": ' . wp_json_encode($listarray) . ', "path": ' . wp_json_encode($relativedir) . ', "target":"' . $target . '", "debug": "' . $debug . '", "isroot": ' . $isroot . '}';
        } else {
            return '{ "result": 1 }';
        }
    }

    // LINK # aeroscroll_delete_item
    public function aeroscroll_delete_item($params)
    {
        $files            = $params['files'];
        $paramrelativedir = sanitize_text_field($params['relativedir']);
        $deleterelevant   = sanitize_key($params['deleterelevant']);
        $dir              = getcwd() . $paramrelativedir;
        $result           = 0;
        $filename         = '';
        $error            = '';

        try {
            if ($files !== null) {
                if (is_array($files)) {
                    $total_files = count($files);
                    for ($x = 0; $x < $total_files; $x++) {
                        $filename = sanitize_text_field($files[$x]['name']);
                        $isfolder = sanitize_text_field($files[$x]['folder']);

                        $fullpath = getcwd() . $paramrelativedir . '/' . $filename;

                        try {
                            // Step 1: Delete actual files
                            if ($isfolder === 1 || $isfolder === '1') {
                                $error = $this->aeroscroll_deletefolder_andlinks($filename, $paramrelativedir, $deleterelevant, $error);
                                $result = 1;
                            } else {
                                $error = $this->aeroscroll_deletefile_andlinks($filename, $paramrelativedir, $deleterelevant);
                                $result = 1;
                            }
                        } catch (Exception $e) {
                            $error = $e->getMessage();
                        }
                    }
                } else {
                    $error = "Not an Array";
                }
            } else {
                $error = "No Files";
            }
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }

        return '{ "result": ' . $result . ', "error": "' . $error . '" }';
    }

    public function aeroscroll_deletefile_andlinks($filename, $paramrelativedir, $deleterelevant)
    {
        $error = '';

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';

        $normalizedrelativepath = $this->aeroscroll_normalizePath($paramrelativedir);
        $normalizedrelativepath = str_replace('\\', '/', $normalizedrelativepath);

        $wpdb->delete(
            $table,
            array(
                'image_name'  => $filename,
                'relativedir' => $normalizedrelativepath,
            )
        );

        $fullpath = getcwd() . $paramrelativedir . '/' . $filename;
        if (file_exists($fullpath)) {
            if (wp_delete_file($fullpath)) {
            }
        }

        return $error;
    }

    public function aeroscroll_deletefolder_andlinks($foldername, $folderpath, $deleterelevant, $error)
    {

        $fullpath = getcwd() . $folderpath . '/' . $foldername;
        $fullpath = str_replace('/', '\\', $fullpath);

        $error .= ' isempty: ' . $this->aeroscroll_is_dir_empty($fullpath);

        if ($this->aeroscroll_is_dir_empty($fullpath) === true) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
            $wp_filesystem = new WP_Filesystem_Direct(null);

            $wp_filesystem->delete($fullpath, false, 'd');
        } else {
            //$error    = 'FOLDER NOT EMPTY';
            $dirfiles = scandir($fullpath);

            //$error = 'C: ' . $fullpath;
            //$error = 'C: ' . count( $dirfiles );

            foreach ($dirfiles as $file) {
                // Skip '.' and '..'
                if ($file !== '.' && $file !== '..') {
                    $relativepath = $folderpath . '\\' . $foldername;
                    $subfullpath  = $fullpath . '\\' . $file;

                    if (is_dir($subfullpath)) {
                        $error .= ' is_dir: ' . is_dir($subfullpath);
                        $error .= $this->aeroscroll_deletefolder_andlinks($file, $relativepath, $deleterelevant, $error);
                    } else {
                        $error .= $this->aeroscroll_deletefile_andlinks($file, $relativepath, $deleterelevant);
                    }
                }

                // $error = ' | '. is_dir( $subfullpath ) . ' | ';
            }

            if ($this->aeroscroll_is_dir_empty($fullpath) === true) {
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
                $wp_filesystem = new WP_Filesystem_Direct(null);

                $wp_filesystem->delete($fullpath, true, 'd');
            }
            /* require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
            $wp_filesystem = new WP_Filesystem_Direct(null);

            $wp_filesystem->delete($fullpath, true, 'd'); */
        }

        return $error;
    }

    public function aeroscroll_is_dir_empty($dir)
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }



    // LINK # aeroscroll_rename_item
    public function aeroscroll_rename_item($params)
    {
        $newname          = sanitize_text_field($params['newname']);
        $oldname          = sanitize_text_field($params['name']);
        $paramrelativedir = sanitize_text_field($params['relativedir']);
        $imageid          = sanitize_text_field($params['id']);
        //$mode             = sanitize_text_field( $params['mode'] );
        $dir    = getcwd() . $paramrelativedir;
        $result = 0;
        $error  = null;

        $error = '( ' . $newname . ' | ' . $oldname . ' )';

        if ($newname != null && $oldname != null) {

            $oldfilepath = $dir . '/' . $oldname;
            $newfilepath = $dir . '/' . $newname;

            $oldfilepath = str_replace('/', '\\', $oldfilepath);
            $newfilepath = str_replace('/', '\\', $newfilepath);

            if (file_exists($oldfilepath)) {

                // 1st Step: Rename Entries of the file by matching 'filename' and 'relativedir'
                $error = '( ' . $oldname . ' | ' . $paramrelativedir . ' )';

                global $wpdb;
                $table   = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
                $results = $wpdb->update(
                    $table,
                    array(
                        'image_name' => $newname,
                    ),
                    array(
                        'image_name'  => $oldname,
                        'relativedir' => $paramrelativedir,
                    )
                );

                // 2nd Step: Rename Actual File
                try {
                    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
                    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
                    $wp_filesystem = new WP_Filesystem_Direct(null);
                    $wp_filesystem->move($oldfilepath, $newfilepath, true);
                } catch (Error $e) {
                    $error = $e->getMessage();
                }

                $result = 1;
            } else {
                $error = 'file does not exist: ' . $oldfilepath;
            }
        }

        return '{ "result": ' . $result . ', "oldname": "' . $oldname . '", "error": "' . $error . '" }';
    }

    // LINK # aeroscroll_create_folder
    public function aeroscroll_create_folder($params)
    {
        $foldername       = sanitize_text_field($params['name']);
        $paramrelativedir = sanitize_text_field($params['relativedir']);
        $dir              = getcwd() . $paramrelativedir;
        $result           = 0;

        if ($foldername !== null) {
            $newfolderpath = $dir . '/' . $foldername;
            if (! file_exists($newfolderpath)) {
                wp_mkdir_p($newfolderpath, 0777, true);
                $result = 1;
            }
        }

        return '{ "result": ' . $result . ' }';
    }


    // LINK # aeroscroll_add_galleryimages
    public function aeroscroll_add_galleryimages($params)
    {
        $error = '-';
        global $wpdb;
        $table   = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $results = null;

        try {
            $images          = $params['images'];
            $imagegallery_id = sanitize_key($params['imagegallery_id']);
            $result          = 0;

            if ($images !== null) {
                if (is_array($images)) {
                    $total_files = count($images);

                    for ($x = 0; $x < $total_files; $x++) {
                        $image_name  = sanitize_text_field($images[$x]['name']);
                        $relativedir = sanitize_text_field($images[$x]['relativedir']);
                        $image_order = sanitize_key($images[$x]['order']);
                        $created_at  = sanitize_key($images[$x]['date']);

                        $encoded_relativedir       = rawurlencode($relativedir);
                        $after_encoded_relativedir = str_replace('%2F', '/', $encoded_relativedir);

                        // MUST URL ESCAPE relativedir
                        $results = $wpdb->insert(
                            $table,
                            array(
                                'imagegallery_id' => $imagegallery_id,
                                'title'           => $title,
                                'description'     => $description,
                                'image_name'      => $image_name,
                                'image_order'     => $image_order,
                                'relativedir'     => $after_encoded_relativedir,
                                'created_at'      => gmdate('Y-m-d H:m:s'),
                            ),
                            array('%d', '%s', '%s', '%s', '%d', '%s', '%s')
                        );
                    }

                    $result = 1;
                }
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $obj         = new stdClass();
        $obj->result = $result;
        $obj->error  = $error;

        return $obj;
    }

    // LINK # aeroscroll_get_galleryimages
    public function aeroscroll_get_galleryimages($params)
    {
        $error = '-';
        global $wpdb;
        $results = null;

        try {
            $imagegallery_id = sanitize_key($params['imagegallery_id']);
            $result          = 0;

            if ($imagegallery_id !== null) {
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        // phpcs:ignore
                        "SELECT * FROM {$wpdb->prefix}aeroscroll_gallery_imagegallery_images WHERE imagegallery_id = %d ORDER BY image_order",
                        array($imagegallery_id)
                    )
                );

                $img__order    = 1;
                $total_results = count($results);
                for ($x = 0; $x < $total_results; $x++) {
                    $imagedata = $results[$x];

                    $image_name  = $imagedata->image_name;
                    $relativedir = $imagedata->relativedir;

                    if ($imagedata->media_gallery_id !== null) {
                        $image_id = $imagedata->media_gallery_id; // Replace with your media ID
                        $image_dt = wp_get_attachment_image_src($image_id, 'full');
                        $image_url = $image_dt[0];

                        $results[$x]->image = $image_url;

                        unset($results[$x]->image_name);

                        ++$img__order;
                    } else {
                        $fullpath = getcwd() . $relativedir . '/' . $image_name;
                        $optimizedpath = getcwd() . $relativedir . '/__optimized/opt__' . $image_name;

                        // Check If Optimized Exists
                        if (file_exists($fullpath)) {
                            $results[$x]->exists     = true;
                            $results[$x]->size       = filesize($fullpath);
                            $results[$x]->resolution = getimagesize($fullpath);
                        } else {
                            $results[$x]->exists = false;
                        }

                        if (file_exists($optimizedpath)) {
                            $results[$x]->optimized     = true;
                            $results[$x]->optimizedsize = filesize($optimizedpath);
                        } else {
                            $results[$x]->optimized = false;
                        }

                        $results[$x]->name  = $results[$x]->image_name;
                        $results[$x]->order = intval($results[$x]->image_order);
                        $results[$x]->image = get_site_url() . $relativedir . '/' . $image_name;

                        unset($results[$x]->image_name);

                        ++$img__order;
                    }
                }

                $result = 1;
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $returnobj                = new stdClass();
        $returnobj->result        = $result;
        $returnobj->galleryimages = $results;
        $returnobj->error         = $error;

        return $returnobj;
    }

    // LINK # aeroscroll_delete_galleryimages
    public function aeroscroll_delete_galleryimages($params)
    {
        $imagestodelete = $params['images'];
        $error          = '-';

        global $wpdb;
        $table  = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $result = 0;

        $image_id = '';
        try {
            if ($imagestodelete !== null) {
                if (is_array($imagestodelete)) {
                    $total_images = count($imagestodelete);
                    for ($x = 0; $x < $total_images; $x++) {
                        $image_id = sanitize_key($imagestodelete[$x]['id']);
                        $results  = $wpdb->delete($table, array('id' => $image_id));
                    }

                    $result = 1;
                }
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $returnobj         = new stdClass();
        $returnobj->result = $result;
        $returnobj->error  = $error;

        return $returnobj;
    }

    // LINK # aeroscroll_update_galleryimages
    public function aeroscroll_update_galleryimages($params)
    {
        $images = $params['images'];

        $error = '-';

        global $wpdb;
        $result = 0;

        try {
            if ($images !== null) {
                if (is_array($images)) {

                    $total_images = count($images);
                    for ($x = 0; $x < $total_images; $x++) {
                        $_image       = $images[$x];
                        $_id          = sanitize_key($_image['id']);
                        $_title       = sanitize_text_field($_image['title']);
                        $_description = sanitize_text_field($_image['description']);
                        $_order       = sanitize_key($_image['order']);

                        $results = $wpdb->get_results($wpdb->prepare("INSERT INTO {$wpdb->prefix}aeroscroll_gallery_imagegallery_images (id, title, description, image_order) VALUES (%d, %s, %s, %d)  ON DUPLICATE KEY UPDATE image_order = VALUES(image_order), title = VALUES(title), description = VALUES(description)", array($_id, $_title, $_description, $_order)));
                    }

                    $result = 1;
                }
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $returnobj         = new stdClass();
        $returnobj->result = $result;
        $returnobj->error  = $error;

        return $returnobj;
    }

    // LINK # aeroscroll_get_imagegallerydata
    public function aeroscroll_get_imagegallerydata($request)
    {
        $galleryimages  = array();
        $searchkeyword  = '';
        $perpage        = -1;
        $page           = -1;
        $orderby        = '';
        $order          = '';
        $infinite       = 'true';
        $loopdata       = false; // If it reaches the end of the "Posts" number then it send a Boolean as loopdata true in order to use Cache data
        $imagegalleryid = -1;
        $galleryid      = -1;
        $debug          = '';

        // Set the Grid ID
        if ($request->get_param('imagegallery_id')) {
            $imagegalleryid = sanitize_key($request->get_param('imagegallery_id'));
        }

        if ($request->get_param('gallery_id')) {
            $galleryid = sanitize_key($request->get_param('gallery_id'));
        }

        if ($request->get_param('searchkeyword')) {
            $searchkeyword = sanitize_text_field($request->get_param('searchkeyword'));
        }
        if ($request->get_param('orderby')) {
            $orderby = sanitize_text_field($request->get_param('orderby'));
        }
        if ($request->get_param('order')) {
            $order = sanitize_text_field($request->get_param('order'));
        }
        if ($request->get_param('infinite')) {
            $infinite = sanitize_key($request->get_param('infinite'));
        }
        if ($request->get_param('perpage')) {

            $perpage_val = sanitize_key($request->get_param('perpage'));

            // Read Per Page value
            $perpage_int_value = ctype_digit($perpage_val) ? intval($perpage_val) : null;
            if ($perpage_int_value !== null) {
                $perpage = $perpage_int_value;
            }
        }
        if ($request->get_param('page')) {
            $page_val = sanitize_key($request->get_param('page'));

            // Read Page value
            $page_int_value = ctype_digit($page_val) ? intval($page_val) : null;
            if ($page_int_value !== null) {
                $page = $page_int_value;
            }
        }

        $totalpages = 1;
        $totalcount = 0;

        if ($imagegalleryid !== -1 && is_numeric($imagegalleryid)) {

            // Get categories for specific Grid
            global $wpdb;
            $table         = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
            $table_gallery = $wpdb->prefix . 'aeroscroll_gallery';

            // First Image Gallery Parameters
            $results_gallery_properties = $wpdb->get_results(
                $wpdb->prepare(
                    // phpcs:ignore
                    "SELECT * FROM {$wpdb->prefix}aeroscroll_gallery WHERE id = %d",
                    array($galleryid)
                )
            );

            $sort_by          = 'id';
            $sort_by_dir      = 'ASC';
            $foundsort_by     = $results_gallery_properties[0]->sortby;
            $foundsort_by_dir = $results_gallery_properties[0]->sortbydir;

            if ($foundsort_by !== '' && $foundsort_by !== null) {
                $sort_by = $foundsort_by;
                if ($sort_by === 'filename') {
                    $sort_by = 'image_name';
                }
            }

            if ($foundsort_by_dir !== '' && $foundsort_by_dir !== null) {
                $sort_by_dir = $foundsort_by_dir;
            }

            // FIRST MAKE QUERY COUNT IMAGES
            $results_count = $wpdb->get_results(
                // phpcs:ignore
                $wpdb->prepare(
                    // phpcs:ignore
                    "SELECT COUNT(*) AS totalimages FROM {$wpdb->prefix}aeroscroll_gallery_imagegallery_images WHERE imagegallery_id = %d",
                    array($imagegalleryid)
                )
            );

            $totalcount = intval($results_count[0]->totalimages);
            $maincount  = $totalcount;

            if ($perpage !== -1 && $perpage !== 0) {
                $totalpages = round($totalcount / $perpage);
            }
            if ($totalpages < 1) {
                $totalpages = 1;
            }

            if ($infinite === 'true') {
                // Activate Infinite functionality

                // A: LESS THAN PERPAGE
                if ($maincount < $perpage) {
                    // Get Posts from Next Page
                    $newpage = $page + 1;
                    if ($newpage > $totalpages) {
                        $newpage  = 1;
                        $loopdata = true;
                    }

                    // Now add posts until we reach the desired number
                    $missingimages = $perpage - $maincount;

                    // phpcs:ignore
                    $results = $wpdb->get_results(
                        $wpdb->prepare(
                            // phpcs:ignore
                            "SELECT * FROM {$wpdb->prefix}aeroscroll_gallery_imagegallery_images WHERE imagegallery_id = %d ORDER BY %1s %1s",
                            array($imagegalleryid, $sort_by, $sort_by_dir)
                        )
                    );

                    $newpage_query_images = $results;
                    $galleryimages        = $results;

                    foreach ($galleryimages as $img) {
                        $img->uid = (int) ($img->id . '0' . wp_rand(1000000, 9999999));
                    }

                    $added = 0;
                    if ($totalpages === 1) {
                        $looptimes = 0;
                        if ($maincount > 0) {
                            $looptimes = intdiv($perpage, $maincount);
                        }

                        for ($k = 0; $k < $looptimes; $k++) {
                            foreach ($newpage_query_images as $newimage) {
                                if ($added >= $missingimages) {
                                    break;
                                }
                                $newimage->uid = (int) ($newimage->id . '0' . wp_rand(1000000, 9999999));
                                array_push($galleryimages, clone $newimage);
                                ++$added;
                            }
                        }
                    } else {
                        foreach ($newpage_query_images as $newimage) {
                            if ($added >= $missingimages) {
                                break;
                            }
                            $newimage->uid = (int) ($newimage->id . '0' . wp_rand(1000000, 9999999));
                            array_push($galleryimages, clone $newimage);
                            ++$added;
                        }
                    }

                    $ind = 0;
                    foreach ($galleryimages as $index => $img) {
                        $decoded_relativedir  = urldecode($img->relativedir);
                        $img->url             = get_site_url() . $img->relativedir . '/' . $img->image_name;
                        $img->thumbnail_image = get_site_url() . $img->relativedir . '/' . $img->image_name;
                        $img->featured_image  = get_site_url() . $img->relativedir . '/' . $img->image_name;
                        $img->image_order     = intval($img->image_order);

                        if ($ind > $maincount - 1) {
                            $ind = 0;
                        }
                        $img->order = $ind + 1;

                        $img->imageexists = true;

                        ++$ind;

                        $realpath = getcwd() . $decoded_relativedir . '/' . $img->image_name;
                        if (! file_exists($realpath)) {
                            $img->imageexists = false;
                        }

                        $optimizedurl  = get_site_url() . $img->relativedir . '/__optimized/opt__' . $img->image_name;
                        $optimizedpath = getcwd() . $decoded_relativedir . '/__optimized/opt__' . $img->image_name;

                        if (file_exists($optimizedpath)) {
                            $img->thumbnail_image = $optimizedurl;
                        }
                    }
                } else {
                    // B: MORE THAN PERPAGE
                    $offset = ($perpage * ($page - 1)); // offset = (limit * pageNumber) - limit;

                    $results = $wpdb->get_results(
                        $wpdb->prepare(
                            // phpcs:ignore
                            "SELECT * FROM {$wpdb->prefix}aeroscroll_gallery_imagegallery_images WHERE imagegallery_id = %d ORDER BY %1s ASC LIMIT %d OFFSET %d",
                            array($imagegalleryid, $sort_by, $perpage, $offset)
                        )
                    );

                    $galleryimages = $results;

                    foreach ($galleryimages as $index => $img) {
                        $decoded_relativedir  = urldecode($img->relativedir);
                        $img->url             = get_site_url() . $img->relativedir . '/' . $img->image_name;
                        $img->thumbnail_image = get_site_url() . $img->relativedir . '/' . $img->image_name;
                        $img->featured_image  = get_site_url() . $img->relativedir . '/' . $img->image_name;
                        $img->image_order     = intval($img->image_order);

                        $calculated_order = $offset + $index + 1;
                        $img->order       = $calculated_order;
                        $img->imageexists = true;
                        $img->moreperpage = true;

                        $realpath = getcwd() . $decoded_relativedir . '/' . $img->image_name;
                        if (! file_exists($realpath)) {
                            $img->imageexists = false;
                        }

                        $optimizedurl  = get_site_url() . $img->relativedir . '/__optimized/opt__' . $img->image_name;
                        $optimizedpath = getcwd() . $decoded_relativedir . '/__optimized/opt__' . $img->image_name;

                        if (file_exists($optimizedpath)) {
                            $img->thumbnail_image = $optimizedurl;
                        }
                    }
                }
            }
        }

        $_gals                = array();
        $total_gallery_images = count($galleryimages);
        for ($i = 1; $i <= $total_gallery_images; $i++) {
            array_push($_gals, (object) $galleryimages[$i - 1]);
            $_gals[$i - 1]->_order = $i;
        }

        $obj                = new stdClass();
        $obj->totalimages   = $totalcount;
        $obj->numimages     = $total_gallery_images;
        $obj->totalpages    = $totalpages;
        $obj->galleryimages = $galleryimages;
        $obj->loopdata      = $loopdata;
        $obj->debug         = $debug;

        return $obj;
    }

    // LINK # aeroscroll_activateonempremium
    public function aeroscroll_activateonempremium($request)
    {
        $serviceactive = 0;
        $debug         = '';

        if ($request->get_param('serviceactive')) {
            $serviceactive = sanitize_key($request->get_param('serviceactive'));
        }

        $api_url = 'http://www.aeroscroll.com/wp-json/aeroscroll-manager/v1/onemonthpremiumservice';
        if ($serviceactive === '1' || $serviceactive === 1 || $serviceactive === true) {
            $serviceactive = 1;
        } else {
            $serviceactive = 0;
        }

        $domain = '';
        try {
            $domain = rawurlencode(get_site_url());
        } catch (Exception $e) {
            throw new Exception('Aeroscroll Gallery Error : {esc_html_e( $e->getMessage() )}');
        }

        $current_user = wp_get_current_user();
        $user_email   = $current_user->user_email;

        $debug = $user_email;

        $data_arr = array(
            'token'         => 'b5x$gj18n',
            'user_email'    => $user_email,
            'serviceactive' => $serviceactive,
            'domain'        => $domain,
        );
        $body     = wp_json_encode($data_arr);

        $remote = wp_remote_post(
            $api_url,
            array(
                'method'  => 'POST',
                'body'    => $body,
                'headers' => array(
                    'Content-Type'  => 'application/json',
                    'Cache-Control' => 'no-cache',
                ),
            )
        );
        if (is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote) || empty(wp_remote_retrieve_body($remote))) {
            $debug = 'ERROR: ' . $remote['body'];
        }

        $result = '{ "r": -1 }';
        if ($remote['response']['code'] === 200) {
            $result = json_decode($remote['body']);
        }

        // Finally UPDATE Table of specific Gallery
        global $wpdb;
        $table   = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->update(
            $table,
            array(
                'poweredbyactive' => $serviceactive,
            ),
            array('id' => $id)
        );

        $obj         = new stdClass();
        $obj->result = $result;
        $obj->debug  = $debug;

        return $obj;
    }

    public function aeroscroll_normalizePath($path)
    {
        // Replace all backslashes and forward slashes with the system's directory separator
        return preg_replace('/[\/\\\\]+/', DIRECTORY_SEPARATOR, $path);
    }
}

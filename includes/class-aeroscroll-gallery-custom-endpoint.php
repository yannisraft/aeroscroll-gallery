<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/aeroscroll-utils.php';
use Aeroscroll\utils;

class aeroscroll_gallery_Api_Endpoint
{
    public $proendpoints = null;

    /**
     * Utils Reference   
     */
    private $utils;

    public function init()
    {
        $this->utils = new utils();

        

        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }
    public function register_rest_routes()
    {
        if ($this->proendpoints != null) $this->proendpoints->register_pro_rest_routes();

        register_rest_route('aeroscroll/v1', '/getposts', [
            'methods' => 'GET',
            'callback' => array($this, 'get_posts'),
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('aeroscroll/v1', '/getimagegallerydata', [
            'methods' => 'GET',
            'callback' => array($this, 'get_imagegallerydata'),
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('aeroscroll/v1', '/settings', [
            'methods' => 'GET',
            'callback' => array($this, 'get_settings'),
            'args' => array(),
            'permission_callback' => array($this, 'permissions')
        ]);

        register_rest_route('aeroscroll/v1', '/settings', [
            'methods' => 'POST',
            'callback' => array($this, 'update_settings'),
            'args' => array(
                'orienation' => array(
                    'type' => 'string',
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field'
                )
            ),
            'permission_callback' => array($this, 'permissions')
        ]);

        // Grids
        register_rest_route('aeroscroll/v1', '/getgrids', [
            'methods' => 'GET',
            'callback' => array($this, 'get_grids'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/updategrid', [
            'methods' => 'POST',
            'callback' => array($this, 'update_grid'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/addgrid', [
            'methods' => 'POST',
            'callback' => array($this, 'add_grid'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/deletegrid', [
            'methods' => 'POST',
            'callback' => array($this, 'delete_grid'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);


        // Image Collections
        register_rest_route('aeroscroll/v1', '/getimagegalleries', [
            'methods' => 'GET',
            'callback' => array($this, 'get_imagegalleries'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/addimagegallery', [
            'methods' => 'POST',
            'callback' => array($this, 'add_imagegallery'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/updateimagegallery', [
            'methods' => 'POST',
            'callback' => array($this, 'update_imagegallery'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/deleteimagegallery', [
            'methods' => 'POST',
            'callback' => array($this, 'delete_imagegallery'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        // Uploader
        register_rest_route('aeroscroll/v1', '/uploadimages', [
            'methods' => 'POST',
            'callback' => array($this, 'upload_images'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        // File Manager
        register_rest_route('aeroscroll/v1', '/listfolder', [
            'methods' => 'POST',
            'callback' => array($this, 'list_folder'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/deleteitem', [
            'methods' => 'POST',
            'callback' => array($this, 'delete_item'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/renameitem', [
            'methods' => 'POST',
            'callback' => array($this, 'rename_item'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/createfolder', [
            'methods' => 'POST',
            'callback' => array($this, 'create_folder'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/getgalleryimages', [
            'methods' => 'GET',
            'callback' => array($this, 'get_galleryimages'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/addgalleryimages', [
            'methods' => 'POST',
            'callback' => array($this, 'add_galleryimages'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/deletegalleryimages', [
            'methods' => 'POST',
            'callback' => array($this, 'delete_galleryimages'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/updategalleryimages', [
            'methods' => 'POST',
            'callback' => array($this, 'update_galleryimages'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'args' => array()
        ]);

        register_rest_route('aeroscroll/v1', '/activateonempremium', [
            'methods' => 'POST',
            'callback' => array($this, 'activateonempremium'),
            'args' => array(),
            'permission_callback' => function () {
                return current_user_can('edit_others_posts');
            },
            'permission_callback' => '__return_true',
            'args' => array()
        ]);

        
    }

    /**
     * Check request permissions
     *
     * @return bool
     */
    public function permissions()
    {
        return current_user_can('manage_options');
    }

    // LINK # get_posts
    public function get_posts($request)
    {
        $posts = array();

        // Get specific parameters: 
        $posttype = 'post';
        $taxonomy = "";
        $taxonomy_field = "";
        $taxonomy_terms = "";
        $searchkeyword = "";
        $perpage = -1;
        $page = -1;
        $orderby = "";
        $order = "";
        $infinite = "true";
        $fillpage = 0;
        $loopdata = false; // If it reaches the end of the "Posts" number then it send a Boolean as loopdata true in order to use Cache data
        $gridid = -1;
        $debug = "";

        // Set the Grid ID
        if ($request->get_param('gridid'))
            $gridid = $request->get_param('gridid');

        if ($request->get_param('posttype'))
            $posttype = $request->get_param('posttype');
        if ($request->get_param('taxonomy'))
            $taxonomy = $request->get_param('taxonomy');
        if ($request->get_param('taxonomy_field'))
            $taxonomy_field = $request->get_param('taxonomy_field');
        if ($request->get_param('taxonomy_terms'))
            $taxonomy_terms = $request->get_param('taxonomy_terms');
        if ($request->get_param('searchkeyword'))
            $searchkeyword = $request->get_param('searchkeyword');
        if ($request->get_param('orderby'))
            $orderby = $request->get_param('orderby');
        if ($request->get_param('order'))
            $order = $request->get_param('order');
        if ($request->get_param('infinite'))
            $infinite = $request->get_param('infinite');
        if ($request->get_param('fillpage'))
            $fillpage = $request->get_param('fillpage');
        if ($request->get_param('perpage')) {
            // Read Per Page value
            $perpage_int_value = ctype_digit($request->get_param('perpage')) ? intval($request->get_param('perpage')) : null;
            if ($perpage_int_value !== null) {
                $perpage = $perpage_int_value;
            }
        } else {
            $perpage = -1;
        }

        if ($request->get_param('page')) {
            // Read Page value
            $page_int_value = ctype_digit($request->get_param('page')) ? intval($request->get_param('page')) : null;
            if ($page_int_value !== null) {
                $page = $page_int_value;
            }
        }

        // Initialize args
        $args = array('posts_per_page' => $perpage, 'post_type' => $posttype);
        $countargs = array('posts_per_page' => -1, 'post_type' => $posttype);

        // Add aditional Optional args
        if ($page != -1)
            $args['paged'] = $page;
        if ($perpage != -1)
            $args['perpage'] = $perpage;

        if ($taxonomy != "") {
            $args['taxonomy'] = $taxonomy;
            $countargs['taxonomy'] = $taxonomy;
        }
        if ($taxonomy_field != "") {
            $args['field'] = $taxonomy_field;
            $countargs['field'] = $taxonomy_field;
        }
        if ($taxonomy_terms != "") {
            $args['terms'] = $taxonomy_terms;
            $countargs['terms'] = $taxonomy_terms;
        }
        if ($searchkeyword != "") {
            $args['s'] = $searchkeyword;
            $countargs['s'] = $searchkeyword;
        }
        if ($orderby != "") {
            $args['orderby'] = $orderby;
        }
        if ($order != "") {
            $args['order'] = $order;
        }

        $final_categories = array();
        $totalpages = 1;
        $totalcount = 0;

        $sort_by = "ID";
        $sort_by_dir = "ASC";
        if ($gridid != -1 && is_numeric($gridid)) {

            // Get categories for specific Grid
            global $wpdb;

            $table = $wpdb->prefix . 'aeroscroll_gallery';
            $query = "SELECT * FROM $table WHERE id = " . $gridid;
            $results = $wpdb->get_results($query);

            $grid_data = $results[0];


            foreach ($results as $grid) {
                if ($grid->sortby != null) {
                    $sort_by = $grid->sortby;
                }

                if ($grid->sortbydir != null) {
                    $sort_by_dir = $grid->sortbydir;
                }

                if ($grid->categories != null) {
                    if ($grid->categories != "") {
                        try {
                            $decoded = json_decode($grid->categories, true);
                            //echo $grid->categories;

                            if (is_array($decoded)) {
                                foreach ($decoded as $item) {
                                    array_push($final_categories, $item);
                                }
                            }
                        } catch (Exception $e) {
                            //$final_categories = 'error' . $e;
                        }
                    }
                }
            }

            //var_dump($final_categories);

            $categoryqueryarray = array(
                array(
                    'taxonomy' => 'category', // the custom vocabulary
                    'field'    => 'term_id',          // term_id, slug or name  (Define by what you want to search the below term)    
                    'terms'    => $final_categories,      // provide the term slugs
                ),
            );
            $countargs['tax_query'] = $categoryqueryarray;
            $args['orderby'] = $sort_by;
            $args['order'] = $sort_by_dir;
            $args['tax_query'] = $categoryqueryarray;


            $count_query = new WP_Query($countargs);
            $main_query = new WP_Query($args);
            $totalcount = $count_query->post_count;
            $maincount = $main_query->post_count;
            $posts = $main_query->posts;

            if ($totalcount > 20 && $perpage == -1) {
                $perpage = 20;
            }

            if ($perpage != -1 && $perpage != 0) {
                $totalpages = round($totalcount / $perpage);
                if (fmod($totalcount, $perpage) > 0) $totalpages++;
            }

            if ($totalpages < 1)
                $totalpages = 1;

            if ($infinite == "true") {

                // Activate Infinite functionality

                if ($maincount < $perpage && $fillpage == 1) {
                    // Get Posts from Next Page
                    $newpage = $page + 1;
                    if ($newpage > $totalpages) {
                        $newpage = 1;
                        $loopdata = true;
                    }

                    $newpage_args = $args;
                    $newpage_args['paged'] = $newpage;
                    $newpage_query = new WP_Query($newpage_args);
                    $newpage_query_posts = $newpage_query->posts;

                    // Now add posts until we reach the desired number
                    $missingposts = $perpage - $maincount;
                    $added = 0;

                    if ($totalpages === 1) {
                        $looptimes = 0;
                        if ($maincount > 0) $looptimes = intdiv($perpage, $maincount);

                        for ($k = 0; $k < $looptimes; $k++) {
                            foreach ($newpage_query_posts as $newpost) {
                                if ($added >= $missingposts)
                                    break;
                                array_push($posts, (object)$newpost);
                                $added++;
                            }
                        }
                    } else {
                        foreach ($newpage_query_posts as $newpost) {
                            if ($added >= $missingposts)
                                break;
                            array_push($posts, (object)$newpost);
                            $added++;
                        }
                    }
                }
            }

            // Now Append the Post featured images
            $_order_index = 1;
            foreach ($posts as $post) {
                $thumbnail_post_id = get_post_thumbnail_id($post->ID);
                $url = wp_get_attachment_url($thumbnail_post_id, 'full');
                $thumbnail = $url;

                $thumbnail_result = wp_get_attachment_image_src($thumbnail_post_id, "medium", true);
                if ($thumbnail_result != null) {
                    if (is_array($thumbnail_result) != null) {
                        $thumbnail = $thumbnail_result[0];
                    }
                }

                $post->featured_image = $url;
                $post->thumbnail_image = $thumbnail;
                $post->uid = (int)($post->ID . "0" . rand(100000, 999999));

                $neworder = $_order_index + ($page - 1) * $perpage;
                $post->order = $neworder;
                $post->permalink = get_permalink($post->ID);
                $post->timestamp = get_the_date( 'U', $post->ID );
                $image_title = get_the_title($thumbnail_post_id);
                $relative_url = wp_make_link_relative($url);
                $relative_path = str_replace($image_title, "", $relative_url);
                $image_id = get_post_thumbnail_id($post->ID);

                

                $_order_index++;
                if ($_order_index > $totalcount) $_order_index = 1;
            }
        }

        $obj = new stdClass();
        $obj->totalposts = $totalcount;
        $obj->numposts = count($posts);
        $obj->totalpages = $totalpages;
        $obj->posts = $posts;
        $obj->loopdata = $loopdata;
        $obj->debug = $debug;

        //sleep(1);

        return $obj;
    }

    protected static $option_key = '_apex_settings';
    protected static $defaults = array(
        'orientation' => 'vertical'
    );

    /**
     * Update settings
     */
    public function update_settings($request)
    {
        $settings = array(
            'orientation' => $request->get_param('orientation')
        );

        //remove any non-allowed indexes before save
        foreach ($settings as $i => $setting) {
            if (!array_key_exists($setting, self::$defaults)) {
                unset($settings[$i]);
            }
        }
        update_option(self::$option_key, $settings);

        return rest_ensure_response()->set_status(201);
    }
    /**
     * Get settings via API
     */
    public function get_settings($request)
    {
        $orientation = get_option('aeroscrollgrid_settings');

        return rest_ensure_response(array(
            'orientation' => $orientation
        ));
    }

    // >> GRIDS Table Endpoints
    // --------------------------------------

    public function get_grids($request)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aeroscroll_gallery';
        $query = "SELECT * FROM $table";
        $results = $wpdb->get_results($query);

        $categories = get_categories(array(
            'orderby' => 'name',
            'order'   => 'ASC'
        ));

        foreach ($results as $grid) {
            $final_categories = array();
            if ($grid->categories != null) {
                if ($grid->categories != "") {
                    try {
                        $decoded = json_decode($grid->categories, true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $item) {
                                foreach ($categories as $category) {
                                    if ($category->term_id == $item) {
                                        $obj_cat = new stdClass();
                                        $obj_cat->id = $category->term_id;
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

        $returnobj = new stdClass();
        $returnobj->grids = $results;
        $returnobj->allcategories = $categories;

        return $returnobj;
    }

    public function update_grid($params)
    {
        $published = $params['published'];
        $id = $params['id'];
        $title = $params['title'];
        $sidegap = $params['sidegap'];
        $cellgap = $params['cellgap'];
        $marginX = $params['marginX'];
        $marginY = $params['marginY'];
        $usemousewheel = $params['usemousewheel'];
        $imagegallery_id = $params['imagegallery_id'];
        $subtitle = $params['subtitle'];
        $shortcode = $params['shortcode'];
        $height = $params['height'];
        $orientation = $params['orientation'];
        $layout = $params['layout'];
        $type = $params['type'];
        $theme = $params['theme'];
        $themeonhover = $params['themeonhover'];
        $showreadmore = $params['showreadmore'];
        
        $autoscroll = $params['autoscroll'];
        $numrows = $params['numrows'];
        $numcolumns = $params['numcolumns'];
        $numcolumns_mid = $params['numcolumns_mid'];
        $numcolumns_low = $params['numcolumns_low'];
        $cellsize = $params['cellsize'];
        $cellsquared = $params['cellsquared'];
        $scrollspeed = $params['scrollspeed'];
        $isinfinite = $params['isinfinite'];
        $scrollbar = $params['scrollbar'];
        $categories = $params['categories'];
        $color_bg = $params['color_bg'];
        $color_theme_a = $params['color_theme_a'];
        $color_theme_title = $params['color_theme_title'];
        $color_theme_desc = $params['color_theme_desc'];
        $color_cell_bg = $params['color_cell_bg'];
        $sortby = $params['sortby'];
        $sortbydir = $params['sortbydir'];

        $social_share_facebook = $params['social_share_facebook'];
        $social_share_twitter = $params['social_share_twitter'];
        $social_share_pinterest = $params['social_share_pinterest'];
        $social_share_instagram = $params['social_share_instagram'];
        $social_share_tumblr = $params['social_share_tumblr'];
        $social_share_email = $params['social_share_email'];

        $watermark_type = $params['watermark_type'];
        $watermark_text = $params['watermark_text'];
        $watermark_fontsize = $params['watermark_fontsize'];
        $watermark_color = $params['watermark_color'];
        $watermark_opacity = $params['watermark_opacity'];
        $watermark_position = $params['watermark_position'];
        $watermark_image_url = $params['watermark_image_url'];
        $watermark_image_size = $params['watermark_image_size'];


        $advertisment_type = $params['advertisment_type'];
        $advertisment_text = $params['advertisment_text'];
        $advertisment_link = $params['advertisment_link'];
        $advertisment_fontsize = $params['advertisment_fontsize'];
        $advertisment_color = $params['advertisment_color'];
        $advertisment_opacity = $params['advertisment_opacity'];
        $advertisment_position = $params['advertisment_position'];
        $advertisment_image_url = $params['advertisment_image_url'];
        $advertisment_image_size = $params['advertisment_image_size'];

        $poweredbyactive = $params['poweredbyactive'];
        $articleinlightbox = $params['articleinlightbox'];

        $finalcategories = "[";
        if (is_array($categories)) {
            if (count($categories) > 0) {
                $ind = 0;
                foreach ($categories as $item) {
                    $finalcategories .= $item["id"];
                    if ($ind < count($categories) - 1) $finalcategories .= ",";
                    $ind++;
                }
            }
        }
        $finalcategories .= "]";

        if (gettype($published) == 'boolean') {
            $published == true ? $published = 1 : $published = 0;
        }

        if (gettype($social_spoweredbyactivehare_facebook) == 'boolean') {
            $poweredbyactive == true ? $poweredbyactive = 1 : $poweredbyactive = 0;
        }

        if($this->utils->is_pro())
        {
            if (gettype($social_share_facebook) == 'boolean') {
                $social_share_facebook == true ? $social_share_facebook = 1 : $social_share_facebook = 0;
            }
        } else {
            $social_share_facebook = 0;
        }

        if($this->utils->is_pro())
        {
            if (gettype($social_share_twitter) == 'boolean') {
                $social_share_twitter == true ? $social_share_twitter = 1 : $social_share_twitter = 0;
            }
        } else {
            $social_share_twitter = 0;
        }

        if($this->utils->is_pro())
        {
            if (gettype($social_share_pinterest) == 'boolean') {
                $social_share_pinterest == true ? $social_share_pinterest = 1 : $social_share_pinterest = 0;
            }
        } else {
            $social_share_pinterest = 0;
        }

        if($this->utils->is_pro())
        {
            if (gettype($social_share_instagram) == 'boolean') {
                $social_share_instagram == true ? $social_share_instagram = 1 : $social_share_instagram = 0;
            }
        } else {
            $social_share_instagram = 0;
        }

        if($this->utils->is_pro())
        {
            if (gettype($social_share_tumblr) == 'boolean') {
                $social_share_tumblr == true ? $social_share_tumblr = 1 : $social_share_tumblr = 0;
            }
        } else {
            $social_share_tumblr = 0;
        }

        if($this->utils->is_pro())
        {
            if (gettype($social_share_email) == 'boolean') {
                $social_share_email == true ? $social_share_email = 1 : $social_share_email = 0;
            }
        } else {
            $social_share_email = 0;
        }
        

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->update($table, array(
            'published' => $published,
            'title' => $title,
            'sidegap' => $sidegap,
            'cellgap' => $cellgap,
            'marginX' => $marginX,
            'marginY' => $marginY,
            'usemousewheel' => $usemousewheel,
            'imagegallery_id' => $imagegallery_id,
            'subtitle' => $subtitle,
            'shortcode' => $shortcode,
            'height' => $height,
            'orientation' => $orientation,
            'layout' => $layout,
            'type' => $type,
            'categories' => $finalcategories,
            'theme' => $theme,
            'themeonhover' => $themeonhover,
            'showreadmore' => $showreadmore,            
            'autoscroll' => $autoscroll,
            'color_bg' => $color_bg,
            'color_theme_a' => $color_theme_a,
            'color_theme_title' => $color_theme_title,
            'color_theme_desc' => $color_theme_desc,
            'color_cell_bg' => $color_cell_bg,
            'numrows' => $numrows,
            'numcolumns' => $numcolumns,
            'numcolumns_mid' => $numcolumns_mid,
            'numcolumns_low' => $numcolumns_low,
            'cellsize' => $cellsize,
            'cellsquared' => $cellsquared,
            'scrollspeed' => $scrollspeed,
            'isinfinite' => $isinfinite,
            'scrollbar' => $scrollbar,
            'updated_at' => date('Y-m-d H:m:s'),
            'sortby' => $sortby,
            'sortbydir' => $sortbydir,
            'social_share_facebook' => $social_share_facebook,
            'social_share_twitter' => $social_share_twitter,
            'social_share_pinterest' => $social_share_pinterest,
            'social_share_instagram' => $social_share_instagram,
            'social_share_facebook' => $social_share_facebook,
            'social_share_tumblr' => $social_share_tumblr,
            'social_share_email' => $social_share_email,
            'watermark_type' => $watermark_type,
            'watermark_text' => $watermark_text,
            'watermark_fontsize' => $watermark_fontsize,
            'watermark_color' => $watermark_color,
            'watermark_opacity' => $watermark_opacity,
            'watermark_position' => $watermark_position,
            'watermark_image_url' => $watermark_image_url,
            'watermark_image_size' => $watermark_image_size,
            'advertisment_type' => $advertisment_type,
            'advertisment_text' => $advertisment_text,
            'advertisment_link' => $advertisment_link,
            'advertisment_fontsize' => $advertisment_fontsize,
            'advertisment_color' => $advertisment_color,
            'advertisment_opacity' => $advertisment_opacity,
            'advertisment_position' => $advertisment_position,
            'advertisment_image_url' => $advertisment_image_url,
            'advertisment_image_size' => $advertisment_image_size,
            'poweredbyactive' => $poweredbyactive,
            'articleinlightbox' => $articleinlightbox,
        ), array('id' => $id));

        return $results;
    }

    public function add_grid($params)
    {
        $title = $params['title'];
        $sidegap = $params['sidegap'];
        $cellgap = $params['cellgap'];
        $marginX = $params['marginX'];
        $marginY = $params['marginY'];
        $usemousewheel = $params['usemousewheel'];
        $imagegallery_id = $params['imagegallery_id'];
        $subtitle = $params['subtitle'];
        $shortcode = $params['shortcode'];
        $height = $params['height'];
        $orientation = $params['orientation'];
        $layout = $params['layout'];
        $type = $params['type'];
        $theme = $params['theme'];
        $themeonhover = $params['themeonhover'];
        $showreadmore = $params['showreadmore'];
        $autoscroll = $params['autoscroll'];
        $numrows = $params['numrows'];
        $numcolumns = $params['numcolumns'];
        $numcolumns_mid = $params['numcolumns_mid'];
        $numcolumns_low = $params['numcolumns_low'];
        $cellsize = $params['cellsize'];
        $cellsquared = $params['cellsquared'];
        $scrollspeed = $params['scrollspeed'];
        $isinfinite = $params['isinfinite'];
        $scrollbar = $params['scrollbar'];
        $categories = $params['categories'];
        $color_bg = $params['color_bg'];
        $color_theme_a = $params['color_theme_a'];
        $color_theme_title = $params['color_theme_title'];
        $color_theme_desc = $params['color_theme_desc'];
        $color_cell_bg = $params['color_cell_bg'];
        $sortby = $params['sortby'];
        $sortbydir = $params['sortbydir'];

        $social_share_facebook = $params['social_share_facebook'];
        $social_share_twitter = $params['social_share_twitter'];
        $social_share_pinterest = $params['social_share_pinterest'];
        $social_share_instagram = $params['social_share_instagram'];
        $social_share_tumblr = $params['social_share_tumblr'];
        $social_share_email = $params['social_share_email'];

        $watermark_type = $params['watermark_type'];
        $watermark_text = $params['watermark_text'];
        $watermark_fontsize = $params['watermark_fontsize'];
        $watermark_color = $params['watermark_color'];
        $watermark_opacity = $params['watermark_opacity'];
        $watermark_position = $params['watermark_position'];
        $watermark_image_url = $params['watermark_image_url'];
        $watermark_image_size = $params['watermark_image_size'];

        $advertisment_type = $params['advertisment_type'];
        $advertisment_text = $params['advertisment_text'];
        $advertisment_link = $params['advertisment_link'];
        $advertisment_fontsize = $params['advertisment_fontsize'];
        $advertisment_color = $params['advertisment_color'];
        $advertisment_opacity = $params['advertisment_opacity'];
        $advertisment_position = $params['advertisment_position'];
        $advertisment_image_url = $params['advertisment_image_url'];
        $advertisment_image_size = $params['advertisment_image_size'];

        $poweredbyactive = $params['poweredbyactive'];
        $articleinlightbox = $params['articleinlightbox'];

        $finalcategories = "[";
        if (is_array($categories)) {
            if (count($categories) > 0) {
                $ind = 0;
                foreach ($categories as $item) {
                    $finalcategories .= $item["id"];
                    if ($ind < count($categories) - 1) $finalcategories .= ",";
                    $ind++;
                }
            }
        }
        $finalcategories .= "]";

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->insert($table, array(
            'title' => $title,
            'sidegap' => $sidegap,
            'cellgap' => $cellgap,
            'marginX' => $marginX,
            'marginY' => $marginY,
            'usemousewheel' => $usemousewheel,
            'imagegallery_id' => $imagegallery_id,
            'subtitle' => $subtitle,
            'shortcode' => $shortcode,
            'height' => $height,
            'orientation' => $orientation,
            'layout' => $layout,
            'type' => $type,
            'categories' => $finalcategories,
            'theme' => $theme,
            'themeonhover' => $themeonhover,
            'showreadmore' => $showreadmore,
            'autoscroll' => $autoscroll,
            'color_bg' => $color_bg,
            'color_theme_a' => $color_theme_a,
            'color_theme_title' => $color_theme_title,
            'color_theme_desc' => $color_theme_desc,
            'color_cell_bg' => $color_cell_bg,
            'numrows' => $numrows,
            'numcolumns' => $numcolumns,
            'numcolumns_mid' => $numcolumns_mid,
            'numcolumns_low' => $numcolumns_low,
            'cellsize' => $cellsize,
            'cellsquared' => $cellsquared,
            'scrollspeed' => $scrollspeed,
            'isinfinite' => $isinfinite,
            'scrollbar' => $scrollbar,
            'updated_at' => date('Y-m-d H:m:s'),
            'created_at' => date('Y-m-d H:m:s'),
            'sortby' => $sortby,
            'sortbydir' => $sortbydir,
            'social_share_facebook' => $social_share_facebook,
            'social_share_twitter' => $social_share_twitter,
            'social_share_pinterest' => $social_share_pinterest,
            'social_share_instagram' => $social_share_instagram,
            'social_share_facebook' => $social_share_facebook,
            'social_share_tumblr' => $social_share_tumblr,
            'social_share_email' => $social_share_email,
            'watermark_type' => $watermark_type,
            'watermark_text' => $watermark_text,
            'watermark_fontsize' => $watermark_fontsize,
            'watermark_color' => $watermark_color,
            'watermark_opacity' => $watermark_opacity,
            'watermark_position' => $watermark_position,
            'watermark_image_url' => $watermark_image_url,
            'watermark_image_size' => $watermark_image_size,
            'advertisment_type' => $advertisment_type,
            'advertisment_text' => $advertisment_text,
            'advertisment_link' => $advertisment_link,
            'advertisment_fontsize' => $advertisment_fontsize,
            'advertisment_color' => $advertisment_color,

            'advertisment_opacity' => $advertisment_opacity,
            'advertisment_position' => $advertisment_position,
            'advertisment_image_url' => $advertisment_image_url,
            'advertisment_image_size' => $advertisment_image_size,
            'poweredbyactive' => $poweredbyactive,
            'articleinlightbox' => $articleinlightbox,
        ), array('%s', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d',   '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%d', '%s', '%d', '%d', '%s', '%d', '%d', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%d', '%d', '%d'));
        // 59
        return $results;
    }

    public function delete_grid($params)
    {
        $id = $params['id'];

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->delete($table, array('id' => $id));
        return $results;
    }



    // >> IMAGE GALLERIES Table Endpoints
    // --------------------------------------

    public function get_imagegalleries($params)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
        $query = "SELECT * FROM $table";
        $results = $wpdb->get_results($query);

        $returnobj = new stdClass();
        $returnobj->imagegalleries = $results;

        return $returnobj;
    }

    public function add_imagegallery($params)
    {
        $title = $params['title'];
        $slug = $params['slug'];
        $description = $params['description'];
        $published = $params['published'];

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
        $results = $wpdb->insert($table, array(
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'published' => $published,
            'updated_at' => date('Y-m-d H:m:s'),
            'created_at' => date('Y-m-d H:m:s')
        ), array('%s', '%s', '%s', '%d', '%s', '%s'));

        return $results;
    }

    public function update_imagegallery($params)
    {
        $id = $params['id'];
        $title = $params['title'];
        $slug = $params['slug'];
        $description = $params['description'];
        $published = $params['published'];

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
        $results = $wpdb->update($table, array(
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'published' => $published,
            'updated_at' => date('Y-m-d H:m:s'),
        ), array('id' => $id));

        return $results;
    }

    public function delete_imagegallery($params)
    {
        $id = $params['id'];
        global $wpdb;

        // Also Delete entries from 'aeroscroll_gallery_imagegallery_images'
        $table2 = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $results = $wpdb->delete($table2, array('imagegallery_id' => $id));

        // delete Gallery Itsself
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
        $results = $wpdb->delete($table, array('id' => $id));

        return $results;
    }

    // Uploader

    public function upload_images($params)
    {
        $relativedir = "";
        foreach (getallheaders() as $name => $value) {
            if ($name == "relativedir") {
                $relativedir = $value;
            }
        }

        $result = 0;

        if (isset($_FILES)) {
            $files = array();

            //echo "relativedir: " . $relativedir . "\n";
            //echo "getallheaders: " . var_export(getallheaders(), true) . "\n";
            if ($relativedir != null && $relativedir != '') {
                $relativefullpath = getcwd() . $relativedir;

                if (!file_exists($relativefullpath)) {
                    mkdir($relativefullpath, 0777, true);
                }

                foreach ($_FILES as $var => $value) {
                    $filename = $value['name'];
                    $type = $value['type'];
                    $file_tmp = $value['tmp_name'];


                    $finalpath = $relativefullpath . "/" . $filename;
                    //$fileContent = file_get_contents($value['tmp_name']);
                    move_uploaded_file($value['tmp_name'], $finalpath);

                    array_push($files, $finalpath);
                    $result = 1;
                }
            }
        }

        echo '{ "result": ' . $result . ', "files": "' . var_export($_FILES, true) . '"}';
    }

    // File Manager

    public function list_folder($params)
    {
        if (!file_exists(getcwd() . '/wp-content/uploads/aeroscroll-gallery')) {
            mkdir(getcwd() . '/wp-content/uploads/aeroscroll-gallery', 0777, true);
        }

        if (!file_exists(getcwd() . '/wp-content/uploads/aeroscroll-gallery/__optimized')) {
            mkdir(getcwd() . '/wp-content/uploads/aeroscroll-gallery/__optimized', 0777, true);
        }

        $errors = array();
        $basedir = '/wp-content/uploads';
        $dir = getcwd() . '/wp-content/uploads'; // root
        $relativedir = $basedir; // relative root
        $eol = PHP_EOL;

        // target can be root, up, (name of folder), refresh
        if (isset($params)) {
            $target = $params['target'];
            $path = $params['path'];
            $paramrelativedir = $params['relativedir'];
            $isroot = 0;

            $listarray = array();
            $debug = "";
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'PNG', 'JPG', 'JPEG', 'jfif'];

            if (isset($path)) {
                if ($target != "root") {
                    if ($paramrelativedir != "root") $relativedir = json_decode($paramrelativedir);

                    if ($target != "up" && $target != "refresh") {
                        $dir = getcwd() . $paramrelativedir . "/" . $target;
                        //$relativedir = json_decode($params['relativedir']) . "/" . $target;   
                        $relativedir = $paramrelativedir . "/" . $target;
                    } else if ($target == "refresh") {
                        $dir = getcwd() . $paramrelativedir;
                        $relativedir = $paramrelativedir;

                        if (basename($paramrelativedir) == 'uploads') {
                            $isroot = 1;
                        }
                    } else {
                        if (basename($paramrelativedir) != 'uploads') {
                            $parent = dirname(getcwd() . $paramrelativedir, 1);
                            $dir = $parent;
                            $relativedir = str_replace(getcwd(), "", $parent);

                            if (basename($parent) === 'uploads') $isroot = 1;
                        } else {
                            $isroot = 1;
                        }
                    }
                } else {
                    $dir .= "/aeroscroll-gallery";
                    $relativedir .= "/aeroscroll-gallery";
                }

                $iterator = new DirectoryIterator($dir);

                foreach ($iterator as $ff) {
                    if ($ff->isFile()) {
                        if (in_array($ff->getExtension(), $allowedExtensions, true)) {
                            $item = new stdClass();
                            $item->file = $ff->getFilename();
                            $item->size = $ff->getSize();
                            $item->date = $ff->getMTime();
                            $item->folder = 0;
                            $item->relativeurl = $relativedir;

                            $optimized_url = getcwd() . $relativedir . '/__optimized/opt__' . $ff->getFilename();
                            $optimized_url_exists = file_exists($optimized_url);
                            if ($optimized_url_exists) {

                                $item->optimized = $optimized_url_exists;
                                $item->optimizedsize = filesize($optimized_url);
                            }

                            array_push($listarray, $item);
                        }
                    } else if ($ff->isDir()) {
                        if ($ff->getFilename() != '.' && $ff->getFilename() != '..' && $ff->getFilename() != '__optimized') {
                            $item = new stdClass();
                            $item->file = $ff->getFilename();
                            $item->size = $ff->getSize();
                            $item->date = $ff->getMTime();
                            $item->folder = 1;
                            $item->relativeurl = $relativedir;

                            array_push($listarray, $item);
                        }
                    }
                }

                array_multisort(array_column($listarray, 'folder'), SORT_DESC, $listarray);
            }
        }

        if (empty($errors) == true) {
            return '{ "result": 1, "list": ' . json_encode($listarray) . ', "path": ' . json_encode($relativedir) . ', "target":"' . $target . '", "debug": "' . $debug . '", "isroot": ' . $isroot . '}';
        } else {
            return '{ "result": 1 }';
        }
    }

    public function delete_item($params)
    {
        $files = $params['files'];
        $paramrelativedir = $params['relativedir'];
        $dir = getcwd() . $paramrelativedir;
        $result = 0;
        $filename = "";
        $error = "";

        try {
            if ($files != null) {
                if (is_array($files)) {
                    for ($x = 0; $x < count($files); $x++) {
                        $filename = $files[$x]["name"];
                        $isfolder = $files[$x]["folder"];

                        $fullpath = getcwd() . $paramrelativedir . "/" . $filename;

                        //echo "fullpath: " . $fullpath;
                        if ($isfolder == 1 || $isfolder == "1") {
                            if (!empty($fullpath) && is_dir($fullpath)) {
                                //echo "EXECUTED";
                                $dir  = new RecursiveDirectoryIterator($fullpath, RecursiveDirectoryIterator::SKIP_DOTS); //upper dirs are not included,otherwise DISASTER HAPPENS :)
                                $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);
                                foreach ($files as $f) {
                                    if (is_file($f)) {
                                        unlink($f);
                                    } else {
                                        $empty_dirs[] = $f;
                                    }
                                }
                                if (!empty($empty_dirs)) {
                                    foreach ($empty_dirs as $eachDir) {
                                        rmdir($eachDir);
                                    }
                                }
                                rmdir($fullpath);
                            }

                            $result = 1;
                        } else {
                            $fullpath = getcwd() . $paramrelativedir . "/" . $filename;

                            if (file_exists($fullpath)) {
                                if (unlink($fullpath)) {
                                    $result = 1;
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            var_dump($ex);
            //$error = $ex->getMessage();
        }


        return '{ "result": ' . $result . ', "error": "' . $error . '" }';
    }

    public function rename_item($params)
    {
        $newname = $params['newname'];
        $oldname = $params['name'];
        $paramrelativedir = $params['relativedir'];
        $dir = getcwd() . $paramrelativedir;
        $result = 0;

        if ($newname && $oldname) {
            $oldfilepath = $dir . "/" . $oldname;
            $newfilepath = $dir . "/" . $newname;

            if (file_exists($oldfilepath)) {
                $renameres = rename($oldfilepath, $newfilepath);
                $result = 1;
            }
        }

        return '{ "result": ' . $result . ', "oldname": "' .  $oldname . '" }';
    }

    public function create_folder($params)
    {
        $foldername = $params['name'];
        $paramrelativedir = $params['relativedir'];
        $dir = getcwd() . $paramrelativedir;
        $result = 0;

        if ($foldername != null) {
            $newfolderpath = $dir . "/" . $foldername;
            if (!file_exists($newfolderpath)) {
                mkdir($newfolderpath, 0777, true);
                $result = 1;
            }
        }

        return '{ "result": ' . $result . ' }';
    }



    public function add_galleryimages($params)
    {
        $error = "-";
        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $results = null;

        try {
            $images = $params['images'];
            $imagegallery_id = $params['imagegallery_id'];
            $result = 0;

            if ($images != null) {
                if (is_array($images)) {
                    $total_files = count($images);


                    for ($x = 0; $x < count($images); $x++) {
                        $image_name = $images[$x]["name"];
                        $relativedir = $images[$x]["relativedir"];
                        $image_order = $images[$x]["order"];
                        $created_at = $images[$x]["date"];

                        $encoded_relativedir = rawurlencode($relativedir);
                        $after_encoded_relativedir = str_replace("%2F", "/", $encoded_relativedir);

                        // MUST URL ESCAPE relativedir
                        $results = $wpdb->insert($table, array(
                            'imagegallery_id' => $imagegallery_id,
                            'title' => $title,
                            'description' => $description,
                            'image_name' => $image_name,
                            'image_order' => $image_order,
                            'relativedir' => $after_encoded_relativedir,
                            'created_at' => date('Y-m-d H:m:s')
                        ), array('%d', '%s', '%s', '%s', '%d', '%s', '%s'));
                    }

                    $result = 1;
                }
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $obj = new stdClass();
        $obj->result = $result;
        $obj->error = $error;

        return $obj;
    }

    public function get_galleryimages($params)
    {
        $error = "-";
        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $results = null;

        try {
            $imagegallery_id = $params['imagegallery_id'];
            $result = 0;

            if ($imagegallery_id != null) {
                $query = "SELECT * FROM $table WHERE imagegallery_id = " . $imagegallery_id . " ORDER BY image_order";
                $results = $wpdb->get_results($query);

                $img__order = 1;
                for ($x = 0; $x < count($results); $x++) {
                    $imagedata = $results[$x];

                    $image_name = $imagedata->image_name;
                    $relativedir = $imagedata->relativedir;

                    $fullpath = getcwd() . $relativedir . "/" . $image_name;
                    $optimizedpath = getcwd() . $relativedir . "/__optimized/" . "opt__" . $image_name;

                    // Check If Optimized Exists
                    if (file_exists($fullpath)) {
                        $results[$x]->exists = true;
                        $results[$x]->size = filesize($fullpath);
                        $results[$x]->resolution = getimagesize($fullpath);
                    } else {
                        $results[$x]->exists = false;
                    }

                    if (file_exists($optimizedpath)) {
                        $results[$x]->optimized = true;
                        $results[$x]->optimizedsize = filesize($optimizedpath);
                    } else {
                        $results[$x]->optimized = false;
                    }

                    $results[$x]->name = $results[$x]->image_name;
                    $results[$x]->order = intval($results[$x]->image_order);
                    $results[$x]->image = get_site_url() . $relativedir . "/" . $image_name;

                    unset($results[$x]->image_name);

                    $img__order++;
                }

                $result = 1;
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $returnobj = new stdClass();
        $returnobj->result = $result;
        $returnobj->galleryimages = $results;
        $returnobj->error = $error;

        return $returnobj;
    }

    public function delete_galleryimages($params)
    {
        $imagestodelete = $params['images'];
        $error = "-";

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $result = 0;

        $image_id = "";
        try {
            if ($imagestodelete != null) {
                if (is_array($imagestodelete)) {
                    $total_images = count($imagestodelete);
                    for ($x = 0; $x < $total_images; $x++) {
                        $image_id = $imagestodelete[$x]["id"];
                        $results = $wpdb->delete($table, array('id' => $image_id));
                    }

                    $result = 1;
                }
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $returnobj = new stdClass();
        $returnobj->result = $result;
        $returnobj->error = $error;

        return $returnobj;
    }

    public function update_galleryimages($params)
    {
        $images = $params['images'];

        $error = "-";

        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
        $result = 0;

        try {
            if ($images != null) {
                if (is_array($images)) {
                    $query = "INSERT INTO  $table (id, title, description, image_order) VALUES ";

                    for ($x = 0; $x < count($images); $x++) {
                        $_image = $images[$x];
                        $_id = $_image["id"];
                        $_title = $_image["title"];
                        $_description = $_image["description"];
                        $_order = $_image["order"];
                        $query .= "($_id, '$_title', '$_description', $_order)";
                        if ($x < count($images) - 1) $query .= ",";
                    }

                    $query .= " ON DUPLICATE KEY UPDATE image_order = VALUES(image_order), title = VALUES(title), description = VALUES(description);";

                    $results = $wpdb->get_results($query);
                    $result = 1;
                }
            }
        } catch (Exception $ex) {
            $error = $ex;
        }

        $returnobj = new stdClass();
        $returnobj->result = $result;
        $returnobj->error = $error;

        return $returnobj;
    }

    // LINK # get_imagegallerydata
    public function get_imagegallerydata($request)
    {
        $galleryimages = array();
        $searchkeyword = "";
        $perpage = -1;
        $page = -1;
        $orderby = "";
        $order = "";
        $infinite = "true";
        $loopdata = false; // If it reaches the end of the "Posts" number then it send a Boolean as loopdata true in order to use Cache data
        $imagegalleryid = -1;
        $galleryid = -1;
        $debug = "";

        // Set the Grid ID
        if ($request->get_param('imagegallery_id'))
            $imagegalleryid = $request->get_param('imagegallery_id');

        if ($request->get_param('gallery_id'))
            $galleryid = $request->get_param('gallery_id');

        if ($request->get_param('searchkeyword'))
            $searchkeyword = $request->get_param('searchkeyword');
        if ($request->get_param('orderby'))
            $orderby = $request->get_param('orderby');
        if ($request->get_param('order'))
            $order = $request->get_param('order');
        if ($request->get_param('infinite'))
            $infinite = $request->get_param('infinite');
        if ($request->get_param('perpage')) {

            // Read Per Page value
            $perpage_int_value = ctype_digit($request->get_param('perpage')) ? intval($request->get_param('perpage')) : null;
            if ($perpage_int_value !== null) {
                $perpage = $perpage_int_value;
            }
        }
        if ($request->get_param('page')) {
            // Read Page value
            $page_int_value = ctype_digit($request->get_param('page')) ? intval($request->get_param('page')) : null;
            if ($page_int_value !== null) {
                $page = $page_int_value;
            }
        }
        
        $totalpages = 1;
        $totalcount = 0;

        if ($imagegalleryid != -1 && is_numeric($imagegalleryid)) {

            // Get categories for specific Grid
            global $wpdb;
            $table = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
            $table_gallery = $wpdb->prefix . 'aeroscroll_gallery';

            // First Image Gallery Parameters
            $query_gallery_properties = "SELECT * FROM $table_gallery WHERE id = " . $galleryid;
            $results_gallery_properties = $wpdb->get_results($query_gallery_properties);

            $sort_by = "id";
            $sort_by_dir = "ASC";
            $foundsort_by = $results_gallery_properties[0]->sortby;
            $foundsort_by_dir = $results_gallery_properties[0]->sortbydir;

            if ($foundsort_by != "" && $foundsort_by != null) {
                $sort_by = $foundsort_by;
                if ($sort_by == "filename") $sort_by = "image_name";
            }

            if ($foundsort_by_dir != "" && $foundsort_by_dir != null) {
                $sort_by_dir = $foundsort_by_dir;
            }

            // FIRST MAKE QUERY COUNT IMAGES
            $query_count = "SELECT COUNT(*) AS totalimages FROM $table WHERE imagegallery_id = " . $imagegalleryid;
            $results_count = $wpdb->get_results($query_count);

            $totalcount = intval($results_count[0]->totalimages);
            $maincount = $totalcount;



            if ($perpage != -1 && $perpage != 0)
                $totalpages = round($totalcount / $perpage);
            if ($totalpages < 1)
                $totalpages = 1;

            if ($infinite == "true") {
                // Activate Infinite functionality

                // A: LESS THAN PERPAGE
                if ($maincount < $perpage) {
                    // Get Posts from Next Page
                    $newpage = $page + 1;
                    if ($newpage > $totalpages) {
                        $newpage = 1;
                        $loopdata = true;
                    }

                    // Now add posts until we reach the desired number
                    $missingimages = $perpage - $maincount;

                    $query = "SELECT * FROM $table WHERE imagegallery_id = " . $imagegalleryid . " ORDER BY " . $sort_by . " " . $sort_by_dir;
                    $results = $wpdb->get_results($query);
                    $newpage_query_images = $results;
                    $galleryimages = $results;

                    foreach ($galleryimages as $img) {
                        $img->uid = (int)($img->id . "0" . rand(1000000, 9999999));
                    }


                    $added = 0;
                    if ($totalpages === 1) {
                        $looptimes = 0;
                        if ($maincount > 0) $looptimes = intdiv($perpage, $maincount);

                        for ($k = 0; $k < $looptimes; $k++) {
                            foreach ($newpage_query_images as $newimage) {
                                if ($added >= $missingimages)
                                    break;
                                $newimage->uid = (int)($newimage->id . "0" . rand(1000000, 9999999));
                                array_push($galleryimages, clone $newimage);
                                $added++;
                            }
                        }
                    } else {
                        foreach ($newpage_query_images as $newimage) {
                            if ($added >= $missingimages)
                                break;
                            $newimage->uid = (int)($newimage->id . "0" . rand(1000000, 9999999));
                            array_push($galleryimages, clone $newimage);
                            $added++;
                        }
                    }

                    $ind = 0;
                    foreach ($galleryimages as $index => $img) {
                        $decoded_relativedir = urldecode($img->relativedir);
                        $img->url = get_site_url() . $img->relativedir . "/" . $img->image_name;
                        $img->thumbnail_image = get_site_url() . $img->relativedir . "/" . $img->image_name;
                        $img->featured_image = get_site_url() . $img->relativedir . "/" . $img->image_name;
                        $img->image_order = intval($img->image_order);

                        if ($ind > $maincount - 1) $ind = 0;
                        $img->order = $ind + 1;

                        $img->imageexists = true;

                        $ind++;

                        $realpath = getcwd() . $decoded_relativedir . '/' . $img->image_name;
                        if (!file_exists($realpath)) {
                            $img->imageexists = false;
                        }

                        $optimizedurl = get_site_url() . $img->relativedir . "/__optimized/" . "opt__" . $img->image_name;
                        $optimizedpath = getcwd() . $decoded_relativedir . '/__optimized/opt__' . $img->image_name;

                        if (file_exists($optimizedpath)) {
                            $img->thumbnail_image = $optimizedurl;
                        }

                        

                    }
                } else {
                    // B: MORE THAN PERPAGE
                    $offset = ($perpage * ($page - 1)); // offset = (limit * pageNumber) - limit;
                    //$offset = ($perpage * $page) - $perpage; // offset = (limit * pageNumber) - limit;
                    $query = "SELECT * FROM $table WHERE imagegallery_id = " . $imagegalleryid . " ORDER BY " . $sort_by . " ASC LIMIT " . $perpage . " OFFSET " . $offset;
                    $results = $wpdb->get_results($query);
                    $galleryimages = $results;

                    foreach ($galleryimages as $index => $img) {
                        $decoded_relativedir = urldecode($img->relativedir);
                        $img->url = get_site_url() . $img->relativedir . "/" . $img->image_name;
                        $img->thumbnail_image = get_site_url() . $img->relativedir . "/" . $img->image_name;
                        $img->featured_image = get_site_url() . $img->relativedir . "/" . $img->image_name;
                        $img->image_order = intval($img->image_order);

                        $calculated_order = $offset + $index + 1;
                        $img->order = $calculated_order;
                        $img->imageexists = true;
                        $img->moreperpage = true;


                        $realpath = getcwd() . $decoded_relativedir . '/' . $img->image_name;
                        if (!file_exists($realpath)) {
                            $img->imageexists = false;
                        }

                        $optimizedurl = get_site_url() . $img->relativedir . "/__optimized/" . "opt__" . $img->image_name;
                        $optimizedpath = getcwd() . $decoded_relativedir . '/__optimized/opt__' . $img->image_name;

                        if (file_exists($optimizedpath)) {
                            $img->thumbnail_image = $optimizedurl;
                        }

                        
                    }
                }
            }
        }

        $_gals = array();
        for ($i = 1; $i <= count($galleryimages); $i++) {
            array_push($_gals, (object)$galleryimages[$i - 1]);
            $_gals[$i - 1]->_order = $i;
        }

        $obj = new stdClass();
        $obj->totalimages = $totalcount;
        $obj->numimages = count($galleryimages);
        $obj->totalpages = $totalpages;
        $obj->galleryimages = $galleryimages;
        $obj->loopdata = $loopdata;
        $obj->debug = $debug;

        return $obj;
    }

    public function activateonempremium($request) {
        $serviceactive = 0;
        $debug = "";

        if ($request->get_param('serviceactive'))
            $serviceactive = $request->get_param('serviceactive');

        $api_url = 'http://www.aeroscroll.com/wp-json/aeroscroll-manager/v1/onemonthpremiumservice';
        if ($serviceactive == "1" || $serviceactive == 1 || $serviceactive == true)
        {
            $serviceactive = 1;
        } else {
            $serviceactive = 0;
        }

        $domain = "";
        try
        {
            $domain = urlencode(get_site_url());
        } catch(Exception $ex)
        {

        }

        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;


        $debug = $user_email;

        $data_arr = array(
            'token' => 'b5x$gj18n',
            'user_email' => $user_email,
            'serviceactive' => $serviceactive,
            'domain' => $domain
        );
        $body = json_encode($data_arr);

        $remote = wp_remote_post($api_url, array(
            'method'  => 'POST',
            'body'    => $body,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
            )
        ));
        if (is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote) || empty(wp_remote_retrieve_body($remote))) {
            $debug = "ERROR: " . $remote['body'];
        }

        
        $result = '{ "r": -1 }';
        if($remote['response']['code'] == 200)
        {
            $result = json_decode($remote['body']);
        }

        // Finally UPDATE Table of specific Gallery
        global $wpdb;
        $table = $wpdb->prefix . 'aeroscroll_gallery';
        $results = $wpdb->update($table, array(
            'poweredbyactive' => $serviceactive
        ), array('id' => $id));


        $obj = new stdClass();
        $obj->result = $result;
        $obj->debug = $debug;

        return $obj;
    }
}

<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.aeroscroll.com
 * @since      1.0.0
 *
 * @package    aeroscroll_gallery
 * @subpackage aeroscroll_gallery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    aeroscroll_gallery
 * @subpackage aeroscroll_gallery/includes
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Aeroscroll_Gallery_Activator
{


    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function aeroscroll_activate()
    {
        self::aeroscroll_create_table_if_not_exist();
    }

    public static function aeroscroll_create_table_if_not_exist()
    {
        try {
            global $wpdb;

            // Let's not break the site with exception messages
            $wpdb->hide_errors();

            if (! function_exists('dbDelta')) {
                require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            }

            $collate = '';

            if ($wpdb->has_cap('collation')) {
                $collate = $wpdb->get_charset_collate();
            }

            // Create the GRIDS TABLE
            // ------------------
            $table_name = $wpdb->prefix . 'aeroscroll_gallery';
            if ($wpdb->get_var("SHOW TABLES LIKE {$wpdb->prefix}aeroscroll_gallery") !== $table_name) {
                $schema = "
                    CREATE TABLE {$wpdb->prefix}aeroscroll_gallery (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    title VARCHAR(120) DEFAULT '',
                    subtitle VARCHAR(256) DEFAULT '',
                    shortcode VARCHAR(30) NOT NULL DEFAULT '',
                    orientation  VARCHAR(30) NOT NULL DEFAULT 'vertical',
                    height VARCHAR(30) NOT NULL DEFAULT '400px',
                    layout VARCHAR(30) NOT NULL,
                    type VARCHAR(30) NOT NULL,
                    categories TEXT NOT NULL,
                    theme VARCHAR(30) NOT NULL,
                    numrows INT NOT NULL DEFAULT 2,
                    numcolumns INT NOT NULL DEFAULT 3,
                    numcolumns_mid INT NOT NULL DEFAULT 2,
                    numcolumns_low INT NOT NULL DEFAULT 1,
                    cellsize INT NOT NULL DEFAULT 200,
                    cellsquared TINYINT NOT NULL DEFAULT 0,
                    scrollspeed TINYINT NOT NULL DEFAULT 6,
                    isinfinite TINYINT NOT NULL DEFAULT 1,
                    scrollbar TINYINT NOT NULL DEFAULT 1,
                    hoveranimation VARCHAR(30) NOT NULL DEFAULT 'none',
                    published TINYINT NOT NULL DEFAULT 1,
                    cellgap INT(11) DEFAULT 0,
                    sortby VARCHAR(30) NOT NULL DEFAULT 'id',
                    sortbydir VARCHAR(30) NOT NULL DEFAULT 'ASC',
                    sidegap INT(11) DEFAULT 0,
                    marginX INT(11) DEFAULT 0,
                    marginY INT(11) DEFAULT 0,
                    usemousewheel TINYINT DEFAULT 1,
                    autoscroll TINYINT DEFAULT 0,
                    imagegallery_id INT(11) DEFAULT 0,
                    themeonhover TINYINT NOT NULL DEFAULT 1,
                    color_theme_desc VARCHAR(30) NOT NULL DEFAULT '#bfbfbfff',
                    color_theme_title VARCHAR(30) NOT NULL DEFAULT '#ffffffff',
                    color_theme_a VARCHAR(30) NOT NULL DEFAULT '#2d2f31ff',
                    color_cell_bg VARCHAR(30) NOT NULL DEFAULT '#dbdfe5ff',
                    showreadmore TINYINT NOT NULL DEFAULT 1,
                    articleinlightbox TINYINT NOT NULL DEFAULT 0,
                    color_bg VARCHAR(30) NOT NULL DEFAULT '#ffffff00',
                    social_share_facebook TINYINT DEFAULT 0,
                    social_share_twitter TINYINT DEFAULT 0,
                    social_share_pinterest TINYINT DEFAULT 0,
                    social_share_instagram TINYINT DEFAULT 0,
                    social_share_tumblr TINYINT DEFAULT 0,
                    social_share_email TINYINT DEFAULT 0,
                    watermark_type TINYINT DEFAULT 0,
                    watermark_text VARCHAR(120) NOT NULL DEFAULT '',
                    watermark_fontsize INT NOT NULL DEFAULT 16,
                    watermark_color VARCHAR(30) NOT NULL DEFAULT '#000000',
                    watermark_opacity TINYINT DEFAULT 30,
                    watermark_position TINYINT DEFAULT 7,
                    watermark_image_url VARCHAR(2048) NOT NULL DEFAULT '',
                    watermark_image_size TINYINT DEFAULT 20,
                    advertisment_type TINYINT DEFAULT 0,
                    advertisment_text VARCHAR(120) NOT NULL DEFAULT '',
                    advertisment_link VARCHAR(2048) NOT NULL DEFAULT '',
                    advertisment_fontsize INT NOT NULL DEFAULT 16,
                    advertisment_color VARCHAR(30) NOT NULL DEFAULT '#000000',
                    advertisment_opacity TINYINT DEFAULT 30,
                    advertisment_position TINYINT DEFAULT 7,
                    advertisment_image_url VARCHAR(2048) NOT NULL DEFAULT '',
                    advertisment_image_size TINYINT DEFAULT 20,
                    poweredbyactive TINYINT DEFAULT 0,
                    PRIMARY KEY (id)
                ) $collate;";

                dbDelta($schema);
            }

            // Create the IMAGE GALLERIES TABLE
            // ------------------
            $table_name2 = $wpdb->prefix . 'aeroscroll_gallery_imagegalleries';
            if ($wpdb->get_var("SHOW TABLES LIKE {$wpdb->prefix}aeroscroll_gallery_imagegalleries") !== $table_name2) {
                $schema_ig = "
                    CREATE TABLE {$wpdb->prefix}aeroscroll_gallery_imagegalleries (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    title VARCHAR(120),
                    slug VARCHAR(256),
                    description TEXT NOT NULL,
                    ordering BIGINT NOT NULL,
                    published TINYINT NOT NULL DEFAULT 1,
                    PRIMARY KEY (id)
                ) $collate;";

                dbDelta($schema_ig);
            }

            // Create the UPLOADED IMAGES TABLE
            // ------------------
            $table_name3 = $wpdb->prefix . 'aeroscroll_gallery_imagegallery_images';
            if ($wpdb->get_var("SHOW TABLES LIKE {$wpdb->prefix}aeroscroll_gallery_imagegallery_images") !== $table_name3) {
                $schema_ig = "
                    CREATE TABLE {$wpdb->prefix}aeroscroll_gallery_imagegallery_images (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    imagegallery_id BIGINT UNSIGNED NOT NULL,  
                    title VARCHAR(120),
                    description TEXT,
                    relativedir VARCHAR(256),
                    image_order BIGINT UNSIGNED NOT NULL DEFAULT 1,
                    image_name VARCHAR(256) NOT NULL,
                    image_type VARCHAR(120),
                    image_resolution BIGINT UNSIGNED,
                    media_gallery_id BIGINT UNSIGNED,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $collate;";

                dbDelta($schema_ig);
            }

            // Create the SETTINGS TABLE
            // ------------------
            $table_name = $wpdb->prefix . 'aeroscroll_gallery_settings';
            if ($wpdb->get_var("SHOW TABLES LIKE {$wpdb->prefix}aeroscroll_gallery_settings") !== $table_name) {
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
                ) $collate;";

                dbDelta($schema);

                $table_cols = $wpdb->get_results("INSERT IGNORE INTO {$wpdb->prefix}aeroscroll_gallery_settings (email,serial_key_product_id) VALUES (``,``);");
            }
        } catch (Exception $e) {
            throw new Exception('Aeroscroll Gallery Error : {esc_html_e( $e->getMessage() )}');
        }
    }
}

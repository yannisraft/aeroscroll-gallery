<?php

namespace Aeroscroll;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Aeroscroll utils.
 *
 * Aeroscroll utils handler class is responsible for different utility methods
 * used by Aeroscroll.
 *
 * @since 1.0.0
 */
if (!class_exists('Aeroscroll\utils')) {
    class utils
    {

        public function __construct()
        {
        }

        public static function is_pro()
        {
            return defined('AEROSCROLL_GALLERY_PRO_VERSION');
        }

        public static function get_version()
        {
            $aeroscroll_gallery_version = '';
            if (defined('AEROSCROLL_GALLERY_PRO_VERSION')) {
                $aeroscroll_gallery_version = AEROSCROLL_GALLERY_PRO_VERSION;
            } else {
                $aeroscroll_gallery_version = AEROSCROLL_GALLERY_VERSION;
            }
            return defined($aeroscroll_gallery_version);
        }

        public static function get_iteration()
        {
            $aeroscroll_gallery_iteration = '';
            if (defined('AEROSCROLL_GALLERY_PRO_VERSION')) {
                $aeroscroll_gallery_iteration = AEROSCROLL_GALLERY_PRO_ITERATION;
            } else {
                $aeroscroll_gallery_iteration = AEROSCROLL_GALLERY_ITERATION;
            }
            return $aeroscroll_gallery_iteration;
        }
    }
}

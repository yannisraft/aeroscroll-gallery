<?php

namespace Aeroscroll;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Aeroscroll_Utils
 *
 * Aeroscroll_Utils handler class is responsible for different utility methods
 * used by Aeroscroll.
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Aeroscroll\Aeroscroll_Utils' ) ) {
	class Aeroscroll_Utils {


		public function __construct() {
		}

		

		public static function aeroscroll_is_pro() {
			return defined( 'AEROSCROLL_GALLERY_PRO_VERSION' );
		}

		public static function aeroscroll_has_pro() {
			$has_pro = false;
			if ( class_exists( 'Aeroscroll_Gallery_Pro' ) ) {
				$has_pro = true;
			}
			return $has_pro;
		}



		public static function aeroscroll_get_version() {
			$aeroscroll_gallery_version = '';
			if ( defined( 'AEROSCROLL_GALLERY_PRO_VERSION' ) ) {
				$aeroscroll_gallery_version = AEROSCROLL_GALLERY_PRO_VERSION;
			} else {
				$aeroscroll_gallery_version = AEROSCROLL_GALLERY_VERSION;
			}
			return defined( $aeroscroll_gallery_version );
		}

		public static function aeroscroll_get_iteration() {
			$aeroscroll_gallery_iteration = '';
			if ( defined( 'AEROSCROLL_GALLERY_PRO_VERSION' ) ) {
				$aeroscroll_gallery_iteration = AEROSCROLL_GALLERY_PRO_ITERATION;
			} else {
				$aeroscroll_gallery_iteration = AEROSCROLL_GALLERY_ITERATION;
			}
			return $aeroscroll_gallery_iteration;
		}

		// Function to get the size of an image in kilobytes
		public static function get_image_size_in_bytes( $image_url ) {
			// Fetch headers of the image
			$headers = get_headers( $image_url, 1 );

			// Check if the Content-Length header is set
			if ( isset( $headers['Content-Length'] ) ) {
				// Get the size in bytes
				$size_in_bytes = $headers['Content-Length'];

				// Convert bytes to kilobytes
				$size_in_kb = $size_in_bytes;// / 1024;

				return round( $size_in_kb, 2 ); // Return size rounded to 2 decimal places
			}

			return null; // Return null if size can't be determined
		}

		// Function to rename a media library file
		public static function rename_media_file( $attachment_id, $new_filename, $error ) {
			global $wp_filesystem;

			try {
				// Initialize the WordPress Filesystem
				if ( false === WP_Filesystem() ) {
					//return new WP_Error( 'filesystem_error', 'Could not initialize the WordPress filesystem.' );
					$error = 'Could not initialize the WordPress filesystem.';
				}

				// Get the attachment post object
				/* $attachment = get_post( $attachment_id );

				// Check if the attachment exists
				if ( ! $attachment || $attachment->post_type !== 'attachment' ) {
					//return new WP_Error( 'invalid_attachment', 'Invalid attachment ID.' );
					$error = 'Invalid attachment ID.';
				}

				// Get the current file path
				$file_path  = get_attached_file( $attachment_id );
				$upload_dir = wp_upload_dir(); // Get the upload directory

				// Get the file's directory and current filename
				$file_dir      = pathinfo( $file_path, PATHINFO_DIRNAME );
				$old_filename  = basename( $file_path );
				$new_file_path = $file_dir . '/' . $new_filename;

				// Use WP_Filesystem to move (rename) the file
				if ( ! $wp_filesystem->move( $file_path, $new_file_path ) ) {
					//return new WP_Error( 'file_rename_error', 'Could not rename the file on the server using WP_Filesystem.' );
					$error = 'Could not rename the file on the server using WP_Filesystem.';
				}

				// Update the database with the new file path
				update_attached_file( $attachment_id, $new_file_path );

				// Update the post title (optional, but usually desired)
				$new_title = pathinfo( $new_filename, PATHINFO_FILENAME ); // Get filename without extension
				wp_update_post(
					array(
						'ID'         => $attachment_id,
						'post_title' => $new_title,
						'post_name'  => sanitize_title( $new_title ), // Update the slug
					)
				);

				// If it's an image, regenerate metadata
				$filetype = wp_check_filetype( $new_file_path );
				if ( strpos( $filetype['type'], 'image' ) !== false ) {
					// Regenerate image metadata (sizes, thumbnails)
					$metadata = wp_generate_attachment_metadata( $attachment_id, $new_file_path );
					wp_update_attachment_metadata( $attachment_id, $metadata );
				} */
			} catch ( Error $e ) {
				$error = $e->getMessage();
			}

			return $error;
		}
	}
}

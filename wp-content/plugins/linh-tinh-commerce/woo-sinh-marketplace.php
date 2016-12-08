<?php
/*
Plugin Name: Woo Sinh Marketplace
Author: Sinh Doan
Description: Thêm các chức năng cần thiết cho 1 trang web C2C
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( !defined( 'linh_tinh_commerce_dir' ) ) 
	define( 'linh_tinh_commerce_dir', trailingslashit( dirname( __FILE__ ) ) );

// Override Woocomerce template
require_once( linh_tinh_commerce_dir . 'override_woocommerce_template.php' );
require_once( trailingslashit( dirname( __FILE__ ) ) . 'rating_report_page.php' );

if ( ! class_exists('Woo_Sinh_Marketplace') ) {
	class Woo_Sinh_Marketplace {
		public function __construct() {		
			// In the user's first login, system auto generate a post for a another user can rate or review.
			$this->sinh_auto_create_post();
		}
		
		public function sinh_auto_create_post() {
			global $current_user;
			get_currentuserinfo();
			$category = get_category_by_slug( 'user-rating' );
			if ( $category == false ) {
				wp_insert_term( 'User Rating', 'category', array('slug' => 'user-rating') );
			}
			$user_post = array(
				'post_title' 	=> $current_user->display_name,
				'post_status' 	=> 'publish',
				'post_content'	=> '[ratings]',
				'post_category' => array( $category->term_id )
			);

			if ( self::sinh_get_post_by_title($user_post['post_title']) == null ) {
				// insert the post to the database
				wp_insert_post( $user_post );
			}

			if ( self::sinh_is_vendor($current_user) ) {
				$vendor_rating_shortcode = '[sinh_view_rating user_email=' . $current_user->user_email . ']';
				$pv_shop_description = get_user_meta( $current_user->ID, 'pv_shop_description', true );
				if ( strpos($pv_shop_description, $vendor_rating_shortcode) == false ) {
					update_user_meta( $current_user->ID, 'pv_shop_description', $vendor_rating_shortcode );
				}
			}
		}

		/**
		 *   Determine if a post exists based on post_title
		 *
		 *   @param $post_title string unique post title
		 * 	 return post_id if exists, return NULL if not found	
		 */
		public static function sinh_get_post_by_title( $post_title ) {
			global $wpdb;
			$query = "SELECT ID FROM $wpdb->posts 
						WHERE post_title LIKE '%s'
						AND post_status  LIKE '%s'";
			$args = array();
			$args[] = $post_title;
			$args[] = 'publish';

			return $wpdb->get_var( $wpdb->prepare($query, $args) );
		}

		/**
		 *   Determine if a user is vendor or not
		 *
		 *   @param $user is user object
		 * 	 return true if exists, return false if not
		 */
		public static function sinh_is_vendor( $user ) {
			if ( implode(', ', $user->roles) == 'vendor' )
				return true;
			return false;
		}

		/**
		 *   Get user rating post link
		 *
		 *   @param $user is user object
		 * 	 return link. If not exists, create then return
		 */
		public static function sinh_get_user_rating_guid( $user ) {
			$post_id = self::sinh_get_post_by_title( $user->display_name );
			$post = get_post( $post_id );
			return $post->guid;
		}

		/**
		 *   Get user rating post link by title
		 *
		 *   @param $user is user object
		 * 	 return link
		 */
		public static function sinh_get_rating_guid_by_title( $title ) {
			$post_id = self::sinh_get_post_by_title( $title );
			$post = get_post( $post_id );
			return $post->guid;
		}

		public function sinh_create_view_ratings_shortcode( $args ) {
			$user_id = email_exists($args['user_email']);
			if ( $user_id )
				$user = get_user_by( 'ID', $user_id );
			if ( $user != false ) {
				$post_id = Woo_Sinh_Marketplace::sinh_get_post_by_title( $user->display_name );
			}
			if ( $post_id != null && function_exists('the_ratings_results') )
				return '<strong>Vendor: ' . $user->display_name . '</strong></br>' . the_ratings_results( $post_id );
			return '';	
		}
	}
}
function Woo_Sinh_Marketplace_load() {
        global $sinh_plugin;
        $sinh_plugin = new Woo_Sinh_Marketplace();
}
add_action( 'plugins_loaded', 'Woo_Sinh_Marketplace_load' );
add_shortcode( 'sinh_view_rating', array('Woo_Sinh_Marketplace', 'sinh_create_view_ratings_shortcode') );
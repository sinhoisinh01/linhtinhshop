<?php
/*
Plugin Name: Woo Sinh Marketplace
Author: Sinh Doan
Description: Thêm các chức năng cần thiết cho 1 trang web C2C
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists('Woo_Sinh_Marketplace') ) {
	class Woo_Sinh_Marketplace {
		public function __construct() {		
			// When create a user, system auto generate a post for a another user can rate or review.
			add_action( 'wp_head', array($this, 'sinh_auto_create_post') );
		}
		
		function sinh_auto_create_post() {
			global $current_user;
			get_currentuserinfo();
			$user_post = array(
				'post_title' 	=> $current_user->display_name,
				'post_status' 	=> 'publish',
				'post_category' => array('user-rating')
			);

			if ( $this->get_post_by_title($user_post['post_title']) == null ) {
				// insert the post to the database
				wp_insert_post( $user_post );
			}
		}

		/**
		 *   Determine if a post exists based on post_title
		 *
		 *   @param $post_title string unique post title
		 * 	 return post_id if exists, return NULL if not found	
		 */
		private function get_post_by_title( $post_title ) {
			global $wpdb;
			$query = "SELECT ID FROM $wpdb->posts 
						WHERE post_title LIKE '%s'
						AND post_status  LIKE '%s'";
			$args = array();
			$args[] = $post_title;
			$args[] = 'publish';

			return $wpdb->get_var( $wpdb->prepare($query, $args) );
		}
	}
}
function Woo_Sinh_Marketplace_load() {
        global $sinh_plugin;
        $sinh_plugin = new Woo_Sinh_Marketplace();
}
add_action( 'plugins_loaded', 'Woo_Sinh_Marketplace_load' );

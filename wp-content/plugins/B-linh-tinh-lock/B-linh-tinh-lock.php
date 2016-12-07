<?php
/**
Plugin Name: Mannual lock bad user
Author: Bao
Description: Chưa xài được
*/
if (!defined('ABSPATH')) exit;
if (!class_exists('B-linh-tinh-lock')) {
	class B_linh_tinh_lock {
		function __construct() {
			wp_get_current_user();
		}
	}
}
function B_linh_tinh_lock_load() {
	global $bao_plugin;
	$bao_plugin = new B_linh_tinh_lock();
	
}
add_action('plugin_loaded', 'B-linh-tinh-lock-load');
?>
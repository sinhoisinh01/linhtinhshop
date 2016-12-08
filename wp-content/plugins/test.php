<?php
	/**
	Plugin Name: test
	Author: Bao
	Description: để test chứ gì
	*/
	function S_test() {
		global $wpdb;
		$u = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
		echo $u;
	}
	add_shortcode('test', 'S_test');
?>
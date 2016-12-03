<?php
/*
Plugin Name: Pay To Post
Plugin URI: www.wordpress-paytopost.com
Description: A plugin to allow administrators to charge users for posting to a site
Version: 1.0.0
Author: PluginCentral
Author URI: https://profiles.wordpress.org/plugincentral/
Text Domain: pay-to-post
License: GPLv2
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

namespace PayToPost;

define("ABS_PATH", dirname(__FILE__).'/');

require_once(ABS_PATH . 'pages/payment-page.php');
require_once(ABS_PATH . 'pages/confirmation-page.php');
require_once(ABS_PATH . 'pages/transactions-page.php');
require_once(ABS_PATH . 'pages/cancellation-page.php');
require_once(ABS_PATH . 'functions/ptp-functions.php');
require_once(ABS_PATH . 'functions/ptp-database.php');
require_once(ABS_PATH . 'functions/ptp-options.php');

//Plugin name
if (!defined('CB_PAY_TO_POST_PLUGIN_NAME'))
    define('CB_PAY_TO_POST_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

// Plugin url 
if (!defined('CB_PAY_TO_POST_PLUGIN_URL'))
    define('CB_PAY_TO_POST_PLUGIN_URL', WP_PLUGIN_URL . '/' . CB_PAY_TO_POST_PLUGIN_NAME);

class CBPayToPost{

	private static $textDomain = 'pay-to-post';
	private static $pageTitle = 'Pay To Post';
	private static $menuTitle = 'Pay To Post';
	
	protected $multiSite = false;

	private static $insufficientFundsMessage = '<div class="%s"><p>You have insufficent funds to create a %s. <a href="%s">Add funds here</a>.</p></div>';
	private static $sufficientFundsMessage = '<div class="%s"><p>You will be charged %s for publishing this %s. Your current balance is %s.</p></div>';

	function __construct(){
		//Setup multisite indicator
		if (function_exists('is_multisite') && is_multisite()){
				$this->multiSite = true;
		}

		register_activation_hook(__FILE__,array($this, 'setupDatabase'));

		//Enqueue the required scripts and styles
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));

		//Setup the admin settings page
		add_action('admin_menu', array($this, 'createSettingsMenu'));

		//Register the settings for this plugin
		add_action('admin_init', array($this, 'registerPluginOptions'));

		//Setup the new post hook
		add_action('admin_notices', array($this, 'applyPayToPostRules'));

		//Setup the publish post hook
		//add_action('publish_post', array($this, 'deductUserPostCost'));
		add_action('transition_post_status', array($this, 'deductUserPostCost'), 10, 3);

		//Setup the shortcode for this functionality
		add_shortcode('pay_to_post', array($this, 'shortcodeHandler'));

		$paymentPage = new PTPPaymentPage();
		$confirmationPage = new PTPConfirmationPage();
		$transactionsPage = new PTPTransactionsPage();
		$cancellationPage = new PTPCancellationPage();
	}

	public function enqueueScripts(){
		wp_enqueue_script('cb_pay_to_post_js', CB_PAY_TO_POST_PLUGIN_URL . '/js/PayToPost.js', array('jquery'));
		wp_enqueue_style('ptp_custom_css', CB_PAY_TO_POST_PLUGIN_URL . '/css/custom.css');
	}

	public function createSettingsMenu(){
		if($this->multiSite){
			add_options_page(__(self::$pageTitle, self::$textDomain), __(self::$menuTitle, self::$textDomain), 'manage_network', 'pay_to_post_menu', array($this, 'outputSettingsPage'));
		}
		else{
			add_options_page(__(self::$pageTitle, self::$textDomain), __(self::$menuTitle, self::$textDomain), 'manage_options', 'pay_to_post_menu', array($this, 'outputSettingsPage'));
		}
	}

	public function outputSettingsPage(){
		if($this->multiSite && !current_user_can('manage_network')){
			wp_die( __('You do not have sufficient permissions to access this page.'));
		}
		elseif(!current_user_can('manage_options')){
			wp_die( __('You do not have sufficient permissions to access this page.'));
		}
			
		require_once(ABS_PATH . 'pages/options-page.php');
	}

	//Setup the settings for this plugin
	public function registerPluginOptions(){
		register_setting( 'pay_to_post_options', 'pay_to_post' );
	}

	//Get the current settings for this plugin
	public function getPluginOptions(){
		$options = get_option('pay_to_post');
		
		if(empty($options) && $this->multiSite){
			$options = get_site_option('pay_to_post');
		}
		if(is_main_site()){
			update_site_option('pay_to_post', $options);
		}
		
		return $options;
	}

	//Get all user roles for add/edit drop down
	private function getUserRoles(){
		global $wp_roles;
		
		$rolesOptions = array();
		
		foreach($wp_roles->roles as $key=>$value){
			if($value['name'] == 'Contributor'){
				$rolesOptions[] = '<option value="'.$value['name'].'" selected>'.$value['name'].'</option>';
			}
			else{
				$rolesOptions[] = '<option value="'.$value['name'].'">'.$value['name'].'</option>';
			}
		}
		
		return implode($rolesOptions);
	}

	private function getPostTypes(){
		$postTypesOptions = array();
		
		$postTypes = get_post_types('', 'objects');
		foreach($postTypes as $key=>$value){
			//$postTypesOptions[] = '<option value="'.$value->name.'">'.ucfirst($value->name).'</option>';
			$postTypesOptions[] = '<option value="'.$value->name.'">'.$value->labels->singular_name.'</option>';
		}
		
		return implode($postTypesOptions);
	}

	public function setupDatabase(){

		PTPDatabase::createTables();
	}

	private function getAllPages($paymentPage){

		$args = array();
		$pages = get_pages($args);

		$options = array();

		foreach($pages as $page){
			$option = '<option value="'.$page->ID.'"';
			if($page->ID == $paymentPage){
				$option .= ' selected ';
			}
			$option .= '>'.$page->post_title.'</option>';
			$options[] = $option;
		}

		return implode($options);

	}

	public function applyPayToPostRules(){
		wp_dequeue_style('cb_disable_publish_css');

		global $pagenow;
		
		if ($pagenow == 'post-new.php'){  //submit for review and publish
			global $typenow;
			
			$actions = array(
				'insufficient_funds' => function($postTypeDisplay, $paymentPagePermalink){
					echo sprintf(self::$insufficientFundsMessage, "error", $postTypeDisplay, $paymentPagePermalink);
					//disable the publish button - load css to disable this
					wp_enqueue_style('ptp_disable_publish_css', CB_PAY_TO_POST_PLUGIN_URL . '/css/disablepublish.css');
				},
				'sufficient_funds' => function($postTypeDisplay, $postCost, $balance){
					echo sprintf(self::$sufficientFundsMessage, "error", $postCost, $postTypeDisplay, $balance);
				}
			);

			$this->processRules($typenow, $actions);
		}
	}

	private function processRules($postType, $actions, $content=NULL){

		$userId = get_current_user_id();
		$postCost = PTPOptions::getPostCost($postType, $userId);
		
		if($postCost > 0){
			$balance = PTPDatabase::getUsersBalance($userId);
			$postTypeDisplay = PTPOptions::getPostTypeDisplay($postType, $userId);
			if($postCost > $balance){
				$paymentPageId = PTPOptions::getPaymentPageId();
				$paymentPagePermalink = get_permalink($paymentPageId);
				call_user_func($actions['insufficient_funds'], $postTypeDisplay, $paymentPagePermalink);
			}
			else{
				call_user_func($actions['sufficient_funds'], $postTypeDisplay, $postCost, $balance, $content);
			}
		}
	}

	//public function deductUserPostCost($ID){
	public function deductUserPostCost($new_status, $old_status, $post){

		if($new_status == 'publish' && $old_status != 'publish'){
			//$postType = get_post_type($ID);
			$postType = $post->post_type;
			$postId = $post->ID;

			$userId = get_current_user_id();
			$postCost = PTPOptions::getPostCost($postType, $userId);
			
			if($postCost > 0){
				PTPDatabase::deductPostCost($userId, $postId, $postCost);
			}
		}
	}

	public function shortcodeHandler($attr, $content=NULL){

		$attributes = shortcode_atts(array(
			'type' => 'post'
			), $attr, 'pay_to_post');

		if (!is_user_logged_in()){
				return apply_filters('pay_to_post_shortcode_not_logged_in', 'You must be logged in');
		}

		$actions = array(
			'insufficient_funds' => function($postTypeDisplay, $paymentPagePermalink){
				echo sprintf(self::$insufficientFundsMessage, "well well-sm pay-error", $postTypeDisplay, $paymentPagePermalink);
				},
			'sufficient_funds' => function($postTypeDisplay, $postCost, $balance, $content){
				echo sprintf(self::$sufficientFundsMessage, "well well-sm pay-warning", $postCost, $postTypeDisplay, $balance);
				echo apply_filters('pay_to_post_shortcode_ok', do_shortcode($content));
				}
			);

		$this->processRules($attributes['type'], $actions, $content);
	}
}

global $cbptp;
$cbptp = new CBPayToPost();
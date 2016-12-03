<?php

namespace PayToPost;

require_once(ABS_PATH . 'functions/ptp-functions.php');

class PTPOptions{

	const SANDBOX_ENDPOINT = "https://api-3t.sandbox.paypal.com/nvp";
	const SANDBOX_URL = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout";
	const PRODUCTION_ENDPOINT = "https://api-3t.paypal.com/nvp";
	const PRODUCTION_URL = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout";

	public static function getCurrency(){

		$paypalCurrency = self::getOption('paypal_currency', 'USD');
		return $paypalCurrency;
	}

	public static function getPaypalEndpoint(){
		if(self::getSandboxMode()){
			return self::SANDBOX_ENDPOINT;
		}
		
		return self::PRODUCTION_ENDPOINT;
	}

	public static function getPaypalUrl(){
		if(self::getSandboxMode()){
			return self::SANDBOX_URL;
		}

		return self::PRODUCTION_URL;
	}

	public static function getConfirmationPageId(){
		$pageId = self::getOption('payment_confirmation_page');
		return $pageId;
	}

	public static function getTransactionsPageId(){
		$pageId = self::getOption('payment_transactions_page');
		return $pageId;
	}

	public static function getPaymentCancellationPageId(){
		$pageId = self::getOption('payment_cancellation_page');
		return $pageId;
	}

	public static function getCredentials(){

		$apiUsername = self::getOption('paypal_api_username');
		$apiUserPassword = self::getOption('paypal_api_password');
		$apiSignature = self::getOption('paypal_api_signature');

		$credentials = "&USER=" . urlencode($apiUsername);
		$credentials .= "&PWD=" . urlencode($apiUserPassword);
		$credentials .= "&SIGNATURE=" . urlencode($apiSignature);

		return $credentials;
	}


	public static function getPostCost($postType, $userId){

		$roleTypeCost = 0.00;
		$rule = self::getRule($postType, $userId);
		if(isset($rule)){
			$roleTypeCost = $rule['cost'];
		}
		return $roleTypeCost;

	}

	public static function getPostTypeDisplay($postType, $userId){
		
		$postTypeDisplay = '';
		$rule = self::getRule($postType, $userId);
		if(isset($rule)){
			$postTypeDisplay = $rule['post_type_display'];
		}
		return $postTypeDisplay;
	}

	private static function getRule($postType, $userId){

		$currentType = strtolower($postType);
		$userRole = strtolower(PTPFunctions::getUserRole($userId));

		$returnRule;

		$rules = self::getOption('rule');
		if(!is_array($rules)){
			return null;
		}

		foreach($rules as $rule){
			if(strtolower($rule['user_role']) == $userRole && strtolower($rule['post_type']) == $currentType){
				$returnRule = $rule;
			}
		}

		return $returnRule;

	}

	public static function getPaymentPageId(){
		$pageId = self::getOption('payment_page');
		return $pageId;
	}

	private static function getOption($option, $default=''){

		$options = get_option('pay_to_post');
		if(isset($options[$option])){
			return $options[$option];
		}

		return $default;
	}

	private static function getSandboxMode(){

		$paypalSandbox = self::getOption('paypal_sandbox', 'off');
		if($paypalSandbox == 'on'){
			return true;
		}
		return false;
	}
}
<?php

namespace PayToPost;

require_once(ABS_PATH . 'functions/ptp-options.php');
require_once(ABS_PATH . 'functions/ptp-functions.php');
require_once(ABS_PATH . 'functions/ptp-database.php');

class PTPConfirmationPage{

	const SHORTCODE_DEFAULT = '<h1>There are no outstanding transactions to confirm</h1>';

	private $shortcodeOutput = self::SHORTCODE_DEFAULT;

	function __construct(){
		add_shortcode('ptp_confirmation_form', array($this, 'shortcodeHandler'));
		add_action( 'init', array($this, 'processConfirmSubmit') );
		add_action( 'init', array($this, 'paypalResponse') );
	}

	public function shortcodeHandler(){

		return $this->shortcodeOutput;
	}

	public function processConfirmSubmit(){

		if(isset($_POST)){
			if(isset($_POST['ptp_confirm_form'])){

				$result = $this->callDoExpressCheckoutPayment();
				$ack = strtoupper($result["ACK"]);
		        if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING"){
		        	PTPDatabase::insertPaypalTransaction($result);
		        	PTPDatabase::insertLocalTransaction($result);
					//redirect to a transactions url - change home for transactions once I've implemented transactions
					wp_redirect(get_permalink(PTPOptions::getTransactionsPageId()));
				}
				else{
					//Display a user friendly Error on the page using any of the following error information returned by PayPal
					$ErrorCode = urldecode($result["L_ERRORCODE0"]);
					$ErrorShortMsg = urldecode($result["L_SHORTMESSAGE0"]);
					$ErrorLongMsg = urldecode($result["L_LONGMESSAGE0"]);
					$ErrorSeverityCode = urldecode($result["L_SEVERITYCODE0"]);
					
					echo "SetExpressCheckout API call failed. ";
					echo "Detailed Error Message: " . $ErrorLongMsg;
					echo "Short Error Message: " . $ErrorShortMsg;
					echo "Error Code: " . $ErrorCode;
					echo "Error Severity Code: " . $ErrorSeverityCode;
				}
				exit;
			}
		}
	}

	private function callDoExpressCheckoutPayment(){

		$params = "&TOKEN=".$_POST['paypal_token'];
		$params .= "&PAYERID=".$_POST['paypal_payer_id'];
		$params .= "&PAYMENTREQUEST_0_PAYMENTACTION=SALE";
		$params .= "&PAYMENTREQUEST_0_AMT=".$_POST['paypal_amt'];
		$params .= "&PAYMENTREQUEST_0_CURRENCYCODE=".$_POST['paypal_currency_code'];

        $result = PTPFunctions::makeAPICall("DoExpressCheckoutPayment", $params);

        return $result;
	}

	

	public function paypalResponse(){
		
		if(isset($_GET['action']) && $_GET['action'] == PTPFunctions::PAYPAL_SUCCESS){
			
			$token = urldecode($_GET['token']);
			//$payerId = urldecode($_GET['PayerID']);
			
			$params = '&TOKEN='.$token;
			
			$result = PTPFunctions::makeAPICall("GetExpressCheckoutDetails", $params);
			
			$this->shortcodeOutput = '<h2>Confirm payment</h2>';
			$this->shortcodeOutput .= '<form action="?" method="post">';
			$this->shortcodeOutput .= '<input type="hidden" name="ptp_confirm_form">';
			$this->shortcodeOutput .= '<input type="hidden" name="paypal_token" value="'.$result['TOKEN'].'">';
			$this->shortcodeOutput .= '<input type="hidden" name="paypal_payer_id" value="'.$result['PAYERID'].'">';
			$this->shortcodeOutput .= '<input type="hidden" name="paypal_amt" value="'.$result['AMT'].'">';
			$this->shortcodeOutput .= '<input type="hidden" name="paypal_currency_code" value="'.$result['CURRENCYCODE'].'">';
			$this->shortcodeOutput .= '<input type="hidden" name="paypal_payer_email" value="'.$result['EMAIL'].'">';

			$this->shortcodeOutput .= '<table class="confirm-table">';
			$this->shortcodeOutput .= '<thead>';
			$this->shortcodeOutput .= '<tr>';
			$this->shortcodeOutput .= '<th>Name</th>';
			$this->shortcodeOutput .= '<th>PayPal email</th>';
			$this->shortcodeOutput .= '<th>Amount</th>';
			$this->shortcodeOutput .= '</tr>';
			$this->shortcodeOutput .= '</thead>';
			$this->shortcodeOutput .= '<tbody>';
			$this->shortcodeOutput .= '<tr>';
			$this->shortcodeOutput .= '<td>'.$result['SHIPTONAME'].'</td>';
			$this->shortcodeOutput .= '<td>'.$result['EMAIL'].'</td>';
			$this->shortcodeOutput .= '<td>'.$result['AMT'].'('. $result['CURRENCYCODE'].')</td>';
			$this->shortcodeOutput .= '</tr>';
			$this->shortcodeOutput .= '</tbody>';
			$this->shortcodeOutput .= '</table>';

			$this->shortcodeOutput .= '<input type="submit" value="Confirm" class="confirm-submit">';

			$this->shortcodeOutput .= '</form>';
		}
	}
}
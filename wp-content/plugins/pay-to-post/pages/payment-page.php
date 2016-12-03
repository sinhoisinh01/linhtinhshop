<?php

namespace PayToPost;

require_once(ABS_PATH . 'functions/ptp-functions.php');
require_once(ABS_PATH . 'functions/ptp-options.php');

class PTPPaymentPage{

	function __construct(){
		add_shortcode('ptp_payment_form', array($this, 'shortcodeHandler'));
		add_action( 'init', array($this, 'processPaymentSubmit') );
	}

	public function shortcodeHandler(){

		$html = "<h1>Add Funds</h1>";

		$html .= '<div id="payment-form-container">';
		$html .= '<form action="?" method="post">';
		$html .= '<input type="hidden" name="ptp_payment_form">';

		$html .= '<p>';
		$html .= '<table id="add_funds_table">';
		$html .= '<tr>';
		$html .= '<th scope="row">Amount</th>';
		$html .= '<td>';
		$html .= '<input type="text" name="amount" pattern="^\d+\.\d{2}$" class="currency-input" required>';
		$html .= ' ('.PTPOptions::getCurrency().')';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>';
		$html .= '</td>';
		$html .= '<td>';
		$html .= '<span class="table_description">Enter the amount you wish to add to your account</span>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td>';
		$html .= '</td>';
		$html .= '<td>';
		$html .= '<input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" border="0" align="top" alt="Check out with PayPal"/>';
		$html .= '</td>';
		$html .= '</tr>';
		$html .= '</table>';
		$html .= '</p>';
		$html .= '</form>';
		$html .= '</div>';

		return $html;
	}

	public function processPaymentSubmit(){
		if(isset($_POST)){
			if(isset($_POST['ptp_payment_form'])){

				$billing_amount = $_POST['amount'];  //sanitize this input
		       
		        $params = "&PAYMENTREQUEST_0_AMT=". $billing_amount;
				$params .= "&PAYMENTREQUEST_0_PAYMENTACTION=" . "SALE";
				$params .= "&RETURNURL=" . urlencode(add_query_arg('action', PTPFunctions::PAYPAL_SUCCESS, get_permalink(PTPOptions::getConfirmationPageId()))); 
				$params .= "&CANCELURL=" . get_permalink(PTPOptions::getPaymentCancellationPageId());
				$params .= "&PAYMENTREQUEST_0_CURRENCYCODE=" . PTPOptions::getCurrency(); 
				
				$_SESSION["currencyCodeType"] = PTPOptions::getCurrency();  //Need to get this from configuration	  
				$_SESSION["PaymentType"] = "SALE";

		        $result = PTPFunctions::makeAPICall("SetExpressCheckout", $params);

		        $ack = strtoupper($result["ACK"]);
		        if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
				{
					$url = add_query_arg(array(
											'token' => urldecode($result['TOKEN']),
											'useraction' => 'commit'),
										PTPOptions::getPaypalUrl());
					
					wp_redirect($url);
				} 
				else  
				{
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
}

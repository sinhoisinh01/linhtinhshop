<?php

namespace PayToPost;

class PTPCancellationPage{

	function __construct(){
		add_shortcode('ptp_cancellation_display', array($this, 'shortcodeHandler'));
	}

	public function shortcodeHandler($attr, $contents=''){

		$html = '<h2>Payment Cancellation</h2>';
		$html .= 'Your payment has been cancelled - you have not been charged anything.';

		return $html;
	}
}
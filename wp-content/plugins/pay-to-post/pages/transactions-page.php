<?php

namespace PayToPost;

require_once(ABS_PATH . 'functions/ptp-database.php');

class PTPTransactionsPage{

	function __construct(){
		add_shortcode('ptp_transaction_display', array($this, 'shortcodeHandler'));
	}

	public function shortcodeHandler($atts, $content='')
	{

		$transactions = PTPDatabase::getUserTransactions(get_current_user_id());

		$html = '<table id="ptp-transactions-table">';

		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th>Date / Time</th>';
		$html .= '<th>Money In</th>';
		$html .= '<th>Money Out</th>';
		$html .= '<th>Post Title</th>';
		$html .= '<th>Balance</th>';
		$html .= '</tr>';
		$html .= '</thead>';

		$html .= '<tbody>';
		foreach($transactions as $transaction){
			$html .= '<tr>';
			$html .= '<td>';
			$html .= $transaction->created_at;
			$html .= '</td>';
			$html .= '<td class="align-right">';
			if($transaction->credit_or_debit == 'CREDIT'){
				$html .= $transaction->amount;
			}
			$html .= '</td>';
			$html .= '<td class="align-right">';
			if($transaction->credit_or_debit == 'DEBIT'){
				$html .= $transaction->amount;
			}
			$html .= '</td>';
			$html .= '<td class="align-right">';
			if($transaction->post_id != NULL){
				$html .= get_the_title($transaction->post_id); 
			}
			$html .= '</td>';
			$html .= '<td class="align-right">';
			$html .= $transaction->balance;
			$html .= '</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';

		$html .= '</table>';

		return $html;
	}
}
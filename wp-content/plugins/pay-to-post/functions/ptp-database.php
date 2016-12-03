<?php

namespace PayToPost;

require_once(ABS_PATH . 'functions/ptp-options.php');

class PTPDatabase{

	const PAYPAL_TRANSACTION_TABLE = "ptp_paypal_transactions";
	const LOCAL_TRANSACTION_TABLE = "ptp_local_transactions";

	public static function createTables(){

		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$tableName = $wpdb->prefix . self::PAYPAL_TRANSACTION_TABLE;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $tableName (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			status varchar(255) NOT NULL DEFAULT 'pending_payment',
			amount decimal(9,2) NOT NULL,
			currency varchar(255) DEFAULT '',
			payer_id varchar(255) NOT NULL,
			payer_email longtext,
			transaction_id longtext NOT NULL,
			created_at timestamp DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)  
			)  $charset_collate;";

        dbDelta($sql);

        //NB note the DOUBLE SPACE between PRIMARY KEY and (id) - this is essential or it wont work properly

        $tableName = $wpdb->prefix . self::LOCAL_TRANSACTION_TABLE;

        $sql = "CREATE TABLE $tableName (
        	id mediumint(9) NOT NULL AUTO_INCREMENT,
        	user_id bigint(20) NOT NULL,
        	credit_or_debit varchar(255) NOT NULL DEFAULT 'DEBIT',
        	amount decimal(9,2) NOT NULL,
        	currency varchar(255) DEFAULT '',
        	balance decimal(9,2) NOT NULL,
        	paypal_transaction_id longtext DEFAULT '',
        	post_id bigint(20),
        	created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        	PRIMARY KEY  (id)
        	) $charset_collate;";

		dbDelta($sql);
	}

	public static function getUserTransactions($userId){

		global $wpdb;

		$tableName = $wpdb->prefix . self::LOCAL_TRANSACTION_TABLE;

		$sql = 'SELECT * FROM ' . $tableName . ' WHERE user_id = ' . $userId . ' ORDER BY created_at DESC';

		$transactions = $wpdb->get_results($sql);
		
		return $transactions;
	}

	public static function getUsersBalance($userId){

		global $wpdb;

		$tableName = $wpdb->prefix . self::LOCAL_TRANSACTION_TABLE;

		$sql = 'SELECT balance FROM '.$tableName.' WHERE user_id = '.$userId.' ORDER BY created_at DESC';

		$balance = $wpdb->get_var($sql);

		if($balance == NULL){
			$balance = 0.00;
		}

		return $balance;
	}

	public static function deductPostCost($userId, $postId, $postCost){

		global $wpdb;

		$usersBalance = self::getUsersBalance($userId);
		$newBalance = $usersBalance - $postCost;

		$tableName = $wpdb->prefix . self::LOCAL_TRANSACTION_TABLE;

		$data = [
			'user_id' => $userId,
        	'credit_or_debit' => 'DEBIT',
        	'amount' => $postCost,
        	'currency' => PTPOptions::getCurrency(),
        	'balance' => $newBalance,
        	'post_id' => $postId
		];

		$wpdb->insert($tableName, $data);
	}

	public static function insertPaypalTransaction($result){

		global $wpdb;

		$tableName = $wpdb->prefix . self::PAYPAL_TRANSACTION_TABLE;

		$data = [
			'user_id' => get_current_user_id(),
			'status' => $result['PAYMENTINFO_0_PAYMENTSTATUS'],
			'amount' => $result['PAYMENTINFO_0_AMT'],
			'currency' => $_POST['paypal_currency_code'],
			'payer_id' => $_POST['paypal_payer_id'],
			'payer_email' => $_POST['paypal_payer_email'],
			'transaction_id' => $result['PAYMENTINFO_0_TRANSACTIONID'],
			'created_at' => current_time('mysql')
			];

		$wpdb->insert($tableName, $data);
	}

	public static function insertLocalTransaction($result){

		global $wpdb;

		$balance = self::getUsersBalance(get_current_user_id());

		$newBalance = $balance + $result['PAYMENTINFO_0_AMT'];

		$tableName = $wpdb->prefix . self::LOCAL_TRANSACTION_TABLE;

		$data = [
			'user_id' => get_current_user_id(),
			'credit_or_debit' => 'CREDIT',
			'amount' => $result['PAYMENTINFO_0_AMT'],
			'currency' => $_POST['paypal_currency_code'],
			'balance' => $newBalance,
			'paypal_transaction_id' => $result['PAYMENTINFO_0_TRANSACTIONID'],
		];

        $wpdb->insert($tableName, $data);
	}
}
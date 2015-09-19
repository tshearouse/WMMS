<?php
require_once('table_names.php');

function db_CreateTransactionTableIfNotExists() {
	global $wpdb;
	$table_name = db_GetTransactionTableName();
	if($wpdb->get_var("show tables like $table_name") != $table_name) {
		$sql = "CREATE TABLE $table_name("
				. " user VARCHAR(60) NOT NULL"
				. " txnId VARCHAR(255) NOT NULL"
				. " date DATE"
				. " paymentType INT"
				. " taggedFor VARCHAR(255)"
				. " id INT NOT NULL AUTO_INCREMENT"
				. " PRIMARY KEY ( id ));";
		require_once(ABSPATH . "wp_admin/includes/upgrade.php");
		dbDelta($sql);
		register_activation_hook( __FILE__, 'db_CreateTransactionTableIfNotExists' );
	}
}

function db_AddTransaction($userId, $transactionId, $date, $paymentType, $taggedFor) {
	db_CreateTransactionTableIfNotExists();
	global $wpdb;
	$table_name = db_GetTransactionTableName();
	$dbUser = strip_tags(addslashes($userId));
	$dbTxnId = strip_tags(addslashes($transactionId));
	$dbTag = strip_tags(addslashes($taggedFor));
	
	$wpdb->insert($table_name, array('user' => $dbUser, 'txnId' => $dbTxnId, 'date' => $date, 'paymentType' => $paymentType, 'taggedFor' => $dbTag));
}

?>
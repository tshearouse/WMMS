<?php
require_once('table_names.php');

function db_CreatePaymentItemsTableIfNotExists() {
	global $wpdb;
	$table_name = db_GetPaymentItemsTableName();
	if($wpdb->get_var("show tables like $table_name") != $table_name) {
		$sql = "CREATE TABLE $table_name("
		. " itemName VARCHAR(255) NOT NULL"
		. " itemPrice DECIMAL(6, 2) NOT NULL"
		. " isFixedPrice BIT"
		. " active BIT"
		. " itemType INT NOT NULL"
		. " id INT NOT NULL AUTO_INCREMENT"
		. " PRIMARY KEY ( id ));";
		require_once(ABSPATH . "wp_admin/includes/upgrade.php");
		dbDelta($sql);
		register_activation_hook( __FILE__, 'db_CreatePaymentItemsTableIfNotExists' );
	}
}

function db_GetActivePaymentItems() {
	db_CreatePaymentItemsTableIfNotExists();
	global $wpdb;
	$table_name = db_GetPaymentItemsTableName();
	$sql = "SELECT * FROM $table_name WHERE active = 1";
	return $wpdb->get_results($sql, ARRAY_A);
}

function db_GetAllPaymentItems() {
	db_CreatePaymentItemsTableIfNotExists();
	global $wpdb;
	$table_name = db_GetPaymentItemsTableName();
	$sql = "SELECT * FROM $table_name";
	return $wpdb->get_results($sql, ARRAY_A);
}

function db_GetPaymentItemById($itemId) {
	db_CreatePaymentItemsTableIfNotExists();
	$dbItemId = intval($itemId);
	global $wpdb;
	$table_name = db_GetPaymentItemsTableName();
	$sql = "SELECT * FROM $table_name WHERE id = $dbItemId";
	return $wpdb->get_results($sql, ARRAY_A);
}

function db_InsertOrUpdatePaymentItem($paymentItem) {
	db_CreatePaymentItemsTableIfNotExists();
	$tableName = db_GetPaymentItemsTableName();
	global $wpdb;
	$dbItemId = intval($paymentItem->DbId);
	$dbItemName = add_tags(stripslashes($paymentItem->ItemName));
	$dbItemPrice = floatval($paymentItem->ItemPrice);
	$dbIsFixed = $paymentItem->IsFixedPrice === true;
	$dbIsActive = $paymentItem->Active === true;
	$dbPaymentType = intval($paymentItem->PaymentType);
	$item = db_GetPaymentItemById($dbItemId);
	if($dbItemId < 0 || $item == null) {
		$wpdb->insert($tableName, array('itemName' => $dbItemName, 'itemPrice' => $dbItemPrice, 'isFixedPrice' => $dbIsFixed, 'active' => $dbIsActive, 'itemType' => $dbPaymentType));
	} else {
		$wpdb->update($tableName, 
				array('itemName' => $dbItemName, 'itemPrice' => $dbItemPrice, 'isFixedPrice' => $dbIsFixed, 'active' => $dbIsActive, 'itemType' => $dbPaymentType),
				array('id' => $dbItemId));
	}
}
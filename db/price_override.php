<?php
require_once('table_names.php');

function db_CreatePriceOverrideTableIfNotExists() {
	global $wpdb;
	$table_name = db_GetPriceOverrideTableName();
	if($wpdb->get_var("show tables like $table_name") != $table_name) {
		$sql = "CREATE TABLE $table_name("
		. " itemId INT NOT NULL"
		. " userId VARCHAR(60) NOT NULL"
		. " itemPrice DECIMAL(6, 2) NOT NULL"
		. " goodThrough DATE"
		. " PRIMARY KEY ( itemId, userId ));";
		require_once(ABSPATH . "wp_admin/includes/upgrade.php");
		dbDelta($sql);
		register_activation_hook( __FILE__, 'db_CreatePriceOverrideTableIfNotExists' );
	}
}

function db_GetAllPriceOverridesForUser($userId) {
	db_CreatePriceOverrideTableIfNotExists();
	global $wpdb;
	$table_name = db_GetPriceOverrideTableName();
	
	$dbUser = strip_tags(addslashes($userId));
	$sql = "SELECT * FROM $table_name WHERE userId = '$dbUser' AND goodThrough >= CURDATE()";
	return $wpdb->get_results($sql, ARRAY_A);
}

function db_GetPriceOverrideForUser($userId, $itemId) {
	db_CreatePriceOverrideTableIfNotExists();
	global $wpdb;
	$table_name = db_GetPriceOverrideTableName();
	
	$dbUser = strip_tags(addslashes($userId));
	$dbItemId = intval($itemId);
	$sql = "SELECT * FROM $table_name WHERE userId = '$dbUser' AND goodThrough >= CURDATE() AND itemId = $dbItemId";
	return $wpdb->get_row($sql);
}

function db_InsertOrUpdatePriceOverride($priceOverride) {
	db_CreatePriceOverrideTableIfNotExists();
	global $wpdb;
	$table_name - db_GetPriceOverrideTableName();
	$dbUser = strip_tags(addslashes($priceOverride->UserId));
	$dbItemId = intval($priceOverride->ItemId);
	$dbItemPrice = floatval($priceOverride->ItemPrice);
	$dbGoodThrough = strip_tags(addslashes($priceOverride->GoodThrough));
	
	$sql = "SELECT * FROM $table_name WHERE userId = '$dbUser' AND itemId = $dbItemId";
	$result = $wpdb->get_row($sql);
	if($result != null) {
		$wpdb->update($table_name,
				array('itemPrice' => $dbItemPrice, 'goodThrough' => $dbGoodThrough),
				array('userId' => $dbUser, 'itemId' => $dbItemId));
	} else {
		$wpdb->insert($table_name, array('itemPrice' => $dbItemPrice, 'goodThrough' => $dbGoodThrough, 'userId' => $dbUser, 'itemId' => $dbItemId));
	}
}
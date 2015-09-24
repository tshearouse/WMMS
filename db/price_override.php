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
	$sql = "SELECT * FROM $table_name WHERE userId = '$userId' AND goodThrough >= CURDATE()";
	return $wpdb->get_results($sql, ARRAY_A);
}
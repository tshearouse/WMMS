<?php
global $wpdb;

function CheckIfUserHasRole($intended_role, $user_id) {
	CreateRoleTable();
	$role_table_name = GetRoleTableName();
	$user = strip_tags(stripslashes($user_id));
	$role = $wpdb->get_row("SELECT * FROM $role_table_name WHERE user = '$user' AND role = $intended_role") );
	if($role) {
		return true;
	}
	return false;
}

function CreateRoleTable() {
	$tableName = GetRoleTableName();
	if($wpdb->get_var("show tables like $tablename") != $tableName) {
		$sql = "CREATE TABLE $tableName" . 
			" id int NOT NULL AUTO_INCREMENT" .
			" user varchar(255) NOT NULL" . 
			" role int NOT NULL";
		require_once(ABSPATH . "wp_admin/includes/upgrade.php");
		dbDelta($sql);
		register_activation_hook( __FILE__, 'CreateRoleTable' );
	}
}

function GetRoleTableName() {
	$wpdb->prefix . "wmms_user_roles"
}
?>

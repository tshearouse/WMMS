<?php
global $wpdb;

function db_CheckIfUserHasRole($user_id, $intended_role) {
	db_CreateRoleTable();
	$role_table_name = db_GetRoleTableName();
	$user = strip_tags(stripslashes($user_id));
	$role = $wpdb->get_row("SELECT * FROM $role_table_name WHERE user = '$user' AND role = $intended_role") );
	if($role) {
		return true;
	}
	return false;
}

function db_AddRoleToUser($user_id, $new_role) {
	db_CreateRoleTable();
	$role_table_name = db_GetRoleTableName();
	$user = strip_tags(stripslashes($user_id));
	$role = intval($new_role);

	$wpdb->insert($role_table_name, array('user' => $user, 'role' => $role));
}

function db_RemoveRoleFromUser($user_id, $role_id) {
	db_CreateRoleTable();
	$role_table_name = db_GetRoleTableName();
	$user = strip_tags(stripslashes($user_id));
	$role = intval($role_id);
	
	$wpdb->delete($role_table_name, array('user' => $user, 'role' => $role));
}

function db_CreateRoleTable() {
	$tableName = db_GetRoleTableName();
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

function db_GetRoleTableName() {
	$wpdb->prefix . "wmms_user_roles"
}
?>
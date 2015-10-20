<?php

require_once('table_names.php');

function db_CheckIfUserHasRole($user_id, $intended_role) {
	global $wpdb;
	db_CreateRoleTable();
	$role_table_name = db_GetRoleTableName();
	$user = strip_tags(addslashes($user_id));
	$role = $wpdb->get_row("SELECT * FROM $role_table_name WHERE user = '$user' AND role = $intended_role");
	if($role) {
		return true;
	}
	return false;
}

function db_GetAllRolesForUser($user_id) {
	global $wpdb;
	db_CreateRoleTable();
	$role_table_name = db_GetRoleTableName();
	$dbUser = strip_tags(addslashes($user_id));
	$sql = "SELECT role FROM $role_table_name WHERE user = '$dbUser'";
	return $wpdb->get_results($sql, ARRAY_A);
}

function db_AddRoleToUser($user_id, $new_role) {
	global $wpdb;
    if(!db_CheckIfUserHasRole($user_id, $new_role)) {
        $role_table_name = db_GetRoleTableName();
        $user = strip_tags(addslashes($user_id));
        $role = intval($new_role);
    
        $wpdb->insert($role_table_name, array('user' => $user, 'role' => $role));
	}
}

function db_RemoveRoleFromUser($user_id, $role_id) {
	global $wpdb;
    if(db_CheckIfUserHasRole($user_id, $role_id)) {
        $role_table_name = db_GetRoleTableName();
        $user = strip_tags(addslashes($user_id));
        $role = intval($role_id);
    
        $wpdb->delete($role_table_name, array('user' => $user, 'role' => $role));
    }
}

function db_CreateRoleTable() {
	global $wpdb;
	$tableName = db_GetRoleTableName();
	if($wpdb->get_var("show tables like $tableName") != $tableName) {
		$sql = "CREATE TABLE $tableName(" . 
			" user VARCHAR(60) NOT NULL," . 
			" role INT NOT NULL," . 
			" PRIMARY KEY ( user, role ));";
		require_once(ABSPATH . "wp_admin/includes/upgrade.php");
		dbDelta($sql);
		register_activation_hook( __FILE__, 'db_CreateRoleTable' );
	}
}

?>
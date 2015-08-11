<?php
global $wpdb;

function CheckIfUserHasRole($intended_role, $user_id) {
	$role_table_name = $wpdb->prefix . "wmms_user_roles";
	$user = strip_tags(stripslashes($user_id));
	$role = $wpdb->get_row("SELECT * FROM $role_table_name WHERE id = '$user' AND role = $intended_role") );
	if($role) {
		return true;
	}
	return false;
}
?>

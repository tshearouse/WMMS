<?php
abstract class UserRoles {

	const None = 0;
	const Member = 1;
	const Board = 2;
	const Admin = 3;

}

function CheckIfUserHasRole($userId, $roleName) {
	require_once('../db/role.php');
	return db_CheckIfUserHasRole($userId, $roleName);
}

function AddRoleToUser($userId, $roleName) {
	AdminRightsOrDie();

	require_once('../db/role.php');
	db_AddRoleToUser($userId, $roleName);
}

function RemoveRoleFromUser($userId, $roleName) {
	AdminRightsOrDie();

	require_once('../db/role.php');
	db_RemoveRoleFromUser($userId, $roleName);
}

function AdminRightsOrDie() {
	if(!IsCurrentUserAdmin()) {
		ReturnWithError();
	}
}

function AdminOrBoardRightsOrDie() {
    if(!IsCurrentUserAdmin() && !IsCurrentUserBoard()) {
        ReturnWithError();
    }
}

function CheckIfUserIdMatches($existing_user_id) {
    $current_user = wp_get_current_user_id();
    if($current_user == $existing_user_id) {
        return true;
    }
    return false;
}

function IsCurrentUserAdmin() {
	$current_user = wp_get_current_user_id();

	return CheckIfUserHasRole($current_user, UserRoles::Admin);
}

function IsCurrentUserBoard() {
    $current_user = wp_get_current_user_id();

    return CheckIfUserHasRole($current_user, UserRoles::Admin);
}

function CheckIfUserIsLoggedIn() {
	$current_user = wp_get_current_user_id();
	if($current_user == 0) {
		ReturnWithError();
	}
}

function ReturnWithError() {
	header('HTTP/1.0 403 Forbidden');
	die('Either you are not logged in, or your account does not have access to do whatever you just tried to do.');
}

?>
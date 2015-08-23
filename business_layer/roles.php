<?php
abstract class UserRoles {

	const None = 0;
	const Member = 1;
	const Board = 2;
	const Admin = 3;
	
	private static $enumValues = NULL;

	public static function prettyPrint($roleId) {
		$constants = self::getConstants();
		foreach($constants as $roleName => $roleValue) {
			if($roleId == $roleValue) {
				return $roleName;
			}
		}
		return "None";
	}
	
	public static function parseFromString($role) {
		$constants = self::getConstants();
		foreach($constants as $roleName => $roleValue) {
			if($role == $roleName) {
				return $roleValue;
			}
		}
		return $this::None;
	}
	
	public static function listAllPrettyPrintRoles() {
		$constants = self::getConstants();
		return array_keys($constants);
	}
	
	private static function getConstants() {
		if(self::$enumValues != null) {
			return $enumValues;
		}
		$reflectedClass = new ReflectionClass(get_called_class());
		$enumValues = $reflectedClass->getConstants();
		return $enumValues;
	}

}

function CheckIfUserHasRole($userId, $roleName) {
	require_once('../db/role.php');
	if(!CheckIfUserIdMatches($userId)) {
		AdminOrBoardRightsOrDie();
	}
	return db_CheckIfUserHasRole($userId, $roleName);
}

function GetRolesForUser($userId) {
	require_once('../db/role.php');
	if(!CheckIfUserIdMatches($userId)) {
		AdminOrBoardRightsOrDie();
	}	
	return db_GetAllRolesForUser($userId);
}

function AddRoleToUser($userId, $roleName) {
	AdminOrBoardRightsOrDie();

	require_once('../db/role.php');
	db_AddRoleToUser($userId, $roleName);
}

function RemoveRoleFromUser($userId, $roleName) {
	AdminOrBoardRightsOrDie();

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
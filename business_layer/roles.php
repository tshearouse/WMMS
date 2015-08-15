<?php
abstract class UserRoles {

	const None = 0;
	const Member = 1;
	const Board = 2;
	const Admin = 3;

	private static $enumValues = NULL;

	public static function parse($roleId) {
		$constants = self::getConstants();
		foreach($constants as $roleName => $roleValue) {
			if($roleId == $roleValue) {
				return $roleName;
			}
		}
		return self::None;
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
		require_once('../util/auth.php');
		ReturnWithError();
	}
}

function IsCurrentUserAdmin() {
	$current_user = wp_get_current_user_id();

	return CheckIfUserHasRole($current_user, UserRoles::Admin);
}
?>

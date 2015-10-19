<?php
abstract class PaymentTypes {
	const Unknown = 0;
	const MembershipYearly = 1;
	const MembershipMonthly = 2;
	const Donation = 3;
	
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
		return $this::Unknown;
	}
	
	public static function listAllPrettyPrintRoles() {
		$constants = self::getConstants();
		return array_keys($constants);
	}
	
	public static function listAllRoleIds() {
		$constants = self::getConstants();
		return array_values($constants);
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
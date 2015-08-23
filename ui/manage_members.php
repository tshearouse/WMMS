<?php
require_once '../business_layer/roles.php';
require_once '../business_layer/members.php';
AdminOrBoardRightsOrDie();

if(isset($_POST["edit_user"])) {
	$userId = strip_tags(stripslashes($_POST["wmms_user"]));
	$rfidId = strip_tags(stripslashes($_POST["wmms_user_rfid"]));
	$paidThrough = strip_tags(stripslashes($_POST["wmms_user_paid_through"]));
	
	$wmmsUser = new WmmsMember($userId, $paidThrough, $rfidId);
	$wmmsUser->saveToDb();
	
	$allRoles = UserRoles::listAllPrettyPrintRoles();
	$selectedRoles = $_POST["wmms_user_roles"];
	$cleanSelectedRoles = arrray();
	foreach ($selectedRoles as $selectedRole) {
		$cleanSelectedRoles[] = strip_tags(stripslashes($selectedRole));
	}
	foreach($allRoles as $availableRole) {
		if(in_array($availableRole, $cleanSelectedRoles)) {
			$wmmsUser->addTextRole($roleName);
		} else {
			$wmmsUser->removeTextRole($roleName);
		}
	}
}

?>
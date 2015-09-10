<?php
require_once '../business_layer/roles.php';
require_once '../business_layer/members.php';
AdminOrBoardRightsOrDie();

if(isset($_POST["edit_user"])) {
	processUserInfoEditForm();
}

displayUserData();


function processUserInfoEditForm() {
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
	echo "User $userId updated.";
}

function displayUserData() {
	$allUsers = getAllUsers();
	echo "<table><tr><td>&nbsp;</td><td><b>Name</b></td><td>Paid Through</td><td>RFID Key</td></tr>";
	foreach($allUsers as $user) {
		echo "<tr>";
		$userId = $user->UserId;
		echo "<td><a href='edit_member_info.php?wmms_user=$userId'>Edit</a></td>";
		$wpInfo = $user->getWordpressUserData();
		$prettyPrintName = $wpInfo->last_name . ", " . $wpInfo->first_name;
		echo "<td>$prettyPrintName</td>";
		$paidThrough = $user->PaidThroughDate;
		echo "<td>$paidThrough</td>";
		$rfidKey = $user->RfidTag;
		echo "<td>$rfidKey</td>";
		echo "</tr>";
	}
	echo "</table>";
}
?>
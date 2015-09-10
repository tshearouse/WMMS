<?php
require_once '../business_layer/roles.php';
require_once '../business_layer/members.php';
AdminOrBoardRightsOrDie();

$userId = strip_tags(stripslashes($_GET["wmms_user"]));
$wmmsUser = new WmmsMember($userId);
$userInfo = $wmmsUser->getWordpressUserData();
echo "<h3>" . $userInfo->first_name . " " . $userInfo->last_name . "</h3><p>&nbsp;</p><br />";
echo "<p><b>Email:</b> " . $userInfo->user_email . "</p>";
echo "<form method='POST' target='manage_members.php'>";
echo "<input type='hidden' name='wmms_user' value='$userId' />";
echo "<input type='hidden' name='edit_user' value='true' />";
echo "<p>RFID tag number: <br /><input type='text' name='wmms_user_rfid' value='" . $wmmsUser->RfidTag . "' /></p>";
echo "<p>Paid through: <br /><input type='text' name='wmms_user_paid_through' value='" . $wmmsUser->PaidThroughDate . "' /></p>";
echo "<p><b>Roles:</b></p>";
$activeRoles = $wmmsUser->getRolesAsPrettyPrint();
$allRoles = UserRoles::listAllPrettyPrintRoles();
foreach($allRoles as $roleName) {
	$checked = "";
	if(in_array($roleName, $activeRoles)) {
		$checked = "checked='true' ";
	}
	echo "<p>$roleName: <input type='checkbox' name='wmms_user_roles[]' value='$roleName' $checked/> ";
}
echo "<p><input type='submit' value='Save Changes' /></p></form>";
?>

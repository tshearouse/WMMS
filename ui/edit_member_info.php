<?php
require_once '../business_layer/roles.php';
require_once '../business_layer/members.php';
AdminOrBoardRightsOrDie();

$userId = strip_tags(stripslashes($_POST["wmms_user"]));
$userInfo = get_userdata($userId);
if(!$userInfo) {
	die("User not found.");
} 
$wmmsUser = new WmmsMember($userId);
echo "<h3>" . $userInfo->first_name . " " . $userInfo->last_name . "</h3><p>&nbsp;</p><br />";
echo "<p><b>Email:</b> " . $userInfo->user_email . "</p>";
echo "<form method='POST' target='manage_members.php'>";
echo "<p>RFID tag number: <br /><input type='text' name='wmms_user_rfid' value='" . $wmmsUser->RfidTag . "' /></p>";
echo "<p>Paid through: <br /><input type='text' name='wmms_user_paid_through' value='" . $wmmsUser->PaidThroughDate . "' /></p>";
echo "<p><input type='submit' value='Save Changes' /></p></form>";
?>

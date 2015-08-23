<?php
require_once '../business_layer/roles.php';
require_once '../business_layer/members.php';
AdminOrBoardRightsOrDie();

if(isset($_POST["edit_user"])) {
	$userId = strip_tags(stripslashes($_POST["wmms_user"]));
	$rfidId = strip_tags(stripslashes($_POST["wmms_user_rfid"]));
	$paidThrough = strip_tags(stripslashes($_POST["wmms_user_paid_through"]));
	
	//TODO: Support adding or removing roles.
	$wmmsUser = new WmmsMember($userId, $paidThrough, $rfidId);
	$wmmsUser->saveToDb();
}

?>
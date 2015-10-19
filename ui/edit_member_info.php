<?php
require_once '../business_layer/roles.php';
require_once 'view_member_info.php';
AdminOrBoardRightsOrDie();

$formTarget = "manage_members.php";
$enableEdit = TRUE;

displayInfoForUser($formTarget, $enableEdit);

?>

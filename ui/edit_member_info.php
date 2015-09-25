<?php
require_once 'view_member_info.php';
AdminOrBoardRightsOrDie();

$formTarget = "manage_members.php";
$enableEdit = TRUE;

displayInfoForUser($formTarget, $enableEdit);
//TODO: Need a UI to add/edit price overrides
?>

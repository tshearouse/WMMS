<?php
function CheckIfUserIsLoggedIn() {
	$current_user = wp_get_current_user_id();
	if($current_user == 0) {
		ReturnWithError();
	}
}

function CheckIfUserIdMatches($existing_user_id) {
	$current_user = wp_get_current_user_id();
	if($current_user == $existing_user_id) {
		return true;
	}
	ReturnWithError();
	return false;
}

function ReturnWithError() {
		header('HTTP/1.0 403 Forbidden');
		die('Either you are not logged in, or your account does not have access to do whatever you just tried to do.');
		exit();
}
?>

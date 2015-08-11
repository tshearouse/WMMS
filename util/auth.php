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
		header('HTTP/1.0 404 Not Found');
		echo "<h1>Authentication required</h1>";
		echo "This page requires that you have logged in. Sorry. Kinda.";
		exit();
}
?>

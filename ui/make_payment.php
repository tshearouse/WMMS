<?php
require_once 'view_member_info.php';

$formTarget = "todotodo.todo"; //TODO: Build form handler? At least need something to switch between payment providers.
$enableEdit = FALSE;

displayInfoForUser($formTarget, $enableEdit);

require_once('../business_layer/payment.php');
$paymentItems = GetAllPaymentItems();
foreach($paymentItems as $paymentItem) {
	//TODO: Display radio selection
}

<?php
require_once 'view_member_info.php';

$formTarget = "todotodo.todo"; //TODO: Build form handler? At least need something to switch between payment providers.
$enableEdit = FALSE;

displayInfoForUser($formTarget, $enableEdit);

require_once('../business_layer/payment.php');
$paymentItems = GetActivePaymentItems();
foreach($paymentItems as $paymentItem) {
// 	var $ItemName;
// 	var $ItemPrice;
// 	var $IsFixedPrice; //If false, then $ItemPrice is merely a suggested price in the UI
// 	var $PaymentType;
// 	var $DbId;
	
}

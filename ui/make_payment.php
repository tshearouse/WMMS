<?php
require_once('../business_layer/payment.php');
require_once('../business_layer/payment_items.php');

if(isset($_GET['wmms_item'])) {
	$itemId = intval($_GET['wmms_item']);
	showPaymentSelectionScreen($itemId);
} else {
	showActivePaymentItems();
}

function showActivePaymentItems() {
	$paymentItems = GetActivePaymentItems();
	foreach($paymentItems as $paymentItem) {
		showPaymentItem($paymentItem);
	}
}

function showPaymentItem($paymentItem) {
	showItemInfo($paymentItem);
	echo "<a href='make_payment.php?wmms_item=" . $paymentItem->DbId . "'>Buy this</a></p>";
}

function showItemInfo($paymentItem) {
	echo "<hr />";
	echo "<p><b>" . $paymentItem->ItemName;
	if($paymentItem->IsFixedPrice !== true) {
		echo " - Suggested Price";
	}
	echo ": $" . prettyPrintPrice($paymentItem->ItemPrice) . "</b>";
	echo "<br />" . $paymentItem->Description . "<br />";
}

function showPaymentSelectionScreen($itemId) {
	$paymentItem = new WmmsPaymentItem($itemId);
	showItemInfo($paymentItem);
	
	//TODO: Add payment buttons for Paypal, Dwolla, etc.
	//Can we make that modular to minimize change when we add new payment methods?
}
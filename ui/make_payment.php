<?php
require_once('../business_layer/payment.php');
require_once('../business_layer/payment_items.php');

if(isset($_GET['wmms_item'])) {
	$itemId = intval($_GET['wmms_item']);
	$paymentItem = new WmmsPaymentItem($itemId);
	
	$itemPrice = $paymentItem->ItemPrice;
	if(isset($_GET['wmms_item_price'])) {
		$itemPrice = sanitizePrice($_GET['wmms_item_price']);
	}
	showPaymentSelectionScreen($itemId, $itemPrice);
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
	echo "<form target='make_payment.php' action='GET' >";
	showItemInfo($paymentItem, $paymentItem->ItemPrice, true);

	echo "<input type='hidden' name='wmms_item' value='" . $paymentItem->DbId . "' />";
	echo "<input type='submit' name='submit'>Buy this</input></form></p>";
}

function showItemInfo($paymentItem, $price, $allowEditAmount) {
	echo "<hr />";
	echo "<p><b>" . $paymentItem->ItemName;
	if($paymentItem->IsFixedPrice !== true && $allowEditAmount) {
		echo " - Suggested Price";
	}
	echo ": $";
	$prettyPrice = prettyPrintPrice($price);
	if($paymentItem->IsFixedPrice !== true && $allowEditAmount) {
		echo "<input type='textbox' name='wmms_item_price'>$prettyPrice</input>";
	} else {
		echo $prettyPrice;
	}
	echo "</b><br />" . $paymentItem->Description . "<br />";
}

function showPaymentSelectionScreen($paymentItem, $itemPrice) {
	showItemInfo($paymentItem, $itemPrice, false);
	require_once 'payment_paypal.php';
	printPaypalButton($paymentItem, $itemPrice);
	//TODO: Add payment buttons for Paypal, Dwolla, etc.
	//Can we make that modular to minimize change when we add new payment methods?
}
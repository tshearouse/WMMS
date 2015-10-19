<?php
require_once("../business_layer/payment.php");
require_once("../business_layer/payment_types.php");
require_once("../business_layer/roles.php");
AdminOrBoardRightsOrDie();

if(isset($_POST["edit_item"])) {
	processPaymentItemFromEditForm();
}
printAllPaymentItems();

function printAllPaymentItems() {
	echo ("<table><tr><td>&nbsp;</td><td><b>Active</b></td><td><b>Fixed Price?</b></td><td><b>Price</b></td><td>Description</td><td><b>Item Type<b></td></tr>");
	$newPaymentItem = new WmmsPaymentItem("New", 0, true, PaymentTypes::Unknown, -1, false);
	printPaymentItem($newPaymentItem);
	
	$allPaymentItems = GetAllPaymentItems();
	$inactivePaymentItems = array();
	foreach($allPaymentItems as $paymentItem) {
		//Let's list the active ones first, then the inactive ones.
		if(!($paymentItem->Active)) {
			$inactivePaymentItems = $paymentItem;
		} else {
			printPaymentItem($paymentItem);
		}
	}
	foreach($inactivePaymentItems as $paymentItem) {
		printPaymentItem($paymentItem);
	}
	echo ("</table>");
}

function printPaymentItem($paymentItem) {
	echo ("<tr>");
	$itemId = $paymentItem->$DbId;
	echo ("<td><a href='edit_payment_item.php?wmms_item=$itemId'>Edit</a></td>");
	$active = $paymentItem->$Active == 1;
	echo ("<td>$active</td>");
	$fixed = $paymentItem->$IsFixedPrice == 1;
	echo ("<td>$fixed</td>");
	$price = prettyPrintPrice($paymentItem->$ItemPrice);
	echo ("<td>$" . $price . "</td>");
	$description = $paymentItem->$ItemName;
	echo ("<td>$description</td>");
	$type = PaymentTypes::prettyPrint($paymentItem->$ItemPrice);
	echo ("<td>$type</td>");
	echo ("</tr>");
}
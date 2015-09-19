<?php

class WmmsPayment {
	
	var $UserId;
	var $TransactionId;
	var $Date;
	var $PaymentType;
	var $TaggedFor;
	
	//__construct($userId, $transactionId, $date, $paymentType, $taggedFor);
	function __construct() {
		$args = func_get_args();
		$this->UserId = $args[0];
		$this->TransactionId = $args[1];
		$this->Date = $args[2];
		$this->PaymentType = $args[3];
		$this->TaggedFor = $args[4];
	}
	
	function saveToDb() {
		//Is this a good time to update the member's paid_through value?
		require_once('../db/transaction.php');
		db_AddTransaction($this->UserId, $this->TransactionId, $this->Date, $this->PaymentType, $this->TaggedFor);
	}
}

class WmmsPaymentItem {
	
	var $ItemName;
	var $ItemPrice;
	var $IsFixedPrice; //If true, then $ItemPrice is merely a suggested price in the UI
	var $PaymentType;
	
	//__construct($itemName, $itemPrice, $isFixedPrice, $paymentType);
	function __construct() {
		$args = func_get_arg();
		$this->ItemName = $args[0];
		$this->ItemPrice = $args[1];
		$this->IsFixedPrice = $args[2] === 1;
		$this->PaymentType = PaymentTypes::prettyPrint($args[3]);
	}
}

function GetAllPaymentItems() {
	require_once('../db/payment_items.php');
	$dbItems = db_GetAllPaymentItems();
	$paymentItems = array();
	foreach($dbItems as $paymentItem) {
		$paymentItems = new WmmsPaymentItem($paymentItem['itemName'], $paymentItem['itemPrice'], $paymentItem['isFixedPrice'], $paymentItem['itemType']);
	}
	return $paymentItems;
}
<?php

class WmmsPayment {
	
	var $UserId;
	var $TransactionId;
	var $Date;
	var $PaymentItemId;
	var $TaggedFor;
	
	//__construct($userId, $transactionId, $date, $paymentType, $taggedFor);
	function __construct() {
		$args = func_get_args();
		$this->UserId = $args[0];
		$this->TransactionId = $args[1];
		$this->Date = $args[2];
		$this->PaymentItemId = $args[3];
		$this->TaggedFor = $args[4];
	}
	
	function saveToDb() {
		require_once('../db/payment_items.php');
		require_once('payment_types.php');
		$paymentItem = db_GetPaymentItemById($this->PaymentItemId);
		UpdatePaidThroughDateForUser($this->UserId, $paymentItem['itemType']);
		
		require_once('../db/transaction.php');
		db_AddTransaction($this->UserId, $this->TransactionId, $this->Date, $this->PaymentItemId, $this->TaggedFor);
	}
	
	function UpdatePaidThroughDateForUser($userId, $itemType) {
		if($itemId != PaymentTypes::MembershipMonthly && $itemId != PaymentTypes::MembershipYearly) {
			return;
		}
		$memberData = new WmmsMember($userId);
		$date = date("Y-m-d");
		
		if($date > $memberData->PaidThroughDate) { //If membership had lapsed or not started, begin from today.
			$memberData->PaidThroughDate = $date;
		}
		if($itemType == PaymentTypes::MembershipMonthly) {
			$date = date_add($memberData->PaidThroughDate, date_interval_create_from_date_string("1 month"));
		}
		if($itemType == PaymentTypes::MembershipYearly) {
			$date = date_add($memberData->PaidThroughDate, date_interval_create_from_date_string("1 year"));
		}
		$memberData->PaidThroughDate = $date;
		$memberData->saveToDb();
	}
}

class WmmsPaymentItem {
	
	var $ItemName;
	var $ItemPrice;
	var $IsFixedPrice; //If false, then $ItemPrice is merely a suggested price in the UI
	var $PaymentType;
	var $DbId;
	var $Active;
	
	//__construct($dbId);
	//__construct($itemName, $itemPrice, $isFixedPrice, $paymentType, $itemId, $active);
	function __construct() {
		$numberOfArgs = func_num_args();
		$args = func_get_arg();
		if($numberOfArgs > 1) {
			$this->ItemName = $args[0];
			$this->ItemPrice = $args[1];
			$this->IsFixedPrice = $args[2] === 1;
			$this->PaymentType = PaymentTypes::prettyPrint($args[3]);
			$this->DbId = $args[4];
			$this->Active = $args[5];
		} else {
			$this->DbId = $args[0];
			populateFromDatabase();
		}
	}
	
	private function populateFromDatabase() {
		$dbItem = db_GetPaymentItemById($this->DbId);
		$this->ItemName = $dbItem['itemName'];
		$this->ItemPrice = $dbItem['itemPrice'];
		$this->IsFixedPrice = $dbItem['isFixedPrice'];
		$this->PaymentType = $dbItem['itemType'];
		$this->Active = $dbItem['active'];
	}
	
	function saveToDb() {
		db_InsertOrUpdatePaymentItem($this);
	}
}

class WmmsPriceOverride {
	var $ItemId;
	var $ItemPrice;
	var $GoodThrough;
	var $ItemName;
	
	//__construct($ItemId, $ItemPrice, $GoodThrough);
	function __construct() {
		$args = func_get_arg();
		$this->ItemId = $args[0];
		$this->ItemPrice = $args[1];
		$this->GoodThrough = $args[2];
	}
}

function GetActivePaymentItems() {
	require_once('../db/payment_items.php');
	$overrides = GetPriceOverrides();
	
	$dbItems = db_GetActivePaymentItems();
	$paymentItems = array();
	foreach($dbItems as $paymentItem) {
		$currentItem = new WmmsPaymentItem($paymentItem['itemName'], $paymentItem['itemPrice'], $paymentItem['isFixedPrice'], $paymentItem['itemType'], $paymentItem['id'], true);
		foreach($overrides as $priceOverride) {
			if($priceOverride->ItemId == $currentItem->DbId) {
				$currentItem->ItemPrice = $priceOverride->ItemPrice;
			}
		}
		$paymentItems = $currentItem;
	}
	return $paymentItems;
}

function GetAllPaymentItems() { 
	//All means all, including non-active items. Also, no payment overrides are applied.
	require_once('../db/payment_items.php');
	
	$dbItems = db_GetAllPaymentItems();
	$paymentItems = array();
	foreach($dbItems as $paymentItem) {
		$currentItem = new WmmsPaymentItem($paymentItem['itemName'], $paymentItem['itemPrice'], $paymentItem['isFixedPrice'], $paymentItem['itemType'], $paymentItem['id'], $paymentItem['active']);
		
		$paymentItems = $currentItem;
	}
	return $paymentItems;	
}

function GetPriceOverrides() {
	require_once('../db/price_override.php');
	$currentUserId = wp_get_current_user_id();
	$allPaymentItems = db_GetActivePaymentItems();
	
	$dbOverrides = db_GetAllPriceOverridesForUser($currentUserId);
	$overrides = array();
	foreach($dbOverrides as $override) {
		$priceOverride = new WmmsPriceOverride($override['itemId'], $override['itemPrice'], $override['goodThrough']);
		foreach($allPaymentItems as $paymentItem) {
			if($paymentItem['DbId'] == $priceOverride->ItemId) {
				$priceOverride->ItemName = $paymentItem['itemName'];
			}
		}
		$overrides = $priceOverride;
	}
	return $overrides;
}
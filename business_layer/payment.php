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
		require_once('payment_items.php');
		require_once('payment_types.php');
		$paymentItem = db_GetPaymentItemById($this->PaymentItemId);
		UpdatePaidThroughDateForUser($this->UserId, $paymentItem['itemType']);
		
		require_once('../db/transaction.php');
		db_AddTransaction($this->UserId, $this->TransactionId, $this->Date, $this->PaymentItemId, $this->TaggedFor);
	}
	
	private function UpdatePaidThroughDateForUser($userId, $itemType) {
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


class WmmsPriceOverride {
	var $ItemId;
	var $ItemPrice;
	var $GoodThrough;
	var $ItemName;
	var $UserId;
	
	//__construct($ItemId, $ItemPrice, $GoodThrough, $UserId);
	function __construct() {
		$args = func_get_arg();
		$this->ItemId = $args[0];
		$this->ItemPrice = $args[1];
		$this->GoodThrough = $args[2];
		$this->UserId = $args[3];
	}
	
	function saveToDb() {
		require_once 'roles.php';
		AdminOrBoardRightsOrDie();

		require_once '../db/price_override.php';
		db_InsertOrUpdatePriceOverride($this);
	}
}

function sanitizePrice($price) {
	$price = trim(addslashes($price), "$, \t\n\r\0\x0B");
	$decPrice = floatval($price);
	if($decPrice <= 0.01)
		die("Really? Did you honestly think a payment of $price would clear?");
	return round($decPrice, 2);
}

function GetPriceOverrides() {
	require_once('../db/price_override.php');
	$currentUserId = wp_get_current_user_id();
	$allPaymentItems = db_GetActivePaymentItems();
	
	$dbOverrides = db_GetAllPriceOverridesForUser($currentUserId);
	$overrides = array();
	foreach($dbOverrides as $override) {
		$priceOverride = new WmmsPriceOverride($override['itemId'], $override['itemPrice'], $override['goodThrough'], $currentUserId);
		foreach($allPaymentItems as $paymentItem) {
			if($paymentItem['DbId'] == $priceOverride->ItemId) {
				$priceOverride->ItemName = $paymentItem['itemName'];
			}
		}
		$overrides = $priceOverride;
	}
	return $overrides;
}
<?php
class WmmsPaymentItem {

	var $ItemName;
	var $Description;
	var $ItemPrice;
	var $IsFixedPrice; //If false, then $ItemPrice is merely a suggested price in the UI
	var $PaymentType;
	var $DbId;
	var $Active;
	var $IsRecurring;

	//__construct($dbId);
	//__construct($itemName, $description, $itemPrice, $isFixedPrice, $paymentType, $itemId, $active, $isRecurring);
	function __construct() {
		$numberOfArgs = func_num_args();
		$args = func_get_arg();
		if($numberOfArgs > 1) {
			$this->ItemName = $args[0];
			$this->Description = $args[1];
			$this->ItemPrice = $args[2];
			$this->IsFixedPrice = $args[3] === 1;
			$this->PaymentType = PaymentTypes::prettyPrint($args[4]);
			$this->DbId = $args[5];
			$this->Active = $args[6];
			$this->IsRecurring = $args[7] === 1;
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
		$this->Description = $dbItem['description'];
		$this->IsRecurring = $dbItem['isRecurring'];

		$priceOverride = db_GetPriceOverrideForUser(wp_get_current_user_id(), $this->DbId);
		if($priceOverride != null) {
			$this->ItemPrice = $priceOverride->ItemPrice;
		}
	}

	function saveToDb() {
		db_InsertOrUpdatePaymentItem($this);
	}
}

function GetActivePaymentItems() {
	require_once('../db/payment_items.php');
	$overrides = GetPriceOverrides();

	$dbItems = db_GetActivePaymentItems();
	$paymentItems = array();
	foreach($dbItems as $paymentItem) {
		$currentItem = new WmmsPaymentItem($paymentItem['itemName'], $paymentItem['itemPrice'], $paymentItem['isFixedPrice'], $paymentItem['itemType'], $paymentItem['id'], true, $paymentItem['isRecurring'] === 1);
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
		$currentItem = new WmmsPaymentItem($paymentItem['itemName'], $paymentItem['itemPrice'], $paymentItem['isFixedPrice'], $paymentItem['itemType'], $paymentItem['id'], $paymentItem['active'], $paymentItem['id'], $paymentItem['isRecurring'] === 1);

		$paymentItems = $currentItem;
	}
	return $paymentItems;
}
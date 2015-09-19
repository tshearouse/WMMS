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
		require_once('../db/transaction.php');
		db_AddTransaction($this->UserId, $this->TransactionId, $this->Date, $this->PaymentType, $this->TaggedFor);
	}
}
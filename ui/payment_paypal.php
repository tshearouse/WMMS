<?php
require_once('../business_layer/payment_items.php');

/*
 * Documentation on the variables we are passing to Paypal is available from:
 * https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/
 */

function printPaypalButton($paymentItem, $price) {
	
	echo ("<form action='https://www.paypal.com/cgi-bin/webscr' method='POST'>");
	//TODO: Add payment config table to store addresses or account id's to send money to
	echo ("<input type='hidden' name='business' value='e@mail.com' />");
	$transactionType = "_cart";
	if($paymentItem->IsRecurring) {
		$transactionType = "_xclick-subscriptions";
		printMonthlyRecurringFields($paymentItem, $price);
	} else {
		printOneTimePaymentFields($paymentItem, $price);
	}
	echo ("<input type='hidden' name='cmd' value='$transactionType' />");
	echo ("<input type='hidden' name='no_note' value='1' />");
	//TODO: Callback URL. Has to be different for each payment provider.
	echo ("<input type='hidden' name='notify_url' value='TODO' />");
	echo ("<input type='hidden' name='callback_timeout' value='6' />");
	echo ("<input type='hidden' name='item_number' value='$paymentItem->DbId' />");
	$currentUserId = wp_get_current_user_id();
	echo ("<input type='hidden' name='custom' value='$currentUserId' />");
	echo ("<input type='hidden' name='invoice' value='$price' />");
	echo ("<input type='image' name='submit' border='0' alt='Check out with Paypal' src='https://www.paypalobjects.com/webstatic/en_US/btn/btn_checkout_pp_142x27.png' />");
	echo ("</form>");
}

function printOneTimePaymentFields($paymentItem, $price) {
	echo ("<input type='hidden' name='item_name' value='$paymentItem->ItemName' />");
	echo ("<input type='hidden' name='amount' value='$price' />");
	
}

function printMonthlyRecurringFields($paymentItem, $price) {
	echo ("<input type='hidden' name='item_name' value='$paymentItem->ItemName' />");
	echo ("<input type='hidden' name='a3' value='$price' />");
	echo ("<input type='hidden' name='srt' value='1' />"); //Tells Paypal it's a recurring payment
	echo ("<input type='hidden' name='sra' value='1' />"); //Re-attempt on failure
	echo ("<input type='hidden' name='p3' value='1' />"); //Recur every 1 ...
	echo ("<input type='hidden' name='t3' value='M' />"); // ... month
}

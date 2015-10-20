<?php
require_once '../business_layer/roles.php';
AdminOrBoardRightsOrDie();
require_once '../business_layer/payment.php';
require_once '../business_layer/payment_types.php';
require_once '../business_layer/payment_items.php';

$item = new WmmsPaymentItem("New", 0, true, PaymentTypes::Unknown, -1, false);
$itemId = intval($_GET['wmms_item']);
if($itemId >= 0) {
	$item = new WmmsPaymentItem($itemId);
}

echo ("<form method='POST' target='manage_payment_items.php'>");
echo ("<input type='hidden' name='wmms_payment_item_id' value='$itemId' />");
$checked = "";
if($item->Active){
	$checked = "checked='true' ";
}
echo ("<p>Active <input type='checkbox' name='wmms_payment_item_active' $checked /></p>");
$checked = "";
if($item->Active){
	$checked = "checked='true' ";
}
echo ("<p>Fixed price <input type='checkbox' name='wmms_payment_item_fixed' $checked /></p>");
$price = $item->ItemPrice;
echo ("<p>Price $<input type='text' name='wmms_payment_item_price' value='$price' /></p>");
$description = $item->ItemName;
echo ("<p>Description <input type='text' name='wmms_payment_item_description' value='$description' /></p>");

echo ("<p>Item Type <select name='wmms_payment_item_type' >");
$paymentTypes = PaymentTypes::listAllPrettyPrintRoles();
foreach($paymentTypes as $paymentType) {
	echo ("<option value='$paymentType'>$paymentType</option>");
}
echo ("</select></p>");

echo ("<p><input type='submit' value='Save Changes' /></p></form>");
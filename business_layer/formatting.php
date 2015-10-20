<?php

function prettyPrintPrice($price) {
	setlocale(LC_MONETARY, 'en_US');
	$prettyPrice = money_format("%i", $price);

	return $prettyPrice;
}
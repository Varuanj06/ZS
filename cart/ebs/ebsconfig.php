<?php 
	
	$domain = 'http://miracas.com';
	if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) {
	  $domain = 'http://miracas.in';
	}

	$ebskey 		= "624cef2d308c3336f7104f3e6734dcae"; // Your Secret Key
	$account_id 	= "11035";
	//$reference_no	= "reference_no";
	$return_url 	= $domain."/ZS/new_cart/";
	$mode			= "LIVE"; //LIVE
	$description 	= "Purchase from Miracas";
	$main_country 	= "IN";

	/* new variables */

	$bank_code 				= "";
	$card_brand 			= ""; //All
	$channel 				= "0";
	$currency				= "";
	$display_currency 		= "INR";
	$display_currency_rates = "1";
	$emi					= "";
	$page_id				= "1600";
	$payment_mode 			= "";
	$payment_option 		= "";

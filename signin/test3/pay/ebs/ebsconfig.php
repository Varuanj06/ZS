<?php 
	
	$domain = 'http://miracas.com';
	if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) {
	  $domain = 'http://miracas.in';
	}

	$ebskey 		= "6b7273441f04274d693731cd3259789a"; // Your Secret Key
	$account_id 	= "25062";
	//$reference_no	= "reference_no";
	$return_url 	= $domain."/ZS/pay/";
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
	$page_id				= "10051";
	$payment_mode 			= "";
	$payment_option 		= "";

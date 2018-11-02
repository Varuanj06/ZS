<?php 

	$payu_key     	= "hnaeZS"; // Merchant key here as provided by Payu
	$payu_salt  	= "FP4o1Zuh"; // Merchant Salt as provided by Payu
  	$payu_url    	= "https://secure.payu.in/_payment"; // End point - change to https://secure.payu.in/_payment for LIVE mode

  	$success_url 	= "http://miracas.com/ZS/orders/?payu_success=1";
  	$failure_url 	= "http://miracas.com/ZS/orders/?payu_failure=1";

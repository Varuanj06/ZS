<?php require_once("ebsconfig.php"); ?>

<div class="text-center login-loading" style="margin-top:50px;"><i class="fa fa-refresh fa-spin" style="font-size: 40px;"></i></div>
<br><br>
<div class="well" style="max-width:700px;margin:auto;">
	Please wait while  redirecting to the payment gateway!
</div>

<?php 

	$POST 	= [];

	$POST['account_id'] 			= $account_id;
	$POST['address'] 				= $shipping_address;
	$POST['amount'] 				= $amount;
	$POST['bank_code'] 				= $bank_code;
	$POST['card_brand'] 			= $card_brand;
	$POST['channel'] 				= $channel;
	$POST['city'] 					= $shipping_city;
	$POST['country'] 				= $main_country;
	$POST['currency'] 				= $currency;
	$POST['description'] 			= $description;
	$POST['display_currency'] 		= $display_currency;
	$POST['display_currency_rates'] = $display_currency_rates;
	$POST['email'] 					= $shipping_email;
	$POST['emi'] 					= $emi;
	$POST['mode'] 					= $mode;
	$POST['name'] 					= $shipping_name;
	$POST['page_id'] 				= $page_id;
	$POST['payment_mode'] 			= $payment_mode;
	$POST['payment_option'] 		= $payment_option;
	$POST['phone'] 					= $shipping_phone;
	$POST['postal_code'] 			= $shipping_postal_code;
	$POST['reference_no'] 			= $reference_no;
	$POST['return_url'] 			= $return_url;
	$POST['ship_address'] 			= $shipping_address;
	$POST['ship_city'] 				= $shipping_city;
	$POST['ship_country'] 			= $main_country;
	$POST['ship_name'] 				= $shipping_name;
	$POST['ship_phone'] 			= $shipping_phone;
	$POST['ship_postal_code'] 		= $shipping_postal_code;
	$POST['ship_state'] 			= $shipping_state;
	$POST['state'] 					= $shipping_state;

?>

<?php
	
	//$hash = "$ebskey|".urlencode($account_id)."|".urlencode($amount)."|".urlencode($reference_no)."|".$return_url."|".urlencode($mode);
	//$secure_hash = md5($hash);

	$hashData = $ebskey;
	ksort($POST);
	foreach ($POST as $key => $value){
		if (strlen($value) > 0) {
			$hashData .= '|'.$value;
		}
	}
	if (strlen($hashData) > 0) {
		$secure_hash = strtoupper(hash("sha512",$hashData));
	}

?>


<form  method="post" action="https://secure.ebs.in/pg/ma/payment/request" name="payment">
	
	<input type="hidden" value="<?php echo $POST['account_id'];?>" name="account_id"/>
	<input type="hidden" value="<?php echo $POST['address'];?>" name="address"/>
	<input type="hidden" value="<?php echo $POST['amount'];?>" name="amount"/>
	<input type="hidden" value="<?php echo $POST['bank_code'];?>" name="bank_code"/>
	<input type="hidden" value="<?php echo $POST['card_brand'];?>" name="card_brand"/>
	<input type="hidden" value="<?php echo $POST['channel'];?>" name="channel"/>
	<input type="hidden" value="<?php echo $POST['city'];?>" name="city"/>
	<input type="hidden" value="<?php echo $POST['country'];?>" name="country"/>
	<input type="hidden" value="<?php echo $POST['currency'];?>" name="currency"/>
	<input type="hidden" value="<?php echo $POST['description'];?>" name="description"/>
	<input type="hidden" value="<?php echo $POST['display_currency'];?>" name="display_currency"/>
	<input type="hidden" value="<?php echo $POST['display_currency_rates'];?>" name="display_currency_rates"/>
	<input type="hidden" value="<?php echo $POST['email'];?>" name="email"/>
	<input type="hidden" value="<?php echo $POST['emi'];?>" name="emi"/>
	<input type="hidden" value="<?php echo $POST['mode'];?>" name="mode"/>
	<input type="hidden" value="<?php echo $POST['name'];?>" name="name"/>
	<input type="hidden" value="<?php echo $POST['page_id'];?>" name="page_id"/>
	<input type="hidden" value="<?php echo $POST['payment_mode'];?>" name="payment_mode"/>
	<input type="hidden" value="<?php echo $POST['payment_option'];?>" name="payment_option"/>
	<input type="hidden" value="<?php echo $POST['phone'];?>" name="phone"/>
	<input type="hidden" value="<?php echo $POST['postal_code'];?>" name="postal_code"/>
	<input type="hidden" value="<?php echo $POST['reference_no'];?>" name="reference_no"/>
	<input type="hidden" value="<?php echo $POST['return_url']; ?>" name="return_url"/>
	<input type="hidden" value="<?php echo $POST['ship_address'];?>" name="ship_address"/>
	<input type="hidden" value="<?php echo $POST['ship_city'];?>" name="ship_city"/>
	<input type="hidden" value="<?php echo $POST['ship_country'];?>" name="ship_country"/>
	<input type="hidden" value="<?php echo $POST['ship_name'];?>" name="ship_name"/>
	<input type="hidden" value="<?php echo $POST['ship_phone'];?>" name="ship_phone"/>
	<input type="hidden" value="<?php echo $POST['ship_postal_code'];?>" name="ship_postal_code"/>
	<input type="hidden" value="<?php echo $POST['ship_state'];?>" name="ship_state"/>
	<input type="hidden" value="<?php echo $POST['state'];?>" name="state"/>
	<input type="hidden" value="<?php echo $secure_hash; ?>" name="secure_hash"/>
 
</form>

<script>
	document.payment.submit();
</script>

<?php 
	exit();
?>

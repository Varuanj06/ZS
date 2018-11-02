<?php require_once("ebsconfig.php"); ?>

<div class="text-center login-loading" style="margin-top:50px;"><i class="fa fa-refresh fa-spin" style="font-size: 40px;"></i></div>
<br><br>
<div class="well" style="max-width:700px;margin:auto;">
	Please wait while  redirecting to the payment gateway!
</div>

<?php
	
	$hash = "$ebskey|".urlencode($account_id)."|".urlencode($amount)."|".urlencode($reference_no)."|".$return_url."|".urlencode($mode);
	
	$secure_hash = md5($hash);

?>

<form  method="post" action="https://secure.ebs.in/pg/ma/payment/request" name="payment">
	
	<input type="hidden" value="<?php echo $account_id; ?>" name="account_id">
	     
	<input type="hidden" value="<?php echo $return_url; ?>" name="return_url" />
	<input type="hidden" value="<?php echo $mode; ?>" name="mode" />
	<input type="hidden" value="<?php echo  $reference_no; ?>" name="reference_no" />
	<input type="hidden" value="<?php echo $amount; ?>" name="amount" />
	<input type="hidden" value="<?php echo $description; ?>"  name="description"/> 
	<input type="hidden" value="<?php echo $shipping_name; ?>" name="name" />
	<input type="hidden" value="<?php echo $shipping_address; ?>" name="address" />
	<input type="hidden" value="<?php echo $shipping_city; ?>" name="city" />
	<input type="hidden" value="<?php echo $shipping_state; ?>" name="state" />
	<input type="hidden" value="<?php echo $shipping_postal_code; ?>" name="postal_code" />
	<input type="hidden" value="<?php echo $main_country; ?>" name="country" />
	<input type="hidden" value="<?php echo $shipping_phone; ?>" name="phone" />
	<input type="hidden" value="<?php echo $shipping_email; ?>" name="email" />
	<input type="hidden" value="<?php echo $shipping_name; ?>" name="ship_name" />
	<input type="hidden" value="<?php echo $shipping_address; ?>" name="ship_address" />
	<input type="hidden" value="<?php echo $shipping_city; ?>" name="ship_city" />
	<input type="hidden" value="<?php echo $shipping_state; ?>" name="ship_state" />
	<input type="hidden" value="<?php echo $shipping_postal_code; ?>" name="ship_postal_code" />
	<input type="hidden" value="<?php echo $main_country; ?>" name="ship_country" />
	<input type="hidden" value="<?php echo $shipping_phone; ?>" name="ship_phone" />
	<input type="hidden" value="<?php echo $secure_hash; ?>" name="secure_hash" />
	
	<!-- new fields -->

	<input type="hidden" value="<?php echo $bank_code; ?>" name="bank_code"/>
	<input type="hidden" value="<?php echo $card_brand; ?>" name="card_brand"/>
	<input type="hidden" value="<?php echo $channel; ?>" name="channel"/>
	<input type="hidden" value="<?php echo $currency; ?>" name="currency"/>
	<input type="hidden" value="<?php echo $display_currency; ?>" name="display_currency"/>
	<input type="hidden" value="<?php echo $display_currency_rates; ?>" name="display_currency_rates"/>
	<input type="hidden" value="<?php echo $emi; ?>" name="emi"/>
	<input type="hidden" value="<?php echo $page_id; ?>" name="page_id"/>
	<input type="hidden" value="<?php echo $payment_mode; ?>" name="payment_mode"/>
	<input type="hidden" value="<?php echo $payment_option; ?>" name="payment_option"/>
 
</form>

<script>
	document.payment.submit();
</script>

<?php 
	exit();
?>

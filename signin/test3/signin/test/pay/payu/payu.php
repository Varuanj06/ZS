<?php require_once("payuconfig.php"); ?>

<div class="text-center login-loading" style="margin-top:50px;"><i class="fa fa-refresh fa-spin" style="font-size: 40px;"></i></div>
<br><br>
<div class="well text-center" style="max-width:700px;margin:auto;">
	Please wait while  redirecting to the payment gateway!
</div>

<?php

	/* #### VARIABLES #### */
    /* (this is in the orders/index.php file) */
    /*
	$payu_amount 			= 9;	
	$payu_first_name 		= 'Benito';
	$payu_product_info 		= 'Brown Jacket';
	$payu_email 			= 'david@gmail.com';
	$payu_phone 			= '5555';
    */
	
    $payu_last_name         = '';
    $payu_address1          = '';
    $payu_city              = '';
    $payu_state             = '';
    $payu_country           = '';
    $payu_zipcode           = '';

	/* #### GENERATE RANDOM TRANSACTION ID #### */
    /* (this is in the orders/index.php file) */
    /*
	$payu_txnid    		= substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    */

	/* #### GENERATE HASH #### */
	
	// key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10|salt

	$payu_hash   		= "$payu_key|$payu_txnid|$payu_amount|$payu_product_info|$payu_first_name|$payu_email|||||||||||$payu_salt";
    $payu_hash          = strtolower(hash('sha512', $payu_hash));

?>

<form action="<?php echo $payu_url; ?>" method="post" name="payuForm">
	
	<!-- PAYU REQUIRED PARAMETERS -->

    <input type="hidden" name="key" value="<?php echo $payu_key ?>" />
    <input type="hidden" name="hash" value="<?php echo $payu_hash ?>"/>
    <input type="hidden" name="txnid" value="<?php echo $payu_txnid ?>" />
      
    <input type="hidden" name="amount" value="<?php echo $payu_amount; ?>" />
    <input type="hidden" name="firstname" value="<?php echo $payu_first_name; ?>" />
    <input type="hidden" name="email" value="<?php echo $payu_email; ?>" />
    <input type="hidden" name="phone" value="<?php echo $payu_phone; ?>" />
    <input type="hidden" name="productinfo" value="<?php echo $payu_product_info; ?>" />
    <input type="hidden" name="surl" value="<?php echo $success_url; ?>" />
    <input type="hidden" name="furl" value="<?php echo $failure_url; ?>" />
    <!-- <input type="hidden" name="service_provider" value="payu_paisa" />  -->
	
	<!-- PAYU OPTIONAL PARAMETERS -->

    <input type="hidden" name="lastname" value="<?php echo $payu_last_name; ?>" />
    <input type="hidden" name="address1" value="<?php echo $payu_address1; ?>" />
    <input type="hidden" name="address2" value="" />
    <input type="hidden" name="city" value="<?php echo $payu_city; ?>" />
    <input type="hidden" name="state" value="<?php echo $payu_state; ?>" />
    <input type="hidden" name="country" value="<?php echo $payu_country; ?>" />
    <input type="hidden" name="zipcode" value="<?php echo $payu_zipcode; ?>" />
    <input type="hidden" name="curl" value="" />
    <input type="hidden" name="udf1" value="" />
    <input type="hidden" name="udf2" value="" />
    <input type="hidden" name="udf3" value="" />
    <input type="hidden" name="udf4" value="" />
    <input type="hidden" name="udf5" value="" />
    <input type="hidden" name="pg" value="" />

</form>

<script>
	document.payuForm.submit();
</script>

<?php 
	exit();
?>

<?php require_once("lib/config_paytm.php"); ?>
<?php require_once("lib/encdec_paytm.php"); ?>

<?php 
	$paramList = array();

	// Create an array having all required parameters for creating checksum.
	$paramList["MID"] 				= PAYTM_MERCHANT_MID;
	$paramList["ORDER_ID"] 			= $ORDER_ID;
	$paramList["CUST_ID"] 			= $CUST_ID;
	$paramList["INDUSTRY_TYPE_ID"] 	= 'Retail';
	$paramList["CHANNEL_ID"] 		= 'WEB';
	$paramList["TXN_AMOUNT"] 		= $TXN_AMOUNT;
	$paramList["WEBSITE"] 			= PAYTM_MERCHANT_WEBSITE;
	$paramList["EMAIL"] 			= $EMAIL; //Email ID of customer
	$paramList["MOBILE_NO"] 		= $MOBILE_NO; //Email ID of customer
	$paramList["CALLBACK_URL"] 		= 'http://miracas.com/ZS/pay/index.php';

	//Here checksum string will return by getChecksumFromArray() function.
	$checkSum = getChecksumFromArray($paramList, PAYTM_MERCHANT_KEY);

	$order->update_paytm_checksum($checkSum, $current_id_order);

?>

<div class="text-center login-loading" style="margin-top:50px;"><i class="fa fa-refresh fa-spin" style="font-size: 40px;"></i></div>
<br><br>
<div class="well text-center" style="max-width:700px;margin:auto;">
	Please wait while  redirecting to the payment gateway!
</div>

<form method="post" action="<?php echo PAYTM_TXN_URL ?>" name="f1">
	<table border="1">
		<tbody>
		<?php
		foreach($paramList as $name => $value) {
			echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
		}
		?>
		<input type="hidden" name="CHECKSUMHASH" value="<?php echo $checkSum ?>">
		</tbody>
	</table>
	<script type="text/javascript">
		document.f1.submit();
	</script>
</form>

<?php 
	exit();
?>
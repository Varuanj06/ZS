<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/order_voucher.php"); ?>
<?php require_once("../classes/vendor_product.php"); ?>
<?php require_once("../confirm/confirm_order.php"); ?>
<?php require_once("ebs/ebsconfig.php"); ?>
<?php require_once("payu/payuconfig.php"); ?>

<?php require('razorpay/config.php'); ?>
<?php require('razorpay/Razorpay.php'); ?>
<?php use Razorpay\Api\Api; ?>
<?php use Razorpay\Api\Errors\SignatureVerificationError; ?>

<!doctype html>
<html lang="en">
<head>

  	<?php require_once("../head.php"); ?>

	<?php

		$id_fb_user 		= $user['id'];

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */

		$order 				= new order();
		$order_detail 		= new order_detail();
		$order_address 		= new order_address();
		$address 			= new address();
		$product_lang 		= new product_lang();
		$product 			= new product();
		$vendor_product 	= new vendor_product();

	/* ====================================================================== *
        	CURRENT ORDER AND CURRENT DETAILS
 	 * ====================================================================== */	

		$current_id_order   = "";
		$details 			= array();
		if($order->get_id_order_by_fb_user($id_fb_user)){
			$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
			$details = $order_detail->get_list($current_id_order, " order by id_product ");
		}
		$order->map($current_id_order);

	/* ====================================================================== *
        	CURRENT ADDRESS
 	 * ====================================================================== */	

		$addresses 			= $address->get_list($id_fb_user, " order by date_update desc ");
		$current_address 	= "";
		foreach ($addresses as $row){ 
			$current_address = $row->get_id_address();
			break;
		}
		$address = new address();
		$address->map($current_address, $id_fb_user);

	/* ====================================================================== *
        	CHECK SOME STUFF
 	 * ====================================================================== */	

 		/* YOU NEED SOME THINGS TO BE HERE */

		if($current_id_order == "" || $current_address == "" || count($details) == 0){
			echo "<script>location.href='../select_address';</script>";
			exit();
		}

		/* REMOVE EXPIRED VOUCHERS */

		$order_voucher 		= new order_voucher();
		$order_voucher->delete_expired_vouchers($current_id_order);

	/* ====================================================================== *
        	CHECK IF AT LEAST ONE OF THE PRODUCTS BELONG TO A SPECIFIC KEYWORDS, IF SO SHOW THE COD PAYMENT
 	 * ====================================================================== */	
 		
		$found_in_keywords 	= "no";
		for ($i=0; $i < count($COD_keywords); $i++) { 
			$COD_keywords[$i] = "/".$COD_keywords[$i]."/";
		}
		foreach ($details as $row){
			$product->map($row->get_id_product());
			if (in_array($product->get_keywords(), $COD_keywords)) {
				$found_in_keywords = "yes";
			}
		}

	/* ====================================================================== *
        	CALCULATE TOTAL AMMOUNT
 	 * ====================================================================== */		

		$total_ammount = 0;
		foreach ($details as $row) {
			$qty 			= $row->get_qty();
			$price 			= get_the_price($row->get_id_product());
			$discount 		= get_the_discount($row->get_id_product(), $price);
			$price_final 	= ((float)$price-(float)$discount)*(float)$qty;

			$total_ammount += $price_final;
		}

		$order_voucher 		= new order_voucher();
		$vouchers_discount 	= $order_voucher->get_vouchers_discount($current_id_order, $total_ammount);
		$total_ammount 		= $total_ammount - $vouchers_discount;

		
	/* ====================================================================== *
        	SAVING PAYMENT
 	 * ====================================================================== */			

		$msj_from_excel = "";
		if(isset($_POST['action']) && $_POST['action'] == '1'){
			$payment_method = $_POST['payment_method'];
			$online_payment = '';

			if($payment_method=='EBS' || $payment_method=='PAYU' || $payment_method=='PAYTM' || $payment_method=='RAZORPAY'){
				$online_payment = $payment_method;
				$payment_method = 'Pay Online';
			}

			/* Get courier allocation */
			$all_good 			= true;
			$pay_method 		= $payment_method;
			$curr_id_order 		= $current_id_order;
			$pin_code 			= $address->get_pin_code();
			$courier_allocation = '';
			$final_amount 		= $total_ammount;
			require_once("../courier_allocation/check_excel.php");
			/* END Get courier allocation */

			if($all_good === true){
				
				$order->update_payment_method_and_courier_allocation($payment_method, $courier_allocation, $current_id_order);
				$order->update_online_payment($online_payment, $current_id_order);

				if($online_payment == 'EBS'){
					
					$amount 				= $total_ammount;
					$shipping_name 			= $address->get_name();
					$shipping_address 		= $address->get_address();
					$shipping_city 			= $address->get_city();
					$shipping_state 		= $address->get_state();
					$shipping_postal_code 	= $address->get_pin_code();
					$shipping_phone 		= $address->get_mobile_number();
					$shipping_email 		= $address->get_email();
					$reference_no			= $current_id_order;

					require_once("ebs/ebs.php");

				}else if($online_payment == 'PAYU'){
					
					$payu_txnid    		= substr(hash('sha256', mt_rand() . microtime()), 0, 20);

					$order->update_payu_transaction($payu_txnid, $current_id_order);

					$payu_amount 			= $total_ammount;	
					$payu_first_name 		= $address->get_name();
					$payu_product_info 		= "ORDER ".$current_id_order;
					$payu_email 			= $address->get_email();
					$payu_phone 			= $address->get_mobile_number();

					if($payu_amount=='' || $payu_first_name=='' || $payu_product_info=='' || $payu_email=='' || $payu_phone==''){
						$error = "<div class='alert alert-danger'>The order #".$current_id_order." is missing some of the following required parameters: <strong>amount, name, email or phone</strong>.</div>";
					}else{

						$success_url 	= $pay_success_url;
	  					$failure_url 	= $pay_failure_url;
						require_once("payu/payu.php");
						
					}

				}else if($online_payment == 'PAYTM'){

					$ORDER_ID 				= $current_id_order;
					$CUST_ID 				= $id_fb_user;
					$TXN_AMOUNT 			= $total_ammount;
					$EMAIL 					= $address->get_email();
					$MOBILE_NO 				= $address->get_mobile_number();

					if($ORDER_ID=='' || $CUST_ID=='' || $TXN_AMOUNT=='' || $EMAIL=='' || $MOBILE_NO==''){
						$error = "<div class='alert alert-danger'>The order #".$current_id_order." is missing some of the following required parameters: <strong>email or phone</strong>.</div>";
					}else{

						$count = 0;
						if($order->get_paytm_id_order() == ''){
							$count = 1;
						}else{
							$count = explode("x", $order->get_paytm_id_order())[1] + 1;
						}

						$paytm_id_order = $current_id_order."x".$count;
						$ORDER_ID 		= $paytm_id_order;
						$order->update_paytm_id_order($paytm_id_order, $current_id_order);	

						$CUST_ID 		= $id_fb_user."x".$count;

						require_once("paytm/paytm.php");
						
					}

				}else if($online_payment == 'RAZORPAY'){

					/* CREATE RAZORPAY ORDER */

					$api 			= new Api($keyId, $keySecret);
					$orderData 		= [
					    'receipt'         => $current_id_order,
					    'amount'          => $total_ammount * 100,
					    'currency'        => 'INR',
					    'payment_capture' => 1 // auto capture
					];
					$razorpayOrder 	= $api->order->create($orderData);

					/* SAVE RAZORPAY ORDER */

					$order->update_razorpay_order($razorpayOrder['id'], $current_id_order);

					/* OPEN MODAL */

					$data = [
					    "key"               => $keyId,
					    "amount"            => $orderData['amount'],
					    "name"              => "MIRACAS INTERNATIONAL",
					    "description"       => "Global Fashion",
					    "image"             => "https://cdn.razorpay.com/logos/7jvgohVKR59fk9_medium.jpg",
					    "prefill"           => [ 
					        "name"              => $address->get_name(), 
					        "email"             => $address->get_email(), 
					        "contact"           => $address->get_mobile_number(), 
					    ],
					    "notes"             => [ 
					        "address"           => $address->get_address(), 
					        "merchant_order_id" => $current_id_order, 
					    ],
					    "theme"             => [ "color" => "#F37254" ],
					    "order_id"          => $razorpayOrder['id'],
					];
					require('razorpay/razorpay_openmodal.php');


				}else{

					echo "<script>location.href='../confirm';</script>";
					exit();

				}

			}//end if "all_good"
		}

	/* ====================================================================== *
        	EBS RESPONSE (FROM EBS SITE)
 	 * ====================================================================== */			

		$error = "";
		
		if(isset($_POST['ResponseCode'])){
			
			$response 	= $_POST;
		    $sh 		= $response['SecureHash'];	
		    $params 	= $ebskey;
		    ksort($response);
			
			foreach ($response as $key => $value){
		        if (strlen($value) > 0 and $key!='SecureHash') {
					$params .= '|'.$value;
			    }
			}
						
		    $hashValue = strtoupper(hash("sha512",$params));
		  	if($sh!=$hashValue){

		  		$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong with your EBS payment, please try again.</div>';

		  	}else{

		  		if(confirm_order($id_fb_user, $current_id_order, $details, $current_address, true)){
				  	echo "<script>location.href='../confirm/?c=true';</script>";
					exit();
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
				}

		  	}
				 
		}

	/* ====================================================================== *
        	PAYU RESPONSE
 	 * ====================================================================== */				

		if(isset($_POST["status"]) && isset($_POST["firstname"]) && isset($_POST["amount"]) && isset($_POST["txnid"]) && isset($_POST["hash"]) && isset($_POST["key"]) && isset($_POST["productinfo"]) && isset($_POST["email"])){
			
			$status         = 'success';
			$first_name     = $_POST["firstname"];
			$amount         = $_POST["amount"];
			$txnid          = $_POST["txnid"];
			$hash           = $_POST["hash"];
			$key            = $_POST["key"];
			$product_info   = $_POST["productinfo"];
			$email          = $_POST["email"];

			$generate_hash  = '';
			if(isset($_POST["additionalCharges"])){
				$additionalCharges  = $_POST["additionalCharges"];
				$generate_hash         = "$additionalCharges|$payu_salt|$status|||||||||||$email|$first_name|$product_info|$amount|$txnid|$key";
			}else{	  
				$generate_hash         = "$payu_salt|$status|||||||||||$email|$first_name|$product_info|$amount|$txnid|$key";
			}
			$generate_hash = hash("sha512", $generate_hash);

			if($total_ammount!=$amount){
				$error = "<div class='alert alert-danger'>The amount you paid doesn't match with the amount in your cart please contact customer care for sorting out this issue.</div>";
			}else if ($hash != $generate_hash) {
				$error = "<div class='alert alert-danger'>Invalid Transaction. Please try again</div>";
			}else{

				if(isset($_GET['payu_success'])){
				//if(isset($_GET['payu_failure'])){
					
					if(confirm_order($id_fb_user, $current_id_order, $details, $current_address, true)){
					  	echo "<script>location.href='../confirm/?c=true';</script>";
						exit();
					}else{
						$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
					}

				}else if(isset($_GET['payu_failure'])){
					
					$error = "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";

				}	

			}  

		}	

	/* ====================================================================== *
        	PAYTM RESPONSE
 	 * ====================================================================== */	

        if(isset($_POST["CHECKSUMHASH"]) && $error == ''){

	 		require_once("paytm/lib/config_paytm.php");
			require_once("paytm/lib/encdec_paytm.php");

			$isValidChecksum 	= "FALSE";
			$paramList 			= $_POST;
			$paytmChecksum 		= isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

			//Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application’s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
			$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.

			if($isValidChecksum == "TRUE") {
				if ($_POST["STATUS"] == "TXN_SUCCESS") {
					$error = "<div class='alert alert-success'>Transaction status is success.</div>";
					
					if(confirm_order($id_fb_user, $current_id_order, $details, $current_address, true)){
					  	echo "<script>location.href='../confirm/?c=true';</script>";
						exit();
					}else{
						$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
					}
				}else {
					$error = "<div class='alert alert-danger'>Something went wrong. Transaction failed.</div>";
				}

			}else {
				$error = "<div class='alert alert-danger'>Something went wrong. Transaction mismatched.</div>";
			}

		}	

	/* ====================================================================== *
        	RAZORPAY RESPONSE
 	 * ====================================================================== */			

		if (empty($_POST['razorpay_payment_id']) === false){

		    $api = new Api($keyId, $keySecret);
		    $success = true;

		    try{
		       
		        $attributes = array(
		            'razorpay_order_id' 	=> $order->get_razorpay_order(),
		            'razorpay_payment_id' 	=> $_POST['razorpay_payment_id'],
		            'razorpay_signature' 	=> $_POST['razorpay_signature']
		        );

		        $api->utility->verifyPaymentSignature($attributes);

		    }catch(SignatureVerificationError $e){
		        $success = false;
		        $error = 'Razorpay Error : ' . $e->getMessage();
		    }

		    if ($success === true){
			    $error = "<div class='alert alert-success'>Your payment was successful.</div>";

			    if(confirm_order($id_fb_user, $current_id_order, $details, $current_address, true)){
				  	echo "<script>location.href='../confirm/?c=true';</script>";
					exit();
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
				}
			}else{
			    $error = "<div class='alert alert-danger'>Something went wrong. Your payment failed.</div>";
			}
		}

		$_POST = array(); // this will erase any post variable from any of the responses
	?>
	
	<style>
		.row{
			margin-bottom: 10px;
		}
		label{
			font-weight: 600;
		}
		
		.btn-group .btn:first-child{
			border: none !important;
		}
		.btn-group .btn{
			border: none !important;
		    background-color: #DEC395;
    		color: #6D4D1C !important;
		    padding: 10px;
		    font-size: 13px;
		    font-weight: 200;
		}
		.btn-group .btn:hover{
			background-color: #DABB84;
		}
		.btn-group .active{
			background-color: #D3AA64 !important;
		}
		.btn-group i{
			display: none;
		}
		.btn-group label.active i{
			display: inline-block;
			margin-right: 4px;
		}
	</style>
</head>
<body>

<?php if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { ?>
	<?php require_once("../menu_global.php"); ?> 
	<?php require_once("../sidebar_global.php"); ?> 
<?php }else{ ?>
	<?php require_once("../menu.php"); ?> 
	<?php require_once("../sidebar.php"); ?> 
<?php } ?>

<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">

	<?php require_once("../message.php"); ?>
	
	<script>$('.nav-right a[href="../cart"]').addClass('selected');</script>

	<div class="tabs-container">

		<h2><i class="fa fa-inr"></i> Payment</h2>
		<a href="../check_voucher?from=pay" class="btn btn-sm btn-green">go back</a>	

		<br><br>

		<?php echo $error; ?>
		<?php if($msj_from_excel!=""){ echo "<div class='alert alert-danger'>$msj_from_excel</div>"; } ?>

		<div class="cart-item">
			<form action="" method="post" name="form">
				<input type="hidden" name="action">
<!--
				<div class="alert alert-info text-center">
				<h5 style="margin:0;" > As the Indian Government has declared that the notes of INR 500/- and INR 1000/- will NOT BE LEGAL tender effective 9th Nov'16, Cash On Delivery is put on hold. We request you to make the payment online using your credit / debit cards or internet banking. 
				</div>

				<div class="alert alert-info text-center">
				<h5 style="margin:0;" >Miracas being one of the biggest fashion market in south east asia with over 100,000 styles, sometimes it is complex to manage the inventory in real time. To help you with checking the inventory real time, we have added a new functionality here. At the click of a button, you can be sure that the product is available in stock or whether it will take time to procure. </h5>
				</div>
				<a href="#" class="btn btn-gray check_url" style="background:#F1DBB5;color:#6D4D1C;font-weight:200;">Check the Stock Availability</a>
				<br><br>
				<div class="not_found_details"></div>
				<script>
					$('.check_url').on('click', function(e){
						e.preventDefault();
						var $this 	= $(this);
						var output  = $('.not_found_details').slideUp(300);

						$this.html('<i class="fa fa-circle-o-notch fa-spin"></i> checking...')
						$.get('check_url.php', function(r){
							$this.html('Check the Stock Availability');
							output.html(r).slideDown(300);
						});
					})
				</script>


				<a href="#" class="btn btn-gray check_url" style="background:#F1DBB5;color:#6D4D1C;font-weight:200;">Check the Stock Availability</a>
				<br><br>
				<div class="not_found_details"></div>
				<script>
					$('.check_url').on('click', function(e){
						e.preventDefault();
						var $this 	= $(this);
						var output  = $('.not_found_details').slideUp(300);

						$this.html('<i class="fa fa-circle-o-notch fa-spin"></i> checking...')
						$.get('check_url.php', function(r){
							$this.html('Check the Stock Availability');
							output.html(r).slideDown(300);
						});
					})
				</script>

-->
				<?php 
					foreach ($relationship_managers as $row_manager) { 
						$explode = explode("-", $row_manager['range']);
						if($total_ammount>=$explode[0] && $total_ammount<=$explode[1]){
				?>
							<div class="manager_card">
								<div class="manager_card_title">
									<img src="<?php echo $row_manager['img_src']; ?>" alt="" />
									<div class="manager_card_title_text">
										<?php echo $row_manager['name']; ?><br><?php echo $row_manager['email']; ?>
									</div>
								</div>
								I am your relationship manager and will help you with the fulfullment of this order. Feel free to drop a mail with your questions or concerns and I will be happy to help. You can ask about product details, payment methods, status of the order, etc.
							</div>
				<?php
							break;
						}
					}
				?>

				<img src="payments.png"/>
				<br>
				<br>
				<img src="payments1.png"/>

				<h3>Total: ₹<?php echo number_format($total_ammount, 2); ?></h3>
				<br>
				Select a payment method:
				<br><br>

				<div class="btn-group" data-toggle="buttons">
				  <?php if($total_ammount<1991 && $total_ammount >480 || $total_ammount ==0 || $found_in_keywords == 'yes'){ ?>
				  <label class="btn btn-primary <?php if($order->get_payment_method() == 'Cash on Delivery'){echo "active";}; ?> ">
				  	<i class="glyphicon glyphicon-ok"></i>
				    <input type="radio" name="payment_method" value="Cash on Delivery" <?php if($order->get_payment_method() == 'Cash on Delivery'){echo "checked";}; ?> autocomplete="off"> 
				    Cash on Delivery
				  </label>
				  <?php } ?>

				  <?php if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { ?>
				  <label class="btn btn-primary <?php if($order->get_payment_method() == 'Pay Online' && ($order->get_online_payment() == 'EBS' || $order->get_online_payment() == '')){echo "active";}; ?> ">
				  	<i class="glyphicon glyphicon-ok"></i>
				    <input type="radio" name="payment_method" value="EBS" <?php if($order->get_payment_method() == 'Pay Online' && ($order->get_online_payment() == 'EBS' || $order->get_online_payment() == '')){echo "checked";}; ?> autocomplete="off"> 
				    Pay Online
				  </label>
				  <?php } ?>

				  <label style="padding:8px;" class="btn btn-primary <?php if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYTM'){echo "active";}; ?> ">
				  	
				  	<i style="top:4px;" class="glyphicon glyphicon-ok"></i>
				    <input type="radio" name="payment_method" value="PAYTM" <?php if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYTM'){echo "checked";}; ?> autocomplete="off"> 
				    <img src="http://miracas.com/ZS/assets/paytm.png" alt="" style="height:22px;">
				    <span style="position:relative;top:2px;">Pay Online Using Debit/Credit Card Or Net Banking</span>

				  </label>
				  <!--
				  <label class="btn btn-primary <?php if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'RAZORPAY'){echo "active";}; ?> ">
				  	<i class="glyphicon glyphicon-ok"></i>
				    <input type="radio" name="payment_method" value="RAZORPAY" <?php if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'RAZORPAY'){echo "checked";}; ?> autocomplete="off"> 
				    Pay Online Using Razor Pay
				  </label>
				  
				  -->
				  
				    <label class="btn btn-primary <?php if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYU'){echo "active";}; ?> ">
				  	<i class="glyphicon glyphicon-ok"></i>
				    <input type="radio" name="payment_method" value="PAYU" <?php if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYU'){echo "checked";}; ?> autocomplete="off"> 
				    Pay Online Using PayU
				  </label>

				
				</div>

						
			</form>
			<br>

			<hr>
			
			<p>
				<a href="javascript:confirm_payment();" class="btn btn-green btn-default">Confirm Payment</a>
			</p>
		</div>	

	</div> <!-- End tabs-container -->
	
	
	<script>
		function confirm_payment(){
			if($("input:radio[name='payment_method']").is(":checked")){
				if(confirm("Are you sure?")){
					document.form.action.value = "1";
					document.form.submit();
				}
			}else{
				alert("Please select a payment method!");
			}
		}
	</script>

	</div></div>
	<?php require_once("../footer.php"); ?>
	
	</div>
</body>

</html>
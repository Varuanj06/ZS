<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/espresso_products.php"); ?>
<?php require_once("../confirm/confirm_order.php"); ?>
<?php require_once("functions.php"); ?>

<?php require_once("ebs/ebsconfig.php"); ?>
<?php require_once("payu/payuconfig.php"); ?>
<?php require_once('razorpay/config.php'); ?>
<?php require_once('razorpay/Razorpay.php'); ?>
<?php use Razorpay\Api\Api; ?>
<?php use Razorpay\Api\Errors\SignatureVerificationError; ?>

<?php

/* ====================================================================== *
    	CLASSES
 * ====================================================================== */			
	
	$address 		= new address();
	$order 			= new order();	

/* ====================================================================== *
    	INIT
 * ====================================================================== */			

	$id_fb_user 		= $user['id'];
	$current_id_order 	= get_id_order($id_fb_user);
	$details 			= get_order_details($current_id_order);
	$totalAmount 		= get_total_amount($details);
	$totalVouchers 		= get_total_vouchers($current_id_order, $totalAmount);
	$total_ammount 		= $totalAmount - $totalVouchers;
	$address 			= get_address($id_fb_user);
	$current_address 	= $address->get_id_address();

	$order->map($current_id_order);

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

			$new_shipping_fee 		= $total_ammount==0 ? 0 : $shipping_fee;
			$new_cod_fee 			= $total_ammount==0 ? 0 : ($payment_method=='Pay Online' ? 0 : $cod_fee);

			$total_ammount_and_fee 	= $total_ammount + $new_shipping_fee + $new_cod_fee;
			
			$order->update_payment_method_and_courier_allocation($payment_method, $courier_allocation, $current_id_order);
			$order->update_online_payment($online_payment, $current_id_order);
			$order->update_fee($new_shipping_fee, $new_cod_fee, $current_id_order);

			if($online_payment == 'EBS'){
				
				$amount 				= $total_ammount_and_fee;
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

				$payu_amount 			= $total_ammount_and_fee;	
				$payu_first_name 		= $address->get_name();
				$payu_product_info 		= "ORDER ".$current_id_order;
				$payu_email 			= $address->get_email();
				$payu_phone 			= $address->get_mobile_number();

				if($payu_amount=='' || $payu_first_name=='' || $payu_product_info=='' || $payu_email=='' || $payu_phone==''){
					$error = "<div class='alert alert-danger'>The order #".$current_id_order." is missing some of the following required parameters: <strong>amount, name, email or phone</strong>.</div>";
				}else{
					/*
					$success_url 	= $pay_success_url;
  					$failure_url 	= $pay_failure_url;
  					*/
					require_once("payu/payu.php");
					
				}

			}else if($online_payment == 'PAYTM'){

				$ORDER_ID 				= $current_id_order;
				$CUST_ID 				= $id_fb_user;
				$TXN_AMOUNT 			= $total_ammount_and_fee;
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
				    'amount'          => $total_ammount_and_fee * 100,
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

		if( sprintf("%01.2f",($total_ammount+$order->get_shipping_fee()+$order->get_cod_fee())) != sprintf("%01.2f", $amount) ){
			//echo ($total_ammount+$order->get_shipping_fee()+$order->get_cod_fee()) ." == ".$amount;
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

	if($error != '' || $msj_from_excel != '' || (isset($_POST['action']) && $_POST['action'] == '1') || (isset($_GET['from_confirm']) && $_GET['from_confirm']=='1')){
		echo " <script>jQuery(document).ready(function(){ jQuery('.form-wizard').bootstrapWizard('show',3); });</script>";
	}

	$_POST = array(); // this will erase any post variable from any of the responses

	echo $error;
	if($msj_from_excel!=""){ echo "<div class='alert alert-danger'>$msj_from_excel</div>"; }



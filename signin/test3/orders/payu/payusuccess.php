<?php require_once("payuconfig.php"); ?>

<?php

  $status         = $_POST["status"];
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
	 
  if ($hash != $generate_hash) {
     $msg = "<div class='alert alert-danger'>Invalid Transaction. Please try again</div>";
	}else{
    
    $order = new order();
    $order->map_by_payu_transaction($txnid);

    $order->update_payment_method('Pay Online', $order->get_id_order());

    $msg = "<div class='alert alert-success'>The transaction of the order #".$order->get_id_order()." was $status. Your order will soon be shipped.</div>";

	}         

?>	
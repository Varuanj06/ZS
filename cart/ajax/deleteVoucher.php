<?php require_once("../../fb_validator.php"); ?>
<?php require_once("../../session_check.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/order_voucher.php"); ?>
<?php require_once("../../classes/voucher.php"); ?>
<?php require_once("../functions.php"); ?>
<?php require_once("getAll_data.php"); ?>

<?php 

/* ====================================================================== *
        GLOBAL VARS
 * ====================================================================== */       	

	$id_fb_user 	= $user['id'];
	$id_order 		= get_id_order($id_fb_user);
	$details 		= get_order_details($id_order);

	$POST 			= (array)json_decode(trim(file_get_contents('php://input')));

	if($id_order == '-1'){
		$output = array();
		$output['error'] = true;
		echo json_encode($output);
		exit();
	}

/* ====================================================================== *
        INIT
 * ====================================================================== */      

	$address 			= get_address($id_fb_user);
	$totalAmmount 		= get_total_amount($details);		

/* ====================================================================== *
        CLASSES
 * ====================================================================== */      

 	$order 				= new order();
	$order_detail 		= new order_detail();
	$order_voucher 		= new order_voucher();
	$voucher 			= new voucher(); 	

 /* ====================================================================== *
        DELETE ORDER DETAIL
 * ====================================================================== */       		

	$error = false;
	$conn->beginTransaction();

	// delete voucher

	$order_voucher->set_id_order($id_order);
	$order_voucher->set_id_voucher($POST['id_voucher']);
	if(!$order_voucher->delete()){
		$error = true;
	}

	// Create a new voucher if the discount is more than the total 

	if(!$voucher->delete_automatic_voucher($id_order)){
		$error = true;
	}

	$vouchers_discount 	= $order_voucher->get_vouchers_discount_real($id_order, $totalAmmount);
	$difference 		= $totalAmmount - $vouchers_discount;
	if($difference < 0){
		$voucher = new voucher();

		$voucher->set_id_voucher($voucher->max_id_voucher());
		$voucher->set_code(md5($voucher->get_id_voucher()));
		$voucher->set_emails("/".$address->get_email()."/");
		$voucher->set_till_date(date('Y-m-d', strtotime('+1 year')));
		$voucher->set_value_kind('amount');
		$voucher->set_value(abs($difference));
		$voucher->set_made_from_id_order($id_order);
		$voucher->set_description("Leftover Voucher");
		$voucher->set_min_cart_value("0");
		$voucher->set_visibility("Y");
		
		if( !$voucher->insert() ){
			$error = true;
		}
	}

	if($error){
	  $conn->rollBack();
	}else{
	  $conn->commit();
	}

/* ====================================================================== *
        JSON OUTPUT
 * ====================================================================== */       	

    $output = array();

    set_all($output);
    
    $output['error'] = $error;

	echo json_encode($output);

?>

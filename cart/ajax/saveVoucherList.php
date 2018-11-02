<?php require_once("../../fb_validator.php"); ?>
<?php require_once("../../session_check.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/voucher.php"); ?>
<?php require_once("../../classes/order_voucher.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
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

 	$voucher 			= new voucher();
	$order_voucher 		= new order_voucher();
	$order_detail 		= new order_detail();

/* ====================================================================== *
        DELETE ESPRESSO PRODUCTSO (this will not longer be needed)
 * ====================================================================== */      

 	//$order_detail->delete_espresso_products($id_order);	

 /* ====================================================================== *
        DELETE ORDER DETAIL
 * ====================================================================== */       		

	$error = false;
	$conn->beginTransaction();

	if(!$order_voucher->delete_all_from_order($id_order)){
		$error = true;
	}

	$voucherList 	= $voucher->get_all_for_user($address->get_email(), $id_order, "order by till_date");

	foreach ($voucherList as $row){
		$found = false;

		foreach($POST['voucherList'] as $row_inner){
			if( $row_inner->id_voucher == $row->get_id_voucher() && $row_inner->checked){
				$found = true;
			}
		}

		if( $found ){

			$order_voucher->set_id_order($id_order);
			$order_voucher->set_id_voucher($row->get_id_voucher());
			$order_voucher->set_code($row->get_code());
			$order_voucher->set_email($address->get_email());
			$order_voucher->set_till_date($row->get_till_date());
			$order_voucher->set_value_kind($row->get_value_kind());
			$order_voucher->set_value($row->get_value());

			if(!$order_voucher->insert()){
				$error = true;
				break;
			}

		}
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

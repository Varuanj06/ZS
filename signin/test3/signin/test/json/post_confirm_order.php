
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/order_voucher.php"); ?>
<?php require_once("../classes/message.php"); ?>

<?php 

	$order 			= new order();
	$address 		= new address();
	$order_address 	= new order_address();
	$message 		= new message();

	$error = false;
	$conn->beginTransaction();

	
	$id_fb_user 			= $_GET['fb_user'];
	$payment_method 		= $_GET['payment_method'];
	$id_address 			= $_GET['id_address'];
	$current_id_order 		= $order->get_id_order_by_fb_user($id_fb_user);
	
	/* ##### CHECK IF THERE'S A CURRENT ORDER ##### */	
	if(!$order->get_id_order_by_fb_user($id_fb_user)){
		$error = true;
	}

	// SAVE ORDER TOTALS
	$order_detail 		= new order_detail();
	$details = $order_detail->get_list($current_id_order, " order by id_product ");
	
	$total_ammount 	= 0;
	$total_discount = 0;
	foreach ($details as $row) {
		$qty 			= $row->get_qty();
		$price 			= get_the_price($row->get_id_product_prestashop());
		$discount 		= get_the_discount($row->get_id_product(), $price);
		$total_ammount 	+= (float)$price*(float)$qty;
		$total_discount += (float)$discount*(float)$qty;
	}
	$order_voucher 		= new order_voucher();
	$vouchers_discount 	= $order_voucher->get_vouchers_discount($current_id_order, $total_ammount-$total_discount);
	if(!$order->update_totals($total_ammount, $total_discount, $vouchers_discount, $current_id_order)){
		$error = true;
	}

	if(($total_ammount-$total_discount-$vouchers_discount) == 0){
		if(!$order->update_free_order("yes", $current_id_order)){
			$error = true;
		}
	}
	// END SAVE ORDER TOTALS

	/* ##### ADD ORDER ADDRESS ##### */
	if(!$address->map($id_address, $id_fb_user)){
		$error = true;
	}
	$id_order_address = $order_address->max_id_order_address($id_fb_user);
	$order_address->set_id_order_address($id_order_address);
	$order_address->set_id_fb_user($id_fb_user);
	$order_address->set_name($address->get_name());
	$order_address->set_mobile_number($address->get_mobile_number());
	$order_address->set_address($address->get_address());
	$order_address->set_landmark($address->get_landmark());
	$order_address->set_city($address->get_city());
	$order_address->set_state($address->get_state());
	$order_address->set_pin_code($address->get_pin_code());
	$order_address->set_email($address->get_email());

	if(!$order_address->insert()){
		$error = true;
	}

	/* ##### UPDATE PAYMENT ##### */

	/* Get courier allocation */
	$msj_from_excel 	= "";
	$all_good 			= true;
	$pay_method 		= $payment_method;
	$curr_id_order 		= $current_id_order;
	$pin_code 			= $address->get_pin_code();
	$courier_allocation = '';
	$final_amount 		= ($total_ammount-$total_discount-$vouchers_discount);
	require_once("../courier_allocation/check_excel.php");
	if($all_good === false){ 
		//$error = true; 
		$courier_allocation = 'CoD not available';

	}
	/* END Get courier allocation */

	if(!$order->update_payment_method_and_courier_allocation($payment_method, $courier_allocation, $current_id_order)){
		$error = true;
	}
	if(!$order->update_payed('yes', $current_id_order)){
		$error = true;
	}

	/* ##### CONFIRM ORDER ##### */
	$order->set_id_order($current_id_order);
	$order->set_id_order_address($id_order_address);
	if(!$order->update_address()){
		$error = true;
	}
	if(!$order->finish_order()){
		$error = true;
	}
	// ##### SEND AUTO MESSAGE #####
	if(!$message->send_auto_message($order, $current_id_order, "")){
		$error = true;
	}
	$message->send_SMS_by_order($order, $current_id_order, $order_address);
	// ##### END SEND AUTO MESSAGE #####

	$output = array();

	if($error){
	  	$conn->rollBack();
	  	//echo "error";
	  	$output['user_status'] 	= '0';
		$output['message'] 		= 'Something wrong';
	}else{
	  	$conn->commit();
	  	//echo 'success';
	  	$output['user_status'] 	= '1';
		$output['message'] 		= 'Success';
	}

	echo json_encode($output);

// post_confirm_order.php?fb_user=10152767632557633&payment_method=Cash on Delivery&id_address=4




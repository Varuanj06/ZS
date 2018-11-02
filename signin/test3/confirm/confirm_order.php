<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/order_voucher.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product_stock.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../classes/message.php"); ?>

<?php 

	function confirm_order($id_fb_user, $current_id_order, $order_details, $current_address, $payed){

		global $conn;

		$error = false;
		$conn->beginTransaction();

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */

		$order 				= new order();
		$order_voucher 		= new order_voucher();
		$order_detail 		= new order_detail();
		$address 			= new address();
		$order_address 		= new order_address();
		$message 			= new message();
		$product_stock 		= new product_stock();

	/* ====================================================================== *
        	SAVE ORDER TOTAL
 	 * ====================================================================== */	

 		/* GET AMOUNT AND DISCOUNT */

		$total_ammount 	= 0;
		$total_discount = 0;
		foreach ($order_details as $row) {
			$qty 			= $row->get_qty();
			$price 			= get_the_price($row->get_id_product());
			$discount 		= get_the_discount($row->get_id_product(), $price);
			$total_ammount 	+= (float)$price*(float)$qty;
			$total_discount += (float)$discount*(float)$qty;

			$order_detail->update_amount_and_discount($row->get_id_order(), $row->get_id_order_detail(), $price, $discount);
		}

		/* GET VOUCHER (THE VOUCHER IS LIKE A COUPON THAT APPLIES TO THE ORDER IN GENERAL) */

		$vouchers_discount 	= $order_voucher->get_vouchers_discount($current_id_order, $total_ammount-$total_discount);

		/* SAVE THE TOTALS OF THE ORDR */

		if(!$order->update_totals($total_ammount, $total_discount, $vouchers_discount, $current_id_order)){
			$error = true;
		}

		/* IF THE FINAL AMOUNT IS 0 THEN IT IS A "FREE ORDER" */

		if(($total_ammount-$total_discount-$vouchers_discount) == 0){
			if(!$order->update_free_order("yes", $current_id_order)){
				$error = true;
			}
		}

	/* ====================================================================== *
        	UPDATE PAYED (I THINK THIS IS ONLY USED IF ITS AN ONLINE PAYMENT, BUT I COUDN'T FIND IF THIS IS USED SOMEWHERE)
 	 * ====================================================================== */		

 		if($payed){
			if(!$order->update_payed('yes', $current_id_order)){
				$error = true;
			}
		}

	/* ====================================================================== *
        	UPDATE STOCK
 	 * ====================================================================== */			

		foreach ($order_details as $row) {
			$color 			= $row->get_color() == '' ? 'Empty' : $row->get_color();
			$size 			= $row->get_size() == '' ? 'Empty' : $row->get_size();

			$product_stock->update_stock($row->get_id_product(), $color, $size, $row->get_qty());
		}

	/* ====================================================================== *
        	SAVE THE ADDRESS IN A NEW TABLE FOR CONFIRMED ORDRERS
 	 * ====================================================================== */		

		$address->map($current_address, $id_fb_user);

		$id_order_address 	= $order_address->max_id_order_address($id_fb_user);
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

	/* ====================================================================== *
        	FINISH ORDER
 	 * ====================================================================== */			

		$order->set_id_order($current_id_order);
		$order->set_id_order_address($id_order_address);
		if(!$order->update_address()){
			$error = true;
		}
		if(!$order->finish_order()){
			$error = true;
		}

	/* ====================================================================== *
        	SEND SMS AND MESSAGE
 	 * ====================================================================== */			

		if(!$message->send_auto_message($order, $current_id_order, "")){
			$error = true;
		}
		$message->send_SMS_by_order($order, $current_id_order, $order_address);

	/* ====================================================================== *
        	RETURN
 	 * ====================================================================== */		

		if($error){
		  	$conn->rollBack();
		  	return false;
		}else{
		  	$conn->commit();
		  	return true;
		}

	}
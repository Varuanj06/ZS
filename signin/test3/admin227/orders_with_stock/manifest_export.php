<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>

<?php 
	
	$order 					= new order();
	$order_detail 			= new order_detail();
	$product_lang 			= new product_lang();
	$order_address 			= new order_address();

	$courier 				= $_GET['courier'];
	$payment_method 		= $_GET['payment_method'];
	$output 				= array();

	// get orders with sent products
	$orders 				= $order->get_orders_with_sent_products( " order by date_done " );

	$cont = 0;
	foreach ($orders as $order) {
		if($order->get_courier_allocation() != $courier){continue;}

		if($order->get_free_order() == 'yes' && $payment_method == 'Pay Online'){
			//all good
			// if its a free order show it when user wants the "Pay Online" manifest
		}else if($order->get_free_order() != 'yes' && $payment_method == $order->get_payment_method()){
			//all good
			// if its NOT a free order then just make sure the payment method of the order matches the want the user selects
		}else{
			continue;
		}

		$set_order = array();

		$cont++;
		$order_address->map($order->get_id_order_address(), $order->get_id_fb_user());

		$set_order['cont'] 						= $cont;
		$set_order['id_order'] 					= $order->get_id_order();
		$set_order['name'] 						= $order_address->get_name();	
		$set_order['email']  					= $order_address->get_email();
		$set_order['mobile_number']  			= $order_address->get_mobile_number();
		$set_order['address']  					= $order_address->get_address();
		$set_order['landmark']  				= $order_address->get_landmark();
		$set_order['city']  					= $order_address->get_city();
		$set_order['state']  					= $order_address->get_state();
		$set_order['pin_code']  				= $order_address->get_pin_code();
		$set_order['payment_method']  			= $order->get_payment_method();
		$set_order['final_amount'] 				= $order->get_total_amount()-$order->get_total_discount()-$order->get_total_discount_voucher();

		$order_details 	= $order_detail->get_details_sent( $order->get_id_order()," order by 1 " );
		foreach ($order_details as $detail) {
			$product	= new product();
			$product->map($detail->get_id_product());

			$details = array();

			$details['id_product_prestashop'] 	= $detail->get_id_product_prestashop();
			$details['name'] 					= $product->get_name();
			$details['color'] 					= $detail->get_color();
			$details['size'] 					= $detail->get_size();
			$details['qty'] 					= $detail->get_qty();

			$set_order['details'][] = $details;
		}

		$output[] = $set_order;
	}

	$_SESSION['manifest_export'] 			= $output;
	$_SESSION['manifest_export_courier'] 	= $courier;

	echo "<a href='javascript:window.close();'>close</a>";

	echo "<script>location.href='manifest_excel.php';</script>";
?>

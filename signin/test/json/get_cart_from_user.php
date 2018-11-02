<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/order.php");
	require_once("../classes/order_detail.php");
	require_once("../classes/product.php");
	require_once("../classes/product_lang.php");
	require_once("../classes/functions.php");

	// OBJETCS
	$order 				= new order();
	$order_detail 		= new order_detail();
	$product 			= new product();

	// VARS
	$id_fb_user 		= $_GET['fb_user'];
	$current_id_order   = "";
	$details 			= array();
	if($order->get_id_order_by_fb_user($id_fb_user)){
		$current_id_order 	= $order->get_id_order_by_fb_user($id_fb_user);
		$details 			= $order_detail->get_list($current_id_order, " order by id_product ");
	}

	$output 		= array();
	foreach ($details as $row){
		$tmp 	= array();

		$tmp['id_order_detail'] 	= $row->get_id_order_detail();
		$tmp['id_product'] 			= $row->get_id_product_prestashop();
		$tmp['color'] 				= $row->get_color();
		$tmp['size'] 				= $row->get_size();
		$tmp['qty'] 				= $row->get_qty();

		$product->map($row->get_id_product());
		$tmp['img_url'] 			= $product->get_image_link();
		$tmp['product_name'] 		= $product->get_name();

		$price 						= get_the_price($row->get_id_product_prestashop());
		$discount 					= get_the_discount($row->get_id_product(), $price);
		$price_final 				= number_format(((float)$price-(float)$discount)*(float)$row->get_qty(), 2, '.', ',');

		$tmp['price'] 				= $price_final;

		$output[] = $tmp;
	}

	//echo json_encode($output, JSON_PRETTY_PRINT);
	echo json_encode($output);

// get_cart_from_user.php?fb_user=10152767632557633
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
	$orders 			= $order->get_list_per_user($id_fb_user, " order by date_done desc "); 

	$output 		= array();
	foreach ($orders as $row){

		$details 	= $order_detail->get_list($row->get_id_order(), " order by id_product ");
		$date_order = $row->get_date_done();

		$products 		= array();
		foreach ($details as $row) {

			$tmp_details 						= array();
			$tmp_details['id_product'] 			= $row->get_id_product_prestashop();
			$tmp_details['color'] 				= $row->get_color();
			$tmp_details['size'] 				= $row->get_size();
			$tmp_details['qty'] 				= $row->get_qty();

			$product->map($row->get_id_product());
			$tmp_details['img_url'] 			= $product->get_image_link();
			$tmp_details['product_name'] 		= $product->get_name();

			$price 			= get_the_price($row->get_id_product());
			$discount 		= get_the_discount($row->get_id_product(), $price);
			$price_final 	= number_format(((float)$price-(float)$discount)*(float)$row->get_qty(), 2, '.', ',');

			$tmp_details['price'] 		= $price_final;

			$products[] = $tmp_details;
		}

		$tmp 				= array();
		$tmp['id_order'] 	= $row->get_id_order();
		$tmp['date_order'] 	= $date_order;
		$tmp['products'] 	= $products;

		$output[] 	= $tmp;
	}

	echo json_encode($output, JSON_PRETTY_PRINT);
	//echo json_encode($output);

// get_orders_from_user.php?fb_user=10152767632557633
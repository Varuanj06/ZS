<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/order.php");
	require_once("../classes/order_detail.php");
	require_once("../classes/keyword.php");
	require_once("../classes/product.php");
	require_once("../classes/espresso_keywords.php");
	require_once("../classes/espresso_products.php");
	require_once("../classes/product_lang.php");
	require_once("../classes/functions.php");
	require_once("../classes/functions_espresso.php");

	// OBJETCS
	$order 				= new order();
	$order_detail 		= new order_detail();
	$product 			= new product();
	$espresso_products 	= new espresso_products();
	$keyword 			= new keyword();
	$espresso_keywords 	= new espresso_keywords();

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
		$tmp['id_product'] 			= $row->get_id_product();
		$tmp['id_product_prestashop'] = $row->get_id_product_prestashop();

		$tmp['color'] 				= $row->get_color();
		$tmp['size'] 				= $row->get_size();
		$tmp['qty'] 				= $row->get_qty();
		$tmp['order_type'] 			= $row->get_order_type();

		if($tmp['order_type'] == 'espresso'){

			$espresso_products->map($row->get_id_product());
			$espresso_keywords->map($espresso_products->get_id_keyword());

			$tmp['keyword_id'] 				= $espresso_keywords->get_id_keyword();
			$tmp['keyword'] 				= $espresso_keywords->get_keyword();
			$tmp['keyword_status'] 			= $espresso_keywords->get_status();
			$tmp['keyword_discount_type'] 	= $espresso_keywords->get_discount_type();
			$tmp['keyword_discount'] 		= $espresso_keywords->get_discount();

			$tmp['img_url'] 			= $espresso_products->get_image_link();
			$tmp['product_name'] 		= $espresso_products->get_name();

			$price 						= get_the_price_espresso($row->get_id_product());
			$discount 					= get_the_discount_espresso($row->get_id_product(), $price);
			$price_final 				= number_format(((float)$price-(float)$discount)*(float)$row->get_qty(), 2, '.', ',');

			$tmp['price'] 				= $price_final;	

		}else{

			$product->map($row->get_id_product());
			$keyword->map( substr($product->get_keywords(), 1, -1) );
			
			$tmp['keyword_id'] 				= '';
			$tmp['keyword'] 				= $keyword->get_keyword();
			$tmp['keyword_status'] 			= $keyword->get_status();
			$tmp['keyword_discount_type'] 	= $keyword->get_discount_type();
			$tmp['keyword_discount'] 		= $keyword->get_discount();

			$tmp['img_url'] 				= $product->get_image_link();
			$tmp['product_name'] 			= $product->get_name();

			$price 						= get_the_price($row->get_id_product());
			$discount 					= get_the_discount($row->get_id_product(), $price);
			$price_final 				= number_format(((float)$price-(float)$discount)*(float)$row->get_qty(), 2, '.', ',');

			$tmp['price'] 				= $price_final;	

		}
		

		$output[] = $tmp;
	}

	echo json_encode($output, JSON_PRETTY_PRINT);
	//echo json_encode($output);

// get_cart_from_user.php?fb_user=10152767632557633
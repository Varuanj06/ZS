<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/espressos.php");
	require_once("../classes/espresso_products.php");
	require_once("../classes/attribute.php");
	require_once("../classes/image.php");
	require_once("../classes/product_lang.php");
	require_once("../classes/functions_espresso.php");

	//OBJETCS
	$espressos 			= new espressos();

	$list 				= $espressos->get_list_by_user_id($_GET['user_id'], "");

	$output = array();

	foreach ($list as $row) {
	
		$tmp 					= array();

		// ESPRESSO

		$tmp['id'] 						= $row->get_id();
		$tmp['user_id'] 				= $row->get_user_id();
		$tmp['keyword_id'] 				= $row->get_keyword_id();
		$tmp['product_id'] 				= $row->get_product_id();
		$tmp['prestashop_id'] 			= $row->get_prestashop_id();
		$tmp['created_at'] 				= $row->get_created_at();
		$tmp['updated_at'] 				= $row->get_updated_at();

		// PRODUCT

		$attribute 			= new attribute();
		$image 				= new image();
		$espresso_products 	= new espresso_products();
		$espresso_products->map($row->get_product_id());

		$last_chars 			= substr($espresso_products->get_image_link(), strrpos($espresso_products->get_image_link(), '/') + 1);
		$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
		$id_product_prestashop 	= $espresso_products->get_id_product_prestashop();
		$price 					= get_the_price_espresso($espresso_products->get_id_product());
		$discount 				= get_the_discount_espresso($espresso_products->get_id_product(), $price);
		$colors 				= $attribute->get_colors_of_product($id_product_prestashop);
		$sizes 					= $attribute->get_sizes_of_product($id_product_prestashop);
		$img_url	 			= "http://miracas.miracaslifestyle.netdna-cdn.com/".$id_product_prestashop."-".$id_image."-large/".$id_product_prestashop."_.jpg";
		$img_url 				= $espresso_products->get_image_link();

		$popup_images 			= array();
		$id_images 				= $image->get_images($id_product_prestashop);
		foreach ($id_images as $id_image) {
			$popup_images[] = "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg";
		}

		$tmp['product'] 							= array();
		$tmp['product']['id_product_prestashop'] 	= $id_product_prestashop;
		$tmp['product']['id_product'] 				= $espresso_products->get_id_product();
		$tmp['product']['name'] 					= $espresso_products->get_name();
		$tmp['product']['price'] 					= $price;
		$tmp['product']['discount'] 				= $discount;
		$tmp['product']['colors'] 					= $colors;
		$tmp['product']['sizes'] 					= $sizes;
		$tmp['product']['img_url'] 					= $img_url;
		$tmp['product']['popup_images'] 			= $popup_images;
		$tmp['product']['love_count'] 				= $espresso_products->get_love_count();

		$output[] 	= $tmp;

	}

	echo json_encode($output, JSON_PRETTY_PRINT);
	//echo json_encode($output);

//get_brewed_products.php?user_id=123
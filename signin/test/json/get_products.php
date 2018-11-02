<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/product.php");
	require_once("../classes/product_lang.php");
	require_once("../classes/attribute.php");
	require_once("../classes/image.php");
	require_once("../classes/functions.php");

	//OBJETCS
	$product 		= new product();
	$attribute 		= new attribute();
	$image 			= new image();
	$product_lang 	= new product_lang();

	$gender 		= "";
	if( isset($_GET['gender']) ){
		$gender = $_GET['gender'];
		if($gender != ''){
			$gender  = "/".$gender."/";
		}
	}

	$age 		= "";
	if( isset($_GET['age']) ){
		$age = $_GET['age'];
		if($age != ''){
			$age  = "/".$age."/";
		}
	}

	// GET ALL PRODUCTS
	$products = $product->get_search_limit($_GET['keyword'], $gender, $age, $_GET['start']," order by final_count desc");

	// FORMAT OUTPUT AND BRING IMAGE
	$output = array();
	$cont 	= 0;
	foreach ($products as $row){
		if($product_lang->get_product_active($row->get_id_product_prestashop()) == '0')continue;
		if($cont >= $_GET['start'] && $cont < $_GET['start']+2){
			// limit, it brings 2 products at the time, starting from whenever the GET is set
			$cont++;
		}else{
			$cont++;
			continue;
		}

		$last_chars 			= substr($row->get_image_link(), strrpos($row->get_image_link(), '/') + 1);
		$id_image 				= str_replace(".jpg", "", $last_chars); // get the 75 from this: http://url.com/9-75/75.jpg
		$id_product_prestashop 	= $row->get_id_product_prestashop();
		$price 					= get_the_price($id_product_prestashop);
		$discount 				= get_the_discount($row->get_id_product(), $price);
		$colors 				= $attribute->get_colors_of_product($id_product_prestashop);
		$sizes 					= $attribute->get_sizes_of_product($id_product_prestashop);
		$img_url	 			= "http://miracas.miracaslifestyle.netdna-cdn.com/".$id_product_prestashop."-".$id_image."-large/".$id_product_prestashop."_.jpg";
		$img_url 				= $row->get_image_link();

		$popup_images 			= array();
		$id_images 				= $image->get_images($id_product_prestashop);
		foreach ($id_images as $id_image) {
			$popup_images[] = "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg";
		}

		$tmp 					= array();
		$tmp['id_product'] 		= $id_product_prestashop;
		$tmp['name'] 			= $row->get_name();
		$tmp['price'] 			= $price;
		$tmp['discount'] 		= $discount;
		$tmp['colors'] 			= $colors;
		$tmp['sizes'] 			= $sizes;
		$tmp['img_url'] 		= $img_url;
		$tmp['popup_images'] 	= $popup_images;
		$tmp['love_count'] 		= $row->get_love_count();
		$tmp['last_start_count']= $_GET['start'];

		$output[] 	= $tmp;
	}

	//echo json_encode($output, JSON_PRETTY_PRINT);
	echo json_encode($output);

//get_products.php?keyword=something&gender=male&age=&start=0
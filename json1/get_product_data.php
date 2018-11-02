<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/attribute.php");
	require_once("../classes/product_lang.php");
	require_once("../classes/image.php");
		require_once("../classes/functions.php");


	//OBJETCS
	$attribute 		= new attribute();
	$product_lang 	= new product_lang();
	$image 			= new image();

	$id_product_prestashop = $_GET['id_product'];
	

	// FORMAT OUTPUT AND BRING IMAGE
	$output = array();

	$id_images 	= $image->get_images($id_product_prestashop);
	
	$images 	= [];
	foreach ($id_images as $id_image) {
		$image_url	= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image-home/$id_image.jpg";
		$popup_url 	= "http://miracas.miracaslifestyle.netdna-cdn.com/$id_product_prestashop-$id_image/$id_image.jpg"; 

		$images[] 	= $popup_url;
	}
			$price_db 	= (float)$product_lang->get_product_price($id_product_prestashop);
			$price 		= round( 1.05 * $price_db , 2);
	$output['name']			= $product_lang->get_product_name($id_product_prestashop);
	$output['images']		= $images;
	$output['colors'] 		= $attribute->get_colors_of_product($id_product_prestashop);
	$output['sizes'] 		= $attribute->get_sizes_of_product($id_product_prestashop);
	$output['price']		= $price;
	//echo json_encode($output, JSON_PRETTY_PRINT);
	echo json_encode($output);

//get_product_data.php?id_product=10
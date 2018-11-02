<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/product.php");

	// OBJETCS
	$product 				= new product();
	$id_product_prestashop 	= $_GET['id_product'];
	$qty 					= isset($_GET['qty'])?$_GET['qty']:'1';

	$output = array();

	if($product->increase_love_count($id_product_prestashop, $qty)){
		//echo 'success';
		$output['user_status'] 	= '1';
		$output['message'] 		= 'Success';
	}else{
		//echo 'error';
		$output['user_status'] 	= '0';
		$output['message'] 		= 'Something wrong';
	}

	echo json_encode($output);

// post_love_count.php?id_product=10&qty=5
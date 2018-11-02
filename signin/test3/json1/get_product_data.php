<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/attribute.php");

	//OBJETCS
	$attribute 		= new attribute();

	$id_product_prestashop = $_GET['id_product'];
	

	// FORMAT OUTPUT AND BRING IMAGE
	$output = array();
	
	$output['colors'] 	= $attribute->get_colors_of_product($id_product_prestashop);
	$output['sizes'] 	= $attribute->get_sizes_of_product($id_product_prestashop);

	//echo json_encode($output, JSON_PRETTY_PRINT);
	echo json_encode($output);

//get_product_data.php?id_product=10
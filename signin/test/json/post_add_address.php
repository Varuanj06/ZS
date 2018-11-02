<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/address.php");

	// OBJETCS
	$address 				= new address();
	$id_fb_user 			= $_GET['fb_user'];
	$id_address 			= $address->max_id_address($id_fb_user);

	$address->set_id_fb_user($id_fb_user);
	$address->set_id_address($id_address);
	$address->set_name($_GET['name']);
	$address->set_mobile_number($_GET['mobile_number']);
	$address->set_address(str_replace("@", "#", $_GET['address']));
	$address->set_landmark($_GET['landmark']);
	$address->set_city($_GET['city']);
	$address->set_state($_GET['state']);
	$address->set_pin_code($_GET['pin_code']);
	$address->set_email($_GET['email']);

	$output = array();

	if(!$address->insert()){
		//echo 'error';
		$output['user_status'] 	= '0';
		$output['message'] 		= 'Something wrong';
	}else{
		//echo 'success';
		$output['user_status'] 	= '1';
		$output['message'] 		= 'Success';
	}

	echo json_encode($output);

// post_add_address.php?fb_user=10152767632557633&name=Dave Jordy&mobile_number=123456789&address=Street Av @123&landmark=LANDMARK&city=Houston&state=Texas&pin_code=54321&email=david@david.com
<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/address.php");

	// OBJETCS
	$address 				= new address();
	$id_fb_user 			= $_GET['fb_user'];
	$id_address 			= $_GET['id_address'];

	$address->map($id_address, $id_fb_user);

	if(isset($_GET['name'])){
		$address->set_name($_GET['name']);
	}
	if(isset($_GET['mobile_number'])){	
		$address->set_mobile_number($_GET['mobile_number']);
	}
	if(isset($_GET['address'])){	
		$address->set_address(str_replace("@", "#", $_GET['address']));
	}
	if(isset($_GET['landmark'])){	
		$address->set_landmark($_GET['landmark']);
	}
	if(isset($_GET['city'])){	
		$address->set_city($_GET['city']);
	}
	if(isset($_GET['state'])){	
		$address->set_state($_GET['state']);
	}
	if(isset($_GET['pin_code'])){	
		$address->set_pin_code($_GET['pin_code']);
	}
	if(isset($_GET['email'])){	
		$address->set_email($_GET['email']);
	}

	$output = array();

	if(!$address->update()){
		//echo 'error';
		$output['user_status'] 	= '0';
		$output['message'] 		= 'Something wrong';
	}else{
		//echo 'success';
		$output['user_status'] 	= '1';
		$output['message'] 		= 'Success';
	}

	echo json_encode($output);

// post_update_address.php?fb_user=10152767632557633&id_address=1&name=Dave Jordy&mobile_number=123456789&address=Street Av @123&landmark=LANDMARK&city=Houston&state=Texas&pin_code=54321&email=david@david.com
<?php
	
	require_once("../dbconnect.php");    
	require_once("../classes/address.php");

	// OBJETCS
	$address 		= new address();

	// VARS
	$id_fb_user 	= $_GET['fb_user'];
	$addresses 		= $address->get_list($id_fb_user, " order by date_update desc ");

	$output 		= array();
	foreach ($addresses as $row){ 
		$tmp 					= array();
		$tmp['name']			= $row->get_name();
		$tmp['mobile_number'] 	= $row->get_mobile_number();
		$tmp['address'] 		= $row->get_address();
		$tmp['landmark'] 		= $row->get_landmark();
		$tmp['city'] 			= $row->get_city();
		$tmp['state'] 			= $row->get_state();
		$tmp['pin_code'] 		= $row->get_pin_code();
		$tmp['email'] 			= $row->get_email();

		$output[$row->get_id_address()] = $tmp;

		break;
	}

	//echo json_encode($output, JSON_PRETTY_PRINT);
	echo json_encode($output);

// get_latest_address_from_user.php?fb_user=10152767632557633
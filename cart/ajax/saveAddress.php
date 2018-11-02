<?php require_once("../../fb_validator.php"); ?>
<?php require_once("../../session_check.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/address.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../functions.php"); ?>
<?php require_once("getAll_data.php"); ?>

<?php 

/* ====================================================================== *
        GLOBAL VARS
 * ====================================================================== */       	

	$id_fb_user 	= $user['id'];
	$id_order 		= get_id_order($id_fb_user);
	$details 		= get_order_details($id_order);

	$POST 			= (array)json_decode(trim(file_get_contents('php://input')));

	if($id_order == '-1'){
		$output = array();
		$output['error'] = true;
		echo json_encode($output);
		exit();
	}

/* ====================================================================== *
        CLASSES
 * ====================================================================== */      

 	$address 			= new address();
 	$order_detail 		= new order_detail();

/* ====================================================================== *
        DELETE ESPRESSO PRODUCTSO (this will not longer be needed)
 * ====================================================================== */      

 	//$order_detail->delete_espresso_products($id_order);

 /* ====================================================================== *
        INSERT ADDRESS
 * ====================================================================== */       		

	$error = false;
	$conn->beginTransaction();

	$id_address 		= "";

	$address->set_id_fb_user($id_fb_user);
	$address->set_name($POST['address']->name);
	$address->set_mobile_number($POST['address']->mobile_number);
	$address->set_address($POST['address']->address);
	$address->set_landmark($POST['address']->landmark);
	$address->set_city($POST['address']->city);
	$address->set_state($POST['address']->state);
	$address->set_pin_code($POST['address']->pin_code);
	$address->set_email(str_replace(' ', '', $POST['address']->email));

	if($POST['address']->id_address == 'new'){
		$id_address = $address->max_id_address($id_fb_user);
		$address->set_id_address($id_address);

		if(!$address->insert()){
			$error = true;
		}
	}else{
		$address->set_id_address($POST['address']->id_address);
		if(!$address->update()){
			$error = true;
		}
	}

	if($error){
      $conn->rollBack();
	}else{
      $conn->commit();
	}

/* ====================================================================== *
        JSON OUTPUT
 * ====================================================================== */       	

    $output = array();

    set_all($output);
    
    $output['error'] = $error;

	echo json_encode($output);

?>

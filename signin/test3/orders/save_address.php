<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order_address.php"); ?>

<?php 
	
	$order_address = new order_address;

	$order_address->set_name($_POST['name']);
	$order_address->set_mobile_number($_POST['mobile_number']);
	$order_address->set_address($_POST['address']);
	$order_address->set_landmark($_POST['landmark']);
	$order_address->set_city($_POST['city']);
	$order_address->set_state($_POST['state']);
	$order_address->set_pin_code($_POST['pin_code']);
	$order_address->set_email(str_replace(' ', '', $_POST['email']));
	$order_address->set_id_fb_user($_POST['id_fb_user']);
	$order_address->set_id_order_address($_POST['id_order_address']);

	if(!$order_address->update()){
		echo 'ERROR';
	}
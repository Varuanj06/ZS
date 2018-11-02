<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>

<?php 

	$id_fb_user 		= $user['id'];
	$order 				= new order();
	$order_detail 		= new order_detail();
	
	$id_order_detail    = $_GET['id_order_detail'];
	$qty    			= $_GET['qty'];
	$current_id_order   = "";
	if($order->get_id_order_by_fb_user($id_fb_user)){
		$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
	}

	if($current_id_order != ""){
		$order_detail->set_id_order($current_id_order);
		$order_detail->set_id_order_detail($id_order_detail);
		$order_detail->set_qty($qty);

		$order_detail->update_qty();
	}
<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>
<?php require_once("../../classes/fb_user_blacklist.php"); ?>

<?php 

	$order 				= new order();
	$message 			= new message();
	$order_address 		= new order_address();
	$fb_user_blacklist 	= new fb_user_blacklist();

	$status_admin 	= $_GET['status'];
	$id_order 		= $_GET['id_order'];

	$order->map($id_order);

	$conn->beginTransaction(); //transaccion
	$error = false;

	if(!$order->update_status_admin($status_admin, $id_order)){
		$error = true;
	}

	if($status_admin == 'ORDER REFUSED'){
		$fb_user_blacklist->set_id_fb_user($order->get_id_fb_user());
		if(!$fb_user_blacklist->exists()){
			if(!$fb_user_blacklist->insert()){
				$error = true;
			}
		}
	}

	// ##### SEND AUTO MESSAGE #####
	if(!$message->send_auto_message($order, $id_order, $status_admin)){
		$error = true;
	}
	$message->send_SMS_by_order($order, $id_order, $order_address);
	// ##### END SEND AUTO MESSAGE #####

	if($error == false){
		$conn->commit();
	}else{
		echo 'ERROR';
		$conn->rollback();
	}
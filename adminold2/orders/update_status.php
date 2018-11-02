<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>

<?php 

	$order 			= new order();
	$status_admin 	= $_GET['status'];
	$id_order 		= $_GET['id_order'];

	if(!$order->update_status_admin($status_admin, $id_order)){
		echo "ERROR";
	}
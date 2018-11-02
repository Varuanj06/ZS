
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product.php"); ?>

<?php 

	$order 			= new order();
	$order_detail 	= new order_detail();

	
	$id_fb_user 			= $_GET['fb_user'];
	$current_id_order 		= $order->get_id_order_by_fb_user($id_fb_user);
	
	$order_detail->set_id_order($current_id_order);
	$order_detail->set_id_order_detail($_GET['id_order_detail']);

	$output = array();

	if(!$order_detail->delete()){
		//echo "error";
		$output['user_status'] 	= '0';
		$output['message'] 		= 'Something wrong';
	}else{
		//echo 'success';
		$output['user_status'] 	= '1';
		$output['message'] 		= 'Success';
	}

	echo json_encode($output);

// post_remove_product_from_cart.php?fb_user=10152767632557633&id_order_detail=1





<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product.php"); ?>

<?php 

	$error = false;
	$conn->beginTransaction();

	$product 		= new product();
	$order 			= new order();
	$order_detail 	= new order_detail();

	
	$id_fb_user 			= $_GET['fb_user'];
	$color 					= str_replace("@", "#", $_GET['color']);
	$size 					= $_GET['size'];
	$qty 					= $_GET['qty'];
	$id_product_prestashop 	= $_GET['id_product'];
	$id_product 			= $product->get_id_product_from_id_product_prestashop($id_product_prestashop);
	$id_order 				= "";
	
	// ORDER ======>
	if(!$order->exists($id_fb_user)){
		$id_order = $order->max_id_order();

		$order->set_id_order($id_order);
		$order->set_id_fb_user($id_fb_user);
		if(!$order->insert()){
			$error = true;
		}
	}else{
		if($order->get_id_order_by_fb_user($id_fb_user)){
			$id_order = $order->get_id_order_by_fb_user($id_fb_user);
		}else{
			$error = true;
		}
	}

	if(!$order->update_order($id_order)){
		$error = true;
	}


	// ADD DETAILS TO THE ORDER ======>
	$order_detail->set_id_order($id_order);
	$order_detail->set_id_order_detail($order_detail->max_id_order_detail($id_order));
	$order_detail->set_id_product($id_product);
	$order_detail->set_id_product_prestashop($id_product_prestashop);
	$order_detail->set_color($color);
	$order_detail->set_size($size);
	$order_detail->set_qty($qty);

	if($order_detail->exists()){
		$order_detail->set_id_order_detail($order_detail->exists());
		if(!$order_detail->update_qty_using_current()){
			$error = true;
		}
	}else{
		if(!$order_detail->insert()){
			$error = true;
		}
	}

	$output = array();

	if($error){
      	$conn->rollBack();
      	//echo "error";
      	$output['user_status'] 	= '0';
		$output['message'] 		= 'Something wrong';
	}else{
      	$conn->commit();
      	//echo 'success';
      	$output['user_status'] 	= '1';
		$output['message'] 		= 'Success';
	}

	echo json_encode($output);

// post_product_to_cart.php?fb_user=10152767632557633&id_product=10&color=@B81D1D&size=Extra Small&qty=2




<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product_stock.php"); ?>

<?php 

	$error = false;
	$conn->beginTransaction();



	
	$id_fb_user 			= $user['id'];
	$color 					= urldecode($_GET['color']);
	$size 					= $_GET['size'];
	$qty 					= $_GET['qty'];
	$id_product 			= $_GET['id_product'];
	$id_product_prestashop 	= $_GET['id_product_prestashop'];
	$id_order 				= "";

	$order 			= new order();
	$order_detail 	= new order_detail();
	$product_stock 	= new product_stock();

	// IF ITS A LUCKY SIZE PRODUCT, THEN CHECK THE STOCK ======>
	$list_stock 			= $product_stock->get_list_stock($id_product, '');
	if(count($list_stock)>0){
		$stock_found = false;
		foreach ($list_stock as $current_stock) {
			if($current_stock->get_color()=='Empty')$current_stock->set_color("");
			if($current_stock->get_size()=='Empty')$current_stock->set_size("");

			if($current_stock->get_color() == $color && $current_stock->get_size() == $size && $current_stock->get_stock() > 0){
				$stock_found = true;
			}
		}

		if($stock_found === false){
			$error = true;
		}
	}

	
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




	if($error){
      $conn->rollBack();
      echo "ERROR";
	}else{
      $conn->commit();
	}
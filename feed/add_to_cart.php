<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product_stock.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword_discount.php"); ?>
<?php require_once("../classes/keyword_discount_code.php"); ?>

<?php 

	$error = false;
	$conn->beginTransaction();



	$espresso_product 		= $_GET['espresso_product'];
	$id_fb_user 			= $user['id'];
	$color 					= urldecode($_GET['color']);
	$size 					= $_GET['size'];
	$qty 					= $_GET['qty'];
	$id_product 			= $_GET['id_product'];
	$id_product_prestashop 	= $_GET['id_product_prestashop'];
	$with_keyword_discount 	= isset($_GET['with_keyword_discount']) ? $_GET['with_keyword_discount'] : 'no';
	$keyword_code 			= isset($_GET['keyword_code']) ? $_GET['keyword_code'] : '';
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
		//$id_order = $order->max_id_order();

		$order->set_id_order(null);
		$order->set_id_fb_user($id_fb_user);
		if(!$order->insert()){
			$error = true;
		}
	}

	if($order->get_id_order_by_fb_user($id_fb_user)){
		$id_order = $order->get_id_order_by_fb_user($id_fb_user);
	}else{
		$error = true;
	}

	if(!$order->update_order($id_order)){
		$error = true;
	}

	//CHECK KEYWORD CODE
	$invalid_code = false;
	if($keyword_code != ''){
		$invalid_code 	= true;
		$product 		= new product();
		$product->map($id_product);
		$product_keyword = str_replace("/", "", $product->get_keywords());

		if($product->get_keyword_discount($product_keyword) != ''){
			$keyword_discount = new keyword_discount();
			$keyword_discount->map_active_by_keyword($product_keyword);

			$keyword_discount_code = new keyword_discount_code();
			if( $keyword_discount_code->exists_with_id_keyword_discount($keyword_code, $keyword_discount->get_id_keyword_discount()) ){
				$invalid_code = false;
			}
		}
	}

	// ADD DETAILS TO THE ORDER ======>
	$order_detail->set_id_order($id_order);
	$order_detail->set_id_order_detail($order_detail->max_id_order_detail($id_order));
	$order_detail->set_id_product($id_product);
	$order_detail->set_id_product_prestashop($id_product_prestashop);
	$order_detail->set_color($color);
	$order_detail->set_size($size);
	$order_detail->set_qty($qty);
	$order_detail->set_order_type($espresso_product=='yes' ? 'espresso' : null);
	$order_detail->set_with_keyword_discount($with_keyword_discount);
	$order_detail->set_keyword_discount_code( !$invalid_code ? $keyword_code : '' );

	if( ($espresso_product=='yes' && $order_detail->exists_with_espresso_and_with_keyword_discount()) || ($espresso_product=='no' && $order_detail->exists_with_keyword_discount()) ){
		$order_detail->set_id_order_detail($order_detail->exists());
		if(!$order_detail->update_qty_using_current()){
			$error = true;
		}
	}else{
		if(!$order_detail->insert()){
			$error = true;
		}
	}

	if($invalid_code){
		$conn->rollBack();
      	echo "INVALID_CODE";
	}else if($error){
      $conn->rollBack();
      echo "ERROR";
	}else{
      $conn->commit();
	}
<?php require_once("../../classes/vendor_product.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/espresso_products.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>

<?php		

	// MAKE REPORT!

	function exists_already($item, $array){
		for ($i=0; $i<count($array); $i++) { 
			if( $array[$i]['item'] == $item ){
				return $i;
				break;
			}
		}
	}

	function get_assian_size($size_difference, $size){
		$assian_sizes = array( "Extra Small", "Small", "Medium", "Large", "Extra Large", "2XL", "3XL", "4XL", "5XL", "5XL", "5XL", "5XL");

		if($size_difference == '0'){
			return $size;
		}else if($size_difference == '1+'){
			return $assian_sizes[array_search($size, $assian_sizes)+1];
		}else if($size_difference == '2+'){
			return $assian_sizes[array_search($size, $assian_sizes)+2];
		}else if($size_difference == '3+'){
			return $assian_sizes[array_search($size, $assian_sizes)+3];
		}else if($size_difference == '4+'){
			return $assian_sizes[array_search($size, $assian_sizes)+4];
		}else if($size_difference == '1+S2+'){
			if($size == 'Small'){
				return $assian_sizes[array_search($size, $assian_sizes)+2];
			}else{
				return $assian_sizes[array_search($size, $assian_sizes)+1];
			}
		}else if($size_difference == '1+S3+'){
			if($size == 'Small'){
				return $assian_sizes[array_search($size, $assian_sizes)+3];
			}else{
				return $assian_sizes[array_search($size, $assian_sizes)+1];
			}
		}else if($size_difference == '2+S3+'){
			if($size == 'Small'){
				return $assian_sizes[array_search($size, $assian_sizes)+3];
			}else{
				return $assian_sizes[array_search($size, $assian_sizes)+2];
			}
		}else{
			return $size_difference;
		}
	}

	function make_purchase_order($id_vendor, $POST){

		//print_r($POST);

		$from_date 				= $POST['from_date']; 
		$till_date				= $POST['till_date']; 

		$paymentSQL				= "";
		if( isset($_POST['payment1']) ){
			$paymentSQL .= " (payment_method = '".$_POST['payment1']."' and free_order!='yes') or";
		} 
		if( isset($_POST['payment2']) ){
			$paymentSQL .= " (payment_method = '".$_POST['payment2']."' or free_order='yes' ) or";
		}
		if($paymentSQL != ""){
			$paymentSQL = " and (". substr($paymentSQL, 0, -2) . ") ";
		}

		$final_result = array();

		/* Bring products in current vendor */
		$vendor_product 	= new vendor_product();
		$products_in_vendor = $vendor_product->get_all_from_vendor($id_vendor, "ORDER BY 1");

		/* Bring orders between the dates specified */
		$order 	= new order();
		$list 	= $order->get_orders_between_dates( $from_date, $till_date, $paymentSQL." and (status_admin in('ORDER PLACED', 'PROCESSING ORDER', 'ORDER PARTIALLY SHIPPED') or status_admin is null or status_admin = '') order by 1" );

		/* Loop the orders */
		$id_orders = "";
		foreach ($list as $order) {
			$id_orders .= "'".$order->get_id_order()."',";
		}
		if(strlen($id_orders)>0){ 
			$id_orders = substr($id_orders, 0, -1); 
		}else{
			$id_orders = "'#### nothing at all ####'";
		}


		/* Bring the products of all orders between the dates specified */
		$order_detail = new order_detail();
		$list_details = $order_detail->get_all_from_orders($id_orders, " order by 1 ");

		/* Loop the details */
		foreach ($list_details as $detail) {
			
			/* loop the producs from the current vendor */
			foreach ($products_in_vendor as $vendor_product) {
				if(
					$vendor_product->get_id_product_lang() == $detail->get_id_product_prestashop() &&
					$detail->get_sent() != 'yes' &&
					$detail->get_refunded() != 'yes' &&
					$detail->get_POmade() != 'yes'
				){
					$tmp = array();

					$order->map($detail->get_id_order());

					$name 		= '';
					if($detail->get_order_type() == 'espresso'){
						$espresso_products	= new espresso_products();
						$espresso_products->map($detail->get_id_product());

						$name 				= $espresso_products->get_name();
					}else{
						$product = new product();
						$product->map($detail->get_id_product());

						$name 				= $product->get_name();
					}

					$product = new product();
					$product->map($detail->get_id_product());

					$tmp['id_product_lang'] = $vendor_product->get_id_product_lang();
					$tmp['id_product'] 		= $vendor_product->get_id_product();
					$tmp['id_order']		= "-".$detail->get_id_order()."-";
					$tmp['payment']			= "-".$order->get_payment_method()."-";
					$tmp['qty'] 			= $detail->get_qty();
					$tmp['thumb']			= $vendor_product->get_image_url();
					$tmp['link'] 			= $vendor_product->get_product_link();
					$tmp['item']			= $vendor_product->get_id_product_lang().",".str_replace('#', '', $detail->get_color()).",".$detail->get_size();
					$tmp['name'] 			= $name;
					$tmp['color']   		= $detail->get_color();
					$tmp['size']   			= $detail->get_size();
					$tmp['asian_size']		= get_assian_size($vendor_product->get_size_difference(), $tmp['size']);

					$order_and_detail 		= $detail->get_id_order()."@@".$detail->get_id_order_detail();
					$tmp['id_order_details']= "-$order_and_detail-";
					
					/* if the item already exists then group the quantity, don't add a new record */
					$exists = exists_already($tmp['item'], $final_result);
					if(isset($exists)){
						$final_result[$exists]['qty']				= $final_result[$exists]['qty'] + $tmp['qty'];
						$final_result[$exists]['id_order_details'] 	= $final_result[$exists]['id_order_details']."$order_and_detail-";

						if( strpos( $final_result[$exists]['id_order'], "-".$tmp['id_order']."-") === false ){
							
							// Add id orders separated by comas
							$final_result[$exists]['id_order'] = $final_result[$exists]['id_order'] . $detail->get_id_order()."-";

							// Add payments separated by comas
							$final_result[$exists]['payment'] = $final_result[$exists]['payment'] . $order->get_payment_method()."-";

						}

					}else{
						$final_result[] = $tmp;
					}

					break;
				}
			}

		}

		
		for ($i=0; $i < count($final_result); $i++) { 

			// Fix id_orders
			$id_order_trim 		= trim($final_result[$i]['id_order'], "-");
			$final_result[$i]['id_order']	= str_replace("-",", ", $id_order_trim);

			// Fix payments
			$payment_trim 		= trim($final_result[$i]['payment'], "-");
			$final_result[$i]['payment']	= str_replace("-",", ", $payment_trim);

		}

		return $final_result;
	}
?>
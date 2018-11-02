<?php require_once("../../classes/address.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/espresso_products.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>
<?php require_once("../../classes/voucher.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_voucher.php"); ?>
<?php require_once("../../classes/fb_user_blacklist.php"); ?>
<?php require_once("../functions.php"); ?>

<?php 

function set_all(&$output){

	global $user;
	global $cod_lowerlimit_man;
	global $cod_upperlimit_man;
	global $cod_lowerlimit_woman;
	global $cod_upperlimit_woman;
	global $shipping_fee;
  	global $cod_fee;

/* ====================================================================== *
        GLOBAL VARS
 * ====================================================================== */       	

	$id_fb_user 		= $user['id'];
	$user_gender 		= isset($user['gender'])?$user['gender']:'';
	$id_order 			= get_id_order($id_fb_user);
	$details 			= get_order_details($id_order);
	$group_order 		= false;
	$output 			= ['productList' => array(), 'addressList' => array(), 'voucherList' => array(), 'totalAmount' => 0, 'totalVouchers' => 0, 'total' => 0, 'manager' => '', 'paymentList' => array()];

/* ====================================================================== *
        CLASSES
 * ====================================================================== */      

 	$address 			= new address();		
 	$voucher 			= new voucher();
	$order_voucher 		= new order_voucher();
	$product			= new product();
	$espresso_products	= new espresso_products();
	$order				= new order();
	$fb_user_blacklist 	= new fb_user_blacklist();

	$order->map($id_order);
	$fb_user_blacklist->set_id_fb_user($id_fb_user);

/* ====================================================================== *
        INIT
 * ====================================================================== */      

	$address 					= get_address($id_fb_user);
	$output['totalAmount'] 		= get_total_amount($details);
	$output['totalVouchers'] 	= get_total_vouchers($id_order, $output['totalAmount']);
	$output['total'] 			= $output['totalAmount'] - $output['totalVouchers'];
	$output['shipping_fee'] 	= $shipping_fee;
	$output['cod_fee'] 			= $cod_fee;

/* ====================================================================== *
        GET CART DETAILS
 * ====================================================================== */       	

	foreach ($details as $row) {

		// CREATE NEW ROW

		$new_row 		= [];

		$new_row['id_order_detail'] 		= $row->get_id_order_detail();
		$new_row['id_product'] 				= $row->get_id_product();
		$new_row['color'] 					= $row->get_color();
		$new_row['size'] 					= $row->get_size();
		$new_row['qty'] 					= $row->get_qty();
		$new_row['order_type'] 				= $row->get_order_type();
		$new_row['with_keyword_discount']	= $row->get_with_keyword_discount();

		if($row->get_order_type() == 'espresso'){

			$espresso_products->map($row->get_id_product());

			$price 							= get_the_price_espresso($row->get_id_product());
			$discount 						= get_the_discount_espresso($row->get_id_product(), $price);
			$price_final 					= ((float)$price-(float)$discount)*(float)$row->get_qty();

			$new_row['price'] 				= $price_final;
			$new_row['name'] 				= $espresso_products->get_name();;
			$new_row['img'] 				= $espresso_products->get_image_link();

		}else{

			$product		= new product();
			$product->map($row->get_id_product());

			$price 			= (float) get_the_price($row->get_id_product());
			$discount 		= (float) get_the_discount($row->get_id_product(), $price);

			if($row->get_with_keyword_discount() == 'yes'){
				$discount 		= (float) get_the_keyword_discount($row->get_id_product(), $price);
				$group_order 	= true;
			}

			$price_final 	= ($price-$discount) * ( (float)$row->get_qty() );

			$new_row['price'] 				= $price_final;
			$new_row['name'] 				= $product->get_name();
			$new_row['img'] 				= $product->get_image_link();

		}
		

		$output['productList'][] 		= $new_row;
	}

/* ====================================================================== *
        DELETE VOUCHERS FROM ORDER IF ITS A GROUP ORDER
 * ====================================================================== */       		

	if($group_order){
		if(!$order_voucher->delete_all_from_order($id_order)){
			$error = true;
		}
	}

	$output['group_order'] 				= $group_order;

/* ====================================================================== *
        ADDRESS
 * ====================================================================== */       		

    $addressList 		= $address->get_list($id_fb_user, " order by date_update desc ");

	foreach ($addressList as $row){
		$new_row 		= [];

		$new_row['id_address'] 			= $row->get_id_address();
		$new_row['all_address'] 		= $row->get_name().", ".$row->get_address().", ".$row->get_city().", ".$row->get_state();
		$new_row['name'] 				= $row->get_name();
		$new_row['mobile_number'] 		= $row->get_mobile_number();
		$new_row['address'] 			= $row->get_address();
		$new_row['landmark'] 			= $row->get_landmark();
		$new_row['city'] 				= $row->get_city();
		$new_row['state'] 				= $row->get_state();
		$new_row['pin_code'] 			= $row->get_pin_code();
		$new_row['email'] 				= $row->get_email();

		$output['addressList'][] 		= $new_row;
	}


/* ====================================================================== *
        VOUCHERS
 * ====================================================================== */       		

	$voucherList 	= $voucher->get_all_for_user($address->get_email(), $id_order, "order by till_date");

	foreach ($voucherList as $row) { 
		if( $order_voucher->exists($id_order, $row->get_id_voucher()) ){
			if($row->get_min_cart_value() != 0 && $output['totalAmount'] < $row->get_min_cart_value()){
				$order_voucher->set_id_order($id_order);
				$order_voucher->set_id_voucher($row->get_id_voucher());
				$order_voucher->delete();
			}
		}

		$new_row 		= [];

		$new_row['id_voucher'] 			= $row->get_id_voucher();
		$new_row['available'] 			= ( $row->get_min_cart_value() != 0 && $output['totalAmount'] < $row->get_min_cart_value() ) ? false : true;
		$new_row['value_kind'] 			= $row->get_value_kind();
		$new_row['value'] 				= $row->get_value();
		$new_row['code'] 				= $row->get_code();
		$new_row['exists_in_order'] 	= $order_voucher->exists($id_order, $row->get_id_voucher()) ? true : false;
		$new_row['checked'] 			= $order_voucher->exists($id_order, $row->get_id_voucher()) ? true : false;
		$new_row['visibility'] 			= $row->get_visibility() == 'Y' ? true : false;
		$new_row['description'] 		= $row->get_description();

		$output['voucherList'][] 		= $new_row;
	}

/* ====================================================================== *
        MANAGER
 * ====================================================================== */       			

    global $relationship_managers;
    global $relationship_managers_group_orders;

    if($group_order){
    	$relationship_managers = $relationship_managers_group_orders;
    }

	foreach ($relationship_managers as $row_manager) {
		$explode = explode("-", $row_manager['range']);
		if($output['total'] >= $explode[0] && $output['total'] <= $explode[1]){

			$output['manager']['img_src'] 	= $row_manager['img_src'];
			$output['manager']['name'] 		= $row_manager['name'];
			$output['manager']['email'] 	= $row_manager['email'];
			break;

		}
	}

/* ====================================================================== *
        PAY
 * ====================================================================== */       				

   	global $COD_keywords;

    $found_in_keywords 	= "no";
	for ($i=0; $i < count($COD_keywords); $i++) { 
		$COD_keywords[$i] = "/".$COD_keywords[$i]."/";
	}
	foreach ($details as $row){
		$product->map($row->get_id_product());
		if (in_array($product->get_keywords(), $COD_keywords)) {
			$found_in_keywords = "yes";
		}
	}    

	$current_payment_method = '';

	/* Cash on delivery */

	$lower_limit 	= $user_gender=='female' ? $cod_lowerlimit_woman : $cod_lowerlimit_man;
	$upper_limit 	= $user_gender=='female' ? $cod_upperlimit_woman : $cod_upperlimit_man;

	$orders_placed 	= $order->get_list_per_user($id_fb_user, " and status_admin = '' order by date_done desc ");

	if(!$group_order){ // if its a group order, then COD shouldn't be visible
		if(count($orders_placed) == 0){ // if he already has an order with status "ORDER PLACED" then the COD shouldn't be visible
			if($output['total'] == 0 || $output['totalVouchers'] <= 0){ // COD only if the total is zero or if the customer hasn't apply any voucher
			  	
			  	if(
			  		$output['total'] <= $upper_limit && $output['total'] >= $lower_limit || 
			  		$output['total'] == 0 || $found_in_keywords == 'yes'
			  	){
			  		if(!$fb_user_blacklist->exists()){
				  		$new_row 					= [];
				  		$new_row['payment_method'] 	= 'Cash on Delivery';
				  		$new_row['name'] 			= 'Cash on Delivery';
				  		//$new_row['active'] 			= $order->get_payment_method() == 'Cash on Delivery' ? true : false;
				  		$output['paymentList'][] 	= $new_row;

				  		if($order->get_payment_method() == 'Cash on Delivery'){
				  			$current_payment_method = 'Cash on Delivery';
				  		}
				  	}
			  	}

			}
	  	}
  	}

  	if($output['total'] > 0){

	  	/* Pay online */

	  	if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { 
	  		$new_row 					= [];
	  		$new_row['payment_method'] 	= 'EBS';
	  		$new_row['name'] 			= 'Pay Online';
	  		//$new_row['active'] 			= ($order->get_payment_method() == 'Pay Online' && ($order->get_online_payment() == 'EBS' || $order->get_online_payment() == '')) ? true : false;
	  		$output['paymentList'][] 	= $new_row;

	  		if($order->get_payment_method() == 'Pay Online' && ($order->get_online_payment() == 'EBS' || $order->get_online_payment() == '')){
	  			$current_payment_method = 'EBS';
	  		}
	  	}

	  	/* Pay online Using Debit/Credit Card Or Net Banking */

		$new_row 					= [];
		$new_row['payment_method'] 	= 'PAYTM';
		$new_row['name'] 			= '<img src="http://miracas.com/ZS/assets/paytm.png"> <span>Pay Online Using PAYTM</span>';
		//$new_row['active'] 			= ($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYTM') ? true : false;
		$output['paymentList'][] 	= $new_row;

		if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYTM'){
  			$current_payment_method = 'PAYTM';
  		}

	  	/* Pay online Using Debit/Credit Card Or Net Banking */

	//	$new_row 					= [];
	//	$new_row['payment_method'] 	= 'RAZORPAY';
	//	$new_row['name'] 			= 'Pay Online Using Razor Pay';
		//$new_row['active'] 			= ($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'RAZORPAY') ? true : false;
	//	$output['paymentList'][] 	= $new_row;

	//	if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'RAZORPAY'){
  	//		$current_payment_method = 'RAZORPAY';
  	//	}

		/* Pay Online Using PayU */

		$new_row 					= [];
		$new_row['payment_method'] 	= 'PAYU';
		$new_row['name'] 			= 'Pay Online Using PayU';
		//$new_row['active'] 			= $order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYU' ? true : false;
		$output['paymentList'][] 	= $new_row;

		if($order->get_payment_method() == 'Pay Online' && $order->get_online_payment() == 'PAYU'){
  			$current_payment_method = 'PAYU';
  		}

	}  

	/* Add current payment method */

  	$output['current_payment_method'] = $current_payment_method;

}
	
?>

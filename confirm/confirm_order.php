<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/functions_espresso.php"); ?>
<?php require_once("../classes/order_voucher.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/product_stock.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/keyword.php"); ?>
<?php require_once("../classes/keyword_discount.php"); ?>
<?php require_once("../classes/keyword_discount_code.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../classes/message.php"); ?>

<?php 

	function confirm_order($id_fb_user, $current_id_order, $order_details, $current_address, $payed){

		global $conn;

		$error = false;
		$conn->beginTransaction();

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */

		$order 					= new order();
		$order_voucher 			= new order_voucher();
		$order_detail 			= new order_detail();
		$address 				= new address();
		$order_address 			= new order_address();
		$message 				= new message();
		$product_stock 			= new product_stock();
		$keyword_discount_code 	= new keyword_discount_code();

	/* ====================================================================== *
        	SAVE ORDER TOTAL
 	 * ====================================================================== */	

 		/* GET AMOUNT AND DISCOUNT */

		$total_ammount 	= 0;
		$total_discount = 0;
		foreach ($order_details as $row) {
			$qty 			= $row->get_qty();
			$price 			= 0;
			$discount 		= 0;

			if($row->get_order_type() == 'espresso'){
				$price 			= get_the_price_espresso($row->get_id_product());
				$discount 		= get_the_discount_espresso($row->get_id_product(), $price);
			}else{
				$price 			= get_the_price($row->get_id_product());
				$discount 		= get_the_discount($row->get_id_product(), $price);	

				if($row->get_with_keyword_discount() == 'yes'){
					$discount = get_the_keyword_discount($row->get_id_product(), $price);
				}
			}

			$total_ammount 	+= (float)$price*(float)$qty;
			$total_discount += (float)$discount*(float)$qty;

			$order_detail->update_amount_and_discount($row->get_id_order(), $row->get_id_order_detail(), $price, $discount);
		}

		/* GET VOUCHER (THE VOUCHER IS LIKE A COUPON THAT APPLIES TO THE ORDER IN GENERAL) */

		$vouchers_discount 	= $order_voucher->get_vouchers_discount($current_id_order, $total_ammount-$total_discount);

		/* SAVE THE TOTALS OF THE ORDR */

		if(!$order->update_totals($total_ammount, $total_discount, $vouchers_discount, $current_id_order)){
			$error = true;
		}

		/* IF THE FINAL AMOUNT IS 0 THEN IT IS A "FREE ORDER" */

		if(($total_ammount-$total_discount-$vouchers_discount) == 0){
			if(!$order->update_free_order("yes", $current_id_order)){
				$error = true;
			}
		}

	/* ====================================================================== *
        	UPDATE PAYED (I THINK THIS IS ONLY USED IF ITS AN ONLINE PAYMENT, BUT I COUDN'T FIND IF THIS IS USED SOMEWHERE)
 	 * ====================================================================== */		

 		if($payed){
			if(!$order->update_payed('yes', $current_id_order)){
				$error = true;
			}
		}

	/* ====================================================================== *
        	UPDATE STOCK
 	 * ====================================================================== */			

		foreach ($order_details as $row) {
			$color 			= $row->get_color() == '' ? 'Empty' : $row->get_color();
			$size 			= $row->get_size() == '' ? 'Empty' : $row->get_size();

			$product_stock->update_stock($row->get_id_product(), $color, $size, $row->get_qty());
		}

	/* ====================================================================== *
        	SAVE THE ADDRESS IN A NEW TABLE FOR CONFIRMED ORDRERS
 	 * ====================================================================== */		

		$address->map($current_address, $id_fb_user);

		$id_order_address 	= $order_address->max_id_order_address($id_fb_user);
		$order_address->set_id_order_address($id_order_address);
		$order_address->set_id_fb_user($id_fb_user);
		$order_address->set_name($address->get_name());
		$order_address->set_mobile_number($address->get_mobile_number());
		$order_address->set_address($address->get_address());
		$order_address->set_landmark($address->get_landmark());
		$order_address->set_city($address->get_city());
		$order_address->set_state($address->get_state());
		$order_address->set_pin_code($address->get_pin_code());
		$order_address->set_email($address->get_email());

		if(!$order_address->insert()){
			$error = true;
		}

	/* ====================================================================== *
        	FINISH ORDER
 	 * ====================================================================== */			

		$order->set_id_order($current_id_order);
		$order->set_id_order_address($id_order_address);
		if(!$order->update_address()){
			$error = true;
		}
		if(!$order->finish_order()){
			$error = true;
		}

	/* ====================================================================== *
        	SHORT URL
 	 * ====================================================================== */	

		function make_bitly_url($url,$login,$appkey,$format = 'xml',$version = '2.0.1'){
			//create the URL
			$bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
			
			//get the url
			//could also use cURL here
			$response = file_get_contents($bitly);
			
			//parse depending on desired format
			if(strtolower($format) == 'json')
			{
				$json = @json_decode($response,true);
				return $json['results'][$url]['shortUrl'];
			}
			else //xml
			{
				$xml = simplexml_load_string($response);
				return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
			}
		}

	/* ====================================================================== *
        	GENERATE CODES FOR FOLLOWERS ON THE KEYWORD DISCOUNT
 	 * ====================================================================== */	

        $new_keyword_codes 			= array();
        $new_keyword_codes_final 	= array();
        foreach ($order_details as $row) {
        	if($row->get_with_keyword_discount() == 'yes' && $row->get_keyword_discount_code() == ''){

        		$product		= new product();
				$product->map($row->get_id_product());
				$current_keyword = substr($product->get_keywords(), 1, -1);

				if(!in_array($current_keyword, $new_keyword_codes)){
					$new_keyword_codes[] = $current_keyword;
				}

        	}
        }	

        if(count($new_keyword_codes) > 0){
        	$order->update_status_admin('GROUP ORDER CREATED', $current_id_order);
        }else{
        	$order->update_date_placed($current_id_order);
        }

        foreach ($new_keyword_codes as $keyword) {
        	$keyword_discount = new keyword_discount();
			$keyword_discount->map_active_by_keyword($keyword);

        	$keyword_discount_code->set_code($keyword_discount_code->generate_code());
        	$keyword_discount_code->set_id_keyword_discount($keyword_discount->get_id_keyword_discount());
        	$keyword_discount_code->set_keyword($keyword);
        	$keyword_discount_code->set_id_order($current_id_order);
        	$keyword_discount_code->set_keyword_url(make_bitly_url("http://miracas.com/ZS/feed/?q=".$keyword,'o_rn8e90umr','R_2a445759f04b445da4d100412d7cc947','json'));

        	if(!$keyword_discount_code->insert()){
        		$error = true;
        	}

        	$new_obj 				= array();
        	$new_obj['keyword'] 	= $keyword_discount_code->get_keyword();
        	$new_obj['code'] 		= $keyword_discount_code->get_code();
        	$new_obj['keyword_url'] = $keyword_discount_code->get_keyword_url();

        	$new_keyword_codes_final[] = $new_obj;
        }

    /* ====================================================================== *
        	CHECK ORDERS THAT GENERATED KEYWORD CODES AND UPDATE STATUS IF THEY ARE READY
 	 * ====================================================================== */	    

        $grouped_orders = $order->get_grouped_orders(""); // get grouped orders (orders that have status_admin = 'GROUP ORDER CREATED')

        foreach ($grouped_orders as $row) {

        	$order_ready 		= true;
        	$keyword_url 		= '';
        	$current_details 	= $order_detail->get_list($row->get_id_order(), " order by id_product ");
        	foreach ($current_details as $row_inner) {
        		if($row_inner->get_with_keyword_discount() == 'yes' && $row_inner->get_keyword_discount_code() == ''){ // only details that generated a new code

        			$product				= new product();
					$product->map($row_inner->get_id_product());
					$current_keyword 		= substr($product->get_keywords(), 1, -1); // get keyword
					$current_keyword_code 	= $keyword_discount_code->get_code_by_keyword_and_order($current_keyword, $row->get_id_order()); // get code from keyword
					$current_keyword_url 	= $keyword_discount_code->get_url_by_keyword_and_order($current_keyword, $row->get_id_order()); // get url from keyword
					$keyword_url 			= $current_keyword_url;

					if(!$order_detail->exists_keyword_discount_code($current_keyword_code)){ // check if keyword code is used, if not, then is not ready
						$order_ready = false;
					}

        		}
        	}

        	if($order_ready){
        		$order->update_status_admin('', $row->get_id_order());
        		$order->update_date_placed($row->get_id_order());

        		$msg = "You have a new follower in your deal. Your orders will be processed together. You can share the deal with more of your friends - ".$keyword_url;
        		

        		$message->send_message_only($row->get_id_fb_user(), $msg);
        		$message->send_SMS_only($row->get_id_fb_user(), $address, $msg);
        	}
        	
        }

	/* ====================================================================== *
        	SEND SMS AND MESSAGE
 	 * ====================================================================== */			

        if(count($new_keyword_codes) > 0){

        	foreach ($new_keyword_codes_final as $row) {

        		$msg = "Your order is now booked and will start processing as soon as one of your friends joins the group deal. Your share id is ".$row['code'].", 
        				and keyword link is ".$row['keyword_url']." Forward it to as many friends as you can to complete the deal.";
        		

        		$message->send_message_only($id_fb_user, $msg);
        		$message->send_SMS_only($id_fb_user, $address, $msg);
        	}

        }else{
        	if(!$message->send_auto_message($order, $current_id_order, "")){
				$error = true;
			}
			$message->send_SMS_by_order($order, $current_id_order, $order_address);
        }

	/* ====================================================================== *
        	RETURN
 	 * ====================================================================== */		

		if($error){
		  	$conn->rollBack();
		  	return false;
		}else{
		  	$conn->commit();
		  	return true;
		}

	}
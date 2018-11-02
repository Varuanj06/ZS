<?php

	function checking_order_status($order_status, $payment_method){

		$new_step = "";

		if($order_status == ""){ // ORDERS_PLACED
			$new_step = "10";
		}else if($order_status == "PROCESSING ORDER"){ // PROCESSING ORDER
			$new_step = "22";
		}else if($order_status == "ORDER SHIPPED"){ // ORDER SHIPPED
			$new_step = "15";
		}else if($order_status == "ORDER CANCELLED"){ // ORDER CANCELLED


			if($payment_method == 'Cash on Delivery'){
				$new_step = "24";	
			}else if($payment_method == 'Pay Online'){
				$new_step = "25";	
			}else{
				$new_step = "ERROR";
			}
			

		}else if($order_status == "ORDER PARTIALLY SHIPPED"){ // ORDER PARTIALLY SHIPPED
			$new_step = "20";
		}else if($order_status == "ORDER REFUSED"){ // ORDER REFUSED
			$new_step = "14";
		}else if($order_status == "ORDER ON HOLD"){ // ORDER ON HOLD
			$new_step = "23";
		}else{
			$new_step = "21";
		}

		return $new_step;

	}

	function get_next_step($old_step, $msg){
		$new_step = $old_step;

		if($old_step == '1'){

			if($msg == 'NO'){
				$new_step = '3';
			}else if($msg == 'YES'){
				$new_step = '4';
			}else{
				$new_step = 'ERROR';
			}

		}else if($old_step == '4'){

			if($msg == 'MY ORDER IS NOT VISIBLE'){

				global $order;
				global $id_fb_user;

				if($order->get_created_order_with_courier_by_fb_user($id_fb_user)){
					$new_step 			= "28";
				}else{
					$new_step 			= "29";
				}

			}else{

				$pieces 	= explode("#", $msg);

				if(count($pieces)>1){

					$id_order 	= $pieces[1];

					global $order_detail;
					global $order;
					global $message_conversation;
					global $id_fb_user;
					global $id_conversation;

					$message_conversation->update_id_order($id_fb_user, $id_conversation, $id_order);

					$details_returned 				= $order_detail->get_list($id_order, " and returned <> '' order by id_product ");
					$details_with_return_request 	= $order_detail->get_list($id_order, " and return_request_from_customer <> '' order by id_product ");

					if(count($details_returned)>0 && count($details_with_return_request)>0){
						$new_step = '5';
					}else if(count($details_returned)>0){
						$new_step = '8';
					}else if(count($details_with_return_request)>0){
						$new_step = '9';
					}else{

						$order->map($id_order);
						$new_step = checking_order_status($order->get_status_admin(), $order->get_payment_method());

					}

				}else{
					$new_step = "ERROR";
				}

			}	

		}else if($old_step == '10'){
			$new_step = '11';
		}else if($old_step == '11'){
			
			if($msg == 'NO'){
				$new_step = '12';

				/* CHECK HOW OLD THE ORDER IS */
				global $message_conversation;
				global $id_fb_user;
				global $id_conversation;
				global $order;
				
				$message_conversation->map($id_fb_user, $id_conversation);
				if($order->id_order_more_than_30_days($message_conversation->get_id_order())){
					$new_step = '30';
				}else{
					$new_step = '12';
				}

			}else if($msg == 'YES'){
				$new_step = '13';
			}else{
				$new_step = 'ERROR';
			}

		}else if($old_step == '15'){
			
			if($msg == 'NO'){
				$new_step = '16';
			}else if($msg == 'YES'){
				$new_step = '19';
			}else{
				$new_step = 'ERROR';
			}

		}else if($old_step == '16'){
			
			if($msg == 'NO'){
				$new_step = '17';
			}else if($msg == 'YES'){
				$new_step = '18';
			}else{
				$new_step = 'ERROR';
			}

		}else if($old_step == '8'){
			
			if($msg == 'MY ORDER'){
				
				global $message_conversation;
				global $id_fb_user;
				global $id_conversation;
				global $order;
				
				$message_conversation->map($id_fb_user, $id_conversation);
				$order->map($message_conversation->get_id_order());

				$new_step = checking_order_status($order->get_status_admin(), $order->get_payment_method());

			}else if($msg == 'MY RETURNED PRODUCT(S)'){
				$new_step = '6';
			}else{
				$new_step = 'ERROR';
			}

		}else if($old_step == '5'){
			
			if($msg == 'MY ORDER'){
				
				global $message_conversation;
				global $id_fb_user;
				global $id_conversation;
				global $order;
				
				$message_conversation->map($id_fb_user, $id_conversation);
				$order->map($message_conversation->get_id_order());
				
				$new_step = checking_order_status($order->get_status_admin(), $order->get_payment_method());

			}else if($msg == 'MY RETURNED PRODUCT(S)'){
				$new_step = '6';
			}else if($msg == 'MY RETURN REQUEST RAISED'){
				$new_step = '7';
			}else{
				$new_step = 'ERROR';
			}

		}else if($old_step == '9'){
			
			if($msg == 'MY ORDER'){
				
				global $message_conversation;
				global $id_fb_user;
				global $id_conversation;
				global $order;
				
				$message_conversation->map($id_fb_user, $id_conversation);
				$order->map($message_conversation->get_id_order());
				
				$new_step = checking_order_status($order->get_status_admin(), $order->get_payment_method());

			}else if($msg == 'MY RETURN REQUEST RAISED'){
				$new_step = '7';
			}else{
				$new_step = 'ERROR';
			}

		}else if($old_step == '20'){

			$pieces 			= explode("#", $msg);
			$id_order_detail 	= $pieces[1];

			global $message_conversation;
			global $id_fb_user;
			global $id_conversation;
			global $order_detail;

			$message_conversation->update_id_order_detail($id_fb_user, $id_conversation, $id_order_detail);
			$message_conversation->map($id_fb_user, $id_conversation);
			$order_detail->map($message_conversation->get_id_order(), $id_order_detail);

			if($order_detail->get_shipped()=='yes'){
				$new_step = '15';
			}else{
				$new_step = '10';
			}

		}else if($old_step == '26'){

			if($msg == 'NO'){
				$new_step = '2';
			}else if($msg == 'YES'){
				$new_step = '27';
			}else{
				$new_step = 'ERROR';
			}
		}else if($old_step == '28'){

			if($msg == 'NO'){
				$new_step = '29';
			}else if($msg == 'YES'){
				$new_step = '27';
			}else{
				$new_step = 'ERROR';
			}
		}else if($old_step == '30'){

			if($msg == 'NO'){
				$new_step = '31';
			}else if($msg == 'YES'){
				$new_step = '12';
			}else{
				$new_step = 'ERROR';
			}
		}
			
		return $new_step;	
	}

	function get_category($old_step, $new_step){
		$category = '';

		if($old_step == '3' || $old_step == '21' || $old_step == '29'){
			$category = 'ENQUIRY';
		}else if($old_step == '2'){
			$category = 'NEW CUSTOMER';
		}else if($old_step == '12'){
			$category = 'OPEN ORDER';
		}else if($old_step == '14'){
			$category = 'REFUSED ISSUE';
		}else if($new_step == '17'){
			$category = 'TRACKING NUMBER TO BE GIVEN';
		}else if($old_step == '18'){
			$category = 'ISSUE WITH TRACKING';
		}else if($old_step == '22' || $old_step == '23'){
			$category = 'PROCESSING ORDERS';
		}else if($old_step == '27'){
			$category = 'PAYMENT ISSUES';
		}

		return $category;
	}

	function get_status($old_step, $new_step){
		$status = 'pending on customer';

		if(
			$old_step == '3' ||
			$old_step == '2' ||
			$old_step == '12' ||
			$old_step == '14' ||
			$new_step == '17' ||
			$old_step == '18' ||
			$old_step == '21' ||
			$old_step == '22' ||
			$old_step == '23' ||
			$old_step == '27' ||
			$old_step == '29' 
		){
			$status = 'open';
		}

		if(
			$new_step == '13' ||
			$new_step == '19' ||
			$new_step == '24' ||
			$new_step == '25' ||
			$new_step == '6' ||
			$new_step == '7' ||
			$new_step == '31' 
		){
			if($old_step != $new_step){
				$status = 'closed';	
			}else{
				$status = 'open'; // if the status didn't change and the customer is responding, it means that the admin open manually the conversation!!! so let him answer with the textfield
			}
			
		}

		return $status;
	}

	function get_admin_msg($new_step){
		$admin_msg = '';

		if($new_step == '2'){
    		$admin_msg = 'Greetings!! We see that you are a new customer, please drop a message to us in the text field below.';
    	}else if($new_step == '27'){
    		$admin_msg = 'Please share your payment id and email id, we will check in our system and get back to you very soon.';
    	}else if($new_step == '28'){
    		$admin_msg = 'We have noticed that you have added some products to your cart. Did you by any chance try to make a payment yet the order is not confirmed?';
    	}else if($new_step == '29'){
    		$admin_msg = 'Please let us know your concern.';
    	}else if($new_step == '3'){
    		$admin_msg = 'Please let us know your concern';
    	}else if($new_step == '4'){
    		$admin_msg = 'Select the order you wish to talk about';
    	}else if($new_step == '5'){
    		$admin_msg = 'We notice that some of your products have a return request raised or are returned. What do you want to talk about?';
    	}else if($new_step == '6'){
    		$admin_msg = 'We see that you have returned the product and that a voucher has been issued to you. This voucher is visible while checkout.';
    	}else if($new_step == '10'){
    		$admin_msg = 'If you would like to see the status of the order, you can track <a href="http://miracas.com/ZS/orders/">here</a>. If you have any other queries please click CONTINUE.';
    	}else if($new_step == '11'){
    		$admin_msg = 'Do you need to change the address of your order?';
    	}else if($new_step == '12'){
    		$admin_msg = 'Please let us know your concern.';
    	}else if($new_step == '13'){
    		$admin_msg = 'To edit your address details, please click <a href="http://miracas.com/ZS/orders/">here</a>.';
    	}else if($new_step == '14'){
    		$admin_msg = 'Your order has returned back to us with the status <strong>Customer Refused</strong>. Please let us know if there is any concern.';
    	}else if($new_step == '15'){

    		global $message_conversation;
			global $id_fb_user;
			global $id_conversation;
			global $order;
			
			$message_conversation->map($id_fb_user, $id_conversation);
			$order->map($message_conversation->get_id_order());
			$courier = $order->get_courier_allocation();

    		$admin_msg = 'Your order is shipped via '.$courier.'. Please let us know if you have received it.';
    	}else if($new_step == '16'){
    		$admin_msg = 'You must have received the tracking number via sms. Have you?';
    	}else if($new_step == '17'){
    		$admin_msg = 'Our agent will soon intimate you the tracking details.';
    	}else if($new_step == '18'){
    		$admin_msg = 'You can track it on the courier website. If you are still facing problems, please use the textfield below to send us a message.';
    	}else if($new_step == '19'){
    		$admin_msg = 'Great. If for somereason you are not satisfied with the product ( Quality, Size etc ) you can return the product. If you wish to raise a return request, check <a href="http://miracas.com/ZS/orders/">here</a>';
    	}else if($new_step == '20'){
    		$admin_msg = 'One or more of the products in your order has been shipped. Please let us know which product you would like an update on.';
    	}else if($new_step == '21'){
    		$admin_msg = 'Please let us know your concern.';
    	}else if($new_step == '22'){
    		$admin_msg = 'I see that your order is under processing today and getting prepared for dispatch. If you have any last minute requirements, please let me know here.';
    	}else if($new_step == '23'){
    		$admin_msg = 'I see that your order is on hold. If you think it is a mistake, do let me know.';
    	}else if($new_step == '24'){
    		$admin_msg = 'Your order has been cancelled. If you still want the product, please place a fresh order.';
    	}else if($new_step == '25'){
    		$admin_msg = 'Your order has been cancelled by the system and a voucher for the same amount has been issued to you. The amount can be used to place a fresh order. The voucher has a default validity of one year.';
    	}else if($new_step == '8'){
    		$admin_msg = 'We notice that some of your products are returned. What do you want to talk about?';
    	}else if($new_step == '9'){
    		$admin_msg = 'We notice that some of your products have a return request raised. What do you want to talk about?';
    	}else if($new_step == '7'){
    		
    		global $message_conversation;
			global $id_fb_user;
			global $id_conversation;
			global $order_detail;
			global $product;
			
			$message_conversation->map($id_fb_user, $id_conversation);

			$details_with_return_request 	= $order_detail->get_list($message_conversation->get_id_order(), " and return_request_from_customer <> '' order by id_product ");
			$msg_from_details 				= "";

			foreach ($details_with_return_request as $row){

				$product->map($row->get_id_product());
				$product_name = $product->get_name();

				if($row->get_return_request_from_customer() == 'request'){
					 $msg_from_details .= "We have received your return request of your $product_name and is under processing. <br><br>";
				}else if($row->get_return_request_from_customer() == 'done'){
					$msg_from_details .= "Your return request of your $product_name is accepted and our courier partner is getting ready to pick the product back. <br><br>";
				}else if($row->get_return_request_from_customer() == 'rejected'){
					$msg_from_details .= "Your return request of your $product_name is rejected as we do not have reverse pickup service in your region . Please send the product back at the given address. <br><br>";
				}
			}
			if(strlen($msg_from_details)>0){
				$msg_from_details = substr($msg_from_details, 0, -8);
			}

			$admin_msg = $msg_from_details;

    	}else if($new_step == '30'){
    		$admin_msg 	= '	We see that your order has been delayed. This usually happens when there is an additional check with the Indian customs. But do not worry, we are filing the paperwork required to clear your shipment on your behalf. This is a free service provided by us.  To get a better understanding, please have a look at the below infogram.
    						<br><br>
    						<img src="http://miracas.com/ZS/static/delivery.png" style="width:100%;max-width:100%;" />
    						<br><br>
    						<br><br>
							would you like to leave a message regarding your order?';
    	}else if($new_step == '31'){
    		$admin_msg = 'Thank you for contacting. Have a nice day.';
    	}

    	return $admin_msg;
	}









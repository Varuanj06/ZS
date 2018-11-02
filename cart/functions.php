<?php if(is_file("../../classes/order.php")){ require_once("../../classes/order.php"); }else{ require_once("../classes/order.php"); } ?>
<?php if(is_file("../../classes/order_detail.php")){ require_once("../../classes/order_detail.php"); }else{ require_once("../classes/order_detail.php"); } ?>
<?php if(is_file("../../classes/address.php")){ require_once("../../classes/address.php"); }else{ require_once("../classes/address.php"); } ?>
<?php if(is_file("../../classes/order_voucher.php")){ require_once("../../classes/order_voucher.php"); }else{ require_once("../classes/order_voucher.php"); } ?>
<?php if(is_file("../../classes/functions.php")){ require_once("../../classes/functions.php"); }else{ require_once("../classes/functions.php"); } ?>
<?php if(is_file("../../classes/functions_espresso.php")){ require_once("../../classes/functions_espresso.php"); }else{ require_once("../classes/functions_espresso.php"); } ?>

<?php 

	function get_id_order($id_fb_user){
		$order 			= new order();
		$id_order   	= "-1";
		if($order->get_id_order_by_fb_user($id_fb_user)){
			$id_order 	= $order->get_id_order_by_fb_user($id_fb_user);
		}

		return $id_order;
	}

	function get_order_details($id_order){
		$order_detail 	= new order_detail();
		return $order_detail->get_list($id_order, " order by id_product ");
	}

	function get_address($id_fb_user){
		$address 			= new address();
		$adressList 		= $address->get_list($id_fb_user, " order by date_update desc ");
		$id_address 		= "";
		foreach ($adressList as $row){ 
			$id_address = $row->get_id_address();
			break;
		}
		$address->map($id_address, $id_fb_user);

		return $address;
	}

	function get_total_amount($details){
		$total_amount = 0;
		foreach ($details as $row) {
			$qty 				= $row->get_qty();
			$price 				= 0;
			$discount 			= 0;

			if($row->get_order_type() == 'espresso'){
				$price 					= get_the_price_espresso($row->get_id_product());
				$discount 				= get_the_discount_espresso($row->get_id_product(), $price);
			}else{
				$price 					= get_the_price($row->get_id_product());
				$discount 				= get_the_discount($row->get_id_product(), $price);

				if($row->get_with_keyword_discount() == 'yes'){
					$discount = get_the_keyword_discount($row->get_id_product(), $price);
				}
			}
			
			$price_final 	= ((float)$price-(float)$discount)*(float)$qty;

			$total_amount += $price_final;
		}

		return $total_amount;
	}

	function get_total_vouchers($id_order, $total_amount){
		$order_voucher 		= new order_voucher();
		
		return $order_voucher->get_vouchers_discount($id_order, $total_amount);
	}

?>
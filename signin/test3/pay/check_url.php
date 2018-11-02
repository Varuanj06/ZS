<?php require_once("../fb_validator_ajax.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/vendor_product.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../includes/plugins/snoopy/Snoopy.class.php"); ?>

<?php 

	$id_fb_user 		= $user['id'];
	$order 				= new order();
	$order_detail 		= new order_detail();
	$vendor_product 	= new vendor_product();
	$product 			= new product();

	$details 			= array();
	if($order->get_id_order_by_fb_user($id_fb_user)){
		$current_id_order 	= $order->get_id_order_by_fb_user($id_fb_user);
		$details 			= $order_detail->get_list($current_id_order, " order by id_product ");
		$order->map($current_id_order);
	}

	$not_found_details = "";

	foreach ($details as $row) {
		$product_link 	= trim($vendor_product->get_product_link_lang($row->get_id_product_prestashop()));
		$exists 		= 'no';

		if(filter_var($product_link, FILTER_VALIDATE_URL) === FALSE){// this checks if the link is valid
			$exists = 'no';
		}else{
			$snoopy 		= new Snoopy;
			$snoopy->fetchtext($product_link);
			$page 			=  mb_convert_encoding($snoopy->results, 'utf-8', "gb18030");
			$response_code 	= $snoopy->response_code;

			if(strpos($response_code, "404 Not Found") !== false || strpos($page, "商品已下架") !== false){
			    $exists 	= 'no';
			}else{
			    $exists 	= 'yes';
			}
		}

		if($exists === 'no'){
			$product->map($row->get_id_product());
			$not_found_details .= "<li style='margin-bottom:5px;'>
			                            <img style='width:30px !important;' src='".$product->get_image_link()."' alt='''>
			                            &nbsp;
										".$product->get_name()."
									</li>";
		}
	}
	if($not_found_details != ""){
		$not_found_details = 	"<div class='alert alert-warning'>
									The following product/products you are ordering may not be in stock  currently and may require time to procure. You will still be able to place the order and pay for it. If the product is not in stock you can opt for a full refund.
									<br><br>
									<ul>$not_found_details</ul>
								</div>";
	}else{
		$not_found_details = 	"<div class='alert alert-success'>
									All product/products you are ordering are in stock.
								</div>";
	}

	echo $not_found_details;

<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/vendor_product_stock.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>
<?php require_once("../../classes/message.php"); ?>

<?php 

$final_result = $_SESSION['final_result_stock'];

$vendor_product_stock 	= new vendor_product_stock();
$order_address 			= new order_address();
$order_detail 			= new order_detail;
$order 					= new order();
$message 				= new message();

$error = false;
$conn->beginTransaction();
$conn_reports->beginTransaction();

$orders_updated = array();

$tmp_order = "";
foreach ($final_result as $row) { 

	if( isset( $_POST['checkbox_'.$row['id_order'].'_'.$row['id_order_detail']]) ){
		if($tmp_order != $row['id_order']){
			$tmp_order = $row['id_order'];

			$orders_updated[] = $row['id_order'];

			if(!$order->update_status_admin("PROCESSING ORDER", $row['id_order'])){
				$error = true;
			}

			// ##### SEND AUTO MESSAGE #####
			if(!$message->send_auto_message($order, $row['id_order'], "PROCESSING ORDER")){
				$error = true;
			}
			$message->send_SMS_by_order($order, $row['id_order'], $order_address);
			// ##### END SEND AUTO MESSAGE #####
		}

		$qty = $_POST['qty_to_reduce_'.$row['id_order'].'_'.$row['id_order_detail']];

		$color_				= str_replace(' ', '_', $row['color']);
		$size_				= str_replace(' ', '_', $row['size']);
		$current_stock 		= $vendor_product_stock->get_stock_each_product_lang( $row['product_id'], $color_, $size_ );

		//echo "ID_ORDER_DETAIL: ".$row['id_order_detail'].", PRODUCT_ID: ".$row['product_id'].", COLOR: $color_, SIZE: $size_ | ".$current_stock ." - ". $qty ." = ".($current_stock-$qty)."<br>";			
		
		$order_detail->set_sent("yes");
		$order_detail->set_id_order($row['id_order']);
		$order_detail->set_id_order_detail($row['id_order_detail']);
		$order_detail->update_sent();

		if($vendor_product_stock->update_stock( $row['product_id'], $color_, $size_, ($current_stock-$qty) ) == false ){
			$error = true;
			break;
		}
	}

}

/*

// Iterate the orders that got products with stock being updated
foreach ($orders_updated as $row) {

	$order_history 	= new order_history();	
	
	$id_order 		= $row;
	$last_status 	= $order_history->get_last_status($id_order);
	$new_status 	= '';

	if($last_status == 'something'){
		$new_status = 'something else';
	}else if($last_status == 'something'){
		$new_status = 'something else';
	}else if($last_status == 'something'){
		$new_status = 'something else';
	}else if($last_status == 'something'){
		$new_status = 'something else';
	}else if($last_status == 'something'){
		$new_status = 'something else';
	}

	$order_history->set_id_employee("1");
	$order_history->set_id_order($id_order);
	$order_history->set_id_order_state($new_status);

	if(!$order_history->insert()){
		$error = true;
		break;
	}
}

*/


if($error){
      /* Recognize mistake and roll back changes */
      $conn->rollBack();
      $conn_reports->rollBack();
      echo "Oops!! There was an error, please go back and try again!";
}else{
      $conn->commit();
      $conn_reports->commit();

      echo "<script>location.href='../orders_with_stock/check_stock.php?updated=1';</script>";
      die();      
}
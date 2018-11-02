<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/vendor_product.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */	

		$order 					= new order();
		$order_detail 			= new order_detail();
		$vendor 				= new vendor();
		$vendor_product 		= new vendor_product();
		
	?>

	<style>
		td .btn{
			vertical-align: bottom;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>Vendor Analysis</h2>

			<hr>

			<form action="" method="post" name="form">
				<p>
					<label for="from_date">From date</label>
					<input value="<?php echo isset($_POST['from_date'])?$_POST['from_date']:""; ?>" class="custom-input" id="from_date" name="from_date" type="text" maxlength="200" placeholder="click here and choose a date!">
				</p>

				<p>
					<label for="till_date">Till date</label>
					<input value="<?php echo isset($_POST['till_date'])?$_POST['till_date']:""; ?>" class="custom-input" id="till_date" name="till_date" type="text" maxlength="200" placeholder="click here and choose a date!">
				</p>

				<p>
					<input <?php if(isset($_POST['payment1'])){ echo "checked"; } ?> name="payment1" id="payment1" type="checkbox" value="Cash on Delivery">&nbsp;
					<label for="payment1">Cash on Delivery</label>
					<br>
					<input <?php if(isset($_POST['payment2'])){ echo "checked"; } ?> name="payment2" id="payment2" type="checkbox" value="Pay Online">&nbsp;
					<label for="payment2">Pay Online</label>
				</p>

				<p>
					<input <?php if(isset($_POST['place_and_partially_shipped'])){ echo "checked"; } ?> name="place_and_partially_shipped" id="place_and_partially_shipped" type="checkbox" value="Pay Online">&nbsp;
					<label for="place_and_partially_shipped">Orders placed and partially shipped</label>
				</p>

				<p>
					<a href="javascript:document.form.submit();" class="btn btn-default btn-large btn-green">Generate Report!</a>
				</p>
			</form>

			<?php if( isset($_POST['from_date']) && isset($_POST['till_date']) ){?>

				<?php 

					$from_date 				= $_POST['from_date'];
					$till_date 				= $_POST['till_date'];

				/* ====================================================================== *
			        	ORDERS BETWEEN DATES
			 	 * ====================================================================== */	

			        $paymentSQL				= "";
					if( isset($_POST['payment1']) ){
						$paymentSQL .= " payment_method = '".$_POST['payment1']."' or";
					} 
					if( isset($_POST['payment2']) ){
						$paymentSQL .= " payment_method = '".$_POST['payment2']."' or";
					}
					if($paymentSQL != ""){
						$paymentSQL = " and (". substr($paymentSQL, 0, -2) . ") ";
					}	

					$place_and_partially_shipped = "";
					if(isset($_POST['place_and_partially_shipped'])){
						$place_and_partially_shipped = " and (status_admin in('ORDER PLACED', 'ORDER PARTIALLY SHIPPED') or status_admin is null or status_admin = '') ";
					}

					$orders_finished 		= $order->get_orders_between_dates($from_date, $till_date, "$paymentSQL $place_and_partially_shipped");

					$id_orders = "";
					foreach ($orders_finished as $order) {
						$id_orders .= "'".$order->get_id_order()."',";
					}
					if(strlen($id_orders)>0){ 
						$id_orders = substr($id_orders, 0, -1); 
					}else{
						$id_orders = "'#### nothing at all ####'";
					}

				/* ====================================================================== *
			        	GET INFO OF THE PRODUCT DETAILS
			 	 * ====================================================================== */	

					$order_details 			= $order_detail->get_all_from_orders($id_orders, " order by id_order, id_product_prestashop ");
					$order_details_info 	= array();

					foreach ($order_details as $detail) {

						$obj 					= array();
						$id_vendor 				= $vendor_product->get_vendor($detail->get_id_product_prestashop());
						$id_order 				= $detail->get_id_order();

						$order->map($id_order);
						$vendor->map($id_vendor);

						$obj['id_vendor'] 		= $id_vendor;
						$obj['vendor_name'] 	= $id_vendor != '' ? $vendor->get_name() : '';
						$obj['id_order'] 		= $id_order;
						$obj['order_amount'] 	= $order->get_total_amount();
						$obj['order_discount'] 	= $order->get_total_discount();
						$obj['amount'] 			= $detail->get_amount()*$detail->get_qty();
						$obj['discount']		= $detail->get_discount()*$detail->get_qty();
						$obj['qty'] 			= $detail->get_qty();
						$obj['total'] 			= $obj['amount']-$obj['discount'];

						$order_details_info[]	= $obj;

					}

				/* ====================================================================== *
			        	IF THE DETAIL HAS NO AMOUNT THEN TAKE THE ORDER AMOUNT
			 	 * ====================================================================== */		

					$array_tmp = array();
					foreach ($order_details_info as $key => $value){
						if($value['amount'] == '' && $value['order_amount'] != ''){

							$key_tmp = $value['id_vendor']."|".$value['id_order'];
							if(!in_array($key_tmp, $array_tmp)){
								$array_tmp[] = $key_tmp;

								$order_details_info[$key]['amount'] 		= $value['order_amount'];
								$order_details_info[$key]['discount'] 		= $value['order_discount'];
								$order_details_info[$key]['total'] 			= $value['order_amount']-$value['order_discount'];
							}

						}
					}

				/* ====================================================================== *
			        	GROUP DETAILS BY ID_VENDOR
			 	 * ====================================================================== */	

			 	 	function in_multi_array($array, $find_key, $find_value){
			        	$return = -1;

			        	foreach ($array as $key => $value) {
			        		if($value[$find_key] == $find_value){
			        			$return = $key;
			        		}
			        	}

			        	return $return;
			        }		

			        $grouped_order_details_info = array();

			        foreach ($order_details_info as $row){
			        	
						$find  				= in_multi_array($grouped_order_details_info, 'id_vendor', $row['id_vendor']);

						if($find != -1){ // found

							$grouped_order_details_info[$find]['qty'] 		+= $row['qty'];
							$grouped_order_details_info[$find]['amount'] 	+= $row['amount'];
							$grouped_order_details_info[$find]['discount'] 	+= $row['discount'];
							$grouped_order_details_info[$find]['total'] 	+= $row['total'];

						}else{ // not found, so add it for the first time
							$grouped_order_details_info[] = $row;
						}

			        }

			    /* ====================================================================== *
			        	SORT BY TOTAL
			 	 * ====================================================================== */	    

			        function sortByOrder($a, $b) {
					    return $b['total'] - $a['total'];
					}

					usort($grouped_order_details_info, 'sortByOrder');

				?>
			
				<table class="table table-condensed table-bordered" id="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Vendor</th>
							<th>Qty</th>
							<th>Amount</th>
							<th>Discount</th>
							<th>Total</th>
						</tr>
						<?php $cont =  0; ?>
						<?php foreach ($grouped_order_details_info as $row) { ?>
							<?php $cont++; ?>
							<tr>
								<td><?php echo $cont; ?></td>
								<td><?php echo $row['vendor_name']==''?'Without Vendor':$row['vendor_name']; ?></td>
								<td><?php echo $row['qty']; ?></td>
								<td><?php echo number_format($row['amount'], 2); ?></td>
								<td><?php echo number_format($row['discount'], 2); ?></td>
								<td><?php echo number_format($row['amount']-$row['discount'], 2); ?></td>
							</tr>
						<?php } ?>
					</thead>
				</table>

			<?php } ?>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../vendor_analysis"]').parents('li').addClass('active');</script>

	<link href="../includes/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script src="../includes/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <script>
    	$('#from_date, #till_date').datepicker({
    		 orientation: "bottom right",
    		 format: "yyyy-mm-dd"
    	});
    </script>
	
</body>
</html>
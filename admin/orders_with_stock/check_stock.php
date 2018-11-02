<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/vendor_product.php"); ?>
<?php require_once("../../classes/vendor_product_stock.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/espresso_products.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 

		$order 					= new order();
		$order_detail 			= new order_detail();

		$vendor 				= new vendor();
		$vendor_product 		= new vendor_product();
		$vendor_product_stock 	= new vendor_product_stock();

		$espresso_products		= new espresso_products();
		$product 				= new product();
		$product_lang 			= new product_lang();
		

		$id_vendor = isset($_SESSION['id_vendor'])?$_SESSION['id_vendor']:"";

		$consider_vendor = isset($_POST['consider_vendor']) ? "checked" : "";
		if( !isset($_POST['from_date']) && !isset($_POST['till_date']) ){
			$consider_vendor = "checked";
		}
		
		/* Bring products in current vendor */
		$products_in_vendor = $vendor_product->get_all_from_vendor($id_vendor, "ORDER BY 1");	
	?>

	<style>
		.datepicker{
			z-index: 9999999999 !important;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Orders with stock
			</h2>

			<p>
				<a href="./" class="btn btn-default btn-gray btn-sm">Go back</a>	
			</p>	

			<?php
				if( isset($_GET['updated']) ){
					echo "<div class='alert alert-success'>Awesome! The stock was updated successfully</div>";
				}
			?>

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
					<input name="consider_vendor" id="consider_vendor" type="checkbox" value="yes" <?php echo $consider_vendor; ?>>&nbsp;
					<label for="consider_vendor">Take the vendor into consideration</label>
				</p>

				<p>
					<a href="javascript:document.form.submit();" class="btn btn-default btn-large btn-green">Generate Report!</a>
				</p>
			</form>

			<?php if( isset($_POST['from_date']) && isset($_POST['till_date']) ){?>
				
				<?php 

					$final_result 			= array();
					$from_date 				= $_POST['from_date'];
					$till_date 				= $_POST['till_date'];
					$all_stock				= array();

					$list 					= $order->get_orders_between_dates( $from_date, $till_date, " and (status_admin in('ORDER PLACED', 'PROCESSING ORDER', 'ORDER PARTIALLY SHIPPED') or status_admin is null or status_admin = '') " );

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
					$list_details = $order_detail->get_all_from_orders($id_orders, " order by id_order, id_product_prestashop ");

					foreach ($list_details as $detail) {
						if($detail->get_refunded() == 'yes'){
							continue;
						}

						$tmp = array();

						$order->map($detail->get_id_order());

						if($detail->get_order_type() == 'espresso'){
							$espresso_products = new espresso_products();
							$espresso_products->map($detail->get_id_product());

							$tmp['item']			= $espresso_products->get_name();
							$tmp['image_url']		= $espresso_products->get_image_link();
						}else{
							$product = new product();
							$product->map($detail->get_id_product());

							$tmp['item']			= $product->get_name();
							$tmp['image_url']		= $product->get_image_link();
						}

						$tmp['id_order_detail']	= $detail->get_id_order_detail();
						$tmp['id_order']		= $detail->get_id_order();
						$tmp['payment']			= $order->get_payment_method();
						$tmp['date_add']		= $order->get_date_done();
						$tmp['product_id']		= $detail->get_id_product_prestashop();
						$tmp['qty'] 			= $detail->get_qty();
						$tmp['supplier'] 		= $product_lang->get_supplier_reference($tmp['product_id']);
						$tmp['reference'] 		= $product_lang->get_reference($tmp['product_id']);
						$tmp['name'] 			= $tmp['item'];
						$tmp['color']   		= $detail->get_color();
						$tmp['size']   			= $detail->get_size();
						$color_					= str_replace(' ', '_', $tmp['color']);
						$size_					= str_replace(' ', '_', $tmp['size']);

						$tmp['init_stock']	= $vendor_product_stock->get_stock_each_product_lang( $tmp['product_id'], $color_, $size_ );
						if($tmp['init_stock'] == '')$tmp['init_stock'] = '0';

						//echo $tmp['product_id']." - ".$color_." - ".$size_." = ".$tmp['init_stock']."<br><br>";

						// Check if the product is part of the vendor
						$in_vendor = false;
						foreach ($products_in_vendor as $product) {
							if($product->get_id_product_lang() == $tmp['product_id'] || (!$consider_vendor || $consider_vendor == '')){
								$in_vendor = true;
							}
						}

						if($detail->get_sent() == 'yes'){
							$tmp['status'] 			= 'sent';
							$tmp['current_stock'] 	= '';
							$tmp['qty_supplied'] 	= '';
							$tmp['init_stock'] 		= '';
						}else{// the product hasn't been sent	

							// Fill the stock
							$key_all_stock = $tmp['product_id']."+".str_replace('#', '', $color_)."+".$size_;
							if (!array_key_exists($key_all_stock, $all_stock)) {
								$all_stock[$key_all_stock] = $tmp['init_stock'];
							}

							$tmp['current_stock'] 	= $all_stock[$key_all_stock];
							if( $tmp['current_stock'] > 0 && $tmp['current_stock'] >= $tmp['qty'] ){
								$tmp['status'] 			= 'Complete';
								$tmp['qty_supplied']	= $tmp['qty'];
							}else if( $tmp['current_stock'] > 0	){
								$tmp['status'] 			= 'Partial';
								$tmp['qty_supplied']	= $tmp['current_stock'];
							}else{
								$tmp['status'] 			= 'Uncomplete';
								$tmp['qty_supplied']	= 0;
							}

							// Check and reduce stock
							if($in_vendor){
								if( $all_stock[$key_all_stock] >= $tmp['qty'] ){
									$all_stock[$key_all_stock] = $all_stock[$key_all_stock] - $tmp['qty'];
								}else{
									$all_stock[$key_all_stock] = '0';
								}
							}

						}

						if($in_vendor){
							$final_result[] 	= $tmp;
						}
					}

					$_SESSION['final_result_stock'] = $final_result;
				?>
				<form action="update_stock.php" method="post" name="form2">
					<table class="table table-condensed table-bordered">
						<thead>
							<tr>
								<th>Select</th>
								<th>Product Id</th>
								<th>Image</th>
								<th>Item</th>
								<th>Supplier</th>
								<th>Reference</th>
								<th>Color</th>
								<th>Size</th>
								<th>Qty</th>
								<th>Status</th>
								<th class="text-right">Current Stock</th>
								<th class="text-right">Initial Stock</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$show_products 	= true;
								$tmp_order 		= "";
								foreach ($final_result as $row) { 
									if($tmp_order != $row['id_order']){
										$tmp_order = $row['id_order'];

										$partial 				= 0;
										$uncomplete         	= 0;
										$sent 					= 0;
										$products 				= 0;
										$show_products 			= true;

										foreach ($final_result as $inner_row) { 
											if($row['id_order'] == $inner_row['id_order']){
												$products++;
												if($inner_row['status'] == 'Partial'){
													$partial++;
												}else if($inner_row['status'] == 'Uncomplete'){
													$uncomplete++;
												}else if($inner_row['status'] == 'sent'){
													$sent++;
												}
											}
										}

										$status_order = "";
										if($sent == $products){
											$status_order = "Order Sent";
										}else if($partial == 0 && $uncomplete == 0){
											$status_order = "Complete Order";
										}else if($uncomplete == $products){
											$status_order = "Uncomplete Order";
										}else{
											$status_order = "Partial Order";
										}

										if($status_order == 'Uncomplete Order' || $status_order == 'Order Sent'){
											$show_products = false;
											continue;
										}
							?>
										<tr>
											<td colspan="12" class="alert-info">
												<strong>Order Id: <?php echo $row['id_order']; ?></strong> - 
												<?php echo $row['payment']; ?>
												<!--<?php echo $row['date_add']; ?>-->
												<span style="float:right;">
													<?php echo $status_order; ?>
												</span>
											</td>
										</tr>
							<?php
									}

									if($show_products){
							?>
										<tr>
											<td>
												<?php if($row['status'] == 'Complete'){ ?>
													<input 
														type="checkbox" 
														name="checkbox_<?php echo $row['id_order']; ?>_<?php echo $row['id_order_detail']; ?>" 
														<?php if($row['qty_supplied'] > 0){ echo "checked"; } ?> 
													/>
													<select 
														name="qty_to_reduce_<?php echo $row['id_order']; ?>_<?php echo $row['id_order_detail']; ?>"
													/>
														<option selected value="<?php echo $row['qty_supplied']; ?>"><?php echo $row['qty_supplied']; ?></option>
														<!--
														<?php for ($i=1; $i <= $row['qty_supplied']; $i++) { ?>
															<option selected value="<?php echo $i; ?>"><?php echo $i; ?></option>
														<?php } ?>
														<?php if($row['qty_supplied'] == 0){ ?>
															<option selected value="<?php echo 0; ?>"><?php echo 0; ?></option>
														<?php } ?>
														-->
													</select>
												<?php } ?>
											</td>
											<td><?php echo $row['product_id']; ?></td>
											<td>
												<img height="60px" src="<?php echo $row['image_url']; ?>" alt="">
											</td>
											<td><?php echo $row['name']; ?></td>
											<td><?php echo $row['supplier']; ?></td>
											<td><?php echo $row['reference']; ?></td>
											<td><?php echo $row['color']; ?></td>
											<td><?php echo $row['size']; ?></td>
											<td><?php echo $row['qty']; ?></td>
											<td>
												<?php 
													if($row['status'] == 'Complete'){
														echo '<div class="alert alert-success">Complete</div>';
													}else if($row['status'] == 'Partial'){
														echo '<div class="alert alert-warning">Partial</div>';
													}else if($row['status'] == 'Uncomplete'){
														echo '<div class="alert alert-danger">Uncomplete</div>';
													}else if($row['status'] == 'sent'){
														echo '<div class="alert alert-info">Sent</div>';
													}
												?>
											</td>
											<td class="text-right"><?php echo $row['current_stock']; ?></td>
											<td class="text-right"><?php echo $row['init_stock']; ?></td>
										</tr>
							<?php 
									}
								} 
							?>
						</tbody>
					</table>

					<p>
						<a href="javascript:process();" class="btn btn-default btn-large btn-blue">Mark as sent</a>
					</p>
				</form>

			<?php } ?>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../orders_with_stock"]').parents('li').addClass('active');</script>

	<link href="../includes/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script src="../includes/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

	<script>
		function process(){
			if(confirm('are you sure?')){
				document.form2.submit();
			}
		}
	</script>

    <script>
    	$('#from_date, #till_date').datepicker({
    		 orientation: "bottom right",
    		 format: "yyyy-mm-dd"
    	});
    </script>

	
</body>
</html>
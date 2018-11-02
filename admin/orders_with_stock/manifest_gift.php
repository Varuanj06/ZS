<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/espresso_products.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/fb_user_blacklist.php"); ?>

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
		$product_lang 			= new product_lang();
		$order_address 			= new order_address();
		$message 				= new message();
		$fb_user_blacklist 		= new fb_user_blacklist();

	/* ====================================================================== *
    		DATES
 	 * ====================================================================== */    

		$from_date 		= date("Y-m-d");
		$till_date 		= date("Y-m-d");

		if(isset($_POST['from_date']) && isset($_POST['till_date'])){
			$from_date 	= $_POST['from_date'];
			$till_date 	= $_POST['till_date'];
		}

		$sql_dates 		= "";
		if($from_date != "" && $till_date != ""){
			$sql_dates 	= " and date_done between '$from_date 00:00:00' and '$till_date 23:59:59' ";
		}

	/* ====================================================================== *
    		GET ORDRS WITHOUT SENT PRODUCTS
 	 * ====================================================================== */    	

		$orders 				= $order->get_orders_without_sent_products( " $sql_dates order by date_done " );

	/* ====================================================================== *
    		MARKED ORDERS AS "GIFT_SHIPPED"
 	 * ====================================================================== */    	

		$msj = "";
		if(isset($_POST['action']) && $_POST['action'] == '1'){

			$error = false;
			$conn->beginTransaction();

			foreach ($orders as $order){

				if(isset( $_POST['order_'.$order->get_id_order()] )){
					
					if(!$order->update_gift_shipped('yes', $order->get_id_order())){
						$error = true;
						break;
					}

				}
			}

			if($error){
		      	/* Recognize mistake and roll back changes */
		      	$conn->rollBack();
		      	$msj = "<div class='alert alert-danger'>Oops!! There was an error, please go back and try again!</div>";
			}else{
			    $conn->commit();
			    $msj = "<div class='alert alert-success'>Gifts shipped!</div>";
			}

			$orders = $order->get_orders_without_sent_products( " $sql_dates order by date_done " );
		}

	/* ====================================================================== *
    		GET COURIERS
 	 * ====================================================================== */    	

		$couriers 				= array();
		foreach ($orders as $order) {
			if(!in_array($order->get_courier_allocation(), $couriers)){
				$couriers[] = $order->get_courier_allocation();
			}
		}

	?>

	<div class="section">
		<div class="content">

			<h2>Gift Manifest</h2>

			<?php echo $msj; ?>
			
			<p>
				<a href="./" class="btn btn-default btn-gray btn-sm">Go back</a>	
			</p>

			<hr>

			<form action="" method="post" name="form_dates">
				<input type="hidden" name="id_orders_selected" value="'#### nothing at all ####'">

				<p>
					<label for="from_date">From date</label>
					<input value="<?php echo $from_date; ?>" class="custom-input" id="from_date" name="from_date" type="text" maxlength="200" placeholder="click here and choose a date!">
				</p>

				<p>
					<label for="till_date">Till date</label>
					<input value="<?php echo $till_date; ?>" class="custom-input" id="till_date" name="till_date" type="text" maxlength="200" placeholder="click here and choose a date!">
				</p>

				<p>
					<a href="javascript:document.form_dates.submit();" class="btn btn-default btn-large btn-green">Filter</a>
				</p>
			</form>
		
		<!--
		/* ====================================================================== *
	    		ITERATE COURIERS
	 	 * ====================================================================== */    	
	 	-->

			<?php foreach ($couriers as $courier) { ?>

				<div class="alert alert-warning">
					<?php echo $courier; ?>
				</div>


				<form action="" method="POST" name="form_<?php $courier ?>">

					<input type="hidden" name="action">

					<table class="table table-condensed table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th><input type="checkbox" class="check_all"></th>
								<th>ID</th>
								<th>Final amount</th>
								<th>Details</th>
								<th>Info</th>
							</tr>
						</thead>
						<tbody>

					<!--
					/* ====================================================================== *
				    		ITERATE PAYMENT METHODS
				 	 * ====================================================================== */    	
				 	-->	
				 		
						<?php 
							$payment_methods = array('Pay Online');
							foreach ($payment_methods as $payment) {	
						?>
								<tr>
									<td colspan="6" class="alert alert-info">
										<?php echo $payment; ?> &nbsp;
										<a 
											target="_blank" 
											href="manifest_gift_export.php?courier=<?php echo $courier; ?>&payment_method=<?php echo $payment; ?>&from_date=<?php echo $from_date; ?>&till_date=<?php echo $till_date; ?>" 
											class="btn btn-default btn-sm btn-primary pull-right">
											Export to excel
										</a>			
									</td>
								</tr>

					<!--
					/* ====================================================================== *
				    		ITERATE ORDERS
				 	 * ====================================================================== */    	
				 	-->	

							<?php
								$cont = 0;
								foreach ($orders as $order) {
									if($order->get_courier_allocation() != $courier){continue;}
									
									if($order->get_free_order() == 'yes' && $payment == 'Pay Online'){
										//all good
										// if its a free order show it when user wants the "Pay Online" manifest
									}else if($order->get_free_order() != 'yes' && $payment == $order->get_payment_method()){
										//all good
										// if its NOT a free order then just make sure the payment method of the order matches the want the user selects
									}else{
										continue;
									}
									$cont++;

									$order_address->map($order->get_id_order_address(), $order->get_id_fb_user());
							?>
									
									<tr>
										<td><?php echo $cont; ?></td>
										<td>
											<input type="checkbox" checked class="check_box" name="order_<?php echo $order->get_id_order(); ?>" />
										</td>
										<td><?php echo $order->get_id_order(); ?></td>
										<td class="text-right">
											₹<?php echo number_format($order->get_total_amount()+$order->get_shipping_fee()+$order->get_cod_fee()-$order->get_total_discount()-$order->get_total_discount_voucher(), 2); ?>
										</td>
										<td>
											
											<table style="width:100%;">
												<thead>
													<tr>
														<th>Product Id</th>
														<th>Image</th>
														<th>Item</th>
														<th>Color</th>
														<th>Size</th>
														<th>Qty</th>
													</tr>
												</thead>
												<tbody>

					<!--
					/* ====================================================================== *
				    		ITERATE DETAILS
				 	 * ====================================================================== */    	
				 	-->

												<?php
													$order_details 	= $order_detail->get_details_not_sent( $order->get_id_order()," order by 1 " );
													foreach ($order_details as $detail) {

														$product_name 		= '';
														$product_image_link = '';
														if($detail->get_order_type() == 'espresso'){
															$espresso_products = new espresso_products();
															$espresso_products->map($detail->get_id_product());

															$product_name			= $espresso_products->get_name();
															$product_image_link 	= $espresso_products->get_image_link();
														}else{
															$product	= new product();
															$product->map($detail->get_id_product());

															$product_name			= $product->get_name();
															$product_image_link 	= $product->get_image_link();
														}
												?>
														<tr>
															<td><?php echo $detail->get_id_product_prestashop(); ?></td>
															<td>
																<img style="height:40px !important;" src="<?php echo $product_image_link; ?>" alt="">
															</td>
															<td><?php echo $product_name; ?></td>
															<td class="text-center">
												        		<?php if($detail->get_color() != ""){ ?>
												        			<?php echo $detail->get_color(); ?>
												        			<br>
												        			<span class="media-box-color"><span style="background:<?php echo strpos($detail->get_color(), '#') !== false ? $detail->get_color() : "#".$detail->get_color(); ?>;"></span></span>
												        		<?php } ?>
												        	</td>
															<td><?php echo $detail->get_size(); ?></td>
															<td><?php echo $detail->get_qty(); ?></td>
														</tr>
												<?php
													}
												?>
												</tbody>
											</table>

										</td>
										<td>

					<!--
					/* ====================================================================== *
				    		SHOW ORDER DETAILS
				 	 * ====================================================================== */    	
				 	-->
											
											<div class="row">
												<div class="col-sm-4"><label for="name">Name</label></div>
												<div class="col-sm-8">
													<?php echo $order_address->get_name(); ?>
													<?php $fb_user_blacklist->set_id_fb_user($order->get_id_fb_user()); ?>
													<?php if($fb_user_blacklist->exists()){ ?>
														<br>
														<span class="label label-danger">Blacklisted</span>
													<?php } ?>
												</div>
											</div>	

											<div class="row">
												<div class="col-sm-4"><label for="pin_code">Email</label></div>
												<div class="col-sm-8"><?php echo $order_address->get_email(); ?></div>
											</div>			
											
											<div class="row">
												<div class="col-sm-4"><label for="mobile_number">Mobile Number</label></div>
												<div class="col-sm-8"><?php echo $order_address->get_mobile_number(); ?></div>
											</div>
											
											<div class="row">
												<div class="col-sm-4"><label for="address_text">Address</label></div>
												<div class="col-sm-8"><?php echo $order_address->get_address(); ?></div>
											</div>
											
											<div class="row">
												<div class="col-sm-4"><label for="landmark">Landmark</label></div>
												<div class="col-sm-8"><?php echo $order_address->get_landmark(); ?></div>
											</div>
											
											<div class="row">
												<div class="col-sm-4"><label for="city">City</label></div>
												<div class="col-sm-8"><?php echo $order_address->get_city(); ?></div>
											</div>
											
											<div class="row">
												<div class="col-sm-4"><label for="state">State</label></div>
												<div class="col-sm-8"><?php echo $order_address->get_state(); ?></div>
											</div>
											
											<div class="row">
												<div class="col-sm-4"><label for="pin_code">Pin code</label></div>
												<div class="col-sm-8"><?php echo $order_address->get_pin_code(); ?></div>
											</div>
											
											<div class="row">
												<div class="col-sm-4"><label for="pin_code">Courier Allocation</label></div>
												<div class="col-sm-8"><?php echo $order->get_courier_allocation(); ?></div>
											</div>

											<div class="row">
												<div class="col-sm-4"><label for="pin_code">Payment Method</label></div>
												<div class="col-sm-8">
													<?php
														if($order->get_free_order() === 'yes'){
															echo '<span class="label label-info">Free Order</span>';
														}else if($order->get_payment_method() == 'Cash on Delivery'){
															echo '<span class="label label-info" style="background-color:#B38D4B;">Cash on Delivery</span>';
														}else if($order->get_payment_method() == 'Pay Online'){
															echo '<span class="label label-success" style="background-color:#79D479;">Pay Online</span>';
														}
													?>
												</div>
											</div>
										</td>
										</tr>
						<?php
								}
							}
						?>
							
							<tr>
								<td>&nbsp;</td>
								<td>
									<a href="#" class="btn btn-sm btn-default btn-gray btn-shipped">Mark as gift shipped</a>
								</td>
								<td colspan="4">&nbsp;</td>
							</tr>

						</tbody>
					</table>

				</form>
			
			<?php } ?>
		
		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../orders_with_stock"]').parents('li').addClass('active');</script>

	<script src="../includes/js/search.js"></script>
	<script>
		$('#search').focus().search({table:$("#table")});
	</script>

	<script>
		$('.check_all').on('click', function(){
			$(this).closest('table').find('.check_box').prop('checked', $(this).prop('checked'));
		}).trigger('click');
	</script>

	<script>
		$('.btn-shipped').on('click', function(){
			var $this 	= $(this);
			var form 	= $this.closest('form')[0];

			form.action.value = "1";
			form.submit();
		})
	</script>

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
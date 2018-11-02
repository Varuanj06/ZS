<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/order_voucher.php"); ?>
<?php require_once("../classes/voucher.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../classes/fb_user_blacklist.php"); ?>
<?php require_once("../classes/purchase_order.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<title>Product Feed - Admin</title>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<link rel="stylesheet" type="text/css" href="../includes/plugins/font-awesome/css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="../includes/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../admin/includes/css/login.css">

	<style>
		.well input{
			width: 250px !important; 
			margin-right: 10px;
			font-size: 20px !important;
			padding: 15px 20px !important;
		}
		.well a{
			font-size: 20px !important;
			padding: 12px 20px !important;
		}

		.item_description img {
		  float: left;
		  margin-right: 20px;
		  width: 100px;
		}
		label{
			font-weight: 600;
		}
		.datepicker{
			z-index: 99999 !important;
		}
		.table td{
			padding: 4px !important;
		}
		.global td{
			background: #eef8ff !important;
		}

		.alert-info{
			line-height: 12px;
			font-size: 12px;
		}
	</style>
</head>
<body>

	<?php 
		$order 					= new order();
		$order_detail 			= new order_detail();
		$order_address 			= new order_address();
		$order_voucher 			= new order_voucher();
		$voucher 				= new voucher();
		$address 				= new address();
		$fb_user_blacklist 		= new fb_user_blacklist();
		$purchase_order 		= new purchase_order();

		/* #### APPLY FILTERS TO THE ORDERS #### */

		$orders 		= array();
		$id_order 		= isset($_POST['id_order']) ? $_POST['id_order'] : "";
		$mobile_number 	= isset($_POST['mobile_number']) ? $_POST['mobile_number'] : "";
		$id_fb_user 	= "";

		if($id_order != "" || $mobile_number != ""){
			$sql_order = "";
			if($id_order != ""){
				$sql_order = " and id_order = '$id_order' ";
			}

			$sql_mobile_number = "";
			if($mobile_number != ""){
				$sql_mobile_number = " and (select mobile_number from order_address where id_order_address = `order`.id_order_address and id_fb_user = `order`.id_fb_user) = '$mobile_number' ";
			}

			$orders = $order->get_list(" $sql_order $sql_mobile_number order by date_done desc ");
		}

		/* #### GET THE ID_FB_USER #### */

		if(count($orders) > 0){
			foreach ($orders as $row){
				$id_fb_user = $row->get_id_fb_user();
				break;
			}
		}

		/* #### GET THE ADDRESS #### */

		$address 			= new address();
		$adressList 		= $address->get_list($id_fb_user, " order by date_update desc ");
		$id_address 		= "";
		foreach ($adressList as $row){ 
			$id_address = $row->get_id_address();
			break;
		}
		$address->map($id_address, $id_fb_user);

		/* #### GET VOUCHERS #### */

		$vouchers_unused 	= $voucher->get_vouchers_unused($address->get_email(), " order by till_date ");
		$vouchers_used 		= $order_voucher->get_vouchers_used($address->get_email(), " order by id_order ")

	?>
	
	<link rel="stylesheet" href="../orders/timeline/css/style.css">
	<?php require_once("../orders/tracking_path.php"); ?>

	<div class="section">
		<div class="content">

			<h2 class="text-center">
				Search Order
			</h2>
				
			<br>

			<form method="post" name="form">
			
				<div class="well text-center" style="background:white;">

					<input value="<?php echo $id_order; ?>" class="custom-input" id="id_order" name="id_order" type="text" placeholder="Order Number">
					<input value="<?php echo $mobile_number; ?>" class="custom-input" id="mobile_number" name="mobile_number" type="text" placeholder="Mobile Number">
					<a href="javascript:search();" class="btn btn-primary">Search</a>	
					
				</div>

			</form>

			<?php if(count($orders) > 0){ ?> 
				
				<div class="well" style="background:white;">
					<div class="row">
						<div class="col-sm-6 ">
							<h5 class="text-center">Vouchers pending (unused)</h5>
							<hr>
							<ul>
								<?php foreach ($vouchers_unused as $row) { ?>
									<li>
										<?php echo $row->get_description() ?>
										<strong class="badge"><?php echo ($row->get_value_kind()=='amount'?'₹':'') . $row->get_value() . ($row->get_value_kind()=='percentage'?'%':''); ?>		</strong>
									</li>
								<?php } ?>
							</ul>
						</div>
						<div class="col-sm-6 ">
							<h5 class="text-center">Vouchers used</h5>
							<hr>
							<ul>
								<?php foreach ($vouchers_used as $row) { ?>
									<?php 
										$voucher = new voucher();
										$voucher->map($row->get_id_voucher());
									?>
									<li>
										<?php echo $voucher->get_description() ?>
										<strong class="badge">
											<?php echo ($row->get_value_kind()=='amount'?'₹':'') . $row->get_value() . ($row->get_value_kind()=='percentage'?'%':''); ?>		
										</strong>
										on #<?php echo $row->get_id_order(); ?>
									</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>

				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Order</th>
							<th>Address</th>
							<th style="width: 55%;">Details</th>
						</tr>
					</thead>
					<tbody>
						<?php

							$count = 0;
							foreach ($orders as $row){
								$count++;

								$details = $order_detail->get_list($row->get_id_order(), " order by id_product ");
								$order_address->map($row->get_id_order_address(), $row->get_id_fb_user());

								$global = 'no';
								foreach ($details as $row_details) {
									$product	= new product();
									$product->map($row_details->get_id_product());

									if($product->get_global()=='yes'){
										$global = 'yes';
									}
								}
						?>
								<tr>
									<td colspan="11" class="well text-center">
										<h4><?php echo $row->get_status_admin() == "" ? "ORDER PLACED" : $row->get_status_admin(); ?></h4>
									</td>
								</tr>
								<tr>
									<?php if($row->get_status_admin() == ""){ ?> <!-- only for orders placed -->
										<?php if($row->get_status_admin() == "" && strtotime($row->get_date_done()) < strtotime('-30 days')){ ?>
											<td colspan="11" class="alert alert-danger text-center">
												Order SLA is breached
											</td>
										<?php }else{ ?>
											<td colspan="11" class="alert alert-success text-center">
												Order processing is on schedule
											</td>
										<?php } ?>
									<?php } ?>
								</tr>
								<tr <?php if($global=='yes'){echo 'class="global"';} ?>>
									<td><?php echo $count; ?></td>
									<td>
										<div class="row">
											<div class="col-sm-4"><label for="name">id</label></div>
											<div class="col-sm-8"><?php echo $row->get_id_order(); ?></div>
										</div>
										<div class="row">
											<div class="col-sm-4"><label for="name">Date</label></div>
											<div class="col-sm-8"><?php echo $row->get_date_done(); ?></div>
										</div>
										<div class="row">
											<div class="col-sm-4"><label for="name">Amount</label></div>
											<div class="col-sm-8">₹<?php echo number_format($row->get_total_amount(), 2); ?></div>
										</div>
										<div class="row">
											<div class="col-sm-4"><label for="name">Discount</label></div>
											<div class="col-sm-8">₹<?php echo number_format($row->get_total_discount(), 2); ?></div>
										</div>
										<div class="row">
											<div class="col-sm-4"><label for="name">Voucher</label></div>
											<div class="col-sm-8">₹<?php echo number_format($row->get_total_discount_voucher(), 2); ?></div>
										</div>
										<div class="row">
											<div class="col-sm-4"><label for="name">Final amount</label></div>
											<div class="col-sm-8">
												<?php $final_amount = $row->get_total_amount()-$row->get_total_discount()-$row->get_total_discount_voucher(); ?>
												₹<?php echo number_format($final_amount, 2); ?>
											</div>
										</div>
									</td>
									<td>
										<div class="row">
											<div class="col-sm-4"><label for="name">Name</label></div>
											<div class="col-sm-8">
												<?php echo $order_address->get_name(); ?>

												<?php $fb_user_blacklist->set_id_fb_user($row->get_id_fb_user()); ?>
												<?php if($fb_user_blacklist->exists()){ ?>
													<br>
													<span class="label label-danger">Blacklisted</span>
												<?php } ?>
											</div>
										</div>	

										<div class="row">
											<div class="col-sm-4"><label for="name">Email</label></div>
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
											<div class="col-sm-8"><?php echo $row->get_courier_allocation(); ?></div>
										</div>

										<div class="row">
											<div class="col-sm-4"><label for="pin_code">Payment Method</label></div>
											<div class="col-sm-8">
												<?php
													if($row->get_free_order() === 'yes'){
														echo '<span class="label label-info">Free Order</span>';
													}else if($row->get_payment_method() == 'Cash on Delivery'){
														echo '<span class="label label-info" style="background-color:#B38D4B;">Cash on Delivery</span>';
													}else if($row->get_payment_method() == 'Pay Online'){
														echo '<span class="label label-success" style="background-color:#79D479;">Pay Online</span>';
													}
												?>
											</div>
										</div>
									</td>
									<td>
										<table style="width:100%;">
											<thead>
												<tr>
													<th style="width:300px;">Item Description</th>
													<th>Color</th>
													<th>Size</th>
													<th>Status</th>
													<th>Qty</th>
													<th>Amount</th>
													<th>Discount</th>
												</tr>
											</thead>
											<tbody>
											<?php
												foreach ($details as $row) {

													$product	= new product();
													$product->map($row->get_id_product());

													$purchase_order = new purchase_order();
													$purchase_order->map_by_order_and_product($row->get_id_order(), $row->get_id_order_detail());
											?>
											        <tr>
											        	<td class="item_description">
											        		<a target="_blank" href="<?php echo $product->get_link(); ?>">
											        			<img style="width:50px !important;" src="<?php echo $product->get_image_link(); ?>" alt="">
											        		</a>
											        		
											        		<div>
											        			<p class="item_name"><?php echo $product->get_name(); ?></p>
											        		</div>
															
															<?php if($product->get_global()=='yes'){ ?>
																<p><span class="label label-primary">Global</span></p>
															<?php } ?>
											        	</td>
											        	<td class="text-center">
											        		<?php if($row->get_color() != ""){ ?>
											        			<?php echo $row->get_color(); ?>
											        			<br>
											        			<span class="media-box-color"><span style="background:<?php echo strpos($row->get_color(), '#') !== false ? $row->get_color() : "#".$row->get_color(); ?>;"></span></span>
											        		<?php } ?>
											        	</td>
											        	<td><?php echo $row->get_size(); ?></td>
											        	<td>
											        		<?php if($row->get_sent() == 'yes'){ ?>
											        			<span class="label label-primary">Sent</span>
											        			<br>
											        		<?php } ?>
											        		
															<?php if($row->get_shipped() == 'yes'){ ?>
											        			<span class="label label-success">Shipped</span>
											        			<br>
											        		<?php } ?>
											        		
															<?php if($row->get_returned() == 'yes'){ ?>
											        			<span class="label label-danger">Returned</span>
											        			<br>
											        		<?php } ?>

											        		<?php if($row->get_refunded() == 'yes'){ ?>
											        			<span class="label label-warning">Refunded</span>
											        			<br>
											        		<?php } ?>
											        		
											        		<?php if($row->get_POmade() == 'yes'){ ?>
											        			<span class="label label-default">POmade</span>
											        		<?php } ?>
											        	</td>
											        	<td><?php echo $row->get_qty(); ?></td>
											        	<td class="text-right">₹<?php echo number_format($row->get_amount()*$row->get_qty(), 2); ?></td>
											        	<td class="text-right">₹<?php echo number_format($row->get_discount()*$row->get_qty(), 2); ?></td>
											        </tr>
											        <tr>
											        	<td colspan="10" class="alert alert-info">
											        		<strong>AWB:</strong> 
											        		<?php echo $purchase_order->get_awb_number(); ?>
															<br>
															<strong>Status:</strong> 
															 <?php echo $purchase_order->get_awb_status(); ?>
											        	</td>
											        </tr>
											<?php				
												}
											?>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="99">
										<div class="text-center">
											<a href="#" class="show_timeline btn btn-green btn-sm">Track my order</a>
										</div>
										<div class="row timeline">
											<div class="col-sm-12">
												<?php echo make_tracking_path($row->get_id_order()); ?>
											</div>
										</div>
									</td>
								</tr>
						<?php
							}
						?>
					</tbody>
				</table>

			<?php } ?>

		</div>
	</div>

	<script src="../includes/js/jquery-1.10.2.min.js"></script>

	<style>
		.timeline{display: none;}
	</style>
	<script>
		$('.show_timeline').on('click', function(e){
			e.preventDefault();
			console.log($(this).parents('.tr')[0]);
			$(this).parents('tr').find('.timeline').slideToggle();
		})
	</script>

	<script>
		function search(){
			if(document.form.id_order.value != "" || document.form.mobile_number.value != ""){
				document.form.submit();
			}else{
				alert('You must specify the order number or the mobile number');
			}
		}
	</script>
	
</body>
</html>
<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>
<?php require_once("../../classes/order_voucher.php"); ?>
<?php require_once("../../classes/voucher.php"); ?>
<?php require_once("../../classes/keyword.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/vendor_product_stock.php"); ?>
<?php require_once("../../classes/address.php"); ?>
<?php require_once("../../classes/fb_user_blacklist.php"); ?>

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
		$order_address 			= new order_address();
		$order_voucher 			= new order_voucher();
		$voucher 				= new voucher();
		$vendor_product_stock 	= new vendor_product_stock();
		$message 				= new message();
		$address 				= new address();
		$fb_user_blacklist 		= new fb_user_blacklist();

		$msj = "";
		if(isset($_POST['action']) && $_POST['action'] == '1'){//erase
			$error = false;
			$conn->beginTransaction();

			$id_order = $_POST['id_order'];
			$order->map($id_order);

			if(!$order->delete()){
				$error = true;
			}

			$order_detail->set_id_order($id_order);
			if(!$order_detail->delete_by_id_order()){
				$error = true;
			}

			$order_address->set_id_order_address($order->get_id_order_address());
			$order_address->set_id_fb_user($order->get_id_fb_user());
			if(!$order_address->delete()){
				$error = true;
			}

			if(!$order_voucher->delete_all_from_order($id_order)){
				$error = true;
			}

			if(!$voucher->delete_automatic_voucher($id_order)){
				$error = true;
			}

			if($error){
			  $conn->rollBack();
			  $msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  $conn->commit();
			}
		}else if(isset($_POST['action']) && $_POST['action'] == '2'){//return product
			$error = false;
			$conn->beginTransaction();
			$conn_reports->beginTransaction();

			$order->map($_POST['id_order']);
			$order_detail->map($_POST['id_order'], $_POST['id_order_detail']);

            /* INCREASE STOCK */
            $qty 				= $order_detail->get_qty();
            $color_				= str_replace(' ', '_', $order_detail->get_color());
			$size_				= str_replace(' ', '_', $order_detail->get_size());
			$current_stock 		= $vendor_product_stock->get_stock_each_product_lang( $order_detail->get_id_product_prestashop(), $color_, $size_ );
			
			if($vendor_product_stock->exists_product_lang($order_detail->get_id_product_prestashop(), $color_, $size_)){
				if( !$vendor_product_stock->update_stock($order_detail->get_id_product_prestashop(), $color_, $size_, ($current_stock+$qty) ) ){
					$error = true;
				}
			}

			/* RETURN PRODUCT */
			$order_detail->set_returned("yes");
            if($order_detail->update_returned() === false){
                $error = true;
            }

            /* SEND MESSAGE */
            $id_fb_user 		= $order->get_id_fb_user();

			$message->set_id_fb_user($id_fb_user);
			$message->set_id_message($message->max_id_message($id_fb_user));
			$message->set_message("We have received the returned product at out warehouse and the voucher will be issued shortly.");
			$message->set_from('system');
			$message->set_id_message_conversation("-1");

			if($message->insert()){
				/* #### SEND SMS #### */
				$message->send_SMS_by_id_fb_user($id_fb_user, $address);
				/* #### END SEND SMS #### */
			}else{
				$error = true;
			}

            if($error){
			  	$conn->rollBack();
			  	$conn_reports->rollBack();
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  	$conn->commit();
				$conn_reports->commit();
			}
		}else if(isset($_POST['action']) && $_POST['action'] == '3'){//refund product
			$error = false;
			$conn->beginTransaction();

			$order->map($_POST['id_order']);
			$order_detail->map($_POST['id_order'], $_POST['id_order_detail']);

			$product	= new product();
			$product->map($order_detail->get_id_product());

			/* RETURN PRODUCT */
			$order_detail->set_refunded("yes");
            if($order_detail->update_refunded() === false){
                $error = true;
            }

            /* CREATE VOUCHER */
            $voucher = new voucher();

			$voucher->set_id_voucher($voucher->max_id_voucher());
			$voucher->set_code(md5($voucher->get_id_voucher()));
			$voucher->set_emails("/".$_POST['voucher_email']."/");
			$voucher->set_till_date(date('Y-m-d', strtotime('+1 year')));
			$voucher->set_value_kind('amount');
			$voucher->set_value(abs(str_replace(",", "", $_POST['voucher_amount']))); //- here!
			$voucher->set_description($_POST['voucher_message']);
			$voucher->set_min_cart_value("0");
			$voucher->set_visibility("Y");
			
			if( !$voucher->insert() ){
				$error = true;
			}

            /* SEND MESSAGE */
            $id_fb_user 		= $order->get_id_fb_user();

			$message->set_id_fb_user($id_fb_user);
			$message->set_id_message($message->max_id_message($id_fb_user));
			$message->set_message("Dear Customer , As we are unable to deliver the product - ".$product->get_name()." - , within the SLA period, the amount is being refunded to your Miracas Account. You can use this to purchase any product of your choosing from the store. We apologize for the inconvenience this may have caused you.");
			$message->set_from('system');
			$message->set_id_message_conversation("-1");

			if($message->insert()){
				/* #### SEND SMS #### */
				$message->send_SMS_by_id_fb_user($id_fb_user, $address);
				/* #### END SEND SMS #### */
			}else{
				$error = true;
			}

            if($error){
			  	$conn->rollBack();
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  	$conn->commit();
			  	$msj = '<div class="alert alert-success"><strong>Great</strong>, the refund voucher has been created.</div>';
			}
		}

		$default_status = "ORDERS_PLACED";
		if(isset($_POST['status'])){
			$default_status = $_POST['status'];
		}

		/* #### FILTER BY STATUS #### */

		$sql_status 	= "";
		if($default_status == "ORDERS_PLACED"){
			$sql_status = " and status_admin = '' ";
		}else if($default_status == "PROCESSING_ORDERS"){
			$sql_status = " and status_admin = 'PROCESSING ORDER' ";
		}else if($default_status == "ORDERS_SHIPPED"){
			$sql_status = " and status_admin = 'ORDER SHIPPED' ";
		}else if($default_status == "ORDERS_CANCELLED"){
			$sql_status = " and status_admin = 'ORDER CANCELLED' ";
		}else if($default_status == "ORDER_PARTIALLY_SHIPPED"){
			$sql_status = " and status_admin = 'ORDER PARTIALLY SHIPPED' ";
		}else if($default_status == "ORDERS_REFUSED"){
			$sql_status = " and status_admin = 'ORDER REFUSED' ";
		}else if($default_status == "ORDERS_ON_HOLD"){
			$sql_status = " and status_admin = 'ORDER ON HOLD' ";
		}

		/* #### FILTER BY DATE #### */

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

		/* #### FILTER BY EMAIL #### */

		$email 			= "";
		if(isset($_POST['email'])){
			$email 		= $_POST['email'];
		}

		$sql_email 		= "";
		if($email != ""){
			$sql_email 	= " and (select email from order_address where id_order_address=`order`.id_order_address and id_fb_user=`order`.id_fb_user) like '%$email%' ";
		}

		/* #### FILTER BY MOBILE #### */

		$mobile_number 			= "";
		if(isset($_POST['mobile_number'])){
			$mobile_number 		= $_POST['mobile_number'];
		}

		$sql_mobile_number 		= "";
		if($mobile_number != ""){
			$sql_mobile_number 	= " and (select mobile_number from order_address where id_order_address=`order`.id_order_address and id_fb_user=`order`.id_fb_user) like '%$mobile_number%' ";
		}

		/* #### FILTER BY ID ORDER #### */

		$filter_id_order 			= "";
		if(isset($_POST['filter_id_order'])){
			$filter_id_order 		= $_POST['filter_id_order'];
		}

		$sql_id_order 		= "";
		if($filter_id_order != ""){
			$sql_id_order 	= " and id_order = '$filter_id_order' ";
		}

		/* #### ONLY ORDERS THAT DOESN'T HAVE ESPRESSO DETAILS/PRODUCTS #### */

		$sql_normal_only 	= " and (select coalesce(count(*),0) from order_detail where id_order=order.id_order and order_type = 'espresso') = 0 ";
		
		/* #### APPLY FILTERS TO THE ORDERS #### */

		$orders = $order->get_list(" $sql_status $sql_dates $sql_email $sql_mobile_number $sql_id_order $sql_normal_only order by date_done desc "); 
	?>
	
	<style>
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
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Orders
			</h2>

			<p>
				<a href="check_url.php" class="btn btn-sm btn-gray">Check Url</a>
			</p>

			<form action="" method="post" name="form">
				
				<br>

				<input type="hidden" name="status" value="<?php echo $default_status; ?>">
				
				<div class="well" style="background:white;">

					<div class="row">
						<div class="col-sm-3">
							<p>
								<label for="from_date">From date</label>
								<input value="<?php echo $from_date; ?>" class="custom-input" id="from_date" name="from_date" type="text" maxlength="200" placeholder="click here and choose a date!">
							</p>

							<p>
								<label for="till_date">Till date</label>
								<input value="<?php echo $till_date; ?>" class="custom-input" id="till_date" name="till_date" type="text" maxlength="200" placeholder="click here and choose a date!">
							</p>
						</div>	
						<div class="col-sm-3">
							<p>
								<label for="email">Email</label>
								<input value="<?php echo $email; ?>" class="custom-input" id="email" name="email" type="text" maxlength="200" placeholder="write an email">
							</p>
						</div>
						<div class="col-sm-3">
							<p>
								<label for="mobile_number">Mobile number</label>
								<input value="<?php echo $mobile_number; ?>" class="custom-input" id="mobile_number" name="mobile_number" type="text" maxlength="200" placeholder="write a mobile number">
							</p>
						</div>	
						<div class="col-sm-3">
							<p>
								<label for="filter_id_order">Id Order</label>
								<input value="<?php echo $filter_id_order; ?>" class="custom-input" id="filter_id_order" name="filter_id_order" type="text" maxlength="200" placeholder="write the id order">
							</p>
						</div>	
					</div>
					
					<a href="javascript:document.form.submit();" class="btn btn-primary">Filter orders</a>
				</div>


				<br>
				<ul class="nav nav-tabs">
				  <li role="presentation" <?php if($default_status==""){echo 'class="active"';} ?>><a href="javascript:refresh_status('');">All</a></li>
				  <li role="presentation" <?php if($default_status=="ORDERS_PLACED"){echo 'class="active"';} ?>><a href="javascript:refresh_status('ORDERS_PLACED');">Orders Placed</a></li>
				  <li role="presentation" <?php if($default_status=="PROCESSING_ORDERS"){echo 'class="active"';} ?>><a href="javascript:refresh_status('PROCESSING_ORDERS');">Processing Orders</a></li>
				  <li role="presentation" <?php if($default_status=="ORDERS_SHIPPED"){echo 'class="active"';} ?>><a href="javascript:refresh_status('ORDERS_SHIPPED');">Orders Shipped</a></li>
				  <li role="presentation" <?php if($default_status=="ORDER_PARTIALLY_SHIPPED"){echo 'class="active"';} ?>><a href="javascript:refresh_status('ORDER_PARTIALLY_SHIPPED');">Orders Partially Shipped</a></li>
				  <li role="presentation" <?php if($default_status=="ORDERS_REFUSED"){echo 'class="active"';} ?>><a href="javascript:refresh_status('ORDERS_REFUSED');">Orders Refused</a></li>
				  <li role="presentation" <?php if($default_status=="ORDERS_ON_HOLD"){echo 'class="active"';} ?>><a href="javascript:refresh_status('ORDERS_ON_HOLD');">Orders On Hold</a></li>
				  <li role="presentation" <?php if($default_status=="ORDERS_CANCELLED"){echo 'class="active"';} ?>><a href="javascript:refresh_status('ORDERS_CANCELLED');">Orders Cancelled</a></li>
				</ul>
				<br>

				<?php echo $msj; ?>
			
				<input type="hidden" name="action" />
				<input type="hidden" name="id_order" />
				<input type="hidden" name="id_order_detail" />

				<table class="table table-condensed table-bordered table-striped" id="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Order</th>
							<th>Address</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$qty_cod 		= 0;
							$qty_online 	= 0;
							$qty_free 		= 0;

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
											<div class="col-sm-4"><label for="name">Shipping</label></div>
											<div class="col-sm-8">₹<?php echo number_format($row->get_shipping_fee(), 2); ?></div>
										</div>
										<div class="row">
											<div class="col-sm-4"><label for="name">Cod</label></div>
											<div class="col-sm-8">₹<?php echo number_format($row->get_cod_fee(), 2); ?></div>
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
												<?php $final_amount = $row->get_total_amount()+$row->get_shipping_fee()+$row->get_cod_fee()-$row->get_total_discount()-$row->get_total_discount_voucher(); ?>
												₹<?php echo number_format($final_amount, 2); ?>
											</div>
										</div>

										<hr>

										<p>
											<select class="form-control" style="display:inline-block;width:150px;">
												<option <?php if($row->get_status_admin() == ""){echo "selected";} ?> value="">ORDER PLACED</option>
												<option <?php if($row->get_status_admin() == "PROCESSING ORDER"){echo "selected";} ?> value="PROCESSING ORDER">PROCESSING ORDER</option>
												<option <?php if($row->get_status_admin() == "ORDER SHIPPED"){echo "selected";} ?> value="ORDER SHIPPED">ORDER SHIPPED</option>
												<option <?php if($row->get_status_admin() == "ORDER PARTIALLY SHIPPED"){echo "selected";} ?> value="ORDER PARTIALLY SHIPPED">ORDER PARTIALLY SHIPPED</option>
												<option <?php if($row->get_status_admin() == "ORDER REFUSED"){echo "selected";} ?> value="ORDER REFUSED">ORDER REFUSED</option>
												<option <?php if($row->get_status_admin() == "ORDER ON HOLD"){echo "selected";} ?> value="ORDER ON HOLD">ORDER ON HOLD</option>
												<option <?php if($row->get_status_admin() == "ORDER CANCELLED"){echo "selected";} ?> value="ORDER CANCELLED">ORDER CANCELLED</option>
											</select>
											<br>
											<br>
											<a href="#" data-idorder="<?php echo $row->get_id_order(); ?>" class="btn btn-sm btn-green update-status">Update</a>
											<a href="javascript:erase('<?php echo $row->get_id_order(); ?>');" class="btn btn-red btn-sm"><i class="glyphicon glyphicon-trash"></i></a>
										</p>
										<p>
											<a class="btn btn-sm btn-gray" href="javascript:open_message_modal('<?php echo $row->get_id_fb_user(); ?>');">
												Send Notification
											</a>
											<div style="margin-bottom:5px;"></div>
											<a class="btn btn-sm btn-gray" href="../messages/conversation_history.php?id_fb_user=<?php echo $row->get_id_fb_user(); ?>">
												Conversation History
											</a>
										</p>

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
														$qty_free+=$final_amount;
														echo '<span class="label label-info">Free Order</span>';
													}else if($row->get_payment_method() == 'Cash on Delivery'){
														$qty_cod+=$final_amount;
														echo '<span class="label label-info" style="background-color:#B38D4B;">Cash on Delivery</span>';
													}else if($row->get_payment_method() == 'Pay Online'){
														$qty_online+=$final_amount;
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
											?>
											        <tr>
											        	<td class="item_description">
											        		<a target="_blank" href="<?php echo $product->get_link(); ?>">
											        			<img style="width:50px !important;" src="<?php echo $product->get_image_link(); ?>" alt="">
											        		</a>
											        		
											        		<div>
											        			<p class="item_name"><?php echo $product->get_name(); ?></p>
											        		</div>

											        		<div>
											        			<?php 
											        				$keyword = new keyword();
											        				$keyword->map( substr($product->get_keywords(), 1, -1) );
											        			?>
											        			<p class="badge badge-info"><?php echo $keyword->get_keyword(); ?></p>
											        		</div>
															
															<?php if($product->get_global()=='yes'){ ?>
																<p><span class="label label-primary">Global</span></p>
															<?php } ?>

											        		<?php if($row->get_shipped() == 'yes' && $row->get_returned() == ''){ ?>
											        			<a style="margin-bottom:5px;" href="javascript:mark_as_returned('<?php echo $row->get_id_order(); ?>','<?php echo $row->get_id_order_detail(); ?>');" class="btn btn-sm btn-red">Mark as returned</a>
											        		<?php } ?>
											        		<?php if($row->get_refunded() == ''){ ?>
																
																<?php 
																	$product_total 		= ($row->get_amount()*$row->get_qty()) - ($row->get_discount()*$row->get_qty());

																	$order_vouchers 	= $order_voucher->get_list($row->get_id_order(), "");
																	$product_voucher 	= 0;
																	foreach ($order_vouchers as $row_voucher) {
																		if($row_voucher->get_value_kind() == 'percentage'){
																			$product_voucher += round( (float)$row_voucher->get_value() * ($product_total/100), 2);
																		}
																	}

																	$new_voucher 		= number_format($product_total-$product_voucher, 2); // the product total minus the discount and minus any percentage voucher that the customer may used
																?>

																<a href="javascript:modal_refund_voucher('<?php echo $row->get_id_order(); ?>','<?php echo $row->get_id_order_detail(); ?>', '<?php echo $new_voucher; ?>', '<?php echo $row->get_id_product_prestashop(); ?>', '<?php echo $order_address->get_email(); ?>');" class="btn btn-sm btn-blue">Give refund voucher</a>

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
											<?php				
												}
											?>
											</tbody>
										</table>
									</td>
								</tr>
						<?php
							}
						?>
					</tbody>
				</table>

				<br><br>
<!--
				<div class="well">
					<table>
						<tr>
							<td style="width:150px;">Cash on Delivery</td>
							<td><?php echo number_format($qty_cod, 2); ?></td>
						</tr>
						<tr>
							<td>Pay Online</td>
							<td><?php echo number_format($qty_online, 2); ?></td>
						</tr>
						<tr>
							<td>Free Order</td>
							<td><?php echo number_format($qty_free, 2); ?></td>
						</tr>
						<tr>
							<td>TOTAL</td>
							<td><?php echo number_format($qty_cod+$qty_online+$qty_free, 2); ?></td>
						</tr>
					</table>
				</div>
-->

			<div class="modal fade" id="modal-notification">
			  	<div class="modal-dialog" role="document">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        		<h4 class="modal-title" id="gridSystemModalLabel">Send Notification</h4>
			      		</div>
			      		
			      		<div class="modal-body">
			      			<input type="hidden" id="fb_user" name="fb_user">
			        		<textarea name="current_message" id="current_message" cols="30" rows="10" class="form-control"></textarea>
			      		</div>
			      		
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-gray" data-dismiss="modal">Close</button>
			        		<a href="javascript:send_message();" id="send_msg" class="btn btn-primary">Send Notification</a>
			      		</div>
			    	</div><!-- /.modal-content -->
			  	</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			<div class="modal fade" id="modal-refund-voucher">
			  	<div class="modal-dialog" role="document">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        		<h4 class="modal-title" id="gridSystemModalLabel">Give Refund Voucher</h4>
		      		</div>
		      			
		      			<div class="modal-body">
		      				<p>
			        			<label for="voucher_email">Email</label>
			        			<input name="voucher_email" id="voucher_email" type="text" class="form-control" />
			        		</p>
		      				<p>
		      					<label for="voucher_message">Message</label>
			        			<textarea name="voucher_message" id="voucher_message" cols="30" rows="10" class="form-control"></textarea>
			        		</p>
			        		<p>
			        			<label for="voucher_amount">Amount</label>
			        			<input name="voucher_amount" id="voucher_amount" type="text" class="form-control" />
			        		</p>
			      		</div>
			      		
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-gray" data-dismiss="modal">Close</button>
			        		<a href="javascript:give_refund_voucher();" id="send_msg" class="btn btn-primary">Give refund voucher</a>
			      		</div>
			    	</div><!-- /.modal-content -->
			  	</div><!-- /.modal-dialog -->
			</div><!-- /.modal -->

			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../orders"]').parents('li').addClass('active');</script>

	<script>
		function open_message_modal(fb_user){
			$('#modal-notification').find('#fb_user').val(fb_user);
			$('#modal-notification').modal();
		}

		$('#modal-notification').on('shown.bs.modal', function () {
		    $('#current_message').focus();
		});

		function send_message(){
			var msg_input	= $('#current_message');
			var msg 		= msg_input.val();
			var id_fb_user 	= $('#fb_user').val();

			if(msg != ''){

				$('#send_msg').attr('disabled', true).html('Sending...');
				
				$.get('../messages/send_notification.php?id_fb_user='+id_fb_user+'&message='+msg, function(r){
					if(r.indexOf('ERROR') > -1){
						alert("Oops, something went wrong! try again!")
					}else{
						alert("Message Sent!");
						msg_input.val('');
						$('#modal-notification').modal('hide');
						$('#send_msg').removeAttr('disabled').html('Send Notification');
					}
				});
			}else{
				alert('Make sure to write some message!');
			}

			return false;
		}
	</script>

	<script>
		function erase(id_order){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_order.value = id_order;
				document.form.submit();
			}
		}	

		function mark_as_returned(id_order, id_order_detail){
			if( confirm("Are you sure?") ){
				document.form.action.value = "2";
				document.form.id_order.value = id_order;
				document.form.id_order_detail.value = id_order_detail;
				document.form.submit();
			}
		}

		function modal_refund_voucher(id_order, id_order_detail, new_voucher, id_product_prestashop, email){
			document.form.id_order.value 			= id_order;
			document.form.id_order_detail.value 	= id_order_detail;
			document.form.voucher_email.value 		= email;
			document.form.voucher_message.value 	= 'refund voucher:'+id_order+':'+id_product_prestashop;
			document.form.voucher_amount.value 		= new_voucher;

			$('#modal-refund-voucher').modal();
		}

		var giving_refund_voucher = false;
		function give_refund_voucher(){
			if( confirm("Are you sure?") && !giving_refund_voucher){
				giving_refund_voucher 		= true;
				document.form.action.value = "3";
				document.form.submit();
			}
		}

		$('.table').on('click', '.update-status', function(e){
			e.preventDefault();
			var $this 	= $(this);
			var status 	= $this.siblings('select').val();
			var idOrder = $this.data('idorder');

			if($this.hasClass('updating')){
				return;
			}

			$this.addClass('updating');
			$this.html("Updating");
			console.log('update_status.php?id_order='+idOrder+'&status='+status);
			$.get('update_status.php?id_order='+idOrder+'&status='+status, function(r){
				$this.removeClass('updating');
				$this.html("Update");
				console.log(r);
				if($.trim(r) == "ERROR"){
					alert('Oops, there was an error while updating the status, please try again!')
				}
			});
		});
	</script>

	<link rel="stylesheet" type="text/css" href="../includes/plugins/dataTable/media/css/jquery.dataTables.css">
	<script type="text/javascript" language="javascript" src="../includes/plugins/dataTable/media/js/jquery.dataTables.js"></script>

	<script>
		$(document).ready(function(){
			$('#table').DataTable({
				"pageLength": 100
			});
		});
	</script>

	<link href="../includes/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script src="../includes/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <script>
    	function refresh_status(status){
    		document.form.status.value = status;
    		document.form.submit();
    	}

    	$('#from_date, #till_date').datepicker({
    		 orientation: "bottom right",
    		 format: "yyyy-mm-dd"
    	});
    </script>
	
</body>
</html>
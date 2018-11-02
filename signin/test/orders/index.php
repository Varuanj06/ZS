<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/message.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../pay/payu/payuconfig.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>
	<?php require_once("../menu.php"); ?>
	<?php require_once("../sidebar.php"); ?>
	<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">
	
	<?php require_once("../message.php"); ?>

	<script>$('.nav-right a[href="../orders"]').addClass('selected');</script>

	<?php 
		$order 			= new order();
		$order_detail 	= new order_detail();
		$order_address 	= new order_address();
		$message 		= new message();

		$msg 	= "";

	/* ====================================================================== *
        	CANCEL ORDER
 	 * ====================================================================== */				

		if(isset($_POST['action']) && $_POST['action'] == '1'){

			$order->map($_POST['id_order']);

			if($order->get_payment_method()=='Cash on Delivery' && ($order->get_status_admin()=="" || $order->get_status_admin()=="ORDER PLACED")){
				if(!$order->update_status_admin('ORDER CANCELLED', $order->get_id_order())){
					$msg = "<div class='alert alert-danger'>Something went wrong, please try again.<div>";
				}else{
					// ##### SEND AUTO MESSAGE #####
					$message->send_auto_message($order, $order->get_id_order(), 'ORDER CANCELLED');
					$message->send_SMS_by_order($order, $order->get_id_order(), $order_address);
					// ##### END SEND AUTO MESSAGE #####
				}
			}else{
				$msg = "<div class='alert alert-danger'>Something went wrong, please try again.<div>";
			}

		}

	/* ====================================================================== *
        	RETURN REQUEST
 	 * ====================================================================== */			

		else if(isset($_POST['action']) && $_POST['action'] == '2'){
			
			$order_detail->set_return_request_from_customer("request");
			$order_detail->set_id_order($_POST['id_order']);
			$order_detail->set_id_order_detail($_POST['id_order_detail']);
			if($order_detail->update_return_request_from_customer()){

				$message->send_message(new order(), new address(), $_POST['id_order'], $msg_request);

				$msg = "<div class='alert alert-success'>Return request raised for your product</div>";
			}else{
				$msg = "<div class='alert alert-danger'>Something went wrong, please try again.</div>";
			}

		}

	/* ====================================================================== *
        	PAY ONLINE (USING PAYU)
 	 * ====================================================================== */				

		else if(isset($_POST['action']) && $_POST['action'] == '3'){ // PAY ONLINE

			$order->map($_POST['id_order']);

			if($order->get_payment_method()=='Cash on Delivery' && ($order->get_status_admin()=="" || $order->get_status_admin()=="ORDER PLACED")){

				$order_address 		= new order_address();
				$payu_txnid    		= substr(hash('sha256', mt_rand() . microtime()), 0, 20);

				$order->update_payu_transaction($payu_txnid, $_POST['id_order']);
				$order_address->map($order->get_id_order_address(), $order->get_id_fb_user());

				$payu_amount 			= $order->get_total_amount()-$order->get_total_discount()-$order->get_total_discount_voucher();	
				$payu_first_name 		= $order_address->get_name();
				$payu_product_info 		= "ORDER ".$_POST['id_order'];
				$payu_email 			= $order_address->get_email();
				$payu_phone 			= $order_address->get_mobile_number();

				if($payu_amount=='' || $payu_first_name=='' || $payu_product_info=='' || $payu_email=='' || $payu_phone==''){
					$msg = "<div class='alert alert-danger'>The order #".$_POST['id_order']." is missing some of the following required parameters: <strong>amount, name, email or phone</strong>.</div>";
				}else{

					$success_url 	= $orders_success_url;
  					$failure_url 	= $orders_failure_url;
					require_once("../pay/payu/payu.php");

				}

			}else{
				$msg = "<div class='alert alert-danger'>The order #".$_POST['id_order']." is not valid for online payment.</div>";
			}

		}

	/* ====================================================================== *
        	PAYU RESPONSE
 	 * ====================================================================== */				

		if(isset($_POST["status"]) && isset($_POST["firstname"]) && isset($_POST["amount"]) && isset($_POST["txnid"]) && isset($_POST["hash"]) && isset($_POST["key"]) && isset($_POST["productinfo"]) && isset($_POST["email"])){
			
			$status         = $_POST["status"];
			$first_name     = $_POST["firstname"];
			$amount         = $_POST["amount"];
			$txnid          = $_POST["txnid"];
			$hash           = $_POST["hash"];
			$key            = $_POST["key"];
			$product_info   = $_POST["productinfo"];
			$email          = $_POST["email"];

			$generate_hash  = '';
			if(isset($_POST["additionalCharges"])){
				$additionalCharges  = $_POST["additionalCharges"];
				$generate_hash         = "$additionalCharges|$payu_salt|$status|||||||||||$email|$first_name|$product_info|$amount|$txnid|$key";
			}else{	  
				$generate_hash         = "$payu_salt|$status|||||||||||$email|$first_name|$product_info|$amount|$txnid|$key";
			}
			$generate_hash = hash("sha512", $generate_hash);
			 
			if ($hash != $generate_hash) {
				$msg = "<div class='alert alert-danger'>Invalid Transaction. Please try again</div>";
			}else{

				if(isset($_GET['payu_success'])){

					$order = new order();
					$order->map_by_payu_transaction($txnid);
					$order->update_payment_method('Pay Online', $order->get_id_order());

					$msg = "<div class='alert alert-success'>The transaction of the order #".$order->get_id_order()." was $status. Your order will soon be shipped.</div>";

				}else if(isset($_GET['payu_failure'])){
					
					$msg = "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";

				}	

			}  

		}

	/* ====================================================================== *
        	GET ORDERS
 	 * ====================================================================== */				

		$orders 		= $order->get_list_per_user($user['id'], " order by date_done desc "); 
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
	h4{
		margin: 5px !important;
	}
	@media only screen and (max-width: 768px) {
		.text-right{
			margin-top: 10px;
			text-align: left !important;
		}
	}
	.title-right{
		position: relative;
		top: -3px;
	}
	</style>

	<?php require_once("tracking_path.php"); ?>

	<div class="tabs-container">

		<h2>
			Your Orders
		</h2>

		<?php echo $msg; ?>

		<br>

		<form action="" method="post" name="form">
			<input type="hidden" name="action">
			<input type="hidden" name="id_order">
			<input type="hidden" name="id_order_detail">
		</form>
		
		<?php
			$count = 0;
			foreach ($orders as $row){
				$count++;

				$details = $order_detail->get_list($row->get_id_order(), " order by id_product ");
				$order_address->map($row->get_id_order_address(), $row->get_id_fb_user());

				$date_order = strtotime($row->get_date_done()); 
		?>
				
				<div class="cart-item">

					<div class="cart-head">
						
						<div class="row">
							<div class="col-sm-6">
								ORDER PLACED ON
								<?php echo date('M d, Y', $date_order); ?>
								
								<?php if($row->get_payment_method()=='Cash on Delivery' && ($row->get_status_admin()=="" || $row->get_status_admin()=="ORDER PLACED")){ ?>
								<br><a href="javascript:cancel_order('<?php echo $row->get_id_order(); ?>');"><i class="fa fa-remove"></i> Cancel this order</a>
								<?php } ?>
							</div>
						
							<div class="col-sm-6 text-right">
								<?php if($row->get_payment_method()=='Cash on Delivery' && ($row->get_status_admin()=="" || $row->get_status_admin()=="ORDER PLACED")){ ?>
									<a href="javascript:pay_online('<?php echo $row->get_id_order(); ?>');" class="btn btn-green btn-sm">Pay Online</a> &nbsp;
								<?php } ?>
								<a href="#" class="show_timeline btn btn-green btn-sm">Track my order</a> &nbsp;
								<span class="title-right">
									ORDER 
									#<?php echo $row->get_id_order(); ?>
									|
									<?php echo $row->get_status_admin()==""?"ORDER PLACED":$row->get_status_admin(); ?>
								</span>
							</div>
						</div>
						
					</div>

					<div class="row timeline">
						<div class="col-sm-12">
							<?php echo make_tracking_path($row->get_id_order()); ?>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">

							<h4>ITEMS</h4>

							<table style="width:100%;" class="table table-condensed table-striped">
								<thead>
									<tr>
										<th style="width:70%;">Description</th>
										<th>Color</th>
										<th>Size</th>
										<th>Quantity</th>
									</tr>
								</thead>
								<tbody>
								<?php
									foreach ($details as $row_detail) {

										$product	= new product();
										$product->map($row_detail->get_id_product());
								?>
								        <tr>
								        	<td class="item_description">
								        		<a target="_blank" href="<?php echo $product->get_link(); ?>">
								        			<img style="width:50px !important;" src="<?php echo $product->get_image_link(); ?>" alt="">
								        		</a>
								        		
								        		<div>
								        			<p class="item_name">
								        				<?php echo $product->get_name(); ?>
								        				<br>
								        				<?php if($row_detail->get_return_request_from_customer() == 'rejected'){ ?>
															<span class="label label-danger">Return request rejected</div>
								        				<?php }else if($row_detail->get_return_request_from_customer() == 'done'){ ?>
															<span class="label label-info">Reverse pickup arranged</div>
								        				<?php }else if($row_detail->get_return_request_from_customer() == 'request'){ ?>
															<span class="label label-warning">Return request raised for this product</div>
								        				<?php }else if($row_detail->get_sent() == 'yes'){ ?>
															<a href="javascript:return_product('<?php echo $row_detail->get_id_order(); ?>','<?php echo $row_detail->get_id_order_detail(); ?>');" class="btn btn-sm btn-gray">
																Return this product
															</a>
														<?php } ?>

														<?php if($row_detail->get_returned() != ""){ ?>
															<span class="label label-primary">Returned</span>
														<?php } ?>
														
														<?php if($row_detail->get_refunded() != ""){ ?>
															<span class="label label-default">Refunded</span>
														<?php } ?>
								        			</p>
								        		</div>
								        	</td>
								        	<td>
								        		<?php if($row_detail->get_color() != ""){ ?>
								        			<span class="media-box-color"><span style="background:<?php echo $row_detail->get_color(); ?>;"></span></span>
								        		<?php } ?>
								        	</td>
								        	<td><?php echo $row_detail->get_size(); ?></td>
								        	<td><?php echo $row_detail->get_qty(); ?></td>
								        </tr>
								<?php				
									}
								?>
								</tbody>
							</table>

							<?php 
								$total_ammount = ($row->get_total_amount()-$row->get_total_discount()-$row->get_total_discount_voucher());
								foreach ($relationship_managers as $row_manager) { 
									$explode = explode("-", $row_manager['range']);
									if($total_ammount>=$explode[0] && $total_ammount<=$explode[1]){
							?>
										<div class="manager_card" style="margin:0;width:100%;">
											<div class="manager_card_title">
												<img src="<?php echo $row_manager['img_src']; ?>" alt="" />
												<div class="manager_card_title_text">
													<?php echo $row_manager['name']; ?><br><?php echo $row_manager['email']; ?>
												</div>
											</div>
											I am your relationship manager and will help you fulfil this order. Feel free to drop a mail with your questions or concerns and I will be happy to help. You can ask about product details, payment methods, status of the order, etc.
										</div>
							<?php
										break;
									}
								}
							?>

						</div>
						<div class="col-sm-2">
							
							<h4>PAYMENT</h4>

							<label style="display:block;margin-top:5px;">Amount</label>

							₹<?php echo  number_format($row->get_total_amount(), 2); ?>

							<label style="display:block;margin-top:5px;">Discount</label>

							₹<?php echo  number_format($row->get_total_discount(), 2); ?>

							<label style="display:block;margin-top:5px;">Voucher</label>

							₹<?php echo  number_format($row->get_total_discount_voucher(), 2); ?>

							<label style="display:block;margin-top:5px;">Final amount</label>

							₹<?php echo  number_format($row->get_total_amount()-$row->get_total_discount()-$row->get_total_discount_voucher(), 2); ?>

							<label style="display:block;margin-top:5px;">Payment method</label>

							<?php echo $row->get_payment_method(); ?>

						</div>
						<div class="col-sm-4">
							
							<h4>SHIP TO</h4>

							<div class="row">
								<div class="col-sm-4"><label for="name">Name</label></div>
								<div class="col-sm-8"><?php echo $order_address->get_name(); ?></div>
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
								<div class="col-sm-4"><label for="email">Email</label></div>
								<div class="col-sm-8"><?php echo $order_address->get_email(); ?></div>
							</div>

						</div>
					</div>	

				</div>
				<br>
		<?php
			}
		?>
				
	</div>

	</div></div>
	<?php require_once("../footer.php"); ?>

	<script>
		function cancel_order(id_order){
			if(confirm("Are you sure? this action can't be undone")){
				document.form.action.value = "1";
				document.form.id_order.value = id_order;
				document.form.submit();
			}
		}

		function return_product(id_order, id_order_detail){
			if(confirm("Are you sure? this action can't be undone")){
				document.form.action.value 			= "2";
				document.form.id_order.value 		= id_order;
				document.form.id_order_detail.value = id_order_detail;
				document.form.submit();
			}
		}

		function pay_online(id_order){
			if(confirm("Are you sure?")){
				document.form.action.value = "3";
				document.form.id_order.value = id_order;
				document.form.submit();
			}
		}
	</script>
	
	<style>
		.timeline{display: none;}
	</style>
	<script>
		$('.show_timeline').on('click', function(e){
			e.preventDefault();
			$(this).parents('.cart-item').find('.timeline').slideToggle();
		})
	</script>
	
	</div>
</body>
</html>
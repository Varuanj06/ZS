<?php require_once("../fb_validator.php"); ?>
<?php require_once("../session_check.php"); ?>
<?php require_once("../dbconnect.php"); ?>
<?php require_once("../classes/order.php"); ?>
<?php require_once("../classes/order_detail.php"); ?>
<?php require_once("../classes/order_address.php"); ?>
<?php require_once("../classes/address.php"); ?>
<?php require_once("../classes/product.php"); ?>
<?php require_once("../classes/product_lang.php"); ?>
<?php require_once("../classes/functions.php"); ?>
<?php require_once("../classes/order_voucher.php"); ?>
<?php require_once("../classes/message.php"); ?>
<?php require_once("confirm_order.php"); ?>

<!doctype html>
<html lang="en">
<head>

  	<?php require_once("../head.php"); ?>

	<?php
		$id_fb_user 		= $user['id'];

	/* ====================================================================== *
        	CLASSES
 	 * ====================================================================== */			

		$order 				= new order();
		$order_detail 		= new order_detail();
		$order_address 		= new order_address();
		$address 			= new address();
		$message 			= new message();

	/* ====================================================================== *
        	CURRENT ORDER AND CURRENT DETAILS
 	 * ====================================================================== */	

		$current_id_order   = "";
		$details 			= array();
		if($order->get_id_order_by_fb_user($id_fb_user)){
			$current_id_order = $order->get_id_order_by_fb_user($id_fb_user);
			$details = $order_detail->get_list($current_id_order, " order by id_product ");
		}
		$order->map($current_id_order);

	/* ====================================================================== *
        	CURRENT ADDRESS
 	 * ====================================================================== */		

		$addresses 			= $address->get_list($id_fb_user, " order by date_update desc ");
		$current_address 	= "";
		foreach ($addresses as $row){ 
			$current_address = $row->get_id_address();
			break;
		}
		$address = new address();
		$address->map($current_address, $id_fb_user);

	/* ====================================================================== *
        	CHECK SOME STUFF
 	 * ====================================================================== */		

        /* YOU NEED SOME THINGS TO BE HERE */	

		if(!isset($_GET['c'])){
			if($current_id_order == "" || $current_address == "" || count($details) == 0 || $order->get_payment_method() == ''){
				echo "<script>location.href='../pay';</script>";
				exit();
			}
		}

		/* REMOVE EXPIRED VOUCHERS */

		$order_voucher 		= new order_voucher();
		$order_voucher->delete_expired_vouchers($current_id_order);

	/* ====================================================================== *
        	CONFIRM ORDER
 	 * ====================================================================== */			

		$error = "";
		if(isset($_POST['action']) && $_POST['action'] == '1'){//confirm
			
			if($order->get_payment_method() == 'Cash on Delivery'){

				if(confirm_order($id_fb_user, $current_id_order, $details, $current_address, false)){
				  	// all good
				}else{
					$error = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
				}

			}else{
				$error = '<div class="alert alert-danger"><strong>Oops</strong>, you can only confirm "Cash on Delivery" orders in this page</div>';
			}

		}
	?>
	
	<style>
		.row{
			margin-bottom: 10px;
		}
		label{
			font-weight: 600;
		}
		.item_description img {
		  float: left;
		  margin-right: 20px;
		  width: 100px;
		}
	</style>
</head>
<body>
	
<?php if (strpos($_SERVER['HTTP_HOST'], 'miracas.in') !== false) { ?>
	<?php require_once("../menu_global.php"); ?> 
	<?php require_once("../sidebar_global.php"); ?> 
<?php }else{ ?>
	<?php require_once("../menu.php"); ?> 
	<?php require_once("../sidebar.php"); ?> 
<?php } ?>
	
<div id="menu-page-wraper">

	<div class="page-wrap"><div class="page-wrap-inner">

	<?php require_once("../message.php"); ?>

	<script>$('.nav-right a[href="../cart"]').addClass('selected');</script>

	<div class="tabs-container">

	<!--
	/* ====================================================================== *
			WHEN IT COMES FROM ONLINE PAYMENT (JUST SHOW THE RECENT CONFIRMED ORDER)
	* ====================================================================== */			
	-->

		<?php if(isset($_GET['c'])){ ?>

			<div class="cart-item">
				<h2><i class="fa fa-check"></i> Thank you for your order</h2>
				<hr>
				<a href="../feed" class="btn btn-sm btn-green">Continue searching</a>
			</div>

			<br>

			<?php
				$orders 		= $order->get_list_per_user($user['id'], " order by date_done desc "); 
				foreach ($orders as $row){

					$details = $order_detail->get_list($row->get_id_order(), " order by id_product ");
					$order_address->map($row->get_id_order_address(), $row->get_id_fb_user());

					$date_order = strtotime($row->get_date_done());
			?>
					
					<div class="cart-item">


						<div class="cart-head">

							ORDER PLACED ON
							<?php echo date('M d, Y', $date_order); ?>
							
							<div class="pull-right">
								ORDER #<?php echo $row->get_id_order(); ?> | <?php echo $row->get_status_admin()==""?"ORDER PLACED":$row->get_status_admin(); ?>
							</div>
							
						</div>

						<div class="row">
							<div class="col-sm-8">

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
									        	</td>
									        	<td>
									        		<?php if($row->get_color() != ""){ ?>
									        			<span class="media-box-color"><span style="background:<?php echo $row->get_color(); ?>;"></span></span>
									        		<?php } ?>
									        	</td>
									        	<td><?php echo $row->get_size(); ?></td>
									        	<td><?php echo $row->get_qty(); ?></td>
									        </tr>
									<?php				
										}
									?>
									</tbody>
								</table>

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
					break; //only one!
				}
			?>

	<!--
	/* ====================================================================== *
			WHEN CONFIRMED FOR CASH ON DELIVERY
	 * ====================================================================== */			
	-->	

		<?php }else if(isset($_POST['action']) && $_POST['action'] == '1' && $error == ""){ ?>
			
			<div class="cart-item">
				<h2><i class="fa fa-check"></i> Thank you for your order</h2>
				<hr>
				<a href="../feed" class="btn btn-sm btn-green">Continue searching</a>
			</div>

	<!--
	/* ====================================================================== *
			CONFIRM FOR CASH ON DELIVERY
	 * ====================================================================== */			
	-->		

		<?php }else{ ?>

			<h2><i class="fa fa-check"></i> Confirm Order</h2>
			<a href="../pay" class="btn btn-sm btn-green">go back</a>	
			<br><br>

			<?php echo $error; ?>

			<div class="cart-item">
				<form action="" method="post" name="form">
					<input type="hidden" name="action">
					
					<h3>Details</h3>

					<hr>

					<table class="table table-striped">
						<thead>
							<tr>
								<th>Item Description</th>
								<th>Color</th>
								<th>Size</th>
								<th style="width:200px;">Quantity</th>
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
						        			<img style="width:100px !important;" src="<?php echo $product->get_image_link(); ?>" alt="">
						        		</a>
						        		
						        		<div>
						        			<p class="item_name"><?php echo $product->get_name(); ?></p>
						        		</div>
						        	</td>
						        	<td>
						        		<?php if($row->get_color() != ""){ ?>
						        			<span class="media-box-color"><span style="background:<?php echo $row->get_color(); ?>;"></span></span>
						        		<?php } ?>
						        	</td>
						        	<td><?php echo $row->get_size(); ?></td>
						        	<td><?php echo $row->get_qty(); ?></td>
						        </tr>
						<?php				
							}
						?>
						</tbody>
					</table>

					<h3>Ship to</h3>

					<hr>

					<div class="row">
						<div class="col-sm-2"><label for="name">Name</label></div>
						<div class="col-sm-6"><?php echo $address->get_name(); ?></div>
					</div>				
					
					<div class="row">
						<div class="col-sm-2"><label for="mobile_number">Mobile Number</label></div>
						<div class="col-sm-6"><?php echo $address->get_mobile_number(); ?></div>
					</div>
					
					<div class="row">
						<div class="col-sm-2"><label for="address_text">Address</label></div>
						<div class="col-sm-6"><?php echo $address->get_address(); ?></div>
					</div>
					
					<div class="row">
						<div class="col-sm-2"><label for="landmark">Landmark</label></div>
						<div class="col-sm-6"><?php echo $address->get_landmark(); ?></div>
					</div>
					
					<div class="row">
						<div class="col-sm-2"><label for="city">City</label></div>
						<div class="col-sm-6"><?php echo $address->get_city(); ?></div>
					</div>
					
					<div class="row">
						<div class="col-sm-2"><label for="state">State</label></div>
						<div class="col-sm-6"><?php echo $address->get_state(); ?></div>
					</div>
					
					<div class="row">
						<div class="col-sm-2"><label for="pin_code">Pin code</label></div>
						<div class="col-sm-6"><?php echo $address->get_pin_code(); ?></div>
					</div>

					<div class="row">
						<div class="col-sm-2"><label for="pin_code">Email</label></div>
						<div class="col-sm-6"><?php echo $address->get_email(); ?></div>
					</div>

					<h3>Payment</h3>
					<hr>
					<strong style="color:gray;"><?php echo $order->get_payment_method(); ?></strong>
							
				</form>
				<br>

				<hr>
				
				<?php if($order->get_payment_method() == 'Cash on Delivery'){ ?>
				<p>
					<a href="javascript:confirm_order();" class="btn btn-green btn-default">Confirm</a>
				</p>
				<?php } ?>
			</div>	

		<?php } ?>

	</div> <!-- End tabs-container -->
	
	
	<script>
		function confirm_order(){
			if(confirm("Are you sure?")){
				document.form.action.value = "1";
				document.form.submit();
			}
		}
	</script>

	</div></div>
	<?php require_once("../footer.php"); ?>

	</div>
</body>

</html>
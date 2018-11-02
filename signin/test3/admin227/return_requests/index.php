<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/address.php"); ?>

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
		$product 				= new product();
		$message 				= new message();

		$requests 				= $order_detail->get_return_requests("");
		$msg 					= "";
		
		if(isset($_POST['action']) && $_POST['action'] == '1'){

			$conn->beginTransaction(); //transaccion
			$error = false;

			foreach ($requests as $row) { 
				if(isset( $_POST['arrange_return_'.$row->get_id_order().'_'.$row->get_id_order_detail()] )){

					$order_detail->set_return_request_from_customer("done");
					$order_detail->set_id_order($row->get_id_order());
					$order_detail->set_id_order_detail($row->get_id_order_detail());
					if(!$order_detail->update_return_request_from_customer()){
						$error = true;
						break;
					}

					$message->send_message(new order(), new address(), $row->get_id_order(), $msg_approved);

				}
			}

			if($error == false){
				$msg = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully arranged reverse pickups.</div>';
				$conn->commit();
			}else{
				$msg = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				$conn->rollback();
			}

			$requests 				= $order_detail->get_return_requests("");

		}else if(isset($_POST['action']) && $_POST['action'] == '2'){

			$conn->beginTransaction(); //transaccion
			$error = false;

			foreach ($requests as $row) { 
				if(isset( $_POST['arrange_return_'.$row->get_id_order().'_'.$row->get_id_order_detail()] )){

					$order_detail->set_return_request_from_customer("rejected");
					$order_detail->set_id_order($row->get_id_order());
					$order_detail->set_id_order_detail($row->get_id_order_detail());
					if(!$order_detail->update_return_request_from_customer()){
						$error = true;
						break;
					}

					$message->send_message(new order(), new address(), $row->get_id_order(), $msg_rejected);

				}
			}

			if($error == false){
				$msg = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully rejected requests.</div>';
				$conn->commit();
			}else{
				$msg = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
				$conn->rollback();
			}

			$requests 				= $order_detail->get_return_requests("");

		}

	?>
	
	<style>
		.item_description img {
		  float: left;
		  margin-right: 20px;
		  width: 100px;
		}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Return Requests
			</h2>

			<?php echo $msg; ?>

			<form action="" method="post" name="form">

				<input type="hidden" name="action">

				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Action</th>
							<th>Id Order</th>
							<th>Address</th>
							<th>Product</th>
							<th>Color</th>
							<th>Size</th>
							<th>Quantity</th>
						</tr>
					</thead>
					<?php 
						$cont = 0;
						foreach ($requests as $row) { 
							$cont++;

							$product	= new product();
							$product->map($row->get_id_product());

							$order 		= new order();
							$order->map($row->get_id_order());

							$order_address->map($order->get_id_order_address(), $order->get_id_fb_user());
					?>
						<tr>
							<td><?php echo $cont; ?></td>
							<td>
								<input type="checkbox" name="arrange_return_<?php echo $row->get_id_order(); ?>_<?php echo $row->get_id_order_detail(); ?>" />
							</td>
							<td><?php echo $row->get_id_order(); ?></td>
							<td>
								<div class="row">
									<div class="col-sm-4"><label for="name">Name</label></div>
									<div class="col-sm-8"><?php echo $order_address->get_name(); ?></div>
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
							</td>
							<td class="item_description">
				        		<a target="_blank" href="<?php echo $product->get_link(); ?>">
				        			<img style="width:50px !important;" src="<?php echo $product->get_image_link(); ?>" alt="">
				        		</a>
				        		
				        		<div>
				        			<p class="item_name"><?php echo $product->get_name(); ?></p>
				        		</div>
				        	</td>
				        	<td class="text-center">
				        		<?php if($row->get_color() != ""){ ?>
				        			<?php echo $row->get_color(); ?>
				        			<br>
				        			<span class="media-box-color"><span style="background:<?php echo strpos($row->get_color(), '#') !== false ? $row->get_color() : "#".$row->get_color(); ?>;"></span></span>
				        		<?php } ?>
				        	</td>
				        	<td><?php echo $row->get_size(); ?></td>
				        	<td><?php echo $row->get_qty(); ?></td>
						</tr>
					<?php 
						} 
					?>
					<tr>
						<td>&nbsp;</td>
						<td>
							<a href="javascript:arrange_return();" class="btn btn-sm btn-green">Arrange Reverse Pickup</a>
							<a href="javascript:reject_request();" class="btn btn-sm btn-red">Reject Request</a>
						</td>
						<td colspan="6"></td>
					</tr>
				</table>

			</form>	
		
		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../return_requests"]').parents('li').addClass('active');</script>

	<script>
		function arrange_return(){
			if(confirm("Are you sure? this action can't be undone")){
				document.form.action.value = "1";
				document.form.submit();
			}
		}

		function reject_request(){
			if(confirm("Are you sure? this action can't be undone")){
				document.form.action.value = "2";
				document.form.submit();
			}	
		}
	</script>
	
</body>
</html>
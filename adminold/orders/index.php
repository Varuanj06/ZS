<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>
<?php require_once("../../classes/product.php"); ?>


<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 
		$order 			= new order();
		$order_detail 	= new order_detail();
		$order_address 	= new order_address();

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

			if($error){
			  $conn->rollBack();
			  $msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}else{
			  $conn->commit();
			}
		}

		$default_status = "ORDERS_PLACED";
		if(isset($_GET['status'])){
			$default_status = $_GET['status'];
		}

		$sql_status 	= "";
		if($default_status == "ORDERS_PLACED"){
			$sql_status = " and status_admin = '' ";
		}else if($default_status == "PROCESSING_ORDERS"){
			$sql_status = " and status_admin = 'PROCESSING ORDER' ";
		}else if($default_status == "ORDERS_SHIPPED"){
			$sql_status = " and status_admin = 'ORDER SHIPPED' ";
		}




		$orders = $order->get_list($sql_status." order by date_done desc "); 
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
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Orders
			</h2>

			<br>
			<ul class="nav nav-tabs">
			  <li role="presentation" <?php if($default_status==""){echo 'class="active"';} ?>><a href="./?status=">All</a></li>
			  <li role="presentation" <?php if($default_status=="ORDERS_PLACED"){echo 'class="active"';} ?>><a href="./?status=ORDERS_PLACED">Orders Placed</a></li>
			  <li role="presentation" <?php if($default_status=="PROCESSING_ORDERS"){echo 'class="active"';} ?>><a href="./?status=PROCESSING_ORDERS">Processing Orders</a></li>
			  <li role="presentation" <?php if($default_status=="ORDERS_SHIPPED"){echo 'class="active"';} ?>><a href="./?status=ORDERS_SHIPPED">Orders Shipped</a></li>
			</ul>
			<br>

			<?php echo $msj; ?>
			
			<form action="" method="post" name="form">
				<input type="hidden" name="action" />
				<input type="hidden" name="id_order" />

				<table class="table table-condensed table-bordered table-striped" id="table">
					<thead>
						<tr>
							<th>#</th>
							<th>id</th>
							<!--<th>fb user</th>-->
							<th style="width:300px;">date</th>
							<th style="width:300px;">Address</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$count = 0;
							foreach ($orders as $row){
								$count++;

								$details = $order_detail->get_list($row->get_id_order(), " order by id_product ");
								$order_address->map($row->get_id_order_address(), $row->get_id_fb_user());
						?>
								<tr>
									<td><?php echo $count; ?></td>
									<td><?php echo $row->get_id_order(); ?></td>
									<!--<td><?php echo $row->get_id_fb_user(); ?></td>-->
									<td>
										<?php echo $row->get_date_done(); ?>
										<hr>
										<select class="form-control" style="display:inline-block;width:180px;">
											<option <?php if($row->get_status_admin() == ""){echo "selected";} ?> value="">ORDER PLACED</option>
											<option <?php if($row->get_status_admin() == "PROCESSING ORDER"){echo "selected";} ?> value="PROCESSING ORDER">PROCESSING ORDER</option>
											<option <?php if($row->get_status_admin() == "ORDER SHIPPED"){echo "selected";} ?> value="ORDER SHIPPED">ORDER SHIPPED</option>
										</select>
										<a href="#" data-idorder="<?php echo $row->get_id_order(); ?>" class="btn btn-sm btn-green update-status">Update</a>
										<a href="javascript:erase('<?php echo $row->get_id_order(); ?>');" class="btn btn-red btn-sm"><i class="glyphicon glyphicon-trash"></i></a>
									</td>
									<td>
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
									</td>
									<td>
										<table style="width:100%;">
											<thead>
												<tr>
													<th style="width:300px;">Item Description</th>
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
											        	<td><?php echo $row->get_color(); ?></td>
											        	<td><?php echo $row->get_size(); ?></td>
											        	<td><?php echo $row->get_qty(); ?></td>
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

			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../orders"]').parents('li').addClass('active');</script>

	<script>
		function erase(id_order){
			if( confirm("Are you sure?") ){
				document.form.action.value = "1";
				document.form.id_order.value = id_order;
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
			$('#table').DataTable();
		});
	</script>
	
</body>
</html>
<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>
<?php require_once("../../classes/order_voucher.php"); ?>
<?php require_once("../../classes/voucher.php"); ?>
<?php require_once("../../classes/product.php"); ?>
<?php require_once("../../classes/message.php"); ?>
<?php require_once("../../classes/vendor_product.php"); ?>
<?php require_once("../../classes/address.php"); ?>
<?php require_once("../../includes/plugins/snoopy/Snoopy.class.php"); ?>

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
		$vendor_product 		= new vendor_product();
		$address 				= new address();

		/* #### FILTER BY DATE #### */

		$from_date 		= date("Y-m-d");
		$till_date 		= date("Y-m-d");

		if(isset($_POST['from_date']) && isset($_POST['till_date'])){
			$from_date 	= $_POST['from_date'];
			$till_date 	= $_POST['till_date'];
		}
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
	.table-condensed th, .table-condensed td{
		/*padding: 10px !important;
		font-size: 12px !important;*/
	}
	</style>

	<div class="section">
		<div class="content">

			<h2>
				Check Products Link
			</h2>

			<p>
				<a href="./" class="btn btn-sm btn-gray">Go back</a>
			</p>

			<form action="" method="post" name="form">
				
				<br>

				<input type="hidden" name="status" value="<?php echo $default_status; ?>">
				
				<div class="well" style="background:white;">

					<div class="row">
						<div class="col-sm-12">
							<p>
								<label for="from_date">From date</label>
								<input value="<?php echo $from_date; ?>" class="custom-input" id="from_date" name="from_date" type="text" maxlength="200" placeholder="click here and choose a date!">
							</p>

							<p>
								<label for="till_date">Till date</label>
								<input value="<?php echo $till_date; ?>" class="custom-input" id="till_date" name="till_date" type="text" maxlength="200" placeholder="click here and choose a date!">
							</p>
						</div>	
					</div>
					
					<a href="javascript:document.form.submit();" class="btn btn-primary">Check Products Link</a>
				</div>

				<?php if(isset($_POST['from_date']) && isset($_POST['till_date'])){ ?>

					<?php 

						$sql_dates 	= " and date_done between '$from_date 00:00:00' and '$till_date 23:59:59' ";
						
						$orders 	= $order->get_list(" $sql_dates order by date_done desc "); 
					?>

					<table class="table table-condensed table-bordered table-striped" id="table">
						<thead>
							<tr>
								<th>#</th>
								<th>id</th>
								<th style="width:300px;">Date</th>
								<th class="text-right">Final Amount</th>
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
							?>
									<tr>
										<td><?php echo $count; ?></td>
										<td><?php echo $row->get_id_order(); ?></td>
										<td><?php echo $row->get_date_done(); ?></td>
										<td class="text-right">
											₹<?php echo number_format($row->get_total_amount()-$row->get_total_discount()-$row->get_total_discount_voucher(), 2); ?>
										</td>
										<td>
											<br>

											<table style="width:100%;">
												<thead>
													<tr>
														<th style="width:300px;">Item Description</th>
														<th>Color</th>
														<th>Size</th>
														<th>Quantity</th>
														<th>Status</th>
														<th>Product link</th>
														<th>Valid link</th>
													</tr>
												</thead>
												<tbody>
												<?php
													foreach ($details as $row) {

														$product	= new product();
														$product->map($row->get_id_product());

												        $product_link 		= $vendor_product->get_product_link_lang($row->get_id_product_prestashop());
												        $exists 			= 'no';
												        
												        if(filter_var($product_link, FILTER_VALIDATE_URL) === FALSE){// this checks if the link is valid
															$exists = 'no';
														}else{
															$snoopy 		= new Snoopy;
															$snoopy->fetchtext($product_link);
															$page 			=  mb_convert_encoding($snoopy->results, 'utf-8', "gb18030");
															$response_code 	= $snoopy->response_code;

															//if(strpos($response_code, "404 Not Found") !== false || strpos($page, "商品已下架") !== false){
															if(strpos($response_code, "404 Not Found") !== false || strpos($response_code, "400 Bad Request") !== false || strpos($page, "商品已下架") !== false){
															    $exists 	= 'no';
															}else{
															    $exists 	= 'yes';
															}
														}
												        	
												?>
												        <tr <?php if($exists=='no'){echo "class='alert alert-danger'";} ?>>
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
												        		
												        		<?php if($row->get_POmade() == 'yes'){ ?>
												        			<span class="label label-default">POmade</span>
												        		<?php } ?>
												        	</td>
												        	<td><a target="_blank" href="<?php echo $product_link; ?>">Link</a></td>
												        	<td>
												        		<?php echo $exists; ?>
												        	</td>
												        </tr>
												<?php				
													}
												?>
												</tbody>
											</table>

											<br>
										</td>
									</tr>
							<?php
								}
							?>
						</tbody>
					</table>

				<?php } ?>

			</form>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../orders"]').parents('li').addClass('active');</script>

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
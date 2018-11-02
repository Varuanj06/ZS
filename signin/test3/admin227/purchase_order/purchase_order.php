<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/vendor_product.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>

	<style>
		.datepicker-dropdown{
			z-index: 9999999 !important;
		}
	</style>
</head>
<body>

	<?php require_once("../navbar.php"); ?>

	<?php 
		$id_vendor = isset($_SESSION['id_vendor'])?$_SESSION['id_vendor']:"";
		$vendor = new vendor();
		$vendor->set_id_vendor($id_vendor);
		if( $vendor->exists() == false ){
			echo "<script>location.href='../vendors';</script>";	
			exit();
		}
	?>

	<div class="section">
		<div class="content">

			<h2>Make a purchase order</h2>
			
			<p>
				<a href="../purchase_order" class="btn btn-default btn-gray btn-sm">Go back</a>	
				<a href="all_purchase_order.php" class="btn btn-default btn-green btn-sm">View All Purchase Orders</a>			
			</p>	
			
			<hr>
			<form action="" method="post" name="form">
				<input type="hidden" name="id_orders_selected" value="'#### nothing at all ####'">

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
					<a href="javascript:document.form.submit();" class="btn btn-default btn-large btn-green">Generate Order!</a>
				</p>
			</form>

			<?php if( isset($_POST['from_date']) && isset($_POST['till_date']) ){ ?>
				
				<?php
					require_once("functions.php");
					$final_result = make_purchase_order($_SESSION['id_vendor'], $_POST);

					$_SESSION['final_result'] = $final_result;
				?>
				
				<table class="table table-condensed table-bordered table-striped">
					<thead>
						<tr>
							<th style="width:5%;">Order Id</th>
							<th style="width:10%;;">Payment</th>
							<th style="width:55px;">Qty</th>
							<th>Thumbnail</th>
							<th>Link</th>
							<th>Id</th>
							<th>Item</th>
							<th>Color</th>
							<th>Size</th>
							<th>Asian Size</th>
						</tr>
					</thead>
					<tbody>
						<?php $total = 0; ?>
						<?php foreach ($final_result as $row) { ?>
							<?php $total += $row['qty']; ?>
							<tr>
								<td><?php echo $row['id_order']; ?></td>
								<td><?php echo $row['payment']; ?></td>
								<td><?php echo $row['qty']; ?></td>
								<td><img height="60px" src="<?php echo $row['thumb']; ?>" alt=""></td>
								<td>
									<a target="_blank" href="<?php echo $row['link']; ?>">
										Product Link
									</a>
								</td>
								<td><?php echo $row['id_product_lang']; ?></td>
								<td><?php echo $row['name']; ?></td>
								<td class="text-center">
									<?php if($row['color'] != ""){ ?>
					        			<?php echo $row['color']; ?>
					        			<br>
					        			<span class="media-box-color"><span style="background:<?php echo strpos($row['color'], '#') !== false ? $row['color'] : "#".$row['color']; ?>;"></span></span>
					        		<?php } ?>
								</td>
								<td><?php echo $row['size']; ?></td>
								<td><?php echo $row['asian_size']; ?></td>
							</tr>
						<?php } ?>
					</tbody>
					<tr>
						<th colspan="2">Total</th>
						<th><?php echo $total; ?></th>
						<th colspan="7"></th>
					</tr>
				</table>

				<p style="overflow:hidden;">
					<?php if(count($final_result)>0){ ?>
						<form name="form_save" action="save_purchase_order.php" method="post">
							<a href="javascript:document.form_save.submit();" class="pull-right btn btn-default btn-large btn-blue">Save Purchase Order</a>
							<input name="purchase_order_name" type="text" class="form-control pull-right" style="display:inline-block;width:200px;margin-right:10px;" placeholder="Purchase Order Name">
						</form>
					<?php } ?>
				</p>
		
			<?php }?>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../purchase_order"]').parents('li').addClass('active');</script>

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
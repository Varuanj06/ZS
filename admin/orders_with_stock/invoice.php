<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/order.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/order_invoice.php"); ?>
<?php require_once("../../classes/order_address.php"); ?>

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

	<div class="section">
		<div class="content">

			<h2>Invoice</h2>
			
			<p>
				<a href="../orders_with_stock" class="btn btn-default btn-gray btn-sm">Go back</a>		
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
					<a href="javascript:document.form.submit();" class="btn btn-default btn-large btn-green">Generate Invoices!</a>
				</p>
			</form>

			<?php if( isset($_POST['from_date']) && isset($_POST['till_date']) ){ ?>

				<?php 
					$from_date 		= $_POST['from_date']; 
					$till_date		= $_POST['till_date']; 
					
					$paymentSQL		= "";
					if( isset($_POST['payment1']) ){
						$paymentSQL .= " (select payment_method from `order` where id_order=order_detail.id_order) = '".$_POST['payment1']."' or";
					} 
					if( isset($_POST['payment2']) ){
						$paymentSQL .= " (select payment_method from `order` where id_order=order_detail.id_order) = '".$_POST['payment2']."' or";
					}
					if($paymentSQL != ""){
						$paymentSQL = " and (". substr($paymentSQL, 0, -2) . ") ";
					}

					$statusSQL 		= " and (select status_admin from `order` where id_order=order_detail.id_order) != 'ORDER REFUSED' ";

					$order 			= new order();
					$order_detail 	= new order_detail();
					$order_invoice 	= new order_invoice();
					$order_address 	= new order_address();

					$details 		= $order_detail->get_details_sent_in_date($from_date, $till_date, " $statusSQL $paymentSQL group by id_order, cast(sent_date as date) order by 1 ");

				?>
				
				<table class="table table-condensed table-bordered table-striped">
					<thead>
						<tr>
							<th style="width:5%;">Order Id</th>
							<th><input type="checkbox" class="check_all"></th>
							<th>Invoice Number</th>
							<th>Name of customer</th>
							<th>State</th>
							<th>Sent Date</th>
							<th style="width:10%;;">Payment</th>
							<th class="text-right">Invoice Value</th>
							<th class="text-right">IGST</th>
							<th class="text-right">CGST</th>
							<th class="text-right">SGST</th>
							<th class="text-right">COD Charge</th>
							<th class="text-right">Shipping Charge</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($details as $row) { ?>
							<?php 

								// SAVE INVOICE ID
								$pure_date = date('Y-m-d', strtotime( $row->get_sent_date() ));
								if(!$order_invoice->exists_order_and_date( $row->get_id_order(), $pure_date )){
									$order_invoice->set_id_order($row->get_id_order());
									$order_invoice->set_date($pure_date);
									$order_invoice->insert();
								}

								// MAP
								$order->map($row->get_id_order());
								$order_invoice->map_by_order_and_date($row->get_id_order(), $pure_date);
								$order_address->map($order->get_id_order_address(), $order->get_id_fb_user());

								// TOTALS
								$all_details	= $order_detail->get_details_sent_in_date_and_order($pure_date, $pure_date, $row->get_id_order(), " order by 1 ");
								$final_amount 	= 0;
								$final_CGST 	= 0;
								$final_SGST 	= 0;
								$final_IGST 	= 0;
								$final_GST 		= 0;

								foreach ($all_details as $row_inner) {
									$price 				= ( $row_inner->get_amount()-$row_inner->get_discount() ) * $row_inner->get_qty(); // price with tax
									$amount 			= 0; // price without tax
									$CGST 				= 0;
									$SGST 				= 0;
									$IGST 				= 0;
									$GST 				= 0;

									$tax_percentage 	= 0;
									if( $price <= 1000 ){
										$tax_percentage = .05;
									}else{
										$tax_percentage = .12;
									}

									$amount 	= $price / (1+$tax_percentage); // PRICE / 1.05 or PRICE / 1.12
									$amount 	= round($amount, 2); // 2 decimals only

									if(
										substr( $order_address->get_pin_code(), 0, 2 ) == '40' || 
										substr( $order_address->get_pin_code(), 0, 2 ) == '41' || 
										substr( $order_address->get_pin_code(), 0, 2 ) == '42' || 
										substr( $order_address->get_pin_code(), 0, 2 ) == '43' || 
										substr( $order_address->get_pin_code(), 0, 2 ) == '44'
									){
										$CGST 		= ($price - $amount)/2;
										$CGST 		= round($CGST, 2); // 2 decimals only

										$SGST 		= ($price - $amount - $CGST);
									}else{
										$IGST 		= ($price - $amount);
									}

									$GST 	= $CGST+$SGST+$IGST;

									$final_amount 	+= $amount;
									$final_CGST 	+= $CGST;
									$final_SGST 	+= $SGST;
									$final_IGST 	+= $IGST;
									$final_GST 		+= $GST;
								}


							?>

							<?php $order->map($row->get_id_order()); ?>
							<tr>
								<td><?php echo $row->get_id_order(); ?></td>
								<td>
									<input class="check_box" type="checkbox" data-id_order="<?php echo $row->get_id_order(); ?>" data-date="<?php echo date('Y-m-d', strtotime($row->get_sent_date())); ?>" />
								</td>
								<td><?php echo $order_invoice->get_id_order_invoice(); ?></td>
								<td><?php echo $order_address->get_name(); ?></td>
								<td><?php echo $order_address->get_state(); ?></td>
								<td><?php echo $row->get_sent_date(); ?></td>
								<td><?php echo $order->get_payment_method(); ?></td>
								<td class="text-right"><?php echo number_format($final_amount, 2); ?></td>
								<td class="text-right"><?php echo number_format($final_IGST, 2); ?></td>
								<td class="text-right"><?php echo number_format($final_CGST, 2); ?></td>
								<td class="text-right"><?php echo number_format($final_SGST, 2); ?></td>
								<td class="text-right"><?php echo number_format($order->get_cod_fee(), 2); ?></td>
								<td class="text-right"><?php echo number_format($order->get_shipping_fee(), 2); ?></td>
							</tr>
						<?php } ?>
						<tr>
							<td>&nbsp;</td>
							<td>
								<a href="javascript:download_pdf()" class="btn btn-gray btn-sm">
									<i class="glyphicon glyphicon-arrow-down"></i> &nbsp;PDF
								</a>
								<a href="javascript:download_one_pdf()" class="btn btn-gray btn-sm">
									<i class="glyphicon glyphicon-arrow-down"></i> &nbsp;One PDF
								</a>
							</td>
							<td colspan="99">&nbsp;</td>
						</tr>
					</tbody>
				</table>
		
			<?php }?>

		</div>
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../orders_with_stock"]').parents('li').addClass('active');</script>

	<link href="../includes/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script src="../includes/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <script>
    	$('#from_date, #till_date').datepicker({
    		 orientation: "bottom right",
    		 format: "yyyy-mm-dd"
    	});
    </script>

    <script>
		$('.check_all').on('click', function(){
			$(this).closest('table').find('.check_box').prop('checked', $(this).prop('checked'));
		});
	</script>

	<script>
		function download_pdf(){
			$('.check_box:checked').each(function(){
				var id_order 	= $(this).attr('data-id_order');
				var date 		= $(this).attr('data-date');

				window.open('invoice_pdf.php?orders='+id_order+'@'+date, '_blank');
			});
		}

		function download_one_pdf(){
			var orders = '';
			$('.check_box:checked').each(function(){
				var id_order 	= $(this).attr('data-id_order');
				var date 		= $(this).attr('data-date');

				orders += id_order+"@"+date+"@@";
			});

			if(orders.length>0){
				orders = orders.substring(0, orders.length-2);
			}

			window.open('invoice_pdf.php?orders='+orders, '_blank');
		}
	</script>

</body>
</html>
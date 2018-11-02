<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/purchase_order.php"); ?>
<?php require_once("../../classes/purchase_order_row.php"); ?>
<?php require_once("../../classes/order_detail.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>

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

 		$vendor 				= new vendor();
 		$purchase_order 		= new purchase_order();
 		$purchase_order_row 	= new purchase_order_row();
 		$order_detail 			= new order_detail();
 		$product_lang 			= new product_lang();

	/* ====================================================================== *
        	GET DASTA
 	* ====================================================================== */		

 		if(!isset($_POST['action'])){
 			$_POST['column'] 			= 'discontinued';
 		}

 		$column 			= $_POST['column']; // discontinued


		$products    		= $purchase_order_row->get_all_unavailable_products($column, " order by (select id_vendor from purchase_order where id_purchase_order=purchase_order_row.id_purchase_order) ");

	?>

	<div class="section">
		<div class="content">

			<h2>Unavailable Products</h2>

			<form action="" name="form" method="post">
				<input type="hidden" name="action" value="1">
				<input type="hidden" name="column" value="<?php echo $column; ?>">
				
				<p>
					<a href="./" class="btn btn-default btn-gray btn-sm">Go back</a>								
				</p>	

				<br>

				<!-- Nav tabs -->
				<ul class="nav nav-tabs">
					<li <?php if($column == 'discontinued'){ echo "class='active'"; } ?>>
						<a href="javascript:change_column('discontinued');">
							Discontinued
						</a>
					</li>
					<li <?php if($column == 'out_of_stock'){ echo "class='active'"; } ?>>
						<a href="javascript:change_column('out_of_stock');">
							Out of Stock
						</a>
					</li>
				</ul>

				<br>

				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Id Orders</th>
							<th>Thumbnail</th>
							<th>Link</th>
							<th>Item</th>
							<th>Color</th>
							<th>Size</th>
							<th>Asian Color</th>
							<th>Asian Size</th>
							<th>Out of Stock</th>
							<th>Discontinued</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$cont 		= 0; 
							$vendor_tmp = '';
						?>
						<?php foreach ($products as $row) { ?>
							<?php 

								/* SHOW ONLY PO OPEN */

								$POclosed 				= false;
								$id_order_details       = explode("-", $row->get_id_order_details());
								foreach ($id_order_details as $row_inner) {
								    if($row_inner=='')continue;

								    $pieces           = explode("@@", $row_inner);
								    $id_order         = $pieces[0];
								    $id_order_detail  = $pieces[1];

								    $order_detail->map($id_order, $id_order_detail);
								    if($order_detail->get_POmade() == ""){
								    	$POclosed = true;
								    	break;
								    }
								}

								if($POclosed)continue;

								/* SHOW ONLY ACTIVE PRODUCTS */

								if($product_lang->get_product_active($row->get_id_product_lang()) == '0')continue;

								/* INCREASE CONT */

								$cont++; 

								/* ID VENDOR */

								$purchase_order->map($row->get_id_purchase_order());
								$vendor->map($purchase_order->get_id_vendor());

								/* ID ORDERS */

								$id_orders 	= "";
								$details 	= $order_detail->get_orders_by_product($row->get_id_product_lang(), "");
								foreach($details as $row_detail){
									$id_orders .= $row_detail->get_id_order().", ";
								}
							
								if($vendor_tmp != $vendor->get_id_vendor()){
									$vendor_tmp = $vendor->get_id_vendor();
						?>
									<tr class="alert alert-info">
										<td colspan="20"><?php echo $vendor->get_name(); ?></td>
									</tr>
						<?php 
								}
						?>
							<tr>
								<td><?php echo $cont; ?></td>
								<td><?php echo $id_orders; ?></td>
								<td>
									<img height="60px" src="<?php echo $row->get_image_url(); ?>" alt="">
								</td>
								<td>
									<a href="<?php echo $row->get_product_link(); ?>">Link</a>
								</td>
								<td><?php echo $row->get_item(); ?></td>
								<td class="text-center">
									<?php if($row->get_color() != ""){ ?>
					        			<?php echo $row->get_color(); ?>
					        			<br>
					        			<span class="media-box-color"><span style="background:<?php echo strpos($row->get_color(), '#') !== false ? $row->get_color() : "#".$row->get_color(); ?>;"></span></span>
					        		<?php } ?>
								</td>
								<td><?php echo $row->get_size(); ?></td>
								<td><?php echo $row->get_asian_color(); ?></td>
								<td><?php echo $row->get_asian_size(); ?></td>
								<td><?php echo $row->get_out_of_stock(); ?></td>
								<td><?php echo $row->get_discontinued(); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			
			</form>
		</div>	
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../purchase_order"]').parents('li').addClass('active');</script>

	<script>
		function change_column(column){
			document.form.column.value = column;
			document.form.submit();
		}
	</script>

</body>
</html>
<?php require_once("../session.php"); ?>
<?php require_once("../../dbconnect.php"); ?>
<?php require_once("../../classes/purchase_order.php"); ?>
<?php require_once("../../classes/purchase_order_row.php"); ?>
<?php require_once("../../classes/vendor_product.php"); ?>
<?php require_once("../../classes/product_lang.php"); ?>
<?php require_once("../../classes/vendor.php"); ?>
<?php require_once("../../classes/vendor_product_stock.php"); ?>

<!doctype html>
<html lang="en">
<head>
	<?php require_once("../head.php"); ?>
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

		if(!isset($_GET['id_purchase_order'])){
			echo "<script>location.href='./';</script>";	
			exit();
		}

		$vendor_product_stock 	= new vendor_product_stock();
		$product_lang 			= new product_lang();
		$vendor_product 		= new vendor_product();
		$products 				= $vendor_product->get_all_from_vendor($id_vendor, "ORDER BY 1");

		$id_purchase_order 		= $_GET['id_purchase_order'];

		$purchase_order_row 	= new purchase_order_row();
		$final_result 			= $purchase_order_row->get_all_from_id_purchase_order($id_purchase_order, " order by id_product_lang, id_row ");

		$purchase_order 		= new purchase_order();
		$purchase_order->map($id_purchase_order);


		$msj = "";

		// ===== Modify purchase order =====>
		if( isset($_POST['action']) && $_POST['action'] == '1' ){

			$conn->beginTransaction();
			$error = false;

			foreach ($final_result as $row) {
				
				$row->set_qty( $_POST[$row->get_id_row()."=>qty"] );
				$row->set_product_link( $_POST[$row->get_id_row()."=>link"] );
				$row->set_item( $_POST[$row->get_id_row()."=>item"] );
				$row->set_color( $_POST[$row->get_id_row()."=>color"] );
				$row->set_size( $_POST[$row->get_id_row()."=>size"] );				
				$row->set_asian_color( $_POST[$row->get_id_row()."=>asian_color"] );
				$row->set_asian_size( $_POST[$row->get_id_row()."=>asian_size"] );
				$row->set_comment( $_POST[$row->get_id_row()."=>comment"] );

				if( isset($_POST[$row->get_id_row()."=>vendor_product"]) && $row->get_id_vendor_product() != $_POST[$row->get_id_row()."=>vendor_product"] ){
					$vendor_product->map( $_POST[$row->get_id_row()."=>vendor_product"] );

					$row->set_id_vendor_product( $_POST[$row->get_id_row()."=>vendor_product"] );
					$row->set_id_product_lang( $vendor_product->get_id_product_lang() );
					$row->set_image_url( $vendor_product->get_image_url() );
					$row->set_product_link( $vendor_product->get_product_link() );
				}

				if( $row->update() === false ){
					$error = true; break;
				}

			}

			if($error === false){
				$conn->commit();
				$msj = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated the purchase order.</div>';
			}else{
				$conn->rollback();
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}

		// ===== delete row =====>
		}else if( isset($_POST['action']) && $_POST['action'] == '2' ){

			$get_id_row 	= $_POST['get_id_row'];
			$error 			= false;

			$purchase_order_row = new purchase_order_row();
			$purchase_order_row->set_id_row($get_id_row);
			if($purchase_order_row->delete()){
				//all good
				$final_result 			= $purchase_order_row->get_all_from_id_purchase_order($id_purchase_order, " order by id_row ");
			}else{
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
			}

		// ===== add row =====>
		}else if( isset($_POST['action']) && $_POST['action'] == '3' ){

			$purchase_order_row = new purchase_order_row();
			
			$purchase_order_row->set_id_row($purchase_order_row->max_id_row());
			$purchase_order_row->set_id_purchase_order($id_purchase_order);
			$purchase_order_row->set_id_orders("");
			$purchase_order_row->set_payment("");
			$purchase_order_row->set_qty("0");
			$purchase_order_row->set_image_url("");
			$purchase_order_row->set_product_link("");
			$purchase_order_row->set_item("");
			$purchase_order_row->set_color("");
			$purchase_order_row->set_size("");
			$purchase_order_row->set_asian_color("");
			$purchase_order_row->set_asian_size("");
			$purchase_order_row->set_comment("");
			$purchase_order_row->set_row_added("S");
			$purchase_order_row->set_id_vendor_product("0");
			$purchase_order_row->set_id_product_lang("0");

			if($purchase_order_row->insert()){
				//all good
				$final_result 			= $purchase_order_row->get_all_from_id_purchase_order($id_purchase_order, " order by id_row ");
			}else{
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, there was an error.</div>';
			}


		// ===== increase stock =====>
		}else if( isset($_POST['action']) && $_POST['action'] == '4' ){

			$conn->beginTransaction();
			$error = false;

			foreach ($final_result as $row) {
				if( isset($_POST[$row->get_id_row()."=>stock"]) ){

					$qty = $_POST[$row->get_id_row()."=>qty"];

					$color_				= str_replace(' ', '_', $_POST[$row->get_id_row()."=>color"]);
					$size_				= str_replace(' ', '_', $_POST[$row->get_id_row()."=>size"] );
					$current_stock 		= $vendor_product_stock->get_stock_each_product_lang( $row->get_id_product_lang(), $color_, $size_ );
					
					//echo "HERE(".$row->get_id_product_lang().",".$color_.",".$size_.") =>".$current_stock ." + ". $qty ." = ".($current_stock+$qty)."<br>";
					if($vendor_product_stock->exists_product_lang($row->get_id_product_lang(), $color_, $size_)){
						if( !$vendor_product_stock->update_stock( $row->get_id_product_lang(), $color_, $size_, ($current_stock+$qty) ) ){
							$error = true;
							break;
						}
					}else{
						$vendor_product_stock->set_id_product($row->get_id_vendor_product());
						$vendor_product_stock->set_hex_color($color_);
						$vendor_product_stock->set_color_name($color_);
						$vendor_product_stock->set_size($size_);
						$vendor_product_stock->set_id_product_lang($row->get_id_product_lang());
						$vendor_product_stock->set_stock($current_stock+$qty);

						if( $vendor_product_stock->insert() === false ){
							$error = true;
							break;
						}
					}

				}
			}

			if($error === false){
				$conn->commit();
				$msj = '<div class="alert alert-success"><strong>Awesome</strong>, You successfully updated the purchase order.</div>';
			}else{
				$conn->rollback();
				$msj = '<div class="alert alert-danger"><strong>Oops</strong>, something went wrong, refresh the page and try again.</div>';
			}

		}
	?>

	<div class="section">
		<div class="content">

			<h2><?php echo $purchase_order->get_name(); ?></h2>
			
			<p>
				<a href="./all_purchase_order.php" class="btn btn-default btn-gray btn-sm">Go back</a>	
				<a target="_blank" href="../../public/PO/?url=<?php echo $purchase_order->get_url(); ?>" class="btn btn-default btn-green btn-sm">Open link for vendor</a>
			</p>	

			<?php echo $msj; ?>

			<hr>

			<form action="" method="post" name="form">
				<input type="hidden" name="action">
				<input type="hidden" name="get_id_row">
				
				<p>
					<a href="javascript:add_new_row();" class="btn btn-default btn-large btn-blue btn-sm">Add a new row</a>
				</p>

				<table class="table table-condensed table-bordered table-striped">
					<thead>
						<tr>
							<th><input type="checkbox" class="all_check"></th>
							<th>Stock</th>
							<th>Action</th>
							<th>Order Id</th>
							<th style="width:150px;">Payment</th>
							<th style="width:55px;">Qty</th>
							<th>Thumbnail</th>
							<th>Link</th>
							<th>Item</th>
							<th>Color</th>
							<th>Size</th>
							<th>Asian Color</th>
							<th>Asian Size</th>
							<th>Comment</th>
							<th>Comment Agent</th>
							<th>Comment QC</th>
						</tr>
					</thead>
					<tbody>
						<?php $total = 0; ?>
						<?php foreach ($final_result as $row) { ?>
							<?php 
								$total += $row->get_qty(); 

								$color_				= str_replace(' ', '_', $row->get_color());
								$size_				= str_replace(' ', '_', $row->get_size());
								$current_stock 		= $vendor_product_stock->get_stock_each_product_lang( $row->get_id_product_lang(), $color_, $size_ );

								if($current_stock == ''){
									$current_stock = 0;
								}
							?>
							<tr>
								<td>
									<?php if($row->get_id_product_lang() != '0' && $row->get_id_vendor_product() != '0'){ ?>
										<input class="stock_check" type="checkbox" name="<?php echo $row->get_id_row()."=>stock"; ?>">
									<?php } ?>
								</td>
								<td><?php echo $current_stock; ?></td>
								<td>
									<a href="javascript:erase('<?php echo $row->get_id_row(); ?>');" class="btn btn-red btn-sm"><i class="glyphicon glyphicon-trash"></i></a>
								</td>
								<?php if( $row->get_row_added() === "S" ){ ?>
									<td colspan="2">
										<strong>Choose the product</strong>
										<select class="form-control" name="<?php echo $row->get_id_row()."=>vendor_product"; ?>" id="">
											<?php foreach ($products as $prod) { ?>
												<option <?php if( $prod->get_id_product() == $row->get_id_vendor_product() ){ echo "selected"; } ?> value="<?php echo $prod->get_id_product(); ?>"><?php echo $prod->get_id_product_lang(); ?> - <?php echo $product_lang->get_product_name($prod->get_id_product_lang()); ?></option>
											<?php } ?>
										</select>
									</td>
								<?php }else{ ?>
									<td><?php echo $row->get_id_orders(); ?></td>
									<td><?php echo $row->get_payment(); ?></td>
								<?php } ?>
								<td>
									<input name="<?php echo $row->get_id_row()."=>qty"; ?>" class="form-control input-sm" type="text" value="<?php echo $row->get_qty(); ?>" />
								</td>
								<td>
									<img height="60px" src="<?php echo $row->get_image_url(); ?>" alt="">
								</td>
								<td>
									<textarea name="<?php echo $row->get_id_row()."=>link"; ?>" class="form-control" type="text"><?php echo $row->get_product_link(); ?></textarea>
								</td>
								<td>
									<textarea name="<?php echo $row->get_id_row()."=>item"; ?>" class="form-control" type="text"><?php echo $row->get_item(); ?></textarea>
								</td>
								<td class="text-center">
									<input name="<?php echo $row->get_id_row()."=>color"; ?>" class="form-control input-sm" type="hidden" value="<?php echo $row->get_color(); ?>" />
									<?php if($row->get_color() != ""){ ?>
					        			<?php echo $row->get_color(); ?>
					        			<br>
					        			<span class="media-box-color"><span style="background:<?php echo strpos($row->get_color(), '#') !== false ? $row->get_color() : "#".$row->get_color(); ?>;"></span></span>
					        		<?php } ?>
								</td>
								<td>
									<input name="<?php echo $row->get_id_row()."=>size"; ?>" class="form-control input-sm" type="text" value="<?php echo $row->get_size(); ?>" />
								</td>

								<td>
									<input name="<?php echo $row->get_id_row()."=>asian_color"; ?>" class="form-control input-sm" type="text" value="<?php echo $row->get_asian_color(); ?>" />
								</td>
								<td>
									<input name="<?php echo $row->get_id_row()."=>asian_size"; ?>" class="form-control input-sm" type="text" value="<?php echo $row->get_asian_size(); ?>" />
								</td>
								<td>
									<textarea name="<?php echo $row->get_id_row()."=>comment"; ?>" class="form-control" type="text"><?php echo $row->get_comment(); ?></textarea>
								</td>
								<td><?php echo  $row->get_comment_agent(); ?></td>
								<td><?php echo  $row->get_comment_qc(); ?></td>
							</tr>
						<?php } ?>
					</tbody>
					<tr>
						<th colspan="2">
							<a href="javascript:increase_stock();" class="btn btn-default btn-sm btn-sm">Increase stock</a>
						</th>
						<th colspan="3">Total</th>
						<th><?php echo $total; ?></th>
						<th colspan="10"></th>
					</tr>
				</table>

			</form>

			<p>
				<a href="javascript:save();" class="btn btn-default btn-large btn-green">Save</a>
			</p>
			
	</div>

	<?php require_once("../bottom.php"); ?>
	<script>jQuery('a[href="../purchase_order"]').parents('li').addClass('active');</script>
	
	<script>

		$('.all_check').on('click', function(){
			var checkBoxes = $('.stock_check');
			checkBoxes.prop("checked", !checkBoxes.prop("checked"));
		});

		function save(){
			document.form.action.value = "1";
			document.form.submit();
		}

		function erase(get_id_row){
			if( confirm("Are you sure?") ){
				document.form.action.value = "2";
				document.form.get_id_row.value = get_id_row;
				document.form.submit();
			}
		}	

		function add_new_row(){
			if( confirm("Are you sure?") ){
				document.form.action.value = "3";
				document.form.submit();
			}
		}

		function increase_stock(){
			if("Are you sure?"){
				document.form.action.value = "4";
				document.form.submit();
			}
		}
	</script>

</body>
</html>